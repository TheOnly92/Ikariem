<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Carpenter controller file.
 * Handle all actions redirected to the carpenter controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */
class CarpenteringController extends Rewaz_Controller_Action_Buildings {
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
		if ($this->Town->getGroundBuilding($position) != Chamista_Model_Formula::CARPENTER) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
		$request = $this->getRequest();
		$now = time();
		
		$position = intval($request->getParam('position'));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Carpenter');
		
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$this->Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::CARPENTER)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::CARPENTER),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $lvl,
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$nextDiscount = Chamista_Model_Formula::carpenterDiscount($lvl+1) * 100;
		$currDiscount = Chamista_Model_Formula::carpenterDiscount($lvl) * 100;
		$researchBuilding = Chamista_Model_Formula::getResearchDiscount($this->User,1) * 100;
		
		$researches_width = (100 - $researchBuilding) / 100 * 99;
		$buildings = array(
			'researches' => $researchBuilding,
			'researches_per' => 100 - $researchBuilding,
			'researches_width' => $researches_width,
			'carpenter' => $currDiscount,
			'carpenter_per' => 100 - $currDiscount - $researchBuilding,
		);
		
		$units = array(
			'carpenter' => $currDiscount,
			'carpenter_per' => 100 - $currDiscount
		);
		
		$carpentering = array(
			'buildings' => $buildings,
			'units' => $units,
			'nextDiscount' => $nextDiscount,
			'currDiscount' => $currDiscount,
		);
		
		$this->view->buildings = $buildings;
		$this->view->units = $units;
		$this->view->town_id = $this->Town->town_id;
		$this->view->build_lvl = $lvl;
		$this->view->position = $position;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::CARPENTER,$this->view->build_lvl + 1,$this->User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::CARPENTER,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::CARPENTER);
		$this->view->nextDiscount = $nextDiscount;
		$this->view->currDiscount = $currDiscount;
		$this->view->body_id = 'carpentering';
		$this->view->css = 'ik_carpentering_'.VERSION.'.css';
	}
}