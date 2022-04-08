<?php
App::uses('CakeEmail', 'Network/Email');
class CronjobsController extends AppController
{
    var $components = array('Twilio', 'Mms', 'Nexmomessage', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');
    var $uses = array('User', 'Config', 'Log');
    var $userId = 0;

    function sendmessage()
    {
        $this->autoRender = false;
        $this->send_message();
        sleep(1);
        $this->send_single_message();
        sleep(1);
        $this->send_voicemessage();
        sleep(1);
        $this->updateclicks(1);
    }

    function send_single_message()
    {
        $this->autoRender = false;
        app::import('Model', 'ScheduleMessageGroup');
        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
        app::import('Model', 'ScheduleMessage');
        $this->ScheduleMessage = new ScheduleMessage();
        app::import('Model', 'Log');
        $this->Log = new Log();
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        app::import('Model', 'User');
        $this->User = new User();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        app::import('Model', 'SingleScheduleMessage');
        $this->SingleScheduleMessage = new SingleScheduleMessage();
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        $schedule_arr = $this->ScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.msg_type <' => 3, 'ScheduleMessage.sent' => 0)), array('fields' => array('DISTINCT ScheduleMessage.user_id')));

        foreach ($schedule_arr as $users) {
            $Uid = $users['ScheduleMessage']['user_id'];
            $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $Uid)));
            $API_TYPE = $user_arr['User']['api_type'];
            $SID = trim($user_arr['User']['sid']);
            $AUTHTOKEN = trim($user_arr['User']['authtoken']);
            $BANDWIDTHUSERID = trim($user_arr['User']['bandwidthuserid']);
            $SIGNALWIRESPACE = trim($user_arr['User']['signalwirespace']);
            $active = $user_arr['User']['active'];
            $sendsms = $user_arr['User']['sendsms'];
            if ($Uid != '' && $active == 1 && $sendsms == 1) {
                $timezone = $user_arr['User']['timezone'];
                date_default_timezone_set($timezone);
                $date = date('Y-m-d H:i:s');
                $SingleScheduleMessage = $this->SingleScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.msg_type <' => 3, 'ScheduleMessage.sent' => 0, 'ScheduleMessage.send_on <= ' => $date, 'ScheduleMessage.user_id' => $Uid)));
                if ($API_TYPE == 0) {
                    if (!empty($SingleScheduleMessage)) {
                        $k = 0;
                        foreach ($SingleScheduleMessage as $SingleScheduleMessages) {
                            $contact_id = $SingleScheduleMessages['SingleScheduleMessage']['contact_id'];
                            $user_id = $SingleScheduleMessages['ScheduleMessage']['user_id'];
                            $message = $SingleScheduleMessages['ScheduleMessage']['message'];
                            $systemmsg = $SingleScheduleMessages['ScheduleMessage']['systemmsg'];
                            $mms_text = $SingleScheduleMessages['ScheduleMessage']['mms_text'] . ' ' . $systemmsg;
                            $rotate_number = $SingleScheduleMessages['ScheduleMessage']['rotate_number'];
                            $throttle = $SingleScheduleMessages['ScheduleMessage']['throttle'];
                            $alphasenderid = $SingleScheduleMessages['ScheduleMessage']['alphasender_input'];
                            $sendfrom = $SingleScheduleMessages['ScheduleMessage']['sendfrom'];
                            if (isset($contact_id)) {
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                                $totalSubscriber = count($schedulemessages1);
                                $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if ($totalSubscriber > 0) {
                                    if ($rotate_number == 1) {
                                        $subscriberPhone1 = '';
                                        app::import('Model', 'UserNumber');
                                        $this->UserNumber = new UserNumber();
                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                        $from_arr = array();
                                        if (!empty($users)) {
                                            if ($users['User']['sms'] == 1) {
                                                $from_arr[] = $users['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['sms'] == 1) {
                                                    $from_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                        $mms_arr = array();
                                        if (!empty($users)) {
                                            if ($users['User']['mms'] == 1) {
                                                $mms_arr[] = $users['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['mms'] == 1) {
                                                    $mms_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                        $this->Twilio->curlinit = curl_init();
                                        $this->Twilio->bulksms = 1;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                    $contactcredits = CHARGECREDITSMMS;
                                                } else {
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $message_arr = explode(',', $message);
                                                        $message2 = $message_arr;
                                                        $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                    }
                                                    $body = '';
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        //$random_keys= array_rand($from_arr,1);
                                                        if ($throttle > 1) {
                                                            $countnumber = count($from_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                                sleep($throttle);
                                                            }
                                                        } else {
                                                            $countnumber = count($from_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                            }
                                                        }
                                                        $from = $from_arr[$k];

                                                        $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                        if ($stickyfrom == 0) {
                                                            $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                            $contact['Contact']['stickysender'] = $from;
                                                            $this->Contact->save($contact);
                                                        } else {
                                                            $from = $stickyfrom;
                                                        }

                                                        //$from=$from_arr[$random_keys];
                                                        $body = $message2 . " " . $systemmsg;
                                                        $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);

                                                        if ($SID == ''){
                                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                        }else{
                                                            $this->Twilio->AccountSid = $SID;
                                                            $this->Twilio->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $spinbody = $this->process($body);
                                                        $response = $this->Twilio->sendsms($to, $from, $spinbody);
                                                        $msg_type = 'text';
                                                        $smsid = $response->ResponseXml->Message->Sid;
                                                        $Status = $response->ResponseXml->RestException->Status;
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        //$random_keys= array_rand($mms_arr,1);
                                                        if ($throttle > 1) {
                                                            $countnumber = count($mms_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                                sleep($throttle);
                                                            }
                                                        } else {
                                                            $countnumber = count($mms_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                            }
                                                        }
                                                        $from = $mms_arr[$k];

                                                        $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                        if ($stickyfrom == 0) {
                                                            $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                            $contact['Contact']['stickysender'] = $from;
                                                            $this->Contact->save($contact);
                                                        } else {
                                                            $from = $stickyfrom;
                                                        }

                                                        //$from=$mms_arr[$random_keys];
                                                        $body = $message2;
                                                        if ($SID == ''){
                                                            $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                            $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                        }else{
                                                            $this->Mms->AccountSid = $SID;
                                                            $this->Mms->AuthToken = $AUTHTOKEN;
                                                        }
                                                        
                                                        $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
						                                        
                                                        $spinmms_text = $this->process($mms_textnew);
                                                        $response = $this->Mms->sendmms($to, $from, $body, $spinmms_text);
                                                        $msg_type = 'text';
                                                        $smsid = $response->sid;
                                                        if ($smsid == '') {
                                                            $ErrorMessage = $response;
                                                            $Status = '400';
                                                        }
                                                    }
                                                    $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                    $this->ScheduleMessage->id = $scheduleId;
                                                    $this->request->data['ScheduleMessage']['sent'] = 1;
                                                    $this->ScheduleMessage->save($this->request->data);
                                                    $this->Log = new Log();
                                                    $logArr_single['sms_id'] = (string)$smsid;
                                                    $logArr_single['user_id'] = $users['User']['id'];
                                                    $logArr_single['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $logArr_single['image_url'] = $message;
                                                        $logArr_single['text_message'] = $spinmms_text;
                                                    } else {
                                                        $logArr_single['text_message'] = $spinbody;
                                                    }
                                                    $logArr_single['msg_type'] = $msg_type;
                                                    $logArr_single['route'] = 'outbox';
                                                    $logArr_single['sms_status'] = '';
                                                    $logArr_single['error_message'] = '';

                                                    if ($Status == 400) {
                                                        $logArr_single['sms_status'] = 'failed';
                                                        if (isset($response->ErrorMessage)) {
                                                            $ErrorMessage = $response->ErrorMessage;
                                                        } else {
                                                            $ErrorMessage = $ErrorMessage;
                                                        }
                                                        $logArr_single['error_message'] = $ErrorMessage;
                                                    }
                                                    $this->Log->save($logArr_single);
                                                    $k = $k + 1;
                                                }

                                            //}

                                        }
                                        curl_close($this->Twilio->curlinit);

                                    } else {
                                        $subscriberPhone1 = '';
                                        if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
                                            if ($alphasenderid != '') {
                                                $assigned_number = $alphasenderid;
                                            } elseif ($sendfrom != '') {
                                                $assigned_number = $sendfrom;
                                            } else {
                                                if (empty($usernumber)) {
                                                    $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                                                    if (!empty($mmsnumber)) {
                                                        $assigned_number = $mmsnumber['UserNumber']['number'];
                                                    } else {
                                                        $assigned_number = $users['User']['assigned_number'];
                                                    }
                                                } else {
                                                    $assigned_number = $usernumber['User']['assigned_number'];
                                                }
                                            }
                                        } else {
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

                                            if ($alphasenderid != '') {
                                                $assigned_number = $alphasenderid;
                                            } elseif ($sendfrom != '') {
                                                $assigned_number = $sendfrom;
                                            } else {
                                                if (!empty($usernumber)) {
                                                    if ($usernumber['User']['sms'] == 1) {
                                                        $assigned_number = $usernumber['User']['assigned_number'];
                                                    } else {
                                                        $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                        if (!empty($user_numbers)) {
                                                            $assigned_number = $user_numbers['UserNumber']['number'];
                                                        } else {
                                                            $assigned_number = $usernumber['User']['assigned_number'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $body = '';
                                        $this->Twilio->curlinit = curl_init();
                                        $this->Twilio->bulksms = 1;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                    $contactcredits = CHARGECREDITSMMS;
                                                } else {
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $from = $assigned_number;
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $message_arr = explode(',', $message);
                                                        $message2 = $message_arr;
                                                        $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                    }
                                                    $body = $message2;
                                                    $smsid = '';
                                                    if ($throttle > 1) {
                                                        sleep($throttle);
                                                    }
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        $body = $message2 . " " . $systemmsg;
                                                        $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
						                                
                                                        if ($SID == ''){
                                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                        }else{
                                                            $this->Twilio->AccountSid = $SID;
                                                            $this->Twilio->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $spinbody = $this->process($body);
                                                        $response = $this->Twilio->sendsms($to, $from, $spinbody);
                                                        $msg_type = 'text';
                                                        $smsid = $response->ResponseXml->Message->Sid;
                                                        $Status = $response->ResponseXml->RestException->Status;

                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $body = $message2;
                                                        if ($SID == ''){
                                                            $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                            $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                        }else{
                                                            $this->Mms->AccountSid = $SID;
                                                            $this->Mms->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
						                                        
                                                        $spinmms_text = $this->process($mms_textnew);
                                                        $response = $this->Mms->sendmms($to, $from, $body, $spinmms_text);
                                                        $msg_type = 'text';
                                                        $smsid = $response->sid;
                                                        if ($smsid == '') {
                                                            $ErrorMessage = $response;
                                                            $Status = '400';
                                                        }

                                                    }
                                                    $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                    $this->ScheduleMessage->id = $scheduleId;
                                                    $this->request->data['ScheduleMessage']['sent'] = 1;
                                                    $this->ScheduleMessage->save($this->request->data);
                                                    $this->Log = new Log();
                                                    $logArr_single['sms_id'] = (string)$smsid;
                                                    $logArr_single['user_id'] = $users['User']['id'];
                                                    $logArr_single['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $logArr_single['image_url'] = $message;
                                                        $logArr_single['text_message'] = $spinmms_text;
                                                    } else {
                                                        $logArr_single['text_message'] = $spinbody;
                                                    }
                                                    $logArr_single['msg_type'] = $msg_type;
                                                    $logArr_single['route'] = 'outbox';
                                                    $logArr_single['sms_status'] = '';
                                                    $logArr_single['error_message'] = '';
                                                    if ($Status == 400) {
                                                        $logArr_single['sms_status'] = 'failed';
                                                        if (isset($response->ErrorMessage)) {
                                                            $ErrorMessage = $response->ErrorMessage;
                                                        } else {
                                                            $ErrorMessage = $ErrorMessage;
                                                        }
                                                        $logArr_single['error_message'] = $ErrorMessage;

                                                    }
                                                    $this->Log->save($logArr_single);
                                                }
                                            //}
                                        }
                                        curl_close($this->Twilio->curlinit);
                                    }
                                }
                            }
                        }
                    }
                } else if ($API_TYPE == 2) {
                    if (!empty($SingleScheduleMessage)) {
                        foreach ($SingleScheduleMessage as $SingleScheduleMessages) {
                            $contact_id = $SingleScheduleMessages['SingleScheduleMessage']['contact_id'];
                            $user_id = $SingleScheduleMessages['ScheduleMessage']['user_id'];
                            $message = $SingleScheduleMessages['ScheduleMessage']['message'];
                            $systemmsg = $SingleScheduleMessages['ScheduleMessage']['systemmsg'];
                            $rotate_number = $SingleScheduleMessages['ScheduleMessage']['rotate_number'];
                            if ($rotate_number == 1) {
                                if (isset($contact_id)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                    $totalSubscriber = count($schedulemessages1);
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                    $from_arr = array();
                                    if (!empty($users)) {
                                        if ($users['User']['sms'] == 1) {
                                            $from_arr[] = $users['User']['assigned_number'];
                                        }
                                    }
                                    if (!empty($user_numbers)) {
                                        foreach ($user_numbers as $values) {
                                            if ($values['UserNumber']['sms'] == 1) {
                                                $from_arr[] = $values['UserNumber']['number'];
                                            }
                                        }
                                    }
                                    if ($totalSubscriber > 0) {
                                        $k = 0;
                                        $sucesscredits = 0;
                                        $credits = 0;
                                        $this->Slooce->curlinit = curl_init();
                                        $this->Slooce->bulksms = 1;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                if ($sms_balance > $totalSubscriber) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $countnumber = count($from_arr);
                                                    if ($countnumber == $k) {
                                                        $k = 0;
                                                    }
                                                    $from = $from_arr[$k];
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $message2 . " " . $systemmsg;

                                                    $spinbody = $this->process($body);
                                                    $response = $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $spinbody);
                                                    $message_id = '';
                                                    $status = '';
                                                    if (isset($response['id'])) {
                                                        if ($response['result'] == 'ok') {
                                                            $message_id = $response['id'];
                                                        }
                                                        $status = $response['result'];
                                                    }
                                                    if (!empty($SingleScheduleMessages['ScheduleMessage']['id'])) {
                                                        $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                        $ScheduleMessage_save_arr['ScheduleMessage']['id'] = $scheduleId;
                                                        $ScheduleMessage_save_arr['ScheduleMessage']['sent'] = 1;
                                                        $this->ScheduleMessage->save($ScheduleMessage_save_arr);
                                                    }
                                                    $this->Log = new Log();
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    $logArr['text_message'] = $spinbody;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($status != 'ok') {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $status;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $sucesscredits = $sucesscredits + 1;
                                                        $length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        $credits = $credits + ceil($length / 160);
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                }
                                            //}
                                            $k = $k + 1;
                                        }
                                        curl_close($this->Slooce->curlinit);
                                        if ($sucesscredits > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_credit['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_credit['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_credit);
                                            }
                                        }
                                        $this->smsmail($user_id);
                                    }
                                }
                            } else {
                                if (isset($contact_id)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                    $totalSubscriber = count($schedulemessages1);
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    if (!empty($users)) {
                                        if ($users['User']['sms'] == 1) {
                                            $assigned_number = $users['User']['assigned_number'];
                                        } else {
                                            $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                            if (!empty($user_numbers)) {
                                                $assigned_number = $user_numbers['UserNumber']['number'];
                                            } else {
                                                $assigned_number = $users['User']['assigned_number'];
                                            }
                                        }
                                    }
                                    if ($totalSubscriber > 0) {
                                        $sucesscredits = 0;
                                        $credits = 0;
                                        $this->Slooce->curlinit = curl_init();
                                        $this->Slooce->bulksms = 1;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                if ($sms_balance > $totalSubscriber) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $from = $assigned_number;
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $message2 . " " . $systemmsg;
                                                    $spinbody = $this->process($body);
                                                    $response = $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $spinbody);
                                                    $message_id = '';
                                                    $status = '';
                                                    if (isset($response['id'])) {
                                                        if ($response['result'] == 'ok') {
                                                            $message_id = $response['id'];
                                                        }
                                                        $status = $response['result'];
                                                    }
                                                    if (!empty($SingleScheduleMessages['ScheduleMessage']['id'])) {
                                                        $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                        $ScheduleMessage_arra['ScheduleMessage']['id'] = $scheduleId;
                                                        $ScheduleMessage_arra['ScheduleMessage']['sent'] = 1;
                                                        $this->ScheduleMessage->save($ScheduleMessage_arra);
                                                    }
                                                    $this->Log = new Log();
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    $logArr['text_message'] = $spinbody;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($status != 'ok') {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $status;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $sucesscredits = $sucesscredits + 1;
                                                        $length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        $credits = $credits + ceil($length / 160);
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                }
                                            //}
                                        }
                                        curl_close($this->Slooce->curlinit);
                                        if ($sucesscredits > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_update['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_update['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_update);
                                            }
                                        }
                                        $this->smsmail($user_id);

                                    }
                                }
                            }
                        }
                    }
                } else if ($API_TYPE == 333) { //****** PLIVO moved down below with the other MMS-enabled gateways ***********
                    if (!empty($SingleScheduleMessage)) {
                        foreach ($SingleScheduleMessage as $SingleScheduleMessages) {
                            $contact_id = $SingleScheduleMessages['SingleScheduleMessage']['contact_id'];
                            $user_id = $SingleScheduleMessages['ScheduleMessage']['user_id'];
                            $message = $SingleScheduleMessages['ScheduleMessage']['message'];
                            $systemmsg = $SingleScheduleMessages['ScheduleMessage']['systemmsg'];
                            $rotate_number = $SingleScheduleMessages['ScheduleMessage']['rotate_number'];
                            $throttle = $SingleScheduleMessages['ScheduleMessage']['throttle'];
                            $alphasenderid = $SingleScheduleMessages['ScheduleMessage']['alphasender_input'];
                            $sendfrom = $SingleScheduleMessages['ScheduleMessage']['sendfrom'];
                            if ($rotate_number == 1) {
                                if (isset($contact_id)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                    $totalSubscriber = count($schedulemessages1);
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                    $from_arr = array();
                                    if (!empty($users)) {
                                        if ($users['User']['sms'] == 1) {
                                            $from_arr[] = $users['User']['assigned_number'];
                                        }
                                    }
                                    if (!empty($user_numbers)) {
                                        foreach ($user_numbers as $values) {
                                            if ($values['UserNumber']['sms'] == 1) {
                                                $from_arr[] = $values['UserNumber']['number'];
                                            }
                                        }
                                    }
                                    if ($totalSubscriber > 0) {
                                        $k = 0;
                                        $sucesscredits = 0;
                                        $credits = 0;
                                        $this->Plivo->curlinit = curl_init();
                                        $this->Plivo->bulksms = 1;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                    $contactcredits = ceil($length / 70);
                                                } else {
                                                    $contactcredits = ceil($length / 160);
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    /*$countnumber = count($from_arr);
													if($countnumber==$k){
														$k = 0;
														sleep($throttle);
													}*/
                                                    if ($throttle > 1) {
                                                        $countnumber = count($from_arr);
                                                        if ($countnumber == $k) {
                                                            $k = 0;
                                                            sleep($throttle);
                                                        }
                                                    } else {
                                                        $countnumber = count($from_arr);
                                                        if ($countnumber == $k) {
                                                            $k = 0;
                                                        }
                                                    }

                                                    $from = $from_arr[$k];

                                                    $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                    if ($stickyfrom == 0) {
                                                        $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                        $contact['Contact']['stickysender'] = $from;
                                                        $this->Contact->save($contact);
                                                    } else {
                                                        $from = $stickyfrom;
                                                    }

                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $message2 . " " . $systemmsg;
                                                    if ($SID == ''){
                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                    }else{
                                                        $this->Plivo->AuthId = $SID;
                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $spinbody = $this->process($body);
                                                    $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                    $errortext = '';
                                                    $message_id = '';
                                                    if (isset($response['response']['error'])) {
                                                        $errortext = $response['response']['error'];
                                                    }
                                                    if (isset($response['response']['message_uuid'][0])) {
                                                        $message_id = $response['response']['message_uuid'][0];
                                                    }

                                                    if (!empty($SingleScheduleMessages['ScheduleMessage']['id'])) {
                                                        $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                        $ScheduleMessage_save_arr['ScheduleMessage']['id'] = $scheduleId;
                                                        $ScheduleMessage_save_arr['ScheduleMessage']['sent'] = 1;
                                                        $this->ScheduleMessage->save($ScheduleMessage_save_arr);
                                                    }
                                                    $this->Log = new Log();
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    $logArr['text_message'] = $spinbody;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if (isset($response['response']['error'])) {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $errortext;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $sucesscredits = $sucesscredits + 1;
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        /*$length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if(!$gsm){
                                                            $credits = $credits + ceil($length / 70);
                                                        } else {
                                                            $credits = $credits + ceil($length / 160);
                                                        }*/
                                                        $credits = $credits + $contactcredits;
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                }
                                            //}
                                            $k = $k + 1;
                                        }
                                        curl_close($this->Plivo->curlinit);
                                        if ($sucesscredits > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_credit['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_credit['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_credit);
                                            }
                                        }
                                        $this->smsmail($user_id);

                                    }
                                }
                            } else {
                                if (isset($contact_id)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                    $totalSubscriber = count($schedulemessages1);
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    if (!empty($users)) {
                                        if ($alphasenderid != '') {
                                            $assigned_number = $alphasenderid;
                                        } elseif ($sendfrom != '') {
                                            $assigned_number = $sendfrom;
                                        } else {
                                            if ($users['User']['sms'] == 1) {
                                                $assigned_number = $users['User']['assigned_number'];
                                            } else {
                                                $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                if (!empty($user_numbers)) {
                                                    $assigned_number = $user_numbers['UserNumber']['number'];
                                                } else {
                                                    $assigned_number = $users['User']['assigned_number'];
                                                }
                                            }
                                        }
                                    }
                                    if ($totalSubscriber > 0) {
                                        $sucesscredits = 0;
                                        $credits = 0;
                                        $this->Plivo->curlinit = curl_init();
                                        $this->Plivo->bulksms = 1;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                    $contactcredits = ceil($length / 70);
                                                } else {
                                                    $contactcredits = ceil($length / 160);
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $from = $assigned_number;
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $message2 . " " . $systemmsg;
                                                    if ($SID == ''){
                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                    }else{
                                                        $this->Plivo->AuthId = $SID;
                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                    }
                                                    //sleep($throttle);
                                                    if ($throttle > 1) {
                                                        sleep($throttle);
                                                    }
                                                    $spinbody = $this->process($body);
                                                    $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                    $errortext = '';
                                                    $message_id = '';
                                                    if (isset($response['response']['error'])) {
                                                        $errortext = $response['response']['error'];
                                                    }
                                                    if (isset($response['response']['message_uuid'][0])) {
                                                        $message_id = $response['response']['message_uuid'][0];
                                                    }
                                                    if (!empty($SingleScheduleMessages['ScheduleMessage']['id'])) {
                                                        $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                        $ScheduleMessage_arra['ScheduleMessage']['id'] = $scheduleId;
                                                        $ScheduleMessage_arra['ScheduleMessage']['sent'] = 1;
                                                        $this->ScheduleMessage->save($ScheduleMessage_arra);
                                                    }
                                                    $this->Log = new Log();
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    $logArr['text_message'] = $spinbody;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if (isset($response['response']['error'])) {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $errortext;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $sucesscredits = $sucesscredits + 1;
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        /*$length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                            $credits = $credits + ceil($length / 70);
                                                        } else {
                                                            $credits = $credits + ceil($length / 160);
                                                        }*/
                                                        $credits = $credits + $contactcredits;
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                }
                                            //}
                                        }
                                        curl_close($this->Plivo->curlinit);
                                        if ($sucesscredits > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_update['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_update['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_update);
                                            }
                                        }
                                        $this->smsmail($user_id);

                                    }
                                }
                            }
                        }
                    }
				}else if ($API_TYPE == 3 || $API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {
                    if (!empty($SingleScheduleMessage)) {
                        $k = 0;
                        foreach ($SingleScheduleMessage as $SingleScheduleMessages) {
                            $contact_id = $SingleScheduleMessages['SingleScheduleMessage']['contact_id'];
                            $user_id = $SingleScheduleMessages['ScheduleMessage']['user_id'];
                            $message = $SingleScheduleMessages['ScheduleMessage']['message'];
                            $systemmsg = $SingleScheduleMessages['ScheduleMessage']['systemmsg'];
                            $mms_text = $SingleScheduleMessages['ScheduleMessage']['mms_text'] . ' ' . $systemmsg;
                            $rotate_number = $SingleScheduleMessages['ScheduleMessage']['rotate_number'];
                            $throttle = $SingleScheduleMessages['ScheduleMessage']['throttle'];
                            $alphasenderid = $SingleScheduleMessages['ScheduleMessage']['alphasender_input'];
                            $sendfrom = $SingleScheduleMessages['ScheduleMessage']['sendfrom'];
                            if (isset($contact_id)) {
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                                $totalSubscriber = count($schedulemessages1);
                                $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                if ($totalSubscriber > 0) {
                                    if ($rotate_number == 1) {
                                        $subscriberPhone1 = '';
                                        app::import('Model', 'UserNumber');
                                        $this->UserNumber = new UserNumber();
                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                        $from_arr = array();
                                        if (!empty($users)) {
                                            if ($users['User']['sms'] == 1) {
                                                $from_arr[] = $users['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['sms'] == 1) {
                                                    $from_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                        $mms_arr = array();
                                        if (!empty($users)) {
                                            if ($users['User']['mms'] == 1) {
                                                $mms_arr[] = $users['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['mms'] == 1) {
                                                    $mms_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                        $success = 0;
                                        $credits = 0;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                    $contactcredits = CHARGECREDITSMMS;
                                                } else {
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $message_arr = explode(',', $message);
                                                        $message2 = $message_arr;
                                                        $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                    }
                                                    $body = '';
													$message_id = '';
													$status = '';
													$errortext = '';
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        //$random_keys= array_rand($from_arr,1);
                                                        if ($throttle > 1) {
                                                            $countnumber = count($from_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                                sleep($throttle);
                                                            }
                                                        } else {
                                                            $countnumber = count($from_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                            }
                                                        }
                                                        $from = $from_arr[$k];

                                                        $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                        if ($stickyfrom == 0) {
                                                            $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                            $contact['Contact']['stickysender'] = $from;
                                                            $this->Contact->save($contact);
                                                        } else {
                                                            $from = $stickyfrom;
                                                        }
                                                        $body = $message2 . " " . $systemmsg;
                                                        $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
                                                        $spinbody = $this->process($body);
                                                        //$response = $this->Twilio->sendsms($to, $from, $spinbody);
                                                        /*if ($SID == ''){
                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                        }else{
                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                        }

                                                        $msg_type = 'text';
														if(isset($response->message)){
															$errortext = $response->message;
															$status = 'failed';
														}else{
															$status = 'sent';
														}*/
														$message_id = '';
														if($API_TYPE==3){
														    if ($SID == ''){
                                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                            }else{
                                                                $this->Plivo->AuthId = $SID;
                                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                                            }
                                                            $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                            $msg_type = 'text';
                                                    		$status = '';
                                                            $errortext = '';
                                                            if (isset($response['response']['error'])) {
                                                                $errortext = $response['response']['error'];
                                                                $status = 'failed';
                                                            }
                                                            if (isset($response['response']['message_uuid'][0])) {
                                                                $message_id = $response['response']['message_uuid'][0];
                                                                $status = 'sent';
                                                            }
														}elseif($API_TYPE==5){
														    if ($SID == ''){
                        								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinbody);
														    }else{
														        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinbody);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if(isset($response->error_message)){
                                                				$errortext = $response->error_message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->sid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==6){
														    if ($SID == ''){
                        								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinbody);
														    }else{
														        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$spinbody);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if($response->Message360->ResponseStatus == 0){
                                                				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->Message360->Message->MessageSid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==7){
														    if ($SID == ''){
                        								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$spinbody);
														    }else{
														        $response = $this->Telnyx->sendsms($SID,$to,$from,$spinbody);
														    }
														    $msg_type = 'text';
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
                        						                if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                                }
                                                                $msg_type = 'text';
                        					
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
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        //$random_keys= array_rand($mms_arr,1);
                                                        if ($throttle > 1) {
                                                            $countnumber = count($mms_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                                sleep($throttle);
                                                            }
                                                        } else {
                                                            $countnumber = count($mms_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                            }
                                                        }
                                                        $from = $mms_arr[$k];

                                                        $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                        if ($stickyfrom == 0) {
                                                            $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                            $contact['Contact']['stickysender'] = $from;
                                                            $this->Contact->save($contact);
                                                        } else {
                                                            $from = $stickyfrom;
                                                        }
                                                        $body = $message2;
                                                        $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
                                                        $spinmms_text = $this->process($mms_textnew);
                                                        /*if ($SID == ''){
                                                            $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                        }else{
                                                            $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                        }
														
                                                        $msg_type = 'text';
														if(isset($response->message)){
															$errortext = $response->message;
															$status = 'failed';
														}else{
															$status = 'sent';
														}*/
														$message_id = '';
														if($API_TYPE==3){
														    if ($SID == ''){
                                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                            }else{
                                                                $this->Plivo->AuthId = $SID;
                                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                                            }
                                                            $response = $this->Plivo->sendmms($to, $from, $spinmms_text, $body);
                                                            $msg_type = 'text';
                                                    		$status = '';
                                                            $errortext = '';
                                                            if (isset($response['response']['error'])) {
                                                                $errortext = $response['response']['error'];
                                                                $status = 'failed';
                                                            }
                                                            if (isset($response['response']['message_uuid'][0])) {
                                                                $message_id = $response['response']['message_uuid'][0];
                                                                $status = 'sent';
                                                            }
														}elseif($API_TYPE==5){
														    if ($SID == ''){
                        								        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinmms_text,$body);
														    }else{
														        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinmms_text,$body);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if(isset($response->error_message)){
                                                				$errortext = $response->error_message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->sid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==6){
														    if ($SID == ''){
                        								        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinmms_text,$body);
														    }else{
														        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$spinmms_text,$body);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if($response->Message360->ResponseStatus == 0){
                                                				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->Message360->Message->MessageSid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==7){
														    if ($SID == ''){
                        								        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$spinmms_text,$body);
														    }else{
														        $response = $this->Telnyx->sendmms($SID,$to,$from,$spinmms_text,$body);
														    }
														    $msg_type = 'text';
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
                        						                if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                                }
                                                                $msg_type = 'text';
                        					
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
                                                    }
                                                    $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                    $this->ScheduleMessage->id = $scheduleId;
                                                    $this->request->data['ScheduleMessage']['sent'] = 1;
                                                    $this->ScheduleMessage->save($this->request->data);
                                                    $this->Log = new Log();
                                                    $logArr_single['sms_id'] = $message_id;
                                                    $logArr_single['user_id'] = $users['User']['id'];
                                                    $logArr_single['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $logArr_single['image_url'] = $message;
                                                        $logArr_single['text_message'] = $spinmms_text;
                                                        if ($errortext == '') {
                                                            $credits = $credits + CHARGECREDITSMMS;
                                                        }
                                                    } else {
                                                        $logArr_single['text_message'] = $spinbody;
                                                        if ($errortext == '') {
                                                            $credits = $credits + $contactcredits;
                                                        }

                                                    }
                                                    $logArr_single['msg_type'] = $msg_type;
                                                    $logArr_single['route'] = 'outbox';
                                                    $logArr_single['sms_status'] = '';
                                                    $logArr_single['error_message'] = '';
													if ($errortext !='') {
														$logArr_single['sms_status'] = $status;
														$logArr_single['error_message'] = $errortext;
                                                    }else{
                                                        $success = $success + 1;
														$logArr_single['sms_status'] = 'sent';	
													}
                                                    $this->Log->save($logArr_single);
                                                    $k = $k + 1;
                                                }

                                            //}

                                        }
                                        
                                        if ($success > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_credit['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_credit['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_credit);
                                            }
                                        }
                                        $this->smsmail($user_id);
                                    } else {
                                        $subscriberPhone1 = '';
                                        if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
                                            if ($alphasenderid != '') {
                                                $assigned_number = $alphasenderid;
                                            } elseif ($sendfrom != '') {
                                                $assigned_number = $sendfrom;
                                            } else {
                                                if (empty($usernumber)) {
                                                    $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                                                    if (!empty($mmsnumber)) {
                                                        $assigned_number = $mmsnumber['UserNumber']['number'];
                                                    } else {
                                                        $assigned_number = $users['User']['assigned_number'];
                                                    }
                                                } else {
                                                    $assigned_number = $usernumber['User']['assigned_number'];
                                                }
                                            }
                                        } else {
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

                                            if ($alphasenderid != '') {
                                                $assigned_number = $alphasenderid;
                                            } elseif ($sendfrom != '') {
                                                $assigned_number = $sendfrom;
                                            } else {
                                                if (!empty($usernumber)) {
                                                    if ($usernumber['User']['sms'] == 1) {
                                                        $assigned_number = $usernumber['User']['assigned_number'];
                                                    } else {
                                                        $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                        if (!empty($user_numbers)) {
                                                            $assigned_number = $user_numbers['UserNumber']['number'];
                                                        } else {
                                                            $assigned_number = $usernumber['User']['assigned_number'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $body = '';
                                        $success = 0;
                                        $credits = 0;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                    $contactcredits = CHARGECREDITSMMS;
                                                } else {
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $from = $assigned_number;
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $message_arr = explode(',', $message);
                                                        $message2 = $message_arr;
                                                        $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                    }
                                                    $body = $message2;
                                                    $message_id = '';
													$status = '';
													$errortext = '';
                                                    if ($throttle > 1) {
                                                        sleep($throttle);
                                                    }
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 1) {
                                                        $body = $message2 . " " . $systemmsg;
                                                        $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
                                                        $spinbody = $this->process($body);
                                                        /*if ($SID == ''){
                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                        }else{
                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                        }
                                                        $msg_type = 'text';
														if(isset($response->message)){
															$errortext = $response->message;
															$status = 'failed';
														}else{
															$status = 'sent';
														}*/
														$message_id = '';
														if($API_TYPE==3){
														    if ($SID == ''){
                                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                            }else{
                                                                $this->Plivo->AuthId = $SID;
                                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                                            }
                                                            $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                            $msg_type = 'text';
                                                    		$status = '';
                                                            $errortext = '';
                                                            if (isset($response['response']['error'])) {
                                                                $errortext = $response['response']['error'];
                                                                $status = 'failed';
                                                            }
                                                            if (isset($response['response']['message_uuid'][0])) {
                                                                $message_id = $response['response']['message_uuid'][0];
                                                                $status = 'sent';
                                                            }
														}elseif($API_TYPE==5){
														    if ($SID == ''){
                        								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinbody);
														    }else{
														        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinbody);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if(isset($response->error_message)){
                                                				$errortext = $response->error_message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->sid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==6){
														    if ($SID == ''){
                        								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinbody);
														    }else{
														        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$spinbody);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if($response->Message360->ResponseStatus == 0){
                                                				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->Message360->Message->MessageSid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==7){
														    if ($SID == ''){
                        								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$spinbody);
														    }else{
														        $response = $this->Telnyx->sendsms($SID,$to,$from,$spinbody);
														    }
														    $msg_type = 'text';
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
                        						                if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                                }
                                                                $msg_type = 'text';
                        					
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
                                                    } else if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
														$body = $message2;
														$mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
														$spinmms_text = $this->process($mms_textnew);
														/*if ($SID == ''){
                                                            $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                        }else{
                                                            $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                        }
                                                        $msg_type = 'text';
														if(isset($response->message)){
															$errortext = $response->message;
															$status = 'failed';
														}else{
															$status = 'sent';
														}*/
														$message_id = '';
														if($API_TYPE==3){
														    if ($SID == ''){
                                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                            }else{
                                                                $this->Plivo->AuthId = $SID;
                                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                                            }
                                                            $response = $this->Plivo->sendmms($to, $from, $spinmms_text,$body);
                                                            $msg_type = 'text';
                                                    		$status = '';
                                                            $errortext = '';
                                                            if (isset($response['response']['error'])) {
                                                                $errortext = $response['response']['error'];
                                                                $status = 'failed';
                                                            }
                                                            if (isset($response['response']['message_uuid'][0])) {
                                                                $message_id = $response['response']['message_uuid'][0];
                                                                $status = 'sent';
                                                            }
														}elseif($API_TYPE==5){
														    if ($SID == ''){
                        								        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinmms_text,$body);
														    }else{
														        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinmms_text,$body);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if(isset($response->error_message)){
                                                				$errortext = $response->error_message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->sid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==6){
														    if ($SID == ''){
                        								        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinmms_text,$body);
														    }else{
														        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$spinmms_text,$body);
														    }
														    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if($response->Message360->ResponseStatus == 0){
                                                				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->Message360->Message->MessageSid;
                                                				$status = 'sent';
                                                			}
														}elseif($API_TYPE==7){
														    if ($SID == ''){
                        								        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$spinmms_text,$body);
														    }else{
														        $response = $this->Telnyx->sendmms($SID,$to,$from,$spinmms_text,$body);
														    }
														    $msg_type = 'text';
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
                        						                if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                                }
                                                                $msg_type = 'text';
                        					
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
                                                    }
                                                    $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                    $this->ScheduleMessage->id = $scheduleId;
                                                    $this->request->data['ScheduleMessage']['sent'] = 1;
                                                    $this->ScheduleMessage->save($this->request->data);
                                                    $this->Log = new Log();
                                                    $logArr_single['sms_id'] = $message_id;
                                                    $logArr_single['user_id'] = $users['User']['id'];
                                                    $logArr_single['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    if ($SingleScheduleMessages['ScheduleMessage']['msg_type'] == 2) {
                                                        $logArr_single['image_url'] = $message;
                                                        $logArr_single['text_message'] = $spinmms_text;
                                                        if ($errortext == '') {
                                                            $credits = $credits + CHARGECREDITSMMS;
                                                        }
                                                    } else {
                                                        $logArr_single['text_message'] = $spinbody;
                                                        if ($errortext == '') {
                                                           $credits = $credits + $contactcredits;
                                                        }

                                                    }
                                                    $logArr_single['msg_type'] = $msg_type;
                                                    $logArr_single['route'] = 'outbox';
                                                    $logArr_single['sms_status'] = '';
                                                    $logArr_single['error_message'] = '';
													if ($errortext !='') {
                                                        $logArr_single['sms_status'] = $status;
                                                        $logArr_single['error_message'] = $errortext;
                                                    }else{
														$logArr_single['sms_status'] = 'sent';	
														$success = $success + 1;
													}
                                                    $this->Log->save($logArr_single);
                                                }
                                        }
                                        if ($success > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_credit['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_credit['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_credit);
                                            }
                                        }
                                        $this->smsmail($user_id);
                                        
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (!empty($SingleScheduleMessage)) {
                        foreach ($SingleScheduleMessage as $SingleScheduleMessages) {
                            $contact_id = $SingleScheduleMessages['SingleScheduleMessage']['contact_id'];
                            $user_id = $SingleScheduleMessages['ScheduleMessage']['user_id'];
                            $message = $SingleScheduleMessages['ScheduleMessage']['message'];
                            $systemmsg = $SingleScheduleMessages['ScheduleMessage']['systemmsg'];
                            $rotate_number = $SingleScheduleMessages['ScheduleMessage']['rotate_number'];
                            $throttle = $SingleScheduleMessages['ScheduleMessage']['throttle'];
                            $alphasenderid = $SingleScheduleMessages['ScheduleMessage']['alphasender_input'];
                            $sendfrom = $SingleScheduleMessages['ScheduleMessage']['sendfrom'];
                            if ($rotate_number == 1) {
                                if (isset($contact_id)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                                    $totalSubscriber = count($schedulemessages1);
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    app::import('Model', 'UserNumber');
                                    $this->UserNumber = new UserNumber();
                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                    $from_arr = array();
                                    if (!empty($users)) {
                                        if ($users['User']['sms'] == 1) {
                                            $from_arr[] = $users['User']['assigned_number'];
                                        }
                                    }
                                    if (!empty($user_numbers)) {
                                        foreach ($user_numbers as $values) {
                                            if ($values['UserNumber']['sms'] == 1) {
                                                $from_arr[] = $values['UserNumber']['number'];
                                            }
                                        }
                                    }
                                    if ($totalSubscriber > 0) {
                                        $k = 0;
                                        $sucesscredits = 0;
                                        $credits = 0;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                    $contactcredits = ceil($length / 70);
                                                } else {
                                                    $contactcredits = ceil($length / 160);
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $countnumber = count($from_arr);
                                                    if ($countnumber == $k) {
                                                        $k = 0;
                                                        sleep($throttle);
                                                    }
                                                    $from = $from_arr[$k];

                                                    $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                    if ($stickyfrom == 0) {
                                                        $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                        $contact['Contact']['stickysender'] = $from;
                                                        $this->Contact->save($contact);
                                                    } else {
                                                        $from = $stickyfrom;
                                                    }

                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $message2 . " " . $systemmsg;
                                                    $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                            $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                            $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                            $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                            $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                            $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                            $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
                                                    if ($SID == ''){
                                                        $this->Nexmomessage->Key = NEXMO_KEY;
                                                        $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                    }else{
                                                        $this->Nexmomessage->Key = $SID;
                                                        $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                    }
                                                    $spinbody = $this->process($body);
                                                    $response = $this->Nexmomessage->sendsms($to, $from, $spinbody);
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

                                                    if (!empty($SingleScheduleMessages['ScheduleMessage']['id'])) {
                                                        $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                        $ScheduleMessage_save_arr['ScheduleMessage']['id'] = $scheduleId;
                                                        $ScheduleMessage_save_arr['ScheduleMessage']['sent'] = 1;
                                                        $this->ScheduleMessage->save($ScheduleMessage_save_arr);
                                                    }
                                                    $this->Log = new Log();
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    $logArr['text_message'] = $spinbody;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($status != 0) {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $errortext;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $sucesscredits = $sucesscredits + 1;
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        /*$length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        //$credits = $credits + ceil($length/160);
                                                        if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                            $credits = $credits + ceil($length / 70);
                                                        } else {
                                                            $credits = $credits + ceil($length / 160);
                                                        }*/
                                                        $credits = $credits + $contactcredits;
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                }
                                            //}
                                            $k = $k + 1;
                                        }
                                        if ($sucesscredits > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                //$user_credit['User']['sms_balance']=$usersms['User']['sms_balance']-$sucesscredits;
                                                //$length = strlen(utf8_decode(substr($body,0,1600)));
                                                //$credits = ceil($length/160);
                                                $user_credit['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_credit['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_credit);
                                            }
                                        }
                                        $this->smsmail($user_id);

                                    }
                                }
                            } else {
                                if (isset($contact_id)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.un_subscribers' => 0),'fields' => array('DISTINCT Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                                    $totalSubscriber = count($schedulemessages1);
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    if (!empty($users)) {
                                        if ($alphasenderid != '') {
                                            $assigned_number = $alphasenderid;
                                        } elseif ($sendfrom != '') {
                                            $assigned_number = $sendfrom;
                                        } else {
                                            if ($users['User']['sms'] == 1) {
                                                $assigned_number = $users['User']['assigned_number'];
                                            } else {
                                                $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                if (!empty($user_numbers)) {
                                                    $assigned_number = $user_numbers['UserNumber']['number'];
                                                } else {
                                                    $assigned_number = $users['User']['assigned_number'];
                                                }
                                            }
                                        }
                                    }
                                    if ($totalSubscriber > 0) {
                                        $sucesscredits = 0;
                                        $credits = 0;
                                        foreach ($schedulemessages1 as $schedulemessages) {
                                            //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                $sms_balance = $users['User']['sms_balance'];
                                                $body = $message . " " . $systemmsg;
                                                $spinbody = $this->process($body);
                                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                $length = mb_strlen($spinbody,"UTF-8");
			                                    $gsm = $this->is_gsm($spinbody);
                                                if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                    $contactcredits = ceil($length / 70);
                                                } else {
                                                    $contactcredits = ceil($length / 160);
                                                }
                                                if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                    //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                    $to = $schedulemessages['Contact']['phone_number'];
                                                    $from = $assigned_number;
                                                    $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $schedulemessages['Contact']['name'];
                                                    }
                                                    $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $message2 . " " . $systemmsg;
                                                    $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                            $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                            $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                            $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                            $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                            $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                            $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
						                            
                                                    if ($SID == ''){
                                                        $this->Nexmomessage->Key = NEXMO_KEY;
                                                        $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                    }else{
                                                        $this->Nexmomessage->Key = $SID;
                                                        $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                    }
                                                    sleep($throttle);
                                                    $spinbody = $this->process($body);
                                                    $response = $this->Nexmomessage->sendsms($to, $from, $spinbody);
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
                                                    if (!empty($SingleScheduleMessages['ScheduleMessage']['id'])) {
                                                        $scheduleId = $SingleScheduleMessages['ScheduleMessage']['id'];
                                                        $ScheduleMessage_arra['ScheduleMessage']['id'] = $scheduleId;
                                                        $ScheduleMessage_arra['ScheduleMessage']['sent'] = 1;
                                                        $this->ScheduleMessage->save($ScheduleMessage_arra);
                                                    }
                                                    $this->Log = new Log();
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                    $logArr['text_message'] = $spinbody;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($status != 0) {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $errortext;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $sucesscredits = $sucesscredits + 1;
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        /*$length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                            $credits = $credits + ceil($length / 70);
                                                        } else {
                                                            $credits = $credits + ceil($length / 160);
                                                        }*/
                                                        $credits = $credits + $contactcredits;
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                }
                                            //}
                                        }
                                        if ($sucesscredits > 0) {
                                            app::import('Model', 'User');
                                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usersms)) {
                                                $user_update['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $user_update['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($user_update);
                                            }
                                        }
                                        $this->smsmail($user_id);

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function send_message()
    {
        $this->autoRender = false;
        ob_start();
        app::import('Model', 'ScheduleMessageGroup');
        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
        app::import('Model', 'ScheduleMessage');
        $this->ScheduleMessage = new ScheduleMessage();
        app::import('Model', 'Log');
        $this->Log = new Log();
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        app::import('Model', 'User');
        $this->User = new User();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        app::import('Model', 'SingleScheduleMessage');
        $this->SingleScheduleMessage = new SingleScheduleMessage();
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        $schedule_arr = $this->ScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.msg_type <' => 3, 'ScheduleMessage.sent' => 0)), array('fields' => array('DISTINCT ScheduleMessage.user_id')));

        foreach ($schedule_arr as $users) {
            $Uid = $users['ScheduleMessage']['user_id'];
            $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $Uid)));
            $API_TYPE = $user_arr['User']['api_type'];
            $SID = trim($user_arr['User']['sid']);
            $AUTHTOKEN = trim($user_arr['User']['authtoken']);
            $BANDWIDTHUSERID = trim($user_arr['User']['bandwidthuserid']);
            $SIGNALWIRESPACE = trim($user_arr['User']['signalwirespace']);
            $active = $user_arr['User']['active'];
            $sendsms = $user_arr['User']['sendsms'];
            $numhold=0;
            $subscriberPhone1 = array();
            
            if ($Uid != '' && $active == 1 && $sendsms == 1) {
                $timezone = $user_arr['User']['timezone'];
                date_default_timezone_set($timezone);
                $date = date('Y-m-d H:i:s');
                //echo "<br>";
                $schedulemessages = $this->ScheduleMessageGroup->find('all', array('conditions' => array('ScheduleMessage.msg_type <' => 3, 'ScheduleMessage.sent' => 0, 'ScheduleMessage.send_on <= ' => $date, 'ScheduleMessage.user_id' => $Uid)));
                if (!empty($schedulemessages)) {
                    foreach ($schedulemessages as $schedulemessage) {
                        $count = 0;
                        $group_id = $schedulemessage['Group']['id'];
                        $message = $schedulemessage['ScheduleMessage']['message'];
                        $systemmsg = $schedulemessage['ScheduleMessage']['systemmsg'];
                        $mms_text = $schedulemessage['ScheduleMessage']['mms_text'] . ' ' . $systemmsg;
                        $sent_on = $schedulemessage['ScheduleMessage']['send_on'];
                        $user_id = $schedulemessage['ScheduleMessage']['user_id'];
                        $rotate = $schedulemessage['ScheduleMessage']['rotate_number'];
                        $throttle = $schedulemessage['ScheduleMessage']['throttle'];
                        $alphasenderid = $schedulemessage['ScheduleMessage']['alphasender_input'];
                        $sendfrom = $schedulemessage['ScheduleMessage']['sendfrom'];
                        $city = $schedulemessage['ScheduleMessage']['city'];
                        $state = $schedulemessage['ScheduleMessage']['state'];
                        $zip = $schedulemessage['ScheduleMessage']['zip'];

                        if (isset($group_id)) {
                            $conditions = array();
                            $conditions[] = array( "AND" => array('ContactGroup.group_id' => $group_id, 'ContactGroup.un_subscribers' => 0) );
                            
                            if(trim($city)!=''){
                                $conditions[] = array( "AND" => array ("Contact.location"=>$city) );
                            }
                            if(trim($state)!=''){
                                $conditions[] = array( "AND" => array ("Contact.state"=>$state) );
                            }
                            if(trim($zip)!=''){
                                $conditions[] = array( "AND" => array ("Contact.zip"=>$zip) );
                            }

                            //$schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_id, 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                            $schedulemessages1 = $this->ContactGroup->find('all', array('conditions' => $conditions, 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
   
                            if (!empty($schedulemessages1)) {
                                $totalSubscriber = count($schedulemessages1);
                                if ($totalSubscriber > 0) {
                                    $scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                    $this->ScheduleMessage->id = $scheduleId;
                                    $this->request->data['ScheduleMessage']['sent'] = 1;
                                    $this->ScheduleMessage->save($this->request->data);
                                                            
                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    
                                    if ($rotate == 1) {
                                        app::import('Model', 'UserNumber');
                                        $this->UserNumber = new UserNumber();
                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                        $from_arr = array();
                                        if (!empty($users)) {
                                            if ($users['User']['sms'] == 1) {
                                                $from_arr[] = $users['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['sms'] == 1) {
                                                    $from_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                        $mms_arr = array();
                                        if (!empty($users)) {
                                            if ($users['User']['mms'] == 1) {
                                                $mms_arr[] = $users['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['mms'] == 1) {
                                                    $mms_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                    }
                                                            
                                    if ($API_TYPE == 0) {
                                        if ($rotate == 1) {
                                            if ($users['User']['sms_balance'] > $totalSubscriber) {
                                                //$subscriberPhone1 = '';
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                                $k = 0;
                                                $this->Twilio->curlinit = curl_init();
                                                $this->Twilio->bulksms = 1;
                                                foreach ($schedulemessages1 as $schedulemessages) {
                                                    //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                    if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                        $sms_balance = $users['User']['sms_balance'];
                                                        $body = $message . " " . $systemmsg;
                                                        $spinbody = $this->process($body);
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        $length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                            $contactcredits = CHARGECREDITSMMS;
                                                        } else {
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $contactcredits = ceil($length / 70);
                                                            } else {
                                                                $contactcredits = ceil($length / 160);
                                                            }
                                                        }
                                                        if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                            //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                            $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                            $to = $schedulemessages['Contact']['phone_number'];
                                                            /*app::import('Model', 'UserNumber');
                                                            $this->UserNumber = new UserNumber();
                                                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                                            $from_arr = array();
                                                            if (!empty($users)) {
                                                                if ($users['User']['sms'] == 1) {
                                                                    $from_arr[] = $users['User']['assigned_number'];
                                                                }
                                                            }
                                                            if (!empty($user_numbers)) {
                                                                foreach ($user_numbers as $values) {
                                                                    if ($values['UserNumber']['sms'] == 1) {
                                                                        $from_arr[] = $values['UserNumber']['number'];
                                                                    }
                                                                }
                                                            }
                                                            $mms_arr = array();
                                                            if (!empty($users)) {
                                                                if ($users['User']['mms'] == 1) {
                                                                    $mms_arr[] = $users['User']['assigned_number'];
                                                                }
                                                            }
                                                            if (!empty($user_numbers)) {
                                                                foreach ($user_numbers as $values) {
                                                                    if ($values['UserNumber']['mms'] == 1) {
                                                                        $mms_arr[] = $values['UserNumber']['number'];
                                                                    }
                                                                }
                                                            }*/
                                                            $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                            if ($space_pos != '') {
                                                                $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                            } else {
                                                                $contact_name = $schedulemessages['Contact']['name'];
                                                            }
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $message_arr = explode(',', $message);
                                                                $message2 = $message_arr;
                                                                $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                            }
                                                            $Status = '';
                                                            $smsid = '';
                                                            $body = '';
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                //$random_keys= array_rand($from_arr,1);
                                                                if ($throttle > 1) {
                                                                    $countnumber = count($from_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                        sleep($throttle);
                                                                    }
                                                                } else {
                                                                    $countnumber = count($from_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                    }
                                                                }
                                                                $from = $from_arr[$k];

                                                                $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }
                                                                //$from=$from_arr[$random_keys];
                                                                $body = $message2 . " " . $systemmsg;
                                                                
                                                                $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                        $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                        $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                        $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                        $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                        $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                        $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
						                                        
                                                                if ($SID == ''){
                                                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                                }else{
                                                                    $this->Twilio->AccountSid = $SID;
                                                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                                                }
                                                                $spinbody = $this->process($body);
                                                                $response = $this->Twilio->sendsms($to, $from, $spinbody);
                                                                $msg_type = 'text';
                                                                $smsid = $response->ResponseXml->Message->Sid;
                                                                $Status = $response->ResponseXml->RestException->Status;
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                if ($throttle > 1) {
                                                                    $countnumber = count($mms_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                        sleep($throttle);
                                                                    }
                                                                } else {
                                                                    $countnumber = count($mms_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                    }
                                                                }
                                                                $from = $mms_arr[$k];

                                                                $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }
                                                                //$from=$mms_arr[$random_keys];
                                                                $body = $message2;
                                                                if ($SID == ''){
                                                                    $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                                    $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                                }else{
                                                                    $this->Mms->AccountSid = $SID;
                                                                    $this->Mms->AuthToken = $AUTHTOKEN;
                                                                }
                                                                
                                                                $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
						                                        
                                                                $spinmms_text = $this->process($mms_textnew);
                                                                $response = $this->Mms->sendmms($to, $from, $body, $spinmms_text);
                                                                $msg_type = 'text';
                                                                $smsid = $response->sid;
                                                                if ($smsid == '') {
                                                                    $ErrorMessage = $response;
                                                                    $Status = 400;
                                                                }
                                                            }
                                                            //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                            //$this->ScheduleMessage->id = $scheduleId;
                                                            //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                            //$this->ScheduleMessage->save($this->request->data);
                                                            $this->Log = new Log();
                                                            $logArr['group_sms_id'] = $group_sms_id;
                                                            $logArr['sms_id'] = $smsid;
                                                            $logArr['group_id'] = $group_id;
                                                            $logArr['user_id'] = $users['User']['id'];
                                                            $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                            $logArr['sendfrom'] = $from;
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $logArr['image_url'] = $message;
                                                                $logArr['text_message'] = $spinmms_text;
                                                            } else {
                                                                $logArr['text_message'] = $spinbody;
                                                            }
                                                            $logArr['msg_type'] = $msg_type;
                                                            $logArr['route'] = 'outbox';
                                                            $logArr['sms_status'] = '';
                                                            $logArr['error_message'] = '';
                                                            if ($Status == 400) {
                                                                $logArr['sms_status'] = 'failed';
                                                                if (isset($response->ErrorMessage)) {
                                                                    $ErrorMessage = $response->ErrorMessage;
                                                                } else {
                                                                    $ErrorMessage = $ErrorMessage;
                                                                }
                                                                $logArr['error_message'] = $ErrorMessage;
                                                            }
                                                            $this->Log->save($logArr);
                                                            if ($Status == 400) {
                                                                $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                                $GroupSmsBlast['GroupSmsBlast']['id'] = $group_sms_id;
                                                                $GroupSmsBlast['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                $this->GroupSmsBlast->save($GroupSmsBlast);
                                                            }

                                                        }
                                                    }
                                                    $k = $k + 1;
                                                    $numhold++;
                                                }
                                                curl_close($this->Twilio->curlinit);
                                            }
                                        } else {
                                            if ($users['User']['sms_balance'] > $totalSubscriber) {
                                                //$subscriberPhone1 = '';
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                                if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                    $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
                                                    if ($alphasenderid != '') {
                                                        $assigned_number = $alphasenderid;
                                                    } elseif ($sendfrom != '') {
                                                        $assigned_number = $sendfrom;
                                                    } else {
                                                        if (empty($usernumber)) {
                                                            $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                                                            if (!empty($mmsnumber)) {
                                                                $assigned_number = $mmsnumber['UserNumber']['number'];
                                                            } else {
                                                                $assigned_number = $users['User']['assigned_number'];
                                                            }
                                                        } else {
                                                            $assigned_number = $usernumber['User']['assigned_number'];
                                                        }
                                                    }
                                                } else {
                                                    $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if ($alphasenderid != '') {
                                                        $assigned_number = $alphasenderid;
                                                    } elseif ($sendfrom != '') {
                                                        $assigned_number = $sendfrom;
                                                    } else {
                                                        if (!empty($usernumber)) {
                                                            if ($usernumber['User']['sms'] == 1) {
                                                                $assigned_number = $usernumber['User']['assigned_number'];
                                                            } else {
                                                                $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                                if (!empty($user_numbers)) {
                                                                    $assigned_number = $user_numbers['UserNumber']['number'];
                                                                } else {
                                                                    $assigned_number = $usernumber['User']['assigned_number'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $this->Twilio->curlinit = curl_init();
                                                $this->Twilio->bulksms = 1;

                                                foreach ($schedulemessages1 as $schedulemessages) {
                                                    //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                    if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                        $sms_balance = $users['User']['sms_balance'];
                                                        $count++;
                                                        $body = $message . " " . $systemmsg;
                                                        $spinbody = $this->process($body);
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        $length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                            $contactcredits = CHARGECREDITSMMS;
                                                        } else {
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $contactcredits = ceil($length / 70);
                                                            } else {
                                                                $contactcredits = ceil($length / 160);
                                                            }
                                                        }
                                                        if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                            //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                            $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                            $to = $schedulemessages['Contact']['phone_number'];
                                                            $from = $assigned_number;
                                                            $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                            if ($space_pos != '') {
                                                                $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                            } else {
                                                                $contact_name = $schedulemessages['Contact']['name'];

                                                            }
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $message_arr = explode(',', $message);
                                                                $message2 = $message_arr;
                                                                $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                            }
                                                            $body = '';
                                                            $msg_type = '';
                                                            $Status = '';
                                                            if ($throttle > 1) {
                                                                sleep($throttle);
                                                            }
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                $body = $message2 . " " . $systemmsg;
                                                                if ($SID == ''){
                                                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                                }else{
                                                                    $this->Twilio->AccountSid = $SID;
                                                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                                                }
                                                                
                                                                $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                        $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                        $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                        $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                        $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                        $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                        $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
                                                                
                                                                $spinbody = $this->process($body);
                                                                $response = $this->Twilio->sendsms($to, $from, $spinbody);
                                                                $msg_type = 'text';
                                                                $smsid = $response->ResponseXml->Message->Sid;
                                                                $Status = $response->ResponseXml->RestException->Status;
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $body = $message2;
                                                                if ($SID == ''){
                                                                    $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                                    $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                                }else{
                                                                    $this->Mms->AccountSid = $SID;
                                                                    $this->Mms->AuthToken = $AUTHTOKEN;
                                                                }
                                                                
                                                                $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
						                                        
                                                                $spinmms_text = $this->process($mms_textnew);
                                                                $response = $this->Mms->sendmms($to, $from, $body, $spinmms_text);
                                                                $msg_type = 'text';
                                                                $smsid = $response->sid;
                                                                if ($smsid == '') {
                                                                    $ErrorMessage = $response;
                                                                    $Status = 400;
                                                                }
                                                            }
                                                            //save data in Schedule Message table
                                                            //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                            //$ScheduleMessage_arr['ScheduleMessage']['id'] = $scheduleId;
                                                            //$ScheduleMessage_arr['ScheduleMessage']['sent'] = 1;
                                                            //$this->ScheduleMessage->save($ScheduleMessage_arr);
                                                            $this->Log = new Log();
                                                            $logArr['group_sms_id'] = $group_sms_id;
                                                            $logArr['sms_id'] = $smsid;
                                                            $logArr['group_id'] = $group_id;
                                                            $logArr['user_id'] = $users['User']['id'];
                                                            $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                            $logArr['sendfrom'] = $from;
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $logArr['image_url'] = $message;
                                                                $logArr['text_message'] = $spinmms_text;
                                                            } else {
                                                                $logArr['text_message'] = $spinbody;
                                                            }
                                                            $logArr['msg_type'] = $msg_type;
                                                            $logArr['route'] = 'outbox';
                                                            $logArr['sms_status'] = '';
                                                            $logArr['error_message'] = '';
                                                            if ($Status == 400) {
                                                                $logArr['sms_status'] = 'failed';
                                                                if (isset($response->ErrorMessage)) {
                                                                    $ErrorMessage = $response->ErrorMessage;
                                                                } else {
                                                                    $ErrorMessage = $ErrorMessage;
                                                                }
                                                                $logArr['error_message'] = $ErrorMessage;
                                                            }
                                                            $this->Log->save($logArr);
                                                            if ($Status == 400) {
                                                                $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                                $GroupSmsBlast['GroupSmsBlast']['id'] = $group_sms_id;
                                                                $GroupSmsBlast['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                $this->GroupSmsBlast->save($GroupSmsBlast);
                                                            }
                                                        }
                                                    }
                                                    $numhold++;
                                                }
                                                
                                                curl_close($this->Twilio->curlinit);
                                            }
                                        }
                                    
                                    } else if ($API_TYPE == 2) {
                                        if ($rotate == 1) {
                                            //$subscriberPhone1 = '';
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                            $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                            $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                            $this->GroupSmsBlast->save($this->request->data);
                                            $group_sms_id = $this->GroupSmsBlast->id;
                                            $k = 0;
                                            $sucesscredits = 0;
                                            $credits = 0;
                                            $this->Slooce->curlinit = curl_init();
                                            $this->Slooce->bulksms = 1;
                                            foreach ($schedulemessages1 as $schedulemessages) {
                                                //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                    $sms_balance = $users['User']['sms_balance'];
                                                    $count++;
                                                    if ($sms_balance > $totalSubscriber) {
                                                        //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                        $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                        $to = $schedulemessages['Contact']['phone_number'];
                                                        /*app::import('Model', 'UserNumber');
                                                        $this->UserNumber = new UserNumber();
                                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                                        $from_arr = array();
                                                        if (!empty($users)) {
                                                            if ($users['User']['sms'] == 1) {
                                                                $from_arr[] = $users['User']['assigned_number'];
                                                            }
                                                        }
                                                        if (!empty($user_numbers)) {
                                                            foreach ($user_numbers as $values) {
                                                                if ($values['UserNumber']['sms'] == 1) {
                                                                    $from_arr[] = $values['UserNumber']['number'];
                                                                }
                                                            }
                                                        }*/
                                                        $countnumber = count($from_arr);
                                                        if ($countnumber == $k) {
                                                            $k = 0;
                                                        }
                                                        $from = $from_arr[$k];
                                                        $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                        if ($space_pos != '') {
                                                            $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                        } else {
                                                            $contact_name = $schedulemessages['Contact']['name'];
                                                        }
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                        $body = $message2 . " " . $systemmsg;
                                                        $spinbody = $this->process($body);
                                                        $response = $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $schedulemessages['Contact']['phone_number'], $users['User']['keyword'], $spinbody);
                                                        $message_id = '';
                                                        $status = '';
                                                        if (isset($response['id'])) {
                                                            if ($response['result'] == 'ok') {
                                                                $message_id = $response['id'];
                                                            }
                                                            $status = $response['result'];
                                                        }
                                                        //save data in log table
                                                        //save data in Schedule Message table
                                                        //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                        //$this->ScheduleMessage->id = $scheduleId;
                                                        //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                        //$this->ScheduleMessage->save($this->request->data);
                                                        $this->Log = new Log();
                                                        //pr($contactArr);
                                                        $logArr['group_sms_id'] = $group_sms_id;
                                                        $logArr['sms_id'] = $message_id;
                                                        $logArr['group_id'] = $group_id;
                                                        $logArr['user_id'] = $users['User']['id'];
                                                        $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                        $logArr['sendfrom'] = $from;
                                                        $logArr['text_message'] = $spinbody;
                                                        $logArr['route'] = 'outbox';
                                                        $logArr['sms_status'] = '';
                                                        $logArr['error_message'] = '';
                                                        if ($status != 'ok') {
                                                            $logArr['sms_status'] = 'failed';
                                                            $ErrorMessage = $status;
                                                            $logArr['error_message'] = $ErrorMessage;
                                                        } else {
                                                            $logArr['sms_status'] = 'sent';
                                                        }

                                                        $this->Log->save($logArr);
                                                        if ($message_id != '') {
                                                            $sucesscredits = $sucesscredits + 1;
                                                            $length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                            $credits = $credits + ceil($length / 160);
                                                        }
                                                        if ($status != 'ok') {
                                                            $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                            $this->GroupSmsBlast->id = $group_sms_id;
                                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                            $this->GroupSmsBlast->save($this->request->data);
                                                        }
                                                    }
                                                }
                                                $k = $k + 1;
                                                $numhold++;
                                            }
                                            curl_close($this->Slooce->curlinit);
                                            if ($sucesscredits > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                $this->GroupSmsBlast->save($group_blast);
                                                app::import('Model', 'User');
                                                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                //$this->request->data['User']['sms_balance']=$usersms['User']['sms_balance']-$sucesscredits;
                                                //$length = strlen(utf8_decode(substr($body,0,1600)));
                                                //$credits = ceil($length/160);
                                                $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);

                                        } else {
                                            //$subscriberPhone1 = '';
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                            $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                            $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                            $this->GroupSmsBlast->save($this->request->data);
                                            $group_sms_id = $this->GroupSmsBlast->id;
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if (!empty($usernumber)) {
                                                if ($usernumber['User']['sms'] == 1) {
                                                    $assigned_number = $usernumber['User']['assigned_number'];
                                                } else {
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
                                            $this->Slooce->curlinit = curl_init();
                                            $this->Slooce->bulksms = 1;
                                            foreach ($schedulemessages1 as $schedulemessages) {
                                                //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                    $sms_balance = $users['User']['sms_balance'];
                                                    $count++;
                                                    if ($sms_balance > $totalSubscriber) {
                                                        //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                        $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                        $to = $schedulemessages['Contact']['phone_number'];
                                                        $from = $assigned_number;
                                                        $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                        if ($space_pos != '') {
                                                            $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                        } else {
                                                            $contact_name = $schedulemessages['Contact']['name'];
                                                        }

                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                        $body = $message2 . " " . $systemmsg;
                                                        $spinbody = $this->process($body);
                                                        $response = $this->Slooce->mt($usernumber['User']['api_url'], $usernumber['User']['partnerid'], $usernumber['User']['partnerpassword'], $schedulemessages['Contact']['phone_number'], $usernumber['User']['keyword'], $spinbody);
                                                        $message_id = '';
                                                        $status = '';
                                                        if (isset($response['id'])) {
                                                            if ($response['result'] == 'ok') {
                                                                $message_id = $response['id'];
                                                            }
                                                            $status = $response['result'];
                                                        }
                                                        //save data in log table
                                                        //save data in Schedule Message table
                                                        //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                        //$this->ScheduleMessage->id = $scheduleId;
                                                        //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                        //$this->ScheduleMessage->save($this->request->data);
                                                        $this->Log = new Log();
                                                        //pr($contactArr);
                                                        $logArr['group_sms_id'] = $group_sms_id;
                                                        $logArr['sms_id'] = $message_id;
                                                        $logArr['group_id'] = $group_id;
                                                        $logArr['user_id'] = $users['User']['id'];
                                                        $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                        $logArr['sendfrom'] = $from;
                                                        $logArr['text_message'] = $spinbody;
                                                        $logArr['route'] = 'outbox';
                                                        $logArr['sms_status'] = '';
                                                        $logArr['error_message'] = '';
                                                        if ($status != 'ok') {
                                                            $logArr['sms_status'] = 'failed';
                                                            $ErrorMessage = $status;
                                                            $logArr['error_message'] = $ErrorMessage;
                                                        } else {
                                                            $logArr['sms_status'] = 'sent';
                                                        }
                                                        $this->Log->save($logArr);
                                                        if ($message_id != '') {
                                                            $sucesscredits = $sucesscredits + 1;
                                                            $length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                            $credits = $credits + ceil($length / 160);
                                                        }
                                                        if ($status != 'ok') {
                                                            $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                            $this->GroupSmsBlast->id = $group_sms_id;
                                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                            $this->GroupSmsBlast->save($this->request->data);
                                                        }
                                                    }
                                                }
                                                $numhold++;
                                            }
                                            curl_close($this->Slooce->curlinit);
                                            if ($sucesscredits > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                $this->GroupSmsBlast->save($group_blast);
                                                app::import('Model', 'User');
                                                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                //$this->request->data['User']['sms_balance']=$usersms['User']['sms_balance']-$sucesscredits;
                                                //$length = strlen(utf8_decode(substr($body,0,1600)));
                                                //$credits = ceil($length/160);
                                                $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);

                                        }
                                    } else if ($API_TYPE == 333) { //****** PLIVO moved down below with the other MMS-enabled gateways ***********
                                        if ($rotate == 1) {
                                            //$subscriberPhone1 = '';
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                            $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                            $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                            $this->GroupSmsBlast->save($this->request->data);
                                            $group_sms_id = $this->GroupSmsBlast->id;
                                            $k = 0;
                                            $sucesscredits = 0;
                                            $credits = 0;
                                            $this->Plivo->curlinit = curl_init();
                                            $this->Plivo->bulksms = 1;
                                            foreach ($schedulemessages1 as $schedulemessages) {
                                                //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                    $sms_balance = $users['User']['sms_balance'];
                                                    $count++;
                                                    $body = $message . " " . $systemmsg;
                                                    $spinbody = $this->process($body);
                                                    //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                    $length = mb_strlen($spinbody,"UTF-8");
			                                        $gsm = $this->is_gsm($spinbody);
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                    if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                        //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                        $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                        $to = $schedulemessages['Contact']['phone_number'];
                                                        /*app::import('Model', 'UserNumber');
                                                        $this->UserNumber = new UserNumber();
                                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                                        $from_arr = array();
                                                        if (!empty($users)) {
                                                            if ($users['User']['sms'] == 1) {
                                                                $from_arr[] = $users['User']['assigned_number'];
                                                            }
                                                        }
                                                        if (!empty($user_numbers)) {
                                                            foreach ($user_numbers as $values) {
                                                                if ($values['UserNumber']['sms'] == 1) {
                                                                    $from_arr[] = $values['UserNumber']['number'];
                                                                }
                                                            }
                                                        }*/
                                                        
                                                        if ($throttle > 1) {
                                                            $countnumber = count($from_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                                sleep($throttle);
                                                            }
                                                        } else {
                                                            $countnumber = count($from_arr);
                                                            if ($countnumber == $k) {
                                                                $k = 0;
                                                            }
                                                        }
                                                        $from = $from_arr[$k];
                                                        $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                        
                                                        if ($stickyfrom == 0) {
                                                            $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                            $contact['Contact']['stickysender'] = $from;
                                                            $this->Contact->save($contact);
                                                        } else {
                                                            $from = $stickyfrom;
                                                        }

                                                        $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                        if ($space_pos != '') {
                                                            $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                        } else {
                                                            $contact_name = $schedulemessages['Contact']['name'];
                                                        }
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                        $body = $message2 . " " . $systemmsg;
                                                        if ($SID == ''){
                                                            $this->Plivo->AuthId = PLIVO_KEY;
                                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                        }else{
                                                            $this->Plivo->AuthId = $SID;
                                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $spinbody = $this->process($body);
                                                        $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                        $errortext = '';
                                                        $message_id = '';
                                                        if (isset($response['response']['error'])) {
                                                            $errortext = $response['response']['error'];
                                                        }
                                                        if (isset($response['response']['message_uuid'][0])) {
                                                            $message_id = $response['response']['message_uuid'][0];
                                                        }
                                                        //save data in log table
                                                        //save data in Schedule Message table
                                                        //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                        //$this->ScheduleMessage->id = $scheduleId;
                                                        //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                        //$this->ScheduleMessage->save($this->request->data);
                                                        $this->Log = new Log();
                                                        //pr($contactArr);
                                                        $logArr['group_sms_id'] = $group_sms_id;
                                                        $logArr['sms_id'] = $message_id;
                                                        $logArr['group_id'] = $group_id;
                                                        $logArr['user_id'] = $users['User']['id'];
                                                        $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                        $logArr['sendfrom'] = $from;
                                                        $logArr['text_message'] = $spinbody;
                                                        $logArr['route'] = 'outbox';
                                                        $logArr['sms_status'] = '';
                                                        $logArr['error_message'] = '';
                                                        if (isset($response['response']['error'])) {
                                                            $logArr['sms_status'] = 'failed';
                                                            $ErrorMessage = $errortext;
                                                            $logArr['error_message'] = $ErrorMessage;
                                                        } else {
                                                            $logArr['sms_status'] = 'sent';
                                                        }

                                                        $this->Log->save($logArr);
                                                        if ($message_id != '') {
                                                            $sucesscredits = $sucesscredits + 1;
                                                            //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                            /*$length = mb_strlen($spinbody,"UTF-8");
			                                                $gsm = $this->is_gsm($spinbody);
                                                            //$credits = $credits + ceil($length/160);
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $credits = $credits + ceil($length / 70);
                                                            } else {
                                                                $credits = $credits + ceil($length / 160);
                                                            }*/
                                                            $credits = $credits + $contactcredits;
                                                        }
                                                        if (isset($response['response']['error'])) {
                                                            $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                            $this->GroupSmsBlast->id = $group_sms_id;
                                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                            $this->GroupSmsBlast->save($this->request->data);
                                                        }
                                                    }
                                                }
                                                $numhold++;
                                                $k = $k + 1;
                                            }
                                            curl_close($this->Plivo->curlinit);
                                            if ($sucesscredits > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                $this->GroupSmsBlast->save($group_blast);
                                                app::import('Model', 'User');
                                                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);

                                        } else {
                                            //$subscriberPhone1 = '';
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                            $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                            $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                            $this->GroupSmsBlast->save($this->request->data);
                                            $group_sms_id = $this->GroupSmsBlast->id;
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                            if ($alphasenderid != '') {
                                                $assigned_number = $alphasenderid;
                                            } elseif ($sendfrom != '') {
                                                $assigned_number = $sendfrom;
                                            } else {
                                                if (!empty($usernumber)) {
                                                    if ($usernumber['User']['sms'] == 1) {
                                                        $assigned_number = $usernumber['User']['assigned_number'];
                                                    } else {
                                                        $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                        if (!empty($user_numbers)) {
                                                            $assigned_number = $user_numbers['UserNumber']['number'];
                                                        } else {
                                                            $assigned_number = $usernumber['User']['assigned_number'];
                                                        }
                                                    }
                                                }
                                            }
                                            $sucesscredits = 0;
                                            $credits = 0;
                                            $this->Plivo->curlinit = curl_init();
                                            $this->Plivo->bulksms = 1;
                                            foreach ($schedulemessages1 as $schedulemessages) {
                                                //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                    $sms_balance = $users['User']['sms_balance'];
                                                    $count++;
                                                    $body = $message . " " . $systemmsg;
                                                    $spinbody = $this->process($body);
                                                    //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                    $length = mb_strlen($spinbody,"UTF-8");
			                                        $gsm = $this->is_gsm($spinbody);
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                    if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                        //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                        $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                        $to = $schedulemessages['Contact']['phone_number'];
                                                        $from = $assigned_number;
                                                        $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                        if ($space_pos != '') {
                                                            $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                        } else {
                                                            $contact_name = $schedulemessages['Contact']['name'];
                                                        }

                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                        $body = $message2 . " " . $systemmsg;
                                                        if ($SID == ''){
                                                            $this->Plivo->AuthId = PLIVO_KEY;
                                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                        }else{
                                                            $this->Plivo->AuthId = $SID;
                                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                                        }
                                                        //sleep($throttle);
                                                        if ($throttle > 1) {
                                                            sleep($throttle);
                                                        }
                                                        $spinbody = $this->process($body);
                                                        $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                        $errortext = '';
                                                        $message_id = '';
                                                        if (isset($response['response']['error'])) {
                                                            $errortext = $response['response']['error'];
                                                        }
                                                        if (isset($response['response']['message_uuid'][0])) {
                                                            $message_id = $response['response']['message_uuid'][0];
                                                        }
                                                        //save data in log table
                                                        //save data in Schedule Message table
                                                        //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                        //$this->ScheduleMessage->id = $scheduleId;
                                                        //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                        //$this->ScheduleMessage->save($this->request->data);
                                                        $this->Log = new Log();
                                                        //pr($contactArr);
                                                        $logArr['group_sms_id'] = $group_sms_id;
                                                        $logArr['sms_id'] = $message_id;
                                                        $logArr['group_id'] = $group_id;
                                                        $logArr['user_id'] = $users['User']['id'];
                                                        $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                        $logArr['sendfrom'] = $from;
                                                        $logArr['text_message'] = $spinbody;
                                                        $logArr['route'] = 'outbox';
                                                        $logArr['sms_status'] = '';
                                                        $logArr['error_message'] = '';
                                                        if (isset($response['response']['error'])) {
                                                            $logArr['sms_status'] = 'failed';
                                                            $ErrorMessage = $errortext;
                                                            $logArr['error_message'] = $ErrorMessage;
                                                        } else {
                                                            $logArr['sms_status'] = 'sent';
                                                        }
                                                        $this->Log->save($logArr);
                                                        if ($message_id != '') {
                                                            $sucesscredits = $sucesscredits + 1;
                                                            //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                            /*$length = mb_strlen($spinbody,"UTF-8");
			                                                $gsm = $this->is_gsm($spinbody);
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $credits = $credits + ceil($length / 70);
                                                            } else {
                                                                $credits = $credits + ceil($length / 160);
                                                            }*/
                                                            $credits = $credits + $contactcredits;
                                                        }
                                                        if (isset($response['response']['error'])) {
                                                            $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                            $this->GroupSmsBlast->id = $group_sms_id;
                                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                            $this->GroupSmsBlast->save($this->request->data);
                                                        }
                                                    }
                                                }
                                                $numhold++;
                                            }
                                            curl_close($this->Plivo->curlinit);
                                            if ($sucesscredits > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                $this->GroupSmsBlast->save($group_blast);
                                                app::import('Model', 'User');
                                                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);

                                        }
                                   } else if ($API_TYPE == 3 || $API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {
                                        if ($rotate == 1) {
                                            if ($users['User']['sms_balance'] > $totalSubscriber) {
                                                //$subscriberPhone1 = '';
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                                $k = 0;
                                                $success = 0;
                                                $credits = 0;
                                                if($API_TYPE==5){
                                                    $this->Signalwire->curlinit = curl_init();
                                                    $this->Signalwire->bulksms = 1;
                                                }elseif($API_TYPE==6){
                                                    $this->Ytel->curlinit = curl_init();
                                                    $this->Ytel->bulksms = 1;
                                                }elseif($API_TYPE==7){
                                                    $this->Telnyx->curlinit = curl_init();
                                                    $this->Telnyx->bulksms = 1;
                                                }elseif($API_TYPE==3){
                                                    $this->Plivo->curlinit = curl_init();
                                                    $this->Plivo->bulksms = 1;
                                                }
                                                foreach ($schedulemessages1 as $schedulemessages) {
                                                    if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                        $sms_balance = $users['User']['sms_balance'];
                                                        $body = $message . " " . $systemmsg;
                                                        $spinbody = $this->process($body);
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        $length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                            $contactcredits = CHARGECREDITSMMS;
                                                        } else {
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $contactcredits = ceil($length / 70);
                                                            } else {
                                                                $contactcredits = ceil($length / 160);
                                                            }
                                                        }
                                                        if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                            $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                            $to = $schedulemessages['Contact']['phone_number'];
                                                            /*app::import('Model', 'UserNumber');
                                                            $this->UserNumber = new UserNumber();
                                                            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                                            $from_arr = array();
                                                            if (!empty($users)) {
                                                                if ($users['User']['sms'] == 1) {
                                                                    $from_arr[] = $users['User']['assigned_number'];
                                                                }
                                                            }
                                                            if (!empty($user_numbers)) {
                                                                foreach ($user_numbers as $values) {
                                                                    if ($values['UserNumber']['sms'] == 1) {
                                                                        $from_arr[] = $values['UserNumber']['number'];
                                                                    }
                                                                }
                                                            }
                                                            $mms_arr = array();
                                                            if (!empty($users)) {
                                                                if ($users['User']['mms'] == 1) {
                                                                    $mms_arr[] = $users['User']['assigned_number'];
                                                                }
                                                            }
                                                            if (!empty($user_numbers)) {
                                                                foreach ($user_numbers as $values) {
                                                                    if ($values['UserNumber']['mms'] == 1) {
                                                                        $mms_arr[] = $values['UserNumber']['number'];
                                                                    }
                                                                }
                                                            }*/
                                                            
                                                            $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                            if ($space_pos != '') {
                                                                $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                            } else {
                                                                $contact_name = $schedulemessages['Contact']['name'];
                                                            }
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $message_arr = explode(',', $message);
                                                                $message2 = $message_arr;
                                                                $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                            }
															$body = '';
															$msg_type = '';
															$message_id = '';
															$status = '';
															$errortext = '';
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                if ($throttle > 1) {
                                                                    $countnumber = count($from_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                        sleep($throttle);
                                                                    }
                                                                } else {
                                                                    $countnumber = count($from_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                    }
                                                                }
                                                                $from = $from_arr[$k];

                                                                $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }
						                                        
                                                                $body = $message2 . " " . $systemmsg;
                                                                
                                                                $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                        $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                        $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                        $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                        $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                        $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                        $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
						                                        
                                                                $spinbody = $this->process($body);
                                                                /*if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                                }
                                                                $msg_type = 'text';
																if(isset($response->message)){
																	$errortext = $response->message;
																	$status = 'failed';
																}else{
																	$status = 'sent';
																}*/
																$message_id = '';
																if($API_TYPE==3){
        														    if ($SID == ''){
                                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                                    }else{
                                                                        $this->Plivo->AuthId = $SID;
                                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                                    }
                                                                    $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                                    $msg_type = 'text';
                                                            		$status = '';
                                                                    $errortext = '';
                                                                    if (isset($response['response']['error'])) {
                                                                        $errortext = $response['response']['error'];
                                                                        $status = 'failed';
                                                                    }
                                                                    if (isset($response['response']['message_uuid'][0])) {
                                                                        $message_id = $response['response']['message_uuid'][0];
                                                                        $status = 'sent';
                                                                    }
        														}elseif($API_TYPE==5){
        														    if ($SID == ''){
                                								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinbody);
        														    }else{
        														        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinbody);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if(isset($response->error_message)){
                                                        				$errortext = $response->error_message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->sid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==6){
        														    if ($SID == ''){
                                								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinbody,$group_sms_id,$group_id,$users['User']['id']);
        														    }else{
        														        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$spinbody,$group_sms_id,$group_id,$users['User']['id']);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if($response->Message360->ResponseStatus == 0){
                                                        				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->Message360->Message->MessageSid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==7){
        														    if ($SID == ''){
                                								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$spinbody);
        														    }else{
        														        $response = $this->Telnyx->sendsms($SID,$to,$from,$spinbody);
        														    }
        														    $msg_type = 'text';
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
                                						                if ($SID == ''){
                                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                                        }else{
                                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                                        }
                                                                        $msg_type = 'text';
                                					
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
																
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                if ($throttle > 1) {
                                                                    $countnumber = count($mms_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                        sleep($throttle);
                                                                    }
                                                                } else {
                                                                    $countnumber = count($mms_arr);
                                                                    if ($countnumber == $k) {
                                                                        $k = 0;
                                                                    }
                                                                }
                                                                $from = $mms_arr[$k];
                                                                $stickyfrom = $schedulemessages['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }
                                                                $body = $message2;
                                                                
                                                                $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);

                                                                $spinmms_text = $this->process($mms_textnew);
                                                                /*if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                                }
                                                                $msg_type = 'text';
																if(isset($response->message)){
																	$errortext = $response->message;
																	$status = 'failed';
																}else{
																	$status = 'sent';
																}*/
																$message_id = '';
																if($API_TYPE==3){
        														    if ($SID == ''){
                                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                                    }else{
                                                                        $this->Plivo->AuthId = $SID;
                                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                                    }
                                                                    $response = $this->Plivo->sendmms($to,$from,$spinmms_text,$body);
                                                                    $msg_type = 'text';
                                                            		$status = '';
                                                                    $errortext = '';
                                                                    if (isset($response['response']['error'])) {
                                                                        $errortext = $response['response']['error'];
                                                                        $status = 'failed';
                                                                    }
                                                                    if (isset($response['response']['message_uuid'][0])) {
                                                                        $message_id = $response['response']['message_uuid'][0];
                                                                        $status = 'sent';
                                                                    }
        														}elseif($API_TYPE==5){
        														    if ($SID == ''){
                                								        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinmms_text,$body);
        														    }else{
        														        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinmms_text,$body);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if(isset($response->error_message)){
                                                        				$errortext = $response->error_message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->sid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==6){
        														    if ($SID == ''){
                                								        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinmms_text,$body,$group_sms_id,$group_id,$users['User']['id']);
        														    }else{
        														        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$spinmms_text,$body,$group_sms_id,$group_id,$users['User']['id']);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if($response->Message360->ResponseStatus == 0){
                                                        				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->Message360->Message->MessageSid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==7){
        														    if ($SID == ''){
                                								        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$spinmms_text,$body);
        														    }else{
        														        $response = $this->Telnyx->sendmms($SID,$to,$from,$spinmms_text,$body);
        														    }
        														    $msg_type = 'text';
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
                                						                if ($SID == ''){
                                                                            $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                                        }else{
                                                                            $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                                        }
                                                                        $msg_type = 'text';
                                					
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
                                                            }
                                                            //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                            //$this->ScheduleMessage->id = $scheduleId;
                                                            //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                            //$this->ScheduleMessage->save($this->request->data);
                                                            $this->Log = new Log();
                                                            $logArr['group_sms_id'] = $group_sms_id;
                                                            $logArr['sms_id'] = $message_id;
                                                            $logArr['group_id'] = $group_id;
                                                            $logArr['user_id'] = $users['User']['id'];
                                                            $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                            $logArr['sendfrom'] = $from;
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $logArr['image_url'] = $message;
                                                                $logArr['text_message'] = $spinmms_text;
                                                                if ($errortext == '') {
                                                                    $credits = $credits + CHARGECREDITSMMS;
                                                                }
                                                            } else {
                                                                $logArr['text_message'] = $spinbody;
                                                                if ($errortext == '') {
                                                                    $credits = $credits + $contactcredits;
                                                                }

                                                            }
                                                            $logArr['msg_type'] = $msg_type;
                                                            $logArr['route'] = 'outbox';
                                                            $logArr['sms_status'] = '';
                                                            $logArr['error_message'] = '';
															if ($errortext !='') {
                                                                $logArr['sms_status'] = 'failed';
                                                                $logArr['error_message'] = $errortext;
                                                                if($API_TYPE==6){
                                                                    $this->Log->save($logArr);
                                                                }
                                                            }else{
															    $logArr['sms_status'] = 'sent';
																$success = $success + 1;
															}
                                                            if($API_TYPE!=6){
                                                                $this->Log->save($logArr);
                                                            }
                                                            if ($errortext !='') {
                                                                $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                                $GroupSmsBlast['GroupSmsBlast']['id'] = $group_sms_id;
                                                                $GroupSmsBlast['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                $this->GroupSmsBlast->save($GroupSmsBlast);
                                                            }

                                                        }
                                                    }
                                                    $numhold++;
                                                    $k = $k + 1;
                                                }
                                                if($API_TYPE==5){
                                                    curl_close($this->Signalwire->curlinit);
                                                }elseif($API_TYPE==6){
                                                    curl_close($this->Ytel->curlinit);
                                                }elseif($API_TYPE==7){
                                                    curl_close($this->Telnyx->curlinit);
                                                }elseif($API_TYPE==3){
                                                    curl_close($this->Plivo->curlinit);
                                                }
                                                if ($success > 0) {
                                                    app::import('Model', 'GroupSmsBlast');
                                                    $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                    $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
                                                    $this->GroupSmsBlast->save($group_blast);
                                                    
                                                    app::import('Model', 'User');
                                                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                    $this->request->data['User']['id'] = $usersms['User']['id'];
                                                    $this->User->save($this->request->data);
                                                }
                                                $this->smsmail($user_id);
                                            }
                                        } else {
                                            if ($users['User']['sms_balance'] > $totalSubscriber) {
                                                //$subscriberPhone1 = '';
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                                if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                    $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
                                                    if ($alphasenderid != '') {
                                                        $assigned_number = $alphasenderid;
                                                    } elseif ($sendfrom != '') {
                                                        $assigned_number = $sendfrom;
                                                    } else {
                                                        if (empty($usernumber)) {
                                                            $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                                                            if (!empty($mmsnumber)) {
                                                                $assigned_number = $mmsnumber['UserNumber']['number'];
                                                            } else {
                                                                $assigned_number = $users['User']['assigned_number'];
                                                            }
                                                        } else {
                                                            $assigned_number = $usernumber['User']['assigned_number'];
                                                        }
                                                    }
                                                } else {
                                                    $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if ($alphasenderid != '') {
                                                        $assigned_number = $alphasenderid;
                                                    } elseif ($sendfrom != '') {
                                                        $assigned_number = $sendfrom;
                                                    } else {
                                                        if (!empty($usernumber)) {
                                                            if ($usernumber['User']['sms'] == 1) {
                                                                $assigned_number = $usernumber['User']['assigned_number'];
                                                            } else {
                                                                $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                                if (!empty($user_numbers)) {
                                                                    $assigned_number = $user_numbers['UserNumber']['number'];
                                                                } else {
                                                                    $assigned_number = $usernumber['User']['assigned_number'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $credits = 0;
                                                $success = 0;
                                                if($API_TYPE==5){
                                                    $this->Signalwire->curlinit = curl_init();
                                                    $this->Signalwire->bulksms = 1;
                                                }elseif($API_TYPE==6){
                                                    $this->Ytel->curlinit = curl_init();
                                                    $this->Ytel->bulksms = 1;
                                                }elseif($API_TYPE==7){
                                                    $this->Telnyx->curlinit = curl_init();
                                                    $this->Telnyx->bulksms = 1;
                                                }elseif($API_TYPE==3){
                                                    $this->Plivo->curlinit = curl_init();
                                                    $this->Plivo->bulksms = 1;
                                                }
                                                foreach ($schedulemessages1 as $schedulemessages) {
                                                    //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                    if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                        $sms_balance = $users['User']['sms_balance'];
                                                        $count++;
                                                        $body = $message . " " . $systemmsg;
                                                        $spinbody = $this->process($body);
                                                        //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                        $length = mb_strlen($spinbody,"UTF-8");
			                                            $gsm = $this->is_gsm($spinbody);
                                                        if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                            $contactcredits = CHARGECREDITSMMS;
                                                        } else {
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $contactcredits = ceil($length / 70);
                                                            } else {
                                                                $contactcredits = ceil($length / 160);
                                                            }
                                                        }
                                                        if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                            //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                            $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                            $to = $schedulemessages['Contact']['phone_number'];
                                                            $from = $assigned_number;
                                                            $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                            if ($space_pos != '') {
                                                                $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                            } else {
                                                                $contact_name = $schedulemessages['Contact']['name'];

                                                            }
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $message_arr = explode(',', $message);
                                                                $message2 = $message_arr;
                                                                $mms_textnew = str_replace('%%Name%%', $contact_name, $mms_text);
                                                            }
															$body = '';
															$msg_type = '';
															$message_id = '';
															$status = '';
															$errortext = '';
                                                            if ($throttle > 1) {
                                                                sleep($throttle);
                                                            }
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 1) {
                                                                $body = $message2 . " " . $systemmsg;
                                                                
                                                                $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                        $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                        $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                        $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                        $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                        $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                        $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);
						                                        
                                                                $spinbody = $this->process($body);
                                                                /*if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                                }
                                                                $msg_type = 'text';
																if(isset($response->message)){
																	$errortext = $response->message;
																	$status = 'failed';
																}else{
																	$status = 'sent';
																}*/
																$message_id = '';
																if($API_TYPE==3){
        														    if ($SID == ''){
                                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                                    }else{
                                                                        $this->Plivo->AuthId = $SID;
                                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                                    }
                                                                    $response = $this->Plivo->sendsms($to, $from, $spinbody);
                                                                    $msg_type = 'text';
                                                            		$status = '';
                                                                    $errortext = '';
                                                                    if (isset($response['response']['error'])) {
                                                                        $errortext = $response['response']['error'];
                                                                        $status = 'failed';
                                                                    }
                                                                    if (isset($response['response']['message_uuid'][0])) {
                                                                        $message_id = $response['response']['message_uuid'][0];
                                                                        $status = 'sent';
                                                                    }
        														}elseif($API_TYPE==5){
        														    if ($SID == ''){
                                								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinbody);
        														    }else{
        														        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinbody);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if(isset($response->error_message)){
                                                        				$errortext = $response->error_message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->sid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==6){
        														    if ($SID == ''){
                                								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinbody,$group_sms_id,$group_id,$users['User']['id']);
        														    }else{
        														        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$spinbody,$group_sms_id,$group_id,$users['User']['id']);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if($response->Message360->ResponseStatus == 0){
                                                        				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->Message360->Message->MessageSid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==7){
        														    if ($SID == ''){
                                								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$spinbody);
        														    }else{
        														        $response = $this->Telnyx->sendsms($SID,$to,$from,$spinbody);
        														    }
        														    $msg_type = 'text';
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
                                						                if ($SID == ''){
                                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinbody);
                                                                        }else{
                                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinbody);
                                                                        }
                                                                        $msg_type = 'text';
                                					
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
                                                            } else if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $body = $message2;
                                                                
                                                                $mms_textnew = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%City%%', $schedulemessages['Contact']['location'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%State%%', $schedulemessages['Contact']['state'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $mms_textnew);
						                                        $mms_textnew = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $mms_textnew);
						                                        
                                                                $spinmms_text = $this->process($mms_textnew);
                                                                /*if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                                }
                                                                $msg_type = 'text';
																if(isset($response->message)){
																	$errortext = $response->message;
																	$status = 'failed';
																}else{
																	$status = 'sent';
																}*/
																$message_id = '';
																if($API_TYPE==3){
        														    if ($SID == ''){
                                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                                    }else{
                                                                        $this->Plivo->AuthId = $SID;
                                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                                    }
                                                                    $response = $this->Plivo->sendmms($to,$from,$spinmms_text,$body);
                                                                    $msg_type = 'text';
                                                            		$status = '';
                                                                    $errortext = '';
                                                                    if (isset($response['response']['error'])) {
                                                                        $errortext = $response['response']['error'];
                                                                        $status = 'failed';
                                                                    }
                                                                    if (isset($response['response']['message_uuid'][0])) {
                                                                        $message_id = $response['response']['message_uuid'][0];
                                                                        $status = 'sent';
                                                                    }
        														}elseif($API_TYPE==5){
        														    if ($SID == ''){
                                								        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$spinmms_text,$body);
        														    }else{
        														        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$spinmms_text,$body);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if(isset($response->error_message)){
                                                        				$errortext = $response->error_message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->sid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==6){
        														    if ($SID == ''){
                                								        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$spinmms_text,$body,$group_sms_id,$group_id,$users['User']['id']);
        														    }else{
        														        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$spinmms_text,$body,$group_sms_id,$group_id,$users['User']['id']);
        														    }
        														    $msg_type = 'text';
                                                            		$status = '';
                                                        			$errortext = '';
                                                        			if($response->Message360->ResponseStatus == 0){
                                                        				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                        				$status = 'failed';
                                                        			}else{
                                                        			    $message_id = $response->Message360->Message->MessageSid;
                                                        				$status = 'sent';
                                                        			}
        														}elseif($API_TYPE==7){
        														    if ($SID == ''){
                                								        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$spinmms_text,$body);
        														    }else{
        														        $response = $this->Telnyx->sendmms($SID,$to,$from,$spinmms_text,$body);
        														    }
        														    $msg_type = 'text';
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
                                						                if ($SID == ''){
                                                                            $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$spinmms_text,$body);
                                                                        }else{
                                                                            $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$spinmms_text,$body);
                                                                        }
                                                                        $msg_type = 'text';
                                					
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
                                                            }
                                                            //save data in Schedule Message table
                                                            //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                            //$ScheduleMessage_arr['ScheduleMessage']['id'] = $scheduleId;
                                                            //$ScheduleMessage_arr['ScheduleMessage']['sent'] = 1;
                                                            //$this->ScheduleMessage->save($ScheduleMessage_arr);
                                                            $this->Log = new Log();
                                                            $logArr['group_sms_id'] = $group_sms_id;
                                                            $logArr['sms_id'] = $message_id;
                                                            $logArr['group_id'] = $group_id;
                                                            $logArr['user_id'] = $users['User']['id'];
                                                            $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                            $logArr['sendfrom'] = $from;
                                                            if ($schedulemessage['ScheduleMessage']['msg_type'] == 2) {
                                                                $logArr['image_url'] = $message;
                                                                $logArr['text_message'] = $spinmms_text;
                                                                if ($errortext == '') {
                                                                    $credits = $credits + CHARGECREDITSMMS;
                                                                }
                                                            } else {
                                                                $logArr['text_message'] = $spinbody;
                                                                if ($errortext == '') {
                                                                    $credits = $credits + $contactcredits;
                                                                }

                                                            }
                                                            $logArr['msg_type'] = $msg_type;
                                                            $logArr['route'] = 'outbox';
                                                            $logArr['sms_status'] = '';
                                                            $logArr['error_message'] = '';
                                                            if ($errortext !='') {
                                                                $logArr['sms_status'] = $status;
                                                                $logArr['error_message'] = $errortext;
                                                                if($API_TYPE==6){
                                                                    $this->Log->save($logArr);
                                                                }
                                                            }else{
															    $logArr['sms_status'] = 'sent';
																$success = $success + 1;
															}
                                                            if($API_TYPE!=6){
                                                                $this->Log->save($logArr);
                                                            }
                                                            if ($errortext !='') {
                                                                $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                                $GroupSmsBlast['GroupSmsBlast']['id'] = $group_sms_id;
                                                                $GroupSmsBlast['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                $this->GroupSmsBlast->save($GroupSmsBlast);
                                                            }
                                                        }
                                                    }
                                                    $numhold++;
                                                }
                                                if($API_TYPE==5){
                                                    curl_close($this->Signalwire->curlinit);
                                                }elseif($API_TYPE==6){
                                                    curl_close($this->Ytel->curlinit);
                                                }elseif($API_TYPE==7){
                                                    curl_close($this->Telnyx->curlinit);
                                                }elseif($API_TYPE==3){
                                                    curl_close($this->Plivo->curlinit);
                                                }
                                                if ($success > 0) {
                                                    app::import('Model', 'GroupSmsBlast');
                                                    $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                    $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
                                                    $this->GroupSmsBlast->save($group_blast);
                                                    
                                                    app::import('Model', 'User');
                                                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                    $this->request->data['User']['id'] = $usersms['User']['id'];
                                                    $this->User->save($this->request->data);
                                                }
                                                $this->smsmail($user_id);
                                            }
                                        }
                                    } else {
                                        if ($rotate == 1) {
                                            //$subscriberPhone1 = '';
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                            $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                            $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                            $this->GroupSmsBlast->save($this->request->data);
                                            $group_sms_id = $this->GroupSmsBlast->id;
                                            $k = 0;
                                            $sucesscredits = 0;
                                            $credits = 0;
                                            foreach ($schedulemessages1 as $schedulemessages) {
                                                //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                    $sms_balance = $users['User']['sms_balance'];
                                                    $count++;
                                                    $body = $message . " " . $systemmsg;
                                                    $spinbody = $this->process($body);
                                                    //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                    $length = mb_strlen($spinbody,"UTF-8");
			                                        $gsm = $this->is_gsm($spinbody);
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                    if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                        //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                        $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                        $to = $schedulemessages['Contact']['phone_number'];
                                                        /*app::import('Model', 'UserNumber');
                                                        $this->UserNumber = new UserNumber();
                                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                                        $from_arr = array();
                                                        if (!empty($users)) {
                                                            if ($users['User']['sms'] == 1) {
                                                                $from_arr[] = $users['User']['assigned_number'];
                                                            }
                                                        }
                                                        if (!empty($user_numbers)) {
                                                            foreach ($user_numbers as $values) {
                                                                if ($values['UserNumber']['sms'] == 1) {
                                                                    $from_arr[] = $values['UserNumber']['number'];
                                                                }
                                                            }
                                                        }*/
                                                        
                                                        $countnumber = count($from_arr);
                                                        if ($countnumber == $k) {
                                                            $k = 0;
                                                            sleep($throttle);
                                                        }
                                                        $from = $from_arr[$k];

                                                        if ($stickyfrom == 0) {
                                                            $contact['Contact']['id'] = $schedulemessages['Contact']['id'];
                                                            $contact['Contact']['stickysender'] = $from;
                                                            $this->Contact->save($contact);
                                                        } else {
                                                            $from = $stickyfrom;
                                                        }

                                                        $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                        if ($space_pos != '') {
                                                            $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                        } else {
                                                            $contact_name = $schedulemessages['Contact']['name'];
                                                        }
                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                        $body = $message2 . " " . $systemmsg;
                                                        if ($SID == ''){
                                                            $this->Nexmomessage->Key = NEXMO_KEY;
                                                            $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                        }else{
                                                            $this->Nexmomessage->Key = $SID;
                                                            $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                        }
                                                        
                                                        $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);

                                                        $spinbody = $this->process($body);
                                                        $response = $this->Nexmomessage->sendsms($to, $from, $spinbody);
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
                                                        //save data in log table
                                                        //save data in Schedule Message table
                                                        //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                        //$this->ScheduleMessage->id = $scheduleId;
                                                        //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                        //$this->ScheduleMessage->save($this->request->data);
                                                        $this->Log = new Log();
                                                        //pr($contactArr);
                                                        $logArr['group_sms_id'] = $group_sms_id;
                                                        $logArr['sms_id'] = $message_id;
                                                        $logArr['group_id'] = $group_id;
                                                        $logArr['user_id'] = $users['User']['id'];
                                                        $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                        $logArr['sendfrom'] = $from;
                                                        $logArr['text_message'] = $spinbody;
                                                        $logArr['route'] = 'outbox';
                                                        $logArr['sms_status'] = '';
                                                        $logArr['error_message'] = '';
                                                        if ($status != 0) {
                                                            $logArr['sms_status'] = 'failed';
                                                            $ErrorMessage = $errortext;
                                                            $logArr['error_message'] = $ErrorMessage;
                                                        } else {
                                                            $logArr['sms_status'] = 'sent';
                                                        }

                                                        $this->Log->save($logArr);
                                                        if ($message_id != '') {
                                                            $sucesscredits = $sucesscredits + 1;
                                                            //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                            /*$length = mb_strlen($spinbody,"UTF-8");
			                                                $gsm = $this->is_gsm($spinbody);
                                                            //$credits = $credits + ceil($length/160);
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $credits = $credits + ceil($length / 70);
                                                            } else {
                                                                $credits = $credits + ceil($length / 160);
                                                            }*/
                                                            $credits = $credits + $contactcredits;
                                                        }
                                                        if ($status != 0) {
                                                            $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                            $this->GroupSmsBlast->id = $group_sms_id;
                                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                            $this->GroupSmsBlast->save($this->request->data);
                                                        }
                                                    }
                                                }
                                                $numhold++;
                                                $k = $k + 1;
                                            }
                                            if ($sucesscredits > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                $this->GroupSmsBlast->save($group_blast);
                                                app::import('Model', 'User');
                                                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                //$this->request->data['User']['sms_balance']=$usersms['User']['sms_balance']-$sucesscredits;
                                                //$length = strlen(utf8_decode(substr($body,0,1600)));
                                                //$credits = ceil($length/160);
                                                $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);

                                        } else {
                                            //$subscriberPhone1 = '';
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                            $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                            $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                            $this->GroupSmsBlast->save($this->request->data);
                                            $group_sms_id = $this->GroupSmsBlast->id;
                                            $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

                                            if ($alphasenderid != '') {
                                                $assigned_number = $alphasenderid;
                                            } elseif ($sendfrom != '') {
                                                $assigned_number = $sendfrom;
                                            } else {
                                                if (!empty($usernumber)) {
                                                    if ($usernumber['User']['sms'] == 1) {
                                                        $assigned_number = $usernumber['User']['assigned_number'];
                                                    } else {
                                                        $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                        if (!empty($user_numbers)) {
                                                            $assigned_number = $user_numbers['UserNumber']['number'];
                                                        } else {
                                                            $assigned_number = $usernumber['User']['assigned_number'];
                                                        }
                                                    }
                                                }
                                            }
                                            $sucesscredits = 0;
                                            $credits = 0;
                                            foreach ($schedulemessages1 as $schedulemessages) {
                                                //if (!isset($subscriberPhone1[$schedulemessages['Contact']['phone_number']])) {
                                                if (!in_array($schedulemessages['Contact']['phone_number'], $subscriberPhone1)){
                                                    $sms_balance = $users['User']['sms_balance'];
                                                    $count++;
                                                    $body = $message . " " . $systemmsg;
                                                    $spinbody = $this->process($body);
                                                    //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                    $length = mb_strlen($spinbody,"UTF-8");
			                                        $gsm = $this->is_gsm($spinbody);
                                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                        $contactcredits = ceil($length / 70);
                                                    } else {
                                                        $contactcredits = ceil($length / 160);
                                                    }
                                                    if ($sms_balance >= ($totalSubscriber * $contactcredits)) {
                                                        //$subscriberPhone1[$schedulemessages['Contact']['phone_number']] = $schedulemessages['Contact']['phone_number'];
                                                        $subscriberPhone1[$numhold] = $schedulemessages['Contact']['phone_number'];
                                                        $to = $schedulemessages['Contact']['phone_number'];
                                                        $from = $assigned_number;
                                                        $space_pos = strpos($schedulemessages['Contact']['name'], ' ');
                                                        if ($space_pos != '') {
                                                            $contact_name = substr($schedulemessages['Contact']['name'], 0, $space_pos);
                                                        } else {
                                                            $contact_name = $schedulemessages['Contact']['name'];
                                                        }

                                                        $message2 = str_replace('%%Name%%', $contact_name, $message);
                                                        $body = $message2 . " " . $systemmsg;
                                                        if ($SID == ''){
                                                            $this->Nexmomessage->Key = NEXMO_KEY;
                                                            $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                        }else{
                                                            $this->Nexmomessage->Key = $SID;
                                                            $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                        }
                                                        sleep($throttle);
                                                        
                                                        $body = str_replace('%%Email%%', $schedulemessages['Contact']['email'], $body);
						                                $body = str_replace('%%City%%', $schedulemessages['Contact']['location'], $body);
						                                $body = str_replace('%%State%%', $schedulemessages['Contact']['state'], $body);
						                                $body = str_replace('%%Postal%%', $schedulemessages['Contact']['zip'], $body);
						                                $body = str_replace('%%Custom1%%', $schedulemessages['Contact']['custom1'], $body);
						                                $body = str_replace('%%Custom2%%', $schedulemessages['Contact']['custom2'], $body);
						                                $body = str_replace('%%Custom3%%', $schedulemessages['Contact']['custom3'], $body);

                                                        $spinbody = $this->process($body);
                                                        $response = $this->Nexmomessage->sendsms($to, $from, $spinbody);
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
                                                        //save data in log table
                                                        //save data in Schedule Message table
                                                        //$scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                                        //$this->ScheduleMessage->id = $scheduleId;
                                                        //$this->request->data['ScheduleMessage']['sent'] = 1;
                                                        //$this->ScheduleMessage->save($this->request->data);
                                                        $this->Log = new Log();
                                                        //pr($contactArr);
                                                        $logArr['group_sms_id'] = $group_sms_id;
                                                        $logArr['sms_id'] = $message_id;
                                                        $logArr['group_id'] = $group_id;
                                                        $logArr['user_id'] = $users['User']['id'];
                                                        $logArr['phone_number'] = $schedulemessages['Contact']['phone_number'];
                                                        $logArr['sendfrom'] = $from;
                                                        $logArr['text_message'] = $spinbody;
                                                        $logArr['route'] = 'outbox';
                                                        $logArr['sms_status'] = '';
                                                        $logArr['error_message'] = '';
                                                        if ($status != 0) {
                                                            $logArr['sms_status'] = 'failed';
                                                            $ErrorMessage = $errortext;
                                                            $logArr['error_message'] = $ErrorMessage;
                                                        } else {
                                                            $logArr['sms_status'] = 'sent';
                                                        }
                                                        $this->Log->save($logArr);
                                                        if ($message_id != '') {
                                                            $sucesscredits = $sucesscredits + 1;
                                                            //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                                            /*$length = mb_strlen($spinbody,"UTF-8");
			                                                $gsm = $this->is_gsm($spinbody);
                                                            if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                                                $credits = $credits + ceil($length / 70);
                                                            } else {
                                                                $credits = $credits + ceil($length / 160);
                                                            }*/
                                                            $credits = $credits + $contactcredits;
                                                        }
                                                        if ($status != 0) {
                                                            $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                            $this->GroupSmsBlast->id = $group_sms_id;
                                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                            $this->GroupSmsBlast->save($this->request->data);
                                                        }
                                                    }
                                                }
                                                $numhold++;
                                            }
                                            if ($sucesscredits > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $group_blast['GroupSmsBlast']['id'] = $group_sms_id;
                                                $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                $this->GroupSmsBlast->save($group_blast);
                                                app::import('Model', 'User');
                                                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $usersms['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function sendresponder($isday = 1)
    {
        $this->autoRender = false;
        app::import('Model', 'Responder');
        $this->Responder = new Responder();
        app::import('Model', 'Log');
        $this->Log = new Log();
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        app::import('Model', 'User');
        $this->User = new User();
        //$responsedata = $this->Responder->find('all');
        $responsedata = $this->Responder->find('all', array('conditions' => array('Responder.days >' => 0, 'Responder.ishour' => $isday)));
        if (!empty($responsedata)) {
            $current_datetime = date("n/d/Y");
            foreach ($responsedata as $response) {
                $Responderid = $response['Responder']['id'];
                $group_id = $response['Responder']['group_id'];
                $sms_type = $response['Responder']['sms_type'];
                $image_url = $response['Responder']['image_url'];
                $message = str_replace('%%CURRENTDATE%%', $current_datetime, $response['Responder']['message']);
                $systemmsg = $response['Responder']['systemmsg'];
                $user_id = $response['Responder']['user_id'];
                $responderdays = $response['Responder']['days'];
                $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                $API_TYPE = $user_arr['User']['api_type'];
                $SID = trim($user_arr['User']['sid']);
                $AUTHTOKEN = trim($user_arr['User']['authtoken']);
                $BANDWIDTHUSERID = trim($user_arr['User']['bandwidthuserid']);
                $SIGNALWIRESPACE = trim($user_arr['User']['signalwirespace']);
                $timezone = $user_arr['User']['timezone'];
                $active = $user_arr['User']['active'];
                date_default_timezone_set($timezone);
                if ($response['Responder']['sms_type'] == 2) {
                    if ($user_arr['User']['mms'] == 1) {
                        $assigned_number = $user_arr['User']['assigned_number'];
                    } else {
                        app::import('Model', 'UserNumber');
                        $this->UserNumber = new UserNumber();
                        $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                        if (!empty($mmsnumber)) {
                            $assigned_number = $mmsnumber['UserNumber']['number'];
                        } else {
                            $assigned_number = $user_arr['User']['assigned_number'];
                        }
                    }
                } else {
                    if (!empty($user_arr)) {
                        if ($user_arr['User']['sms'] == 1) {
                            $assigned_number = $user_arr['User']['assigned_number'];
                        } else {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            if (!empty($user_numbers)) {
                                $assigned_number = $user_numbers['UserNumber']['number'];
                            } else {
                                $assigned_number = $user_arr['User']['assigned_number'];
                            }
                        }
                    }
                }
                if ($assigned_number != '' && $active == 1) {
                    if (isset($group_id)) {

                        if ($isday == 1) {
                            $tomorrow1 = mktime(0, 0, 0, date("m"), date("d") - $responderdays, date("Y"));
                            $last_date = date("Y-m-d", $tomorrow1);
                            $contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.subscribed_by_sms !=' => 0, 'DATE(ContactGroup.created)' => $last_date), 'fields' => array('Contact.phone_number', 'Contact.stickysender', 'Contact.name', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                        } else {
                            $hour_date = mktime(date("H") - $responderdays, 0, 0, date("m"), date("d"), date("Y"));
                            $hour = date("H", $hour_date);
                            $full_date = date("Y-m-d", $hour_date);
                            $contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.subscribed_by_sms !=' => 0, 'DATE(ContactGroup.created)' => $full_date, 'HOUR(ContactGroup.created)' => $hour), 'fields' => array('Contact.phone_number', 'Contact.stickysender', 'Contact.name', 'Contact.email', 'Contact.location', 'Contact.state', 'Contact.zip', 'Contact.custom1', 'Contact.custom2', 'Contact.custom3')));
                        }


                        if (!empty($contacts)) {
                            $totalSubscriber = count($contacts);

                            if ($totalSubscriber > 0) {
                                $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                $smsbalance = $users['User']['sms_balance'];
                                $body = $message . " " . $systemmsg;
                                //$length = strlen(utf8_decode(substr($body, 0, 160)));
                                $length = mb_strlen($body,"UTF-8");
			                    $gsm = $this->is_gsm($body);
                                if ($sms_type == 2) {
                                    $contactcredits = CHARGECREDITSMMS;
                                } else {
                                    if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                        $contactcredits = ceil($length / 70);
                                    } else {
                                        $contactcredits = ceil($length / 160);
                                    }
                                }
                                if ($smsbalance >= ($totalSubscriber * $contactcredits)) {
                                    if ($API_TYPE == 0) {
                                        $group_sms_id = '';
                                        $failed = 0;
                                        $this->Twilio->curlinit = curl_init();
                                        $this->Twilio->bulksms = 1;
                                        foreach ($contacts as $contact) {
                                            //$reg_date=$contact['ContactGroup']['created'];
                                            //$date2 = date('Y-m-d H:i:s');
                                            //$seconds = strtotime($date2) - strtotime($reg_date);
                                            //$days = floor($seconds / 86400);
                                            //if($days == $responderdays){
                                            if ($group_sms_id == '') {
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                            }
                                            $to = $contact['Contact']['phone_number'];
                                            $from = $assigned_number;

                                            $stickyfrom = $contact['Contact']['stickysender'];
                                            if ($stickyfrom != 0) {
                                                $from = $stickyfrom;
                                            }

                                            $body = $message . "\n" . $systemmsg;
                                            $space_pos = strpos($contact['Contact']['name'], ' ');
                                            if ($space_pos != '') {
                                                $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                            } else {
                                                $contact_name = $contact['Contact']['name'];
                                            }
                                            $body = str_replace('%%Name%%', $contact_name, $body);
                                            $body = str_replace('%%Email%%', $contact['Contact']['email'], $body);
						                    $body = str_replace('%%City%%', $contact['Contact']['location'], $body);
						                    $body = str_replace('%%State%%', $contact['Contact']['state'], $body);
						                    $body = str_replace('%%Postal%%', $contact['Contact']['zip'], $body);
						                    $body = str_replace('%%Custom1%%', $contact['Contact']['custom1'], $body);
						                    $body = str_replace('%%Custom2%%', $contact['Contact']['custom2'], $body);
						                    $body = str_replace('%%Custom3%%', $contact['Contact']['custom3'], $body);
						                                
                                            $Status = '';
                                            if ($sms_type == 1) {
                                                if ($SID == ''){
                                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                }else{
                                                    $this->Twilio->AccountSid = $SID;
                                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                                }
                                                $response = $this->Twilio->sendsms($to, $from, $body);
                                                $smsid = $response->ResponseXml->Message->Sid;
                                                $Status = $response->ResponseXml->RestException->Status;
                                            } else if ($sms_type == 2) {
                                                if ($SID == ''){
                                                    $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                    $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                }else{
                                                    $this->Mms->AccountSid = $SID;
                                                    $this->Mms->AuthToken = $AUTHTOKEN;
                                                }
                                                $message_arr = explode(',', $image_url);
                                                $response = $this->Mms->sendmms($to, $from, $message_arr, $body);
                                                $smsid = $response->sid;
                                                if ($smsid == '') {
                                                    $ErrorMessage = $response;
                                                    $Status = '400';
                                                }
                                            }
                                            $logArr['id'] = '';
                                            $logArr['group_sms_id'] = $group_sms_id;
                                            $logArr['sms_id'] = $smsid;
                                            $logArr['group_id'] = $group_id;
                                            $logArr['user_id'] = $users['User']['id'];
                                            $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                            $logArr['sendfrom'] = $from;
                                            if ($sms_type == 2) {
                                                $logArr['text_message'] = $body;
                                                $logArr['image_url'] = $image_url;
                                            } else {
                                                $logArr['text_message'] = $body;
                                            }
                                            $logArr['route'] = 'outbox';
                                            $logArr['sms_status'] = '';
                                            $logArr['error_message'] = '';
                                            if ($Status == 400) {
                                                $logArr['sms_status'] = 'failed';
                                                if (isset($response->ErrorMessage)) {
                                                    $ErrorMessage = $response->ErrorMessage;
                                                } else {
                                                    $ErrorMessage = $ErrorMessage;
                                                }
                                                $logArr['error_message'] = $ErrorMessage;
                                                $failed++;
                                            }
                                            $this->Log->save($logArr);
                                            /*if($Status==400){
												    $failed++;
													$groupContacts = $this->GroupSmsBlast->find('first',array('conditions' => array('GroupSmsBlast.id'=>$group_sms_id)));
													app::import('Model','GroupSmsBlast');
													$this->GroupSmsBlast = new GroupSmsBlast();
													$this->GroupSmsBlast->id = $group_sms_id;
													$this->request->data['GroupSmsBlast']['total_failed_messages']=$groupContacts['GroupSmsBlast']['total_failed_messages']+1;
													$this->GroupSmsBlast->save($this->request->data);
												}*/
                                            //}
                                        }
                                        curl_close($this->Twilio->curlinit);
                                        if ($failed > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $failed;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }
                                    } else if ($API_TYPE == 2) {
                                        $group_sms_id = '';
                                        $failed = 0;
                                        $success = 0;
                                        $this->Slooce->curlinit = curl_init();
                                        $this->Slooce->bulksms = 1;
                                        foreach ($contacts as $contact) {
                                            //$reg_date=$contact['ContactGroup']['created'];
                                            //$date2 = date('Y-m-d h:i:s');
                                            //$seconds = strtotime($date2) - strtotime($reg_date);
                                            //$days = floor($seconds / 86400);
                                            //if($days == $responderdays){
                                            if ($group_sms_id == '') {
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                            }
                                            $to = $contact['Contact']['phone_number'];
                                            $from = $assigned_number;
                                            $body = $message . "\n" . $systemmsg;
                                            $response = $this->Slooce->mt($user_arr['User']['api_url'], $user_arr['User']['partnerid'], $user_arr['User']['partnerpassword'], $to, $user_arr['User']['keyword'], $body);
                                            $message_id = '';
                                            $status = '';
                                            if (isset($response['id'])) {
                                                if ($response['result'] == 'ok') {
                                                    $message_id = $response['id'];
                                                }
                                                $status = $response['result'];
                                            }
                                            $logArr['id'] = '';
                                            $logArr['group_sms_id'] = $group_sms_id;
                                            $logArr['sms_id'] = $message_id;
                                            $logArr['group_id'] = $group_id;
                                            $logArr['user_id'] = $users['User']['id'];
                                            $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                            $logArr['sendfrom'] = $from;
                                            $logArr['text_message'] = $body;
                                            $logArr['route'] = 'outbox';
                                            $logArr['sms_status'] = '';
                                            $logArr['error_message'] = '';
                                            if ($status != 'ok') {
                                                $logArr['sms_status'] = 'failed';
                                                $ErrorMessage = $status;
                                                $logArr['error_message'] = $ErrorMessage;
                                                $failed++;
                                            }
                                            if ($message_id != '') {
                                                $logArr['sms_status'] = 'sent';
                                                $success++;
                                            }
                                            $this->Log->save($logArr);
                                        }
                                        curl_close($this->Slooce->curlinit);
                                        if ($failed > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $failed;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }
                                        if ($success > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_successful_messages'] = $success;
                                            $this->GroupSmsBlast->save($this->request->data);

                                            $this->request->data['User']['sms_balance'] = $users['User']['sms_balance'] - $success;
                                            $this->request->data['User']['id'] = $users['User']['id'];
                                            $this->User->save($this->request->data);
                                        }
                                        $this->smsmail($user_id);
                                    } else if ($API_TYPE == 333) { //****** PLIVO moved down below with the other MMS-enabled gateways ***********
                                        $group_sms_id = '';
                                        $failed = 0;
                                        $success = 0;
                                        $this->Plivo->curlinit = curl_init();
                                        $this->Plivo->bulksms = 1;
                                        foreach ($contacts as $contact) {
                                            //$reg_date=$contact['ContactGroup']['created'];
                                            //$date2 = date('Y-m-d h:i:s');
                                            //$seconds = strtotime($date2) - strtotime($reg_date);
                                            //$days = ceil($seconds / 86400);

                                            //if($days == $responderdays){
                                            if ($group_sms_id == '') {
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                            }
                                            $to = $contact['Contact']['phone_number'];
                                            $from = $assigned_number;

                                            $stickyfrom = $contact['Contact']['stickysender'];
                                            if ($stickyfrom != 0) {
                                                $from = $stickyfrom;
                                            }

                                            $body = $message . "\n" . $systemmsg;
                                            if ($SID == ''){
                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                            }else{
                                                $this->Plivo->AuthId = $SID;
                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                            }
                                            //sleep(1);
                                            //$body = $this->process($body);
                                            $response = $this->Plivo->sendsms($to, $from, $body);
                                            $errortext = '';
                                            $message_id = '';
                                            if (isset($response['response']['error'])) {
                                                $errortext = $response['response']['error'];
                                            }
                                            if (isset($response['response']['message_uuid'][0])) {
                                                $message_id = $response['response']['message_uuid'][0];
                                            }
                                            $logArr['id'] = '';
                                            $logArr['group_sms_id'] = $group_sms_id;
                                            $logArr['sms_id'] = $message_id;
                                            $logArr['group_id'] = $group_id;
                                            $logArr['user_id'] = $users['User']['id'];
                                            $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                            $logArr['sendfrom'] = $from;
                                            $logArr['text_message'] = $body;
                                            $logArr['route'] = 'outbox';
                                            $logArr['sms_status'] = '';
                                            $logArr['error_message'] = '';
                                            if (isset($response['response']['error'])) {
                                                $logArr['sms_status'] = 'failed';
                                                $ErrorMessage = $errortext;
                                                $logArr['error_message'] = $ErrorMessage;
                                                $failed++;
                                            }
                                            if ($message_id != '') {
                                                $logArr['sms_status'] = 'sent';
                                                //$length = strlen(utf8_decode(substr($body, 0, 160)));
                                                $length = mb_strlen($body,"UTF-8");
			                                    $gsm = $this->is_gsm($body);
                                                if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                                    $credits = $credits + ceil($length / 70);
                                                } else {
                                                    $credits = $credits + ceil($length / 160);
                                                }
                                                $success++;
                                            }
                                            $this->Log->save($logArr);
                                        }
                                        curl_close($this->Plivo->curlinit);
                                        if ($failed > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $failed;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }
                                        if ($success > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_successful_messages'] = $success;
                                            $this->GroupSmsBlast->save($this->request->data);

                                            $this->request->data['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                                            $this->request->data['User']['id'] = $users['User']['id'];
                                            $this->User->save($this->request->data);
                                        }
                                        $this->smsmail($user_id);
									}else if ($API_TYPE == 3 || $API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {
											$group_sms_id = '';
											$failed = 0;
											$success = 0;
											$credits = 0;
											foreach ($contacts as $contact) {
												if ($group_sms_id == '') {
													app::import('Model', 'GroupSmsBlast');
													$this->GroupSmsBlast = new GroupSmsBlast();
													$this->request->data['GroupSmsBlast']['user_id'] = $user_id;
													$this->request->data['GroupSmsBlast']['group_id'] = $group_id;
													$this->request->data['GroupSmsBlast']['responder'] = 1;
													$this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
													$this->GroupSmsBlast->save($this->request->data);
													$group_sms_id = $this->GroupSmsBlast->id;
												}
												$to = $contact['Contact']['phone_number'];
												$from = $assigned_number;
												$stickyfrom = $contact['Contact']['stickysender'];
												if ($stickyfrom != 0) {
													$from = $stickyfrom;
												}
												$body = $message . "\n" . $systemmsg;
												
												$space_pos = strpos($contact['Contact']['name'], ' ');
                                                if ($space_pos != '') {
                                                    $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                                } else {
                                                    $contact_name = $contact['Contact']['name'];
                                                }
                                                $body = str_replace('%%Name%%', $contact_name, $body);
                                                $body = str_replace('%%Email%%', $contact['Contact']['email'], $body);
						                        $body = str_replace('%%City%%', $contact['Contact']['location'], $body);
						                        $body = str_replace('%%State%%', $contact['Contact']['state'], $body);
						                        $body = str_replace('%%Postal%%', $contact['Contact']['zip'], $body);
						                        $body = str_replace('%%Custom1%%', $contact['Contact']['custom1'], $body);
						                        $body = str_replace('%%Custom2%%', $contact['Contact']['custom2'], $body);
						                        $body = str_replace('%%Custom3%%', $contact['Contact']['custom3'], $body);
						                    
												$Status = '';
												if ($sms_type == 1) {
												    /*if ($SID == ''){
                                                        $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body);
                                                    }else{
                                                        $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body);
                                                    }
													$message_id = '';
													$status = '';
													$errortext = '';
													if(isset($response->message)){
														$errortext = $response->message;
														$status = 'failed';
													}else{
														$status = 'sent';
													}*/
													$message_id = '';
													if($API_TYPE==3){
													    if ($SID == ''){
                                                            $this->Plivo->AuthId = PLIVO_KEY;
                                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                        }else{
                                                            $this->Plivo->AuthId = $SID;
                                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $response = $this->Plivo->sendsms($to, $from, $body);
                                                        $msg_type = 'text';
                                                		$status = '';
                                                        $errortext = '';
                                                        if (isset($response['response']['error'])) {
                                                            $errortext = $response['response']['error'];
                                                            $status = 'failed';
                                                        }
                                                        if (isset($response['response']['message_uuid'][0])) {
                                                            $message_id = $response['response']['message_uuid'][0];
                                                            $status = 'sent';
                                                        }
													}elseif($API_TYPE==5){
													    if ($SID == ''){
                    								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$body);
													    }else{
													        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$body);
													    }
													    $msg_type = 'text';
                                                		$status = '';
                                            			$errortext = '';
                                            			if(isset($response->error_message)){
                                            				$errortext = $response->error_message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $message_id = $response->sid;
                                            				$status = 'sent';
                                            			}
													}elseif($API_TYPE==6){
													    if ($SID == ''){
                    								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$body);
													    }else{
													        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$body);
													    }
													    $msg_type = 'text';
                                                		$status = '';
                                            			$errortext = '';
                                            			if($response->Message360->ResponseStatus == 0){
                                            				$errortext = $response->Message360->Errors->Error[0]->Message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $message_id = $response->Message360->Message->MessageSid;
                                            				$status = 'sent';
                                            			}
													}elseif($API_TYPE==7){
													    if ($SID == ''){
                    								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$body);
													    }else{
													        $response = $this->Telnyx->sendsms($SID,$to,$from,$body);
													    }
													    $msg_type = 'text';
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
                    						                if ($SID == ''){
                                                                $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body);
                                                            }else{
                                                                $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body);
                                                            }
                                                            $msg_type = 'text';
                    					
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
												} else if ($sms_type == 2) {
													$message_arr = explode(',', $image_url);
													/*if ($SID == ''){
                                                        $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body,$message_arr);
                                                    }else{
                                                        $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body,$message_arr);
                                                    }
													$message_id = '';
													$status = '';
													$errortext = '';
													if(isset($response->message)){
														$errortext = $response->message;
														$status = 'failed';
													}else{
														$status = 'sent';
													}*/
													$message_id = '';
													if($API_TYPE==3){
													    if ($SID == ''){
                                                            $this->Plivo->AuthId = PLIVO_KEY;
                                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                        }else{
                                                            $this->Plivo->AuthId = $SID;
                                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $response = $this->Plivo->sendmms($to,$from,$body,$message_arr);
                                                        $msg_type = 'text';
                                                		$status = '';
                                                        $errortext = '';
                                                        if (isset($response['response']['error'])) {
                                                            $errortext = $response['response']['error'];
                                                            $status = 'failed';
                                                        }
                                                        if (isset($response['response']['message_uuid'][0])) {
                                                            $message_id = $response['response']['message_uuid'][0];
                                                            $status = 'sent';
                                                        }
													}elseif($API_TYPE==5){
													    if ($SID == ''){
                    								        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$body,$message_arr);
													    }else{
													        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$body,$message_arr);
													    }
													    $msg_type = 'text';
                                                		$status = '';
                                            			$errortext = '';
                                            			if(isset($response->error_message)){
                                            				$errortext = $response->error_message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $message_id = $response->sid;
                                            				$status = 'sent';
                                            			}
													}elseif($API_TYPE==6){
													    if ($SID == ''){
                    								        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$body,$message_arr);
													    }else{
													        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$body,$message_arr);
													    }
													    $msg_type = 'text';
                                                		$status = '';
                                            			$errortext = '';
                                            			if($response->Message360->ResponseStatus == 0){
                                            				$errortext = $response->Message360->Errors->Error[0]->Message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $message_id = $response->Message360->Message->MessageSid;
                                            				$status = 'sent';
                                            			}
													}elseif($API_TYPE==7){
													    if ($SID == ''){
                    								        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$body,$message_arr);
													    }else{
													        $response = $this->Telnyx->sendmms($SID,$to,$from,$body,$message_arr);
													    }
													    $msg_type = 'text';
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
                    						                if ($SID == ''){
                                                                $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body,$message_arr);
                                                            }else{
                                                                $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body,$message_arr);
                                                            }
                                                            $msg_type = 'text';
                    					
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
												}
												$logArr['id'] = '';
												$logArr['group_sms_id'] = $group_sms_id;
												$logArr['sms_id'] = $message_id;
												$logArr['group_id'] = $group_id;
												$logArr['user_id'] = $users['User']['id'];
												$logArr['phone_number'] = $contact['Contact']['phone_number'];
												$logArr['sendfrom'] = $from;
												if ($sms_type == 2) {
													$logArr['text_message'] = $body;
													$logArr['image_url'] = $image_url;
													if ($errortext == '') {
													    $credits = $credits + CHARGECREDITSMMS;
													}
												} else {
													$logArr['text_message'] = $body;
													if ($errortext == '') {
    													$length = mb_strlen($body,"UTF-8");
                    			                        $gsm = $this->is_gsm($body);
                                                      
                                                        if(!$gsm){
                                                            $credits = $credits + ceil($length / 70);
                                                        } else {
                                                            $credits = $credits + ceil($length / 160);
                                                        }
													}

												}
												$logArr['route'] = 'outbox';
												$logArr['sms_status'] = '';
												$logArr['error_message'] = '';
												if ($errortext !='') {
													$logArr['sms_status'] = $status;
													$logArr['error_message'] = $errortext;
													$failed++;
												}else{
													$logArr['sms_status'] = "sent";
													$success++;
												}												
												$this->Log->save($logArr);
											}
											if ($failed > 0) {
												app::import('Model', 'GroupSmsBlast');
												$this->GroupSmsBlast = new GroupSmsBlast();
												$this->GroupSmsBlast->id = $group_sms_id;
												$this->request->data['GroupSmsBlast']['total_failed_messages'] = $failed;
												$this->GroupSmsBlast->save($this->request->data);
											}
											if ($success > 0) {
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->GroupSmsBlast->id = $group_sms_id;
                                                $this->request->data['GroupSmsBlast']['total_successful_messages'] = $success;
                                                $this->GroupSmsBlast->save($this->request->data);
    
                                                $this->request->data['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                                                $this->request->data['User']['id'] = $users['User']['id'];
                                                $this->User->save($this->request->data);
                                            }
                                            $this->smsmail($user_id);
                                    } else {	
										
                                    
                                        $group_sms_id = '';
                                        $failed = 0;
                                        $success = 0;
                                        foreach ($contacts as $contact) {
                                            //$reg_date=$contact['ContactGroup']['created'];
                                            //$date2 = date('Y-m-d h:i:s');
                                            //$seconds = strtotime($date2) - strtotime($reg_date);
                                            //$days = floor($seconds / 86400);
                                            //if($days == $responderdays){
                                            if ($group_sms_id == '') {
                                                app::import('Model', 'GroupSmsBlast');
                                                $this->GroupSmsBlast = new GroupSmsBlast();
                                                $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                $this->GroupSmsBlast->save($this->request->data);
                                                $group_sms_id = $this->GroupSmsBlast->id;
                                            }
                                            $to = $contact['Contact']['phone_number'];
                                            $from = $assigned_number;

                                            $stickyfrom = $contact['Contact']['stickysender'];
                                            if ($stickyfrom != 0) {
                                                $from = $stickyfrom;
                                            }

                                            $body = $message . "\n" . $systemmsg;
                                            
                                            $space_pos = strpos($contact['Contact']['name'], ' ');
                                            if ($space_pos != '') {
                                                $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                            } else {
                                                $contact_name = $contact['Contact']['name'];
                                            }
                                            $body = str_replace('%%Name%%', $contact_name, $body);
                                            $body = str_replace('%%Email%%', $contact['Contact']['email'], $body);
						                    $body = str_replace('%%City%%', $contact['Contact']['location'], $body);
						                    $body = str_replace('%%State%%', $contact['Contact']['state'], $body);
						                    $body = str_replace('%%Postal%%', $contact['Contact']['zip'], $body);
						                    $body = str_replace('%%Custom1%%', $contact['Contact']['custom1'], $body);
						                    $body = str_replace('%%Custom2%%', $contact['Contact']['custom2'], $body);
						                    $body = str_replace('%%Custom3%%', $contact['Contact']['custom3'], $body);
						                    
                                            if ($SID == ''){
                                                $this->Nexmomessage->Key = NEXMO_KEY;
                                                $this->Nexmomessage->Secret = NEXMO_SECRET;
                                            }else{
                                                $this->Nexmomessage->Key = $SID;
                                                $this->Nexmomessage->Secret = $AUTHTOKEN;
                                            }
                                            sleep(1);
                                            //$body = $this->process($body);
                                            $responsemsg = $this->Nexmomessage->sendsms($to, $from, $body);
                                            foreach ($responsemsg->messages as $doc) {
                                                $message_id = $doc->messageid;
                                                if ($message_id != '') {
                                                    $status = $doc->status;
                                                    $message_id = $doc->messageid;
                                                } else {
                                                    $status = $doc->status;
                                                    $errortext = $doc->errortext;
                                                }
                                            }
                                            $logArr['id'] = '';
                                            $logArr['group_sms_id'] = $group_sms_id;
                                            $logArr['sms_id'] = $message_id;
                                            $logArr['group_id'] = $group_id;
                                            $logArr['user_id'] = $users['User']['id'];
                                            $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                            $logArr['sendfrom'] = $from;
                                            $logArr['text_message'] = $body;
                                            $logArr['route'] = 'outbox';
                                            $logArr['sms_status'] = '';
                                            $logArr['error_message'] = '';
                                            if ($status != 0) {
                                                $logArr['sms_status'] = 'failed';
                                                $ErrorMessage = $errortext;
                                                $logArr['error_message'] = $ErrorMessage;
                                                $failed++;
                                            }
                                            if ($message_id != '') {
                                                $logArr['sms_status'] = 'sent';
                                                //$length = strlen(utf8_decode(substr($body, 0, 160)));
                                                $length = mb_strlen($body,"UTF-8");
			                                    $gsm = $this->is_gsm($body);
                                                if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                                    $credits = $credits + ceil($length / 70);
                                                } else {
                                                    $credits = $credits + ceil($length / 160);
                                                }
                                                $success++;
                                            }
                                            $this->Log->save($logArr);
                                        }
                                        if ($failed > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $failed;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }
                                        if ($success > 0) {
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $this->GroupSmsBlast->id = $group_sms_id;
                                            $this->request->data['GroupSmsBlast']['total_successful_messages'] = $success;
                                            $this->GroupSmsBlast->save($this->request->data);

                                            $this->request->data['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                                            $this->request->data['User']['id'] = $users['User']['id'];
                                            $this->User->save($this->request->data);
                                        }
                                        $this->smsmail($user_id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    function sendbirthday()
    {
        $this->autoRender = false;
        
        app::import('Model', 'Birthday');
        $this->Birthday = new Birthday();
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        app::import('Model', 'Log');
        $this->Log = new Log();
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        app::import('Model', 'User');
        $this->User = new User();
        
        $birthdaydata = $this->Birthday->find('all');
        if (!empty($birthdaydata)) {
            foreach ($birthdaydata as $birthday) {
                $Responderid = $birthday['Birthday']['id'];
                $group_id = $birthday['Birthday']['group_id'];
                $sms_type = $birthday['Birthday']['sms_type'];
                $image_url = $birthday['Birthday']['image_url'];
                $message = $birthday['Birthday']['message'];
                $systemmsg = $birthday['Birthday']['systemmsg'];
                $user_id = $birthday['Birthday']['user_id'];
                $responderdays = $birthday['Birthday']['days'];
                $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                $API_TYPE = $user_arr['User']['api_type'];
                $SID = trim($user_arr['User']['sid']);
                $AUTHTOKEN = trim($user_arr['User']['authtoken']);
                $BANDWIDTHUSERID = trim($user_arr['User']['bandwidthuserid']);
                $SIGNALWIRESPACE = trim($user_arr['User']['signalwirespace']);
                $timezone = $user_arr['User']['timezone'];
                $active = $user_arr['User']['active'];
                date_default_timezone_set($timezone);
                if ($birthday['Birthday']['sms_type'] == 2) {
                    if ($user_arr['User']['mms'] == 1) {
                        $assigned_number = $user_arr['User']['assigned_number'];
                    } else {
                        app::import('Model', 'UserNumber');
                        $this->UserNumber = new UserNumber();
                        $mmsnumber = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
                        if (!empty($mmsnumber)) {
                            $assigned_number = $mmsnumber['UserNumber']['number'];
                        } else {
                            $assigned_number = $user_arr['User']['assigned_number'];
                        }
                    }
                } else {
                    if (!empty($user_arr)) {
                        if ($user_arr['User']['sms'] == 1) {
                            $assigned_number = $user_arr['User']['assigned_number'];
                        } else {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            if (!empty($user_numbers)) {
                                $assigned_number = $user_numbers['UserNumber']['number'];
                            } else {
                                $assigned_number = $user_arr['User']['assigned_number'];
                            }
                        }
                    }
                }
                if ($assigned_number != '' && $active == 1) {
                    if (isset($group_id)) {
                        $tomorrow1 = mktime(0, 0, 0, date("m"), date("d") + $responderdays, date("Y"));
                        $birthday_month = date("m", $tomorrow1);
                        $birthday_date = date("d", $tomorrow1);
                        //$contacts_group = $this->ContactGroup->find('all', array('conditions' => array('Group.bithday_enable'=>1,'ContactGroup.group_id'=>$group_id,'ContactGroup.un_subscribers'=>0,'ContactGroup.subscribed_by_sms != '=>0,'MONTH(Contact.birthday)'=>$birthday_month)));

                        $contacts_group = $this->ContactGroup->find('all', array('conditions' => array('Group.bithday_enable' => 1, 'ContactGroup.group_id' => $group_id, 'ContactGroup.un_subscribers' => 0, 'MONTH(Contact.birthday)' => $birthday_month)));

                        if (!empty($contacts_group)) {

                            foreach ($contacts_group as $contacts_groups) {
                                $contacts = $this->Contact->find('all', array('conditions' => array('Contact.id' => $contacts_groups['ContactGroup']['contact_id'], 'MONTH(Contact.birthday)' => $birthday_month, 'DAY(Contact.birthday)' => $birthday_date)));
                                if (!empty($contacts)) {
                                    $totalSubscriber = count($contacts);
                                    if ($totalSubscriber > 0) {
                                        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                        $smsbalance = $users['User']['sms_balance'];
                                        $body = $message . " " . $systemmsg;
                                        //$length = strlen(utf8_decode(substr($body, 0, 160)));
                                        $length = mb_strlen($body,"UTF-8");
			                            $gsm = $this->is_gsm($body);
                                        if ($sms_type == 2) {
                                            $contactcredits = CHARGECREDITSMMS;
                                        } else {
                                            if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                                $contactcredits = ceil($length / 70);
                                            } else {
                                                $contactcredits = ceil($length / 160);
                                            }
                                        }
                                        if ($smsbalance >= ($totalSubscriber * $contactcredits)) {
                                            if ($API_TYPE == 0) {
                                                $group_sms_id = '';
                                                $this->Twilio->curlinit = curl_init();
                                                $this->Twilio->bulksms = 1;
                                                foreach ($contacts as $contact) {
                                                    if ($group_sms_id == '') {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                        $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                        $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                        $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                        $group_sms_id = $this->GroupSmsBlast->id;
                                                    }
                                                    $to = $contact['Contact']['phone_number'];
                                                    $space_pos = strpos($contact['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $contact['Contact']['name'];
                                                    }
                                                    $newmessage = str_replace('%%Name%%', $contact_name, $message);
                                                    $from = $assigned_number;

                                                    $stickyfrom = $contact['Contact']['stickysender'];
                                                    if ($stickyfrom != 0) {
                                                        $from = $stickyfrom;
                                                    }

                                                    $body = $newmessage . "\n" . $systemmsg;
                                                    $Status = '';
                                                    if ($sms_type == 1) {
                                                        if ($SID == ''){
                                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                        }else{
                                                            $this->Twilio->AccountSid = $SID;
                                                            $this->Twilio->AuthToken = $AUTHTOKEN;
                                                        }
                                                        //$body = $this->process($body);
                                                        $response = $this->Twilio->sendsms($to, $from, $body);
                                                        $smsid = $response->ResponseXml->Message->Sid;
                                                        $Status = $response->ResponseXml->RestException->Status;
                                                    } else if ($sms_type == 2) {
                                                        if ($SID == ''){
                                                            $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                            $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                        }else{
                                                            $this->Mms->AccountSid = $SID;
                                                            $this->Mms->AuthToken = $AUTHTOKEN;
                                                        }
                                                        $message_arr = explode(',', $image_url);
                                                        //$body = $this->process($body);
                                                        $response = $this->Mms->sendmms($to, $from, $message_arr, $body);
                                                        $smsid = $response->sid;
                                                        if ($smsid == '') {
                                                            $ErrorMessage = $response;
                                                            $Status = '400';
                                                        }
                                                    }
                                                    $logArr['id'] = '';
                                                    $logArr['group_sms_id'] = $group_sms_id;
                                                    $logArr['sms_id'] = $smsid;
                                                    $logArr['group_id'] = $group_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                                    $logArr['sendfrom'] = $from;
                                                    if ($sms_type == 2) {
                                                        $logArr['text_message'] = $body;
                                                        $logArr['image_url'] = $image_url;
                                                    } else {
                                                        $logArr['text_message'] = $body;
                                                    }
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($Status == 400) {
                                                        $logArr['sms_status'] = 'failed';
                                                        if (isset($response->ErrorMessage)) {
                                                            $ErrorMessage = $response->ErrorMessage;
                                                        } else {
                                                            $ErrorMessage = $ErrorMessage;
                                                        }
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    $this->Log->save($logArr);
                                                    if ($Status == 400) {
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->GroupSmsBlast->id = $group_sms_id;
                                                        $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                    }
                                                }
                                                curl_close($this->Twilio->curlinit);
                                            } else if ($API_TYPE == 2) {
                                                $group_sms_id = '';
                                                $this->Slooce->curlinit = curl_init();
                                                $this->Slooce->bulksms = 1;
                                                foreach ($contacts as $contact) {
                                                    if ($group_sms_id == '') {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                        $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                        $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                        $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                        $group_sms_id = $this->GroupSmsBlast->id;
                                                    }
                                                    $to = $contact['Contact']['phone_number'];
                                                    $from = $assigned_number;
                                                    $space_pos = strpos($contact['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $contact['Contact']['name'];
                                                    }
                                                    $newmessage = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $newmessage . "\n" . $systemmsg;
                                                    $response = $this->Slooce->mt($user_arr['User']['api_url'], $user_arr['User']['partnerid'], $user_arr['User']['partnerpassword'], $to, $user_arr['User']['keyword'], $body);
                                                    $message_id = '';
                                                    $status = '';
                                                    if (isset($response['id'])) {
                                                        if ($response['result'] == 'ok') {
                                                            $message_id = $response['id'];
                                                        }
                                                        $status = $response['result'];
                                                    }
                                                    $logArr['id'] = '';
                                                    $logArr['group_sms_id'] = $group_sms_id;
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['group_id'] = $group_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                                    $logArr['sendfrom'] = $from;
                                                    $logArr['text_message'] = $body;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($status != 'ok') {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $status;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                    if ($message_id != '') {
                                                        app::import('Model', 'User');
                                                        $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($usersms)) {
                                                            $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - 1;
                                                            $this->request->data['User']['id'] = $usersms['User']['id'];
                                                            $this->User->save($this->request->data);
                                                        }
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        if (!empty($groupContacts)) {
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                                            $GroupSmsBlast_arr['GroupSmsBlast']['id'] = $group_sms_id;
                                                            $GroupSmsBlast_arr['GroupSmsBlast']['total_successful_messages'] = $groupContacts['GroupSmsBlast']['total_successful_messages'] + 1;
                                                            $this->GroupSmsBlast->save($GroupSmsBlast_arr);
                                                        }
                                                        $this->smsmail($user_id);

                                                    }
                                                    if ($status != 'ok') {
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->GroupSmsBlast->id = $group_sms_id;
                                                        $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                    }
                                                }
                                                curl_close($this->Slooce->curlinit);
                                            } else if ($API_TYPE == 333) { //****** PLIVO moved down below with the other MMS-enabled gateways ***********
                                                $group_sms_id = '';
                                                $this->Plivo->curlinit = curl_init();
                                                $this->Plivo->bulksms = 1;
                                                foreach ($contacts as $contact) {
                                                    if ($group_sms_id == '') {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                        $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                        $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                        $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                        $group_sms_id = $this->GroupSmsBlast->id;
                                                    }
                                                    $to = $contact['Contact']['phone_number'];
                                                    $from = $assigned_number;

                                                    $stickyfrom = $contact['Contact']['stickysender'];
                                                    if ($stickyfrom != 0) {
                                                        $from = $stickyfrom;
                                                    }

                                                    $space_pos = strpos($contact['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $contact['Contact']['name'];
                                                    }
                                                    $newmessage = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $newmessage . "\n" . $systemmsg;
                                                    if ($SID == ''){
                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                    }else{
                                                        $this->Plivo->AuthId = $SID;
                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                    }
                                                    //sleep(1);
                                                    //$body = $this->process($body);
                                                    $response = $this->Plivo->sendsms($to, $from, $body);
                                                    $errortext = '';
                                                    $message_id = '';
                                                    if (isset($response['response']['error'])) {
                                                        $errortext = $response['response']['error'];
                                                    }
                                                    if (isset($response['response']['message_uuid'][0])) {
                                                        $message_id = $response['response']['message_uuid'][0];
                                                    }
                                                    $logArr['id'] = '';
                                                    $logArr['group_sms_id'] = $group_sms_id;
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['group_id'] = $group_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                                    $logArr['sendfrom'] = $from;
                                                    $logArr['text_message'] = $body;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if (isset($response['response']['error'])) {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $errortext;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                    if ($message_id != '') {
                                                        app::import('Model', 'User');
                                                        $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($usersms)) {
                                                            //$length = strlen(utf8_decode(substr($body, 0, 160)));
                                                            $length = mb_strlen($body,"UTF-8");
			                                                $gsm = $this->is_gsm($body);
                                                            if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                                                $credits = ceil($length / 70);
                                                            } else {
                                                                $credits = ceil($length / 160);
                                                            }
                                                            //$this->request->data['User']['sms_balance']=$usersms['User']['sms_balance']-1;
                                                            $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                            $this->request->data['User']['id'] = $usersms['User']['id'];
                                                            $this->User->save($this->request->data);
                                                        }
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        if (!empty($groupContacts)) {
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                                            $GroupSmsBlast_arr['GroupSmsBlast']['id'] = $group_sms_id;
                                                            $GroupSmsBlast_arr['GroupSmsBlast']['total_successful_messages'] = $groupContacts['GroupSmsBlast']['total_successful_messages'] + 1;
                                                            $this->GroupSmsBlast->save($GroupSmsBlast_arr);
                                                        }
                                                        $this->smsmail($user_id);

                                                    }
                                                    if (isset($response['response']['error'])) {
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->GroupSmsBlast->id = $group_sms_id;
                                                        $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                    }
                                                }
                                                curl_close($this->Plivo->curlinit);
                                            } else if ($API_TYPE == 3 || $API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {
                                                $group_sms_id = '';
                                                $success = 0;
                                                $credits = 0;
                                                foreach ($contacts as $contact) {
                                                    if ($group_sms_id == '') {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                        $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                        $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                        $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                        $group_sms_id = $this->GroupSmsBlast->id;
                                                    }
                                                    $to = $contact['Contact']['phone_number'];
                                                    $space_pos = strpos($contact['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $contact['Contact']['name'];
                                                    }
                                                    $newmessage = str_replace('%%Name%%', $contact_name, $message);
                                                    $from = $assigned_number;

                                                    $stickyfrom = $contact['Contact']['stickysender'];
                                                    if ($stickyfrom != 0) {
                                                        $from = $stickyfrom;
                                                    }
                                                    $body = $newmessage . "\n" . $systemmsg;
                                                    $status = '';
													$message_id = '';
													$status = '';
													$errortext = '';
                                                    if ($sms_type == 1) {
                                                        /*if ($SID == ''){
                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body);
                                                        }else{
                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body);
                                                        }
														if(isset($response->message)){
															$errortext = $response->message;
															$status = 'failed';
														}else{
															$status = 'sent';
														}*/
														$message_id = '';
														if($API_TYPE==3){
														    if ($SID == ''){
                                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                            }else{
                                                                $this->Plivo->AuthId = $SID;
                                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                                            }
                                                            $response = $this->Plivo->sendsms($to, $from, $body);
                                                            $msg_type = 'text';
                                                    		$status = '';
                                                            $errortext = '';
                                                            if (isset($response['response']['error'])) {
                                                                $errortext = $response['response']['error'];
                                                                $status = 'failed';
                                                            }
                                                            if (isset($response['response']['message_uuid'][0])) {
                                                                $message_id = $response['response']['message_uuid'][0];
                                                                $status = 'sent';
                                                            }
														}elseif($API_TYPE==5){
    													    if ($SID == ''){
                        								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$body);
    													    }else{
    													        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$body);
    													    }
    													    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if(isset($response->error_message)){
                                                				$errortext = $response->error_message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->sid;
                                                				$status = 'sent';
                                                			}
    													}elseif($API_TYPE==6){
    													    if ($SID == ''){
                        								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$body);
    													    }else{
    													        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$body);
    													    }
    													    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if($response->Message360->ResponseStatus == 0){
                                                				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->Message360->Message->MessageSid;
                                                				$status = 'sent';
                                                			}
    													}elseif($API_TYPE==7){
    													    if ($SID == ''){
                        								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$body);
    													    }else{
    													        $response = $this->Telnyx->sendsms($SID,$to,$from,$body);
    													    }
    													    $msg_type = 'text';
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
                        						                if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body);
                                                                }
                                                                $msg_type = 'text';
                        					
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
                                                    } else if ($sms_type == 2) {
                                                        $message_arr = explode(',', $image_url);
                                                        /*if ($SID == ''){
                                                            $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body,$message_arr);
                                                        }else{
                                                            $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body,$message_arr);
                                                        }
														if(isset($response->message)){
															$errortext = $response->message;
															$status = 'failed';
														}else{
															$status = 'sent';
														}*/
														$message_id = '';
														if($API_TYPE==3){
														    if ($SID == ''){
                                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                            }else{
                                                                $this->Plivo->AuthId = $SID;
                                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                                            }
                                                            $response = $this->Plivo->sendmms($to,$from,$body,$message_arr);
                                                            $msg_type = 'text';
                                                    		$status = '';
                                                            $errortext = '';
                                                            if (isset($response['response']['error'])) {
                                                                $errortext = $response['response']['error'];
                                                                $status = 'failed';
                                                            }
                                                            if (isset($response['response']['message_uuid'][0])) {
                                                                $message_id = $response['response']['message_uuid'][0];
                                                                $status = 'sent';
                                                            }
														}elseif($API_TYPE==5){
    													    if ($SID == ''){
                        								        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$body,$message_arr);
    													    }else{
    													        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$body,$message_arr);
    													    }
    													    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if(isset($response->error_message)){
                                                				$errortext = $response->error_message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->sid;
                                                				$status = 'sent';
                                                			}
    													}elseif($API_TYPE==6){
    													    if ($SID == ''){
                        								        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$body,$message_arr);
    													    }else{
    													        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$body,$message_arr);
    													    }
    													    $msg_type = 'text';
                                                    		$status = '';
                                                			$errortext = '';
                                                			if($response->Message360->ResponseStatus == 0){
                                                				$errortext = $response->Message360->Errors->Error[0]->Message;
                                                				$status = 'failed';
                                                			}else{
                                                			    $message_id = $response->Message360->Message->MessageSid;
                                                				$status = 'sent';
                                                			}
    													}elseif($API_TYPE==7){
    													    if ($SID == ''){
                        								        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$body,$message_arr);
    													    }else{
    													        $response = $this->Telnyx->sendmms($SID,$to,$from,$body,$message_arr);
    													    }
    													    $msg_type = 'text';
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
                        						                if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body,$message_arr);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body,$message_arr);
                                                                }
                                                                $msg_type = 'text';
                        					
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
                                                    }
                                                    $logArr['id'] = '';
                                                    $logArr['group_sms_id'] = $group_sms_id;
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['group_id'] = $group_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                                    $logArr['sendfrom'] = $from;
                                                    if ($sms_type == 2) {
                                                        $logArr['text_message'] = $body;
                                                        $logArr['image_url'] = $image_url;
                                                        if ($errortext == '') {
                                                            $credits = $credits + CHARGECREDITSMMS;
                                                        }
                                                    } else {
                                                        $logArr['text_message'] = $body;
                                                        if ($errortext == '') {
                                                            $length = mb_strlen($body,"UTF-8");
                        			                        $gsm = $this->is_gsm($body);
                                                          
                                                            if(!$gsm){
                                                                $credits = $credits + ceil($length / 70);
                                                            } else {
                                                                $credits = $credits + ceil($length / 160);
                                                            }
                                                        }

                                                    }
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($errortext !='') {
                                                        $logArr['sms_status'] = $status;
                                                        $logArr['error_message'] = $errortext;
                                                    }else{
														$logArr['sms_status'] = $status;
														$success = $success + 1;
													}
                                                    $this->Log->save($logArr);
                                                     if ($errortext !='') {
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->GroupSmsBlast->id = $group_sms_id;
                                                        $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                    }
                                                }
                                                
                                                if ($success > 0) {
                                                    app::import('Model', 'GroupSmsBlast');
                                                    $this->GroupSmsBlast = new GroupSmsBlast();
                                                    $this->GroupSmsBlast->id = $group_sms_id;
                                                    $this->request->data['GroupSmsBlast']['total_successful_messages'] = $success;
                                                    $this->GroupSmsBlast->save($this->request->data);
        
                                                    $this->request->data['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                                                    $this->request->data['User']['id'] = $users['User']['id'];
                                                    $this->User->save($this->request->data);
                                                }
                                                $this->smsmail($user_id);
                                            } else {
                                                $group_sms_id = '';
                                                foreach ($contacts as $contact) {
                                                    if ($group_sms_id == '') {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                                                        $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
                                                        $this->request->data['GroupSmsBlast']['responder'] = 1;
                                                        $this->request->data['GroupSmsBlast']['totals'] = $totalSubscriber;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                        $group_sms_id = $this->GroupSmsBlast->id;
                                                    }
                                                    $to = $contact['Contact']['phone_number'];
                                                    $from = $assigned_number;

                                                    $stickyfrom = $contact['Contact']['stickysender'];
                                                    if ($stickyfrom != 0) {
                                                        $from = $stickyfrom;
                                                    }

                                                    $space_pos = strpos($contact['Contact']['name'], ' ');
                                                    if ($space_pos != '') {
                                                        $contact_name = substr($contact['Contact']['name'], 0, $space_pos);
                                                    } else {
                                                        $contact_name = $contact['Contact']['name'];
                                                    }
                                                    $newmessage = str_replace('%%Name%%', $contact_name, $message);
                                                    $body = $newmessage . "\n" . $systemmsg;
                                                    if ($SID == ''){
                                                        $this->Nexmomessage->Key = NEXMO_KEY;
                                                        $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                    }else{
                                                        $this->Nexmomessage->Key = $SID;
                                                        $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                    }
                                                    sleep(1);
                                                    //$body = $this->process($body);
                                                    $responsemsg = $this->Nexmomessage->sendsms($to, $from, $body);
                                                    foreach ($responsemsg->messages as $doc) {
                                                        $message_id = $doc->messageid;
                                                        if ($message_id != '') {
                                                            $status = $doc->status;
                                                            $message_id = $doc->messageid;
                                                        } else {
                                                            $status = $doc->status;
                                                            $errortext = $doc->errortext;
                                                        }
                                                    }
                                                    $logArr['id'] = '';
                                                    $logArr['group_sms_id'] = $group_sms_id;
                                                    $logArr['sms_id'] = $message_id;
                                                    $logArr['group_id'] = $group_id;
                                                    $logArr['user_id'] = $users['User']['id'];
                                                    $logArr['phone_number'] = $contact['Contact']['phone_number'];
                                                    $logArr['sendfrom'] = $from;
                                                    $logArr['text_message'] = $body;
                                                    $logArr['route'] = 'outbox';
                                                    $logArr['sms_status'] = '';
                                                    $logArr['error_message'] = '';
                                                    if ($status != 0) {
                                                        $logArr['sms_status'] = 'failed';
                                                        $ErrorMessage = $errortext;
                                                        $logArr['error_message'] = $ErrorMessage;
                                                    }
                                                    if ($message_id != '') {
                                                        $logArr['sms_status'] = 'sent';
                                                    }
                                                    $this->Log->save($logArr);
                                                    if ($message_id != '') {
                                                        app::import('Model', 'User');
                                                        $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($usersms)) {
                                                            //$length = strlen(utf8_decode(substr($body, 0, 160)));
                                                            $length = mb_strlen($body,"UTF-8");
			                                                $gsm = $this->is_gsm($body);
                                                            if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                                                $credits = ceil($length / 70);
                                                            } else {
                                                                $credits = ceil($length / 160);
                                                            }
                                                            //$this->request->data['User']['sms_balance']=$usersms['User']['sms_balance']-1;
                                                            $this->request->data['User']['sms_balance'] = $usersms['User']['sms_balance'] - $credits;
                                                            $this->request->data['User']['id'] = $usersms['User']['id'];
                                                            $this->User->save($this->request->data);
                                                        }
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        if (!empty($groupContacts)) {
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                                            $GroupSmsBlast_arr['GroupSmsBlast']['id'] = $group_sms_id;
                                                            $GroupSmsBlast_arr['GroupSmsBlast']['total_successful_messages'] = $groupContacts['GroupSmsBlast']['total_successful_messages'] + 1;
                                                            $this->GroupSmsBlast->save($GroupSmsBlast_arr);
                                                        }
                                                        $this->smsmail($user_id);

                                                    }
                                                    if ($status != 0) {
                                                        $groupContacts = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $group_sms_id)));
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $this->GroupSmsBlast->id = $group_sms_id;
                                                        $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupContacts['GroupSmsBlast']['total_failed_messages'] + 1;
                                                        $this->GroupSmsBlast->save($this->request->data);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function updateclicks($cronjob = 0)
    {
        $this->autoRender = false;
        app::import('Model', 'Shortlink');
        $this->Shortlink = new Shortlink();
        $Shortlinkdetails = $this->Shortlink->find('all', array('order' => array('Shortlink.url' => 'asc')));
        if (!empty($Shortlinkdetails)) {
            if (BITLY_ACCESS_TOKEN != '') {
                foreach ($Shortlinkdetails as $Shortlinkdetail) {
                    $shorturl = $Shortlinkdetail['Shortlink']['short_url'];
                    $domain = str_replace(array('http://','https://'), '', $shorturl);
                    //$url = 'https://api-ssl.bitly.com/v3/link/clicks?access_token=' . $access_token . '&link=' . $Shortlinkdetail['Shortlink']['short_url'] . '';
                    $url = 'https://api-ssl.bitly.com/v4/bitlinks/'.$domain.'/clicks';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    $headers = array(
        		    'Authorization: Bearer '. BITLY_ACCESS_TOKEN
           		    );
        		    array_push($headers, "Content-Type:application/json");
        			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
                    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $jsonresponse = json_decode($result);
          
                    if(isset($jsonresponse->link_clicks)){
                        $link_clicks = $jsonresponse->link_clicks[0]->clicks;
                        $data['Shortlink']['id'] = $Shortlinkdetail['Shortlink']['id'];
                        $data['Shortlink']['clicks'] = $link_clicks;
                        $this->Shortlink->save($data);
                    }
                }
            }
        }

        if ($cronjob == 0) {
            $this->Session->setFlash('Clicks have been refreshed');
            $this->redirect(array('controller' => 'users', 'action' => 'shortlinks'));
        }
    }

    function process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );
    }

    function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }

    function loyaltycron()
    {
        $this->autoRender = false;
        app::import('Model', 'Smsloyalty');
        $this->Smsloyalty = new Smsloyalty();
        $loyalty_arr = $this->Smsloyalty->find('all');
        if (!empty($loyalty_arr)) {
            foreach ($loyalty_arr as $loyalty) {
                $couponcode = $this->couponcode(6);
                $loyalty_update['Smsloyalty']['id'] = $loyalty['Smsloyalty']['id'];
                $loyalty_update['Smsloyalty']['coupancode'] = $couponcode;
                if ($this->Smsloyalty->save($loyalty_update)) {
                    if ($loyalty['Smsloyalty']['notify_punch_code'] == 1) {
                        if ($loyalty['Smsloyalty']['my_email_address'] == 1) {
                            $username = $loyalty['User']['username'];
                            $program_name = $loyalty['Smsloyalty']['program_name'];
                            $email = $loyalty['User']['email'];
                            $subject = "New Daily Punch Code";
                            $sitename = str_replace(' ', '', SITENAME);
                            /*$this->Email->to = $email;
                            $this->Email->subject = $subject;
                            $this->Email->from = $sitename;
                            $this->Email->template = 'new_coupon_code';
                            $this->Email->sendAs = 'html';
                            $this->Email->Controller->set('username', $username);
                            $this->Email->Controller->set('couponcode', $couponcode);
                            $this->Email->Controller->set('programname', $program_name);
                            $this->Email->send();*/
                            
                            $Email = new CakeEmail();
                            if(EMAILSMTP==1){
                                $Email->config('smtp');
                            }
                            $Email->from(array(SUPPORT_EMAIL => SITENAME));
                            $Email->to($email);
                            $Email->subject($subject);
                            $Email->template('new_coupon_code');
                            $Email->emailFormat('html');
                            $Email->viewVars(array('username' => $username));
                            $Email->viewVars(array('couponcode' => $couponcode));
                            $Email->viewVars(array('programname' => $program_name));
                            $Email->send();
                            
                        }
                        if ($loyalty['Smsloyalty']['email_address'] == 1) {
                            $username = $loyalty['User']['username'];
                            $program_name = $loyalty['Smsloyalty']['program_name'];
                            $email = $loyalty['Smsloyalty']['email_address_input'];
                            $subject = "New Daily Punch Code";
                            $sitename = str_replace(' ', '', SITENAME);
                            /*$this->Email->to = $email;
                            $this->Email->subject = $subject;
                            $this->Email->from = $sitename;
                            $this->Email->template = 'new_coupon_code';
                            $this->Email->sendAs = 'html';
                            $this->Email->Controller->set('username', $username);
                            $this->Email->Controller->set('couponcode', $couponcode);
                            $this->Email->Controller->set('programname', $program_name);
                            $this->Email->send();*/
                            
                            $Email = new CakeEmail();
                            if(EMAILSMTP==1){
                                $Email->config('smtp');
                            }
                            $Email->from(array(SUPPORT_EMAIL => SITENAME));
                            $Email->to($email);
                            $Email->subject($subject);
                            $Email->template('new_coupon_code');
                            $Email->emailFormat('html');
                            $Email->viewVars(array('username' => $username));
                            $Email->viewVars(array('couponcode' => $couponcode));
                            $Email->viewVars(array('programname' => $program_name));
                            $Email->send();
                        }
                        if ($loyalty['Smsloyalty']['mobile_number'] == 1) {
                            $to = $loyalty['Smsloyalty']['mobile_number_input'];
                            $from = $loyalty['User']['assigned_number'];
                            $program_name = $loyalty['Smsloyalty']['program_name'];
                            $body = 'Here is the new daily Punch Code: ' . $couponcode . ' for loyalty program ' . $program_name;
                            $usersms = $this->User->find('first', array('conditions' => array('User.id' => $loyalty['Smsloyalty']['user_id'])));
                            $API_TYPE = $usersms['User']['api_type'];
                            $SID = trim($usersms['User']['sid']);
                            $AUTHTOKEN = trim($usersms['User']['authtoken']);
                            $BANDWIDTHUSERID = trim($usersms['User']['bandwidthuserid']);
                            $SIGNALWIRESPACE = trim($usersms['User']['signalwirespace']);
                            if (!empty($usersms)) {
                                $this->User->id = $loyalty['Smsloyalty']['user_id'];
                                $sms_balance = $usersms['User']['sms_balance'] - 1;
                                $this->User->saveField('sms_balance', $sms_balance);
                            }
                            if ($API_TYPE == 0) {
                                if ($SID == ''){
                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                }else{
                                    $this->Twilio->AccountSid = $SID;
                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                }
                                $this->Twilio->sendsms($to, $from, $body);
                            } else if ($API_TYPE ==4) {
                                if ($SID == ''){
                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body);
                                }else{
                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body);
                                }
                            } else if ($API_TYPE ==5) {
                                if ($SID == ''){
                                    $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$body);
                                }else{
                                    $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$body);
                                }
                            } else if ($API_TYPE ==6) {
                                if ($SID == ''){
                                    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$body);
                                }else{
                                    $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$body);
                                }
                            } else if ($API_TYPE ==7) {
                                if ($SID == ''){
                                    $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$body);
                                }else{
                                    $response = $this->Telnyx->sendsms($SID,$to,$from,$body);
                                }
                            } else if ($API_TYPE == 2) {
                                $response = $this->Slooce->mt($loyalty['User']['api_url'], $loyalty['User']['partnerid'], $loyalty['User']['partnerpassword'], $to, $loyalty['User']['keyword'], $body);
                            } else if ($API_TYPE == 3) {
                                if ($SID == ''){
                                    $this->Plivo->AuthId = PLIVO_KEY;
                                    $this->Plivo->AuthToken = PLIVO_TOKEN;
                                }else{
                                    $this->Plivo->AuthId = $SID;
                                    $this->Plivo->AuthToken = $AUTHTOKEN;
                                }
                                $this->Plivo->sendsms($to, $from, $body);
                            } else {
                                if ($SID == ''){
                                    $this->Nexmomessage->Key = NEXMO_KEY;
                                    $this->Nexmomessage->Secret = NEXMO_SECRET;
                                }else{
                                    $this->Nexmomessage->Key = $SID;
                                    $this->Nexmomessage->Secret = $AUTHTOKEN;
                                }
                                sleep(1);
                                $this->Nexmomessage->sendsms($to, $from, $body);
                            }
                        }
                    }
                }
            }
        }

    }

    function couponcode($digits)
    {
        $this->autoRender = false;
        srand((double)microtime() * 10000000);
        $input = array("A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        //$input = array("0","1","2","3","4","5","6","7","8","9");
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
    
    function send_voicemessage()
    {
        $this->autoRender = false;
        ob_start();
        app::import('Model', 'ScheduleMessageGroup');
        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
        app::import('Model', 'ScheduleMessage');
        $this->ScheduleMessage = new ScheduleMessage();
        app::import('Model', 'Log');
        $this->Log = new Log();
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        app::import('Model', 'User');
        $this->User = new User();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        app::import('Model', 'SingleScheduleMessage');
        $this->SingleScheduleMessage = new SingleScheduleMessage();
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        $schedule_arr = $this->ScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.msg_type' => 3, 'ScheduleMessage.sent' => 0)), array('fields' => array('DISTINCT ScheduleMessage.user_id')));

        foreach ($schedule_arr as $users) {
            $Uid = $users['ScheduleMessage']['user_id'];
            $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $Uid)));
            $API_TYPE = $user_arr['User']['api_type'];
            $SID = trim($user_arr['User']['sid']);
            $AUTHTOKEN = trim($user_arr['User']['authtoken']);
            $BANDWIDTHUSERID = trim($user_arr['User']['bandwidthuserid']);
            $SIGNALWIRESPACE = trim($user_arr['User']['signalwirespace']);
            $active = $user_arr['User']['active'];
            $voicebroadcast = $user_arr['User']['voicebroadcast'];
            $telnyx_callappid = trim($user_arr['User']['call_app_id']);

            if ($Uid != '' && $active == 1 && $voicebroadcast == 1) {
                $timezone = $user_arr['User']['timezone'];
                date_default_timezone_set($timezone);
                $date = date('Y-m-d H:i:s');
                //echo "<br>";
                $schedulemessages = $this->ScheduleMessageGroup->find('all', array('conditions' => array('ScheduleMessage.msg_type' => 3, 'ScheduleMessage.sent' => 0, 'ScheduleMessage.send_on <= ' => $date, 'ScheduleMessage.user_id' => $Uid)));
                if (!empty($schedulemessages)) {
                    foreach ($schedulemessages as $schedulemessage) {
                        $count = 0;
                        $group_id = $schedulemessage['Group']['id'];
                        $message = $schedulemessage['ScheduleMessage']['message'];
                        $sent_on = $schedulemessage['ScheduleMessage']['send_on'];
                        $user_id = $schedulemessage['ScheduleMessage']['user_id'];
                        $rotate = $schedulemessage['ScheduleMessage']['rotate_number'];
                        $throttle = $schedulemessage['ScheduleMessage']['throttle'];
                        $sendfrom = $schedulemessage['ScheduleMessage']['sendfrom'];
                        $repeat = $schedulemessage['ScheduleMessage']['repeatmsg'];
                        $language = $schedulemessage['ScheduleMessage']['language'];
                        $pause = $schedulemessage['ScheduleMessage']['pause'];
                        $forward = $schedulemessage['ScheduleMessage']['forward'];
                        $forward_number = $schedulemessage['ScheduleMessage']['forwardnumber'];

                        if (isset($group_id)) {
                            $Subscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_id, 'ContactGroup.do_not_call' => 0, 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                            if (!empty($Subscriber)) {
                                $totalSubscriber = count($Subscriber);
                                if ($totalSubscriber > 0) {
                                    $scheduleId = $schedulemessage['ScheduleMessage']['id'];
                                    $this->ScheduleMessage->id = $scheduleId;
                                    $this->request->data['ScheduleMessage']['sent'] = 1;
                                    $this->ScheduleMessage->save($this->request->data);
                                                            
                                    //$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    
                                    if ($rotate == 1) {
                                        app::import('Model', 'UserNumber');
                                        $this->UserNumber = new UserNumber();
                                        $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id)));
                                        $from_arr = array();
                                        if (!empty($user_arr)) {
                                            if ($user_arr['User']['voice'] == 1) {
                                                $from_arr[] = $user_arr['User']['assigned_number'];
                                            }
                                        }
                                        if (!empty($user_numbers)) {
                                            foreach ($user_numbers as $values) {
                                                if ($values['UserNumber']['voice'] == 1) {
                                                    $from_arr[] = $values['UserNumber']['number'];
                                                }
                                            }
                                        }
                                    }
                                    
                                    if ($API_TYPE == 0) {
                                        if ($rotate == 1) {
                                            $k = 0;
                                            foreach ($Subscriber as $contacts) {
            
                                                if ($throttle > 1) {
                                                    $countnumber = count($from_arr);
                                                    if ($countnumber == $k) {
                                                        $k = 0;
                                                        sleep($throttle);
                                                    }
                                                } else {
                                                    $countnumber = count($from_arr);
                                                    if ($countnumber == $k) {
                                                        $k = 0;
                                                    }
                                                }
            
                                                $callnumber = $contacts['Contact']['phone_number'];
                                                //$callerPhone = $this->request->data['Group']['number'];
                                                $callerPhone = $from_arr[$k];
                                                
                                                $stickyfrom = $contacts['Contact']['stickysender'];
                                                if ($stickyfrom != 0) {
                                                    $callerPhone = $stickyfrom;
                                                }
            
                                                Controller::loadModel('Log');
                                                $logarr['Log']['id'] = '';
                                                $logarr['Log']['user_id'] = $user_id;
                                                $logarr['Log']['group_id'] = $group_id;
                                                $logarr['Log']['phone_number'] = $callnumber;
                                                $logarr['Log']['route'] = 'inbox';
                                                $logarr['Log']['msg_type'] = 'broadcast';
                                                $this->Log->save($logarr);
                                                $lastinsertID = $this->Log->getLastInsertId();
                                                $Sid = '';
                                                if ($lastinsertID > 0) {
                                                    if ($SID == ''){
                                                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                    }else{
                                                        $this->Twilio->AccountSid = $SID;
                                                        $this->Twilio->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $response = $this->Twilio->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);
                                                    $Sid = $response->ResponseXml->Call->Sid;
                                                    if ($Sid == '') {
                                                        $logarr['Log']['id'] = $lastinsertID;
                                                        $logarr['Log']['sms_status'] = 'failed';
                                                        $this->Log->save($logarr);
                                                    }
                                                    $k = $k + 1;
                                                }
                                            }
            
                                        } else {
            
                                            foreach ($Subscriber as $contacts) {
                                                $callnumber = $contacts['Contact']['phone_number'];                                    
                                                $callerPhone = $sendfrom;
                                                Controller::loadModel('Log');
                                                $logarr['Log']['id'] = '';
                                                $logarr['Log']['user_id'] = $user_id;
                                                $logarr['Log']['group_id'] = $group_id;
                                                $logarr['Log']['phone_number'] = $callnumber;
                                                $logarr['Log']['route'] = 'inbox';
                                                $logarr['Log']['msg_type'] = 'broadcast';
                                                $this->Log->save($logarr);
                                                $lastinsertID = $this->Log->getLastInsertId();
                                                $Sid = '';
            
                                                if ($throttle > 1) {
                                                    sleep($throttle);
                                                }
            
                                                if ($lastinsertID > 0) {
                                                    if ($SID == ''){
                                                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                    }else{
                                                        $this->Twilio->AccountSid = $SID;
                                                        $this->Twilio->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $response = $this->Twilio->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);
                                                    $Sid = $response->ResponseXml->Call->Sid;
                                                    if ($Sid == '') {
                                                        $logarr['Log']['id'] = $lastinsertID;
                                                        $logarr['Log']['sms_status'] = 'failed';
                                                        $this->Log->save($logarr);
                                                    }
                                                }
                                            }
                                        }
                                    } else if ($API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {							
                                        if ($rotate == 1) {                                
                                            $k = 0;    
                                            
                                            foreach ($Subscriber as $contacts) {                                    
                                                if ($throttle > 1) {                                        
                                                    $countnumber = count($from_arr);                                        
                                                    if ($countnumber == $k) {                                           
                                                        $k = 0;                                            
                                                        sleep($throttle);                                        
                                                        
                                                    }                                    
                                                    
                                                } else {                                        
                                                    $countnumber = count($from_arr);                                        
                                                    if ($countnumber == $k) {                                            
                                                        $k = 0;                                        
                                                        
                                                    }                                    
                                                    
                                                }                                    
                                                $callnumber = $contacts['Contact']['phone_number'];                                    
                                                $callerPhone = $from_arr[$k];                                    
                                                $stickyfrom = $contacts['Contact']['stickysender'];                                    
                                                if ($stickyfrom != 0) {                                        
                                                    $callerPhone = $stickyfrom;                                    
                                                    
                                                }                                    
                                                Controller::loadModel('Log');                                    
                                                $logarr['Log']['id'] = '';                                    
                                                $logarr['Log']['user_id'] = $user_id;                                    
                                                $logarr['Log']['group_id'] = $group_id;                                    
                                                $logarr['Log']['phone_number'] = $callnumber;                                    
                                                $logarr['Log']['route'] = 'inbox';                                   
                                                $logarr['Log']['msg_type'] = 'broadcast';                                    
                                                $this->Log->save($logarr);                                    
                                                $lastinsertID = $this->Log->getLastInsertId();                                    
                                                $Sid = '';                                    
                                                if ($lastinsertID > 0) {										                                        
                 
                                                    if($API_TYPE==5){
                                                        if ($SID == ''){
            									            $response = $this->Signalwire->callNumberToPEOPLE(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }else{
                                                            $response = $this->Signalwire->callNumberToPEOPLE($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }
                                                		$status = '';
                                            			$errortext = '';
                                            			if(isset($response->error_message)){
                                            				$errortext = $response->error_message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $errortext = '';
                                            				$status = 'sent';
                                            			}
                                                    }elseif($API_TYPE==6){
                                                        if ($SID == ''){
            									            $response = $this->Ytel->callNumberToPEOPLE(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }else{
                                                            $response = $this->Ytel->callNumberToPEOPLE($SID,$AUTHTOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }
                                                		$status = '';
                                            			$errortext = '';
                                            			if($response->Message360->ResponseStatus == 0){
                                            				$errortext = $response->Message360->Errors->Error[0]->Message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $errortext = '';
                                            				$status = 'sent';
                                            			}
                                                    }elseif($API_TYPE==7){
                                                        if ($SID == ''){
            									            $response = $this->Telnyx->callNumberToPEOPLE(TELNYX_KEY,$telnyx_callappid,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }else{
                                                            $response = $this->Telnyx->callNumberToPEOPLE($SID,$telnyx_callappid,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }
                                                		$status = '';
                                            			$errortext = '';
                                            			if(isset($response->errors)){
                                            				$errortext = $response->errors[0]->detail;
                                            				$status = 'failed';
                                            			}else{
                                            			    $errortext = '';
                                            				$status = 'sent';
                                            			}
                    								}else{
                                                        $retry = false;
                        					            $retries = 0;
                        					            $MAX_RETRIES = 15;  
                        					            
                        					            do{
                        					                if ($SID == ''){
                        					                    $response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                        					                }else{
                        					                    $response = $this->Bandwidth->callNumberToPEOPLE($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                        					                }
                        					                if(isset($response->message)){
                        						                $errortext = $response->message;
                        						                $status = 'failed';
                        				
                        						                if(trim($response->code) == "call-rate-limit"){
                                                                    $retry = true;
                                                                    $retries = $retries + 1;
                                                                    sleep(1);
                                                                }else{
                                                                    $retry = false;
                                                                }
                        					                }else{
                        					                    $status = '';
                        						                $errortext = '';
                        						                $retry = false;
                        					                }
                        					                
                        					            } while ($retry && $retries < $MAX_RETRIES);
                    								}
                    					            
                                                    if ($errortext  != '') {                                            
                                                        $logarr['Log']['id'] = $lastinsertID;                                            
                                                        $logarr['Log']['sms_status'] = $status;                                            
                                                        $this->Log->save($logarr);                                        
                                                        
                                                    }                                         
                                                    $k = $k + 1;                                    
                                                    
                                                }                                
                                                
                                            }   
                                           
                                        } else {   
                                           
                                            foreach ($Subscriber as $contacts) {                                    
                                                $callnumber = $contacts['Contact']['phone_number'];                                    
                                                $callerPhone = $sendfrom;                                
                                                Controller::loadModel('Log');                                    
                                                $logarr['Log']['id'] = '';                                    
                                                $logarr['Log']['user_id'] = $user_id;                                    
                                                $logarr['Log']['group_id'] = $group_id;                                    
                                                $logarr['Log']['phone_number'] = $callnumber;                                    
                                                $logarr['Log']['route'] = 'inbox';                                    
                                                $logarr['Log']['msg_type'] = 'broadcast';                                    
                                                $this->Log->save($logarr);                                    
                                                $lastinsertID = $this->Log->getLastInsertId();                                    
                                                $Sid = '';                                    
                                                if ($throttle > 1) {
                                                    sleep($throttle);                  
                                                    
                                                }                                    
                                                if ($lastinsertID > 0) {																				
                      
                                                    if($API_TYPE==5){
            									        //$response = $this->Signalwire->callNumberToPEOPLE(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
            										    if ($SID == ''){
            									            $response = $this->Signalwire->callNumberToPEOPLE(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }else{
                                                            $response = $this->Signalwire->callNumberToPEOPLE($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }
                                                		$status = '';
                                            			$errortext = '';
                                            			if(isset($response->error_message)){
                                            				$errortext = $response->error_message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $errortext = '';
                                            				$status = 'sent';
                                            			}
                                                    }elseif($API_TYPE==6){
            									        //$response = $this->Ytel->callNumberToPEOPLE(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
            										    if ($SID == ''){
            									            $response = $this->Ytel->callNumberToPEOPLE(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }else{
                                                            $response = $this->Ytel->callNumberToPEOPLE($SID,$AUTHTOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }
                                                		$status = '';
                                            			$errortext = '';
                                            			if($response->Message360->ResponseStatus == 0){
                                            				$errortext = $response->Message360->Errors->Error[0]->Message;
                                            				$status = 'failed';
                                            			}else{
                                            			    $errortext = '';
                                            				$status = 'sent';
                                            			}
                                                    }elseif($API_TYPE==7){
            									        //$response = $this->Ytel->callNumberToPEOPLE(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
            										    if ($SID == ''){
            									            $response = $this->Telnyx->callNumberToPEOPLE(TELNYX_KEY,$telnyx_callappid,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }else{
                                                            $response = $this->Telnyx->callNumberToPEOPLE($SID,$telnyx_callappid,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
                                                        }
                                                		$status = '';
                                            			$errortext = '';
                                            			if(isset($response->errors)){
                                            				$errortext = $response->errors[0]->detail;
                                            				$status = 'failed';
                                            			}else{
                                            			    $errortext = '';
                                            				$status = 'sent';
                                            			}
                    								}else{
                                                    
                                                        $retry = false;
                        					            $retries = 0;
                        					            $MAX_RETRIES = 15;  
                        					            
                        					            do{
                        					                //$response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                                                            if ($SID == ''){
                        					                    $response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                        					                }else{
                        					                    $response = $this->Bandwidth->callNumberToPEOPLE($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                        					                }
                        					                
                        					                if(isset($response->message)){
                        						                $errortext = $response->message;
                        						                $status = 'failed';
                        				
                        						                if(trim($response->code) == "call-rate-limit"){
                                                                    $retry = true;
                                                                    $retries = $retries + 1;
                                                                    sleep(1);
                                                                }else{
                                                                    $retry = false;
                                                                }
                        					                }else{
                        					                    $status = '';
                        						                $errortext = '';
                        						                $retry = false;
                        					                }
                        					                
                        					            } while ($retry && $retries < $MAX_RETRIES);
                    								}
                    					            
                                                    if ($errortext  != '') {                                            
                                                        $logarr['Log']['id'] = $lastinsertID;                                            
                                                        $logarr['Log']['sms_status'] = $status;                                            
                                                        $this->Log->save($logarr);                                        
                                                        
                                                    }                                    
                                                    
                                                }                                
                                                
                                            } 
                                            
                                        }
                                            
                                    } else if ($API_TYPE == 3) {
                                        if ($rotate == 1) {
                                            $k = 0;
            
                                            foreach ($Subscriber as $contacts) {
                                                $callnumber = $contacts['Contact']['phone_number'];
                                                $callerPhone = $from_arr[$k];
            
                                                $stickyfrom = $contacts['Contact']['stickysender'];
                                                if ($stickyfrom != 0) {
                                                    $callerPhone = $stickyfrom;
                                                }
            
                                                $countnumber = count($from_arr);
                                                if ($countnumber == $k) {
                                                    $k = 0;
                                                    sleep($throttle);
                                                }
            
                                                Controller::loadModel('Log');
                                                $logarr['Log']['id'] = '';
                                                $logarr['Log']['user_id'] = $user_id;
                                                $logarr['Log']['group_id'] = $group_id;
                                                $logarr['Log']['phone_number'] = $callnumber;
                                                $logarr['Log']['route'] = 'inbox';
                                                $logarr['Log']['msg_type'] = 'broadcast';
                                                $this->Log->save($logarr);
                                                $lastinsertID = $this->Log->getLastInsertId();
                                                $Sid = '';
                                                if ($lastinsertID > 0) {
                                                    if ($SID == ''){
                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                    }else{
                                                        $this->Plivo->AuthId = $SID;
                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $response = $this->Plivo->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);
                                                }
                                                $k = $k + 1;
            
                                            }
            
                                        } else {
            
                                            foreach ($Subscriber as $contacts) {
                                                $callnumber = $contacts['Contact']['phone_number'];
                                                $callerPhone = $sendfrom;
                                                Controller::loadModel('Log');
                                                $logarr['Log']['id'] = '';
                                                $logarr['Log']['user_id'] = $user_id;
                                                $logarr['Log']['group_id'] = $group_id;
                                                $logarr['Log']['phone_number'] = $callnumber;
                                                $logarr['Log']['route'] = 'inbox';
                                                $logarr['Log']['msg_type'] = 'broadcast';
                                                $this->Log->save($logarr);
                                                $lastinsertID = $this->Log->getLastInsertId();
                                                $Sid = '';
                                                if ($throttle > 1) {
                                                    sleep($throttle);
                                                }
            
                                                if ($lastinsertID > 0) {
                                                    if ($SID == ''){
                                                        $this->Plivo->AuthId = PLIVO_KEY;
                                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                    }else{
                                                        $this->Plivo->AuthId = $SID;
                                                        $this->Plivo->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $response = $this->Plivo->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);
                                                }
            
                                            }
                                        }
                                    } else {
                                        if ($rotate == 1) {
                                            $k = 0;
                                            foreach ($Subscriber as $contacts) {
                                                $callnumber = $contacts['Contact']['phone_number'];
                                                $callerPhone = $from_arr[$k];
            
                                                $stickyfrom = $contacts['Contact']['stickysender'];
                                                if ($stickyfrom != 0) {
                                                    $callerPhone = $stickyfrom;
                                                }
            
                                                $countnumber = count($from_arr);
                                                if ($countnumber == $k) {
                                                    $k = 0;
                                                    sleep($throttle);
                                                }
                                                Controller::loadModel('Log');
                                                $logarr['Log']['id'] = '';
                                                $logarr['Log']['user_id'] = $user_id;
                                                $logarr['Log']['group_id'] = $group_id;
                                                $logarr['Log']['phone_number'] = $callnumber;
                                                $logarr['Log']['route'] = 'inbox';
                                                $logarr['Log']['msg_type'] = 'broadcast';
                                                $this->Log->save($logarr);
                                                $lastinsertID = $this->Log->getLastInsertId();
                                                $Sid = '';
                                                if ($lastinsertID > 0) {
                                                    $api_key = NEXMO_KEY;
                                                    $api_secret = NEXMO_SECRET;
                                                    $response = $this->Nexmo->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $api_key, $api_secret, $lastinsertID, $repeat, $language, $pause);
                                                    $Sid = $response->{'call-id'};
                                                }
            
                                                if ($Sid != '') {
                                                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    $this->request->data['User']['voice_balance'] = $usersms['User']['voice_balance'] - 1;
                                                    $this->request->data['User']['id'] = $user_id;
                                                    $this->User->save($this->request->data);
                                                    //Check voice credit balance
                                                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if ($usersms['User']['email_alert_credit_options'] == 0) {
                                                        if ($usersms['User']['voice_balance'] <= $usersms['User']['low_voice_balances']) {
                                                            if ($usersms['User']['VM_credit_balance_email_alerts'] == 0) {
                                                                $username = $usersms['User']['username'];
                                                                $email = $usersms['User']['email'];
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
                                                                $Email->viewVars(array('low_voice_balances' => $usersms['User']['low_voice_balances']));
                                                                $Email->send();
                                        
                                                                $this->User->id = $usersms['User']['id'];
                                                                $this->User->saveField('VM_credit_balance_email_alerts', 1);
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $logarr['Log']['id'] = $lastinsertID;
                                                    $logarr['Log']['sms_status'] = 'failed';
                                                    $this->Log->save($logarr);
                                                }
                                                $k = $k + 1;
                                            }
                                        } else {
            
                                            foreach ($Subscriber as $contacts) {
                                                $callnumber = $contacts['Contact']['phone_number'];
                                                $callerPhone = $sendfrom;
                                                Controller::loadModel('Log');
                                                $logarr['Log']['id'] = '';
                                                $logarr['Log']['user_id'] = $user_id;
                                                $logarr['Log']['group_id'] = $group_id;
                                                $logarr['Log']['phone_number'] = $callnumber;
                                                $logarr['Log']['route'] = 'inbox';
                                                $logarr['Log']['msg_type'] = 'broadcast';
                                                $this->Log->save($logarr);
                                                $lastinsertID = $this->Log->getLastInsertId();
                                                $Sid = '';
                                                sleep($throttle);
                                                if ($lastinsertID > 0) {
                                                    $api_key = NEXMO_KEY;
                                                    $api_secret = NEXMO_SECRET;
                                                    $response = $this->Nexmo->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $api_key, $api_secret, $lastinsertID, $repeat, $language, $pause);
                                                    $Sid = $response->{'call-id'};
                                                }
            
                                                if ($Sid != '') {
                                                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    $this->request->data['User']['voice_balance'] = $usersms['User']['voice_balance'] - 1;
                                                    $this->request->data['User']['id'] = $user_id;
                                                    $this->User->save($this->request->data);
                                                    //Check voice credit balance
                                                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if ($usersms['User']['email_alert_credit_options'] == 0) {
                                                        if ($usersms['User']['voice_balance'] <= $usersms['User']['low_voice_balances']) {
                                                            if ($usersms['User']['VM_credit_balance_email_alerts'] == 0) {
                                                                $username = $usersms['User']['username'];
                                                                $email = $usersms['User']['email'];
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
                                                                $Email->viewVars(array('low_voice_balances' => $usersms['User']['low_voice_balances']));
                                                                $Email->send();
                                                                
                                                                $this->User->id = $usersms['User']['id'];
                                                                $this->User->saveField('VM_credit_balance_email_alerts', 1);
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $logarr['Log']['id'] = $lastinsertID;
                                                    $logarr['Log']['sms_status'] = 'failed';
                                                    $this->Log->save($logarr);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
?>