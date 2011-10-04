<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Tour controller file.
 * A very simple controller file with nothing much to do.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class TourController extends Zend_Controller_Action {
	public function predispatch() {
		$this->_helper->layout->setLayout('guest');
	}
	
	public function indexAction() {
		return $this->_helper->redirector('step1');
	}
	
	public function step1Action() {
		// No need to do anything here
	}
	
	public function step2Action() {
		// No need to do anything here too
	}
	
	public function step3Action() {
		// Nothing to do here
	}
	
	public function step4Action() {
		// Nothing here :P
	}
	
	public function step5Action() {
		// These actions have nothing to control
	}
}