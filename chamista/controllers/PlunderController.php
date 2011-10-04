<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Plunder controller file.
 * Handle all actions redirected to the plunder controller.
 * Last updated: $LastChangedDate$
 * 
 * @author        TheOnly92
 * @copyright    (c) 2008-2010 Rewaz Labs.
 * @package        Project Chamista
 * @version        $LastChangedRevision$
 */

class PlunderController extends Zend_Controller_Action {
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
		
		$destinationCityId = intval($request->getParam('destinationCityId'));
		if ($destinationCityId == 0 && $request->getParam('barbarianVillage') == 1) {
			return $this->barbarianVillage();
		}
		$DestinationTown = new Chamista_Model_Town($destinationCityId);
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
	}
	
	public function barbarianVillage() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Units = new Chamista_Model_DbTable_Units();
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		if (!$Town->isCapital()) {
			$this->_helper->flashMessenger->addMessage("Access denied!");
			$this->_helper->redirector('index','error');
		}
		
		$sendUnits = array();
		$troops = $Units->fetchAll($Units->select()->where('unit_type = ?',0));
		foreach ($troops as $troop) {
			if ($troop->unit_requiretech != 0) {
				if ($User->userResearched($troop->unit_requiretech)) {
					$value = $Towns_Units->getArmy($troop->unit_id,$Town->town_id);
				} else {
					$value = 0;
				}
			} else {
				$value = $Towns_Units->getArmy($troop->unit_id,$Town->town_id);
			}
			if ($value == 0) continue;
			$sendUnits[] = array(
				'id' => $troop->unit_id,
				'unit_id' => Chamista_Model_Format::unit_id($troop->unit_name),
				'name' => $troop->unit_name,
				'available' => $value,
				'desc' => $troop->unit_desc,
				'maxValue' => $value,
				'weight' => $troop->unit_weight,
				'upkeep' => $troop->unit_upkeep,
				'sid' => $troop->unit_sid,
			);
		}
		
		$this->view->plunder = array(
			'islandId' => $Town->town_island,
			'barbarianVillage' => 1,
			'townId' => $Town->town_id,
			'destinationCityId' => 0,
			'townName' => $Town->town_name,
			'units' => $sendUnits,
			'transporters' => $User->getFreeShips(),
		);
		$this->view->body_id = 'plunder';
	}
}