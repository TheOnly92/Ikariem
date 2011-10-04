<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Barracks controller file.
 * Handle all actions redirected to the barracks controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */
class BarracksController extends Rewaz_Controller_Action_Buildings {
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
		$Trailer->addStep($this->Town->town_name,'/city?id='.$this->Town->town_id, 'Back to the town');
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Barracks');
		
		$position = intval($request->getParam('position'));
		if ($this->Town->getGroundBuilding($position) != Chamista_Model_Formula::BARRACKS) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
		$Units = new Chamista_Model_DbTable_Units();
		$request = $this->getRequest();
		$now = time();
		
		$position = intval($request->getParam('position'));
		
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->getCurrBuildingQueue($this->Town->town_id, Chamista_Model_Formula::BARRACKS, $position);
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::BARRACKS),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $lvl,
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		// Retrieve the units being trained
		$Units_Queue = new Chamista_Model_DbTable_Units_Queue();
		$rows = $Units_Queue->fetchUnitQueue($this->Town->town_id, 0);
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
					$unit_row = $Units->getUnitById($i);
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
					$unit_row = $Units->getUnitById($i);
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
			$this->view->upgrade_disabled = true;
		}
		
		// Retrieve the units for training
		$units_view = array();
		$unit_rows = $Units->getUnits(0);
		foreach ($unit_rows as $unit_row) {
			$cost = array();
			$action = true;
			$temp = 0;
			if ($unit_row->unit_requiretech != 0) {
				if (!$this->User->userResearched($unit_row->unit_requiretech)) {
					continue;
				}
			}
			$maxValue = floor($this->Town->getTownResource(0) / $unit_row->unit_costwood);
			$temp = $maxValue;
			$cost['wood'] = $unit_row->unit_costwood;
			if ($unit_row->unit_costwine > 0) {
				$temp = floor($this->Town->getTownResource(1) / $unit_row->unit_costwine);
				$cost['wine'] = $unit_row->unit_costwine;
			}
			if ($unit_row->unit_costcrystal > 0) {
				$temp = floor($this->Town->getTownResource(3) / $unit_row->unit_costcrystal);
				$cost['crystal'] = $unit_row->unit_costcrystal;
			}
			if ($unit_row->unit_costsulfur > 0) {
				$temp = floor($this->Town->getTownResource(4) / $unit_row->unit_costsulfur);
				$cost['sulfur'] = $unit_row->unit_costsulfur;
			}
			if ($temp < $maxValue) $maxValue = $temp;
			if (floor($this->Town->getFreeCitizens() / $unit_row->unit_citizen) < $maxValue) $maxValue = floor($this->Town->getFreeCitizens() / $unit_row->unit_citizen);
			if ($maxValue < 1) {
				$action = 'Insufficient resources';
			}
			if ($this->Town->getFreeCitizens() == 0) {
				$action = 'Insufficient resources';
			}
			if ($lvl < $unit_row->unit_requirement) {
				$action = 'Building level too low!';
			}
			if ($maxValue > $this->Town->getFreeCitizens()) {
				$maxValue = $this->Town->getFreeCitizens();
			}
			$available = $this->Town->getAvailableArmy($unit_row->unit_id);
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
		
		$availableResources = array(
			'citizens' => $this->Town->getFreeCitizens(),
			'wood' => $this->Town->getTownResource(0),
			'wine' => $this->Town->getTownResource(1),
			'marble' => $this->Town->getTownResource(2),
			'crystal' => $this->Town->getTownResource(3),
			'sulfur' => $this->Town->getTownResource(4)
		);
		
		$barracks = array(
			'availableResources' => $availableResources,
			'trainingCurrent' => $training_current,
			'training' => $training,
			'units' => $units_view,
		);
		
		$this->view->barracks = $barracks;
		$this->view->position = $position;
		$this->view->build_lvl = $lvl;
		$this->view->town_id = $this->Town->town_id;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::BARRACKS,$this->view->build_lvl + 1,$this->User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::BARRACKS,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::BARRACKS);
		$this->view->body_id = 'barracks';
	}
	
	public function buildunitsAction() {
		$request = $this->getRequest();
		$Units = new Chamista_Model_DbTable_Units();
		$Units_Queue = new Chamista_Model_DbTable_Units_Queue();
		$now = time();
		
		$position = intval($request->getParam('position'));
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($this->Town->isBeingExpanded($position)) {
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
				$last = $Units_Queue->getLastFinishUnit($this->Town->town_id);
				if ($last > 0) {
					$now = $last;
				}
				$queue_data = array();
				foreach ($request->getPost() as $unit_id => $value) {
					//if (!is_numeric($value) || $value == 0) continue;
					$value = $zfi->{$unit_id};
					if ($value == 0) continue;
					$unit_id = intval(substr($unit_id,1));
					$unit_row = $Units->getUnitById($unit_id);
					if ($unit_row->unit_requiretech != 0) {
						if (!$this->User->userResearched($unit_row->unit_requiretech)) {
							continue;
						}
					}
					if ($lvl < $unit_row->unit_requirement) {
						$this->_helper->flashMessenger->addMessage("You cannot train this unit yet!");
						$this->_helper->redirector('index','error');
					}
					if ($this->Town->getTownResource(0) < $unit_row->unit_costwood * $value ||
						$this->Town->getTownResource(1) < $unit_row->unit_costwine * $value ||
						$this->Town->getTownResource(2) < $unit_row->unit_costcrystal * $value ||
						$this->Town->getTownResource(4) < $unit_row->unit_costsulfur * $value ||
						$this->Town->getFreeCitizens() < $value) {
						$this->_helper->flashMessenger->addMessage("You don't have enough resources!");
						$this->_helper->redirector('index','error');
					}
					
					$training_time += $unit_row->unit_training * $value;
					$queue_data[$unit_id] = $value;
					
					$this->Town->town_resource0 -= $unit_row->unit_costwood * $value;
					$this->Town->town_resource1 -= $unit_row->unit_costwine * $value;
					$this->Town->town_resource2 -= $unit_row->unit_costcrystal * $value;
					$this->Town->town_resource4 -= $unit_row->unit_costsulfur * $value;
					$this->Town->town_population -= $unit_row->unit_citizen * $value;
				}
				$Units_Queue->insertNewQueue($this->Town->town_id, $now + $training_time, $now, serialize($queue_data), 0);
			}
		}
		
		$this->_helper->redirector('index','barracks','',array('id' => $this->Town->town_id, 'position' => $position));
	}
	
	public function armygarrisoneditAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$Units = new Chamista_Model_DbTable_Units();
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		
		$position = intval($request->getParam('position'));
		
		$showConfirm = false;
		$fire = array();
		if ($request->isPost()) {
			$post = $request->getPost();
			if ($request->getParam('confirm') == 1) {
				foreach ($post as $i => $v) {
					if (substr($i,0,1) == 'u' && $v > 0) {
						$unitId = substr($i,1);
						$fire[$unitId] = intval($v);
					}
				}
				$showConfirm = true;
			}
			$fire['total'] = array_sum($fire);
		}
		
		$unitsView = array();
		$troops = $Units->getUnits(0);
		foreach ($troops as $troop) {
			if ($troop->unit_requiretech != 0) {
				if ($this->User->userResearched($troop->unit_requiretech)) {
					$value = $Towns_Units->getArmy($troop->unit_id,$this->Town->town_id);
				} else {
					$value = 0;
				}
			} else {
				$value = $Towns_Units->getArmy($troop->unit_id,$this->Town->town_id);
			}
			if ($value == 0) continue;
			$unitsView[] = array(
				'id' => $troop->unit_id,
				'unit_id' => Chamista_Model_Format::unit_id($troop->unit_name),
				'name' => $troop->unit_name,
				'available' => $value,
				'desc' => $troop->unit_desc,
				'maxValue' => $value,
				'upkeep' => $troop->unit_upkeep,
				'sid' => $troop->unit_sid,
			);
		}
		
		$this->view->armyGarrisonEdit = array(
			'units' => $unitsView,
			'showConfirm' => $showConfirm,
			'fire' => $fire,
		);
		$this->view->position = $position;
		$this->view->town_id = $this->Town->town_id;
		$this->view->body_id = 'armyGarrisonEdit';
	}
	
	public function fireunitsAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$Units = new Chamista_Model_DbTable_Units();
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		
		$position = intval($request->getParam('position'));
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'*' => 'Int'
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				foreach ($request->getPost() as $unit_id => $value) {
					$value = $zfi->{$unit_id};
					if ($value == 0) continue;
					$unit_id = intval($unit_id);
					$available = $Towns_Units->getArmy($unit_id,$this->Town->town_id);
					$value = $available - $value;
					if ($value < 0) $value = 0;
					$Towns_Units->setArmy($unit_id, $value, $this->Town->town_id);
				}
			}
		}
		
		$this->_helper->redirector('index','barracks','',array('id' => $this->Town->town_id, 'position' => $position));
	}
}