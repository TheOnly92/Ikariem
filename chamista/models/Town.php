<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Town model file.
 * Handles every data related to a single town.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_Town {
	/**
	 * Holds the Chamista_Model_DbTable_Towns class
	 * 
	 * @var object
	 */
	protected $Towns;
	
	/**
	 * Holds the Chamista_Model_DbTable_Buildings class
	 * 
	 * @var object
	 */
	protected $Buildings;
	
	/**
	 * Holds the $User class of the owner of the town
	 * 
	 * @var object
	 */
	protected $User;
	
	/**
	 * The ID of the town
	 * 
	 * @var int
	 */
	public $town_id;
	
	/**
	 * Determines whether or not this $Town instance is used for view, where everything will be floor'd
	 * 
	 * @deprecated
	 * @var boolean
	 */
	protected $in_view = false;
	
	/**
	 * Caches the values and properties of the town retrieved during this instanc
	 * e
	 * @var array
	 */
	protected $_vars = array();
	
	/**
	 * Constructs the Town object
	 * 
	 * @param int $town_id
	 * 	The ID of the town
	 * @param boolean @deprecated $in_view
	 * 	TRUE to set it in_view, FALSE to not
	 * @return void
	 */
	public function __construct($town_id,$in_view = false) {
		$town_id = intval($town_id);
		$this->Towns = new Chamista_Model_DbTable_Towns();
		$this->Buildings = new Chamista_Model_DbTable_Buildings();
		$this->town_id = $town_id;
		$this->in_view = $in_view;
		if (!$this->town_uid) {
			$flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
			$flashMessenger->addMessage("Town not found!");
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->goto('index','error');
		}
		$this->User = new Chamista_Model_User($this->town_uid);
		$this->updateTown();
	}
	
	/**
	 * Retrieves the property of the town from the database and cache it in $_vars
	 * 
	 * @param string $name
	 * 	The name of the property
	 * @return mixed
	 * 	The value of the property
	 */
	public function __get($name) {
		if (!isset($this->_vars[$name])) {
			$row = $this->Towns->fetchRow($this->Towns->select()->from($this->Towns->_name,$name)->where('town_id = ?', $this->town_id)->limit(1));
			$result = $row->{$name};
		} else {
			$result = $this->_vars[$name];
		}
		if ($this->in_view) {
			if (is_float($result)) {
				return floor($result);
			}
		}
		return $result;
	}
	
	/**
	 * Updates the property of the town to the database
	 * 
	 * @param string $name
	 * 	The name of the property
	 * @param string $value
	 * 	The value to update to
	 * @return void
	 */
	public function __set($name,$value) {
		$data = array($name => $value);
		$where = $this->Towns->getAdapter()->quoteInto('town_id = ?', $this->town_id);
		$this->Towns->update($data,$where);
	}
	
	/**
	 * Gets the available action points for this town
	 * It will first retrieve the maximum action point from Chamista_Model_Formula, then deduct the used ones
	 * 
	 * @return int
	 * 	The available action points
	 */
	public function getAvailActionPoints() {
		$Transports = new Chamista_Model_DbTable_Transports();
		$maxAP = Chamista_Model_Formula::getActionPoints($this->town_lvl);
		$maxAP -= $Transports->fetchRow(
			$Transports->select()
				->from($Transports->_name, 'COUNT(*) AS total')
				->where('trans_uid = ?', $this->town_uid)
				->where('trans_origin = ?', $this->town_id)
		)->total;
		return $maxAP;
	}
	
	/**
	 * Gets the corruption only if this is a colony, in 100%
	 * 
	 * @return int
	 * 	The corruption in 100%
	 */
	public function getCorruption() {
		$corruption = 0;
		if (!$this->isCapital()) {
			$Capital = new Chamista_Model_Town($this->User->usr_capital);
			$corruption = round((1 - ($this->getBuildingLvl(Chamista_Model_Formula::GOVNR_RESIDENCE) + 1) / ($Capital->getBuildingLvl(Chamista_Model_Formula::PALACE) + 1)) * 100);
			if ($corruption < 0) $corruption = 0;
		}
		return $corruption;
	}
	
	/**
	 * Gets the island's X,Y coordinates by the Chamista_Model_DbTable_Towns getIslandXY method
	 * 
	 * @return array
	 * 	The X Y coordinates of the island the town is on
	 */
	public function getIslandXY() {
		return $this->Towns->getIslandXY($this->town_id);
	}
	
	/**
	 * Gets the island's name the town is on
	 * 
	 * @return string
	 * 	The island's name the town is on
	 */
	public function getIslandName() {
		$Island = new Chamista_Model_DbTable_Islands();
		$island_id = $this->Towns->getIslandID($this->town_id);
		$row = $Island->fetchRow($Island->select()->from($Island->_name,'island_name')->where('island_id = ?',$island_id)->limit(1));
		return $row->island_name;
	}
	
	/**
	 * Gets the island's resource type the town is on
	 * 
	 * @return int
	 * 	The integer ID of the resource of the island the town is on
	 */
	public function getIslandResource() {
		$Island = new Chamista_Model_DbTable_Islands();
		$island_id = $this->Towns->getIslandID($this->town_id);
		$row = $Island->fetchRow($Island->select()->from($Island->_name,'island_resource')->where('island_id = ?',$island_id)->limit(1));
		return $row->island_resource;
	}
	
	/**
	 * Get the town's available resources deducting those on the market
	 * 
	 * @param int $type
	 * 	The type of resource to be retrieved
	 * @return int
	 * 	The available resources in the town deducting the resource placed on trading post
	 */
	public function getTownResource($type) {
		$Tradings = new Chamista_Model_DbTable_Tradings();
		
		$rt = floor($this->{'town_resource'.$type});
		$row = $Tradings->getTownRow($this->town_id);
		$rt -= $row->{'trading_resource'.$type.'_val'};
		return $rt;
	}
	
	/**
	 * Gets the free citizens in town
	 * 
	 * @return int
	 * 	Free citizens in town
	 */
	public function getFreeCitizens() {
		$free = floor($this->town_population);
		$free -= $this->getLumberWorkers();
		$free -= $this->getResourceWorkers();
		$free -= $this->getScientists();
		$free -= $this->town_lumber_overload;
		$free -= $this->town_miner_overload;
		return $free;
	}
	
	/**
	 * This method handles the render for the town view
	 * 
	 * @return array
	 * 	The array that can be used for rendering the town view
	 */
	public function getGrounds() {
		$Buildings_Data = new Chamista_Model_DbTable_Buildings_Data();
		$grounds = array();
		for ($i=0;$i<=14;$i++) {
			$BuildID = $this->getGroundBuilding($i);
			if ($BuildID == 0) {
				$class = 'buildingGround ';
				if ($i < 3) {
					$class .= 'shore';
				} elseif ($i == 14) {
					$class .= 'wall';
				} else {
					$class .= 'land';
				}
				$href = '/city/buildingGround?id='.$this->town_id.'&position='.$i;
				$title = 'Free Building Ground';
				$divclass = 'flag';
			} else {
				$row = $Buildings_Data->fetchRow($Buildings_Data->select()->where('building_id = ?',$BuildID)->limit(1));
				$class = $row->building_css;
				$href = '/'.$row->building_css.'?id='.$this->town_id.'&position='.$i;
				$title = $row->building_name.' Level '.$this->getPositionLvl($i);
				$divclass = 'buildingimg';
			}
			if ($BuildID != 0) {
				if ($this->isBeingExpanded($i)) {
					$complete = $this->getExpandEnd($i);
					$now = time();
					$grounds[$i] = array(
						'class' => $class,
						'title' => $title.' (Under construction)',
						'construction' => array(
							'cityCountDown' => Chamista_Model_Format::formatTime($complete - $now),
							'currentdate' => $now,
							'enddate' => $complete
						),
						'href' => $href,
						'divclass' => 'constructionSite'
					);
					continue;
				}
			}
			if ($i == 13 && !$this->User->userResearched(49)) {
				$grounds[$i] = array(
					'class' => $class,
					'href' => '#',
					'divclass' => '',
					'title' => 'In order to build here, you must research bureaucracy'
				);
				continue;
			}
			$grounds[$i] = array(
				'class' => $class,
				'href' => $href,
				'title' => $title,
				'divclass' => $divclass
			);
		}
		
		return $grounds;
	}
	
	/**
	 * Check if the specified location on town is being expanded
	 * 
	 * @param int $location
	 * 	The location to be checked
	 * @return boolean
	 * 	TRUE if the location is being expanded, FALSE otherwise
	 */
	public function isBeingExpanded($location) {
		$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
		$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()->from($Buildings_Queue->_name,'COUNT(*) AS total')->where('queue_town = ?',$this->town_id)->where('queue_pos = ?',$location));
		if ($row->total != 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Checks if there are any buildings being expanded in the town
	 * 
	 * @return boolean
	 * 	TRUE if there is an expansion, FALSE otherwise
	 */
	public function isExpanding() {
		$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
		$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()->from($Buildings_Queue->_name,'COUNT(*) AS total')->where('queue_town = ?',$this->town_id));
		if ($row->total != 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Gets the end time of the expansion of the specified location, if there is any
	 * 
	 * @param int $location
	 * 	The location to be checked
	 * @return int
	 * 	The timestamp for when the expansion ends
	 */
	public function getExpandEnd($location) {
		if (!$this->isBeingExpanded($location)) {
			return 0;
		}
		$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
		$row = $Buildings_Queue->fetchRow($Buildings_Queue->select()->from($Buildings_Queue->_name,'queue_endtime')->where('queue_town = ?',$this->town_id)->where('queue_pos = ?',$location));
		return $row->queue_endtime;
	}
	
	/**
	 * Gets the type of building built on the specified location
	 * 
	 * @param int $location
	 * 	The location to be checked
	 * @return int
	 * 	The type of building built on the specified location
	 */
	public function getGroundBuilding($location) {
		return $this->Buildings->getGroundBuilding($location, $this->town_id);
	}
	
	/**
	 * Gets the level of palace of the town, if it is a capital, the level of governor residence, if it is a colony
	 * 
	 * @return int
	 * 	The level of palace or governor residence
	 */
	public function getPalaceLvl() {
		if ($this->isCapital()) {
			return $this->getBuildingLvl(Chamista_Model_Formula::PALACE);
		} else {
			return $this->getBuildingLvl(Chamista_Model_Formula::GOVNR_RESIDENCE);
		}
	}
	
	/**
	 * Gets the level of building built on the specified location
	 * 
	 * @param int $location
	 * 	The location to be checked
	 * @return int
	 * 	The level of the building
	 */
	public function getPositionLvl($location) {
		return $this->Buildings->getPositionLvl($location, $this->town_id);
	}
	
	/**
	 * Gets the warehouse capacity of this town
	 * 
	 * @param int $type
	 * 	If you want to get the capacity for a certain resource, specify it with this parameter. It will deduct resources placed in trading post
	 * @return int
	 * 	The capacity of the warehouse
	 */
	public function getWarehouseCap($type = null) {
		$Tradings = new Chamista_Model_DbTable_Tradings();
		
		$row = $this->Buildings->fetchRow($this->Buildings->select()->from($this->Buildings->_name,'SUM(build_lvl) as total_lvl')->where("build_type = ?", 5)->where("build_town = ?", $this->town_id));
		$rt = Chamista_Model_Formula::TOWN_HALL_STORAGE + Chamista_Model_Formula::getWarehouseStorage($row->total_lvl);
		
		if ($type !== null) {
			$row = $Tradings->getTownRow($this->town_id);
			$rt -= $row->{'trading_resource'.$type.'_val'};
		}
		return $rt;
	}
	
	/**
	 * Gets the number of resources placed in trading post
	 * 
	 * @param int $type
	 * 	The type of resource
	 * @return int
	 * 	The number of resources placed in trading post
	 */
	public function getTradingPost($type) {
		$Tradings = new Chamista_Model_DbTable_Tradings();
		
		$row = $Tradings->getTownRow($this->town_id);
		return $row->{'trading_resource'.$type.'_val'};
	}
	
	/**
	 * Gets the number of goods that are safe in the warehouse
	 * 
	 * @return int
	 * 	The number of goods that are safe in the warehouse
	 */
	public function getWarehouseSafe() {
		$row = $this->Buildings->fetchRow($this->Buildings->select()->from($this->Buildings->_name,'SUM(build_lvl) as total_lvl')->where("build_type = ?", 5)->where("build_town = ?", $this->town_id));
		return Chamista_Model_Formula::getWarehouseSafe($row->total_lvl);
	}
	
	/**
	 * Checks if this town is the capital of the owner
	 * 
	 * @return boolean
	 * 	TRUE if it is the capital, FALSE otherwise
	 */
	public function isCapital() {
		$Users = new Chamista_Model_DbTable_Users();
		$row = $Users->fetchRow($Users->select()->from($Users->_name,'usr_capital')->where("usr_id = ?", $this->town_uid)->limit(1));
		if ($row->usr_capital == $this->town_id) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Gets the population limit of the town
	 * 
	 * @return int
	 * 	The population limit of the town
	 */
	public function getPopulationLimit() {
		$limit = Chamista_Model_Formula::getPopulationLimit($this->town_lvl);
		// Well digging
		if ($this->isCapital() && $this->User->userResearched(3)) {
			$limit += 50;
		}
		return $limit;
	}
	
	/**
	 * Gets the happiness of the town
	 * 
	 * @return int
	 * 	The happiness of the town
	 */
	public function getHappiness() {
		$happiness = Chamista_Model_Formula::BASIC_HAPPINESS;
		$happiness += $this->happinessCapBonus();
		if ($this->isBuildingBuilt(Chamista_Model_Formula::TAVERN)) {
			$happiness += $this->getBuildingLvl(Chamista_Model_Formula::TAVERN) * 12;
			$happiness += Chamista_Model_Formula::getTavernWine($this->town_wine) * 15;
		}
		if ($this->getCorruption() > 0) {
			$happiness *= ($this->getCorruption() / 100);
		}
		$happiness -=  floor($this->town_population);
		return $happiness;
	}
	
	/**
	 * Gets the happiness bonus obtained through research
	 * 
	 * @return int
	 * 	Happiness bonus from research
	 */
	public function getResearchHappiness() {
		return 0;
	}
	
	/**
	 * Gets the happiness bonus for capital, if well digging is also researched
	 * 
	 * @return int
	 * 	The happiness bonus for capital
	 */
	public function happinessCapBonus() {
		$bonus = 0;
		// Well digging
		if ($this->isCapital() && $this->User->userResearched(3)) {
			$bonus += 50;
		}
		return $bonus;
	}
	
	/**
	 * Gets the growth of the town
	 * 
	 * @return float
	 * 	The growth of the town
	 */
	public function getGrowth() {
		return $this->getHappiness() * 0.02 * SPEED;
	}
	
	/**
	 * Gets the satisfaction of the town
	 * 
	 * @return string
	 * 	The satisfaction of the town
	 */
	public function getSatisfaction() {
		$growth = $this->getGrowth() / SPEED;
		if ($growth > 6) {
			return 'Euphoric';
		} elseif ($growth < 6 && $growth >= 1) {
			return 'Happy';
		} elseif ($growth < 1 && $growth >= 0) {
			return 'Neutral';
		} elseif ($growth < 0 && $growth >= -1) {
			return 'Sad';
		} elseif ($growth < -1) {
			return 'Angry';
		}
	}
	
	/**
	 * Get overall gold income of the town
	 * 
	 * @return int
	 * 	The overall gold income of the town
	 */
	public function getGoldIncome() {
		$income = $this->getGoldIncomeOnly();
		$upkeep = $this->getGoldUpkeepOnly();
		return $income - $upkeep;
	}
	
	/**
	 * Gets only the gold income of the town
	 * 
	 * @return int
	 * 	Gold income of the town
	 */
	public function getGoldIncomeOnly() {
		return $this->getFreeCitizens() * 3 * SPEED;
	}
	
	/**
	 * Gets the gold upkeep of the town
	 * 
	 * @return int
	 * 	Gold upkeep of the town
	 */
	public function getGoldUpkeepOnly() {
		return $this->getScientists() * 6 * SPEED;
	}
	
	/**
	 * Gets the number of lumber workesr of the town
	 * 
	 * @return int
	 * 	The lumber workers of the town
	 */
	public function getLumberWorkers() {
		return $this->town_lumber;
	}
	
	/**
	 * Gets the number of resource workers of the town
	 * 
	 * @return int
	 * 	The resource workers of the town
	 */
	public function getResourceWorkers() {
		return $this->town_miner;
	}
	
	/**
	 * Gets the number of scientists assigned in the town
	 * 
	 * @return int
	 * 	The number of scientists assigned in town
	 */
	public function getScientists() {
		return $this->town_scientists;
	}
	
	/**
	 * Gets the number of priests assigned in the town
	 * 
	 * @return int
	 * 	The number of priests assigned in the town
	 */
	public function getPriests() {
		return 0;
	}
	
	/**
	 * Gets the wood production of the town per hour
	 * 
	 * @return float
	 * 	The wood production of the town per hour
	 */
	public function getWoodProduction() {
		$production = $this->getLumberWorkers();
		if ($this->getCorruption()) $production *= ($this->getCorruption() / 100);
		// Overloaded workers are only 25% effective
		$production += $this->town_lumber_overload * 0.25;
		$production = floor($production);
		$bonus = $this->getBuildingLvl(Chamista_Model_Formula::FORESTER) * 2;
		if ($bonus > 0) {
			$production *= ($bonus+100)/100;
		}
		$production *=  SPEED;
		return $production;
	}
	
	/**
	 * Gets the resource production of the town per hour
	 * 
	 * @return float
	 * 	The research production of the town per hour
	 */
	public function getResourceProduction() {
		$production = $this->getResourceWorkers();
		if ($this->getCorruption()) $production *= ($this->getCorruption() / 100);
		// Overloaded workers are only 25% effective
		$production += $this->town_miner_overload * 0.25;
		$production = floor($production);
		$bonus = 0;
		switch ($this->getIslandResource()) {
			case 1:
				$bonus = $this->getBuildingLvl(Chamista_Model_Formula::WINEGROWER) * 2;
				break;
			case 2:
				$bonus = $this->getBuildingLvl(Chamista_Model_Formula::STONEMASON) * 2;
				break;
		}
		if ($bonus > 0) {
			$production *= ($bonus+100)/100;
		}
		$production *=  SPEED;
		return $production;
	}
	
	/**
	 * Gets the research production of the town per hour
	 * 
	 * @return float
	 * 	The research production of the town per hour
	 */
	public function getResearchProduction() {
		$research = $this->getScientists() * SPEED;
		// If Paper has been researched
		if ($this->User->userResearched(7)) {
			$research *= 1.02;
		}
		if ($this->getCorruption()) $research *= ($this->getCorruption() / 100);
		return $research;
	}
	
	/**
	 * Gets the available army of the specified unit in town
	 * 
	 * @param int $unit_id
	 * 	The type of unit
	 * @return int
	 * 	The number of units available
	 */
	public function getAvailableArmy($unit_id) {
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		return $Towns_Units->getArmy($unit_id,$this->town_id);
	}
	
	/**
	 * Checks if there are any troops in town
	 * 
	 * @return boolean
	 * 	TRUE if there are troops, FALSE otherwise
	 */
	public function hasTroops() {
		$Towns_Units = new Chamista_Model_DbTable_Towns_Units();
		$total = $Towns_Units->fetchRow(
			$Towns_Units->select()
				->from($Towns_Units->_name, 'SUM(value) AS total')
				->where('town_id = ?',$this->town_id)
		)->total;
		if ($total > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the number of currently available spies
	 * 
	 * @return int
	 * 	Number of unused spies
	 */
	public function getAvailSpies() {
		$spies = $this->town_spies;
		$spies -= $this->getSpiesInMission();
		return $spies;
	}
	
	/**
	 * Returns the number of spies currently in mission
	 * 
	 * @return int
	 * 	Number of used spies
	 */
	public function getSpiesInMission() {
		$Mission = new Chamista_Model_DbTable_Spies_Mission();
		return $Mission->getBusySpies($this->town_id);
	}
	
	/**
	 * Get the username of the owner of the town
	 * 
	 * @return string
	 * 	The username of the owner of the town
	 */
	public function getOwnerName() {
		$Users = new Chamista_Model_DbTable_Users();
		$row = $Users->fetchRow($Users->select()->from($Users->_name,'usr_nick')->where('usr_id = ?', $this->town_uid));
		return $row->usr_nick;
	}
	
	/**
	 * Checks if the building has been built in the town
	 * 
	 * @param int $build_id
	 * 	The building id to be checked
	 * @return int
	 * 	0 if not built, the number of building built otherwise
	 */
	public function isBuildingBuilt($build_id) {
		// Some buildings are allowed to build multiple times
		if ($build_id == Chamista_Model_Formula::WAREHOUSE) return 0;
		
		$Buildings = new Chamista_Model_DbTable_Buildings();
		$row = $Buildings->fetchRow($Buildings->select()
			->from($Buildings->_name,'COUNT(*) AS total')
			->where('build_town = ?', $this->town_id)
			->where('build_type = ?',$build_id));
		
		return $row->total;
	}
	
	/**
	 * Gets the position the specified building is being built on
	 * 
	 * @param int $build_id
	 * 	The building id to be checked
	 * @return int
	 * 	The position of the building
	 */
	public function getBuildingPos($build_id) {
		$Buildings = new Chamista_Model_DbTable_Buildings();
		$rows = $Buildings->fetchAll($Buildings->select()
			->from($Buildings->_name,'build_position')
			->where('build_town = ?',$this->town_id)
			->where('build_type = ?',$build_id));
		
		$rt = array();
		foreach ($rows as $row) {
			$rt[] = $row->build_position;
		}
		if (count($rt) == 1) return $rt[0];
		return $rt;
	}
	
	/**
	 * Get the level of the building built in town
	 * 
	 * @param int $build_id
	 * 	The ID of the building to be checked
	 * @return int
	 * 	0 if not built, the level of the building otherwise
	 */
	public function getBuildingLvl($build_id) {
		$Buildings = new Chamista_Model_DbTable_Buildings();
		$row = $Buildings->fetchRow($Buildings->select()
			->from($Buildings->_name,'build_lvl')
			->where('build_town = ?', $this->town_id)
			->where('build_type = ?',$build_id)
			->order('build_lvl DESC'));
		if ($row)
			return $row->build_lvl;
		else
			return 0;
	}
	
	/**
	 * Checks if the town has the available resources to build a building
	 * 
	 * @param array $cost
	 * 	The costs of the building
	 * @return boolean
	 * 	TRUE if enough resources, FALSE otherwise
	 */
	public function canBuild($cost) {
		$canBuild = true;
		if (isset($cost['wood'])) {
			if ($this->town_resource0 < $cost['wood']) {
				$canBuild = false;
			}
		}
		if (isset($cost['marble'])) {
			if ($this->town_resource2 < $cost['marble']) {
				$canBuild = false;
			}
		}
		return $canBuild;
	}
	
	/**
	 * Checks if the town can build the building
	 * 
	 * @param int $build_type
	 * 	The ID of the building to be checked
	 * @param int $lvl
	 * 	The level of the building to be built
	 * @return int
	 * 	-1 if not enough resources, -3 if there is another building being expanded, -2 if building has been built, TRUE if buildable
	 */
	public function canBuildBuilding($build_type,$lvl) {
		$User = new Chamista_Model_User($this->town_uid);
		$cost = Chamista_Model_Formula::getBuildCost($build_type,$lvl,$User);
		if (!$this->canBuild($cost)) {
			return -1;
		}
		if ($this->isExpanding()) {
			return -3;
		}
		// @TODO Check prerequisites
		if ($lvl == 1) {
			if ($this->isBuildingBuilt($build_type)) {
				return -2;
			}
		}
		return true;
	}
	
	/**
	 * Performs the town update
	 * 
	 * @return void
	 */
	public function updateTown() {
		// Check if owner is in vacation
		$Owner = new Chamista_Model_User($this->town_uid);
		if ($Owner->usr_vacation > 0) return;
		
		$now = time();
		$duration = $now - $this->town_update;
		if ($duration < 10) return;
		
		// Update Population
		$town_population = $this->town_population + $this->getHappiness() * SPEED * (1 - exp(-($duration / 3600)/50));
		if ($town_population > $this->getPopulationLimit()) {
			$town_population = $this->getPopulationLimit();
		}
		// If invalid town population
		if ($town_population < 0) $town_population = 0;
		
		// Update resources
		$Islands = new Chamista_Model_DbTable_Islands();
		$wood = $this->town_resource0 + $this->getWoodProduction() / 3600 * $duration;
		if ($wood > $this->getWarehouseCap()) $wood = $this->getWarehouseCap();
		$resource = $this->getIslandResource();
		$tradegood = $this->{'town_resource'.$resource} + $this->getResourceProduction() / 3600 * $duration;
		if ($tradegood > $this->getWarehouseCap()) $tradegood = $this->getWarehouseCap();
		
		// Update units queue
		$Units_Queue = new Chamista_Model_DbTable_Units_Queue();
		$Units = new Chamista_Model_DbTable_Towns_Units();
		$rows = $Units_Queue->fetchAll($Units_Queue->select()
			->where('queue_finish <= ?',$now)
			->where('queue_town = ?',$this->town_id)
		);
		foreach ($rows as $row) {
			$queue_data = unserialize($row->queue_data);
			foreach ($queue_data as $i => $v) {
				$Units->setArmy($i, $Units->getArmy($i ,$this->town_id) + intval($v), $this->town_id);
			}
			$where = array(
				$Units_Queue->getAdapter()->quoteInto('queue_town = ?',$row->queue_town),
				$Units_Queue->getAdapter()->quoteInto('queue_created = ?',$row->queue_created),
			);
			$Units_Queue->delete($where);
		}
		$Spies_Queue = new Chamista_Model_DbTable_Spies_Queue();
		$rows = $Spies_Queue->fetchAll($Spies_Queue->select()
			->where('town_id = ?',$this->town_id)
			->where('enddate <= ?',$now));
		foreach ($rows as $row) {
			$this->town_spies += $row->value;
			$where = array(
				$Spies_Queue->getAdapter()->quoteInto('town_id = ?',$this->town_id),
				$Spies_Queue->getAdapter()->quoteInto('enddate = ?',$row->enddate)
			);
			$Spies_Queue->delete($where);
		}
		
		$this->town_population = $town_population;
		$this->town_resource0 = $wood;
		$this->{'town_resource'.$resource} = $tradegood;
		$this->town_update = $now;
	}
	
}