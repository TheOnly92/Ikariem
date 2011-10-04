#!/usr/local/bin/php -q
<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Combat engine daemon file.
 * This daemon will update combats in Ikariem every second or so.
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
	'appName' => 'combats',
	'appDir' => dirname(__FILE__),
	'appDescription' => 'This daemon will update combats in Ikariem every second or so.',
	'sysMaxExecutionTime' => '0',
	'sysMaxInputTime' => '0',
	'sysMemoryLimit' => '64M',
	'logLocation' => '/var/log/ikariem/combats.log',
	'appPidLocation' => '/var/run/ikariem/combats/combats.pid',
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
$Combats = new Chamista_Model_DbTable_Combats();
$Events = new Chamista_Model_DbTable_Events();
$missions = array(5,6,7);

while (!System_Daemon::isDying() && $runningOkay) {
	$now = time();
	
	// Initiate battles which the attackers arrived first
	$rows = $Transports->fetchAll($Transports->select()->where('trans_enddate <= ?',$now)->where('trans_mission IN (?)', $missions));
	foreach ($rows as $row) {
		switch ($row->trans_mission) {
			// Just arrived to fight
			case 5:
				$Town = new Chamista_Model_Town($row->trans_origin);
				
				if ($row->trans_destination == 0) {
					// Barbarian village
					$nextRound = ($row->trans_enddate - $row->trans_startdate);
					$insert = array(
						'combat_trans' => $row->trans_id,
						'combat_attacker' => $Town->town_id,
						'combat_defender' => 0,
						//'combat_nextround' => $now + $nextRound,
						'combat_data' => serialize(array())
					);
					$combatId = $Combats->insert($insert);
					$update = array(
						'trans_mission' => 6,
						'trans_startdate' => $now,
						//'trans_enddate' => $now + $nextRound,
					);
					$where = $Transports->getAdapter()->quoteInto('trans_id = ?', $row->trans_id);
					$Transports->update($update, $where);
					$CEngine = new Chamista_Model_CombatEngine($combatId);
					$CEngine->landBattle();
				} else {
					
				}
				break;
			case 6:
				// Battle already on-going
				$nextRound = ($row->trans_enddate - $row->trans_startdate);
				$update = array(
					'trans_mission' => 6,
					'trans_startdate' => $now,
					'trans_enddate' => $now + $nextRound,
				);
				$where = $Transports->getAdapter()->quoteInto('trans_id = ?', $row->trans_id);
				$Transports->update($update, $where);
				$update = array(
					'combat_nextround' => $now + $nextRound,
				);
				$combatRow = $Combats->fetchRow($Combats->select()->where('combat_trans = ?', $row->trans_id));
				$where = $Combats->getAdapter()->quoteInto('combat_id = ?', $combatRow->combat_id);
				$Combats->update($update, $where);
				$CEngine = new Chamista_Model_CombatEngine($combatRow->combat_id);
				$CEngine->landBattle();
				break;
			case 7:
				// Back to home
				$Town = new Chamista_Model_Town($row->trans_origin);
				$Units = new Chamista_Model_DbTable_Towns_Units();
				if ($row->trans_pillage == 1) {
					$cargo = unserialize($row->trans_cargo);
					$desc = 'Your fleet has returned to <a href="/island?cityId='.$row->trans_origin.'">'.$Town->town_name.'</a> from pillaging ';
					if ($row->trans_destination == 0) {
						$desc .= 'Barbarian Village';
					} else {
						$Defender = new Chamista_Model_Town($row->trans_destination);
						$desc .= '<a href="/island?cityId='.$row->trans_destination.'">'.$Defender->town_name.'</a>';
					}
					$desc .= ' and has brought with it: ';
					if (count($cargo) > 0) {
						$desc .= '<ul class="resources">';
						foreach ($cargo as $i => $v) {
							if ($v > 0) {
								$desc .= '<li class="'.Chamista_Model_Format::tradegood_css(substr($i,-1)).'"><span class="textLabel">'.
									Chamista_Model_Format::tradegood_name(substr($i,-1)).': </span>'.number_format((int) $v).'</li>';
								$Town->{'town_'.$i} += $v;
							}
						}
						$desc .= '</ul>';
					} else {
						$desc .= 'Nothing';
					}
					$data = array(
						'event_location' => $row->trans_origin,
						'event_date' => $now,
						'event_desc' => $desc,
						'event_new' => 1,
						'event_uid' => $Town->town_uid,
					);
					$Events->insert($data);
				}
				$armies = unserialize($row->trans_armies);
				if (count($armies) > 0) {
					foreach ($armies as $i => $v) {
						$Units->setArmy($i, $Units->getArmy($i ,$Town->town_id) + intval($v), $Town->town_id);
					}
				}
				$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$row->trans_id);
				$Transports->delete($where);
				break;
		}
	}
	// Relax the system by sleeping for a little bit
	System_Daemon::iterate(2); 
}

System_Daemon::stop();