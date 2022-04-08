<?php
App::import('Vendor', 'mailchimp', array('file' => 'mailchimp/MailChimp.php'));
App::import('Vendor', 'getresponse', array('file' => 'getresponse/GetResponse.php'));
App::import('Vendor', 'activecampaign', array('file' => 'activecampaign/ActiveCampaign.class.php'));
App::import('Vendor', 'aweber', array('file' => 'aweber/aweber_api.php'));
App::import('Vendor', 'mailin', array('file' => 'mailin/Mailin.php'));
App::uses('CakeEmail', 'Network/Email');

class KiosksController extends AppController
{
    public $name = 'Kiosks';
    var $components = array('Twilio', 'Qr', 'Qrsms', 'Nexmomessage', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');

    function index()
    {
        if ($this->Session->check('User')) {
            $this->layout = 'admin_new_layout';
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Kiosks');
            $this->Kiosks = new Kiosks();
            $this->Kiosks->recursive = 0;
            $this->paginate = array(
                'conditions' => array('Kiosks.user_id' => $user_id), 'order' => array('Kiosks.id' => 'asc')
            );
            $data = $this->paginate('Kiosks');
            $this->set('kiosks', $data);
        } else {
            $this->redirect('/users/login');
        }
    }

    function add()
    {
        if ($this->Session->check('User')) {
            $this->layout = 'admin_new_layout';
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();
            $Smsloyalty = $this->Smsloyalty->find('list', array('conditions' => array('Smsloyalty.user_id' => $user_id), 'fields' => 'Smsloyalty.program_name', 'order' => array('Smsloyalty.program_name' => 'asc')));
            $this->set('Smsloyalty', $Smsloyalty);
            app::import('Model', 'Kiosks');
            $this->Kiosks = new Kiosks();

            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $numbers_sms = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
            $this->set('numbers_sms', $numbers_sms);
            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('users', $users);

            if (!empty($this->request->data)) {
                if($this->request->data['Kiosk']['loyalty_programs'] == 0){
                    $this->Session->setFlash(__('You must select a loyalty program to associate the kiosk with.', true));
                    $this->redirect(array('controller' => 'kiosks', 'action' => 'index'));
                }else{
                    $Kioskslist = $this->Kiosks->find('first', array('conditions' => array('Kiosks.loyalty_id' => $this->request->data['Kiosk']['loyalty_programs'], 'Kiosks.user_id' => $user_id), 'order' => array('Kiosks.id' => 'asc')));
                    if (empty($Kioskslist)) {
                        
                        $unique_id = $this->unique_code(8);
                        $kiosks_arr['Kiosks']['unique_id'] = $unique_id;
                        $kiosks_arr['Kiosks']['name'] = $this->request->data['Kiosk']['name'];
                        $kiosks_arr['Kiosks']['loyalty_id'] = $this->request->data['Kiosk']['loyalty_programs'];
                        $kiosks_arr['Kiosks']['user_id'] = $user_id;
                        $kiosks_arr['Kiosks']['background_color'] = $this->request->data['Kiosk']['background_color'];
                        $kiosks_arr['Kiosks']['style'] = $this->request->data['Kiosk']['style'];
                        $image = '';
                        $business_logo = '';
                        
                        if (isset($this->request->data['Kiosk']['file']['name']) && $this->request->data['Kiosk']['file']['name'] != '') {
                            $extension = strtolower(pathinfo($this->request->data['Kiosk']['file']['name'], PATHINFO_EXTENSION));
    						$extension_arr = array('jpg','jpeg','png','gif');
    						if (!in_array($extension, $extension_arr)){
    							$this->Session->setFlash(__('Please select image file type only for the background image', true));
    							$this->redirect(array('action'=>'add'));
    						}
                        }
                        
                        if (isset($this->request->data['Kiosk']['business_logo']['name']) && $this->request->data['Kiosk']['business_logo']['name'] != '') {
                            $extension = strtolower(pathinfo($this->request->data['Kiosk']['business_logo']['name'], PATHINFO_EXTENSION));
    						$extension_arr = array('jpg','jpeg','png','gif');
    						if (!in_array($extension, $extension_arr)){
    							$this->Session->setFlash(__('Please select image file type only for the logo image', true));
    							$this->redirect(array('action'=>'add'));
    						}
                        }
                    
                        if (isset($this->request->data['Kiosk']['file']['name'])) {
                            if ($this->request->data['Kiosk']['file']['name'] != '') {
                                $image = str_replace(' ', '_', time() . '' . $this->request->data['Kiosk']['file']['name']);
                                move_uploaded_file($this->request->data['Kiosk']['file']['tmp_name'], "img_kiosks/" . $image);
                            }
                        }
                        if (isset($this->request->data['Kiosk']['business_logo']['name'])) {
                            if ($this->request->data['Kiosk']['business_logo']['name'] != '') {
                                $business_logo = str_replace(' ', '_', time() . '' . $this->request->data['Kiosk']['business_logo']['name']);
                                move_uploaded_file($this->request->data['Kiosk']['business_logo']['tmp_name'], "img_kiosks/" . $business_logo);
                            }
                        }
                        $kiosks_arr['Kiosks']['file'] = $image;
                        $kiosks_arr['Kiosks']['business_logo'] = $business_logo;
                        $kiosks_arr['Kiosks']['textheader'] = $this->request->data['Kiosk']['textheader'];
                        $kiosks_arr['Kiosks']['alignment'] = $this->request->data['Kiosk']['alignment'];
                        $kiosks_arr['Kiosks']['font'] = $this->request->data['Kiosk']['font'];
                        $kiosks_arr['Kiosks']['fontsize'] = $this->request->data['Kiosk']['fontsize'];
                        $kiosks_arr['Kiosks']['color'] = $this->request->data['Kiosk']['color'];
                        if (isset($this->request->data['Kiosk']['styleB'])) {
                            $kiosks_arr['Kiosks']['styleB'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['styleB'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['styleI'])) {
                            $kiosks_arr['Kiosks']['styleI'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['styleI'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['styleU'])) {
                            $kiosks_arr['Kiosks']['styleU'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['styleU'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['joinbuttons'])) {
                            $kiosks_arr['Kiosks']['joinbuttons'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['joinbuttons'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['punchcard'])) {
                            $kiosks_arr['Kiosks']['punchcard'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['punchcard'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['checkpoints'])) {
                            $kiosks_arr['Kiosks']['checkpoints'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['checkpoints'] = 0;
                        }
                        $kiosks_arr['Kiosks']['joinbutton'] = $this->request->data['Kiosk']['joinbutton'];
                        $kiosks_arr['Kiosks']['checkin'] = $this->request->data['Kiosk']['checkin'];
                        $kiosks_arr['Kiosks']['mypoints'] = $this->request->data['Kiosk']['mypoints'];
                        $kiosks_arr['Kiosks']['buttoncolor'] = $this->request->data['Kiosk']['buttoncolor'];
                        $kiosks_arr['Kiosks']['textcolor'] = $this->request->data['Kiosk']['textcolor'];
                        $kiosks_arr['Kiosks']['keypad_button_color'] = $this->request->data['Kiosk']['keypad_button_color'];
                        $kiosks_arr['Kiosks']['keypad_text_color'] = $this->request->data['Kiosk']['keypad_text_color'];
                        $kiosks_arr['Kiosks']['bottom_text'] = $this->request->data['Kiosk']['bottom_text'];
                        $kiosks_arr['Kiosks']['bottom_text_alignment'] = $this->request->data['Kiosk']['bottom_text_alignment'];
                        $kiosks_arr['Kiosks']['bottom_text_font'] = $this->request->data['Kiosk']['bottom_text_font'];
                        $kiosks_arr['Kiosks']['bottom_text_size'] = $this->request->data['Kiosk']['bottom_text_size'];
                        $kiosks_arr['Kiosks']['bottom_text_color'] = $this->request->data['Kiosk']['bottom_text_color'];
                        if (isset($this->request->data['Kiosk']['bottom_text_styleB'])) {
                            $kiosks_arr['Kiosks']['bottom_text_styleB'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['bottom_text_styleB'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['bottom_text_styleI'])) {
                            $kiosks_arr['Kiosks']['bottom_text_styleI'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['bottom_text_styleI'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['bottom_text_styleU'])) {
                            $kiosks_arr['Kiosks']['bottom_text_styleU'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['bottom_text_styleU'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['firstname'])) {
                            $kiosks_arr['Kiosks']['firstname'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['firstname'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['lastname'])) {
                            $kiosks_arr['Kiosks']['lastname'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['lastname'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['email'])) {
                            $kiosks_arr['Kiosks']['email'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['email'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['dob'])) {
                            $kiosks_arr['Kiosks']['dob'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['dob'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['city'])) {
                            $kiosks_arr['Kiosks']['city'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['city'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['state'])) {
                            $kiosks_arr['Kiosks']['state'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['state'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['zip'])) {
                            $kiosks_arr['Kiosks']['zip'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['zip'] = 0;
                        }
                        if ($this->Kiosks->save($kiosks_arr)) {
                            $this->Session->setFlash(__('Kiosk has been created', true));
                            $this->redirect(array('controller' => 'kiosks', 'action' => 'index'));
                        } else {
                            $this->Session->setFlash(__('Kiosk was not created', true));
                        }
                    } else {
                        $this->Session->setFlash(__('You already have a kiosk created for this loyalty program.', true));
                    }
                }
            }
        } else {
            $this->redirect('/users/login');
        }
    }

    function edit($id = null)
    {
        if ($this->Session->check('User')) {
            $this->layout = 'admin_new_layout';
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();
            $Smsloyalty = $this->Smsloyalty->find('list', array('conditions' => array('Smsloyalty.user_id' => $user_id), 'fields' => 'Smsloyalty.program_name', 'order' => array('Smsloyalty.program_name' => 'asc')));
            $this->set('Smsloyalty', $Smsloyalty);
            app::import('Model', 'Kiosks');
            $this->Kiosks = new Kiosks();
            $Kiosk = $this->Kiosks->find('first', array('conditions' => array('Kiosks.id' => $id, 'Kiosks.user_id' => $user_id), 'order' => array('Kiosks.id' => 'asc')));
            $this->set('Kioskslist', $Kiosk);
            $this->set('id', $id);
            
            if (!empty($this->request->data)) {
                if($this->request->data['Kiosk']['loyalty_programs'] == 0){
                        $this->Session->setFlash(__('You must select a loyalty program to associate the kiosk with.', true));
                        $this->redirect(array('controller' => 'kiosks', 'action' => 'index'));
                }else{
                    $Kioskslist = $this->Kiosks->find('first', array('conditions' => array('Kiosks.id !=' => $id, 'Kiosks.loyalty_id' => $this->request->data['Kiosk']['loyalty_programs'], 'Kiosks.user_id' => $user_id), 'order' => array('Kiosks.id' => 'asc')));
                    if (empty($Kioskslist)) {
                        $kiosks_arr['Kiosks']['id'] = $this->request->data['Kiosk']['id'];
                        $kiosks_arr['Kiosks']['name'] = $this->request->data['Kiosk']['name'];
                        $kiosks_arr['Kiosks']['loyalty_id'] = $this->request->data['Kiosk']['loyalty_programs'];
                        $kiosks_arr['Kiosks']['user_id'] = $user_id;
                        $kiosks_arr['Kiosks']['background_color'] = $this->request->data['Kiosk']['background_color'];
                        $kiosks_arr['Kiosks']['style'] = $this->request->data['Kiosk']['style'];
                        
                        if (isset($this->request->data['Kiosk']['file']['name']) && $this->request->data['Kiosk']['file']['name'] != '') {
                            $extension = strtolower(pathinfo($this->request->data['Kiosk']['file']['name'], PATHINFO_EXTENSION));
    						$extension_arr = array('jpg','jpeg','png','gif');
    						if (!in_array($extension, $extension_arr)){
    							$this->Session->setFlash(__('Please select image file type only for the background image', true));
    							$this->redirect(array('action'=>'edit/'.$id));
    						}
                        }
                        
                        if (isset($this->request->data['Kiosk']['business_logo']['name']) && $this->request->data['Kiosk']['business_logo']['name'] != '') {
                            $extension = strtolower(pathinfo($this->request->data['Kiosk']['business_logo']['name'], PATHINFO_EXTENSION));
    						$extension_arr = array('jpg','jpeg','png','gif');
    						if (!in_array($extension, $extension_arr)){
    							$this->Session->setFlash(__('Please select image file type only for the logo image', true));
    							$this->redirect(array('action'=>'edit/'.$id));
    						}
                        }
                        
                        if (isset($this->request->data['Kiosk']['file']['name'])) {
                            if ($this->request->data['Kiosk']['file']['name'] != '') {
                                $image = str_replace(' ', '_', time() . '' . $this->request->data['Kiosk']['file']['name']);
                                move_uploaded_file($this->request->data['Kiosk']['file']['tmp_name'], "img_kiosks/" . $image);
                                $kiosks_arr['Kiosks']['file'] = $image;
                            } else {
                                $background_color = $this->request->data['Kiosk']['background_color'];
                                $savebackground_color = $Kiosk['Kiosks']['background_color'];
    
                                if (trim($background_color) != trim($savebackground_color)) {
                                    $kiosks_arr['Kiosks']['file'] = '';
                                }
                            }
                        }
    
                        if (isset($this->request->data['Kiosk']['business_logo']['name'])) {
                            if ($this->request->data['Kiosk']['business_logo']['name'] != '') {
                                $business_logo = str_replace(' ', '_', time() . '' . $this->request->data['Kiosk']['business_logo']['name']);
                                move_uploaded_file($this->request->data['Kiosk']['business_logo']['tmp_name'], "img_kiosks/" . $business_logo);
                                $kiosks_arr['Kiosks']['business_logo'] = $business_logo;
                            }
                        }
                        $kiosks_arr['Kiosks']['textheader'] = $this->request->data['Kiosk']['textheader'];
                        $kiosks_arr['Kiosks']['alignment'] = $this->request->data['Kiosk']['alignment'];
                        $kiosks_arr['Kiosks']['font'] = $this->request->data['Kiosk']['font'];
                        $kiosks_arr['Kiosks']['fontsize'] = $this->request->data['Kiosk']['fontsize'];
                        $kiosks_arr['Kiosks']['color'] = $this->request->data['Kiosk']['color'];
                        if (isset($this->request->data['Kiosk']['styleB'])) {
                            $kiosks_arr['Kiosks']['styleB'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['styleB'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['styleI'])) {
                            $kiosks_arr['Kiosks']['styleI'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['styleI'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['styleU'])) {
                            $kiosks_arr['Kiosks']['styleU'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['styleU'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['joinbuttons'])) {
                            $kiosks_arr['Kiosks']['joinbuttons'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['joinbuttons'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['punchcard'])) {
                            $kiosks_arr['Kiosks']['punchcard'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['punchcard'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['checkpoints'])) {
                            $kiosks_arr['Kiosks']['checkpoints'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['checkpoints'] = 0;
                        }
                        $kiosks_arr['Kiosks']['joinbutton'] = $this->request->data['Kiosk']['joinbutton'];
                        $kiosks_arr['Kiosks']['checkin'] = $this->request->data['Kiosk']['checkin'];
                        $kiosks_arr['Kiosks']['mypoints'] = $this->request->data['Kiosk']['mypoints'];
                        $kiosks_arr['Kiosks']['buttoncolor'] = $this->request->data['Kiosk']['buttoncolor'];
                        $kiosks_arr['Kiosks']['textcolor'] = $this->request->data['Kiosk']['textcolor'];
                        $kiosks_arr['Kiosks']['keypad_button_color'] = $this->request->data['Kiosk']['keypad_button_color'];
                        $kiosks_arr['Kiosks']['keypad_text_color'] = $this->request->data['Kiosk']['keypad_text_color'];
                        $kiosks_arr['Kiosks']['bottom_text'] = $this->request->data['Kiosk']['bottom_text'];
                        $kiosks_arr['Kiosks']['bottom_text_alignment'] = $this->request->data['Kiosk']['bottom_text_alignment'];
                        $kiosks_arr['Kiosks']['bottom_text_font'] = $this->request->data['Kiosk']['bottom_text_font'];
                        $kiosks_arr['Kiosks']['bottom_text_size'] = $this->request->data['Kiosk']['bottom_text_size'];
                        $kiosks_arr['Kiosks']['bottom_text_color'] = $this->request->data['Kiosk']['bottom_text_color'];
                        if (isset($this->request->data['Kiosk']['bottom_text_styleB'])) {
                            $kiosks_arr['Kiosks']['bottom_text_styleB'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['bottom_text_styleB'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['bottom_text_styleI'])) {
                            $kiosks_arr['Kiosks']['bottom_text_styleI'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['bottom_text_styleI'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['bottom_text_styleU'])) {
                            $kiosks_arr['Kiosks']['bottom_text_styleU'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['bottom_text_styleU'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['firstname'])) {
                            $kiosks_arr['Kiosks']['firstname'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['firstname'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['lastname'])) {
                            $kiosks_arr['Kiosks']['lastname'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['lastname'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['email'])) {
                            $kiosks_arr['Kiosks']['email'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['email'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['dob'])) {
                            $kiosks_arr['Kiosks']['dob'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['dob'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['city'])) {
                            $kiosks_arr['Kiosks']['city'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['city'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['state'])) {
                            $kiosks_arr['Kiosks']['state'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['state'] = 0;
                        }
                        if (isset($this->request->data['Kiosk']['zip'])) {
                            $kiosks_arr['Kiosks']['zip'] = 1;
                        } else {
                            $kiosks_arr['Kiosks']['zip'] = 0;
                        }
                        if ($this->Kiosks->save($kiosks_arr)) {
                            $this->Session->setFlash(__('Kiosk has been updated', true));
                            $this->redirect(array('controller' => 'kiosks', 'action' => 'index'));
                        } else {
                            $this->Session->setFlash(__('Kiosk was not updated', true));
                        }
                    } else {
                        $this->Session->setFlash(__('You already have a kiosk created for this loyalty program.', true));
                    }
                }
            }
        } else {
            $this->redirect('/users/login');
        }
    }

    function delete($id = null)
    {
        if ($this->Session->check('User')) {
            if (!$id) {
                $this->Session->setFlash(__('Invalid id for Page', true));
                $this->redirect(array('action' => 'index'));
            }
            app::import('Model', 'Kiosks');
            $this->Kiosks = new Kiosks();
            if ($this->Kiosks->delete($id)) {
                $this->Session->setFlash(__('Kiosk has been deleted', true));
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Kiosk was not deleted', true));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->redirect('/users/login');
        }
    }

    function view($code = null)
    {
        $this->layout = null;
        app::import('Model', 'Kiosks');
        $this->Kiosks = new Kiosks();
        $kiosks = $this->Kiosks->find('first', array('conditions' => array('Kiosks.unique_id' => $code), 'order' => array('Kiosks.id' => 'asc')));
        $this->set('code', $code);
        $this->set('kiosks', $kiosks);

    }

    function success($code = null)
    {
        $this->layout = null;
        app::import('Model', 'Kiosks');
        $this->Kiosks = new Kiosks();
        $kiosks = $this->Kiosks->find('first', array('conditions' => array('Kiosks.unique_id' => $code), 'order' => array('Kiosks.id' => 'asc')));
        $this->set('code', $code);
        $this->set('kiosks', $kiosks);
    }

    function joins($code = null)
    {
        $this->layout = null;
        app::import('Model', 'Kiosks');
        $this->Kiosks = new Kiosks();
        $kiosks = $this->Kiosks->find('first', array('conditions' => array('Kiosks.unique_id' => $code), 'order' => array('Kiosks.id' => 'asc')));
        $this->set('code', $code);
        $this->set('kiosks', $kiosks);
        
        $user_id = $kiosks['Kiosks']['user_id'];
        $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $this->set('country', $users['User']['user_country']);
        
        if (!empty($_POST)) {
            $phone = $_POST['numberval'];
            //$user_id = $kiosks['Kiosks']['user_id'];
            //$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            
            $timezone = $users['User']['timezone'];
            date_default_timezone_set($timezone);
                        
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $newsubscriber = $this->Contact->find('first', array('conditions' => array('Contact.user_id' => $user_id, 'Contact.phone_number' => $phone)));

            if (empty($newsubscriber)) {
                $API_TYPE = $users['User']['api_type'];
                $SID = trim($users['User']['sid']);
                $AUTHTOKEN = trim($users['User']['authtoken']);
                $BANDWIDTHUSERID = trim($users['User']['bandwidthuserid']);
                $SIGNALWIRESPACE = trim($users['User']['signalwirespace']);
                
                $contact['user_id'] = $user_id;
                $fname = '';
                $lname = '';
                if (isset($_POST['firstname']) && trim($_POST['firstname'])!='') {
                    $fname = trim($_POST['firstname']);
                }
                if (isset($_POST['lastname']) && trim($_POST['lastname'])!='') {
                    $lname = trim($_POST['lastname']);
                }
                if (isset($_POST['email']) && trim($_POST['email'])!='') {
                    $contact['email'] = trim($_POST['email']);
                }
                if (isset($_POST['date']) && trim($_POST['date'])!='') {
                    $birthday = date('Y-m-d', strtotime($_POST['date']));
                    $contact['birthday'] = $birthday;
                }
                if (isset($_POST['city']) && trim($_POST['city'])!='') {
                    $contact['location'] = trim($_POST['city']);
                }
                if (isset($_POST['data']['state']) && trim($_POST['data']['state'])!='') {
                    $contact['state'] = trim($_POST['data']['state']);
                }
                if (isset($_POST['zip']) && trim($_POST['zip'])!='') {
                    $contact['zip'] = trim($_POST['zip']);
                }
                //print_r($_POST['data']['state']);

                if (NUMVERIFY != '') {
                    $numbervalidation = $this->validateNumber($phone);
                    $errorcode = $numbervalidation['error']['code'];
                    $valid = $numbervalidation['valid'];
                    $linetype = $numbervalidation['line_type'];

                    if ($errorcode == '') {
                        if ($valid != 1) {
                            $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999 UK format: 449999999999";
                            $this->Session->setFlash(__($err, true));
                            $this->redirect(array('action' => 'joins/' . $code));
                        } else if (trim($linetype) != 'mobile') {
                            $err = "The line type of the number entered is " . $linetype . ". You must provide a mobile number.";
                            $this->Session->setFlash(__($err, true));
                            $this->redirect(array('action' => 'joins/' . $code));
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
                        $carrier = $this->Ytel->lookupcarrier(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone);
                    }else{
                        $carrier = $this->Ytel->lookupcarrier($SID,$AUTHTOKEN,$phone);
                    }
                    
                    if ($carrier->Message360->ResponseStatus == 0) {
                        $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                        $this->Session->setFlash(__($err, true));
                        $this->redirect(array('action' => 'joins/' . $code));
                    } else if (trim($carrier->Message360->Carrier->Wireless) !='true') {
                        $err = "The line type of the number entered is not wireless. You must provide a mobile number.";
                        $this->Session->setFlash(__($err, true));
                        $this->redirect(array('action' => 'joins/' . $code));
                    } else {
                        $contact['carrier']  = $carrier->Message360->Carrier->Network;
                        $contact['location'] = $carrier->Message360->Carrier->City;
                    }
                
                }elseif ($API_TYPE==7){
                
                    if ($SID == ''){
                        $carrier = $this->Telnyx->numberlookup(TELNYX_KEY,$phone);
                    }else{
                        $carrier = $this->Telnyx->numberlookup($SID,$phone);
                    }
                    
                    if(isset($carrier->errors)){
                        $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                        $this->Session->setFlash(__($err, true));
                        $this->redirect(array('action' => 'joins/' . $code));
                    } else if (trim($carrier->data->carrier->type) !='mobile') {
                        $err = "The line type of the number entered is not wireless. You must provide a mobile number.";
                        $this->Session->setFlash(__($err, true));
                        $this->redirect(array('action' => 'joins/' . $code));
                    } else {
                        $carrier_name = explode(':', $carrier->data->carrier->name);
                        $carrier_name = $carrier_name[0];
                        $contact['carrier']  = $carrier_name;
                        //$contact['location'] = $carrier->Message360->Carrier->City;
                    }
                
                }
                $contact['name'] = $fname . ' ' . $lname;
                $contact['phone_number'] = $phone;
                $contact['color'] = $this->choosecolor();
                $this->Contact->save($contact);
                $contactArr = $this->Contact->getLastInsertId();

                if ($users['User']['email_apikey'] != '' && $users['User']['email_listid'] != '' && $contact['email'] != '') {
                    if ($users['User']['email_service'] == 1) { //Mailchimp

                        $list_id = $users['User']['email_listid'];
                        $MailChimp = new MailChimp($users['User']['email_apikey']);

                        if ($contact['name'] != '') {

                            $result = $MailChimp->post("lists/$list_id/members", array(
                                'email_address' => $contact['email'],
                                'status' => 'subscribed',
                                'merge_fields' => array('FNAME' => $fname),
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

                        $newcontact = array(
                            'email' => $contact['email'],
                            'first_name' => $fname,
                            'phone' => $contact['phone_number'],
                            'p[{$list_id}]' => $list_id,
                            'status[{$list_id}]' => 1, // "Active" status
                        );

                        $contact_sync = $ac->api("contact/sync", $newcontact);

                        if (!(int)$contact_sync->success) {
                            // request failed
                            $this->Session->setFlash(__('Syncing contact to Active Campaign failed. Error returned: ' . $contact_sync->error, true));
                            $this->redirect(array('action' => 'joins/' . $code));
                        }

                    } else if ($users['User']['email_service'] == 4) { //AWeber

                        $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);
                        $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);

                        $account_id = $account->id;
                        $list_id = $users['User']['email_listid'];

                        $listURL = "/accounts/{$account_id}/lists/{$list_id}";
                        $list = $account->loadFromUrl($listURL);

                        $params = array(
                            'email' => $contact['email'],
                            'name' => $fname,
                        );

                        $subscribers = $list->subscribers;
                        $new_subscriber = $subscribers->create($params);

                    } else if ($users['User']['email_service'] == 5) { //Sendinblue

                        $mailin = new Mailin(SENDINBLUE_APIURL, $users['User']['email_apikey']);

                        $list_id = (int)$users['User']['email_listid'];

                        $data = array("email" => $contact['email'],
                            "attributes" => array("FIRSTNAME" => $fname),
                            "listid" => array($list_id)
                        );

                        $result = $mailin->create_update_user($data);

                    }


                }
            } else {
                $contactArr = $newsubscriber['Contact']['id'];
            }
            if ($contactArr != '') {
                app::import('Model', 'Smsloyalty');
                $this->Smsloyalty = new Smsloyalty();
                $smsloyalty_arr = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.user_id' => $user_id, 'Smsloyalty.id' => $kiosks['Kiosks']['loyalty_id'])));
                $group_id = $smsloyalty_arr['Smsloyalty']['group_id'];
                app::import('Model', 'Group');
                $this->Group = new Group();
                $keywordname = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                app::import('Model', 'ContactGroup');
                $this->ContactGroup = new ContactGroup();
                $subscriber11 = $this->ContactGroup->find('first', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.contact_id' => $contactArr, 'ContactGroup.group_id' => $group_id)));


                if (empty($subscriber11)) {
                    
                    if (!empty($newsubscriber)) {
                        $fname = '';
                        $lname = '';
                        if (isset($_POST['firstname'])) {
                            $fname = trim($_POST['firstname']);
                        }
                        if (isset($_POST['lastname'])) {
                            $lname = trim($_POST['lastname']);
                        }
                        if (isset($_POST['email'])) {
                            $contact['email'] = trim($_POST['email']);
                        }
                        if (isset($_POST['date'])) {
                            $birthday = date('Y-m-d', strtotime($_POST['date']));
                            $contact['birthday'] = $birthday;
                        }
                        if (isset($_POST['city'])) {
                            $contact['location'] = trim($_POST['city']);
                        }
                        if (isset($_POST['data']['state'])) {
                            $contact['state'] = trim($_POST['data']['state']);
                        }
                        if (isset($_POST['zip'])) {
                            $contact['zip'] = trim($_POST['zip']);
                        }
                        
                        $contact['name'] = $fname . ' ' . $lname;
                        $contact['id'] = $contactArr;
                        
                        if (NUMVERIFY != '') {
                            $numbervalidation = $this->validateNumber($phone);
                            $errorcode = $numbervalidation['error']['code'];
                            $valid = $numbervalidation['valid'];
                            $linetype = $numbervalidation['line_type'];

                            if ($errorcode == '') {
                                if ($valid != 1) {
                                    $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999 UK format: 449999999999";
                                    $this->Session->setFlash(__($err, true));
                                    $this->redirect(array('action' => 'joins/' . $code));
                                } else if (trim($linetype) != 'mobile') {
                                    $err = "The line type of the number entered is " . $linetype . ". You must provide a mobile number.";
                                    $this->Session->setFlash(__($err, true));
                                    $this->redirect(array('action' => 'joins/' . $code));
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
                                $carrier = $this->Ytel->lookupcarrier(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$phone);
                            }else{
                                $carrier = $this->Ytel->lookupcarrier($SID,$AUTHTOKEN,$phone);
                            }
                            
                            if ($carrier->Message360->ResponseStatus == 0) {
                                $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                                $this->Session->setFlash(__($err, true));
                                $this->redirect(array('action' => 'joins/' . $code));
                            } else if (trim($carrier->Message360->Carrier->Wireless) !='true') {
                                $err = "The line type of the number entered is not wireless. You must provide a mobile number.";
                                $this->Session->setFlash(__($err, true));
                                $this->redirect(array('action' => 'joins/' . $code));
                            } else {
                                $contact['carrier']  = $carrier->Message360->Carrier->Network;
                                $contact['location'] = $carrier->Message360->Carrier->City;
                            }
                        }elseif ($API_TYPE==7){
                
                            if ($SID == ''){
                                $carrier = $this->Telnyx->numberlookup(TELNYX_KEY,$phone);
                            }else{
                                $carrier = $this->Telnyx->numberlookup($SID,$phone);
                            }
                            
                            if(isset($carrier->errors)){
                                $err = "The phone number you entered is not valid. Please provide a valid working phone number with country code in the proper format. US format: 19999999999";
                                $this->Session->setFlash(__($err, true));
                                $this->redirect(array('action' => 'joins/' . $code));
                            } else if (trim($carrier->data->carrier->type) !='mobile') {
                                $err = "The line type of the number entered is not wireless. You must provide a mobile number.";
                                $this->Session->setFlash(__($err, true));
                                $this->redirect(array('action' => 'joins/' . $code));
                            } else {
                                $carrier_name = explode(':', $carrier->data->carrier->name);
                                $carrier_name = $carrier_name[0];
                                $contact['carrier']  = $carrier_name;
                                //$contact['location'] = $carrier->Message360->Carrier->City;
                            }
                
                        }
                        
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
                        $this->Contact->save($contact);
                    }

                    app::import('Model', 'ContactGroup');
                    $this->ContactGroup = new ContactGroup();
                    $contactgroup['ContactGroup']['user_id'] = $user_id;
                    $contactgroup['ContactGroup']['group_id'] = $group_id;
                    $contactgroup['ContactGroup']['group_subscribers'] = $keywordname['Group']['keyword'];
                    $contactgroup['ContactGroup']['subscribed_by_sms'] = 3;
                    $contactgroup['ContactGroup']['contact_id'] = $contactArr;
                    $contactgroup['ContactGroup']['created'] = date('Y-m-d H:i:s');
                    $this->ContactGroup->save($contactgroup);
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $groups = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    $groupArr['id'] = $groups['Group']['id'];
                    $groupArr['totalsubscriber'] = $groups['Group']['totalsubscriber'] + 1;
                    $this->Group->save($groupArr);
                    if ($groups['Group']['sms_type'] == 2) {
                        
                        $check=strpos($groups['Group']['image_url'],":");
						if($check!=''){
							$comma=strpos($groups['Group']['image_url'],",");
							if($comma!=''){
								$image_arr=explode(",",$groups['Group']['image_url']);
								$msg = "<p>" . $groups['Group']['system_message'] . ' ' . $groups['Group']['auto_message'] . '. Return home to check-in and add a point to your account now.</p><p>';
								foreach($image_arr as $value){
								    $img = '<img style="text-align:center;" width="100px;" height="100px;" src="' . $value . '" alt="" title="" />';
								    $img2 = $img2 . $img;
							    }
							    $msg = $msg . $img2 . '</p>';
							}else{ 
								$msg = "<p>" . $groups['Group']['system_message'] . ' ' . $groups['Group']['auto_message'] . '. Return home to check-in and add a point to your account now.</p><img style="text-align:center;" width="100px;" height="100px;" src="' . $groups['Group']['image_url'] . '" alt="" title="" />';
							}
						}
                        //$msg = "<p>" . $groups['Group']['system_message'] . ' ' . $groups['Group']['auto_message'] . '. Return home to check-in and add a point to your account now.</p><img style="text-align:center;" width="100px;" height="100px;" src="' . $groups['Group']['image_url'] . '" alt="" title="" />';
                    } else {
                        $msg = $groups['Group']['system_message'] . ' ' . $groups['Group']['auto_message'] . '. Return home to check-in and add a point to your account now.';
                    }

                    $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                    $API_TYPE = $user_arr['User']['api_type'];
                    $SID = trim($user_arr['User']['sid']);
                    $AUTHTOKEN = trim($user_arr['User']['authtoken']);
                    $BANDWIDTHUSERID = trim($user_arr['User']['bandwidthuserid']);
                    $SIGNALWIRESPACE = trim($user_arr['User']['signalwirespace']);
                    
                    $timezone = $user_arr['User']['timezone'];
                    date_default_timezone_set($timezone);
                    $date = date('Y-m-d H:i:s');


                    //*********** Save to activity timeline
                    app::import('Model', 'ActivityTimeline');
                    $this->ActivityTimeline = new ActivityTimeline();
                    $timeline['ActivityTimeline']['user_id'] = $user_id;
                    $timeline['ActivityTimeline']['contact_id'] = $contactArr;
                    $timeline['ActivityTimeline']['activity'] = 1;
                    $timeline['ActivityTimeline']['title'] = 'Contact Subscribed via Kiosk';
                    $timeline['ActivityTimeline']['description'] = $msg;
                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                    $this->ActivityTimeline->save($timeline);
                    //*************

                    if ($user_arr['User']['email_alert_options'] == 0) {
                        if ($user_arr['User']['email_alerts'] == 1) {
                            $username = $user_arr['User']['username'];
                            $email = $user_arr['User']['email'];
                            $subject = "New Subscriber via Kiosk to " . $keywordname['Group']['group_name'];
                            $sitename = str_replace(' ', '', SITENAME);
                            /*$this->Email->to = $email;
                            $this->Email->subject = $subject;
                            $this->Email->from = $sitename;
                            $this->Email->template = 'new_subscriber_template';
                            $this->Email->sendAs = 'html';
                            $this->Email->Controller->set('username', $username);
                            $this->Email->Controller->set('phoneno', $phone);
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
                            $Email->viewVars(array('phoneno' => $phone));
                            $Email->viewVars(array('groupname' => $keywordname['Group']['group_name']));
                            $Email->viewVars(array('keyword' => $keywordname['Group']['keyword']));
                            $Email->viewVars(array('datetime' => $date));
                            $Email->send();
                        }
                    }

                    if ($groups['Group']['notify_signup'] == 1) {
                        $mobile = $groups['Group']['mobile_number_input'];
                        $groupname = $groups['Group']['group_name'];
                        $message = "New Subscriber Alert: " . $phone . " has joined group " . $groupname;

                        if ($user_arr['User']['sms'] == 1) {
                            $from = $user_arr['User']['assigned_number'];
                        } else {
                            app::import('Model', 'UserNumber');
                            $this->UserNumber = new UserNumber();
                            $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.sms' => 1)));
                            if (!empty($user_numbers)) {
                                $from = $user_numbers['UserNumber']['number'];
                            } else {
                                $from = $user_arr['User']['assigned_number'];
                            }
                        }

                        if ($API_TYPE == 0) {
                            if ($SID == ''){
                                $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                            }else{
                                $this->Twilio->AccountSid = $SID;
                                $this->Twilio->AuthToken = $AUTHTOKEN;
                            }
                            $response = $this->Twilio->sendsms($mobile, $from, $message);
                        } elseif ($API_TYPE == 4) {
                            if ($SID == ''){
                                $response = $this->Bandwidth->sendsms(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$mobile,$from,$message,1);
                            }else{
                                $response = $this->Bandwidth->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$mobile,$from,$message,1);
                            }
                        } elseif ($API_TYPE == 5) {
                            if ($SID == ''){
                                $response = $this->Signalwire->sendsms(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$mobile,$from,$message);
                            }else{
                                $response = $this->Signalwire->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$mobile,$from,$message);
                            }
                        } elseif ($API_TYPE == 6) {
                            if ($SID == ''){
                                $response = $this->Ytel->sendsms(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$mobile,$from,$message);
                            }else{
                                $response = $this->Ytel->sendsms($SID,$AUTHTOKEN,$mobile,$from,$message);
                            }
                        } elseif ($API_TYPE == 7) {
                            if ($SID == ''){
                                $response = $this->Telnyx->sendsms(TELNYX_KEY,$mobile,$from,$message);
                            }else{
                                $response = $this->Telnyx->sendsms($SID,$mobile,$from,$message);
                            }
                        } elseif ($API_TYPE == 1) {
                            if ($SID == ''){
                                $this->Nexmomessage->Key = NEXMO_KEY;
                                $this->Nexmomessage->Secret = NEXMO_SECRET;
                            }else{
                                $this->Nexmomessage->Key = $SID;
                                $this->Nexmomessage->Secret = $AUTHTOKEN;
                            }
                            $this->Nexmomessage->sendsms($mobile, $from, $message);
                            sleep(1);
                        } elseif ($API_TYPE == 2) {
                            $this->Slooce->mt($user_arr['User']['api_url'], $user_arr['User']['partnerid'], $user_arr['User']['partnerpassword'], $mobile, $user_arr['User']['keyword'], $message);
                        } elseif ($API_TYPE == 3) {
                            if ($SID == ''){
                                $this->Plivo->AuthId = PLIVO_KEY;
                                $this->Plivo->AuthToken = PLIVO_TOKEN;
                            }else{
                                $this->Plivo->AuthId = $SID;
                                $this->Plivo->AuthToken = $AUTHTOKEN;
                            }
                            $response = $this->Plivo->sendsms($mobile, $from, $message);
                            sleep(1);
                        }
                        
                        $someone_users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

                        if (!empty($someone_users)) {
                            $user_credit['User']['sms_balance'] = $someone_users['User']['sms_balance'] - 1;
                            $user_credit['User']['id'] = $user_id;
                            $this->User->save($user_credit);
                        }
                        
                    }
                    $this->Immediatelyresponder($user_id, $group_id, $phone, $from);
                    $this->Session->setFlash(__($msg, true));
                } else {
                    $this->Session->setFlash(__('You are already subscribed with us and eligible to participate in our loyalty program. Please go back and check in.', true));
                }
                $this->redirect(array('action' => 'success/' . $code));
            } else {
                $this->Session->setFlash(__('Something is wrong. please try again', true));
            }
        }
    }

    function punchcard($code = null)
    {
        $this->layout = null;
        app::import('Model', 'Kiosks');
        $this->Kiosks = new Kiosks();
        $kiosks = $this->Kiosks->find('first', array('conditions' => array('Kiosks.unique_id' => $code), 'order' => array('Kiosks.id' => 'asc')));
        $this->set('code', $code);
        $this->set('kiosks', $kiosks);
        if (!empty($_POST)) {

            app::import('Model', 'ActivityTimeline');
            $this->ActivityTimeline = new ActivityTimeline();
            $phone = $_POST['numberval'];
            $user_id = $kiosks['Kiosks']['user_id'];
            $loyalty_id = $kiosks['Kiosks']['loyalty_id'];
            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();
            $smsloyalty_arr = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.user_id' => $user_id, 'Smsloyalty.id' => $loyalty_id)));
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();

            $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $timezone = $user_arr['User']['timezone'];
            date_default_timezone_set($timezone);
            $contactgroupid = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.group_id' => $smsloyalty_arr['Smsloyalty']['group_id'], 'ContactGroup.user_id' => $user_id)));
            if (!empty($contactgroupid)) {
                $current_date = date('Y-m-d');
                if ($smsloyalty_arr['Smsloyalty']['startdate'] > $current_date) {
                    $message = "Loyalty program " . $smsloyalty_arr['Smsloyalty']['program_name'] . " hasn't started yet. It begins on " . date('m/d/Y', strtotime($smsloyalty_arr['Smsloyalty']['startdate'])) . "";
                    $this->Session->setFlash(__($message, true));
                    $this->redirect(array('action' => 'success/' . $code));
                } else if ($smsloyalty_arr['Smsloyalty']['enddate'] < $current_date) {
                    $message = "Loyalty program " . $smsloyalty_arr['Smsloyalty']['program_name'] . " ended on " . date('m/d/Y', strtotime($smsloyalty_arr['Smsloyalty']['enddate'])) . "";
                    $this->Session->setFlash(__($message, true));
                    $this->redirect(array('action' => 'success/' . $code));
                } else {
                    $currentdate = date('Y-m-d');
                    app::import('Model', 'SmsloyaltyUser');
                    $this->SmsloyaltyUser = new SmsloyaltyUser();
                    $loyaltyuser = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 0), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                    if (empty($loyaltyuser)) {
                        $loyaltyuserredeem = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 1, 'SmsloyaltyUser.msg_date' => $currentdate), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                        if (empty($loyaltyuserredeem)) {
                            $loyalty_user['SmsloyaltyUser']['id'] = '';
                            $loyalty_user['SmsloyaltyUser']['unique_key'] = $this->unique_code(10);
                            $loyalty_user['SmsloyaltyUser']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                            $loyalty_user['SmsloyaltyUser']['sms_loyalty_id'] = $smsloyalty_arr['Smsloyalty']['id'];
                            $loyalty_user['SmsloyaltyUser']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                            $loyalty_user['SmsloyaltyUser']['keyword'] = '';
                            $loyalty_user['SmsloyaltyUser']['count_trial'] = 1;
                            $loyalty_user['SmsloyaltyUser']['msg_date'] = $currentdate;
                            $loyalty_user['SmsloyaltyUser']['created'] = date('Y-m-d H:i:s');
                            if ($smsloyalty_arr['Smsloyalty']['reachgoal'] == 1) {
                                $loyalty_user['SmsloyaltyUser']['is_winner'] = 1;
                                if ($this->SmsloyaltyUser->save($loyalty_user)) {
                                    if ($smsloyalty_arr['Smsloyalty']['type'] == 1) {
                                        $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                        $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                        $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyalty_user['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                                        $sms = $msg . ' ' . $redeem;

                                        //*********** Save to activity timeline
                                        $timeline['ActivityTimeline']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                        $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                        $timeline['ActivityTimeline']['activity'] = 7;
                                        $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                        $timeline['ActivityTimeline']['description'] = $sms;
                                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                        $this->ActivityTimeline->save($timeline);
                                        //*************

                                        $this->Session->setFlash(__($sms, true));
                                        $this->redirect(array('action' => 'success/' . $code));
                                    } else if ($smsloyalty_arr['Smsloyalty']['type'] == 2) {
                                        $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                        $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                        $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyalty_user['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                                        $image = '<br/><br/><img style="text-align:center;" width="100px;" height="100px;" src="' . SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'] . '" alt="" title="" />';
                                        $sms = $msg . ' ' . $redeem . ' ' . $image;

                                        //*********** Save to activity timeline
                                        $timeline['ActivityTimeline']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                        $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                        $timeline['ActivityTimeline']['activity'] = 7;
                                        $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                        $timeline['ActivityTimeline']['description'] = $sms;
                                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                        $this->ActivityTimeline->save($timeline);
                                        //*************

                                        $this->Session->setFlash(__($sms, true));
                                        $this->redirect(array('action' => 'success/' . $code));
                                    }
                                }
                            } else {
                                $this->SmsloyaltyUser->save($loyalty_user);
                                $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['addpoints']);
                                $msg = str_replace('%%STATUS%%', 1, $message);

                                //*********** Save to activity timeline
                                $timeline['ActivityTimeline']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                $timeline['ActivityTimeline']['activity'] = 5;
                                $timeline['ActivityTimeline']['title'] = 'Loyalty Program Punch';
                                $timeline['ActivityTimeline']['description'] = $msg;
                                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                $this->ActivityTimeline->save($timeline);
                                //*************
                                $this->Session->setFlash(__($msg, true));
                                $this->redirect(array('action' => 'success/' . $code));
                            }
                        } else {
                            $message = "You have already redeemed your reward today.";
                            $this->Session->setFlash(__($message, true));
                            $this->redirect(array('action' => 'success/' . $code));
                        }
                    } else if ($loyaltyuser['SmsloyaltyUser']['msg_date'] < $currentdate) {
                        $count_trial = $loyaltyuser['SmsloyaltyUser']['count_trial'] + 1;
                        
                        /***08/11/2018*****/
                        if ($count_trial > $smsloyalty_arr['Smsloyalty']['reachgoal'] && $loyaltyuser['SmsloyaltyUser']['is_winner'] == 1){
                            $message = "You have already reached the goal of " . $smsloyalty_arr['Smsloyalty']['reachgoal'] . " points.";
                            $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyaltyuser['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                            $sms = $message . ' ' . $redeem;
                            $this->Session->setFlash(__($sms, true));
                            $this->redirect(array('action' => 'success/' . $code));
                        }
                        /******************/
                            
                        $loyalty_user['SmsloyaltyUser']['id'] = $loyaltyuser['SmsloyaltyUser']['id'];
                        $loyalty_user['SmsloyaltyUser']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                        $loyalty_user['SmsloyaltyUser']['sms_loyalty_id'] = $smsloyalty_arr['Smsloyalty']['id'];
                        $loyalty_user['SmsloyaltyUser']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                        $loyalty_user['SmsloyaltyUser']['keyword'] = '';
                        $loyalty_user['SmsloyaltyUser']['count_trial'] = $count_trial;
                        $loyalty_user['SmsloyaltyUser']['msg_date'] = $currentdate;
                        $loyalty_user['SmsloyaltyUser']['created'] = date('Y-m-d H:i:s');
                        if ($this->SmsloyaltyUser->save($loyalty_user)) {
                            $loyaltyuser_list = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 0), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                            if ($loyaltyuser_list['SmsloyaltyUser']['count_trial'] == $smsloyalty_arr['Smsloyalty']['reachgoal']) {
                                $loyalty_user_arr['SmsloyaltyUser']['id'] = $loyaltyuser['SmsloyaltyUser']['id'];
                                $loyalty_user_arr['SmsloyaltyUser']['is_winner'] = 1;
                                if ($this->SmsloyaltyUser->save($loyalty_user_arr)) {
                                    if ($smsloyalty_arr['Smsloyalty']['type'] == 1) {
                                        $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                        $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                        $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyaltyuser_list['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                                        $sms = $msg . ' ' . $redeem;

                                        //*********** Save to activity timeline
                                        $timeline['ActivityTimeline']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                        $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                        $timeline['ActivityTimeline']['activity'] = 7;
                                        $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                        $timeline['ActivityTimeline']['description'] = $sms;
                                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                        $this->ActivityTimeline->save($timeline);
                                        //*************

                                        $this->Session->setFlash(__($sms, true));
                                        $this->redirect(array('action' => 'success/' . $code));
                                    } else if ($smsloyalty_arr['Smsloyalty']['type'] == 2) {
                                        $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                        $msg = str_replace('%%STATUS%%', $count_trial, $message);
                                        $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyaltyuser_list['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                                        $image = '<br/><br/><img style="text-align:center;" width="100px;" height="100px;" src="' . SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'] . '" alt="" title="" />';
                                        $sms = $msg . ' ' . $redeem . ' ' . $image;

                                        //*********** Save to activity timeline
                                        $timeline['ActivityTimeline']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                        $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                        $timeline['ActivityTimeline']['activity'] = 7;
                                        $timeline['ActivityTimeline']['title'] = 'Loyalty Program Winner';
                                        $timeline['ActivityTimeline']['description'] = $sms;
                                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                        $this->ActivityTimeline->save($timeline);
                                        //*************

                                        $this->Session->setFlash(__($sms, true));
                                        $this->redirect(array('action' => 'success/' . $code));
                                    }
                                }
                            } else {
                                $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['addpoints']);
                                $msg = str_replace('%%STATUS%%', $count_trial, $message);

                                //*********** Save to activity timeline
                                $timeline['ActivityTimeline']['user_id'] = $smsloyalty_arr['Smsloyalty']['user_id'];
                                $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                                $timeline['ActivityTimeline']['activity'] = 5;
                                $timeline['ActivityTimeline']['title'] = 'Loyalty Program Punch';
                                $timeline['ActivityTimeline']['description'] = $msg;
                                $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                                $this->ActivityTimeline->save($timeline);
                                //*************
                                $this->Session->setFlash(__($msg, true));
                                $this->redirect(array('action' => 'success/' . $code));
                            }
                        }
                    } else if ($loyaltyuser['SmsloyaltyUser']['is_winner'] == 1) {
                        $message = "You have already reached the goal of " . $smsloyalty_arr['Smsloyalty']['reachgoal'] . " points.";
                        $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyaltyuser['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                        $sms = $message . ' ' . $redeem;
                        $this->Session->setFlash(__($sms, true));
                        $this->redirect(array('action' => 'success/' . $code));
                    } else {
                        $message = "You already punched your card today. Stop in tomorrow to add another punch.";
                        $this->Session->setFlash(__($message, true));
                        $this->redirect(array('action' => 'success/' . $code));
                    }
                }
            } else {
                $message = "You are not eligible to participate since you are not subscribed to our opt-in list. Please click Home to go back and join our opt-in list.";
                $this->Session->setFlash(__($message, true));
                $this->redirect(array('action' => 'success/' . $code));
            }
        }
    }

    function checkpoints($code = null)
    {
        $this->layout = null;
        app::import('Model', 'Kiosks');
        $this->Kiosks = new Kiosks();
        $kiosks = $this->Kiosks->find('first', array('conditions' => array('Kiosks.unique_id' => $code), 'order' => array('Kiosks.id' => 'asc')));
        $this->set('code', $code);
        $this->set('kiosks', $kiosks);
        if (!empty($_POST)) {
            app::import('Model', 'ActivityTimeline');
            $this->ActivityTimeline = new ActivityTimeline();
            $phone = $_POST['numberval'];
            $user_id = $kiosks['Kiosks']['user_id'];
            $loyalty_id = $kiosks['Kiosks']['loyalty_id'];
            app::import('Model', 'Smsloyalty');
            $this->Smsloyalty = new Smsloyalty();
            $smsloyalty_arr = $this->Smsloyalty->find('first', array('conditions' => array('Smsloyalty.user_id' => $user_id, 'Smsloyalty.id' => $loyalty_id)));
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();

            $user_arr = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $timezone = $user_arr['User']['timezone'];
            date_default_timezone_set($timezone);
            $contactgroupid = $this->ContactGroup->find('first', array('conditions' => array('Contact.phone_number' => $phone, 'ContactGroup.group_id' => $smsloyalty_arr['Smsloyalty']['group_id'], 'ContactGroup.user_id' => $user_id)));
            if (!empty($contactgroupid)) {
                $current_date = date('Y-m-d');
                if ($smsloyalty_arr['Smsloyalty']['startdate'] > $current_date) {
                    $message = "Loyalty program " . $smsloyalty_arr['Smsloyalty']['program_name'] . " hasn't started yet. It begins on " . date('m/d/Y', strtotime($smsloyalty_arr['Smsloyalty']['startdate'])) . "";
                    $this->Session->setFlash(__($message, true));
                    $this->redirect(array('action' => 'success/' . $code));
                } else if ($smsloyalty_arr['Smsloyalty']['enddate'] < $current_date) {
                    $message = "Loyalty program " . $smsloyalty_arr['Smsloyalty']['program_name'] . " ended on " . date('m/d/Y', strtotime($smsloyalty_arr['Smsloyalty']['enddate'])) . "";
                    $this->Session->setFlash(__($message, true));
                    $this->redirect(array('action' => 'success/' . $code));
                } else {
                    $currentdate = date('Y-m-d');
                    app::import('Model', 'SmsloyaltyUser');
                    $this->SmsloyaltyUser = new SmsloyaltyUser();
                    $loyaltyuser = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 0), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                    if (!empty($loyaltyuser)) {
                        $loyaltyuser_list = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 0), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                        if ($loyaltyuser_list['SmsloyaltyUser']['count_trial'] == $smsloyalty_arr['Smsloyalty']['reachgoal']) {
                            if ($smsloyalty_arr['Smsloyalty']['type'] == 1) {
                                $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                $msg = str_replace('%%STATUS%%', $loyaltyuser_list['SmsloyaltyUser']['count_trial'], $message);
                                $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyaltyuser_list['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                                $sms = $msg . ' ' . $redeem;
                                $this->Session->setFlash(__($sms, true));
                            } else if ($smsloyalty_arr['Smsloyalty']['type'] == 2) {
                                $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['reachedatgoal']);
                                $msg = str_replace('%%STATUS%%', $loyaltyuser_list['SmsloyaltyUser']['count_trial'], $message);
                                $redeem = "Click link to redeem <a href='" . SITE_URL . "/users/redeem/" . $loyaltyuser_list['SmsloyaltyUser']['unique_key'] . "/" . $code . "'><b>>> Click here <<</b></a>";
                                $image = '<img style="text-align:center;" width="100px;" height="100px;" src="' . SITE_URL . '/mms/' . $smsloyalty_arr['Smsloyalty']['image'] . '" alt="" title="" />';
                                $sms = $msg . ' ' . $redeem . ' ' . $image;
                                $this->Session->setFlash(__($sms, true));
                            }
                            $timeline['ActivityTimeline']['description'] = $sms;
                        } else {
                            $message = str_replace('%%Name%%', $contactgroupid['Contact']['name'], $smsloyalty_arr['Smsloyalty']['addpoints']);
                            $msg = str_replace('%%STATUS%%', $loyaltyuser_list['SmsloyaltyUser']['count_trial'], $message);
                            $timeline['ActivityTimeline']['description'] = $msg;
                            $this->Session->setFlash(__($msg, true));
                        }

                        //*********** Save to activity timeline
                        $timeline['ActivityTimeline']['user_id'] = $user_id;
                        $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                        $timeline['ActivityTimeline']['activity'] = 6;
                        $timeline['ActivityTimeline']['title'] = 'Check Loyalty Status';
                        //$timeline['ActivityTimeline']['description'] = $message;
                        $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                        $this->ActivityTimeline->save($timeline);
                        //*************

                        $this->set('trial_count', $loyaltyuser_list['SmsloyaltyUser']['count_trial']);
                        $this->set('total_count', $smsloyalty_arr['Smsloyalty']['reachgoal']);
                    } else {
                        $loyaltyuser = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.contact_id' => $contactgroupid['ContactGroup']['contact_id'], 'SmsloyaltyUser.sms_loyalty_id' => $smsloyalty_arr['Smsloyalty']['id'], 'SmsloyaltyUser.redemptions' => 1), 'order' => array('SmsloyaltyUser.msg_date' => 'desc')));
                        if (!empty($loyaltyuser)) {
                            $message = "You have already redeemed your reward. Please click Home and start a new card by adding a punch.";
                            $this->Session->setFlash(__($message, true));
                            $this->redirect(array('action' => 'success/' . $code));
                        } else {
                            $message = "You currently have 0 points. Click Home to go back and add a point on your card.";
                            $this->Session->setFlash(__($message, true));
                            $this->set('trial_count', 0);
                            $this->set('total_count', $smsloyalty_arr['Smsloyalty']['reachgoal']);

                            //*********** Save to activity timeline
                            $timeline['ActivityTimeline']['user_id'] = $user_id;
                            $timeline['ActivityTimeline']['contact_id'] = $contactgroupid['ContactGroup']['contact_id'];
                            $timeline['ActivityTimeline']['activity'] = 6;
                            $timeline['ActivityTimeline']['title'] = 'Check Loyalty Status';
                            $timeline['ActivityTimeline']['description'] = $message;
                            $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                            $this->ActivityTimeline->save($timeline);
                            //*************
                        }
                    }
                }
            } else {
                $message = "You are not eligible to participate since you are not subscribed to our opt-in list. Please click Home to go back and join our opt-in list.";
                $this->Session->setFlash(__($message, true));
                $this->redirect(array('action' => 'success/' . $code));
            }
        }
    }

    function unique_code($digits)
    {
        $this->autoRender = false;
        srand((double)microtime() * 10000000);
        $input = array("A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        //$input = array("0","1","2","3","4","5","6","7","8","9");
        $random_generator = "";// Initialize the string to store random numbers
        for ($i = 1; $i < $digits + 1; $i++) {
            // Loop the number of times of required digits

            if (rand(1, 2) == 1) {// to decide the digit should be numeric or alphabet
                $rand_index = array_rand($input);
                $random_generator .= $input[$rand_index]; // One char is added
            } else {
                $random_generator .= rand(1, 7); // one number is added
            }
        } // end of for loop
        return $random_generator;
    } // end of function


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
                if ($users['User']['sms_balance'] > 0)
                    $Responderid = $response['Responder']['id'];
                    $group_id = $response['Responder']['group_id'];
                    $sms_type = $response['Responder']['sms_type'];
                    $image_url = $response['Responder']['image_url'];
                    $message = $response['Responder']['message'];
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

?>