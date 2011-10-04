#!/usr/local/bin/php -q
<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Transports daemon file.
 * This daemon will update transports every second or so.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

define('ZFW_PREFIX','/usr/local/Zend/Framework/ZendFramework');
define('APP_PATH', realpath(dirname(__FILE__) . '/../'));
define('LIB_PATH', APP_PATH . '/library');

$paths = array(
	APP_PATH,
	APP_PATH . DIRECTORY_SEPARATOR . 'chamista' . DIRECTORY_SEPARATOR . 'models',
	ZFW_PREFIX . DIRECTORY_SEPARATOR . 'library',
	LIB_PATH . DIRECTORY_SEPARATOR,
	get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $paths));

require_once APP_PATH.'/environment.php';

// Allowed arguments & their defaults 
$runmode = array(
    'no-daemon' => false,
); 

// Scan command line attributes for allowed arguments
foreach ($argv as $k=>$arg) {
    if (substr($arg, 0, 2) == '--' && isset($runmode[substr($arg, 2)])) {
        $runmode[substr($arg, 2)] = true;
    }
} 

require_once 'System/Daemon.php';

$options = array(
	'appName' => 'transports',
	'appDir' => dirname(__FILE__),
	'appDescription' => 'This daemon will update transports every second or so.',
	'sysMaxExecutionTime' => '0',
	'sysMaxInputTime' => '0',
	'sysMemoryLimit' => '64M',
	'logLocation' => '/var/log/ikariem/transports.log',
	'appPidLocation' => '/var/run/ikariem/transports/transports.pid',
);

switch (ENV) {
	case 'staging':
		$options['appRunAsGID'] = 1000;
		$options['appRunAsUID'] = 1000;
		break;
	case 'test':
		$options['appRunAsGID'] = 500;
		$options['appRunAsUID'] = 500;
		break;
	case 'development':
		$options['appRunAsGID'] = 502;
		$options['appRunAsUID'] = 502;
		break;
}

System_Daemon::setOptions($options);

if (!$runmode['no-daemon']) {
	// Spawn daemon!
	System_Daemon::start();
}

// ================== Perform Zend Framework Initialization ===================

require_once 'Zend/Loader/Autoloader.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace(array('EZ_', 'Galahad_', 'Rewaz_'));

$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
	'basePath' => APP_PATH . '/chamista/',
	'namespace' => 'Chamista'
));

$resourceLoader->addResourceType('model', 'models/', 'Model');

require_once APP_PATH.'/chamista/Bootstrap.php';

Bootstrap::setupEnvironment();
Bootstrap::setupConfiguration();
Bootstrap::setupDatabase();
// =================================== End ====================================

$runningOkay = true;
$Transports = new Chamista_Model_DbTable_Transports();
$Events = new Chamista_Model_DbTable_Events();
$Mission = new Chamista_Model_DbTable_Spies_Mission();

