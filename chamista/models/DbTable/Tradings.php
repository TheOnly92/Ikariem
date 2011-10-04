<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Tradings table file.
 * Last updated: $LastChangedDate$
 * 
 * @author        TheOnly92
 * @copyright    (c) 2008-2010 Rewaz Labs.
 * @package        Project Chamista
 * @version        $LastChangedRevision$
 */

class Chamista_Model_DbTable_Tradings extends Zend_Db_Table {
	public $_name = 'tradings';
	public $_key = 'trading_town';
	
	public function getTownRow($town_id) {
		$row = $this->fetchRow($this->select()->where('trading_town = ?',$town_id));
		if (!$row) {
			$Town = new Chamista_Model_Town($town_id);
			$insert = array(
				'trading_town' => $town_id,
				'trading_island' => $Town->town_island
			);
			$this->insert($insert);
			$row = $this->fetchRow($this->select()->where('trading_town = ?',$town_id));
		}
		return $row;
	}
}