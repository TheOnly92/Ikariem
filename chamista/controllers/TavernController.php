<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Tavern controller file.
 * Handle all actions redirected to the tavern controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class TavernController extends Rewaz_Controller_Action_Buildings {
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
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::TAVERN) {
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
		$Trailer->addStep('Tavern');
		
		$lvl = $Town->getPositionLvl($position);
		
		if ($Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::TAVERN)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::TAVERN),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $lvl,
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$serve = array();
		for ($i=1;$i<=$lvl;$i++) {
			$serve[$i] = Chamista_Model_Formula::getTavernWine($lvl);
		}
		
		$tavern = array(
			'serve' => $serve,
			'serving' => $Town->town_wine,
		);
		
		$this->view->tavern = $tavern;
		
		$this->view->happiness = round($Town->getHappiness());
		$this->view->satisfaction_word = $Town->getSatisfaction();
		$this->view->satisfaction_img = Chamista_Model_Format::getSatisfactionImg($Town->getSatisfaction());
		$this->view->town_id = $Town->town_id;
		$this->view->build_lvl = $lvl;
		$this->view->position = $position;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::TAVERN,$this->view->build_lvl + 1,$User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::TAVERN,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::TAVERN);
		$this->view->body_id = 'tavern';
		$this->view->css = 'ik_tavern_'.VERSION.'.css';
	}
	
	public function assignwinepertickAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$now = time();
		
		$town_id = $request->getParam('id');
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		$lvl = $Town->getPositionLvl($position);
		
		if ($request->isPost()) {
			$filter = new Zend_Filter_Int();
			$amount = $filter->filter($request->getParam('amount'));
			
			if ($amount > $lvl || $amount < 0) {
				$this->_helper->flashMessenger->addMessage("The specified amount of wine could not be served.");
				$this->_helper->redirector('index','error');
			} else {
				$Town->town_wine = $amount;
				$Town->town_resource1 -= Chamista_Model_Formula::getTavernWine($amount);
			}
		}
		
		$this->_helper->redirector('index','tavern','',array('id' => $town_id, 'position' => $position));
	}
}