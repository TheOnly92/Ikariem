<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Update town cron file.
 * This cron runs every hour just to subtract the wine for Tavern.
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

// Update towns by islands
$Islands = new Chamista_Model_DbTable_Islands();
$Towns = new Chamista_Model_DbTable_Towns();
$islands_ids = $Islands->fetchAll($Islands->select('island_id'));
foreach ($islands_ids as $island_id) {
	$towns_ids = $Towns->fetchAll($Towns->select('town_id')->where('town_island = ?',$island_id->island_id)->where('town_update < ?',time() - 60 * 60));
	foreach ($towns_ids as $town_id) {
		echo('Spending wine '.$town_id->town_id.'...');
		$Town = new Chamista_Model_Town($town_id->town_id);
		$Town->town_resource1 -= Chamista_Model_Formula::getTavernWine($Town->town_wine);
		echo('Done'."\n");
	}
}