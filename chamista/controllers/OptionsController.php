<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Options controller file.
 * Handles all actions redirected to the options controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class OptionsController extends Zend_Controller_Action {
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
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Settings');
		
		$this->view->citySelectOptions = array(
			0 => 'No details',
			1 => 'Show coordinates in town navigation',
			2 => 'Trade goods'
		);
		
		$this->view->usr_nick = $User->usr_nick;
		$this->view->usr_id = $User->usr_id;
		$this->view->usr_email = $User->usr_email;
		$this->view->usr_citySelectOptions = $User->usr_citySelectOptions;
		$this->view->town_id = Zend_Registry::get('session')->current_city;
		$this->view->css = 'ik_options_'.VERSION.'.css';
		$this->view->body_id = 'options';
		
		if ($this->getHelper('FlashMessenger')->hasMessages()) {
			$this->view->messages = $this->getHelper('FlashMessenger')->getMessages();
		}
	}
	
	public function changeavatarvaluesAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim',
				'citySelectOptions' => 'Digits'
			);
			
			$validation = array(
				'oldPassword' => array(
					array('StringLength',8,15),
					Zend_Filter_Input::ALLOW_EMPTY => true,
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'The password you entered is too short.',
							Zend_Validate_StringLength::TOO_LONG => 'The password you entered is too long.',
						),
					)
				),
				'newPassword' => array(
					array('StringLength',8,15),
					Zend_Filter_Input::ALLOW_EMPTY => true,
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'The password you entered is too short.',
							Zend_Validate_StringLength::TOO_LONG => 'The password you entered is too long.',
						),
					)
				),
				'newPasswordConfirm' => array(
					array('StringLength',8,15),
					Zend_Filter_Input::ALLOW_EMPTY => true,
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'The password you entered is too short.',
							Zend_Validate_StringLength::TOO_LONG => 'The password you entered is too long.',
						),
					)
				),
				'citySelectOptions' => array(
					'Int',
					array('LessThan',3),
					array('GreaterThan',-1)
				)
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if ($zfi->isValid()) {
				$clean['oldPassword'] = $zfi->oldPassword;
				$clean['newPassword'] = $zfi->newPassword;
				$clean['newPasswordConfirm'] = $zfi->newPasswordConfirm;
				$clean['citySelectOptions'] = $zfi->citySelectOptions;
				
				if ($zfi->citySelectOptions != $User->usr_citySelectOptions) {
					$User->usr_citySelectOptions = $zfi->citySelectOptions;
				}
				
				if (!empty($clean['newPassword'])) {
					if ($clean['newPassword'] != $clean['newPasswordConfirm'] || md5($clean['oldPassword']) != $User->usr_password) {
						$this->getHelper('FlashMessenger')->addmessage('The password you entered is wrong!');
						$this->_helper->redirector('index');
					}
					
					$User->usr_password = md5($clean['newPassword']);
				}
			} else {
				foreach ($zfi->getMessages() as $field => $messages) {
					// Put each ZFI message into the FlashMessenger so it shows on the form
					foreach ($messages as $message) {
						$this->getHelper('FlashMessenger')->addmessage($message);
					}
				}
			}
		}
		
		$this->_helper->redirector('index');
	}
	
	public function umodconfirmAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$request = $this->getRequest();
		
		$this->view->body_id = 'options_umod_confirm';
	}
	
	// Confirm vacation, set user to vacation mode and logout
	public function activatevacationmodeAction() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$request = $this->getRequest();
		$Transports = new Chamista_Model_DbTable_Transports();
		
		// Remove scientists and lumber and miner and etc
		$cityIds = $User->getUserCityIDs();
		foreach ($cityIds as $cityId) {
			$Town = new Chamista_Model_Town($cityId->town_id);
			$Town->town_lumber = 0;
			$Town->town_miner = 0;
			$Town->town_scientists = 0;
			$Town->town_wine = 0;
			
			// If there are any related trade routes
			$transportRows = $Transports->fetchAll(
				$Transports->select()
					->where('trans_origin = ?',$cityId->town_id)
					->orWhere('trans_destination = ?',$cityId->town_id)
			);
			foreach ($transportRows as $transportRow) {
				// If transporting to other city
				if ($transportRow->trans_origin == $cityId->town_id) {
					switch ($transportRow->trans_mission) {
						// Loading, just stop loading and unload
						case 0:
							$cargo = unserialize($transportRow->trans_cargo);
							foreach ($cargo as $i => $v) {
								$Town->{'town_'.$i} += $v;
							}
							
							$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$transportRow->trans_id);
							$Transports->delete($where);
							break;
						// Transporting, return to port and unload goods
						case 1:
							$cargo = unserialize($transportRow->trans_cargo);
							foreach ($cargo as $i => $v) {
								$Town->{'town_'.$i} += $v;
							}
							
							$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$transportRow->trans_id);
							$Transports->delete($where);
							break;
						// Returning from foreign port, just return immediately
						case 2:
							$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$transportRow->trans_id);
							$Transports->delete($where);
							break;
					}
				} else {
					$Origin = new Chamista_Model_Town($transportRow->trans_origin);
					switch ($transportRow->trans_mission) {
						// Loading, just stop loading and unload
						case 0:
							$cargo = unserialize($transportRow->trans_cargo);
							foreach ($cargo as $i => $v) {
								$Origin->{'town_'.$i} += $v;
							}
							
							$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$transportRow->trans_id);
							$Transports->delete($where);
							break;
						// Transporting, return to port and unload goods
						case 1:
							$cargo = unserialize($transportRow->trans_cargo);
							foreach ($cargo as $i => $v) {
								$Origin->{'town_'.$i} += $v;
							}
							
							$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$transportRow->trans_id);
							$Transports->delete($where);
							break;
						// Returning from foreign port, just return immediately
						case 2:
							$where = $Transports->getAdapter()->quoteInto('trans_id = ?',$transportRow->trans_id);
							$Transports->delete($where);
							break;
					}
				}
			}
		}
		
		$User->usr_vacation = time();
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index');
	}
}