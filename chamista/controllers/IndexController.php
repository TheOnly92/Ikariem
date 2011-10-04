<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Index controller file.
 * For guests and logging in, with forgot password.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class IndexController extends Zend_Controller_Action {
	protected $storage;
	
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity() && $this->getRequest()->getActionName() != 'logout') {
			$this->_helper->redirector('index','city');
		}
	}
	
	public function indexAction() {
		$this->_helper->layout->setLayout('guest');
		
		if ($this->getHelper('FlashMessenger')->hasMessages()) {
			$this->view->messages = $this->getHelper('FlashMessenger')->getMessages();
		}
		
		$this->view->servers = Chamista_Model_Formula::getServers();
	}
	
	public function logoutAction() {
		Zend_Session::destroy(true, false);
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index');
	}
	
	public function loginAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'usr_nick' => array(
					array('StringLength',5,40),
					array('NotEmpty'),
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'Wrong nickname or password.',
							Zend_Validate_StringLength::TOO_LONG => 'Wrong nickname or password.',
						),
						'Wrong nickname or password.'
					),
				),
				'usr_password' => array(
					array('StringLength',8,15),
					array('NotEmpty'),
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'Wrong nickname or password.',
							Zend_Validate_StringLength::TOO_LONG => 'Wrong nickname or password.',
						),
						'Wrong nickname or password.'
					),
				),
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $this->getRequest()->getPost());
			
			if ($zfi->isValid()) {
				// Create a new Zend_Auth_Adapter
				$adapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'),'users','usr_nick','usr_password','md5(?)');
				
				// Set the identity and credential fields to validated values
				$adapter->setIdentity($zfi->usr_nick)
					->setCredential($zfi->usr_password);
				
				// Get an instance of Zend_Auth and authenticate() using the adapter
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate($adapter);
				
				// Check for authentication success
				if ($result->isValid()) {
					$auth->getStorage()->write($adapter->getResultRowObject());
					
					// @TODO Check for vacation mode
					$User_data = $auth->getStorage()->read();
					$User = new Chamista_Model_User($User_data->usr_id);
					if ($User->usr_vacation > 0) {
						// If less than 48 hours
						if (time() - $User->usr_vacation < 48 * 3600) {
							$this->view->usr_nick = $User->usr_nick;
							$this->view->waittime = Chamista_Model_Format::formatTime(48 * 3600 - (time() - $User->usr_vacation));
							Zend_Auth::getInstance()->clearIdentity();
							$this->_helper->layout->setLayout('guest');
							$this->render('vacation');
							return;
						} else {
							$User->usr_vacation = 0;
							$townRows = $User->getUserCityIDs();
							$Towns = new Chamista_Model_DbTable_Towns();
							foreach ($townRows as $townRow) {
								$update = array(
									'town_update' => time()
								);
								$where = $Towns->getAdapter()->quoteInto('town_id = ?',$townRow->town_id);
								$Towns->update($update,$where);
							}
						}
					}
					
					$this->_helper->redirector('index','city');
				} else {
					$this->getHelper('FlashMessenger')->addMessage('Wrong nickname or password.');
					$this->_helper->redirector('index');
				}
			} else {
				foreach ($zfi->getMessages() as $messages) {
					foreach ($messages as $message) {
						$this->_helper->flashMessenger->addMessage($message);
					}
				}
				$this->_helper->redirector('index');
			}
		} else {
			$this->_helper->redirector('index');
		}
	}
}