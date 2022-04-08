<?php
App::uses('CakeEmail', 'Network/Email');
class MultiquestionsController extends AppController {
	var $name = 'Multiquestions';	
	var $components = array('Twilio', 'Mms', 'Nexmomessage', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel');
	public $uses = array('ContactGroup','User','Contact','MultiContest','MultiContestQuestion','MultiContestQuestionsOption','MultiContestSubscriber','MultiContestSubscriberArchive');
	function index(){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');	
			$this->paginate = array('conditions' => array('MultiContest.user_id' =>$user_id),'order' =>array('MultiContest.id' => 'desc'));
			$contest_arr = $this->paginate('MultiContest');
			$this->set('contest_arr',$contest_arr);	
		} else {
            $this->redirect('/users/login');
        }
	}
    function add(){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			app::import('Model', 'Group');
			$this->Group = new Group();
			$Group = $this->Group->find('all', array('conditions' => array('Group.user_id' =>$this->Session->read('User.id')), 'order' => array('Group.group_name' => 'asc')));
			$this->set('Group', $Group);
			if(!empty($this->request->data)){
				app::import('Model', 'Contest');
				$this->Contest = new Contest();
				$contestkeyword = $this->Contest->find('first', array('conditions' => array('Contest.keyword ' => $this->request->data['MultiContest']['keyword'], 'Contest.user_id' =>$this->Session->read('User.id'))));
				if (!empty($contestkeyword)) {
					$this->Session->setFlash(__('Keyword is already registered for a contest. Please choose another keyword.', true));
					$this->redirect(array('controller' => 'multiquestions', 'action' => 'add'));
				}
				app::import('Model', 'Smsloyalty');
				$this->Smsloyalty = new Smsloyalty();
				$loyaltykeyword = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.codestatus ' => $this->request->data['MultiContest']['keyword'], 'Smsloyalty.user_id' =>$this->Session->read('User.id'))));
				if (!empty($loyaltykeyword)) {
					$this->Session->setFlash(__('Keyword is already registered for another loyalty program. Please choose another keyword.', true));
					$this->redirect(array('controller' => 'multiquestions', 'action' => 'add'));
				}
				app::import('Model', 'Group');
				$this->Group = new Group();
				$groupkeyword = $this->Group->find('first', array('conditions' => array('Group.keyword ' => $this->request->data['MultiContest']['keyword'], 'Group.user_id' =>$this->Session->read('User.id'))));
				if (!empty($groupkeyword)) {
					$this->Session->setFlash(__('Keyword is already registered for group. Please choose another keyword.', true));
					$this->redirect(array('controller' => 'multiquestions', 'action' => 'add'));
				}
				$keywords = $this->MultiContest->find('first', array('conditions' => array('MultiContest.keyword ' => $this->request->data['MultiContest']['keyword'],'MultiContest.user_id ' =>$this->Session->read('User.id'))));
				if (empty($keywords)) {
					$this->request->data['MultiContest']['id']= '';
					$this->request->data['MultiContest']['user_id']= $this->Session->read('User.id');
					if(isset($this->request->data['MultiContest']['finish_all_questions'])){
						$this->request->data['MultiContest']['finish_all_questions'] = 1;
					}else{
						$this->request->data['MultiContest']['finish_all_questions'] = 0;
					}
					if($this->MultiContest->save($this->request->data)){
						$last_id  = $this->MultiContest->getLastInsertID();
						$question_count = 1;
						foreach($this->request->data['MultiContestQuestion'] as $question_arr){
							$multi_contest_question_arr['MultiContestQuestion']['id'] = '';
							$multi_contest_question_arr['MultiContestQuestion']['multi_contest_id'] = $last_id;
							$multi_contest_question_arr['MultiContestQuestion']['user_id'] = $user_id;
							$multi_contest_question_arr['MultiContestQuestion']['question'] = $question_arr['question'];
							if($this->MultiContestQuestion->save($multi_contest_question_arr)){
								$last_question_id  = $this->MultiContestQuestion->getLastInsertID();
								for($i=0;$i<count($this->request->data['MultiContestQuestionsOption'][$question_count]['answers']);$i++){
									$contest_questions_option_arrs['MultiContestQuestionsOption']['id'] = '';
									$contest_questions_option_arrs['MultiContestQuestionsOption']['question_id'] = $last_question_id;
									$contest_questions_option_arrs['MultiContestQuestionsOption']['multi_contest_id'] = $last_id;
									$contest_questions_option_arrs['MultiContestQuestionsOption']['user_id'] = $user_id;
									$contest_questions_option_arrs['MultiContestQuestionsOption']['answer']	= trim($this->request->data['MultiContestQuestionsOption'][$question_count]['answers'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['description'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['description'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['response'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['response'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['email'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['email'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['phone'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['phone'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['text'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['text'][$i]);
									$this->MultiContestQuestionsOption->save($contest_questions_option_arrs);
								}
							}
							$question_count = $question_count + 1;
						}
						$this->Session->setFlash(__('Q&A SMS bot has been saved', true));
						$this->redirect(array('controller' =>'multiquestions', 'action'=>'index'));		
					}else{
						$this->Session->setFlash(__('Q&A SMS bot could not be saved. Please, try again.', true));	
					}
				} else {
					$this->Session->setFlash(__('Keyword is already registered. please choose another keyword.', true));
				}			
			}
		} else {
            $this->redirect('/users/login');
        }
	}
    function edit($id=null){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			$question_arr = $this->MultiContest->find('first', array('conditions' =>array('MultiContest.id'=>$id,'MultiContest.user_id' =>$user_id))); 
			$this->set('question_arr',$question_arr);
			$ContestQuestion = $this->MultiContestQuestion->find('all', array('conditions' =>array('MultiContestQuestion.multi_contest_id'=>$id,'MultiContestQuestion.user_id' =>$user_id), 'order' => array('MultiContestQuestion.id' => 'asc')));
			$this->set('ContestQuestion',$ContestQuestion);
			app::import('Model', 'Group');
			$this->Group = new Group();
			$Group = $this->Group->find('all', array('conditions' => array('Group.user_id' =>$this->Session->read('User.id')), 'order' => array('Group.group_name' => 'asc')));
			$this->set('Group', $Group);
			if(!empty($this->request->data)){
				app::import('Model', 'Contest');
				$this->Contest = new Contest();
				$contestkeyword = $this->Contest->find('first', array('conditions' => array('Contest.keyword ' => $this->request->data['MultiContest']['keyword'], 'Contest.user_id' =>$this->Session->read('User.id'))));
				if (!empty($contestkeyword)) {
					$this->Session->setFlash(__('Keyword is already registered for a contest. Please choose another keyword.', true));
					$this->redirect(array('controller' => 'multiquestions', 'action' => 'edit/'.$id));
				}
				app::import('Model', 'Smsloyalty');
				$this->Smsloyalty = new Smsloyalty();
				$loyaltykeyword = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.codestatus ' => $this->request->data['MultiContest']['keyword'], 'Smsloyalty.user_id' =>$this->Session->read('User.id'))));
				if (!empty($loyaltykeyword)) {
					$this->Session->setFlash(__('Keyword is already registered for another loyalty program. Please choose another keyword.', true));
					$this->redirect(array('controller' => 'multiquestions', 'action' => 'edit/'.$id));
				}
				app::import('Model', 'Group');
				$this->Group = new Group();
				$groupkeyword = $this->Group->find('first', array('conditions' => array('Group.keyword ' => $this->request->data['MultiContest']['keyword'], 'Group.user_id' =>$this->Session->read('User.id'))));
				if (!empty($groupkeyword)) {
					$this->Session->setFlash(__('Keyword is already registered for group. Please choose another keyword.', true));
					$this->redirect(array('controller' => 'multiquestions', 'action' => 'edit/'.$id));
				}
				$keywords = $this->MultiContest->find('first', array('conditions' => array('MultiContest.id !=' => $id,'MultiContest.keyword ' => $this->request->data['MultiContest']['keyword'],'MultiContest.user_id ' =>$this->Session->read('User.id'))));
				if (empty($keywords)) {
					if(isset($this->request->data['MultiContest']['finish_all_questions'])){
						$this->request->data['MultiContest']['finish_all_questions'] = 1;
					}else{
						$this->request->data['MultiContest']['finish_all_questions'] = 0;
					}
					if($this->MultiContest->save($this->request->data)){
						app::import('Model', 'MultiContestQuestion');
						$this->MultiContestQuestion = new MultiContestQuestion();
						$this->MultiContestQuestion->deleteAll(array('MultiContestQuestion.multi_contest_id' => $id));
						app::import('Model', 'MultiContestQuestionsOption');
						$this->MultiContestQuestionsOption = new MultiContestQuestionsOption();
						$this->MultiContestQuestionsOption->deleteAll(array('MultiContestQuestionsOption.multi_contest_id' => $id));
						app::import('Model', 'MultiContestSubscriber');
						$this->MultiContestSubscriber = new MultiContestSubscriber();
						$this->MultiContestSubscriber->deleteAll(array('MultiContestSubscriber.multi_contest_id' => $id));
						$question_count = 1;
						foreach($this->request->data['MultiContestQuestion'] as $question_arr){
							$multi_contest_question_arr['MultiContestQuestion']['id'] = '';
							$multi_contest_question_arr['MultiContestQuestion']['multi_contest_id'] = $this->request->data['MultiContest']['id'];
							$multi_contest_question_arr['MultiContestQuestion']['user_id'] = $user_id;
							$multi_contest_question_arr['MultiContestQuestion']['question'] = $question_arr['question'];
							if($this->MultiContestQuestion->save($multi_contest_question_arr)){
								$last_question_id  = $this->MultiContestQuestion->getLastInsertID();
								for($i=0;$i<count($this->request->data['MultiContestQuestionsOption'][$question_count]['answers']);$i++){
									$contest_questions_option_arrs['MultiContestQuestionsOption']['id'] = '';
									$contest_questions_option_arrs['MultiContestQuestionsOption']['question_id'] = $last_question_id;
									$contest_questions_option_arrs['MultiContestQuestionsOption']['multi_contest_id'] = $this->request->data['MultiContest']['id'];
									$contest_questions_option_arrs['MultiContestQuestionsOption']['user_id'] = $user_id;
									$contest_questions_option_arrs['MultiContestQuestionsOption']['answer']	= trim($this->request->data['MultiContestQuestionsOption'][$question_count]['answers'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['description'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['description'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['response'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['response'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['email'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['email'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['phone'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['phone'][$i]);
									$contest_questions_option_arrs['MultiContestQuestionsOption']['text'] = trim($this->request->data['MultiContestQuestionsOption'][$question_count]['text'][$i]);
									$this->MultiContestQuestionsOption->save($contest_questions_option_arrs);
								}
							}
							$question_count = $question_count + 1;
						}
						$this->Session->setFlash(__('Q&A SMS bot has been updated', true));
						$this->redirect(array('controller' =>'multiquestions', 'action'=>'index'));		
					}else{
						$this->Session->setFlash(__('Q&A SMS bot could not be saved. Please, try again.', true));	
					}
				} else {
					$this->Session->setFlash(__('Keyword is already registered. please choose another keyword.', true));
				}
			}
		} else {
            $this->redirect('/users/login');
        }
	}
	function view($id = null){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			$question_arr = $this->MultiContest->find('first', array('conditions' =>array('MultiContest.id'=>$id,'MultiContest.user_id' =>$user_id))); 
			$this->set('question_arr',$question_arr); 
			$ContestQuestion = $this->MultiContestQuestion->find('all', array('conditions' =>array('MultiContestQuestion.multi_contest_id'=>$id,'MultiContestQuestion.user_id' =>$user_id),'order' =>array('MultiContestQuestion.id' => 'asc'))); 
			$this->set('ContestQuestion',$ContestQuestion);	
		} else {
            $this->redirect('/users/login');
        }
	}
	function delete($id=null){
		$this->autoRender = false;	
		if ($this->MultiContest->delete($id)){
			app::import('Model', 'MultiContestQuestion');
			$this->MultiContestQuestion = new MultiContestQuestion();
			$this->MultiContestQuestion->deleteAll(array('MultiContestQuestion.multi_contest_id' => $id));
			app::import('Model', 'MultiContestQuestionsOption');
			$this->MultiContestQuestionsOption = new MultiContestQuestionsOption();
			$this->MultiContestQuestionsOption->deleteAll(array('MultiContestQuestionsOption.multi_contest_id' => $id));
			app::import('Model', 'MultiContestSubscriber');
			$this->MultiContestSubscriber = new MultiContestSubscriber();
			$this->MultiContestSubscriber->deleteAll(array('MultiContestSubscriber.multi_contest_id' => $id));
			$this->Session->setFlash(__('Q&A SMS bot has been deleted', true));
			$this->redirect(array('controller' =>'multiquestions', 'action'=>'index'));
		}
	}
	function question_delete($id=null,$multicontest_id=null){
		$this->autoRender = false;	
		if ($this->MultiContestQuestion->delete($id)){
			$this->MultiContestQuestionsOption->deleteAll(array('MultiContestQuestionsOption.question_id' => $id));
			$this->Session->setFlash(__('Question has been deleted', true));
			$this->redirect(array('controller' =>'multiquestions', 'action'=>'edit/'.$multicontest_id));
		}
	}
	function options_delete($id=null,$multicontest_id=null){
		$this->autoRender = false;	
		if ($this->MultiContestQuestionsOption->delete($id)){
			$this->Session->setFlash(__('Option has been deleted', true));
			$this->redirect(array('controller' =>'multiquestions', 'action'=>'edit/'.$multicontest_id));
		}
	}
	function participants($id=null){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			if($user_id > 0){
				app::import('Model','MultiContestSubscriberArchive');
				$this->MultiContestSubscriberArchive = new MultiContestSubscriberArchive();
				$this->MultiContestSubscriberArchive->recursive = 0;
				$this->paginate = array('conditions' => array('MultiContestSubscriberArchive.multi_contest_id' =>$id,'MultiContestSubscriberArchive.user_id' =>$user_id),'order' =>array('MultiContestSubscriberArchive.id' => 'desc'));
				$participant = $this->paginate('MultiContestSubscriberArchive');
				$this->set('participant', $participant);
				$this->set('id', $id);
			}else{
				$this->Session->setFlash('You need to login first'); 				
				$this->redirect(array('controller' =>'users', 'action'=>'login'));
			}
		} else {
            $this->redirect('/users/login');
        }
	}
	function export($id=null){
		$this->autoRender = false;
		app::import('Model','MultiContestSubscriberArchive');
		$this->MultiContestSubscriber = new MultiContestSubscriberArchive();
		$user_id=$this->Session->read('User.id');
		$contacts = $this->MultiContestSubscriberArchive->find('all',array('conditions'=>array('MultiContestSubscriberArchive.multi_contest_id'=>$id,'MultiContestSubscriberArchive.user_id'=>$user_id),'order' =>array('MultiContestSubscriberArchive.created' => 'desc')));
		$filename = "Q&ASMSBotParticipants".date("Y.m.d").".csv";
		$csv_file = fopen('php://output', 'w');
		header('Content-type: application/csv');
		//header('Content-Type: text/html');
		header('Content-Disposition: attachment; filename="'.$filename.'"'); 
		$header_row = array("Name","Phone","Question","Answer","RespondedOn");
		fputcsv($csv_file,$header_row,',','"');
		foreach($contacts as $result){
			$row = array(
				ucfirst($result['Contact']['name']),
				$result['MultiContestSubscriberArchive']['phone'],
				//$result['MultiContestSubscriberArchive']['multi_contest_question_id'],
				$result['MultiContestSubscriberArchive']['question'],
				$result['MultiContestSubscriberArchive']['answer'],
				$result['MultiContestSubscriberArchive']['created']
			);	
			fputcsv($csv_file,$row,',','"');
		}
		fclose($csv_file);
	}
}
?>