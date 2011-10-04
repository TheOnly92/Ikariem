<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Mayor controller file.
 * Handle all actions redirected to the tradeAdvisor controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class TradeadvisorController extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		
		$Trailer = Zend_Registry::get('trailer');
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$town_id = $request->getParam('id');
		if (!$town_id) {
			$town_id = Zend_Registry::get('session')->current_city;
		}
		$Town = new Chamista_Model_Town($town_id);
		
		$Trailer->addStep('Mayor');
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Towns = new Chamista_Model_DbTable_Towns();
		$Events = new Chamista_Model_DbTable_Events();
		
		$page = 1;
		if ($request->getParam('page')) $page = intval($request->getParam('page'));
		$rows = $Events->fetchAll($Events->select()->where('event_uid = ?',$User->usr_id)->order('event_date DESC')->limitPage($page,10));
		$events_view = array();
		foreach ($rows as $row) {
			$events_view[] = array(
				'location' => $row->event_location,
				'location_name' => $Towns->getData('town_name',$row->event_location),
				'date' => date('d.m.Y H:i',$row->event_date),
				'subject' => $row->event_desc,
				'event_new' => $row->event_new
			);
		}
		$where = array(
			$Events->getAdapter()->quoteInto('event_uid = ?',$User->usr_id),
			$Events->getAdapter()->quoteInto('event_new = ?',1),
		);
		$Events->update(array('event_new' => 0),$where);
		$row = $Events->fetchRow($Events->select()->from($Events->_name,'COUNT(*) AS total')->where('event_uid = ?',$User->usr_id));
		
		$this->view->events_no = $row->total;
		$this->view->page = $page;
		$this->view->events = $events_view;
		$this->view->css = 'ik_tradeAdvisor_'.VERSION.'.css';
		$this->view->body_id = 'tradeAdvisor';
	}
}