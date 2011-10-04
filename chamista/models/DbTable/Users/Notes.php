<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * User notes table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Users_Notes extends Zend_Db_Table {
	public $_name = 'users_notes';
	public $_key = 'usr_id';
	
	public function getNotes($userId) {
		$rt = $this->fetchRow(
			$this->select()
				->from($this->_name, 'usr_notes')
				->where('usr_id = ?', $userId)
				->limit(1)
		);
		
		return $rt->usr_notes;
	}
	
	public function updateNotes($userId, $notes) {
		$where = $this->getAdapter()->quoteInto('usr_id = ?', $userId);
		$data = array(
			'usr_notes' => $notes,
		);
		$this->update($data, $where);
	}
}