<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * User model file.
 * Handles specific user actions.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_User {
	protected $Users;
	public $usr_id;
	protected $_vars = array();
	
	public function __construct($usr_id) {
		$this->usr_id = $usr_id;
		$this->Users = new Chamista_Model_DbTable_Users();
		$row = $this->Users->fetchRow($this->Users->select()->from($this->Users->_name,'COUNT(*) AS total')->where('usr_id = ?',$usr_id));
		if ($row->total == 0) {
			return false;
		}
	}
	
	public function __get($name) {
		if (!isset($this->_vars[$name])) {
			$row = $this->Users->fetchRow($this->Users->select()->from($this->Users->_name,$name)->where('usr_id = ?',$this->usr_id)->limit(1));
			$result = $row->{$name};
			$this->_vars[$name] = $result;
		} else {
			$result = $this->_vars[$name];
		}
		return $result;
	}
	
	public function __set($name,$value) {
		$data = array($name => $value);
		$where = $this->Users->getAdapter()->quoteInto('usr_id = ?', $this->usr_id);
		$this->Users->update($data,$where);
	}
	
	public function getFreeShips() {
		$ships = $this->usr_ships;
		// @TODO Deduct ships on route
		$Transports = new Chamista_Model_DbTable_Transports();
		$ships -= $Transports->fetchRow($Transports->select()
			->from($Transports->_name,'SUM(trans_ships) AS total')
			->where('trans_uid = ?', $this->usr_id))->total;
		
		return $ships;
	}
	
	public function getUserCities() {
		$Towns = new Chamista_Model_DbTable_Towns();
		$Islands = new Chamista_Model_DbTable_Islands();
		
		$cities = $Towns->getUserCities($this->usr_id);
		$return = array();
		foreach ($cities as $city) {
			$return[] = array(
				'town_id' => $city->town_id,
				'town_island' => $city->town_island,
				'town_name' => $city->town_name,
				'tradegood' => $Islands->getTradeGood($city->town_island),
				'posx' => $Islands->getPosX($city->town_island),
				'posy' => $Islands->getPosY($city->town_island),
				'tradegood_name' => Chamista_Model_Format::tradegood_name($Islands->getTradeGood($city->town_island))
			);
		}
		
		return $return;
	}
	
	public function getUserCityIDs($inArray = false) {
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$cities = $Towns->fetchAll($Towns->select()
			->from($Towns->_name,'town_id')
			->where('town_uid = ?', $this->usr_id));
		
		if ($inArray) {
			$rt = array();
			foreach ($cities as $city) {
				$rt[] = $city->town_id;
			}
			return $rt;
		}
		return $cities;
	}
	
	public function userOwnsCity($townId) {
		$Towns = new Chamista_Model_DbTable_Towns();
		
		$cities = $Towns->fetchRow($Towns->select()
			->from($Towns->_name,'COUNT(*) AS total')
			->where('town_uid = ?', $this->usr_id)
			->where('town_id = ?',$townId));
		
		if ($cities->total > 0)
			return true;
		else
			return false;
	}
	
	public function getShips() {
		return $this->usr_ships;
	}
	
	public function getGlobalIncome() {
		$Units = new Chamista_Model_DbTable_Units();
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		$cities = $this->getUserCityIDs();
		
		$income = 0;
		$upkeep = 0;
		
		$troops = $Units->fetchAll($Units->select()->from($Units->_name,'unit_id')->where('unit_type = ?',0));
		$temp = array();
		foreach ($troops as $troop) {
			$temp[] = $troop->unit_id;
		}
		$troops = $temp;
		$fleets = $Units->fetchAll($Units->select()->from($Units->_name,'unit_id')->where('unit_type = ?',1));
		$temp = array();
		foreach ($fleets as $troop) {
			$temp[] = $troop->unit_id;
		}
		$fleets = $temp;
		foreach ($cities as $city) {
			$upkeep_troops = 0;
			$upkeep_fleets = 0;
			$Town = new Chamista_Model_Town($city->town_id);
			$income += $Town->getGoldIncome();
			
			$select = $Towns_Units->select()
				->from($Towns_Units->_name,array('unit_id','value'))
				->where('unit_id IN (?)',$troops);
			$troops_rows = $Towns_Units->fetchAll($select);
			foreach ($troops_rows as $unit_row) {
				$upkeepT = $Units->fetchRow($Units->select()->from($Units->_name,'unit_upkeep')->where('unit_id = ?',$unit_row->unit_id))->unit_upkeep;
				$upkeep_troops += $upkeepT * $unit_row->value;
			}
			$discount_troops = 1 - Chamista_Model_Formula::getTroopsDiscount($this);
			$upkeep += $upkeep_troops * $discount_troops;
			
			$select = $Towns_Units->select()
				->from($Towns_Units->_name,array('unit_id','value'))
				->where('unit_id IN (?)',$fleets);
			$fleets_rows = $Towns_Units->fetchAll($select);
			foreach ($fleets_rows as $unit_row) {
				$upkeepT = $Units->fetchRow($Units->select()->from($Units->_name,'unit_upkeep')->where('unit_id = ?',$unit_row->unit_id))->unit_upkeep;
				$upkeep_fleets += $upkeepT * $unit_row->value;
			}
			$discount_fleets = 1 - Chamista_Model_Formula::getFleetsDiscount($this);
			$upkeep += $upkeep_fleets * $discount_fleets;
			
			// Clear resources
			unset($Town);
		}
		return $income - $upkeep;
	}
	
	public function getGlobalResearch() {
		$cities = $this->getUserCityIDs();
		
		$research = 0;
		foreach ($cities as $city) {
			$Town = new Chamista_Model_Town($city->town_id);
			$research += $Town->getResearchProduction();
			
			unset($Town);
		}
		return $research;
	}
	
	public function getGlobalScientists() {
		$cities = $this->getUserCityIDs();
		
		$scientists = 0;
		foreach ($cities as $city) {
			$Town = new Chamista_Model_Town($city->town_id);
			$scientists += $Town->getScientists();
			
			unset($Town);
		}
		return $scientists;
	}
	
	public function newEvent() {
		$Events = new Chamista_Model_DbTable_Events();
		$row = $Events->fetchRow($Events->select()->from($Events->_name,'COUNT(*) AS total')->where('event_uid = ?',$this->usr_id)->where('event_new = ?',1));
		if ($row->total > 0) {
			return true;
		}
		return false;
	}
	
	public function newMesg() {
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		$row = $IKMessages->fetchRow($IKMessages->select()->from($IKMessages->_name,'COUNT(*) AS total')->where('message_receiver = ?',$this->usr_id)->where('message_new = ?',1));
		if ($row->total > 0) {
			return true;
		}
		return false;
	}
	
	public function userResearched($research) {
		$Users_Researches = new Chamista_Model_DbTable_Users_Researches();
		$row = $Users_Researches->fetchRow($Users_Researches->select()->from($Users_Researches->_name,'research_'.$research)->where('usr_id = ?',$this->usr_id)->limit(1));
		if ($row->{'research_'.$research} == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	public function researchNextField($field) {
		$Researches = new Chamista_Model_DbTable_Researches();
		$techs = $Researches->fetchAll($Researches->select()->from($Researches->_name,'research_id')->where('research_cat = ?',$field)->order('research_id ASC'));
		foreach ($techs as $tech) {
			if (!$this->userResearched($tech->research_id)) {
				return $tech->research_id;
			}
		}
	}
}