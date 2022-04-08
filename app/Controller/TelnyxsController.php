<?php
App::import('Vendor', 'mailchimp', array('file' => 'mailchimp/MailChimp.php'));
App::import('Vendor', 'getresponse', array('file' => 'getresponse/GetResponse.php'));
App::import('Vendor', 'activecampaign', array('file' => 'activecampaign/ActiveCampaign.class.php'));
App::import('Vendor', 'aweber', array('file' => 'aweber/aweber_api.php'));
App::import('Vendor', 'mailin', array('file' => 'mailin/Mailin.php'));
App::uses('CakeEmail', 'Network/Email');
class TelnyxsController extends AppController{
	var $uses = 'Users';
    var $components = array('Telnyx');
	function searchcountry(){
        $this->layout = 'popup';
    }
	function searchbynumber(){
        $this->layout = 'popup';
		if(!empty($this->request->data)){
		    $fields['filter[limit]'] = 20;
			//$country = $this->request->data['country'];
			
			if(!empty($_REQUEST['areaCode'])){
				//$areacode = $this->request->data['areaCode'];
				$fields['filter[national_destination_code][]'] = $_REQUEST['areaCode']; //$this->request->data['areaCode'];
			}
			if(!empty($_REQUEST['state'])){
			    //$state = $this->request->data['state'];
			    $fields['filter[administrative_area][]'] = strtoupper($_REQUEST['state']); //$this->request->data['state'];
			}
			if(!empty($_REQUEST['city'])){
			    //$city = $this->request->data['city'];
			    $fields['filter[locality][]']= $_REQUEST['city'];//$this->request->data['city'];
			}
			if(!empty($_REQUEST['country'])){
			    //$country = $this->request->data['country'];
			    $fields['filter[country_code][]'] = $_REQUEST['country'];//$this->request->data['country'];
			}
			if(!empty($_REQUEST['type'])){
			    //$features = $this->request->data['features'];
			    $fields['filter[features][]'] = $_REQUEST['type'];//$this->request->data['features'];
			}
			
			$response = $this->Telnyx->listNumbers(TELNYX_KEY,$fields);
		
			//print_r($response);
			
			if(isset($response->errors)){
				$errortext = $response->errors[0]->detail;
				$this->Session->setFlash(__($errortext, true));	
			}else{
				$this->set('response', $response->data);	
			}
			
			//$this->set('type', $this->request->data['type']);
		}
    }
	function assignthisnumber(){
        $this->autoRender = false;
        
		if(!empty($_REQUEST['number'])){
		    
		    Controller::loadModel('User');
            $user_id = $this->Session->read('User.id');
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $billinggroupid = trim($someone['User']['billinggroupid']);
            $message_profileid = trim($someone['User']['app_id']);
            $telnyx_callappid = trim($someone['User']['call_app_id']);
            $username = trim($someone['User']['username']);
            
            if($billinggroupid==''){
                if(trim($someone['User']['sid']) !=''){
                    $response = $this->Telnyx->createbillinggroup(trim($someone['User']['sid']),$username); 
                }else{
                    $response = $this->Telnyx->createbillinggroup(TELNYX_KEY,$username);   
                }
                if(!isset($response->errors)){
                    $billinggroupid = $response->data->id;
                    $this->User->id = $user_id;
    			    $this->User->saveField('billinggroupid', $billinggroupid);
                }
            }
        
			/*if(TELNYX_MESSAGEPROFILE=='' && trim($someone['User']['sid']) == ''){
				$response = $this->Telnyx->createmessagingprofile(TELNYX_KEY);
				if(!isset($response->errors)){
				    $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY);
				    $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billinggroupid);
            		$updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
            		
            		app::import('Model', 'Config');
					$this->Config = new Config();
					$config['Config']['id'] = 1;
					$config['Config']['telnyx_messageprofile'] = $response->data->id;
					$config['Config']['telnyx_callapp'] = $callresponse->data->id;
					if($this->Config->save($config)){
						$message_profileid = $response->data->id;
						$telnyx_callappid = $callresponse->data->id;
					}
				}else{
				    $errortext = $response->errors[0]->detail;
				    $this->Session->setFlash(__($errortext, true));
				    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
				}
			}else if (trim($someone['User']['sid']) != '' && trim($someone['User']['app_id']) == '') {
			    $response = $this->Telnyx->createmessagingprofile(TELNYX_KEY,$username);
				if(!isset($response->errors)){
				    $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY);
				    $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billinggroupid);
            		$updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
            
					$this->User->id = $user_id;
                    $this->User->saveField('app_id', $response->data->id);
                    $this->User->saveField('call_app_id', $callresponse->data->id);
                    $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
				}else{
				    $errortext = $response->errors[0]->detail;
				    $this->Session->setFlash(__($errortext, true));
				    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
				}
			}
			
			if (trim($someone['User']['app_id']) != '') {
                $message_profileid = trim($someone['User']['app_id']);
            }else {
                if (trim($message_profileid) == ''){
                    $message_profileid = TELNYX_MESSAGEPROFILE;
                }
            }
            if (trim($someone['User']['call_app_id']) != '') {
                $telnyx_callappid = trim($someone['User']['call_app_id']);
            }else {
                if (trim($telnyx_callappid) == ''){
                    $telnyx_callappid = TELNYX_CALLAPP;
                }
            }*/
		    
		    $buyresponse = $this->Telnyx->assignthisnumber(TELNYX_KEY,$_REQUEST['number']);
		    
			if(!isset($buyresponse->errors)){
			    sleep(2);
			    $numbersresponse = $this->Telnyx->listallnumbers(TELNYX_KEY);
    	
    			foreach($numbersresponse as $phone_arr){
    			    if($_REQUEST['number'] == str_replace('+','',$phone_arr->phone_number)){
    			        $phoneid = $phone_arr->id;
    			        break;
    			    }
    			    
    			}
    		    $msgresponse = $this->Telnyx->updatemessagingnumber(TELNYX_KEY,$phoneid,$message_profileid);
       			$callappresponse = $this->Telnyx->updatecallnumber(TELNYX_KEY,$phoneid,$telnyx_callappid,$billinggroupid);
			    
			    if(!isset($msgresponse->errors)){
    				Controller::loadModel('User');
    				$user_id = $this->Session->read('User.id');
    				$someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
    				if ($someone['User']['assigned_number'] == 0) {
    					$this->User->id = $this->Session->read('User.id');
    					$this->User->saveField('assigned_number', $_REQUEST['number']);
    					$this->User->saveField('phone_sid',$phoneid);
    				    if (isset($_REQUEST['voice'])) {
                            $this->User->saveField('voice', $_REQUEST['voice']);
                        }
                        if (isset($_REQUEST['sms'])) {
                            $this->User->saveField('sms', $_REQUEST['sms']);
                        }
                        if (isset($_REQUEST['mms'])) {
                            $this->User->saveField('mms', $_REQUEST['mms']);
                        }
                        if (isset($_REQUEST['fax'])) {
                            $this->User->saveField('fax', $_REQUEST['fax']);
                        }
    					$this->User->saveField('number_limit_count', 1);
    					echo 'sucess';
    					$this->Session->setFlash(__('Number Assigned', true));
    				} else {
    					app::import('Model', 'UserNumber');
    					$this->UserNumber = new UserNumber();
    					$arr['UserNumber']['user_id'] = $user_id;
    					$arr['UserNumber']['number'] = $_REQUEST['number'];
    					$arr['UserNumber']['phone_sid'] = $phoneid;
    					$arr['UserNumber']['country_code'] = '';
    					if (isset($_REQUEST['voice'])) {
                            $arr['UserNumber']['voice'] = $_REQUEST['voice'];
                        }
                        if (isset($_REQUEST['sms'])) {
                            $arr['UserNumber']['sms'] = $_REQUEST['sms'];
                        }
                        if (isset($_REQUEST['mms'])) {
                            $arr['UserNumber']['mms'] = $_REQUEST['mms'];
                        }
                        if (isset($_REQUEST['fax'])) {
                            $arr['UserNumber']['fax'] = $_REQUEST['fax'];
                        }
    					$arr['UserNumber']['api_type'] = $this->Session->read('User.api_type');
    					$this->UserNumber->save($arr);
    					if (!empty($someone)) {
    						$arr_number['User']['id'] = $user_id;
    						$arr_number['User']['number_limit_count'] = $someone['User']['number_limit_count'] + 1;
    						$this->User->save($arr_number);
    					}
    					echo 'sucess';
    					$this->Session->setFlash(__('Number Assigned', true));
    				}
			    }else{
    				    $errortext = $msgresponse->errors[0]->detail;
    				    $this->Session->setFlash(__($errortext, true));
    				    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
				    }
            }else{
                    $errortext = $buyresponse->errors[0]->detail;
				    $this->Session->setFlash(__($errortext, true));
				    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
				}
		}
	}
	function sendsms($id = null){
        $this->autoRender = false;
        $userDetails = $this->getLoggedUserDetails();
        if ($userDetails['User']['sms_balance'] > 0) {
            $to = ($this->request->data['Telnyx']['phone_number']) ? $this->request->data['Telnyx']['phone_number'] : $this->request->data['Telnyx']['phone'];
            if (!empty($userDetails)) {
                if ($userDetails['User']['sms'] == 1) {
                    $from = $userDetails['User']['assigned_number'];
                } else {
                    app::import('Model', 'UserNumber');
                    $this->UserNumber = new UserNumber();
                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $userDetails['User']['id'], 'UserNumber.sms' => 1)));
                    if (!empty($user_numbers)) {
                        $from = $user_numbers['UserNumber']['number'];
                    } else {
                        $from = $userDetails['User']['assigned_number'];
                    }
                }
            }
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $contact = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $userDetails['User']['id'], 'Contact.phone_number' => $to)));
            $stickyfrom = $contact['Contact']['stickysender'];
            if ($stickyfrom != 0) {
                $from = $stickyfrom;
            }
			$body = $this->request->data['Telnyx']['message'];  
    		$response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$body);
    		//$response = $this->Telnyx->numberlookup(TELNYX_KEY,$to);
 
  			$message_id = '';
			$status = '';
			$errortext = '';
			if(isset($response->errors)){
				$errortext = $response->errors[0]->detail;
				$status = 'failed';
			}else{
			    $message_id = $response->data->id;
				$status = 'sent';
			}
            //saving logs
            Controller::loadModel('Log');
            $this->Log->create();
            $this->request->data['Log']['sms_id'] = $message_id;
            $this->request->data['Log']['user_id'] = $this->Session->read('User.id');
            $this->request->data['Log']['phone_number'] = $to;
            $this->request->data['Log']['text_message'] = $body;
            $this->request->data['Log']['route'] = 'outbox';
            if ($errortext !='') {
                $this->request->data['Log']['sms_status'] = $status;
                $this->request->data['Log']['error_message'] = $errortext;
            } else {
                $this->request->data['Log']['sms_status'] = 'sent';
                Controller::loadModel('User');
                $this->User->id = $this->Session->read('User.id');
                if ($this->User->id != '') {
                    $length = mb_strlen($body,"UTF-8");
			        $gsm = $this->is_gsm($body);
                    if(!$gsm){
                        $credits = ceil($length / 70);
                    } else {
                        $credits = ceil($length / 160);
                    }
                    $this->User->saveField('sms_balance', ($userDetails['User']['sms_balance'] - $credits));
                    $this->smsmail($this->Session->read('User.id'));
                }
            }
            $this->Log->save($this->request->data);
            if ($errortext !='') {
                $this->Session->setFlash(__($errortext, true));
                if (!empty($id)) {
                    $this->redirect(array('controller' => 'groups'));
                } else {
                    $this->redirect(array('controller' => 'contacts'));
                }
            } else {
                $this->Session->setFlash(__('SMS message sent', true));
                if (!empty($id)) {
                    $this->redirect(array('controller' => 'groups'));
                } else {
                    $this->redirect(array('controller' => 'contacts'));
                }
            }
        } else {
            $this->Session->setFlash(__('SMS Balance too low.', true));
        }
    }
    
	function sms(){
        $this->autoRender = false;
        
        ob_start();
        header("HTTP/1.1 200 OK");
        header('Content-Encoding: none');
        header('Content-Length: '.ob_get_length());
        header('Connection: close');
        //if (is_callable('fastcgi_finish_request')) {
        //    session_write_close();
        //    fastcgi_finish_request(); 
        //}
        ob_end_flush();
        ob_flush(); 
        flush();
        
        $out = @file_get_contents('php://input');
        $event_json = json_decode('[' . $out . ']');
        $jsonObject = $event_json[0];
		
		/*ob_start();
		print_r($jsonObject);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/telnyx_incoming_json".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
		
		if(!isset($jsonObject->data->payload->to[0]->phone_number)){
		    $to = $jsonObject->data->payload->to;
		}else{
		    $to = $jsonObject->data->payload->to[0]->phone_number;
		}

        $_REQUEST['To'] = str_replace('+', '', $to);
        $_REQUEST['From'] = str_replace('+', '', $jsonObject->data->payload->from->phone_number);
        $_REQUEST['Text'] = trim($jsonObject->data->payload->text);
        if(trim($jsonObject->data->payload->type)=="MMS"){
            $_REQUEST['media'] = trim($jsonObject->data->payload->media[0]->url);
        }else{
            $_REQUEST['media'] = ''; 
        }
        $phone = $_REQUEST['From'];
        
        app::import('Model', 'User');
        $this->User = new User();
        $someone = $this->User->find('first', array('conditions' => array('assigned_number' => '' . trim($_REQUEST['To']) . '')));
        if (empty($someone)) {
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $someone = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => '' . trim($_REQUEST['To']) . '')));
        }
        $SID = trim($someone['User']['sid']);
        $AUTHTOKEN = trim($someone['User']['authtoken']);
        $user_id = $someone['User']['id'];
        $timezone = $someone['User']['timezone'];
        date_default_timezone_set($timezone);
        $sms_balance = $someone['User']['sms_balance'];
        $active = $someone['User']['active'];
        
        if (($active == 0 || $sms_balance < 1) && strtoupper(trim($_REQUEST['Text'])) != 'STOP') {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response></Response>";
            exit;
        }
        
        $checkmsgpart = explode(':', $_REQUEST['Text']);
        $checkgroup = explode(' ', $checkmsgpart[0]);
        app::import('Model', 'ActivityTimeline');
        $this->ActivityTimeline = new ActivityTimeline();
        if (strtoupper($checkgroup[0]) == 'SEND') {
            $checkbroadcast = $this->User->find('first', array('conditions' => array('User.id' => $someone['User']['id'], 'User.broadcast' => '' . trim($_REQUEST['From']) . '')));
            if (!empty($checkbroadcast)) {
                app::import('Model', 'Group');
                $this->Group = new Group();
                $groupbroadcast = $this->Group->find('first', array('conditions' => array('Group.keyword' => $checkgroup[1], array('Group.user_id' => $someone['User']['id']))));
                $group_sms_id = 0;
                if (!empty($groupbroadcast)) {
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $contactlist = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $groupbroadcast['Group']['id'], 'ContactGroup.un_subscribers' => 0, 'ContactGroup.user_id' => $user_id), 'fields' => array('Contact.phone_number')));
                    $totalSubscriber = 0;
                    if (!empty($contactlist)) {
                        $credits = CHARGEINCOMINGSMS;//1;
                        $faildsms = 0;
                        $totalSubscriber = count($contactlist);
                        $sms_balance = $checkbroadcast['User']['sms_balance'];
                        $bodymsg = $checkmsgpart[1];
                        //$length = strlen(utf8_decode(substr($bodymsg, 0, 1600)));
                        $length = mb_strlen($bodymsg,"UTF-8");
			            $gsm = $this->is_gsm($bodymsg);
                        if(!$gsm){//if (strlen($bodymsg) != strlen(utf8_decode($bodymsg))) {
                            $contactcredits = ceil($length / 70);
                        } else {
                            $contactcredits = ceil($length / 160);
                        }
                        if ($sms_balance < ($totalSubscriber * $contactcredits)) {
                            $message = "You do not have enough credits to broadcast this message to " . $groupbroadcast['Group']['group_name'];
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }else{
                                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }
							
                            $this->smsmail($someone['User']['id']);
                            $this->User->id = $someone['User']['id'];
                            if ($this->User->id != '') {
                                $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - CHARGEINCOMINGSMS - 1));
                            }
                            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                            echo "<Response></Response>";
                            exit;
                        }
                        app::import('Model', 'GroupSmsBlast');
                        $this->GroupSmsBlast = new GroupSmsBlast();
                        $group_blast['GroupSmsBlast']['user_id'] = $someone['User']['id'];
                        $group_blast['GroupSmsBlast']['group_id'] = $groupbroadcast['Group']['id'];
                        $group_blast['GroupSmsBlast']['responder'] = 1;
                        $group_blast['GroupSmsBlast']['totals'] = $totalSubscriber;
                        $this->GroupSmsBlast->save($group_blast);
                        $group_sms_id = $this->GroupSmsBlast->id;
                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                        foreach ($contactlist as $contactlists) {
                            $tonumber = $contactlists['Contact']['phone_number'];
                            $fromnumber = $_REQUEST['To'];
                            $bodymsg = $checkmsgpart[1];
                            if ($SID == ''){
                                $response = $this->Telnyx->sendsms(TELNYX_KEY,$tonumber,$fromnumber,$bodymsg);
                            }else{
                                $response = $this->Telnyx->sendsms($SID,$tonumber,$fromnumber,$bodymsg);
                            }

							if(!isset($response->errors)){
								$credits = $credits + $contactcredits;
								if (!empty($groupContacts)) {
                                    $successsms = $successsms + 1;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $GroupSmsBlast_arr['GroupSmsBlast']['id'] = $group_sms_id;
                                    $GroupSmsBlast_arr['GroupSmsBlast']['total_successful_messages'] = $successsms;
                                    $this->GroupSmsBlast->save($GroupSmsBlast_arr);
                                }
							}else{
								if (!empty($groupContacts)) {
                                    $faildsms = $faildsms + 1;
                                    app::import('Model', 'GroupSmsBlast');
                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                    $GroupSmsBlast_arr['GroupSmsBlast']['id'] = $group_sms_id;
                                    $GroupSmsBlast_arr['GroupSmsBlast']['total_failed_messages'] = $faildsms;
                                    $this->GroupSmsBlast->save($GroupSmsBlast_arr);
                                }
							}
                            $sms_id = '';
							if(!isset($response->errors)){
                                $sms_status = 'sent';
                                $sms_id = $response->data->id;
                            }else {
                                $sms_status = 'failed';
                                $errortext = $response->errors[0]->detail;
                            }
                            Controller::loadModel('Log');
                            $this->Log->create();
                            $log_ar['Log']['sms_id'] = $sms_id;
                            $log_ar['Log']['user_id'] = $someone['User']['id'];
                            $log_ar['Log']['group_id'] = $groupbroadcast['Group']['id'];
                            $log_ar['Log']['group_sms_id'] = $group_sms_id;
                            $log_ar['Log']['phone_number'] = $tonumber;
                            $log_ar['Log']['text_message'] = $bodymsg;
                            $log_ar['Log']['sms_status'] = $sms_status;
                            $log_ar['Log']['error_message'] = $errortext;
                            $log_ar['Log']['sendfrom'] = $fromnumber;
                            $log_ar['Log']['route'] = 'outbox';
                            $this->Log->save($log_ar);
                            $this->smsmail($someone['User']['id']);
                        }
                        $message = "Your SMS broadcast has been sent to " . $groupbroadcast['Group']['group_name'];
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
						$credits = $credits + 1;
                        $this->smsmail($someone['User']['id']);
                        $this->User->id = $someone['User']['id'];
                        if ($this->User->id != '') {
                            $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
                        }
                    }
                    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                    echo "<Response></Response>";
                    exit;
                }
            }
        }
        if ($someone['User']['birthday_wishes'] == 0) {
            $birthday_wishes = $_REQUEST['Text'];
            $tempDate = explode('-', $birthday_wishes);
            if(is_numeric($tempDate[0]) && is_numeric($tempDate[1]) && is_numeric($tempDate[2])){
                if (checkdate($tempDate[1], $tempDate[2], $tempDate[0])) {//checkdate(month, day, year)
                    $bday = 1;
                } else {
                    $bday = 0;
                }
            }
            if ($bday == 1) {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.id' => 'desc')));
                if (!empty($contact)) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $cont['Contact']['id'] = $contact['Contact']['id'];
                    $cont['Contact']['birthday'] = trim($birthday_wishes);
                    $this->Contact->save($cont);

                    //*********** Save to activity timeline
                    $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                    $timeline['ActivityTimeline']['contact_id'] = $contact['Contact']['id'];
                    $timeline['ActivityTimeline']['activity'] = 13;
                    $timeline['ActivityTimeline']['title'] = 'Capture Birthday';
                    $timeline['ActivityTimeline']['description'] = 'Contact texted in their birthday: ' . $birthday_wishes . ' so they can receive birthday SMS wishes.';
                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                    $this->ActivityTimeline->save($timeline);
                    //*************
                    $someoneuser = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    if ($someoneuser['User']['sms_balance'] > 0) {
                        app::import('Model', 'User');
                        $this->User = new User();
                        $this->User->id = $someone['User']['id'];
                        if ($this->User->id != '') {
                            $this->User->saveField('sms_balance', ($someoneuser['User']['sms_balance'] - CHARGEINCOMINGSMS - 1));
                        }
                        $message = 'Thanks for your response.';
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
                        $this->smsmail($someone['User']['id']);
                        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                        echo "<Response></Response>";
                        exit;
                    }
                }
            }
        }
        if ($someone['User']['capture_email_name'] == 0) {
            $capture_email_name = explode(':', $_REQUEST['Text']);
            if ((strtoupper($capture_email_name[0]) == 'EMAIL')) {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.id' => 'desc')));
                if (!empty($contact)) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $cont['Contact']['id'] = $contact['Contact']['id'];
                    $cont['Contact']['email'] = trim($capture_email_name[1]);
                    $this->Contact->save($cont);

                    //*********** Save to activity timeline
                    $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                    $timeline['ActivityTimeline']['contact_id'] = $contact['Contact']['id'];
                    $timeline['ActivityTimeline']['activity'] = 12;
                    $timeline['ActivityTimeline']['title'] = 'Capture Email';
                    $timeline['ActivityTimeline']['description'] = 'Contact texted in their email address: ' . $capture_email_name[1] . ' so you can also contact them via email if you choose.';
                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                    $this->ActivityTimeline->save($timeline);
                    //*************
                    $someoneuser = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    if ($someoneuser['User']['email_apikey'] != '' && $someoneuser['User']['email_listid'] != '') {
                        if ($someoneuser['User']['email_service'] == 1) { //Mailchimp
                            $list_id = $someoneuser['User']['email_listid'];
                            $MailChimp = new MailChimp($someoneuser['User']['email_apikey']);
                            if ($contact['Contact']['name'] != '') {
                                $fullname = explode(' ', $contact['Contact']['name']);
                                $firstname = $fullname[0];
                                $result = $MailChimp->post("lists/$list_id/members", array(
                                    'email_address' => $capture_email_name[1],
                                    'status' => 'subscribed',
                                    'merge_fields' => array('FNAME' => $firstname),
                                ));
                            } else {
                                $result = $MailChimp->post("lists/$list_id/members", array(
                                    'email_address' => $capture_email_name[1],
                                    'status' => 'subscribed',
                                ));
                            }
                        } else if ($someoneuser['User']['email_service'] == 2) { //Getresponse
                            $list_id = $someoneuser['User']['email_listid'];
                            $GetResponse = new GetResponse($someoneuser['User']['email_apikey']);
                            if ($contact['Contact']['name'] != '') {
                                $result = $GetResponse->addContact(array(
                                    'email' => $capture_email_name[1],
                                    'name' => $contact['Contact']['name'],
                                    'campaign' => array('campaignId' => $list_id)));
                            } else {
                                $result = $GetResponse->addContact(array(
                                    'email' => $capture_email_name[1],
                                    'campaign' => array('campaignId' => $list_id)));
                            }
                        } else if ($someoneuser['User']['email_service'] == 3) { //Active Campaign
                            $ac = new ActiveCampaign($someoneuser['User']['email_apiurl'], $someoneuser['User']['email_apikey']);
                            $list_id = (int)$someoneuser['User']['email_listid'];
                            $fullname = explode(' ', $contact['Contact']['name']);
                            $firstname = $fullname[0];
                            $newcontact = array(
                                'email' => $capture_email_name[1],
                                'first_name' => $firstname,
                                'phone' => $contact['Contact']['phone_number'],
                                'p[{$list_id}]' => $list_id,
                                'status[{$list_id}]' => 1, // "Active" status
                            );
                            $contact_sync = $ac->api("contact/sync", $newcontact);
                        } else if ($someoneuser['User']['email_service'] == 4) { //AWeber
                            $aweber = new AWeberAPI($someoneuser['User']['consumerkey'], $someoneuser['User']['consumersecret']);
                            $account = $aweber->getAccount($someoneuser['User']['accesskey'], $someoneuser['User']['accesssecret']);
                            $account_id = $account->id;
                            $list_id = $someoneuser['User']['email_listid'];
                            $fullname = explode(' ', $contact['Contact']['name']);
                            $firstname = $fullname[0];
                            $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                            $list = $account->loadFromUrl($listURL);
                            $params = array(
                                'email' => $capture_email_name[1],
                                'name' => $firstname,
                            );
                            $subscribers = $list->subscribers;
                            $new_subscriber = $subscribers->create($params);
                        } else if ($someoneuser['User']['email_service'] == 5) { //Sendinblue
                            $mailin = new Mailin(SENDINBLUE_APIURL, $someoneuser['User']['email_apikey']);
                            $list_id = (int)$someoneuser['User']['email_listid'];
                            $fullname = explode(' ', $contact['Contact']['name']);
                            $firstname = $fullname[0];
                            $data = array("email" => $capture_email_name[1],
                                "attributes" => array("FIRSTNAME" => $firstname),
                                "listid" => array($list_id)
                            );
                            $result = $mailin->create_update_user($data);
                        }
                    }
                    if ($someoneuser['User']['sms_balance'] > 0) {
                        app::import('Model', 'User');
                        $this->User = new User();
                        $this->User->id = $someone['User']['id'];
                        if ($this->User->id != '') {
                            $this->User->saveField('sms_balance', ($someoneuser['User']['sms_balance'] - CHARGEINCOMINGSMS - 1));
                        }
                        $message = 'Thanks for your response.';
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }

                        $this->smsmail($someone['User']['id']);
                        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                        echo "<Response></Response>";
                        exit;
                    }
                }
            } else if ((strtoupper($capture_email_name[0]) == 'NAME')) {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.id' => 'desc')));
                if (!empty($contact)) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $cont['Contact']['id'] = $contact['Contact']['id'];
                    $cont['Contact']['name'] = trim($capture_email_name[1]);
                    $this->Contact->save($cont);

                    //*********** Save to activity timeline
                    $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                    $timeline['ActivityTimeline']['contact_id'] = $contact['Contact']['id'];
                    $timeline['ActivityTimeline']['activity'] = 11;
                    $timeline['ActivityTimeline']['title'] = 'Capture Name';
                    $timeline['ActivityTimeline']['description'] = 'Contact texted in their name: ' . $capture_email_name[1] . ' so you can personalize your bulk outbound messages by using the %%Name%% token inside the message body.';
                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                    $this->ActivityTimeline->save($timeline);
                    //*************
                    $someoneuser = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    if ($someoneuser['User']['sms_balance'] > 0) {
                        app::import('Model', 'User');
                        $this->User = new User();
                        $this->User->id = $someone['User']['id'];
                        $message = EMAIL_CAPTURE_MSG;
                        //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                        $length = mb_strlen($message,"UTF-8");
			            $gsm = $this->is_gsm($message);
                        
                        if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                            $credits = ceil($length / 70) + CHARGEINCOMINGSMS;
                        } else {
                            $credits = ceil($length / 160) + CHARGEINCOMINGSMS;
                        }
                        if ($this->User->id != '') {
                            $this->User->saveField('sms_balance', ($someoneuser['User']['sms_balance'] - $credits));
                        }
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }

                    }
                    $this->smsmail($someone['User']['id']);
                    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                    echo "<Response></Response>";
                    exit;
                }
            }
        }
		//multi contest
		app::import('Model','MultiContest');
		$this->MultiContest = new MultiContest();
		$multicontestkeywords = $this->MultiContest->find('first',array('conditions'=>array('MultiContest.user_id'=>$someone['User']['id'],'upper(MultiContest.keyword)'=>strtoupper($_REQUEST['Text']))));
		if(!empty($multicontestkeywords)){
			$contact_id = 0;
			if($multicontestkeywords['MultiContest']['group_id'] > 0){
				app::import('Model', 'ContactGroup');
				$this->ContactGroup = new ContactGroup();
				$contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.group_id' =>$multicontestkeywords['MultiContest']['group_id'],'ContactGroup.user_id' =>$someone['User']['id']), 'order' => array('ContactGroup.id' => 'desc')));
				if(empty($contact)){
					app::import('Model', 'Contact');
					$this->Contact = new Contact();
					$contact = $this->Contact->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'Contact.user_id' =>$someone['User']['id'])));
					if(empty($contact)){
						$contacts_arr['Contact']['id'] = '';
						if (NUMVERIFY != '') {
							$numbervalidation = $this->validateNumber($phone);
							$errorcode = $numbervalidation['error']['code'];
							if ($errorcode == '') {
								$contacts_arr['Contact']['carrier'] = $numbervalidation['carrier'];
								$contacts_arr['Contact']['location'] = $numbervalidation['location'];
								$contacts_arr['Contact']['phone_country'] = $numbervalidation['country_name'];
								$contacts_arr['Contact']['line_type'] = $numbervalidation['line_type'];
							}
						}
						$contacts_arr['Contact']['phone_number'] = $phone;
						$contacts_arr['Contact']['user_id'] = $someone['User']['id'];
						$contacts_arr['Contact']['created'] = date('Y-m-d H:i:s', time());
						$contacts_arr['Contact']['color'] = $this->choosecolor();
						if($this->Contact->save($contacts_arr)){
							$contact_id = $this->Contact->id;
						}
					}else{
						$contact_id = $contact['Contact']['id'];
					}
					app::import('Model','ContactGroup');
					$this->ContactGroup = new ContactGroup();
					$contactgroupids=$this->ContactGroup->find('first',array('conditions'=>array('ContactGroup.contact_id'=>$contact_id,'ContactGroup.group_id'=>$multicontestkeywords['MultiContest']['group_id'],'ContactGroup.user_id'=> $someone['User']['id'])));
					if(empty($contactgroupids)){
						app::import('Model', 'ContactGroup');
						$this->ContactGroup = new ContactGroup();
						$contactgroups_arr['ContactGroup']['id'] = '';
						$contactgroups_arr['ContactGroup']['user_id'] = $someone['User']['id'];
						$contactgroups_arr['ContactGroup']['contact_id'] = $contact_id;
						$contactgroups_arr['ContactGroup']['group_id'] = $multicontestkeywords['MultiContest']['group_id'];
						$contactgroups_arr['ContactGroup']['group_subscribers'] = $_REQUEST['Text'];
						$contactgroups_arr['ContactGroup']['subscribed_by_sms'] = 1;
						$contactgroups_arr['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
						if($this->ContactGroup->save($contactgroups_arr)){
					        app::import('Model', 'Group');
					        $this->Group = new Group();
					        $contactgroup=$this->Group->find('first',array('conditions'=>array('Group.id'=>$multicontestkeywords['MultiContest']['group_id'])));
							if(!empty($contactgroup)){
								$group_arr['Group']['id'] = $contactgroup['Group']['id'];
								$group_arr['Group']['totalsubscriber'] = $contactgroup['Group']['totalsubscriber'] + 1;
								$this->Group->save($group_arr);
							}
						}
					}
				}else{
					$contact_id = $contact['ContactGroup']['contact_id'];
				}
			}
			app::import('Model','MultiContestQuestion');
			$this->MultiContestQuestion = new MultiContestQuestion();
			$multicontestquestion = $this->MultiContestQuestion->find('first',array('conditions'=>array('MultiContestQuestion.user_id'=>$someone['User']['id'],'MultiContestQuestion.multi_contest_id'=>$multicontestkeywords['MultiContest']['id']),'order' =>array('MultiContestQuestion.id' => 'asc')));
			if(!empty($multicontestquestion)){
				app::import('Model','MultiContestSubscriber');
				$this->MultiContestSubscriber = new MultiContestSubscriber();
				app::import('Model','MultiContestSubscriberArchive');
				$this->MultiContestSubscriberArchive = new MultiContestSubscriberArchive();
				if($multicontestkeywords['MultiContest']['finish_all_questions'] == 0){
					$this->MultiContestSubscriber->deleteAll(array('MultiContestSubscriber.multi_contest_id' =>$multicontestkeywords['MultiContest']['id'],'MultiContestSubscriber.phone' =>$phone));
				}
				if($multicontestkeywords['MultiContest']['finish_all_questions'] == 1){
					//$total_multi_contest_subscribers_questions = $this->MultiContestSubscriber->find('count',array('conditions'=>array('MultiContestSubscriber.user_id'=>$someone['User']['id'],'MultiContestSubscriber.multi_contest_id'=>$multicontestkeywords['MultiContest']['id'],'MultiContestSubscriber.phone'=>$phone),'order' =>array('MultiContestSubscriber.id' => 'desc')));
					$total_multi_contest_subscribers_questions = $this->MultiContestSubscriber->find('count',array('conditions'=>array('MultiContestSubscriber.user_id'=>$someone['User']['id'],'MultiContestSubscriber.multi_contest_id'=>$multicontestkeywords['MultiContest']['id'],'MultiContestSubscriber.phone'=>$phone,'MultiContestSubscriber.total_answer_questions'=>1),'order' =>array('MultiContestSubscriber.id' => 'desc')));
					$total_multi_contest_questions = $this->MultiContestQuestion->find('count',array('conditions'=>array('MultiContestQuestion.user_id'=>$someone['User']['id'],'MultiContestQuestion.multi_contest_id'=>$multicontestkeywords['MultiContest']['id']),'order' =>array('MultiContestQuestion.id' => 'desc')));
					if($total_multi_contest_questions == $total_multi_contest_subscribers_questions){
						app::import('Model','MultiContestSubscriber');
						$this->MultiContestSubscriber = new MultiContestSubscriber();
						$this->MultiContestSubscriber->deleteAll(array('MultiContestSubscriber.multi_contest_id' =>$multicontestkeywords['MultiContest']['id'],'MultiContestSubscriber.phone' =>$phone));
					}
				}
				$multicontestsubscribers = $this->MultiContestSubscriber->find('first',array('conditions'=>array('MultiContestSubscriber.user_id'=>$someone['User']['id'],'MultiContestSubscriber.multi_contest_id'=>$multicontestkeywords['MultiContest']['id'],'MultiContestSubscriber.phone'=>$phone),'order' =>array('MultiContestSubscriber.id' => 'desc')));
				if(empty($multicontestsubscribers)){
					//app::import('Model','MultiContestSubscriber');
					//$this->MultiContestSubscriber = new MultiContestSubscriber();
					//app::import('Model','MultiContestSubscriberArchive');
				    //$this->MultiContestSubscriberArchive = new MultiContestSubscriberArchive();
					$multicontest_subscriber['MultiContestSubscriber']['id'] = '';
					$multicontest_subscriber['MultiContestSubscriber']['user_id'] = $someone['User']['id'];
					$multicontest_subscriber['MultiContestSubscriber']['contact_id'] = $contact_id;
					$multicontest_subscriber['MultiContestSubscriber']['multi_contest_id'] = $multicontestkeywords['MultiContest']['id'];
					$multicontest_subscriber['MultiContestSubscriber']['multi_contest_question_id'] = $multicontestquestion['MultiContestQuestion']['id'];
					$multicontest_subscriber['MultiContestSubscriber']['question'] = $multicontestquestion['MultiContestQuestion']['question'];
					$multicontest_subscriber['MultiContestSubscriber']['phone'] = $phone;
					$multicontest_subscriber['MultiContestSubscriber']['total_questions'] = 1;
					$multicontest_subscriber['MultiContestSubscriber']['total_answer_questions'] = 0;
					$multicontest_subscriber['MultiContestSubscriber']['answer'] = '';
					$multicontest_subscriber['MultiContestSubscriber']['created'] = date('Y-m-d H:i:s');
					
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['id'] = '';
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['user_id'] = $someone['User']['id'];
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['contact_id'] = $contact_id;
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['multi_contest_id'] = $multicontestkeywords['MultiContest']['id'];
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['multi_contest_question_id'] = $multicontestquestion['MultiContestQuestion']['id'];
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['question'] = $multicontestquestion['MultiContestQuestion']['question'];
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['phone'] = $phone;
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['total_questions'] = 1;
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['total_answer_questions'] = 0;
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['answer'] = '';
					$multicontest_subscriber_archive['MultiContestSubscriberArchive']['created'] = date('Y-m-d H:i:s');
					if($this->MultiContestSubscriber->save($multicontest_subscriber)){
					    $this->MultiContestSubscriberArchive->save($multicontest_subscriber_archive);
						$options = '';
						app::import('Model','MultiContestQuestionsOption');
						$this->MultiContestQuestionsOption = new MultiContestQuestionsOption();
						$answers_arr = $this->MultiContestQuestionsOption->find('all',array('conditions'=>array('MultiContestQuestionsOption.question_id'=>$multicontestquestion['MultiContestQuestion']['id'],'MultiContestQuestionsOption.user_id'=>$someone['User']['id'],'MultiContestQuestionsOption.multi_contest_id'=>$multicontestkeywords['MultiContest']['id']),'order' =>array('MultiContestQuestionsOption.id' => 'asc')));
						if(!empty($answers_arr)){
							foreach($answers_arr as $answer_arr){
								$options .= $answer_arr['MultiContestQuestionsOption']['answer'].". ".$answer_arr['MultiContestQuestionsOption']['description']."\n";
							}
						}
						$sendthis = "Reply with letter of your choice";
						$message = $multicontestquestion['MultiContestQuestion']['question']. "\n" . $options . "\n" . $sendthis . "\n" . OPTMSG;
						$length = mb_strlen($message,"UTF-8");
						$gsm = $this->is_gsm($message);
						if(!$gsm){
							$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
						} else {
							$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
						}
						app::import('Model', 'User');
						$this->User = new User();
						$this->User->id = $someone['User']['id'];
						if ($this->User->id != '') {
							$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
						}
						if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
						echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
						echo "<Response>";
						echo "</Response>";
						exit();
					}
				}else{
					$message = 'You must finish all questions in the sequence before texting in the keyword again.';
					$length = mb_strlen($message,"UTF-8");
					$gsm = $this->is_gsm($message);
					if(!$gsm){
						$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
					} else {
						$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
					}
					app::import('Model', 'User');
					$this->User = new User();
					$this->User->id = $someone['User']['id'];
					if ($this->User->id != '') {
						$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
					}
					if ($SID == ''){
						$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
					}else{
						$this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
					}
					echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
					echo "<Response>";
					echo "</Response>";
					exit();
				}
			}
		}else{
			app::import('Model','MultiContestSubscriber');
			$this->MultiContestSubscriber = new MultiContestSubscriber();
			app::import('Model','MultiContestSubscriberArchive');
			$this->MultiContestSubscriberArchive = new MultiContestSubscriberArchive();
			$multicontestsubscribers = $this->MultiContestSubscriber->find('first',array('conditions'=>array('MultiContestSubscriber.total_answer_questions'=>0,'MultiContestSubscriber.user_id'=>$someone['User']['id'],'MultiContestSubscriber.phone'=>$phone),'order' =>array('MultiContestSubscriber.id' => 'desc')));
			if(!empty($multicontestsubscribers)){
				app::import('Model','MultiContestQuestionsOption');
				$this->MultiContestQuestionsOption = new MultiContestQuestionsOption();
				$answer = $this->MultiContestQuestionsOption->find('first',array('conditions'=>array('MultiContestQuestionsOption.answer'=>$_REQUEST['Text'],'MultiContestQuestionsOption.question_id'=>$multicontestsubscribers['MultiContestSubscriber']['multi_contest_question_id'],'MultiContestQuestionsOption.user_id'=>$someone['User']['id'],'MultiContestQuestionsOption.multi_contest_id'=>$multicontestsubscribers['MultiContestSubscriber']['multi_contest_id']),'order' =>array('MultiContestQuestionsOption.id' => 'asc')));
				$response = '';
				$description_answer = '';
				$response_email = '';
				$response_phone = '';
				$response_text_msg = '';
				if(!empty($answer)){
					$response = $answer['MultiContestQuestionsOption']['response'];
					$description_answer = ' - '.$answer['MultiContestQuestionsOption']['description'];
					$response_email = $answer['MultiContestQuestionsOption']['email'];
					$response_phone = $answer['MultiContestQuestionsOption']['phone'];
					$response_text_msg = $answer['MultiContestQuestionsOption']['text'];
					app::import('Model', 'Contact');
					$this->Contact = new Contact();
					//$contact_arr = $this->Contact->find('first', array('conditions' => array('Contact.id' =>$contact_id)));
					$contact_arr = $this->Contact->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'Contact.user_id' =>$someone['User']['id'])));
					if(!empty($contact_arr)){
						$response_text_msg = str_replace('%%Name%%', $contact_arr['Contact']['name'], $response_text_msg);
						$response_text_msg = str_replace('%%Number%%', $contact_arr['Contact']['phone_number'], $response_text_msg);
					}else{
						$response_text_msg = str_replace('%%Name%%', '', $response_text_msg);
						$response_text_msg = str_replace('%%Number%%', '', $response_text_msg);
					}
				//}
    				$multicontest_subscribers['MultiContestSubscriber']['id'] = $multicontestsubscribers['MultiContestSubscriber']['id'];
    				$multicontest_subscribers['MultiContestSubscriber']['total_answer_questions'] = 1;
    				$multicontest_subscribers['MultiContestSubscriber']['answer'] = $_REQUEST['Text'].''.$description_answer;
    				
    				$multicontest_subscribers_archive['MultiContestSubscriberArchive']['id'] = $multicontestsubscribers['MultiContestSubscriber']['id'];
    				$multicontest_subscribers_archive['MultiContestSubscriberArchive']['total_answer_questions'] = 1;
    				$multicontest_subscribers_archive['MultiContestSubscriberArchive']['answer'] = $_REQUEST['Text'].''.$description_answer;
    				
    				if($this->MultiContestSubscriber->save($multicontest_subscribers)){
    				    $this->MultiContestSubscriberArchive->save($multicontest_subscribers_archive);
    					app::import('Model','MultiContestQuestion');
    					$this->MultiContestQuestion = new MultiContestQuestion();
    					$multicontestquestion = $this->MultiContestQuestion->find('first',array('conditions'=>array('MultiContestQuestion.id > '=>$multicontestsubscribers['MultiContestSubscriber']['multi_contest_question_id'],'MultiContestQuestion.user_id'=>$someone['User']['id'],'MultiContestQuestion.multi_contest_id'=>$multicontestsubscribers['MultiContestSubscriber']['multi_contest_id']),'order' =>array('MultiContestQuestion.id' => 'asc')));
    					if(!empty($multicontestquestion)){
    						//app::import('Model','MultiContestSubscriber');
    						//$this->MultiContestSubscriber = new MultiContestSubscriber();
    						$multicontest_subscriber['MultiContestSubscriber']['id'] = '';
    						$multicontest_subscriber['MultiContestSubscriber']['user_id'] = $someone['User']['id'];
    						$multicontest_subscriber['MultiContestSubscriber']['contact_id'] = $multicontestsubscribers['MultiContestSubscriber']['contact_id'];
    						$multicontest_subscriber['MultiContestSubscriber']['multi_contest_id'] = $multicontestquestion['MultiContestQuestion']['multi_contest_id'];
    						$multicontest_subscriber['MultiContestSubscriber']['multi_contest_question_id'] = $multicontestquestion['MultiContestQuestion']['id'];
    						$multicontest_subscriber['MultiContestSubscriber']['question'] = $multicontestquestion['MultiContestQuestion']['question'];
    						$multicontest_subscriber['MultiContestSubscriber']['phone'] = $phone;
    						$multicontest_subscriber['MultiContestSubscriber']['total_questions'] = 1;
    						$multicontest_subscriber['MultiContestSubscriber']['total_answer_questions'] = 0;
    						$multicontest_subscriber['MultiContestSubscriber']['answer'] = '';
    						$multicontest_subscriber['MultiContestSubscriber']['created'] = date('Y-m-d H:i:s');
    						
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['id'] = '';
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['user_id'] = $someone['User']['id'];
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['contact_id'] = $multicontestsubscribers['MultiContestSubscriber']['contact_id'];
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['multi_contest_id'] = $multicontestquestion['MultiContestQuestion']['multi_contest_id'];
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['multi_contest_question_id'] = $multicontestquestion['MultiContestQuestion']['id'];
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['question'] = $multicontestquestion['MultiContestQuestion']['question'];
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['phone'] = $phone;
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['total_questions'] = 1;
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['total_answer_questions'] = 0;
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['answer'] = '';
    						$multicontest_subscriber_archive['MultiContestSubscriberArchive']['created'] = date('Y-m-d H:i:s');
    						if($this->MultiContestSubscriber->save($multicontest_subscriber)){
    						    $this->MultiContestSubscriberArchive->save($multicontest_subscriber_archive);
    							if(($response_email !='') && ($response_text_msg !='')){
    								if (strpos($response_email, ',') !== false) {
    									$response_email_arr = explode(",",$response_email);
    									if(!empty($response_email_arr)){
    										foreach($response_email_arr as $response_email_arrs){
    											try {
    												$Email = new CakeEmail();
    												if(EMAILSMTP==1){
    													$Email->config('smtp');
    												}
    												$Email->from(array(SUPPORT_EMAIL => SITENAME));
    												$Email->to($response_email_arrs);
    												$Email->subject('Q&A SMS Bot Notification');
    												$Email->emailFormat('html');
    												$Email->send($response_text_msg);
    											}catch(Exception $e) {
    											}
    										}
    									}
    								}else{
    									try {
    										$Email = new CakeEmail();
    										if(EMAILSMTP==1){
    											$Email->config('smtp');
    										}
    										$Email->from(array(SUPPORT_EMAIL => SITENAME));
    										$Email->to($response_email);
    										$Email->subject('Q&A SMS Bot Notification');
    										$Email->emailFormat('html');
    										$Email->send($response_text_msg);
    									}catch(Exception $e) {
    									}
    								}
    							}
    							if(($response_phone !='') && ($response_text_msg !='')){
    								if (strpos($response_phone, ',') !== false) {
    									$response_phone_arr = explode(",",$response_phone);
    									if(!empty($response_phone_arr)){
    										foreach($response_phone_arr as $response_phone_arrs){
    											try {
    												if ($SID == ''){
    													$this->Telnyx->sendsms(TELNYX_KEY,$response_phone_arrs,$_REQUEST['To'],$response_text_msg);
    												}else{
    													$this->Telnyx->sendsms($SID,$response_phone_arrs,$_REQUEST['To'],$response_text_msg);
    												}
    												$length = mb_strlen($response_text_msg,"UTF-8");
    												$gsm = $this->is_gsm($response_text_msg);
    												if(!$gsm){
    													$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
    												} else {
    													$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
    												}
    												app::import('Model', 'User');
    												$this->User = new User();
    												$this->User->id = $someone['User']['id'];
    												if ($this->User->id != '') {
    													$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
    												}
    											}catch(Exception $e) {
    											}
    										}
    									}
    								}else{
    									try {
    										if ($SID == ''){
    											$this->Telnyx->sendsms(TELNYX_KEY,$response_phone,$_REQUEST['To'],$response_text_msg);
    										}else{
    											$this->Telnyx->sendsms($SID,$response_phone,$_REQUEST['To'],$response_text_msg);
    										}
    										$length = mb_strlen($response_text_msg,"UTF-8");
    										$gsm = $this->is_gsm($response_text_msg);
    										if(!$gsm){
    											$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
    										} else {
    											$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
    										}
    										app::import('Model', 'User');
    										$this->User = new User();
    										$this->User->id = $someone['User']['id'];
    										if ($this->User->id != '') {
    											$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
    										}
    									}catch(Exception $e) {
    									}
    								}
    							}
    							$options = '';
    							app::import('Model','MultiContestQuestionsOption');
    							$this->MultiContestQuestionsOption = new MultiContestQuestionsOption();
    							$answers_arr = $this->MultiContestQuestionsOption->find('all',array('conditions'=>array('MultiContestQuestionsOption.question_id'=>$multicontestquestion['MultiContestQuestion']['id'],'MultiContestQuestionsOption.user_id'=>$someone['User']['id'],'MultiContestQuestionsOption.multi_contest_id'=>$multicontestquestion['MultiContestQuestion']['multi_contest_id']),'order' =>array('MultiContestQuestionsOption.id' => 'asc')));
    							if(!empty($answers_arr)){
    								foreach($answers_arr as $answer_arr){
    									$options .= $answer_arr['MultiContestQuestionsOption']['answer'].". ".$answer_arr['MultiContestQuestionsOption']['description']."\n";
    								}
    							}
    							$sendthis = "Reply with letter of your choice";
    							$message = $response. "\n" .$multicontestquestion['MultiContestQuestion']['question']. "\n" . $options . "\n" . $sendthis . "\n" . OPTMSG;
    							$length = mb_strlen($message,"UTF-8");
    							$gsm = $this->is_gsm($message);
    							if(!$gsm){
    								$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
    							} else {
    								$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
    							}
    							app::import('Model', 'User');
    							$this->User = new User();
    							$this->User->id = $someone['User']['id'];
    							if ($this->User->id != '') {
    								$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
    							}
    							if ($SID == ''){
    								$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
    							}else{
    								$this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
    							}
    							echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    							echo "<Response>";
    							echo "</Response>";
    							exit();
    						}
    					}else{ 
    						if(($response_email !='') && ($response_text_msg !='')){
    							if (strpos($response_email, ',') !== false) {
    								$response_email_arr = explode(",",$response_email);
    								if(!empty($response_email_arr)){
    									foreach($response_email_arr as $response_email_arrs){
    										try {
    											$Email = new CakeEmail();
    											if(EMAILSMTP==1){
    												$Email->config('smtp');
    											}
    											$Email->from(array(SUPPORT_EMAIL => SITENAME));
    											$Email->to($response_email_arrs);
    											$Email->subject('Q&A SMS Bot Notification');
    											$Email->emailFormat('html');
    											$Email->send($response_text_msg);
    										}catch(Exception $e) {
    										}
    									}
    								}
    							}else{
    								try {
    									$Email = new CakeEmail();
    									if(EMAILSMTP==1){
    										$Email->config('smtp');
    									}
    									$Email->from(array(SUPPORT_EMAIL => SITENAME));
    									$Email->to($response_email);
    									$Email->subject('Q&A SMS Bot Notification');
    									$Email->emailFormat('html');
    									$Email->send($response_text_msg);
    								}catch(Exception $e) {
    								}
    							}
    						}
    						if(($response_phone !='') && ($response_text_msg !='')){
    							if (strpos($response_phone, ',') !== false) {
    								$response_phone_arr = explode(",",$response_phone);
    								if(!empty($response_phone_arr)){
    									foreach($response_phone_arr as $response_phone_arrs){
    										try {
    											if ($SID == ''){
    												$this->Telnyx->sendsms(TELNYX_KEY,$response_phone_arrs,$_REQUEST['To'],$response_text_msg);
    											}else{
    												$this->Telnyx->sendsms($SID,$response_phone_arrs,$_REQUEST['To'],$response_text_msg);
    											}
    											$length = mb_strlen($response_text_msg,"UTF-8");
    											$gsm = $this->is_gsm($response_text_msg);
    											if(!$gsm){
    												$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
    											} else {
    												$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
    											}
    											app::import('Model', 'User');
    											$this->User = new User();
    											$this->User->id = $someone['User']['id'];
    											if ($this->User->id != '') {
    												$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
    											}
    										}catch(Exception $e) {
    										}
    									}
    								}
    							}else{
    								try {
    									if ($SID == ''){
    										$this->Telnyx->sendsms(TELNYX_KEY,$response_phone,$_REQUEST['To'],$response_text_msg);
    									}else{
    										$this->Telnyx->sendsms($SID,$response_phone,$_REQUEST['To'],$response_text_msg);
    									}
    									$length = mb_strlen($response_text_msg,"UTF-8");
    									$gsm = $this->is_gsm($response_text_msg);
    									if(!$gsm){
    										$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
    									} else {
    										$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
    									}
    									app::import('Model', 'User');
    									$this->User = new User();
    									$this->User->id = $someone['User']['id'];
    									if ($this->User->id != '') {
    										$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
    									}
    								}catch(Exception $e) {
    								}
    							}
    						}
    						if($response !=''){
        						$message = $response;
        						$length = mb_strlen($message,"UTF-8");
        						$gsm = $this->is_gsm($message);
        						if(!$gsm){
        							$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
        						} else {
        							$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
        						}
        						app::import('Model', 'User');
        						$this->User = new User();
        						$this->User->id = $someone['User']['id'];
        						if ($this->User->id != '') {
        							$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
        						}
        						if ($SID == ''){
                                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                                }else{
                                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                                }
        						echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        						echo "<Response>";
        						echo "</Response>";
        						exit();
    					    }else{
    					        app::import('Model', 'User');
        						$this->User = new User();
        						$this->User->id = $someone['User']['id'];
        						if ($this->User->id != '') {
        							$this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - CHARGEINCOMINGSMS));
        						}
    					        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        						echo "<Response>";
        						echo "</Response>";
        						exit();
    					    }
    					}
    				}
			    }
			}
		}
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('first', array('conditions' => array('Group.keyword' => $_REQUEST['Text'], array('Group.user_id' => $someone['User']['id']))));
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.question_id >' => 0, 'ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.id' => 'desc')));
        $question_id = $contact['ContactGroup']['question_id'];
        //****DOUBLE OPT-IN CHANGES****// 
        $contactname_arr = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers !=' => 2), 'order' => array('ContactGroup.id' => 'desc')));
        $contactname = '';
        $contact_id = 0;
        if (!empty($contactname_arr)) {
            $contactname = $contactname_arr['Contact']['name'];
            $contact_id = $contactname_arr['Contact']['id'];
        }
        //****DOUBLE OPT-IN CHANGES****// 
        $contact_doubleoptin = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 2), 'order' => array('ContactGroup.id' => 'desc')));
        app::import('Model', 'AppointmentSetting');
        $this->AppointmentSetting = new AppointmentSetting();
        $appointmentsetting = $this->AppointmentSetting->find('first', array('conditions' => array('AppointmentSetting.user_id' => $user_id)));
        $cancelkeyword = $appointmentsetting['AppointmentSetting']['cancel_keyword'];
        $confirmkeyword = $appointmentsetting['AppointmentSetting']['confirm_keyword'];
        $reschedulekeyword = $appointmentsetting['AppointmentSetting']['reschedule_keyword'];
        
        if (strtoupper(trim($confirmkeyword)) == strtoupper($_REQUEST['Text'])) {
            app::import('Model', 'Appointment');
            $this->Appointment = new Appointment();
            $appointment = $this->Appointment->find('first', array('conditions' => array('Appointment.contact_id' => $contact_id, 'Appointment.user_id' => $user_id), 'order' => array('Appointment.id' => 'desc')));
            if (!empty($appointment)) {
                $confirmmessage = $appointmentsetting['AppointmentSetting']['confirm_message'];
                $confirmemailbody = $appointmentsetting['AppointmentSetting']['confirm_email_body'];
                $confirmemailsubject = $appointmentsetting['AppointmentSetting']['confirm_email_subject'];
                $confirmemailfrom = $appointmentsetting['AppointmentSetting']['confirm_email_from'];
                $confirmemailto = $appointmentsetting['AppointmentSetting']['confirm_email_to'];
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$confirmmessage);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$confirmmessage);
                }
                $this->User->id = $someone['User']['id'];
                if ($this->User->id != '') {
                    $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - CHARGEINCOMINGSMS - 1));
                }
                $this->smsmail($someone['User']['id']);
                $message_replace1 = str_replace('%%Name%%', $appointment['Contact']['name'], $confirmemailbody);
                $message_replace2 = str_replace('%%Email%%', $appointment['Contact']['email'], $message_replace1);
                $message_replace3 = str_replace('%%Number%%', $appointment['Contact']['phone_number'], $message_replace2);
                $apptdatetime = date('F j, Y \a\t g:i a', strtotime($appointment['Appointment']['app_date_time']));
                $message_replace4 = str_replace('%%ApptDate%%', $apptdatetime, $message_replace3);
                $sitename = str_replace(' ', '', SITENAME);
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to($confirmemailto);
                $Email->subject($confirmemailsubject);
                $Email->emailFormat('html');
                $Email->send($message_replace4);
                $this->Appointment->id = $appointment['Appointment']['id'];
                if ($this->Appointment->id != '') {
                    $this->Appointment->saveField('appointment_status', 1);
                }
                //*********** Save to activity timeline
                $timeline['ActivityTimeline']['user_id'] = $user_id;
                $timeline['ActivityTimeline']['contact_id'] = $appointment['Contact']['id'];
                $timeline['ActivityTimeline']['activity'] = 17;
                $timeline['ActivityTimeline']['title'] = 'Confirm Appointment';
                $timeline['ActivityTimeline']['description'] = $message_replace4;
                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                $this->ActivityTimeline->save($timeline);
                //*************
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
        } elseif (strtoupper(trim($cancelkeyword)) == strtoupper($_REQUEST['Text'])) {
            app::import('Model', 'Appointment');
            $this->Appointment = new Appointment();
            $appointment = $this->Appointment->find('first', array('conditions' => array('Appointment.contact_id' => $contact_id, 'Appointment.user_id' => $user_id), 'order' => array('Appointment.id' => 'desc')));
            if (!empty($appointment)) {
                $cancelmessage = $appointmentsetting['AppointmentSetting']['cancel_message'];
                $cancelemailbody = $appointmentsetting['AppointmentSetting']['cancel_email_body'];
                $cancelemailsubject = $appointmentsetting['AppointmentSetting']['cancel_email_subject'];
                $cancelemailfrom = $appointmentsetting['AppointmentSetting']['cancel_email_from'];
                $cancelemailto = $appointmentsetting['AppointmentSetting']['cancel_email_to'];
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$cancelmessage);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$cancelmessage);
                }
                $this->User->id = $someone['User']['id'];
                if ($this->User->id != '') {
                    $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - CHARGEINCOMINGSMS - 1));
                }
                $this->smsmail($someone['User']['id']);
                $message_replace1 = str_replace('%%Name%%', $appointment['Contact']['name'], $cancelemailbody);
                $message_replace2 = str_replace('%%Email%%', $appointment['Contact']['email'], $message_replace1);
                $message_replace3 = str_replace('%%Number%%', $appointment['Contact']['phone_number'], $message_replace2);
                $apptdatetime = date('F j, Y \a\t g:i a', strtotime($appointment['Appointment']['app_date_time']));
                $message_replace4 = str_replace('%%ApptDate%%', $apptdatetime, $message_replace3);
                $sitename = str_replace(' ', '', SITENAME);
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to($cancelemailto);
                $Email->subject($cancelemailsubject);
                $Email->emailFormat('html');
                $Email->send($message_replace4);
                $this->Appointment->id = $appointment['Appointment']['id'];
                if ($this->Appointment->id != '') {
                    $this->Appointment->saveField('appointment_status', 2);
                }
                //*********** Save to activity timeline
                $timeline['ActivityTimeline']['user_id'] = $user_id;
                $timeline['ActivityTimeline']['contact_id'] = $appointment['Contact']['id'];
                $timeline['ActivityTimeline']['activity'] = 18;
                $timeline['ActivityTimeline']['title'] = 'Cancel Appointment';
                $timeline['ActivityTimeline']['description'] = $message_replace4;
                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                $this->ActivityTimeline->save($timeline);
                //*************
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
        } elseif (strtoupper(trim($reschedulekeyword)) == strtoupper($_REQUEST['Text'])) {
            app::import('Model', 'Appointment');
            $this->Appointment = new Appointment();
            $appointment = $this->Appointment->find('first', array('conditions' => array('Appointment.contact_id' => $contact_id, 'Appointment.user_id' => $user_id), 'order' => array('Appointment.id' => 'desc')));
            if (!empty($appointment)) {
                $reschedulemessage = $appointmentsetting['AppointmentSetting']['reschedule_message'];
                $rescheduleemailbody = $appointmentsetting['AppointmentSetting']['reschedule_email_body'];
                $rescheduleemailsubject = $appointmentsetting['AppointmentSetting']['reschedule_email_subject'];
                $rescheduleemailfrom = $appointmentsetting['AppointmentSetting']['reschedule_email_from'];
                $rescheduleemailto = $appointmentsetting['AppointmentSetting']['reschedule_email_to'];
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$reschedulemessage);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$reschedulemessage);
                }
                $this->User->id = $someone['User']['id'];
                if ($this->User->id != '') {
                    $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - CHARGEINCOMINGSMS - 1));
                }
                $this->smsmail($someone['User']['id']);
                $message_replace1 = str_replace('%%Name%%', $appointment['Contact']['name'], $rescheduleemailbody);
                $message_replace2 = str_replace('%%Email%%', $appointment['Contact']['email'], $message_replace1);
                $message_replace3 = str_replace('%%Number%%', $appointment['Contact']['phone_number'], $message_replace2);
                $apptdatetime = date('F j, Y \a\t g:i a', strtotime($appointment['Appointment']['app_date_time']));
                $message_replace4 = str_replace('%%ApptDate%%', $apptdatetime, $message_replace3);
                $sitename = str_replace(' ', '', SITENAME);
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to($rescheduleemailto);
                $Email->subject($rescheduleemailsubject);
                $Email->emailFormat('html');
                $Email->send($message_replace4);
                $this->Appointment->id = $appointment['Appointment']['id'];
                if ($this->Appointment->id != '') {
                    $this->Appointment->saveField('appointment_status', 3);
                }
                //*********** Save to activity timeline
                $timeline['ActivityTimeline']['user_id'] = $user_id;
                $timeline['ActivityTimeline']['contact_id'] = $appointment['Contact']['id'];
                $timeline['ActivityTimeline']['activity'] = 19;
                $timeline['ActivityTimeline']['title'] = 'Reschedule Appointment';
                $timeline['ActivityTimeline']['description'] = $message_replace4;
                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                $this->ActivityTimeline->save($timeline);
                //*************
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
        }
        app::import('Model', 'Contest');
        $this->Contest = new Contest();
        //$contestkeywords = $this->Contest->find('first',array('conditions'=>array('Contest.id '=>$contest_id,'Contest.keyword'=>$_REQUEST['Text'])));
        $contestkeywords = $this->Contest->find('first', array('conditions' => array('Contest.user_id' => $user_id, 'Contest.keyword' => $_REQUEST['Text'])));
        $contest_id = $contestkeywords['Contest']['id'];
        app::import('Model', 'Option');
        $this->Option = new Option();
        $answers123 = $this->Option->find('first', array('conditions' => array('Option.question_id' => $question_id, 'Option.optionb' => $_REQUEST['Text'])));
        app::import('Model', 'Smsloyalty');
        $this->Smsloyalty = new Smsloyalty();
        $smsloyalty_arr = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.user_id' => $user_id, 'Smsloyalty.coupancode' => strtoupper($_REQUEST['Text']))));
        $smsloyalty_status = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.user_id' => $user_id, 'Smsloyalty.codestatus' => strtoupper($_REQUEST['Text']))));
        if (!empty($smsloyalty_status)) {
            if ($someone['User']['sms_balance'] > 0) {
                $credits = 0;
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contactgroupid = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.group_id' => $smsloyalty_status['Smsloyalty']['group_id'], 'ContactGroup.user_id' => $user_id)));
                if (!empty($contactgroupid)) {
                    app::import('Model', 'SmsloyaltyUser');
                    $this->SmsloyaltyUser = new SmsloyaltyUser();
                    $loyaltyuser = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_status['Smsloyalty']['id']), 'order' => array('SmsloyaltyUser.created' => 'desc')));
                    if (!empty($loyaltyuser)) {
                        $credits = CHARGEINCOMINGSMS + 1;//2;
                        $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_status['Smsloyalty']['checkstatus']);
                        $msg = str_replace('%%STATUS%%', $loyaltyuser['SmsloyaltyUser']['count_trial'], $message);
                        $statusmsg = str_replace('%%GOAL%%', $smsloyalty_status['Smsloyalty']['reachgoal'], $msg);
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$statusmsg);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$statusmsg);
                        }

                        $this->smsmail($someone['User']['id']);
                        //*********** Save to activity timeline
                        $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                        $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                        $timeline['ActivityTimeline']['activity'] = 6;
                        $timeline['ActivityTimeline']['title'] = 'Check Loyalty Status';
                        $timeline['ActivityTimeline']['description'] = $statusmsg;
                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                        $this->ActivityTimeline->save($timeline);
                        //*************
                    }
                }else{
                    $credits = CHARGEINCOMINGSMS + 1;//2;
                    $message = "You are not eligible to participate since you are not subscribed to our opt-in list. Please text in " . $smsloyalty_status['Group']['keyword'] . " to be added to our opt-in list.";
					if ($SID == ''){
                        $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                    }else{
                        $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                    }
					//$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                    $this->smsmail($someone['User']['id']);
                }
                if ($credits > 0) {
                    $update_user['User']['id'] = $someone['User']['id'];
                    $update_user['User']['sms_balance'] = $someone['User']['sms_balance'] - $credits;
                    $this->User->save($update_user);
                }
            }
        } else if (!empty($smsloyalty_arr)) {
            if ($someone['User']['sms_balance'] > 0) {
                $credits = 0;
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contactgroupid = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.group_id' => $smsloyalty_arr['Smsloyalty']['group_id'], 'ContactGroup.user_id' => $user_id)));
                if (!empty($contactgroupid)) {
                    $current_date = date('Y-m-d');
                    if ($smsloyalty_arr['Smsloyalty']['startdate'] > $current_date) {
                        $credits =  CHARGEINCOMINGSMS + 1;
                        $message = "Loyalty program " . $smsloyalty_arr['Smsloyalty']['program_name'] . " hasn't started yet. It begins on " . date('m/d/Y', strtotime($smsloyalty_arr['Smsloyalty']['startdate'])) . "";
						if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
						//$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        $this->smsmail($someone['User']['id']);
                    } else if ($smsloyalty_arr['Smsloyalty']['enddate'] < $current_date) {
                        $credits = CHARGEINCOMINGSMS + 1;
                        $message = "Loyalty program " . $smsloyalty_arr['Smsloyalty']['program_name'] . " ended on " . date('m/d/Y', strtotime($smsloyalty_arr['Smsloyalty']['enddate'])) . "";
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
                        //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        $this->smsmail($someone['User']['id']);
                    } else {
                        $currentdate = date('Y-m-d');
                        app::import('Model', 'SmsloyaltyUser');
                        $this->SmsloyaltyUser = new SmsloyaltyUser();
                        $loyaltyuser = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 0), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                        if (empty($loyaltyuser)) {
                            $loyaltyuserredeem = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 1, 'SmsloyaltyUser.msg_date' => $currentdate), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                            if (empty($loyaltyuserredeem)) {
                                $loyalty_user['SmsloyaltyUser']['id'] = '';
                                $loyalty_user['SmsloyaltyUser']['unique_key'] = $this->random_generator(10);
                                $loyalty_user['SmsloyaltyUser']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                $loyalty_user['SmsloyaltyUser']['sms_loyalty_id'] = $smsloyalty_arr['Smsloyalty']['id'];
                                $loyalty_user['SmsloyaltyUser']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                $loyalty_user['SmsloyaltyUser']['keyword'] = $_REQUEST['Text'];
                                $loyalty_user['SmsloyaltyUser']['count_trial'] = 1;
                                $loyalty_user['SmsloyaltyUser']['msg_date'] = $currentdate;
                                $loyalty_user['SmsloyaltyUser']['created'] = date('Y-m-d H:i:s');
                                if ($smsloyalty_arr['Smsloyalty']['reachgoal'] == 1) {
                                    $loyalty_user['SmsloyaltyUser']['is_winner'] = 1;
                                    if ($this->SmsloyaltyUser->save($loyalty_user)) {
                                        if ($smsloyalty_arr['Smsloyalty']['type'] == 1) {
                                            $credits = CHARGEINCOMINGSMS + 1;
                                            $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                            $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                            $redeem = "Click link to redeem " . SITE_URL . "/users/redeem/" . $loyalty_user['SmsloyaltyUser']['unique_key'] . "";
                                            $sms = $msg . ' ' . $redeem;
                                            if ($SID == ''){
                                                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                            }else{
                                                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                            }
                                            //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                            $this->smsmail($someone['User']['id']);
                                            //*********** Save to activity timeline
                                            $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                                            $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                            $timeline['ActivityTimeline']['activity'] = 7;
                                            $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                            $timeline['ActivityTimeline']['description'] = $sms;
                                            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                            $this->ActivityTimeline->save($timeline);
                                            //*************
                                        } else if ($smsloyalty_arr['Smsloyalty']['type'] == 2) {
                                            $credits = CHARGEINCOMINGSMS + CHARGECREDITSMMS;
                                            $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                            $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                            $redeem = "Click link to redeem " . SITE_URL . "/users/redeem/" . $loyalty_user['SmsloyaltyUser']['unique_key'] . "";
                                            $sms = $msg . ' ' . $redeem;
											$media = SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'];
											if ($SID == ''){
                                                $this->Telnyx->sendmms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms,$media);
                                            }else{
                                                $this->Telnyx->sendmms($SID,$_REQUEST['From'],$_REQUEST['To'],$sms,$media);
                                            }
											//$this->Telnyx->sendmms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms,$media);
                                            $this->smsmail($someone['User']['id']);
                                            //*********** Save to activity timeline
                                            $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                                            $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                            $timeline['ActivityTimeline']['activity'] = 7;
                                            $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                            $image = '<br/><br/><img width="100px;" height="100px;" src="' . SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'] . '" alt="" title="" />';
                                            $desc = $msg . ' ' . $redeem . ' ' . $image;
                                            $timeline['ActivityTimeline']['description'] = $desc;
                                            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                            $this->ActivityTimeline->save($timeline);
                                            //*************
                                        }
                                    }
                                } else {
                                    $this->SmsloyaltyUser->save($loyalty_user);
                                    $credits = CHARGEINCOMINGSMS + 1;
                                    $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['addpoints']);
                                    $msg = str_replace('%%STATUS%%', 1, $message);
                                    if ($SID == ''){
                                        $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$msg);
                                    }else{
                                        $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$msg);
                                    }
                                    //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$msg);
                                    $this->smsmail($someone['User']['id']);
                                    //*********** Save to activity timeline
                                    $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                                    $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                    $timeline['ActivityTimeline']['activity'] = 5;
                                    $timeline['ActivityTimeline']['title'] = 'Loyalty Program Punch';
                                    $timeline['ActivityTimeline']['description'] = $msg;
                                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                    $this->ActivityTimeline->save($timeline);
                                    //*************
                                }
                            } else {
                                $credits = CHARGEINCOMINGSMS + 1;
                                $message = "You have already redeemed your reward today.";
                                if ($SID == ''){
                                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                                }else{
                                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                                }
                                //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                                $this->smsmail($someone['User']['id']);
                            }

                        } else if ($loyaltyuser['SmsloyaltyUser']['msg_date'] < $currentdate) {
                            $count_trial = $loyaltyuser['SmsloyaltyUser']['count_trial'] + 1;
                            /***08/11/2018*****/
                            if ($count_trial > $smsloyalty_arr['Smsloyalty']['reachgoal'] && $loyaltyuser['SmsloyaltyUser']['is_winner'] == 1){
                                $credits = CHARGEINCOMINGSMS + 1;
                                $message = "You have already reached the goal of " . $smsloyalty_arr['Smsloyalty']['reachgoal'] . " points.";
                                $redeem = "Click link to redeem " . SITE_URL . "/users/redeem/" . $loyaltyuser['SmsloyaltyUser']['unique_key'] . "";
                                $sms = $message . ' ' . $redeem;
                                if ($SID == ''){
                                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                }else{
                                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                }
                                //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                $update_user['User']['id'] = $someone['User']['id'];
                                $update_user['User']['sms_balance'] = $someone['User']['sms_balance'] - $credits;
                                $this->User->save($update_user);
                                $this->smsmail($someone['User']['id']);
                                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                                echo "<Response></Response>";
                                exit;
                            }
                            /******************/
                            $loyalty_user['SmsloyaltyUser']['id'] = $loyaltyuser['SmsloyaltyUser']['id'];
                            $loyalty_user['SmsloyaltyUser']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                            $loyalty_user['SmsloyaltyUser']['sms_loyalty_id'] = $smsloyalty_arr['Smsloyalty']['id'];
                            $loyalty_user['SmsloyaltyUser']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                            $loyalty_user['SmsloyaltyUser']['keyword'] = $_REQUEST['Text'];
                            $loyalty_user['SmsloyaltyUser']['count_trial'] = $count_trial;
                            $loyalty_user['SmsloyaltyUser']['msg_date'] = $currentdate;
                            $loyalty_user['SmsloyaltyUser']['created'] = date('Y-m-d H:i:s');
                            if ($this->SmsloyaltyUser->save($loyalty_user)) {
                                $loyaltyuser_list = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 0), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                                if ($loyaltyuser_list['SmsloyaltyUser']['count_trial'] == $smsloyalty_arr['Smsloyalty']['reachgoal']) {
                                    $loyalty_user_arr['SmsloyaltyUser']['id'] = $loyaltyuser['SmsloyaltyUser']['id'];
                                    $loyalty_user_arr['SmsloyaltyUser']['is_winner'] = 1;
                                    if ($this->SmsloyaltyUser->save($loyalty_user_arr)) {
                                        if ($smsloyalty_arr['Smsloyalty']['type'] == 1) {
                                            $credits = CHARGEINCOMINGSMS + 1;
                                            $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                            $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                            $redeem = "Click link to redeem " . SITE_URL . "/users/redeem/" . $loyaltyuser_list['SmsloyaltyUser']['unique_key'] . "";
                                            $sms = $msg . ' ' . $redeem;
                                            if ($SID == ''){
                                                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                            }else{
                                                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                            }
                                            //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms);
                                            $this->smsmail($someone['User']['id']);
                                            //*********** Save to activity timeline
                                            $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                                            $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                            $timeline['ActivityTimeline']['activity'] = 7;
                                            $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                            $timeline['ActivityTimeline']['description'] = $sms;
                                            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                            $this->ActivityTimeline->save($timeline);
                                            //*************
                                        } else if ($smsloyalty_arr['Smsloyalty']['type'] == 2) {
                                            $credits = CHARGEINCOMINGSMS + CHARGECREDITSMMS;
                                            $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                            $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                            $redeem = "Click link to redeem " . SITE_URL . "/users/redeem/" . $loyaltyuser_list['SmsloyaltyUser']['unique_key'] . "";
                                            $sms = $msg . ' ' . $redeem;
											$media = SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'];
											if ($SID == ''){
                                                $this->Telnyx->sendmms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms,$media);
                                            }else{
                                                $this->Telnyx->sendmms($SID,$_REQUEST['From'],$_REQUEST['To'],$sms,$media);
                                            }
											//$this->Telnyx->sendmms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$sms,$media);
                                            $this->smsmail($someone['User']['id']);
                                            //*********** Save to activity timeline
                                            $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                                            $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                            $timeline['ActivityTimeline']['activity'] = 7;
                                            $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                            $image = '<br/><br/><img width="100px;" height="100px;" src="' . SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'] . '" alt="" title="" />';
                                            $desc = $msg . ' ' . $redeem . ' ' . $image;
                                            $timeline['ActivityTimeline']['description'] = $desc;
                                            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                            $this->ActivityTimeline->save($timeline);
                                            //*************
                                        }
                                    }
                                } else {
                                    $credits = CHARGEINCOMINGSMS + 1;
                                    $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['addpoints']);
                                    $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                    if ($SID == ''){
                                        $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$msg);
                                    }else{
                                        $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$msg);
                                    }
                                    //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$msg);
                                    $this->smsmail($someone['User']['id']);
                                    //*********** Save to activity timeline
                                    $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                                    $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                    $timeline['ActivityTimeline']['activity'] = 5;
                                    $timeline['ActivityTimeline']['title'] = 'Loyalty Program Punch';
                                    $timeline['ActivityTimeline']['description'] = $msg;
                                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                    $this->ActivityTimeline->save($timeline);
                                    //*************
                                }
                            }
                        } else if ($loyaltyuser['SmsloyaltyUser']['is_winner'] == 1) {
                            $credits = CHARGEINCOMINGSMS + 1;
                            $message = "You have already reached the goal of " . $smsloyalty_arr['Smsloyalty']['reachgoal'] . " points. Please redeem your reward.";
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }else{
                                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }
                            //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            $this->smsmail($someone['User']['id']);
                        } else {
                            $credits = CHARGEINCOMINGSMS + 1;
                            $message = "You already punched your card today. Stop in tomorrow for the new punch code.";
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }else{
                                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }
                            //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            $this->smsmail($someone['User']['id']);
                        }
                    }

                } else {
                    $credits = CHARGEINCOMINGSMS + 1;
                    $message = "You are not eligible to participate since you are not subscribed to our opt-in list. Please text in " . $smsloyalty_arr['Group']['keyword'] . " to be added to our opt-in list.";
                    if ($SID == ''){
                        $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                    }else{
                        $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                    }
                    //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                    $this->smsmail($someone['User']['id']);
                }
                if ($credits > 0) {
                    $update_user['User']['id'] = $someone['User']['id'];
                    $update_user['User']['sms_balance'] = $someone['User']['sms_balance'] - $credits;
                    $this->User->save($update_user);
                }
            }
        } else if (strtoupper(trim($_REQUEST['Text'])) == 'START' || strtoupper(trim($_REQUEST['Text'])) == 'UNSTOP') {
            app::import('Model', 'User');
            $this->User = new User();
            $user_id = $someone['User']['id'];
            $sms_balance = $someone['User']['sms_balance'];
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $contactsstart = $this->ContactGroup->find('all', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.un_subscribers' => 1, 'ContactGroup.user_id' => $user_id)));
            if (!empty($contactsstart)) {
                foreach ($contactsstart as $contact) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $contact_id = $contact['Contact']['id'];
                    $id = $contact['ContactGroup']['id'];
                    $this->request->data['ContactGroup']['id'] = $id;
                    $this->request->data['ContactGroup']['contact_id'] = $contact_id;
                    $this->request->data['ContactGroup']['un_subscribers'] = 0;
                    $this->request->data['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
                    if ($this->ContactGroup->save($this->request->data)) {
                        $contact_arr['Contact']['id'] = $contact_id;
                        $contact_arr['Contact']['un_subscribers'] = 0;
                        if ($this->Contact->save($contact_arr)) {
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $this->request->data['Group']['id'] = $contact['Group']['id'];
                            $this->request->data['Group']['totalsubscriber'] = $contact['Group']['totalsubscriber'] + 1;
                            $this->Group->save($this->request->data);
                        }
                    }
                }
                app::import('Model', 'User');
                $this->User = new User();
                $this->request->data['User']['id'] = $user_id;
                $this->request->data['User']['sms_balance'] = $sms_balance - CHARGEINCOMINGSMS - 1;
                $this->User->save($this->request->data);
                /*$message = 'You have successfully been re-subscribed. Text STOP to cancel. Msg&Data Rates May Apply.';
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                }
                $this->smsmail($someone['User']['id']);*/
                //*********** Save to activity timeline
                $timeline['ActivityTimeline']['user_id'] = $user_id;
                $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                $timeline['ActivityTimeline']['activity'] = 15;
                $timeline['ActivityTimeline']['title'] = 'Contact Resubscribed';
                $timeline['ActivityTimeline']['description'] = 'Contact has resubscribed and can now continue to receive messages.';
                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                $this->ActivityTimeline->save($timeline);
            }
        }else if (strtoupper(trim($_REQUEST['Text'])) == 'HELP') {
            app::import('Model', 'User');
            $this->User = new User();
            $user_id = $someone['User']['id'];
            $sms_balance = $someone['User']['sms_balance'];
            $this->request->data['User']['id'] = $user_id;
            $this->request->data['User']['sms_balance'] = $sms_balance - CHARGEINCOMINGSMS - 1;
            $this->User->save($this->request->data);
            $companyname = $someone['User']['company_name'];
            if (!empty($companyname)) {
                $message = "You have signed up to receive promotional messages from " . $companyname . ". Text STOP to cancel. Msg&Data Rates May Apply.";
            } else {
                $message = "Text STOP to cancel. Msg&Data Rates May Apply.";
            }
            if ($SID == ''){
                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
            }else{
                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
            }
            $this->smsmail($someone['User']['id']);

            //*********** Save to activity timeline
            $timeline['ActivityTimeline']['user_id'] = $user_id;
            $timeline['ActivityTimeline']['contact_id'] = $contact_id;
            $timeline['ActivityTimeline']['activity'] = 16;
            $timeline['ActivityTimeline']['title'] = 'Help';
            $timeline['ActivityTimeline']['description'] = $message;
            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
            $this->ActivityTimeline->save($timeline);
            //*************
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response></Response>";
            exit;
        } else if (strtoupper(trim($_REQUEST['Text'])) == 'STOP') {
            app::import('Model', 'User');
            $this->User = new User();
            $user_id = $someone['User']['id'];
            $sms_balance = $someone['User']['sms_balance'];
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $contacts = $this->ContactGroup->find('all', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.un_subscribers' => array(0,2), 'ContactGroup.user_id' => $user_id)));
            if (!empty($contacts)) {
                foreach ($contacts as $contact) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $contact_id = $contact['Contact']['id'];
                    $id = $contact['ContactGroup']['id'];
                    $this->request->data['ContactGroup']['id'] = $id;
                    $this->request->data['ContactGroup']['contact_id'] = $contact_id;
                    $this->request->data['ContactGroup']['un_subscribers'] = 1;
                    $this->request->data['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
                    if ($this->ContactGroup->save($this->request->data)) {
                        $contact_arr['Contact']['id'] = $contact_id;
                        $contact_arr['Contact']['un_subscribers'] = 1;
                        if ($this->Contact->save($contact_arr) && $contact['ContactGroup']['un_subscribers']==0) {
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $this->request->data['Group']['id'] = $contact['Group']['id'];
                            $this->request->data['Group']['totalsubscriber'] = $contact['Group']['totalsubscriber'] - 1;
                            $this->Group->save($this->request->data);
                        }
                    }
                }
                app::import('Model', 'User');
                $this->User = new User();
                $this->request->data['User']['id'] = $user_id;
                $this->request->data['User']['sms_balance'] = $sms_balance - CHARGEINCOMINGSMS;
                $this->User->save($this->request->data);
                //$message = 'You have successfully been unsubscribed. Reply START to get added back to our list. Msg&Data Rates May Apply.';
                //if ($SID == ''){
                //    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                //}else{
                //    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                //}
                //$this->smsmail($someone['User']['id']);
                //*********** Save to activity timeline
                $timeline['ActivityTimeline']['user_id'] = $user_id;
                $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                $timeline['ActivityTimeline']['activity'] = 14;
                $timeline['ActivityTimeline']['title'] = 'Contact Unsubscribed';
                $timeline['ActivityTimeline']['description'] = 'Contact has unsubscribed from your list and can no longer receive messages.';
                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                $this->ActivityTimeline->save($timeline);
                //*************
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $number = $this->Contact->find('first', array('conditions' => array('Contact.id' => $contact_id), 'fields' => array('Contact.phone_number')));
                $beforecontactnumber = $number['Contact']['phone_number'];
                $aftercontactnumber = substr_replace($beforecontactnumber, '****', -4);
                app::import('Model', 'Log');
                $this->Log = new Log();
                $this->Log->updateAll(array('Log.phone_number' => "'$aftercontactnumber'"), array('Log.user_id'=>$user_id, 'Log.phone_number' => $beforecontactnumber));
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
        //****DOUBLE OPT-IN CHANGES****//    
        }  else if (strtoupper(trim($_REQUEST['Text'])) == 'Y' && $contact_doubleoptin['ContactGroup']['un_subscribers']==2) {
            app::import('Model', 'Group');
            $this->Group = new Group();
            $group = $this->Group->find('first', array('conditions' => array('Group.keyword' => $contact_doubleoptin['ContactGroup']['group_subscribers'], array('Group.user_id' => $someone['User']['id']))));
            $sender_number = $_REQUEST['To'];
            $group_id = $group['Group']['id'];
            $group_name = $group['Group']['group_name'];
            $totalsubscriber = $group['Group']['totalsubscriber'];
            $sms_type = $group['Group']['sms_type'];
            $system_message = $group['Group']['system_message'];
            $auto_message = $group['Group']['auto_message'];
            $image_url = $group['Group']['image_url'];
            $group_type = $group['Group']['group_type'];
            $user_id = $someone['User']['id'];
            $this->request->data['ContactGroup']['id'] = $contact_doubleoptin['ContactGroup']['id'];
            $this->request->data['ContactGroup']['un_subscribers'] = 0;
            $this->request->data['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
            $this->ContactGroup->save($this->request->data);
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $contact_arr['Contact']['id'] = $contact_doubleoptin['ContactGroup']['contact_id'];
            $contact_arr['Contact']['un_subscribers'] = 0;
            $this->Contact->save($contact_arr);
            if ($someone['User']['email_alert_options'] == 0) {
                if ($someone['User']['email_alerts'] == 1) {
                    $username = $someone['User']['username'];
                    $email = $someone['User']['email'];
                    $date = date('Y-m-d H:i:s', time());
                    $subject = "New Subscriber to " . $group_name;
                    $sitename = str_replace(' ', '', SITENAME);
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($email);
                    $Email->subject($subject);
                    $Email->template('new_subscriber_template');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('username' => $username));
                    $Email->viewVars(array('phoneno' => $phone));
                    $Email->viewVars(array('groupname' => $group_name));
                    $Email->viewVars(array('keyword' => $keyword));
                    $Email->viewVars(array('datetime' => $date));
                    $Email->send();
                }
            }
            $this->request->data['Group']['id'] = $group_id;
            $this->request->data['Group']['totalsubscriber'] = $totalsubscriber + 1;
            $this->Group->save($this->request->data);
            if ($group_type == 2) {
                $address = $group['Group']['property_address'];
                $price = $group['Group']['property_price'];
                $bed = $group['Group']['property_bed'];
                $bath = $group['Group']['property_bath'];
                $description = $group['Group']['property_description'];
                $url = $group['Group']['property_url'];
                $message = $address . "\n" . $price . "\nBed: " . $bed . "\nBath: " . $bath . "\n" . $description . "\n" . $url . "\n";
                $message = $message . $system_message . ' ' . $auto_message;
            } elseif ($group_type == 3) {
                $year = $group['Group']['vehicle_year'];
                $make = $group['Group']['vehicle_make'];
                $model = $group['Group']['vehicle_model'];
                $mileage = $group['Group']['vehicle_mileage'];
                $price = $group['Group']['vehicle_price'];
                $description = $group['Group']['vehicle_description'];
                $url = $group['Group']['vehicle_url'];
                $message = $year . ' ' . $make . ' ' . $model . "\n" . $mileage . "\n" . $price . "\n" . $description . "\n" . $url . "\n";
                $message = $message . $system_message . ' ' . $auto_message;
            } else {
                $message = $system_message . ' ' . $auto_message;
            }
            $current_datetime = date("n/d/Y");
            $message = str_replace('%%CURRENTDATE%%', $current_datetime, $message);
            //*********** Save to activity timeline
            $timeline['ActivityTimeline']['user_id'] = $user_id;
            $timeline['ActivityTimeline']['contact_id'] = $contact_doubleoptin['ContactGroup']['contact_id'];
            $timeline['ActivityTimeline']['activity'] = 1;
            if($contact_doubleoptin['ContactGroup']['subscribed_by_sms']==1){
                $timeline['ActivityTimeline']['title'] = 'Contact Subscribed via SMS';
            }elseif($contact_doubleoptin['ContactGroup']['subscribed_by_sms']==2){
                $timeline['ActivityTimeline']['title'] = 'Contact Subscribed via Web Widget';
            }
            $timeline['ActivityTimeline']['description'] = $message;
            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
            $this->ActivityTimeline->save($timeline);
            //*************
            if ($sms_type == 1) {
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$message);
                }else{
                    $this->Telnyx->sendsms($SID,$phone,$sender_number,$message);
                }
				//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$message);
				sleep(1);
                $this->Immediatelyresponder($user_id, $group_id, $phone, $sender_number);
            } else if ($sms_type == 2) {
                $message_arr = explode(',', $image_url);
                if ($SID == ''){
                    $this->Telnyx->sendmms(TELNYX_KEY,$phone,$sender_number,$message,$message_arr);
                }else{
                    $this->Telnyx->sendmms($SID,$phone,$sender_number,$message,$message_arr);
                }
				//$this->Telnyx->sendmms(TELNYX_KEY,$phone,$sender_number,$message,$message_arr);
				sleep(1);
                $this->Immediatelyresponder($user_id, $group_id, $phone, $sender_number);
            }
            $curcredits = $someone['User']['sms_balance'];
            //$length = strlen(utf8_decode(substr($message, 0, 1600)));
            $length = mb_strlen($message,"UTF-8");
			$gsm = $this->is_gsm($message);
            if ($sms_type == 1) {
                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                    $credits = ceil($length / 70) + CHARGEINCOMINGSMS;
                } else {
                    $credits = ceil($length / 160) + CHARGEINCOMINGSMS;
                }
            } else {
                $credits = CHARGEINCOMINGSMS + CHARGECREDITSMMS;
            }
            $this->request->data['User']['sms_balance'] = $curcredits - $credits;
            $this->request->data['User']['id'] = $user_id;
            $this->User->save($this->request->data);
            $this->smsmail($someone['User']['id']);
            if ($contact_doubleoptin['ContactGroup']['subscribed_by_sms']==1){
                if ($someone['User']['capture_email_name'] == 0) {
                    $capture_email_name = NAME_CAPTURE_MSG;
                    if ($SID == ''){
                        $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$capture_email_name);
                    }else{
                        $this->Telnyx->sendsms($SID,$phone,$sender_number,$capture_email_name);
                    }
					//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$capture_email_name);
                    //sleep(2);
                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    //$length = strlen(utf8_decode(substr($capture_email_name, 0, 1600)));
                    $length = mb_strlen($capture_email_name,"UTF-8");
			        $gsm = $this->is_gsm($capture_email_name);
                    if(!$gsm){//if (strlen($capture_email_name) != strlen(utf8_decode($capture_email_name))) {
                        $credits = ceil($length / 70);
                    } else {
                        $credits = ceil($length / 160);
                    }
                    if (!empty($someone_users)) {
                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                        $user_credit['User']['id'] = $user_id;
                        $this->User->save($user_credit);
                    }
                }
                if ($group['Group']['bithday_enable'] == 1) {
                    if ($someone['User']['birthday_wishes'] == 0) {
                        $birthday_wishes = BIRTHDAY_MSG;
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$birthday_wishes);
                        }else{
                            $this->Telnyx->sendsms($SID,$phone,$sender_number,$birthday_wishes);
                        }
                        //$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$birthday_wishes);
                        //sleep(2);
                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                        //$length = strlen(utf8_decode(substr($birthday_wishes, 0, 1600)));
                        $length = mb_strlen($birthday_wishes,"UTF-8");
			            $gsm = $this->is_gsm($birthday_wishes);
                        if(!$gsm){//if (strlen($birthday_wishes) != strlen(utf8_decode($birthday_wishes))) {
                            $credits = ceil($length / 70);
                        } else {
                            $credits = ceil($length / 160);
                        }
                        if (!empty($someone_users)) {
                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                            $user_credit['User']['id'] = $user_id;
                            $this->User->save($user_credit);
                        }
                    }
                }
            }
            if ($group['Group']['notify_signup'] == 1) {
                $mobile = $group['Group']['mobile_number_input'];
                $groupname = $group['Group']['group_name'];
                $message = "New Subscriber Alert: " . $phone . " has joined group " . $groupname;
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$mobile,$sender_number,$message);
                }else{
                    $this->Telnyx->sendsms($SID,$mobile,$sender_number,$message);
                }
				//$this->Telnyx->sendsms(TELNYX_KEY,$mobile,$sender_number,$message);
                $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                if (!empty($someone_users)) {
                    $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                    $user_credit['User']['id'] = $user_id;
                    $this->User->save($user_credit);
                }
            }
        } else if (!empty($group)) {
            $keyword = $_REQUEST['Text'];
            $sender_number = $_REQUEST['To'];
            $group_id = $group['Group']['id'];
            $group_name = $group['Group']['group_name'];
            $totalsubscriber = $group['Group']['totalsubscriber'];
            $sms_type = $group['Group']['sms_type'];
            $system_message = $group['Group']['system_message'];
            $auto_message = $group['Group']['auto_message'];
            $image_url = $group['Group']['image_url'];
            $group_type = $group['Group']['group_type'];
            //****DOUBLE OPT-IN CHANGES****//
            $double_optin = $group['Group']['double_optin'];
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $contact = $this->Contact->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'Contact.user_id' => $user_id)));
            if ($someone['User']['sms_balance'] > 0) {
                if (empty($contact)) {
                    if (NUMVERIFY != '') {
                        $numbervalidation = $this->validateNumber($phone);
                        $errorcode = $numbervalidation['error']['code'];
                        if ($errorcode == '') {
                            $this->request->data['Contact']['carrier'] = $numbervalidation['carrier'];
                            $this->request->data['Contact']['location'] = $numbervalidation['location'];
                            $this->request->data['Contact']['phone_country'] = $numbervalidation['country_name'];
                            $this->request->data['Contact']['line_type'] = $numbervalidation['line_type'];
                        }
                    }else{

                        //if ($SID == ''){
                        //    $carrier = $this->Ytel->lookupcarrier(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone);
                        //}else{
                        //    $carrier = $this->Ytel->lookupcarrier($SID,$AUTHTOKEN,$phone);
                        //}
                        
                        //if($carrier->Message360->ResponseStatus == 1){
                            $this->request->data['Contact']['carrier'] = $jsonObject->data->payload->from->carrier;
                        //    $this->request->data['Contact']['location'] = $carrier->Message360->Carrier->City;
                        //}
                        
                    }
                    //****DOUBLE OPT-IN CHANGES****//
                    if($double_optin==1){
                       $this->request->data['Contact']['un_subscribers'] = 2; 
                    }
                    $this->request->data['Contact']['phone_number'] = $phone;
                    $this->request->data['Contact']['user_id'] = $user_id;
                    $this->request->data['Contact']['created'] = date('Y-m-d H:i:s', time());
                    $this->request->data['Contact']['color'] = $this->choosecolor();
                    $this->Contact->save($this->request->data);
                    $contact_id = $this->Contact->id;
                } else {
                    $contact_id = $contact['Contact']['id'];
                }
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contactgroupid = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.group_id' => $group_id, 'ContactGroup.user_id' => $user_id)));
                if (!empty($contactgroupid)) {
                    if ($contactgroupid['ContactGroup']['un_subscribers'] == 1) {
                        $curcredits = $someone['User']['sms_balance'];
                        $this->request->data['User']['sms_balance']=$curcredits-1;
                        $this->request->data['User']['id'] = $user_id;
                        $this->User->save($this->request->data);
                        $this->smsmail($someone['User']['id']);
                        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                        echo "<Response></Response>";
                        exit;
                    } elseif ($contactgroupid['ContactGroup']['un_subscribers'] == 0) {
                        if ($group_type == 0) {
                            $message = $system_message . ' ' . $auto_message;
                            $current_datetime = date("n/d/Y");
                            $message = str_replace('%%CURRENTDATE%%', $current_datetime, $message);
                            //*********** Save to activity timeline
                            $timeline['ActivityTimeline']['user_id'] = $user_id;
                            $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                            $timeline['ActivityTimeline']['activity'] = 9;
                            $timeline['ActivityTimeline']['title'] = 'Coupon Code Group';
                            $timeline['ActivityTimeline']['description'] = $message;
                            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                            $this->ActivityTimeline->save($timeline);
                            //*************
                            if ($sms_type == 1) {
                                if ($SID == ''){
                                    $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$message);
                                }else{
                                    $this->Telnyx->sendsms($SID,$phone,$sender_number,$message);
                                }
								//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$message);
                                sleep(1);
                                $this->Immediatelyresponder($user_id, $group_id, $phone, $sender_number);
                            } else if ($sms_type == 2) {
                                $message_arr = explode(',', $image_url);
                                if ($SID == ''){
                                    $this->Telnyx->sendmms(TELNYX_KEY,$phone,$sender_number,$message,$message_arr);
                                }else{
                                    $this->Telnyx->sendmms($SID,$phone,$sender_number,$message,$message_arr);
                                }
								//$this->Telnyx->sendmms(TELNYX_KEY,$phone,$sender_number,$message,$message_arr);
                                sleep(1);
                                $this->Immediatelyresponder($user_id, $group_id, $phone, $sender_number);
                            }
                            $curcredits = $someone['User']['sms_balance'];
                            //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                            $length = mb_strlen($message,"UTF-8");
			                $gsm = $this->is_gsm($message);
                            if ($sms_type == 1) {
                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                    $credits = ceil($length / 70) + CHARGEINCOMINGSMS;
                                } else {
                                    $credits = ceil($length / 160) + CHARGEINCOMINGSMS;
                                }
                            } else {
                                $credits = CHARGEINCOMINGSMS + CHARGECREDITSMMS;
                            }
                            ////$this->request->data['User']['sms_balance']=$credits-2;
                            $this->request->data['User']['sms_balance'] = $curcredits - $credits;
                            $this->request->data['User']['id'] = $user_id;
                            $this->User->save($this->request->data);
                            $this->smsmail($someone['User']['id']);
                            $name = $contactgroupid['Contact']['name'];
                            $email = $contactgroupid['Contact']['email'];
                            $bday = $contactgroupid['Contact']['birthday'];
                            if ($someone['User']['capture_email_name'] == 0 && $name == '') {
                                $capture_email_name = NAME_CAPTURE_MSG;
                                if ($SID == ''){
                                    $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$capture_email_name);
                                }else{
                                    $this->Telnyx->sendsms($SID,$phone,$sender_number,$capture_email_name);
                                }
								//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$capture_email_name);
                                //sleep(2);
                                $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                //$length = strlen(utf8_decode(substr($capture_email_name, 0, 1600)));
                                $length = mb_strlen($capture_email_name,"UTF-8");
			                    $gsm = $this->is_gsm($capture_email_name);
                                if(!$gsm){//if (strlen($capture_email_name) != strlen(utf8_decode($capture_email_name))) {
                                    $credits = ceil($length / 70);
                                } else {
                                    $credits = ceil($length / 160);
                                }
                                if (!empty($someone_users)) {
                                    $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                                    $user_credit['User']['id'] = $user_id;
                                    $this->User->save($user_credit);
                                }
                            }
                            if ($group['Group']['bithday_enable'] == 1 && $bday == '0000-00-00') {
                                if ($someone['User']['birthday_wishes'] == 0) {
                                    $birthday_wishes = BIRTHDAY_MSG;
                                    if ($SID == ''){
                                        $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$birthday_wishes);
                                    }else{
                                        $this->Telnyx->sendsms($SID,$phone,$sender_number,$birthday_wishes);
                                    }
									//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$birthday_wishes);
                                    //sleep(2);
                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    //$length = strlen(utf8_decode(substr($birthday_wishes, 0, 1600)));
                                    $length = mb_strlen($birthday_wishes,"UTF-8");
			                        $gsm = $this->is_gsm($birthday_wishes);
                                    if(!$gsm){//if (strlen($birthday_wishes) != strlen(utf8_decode($birthday_wishes))) {
                                        $credits = ceil($length / 70);
                                    } else {
                                        $credits = ceil($length / 160);
                                    }
                                    if (!empty($someone_users)) {
                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                                        $user_credit['User']['id'] = $user_id;
                                        $this->User->save($user_credit);
                                    }
                                }
                            }
                        } else {
                            $message = $group['Group']['ifmember_message'];
                            //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                            $length = mb_strlen($message,"UTF-8");
			                $gsm = $this->is_gsm($message);
                            if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                $credits = ceil($length / 70) + CHARGEINCOMINGSMS;
                            } else {
                                $credits = ceil($length / 160) + CHARGEINCOMINGSMS;
                            }
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }else{
                                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                            }
							//$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                            $user_credit['User']['sms_balance'] = $someone['User']['sms_balance'] - $credits;
                            $user_credit['User']['id'] = $user_id;
                            $this->User->save($user_credit);
                            $this->smsmail($user_id);
                            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                            echo "<Response></Response>";
                            exit;
                        }
                    //****DOUBLE OPT-IN CHANGES****//     
                    } elseif ($contactgroupid['ContactGroup']['un_subscribers'] == 2) {
						$message = "Reply Y to confirm that you want to receive SMS messages from ". $someone['User']['company_name'];
						//$length = strlen(utf8_decode(substr($message, 0, 1600)));
						$length = mb_strlen($message,"UTF-8");
			            $gsm = $this->is_gsm($message);
						if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
							$credits = ceil($length / 70) + CHARGEINCOMINGSMS;
						} else {
							$credits = ceil($length / 160) + CHARGEINCOMINGSMS;
						}
						if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
						//$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
						$user_credit['User']['sms_balance'] = $someone['User']['sms_balance'] - $credits;
						$user_credit['User']['id'] = $user_id;
						$this->User->save($user_credit);
						$this->smsmail($user_id);
						echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                        echo "<Response></Response>";
						exit;
					}
                } else {
                    if (!empty($contact) && NUMVERIFY != '') {
                        $numbervalidation = $this->validateNumber($phone);
                        $errorcode = $numbervalidation['error']['code'];
                        if ($errorcode == '') {
                            $contactarr['carrier'] = $numbervalidation['carrier'];
                            $contactarr['location'] = $numbervalidation['location'];
                            $contactarr['phone_country'] = $numbervalidation['country_name'];
                            $contactarr['line_type'] = $numbervalidation['line_type'];
                        }
                        $contactarr['id'] = $contact_id;
                        $this->Contact->save($contactarr);
                    }elseif (!empty($contact) && NUMVERIFY == '') {
                        
                        //if ($SID == ''){
                        //    $carrier = $this->Ytel->lookupcarrier(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone);
                        //}else{
                        //    $carrier = $this->Ytel->lookupcarrier($SID,$AUTHTOKEN,$phone);
                        //}
                        
                        //if($carrier->Message360->ResponseStatus == 1){
                            $contactarr['carrier'] = $jsonObject->data->payload->from->carrier;
                        //    $contactarr['location'] = $carrier->Message360->Carrier->City;
                        //}
                        
                        $contactarr['id'] = $contact_id;
                        $this->Contact->save($contactarr);
                        
                    }
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $this->request->data['ContactGroup']['user_id'] = $user_id;
                    $this->request->data['ContactGroup']['contact_id'] = $contact_id;
                    $this->request->data['ContactGroup']['group_id'] = $group_id;
                    $this->request->data['ContactGroup']['group_subscribers'] = $keyword;
                    $this->request->data['ContactGroup']['subscribed_by_sms'] = 1;
                    $this->request->data['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
                    //****DOUBLE OPT-IN CHANGES****//
                    if($double_optin==1){
                       $this->request->data['ContactGroup']['un_subscribers'] = 2; 
                    }
                    $this->ContactGroup->save($this->request->data);
                    //****DOUBLE OPT-IN CHANGES****//
                    if($double_optin==0){
                        if ($someone['User']['email_alert_options'] == 0) {
                            if ($someone['User']['email_alerts'] == 1) {
                                $username = $someone['User']['username'];
                                $email = $someone['User']['email'];
                                $date = date('Y-m-d H:i:s', time());
                                $subject = "New Subscriber to " . $group_name;
                                $sitename = str_replace(' ', '', SITENAME);
                                $Email = new CakeEmail();
                                if(EMAILSMTP==1){
                                    $Email->config('smtp');
                                }
                                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                                $Email->to($email);
                                $Email->subject($subject);
                                $Email->template('new_subscriber_template');
                                $Email->emailFormat('html');
                                $Email->viewVars(array('username' => $username));
                                $Email->viewVars(array('phoneno' => $phone));
                                $Email->viewVars(array('groupname' => $group_name));
                                $Email->viewVars(array('keyword' => $keyword));
                                $Email->viewVars(array('datetime' => $date));
                                $Email->send();
                            }
                        }
                        app::import('Model', 'Group');
                        $this->Group = new Group();
                        $this->request->data['Group']['id'] = $group_id;
                        $this->request->data['Group']['totalsubscriber'] = $totalsubscriber + 1;
                        $this->Group->save($this->request->data);
                        if ($group_type == 2) {
                            $address = $group['Group']['property_address'];
                            $price = $group['Group']['property_price'];
                            $bed = $group['Group']['property_bed'];
                            $bath = $group['Group']['property_bath'];
                            $description = $group['Group']['property_description'];
                            $url = $group['Group']['property_url'];
                            $message = $address . "\n" . $price . "\nBed: " . $bed . "\nBath: " . $bath . "\n" . $description . "\n" . $url . "\n";
                            $message = $message . $system_message . ' ' . $auto_message;
                        } elseif ($group_type == 3) {
                            $year = $group['Group']['vehicle_year'];
                            $make = $group['Group']['vehicle_make'];
                            $model = $group['Group']['vehicle_model'];
                            $mileage = $group['Group']['vehicle_mileage'];
                            $price = $group['Group']['vehicle_price'];
                            $description = $group['Group']['vehicle_description'];
                            $url = $group['Group']['vehicle_url'];
                            $message = $year . ' ' . $make . ' ' . $model . "\n" . $mileage . "\n" . $price . "\n" . $description . "\n" . $url . "\n";
                            $message = $message . $system_message . ' ' . $auto_message;
                        } else {
                            $message = $system_message . ' ' . $auto_message;
                        }
                        $current_datetime = date("n/d/Y");
                        $message = str_replace('%%CURRENTDATE%%', $current_datetime, $message);
                        //*********** Save to activity timeline
                        $timeline['ActivityTimeline']['user_id'] = $user_id;
                        $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                        $timeline['ActivityTimeline']['activity'] = 1;
                        $timeline['ActivityTimeline']['title'] = 'Contact Subscribed via SMS';
                        $timeline['ActivityTimeline']['description'] = $message;
                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                        $this->ActivityTimeline->save($timeline);
                        //*************
                        if ($sms_type == 1) {
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$message);
                            }else{
                                $this->Telnyx->sendsms($SID,$phone,$sender_number,$message);
                            }
							//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$message);
							sleep(1);
                            $this->Immediatelyresponder($user_id, $group_id, $phone, $sender_number);
                        } else if ($sms_type == 2) {
                            $message_arr = explode(',', $image_url);
                            if ($SID == ''){
                                $this->Telnyx->sendmms(TELNYX_KEY,$phone,$sender_number,$message,$message_arr);
                            }else{
                                $this->Telnyx->sendmms($SID,$phone,$sender_number,$message,$message_arr);
                            }
                            //$this->Telnyx->sendmms(TELNYX_KEY,$phone,$sender_number,$message,$message_arr);
                            sleep(1);
                            $this->Immediatelyresponder($user_id, $group_id, $phone, $sender_number);
                        }
                        $curcredits = $someone['User']['sms_balance'];
                        //$length = strlen(utf8_decode(substr($message, 0, 160)));
                        $length = mb_strlen($message,"UTF-8");
			            $gsm = $this->is_gsm($message);
                        if ($sms_type == 1) {
                            if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                $credits = ceil($length / 70) + CHARGEINCOMINGSMS;
                            } else {
                                $credits = ceil($length / 160) + CHARGEINCOMINGSMS;
                            }
                        } else {
                            $credits = CHARGEINCOMINGSMS + CHARGECREDITSMMS;
                        }
                        
                        $this->request->data['User']['sms_balance'] = $curcredits - $credits;
                        $this->request->data['User']['id'] = $user_id;
                        $this->User->save($this->request->data);
                        $this->smsmail($someone['User']['id']);
                        if ($someone['User']['capture_email_name'] == 0) {
                            $capture_email_name = NAME_CAPTURE_MSG;
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$capture_email_name);
                            }else{
                                $this->Telnyx->sendsms($SID,$phone,$sender_number,$capture_email_name);
                            }
							//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$capture_email_name);
                            //sleep(2);
                            $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            //$length = strlen(utf8_decode(substr($capture_email_name, 0, 1600)));
                            $length = mb_strlen($capture_email_name,"UTF-8");
			                $gsm = $this->is_gsm($capture_email_name);
                            if(!$gsm){//if (strlen($capture_email_name) != strlen(utf8_decode($capture_email_name))) {
                                $credits = ceil($length / 70);
                            } else {
                                $credits = ceil($length / 160);
                            }
                            if (!empty($someone_users)) {
                                $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                                $user_credit['User']['id'] = $user_id;
                                $this->User->save($user_credit);
                            }
                        }
                        if ($group['Group']['bithday_enable'] == 1) {
                            if ($someone['User']['birthday_wishes'] == 0) {
                                $birthday_wishes = BIRTHDAY_MSG;
                                if ($SID == ''){
                                    $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$birthday_wishes);
                                }else{
                                    $this->Telnyx->sendsms($SID,$phone,$sender_number,$birthday_wishes);
                                }
                                //$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$birthday_wishes);
                                //sleep(2);
                                $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                //$length = strlen(utf8_decode(substr($birthday_wishes, 0, 1600)));
                                $length = mb_strlen($birthday_wishes,"UTF-8");
			                    $gsm = $this->is_gsm($birthday_wishes);
                                if(!$gsm){//if (strlen($birthday_wishes) != strlen(utf8_decode($birthday_wishes))) {
                                    $credits = ceil($length / 70);
                                } else {
                                    $credits = ceil($length / 160);
                                }
                                if (!empty($someone_users)) {
                                    $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                                    $user_credit['User']['id'] = $user_id;
                                    $this->User->save($user_credit);
                                }
                            }
                        }
                        if ($group['Group']['notify_signup'] == 1) {
                            $mobile = $group['Group']['mobile_number_input'];
                            $groupname = $group['Group']['group_name'];
                            $message = "New Subscriber Alert: " . $phone . " has joined group " . $groupname;
                            if ($SID == ''){
                                $this->Telnyx->sendsms(TELNYX_KEY,$mobile,$sender_number,$message);
                            }else{
                                $this->Telnyx->sendsms($SID,$mobile,$sender_number,$message);
                            }
							//$this->Telnyx->sendsms(TELNYX_KEY,$mobile,$sender_number,$message);
                            $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            if (!empty($someone_users)) {
                                $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                $user_credit['User']['id'] = $user_id;
                                $this->User->save($user_credit);
                            }
                        }
                    }else{
                        //****DOUBLE OPT-IN CHANGES****//
                        $double_optin_msg = "Reply Y to confirm that you want to receive SMS messages from ". $someone['User']['company_name'];
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$double_optin_msg);
                        }else{
                            $this->Telnyx->sendsms($SID,$phone,$sender_number,$double_optin_msg);
                        }
						//$this->Telnyx->sendsms(TELNYX_KEY,$phone,$sender_number,$double_optin_msg);
                        //sleep(2);
                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                        //$length = strlen(utf8_decode(substr($double_optin_msg, 0, 1600)));
                        $length = mb_strlen($double_optin_msg,"UTF-8");
			            $gsm = $this->is_gsm($double_optin_msg);
                        if(!$gsm){//if (strlen($double_optin_msg) != strlen(utf8_decode($double_optin_msg))) {
                            $credits = ceil($length / 70) + CHARGEINCOMINGSMS;
                        } else {
                            $credits = ceil($length / 160) + CHARGEINCOMINGSMS;
                        }
                        if (!empty($someone_users)) {
                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                            $user_credit['User']['id'] = $user_id;
                            $this->User->save($user_credit);
                        }
                    }
                }
            }
        } elseif (!empty($answers123)) {
            $answers_id = $answers123['Option']['id'];
            $autorsponder_message = $answers123['Option']['autorsponder_message'];
            if (!empty($contact)) {
                $contact_id = $contact['Contact']['id'];
                app::import('Model', 'AnswerSubscriber');
                $this->AnswerSubscriber = new AnswerSubscriber();
                $ansersubs = $this->AnswerSubscriber->find('first', array('conditions' => array('AnswerSubscriber.contact_id' => $contact_id, 'AnswerSubscriber.question_id' => $question_id)));
                //pr($ansersubs);
                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                if ($usersbalance['User']['sms_balance'] > 0) {
                    if (empty($ansersubs)) {
                        if ($answers_id != '') {
                            $this->Option->id = $answers_id;
                            $this->request->data['Option']['count'] = $answers123['Option']['count'] + 1;
                            $this->Option->save($this->request->data);
                        }
                        $this->request->data['AnswerSubscriber']['answer_id'] = $answers_id;
                        $this->request->data['AnswerSubscriber']['question_id'] = $question_id;
                        $this->request->data['AnswerSubscriber']['contact_id'] = $contact_id;
                        $this->request->data['AnswerSubscriber']['created'] = date('Y-m-d H:i:s', time());
                        $this->AnswerSubscriber->save($this->request->data);
                        app::import('Model', 'User');
                        $this->User = new User();
                        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                        $credits = $users['User']['sms_balance'];
                        $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
                        $this->request->data['User']['id'] = $user_id;
                        $this->User->save($this->request->data);
                        $this->smsmail($users['User']['id']);
                        if ($autorsponder_message != '') {
                            $message = $autorsponder_message;
                        } else {
                            $message = $answers123['Question']['autoreply_message'];
                        }
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
                        //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        //*********** Save to activity timeline
                        $timeline['ActivityTimeline']['user_id'] = $user_id;
                        $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                        $timeline['ActivityTimeline']['activity'] = 2;
                        $timeline['ActivityTimeline']['title'] = 'Voted in Poll';
                        $timeline['ActivityTimeline']['description'] = 'Contact voted in poll: ' . $answers123['Question']['question'];
                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                        $this->ActivityTimeline->save($timeline);
                        //*************
                        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                        echo "<Response></Response>";
                        exit;
                    } else {
                        app::import('Model', 'User');
                        $this->User = new User();
                        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                        $credits = $users['User']['sms_balance'];
                        $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
                        $this->request->data['User']['id'] = $user_id;
                        $this->User->save($this->request->data);
                        $this->smsmail($users['User']['id']);
                        $message = "You have already voted in this poll";
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }else{
                            $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                        }
						//$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
						echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                        echo "<Response></Response>";
                        exit;
                    }
                }
            }
        } else if (!empty($contestkeywords)) {
            app::import('Model', 'ContestSubscriber');
            $this->ContestSubscriber = new ContestSubscriber();
            $contestsubcount = $this->ContestSubscriber->find('count', array('conditions' => array('ContestSubscriber.phone_number' => $phone, 'ContestSubscriber.contest_id' => $contest_id)));
            $credits = $someone['User']['sms_balance'];
            if ($contestkeywords['Contest']['winning_phone_number'] !=''){
                $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
                $this->request->data['User']['id'] = $contestkeywords['Contest']['user_id'];
                $this->User->save($this->request->data);
                $message1 = "A winner was already selected. Stay tuned for other contests we run!";
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message1);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message1);
                }
                //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message1);
                $this->smsmail($user_id);
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
            if ($contestsubcount == $contestkeywords['Contest']['maxentries']) {
                $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
                $this->request->data['User']['id'] = $contestkeywords['Contest']['user_id'];
                $this->User->save($this->request->data);
                $message1 = "You have already entered this contest " . $contestsubcount . " time(s) which is the maximum number allowed.";
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message1);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message1);
                }
                //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message1);
                $this->smsmail($user_id);
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
            $current_date = date('Y-m-d');
            if ($contestkeywords['Contest']['startdate'] > $current_date) {
                $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
                $this->request->data['User']['id'] = $contestkeywords['Contest']['user_id'];
                $this->User->save($this->request->data);
                $message = "Contest " . $contestkeywords['Contest']['group_name'] . " hasn't started yet. It begins on " . date('m/d/Y', strtotime($contestkeywords['Contest']['startdate'])) . "";
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                }
                //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                $this->smsmail($contestkeywords['Contest']['user_id']);
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            } else if ($contestkeywords['Contest']['enddate'] < $current_date) {
                $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
                $this->request->data['User']['id'] = $contestkeywords['Contest']['user_id'];
                $this->User->save($this->request->data);
                $message = "Contest " . $contestkeywords['Contest']['group_name'] . " ended on " . date('m/d/Y', strtotime($contestkeywords['Contest']['enddate'])) . "";
                if ($SID == ''){
                    $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                }else{
                    $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$message);
                }
                //$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$message);
                $this->smsmail($contestkeywords['Contest']['user_id']);
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
            $contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'Contact.user_id' => $user_id, 'ContactGroup.group_id' => $contestkeywords['Contest']['group_id'])));
            if (empty($contact)) {
                if (NUMVERIFY != '') {
                    $numbervalidation = $this->validateNumber($phone);
                    $errorcode = $numbervalidation['error']['code'];
                    if ($errorcode == '') {
                        $this->request->data['Contact']['carrier'] = $numbervalidation['carrier'];
                        $this->request->data['Contact']['location'] = $numbervalidation['location'];
                        $this->request->data['Contact']['phone_country'] = $numbervalidation['country_name'];
                        $this->request->data['Contact']['line_type'] = $numbervalidation['line_type'];
                    }
                }else{
                    //if ($SID == ''){
                    //    $carrier = $this->Ytel->lookupcarrier(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone);
                    //}else{
                    //    $carrier = $this->Ytel->lookupcarrier($SID,$AUTHTOKEN,$phone);
                    //}
                    
                    //if($carrier->Message360->ResponseStatus == 1){
                        $this->request->data['Contact']['carrier'] = $jsonObject->data->payload->from->carrier;
                    //    $this->request->data['Contact']['location'] = $carrier->Message360->Carrier->City;
                    //}
                }
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $this->request->data['Contact']['phone_number'] = $phone;
                $this->request->data['Contact']['user_id'] = $user_id;
                $this->request->data['Contact']['created'] = date('Y-m-d H:i:s', time());
                $this->request->data['Contact']['color'] = $this->choosecolor();
                $this->Contact->save($this->request->data);
                $contact_id = $this->Contact->id;
                $this->request->data['ContactGroup']['user_id'] = $user_id;
                $this->request->data['ContactGroup']['contact_id'] = $contact_id;
                $this->request->data['ContactGroup']['group_id'] = $contestkeywords['Contest']['group_id'];
                $this->request->data['ContactGroup']['group_subscribers'] = $contestkeywords['Group']['keyword'];
                $this->request->data['ContactGroup']['subscribed_by_sms'] = 1;
                $this->request->data['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
                $this->ContactGroup->save($this->request->data);
                if ($someone['User']['email_alert_options'] == 0) {
                    if ($someone['User']['email_alerts'] == 1) {
                        $username = $someone['User']['username'];
                        $email = $someone['User']['email'];
                        $date = date('Y-m-d H:i:s', time());
                        $subject = "New Subscriber to " . $contestkeywords['Group']['group_name'];
                        $sitename = str_replace(' ', '', SITENAME);
                        $Email = new CakeEmail();
                        if(EMAILSMTP==1){
                            $Email->config('smtp');
                        }
                        $Email->from(array(SUPPORT_EMAIL => SITENAME));
                        $Email->to($email);
                        $Email->subject($subject);
                        $Email->template('new_subscriber_template');
                        $Email->emailFormat('html');
                        $Email->viewVars(array('username' => $username));
                        $Email->viewVars(array('phoneno' => $phone));
                        $Email->viewVars(array('groupname' => $contestkeywords['Group']['group_name']));
                        $Email->viewVars(array('keyword' => $contestkeywords['Group']['keyword']));
                        $Email->viewVars(array('datetime' => $date));
                        $Email->send();
                    }
                }
                app::import('Model', 'Group');
                $this->Group = new Group();
                $this->request->data['Group']['id'] = $contestkeywords['Contest']['group_id'];
                $this->request->data['Group']['totalsubscriber'] = $contestkeywords['Group']['totalsubscriber'] + 1;
                $this->Group->save($this->request->data);
            }
            $this->request->data['ContestSubscriber']['user_id'] = $contestkeywords['Contest']['user_id'];
            $this->request->data['ContestSubscriber']['contest_id'] = $contestkeywords['Contest']['id'];
            $this->request->data['ContestSubscriber']['phone_number'] = $phone;
            $this->ContestSubscriber->save($this->request->data);
            app::import('Model', 'Contest');
            $this->Contest = new Contest();
            $totalsubscriberdata = $this->Contest->find('first', array('conditions' => array('Contest.id' => $contest_id)));
            $Contestdata['id'] = $contest_id;
            $Contestdata['totalsubscriber'] = $totalsubscriberdata['Contest']['totalsubscriber'] + 1;
            $this->Contest->save($Contestdata);
            $this->request->data['User']['sms_balance'] = $credits - CHARGEINCOMINGSMS - 1;
            $this->request->data['User']['id'] = $user_id;
            $this->User->save($this->request->data);
            if ($SID == ''){
                $this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$contestkeywords['Contest']['system_message']);
            }else{
                $this->Telnyx->sendsms($SID,$_REQUEST['From'],$_REQUEST['To'],$contestkeywords['Contest']['system_message']);
            }
			//$this->Telnyx->sendsms(TELNYX_KEY,$_REQUEST['From'],$_REQUEST['To'],$contestkeywords['Contest']['system_message']);
            $this->smsmail($user_id);
            //*********** Save to activity timeline
            $timeline['ActivityTimeline']['user_id'] = $contestkeywords['Contest']['user_id'];
            $timeline['ActivityTimeline']['contact_id'] = $contact_id;
            $timeline['ActivityTimeline']['activity'] = 3;
            $timeline['ActivityTimeline']['title'] = 'Entered Contest';
            $timeline['ActivityTimeline']['description'] = 'Contact entered contest: ' . $contestkeywords['Contest']['group_name'];
            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
            $this->ActivityTimeline->save($timeline);
            //*************
        } else {
            if ($someone['User']['sms_balance'] > 0) {
                $this->User->id = $someone['User']['id'];
                if ($this->User->id != '') {
                    //$length = strlen(utf8_decode(substr($_REQUEST['Text'], 0, 1600)));
                    $length = mb_strlen($_REQUEST['Text'],"UTF-8");
			        $gsm = $this->is_gsm($_REQUEST['Text']);
			        if (trim($_REQUEST['media']) != '') {
			            $credits = 1;
			        }elseif(!$gsm){
                        $credits = ceil($length / 70);
                    } else {
                        $credits = ceil($length / 160);
                    }
                    if(CHARGEINCOMINGSMS == 1){
                        $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - $credits));
                    }
                }
                //*********** Save to activity timeline
                $timeline['ActivityTimeline']['user_id'] = $someone['User']['id'];
                $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                $timeline['ActivityTimeline']['activity'] = 10;
                $timeline['ActivityTimeline']['title'] = 'Incoming SMS Message';
                $timeline['ActivityTimeline']['description'] = $_REQUEST['Text'];
                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                $this->ActivityTimeline->save($timeline);
                //*************
                Controller::loadModel('Log');
                $this->Log->create();
                $this->request->data['Log']['user_id'] = $someone['User']['id'];
                $this->request->data['Log']['phone_number'] = $_REQUEST['From'];
                $this->request->data['Log']['name'] = $contactname;
                $this->request->data['Log']['contact_id'] = $contact_id;
                $this->request->data['Log']['email_to_sms_number'] = $_REQUEST['To'];
                $this->request->data['Log']['text_message'] = $_REQUEST['Text'];
                $this->request->data['Log']['created'] = date('Y-m-d H:i:s', time());
                if (isset($_REQUEST['Status'])) {
                    $this->request->data['Log']['sms_status'] = $_REQUEST['Status'];
                }else{
                    $this->request->data['Log']['sms_status'] = 'received';
                }
                $this->request->data['Log']['image_url'] = $_REQUEST['media'];
                $this->request->data['Log']['inbox_type'] = 1;
                $this->request->data['Log']['route'] = 'inbox';
                pr($this->request->data['Log']);
                if ($someone['User']['email_to_sms'] == 0) {
                    $random_generator = $this->random_generator(15);
                    $subject = 'Incoming SMS to Email Notice-' . $random_generator;
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($someone['User']['email']);
                    $Email->subject($subject);
                    $Email->template('sendemail');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('phone' => $_REQUEST['From']));
                    $Email->viewVars(array('name' => $contactname));
                    $Email->viewVars(array('message' => $_REQUEST['Text']));
                    $Email->send();
                    $this->request->data['Log']['ticket'] = $random_generator;
                }
                $this->Log->save($this->request->data);
                if ($contact_id > 0) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $contact_arra_save['Contact']['id'] = $contact_id;
                    $contact_arra_save['Contact']['lastmsg'] = date('Y-m-d H:i:s');
                    $this->Contact->save($contact_arra_save);
                } else {
                    if ($someone['User']['sms_balance'] > 0 && $someone['User']['incoming_nonkeyword'] == 1) {
                        $this->User->id = $someone['User']['id'];
                        if ($this->User->id != '') {
                            $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - 1 - CHARGEINCOMINGSMS));
                        }
                        $this->Log = new Log();
                        $message = $someone['User']['nonkeyword_autoresponse'];
                        $to = $_REQUEST['From'];
                        $from = $_REQUEST['To'];
                        if ($SID == ''){
                            $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
                        }else{
                            $response = $this->Telnyx->sendsms($SID,$to,$from,$message);
                        }
                        //$response =  $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
						$message_id = '';
						$status = '';
						$errortext = '';
						if(isset($response->message)){
							$errortext = $response->message;
							$status = 'failed';
						}else{
							$status = 'sent';
						}
                        $this->request->data['Log']['contact_id'] = 0;
                        $this->request->data['Log']['group_sms_id'] = 0;
                        $this->request->data['Log']['sms_id'] = $message_id;
                        $this->request->data['Log']['user_id'] = $user_id;
                        $this->request->data['Log']['group_id'] = 0;
                        $this->request->data['Log']['phone_number'] = $to;
                        $this->request->data['Log']['text_message'] = $message;
                        $this->request->data['Log']['route'] = 'outbox';
                        $this->request->data['Log']['sms_status'] = '';
                        $this->request->data['Log']['error_message'] = '';
                        $this->request->data['Log']['sms_status'] = $status;
						$this->request->data['Log']['error_message'] = $errortext;
                        $this->Log->save($this->request->data);
                    }
                }
                if ($someone['User']['incomingsms_alerts'] == 0) {
                    if ($someone['User']['incomingsms_emailalerts'] == 1) {
                        $username = $someone['User']['username'];
                        $email = $someone['User']['email'];
                        $from = $_REQUEST['From'];
                        $date = date('Y-m-d H:i:s', time());
                        $sitename = str_replace(' ', '', SITENAME);
                        $subject = "New Incoming SMS To Your Account At " . SITENAME;
                        $Email = new CakeEmail();
                        if(EMAILSMTP==1){
                            $Email->config('smtp');
                        }
                        $Email->from(array(SUPPORT_EMAIL => SITENAME));
                        $Email->to($email);
                        $Email->subject($subject);
                        $Email->template('incoming_sms_email_alert');
                        $Email->emailFormat('html');
                        $Email->viewVars(array('username' => $username));
                        $Email->viewVars(array('from' => $from));
                        $Email->viewVars(array('name' => $contactname));
                        $Email->viewVars(array('body' => $_REQUEST['Text']));
                        $Email->send();
                    } elseif ($someone['User']['incomingsms_emailalerts'] == 2) {
                        $this->User->id = $someone['User']['id'];
                        if ($this->User->id != '') {
                            $this->User->saveField('sms_balance', ($someone['User']['sms_balance'] - 1 - CHARGEINCOMINGSMS));
                        }
                        $this->Log = new Log();
                        $message = "Incoming SMS Alert From: " . $_REQUEST['From'] . " - " . $_REQUEST['Text'];
                        $to = $username = $someone['User']['smsalerts_number'];
                        $from = $_REQUEST['To'];
                        if ($SID == ''){
                            $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
                        }else{
                            $response = $this->Telnyx->sendsms($SID,$to,$from,$message);
                        }
                        //$response =  $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
						$message_id = '';
						$status = '';
						$errortext = '';
						if(isset($response->message)){
							$errortext = $response->message;
							$status = 'failed';
						}else{
							$status = 'sent';
						}
                        $this->request->data['Log']['contact_id'] = 0;
                        $this->request->data['Log']['group_sms_id'] = 0;
                        $this->request->data['Log']['sms_id'] = $message_id;
                        $this->request->data['Log']['user_id'] = $user_id;
                        $this->request->data['Log']['group_id'] = 0;
                        $this->request->data['Log']['phone_number'] = $to;
                        $this->request->data['Log']['text_message'] = $message;
                        $this->request->data['Log']['route'] = 'outbox';
                        $this->request->data['Log']['sms_status'] = '';
                        $this->request->data['Log']['error_message'] = '';
						$this->request->data['Log']['sms_status'] = $status;
						$this->request->data['Log']['error_message'] = $errortext;
                        $this->Log->save($this->request->data);
                    }
                }
            }
        }
        
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response></Response>";
    }
	function Immediatelyresponder($user_id = null, $group_id = null, $to = null, $from = null){
        $this->autoRender = false;
        app::import('Model', 'Responder');
        $this->Responder = new Responder();
        app::import('Model', 'User');
        $this->User = new User();
        $response = $this->Responder->find('first', array('conditions' => array('Responder.user_id' => $user_id, 'Responder.group_id' => $group_id, 'Responder.days' => 0)));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $SID = trim($users['User']['sid']);
        $AUTHTOKEN = trim($users['User']['authtoken']);

        if ($response['Responder']['sms_type'] == 2) {
            if ($users['User']['mms'] == 1) {
                $assigned_number = $users['User']['assigned_number'];
            } else {
                app::import('Model', 'UserNumber');
                $this->UserNumber = new UserNumber();
                $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                if (!empty($mmsnumber)) {
                    $assigned_number = $mmsnumber['UserNumber']['number'];
                } else {
                    $assigned_number = $users['User']['assigned_number'];
                }
            }
        } else {
            if (!empty($users)) {
                if ($users['User']['sms'] == 1) {
                    $assigned_number = $users['User']['assigned_number'];
                } else {
                    app::import('Model', 'UserNumber');
                    $this->UserNumber = new UserNumber();
                    $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                    if (!empty($user_numbers)) {
                        $assigned_number = $user_numbers['UserNumber']['number'];
                    } else {
                        $assigned_number = $users['User']['assigned_number'];
                    }
                }
            }
        }
        if ($assigned_number != '') {
            if (!empty($response)) {
                $current_datetime = date("n/d/Y");
                if ($users['User']['sms_balance'] > 0) {
                    $Responderid = $response['Responder']['id'];
                    $group_id = $response['Responder']['group_id'];
                    $sms_type = $response['Responder']['sms_type'];
                    $image_url = $response['Responder']['image_url'];
                    $message = str_replace('%%CURRENTDATE%%', $current_datetime, $response['Responder']['message']);
                    $systemmsg = $response['Responder']['systemmsg'];
                    $user_id = $response['Responder']['user_id'];
                    $body = $message . "\n" . $systemmsg;
                    if ($sms_type == 1) {
                        if ($SID == ''){
                            $this->Telnyx->sendsms(TELNYX_KEY,$to,$assigned_number,$body);
                        }else{
                            $this->Telnyx->sendsms($SID,$to,$assigned_number,$body);
                        }
						//$this->Telnyx->sendsms(TELNYX_KEY,$to, $assigned_number, $body);
                        $credits = 1;
                    } else if ($sms_type == 2) {
                        $message_arr = explode(',', $image_url);
                        if ($SID == ''){
                            $this->Telnyx->sendmms(TELNYX_KEY,$to,$assigned_number,$body,$message_arr);
                        }else{
                            $this->Telnyx->sendmms($SID,$to,$assigned_number,$body,$message_arr);
                        }
						//$this->Telnyx->sendmms(TELNYX_KEY,$to, $assigned_number, $body,$message_arr);
						$credits = CHARGECREDITSMMS;
                    }
                    $usersave['User']['id'] = $user_id;
                    $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                    $this->User->save($usersave);
                }
            }
        }
    }
	function status($group_sms_id=null,$group_id=null,$userid=null){
	   	$this->autoRender = false;
	   	
	   	$out = @file_get_contents('php://input');
        $event_json = json_decode('[' . $out . ']');
        $jsonObject = $event_json[0];
        
        $smsid = $jsonObject->data->payload->id;
        $type = $jsonObject->data->payload->type;
        $text = $jsonObject->data->payload->text;
        $status = $jsonObject->data->payload->to[0]->status;
        
        /*ob_start();
        print_r($jsonObject);
        print_r($smsid);
        print_r("----");
        print_r($type);
        print_r("----");
        print_r($text);
        print_r("----");
        print_r($status);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/telnyxstatus".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
	   	
        app::import('Model', 'Log');
		$this->Log = new Log();
		$this->Log->recursive = -1;
		$someone = $this->Log->find('first', array('conditions' => array('Log.sms_id' => $smsid), 'fields' => array('Log.id', 'Log.user_id', 'Log.text_message', 'Log.group_sms_id')));
		$length = mb_strlen($text,"UTF-8");
		$gsm = $this->is_gsm($text);
		
		if (trim($type) == "SMS") {
		    if(!$gsm) { 
			    $credits = ceil($length / 70);
		    } else {
			    $credits = ceil($length / 160);
		    }
		} else {
			$credits = CHARGECREDITSMMS;
		}
		
		if (!empty($someone)){
            $logid = $someone['Log']['id'];
            $user_id = $someone['Log']['user_id'];
            //$Status = $_REQUEST['MessageStatus'];
            $this->request->data['Log']['id'] = $logid;

            app::import('Model', 'GroupSmsBlast');
            $this->GroupSmsBlast = new GroupSmsBlast();
            $GroupSmsBlast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $someone['Log']['group_sms_id'])));

            if (trim($status) == 'delivery_failed' || trim($status) == 'sending_failed') {
                app::import('Model', 'User');
                $this->User = new User();
                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'fields' => array('User.sms_balance', 'User.id', 'User.sid')));
                
           		$ErrorMessage = $jsonObject->data->payload->errors[0]->detail;
                    
                if (!empty($GroupSmsBlast['GroupSmsBlast']['id'])) {
                    $this->request->data['GroupSmsBlast']['total_successful_messages'] = $GroupSmsBlast['GroupSmsBlast']['total_successful_messages'] - 1;
                    $this->request->data['GroupSmsBlast']['total_failed_messages'] = $GroupSmsBlast['GroupSmsBlast']['total_failed_messages'] + 1;
                    $this->request->data['GroupSmsBlast']['id'] = $GroupSmsBlast['GroupSmsBlast']['id'];
                    $this->GroupSmsBlast->save($this->request->data);
                }

                if (trim($status) == 'sending_failed') {
                    $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] + $credits;
                    $this->request->data['User']['id'] = $usersms['User']['id'];
                    $this->User->save($this->request->data);
                }

                $this->request->data['Log']['error_message'] = $ErrorMessage;
                $this->request->data['Log']['sms_status'] = $status;

            } elseif (trim($status) == 'delivered') {
                $this->request->data['Log']['sms_status'] = $status;
            }

            $this->Log->save($this->request->data);
        }
		
	}
	
	function rvmstatus($user_id,$group_id,$voicebalance){
	    $this->autoRender = false;
	    
	    app::import('Model', 'Log');
		$this->Log = new Log();
		$this->Log->recursive = -1;
		
		if(strtoupper(trim($_REQUEST['Status']))=="COMPLETED"){
    	    $logarr['Log']['id'] = '';                                    
            $logarr['Log']['user_id'] = $user_id;                                    
            $logarr['Log']['group_id'] = $group_id;                                    
            $logarr['Log']['phone_number'] = str_replace('+', '', $_REQUEST['To']);  
            $logarr['Log']['sms_status'] = $_REQUEST['VoiceMailStatus']; 
            $logarr['Log']['route'] = 'inbox';                                   
            $logarr['Log']['msg_type'] = 'broadcast';                                    
            $this->Log->save($logarr); 
            
            if (strtoupper(trim($_REQUEST['VoiceMailStatus']))=="SUCCESS" && $_REQUEST['CallCost'] > 0){
                $voicebalance = $voicebalance-CHARGECREDITSRVM;
                $this->User->id = $user_id;
                $this->User->saveField('voice_balance', $voicebalance);
            }

		}
		
	    /*ob_start();
        print_r($_REQUEST);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/rvmstatus".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
	    
	}
	
	function voice()
    {
        //ob_start();
        $this->autoRender = false;
        
        $_REQUEST['To'] = str_replace('+', '', $_REQUEST['To']);
        $_REQUEST['From'] = str_replace('+', '', $_REQUEST['From']);
        $callerid = $_REQUEST['From'];

        Controller::loadModel('User');
        $someone = $this->User->find('first', array('conditions' => array('assigned_number' => '' . trim($_REQUEST['To']) . '')));
        if (empty($someone)) {
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $someone = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => '' . trim($_REQUEST['To']) . '')));
        }
        date_default_timezone_set($someone['User']['timezone']);
        $active = $someone['User']['active'];
        $user_id = $someone['User']['id'];
        
        if ($someone['User']['voice_balance'] > 0 && $active == 1) {
            if ($someone['User']['incomingcall_forward'] == 0 && $someone['User']['assign_callforward'] == trim($_REQUEST['To'])) {
                header("content-type: text/xml");
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<Response>';
                echo '<Dial callerId="' .$callerid.'">';
                echo '<Number statusCallback="' . SITE_URL . '/telnyxs/voicerecord/'.$user_id.'">+'.$someone['User']['callforward_number'].'</Number>';
                echo '</Dial>';
                echo '</Response>';
                exit;

            } else {
                //$this->User->id = $someone['User']['id'];
                //$this->User->saveField('voice_balance', ($someone['User']['voice_balance'] - 1));
                //$this->voicebalancemail($someone['User']['id']);

                Controller::loadModel('Log');
                $this->Log->create();
                $this->request->data['Log']['user_id'] = $someone['User']['id'];
                $this->request->data['Log']['phone_number'] = $_REQUEST['From'];
                $this->request->data['Log']['voice_url'] = $_REQUEST['CallSid'];
                $this->request->data['Log']['route'] = 'inbox';
                $this->request->data['Log']['msg_type'] = 'voice';
                $this->Log->save($this->request->data);

                if ($someone['User']['welcome_msg_type'] == 1) {
                    $msg = "<Play>" . SITE_URL . '/mp3/' . $someone['User']['mp3'] . "</Play>";
                } else {
                    $msg = "<Say>" . $someone['User']['defaultgreeting'] . "</Say>";
                }

                header("content-type: text/xml");
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo $msg;
                echo "<Record
							recordingStatusCallback='" . SITE_URL . "/telnyxs/voicerecording'
							method='POST'
							finishOnKey='*'
							playBeep='true'
							/>
				</Response>";
                exit;
            }
        } else {
            header("content-type: text/xml");
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response></Response>";
            exit;
        }
    }

    function voicerecord($user_id,$forwardfrombroadcast=0)
    {
        $this->autoRender = false;
        
        /*ob_start();
		echo "<pre>";
		print_r($_REQUEST);
		echo "</pre>";
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/voicerecord".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
        
        if(strtoupper(trim($_REQUEST['CallStatus'])) == "COMPLETED"){
            
            if (isset($_REQUEST['CallDuration']) && $_REQUEST['CallDuration'] > 0) {
                $duration = $_REQUEST['CallDuration'];
                $_REQUEST['To'] = str_replace('+', '', $_REQUEST['To']);
                $_REQUEST['From'] = str_replace('+', '', $_REQUEST['From']);
                $number = trim($_REQUEST['From']);
                
                Controller::loadModel('User');
               
                //$someone = $this->User->find('first', array('conditions' => array('assigned_number' => $number)));
                $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                if (empty($someone)) {
                    app::import('Model', 'UserNumber');
                    $this->UserNumber = new UserNumber();
                    //$someone = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => $number)));
                    $someone = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id)));
                }
                date_default_timezone_set($someone['User']['timezone']);
                
                Controller::loadModel('Log');
                $this->Log->create();
                $this->request->data['Log']['user_id'] = $user_id;
                $this->request->data['Log']['phone_number'] = $_REQUEST['From'];
                $this->request->data['Log']['route'] = 'inbox';
                $this->request->data['Log']['msg_type'] = 'callforward';
                $this->request->data['Log']['call_duration'] = gmdate("H:i:s", $duration);
    
                $minute = gmdate("H:i:s", $duration);
                $minute_arr = explode(':', $minute);
                $hour = 0;
                $mint = 0;
                $secnd = 0;
                if ($minute_arr[0] != '00') {
                    $hour = $minute_arr[0] * 60;
                }
                if ($minute_arr[1] > 0) {
                    $mint = $minute_arr[1];
                }
                if ($minute_arr[2] > 0) {
                    $secnd = 1;
                }
                $minutes = $hour + $mint + $secnd;
    
                if ($this->Log->save($this->request->data)) {
                    if ($minutes > 0) {
                        if ($forwardfrombroadcast == 0) {
                            $totalminutes = $minutes * 2;
                        } else {
                            $totalminutes = $minutes;
                        }
                        //$totalminutes = $minutes * 2;
                        $voice_balance = $someone['User']['voice_balance'] - $totalminutes;
                        $this->User->id = $someone['User']['id'];
                        $this->User->saveField('voice_balance', $voice_balance);
                        $this->voicebalancemail($someone['User']['id']);
                    }
        
                }
            }
        }
        
        header("content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response></Response>";
        exit;
    }

    function voicerecording()
    {
        //ob_start();
        $this->autoRender = false;
        
        $_REQUEST['To'] = str_replace('+', '', $_REQUEST['To']);
        $_REQUEST['From'] = str_replace('+', '', $_REQUEST['From']);
        
        /*ob_start();
        print_r($_REQUEST);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/telnyxvoice_recording".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/

        Controller::loadModel('Log');
        $this->Log->recursive = -1;
        $someoneLog = $this->Log->find('first', array('conditions' => array('voice_url' => '' . trim($_REQUEST['CallSid']) . '')));
        if (isset($someoneLog['Log']['id']) && isset($_REQUEST['RecordingUrl'])) {
            $this->Log->id = $someoneLog['Log']['id'];
            $url_to_image = $_REQUEST['RecordingUrl'];
            $this->Log->saveField('voice_url', $url_to_image);
        }
        //if(isset($_REQUEST['TranscriptionText'])){
        if (isset($_REQUEST['RecordingUrl'])) {
            Controller::loadModel('User');
            $someone = $this->User->find('first', array('conditions' => array('assigned_number' => '' . trim($_REQUEST['To']) . '')));
            if (empty($someone)) {
                app::import('Model', 'UserNumber');
                $this->UserNumber = new UserNumber();
                $someone = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => '' . trim($_REQUEST['To']) . '')));

            }
            
            $minute = gmdate("H:i:s", $_REQUEST['RecordingDuration']);
            $minute_arr = explode(':', $minute);
            $hour = 0;
            $mint = 0;
            $secnd = 0;
            if ($minute_arr[0] != '00') {
                $hour = $minute_arr[0] * 60;
            }
            if ($minute_arr[1] > 0) {
                $mint = $minute_arr[1];
            }
            if ($minute_arr[2] > 0) {
                $secnd = 1;
            }
            $minutes = $hour + $mint + $secnd;
            
            if ($minutes > 0) {
                $this->User->id = $someone['User']['id'];
                $this->User->saveField('voice_balance', ($someone['User']['voice_balance'] - $minutes));
                $this->voicebalancemail($someone['User']['id']);
            }
                
            if (!empty($someone) && trim($someone['User']['voicemailnotifymail']!='')) {
                $this->User->id = $someone['User']['id'];
                $email = $someone['User']['voicemailnotifymail'];
                $id = $someone['User']['id'];
                $first_name = $someone['User']['first_name'];
                $sitename = str_replace(' ', '', SITENAME);
                $subject = "New Voicemail: " . SITENAME . "";
                $comment = SITE_URL;
                
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to($email);
                $Email->subject($subject);
                $Email->template('newvoicemail');
                $Email->emailFormat('html');
                $Email->viewVars(array('first_name' => $first_name));
                $Email->viewVars(array('comment' => $comment));
                $Email->viewVars(array('email' => $email));
                $Email->send();
            }
            /********** Mail function ends*/
        }
        header("content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response></Response>";
        exit;
    }
	
	function voicebalancemail($user_id = null){
        $this->autoRender = false;
        app::import('Model', 'User');
        $this->User = new User();
        $usersvoicebalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        if ($usersvoicebalance['User']['email_alert_credit_options'] == 0) {
            if ($usersvoicebalance['User']['voice_balance'] <= $usersvoicebalance['User']['low_voice_balances']) {
                if ($usersvoicebalance['User']['VM_credit_balance_email_alerts'] == 0) {
                    $username = $usersvoicebalance['User']['username'];
                    $email = $usersvoicebalance['User']['email'];
                    $sitename = str_replace(' ', '', SITENAME);
                    $subject = "Low Voice Credit Balance";
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($email);
                    $Email->subject($subject);
                    $Email->template('low_voice_credit_template');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('username' => $username));
                    $Email->viewVars(array('low_voice_balances' => $usersvoicebalance['User']['low_voice_balances']));
                    $Email->send();
                    $this->User->id = $usersvoicebalance['User']['id'];
                    $this->User->saveField('VM_credit_balance_email_alerts', 1);
                }
            }
        }
    }

	function peoplecallrecordscript($group_id = null, $loop = null, $language = null, $pause = null, $forward = null, $forward_number = null)
    {
        $this->autoRender = false;
        app::import('Model', 'VoiceMessage');
        $this->VoiceMessage = new VoiceMessage();
        $VoiceMessage = $this->VoiceMessage->find('first', array('conditions' => array('VoiceMessage.group_id' => $group_id)));
        $message_type = $VoiceMessage['VoiceMessage']['message_type'];
        if ($message_type == 1) {  // audio url
            $audio_url = SITE_URL . '/voice/' . $VoiceMessage['VoiceMessage']['audio']; // audio file url
            header("content-type: text/xml");
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>";
            echo "<Pause length='" . $pause . "'/>";
            echo "<Play loop='" . $loop . "'>" . $audio_url . "</Play>";
            echo "<Gather action='" . SITE_URL . "/telnyxs/do_not_call/" . $group_id . "/" . $forward . "/" . $forward_number . "' timeout='3' numDigits='1' finishOnKey='*'>";
            if ($forward == 1) {
                echo "<Say voice='alice' language ='" . $language . "'>Please press 1 to speak with a representative or press 2 to be added to the do not call list.</Say>";
            } else {
                echo "<Say voice='alice' language ='" . $language . "'>Please press 2 to be added to the do not call list.</Say>";
            }
            echo "</Gather>";
            echo "</Response>";
            exit;

        } else {  // text to voice
            $msg = $VoiceMessage['VoiceMessage']['text_message'];
            header("content-type: text/xml");
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>";
            echo "<Pause length='" . $pause . "'/>";
            echo "<Say voice='alice' language ='" . $language . "' loop='" . $loop . "'>" . $msg . "</Say>";
            echo "<Gather action='" . SITE_URL . "/telnyxs/do_not_call/" . $group_id . "/" . $forward . "/" . $forward_number . "' timeout='3' numDigits='1' finishOnKey='*'>";
            if ($forward == 1) {
                echo "<Say voice='alice' language ='" . $language . "'>Please press 1 to speak with a representative or press 2 to be added to the do not call list.</Say>";
            } else {
                echo "<Say voice='alice' language ='" . $language . "'>Please press 2 to be added to the do not call list.</Say>";
            }
            echo "</Gather>";
            echo "</Response>";
            exit;
        }
    }
	
	function do_not_call($group_id = null, $forward = null, $forward_number = null)
    {
        $this->autoRender = false;

        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $contactgroup = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.group_id' => $group_id)));
        $user_id = $contactgroup['ContactGroup']['user_id'];
        
        /*ob_start();
		print_r($forward_number);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/donotcall".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/

        if ($_REQUEST['Digits'] == 2) {
            $phone = str_replace('+', '', $_REQUEST['To']);
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $Subscriber = $this->Contact->find('first', array('conditions' => array('phone_number' => $phone, 'Contact.user_id' => $user_id)));
            if (!empty($Subscriber)) {
                $Subscribergroup = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $Subscriber['Contact']['id'], 'ContactGroup.group_id' => $group_id)));
                if (!empty($Subscribergroup)) {
                    $arr['ContactGroup']['id'] = $Subscribergroup['ContactGroup']['id'];
                    $arr['ContactGroup']['do_not_call'] = 1;
                    $this->ContactGroup->save($arr);

                    header("content-type: text/xml");
                    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                    echo "<Response><Say>You have successfully been un-subscribed and added to the do not call list</Say></Response>";
                    exit;
                } else {
                    header("content-type: text/xml");
                    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                    echo "<Response></Response>";
                    exit;
                }
            } else {
                header("content-type: text/xml");
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response></Response>";
                exit;
            }
        } elseif ($_REQUEST['Digits'] == 1) {
            header("content-type: text/xml");
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            //echo '<Response>';
            //echo '<Dial>+' . $forward_number . '</Dial>';
            //echo '</Response>';
            
            echo '<Response>';
            echo '<Dial>';
            echo '<Number statusCallback="' . SITE_URL . '/telnyxs/voicerecord/'.$user_id.'/'.$forward.'">+'.$forward_number.'</Number>';
            echo '</Dial>';
            echo '</Response>';
                
            exit;
        } else {
            header("content-type: text/xml");
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response></Response>";
            exit;
        }
    }
    
    function sendcallStatus($lastinsertID = null)
    {

        $this->autoRender = false;
        
        if ($lastinsertID > 0) {
            //if(isset($_REQUEST['RecordingUrl'])){
            if (strtoupper(trim($_REQUEST['CallStatus'])) == 'COMPLETED') {
                
                /*ob_start();
        		echo "<pre>";
        		print_r($_REQUEST);
        		echo "</pre>";
        		$out1 = ob_get_contents();
        		ob_end_clean();
        		$file = fopen("debug/sendcallStatus".time().".txt", "w");
        		fwrite($file, $out1);
        		fclose($file);*/
		
                Controller::loadModel('Log');
                app::import('Model', 'Log');
                $this->Log = new Log();
                $this->Log->recursive = -1;
                $someone = $this->Log->find('first', array('conditions' => array('Log.id' => $lastinsertID)));
                if (!empty($someone)) {
                    app::import('Model', 'User');
                    $this->User = new User();
                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $someone['Log']['user_id'])));
                    if (!empty($usersms)) {

                        //if(isset($_REQUEST['CallDuration'])){
                        $minute = gmdate("H:i:s", $_REQUEST['CallDuration']);
                        $minute_arr = explode(':', $minute);
                        $hour = 0;
                        $mint = 0;
                        $secnd = 0;
                        if ($minute_arr[0] != '00') {
                            $hour = $minute_arr[0] * 60;
                        }
                        if ($minute_arr[1] > 0) {
                            $mint = $minute_arr[1];
                        }
                        if ($minute_arr[2] > 0) {
                            $secnd = 1;
                        }
                        $minutes = $hour + $mint + $secnd;
                        
                        //if ($forward == 1) {
                        //    $totalminutes = $minutes * 2;
                        //} else {
                            $totalminutes = $minutes;
                        //}
                        // }

                        $arr['User']['voice_balance'] = $usersms['User']['voice_balance'] - $totalminutes;
                        $arr['User']['id'] = $usersms['User']['id'];
                        $this->User->save($arr);
                        $this->voicebalancemail($usersms['User']['id']);
                    }
                }

            }
            $this->request->data['Log']['id'] = $lastinsertID;
            $this->request->data['Log']['sms_status'] = $_REQUEST['CallStatus'];
            $this->Log->save($this->request->data);
        }
    }
    
    function faxsend($sourcepage)
    {
        $this->autoRender = false; 
        
        $userDetails = $this->getLoggedUserDetails();
        $SID = trim($userDetails['User']['sid']);
        $CALLAPP = trim($userDetails['User']['call_app_id']);

        if ($userDetails['User']['voice_balance'] > 0) {
            $filename = $this->request->data['Contact']['fax']['name'];
            $type = $this->request->data['Contact']['fax']['type'];
             
            if($type!='application/pdf'){
              $this->Session->setFlash(__('You can only upload PDF files.', true));
              $this->redirect(array('controller' => 'contacts'));
            }
           
            $tmp_name = $this->request->data['Contact']['fax']['tmp_name'];
            $time = time();
            $filename = str_replace(' ', '_', $filename);
            $tmp_name = str_replace(' ', '_', $tmp_name);
            if (!empty($filename)) {
                move_uploaded_file($tmp_name, "fax/" . $time . $filename);
                $this->request->data['Contact']['fax'] = $time . $filename;
            } else {
                $this->request->data['Contact']['fax'] = '';
            }
            
            $filepath = SITE_URL ."/fax/". $time . $filename;
            $from = $this->request->data['Contact']['fax_number'];
            $to = $this->request->data['Contact']['to'];
            $quality = $this->request->data['Contact']['quality'];

            if($SID == ''){
                $response = $this->Telnyx->sendfax(TELNYX_KEY, $to, $from, $CALLAPP, $quality, $filepath);
            }else{
                $response = $this->Telnyx->sendfax($SID, $to, $from, $CALLAPP, $quality, $filepath);
            }

            Controller::loadModel('Log');
            $this->Log->create();
            $this->request->data['Log']['user_id'] = $this->Session->read('User.id');
            $this->request->data['Log']['phone_number'] = $to;
            $this->request->data['Log']['route'] = 'outbox';
            $this->request->data['Log']['msg_type'] = 'fax';
            $this->request->data['Log']['sms_status'] = 'sent';
            $this->request->data['Log']['voice_url'] = $filepath;
            
            if(isset($response->errors)){
				$ErrorMessage = $response->errors[0]->detail;
				$this->request->data['Log']['sms_status'] = 'failed';
				$this->request->data['Log']['error_message'] = $ErrorMessage;
				$this->Session->setFlash(__($ErrorMessage, true));
			}else{
			    $this->request->data['Log']['sms_id'] = $response->data->id;
			    $this->Session->setFlash(__('Fax message sent', true));
			}

            $this->Log->save($this->request->data);
            
            //if ($response->status >= 400) {
            //    $this->Session->setFlash(__($response->message, true));
            //} else  {
            //    $this->Session->setFlash(__('Fax message sent', true));
            //}
        } else {
            $this->Session->setFlash(__('Voice/Fax balance is too low.', true));
        }
        
        if($sourcepage == 'contacts'){
           $this->redirect(array('controller' => 'contacts'));
        }else{
           $this->redirect(array('controller' => 'appointments')); 
        }
            
    }
    
    function faxcallstatus()
    {
        $this->autoRender = false;
        
        $out = @file_get_contents('php://input');
        $event_json = json_decode('[' . $out . ']');
        $jsonObject = $event_json[0];
        
        /*ob_start();
		print_r($jsonObject);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/faxcallstatus".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
		
		$faxid = $jsonObject->data->payload->fax_id;
		$status = $jsonObject->data->payload->status;
		$filepath = $jsonObject->data->payload->original_media_url;
        
        app::import('Model', 'Log');
        $this->Log = new Log();
        $this->Log->recursive = -1;
        $someone = $this->Log->find('first', array('conditions' => array('Log.sms_id' => $faxid)));
        
        $logid = $someone['Log']['id'];
        $user_id = $someone['Log']['user_id'];
        
        if (!empty($someone) && $status == "delivered") {
            
            /*$response = $this->faxget($_REQUEST['FaxSid'],$user_id);
            $faxduration = $response->duration;
            
            $minute = gmdate("H:i:s", $faxduration);
            $minute_arr = explode(':', $minute);
            $hour = 0;
            $mint = 0;
            $secnd = 0;
            if ($minute_arr[0] != '00') {
                $hour = $minute_arr[0] * 60;
            }
            if ($minute_arr[1] > 0) {
                $mint = $minute_arr[1];
            }
            if ($minute_arr[2] > 0) {
                $secnd = 1;
            }
            $minutes = $hour + $mint + $secnd;
            //$totalcredits = $_REQUEST['NumPages'] + $minutes;
            $totalcredits = $minutes;*/
            
            $pages = $this->getNumPagesPdf($filepath);
            $totalcredits = $pages;
  
            app::import('Model', 'User');
            $this->User = new User();
            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->User->id = $usersms['User']['id'];
            $this->User->saveField('voice_balance', ($usersms['User']['voice_balance'] - $totalcredits));
            $this->voicebalancemail($usersms['User']['id']);
            
            //$this->request->data['Log']['call_duration'] = gmdate("H:i:s", $faxduration);
            $this->request->data['Log']['inbox_type'] = $pages;
            $this->request->data['Log']['id'] = $logid;
            $this->request->data['Log']['sms_status'] = $status;
            $this->Log->save($this->request->data);
        }elseif($status == "failed"){
            $pages = $this->getNumPagesPdf($filepath);
            $ErrorMessage = $jsonObject->data->payload->failure_reason;
            $this->request->data['Log']['id'] = $logid;
            $this->request->data['Log']['inbox_type'] = $pages;
            $this->request->data['Log']['sms_status'] = $status;
            $this->request->data['Log']['error_message'] = $ErrorMessage;
            $this->Log->save($this->request->data);
        }
        
        header("content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response></Response>";
        exit;
    }
    
    function getNumPagesPdf($filepath) {
        $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
        $max = 0;
        if (!$fp) {
            return "Could not open file: $filepath";
        } else {
            while (!@feof($fp)) {
                $line = @fgets($fp, 255);
                if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                    preg_match('/[0-9]+/', $matches[0], $matches2);
                    if ($max < $matches2[0]) {
                        $max = trim($matches2[0]);
                        break;
                    }
                }
            }
            @fclose($fp);
        }
    
        return $max;
    }
    
    
	function random_generator($digits){
        srand((double)microtime() * 10000000);
        $input = array("A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $random_generator = "";// Initialize the string to store random numbers
        for ($i = 1; $i < $digits + 1; $i++) {
            // Loop the number of times of required digits
            if (rand(1, 2) == 1) {// to decide the digit should be numeric or alphabet
                $rand_index = array_rand($input);
                $random_generator .= $input[$rand_index]; // One char is added
            } else {
                $random_generator .= rand(1, 7); // one number is added

            }
        } // end of for loop
        return $random_generator;
    } // end of function
}
?>