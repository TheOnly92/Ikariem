<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Research advisor controller file.
 * Handle all actions redirected to the researchAdvisor controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class ResearchadvisorController extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Research Advisor');
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Researches = new Chamista_Model_DbTable_Researches();
		
		// Retrieve available researches for the user
		$research_id = $User->researchNextField(Chamista_Model_Formula::SEAFARING);
		$tech = $Researches->fetchRow($Researches->select()->where('research_id = ?',$research_id)->limit(1));
		$seafaring = array(
			'id' => $research_id,
			'name' => $tech->research_name,
			'desc' => $tech->research_desc,
			'points' => $tech->research_points,
			'action' => ($User->usr_research >= $tech->research_points) ? true : 'Not enough research points',
		);
		// Check for tech requirements
		for ($i=1;$i<=4;$i++) {
			if ($tech->{'research_required'.$i} == 0) continue;
			if (!$User->userResearched($tech->{'research_required'.$i})) {
				$seafaring['action'] = 'At least one requirement has not been researched';
			}
		}
		$this->view->seafaring = $seafaring;
		
		// Economy
		$research_id = $User->researchNextField(Chamista_Model_Formula::ECONOMY);
		$tech = $Researches->fetchRow($Researches->select()->where('research_id = ?',$research_id)->limit(1));
		$economy = array(
			'id' => $research_id,
			'name' => $tech->research_name,
			'desc' => $tech->research_desc,
			'points' => $tech->research_points,
			'action' => ($User->usr_research >= $tech->research_points) ? true : 'Not enough research points',
		);
		// Check for tech requirements
		for ($i=1;$i<=4;$i++) {
			if ($tech->{'research_required'.$i} == 0) continue;
			if (!$User->userResearched($tech->{'research_required'.$i})) {
				$economy['action'] = 'At least one requirement has not been researched';
			}
		}
		$this->view->economy = $economy;
		
		// Science
		$research_id = $User->researchNextField(Chamista_Model_Formula::SCIENCE);
		$tech = $Researches->fetchRow($Researches->select()->where('research_id = ?',$research_id)->limit(1));
		$science = array(
			'id' => $research_id,
			'name' => $tech->research_name,
			'desc' => $tech->research_desc,
			'points' => $tech->research_points,
			'action' => ($User->usr_research >= $tech->research_points) ? true : 'Not enough research points',
		);
		// Check for tech requirements
		for ($i=1;$i<=4;$i++) {
			if ($tech->{'research_required'.$i} == 0) continue;
			if (!$User->userResearched($tech->{'research_required'.$i})) {
				$science['action'] = 'At least one requirement has not been researched';
			}
		}
		$this->view->science = $science;
		
		// Military
		$research_id = $User->researchNextField(Chamista_Model_Formula::MILITARY);
		$tech = $Researches->fetchRow($Researches->select()->where('research_id = ?',$research_id)->limit(1));
		$military = array(
			'id' => $research_id,
			'name' => $tech->research_name,
			'desc' => $tech->research_desc,
			'points' => $tech->research_points,
			'action' => ($User->usr_research >= $tech->research_points) ? true : 'Not enough research points',
		);
		// Check for tech requirements
		for ($i=1;$i<=4;$i++) {
			if ($tech->{'research_required'.$i} == 0) continue;
			if (!$User->userResearched($tech->{'research_required'.$i})) {
				$military['action'] = 'At least one requirement has not been researched';
			}
		}
		$this->view->military = $military;
		
		$this->view->scientists = $User->getGlobalScientists();
		$this->view->researchPoints = floor($User->usr_research);
		$this->view->researchProduction = floor($User->getGlobalResearch());
		
		$this->view->css = 'ik_researchAdvisor_'.VERSION.'.css';
		$this->view->body_id = 'researchAdvisor';
	}
	
	public function doresearchAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Researches = new Chamista_Model_DbTable_Researches();
		$Users_Researches = new Chamista_Model_DbTable_Users_Researches();
		
		$type = $request->getParam('type');
		$type_id = Chamista_Model_Formula::techTypeFromId($type);
		if ($type_id != 0) {
			$research_id = $User->researchNextField($type_id);
			$tech = $Researches->fetchRow($Researches->select()->where('research_id = ?',$research_id)->limit(1));
			
			// Check for research requirements
			if ($User->usr_research < $tech->research_points) {
				$this->_helper->flashMessenger->addMessage("Not enough research points!");
				$this->_helper->redirector('index','error');
			}
			
			for ($i=1;$i<=4;$i++) {
				if ($tech->{'research_required'.$i} == 0) continue;
				if (!$User->userResearched($tech->{'research_required'.$i})) {
					$this->_helper->flashMessenger->addMessage("At least one requirement has not been researched!");
					$this->_helper->redirector('index','error');
				}
			}
			
			// Perform research!
			$data = array(
				'research_'.$research_id => 1
			);
			$where = $Users_Researches->getAdapter()->quoteInto('usr_id = ?', $User->usr_id);
			$Users_Researches->update($data,$where);
			
			// Deduct research points
			$User->usr_research -= $tech->research_points;
			
			// Do after research actions here
			switch ($research_id) {
				// Wealth
				case 10:
					$Town = new Chamista_Model_Town($User->usr_capital);
					$Town->town_resource1 = 130;
					$Town->town_resource2 = 130;
					$Town->town_resource3 = 130;
					$Town->town_resource4 = 130;
					break;
			}
		}
		
		$this->_helper->redirector('index');
	}
	
	public function researchoverviewAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Researches = new Chamista_Model_DbTable_Researches();
		$Users_Researches = new Chamista_Model_DbTable_Users_Researches();
		
		$seafaring = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::SEAFARING)->order('research_id ASC'));
		foreach ($rows as $row) {
			$seafaring[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
				'explored' => $User->userResearched($row->research_id)
			);
		}
		
		$economy = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::ECONOMY)->order('research_id ASC'));
		foreach ($rows as $row) {
			$economy[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
				'explored' => $User->userResearched($row->research_id)
			);
		}
		
		$science = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::SCIENCE)->order('research_id ASC'));
		foreach ($rows as $row) {
			$science[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
				'explored' => $User->userResearched($row->research_id)
			);
		}
		
		$military = array();
		$rows = $Researches->fetchAll($Researches->select()->from($Researches->_name,array('research_id','research_name'))->where('research_cat = ?',Chamista_Model_Formula::MILITARY)->order('research_id ASC'));
		foreach ($rows as $row) {
			$military[] = array(
				'id' => $row->research_id,
				'name' => $row->research_name,
				'explored' => $User->userResearched($row->research_id)
			);
		}
		
		$this->view->seafaring = $seafaring;
		$this->view->economy = $economy;
		$this->view->science = $science;
		$this->view->military = $military;
		
		$session = Zend_Registry::get('session');
		$Town = new Chamista_Model_Town($session->current_city);
		$this->view->academyPos = $Town->getBuildingPos(Chamista_Model_Formula::ACADEMY);
		$this->view->town_id = $session->current_city;
		
		$this->view->body_id = 'researchOverview';
		$this->view->css = 'ik_researchOverview_'.VERSION.'.css';
	}
}