#!/usr/local/bin/php -q
<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Islands queue daemon file.
 * This daemon will update mine upgrade queue every second or so.
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
	'appName' => 'islands_queue',
	'appDir' => dirname(__FILE__),
	'appDescription' => 'This daemon will update mine upgrade queue every second or so.',
	'sysMaxExecutionTime' => '0',
	'sysMaxInputTime' => '0',
	'sysMemoryLimit' => '64M',
	'logLocation' => '/var/log/ikariem/islands_queue.log',
	'appPidLocation' => '/var/run/ikariem/islands_queue/islands_queue.pid',
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

$Islands_Queue = new Chamista_Model_DbTable_Islands_Queue();
$Islands = new Chamista_Model_DbTable_Islands();
$Events = new Chamista_Model_DbTable_Events();
$Towns = new Chamista_Model_DbTable_Towns();

while (!System_Daemon::isDying() && $runningOkay) {
	$now = time();
	
	$rows = $Islands_Queue->fetchAll($Islands_Queue->select()->where('queue_endtime <= ?',$now));
	foreach ($rows as $row) {
		$island = $Islands->fetchRow($Islands->select()->where('island_id = ?',$row->queue_island));
		
		// Insert the event
		if ($row->queue_type == 0) {
			$desc = 'Your <a href="/island/resource/id/'.$row->queue_island.'">Saw Mill</a> on <a href="/island/index/id/'.$row->queue_island.'">'.$island->island_name.' ('.$island->island_posx.':'.$island->island_posy.')</a> has been expanded!';
		} else {
			$desc = 'The <a href="/island/tradegood/id/'.$row->queue_island.'">'.Chamista_Model_Format::tradegood_mine($row->queue_type).'</a> was expanded on the island <a href="/island/index/id/'.$row->queue_island.'">'.$island->island_name.' ('.$island->island_posx.':'.$island->island_posy.')</a>!';
		}
		$town_rows = $Towns->fetchAll($Towns->select()->from($Towns->_name,array('town_id','town_uid'))->where('town_island = ?',$row->queue_island)->limit(16));
		foreach ($town_rows as $town_row) {
			$data = array(
				'event_location' => $town_row->town_id,
				'event_date' => $now,
				'event_desc' => $desc,
				'event_new' => 1,
				'event_uid' => $town_row->town_uid
			);
			$Events->insert($data);
		}
		
		// Upgrade the mine and remove the queue
		$data = array();
		if ($row->queue_type == 0) {
			$data['island_saw_lvl'] = $island->island_saw_lvl + 1;
		} else {
			$data['island_resource_lvl'] = $island->island_resource_lvl + 1;
		}
		$where = $Islands->getAdapter()->quoteInto('island_id = ?',$row->queue_island);
		$Islands->update($data,$where);
		$where = array(
			$Islands_Queue->getAdapter()->quoteInto('queue_island',$row->queue_island),
			$Islands_Queue->getAdapter()->quoteInto('queue_type = ?',$row->queue_type)
		);
		$Islands_Queue->delete($where);
	}
	
	// Relax the system by sleeping for a little bit
	System_Daemon::iterate(2); 
}

System_Daemon::stop();