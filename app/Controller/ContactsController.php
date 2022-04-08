<?php
App::import('Vendor', 'mailchimp', array('file' => 'mailchimp/MailChimp.php'));
App::import('Vendor', 'getresponse', array('file' => 'getresponse/GetResponse.php'));
App::import('Vendor', 'activecampaign', array('file' => 'activecampaign/ActiveCampaign.class.php'));
App::import('Vendor', 'aweber', array('file' => 'aweber/aweber_api.php'));
App::import('Vendor', 'mailin', array('file' => 'mailin/Mailin.php'));
App::uses('CakeEmail', 'Network/Email');

class ContactsController extends AppController
{

    var $name = 'Contacts';
    var $components = array('Cookie', 'Slooce');
    
    function index()
    {
        $this->layout = 'admin_new_layout';
        $this->Contact->recursive = 0;
        $user_id = $this->Session->read('User.id');
        /*********************************************/
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $group);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('users', $users);

        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();

        $totsubscribercount = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0)));
        $this->set('totsubscribercount', $totsubscribercount);

        $unsubscribercount = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 1)));
        $this->set('unsubscribercount', $unsubscribercount);

        $Subscribercountsms = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.subscribed_by_sms' => 1)));
        $this->set('Subscribercountsms', $Subscribercountsms);

        $Subscribercountwidget = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.subscribed_by_sms' => 2)));
        $this->set('Subscribercountwidget', $Subscribercountwidget);

        $Subscribercountkiosk = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.subscribed_by_sms' => 3)));
        $this->set('Subscribercountkiosk', $Subscribercountkiosk);

        $Subscribercountimport = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.subscribed_by_sms' => 0)));
        $this->set('Subscribercountimport', $Subscribercountimport);


        if ($this->request->data['Contact']['phone'] == 1) {
            $conditions = array('OR' => array(array(array('Contact.name like' => $this->request->data['Contact']['name'] . '%'), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('Contact.name like' => $this->request->data['Contact']['name'] . '%'), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        } elseif ($this->request->data['Contact']['phone'] == 2) {
            $conditions = array('OR' => array(array(array('Contact.phone_number' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('Contact.phone_number' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        } elseif ($this->request->data['Contact']['phone'] == 3) {
            $conditions = array('OR' => array(array(array('Contact.location' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('Contact.phone_number' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        } elseif ($this->request->data['Contact']['phone'] == 4) {
            $conditions = array('OR' => array(array(array('Contact.state' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('Contact.phone_number' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        } elseif ($this->request->data['Contact']['phone'] == 5) {
            $conditions = array('OR' => array(array(array('Contact.zip' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('Contact.phone_number' => $this->request->data['Contact']['name']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        }
        /***********************************************************************/
        if ($this->request->data['Group']['id'] != 0 && $this->request->data['Contact']['source'] != 4) {
            $conditions = array('OR' => array(array(array('ContactGroup.group_id' => $this->request->data['Group']['id']), array('ContactGroup.subscribed_by_sms' => $this->request->data['Contact']['source']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('ContactGroup.group_id' => $this->request->data['Group']['id']), array('ContactGroup.subscribed_by_sms' => $this->request->data['Contact']['source']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        } else if ($this->request->data['Contact']['source'] != 4) {
            $conditions = array('OR' => array(array(array('ContactGroup.subscribed_by_sms' => $this->request->data['Contact']['source']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('ContactGroup.subscribed_by_sms' => $this->request->data['Contact']['source']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        } else if ($this->request->data['Group']['id'] != 0) {
            $conditions = array('OR' => array(array(array('ContactGroup.group_id' => $this->request->data['Group']['id']), array('ContactGroup.user_id' => $user_id))));
            //$conditionscount = array('OR' => array(array(array('ContactGroup.un_subscribers' => '0'), array('ContactGroup.group_id' => $this->request->data['Group']['id']), array('ContactGroup.user_id' => $user_id))));
            $this->set('contacts', '');
            $this->set('Subscribercount', 0);
        }

        if (!empty($this->request->data)) {
            $this->paginate = array('conditions' => $conditions, 'order' => $order, 'limit' => 50);
            $Subscriber11 = $this->paginate('ContactGroup');
            $this->set('contacts', $Subscriber11);
        } else {
            $this->paginate = array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.created' => 'desc'), 'limit' => 50);
            $data = $this->paginate('ContactGroup');
            $this->set('contacts', $data);

        }

    }

    function add($source = null)
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $contacts_members1 = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        $this->set('groupname', $contacts_members1);
        /*********/
        $countrycode = $this->request->data['Contact']['countrycodetel'];
        $phoneno1 = $this->request->data['Contact']['phone_number'];
        $phone = str_replace('+', '', $phoneno1);
        $phone_number = str_replace('-', '', $phone);
        $phone_number = $countrycode . $phone_number;
        $this->set('source', $source);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('country', $users['User']['user_country']);
        /********/
        if (!empty($this->request->data)) {
            if (API_TYPE == 2) {
                //$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                if (!empty($users)) {
                    $response = $this->Slooce->supported($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $phone_number, $users['User']['keyword']);
                    if ($response == 'supported') {
                        app::import('Model', 'ContactGroup');
                        $this->ContactGroup = new ContactGroup();
                        //$newsubscriber = $this->ContactGroup->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number'])));
                        $newsubscriber = $this->ContactGroup->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $phone_number)));
                        if (empty($newsubscriber)) {
                            $contact['user_id'] = $this->Session->read('User.id');
                            $contact['name'] = $this->request->data['Contact']['name'];
                            $contact['phone_number'] = $phone_number;
                            $contact['countrycodetel'] = $countrycode;
                            $contact['color'] = $this->choosecolor();
                            $contact['email'] = $this->request->data['Contact']['email'];
                            $contact['birthday'] = $this->request->data['Contact']['birthday'];
                            $contact['location'] = $this->request->data['Contact']['city'];
                            $contact['state'] = $this->request->data['Contact']['state'];
                            $contact['zip'] = $this->request->data['Contact']['zip'];
                            $contact['custom1'] = $this->request->data['Contact']['custom1'];
                            $contact['custom2'] = $this->request->data['Contact']['custom2'];
                            $contact['custom3'] = $this->request->data['Contact']['custom3'];
                            $this->Contact->save($contact);
                            $contactArr = $this->Contact->getLastInsertId();

                            if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $contact['email'] != '') {
                                if ($users['User']['email_service'] == 1) { //Mailchimp

                                    $list_id = $users['User']['email_listid'];
                                    $MailChimp = new MailChimp($users['User']['email_apikey']);

                                    if ($contact['name'] != '') {
                                        $fullname = explode(' ', $contact['name']);
                                        $firstname = $fullname[0];

                                        $result = $MailChimp->post("lists/$list_id/members",
                                            array(
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

                                    if (!(int)$contact_sync->success) {
                                        // request failed
                                        $this->Session->setFlash(__('Syncing contact to Active Campaign failed. Error returned: ' . $contact_sync->error, true));
                                        exit();
                                    }

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

                            //$contactArr = 1;
                        } else {
                            $contactArr = $newsubscriber['Contact']['id'];
                        }
                        if ($contactArr != '') {
                            foreach ($this->request->data['Group']['id'] as $groupIds) {
                                $group_id = $groupIds;
                                app::import('Model', 'Group');
                                $this->Group = new Group();
                                $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                                if (empty($subscriber11)) {
                                    $message = $keywordname['Group']['system_message'] . ' ' . $keywordname['Group']['auto_message'];
                                    $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $phone_number, $users['User']['keyword'], $message);
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $contactgroup['user_id'] = $this->Session->read('User.id');
                                    $contactgroup['group_id'] = $group_id;
                                    $contactgroup['group_subscribers'] = $keywordname['Group']['keyword'];
                                    $contactgroup['contact_id'] = $contactArr;
                                    $this->ContactGroup->save($contactgroup);
                                    app::import('Model', 'Group');
                                    $this->Group = new Group();
                                    $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                    $groupArr['id'] = $groups['Group']['id'];
                                    $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                                    $this->Group->save($groupArr);

                                    $update_user['User']['id'] = $users['User']['id'];
                                    $update_user['User']['sms_balance'] = $users['User']['sms_balance'] - 1;
                                    $this->User->save($update_user);
                                    $this->Session->setFlash(__('Contact has been saved', true));
                                } else {
                                    $this->Session->setFlash(__('This contact already exists', true));
                                }
                            }
                        } else {
                            $this->Session->setFlash(__('The contact could not be saved. Please, try again.', true));
                        }
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The specified phone number is not valid', true));
                    }
                } else {
                    $this->Session->setFlash(__('User not found', true));
                }
                if ($this->request->data['Contact']['source'] == 1) {
                    $this->redirect(array('controller' => 'groups', 'action' => 'index'));
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                //$newsubscriber = $this->ContactGroup->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number'])));
                $newsubscriber = $this->ContactGroup->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $phone_number)));
                //$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                if (empty($newsubscriber)) {
                    $contact['user_id'] = $this->Session->read('User.id');
                    $contact['name'] = $this->request->data['Contact']['name'];
                    $contact['phone_number'] = $phone_number;
                    $contact['countrycodetel'] = $countrycode;
                    $contact['color'] = $this->choosecolor();
                    $contact['email'] = $this->request->data['Contact']['email'];
                    $contact['birthday'] = $this->request->data['Contact']['birthday'];
                    $contact['location'] = $this->request->data['Contact']['city'];
                    $contact['state'] = $this->request->data['Contact']['state'];
                    $contact['zip'] = $this->request->data['Contact']['zip'];
                    $contact['custom1'] = $this->request->data['Contact']['custom1'];
                    $contact['custom2'] = $this->request->data['Contact']['custom2'];
                    $contact['custom3'] = $this->request->data['Contact']['custom3'];
                            
                    if((API_TYPE==0 || API_TYPE==5 || API_TYPE==7) && $this->request->data['Contact']['fax_number']!=''){
                        $faxcountrycode = $this->request->data['Contact']['faxcountrycodetel'];
                        $faxphoneno1 = $this->request->data['Contact']['fax_number'];
                        $faxphone = str_replace('+', '', $faxphoneno1);
                        $faxphone_number = str_replace('-', '', $faxphone);
                        $faxphone_number = $faxcountrycode . $faxphone_number;
                        $contact['fax_number'] = $faxphone_number;
                        $contact['faxcountrycodetel'] = $faxcountrycode;
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

                            if (!(int)$contact_sync->success) {
                                // request failed
                                $this->Session->setFlash(__('Syncing contact to Active Campaign failed. Error returned: ' . $contact_sync->error, true));
                                exit();
                            }

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

                } else {
                    $contactArr = $newsubscriber['Contact']['id'];
                }
                if ($contactArr != '') {
                    foreach ($this->request->data['Group']['id'] as $groupIds) {
                        $group_id = $groupIds;
                        app::import('Model', 'Group');
                        $this->Group = new Group();
                        $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
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
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                            $groupArr['id'] = $groups['Group']['id'];
                            $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                            $this->Group->save($groupArr);
                            $this->Session->setFlash(__('Contact has been saved', true));
                        } else {
                            $this->Session->setFlash(__('This contact already exists', true));
                        }
                    }
                } else {
                    $this->Session->setFlash(__('The contact could not be saved. Please, try again.', true));
                }
                if ($this->request->data['Contact']['source'] == 1) {
                    $this->redirect(array('controller' => 'groups', 'action' => 'index'));
                } else if ($this->request->data['Contact']['source'] == 2) {
                    $this->redirect(array('controller' => 'chats', 'action' => 'index'));
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
    }

    function edit($id = null, $source = null)
    {
        $this->layout = 'popup';
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid contact', true));
            $this->redirect(array('action' => 'index'));
        }
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $contacts_groupname = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        $this->set('groupsnames', $contacts_groupname);
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $message = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'Contact.id' => $id)));
        foreach ($message as $message) {
            $groupid[$message['Group']['id']] = $message['Group']['group_name'];
        }
        $this->set('groupid', $groupid);
        $this->set('email', $message['Contact']['email']);
        $this->set('birthday', $message['Contact']['birthday']);
        $this->set('countrycodetel', $message['Contact']['countrycodetel']);
        $this->set('phone', $message['Contact']['phone_number']);
        $this->set('city', $message['Contact']['location']);
        $this->set('zip', $message['Contact']['zip']);
        $this->set('custom1', $message['Contact']['custom1']);
        $this->set('custom2', $message['Contact']['custom2']);
        $this->set('custom3', $message['Contact']['custom3']);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('country', $users['User']['user_country']);

        if (!empty($this->request->data)) {
            if (API_TYPE == 2) {
                //$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                $newsubscriber_details = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number'])));
                if (!empty($users)) {
                    if (empty($newsubscriber_details)) {
                        $response = $this->Slooce->supported($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $this->request->data['Contact']['phone_number'], $users['User']['keyword']);
                    } else {
                        $response = 'supported';
                    }
                    if ($response == 'supported') {
                        app::import('Model', 'ContactGroup');
                        $this->ContactGroup = new ContactGroup();
                        $newsubscriber1 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number'])));
                        if (empty($newsubscriber1)) {
                            //$countrycode = $this->request->data['Contact']['countrycodetel'];
                            $phoneno1 = $this->request->data['Contact']['phone_number'];
                            $phone = str_replace('+', '', $phoneno1);
                            $phone_number = str_replace('-', '', $phone);
                            //$phone_number = $countrycode.$phone_number;
                            $contact['user_id'] = $this->Session->read('User.id');
                            $contact['name'] = trim($this->request->data['Contact']['name']);
                            $contact['phone_number'] = $phone_number;
                            $contact['countrycodetel'] = $countrycode;
                            $contact['email'] = $this->request->data['Contact']['email'];
                            $contact['birthday'] = $this->request->data['Contact']['birthday'];
                            $contact['location'] = $this->request->data['Contact']['city'];
                            $contact['state'] = $this->request->data['Contact']['state'];
                            $contact['zip'] = $this->request->data['Contact']['zip'];
                            $contact['custom1'] = $this->request->data['Contact']['custom1'];
                            $contact['custom2'] = $this->request->data['Contact']['custom2'];
                            $contact['custom3'] = $this->request->data['Contact']['custom3'];
                            $contact['color'] = $this->choosecolor();
                            $contact['id'] = $id;
                            $contact['created'] = date('Y-m-d H:i:s');
                            $this->Contact->save($contact);
                            $contactArr = $this->Contact->id;
                        } else {
                            //$countrycode = $this->request->data['Contact']['countrycodetel'];
                            $phoneno1 = $this->request->data['Contact']['phone_number'];
                            $phone = str_replace('+', '', $phoneno1);
                            $phone_number = str_replace('-', '', $phone);
                            //$phone_number = $countrycode.$phone_number;
                            $contact['name'] = trim($this->request->data['Contact']['name']);
                            $contact['phone_number'] = $phone_number;
                            $contact['countrycodetel'] = $countrycode;
                            $contact['email'] = $this->request->data['Contact']['email'];
                            $contact['birthday'] = $this->request->data['Contact']['birthday'];
                            $contact['location'] = $this->request->data['Contact']['city'];
                            $contact['state'] = $this->request->data['Contact']['state'];
                            $contact['zip'] = $this->request->data['Contact']['zip'];
                            $contact['custom1'] = $this->request->data['Contact']['custom1'];
                            $contact['custom2'] = $this->request->data['Contact']['custom2'];
                            $contact['custom3'] = $this->request->data['Contact']['custom3'];
                            $contact['color'] = $this->choosecolor();
                            $contact['id'] = $id;
                            $contact['created'] = date('Y-m-d H:i:s');
                            $this->Contact->save($contact);
                            $contactArr = $newsubscriber1['Contact']['id'];
                        }
                        if ($contactArr != '') {
                            app::import('Model', 'ContactGroup');
                            $this->ContactGroup = new ContactGroup();
                            $subscriber12 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contactArr)));
                            foreach ($subscriber12 as $groupId) {
                                app::import('Model', 'Group');
                                $this->Group = new Group();
                                $keyword = $this->Group->find('first', array('conditions' => array('Group.id' => $groupId['ContactGroup']['group_id'])));
                                //app::import('Model', 'Group');
                                //$this->Group = new Group();
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
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                                //$this->ContactGroup->deleteAll(array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id));
                                if (empty($subscriber11)) {
                                    $message = $keywordname['Group']['system_message'] . ' ' . $keywordname['Group']['auto_message'];
                                    if ($newsubscriber_details['Contact']['phone_number'] != $this->request->data['Contact']['phone_number']) {
                                        $this->Slooce->mt($users['User']['api_url'], $users['User']['partnerid'], $users['User']['partnerpassword'], $phone_number, $users['User']['keyword'], $message);
                                        $update_user['User']['id'] = $users['User']['id'];
                                        $update_user['User']['sms_balance'] = $users['User']['sms_balance'] - 1;
                                        $this->User->save($update_user);
                                    }
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $this->request->data['ContactGroup']['user_id'] = $this->Session->read('User.id');
                                    $this->request->data['ContactGroup']['group_id'] = $group_id;
                                    $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                                    $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                                    $this->request->data['ContactGroup']['subscribed_by_sms'] = $contactsms['ContactGroup']['subscribed_by_sms'];
                                    $this->request->data['ContactGroup']['un_subscribers'] = $contactsms['ContactGroup']['un_subscribers'];
                                    $this->request->data['ContactGroup']['do_not_call'] = $contactsms['ContactGroup']['do_not_call'];
                                    $this->request->data['ContactGroup']['created'] = $contactsms['ContactGroup']['created'];
                                    $this->ContactGroup->save($this->request->data);
                                    app::import('Model', 'Group');
                                    $this->Group = new Group();
                                    $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                    $groupArr['id'] = $groups['Group']['id'];
                                    $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                                    $this->Group->save($groupArr);
                                    $this->Session->setFlash(__('Contact has been saved', true));
                                }
                            }
                        }
                        if ($source == 1) {
                            $this->redirect(array('controller' => 'groups', 'action' => 'index'));
                        }else if ($source == 2) {  
                            $this->redirect(array('controller' => 'chats', 'action' => 'index'));
                        } else {
                            $this->redirect(array('action' => 'index'));
                        }
                    } else {
                        $this->Session->setFlash(__('The specified phone number is not valid', true));
                    }
                } else {
                    $this->Session->setFlash(__('User not found', true));
                }
                if ($source == 1) {
                    $this->redirect(array('controller' => 'groups', 'action' => 'index'));
                }else if ($source == 2) {  
                    $this->redirect(array('controller' => 'chats', 'action' => 'index'));
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $newsubscriber1 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.user_id' => $user_id, 'Contact.phone_number' => $this->request->data['Contact']['phone_number'])));
                if (empty($newsubscriber1)) {
                    //$countrycode = $this->request->data['Contact']['countrycodetel'];
                    $phoneno1 = $this->request->data['Contact']['phone_number'];
                    $phone = str_replace('+', '', $phoneno1);
                    $phone_number = str_replace('-', '', $phone);
                    if (API_TYPE==0 || API_TYPE==5 || API_TYPE==7){
                        $faxphoneno1 = $this->request->data['Contact']['fax_number'];
                        $faxphone = str_replace('+', '', $faxphoneno1);
                        $faxphone_number = str_replace('-', '', $faxphone);
                        $contact['fax_number'] = $faxphone_number;
                    }
                    //$phone_number = $countrycode.$phone_number;
                    $contact['user_id'] = $this->Session->read('User.id');
                    $contact['name'] = trim($this->request->data['Contact']['name']);
                    $contact['phone_number'] = $phone_number;
                    $contact['countrycodetel'] = $countrycode;
                    $contact['email'] = $this->request->data['Contact']['email'];
                    $contact['birthday'] = $this->request->data['Contact']['birthday'];
                    $contact['location'] = $this->request->data['Contact']['city'];
                    $contact['state'] = $this->request->data['Contact']['state'];
                    $contact['zip'] = $this->request->data['Contact']['zip'];
                    $contact['custom1'] = $this->request->data['Contact']['custom1'];
                    $contact['custom2'] = $this->request->data['Contact']['custom2'];
                    $contact['custom3'] = $this->request->data['Contact']['custom3'];
                    $contact['color'] = $this->choosecolor();
                    $contact['id'] = $id;
                    $contact['created'] = date('Y-m-d H:i:s');
                    if($source==2){
                        $contact['lastmsg'] = date('Y-m-d H:i:s');
                    }
                    $this->Contact->save($contact);
                    $contactArr = $this->Contact->id;
                } else {
                    //$countrycode = $this->request->data['Contact']['countrycodetel'];
                    $phoneno1 = $this->request->data['Contact']['phone_number'];
                    $phone = str_replace('+', '', $phoneno1);
                    $phone_number = str_replace('-', '', $phone);
                    if (API_TYPE==0 || API_TYPE==5 || API_TYPE==7){
                        $faxphoneno1 = $this->request->data['Contact']['fax_number'];
                        $faxphone = str_replace('+', '', $faxphoneno1);
                        $faxphone_number = str_replace('-', '', $faxphone);
                        $contact['fax_number'] = $faxphone_number;
                    }
                    //$phone_number = $countrycode.$phone_number;
                    $contact['name'] = trim($this->request->data['Contact']['name']);
                    $contact['phone_number'] = $phone_number;
                    $contact['countrycodetel'] = $countrycode;
                    $contact['email'] = $this->request->data['Contact']['email'];
                    $contact['birthday'] = $this->request->data['Contact']['birthday'];
                    $contact['location'] = $this->request->data['Contact']['city'];
                    $contact['state'] = $this->request->data['Contact']['state'];
                    $contact['zip'] = $this->request->data['Contact']['zip'];
                    $contact['custom1'] = $this->request->data['Contact']['custom1'];
                    $contact['custom2'] = $this->request->data['Contact']['custom2'];
                    $contact['custom3'] = $this->request->data['Contact']['custom3'];
                    $contact['color'] = $this->choosecolor();
                    $contact['id'] = $id;
                    //$contact['created'] = date('Y-m-d H:i:s');
                    if($source==2){
                        $contact['lastmsg'] = date('Y-m-d H:i:s');
                    }
                    $this->Contact->save($contact);
                    $contactArr = $newsubscriber1['Contact']['id'];
                }
                if ($contactArr != '') {
                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $subscriber12 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $contactArr)));
                    foreach ($subscriber12 as $groupId) {
                        app::import('Model', 'Group');
                        $this->Group = new Group();
                        $keyword = $this->Group->find('first', array('conditions' => array('Group.id' => $groupId['ContactGroup']['group_id'])));
                        //app::import('Model', 'Group');
                        //$this->Group = new Group();
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
                        app::import('Model', 'ContactGroup');
                        $this->ContactGroup = new ContactGroup();
                        $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                        //$this->ContactGroup->deleteAll(array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id));
                        if (empty($subscriber11)) {
                            app::import('Model', 'ContactGroup');
                            $this->ContactGroup = new ContactGroup();
                            $this->request->data['ContactGroup']['user_id'] = $this->Session->read('User.id');
                            $this->request->data['ContactGroup']['group_id'] = $group_id;
                            $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                            $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                            $this->request->data['ContactGroup']['subscribed_by_sms'] = $contactsms['ContactGroup']['subscribed_by_sms'];
                            $this->request->data['ContactGroup']['un_subscribers'] = $contactsms['ContactGroup']['un_subscribers'];
                            $this->request->data['ContactGroup']['do_not_call'] = $contactsms['ContactGroup']['do_not_call'];
                            $this->request->data['ContactGroup']['created'] = $contactsms['ContactGroup']['created'];
                            $this->ContactGroup->save($this->request->data);
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                            $groupArr['id'] = $groups['Group']['id'];
                            $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                            $this->Group->save($groupArr);
                            $this->Session->setFlash(__('Contact has been saved', true));
                        }
                    }
                }
                if ($source == 1) {
                    $this->redirect(array('controller' => 'groups', 'action' => 'index'));
                }else if ($source == 2) {  
                    $this->redirect(array('controller' => 'chats', 'action' => 'index'));
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Contact->read(null, $id);
        }
    }

    function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for contact', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Contact->delete($id)) {
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $contacts_members = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $id)));
            foreach ($contacts_members as $contacts_member) {
                $group_id = $contacts_member['ContactGroup']['group_id'];
                $un_subscribers = $contacts_member['ContactGroup']['un_subscribers'];
                if ($un_subscribers == 0) {
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $Group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    $this->request->data['Group']['id'] = $group_id;
                    $this->request->data['Group']['totalsubscriber'] = $Group['Group']['totalsubscriber'] - 1;
                    $this->Group->save($this->request->data);
                }
            }
            $this->ContactGroup->deleteAll(array('ContactGroup.contact_id' => $id));

            app::import('Model', 'Appointment');
            $this->Appointment = new Appointment();
            $appointment = $this->Appointment->find('first', array('conditions' => array('Appointment.contact_id' => $id)));

            if (!empty($appointment)) {
                $this->Appointment->deleteAll(array('Appointment.contact_id' => $id));
            }

            $this->Session->setFlash(__('Contact deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Contact was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function stop($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for contact', true));
            $this->redirect(array('action' => 'index'));
        }
        //if ($this->Contact->delete($id)) {
        if ($this->Contact->updateAll(array('Contact.un_subscribers' => 1), array('Contact.id' => $id))) {
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $contacts_members = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $id)));
            foreach ($contacts_members as $contacts_member) {
                $group_id = $contacts_member['ContactGroup']['group_id'];
                $un_subscribers = $contacts_member['ContactGroup']['un_subscribers'];
                if ($un_subscribers == 0) {
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $Group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    $this->request->data['Group']['id'] = $group_id;
                    $this->request->data['Group']['totalsubscriber'] = $Group['Group']['totalsubscriber'] - 1;
                    $this->Group->save($this->request->data);
                }
            }
            $this->ContactGroup->updateAll(array('ContactGroup.un_subscribers' => 1), array('ContactGroup.contact_id' => $id));
            
            $user_id = $this->Session->read('User.id');
            $number = $this->Contact->find('first', array('conditions' => array('Contact.id' => $id), 'fields' => array('Contact.phone_number')));
            $beforecontactnumber = $number['Contact']['phone_number'];
            $aftercontactnumber = substr_replace($beforecontactnumber, '****', -4);
            app::import('Model', 'Log');
            $this->Log = new Log();
            $this->Log->updateAll(array('Log.phone_number' => "'$aftercontactnumber'"), array('Log.user_id'=>$user_id, 'Log.phone_number' => $beforecontactnumber));
            
            $this->Session->setFlash(__('Contact has been unsubscribed from all lists', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Contact was not unsubscribed', true));
        $this->redirect(array('action' => 'index'));
    }

    function delete_stickysenders()
    {

        $user_id = $this->Session->read('User.id');

        if ($this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.user_id' => $user_id))) {
            $this->Session->setFlash(__('Sticky senders have been removed from all contacts', true));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('Sticky senders were not removed. Please try again', true));
            $this->redirect(array('action' => 'index'));
        }

    }

    function send_sms($id = null)
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
            //app::import('Model', 'ContactGroup');
            //$this->ContactGroup = new ContactGroup();
            $user_id = $this->Session->read('User.id');
            /*$contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0), 'order' => array('Contact.name' => 'asc'), 'fields' => array('Contact.name', 'Contact.phone_number')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if (trim($contactsvar['Contact']['name']) != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'] . ' (' . $contactsvar['Contact']['phone_number'] . ')';
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
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
    }

    function nexmo_send_sms($id = null)
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
            //app::import('Model', 'ContactGroup');
            //$this->ContactGroup = new ContactGroup();
            //$user_id = $this->Session->read('User.id');
            /*$contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0), 'order' => array('Contact.name' => 'asc'), 'fields' => array('Contact.name', 'Contact.phone_number')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if (trim($contactsvar['Contact']['name']) != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'] . ' (' . $contactsvar['Contact']['phone_number'] . ')';
                }
            }
            $this->set('contacts', $contactnames);*/
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
    }

    function plivo_send_sms($id = null)
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
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0), 'order' => array('Contact.name' => 'asc'), 'fields' => array('Contact.name', 'Contact.phone_number')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if (trim($contactsvar['Contact']['name']) != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'] . ' (' . $contactsvar['Contact']['phone_number'] . ')';
                }
            }
            $this->set('contacts', $contactnames);*/
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
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
            $contactsvars = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0), 'order' => array('Contact.name' => 'asc'), 'fields' => array('Contact.name', 'Contact.phone_number')));
            $contactnames[0] = '';
            foreach ($contactsvars as $contactsvar) {
                if (trim($contactsvar['Contact']['name']) != '') {
                    $contactnames[$contactsvar['Contact']['phone_number']] = $contactsvar['Contact']['name'] . ' (' . $contactsvar['Contact']['phone_number'] . ')';
                }
            }
            $this->set('contacts', $contactnames);*/
            $this->set('nonactiveuser', 0);
        } else {
            $this->set('nonactiveuser', 1);
        }
    }

    function show_next()
    {
        //$this->checkUserSession();
        $this->layout = 'admin_new_layout';
        $filename = $this->request->data['contact']['name'];
        $type = $this->request->data['contact']['type'];
        $tmp_name = $this->request->data['contact']['tmp_name'];
        $name = $this->request->data['contact']['tmp_name'];
        $handle = fopen($name, 'r');
        $header = fgetcsv($handle);
        $ext = substr(strrchr($this->request->data['contact']['name'], '.'), 1);
        if (strtoupper(trim($ext)) == 'CSV') {
        } else {
            $this->Session->setFlash(__('Please only use CSV file type', true));
            $this->redirect(array('controller' => 'contacts', 'action' => 'upload'));
        }
        move_uploaded_file($tmp_name, "csvfile/" . time() . $filename);
        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $this->request->data['User']['id'] = $user_id;
        $this->request->data['User']['file_name'] = time() . $filename;
        $this->User->save($this->request->data);
        fclose($handle);
        //if ($this->request->data['contact']['header'] == 0) {
        //    $this->redirect(array('controller' => 'contacts', 'action' => 'importcontact'));
        //}
        $this->set('header', $header);
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('list', array('fields' => array('Group.group_name'), 'conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.id' => 'asc')));
        $this->set('Group', $Group);
    }

    function importcontact()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('list', array('fields' => array('Group.group_name'), 'conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $Group);
        app::import('Model', 'User');
        $this->User = new User();
        $files = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $file_path = $files['User']['file_name'];
        $path = 'csvfile/' . $file_path;
        $handle = fopen($path, "r");
        if (!empty($this->request->data)) {
            if (API_TYPE == 2) {
                while (($row = fgetcsv($handle)) !== FALSE) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $Contact['user_id'] = $user_id;
                    $var = $row[0];
                    if ($var > 0) {
                        $Contact['name'] = '';
                        $Contact['phone_number'] = $row[0];
                        $Contact['color'] = $this->choosecolor();
                    } else {
                        if (empty($var)) {
                            $Contact['name'] = '';
                        } else {
                            $Contact['name'] = $row[0];
                        }
                        $phone = str_replace('+', '', $row[1]);
                        $row[1] = str_replace('-', '', $phone);
                        $Contact['phone_number'] = $row[1];
                        $Contact['color'] = $this->choosecolor();
                    }
                    $firstSubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $Contact['phone_number'])));
                    if (empty($firstSubscriber)) {
                        $response = $this->Slooce->supported($files['User']['api_url'], $files['User']['partnerid'], $files['User']['partnerpassword'], $Contact['phone_number'], $files['User']['keyword']);
                    } else {
                        $response = 'supported';
                    }
                    if ($response == 'supported') {
                        if (empty($firstSubscriber)) {
                            //$this->Contact->save($Contact);
                            //$contactArr = $this->Contact->getLastInsertId();
                            if($this->Contact->save($Contact)){
                                $contactArr = $this->Contact->getLastInsertId();
                                $contactsaved = true;
                            }else{
                                $contactsaved = false;
                                /*ob_start();
                                print_r($Contact);
                                print_r("------");
                                print_r($contactArr);
                        		$out1 = ob_get_contents();
                        		ob_end_clean();
                        		$file = fopen("debug/import_contacts".time().".txt", "w");
                        		fwrite($file, $out1);
                        		fclose($file);*/
                            }
                            //$contactArr = 0;
                            $phone_number = $Contact['phone_number'];
                        } else {
                            $contactsaved = true;
                            $contactArr = $firstSubscriber['Contact']['id'];
                            $phone_number = $firstSubscriber['Contact']['phone_number'];
                        }
                        
                        if ($Contact['phone_number'] != '' && $contactsaved) {
                            foreach ($this->request->data['Group']['id'] as $group_id) {
                                app::import('Model', 'Group');
                                $this->Group = new Group();
                                $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $unsubscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.un_subscribers' => 1)));
                                if (empty($unsubscriber)) {
                                    $subscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                                    if (empty($subscriber)) {
                                        $message = $keywordname['Group']['system_message'] . ' ' . $keywordname['Group']['auto_message'];
                                        //if($phone_number != $Contact['phone_number']){
                                        $this->Slooce->mt($files['User']['api_url'], $files['User']['partnerid'], $files['User']['partnerpassword'], $Contact['phone_number'], $files['User']['keyword'], $message);

                                        $update_user['User']['id'] = $users['User']['id'];
                                        $update_user['User']['sms_balance'] = $users['User']['sms_balance'] - 1;
                                        $this->User->save($update_user);
                                        //}
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        $this->request->data['ContactGroup']['user_id'] = $user_id;
                                        $this->request->data['ContactGroup']['group_id'] = $group_id;
                                        $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                                        $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                                        $this->ContactGroup->save($this->request->data);
                                        app::import('Model', 'Group');
                                        $this->Group = new Group();
                                        $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                        $groupArr['id'] = $groups['Group']['id'];
                                        $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                                        $this->Group->save($groupArr);
                                        $this->Session->setFlash(__('Contacts have been uploaded', true));
                                    } else {
                                        $this->Session->setFlash(__('This contact already exists for this group', true));
                                    }
                                }
                            }
                        }
                    } else {
                        $this->Session->setFlash(__('The specified phone number is not valid', true));
                    }
                }
            } else {
                //app::import('Model', 'Contact');
                //$this->Contact = new Contact();
                //$firstSubscriber = $this->Contact->find('all', array('conditions' => array('Contact.user_id' => $user_id), 'fields' => 'Contact.id, Contact.phone_number'));
                while (($row = fgetcsv($handle)) !== FALSE) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $Contact['user_id'] = $user_id;
                    $var = $row[0];
                    if ($var > 0) {
                        $Contact['name'] = '';
                        $Contact['phone_number'] = $row[0];
                        $Contact['color'] = $this->choosecolor();
                    } else {
                        if (empty($var)) {
                            $Contact['name'] = '';
                        } else {
                            $Contact['name'] = $row[0];
                        }
                        $phone = str_replace('+', '', $row[1]);
                        $row[1] = str_replace('-', '', $phone);
                        $Contact['phone_number'] = $row[1];
                        $Contact['color'] = $this->choosecolor();
                    }
                    $firstSubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $Contact['phone_number'])));
                    if (empty($firstSubscriber)) {
                    //if (!in_array($Contact['phone_number'], $firstSubscriber)){
                        //$this->Contact->save($Contact);
                        //$contactArr = $this->Contact->getLastInsertId();
                        if($this->Contact->save($Contact)){
                            $contactArr = $this->Contact->getLastInsertId();
                            $contactsaved = true;
                        }else{
                            $contactsaved = false;
                            /*ob_start();
                            print_r($Contact);
                            print_r("------");
                            print_r($contactArr);
                    		$out1 = ob_get_contents();
                    		ob_end_clean();
                    		$file = fopen("debug/import_contacts".time().".txt", "w");
                    		fwrite($file, $out1);
                    		fclose($file);*/
                        }
                    } else {
                        $contactsaved = true;
                        $contactArr = $firstSubscriber['Contact']['id'];
                    }
                    
                    if ($Contact['phone_number'] != '' && $contactsaved) {
                        foreach ($this->request->data['Group']['id'] as $group_id) {
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                            app::import('Model', 'ContactGroup');
                            $this->ContactGroup = new ContactGroup();
                            $unsubscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.un_subscribers' => 1)));
                            if (empty($unsubscriber)) {
                                $subscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                                if (empty($subscriber)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $this->request->data['ContactGroup']['user_id'] = $user_id;
                                    $this->request->data['ContactGroup']['group_id'] = $group_id;
                                    $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                                    $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                                    $this->ContactGroup->save($this->request->data);
                                    //app::import('Model', 'Group');
                                    //$this->Group = new Group();
                                    //$groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                    //$groupArr['id'] = $groups['Group']['id'];
                                    $groupArr['id'] = $group_id;
                                    $groupArr['totalsubscriber'] = $keywordname['Group']['totalsubscriber'] + 1;
                                    $this->Group->save($groupArr);
                                    $this->Session->setFlash(__('Contacts have been uploaded', true));
                                } //else {
                                //    $this->Session->setFlash(__('This contact already exists for this group', true));
                                //}
                            }
                        }
                    }
                }
            }

            $sitename = str_replace(' ', '', SITENAME);
            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $username = $users["User"]["username"];
            $first_name = $users["User"]["first_name"];
            $last_name = $users["User"]["last_name"];
            $email = $users["User"]["email"];
            $phone = $users["User"]["phone"];
            $subject = "Import Contacts Alert - " . SITENAME;
            
            $Email = new CakeEmail();
            if(EMAILSMTP==1){
                $Email->config('smtp');
            }
            $Email->from(array(SUPPORT_EMAIL => SITENAME));
            $Email->to(SUPPORT_EMAIL);
            $Email->subject($subject);
            $Email->template('import_contacts_alert');
            $Email->emailFormat('html');
            $Email->viewVars(array('username' => $username));
            $Email->viewVars(array('firstname' => $first_name));
            $Email->viewVars(array('lastname' => $last_name));
            $Email->viewVars(array('email' => $email));
            $Email->viewVars(array('phone' => $phone));
            $Email->send();
                        
            $this->redirect(array('controller' => 'contacts', 'action' => 'index'));
        }
    }

    function upload()
    {
        $this->layout = 'admin_new_layout';
        //$this->checkUserSession();
        //$this->layout="default";
    }

    function check_csvdata()
    {
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();
        $files = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $file_path = $files['User']['file_name'];
        $path = 'csvfile/' . $file_path;
        $handle = fopen($path, "r");
        $header = fgetcsv($handle);
        if ($this->request->data['Group']['id'] == '' && $this->request->data['Contact']['name'] == '' && $this->request->data['Contact']['name'] == '') {
            $this->Session->setFlash(__('Contact are  not uploaded', true));
            $this->redirect(array('controller' => 'contacts', 'action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if (API_TYPE == 2) {
                while (($row = fgetcsv($handle)) !== FALSE) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $Contact['user_id'] = $user_id;
                    $var = $row[0];
                    if ($var > 0) {
                        $Contact['name'] = '';
                        $Contact['phone_number'] = $row[0];
                        $Contact['color'] = $this->choosecolor();
                    } else {
                        if (empty($var)) {
                            $Contact['name'] = '';
                        } else {
                            $Contact['name'] = $row[0];
                        }
                        $phone = str_replace('+', '', $row[1]);
                        $row[1] = str_replace('-', '', $phone);
                        $Contact['phone_number'] = $row[1];
                        $Contact['color'] = $this->choosecolor();
                    }
                    $firstSubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $Contact['phone_number'])));
                    if (empty($firstSubscriber)) {
                        $response = $this->Slooce->supported($files['User']['api_url'], $files['User']['partnerid'], $files['User']['partnerpassword'], $Contact['phone_number'], $files['User']['keyword']);
                    } else {
                        $response = 'supported';
                    }
                    if ($response == 'supported') {
                        if (empty($firstSubscriber)) {
                            //$this->Contact->save($Contact);
                            if($this->Contact->save($Contact)){
                                $contactArr = $this->Contact->getLastInsertId();
                                $contactsaved = true;
                            }else{
                                $contactsaved = false;
                                /*ob_start();
                                print_r($Contact);
                                print_r("------");
                                print_r($contactArr);
                        		$out1 = ob_get_contents();
                        		ob_end_clean();
                        		$file = fopen("debug/import_contacts".time().".txt", "w");
                        		fwrite($file, $out1);
                        		fclose($file);*/
                            }
                            //$contactArr = $this->Contact->getLastInsertId();
                            $phone_number = $Contact['phone_number'];
                        } else {
                            $contactsaved = true;
                            $contactArr = $firstSubscriber['Contact']['id'];
                            $phone_number = $firstSubscriber['Contact']['phone_number'];
                        }
                        if ($Contact['phone_number'] != ''  && $contactsaved) {
                            foreach ($this->request->data['Group']['id'] as $group_id) {
                                app::import('Model', 'Group');
                                $this->Group = new Group();
                                $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                app::import('Model', 'ContactGroup');
                                $this->ContactGroup = new ContactGroup();
                                $unsubscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.un_subscribers' => 1)));
                                if (empty($unsubscriber)) {
                                    $subscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                                    if (empty($subscriber)) {
                                        $message = $keywordname['Group']['system_message'] . ' ' . $keywordname['Group']['auto_message'];
                                        if ($phone_number != $Contact['phone_number']) {
                                            $this->Slooce->mt($files['User']['api_url'], $files['User']['partnerid'], $files['User']['partnerpassword'], $Contact['phone_number'], $files['User']['keyword'], $message);
                                        }
                                        app::import('Model', 'ContactGroup');
                                        $this->ContactGroup = new ContactGroup();
                                        $this->request->data['ContactGroup']['user_id'] = $user_id;
                                        $this->request->data['ContactGroup']['group_id'] = $group_id;
                                        $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                                        $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                                        $this->ContactGroup->save($this->request->data);
                                        app::import('Model', 'Group');
                                        $this->Group = new Group();
                                        $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                        $groupArr['id'] = $groups['Group']['id'];
                                        $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                                        $this->Group->save($groupArr);
                                        $this->Session->setFlash(__('Contacts have been uploaded', true));
                                    } else {
                                        $this->Session->setFlash(__('This contact already exists for this group', true));
                                    }
                                }
                            }
                        }
                    } else {
                        $this->Session->setFlash(__('The specified phone number is not valid', true));
                    }
                }
            } else {
                while (($row = fgetcsv($handle)) !== FALSE) {
                    app::import('Model', 'Contact');
                    $this->Contact = new Contact();
                    $Contact['user_id'] = $user_id;
                    $var = $row[0];
                    if (empty($var)) {
                        $Contact['name'] = ' ';
                    } else {
                        $Contact['name'] = $row[$this->request->data['Contact']['name']];
                    }
                    $phoneno = $row[$this->request->data['Contact']['phone']];
                    $phone = str_replace('+', '', $phoneno);
                    $phone_number = str_replace('-', '', $phone);
                    $phone_number = str_replace('(', '', $phone_number);
                    $phone_number = str_replace(')', '', $phone_number);
                    $Contact['phone_number'] = $phone_number;
                    
                    if($this->request->data['Contact']['fax']!=''){
                        $faxno = $row[$this->request->data['Contact']['fax']];
                        $faxno = str_replace('+', '', $faxno);
                        $faxno = str_replace('-', '', $faxno);
                        $faxno = str_replace('(', '', $faxno);
                        $faxno = str_replace(')', '', $faxno);
                        $Contact['fax_number'] = $faxno;
                    }
                    if($this->request->data['Contact']['email']!=''){
                        $Contact['email'] = $row[$this->request->data['Contact']['email']];
                    }
                    if($this->request->data['Contact']['birthday']!=''){
                        $Contact['birthday'] = $row[$this->request->data['Contact']['birthday']];
                    }
                    if($this->request->data['Contact']['city']!=''){
                        $Contact['location'] = $row[$this->request->data['Contact']['city']];
                    }
                    if($this->request->data['Contact']['state']!=''){
                        $Contact['state'] = strtoupper($row[$this->request->data['Contact']['state']]);
                    }
                    if($this->request->data['Contact']['zip']!=''){
                        $Contact['zip'] = $row[$this->request->data['Contact']['zip']];
                    }
                    if($this->request->data['Contact']['custom1']!=''){
                        $Contact['custom1'] = $row[$this->request->data['Contact']['custom1']];
                    }
                    if($this->request->data['Contact']['custom2']!=''){
                        $Contact['custom2'] = $row[$this->request->data['Contact']['custom2']];
                    }
                    if($this->request->data['Contact']['custom3']!=''){
                        $Contact['custom3'] = $row[$this->request->data['Contact']['custom3']];
                    }
                    $Contact['color'] = $this->choosecolor();
                    $firstSubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $Contact['phone_number'])));
                    if (empty($firstSubscriber)) {
                        if($this->Contact->save($Contact)){
                            $contactArr = $this->Contact->getLastInsertId();
                            $contactsaved = true;
                        }else{
                            $contactsaved = false;
                            /*ob_start();
                            print_r($Contact);
                            print_r("------");
                            print_r($contactArr);
                    		$out1 = ob_get_contents();
                    		ob_end_clean();
                    		$file = fopen("debug/import_contacts".time().".txt", "w");
                    		fwrite($file, $out1);
                    		fclose($file);*/
                        }
                    } else {
                        $contactsaved = true;
                        $contactArr = $firstSubscriber['Contact']['id'];
                    }
		
                    if ($Contact['phone_number'] != '' && $contactsaved) {
                        foreach ($this->request->data['Group']['id'] as $group_id) {
                            app::import('Model', 'Group');
                            $this->Group = new Group();
                            $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                            app::import('Model', 'ContactGroup');
                            $this->ContactGroup = new ContactGroup();
                            $unsubscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.un_subscribers' => 1)));
                            if (empty($unsubscriber)) {
                                $subscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));
                                if (empty($subscriber)) {
                                    app::import('Model', 'ContactGroup');
                                    $this->ContactGroup = new ContactGroup();
                                    $this->request->data['ContactGroup']['user_id'] = $user_id;
                                    $this->request->data['ContactGroup']['group_id'] = $group_id;
                                    $this->request->data['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                                    $this->request->data['ContactGroup']['contact_id'] = $contactArr;
                                    $this->ContactGroup->save($this->request->data);
                                    //app::import('Model', 'Group');
                                    //$this->Group = new Group();
                                    //$groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                                    //$groupArr['id'] = $groups['Group']['id'];
                                    $groupArr['id'] = $group_id;
                                    $groupArr['totalsubscriber'] = $keywordname['Group']['totalsubscriber'] + 1;
                                    $this->Group->save($groupArr);
                                    $this->Session->setFlash(__('Contacts have been uploaded', true));
                                } //else {
                                //    $this->Session->setFlash(__('This contact already exists for this group', true));
                                //}
                            }
                        }
                    }
                }
            }
            $this->redirect(array('controller' => 'contacts', 'action' => 'index'));
        }
    }

    function export()
    {
        $this->autoRender = false;
        $contacts = $this->Session->read('contacts');
        if (empty($contacts)) {
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.created' => 'desc')));
        }
        $filename = "contacts" . date("Y.m.d") . ".csv";
        $csv_file = fopen('php://output', 'w');
        header('Content-type: application/csv');
        //header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $header_row = array("Subscriber Name", "Email", "Birthday", "Phone", "Fax", "Carrier", "Phone Country", "Line Type", "Group Name", "Sub", "Source", "City", "State/Province", "Postal Code", "Custom 1", "Custom 2", "Custom 3");
        fputcsv($csv_file, $header_row, ',', '"');
        foreach ($contacts as $result) {
            if ($result['ContactGroup']['un_subscribers'] == 0) {
                $type = "YES";
            }elseif ($result['ContactGroup']['un_subscribers'] == 2) {
                $type = "PENDING";
            } else {
                $type = "NO";
            }
            if ($result['ContactGroup']['subscribed_by_sms'] == 0) {
                $subscribed_by_sms = "Import";
            } else if ($result['ContactGroup']['subscribed_by_sms'] == 1) {
                $subscribed_by_sms = "SMS";
            } else if ($result['ContactGroup']['subscribed_by_sms'] == 3) {
                $subscribed_by_sms = "Kiosk";
            } else {
                $subscribed_by_sms = "Widget";
            }
            if ($result['ContactGroup']['un_subscribers'] == 0) {
                $number = $result['Contact']['phone_number'];
            }else{
                $number = substr_replace($result['Contact']['phone_number'], '****', -4);
            }
            $row = array(
                $result['Contact']['name'],
                $result['Contact']['email'],
                $result['Contact']['birthday'],
                $number,
                $result['Contact']['fax_number'],
                $result['Contact']['carrier'],
                $result['Contact']['phone_country'],
                $result['Contact']['line_type'],
                $result['Group']['group_name'],
                $type,
                $subscribed_by_sms,
                $result['Contact']['location'],
                $result['Contact']['state'],
                $result['Contact']['zip'],
                $result['Contact']['custom1'],
                $result['Contact']['custom2'],
                $result['Contact']['custom3']
                );
            fputcsv($csv_file, $row, ',', '"');
        }
        fclose($csv_file);
    }

    function stoppartner($group_id, $contact_id)
    {
        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();
        $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $subscriber = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.contact_id' => $contact_id, 'ContactGroup.group_id' => $group_id)));
        if (!empty($subscriber)) {
            $this->request->data['ContactGroup']['id'] = $subscriber['ContactGroup']['id'];
            $this->request->data['ContactGroup']['un_subscribers'] = 1;
            if ($this->ContactGroup->save($this->request->data)) {
                $response = $this->Slooce->stoppartener($user['User']['api_url'], $user['User']['partnerid'], $user['User']['partnerpassword'], $subscriber['Contact']['phone_number'], $user['User']['keyword']);
                if ($response == 'ok') {
                    $companyname = $user['User']['company_name'];
                    $this->request->data['User']['id'] = $user_id;
                    $this->request->data['User']['sms_balance'] = $user['User']['sms_balance'] - 1;
                    $this->User->save($this->request->data);
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $this->request->data['Group']['id'] = $group_id;
                    $this->request->data['Group']['totalsubscriber'] = $subscriber ['Group']['totalsubscriber'] - 1;
                    $this->Group->save($this->request->data);
                    $userphone = $this->format_phone($user['User']['phone']);
                    if (!empty($companyname)) {
                        $message = $companyname . ": You have opted out successfully. For help call " . $userphone . ". No more messages will be sent";
                    } else {
                        $message = "You have opted out successfully. For help call " . $userphone . ". No more messages will be sent";
                    }
                    $response = $this->Slooce->mt($user['User']['api_url'], $user['User']['partnerid'], $user['User']['partnerpassword'], $subscriber['Contact']['phone_number'], $user['User']['keyword'], $message);
                    $this->smsmail($user['User']['id']);
                    $this->Session->setFlash(__('Contact has been unsubscribed.', true));
                } else {
                    $this->Session->setFlash(__('Contact was not unsubscribed. Please try again.', true));
                }
            } else {
                $this->Session->setFlash(__('Contact was not unsubscribed. Please try again.', true));
            }
        } else {
            $this->Session->setFlash(__('Contact is not found.', true));
        }
        $this->redirect(array('controller' => 'contacts', 'action' => 'index'));
    }

    function smsmail($user_id = null)
    {
        $this->autoRender = false;
        app::import('Model', 'User');
        $this->User = new User();
        $usersmail = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        if ($usersmail['User']['email_alert_credit_options'] == 0) {
            if ($usersmail['User']['sms_balance'] <= $usersmail['User']['low_sms_balances']) {
                if ($usersmail['User']['sms_credit_balance_email_alerts'] == 0) {
                    $sitename = str_replace(' ', '', SITENAME);
                    $username = $usersmail['User']['username'];
                    $email = $usersmail['User']['email'];
                    //echo $phone = $usersmail['User']['assigned_number'];
                    $subject = "Low SMS Credit Balance";
                    /*$this->Email->to = $email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $sitename;
                    $this->Email->template = 'low_sms_credit_template';
                    $this->Email->sendAs = 'html';
                    $this->Email->Controller->set('username', $username);
                    $this->Email->Controller->set('low_sms_balances', $usersmail['User']['low_sms_balances']);
                    $this->Email->send();*/
                    
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($email);
                    $Email->subject($subject);
                    $Email->template('low_sms_credit_template');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('username' => $username));
                    $Email->viewVars(array('low_sms_balances' => $usersmail['User']['low_sms_balances']));
                    $Email->send();
            
                    $this->User->id = $usersmail['User']['id'];
                    $this->User->saveField('sms_credit_balance_email_alerts', 1);
                }
            }
        }
    }

    function format_phone($phone)
    {
        $phone = preg_replace("/[^0-9]/", "", $phone);
        if (strlen($phone) == 7)
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif (strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
        elseif (strlen($phone) == 11)
            return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3-$4", $phone);
        else
            return $phone;
    }

    function unsubscribers()
    {
        $this->layout = 'admin_new_layout';
        $this->Contact->recursive = 0;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $this->paginate = array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 1), 'order' => array('ContactGroup.created' => 'desc'));
        $data = $this->paginate('ContactGroup');
        $this->set('contacts', $data);
        //$Subscribercountfind = $this->ContactGroup->find('all',array('conditions'=>array('ContactGroup.user_id' =>$user_id)));
        //$this->Session->write('contacts', $Subscribercountfind);
    }

    function exportunsubs()
    {
        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 1), 'order' => array('ContactGroup.created' => 'desc')));
        $filename = "unsubscribers" . date("Y.m.d") . ".csv";
        $csv_file = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $header_row = array("Name", "Number", "Group", "Source", "Unsubscribed Date");
        fputcsv($csv_file, $header_row, ',', '"');
        foreach ($contacts as $result) {
            if ($result['ContactGroup']['subscribed_by_sms'] == 0) {
                $subscribed_by_sms = "Import";
            } else if ($result['ContactGroup']['subscribed_by_sms'] == 1) {
                $subscribed_by_sms = "SMS";
            } else if ($result['ContactGroup']['subscribed_by_sms'] == 3) {
                $subscribed_by_sms = "Kiosk";
            } else {
                $subscribed_by_sms = "Widget";
            }
            $row = array(
                $result['Contact']['name'],
                $result['Contact']['phone_number'],
                $result['Group']['group_name'],
                $subscribed_by_sms,
                $result['ContactGroup']['created']);
            fputcsv($csv_file, $row, ',', '"');
        }
        fclose($csv_file);
    }

    function activity_timeline($contact_id, $unsub)
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');

        app::import('Model', 'ActivityTimeline');
        $this->ActivityTimeline = new ActivityTimeline();
        $timeline = $this->ActivityTimeline->find('all', array('conditions' => array('ActivityTimeline.user_id' => $user_id, 'ActivityTimeline.contact_id' => $contact_id), 'order' => array('ActivityTimeline.created' => 'asc')));

        $this->set('timeline', $timeline);
        $this->set('unsub', $unsub);

    }
    
    function send_fax($id = null,$sourcepage = null)
    {
        $this->layout = 'popup';
        $this->set('phoneno', $id);
        $this->set('sourcepage', $sourcepage);
        if (!empty($this->request->data)) {
            $this->Contact->set($this->request->data);
            $this->Contact->validationSet = 'sendMsg';
            if ($this->Contact->validates()) {
                $this->redirect(array('action' => 'index'));
            }
        }
        $userDetails = $this->getLoggedUserDetails();
        $user_id = $this->Session->read('User.id');
        $this->Session->write('User.sms_balance', $userDetails['User']['voice_balance']);
        $this->Session->write('User.assigned_number', $userDetails['User']['assigned_number']);
        $this->Session->write('User.active', $userDetails['User']['active']);
        $this->Session->write('User.getnumbers', $userDetails['User']['getnumbers']);
        $this->Session->write('User.package', $userDetails['User']['package']);
        
        $numbers = array();
        $primary = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.fax' => 1)));
        $this->set('faxnumber', $primary['User']['fax_number']);

        if (!empty($primary)) {
            $numbers[] = array('nickname' => 'Primary', 'number' => $primary['User']['assigned_number'], 'number_details' => $primary['User']['assigned_number']);
        }

        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $UserNumbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.fax' => 1)));
        if (!empty($UserNumbers)) {
            foreach ($UserNumbers as $UserNumber) {
            $numbers[] = array(
                    'nickname' => 'Secondary',
                    'number' => $UserNumber['UserNumber']['number'],
                    'number_details' => $UserNumber['UserNumber']['number'],
            );
            }
        }

        $this->set('numbers', $numbers);
    }
    
    function send_email($email = null)
    {
        $this->layout = 'popup';
        
        $userDetails = $this->getLoggedUserDetails();
        $user_id = $this->Session->read('User.id');
        
        $this->set('email', $email);
        $this->set('fromemail', $userDetails['User']['email']);
        
         if (!empty($this->request->data)) {
             if(trim($this->request->data['contacts']['emailfrom'])==''){
                $this->Session->setFlash(__('Please enter a From email address', true));
                $this->redirect(array('controller' => 'contacts', 'action' => 'send_email/'.$email));
             }else if(trim($this->request->data['contacts']['emailbody'])==''){
                $this->Session->setFlash(__('Please enter an email body', true));
                $this->redirect(array('controller' => 'contacts', 'action' => 'send_email/'.$email));
             }else{
                $toemail = $this->request->data['contacts']['emailto'];
                $ccemail = $this->request->data['contacts']['emailcc'];
                $bccemail = $this->request->data['contacts']['emailbcc'];
                $fromemail = $this->request->data['contacts']['emailfrom'];
                $replyemail = $this->request->data['contacts']['replyto'];
                $subject = $this->request->data['contacts']['emailsubject'];
                $body = $this->request->data['contacts']['emailbody'];
                $readreceipt = $this->request->data['contacts']['readreceipt'];
                
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->sender(SUPPORT_EMAIL);
                $Email->from($fromemail);
                $Email->to($toemail);
                if(trim($ccemail)!=''){
                    $Email->cc($ccemail);
                }
                if(trim($bccemail)!=''){
                    $Email->bcc($bccemail);
                }
                if($readreceipt==1){
                    $Email->readReceipt($fromemail);
                }
                $Email->replyTo($replyemail);
                $Email->subject($subject);
                $Email->returnPath($fromemail);
                $Email->emailFormat('html');
                
                $filename = $this->request->data['contacts']['attachments']['name'];
                $type = $this->request->data['contacts']['attachments']['type'];
                $tmp_name = $this->request->data['contacts']['attachments']['tmp_name'];
                $time = time();
                $filename = str_replace(' ', '_', $filename);
                $tmp_name = str_replace(' ', '_', $tmp_name);
                if (!empty($filename)) {
                    move_uploaded_file($tmp_name, "email/" . $time . $filename);
                    $filepath = "email/". $time . $filename;
                    $Email->attachments($filepath);
                }
                
                try{
                    $Email->send($body);
                }catch (Exception $e) {
                    $this->Session->setFlash("This message was classified as SPAM by the mailer and may not be delivered.");
                    $this->redirect(array('controller' => 'contacts', 'action' => 'send_email/'.$email));
                }
                
                $this->Session->setFlash(__('Email has been sent to '.$toemail, true));
                $this->redirect(array('controller' => 'contacts', 'action' => 'send_email?status=success'));
             }
            
         }

    }


}	