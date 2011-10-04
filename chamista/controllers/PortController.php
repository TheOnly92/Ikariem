<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Port controller file.
 * Handle all actions redirected to the port controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class PortController extends Rewaz_Controller_Action_Buildings {
	public function preDispatch() {
		parent::preDispatch();
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$now = time();
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::TRADING_PORT) {
			throw new Exception('Invalid URL!');
		}
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($Town->town_name,'/city/index/id/'.$Town->town_id, 'Back to the town');
		$Trailer->addStep('Trading port');
		
		if ($Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::TRADING_PORT)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::TRADING_PORT),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $Town->getPositionLvl($position),
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$fleetsLoaded = array();
		// Get own cargo ships
		$loadingMissions = array(0, 3);
		$rows = $Transports->fetchAll(
			$Transports->select()
				->where('trans_uid = ?',$User->usr_id)
				->where('trans_origin = ?',$Town->town_id)
				->where('trans_mission IN (?)',$loadingMissions)
		);
		$fleetsLoaded['own'] = array();
		foreach ($rows as $row) {
			$Destination = new Chamista_Model_Town($row->trans_destination);
			$progress = array(
				'startdate' => $row->trans_startdate,
				'enddate' => $row->trans_enddate,
				'currentdate' => $now,
				'progress' => floor(($row->trans_enddate - $now) / ($row->trans_enddate - $row->trans_startdate) * 100),
				'countDown' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
			);
			$stuffRaw = unserialize($row->trans_cargo);
			$stuff = array();
			foreach ($stuffRaw as $i => $v) {
				if ($v == 0) continue;
				if ($i == 'resource0' && $row->trans_mission == 3) $v += 1250;
				$stuff[] = array(
					'css' => Chamista_Model_Format::tradegood_css(intval(substr($i,-1))),
					'quantity' => $v
				);
			}
			$fleetsLoaded['own'][] = array(
				'destination' => $Destination->town_name,
				'quantity' => $row->trans_ships,
				'stuff' => $stuff,
				'progress' => $progress,
				'mission' => $row->trans_mission,
			);
		}
		
		$this->view->port = array(
			'colonies' => $User->getUserCities(),
		);
		$this->view->fleetsLoaded = $fleetsLoaded;
		$this->view->ships = $User->usr_ships;
		$this->view->ships_cost = Chamista_Model_Formula::getShipsCost($User->usr_ships + 1);
		$this->view->ships_buyable = ($User->usr_gold > $this->view->ships_cost);
		$this->view->loading_speed = Chamista_Model_Formula::getLoadingSpeed($Town->getPositionLvl($position));
		$this->view->position = $position;
		$this->view->build_lvl = $Town->getPositionLvl($position);
		$this->view->town_id = $Town->town_id;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::TRADING_PORT,$this->view->build_lvl + 1,$User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::TRADING_PORT,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::TRADING_PORT);
		$this->view->css = 'ik_port_'.VERSION.'.css';
		$this->view->body_id = 'port';
	}
	
	public function increasetransporterAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::TRADING_PORT) {
			throw new Exception('Invalid URL!');
		}
		
		$usr_ships = $User->usr_ships;
		if (Chamista_Model_Formula::getShipsCost($usr_ships + 1) < $User->usr_gold) {
			// Successfully bought ship
			$User->usr_gold -= Chamista_Model_Formula::getShipsCost($usr_ships + 1);
			$User->usr_ships += 1;
			$this->_helper->redirector('index','port','',array('id' => $town_id,'position' => $position));
		} else {
			$this->_helper->flashMessenger->addMessage("You don't have enough gold!");
			$this->_helper->redirector('index','error');
		}
	}
}