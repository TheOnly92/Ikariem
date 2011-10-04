<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Barbarian model file.
 * A pseudo class file for Barbarian Village.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_Barbarian {
	public $town_id = 0;
	public $town_uid = 0;
	public $town_name = 'Barbarian Village';
	
	public function getOwnerName() {
		// This will never be displayed, but just for fun :P
		return 'Barbarians';
	}
}