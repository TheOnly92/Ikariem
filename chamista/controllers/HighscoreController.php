<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Highscores controller file.
 * Handles all actions redirected to the highscores controller.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class HighscoreController extends Zend_Controller_Action {
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
		$request = $this->getRequest();
		$User_data = $auth->getStorage()->read();
		$User = new Chamista_Model_User($User_data->usr_id);
		$Users = new Chamista_Model_DbTable_Users();
		$Highscores = new Chamista_Model_DbTable_Highscores();
		
		$Trailer = Zend_Registry::get('trailer');
		$Trailer->addStep('Highscore');
		
		$fieldName = 'highscore_total';
		$highscoreType = 'score';
		$offset = 0;
		$searchId = 0;
		$searchUser = '';
		if ($request->getParam('showMe') == 1) $offset = -1;
		if ($request->isPost()) {
			$post = $request->getPost();
			if (isset($post['highscoreType'])) {
				switch ($post['highscoreType']) {
					case 'building_score_main':
						$fieldName = 'highscore_builders';
						break;
					case 'building_score_secondary':
						$fieldName = 'highscore_buildlvl';
						break;
					case 'research_score_main':
						$fieldName = 'highscore_scientists';
						break;
					case 'research_score_secondary':
						$fieldName = 'highscore_research';
						break;
					case 'army_score_main':
						$fieldName = 'highscore_military';
						break;
					case 'trader_score_secondary':
						$fieldName = 'highscore_gold';
						break;
					case 'offense':
						$fieldName = 'highscore_offense';
						break;
					case 'defense':
						$fieldName = 'highscore_defense';
						break;
					case 'trade':
						$fieldName = 'highscore_trade';
						break;
					case 'resources':
						$fieldName = 'highscore_resources';
						break;
					case 'donations':
						$fieldName = 'highscore_donate';
						break;
					default:
						$post['highscoreType'] = 'score';
						break;
				}
				$highscoreType = $post['highscoreType'];
			}
			if (isset($post['searchUser']) && !empty($post['searchUser'])) {
				$s = $Users->fetchRow(
					$Users->select()
						->from($Users->_name,'usr_id')
						->where('usr_nick = ?',$post['searchUser'])
				);
				if ($s) {
					$searchId = $s->usr_id;
					$searchUser = $post['searchUser'];
				}
			}
		}
		
		$total = $Highscores->fetchRow(
			$Highscores->select()
				->from($Highscores->_name, 'COUNT(*) AS total')
		)->total;
		
		if ($offset == -1) {
			$rank = $Highscores->fetchRow(
				$Highscores->select()
					->from($Highscores->_name, $fieldName.'_rank AS rank')
					->where('highscore_user = ?',$User->usr_id)
			)->rank;
			$current = floor($rank / 100) * 100;
		} else {
			$current = ceil($offset / 100) * 100;
		}
		
		if (!$searchId) {
			$scores = $Highscores->fetchAll(
				$Highscores->select()
					->from($Highscores->_name, array($fieldName,'highscore_user',$fieldName.'_rank'))
					->order($fieldName.'_rank ASC')
					->limitPage($current / 100,100)
			);
		} else {
			$scores = $Highscores->fetchAll(
				$Highscores->select()
					->from($Highscores->_name, array($fieldName,'highscore_user',$fieldName.'_rank'))
					->where('highscore_user = ?', $searchId)
					->order($fieldName.'_rank ASC')
			);
		}
		
		$scoresView = array();
		foreach ($scores as $score) {
			$HighUser = new Chamista_Model_User($score->highscore_user);
			$scoresView[] = array(
				'rank' => $score->{$fieldName.'_rank'},
				'name' => $HighUser->usr_nick,
				'alliance' => '',
				'points' => $score->$fieldName,
				'usrId' => $HighUser->usr_id,
			);
		}
		
		$this->view->highscore = array(
			'scores' => $scoresView,
			'highscoreType' => $highscoreType,
			'offsetEnd' => ceil($total / 100),
			'offset' => $offset,
			'current' => $current,
			'usrId' => $User->usr_id,
			'searchUser' => $searchUser,
		);
		$this->view->body_id = 'highscore';
	}
}