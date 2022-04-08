<?php
App::import('Vendor', 'mailchimp', array('file' => 'mailchimp/MailChimp.php'));
App::import('Vendor', 'getresponse', array('file' => 'getresponse/GetResponse.php'));
App::import('Vendor', 'activecampaign', array('file' => 'activecampaign/ActiveCampaign.class.php'));
App::import('Vendor', 'aweber', array('file' => 'aweber/aweber_api.php'));
App::import('Vendor', 'mailin', array('file' => 'mailin/Mailin.php'));
App::uses('CakeEmail', 'Network/Email');

class WebwidgetsController extends AppController
{
    var $name = 'Webwidgets';
    var $layout = 'default';
    var $components = array('Twilio', 'Nexmomessage', 'Mms', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');
    var $uses = array('Contact', 'Group', 'User');

    function index($file = null)
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        $groups = $this->Group->find('list', array('fields' => array('Group.group_name'), 'conditions' => array('Group.user_id' => $user_id)));
        $this->set('groups', $groups);
    }

    function subscribe()
    {
        $this->layout = null;
        $saved = false;
        $err = '';
        
        $groupid = $_GET['group'];
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('first', array('conditions' => array('Group.id' => $groupid)));
        $user_id = $group['Group']['user_id'];
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('country', $users['User']['user_country']);
            
        
        if (!empty($this->request->data)) {
            app::import('Model', 'ActivityTimeline');
            $this->ActivityTimeline = new ActivityTimeline();
            app::import('Model', 'Group');
            $this->Group = new Group();
            $group = $this->Group->find('first', array('conditions' => array('Group.id' => $this->request->data['Contact']['group_id'])));
            $user_id = $group['Group']['user_id'];
            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $API_TYPE = $users['User']['api_type'];
            $SID = trim($users['User']['sid']);
            $AUTHTOKEN = trim($users['User']['authtoken']);
            $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
            $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);

            if (NUMVERIFY != '') {
                $numbervalidation = $this->validateNumber($this->request->data['Contact']['phone_number']);
                $errorcode = $numbervalidation['error']['code'];
                $valid = $numbervalidation['valid'];
                $linetype = $numbervalidation['line_type'];

                if ($errorcode == '') {
                    if ($valid != 1) {
                        $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                        $this->set('err', $err);
                        $this->set('saved', $saved);
                    } else if (trim($linetype) != 'mobile') {
                        $err = "The line type of the number entered is " . $linetype . ". You must provide a mobile number.";
                        $this->set('err', $err);
                        $this->set('saved', $saved);
                    } else {
                        $contact['carrier'] = $numbervalidation['carrier'];
                        $contact['location'] = $numbervalidation['location'];
                        $contact['phone_country'] = $numbervalidation['country_name'];
                        $contact['line_type'] = $numbervalidation['line_type'];
                    }
                } else {
                    ob_start();
                    print_r($numbervalidation['error']['info']);
                    $out1 = ob_get_contents();
                    ob_end_clean();
                    $file = fopen("debug/NumberVerifyAPI" . time() . ".txt", "w");
                    fwrite($file, $out1);
                    fclose($file);

                }
            }elseif ($API_TYPE==6){
                
                if ($SID == ''){
                    $carrier = $this->Ytel->lookupcarrier(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$this->request->data['Contact']['phone_number']);
                }else{
                    $carrier = $this->Ytel->lookupcarrier($SID,$AUTHTOKEN,$this->request->data['Contact']['phone_number']);
                }
                
                if ($carrier->Message360->ResponseStatus == 0) {
                    $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                    $this->set('err', $err);
                    $this->set('saved', $saved);
                } else if (trim($carrier->Message360->Carrier->Wireless) !='true') {
                    $err = "The line type of the number entered is not wireless. You must provide a mobile number.";
                    $this->set('err', $err);
                    $this->set('saved', $saved);
                } else {
                    $contact['carrier']  = $carrier->Message360->Carrier->Network;
                    $contact['location'] = $carrier->Message360->Carrier->City;
                }
                
            }elseif ($API_TYPE==7){
                
                if ($SID == ''){
                    $carrier = $this->Telnyx->numberlookup(TELNYX_KEY,$this->request->data['Contact']['phone_number']);
                }else{
                    $carrier = $this->Telnyx->numberlookup($SID,$this->request->data['Contact']['phone_number']);
                }
                
                if(isset($carrier->errors)){
                    $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                    $this->set('err', $err);
                    $this->set('saved', $saved);
                } else if (trim($carrier->data->carrier->type) !='mobile') {
                    $err = "The line type of the number entered is not wireless. You must provide a mobile number.";
                    $this->set('err', $err);
                    $this->set('saved', $saved);
                } else {
                    $carrier_name = explode(':', $carrier->data->carrier->name);
                    $carrier_name = $carrier_name[0];
                    $contact['carrier']  = $carrier_name;
                    //$contact['location'] = $carrier->Message360->Carrier->City;
                }
                
            }
            if ($err == '') {
                if (preg_match("/^[0-9]+$/", $this->request->data['Contact']['phone_number'])) {
                    
                    //****DOUBLE OPT-IN CHANGES****//
                    $double_optin = $group['Group']['double_optin'];
                    $phone = $this->request->data['Contact']['phone_number'];

                    if ($API_TYPE == 2) {
                        /*app::import('Model','Group');
                        $this->Group = new Group();
                        $group = $this->Group->find('first',array('conditions'=>array('Group.id'=>$this->request->data['Contact']['group_id'])));*/
                        if (!empty($group)) {
                            //$user_id=$group['Group']['user_id'];
                            //$users=$this->User->find('first',array('conditions' => array('User.id'=>$user_id)));
                            if (!empty($users)) {
                                $response = $this->Slooce->supported($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $this->request->data['Contact']['phone_number'], $users['User']['keyword']);
                                if ($response == 'supported') {
                                    $sms_type = $group['Group']['sms_type'];
                                    $system_message = $group['Group']['system_message'];
                                    $auto_message = $group['Group']['auto_message'];
                                    $image_url = $group['Group']['image_url'];
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $group_id = $group['Group']['id'];
                                    $user_id = $group['Group']['user_id'];
                                    app::import('Model', 'Contact');
                                    $this->Contact = new Contact();
                                    $newsubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number']), 'order' => array('Contact.id' => 'desc')));
                                    if (empty($newsubscriber)) {
                                        $this->Contact = new Contact();
                                        $contact['user_id'] = $group['Group']['user_id'];
                                        $contact['phone_number'] = $this->request->data['Contact']['phone_number'];
                                        if (isset($this->request->data['Contact']['name'])) {
                                            if (trim($this->request->data['Contact']['name']) !=''){
                                                $contact['name'] = trim($this->request->data['Contact']['name']);
                                            }
                                        }
                                        if (isset($this->request->data['Contact']['email'])) {
                                            if ($this->request->data['Contact']['email'] != '') {
                                                $contact['email'] = trim($this->request->data['Contact']['email']);
                                            }
                                        }
                                        if (isset($this->request->data['Contact']['birthday'])) {
                                            if ($this->request->data['Contact']['birthday'] != '') {
                                                $contact['birthday'] = $this->request->data['Contact']['birthday'];
                                            }
                                        }
                                        if (isset($this->request->data['Contact']['city'])) {
                                            if ($this->request->data['Contact']['city'] != '') {
                                                $contact['location'] = trim($this->request->data['Contact']['city']);
                                            }
                                        }
                                        if (isset($this->request->data['Contact']['state'])) {
                                            if ($this->request->data['Contact']['state'] != '') {
                                                $contact['state'] = trim($this->request->data['Contact']['state']);
                                            }
                                        }
                                        if (isset($this->request->data['Contact']['zip'])) {
                                            if ($this->request->data['Contact']['zip'] != '') {
                                                $contact['zip'] = trim($this->request->data['Contact']['zip']);
                                            }
                                        }
                                        $contact['color'] = $this->choosecolor();
                                        //****DOUBLE OPT-IN CHANGES****//
                                        if($double_optin==1){
                                            $contact['un_subscribers'] = 2; 
                                        }
                                        
                                        $this->Contact->save($contact);
                                        $contactArr = $this->Contact->getLastInsertId();

                                        if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $contact['email'] != '') {
                                            if ($users['User']['email_service'] == 1) { //Mailchimp

                                                $list_id = $users['User']['email_listid'];
                                                $MailChimp = new MailChimp($users['User']['email_apikey']);

                                                if ($contact['name'] != '') {
                                                    $fullname = explode(' ', $contact['name']);
                                                    $firstname = $fullname[0];

                                                    $result = $MailChimp->post("lists/$list_id/members", array(
                                                        'email_address' => $contact['email'],
                                                        'status' => 'subscribed',
                                                        'merge_fields' => array('FNAME' => $firstname),
                                                    ));

                                                } else {
                                                    $result = $MailChimp->post("lists/$list_id/members", array(
                                                        'email_address' => $contact['email'],
                                                        'status' => 'subscribed',
                                                    ));
                                                }
                                            } else if ($users['User']['email_service'] == 2) { //Getresponse

                                                $list_id = $users['User']['email_listid'];
                                                $GetResponse = new GetResponse($users['User']['email_apikey']);

                                                if ($contact['name'] != '') {
                                                    $fullname = explode(' ', $contact['name']);
                                                    $firstname = $fullname[0];

                                                    $result = $GetResponse->addContact(array(
                                                        'email' => $contact['email'],
                                                        'name' => $contact['name'],
                                                        'campaign' => array('campaignId' => $list_id)));
                                                } else {

                                                    $result = $GetResponse->addContact(array(
                                                        'email' => $contact['email'],
                                                        'campaign' => array('campaignId' => $list_id)));

                                                }

                                            } else if ($users['User']['email_service'] == 3) { //Active Campaign

                                                $ac = new ActiveCampaign($users['User']['email_apiurl'], $users['User']['email_apikey']);
                                                $list_id = (int)$users['User']['email_listid'];
                                                $fullname = explode(' ', $contact['name']);
                                                $firstname = $fullname[0];

                                                $newcontact = array(
                                                    'email' => $contact['email'],
                                                    'first_name' => $firstname,
                                                    'phone' => $contact['phone_number'],
                                                    'p[{$list_id}]' => $list_id,
                                                    'status[{$list_id}]' => 1, // "Active" status
                                                );

                                                $contact_sync = $ac->api("contact/sync", $newcontact);

                                            } else if ($users['User']['email_service'] == 4) { //AWeber

                                                $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);
                                                $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);

                                                $account_id = $account->id;
                                                $list_id = $users['User']['email_listid'];
                                                $fullname = explode(' ', $contact['name']);
                                                $firstname = $fullname[0];

                                                $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                                                $list = $account->loadFromUrl($listURL);

                                                $params = array(
                                                    'email' => $contact['email'],
                                                    'name' => $firstname,
                                                );

                                                $subscribers = $list->subscribers;
                                                $new_subscriber = $subscribers->create($params);

                                            } else if ($users['User']['email_service'] == 5) { //Sendinblue

                                                $mailin = new Mailin(SENDINBLUE_APIURL, $users['User']['email_apikey']);

                                                $list_id = (int)$users['User']['email_listid'];
                                                $fullname = explode(' ', $contact['name']);
                                                $firstname = $fullname[0];

                                                $data = array("email" => $contact['email'],
                                                    "attributes" => array("FIRSTNAME" => $firstname),
                                                    "listid" => array($list_id)
                                                );

                                                $result = $mailin->create_update_user($data);

                                            }


                                        }
                                        //$contactArr = 0;
                                    } else {
                                        $contactArr = $newsubscriber['Contact']['id'];
                                    }

                                    if ($contactArr != '') {
                                        app::import('Model', 'Group');
                                        $this->Group = new Group();
                                        $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id, 'ContactGroup.user_id' => $user_id)));
                                        $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                        $timezone = $user_arr['User']['timezone'];
                                        date_default_timezone_set($timezone);
                                        app::import('Model', 'ContactGroup');
                                        if (empty($subscriber11)) {
                                            
                                            if (!empty($newsubscriber)) {
                                                $contact['id'] = $contactArr;
                                                if (isset($this->request->data['Contact']['name'])) {
                                                    if (trim($this->request->data['Contact']['name']) !=''){
                                                        $contact['name'] = trim($this->request->data['Contact']['name']);
                                                    }
                                                }
                                                if (isset($this->request->data['Contact']['email'])) {
                                                    if ($this->request->data['Contact']['email'] != '') {
                                                        $contact['email'] = trim($this->request->data['Contact']['email']);
                                                    }
                                                }
                                                if (isset($this->request->data['Contact']['birthday'])) {
                                                    if ($this->request->data['Contact']['birthday'] != '') {
                                                        $contact['birthday'] = $this->request->data['Contact']['birthday'];
                                                    }
                                                }
                                                if (isset($this->request->data['Contact']['city'])) {
                                                    if ($this->request->data['Contact']['city'] != '') {
                                                        $contact['location'] = trim($this->request->data['Contact']['city']);
                                                    }
                                                }
                                                if (isset($this->request->data['Contact']['state'])) {
                                                    if ($this->request->data['Contact']['state'] != '') {
                                                        $contact['state'] = trim($this->request->data['Contact']['state']);
                                                    }
                                                }
                                                if (isset($this->request->data['Contact']['zip'])) {
                                                    if ($this->request->data['Contact']['zip'] != '') {
                                                        $contact['zip'] = trim($this->request->data['Contact']['zip']);
                                                    }
                                                }
                                                $this->Contact->save($contact);
                                            
                                                if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $contact['email'] != '') {
                                                    if ($users['User']['email_service'] == 1) { //Mailchimp
            
                                                        $list_id = $users['User']['email_listid'];
                                                        $MailChimp = new MailChimp($users['User']['email_apikey']);
            
                                                        if ($contact['name'] != '') {
                                                            $fullname = explode(' ', $contact['name']);
                                                            $firstname = $fullname[0];
            
                                                            $result = $MailChimp->post("lists/$list_id/members", array(
                                                                'email_address' => $contact['email'],
                                                                'status' => 'subscribed',
                                                                'merge_fields' => array('FNAME' => $firstname),
                                                            ));
            
                                                        } else {
                                                            $result = $MailChimp->post("lists/$list_id/members", array(
                                                                'email_address' => $contact['email'],
                                                                'status' => 'subscribed',
                                                            ));
                                                        }
                                                    } else if ($users['User']['email_service'] == 2) { //Getresponse
                    
                                                        $list_id = $users['User']['email_listid'];
                                                        $GetResponse = new GetResponse($users['User']['email_apikey']);
                    
                                                        if ($contact['name'] != '') {
                                                            $fullname = explode(' ', $contact['name']);
                                                            $firstname = $fullname[0];
                    
                                                            $result = $GetResponse->addContact(array(
                                                                'email' => $contact['email'],
                                                                'name' => $contact['name'],
                                                                'campaign' => array('campaignId' => $list_id)));
                                                        } else {
                    
                                                            $result = $GetResponse->addContact(array(
                                                                'email' => $contact['email'],
                                                                'campaign' => array('campaignId' => $list_id)));
                    
                                                        }
                    
                                                    } else if ($users['User']['email_service'] == 3) { //Active Campaign
                    
                                                        $ac = new ActiveCampaign($users['User']['email_apiurl'], $users['User']['email_apikey']);
                                                        $list_id = (int)$users['User']['email_listid'];
                                                        $fullname = explode(' ', $contact['name']);
                                                        $firstname = $fullname[0];
                    
                                                        $newcontact = array(
                                                            'email' => $contact['email'],
                                                            'first_name' => $firstname,
                                                            'phone' => $contact['phone_number'],
                                                            'p[{$list_id}]' => $list_id,
                                                            'status[{$list_id}]' => 1, // "Active" status
                                                        );
                    
                                                        $contact_sync = $ac->api("contact/sync", $newcontact);
                    
                    
                                                    } else if ($users['User']['email_service'] == 4) { //AWeber
                    
                                                        $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);
                                                        $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);
                    
                                                        $account_id = $account->id;
                                                        $list_id = $users['User']['email_listid'];
                                                        $fullname = explode(' ', $contact['name']);
                                                        $firstname = $fullname[0];
                    
                                                        $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                                                        $list = $account->loadFromUrl($listURL);
                    
                                                        $params = array(
                                                            'email' => $contact['email'],
                                                            'name' => $firstname,
                                                        );
                    
                                                        $subscribers = $list->subscribers;
                                                        $new_subscriber = $subscribers->create($params);
                    
                                                    } else if ($users['User']['email_service'] == 5) { //Sendinblue
                    
                                                        $mailin = new Mailin("https://api.sendinblue.com/v2.0", $users['User']['email_apikey']);
                    
                                                        $list_id = (int)$users['User']['email_listid'];
                                                        $fullname = explode(' ', $contact['name']);
                                                        $firstname = $fullname[0];
                    
                                                        $data = array("email" => $contact['email'],
                                                            "attributes" => array("FIRSTNAME" => $firstname),
                                                            "listid" => array($list_id)
                                                        );
                    
                                                        $result = $mailin->create_update_user($data);
                    
                                                    }
                                                }
                                            }
                                            
