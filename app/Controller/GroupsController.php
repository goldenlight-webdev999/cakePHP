<?php
App::uses('CakeEmail', 'Network/Email');
class GroupsController extends AppController
{
    var $name = 'Groups';
    var $components = array('Twilio', 'Qr', 'Qrsms', 'Nexmo', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');

    //var $layout="default";
    function index()
    {

        $this->layout = 'admin_new_layout';
        $this->Group->recursive = 0;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('users', $users);
        $this->paginate = array(
            'conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.id' => 'asc')
        );
        $data = $this->paginate('Group');
        $this->set('groups', $data);


    }


    function add()
    {
        $this->layout = 'admin_new_layout';

        $user_id = $this->Session->read('User.id');
        $this->set('user_id', $user_id);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        app::import('Model', 'User');
        $this->User = new User();
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $this->set('users', $users);

        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('copygroup', $Group);

        if (!empty($this->request->data)) {
            /*  echo'<pre>';
            print_r($this->request->data);
            die();  */
            app::import('Model', 'Contest');
            $this->Contest = new Contest();

            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();

            if (API_TYPE != 2) {
                $keywords = $this->Group->find('first', array('conditions' => array('Group.keyword ' => $this->request->data['Group']['keyword'], 'Group.user_id ' => $user_id)));
            } else {
                $keywords = $this->Group->find('first', array('conditions' => array('Group.keyword ' => $this->request->data['Group']['keyword'])));
            }

            $contestkeyword = $this->Contest->find('first', array('conditions' => array('Contest.keyword ' => $this->request->data['Group']['keyword'], 'Contest.user_id' => $user_id)));

            if (!empty($contestkeyword)) {
                $this->Session->setFlash(__('Keyword is already registered for a contest. Please choose another keyword.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'add'));
            }

            $loyaltykeyword = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.codestatus ' => $this->request->data['Group']['keyword'], 'Smsloyalty.user_id' => $user_id)));

            if (!empty($loyaltykeyword)) {
                $this->Session->setFlash(__('Keyword is already registered for another loyalty program. Please choose another keyword.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'add'));
            }

            if (empty($keywords)) {
                $msgtype = $this->request->data['Group']['msg_type'];
                $group['Group']['user_id'] = $this->request->data['Group']['user_id'];
                $group['Group']['group_type'] = $this->request->data['Group']['group_type'];
                $group['Group']['group_name'] = $this->request->data['Group']['group_name'];
                $group['Group']['keyword'] = $this->request->data['Group']['keyword'];
                $group['Group']['ifmember_message'] = $this->request->data['Group']['ifmember_message'];
                if ($this->request->data['Group']['msg'] != '' && $this->request->data['Message']['msg_type'] == 1) {
                    $group['Group']['system_message'] = $this->request->data['Group']['msg'];
                }
                if ($this->request->data['Group']['msg2'] != '' && $this->request->data['Message']['msg_type'] == 1) {
                    $group['Group']['system_message'] = $this->request->data['Group']['msg2'];
                }
                if ($this->request->data['Group']['msg1'] != '' && $this->request->data['Message']['msg_type'] == 2) {
                    $group['Group']['system_message'] = $this->request->data['Group']['msg1'];
                }
                $group['Group']['auto_message'] = $this->request->data['Group']['auto_message'];
                if (!empty($this->request->data['Message']['msg_type'])) {
                    $group['Group']['sms_type'] = $this->request->data['Message']['msg_type'];
                } else {
                    $group['Group']['sms_type'] = 1;
                }

                if (!empty($this->request->data['Message']['bithday_enable'])) {
                    $group['Group']['bithday_enable'] = $this->request->data['Message']['bithday_enable'];
                } else {
                    $group['Group']['bithday_enable'] = 0;
                }

                if ($this->request->data['Group']['image'][0]['name'] != '') {
                    $image_arr = '';
                    foreach ($this->request->data['Group']['image'] as $value) {
                        $image = str_replace(' ', '_', mt_rand() . $value["name"]);
                        move_uploaded_file($value['tmp_name'], "mms/" . $image);
                        if ($image_arr != '') {
                            $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                        } else {
                            $image_arr = SITE_URL . '/mms/' . $image;
                        }
                    }
                    $group['Group']['image_url'] = $image_arr;
                }
                $group['Group']['active'] = 1;

                if (isset($this->request->data['Group']['notify_signup'])) {
                    $group['Group']['notify_signup'] = 1;
                } else {
                    $group['Group']['notify_signup'] = 0;
                }

                $group['Group']['mobile_number_input'] = $this->request->data['Group']['mobile_number_input'];
                
                if (isset($this->request->data['Group']['double_optin'])) {
                    $group['Group']['double_optin'] = 1;
                } else {
                    $group['Group']['double_optin'] = 0;
                }

                $group_type = $this->request->data['Group']['group_type'];
                if ($group_type == '2') {
                    $group['Group']['property_address'] = $this->request->data['Group']['propertyaddress'];
                    $group['Group']['property_price'] = $this->request->data['Group']['propertyprice'];
                    $group['Group']['property_bed'] = $this->request->data['Group']['property_bed'];
                    $group['Group']['property_bath'] = $this->request->data['Group']['property_bath'];
                    $group['Group']['property_description'] = $this->request->data['Group']['propertydesc'];
                    $group['Group']['property_url'] = $this->request->data['Group']['propertyurl'];

                } elseif ($group_type == '3') {
                    $group['Group']['vehicle_year'] = $this->request->data['Group']['vehicleyear'];
                    $group['Group']['vehicle_make'] = $this->request->data['Group']['vehiclemake'];
                    $group['Group']['vehicle_model'] = $this->request->data['Group']['vehiclemodel'];
                    $group['Group']['vehicle_mileage'] = $this->request->data['Group']['vehiclemileage'];
                    $group['Group']['vehicle_price'] = $this->request->data['Group']['vehicleprice'];
                    $group['Group']['vehicle_description'] = $this->request->data['Group']['vehicledesc'];
                    $group['Group']['vehicle_url'] = $this->request->data['Group']['vehicleurl'];

                }

                if ($this->Group->save($group)) {
                    $this->Session->setFlash(__('The group has been saved', true));
                    $this->redirect(array('action' => 'index'));
                }
                //$this->redirect(array('controller' =>'users', 'action'=>'dashboard'));
            } else {
                $this->Session->setFlash(__('Keyword is already registered. please choose another keyword.', true));
            }
        }

    }


    function delete($id = null, $message_type = null)
    {
        //$this->layout="default";
        if ($this->Group->delete($id)) {
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $Contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $id), 'fields' => 'ContactGroup.contact_id'));
            foreach ($Contacts as $Contact) {
                $contact_id = $Contact['ContactGroup']['contact_id'];
                $this->Contact->delete($contact_id);
            }
            $this->ContactGroup->deleteAll(array('ContactGroup.group_id' => $id));
            
            app::import('Model', 'Contest');
            $this->Contest = new Contest();
            $this->Contest->deleteAll(array('Contest.group_id' => $id));
            
            app::import('Model', 'Birthday');
            $this->Birthday = new Birthday();
            $this->Birthday->deleteAll(array('Birthday.group_id' => $id));
            
            app::import('Model', 'VoiceMessage');
            $this->VoiceMessage = new VoiceMessage();
            $this->VoiceMessage->deleteAll(array('VoiceMessage.group_id' => $id));
            
            app::import('Model', 'Responder');
            $this->Responder = new Responder();
            $this->Responder->deleteAll(array('Responder.group_id' => $id));

            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();
            $this->Smsloyalty->deleteAll(array('Smsloyalty.group_id' => $id));
            
            app::import('Model','MultiContest');
		    $this->MultiContest = new MultiContest();
            $this->MultiContest->deleteAll(array('MultiContest.group_id' => $id));

            $this->Session->setFlash(__('Group deleted', true));
            if ($message_type == 1) {
                $this->redirect(array('action' => 'mms_group'));
            } else if ($message_type == 0) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $this->Session->setFlash(__('Group  deleted', true));
        $this->redirect(array('action' => 'index'));

    }


    function edit($id = null)
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        $this->set('id', $id);
        $this->set('user_id', $user_id);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        app::import('Model', 'User');
        $this->User = new User();
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $this->set('users', $users);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid group', true));
            $this->redirect(array('action' => 'index'));
        }
        $group_arr = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
        $this->set('group', $group_arr);
        if (!empty($this->request->data)) {
            $group = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
            $this->set('group', $group);
            $group['Group']['keyword'];
            if ($group['Group']['keyword'] == $this->request->data['Group']['keyword']) {
                $this->request->data['Group']['keyword'] = $this->request->data['Group']['keyword'];
            }
            /* message type option update data */
            $msgtype = $this->request->data['Group']['msg_type'];
            if ($msgtype == 0) {
                $this->request->data['Group']['text_message'] = $this->request->data['Group']['text_message'];
                $this->request->data['Group']['audio_name'] = '';
                $this->request->data['Group']['message_type'] = 0;
            } else if ($msgtype == 1) {
                $name = $this->request->data['Group']['mp3']['name'];
                $allowedExts = array("mp3", "wav");
                $temp = explode(".", $this->request->data['Group']['mp3']['name']);
                $extension = end($temp);
                if ((($this->request->data['Group']['mp3']['type'] == "audio/mp3")
                        || ($this->request->data['Group']['mp3']['type'] == "audio/wav")
                        || ($this->request->data['Group']['mp3']['type'] == "audio/mpeg"))
                    && in_array($extension, $allowedExts)) {
                    $filename = str_replace(' ', '_', time() . $name);
                    move_uploaded_file($this->request->data['Group']['mp3']['tmp_name'], "mp3/" . $filename);
                    $this->request->data['Group']['audio_name'] = $filename;
                    $this->request->data['Group']['text_message'] = '';
                    $this->request->data['Group']['message_type'] = 1;
                } else {
                    $this->Session->setFlash(__('Please select the audio like mp3 and wav files.', true));
                }
            }
            $group['Group']['id'] = $this->request->data['Group']['id'];
            $group['Group']['user_id'] = $this->request->data['Group']['user_id'];
            $group['Group']['group_type'] = $this->request->data['Group']['group_type'];
            $group['Group']['group_name'] = $this->request->data['Group']['group_name'];
            $group['Group']['keyword'] = $this->request->data['Group']['keyword'];
            $group['Group']['ifmember_message'] = $this->request->data['Group']['ifmember_message'];
            if ($this->request->data['Message']['msg_type'] == 1) {
                $group['Group']['system_message'] = $this->request->data['Group']['msg2'];
            } else if ($this->request->data['Message']['msg_type'] == 2) {
                $group['Group']['system_message'] = $this->request->data['Group']['msg1'];
            } else {
                $group['Group']['system_message'] = $this->request->data['Group']['msg2'];
            }
            $group['Group']['auto_message'] = $this->request->data['Group']['auto_message'];
            if (!empty($this->request->data['Message']['msg_type'])) {
                $group['Group']['sms_type'] = $this->request->data['Message']['msg_type'];
            } else {
                $group['Group']['sms_type'] = 1;
            }
            if (!empty($this->request->data['Message']['bithday_enable'])) {
                $group['Group']['bithday_enable'] = $this->request->data['Message']['bithday_enable'];
            } else {
                $group['Group']['bithday_enable'] = 0;
            }
            if ($this->request->data['Group']['image'][0]['name'] != '') {
                $image_arr = '';
                foreach ($this->request->data['Group']['image'] as $value) {
                    $image = str_replace(' ', '_', mt_rand() . $value["name"]);
                    move_uploaded_file($value['tmp_name'], "mms/" . $image);
                    if ($image_arr != '') {
                        $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                    } else {
                        $image_arr = SITE_URL . '/mms/' . $image;
                    }
                }
                $group['Group']['image_url'] = $image_arr;
            } else {
                $group['Group']['image_url'] = $this->request->data['Message']['image_url_old'];
            }
            $group['Group']['active'] = 1;
            if (isset($this->request->data['Group']['notify_signup'])) {
                $group['Group']['notify_signup'] = 1;
            } else {
                $group['Group']['notify_signup'] = 0;
            }

            $group['Group']['mobile_number_input'] = $this->request->data['Group']['mobile_number_input'];
            
            if (isset($this->request->data['Group']['double_optin'])) {
                $group['Group']['double_optin'] = 1;
            } else {
                $group['Group']['double_optin'] = 0;
            }

            $group_type = $this->request->data['Group']['group_type'];
            if ($group_type == '2') {
                $group['Group']['property_address'] = $this->request->data['Group']['propertyaddress'];
                $group['Group']['property_price'] = $this->request->data['Group']['propertyprice'];
                $group['Group']['property_bed'] = $this->request->data['Group']['property_bed'];
                $group['Group']['property_bath'] = $this->request->data['Group']['property_bath'];
                $group['Group']['property_description'] = $this->request->data['Group']['propertydesc'];
                $group['Group']['property_url'] = $this->request->data['Group']['propertyurl'];

            } elseif ($group_type == '3') {
                $group['Group']['vehicle_year'] = $this->request->data['Group']['vehicleyear'];
                $group['Group']['vehicle_make'] = $this->request->data['Group']['vehiclemake'];
                $group['Group']['vehicle_model'] = $this->request->data['Group']['vehiclemodel'];
                $group['Group']['vehicle_mileage'] = $this->request->data['Group']['vehiclemileage'];
                $group['Group']['vehicle_price'] = $this->request->data['Group']['vehicleprice'];
                $group['Group']['vehicle_description'] = $this->request->data['Group']['vehicledesc'];
                $group['Group']['vehicle_url'] = $this->request->data['Group']['vehicleurl'];

            }

            if ($this->Group->save($group)) {
                $this->Session->setFlash(__('The Group has been edited', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Group could not be edited. Please, try again.', true));
            }
            //pr($this->request->data);
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Group->read(null, $id);
        }
    }

    function view($id = null)
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'Group');
        $this->Group = new Group();
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();
        $phoneno = $this->User->find('first', array('conditions' => array('User.id ' => $user_id)));
        $phone_number = $phoneno['User']['assigned_number'];
        $url = SITE_URL;
        $groupname = $this->Group->find('first', array('conditions' => array('Group.id ' => $id, 'Group.user_id ' => $user_id)));
        $keyword = $groupname['Group']['keyword'];
        $this->Qrsms->sms($phone_number, $keyword);
        $name = $id;
        $this->Qrsms->draw(150, "qr/" . $name);
        $this->set('qrimage', $url . '/qr/' . $name . '.png');
    }

