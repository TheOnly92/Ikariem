<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Buildings queue table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Buildings_Queue extends Zend_Db_Table {
	public $_name = 'buildings_queue';
	public $_key = array('queue_town','queue_pos');
	
	/**
	 * This function is used to get the queue information in the building information page
	 * @param int $townId
	 * @param int $buildingType
	 * @param int $position
	 * 
	 * @return object
	 * Returns the row of the queue with information
	 */
	public function getCurrBuildingQueue($townId, $buildingType, $position) {
		$rt = $this->fetchRow($this->select()
			->where('queue_town = ?', $townId)
			->where('queue_type = ?', $buildingType)
			->where('queue_pos = ?', $position));
		return $rt;
	}
}