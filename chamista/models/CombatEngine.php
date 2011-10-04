<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Combat engine model file.
 * This is the class file that houses the combat engine of Ikariem.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_CombatEngine {
	private $_data;
	private $_report;
	public $combatId;
	private $Attacker;
	private $Defender;
	
	public function __construct($combatId = 0) {
		$Combats = new Chamista_Model_DbTable_Combats();
		$Transports = new Chamista_Model_DbTable_Transports();
		$Barbarians = new Chamista_Model_DbTable_Barbarian();
		$Units = new Chamista_Model_DbTable_Units();
		
		$this->combatId = $combatId;
		
		$combatRow = $Combats->fetchRow($Combats->select()->where('combat_id = ?', $combatId));
		$data = unserialize($combatRow->combat_data);
		
		$Attacker = new Chamista_Model_Town($combatRow->combat_attacker);
		if ($combatRow->combat_defender > 0) {
			$Defender = new Chamista_Model_Town($combatRow->combat_defender);
		} else {
			$Defender = new Chamista_Model_Barbarian();
		}
		
		$this->Attacker = $Attacker;
		$this->Defender = $Defender;
		if (count($data) == 0) {
			// Battle has just been started, initialize everything
			// Get attacker army
			$transportRow = $Transports->fetchRow($Transports->select()->where('trans_id = ?', $combatRow->combat_trans));
			$army = (array) unserialize($transportRow->trans_armies);
			$t = array();
			foreach ($army as $i => $v) {
				$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $i));
				$t[] = array(
					'id' => $i,
					'value' => $v,
					'attack' => $unitRow->unit_primarydamage,
					'origValue' => $v,
				);
			}
			/*
			 * !!HACK!!
			 * The orderBy code will rewrite all the index, so we have to retain index this way
			 */
			$t = Chamista_Model_Format::orderBy($t, 'attack', 0);
			$attackerArmy = array();
			foreach ($t as $v) {
				$attackerArmy[$v['id']] = $v;
			}
			// @TODO Sea units
			
			// Get defender army
			if ($Defender->town_id == 0) {
				// Barbarian, use calculation instead
				$attacks = $Barbarians->getUserAttacked($Attacker->town_uid);
				$stats = Chamista_Model_Formula::getBarbarianStatus($attacks + 1);
				$defenderArmy = array(
					22 => array(
						'value' => $stats['barbarians'],
						'attack' => 7,
						'origValue' => $stats['barbarians'],
					),
				);
				$townSize = $stats['townsize'];
			} else {
				// @TODO Complete combat engine for non-barbarian
			}
			
			$this->_data = array(
				'attacker' => array(
					// All information about the attacker
					'id' => $Attacker->town_id,
					'army' => $attackerArmy,
					'morale' => 100,
				),
				'defender' => array(
					// All information about the defender
					'id' => $Defender->town_id,
					'army' => $defenderArmy,
					'morale' => 100,
				),
				'battleField' => array(
					// Information about battle field
					'size' => $townSize,
				),
				'round' => 0,
				'slots' => array(
					'attacker' => array(),
					'defender' => array(),
				),
			);
		} else {
			$this->_data = $data;
		}
	}
	
	/**
	 * 
	 * This function will initiate one round of land battle.
	 * 
	 */
	public function landBattle() {
		$this->loadLandSlots();
		
		$report = array();
		$data = $this->_data;		// This stores the data for the next round the
									// data in $this->_data will not be edited until
									// the end of this round
		
		$Units = new Chamista_Model_DbTable_Units();
		
		// Total battle sequence:
		// 1. Support units
		// 2. Bombers
		// 3. Artillery
		// 4. Long-range
		// 5. Frontline
		// 6. Flanks
		// 7. Air defense
		$attacking = array(
			'air' => array('artillery', 'long', 'front', 'light'),
			'artillery' => array('front', 'light'),
			'long' => array('front', 'light', 'long'),
			'front' => array('front', 'long', 'artillery', 'light'),
			'light' => array('light', 'long', 'artillery', 'front'),
			'airDef' => array('air', 'airDef'),
		);
		
		// Here is all the magic of the combat engine :)
		$attackerDamage = 0;
		$defenderDamage = 0;
		foreach ($attacking as $type => $sequence) {
			foreach ($this->_data['attacker']['slots'][$type] as $i => $unit) {
				if ($unit['value'] == 0) continue;
				$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $unit['id']));
				// Calculate total damage doable
				$totalDamage = $unit['value'] * $unitRow->unit_primarydamage;
				$attackerDamage += $totalDamage;
				$seq = 0;
				do {
					foreach ($this->_data['defender']['slots'][$sequence[$seq]] as $defI => $defUnit) {
						if ($totalDamage == 0) break;
						$hp = $data['defender']['slots'][$sequence[$seq]][$defI]['hp'];
						if ($hp == 0) continue;
						if ($hp < $totalDamage) {
							$data['defender']['slots'][$sequence[$seq]][$defI]['hp'] = 0;
							$totalDamage -= $hp;
						} else {
							$data['defender']['slots'][$sequence[$seq]][$defI]['hp'] -= $totalDamage;
							$totalDamage = 0;
						}
					}
					$seq++;
					if (!isset($sequence[$seq])) break;
				} while ($totalDamage > 0);
			}
			foreach ($this->_data['defender']['slots'][$type] as $i => $unit) {
				if ($unit['value'] == 0) continue;
				$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $unit['id']));
				// Calculate total damage doable
				$totalDamage = $unit['value'] * $unitRow->unit_primarydamage;
				$defenderDamage += $totalDamage;
				$seq = 0;
				do {
					foreach ($this->_data['attacker']['slots'][$sequence[$seq]] as $defI => $defUnit) {
						if ($totalDamage == 0) break;
						$hp = $data['attacker']['slots'][$sequence[$seq]][$defI]['hp'];
						if ($hp == 0) continue;
						if ($hp < $totalDamage) {
							$data['attacker']['slots'][$sequence[$seq]][$defI]['hp'] = 0;
							$totalDamage -= $hp;
						} else {
							$data['attacker']['slots'][$sequence[$seq]][$defI]['hp'] -= $totalDamage;
							$totalDamage = 0;
						}
					}
					$seq++;
					if (!isset($sequence[$seq])) break;
				} while ($totalDamage > 0);
			}
		}
		
		// Supporter units
		// @TODO
		
		// Calculate losses and etc
		$report['armies'] = array(
			'attacker' => array(),
			'defender' => array(),
		);
		$attackerLosses = 0;
		foreach ($data['attacker']['slots'] as $slots => $s) {
			foreach ($s as $i => $v) {
				if ($v['value'] == 0) continue;
				$percentDamage = round($v['hp'] / $this->_data['attacker']['slots'][$slots][$i]['hp'] * 100);
				$v['value'] = ceil($this->_data['attacker']['slots'][$slots][$i]['value'] * $percentDamage / 100);
				$loss = abs($this->_data['attacker']['slots'][$slots][$i]['value'] - $v['value']);
				$report['armies']['attacker'][$slots][$i] = array(
					'id' => $s[$i]['id'],
					'value' => $v['value'],
					'hp' => $percentDamage,
					'loss' => $loss,
				);
				$data['attacker']['slots'][$slots][$i]['value'] = $v['value'];
				$attackerLosses += $loss;
			}
		}
		$defenderLosses = 0;
		foreach ($data['defender']['slots'] as $slots => $s) {
			foreach ($s as $i => $v) {
				if ($v['value'] == 0) continue;
				$percentDamage = round($v['hp'] / $this->_data['defender']['slots'][$slots][$i]['hp'] * 100);
				$v['value'] = ceil($this->_data['defender']['slots'][$slots][$i]['value'] * $percentDamage / 100);
				$loss = abs($this->_data['defender']['slots'][$slots][$i]['value'] - $v['value']);
				$report['armies']['defender'][$slots][$i] = array(
					'id' => $s[$i]['id'],
					'value' => $v['value'],
					'hp' => $percentDamage,
					'loss' => $loss,
				);
				$data['defender']['slots'][$slots][$i]['value'] = $v['value'];
				$defenderLosses += $loss;
			}
		}
		$report['losses'] = array(
			'attacker' => $attackerLosses,
			'defender' => $defenderLosses,
		);
		
		// Sum up all army type losses
		$tempUnits = array();
		foreach ($data['attacker']['slots'] as $slots) {
			foreach ($slots as $slot) {
				if (!isset($slot['id'])) continue;
				if (isset($tempUnits[$slot['id']])) {
					$tempUnits[$slot['id']] += $slot['value'];
				} else {
					$tempUnits[$slot['id']] = $slot['value'];
				}
			}
		}
		foreach ($tempUnits as $type => $value) {
			$data['attacker']['army'][$type]['value'] = $value;
		}
		$tempUnits = array();
		foreach ($data['defender']['slots'] as $slots) {
			foreach ($slots as $slot) {
				if (!isset($slot['id'])) continue;
				if (isset($tempUnits[$slot['id']])) {
					$tempUnits[$slot['id']] += $slot['value'];
				} else {
					$tempUnits[$slot['id']] = $slot['value'];
				}
			}
		}
		foreach ($tempUnits as $type => $value) {
			$data['defender']['army'][$type]['value'] = $value;
		}
		
		/*
		 * Morale changes
		 */
		// Both sides were exhausted
		$data['attacker']['morale'] -= 10;
		$report['morale']['attacker'] = array(1);
		$data['defender']['morale'] -= 10;
		$report['morale']['defender'] = array(1);
		if ($attackerDamage > $defenderDamage) {
			$data['defender']['morale'] -= 5;
			$report['morale']['defender'][] = 2;
		} elseif ($attackerDamage < $defenderDamage) {
			$data['attacker']['morale'] -= 5;
			$report['morale']['attacker'][] = 2;
		}
		
		if ($attackerLosses > $defenderLosses) {
			$data['attacker']['morale'] -= 5;
			$report['morale']['attacker'][] = 3;
		} elseif ($attackerLosses < $defenderLosses) {
			$data['defender']['morale'] -= 5;
			$report['morale']['defender'][] = 3;
		}
		if ($data['defender']['id'] == 0) {
			$data['defender']['morale'] = 100;
		}
		
		// Build outcome of current battle round
		$outcome = array();
		$outcome['attacker'] = array();
		foreach ($this->_data['attacker']['army'] as $type => $army) {
			$outcome['attacker'][$type] = array(
				'value' => $data['attacker']['army'][$type]['value'],
				'loss' => $army['origValue'] - $data['attacker']['army'][$type]['value'],
			);
		}
		$outcome['defender'] = array();
		foreach ($this->_data['defender']['army'] as $type => $army) {
			$outcome['defender'][$type] = array(
				'value' => $data['defender']['army'][$type]['value'],
				'loss' => $army['origValue'] - $data['defender']['army'][$type]['value'],
			);
		}
	
		// Insert events
		$event = array();
		if ($this->_data['round'] == 0) {
			// Joined battle event
			$units = array();
			foreach ($this->_data['defender']['army'] as $id => $defenderArmy) {
				$units[] = array(
					'class' => Chamista_Model_Format::unit_sid($id),
					'amount' => $defenderArmy['value'],
				);
			}
			if ($this->_data['defender']['id'] != 0) {
				$Defender = new Chamista_Model_User($this->_data['defender']['id']);
				$nick = $Defender->usr_nick;
			} else {
				$nick = 'Barbarian';
			}
			$event[] = array(
				'person' => $nick,
				'type' => 1,
				'units' => $units,
			);
			$units = array();
			foreach ($this->_data['attacker']['army'] as $id => $defenderArmy) {
				$units[] = array(
					'class' => Chamista_Model_Format::unit_sid($id),
					'amount' => $defenderArmy['value'],
				);
			}
			$Attacker = new Chamista_Model_User($this->_data['attacker']['id']);
			$event[] = array(
				'person' => $Attacker->usr_nick,
				'type' => 2,
				'units' => $units,
			);
		}
		
		// Decide who's the winner
		$winner = false;
		$attackerArmySize = 0;
		$loot = array();
		foreach ($data['attacker']['army'] as $army) {
			$attackerArmySize += $army['value'];
		}
		$defenderArmySize = 0;
		foreach ($data['defender']['army'] as $army) {
			$defenderArmySize += $army['value'];
		}
		if ($attackerArmySize == 0 || $data['attacker']['morale'] == 0) {
			// Defender wins
			$winner = 1;
		} elseif ($defenderArmySize == 0 || $data['defender']['morale'] == 0) {
			// Attacker wins
			$winner = 2;
			$loot = $this->pillage();
		}
		
		// Save changes
		$data['round']++;
		$this->_data = $data;
		
		// Write to database
		$Combats = new Chamista_Model_DbTable_Combats();
		$update = array(
			'combat_data' => serialize($this->_data),
		);
		$where = $Combats->getAdapter()->quoteInto('combat_id = ?', $this->combatId);
		$Combats->update($update, $where);
		
		/*
		 * Generate battle report
		 */
		$Reports = new Chamista_Model_DbTable_Combats_Reports();
		$Rounds = new Chamista_Model_DbTable_Combats_Rounds();
		$Attacker = new Chamista_Model_Town($this->_data['attacker']['id']);
		if ($this->_data['defender']['id'] > 0)
			$Defender = new Chamista_Model_Town($this->_data['defender']['id']);
		else
			$Defender = new Chamista_Model_Barbarian();
			
		$outcome = serialize($outcome);
		if ($this->_data['round'] == 1) {
			$reportData = array(
				'fieldSize' => $this->_data['battleField']['size'],
			);
			
			// Create the parent report
			$insert = array(
				'report_date' => time(),
				'report_attacker' => $this->_data['attacker']['id'],
				'report_defender' => $this->_data['defender']['id'],
				'report_title' => 'Battle for '.$Defender->town_name,
				'report_winner' => 0,
				'report_anew' => 1,
				'report_dnew' => 1,
				'report_outcome' => $outcome,
				'report_data' => serialize($reportData),
			);
			
			if ($winner !== false) {
				$insert['report_winner'] = $winner;
				$insert['report_loots'] = serialize($loot);
			}
			
			$parent = $Reports->insert($insert);
			
			$insert = array(
				'round_parent' => $parent,
				'round_round' => $this->_data['round'],
				'round_time' => time(),
				'round_data' => serialize($report),
				'round_events' => serialize($event),
			);
			$Rounds->insert($insert);
		} else {
			// Find the parent battle report
			$reportRow = $Reports->fetchRow(
				$Reports->select()
					->where('report_attacker = ?', $this->_data['attacker']['id'])
					->where('report_defender = ?', $this->_data['defender']['id'])
			);
			$update = array(
				'report_date' => time(),
				'report_anew' => 1,
				'report_dnew' => 1,
				'report_outcome' => $outcome,
			);
			if ($winner !== false) {
				$update['report_winner'] = $winner;
				$update['report_loots'] = serialize($loot);
			}
			$where = $Reports->getAdapter()->quoteInto('report_id = ?', $reportRow->report_id);
			$Reports->update($update, $where);
			
			$insert = array(
				'round_parent' => $reportRow->report_id,
				'round_round' => $this->_data['round'],
				'round_time' => time(),
				'round_data' => serialize($report),
				'round_events' => serialize($event),
			);
			$Rounds->insert($insert);
		}
		if ($winner !== false) {
			$this->endLandBattle($loot, $winner);
		}
	}
	
	/**
	 * Bring back loot, adjust army size and send fleet back to port
	 */
	private function endLandBattle($loot, $winner) {
		$Combats = new Chamista_Model_DbTable_Combats();
		$Transports = new Chamista_Model_DbTable_Transports();
		$Barbarians = new Chamista_Model_DbTable_Barbarian();
		$transportId = $Combats->getTransportId($this->combatId);
		
		// Attacker end battle routine
		$row = $Transports->fetchRow($Transports->select()->where('trans_id = ?', $transportId));
		$returnTime = $row->trans_enddate - $row->trans_startdate;
		if (count($loot) > 0) {
			$cargo = array(
				'resource0' => $loot['wood'],
				'resource1' => $loot['wine'],
				'resource2' => $loot['marble'],
				'resource3' => $loot['crystal'],
				'resource4' => $loot['sulfur'],
			);
		} else {
			$cargo = array();
		}
		$armies = array();
		foreach ($this->_data['attacker']['army'] as $type => $army) {
			$armies[$type] = $army['value'];
		}
		$update = array(
			'trans_cargo' => serialize($cargo),
			'trans_armies' => serialize($armies),
			'trans_startdate' => time(),
			'trans_enddate' => time() + $returnTime,
			'trans_mission' => 7,
			'trans_pillage' => ($winner == 2) ? 1 : 0,
		);
		$where = $Transports->getAdapter()->quoteInto('trans_id = ?', $transportId);
		$Transports->update($update, $where);
		
		// Defender end battle routine
		if ($this->Defender->town_id == 0) {
			if ($winner == 2) {
				// Just update barbarian attack times
				$update = array(
					'barbarian_attacks' => $Barbarians->getUserAttacked($this->Attacker->town_uid) + 1,
				);
				$where = $Barbarians->getAdapter()->quoteInto('barbarian_user = ?', $this->Attacker->town_uid);
				$Barbarians->update($update, $where);
			}
		} else {
			
		}
		
		$Combats->deleteCombat($this->combatId);
	}
	
	/**
	 * Decides how many resources will be pillaged
	 */
	private function pillage() {
		$Transports = new Chamista_Model_DbTable_Transports();
		$Combats = new Chamista_Model_DbTable_Combats();
		if ($this->Defender->town_id == 0) {
			$Barbarians = new Chamista_Model_DbTable_Barbarian();
			
			$attacks = $Barbarians->getUserAttacked($this->Attacker->town_uid);
			$stats = Chamista_Model_Formula::getBarbarianStatus($attacks + 1);
			
			$loot = $stats['loots'];
		} else {
			
		}
		
		$combatRow = $Combats->fetchRow($Combats->select()->where('combat_id = ?', $this->combatId));
		$transportRow = $Transports->fetchRow($Transports->select()->where('trans_id = ?', $combatRow->combat_trans));
		
		$storage = $transportRow->trans_ships * 500;
		
		// Below algorithm will get the ratio of the 5 resources
		// First, get the smallest number, also get the total number of resources
		$smallest = 0;
		$total = 0;
		foreach ($loot as $v) {
			if ($smallest == 0 || $smallest > $v) $smallest = $v;
			$total += $v;
		}
		// If the attacker can accomodate all, just take all
		// @TODO Define transport time and limit
		if ($total <= $storage) return $loot;
		
		$woodRatio = $loot['wood'] / $smallest;
		$wineRatio = $loot['wine'] / $smallest;
		$marbleRatio = $loot['marble'] / $smallest;
		$crystalRatio = $loot['crystal'] / $smallest;
		$sulfurRatio = $loot['sulfur'] / $smallest;
		
		$totalRatio = $woodRatio + $wineRatio + $marbleRatio + $crystalRatio + $sulfurRatio;
		$loot['wood'] = floor($woodRatio / $totalRatio * $storage);
		$loot['wine'] = floor($wineRatio / $totalRatio * $storage);
		$loot['marble'] = floor($marbleRatio / $totalRatio * $storage);
		$loot['crystal'] = floor($crystalRatio / $totalRatio * $storage);
		$loot['sulfur'] = floor($sulfurRatio / $totalRatio * $storage);
		
		return $loot;
	}
	
	/**
	 * 
	 * This function will load both the attacker's armies and defender's armies into the battle field's battle slots.
	 * It will also move units from reserved slots if units have been damaged.
	 * 
	 */
	private function loadLandSlots() {
		$Units = new Chamista_Model_DbTable_Units();
		
		$slots = Chamista_Model_Formula::getBattleFieldInfo($this->_data['battleField']['size']);
		
		/*
		 * Initialize attacker's slots
		 */
		$attackerSlots = array();
		$attackerReserveSlots = array();
		foreach ($slots as $i => $v) {
			$attackerSlots[$i] = array();
			for ($j=1;$j<=$v;$j++) {
				$attackerSlots[$i][] = array('value' => 0, 'hp' => 0);
			}
		}
		foreach ($this->_data['attacker']['army'] as $id => $value) {
			$id = (int) $id;
			$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $id));
			$fitSlot = 'front';		// Check which slot to fit into
			$backSlot = 'reserve';	// Backup slot in case the first fit is full or unavailable
			switch ($unitRow->unit_primaryclass) {
				case 1:
					$fitSlot = 'front';
					break;
				case 5:
					$fitSlot = 'long';
					break;
				case 4:
					$fitSlot = 'light';
					$backSlot = 'front';
					break;
				case 6:
					$fitSlot = 'artillery';
					break;
				case 7:
					$fitSlot = 'airDef';
					break;
				case 8:
					$fitSlot = 'air';
					break;
				case 9:
					if ($unitRow->unit_id == 13) {
						// Cook
						$fitSlot = 'cooks';
					} elseif ($unitRow->unit_id == 14) {
						// Doctor
						$fitSlot = 'doctors';
					}
					break;
			}
			
			$value = $value['value'];
			// Put units into slots
			if (count($attackerSlots[$fitSlot]) > 0) {
				foreach ($attackerSlots[$fitSlot] as $i => $j) {
					if ($j['value'] > 0) continue;
					$maxFit = 30;
					if ($fitSlot == 'airDef' || $fitSlot == 'air') $maxFit = $this->_data['battleField']['size'] * 10;
					if ($fitSlot == 'cooks' || $fitSlot == 'doctors') {
						// Unlimited number of units placable here, just put a number high enough the user can't reach
						$maxFit = pow(99,99);		// Not enough? Just increase it!
					}
					if ($value >= $maxFit) {
						$attackerSlots[$fitSlot][$i] = array('value' => $maxFit, 'hp' => $unitRow->unit_hp * $maxFit, 'id' => $unitRow->unit_id);
						$value -= $maxFit;
					} else {
						$attackerSlots[$fitSlot][$i] = array('value' => $value, 'hp' => $unitRow->unit_hp * $value, 'id' => $unitRow->unit_id);
						$value = 0;
					}
				}
			}
			if ($value > 0) {
				// Put into backup slot
				if ($backSlot != 'reserve') {
					foreach ($attackerSlots[$backSlot] as $i => $v) {
						if ($v['value'] > 0) continue;
						$maxFit = 30;
						if ($backSlot == 'airDef' || $backSlot == 'air') $maxFit = $this->_data['battleField']['size'] * 10;
						if ($backSlot == 'cooks' || $backSlot == 'doctors') {
							// Unlimited number of units placable here, just put a number high enough the user can't reach
							$maxFit = pow(99,99);		// Not enough? Just increase it!
						}
						if ($value >= $maxFit) {
							$attackerSlots[$backSlot][$i] = array('value' => $maxFit, 'hp' => $unitRow->unit_hp * $maxFit, 'id' => $unitRow->unit_id);
							$value -= $maxFit;
						} else {
							$attackerSlots[$backSlot][$i] = array('value' => $value, 'hp' => $unitRow->unit_hp * $value, 'id' => $unitRow->unit_id);
							$value = 0;
						}
					}
					if ($value > 0) {
						$attackerReserveSlots[$id] = $value;
					}
				} else {
					$attackerReserveSlots[$id] = $value;
				}
			}
		}
		$this->_data['attacker']['slots'] = $attackerSlots;
		$this->_data['attacker']['reserveSlots'] = $attackerReserveSlots;
		
		/*
		 * Initialize defender's slots
		 */
		$defenderSlots = array();
		$defenderReserveSlots = array();
		foreach ($slots as $i => $v) {
			$defenderSlots[$i] = array();
			for ($j=1;$j<=$v;$j++) {
				$defenderSlots[$i][] = array('value' => 0, 'hp' => 0);
			}
		}
		if ($this->_data['round'] == 0) {
			// Fill up front line with walls before battle
			if ($this->_data['defender']['id']) {
				$Defender = new Chamista_Model_Town($this->_data['defender']['id']);
				$wallLvl = $Defender->getBuildingLvl(Chamista_Model_Formula::TOWN_WALL);
			} else {
				$Barbarian = new Chamista_Model_DbTable_Barbarian();
				$Attacker = new Chamista_Model_Town($this->_data['attacker']['id']);
				$attacks = $Barbarian->getUserAttacked($Attacker->town_uid);
				$barbarianInfo = Chamista_Model_Formula::getBarbarianStatus($attacks);
				$wallLvl = $barbarianInfo['wall'];
			}
			$wallInfo = Chamista_Model_Formula::getWallStatus($wallLvl);
			foreach ($defenderSlots['front'] as $i => $v) {
				$defenderSlots['front'][$i] = array('value' => 1, 'hp' => $wallInfo['hitpoints'], 'id' => -1, 'lvl' => $wallLvl);
			}
		}
		foreach ($this->_data['defender']['army'] as $id => $value) {
			$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $id));
			$fitSlot = 'front';		// Check which slot to fit into
			$backSlot = 'reserve';	// Backup slot in case the first fit is full or unavailable
			switch ($unitRow->unit_primaryclass) {
				case 2:
					$fitSlot = 'front';
					break;
				case 5:
					$fitSlot = 'long';
					break;
				case 4:
					$fitSlot = 'light';
					$backSlot = 'front';
					break;
				case 6:
					$fitSlot = 'artillery';
					break;
				case 7:
					$fitSlot = 'airDef';
					break;
				case 8:
					$fitSlot = 'air';
					break;
				case 9:
					if ($unitRow->unit_id == 13) {
						// Cook
						$fitSlot = 'cooks';
					} elseif ($unitRow->unit_id == 14) {
						// Doctor
						$fitSlot = 'doctors';
					}
					break;
			}
			
			$value = $value['value'];
			// Put units into slots
			if (count($defenderSlots[$fitSlot]) > 0) {
				foreach ($defenderSlots[$fitSlot] as $i => $j) {
					if ($v['value'] > 0) continue;
					$maxFit = 30;
					if ($fitSlot == 'airDef' || $fitSlot == 'air') $maxFit = $this->_data['battleField']['size'] * 10;
					if ($fitSlot == 'cooks' || $fitSlot == 'doctors') {
						// Unlimited number of units placable here, just put a number high enough the user can't reach
						$maxFit = pow(99,99);		// Not enough? Just increase it!
					}
					if ($value >= $maxFit) {
						$defenderSlots[$fitSlot][$i] = array('value' => $maxFit, 'hp' => $unitRow->unit_hp * $maxFit, 'id' => $unitRow->unit_id);
						$value -= $maxFit;
					} else {
						$defenderSlots[$fitSlot][$i] = array('value' => $value, 'hp' => $unitRow->unit_hp * $value, 'id' => $unitRow->unit_id);
						$value = 0;
					}
				}
			}
			if ($value > 0) {
				// Put into backup slot
				if ($backSlot != 'reserve') {
					foreach ($defenderSlots[$backSlot] as $i => $v) {
						if ($v['value'] > 0) continue;
						$maxFit = 30;
						if ($backSlot == 'airDef' || $backSlot == 'air') $maxFit = $this->_data['battleField']['size'] * 10;
						if ($backSlot == 'cooks' || $backSlot == 'doctors') {
							// Unlimited number of units placable here, just put a number high enough the user can't reach
							$maxFit = pow(99,99);		// Not enough? Just increase it!
						}
						if ($value >= $maxFit) {
							$defenderSlots[$backSlot][$i] = array('value' => $maxFit, 'hp' => $unitRow->unit_hp * $maxFit, 'id' => $unitRow->unit_id);
							$value -= $maxFit;
						} else {
							$defenderSlots[$backSlot][$i] = array('value' => $value, 'hp' => $unitRow->unit_hp * $value, 'id' => $unitRow->unit_id);
							$value = 0;
						}
					}
					if ($value > 0) {
						$defenderReserveSlots[$id] = $value;
					}
				} else {
					$defenderReserveSlots[$id] = $value;
				}
			}
		}
		$this->_data['defender']['slots'] = $defenderSlots;
		$this->_data['defender']['reserveSlots'] = $defenderReserveSlots;
	}
}