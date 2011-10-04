<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Format model file.
 * Functions to handle all conversion formatting.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Chamista_Model_Format {
	public function formatTimeToArray($time) {
		if ($time < 0) $time = 0;
		$m = floor($time / 60);
		$rt['s'] = $time - $m * 60;
		if ($m >= 60) {
			if (strlen($rt['s']) < 2) $rt['s'] = '0'.$rt['s'];
			$h = floor($m / 60);
			$rt['m'] = $m - $h * 60;
			if ($h >= 24) {
				$D = floor($h / 24);
				$rt['h'] = $h - $D * 24;
				$rt['D'] = $D;
			} else {
				$rt['h'] = $h;
			}
			unset($rt['s']);
		} else {
			if (strlen($rt['s']) < 2) $rt['s'] = '0'.$rt['s'];
			$rt['m'] = $m;
		}
		return $rt;
	}
	
	public function formatTimeFromArray($time) {
		$rt = '';
		if (isset($time['D'])) {
			$rt = $time['D'].'D ';
		}
		if (isset($time['h'])) {
			$rt .= $time['h'].'h ';
		}
		if (isset($time['m']) && $time['m'] != 0) {
			$rt .= $time['m'].'m ';
		}
		if (isset($time['s']) && $time['s'] != 0) {
			$rt .= $time['s'].'s';
		}
		return $rt;
	}
	
	public function formatTime($time) {
		return self::formatTimeFromArray(self::formatTimeToArray(round($time)));
	}
	
	public function tradegood_plaintext($id) {
		switch ($id) {
			case 0: return 'wood';
			case 1: return 'wine';
			case 2: return 'marble';
			case 3: return 'crystal';
			case 4: return 'sulfur';
		}
	}
	
	public function tradegood_name($id) {
		switch ($id) {
			case 0: return 'Wood';
			case 1: return 'Wine';
			case 2: return 'Marble';
			case 3: return 'Crystal';
			case 4: return 'Sulfur';
		}
	}
	
	public function tradegood_fullname($id) {
		switch ($id) {
			case 0: return 'Building Material';
			case 1: return 'Wine';
			case 2: return 'Marble';
			case 3: return 'Crystal';
			case 4: return 'Sulfur';
		}
	}
	
	public function tradegood_mine($id) {
		switch ($id) {
			case 1: return 'Vineyard';
			case 2: return 'Quarry';
			case 3: return 'Crystal Mine';
			case 4: return 'Sulfur pit';
		}
	}
	
	public function tradegood_css($id) {
		switch ($id) {
			case 0: return 'wood';
			case 1: return 'wine';
			case 2: return 'marble';
			case 3: return 'glass';
			case 4: return 'sulfur';
		}
	}
	
	public function tradegood_id($text) {
		switch ($text) {
			case 'wood': return 0;
			case 'wine': return 1;
			case 'marble': return 2;
			case 'crystal': return 3;
			case 'sulfur': return 4;
		}
	}
	
	public function building_plaintext($id) {
		$Buildings_Data = new Chamista_Model_DbTable_Buildings_Data();
		return $Buildings_Data->fetchRow($Buildings_Data->select()->from($Buildings_Data->_name,'building_name')->where('building_id = ?',$id))->building_name;
	}
	
	public function building_action($id) {
		$Buildings_Data = new Chamista_Model_DbTable_Buildings_Data();
		return $Buildings_Data->fetchRow($Buildings_Data->select()->from($Buildings_Data->_name,'building_css')->where('building_id = ?',$id))->building_css;
	}
	
	public function unit_id($name) {
		$Units = new Chamista_Model_DbTable_Units();
		// Due to my laziness, we need to retain backward compatibility
		if (is_string($name)) {
			$select = $Units->select()
				->from($Units->_name,'unit_css')
				->where('unit_name = ?',$name)
				->limit(1);
		} elseif (is_int($name)) {
			$select = $Units->select()
				->from($Units->_name,'unit_css')
				->where('unit_id = ?',$name)
				->limit(1);
		}
		$row = $Units->fetchRow($select);
		
		return $row->unit_css;
	}
	
	public function unit_sid($identifier) {
		$Units = new Chamista_Model_DbTable_Units();
		// Due to my laziness, we need to retain backward compatibility
		if (is_string($identifier)) {
			$select = $Units->select()
				->from($Units->_name,'unit_sid')
				->where('unit_name = ?',$identifier)
				->limit(1);
		} elseif (is_int($identifier)) {
			$select = $Units->select()
				->from($Units->_name,'unit_sid')
				->where('unit_id = ?',$identifier)
				->limit(1);
		}
		$row = $Units->fetchRow($select);
		
		return $row->unit_sid;
	}
	
	public function unit_name($unit_id) {
		$Units = new Chamista_Model_DbTable_Units();
		$row = $Units->fetchRow($Units->select()->from($Units->_name,'unit_name')->where('unit_id = ?',$unit_id)->limit(1));
		return $row->unit_name;
	}
	
	public function orderBy($data,$field,$asc = 1) {
		if ($asc == 1)
			$code = 'return strnatcmp($a["'.$field.'"], $b["'.$field.'"]);';
		else
			$code = 'return strnatcmp($b["'.$field.'"], $a["'.$field.'"]);';
		usort($data, create_function('$a,$b', $code));
		return $data;
	}
	
	public function getSatisfactionImg($satisfaction) {
		switch ($satisfaction) {
			case 'Euphoric':
				return 'ecstatic';
				break;
			case 'Happy':
				return 'happy';
				break;
			case 'Neutral':
				return 'neutral';
				break;
			case 'Sad':
				return 'sad';
				break;
			case 'Angry':
				return 'outraged';
				break;
		}
	}
	
	public function getSpyMission($mission) {
		switch ($mission) {
			case 0:
				return 'Your spy is awaiting new orders.';
			case 1:
				return 'Spy is on his way';
		}
	}
}