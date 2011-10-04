<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Spies queue table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Spies_Reports extends Zend_Db_Table {
	private $_name = 'spies_reports';
	private $_key = 'report_id';
	
	public function getUserReports($userId) {
		return $this->fetchAll(
			$this->select()
				->where('report_owner = ?', $userId)
		);
	}
}