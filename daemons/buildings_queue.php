#!/usr/local/bin/php -q
<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Buildings queue daemon file.
 * This daemon will update building upgrade queue every second or so.
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
	'appName' => 'buildings_queue',
	'appDir' => dirname(__FILE__),
	'appDescription' => 'This daemon will update building upgrade queue every second or so.',
	'sysMaxExecutionTime' => '0',
	'sysMaxInputTime' => '0',
	'sysMemoryLimit' => '64M',
	'logLocation' => '/var/log/ikariem/buildings_queue.log',
	'appPidLocation' => '/var/run/ikariem/buildings_queue/buildings_queue.pid',
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

$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
$Buildings = new Chamista_Model_DbTable_Buildings();
$Events = new Chamista_Model_DbTable_Events();
$Towns = new Chamista_Model_DbTable_Towns();

while (!System_Daemon::isDying() && $runningOkay) {
	$now = time();
	
	$rows = $Buildings_Queue->fetchAll($Buildings_Queue->select()->where('queue_endtime <= ?',$now));
	foreach ($rows as $row) {
		$data = array(
			'build_lvl' => $row->queue_lvl
		);
		$where = array(
			$Buildings->getAdapter()->quoteInto('build_town = ?',$row->queue_town),
			$Buildings->getAdapter()->quoteInto('build_position = ?',$row->queue_pos),
			$Buildings->getAdapter()->quoteInto('build_type = ?',$row->queue_type)
		);
		$Buildings->update($data,$where);
		$where = array(
			$Buildings_Queue->getAdapter()->quoteInto('queue_town = ?',$row->queue_town),
			$Buildings_Queue->getAdapter()->quoteInto('queue_pos = ?',$row->queue_pos)
		);
		$Buildings_Queue->delete($where);
		if ($row->queue_lvl == 1) {
			$desc = 'Your building "<a href="/'.Chamista_Model_Format::building_action($row->queue_type).'?id='.$row->queue_town.'&position='.$row->queue_pos.'">'.
				Chamista_Model_Format::building_plaintext($row->queue_type).'</a>" has been completed!';
		} else {
			$desc = 'Your building <a href="/'.Chamista_Model_Format::building_action($row->queue_type).'?id='.$row->queue_town.'&position='.$row->queue_pos.'">'.
				Chamista_Model_Format::building_plaintext($row->queue_type).'</a> has been expanded to level '.$row->queue_lvl.'!';
		}
		if ($row->queue_type == 1) {
			$Town = new Chamista_Model_Town($row->queue_town);
			$Town->town_lvl ++;
			unset($Town);
		}
		$data = array(
			'event_location' => $row->queue_town,
			'event_date' => $now,
			'event_desc' => $desc,
			'event_new' => 1,
			'event_uid' => $Towns->getData('town_uid',$row->queue_town)
		);
		$Events->insert($data);
	}
	
	// Relax the system by sleeping for a little bit
	System_Daemon::iterate(2); 
}

System_Daemon::stop();