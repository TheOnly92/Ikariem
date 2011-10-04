<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Highscores table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Highscores extends Zend_Db_Table {
	public $_name = 'highscores';
	public $_key = 'highscore_user';
	
	public function getUserHighscore($userId) {
		return $this->fetchRow(
			$this->select()
				->from($this->_name, 'highscore_total')
				->where('highscore_user = ?', $userId)
		)->highscore_total;
	}
}