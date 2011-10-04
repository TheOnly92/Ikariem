<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Units table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Units extends Zend_Db_Table {
	public $_name = 'units';
	public $_key = 'unit_id';
	
	public function getUnits($type) {
		return $this->fetchAll(
			$this->select()
				->where('unit_type = ?', $type)
				->order('unit_id ASC')
		);
	}
	
	public function getUnitById($id) {
		return $this->fetchRow(
			$this->select()
				->where('unit_id = ?', $id)
				->limit(1)
		);
	}
}