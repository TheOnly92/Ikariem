<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Version controller file.
 * Handles and displays version changelog.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class VersionController extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
	}
	
	public function indexAction() {
		$this->view->body_id = 'version';
		$this->view->css = 'ik_version_'.VERSION.'.css';
	}
}