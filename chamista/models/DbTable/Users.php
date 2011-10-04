<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Users table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Users extends Zend_Db_Table {
	public $_name = 'users';
	public $_primary = 'usr_id';
	
	public function createNewUser($data) {
		$now = time();
		$Towns = new Chamista_Model_DbTable_Towns();
		$Islands = new Chamista_Model_DbTable_Islands();
		$Buildings = new Chamista_Model_DbTable_Buildings();
		$Users_Researches = new Chamista_Model_DbTable_Users_Researches();
		$Users_Notes = new Chamista_Model_DbTable_Users_Notes();
		
		// Select a random island for this town
		$towns = 16;
		while ($towns == 16) {
			$id = rand(1,10);
			$island = $Islands->fetchRow($Islands->select('island_id')->order('rand()')->limit($id,1));
			$towns = $Towns->getTownsInIsland($island->island_id);
		}
		
		$data = array(
			'usr_nick' => $data['usr_nick'],
			'usr_password' => md5($data['usr_password']),
			'usr_email' => $data['usr_email'],
			'usr_capital' => 0,
			'usr_ships' => 0,
			'usr_gold' => 100,
			'usr_activation' => '',
			'usr_research' => 0,
			'usr_update' => $now
		);
		$usr_id = $this->insert($data);
		
		// Other tables that are related to the users
		$data = array(
			'usr_id' => $usr_id
		);
		$Users_Researches->insert($data);
		$Users_Notes->insert($data);
		
		$offset = rand(0,15);
		// Select a town position on the island
		for ($town_location=0;$town_location<=15;$town_location++) {
            $loc = $town_location + $offset;
            if ($loc > 15) $loc -= 15;
			$row = $Towns->fetchRow($Towns->select()->from($Towns->_name,'COUNT(*) as total')->where("town_location = ?",$loc)->where("town_island = ?",$island->island_id));
			if ($row->total == 0) {
				break;
			}
		}
        $town_location = $loc;
		
		$data = array(
			'town_uid' => $usr_id,
			'town_name' => 'Littletinople',
			'town_population' => 40,
			'town_lvl' => 1,
			'town_actions' => 3,
			'town_resource0' => 160,
			'town_resource1' => 0,
			'town_resource2' => 0,
			'town_resource3' => 0,
			'town_resource4' => 0,
			'town_island' => $island->island_id,
			'town_update' => $now,
			'town_location' => $town_location
		);
		$town_id = $Towns->insert($data);
		
		$data = array(
			'usr_capital' => $town_id
		);
		$where = $this->getAdapter()->quoteInto('usr_id = ?', $usr_id);
		$this->update($data, $where);
		
		$data = array(
			'build_town' => $town_id,
			'build_position' => 0,
			'build_type' => 1,
			'build_lvl' => 1
		);
		$Buildings->insert($data);
	}
}