    function qrcodes($groupname = null, $keyword = null, $id = null)
    {
        $this->layout = 'admin_new_layout';
        $this->set('id', $id);
        $this->set('keyword', $keyword);
        $this->set('groupname', $groupname);
        if (!empty($this->request->data)) {
            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
            app::import('Model', 'Group');
            $this->Group = new Group();
            $groupkeyword = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
            $user_id = $groupkeyword['Group']['user_id'];
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $this->request->data['Contact']['phoneno'], 'ContactGroup.user_id' => $user_id)));
            if (empty($subscriber11)) {
                $contact['user_id'] = $user_id;
                $contact['name'] = $this->request->data['Contact']['name'];
                $contact['phone_number'] = $this->request->data['Contact']['phoneno'];
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $this->Contact->save($contact);
                $contactArr = $this->Contact->getLastInsertId();
            } else {
                $contactArr = $subscriber11['Contact']['id'];
            }
            if (isset($id)) {
                app::import('Model', 'Group');
                $this->Group = new Group();
                $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
                //print_r($keywordname);
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $id)));
                if (empty($subscriber11)) {
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $this->request->data['ContactGroup']['user_id'] = $user_id;
                    $this->request->data['ContactGroup']['group_id'] = $id;
                    $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                    $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                    $this->ContactGroup->save($this->request->data);
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
                    //pr($groups);
                    $groupArr['id'] = $groups['Group']['id'];
                    $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                    /* print_r($groupArr);
                    die();  */
                    $this->Group->save($groupArr);
                    $this->Session->setFlash(__('Contacts have been saved', true));
                    app::import('Model', 'User');
                    $this->User = new User();
                    $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    if ($someone['User']['email_alert_options'] == 0) {
                        if ($someone['User']['email_alerts'] == 1) {
                            $username = $someone['User']['username'];
                            $email = $someone['User']['email'];
                            $date = date('Y-m-d H:i:s', time());
                            $subject = "New Subscriber to " . $group_name;
                            $site_name = str_replace(" ", "", SITENAME);
                            /*$this->Email->to = $email;
                            $this->Email->subject = $subject;
                            $this->Email->from = $site_name;
                            $this->Email->template = 'new_subscriber_template';
                            $this->Email->sendAs = 'html';
                            $this->Email->Controller->set('username', $username);
                            $this->Email->Controller->set('phoneno', $this->request->data['Contact']['phoneno']);
                            $this->Email->Controller->set('groupname', $groupname);
                            $this->Email->Controller->set('keyword', $keyword);
                            $this->Email->Controller->set('datetime', $date);
                            $this->Email->send();*/
                            
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
                            $Email->viewVars(array('phoneno' => $this->request->data['Contact']['phoneno']));
                            $Email->viewVars(array('groupname' => $groupname));
                            $Email->viewVars(array('keyword' => $keyword));
                            $Email->viewVars(array('datetime' => $date));
                            $Email->send();
                        }
                    }
                    $user['sms_balance'] = $someone['User']['sms_balance'] - 1;
                    $user['id'] = $user_id;
                    $this->User->save($user);
                    $to = $this->request->data['Contact']['phoneno'];
                    $from = $someone['User']['assigned_number'];
                    $message = $groupkeyword['Group']['system_message'] . ' ' . $groupkeyword['Group']['auto_message'];
                    $response = $this->Twilio->sendsms($to, $from, $message);
                } else {
                    $this->Session->setFlash(__('This contact already exists other groups', true));
                }
            }

        }

    }

    function contactlist($id)
    {
        $this->layout = null;
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $this->paginate = array('conditions' => array('ContactGroup.group_id' => $id, 'ContactGroup.un_subscribers' => 0), 'order' => array('Contact.name' => 'asc'));
        $data = $this->paginate('ContactGroup');
        $this->set('groups', $data);
        $this->set('id', $id);
        /* $groupcontact=$this->ContactGroup->find('all',array('conditions' => array('ContactGroup.group_id'=>$id,'ContactGroup.un_subscribers'=>0),'order' =>array('Contact.name' => 'asc')));
        $this->set('groups',$groupcontact); */
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
        $this->set('groupcount', $group);
    }

    function deletecontact($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for contact', true));
            $this->redirect(array('action' => 'index'));
        }
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        if ($this->Contact->delete($id)) {
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $contacts_members = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $id)));
            //pr($contacts_member);
            foreach ($contacts_members as $contacts_member) {
                $group_id = $contacts_member['ContactGroup']['group_id'];
                app::import('Model', 'Group');
                $this->Group = new Group();
                $Group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                $this->request->data['Group']['id'] = $group_id;
                $this->request->data['Group']['totalsubscriber'] = $Group['Group']['totalsubscriber'] - 1;
                $this->Group->save($this->request->data);
            }
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            //pr($Groupname);
            $this->ContactGroup->deleteAll(array('ContactGroup.contact_id' => $id));
            $this->Session->setFlash(__('Contact deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Contact was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function editcontact($id = null)
    {
        $this->layout = 'popup';
        $this->set('id', $id);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid contact', true));
            $this->redirect(array('action' => 'index'));
        }
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $contacts_groupname = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        //print_r($contacts_members1);
        $this->set('groupsnames', $contacts_groupname);
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $message = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'Contact.id' => $id)));
        foreach ($message as $message) {
            $groupid[$message['Group']['id']] = $message['Group']['group_name'];
        }
        //print_r($groupid);
        $this->set('groupid', $groupid);
        $this->set('email', $message['Contact']['email']);
        $this->set('birthday', $message['Contact']['birthday']);
        if (!empty($this->request->data)) {
            /* echo "<pre>";
                   print_r($this->request->data);
                    echo "</pre>";  */
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $newsubscriber1 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Group']['phone_number'])));
            /*  echo "<pre>";
          print_r($newsubscriber1);
           echo "</pre>";
          die();
           */
            if (empty($newsubscriber1)) {
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $phoneno1 = $this->request->data['Group']['phone_number'];
                $phone = str_replace('+', '', $phoneno1);
                $phone_number = str_replace('-', '', $phone);
                $contact['user_id'] = $this->Session->read('User.id');
                $contact['name'] = $this->request->data['Group']['name'];
                $contact['phone_number'] = $phone_number;
                $contact['email'] = $this->request->data['Contact']['email'];
                $contact['birthday'] = $this->request->data['Contact']['birthday'];
                $contact['id'] = $id;
                $this->Contact->save($contact);
                $contactArr = $this->Contact->id;
                //$contactArr = $this->Contact->getLastInsertId();
            } else {
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $phoneno1 = $this->request->data['Group']['phone_number'];
                $phone = str_replace('+', '', $phoneno1);
                $phone_number = str_replace('-', '', $phone);
                $contact1['name'] = $this->request->data['Group']['name'];
                $contact1['phone_number'] = $phone_number;
                $contact['email'] = $this->request->data['Contact']['email'];
                $contact['birthday'] = $this->request->data['Contact']['birthday'];
                $contact1['id'] = $id;
                $this->Contact->save($contact1);
                $contactArr = $this->Contact->id;
            }
            if ($contactArr != '') {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $subscriber12 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contactArr)));
                foreach ($subscriber12 as $groupId) {
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $keyword = $this->Group->find('first', array('conditions' => array('Group.id' => $groupId['ContactGroup']['group_id'])));
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $groupArr1['id'] = $keyword['Group']['id'];
                    $groupArr1['totalsubscriber'] = $keyword['Group']['totalsubscriber'] - 1;
                    $this->Group->save($groupArr1);
                }
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $contactsms = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr)));
                $this->ContactGroup->deleteAll(array('ContactGroup.contact_id' => $contactArr));
                foreach ($this->request->data['Group']['id'] as $groupIds) {
                    $group_id = $groupIds;
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    //print_r($keywordname);
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                    if (empty($subscriber11)) {
                        app::import('Model', 'ContactGroup');
                        $this->ContactGroup = new ContactGroup();
                        $this->request->data['ContactGroup']['user_id'] = $this->Session->read('User.id');
                        $this->request->data['ContactGroup']['group_id'] = $group_id;
                        $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                        $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                        $this->request->data['ContactGroup']['subscribed_by_sms'] = $contactsms['ContactGroup']['subscribed_by_sms'];
                        $this->request->data['ContactGroup']['do_not_call'] = $contactsms['ContactGroup']['do_not_call'];
                        $this->ContactGroup->save($this->request->data);
                        app::import('Model', 'Group');
                        $this->Group = new Group();
                        $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                        //pr($groups);
                        $groupArr['id'] = $groups['Group']['id'];
                        $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                        /* print_r($groupArr);
                        die();  */
                        $this->Group->save($groupArr);
                        $this->Session->setFlash(__('Contact has been saved', true));

                    }
                }
            }
            $this->redirect(array('action' => 'index'));
            /* echo "<pre>";
            print_r($subscriber12);
            echo "<pre>"; */
        } else {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $newsubscriber1 = $this->Contact->find('first', array('conditions' => array('Contact.id' => $id)));
            //print_r($newsubscriber1);
            $this->set('newsubscriber1', $newsubscriber1);
        }
    }

    function send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            //app::import('Model', 'ContactGroup');
            //$this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            /*$contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                    $contact_name = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);
            $this->set('contact_name', $contact_name);*/
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
            $this->set('numbers_mms', $numbers_mms);
            $this->set('numbers_sms', $numbers_sms);
            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('users', $users);
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }

    function nexmo_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }

    function plivo_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }
	function bandwidth_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }
    function signalwire_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }
    function ytel_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }
    function telnyx_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id)));
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            //$contacts =$this->Contact->find('list', array('conditions' => array('Contact.user_id' =>$this->Session->read('User.id')), 'fields' => array('phone_number','name')));
            //$this->set(compact('contacts'));
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
        //$this->redirect(array('action' => 'index'));
    }
    function slooce_send_sms($id = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        if (!empty($this->request->data)) {
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $this->Session->write('User.sms_balance', $userDetails['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        if ($userDetails['User']['assigned_number'] != 0) {
            /*app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('Contact.name' => 'asc')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if ($contactsvar['Contact']['name'] != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'];
                }
            }
            $this->set('contacts', $contactnames);*/
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
    }

    function addcontact()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $contacts_members1 = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        //print_r($contacts_members1);
        $this->set('groupname', $contacts_members1);
        if (!empty($this->request->data)) {
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $newsubscriber = $this->ContactGroup->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Group']['phone_number'])));
            if (empty($newsubscriber)) {
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $contact['user_id'] = $this->Session->read('User.id');
                $contact['name'] = $this->request->data['Group']['name'];
                $contact['phone_number'] = $this->request->data['Group']['phone_number'];
                $this->Contact->save($contact);
                $contactArr = $this->Contact->getLastInsertId();
                //$contactArr =  $contact['Contact']['id'];
            } else {
                $contactArr = $newsubscriber['Contact']['id'];
            }
            if ($contactArr != '') {
                //print_r($this->request->data['Group']['id']);
                foreach ($this->request->data['Group']['id'] as $groupIds) {
                    $group_id = $groupIds;
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    //print_r($keywordname);
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                    if (empty($subscriber11)) {
                        app::import('Model', 'ContactGroup');
                        $this->ContactGroup = new ContactGroup();
                        $contactgroup['user_id'] = $this->Session->read('User.id');
                        $contactgroup['group_id'] = $group_id;
                        $contactgroup['group_subscribers'] = $keywordname['Group']['keyword'];
                        $contactgroup['contact_id'] = $contactArr;
                        $this->ContactGroup->save($contactgroup);
                        //print_r($contactgroup);
                        app::import('Model', 'Group');
                        $this->Group = new Group();
                        $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                        //pr($groups);
                        $groupArr['id'] = $groups['Group']['id'];
                        $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                        /* print_r($groupArr);
                        die();  */
                        $this->Group->save($groupArr);
                        $this->Session->setFlash(__('Contacts have been saved', true));
                    } else {
                        $this->Session->setFlash(__('This contact already exists', true));
                    }
                }
            } else {
                $this->Session->setFlash(__('The contact could not be saved. Please, try again.', true));
            }
            $this->redirect(array('controller' => 'groups', 'action' => 'index'));
        }
    }

    function voicebroadcasting($id = null, $group_id = null)
    {
        $this->layout = 'popup';
        app::import('Model', 'User');
        $this->User = new User();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $user_id = $this->Session->read('User.id');
        $users_number = $this->UserNumber->find('list', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.voice' => 1), 'fields' => 'UserNumber.number'));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.voice' => 1)));
        $this->set('usernumber', $users_number);
        $this->set('users', $users);
        $this->set('group_id', $group_id);
        $this->set('id', $id);
        $active = $users['User']['active'];
        if (!empty($this->request->data)) {
            if ($active == 0) {
                $this->Session->setFlash(__('Your account is inactive. You need to activate your account to send voice broadcasts.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
            }
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $Subscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Group']['id'], 'ContactGroup.do_not_call' => 0, 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.phone_number', 'Contact.stickysender')));
            $group_id = $this->request->data['Group']['id'];
            $repeat = $this->request->data['voice']['repeat'];
            $language = $this->request->data['voice']['language'];
            $pause = $this->request->data['voice']['pause'];
            $rotate_number = $this->request->data['voice']['rotate_number'];
            $throttle = $this->request->data['voice']['throttle'];
            $forward = $this->request->data['voice']['forward'];
            $forward_number = $this->request->data['voice']['mobile_number_input'];
            
            if ($this->request->data['voice']['schedule_broadcast'] == 1 && trim($this->request->data['voice']['broadcast_date']) != '') {
                
                app::import('Model', 'ScheduleMessage');
                $this->ScheduleMessage = new ScheduleMessage();
                app::import('Model', 'VoiceMessage');
                $this->VoiceMessage = new VoiceMessage();
                
                $this->request->data['ScheduleMessage']['voice_id'] = $this->request->data['voice']['id'];
                $VoiceMessage = $this->VoiceMessage->find('first', array('conditions' => array('VoiceMessage.group_id' => $group_id)));
                $message_type = $VoiceMessage['VoiceMessage']['message_type'];
                if ($message_type == 1) {  
                    $this->request->data['ScheduleMessage']['message'] = SITE_URL . '/voice/' . $VoiceMessage['VoiceMessage']['audio']; 
                } else {  
                    $this->request->data['ScheduleMessage']['message'] = $VoiceMessage['VoiceMessage']['text_message'];
                }
                if (isset($this->request->data['Group']['number'])) {
                    $this->request->data['ScheduleMessage']['sendfrom'] = $this->request->data['Group']['number'];
                } else {
                    $this->request->data['ScheduleMessage']['sendfrom'] = '';
                }
                $this->request->data['ScheduleMessage']['user_id'] = $user_id;
                $this->request->data['ScheduleMessage']['send_on'] = date('Y-m-d H:i:s', strtotime($this->request->data['voice']['broadcast_date']));
                if (isset($this->request->data['voice']['rotate_number'])) {
                    $this->request->data['ScheduleMessage']['rotate_number'] = $rotate_number;
                } else {
                    $this->request->data['ScheduleMessage']['rotate_number'] = 0;
                }
                $this->request->data['ScheduleMessage']['msg_type'] = 3;
                $this->request->data['ScheduleMessage']['throttle'] = $throttle;
                $this->request->data['ScheduleMessage']['repeatmsg'] = $repeat;
                $this->request->data['ScheduleMessage']['language'] = $language;
                $this->request->data['ScheduleMessage']['pause'] = $pause;
                if (isset($this->request->data['voice']['forward'])) {
                    $this->request->data['ScheduleMessage']['forward'] = $forward;
                } else {
                    $this->request->data['ScheduleMessage']['forward'] = 0;
                }
                $this->request->data['ScheduleMessage']['forwardnumber'] = $forward_number;
             
                $this->ScheduleMessage->save($this->request->data);
                $scheduleMessageid = $this->ScheduleMessage->id;
                app::import('Model', 'ScheduleMessageGroup');
                $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                
                $this->request->data['ScheduleMessageGroup']['group_id'] = $group_id;
                $this->request->data['ScheduleMessageGroup']['schedule_sms_id'] = $scheduleMessageid;
                $this->ScheduleMessageGroup->save($this->request->data);
                
                $this->Session->setFlash(__('Broadcast message has been scheduled', true));
            }else{                
                
                if ($rotate_number == 1) {
                    $from_arr = array();
                    if (!empty($users)) {
                        $from_arr[] = trim($users['User']['assigned_number']);
                    }
                    if (!empty($users_number)) {
                        foreach ($users_number as $values) {
                            $from_arr[] = trim($values['UserNumber']['number']);
                        }
                    }
                }
    
                if (!empty($Subscriber)) {
                    $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    $totalsubscribers = count($Subscriber);
                    if ($usersms['User']['voice_balance'] >= $totalsubscribers) {
                        if ($usersms['User']['voice_balance'] > 0) {
                            if (API_TYPE == 0) {
                                if ($rotate_number == 1) {
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
                                        //$logarr['Log']['group_id'] = $contacts['Contact']['group_id'];
                                        $logarr['Log']['group_id'] = $group_id;
                                        $logarr['Log']['phone_number'] = $callnumber;
                                        $logarr['Log']['route'] = 'inbox';
                                        $logarr['Log']['msg_type'] = 'broadcast';
                                        $this->Log->save($logarr);
                                        $lastinsertID = $this->Log->getLastInsertId();
                                        $Sid = '';
                                        if ($lastinsertID > 0) {
                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
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
                                        $callerPhone = trim($this->request->data['Group']['number']);
                                        Controller::loadModel('Log');
                                        $logarr['Log']['id'] = '';
                                        $logarr['Log']['user_id'] = $user_id;
                                        //$logarr['Log']['group_id'] = $contacts['Contact']['group_id'];
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
                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
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
                            } else if (API_TYPE == 4 || API_TYPE == 5 || API_TYPE == 6 || API_TYPE == 7) {							
                                if ($rotate_number == 1) {                                
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
                                            /*$response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                                            $errortext = '';										
                                            if(isset($response->message)){											
                                                $errortext = $response->message;											
                                                $status = 'failed';										
                                                
                                            }  */
                                            
                                            if(API_TYPE==5){
    									        $response = $this->Signalwire->callNumberToPEOPLE(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
    										    
                                        		$status = '';
                                    			$errortext = '';
                                    			if(isset($response->error_message)){
                                    				$errortext = $response->error_message;
                                    				$status = 'failed';
                                    			}else{
                                    			    $errortext = '';
                                    				$status = 'sent';
                                    			}
                                            }elseif(API_TYPE==6){
    									        $response = $this->Ytel->callNumberToPEOPLE(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
    										    
                                        		$status = '';
                                    			$errortext = '';
                                    			if($response->Message360->ResponseStatus == 0){
                                    				$errortext = $response->Message360->Errors->Error[0]->Message;
                                    				$status = 'failed';
                                    			}else{
                                    			    $errortext = '';
                                    				$status = 'sent';
                                    			}
                                            }elseif(API_TYPE==7){
    									        $response = $this->Telnyx->callNumberToPEOPLE(TELNYX_KEY,TELNYX_CALLAPP,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
    										    
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
                					                $response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                
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
                                        $callerPhone = trim($this->request->data['Group']['number']);                                    
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
                                            /*$response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										                                        
                                            $errortext = '';										
                                            if(isset($response->message)){											
                                                $errortext = $response->message;											
                                                $status = 'failed';									
                                                
                                            }   */
                                            
                                            if(API_TYPE==5){
    									        $response = $this->Signalwire->callNumberToPEOPLE(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
    										    
                                        		$status = '';
                                    			$errortext = '';
                                    			if(isset($response->error_message)){
                                    				$errortext = $response->error_message;
                                    				$status = 'failed';
                                    			}else{
                                    			    $errortext = '';
                                    				$status = 'sent';
                                    			}
                                            }elseif(API_TYPE==6){
    									        $response = $this->Ytel->callNumberToPEOPLE(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
    										    
                                        		$status = '';
                                    			$errortext = '';
                                    			if($response->Message360->ResponseStatus == 0){
                                    				$errortext = $response->Message360->Errors->Error[0]->Message;
                                    				$status = 'failed';
                                    			}else{
                                    			    $errortext = '';
                                    				$status = 'sent';
                                    			}
                                            }elseif(API_TYPE==7){
    									        $response = $this->Telnyx->callNumberToPEOPLE(TELNYX_KEY,TELNYX_CALLAPP,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);										
    										    
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
                					                $response = $this->Bandwidth->callNumberToPEOPLE(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number);										
                
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
                                    
                            } else if (API_TYPE == 3) {
                                if ($rotate_number == 1) {
                                    $k = 0;
    
                                    foreach ($Subscriber as $contacts) {
                                        $callnumber = $contacts['Contact']['phone_number'];
                                        //$callerPhone = $this->request->data['Group']['number'];
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
                                        //$logarr['Log']['group_id'] = $contacts['Contact']['group_id'];
                                        $logarr['Log']['group_id'] = $group_id;
                                        $logarr['Log']['phone_number'] = $callnumber;
                                        $logarr['Log']['route'] = 'inbox';
                                        $logarr['Log']['msg_type'] = 'broadcast';
                                        $this->Log->save($logarr);
                                        $lastinsertID = $this->Log->getLastInsertId();
                                        $Sid = '';
                                        if ($lastinsertID > 0) {
                                            $this->Plivo->AuthId = PLIVO_KEY;
                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                            $response = $this->Plivo->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);
                                        }
                                        $k = $k + 1;
    
                                    }
    
                                } else {
    
                                    foreach ($Subscriber as $contacts) {
                                        $callnumber = $contacts['Contact']['phone_number'];
                                        $callerPhone = trim($this->request->data['Group']['number']);
                                        Controller::loadModel('Log');
                                        $logarr['Log']['id'] = '';
                                        $logarr['Log']['user_id'] = $user_id;
                                        //$logarr['Log']['group_id'] = $contacts['Contact']['group_id'];
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
                                            $this->Plivo->AuthId = PLIVO_KEY;
                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                            $response = $this->Plivo->callNumberToPEOPLE($callnumber, $callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number);
                                        }
    
                                    }
                                }
                            } else {
                                if ($rotate_number == 1) {
                                    $k = 0;
                                    foreach ($Subscriber as $contacts) {
                                        $callnumber = $contacts['Contact']['phone_number'];
                                        //$callerPhone = $this->request->data['Group']['number'];
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
                                        //$logarr['Log']['group_id'] = $contacts['Contact']['group_id'];
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
                                                        /*$this->Email->to = $email;
                                                        $this->Email->subject = $subject;
                                                        $this->Email->from = $sitename;
                                                        $this->Email->template = 'low_voice_credit_template';
                                                        $this->Email->sendAs = 'html';
                                                        $this->Email->Controller->set('username', $username);
                                                        $this->Email->Controller->set('low_voice_balances', $usersms['User']['low_voice_balances']);
                                                        $this->Email->send();*/
                                                        
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
                                        $callerPhone = trim($this->request->data['Group']['number']);
                                        Controller::loadModel('Log');
                                        $logarr['Log']['id'] = '';
                                        $logarr['Log']['user_id'] = $user_id;
                                        //$logarr['Log']['group_id'] = $contacts['Contact']['group_id'];
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
                                                        /*$this->Email->to = $email;
                                                        $this->Email->subject = $subject;
                                                        $this->Email->from = $sitename;
                                                        $this->Email->template = 'low_voice_credit_template';
                                                        $this->Email->sendAs = 'html';
                                                        $this->Email->Controller->set('username', $username);
                                                        $this->Email->Controller->set('low_voice_balances', $usersms['User']['low_voice_balances']);
                                                        $this->Email->send();*/
                                                        
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
                            $this->Session->setFlash(__('Broadcast message has been sent', true));
                        } else {
                            $this->Session->setFlash(__('Voice credit balance is too low. Please purchase more voice credits', true));
                        }
                    } else {
                        $this->Session->setFlash(__('You do not have enough credits to send a broadcast to these contacts', true));
                    }
                } else {
                    $this->Session->setFlash(__('Broadcast message could not be sent. Please, try again', true));
                }
            }
            $this->redirect(array('controller' => 'groups', 'action' => 'voicebroadcasting?status=success'));
        }
    }
    
    function sendrvm($id = null, $group_id = null)
    {
        $this->layout = 'popup';
        app::import('Model', 'User');
        $this->User = new User();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $user_id = $this->Session->read('User.id');
        $users_number = $this->UserNumber->find('list', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.voice' => 1), 'fields' => 'UserNumber.number'));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.voice' => 1)));
        $this->set('usernumber', $users_number);
        $this->set('users', $users);
        $this->set('group_id', $group_id);
        $this->set('id', $id);
        $active = $users['User']['active'];
        
        if (!empty($this->request->data)) {
            if ($active == 0) {
                $this->Session->setFlash(__('Your account is inactive. You need to activate your account to send ringless voicemail.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
            }
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $Subscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Group']['id'], 'ContactGroup.do_not_call' => 0, 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.phone_number')));
            
            app::import('Model', 'VoiceMessage');
            $this->VoiceMessage = new VoiceMessage();
            $VoiceMessage = $this->VoiceMessage->find('first', array('conditions' => array('VoiceMessage.group_id' => $this->request->data['Group']['id'])));

            $from = $this->request->data['Group']['number'];
            $callerid = $this->request->data['Group']['callerid'];
            
            if (!empty($Subscriber)) {
                $usersms = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                $totalsubscribers = count($Subscriber);
                if ($usersms['User']['voice_balance'] >= $totalsubscribers) {
                        if (API_TYPE == 6) {
                            
                            $rotate_number = $this->request->data['Group']['rotate_number'];
                            $rotate_callerid = $this->request->data['Group']['rotate_callerid'];

                            if ($rotate_number == 1) {
                                $from_numberarr = array();
                                if (!empty($users)) {
                                    $from_numberarr[] = trim($users['User']['assigned_number']);
                                }
                                if (!empty($users_number)) {
                                    foreach ($users_number as $values) {
                                        $from_numberarr[] = trim($values);
                                    }
                                }
                            }
                            
                            if ($rotate_callerid == 1) {
                                $from_calleridarr = array();
                                if (!empty($users)) {
                                    $from_calleridarr[] = trim($users['User']['assigned_number']);
                                }
                                if (!empty($users_number)) {
                                    foreach ($users_number as $values) {
                                        $from_calleridarr[] = trim($values);
                                    }
                                }
                            }
                                
                            foreach ($Subscriber as $contacts) {                                    
                                $to = $contacts['Contact']['phone_number'];  
                                
                                if ($rotate_number == 1 || $rotate_callerid == 1) {
                                    $retry = false;
    					            $retries = 0;
    					            $MAX_RETRIES = 10;  
    					            
    					            do{
    					                if ($rotate_number == 1) {
                                            $random_keys = array_rand($from_numberarr, 1);
                                            $from = $from_numberarr[$random_keys];
                                        }
                                        if ($rotate_callerid == 1) {
                                            $random_keys = array_rand($from_calleridarr, 1);
                                            $callerid = $from_calleridarr[$random_keys];
                                        }
    					                if(trim($callerid) == trim($from)){
    		                                $retry = true;
                                            $retries = $retries + 1;
                                        }else{
                                            $retry = false;
                                        }
    				
    					            } while ($retry && $retries < $MAX_RETRIES);
                                }
                                
      					        $response = $this->Ytel->sendrvm(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$callerid,$VoiceMessage['VoiceMessage']['audio'],$user_id,$this->request->data['Group']['id'],$usersms['User']['voice_balance']);

                    			if($response->Message360->ResponseStatus == 0){
                    				$errortext = $response->Message360->Errors->Error[0]->Message;
                    			}else{
                    			    $errortext = '';
                    			}
								
								if ($errortext != '') {                                 
                                    $this->Session->setFlash(__($errortext, true));
                                    break;
                                } 
                            }
                        } 
                        if ($errortext == '') {
                            $this->Session->setFlash(__('Ringless Voicemail has been sent', true));
                        }
                } else {
                    $this->Session->setFlash(__('You do not have enough credits to send a ringless voicemail to these contacts', true));
                }
            } else {
                $this->Session->setFlash(__('Ringless voicemail could not be sent. Please, try again', true));
            }
            
            $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
        }
    }

    function voicebroadcast()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group_list = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('groups', $group_list);
        if (!empty($this->request->data)) {
            app::import('Model', 'VoiceMessage');
            $this->VoiceMessage = new VoiceMessage();
            $group_id = $this->request->data['Group']['id'];
            $voice_arr = $this->VoiceMessage->find('first', array('conditions' => array('VoiceMessage.group_id' => $group_id)));
            if (empty($voice_arr)) {
                $this->request->data['VoiceMessage']['user_id'] = $user_id;
                $this->request->data['VoiceMessage']['group_id'] = $this->request->data['Group']['id'];
                $this->request->data['VoiceMessage']['text_message'] = $this->request->data['Group']['text_message'];
                $this->request->data['VoiceMessage']['message_type'] = $this->request->data['Group']['msg_type'];
                $this->request->data['VoiceMessage']['created'] = date('Y-m-d h:i:s a');
                if ($this->request->data['Group']['msg_type'] == 1) {
                    $filename = str_replace(' ', '_', $this->request->data['Group']['mp3']['name']);
                    $filename1 = str_replace('(', '_', $filename);
                    $filename2 = str_replace(')', '_', $filename1);
                    $filenewname = time() . $filename2;
                    //if (move_uploaded_file($this->request->data['Group']['mp3']['tmp_name'], 'voice/' . $filenewname)) {
                    //    $this->request->data['VoiceMessage']['audio'] = $filenewname;
                    //}
                    
                    $extension = strtolower(pathinfo($filenewname, PATHINFO_EXTENSION));
					$extension_arr = array('mp3','wav');
					if (!in_array($extension, $extension_arr)){
						$this->Session->setFlash(__('Please select mp3 or wav file type only for voicebroadcast message type', true));
						$this->redirect(array('controller' => 'groups', 'action' => 'voicebroadcast'));
					}else{
                        move_uploaded_file($this->request->data['Group']['mp3']['tmp_name'], 'voice/' . $filenewname);
                        $this->request->data['VoiceMessage']['audio'] = $filenewname;
                    }
                }
                if ($this->VoiceMessage->save($this->request->data)) {
                    $this->Session->setFlash(__('Broadcast message saved.', true));
                    $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
                } else {
                    $this->Session->setFlash(__('Broadcast message not  saved.', true));
                    $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
                }

            } else {
                $this->Session->setFlash(__('Broadcast message already assigned to this group.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
            }
        }
    }

    function broadcast_list()
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'VoiceMessage');
        $this->VoiceMessage = new VoiceMessage();
        $user_id = $this->Session->read('User.id');
        $this->paginate = array(
            'conditions' => array('VoiceMessage.user_id' => $user_id), 'order' => array('VoiceMessage.id' => 'desc')
        );
        $data = $this->paginate('VoiceMessage');
        $this->set('vioce_broad', $data);
    }

    function edit_broadcast($id = null)
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'VoiceMessage');
        $this->VoiceMessage = new VoiceMessage();
        app::import('Model', 'Group');
        $this->Group = new Group();
        //$group_list=$this->Group->find('list',array('conditions'=>array('Group.user_id'=>$user_id),'fields'=>'Group.group_name','order' =>array('Group.group_name' => 'asc')));
        $group_ids = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        //$this->set('group_lists',$group_list);
        $this->set('group_ids', $group_ids);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid id', true));
            $this->redirect(array('action' => 'broadcast_list'));
        }
        $voice_arr = $this->VoiceMessage->find('first', array('conditions' => array('VoiceMessage.id' => $id)));
        $this->set('broad_cast', $voice_arr);
        if (!empty($this->request->data)) {
            $voice_arr = $this->VoiceMessage->find('first', array('conditions' => array('VoiceMessage.id' => $this->request->data['VoiceMessage']['id'], 'VoiceMessage.group_id' => $this->request->data['VoiceMessage']['group_id'])));
            if (!empty($voice_arr)) {
                app::import('Model', 'VoiceMessage');
                $this->VoiceMessage = new VoiceMessage();
                $this->request->data['VoiceMessage']['id'] = $this->request->data['VoiceMessage']['id'];
                $this->request->data['VoiceMessage']['user_id'] = $user_id;
                $this->request->data['VoiceMessage']['group_id'] = $this->request->data['VoiceMessage']['group_id'];
                $this->request->data['VoiceMessage']['text_message'] = $this->request->data['VoiceMessage']['text_message'];
                $this->request->data['VoiceMessage']['message_type'] = $this->request->data['VoiceMessage']['msg_type'];
                $this->request->data['VoiceMessage']['created'] = date('Y-m-d h:i:s a');
                if ($this->request->data['VoiceMessage']['msg_type'] == 1) {
                    $filename = str_replace(' ', '_', $this->request->data['VoiceMessage']['mp3']['name']);
                    $filename1 = str_replace('(', '_', $filename);
                    $filename2 = str_replace(')', '_', $filename1);
                    $filenewname = time() . $filename2;
                    //if (move_uploaded_file($this->request->data['VoiceMessage']['mp3']['tmp_name'], 'voice/' . $filenewname)) {
                    //    $this->request->data['VoiceMessage']['audio'] = $filenewname;
                    //}
                    
                    $extension = strtolower(pathinfo($filenewname, PATHINFO_EXTENSION));
					$extension_arr = array('mp3','wav');
					if (!in_array($extension, $extension_arr)){
						$this->Session->setFlash(__('Please select mp3 or wav file type only for voicebroadcast message type', true));
						$this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
					}else{
                        move_uploaded_file($this->request->data['VoiceMessage']['mp3']['tmp_name'], 'voice/' . $filenewname);
                        $this->request->data['VoiceMessage']['audio'] = $filenewname;
                    }
                }
                if ($this->VoiceMessage->save($this->request->data)) {
                    $this->Session->setFlash(__('Broadcast message updated.', true));
                    $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
                } else {
                    $this->Session->setFlash(__('Broadcast message not  updated.', true));
                    $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
                }
            } else {
                $this->Session->setFlash(__('Broadcast message already assigned to this group.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
            }
        }
        if (empty($this->request->data)) {
            //echo 'test';
            $this->VoiceMessage->read(null, $id);
        }
    }

    function delete_broadcast($id = null)
    {
        $this->autoRender = false;
        if (!$id) {
            $this->Session->setFlash(__('Invalid id', true));
            $this->redirect(array('action' => 'broadcast_list'));
        }
        if ($id != '') {
            app::import('Model', 'VoiceMessage');
            $this->VoiceMessage = new VoiceMessage();
            if ($this->VoiceMessage->delete($id)) {
                $this->Session->setFlash(__('Broadcast message deleted.', true));
                $this->redirect(array('controller' => 'groups', 'action' => 'broadcast_list'));
            }

        }
    }

    function do_not_call($id = null)
    {
        $this->layout = 'popup';
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        app::import('Model', 'Group');
        $this->Group = new Group();
        $this->paginate = array('conditions' => array('ContactGroup.group_id' => $id, 'ContactGroup.do_not_call' => 1), 'order' => array('ContactGroup.id' => 'desc'));
        $data = $this->paginate('ContactGroup');
        $this->set('calls', $data);
        $group_names = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
        $this->set('group_name', $group_names);
        $totalUsers = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.group_id' => $id)));
        $this->set('count_user', $totalUsers);

    }

    function copygroup($id)
    {

        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');

        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('first', array('conditions' => array('Group.user_id' => $user_id, 'Group.id' => $id)));

        $copygroup['group_type'] = $Group['Group']['group_type'];
        $copygroup['system_message'] = $Group['Group']['system_message'];
        $copygroup['ifmember_message'] = $Group['Group']['ifmember_message'];
        $copygroup['sms_type'] = $Group['Group']['sms_type'];
        $copygroup['bithday_enable'] = $Group['Group']['bithday_enable'];
        $copygroup['image_url'] = $Group['Group']['image_url'];
        $copygroup['notify_signup'] = $Group['Group']['notify_signup'];
        $copygroup['double_optin'] = $Group['Group']['double_optin'];
        $copygroup['mobile_number_input'] = $Group['Group']['mobile_number_input'];

        $copygroup['property_address'] = $Group['Group']['property_address'];
        $copygroup['property_price'] = $Group['Group']['property_price'];
        $copygroup['property_bed'] = $Group['Group']['property_bed'];
        $copygroup['property_bath'] = $Group['Group']['property_bath'];
        $copygroup['property_description'] = $Group['Group']['property_description'];
        $copygroup['property_url'] = $Group['Group']['property_url'];

        $copygroup['vehicle_year'] = $Group['Group']['vehicle_year'];
        $copygroup['vehicle_make'] = $Group['Group']['vehicle_make'];
        $copygroup['vehicle_model'] = $Group['Group']['vehicle_model'];
        $copygroup['vehicle_mileage'] = $Group['Group']['vehicle_mileage'];
        $copygroup['vehicle_price'] = $Group['Group']['vehicle_price'];
        $copygroup['vehicle_description'] = $Group['Group']['vehicle_description'];
        $copygroup['vehicle_url'] = $Group['Group']['vehicle_url'];

        $finaljson[] = $copygroup;

        echo $json_string = json_encode($finaljson);


    }


}

?>