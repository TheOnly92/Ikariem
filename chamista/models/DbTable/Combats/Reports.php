<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Combat reports parent table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Combats_Reports extends Zend_Db_Table {
	public $_name = 'combats_reports';
	public $_key = 'report_id';
	
	public function markAsRead($side, $id) {
		$update = array(
			'report_'.$side.'new' => 0,
		);
		$where = $this->getAdapter()->quoteInto('report_id = ?', $id);
		$this->update($update, $where);
	}
	
	public function getNumberofReports($userId) {
		$User = new Chamista_Model_User($userId);
		$towns = $User->getUserCityIDs(true);
		
		return $this->fetchRow(
			$this->select()
				->from($this->_name, 'COUNT(*) AS total')
				->where('report_attacker IN (?) OR report_defender IN (?)', $towns)
		)->total;
	}
}