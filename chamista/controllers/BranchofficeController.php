<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Trading post controller file.
 * Handle all actions redirected to the trading post controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */
class BranchofficeController extends Rewaz_Controller_Action_Buildings {
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
		$Trailer->addStep($this->Town->town_name,'/city/index/id/'.$this->Town->town_id, 'Back to the town');
		
		$position = intval($request->getParam('position'));
		if ($this->Town->getGroundBuilding($position) != Chamista_Model_Formula::TRADING_POST) {
			throw new Exception('Invalid URL!');
		}
	}
	
	public function indexAction() {
		$request = $this->getRequest();
		$Tradings = new Chamista_Model_DbTable_Tradings();
		$Islands = new Chamista_Model_DbTable_Islands();
		
		$position = intval($request->getParam('position'));
		$lvl = $this->Town->getPositionLvl($position);
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Trading post');
		
		if ($this->Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->getCurrBuildingQueue($this->Town->town_id, Chamista_Model_Formula::TRADING_POST, $position);
			$now = time();
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::TRADING_POST),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $this->Town->getPositionLvl($position),
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$branchOffice = array();
		$trading = $Tradings->getTownRow($this->Town->town_id);
		$ownOffers = array(
			'wood_type' => $trading->trading_resource0_type,
			'wood_value' => $trading->trading_resource0_val,
			'wood_price' => $trading->trading_resource0_price,
			'wine_type' => $trading->trading_resource1_type,
			'wine_value' => $trading->trading_resource1_val,
			'wine_price' => $trading->trading_resource1_price,
			'marble_type' => $trading->trading_resource2_type,
			'marble_value' => $trading->trading_resource2_val,
			'marble_price' => $trading->trading_resource2_price,
			'crystal_type' => $trading->trading_resource3_type,
			'crystal_value' => $trading->trading_resource3_val,
			'crystal_price' => $trading->trading_resource3_price,
			'sulfur_type' => $trading->trading_resource4_type,
			'sulfur_value' => $trading->trading_resource4_val,
			'sulfur_price' => $trading->trading_resource4_price,
		);
		$branchOffice['ownOffers'] = $ownOffers;
		$branchOffice['maxSearch'] = Chamista_Model_Formula::tradingPost('search',$lvl);
		
		// Default values
		$branchOffice['search'] = array(
			'range' => $branchOffice['maxSearch'],
			'type' => 444,
			'searchResource' => 0,
		);
		
		if ($request->getCookie('ikariem_BranchOffice'))
			$branchOffice['search'] = unserialize($request->getCookie('ikariem_BranchOffice'));
		
		$bargainResults = array();
		
		// Search for bargain
		$filters = array(
			'*' => 'StringTrim'
		);
		
		$validation = array(
			'range' => 'Int',
			'searchResource' => 'Int',
			'type' => 'Int',
		);
		
		if ($request->isPost())
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
		else
			$zfi = new Zend_Filter_Input($filters, $validation, $branchOffice['search']);
		
		if ($zfi->isValid()) {
			if ($zfi->range > $branchOffice['maxSearch'] || $zfi->range < 1) {
				$zfi->range = $branchOffice['maxSearch'];
			}
			$currPos = $this->Town->getIslandXY();
			
			// Get islands in range
			$rows = $Islands->getIslandWithinRange($currPos, $zfi->range);
			$searchIslands = array();
			foreach ($rows as $row) {
				$searchIslands[] = $row->island_id;
			}
			
			$searchResource = $zfi->searchResource;
			if ($searchResource > 4) {
				$this->_helper->flashMessenger->addMessage("You have entered an invalid value!");
				$this->_helper->redirector('index','error');
			}
			
			if ($zfi->type == 333) {
				$type = 1;
			} elseif ($zfi->type == 444) {
				$type = 0;
			} else {
				$this->_helper->flashMessenger->addMessage("You have entered an invalid value!");
				$this->_helper->redirector('index','error');
			}
			
			// These sortings needs to be done before querying
			$order = 'trading_town ASC';
			switch ($request->getParam('orderBy')) {
				case 'cityAsc':
					$order = 't.town_name ASC';
					break;
				case 'cityDesc':
					$order = 't.town_name DESC';
					break;
				case 'ressAsc':
					$order = 'tr.trading_resource'.$searchResource.'_val ASC';
					break;
				case 'ressDesc':
					$order = 'tr.trading_resource'.$searchResource.'_val DESC';
					break;
				case 'sellAsc':
					$order = 'tr.trading_resource'.$searchResource.'_price ASC';
					break;
				case 'sellDesc':
					$order = 'tr.trading_resource'.$searchResource.'_price DESC';
					break;
			}
			
			$this->Towns = new Chamista_Model_DbTable_Towns();
			// Now get the bargains
			$rows = $Tradings->fetchAll(
				$Tradings->select()->setIntegrityCheck(false)
					->from(array('tr' => $Tradings->_name))
					->join(array('t' => $this->Towns->_name),'tr.trading_town = t.town_id')
					->where('tr.trading_island IN (?)',$searchIslands)
					->where('tr.trading_resource'.$searchResource.'_type = ?', $type)
					->where('tr.trading_town <> ?',$this->Town->town_id)
					->where('tr.trading_resource'.$searchResource.'_val > ?',0)
			);
			
			foreach ($rows as $row) {
				$rTown = new Chamista_Model_Town($row->trading_town);
				$rPos = $rTown->getIslandXY();
				$bargainResults[] = array(
					'town' => array('name' => $rTown->town_name, 'owner' => $rTown->getOwnerName(),'id' => $rTown->town_id),
					'harbor' => $rTown->getBuildingLvl(Chamista_Model_Formula::TRADING_PORT),
					'resource' => array(
						'css' => Chamista_Model_Format::tradegood_css($searchResource),
						'name' => Chamista_Model_Format::tradegood_name($searchResource),
						'id' => $searchResource,
					),
					'piece' => $row->{'trading_resource'.$searchResource.'_val'},
					'price' => $row->{'trading_resource'.$searchResource.'_price'},
					'distance' => sqrt(pow($rPos['x'] - $currPos['x'],2) + pow($rPos['y'] - $currPos['y'],2)),
					'type' => $zfi->type,
				);
			}
			
			// These sortings are done after querying and everything
			switch ($request->getParam('orderBy')) {
				case 'portLevelAsc':
					$bargainResults = Chamista_Model_Format::orderBy($bargainResults,'harbor');
					break;
				case 'portLevelDesc':
					$bargainResults = Chamista_Model_Format::orderBy($bargainResults,'harbor',0);
					break;
				case 'distanceAsc':
					$bargainResults = Chamista_Model_Format::orderBy($bargainResults,'distance');
					break;
				case 'distanceDesc':
					$bargainResults = Chamista_Model_Format::orderBy($bargainResults,'distance',0);
					break;
			}
			
			$branchOffice['search'] = array(
				'range' => $zfi->range,
				'type' => $zfi->type,
				'searchResource' => $zfi->searchResource,
			);
		}
		
		setcookie(
			'ikariem_BranchOffice',
			serialize($branchOffice['search']),
			time() + 3600 * 24 * 30,
			'/'
		);
		
		$branchOffice['bargainResults'] = $bargainResults;
		
		$this->view->branchOffice = $branchOffice;
		$this->view->position = $position;
		$this->view->build_lvl = $this->Town->getPositionLvl($position);
		$this->view->town_id = $this->Town->town_id;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::TRADING_POST,$this->view->build_lvl + 1,$this->User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::TRADING_POST,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::TRADING_POST);
		$this->view->css = 'ik_branchOffice_'.VERSION.'.css';
		$this->view->body_id = 'branchOffice';
	}
	
	public function updateoffersAction() {
		$request = $this->getRequest();
		$Tradings = new Chamista_Model_DbTable_Tradings();
		
		$position = intval($request->getParam('position'));
		$lvl = $this->Town->getPositionLvl($position);
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'*' => 'Int'
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$row = $Tradings->getTownRow($this->Town->town_id);
				
				$update = array();
				if ($zfi->resourceTradeType == 444) {
					$update['trading_resource0_type'] = 0;
				} elseif ($zfi->resourceTradeType == 333) {
					$update['trading_resource0_type'] = 1;
				}
				if ($zfi->resourcePrice >= 0 && $zfi->resourcePrice < 100) {
					$update['trading_resource0_price'] = $zfi->resourcePrice;
					$resource = $zfi->resource;
					if ($resource < 0) {
						$resource = 0;
						$update['trading_resource0_price'] = 0;
					} elseif ($resource > $this->Town->town_resource0) {
						$resource = floor($this->Town->town_resource0);
					}
					$update['trading_resource0_val'] = $resource;
				}
				
				if ($zfi->tradegood1TradeType == 444) {
					$update['trading_resource1_type'] = 0;
				} elseif ($zfi->tradegood1TradeType == 333) {
					$update['trading_resource1_type'] = 1;
				}
				if ($zfi->tradegood1Price > 0 && $zfi->tradegood1Price < 100) {
					$update['trading_resource1_price'] = $zfi->tradegood1Price;
					$resource = $zfi->tradegood1;
					if ($resource < 0) {
						$resource = 0;
						$update['trading_resource1_price'] = 0;
					} elseif ($resource > $this->Town->town_resource1) {
						$resource = floor($this->Town->town_resource1);
					}
					$update['trading_resource1_val'] = $resource;
				}
				
				if ($zfi->tradegood2TradeType == 444) {
					$update['trading_resource2_type'] = 0;
				} elseif ($zfi->tradegood2TradeType == 333) {
					$update['trading_resource2_type'] = 1;
				}
				if ($zfi->tradegood2Price > 0 && $zfi->tradegood2Price < 100) {
					$update['trading_resource2_price'] = $zfi->tradegood2Price;
					$resource = $zfi->tradegood2;
					if ($resource < 0) {
						$resource = 0;
						$update['trading_resource2_price'] = 0;
					} elseif ($resource > $this->Town->town_resource2) {
						$resource = floor($this->Town->town_resource2);
					}
					$update['trading_resource2_val'] = $resource;
				}
				
				if ($zfi->tradegood3TradeType == 444) {
					$update['trading_resource3_type'] = 0;
				} elseif ($zfi->tradegood3TradeType == 333) {
					$update['trading_resource3_type'] = 1;
				}
				if ($zfi->tradegood3Price > 0 && $zfi->tradegood3Price < 100) {
					$update['trading_resource3_price'] = $zfi->tradegood3Price;
					$resource = $zfi->tradegood3;
					if ($resource < 0) {
						$resource = 0;
						$update['trading_resource3_price'] = 0;
					} elseif ($resource > $this->Town->town_resource3) {
						$resource = floor($this->Town->town_resource3);
					}
					$update['trading_resource3_val'] = $resource;
				}
				
				if ($zfi->tradegood4TradeType == 444) {
					$update['trading_resource4_type'] = 0;
				} elseif ($zfi->tradegood4TradeType == 333) {
					$update['trading_resource4_type'] = 1;
				}
				if ($zfi->tradegood4Price > 0 && $zfi->tradegood4Price < 100) {
					$update['trading_resource4_price'] = $zfi->tradegood4Price;
					$resource = $zfi->tradegood4;
					if ($resource < 0) {
						$resource = 0;
						$update['trading_resource4_price'] = 0;
					} elseif ($resource > $this->Town->town_resource4) {
						$resource = floor($this->Town->town_resource4);
					}
					$update['trading_resource4_val'] = $resource;
				}
				
				$Tradings->update($update,$Tradings->getAdapter()->quoteInto('trading_town = ?',$this->Town->town_id));
			}
		}
		
		$this->_helper->redirector('index','branchOffice','',array('id' => $this->Town->town_id, 'position' => $position));
	}
}