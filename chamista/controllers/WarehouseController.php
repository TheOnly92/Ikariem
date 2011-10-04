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

class WarehouseController extends Rewaz_Controller_Action_Buildings {
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
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::WAREHOUSE) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
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
		
		$position = intval($request->getParam('position'));
		$lvl = $Town->getPositionLvl($position);
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Warehouse');
		
		$this->view->warehouseSafe = $Town->getWarehouseSafe();
		$this->view->lootable = array(
			'wood' => ($Town->getTownResource(0) - $this->view->warehouseSafe > 0) ? $Town->getTownResource(0) - $this->view->warehouseSafe : 0,
			'wine' => ($Town->getTownResource(1) - $this->view->warehouseSafe > 0) ? $Town->getTownResource(1) - $this->view->warehouseSafe : 0,
			'marble' => ($Town->getTownResource(2) - $this->view->warehouseSafe > 0) ? $Town->getTownResource(2) - $this->view->warehouseSafe : 0,
			'crystal' => ($Town->getTownResource(3) - $this->view->warehouseSafe > 0) ? $Town->getTownResource(3) - $this->view->warehouseSafe : 0,
			'sulfur' => ($Town->getTownResource(4) - $this->view->warehouseSafe > 0) ? $Town->getTownResource(4) - $this->view->warehouseSafe : 0,
		);
		$this->view->resources = array(
			'wood' => $Town->getTownResource(0),
			'wine' => $Town->getTownResource(1),
			'marble' => $Town->getTownResource(2),
			'crystal' => $Town->getTownResource(3),
			'sulfur' => $Town->getTownResource(4)
		);
		$this->view->capacity = $Town->getWarehouseCap();
		
		if ($Town->isBeingExpanded($position)) {
			$now = time();
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::WAREHOUSE)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::WAREHOUSE),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $lvl,
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$this->view->build_lvl = $lvl;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::WAREHOUSE,$this->view->build_lvl + 1,$User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::WAREHOUSE,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::WAREHOUSE);
		$this->view->position = $position;
		$this->view->town_id = $town_id;
		$this->view->css = 'ik_warehouse_'.VERSION.'.css';
		$this->view->body_id = 'warehouse';
	}
}