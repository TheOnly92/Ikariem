<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Town hall controller file.
 * Handle all actions redirected to the town hall controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class TownhallController extends Rewaz_Controller_Action_Buildings {
	public function preDispatch() {
		parent::preDispatch();
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Islands = new Chamista_Model_DbTable_Islands();
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		
		$Town = new Chamista_Model_Town($town_id);
		
		$position = intval($request->getParam('position'));
		if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::TOWN_HALL) {
			throw new Exception('Invalid URL!');
		}
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($Town->town_name,'/city/index/id/'.$Town->town_id, 'Back to the town');
		$Trailer->addStep('Town hall');
		
		if ($Town->isBeingExpanded($position)) {
			$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
			$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
				->where('queue_town = ?',$Town->town_id)
				->where('queue_type = ?',Chamista_Model_Formula::TOWN_HALL)
				->where('queue_pos = ?',$position));
			$now = time();
			$this->view->construction = array(
				'startdate' => $row->queue_created,
				'enddate' => $row->queue_endtime,
				'currentdate' => $now,
				'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::TOWN_HALL),
				'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
				'build_lvl' => $Town->getPositionLvl($position),
				'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
			);
		}
		
		$this->view->corruption = $Town->getCorruption();
		
		// --- Variables for Population and production graph ---
		$this->view->freeCitizens = $Town->getFreeCitizens();
		$this->view->lumberWorkers = $Town->getLumberWorkers();
		$this->view->resourceWorkers = $Town->getResourceWorkers();
		$this->view->scientists = $Town->getScientists();
		$this->view->priests = $Town->getPriests();
		$this->view->population = floor($Town->town_population);
		$this->view->woodProduction = $Town->getWoodProduction();
		$this->view->resourceProd = $Town->getResourceProduction();
		$this->view->researchProd = $Town->getResearchProduction();
		$this->view->resource = Chamista_Model_Format::tradegood_css($Islands->fetchRow($Islands->select()
			->from($Islands->_name,'island_resource')
			->where('island_id = ?',$Town->town_island))->island_resource);
		// --- End ---
		
		// --- Variables for Satisfaction graph ---
		$tavern = false;
		if ($Town->isBuildingBuilt(Chamista_Model_Formula::TAVERN)) {
			$tavern = array(
				'base' => $Town->getBuildingLvl(Chamista_Model_Formula::TAVERN) * 12,
				'wine' => Chamista_Model_Formula::getTavernWine($Town->town_wine) * 15
			);
		}
		
		$corruption = Chamista_Model_Formula::BASIC_HAPPINESS + $Town->getResearchHappiness() + $Town->happinessCapBonus();
		if ($tavern !== false) {
			$corruption += array_sum($tavern);
		}
		$corruption = $corruption * ($Town->getCorruption() / 100);
		
		$this->view->satisfaction = array(
			'basic' => Chamista_Model_Formula::BASIC_HAPPINESS,
			'research' => $Town->getResearchHappiness(),
			'capital' => $Town->happinessCapBonus(),
			'population' => floor($Town->town_population),
			'tavern' => $tavern,
			'corruption' => $corruption
		);
		// --- End ---
		
		$this->view->is_capital = $Town->isCapital();
		$this->view->town_name = $Town->town_name;
		$this->view->town_id = $Town->town_id;
		$this->view->town_population = floor($Town->town_population);
		$this->view->population_limit = $Town->getPopulationLimit();
		$this->view->town_actions = $Town->getAvailActionPoints();
		$this->view->max_actions = Chamista_Model_Formula::getActionPoints($Town->town_lvl);
		$this->view->satisfaction_word = $Town->getSatisfaction();
		$this->view->satisfaction_img = Chamista_Model_Format::getSatisfactionImg($Town->getSatisfaction());
		$this->view->happiness = $Town->getHappiness();
		$this->view->growth = $Town->getGrowth();
		$this->view->gold_income = $Town->getGoldIncome();
		$this->view->build_lvl = $Town->getPositionLvl($position);
		$this->view->position = $position;
		$this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::TOWN_HALL,$this->view->build_lvl + 1,$User);
		$this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::TOWN_HALL,$this->view->build_lvl + 1,true));
		$this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::TOWN_HALL);
		$this->view->body_id = 'townHall';
		$this->view->css = 'ik_townHall_'.VERSION.'.css';
	}
	
	public function renamecityAction() {
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
		if ($Town->getGroundBuilding($position) != 1) {
			throw new Exception('Invalid URL!');
		}
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($Town->town_name,'/city/index/id/'.$Town->town_id, 'Back to the town');
		$Trailer->addStep('Town hall','/townHall/index/id/'.$Town->town_id.'/position/'.$position, 'Town hall');
		$Trailer->addStep('Rename town');
		
		$this->view->town_name = $Town->town_name;
		$this->view->town_id = $town_id;
		$this->view->body_id = 'renameCity';
		$this->view->css = 'ik_renameCity_'.VERSION.'.css';
	}
	
	public function renameAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		
		$Town = new Chamista_Model_Town($town_id);
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim',
				'town_name' => 'Alnum'
			);
			
			$validation = array(
				'town_name' => array(
					array('StringLength',3,15),
				)
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$Town->town_name = $zfi->town_name;
				$this->_helper->redirector('index','townHall','',array('id' => $town_id));
			} else {
				$dynamics = new Zend_Session_Namespace('dynamics');
				$dynamics->box = array(
					'type' => 'backTo',
					'title' => 'Town',
					'href' => '/city/index/id/'.$town_id,
					'img' => '/img/relatedCities/show_city.jpg'
				);
				$dynamics->setExpirationHops(1);
				
				$this->_helper->flashMessenger->addMessage("The town name couldn't be saved. The name should contain between 3 and 15 letters and no special characters.");
				$this->_helper->redirector('index','error');
			}
		} else {
			$this->_helper->redirector('index','townHall','',array('id' => $town_id));
		}
	}
}