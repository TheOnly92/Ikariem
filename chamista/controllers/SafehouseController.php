<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Safehouse controller file.
 * Handle all actions redirected to the safehouse controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class SafehouseController extends Rewaz_Controller_Action_Buildings {
	public function preDispatch() {
		parent::preDispatch();
		
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = $request->getParam('cityId');
			if (!$town_id)
				$town_id = Zend_Registry::get('session')->current_city;
		}
		
		$Town = new Chamista_Model_Town($town_id);
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($Town->town_name,'/city/index/id/'.$Town->town_id, 'Back to the town');
		
		$position = intval($request->getParam('position'));
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::HIDEOUT) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$now = time();
		
		$town_id = $request->getParam('id');
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Hideout');
		
		if ($Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::HIDEOUT)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::HIDEOUT),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $Town->getPositionLvl($position),
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$Spies_Queue = new Chamista_Model_DbTable_Spies_Queue();
		$rows = $Spies_Queue->fetchAll($Spies_Queue->select()->where('town_id = ?',$town_id)->order('startdate ASC'));
		$training = array();
		$training_current = array();
		$current = true;
		foreach ($rows as $row) {
			if ($current) {
				$training_current = array(
					'startdate' => $row->startdate,
					'enddate' => $row->enddate,
					'currentdate' => $now,
					'remaining' => Chamista_Model_Format::formatTime($row->enddate - $now)
				);
			}
		}
		$this->view->training_current = $training_current;
		$this->view->training = $training;
		
		$maxSpy = $Town->getPositionLvl($position);
		$spiesAvail = $Town->getAvailSpies();
		$canTrain = $maxSpy - $Town->town_spies;
		if ($canTrain * 80 > $Town->getTownResource(3)) {
			$trainable = floor($Town->getTownResource(3) / 80);
		}
		$inUse = $Town->getSpiesInMission();
		
		$tab = 'general';
		switch ($request->getParam('tab')) {
			case 'report':
				$tab = 'report';
				$Spies_Reports = new Chamista_Model_DbTable_Spies_Reports();
				$reports = $Spies_Reports->getUserReports();
				
				break;
			default:
				$spies = array();
				$Mission = new Chamista_Model_DbTable_Spies_Mission();
				foreach ($Mission->getTownSpies($Town->town_id) as $spy) {
					$Destination = new Chamista_Model_Town($spy->spy_destination);
					$spies[] = array(
						'destinationTown' => $Destination->town_name,
						'townId' => $Destination->town_id,
						'arrivalTime' => $spy->spy_arrivaltime,
						'destinationPos' => $Destination->getIslandXY(),
						'mission' => Chamista_Model_Format::getSpyMission($spy->spy_mission),
						'risk' => $spy->spy_risk,
						'missionId' => $spy->spy_mission,
						'id' => $spy->spy_id,
						'countDown' => Chamista_Model_Format::formatTime($spy->spy_arrivaltime - time()),
					);
				}
				$this->view->spies = $spies;
				break;
		}
		
		$this->view->hideout = array(
			'maxSpy' => $maxSpy,
			'spiesAvail' => $spiesAvail,
			'inUse' => $inUse,
			'canTrain' => $canTrain,
			'trainingTime' => Chamista_Model_Format::formatTime(Chamista_Model_Formula::trainSpyTime($Town->getPositionLvl($position))),
			'trainable' => $trainable,
			'tab' => $tab,
		);
		$this->view->position = $position;
		$this->view->build_lvl = $Town->getPositionLvl($position);
		$this->view->town_id = $Town->town_id;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::HIDEOUT,$this->view->build_lvl + 1,$User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::HIDEOUT,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::HIDEOUT);
		$this->view->css = 'ik_safehouse_'.VERSION.'.css';
		$this->view->body_id = 'safehouse';
	}
	
	public function missionsAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Mission = new Chamista_Model_DbTable_Spies_Mission();
		
		$town_id = intval($request->getParam('id'));
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Hideout');
		
		$spyId = intval($request->getParam('spy'));
		
		if (!$Mission->spyValid($spyId, $town_id)) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$spyInfo = $Mission->getSpyInfo($spyId, $town_id);
		$Destination = new Chamista_Model_Town($spyInfo->spy_destination);
		
		$this->view->safehouseMissions = array(
			'desTownName' => $Destination->town_name,
			'currentRisk' => $spyInfo->spy_risk,
		);
		
		$this->view->position = $position;
		$this->view->town_id = $Town->town_id;
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::HIDEOUT);
		$this->view->css = 'ik_safehouseMissions_'.VERSION.'.css';
		$this->view->body_id = 'safehouseMissions';
	}
	
	public function buildspyAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Spies_Queue = new Chamista_Model_DbTable_Spies_Queue();
		
		$town_id = intval($request->getParam('id'));
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		$lvl = $Town->getPositionLvl($position);
		
		if ($Town->isBeingExpanded($position)) {
			$this->_helper->flashMessenger->addMessage("A building is already being constructed or expanded!");
			$this->_helper->redirector('index','error');
		}
		
		$maxSpy = $Town->getPositionLvl($position);
		$spiesAvail = $Town->getAvailSpies();
		$canTrain = $maxSpy - $Town->town_spies;
		if ($canTrain * 80 > $Town->getTownResource(3)) {
			$canTrain = floor($Town->getTownResource(3) / 80);
		}
		$inUse = $Town->getSpiesInMission();
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'*' => 'Int'
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$spies = $zfi->textfield_spy;
				if ($spies > $canTrain) {
					$this->_helper->flashMessenger->addMessage("You don't have enough crystal glass!");
					$this->_helper->redirector('index','error');
				}
				$Town->town_resource3 = $Town->town_resource3 - $spies * 80;
				$User->usr_gold = $User->usr_gold - 150;
				
				$now = time();
				$enddate = $now + Chamista_Model_Formula::trainSpyTime($lvl) * $spies;
				$insert = array(
					'town_id' => $town_id,
					'startdate' => $now,
					'enddate' => $enddate,
					'value' => $spies
				);
				$Spies_Queue->insert($insert);
			}
		}
		
		$this->_helper->redirector('index','safehouse','',array('id' => $town_id, 'position' => $position));
	}
}