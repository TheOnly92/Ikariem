<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Finances controller file.
 * Handles all actions redirected to the finances controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class FinancesController extends Zend_Controller_Action {
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
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		$Units = new Chamista_Model_DbTable_Units();
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Balance sheet of all finances');
		
		$cities = $User->getUserCities();
		$city_view = array();
		$income_sum = 0;
		$upkeep_sum = 0;
		$result_sum = 0;
		foreach ($cities as $city) {
			$Town = new Chamista_model_Town($city['town_id']);
			$income = floor($Town->getGoldIncomeOnly());
			$upkeep = floor($Town->getGoldUpkeepOnly());
			$result = $income - $upkeep;
			$city_view[] = array(
				'town' => $Town->town_name,
				'income' => $income,
				'upkeep' => $upkeep,
				'result' => $result
			);
			$income_sum += $income;
			$upkeep_sum += $upkeep;
			$result_sum += $result;
			
			unset($Town);
		}
		
		// Troop upkeep
		$troops = $Units->fetchAll($Units->select()->from($Units->_name,'unit_id')->where('unit_type = ?',0));
		$temp = array();
		foreach ($troops as $troop) {
			$temp[] = $troop->unit_id;
		}
		$troops = $temp;
		$city_select = array();
		foreach ($User->getUserCityIDs() as $city) {
			$cities_select[] = $city->town_id;
		}
		$select = $Towns_Units->select()
			->from($Towns_Units->_name,array('unit_id','value'))
			->where('town_id IN (?)',$cities_select)
			->where('unit_id IN (?)',$troops);
		$unit_rows = $Towns_Units->fetchAll($select);
		$upkeep_troops = 0;
		foreach ($unit_rows as $unit_row) {
			$upkeep = $Units->fetchRow($Units->select()->from($Units->_name,'unit_upkeep')->where('unit_id = ?',$unit_row->unit_id))->unit_upkeep;
			$upkeep_troops += $upkeep * $unit_row->value;
		}
		$discount_troops = Chamista_Model_Formula::getTroopsDiscount($User);
		
		// Fleet upkeep
		$fleets = $Units->fetchAll($Units->select()->from($Units->_name,'unit_id')->where('unit_type = ?',1));
		$temp = array();
		foreach ($fleets as $troop) {
			$temp[] = $troop->unit_id;
		}
		$fleets = $temp;
		$city_select = array();
		foreach ($User->getUserCityIDs() as $city) {
			$cities_select[] = $city->town_id;
		}
		$select = $Towns_Units->select()
			->from($Towns_Units->_name,array('unit_id','value'))
			->where('town_id IN (?)',$cities_select)
			->where('unit_id IN (?)',$fleets);
		$unit_rows = $Towns_Units->fetchAll($select);
		$upkeep_fleets = 0;
		foreach ($unit_rows as $unit_row) {
			$upkeep = $Units->fetchRow($Units->select()->from($Units->_name,'unit_upkeep')->where('unit_id = ?',$unit_row->unit_id))->unit_upkeep;
			$upkeep_fleets += $upkeep * $unit_row->value;
		}
		$discount_fleets = Chamista_Model_Formula::getFleetsDiscount($User);
		
		$this->view->basic = array(
			'troops' => $upkeep_troops,
			'troops_discount' => $discount_troops * 100,
			'troops_discount_value' => $upkeep_troops * (1-$discount_troops),
			'troops_discount_width' => 100 - $discount_troops * 100,
			'troops_total' => $upkeep_troops * (1-$discount_troops),
			'fleets' => $upkeep_fleets,
			'fleets_discount' => $discount_fleets * 100,
			'fleets_discount_value' => $upkeep_fleets * (1-$discount_fleets),
			'fleets_discount_width' => 100 - $discount_fleets * 100,
			'fleets_total' => $upkeep_fleets * (1-$discount_fleets),
		);
		$this->view->basic['total'] = $this->view->basic['troops_total'] + $this->view->basic['fleets_total'];
		$this->view->total_gold = floor($User->usr_gold);
		$this->view->city_view = $city_view;
		$this->view->income_sum = $income_sum;
		$this->view->upkeep_sum = $upkeep_sum;
		$this->view->result_sum = $result_sum;
		$this->view->result_upkeep = -$this->view->basic['total'];
		$this->view->total = $this->view->result_sum + $this->view->result_upkeep;
		$this->view->town_id = Zend_Registry::get('session')->current_city;
		
		$this->view->css = 'ik_finances_'.VERSION.'.css';
		$this->view->body_id = 'finances';
	}
}