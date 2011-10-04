<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Buildings parent class file.
 * Initialize everything for buildings.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Rewaz_Controller_Action_Buildings extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		
		$Trailer = Zend_Registry::get('trailer');
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = $request->getParam('cityId');
			if (!$town_id)
				$town_id = Zend_Registry::get('session')->current_city;
			else
				$request->setParam('id',$town_id);
		}
		$Town = new Chamista_Model_Town($town_id);
		
		$pos = $Town->getIslandXY();
		$Trailer->addStep($Town->getIslandName().' ['.$pos['x'].':'.$pos['y'].']','/island/index/'.$Town->town_island, 'Back to the island');
		
		$Buildings_Data = new Chamista_Model_DbTable_Buildings_Data();
		$row = $Buildings_Data->fetchRow($Buildings_Data->select()->from($Buildings_Data->_name,'building_desc')->where('building_css = ?',$request->getControllerName()));
		$this->view->building_desc = $row->building_desc;
		$this->setUpgradeInfo();
	}
	
	public function setUpgradeInfo() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		$lvl = $Town->getPositionLvl($position);
		
		$nextLvl = $lvl + 1;
		if ($Town->isBeingExpanded($position)) {
			$nextLvl++;
		}
		
		$building = $Town->getGroundBuilding($position);
		
		$this->view->buildingUpgrade = array(
			'currLvl' => $lvl,
			'nextLvl' => $nextLvl,
			'upgradeCost' => Chamista_Model_Formula::getBuildCost($building,$nextLvl,$User),
			'upgradeTime' => Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime($building,$nextLvl,true))
		);
	}
}