while (!System_Daemon::isDying() && $runningOkay) {
	$now = time();
	
	$rows = $Transports->fetchAll($Transports->select()->where('trans_enddate <= ?',$now));
	foreach ($rows as $row) {
		switch ($row->trans_mission) {
			// Transport (Loading)
			case 0:
				// Only update mission to transporting and update the startdate and enddate
				$Town = new Chamista_Model_Town($row->trans_origin);
				$Destination = new Chamista_Model_Town($row->trans_destination);
				$timeNeeded = Chamista_Model_Formula::travelTime(60,$Town->getIslandXY(),$Destination->getIslandXY());
				$update = array(
					'trans_mission' => 1,
					'trans_startdate' => $row->trans_enddate,
					'trans_enddate' => $row->trans_enddate + $timeNeeded
				);
				$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
				$Transports->update($update,$where);
				break;
			// Transport underway
			case 1:
				// Unload goods and if the city does not belong to the transporter, ask it to return
				$Town = new Chamista_Model_Town($row->trans_origin);
				$Destination = new Chamista_Model_Town($row->trans_destination);
				$cargo = unserialize($row->trans_cargo);
				foreach ($cargo as $i => $v) {
					$Destination->{'town_'.$i} += $v;
				}
				if ($Town->town_uid == $Destination->town_uid) {
					// You can delete the transport
					$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
					$Transports->delete($where);
					$desc = 'Your trade fleet from <a href="/island?cityId='.$row->trans_origin.'">'.$Town->town_name.'</a>'.
						' has arrived in <a href="/island?cityId='.$row->trans_destination.'">'.$Destination->town_name.'</a>'.
						' and brought the following goods: <ul class="resources">';
					foreach ($cargo as $i => $v) {
						if ($v == 0) continue;
						$desc .= '<li class="'.Chamista_Model_Format::tradegood_css(substr($i,-1)).'"><span class="textLabel">'.
							Chamista_Model_Format::tradegood_name(substr($i,-1)).': </span>'.number_format($v).'</li>';
					}
					$desc .= '</ul>';
					$data = array(
						'event_location' => $row->trans_origin,
						'event_date' => $now,
						'event_desc' => $desc,
						'event_new' => 1,
						'event_uid' => $Town->town_uid,
					);
					$Events->insert($data);
				} else {
					$timeNeeded = Chamista_Model_Formula::travelTime(60,$Town->getIslandXY(),$Destination->getIslandXY());
					$update = array(
						'trans_mission' => 2,
						'trans_startdate' => $row->trans_enddate,
						'trans_enddate' => $row->trans_enddate + $timeNeeded,
						'cargo' => serialize(array())
					);
					$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
					$Transports->update($update,$where);
				}
				break;
			// Transport returning
			case 2:
				$Town = new Chamista_Model_Town($row->trans_origin);
				$cargo = unserialize($row->trans_cargo);
				foreach ($cargo as $i => $v) {
					$Town->{'town_'.$i} += $v;
				}
				$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
				$Transports->delete($where);
				break;
			// Colonization (Loading)
			case 3:
				// Only update mission to transporting and update the startdate and enddate
				$Town = new Chamista_Model_Town($row->trans_origin);
				$Destination = new Chamista_Model_Town($row->trans_destination);
				$timeNeeded = Chamista_Model_Formula::travelTime(60,$Town->getIslandXY(),$Destination->getIslandXY());
				$update = array(
					'trans_mission' => 4,
					'trans_startdate' => $row->trans_enddate,
					'trans_enddate' => $row->trans_enddate + $timeNeeded
				);
				$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
				$Transports->update($update,$where);
				break;
			// Colonization (underway)
			case 4:
				// Colonize and officially create the town here
				$Town = new Chamista_Model_Town($row->trans_origin);
				$Destination = new Chamista_Model_Town($row->trans_destination);
				$cargo = unserialize($row->trans_cargo);
				foreach ($cargo as $i => $v) {
					$Destination->{'town_'.$i} += $v;
				}
				// You can delete the transport
				$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
				$Transports->delete($where);
				$Destination->town_lvl = 1;
				$Destination->town_update = time();
				$Destination->town_population = 40;
				$desc = 'We have founded a new town (<a href="/city?id='.$Destination->town_id.'">'.$Destination->town_name.'</a>).';
				if (array_sum($cargo) > 0) {
					$desc .= ' Your trade fleet has cleared its cargo: <ul class="resources">';
					foreach ($cargo as $i => $v) {
						if ($v == 0) continue;
						$desc .= '<li class="'.Chamista_Model_Format::tradegood_css(substr($i,-1)).'"><span class="textLabel">'.
							Chamista_Model_Format::tradegood_name(substr($i,-1)).': </span>'.number_format($v).'</li>';
					}
					$desc .= '</ul>';
				}
				$data = array(
					'event_location' => $row->trans_origin,
					'event_date' => $now,
					'event_desc' => $desc,
					'event_new' => 1,
					'event_uid' => $Town->town_uid,
				);
				$Events->insert($data);
				break;
		}
	}
	
	// Also check spies
	$rows = $Mission->fetchAll(
		$Mission->select()
			->where('spy_arrivaltime <> 0 AND spy_arrivaltime < ?', $now)
	);
	foreach ($rows as $row) {
		if ($row->spy_mission == 1) {
			$Mission->setSpyMission($row->spy_id, 0);
		} elseif ($row->spy_mission == 10) {
			$Mission->removeSpy($row->spy_id);
		}
	}
	
	// Relax the system by sleeping for a little bit
	System_Daemon::iterate(2); 
}

System_Daemon::stop();