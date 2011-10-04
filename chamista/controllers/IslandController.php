<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Island controller file.
 * Handles all actions redirected to the island controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class IslandController extends Zend_Controller_Action {
	private $Town;
	
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Barbarians = new Chamista_Model_DbTable_Barbarian();
		$Mission = new Chamista_Model_DbTable_Spies_Mission();
		$Highscores = new Chamista_Model_DbTable_Highscores();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$this->view->currCity = $town_id;
		$this->Town = new Chamista_Model_Town($town_id);
		
		$island_id = intval($request->getParam('id'));
		if (!$island_id) {
			$cityId = intval($request->getParam('cityId'));
			if (!$cityId)
				$island_id = $this->Town->town_island;
			else {
				$SelCity = new Chamista_Model_Town($cityId);
				$island_id = $SelCity->town_island;
				$this->view->selectCity = $cityId;
			}
		}
		
		$selectCity = intval($request->getParam('selectCity'));
		if ($selectCity) {
			$this->view->selectCity = $selectCity;
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $island_id));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('<img src="/img/layout/icon-world.gif" alt="World" /> World','/worldmap/index/islandx/'.$row->island_posx.'/islandy/'.$row->island_posy,'Back to the world map');
		$Trailer->addStep($row->island_name.' ['.$row->island_posx.':'.$row->island_posy.']');
		
		// If user's capital is on this island
		$isCapital = false;
		$barbarian = array();
		$Capital = new Chamista_Model_Town($User->usr_capital);
		if ($Capital->town_island == $island_id) {
			$isCapital = true;
			$barbarian = Chamista_Model_Formula::getBarbarianStatus($Barbarians->getUserAttacked($User->usr_id) + 1);
			$barbarian['level'] = $Barbarians->getUserAttacked($User->usr_id) + 1;
		}
		
		// Get towns on the island
		$Towns = new Chamista_Model_DbTable_Towns();
		$towns_view = array();
		$towns_ids = $Towns->fetchAll($Towns->select('town_id')->where('town_island = ?', $island_id));
		foreach ($towns_ids as $town_id) {
			$Town = new Chamista_Model_Town($town_id->town_id);
			$Owner = new Chamista_Model_User($Town->town_uid);
			
			$towns_view[$Town->town_location] = array(
				'town_lvl' => $Town->town_lvl,
				'town_id' => $Town->town_id,
				'town_name' => $Town->town_name,
				'owner_name' => $Town->getOwnerName(),
				'town_uid' => $Town->town_uid,
				'vacation' => ($Owner->usr_vacation > 0) ? true : false,
				'has_spy' => $Mission->hasSpy($this->Town->town_id, $Town->town_id),
				'highscore' => $Highscores->getUserHighscore($Town->town_uid),
			);
		}
		
		$this->view->towns = $towns_view;
		$this->view->island = array(
			'island_type' => $row->island_type,
			'island_name' => $row->island_name,
			'island_saw_lvl' => $row->island_saw_lvl,
			'trade_plain' => Chamista_Model_Format::tradegood_plaintext($row->island_resource),
			'trade_mine' => Chamista_Model_Format::tradegood_mine($row->island_resource),
			'island_resource_lvl' => $row->island_resource_lvl,
			'island_wonder' => $row->island_wonder,
			'island_id' => $row->island_id,
			'isCapital' => $isCapital,
			'barbarian' => $barbarian,
			'hasTroops' => $this->Town->hasTroops(),
		);
		
		$espionage = array(
			'show' => false,
			'enabled' => false,
		);
		if ($User->userResearched(11)) {
			$espionage['show'] = true;
			if ($this->Town->getAvailSpies() > 0) {
				$espionage['enabled'] = true;
			}
		}
		
		$this->view->espionage = $espionage;
		$this->view->self_id = $User->usr_id;
		$this->view->body_id = 'island';
		$this->view->css = 'ik_island_'.VERSION.'.css';
	}
	
	public function resourceAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$island_id = $request->getParam('id');
		if (!$island_id) {
			$island_id = $Town->town_island;
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		if (!$Islands->userOwns($User->usr_id,$island_id)) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $island_id));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('<img src="/img/layout/icon-world.gif" alt="World" /> World','/worldmap/index/islandx/'.$row->island_posx.'/islandy/'.$row->island_posy,'Back to the world map');
		$Trailer->addStep($row->island_name.' ['.$row->island_posx.':'.$row->island_posy.']','/island/index/'.$island_id, 'Back to the island');
		$Trailer->addStep('Saw Mill');
		
		if ($Islands->islandMineUpgrading(0,$island_id)) {
			$Islands_Queue = new Chamista_Model_DbTable_Islands_Queue();
			$qrow = $Islands_Queue->fetchRow($Islands_Queue->select()
				->where('queue_island = ?',$island_id)
				->where('queue_type = ?',0));
			$now = time();
			
			$this->view->construction = array(
				'currentlevel' => $row->island_saw_lvl,
				'nextlevel' => $row->island_saw_lvl + 1,
				'startdate' => $qrow->queue_starttime,
				'enddate' => $qrow->queue_endtime,
				'currentdate' => $now,
				'countDown' => Chamista_Model_Format::formatTime($qrow->queue_endtime - $now),
				'progress' => floor(($qrow->queue_endtime - $now) / ($qrow->queue_endtime - $qrow->queue_starttime) * 100),
			);
		}
		
		$this->view->island = array(
			'island_type' => $row->island_type,
			'island_name' => $row->island_name,
			'island_saw_lvl' => $row->island_saw_lvl,
			'trade_plain' => Chamista_Model_Format::tradegood_plaintext($row->island_resource),
			'trade_mine' => Chamista_Model_Format::tradegood_mine($row->island_resource),
			'island_resource_lvl' => $row->island_resource_lvl,
			'island_wonder' => $row->island_wonder,
			'island_id' => $row->island_id,
			'overload' => ($User->userResearched(37)) ? true : false,
		);
		
		$this->view->upgrade_cost = Chamista_Model_Formula::sawUpgradeCost($row->island_saw_lvl+1);
		$this->view->donated = $row->island_saw_donated;
		
		$maxDonate = $this->view->upgrade_cost['wood'] - $this->view->donated;
		$resource0 = $Town->getTownResource(0);
		if ($maxDonate > $resource0) {
			$maxDonate = $resource0;
		}
		$this->view->maxDonate = $maxDonate;
		
		$this->view->woodProduction = floor($Town->getWoodProduction());
		$this->view->goldProduction = floor($Town->getGoldIncome());
		
		$workers = $Town->getLumberWorkers();
		$freeCitizen = $Town->getFreeCitizens();
		$maxWorkers = Chamista_Model_Formula::sawMaxWorkers($row->island_saw_lvl);
		$maxValue = 0;
		$this->view->overload = floor($maxWorkers / 2);
		if ($freeCitizen + $workers > $maxWorkers) {
			$maxValue = $maxWorkers;
		} else {
			$maxValue = $freeCitizen + $workers;
		}
		$this->view->maxWorkers = $maxValue;
		
		$select = $Towns->select();
		$select->setIntegrityCheck(false);
		$towns_rows = $Towns->fetchAll($select
			->from(array('t' => $Towns->_name),array('town_name','town_id','town_saw_donated','town_lvl','town_lumber', 'town_lumber_overload'))
			->join(array('u' => 'users'),'u.usr_id = t.town_uid',array('usr_nick','usr_id'))
			->where('town_island = ?',$island_id)->order('usr_nick ASC'));
		$other_towns = array();
		foreach ($towns_rows as $town_row) {
			$other_towns[] = array(
				'player' => $town_row->usr_nick,
				'town' => $town_row->town_name,
				'level' => $town_row->town_lvl,
				'workers' => $town_row->town_lumber + $town_row->town_lumber_overload,
				'donated' => $town_row->town_saw_donated,
				'owner' => $town_row->usr_id
			);
		}
		$this->view->towns = $other_towns;
		$this->view->resource = array(
			'townId' => $Town->town_id,
			'freeCitizens' => $Town->getFreeCitizens(),
			'workers' => $Town->getLumberWorkers() + $Town->town_lumber_overload,
			'owner' => $Town->town_uid,
			'income' => $Town->getGoldIncome(),
			'corruption' => 1 - ($Town->getCorruption() / 100),
		);
		$this->view->body_id = 'resource';
	}
	
	public function tradegoodAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$island_id = $request->getParam('id');
		if (!$island_id) {
			$island_id = $Town->town_island;
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		if (!$Islands->userOwns($User->usr_id,$island_id)) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		
		if (!$User->userResearched(10)) {
			$this->_helper->redirector('noluxury','island','',array('id' => $island_id));
		}
		
		$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $island_id));
		$tradegood = $row->island_resource;
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('<img src="/img/layout/icon-world.gif" alt="World" /> World','/worldmap/index/islandx/'.$row->island_posx.'/islandy/'.$row->island_posy,'Back to the world map');
		$Trailer->addStep($row->island_name.' ['.$row->island_posx.':'.$row->island_posy.']','/island/index/'.$island_id, 'Back to the island');
		$Trailer->addStep(Chamista_Model_Format::tradegood_mine($tradegood));
		
		if ($Islands->islandMineUpgrading(1,$island_id)) {
			$Islands_Queue = new Chamista_Model_DbTable_Islands_Queue();
			$qrow = $Islands_Queue->fetchRow($Islands_Queue->select()
				->where('queue_island = ?',$island_id)
				->where('queue_type = ?',1));
			$now = time();
			
			$this->view->construction = array(
				'currentlevel' => $row->island_resource_lvl,
				'nextlevel' => $row->island_resource_lvl + 1,
				'startdate' => $qrow->queue_starttime,
				'enddate' => $qrow->queue_endtime,
				'currentdate' => $now,
				'countDown' => Chamista_Model_Format::formatTime($qrow->queue_endtime - $now),
				'progress' => floor(($qrow->queue_endtime - $now) / ($qrow->queue_endtime - $qrow->queue_starttime) * 100),
			);
		}
		
		$this->view->island = array(
			'island_type' => $row->island_type,
			'island_name' => $row->island_name,
			'island_saw_lvl' => $row->island_saw_lvl,
			'trade_plain' => Chamista_Model_Format::tradegood_plaintext($row->island_resource),
			'trade_mine' => Chamista_Model_Format::tradegood_mine($row->island_resource),
			'trade_css' => Chamista_Model_Format::tradegood_css($row->island_resource),
			'island_resource_lvl' => $row->island_resource_lvl,
			'island_wonder' => $row->island_wonder,
			'island_id' => $row->island_id,
			'overload' => ($User->userResearched(37)) ? true : false,
		);
		
		$this->view->upgrade_cost = Chamista_Model_Formula::tradeUpgradeCost($row->island_resource_lvl+1);
		$this->view->donated = $row->island_resource_donated;
		
		$maxDonate = $this->view->upgrade_cost['wood'] - $this->view->donated;
		$resource0 = $Town->getTownResource(0);
		if ($maxDonate > $resource0) {
			$maxDonate = $resource0;
		}
		$this->view->maxDonate = $maxDonate;
		
		$this->view->tradeProduction = floor($Town->getResourceProduction());
		$this->view->goldProduction = floor($Town->getGoldIncome());
		
		$workers = $Town->getResourceWorkers();
		$freeCitizen = $Town->getFreeCitizens();
		$maxWorkers = Chamista_Model_Formula::tradeMaxWorkers($row->island_resource_lvl);
		$maxValue = 0;
		$this->view->overload = floor($maxWorkers / 2);
		if ($freeCitizen + $workers > $maxWorkers) {
			$maxValue = $maxWorkers;
		} else {
			$maxValue = $freeCitizen + $workers;
		}
		$this->view->maxWorkers = $maxValue;
		
		$select = $Towns->select();
		$select->setIntegrityCheck(false);
		$towns_rows = $Towns->fetchAll($select
			->from(array('t' => $Towns->_name),array('town_name','town_id','town_tradegood_donated','town_lvl','town_miner', 'town_miner_overload'))
			->join(array('u' => 'users'),'u.usr_id = t.town_uid',array('usr_nick','usr_id'))
			->where('town_island = ?',$island_id)->order('usr_nick ASC'));
		$other_towns = array();
		foreach ($towns_rows as $town_row) {
			$other_towns[] = array(
				'player' => $town_row->usr_nick,
				'town' => $town_row->town_name,
				'level' => $town_row->town_lvl,
				'workers' => $town_row->town_miner + $town_row->town_miner_overload,
				'donated' => $town_row->town_tradegood_donated,
				'owner' => $town_row->usr_id
			);
		}
		$this->view->towns = $other_towns;
		
		$description = '';
		switch ($row->island_resource) {
			case 1:
				$description = 'Golden sunlight falls down on the gentle slopes. A perfect place for cultivating wine of excellent quality.';
				break;
			case 2:
				$description = 'The old stone pit has a surprisingly large amount of marble with first class quality.';
				break;
			case 3:
				$description = 'There are large deposits of rock crystal lying under the hills surrounding your town. We can use it to make precious crystal glass!';
				break;
			case 4:
				$description = 'An experienced alchemist knows that a biting smell in the air is a clear indication of a rich source of sulphur in the area.';
				break;
		}
		
		$this->view->description = $description;
		$this->view->tradegood = array(
			'townId' => $Town->town_id,
			'freeCitizens' => $Town->getFreeCitizens(),
			'workers' => $Town->getResourceWorkers() + $Town->town_miner_overload,
			'owner' => $Town->town_uid,
			'income' => $Town->getGoldIncome(),
			'corruption' => 1 - ($Town->getCorruption() / 100),
		);
		$this->view->body_id = 'tradegood';
		$this->view->css = 'ik_tradegood_'.VERSION.'.css';
	}
	
	public function noluxuryAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$island_id = $request->getParam('id');
		if (!$island_id) {
			$island_id = $Town->town_island;
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		if (!$Islands->userOwns($User->usr_id,$island_id)) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $island_id));
		$tradegood = $row->island_resource;
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($row->island_name.' ['.$row->island_posx.':'.$row->island_posy.']','/island/index/'.$island_id, 'Back to the island');
		$Trailer->addStep(Chamista_Model_Format::tradegood_mine($tradegood));
		
		$description = '';
		switch ($tradegood) {
			case 1:
				$description = 'We can cultivate wine on the sunny hills of the area. In order to use the wine we will have to research "Wealth" first. Only then can we serve it to our citizens in the tavern or equip cooks with it.';
				break;
			case 2:
				$description = 'There is a lot of marble left in the old quarry! We will have to research "Wealth" first, though. Then we can use this marble to build even larger and more beautiful buildings! Our town will prosper!';
				break;
			case 3:
				$description = 'There are vast amounts of rock crystal in the caves of this area. If we research "Wealth", we can use it for precious crystal glass!';
				break;
			case 4:
				$description = 'The pungent smell in the air is a strong indication for a rich source of sulphur in the area. In order to mine sulphur, we will have to research "Wealth" first. Then we can mine all the earth`s treasures and use sulphur.';
				break;
		}
		
		$this->view->description = $description;
		$this->view->resource_css = Chamista_Model_Format::tradegood_css($tradegood);
		$this->view->resource_mine = Chamista_Model_Format::tradegood_mine($tradegood);
		$this->view->island_id = $island_id;
		$this->view->css = 'ik_noluxury_'.VERSION.'.css';
		$this->view->body_id = 'noluxury';
	}
	
	public function setworkersAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$town_id = $request->getParam('cityId');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		$Town = new Chamista_Model_Town($town_id);
		
		$island_id = $request->getParam('id');
		if (!$island_id) {
			$island_id = $Town->town_island;
		}
		
		if ($Town->town_island != $island_id) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		if (!$Islands->userOwns($User->usr_id,$island_id)) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'rw' => 'Int',
				'type' => 'Alpha',
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$inputWorkers = $zfi->rw;
				$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $island_id));
				if ($zfi->type == 'resource') {
					// Maximum workers for the current level of saw mill
					$maxWorkers = Chamista_Model_Formula::sawMaxWorkers($row->island_saw_lvl);
					$overloaded = 0;
					if ($inputWorkers > $maxWorkers) {
						if ($User->userResearched(18)) {
							$origMax = $maxWorkers;
							$maxWorkers += floor($maxWorkers / 2);
							if ($inputWorkers > $maxWorkers) {
								$inputWorkers = $maxWorkers;
							}
							$overloaded = $inputWorkers - $origMax;
							$inputWorkers = $origMax;
						} else {
							$inputWorkers = $maxWorkers;
						}
					}
					if ($inputWorkers < 0) $inputWorkers = 0;
					$Town->town_lumber = $inputWorkers;
					$Town->town_lumber_overload = $overloaded;
				} elseif ($zfi->type == 'tradegood') {
					// Maximum workers for the current level of mine
					$maxWorkers = Chamista_Model_Formula::tradeMaxWorkers($row->island_resource_lvl);
					$overloaded = 0;
					if ($inputWorkers > $maxWorkers) {
						if ($User->userResearched(18)) {
							$origMax = $maxWorkers;
							$maxWorkers += floor($maxWorkers / 2);
							if ($inputWorkers > $maxWorkers) {
								$inputWorkers = $maxWorkers;
							}
							$overloaded = $inputWorkers - $origMax;
							$inputWorkers = $origMax;
						} else {
							$inputWorkers = $maxWorkers;
						}
					}
					if ($inputWorkers < 0) $inputWorkers = 0;
					$Town->town_miner = $inputWorkers;
					$Town->town_miner_overload = $overloaded;
				}
				$this->_helper->redirector($zfi->type,'island','',array('id' => $island_id));
			}
		}
		
		// Redirect back to the saw mill
		$this->_helper->redirector('index','island','',array('id' => $island_id));
	}
	
	public function donateAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		$now = time();
		
		$town_id = $request->getParam('cityId');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		$Town = new Chamista_Model_Town($town_id);
		
		$island_id = $request->getParam('id');
		if (!$island_id) {
			$island_id = $Town->town_island;
		}
		
		if ($Town->town_island != $island_id) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		if (!$Islands->userOwns($User->usr_id,$island_id)) {
			$this->_helper->flashMessenger->addMessage("This is not your town");
			$this->_helper->redirector('index','error');
		}
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'donation' => 'Int',
				'type' => 'Alpha',
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());

			if ($zfi->isValid()) {
				$donation = $zfi->donation;
				$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $island_id));
				if ($zfi->type == 'resource') {
					if ($Islands->islandMineUpgrading(0,$island_id)) {
						$this->_helper->flashMessenger->addMessage("The lumber mill is under construction!");
						$this->_helper->redirector('index','error');
					}
					$donated = $row->island_saw_donated;
					$upgradeCost = Chamista_Model_Formula::sawUpgradeCost($row->island_saw_lvl+1);
					$upgradeCost = $upgradeCost['wood'];
					$maxDonate = $upgradeCost - $donated;
					$resource0 = $Town->getTownResource(0);
					if ($donation > $resource0 || $donation > $maxDonate) {
						$this->_helper->flashMessenger->addMessage("You don't have enough wood or gold!");
						$this->_helper->redirector('index','error');
					}
					
					// Donation successful
					$Town->town_resource0 -= $donation;
					$Town->town_saw_donated += $donation;
					
					if ($donated + $donation == $upgradeCost) {
						// Upgrade mine
						$Islands_Queue = new Chamista_Model_DbTable_Islands_Queue();
						
						$data = array(
							'queue_island' => $island_id,
							'queue_type' => 0,
							'queue_endtime' => $now + Chamista_Model_Formula::sawUpgradeTime($row->island_saw_lvl + 1),
							'queue_starttime' => $now
						);
						$Islands_Queue->insert($data);
						$data = array('island_saw_donated' => 0);
						$where = $Islands->getAdapter()->quoteInto('island_id = ?', $island_id);
						$Islands->update($data,$where);
					} else {
						$data = array('island_saw_donated' => $row->island_saw_donated + $donation);
						$where = $Islands->getAdapter()->quoteInto('island_id = ?', $island_id);
						$Islands->update($data,$where);
					}
					$this->_helper->redirector('resource','island','',array('id' => $island_id));
				} elseif ($zfi->type == 'tradegood') {
					if ($Islands->islandMineUpgrading(1,$island_id)) {
						$this->_helper->flashMessenger->addMessage("The mine is under construction!");
						$this->_helper->redirector('index','error');
					}
					$donated = $row->island_resource_donated;
					$upgradeCost = Chamista_Model_Formula::tradeUpgradeCost($row->island_resource_lvl+1);
					$upgradeCost = $upgradeCost['wood'];
					$maxDonate = $upgradeCost - $donated;
					$resource0 = $Town->getTownResource(0);
					if ($donation > $resource0 || $donation > $maxDonate) {
						$this->_helper->flashMessenger->addMessage("You don't have enough wood or gold!");
						$this->_helper->redirector('index','error');
					}
					
					// Donation successful
					$Town->town_resource0 -= $donation;
					$Town->town_tradegood_donated += $donation;
					
					if ($donated + $donation >= $upgradeCost) {
						// Upgrade mine
						$Islands_Queue = new Chamista_Model_DbTable_Islands_Queue();
						
						$data = array(
							'queue_island' => $island_id,
							'queue_type' => 1,
							'queue_endtime' => $now + Chamista_Model_Formula::tradeUpgradeTime($row->island_resource_lvl + 1),
							'queue_starttime' => $now
						);
						$Islands_Queue->insert($data);
						$data = array('island_resource_donated' => 0);
						$where = $Islands->getAdapter()->quoteInto('island_id = ?', $island_id);
						$Islands->update($data,$where);
					} else {
						$data = array('island_resource_donated' => $row->island_resource_donated + $donation);
						$where = $Islands->getAdapter()->quoteInto('island_id = ?', $island_id);
						$Islands->update($data,$where);
					}
					$this->_helper->redirector('tradegood','island','',array('id' => $island_id));
				}
			}
		}
		
		// Redirect back to the saw mill
		$this->_helper->redirector('index','island','',array('id' => $island_id));
	}
	
	public function colonizeAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$town_id = $request->getParam('cityId');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		$Town = new Chamista_Model_Town($town_id);
		
		$island_id = $request->getParam('id');
		if (!$island_id) {
			$island_id = $Town->town_island;
		}
		
		$position = intval($request->getParam('position'));
		
		$Islands = new Chamista_Model_DbTable_Islands();
		
		$islandRow = $Islands->fetchRow($Islands->select()->where('island_id = ?',$island_id));
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('<img src="/img/layout/icon-world.gif" alt="World" /> World','/worldmap/index/islandx/'.$islandRow->island_posx.'/islandy/'.$islandRow->island_posy,'Back to the world map');
		$Trailer->addStep($islandRow->island_name.' ['.$islandRow->island_posx.':'.$islandRow->island_posy.']','/island/index/'.$islandRow->island_id, 'Back to the island');
		$Trailer->addStep('Colonise');
		
		$tcount = $Towns->fetchRow($Towns->select()->from($Towns->_name,'COUNT(*) AS total')->where('town_island = ?',$island_id)->where('town_location = ?',$position));
		if ($tcount->total > 0) {
			$this->_helper->flashMessenger->addMessage('The building ground is already taken!');
			$this->_helper->redirector('index','error');
		}
		
		$colonize = array();
		// Check for prerequisites
		$Capital = new Chamista_Model_Town($User->usr_capital);
		$colonize['palace'] = $Capital->getPalaceLvl();
		$error = array();
		if (count($User->getUserCityIDs()) == $colonize['palace'] + 1) {
			$error[] = array('no' => 4, 'colonies' => count($User->getUserCityIDs()) - 1);
		}
		if ($Town->getFreeCitizens() < 40) {
			$error[] = array('no' => 1, 'lack' => 40 - $Town->getFreeCitizens());
		}
		if ($User->usr_gold < 9000) {
			$error[] = array('no' => 2, 'lack' => 9000 - floor($User->usr_gold));
		}
		if ($Town->getTownResource(0) < 1250) {
			$error[] = array('no' => 3, 'lack' => 1250 - $Town->getTownResource(0));
		}
		$colonize['error'] = $error;
		
		$max = array();
		if ($Town->getTownResource(0) - 1250 > 1250) {
			$max['wood'] = 1250;
		} else {
			$max['wood'] = $Town->getTownResource(0) - 1250;
		}
		if ($Town->getTownResource(1) > 1500) {
			$max['wine'] = 1500;
		} else {
			$max['wine'] = $Town->getTownResource(1);
		}
		if ($Town->getTownResource(2) > 1500) {
			$max['marble'] = 1500;
		} else {
			$max['marble'] = $Town->getTownResource(2);
		}
		if ($Town->getTownResource(3) > 1500) {
			$max['crystal'] = 1500;
		} else {
			$max['crystal'] = $Town->getTownResource(3);
		}
		if ($Town->getTownResource(4) > 1500) {
			$max['sulfur'] = 1500;
		} else {
			$max['sulfur'] = $Town->getTownResource(4);
		}
		$colonize['max'] = $max;
		$colonize['ships'] = $User->getFreeShips();
		$colonize['cargo'] = $User->getFreeShips() * 500 - 1290;
		if ($colonize['cargo'] < 0) $colonize['cargo'] = 0;
		$colonize['destination'] = array(
			'name' => $islandRow->island_name,
			'time' => Chamista_Model_Format::formatTime(Chamista_Model_Formula::travelTime(60, array('x' => $islandRow->island_posx, 'y' => $islandRow->island_posy), $Town->getIslandXY())),
		);
		
		$this->view->island_id = $island_id;
		$this->view->position = $position;
		$this->view->colonize = $colonize;
		$this->view->css = 'ik_colonize_'.VERSION.'.css';
		$this->view->body_id = 'colonize';
	}
}