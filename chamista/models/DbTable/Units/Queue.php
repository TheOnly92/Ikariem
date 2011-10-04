<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Units queue table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Units_Queue extends Zend_Db_Table {
	public $_name = 'units_queue';
	public $_key = 'queue_town';
	
	public function fetchUnitQueue($townId, $type) {
		$rt = $this->fetchAll(
			$this->select()
				->where('queue_town = ?', $townId)
				->where('queue_type = ?', $type)
				->order('queue_created')
		);
		
		return $rt;
	}
	
	public function getLastFinishUnit($townId) {
		return $this->fetchRow(
			$this->select()
				->from($this->_name, 'MAX(queue_finish) AS finish')
				->where('queue_town = ?', $townId)
		)->finish;
	}
	
	public function insertNewQueue($townId, $finish, $created, $data, $type) {
		$data = array(
			'queue_town' => $townId,
			'queue_finish' => $finish,
			'queue_created' => $created,
			'queue_data' => $data,
			'queue_type' => $type,
		);
		$this->insert($data);
	}
}