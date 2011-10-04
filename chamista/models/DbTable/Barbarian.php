<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Barbarian table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Barbarian extends Zend_Db_Table {
	public $_name = 'barbarian';
	public $_key = 'barbarian_user';
	
	public function getUserAttacked($userId) {
		$row = $this->fetchRow($this->select()->from($this->_name,'barbarian_attacks')->where('barbarian_user = ?',$userId));
		if (!$row) {
			$insert = array(
				'barbarian_user' => $userId,
				'barbarian_attacks' => 0,
			);
			$this->insert($insert);
			return 0;
		} else {
			return $row->barbarian_attacks;
		}
	}
}