                                            $this->ContactGroup = new ContactGroup();
                                            $contactgroup['subscribed_by_sms'] = 2;
                                            $contactgroup['user_id'] = $user_id;
                                            $contactgroup['group_id'] = $group_id;
                                            $contactgroup['group_subscribers'] = $keywordname['Group']['keyword'];
                                            $contactgroup['contact_id'] = $contactArr;
                                            $date = date('Y-m-d H:i:s');
                                            $contactgroup['created'] = $date;
                                            //****DOUBLE OPT-IN CHANGES****//
                                            if($double_optin==1){
                                                $contactgroup['un_subscribers'] = 2; 
                                            }
                                            $this->ContactGroup->save($contactgroup);
                                            
                                            //****DOUBLE OPT-IN CHANGES****//
                                            if($double_optin==0){
                                                if ($user_arr['User']['email_alert_options'] == 0) {
                                                    if ($user_arr['User']['email_alerts'] == 1) {
                                                        $username = $user_arr['User']['username'];
                                                        $email = $user_arr['User']['email'];
                                                        $subject = "New Subscriber via Web Widget to " . $keywordname['Group']['group_name'];
                                                        $sitename = str_replace(' ', '', SITENAME);
                                                        /*$this->Email->to = $email;
                                                        $this->Email->subject = $subject;
                                                        $this->Email->from = $sitename;
                                                        $this->Email->template = 'new_subscriber_template';
                                                        $this->Email->sendAs = 'html';
                                                        $this->Email->Controller->set('username', $username);
                                                        $this->Email->Controller->set('phoneno', $this->request->data['Contact']['phone_number']);
                                                        $this->Email->Controller->set('groupname', $keywordname['Group']['group_name']);
                                                        $this->Email->Controller->set('keyword', $keywordname['Group']['keyword']);
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
                                                        $Email->viewVars(array('phoneno' => $this->request->data['Contact']['phone_number']));
                                                        $Email->viewVars(array('groupname' => $keywordname['Group']['group_name']));
                                                        $Email->viewVars(array('keyword' => $keywordname['Group']['keyword']));
                                                        $Email->viewVars(array('datetime' => $date));
                                                        $Email->send();
                                                    }
                                                }
    
                                                //*********** Save to activity timeline
                                                $timeline['ActivityTimeline']['user_id'] = $user_id;
                                                $timeline['ActivityTimeline']['contact_id'] = $contactArr;
                                                $timeline['ActivityTimeline']['activity'] = 1;
                                                $timeline['ActivityTimeline']['title'] = 'Contact Subscribed via Web Widget';
                                                $timeline['ActivityTimeline']['description'] = $system_message . ' ' . $auto_message;
                                                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                                $this->ActivityTimeline->save($timeline);
                                                //*************
                                                app::import('Model', 'Group');
                                                $this->Group = new Group();
                                                $groupsdata = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                                $groupArr['id'] = $group_id;
                                                $groupArr['totalsubscriber'] = $groupsdata['Group']['totalsubscriber'] + 1;
                                                $this->Group->save($groupArr);
                                                if ($this->request->data['Contact']['autoresponse'] != '') {
                                                    app::import('Model', 'User');
                                                    $this->User = new User();
                                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                                    if ($users['User']['sms_balance'] > 0) {
                                                        $to = $this->request->data['Contact']['phone_number'];
                                                        $body = $this->request->data['Contact']['autoresponse'];
                                                        $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $body);
                                                        sleep(2);
                                                        $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                        //sleep(1);
                                                        /*if ($user_arr['User']['capture_email_name'] == 0) {
                                                            $capture_email_name = NAME_CAPTURE_MSG;
                                                            $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $capture_email_name);
                                                            $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                            if (!empty($someone_users)) {
                                                                $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                                $user_credit['User']['id'] = $user_id;
                                                                $this->User->save($user_credit);
                                                            }
                                                        }
                                                        //sleep(1);
                                                        if ($group['Group']['bithday_enable'] == 1) {
                                                            if ($user_arr['User']['birthday_wishes'] == 0) {
                                                                $birthday_wishes = BIRTHDAY_MSG;
                                                                $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $birthday_wishes);
                                                                $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                                if (!empty($someone_users)) {
                                                                    $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                                    $user_credit['User']['id'] = $user_id;
                                                                    $this->User->save($user_credit);
                                                                }
                                                            }
                                                        }*/
                                                        if ($group['Group']['notify_signup'] == 1) {
                                                            $mobile = $group['Group']['mobile_number_input'];
                                                            $groupname = $group['Group']['group_name'];
                                                            $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                            $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $mobile, $users['User']['keyword'], $message);
                                                            $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                            if (!empty($someone_users)) {
                                                                $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                                $user_credit['User']['id'] = $user_id;
                                                                $this->User->save($user_credit);
                                                            }
                                                        }
                                                    }
                                                    $saved = true;
                                                } else {
                                                    app::import('Model', 'User');
                                                    $this->User = new User();
                                                    $users = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                                    if ($users['User']['sms_balance'] > 0) {
                                                        $to = $this->request->data['Contact']['phone_number'];
                                                        $message = $system_message . ' ' . $auto_message;
                                                        $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $message);
                                                        sleep(2);
                                                        $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                        //sleep(1);
                                                        /*if ($user_arr['User']['capture_email_name'] == 0) {
                                                            $capture_email_name = NAME_CAPTURE_MSG;
                                                            $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $capture_email_name);
                                                            $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                            if (!empty($someone_users)) {
                                                                $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                                $user_credit['User']['id'] = $user_id;
                                                                $this->User->save($user_credit);
                                                            }
                                                        }
                                                        //sleep(1);
                                                        if ($group['Group']['bithday_enable'] == 1) {
                                                            if ($user_arr['User']['birthday_wishes'] == 0) {
                                                                $birthday_wishes = BIRTHDAY_MSG;
                                                                $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $birthday_wishes);
                                                                $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                                if (!empty($someone_users)) {
                                                                    $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                                    $user_credit['User']['id'] = $user_id;
                                                                    $this->User->save($user_credit);
                                                                }
                                                            }
                                                        }*/
                                                        if ($group['Group']['notify_signup'] == 1) {
                                                            $mobile = $group['Group']['mobile_number_input'];
                                                            $groupname = $group['Group']['group_name'];
                                                            $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                            $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $mobile, $users['User']['keyword'], $message);
    
                                                            $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
    
                                                            if (!empty($someone_users)) {
                                                                $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                                $user_credit['User']['id'] = $user_id;
                                                                $this->User->save($user_credit);
                                                            }
                                                        }
    
                                                    }
                                                    $saved = true;
                                                }
                                        }else{
                                                //****DOUBLE OPT-IN CHANGES****//
                                                $double_optin_msg = "Reply Y to confirm that you want to receive SMS messages from ". $users['User']['company_name'];
                                                $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $phone, $users['User']['keyword'], $double_optin_msg);
                                                //sleep(2);
                                                $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            
                                                //$length = strlen(utf8_decode(substr($double_optin_msg, 0, 1600)));
                                                $length = mb_strlen($double_optin_msg,"UTF-8");
			                                    $gsm = $this->is_gsm($double_optin_msg);
                                                if(!$gsm){//if (strlen($double_optin_msg) != strlen(utf8_decode($double_optin_msg))) {
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
                                            
                                            //****DOUBLE OPT-IN CHANGES****//     
                                    } elseif ($subscriber11['ContactGroup']['un_subscribers'] == 2) {
                                            $message = "Reply Y to confirm that you want to receive SMS messages from ". $users['User']['company_name'];
                                            //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                            $length = mb_strlen($message,"UTF-8");
			                                $gsm = $this->is_gsm($message);
                                            if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                $credits = ceil($length / 70);
                                            } else {
                                                $credits = ceil($length / 160);
                                            }
                        
                                            $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $phone, $users['User']['keyword'], $message);
                        
                                            $user_credit['User']['sms_balance'] = $someone['User']['sms_balance'] - $credits;
                                            $user_credit['User']['id'] = $user_id;
                                            $this->User->save($user_credit);
                                            $this->smsmail($user_id);
                                            exit;
                    
                                    } else {
                                            app::import('Model', 'User');
                                            $this->User = new User();
                                            $users = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                            if ($users['User']['sms_balance'] > 0) {
                                                $to = $this->request->data['Contact']['phone_number'];
                                                //$message = "You are already a subscriber with us";
                                                $message = $group['Group']['ifmember_message'];
                                                $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $message);
                                                $users_balance = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                                if (!empty($users_balance)) {
                                                    $credits = $users_balance['User']['sms_balance'];
                                                    $userbalance['User']['sms_balance'] = $credits - 1;
                                                    $userbalance['User']['id'] = $group['Group']['user_id'];
                                                    $this->User->save($userbalance);
                                                }
                                                $saved = true;
                                            }
                                        }
                                    }
                                    $saved = true;
                                } else {
                                    $err = "The specified phone number is not valid";
                                }
                            } else {
                                $err = "User not found";
                            }
                        } else {
                            $err = "Group not found";
                        }
                    } else {
                        //app::import('Model','Group');
                        //$this->Group = new Group();
                        //$group = $this->Group->find('first',array('conditions'=>array('Group.id'=>$this->request->data['Contact']['group_id'])));
                        $sms_type = $group['Group']['sms_type'];
                        $group_type = $group['Group']['group_type'];
                        $system_message = $group['Group']['system_message'];
                        $auto_message = $group['Group']['auto_message'];
                        $image_url = $group['Group']['image_url'];
                        app::import('Model', 'ContactGroup');
                        $this->ContactGroup = new ContactGroup();
                        $group_id = $group['Group']['id'];
                        $user_id = $group['Group']['user_id'];
                        app::import('Model', 'Contact');
                        $this->Contact = new Contact();
                        $newsubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number']), 'order' => array('Contact.id' => 'desc')));
                        if (empty($newsubscriber)) {
                            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            $API_TYPE = $users['User']['api_type'];
                            $SID = trim($users['User']['sid']);
                            $AUTHTOKEN = trim($users['User']['authtoken']);
                            $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                            $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
                            $this->Contact = new Contact();
                            $contact['user_id'] = $group['Group']['user_id'];
                            $contact['phone_number'] = $this->request->data['Contact']['phone_number'];
                            if (isset($this->request->data['Contact']['name'])) {
                                if (trim($this->request->data['Contact']['name']) !=''){
                                    $contact['name'] = trim($this->request->data['Contact']['name']);
                                }
                            }
                            if (isset($this->request->data['Contact']['email'])) {
                                if ($this->request->data['Contact']['email'] != '') {
                                    $contact['email'] = trim($this->request->data['Contact']['email']);
                                }
                            }
                            if (isset($this->request->data['Contact']['birthday'])) {
                                if ($this->request->data['Contact']['birthday'] != '') {
                                    $contact['birthday'] = $this->request->data['Contact']['birthday'];
                                }
                            }
                            if (isset($this->request->data['Contact']['city'])) {
                                if ($this->request->data['Contact']['city'] != '') {
                                    $contact['location'] = trim($this->request->data['Contact']['city']);
                                }
                            }
                            if (isset($this->request->data['Contact']['state'])) {
                                if ($this->request->data['Contact']['state'] != '') {
                                    $contact['state'] = trim($this->request->data['Contact']['state']);
                                }
                            }
                            if (isset($this->request->data['Contact']['zip'])) {
                                if ($this->request->data['Contact']['zip'] != '') {
                                    $contact['zip'] = trim($this->request->data['Contact']['zip']);
                                }
                            }
                            $contact['color'] = $this->choosecolor();
                            //****DOUBLE OPT-IN CHANGES****//
                            if($double_optin==1){
                                $contact['un_subscribers'] = 2; 
                            }
                                        
                            $this->Contact->save($contact);
                            $contactArr = $this->Contact->getLastInsertId();

                            if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $contact['email'] != '') {
                                if ($users['User']['email_service'] == 1) { //Mailchimp

                                    $list_id = $users['User']['email_listid'];
                                    $MailChimp = new MailChimp($users['User']['email_apikey']);

                                    if ($contact['name'] != '') {
                                        $fullname = explode(' ', $contact['name']);
                                        $firstname = $fullname[0];

                                        $result = $MailChimp->post("lists/$list_id/members", array(
                                            'email_address' => $contact['email'],
                                            'status' => 'subscribed',
                                            'merge_fields' => array('FNAME' => $firstname),
                                        ));

                                    } else {
                                        $result = $MailChimp->post("lists/$list_id/members", array(
                                            'email_address' => $contact['email'],
                                            'status' => 'subscribed',
                                        ));
                                    }
                                } else if ($users['User']['email_service'] == 2) { //Getresponse

                                    $list_id = $users['User']['email_listid'];
                                    $GetResponse = new GetResponse($users['User']['email_apikey']);

                                    if ($contact['name'] != '') {
                                        $fullname = explode(' ', $contact['name']);
                                        $firstname = $fullname[0];

                                        $result = $GetResponse->addContact(array(
                                            'email' => $contact['email'],
                                            'name' => $contact['name'],
                                            'campaign' => array('campaignId' => $list_id)));
                                    } else {

                                        $result = $GetResponse->addContact(array(
                                            'email' => $contact['email'],
                                            'campaign' => array('campaignId' => $list_id)));

                                    }

                                } else if ($users['User']['email_service'] == 3) { //Active Campaign

                                    $ac = new ActiveCampaign($users['User']['email_apiurl'], $users['User']['email_apikey']);
                                    $list_id = (int)$users['User']['email_listid'];
                                    $fullname = explode(' ', $contact['name']);
                                    $firstname = $fullname[0];

                                    $newcontact = array(
                                        'email' => $contact['email'],
                                        'first_name' => $firstname,
                                        'phone' => $contact['phone_number'],
                                        'p[{$list_id}]' => $list_id,
                                        'status[{$list_id}]' => 1, // "Active" status
                                    );

                                    $contact_sync = $ac->api("contact/sync", $newcontact);


                                } else if ($users['User']['email_service'] == 4) { //AWeber

                                    $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);
                                    $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);

                                    $account_id = $account->id;
                                    $list_id = $users['User']['email_listid'];
                                    $fullname = explode(' ', $contact['name']);
                                    $firstname = $fullname[0];

                                    $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                                    $list = $account->loadFromUrl($listURL);

                                    $params = array(
                                        'email' => $contact['email'],
                                        'name' => $firstname,
                                    );

                                    $subscribers = $list->subscribers;
                                    $new_subscriber = $subscribers->create($params);

                                } else if ($users['User']['email_service'] == 5) { //Sendinblue

                                    $mailin = new Mailin("https://api.sendinblue.com/v2.0", $users['User']['email_apikey']);

                                    $list_id = (int)$users['User']['email_listid'];
                                    $fullname = explode(' ', $contact['name']);
                                    $firstname = $fullname[0];

                                    $data = array("email" => $contact['email'],
                                        "attributes" => array("FIRSTNAME" => $firstname),
                                        "listid" => array($list_id)
                                    );

                                    $result = $mailin->create_update_user($data);

                                }


                            }

                        } else {
                            $contactArr = $newsubscriber['Contact']['id'];
                        }
                        if ($contactArr != '') {
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                            app::import('Model', 'ContactGroup');
                            $this->ContactGroup = new ContactGroup();
                            $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id, 'ContactGroup.user_id' => $user_id)));
                            $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                            $timezone = $user_arr['User']['timezone'];
                            date_default_timezone_set($timezone);
                            app::import('Model', 'ContactGroup');
                            if (empty($subscriber11)) {
                                
                                if (!empty($newsubscriber)) {
                                    $contact['id'] = $contactArr;
                                    if (isset($this->request->data['Contact']['name'])) {
                                        if (trim($this->request->data['Contact']['name']) !=''){
                                            $contact['name'] = trim($this->request->data['Contact']['name']);
                                        }
                                    }
                                    if (isset($this->request->data['Contact']['email'])) {
                                        if ($this->request->data['Contact']['email'] != '') {
                                            $contact['email'] = trim($this->request->data['Contact']['email']);
                                        }
                                    }
                                    if (isset($this->request->data['Contact']['birthday'])) {
                                        if ($this->request->data['Contact']['birthday'] != '') {
                                            $contact['birthday'] = $this->request->data['Contact']['birthday'];
                                        }
                                    }
                                    if (isset($this->request->data['Contact']['city'])) {
                                        if ($this->request->data['Contact']['city'] != '') {
                                            $contact['location'] = trim($this->request->data['Contact']['city']);
                                        }
                                    }
                                    if (isset($this->request->data['Contact']['state'])) {
                                        if ($this->request->data['Contact']['state'] != '') {
                                            $contact['state'] = trim($this->request->data['Contact']['state']);
                                        }
                                    }
                                    if (isset($this->request->data['Contact']['zip'])) {
                                        if ($this->request->data['Contact']['zip'] != '') {
                                            $contact['zip'] = trim($this->request->data['Contact']['zip']);
                                        }
                                    }
                                    $this->Contact->save($contact);
                                
                                    if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $contact['email'] != '') {
                                        if ($users['User']['email_service'] == 1) { //Mailchimp

                                            $list_id = $users['User']['email_listid'];
                                            $MailChimp = new MailChimp($users['User']['email_apikey']);

                                            if ($contact['name'] != '') {
                                                $fullname = explode(' ', $contact['name']);
                                                $firstname = $fullname[0];

                                                $result = $MailChimp->post("lists/$list_id/members", array(
                                                    'email_address' => $contact['email'],
                                                    'status' => 'subscribed',
                                                    'merge_fields' => array('FNAME' => $firstname),
                                                ));

                                            } else {
                                                $result = $MailChimp->post("lists/$list_id/members", array(
                                                    'email_address' => $contact['email'],
                                                    'status' => 'subscribed',
                                                ));
                                            }
                                        } else if ($users['User']['email_service'] == 2) { //Getresponse
        
                                            $list_id = $users['User']['email_listid'];
                                            $GetResponse = new GetResponse($users['User']['email_apikey']);
        
                                            if ($contact['name'] != '') {
                                                $fullname = explode(' ', $contact['name']);
                                                $firstname = $fullname[0];
        
                                                $result = $GetResponse->addContact(array(
                                                    'email' => $contact['email'],
                                                    'name' => $contact['name'],
                                                    'campaign' => array('campaignId' => $list_id)));
                                            } else {
        
                                                $result = $GetResponse->addContact(array(
                                                    'email' => $contact['email'],
                                                    'campaign' => array('campaignId' => $list_id)));
        
                                            }
        
                                        } else if ($users['User']['email_service'] == 3) { //Active Campaign
        
                                            $ac = new ActiveCampaign($users['User']['email_apiurl'], $users['User']['email_apikey']);
                                            $list_id = (int)$users['User']['email_listid'];
                                            $fullname = explode(' ', $contact['name']);
                                            $firstname = $fullname[0];
        
                                            $newcontact = array(
                                                'email' => $contact['email'],
                                                'first_name' => $firstname,
                                                'phone' => $contact['phone_number'],
                                                'p[{$list_id}]' => $list_id,
                                                'status[{$list_id}]' => 1, // "Active" status
                                            );
        
                                            $contact_sync = $ac->api("contact/sync", $newcontact);
        
        
                                        } else if ($users['User']['email_service'] == 4) { //AWeber
        
                                            $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);
                                            $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);
        
                                            $account_id = $account->id;
                                            $list_id = $users['User']['email_listid'];
                                            $fullname = explode(' ', $contact['name']);
                                            $firstname = $fullname[0];
        
                                            $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                                            $list = $account->loadFromUrl($listURL);
        
                                            $params = array(
                                                'email' => $contact['email'],
                                                'name' => $firstname,
                                            );
        
                                            $subscribers = $list->subscribers;
                                            $new_subscriber = $subscribers->create($params);
        
                                        } else if ($users['User']['email_service'] == 5) { //Sendinblue
        
                                            $mailin = new Mailin("https://api.sendinblue.com/v2.0", $users['User']['email_apikey']);
        
                                            $list_id = (int)$users['User']['email_listid'];
                                            $fullname = explode(' ', $contact['name']);
                                            $firstname = $fullname[0];
        
                                            $data = array("email" => $contact['email'],
                                                "attributes" => array("FIRSTNAME" => $firstname),
                                                "listid" => array($list_id)
                                            );
        
                                            $result = $mailin->create_update_user($data);
        
                                        }
                                    }
                                }
                                            
                                $this->ContactGroup = new ContactGroup();
                                $contactgroup['subscribed_by_sms'] = 2;
                                $contactgroup['user_id'] = $user_id;
                                $contactgroup['group_id'] = $group_id;
                                $contactgroup['group_subscribers'] = $keywordname['Group']['keyword'];
                                $contactgroup['contact_id'] = $contactArr;
                                $date = date('Y-m-d H:i:s');
                                $contactgroup['created'] = $date;
                                //****DOUBLE OPT-IN CHANGES****//
                                if($double_optin==1){
                                    $contactgroup['un_subscribers'] = 2; 
                                }
                                $this->ContactGroup->save($contactgroup);

                                //****DOUBLE OPT-IN CHANGES****//
                                if($double_optin==0){
                                    if ($user_arr['User']['email_alert_options'] == 0) {
                                        if ($user_arr['User']['email_alerts'] == 1) {
                                            $username = $user_arr['User']['username'];
                                            $email = $user_arr['User']['email'];
                                            $subject = "New Subscriber via Web Widget to " . $keywordname['Group']['group_name'];
                                            $sitename = str_replace(' ', '', SITENAME);
                                            /*$this->Email->to = $email;
                                            $this->Email->subject = $subject;
                                            $this->Email->from = $sitename;
                                            $this->Email->template = 'new_subscriber_template';
                                            $this->Email->sendAs = 'html';
                                            $this->Email->Controller->set('username', $username);
                                            $this->Email->Controller->set('phoneno', $this->request->data['Contact']['phone_number']);
                                            $this->Email->Controller->set('groupname', $keywordname['Group']['group_name']);
                                            $this->Email->Controller->set('keyword', $keywordname['Group']['keyword']);
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
                                            $Email->viewVars(array('phoneno' => $this->request->data['Contact']['phone_number']));
                                            $Email->viewVars(array('groupname' => $keywordname['Group']['group_name']));
                                            $Email->viewVars(array('keyword' => $keywordname['Group']['keyword']));
                                            $Email->viewVars(array('datetime' => $date));
                                            $Email->send();
                                        }
                                    }
                                    //*********** Save to activity timeline
                                    $timeline['ActivityTimeline']['user_id'] = $user_id;
                                    $timeline['ActivityTimeline']['contact_id'] = $contactArr;
                                    $timeline['ActivityTimeline']['activity'] = 1;
                                    $timeline['ActivityTimeline']['title'] = 'Contact Subscribed via Web Widget';
                                    $timeline['ActivityTimeline']['description'] = $system_message . ' ' . $auto_message;
                                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                    $this->ActivityTimeline->save($timeline);
                                    //*************	
                                    app::import('Model', 'Group');
                                    $this->Group = new Group();
                                    $groupsdata = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                    $groupArr['id'] = $group_id;
                                    $groupArr['totalsubscriber'] = $groupsdata['Group']['totalsubscriber'] + 1;
                                    $this->Group->save($groupArr);
                                    if ($this->request->data['Contact']['autoresponse'] != '') {
                                        app::import('Model', 'User');
                                        $this->User = new User();
                                        $users = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                        $API_TYPE = $users['User']['api_type'];
                                        $SID = trim($users['User']['sid']);
                                        $AUTHTOKEN = trim($users['User']['authtoken']);
                                        $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                                        $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
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
                                        if ($users['User']['sms_balance'] > 0) {
                                            $to = $this->request->data['Contact']['phone_number'];
                                            $from = $assigned_number;
                                            $body1 = $this->request->data['Contact']['autoresponse'];
                                            if ($group_type == 2) {
                                                $address = $group['Group']['property_address'];
                                                $price = $group['Group']['property_price'];
                                                $bed = $group['Group']['property_bed'];
                                                $bath = $group['Group']['property_bath'];
                                                $description = $group['Group']['property_description'];
                                                $url = $group['Group']['property_url'];
    
                                                $message = $address . "\n" . $price . "\nBed: " . $bed . "\nBath: " . $bath . "\n" . $description . "\n" . $url . "\n";
                                                $body = $message . $body1;
                                            } elseif ($group_type == 3) {
                                                $year = $group['Group']['vehicle_year'];
                                                $make = $group['Group']['vehicle_make'];
                                                $model = $group['Group']['vehicle_model'];
                                                $mileage = $group['Group']['vehicle_mileage'];
                                                $price = $group['Group']['vehicle_price'];
                                                $description = $group['Group']['vehicle_description'];
                                                $url = $group['Group']['vehicle_url'];
    
                                                $message = $year . ' ' . $make . ' ' . $model . "\n" . $mileage . "\n" . $price . "\n" . $description . "\n" . $url . "\n";
                                                //$message=$message. $system_message.' '.$auto_message;
                                                $body = $message . $body1;
                                            } else {
                                                $body = $this->request->data['Contact']['autoresponse'];
                                            }
                                            $current_datetime = date("n/d/Y");
                                            $body = str_replace('%%CURRENTDATE%%', $current_datetime, $body);
                                            if ($API_TYPE == 0) {
                                                if ($SID == ''){
                                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                }else{
                                                    $this->Twilio->AccountSid = $SID;
                                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                                }
                                                $response = $this->Twilio->sendsms($to, $from, $body);
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                /*sleep(1);
                                                if ($user_arr['User']['capture_email_name'] == 0) {
                                                    $capture_email_name = NAME_CAPTURE_MSG;
                                                    $this->Twilio->sendsms($to, $from, $capture_email_name);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                                sleep(1);
                                                if ($group['Group']['bithday_enable'] == 1) {
                                                    if ($user_arr['User']['birthday_wishes'] == 0) {
                                                        $birthday_wishes = BIRTHDAY_MSG;
                                                        $this->Twilio->sendsms($to, $from, $birthday_wishes);
                                                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($someone_users)) {
                                                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                            $user_credit['User']['id'] = $user_id;
                                                            $this->User->save($user_credit);
                                                        }
                                                    }
                                                }*/
    
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    $response = $this->Twilio->sendsms($mobile, $from, $message);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 4) {
                                                if ($SID == ''){
                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$body,1);
                                                }else{
                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$body,1);
                                                }
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
													    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$mobile,$from,$message,1);
													}else{
													    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$mobile,$from,$message,1);   
													}
													    
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 5) {
                                                if ($SID == ''){
                                                    $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$body);
                                                }else{
                                                    $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$body);
                                                }
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
													    $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$mobile,$from,$message);
													}else{
													    $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$mobile,$from,$message);   
													}
													    
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 6) {
                                                if ($SID == ''){
                                                    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$body);
                                                }else{
                                                    $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$body);
                                                }
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
													    $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$mobile,$from,$message);
													}else{
													    $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$mobile,$from,$message);   
													}
													    
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 7) {
                                                if ($SID == ''){
                                                    $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$body);
                                                }else{
                                                    $response = $this->Telnyx->sendsms($SID,$to,$from,$body);
                                                }
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
													    $response = $this->Telnyx->sendsms(TELNYX_KEY,$mobile,$from,$message);
													}else{
													    $response = $this->Telnyx->sendsms($SID,$mobile,$from,$message);   
													}
													    
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 3) {
                                                if ($SID == ''){
                                                    $this->Plivo->AuthId = PLIVO_KEY;
                                                    $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                }else{
                                                    $this->Plivo->AuthId = $SID;
                                                    $this->Plivo->AuthToken = $AUTHTOKEN;
                                                }
                                                $response = $this->Plivo->sendsms($to, $from, $body);
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                /*sleep(1);
                                                if ($user_arr['User']['capture_email_name'] == 0) {
                                                    $capture_email_name = NAME_CAPTURE_MSG;
                                                    $this->Plivo->sendsms($to, $from, $capture_email_name);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                                sleep(1);
                                                if ($group['Group']['bithday_enable'] == 1) {
                                                    if ($user_arr['User']['birthday_wishes'] == 0) {
                                                        $birthday_wishes = BIRTHDAY_MSG;
                                                        $this->Plivo->sendsms($to, $from, $birthday_wishes);
                                                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($someone_users)) {
                                                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                            $user_credit['User']['id'] = $user_id;
                                                            $this->User->save($user_credit);
                                                        }
                                                    }
                                                }*/
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    $this->Plivo->sendsms($mobile, $from, $message);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else {
                                                if ($SID == ''){
                                                    $this->Nexmomessage->Key = NEXMO_KEY;
                                                    $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                }else{
                                                    $this->Nexmomessage->Key = $SID;
                                                    $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                }
                                                $response = $this->Nexmomessage->sendsms($to, $from, $body);
                                                sleep(2);
                                                $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                /*sleep(1);
                                                if ($user_arr['User']['capture_email_name'] == 0) {
                                                    $capture_email_name = NAME_CAPTURE_MSG;
                                                    $this->Nexmomessage->sendsms($to, $from, $capture_email_name);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                                sleep(1);
                                                if ($group['Group']['bithday_enable'] == 1) {
                                                    if ($user_arr['User']['birthday_wishes'] == 0) {
                                                        $birthday_wishes = BIRTHDAY_MSG;
                                                        $this->Nexmomessage->sendsms($to, $from, $birthday_wishes);
                                                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($someone_users)) {
                                                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                            $user_credit['User']['id'] = $user_id;
                                                            $this->User->save($user_credit);
                                                        }
                                                    }
                                                }*/
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    $this->Nexmomessage->sendsms($mobile, $from, $message);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            }
                                            $credits = $users['User']['sms_balance'];
                                            //$length = strlen(utf8_decode(substr($body, 0, 1600)));
                                            $length = mb_strlen($body,"UTF-8");
			                                $gsm = $this->is_gsm($body);
                                            if(!$gsm){//if (strlen($body) != strlen(utf8_decode($body))) {
                                                $dedcredits = ceil($length / 70);
                                            } else {
                                                $dedcredits = ceil($length / 160);
                                            }
                                            $this->request->data['User']['sms_balance'] = $credits - $dedcredits;
                                            $this->request->data['User']['id'] = $group['Group']['user_id'];
                                            $this->User->save($this->request->data);
                                            $saved = true;
                                            $this->smsmail($users['User']['id']);
                                        }
                                    } else {
                                        app::import('Model', 'User');
                                        $this->User = new User();
                                        $users = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                        $API_TYPE = $users['User']['api_type'];
                                        $SID = trim($users['User']['sid']);
                                        $AUTHTOKEN = trim($users['User']['authtoken']);
                                        $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                                        $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
                                        if ($users['User']['sms_balance'] > 0) {
                                            $to = $this->request->data['Contact']['phone_number'];
                                            $fromarr = array();
                                            if ($group['Group']['sms_type'] == 2) {
                                                $usernumber = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'], 'User.mms' => 1)));
                                                if (empty($usernumber)) {
                                                    app::import('Model', 'UserNumber');
                                                    $this->UserNumber = new UserNumber();
                                                    $mmsnumber = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $group['Group']['user_id'], 'UserNumber.mms' => 1)));
                                                    if (!empty($mmsnumber)) {
                                                        foreach ($mmsnumber as $mmsnumbers) {
                                                            $fromarr[] = $mmsnumbers['UserNumber']['number'];
                                                        }
                                                    } else {
                                                        $fromarr[] = $users['User']['assigned_number'];
                                                    }
                                                } else {
                                                    $fromarr[] = $usernumber['User']['assigned_number'];
                                                }
                                            } else {
                                                if (!empty($users)) {
                                                    if ($users['User']['sms'] == 1) {
                                                        $fromarr[] = $users['User']['assigned_number'];
                                                    } else {
                                                        app::import('Model', 'UserNumber');
                                                        $this->UserNumber = new UserNumber();
                                                        $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                                                        if (!empty($user_numbers)) {
                                                            $fromarr[] = $user_numbers['UserNumber']['number'];
                                                        } else {
                                                            $fromarr[] = $users['User']['assigned_number'];
                                                        }
                                                    }
                                                }
                                            }
                                            $random_keys = array_rand($fromarr, 1);
                                            $from = $fromarr[$random_keys];
                                            //$message= $system_message.' '.$auto_message;
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
                                            if ($API_TYPE == 0) {
                                                if ($sms_type == 1) {
                                                    if ($SID == ''){
                                                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                    }else{
                                                        $this->Twilio->AccountSid = $SID;
                                                        $this->Twilio->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $this->Twilio->sendsms($to, $from, $message);
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                } else if ($sms_type == 2) {
                                                    if ($SID == ''){
                                                        $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                                                        $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                                                    }else{
                                                        $this->Mms->AccountSid = $SID;
                                                        $this->Mms->AuthToken = $AUTHTOKEN;
                                                    }
                                                    $message_arr = explode(',', $image_url);
                                                    $this->Mms->sendmms($to, $from, $message_arr, $message);
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                }
                                                $credits = $users['User']['sms_balance'];
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $dedcredits = ceil($length / 70);
                                                } else {
                                                    $dedcredits = ceil($length / 160);
                                                }
                                                $this->request->data['User']['sms_balance'] = $credits - $dedcredits;
                                                $this->request->data['User']['id'] = $user_id;
                                                $this->User->save($this->request->data);
                                                $saved = true;
                                                /*sleep(1);
                                                if ($users['User']['capture_email_name'] == 0) {
                                                    $capture_email_name = NAME_CAPTURE_MSG;
                                                    $this->Twilio->sendsms($to, $from, $capture_email_name);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                                sleep(1);
                                                if ($group['Group']['bithday_enable'] == 1) {
                                                    if ($users['User']['birthday_wishes'] == 0) {
                                                        $birthday_wishes = BIRTHDAY_MSG;
                                                        $this->Twilio->sendsms($to, $from, $birthday_wishes);
                                                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                        if (!empty($someone_users)) {
                                                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                            $user_credit['User']['id'] = $user_id;
                                                            $this->User->save($user_credit);
                                                        }
                                                    }
                                                }*/
    
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    $response = $this->Twilio->sendsms($mobile, $from, $message);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 4) {
												if ($sms_type == 1) {
												    if ($SID == ''){
                                                        $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message,1);
                                                    }else{
                                                        $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message,1);
                                                    }
													sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                } else if ($sms_type == 2) {
                                                    $message_arr = explode(',', $image_url);
                                                    if ($SID == ''){
													   $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message,$message_arr,1);
                                                    }else{
                                                       $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message,$message_arr,1); 
                                                    }
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                }
                                                $credits = $users['User']['sms_balance'];
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $dedcredits = ceil($length / 70);
                                                } else {
                                                    $dedcredits = ceil($length / 160);
                                                }
                                                $this->request->data['User']['sms_balance'] = $credits - $dedcredits;
                                                $this->request->data['User']['id'] = $user_id;
                                                $this->User->save($this->request->data);
                                                $saved = true;
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
													   $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$mobile,$from,$message,1);
                                                    }else{
                                                       $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$mobile,$from,$message,1); 
                                                    }
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                            } else if ($API_TYPE == 5) {
												if ($sms_type == 1) {
											        if ($SID == ''){
                                                        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message);
                                                    }else{
                                                        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$message);
                                                    }
													sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                } else if ($sms_type == 2) {
                                                    $message_arr = explode(',', $image_url);
                                                    if ($SID == ''){
                                                        $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message,$message_arr);
                                                    }else{
                                                        $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$message,$message_arr);
                                                    }
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                }
                                                $credits = $users['User']['sms_balance'];
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $dedcredits = ceil($length / 70);
                                                } else {
                                                    $dedcredits = ceil($length / 160);
                                                }
                                                $this->request->data['User']['sms_balance'] = $credits - $dedcredits;
                                                $this->request->data['User']['id'] = $user_id;
                                                $this->User->save($this->request->data);
                                                $saved = true;
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
                                                        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$mobile,$from,$message);
                                                    }else{
                                                        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$mobile,$from,$message);
                                                    }
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                } 
                                            } else if ($API_TYPE == 6) {
												if ($sms_type == 1) {
											        if ($SID == ''){
                                                        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message);
                                                    }else{
                                                        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$message);
                                                    }
													sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                } else if ($sms_type == 2) {
                                                    $message_arr = explode(',', $image_url);
                                                    if ($SID == ''){
                                                        $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message,$message_arr);
                                                    }else{
                                                        $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$from,$message,$message_arr);
                                                    }
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                }
                                                $credits = $users['User']['sms_balance'];
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $dedcredits = ceil($length / 70);
                                                } else {
                                                    $dedcredits = ceil($length / 160);
                                                }
                                                $this->request->data['User']['sms_balance'] = $credits - $dedcredits;
                                                $this->request->data['User']['id'] = $user_id;
                                                $this->User->save($this->request->data);
                                                $saved = true;
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
                                                        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$mobile,$from,$message);
                                                    }else{
                                                        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$mobile,$from,$message);
                                                    }
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                } 
                                            } else if ($API_TYPE == 7) {
												if ($sms_type == 1) {
											        if ($SID == ''){
                                                        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
                                                    }else{
                                                        $response = $this->Telnyx->sendsms($SID,$to,$from,$message);
                                                    }
													sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                } else if ($sms_type == 2) {
                                                    $message_arr = explode(',', $image_url);
                                                    if ($SID == ''){
                                                        $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$from,$message,$message_arr);
                                                    }else{
                                                        $response = $this->Telnyx->sendmms($SID,$to,$from,$message,$message_arr);
                                                    }
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                }
                                                $credits = $users['User']['sms_balance'];
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $dedcredits = ceil($length / 70);
                                                } else {
                                                    $dedcredits = ceil($length / 160);
                                                }
                                                $this->request->data['User']['sms_balance'] = $credits - $dedcredits;
                                                $this->request->data['User']['id'] = $user_id;
                                                $this->User->save($this->request->data);
                                                $saved = true;
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    if ($SID == ''){
                                                        $response = $this->Telnyx->sendsms(TELNYX_KEY,$mobile,$from,$message);
                                                    }else{
                                                        $response = $this->Telnyx->sendsms($SID,$mobile,$from,$message);
                                                    }
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                } 
                                            } else if ($API_TYPE == 3) {
                                                if ($SID == ''){
                                                    $this->Plivo->AuthId = PLIVO_KEY;
                                                    $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                }else{
                                                    $this->Plivo->AuthId = $SID;
                                                    $this->Plivo->AuthToken = $AUTHTOKEN;
                                                }
                                                if ($sms_type == 1) {
                                                    $response = $this->Plivo->sendsms($to, $from, $message);
                                                } else if ($sms_type == 2) {
                                                    $message_arr = explode(',', $image_url);
                                                    $response = $this->Plivo->sendmms($to, $from, $message, $message_arr);
                                                }
                                                $errortext = '';
                                                $message_id = '';
                                                if (isset($response['response']['error'])) {
                                                    $errortext = $response['response']['error'];
                                                }
                                                if (isset($response['response']['message_uuid'][0])) {
                                                    $message_id = $response['response']['message_uuid'][0];
                                                }
                                                if ($message_id != '') {
                                                    sleep(2);
                                                    $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                }
                                                $users_balance = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                                sleep(1);
                                                //$creditdeduct = 1;
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $creditdeduct = ceil($length / 70);
                                                } else {
                                                    $creditdeduct = ceil($length / 160);
                                                }
                                                /*if ($users_balance['User']['capture_email_name'] == 0) {
                                                    $capture_email_name = NAME_CAPTURE_MSG;
                                                    $this->Plivo->sendsms($to, $from, $capture_email_name);
                                                    $creditdeduct = $creditdeduct + 1;
                                                }
                                                sleep(1);
                                                if ($group['Group']['bithday_enable'] == 1) {
                                                    if ($users_balance['User']['birthday_wishes'] == 0) {
                                                        $birthday_wishes = BIRTHDAY_MSG;
                                                        $this->Plivo->sendsms($to, $from, $birthday_wishes);
                                                        $creditdeduct = $creditdeduct + 1;
                                                    }
                                                }*/
    
                                                if (!empty($users_balance)) {
                                                    $credits = $users_balance['User']['sms_balance'];
                                                    $userbalance['User']['sms_balance'] = $credits - $creditdeduct;
                                                    $userbalance['User']['id'] = $user_id;
                                                    $this->User->save($userbalance);
                                                }
                                                $saved = true;
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    $this->Plivo->sendsms($mobile, $from, $message);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                                $this->smsmail($users['User']['id']);
                                            } else {
                                                if ($SID == ''){
                                                    $this->Nexmomessage->Key = NEXMO_KEY;
                                                    $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                }else{
                                                    $this->Nexmomessage->Key = $SID;
                                                    $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                }
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
                                                if (isset($status)) {
                                                    if ($status == 0) {
                                                        sleep(2);
                                                        $this->Immediatelyresponder($user_id, $group_id, $to, $from);
                                                    }
                                                }
                                                $users_balance = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                                sleep(1);
                                                //$creditdeduct = 1;
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $creditdeduct = ceil($length / 70);
                                                } else {
                                                    $creditdeduct = ceil($length / 160);
                                                }
                                                /*if ($users_balance['User']['capture_email_name'] == 0) {
                                                    $capture_email_name = NAME_CAPTURE_MSG;
                                                    $this->Nexmomessage->sendsms($to, $from, $capture_email_name);
                                                    $creditdeduct = $creditdeduct + 1;
                                                }
                                                sleep(1);
                                                if ($group['Group']['bithday_enable'] == 1) {
                                                    if ($users_balance['User']['birthday_wishes'] == 0) {
                                                        $birthday_wishes = BIRTHDAY_MSG;
                                                        $this->Nexmomessage->sendsms($to, $from, $birthday_wishes);
                                                        $creditdeduct = $creditdeduct + 1;
                                                    }
                                                }*/
    
                                                if (!empty($users_balance)) {
                                                    $credits = $users_balance['User']['sms_balance'];
                                                    $userbalance['User']['sms_balance'] = $credits - $creditdeduct;
                                                    $userbalance['User']['id'] = $user_id;
                                                    $this->User->save($userbalance);
                                                }
                                                $saved = true;
                                                if ($group['Group']['notify_signup'] == 1) {
                                                    $mobile = $group['Group']['mobile_number_input'];
                                                    $groupname = $group['Group']['group_name'];
                                                    $message = "New Subscriber Alert: " . $to . " has joined group " . $groupname;
                                                    $this->Nexmomessage->sendsms($mobile, $from, $message);
                                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                                    if (!empty($someone_users)) {
                                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                                                        $user_credit['User']['id'] = $user_id;
                                                        $this->User->save($user_credit);
                                                    }
                                                }
                                                $this->smsmail($users['User']['id']);
                                            }
                                        }
                                    }
                                } else{
                                    //****DOUBLE OPT-IN CHANGES****//
                                    $double_optin_msg = "Reply Y to confirm that you want to receive SMS messages from ". $users['User']['company_name'];
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
                                    if ($API_TYPE == 3) {
                                        if ($SID == ''){
                                            $this->Plivo->AuthId = PLIVO_KEY;
                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                        }else{
                                            $this->Plivo->AuthId = $SID;
                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                        }
                                        $this->Plivo->sendsms($phone, $assigned_number, $double_optin_msg);
                                    }elseif($API_TYPE == 1){
                                        if ($SID == ''){
                                            $this->Nexmomessage->Key = NEXMO_KEY;
                                            $this->Nexmomessage->Secret = NEXMO_SECRET;
                                        }else{
                                            $this->Nexmomessage->Key = $SID;
                                            $this->Nexmomessage->Secret = $AUTHTOKEN;
                                        }
                                        $response = $this->Nexmomessage->sendsms($phone, $assigned_number, $double_optin_msg);
                                    }elseif($API_TYPE == 0){
                                        if ($SID == ''){
                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                        }else{
                                            $this->Twilio->AccountSid = $SID;
                                            $this->Twilio->AuthToken = $AUTHTOKEN;
                                        }
                                        $this->Twilio->sendsms($phone, $assigned_number, $double_optin_msg);
                                    }elseif($API_TYPE == 4){
                                        if ($SID == ''){
                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$phone,$assigned_number,$double_optin_msg,1);
                                        }else{
                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$phone,$assigned_number,$double_optin_msg,1);
                                        }

                                    }elseif($API_TYPE == 5){
                                        if ($SID == ''){
                                            $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$phone,$assigned_number,$double_optin_msg);
                                        }else{
                                            $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$phone,$assigned_number,$double_optin_msg);
                                        }
                                    }elseif($API_TYPE == 6){
                                        if ($SID == ''){
                                            $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone,$assigned_number,$double_optin_msg);
                                        }else{
                                            $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$phone,$assigned_number,$double_optin_msg);
                                        }
                                    }elseif($API_TYPE == 7){
                                        if ($SID == ''){
                                            $response = $this->Telnyx->sendsms(TELNYX_KEY,$phone,$assigned_number,$double_optin_msg);
                                        }else{
                                            $response = $this->Telnyx->sendsms($SID,$phone,$assigned_number,$double_optin_msg);
                                        }

                                    }
                                    
                                    //sleep(2);
                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    //$length = strlen(utf8_decode(substr($double_optin_msg, 0, 1600)));
                                    $length = mb_strlen($double_optin_msg,"UTF-8");
			                        $gsm = $this->is_gsm($double_optin_msg);
                                    if(!$gsm){//if (strlen($double_optin_msg) != strlen(utf8_decode($double_optin_msg))) {
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
                            } elseif ($subscriber11['ContactGroup']['un_subscribers'] == 2) {
                                    //****DOUBLE OPT-IN CHANGES****//
                                    $double_optin_msg = "Reply Y to confirm that you want to receive SMS messages from ". $users['User']['company_name'];
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
                                    if ($API_TYPE == 3) {
                                        if ($SID == ''){
                                            $this->Plivo->AuthId = PLIVO_KEY;
                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                        }else{
                                            $this->Plivo->AuthId = $SID;
                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                        }
                                        $this->Plivo->sendsms($phone, $assigned_number, $double_optin_msg);
                                    }elseif($API_TYPE == 1){
                                        if ($SID == ''){
                                            $this->Nexmomessage->Key = NEXMO_KEY;
                                            $this->Nexmomessage->Secret = NEXMO_SECRET;
                                        }else{
                                            $this->Nexmomessage->Key = $SID;
                                            $this->Nexmomessage->Secret = $AUTHTOKEN;
                                        }
                                        $response = $this->Nexmomessage->sendsms($phone, $assigned_number, $double_optin_msg);
                                    }elseif($API_TYPE == 0){
                                        if ($SID == ''){
                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                        }else{
                                            $this->Twilio->AccountSid = $SID;
                                            $this->Twilio->AuthToken = $AUTHTOKEN;
                                        }
                                        $this->Twilio->sendsms($phone, $assigned_number, $double_optin_msg);
                                    }elseif($API_TYPE == 4){
                                        if ($SID == ''){
                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$phone,$assigned_number,$double_optin_msg,1);
                                        }else{
                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$phone,$assigned_number,$double_optin_msg,1);
                                        }
										
                                    }elseif($API_TYPE == 5){
                                        if ($SID == ''){
                                            $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$phone,$assigned_number,$double_optin_msg);
                                        }else{
                                            $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$phone,$assigned_number,$double_optin_msg);
                                        }
                                    
                                    }elseif($API_TYPE == 6){
                                        if ($SID == ''){
                                            $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone,$assigned_number,$double_optin_msg);
                                        }else{
                                            $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$phone,$assigned_number,$double_optin_msg);
                                        }

                                    }elseif($API_TYPE == 7){
                                        if ($SID == ''){
                                            $response = $this->Telnyx->sendsms(TELNYX_KEY,$phone,$assigned_number,$double_optin_msg);
                                        }else{
                                            $response = $this->Telnyx->sendsms($SID,$phone,$assigned_number,$double_optin_msg);
                                        }

                                    }
                                    
                                    //sleep(2);
                                    
                                    $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                                    //$length = strlen(utf8_decode(substr($double_optin_msg, 0, 1600)));
                                    $length = mb_strlen($double_optin_msg,"UTF-8");
			                        $gsm = $this->is_gsm($double_optin_msg);
                                    if(!$gsm){//if (strlen($double_optin_msg) != strlen(utf8_decode($double_optin_msg))) {
                                        $credits = ceil($length / 70);
                                    } else {
                                        $credits = ceil($length / 160);
                                    }
                                    if (!empty($someone_users)) {
                                        $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - $credits;
                                        $user_credit['User']['id'] = $user_id;
                                        $this->User->save($user_credit);
                                    }
                                    
                                
                            } else {
                                app::import('Model', 'User');
                                $this->User = new User();
                                $users = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                $API_TYPE = $users['User']['api_type'];
                                $SID = trim($users['User']['sid']);
                                $AUTHTOKEN = trim($users['User']['authtoken']);
                                $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                                $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
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
                                if ($users['User']['sms_balance'] > 0) {
                                    $to = $this->request->data['Contact']['phone_number'];
                                    $from = $assigned_number;
                                    //$message= "You are already a subscriber with us";
                                    /*if($group_type==2){
                                        $message='We have already sent you information on this property.';
                                    }elseif ($group_type==3){
                                        $message='We have already sent you information on this vehicle.';
                                    }else{
                                        $message='You are already subscribed to this list.';
                                    }*/
                                    $message = $group['Group']['ifmember_message'];
                                    //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                    $length = mb_strlen($message,"UTF-8");
			                        $gsm = $this->is_gsm($message);
                                    if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                        $credits = ceil($length / 70);
                                    } else {
                                        $credits = ceil($length / 160);
                                    }
                                    if ($API_TYPE == 0) {
                                        if ($SID == ''){
                                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                        }else{
                                            $this->Twilio->AccountSid = $SID;
                                            $this->Twilio->AuthToken = $AUTHTOKEN;
                                        }
                                        $response = $this->Twilio->sendsms($to, $from, $message);
									} else if ($API_TYPE == 4) {
									    if ($SID == ''){
                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message,1);
                                        }else{
                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message,1);
                                        }
									} else if($API_TYPE == 5){
                                        if ($SID == ''){
                                            $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message);
                                        }else{
                                            $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$message);
                                        }
									} else if($API_TYPE == 6){
                                        if ($SID == ''){
                                            $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message);
                                        }else{
                                            $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$message);
                                        }
									} else if($API_TYPE == 7){
                                        if ($SID == ''){
                                            $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
                                        }else{
                                            $response = $this->Telnyx->sendsms($SID,$to,$from,$message);
                                        }
                                    } else if ($API_TYPE == 2) {
                                        $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $message);
                                    } else if ($API_TYPE == 3) {
                                        if ($SID == ''){
                                            $this->Plivo->AuthId = PLIVO_KEY;
                                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                                        }else{
                                            $this->Plivo->AuthId = $SID;
                                            $this->Plivo->AuthToken = $AUTHTOKEN;
                                        }
                                        $response = $this->Plivo->sendsms($to, $from, $message);
                                    } else {
                                        if ($SID == ''){
                                            $this->Nexmomessage->Key = NEXMO_KEY;
                                            $this->Nexmomessage->Secret = NEXMO_SECRET;
                                        }else{
                                            $this->Nexmomessage->Key = $SID;
                                            $this->Nexmomessage->Secret = $AUTHTOKEN;
                                        }
                                        $response = $this->Nexmomessage->sendsms($to, $from, $message);
                                        $this->smsmail($users['User']['id']);
                                    }
                                    
                                    $users_balance = $this->User->find('first', array('conditions' => array('User.id' => $group['Group']['user_id'])));
                                    if (!empty($users_balance)) {
                                        //$credits = $users_balance['User']['sms_balance'];
                                        $userbalance['User']['sms_balance'] = $users_balance['User']['sms_balance'] - $credits;
                                        $userbalance['User']['id'] = $group['Group']['user_id'];
                                        $this->User->save($userbalance);
                                    }
                                    
                                    $saved = true;
                                }
                            }
                        }
                        $saved = true;
                    }
                } else {
                    $err = "'" . $this->request->data['Contact']['phone_number'] . "' is not a valid phone number ";
                }
            }
        }

        $this->set('err', $err);
        $this->set('saved', $saved);
        $this->render('/Webwidgets/subscribe');
    }

    function facebookwidget($group_id = NULL)
    {
        $this->autoRender = false;
        Configure:: write('debug', 0);
        $this->layout = false;
        $font_family = array('A' => 'Arial', 'H' => 'Helvetica', 'T' => 'Times New Roman', 'V' => 'Verdana');
        $font_weight = array('N' => 'Normal', 'B' => 'Bold', 'I' => 'Italic', 'BI' => 'Bold+Italic');
        $user_id = $this->Session->read('User.id');
        $this->set('user_id', $user_id);
        $this->set('group_id', $group_id);
        $this->set('font_family', $font_family);
        $this->set('font_weight', $font_weight);
        $this->set('data', $_REQUEST);
        $this->render('facebookwidget');
    }

    function subscribefb($user_id = NULL, $group_id = NULL)
    {
        echo '<pre>';
        echo 'user: ' . $user_id . ' group ' . $group_id;
        $group = $this->Group->find('first', array('conditions' => array('id' => $group_id)));
        $this->request->data['Contact']['user_id'] = $user_id;
        $this->request->data['Contact']['msg_user_id'] = $user_id;
        $this->request->data['Contact']['contact_first_name'] = $_REQUEST['firstName'];
        $this->request->data['Contact']['contact_last_name'] = $_REQUEST['lastName'];
        $this->request->data['Contact']['phone'] = $_REQUEST['phonenumber'];
        $this->request->data['Contact']['source_id'] = 2;
        if ($this->Contact->save($this->request->data)) {
            $this->request->data['GroupsContact']['contact_id'] = $this->Contact->getLastInsertId();
            $this->request->data['GroupsContact']['group_id'] = $group_id;
            if ($_REQUEST['autoresponder'] != '') {
                $this->loadModel('Config');
                $config = $this->Config->find('first');
                $to = $this->request->data['Contact']['phone'];
                $from = $config['Config']['sms_sender_phone'];
                $body = $_REQUEST['autoresponder'];
                //$response = $this->Twilio->sendsms($to,$from,$body);
            }
            if ($this->GroupsContact->save($this->request->data)) {
                $saved = true;
            }
        }
        die;
    }

    function upload()
    {
        $this->layout = null;
        $filename = $_FILES['data']['name']['image'];
        $type = $_FILES['data']['type']['image'];
        $tmp_name = $_FILES['data']['tmp_name']['image'];
        
        if (!empty($filename)) {
            if(trim($type)=='image/png' || trim($type)=='image/jpeg' || trim($type)=='image/gif'){
                $time = time();
                move_uploaded_file($tmp_name, "uploadimages/" . $time . $filename);
                $siteurl = SITE_URL;
                $imagepath = $siteurl . '/uploadimages/' . $time . $filename;
                $this->set('imagename', $imagepath);
            }
        }
        
    }

    function Immediatelyresponder($user_id = null, $group_id = null, $to = null, $from = null)
    {
        $this->autoRender = false;
        app::import('Model', 'Responder');
        $this->Responder = new Responder();
        $response = $this->Responder->find('first', array('conditions' => array('Responder.user_id' => $user_id, 'Responder.group_id' => $group_id, 'Responder.days' => 0)));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $API_TYPE = $users['User']['api_type'];
        $SID = trim($users['User']['sid']);
        $AUTHTOKEN = trim($users['User']['authtoken']);
        $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
        $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
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
                if ($users['User']['sms_balance'] > 0)
                $Responderid = $response['Responder']['id'];
                $group_id = $response['Responder']['group_id'];
                $sms_type = $response['Responder']['sms_type'];
                $image_url = $response['Responder']['image_url'];
                $message = str_replace('%%CURRENTDATE%%', $current_datetime, $response['Responder']['message']);
                $systemmsg = $response['Responder']['systemmsg'];
                $user_id = $response['Responder']['user_id'];
                $body = $message . " " . $systemmsg;
                if ($API_TYPE == 0) {
                    if ($sms_type == 1) {
                        if ($SID == ''){
                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Twilio->AccountSid = $SID;
                            $this->Twilio->AuthToken = $AUTHTOKEN;
                        }
                        $response = $this->Twilio->sendsms($to, $assigned_number, $body);
                        $smsid = $response->ResponseXml->Message->Sid;
                        $Status = $response->ResponseXml->RestException->Status;
                        $credits = 1;
                    } else if ($sms_type == 2) {
                        if ($SID == ''){
                            $this->Mms->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Mms->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Mms->AccountSid = $SID;
                            $this->Mms->AuthToken = $AUTHTOKEN;
                        }
                        $message_arr = explode(',', $image_url);
                        $response = $this->Mms->sendmms($to, $assigned_number, $message_arr, $body);
                        $smsid = $response->sid;
                        $credits = 2;
                        if ($smsid == '') {
                            $ErrorMessage = $response;
                            $Status = 400;
                        }
                    }
                    $usersave['User']['id'] = $user_id;
                    $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                    $this->User->save($usersave);
				} else if ($API_TYPE == 4) {
					if ($sms_type == 1) {
					    if ($SID == ''){
                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$assigned_number,$body,1);
                        }else{
                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$assigned_number,$body,1);
                        }
                        $credits = 1;
                    } else if ($sms_type == 2) {
                        $message_arr = explode(',', $image_url);
                        if ($SID == ''){
                            $response = $this->Bandwidth->sendmms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$assigned_number,$body,$message_arr,1);
                        }else{
                            $response = $this->Bandwidth->sendmms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$assigned_number,$body,$message_arr,1);
                        }
                        $credits = 2;
                    }
                    $usersave['User']['id'] = $user_id;
                    $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                    $this->User->save($usersave);
				} else if ($API_TYPE == 5) {
					if ($sms_type == 1) {
					    if ($SID == ''){
                            $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$assigned_number,$body);
                        }else{
                            $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$assigned_number,$body);
                        }
                        $credits = 1;
                    } else if ($sms_type == 2) {
                        $message_arr = explode(',', $image_url);
                        if ($SID == ''){
                            $response = $this->Signalwire->sendmms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$assigned_number,$body,$message_arr);
                        }else{
                            $response = $this->Signalwire->sendmms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$assigned_number,$body,$message_arr);
                        }
                        $credits = 2;
                    }
                    $usersave['User']['id'] = $user_id;
                    $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                    $this->User->save($usersave);
				} else if ($API_TYPE == 6) {
					if ($sms_type == 1) {
					    if ($SID == ''){
                            $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$assigned_number,$body);
                        }else{
                            $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$assigned_number,$body);
                        }
                        $credits = 1;
                    } else if ($sms_type == 2) {
                        $message_arr = explode(',', $image_url);
                        if ($SID == ''){
                            $response = $this->Ytel->sendmms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$assigned_number,$body,$message_arr);
                        }else{
                            $response = $this->Ytel->sendmms($SID,$AUTHTOKEN,$to,$assigned_number,$body,$message_arr);
                        }
                        $credits = 2;
                    }
                    $usersave['User']['id'] = $user_id;
                    $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                    $this->User->save($usersave); 
				} else if ($API_TYPE == 7) {
					if ($sms_type == 1) {
					    if ($SID == ''){
                            $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$assigned_number,$body);
                        }else{
                            $response = $this->Telnyx->sendsms($SID,$to,$assigned_number,$body);
                        }
                        $credits = 1;
                    } else if ($sms_type == 2) {
                        $message_arr = explode(',', $image_url);
                        if ($SID == ''){
                            $response = $this->Telnyx->sendmms(TELNYX_KEY,$to,$assigned_number,$body,$message_arr);
                        }else{
                            $response = $this->Telnyx->sendmms($SID,$to,$assigned_number,$body,$message_arr);
                        }
                        $credits = 2;
                    }
                    $usersave['User']['id'] = $user_id;
                    $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - $credits;
                    $this->User->save($usersave);    
                } else if ($API_TYPE == 2) {
                    $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $body);
                    $message_id = '';
                    $status = '';
                    if (isset($response['id'])) {
                        if ($response['result'] == 'ok') {
                            $message_id = $response['id'];
                        }
                        $status = $response['result'];
                    }
                    if ($message_id != '') {
                        $usersave['User']['id'] = $user_id;
                        $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - 1;
                        $this->User->save($usersave);
                    }
                } else if ($API_TYPE == 3) {
                    if ($SID == ''){
                        $this->Plivo->AuthId = PLIVO_KEY;
                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                    }else{
                        $this->Plivo->AuthId = $SID;
                        $this->Plivo->AuthToken = $AUTHTOKEN;
                    }
                    $response = $this->Plivo->sendsms($to, $assigned_number, $body);
                    $errortext = '';
                    $message_id = '';
                    if (isset($response['response']['error'])) {
                        $errortext = $response['response']['error'];
                    }
                    if (isset($response['response']['message_uuid'][0])) {
                        $message_id = $response['response']['message_uuid'][0];
                    }
                    if ($message_id != '') {
                        $usersave['User']['id'] = $user_id;
                        $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - 1;
                        $this->User->save($usersave);
                    }
                } else {
                    if ($SID == ''){
                        $this->Nexmomessage->Key = NEXMO_KEY;
                        $this->Nexmomessage->Secret = NEXMO_SECRET;
                    }else{
                        $this->Nexmomessage->Key = $SID;
                        $this->Nexmomessage->Secret = $AUTHTOKEN;
                    }
                    $response = $this->Nexmomessage->sendsms($to, $assigned_number, $body);
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
                        $usersave['User']['id'] = $user_id;
                        $usersave['User']['sms_balance'] = $users['User']['sms_balance'] - 1;
                        $this->User->save($usersave);
                    }
                }
            }
        }
    }
}