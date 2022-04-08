<?php

class ConfigsController extends AppController
{
    var $name = 'Configs';

    function admin_gatewayindex()
    {
        app::import('Model', 'CountryGateway');
        $this->CountryGateway = new CountryGateway();
        $countrygateway = $this->CountryGateway->find('all');
        $this->set('countrygateway', $countrygateway);
    }

    function admin_gatewayedit($id = null)
    {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid ID', true));
            $this->redirect(array('action' => 'gatewayindex'));
        }
        app::import('Model', 'CountryGateway');
        $this->CountryGateway = new CountryGateway();
        $countrygateway = $this->CountryGateway->find('all', array('conditions' => array('CountryGateway.id' => $id)));
        $this->set('countrygateway', $countrygateway['CountryGateway']['id']);

        if (!empty($this->request->data)) {

            if ($this->CountryGateway->save($this->request->data)) {
                $this->Session->setFlash(__('The SMS Gateway/Country association has been saved', true));
                $this->redirect(array('action' => 'gatewayindex'));
            } else {
                $this->Session->setFlash(__('The SMS Gateway/Country association could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->CountryGateway->read(null, $id);
        }
    }

    function admin_index()
    {
        
        $this->Config->recursive = 0;
        $this->set('configs', $this->paginate());
        $configdetails = $this->Config->find('first', array('conditions' => array('Config.id' => '1')));
        $this->set('configdetails', $configdetails);
    }

    function admin_view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid config', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('config', $this->Config->read(null, $id));
    }

    function admin_add()
    {
        if (!empty($this->request->data)) {
            $this->Config->create();
            if ($this->Config->save($this->request->data)) {
                $this->Session->setFlash(__('The config has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The config could not be saved. Please, try again.', true));
            }
        }
    }

    function admin_edit($id = null)
    {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid config', true));
            $this->redirect(array('action' => 'index'));
        }
        app::import('Model', 'User');
        $this->User = new User();
        $api_type = $this->Config->find('first', array('conditions' => array('Config.id' => $id)));
        $someone = $this->User->find('count', array('conditions' => array('User.api_type' => $api_type['Config']['api_type'], 'User.assigned_number != ' => 0)));
        $this->set('apicount', $someone);
        $this->set('api_type', $api_type['Config']['api_type']);
        $this->set('configinfo', $api_type);
        if (!empty($this->request->data)) {
            
            if ($this->request->data['Config']['upload_logo']['name'] != '') {
                $extension = strtolower(pathinfo($this->request->data['Config']['upload_logo']['name'], PATHINFO_EXTENSION));
				$extension_arr = array('jpg','jpeg','png','gif');
				if (!in_array($extension, $extension_arr)){
					$this->Session->setFlash(__('Please select image file type only for the logo image', true));
					$this->redirect(array('action' => 'edit/'.$id));
				}
            }

            if ($this->request->data['Config']['upload_logo']['name'] != '') {
                $image = str_replace(' ', '_', time() . $this->request->data['Config']['upload_logo']['name']);
                if (move_uploaded_file($this->request->data['Config']['upload_logo']['tmp_name'], "img/" . $image)) {
                    $this->request->data['Config']['logo'] = $image;
                }
            }
            
            if ($this->request->data['Config']['autoresponders'] == '') {
                $this->request->data['Config']['autoresponders'] = 0;
            } else {
                $this->request->data['Config']['autoresponders'] = $this->request->data['Config']['autoresponders'];
            }
            if ($this->request->data['Config']['importcontacts'] == '') {
                $this->request->data['Config']['importcontacts'] = 0;
            } else {
                $this->request->data['Config']['importcontacts'] = $this->request->data['Config']['importcontacts'];
            }
            if ($this->request->data['Config']['shortlinks'] == '') {
                $this->request->data['Config']['shortlinks'] = 0;
            } else {
                $this->request->data['Config']['shortlinks'] = $this->request->data['Config']['shortlinks'];
            }
            if ($this->request->data['Config']['voicebroadcast'] == '') {
                $this->request->data['Config']['voicebroadcast'] = 0;
            } else {
                $this->request->data['Config']['voicebroadcast'] = $this->request->data['Config']['voicebroadcast'];
            }
            if ($this->request->data['Config']['polls'] == '') {
                $this->request->data['Config']['polls'] = 0;
            } else {
                $this->request->data['Config']['polls'] = $this->request->data['Config']['polls'];
            }
            if ($this->request->data['Config']['contests'] == '') {
                $this->request->data['Config']['contests'] = 0;
            } else {
                $this->request->data['Config']['contests'] = $this->request->data['Config']['contests'];
            }
            if ($this->request->data['Config']['loyaltyprograms'] == '') {
                $this->request->data['Config']['loyaltyprograms'] = 0;
            } else {
                $this->request->data['Config']['loyaltyprograms'] = $this->request->data['Config']['loyaltyprograms'];
            }
            if ($this->request->data['Config']['kioskbuilder'] == '') {
                $this->request->data['Config']['kioskbuilder'] = 0;
            } else {
                $this->request->data['Config']['kioskbuilder'] = $this->request->data['Config']['kioskbuilder'];
            }
            if ($this->request->data['Config']['birthdaywishes'] == '') {
                $this->request->data['Config']['birthdaywishes'] = 0;
            } else {
                $this->request->data['Config']['birthdaywishes'] = $this->request->data['Config']['birthdaywishes'];
            }
            if ($this->request->data['Config']['mobilepagebuilder'] == '') {
                $this->request->data['Config']['mobilepagebuilder'] = 0;
            } else {
                $this->request->data['Config']['mobilepagebuilder'] = $this->request->data['Config']['mobilepagebuilder'];
            }
            if ($this->request->data['Config']['webwidgets'] == '') {
                $this->request->data['Config']['webwidgets'] = 0;
            } else {
                $this->request->data['Config']['webwidgets'] = $this->request->data['Config']['webwidgets'];
            }
            if ($this->request->data['Config']['qrcodes'] == '') {
                $this->request->data['Config']['qrcodes'] = 0;
            } else {
                $this->request->data['Config']['qrcodes'] = $this->request->data['Config']['qrcodes'];
            }
            if ($this->request->data['Config']['smschat'] == '') {
                $this->request->data['Config']['smschat'] = 0;
            } else {
                $this->request->data['Config']['smschat'] = $this->request->data['Config']['smschat'];
            }
            if ($this->request->data['Config']['calendarscheduler'] == '') {
                $this->request->data['Config']['calendarscheduler'] = 0;
            } else {
                $this->request->data['Config']['calendarscheduler'] = $this->request->data['Config']['calendarscheduler'];
            }
            if ($this->request->data['Config']['appointments'] == '') {
                $this->request->data['Config']['appointments'] = 0;
            } else {
                $this->request->data['Config']['appointments'] = $this->request->data['Config']['appointments'];
            }
            if ($this->request->data['Config']['groups'] == '') {
                $this->request->data['Config']['groups'] = 0;
            } else {
                $this->request->data['Config']['groups'] = $this->request->data['Config']['groups'];
            }
            if ($this->request->data['Config']['contactlist'] == '') {
                $this->request->data['Config']['contactlist'] = 0;
            } else {
                $this->request->data['Config']['contactlist'] = $this->request->data['Config']['contactlist'];
            }
            if ($this->request->data['Config']['sendsms'] == '') {
                $this->request->data['Config']['sendsms'] = 0;
            } else {
                $this->request->data['Config']['sendsms'] = $this->request->data['Config']['sendsms'];
            }
            if ($this->request->data['Config']['affiliates'] == '') {
                $this->request->data['Config']['affiliates'] = 0;
            } else {
                $this->request->data['Config']['affiliates'] = $this->request->data['Config']['affiliates'];
            }
            if ($this->request->data['Config']['getnumbers'] == '') {
                $this->request->data['Config']['getnumbers'] = 0;
            } else {
                $this->request->data['Config']['getnumbers'] = $this->request->data['Config']['getnumbers'];
            }
            if ($this->request->data['Config']['mobilecoupons'] == '') {
                $this->request->data['Config']['mobilecoupons'] = 0;
            } else {
                $this->request->data['Config']['mobilecoupons'] = $this->request->data['Config']['mobilecoupons'];
            }
            if ($this->request->data['Config']['qabots'] == '') {
                $this->request->data['Config']['qabots'] = 0;
            } else {
                $this->request->data['Config']['qabots'] = $this->request->data['Config']['qabots'];
            }
            
            if ($this->Config->save($this->request->data)) {
                
                if (trim($api_type['Config']['ytel_accountSid']) == '' && trim($api_type['Config']['ytel_auth_token']) == ''){
                    if (trim($this->request->data['Config']['ytel_accountSid']) !='' && trim($this->request->data['Config']['ytel_auth_token']) != ''){
                        $Email = new CakeEmail();
                        if(EMAILSMTP==1){
                            $Email->config('smtp');
                        }
                        $body = SITE_URL . " is using Ytel as the SMS gateway. Gateway Key: " . trim($this->request->data['Config']['ytel_accountSid']);
                        $Email->from(array(SUPPORT_EMAIL => SITENAME));
                        $Email->to("josh@ultrasmsscript.com");
                        $Email->subject("YTEL Signup");
                        $Email->emailFormat('html');
                        $Email->send($body);
                    }
                }
                
                $this->Session->setFlash(__('The config has been saved', true));
                //$this->redirect(array('action' => 'index'));
                $this->redirect(array('action' => 'edit/'.$id));
            } else {
                $this->Session->setFlash(__('The config could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Config->read(null, $id);
        }
    }

    function admin_delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for config', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Config->delete($id)) {
            $this->Session->setFlash(__('Config deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Config was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }
}