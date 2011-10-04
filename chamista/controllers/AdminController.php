<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Admin controller file.
 * Handle all actions redirected to the admin controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class AdminController extends Zend_Controller_Action {
	public function resetAction() {
		$Combats = new Chamista_Model_DbTable_Combats();
		$CombatReports = new Chamista_Model_DbTable_Combats_Reports();
		$CombatRounds = new Chamista_Model_DbTable_Combats_Rounds();
		$Transports = new Chamista_Model_DbTable_Transports();
		$Combats->delete();
		$CombatReports->delete();
		$CombatRounds->delete();
		$update = array(
			'trans_mission' => 5,
		);
		$where = $Transports->getAdapter()->quoteInto('trans_mission = 6');
		$Transports->update($update, $where);
		exit;
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index');
		}
		
		echo('<pre>');
		$Transports = new Chamista_Model_DbTable_Transports();
		$now = time();
		$Combats = new Chamista_Model_DbTable_Combats();
		$Events = new Chamista_Model_DbTable_Events();
		$missions = array(5,6);
		$rows = $Transports->fetchAll($Transports->select()->where('trans_enddate <= ?',$now)->where('trans_mission IN (?)', $missions));
		foreach ($rows as $row) {
			switch ($row->trans_mission) {
				// Just arrived to fight
				case 5:
					$Town = new Chamista_Model_Town($row->trans_origin);
					
					if ($row->trans_destination == 0) {
						// Barbarian village
						$nextRound = ($row->trans_enddate - $row->trans_startdate);
						$insert = array(
							'combat_trans' => $row->trans_id,
							'combat_attacker' => $Town->town_id,
							'combat_defender' => 0,
							//'combat_nextround' => $now + $nextRound,
							'combat_data' => serialize(array())
						);
						$combatId = $Combats->insert($insert);
						$update = array(
							'trans_mission' => 6,
							'trans_startdate' => $now,
							//'trans_enddate' => $now + $nextRound,
						);
						$where = $Transports->getAdapter()->quoteInto('trans_id = ?', $row->trans_id);
						$Transports->update($update, $where);
						$CEngine = new Chamista_Model_CombatEngine($combatId);
						$CEngine->landBattle();
					} else {
						
					}
					break;
				case 6:
					echo('Invoking battle for: '.$row->trans_id."\n");
					// Battle already on-going
					$nextRound = ($row->trans_enddate - $row->trans_startdate);
					/*$update = array(
						'trans_mission' => 6,
						'trans_startdate' => $now,
						'trans_enddate' => $now + $nextRound,
					);
					$where = $Transports->getAdapter()->quoteInto('trans_id = ?', $row->trans_id);
					$Transports->update($update, $where);*/
					$update = array(
						'combat_nextround' => $now + $nextRound,
					);
					$combatRow = $Combats->fetchRow($Combats->select()->where('combat_trans = ?', $row->trans_id));
					$where = $Combats->getAdapter()->quoteInto('combat_id = ?', $combatRow->combat_id);
					//$Combats->update($update, $where);
					$CEngine = new Chamista_Model_CombatEngine($combatRow->combat_id);
					$CEngine->landBattle();
					break;
			}
		}
		exit;
	}
}