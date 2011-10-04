<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Units class table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Units_Class extends Zend_Db_Table {
	public $_name = 'units_class';
	public $_key = 'id';
	
	public function getClassById($id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'name')->where('id = ?',$id));
		return $row->name;
	}
}