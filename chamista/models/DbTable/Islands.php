<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Islands table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Islands extends Zend_Db_Table {
	public $_name = 'islands';
	public $_primary = 'island_id';
	
	public function getTradeGood($island_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'island_resource')->where('island_id = ?',$island_id)->limit(1));
		return $row->island_resource;
	}
	
	public function getPosX($island_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'island_posx')->where('island_id = ?',$island_id)->limit(1));
		return $row->island_posx;
	}
	
	public function getPosY($island_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'island_posy')->where('island_id = ?',$island_id)->limit(1));
		return $row->island_posy;
	}
	
	public function getIslandWithinRange($currPos, $range) {
		$rt = $this->fetchAll(
			$this->select()
				->from($this->_name, 'island_id')
				->where('island_posx >= ?', $currPos['x'] - $range)
				->where('island_posx <= ?', $currPos['x'] + $range)
				->where('island_posy >= ?', $currPos['y'] - $range)
				->where('island_posy <= ?', $currPos['y'] + $range)
		);
		return $rt;
	}
	
	public function userOwns($user_id,$island_id) {
		$Towns = new Chamista_Model_DbTable_Towns();
		$user_towns = $Towns->getUserCities($user_id);
		$town_ids = array();
		foreach ($user_towns as $user_town) {
			$town_ids[] = $user_town->town_id;
		}
		
		$row = $Towns->fetchRow($Towns->select()->from($Towns->_name,'COUNT(*) AS total')->where('town_id IN ( ? )', $town_ids)->where('town_island = ?',$island_id));
		if ($row->total > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function islandSawDonated($island_id) {
		$Towns = new Chamista_Model_DbTable_Towns();
		$row = $Towns->fetchRow($Towns->select()->from($Towns->_name,'SUM(town_donated) AS total')->where('town_island = ?',$island_id));
		return $row->total;
	}
	
	public function islandMineUpgrading($type, $island_id) {
		$Islands_Queue = new Chamista_Model_DbTable_Islands_Queue();
		$row = $Islands_Queue->fetchRow($Islands_Queue->select()->from($Islands_Queue->_name,'COUNT(*) AS total')->where('queue_island = ?',$island_id)->where('queue_type = ?',$type));
		return $row->total;
	}
	
	public function generateJSON($x_min,$x_max,$y_min,$y_max) {
		$Towns = new Chamista_Model_DbTable_Towns();
		$rt = array();
		$rt['request'] = array(
			'x_min' => $x_min,
			'x_max' => $x_max,
			'y_min' => $y_min,
			'y_max' => $y_max
		);
		$data = array();
		for ($i=$x_min;$i<=$x_max;$i++) {
			$islands = $this->fetchAll($this->select()->where('island_posx = ?',$i)->where('island_posy >= ?',$y_min)->where('island_posy <= ?',$y_max));
			foreach ($islands as $island) {
				$data[$i][$island->island_posy] = array(
					$island->island_id,
					$island->island_name,
					$island->island_resource,
					$island->island_wonder,
					0,
					$island->island_type,
					$island->island_saw_lvl,
					$Towns->getTownsInIsland($island->island_id)
				);
			}
		}
		$rt['data'] = $data;
		return $rt;
	}
}