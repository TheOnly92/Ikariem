<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Transport controller file.
 * Handle all actions redirected to the transport controller.
 * Last updated: $LastChangedDate$
 * 
 * @author        TheOnly92
 * @copyright    (c) 2008-2010 Rewaz Labs.
 * @package        Project Chamista
 * @version        $LastChangedRevision$
 */

class SendspyController extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$destinationCityId = intval($request->getParam('destinationCityId'));
		$DestinationTown = new Chamista_Model_Town($destinationCityId);
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		if ($Town->getAvailSpies() == 0) {
			$this->_helper->flashMessenger->addMessage("You have no spies available!");
			$this->_helper->redirector('index','error');
		}
		
		$Islands = new Chamista_Model_DbTable_Islands();
		$row = $Islands->fetchRow($Islands->select()->where('island_id = ?', $DestinationTown->town_island));
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep($row->island_name.' ['.$row->island_posx.':'.$row->island_posy.']','/island/index/'.$DestinationTown->town_island, 'Back to the island');
		$Trailer->addStep('Send out spy');
		
		$this->view->sendSpy = array(
			'islandId' => $DestinationTown->town_island,
			'destinationCityId' => $destinationCityId,
			'townName' => $DestinationTown->town_name,
			'townSize' => $DestinationTown->town_lvl,
			'risk' => Chamista_Model_Formula::getRisk(1,$Town,$DestinationTown),
			'arrivalTime' => time() + Chamista_Model_Formula::travelTime(240, $Town->getIslandXY(), $DestinationTown->getIslandXY()),
		);
		
		$this->view->css = 'ik_sendSpy_'.VERSION.'.css';
		$this->view->body_id = 'sendSpy';
	}
	
	public function espionageAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Transports = new Chamista_Model_DbTable_Transports();
		$Islands = new Chamista_Model_DbTable_Islands();
		$Towns = new Chamista_Model_DbTable_Towns();
		$now = time();
		
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		
		if ($Town->getAvailSpies() == 0) {
			$this->_helper->flashMessenger->addMessage("You have no spies available!");
			$this->_helper->redirector('index','error');
		}
		
		$destinationCityId = intval($request->getParam('destinationCityId'));
		$DestinationTown = new Chamista_Model_Town($destinationCityId);
		
		$islandId = (int) $request->getParam('islandId');
		if ($Islands->fetchRow($Islands->select()->from($Islands->_name,'COUNT(*) AS total')->where('island_id = ?',$islandId))->total == 0) {
			$this->_helper->flashMessenger->addMessage("Invalid island!");
			$this->_helper->redirector('index','error');
		}
		
		$Mission = new Chamista_Model_DbTable_Spies_Mission();
		$Mission->sendSpy($Town, $DestinationTown);
		
		$this->_helper->redirector('index','island','',array('id' => $islandId));
	}
}