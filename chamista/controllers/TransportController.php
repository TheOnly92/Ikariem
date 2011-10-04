<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Transport controller file.
 * Handle all actions redirected to the transport controller.
 * Last updated: $LastChangedDate$
 * 
 * @author        TheOnly92
 * @copyright    (c) 2008-2010 Rewaz Labs.
 * @package        Project Chamista
 * @version        $LastChangedRevision$
 */

class TransportController extends Zend_Controller_Action {
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
		$DestinationTown = new Chamista_Model_Town($destinationCityId);
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$Islands = new Chamista_Model_DbTable_Islands();
		$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $DestinationTown->town_island));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($row->island_name.' ['.$row->island_posx.':'.$row->island_posy.']','/island/index/'.$DestinationTown->town_island, 'Back to the island');
		$Trailer->addStep('Transport');
		
		$transport = array();
		$transport['destinationInfo'] = array(
			'id' => $DestinationTown->town_id,
			'name' => $DestinationTown->town_name
		);
		$transport['currCityInfo'] = array(
			'id' => $Town->town_id,
			'name' => $Town->town_name,
			'wood' => $Town->getTownResource(0),
			'wine' => $Town->getTownResource(1),
			'marble' => $Town->getTownResource(2),
			'crystal' => $Town->getTownResource(3),
			'sulfur' => $Town->getTownResource(4)
		);
		$transport['travelTime'] = Chamista_Model_Format::formatTime(Chamista_Model_Formula::travelTime(60,$Town->getIslandXY(),$DestinationTown->getIslandXY()));
		$transport['transporters'] = $User->getFreeShips();
		
		$this->view->transport = $transport;
		$this->view->css = 'ik_transport_'.VERSION.'.css';
		$this->view->body_id = 'transport';
	}
	
	public function loadtransporterswithfreightAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$now = time();
		
		$destinationCityId = intval($request->getParam('destinationCityId'));
		$DestinationTown = new Chamista_Model_Town($destinationCityId);
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'*' => 'Int'
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$cargo['resource0'] = $zfi->cargo_resource;
				$cargo['resource1'] = $zfi->cargo_tradegood1;
				$cargo['resource2'] = $zfi->cargo_tradegood2;
				$cargo['resource3'] = $zfi->cargo_tradegood3;
				$cargo['resource4'] = $zfi->cargo_tradegood4;
				if (array_sum($cargo) == 0) {
					$this->_helper->flashMessenger->addMessage("The trade fleet doesn`t have any freight");
					$this->_helper->redirector('index','error');
				}
				$ships = $User->getFreeShips();
				if (array_sum($cargo) > $ships * 500) {
					$this->_helper->flashMessenger->addMessage("You do not have enough trade fleets");
					$this->_helper->redirector('index','error');
				}
				foreach ($cargo as $i => $v) {
					if ($Town->{'town_'.$i} < $v) {
						$this->_helper->flashMessenger->addMessage("You do not have enough ".Chamista_Model_Format::tradegood_name(intval(substr($i,-1))));
						$this->_helper->redirector('index','error');
					}
					$Town->{'town_'.$i} -= $v;
				}
				$shipsUsed = ceil(array_sum($cargo) / 500);
				// Mark as loading
				$loadingSpeed = Chamista_Model_Formula::getLoadingSpeed($Town->getBuildingLvl(Chamista_Model_Formula::TRADING_PORT));
				$timeUsed = round(array_sum($cargo) / $loadingSpeed * 60);
				$data = array(
					'trans_uid' => $User->usr_id,
					'trans_ships' => $shipsUsed,
					'trans_origin' => $Town->town_id,
					'trans_destination' => $DestinationTown->town_id,
					'trans_mission' => 0,
					'trans_startdate' => $now,
					'trans_enddate' => $now + $timeUsed,
					'trans_cargo' => serialize($cargo)
				);
				$Transports->insert($data);
			}
		}
		
		$this->_helper->redirector('index','island','',array('id' => $DestinationTown->town_island));
	}
	
	public function merchantnavyAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$now = time();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$transportsView = array();
		$missions = array(0,1,2,3,4);
		$rows = $Transports->fetchAll($Transports->select()->where('trans_uid = ?',$User->usr_id)->where('trans_mission IN (?)',$missions));
		foreach ($rows as $row) {
			$Origin = new Chamista_Model_Town($row->trans_origin);
			if ($row->trans_destination != 0)
				$Destination = new Chamista_Model_Town($row->trans_destination);
			else
				$Destination = new Chamista_Model_Barbarian();
			$missionName = '';
			$eta = '';
			$ret = array();
			switch ($row->trans_mission) {
				case 0:
					$missionName = 'Transport (loading)';
					$eta = array(
						'title' => 'loading'
					);
					$ret = array(
						'title' => '-'
					);
					$class = ($Origin->town_uid == $Destination->town_uid) ? 'gotoown' : 'gotoforeign';
					break;
				case 1:
					$missionName = 'Transport (underway)';
					if ($Origin->town_uid == $Destination->town_uid) {
						$ret = array(
							'title' => '-'
						);
					} else {
						// Needs return, the return time is same as the travelling time
						$ret = array(
							'title' => '-'
						);
					}
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$class = ($Origin->town_uid == $Destination->town_uid) ? 'gotoown' : 'gotoforeign';
					break;
				case 2:
					$missionName = 'Transport (return)';
					if ($Origin->town_uid == $Destination->town_uid) {
						$ret = array(
							'title' => '-'
						);
					} else {
						// Needs return, the return time is same as the travelling time
					}
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$class = 'returning';
					break;
				case 3:
					$missionName = 'Colonize (loading)';
					$eta = array(
						'title' => 'loading'
					);
					$ret = array(
						'title' => '-'
					);
					$class = 'gotoown';
					break;
				case 4:
					$missionName = 'Colonize (underway)';
					$ret = array(
						'title' => '-'
					);
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$class = 'gotoown';
					break;
			}
			$cargo = unserialize($row->trans_cargo);
			$load_sum = array_sum($cargo);
			if ($row->trans_mission == 3 || $row->trans_mission == 4) $load_sum += 1250;
			$load_total = $row->trans_ships * 500;
			$goods = array();
			foreach ($cargo as $i => $v) {
				if ($v > 0) {
					if (($row->trans_mission == 3 || $row->trans_mission == 4) && $i == 'resource0') $v += 1250;
					if ($v > 47)
						$show = $v / $load_total * ($i == 'resource0') ? 47 : 48;
					else
						$show = $v;
					$goods[Chamista_Model_Format::tradegood_css(intval(substr($i,-1)))] = array(
						'show' => $show,
						'total' => $v,
						'margin' => ($i == 'resource0') ? 17 : 16,
						'name' => Chamista_Model_Format::tradegood_name(intval(substr($i,-1))),
						'width' => ($i == 'resource0') ? 25 : 24,
					);
				}
			}
			$target = array(
				'name' => $Destination->town_name,
				'id' => $Destination->town_id,
				'owner' => ($Destination->town_uid != $Origin->town_uid) ? array('name' => $Destination->getOwnerName(), 'id' => $Destination->town_uid) : array()
			);
			
			$transportsView[] = array(
				'id' => $row->trans_id,
				'quantity' => $row->trans_ships,
				'source' => array('name' => $Origin->town_name, 'id' => $Origin->town_id),
				'mission' => array(
					'class' => $class,
					'name' => $missionName
				),
				'target' => $target,
				'eta' => $eta,
				'ret' => $ret,
				'cargo' => array('load' => $load_sum, 'total' => $load_total, 'goods' => $goods)
			);
		}
		
		$missions = array(5,6,7);
		$troops = array();
		$rows = $Transports->fetchAll($Transports->select()->where('trans_uid = ?',$User->usr_id)->where('trans_mission IN (?)',$missions));
		foreach ($rows as $row) {
			$Origin = new Chamista_Model_Town($row->trans_origin);
			if ($row->trans_destination != 0)
				$Destination = new Chamista_Model_Town($row->trans_destination);
			$missionName = '';
			$eta = '';
			$ret = array();
			switch ($row->trans_mission) {
				case 5:
					$missionName = 'Pillage (underway)';
					$ret = array(
						'title' => '-'
					);
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$class = 'gotoforeign';
					break;
				case 6:
					$missionName = 'Pillage (Open Battle)';
					$ret = array(
						'title' => '-'
					);
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$class = 'gotoforeign';
					break;
				case 7:
					$missionName = 'Pillage (return)';
					$ret = array(
						'title' => '-',
					);
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$class = 'gotoforeign';
					break;
			}
			$cargo = (array) unserialize($row->trans_cargo);
			$load_sum = array_sum($cargo);
			if ($row->trans_mission == 3 || $row->trans_mission == 4) $load_sum += 1250;
			$load_total = $row->trans_ships * 500;
			$goods = array();
			foreach ($cargo as $i => $v) {
				if ($v > 0) {
					if (($row->trans_mission == 3 || $row->trans_mission == 4) && $i == 'resource0') $v += 1250;
					if ($v > 47)
						$show = $v / $load_total * ($i == 'resource0') ? 47 : 48;
					else
						$show = $v;
					$goods[Chamista_Model_Format::tradegood_css(intval(substr($i,-1)))] = array(
						'show' => $show,
						'total' => $v,
						'margin' => ($i == 'resource0') ? 17 : 16,
						'name' => Chamista_Model_Format::tradegood_name(intval(substr($i,-1))),
						'width' => ($i == 'resource0') ? 25 : 24,
					);
				}
			}
			if ($row->trans_destination != 0)
				$target = array(
					'name' => $Destination->town_name,
					'id' => $Destination->town_id,
					'owner' => ($Destination->town_uid != $Origin->town_uid) ? array('name' => $Destination->getOwnerName(), 'id' => $Destination->town_uid) : array()
				);
			else
				$target = array(
					'name' => 'Barbarian Village',
					'id' => 0,
					'owner' => '-'
				);
			
			$troops[] = array(
				'id' => $row->trans_id,
				'quantity' => $row->trans_ships,
				'source' => array('name' => $Origin->town_name, 'id' => $Origin->town_id),
				'mission' => array(
					'class' => $class,
					'name' => $missionName
				),
				'target' => $target,
				'eta' => $eta,
				'ret' => $ret,
				'cargo' => array('load' => $load_sum, 'total' => $load_total, 'goods' => $goods)
			);
		}
		
		$this->view->merchantNavy = array(
			'town_id' => $town_id,
			'island_id' => $Town->town_island,
			'goods' => $transportsView,
			'troops' => $troops,
		);
		
		$this->view->css = 'ik_merchantNavy_'.VERSION.'.css';
		$this->view->body_id = 'merchantNavy';
	}
	
	public function startcolonizationAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$Islands = new Chamista_Model_DbTable_Islands();
		$Towns = new Chamista_Model_DbTable_Towns();
		$now = time();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$islandId = (int) $request->getParam('id');
		if ($Islands->fetchRow($Islands->select()->from($Islands->_name,'COUNT(*) AS total')->where('island_id = ?',$islandId))->total == 0) {
			$this->_helper->flashMessenger->addMessage("Invalid island!");
			$this->_helper->redirector('index','error');
		}
		$position = (int) $request->getParam('desiredPosition');
		if ($Towns->fetchRow($Towns->select()->from($Towns->_name,'COUNT(*) as total')->where("town_location = ?",$position)->where("town_island = ?",$islandId))->total > 0) {
			$this->_helper->flashMessenger->addMessage("That position has already been taken!");
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
				$cargo['resource0'] = $zfi->sendresource;
				$cargo['resource1'] = $zfi->sendwine;
				$cargo['resource2'] = $zfi->sendmarble;
				$cargo['resource3'] = $zfi->sendcrystal;
				$cargo['resource4'] = $zfi->sendsulfur;
				$ships = $User->getFreeShips();
				
				$totalFreight = array_sum($cargo) + 1290;
				if ($totalFreight > $ships * 500) {
					$this->_helper->flashMessenger->addMessage("You do not have enough trade fleets");
					$this->_helper->redirector('index','error');
				}
				// Check first
				foreach ($cargo as $i => $v) {
					if ($i == 'resource0') $v += 1250;
					if ($Town->{'town_'.$i} < $v) {
						$this->_helper->flashMessenger->addMessage("You do not have enough ".Chamista_Model_Format::tradegood_name(intval(substr($i,-1))));
						$this->_helper->redirector('index','error');
					}
				}
				// And then deduct
				foreach ($cargo as $i => $v) {
					if ($i == 'resource0') $v += 1250;
					$Town->{'town_'.$i} -= $v;
				}
				$shipsUsed = ceil($totalFreight / 500);
				// Mark as loading
				$loadingSpeed = Chamista_Model_Formula::getLoadingSpeed($Town->getBuildingLvl(Chamista_Model_Formula::TRADING_PORT));
				$timeUsed = round($totalFreight / $loadingSpeed * 60);
				$User->usr_gold -= 9000;
				
				// Create a colonization town
				$townId = $Towns->colonize($islandId, $position, $User->usr_id);
				
				$data = array(
					'trans_uid' => $User->usr_id,
					'trans_ships' => $shipsUsed,
					'trans_origin' => $Town->town_id,
					'trans_destination' => $townId,
					'trans_mission' => 3,
					'trans_startdate' => $now,
					'trans_enddate' => $now + $timeUsed,
					'trans_cargo' => serialize($cargo)
				);
				$Transports->insert($data);
			}
		}
		
		$this->_helper->redirector('index','island','',array('id' => $islandId));
	}
	
	public function attackbarbarianvillageAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$Units = new Chamista_Model_DbTable_Units();
		$Town_Units = new Chamista_Model_DbTable_Towns_Units();
		$now = time();
		
		$town_id = $request->getParam('id');
		$Town = new Chamista_Model_Town($town_id);
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'*' => 'Int'
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$post = $request->getPost();
				$armies = array();
				foreach ($post as $i => $v) {
					if (substr($i,0,11) == 'cargo_army_' && substr($i,-7) != '_upkeep') {
						$type = (int) substr($i,-1);
						if ($Town_Units->getArmy($type, $Town->town_id) >= $zfi->{$i}) {
							$armies[$type] = $zfi->{$i};
							$Town_Units->setArmy($type, $Town_Units->getArmy($type ,$Town->town_id) - intval($v), $Town->town_id);
						}
					}
				}
				// Calculate additional upkeeps
				$trans_troops = 0;
				foreach ($armies as $type => $v) {
					$trans_troops += $Units->fetchAll(
						$Units->select()
							->from($Units->_name, 'unit_upkeep')
							->where('unit_id = ?', $type)
					)->unit_upkeep * SPEED;
				}
				
				$insert = array(
					'trans_uid' => $User->usr_id,
					'trans_ships' => $zfi->transporter,
					'trans_origin' => $Town->town_id,
					'trans_destination' => 0,
					'trans_mission' => 5,
					'trans_startdate' => $now,
					'trans_enddate' => $now + Chamista_Model_Formula::travelTime(60,$Town->getIslandXY(),$Town->getIslandXY()),
					'trans_cargo' => '',
					'trans_armies' => serialize($armies),
					'trans_troops' => $trans_troops,
				);
				$Transports->insert($insert);
			}
		}
		
		$this->_helper->redirector('index','island','',array('id' => $Town->town_island));
	}
}