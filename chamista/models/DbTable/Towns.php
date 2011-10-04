<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Towns table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Towns extends Zend_Db_Table {
	public $_name = 'towns';
	public $_primary = 'town_id';
	
	public function getTownsInIsland($island_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'COUNT(*) as total')->where("town_island = ?",$island_id));
		return intval($row->total);
	}
	
	public function getData($name, $town_id) {
		$row = $this->fetchRow($this->select()->from($this->_name, $name)->where('town_id = ?',$town_id));
		return $row->{$name};
	}
	
	public function getUserCities($user_id) {
		$rows = $this->fetchAll($this->select()->from($this->_name, array('town_id','town_name','town_island'))->where('town_uid = ?', $user_id)->where('town_lvl > 0'));
		return $rows;
	}
	
	public function getIslandXY($town_id) {
		$Island = new Chamista_Model_DbTable_Islands();
		$island_id = $this->getIslandID($town_id);
		return array('x' => $Island->getPosX($island_id), 'y' => $Island->getPosY($island_id));
	}
	
	public function getIslandID($town_id) {
		$Island = new Chamista_Model_DbTable_Islands();
		$row = $this->fetchRow($this->select()->from($this->_name,'town_island')->where('town_id = ?',$town_id)->limit(1));
		return $row->town_island;
	}
	
	public function colonize($islandId, $position, $userId) {
		$Islands = new Chamista_Model_DbTable_Islands();
		$Buildings = new Chamista_Model_DbTable_Buildings();
		
		// Check if island exists
		if ($Islands->fetchRow($Islands->select()->from($Islands->_name,'COUNT(*) AS total')->where('island_id = ?',$islandId))->total == 0) {
			return -1;
		}
		// Check if position available
		if ($this->fetchRow($this->select()->from($this->_name,'COUNT(*) as total')->where("town_location = ?",$position)->where("town_island = ?",$islandId))->total > 0) {
			return -2;
		}
		
		$insert = array(
			'town_uid' => $userId,
			'town_name' => 'Littletinople',
			'town_population' => 40,
			'town_lvl' => 0,
			'town_island' => $islandId,
			'town_location' => $position,
		);
		$townId = $this->insert($insert);
		
		$data = array(
			'build_town' => $townId,
			'build_position' => 0,
			'build_type' => 1,
			'build_lvl' => 1
		);
		$Buildings->insert($data);
		return $townId;
	}
}