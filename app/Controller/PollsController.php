<?php
App::uses('CakeEmail', 'Network/Email');

class POllsController extends AppController
{
    var $uses = array();
    var $name = 'Polls';
    var $components = array('Cookie', 'Twilio', 'Nexmomessage', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');

    function question_list()
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'Question');
        $this->Question = new Question();
        $this->Question->recursive = 0;
        $this->set('questions', $this->paginate('Question', array('Question.user_id' => $this->Session->read('User.id'))));
    }

    function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Poll', true));
            $this->redirect(array('action' => 'question_list'));
        }
        app::import('Model', 'Question');
        $this->Question = new Question();
        if ($this->Question->delete($id)) {
            app::import('Model', 'Option');
            $this->Option = new Option();
            $this->Option->deleteAll(array('Option.question_id' => $id));
            $this->Session->setFlash(__('Poll Deleted', true));
            $this->redirect(array('action' => 'question_list'));
        }
    }

    function index()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('users', $users);
        if (!empty($this->request->data)) {
           
            app::import('Model', 'Question');
            $this->Question = new Question();
            $this->request->data['Question']['user_id'] = $user_id;
            $this->Question->save($this->request->data);
            $question_id = $this->Question->id;
            //$optionb = array('0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D');
            $optionb = array('0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E', '5' => 'F');
            $automessage = array();
            foreach ($this->request->data['Option']['autorsponder_message'] as $automessage) {
                $auto_message[] = $automessage;
            }
            $count = 0;
            $i = 0;
            foreach ($this->request->data['Option']['optiona'] as $option) {
                app::import('Model', 'Option');
                $this->Option = new Option();
                $this->request->data['Option']['question_id'] = $question_id;
                $this->request->data['Option']['optiona'] = $option;
                $this->request->data['Option']['autorsponder_message'] = $auto_message[$i];
                $this->request->data['Option']['optionb'] = $optionb[$count];
                $this->Option->save($this->request->data);
                $count++;
                $i++;
            }
            $this->Session->setFlash(__('The Poll has been saved', true));
            $this->redirect(array('action' => 'question_list'));

        }
    }

    function send_question($id = null)
    {
        $this->layout = 'popup';
        $this->set('id', $id);
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $group);
        app::import('Model', 'User');
        $this->User = new User();
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $credits = $users['User']['sms_balance'];
        $this->Session->write('User.sms_balance', $users['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $users['User']['assigned_number']);
        $this->Session->write('User.active', $users['User']['active']);
        $this->Session->write('User.pay_activation_fees_active', $users['User']['pay_activation_fees_active']);
        $this->Session->write('User.getnumbers', $users['User']['getnumbers']);
        $this->Session->write('User.package', $users['User']['package']);
        app::import('Model', 'Option');
        $this->Option = new Option();
        $questionmessage = $this->Option->find('all', array('conditions' => array('Option.question_id' => $id), 'order' => array('Option.id' => 'ASC')));
        $this->set('questionmessages', $questionmessage);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $this->set('users', $users);
        if (!empty($this->request->data)) {
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $users_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.sms' => 1)));
            $rotate_number = $this->request->data['User']['rotate_number'];
            app::import('Model', 'Contact');
            $this->Contact = new Contact();

            if ((!empty($user_numbers)) || (!empty($users_arr))) {

                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $Subscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('ContactGroup.id', 'Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                if (empty($Subscriber)) {
                    $this->Session->setFlash(__('Add contacts to this group or select a different group.', true));
                    $this->redirect(array('controller' => 'polls', 'action' => 'question_list'));
                }
                
                app::import('Model', 'Option');
                $this->Option = new Option();
                $questions = $this->Option->find('all', array('conditions' => array('Option.question_id' => $this->request->data['Question']['question_id']), 'order' => array('Option.id' => 'ASC')));
                $options = '';
                foreach ($questions as $question) {
                    if(trim($question['Option']['optiona']) !=''){
                        $questions1 = $question['Question']['question'];
                        $options .= $question['Option']['optionb'] . ". " . $question['Option']['optiona'] . "\n";
                    }
                }

                $totalsubscribers = count($Subscriber);
                $subscriberPhone = '';
                //$sendthis = "Reply A,B,C OR D.";
                $sendthis = "Reply with letter of your choice";
                $optmsg = OPTMSG;
                $message1 = $questions1 . "\n" . $options . "\n" . $sendthis . "\n" . $optmsg;
                //$length = strlen(utf8_decode(substr($message1, 0, 160)));
                $length = mb_strlen($message1,"UTF-8");
			    $gsm = $this->is_gsm($message1);
                if(!$gsm){//if (strlen($message1) != strlen(utf8_decode($message1))) {
                    $contactcredits = ceil($length / 70);
                } else {
                    $contactcredits = ceil($length / 160);
                }
                if ($credits < ($totalsubscribers * $contactcredits)) {
                    $this->Session->setFlash(__('You do not have enough credits to send a poll to this group.', true));
                    $this->redirect(array('controller' => 'polls', 'action' => 'question_list'));
                }

                app::import('Model', 'Question');
                $this->Question = new Question();
                $this->request->data['Question']['id'] = $this->request->data['Question']['question_id'];
                $groupid = $this->request->data['Group']['id'];
                $this->request->data['Question']['group_id'] = $groupid;
                $this->Question->save($this->request->data);
                //print_r($Subscriber);
                //$subscriberPhone1 = '';
                //foreach ($Subscriber as $Subscribers) {
                //    $subscriberPhone1[$Subscribers['ContactGroup']['id']] = $Subscribers['Contact']['phone_number'];
                //}
                //pr($subscriberPhone1);
                //$subscriberPhoneTotal = count($subscriberPhone1);
                /*	$this->request->data['User']['sms_balance']=$credits-$subscriberPhoneTotal;
                    $this->request->data['User']['id']=$user_id;
                    $this->User->save($this->request->data);*/
                //if($subscriberPhoneTotal > 0){
                if ($totalsubscribers > 0) {
                    app::import('Model', 'GroupSmsBlast');
                    $this->GroupSmsBlast = new GroupSmsBlast();
                    $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                    $this->request->data['GroupSmsBlast']['group_id'] = $groupid;
                    //$this->request->data['GroupSmsBlast']['totals'] = $subscriberPhoneTotal;
                    $this->request->data['GroupSmsBlast']['totals'] = $totalsubscribers;
                    $this->GroupSmsBlast->save($this->request->data);
                    $groupblastid = $this->GroupSmsBlast->id;
                    $this->Session->write('groupsmsid', $groupblastid);
                    app::import('Model', 'Log');
                    if (API_TYPE == 0) {
                        if ($rotate_number == 1) {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            $from_arr = array();
                            if (!empty($users_arr)) {
                                $from_arr[] = $users_arr['User']['assigned_number'];
                            }
                            if (!empty($user_numbers)) {
                                foreach ($user_numbers as $values) {
                                    $from_arr[] = $values['UserNumber']['number'];
                                }
                            }
                            $this->Twilio->curlinit = curl_init();
                            $this->Twilio->bulksms = 1;
                            //foreach($subscriberPhone1 as $contactgroupid=>$subscriberPhones){
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $random_keys = array_rand($from_arr, 1);
                                $from = $from_arr[$random_keys];

                                $stickyfrom = $Subscribers['Contact']['stickysender'];
                                if ($stickyfrom == 0) {
                                    $contact['Contact']['id'] = $Subscribers['Contact']['id'];
                                    $contact['Contact']['stickysender'] = $from;
                                    $this->Contact->save($contact);
                                } else {
                                    $from = $stickyfrom;
                                }

                                //$from = '2029993169';
                                $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                $response = $this->Twilio->sendsms($to, $from, $message);
                                //pr($response);
                                $smsid = $response->ResponseXml->Message->Sid;
                                $Status = $response->ResponseXml->RestException->Status;
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $smsid;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($Status == 400) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $response->ErrorMessage;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    //pr($groupContacts);
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                $this->Log->save($this->request->data);
                            }
                        } else {
                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            if (!empty($usernumber)) {
                                if ($usernumber['User']['sms'] == 1) {
                                    $assigned_number = $usernumber['User']['assigned_number'];
                                } else {
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                    if (!empty($user_numbers)) {
                                        $assigned_number = $user_numbers['UserNumber']['number'];
                                    } else {
                                        $assigned_number = $usernumber['User']['assigned_number'];
                                    }
                                }
                            }
                            $this->Twilio->curlinit = curl_init();
                            $this->Twilio->bulksms = 1;
                            //foreach ($subscriberPhone1 as $contactgroupid => $subscriberPhones) {
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $from = $assigned_number;
                                //$from = '2029993169';
                                $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                $response = $this->Twilio->sendsms($to, $from, $message);
                                //pr($response);
                                $smsid = $response->ResponseXml->Message->Sid;
                                $Status = $response->ResponseXml->RestException->Status;
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $smsid;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($Status == 400) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $response->ErrorMessage;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    //pr($groupContacts);
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                $this->Log->save($this->request->data);
                            }
                        }
                    } else if (API_TYPE == 4 || API_TYPE == 5 || API_TYPE == 6 || API_TYPE == 7) {
						if ($rotate_number == 1) {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            $from_arr = array();
                            if (!empty($users_arr)) {
                                $from_arr[] = $users_arr['User']['assigned_number'];
                            }
                            if (!empty($user_numbers)) {
                                foreach ($user_numbers as $values) {
                                    $from_arr[] = $values['UserNumber']['number'];
                                }
                            }
                            $credits = 0;
                            $failed = 0;
                            $success = 0;
                            //foreach($subscriberPhone1 as $contactgroupid=>$subscriberPhones){
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $random_keys = array_rand($from_arr, 1);
                                $from = $from_arr[$random_keys];

                                $stickyfrom = $Subscribers['Contact']['stickysender'];
                                if ($stickyfrom == 0) {
                                    $contact['Contact']['id'] = $Subscribers['Contact']['id'];
                                    $contact['Contact']['stickysender'] = $from;
                                    $this->Contact->save($contact);
                                } else {
                                    $from = $stickyfrom;
                                }
								/*$response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
								$status = '';
								$message_id = '';
								$errortext = '';
								if(isset($response->message)){
									$errortext = $response->message;
									$status = 'failed';
								}else{
									$status = 'sent';
								}*/
								$message_id = '';
								if(API_TYPE==5){
								    $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message);
                            		$status = '';
                        			$errortext = '';
                        			if(isset($response->error_message)){
                        				$errortext = $response->error_message;
                        				$status = 'failed';
                        			}else{
                        			    $message_id = $response->sid;
                        				$status = 'sent';
                        			}
                        	   }elseif(API_TYPE==6){
								    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message,$groupblastid,$groupid,$user_id);
                            		$status = '';
                        			$errortext = '';
                        			if($response->Message360->ResponseStatus == 0){
                        				$errortext = $response->Message360->Errors->Error[0]->Message;
                        				$status = 'failed';
                        			}else{
                        			    $message_id = $response->Message360->Message->MessageSid;
                        				$status = 'sent';
                        			}
                        	   }elseif(API_TYPE==7){
								    $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
                            		$status = '';
                        			$errortext = '';
                        			if(isset($response->errors)){
                        				$errortext = $response->errors[0]->detail;
                        				$status = 'failed';
                        			}else{
                        			    $message_id = $response->data->id;
                        				$status = 'sent';
                        			}
								}else{
    								$retry = false;
    					            $retries = 0;
    					            $MAX_RETRIES = 15;  
    					            
    					            do{
    					                $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
    
    					                if(isset($response->message)){
    						                $errortext = $response->message;
    						                $status = 'failed';
    				
    						                if(trim($response->code) == "message-rate-limit-overall" || trim($response->code) == "message-rate-limit-per-tn"){
                                                $retry = true;
                                                $retries = $retries + 1;
                                                sleep(1);
                                            }else{
                                                $retry = false;
                                            }
    					                }else{
    						                $status = 'sent';
    						                $errortext = '';
    						                $retry = false;
    					                }
    					                
    					            } while ($retry && $retries < $MAX_RETRIES);
								}
                                //pr($response);
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($errortext !='') {
                                    $this->request->data['Log']['sms_status'] = $status;
                                    $this->request->data['Log']['error_message'] = $errortext;
                                    if(API_TYPE==6){
                                        $this->Log->save($this->request->data);
                                    }
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }else{
                                    $credits = $credits + $contactcredits;
                                    $success = $success + 1;
                                }
                                if(API_TYPE!=6){
                                    $this->Log->save($this->request->data);
                                }
                            }
                            
                            if ($success > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $this->GroupSmsBlast = new GroupSmsBlast();
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);
                        } else {
                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            if (!empty($usernumber)) {
                                if ($usernumber['User']['sms'] == 1) {
                                    $assigned_number = $usernumber['User']['assigned_number'];
                                } else {
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                    if (!empty($user_numbers)) {
                                        $assigned_number = $user_numbers['UserNumber']['number'];
                                    } else {
                                        $assigned_number = $usernumber['User']['assigned_number'];
                                    }
                                }
                            }
                            $credits = 0;
                            $failed = 0;
                            $success = 0;
                            //foreach ($subscriberPhone1 as $contactgroupid => $subscriberPhones) {
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $from = $assigned_number;
                                //$from = '2029993169';
								/*$response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
								$status = '';
								$message_id = '';
								$errortext = '';
								if(isset($response->message)){
									$errortext = $response->message;
									$status = 'failed';
								}else{
									$status = 'sent';
								}*/
								
								$message_id = '';
								if(API_TYPE==5){
								    $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message);
                            		$status = '';
                        			$errortext = '';
                        			if(isset($response->error_message)){
                        				$errortext = $response->error_message;
                        				$status = 'failed';
                        			}else{
                        			    $message_id = $response->sid;
                        				$status = 'sent';
                        			}
								}elseif(API_TYPE==6){
								    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message,$groupblastid,$groupid,$user_id);
                            		$status = '';
                        			$errortext = '';
                        			if($response->Message360->ResponseStatus == 0){
                        				$errortext = $response->Message360->Errors->Error[0]->Message;
                        				$status = 'failed';
                        			}else{
                        			    $message_id = $response->Message360->Message->MessageSid;
                        				$status = 'sent';
                        			}
								}elseif(API_TYPE==7){
								    $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
                            		$status = '';
                        			$errortext = '';
                        			if(isset($response->errors)){
                        				$errortext = $response->errors[0]->detail;
                        				$status = 'failed';
                        			}else{
                        			    $message_id = $response->data->id;
                        				$status = 'sent';
                        			}
								}else{
    								$retry = false;
    					            $retries = 0;
    					            $MAX_RETRIES = 15;  
    					            
    					            do{
    					                $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
    
    					                if(isset($response->message)){
    						                $errortext = $response->message;
    						                $status = 'failed';
    				
    						                if(trim($response->code) == "message-rate-limit-overall" || trim($response->code) == "message-rate-limit-per-tn"){
                                                $retry = true;
                                                $retries = $retries + 1;
                                                sleep(1);
                                            }else{
                                                $retry = false;
                                            }
    					                }else{
    						                $status = 'sent';
    						                $errortext = '';
    						                $retry = false;
    					                }
    					                
    					            } while ($retry && $retries < $MAX_RETRIES);
								}
                            
                                //pr($response);
                                
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($errortext !='') {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $this->request->data['Log']['error_message'] = $errortext;
                                    if(API_TYPE==6){
                                        $this->Log->save($this->request->data);
                                    }
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }else{
                                    $credits = $credits + $contactcredits;
                                    $success = $success + 1;
                                }
                                if(API_TYPE!=6){
                                    $this->Log->save($this->request->data);
                                }
                            }
                            
                            if ($success > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $this->GroupSmsBlast = new GroupSmsBlast();
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);
                        }
                    } else if (API_TYPE == 2) {
                        if ($rotate_number == 1) {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            $from_arr = array();
                            if (!empty($users_arr)) {
                                $from_arr[] = $users_arr['User']['assigned_number'];
                            }
                            if (!empty($user_numbers)) {
                                foreach ($user_numbers as $values) {
                                    $from_arr[] = $values['UserNumber']['number'];
                                }
                            }
                            $k = 0;
                            $sucesscredits = 0;
                            $this->Slooce->curlinit = curl_init();
                            $this->Slooce->bulksms = 1;
                            //foreach ($subscriberPhone1 as $contactgroupid => $subscriberPhones) {
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                //$random_keys= array_rand($from_arr,1);
                                $countnumber = count($from_arr);
                                if ($countnumber == $k) {
                                    $k = 0;
                                }

                                $from = $from_arr[$k];
                                $response = $this->Slooce->mt($users_arr['User']['api_url'], $users_arr['User']['partnerid'], $users_arr['User']['partnerpassword'], $to, $users_arr['User']['keyword'], $message);
                                $message_id = '';
                                $status = '';
                                if (isset($response['id'])) {
                                    if ($response['result'] == 'ok') {
                                        $message_id = $response['id'];
                                    }
                                    $status = $response['result'];
                                }
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($status != 'ok') {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $status;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                if ($message_id != '') {
                                    $sucesscredits = $sucesscredits + 1;
                                    $this->request->data['Log']['sms_status'] = 'sent';
                                }
                                $this->Log->save($this->request->data);
                                $k = $k + 1;
                            }
                            curl_close($this->Slooce->curlinit);
                            if ($sucesscredits > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $sucesscredits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);

                        } else {
                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            if (!empty($usernumber)) {
                                if ($usernumber['User']['sms'] == 1) {
                                    $assigned_number = $usernumber['User']['assigned_number'];
                                } else {
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                    if (!empty($user_numbers)) {
                                        $assigned_number = $user_numbers['UserNumber']['number'];
                                    } else {
                                        $assigned_number = $usernumber['User']['assigned_number'];
                                    }
                                }
                            }
                            $sucesscredits = 0;
                            $this->Slooce->curlinit = curl_init();
                            $this->Slooce->bulksms = 1;
                            //foreach ($subscriberPhone1 as $contactgroupid => $subscriberPhones) {
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $from = $assigned_number;
                                $response = $this->Slooce->mt($usernumber['User']['api_url'], $usernumber['User']['partnerid'], $usernumber['User']['partnerpassword'], $to, $usernumber['User']['keyword'], $message);
                                $message_id = '';
                                $status = '';
                                if (isset($response['id'])) {
                                    if ($response['result'] == 'ok') {
                                        $message_id = $response['id'];
                                    }
                                    $status = $response['result'];
                                }
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($message_id != '') {
                                    $sucesscredits = $sucesscredits + 1;
                                    $this->request->data['Log']['sms_status'] = 'sent';
                                }
                                if ($status != 0) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $errortext;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    //pr($groupContacts);
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                $this->Log->save($this->request->data);
                            }
                            curl_close($this->Slooce->curlinit);
                            if ($sucesscredits > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $sucesscredits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);

                        }
                    } else if (API_TYPE == 3) {
                        if ($rotate_number == 1) {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            $from_arr = array();
                            if (!empty($users_arr)) {
                                $from_arr[] = $users_arr['User']['assigned_number'];
                            }
                            if (!empty($user_numbers)) {
                                foreach ($user_numbers as $values) {
                                    $from_arr[] = $values['UserNumber']['number'];
                                }
                            }
                            $k = 0;
                            $sucesscredits = 0;
                            $credits = 0;
                            $this->Plivo->curlinit = curl_init();
                            $this->Plivo->bulksms = 1;
                            //foreach($subscriberPhone1 as $contactgroupid=>$subscriberPhones){
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                $countnumber = count($from_arr);
                                if ($countnumber == $k) {
                                    $k = 0;
                                    //sleep(1);
                                }

                                $from = $from_arr[$k];

                                $stickyfrom = $Subscribers['Contact']['stickysender'];
                                if ($stickyfrom == 0) {
                                    $contact['Contact']['id'] = $Subscribers['Contact']['id'];
                                    $contact['Contact']['stickysender'] = $from;
                                    $this->Contact->save($contact);
                                } else {
                                    $from = $stickyfrom;
                                }

                                //$from = '2029993169';
                                $this->Plivo->AuthId = PLIVO_KEY;
                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                //sleep(1);
                                $response = $this->Plivo->sendsms($to, $from, $message);
                                $errortext = '';
                                $message_id = '';
                                if (isset($response['response']['error'])) {
                                    $errortext = $response['response']['error'];
                                }
                                if (isset($response['response']['message_uuid'][0])) {
                                    $message_id = $response['response']['message_uuid'][0];
                                }
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if (isset($response['response']['error'])) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $errortext;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                if ($message_id != '') {
                                    $sucesscredits = $sucesscredits + $contactcredits;
                                    $credits = $credits + 1;
                                    $this->request->data['Log']['sms_status'] = 'sent';
                                }
                                $this->Log->save($this->request->data);
                                $k = $k + 1;
                            }
                            curl_close($this->Plivo->curlinit);
                            if ($sucesscredits > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $sucesscredits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $credits;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);

                        } else {
                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            if (!empty($usernumber)) {
                                if ($usernumber['User']['sms'] == 1) {
                                    $assigned_number = $usernumber['User']['assigned_number'];
                                } else {
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                    if (!empty($user_numbers)) {
                                        $assigned_number = $user_numbers['UserNumber']['number'];
                                    } else {
                                        $assigned_number = $usernumber['User']['assigned_number'];
                                    }
                                }
                            }
                            $sucesscredits = 0;
                            $credits = 0;
                            $this->Plivo->curlinit = curl_init();
                            $this->Plivo->bulksms = 1;
                            //foreach ($subscriberPhone1 as $contactgroupid => $subscriberPhones) {
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $from = $assigned_number;
                                //$from = '2029993169';
                                $this->Plivo->AuthId = PLIVO_KEY;
                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                //sleep(1);
                                $response = $this->Plivo->sendsms($to, $from, $message);
                                $errortext = '';
                                $message_id = '';
                                if (isset($response['response']['error'])) {
                                    $errortext = $response['response']['error'];
                                }
                                if (isset($response['response']['message_uuid'][0])) {
                                    $message_id = $response['response']['message_uuid'][0];
                                }
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($message_id != '') {
                                    $sucesscredits = $sucesscredits + $contactcredits;
                                    $credits = $credits + 1;
                                    $this->request->data['Log']['sms_status'] = 'sent';
                                }
                                if (isset($response['response']['error'])) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $errortext;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    //pr($groupContacts);
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                $this->Log->save($this->request->data);
                            }
                            curl_close($this->Plivo->curlinit);
                            if ($sucesscredits > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $sucesscredits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $credits;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);

                        }
                    } else {
                        if ($rotate_number == 1) {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            $from_arr = array();
                            if (!empty($users_arr)) {
                                $from_arr[] = $users_arr['User']['assigned_number'];
                            }
                            if (!empty($user_numbers)) {
                                foreach ($user_numbers as $values) {
                                    $from_arr[] = $values['UserNumber']['number'];
                                }
                            }
                            $k = 0;
                            $sucesscredits = 0;
                            $credits = 0;
                            //foreach($subscriberPhone1 as $contactgroupid=>$subscriberPhones){
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                //$random_keys= array_rand($from_arr,1);
                                $countnumber = count($from_arr);
                                if ($countnumber == $k) {
                                    $k = 0;
                                    sleep(1);
                                }

                                $from = $from_arr[$k];

                                $stickyfrom = $Subscribers['Contact']['stickysender'];
                                if ($stickyfrom == 0) {
                                    $contact['Contact']['id'] = $Subscribers['Contact']['id'];
                                    $contact['Contact']['stickysender'] = $from;
                                    $this->Contact->save($contact);
                                } else {
                                    $from = $stickyfrom;
                                }

                                //$from = '2029993169';
                                $this->Nexmomessage->Key = NEXMO_KEY;
                                $this->Nexmomessage->Secret = NEXMO_SECRET;
                                //sleep(1);
                                $response = $this->Nexmomessage->sendsms($to, $from, $message);
                                //pr($response);
                                foreach ($response->messages as $doc) {
                                    $message_id = $doc->messageid;
                                    if ($message_id != '') {
                                        $status = $doc->status;
                                        $message_id = $doc->messageid;
                                    } else {
                                        $status = $doc->status;
                                        $errortext = $doc->errortext;
                                    }
                                }
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($status != 0) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $errortext;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                if ($message_id != '') {
                                    $sucesscredits = $sucesscredits + $contactcredits;
                                    $credits = $credits + 1;
                                    $this->request->data['Log']['sms_status'] = 'sent';
                                }
                                $this->Log->save($this->request->data);
                                $k = $k + 1;
                            }
                            if ($sucesscredits > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $sucesscredits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $credits;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);

                        } else {
                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            if (!empty($usernumber)) {
                                if ($usernumber['User']['sms'] == 1) {
                                    $assigned_number = $usernumber['User']['assigned_number'];
                                } else {
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                    if (!empty($user_numbers)) {
                                        $assigned_number = $user_numbers['UserNumber']['number'];
                                    } else {
                                        $assigned_number = $usernumber['User']['assigned_number'];
                                    }
                                }
                            }
                            $sucesscredits = 0;
                            $credits = 0;
                            //foreach ($subscriberPhone1 as $contactgroupid => $subscriberPhones) {
                            foreach ($Subscriber as $Subscribers) {
                                $this->Log = new Log();
                                //$firstName = $Subscribers['Contact']['name'];
                                $message = $message1;
                                //$to = $subscriberPhones;
                                $to = $Subscribers['Contact']['phone_number'];
                                //$from = $users['User']['assigned_number'];
                                $from = $assigned_number;
                                //$from = '2029993169';
                                $this->Nexmomessage->Key = NEXMO_KEY;
                                $this->Nexmomessage->Secret = NEXMO_SECRET;
                                sleep(1);
                                $response = $this->Nexmomessage->sendsms($to, $from, $message);
                                //pr($response);
                                foreach ($response->messages as $doc) {
                                    $message_id = $doc->messageid;
                                    if ($message_id != '') {
                                        $status = $doc->status;
                                        $message_id = $doc->messageid;
                                    } else {
                                        $status = $doc->status;
                                        $errortext = $doc->errortext;
                                    }
                                }
                                $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                $this->request->data['Log']['sms_id'] = $message_id;
                                $this->request->data['Log']['user_id'] = $user_id;
                                $this->request->data['Log']['group_id'] = $groupid;
                                $this->request->data['Log']['phone_number'] = $to;
                                $this->request->data['Log']['text_message'] = $message;
                                $this->request->data['Log']['route'] = 'outbox';
                                $this->request->data['Log']['sms_status'] = '';
                                $this->request->data['Log']['error_message'] = '';
                                $this->request->data['Log']['sendfrom'] = $from;
                                //echo $contactgroupid;
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                $this->request->data['ContactGroup']['question_id'] = $this->request->data['Question']['question_id'];
                                $this->ContactGroup->save($this->request->data);
                                //$subscriberPhone[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                                if ($message_id != '') {
                                    $sucesscredits = $sucesscredits + $contactcredits;
                                    $credits = $credits + 1;
                                    $this->request->data['Log']['sms_status'] = 'sent';
                                }
                                if ($status != 0) {
                                    $this->request->data['Log']['sms_status'] = 'failed';
                                    $ErrorMessage = $errortext;
                                    $this->request->data['Log']['error_message'] = $ErrorMessage;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                    //pr($groupContacts);
                                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                    $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                    $this->GroupSmsBlast->save($this->request->data);
                                }
                                $this->Log->save($this->request->data);
                            }
                            if ($sucesscredits > 0) {
                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if (!empty($usersbalance)) {
                                    $usercredit['User']['id'] = $user_id;
                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $sucesscredits;
                                    $this->User->save($usercredit);
                                }
                                app::import('Model', 'GroupSmsBlast');
                                $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $credits;
                                $this->GroupSmsBlast->save($group_blast);
                            }
                            $this->smsmail($user_id);

                        }
                    }
                    $this->Session->setFlash(__('The poll has been sent', true));
                    $this->redirect(array('controller' => 'polls', 'action' => 'question_list'));
                }
            } else {
                $this->Session->setFlash(__('You do not have any number with SMS capability', true));
            }
        }
    }

    function polling_report($id = null)
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'Question');
        $this->Question = new Question();
        $Subscriber1 = $this->Question->find('first', array('conditions' => array('Question.id' => $id)));
        $question11 = $Subscriber1['Question']['question'];
        $this->set('questions', $question11);
        
        app::import('Model', 'AnswerSubscriber');
        $this->AnswerSubscriber = new AnswerSubscriber();
        //$contactvotes = $this->AnswerSubscriber->find('all', array('conditions' => array('AnswerSubscriber.question_id' => $id)));
        
        $this->paginate = array('conditions' => array('AnswerSubscriber.question_id' => $id));
        $contactvotes = $this->paginate('AnswerSubscriber');
        $this->set('contactvotes', $contactvotes);
        
        app::import('Model', 'Option');
        $this->Option = new Option();
        $Subscriber = $this->Option->find('all', array('conditions' => array('Option.question_id' => $id)));

        $total = count($Subscriber);
        $this->Session->write('total', $total);
        
        $count = 1;
        $month_list = array();
        foreach ($Subscriber as $m_list) {
            $day = $m_list['Option']['optionb'];
            /*if (isset($month_list[$day])) {
                $month_list[$day] = $count + 1; 
                $count++;
            } else {
                $month_list[$day] = 1;
            }*/
            
            if($m_list['Option']['count'] !=0 ){
                $month_list[$day] = (int)$m_list['Option']['count'];
            }
        }

        $mon_list = array();
        for ($i = 0; $i < 6; $i++) {
            if ($i == 0) {
                $j = 'A';
            }
            if ($i == 1) {
                $j = 'B';
            }
            if ($i == 2) {
                $j = 'C';
            }
            if ($i == 3) {
                $j = 'D';
            }
            if ($i == 4) {
                $j = 'E';
            }
            if ($i == 5) {
                $j = 'F';
            }
            if (isset($month_list[$j]) && $month_list[$j] != '')
                $mon_list[$i] = $month_list[$j];
            else
                $mon_list[$i] = 0;
        }
        $caller_list = json_encode($mon_list);
        $this->set('caller_list', $caller_list);
        
        /*ob_start();
        print_r($contactvotes);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/pollreport".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
    }

    function edit($id = null)
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        $this->set('id', $id);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid Poll', true));
            $this->redirect(array('action' => 'question_list'));
        }
        //app::import('Model', 'Question');
        //$this->Question = new Question();
        //$questionedit = $this->Question->read(null, $id);
        //$questionedit = $this->Question->find('first', array('conditions' => array('Question.id ' => $id, 'Question.user_id ' => $user_id)));
        
        app::import('Model', 'Option');
        $this->Option = new Option();
        $questionedit = $this->Option->find('all', array('conditions' => array('Option.question_id' => $id), 'order' => array('Option.id' => 'ASC')));
        $this->set('questionedits', $questionedit);

        if (!empty($this->request->data)) {
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Question');
            $this->Question = new Question();
            $questioncode = $this->Question->find('first', array('conditions' => array('Question.id ' => $id, 'Question.user_id ' => $user_id)));
            if (!empty($questioncode)) {
                $this->request->data['Question']['user_id'] = $user_id;
                $this->request->data['Question']['id'] = $id;
                $this->Question->save($this->request->data);
                //app::import('Model', 'Option');
                //$this->Option = new Option();
                //$Optionvalue = $this->Option->find('all', array('conditions' => array('Option.question_id' => $id), 'order' => array('Option.id' => 'ASC')));
  
                $optionid = array();
                foreach ($questionedit as $optionid) {
                    $option_id[] = $optionid['Option']['id'];
                }
   
                $optionb = array('0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E', '5' => 'F');
                $count = 0;
                $i = 0;

                foreach ($this->request->data['Option']['autorsponder_message'] as $msg) {
                    $automsg[] = $msg;

                }                
                foreach ($this->request->data['Option']['optiona'] as $option) {
                    $this->request->data['Option']['autorsponder_message'] = $automsg[$i];
                    $this->request->data['Option']['id'] = $option_id[$i];
                    $this->request->data['Option']['question_id'] = $id;
                    $this->request->data['Option']['optiona'] = $option;
                    $this->request->data['Option']['optionb'] = $optionb[$count];
                    $this->Option->save($this->request->data);
                    $count++;
                    $i++;
                }
                $this->Session->setFlash(__('The poll has been updated', true));
                $this->redirect(array('action' => 'question_list'));
            } else {
                $this->Session->setFlash(__('The Poll could not be edited. Please, try again.', true));
                $this->redirect(array('action' => 'question_list'));

            }
        }
    }

    function check($id = null, $questionid = null)
    {
        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $Subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.group_id' => $id)));
        if ($Subscriber11['ContactGroup']['question_id'] > 0) {
            echo "A poll was already sent to this group. Please select another group, otherwise the previous poll for this group will be deactivated.";
        }
    }
}
?>