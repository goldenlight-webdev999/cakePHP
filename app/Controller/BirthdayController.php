<?php

class BirthdayController extends AppController
{
    var $name = 'Birthday';
    var $components = array('Twilio', 'Qr', 'Qrsms');

    //var $layout="default";
    function index()
    {
        $this->layout = 'admin_new_layout';
        $this->Birthday->recursive = 0;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.mms' => 1)));
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('users', $users);
        app::import('Model', 'Group');
        $this->Group = new Group();
        $this->paginate = array('conditions' => array('Birthday.user_id' => $user_id), 'order' => array('Group.group_name' => 'ASC', 'Birthday.days' => 'ASC'), 'paramType' => 'querystring');
        $data = $this->paginate('Birthday');
        $this->set('birthdays', $data);
    }

    function add()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Birthday');
        $this->Birthday = new Birthday();
        $Birthdaylist = $this->Birthday->find('all', array('conditions' => array('Birthday.user_id' => $user_id), 'order' => array('Birthday.name' => 'asc')));
        $group_arr_id = array();
        if (!empty($Birthdaylist)) {
            foreach ($Birthdaylist as $birthlist) {
                $group_arr_id[] = $birthlist['Birthday']['group_id'];
            }

        }
        app::import('Model', 'Group');
        $this->Group = new Group();

