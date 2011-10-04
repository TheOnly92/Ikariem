<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Islands queue table file.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_DbTable_Islands_Queue extends Zend_Db_Table {
	public $_name = 'islands_queue';
	public $_key = array('queue_island', 'queue_type');
}