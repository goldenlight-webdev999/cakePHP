<?php
App::uses('CakeEmail', 'Network/Email');
class ContestsController extends AppController
{
    var $name = 'Contests';
    var $components = array('Cookie','Twilio','Qr','Nexmomessage','Slooce','Plivo','Bandwidth','Signalwire','Ytel','Telnyx');

    function index()
    {
        $this->layout = 'admin_new_layout';
        $this->Contest->recursive = 0;
        $user_id = $this->Session->read('User.id');
        $this->paginate = array('conditions' => array('Contest.user_id' => $user_id), 'order' => array('Contest.id' => 'asc'));
        $data = $this->paginate('Contest');
        $this->set('contests', $data);
    }

    function participants($id = null)
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ContestSubscriber');
        $this->ContestSubscriber = new ContestSubscriber();
        $this->paginate = array('conditions' => array('ContestSubscriber.contest_id' => $id, 'ContestSubscriber.user_id' => $user_id), 'order' => array('ContestSubscriber.id' => 'asc'));
        $participant = $this->paginate('ContestSubscriber');
        $this->set('participant', $participant);

    }

    function add()
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
        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $Group);
        if (!empty($this->request->data)) {

            app::import('Model', 'Group');
            $this->Group = new Group();

            app::import('Model', 'Contest');
            $this->Contest = new Contest();

            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();


            $keywords = $this->Group->find('first', array('conditions' => array('Group.keyword ' => $this->request->data['Contest']['keyword'], 'Group.user_id ' => $user_id)));
            $contestkeyword = $this->Contest->find('first', array('conditions' => array('Contest.keyword ' => $this->request->data['Contest']['keyword'], 'Contest.user_id' => $user_id)));

            if (!empty($contestkeyword)) {
                $this->Session->setFlash(__('Keyword is already registered for a contest. Please choose another keyword.', true));
                $this->redirect(array('controller' => 'contests', 'action' => 'add'));
            }

            $loyaltykeyword = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.codestatus ' => $this->request->data['Contest']['keyword'], 'Smsloyalty.user_id' => $user_id)));

            if (!empty($loyaltykeyword)) {
                $this->Session->setFlash(__('Keyword is already registered for another loyalty program. Please choose another keyword.', true));
                $this->redirect(array('controller' => 'contests', 'action' => 'add'));
            }

            if (empty($keywords)) {
                $this->request->data['Contest']['active'] = 1;
                $this->request->data['Contest']['user_id'] = $user_id;
                $this->request->data['Contest']['startdate'] = date('Y-m-d', strtotime($this->request->data['Contest']['start']));
                $this->request->data['Contest']['enddate'] = date('Y-m-d', strtotime($this->request->data['Contest']['end']));

                $this->Contest->save($this->request->data);
                $this->Session->setFlash(__('The SMS contest has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Keyword is already registered. Please choose another keyword.', true));
            }

        }
    }

    function edit($id = null)
    {
        $this->layout = 'admin_new_layout';
        //$this->layout="default";
        //$this->checkUserSession();
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid group', true));
            $this->redirect(array('action' => 'index'));
        }
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Contest');
        $this->Contest = new Contest();
        app::import('Model', 'Group');
        $this->Group = new Group();

        if (!empty($this->request->data)) {

            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();

            $findkeyword = $this->Group->find('first', array('conditions' => array('Group.keyword ' => $this->request->data['Contest']['keyword'], 'Group.user_id ' => $user_id)));
            $contestkeyword = $this->Contest->find('first', array('conditions' => array('Contest.keyword ' => $this->request->data['Contest']['keyword'], 'Contest.user_id' => $user_id, 'Contest.group_name !=' => $this->request->data['Contest']['group_name'])));

            if (!empty($contestkeyword)) {
                $this->Session->setFlash(__('Keyword is already registered for a contest. Please choose another keyword.', true));
                $this->redirect(array('controller' => 'contests', 'action' => 'edit/' . $id));
            }

            $loyaltykeyword = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.codestatus ' => $this->request->data['Contest']['keyword'], 'Smsloyalty.user_id' => $user_id)));

            if (!empty($loyaltykeyword)) {
                $this->Session->setFlash(__('Keyword is already registered for another loyalty program. Please choose another keyword.', true));
                $this->redirect(array('controller' => 'contests', 'action' => 'edit/' . $id));
            }

            if (empty($findkeyword)) {
                //$user_id=$this->Session->read('User.id');
                $this->Session->write('User.active', $this->Session->read('User.active'));
                $this->Session->write('sms_balance', $this->Session->read('User.sms_balance'));
                $this->Session->write('User.assigned_number', $this->Session->read('User.assigned_number'));
                //$contests= $this->Contest->find('first', array('conditions'=>array('Contest.keyword'=>$this->request->data['Contest']['keyword'],'Contest.user_id !='=>$user_id)));
                if (empty($contestkeyword)) {
                    $this->set('contest', $contests);
                    //$contest['Contest']['keyword'];
                    //if($contestkeyword['Contest']['keyword'] == $this->request->data['Contest']['keyword']){
                    //$this->request->data['Contest']['keyword'] = $this->request->data['Contest']['keyword'];
                    $this->request->data['Contest']['id'] = $id;
                    $this->request->data['Contest']['startdate'] = date('Y-m-d', strtotime($this->request->data['Contest']['start']));
                    $this->request->data['Contest']['enddate'] = date('Y-m-d', strtotime($this->request->data['Contest']['end']));
//				}   					
                    if ($this->Contest->save($this->request->data)) {
                        $this->Session->setFlash(__('The SMS contest has been updated', true));
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The SMS contest could not be updated. Please, try again.', true));
                    }
                }
            } else {
                $this->Session->setFlash(__('Keyword is already registered. Please choose another keyword.', true));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Contest->read(null, $id);
        }

        $contest = $this->Contest->find('first', array('conditions' => array('Contest.id' => $id)));
        $this->set('contest', $contest);

        $Group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $Group);
    }

    function delete($id = null)
    {
        if ($this->Contest->delete($id)) {
            $this->Session->setFlash(__('SMS contest has been deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('SMS contest has been deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function sendcontest($id = null)
    {
        $this->set('id', $id);
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        $this->Session->write('User.active', $this->Session->read('User.active'));
        $this->Session->write('sms_balance', $this->Session->read('User.sms_balance'));
        $this->Session->write('User.assigned_number', $this->Session->read('User.assigned_number'));
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $group);
        app::import('Model', 'Contest');
        $this->Contest = new Contest();
        $contestkeyworddata = $this->Contest->find('first', array('conditions' => array('Contest.id ' => $id, 'Contest.user_id' => $user_id)));
        $this->set('contestkeyworddata', $contestkeyworddata);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('users', $users);
        $this->Session->write('User.getnumbers', $users['User']['getnumbers']);
        $this->Session->write('User.package', $users['User']['package']);
    }

    function send_message()
    {
        if (!empty($this->request->data)) {
            $rotate_number = $this->request->data['User']['rotate_number'];
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'User');
            $this->User = new User();
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $users_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.sms' => 1)));
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            if ((!empty($user_numbers)) || (!empty($users_arr))) {
                $this->Session->write('User.active', $this->Session->read('User.active'));
                $this->Session->write('sms_balance', $this->Session->read('User.sms_balance'));
                $this->Session->write('User.assigned_number', $this->Session->read('User.assigned_number'));
                app::import('Model', 'Contest');
                $this->Contest = new Contest();
                $contestkeywords = $this->Contest->find('first', array('conditions' => array('Contest.id ' => $this->request->data['Contest']['id'], 'Contest.user_id' => $user_id)));
                app::import('Model', 'User');
                $this->User = new User();
                $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                $credits = $users['User']['sms_balance'];
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                //$Subscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.id')));
                $Subscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.id')));
                //if (empty($Subscriber)) {
                if ($Subscriber == 0) {
                    $this->Session->setFlash(__('Add contacts to this group or select a different group.', true));
                    $this->redirect(array('controller' => 'contests', 'action' => 'index'));
                }
                //$totalsubscribers = count($Subscriber);
                $totalsubscribers = $Subscriber;
                $message1 = $this->request->data['Contest']['message'];
                $sendthis = " Reply " . $contestkeywords['Contest']['keyword'] . " to Enter";
                $optmsg = OPTMSG;
                $message2 = $message1 . "\n" . $sendthis . "\n" . $optmsg;

                //$length = strlen(utf8_decode(substr($message2, 0, 160)));
                $length = mb_strlen($message2,"UTF-8");
			    $gsm = $this->is_gsm($message2);
                if(!$gsm){//if (strlen($message2) != strlen(utf8_decode($message2))) {
                    $contactcredits = ceil($length / 70);
                } else {
                    $contactcredits = ceil($length / 160);
                }
                if ($credits < ($totalsubscribers * $contactcredits)) {
                    $this->Session->setFlash(__('You do not have enough credits to send a contest to this group.', true));
                    $this->redirect(array('controller' => 'contests', 'action' => 'index'));
                }
                //$subscriberPhone1 = '';
                //foreach ($Subscriber as $Subscribers) {
                //    $subscriberPhone1[$Subscribers['Contact']['phone_number']] = $Subscribers['Contact']['phone_number'];
                //}
                foreach ($this->request->data['Group']['id'] as $groupIds) {
                    $group_id = $groupIds;
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_id, 'ContactGroup.un_subscribers' => 0), 'fields' => array('ContactGroup.id', 'Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                    
                    if ($totalsubscribers > 0){
                        app::import('Model', 'GroupSmsBlast');
                        $this->GroupSmsBlast = new GroupSmsBlast();
                        $this->request->data['GroupSmsBlast']['user_id'] = $user_id;
                        $this->request->data['GroupSmsBlast']['group_id'] = $group_id;
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
                                $i = 0;
                                $this->Twilio->curlinit = curl_init();
                                $this->Twilio->bulksms = 1;
                                //foreach($subscriberPhone2 as $contactgroupid=>$subscriberPhones){
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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

                                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                        $response = $this->Twilio->sendsms($to, $from, $message);
                                        $smsid = $response->ResponseXml->Message->Sid;
                                        $Status = $response->ResponseXml->RestException->Status;
                                        $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                        $this->request->data['Log']['sms_id'] = $smsid;
                                        $this->request->data['Log']['user_id'] = $user_id;
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($Status == 400) {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $response->ErrorMessage;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }
                                        $this->Log->save($this->request->data);
                                        $i++;
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
                                //foreach ($subscriberPhone2 as $contactgroupid => $subscriberPhones) {
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
                                        $from = $assigned_number;
                                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                        $response = $this->Twilio->sendsms($to, $from, $message);
                                        $smsid = $response->ResponseXml->Message->Sid;
                                        $Status = $response->ResponseXml->RestException->Status;
                                        $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                        $this->request->data['Log']['sms_id'] = $smsid;
                                        $this->request->data['Log']['user_id'] = $user_id;
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($Status == 400) {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $response->ErrorMessage;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }
                                        $this->Log->save($this->request->data);
                                        $i++;
                                }
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
                                //foreach ($subscriberPhone2 as $contactgroupid => $subscriberPhones) {
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($message_id != '') {
                                            $sucesscredits = $sucesscredits + 1;
                                            $this->request->data['Log']['sms_status'] = 'sent';
                                        } else if ($status != 'ok') {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $status;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
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
                                //foreach ($subscriberPhone2 as $contactgroupid => $subscriberPhones) {
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $from = $assigned_number;
                                        //$contact_name = $subscriberPhone3[$i];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($message_id != '') {
                                            $sucesscredits = $sucesscredits + 1;
                                            $this->request->data['Log']['sms_status'] = 'sent';
                                        } else if ($status != 'ok') {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $status;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
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
                                //foreach($subscriberPhone2 as $contactgroupid=>$subscriberPhones){
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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

                                        $this->Plivo->AuthId = PLIVO_KEY;
                                        $this->Plivo->AuthToken = PLIVO_TOKEN;
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
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($message_id != '') {
                                            $sucesscredits = $sucesscredits + $contactcredits;
                                            $credits = $credits + 1;
                                            $this->request->data['Log']['sms_status'] = 'sent';
                                        } else if (isset($response['response']['error'])) {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $errortext;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
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
                                //foreach ($subscriberPhone2 as $contactgroupid => $subscriberPhones) {
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $from = $assigned_number;
                                        //$contact_name = $subscriberPhone3[$i];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($message_id != '') {
                                            $sucesscredits = $sucesscredits + $contactcredits;
                                            $credits = $credits + 1;
                                            $this->request->data['Log']['sms_status'] = 'sent';
                                        } else if (isset($response['response']['error'])) {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $errortext;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
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
                                $i = 0;
                                $credits = 0;
                                $failed = 0;
                                $success = 0;
                                //foreach($subscriberPhone2 as $contactgroupid=>$subscriberPhones){
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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
        								    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message,$groupblastid,$group_id,$user_id);
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
        					            
                                        $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                        $this->request->data['Log']['sms_id'] = $message_id;
                                        $this->request->data['Log']['user_id'] = $user_id;
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($errortext !='') {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $this->request->data['Log']['error_message'] = $errortext;
                                            if(API_TYPE==6){
                                                $this->Log->save($this->request->data);
                                            }
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }else{
                                            $credits = $credits + $contactcredits;
                                            $success = $success + 1;
                                        }
                                        if(API_TYPE!=6){
                                            $this->Log->save($this->request->data);
                                        }
                                        $i++;
                                }
                                if ($success > 0) {
                                    $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    if (!empty($usersbalance)) {
                                        $usercredit['User']['id'] = $user_id;
                                        $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                        $this->User->save($usercredit);
                                    }
                                    app::import('Model', 'GroupSmsBlast');
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
                                //foreach ($subscriberPhone2 as $contactgroupid => $subscriberPhones) {
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
                                        $from = $assigned_number;
                                        
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
        								    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message,$groupblastid,$group_id,$user_id);
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
        					            
                                        $this->request->data['Log']['group_sms_id'] = $groupblastid;
                                        $this->request->data['Log']['sms_id'] = $message_id;
                                        $this->request->data['Log']['user_id'] = $user_id;
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($errortext !='') {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $response->ErrorMessage;
                                            $this->request->data['Log']['error_message'] = $errortext;
                                            if(API_TYPE==6){
                                                $this->Log->save($this->request->data);
                                            }
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
                                        }else{
                                            $credits = $credits + $contactcredits;
                                            $success = $success + 1;
                                        }
                                        if(API_TYPE!=6){
                                            $this->Log->save($this->request->data);
                                        }
                                        $i++;
                                }
                                
                                if ($success > 0) {
                                    $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    if (!empty($usersbalance)) {
                                        $usercredit['User']['id'] = $user_id;
                                        $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                        $this->User->save($usercredit);
                                    }
                                    app::import('Model', 'GroupSmsBlast');
                                    $group_blast['GroupSmsBlast']['id'] = $groupblastid;
                                    $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
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
                                //foreach($subscriberPhone2 as $contactgroupid=>$subscriberPhones){
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
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

                                        $this->Nexmomessage->Key = NEXMO_KEY;
                                        $this->Nexmomessage->Secret = NEXMO_SECRET;
                                        $response = $this->Nexmomessage->sendsms($to, $from, $message);
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
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id']=$contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                        $this->request->data['Log']['sms_status'] = '';
                                        $this->request->data['Log']['error_message'] = '';
                                        if ($message_id != '') {
                                            $sucesscredits = $sucesscredits + $contactcredits;
                                            $credits = $credits + 1;
                                            $this->request->data['Log']['sms_status'] = 'sent';
                                        } else if ($status != 0) {
                                            $this->request->data['Log']['sms_status'] = 'failed';
                                            $ErrorMessage = $errortext;
                                            $this->request->data['Log']['error_message'] = $ErrorMessage;
                                            app::import('Model', 'GroupSmsBlast');
                                            $this->GroupSmsBlast = new GroupSmsBlast();
                                            $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                            $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                            $this->request->data['GroupSmsBlast']['id']  = $groupblastid;
                                            $this->GroupSmsBlast->save($this->request->data);
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
                                //foreach ($subscriberPhone2 as $contactgroupid => $subscriberPhones) {
                                foreach ($groupContacts as $Subscribers) {
                                    //if (!isset($phone[$subscriberPhones])) {
                                        $this->Log = new Log();
                                        //$phone[$subscriberPhones] = $subscriberPhones;
                                        //$to = $subscriberPhones;
                                        $to = $Subscribers['Contact']['phone_number'];
                                        //$contact_name = $subscriberPhone3[$i];
                                        $contact_name = $Subscribers['Contact']['name'];
                                        $from = $assigned_number;
                                        //$contact_name = $subscriberPhone3[$i];
                                        $message = str_replace('%%Name%%', $contact_name, $message2);
                                        $this->Nexmomessage->Key = NEXMO_KEY;
                                        $this->Nexmomessage->Secret = NEXMO_SECRET;
                                        sleep(1);
                                        $response = $this->Nexmomessage->sendsms($to, $from, $message);
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
                                        $this->request->data['Log']['group_id'] = $group_id;
                                        $this->request->data['Log']['phone_number'] = $to;
                                        $this->request->data['Log']['text_message'] = $message;
                                        $this->request->data['Log']['sendfrom'] = $from;
                                        $this->request->data['Log']['route'] = 'outbox';
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        //$this->request->data['ContactGroup']['id'] = $contactgroupid;
                                        $this->request->data['ContactGroup']['id'] = $Subscribers['ContactGroup']['id'];
                                        $this->request->data['ContactGroup']['contest_id'] = $this->request->data['Contest']['id'];
                                        $this->ContactGroup->save($this->request->data);
                                    //}
                                    $this->request->data['Log']['sms_status'] = '';
                                    $this->request->data['Log']['error_message'] = '';
                                    if ($message_id != '') {
                                        $sucesscredits = $sucesscredits + $contactcredits;
                                        $credits = $credits + 1;
                                        $this->request->data['Log']['sms_status'] = 'sent';
                                    } else if ($status != 0) {
                                        $this->request->data['Log']['sms_status'] = 'failed';
                                        $ErrorMessage = $errortext;
                                        $this->request->data['Log']['error_message'] = $ErrorMessage;
                                        app::import('Model', 'GroupSmsBlast');
                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                        $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupblastid)));
                                        $this->request->data['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
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
                        $this->Session->setFlash(__('SMS contest has been sent', true));
                        $this->redirect(array('controller' => 'contests', 'action' => 'index'));
                    }
                }
            } else {
                $this->Session->setFlash(__('You do not have any number with SMS capability', true));
                $this->redirect(array('controller' => 'contests', 'action' => 'index'));
            }
        }
    }

    function contest_winner($id = null, $contest_id = null)
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        $this->Session->write('User.active', $this->Session->read('User.active'));
        $this->Session->write('sms_balance', $this->Session->read('User.sms_balance'));
        $this->Session->write('User.assigned_number', $this->Session->read('User.assigned_number'));
        $this->set('id', $id);
        app::import('Model', 'ContestSubscriber');
        $this->ContestSubscriber = new ContestSubscriber();
        $phone_number = $this->ContestSubscriber->find('first', array('conditions' => array('ContestSubscriber.contest_id' => $id,), 'order' => 'rand()',));
        $this->set('phoneno', $phone_number);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('users', $users);
        if (!empty($this->request->data)) {
            app::import('Model', 'User');
            $this->User = new User();
            $assign_number = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $to = $this->request->data['ContestSubscriber']['phoneno'];
            $from = $assign_number['User']['assigned_number'];
            $message = $this->request->data['ContestSubscriber']['message'];
            if (API_TYPE == 0) {
                $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                $response = $this->Twilio->sendsms($to, $from, $message);
                $smsid = $response->ResponseXml->Message->Sid;
                if ($smsid != '') {
                    $smsbalance = $assign_number['User']['sms_balance'] - 1;
                    $this->User->id = $assign_number['User']['id'];
                    $this->User->saveField('sms_balance', $smsbalance);
                    $this->smsmail($user_id);
                    $this->Session->setFlash(__('Message sent', true));
                } else {
                    $this->Session->setFlash(__('Message not sent', true));
                }
            } elseif (API_TYPE == 4 || API_TYPE == 5 || API_TYPE == 6 || API_TYPE == 7) {
                if(API_TYPE == 5){
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
				    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message);
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
                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
        			$message_id = '';
        			$status = '';
        			$errortext = '';
        			if(isset($response->message)){
        				$errortext = $response->message;
        				$status = 'failed';
        			}else{
        				$status = 'sent';
        			}
				}
                if ($errortext == '') {
                    $smsbalance = $assign_number['User']['sms_balance'] - 1;
                    $this->User->id = $assign_number['User']['id'];
                    $this->User->saveField('sms_balance', $smsbalance);
                    $this->smsmail($user_id);
                    $this->Session->setFlash(__('Message sent', true));
                } else {
                    $this->Session->setFlash(__('Message not sent', true));
                }
            } elseif (API_TYPE == 3) {
                $this->Plivo->AuthId = PLIVO_KEY;
                $this->Plivo->AuthToken = PLIVO_TOKEN;
                $response = $this->Plivo->sendsms($to, $from, $message);
                $errortext = '';
                $message_id = '';
                if (isset($response['response']['error'])) {
                    $errortext = $response['response']['error'];
                }
                if (isset($response['response']['message_uuid'][0])) {
                    $message_id = $response['response']['message_uuid'][0];
                }
                if ($message_id != '') {
                    $smsbalance = $assign_number['User']['sms_balance'] - 1;
                    $this->User->id = $assign_number['User']['id'];
                    $this->User->saveField('sms_balance', $smsbalance);
                    $this->smsmail($user_id);
                    $this->Session->setFlash(__('Message sent', true));
                } else {
                    $this->Session->setFlash(__('Message not sent', true));
                }
            } elseif (API_TYPE == 1) {
                $this->Nexmomessage->Key = NEXMO_KEY;
                $this->Nexmomessage->Secret = NEXMO_SECRET;
                sleep(1);
                $response = $this->Nexmomessage->sendsms($to, $from, $message);
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
                if ($message_id != '') {
                    $smsbalance = $assign_number['User']['sms_balance'] - 1;
                    $this->User->id = $assign_number['User']['id'];
                    $this->User->saveField('sms_balance', $smsbalance);
                    $this->smsmail($user_id);
                    $this->Session->setFlash(__('Message sent', true));
                } else {
                    $this->Session->setFlash(__('Message not sent', true));
                }
            } elseif (API_TYPE == 2) {
                $response = $this->Slooce->mt($assign_number['User']['api_url'], $assign_number['User']['partnerid'], $assign_number['User']['partnerpassword'], $to, $assign_number['User']['keyword'], $message);
                $message_id = '';
                $status = '';
                if (isset($response['id'])) {
                    if ($response['result'] == 'ok') {
                        $message_id = $response['id'];
                    }
                    $status = $response['result'];
                }
                if ($message_id != '') {
                    $smsbalance = $assign_number['User']['sms_balance'] - 1;
                    $this->User->id = $assign_number['User']['id'];
                    $this->User->saveField('sms_balance', $smsbalance);
                    $this->smsmail($user_id);
                    $this->Session->setFlash(__('Message sent', true));
                } else {
                    $this->Session->setFlash(__('Message not sent', true));
                }
            }
            //app::import('Model','ContestSubscriber');
            //$this->ContestSubscriber=new ContestSubscriber();
            //$condition =array('ContestSubscriber.contest_id'=>$contest_id);
            //$this->ContestSubscriber->deleteAll($condition,false);
            app::import('Model', 'Contest');
            $this->Contest = new Contest();
            $this->request->data['Contest']['id'] = $contest_id;
            //$this->request->data['Contest']['totalsubscriber']=0;
            $this->request->data['Contest']['winning_phone_number'] = $this->request->data['ContestSubscriber']['phoneno'];
            $this->Contest->save($this->request->data);
            $this->redirect(array('controller' => 'contests', 'action' => 'index'));
        }
    }
}
?>