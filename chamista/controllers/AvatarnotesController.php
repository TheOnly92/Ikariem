<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Avatar notes controller file.
 * Handle all actions redirected to the avatar notes controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class AvatarnotesController extends Zend_Controller_Action {
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
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$this->_helper->layout->disableLayout();
		$Users_Notes = new Chamista_Model_DbTable_Users_Notes();
		
		$this->view->notes = $Users_Notes->getNotes($User_data->usr_id);
	}
	
	public function saveavatarnotesAction() {
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$Users_Notes = new Chamista_Model_DbTable_Users_Notes();
		
		$notes = $request->getParams('notes');
		$Users_Notes->updateNotes($User->usr_id, $notes);
		
		$this->_helper->viewRenderer->setNoRender(true);
	}
}