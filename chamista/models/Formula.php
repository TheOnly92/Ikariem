<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Formula model file.
 * Here is where all the formulas in the game lies.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_Formula {
	/**
	 * The basic storage a town hall has
	 * 
	 * @var int
	 */
	const TOWN_HALL_STORAGE = 1500;
	/**
	 * The basic happiness a town hall can provide
	 * @var int
	 */
	const BASIC_HAPPINESS = 196;
	
	// Buildings
	const TOWN_HALL = 1;
	const TRADING_PORT = 2;
	const SHIPYARD = 3;
	const ACADEMY = 4;
	const WAREHOUSE = 5;
	const BARRACKS = 6;
	const TOWN_WALL = 7;
	const CARPENTER = 8;
	const TAVERN = 9;
	const HIDEOUT = 10;
	const TRADING_POST = 11;
	const PALACE = 12;
	const GOVNR_RESIDENCE = 13;
	const FORESTER = 14;
	const WINEGROWER = 15;
	const STONEMASON = 16;
	const GLASSBLOWING = 17;
	const ALCHEMIST = 18;
	
	// Technologies
	const SEAFARING = 1;
	const ECONOMY = 2;
	const SCIENCE = 3;
	const MILITARY = 4;
	
	// Battlefield size
	const VILLAGE = 1;
	const TOWN = 2;
	const METROPOLIS = 3;
	
	/**
	 * Forumla for warehouse storage
	 * 
	 * @param int $lvl
	 * 	Level of the warehouse
	 * @return int
	 * 	Warehouse storage for the specific level
	 */
	public function getWarehouseStorage($lvl) {
		return 8000 + 8000 * ($lvl - 1);
	}
	
	/**
	 * Formula for the number of safe goods a warehouse could protect
	 * 
	 * @param int $lvl
	 * 	Level of the warehouse
	 * @return int
	 * 	Safe goods protection for the specific level
	 */
	public function getWarehouseSafe($lvl) {
		return 180 + 180 * ($lvl - 1);
	}
	
	/**
	 * Formula for the population limit of the town hall
	 * 
	 * @param int $lvl
	 * 	Level of the town hall
	 * @return int
	 * 	Population limit for the specific level
	 */
	public function getPopulationLimit($lvl) {
		return floor(402 * pow(1.1, $lvl) - 391);
	}
	
	/**
	 * Formula for the maximum scientist an academy could accomodate
	 * 
	 * @param int $lvl
	 * 	Level of the academy
	 * @return int
	 * 	Maxmimum number of scientists for the specific level
	 */
	public function getMaxScientist($lvl) {
		return floor(138 * pow(1.05, $lvl) + -140);
	}
	
	/**
	 * Gets the building cost for the specific user of the specific building on the specific level
	 * This function calculates discounts by research and carpenters
	 * 
	 * @param int $type
	 * 	The building type ID
	 * @param int $lvl
	 * 	The level of the building
	 * @param object $User
	 * 	The $User object for which the building cost is for
	 * @return array
	 * 	Building cost of the building
	 */
	public function getBuildCost($type,$lvl,$User) {
		$rt = self::calcBuildCost($type,$lvl);
		$researchDiscount = self::getResearchDiscount($User,1);
		foreach ($rt as $i => $v) {
			$rt[$i] *= (1 - $researchDiscount);
		}
		$Town = new Chamista_Model_Town(Zend_Registry::get('session')->current_city);
		if (isset($rt['wood']))
			$rt['wood'] *= (1 - self::carpenterDiscount($Town->getBuildingLvl(self::CARPENTER)));
		return $rt;
	}
	
	/**
	 * Formulas for the building cost of the specific level
	 * 
	 * @param int $type
	 * 	The building type ID
	 * @param int $lvl
	 * 	The level of the building
	 * @return array
	 * 	Building cost of the building
	 */
	public function calcBuildCost($type,$lvl) {
		$rt = array();
		switch ($type) {
			case self::TOWN_HALL:
				$rt['wood'] = floor(490 * pow(1.27, $lvl) - 652);
				if ($lvl > 4) {
					$rt['marble'] = floor(638 * pow(1.33, $lvl - 4) - 568);
				}
				break;
			case self::TRADING_PORT:
				$rt['wood'] = floor(324 * pow(1.25, $lvl) - 359);
				if ($lvl > 5) {
					$rt['marble'] = floor(401 * pow(1.29887) - 346);
				}
				break;
			case self::ACADEMY:
				$rt['wood'] = floor(-240 + 250 * pow(1.22, $lvl));
				if ($lvl > 4) {
					$rt['crystal'] = floor(420 * pow(1.37, $lvl - 4) - 359);
				}
				break;
			case self::WAREHOUSE:
				$rt['wood'] = floor(533 * pow(1.20, $lvl) - 480);
				if ($lvl > 3) {
					$rt['marble'] = floor(477 * pow(1.2, $lvl - 3) - 477);
				}
				break;
			case self::BARRACKS:
				$rt['wood'] = floor(219 * pow(1.24, $lvl) - 222);
				if ($lvl > 8) {
					$rt['marble'] = floor(853 * pow(1.24, $lvl - 8) - 880);
				}
				break;
			case self::TOWN_WALL:
				$rt['wood'] = floor(1026 * pow(1.2, $lvl) - 1117);
				if ($lvl > 1) {
					$rt['marble'] = floor(1305 * pow(1.2, $lvl - 1) - 1363);
				}
				break;
			case self::SHIPYARD:
				$rt['wood'] = floor(295 * pow(1.26, $lvl) - 267);
				if ($lvl > 5) {
					$rt['marble'] = floor(832 * pow(1.26, $lvl - 5) - 271);
				}
				break;
			case self::CARPENTER:
				$rt['wood'] = floor(263 * pow(1.19, $lvl) - 250);
				if ($lvl > 7) {
					$rt['marble'] = floor(353 * pow(1.2, $lvl - 7) - 65);
				}
				break;
			case self::HIDEOUT:
				$rt['wood'] = floor(831 * pow(1.14, $lvl) - 837);
				if ($lvl > 3) {
					$rt['marble'] = floor(371 * pow(1.16, $lvl - 3) - 300);
				}
				break;
			case self::TRADING_POST:
				$rt['wood'] = floor(263 * pow(1.36, $lvl) - 309);
				break;
			case self::TAVERN:
				$rt['wood'] = round(503 * pow(1.20, $lvl) - 503);
				break;
			case self::GOVNR_RESIDENCE:
			case self::PALACE:
				$rt['wood'] = round(2555 * pow(2, $lvl) - 4400);
				if ($lvl > 1) {
					$rt['marble'] = round(1556 * pow(1.9999,$lvl - 1) - 1679);
				}
				if ($lvl > 2) {
					$rt['sulfur'] = round(3606 * pow(2, $lvl - 2) - 4125);
				}
				if ($lvl > 3) {
					$rt['wine'] = round(5606 * pow(2,$lvl - 3) - 314);
				}
				if ($lvl > 4) {
					$rt['crystal'] = round(10606 * pow(2, $lvl - 4) - 24);
				}
				break;
			case self::FORESTER:
				$rt['wood'] = round(461 * pow(1.3, $lvl) - 350);
				if ($lvl > 1) {
					$rt['marble'] = round(339 * pow(1.3, $lvl - 1) - 338);
				}
				break;
			case self::ALCHEMIST:
			case self::GLASSBLOWING:
			case self::STONEMASON:
			case self::WINEGROWER:
				$rt['wood'] = round(495 * pow(1.3, $lvl) - 370);
				if ($lvl > 1) {
					$rt['marble'] = round(355 * pow(1.3, $lvl - 1) - 346);
				}
				break;
		}
		return $rt;
	}
	
	/**
	 * Formulas for the building time of the buildings
	 * 
	 * @param int $type
	 * 	The building type ID
	 * @param int $lvl
	 * 	The level of the building
	 * @param boolean $formatted
	 * 	If TRUE, the returned value is a formatted array, FALSE will be the number of seconds
	 * @return mixed
	 * 	Depends on $formatted
	 */
	public function getBuildTime($type, $lvl, $formatted = false) {
		$time = 0;
		switch ($type) {
			case self::TOWN_HALL:
				$time = floor(1843 * pow(1.16416, $lvl) - 1050);
				break;
			case self::TRADING_PORT:
				$time = floor(2667 * pow(1.13056, $lvl) - 2029);
				break;
			case self::ACADEMY:
				$time = floor(1717364 * pow(1.00036,$lvl) - 1717463);
				break;
			case self::WAREHOUSE:
				$time = floor(22336 * pow(1.03,$lvl) - 22263);
				break;
			case self::BARRACKS:
				$time = floor(51512 * pow(1.007, $lvl) - 51328);
				break;
			case self::TOWN_WALL:
				//$time = floor(15 * pow($lvl, 3) - 230 * pow($lvl, 2) + 1858 * $lvl - 195);
				$time = floor(2289 * exp(0.16 * $lvl) - 2482 * exp(-2.8 * $lvl));
				break;
			case self::SHIPYARD:
				$time = floor(665 * pow(1.3, $lvl) + 2020);
				break;
			case self::CARPENTER:
				$time = floor(3410 * pow(1.06, $lvl) - 2822);
				break;
			case self::HIDEOUT:
				$time = floor(15503 * pow(1.04, $lvl) - 14743);
				break;
			case self::TRADING_POST:
				$time = floor(8684 * pow(1.11, $lvl) - 8189);
				break;
			case self::TAVERN:
				$time = round(10786 * pow(1.06, $lvl) - 10426);
				break;
			case self::GOVNR_RESIDENCE:
			case self::PALACE:
				$time = round(11516 * pow(1.4, $lvl) - 4.25);
				break;
			case self::ALCHEMIST:
			case self::GLASSBLOWING:
			case self::STONEMASON:
			case self::WINEGROWER:
			case self::FORESTER:
				$time = round(6555 * pow(1.09, $lvl) - 6130);
				break;
		}
		$time /= SPEED;
		$time = round($time);
		if (!$formatted) {
			return $time;
		} else {
			return Chamista_Model_Format::formatTimeToArray($time);
		}
	}
	
	/**
	 * Formula for the upgrade cost of a saw mill at the specific level
	 * 
	 * @param int $lvl
	 * 	The level of the saw mill
	 * @return int
	 * 	The amount of wood required to upgrade a saw mill
	 */
	public function sawUpgradeCost($lvl) {
		$rt['wood'] = floor(760.69 * pow(1.36, $lvl) - 943);
		return $rt;
	}
	
	/**
	 * Formula for the amount of time needed for a saw mill to upgrade at a specific level
	 * 
	 * @param int $lvl
	 * 	The level of the saw mill
	 * @return int
	 * 	The amount of time
	 */
	public function sawUpgradeTime($lvl) {
		return floor((8055 * pow(1.09, $lvl) - 8079) / SPEED) ;
	}
	
	/**
	 * Formula for the maximum number of lumber jackers a saw mill is able to accomodate at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the saw mill
	 * @return int
	 * 	The number of lumber jackers
	 */
	public function sawMaxWorkers($lvl) {
		return round(34 * pow(1.22, $lvl) - 12);
	}
	
	/**
	 * Formula for the upgrade cost of a tradegood mine at a specific level
	 * 
	 * @param int $lvl
	 * 	The level of the tradegood mine
	 * @return int
	 * 	The amount of wood required to upgrade a tradegood mine
	 */
	public function tradeUpgradeCost($lvl) {
		$rt['wood'] = floor(3554 * pow(1.27, $lvl) - 4480);
		return $rt;
	}
	
	/**
	 * Formula for the amount of time required in order to upgrade a tradegood mine at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the tradegood mine
	 * @return int
	 * 	The amount of time required to upgrade a tradegood mine
	 */
	public function tradeUpgradeTime($lvl) {
		return floor((13200 * pow(1.107, $lvl) - 13143) / SPEED);
	}
	
	/**
	 * Formula for the maximum number of tradegood worker a tradegood mine is able to accomodate at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the tradegood mine
	 * @return int
	 * 	The number of tradegood worker
	 */
	public function tradeMaxWorkers($lvl) {
		return floor(52 * pow(1.20, $lvl) - 43);
	}
	
	/**
	 * The amount of gold required to buy a trade ship after $ships number of ships
	 * This information is provided by http://ikariam.wikia.com/wiki/Main_Page
	 * 
	 * @param int $ships
	 * 	The number of ships the user currently possess
	 * @return int
	 * 	The number of gold required to buy the ship
	 */
	public function getShipsCost($ships) {
		return floor(-13425 + 13500 * pow(1.03, $ships));
	}
	
	/**
	 * The loading speed of the trading port at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the trading port
	 * @return int
	 * 	The number of goods loadable per minute
	 */
	public function getLoadingSpeed($lvl) {
		return round(273.359 * pow(1.09964, $lvl) - 270.567) * SPEED;
	}
	
	/**
	 * Returns the type of a tech field specified by $id
	 * 
	 * @param int $id
	 * 	The ID of the tech field
	 * @return string
	 * 	The type of tech field in name
	 */
	public function tech_type($id) {
		switch ($id) {
			case self::SEAFARING:
				return 'seafaring';
			case self::ECONOMY:
				return 'economy';
			case self::SCIENCE:
				return 'science';
			case self::MILITARY:
				return 'military';
		}
	}
	
	/**
	 * Returns the name of a tech field specified by $id
	 * 
	 * @param int $id
	 * 	The ID of the tech field
	 * @return string
	 * 	The name of the tech field
	 */
	public function tech_type_name($id) {
		switch ($id) {
			case self::SEAFARING:
				return 'Seafaring';
			case self::ECONOMY:
				return 'Economy';
			case self::SCIENCE:
				return 'Science';
			case self::MILITARY:
				return 'Military';
		}
	}
	
	/**
	 * Returns the ID of a tech field specified by $type
	 * 
	 * @param string $type
	 * 	The type of the tech field
	 * @return int
	 * 	The ID of the tech field
	 */
	public function techTypeFromId($type) {
		switch ($type) {
			case 'seafaring':
				return self::SEAFARING;
			case 'economy':
				return self::ECONOMY;
			case 'science':
				return self::SCIENCE;
			case 'military':
				return self::MILITARY;
			default:
				return 0;
		}
	}
	
	/**
	 * Get the amount of time required to train a unit at a certain barracks level
	 * 
	 * @param int $unit_id
	 * 	The ID of the unit
	 * @param int $lvl
	 * 	The level of the barracks
	 * @return int
	 * 	The amount of time required to train
	 */
	public function getUnitTraining($unit_id,$lvl) {
		$Units = new Chamista_Model_DbTable_Units();
		$row = $Units->fetchRow($Units->select()->from($Units->_name,array('unit_training','unit_requirement'))->where('unit_id = ?',$unit_id)->limit(1));
		$rt = $row->unit_training;
		$rt = round($row->unit_training * pow(0.95,($lvl - $row->unit_requirement)));
		$rt /= SPEED;
		return $rt;
	}
	
	/**
	 * Gets the status of a wall at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the wall
	 * @return array
	 * 	The information about the wall
	 */
	public function getWallStatus($lvl) {
		$info = array(
			'hitpoints' => 150,
			'armor' => 15,
			'combat' => 'Ballistae',
			'damage' => 12,
			'accuracy' => 0.3,
		);
		$info['hitpoints'] += 50 * ($lvl - 1);
		$info['armor'] += 8 * floor(($lvl - 1) / 2);
		$info['damage'] += 2 * ($lvl - 1);
		if ($lvl >= 10) {
			$info['combat'] = 'Catapults';
			$info['accuracy'] = 0.5;
		} elseif ($lvl >= 20) {
			$info['combat'] = 'Bombs';
			$info['accuracy'] = 0.8;
		}
		
		return $info;
	}
	
	/**
	 * The amount of resources to be returned during the demolition of a building at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the building
	 * @return int
	 * 	The percentage of resources to be returned
	 */
	public function demolitionReturn($lvl) {
		return round(0.66 / $lvl + 0.28,2);
	}
	
	/**
	 * Retrieves the servers for users to login
	 * 
	 * @return array
	 * 	The servers for login
	 */
	public function getServers() {
		$servers = array();
		switch (ENV) {
			case 'staging':
				$servers['ikariem.localhost'] = 'Rewaz Server';
				break;
			case 'development':
				$servers[$_SERVER['HTTP_HOST']] = 'Rewaz Server';
				break;
			case 'test':
				$servers['test.ikariem.org'] = 'Test Server';
				break;
		}
		return $servers;
	}
	
	/**
	 * The amount of discount a carpenter provides at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the carpenter
	 * @return float
	 * 	The percentage of the discount
	 */
	public function carpenterDiscount($lvl) {
		return $lvl / 100;
	}
	
	/**
	 * The amount of discount a user has obtained from research for building costs
	 * 
	 * @param object $User
	 * 	The Chamista_Model_User object
	 * @param int $building
	 * 	Determines if it is a building
	 * @return float
	 * 	The amount of discount
	 */
	public function getResearchDiscount($User,$building = 0) {
		$discount = 0;
		if ($building == 1) {
			if ($User->userResearched(6)) {
				$discount += 0.02;
			}
			if ($User->userResearched(22)) {
				$discount += 0.04;
			}
		}
		return $discount;
	}
	
	/**
	 * The amount of discount a user has obtained from research for unit training costs
	 * 
	 * @param object $User
	 * 	The Chamista_Model_User object
	 * @return float
	 * 	The amount of discount
	 */
	public function getTroopsDiscount($User) {
		$discount = 0;
		if ($User->userResearched(8)) {
			$discount += 0.02;
		}
		if ($User->userResearched(20)) {
			$discount += 0.04;
		}
		return $discount;
	}
	
	/**
	 * The amount of discount a user has obtained from research for ship building costs
	 * 
	 * @param object $User
	 * 	The Chamista_Model_User object
	 * @return float
	 * 	The amount of discount
	 */
	public function getFleetsDiscount($User) {
		$discount = 0;
		if ($User->userResearched(9)) {
			$discount += 0.02;
		}
		return $discount;
	}
	
	/**
	 * Gets the amount of wine a tavern can serve at a certain level
	 * 
	 * @param int $lvl
	 * 	The level of the tavern
	 * @return int
	 * 	The maximum amount of wine
	 */
	public function getTavernWine($lvl) {
		return round(31 * pow(1.121, $lvl) - 30) * SPEED;
	}
	
	/**
	 * Gets the amount of time a spy training is required at a certain level of hideout
	 * 
	 * @param int $lvl
	 * 	The level of the hideout
	 * @return int
	 * 	The amount of time
	 */
	public function trainSpyTime($lvl) {
		return round(($lvl / (-5.63E-06 + 1.12E-03 * $lvl + 5.31E-05 * pow($lvl,2) + 2.05E-06 * pow($lvl, 3))) / SPEED);
	}
	
	/**
	 * Able to return the radius of search and the capacity of a trading post at a certain level
	 * 
	 * @param string $property
	 * 	'search' for radius of search, 'capacity' for trading post capacity
	 * @param int $lvl
	 * 	The level of the trading post
	 * @return mixed
	 * 	The values specified
	 */
	public function tradingPost($property,$lvl) {
		switch ($property) {
			case 'search':
				return ceil($lvl/2);
			case 'capacity':
				return round(2509.6 * pow(1.39388, $lvl) - 3169.77);
		}
	}
	
	/**
	 * Gets the travel time from an island to another
	 * 
	 * @param int $speed
	 * 	The speed of the travelling object
	 * @param array $pos1
	 * 	The origin
	 * @param array $pos2
	 * 	The destination
	 * @return int
	 * 	The amount of time required to travel
	 */
	public function travelTime($speed,$pos1,$pos2) {
		if ($pos1['x'] == $pos2['x'] && $pos1['y'] == $pos2['y']) {
			$distance = 0.5;
		} else {
			$distance = sqrt(pow($pos2['x'] - $pos1['x'],2) + pow($pos2['y'] - $pos1['y'],2));
		}
		return round(((1200 / $speed * $distance) * 60) / SPEED);
	}
	
	/**
	 * The amount of action points available at a certain level of town hall
	 * 
	 * @param int $lvl
	 * 	The level of town hall
	 * @return int
	 * 	The maximum number of action points
	 */
	public function getActionPoints($lvl) {
		return floor($lvl / 4) + 3;
	}
	
	/**
	 * Get the risk of getting caught of a mission a spy will perform
	 * 
	 * @param int $mission
	 * 	The type of mission
	 * @param Chamista_Model_Town $Source
	 * 	The class of the source town
	 * @param Chamista_Model_Town $Destination
	 * 	The class of the destination town
	 * @return int
	 * 	The risk in 100, must divide by 100 to convert to decimal
	 */
	public function getRisk($mission, Chamista_Model_Town $Source, Chamista_Model_Town $Destination) {
		$riskBase = 0;
		switch ($mission) {
			case 1:
				$riskBase = 5;
				break;
			case 2:
				$riskBase = 24;
				break;
			case 3:
			case 4:
				$riskBase = 30;
				break;
			case 5:
				$riskBase = 40;
				break;
			case 6:
				$riskBase = 50;
				break;
			case 7:
				$riskBase = 70;
				break;
			case 8:
				$riskBase = 80;
				break;
			case 9:
				$riskBase = 90;
				break;
		}
		// @TODO get current risk
		$currentRisk = 0;
		$risk = $currentRisk + $riskBase + $Destination->getAvailSpies() * 5 + $Destination->getBuildingLvl(self::HIDEOUT) * 2 - $Destination->town_lvl * 2 - $Source->getBuildingLvl(self::HIDEOUT) * 2;
		if ($risk < 5) $risk = 5;
		if ($risk > 95) $risk = 95;
		return $risk;
	}
	
	/**
	 * Gets the status of the barbarian village after $attacks
	 * 
	 * @param int $attacks
	 * 	Number of attacks made by user
	 * @return array
	 * 	The status of the barbarians
	 */
	public function getBarbarianStatus($attacks) {
		// Hacks for wall level
		$wall = 0;
		if ($attacks > 3) $wall = 1;
		if ($attacks == 6) $wall = 2;
		if ($attacks == 7) $wall = 4;
		if ($attacks == 8) $wall = 5;
		if ($attacks == 9) $wall = 6;
		if ($attacks == 10) $wall = 7;
		if ($attacks == 11) $wall = 8;
		if ($attacks == 12) $wall = 10;
		if ($attacks == 13) $wall = 12;
		if ($attacks == 14) $wall = 14;
		if ($attacks == 15) $wall = 16;
		
		// Loots
		$loots = array();
		$loots['wood'] = 250;
		if ($attacks > 5) $loots['wood'] = 500;
		if ($attacks > 10) $loots['wood'] = 1000;
		if ($attacks > 1) {
			$loots['sulfur'] = 250;
			if ($attacks > 6) $loots['sulfur'] = 500;
			if ($attacks > 11) $loots['sulfur'] = 1000;
		}
		if ($attacks > 2) {
			$loots['marble'] = 250;
			if ($attacks > 7) $loots['marble'] = 500;
			if ($attacks > 12) $loots['marble'] = 1000;
		}
		if ($attacks > 3) {
			$loots['wine'] = 250;
			if ($attacks > 8) $loots['wine'] = 500;
			if ($attacks > 13) $loots['wine'] = 1000;
		}
		if ($attacks > 4) {
			$loots['crystal'] = 250;
			if ($attacks > 9) $loots['crystal'] = 500;
			if ($attacks > 14) $loots['crystal'] = 1000;
		}
		
		// Town size
		$town = self::VILLAGE;
		if ($attacks > 8) $town = self::TOWN;
		if ($attacks > 12) $town = self::METROPOLIS; 
		
		return array(
			'barbarians' => round(2.6 - 4.16 * $attacks + 2.64 * pow($attacks, 2)),
			'wall' => $wall,
			'loots' => $loots,
			'townsize' => $town,
		);
	}
	
	/**
	 * Gets the information about the specified battle field
	 * 
	 * @param int $battlefield
	 * 	The type of battle field
	 * @return array
	 * 	The information about the battle field
	 */
	public function getBattleFieldInfo($battlefield) {
		$rt = array();
		switch ($battlefield) {
			case self::VILLAGE:
				$rt['front'] = 3;
				$rt['long'] = 3;
				$rt['artillery'] = 1;
				$rt['light'] = 0;
				$rt['airDef'] = 1;
				$rt['air'] = 1;
				$rt['cooks'] = 1;
				$rt['doctors'] = 1;
				break;
			case self::TOWN:
				$rt['front'] = 5;
				$rt['long'] = 5;
				$rt['artillery'] = 2;
				$rt['light'] = 2;
				$rt['airDef'] = 1;
				$rt['air'] = 1;
				$rt['cooks'] = 1;
				$rt['doctors'] = 1;
				break;
			case self::METROPOLIS:
				$rt['front'] = 7;
				$rt['long'] = 7;
				$rt['artillery'] = 3;
				$rt['light'] = 4;
				$rt['airDef'] = 1;
				$rt['air'] = 1;
				$rt['cooks'] = 1;
				$rt['doctors'] = 1;
				break;
		}
		
		return $rt;
	}
}