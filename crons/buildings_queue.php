<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Update buildings queue cron file.
 * This cron file is responsible for updating building queues when the user is away.
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

require_once 'Zend/Loader/Autoloader.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace(array('EZ_', 'Galahad_', 'Rewaz_'));

$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
	'basePath' => APP_PATH . '/chamista/',
	'namespace' => 'Chamista'
));

$resourceLoader->addResourceType('model', 'models/', 'Model');

if(defined('ENV') !== TRUE) {
	define('ENV','staging');	//change staging to production to go to production settings
}

require_once APP_PATH.'/chamista/Bootstrap.php';

Bootstrap::setupEnvironment();
Bootstrap::setupConfiguration();
Bootstrap::setupDatabase();

// In order to prevent high load, just select 10,000 to update the queue
// the rest are updated the on instance
$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
$Buildings = new Chamista_Model_DbTable_Buildings();
$Events = new Chamista_Model_DbTable_Events();
$Towns = new Chamista_Model_DbTable_Towns();
$now = time();
$rows = $Buildings_Queue->fetchAll($Buildings_Queue->select()
	->where('queue_endtime <= ?',$now)
	->limit(10000)
);
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
		$desc = 'Your building <a href="/city/'.Chamista_Model_Format::building_action($row->queue_type).'/id/'.$row->queue_town.'/position/'.$row->queue_pos.'">'.
			Chamista_Model_Format::building_name($row->queue_type).'</a> has been completed!';
	} else {
		$desc = 'Your building <a href="/city/'.Chamista_Model_Format::building_action($row->queue_type).'/id/'.$row->queue_town.'/position/'.$row->queue_pos.'">'.
			Chamista_Model_Format::building_name($row->queue_type).'</a> has been expanded to level '.$row->queue_lvl.'!';
	}
	if ($row->queue_type == 1) {
		$this->town_lvl ++;
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