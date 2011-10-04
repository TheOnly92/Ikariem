<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Trailer model file.
 * Specialized breadcrumb creator.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_Trailer {
	var $path = array ();
	
	function Trail($includeHome = true, $homeLabel = 'Home', $homeLink = '/') {
		if ($includeHome)
			$this->addStep ( $homeLabel, $homeLink );
	}
	
	function addStep($title, $link = '', $tips = '') {
		$item = array ('title' => $title );
		if (strlen ( $link ) > 0)
			$item ['link'] = $link;
		if (strlen($tips) > 0)
			$item['tips'] = $tips;
		$this->path [] = $item;
	}
}