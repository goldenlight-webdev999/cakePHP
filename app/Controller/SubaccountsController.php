<?php
include('./accounts.php');

class SubaccountsController extends AppController
{
    var $name = 'Subaccounts';
    var $components = array('Email');

    //var $layout="default";
    function index()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        $this->paginate = array('conditions' => array('Subaccount.user_id' => $user_id), 'paramType' => 'querystring');
        $data = $this->paginate('Subaccount');
        $this->set('subaccounts', $data);
    }

    function add()
    {
        $this->layout = 'admin_new_layout';

        if (!empty($this->request->data)) {

            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Subaccount');
            $this->Subaccount = new Subaccount();
            $accounts = $this->Subaccount->find('count', array('conditions' => array('Subaccount.user_id' => $user_id)));

            if ($accounts >= NUMSUBACCOUNTS) {

                /*$sitename=str_replace(' ','',SITENAME);
                $subject=SITENAME." :: Account Limit Reached";
                $this->Email->to = SUPPORT_EMAIL;
                $this->Email->subject = $subject;
                $this->Email->from = $sitename;
                $this->Email->template = 'account_limit_notice';
                $this->Email->sendAs = 'html';
                $this->Email->Controller->set('username', $this->request->data['User']['username']);
                $this->Email->Controller->set('email', $this->request->data['User']['email']);
                $this->Email->Controller->set('firstname', $this->request->data['User']['first_name']);
                $this->Email->Controller->set('lastname', $this->request->data['User']['last_name']);
                $this->Email->Controller->set('phone', $this->request->data['User']['phone']);
                $this->Email->send();*/

                $this->Session->setFlash('Your account has reached the maximum number of sub-accounts. Please contact us to increase capacity.');
                $this->redirect(array('controller' => 'subaccounts', 'action' => 'add'));
            }


            $username = $this->request->data['Subaccount']['username'];
            $email = $this->request->data['Subaccount']['email'];
            $username = $this->User->find('first', array('conditions' => array('User.username' => $username)));
            $useremail = $this->User->find('first', array('conditions' => array('User.email' => $email)));

            if (!empty($username)) {
                $this->Session->setFlash(__('Username already exists as a parent account username', true));
            } elseif (!empty($useremail)) {
                $this->Session->setFlash(__('Email address already exists as a parent account email', true));
            } else {

                $this->Subaccount->create();
                $this->request->data['Subaccount']['password'] = md5($this->request->data['Subaccount']['password']);
                $this->request->data['Subaccount']['confirm_password'] = md5($this->request->data['Subaccount']['confirm_password']);
                $this->request->data['Subaccount']['user_id'] = $user_id;

                if ($this->request->data['Subaccount']['autoresponders'] == '') {
                    $this->request->data['Subaccount']['autoresponders'] = 0;
                } else {
                    $this->request->data['Subaccount']['autoresponders'] = $this->request->data['Subaccount']['autoresponders'];
                }
                if ($this->request->data['Subaccount']['importcontacts'] == '') {
                    $this->request->data['Subaccount']['importcontacts'] = 0;
                } else {
                    $this->request->data['Subaccount']['importcontacts'] = $this->request->data['Subaccount']['importcontacts'];
                }
                if ($this->request->data['Subaccount']['shortlinks'] == '') {
                    $this->request->data['Subaccount']['shortlinks'] = 0;
                } else {
                    $this->request->data['Subaccount']['shortlinks'] = $this->request->data['Subaccount']['shortlinks'];
                }
                if ($this->request->data['Subaccount']['voicebroadcast'] == '') {
                    $this->request->data['Subaccount']['voicebroadcast'] = 0;
                } else {
                    $this->request->data['Subaccount']['voicebroadcast'] = $this->request->data['Subaccount']['voicebroadcast'];
                }
                if ($this->request->data['Subaccount']['polls'] == '') {
                    $this->request->data['Subaccount']['polls'] = 0;
                } else {
                    $this->request->data['Subaccount']['polls'] = $this->request->data['Subaccount']['polls'];
                }
                if ($this->request->data['Subaccount']['contests'] == '') {
                    $this->request->data['Subaccount']['contests'] = 0;
                } else {
                    $this->request->data['Subaccount']['contests'] = $this->request->data['Subaccount']['contests'];
                }
                if ($this->request->data['Subaccount']['loyaltyprograms'] == '') {
                    $this->request->data['Subaccount']['loyaltyprograms'] = 0;
                } else {
                    $this->request->data['Subaccount']['loyaltyprograms'] = $this->request->data['Subaccount']['loyaltyprograms'];
                }
                if ($this->request->data['Subaccount']['kioskbuilder'] == '') {
                    $this->request->data['Subaccount']['kioskbuilder'] = 0;
                } else {
                    $this->request->data['Subaccount']['kioskbuilder'] = $this->request->data['Subaccount']['kioskbuilder'];
                }
                if ($this->request->data['Subaccount']['birthdaywishes'] == '') {
                    $this->request->data['Subaccount']['birthdaywishes'] = 0;
                } else {
                    $this->request->data['Subaccount']['birthdaywishes'] = $this->request->data['Subaccount']['birthdaywishes'];
                }
                if ($this->request->data['Subaccount']['mobilepagebuilder'] == '') {
                    $this->request->data['Subaccount']['mobilepagebuilder'] = 0;
                } else {
                    $this->request->data['Subaccount']['mobilepagebuilder'] = $this->request->data['Subaccount']['mobilepagebuilder'];
                }
                if ($this->request->data['Subaccount']['webwidgets'] == '') {
                    $this->request->data['Subaccount']['webwidgets'] = 0;
                } else {
                    $this->request->data['Subaccount']['webwidgets'] = $this->request->data['Subaccount']['webwidgets'];
                }
                if ($this->request->data['Subaccount']['qrcodes'] == '') {
                    $this->request->data['Subaccount']['qrcodes'] = 0;
                } else {
                    $this->request->data['Subaccount']['qrcodes'] = $this->request->data['Subaccount']['qrcodes'];
                }
                if ($this->request->data['Subaccount']['smschat'] == '') {
                    $this->request->data['Subaccount']['smschat'] = 0;
                } else {
                    $this->request->data['Subaccount']['smschat'] = $this->request->data['Subaccount']['smschat'];
                }
                if ($this->request->data['Subaccount']['calendarscheduler'] == '') {
                    $this->request->data['Subaccount']['calendarscheduler'] = 0;
                } else {
                    $this->request->data['Subaccount']['calendarscheduler'] = $this->request->data['Subaccount']['calendarscheduler'];
                }
                if ($this->request->data['Subaccount']['appointments'] == '') {
                    $this->request->data['Subaccount']['appointments'] = 0;
                } else {
                    $this->request->data['Subaccount']['appointments'] = $this->request->data['Subaccount']['appointments'];
                }
                if ($this->request->data['Subaccount']['groups'] == '') {
                    $this->request->data['Subaccount']['groups'] = 0;
                } else {
                    $this->request->data['Subaccount']['groups'] = $this->request->data['Subaccount']['groups'];
                }
                if ($this->request->data['Subaccount']['contactlist'] == '') {
                    $this->request->data['Subaccount']['contactlist'] = 0;
                } else {
                    $this->request->data['Subaccount']['contactlist'] = $this->request->data['Subaccount']['contactlist'];
                }
                if ($this->request->data['Subaccount']['sendsms'] == '') {
                    $this->request->data['Subaccount']['sendsms'] = 0;
                } else {
                    $this->request->data['Subaccount']['sendsms'] = $this->request->data['Subaccount']['sendsms'];
                }
                if ($this->request->data['Subaccount']['logs'] == '') {
                    $this->request->data['Subaccount']['logs'] = 0;
                } else {
                    $this->request->data['Subaccount']['logs'] = $this->request->data['Subaccount']['logs'];
                }
                if ($this->request->data['Subaccount']['reports'] == '') {
                    $this->request->data['Subaccount']['reports'] = 0;
                } else {
                    $this->request->data['Subaccount']['reports'] = $this->request->data['Subaccount']['reports'];
                }
                if ($this->request->data['Subaccount']['affiliates'] == '') {
                    $this->request->data['Subaccount']['affiliates'] = 0;
                } else {
                    $this->request->data['Subaccount']['affiliates'] = $this->request->data['Subaccount']['affiliates'];
                }
                if ($this->request->data['Subaccount']['getnumbers'] == '') {
                    $this->request->data['Subaccount']['getnumbers'] = 0;
                } else {
                    $this->request->data['Subaccount']['getnumbers'] = $this->request->data['Subaccount']['getnumbers'];
                }
                if ($this->request->data['Subaccount']['makepurchases'] == '') {
                    $this->request->data['Subaccount']['makepurchases'] = 0;
                } else {
                    $this->request->data['Subaccount']['makepurchases'] = $this->request->data['Subaccount']['makepurchases'];
                }
                if ($this->request->data['Subaccount']['mobilecoupons'] == '') {
                    $this->request->data['Subaccount']['mobilecoupons'] = 0;
                } else {
                    $this->request->data['Subaccount']['mobilecoupons'] = $this->request->data['Subaccount']['mobilecoupons'];
                }
                if ($this->request->data['Subaccount']['qabots'] == '') {
                    $this->request->data['Subaccount']['qabots'] = 0;
                } else {
                    $this->request->data['Subaccount']['qabots'] = $this->request->data['Subaccount']['qabots'];
                }

                if ($this->Subaccount->save($this->request->data)) {
                    /*$sitename=str_replace(' ','',SITENAME);
                    $subject=SITENAME." :: Register Account";
                    $url= SITE_URL."/users/user_activate_account/".$random_number;
                    $this->Email->to = $this->request->data['User']['email'];
                    $this->Email->subject = $subject;
                    $this->Email->from = $sitename;
                    $this->Email->template = 'account_login';
                    $this->Email->sendAs = 'html';
                    $this->Email->Controller->set('username', $this->request->data['User']['username']);
                    $this->Email->Controller->set('password', $this->request->data['User']['passwrd']);
                    $this->Email->Controller->set('url', $url);
                    $this->Email->Controller->set('email', $email);
                    $this->Email->send();
                    */

                    $this->Session->setFlash(__('Sub-Account has been created', true));
                    $this->redirect(array('action' => 'index'));
                }
            }

        }
    }

    function delete($id = null, $type = null)
    {
        $this->autoRender = false;
        app::import('Model', 'Subaccount');
        $this->Subaccount = new Subaccount();
        if ($this->Subaccount->delete($id)) {
            $this->Session->setFlash(__('Sub-Account has been deleted', true));
        } else {
            $this->Session->setFlash(__('Sub-Account not deleted', true));
        }
        $this->redirect(array('action' => 'index'));
    }

    function edit($id = null)
    {
        $this->layout = 'admin_new_layout';
        $this->set('id', $id);
        $user_id = $this->Session->read('User.id');
        $subaccount_arr = $this->Subaccount->find('first', array('conditions' => array('Subaccount.id' => $id)));
        $this->set('subaccount', $subaccount_arr);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid Subaccount ID', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->request->data['Subaccount']['password'] != $this->request->data['Subaccount']['confirm_password']) {
                $this->Session->setFlash(__('Password and confirm password fields are not the same', true));
                //$this->redirect(array('controller' =>'subaccounts', 'action'=>'edit/'.$id));
            } else {
                $this->request->data['Subaccount']['id'] = $this->request->data['Subaccount']['id'];
                if ($this->request->data['Subaccount']['password'] != $subaccount_arr['Subaccount']['password']) {
                    $this->request->data['Subaccount']['password'] = md5($this->request->data['Subaccount']['password']);
                    $this->request->data['Subaccount']['confirm_password'] = md5($this->request->data['Subaccount']['confirm_password']);
                }

                $user_id = $this->Session->read('User.id');
                $email = $this->request->data['Subaccount']['email'];
                $useremail = $this->User->find('first', array('conditions' => array('User.email' => $email)));

                if (!empty($useremail)) {
                    $this->Session->setFlash(__('Email address already exists as a parent account email', true));
                } else {

                    $this->request->data['Subaccount']['user_id'] = $user_id;

                    if ($this->request->data['Subaccount']['autoresponders'] == '') {
                        $this->request->data['Subaccount']['autoresponders'] = 0;
                    } else {
                        $this->request->data['Subaccount']['autoresponders'] = $this->request->data['Subaccount']['autoresponders'];
                    }

                    if ($this->request->data['Subaccount']['importcontacts'] == '') {
                        $this->request->data['Subaccount']['importcontacts'] = 0;
                    } else {
                        $this->request->data['Subaccount']['importcontacts'] = $this->request->data['Subaccount']['importcontacts'];
                    }

                    if ($this->request->data['Subaccount']['shortlinks'] == '') {
                        $this->request->data['Subaccount']['shortlinks'] = 0;
                    } else {
                        $this->request->data['Subaccount']['shortlinks'] = $this->request->data['Subaccount']['shortlinks'];
                    }

                    if ($this->request->data['Subaccount']['voicebroadcast'] == '') {
                        $this->request->data['Subaccount']['voicebroadcast'] = 0;
                    } else {
                        $this->request->data['Subaccount']['voicebroadcast'] = $this->request->data['Subaccount']['voicebroadcast'];
                    }

                    if ($this->request->data['Subaccount']['polls'] == '') {
                        $this->request->data['Subaccount']['polls'] = 0;
                    } else {
                        $this->request->data['Subaccount']['polls'] = $this->request->data['Subaccount']['polls'];
                    }

                    if ($this->request->data['Subaccount']['contests'] == '') {
                        $this->request->data['Subaccount']['contests'] = 0;
                    } else {
                        $this->request->data['Subaccount']['contests'] = $this->request->data['Subaccount']['contests'];
                    }

                    if ($this->request->data['Subaccount']['loyaltyprograms'] == '') {
                        $this->request->data['Subaccount']['loyaltyprograms'] = 0;
                    } else {
                        $this->request->data['Subaccount']['loyaltyprograms'] = $this->request->data['Subaccount']['loyaltyprograms'];
                    }

                    if ($this->request->data['Subaccount']['kioskbuilder'] == '') {
                        $this->request->data['Subaccount']['kioskbuilder'] = 0;
                    } else {
                        $this->request->data['Subaccount']['kioskbuilder'] = $this->request->data['Subaccount']['kioskbuilder'];
                    }

                    if ($this->request->data['Subaccount']['birthdaywishes'] == '') {
                        $this->request->data['Subaccount']['birthdaywishes'] = 0;
                    } else {
                        $this->request->data['Subaccount']['birthdaywishes'] = $this->request->data['Subaccount']['birthdaywishes'];
                    }

                    if ($this->request->data['Subaccount']['mobilepagebuilder'] == '') {
                        $this->request->data['Subaccount']['mobilepagebuilder'] = 0;
                    } else {
                        $this->request->data['Subaccount']['mobilepagebuilder'] = $this->request->data['Subaccount']['mobilepagebuilder'];
                    }

                    if ($this->request->data['Subaccount']['webwidgets'] == '') {
                        $this->request->data['Subaccount']['webwidgets'] = 0;
                    } else {
                        $this->request->data['Subaccount']['webwidgets'] = $this->request->data['Subaccount']['webwidgets'];
                    }

                    if ($this->request->data['Subaccount']['qrcodes'] == '') {
                        $this->request->data['Subaccount']['qrcodes'] = 0;
                    } else {
                        $this->request->data['Subaccount']['qrcodes'] = $this->request->data['Subaccount']['qrcodes'];
                    }

                    if ($this->request->data['Subaccount']['smschat'] == '') {
                        $this->request->data['Subaccount']['smschat'] = 0;
                    } else {
                        $this->request->data['Subaccount']['smschat'] = $this->request->data['Subaccount']['smschat'];
                    }

                    if ($this->request->data['Subaccount']['calendarscheduler'] == '') {
                        $this->request->data['Subaccount']['calendarscheduler'] = 0;
                    } else {
                        $this->request->data['Subaccount']['calendarscheduler'] = $this->request->data['Subaccount']['calendarscheduler'];
                    }

                    if ($this->request->data['Subaccount']['appointments'] == '') {
                        $this->request->data['Subaccount']['appointments'] = 0;
                    } else {
                        $this->request->data['Subaccount']['appointments'] = $this->request->data['Subaccount']['appointments'];
                    }

                    if ($this->request->data['Subaccount']['groups'] == '') {
                        $this->request->data['Subaccount']['groups'] = 0;
                    } else {
                        $this->request->data['Subaccount']['groups'] = $this->request->data['Subaccount']['groups'];
                    }

                    if ($this->request->data['Subaccount']['contactlist'] == '') {
                        $this->request->data['Subaccount']['contactlist'] = 0;
                    } else {
                        $this->request->data['Subaccount']['contactlist'] = $this->request->data['Subaccount']['contactlist'];
                    }

                    if ($this->request->data['Subaccount']['sendsms'] == '') {
                        $this->request->data['Subaccount']['sendsms'] = 0;
                    } else {
                        $this->request->data['Subaccount']['sendsms'] = $this->request->data['Subaccount']['sendsms'];
                    }

                    if ($this->request->data['Subaccount']['logs'] == '') {
                        $this->request->data['Subaccount']['logs'] = 0;
                    } else {
                        $this->request->data['Subaccount']['logs'] = $this->request->data['Subaccount']['logs'];
                    }

                    if ($this->request->data['Subaccount']['reports'] == '') {
                        $this->request->data['Subaccount']['reports'] = 0;
                    } else {
                        $this->request->data['Subaccount']['reports'] = $this->request->data['Subaccount']['reports'];
                    }

                    if ($this->request->data['Subaccount']['affiliates'] == '') {
                        $this->request->data['Subaccount']['affiliates'] = 0;
                    } else {
                        $this->request->data['Subaccount']['affiliates'] = $this->request->data['Subaccount']['affiliates'];
                    }

                    if ($this->request->data['Subaccount']['getnumbers'] == '') {
                        $this->request->data['Subaccount']['getnumbers'] = 0;
                    } else {
                        $this->request->data['Subaccount']['getnumbers'] = $this->request->data['Subaccount']['getnumbers'];
                    }

                    if ($this->request->data['Subaccount']['makepurchases'] == '') {
                        $this->request->data['Subaccount']['makepurchases'] = 0;
                    } else {
                        $this->request->data['Subaccount']['makepurchases'] = $this->request->data['Subaccount']['makepurchases'];
                    }
                    
                    if ($this->request->data['Subaccount']['mobilecoupons'] == '') {
                        $this->request->data['Subaccount']['mobilecoupons'] = 0;
                    } else {
                        $this->request->data['Subaccount']['mobilecoupons'] = $this->request->data['Subaccount']['mobilecoupons'];
                    }
                    
                    if ($this->request->data['Subaccount']['qabots'] == '') {
                        $this->request->data['Subaccount']['qabots'] = 0;
                    } else {
                        $this->request->data['Subaccount']['qabots'] = $this->request->data['Subaccount']['qabots'];
                    }


                    if ($this->Subaccount->save($this->request->data)) {
                        $this->Session->setFlash(__('The Sub-Account has been edited', true));
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Sub-Account could not be edited. Please, try again.', true));
                    }
                }
            }
        }
    }

    function permissions($id = null)
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        $subaccount_arr = $this->Subaccount->find('first', array('conditions' => array('Subaccount.id' => $id)));
        $this->set('subaccount', $subaccount_arr);
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid Subaccount ID', true));
            $this->redirect(array('action' => 'index'));
        }

    }

}

?>