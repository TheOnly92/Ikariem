<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Buildings queue table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Buildings_Data extends Zend_Db_Table {
	public $_name = 'buildings_data';
	public $_key = array('building_id');
	
	public function getField($build_id, $field) {
		if (substr($field,0,9) != 'building_') $field = 'building_'.$field;
		$row = $this->fetchRow($this->select()->from($this->_name,$field)->where('building_id = ?',$build_id));
		return $row->{$field};
	}
}