<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Diplomacy advisor controller file.
 * Handle all actions redirected to the diplomacyAdvisor controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class DiplomacyadvisorController  extends Zend_Controller_Action {
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_helper->redirector('index','index');
		}
		Rewaz_Controller_Action_Helper_Init::init();
	}
	
	public function indexAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		
		$total_in = $IKMessages->fetchRow($IKMessages->select()->from($IKMessages->_name,'COUNT(*) AS total')->where('message_receiver = ?',$User->usr_id)->where('message_deleted = ?',0))->total;
		$total_out = $IKMessages->fetchRow($IKMessages->select()->from($IKMessages->_name,'COUNT(*) AS total')->where('message_sender = ?',$User->usr_id)->where('message_outbox = ?',0))->total;
		
		$offset = intval($request->getParam('offset'));
		if ($offset < 1) $offset = 1;
		
		$messages_view = array();
		$messages = $IKMessages->fetchAll($IKMessages->select()->where('message_receiver = ?',$User->usr_id)->where('message_deleted = ?',0)->order('message_date DESC')->limitPage($offset,10));
		foreach ($messages as $message) {
			$Sender = new Chamista_Model_User($message->message_sender);
			$Sender_Town = new Chamista_Model_Town($message->message_town);
			$messages_view[] = array(
				'id' => $message->message_id,
				'new' => $message->message_new,
				'sender_name' => $Sender->usr_nick,
				'sender_id' => $message->message_sender,
				'subject' => $message->message_subject,
				'town' => array(
					'id' => $Sender_Town->town_id,
					'name' => $Sender_Town->town_name,
					'pos' => $Sender_Town->getIslandXY(),
					'island' => $Sender_Town->town_island
				),
				'date' => date('d.m.Y H:i:s', $message->message_date),
				'body' => $message->message_body
			);
		}
		
		$this->view->offset = $offset;
		$this->view->disp_messages = ($offset-1)*10 + count($messages);
		$this->view->messages = $messages_view;
		$this->view->total_in = $total_in;
		$this->view->total_out = $total_out;
		$this->view->body_id = 'diplomacyAdvisor';
		$this->view->css = 'ik_diplomacyAdvisor_'.VERSION.'.css';
	}
	
	public function outboxAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		
		$total_in = $IKMessages->fetchRow($IKMessages->select()->from($IKMessages->_name,'COUNT(*) AS total')->where('message_receiver = ?',$User->usr_id)->where('message_deleted = ?',0))->total;
		$total_out = $IKMessages->fetchRow($IKMessages->select()->from($IKMessages->_name,'COUNT(*) AS total')->where('message_sender = ?',$User->usr_id)->where('message_outbox = ?',0))->total;
		
		$offset = intval($request->getParam('offset'));
		if ($offset < 1) $offset = 1;
		
		$messages_view = array();
		$messages = $IKMessages->fetchAll($IKMessages->select()->where('message_sender = ?',$User->usr_id)->where('message_outbox = ?',0)->order('message_date DESC')->limitPage($offset,10));
		foreach ($messages as $message) {
			$Receiver = new Chamista_Model_User($message->message_receiver);
			$messages_view[] = array(
				'id' => $message->message_id,
				'receiver_name' => $Receiver->usr_nick,
				'receiver_id' => $message->message_receiver,
				'subject' => $message->message_subject,
				'date' => date('d.m.Y H:i:s', $message->message_date),
				'body' => $message->message_body
			);
		}
		
		$this->view->offset = $offset;
		$this->view->disp_messages = ($offset-1)*10 + count($messages);
		$this->view->messages = $messages_view;
		$this->view->total_in = $total_in;
		$this->view->total_out = $total_out;
		$this->view->body_id = 'diplomacyAdvisorOutBox';
		$this->view->css = 'ik_diplomacyAdvisorOutBox_'.VERSION.'.css';
	}
	
	public function sendikmessageAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		
		$receiverId = intval($request->getParam('receiverId'));
		if (!$receiverId) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$Receiver = new Chamista_Model_User($receiverId);
		if (!$Receiver) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$replyTo = intval($request->getParam('replyTo'));
		if ($replyTo) {
			$IKMessages = new Chamista_Model_DbTable_Ikmessages();
			$exist = $IKMessages->fetchRow($IKMessages->select()->from($IKMessages->_name,'COUNT(*) AS total')->where('message_id = ?',$replyTo))->total;
			if ($exist) {
				$message = $IKMessages->fetchRow($IKMessages->select()->where('message_id = ?',$replyTo)->where('message_receiver = ?',$User->usr_id));
				$body = $message->message_body;
				//$body = nl2br($body);
				$body = str_replace("\r",'',$body);	// Remove all \r's
				$body = explode("\n",$body);
				foreach ($body as $i=>$line) {
					$body[$i] = '> '.$line;
				}
				$body = implode("\n",$body);
				$Sender = new Chamista_Model_User($message->message_sender);
				$body = "\n\n\n\nOn the ".date('d.m.Y H:i:s', $message->message_date)." ".$Sender->usr_nick." wrote:\n".$body;
				$this->view->content = $body;
			}
		}
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Write Message');
		
		$this->view->body_id = 'sendIKMessage';
		$this->view->receiver_id = $receiverId;
		$this->view->receiver_name = $Receiver->usr_nick;
	}
	
	public function sendAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		
		$receiverId = intval($request->getParam('receiverId'));
		if (!$receiverId) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		$Receiver = new Chamista_Model_User($receiverId);
		if (!$Receiver) {
			$this->_helper->flashMessenger->addMessage("Access denied");
			$this->_helper->redirector('index','error');
		}
		
		if ($request->isPost()) {
			$filters = array(
				'*' => 'StringTrim',
				'msgType' => 'Digits'
			);
			
			$validation = array(
				'content' => array(
					array('StringLength',1,8000),
					'NotEmpty',
					Zend_Filter_Input::MESSAGES => array(
						array(
							Zend_Validate_StringLength::TOO_SHORT => 'You forgot to enter your content.',
							Zend_Validate_StringLength::TOO_LONG => 'Your message is too long.',
						),
						'You forgot to enter your content.',
					),
				),
				'msgType' => array(
					'presence' => 'required',
				)
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			if ($zfi->isValid()) {
				$clean = array();
				$clean['content'] = $zfi->content;
				$clean['msgType'] = $zfi->msgType;
				
				$insert = array(
					'message_sender' => $User->usr_id,
					'message_receiver' => $receiverId,
					'message_date' => time(),
					'message_town' => Zend_Registry::get('session')->current_city,
					'message_subject' => $clean['msgType'],
					'message_body' => $clean['content'],
					'message_new' => 1,
					'message_deleted' => 0
				);
				$IKMessages->insert($insert);
			} else {
				foreach ($zfi->getMessages() as $field => $messages) {
					// Put each ZFI message into the FlashMessenger so it shows on the form
					foreach ($messages as $message) {
						$this->getHelper('FlashMessenger')->addmessage($message);
					}
					$this->_helper->redirector('index','error');
				}
			}
		}
		$this->_helper->redirector('index');
	}
	
	public function markmessageasreadAction() {
		$this->_helper->layout->disableLayout();
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		
		$id = intval($request->getParam('id'));
		$update = array('message_new' => 0);
		$where = array(
			$IKMessages->getAdapter()->quoteInto('message_id = ?',$id),
			$IKMessages->getAdapter()->quoteInto('message_receiver = ?',$User->usr_id)
		);
		$IKMessages->update($update,$where);
		$this->_helper->viewRenderer->setNoRender(true);
	}
	
	public function markasdeletedbyreceiverAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		
		$id = intval($request->getParam('id'));
		$update = array('message_deleted' => 1);
		$where = array(
			$IKMessages->getAdapter()->quoteInto('message_id = ?',$id),
			$IKMessages->getAdapter()->quoteInto('message_receiver = ?',$User->usr_id)
		);
		$IKMessages->update($update,$where);
		$this->_helper->redirector('index');
	}
	
	public function processusermessageAction() {
		$auth = Zend_Auth::getInstance();
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$IKMessages = new Chamista_Model_DbTable_Ikmessages();
		
		$posts = $request->getPost();
		if ($request->getParam('pos') == 'outbox') {
			// Process outbox action
			$deleteId = $posts['deleteId'];
			foreach ($deleteId as $message_id => $v) {
				if ($v == 1) {
					$message_id = intval($message_id);
					$update = array('message_outbox' => 1);
					$where = array(
						$IKMessages->getAdapter()->quoteInto('message_id = ?',$message_id),
						$IKMessages->getAdapter()->quoteInto('message_sender = ?',$User->usr_id)
					);
					$IKMessages->update($update,$where);
				}
			}
			$this->_helper->redirector('outBox');
		} elseif ($request->getParam('pos') == 'inbox') {
			if (isset($posts['555'])) {
				// Mark as read
				$deleteId = $posts['deleteId'];
				foreach ($deleteId as $message_id => $v) {
					if ($v == 'read') {
						$message_id = intval($message_id);
						$update = array('message_new' => 0);
						$where = array(
							$IKMessages->getAdapter()->quoteInto('message_id = ?',$message_id),
							$IKMessages->getAdapter()->quoteInto('message_receiver = ?',$User->usr_id)
						);
						$IKMessages->update($update,$where);
					}
				}
				$this->_helper->redirector('index');
			} elseif (isset($posts['666'])) {
				// Delete
				$deleteId = $posts['deleteId'];
				foreach ($deleteId as $message_id => $v) {
					if ($v == 'read') {
						$message_id = intval($message_id);
						$update = array('message_deleted' => 1);
						$where = array(
							$IKMessages->getAdapter()->quoteInto('message_id = ?',$message_id),
							$IKMessages->getAdapter()->quoteInto('message_receiver = ?',$User->usr_id)
						);
						$IKMessages->update($update,$where);
					}
				}
				$this->_helper->redirector('index');
			}
		}
	}
}