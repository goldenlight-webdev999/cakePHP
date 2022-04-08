<?php
App::import('Vendor', 'mailchimp', array('file' => 'mailchimp/MailChimp.php'));
App::import('Vendor', 'getresponse', array('file' => 'getresponse/GetResponse.php'));
App::import('Vendor', 'activecampaign', array('file' => 'activecampaign/ActiveCampaign.class.php'));
App::import('Vendor', 'aweber', array('file' => 'aweber/aweber_api.php'));
App::import('Vendor', 'mailin', array('file' => 'mailin/Mailin.php'));
include('./accounts.php');
App::uses('CakeEmail', 'Network/Email');

class ApisController extends AppController
{
    var $name = 'Apis';
    public $components = array('Twilio', 'Nexmomessage', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');
    public $uses = array('User', 'Contact', 'ContactGroup', 'Group', 'ActivityTimeline', 'Appointment', 'UserNumber', 'Log', 'ScheduleMessage', 'ScheduleMessageGroup', 'GroupSmsBlast', 'SingleScheduleMessage');
    public $useModel = false;

    function addcontact()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                if (!empty($users)) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $group = '';
                    if (isset($_REQUEST['group'])) {
                        $group = trim($_REQUEST['group']);
                    }
                    $number = '';
                    if (isset($_REQUEST['number'])) {
                        $number = trim(str_replace(array('-', '(', ')', '+', ' '), '', $_REQUEST['number']));
                    }
                    $name = '';
                    if (isset($_REQUEST['name'])) {
                        $name = trim($_REQUEST['name']);
                    }
                    $email = '';
                    if (isset($_REQUEST['email'])) {
                        $email = trim($_REQUEST['email']);
                    }
                    $bday = '';
                    if (isset($_REQUEST['bday'])) {
                        $bday = trim($_REQUEST['bday']);
                    }
                    if (($group != '') && ($number != '')) {
                        $groups = $this->Group->find('first', array('conditions' => array('Group.group_name' => $group, 'Group.user_id' => $users['User']['id']), 'order' => array('Group.id' => 'asc')));
                        if (!empty($groups)) {
                            app::import('Model', 'Contact');
                            $this->Contact = new Contact();
                            $contact = $this->Contact->find('first', array('conditions' => array('Contact.phone_number' => $number, 'Contact.user_id' => $users['User']['id'])));
                            if (empty($contact)) {
                                if (NUMVERIFY != '') {
                                    $numbervalidation = $this->validateNumber($number);
                                    $errorcode = $numbervalidation['error']['code'];
                                    $valid = $numbervalidation['valid'];
                                    $linetype = $numbervalidation['line_type'];
                                    if ($errorcode == '') {
                                        if ($valid == 1 && trim($linetype) == 'mobile') {
                                            $contact_arr['Contact']['carrier'] = $numbervalidation['carrier'];
                                            $contact_arr['Contact']['location'] = $numbervalidation['location'];
                                            $contact_arr['Contact']['phone_country'] = $numbervalidation['country_name'];
                                            $contact_arr['Contact']['line_type'] = $numbervalidation['line_type'];
                                        } else {
                                            $contact_arr['Contact']['carrier'] = '';
                                            $contact_arr['Contact']['location'] = '';
                                            $contact_arr['Contact']['phone_country'] = '';
                                            $contact_arr['Contact']['line_type'] = '';
                                        }
                                    } else {
                                        $contact_arr['Contact']['carrier'] = '';
                                        $contact_arr['Contact']['location'] = '';
                                        $contact_arr['Contact']['phone_country'] = '';
                                        $contact_arr['Contact']['line_type'] = '';
                                    }
                                }
                                $contact_arr['Contact']['name'] = $name;
                                $contact_arr['Contact']['email'] = $email;
                                if($bday !=''){
                                    $tempDate = explode('-', $bday);
                                    if (checkdate($tempDate[1], $tempDate[2], $tempDate[0])) {//checkdate(month, day, year)
                                        $bdayflag = 1;
                                    } else {
                                        $bdayflag = 0;
                                    }
                                    if ($bdayflag == 1) {
                                        $contact_arr['Contact']['birthday'] = $bday;
                                    }
                                }
                                $contact_arr['Contact']['phone_number'] = $number;
                                $contact_arr['Contact']['user_id'] = $users['User']['id'];
                                $contact_arr['Contact']['created'] = date('Y-m-d H:i:s', time());
                                $contact_arr['Contact']['color'] = $this->choosecolor();
                                if ($this->Contact->save($contact_arr)) {
                                    $contact_id = $this->Contact->id;
                                }
                            } else {
                                $contact_id = $contact['Contact']['id'];
                            }
                            app::import('Model', 'ContactGroup');
                            $this->ContactGroup = new ContactGroup();
                            $contactgroupid = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.group_id' => $groups['Group']['id'], 'ContactGroup.user_id' => $users['User']['id'])));
                            if (empty($contactgroupid)) {
                                if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $email != '') {
                                    if ($users['User']['email_service'] == 1) { //Mailchimp
                                        $list_id = $users['User']['email_listid'];
                                        $MailChimp = new MailChimp($users['User']['email_apikey']);
                                        if ($name != '') {
                                            $result = $MailChimp->post("lists/$list_id/members", array(
                                                'email_address' => $email,
                                                'status' => 'subscribed',
                                                'merge_fields' => array('FNAME' => $name),
                                            ));
                                        } else {
                                            $result = $MailChimp->post("lists/$list_id/members", array(
                                                'email_address' => $email,
                                                'status' => 'subscribed',
                                            ));
                                        }
                                    } else if ($users['User']['email_service'] == 2) { //Getresponse
                                        $list_id = $users['User']['email_listid'];
                                        $GetResponse = new GetResponse($users['User']['email_apikey']);
                                        if ($name != '') {
                                            $result = $GetResponse->addContact(array(
                                                'email' => $email,
                                                'name' => $name,
                                                'campaign' => array('campaignId' => $list_id
                                                )));
                                        } else {
                                            $result = $GetResponse->addContact(array(
                                                'email' => $email,
                                                'campaign' => array('campaignId' => $list_id
                                                )));
                                        }
                                    } else if ($users['User']['email_service'] == 3) { //Active Campaign
                                        $ac = new ActiveCampaign($users['User']['email_apiurl'], $users['User']['email_apikey']);
                                        $list_id = (int)$users['User']['email_listid'];
                                        $newcontact = array(
                                            'email' => $email,
                                            'first_name' => $name,
                                            'phone' => $number,
                                            'p[{$list_id}]' => $list_id,
                                            'status[{$list_id}]' => 1, // "Active" status
                                        );
                                        $contact_sync = $ac->api("contact/sync", $newcontact);
                                    } else if ($users['User']['email_service'] == 4) { //AWeber
                                        $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);
                                        $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);
                                        $account_id = $account->id;
                                        $list_id = $users['User']['email_listid'];
                                        $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                                        $list = $account->loadFromUrl($listURL);
                                        $params = array(
                                            'email' => $email,
                                            'name' => $name,
                                        );
                                        $subscribers = $list->subscribers;
                                        $new_subscriber = $subscribers->create($params);
                                    } else if ($users['User']['email_service'] == 5) { //Sendinblue
                                        $mailin = new Mailin(SENDINBLUE_APIURL, $users['User']['email_apikey']);
                                        $list_id = (int)$users['User']['email_listid'];
                                        $data = array("email" => $email,
                                            "attributes" => array("FIRSTNAME" => $name),
                                            "listid" => array($list_id)
                                        );
                                        $result = $mailin->create_update_user($data);
                                    }
                                }
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $contactgroup_arr['ContactGroup']['id'] = '';
                                $contactgroup_arr['ContactGroup']['user_id'] = $users['User']['id'];
                                $contactgroup_arr['ContactGroup']['contact_id'] = $contact_id;
                                $contactgroup_arr['ContactGroup']['group_id'] = $groups['Group']['id'];
                                $contactgroup_arr['ContactGroup']['group_subscribers'] = $groups['Group']['keyword'];
                                $contactgroup_arr['ContactGroup']['subscribed_by_sms'] = 0;
                                $contactgroup_arr['ContactGroup']['created'] = date('Y-m-d H:i:s', time());
                                if ($this->ContactGroup->save($contactgroup_arr)) {
                                    app::import('Model', 'Group');
                                    $this->Group = new Group();
                                    $groups_arr['Group']['id'] = $groups['Group']['id'];
                                    $groups_arr['Group']['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                                    if ($this->Group->save($groups_arr)) {
                                        //*********** Save to activity timeline
                                        $timeline['ActivityTimeline']['id'] = '';
                                        $timeline['ActivityTimeline']['user_id'] = $users['User']['id'];
                                        $timeline['ActivityTimeline']['contact_id'] = $contact_id;
                                        $timeline['ActivityTimeline']['activity'] = 1;
                                        $timeline['ActivityTimeline']['title'] = 'Contact Added via API';
                                        $timeline['ActivityTimeline']['description'] = 'Contact Added via API';
                                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                        if ($this->ActivityTimeline->save($timeline)) {
                                            $json_arr['status'] = '0';
                                            $json_arr['msg'] = 'Successful API call';
                                        } else {
                                            $json_arr['status'] = '-6';
                                            $json_arr['msg'] = 'Other error.';
                                        }
                                    } else {
                                        $json_arr['status'] = '-6';
                                        $json_arr['msg'] = 'Other error.';
                                    }
                                } else {
                                    $json_arr['status'] = '-6';
                                    $json_arr['msg'] = 'Other error.';
                                }
                            } else {
                                $json_arr['status'] = '-7';
                                $json_arr['msg'] = 'Number is already subscribed for ' . $groups['Group']['group_name'] . '';
                            }
                        } else {
                            $json_arr['status'] = '-5';
                            $json_arr['msg'] = 'The group passed in is invalid or do not exist in the group list';
                        }
                    } else if (($group != '') && ($number == '')) {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'There was no number passed in';
                    } else if (($group == '') && ($number != '')) {
                        $json_arr['status'] = '-4';
                        $json_arr['msg'] = 'There was no group passed in';
                    } else if (($group == '') && ($number == '')) {
                        $json_arr['status'] = '-3';
                        $json_arr['msg'] = 'Group and Number Required';
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode(array($json_arr));
    }

    function getcreditbalance()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                if (!empty($users)) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $type = '';
                    if (isset($_REQUEST['type'])) {
                        $type = preg_replace("/[^0-9]/", "", $_REQUEST['type']);
                    }
                    if (($type == 1) || ($type == 2)) {
                        if ($type == 2) {
                            $json_arr['creditbalancenumber'] = $users['User']['voice_balance'];
                        } else {
                            $json_arr['creditbalancenumber'] = $users['User']['sms_balance'];
                        }
                        $json_arr['msg'] = 'Your credit balance is returned on successful API call';
                    } else {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'Invalid type passed in. Should only be either a 1 or 2 depending on what credit balance you want to retrieve. 1 for SMS and 2 for Voice';
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode(array($json_arr));
    }

    function addappointment()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                if (!empty($users)) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $status_arr = array(0, 1, 2, 3);
                    $number = '';
                    if (isset($_REQUEST['number'])) {
                        $number = str_replace(array('-', '(', ')', '+', ' '), '', $_REQUEST['number']);
                    }
                    $apptdate = '';
                    if (isset($_REQUEST['apptdate'])) {
                        if ($_REQUEST['apptdate'] != '') {
                            $apptdate = date('Y-m-d H:i:s', strtotime($_REQUEST['apptdate']));
                        }
                    }
                    $status = '';
                    if (isset($_REQUEST['status'])) {
                        $status = preg_replace("/[^0-9]/", "", $_REQUEST['status']);
                    }
                    if ($number == '') {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'There was no contact number passed in';
                    } else if ($apptdate == '') {
                        $json_arr['status'] = '-4';
                        $json_arr['msg'] = 'There was no appt datetime passed in';
                    } else if ($status == '') {
                        $json_arr['status'] = '-5';
                        $json_arr['msg'] = 'There was no status passed in.';
                    } else if (!in_array($status, $status_arr)) {
                        $json_arr['status'] = '-6';
                        $json_arr['msg'] = 'The status passed in is invalid. Valid values are 0, 1, 2, 3.';
                    } else if (($number != '') && ($apptdate != '') && ($status != '')) {
                        $contact = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $number, 'ContactGroup.user_id' => $users['User']['id'])));
                        if (!empty($contact)) {
                            $appointments = $this->Appointment->find('first', array('conditions' => array('Appointment.contact_id' => $contact['Contact']['id'], 'Appointment.app_date_time' => $apptdate, 'Appointment.user_id' => $users['User']['id']), 'order' => array('Appointment.id' => 'asc')));
                            if (empty($appointments)) {
                                $appointment_arr['Appointment']['id'] = '';
                                $appointment_arr['Appointment']['user_id'] = $users['User']['id'];
                                $appointment_arr['Appointment']['contact_id'] = $contact['Contact']['id'];
                                $appointment_arr['Appointment']['app_date_time'] = $apptdate;
                                $appointment_arr['Appointment']['scheduled'] = 0;
                                $appointment_arr['Appointment']['appointment_status'] = $status;
                                $appointment_arr['Appointment']['created'] = date("Y-m-d H:i:s");
                                if ($this->Appointment->save($appointment_arr)) {
                                    $json_arr['status'] = '0';
                                    $json_arr['msg'] = 'Successful API call';
                                } else {
                                    $json_arr['status'] = '-9';
                                    $json_arr['msg'] = 'Other error.';
                                }
                            } else {
                                $json_arr['status'] = '-8';
                                $json_arr['msg'] = 'Appointment already exists for this contact and datetime';
                            }
                        } else {
                            $json_arr['status'] = '-7';
                            $json_arr['msg'] = 'Contact not found';
                        }
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode(array($json_arr));
    }

    function smsgroup()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                $API_TYPE = $users['User']['api_type'];
                $SID = trim($users['User']['sid']);
                $AUTHTOKEN = trim($users['User']['authtoken']);
                $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
                $sendsms = $users['User']['sendsms'];
                if (!empty($users) && $sendsms == 1) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $from = '';
                    if (isset($_REQUEST['from'])) {
                        $from = $_REQUEST['from'];
                    }
                    $group_name = '';
                    if (isset($_REQUEST['to'])) {
                        $group_name = $_REQUEST['to'];
                    }
                    $message = '';
                    if (isset($_REQUEST['message'])) {
                        $message = $_REQUEST['message'];
                    }
                    $alphasender = '';
                    if (isset($_REQUEST['alphasender'])) {
                        $alphasender = $_REQUEST['alphasender'];
                    }
                    $rotate = '';
                    if (isset($_REQUEST['rotate'])) {
                        $rotate = $_REQUEST['rotate'];
                    }
                    $throttle = '';
                    if (isset($_REQUEST['throttle'])) {
                        $throttle = $_REQUEST['throttle'];
                    }
                    $sendondate = '';
                    if (isset($_REQUEST['sendondate'])) {
                        if ($_REQUEST['sendondate'] != '') {
                            $sendondate = date('Y-m-d H:i:s', strtotime($_REQUEST['sendondate']));
                        }
                    }
                    $recurring = '';
                    if (isset($_REQUEST['recurring'])) {
                        $recurring = $_REQUEST['recurring'];
                    }
                    $repeat = '';
                    if (isset($_REQUEST['repeat'])) {
                        $repeat = $_REQUEST['repeat'];
                    }
                    $frequency = '';
                    if (isset($_REQUEST['frequency'])) {
                        $frequency = $_REQUEST['frequency'];
                    }
                    $enddate = '';
                    if (isset($_REQUEST['enddate'])) {
                        if ($_REQUEST['enddate'] != '') {
                            $enddate = date('Y-m-d H:i:s', strtotime($_REQUEST['enddate']));
                        }
                    }
                    if ($from == '') {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'There was no from number passed in.';
                    } else if ($group_name == '') {
                        $json_arr['status'] = '-5';
                        $json_arr['msg'] = 'There was no group passed in.';
                    } else if ($message == '') {
                        $json_arr['status'] = '-4';
                        $json_arr['msg'] = 'There was no message passed in to be sent.';
                    } else if (($alphasender != '') && ($users['User']['alphasender'] == 0)) {
                        $json_arr['status'] = '-7';
                        $json_arr['msg'] = "The alphanumeric sender ID passed in is either invalid(doesn't match requirements in API doc) OR the user account does not have permission to send from an alphanumeric sender ID";
                    } else if (($users['User']['alphasender'] == 1) && (strlen($alphasender) > 11)) {
                        $json_arr['status'] = '-7';
                        $json_arr['msg'] = "The alphanumeric sender ID passed in is either invalid(doesn't match requirements in API doc) OR the user account does not have permission to send from an alphanumeric sender ID";
                    } else if (($rotate != '') && (!in_array($rotate, array(0, 1)))) {
                        $json_arr['status'] = '-8';
                        $json_arr['msg'] = "The rotate flag passed in is invalid. Proper values are either 1 or 0.";
                    } else if (($throttle != '') && (!in_array($throttle, array(1, 2, 3, 4, 5, 6)))) {
                        $json_arr['status'] = '-9';
                        $json_arr['msg'] = "The sending throttle flag passed is invalid. Proper values are 1, 2, 3, 4, 5, or 6.";
                    } else if (($recurring != '') && (!in_array($recurring, array(0, 1)))) {
                        $json_arr['status'] = '-10';
                        $json_arr['msg'] = "The recurring flag passed in is invalid. Proper values are either 1 or 0.";
                    } else if (($recurring == 1) && ($repeat == '')) {
                        $json_arr['status'] = '-11';
                        $json_arr['msg'] = "The repeat parameter passed in is invalid. Proper values are 'Daily', 'Weekly', 'Monthly', 'Yearly'. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.";
                    } else if (($recurring == 1) && (!in_array($repeat, array('Daily', 'Weekly', 'Monthly', 'Yearly')))) {
                        $json_arr['status'] = '-11';
                        $json_arr['msg'] = "The repeat parameter passed in is invalid. Proper values are 'Daily', 'Weekly', 'Monthly', 'Yearly'. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.";
                    } else if (($recurring == 1) && ($frequency == '')) {
                        $json_arr['status'] = '-12';
                        $json_arr['msg'] = 'The frequency parameter passed in is invalid. Proper values are 1-30. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.';
                    } else if (($recurring == 1) && (!in_array($frequency, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30)))) {
                        $json_arr['status'] = '-12';
                        $json_arr['msg'] = 'The frequency parameter passed in is invalid. Proper values are 1-30. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.';
                    } else if (($recurring == 1) && ($enddate == '')) {
                        $json_arr['status'] = '-13';
                        $json_arr['msg'] = 'Must have a recurring end date if you want to schedule a recurring event. The recurring flag is 1 and nothing passed in for enddate.';
                    } else if (($from != '') && ($group_name != '') && ($message != '')) {
                        $user_arr = $this->User->find('first', array('conditions' => array('User.assigned_number' => $from, 'User.sms' => 1), 'order' => array('User.id' => 'asc')));
                        if (empty($user_arr)) {
                            $usernumbers_arr = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => $from, 'UserNumber.sms' => 1), 'order' => array('UserNumber.id' => 'asc')));
                            if (!empty($usernumbers_arr)) {
                                $from = $usernumbers_arr['UserNumber']['number'];
                            } else {
                                $from = '';
                            }
                        } else {
                            $from = $user_arr['User']['assigned_number'];
                        }
                        if ($alphasender != '') {
                            $from = $alphasender;
                        }
                        if ($from != '') {
                            $group_arr = $this->Group->find('first', array('conditions' => array('Group.group_name' => $group_name, 'Group.user_id' => $users['User']['id']), 'order' => array('Group.id' => 'asc')));
                            if (!empty($group_arr)) {
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $totalsubscribers = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0)));
                                if ($totalsubscribers > 0) {
                                    $spinbody = $this->process($message);
                                    //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                    $length = mb_strlen($spinbody,"UTF-8");
			                        $gsm = $this->is_gsm($spinbody);
                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                        $contactcredits = ceil($length / 70);
                                    } else {
                                        $contactcredits = ceil($length / 160);
                                    }
                                    if ($users['User']['sms_balance'] >= ($totalsubscribers * $contactcredits)) {
                                        if ($sendondate == '') {
                                            if ($API_TYPE == 0) {
                                                if ($rotate == 1) {
                                                    app::import('Model', 'UserNumber');
                                                    $this->UserNumber = new UserNumber();
                                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $users['User']['id'])));
                                                    $from_arr = array($from);
                                                    if (!empty($user_numbers)) {
                                                        foreach ($user_numbers as $values) {
                                                            if ($values['UserNumber']['sms'] == 1) {
                                                                $from_arr[] = $values['UserNumber']['number'];
                                                            }
                                                        }
                                                    }
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_id = 0;
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        $k = 0;
                                                        app::import('Model', 'Log');
                                                        $this->Twilio->curlinit = curl_init();
                                                        $this->Twilio->bulksms = 1;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
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
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $from = $from_arr[$k];

                                                                $stickyfrom = $groupContact['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $groupContact['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }

                                                                $message2 = $this->process($message1);
                                                                if ($SID == ''){
                                                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                                }else{
                                                                    $this->Twilio->AccountSid = $SID;
                                                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                                                }
                                                                $response = $this->Twilio->sendsms($to, $from, $message2);
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if (isset($response->ResponseXml->Message->Sid)) {
                                                                    $log_arr['Log']['sms_id'] = $response->ResponseXml->Message->Sid;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($response->ResponseXml->RestException->Status == 400) {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    if (isset($response->ErrorMessage)) {
                                                                        $log_arr['Log']['error_message'] = $response->ErrorMessage;
                                                                    } else {
                                                                        $log_arr['Log']['error_message'] = '';
                                                                    }
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    // $this->GroupSmsBlast = new GroupSmsBlast();
                                                                    $group_sms_blast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    //$groupsmsblast_id
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    //pr($groupContacts);
                                                                    $group_sms_blast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    //pr($this->request->data);
                                                                    $this->GroupSmsBlast->save($group_sms_blast_arr);
                                                                }
                                                                $this->Log->save($log_arr);
                                                                $k = $k + 1;
                                                            //}
                                                        }
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                } else {
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $this->Twilio->curlinit = curl_init();
                                                        $this->Twilio->bulksms = 1;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                if ($throttle > 1) {
                                                                    sleep($throttle);
                                                                }
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $message2 = $this->process($message1);
                                                                if ($SID == ''){
                                                                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                                                }else{
                                                                    $this->Twilio->AccountSid = $SID;
                                                                    $this->Twilio->AuthToken = $AUTHTOKEN;
                                                                }
                                                                $response = $this->Twilio->sendsms($to, $from, $message2);
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if (isset($response->ResponseXml->Message->Sid)) {
                                                                    $log_arr['Log']['sms_id'] = $response->ResponseXml->Message->Sid;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($response->ResponseXml->RestException->Status == 400) {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    if (isset($response->ErrorMessage)) {
                                                                        $log_arr['Log']['error_message'] = $response->ErrorMessage;
                                                                    } else {
                                                                        $log_arr['Log']['error_message'] = '';
                                                                    }
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    // $this->GroupSmsBlast = new GroupSmsBlast();
                                                                    $group_sms_blast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    //$groupsmsblast_id
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    //pr($groupContacts);
                                                                    $group_sms_blast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    //pr($this->request->data);
                                                                    $this->GroupSmsBlast->save($group_sms_blast_arr);
                                                                }
                                                                $this->Log->save($log_arr);
                                                            //}
                                                        }
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                }
                                            } else if ($API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {
												 if ($rotate == 1) {
                                                    app::import('Model', 'UserNumber');
                                                    $this->UserNumber = new UserNumber();
                                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $users['User']['id'])));
                                                    $from_arr = array($from);
                                                    if (!empty($user_numbers)) {
                                                        foreach ($user_numbers as $values) {
                                                            if ($values['UserNumber']['sms'] == 1) {
                                                                $from_arr[] = $values['UserNumber']['number'];
                                                            }
                                                        }
                                                    }
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_id = 0;
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        $k = 0;
                                                        $success = 0;
                                                        $credits = 0;
                                                        app::import('Model', 'Log');
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
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
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $from = $from_arr[$k];

                                                                $stickyfrom = $groupContact['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $groupContact['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }
                                                                $message2 = $this->process($message1);
                                                                /*if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message2);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message2);
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
																if($API_TYPE==5){
        														    if ($SID == ''){
                                								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message2);
        														    }else{
        														        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$message2);
        														    }
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
                                								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message2);
        														    }else{
        														        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$message2);
        														    }
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
                                								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message2);
        														    }else{
        														        $response = $this->Telnyx->sendsms($SID,$to,$from,$message2);
        														    }
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
                                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message2);
                                                                        }else{
                                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message2);
                                                                        }
                                    
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
                                					            
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
																
                                                                /* if (isset($response->ResponseXml->Message->Sid)) {
                                                                    $log_arr['Log']['sms_id'] = $response->ResponseXml->Message->Sid;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                } */
																$log_arr['Log']['sms_id'] = $message_id;
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($errortext !='') {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
																	$log_arr['Log']['error_message'] = $errortext;
                                                                    
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    // $this->GroupSmsBlast = new GroupSmsBlast();
                                                                    $group_sms_blast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    //$groupsmsblast_id
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    //pr($groupContacts);
                                                                    $group_sms_blast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    //pr($this->request->data);
                                                                    $this->GroupSmsBlast->save($group_sms_blast_arr);
                                                                }else{
                                                                    $success = $success + 1;
                                                                    $length = mb_strlen($message2,"UTF-8");
                                			                        $gsm = $this->is_gsm($message2);
                                                                  
                                                                    if(!$gsm){
                                                                        $credits = $credits + ceil($length / 70);
                                                                    } else {
                                                                        $credits = $credits + ceil($length / 160);
                                                                    }

                                                                }
                                                                $this->Log->save($log_arr);
                                                                $k = $k + 1;
                                                            //}
                                                        }
                                                        if ($success > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                } else {
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $success = 0;
                                                        $credits = 0;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                if ($throttle > 1) {
                                                                    sleep($throttle);
                                                                }
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $message2 = $this->process($message1);
                                                        
                                                                /*if ($SID == ''){
                                                                    $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message2);
                                                                }else{
                                                                    $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message2);
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
																if($API_TYPE==5){
        														    if ($SID == ''){
                                								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message2);
        														    }else{
        														        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$message2);
        														    }
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
                                								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message2);
        														    }else{
        														        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$message2);
        														    }
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
                                								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message2);
        														    }else{
        														        $response = $this->Telnyx->sendsms($SID,$to,$from,$message2);
        														    }
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
                                                                            $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message2);
                                                                        }else{
                                                                            $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message2);
                                                                        }
                                    
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
																
																
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                               /*  if (isset($response->ResponseXml->Message->Sid)) {
                                                                    $log_arr['Log']['sms_id'] = $response->ResponseXml->Message->Sid;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                } */
																$log_arr['Log']['sms_id'] = $message_id;
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                               if ($errortext !='') {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
																	$log_arr['Log']['error_message'] = $errortext;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    // $this->GroupSmsBlast = new GroupSmsBlast();
                                                                    $group_sms_blast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    //$groupsmsblast_id
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    //pr($groupContacts);
                                                                    $group_sms_blast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    //pr($this->request->data);
                                                                    $this->GroupSmsBlast->save($group_sms_blast_arr);
                                                                }else{
                                                                    $success = $success + 1;
                                                                    $length = mb_strlen($message2,"UTF-8");
                                			                        $gsm = $this->is_gsm($message2);
                                                                  
                                                                    if(!$gsm){
                                                                        $credits = $credits + ceil($length / 70);
                                                                    } else {
                                                                        $credits = $credits + ceil($length / 160);
                                                                    }

                                                                }
                                                                $this->Log->save($log_arr);
                                                            //}
                                                        }
                                                        if ($success > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $success;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                }
                                            } else if ($API_TYPE == 1) {
                                                if ($rotate == 1) {
                                                    app::import('Model', 'UserNumber');
                                                    $this->UserNumber = new UserNumber();
                                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $users['User']['id'])));
                                                    $from_arr = array($from);
                                                    if (!empty($user_numbers)) {
                                                        foreach ($user_numbers as $values) {
                                                            if ($values['UserNumber']['sms'] == 1) {
                                                                $from_arr[] = $values['UserNumber']['number'];
                                                            }
                                                        }
                                                    }
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $k = 0;
                                                        $sucesscredits = 0;
                                                        $credits = 0;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $countnumber = count($from_arr);
                                                                if ($countnumber == $k) {
                                                                    $k = 0;
                                                                    sleep($throttle);
                                                                }
                                                                $from = $from_arr[$k];

                                                                $stickyfrom = $groupContact['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $groupContact['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }

                                                                if ($SID == ''){
                                                                    $this->Nexmomessage->Key = NEXMO_KEY;
                                                                    $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                                }else{
                                                                    $this->Nexmomessage->Key = $SID;
                                                                    $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                                }
                                                                $message2 = $this->process($message1);
                                                                $response = $this->Nexmomessage->sendsms($to, $from, $message2);
                                                                $message_id = '';
                                                                $errortext = '';
                                                                $status = '';
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
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if ($message_id != '') {
                                                                    $log_arr['Log']['sms_id'] = $message_id;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($message_id != '') {
                                                                    $sucesscredits = $sucesscredits + 1;
                                                                    //$length = strlen(utf8_decode(substr($message2, 0, 1600)));
                                                                    $length = mb_strlen($message2,"UTF-8");
			                                                        $gsm = $this->is_gsm($message2);
                                                                    if(!$gsm){//if (strlen($message2) != strlen(utf8_decode($message2))) {
                                                                        $credits = $credits + ceil($length / 70);
                                                                    } else {
                                                                        $credits = $credits + ceil($length / 160);
                                                                    }
                                                                    $log_arr['Log']['sms_status'] = 'sent';
                                                                } else if ($status != 0) {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    $log_arr['Log']['error_message'] = $errortext;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    $groupsmsblast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    $groupsmsblast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    $this->GroupSmsBlast->save($groupsmsblast_arr);
                                                                }
                                                                $this->Log->save($log_arr);
                                                            //}
                                                            $k = $k + 1;
                                                        }
                                                        if ($sucesscredits > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                } else {
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $sucesscredits = 0;
                                                        $credits = 0;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                if ($throttle > 0) {
                                                                    sleep($throttle);
                                                                }
                                                                if ($SID == ''){
                                                                    $this->Nexmomessage->Key = NEXMO_KEY;
                                                                    $this->Nexmomessage->Secret = NEXMO_SECRET;
                                                                }else{
                                                                    $this->Nexmomessage->Key = $SID;
                                                                    $this->Nexmomessage->Secret = $AUTHTOKEN;
                                                                }
                                                                $message2 = $this->process($message1);
                                                                $response = $this->Nexmomessage->sendsms($to, $from, $message2);
                                                                $message_id = '';
                                                                $errortext = '';
                                                                $status = '';
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
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if ($message_id != '') {
                                                                    $log_arr['Log']['sms_id'] = $message_id;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($message_id != '') {
                                                                    $sucesscredits = $sucesscredits + 1;
                                                                    //$length = strlen(utf8_decode(substr($message2, 0, 1600)));
                                                                    $length = mb_strlen($message2,"UTF-8");
			                                                        $gsm = $this->is_gsm($message2);
                                                                    if(!$gsm){//if (strlen($message2) != strlen(utf8_decode($message2))) {
                                                                        $credits = $credits + ceil($length / 70);
                                                                    } else {
                                                                        $credits = $credits + ceil($length / 160);
                                                                    }
                                                                    $log_arr['Log']['sms_status'] = 'sent';
                                                                } else if ($status != 0) {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    $log_arr['Log']['error_message'] = $errortext;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    $groupsmsblast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    $groupsmsblast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    $this->GroupSmsBlast->save($groupsmsblast_arr);
                                                                }
                                                                $this->Log->save($log_arr);
                                                            //}
                                                        }
                                                        if ($sucesscredits > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                }
                                            } else if ($API_TYPE == 2) {
                                                if ($rotate == 1) {
                                                    app::import('Model', 'UserNumber');
                                                    $this->UserNumber = new UserNumber();
                                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $users['User']['id'])));
                                                    $from_arr = array($from);
                                                    if (!empty($user_numbers)) {
                                                        foreach ($user_numbers as $values) {
                                                            if ($values['UserNumber']['sms'] == 1) {
                                                                $from_arr[] = $values['UserNumber']['number'];
                                                            }
                                                        }
                                                    }
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $k = 0;
                                                        $sucesscredits = 0;
                                                        $credits = 0;
                                                        $this->Slooce->curlinit = curl_init();
                                                        $this->Slooce->bulksms = 1;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $countnumber = count($from_arr);
                                                                if ($countnumber == $k) {
                                                                    $k = 0;
                                                                }
                                                                $from = $from_arr[$k];
                                                                $body = $this->process($message1);
                                                                $response = $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $body);
                                                                $message_id = '';
                                                                $status = '';
                                                                if (isset($response['id'])) {
                                                                    if ($response['result'] == 'ok') {
                                                                        $message_id = $response['id'];
                                                                    }
                                                                    $status = $response['result'];
                                                                }
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if ($message_id != '') {
                                                                    $log_arr['Log']['sms_id'] = $message_id;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $body;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($message_id != '') {
                                                                    $sucesscredits = $sucesscredits + 1;
                                                                    $length = strlen(utf8_decode(substr($body, 0, 1600)));
                                                                    $credits = $credits + ceil($length / 160);
                                                                    $log_arr['Log']['sms_status'] = 'sent';
                                                                } else if ($status != 'ok') {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    $log_arr['Log']['error_message'] = $status;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    $group_sms_blast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    $group_sms_blast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    $this->GroupSmsBlast->save($group_sms_blast_arr);
                                                                }
                                                            //}
                                                            $k = $k + 1;
                                                        }
                                                        curl_close($this->Slooce->curlinit);
                                                        if ($sucesscredits > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                } else {
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $k = 0;
                                                        $sucesscredits = 0;
                                                        $credits = 0;
                                                        $this->Slooce->curlinit = curl_init();
                                                        $this->Slooce->bulksms = 1;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                $countnumber = count($from_arr);
                                                                if ($countnumber == $k) {
                                                                    $k = 0;
                                                                }
                                                                $body = $this->process($message1);
                                                                $response = $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $to, $users['User']['keyword'], $body);
                                                                $message_id = '';
                                                                $status = '';
                                                                if (isset($response['id'])) {
                                                                    if ($response['result'] == 'ok') {
                                                                        $message_id = $response['id'];
                                                                    }
                                                                    $status = $response['result'];
                                                                }
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if ($message_id != '') {
                                                                    $log_arr['Log']['sms_id'] = $message_id;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $body;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($message_id != '') {
                                                                    $sucesscredits = $sucesscredits + 1;
                                                                    $length = strlen(utf8_decode(substr($body, 0, 1600)));
                                                                    $credits = $credits + ceil($length / 160);
                                                                    $log_arr['Log']['sms_status'] = 'sent';
                                                                } else if ($status != 'ok') {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    $log_arr['Log']['error_message'] = $status;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    $group_sms_blast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    $group_sms_blast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    $this->GroupSmsBlast->save($group_sms_blast_arr);
                                                                }
                                                            //}
                                                            $k = $k + 1;
                                                        }
                                                        curl_close($this->Slooce->curlinit);
                                                        if ($sucesscredits > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                }
                                            } else if ($API_TYPE == 3) {
                                                if ($rotate == 1) {
                                                    app::import('Model', 'UserNumber');
                                                    $this->UserNumber = new UserNumber();
                                                    $user_numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $users['User']['id'])));
                                                    $from_arr = array($from);
                                                    if (!empty($user_numbers)) {
                                                        foreach ($user_numbers as $values) {
                                                            if ($values['UserNumber']['sms'] == 1) {
                                                                $from_arr[] = $values['UserNumber']['number'];
                                                            }
                                                        }
                                                    }
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $k = 0;
                                                        $sucesscredits = 0;
                                                        $credits = 0;
                                                        $this->Plivo->curlinit = curl_init();
                                                        $this->Plivo->bulksms = 1;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                $to = $groupContact['Contact']['phone_number'];
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

                                                                $stickyfrom = $groupContact['Contact']['stickysender'];
                                                                if ($stickyfrom == 0) {
                                                                    $contact['Contact']['id'] = $groupContact['Contact']['id'];
                                                                    $contact['Contact']['stickysender'] = $from;
                                                                    $this->Contact->save($contact);
                                                                } else {
                                                                    $from = $stickyfrom;
                                                                }

                                                                if ($SID == ''){
                                                                    $this->Plivo->AuthId = PLIVO_KEY;
                                                                    $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                                }else{
                                                                    $this->Plivo->AuthId = $SID;
                                                                    $this->Plivo->AuthToken = $AUTHTOKEN;
                                                                }
                                                                $message2 = $this->process($message1);
                                                                $response = $this->Plivo->sendsms($to, $from, $message2);
                                                                $errortext = '';
                                                                $message_id = '';
                                                                if (isset($response['response']['error'])) {
                                                                    $errortext = $response['response']['error'];
                                                                }
                                                                if (isset($response['response']['message_uuid'][0])) {
                                                                    $message_id = $response['response']['message_uuid'][0];
                                                                }
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if ($message_id != '') {
                                                                    $log_arr['Log']['sms_id'] = $message_id;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($message_id != '') {
                                                                    $sucesscredits = $sucesscredits + 1;
                                                                    //$length = strlen(utf8_decode(substr($message2, 0, 1600)));
                                                                    $length = mb_strlen($message2,"UTF-8");
			                                                        $gsm = $this->is_gsm($message2);
                                                                    if(!$gsm){//if (strlen($message2) != strlen(utf8_decode($message2))) {
                                                                        $credits = $credits + ceil($length / 70);
                                                                    } else {
                                                                        $credits = $credits + ceil($length / 160);
                                                                    }
                                                                    $log_arr['Log']['sms_status'] = 'sent';
                                                                } else if (isset($response['response']['error'])) {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    $log_arr['Log']['error_message'] = $errortext;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    $groupsmsblast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    $groupsmsblast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    $this->GroupSmsBlast->save($groupsmsblast_arr);
                                                                }
                                                                $this->Log->save($log_arr);
                                                            //}
                                                            $k = $k + 1;
                                                        }
                                                        curl_close($this->Plivo->curlinit);
                                                        if ($sucesscredits > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                } else {
                                                    $groupContacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $group_arr['Group']['id'], 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.phone_number', 'Contact.id', 'Contact.stickysender')));
                                                    if (!empty($groupContacts)) {
                                                        $groupsmsblast_id = 0;
                                                        app::import('Model', 'GroupSmsBlast');
                                                        $this->GroupSmsBlast = new GroupSmsBlast();
                                                        $groupsmsblast_arr['GroupSmsBlast']['id'] = '';
                                                        $groupsmsblast_arr['GroupSmsBlast']['user_id'] = $users['User']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['group_id'] = $group_arr['Group']['id'];
                                                        $groupsmsblast_arr['GroupSmsBlast']['totals'] = count($groupContacts);
                                                        if ($this->GroupSmsBlast->save($groupsmsblast_arr)) {
                                                            $groupsmsblast_id = $this->GroupSmsBlast->id;
                                                        }
                                                        app::import('Model', 'Log');
                                                        $sucesscredits = 0;
                                                        $credits = 0;
                                                        $this->Plivo->curlinit = curl_init();
                                                        $this->Plivo->bulksms = 1;
                                                        foreach ($groupContacts as $groupContact) {
                                                            $this->Log = new Log();
                                                            //if (!isset($phone[$groupContact['Contact']['phone_number']])) {
                                                                //$phone[$groupContact['Contact']['phone_number']] = $groupContact['Contact']['phone_number'];
                                                                $space_pos = strpos($groupContact['Contact']['name'], ' ');
                                                                if ($space_pos != '') {
                                                                    $contact_name = substr($groupContact['Contact']['name'], 0, $space_pos);
                                                                } else {
                                                                    $contact_name = $groupContact['Contact']['name'];
                                                                }
                                                                $message1 = str_replace('%%Name%%', $contact_name, $message);
                                                                $to = $groupContact['Contact']['phone_number'];
                                                                if ($throttle > 1) {
                                                                    sleep($throttle);
                                                                }
                                                                if ($SID == ''){
                                                                    $this->Plivo->AuthId = PLIVO_KEY;
                                                                    $this->Plivo->AuthToken = PLIVO_TOKEN;
                                                                }else{
                                                                    $this->Plivo->AuthId = $SID;
                                                                    $this->Plivo->AuthToken = $AUTHTOKEN;
                                                                }
                                                                $message2 = $this->process($message1);
                                                                $response = $this->Plivo->sendsms($to, $from, $message2);
                                                                $errortext = '';
                                                                $message_id = '';
                                                                if (isset($response['response']['error'])) {
                                                                    $errortext = $response['response']['error'];
                                                                }
                                                                if (isset($response['response']['message_uuid'][0])) {
                                                                    $message_id = $response['response']['message_uuid'][0];
                                                                }
                                                                $log_arr['Log']['id'] = '';
                                                                $log_arr['Log']['group_sms_id'] = $groupsmsblast_id;
                                                                if ($message_id != '') {
                                                                    $log_arr['Log']['sms_id'] = $message_id;
                                                                } else {
                                                                    $log_arr['Log']['sms_id'] = '';
                                                                }
                                                                $log_arr['Log']['user_id'] = $users['User']['id'];
                                                                $log_arr['Log']['group_id'] = $group_arr['Group']['id'];
                                                                $log_arr['Log']['phone_number'] = $groupContact['Contact']['phone_number'];
                                                                $log_arr['Log']['sendfrom'] = $from;
                                                                $log_arr['Log']['text_message'] = $message2;
                                                                $log_arr['Log']['msg_type'] = 'text';
                                                                $log_arr['Log']['route'] = 'outbox';
                                                                $log_arr['Log']['sms_status'] = '';
                                                                $log_arr['Log']['error_message'] = '';
                                                                if ($message_id != '') {
                                                                    $sucesscredits = $sucesscredits + 1;
                                                                    //$length = strlen(utf8_decode(substr($message2, 0, 1600)));
                                                                    $length = mb_strlen($message2,"UTF-8");
			                                                        $gsm = $this->is_gsm($message2);
                                                                    if(!$gsm){//if (strlen($message2) != strlen(utf8_decode($message2))) {
                                                                        $credits = $credits + ceil($length / 70);
                                                                    } else {
                                                                        $credits = $credits + ceil($length / 160);
                                                                    }
                                                                    $log_arr['Log']['sms_status'] = 'sent';
                                                                } else if (isset($response['response']['error'])) {
                                                                    $log_arr['Log']['sms_status'] = 'failed';
                                                                    $log_arr['Log']['error_message'] = $errortext;
                                                                    app::import('Model', 'GroupSmsBlast');
                                                                    $groupsmsblast_arr['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                                    $groupsmsblast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsblast_id)));
                                                                    $groupsmsblast_arr['GroupSmsBlast']['total_failed_messages'] = $groupsmsblast['GroupSmsBlast']['total_failed_messages'] + 1;
                                                                    $this->GroupSmsBlast->save($groupsmsblast_arr);
                                                                }
                                                                $this->Log->save($log_arr);
                                                            //}
                                                        }
                                                        curl_close($this->Plivo->curlinit);
                                                        if ($sucesscredits > 0) {
                                                            $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                            if (!empty($usersbalance)) {
                                                                $usercredit['User']['id'] = $users['User']['id'];
                                                                $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                                $this->User->save($usercredit);
                                                            }
                                                            app::import('Model', 'GroupSmsBlast');
                                                            $group_blast['GroupSmsBlast']['id'] = $groupsmsblast_id;
                                                            $group_blast['GroupSmsBlast']['total_successful_messages'] = $sucesscredits;
                                                            $this->GroupSmsBlast->save($group_blast);
                                                        }
                                                        $this->smsmail($users['User']['id']);
                                                        //$json_arr['status'] = '0';
                                                        $json_arr['status'] = $groupsmsblast_id;
                                                        $json_arr['msg'] = 'Successful API call';
                                                    } else {
                                                        $json_arr['status'] = '-15';
                                                        $json_arr['msg'] = 'No subscribers found in group.';
                                                    }
                                                }
                                            }
                                        } else {
                                            app::import('Model', 'ScheduleMessage');
                                            $this->ScheduleMessage = new ScheduleMessage();
                                            $schedulemessage_arr['ScheduleMessage']['id'] = '';
                                            $schedulemessage_arr['ScheduleMessage']['user_id'] = $users['User']['id'];
                                            $schedulemessage_arr['ScheduleMessage']['send_on'] = $sendondate;
                                            $schedulemessage_arr['ScheduleMessage']['message'] = $message;
                                            if ($rotate == '') {
                                                $schedulemessage_arr['ScheduleMessage']['rotate_number'] = 0;
                                            } else {
                                                $schedulemessage_arr['ScheduleMessage']['rotate_number'] = $rotate;
                                            }
                                            $schedulemessage_arr['ScheduleMessage']['msg_type'] = 1;
                                            $schedulemessage_arr['ScheduleMessage']['mms_text'] = '';
                                            if ($throttle != '') {
                                                $schedulemessage_arr['ScheduleMessage']['throttle'] = $throttle;
                                            } else {
                                                $schedulemessage_arr['ScheduleMessage']['throttle'] = 1;
                                            }
                                            if ($alphasender != '') {
                                                $schedulemessage_arr['ScheduleMessage']['alphasender_input'] = $alphasender;
                                            } else {
                                                $schedulemessage_arr['ScheduleMessage']['alphasender_input'] = '';
                                            }
                                            if ($from != '') {
                                                $schedulemessage_arr['ScheduleMessage']['sendfrom'] = $from;
                                            } else {
                                                $schedulemessage_arr['ScheduleMessage']['sendfrom'] = '';
                                            }
                                            $schedulemessage_arr['ScheduleMessage']['systemmsg'] = '';
                                            $schedulemessage_arr['ScheduleMessage']['pick_file'] = '';
                                            if ($recurring == 1) {
                                                $begin = new DateTime($sendondate);
                                                $end = new DateTime($enddate);
                                                $end->modify('+5 minutes');
                                                if ($repeat == 'Daily') {
                                                    $interval = DateInterval::createFromDateString("$frequency days");
                                                    $period = new DatePeriod($begin, $interval, $end);
                                                } else if ($repeat == 'Weekly') {
                                                    $interval = DateInterval::createFromDateString("$frequency week");
                                                    $period = new DatePeriod($begin, $interval, $end);
                                                } else if ($repeat == 'Monthly') {
                                                    $interval = DateInterval::createFromDateString("$frequency month");
                                                    $period = new DatePeriod($begin, $interval, $end);
                                                } else {
                                                    $interval = DateInterval::createFromDateString("$frequency year");
                                                    $period = new DatePeriod($begin, $interval, $end);
                                                }
                                                $Schedule_Message = $this->ScheduleMessage->find('first', array('conditions' => array('ScheduleMessage.user_id' => $users['User']['id']), 'fields' => 'ScheduleMessage.recurring_id', 'order' => array('ScheduleMessage.recurring_id' => 'desc')));
                                                if (!empty($Schedule_Message['ScheduleMessage']['recurring_id'])) {
                                                    $schedulemessage_arr['ScheduleMessage']['recurring_id'] = $Schedule_Message['ScheduleMessage']['recurring_id'] + 1;
                                                } else {
                                                    $schedulemessage_arr['ScheduleMessage']['recurring_id'] = 1;
                                                }
                                                app::import('Model', 'ScheduleMessageGroup');
                                                foreach ($period as $dt) {
                                                    $schedulemessage_arr['ScheduleMessage']['id'] = '';
                                                    $schedulemessage_arr['ScheduleMessage']['send_on'] = $dt->format("Y-m-d H:i:s");
                                                    if ($this->ScheduleMessage->save($schedulemessage_arr)) {
                                                        $schedule_sms_id = $this->ScheduleMessage->id;
                                                        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                                                        $schedulemessage_group_arr['ScheduleMessageGroup']['id'] = '';
                                                        $schedulemessage_group_arr['ScheduleMessageGroup']['group_id'] = $group_arr['Group']['id'];
                                                        $schedulemessage_group_arr['ScheduleMessageGroup']['schedule_sms_id'] = $schedule_sms_id;
                                                        $this->ScheduleMessageGroup->save($schedulemessage_group_arr);
                                                    }
                                                }
                                                $json_arr['status'] = '0';
                                                $json_arr['msg'] = 'Successful API call';
                                            } else {
                                                if ($this->ScheduleMessage->save($schedulemessage_arr)) {
                                                    $schedule_sms_id = $this->ScheduleMessage->id;
                                                    if ($schedule_sms_id > 0) {
                                                        app::import('Model', 'ScheduleMessageGroup');
                                                        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                                                        $schedulemessage_group_arr['ScheduleMessageGroup']['id'] = '';
                                                        $schedulemessage_group_arr['ScheduleMessageGroup']['group_id'] = $group_arr['Group']['id'];
                                                        $schedulemessage_group_arr['ScheduleMessageGroup']['schedule_sms_id'] = $schedule_sms_id;
                                                        if ($this->ScheduleMessageGroup->save($schedulemessage_group_arr)) {
                                                            $json_arr['status'] = '0';
                                                            $json_arr['msg'] = 'Successful API call';
                                                        }
                                                    } else {
                                                        $json_arr['status'] = '-16';
                                                        $json_arr['msg'] = 'Other error.';
                                                    }
                                                } else {
                                                    $json_arr['status'] = '-16';
                                                    $json_arr['msg'] = 'Other error.';
                                                }
                                            }
                                        }
                                    } else {
                                        $json_arr['status'] = '-14';
                                        $json_arr['msg'] = 'Not enough credits to send the message.';
                                    }
                                } else {
                                    $json_arr['status'] = '-15';
                                    $json_arr['msg'] = 'No subscribers found in group.';
                                }
                            } else {
                                $json_arr['status'] = '-6';
                                $json_arr['msg'] = 'The group passed in is invalid or does not exist.';
                            }
                        } else {
                            $json_arr['status'] = '-3';
                            $json_arr['msg'] = "The from number passed in doesn't exist in your account or isn't SMS-enabled.";
                        }
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in or account does not have access to send sms';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode(array($json_arr));
    }

    function smscontact()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $schedule_message_arr = array();
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                $API_TYPE = $users['User']['api_type'];
                $SID = trim($users['User']['sid']);
                $AUTHTOKEN = trim($users['User']['authtoken']);
                $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
                $sendsms = $users['User']['sendsms'];
                if (!empty($users) && $sendsms == 1) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $from = '';
                    if (isset($_REQUEST['from'])) {
                        $from = $_REQUEST['from'];
                    }
                    $to = '';
                    if (isset($_REQUEST['to'])) {
                        $to = $_REQUEST['to'];
                    }
                    $message = '';
                    if (isset($_REQUEST['message'])) {
                        $message = $_REQUEST['message'];
                    }
                    $alphasender = '';
                    if (isset($_REQUEST['alphasender'])) {
                        $alphasender = $_REQUEST['alphasender'];
                    }
                    $sendondate = '';
                    if (isset($_REQUEST['sendondate'])) {
                        if ($_REQUEST['sendondate'] != '') {
                            $sendondate = date('Y-m-d H:i:s', strtotime($_REQUEST['sendondate']));
                        }
                    }
                    $recurring = '';
                    if (isset($_REQUEST['recurring'])) {
                        $recurring = $_REQUEST['recurring'];
                    }
                    $repeat = '';
                    if (isset($_REQUEST['repeat'])) {
                        $repeat = $_REQUEST['repeat'];
                    }
                    $frequency = '';
                    if (isset($_REQUEST['frequency'])) {
                        $frequency = $_REQUEST['frequency'];
                    }
                    $enddate = '';
                    if (isset($_REQUEST['enddate'])) {
                        if ($_REQUEST['enddate'] != '') {
                            $enddate = date('Y-m-d H:i:s', strtotime($_REQUEST['enddate']));
                        }
                    }
                    if ($from == '') {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'There was no from number passed in.';
                    } else if ($to == '') {
                        $json_arr['status'] = '-5';
                        $json_arr['msg'] = 'There was no contact number passed in.';
                    } else if ($message == '') {
                        $json_arr['status'] = '-4';
                        $json_arr['msg'] = 'There was no message passed in to be sent.';
                    } else if (($alphasender != '') && ($users['User']['alphasender'] == 0)) {
                        $json_arr['status'] = '-7';
                        $json_arr['msg'] = "The alphanumeric sender ID passed in is either invalid(doesn't match requirements in API doc) OR the user account does not have permission to send from an alphanumeric sender ID";
                    } else if (($alphasender != '') && ($users['User']['alphasender'] == 1) && (strlen($alphasender) > 11)) {
                        $json_arr['status'] = '-7';
                        $json_arr['msg'] = "The alphanumeric sender ID passed in is either invalid(doesn't match requirements in API doc) OR the user account does not have permission to send from an alphanumeric sender ID";
                    } else if (($recurring != '') && (!in_array($recurring, array(0, 1)))) {
                        $json_arr['status'] = '-8';
                        $json_arr['msg'] = "The recurring flag passed in is invalid. Proper values are either 1 or 0.";
                    } else if (($recurring == 1) && ($repeat == '')) {
                        $json_arr['status'] = '-9';
                        $json_arr['msg'] = "The repeat parameter passed in is invalid. Proper values are 'Daily', 'Weekly', 'Monthly', 'Yearly'. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.";
                    } else if (($recurring == 1) && (!in_array($repeat, array('Daily', 'Weekly', 'Monthly', 'Yearly')))) {
                        $json_arr['status'] = '-9';
                        $json_arr['msg'] = "The repeat parameter passed in is invalid. Proper values are 'Daily', 'Weekly', 'Monthly', 'Yearly'. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.";
                    } else if (($recurring == 1) && ($frequency == '')) {
                        $json_arr['status'] = '-10';
                        $json_arr['msg'] = 'The frequency parameter passed in is invalid. Proper values are 1-30. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.';
                    } else if (($recurring == 1) && (!in_array($frequency, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30)))) {
                        $json_arr['status'] = '-10';
                        $json_arr['msg'] = 'The frequency parameter passed in is invalid. Proper values are 1-30. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.';
                    } else if (($recurring == 1) && ($enddate == '')) {
                        $json_arr['status'] = '-11';
                        $json_arr['msg'] = 'Must have a recurring end date if you want to schedule a recurring event. The recurring flag is 1 and nothing passed in for enddate.';
                    } else if (($from != '') && ($to != '') && ($message != '')) {
                        $user_arr = $this->User->find('first', array('conditions' => array('User.assigned_number' => $from, 'User.sms' => 1)));
                        if (empty($user_arr)) {
                            $usernumbers_arr = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => $from, 'UserNumber.sms' => 1)));
                            if (!empty($usernumbers_arr)) {
                                $from = $usernumbers_arr['UserNumber']['number'];
                            } else {
                                $from = '';
                            }
                        } else {
                            $from = $user_arr['User']['assigned_number'];
                        }
                        if ($alphasender != '') {
                            $from = $alphasender;
                        }
                        if ($from != '') {
                            //$contact_arr = $this->Contact->find('first',array('conditions' => array('Contact.phone_number'=>$to)));
                            $contact_arr = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $to, 'ContactGroup.user_id' => $users['User']['id'], 'ContactGroup.un_subscribers' => 0)));
                            if (empty($sendondate)) {
                                if (!empty($contact_arr)) {
                                    $spinbody = $this->process($message);
                                    //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                    $length = mb_strlen($spinbody,"UTF-8");
			                        $gsm = $this->is_gsm($spinbody);
                                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                        $contactcredits = ceil($length / 70);
                                    } else {
                                        $contactcredits = ceil($length / 160);
                                    }
                                    if ($users['User']['sms_balance'] >= $contactcredits) {
                                        if ($API_TYPE == 0) {
                                            if ($SID == ''){
                                                $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                                $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                                            }else{
                                                $this->Twilio->AccountSid = $SID;
                                                $this->Twilio->AuthToken = $AUTHTOKEN;
                                            }
                                            $message = $this->process($message);
                                            $response = $this->Twilio->sendsms($to, $from, $message);
                                            $log_arr['Log']['id'] = '';
                                            $log_arr['Log']['group_sms_id'] = 0;
                                            if (isset($response->ResponseXml->Message->Sid)) {
                                                $log_arr['Log']['sms_id'] = $response->ResponseXml->Message->Sid;
                                            } else {
                                                $log_arr['Log']['sms_id'] = '';
                                            }
                                            $log_arr['Log']['user_id'] = $users['User']['id'];
                                            $log_arr['Log']['group_id'] = 0;
                                            $log_arr['Log']['msg_type'] = 'text';
                                            $log_arr['Log']['phone_number'] = $to;
                                            $log_arr['Log']['text_message'] = $message;
                                            $log_arr['Log']['route'] = 'outbox';
                                            $log_arr['Log']['sms_status'] = '';
                                            $log_arr['Log']['error_message'] = '';
                                            if ($response->ResponseXml->RestException->Status == 400) {
                                                $log_arr['Log']['sms_status'] = 'failed';
                                                if (isset($response->ErrorMessage)) {
                                                    $log_arr['Log']['error_message'] = $response->ErrorMessage;
                                                }
                                            }
                                            if ($this->Log->save($log_arr)) {
                                                $json_arr['status'] = '0';
                                                $json_arr['msg'] = 'Successful API call';
                                            }
                                        } else if ($API_TYPE == 4 || $API_TYPE == 5 || $API_TYPE == 6 || $API_TYPE == 7) {
                                            $message = $this->process($message);
                                            /*if ($SID == ''){
                                                $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
                                            }else{
                                                $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message);
                                            }
											
											$message_id= '';
											$status= '';
											$errortext= '';
											if(isset($response->message)){
												$errortext = $response->message;
												$status = 'failed';
											}else{
												$status = 'sent';
											}*/
											
											$message_id = '';
											if($API_TYPE==5){
											    if ($SID == ''){
            								        $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$to,$from,$message);
											    }else{
											        $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$to,$from,$message);
											    }
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
            								        $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$to,$from,$message);
											    }else{
											        $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$to,$from,$message);
											    }
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
            								        $response = $this->Telnyx->sendsms(TELNYX_KEY,$to,$from,$message);
											    }else{
											        $response = $this->Telnyx->sendsms($SID,$to,$from,$message);
											    }
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
                                                        $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$to,$from,$message);
                                                    }else{
                                                        $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$to,$from,$message);
                                                    }
                
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
            					            
											$log_arr['Log']['sms_id'] = $message_id;
                                            $log_arr['Log']['id'] = '';
                                            $log_arr['Log']['group_sms_id'] = 0;
                                            $log_arr['Log']['user_id'] = $users['User']['id'];
                                            $log_arr['Log']['group_id'] = 0;
                                            $log_arr['Log']['msg_type'] = 'text';
                                            $log_arr['Log']['phone_number'] = $to;
                                            $log_arr['Log']['text_message'] = $message;
                                            $log_arr['Log']['route'] = 'outbox';
                                            $log_arr['Log']['sms_status'] = '';
                                            $log_arr['Log']['error_message'] = '';
                                            if ($errortext !='') {
                                                $log_arr['Log']['sms_status'] = 'failed';
												$log_arr['Log']['error_message'] = $errortext;
                                            }else{
                                                $success = $success + 1;
                                                $length = mb_strlen($message,"UTF-8");
            			                        $gsm = $this->is_gsm($message);
                                              
                                                if(!$gsm){
                                                    $credits = $credits + ceil($length / 70);
                                                } else {
                                                    $credits = $credits + ceil($length / 160);
                                                }

                                            }
                                            if ($this->Log->save($log_arr)) {
                                                if ($success > 0) {
                                                    $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                    if (!empty($usersbalance)) {
                                                        $usercredit['User']['id'] = $users['User']['id'];
                                                        $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                        $this->User->save($usercredit);
                                                    }
                                                }
                                                $this->smsmail($users['User']['id']);
                                                $json_arr['status'] = '0';
                                                $json_arr['msg'] = 'Successful API call';
                                            }
                                        } else if ($API_TYPE == 2) {
                                            $message = $this->process($message);
                                            $response = $this->Slooce->mt($users_arr['User']['api_url'], $users_arr['User']['partnerid'], $users_arr['User']['partnerpassword'], $to, $users_arr['User']['keyword'], $message);
                                            $message_id = '';
                                            $status = '';
                                            if (isset($response['id'])) {
                                                if ($response['result'] == 'ok') {
                                                    $message_id = $response['id'];
                                                }
                                                $status = $response['result'];
                                            }
                                            $log_arr['Log']['id'] = '';
                                            $log_arr['Log']['group_sms_id'] = 0;
                                            $log_arr['Log']['sms_id'] = $message_id;
                                            $log_arr['Log']['user_id'] = $users['User']['id'];
                                            $log_arr['Log']['group_id'] = 0;
                                            $log_arr['Log']['msg_type'] = 'text';
                                            $log_arr['Log']['phone_number'] = $to;
                                            $log_arr['Log']['text_message'] = $message;
                                            $log_arr['Log']['route'] = 'outbox';
                                            $log_arr['Log']['sms_status'] = '';
                                            $log_arr['Log']['error_message'] = '';
                                            if ($status != 'ok') {
                                                $log_arr['Log']['sms_status'] = 'failed';
                                                $log_arr['Log']['error_message'] = $status;
                                            }
                                            if ($message_id != '') {
                                                $sucesscredits = $sucesscredits + 1;
                                                $length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $credits = $credits + ceil($length / 160);
                                                $log_arr['Log']['sms_status'] = 'sent';
                                            }
                                            $this->Log->save($log_arr);
                                            if ($sucesscredits > 0) {
                                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                if (!empty($usersbalance)) {
                                                    $usercredit['User']['id'] = $users['User']['id'];
                                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                    $this->User->save($usercredit);
                                                }
                                            }
                                            $this->smsmail($users['User']['id']);
                                            $json_arr['status'] = '0';
                                            $json_arr['msg'] = 'Successful API call';
                                        } else if ($API_TYPE == 3) {
                                            if ($SID == ''){
                                                $this->Plivo->AuthId = PLIVO_KEY;
                                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                                            }else{
                                                $this->Plivo->AuthId = $SID;
                                                $this->Plivo->AuthToken = $AUTHTOKEN;
                                            }
                                            $message = $this->process($message);
                                            $response = $this->Plivo->sendsms($to, $from, $message);
                                            $errortext = '';
                                            $message_id = '';
                                            if (isset($response['response']['error'])) {
                                                $errortext = $response['response']['error'];
                                            }
                                            if (isset($response['response']['message_uuid'][0])) {
                                                $message_id = $response['response']['message_uuid'][0];
                                            }
                                            $log_arr['Log']['id'] = '';
                                            $log_arr['Log']['group_sms_id'] = 0;
                                            $log_arr['Log']['sms_id'] = $message_id;
                                            $log_arr['Log']['user_id'] = $users['User']['id'];
                                            $log_arr['Log']['group_id'] = 0;
                                            $log_arr['Log']['phone_number'] = $to;
                                            $log_arr['Log']['text_message'] = $message;
                                            $log_arr['Log']['route'] = 'outbox';
                                            $log_arr['Log']['msg_type'] = 'text';
                                            $log_arr['Log']['sms_status'] = '';
                                            $log_arr['Log']['error_message'] = '';
                                            if (isset($response['response']['error'])) {
                                                $log_arr['Log']['sms_status'] = 'failed';
                                                $log_arr['Log']['error_message'] = $errortext;
                                            }
                                            if ($message_id != '') {
                                                $sucesscredits = $sucesscredits + 1;
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $credits = $credits + ceil($length / 70);
                                                } else {
                                                    $credits = $credits + ceil($length / 160);
                                                }
                                                $log_arr['Log']['sms_status'] = 'sent';
                                            }
                                            $this->Log->save($log_arr);
                                            if ($sucesscredits > 0) {
                                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                if (!empty($usersbalance)) {
                                                    $usercredit['User']['id'] = $users['User']['id'];
                                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                    $this->User->save($usercredit);
                                                }
                                            }
                                            $this->smsmail($users['User']['id']);
                                            $json_arr['status'] = '0';
                                            $json_arr['msg'] = 'Successful API call';
                                        } else {
                                            if ($SID == ''){
                                                $this->Nexmomessage->Key = NEXMO_KEY;
                                                $this->Nexmomessage->Secret = NEXMO_SECRET;
                                            }else{
                                                $this->Nexmomessage->Key = $SID;
                                                $this->Nexmomessage->Secret = $AUTHTOKEN;
                                            }
                                            $message = $this->process($message);
                                            $response = $this->Nexmomessage->sendsms($to, $from, $message);
                                            $message_id = '';
                                            $status = '';
                                            $errortext = '';
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
                                            $log_arr['Log']['id'] = '';
                                            $log_arr['Log']['group_sms_id'] = 0;
                                            $log_arr['Log']['sms_id'] = $message_id;
                                            $log_arr['Log']['user_id'] = $users['User']['id'];
                                            $log_arr['Log']['group_id'] = 0;
                                            $log_arr['Log']['phone_number'] = $to;
                                            $log_arr['Log']['text_message'] = $message;
                                            $log_arr['Log']['route'] = 'outbox';
                                            $log_arr['Log']['msg_type'] = 'text';
                                            $log_arr['Log']['sms_status'] = '';
                                            $log_arr['Log']['error_message'] = '';
                                            if ($status != 0) {
                                                $log_arr['Log']['sms_status'] = 'failed';
                                                $log_arr['Log']['error_message'] = $errortext;
                                            }
                                            if ($message_id != '') {
                                                $sucesscredits = $sucesscredits + 1;
                                                //$length = strlen(utf8_decode(substr($message, 0, 1600)));
                                                $length = mb_strlen($message,"UTF-8");
			                                    $gsm = $this->is_gsm($message);
                                                if(!$gsm){//if (strlen($message) != strlen(utf8_decode($message))) {
                                                    $credits = $credits + ceil($length / 70);
                                                } else {
                                                    $credits = $credits + ceil($length / 160);
                                                }
                                                $log_arr['Log']['sms_status'] = 'sent';
                                            }
                                            $this->Log->save($log_arr);
                                            if ($sucesscredits > 0) {
                                                $usersbalance = $this->User->find('first', array('conditions' => array('User.id' => $users['User']['id'])));
                                                if (!empty($usersbalance)) {
                                                    $usercredit['User']['id'] = $users['User']['id'];
                                                    $usercredit['User']['sms_balance'] = $usersbalance['User']['sms_balance'] - $credits;
                                                    $this->User->save($usercredit);
                                                }
                                            }
                                            $this->smsmail($users['User']['id']);
                                            $json_arr['status'] = '0';
                                            $json_arr['msg'] = 'Successful API call';
                                        }
                                    } else {
                                        $json_arr['status'] = '-12';
                                        $json_arr['msg'] = 'Not enough credits to send the message.';
                                    }
                                } else {
                                    $json_arr['status'] = '-6';
                                    $json_arr['msg'] = 'The contact number passed in is either invalid, does not exist in your contact list, or has unsubscribed.';
                                }
                            } else {
                                $spinbody = $this->process($message);
                                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                                $length = mb_strlen($spinbody,"UTF-8");
			                    $gsm = $this->is_gsm($spinbody);
                                if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                                    $contactcredits = ceil($length / 70);
                                } else {
                                    $contactcredits = ceil($length / 160);
                                }
                                $schedule_message_arr['ScheduleMessage']['id'] = '';
                                $schedule_message_arr['ScheduleMessage']['user_id'] = $users['User']['id'];
                                $schedule_message_arr['ScheduleMessage']['send_on'] = $sendondate;
                                $schedule_message_arr['ScheduleMessage']['message'] = $message;
                                $schedule_message_arr['ScheduleMessage']['systemmsg'] = '';
                                $schedule_message_arr['ScheduleMessage']['msg_type'] = 1;
                                if ($alphasender != '') {
                                    $schedule_message_arr['ScheduleMessage']['alphasender_input'] = $alphasender;
                                } else {
                                    $schedule_message_arr['ScheduleMessage']['alphasender_input'] = '';
                                }
                                if ($from != '') {
                                    $schedule_message_arr['ScheduleMessage']['sendfrom'] = $from;
                                } else {
                                    $schedule_message_arr['ScheduleMessage']['sendfrom'] = '';
                                }
                                if ($users['User']['sms_balance'] >= $contactcredits) {
                                    if ($recurring == 1) {
                                        $begin = new DateTime($sendondate);
                                        $end = new DateTime($enddate);
                                        $end->modify('+5 minutes');
                                        if ($repeat == 'Daily') {
                                            $interval = DateInterval::createFromDateString("$frequency days");
                                            $period = new DatePeriod($begin, $interval, $end);
                                        } else if ($repeat == 'Weekly') {
                                            $interval = DateInterval::createFromDateString("$frequency week");
                                            $period = new DatePeriod($begin, $interval, $end);
                                        } else if ($repeat == 'Monthly') {
                                            $interval = DateInterval::createFromDateString("$frequency month");
                                            $period = new DatePeriod($begin, $interval, $end);
                                        } else {
                                            $interval = DateInterval::createFromDateString("$frequency year");
                                            $period = new DatePeriod($begin, $interval, $end);
                                        }
                                        $Schedule_Message = $this->ScheduleMessage->find('first', array('conditions' => array('ScheduleMessage.user_id' => $users['User']['id']), 'fields' => 'ScheduleMessage.recurring_id', 'order' => array('ScheduleMessage.recurring_id' => 'desc')));
                                        if (!empty($Schedule_Message['ScheduleMessage']['recurring_id'])) {
                                            $schedule_message_arr['ScheduleMessage']['recurring_id'] = $Schedule_Message['ScheduleMessage']['recurring_id'] + 1;
                                        } else {
                                            $schedule_message_arr['ScheduleMessage']['recurring_id'] = 1;
                                        }
                                        app::import('Model', 'SingleScheduleMessage');
                                        foreach ($period as $dt) {
                                            $schedule_message_arr['ScheduleMessage']['id'] = '';
                                            $schedule_message_arr['ScheduleMessage']['send_on'] = $dt->format("Y-m-d H:i:s");
                                            if ($this->ScheduleMessage->save($schedule_message_arr)) {
                                                $scheduleMessageid = $this->ScheduleMessage->id;
                                                $this->SingleScheduleMessage = new SingleScheduleMessage();
                                                $schedulemessage_contact_arr['SingleScheduleMessage']['id'] = '';
                                                $schedulemessage_contact_arr['SingleScheduleMessage']['contact_id'] = $contact_arr['Contact']['id'];
                                                $schedulemessage_contact_arr['SingleScheduleMessage']['schedule_sms_id'] = $scheduleMessageid;
                                                $this->SingleScheduleMessage->save($schedulemessage_contact_arr);
                                            }
                                        }
                                        $json_arr['status'] = '0';
                                        $json_arr['msg'] = 'Successful API call';
                                    } else {
                                        if ($this->ScheduleMessage->save($schedule_message_arr)) {
                                            $schedule_sms_id = $this->ScheduleMessage->id;
                                            if ($schedule_sms_id > 0) {
                                                app::import('Model', 'SingleScheduleMessage');
                                                $this->SingleScheduleMessage = new SingleScheduleMessage();
                                                $schedulemessage_contact_arr['SingleScheduleMessage']['id'] = '';
                                                $schedulemessage_contact_arr['SingleScheduleMessage']['contact_id'] = $contact_arr['Contact']['id'];
                                                $schedulemessage_contact_arr['SingleScheduleMessage']['schedule_sms_id'] = $schedule_sms_id;
                                                if ($this->SingleScheduleMessage->save($schedulemessage_contact_arr)) {
                                                    $json_arr['status'] = '0';
                                                    $json_arr['msg'] = 'Successful API call';
                                                }
                                            } else {
                                                $json_arr['status'] = '-16';
                                                $json_arr['msg'] = 'Other error.';
                                            }
                                        } else {
                                            $json_arr['status'] = '-16';
                                            $json_arr['msg'] = 'Other error.';
                                        }
                                    }
                                } else {
                                    $json_arr['status'] = '-12';
                                    $json_arr['msg'] = 'Not enough credits to send the message.';
                                }
                            }
                        } else {
                            $json_arr['status'] = '-3';
                            $json_arr['msg'] = "The from number passed in doesn't exist in your account or isn't SMS-enabled.";
                        }
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in or account does not have access to send sms';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode(array($json_arr));
    }

    function getcontacts()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                if (!empty($users)) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $name = '';
                    if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
                        $name = trim($_REQUEST['name']);
                    }
                    $email = '';
                    if (isset($_REQUEST['email']) && $_REQUEST['email'] != '') {
                        $email = trim($_REQUEST['email']);
                    }
                    $number = '';
                    if (isset($_REQUEST['number']) && $_REQUEST['number'] != '') {
                        $number = trim($_REQUEST['number']);
                    }
                    $group = '';
                    if (isset($_REQUEST['group']) && $_REQUEST['group'] != '') {
                        $group = trim($_REQUEST['group']);
                    }
                    $source = '';
                    if (isset($_REQUEST['source']) && $_REQUEST['source'] != '') {
                        $source = trim($_REQUEST['source']);
                    }
                    $subscribed = '';
                    if (isset($_REQUEST['subscribed']) && $_REQUEST['subscribed'] != '') {
                        $subscribed = trim($_REQUEST['subscribed']);
                    }
                    $sortby = 'created';
                    if (isset($_REQUEST['sortby']) && $_REQUEST['sortby'] != '') {
                        $sortby = trim($_REQUEST['sortby']);
                    }
                    $sortdir = 'desc';
                    if (isset($_REQUEST['sortdir']) && $_REQUEST['sortdir'] != '') {
                        $sortdir = trim($_REQUEST['sortdir']);
                    }
                    $limit = 50;
                    if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != '') {
                        if (is_numeric($_REQUEST['limit'])) {
                            $limit = trim($_REQUEST['limit']);
                        }
                    }
                    $page = 1;
                    if (isset($_REQUEST['page']) && $_REQUEST['page'] != '') {
                        $page = trim($_REQUEST['page']);
                    }
                    if (($sortby != '') && (!in_array($sortby, array('name', 'phone_number', 'created')))) {
                        $json_arr['status'] = '-5';
                        $json_arr['msg'] = "The sortby passed in is invalid. Proper values are name, phone_number, created.";
                    } else if (($sortdir != '') && (!in_array($sortdir, array('asc', 'desc')))) {
                        $json_arr['status'] = '-6';
                        $json_arr['msg'] = "The sortdir passed in is invalid. Proper values are asc, desc.";
                    } else if (($source != '') && (!in_array($source, array(0, 1, 2, 3)))) {
                        $json_arr['status'] = '-3';
                        $json_arr['msg'] = "The source passed in is invalid. Proper values are 0, 1, 2, 3.";
                    } else if (($subscribed != '') && (!in_array($subscribed, array(0, 1)))) {
                        $json_arr['status'] = '-4';
                        $json_arr['msg'] = "The subscribed passed in is invalid. Proper values are 0, 1.";
                    } else {
                        $group_valid = 1;
                        if ($group != '') {
                            $group_arr = $this->Group->find('first', array('conditions' => array('Group.group_name' => $group, 'Group.user_id' => $users['User']['id']), 'order' => array('Group.id' => 'asc')));
                            if (empty($group_arr)) {
                                $group_valid = 0;
                            }
                        }
                        if ($group_valid == 1) {
                            $conditions['AND'] = array();
                            if ($users['User']['id'] > 0) {
                                $contact_user_id = array('Contact.user_id' => $users['User']['id']);
                                array_push($conditions['AND'], $contact_user_id);
                            }
                            if ($name != '') {
                                $contact_name = array('Contact.name LIKE' => '%' . $name . '%');
                                array_push($conditions['AND'], $contact_name);
                            }
                            if ($email != '') {
                                $contact_email = array('Contact.email LIKE' => '%' . $email . '%');
                                array_push($conditions['AND'], $contact_email);
                            }
                            if ($number != '') {
                                $contact_phone_number = array('Contact.phone_number LIKE' => '%' . $number . '%');
                                array_push($conditions['AND'], $contact_phone_number);
                            }
                            if ($group != '') {
                                $group_name = array('Group.group_name LIKE' => '%' . $group . '%');
                                array_push($conditions['AND'], $group_name);
                            }
                            if ($source != '') {
                                //$subscribed_by_sms = array('ContactGroup.subscribed_by_sms LIKE'=>'%'.$source.'%');
                                $subscribed_by_sms = array('ContactGroup.subscribed_by_sms' => $source);
                                array_push($conditions['AND'], $subscribed_by_sms);
                            }
                            if ($subscribed != '') {
                                //$un_subscribers = array('ContactGroup.un_subscribers LIKE'=>'%'.$subscribed.'%');
                                $un_subscribers = array('ContactGroup.un_subscribers' => $subscribed);
                                array_push($conditions['AND'], $un_subscribers);
                            }
                            if (strtoupper($sortby) == 'CREATED') {
                                $contact_group = $this->ContactGroup->find('all', array('conditions' => $conditions, 'order' => array('ContactGroup.' . $sortby => $sortdir), 'limit' => $limit, 'page' => $page));
                            } else {
                                $contact_group = $this->ContactGroup->find('all', array('conditions' => $conditions, 'order' => array('Contact.' . $sortby => $sortdir), 'limit' => $limit, 'page' => $page));
                            }

                            if (!empty($contact_group)) {
                                $contacts = array();
                                foreach ($contact_group as $contact_groups) {
                                    if ($contact_groups['ContactGroup']['un_subscribers'] == 0) {
                                        $subscribers = "Yes";
                                    } else {
                                        $subscribers = "No";
                                    }
                                    if ($contact_groups['ContactGroup']['subscribed_by_sms'] == 0) {
                                        $subscribed_by_sms = "Import";
                                    } else if ($contact_groups['ContactGroup']['subscribed_by_sms'] == 1) {
                                        $subscribed_by_sms = "SMS";
                                    } elseif ($contact_groups['ContactGroup']['subscribed_by_sms'] == 2) {
                                        $subscribed_by_sms = "Widget";
                                    } else {
                                        $subscribed_by_sms = "Kiosk";
                                    }
                                    $contacts[] = array(
                                        'id' => $contact_groups['Contact']['id'],
                                        'name' => $contact_groups['Contact']['name'],
                                        'birthday' => $contact_groups['Contact']['birthday'],
                                        'email' => $contact_groups['Contact']['email'],
                                        'number' => $contact_groups['Contact']['phone_number'],
                                        'group' => $contact_groups['Group']['group_name'],
                                        'subscriber' => $subscribers,
                                        'source' => $subscribed_by_sms,
                                        'carrier' => $contact_groups['Contact']['carrier'],
                                        'date' => date('Y-m-d H:i:s', strtotime($contact_groups['ContactGroup']['created']))
                                    );
                                }
                                $json_arr['status'] = '0';
                                $json_arr['contacts'] = $contacts;
                            } else {
                                $json_arr['status'] = '-7';
                                $json_arr['msg'] = "No contacts found.";
                            }
                        } else {
                            $json_arr['status'] = '-2';
                            $json_arr['msg'] = "The group passed in is invalid or does not exist.";
                        }
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in.';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in.';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode($json_arr);
    }

    function getbulksmsdeliveryreport()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                if (!empty($users)) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $groupsmsid = '';
                    if (isset($_REQUEST['groupsmsid'])) {
                        $groupsmsid = $_REQUEST['groupsmsid'];
                    }
                    if ($groupsmsid == '') {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'The group SMS ID is missing. You must pass this in to get delivery stats for this group.';
                    } else {
                        $group_sms_blast = $this->GroupSmsBlast->find('first', array('conditions' => array('GroupSmsBlast.id' => $groupsmsid, 'GroupSmsBlast.user_id' => $users['User']['id'])));
                        if (!empty($group_sms_blast)) {
                            $json_arr = array(
                                'successful' => $group_sms_blast['GroupSmsBlast']['total_successful_messages'],
                                'failed' => $group_sms_blast['GroupSmsBlast']['total_failed_messages']
                            );
                        } else {
                            $json_arr['status'] = '-3';
                            $json_arr['msg'] = 'The group SMS ID is invalid or does not exist.';
                        }
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in.';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in.';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode($json_arr);
    }

    function getcontactsmsdeliveryreport()
    {
        $this->autoRender = false;
        $json_arr = array();
        if (NUMACCOUNTS >= 30) {
            if ((isset($_REQUEST['apikey'])) && ($_REQUEST['apikey'] != '')) {
                $users = $this->User->find('first', array('conditions' => array('User.apikey' => $_REQUEST['apikey'], 'User.active' => 1), 'order' => array('User.id' => 'asc')));
                if (!empty($users)) {
                    if ($users['User']['timezone'] != '') {
                        date_default_timezone_set($users['User']['timezone']);
                    }
                    $number = '';
                    if (isset($_REQUEST['number'])) {
                        $number = $_REQUEST['number'];
                    }
                    if ($number == '') {
                        $json_arr['status'] = '-2';
                        $json_arr['msg'] = 'Number passed in is blank.';
                    } else {
                        $log_arr = $this->Log->find('first', array('conditions' => array('Log.user_id' => $users['User']['id'], 'Log.route' => 'outbox', 'Log.msg_type' => 'text', 'Log.phone_number' => $number), 'order' => array('Log.id' => 'desc')));
                        if (!empty($log_arr)) {
                            $json_arr = array(
                                'smsstatus' => $log_arr['Log']['sms_status'],
                                'errormsg' => $log_arr['Log']['error_message']
                            );
                        } else {
                            $json_arr['status'] = '-3';
                            $json_arr['msg'] = "Number passed in is invalid or can't be found for outbound SMS sent to this number.";
                        }
                    }
                } else {
                    $json_arr['status'] = '-1';
                    $json_arr['msg'] = 'Invalid API key passed in.';
                }
            } else {
                $json_arr['status'] = '-1';
                $json_arr['msg'] = 'Invalid API key passed in.';
            }
        } else {
            $json_arr['status'] = '-20';
            $json_arr['msg'] = 'Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.';
        }
        echo json_encode($json_arr);
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
}