<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * City controller file.
 * Handle all actions redirected to the city controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */
class CityController extends Zend_Controller_Action {
	// This protected property contains the Town model of the currently selected town
	protected $Town;
	// This protected property contains the user model of the logged in user
	protected $User;
	
	/**
	 * Initializes the required variables and makes necessary checks
	 */
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		
		$Trailer = Zend_Registry::get('trailer');
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$this->User = new Chamista_Model_User($User_data->usr_id);
		
		if ($request->getActionName() != 'changeCurrentCity') {
			$town_id = $request->getParam('id');
			if (!$town_id) {
				$town_id = Zend_Registry::get('session')->current_city;
				$this->_helper->redirector($this->getRequest()->getActionName(),'city','',array('id' => $town_id));
			}
			$this->Town = new Chamista_Model_Town($town_id);
			if ($this->Town->town_uid != $User_data->usr_id) {
				unset(Zend_Registry::get('session')->current_city);
				$this->_helper->flashMessenger->addMessage("Access denied");
				$this->_helper->redirector('index','error');
			}
			
			$pos = $this->Town->getIslandXY();
			$Trailer->addStep($this->Town->getIslandName().' ['.$pos['x'].':'.$pos['y'].']','/island/index/'.$this->Town->town_island, 'Back to the island');
		}
	}
	
	public function indexAction() {
		$request = $this->getRequest();
		$auth = Zend_Auth::getInstance();
		$this->User_data = $auth->getStorage()->read();
		$this->User = new Chamista_Model_User($this->User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();

		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($this->Town->town_name);
		
		$row = $Transports->fetchRow($Transports->select()->from($Transports->_name,'COUNT(*) AS total')->where('trans_uid = ?',$this->User->usr_id)->where('trans_origin = ?',$this->Town->town_id)->where('trans_mission = ?',0));
		if ($row->total > 0) {
			$this->view->loading = true;
		} else {
			$this->view->loading = false;
		}
		
		$this->view->town_id = $this->Town->town_id;
		$this->view->This_Town = $this->Town;
		$this->view->growth = $this->Town->getGrowth();
		$this->view->User = $this->User;
		$this->view->grounds = $this->Town->getGrounds();
		$this->view->body_id = 'city';
		$this->view->css = 'ik_city_'.VERSION.'.css';
	}
	
	public function changecurrentcityAction() {
		$request = $this->getRequest();
		$auth = Zend_Auth::getInstance();
		$this->User_data = $auth->getStorage()->read();
		$this->User = new Chamista_Model_User($this->User_data->usr_id);
		
		if ($request->isPost()) {
			$newTownId = (int) $request->getPost('cityId');
			if ($this->User->userOwnsCity($newTownId)) {
				$action = 'index';
				$controller = 'city';
				$params = array('id' => $newTownId);
				if ($request->getParam('oldAction')) {
					$action = $request->getParam('oldAction');
				}
				if ($request->getParam('position')) {
					$params['position'] = $request->getParam('position');
				}
				if ($request->getParam('oldController')) {
					$controller = $request->getParam('oldController');
					// If controller is a building, redirect to city instead
					switch ($controller) {
						case 'academy':
						case 'alchemist':
						case 'barracks':
						case 'branchOffice':
						case 'carpentering':
						case 'forester':
						case 'glassblowing':
						case 'palaceColony':
						case 'palace':
						case 'port':
						case 'safehouse':
						case 'shipyard':
						case 'stonemason':
						case 'tavern':
						case 'townHall':
						case 'wall':
						case 'winery':
						case 'warehouse':
							$controller = 'city';
							$action = 'index';
							unset($params['position']);
							break;
					}
				}
				if ($request->getParam('id')) {
					$params['id'] = $request->getParam('id');
				}
				if ($request->getParam('id') == Zend_Registry::get('session')->current_city) {
					$params['id'] = $newTownId;
				}
				Zend_Registry::get('session')->current_city = $newTownId;
				$this->_helper->redirector($action,$controller,'',$params);
			}
		}
		$this->_helper->flashMessenger->addMessage("Invalid city selected!");
		$this->_helper->redirector('index','error');
	}

	//===========================================================================
	public function buildinggroundAction() {
		$request = $this->getRequest();
		$Buildings_Data = new Chamista_Model_DbTable_Buildings_Data();
		
		$position = intval($request->getParam('position'));
		if ($this->Town->getGroundBuilding($position) != 0) {
			$this->_helper->flashMessenger->addMessage("The building ground is already taken!");
			$this->_helper->redirector('index','error');
		}
		
		if ($position == 13 && !$this->User->userResearched(49)) {
			$this->_helper->flashMessenger->addMessage("We haven`t thought about this yet!");
			$this->_helper->redirector('index','error');
		}
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($this->Town->town_name,'/city?id='.$this->Town->town_id, 'Back to the town');
		$Trailer->addStep('Free building ground');
		
		$buildings = array();
		
		$building_type = array(0);
		if ($position < 3) $building_type = array(1);
		if ($position == 14) $building_type = array(2);
		switch ($this->Town->getIslandResource()) {
			case 1:
				$building_type[] = 3;
				break;
			case 2:
				$building_type[] = 4;
				break;
			case 3:
				$building_type[] = 5;
				break;
			case 4:
				$building_type[] = 6;
				break;
		}
		
		$rows = $Buildings_Data->fetchAll($Buildings_Data->select()->where('building_type IN (?)',$building_type));
		foreach ($rows as $row) {
			if ($row->building_id != Chamista_Model_Formula::WAREHOUSE) {
				// Check if built
				if ($this->Town->isBuildingBuilt($row->building_id)) continue;
			}
			if ($row->building_tech != 0) {
				// If there's a technology requirement
				if (!$this->User->userResearched($row->building_tech)) continue;
			}
			
			if ($row->building_id == Chamista_Model_Formula::PALACE && !$this->Town->isCapital()) {
				// If town is not capital no palace
				continue;
			}
			
			if ($row->building_id == Chamista_Model_Formula::GOVNR_RESIDENCE && $this->Town->isCapital()) {
				// If town is capital then no governor residence
				continue;
			}
			
			$cost = Chamista_Model_Formula::getBuildCost($row->building_id,1,$this->User);
			$time = Chamista_Model_Formula::getBuildTime($row->building_id,1,true);
			$buildings[] = array(
				'id' => $row->building_id,
				'img' => '/img/buildings/y100/'.$row->building_css.'.gif',
				'name' => $row->building_name,
				'desc' => $row->building_desc,
				'cost' => $cost,
				'time' => Chamista_Model_Format::formatTimeFromArray($time),
				'class' => $row->building_css,
				'canBuild' => $this->Town->canBuildBuilding($row->building_id,1)
			);
		}


        $this->view->town_id = $this->Town->town_id;
		$this->view->is_expanding = $this->Town->isExpanding();
		$this->view->buildings = $buildings;
		$this->view->position = $position;
		$this->view->body_id = 'buildingGround';
		$this->view->css = 'ik_buildingGround_'.VERSION.'.css';
	}
	
	public function buildAction() {
		$request = $this->getRequest();

		$position = intval($request->getParam('position'));
		if ($this->Town->getGroundBuilding($position) != 0) {
			$this->_helper->flashMessenger->addMessage("The building ground is already taken!");
			$this->_helper->redirector('index','error');
		}
		
		$building = intval($request->getParam('building'));
		if (!$building) {
			$this->_helper->flashMessenger->addMessage("No building has been specified!");
			$this->_helper->redirector('index','error');
		}
		
		if (($building == Chamista_Model_Formula::TRADING_PORT || $building == Chamista_Model_Formula::SHIPYARD) && $position > 2) {
			$this->_helper->flashMessenger->addMessage("You can't build this building here!");
			$this->_helper->redirector('index','error');
		}
		
		$Buildings_Data = new Chamista_Model_DbTable_Buildings_Data();
		if ($Buildings_Data->getField($building, 'type') == 3) {
			if ($this->Town->getIslandResource() != 1) {
				$this->_helper->flashMessenger->addMessage("You can't build this building here!");
				$this->_helper->redirector('index','error');
			}
		}
		if ($Buildings_Data->getField($building, 'type') == 4) {
			if ($this->Town->getIslandResource() != 2) {
				$this->_helper->flashMessenger->addMessage("You can't build this building here!");
				$this->_helper->redirector('index','error');
			}
		}
		if ($Buildings_Data->getField($building, 'type') == 5) {
			if ($this->Town->getIslandResource() != 3) {
				$this->_helper->flashMessenger->addMessage("You can't build this building here!");
				$this->_helper->redirector('index','error');
			}
		}
		if ($Buildings_Data->getField($building, 'type') == 6) {
			if ($this->Town->getIslandResource() != 4) {
				$this->_helper->flashMessenger->addMessage("You can't build this building here!");
				$this->_helper->redirector('index','error');
			}
		}
		
		$result = $this->Town->canBuildBuilding($building,1);
		if ($result !== true) {
			switch ($result) {
				case -1:
					$this->_helper->flashMessenger->addMessage("You don't have enough resources to build this building!");
					$this->_helper->redirector('index','error');
					break;
				case -2:
					$this->_helper->flashMessenger->addMessage("This building has already been built!");
					$this->_helper->redirector('index','error');
					break;
				case -3:
					$this->_helper->flashMessenger->addMessage("A building is already being constructed or expanded!");
					$this->_helper->redirector('index','error');
					break;
			}
		}
		$cost = Chamista_Model_Formula::getBuildCost($building,1,$this->User);
		
		$this->Towns = new Chamista_Model_DbTable_Towns();
		$data = array();
		if (isset($cost['wood'])) {
			$data['town_resource0'] = $this->Town->town_resource0 - $cost['wood'];
		}
		if (isset($cost['marble'])) {
			$data['town_resource2'] = $this->Town->town_resource2 - $cost['marble'];
		}
		$where = $this->Towns->getAdapter()->quoteInto('town_id = ?', $this->Town->town_id);
		$this->Towns->update($data,$where);
		
		$time = Chamista_Model_Formula::getBuildTime($building,1);
		$now = time();
		
		$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
		$data = array(
			'queue_town' => $this->Town->town_id,
			'queue_type' => $building,
			'queue_pos' => $position,
			'queue_endtime' => $now + $time,
			'queue_lvl' => 1,
			'queue_created' => $now
		);
		$Buildings_Queue->insert($data);
		
		$Buildings = new Chamista_Model_DbTable_Buildings();
		$data = array(
			'build_town' => $this->Town->town_id,
			'build_type' => $building,
			'build_position' => $position,
			'build_lvl' => 0
		);
		$Buildings->insert($data);
		
		$this->_helper->redirector('index','city','',array('id' => $this->Town->town_id));
	}
	
	public function upgradebuildingAction() {
		$request = $this->getRequest();
		
		$position = intval($request->getParam('position'));
		$building = $this->Town->getGroundBuilding($position);
		if ($building == 0) {
			$this->_helper->flashMessenger->addMessage("No building to be upgraded here!");
			$this->_helper->redirector('index','error');
		}
		
		$lvl = $this->Town->getPositionLvl($position);
		$cost = Chamista_Model_Formula::getBuildCost($building,$lvl+1,$this->User);
		$result = $this->Town->canBuildBuilding($building,$lvl+1);
		if ($result !== true) {
			switch ($result) {
				case -1:
					$this->_helper->flashMessenger->addMessage("You don't have enough resources to upgrade this building!");
					$this->_helper->redirector('index','error');
					break;
				case -3:
					$this->_helper->flashMessenger->addMessage("A building is already being constructed or expanded!");
					$this->_helper->redirector('index','error');
					break;
			}
		}
		
		$this->Towns = new Chamista_Model_DbTable_Towns();
		$data = array();
		if (isset($cost['wood'])) {
			$data['town_resource0'] = $this->Town->town_resource0 - $cost['wood'];
		}
		if (isset($cost['marble'])) {
			$data['town_resource2'] = $this->Town->town_resource2 - $cost['marble'];
		}
		$where = $this->Towns->getAdapter()->quoteInto('town_id = ?', $this->Town->town_id);
		$this->Towns->update($data,$where);
		
		$time = Chamista_Model_Formula::getBuildTime($building,$lvl+1);
		$now = time();
		
		$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
		$data = array(
			'queue_town' => $this->Town->town_id,
			'queue_type' => $building,
			'queue_pos' => $position,
			'queue_endtime' => $now + $time,
			'queue_lvl' => $lvl+1,
			'queue_created' => $now
		);
		$Buildings_Queue->insert($data);
		
		$this->_helper->redirector('index','city','',array('id' => $this->Town->town_id));
	}
	
	public function buildingsdemolitionAction() {
		$request = $this->getRequest();
		
		$position = intval($request->getParam('position'));
		$building = $this->Town->getGroundBuilding($position);
		if ($building == 0) {
			$this->_helper->flashMessenger->addMessage("No building to be demolished here!");
			$this->_helper->redirector('index','error');
		}
		
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$this->Town->town_id)
				->where('queue_pos = ?',$position));
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
			$lvl++;
		}
		
		$cost = Chamista_Model_Formula::getBuildCost($building,$lvl,$this->User);
		foreach ($cost as $i => $v) {
			$cost[$i] *= Chamista_Model_Formula::demolitionReturn($lvl);
			$cost[$i] = floor($cost[$i]);
		}

		$this->view->building = array(
			'build_name' => Chamista_Model_Format::building_plaintext($building),
			'build_action' => Chamista_Model_Format::building_action($building),
			'build_lvl' => $lvl
		);
		$this->view->body_id = 'buildings_demolition';
		$this->view->cost = $cost;
		$this->view->town_id = $this->Town->town_id;
		$this->view->position = $position;
		$this->view->demolitionReturn = Chamista_Model_Formula::demolitionReturn($lvl) * 100;
	}
	
	public function downgradebuildingAction() {
		$request = $this->getRequest();
		$this->Towns = new Chamista_Model_DbTable_Towns();
		
		$position = intval($request->getParam('position'));
		$building = $this->Town->getGroundBuilding($position);
		if ($building == 0) {
			$this->_helper->flashMessenger->addMessage("No building to be demolished here!");
			$this->_helper->redirector('index','error');
		}
		
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$this->Town->town_id)
				->where('queue_pos = ?',$position));
			$lvl++;
			
			$cost = Chamista_Model_Formula::getBuildCost($building,$lvl,$this->User);
			foreach ($cost as $i => $v) {
				$cost[$i] *= Chamista_Model_Formula::demolitionReturn($lvl);
				$cost[$i] = floor($cost[$i]);
			}
			
			// Return the resources
			$data = array();
			if (isset($cost['wood'])) {
				$data['town_resource0'] = $this->Town->town_resource0 + $cost['wood'];
			}
			if (isset($cost['marble'])) {
				$data['town_resource2'] = $this->Town->town_resource2 + $cost['marble'];
			}
			$where = $this->Towns->getAdapter()->quoteInto('town_id = ?',$this->Town->town_id);
			$this->Towns->update($data,$where);
			
			$where = array(
				$Buildings_Queue->getAdapter()->quoteInto('queue_town = ?',$row->queue_town),
				$Buildings_Queue->getAdapter()->quoteInto('queue_type = ?',$row->queue_type),
				$Buildings_Queue->getAdapter()->quoteInto('queue_pos = ?',$row->queue_pos)
			);
			$Buildings_Queue->delete();
			
			$this->_helper->redirector('index','city','',array('id' => $this->Town->town_id));
		} else {
			$cost = Chamista_Model_Formula::getBuildCost($building,$lvl,$this->User);
			foreach ($cost as $i => $v) {
				$cost[$i] *= Chamista_Model_Formula::demolitionReturn($lvl);
				$cost[$i] = floor($cost[$i]);
			}
			
			$Buildings = new Chamista_Model_DbTable_Buildings();
			
			$lvl--;
			$where = array(
				$Buildings->getAdapter()->quoteInto('build_town = ?',$this->Town->town_id),
				$Buildings->getAdapter()->quoteInto('build_position = ?',$position),
				$Buildings->getAdapter()->quoteInto('build_type = ?',$building)
			);
			if ($lvl == 0) {
				// Totally demolish
				$Buildings->delete($where);
			} else {
				$data = array('build_lvl' => $lvl);
				$Buildings->update($data,$where);
			}
			
			// Return the resources
			$data = array();
			if (isset($cost['wood'])) {
				$data['town_resource0'] = $this->Town->town_resource0 + $cost['wood'];
			}
			if (isset($cost['marble'])) {
				$data['town_resource2'] = $this->Town->town_resource2 + $cost['marble'];
			}
			$where = $this->Towns->getAdapter()->quoteInto('town_id = ?',$this->Town->town_id);
			$this->Towns->update($data,$where);
			
			if ($lvl == 0) {
				// Redirect to town
				$this->_helper->redirector('index','city','',array('id' => $this->Town->town_id));
			} else {
				// Redirect to building
				$this->_helper->redirector('index',Chamista_Model_Format::building_action($building),'',array('id' => $this->Town->town_id,'position' => $position));
			}
		}
	}
	
	public function militaryAction() {
		$request = $this->getRequest();
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		$Units = new Chamista_Model_DbTable_Units();
		
		$type = $request->getParam('type');
		if ($type != 'army' && $type != 'fleet') {
			$this->_helper->flashMessenger->addMessage("Invalid link!");
			$this->_helper->redirector('index','error');
		}
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($this->Town->town_name,'/city/index/id/'.$this->Town->town_id, 'Back to the town');
		$Trailer->addStep('Military view');
		
		if ($type == 'army') {
			$troopsView = array();
			$troopsValue = array();
			$troops = $Units->fetchAll($Units->select()->from($Units->_name,array('unit_id','unit_name','unit_requiretech','unit_css'))->where('unit_type = ?',0));
			foreach ($troops as $troop) {
				if ($troop->unit_requiretech != 0) {
					if ($this->User->userResearched($troop->unit_requiretech)) {
						$value = $Towns_Units->getArmy($troop->unit_id,$this->Town->town_id);
					} else {
						$value = '-';
					}
				} else {
					$value = $Towns_Units->getArmy($troop->unit_id,$this->Town->town_id);
				}
				$troopsView[] = array(
					'name' => $troop->unit_name,
					'css' => $troop->unit_css
				);
				$troopsValue[] = $value;
			}
			$this->view->troops = $troopsView;
			$this->view->troopsValue = $troopsValue;
		} else {
			$troopsView = array();
			$troopsValue = array();
			$troops = $Units->fetchAll($Units->select()->from($Units->_name,array('unit_id','unit_name','unit_requiretech','unit_css'))->where('unit_type = ?',1));
			foreach ($troops as $troop) {
				if ($troop->unit_requiretech != 0) {
					if ($this->User->userResearched($troop->unit_requiretech)) {
						$value = $Towns_Units->getArmy($troop->unit_id,$this->Town->town_id);
					} else {
						$value = '-';
					}
				} else {
					$value = $Towns_Units->getArmy($troop->unit_id,$this->Town->town_id);
				}
				$troopsView[] = array(
					'name' => $troop->unit_name,
					'css' => $troop->unit_css
				);
				$troopsValue[] = $value;
			}
			$this->view->troops = $troopsView;
			$this->view->troopsValue = $troopsValue;
		}
		
		$this->view->position = $this->Town->getBuildingPos(Chamista_Model_Formula::BARRACKS);
		$this->view->town_id = $this->Town->town_id;
		$this->view->type = $type;
		$this->view->body_id = 'cityMilitary-'.$type;
	}
}