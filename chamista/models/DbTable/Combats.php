<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Combats table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Combats extends Zend_Db_Table {
	public $_name = 'combats';
	public $_key = 'combat_id';
	
	public function getTransportId($combatId) {
		return $this->fetchRow(
			$this->select()
				->from($this->_name, 'combat_trans')
				->where('combat_id = ?', $combatId)
		)->combat_trans;
	}
	
	public function deleteCombat($combatId) {
		$where = $this->getAdapter()->quoteInto('combat_id = ?', $combatId);
		$this->delete($where);
	}
}