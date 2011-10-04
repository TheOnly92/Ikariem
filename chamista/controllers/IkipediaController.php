<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Ikipedia controller file.
 * Handles all actions redirected to the Ikipedia controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class IkipediaController extends Zend_Controller_Action {
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
	
	public function researchdetailAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$request = $this->getRequest();
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Research Detail Page');
		
		if (!$request->getParam('researchId')) {
			$this->_helper->redirector('researchDetail','ikipedia','',array('researchId' => 1));
		}
		
		$Researches = new Chamista_Model_DbTable_Researches();
		
		$seafaring = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::SEAFARING)->order('research_id ASC'));
		foreach ($rows as $row) {
			$seafaring[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
			);
		}
		
		$economy = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::ECONOMY)->order('research_id ASC'));
		foreach ($rows as $row) {
			$economy[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
			);
		}
		
		$science = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::SCIENCE)->order('research_id ASC'));
		foreach ($rows as $row) {
			$science[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
			);
		}
		
		$military = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::MILITARY)->order('research_id ASC'));
		foreach ($rows as $row) {
			$military[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
			);
		}
		
		// Get research details
		$research_id = intval($request->getParam('researchId'));
		$research = $Researches->fetchRow($Researches->select()->where('research_id = ?',$research_id)->limit(1));
		if (!$research) {
			$this->_helper->flashMessenger->addMessage("The specified research is not found!");
			$this->_helper->redirector('index','error');
		}
		
		if ($User->userResearched($research_id)) {
			$time = 'Research is already finished!';
		} else {
			$time = Chamista_Model_Format::formatTime($research->research_points / ($User->getGlobalResearch() / 3600)).' ('.$research->research_points.')';
		}
		$next_row = $Researches->fetchRow($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',$research->research_cat)->where('research_id > ?',$research_id)->order('research_id ASC')->limit(1));
		if (!$next_row) {
			$next_row = $research;
		}
		if ($research->research_required1) {
			$required1_row = $Researches->fetchRow($Researches->select()->from($Researches->_name,array('research_id','research_name','research_cat'))->where('research_id = ?',$research->research_required1));
			$required1 = array(
				'field' => Chamista_Model_Formula::tech_type_name($required1_row->research_cat),
				'name' => $required1_row->research_name,
				'id' => $required1_row->research_id
			);
		} else {
			$required1 = false;
		}
		if ($research->research_required2) {
			$required2_row = $Researches->fetchRow($Researches->select()->from($Researches->_name,array('research_id','research_name','research_cat'))->where('research_id = ?',$research->research_required2));
			$required2 = array(
				'field' => Chamista_Model_Formula::tech_type_name($required2_row->research_cat),
				'name' => $required2_row->research_name,
				'id' => $required2_row->research_id
			);
		} else {
			$required2 = false;
		}
		if ($research->research_required3) {
			$required3_row = $Researches->fetchRow($Researches->select()->from($Researches->_name,array('research_id','research_name','research_cat'))->where('research_id = ?',$research->research_required3));
			$required3 = array(
				'field' => Chamista_Model_Formula::tech_type_name($required3_row->research_cat),
				'name' => $required3_row->research_name,
				'id' => $required3_row->research_id
			);
		} else {
			$required3 = false;
		}
		if ($research->research_required4) {
			$required4_row = $Researches->fetchRow($Researches->select()->from($Researches->_name,array('research_id','research_name','research_cat'))->where('research_id = ?',$research->research_required4));
			$required4 = array(
				'field' => Chamista_Model_Formula::tech_type_name($required4_row->research_cat),
				'name' => $required4_row->research_name,
				'id' => $required4_row->research_id
			);
		} else {
			$required4 = false;
		}
		$this->view->researchDetail = array(
			'field' => Chamista_Model_Formula::tech_type_name($research->research_cat),
			'name' => $research->research_name,
			'desc' => $research->research_desc,
			'time' => $time,
			'next' => array(
				'id' => $next_row->research_id,
				'name' => $next_row->research_name
			),
			'required1' => $required1,
			'required2' => $required2,
			'required3' => $required3,
			'required4' => $required4
		);
		
		$this->view->researches = array(
			'seafaring' => $seafaring,
			'economy' => $economy,
			'science' => $science,
			'military' => $military,
		);
		$this->view->body_id = 'researchDetail';
		$this->view->css = 'ik_researchDetail_'.VERSION.'.css';
	}
	
	public function unitdescriptionAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$request = $this->getRequest();
		$Researches = new Chamista_Model_DbTable_Researches();
		$Units_Class = new Chamista_Model_DbTable_Units_Class();
		$Units_Attack = new Chamista_Model_DbTable_Units_Attack();
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Unit Description');
		
		if (!$request->getParam('unitId')) {
			$this->_helper->redirector('unitDescription','ikipedia','',array('unitId' => 5));
		}
		
		$Units = new Chamista_Model_DbTable_Units();
		
		$units_view = array();
		$rows = $Units->fetchAll($Units->select()->from($Units->_name,array('unit_id','unit_name'))->where('unit_type = ?',0)->order('unit_id ASC'));
		foreach ($rows as $row) {
			$units_view[] = array(
				'id' => $row->unit_id,
				'name' => $row->unit_name
			);
		}
		
		$unit_id = intval($request->getParam('unitId'));
		$row = $Units->fetchRow($Units->select()->where('unit_id = ?',$unit_id)->limit(1));
		if (!$row) {
			$this->_helper->flashMessenger->addMessage("The specified unit is not found!");
			$this->_helper->redirector('index','error');
		}
		
		$attack = array();
		$attack['primary'] = array(
			'name' => $Units_Attack->getAttackById($row->unit_primaryattack),
			'damage' => $row->unit_primarydamage,
			'accuracy' => $row->unit_primaryaccuracy,
			'munition' => $row->unit_primarymunition
		);
		if ($row->unit_secondaryattack != 0) {
			$attack['secondary'] = array(
				'name' => $Units_Attack->getAttackById($row->unit_secondaryattack),
				'damage' => $row->unit_secondarydamage,
				'accuracy' => $row->unit_secondaryaccuracy,
				'munition' => $row->unit_secondarymunition
			);
		}
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		$barracks = $Town->getBuildingLvl(Chamista_Model_Formula::BARRACKS);
		$requirements = array(
			'barracks' => array('required' => $row->unit_requirement, 'user' => $barracks),
		);
		if ($row->unit_requiretech != 0) {
			$requirements['tech'] = array('required' => $row->unit_requiretech, 'user' => $User->userResearched($row->unit_requiretech),
				'details' => array('id' => $row->unit_requiretech, 'name' => $Researches->getResearchById($row->unit_requiretech)));
		}
		
		$this->view->unitDescription = array(
			'name' => $row->unit_name,
			'wood' => $row->unit_costwood,
			'wine' => $row->unit_costwine,
			'crystal' => $row->unit_costcrystal,
			'sulfur' => $row->unit_costsulfur,
			'sid' => $row->unit_sid,
			'citizen' => $row->unit_citizen,
			'upkeep' => $row->unit_upkeep,
			'weight' => $row->unit_weight,
			'size' => $row->unit_size,
			'barracks' => $row->unit_requirement,
			'training' => Chamista_Model_Format::formatTime($row->unit_training),
			'class' => $Units_Class->getClassById($row->unit_primaryclass).', '.$Units_Class->getClassById($row->unit_secondaryclass),
			'hp' => $row->unit_hp,
			'armor' => $row->unit_armour,
			'speed' => $row->unit_speed,
			'size' => $row->unit_size,
			'attack' => $attack,
			'requirements' => $requirements,
			'desc' => $row->unit_desc
		);
		
		$this->view->units = $units_view;
		$this->view->body_id = 'unitdescription';
	}
	
	public function shipdescriptionAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$request = $this->getRequest();
		$Researches = new Chamista_Model_DbTable_Researches();
		$Units_Class = new Chamista_Model_DbTable_Units_Class();
		$Units_Attack = new Chamista_Model_DbTable_Units_Attack();
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Unit Description');
		
		if (!$request->getParam('shipId')) {
			$this->_helper->redirector('shipDescription','ikipedia','',array('shipId' => 15));
		}
		
		$Units = new Chamista_Model_DbTable_Units();
		
		$units_view = array();
		$rows = $Units->fetchAll($Units->select()->from($Units->_name,array('unit_id','unit_name'))->where('unit_type = ?',1)->order('unit_id ASC'));
		foreach ($rows as $row) {
			$units_view[] = array(
				'id' => $row->unit_id,
				'name' => $row->unit_name
			);
		}
		
		$unit_id = intval($request->getParam('shipId'));
		$row = $Units->fetchRow($Units->select()->where('unit_id = ?',$unit_id)->limit(1));
		if (!$row) {
			$this->_helper->flashMessenger->addMessage("The specified unit is not found!");
			$this->_helper->redirector('index','error');
		}
		
		$attack = array();
		$attack['primary'] = array(
			'name' => $Units_Attack->getAttackById($row->unit_primaryattack),
			'damage' => $row->unit_primarydamage,
			'accuracy' => $row->unit_primaryaccuracy,
			'munition' => $row->unit_primarymunition
		);
		if ($row->unit_secondaryattack != 0) {
			$attack['secondary'] = array(
				'name' => $Units_Attack->getAttackById($row->unit_secondaryattack),
				'damage' => $row->unit_secondarydamage,
				'accuracy' => $row->unit_secondaryaccuracy,
				'munition' => $row->unit_secondarymunition
			);
		}
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		$barracks = $Town->getBuildingLvl(Chamista_Model_Formula::BARRACKS);
		$requirements = array(
			'barracks' => array('required' => $row->unit_requirement, 'user' => $barracks),
		);
		if ($row->unit_requiretech != 0) {
			$requirements['tech'] = array('required' => $row->unit_requiretech, 'user' => $User->userResearched($row->unit_requiretech),
				'details' => array('id' => $row->unit_requiretech, 'name' => $Researches->getResearchById($row->unit_requiretech)));
		}
		
		$this->view->unitDescription = array(
			'name' => $row->unit_name,
			'wood' => $row->unit_costwood,
			'wine' => $row->unit_costwine,
			'crystal' => $row->unit_costcrystal,
			'sulfur' => $row->unit_costsulfur,
			'sid' => $row->unit_sid,
			'citizen' => $row->unit_citizen,
			'upkeep' => $row->unit_upkeep,
			'weight' => $row->unit_weight,
			'size' => $row->unit_size,
			'barracks' => $row->unit_requirement,
			'training' => Chamista_Model_Format::formatTime($row->unit_training),
			'class' => $Units_Class->getClassById($row->unit_primaryclass).', '.$Units_Class->getClassById($row->unit_secondaryclass),
			'hp' => $row->unit_hp,
			'armor' => $row->unit_armour,
			'speed' => $row->unit_speed,
			'size' => $row->unit_size,
			'attack' => $attack,
			'requirements' => $requirements,
			'desc' => $row->unit_desc
		);
		
		$this->view->ships = $units_view;
		$this->view->body_id = 'shipdescription';
		$this->view->css = 'ik_shipdescription_'.VERSION.'.css';
	}
}