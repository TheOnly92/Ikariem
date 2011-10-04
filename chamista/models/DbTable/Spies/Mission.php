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

class Chamista_Model_DbTable_Spies_Mission extends Zend_Db_Table {
	protected $_name = 'spies_mission';
	protected $_key = 'spy_id';
	
	public function sendSpy(Chamista_Model_Town $Source, Chamista_Model_Town $Destination) {
		$insert = array(
			'spy_origin' => $Source->town_id,
			'spy_mission' => 1,
			'spy_destination' => $Destination->town_id,
			'spy_risk' => Chamista_Model_Formula::getRisk(1,$Source,$Destination),
			'spy_arrivaltime' => time() + Chamista_Model_Formula::travelTime(240, $Source->getIslandXY(), $Destination->getIslandXY()),
		);
		
		$this->insert($insert);
	}
	
	public function getBusySpies($townId) {
		return $this->fetchRow(
			$this->select()
				->from($this->_name, 'COUNT(*) AS total')
				->where('spy_origin = ?', $townId)
		)->total;
	}
	
	public function getTownSpies($townId) {
		return $this->fetchAll(
			$this->select()
				->where('spy_origin = ?', $townId)
		);
	}
	
	public function setSpyMission($spyId, $mission, $arrivalTime = 0) {
		$update = array(
			'spy_mission' => $mission,
			'spy_arrivaltime' => $arrivalTime,
		);
		$where = $this->getAdapter()->quoteInto('spy_id = ?', $spyId);
		$this->update($update, $where);
	}
	
	public function spyValid($spyId, $ownerId) {
		if ($this->fetchRow($this->select()->from($this->_name, 'COUNT(*) AS total')->where('spy_id = ?', $spyId)->where('spy_origin = ?', $ownerId))->total > 0) {
			return true;
		}
		return false;
	}
	
	public function getSpyInfo($spyId, $ownerId) {
		if ($this->spyValid($spyId, $ownerId)) {
			return $this->fetchRow(
				$this->select()
					->where('spy_id = ?', $spyId)
			);
		}
		return false;
	}
	
	public function removeSpy($spyId) {
		$where = $this->getAdapter()->quoteInto('spy_id = ?', $spyId);
		$this->delete($where);
	}
	
	public function hasSpy($ownerTown, $targetTown) {
		if ($this->fetchRow($this->select()->from($this->_name, 'COUNT(*) AS total')->where('spy_origin = ?', $ownerTown)->where('spy_destination = ?', $targetTown))->total > 0) {
			return true;
		}
		return false;
	}
}