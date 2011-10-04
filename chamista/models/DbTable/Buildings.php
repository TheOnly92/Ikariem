<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Buildings table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Buildings extends Zend_Db_Table {
	public $_name = 'buildings';
	
	public function getGroundBuilding($location, $town_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'build_type')->where("build_position = ?",$location)->where("build_town = ?",$town_id));
		if (!$row) {
			return 0;
		}
		return $row->build_type;
	}
	
	public function getPositionLvl($location, $town_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'build_lvl')->where("build_position = ?",$location)->where("build_town = ?",$town_id));
		if (!$row) {
			return 0;
		}
		return $row->build_lvl;
	}
	
}