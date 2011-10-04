<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Register contoller file.
 * Do the user account registrations here.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class RegisterController extends Zend_Controller_Action {
	public function indexAction() {
		$this->_helper->layout->setLayout('guest');
		
		$request = $this->getRequest();
		
		// Determine if processing a post request (means registering)
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim'
			);
			
			$validation = array(
				'usr_nick' => array(
					array('StringLength',5,40),
					array('NotEmpty'),
					array('Db_NoRecordExists','table' => 'users', 'field' => 'usr_nick'),
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'The nickname you entered is too short.',
							Zend_Validate_StringLength::TOO_LONG => 'The nickname you entered is too long.',
						),
						'You forgot to enter your nickname.',
						'The nickname you entered has already been taken!',
					),
				),
				'usr_password' => array(
					array('StringLength',8,15),
					array('NotEmpty'),
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'The password you entered is too short.',
							Zend_Validate_StringLength::TOO_LONG => 'The password you entered is too long.',
						),
						'You forgot to enter your password.'
					)
				),
				'usr_email' => array(
					array('EmailAddress'),
					array('Db_NoRecordExists','table' => 'users', 'field' => 'usr_email'),
					array('NotEmpty'),
					Zend_Filter_Input::MESSAGES => array(
						array(Zend_Validate_EmailAddress::INVALID_FORMAT => 'The e-mail address you entered is invalid.'),
						'The e-mail address you entered has already registered an account!',
						'You forgot to enter your e-mail.',
					)
				),
				'agb' => array(
					'presence' => 'required',
				)
			);
			
			// Initialize Zend_Filter_Input passing it the entire getPost() array
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			$zfi->setOptions(array(Zend_Filter_Input::MISSING_MESSAGE => 'You must agree to the terms and conditions to continue.'));
			
			// If the validators passed this will be true
			if ($zfi->isValid()) {
				// Fetch the data from zfi directly and create an array for Zend_Db
				$clean = array();
				$clean['usr_nick'] = $zfi->usr_nick;
				$clean['usr_password'] = $zfi->usr_password;
				$clean['usr_email'] = $zfi->usr_email;
				
				$users = new Chamista_Model_DbTable_Users();
				$users->createNewUser($clean);
				
				$this->_helper->flashMessenger->addMessage($clean['usr_nick']);
				$this->_helper->flashMessenger->addMessage($clean['usr_email']);
				$this->getHelper('redirector')->goto('success');
			} else {
				// The form didn't validate, get the messages from ZFI
				foreach ($zfi->getMessages() as $field => $messages) {
					// Put each ZFI message into the FlashMessenger so it shows on the form
					foreach ($messages as $message) {
						$this->getHelper('FlashMessenger')->addmessage($message);
					}
				}
				
				// Record the form values too, for the user to edit
				$prev_form = new Zend_Session_Namespace('prevRegistrationForm');
				$prev_form->setExpirationHops(1);
				$filter = new Zend_Filter_StripTags();
				$prev_form->data = array(
					'usr_nick' => $filter->filter($request->getParam('usr_nick')),
					'usr_email' => $filter->filter($request->getParam('usr_email'))
				);
				
				// Redirect back to the input form, but with messages
				$this->getHelper('redirector')->goto('index');
			}
		}
		
		// Not a post request, check for flash messages and expose to the view
		if ($this->getHelper('FlashMessenger')->hasMessages()) {
			$this->view->messages = $this->getHelper('FlashMessenger')->getMessages();
		}
		
		$prev_form = new Zend_Session_Namespace('prevRegistrationForm');
		if (isset($prev_form->data)) {
			$this->view->usr_nick = $prev_form->data['usr_nick'];
			$this->view->usr_email = $prev_form->data['usr_email'];
		}
		
		$this->view->servers = Chamista_Model_Formula::getServers();
		
	}
	
	public function successAction() {
		$this->_helper->layout->setLayout('guest');
		
		$messages = $this->_helper->flashMessenger->getMessages();
		$this->view->usr_nick = $messages[0];
		$this->view->usr_email = $messages[1];
	}
}