<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Page initialization action helper.
 * This helper initializes all variables and classes needed to be accessed by the view
 * frequently across all pages.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Rewaz_Controller_Action_Helper_Init extends Zend_Controller_Action_Helper_Abstract {
	public function init() {
		$auth = Zend_Auth::getInstance();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$session = Zend_Registry::get('session');
		$Trailer = new Chamista_Model_Trailer();
		Zend_Registry::set('trailer',$Trailer);
		
		// If no current city is set, use the user's capital
		if (!isset($session->current_city)) {
			$session->current_city = $User->usr_capital;
		}
		
		$Curr_City = new Chamista_Model_Town($session->current_city,true);
		
		// Every page will have a "World" link at the breadcrumb
		$pos = $Curr_City->getIslandXY();
		if ($this->getRequest()->getControllerName() != 'island')
			$Trailer->addStep('<img src="/img/layout/icon-world.gif" alt="World" /> World','/worldmap/index/islandx/'.$pos['x'].'/islandy/'.$pos['y'],'Back to the world map');
		
		$this->view->wealth = $User->userResearched(10);
		$this->view->cities = $User->getUserCities();
		$this->view->usr_citySelectOptions = $User->usr_citySelectOptions;
		//$this->view->Curr_City = $Curr_City;
		$this->view->layout = array(
			'resources' => array(
				'wood' => $Curr_City->getTownResource(0),
				'wine' => $Curr_City->getTownResource(1),
				'marble'=> $Curr_City->getTownResource(2),
				'crystal' => $Curr_City->getTownResource(3),
				'sulfur' => $Curr_City->getTownResource(4),
				'resource' => $Curr_City->getTownResource($Curr_City->getIslandResource()),
			),
			'maxCapacity' => array(
				'wood' => $Curr_City->getWarehouseCap(0),
				'wine' => $Curr_City->getWarehouseCap(1),
				'marble'=> $Curr_City->getWarehouseCap(2),
				'crystal' => $Curr_City->getWarehouseCap(3),
				'sulfur' => $Curr_City->getWarehouseCap(4),
				'resource' => $Curr_City->getWarehouseCap($Curr_City->getIslandResource()),
			),
			'townId' => $Curr_City->town_id,
			'islandId' => $Curr_City->town_island,
			'freeCitizens' => $Curr_City->getFreeCitizens(),
			'population' => floor($Curr_City->town_population),
			'tradingPost' => array(
				'wood' => $Curr_City->getTradingPost(0),
				'wine' => $Curr_City->getTradingPost(1),
				'marble'=> $Curr_City->getTradingPost(2),
				'crystal' => $Curr_City->getTradingPost(3),
				'sulfur' => $Curr_City->getTradingPost(4),
			),
			'production' => array(
				'wood' => floor($Curr_City->getWoodProduction()),
				'resource' => floor($Curr_City->getResourceProduction()),
			),
			'resource' => Chamista_Model_Format::tradegood_plaintext($Curr_City->getIslandResource()),
			'actionPoints' => $Curr_City->getAvailActionPoints(),
			'oldController' => $this->getRequest()->getControllerName(),
			'oldAction' => $this->getRequest()->getActionName(),
		);
		if ($this->getRequest()->getParam('id')) {
			$this->view->layout['oldId'] = $this->getRequest()->getParam('id');
		}
		if ($this->getRequest()->getParam('position')) {
			$this->view->layout['oldPos'] = $this->getRequest()->getParam('position');
		}
		$this->view->User = $User;
		$this->view->trail = $Trailer;
		
		$Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
		$townRows = $User->getUserCityIDs();
		$towns = array();
		foreach ($townRows as $townRow) {
			$towns[] = $townRow->town_id;
		}
		$newETA = $Buildings_Queue->fetchRow($Buildings_Queue->select()->from($Buildings_Queue->_name,'queue_endtime')
			->where('queue_town IN (?)',$towns)->order('queue_endtime ASC'));
		if ($newETA)
			$this->view->newETA = $newETA->queue_endtime;
		else
			$this->view->newETA = 0;

        if (ENV == 'test') {
            $rev = file_get_contents(APP_PATH.'/../version.txt');
            $this->view->VERSION = VERSION.' r'.$rev;
        } else {
            $this->view->VERSION = VERSION;
        }
	}
}