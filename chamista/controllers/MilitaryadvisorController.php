<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Military advisor controller file.
 * Handle all actions redirected to the militaryAdvisor controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class MilitaryadvisorController  extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$Units = new Chamista_Model_DbTable_Units();
		$Combats = new Chamista_Model_DbTable_Combats();
		$Reports = new Chamista_Model_DbTable_Combats_Reports();
		
		$now = time();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		$transportsView = array();
		$rows = $Transports->fetchAll($Transports->select()->where('trans_uid = ?',$User->usr_id));
		foreach ($rows as $row) {
			$Origin = new Chamista_Model_Town($row->trans_origin);
			if ($row->trans_destination > 0)
				$Destination = new Chamista_Model_Town($row->trans_destination);
			else
				$Destination = new Chamista_Model_Barbarian();
			$missionName = '';
			$eta = '';
			$return = false;
			$going = false;
			switch ($row->trans_mission) {
				case 0:
					$missionName = 'Transport (loading)';
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$img = '/img/interface/mission_transport.gif';
					$class1 = 'own';
					$class2 = 'own';
					$going = true;
					break;
				case 1:
					$missionName = 'Transport (underway)';
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$img = '/img/interface/mission_transport.gif';
					$class1 = 'own';
					$class2 = 'own';
					break;
				case 2:
					$missionName = 'Transport (return)';
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$img = '/img/interface/mission_transport.gif';
					$class1 = 'own';
					$class2 = 'own';
					$return = true;
					break;
				case 5:
					$missionName = 'Pillaging (underway)';
					$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);
					$img = '/img/interface/mission_plunder.gif';
					$class1 = 'own';
					$class2 = 'own';
					$going = true;
					break;
				case 6:
					$missionName = 'Pillage (Open Battle)';
					/*$eta = array(
						'enddate' => $row->trans_enddate,
						'currentdate' => $now,
						'title' => Chamista_Model_Format::formatTime($row->trans_enddate - $now)
					);*/
					$eta = array();
					$img = '/img/interface/mission_plunder.gif';
					$class1 = 'own';
					$class2 = 'own';
					$going = true;
					break;
				case 7:
					$missionName = 'Pillage (return)';
					$eta = array();
					$img = '/img/interface/mission_plunder.gif';
					$class1 = 'own';
					$class2 = 'own';
					$return = true;
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
					$goods[Chamista_Model_Format::tradegood_css(intval(substr($i,-1)))] = array(
						'total' => $v,
						'name' => Chamista_Model_Format::tradegood_name(intval(substr($i,-1))),
						'css' => Chamista_Model_Format::tradegood_css(intval(substr($i,-1))),
					);
				}
			}
			$armies = (array) unserialize($row->trans_armies);
			$troops = array();
			foreach ($armies as $i => $v) {
				if ($v == 0) continue;
				$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $i));
				$troops[] = array(
					'css' => $unitRow->unit_css,
					'name' => $unitRow->unit_name,
					'total' => $v,
				);
			}
			$units = array_sum($armies);
			$origin = array(
				'name' => $Origin->town_name,
				'owner' => $Origin->getOwnerName(),
				'id' => $Origin->town_id,
			);
			$target = array(
				'name' => $Destination->town_name,
				'owner' => $Destination->getOwnerName(),
				'id' => $Destination->town_id,
			);
			$transportsView[] = array(
				'class1' => $class1,
				'class2' => $class2,
				'id' => $row->trans_id,
				'eta' => $eta,
				'ships' => $row->trans_ships,
				'goods' => $goods,
				'origin' => $origin,
				'return' => $return,
				'going' => $going,
				'img' => $img,
				'missionName' => $missionName,
				'target' => $target,
				'armies' => $troops,
				'units' => $units,
			);
		}
		
		// Events
		$combatRows = $Combats->fetchAll(
			$Combats->select()
				->where('combat_attacker IN (?) OR combat_defender IN (?)', $User->getUserCityIDs(true))
		);
		$events = array();
		$defendants = array();
		foreach ($combatRows as $combatRow) {
			if (in_array($combatRow->combat_defender, $defendants)) continue;
			$Attacker = new Chamista_Model_Town($combatRow->combat_attacker);
			if ($combatRow->combat_defender > 0)
				$Defender = new Chamista_Model_Town($combatRow->combat_defender);
			else
				$Defender = new Chamista_Model_Barbarian();
			
			if ($combatRow->combat_defender > 0) {
				$coords = $Defender->getIslandXY();
			} else {
				$coords = $Attacker->getIslandXY();
			}
			
			// Get all occuring events
			$eventRows = $Combats->fetchAll(
				$Combats->select()
					->where('combat_defender = ?', $combatRow->combat_defender)
			);
			$battles = array();
			$nextEventETA = $now + 30 * 24 * 3600;
			foreach ($eventRows as $eventRow) {
				$row = $Transports->fetchRow(
					$Transports->select()
						->where('trans_id = ?', $eventRow->combat_trans)
				);
				$Origin = new Chamista_Model_Town($row->trans_origin);
				if ($row->trans_destination > 0)
					$Destination = new Chamista_Model_Town($row->trans_destination);
				else
					$Destination = new Chamista_Model_Barbarian();
				$missionName = 'Pillage (Open Battle)';
				if ($row->trans_enddate < $nextEventETA) $nextEventETA = $row->trans_enddate;
				$armies = (array) unserialize($row->trans_armies);
				$troops = array();
				foreach ($armies as $i => $v) {
					if ($v == 0) continue;
					$unitRow = $Units->fetchRow($Units->select()->where('unit_id = ?', $i));
					$troops[] = array(
						'css' => $unitRow->unit_css,
						'name' => $unitRow->unit_name,
						'total' => $v,
					);
				}
				$units = array_sum($armies);
				$origin = array(
					'name' => $Origin->town_name,
					'owner' => $Origin->getOwnerName(),
					'id' => $Origin->town_id,
				);
				$target = array(
					'name' => $Destination->town_name,
					'owner' => $Destination->getOwnerName(),
					'id' => $Destination->town_id,
				);
				$battles[] = array(
					'id' => $row->trans_id + $eventRow->combat_id,
					'eta' => $eta,
					'ships' => $row->trans_ships,
					'goods' => $goods,
					'origin' => $origin,
					'missionName' => $missionName,
					'target' => $target,
					'armies' => $troops,
					'units' => $units,
				);
			}
			
			$events[] = array(
				'name' => $Defender->town_name,
				'coords' => $coords,
				'id' => $Defender->town_id,
				'battles' => $battles,
				'nextEventETA' => array(
					'enddate' => $nextEventETA,
					'currentdate' => $now,
					'title' => Chamista_Model_Format::formatTime($nextEventETA - $now)
				),
			);
			$defendants[] = $combatRow->combat_defender;
		}
		
		$this->view->militaryAdvisor = array(
			'transports' => $transportsView,
			'movements' => count($transportsView),
			'events' => $events,
			'combatReports' => $Reports->getNumberofReports($User->usr_id),
		);
		$this->view->body_id = 'militaryAdvisorMilitaryMovements';
		$this->view->css = 'ik_militaryAdvisorMilitaryMovements_'.VERSION.'.css';
	}
	
	public function combatreportsAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Reports = new Chamista_Model_DbTable_Combats_Reports();
		$Transports = new Chamista_Model_DbTable_Transports();
		
		$now = time();
		
		$towns = $User->getUserCityIDs(true);
		$reportRows = $Reports->fetchAll(
			$Reports->select()
				->where('report_attacker IN (?) OR report_defender IN (?)', $towns)
				->order('report_date DESC')
		);
		$reportsView = array();
		foreach ($reportRows as $reportRow) {
			$new = false;
			$won = false;
			$lost = false;
			if (in_array($reportRow->report_attacker, $towns)) {
				if ($reportRow->report_anew) $new = true;
				if ($reportRow->report_winner == 2) $won = true;
				if ($reportRow->report_winner == 1) $lost = true;
			} else {
				if ($reportRow->report_dnew) $new = true;
				if ($reportRow->report_winner == 1) $won = true;
				if ($reportRow->report_winner == 2) $lost = true;
			}
			$reportsView[] = array(
				'id' => $reportRow->report_id,
				'title' => $reportRow->report_title,
				'date' => $reportRow->report_date,
				'new' => $new,
				'won' => $won,
				'lost' => $lost,
			);
		}
		
		$movements = $Transports->fetchRow($Transports->select()->from($Transports->_name,'COUNT(*) AS total')->where('trans_uid = ?',$User->usr_id))->total;
		
		$this->view->militaryAdvisor = array(
			'reports' => $reportsView,
			'movements' => $movements,
			'combatReports' => $Reports->getNumberofReports($User->usr_id),
		);
		$this->view->body_id = 'militaryAdvisorCombatReports';
		$this->view->css = 'ik_militaryAdvisorCombatReports_'.VERSION.'.css';
	}
	
	public function reportviewAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Reports = new Chamista_Model_DbTable_Combats_Reports();
		$Transports = new Chamista_Model_DbTable_Transports();
		
		$now = time();
		
		$combatId = (int) $request->getParam('combatId');
		$reportRow = $Reports->fetchRow(
			$Reports->select()
				->where('report_id = ?', $combatId)
		);
		if (!$reportRow) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$Attacker = new Chamista_Model_Town($reportRow->report_attacker);
		if ($reportRow->report_defender > 0)
			$Defender = new Chamista_Model_Town($reportRow->report_defender);
		else
			$Defender = new Chamista_Model_Barbarian();
		
		if ($Attacker->town_uid != $User->usr_id && $Defender->town_uid != $User->usr_id) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		if ($Attacker->town_uid == $User->usr_id) {
			$Reports->markAsRead('a', $combatId);
		} elseif ($Defender->town_uid == $User->usr_id) {
			$Reports->markAsRead('d', $combatId);
		}
		
		$outcome = array();
		$temp = unserialize($reportRow->report_outcome);
		$typesOfArmy = array();
		foreach ($temp['attacker'] as $type => $t) {
			if (!in_array($type, $typesOfArmy)) {
				$typesOfArmy[] = $type;
			}
		}
		foreach ($temp['defender'] as $type => $t) {
			if (!in_array($type, $typesOfArmy)) {
				$typesOfArmy[] = $type;
			}
		}
		foreach ($typesOfArmy as $k => $v) {
			$typesOfArmy[$k] = array('sid' => Chamista_Model_Format::unit_sid($v), 'id' => $v);
		}
		if (count($typesOfArmy) > 7) {
			$rows = array();
			$rowsA = array();
			$rowsD = array();
			$i = 1;
			$j = 0;
			foreach ($typesOfArmy as $t) {
				$rows[$j][] = $t['sid'];
				if (!isset($temp['attacker'][$t['id']])) {
					$rowsA[$j][] = '-';
				} else {
					$rowsA[$j][] = ($temp['attacker'][$t['id']]['value'] ). ' (-'.$temp['attacker'][$t['id']]['loss'].')';
				}
				if (!isset($temp['defender'][$t['id']])) {
					$rowsD[$j][] = '-';
				} else {
					$rowsD[$j][] = ($temp['defender'][$t['id']]['value'] ). ' (-'.$temp['defender'][$t['id']]['loss'].')';
				}
				$i++;
				if ($i > 7) {
					$i = 1;
					$j++;
				}
			}
			$outcome['typesOfArmy'] = $rows;
			$outcome['attacker'] = $rowsA;
			$outcome['defender'] = $rowsD;
		} else {
			$attacker = array();
			$defender = array();
			foreach ($typesOfArmy as $t) {
				$rows[] = $t['sid'];
				if (!isset($temp['attacker'][$t['id']])) {
					$attacker[] = '-';
				} else {
					$attacker[] = ($temp['attacker'][$t['id']]['value']). ' (-'.$temp['attacker'][$t['id']]['loss'].')';
				}
				if (!isset($temp['defender'][$t['id']])) {
					$defender[] = '-';
				} else {
					$defender[] = ($temp['defender'][$t['id']]['value']). ' (-'.$temp['defender'][$t['id']]['loss'].')';
				}
			}
			$outcome['typesOfArmy'] = array($rows);
			$outcome['attacker'] = array($attacker);
			$outcome['defender'] = array($defender);
		}
		
		$loots = array();
		$temp = unserialize($reportRow->report_loots);
		if (count($temp) > 0) {
			foreach ($temp as $resource => $value) {
				if ($value == 0) continue;
				$id = Chamista_Model_Format::tradegood_id($resource);
				$loots[] = array(
					'css' => Chamista_Model_Format::tradegood_css($id),
					'name' => Chamista_Model_Format::tradegood_fullname($id),
					'value' => $value,
				);
			}
		}
		
		$this->view->reportView = array(
			'id' => $reportRow->report_id,
			'title' => $reportRow->report_title,
			'date' => $reportRow->report_date,
			'outcome' => $outcome,
			'winner' => $reportRow->report_winner,
			'loots' => $loots,
			'attacker' => array(
				'name' => $Attacker->getOwnerName(),
				'id' => $Attacker->town_uid,
				'townName' => $Attacker->town_name,
				'townId' => $Attacker->town_id,
			),
			'defender' => array(
				'name' => $Defender->getOwnerName(),
				'id' => $Defender->town_uid,
				'townName' => $Defender->town_name,
				'townId' => $Defender->town_id,
			),
			'movements' => $Transports->fetchRow($Transports->select()->from($Transports->_name,'COUNT(*) AS total')->where('trans_uid = ?',$User->usr_id))->total,
		);
		
		$this->view->body_id = 'militaryAdvisorReportView';
		$this->view->css = 'ik_militaryAdvisorReportView_'.VERSION.'.css';
	}
	
	public function detailedreportviewAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Reports = new Chamista_Model_DbTable_Combats_Reports();
		$Rounds = new Chamista_Model_DbTable_Combats_Rounds();
		$Transports = new Chamista_Model_DbTable_Transports();
		
		$now = time();
		
		$combatId = (int) $request->getParam('detailedCombatId');
		$reportRow = $Reports->fetchRow(
			$Reports->select()
				->where('report_id = ?', $combatId)
		);
		if (!$reportRow) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$Attacker = new Chamista_Model_Town($reportRow->report_attacker);
		if ($reportRow->report_defender > 0)
			$Defender = new Chamista_Model_Town($reportRow->report_defender);
		else
			$Defender = new Chamista_Model_Barbarian();
		
		if ($Attacker->town_uid != $User->usr_id && $Defender->town_uid != $User->usr_id) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$roundsOfReport = $Rounds->fetchRow(
			$Rounds->select()
				->from($Rounds->_name, 'COUNT(*) AS total')
				->where('round_parent = ?', $combatId)
		)->total;
		
		$round = (int) $request->getParam('combatRound');
		if ($round > $roundsOfReport) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		$roundRow = $Rounds->fetchRow(
			$Rounds->select()
				->where('round_parent = ?', $combatId)
				->where('round_round = ?', $round)
		);
		if (!$roundRow) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$data = unserialize($reportRow->report_data);
		$rndData = unserialize($roundRow->round_data);
		
		$mouseOver = array();
		// Organize the battlefield layout in the controller rather than in the view
		$slots = Chamista_Model_Formula::getBattleFieldInfo($data['fieldSize']);
		$attacker = $rndData['armies']['attacker'];
		$defender = $rndData['armies']['defender'];
		
		// Build 1 slot by 1 slot
		$fieldAttacker = array();
		$fieldAttacker['special'] = array(false, false);
		if (isset($attacker['cooks'])) {
			// @TODO cooks
		}
		if (isset($attacker['doctors'])) {
			// @TODO doctors
		}
		$fieldAttacker['airfighter'] = array(false);
		if (isset($attacker['airDef'])) {
			// @TODO airDef
		}
		$fieldAttacker['air'] = array(false);
		if (isset($attacker['air'])) {
			// @TODO air
		}
		$fieldAttacker['flankleft'] = array(false, false);
		$fieldAttacker['flankright'] = array(false, false);
		if (isset($attacker['light'])) {
			// @TODO flanks
		}
		$fieldAttacker['main'] = array(false, false, false, false, false, false, false);
		$start = floor((7 - $slots['front']) / 2);
		for ($i = $start; $i <= $slots['front'] + 1; $i++) {
			$fieldAttacker['main'][$i] = 0;
		}
		if (isset($attacker['front'])) {
			$start = floor(($slots['front'] - count($attacker['front'])) / 2);		// Start filling armies from which slot
			$start += floor((7 - $slots['front']) / 2);		// Penalized if slots less than 7
			$count = $start;
			foreach ($attacker['front'] as $front) {
				$fieldAttacker['main'][$count] = array(
					'id' => '11_'.$front['id'].'_'.$count,
					'class' => Chamista_Model_Format::unit_sid($front['id']),
					'loss' => ceil((100 - $front['hp']) / 100 * 32),
					'number' => $front['value'].' (-'.$front['loss'].')',
				);
				$mouseOver['11_'.$front['id'].'_'.$count] = array(
					'name' => Chamista_Model_Format::unit_name($front['id']),
					'hp' => $front['hp'].'%',
				);
				$count++;
			}
		}
		$fieldAttacker['longRange'] = array(false, false, false, false, false, false, false);
		$start = floor((7 - $slots['long']) / 2);
		for ($i = $start; $i <= $slots['long'] + 1; $i++) {
			$fieldAttacker['longRange'][$i] = 0;
		}
		if (isset($attacker['long'])) {
			$start = floor(($slots['long'] - count($attacker['long'])) / 2);		// Start filling armies from which slot
			$start += floor((7 - $slots['long']) / 2);		// Penalized if slots less than 7
			$count = $start;
			foreach ($attacker['long'] as $front) {
				$fieldAttacker['longRange'][$count] = array(
					'id' => '12_'.$front['id'].'_'.$count,
					'class' => Chamista_Model_Format::unit_sid($front['id']),
					'loss' => ceil((100 - $front['hp']) / 100 * 32),
					'number' => $front['value'].' (-'.$front['loss'].')',
				);
				$mouseOver['12_'.$front['id'].'_'.$count] = array(
					'name' => Chamista_Model_Format::unit_name($front['id']),
					'hp' => $front['hp'].'%',
				);
				$count++;
			}
		}
		$fieldAttacker['artillery'] = array(false, false, false);
		if (isset($attacker['artillery'])) {
			// @TODO artillery
		}
		
		$fieldDefender = array();
		$fieldDefender['special'] = array(false, false);
		if (isset($defender['cooks'])) {
			// @TODO cooks
		}
		if (isset($defender['doctors'])) {
			// @TODO doctors
		}
		$fieldDefender['airfighter'] = array(false);
		if (isset($defender['airDef'])) {
			// @TODO airDef
		}
		$fieldDefender['air'] = array(false);
		if (isset($defender['air'])) {
			// @TODO air
		}
		$fieldDefender['flankleft'] = array(false, false);
		$fieldDefender['flankright'] = array(false, false);
		if (isset($defender['light'])) {
			// @TODO flanks
		}
		$fieldDefender['main'] = array(false, false, false, false, false, false, false);
		$start = floor((7 - $slots['front']) / 2);
		for ($i = $start; $i <= $slots['front'] + 1; $i++) {
			$fieldDefender['main'][$i] = 0;
		}
		if (isset($defender['front'])) {
			$start = floor(($slots['front'] - count($defender['front'])) / 2);		// Start filling armies from which slot
			$start += floor((7 - $slots['front']) / 2);		// Penalized if slots less than 7
			$count = $start;
			foreach ($defender['front'] as $front) {
				$fieldDefender['main'][$count] = array(
					'id' => '21_'.$front['id'].'_'.$count,
					'class' => Chamista_Model_Format::unit_sid($front['id']),
					'loss' => ceil((100 - $front['hp']) / 100 * 32),
					'number' => $front['value'].' (-'.$front['loss'].')',
				);
				$mouseOver['21_'.$front['id'].'_'.$count] = array(
					'name' => Chamista_Model_Format::unit_name($front['id']),
					'hp' => $front['hp'].'%',
				);
				$count++;
			}
		}
		$fieldDefender['longRange'] = array(false, false, false, false, false, false, false);
		$start = floor((7 - $slots['long']) / 2);
		for ($i = $start; $i <= $slots['long'] + 1; $i++) {
			$fieldDefender['longRange'][$i] = 0;
		}
		if (isset($defender['long'])) {
			$start = floor(($slots['long'] - count($defender['long'])) / 2);		// Start filling armies from which slot
			$start += floor((7 - $slots['long']) / 2);		// Penalized if slots less than 7
			$count = $start;
			foreach ($defender['long'] as $front) {
				$fieldDefender['longRange'][$count] = array(
					'id' => '22_'.$front['id'].'_'.$count,
					'class' => Chamista_Model_Format::unit_sid($front['id']),
					'loss' => ceil((100 - $front['hp']) / 100 * 32),
					'number' => $front['value'].' (-'.$front['loss'].')',
				);
				$mouseOver['22_'.$front['id'].'_'.$count] = array(
					'name' => Chamista_Model_Format::unit_name($front['id']),
					'hp' => $front['hp'].'%',
				);
				$count++;
			}
		}
		$fieldDefender['artillery'] = array(false, false, false);
		if (isset($defender['artillery'])) {
			// @TODO artillery
		}
		$this->view->militaryAdvisor = array(
			'combatId' => $combatId,
			'reportTitle' => $reportRow->report_title,
			'attackerNick' => $Attacker->getOwnerName(),
			'defenderNick' => $Defender->getOwnerName(),
			'fieldAttacker' => $fieldAttacker,
			'mouseOver' => $mouseOver,
			'fieldDefender' => $fieldDefender,
			'time' => $roundRow->round_time,
			'events' => unserialize($roundRow->round_events),
			'round' => $round,
			'totalRounds' => $roundsOfReport,
		);
		
		$this->view->body_id = 'militaryAdvisorDetailedReportView';
		$this->view->css = 'ik_militaryAdvisorDetailedReportView_'.VERSION.'.css';
	}
}