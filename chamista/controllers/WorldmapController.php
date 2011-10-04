<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * World map controller file.
 * Handle all actions redirected to the world map controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class WorldmapController extends Zend_Controller_Action {
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
		$request = $this->getRequest();
		$Islands = new Chamista_Model_DbTable_Islands();
		$town_id = Zend_Registry::get('session')->current_city;
		$Town = new Chamista_Model_Town($town_id);
		if (!$request->getParam('islandx') && !$request->getParam('island_y')) {
			$pos = $Town->getIslandXY();
			$island_name = $Town->getIslandName();
		} else {
			$pos = array('x' => intval($request->getParam('islandx')), 'y' => intval($request->getParam('islandy')));
			$island_name = $Islands->fetchRow($Islands->select()->from($Islands->_name,'island_name')->where('island_posx = ?',$pos['x'])->where('island_posy = ?',$pos['y']))->island_name;
		}
		
		$this->view->x_min = $pos['x'] - 9;
		$this->view->x_max = $pos['x'] + 9;
		$this->view->y_min = $pos['y'] - 9;
		$this->view->y_max = $pos['y'] + 9;
		$this->view->start_x = $pos['x'];
		$this->view->start_y = $pos['y'];
		$this->view->json = json_encode($Islands->generateJSON($this->view->x_min,$this->view->x_max,$this->view->y_min,$this->view->y_max));
		$this->view->island_name = $island_name;
		$this->view->town_id = $town_id;
		$this->view->css = 'ik_worldmap_iso_'.VERSION.'.css';
		$this->view->body_id = 'worldmap_iso';
	}
	
	public function getjsonareaAction() {
		$this->_helper->layout->disableLayout();
		$request = $this->getRequest();
		$Islands = new Chamista_Model_DbTable_Islands();
		
		$x_min = intval($request->getParam('x_min'));
		$x_max = intval($request->getParam('x_max'));
		$y_min = intval($request->getParam('y_min'));
		$y_max = intval($request->getParam('y_max'));
		
		$this->_helper->viewRenderer->setNoRender(true);
		echo json_encode($Islands->generateJSON($x_min,$x_max,$y_min,$y_max));
	}
}