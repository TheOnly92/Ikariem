<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Palace colony controller file.
 * Handle all actions redirected to the palace colony controller.
 * Last updated: $LastChangedDate$
 * 
 * @author        TheOnly92
 * @copyright    (c) 2008-2010 Rewaz Labs.
 * @package        Project Chamista
 * @version        $LastChangedRevision$
 */

class PalacecolonyController extends Rewaz_Controller_Action_Buildings {
    public function preDispatch() {
        parent::preDispatch();

        $auth = Zend_Auth::getInstance();
        $request = $this->getRequest();
        $User_data = $auth->getStorage()->read();
        $User = new Chamista_Model_User($User_data->usr_id);

        $town_id = $request->getParam('id');
        if (!$town_id) {
            $town_id = $request->getParam('cityId');
            if (!$town_id)
                $town_id = Zend_Registry::get('session')->current_city;
        }

        $Town = new Chamista_Model_Town($town_id);

        $Trailer = Zend_Registry::get('trailer');
        $Trailer->addStep($Town->town_name,'/city/index/id/'.$Town->town_id, 'Back to the town');

        $position = intval($request->getParam('position'));
        if ($Town->getGroundBuilding($position) != Chamista_Model_Formula::GOVNR_RESIDENCE) {
            throw new Exception('Invalid URL!');
        }
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $request = $this->getRequest();
        $User_data = $auth->getStorage()->read();
        $User = new Chamista_Model_User($User_data->usr_id);

        $town_id = $request->getParam('id');

        $Town = new Chamista_Model_Town($town_id);

        $position = intval($request->getParam('position'));

        $Trailer = Zend_Registry::get('trailer');
        $Trailer->addStep('Governor`s Residence');

        if ($Town->isBeingExpanded($position)) {
            $Buildings_Queue = new Chamista_Model_DbTable_Buildings_Queue();
            $row = $Buildings_Queue->fetchRow($Buildings_Queue->select()
                ->where('queue_town = ?',$Town->town_id)
                ->where('queue_type = ?',Chamista_Model_Formula::GOVNR_RESIDENCE)
                ->where('queue_pos = ?',$position));
            $now = time();
            $this->view->construction = array(
                'startdate' => $row->queue_created,
                'enddate' => $row->queue_endtime,
                'currentdate' => $now,
                'building' => Chamista_Model_Format::building_action(Chamista_Model_Formula::GOVNR_RESIDENCE),
                'upgradeCountDown' => Chamista_Model_Format::formatTime($row->queue_endtime - $now),
                'build_lvl' => $Town->getPositionLvl($position),
                'progress' => floor(($row->queue_endtime - $now) / ($row->queue_endtime - $row->queue_created) * 100)
            );
        }
		
        $palace = array();
        $towns = array();
        $townRows = $User->getUserCityIDs();
        foreach ($townRows as $townRow) {
        	$CurrTown = new Chamista_Model_Town($townRow->town_id);
        	$towns[] = array(
        		'capital' => $CurrTown->isCapital(),
        		'name' => $CurrTown->town_name,
        		'level' => $CurrTown->getBuildingLvl(Chamista_Model_Formula::TOWN_HALL),
        		'palace' => $CurrTown->getPalaceLvl(),
        		'island' => array(
        			'name' => $CurrTown->getIslandName(),
        			'pos' => $CurrTown->getIslandXY()
        		),
        		'resource' => array(
        			'css' => Chamista_Model_Format::tradegood_css($CurrTown->getIslandResource()),
        			'name' => Chamista_Model_Format::tradegood_name($CurrTown->getIslandResource())
        		)
        	);
        }
        $palace['towns'] = $towns;
        
        $this->view->palace = $palace;
        $this->view->position = $position;
        $this->view->build_lvl = $Town->getPositionLvl($position);
        $this->view->town_id = $Town->town_id;
        $this->view->upgrade_cost = Chamista_Model_Formula::getBuildCost(Chamista_Model_Formula::GOVNR_RESIDENCE,$this->view->build_lvl + 1,$User);
        $this->view->upgrade_time = Chamista_Model_Format::formatTimeFromArray(Chamista_Model_Formula::getBuildTime(Chamista_Model_Formula::GOVNR_RESIDENCE,$this->view->build_lvl + 1,true));
        $this->view->build_name = Chamista_Model_Format::building_plaintext(Chamista_Model_Formula::GOVNR_RESIDENCE);
        $this->view->css = 'ik_palaceColony_'.VERSION.'.css';
        $this->view->body_id = 'palaceColony';
    }
}