        $Group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id, 'Not' => array("Group.id" => $group_arr_id)), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $Group);

        app::import('Model', 'Smstemplate');
        $this->Smstemplate = new Smstemplate();
        $Smstemplate = $this->Smstemplate->find('list', array('conditions' => array('Smstemplate.user_id' => $user_id), 'fields' => 'Smstemplate.messagename', 'order' => array('Smstemplate.messagename' => 'asc')));
        $this->set('Smstemplate', $Smstemplate);
        app::import('Model', 'MobilePage');
        $this->MobilePage = new MobilePage();
        $mobilespage = $this->MobilePage->find('list', array('conditions' => array('MobilePage.user_id' => $user_id), 'fields' => 'MobilePage.title', 'order' => array('MobilePage.title' => 'asc')));
        $this->set('mobilespages', $mobilespage);
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('users', $users);
        if (!empty($this->request->data)) {
            app::import('Model', 'Birthday');
            $this->Birthday = new Birthday();
            $this->request->data['Birthday']['user_id'] = $user_id;
            $this->request->data['Birthday']['created'] = date('Y-m-d h:i:s');
            $this->request->data['Birthday']['group_id'] = $this->request->data['Birthday']['group_id'];
            $this->request->data['Birthday']['name'] = $this->request->data['Birthday']['name'];
            if (!empty($this->request->data['Birthday']['msg_type'])) {
                $this->request->data['Birthday']['sms_type'] = $this->request->data['Birthday']['msg_type'];
            } else {
                $this->request->data['Birthday']['sms_type'] = 1;
            }
            if ($this->request->data['Birthday']['message'] != '') {
                $this->request->data['Birthday']['message'] = str_replace("\r\n", "\n", $this->request->data['Birthday']['message']);
            }
            if ($this->request->data['Birthday']['message1'] != '') {
                $this->request->data['Birthday']['message'] = str_replace("\r\n", "\n", $this->request->data['Birthday']['message1']);
            }
            $this->request->data['Birthday']['systemmsg'] = $this->request->data['Birthday']['systemmsg'];
            $this->request->data['Birthday']['days'] = $this->request->data['Birthday']['days'];
            if ($this->request->data['Message']['pick_file'] != '') {
                $this->request->data['Birthday']['image_url'] = $this->request->data['Message']['pick_file'];
            } else if ($this->request->data['Message']['image'][0]['name'] != '') {
                $image_arr = '';
                foreach ($this->request->data['Message']['image'] as $value) {
                    $image = str_replace(' ', '_', mt_rand() . $value["name"]);
                    move_uploaded_file($value['tmp_name'], "mms/" . $image);
                    if ($image_arr != '') {
                        $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                    } else {
                        $image_arr = SITE_URL . '/mms/' . $image;
                    }
                }
                $this->request->data['Birthday']['image_url'] = $image_arr;
            }
            $this->Birthday->save($this->request->data);
            $this->Session->setFlash(__('The Birthday SMS wish has been saved', true));
            $this->redirect(array('action' => 'index'));
        }
    }


    function delete($id = null, $type = null)
    {
        $this->autoRender = false;
        app::import('Model', 'Birthday');
        $this->Birthday = new Birthday();
        if ($this->Birthday->delete($id)) {
            if ($type == 1) {
                $this->redirect(array('action' => 'mms'));
                $this->Session->setFlash(__('Birthday SMS wish has been deleted', true));
            } else if ($type == 0) {
                $this->redirect(array('action' => 'index'));
                $this->Session->setFlash(__('Birthday SMS wish has been deleted', true));
            }
        } else {
            $this->Session->setFlash(__('Birthday SMS wish not deleted', true));
        }
        $this->redirect(array('action' => 'index'));
    }

    function edit($id = null)
    {
        $this->layout = 'admin_new_layout';
        $this->set('id', $id);
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
        $numbers_mms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.mms' => 1)));
        $this->set('numbers_mms', $numbers_mms);
        $this->set('numbers_sms', $numbers_sms);
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $birthday_arr = $this->Birthday->find('first', array('conditions' => array('Birthday.id' => $id)));
        $this->set('users', $users);
        $this->set('birthday', $birthday_arr);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid Birthday', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $this->request->data['Birthday']['id'] = $this->request->data['Birthday']['id'];
            $this->request->data['Birthday']['group_id'] = $this->request->data['Birthday']['group_id'];
            $this->request->data['Birthday']['name'] = $this->request->data['Birthday']['name'];
            if (!empty($this->request->data['Birthday']['msg_type'])) {
                $this->request->data['Birthday']['sms_type'] = $this->request->data['Birthday']['msg_type'];
            } else {
                $this->request->data['Birthday']['sms_type'] = 1;
            }
            if ($this->request->data['Birthday']['msg_type'] == 1) {
                $this->request->data['Birthday']['message'] = str_replace("\r\n", "\n", $this->request->data['Birthday']['message']);
            }
            if ($this->request->data['Birthday']['msg_type'] == 2) {
                $this->request->data['Birthday']['message'] = str_replace("\r\n", "\n", $this->request->data['Birthday']['message1']);
            }
            $this->request->data['Birthday']['systemmsg'] = $this->request->data['Birthday']['systemmsg'];
            $this->request->data['Birthday']['days'] = $this->request->data['Birthday']['days'];
            if ($this->request->data['Birthday']['check_img_validation'] == 0) {
                if ($this->request->data['Message']['pick_file'] != '') {
                    $this->request->data['Birthday']['image_url'] = $this->request->data['Message']['pick_file'];
                } else {
                    $this->request->data['Birthday']['image_url'] = $this->request->data['Message']['pick_file_old'];
                }
            } else {
                if ($this->request->data['Message']['image'][0]['name'] != '') {
                    $image_arr = '';
                    foreach ($this->request->data['Message']['image'] as $value) {
                        $image = str_replace(' ', '_', mt_rand() . $value["name"]);
                        move_uploaded_file($value['tmp_name'], "mms/" . $image);
                        if ($image_arr != '') {
                            $image_arr = $image_arr . ',' . SITE_URL . '/mms/' . $image;
                        } else {
                            $image_arr = SITE_URL . '/mms/' . $image;
                        }
                    }
                    $this->request->data['Birthday']['image_url'] = $image_arr;
                } else {
                    $this->request->data['Birthday']['image_url'] = $this->request->data['Message']['mms_image'];
                }
            }
            if ($this->Birthday->save($this->request->data)) {
                $this->Session->setFlash(__('The Birthday SMS wish has been edited', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Birthday SMS wish could not be edited. Please, try again.', true));
            }
        }
        app::import('Model', 'Smstemplate');
        $this->Smstemplate = new Smstemplate();
        $Smstemplate = $this->Smstemplate->find('list', array('conditions' => array('Smstemplate.user_id' => $user_id), 'fields' => 'Smstemplate.messagename', 'order' => array('Smstemplate.messagename' => 'asc')));
        $this->set('Smstemplate', $Smstemplate);
        app::import('Model', 'MobilePage');
        $this->MobilePage = new MobilePage();
        $mobilespage = $this->MobilePage->find('list', array('conditions' => array('MobilePage.user_id' => $user_id), 'fields' => 'MobilePage.title', 'order' => array('MobilePage.title' => 'asc')));
        $this->set('mobilespages', $mobilespage);
        if (empty($this->request->data)) {
            $this->request->data = $this->Birthday->read(null, $id);
        }
        app::import('Model', 'Birthday');
        $this->Birthday = new Birthday();
        $Birthdaylist = $this->Birthday->find('all', array('conditions' => array('Birthday.user_id' => $user_id, 'Birthday.id !=' => $id), 'order' => array('Birthday.name' => 'asc')));
        $group_arr_id = array();
        if (!empty($Birthdaylist)) {
            foreach ($Birthdaylist as $birthlist) {
                $group_arr_id[] = $birthlist['Birthday']['group_id'];
            }

        }
        app::import('Model', 'Group');
        $this->Group = new Group();
        $Group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id, 'Not' => array("Group.id" => $group_arr_id)), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('Group', $Group);
    }

}

?>