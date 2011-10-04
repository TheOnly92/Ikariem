<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Update highscores cron file.
 * This cron runs every hour to recalculate the highscore.
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


$Users = new Chamista_Model_DbTable_Users();
$Highscores = new Chamista_Model_DbTable_Highscores();
$Buildings = new Chamista_Model_DbTable_Buildings();
$Users_Researches = new Chamista_Model_DbTable_Users_Researches();
$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
$Units = new Chamista_Model_DbTable_Units();
$Towns = new Chamista_Model_DbTable_Towns();

// Calculate by users
$userRows = $Users->fetchAll(
	$Users->select()
		->from($Users->_name, 'usr_id')
);
foreach ($userRows as $userRow) {
	$User = new Chamista_Model_User($userRow->usr_id);
	echo('Updating highscore for user: '.$User->usr_id.'... ');
	
	// Calculate master builders score
	$cities = $User->getUserCityIDs(true);
	$builtBuildings = $Buildings->fetchAll(
		$Buildings->select()
			->from($Buildings->_name, array('build_type', 'build_lvl'))
			->where('build_town IN (?)', $cities)
	);
	$builders = 0;
	foreach ($builtBuildings as $builtBuilding) {
		for ($i=1;$i<=$builtBuilding->build_lvl;$i++) {
			$cost = Chamista_Model_Formula::calcBuildCost($builtBuilding->build_type,$i);
			if (isset($cost['wood'])) $builders += $cost['wood'] * 0.01;
			if (isset($cost['wine'])) $builders += $cost['wine'] * 0.01;
			if (isset($cost['marble'])) $builders += $cost['marble'] * 0.01;
			if (isset($cost['crystal'])) $builders += $cost['crystal'] * 0.01;
			if (isset($cost['sulfur'])) $builders += $cost['sulfur'] * 0.01;
		}
	}
	
	// Building level score
	$buildlvl = $Buildings->fetchRow(
		$Buildings->select()
			->from($Buildings->_name, 'SUM(build_lvl) AS buildlvl')
			->where('build_town IN (?)', $cities)
	)->buildlvl;
	
	// Calculate research score
	$research = 0;
	$researchRow = $Users_Researches->fetchRow($Users_Researches->select()->where('usr_id = ?',$User->usr_id));
	for ($i=1;$i<=59;$i++) {
		if ($researchRow->{'research_'.$i} == 1) $research += 4;
	}
	
	// Calculate military score
	$military = 0;
	$unitRows = $Towns_Units->fetchAll(
		$Towns_Units->select()
			->from($Towns_Units->_name, array('unit_id', 'value'))
			->where('town_id IN (?)', $cities)
			->where('value > 0')
	);
	foreach ($unitRows as $unitRow) {
		$unitDetail = $Units->fetchRow(
			$Units->select()
				->from($Units->_name,array('unit_costwood', 'unit_costwine', 'unit_costcrystal', 'unit_costsulfur'))
				->where('unit_id = ?', $unitRow->unit_id)
		);
		$military += $unitDetail->unit_costwood * 0.02 * $unitRow->value;
		$military += $unitDetail->unit_costwine * 0.02 * $unitRow->value;
		$military += $unitDetail->unit_costcrystal * 0.02 * $unitRow->value;
		$military += $unitDetail->unit_costsulfur * 0.02 * $unitRow->value;
	}
	
	// Gold score
	$gold = floor($User->usr_gold);
	
	// Scientists score
	$scientists = $Towns->fetchRow(
		$Towns->select()
			->from($Towns->_name, 'SUM(town_scientists) AS total')
			->where('town_id IN (?)', $cities)
	)->total;
	
	// Total score
	$total = $builders + $scientists + $military;
	$total += floor($Towns->fetchRow(
		$Towns->select()
			->from($Towns->_name, 'SUM(town_population) AS total')
			->where('town_id IN (?)', $cities)
	)->total);
	
	// Update the highscore
	$update = array(
		'highscore_builders' => $builders,
		'highscore_buildlvl' => $buildlvl,
		'highscore_scientists' => $scientists,
		'highscore_research' => $research,
		'highscore_military' => $military,
		'highscore_gold' => $gold,
		'highscore_total' => $total,
	);
	if ($Highscores->fetchRow($Highscores->select()->from($Highscores->_name,'COUNT(*) AS total')->where('highscore_user = ?',$User->usr_id))->total == 0) {
		$update['highscore_user'] = $User->usr_id;
		$Highscores->insert($update);
	} else {
		$where = $Highscores->getAdapter()->quoteInto('highscore_user = ?',$User->usr_id);
		$Highscores->update($update,$where);
	}
	echo('Done'."\n");
}

echo('Sorting positions...');

$scores = array(
	'highscore_builders',
	'highscore_buildlvl',
	'highscore_scientists',
	'highscore_research',
	'highscore_military',
	'highscore_gold',
	'highscore_offense',
	'highscore_defense',
	'highscore_trade',
	'highscore_resources',
	'highscore_donate',
	'highscore_total',
);

foreach ($scores as $score) {
	$positions = $Highscores->fetchAll(
		$Highscores->select()
			->from($Highscores->_name, 'highscore_user')
			->order($score.' DESC')
	);
	$pos = 1;
	foreach ($positions as $position) {
		$update = array();
		$update[$score.'_rank'] = $pos;
		$pos++;
		$where = $Highscores->getAdapter()->quoteInto('highscore_user = ?',$position->highscore_user);
		$Highscores->update($update,$where);
	}
}

echo('Done'."\n");