<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Update gold cron file.
 * This cron file is responsible for updating every user's gold every hour.
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

require_once APP_PATH.'/environment.php';

require_once APP_PATH.'/chamista/Bootstrap.php';

Bootstrap::setupEnvironment();
Bootstrap::setupConfiguration();
Bootstrap::setupDatabase();

$Users = new Chamista_Model_DbTable_Users();
$now = time();

$rows = $Users->fetchAll($Users->select()
	->from($Users->_name,'usr_id'));
foreach ($rows as $row) {
	echo('Updating '.$row->usr_id.'...'."\n");
	$User = new Chamista_Model_User($row->usr_id);
	$duration = $now - $User->usr_update;
	$income = $User->getGlobalIncome();
	$new_gold = $User->usr_gold + $income / 3600 * ($duration);
	if ($new_gold < 0) {
		$fire_scientists = 0;
		while ($income < 0) {
			// Fire random number of scientists
			$Town = new Chamista_Model_Town($User->usr_capital);
			if ($Town->getScientists() > 0) {
				$max = $Town->getScientists();
				if ($max > 20) $max = 20;
				$fire = rand(0,$max);
				$Town->town_scientists -= $fire;
				$fire_scientists += $fire;
			} else {
				// Get scientist from another city
				$cities = $User->getUserCityIDs();
				foreach ($cities as $city) {
					$Town = new Chamista_Model_Town($city->town_id);
					if ($Town->getScientists() == 0) continue;
					$max = $Town->getScientists();
					if ($max > 20) $max = 20;
					$fire = rand(0,$max);
					$Town->town_scientists -= $fire;
					$fire_scientists += $fire;
					break;
				}
			}
			// If there are no scientists to be fired
			if ($fire == 0) {
				// Armies will run away
				/*
				 * Here's the message if you can't pay for the army's upkeep:
				 * Your gold is running low. Since you cannot pay the upkeep for your army, the following units have left your army: Spearmen: 44
				 * Ram-Ship: 1
				 */
			}
			// Recalculate income
			$income = $User->getGlobalIncome();
		}
	}
	echo("\tUser gold: ".$User->usr_gold." -> ".$new_gold."\n");
	
	$usr_research = $User->usr_research + ($User->getGlobalResearch() / 3600) * $duration;
	echo("\tUser research: ".$User->usr_research." -> ".$usr_research."\n");
	$User->usr_research = $usr_research;
	$User->usr_gold = $new_gold;
	$User->usr_update = $now;
	
	// Clear $User
	unset($User);
}