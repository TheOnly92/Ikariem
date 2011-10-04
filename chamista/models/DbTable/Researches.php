<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Events table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Researches extends Zend_Db_Table {
	public $_name = 'researches';
	public $_key = 'research_id';
	
	public function getResearchById($research_id) {
		$row = $this->fetchRow($this->select()->from($this->_name, 'research_name')->where('research_id = ?',$research_id)->limit(1));
		
		return $row->research_name;
	}
	
}