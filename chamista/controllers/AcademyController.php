<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Academy controller file.
 * Handle all actions redirected to the academy controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */
class AcademyController extends Rewaz_Controller_Action_Buildings {
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
		if ($this->Town->getGroundBuilding($position) != Chamista_Model_Formula::ACADEMY) {
			$this->_helper->flashMessenger->addMessage("You have entered an invalid URL!");
			$this->_helper->redirector('index','error');
		}
	}
	
	/**
	 * The index action
	 */
	public function indexAction() {
		$request = $this->getRequest();
		$position = intval($request->getParam('position'));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Academy');
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->getCurrBuildingQueue($this->Town->town_id, Chamista_Model_Formula::ACADEMY, $position);
			$now = time();
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::ACADEMY),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $this->Town->getPositionLvl($position),
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$maxAcademy = Chamista_Model_Formula::getMaxScientist($this->Town->getPositionLvl($position));
		if ($this->Town->getFreeCitizens() + $this->Town->town_scientists > $maxAcademy) {
			$this->view->maxValue = $maxAcademy;
		} else {
			$this->view->maxValue = $this->Town->getFreeCitizens() + $this->Town->town_scientists;
		}
		$this->view->academy = array(
			'freeCitizens' => $this->Town->getFreeCitizens(),
			'scientists' => $this->Town->town_scientists,
			'goldProduction' => floor($this->Town->getGoldIncome()),
			'researchPoints' =>  floor($this->Town->getResearchProduction()),
		);
		
		$this->view->position = $position;
		$this->view->build_lvl = $this->Town->getPositionLvl($position);
		$this->view->town_id = $this->Town->town_id;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::ACADEMY,$this->view->build_lvl + 1,$this->User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::ACADEMY,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::ACADEMY);
		$this->view->css = 'ik_academy_'.VERSION.'.css';
		$this->view->body_id = 'academy';
	}
	
	public function setscientistsAction() {
		$request = $this->getRequest();
		$position = intval($request->getParam('position'));
		
		$maxValue = 0;
		$maxAcademy = Chamista_Model_Formula::getMaxScientist($this->Town->getPositionLvl($position));
		if ($this->Town->getFreeCitizens() + $this->Town->town_scientists > $maxAcademy) {
			$maxValue = $maxAcademy;
		} else {
			$maxValue = $this->Town->getFreeCitizens() + $this->Town->town_scientists;
		}
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				's' => 'Int',
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$scientists = $zfi->s;
				if ($scientists > $maxValue) $scientists = $maxValue;
				if ($scientists < 0) $scientists = 0;
				$this->Town->town_scientists = $scientists;
			}
		}
		
		$this->_helper->redirector('index','academy','',array('id' => $this->Town->town_id,'position' => $position));
	}
}