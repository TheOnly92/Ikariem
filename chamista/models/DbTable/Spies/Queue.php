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

class Chamista_Model_DbTable_Spies_Queue extends Zend_Db_Table {
	public $_name = 'spies_queue';
	public $_key = array('town_id', 'startdate');
}