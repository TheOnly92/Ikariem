<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Winegrower controller file.
 * Handle all actions redirected to the winegrower controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */
class WinegrowerController extends Rewaz_Controller_Action_Buildings {
// This protected property contains the Town model of the currently selected town
	protected $Town;
	// This protected property contains the user model of the logged in user
	protected $User;
	
	/**
	 * Initializes the required variables and makes necessary checks
	 */
	public function preDispatch() {
		parent::preDispatch();
		
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$this->User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = $request->getParam('cityId');
			if (!$town_id)
				$town_id = Zend_Registry::get('session')->current_city;
		}
		
		$this->Town = new Chamista_Model_Town($town_id);
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($this->Town->town_name,'/city/index/id/'.$this->Town->town_id, 'Back to the town');
		
		$position = intval($request->getParam('position'));
		if ($this->Town->getGroundBuilding($position) != Chamista_Model_Formula::WINEGROWER) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
		$request = $this->getRequest();
		$now = time();
		
		$position = intval($request->getParam('position'));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Winery');
		
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$this->Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::WINEGROWER)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::WINEGROWER),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $lvl,
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$rawProduction = $this->Town->getResourceWorkers();
		if ($this->Town->getCorruption()) $rawProduction *= ($this->Town->getCorruption() / 100);
		$rawProduction += $this->Town->town_miner_overload * 0.25;
		$rawProduction = floor($rawProduction);
		$rawProduction *=  SPEED;
		$bonus = $lvl * 2;
		$bonusProduction = $rawProduction * $bonus/100;
		$totalProduction = $rawProduction + $bonusProduction;
		
		$this->view->winegrower = array(
			'rawProduction' => $rawProduction,
			'bonusProduction' => $bonusProduction,
			'totalProduction' => $totalProduction,
			'bonus' => $bonus,
		);
		
		$this->view->nextBonus = ($lvl + 1) * 2;
		$this->view->town_id = $this->Town->town_id;
		$this->view->build_lvl = $lvl;
		$this->view->position = $position;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::WINEGROWER,$this->view->build_lvl + 1,$this->User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::WINEGROWER,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::WINEGROWER);
		$this->view->body_id = 'winegrower';
		$this->view->css = 'ik_winegrower_'.VERSION.'.css';
	}
}