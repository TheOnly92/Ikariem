<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Town units table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Towns_Units extends Zend_Db_Table {
	public $_name = 'towns_units';
	public $_key = array('town_id', 'unit_id');
	
	public function getArmy($unit_id, $town_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'value')->where('unit_id = ?',$unit_id)->where('town_id = ?',$town_id)->limit(1));
		if (!$row) {
			$data = array(
				'unit_id' => $unit_id,
				'town_id' => $town_id,
				'value' => 0
			);
			$this->insert($data);
			return 0;
		} else {
			return $row->value;
		}
	}
	
	public function setArmy($unit_id, $value, $town_id) {
		$row = $this->fetchRow($this->select()->from($this->_name,'value')->where('unit_id = ?',$unit_id)->where('town_id = ?',$town_id)->limit(1));
		if (!$row) {
			$data = array(
				'unit_id' => $unit_id,
				'town_id' => $town_id,
				'value' => $value
			);
			$this->insert($data);
		} else {
			$data = array(
				'value' => $value
			);
			$where = array(
				$this->getAdapter()->quoteInto('unit_id = ?', $unit_id),
				$this->getAdapter()->quoteInto('town_id = ?', $town_id)
			);
			$this->update($data,$where);
		}
	}
}