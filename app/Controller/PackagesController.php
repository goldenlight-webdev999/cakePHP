<?php

class PackagesController extends AppController
{
    var $name = 'Packages';
    var $uses = array('Package', 'MonthlyPackage', 'MonthlyNumberPackage');
    
    function admin_index()
    {
        $this->Package->recursive = 0;
        app::import('Model', 'Package');
        $this->Package = new Package();
        $this->set('packages', $this->paginate('Package'));
        
    }

    function admin_view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid package', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('package', $this->Package->read(null, $id));
    }

    function admin_add()
    {
        if (!empty($this->request->data)) {
            $this->Package->create();
            if ($this->Package->save($this->request->data)) {
                $this->Session->setFlash(__('The package has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The package could not be saved. Please, try again.', true));
            }
        }else{
            app::import('Model', 'User');
            $this->User = new User();
            $users = array();
            $allusers = $this->User->find('all', array('conditions' => array('User.active' => 1), 'order' => array('User.username' => 'asc'), 'fields' => array('User.id', 'User.username')));
            
            if (!empty($allusers)) {
                foreach ($allusers as $alluser) {
                   $users[] = array('nickname' => 'Primary', 'userid' => $alluser['User']['id'], 'username' => $alluser['User']['username']);
                }
            }
            
            $this->set('users', $users);
            
        }
    }

    function admin_edit($id = null)
    {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid package', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Package->save($this->request->data)) {
                $this->Session->setFlash(__('The package has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The package could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->request->data)) {
            app::import('Model', 'User');
            $this->User = new User();
            $users = array();
            $allusers = $this->User->find('all', array('conditions' => array('User.active' => 1), 'order' => array('User.username' => 'asc'), 'fields' => array('User.id', 'User.username')));
            
            if (!empty($allusers)) {
                foreach ($allusers as $alluser) {
                   $users[] = array('nickname' => 'Primary', 'userid' => $alluser['User']['id'], 'username' => $alluser['User']['username']);
                }
            }
            
            $this->set('users', $users);
            $this->request->data = $this->Package->read(null, $id);
            $this->set('package', $this->Package->read(null, $id));
        }
    }

    function admin_delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for package', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Package->delete($id)) {
            $this->Session->setFlash(__('Package deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Package was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function admin_monthlypackage()
    {
        $this->MonthlyPackage->recursive = 0;
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $this->set('packagesdata', $this->paginate('MonthlyPackage'));
    }

    function admin_addmonthlypackage()
    {
        if (!empty($this->request->data)) {
            //app::import('Model', 'MonthlyPackage');
            //$this->MonthlyPackage = new MonthlyPackage();
            $this->MonthlyPackage->create();
            /*if (!empty($this->request->data['Package']['product_id'])) {
                $this->request->data['MonthlyPackage']['product_id'] = $this->request->data['Package']['product_id'];
            } else {
                $this->request->data['MonthlyPackage']['product_id'] = ' ';
            }
            $this->request->data['MonthlyPackage']['package_name'] = $this->request->data['Package']['package_name'];
            $this->request->data['MonthlyPackage']['amount'] = $this->request->data['Package']['amount'];
            $this->request->data['MonthlyPackage']['text_messages_credit'] = $this->request->data['Package']['text_messages_credit'];
            $this->request->data['MonthlyPackage']['voice_messages_credit'] = $this->request->data['Package']['voice_messages_credit'];
            $this->request->data['MonthlyPackage']['status'] = $this->request->data['Package']['status'];
            $this->request->data['MonthlyPackage']['user_country'] = $this->request->data['Package']['user_country'];
            $this->request->data['MonthlyPackage']['username'] = $this->request->data['Package']['username'];*/
            if ($this->MonthlyPackage->save($this->request->data)) {
                $this->Session->setFlash(__('The monthly package has been saved', true));
                $this->redirect(array('action' => 'monthlypackage'));
            } else {
                $this->Session->setFlash(__('The monthly package could not be saved. Please, try again.', true));
            }
        }else{
            app::import('Model', 'User');
            $this->User = new User();
            $users = array();
            $allusers = $this->User->find('all', array('conditions' => array('User.active' => 1), 'order' => array('User.username' => 'asc'), 'fields' => array('User.id', 'User.username')));
            
            if (!empty($allusers)) {
                foreach ($allusers as $alluser) {
                   $users[] = array('nickname' => 'Primary', 'userid' => $alluser['User']['id'], 'username' => $alluser['User']['username']);
                }
            }
            
            $this->set('users', $users);
        }
    }

    function admin_editmonthlypackage($id = null)
    {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid monthly package', true));
            $this->redirect(array('action' => 'monthlypackage'));
        }
        if (!empty($this->request->data)) {
            if ($this->MonthlyPackage->save($this->request->data)) {
                $this->Session->setFlash(__('The monthly package has been saved', true));
                $this->redirect(array('action' => 'monthlypackage'));
            } else {
                $this->Session->setFlash(__('The monthly package could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->request->data)) {
            app::import('Model', 'User');
            $this->User = new User();
            $users = array();
            $allusers = $this->User->find('all', array('conditions' => array('User.active' => 1), 'order' => array('User.username' => 'asc'), 'fields' => array('User.id', 'User.username')));
            
            if (!empty($allusers)) {
                foreach ($allusers as $alluser) {
                   $users[] = array('nickname' => 'Primary', 'userid' => $alluser['User']['id'], 'username' => $alluser['User']['username']);
                }
            }
            
            $this->set('users', $users);
            $this->request->data = $this->MonthlyPackage->read(null, $id);
            $this->set('package', $this->MonthlyPackage->read(null, $id));
        }
    }

    function admin_monthlydelete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for monthly package', true));
            $this->redirect(array('action' => 'monthlypackage'));
        }
        if ($this->MonthlyPackage->delete($id)) {
            $this->Session->setFlash(__('Monthly package deleted', true));
            $this->redirect(array('action' => 'monthlypackage'));
        }
        $this->Session->setFlash(__('Monthly package was not deleted', true));
        $this->redirect(array('action' => 'monthlypackage'));
    }

    function admin_monthlynumberpackage()
    {
        $this->MonthlyNumberPackage->recursive = 0;
        app::import('Model', 'MonthlyNumberPackage');
        $this->MonthlyNumberPackage = new MonthlyNumberPackage();
        $this->set('packagesdata', $this->paginate('MonthlyNumberPackage'));
    }

    function admin_addmonthlynumberpackage()
    {
        if (!empty($this->request->data)) {
            //app::import('Model', 'MonthlyNumberPackage');
            //$this->MonthlyNumberPackage = new MonthlyNumberPackage();
            $this->MonthlyNumberPackage->create();
     
            /*if (!empty($this->request->data['Package']['plan'])) {
                $this->request->data['MonthlyNumberPackage']['plan'] = $this->request->data['Package']['plan'];
            } else {
                $this->request->data['MonthlyNumberPackage']['plan'] = ' ';
            }

            $this->request->data['MonthlyNumberPackage']['package_name'] = $this->request->data['Package']['package_name'];
            $this->request->data['MonthlyNumberPackage']['amount'] = $this->request->data['Package']['amount'];
            $this->request->data['MonthlyNumberPackage']['total_secondary_numbers'] = $this->request->data['Package']['total_secondary_numbers'];
            $this->request->data['MonthlyNumberPackage']['status'] = $this->request->data['Package']['status'];
            $this->request->data['MonthlyNumberPackage']['username'] = $this->request->data['Package']['username'];
            $this->request->data['MonthlyNumberPackage']['country'] = $this->request->data['Package']['user_country'];*/
            //$this->request->data['MonthlyNumberPackage']['created']=date('Y-m-d H:i:s');
            if ($this->MonthlyNumberPackage->save($this->request->data)) {
                $this->Session->setFlash(__('The monthly number package has been saved', true));
                $this->redirect(array('action' => 'monthlynumberpackage'));
            } else {
                $this->Session->setFlash(__('The monthly number package could not be saved. Please, try again.', true));
            }
        }else{
            app::import('Model', 'User');
            $this->User = new User();
            $users = array();
            $allusers = $this->User->find('all', array('conditions' => array('User.active' => 1), 'order' => array('User.username' => 'asc'), 'fields' => array('User.id', 'User.username')));
            
            if (!empty($allusers)) {
                foreach ($allusers as $alluser) {
                   $users[] = array('nickname' => 'Primary', 'userid' => $alluser['User']['id'], 'username' => $alluser['User']['username']);
                }
            }
            
            $this->set('users', $users);
        }
    }

    function admin_editmonthlynumberpackage($id = null)
    {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid monthly number package', true));
            $this->redirect(array('action' => 'monthlynumberpackage'));
        }
        if (!empty($this->request->data)) {
            if ($this->MonthlyNumberPackage->save($this->request->data)) {
                $this->Session->setFlash(__('The monthly number package has been saved', true));
                $this->redirect(array('action' => 'monthlynumberpackage'));
            } else {
                $this->Session->setFlash(__('The monthly number package could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->request->data)) {
            app::import('Model', 'User');
            $this->User = new User();
            $users = array();
            $allusers = $this->User->find('all', array('conditions' => array('User.active' => 1), 'order' => array('User.username' => 'asc'), 'fields' => array('User.id', 'User.username')));
            
            if (!empty($allusers)) {
                foreach ($allusers as $alluser) {
                   $users[] = array('nickname' => 'Primary', 'userid' => $alluser['User']['id'], 'username' => $alluser['User']['username']);
                }
            }
            
            $this->set('users', $users);
            $this->request->data = $this->MonthlyNumberPackage->read(null, $id);
            $this->set('package', $this->MonthlyNumberPackage->read(null, $id));
        }
    }

    function admin_monthlynumberdelete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for monthly package', true));
            $this->redirect(array('action' => 'monthlynumberpackage'));
        }
        if ($this->MonthlyNumberPackage->delete($id)) {
            $this->Session->setFlash(__('Monthly number package deleted', true));
            $this->redirect(array('action' => 'monthlynumberpackage'));
        }
        $this->Session->setFlash(__('Monthly number package was not deleted', true));
        $this->redirect(array('action' => 'monthlynumberpackage'));
    }
    
    function admin_packagepermissions($id = null, $submit = null)
    {
        $this->layout = 'popup';
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();

        if (!empty($this->request->data) || $submit == 1) {
            
            $id = base64_decode($id);
            $this->request->data['MonthlyPackage']['id'] = $id;
            
            if ($this->request->data['MonthlyPackage']['autoresponders'] == '') {
                $this->request->data['MonthlyPackage']['autoresponders'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['autoresponders'] = $this->request->data['MonthlyPackage']['autoresponders'];
            }
            if ($this->request->data['MonthlyPackage']['importcontacts'] == '') {
                $this->request->data['MonthlyPackage']['importcontacts'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['importcontacts'] = $this->request->data['MonthlyPackage']['importcontacts'];
            }
            if ($this->request->data['MonthlyPackage']['shortlinks'] == '') {
                $this->request->data['MonthlyPackage']['shortlinks'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['shortlinks'] = $this->request->data['MonthlyPackage']['shortlinks'];
            }
            if ($this->request->data['MonthlyPackage']['voicebroadcast'] == '') {
                $this->request->data['MonthlyPackage']['voicebroadcast'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['voicebroadcast'] = $this->request->data['MonthlyPackage']['voicebroadcast'];
            }
            if ($this->request->data['MonthlyPackage']['polls'] == '') {
                $this->request->data['MonthlyPackage']['polls'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['polls'] = $this->request->data['MonthlyPackage']['polls'];
            }
            if ($this->request->data['MonthlyPackage']['contests'] == '') {
                $this->request->data['MonthlyPackage']['contests'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['contests'] = $this->request->data['MonthlyPackage']['contests'];
            }
            if ($this->request->data['MonthlyPackage']['loyaltyprograms'] == '') {
                $this->request->data['MonthlyPackage']['loyaltyprograms'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['loyaltyprograms'] = $this->request->data['MonthlyPackage']['loyaltyprograms'];
            }
            if ($this->request->data['MonthlyPackage']['kioskbuilder'] == '') {
                $this->request->data['MonthlyPackage']['kioskbuilder'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['kioskbuilder'] = $this->request->data['MonthlyPackage']['kioskbuilder'];
            }
            if ($this->request->data['MonthlyPackage']['birthdaywishes'] == '') {
                $this->request->data['MonthlyPackage']['birthdaywishes'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['birthdaywishes'] = $this->request->data['MonthlyPackage']['birthdaywishes'];
            }
            if ($this->request->data['MonthlyPackage']['mobilepagebuilder'] == '') {
                $this->request->data['MonthlyPackage']['mobilepagebuilder'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['mobilepagebuilder'] = $this->request->data['MonthlyPackage']['mobilepagebuilder'];
            }
            if ($this->request->data['MonthlyPackage']['webwidgets'] == '') {
                $this->request->data['MonthlyPackage']['webwidgets'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['webwidgets'] = $this->request->data['MonthlyPackage']['webwidgets'];
            }
            if ($this->request->data['MonthlyPackage']['qrcodes'] == '') {
                $this->request->data['MonthlyPackage']['qrcodes'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['qrcodes'] = $this->request->data['MonthlyPackage']['qrcodes'];
            }
            if ($this->request->data['MonthlyPackage']['smschat'] == '') {
                $this->request->data['MonthlyPackage']['smschat'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['smschat'] = $this->request->data['MonthlyPackage']['smschat'];
            }
            if ($this->request->data['MonthlyPackage']['calendarscheduler'] == '') {
                $this->request->data['MonthlyPackage']['calendarscheduler'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['calendarscheduler'] = $this->request->data['MonthlyPackage']['calendarscheduler'];
            }
            if ($this->request->data['MonthlyPackage']['appointments'] == '') {
                $this->request->data['MonthlyPackage']['appointments'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['appointments'] = $this->request->data['MonthlyPackage']['appointments'];
            }
            if ($this->request->data['MonthlyPackage']['groups'] == '') {
                $this->request->data['MonthlyPackage']['groups'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['groups'] = $this->request->data['MonthlyPackage']['groups'];
            }
            if ($this->request->data['MonthlyPackage']['contactlist'] == '') {
                $this->request->data['MonthlyPackage']['contactlist'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['contactlist'] = $this->request->data['MonthlyPackage']['contactlist'];
            }
            if ($this->request->data['MonthlyPackage']['sendsms'] == '') {
                $this->request->data['MonthlyPackage']['sendsms'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['sendsms'] = $this->request->data['MonthlyPackage']['sendsms'];
            }
            /*if ($this->request->data['MonthlyPackage']['logs'] == '') {
                $this->request->data['MonthlyPackage']['logs'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['logs'] = $this->request->data['MonthlyPackage']['logs'];
            }
            if ($this->request->data['MonthlyPackage']['reports'] == '') {
                $this->request->data['MonthlyPackage']['reports'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['reports'] = $this->request->data['MonthlyPackage']['reports'];
            }*/
            if ($this->request->data['MonthlyPackage']['affiliates'] == '') {
                $this->request->data['MonthlyPackage']['affiliates'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['affiliates'] = $this->request->data['MonthlyPackage']['affiliates'];
            }
            if ($this->request->data['MonthlyPackage']['getnumbers'] == '') {
                $this->request->data['MonthlyPackage']['getnumbers'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['getnumbers'] = $this->request->data['MonthlyPackage']['getnumbers'];
            }
            
            if ($this->request->data['MonthlyPackage']['mobilecoupons'] == '') {
                $this->request->data['MonthlyPackage']['mobilecoupons'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['mobilecoupons'] = $this->request->data['MonthlyPackage']['mobilecoupons'];
            }
            if ($this->request->data['MonthlyPackage']['qabots'] == '') {
                $this->request->data['MonthlyPackage']['qabots'] = 0;
            } else {
                $this->request->data['MonthlyPackage']['qabots'] = $this->request->data['MonthlyPackage']['qabots'];
            }
            
            $this->MonthlyPackage->save($this->request->data);

            $this->Session->setFlash('Monthly package permissions have been saved');
            $this->redirect(array('action' => 'monthlypackage'));
        } else {
            $this->set('id', $id);
            $id = base64_decode($id);
            $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $id)));
            if (!empty($monthlypackage)) {
                $this->set('packagepermissions', $monthlypackage);
            } else {
                $this->Session->setFlash(__('Monthly package permissions could not be found. Please, try again.', true));
                $this->redirect(array('action' => 'index'));
            }

        }
    }
} ?>