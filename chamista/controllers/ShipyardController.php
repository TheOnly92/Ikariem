<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Shipyard controller file.
 * Handle all actions redirected to the shipyard controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class ShipyardController extends Rewaz_Controller_Action_Buildings {
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
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::SHIPYARD) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Units = new Chamista_Model_DbTable_Units();
		$now = time();
		
		$town_id = $request->getParam('id');
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Shipyard');
		
		$lvl = $Town->getPositionLvl($position);
		
		if ($Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::SHIPYARD)
				->where('queue_pos = ?',$position));
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::SHIPYARD),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $lvl,
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		// Retrieve units being trained
		$Units_Queue = new Chamista_Model_DbTable_Units_Queue();
		$rows = $Units_Queue->fetchAll($Units_Queue->select()->where('queue_town = ?', $town_id)->where('queue_type = ?',1)->order('queue_created'));
		$training = array();
		$training_current = array();
		$current = true;
		foreach ($rows as $row) {
			$trainingtime = 0;
			if ($current) {
				$percentage = ($now - $row->queue_created) / ($row->queue_finish - $row->queue_created);
				$percentage = round($percentage * 100);
				$units = array();
				$queue_data = unserialize($row->queue_data);
				foreach ($queue_data as $i => $v) {
					$unit_row = $Units->fetchRow($Units->select()->where('unit_id = ?',$i));
					$units[] = array(
						'id' => Chamista_Model_Format::unit_id(Chamista_Model_Format::unit_name($i)),
						'value' => $v,
						'name' => Chamista_Model_Format::unit_name($i),
						'sid' => $unit_row->unit_sid,
					);
					$trainingtime += Chamista_Model_Formula::getUnitTraining($i,$lvl) * $v;
				}
				$countDownRaw = round($trainingtime * (1 - $percentage / 100));
				$countDown = Chamista_Model_Format::formatTime($countDownRaw);
				$training_current = array(
					'startdate' => $row->queue_created,
					'enddate' => $row->queue_finish,
					'currentdate' => $now,
					'percentage' => $percentage,
					'countdown' => $countDown,
					'units' => $units
				);
				$current = false;
			} else {
				$units = array();
				$queue_data = unserialize($row->queue_data);
				foreach ($queue_data as $i => $v) {
					$unit_row = $Units->fetchRow($Units->select()->where('unit_id = ?',$i));
					$units[] = array(
						'id' => Chamista_Model_Format::unit_id(Chamista_Model_Format::unit_name($i)),
						'value' => $v,
						'name' => Chamista_Model_Format::unit_name($i),
						'sid' => $unit_row->unit_sid,
					);
					$trainingtime += Chamista_Model_Formula::getUnitTraining($i,$lvl) * $v;
				}
				$countDownRaw += $trainingtime;
				$training[] = array(
					'units' => $units,
					'done' => Chamista_Model_Format::formatTime($countDownRaw),
				);
			}
		}
		
		// Retrieve the units for training
		$units_view = array();
		$unit_rows = $Units->fetchAll($Units->select()->where('unit_type = ?',1)->order('unit_id ASC'));
		foreach ($unit_rows as $unit_row) {
			$cost = array();
			$action = true;
			$temp = 0;
			if ($unit_row->unit_requiretech != 0) {
				if ($User->userResearched($unit_row->unit_requiretech)) {
					$maxValue = floor($Town->getTownResource(0) / $unit_row->unit_costwood);
					$temp = $maxValue;
					$cost['wood'] = $unit_row->unit_costwood;
					if ($unit_row->unit_costwine > 0) {
						$temp = floor($Town->getTownResource(1) / $unit_row->unit_costwine);
						$cost['wine'] = $unit_row->unit_costwine;
					}
					if ($unit_row->unit_costcrystal > 0) {
						$temp = floor($Town->getTownResource(3) / $unit_row->unit_costcrystal);
						$cost['crystal'] = $unit_row->unit_costcrystal;
					}
					if ($unit_row->unit_costsulfur > 0) {
						$temp = floor($Town->getTownResource(4) / $unit_row->unit_costsulfur);
						$cost['sulfur'] = $unit_row->unit_costsulfur;
					}
					if ($temp < $maxValue) $maxValue = $temp;
					if (floor($Town->getFreeCitizens() / $unit_row->unit_citizen) < $maxValue) $maxValue = floor($Town->getFreeCitizens() / $unit_row->unit_citizen);
					if ($maxValue < 1) {
						$action = 'Insufficient resources';
					}
					if ($maxValue > $Town->getFreeCitizens()) {
						$action = 'Insufficient resources';
					}
					$available = $Town->getAvailableArmy($unit_row->unit_id);
					$units_view[] = array(
						'id' => $unit_row->unit_id,
						'unit_id' => Chamista_Model_Format::unit_id($unit_row->unit_name),
						'name' => $unit_row->unit_name,
						'available' => $available,
						'desc' => $unit_row->unit_desc,
						'maxValue' => $maxValue,
						'citizen' => $unit_row->unit_citizen,
						'upkeep' => $unit_row->unit_upkeep,
						'time' => Chamista_Model_Format::formatTime(Chamista_Model_Formula::getUnitTraining($unit_row->unit_id,$lvl)),
						'cost' => $cost,
						'action' => $action,
						'sid' => $unit_row->unit_sid,
						'timeRaw' => Chamista_Model_Formula::getUnitTraining($unit_row->unit_id,$lvl)
					);
				}
			} else {
				$maxValue = floor($Town->getTownResource(0) / $unit_row->unit_costwood);
				$temp = $maxValue;
				$cost['wood'] = $unit_row->unit_costwood;
				if ($unit_row->unit_costwine > 0) {
					$temp = floor($Town->getTownResource(1) / $unit_row->unit_costwine);
					$cost['wine'] = $unit_row->unit_costwine;
				}
				if ($unit_row->unit_costcrystal > 0) {
					$temp = floor($Town->getTownResource(3) / $unit_row->unit_costcrystal);
					$cost['crystal'] = $unit_row->unit_costcrystal;
				}
				if ($unit_row->unit_costsulfur > 0) {
					$temp = floor($Town->getTownResource(4) / $unit_row->unit_costsulfur);
					$cost['sulfur'] = $unit_row->unit_costsulfur;
				}
				if ($temp < $maxValue) $maxValue = $temp;
				if (floor($Town->getFreeCitizens() / $unit_row->unit_citizen) < $maxValue) $maxValue = floor($Town->getFreeCitizens() / $unit_row->unit_citizen);
				if ($maxValue < 1) {
					$action = 'Insufficient resources';
				}
				if ($Town->getFreeCitizens() == 0) {
					$action = 'Insufficient resources';
				}
				if ($maxValue > $Town->getFreeCitizens()) {
					$maxValue = $Town->getFreeCitizens();
				}
				$available = $Town->getAvailableArmy($unit_row->unit_id);
				$units_view[] = array(
					'id' => $unit_row->unit_id,
					'unit_id' => Chamista_Model_Format::unit_id($unit_row->unit_name),
					'name' => $unit_row->unit_name,
					'available' => $available,
					'desc' => $unit_row->unit_desc,
					'maxValue' => $maxValue,
					'citizen' => $unit_row->unit_citizen,
					'upkeep' => $unit_row->unit_upkeep,
					'time' => Chamista_Model_Format::formatTime($unit_row->unit_training),
					'cost' => $cost,
					'action' => $action,
					'sid' => $unit_row->unit_sid,
					'timeRaw' => Chamista_Model_Formula::getUnitTraining($unit_row->unit_id,$lvl)
				);
			}
		}
		
		$this->view->availableResources = array(
			'citizens' => $Town->getFreeCitizens(),
			'wood' => $Town->getTownResource(0),
			'wine' => $Town->getTownResource(1),
			'marble' => $Town->getTownResource(2),
			'crystal' => $Town->getTownResource(3),
			'sulfur' => $Town->getTownResource(4)
		);
		$this->view->training_current = $training_current;
		$this->view->training = $training;
		$this->view->units = $units_view;
		$this->view->position = $position;
		$this->view->build_lvl = $lvl;
		$this->view->town_id = $Town->town_id;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::SHIPYARD,$this->view->build_lvl + 1,$User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::SHIPYARD,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::SHIPYARD);
		$this->view->body_id = 'shipyard';
		$this->view->css = 'ik_shipyard_'.VERSION.'.css';
	}
	
	public function buildshipsAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Units = new Chamista_Model_DbTable_Units();
		$Units_Queue = new Chamista_Model_DbTable_Units_Queue();
		$now = time();
		
		$town_id = $request->getParam('id');
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		$lvl = $Town->getPositionLvl($position);
		
		if ($Town->isBeingExpanded($position)) {
			$this->_helper->flashMessenger->addMessage("A building is already being constructed or expanded!");
			$this->_helper->redirector('index','error');
		}
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'*' => 'Int'
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$row = $Units_Queue->fetchRow($Units_Queue->select()->from($Units_Queue->_name,'MAX(queue_finish) AS finish')->where('queue_town = ?',$town_id));
				if ($row->finish > 0) {
					$now = $row->finish;
				}
				$queue_data = array();
				foreach ($request->getPost() as $unit_id => $value) {
					//if (!is_numeric($value) || $value == 0) continue;
					$value = $zfi->{$unit_id};
					if ($value == 0) continue;
					$unit_id = intval(substr($unit_id,1));
					$unit_row = $Units->fetchRow($Units->select()->where('unit_id = ?',$unit_id)->limit(1));
					if ($unit_row->unit_requiretech != 0) {
						if (!$User->userResearched($unit_row->unit_requiretech)) {
							continue;
						}
					}
					if ($lvl < $unit_row->unit_requirement) {
						continue;
					}
					if ($Town->town_resource0 < $unit_row->unit_costwood * $value ||
						$Town->town_resource1 < $unit_row->unit_costwine * $value ||
						$Town->town_resource2 < $unit_row->unit_costcrystal * $value ||
						$Town->town_resource3 < $unit_row->unit_costsulfur * $value ||
						$Town->getFreeCitizens() < $value * $unit_row->unit_citizen) {
						continue;
					}
					
					$training_time += $unit_row->unit_training * $value;
					$queue_data[$unit_id] = $value;
					
					$Town->town_resource0 -= $unit_row->unit_costwood * $value;
					$Town->town_resource1 -= $unit_row->unit_costwine * $value;
					$Town->town_resource2 -= $unit_row->unit_costcrystal * $value;
					$Town->town_resource3 -= $unit_row->unit_costsulfur * $value;
					$Town->town_population -= $unit_row->unit_citizen * $value;
					//$now += $training_time;
				}
				$data = array(
					'queue_town' => $town_id,
					'queue_finish' => $now + $training_time,
					'queue_created' => $now,
					'queue_data' => serialize($queue_data),
					'queue_type' => 1
				);
				$Units_Queue->insert($data);
			}
		}
		
		$this->_helper->redirector('index','shipyard','',array('id' => $town_id, 'position' => $position));
	}
}