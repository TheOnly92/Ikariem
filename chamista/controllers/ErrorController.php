<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Error controller file.
 * Handles and displays all errors.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class ErrorController extends Zend_Controller_Action {
	public function indexAction() {
        $auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
		if ($this->getHelper('FlashMessenger')->hasMessages()) {
			$this->view->messages = $this->getHelper('FlashMessenger')->getMessages();
		} else {
			$this->_helper->redirector('index','city');
		}
		$this->view->body_id = 'error';
		$this->view->css = 'ik_error_'.VERSION.'.css';
		
		$dynamics = new Zend_Session_Namespace('dynamics');
		$this->view->dynamics = $dynamics->box;
	}
	
	public function applicationAction() {
		$this->getResponse()->clearBody();
		$this->_helper->layout->disableLayout();
		
		$errors = $this->_getParam('error_handler');
		
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()
					->setRawHeader('HTTP/1.1 404 Not Found');
				$this->view->message = 'Page is not found';
				break;
			default:
				$exception = $errors->exception;
				echo('<pre>');
				var_dump($exception);exit;
				$this->view->message = '<pre>'.$exception->getMessage()."\n".$exception->getTraceAsString().'</pre>';
				break;
		}
	}
}