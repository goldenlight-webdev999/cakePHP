<?php

class SchedulersController extends AppController
{
    var $name = 'Schedulers';
    var $components = array('Cookie', 'Twilio', 'Mms', 'Nexmomessage', 'Facebook', 'Slooce', 'Plivo');
    public $uses = array('User', 'UserNumber', 'Contact', 'Log', 'Group', 'GroupSmsBlast', 'ContactGroup', 'Smstemplate', 'ScheduleMessage');
    public $useModel = false;

    function view()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ScheduleMessage');
        $this->ScheduleMessage = new ScheduleMessage();
        app::import('Model', 'ScheduleMessageGroup');
        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
        app::import('Model', 'SingleScheduleMessage');
        $this->SingleScheduleMessage = new SingleScheduleMessage();
        $singlemessage = $this->SingleScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.user_id' => $user_id)));
        $this->set('singlemessage', $singlemessage);
        $message = $this->ScheduleMessageGroup->find('all', array('conditions' => array('ScheduleMessage.user_id' => $user_id)));
        $this->set('message', $message);
    }


    function get_events()
    {
        $this->autoRender = false;
        app::import('Model', 'ScheduleMessage');
        $this->ScheduleMessage = new ScheduleMessage();
        $messages = $this->ScheduleMessage->find('all', array('conditions' => array('DATE(ScheduleMessage.send_on) >=' => $_REQUEST['start'], 'DATE(ScheduleMessage.send_on) <=' => $_REQUEST['end'], 'ScheduleMessage.user_id' => $this->Session->read('User.id'))));
        $json_arr = array();
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $json_arr[] = array(
                    'event_id' => $message['ScheduleMessage']['id'],
                    'title' => $message['ScheduleMessage']['message'],
                    'start' => $message['ScheduleMessage']['send_on'],
                    'url' => SITE_URL . '/schedulers/editevent/' . $message['ScheduleMessage']['id'],
                    'viewevent' => SITE_URL . '/schedulers/viewevent/' . $message['ScheduleMessage']['id'],
                );
            }
        }
        echo json_encode($json_arr);
    }

    function events_view_cal($id = null)
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();
        $this->User->recursive = -1;
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('users', $users);
        app::import('Model', 'SingleScheduleMessage');
        $this->SingleScheduleMessage = new SingleScheduleMessage();
        $ScheduleMessage = $this->SingleScheduleMessage->find('first', array('conditions' => array('ScheduleMessage.id' => $id, 'ScheduleMessage.user_id' => $user_id)));
        $this->set('ScheduleMessage', $ScheduleMessage);

        app::import('Model', 'ScheduleMessageGroup');
        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
        $ScheduleMessageGroup = $this->ScheduleMessageGroup->find('first', array('conditions' => array('ScheduleMessage.id' => $id, 'ScheduleMessage.user_id' => $user_id)));
        $this->set('ScheduleMessageGroup', $ScheduleMessageGroup);


    }

    function events_edit_pop($id = null, $recurring_id = 0)
    {
        $this->layout = 'popup';
        $this->set('id', $id);
        $this->set('recurring_id', $recurring_id);
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'MobilePage');
        $this->MobilePage = new MobilePage();
        $mobilespage = $this->MobilePage->find('list', array('conditions' => array('MobilePage.user_id' => $user_id), 'fields' => 'MobilePage.title', 'order' => array('MobilePage.title' => 'asc')));
        $this->set('mobilespages', $mobilespage);
        if (!empty($this->request->data)) {

            if ($this->request->data['Message']['new_image'][0]['name'] != '') {
                $counter = sizeof($this->request->data['Message']['new_image']);
                if ($counter > 10) {
                    $this->Session->setFlash(__('You can not upload more than 10 images', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'events_edit_pop/' . $this->request->data['Message']['id']));

                }
            }
            app::import('Model', 'ScheduleMessage');
            $this->ScheduleMessage = new ScheduleMessage();
            $this->request->data['ScheduleMessage']['id'] = $this->request->data['Message']['id'];
            $this->request->data['ScheduleMessage']['user_id'] = $user_id;
            //$this->request->data['ScheduleMessage']['send_on'] =date('Y-m-d H:i:s',strtotime($this->request->data['User']['shedule']));
            //$this->request->data['ScheduleMessage']['send_on'] =date('Y-m-d',strtotime($this->request->data['User']['shedule']));
            if ($this->request->data['Message']['msg_type'] == 2) {
                $image_arr = '';
                if ($this->request->data['Message']['new_image'][0]['name'] != '') {
                    foreach ($this->request->data['Message']['new_image'] as $value) {
                        $image = str_replace(' ', '_', $value["name"]);
                        move_uploaded_file($value['tmp_name'], "mms/" . $image);
                        if ($image_arr != '') {
                            $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                        } else {
                            $image_arr = SITE_URL . '/mms/' . $image;

                        }
                    }
                    $this->request->data['ScheduleMessage']['message'] = $image_arr;

                } else {
                    $this->request->data['ScheduleMessage']['message'] = $this->request->data['Message']['image'];
                }
            } else if ($this->request->data['Message']['msg_type'] == 1) {
                $this->request->data['ScheduleMessage']['message'] = str_replace("\r\n", "\n", $this->request->data['Keyword']['message']);
            } else {
                $this->request->data['ScheduleMessage']['message'] = $this->request->data['Keyword']['message'];
            }
            if ($this->request->data['User']['rotate_number'] == '') {
                $this->request->data['ScheduleMessage']['rotate_number'] = 0;
            } else {
                $this->request->data['ScheduleMessage']['rotate_number'] = $this->request->data['User']['rotate_number'];
            }
            if (isset($this->request->data['Message']['msg_type'])) {
                $this->request->data['ScheduleMessage']['msg_type'] = $this->request->data['Message']['msg_type'];
            }
            if (isset($this->request->data['Message']['mms_text'])) {
                $this->request->data['ScheduleMessage']['mms_text'] = str_replace("\r\n", "\n", $this->request->data['Message']['mms_text']);
            }
            if (isset($this->request->data['User']['throttle'])) {
                $this->request->data['ScheduleMessage']['throttle'] = $this->request->data['User']['throttle'];
            } else {
                $this->request->data['ScheduleMessage']['throttle'] = 1;
            }
            
            $this->request->data['ScheduleMessage']['pick_file'] = $this->request->data['Message']['pick_file'];
            $this->request->data['ScheduleMessage']['systemmsg'] = $this->request->data['Message']['systemmsg'];

            if ($recurring_id > 0) {
                $schedulemessages = $this->ScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.recurring_id' => $recurring_id, 'ScheduleMessage.user_id' => $user_id)));

                if (!empty($schedulemessages)) {
                    foreach ($schedulemessages as $schedule_message_group_arr) {
                        $schedule_sms_id = $schedule_message_group_arr['ScheduleMessage']['id'];
                        $this->request->data['ScheduleMessage']['id'] = $schedule_sms_id;
                        $this->ScheduleMessage->save($this->request->data);
                    }
                    $this->Session->setFlash(__('All scheduled messages in the series have been updated', true));
                }
            } else {
                $this->request->data['ScheduleMessage']['send_on'] = date('Y-m-d H:i:s', strtotime($this->request->data['User']['shedule']));
                $this->ScheduleMessage->save($this->request->data);
                $this->Session->setFlash(__('Scheduled message updated', true));
            }

            $scheduleMessageid = $this->ScheduleMessage->id;
            app::import('Model', 'SingleScheduleMessage');
            $this->SingleScheduleMessage = new SingleScheduleMessage();
            $this->redirect(array('controller' => 'schedulers', 'action' => 'events_edit_pop?status=success'));
        } else {
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            app::import('Model', 'User');
            $this->User = new User();
            $this->User->recursive = -1;
            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));

            $this->set('numbers_mms', $numbers_mms);
            $this->set('numbers_sms', $numbers_sms);
            $this->set('users', $users);
            //$Subscribercountfind = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'fields' => array('Contact.id', 'Contact.phone_number')));
            //$this->set('contacts', $Subscribercountfind);
            app::import('Model', 'ScheduleMessage');
            $this->ScheduleMessage = new ScheduleMessage();
            $ScheduleMessage = $this->ScheduleMessage->read(null, $id);
            $this->set('ScheduleMessage', $ScheduleMessage);
            app::import('Model', 'SingleScheduleMessage');
            $this->SingleScheduleMessage = new SingleScheduleMessage();
            $message = $this->SingleScheduleMessage->find('first', array('conditions' => array('ScheduleMessage.user_id' => $user_id, 'ScheduleMessage.id' => $id)));
            $this->set('message', $message);
            //foreach ($message as $message) {
            //    $contactid[$message['Contact']['id']] = $message['SingleScheduleMessage']['contact_id'];
            //}
            $contactid = $message['SingleScheduleMessage']['contact_id'];
            $this->set('contactid', $contactid);
            $Subscribercountfind = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.contact_id' => $message['SingleScheduleMessage']['contact_id']), 'fields' => array('Contact.id', 'Contact.phone_number')));
            $this->set('contacts', $Subscribercountfind);
            
            if (empty($contactid)) {
                app::import('Model', 'Group');
                $this->Group = new Group();
                $Group = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
                $this->set('Group', $Group);
                app::import('Model', 'ScheduleMessageGroup');
                $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                $message = $this->ScheduleMessageGroup->find('all', array('conditions' => array('ScheduleMessage.user_id' => $user_id, 'ScheduleMessage.id' => $id)));
                $this->set('message', $message);
                foreach ($message as $message) {
                    $groupid[$message['Group']['id']] = $message['Group']['group_name'];
                }
                $this->set('groupid', $groupid);
            }
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Smstemplate');
            $this->Smstemplate = new Smstemplate();
            $Smstemplate = $this->Smstemplate->find('list', array('conditions' => array('Smstemplate.user_id' => $user_id), 'fields' => 'Smstemplate.messagename', 'order' => array('Smstemplate.messagename' => 'asc')));
            $this->set('Smstemplate', $Smstemplate);
        }
    }

    function events_edit_group($id = null, $recurring_id = 0)
    {
        $this->layout = 'popup';
        $this->set('id', $id);
        $this->set('recurring_id', $recurring_id);
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'MobilePage');
        $this->MobilePage = new MobilePage();
        $mobilespage = $this->MobilePage->find('list', array('conditions' => array('MobilePage.user_id' => $user_id), 'fields' => 'MobilePage.title', 'order' => array('MobilePage.title' => 'asc')));
        $this->set('mobilespages', $mobilespage);
        if (!empty($this->request->data)) {
            if ($this->request->data['Message']['new_image'][0]['name'] != '') {
                $counter = sizeof($this->request->data['Message']['new_image']);
                if ($counter > 10) {
                    $this->Session->setFlash(__('Please upload 10 images or less', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'events_edit_pop/' . $id));

                }
            }
            app::import('Model', 'ScheduleMessage');
            $this->ScheduleMessage = new ScheduleMessage();
            $this->request->data['ScheduleMessage']['id'] = $id;
            $this->request->data['ScheduleMessage']['user_id'] = $user_id;
            //$this->request->data['ScheduleMessage']['send_on'] =date('Y-m-d H:i:s',strtotime($this->request->data['User']['shedule']));
            //$this->request->data['ScheduleMessage']['send_on'] =date('Y-m-d',strtotime($this->request->data['User']['shedule']));
            if ($this->request->data['Message']['msg_type'] == 2) {
                $image_arr = '';
                if ($this->request->data['Message']['new_image'][0]['name'] != '') {
                    foreach ($this->request->data['Message']['new_image'] as $value) {
                        $image = str_replace(' ', '_', $value["name"]);
                        move_uploaded_file($value['tmp_name'], "mms/" . $image);
                        if ($image_arr != '') {
                            $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                        } else {
                            $image_arr = SITE_URL . '/mms/' . $image;
                        }

                    }
                    $this->request->data['ScheduleMessage']['message'] = $image_arr;
                } else {
                    $this->request->data['ScheduleMessage']['message'] = $this->request->data['Message']['image'];
                }
            } else if ($this->request->data['Message']['msg_type'] == 1) {
                $this->request->data['ScheduleMessage']['message'] = str_replace("\r\n", "\n", $this->request->data['Keyword']['message']);
            } else {
                $this->request->data['ScheduleMessage']['message'] = $this->request->data['Keyword']['message'];
            }
            if ($this->request->data['User']['rotate_number'] == '') {
                $this->request->data['ScheduleMessage']['rotate_number'] = 0;
            } else {
                $this->request->data['ScheduleMessage']['rotate_number'] = $this->request->data['User']['rotate_number'];
            }
            if (isset($this->request->data['Message']['msg_type'])) {
                $this->request->data['ScheduleMessage']['msg_type'] = $this->request->data['Message']['msg_type'];
            }
            if (isset($this->request->data['Message']['mms_text'])) {
                $this->request->data['ScheduleMessage']['mms_text'] = str_replace("\r\n", "\n", $this->request->data['Message']['mms_text']);
            }
            if ($this->request->data['Message']['pick_file'] != '') {
                $this->request->data['ScheduleMessage']['pick_file'] = $this->request->data['Message']['pick_file'];
            } else {
                $this->request->data['ScheduleMessage']['pick_file'] = $this->request->data['Message']['pick_old'];
            }
            if (isset($this->request->data['User']['throttle'])) {
                $this->request->data['ScheduleMessage']['throttle'] = $this->request->data['User']['throttle'];
            } else {
                $this->request->data['ScheduleMessage']['throttle'] = 1;
            }
            $this->request->data['ScheduleMessage']['systemmsg'] = $this->request->data['Message']['systemmsg'];
            
            if ($this->request->data['Message']['city'] == '') {
                $this->request->data['ScheduleMessage']['city'] = '';
            } else {
                $this->request->data['ScheduleMessage']['city'] = $this->request->data['Message']['city'];
            }
            
            if ($this->request->data['Message']['state'] == '') {
                $this->request->data['ScheduleMessage']['state'] = '';
            } else {
                $this->request->data['ScheduleMessage']['state'] = $this->request->data['Message']['state'];
            }
            
            if ($this->request->data['Message']['zip'] == '') {
                $this->request->data['ScheduleMessage']['zip'] = '';
            } else {
                $this->request->data['ScheduleMessage']['zip'] = $this->request->data['Message']['zip'];
            }

            if ($recurring_id > 0) {
                $schedulemessages = $this->ScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.recurring_id' => $recurring_id, 'ScheduleMessage.user_id' => $user_id)));

                if (!empty($schedulemessages)) {
                    foreach ($schedulemessages as $schedule_message_group_arr) {
                        $schedule_sms_id = $schedule_message_group_arr['ScheduleMessage']['id'];
                        $this->request->data['ScheduleMessage']['id'] = $schedule_sms_id;
                        $this->ScheduleMessage->save($this->request->data);
                    }
                    $this->Session->setFlash(__('All scheduled messages in the series have been updated', true));
                }
            } else {
                $this->request->data['ScheduleMessage']['send_on'] = date('Y-m-d H:i:s', strtotime($this->request->data['User']['shedule']));
                $this->ScheduleMessage->save($this->request->data);
                $this->Session->setFlash(__('Scheduled message updated', true));
            }

            $scheduleMessageid = $this->ScheduleMessage->id;
            app::import('Model', 'ScheduleMessageGroup');
            $this->ScheduleMessageGroup = new ScheduleMessageGroup();
            $this->ScheduleMessageGroup->deleteAll(array('ScheduleMessageGroup.schedule_sms_id' => $id));
            foreach ($this->request->data['Keyword']['id'] as $groupIds) {
                $group_id = $groupIds;
                app::import('Model', 'ScheduleMessageGroup');
                $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                app::import('Model', 'ScheduleMessageGroup');
                $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                $this->request->data['ScheduleMessageGroup']['schedule_sms_id'] = $id;
                $this->request->data['ScheduleMessageGroup']['group_id'] = $group_id;
                $this->ScheduleMessageGroup->save($this->request->data);
            }

            $this->redirect(array('controller' => 'schedulers', 'action' => 'events_edit_pop?status=success'));

        } else {
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            app::import('Model', 'User');
            $this->User = new User();
            $this->User->recursive = -1;
            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
            $this->set('numbers_mms', $numbers_mms);
            $this->set('numbers_sms', $numbers_sms);
            $this->set('users', $users);
            app::import('Model', 'Group');
            $this->Group = new Group();
            $Group = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
            $this->set('Group', $Group);
            app::import('Model', 'ScheduleMessage');
            $this->ScheduleMessage = new ScheduleMessage();
            $ScheduleMessage = $this->ScheduleMessage->read(null, $id);
            $this->set('ScheduleMessage', $ScheduleMessage);
            app::import('Model', 'ScheduleMessageGroup');
            $this->ScheduleMessageGroup = new ScheduleMessageGroup();
            $message = $this->ScheduleMessageGroup->find('all', array('conditions' => array('ScheduleMessage.user_id' => $user_id, 'ScheduleMessage.id' => $id)));
            $this->set('message', $message);
            foreach ($message as $message) {
                $groupid[$message['Group']['id']] = $message['Group']['group_name'];
            }
            //print_r($groupid);
            $this->set('groupid', $groupid);
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Smstemplate');
            $this->Smstemplate = new Smstemplate();
            $Smstemplate = $this->Smstemplate->find('list', array('conditions' => array('Smstemplate.user_id' => $user_id), 'fields' => 'Smstemplate.messagename', 'order' => array('Smstemplate.messagename' => 'asc')));
            $this->set('Smstemplate', $Smstemplate);
        }


    }

    function events_delete($id = null)
    {
        $this->autoRender = false;

        app::import('Model', 'ScheduleMessage');
        $this->ScheduleMessage = new ScheduleMessage();
        //$this->layout="default";
        $this->ScheduleMessage->delete($id);
        if ($this->ScheduleMessage->delete($id)) {
            $this->Session->setFlash(__('Scheduled Message Deleted', true));
            $this->redirect(array('action' => 'view/deleted'));
        }
        $this->Session->setFlash(__('Scheduled Message Deleted', true));
        $this->redirect(array('action' => 'view/deleted'));
    }

    function add()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        $this->Session->write('User.sms_balance');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id), 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $Group);
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        //$contactnumber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.id', 'Group.group_name', 'Contact.phone_number'), 'order' => array('Group.group_name' => 'asc', 'Contact.name' => 'asc')));
        if (trim($_REQUEST['contactid']) != ''){
            $contactnumber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.contact_id' => $_REQUEST['contactid']), 'fields' => array('Contact.name', 'Contact.id', 'Group.group_name', 'Contact.phone_number')));
        }else{
            $contactnumber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.un_subscribers' => 0), 'fields' => array('Contact.name', 'Contact.id', 'Group.group_name', 'Contact.phone_number'), 'order' => array('ContactGroup.created' => 'desc'), 'limit' => 950));
        }
        $this->set('contactnumber', $contactnumber);
        app::import('Model', 'Smstemplate');
        $this->Smstemplate = new Smstemplate();
        $Smstemplate = $this->Smstemplate->find('list', array('conditions' => array('Smstemplate.user_id' => $user_id), 'fields' => 'Smstemplate.messagename', 'order' => array('Smstemplate.messagename' => 'asc')));
        $this->set('Smstemplate', $Smstemplate);
        app::import('Model', 'MobilePage');
        $this->MobilePage = new MobilePage();
        $mobilespage = $this->MobilePage->find('list', array('conditions' => array('MobilePage.user_id' => $user_id), 'fields' => 'MobilePage.title', 'order' => array('MobilePage.title' => 'asc')));
        $this->set('mobilespages', $mobilespage);
        app::import('Model', 'User');
        $this->User = new User();
        $this->User->recursive = -1;
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $credits = $users['User']['sms_balance'];
        $this->Session->write('User.sms_balance', $users['User']['sms_balance']);
        $this->Session->write('User.assigned_number', $users['User']['assigned_number']);
        $this->Session->write('User.pay_activation_fees_active', $users['User']['pay_activation_fees_active']);
        $this->Session->write('User.active', $users['User']['active']);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $this->set('users', $users);
        $numbers = array();
        $primary = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.sms' => 1)));
        if (!empty($primary)) {
            $numbers[] = array('nickname' => 'Primary', 'number' => $primary['User']['assigned_number'], 'number_details' => $primary['User']['assigned_number']);
        }
        $UserNumbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
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
        if (!empty($this->request->data)) {
            //echo"<pre>";print_r($this->request->data);die;
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $user_numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $user_numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
            $users_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('users', $users);
            if (isset($this->request->data['Message']['msg_type'])) {
                if ($this->request->data['Message']['msg_type'] == 1) {
                    if (($users_arr['User']['sms'] != 1) && (empty($user_numbers_sms))) {
                        $this->Session->setFlash(__('You do not have any number with SMS capability', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
                    }
                } else if ($this->request->data['Message']['msg_type'] == 2) {
                    if (($users_arr['User']['mms'] != 1) && (empty($user_numbers_mms))) {
                        $this->Session->setFlash(__('You do not have any number with Mms capability', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
                    }
                }
            }
            if (isset($this->request->data['Message']['image'])) {
                if ($this->request->data['Message']['image'][0]['name'] != '') {
                    $counter = sizeof($this->request->data['Message']['image']);
                    if ($counter > 10) {
                        $this->Session->setFlash(__('You can not upload more than 10 images', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
                    }
                }
            }
            app::import('Model', 'Group');
            $this->Group = new Group();
            $Groupname = $this->Group->find('all', array('conditions' => array('Group.user_id' => $user_id)));
            if (empty($Groupname)) {
                $this->Session->setFlash(__('Please create a group before sending a message.', true));
                $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
            }
            if ($this->request->data['pick']['id'] == 1) {
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                /*$Subscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Keyword']['id'])));

                if (empty($Subscriber)) {
                    $this->Session->setFlash(__('Add contacts to this group or select a different group.', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
                }
                $totalsubscribers = count($Subscriber);*/
                
                $groupcount = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.group_id' => $this->request->data['Keyword']['id'])));
                    
                if (empty($groupcount) || $groupcount == 0) {
                    $this->Session->setFlash(__('Add contacts to this group or select a different group.', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'add')); 
                }
                    
                $conditions = array();
                $conditions[] = array( "AND" => array('ContactGroup.group_id' => $this->request->data['Keyword']['id'], 'ContactGroup.un_subscribers' => 0) );
                
                if(trim($this->request->data['Message']['city'])!=''){
                    $conditions[] = array( "AND" => array ("Contact.location"=>trim($this->request->data['Message']['city'])) );
                }
                if(trim($this->request->data['Message']['state'])!=''){
                    $conditions[] = array( "AND" => array ("Contact.state"=>trim($this->request->data['Message']['state'])) );
                }
                if(trim($this->request->data['Message']['zip'])!=''){
                    $conditions[] = array( "AND" => array ("Contact.zip"=>trim($this->request->data['Message']['zip'])) );
                }
  
                $totalsubscribers = $this->ContactGroup->find('count', array('conditions' => $conditions));
                
                $body = str_replace("\r\n", "\n", $this->request->data['Keyword']['message']) . " " . $this->request->data['Message']['systemmsg'];
                $spinbody = $this->process($body);
                $length = mb_strlen($spinbody,"UTF-8");
			    $gsm = $this->is_gsm($spinbody);
                if ($this->request->data['Message']['msg_type'] == 2) {
                    $contactcredits = CHARGECREDITSMMS;
                } else {
                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                        $contactcredits = ceil($length / 70);
                    } else {
                        $contactcredits = ceil($length / 160);
                    }
                }
                if ($credits < ($totalsubscribers * $contactcredits)) {
                    $this->Session->setFlash(__('You do not have enough credits to schedule this message.', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
                }
                if($totalsubscribers > 0){
                    app::import('Model', 'ScheduleMessage');
                    $this->ScheduleMessage = new ScheduleMessage();
                    $this->request->data['ScheduleMessage']['user_id'] = $user_id;
                    $this->request->data['ScheduleMessage']['send_on'] = date('Y-m-d H:i:s', strtotime($this->request->data['User']['shedule']));
                    if ($this->request->data['Message']['image'][0]['name'] != '') {
                        $image_arr = '';
                        foreach ($this->request->data['Message']['image'] as $value) {
                            $image = str_replace(' ', '_', $value["name"]);
                            move_uploaded_file($value['tmp_name'], "mms/" . $image);
                            if ($image_arr != '') {
                                $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                            } else {
                                $image_arr = SITE_URL . '/mms/' . $image;
                            }
                        }
                        $this->request->data['ScheduleMessage']['message'] = $image_arr;
                    } else {
                        $this->request->data['ScheduleMessage']['message'] = str_replace("\r\n", "\n", $this->request->data['Keyword']['message']);
                    }
                    if ($this->request->data['User']['rotate_number'] == '') {
                        $this->request->data['ScheduleMessage']['rotate_number'] = 0;
                    } else {
                        $this->request->data['ScheduleMessage']['rotate_number'] = $this->request->data['User']['rotate_number'];
                    }
                    if (isset($this->request->data['Message']['msg_type'])) {
                        $this->request->data['ScheduleMessage']['msg_type'] = $this->request->data['Message']['msg_type'];
                    } else {
                        $this->request->data['ScheduleMessage']['msg_type'] = 1;
                    }
                    if (isset($this->request->data['Message']['msg'])) {
                        $this->request->data['ScheduleMessage']['mms_text'] = str_replace("\r\n", "\n", $this->request->data['Message']['msg']);
                    } else {
                        $this->request->data['ScheduleMessage']['mms_text'] = '';
                    }
                    if (isset($this->request->data['User']['throttle'])) {
                        $this->request->data['ScheduleMessage']['throttle'] = $this->request->data['User']['throttle'];
                    } else {
                        $this->request->data['ScheduleMessage']['throttle'] = 1;
                    }
                    if ($this->request->data['Message']['alphasenderid'] == '') {
                        $this->request->data['ScheduleMessage']['alphasender'] = 0;
                    } else {
                        $this->request->data['ScheduleMessage']['alphasender'] = $this->request->data['Message']['alphasenderid'];
                    }
                    if (isset($this->request->data['Message']['alphasenderid_input'])) {
                        $this->request->data['ScheduleMessage']['alphasender_input'] = $this->request->data['Message']['alphasenderid_input'];
                    } else {
                        $this->request->data['ScheduleMessage']['alphasender_input'] = '';
                    }
                    if (isset($this->request->data['Message']['sendernumber'])) {
                        $this->request->data['ScheduleMessage']['sendfrom'] = $this->request->data['Message']['sendernumber'];
                    } else {
                        $this->request->data['ScheduleMessage']['sendfrom'] = '';
                    }
                    
                    $this->request->data['ScheduleMessage']['systemmsg'] = $this->request->data['Message']['systemmsg'];
                    $this->request->data['ScheduleMessage']['pick_file'] = $this->request->data['Message']['pick_file'];
                    
                    if ($this->request->data['Message']['city'] == '') {
                        $this->request->data['ScheduleMessage']['city'] = '';
                    } else {
                        $this->request->data['ScheduleMessage']['city'] = $this->request->data['Message']['city'];
                    }
                    
                    if ($this->request->data['Message']['state'] == '') {
                        $this->request->data['ScheduleMessage']['state'] = '';
                    } else {
                        $this->request->data['ScheduleMessage']['state'] = $this->request->data['Message']['state'];
                    }
                    
                    if ($this->request->data['Message']['zip'] == '') {
                        $this->request->data['ScheduleMessage']['zip'] = '';
                    } else {
                        $this->request->data['ScheduleMessage']['zip'] = $this->request->data['Message']['zip'];
                    }
                        
                    if ($this->request->data['ScheduleMessage']['recurring'] == 1) {
                        $frequency = $this->request->data['ScheduleMessage']['frequency'];
                        //$enddate = date('Y-m-d',strtotime($this->request->data['ScheduleMessage']['shedule']));
                        $enddate = date('Y-m-d H:i:s', strtotime($this->request->data['ScheduleMessage']['shedule']));
                        //$current_date = date('Y-m-d');
                        $current_date = date('Y-m-d H:i:s', strtotime($this->request->data['User']['shedule']));
                        $begin = new DateTime($current_date);
                        $end = new DateTime($enddate);
                        $end->modify('+5 minutes');
                        if ($this->request->data['ScheduleMessage']['repeat_type'] == 'Daily') {
                            $interval = DateInterval::createFromDateString("$frequency days");
                            $period = new DatePeriod($begin, $interval, $end);
                        } else if ($this->request->data['ScheduleMessage']['repeat_type'] == 'Weekly') {
                            $interval = DateInterval::createFromDateString("$frequency week");
                            $period = new DatePeriod($begin, $interval, $end);
                        } else if ($this->request->data['ScheduleMessage']['repeat_type'] == 'Monthly') {
                            $interval = DateInterval::createFromDateString("$frequency month");
                            $period = new DatePeriod($begin, $interval, $end);
                        } else {
                            $interval = DateInterval::createFromDateString("$frequency year");
                            $period = new DatePeriod($begin, $interval, $end);
                        }
                        //$this->request->data['ScheduleMessage']['recurring_id']=rand(1, 1000);
                        $Schedule_Message = $this->ScheduleMessage->find('first', array('conditions' => array('ScheduleMessage.user_id' => $user_id), 'fields' => 'ScheduleMessage.recurring_id', 'order' => array('ScheduleMessage.recurring_id' => 'desc')));
                        if (!empty($Schedule_Message['ScheduleMessage']['recurring_id'])) {
                            $this->request->data['ScheduleMessage']['recurring_id'] = $Schedule_Message['ScheduleMessage']['recurring_id'] + 1;
                        } else {
                            $this->request->data['ScheduleMessage']['recurring_id'] = 1;
                        }
                        app::import('Model', 'ScheduleMessageGroup');
                        foreach ($period as $dt) {
                            $this->request->data['ScheduleMessage']['id'] = '';
                            //$this->request->data['ScheduleMessage']['send_on'] = $dt->format("Y-m-d");
                            $this->request->data['ScheduleMessage']['send_on'] = $dt->format("Y-m-d H:i:s");
                            $this->ScheduleMessage->save($this->request->data);
                            $scheduleMessageid = $this->ScheduleMessage->id;
                            foreach ($this->request->data['Keyword']['id'] as $groupIds) {
                                $group_id = $groupIds;
                                $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                                $this->request->data['ScheduleMessageGroup']['group_id'] = $group_id;
                                $this->request->data['ScheduleMessageGroup']['schedule_sms_id'] = $scheduleMessageid;
                                $this->ScheduleMessageGroup->save($this->request->data);
                            }
                        }
                        $this->Session->setFlash(__('Group recurring messages has been scheduled for <b>'.$totalsubscribers.'</b> recipients', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add?status=success'));
                    } else {
                        $this->ScheduleMessage->save($this->request->data);
                        $scheduleMessageid = $this->ScheduleMessage->id;
                        app::import('Model', 'ScheduleMessageGroup');
    
                        foreach ($this->request->data['Keyword']['id'] as $groupIds) {
                            $group_id = $groupIds;
                            $this->ScheduleMessageGroup = new ScheduleMessageGroup();
                            $this->request->data['ScheduleMessageGroup']['group_id'] = $group_id;
                            $this->request->data['ScheduleMessageGroup']['schedule_sms_id'] = $scheduleMessageid;
                            $this->ScheduleMessageGroup->save($this->request->data);
                        }
                        $this->Session->setFlash(__('Group SMS message has been scheduled for <b>'.$totalsubscribers.'</b> recipients', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add?status=success'));
                    }
                }else{
                    $this->Session->setFlash(__('There were no recipients returned that matched location filters entered', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'add?status=success'));
                }
            } else {
                app::import('Model', 'Contact');
                $this->Contact = new Contact();
                $Subscribercontact = $this->Contact->find('all', array('conditions' => array('Contact.id' => $this->request->data['Contact']['phone'])));
                $totalsubscribers = count($Subscribercontact);
                $body = str_replace("\r\n", "\n", $this->request->data['Keyword']['message']) . " " . $this->request->data['Message']['systemmsg'];
                $spinbody = $this->process($body);
                //$length = strlen(utf8_decode(substr($spinbody, 0, 1600)));
                $length = mb_strlen($spinbody,"UTF-8");
			    $gsm = $this->is_gsm($spinbody);
                if ($this->request->data['Message']['msg_type'] == 2) {
                    $contactcredits = CHARGECREDITSMMS;
                } else {
                    if(!$gsm){//if (strlen($spinbody) != strlen(utf8_decode($spinbody))) {
                        $contactcredits = ceil($length / 70);
                    } else {
                        $contactcredits = ceil($length / 160);
                    }
                }
                if ($credits < ($totalsubscribers * $contactcredits)) {
                    $this->Session->setFlash(__('You do not have enough credits to schedule a message to these contacts.', true));
                    $this->redirect(array('controller' => 'schedulers', 'action' => 'add'));
                }
                if (!empty($Subscribercontact)) {
                    app::import('Model', 'ScheduleMessage');
                    $this->ScheduleMessage = new ScheduleMessage();
                    $this->request->data['ScheduleMessage']['user_id'] = $user_id;
                    $this->request->data['ScheduleMessage']['send_on'] = date('Y-m-d H:i:s', strtotime($this->request->data['User']['shedule']));
                    if ($this->request->data['Message']['image'][0]['name'] != '') {
                        $image_arr = '';
                        foreach ($this->request->data['Message']['image'] as $value) {
                            $image = str_replace(' ', '_', $value["name"]);
                            move_uploaded_file($value['tmp_name'], "mms/" . $image);
                            if ($image_arr != '') {
                                $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                            } else {
                                $image_arr = SITE_URL . '/mms/' . $image;
                            }
                        }
                        $this->request->data['ScheduleMessage']['message'] = $image_arr;;
                    } else {
                        $this->request->data['ScheduleMessage']['message'] = str_replace("\r\n", "\n", $this->request->data['Keyword']['message']);
                    }
                    if ($this->request->data['User']['rotate_number'] == '') {
                        $this->request->data['ScheduleMessage']['rotate_number'] = 0;
                    } else {
                        $this->request->data['ScheduleMessage']['rotate_number'] = $this->request->data['User']['rotate_number'];
                    }
                    if (isset($this->request->data['Message']['msg_type'])) {
                        $this->request->data['ScheduleMessage']['msg_type'] = $this->request->data['Message']['msg_type'];
                    } else {
                        $this->request->data['ScheduleMessage']['msg_type'] = 1;
                    }
                    if (isset($this->request->data['Message']['msg'])) {
                        $this->request->data['ScheduleMessage']['mms_text'] = str_replace("\r\n", "\n", $this->request->data['Message']['msg']);
                    } else {
                        $this->request->data['ScheduleMessage']['mms_text'] = '';
                    }
                    if (isset($this->request->data['User']['throttle'])) {
                        $this->request->data['ScheduleMessage']['throttle'] = $this->request->data['User']['throttle'];
                    } else {
                        $this->request->data['ScheduleMessage']['throttle'] = 1;
                    }
                    if ($this->request->data['Message']['alphasenderid'] == '') {
                        $this->request->data['ScheduleMessage']['alphasender'] = 0;
                    } else {
                        $this->request->data['ScheduleMessage']['alphasender'] = $this->request->data['Message']['alphasenderid'];
                    }
                    if (isset($this->request->data['Message']['alphasenderid_input'])) {
                        $this->request->data['ScheduleMessage']['alphasender_input'] = $this->request->data['Message']['alphasenderid_input'];
                    } else {
                        $this->request->data['ScheduleMessage']['alphasender_input'] = '';
                    }
                    if (isset($this->request->data['Message']['sendernumber'])) {
                        $this->request->data['ScheduleMessage']['sendfrom'] = $this->request->data['Message']['sendernumber'];
                    } else {
                        $this->request->data['ScheduleMessage']['sendfrom'] = '';
                    }
                    $this->request->data['ScheduleMessage']['pick_file'] = $this->request->data['Message']['pick_file'];
                    $this->request->data['ScheduleMessage']['systemmsg'] = $this->request->data['Message']['systemmsg'];


                    if ($this->request->data['ScheduleMessage']['recurring'] == 1) {
                        $frequency = $this->request->data['ScheduleMessage']['frequency'];
                        //$enddate = date('Y-m-d',strtotime($this->request->data['ScheduleMessage']['shedule']));
                        $enddate = date('Y-m-d H:i:s', strtotime($this->request->data['ScheduleMessage']['shedule']));
                        //$current_date = date('Y-m-d');
                        $current_date = date('Y-m-d H:i:s', strtotime($this->request->data['User']['shedule']));
                        $begin = new DateTime($current_date);
                        $end = new DateTime($enddate);
                        $end->modify('+5 minutes');
                        if ($this->request->data['ScheduleMessage']['repeat_type'] == 'Daily') {
                            $interval = DateInterval::createFromDateString("$frequency days");
                            $period = new DatePeriod($begin, $interval, $end);
                        } else if ($this->request->data['ScheduleMessage']['repeat_type'] == 'Weekly') {
                            $interval = DateInterval::createFromDateString("$frequency week");
                            $period = new DatePeriod($begin, $interval, $end);
                        } else if ($this->request->data['ScheduleMessage']['repeat_type'] == 'Monthly') {
                            $interval = DateInterval::createFromDateString("$frequency month");
                            $period = new DatePeriod($begin, $interval, $end);
                        } else {
                            $interval = DateInterval::createFromDateString("$frequency year");
                            $period = new DatePeriod($begin, $interval, $end);
                        }
                        $Schedule_Message = $this->ScheduleMessage->find('first', array('conditions' => array('ScheduleMessage.user_id' => $user_id), 'fields' => 'ScheduleMessage.recurring_id', 'order' => array('ScheduleMessage.recurring_id' => 'desc')));
                        if (!empty($Schedule_Message['ScheduleMessage']['recurring_id'])) {
                            $this->request->data['ScheduleMessage']['recurring_id'] = $Schedule_Message['ScheduleMessage']['recurring_id'] + 1;
                        } else {
                            $this->request->data['ScheduleMessage']['recurring_id'] = 1;
                        }

                        app::import('Model', 'SingleScheduleMessage');
                        foreach ($period as $dt) {
                            $this->request->data['ScheduleMessage']['id'] = '';
                            //$this->request->data['ScheduleMessage']['send_on'] = $dt->format("Y-m-d");
                            $this->request->data['ScheduleMessage']['send_on'] = $dt->format("Y-m-d H:i:s");
                            $this->ScheduleMessage->save($this->request->data);
                            $scheduleMessageid = $this->ScheduleMessage->id;
                            foreach ($Subscribercontact as $Subscribercontacts) {
                                $contact_id = $Subscribercontacts['Contact']['id'];
                                $this->SingleScheduleMessage = new SingleScheduleMessage();
                                $this->request->data['SingleScheduleMessage']['contact_id'] = $contact_id;
                                $this->request->data['SingleScheduleMessage']['schedule_sms_id'] = $scheduleMessageid;
                                $this->SingleScheduleMessage->save($this->request->data);
                            }

                        }

                        if (isset($this->request->data['Message']['apptid'])) {
                            app::import('Model', 'Appointment');
                            $this->Appointment = new Appointment();
                            $this->Appointment->id = $this->request->data['Message']['apptid'];

                            if ($this->Appointment->id != '') {
                                $this->Appointment->saveField('scheduled', 1);
                            }

                        }
                        $this->Session->setFlash(__('Contacts recurring messages has been scheduled', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add?status=success'));
                    } else {
                        $this->ScheduleMessage->save($this->request->data);
                        $scheduleMessageid = $this->ScheduleMessage->id;
                        app::import('Model', 'SingleScheduleMessage');

                        foreach ($Subscribercontact as $Subscribercontacts) {
                            $contact_id = $Subscribercontacts['Contact']['id'];
                            $this->SingleScheduleMessage = new SingleScheduleMessage();
                            $this->request->data['SingleScheduleMessage']['contact_id'] = $contact_id;
                            $this->request->data['SingleScheduleMessage']['schedule_sms_id'] = $scheduleMessageid;
                            $this->SingleScheduleMessage->save($this->request->data);
                        }

                        if (isset($this->request->data['Message']['apptid'])) {
                            app::import('Model', 'Appointment');
                            $this->Appointment = new Appointment();
                            $this->Appointment->id = $this->request->data['Message']['apptid'];

                            if ($this->Appointment->id != '') {
                                $this->Appointment->saveField('scheduled', 1);
                            }

                        }

                        $this->Session->setFlash(__('Contacts message has been scheduled', true));
                        $this->redirect(array('controller' => 'schedulers', 'action' => 'add?status=success'));
                    }

                }
            }
        }
    }

    function process($text)
    {
        /*return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );*/
        
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            function ($matches) {
            return $matches[0];
            },
            $text
        );

    }

    function multi_event_delete($recurring_id = null)
    {
        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ScheduleMessageGroup');
        $this->ScheduleMessageGroup = new ScheduleMessageGroup();
        app::import('Model', 'SingleScheduleMessage');
        $this->SingleScheduleMessage = new SingleScheduleMessage();
        $schedule_message_group = $this->ScheduleMessage->find('all', array('conditions' => array('ScheduleMessage.recurring_id' => $recurring_id, 'ScheduleMessage.user_id' => $user_id, 'ScheduleMessage.sent' => 0)));
        $this->set('schedule_message_group', $schedule_message_group);
        if (!empty($schedule_message_group)) {
            foreach ($schedule_message_group as $schedule_message_group_arr) {
                $schedule_sms_id = $schedule_message_group_arr['ScheduleMessage']['id'];
                $this->ScheduleMessageGroup->query("delete from schedule_message_groups where schedule_sms_id='" . $schedule_sms_id . "'");
                $this->SingleScheduleMessage->query("delete from single_schedule_messages where schedule_sms_id='" . $schedule_sms_id . "'");
            }
        }
        $this->ScheduleMessage->deleteAll(array('ScheduleMessage.recurring_id' => $recurring_id, 'ScheduleMessage.user_id' => $user_id, 'ScheduleMessage.sent' => 0));
        $this->Session->setFlash(__('All scheduled messages in the series have been deleted', true));
        $this->redirect(array('action' => 'view/deleted'));
    }
}	