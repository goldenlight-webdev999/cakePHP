<?php
//**** Use Composer
//App::import('Vendor','stripe',array('file' => 'stripe/autoload.php')); 

//**** Manually load required dependent files
App::import('Vendor', 'stripe', array('file' => 'stripe/stripe/stripe-php/init.php'));
App::import('Vendor', 'mailchimp', array('file' => 'mailchimp/MailChimp.php'));
App::import('Vendor', 'getresponse', array('file' => 'getresponse/GetResponse.php'));
App::import('Vendor', 'activecampaign', array('file' => 'activecampaign/ActiveCampaign.class.php'));
App::import('Vendor', 'aweber', array('file' => 'aweber/aweber_api.php'));
App::import('Vendor', 'mailin', array('file' => 'mailin/Mailin.php'));

include('./accounts.php');
include('./alphacountrypermissions.php');
include('./emailcheck.php');

App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController
{
    var $name = 'Users';
    //var $components = array('Captcha', 'Cookie', 'Email', 'Twilio', 'Paginationclass', 'Qr', 'Checkout', 'Expresscheckout', 'Nexmo', 'Plivo');
    var $components = array('Captcha', 'Cookie', 'Twilio', 'Paginationclass', 'Qr', 'Nexmo', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel', 'Telnyx');

    //function captcha_check($userCode)
    //{
    //    $this->autoRender = false;
    //    return $this->Captcha->check($userCode);
    //}

    function captcha_image()
    {
        $this->autoRender = false;
        $this->Captcha->image();

    }

    function captcha_audio()
    {
        $this->autoRender = false;
        $this->Captcha->audio();
    }

    function home()
    {
        $this->set('title_for_layout', 'Home');
        $this->layout = 'home';
    }

    /*User Login*/
    function autologin($user_id, $password)
    {
        $this->autoRender = false;
        $user_id = base64_decode($user_id);
        $password = base64_decode($password);
        $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.password' => $password)));
        if (!empty($someone)) {
            $this->Session->write('User', $someone['User']);
            $pay_activation_fee = PAY_ACTIVATION_FEES;

            if ($someone['User']['active'] == 0 && $pay_activation_fee == 1) {
                $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
            } else {
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            }
        }
    }

    /*User Login*/
    function login()
    {
        $this->set('title_for_layout', 'Login');
        $this->layout = 'login_layout';

        /*if(!empty($this->request->data)){
			$this->User->set($this->request->data);
			$this->User->validationSet = 'Userlogin';
			if ($this->User->validates()){
				$someone = $this->User->find('first', array('conditions' => array('username' =>$this->request->data['User']['usrname'])));
				if($someone['User']['register']==1){
					if(!empty($someone['User']['password']) && $someone['User']['password'] == md5($this->request->data['User']['passwrd'])){

						$this->Session->write('User', $someone['User']);

						$pay_activation_fee=PAY_ACTIVATION_FEES;

						if($someone['User']['active'] == 0 && $pay_activation_fee == 1){
							$this->redirect(array('controller' =>'users', 'action' => 'dashboard'));
						}else{
							$this->redirect(array('controller' =>'users', 'action' => 'profile'));
						}
					}else{
						$this->Session->setFlash('Username or Password Wrong');
					}
				}else{
					$this->Session->setFlash('You must complete the register process before logging in.');
				}
			}else{
	            $this->Session->setFlash('Username or Password Wrong');
			}
		}*/

        if (!empty($this->request->data)) {
            $this->User->set($this->request->data);
            $this->User->validationSet = 'Userlogin';
            app::import('Model', 'Subaccount');
            $this->Subaccount = new Subaccount();
            $this->Subaccount->set($this->request->data);
            $this->Subaccount->validationSet = 'Userlogin';

            if ($this->User->validates() || $this->Subaccount->validates()) {
                $someone = $this->User->find('first', array('conditions' => array('username' => $this->request->data['User']['usrname'])));
                $subaccount = $this->Subaccount->find('first', array('conditions' => array('username' => $this->request->data['User']['usrname'])));
                if (empty($someone) && empty($subaccount)) {
                    $this->Session->setFlash('Username or Password Wrong');
                } elseif (!empty($someone)) {
                    if ($someone['User']['register'] == 1) {
                        if (!empty($someone['User']['password']) && $someone['User']['password'] == md5($this->request->data['User']['passwrd'])) {

                            $this->Session->write('User', $someone['User']);
                            $this->User->id = $someone['User']['id'];
                            $this->User->saveField('lastlogin_username', $someone['User']['username']);
                            $this->User->saveField('lastlogin_datetime', date('Y-m-d H:i:s'));

                            $pay_activation_fee = PAY_ACTIVATION_FEES;

                            if ($someone['User']['active'] == 0 && $pay_activation_fee == 1) {
                                $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
                            } else {
                                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
                            }
                        } else {
                            $this->Session->setFlash('Username or Password Wrong');
                        }
                    } else {
                        $this->Session->setFlash('You must complete the register process before logging in.');
                    }
                } elseif (!empty($subaccount)) {
                    if (!empty($subaccount['Subaccount']['password']) && $subaccount['Subaccount']['password'] == md5($this->request->data['User']['passwrd'])) {

                        $user = $this->User->find('first', array('conditions' => array('User.id' => $subaccount['Subaccount']['user_id'])));
                        $this->Session->write('User', $user['User']);
                        $this->Session->write('Subaccount', $subaccount['Subaccount']);
                        
                        $this->User->id = $user['User']['id'];
                        $this->User->saveField('lastlogin_username', $subaccount['Subaccount']['username']);
                        $this->User->saveField('lastlogin_datetime', date('Y-m-d H:i:s'));

                        $this->redirect(array('controller' => 'users', 'action' => 'profile'));

                    } else {
                        $this->Session->setFlash('Username or Password Wrong');
                    }
                }
            } else {
                $this->Session->setFlash('Username or Password Wrong');
            }
        }
    }

    function activation($id = null)
    {
        $this->layout = 'admin_new_layout';
        $this->set('id', $id);
        if (isset($_POST['stripeToken'])) {
            if ($_POST['stripeToken'] != '') {
                $amount = REGISTRATION_CHARGE * 100;
                $desc = SITENAME . ' Membership Activation';
                $setApiKey = SECRET_KEY;
                $currency = PAYMENT_CURRENCY_CODE;
                \Stripe\Stripe::setApiKey(SECRET_KEY);
                try {
                    $charge = \Stripe\Charge::create(array(
                            "amount" => REGISTRATION_CHARGE * 100,
                            "currency" => $currency,
                            "description" => $desc,
                            "source" => $_POST['stripeToken']
                        )
                    );
                    if (isset($charge->id)) {
                        $this->request->data['User']['id'] = $this->Session->read('User.id');
                        $this->request->data['User']['sms_balance'] = FREE_SMS;
                        $this->request->data['User']['voice_balance'] = FREE_VOICE;
                        $this->request->data['User']['active'] = 1;
                        $this->User->save($this->request->data);

                        app::import('Model', 'Invoice');
                        $this->Invoice = new Invoice();
                        $invoice['user_id'] = $this->Session->read('User.id');
                        $invoice['txnid'] = $charge->id;
                        $invoice['type'] = 1;
                        $invoice['package_name'] = 'Activation Fee';
                        $invoice['amount'] = REGISTRATION_CHARGE;
                        $invoice['created'] = date("Y-m-d");
                        $this->Invoice->save($invoice);

                        $users = $this->getLoggedUserDetails();
                        //$this->Session->delete('User');
                        //$this->Session->write('User', $user['User']);

                        $this->Session->write('User.sms_balance', $users['User']['sms_balance']);
                        $this->Session->write('User.assigned_number', $users['User']['assigned_number']);
                        $this->Session->write('User.pay_activation_fees_active', $users['User']['pay_activation_fees_active']);
                        $this->Session->write('User.active', $users['User']['active']);

                        app::import('Model', 'Referral');
                        $this->Referral = new Referral();
                        $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $this->Session->read('User.id'))));
                        if (!empty($Referraldetails)) {
                            $referral['id'] = $Referraldetails['Referral']['id'];
                            $referral['account_activated'] = 1;
                            $this->Referral->save($referral);
                        }
                        $balance1 = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));    //-------------------------------------------------------------------------------//
                        //Activation mail function
                        /*$sitename = str_replace(' ', '', SITENAME);
                        $this->Email->to = $balance1['User']['email'];
                        $this->Email->subject = 'Account has been activated';
                        $this->Email->from = $sitename;
                        $this->Email->template = 'membership'; // note no '.ctp'
                        $this->Email->sendAs = 'html'; // because we like to send pretty mail
                        $this->set('data', $balance1);
                        $this->Email->send();*/
                        
                        $Email = new CakeEmail();
                        if(EMAILSMTP==1){                     
                            $Email->config('smtp');       
                        }
                        $Email->from(array(SUPPORT_EMAIL => SITENAME));
                        $Email->to($balance1['User']['email']);
                        $Email->subject('Account has been activated');
                        $Email->template('membership');
                        $Email->emailFormat('html');
                        $Email->viewVars(array('data' => $balance1));
                        $Email->send();
                        
                        //------------------------------------------------------------------//
                        $this->Session->setFlash(__('Thank you for activating your account.', true));
                    }
                    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
                } catch (\Stripe\Error\Card $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
                } catch (\Stripe\Error\RateLimit $e) {
                    // Too many requests made to the API too quickly
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
                } catch (\Stripe\Error\InvalidRequest $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
                } catch (\Stripe\Error\Authentication $e) {
                    // Authentication with Stripe's API failed
                    // (maybe you changed API keys recently)
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
                } catch (\Stripe\Error\ApiConnection $e) {
                    // Network communication with Stripe failed
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
                } catch (\Stripe\Error\Base $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
                } catch (Exception $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));

                }

            } else {
                $this->Session->setFlash('Please try again');
                $this->redirect(array('controller' => 'users', 'action' => 'activation/' . $id));
            }
        }
    }

    function thank_you()
    {
        $this->layout = 'admin_new_layout';
        //print_r($_POST);exit;
    }

    /*
	User frontend Logout
	*/
    function logout()
    {
        $this->Session->delete('User');
        $this->Session->delete('Token');
        $this->Session->delete('TwitterSuccess');
        $this->Session->delete('Subaccount');
        $this->Session->delete('setsqlmode');
        $this->Session->delete('adminstats');
        if (LOGOUT_URL != '') {
            $this->redirect(LOGOUT_URL);
        } else {
            $this->redirect('/');
        }
    }

    function forgot_password()
    {
        $this->set('title_for_layout', 'Forgot Password');
        $this->layout = 'login_layout';
        if (!empty($this->request->data)) {
            if ($this->request->data['User']['email'] != "") {
                $someone = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['User']['email'])));
                if (!empty($someone)) {
                    $email = $this->request->data['User']['email'];
                    $id = $someone['User']['id'];
                    $sitename = str_replace(' ', '', SITENAME);
                    $first_name = $someone['User']['first_name'];
                    $subject = SITENAME . " :: Forgot Password";
                    $comment = SITE_URL . "/users/pwdreset/" . $id . "/" . $someone['User']['password'];
                    /*$this->Email->to = $email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $sitename;
                    $this->Email->template = 'forgot_password';
                    $this->Email->sendAs = 'html';
                    $this->Email->Controller->set('first_name', $first_name);
                    $this->Email->Controller->set('comment', $comment);
                    $this->Email->Controller->set('email', $email);
                    $this->Email->send();*/
                    
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){                                                  
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($email);
                    $Email->subject($subject);
                    $Email->template('forgot_password');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('first_name' => $first_name));
                    $Email->viewVars(array('comment' => $comment));
                    $Email->viewVars(array('email' => $email));
                    $Email->send();
                        
                    $this->Session->setFlash('Please check your email for instructions to reset your password');
                } else {
                    $this->Session->setFlash('We do not have any accounts registered with that email.');
                }
            } else {
                $this->User->invalidate('email', 'Please enter your Email id');
            }
        }
    }//fun

    function pwdreset($id, $code)
    {
        $someone = $this->User->find('first', array('conditions' => array('User.id' => $id, 'User.password' => $code)));
        //print_r($someone);
        if (!empty($someone)) {
            /*****************************Random number**********************************************/
            function random_generator($digits)
            {
                srand((double)microtime() * 10000000);
                //Array of alphabets
                $input = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q",
                    "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

                $random_generator = "";// Initialize the string to store random numbers
                for ($i = 1; $i < $digits + 1; $i++) { // Loop the number of times of required digits
                    if (rand(1, 2) == 1) {// to decide the digit should be numeric or alphabet
                        // Add one random alphabet
                        $rand_index = array_rand($input);
                        $random_generator .= $input[$rand_index]; // One char is added

                    } else {
                        // Add one numeric digit between 1 and 10
                        $random_generator .= rand(1, 10); // one number is added
                    } // end of if else

                } // end of for loop

                return $random_generator;
            } // end of function
            /******************************************************************************************/

            $random_number = random_generator(10);
            $username = $someone['User']['username'];
            $email = $someone['User']['email'];
            $first_name = $someone['User']['first_name'];
            $sitename = str_replace(' ', '', SITENAME);
            $subject = SITENAME . " :: New Password";
            /*$this->Email->to = $email;
            $this->Email->subject = $subject;
            $this->Email->from = $sitename;
            $this->Email->template = 'pwdreset';
            $this->Email->sendAs = 'html';
            $this->Email->Controller->set('first_name', $first_name);
            $this->Email->Controller->set('username', $username);
            $this->Email->Controller->set('password', $random_number);
            $this->Email->Controller->set('email', $email);
            $this->Email->send();*/
            
            $Email = new CakeEmail();
            if(EMAILSMTP==1){                                                  
                $Email->config('smtp');
            }
            $Email->from(array(SUPPORT_EMAIL => SITENAME));
            $Email->to($email);
            $Email->subject($subject);
            $Email->template('pwdreset');
            $Email->emailFormat('html');
            $Email->viewVars(array('first_name' => $first_name));
            $Email->viewVars(array('username' => $username));
            $Email->viewVars(array('password' => $random_number));
            $Email->viewVars(array('email' => $email));
            $Email->send();

            $this->User->id = $id;
            $this->User->saveField('password', md5("$random_number"));
            $this->Session->setFlash('Your new password has now been sent to your email. Use this password to login');

            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        } else {
            $this->Session->setFlash('Invalid information');
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
    }

    function add()
    {
        $this->set('title_for_layout', 'Register');
        $this->layout = 'login_layout';


        if (!empty($this->request->data)) {

            $accounts = $this->User->find('count', array('conditions' => array('User.active' => 1)));

            if ($accounts >= NUMACCOUNTS) {

                $sitename = str_replace(' ', '', SITENAME);
                $subject = SITENAME . " :: Account Limit Reached";
                /*$this->Email->to = SUPPORT_EMAIL;
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
                
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to(SUPPORT_EMAIL);
                $Email->subject($subject);
                $Email->template('account_limit_notice');
                $Email->emailFormat('html');
                $Email->viewVars(array('firstname' => $this->request->data['User']['first_name']));
                $Email->viewVars(array('lastname' => $this->request->data['User']['last_name']));
                $Email->viewVars(array('username' => $this->request->data['User']['username']));
                $Email->viewVars(array('email' => $this->request->data['User']['email']));
                $Email->viewVars(array('phone' => $this->request->data['User']['phone']));
                $Email->send();

                $this->Session->setFlash('System has reached maximum number of active accounts. Please contact us to increase capacity.');
                $this->redirect(array('controller' => 'users', 'action' => 'add'));
            }

            function random_generator($digits)
            {
                srand((double)microtime() * 10000000);
                //Array of alphabets
                $input = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q",
                    "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

                $random_generator = "";// Initialize the string to store random numbers
                for ($i = 1; $i < $digits + 1; $i++) { // Loop the number of times of required digits
                    if (rand(1, 2) == 1) {// to decide the digit should be numeric or alphabet
                        // Add one random alphabet
                        $rand_index = array_rand($input);
                        $random_generator .= $input[$rand_index]; // One char is added

                    } else {
                        // Add one numeric digit between 1 and 10
                        $random_generator .= rand(1, 10); // one number is added
                    } // end of if else

                } // end of for loop

                return $random_generator;
            } // end of function

            $phone = $this->request->data['User']['phone'];

            if(REQUIRE_REAL_EMAIL==1){
                if (disposablecheck($this->request->data['User']['email']) == 1) { 
                    $this->Session->setFlash('*** You MUST use a real domain related email address. Free email accounts from Gmail, Yahoo, etc... are NOT allowed. ***');
                    $this->redirect(array('controller' => 'users', 'action' => 'add'));
                }
            }

            $this->User->create();

            $country = $this->request->data['User']['user_country'];

            if ($country == "Australia") {
                $this->request->data['User']['alphasender'] = AUSTRALIA;
            } else if ($country == "Austria") {
                $this->request->data['User']['alphasender'] = AUSTRIA;
            } else if ($country == "Belgium") {
                $this->request->data['User']['alphasender'] = BELGIUM;
            } else if ($country == "Canada") {
                $this->request->data['User']['alphasender'] = CANADA;
            } else if ($country == "Denmark") {
                $this->request->data['User']['alphasender'] = DENMARK;
            } else if ($country == "Estonia") {
                $this->request->data['User']['alphasender'] = ESTONIA;
            } else if ($country == "Finland") {
                $this->request->data['User']['alphasender'] = FINLAND;
            } else if ($country == "France") {
                $this->request->data['User']['alphasender'] = FRANCE;
            } else if ($country == "Germany") {
                $this->request->data['User']['alphasender'] = GERMANY;
            } else if ($country == "Hong Kong") {
                $this->request->data['User']['alphasender'] = HONGKONG;
            } else if ($country == "Hungary") {
                $this->request->data['User']['alphasender'] = HUNGARY;
            } else if ($country == "Ireland") {
                $this->request->data['User']['alphasender'] = IRELAND;
            } else if ($country == "Israel") {
                $this->request->data['User']['alphasender'] = ISRAEL;
            } else if ($country == "Italy") {
                $this->request->data['User']['alphasender'] = ITALY;
            } else if ($country == "Lithuania") {
                $this->request->data['User']['alphasender'] = LITHUANIA;
            } else if ($country == "Mexico") {
                $this->request->data['User']['alphasender'] = MEXICO;
            } else if ($country == "Netherlands") {
                $this->request->data['User']['alphasender'] = NETHERLANDS;
            } else if ($country == "Norway") {
                $this->request->data['User']['alphasender'] = NORWAY;
            } else if ($country == "Poland") {
                $this->request->data['User']['alphasender'] = POLAND;
            } else if ($country == "Puerto Rico") {
                $this->request->data['User']['alphasender'] = PUERTORICO;
            } else if ($country == "Spain") {
                $this->request->data['User']['alphasender'] = SPAIN;
            } else if ($country == "Sweden") {
                $this->request->data['User']['alphasender'] = SWEDEN;
            } else if ($country == "Switzerland") {
                $this->request->data['User']['alphasender'] = SWITZERLAND;
            } else if ($country == "United Kingdom") {
                $this->request->data['User']['alphasender'] = UNITEDKINGDOM;
            } else if ($country == "United States") {
                $this->request->data['User']['alphasender'] = UNITEDSTATES;
            }

            $random_number = random_generator(4);
            $this->request->data['User']['password'] = md5($this->request->data['User']['passwrd']);
            $this->request->data['User']['email_alert_options'] = 1;
            $this->request->data['User']['email_alert_credit_options'] = 1;

            app::import('Model', 'CountryGateway');
            $this->CountryGateway = new CountryGateway();
            $countrygateway = $this->CountryGateway->find('first', array('conditions' => array('CountryGateway.country' => $country)));
            $api_type = $countrygateway['CountryGateway']['api_type'];

            if (!empty($countrygateway) && $api_type != '') {
                $this->request->data['User']['api_type'] = $api_type;
                if($api_type==7){
                    $billresponse = $this->Telnyx->createbillinggroup(TELNYX_KEY,$this->request->data['User']['username']); 
                    if(!isset($billresponse->errors)){
                        $this->request->data['User']['billinggroupid'] = $billresponse->data->id;
                    }
                    $response = $this->Telnyx->createmessagingprofile(TELNYX_KEY,$this->request->data['User']['username']);
                    $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY,$this->request->data['User']['username']);
				    $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billresponse->data->id,$this->request->data['User']['username']);
            		$updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
            		$this->request->data['User']['app_id'] = $response->data->id;
            		$this->request->data['User']['call_app_id'] = $callresponse->data->id;
            	}
                
            } else if (TWILIO_ACCOUNTSID != '') {
                $this->request->data['User']['api_type'] = 0;
            } else if (PLIVO_KEY != '') {
                $this->request->data['User']['api_type'] = 3;
            } else if (NEXMO_KEY != '') {
                $this->request->data['User']['api_type'] = 1;
            } else if (BANDWIDTH_KEY != '') {
                $this->request->data['User']['api_type'] = 4;  
            } else if (SIGNALWIRE_KEY != '') {
                $this->request->data['User']['api_type'] = 5; 
            } else if (YTEL_ACCOUNTSID != '') {
                $this->request->data['User']['api_type'] = 6; 
            } else if (TELNYX_KEY != '') {
                $this->request->data['User']['api_type'] = 7; 
                $billresponse = $this->Telnyx->createbillinggroup(TELNYX_KEY,$this->request->data['User']['username']); 
                if(!isset($billresponse->errors)){
                    $this->request->data['User']['billinggroupid'] = $billresponse->data->id;
                }
                $response = $this->Telnyx->createmessagingprofile(TELNYX_KEY,$this->request->data['User']['username']);
                $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY,$this->request->data['User']['username']);
				$voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billresponse->data->id,$this->request->data['User']['username']);
            	$updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
            	$this->request->data['User']['app_id'] = $response->data->id;
            	$this->request->data['User']['call_app_id'] = $callresponse->data->id;
            } else {
                $this->request->data['User']['api_type'] = 0;
            }

            //$this->request->data['User']['api_type'] = API_TYPE;
            $this->request->data['User']['account_activated'] = $random_number;
            $userIP = $this->getRealUserIp();
            $this->request->data['User']['IP_address'] = $userIP;
            
            //***Default user permissions based on config data
            $this->request->data['User']['autoresponders'] = AUTORESPONDERS;
            $this->request->data['User']['importcontacts'] = IMPORTCONTACTS;
            $this->request->data['User']['shortlinks'] = SHORTLINKS;
            $this->request->data['User']['voicebroadcast'] = VOICEBROADCAST;
            $this->request->data['User']['polls'] = POLLS;
            $this->request->data['User']['contests'] = CONTESTS;
            $this->request->data['User']['loyaltyprograms'] = LOYALTYPROGRAMS;
            $this->request->data['User']['kioskbuilder'] = KIOSKBUILDER;
            $this->request->data['User']['birthdaywishes'] = BIRTHDAYWISHES;
            $this->request->data['User']['mobilepagebuilder'] = MOBILEPAGEBUILDER;
            $this->request->data['User']['webwidgets'] = WEBWIDGETS;
            $this->request->data['User']['smschat'] = SMSCHAT;
            $this->request->data['User']['qrcodes'] = QRCODES;
            $this->request->data['User']['calendarscheduler'] = CALENDARSCHEDULER;
            $this->request->data['User']['appointments'] = APPOINTMENTS;
            $this->request->data['User']['groups'] = GROUPS;
            $this->request->data['User']['contactlist'] = CONTACTLIST;
            $this->request->data['User']['sendsms'] = SENDSMS;
            $this->request->data['User']['affiliates'] = AFFILIATES;
            $this->request->data['User']['getnumbers'] = GETNUMBERS;
            $this->request->data['User']['mobilecoupons'] = MOBILECOUPONS;
            $this->request->data['User']['qabots'] = QABOTS;

            //if ($this->User->saveAll($this->request->data,array('validate'=>false))) {
            if ($this->User->save($this->request->data)) {
                $sitename = str_replace(' ', '', SITENAME);
                $subject = SITENAME . " :: Register Account";
                $url = SITE_URL . "/users/user_activate_account/" . $random_number;
                //$this->Email->to = $this->request->data['User']['email'];
                //$this->Email->subject = $subject;
                //$this->Email->from = $sitename;
                //$this->Email->template = 'account_login';
                //$this->Email->sendAs = 'html';
                //$this->Email->Controller->set('username', $this->request->data['User']['username']);
                //$this->Email->Controller->set('password', $this->request->data['User']['passwrd']);
                //$this->Email->Controller->set('url', $url);
                //$this->Email->Controller->set('email', $email);
                //$this->Email->send();
                
                $Email = new CakeEmail();
                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }
                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to($this->request->data['User']['email']);
                $Email->subject($subject);
                $Email->template('account_login');
                $Email->emailFormat('html');
                $Email->viewVars(array('username' => $this->request->data['User']['username']));
                $Email->viewVars(array('password' => $this->request->data['User']['passwrd']));
                $Email->viewVars(array('url' => $url));
                $Email->send();

                
                $last_id = $this->User->getLastInsertId();
                $referral = $this->Cookie->read('refArray');
                //$recurringrefArray = $this->Cookie->read('recurringrefArray');
                //print_r($referral);
                //print_r($recurringrefArray);
                if (!empty($referral)) {
                    $this->loadModel('Referral');
                    $refData = array('Referral' => array('user_id' => $last_id, 'referred_by' => $referral['id'], 'url' => $referral['url'], 'type' => 0, 'amount' => REFERRAL_AMOUNT));
                    $this->Referral->save($refData);
                    $this->Cookie->delete('refArray');

                }/*else if(!empty($recurringrefArray)){

				    app::import('Model','Referral');

                    $this->Referral = new Referral();

					app::import('Model','User');

                    $this->User = new User();

	                $userdetails = $this->User->find('first', array('conditions' => array('User.id' =>$recurringrefArray['id'])));

					/* echo "<pre>";
					print_r($userdetails);
					echo "</pre>"; */
                /*if($userdetails['User']['active']==1){
					$this->loadModel('MonthlyPackage');

		            $monthlydetails=$this->MonthlyPackage->find('first' , array('conditions' => array('MonthlyPackage.id' => $userdetails['User']['package'])));

					/* echo "<pre>";
					print_r($monthlydetails);
					echo "</pre>"; */

                /*$percentage=$monthlydetails['MonthlyPackage']['amount']*RECURRING_REFERRAL_PERCENT/100;

					$refData = array('Referral' =>array('user_id' =>$last_id,'referred_by' =>$recurringrefArray['id'], 'url' =>$recurringrefArray['url'],'type' =>1, 'amount' =>$percentage));
					$this->Referral->save($refData);
					$this->Cookie->delete('recurringrefArray');

					}
				}*/

                //$this->Session->setFlash(__('You have been saved as a user.', true));
                $this->redirect(array('action' => 'success'));
            }

        }
    }

    function user_activate_account($id = null, $admin = 0)
    {
        $this->autoRender = false;
        if (!empty($id)) {
            app::import('Model', 'User');
            $this->User = new User();
            $activate_user = $this->User->find('first', array('conditions' => array('User.account_activated' => $id)));
			if(!empty($activate_user)){
				$accounts = $this->User->find('count', array('conditions' => array('User.active' => 1)));
				if ($accounts >= NUMACCOUNTS) {
					$sitename = str_replace(' ', '', SITENAME);
					$subject = SITENAME . " :: Account Limit Reached";
					/*$this->Email->to = SUPPORT_EMAIL;
					$this->Email->subject = $subject;
					$this->Email->from = $sitename;
					$this->Email->template = 'account_limit_notice';
					$this->Email->sendAs = 'html';
					$this->Email->Controller->set('username', $activate_user['User']['username']);
					$this->Email->Controller->set('firstname', $activate_user['User']['first_name']);
					$this->Email->Controller->set('lastname', $activate_user['User']['last_name']);
					$this->Email->Controller->set('email', $activate_user['User']['email']);
					$this->Email->Controller->set('phone', $activate_user['User']['phone']);
					$this->Email->send();*/
					
					$Email = new CakeEmail();
					if(EMAILSMTP==1){                                                  
						$Email->config('smtp');
					}
					$Email->from(array(SUPPORT_EMAIL => SITENAME));
					$Email->to(SUPPORT_EMAIL);
					$Email->subject($subject);
					$Email->template('account_limit_notice');
					$Email->emailFormat('html');
					$Email->viewVars(array('firstname' => $activate_user['User']['first_name']));
					$Email->viewVars(array('lastname' => $activate_user['User']['last_name']));
					$Email->viewVars(array('username' => $activate_user['User']['username']));
					$Email->viewVars(array('email' => $activate_user['User']['email']));
					$Email->viewVars(array('phone' => $activate_user['User']['phone']));
					$Email->send();

					$this->Session->setFlash('System has reached maximum number of active accounts, therefore your account could not be activated. Please contact us to increase capacity.');
					$this->redirect(array('controller' => 'users', 'action' => 'login'));
				}

				$sitename = str_replace(' ', '', SITENAME);
				$subject = "New User Registered at " . SITENAME;
				/*$this->Email->to = SUPPORT_EMAIL;
				$this->Email->subject = $subject;
				$this->Email->from = $sitename;
				$this->Email->template = 'new_user_registered';
				$this->Email->sendAs = 'html';
				$this->Email->Controller->set('username', $activate_user['User']['username']);
				$this->Email->Controller->set('firstname', $activate_user['User']['first_name']);
				$this->Email->Controller->set('lastname', $activate_user['User']['last_name']);
				$this->Email->Controller->set('email', $activate_user['User']['email']);
				$this->Email->send();*/
				
				$Email = new CakeEmail();
				if(EMAILSMTP==1){                                                  
					$Email->config('smtp');
				}
				$Email->from(array(SUPPORT_EMAIL => SITENAME));
				$Email->to(SUPPORT_EMAIL);
				$Email->subject($subject);
				$Email->template('new_user_registered');
				$Email->emailFormat('html');
				$Email->viewVars(array('firstname' => $activate_user['User']['first_name']));
				$Email->viewVars(array('lastname' => $activate_user['User']['last_name']));
				$Email->viewVars(array('username' => $activate_user['User']['username']));
				$Email->viewVars(array('email' => $activate_user['User']['email']));
				$Email->send();

				$pay_activation_fee = PAY_ACTIVATION_FEES;
				if ($pay_activation_fee == 2) {
					app::import('Model', 'User');
					$this->User = new User();
					$activationfees['id'] = $activate_user['User']['id'];
					$activationfees['active'] = 1;
					$activationfees['sms_balance'] = FREE_SMS;
					$activationfees['voice_balance'] = FREE_VOICE;
					$activationfees['pay_activation_fees_active'] = PAY_ACTIVATION_FEES;
					$this->User->save($activationfees);
				} else {
					$activationfeesdetails['id'] = $activate_user['User']['id'];
					$activationfeesdetails['pay_activation_fees_active'] = PAY_ACTIVATION_FEES;
					$this->User->save($activationfeesdetails);
				}

				$recurringrefArray = $this->Cookie->read('recurringrefArray');
				if (!empty($recurringrefArray)) {
					app::import('Model', 'Referral');
					$this->Referral = new Referral();
					app::import('Model', 'User');
					$this->User = new User();

					$userdetails = $this->User->find('first', array('conditions' => array('User.id' => $recurringrefArray['id'])));

					if ($userdetails['User']['active'] == 1) {
						$this->loadModel('MonthlyPackage');
						$monthlydetails = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $userdetails['User']['package'])));
						$percentage = $monthlydetails['MonthlyPackage']['amount'] * RECURRING_REFERRAL_PERCENT / 100;
						$refData = array('Referral' => array('user_id' => $activate_user['User']['id'], 'referred_by' => $recurringrefArray['id'], 'url' => $recurringrefArray['url'], 'type' => 1, 'amount' => $percentage));
						$this->Referral->save($refData);
						$this->Cookie->delete('recurringrefArray');

					}
				}
				
				/*if ($activate_user['User']['api_type']==0){
					$this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
					$this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
					$response = $this->Twilio->createsubaccount($activate_user['User']['username']);
					$authToken = $response->ResponseXml->Account->AuthToken;
					$Sid = $response->ResponseXml->Account->Sid;
					$this->User->id = $activate_user['User']['id'];
					$this->User->saveField('SID', $Sid);
					$this->User->saveField('AuthToken', $authToken);
				}else if($activate_user['User']['api_type']==3){
					
				}
				ob_start();
				echo "<pre>";
				print_r($response->ResponseXml->Account->AuthToken);
				print_r(" ******** ");
				print_r($response->ResponseXml->Account->Sid);
				echo "</pre>";
				$out1 = ob_get_contents();
				ob_end_clean();
				$file = fopen("callstatusdebug/createsubaccount".time().".txt", "w");
				fwrite($file, $out1);
				fclose($file);*/
				
				$subject = SITENAME . " :: Account Registered Successfully";
				/*$this->Email->to = $activate_user['User']['email'];
				$this->Email->subject = $subject;
				$this->Email->from = $sitename;
				$this->Email->template = 'activate_sucessfully';
				$this->Email->sendAs = 'html';
				$this->Email->Controller->set('username', $activate_user['User']['username']);
				$this->Email->Controller->set('email', $activate_user['User']['email']);
				$this->Email->send();*/
				
				$Email = new CakeEmail();
				if(EMAILSMTP==1){                                                  
					$Email->config('smtp');
				}
				$Email->from(array(SUPPORT_EMAIL => SITENAME));
				$Email->to($activate_user['User']['email']);
				$Email->subject($subject);
				$Email->template('activate_sucessfully');
				$Email->emailFormat('html');
				$Email->viewVars(array('username' => $activate_user['User']['username']));
				$Email->viewVars(array('email' => $activate_user['User']['email']));
				$Email->send();
				
				$this->User->id = $activate_user['User']['id'];
				$this->User->saveField('register', 1);
				$this->Session->setFlash('Your account has been successfully registered');
				if ($admin == 0) {
					$this->redirect(array('controller' => 'users', 'action' => 'login'));
				}
            }else{
				$this->Session->setFlash('Activation code not found. please try again.');
				$this->redirect(array('controller' => 'users', 'action' => 'login'));
			}
        }
    }

    function success()
    {
        //$this->layout= 'login_layout';
        $this->layout = 'default';
    }


    function dashboard()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->getLoggedInUserId();
        $this->User->recursive = 0;
        $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'fields' => 'active'));
        /*if($user['User']['active'] == 1){
			$this->redirect(array('controller' =>'users', 'action'=>'profile'));
		}*/

        $pay_activation_fee = PAY_ACTIVATION_FEES;

        if ($user['User']['active'] == 1 || ($pay_activation_fee == 2 && $user['User']['active'] == 0)) {
            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
        }

    }

    function purchasenumber()
    {
        $this->layout = 'popup';
    }
    
    function purchaseplan()
    {
        $this->layout = 'popup';
        
        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $user_name = trim($this->Session->read('User.username'));
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $country = $userdetails['User']['user_country'];
        
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . '', 'MonthlyPackage.username' => $user_name), 'order' => array('MonthlyPackage.amount' => 'asc')));
        
        if (empty($monthlydetails)){
            $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . '', 'MonthlyPackage.username ' => ''), 'order' => array('MonthlyPackage.amount' => 'asc')));
        }
        $this->set('monthlydetails', $monthlydetails);
        
        app::import('Model', 'Package');
        $this->Package = new Package();
        $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.user_country' => '' . trim($country) . '', 'Package.username' => $user_name), 'order' => array('Package.type' => 'asc','Package.amount' => 'asc')));
        
        if (empty($Packagedetails)){
           $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.user_country' => '' . trim($country) . '', 'Package.username ' => ''), 'order' => array('Package.type' => 'asc','Package.amount' => 'asc'))); 
        }
        $this->set('packagedetails', $Packagedetails);

    }

    function profile()
    {
        $this->set('title_for_layout', 'Profile');
        $this->layout = 'admin_new_layout';
        //$this->layout = 'login_layout';
        $userId = $this->getLoggedInUserId();
        $userActive = $this->getLoggedUserDetails();
        $activeid = $userActive['User']['active'];
        $this->loadModel('Log');
        //$activeid = $this->Session->read('User.active');
        $user = $this->Session->read('User');
        if ($activeid == 1) {

            $this->Session->write('User.sms_balance', $userActive['User']['sms_balance']);
            $this->Session->write('User.assigned_number', $userActive['User']['assigned_number']);
            $this->Session->write('User.pay_activation_fees_active', $userActive['User']['pay_activation_fees_active']);
            $this->Session->write('User.active', $userActive['User']['active']);
            $this->Session->write('User.package', $userActive['User']['package']);
            $this->Session->write('User.getnumbers', $userActive['User']['getnumbers']);

            $month = date('m');
            $year = date('Y');
            $lastyear = date('Y', strtotime('-1 year'));

            $this->Log->recursive = -1;

            $firstday = new DateTime('first day of this month');
            $firstday = $firstday->format('Y-m-d 00:00:00');
            $lastday = new DateTime('last day of this month');
            $lastday = $lastday->format('Y-m-d 23:59:59');

            $inbox = $this->Log->find('all', array('conditions' => array('Log.user_id' => $userId, 'Log.route' => 'inbox', 'msg_type' => 'text', 'Log.created >=' => $firstday, 'Log.created <=' => $lastday), 'fields' => array('Log.created')));
            $this->set('inbox', $inbox);
            $outbox = $this->Log->find('all', array('conditions' => array('Log.user_id' => $userId, 'Log.route' => 'outbox', 'msg_type' => 'text', 'Log.created >=' => $firstday, 'Log.created <=' => $lastday), 'fields' => array('Log.created')));
            $this->set('outbox', $outbox);
            $vminbox = $this->Log->find('all', array('conditions' => array('Log.user_id' => $userId, 'Log.route' => 'inbox', 'msg_type' => 'broadcast', 'Log.created >=' => $firstday, 'Log.created <=' => $lastday), 'fields' => array('Log.created')));
            $this->set('vminbox', $vminbox);

            /*$inbox =  $this->Log->find('all', array('conditions' => array('MONTH(Log.created)' =>$month,'YEAR(Log.created)' =>$year,'Log.user_id' =>$userId, 'Log.route' => 'inbox' ,'msg_type' => 'text'),'fields' => array('Log.created')));
			$this->set('inbox',$inbox);
			$outbox =  $this->Log->find('all', array('conditions' => array('MONTH(Log.created)' =>$month,'YEAR(Log.created)' =>$year,'Log.user_id' =>$userId, 'Log.route' => 'outbox' ,'msg_type' => 'text'),'fields' => array('Log.created')));
			$this->set('outbox',$outbox);
			$vminbox =  $this->Log->find('all', array('conditions' => array('MONTH(Log.created)' =>$month,'YEAR(Log.created)' =>$year,'Log.user_id' =>$userId, 'Log.route' => 'inbox' ,'msg_type' => 'broadcast'),'fields' => array('Log.created')));
			$this->set('vminbox',$vminbox);*/

            //Overall
            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();

            $subscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3))));
            $unsubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.un_subscribers' => 1, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3))));

            if ($subscriber != 0) {
                $total = $subscriber - $unsubscriber;
                $percentage = $total / $subscriber * 100;
                $this->set('percentage', $percentage);
            }

            //Yearly
            $yearlysubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3), 'YEAR(ContactGroup.created)' => $year)));
            $yearlyunsubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.un_subscribers' => 1, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3), 'YEAR(ContactGroup.created)' => $year)));

            if ($yearlysubscriber != 0) {
                $yearlytotal = $yearlysubscriber - $yearlyunsubscriber;
                $yearlypercentage = $yearlytotal / $yearlysubscriber * 100;
                $this->set('yearlypercentage', $yearlypercentage);
            }

            //monthly
            $monthlysubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3), 'MONTH(ContactGroup.created)' => $month, 'YEAR(ContactGroup.created)' => $year)));
            $monthlyunsubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.un_subscribers' => 1, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3), 'MONTH(ContactGroup.created)' => $month, 'YEAR(ContactGroup.created)' => $year)));

            if ($monthlysubscriber != 0) {
                $monthlytotal = $monthlysubscriber - $monthlyunsubscriber;
                $monthlypercentage = $monthlytotal / $monthlysubscriber * 100;
                $this->set('monthlypercentage', $monthlypercentage);
            }

            //weekly
            $week = date('W');
            $current_date = date('Y-m-d');
            $one_week_date = date('Y-m-d', strtotime('-7 days'));
            $weeklysubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3), 'DATE(ContactGroup.created) >=' => $one_week_date, 'DATE(ContactGroup.created) <=' => $current_date)));
            $weeklyunsubscriber = $this->ContactGroup->find('count', array('conditions' => array('ContactGroup.user_id' => $userId, 'ContactGroup.un_subscribers' => 1, 'ContactGroup.subscribed_by_sms' => array(1, 2, 3), 'DATE(ContactGroup.created) >=' => $one_week_date, 'DATE(ContactGroup.created) <=' => $current_date)));


            if ($weeklysubscriber != 0) {
                $weeklytotal = $weeklysubscriber - $weeklyunsubscriber;
                $weeklypercentage = $weeklytotal / $weeklysubscriber * 100;
                $this->set('weeklypercentage', $weeklypercentage);
            }

            app::import('Model', 'MonthlyPackage');
            $this->MonthlyPackage = new MonthlyPackage();
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
            $packageid = $someone['User']['package'];
            $package = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $packageid)));
            $this->set('packages', $package);

            app::import('Model', 'MonthlyNumberPackage');
            $this->MonthlyNumberPackage = new MonthlyNumberPackage();
            $numberpackageid = $someone['User']['number_package'];
            $numberpackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $numberpackageid)));
            $this->set('numberpackages', $numberpackage);

            app::import('Model', 'Contact');
            $this->Contact = new Contact();

            $query = "SELECT contact_groups.created, groups.keyword, contact_groups.subscribed_by_sms FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $userId . " and contact_groups.un_subscribers=0 and contact_groups.subscribed_by_sms !=0 and YEAR(contact_groups.created) = '$year'";
            $subscribers = $this->Contact->query($query);
            $this->set('subscribers', $subscribers);

            $query = "SELECT contact_groups.created, groups.keyword, contact_groups.subscribed_by_sms FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $userId . " and contact_groups.un_subscribers=0 and contact_groups.subscribed_by_sms !=0 and YEAR(contact_groups.created) = '$lastyear'";
            $subscriberslastyear = $this->Contact->query($query);
            $this->set('subscriberslastyear', $subscriberslastyear);

            $query_un = "SELECT contact_groups.created, groups.keyword FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $userId . " and contact_groups.un_subscribers=1 and contact_groups.subscribed_by_sms !=0 and YEAR(contact_groups.created) = '$year'";
            $un_subscribers = $this->Contact->query($query_un);
            $this->set('un_subscribers', $un_subscribers);

            $query_un = "SELECT contact_groups.created, groups.keyword FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $userId . " and contact_groups.un_subscribers=1 and contact_groups.subscribed_by_sms !=0 and YEAR(contact_groups.created) = '$lastyear'";
            $un_subscriberslastyear = $this->Contact->query($query_un);
            $this->set('un_subscriberslastyear', $un_subscriberslastyear);

            //Invoice list
            app::import('Model', 'Invoice');
            $this->Invoice = new Invoice();
            $invoicedetil = $this->Invoice->find('all', array('conditions' => array('Invoice.user_id' => $userId), 'order' => array('Invoice.id' => 'desc'), 'limit' => 5));
            $this->set('invoicedetils', $invoicedetil);
            //Referrals list
            app::import('Model', 'Referral');
            $this->Referral = new Referral();
            $referrals = $this->Referral->find('all', array('conditions' => array('Referral.referred_by' => $this->getLoggedInUserId(), 'Referral.account_activated' => 1), 'order' => array('Referral.id' => 'desc'), 'limit' => 5));
            $this->set('referrals', $referrals);

            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $userId, 'UserNumber.api_type' => API_TYPE), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
            $this->set('numbers', $numbers);

            app::import('Model', 'Group');
            $this->Group = new Group();
            $keywords = $this->Group->find('all', array('conditions' => array('Group.user_id' => $userId), 'fields' => array('Group.keyword')));
            $this->set('keywords', $keywords);

            if (NUMVERIFY != '' || (NUMVERIFY == '' && (API_TYPE == 6 || API_TYPE == 7))) {
                $query = "SELECT  DISTINCT contacts.carrier, count( * ) 'count' FROM contacts, contact_groups WHERE contact_groups.contact_id = contacts.id 
                AND contact_groups.user_id = " . $userId . " AND contact_groups.un_subscribers=0 AND contact_groups.subscribed_by_sms !=0 AND contacts.carrier !='' GROUP BY contacts.carrier";
                $carriers = $this->Contact->query($query);
                $this->set('carriers', $carriers);
            }

            $query = "SELECT groups.keyword, count(*) 'count'  FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $userId . " and contact_groups.un_subscribers=0 and contact_groups.subscribed_by_sms !=0 group by groups.keyword";
            $keywordcounts = $this->Contact->query($query);
            $this->set('keywordcounts', $keywordcounts);

            $query = "SELECT  DISTINCT contact_groups.subscribed_by_sms 'source', count( * ) 'count' FROM contacts, contact_groups WHERE contact_groups.contact_id = contacts.id 
            AND contact_groups.user_id = " . $userId . " AND contact_groups.un_subscribers=0 AND contact_groups.subscribed_by_sms !=0 GROUP BY contact_groups.subscribed_by_sms";
            $sourcecounts = $this->Contact->query($query);
            $this->set('sourcecounts', $sourcecounts);


        } else {
            $this->Session->setFlash(__('Your account is not active. Please activate your account', true));
            $pay_activation_fee = PAY_ACTIVATION_FEES;
            if ($pay_activation_fee == 1) {
                $this->redirect(array('action' => 'dashboard'));
            }
        }
    }

    function view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid user', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    function affiliates()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->getLoggedInUserId();
        $this->User->recursive = 0;
        $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'fields' => 'paypal_email'));
        $this->set('user', $user);
    }

    function change_paypal_email()
    {
        $this->layout = 'popup';
        if (!empty($this->request->data)) {
            $this->User->set($this->request->data);
            $this->User->validationSet = 'ChangePaypalEmail';
            if ($this->User->validates()) {
                $this->User->id = $this->getLoggedInUserId();
                $this->User->saveField('paypal_email', $this->request->data['User']['paypal_email']);
                $this->redirect(array('action' => 'affiliates'));
            }
        }
        $user_id = $this->getLoggedInUserId();
        $this->User->recursive = 0;
        $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'fields' => 'paypal_email'));
        $this->set('user', $user);
    }

    function edit()
    {
        $this->layout = 'admin_new_layout';
        if (!empty($this->request->data)) {
            
            $user_arr['User']['id'] = $this->request->data['User']['id'];
            $user_arr['User']['first_name'] = $this->request->data['User']['first_name'];
            $user_arr['User']['last_name'] = $this->request->data['User']['last_name'];
            $user_arr['User']['email'] = $this->request->data['User']['email'];
            $user_arr['User']['phone'] = $this->request->data['User']['phone'];
            $user_arr['User']['company_name'] = $this->request->data['User']['company_name'];
            $user_arr['User']['paypal_email'] = $this->request->data['User']['paypal_email'];
            $user_arr['User']['voicemailnotifymail'] = $this->request->data['User']['voicemailnotifymail'];
            $user_arr['User']['welcome_msg_type'] = $this->request->data['User']['welcome_msg_type'];
            $user_arr['User']['defaultgreeting'] = $this->request->data['User']['defaultgreeting'];
            $user_arr['User']['timezone'] = $this->request->data['User']['timezone'];
            //$user_arr['User']['user_country'] = $this->request->data['User']['user_country'];
            if (isset($this->request->data['User']['mp3']['name'])) {
                if ($this->request->data['User']['mp3']['name'] != '') {
                    $temp_name = $this->request->data['User']['mp3']['tmp_name'];
                    $name1 = str_replace(" ", "_", $this->request->data['User']['mp3']['name']);
                    $name2 = str_replace("&", "_", $name1);
                    //move_uploaded_file($temp_name, "mp3/" . time() . $name2);
                    //$user_arr['User']['mp3'] = time() . $name2;
                    
                    $extension = strtolower(pathinfo($name2, PATHINFO_EXTENSION));
					$extension_arr = array('mp3','wav');
					if (!in_array($extension, $extension_arr)){
						$this->Session->setFlash(__('Please select mp3 or wav file type only for voicemail greeting', true));
						$this->redirect(array('action' => 'edit'));
					}else{
                        move_uploaded_file($temp_name, "mp3/" . time() . $name2);
                        $user_arr['User']['mp3'] = time() . $name2;
                    }
                }
            }
            
            if ($this->request->data['User']['currentfaxnumber'] != $this->request->data['User']['fax_number']){
                Controller::loadModel('User');
                $someone = $this->User->find('first', array('conditions' => array('assigned_number' => $this->request->data['User']['fax_number']), 'fields' => 'User.phone_sid'));
                $phonesid = $someone['User']['phone_sid'];
                
                if (empty($someone)) {
                    app::import('Model', 'UserNumber');
                    $this->UserNumber = new UserNumber();
                    $someone = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => $this->request->data['User']['fax_number']), 'fields' => 'UserNumber.phone_sid'));
                    $phonesid = $someone['UserNumber']['phone_sid'];
                }
                
                if(API_TYPE==0){
                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                }
                    
                if($this->request->data['User']['currentfaxnumber'] == ''){
                    if(API_TYPE==0){
                        $this->Twilio->updatefaxnumber($phonesid,'fax');
                    }elseif (API_TYPE==5){
                        $this->Signalwire->updatefaxnumber(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$phonesid,'fax');
                    }
                }else{

                    $someonecurrent = $this->User->find('first', array('conditions' => array('assigned_number' => $this->request->data['User']['currentfaxnumber']), 'fields' => 'User.phone_sid'));
                    $phonesidcurrent = $someonecurrent['User']['phone_sid'];
                
                    if (empty($someonecurrent)) {
                        app::import('Model', 'UserNumber');
                        $this->UserNumber = new UserNumber();
                        $someonecurrent = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => $this->request->data['User']['currentfaxnumber']), 'fields' => 'UserNumber.phone_sid'));
                        $phonesidcurrent = $someonecurrent['UserNumber']['phone_sid'];
                    }
                    
                    if($phonesid !=''){
                        if(API_TYPE==0){
                            $this->Twilio->updatefaxnumber($phonesid,'fax');
                        }elseif (API_TYPE==5){
                            $this->Signalwire->updatefaxnumber(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$phonesid,'fax');
                        }
                    }
                    if(API_TYPE==0){
                        $this->Twilio->updatefaxnumber($phonesidcurrent,'voice');
                    }elseif (API_TYPE==5){
                        $this->Signalwire->updatefaxnumber(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$phonesidcurrent,'voice');
                    }
                }
                
                $user_arr['User']['fax_number'] = $this->request->data['User']['fax_number'];
            }
            
            if ($this->User->save($user_arr)) {
                $this->Session->setFlash(__('The user has been saved', true));
                $this->redirect(array('action' => 'edit'));
            } else {
                $id = $this->getLoggedInUserId();
                $this->request->data = $this->User->read(null, $id);
                $user_arr = $this->User->read(null, $id);
                $this->set('user_arr', $user_arr);
            
                $numbers = array();
                $primary = $this->User->find('first', array('conditions' => array('User.id' => $id, 'User.fax' => 1)));
                $this->set('faxnumber', $primary['User']['fax_number']);
    
                if (!empty($primary)) {
                    $numbers[] = array('nickname' => 'Primary', 'number' => $primary['User']['assigned_number'], 'number_details' => $primary['User']['assigned_number']);
                }
                
                app::import('Model', 'UserNumber');
                $this->UserNumber = new UserNumber();
                $UserNumbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $id, 'UserNumber.fax' => 1)));
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
                $this->Session->setFlash(__('The user could not be saved. Email address already taken.', true));
            }
        }
        if (empty($this->request->data)) {
            $id = $this->getLoggedInUserId();
            $this->request->data = $this->User->read(null, $id);
            $user_arr = $this->User->read(null, $id);
            $this->set('user_arr', $user_arr);
            
            $numbers = array();
            $primary = $this->User->find('first', array('conditions' => array('User.id' => $id, 'User.fax' => 1)));
            $this->set('faxnumber', $primary['User']['fax_number']);

            if (!empty($primary)) {
                $numbers[] = array('nickname' => 'Primary', 'number' => $primary['User']['assigned_number'], 'number_details' => $primary['User']['assigned_number']);
            }
            
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $UserNumbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $id, 'UserNumber.fax' => 1)));
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
    }

    function change_password()
    {
        $this->layout = 'admin_new_layout';
        $this->set('title_for_layout', 'Change Password');
        if (!empty($this->request->data)) {
            $this->User->set($this->request->data);
            $this->User->validationSet = 'ChangePassword';
            if ($this->User->validates()) {
                $username = $this->getLoggedInUserName();
                $someone = $this->User->find('first', array('conditions' => array('username' => $username)));
                if (!empty($someone['User']['password']) && $someone['User']['password'] == md5($this->request->data['User']['old_password'])) {
                    $this->User->id = $someone['User']['id'];
                    $this->User->saveField('password', md5($this->request->data['User']['new_password']));
                    $this->Session->setFlash(__('Password changed', true));
                    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
                } // Else, they supplied incorrect data:
                else {
                    $this->Session->setFlash('Old Password is Wrong');
                }
            }
        }//this->data
    }

    function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for user', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->User->delete($id)) {
            $this->Session->setFlash(__('User deleted', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function list_package($id = null)
    {
        $this->layout = 'admin_new_layout';
        $this->set('id', $id);
        $this->loadModel('Package');
        $this->set('text_packages', $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text'))));
        $this->set('voice_packages', $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice'))));
        $this->set('user', $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id')))));
    }

    function purchase_credit($id)
    {
        $this->layout = 'popup';
        $this->loadModel('Package');
        $this->set('package', $this->Package->find('first', array('conditions' => array('id' => $id))));
    }

    function purchase_credit_stripe($id = null)
    {
        $this->layout = 'popup';
        $this->loadModel('Package');
        $this->set('package', $this->Package->find('first', array('conditions' => array('id' => $id))));
        if (isset($_POST['stripeToken'])) {
            if ($_POST['stripeToken'] != '') {
                $package = $this->Package->find('first', array('conditions' => array('id' => $id)));
                $desc = $package['Package']['name'];
                $setApiKey = SECRET_KEY;
                $currency = PAYMENT_CURRENCY_CODE;
                \Stripe\Stripe::setApiKey(SECRET_KEY);
                try {
                    $charge = \Stripe\Charge::create(array(
                            "amount" => $package['Package']['amount'] * 100,
                            "currency" => $currency,
                            "description" => $desc,
                            "source" => $_POST['stripeToken']
                        )
                    );
                    if (isset($charge->id)) {
                        $user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
                        if (!empty($user)) {
                            $user_arr['User']['id'] = $this->Session->read('User.id');
                            $user_arr['User']['active'] = 1;
                            if ($package['Package']['type'] == 'text') {
                                $user_arr['User']['sms_balance'] = $user['User']['sms_balance'] + $package['Package']['credit'];
                                $user_arr['User']['sms_credit_balance_email_alerts'] = 0;
                            }
                            if ($package['Package']['type'] == 'voice') {
                                $user_arr['User']['voice_balance'] = $user['User']['voice_balance'] + $package['Package']['credit'];
                                $user_arr['User']['VM_credit_balance_email_alerts'] = 0;
                            }
                            $this->User->save($user_arr);
                            app::import('Model', 'Invoice');
                            $this->Invoice = new Invoice();
                            $invoice['user_id'] = $this->Session->read('User.id');
                            $invoice['txnid'] = $charge->id;
                            $invoice['type'] = 1;
                            $invoice['amount'] = $package['Package']['amount'];
                            $invoice['package_name'] = $package['Package']['name'];
                            $invoice['created'] = date("Y-m-d");
                            $this->Invoice->save($invoice);
                        }
                    }
                    $this->Session->setFlash('Thank you for your purchase! Credits updated successfully');
                    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
                } catch (\Stripe\Error\Card $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\RateLimit $e) {
                    // Too many requests made to the API too quickly
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\InvalidRequest $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\Authentication $e) {
                    // Authentication with Stripe's API failed
                    // (maybe you changed API keys recently)
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\ApiConnection $e) {
                    // Network communication with Stripe failed
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\Base $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (Exception $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                }
            } else {
                $this->Session->setFlash('Please try again');
                $this->redirect(array('controller' => 'users', 'action' => 'activation'));
            }
        }
    }

    function purchase_subscription($id)
    {
        $this->layout = 'popup';
        $this->loadModel('MonthlyPackage');
        $this->set('monthlypackage', $this->MonthlyPackage->find('first', array('conditions' => array('id' => $id))));
    }

    function purchase_subscription_stripe($id)
    {
        $this->layout = 'popup';
        $this->loadModel('MonthlyPackage');
        $this->set('monthlypackage', $this->MonthlyPackage->find('first', array('conditions' => array('id' => $id))));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        $this->set('users', $users);
        if (isset($_POST['stripeToken'])) {
            if ($_POST['stripeToken'] != '') {
                $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('id' => $id)));
                $desc = $monthlypackage['MonthlyPackage']['package_name'];
                $setApiKey = SECRET_KEY;
                $currency = PAYMENT_CURRENCY_CODE;
                $user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
                $username = $user['User']['username'];
                $firstname = $user['User']['first_name'];
                $lastname = $user['User']['last_name'];
                $stripecustomerid = $user['User']['stripe_customer_id'];
                $monthly_stripe_subscription_id = '';
                \Stripe\Stripe::setApiKey(SECRET_KEY);
                try {
                    if ($stripecustomerid != '') {
                        $customer = \Stripe\Subscription::create(array(
                            "customer" => $stripecustomerid,
                            "plan" => $monthlypackage['MonthlyPackage']['product_id']
                        ));
                        if (isset($customer->id)) {
                            $monthly_stripe_subscription_id = $customer->id;
                        }
                    } else {
                        $customer = \Stripe\Customer::create(array(
                                "plan" => $monthlypackage['MonthlyPackage']['product_id'],
                                "source" => $_POST['stripeToken'],
                                "email" => $this->Session->read('User.email'),
                                "metadata" => array("username" => $username, "firstname" => $firstname, "lastname" => $lastname)
                            )
                        );
                        $stripecustomerid = $customer->id;
                        if (isset($customer->subscriptions->data[0]->id)) {
                            $monthly_stripe_subscription_id = $customer->subscriptions->data[0]->id;
                        }
                    }
                    if (isset($customer->id)) {
                        if (!empty($user)) {
                            $user_arr['User']['id'] = $this->Session->read('User.id');
                            $user_arr['User']['stripe_customer_id'] = $stripecustomerid;
                            $user_arr['User']['monthly_stripe_subscription_id'] = $monthly_stripe_subscription_id;
                            //$user_arr['User']['sms_balance']=$user['User']['sms_balance'] + $monthlypackage['MonthlyPackage']['text_messages_credit'];
                            //$user_arr['User']['voice_balance']=$user['User']['voice_balance'] + $monthlypackage['MonthlyPackage']['voice_messages_credit'];
                            $user_arr['User']['sms_credit_balance_email_alerts'] = 0;
                            $user_arr['User']['VM_credit_balance_email_alerts'] = 0;
                            $user_arr['User']['package'] = $monthlypackage['MonthlyPackage']['id'];
                            $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
                            $user_arr['User']['next_renewal_dates'] = $nextdate;
                            $user_arr['User']['active'] = 1;
                            $this->User->save($user_arr);
                            app::import('Model', 'Referral');
                            $this->Referral = new Referral();
                            $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $this->Session->read('User.id'))));
                            if (!empty($Referraldetails)) {
                                $referral['id'] = $Referraldetails['Referral']['id'];
                                $referral['account_activated'] = 1;
                                $this->Referral->save($referral);
                            }
                        }
                    }
                    $this->Session->setFlash('Thanks for your purchase! Please allow a few moments for your account to be updated as we have to wait for confirmation from the credit card processor.');
                    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
                } catch (\Stripe\Error\Card $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\RateLimit $e) {
                    // Too many requests made to the API too quickly
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\InvalidRequest $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\Authentication $e) {
                    // Authentication with Stripe's API failed
                    // (maybe you changed API keys recently)
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\ApiConnection $e) {
                    // Network communication with Stripe failed
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (\Stripe\Error\Base $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                } catch (Exception $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripepayment'));
                }
            } else {
                $this->Session->setFlash('Please try again');
                $this->redirect(array('controller' => 'users', 'action' => 'activation'));
            }
        }
    }

    function purchase_subscription_stripe_numbers($id)
    {
        $this->layout = 'popup';
        $this->loadModel('MonthlyNumberPackage');
        $this->set('monthlynumberpackage', $this->MonthlyNumberPackage->find('first', array('conditions' => array('id' => $id))));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        $this->set('users', $users);
        if (isset($_POST['stripeToken'])) {
            if ($_POST['stripeToken'] != '') {
                $monthlypackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('id' => $id)));
                $user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
                $username = $user['User']['username'];
                $firstname = $user['User']['first_name'];
                $lastname = $user['User']['last_name'];
                $stripecustomerid = $user['User']['stripe_customer_id'];
                $monthly_number_subscription_id = '';
                \Stripe\Stripe::setApiKey(SECRET_KEY);
                try {
                    if ($stripecustomerid != '') {
                        $customer = \Stripe\Subscription::create(array(
                            "customer" => $stripecustomerid,
                            "plan" => $monthlypackage['MonthlyNumberPackage']['plan']
                        ));
                        if (isset($customer->id)) {
                            $monthly_number_subscription_id = $customer->id;
                        }
                    } else {
                        $customer = \Stripe\Customer::create(array(
                                "plan" => $monthlypackage['MonthlyNumberPackage']['plan'],
                                "source" => $_POST['stripeToken'],
                                "email" => $this->Session->read('User.email'),
                                "metadata" => array("username" => $username, "firstname" => $firstname, "lastname" => $lastname)
                            )
                        );
                        $stripecustomerid = $customer->id;
                        if (isset($customer->subscriptions->data[0]->id)) {
                            $monthly_number_subscription_id = $customer->subscriptions->data[0]->id;
                        }
                    }
                    if (isset($customer->id)) {
                        if (!empty($user)) {
                            $user_arr['User']['id'] = $this->Session->read('User.id');
                            $user_arr['User']['stripe_customer_id'] = $stripecustomerid;
                            $user_arr['User']['monthly_number_subscription_id'] = $monthly_number_subscription_id;
                            $user_arr['User']['number_package'] = $monthlypackage['MonthlyNumberPackage']['id'];
                            $user_arr['User']['number_limit'] = $user['User']['number_limit'] + $monthlypackage['MonthlyNumberPackage']['total_secondary_numbers'];
                            $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
                            $user_arr['User']['number_next_renewal_dates'] = $nextdate;
                            $user_arr['User']['active'] = 1;
                            $this->User->save($user_arr);
                        }
                    }
                    $this->Session->setFlash('Thanks for your purchase! Your account was updated successfully');
                    $this->redirect(array('controller' => 'users', 'action' => 'profile'));
                } catch (\Stripe\Error\Card $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                } catch (\Stripe\Error\RateLimit $e) {
                    // Too many requests made to the API too quickly
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                } catch (\Stripe\Error\InvalidRequest $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                } catch (\Stripe\Error\Authentication $e) {
                    // Authentication with Stripe's API failed
                    // (maybe you changed API keys recently)
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                } catch (\Stripe\Error\ApiConnection $e) {
                    // Network communication with Stripe failed
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                } catch (\Stripe\Error\Base $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                } catch (Exception $e) {
                    $this->Session->setFlash($e->getMessage());
                    $this->redirect(array('controller' => 'users', 'action' => 'stripenumbers'));
                }
            } else {
                $this->Session->setFlash('Please try again');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            }
        }
    }

    function purchase_subscription_numbers($id)
    {
        $this->layout = 'popup';
        $this->loadModel('MonthlyNumberPackage');
        $this->set('monthlynumberpackage', $this->MonthlyNumberPackage->find('first', array('conditions' => array('id' => $id))));
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        $this->set('users', $users);
    }

    function purchase_credit_checkout($id)
    {
        $this->layout = 'popup';
        $this->loadModel('Package');
        $this->set('package', $this->Package->find('first', array('conditions' => array('id' => $id))));
        app::import('Model', 'Config');
        $this->Config = new Config();
        $configdata = $this->Config->find('first');
        //print_r($configdata);
        $this->set('config', $configdata);
    }


    function checkout_credit($id)
    {
        $this->layout = 'popup';
        $this->loadModel('MonthlyPackage');
        $this->set('monthlydetail', $this->MonthlyPackage->find('first', array('conditions' => array('id' => $id))));
        app::import('Model', 'Config');
        $this->Config = new Config();
        $configdata = $this->Config->find('first');
        $this->set('config', $configdata);
    }

    function paypal_credit($id)
    {
        $this->layout = 'popup';
        $this->loadModel('MonthlyPackage');
        $this->set('monthlydetail', $this->MonthlyPackage->find('first', array('conditions' => array('id' => $id))));
    }

    function account_credited()
    {
        $this->layout = 'admin_new_layout';
    }

    function listNumbers()
    {
        $response = $this->Twilio->listNumbers();
        $AvailablePhoneNumbers = $response->ResponseXml->AvailablePhoneNumbers->AvailablePhoneNumber;

        if (empty($AvailablePhoneNumbers)) {
            $this->Session->setFlash(__('We did not find any phone numbers by that search', true));
        }
        $this->set('AvailablePhoneNumbers', $AvailablePhoneNumbers);
    }

    function admin_index()
    {
        $this->User->recursive = 0;
        $conditions['AND'] = array();
        if ($this->request->data['Users']['phone'] == 1) {
            $cond = array('User.username like' => $this->request->data['users']['name'] . '%');
            array_push($conditions['AND'], $cond);
        } else if ($this->request->data['Users']['phone'] == 2) {
            $cond1 = array('User.first_name like' => $this->request->data['users']['name'] . '%');
            array_push($conditions['AND'], $cond1);
        } else if ($this->request->data['Users']['phone'] == 3) {
            $cond2 = array('User.last_name like' => $this->request->data['users']['name'] . '%');
            array_push($conditions['AND'], $cond2);
        } else if ($this->request->data['Users']['phone'] == 4) {
            $cond3 = array('User.email like' => $this->request->data['users']['name'] . '%');
            array_push($conditions['AND'], $cond3);
        } else if ($this->request->data['Users']['phone'] == 5) {
            $cond4 = array('User.assigned_number like' => $this->request->data['users']['name'] . '%');
            array_push($conditions['AND'], $cond4);
        } else if ($this->request->data['Users']['phone'] == 7) {
            $cond7 = array('User.IP_address like' => $this->request->data['users']['name'] . '%');
            array_push($conditions['AND'], $cond7);
        }
        if ($this->request->data['Users']['api_type'] != '') {
            $cond5 = array('User.api_type like' => $this->request->data['Users']['api_type']);
            //$cond5 = array('User.api_type =' => API_TYPE);
            array_push($conditions['AND'], $cond5);
        } else {
            //$default = array('User.api_type like' => $this->request->data['Users']['api_type'].'%');
            //$default = array('User.api_type =' => API_TYPE);
            $default = array('User.api_type like' => '%');
            array_push($conditions['AND'], $default);
        }

        $this->paginate = array('conditions' => $conditions);
        $data = $this->paginate('User');
        $this->set('users', $data);
    }

    function admin_add()
    {
        $this->set('title_for_layout', 'Add User');
        if (!empty($this->request->data)) {

            $accounts = $this->User->find('count', array('conditions' => array('User.active' => 1)));

            if ($accounts >= NUMACCOUNTS) {

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

                $this->Session->setFlash('System has reached maximum number of active accounts. Please contact sales@ultrasmsscript.com to upgrade your level to increase capacity.');
                $this->redirect(array('controller' => 'users', 'action' => 'admin_add'));
            }

            $activate_user = $this->User->find('first', array('conditions' => array('User.id' => $this->request->data['User']['email'])));
            if (empty($activate_user)) {
                $this->request->data['User']['register'] = 1;
                $this->request->data['User']['password'] = md5($this->request->data['User']['passwrd']);
                /*if (API_TYPE !=2 ) {
                        $this->request->data['User']['assigned_number'] = 0;
                }*/
                if ($this->request->data['User']['api_type'] != 2) {
                    $this->request->data['User']['assigned_number'] = 0;
                    
                    if ($this->request->data['User']['api_type'] == 7) {
                        if(trim($this->request->data['User']['sid'])=='' && (TELNYX_KEY=='' || is_null(TELNYX_KEY) || empty(TELNYX_KEY))){
                             $this->Session->setFlash(__('Please add your Telnyx API Key in the admin config or here before creating this user', true));
                             $this->redirect(array('action' => 'add'));
                        }else{
                            if(trim($this->request->data['User']['sid']) !=''){
                                $billresponse = $this->Telnyx->createbillinggroup(trim($this->request->data['User']['sid']),$this->request->data['User']['username']); 
                                $response = $this->Telnyx->createmessagingprofile(trim($this->request->data['User']['sid']),$this->request->data['User']['username']);
                                $callresponse = $this->Telnyx->createcallapplication(trim($this->request->data['User']['sid']),$this->request->data['User']['username']);
    				            $voiceresponse = $this->Telnyx->createvoiceprofile(trim($this->request->data['User']['sid']),$billresponse->data->id,$this->request->data['User']['username']);
                		        $updatecallresponse = $this->Telnyx->updatecallapplication(trim($this->request->data['User']['sid']),$callresponse->data->id,$voiceresponse->data->id);
                            }else{
                                $billresponse = $this->Telnyx->createbillinggroup(TELNYX_KEY,$this->request->data['User']['username']);
                                $response = $this->Telnyx->createmessagingprofile(TELNYX_KEY,$this->request->data['User']['username']);
                                $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY,$this->request->data['User']['username']);
    				            $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billresponse->data->id,$this->request->data['User']['username']);
                		        $updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
                            }
                            if(!isset($billresponse->errors)){
                                $this->request->data['User']['billinggroupid'] = $billresponse->data->id;
                            }
                            
                            $this->request->data['User']['app_id'] = $response->data->id;
            		        $this->request->data['User']['call_app_id'] = $callresponse->data->id;
                        }
            		    
                    }
                    
                    /*if ($this->request->data['User']['api_type']==0){
                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        $response = $this->Twilio->createsubaccount($this->request->data['User']['username']);
                        $authToken = $response->ResponseXml->Account->AuthToken;
                        $Sid = $response->ResponseXml->Account->Sid;
                        $this->request->data['User']['SID'] = $Sid;
                        $this->request->data['User']['AuthToken'] = $authToken;
                    }else if($this->request->data['User']['api_type']==3){
                
                    }*/
                }
                $this->request->data['User']['sms_balance'] = FREE_SMS;
                $this->request->data['User']['voice_balance'] = FREE_VOICE;
                //$this->request->data['User']['api_type'] = API_TYPE;

                $country = $this->request->data['User']['user_country'];

                if ($country == "Australia") {
                    $this->request->data['User']['alphasender'] = AUSTRALIA;
                } else if ($country == "Austria") {
                    $this->request->data['User']['alphasender'] = AUSTRIA;
                } else if ($country == "Belgium") {
                    $this->request->data['User']['alphasender'] = BELGIUM;
                } else if ($country == "Canada") {
                    $this->request->data['User']['alphasender'] = CANADA;
                } else if ($country == "Denmark") {
                    $this->request->data['User']['alphasender'] = DENMARK;
                } else if ($country == "Estonia") {
                    $this->request->data['User']['alphasender'] = ESTONIA;
                } else if ($country == "Finland") {
                    $this->request->data['User']['alphasender'] = FINLAND;
                } else if ($country == "France") {
                    $this->request->data['User']['alphasender'] = FRANCE;
                } else if ($country == "Germany") {
                    $this->request->data['User']['alphasender'] = GERMANY;
                } else if ($country == "Hong Kong") {
                    $this->request->data['User']['alphasender'] = HONGKONG;
                } else if ($country == "Hungary") {
                    $this->request->data['User']['alphasender'] = HUNGARY;
                } else if ($country == "Ireland") {
                    $this->request->data['User']['alphasender'] = IRELAND;
                } else if ($country == "Israel") {
                    $this->request->data['User']['alphasender'] = ISRAEL;
                } else if ($country == "Italy") {
                    $this->request->data['User']['alphasender'] = ITALY;
                } else if ($country == "Lithuania") {
                    $this->request->data['User']['alphasender'] = LITHUANIA;
                } else if ($country == "Mexico") {
                    $this->request->data['User']['alphasender'] = MEXICO;
                } else if ($country == "Netherlands") {
                    $this->request->data['User']['alphasender'] = NETHERLANDS;
                } else if ($country == "Norway") {
                    $this->request->data['User']['alphasender'] = NORWAY;
                } else if ($country == "Poland") {
                    $this->request->data['User']['alphasender'] = POLAND;
                } else if ($country == "Puerto Rico") {
                    $this->request->data['User']['alphasender'] = PUERTORICO;
                } else if ($country == "Spain") {
                    $this->request->data['User']['alphasender'] = SPAIN;
                } else if ($country == "Sweden") {
                    $this->request->data['User']['alphasender'] = SWEDEN;
                } else if ($country == "Switzerland") {
                    $this->request->data['User']['alphasender'] = SWITZERLAND;
                } else if ($country == "United Kingdom") {
                    $this->request->data['User']['alphasender'] = UNITEDKINGDOM;
                } else if ($country == "United States") {
                    $this->request->data['User']['alphasender'] = UNITEDSTATES;
                }
                
                //***Default user permissions based on config data
                $this->request->data['User']['autoresponders'] = AUTORESPONDERS;
                $this->request->data['User']['importcontacts'] = IMPORTCONTACTS;
                $this->request->data['User']['shortlinks'] = SHORTLINKS;
                $this->request->data['User']['voicebroadcast'] = VOICEBROADCAST;
                $this->request->data['User']['polls'] = POLLS;
                $this->request->data['User']['contests'] = CONTESTS;
                $this->request->data['User']['loyaltyprograms'] = LOYALTYPROGRAMS;
                $this->request->data['User']['kioskbuilder'] = KIOSKBUILDER;
                $this->request->data['User']['birthdaywishes'] = BIRTHDAYWISHES;
                $this->request->data['User']['mobilepagebuilder'] = MOBILEPAGEBUILDER;
                $this->request->data['User']['webwidgets'] = WEBWIDGETS;
                $this->request->data['User']['smschat'] = SMSCHAT;
                $this->request->data['User']['qrcodes'] = QRCODES;
                $this->request->data['User']['calendarscheduler'] = CALENDARSCHEDULER;
                $this->request->data['User']['appointments'] = APPOINTMENTS;
                $this->request->data['User']['groups'] = GROUPS;
                $this->request->data['User']['contactlist'] = CONTACTLIST;
                $this->request->data['User']['sendsms'] = SENDSMS;
                $this->request->data['User']['affiliates'] = AFFILIATES;
                $this->request->data['User']['getnumbers'] = GETNUMBERS;
                $this->request->data['User']['mobilecoupons'] = MOBILECOUPONS;
                $this->request->data['User']['qabots'] = QABOTS;

                if ($this->User->save($this->request->data)) {
                    
                    if ($this->request->data['User']['api_type'] == 6){
                        if (trim($this->request->data['User']['sid']) !='' && trim($this->request->data['User']['authtoken']) != ''){
                            $Email = new CakeEmail();
                            if(EMAILSMTP==1){
                                $Email->config('smtp');
                            }
                            $body = SITE_URL . " is using Ytel as the SMS gateway. Gateway Key: " . trim($this->request->data['User']['sid']) . " First name: " . trim($this->request->data['User']['first_name']) . " Last Name: " . trim($this->request->data['User']['last_name']);
                            $Email->from(array(SUPPORT_EMAIL => SITENAME));
                            $Email->to("josh@ultrasmsscript.com");
                            $Email->subject("YTEL Signup");
                            $Email->emailFormat('html');
                            $Email->send($body);
                        }
                    }
                    
                    $this->Session->setFlash(__('The user has been saved', true));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
                }
            } else {
                $this->Session->setFlash(__('The User already exists. Please, try again.', true));
            }

        }

    }

    function admin_edit($id = null, $password = null)
    {
        $this->set('title_for_layout', 'Edit User');

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid id', true));
            $this->redirect(array('action' => 'index'));
        }


        if (!empty($this->request->data)) {
            app::import('Model', 'User');
            $this->User = new User();
            $activate_user = $this->User->find('first', array('conditions' => array('User.id' => $this->request->data['User']['id'])));

            if ($this->request->data['User']['api_type'] != $activate_user['User']['api_type'] && $activate_user['User']['assigned_number'] != 0) {
                $this->Session->setFlash(__('You can not change SMS gateways because this user has numbers assigned from this gateway. You must first release all numbers associated to this user account.', true));
                $this->redirect(array('action' => 'index'));
            }

            if ($activate_user['User']['api_type'] == 2 || ($activate_user['User']['api_type'] != 2 && $this->request->data['User']['api_type'] == 2)) {
                $this->request->data['User']['assigned_number'] = $this->request->data['slooce']['assigned_number'];
            }
            
            if ($this->request->data['User']['api_type'] == 7) {
                if ($activate_user['User']['api_type'] !=7) {
                    if(trim($this->request->data['User']['sid'])=='' && (TELNYX_KEY=='' || is_null(TELNYX_KEY) || empty(TELNYX_KEY))){
                        $this->Session->setFlash(__('Please add your Telnyx API Key in the admin config before creating this user', true));
                        $this->redirect(array('action' => 'index'));
                    }else{
                        if(trim($this->request->data['User']['sid']) !=''){
                            $billresponse = $this->Telnyx->createbillinggroup(trim($this->request->data['User']['sid']),$activate_user['User']['username']); 
                            $response = $this->Telnyx->createmessagingprofile(trim($this->request->data['User']['sid']),$activate_user['User']['username']);
                            $callresponse = $this->Telnyx->createcallapplication(trim($this->request->data['User']['sid']),$activate_user['User']['username']);
    				        $voiceresponse = $this->Telnyx->createvoiceprofile(trim($this->request->data['User']['sid']),$billresponse->data->id,$activate_user['User']['username']);
                		    $updatecallresponse = $this->Telnyx->updatecallapplication(trim($this->request->data['User']['sid']),$callresponse->data->id,$voiceresponse->data->id);
                        }else{
                            $billresponse = $this->Telnyx->createbillinggroup(TELNYX_KEY,$activate_user['User']['username']);  
                            $response = $this->Telnyx->createmessagingprofile(TELNYX_KEY,$activate_user['User']['username']);
                            $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY,$activate_user['User']['username']);
    				        $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billresponse->data->id,$activate_user['User']['username']);
                		    $updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
                        }
                        if(!isset($billresponse->errors)){
                            $this->request->data['User']['billinggroupid'] = $billresponse->data->id;
                        }
                    
                        $this->request->data['User']['app_id'] = $response->data->id;
            		    $this->request->data['User']['call_app_id'] = $callresponse->data->id;
                    }
                }
            }
            
            if ($this->User->save($this->request->data)) {
                
                if ($this->request->data['User']['api_type'] == 6 && trim($activate_user['User']['sid']) == '' && trim($activate_user['User']['authtoken']) == ''){
                    if (trim($this->request->data['User']['sid']) !='' && trim($this->request->data['User']['authtoken']) != ''){
                        $Email = new CakeEmail();
                        if(EMAILSMTP==1){
                            $Email->config('smtp');
                        }
                        $body = SITE_URL . " is using Ytel as the SMS gateway. Gateway Key: " . trim($this->request->data['User']['sid']) . " First name: " . trim($this->request->data['User']['first_name']) . " Last Name: " . trim($this->request->data['User']['last_name']);
                        $Email->from(array(SUPPORT_EMAIL => SITENAME));
                        $Email->to("josh@ultrasmsscript.com");
                        $Email->subject("YTEL Signup");
                        $Email->emailFormat('html');
                        $Email->send($body);
                    }
                }
                    
                if ($this->request->data['User']['active'] == 1) {
                    if ($activate_user['User']['active'] == 0) {
                        $this->user_activate_account($activate_user['User']['account_activated'], 1);
                    }

                    app::import('Model', 'Referral');
                    $this->Referral = new Referral();
                    $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $this->request->data['User']['id'])));
                    if ($Referraldetails['Referral']['account_activated'] == 0) {
                        $referral['id'] = $Referraldetails['Referral']['id'];
                        $referral['account_activated'] = 1;
                        $this->Referral->save($referral);
                    }
                } else if ($this->request->data['User']['active'] == 0) {
                    app::import('Model', 'Referral');
                    $this->Referral = new Referral();
                    $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $this->request->data['User']['id'])));
                    if ($Referraldetails['Referral']['account_activated'] == 1) {
                        $referral['id'] = $Referraldetails['Referral']['id'];
                        $referral['account_activated'] = 0;
                        $this->Referral->save($referral);
                    }

                }

                $this->Session->setFlash(__('The user has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
            }
        }

        if (empty($this->request->data)) {
            $password = base64_decode($password);
            app::import('Model', 'User');
            $this->User = new User();
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $id, 'User.password' => $password)));

            if (!empty($someone)) {
                $this->request->data = $this->User->read(null, $id);
                $this->set('api_type', $someone['User']['api_type']);
                if ($someone['User']['api_type'] == 2) {
                    $this->set('assigned_number', $someone['User']['assigned_number']);
                } else {
                    $this->set('assigned_number', 0);
                }
            } else {
                $this->Session->setFlash(__('The user could not be found. Please, try again.', true));
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    function admin_delete($id = null, $password = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for user', true));
            $this->redirect(array('action' => 'index'));
        }
        $id = base64_decode($id);
        $password = base64_decode($password);
        app::import('Model', 'User');
        $this->User = new User();
        $someone = $this->User->find('first', array('conditions' => array('User.id' => $id, 'User.password' => $password)));
        if (!empty($someone)) {

            if ($this->User->delete($id)) {
                app::import('Model', 'Referral');
                $this->Referral = new Referral();
                $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $id)));
                if (!empty($Referraldetails)) {
                    $referral['id'] = $Referraldetails['Referral']['id'];
                    $referral['account_activated'] = 0;
                    $this->Referral->save($referral);
                }
                $this->Session->setFlash(__('User deleted', true));
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('User was not deleted', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function about()
    {
        $this->set('title_for_layout', 'About Us');
    }

    function array_push_assoc($array, $key, $value)
    {
        $array[$key] = $value;
        return $array;
    }

    function admin_user_messages()
    {
        $user = $this->User->find('all');
        $this->set('users', $user);
        if ($_REQUEST['all']) {
            $this->Session->write('user_session_id', '');
            $this->Session->write('date', '');
            //$conditions = array();
            //$conditions = $this->array_push_assoc($conditions, 'msg_type', "text");
            //$this->Session->write('session_cond', $conditions);
        }
        $userid = $this->Session->read('user_session_id');

        $date = $this->Session->read('date');
        if ($date == $this->request->data['User']['date'] && $id == $this->request->data['User']['id']) {
            if (!empty($userid)) {
                $this->request->data['User']['id'] = $userid;
            }
            if (!empty($date)) {
                $this->request->data['User']['date'] = $date;
            }

        }

        if (isset($this->request->data['User']['date']) && !empty($this->request->data['User']['date'])) {
            $call_date = explode('/', $this->request->data['User']['date']);
            if (count($call_date) == 3) {
                $date_call = $call_date[2] . "-" . $call_date[0] . "-" . $call_date[1];
            }
        } else {
            $date_call = date("Y-m-d");
        }

        if (($this->request->data['User']['date'] != '') && ($this->request->data['User']['id'] == 0)) {
            $conditions = array();
            $this->Session->write('date', $this->request->data['User']['date']);
            $this->Session->write('user_session_id', '');
            $conditions = $this->array_push_assoc($conditions, 'Log.user_id !=', "0");
            $conditions = $this->array_push_assoc($conditions, 'route !=', "");
            $conditions = $this->array_push_assoc($conditions, 'msg_type', "text");
            
            $date_call = new DateTime($date_call);
            $start = $date_call->format('Y-m-d 00:00:00');
            $end = $date_call->format('Y-m-d 23:59:59');
            
            //$conditions = $this->array_push_assoc($conditions, 'DATE(Log.created)', "$date_call");
            $conditions = $this->array_push_assoc($conditions, 'Log.created >=', "$start");
            $conditions = $this->array_push_assoc($conditions, 'Log.created <=', "$end");
            
            $this->Session->write('session_cond', $conditions);
        } else if ($this->request->data['User']['id'] != 0 && $this->request->data['User']['date'] == '') {
            $user_id = $this->request->data['User']['id'];
            $this->Session->write('user_session_id', $user_id);
            $this->Session->write('date', '');
            $conditions = array();
            $month = date('m');
            //contacts.created like '%$month%'"
            
            $firstdaymonth = new DateTime('first day of this month');
            $firstdaymonth = $firstdaymonth->format('Y-m-d 00:00:00');
            $lastdaymonth = new DateTime('last day of this month');
            $lastdaymonth = $lastdaymonth->format('Y-m-d 23:59:59');
            
            $conditions = $this->array_push_assoc($conditions, 'Log.user_id', "$user_id");
            $conditions = $this->array_push_assoc($conditions, 'route !=', "");
            $conditions = $this->array_push_assoc($conditions, 'msg_type', "text");
            //$conditions = $this->array_push_assoc($conditions, 'MONTH(Log.created)', "$month");
            $conditions = $this->array_push_assoc($conditions, 'Log.created >=', "$firstdaymonth");
            $conditions = $this->array_push_assoc($conditions, 'Log.created <=', "$lastdaymonth");
            $this->Session->write('session_cond', $conditions);
        } else if (($this->request->data['User']['id'] != 0) && ($this->request->data['User']['date'] != '')) {
            $user_id = $this->request->data['User']['id'];
            $this->Session->write('user_session_id', $user_id);
            $this->Session->write('date', $this->request->data['User']['date']);
            $conditions = array();
            
            $date_call = new DateTime($date_call);
            $start = $date_call->format('Y-m-d 00:00:00');
            $end = $date_call->format('Y-m-d 23:59:59');
            
            $conditions = $this->array_push_assoc($conditions, 'Log.user_id', "$user_id");
            $conditions = $this->array_push_assoc($conditions, 'route !=', "");
            $conditions = $this->array_push_assoc($conditions, 'msg_type', "text");
            //$conditions = $this->array_push_assoc($conditions, 'DATE(Log.created)', "$date_call");
            $conditions = $this->array_push_assoc($conditions, 'Log.created >=', "$start");
            $conditions = $this->array_push_assoc($conditions, 'Log.created <=', "$end");
            $this->Session->write('session_cond', $conditions);
        }
        $sconditions = $this->Session->read('session_cond');
        app::import('Model', 'Log');
        $this->Log = new Log();
        $this->Log->recursive = -1;
        if (trim($_REQUEST['all']) == '') {
            $this->paginate = array('conditions' => $sconditions, 'order' => array('Log.created' => 'desc'));
            $Messege = $this->paginate('Log');
            $Messege15 = $this->Log->find('all', array('conditions' => array('AND' => array($sconditions)), 'order' => 'Log.created'));
        }
        //$total = $this->Log->find('count', array('conditions' => array('AND' => array($sconditions)), 'order' => 'Log.created'));
        //$this->Session->write('total', $total);

        foreach ((array)$Messege15 as $m_list) {
            //count records day wise from calllogs table for a month
            $day = date("d", strtotime($m_list['Log']['created']));
            if (isset($month_list[$day])) {
                $month_list[$day] = $month_list[$day] + 1;
            } else {
                $month_list[$day] = 1;
            }

            $number = $m_list['Log']['phone_number'];

            if (isset($number))
                $no_list[$number] = $no_list[$number] + 1;
            else
                $no_list[$number] = 1;

        }

        $mon_list = array();
        for ($i = 0; $i < 31; $i++) {
            $j = $i + 1;
            if (strlen($j) == 1)
                $j = '0' . $j;
            if (isset($month_list[$j]) && $month_list[$j] != '')
                $mon_list[$i] = $month_list[$j];
            else
                $mon_list[$i] = 0;
        }
        $caller_list = json_encode($mon_list);
        $this->set('caller_list', $caller_list);
        $this->set('Messege', $Messege);
    }

    function admin_top_users()
    {
        $perpg = 5;
        if (isset($_REQUEST['_pn']) && $_REQUEST['_pn'] > 0 && $_REQUEST['_pn'] != '') {
            $pageNumber = $_REQUEST['_pn'];
            $perpg = "" . ($perpg * ($pageNumber - 1)) . "," . $perpg;
        } else {
            $pageNumber = 1;
        }

        if ($_REQUEST['all']) {
            $this->Session->write('todatetop', '');
            $this->Session->write('fromdatetop', '');
        }
        app::import('Model', 'Log');
        $this->Log = new Log();
        if (isset($this->request->data['date']['to']) && !empty($this->request->data['date']['from'])) {

            $this->Session->write('todatetop', $this->request->data['date']['to']);
            $this->Session->write('fromdatetop', $this->request->data['date']['from']);
            $start_call_date = explode('/', $this->request->data['date']['from']);
            $end_call_date = explode('/', $this->request->data['date']['to']);
            if (count($start_call_date) == 3) {
                $stardate = $start_call_date[2] . "-" . $start_call_date[0] . "-" . $start_call_date[1];
            }
            if (count($end_call_date) == 3) {
                $enddate = $end_call_date[2] . "-" . $end_call_date[0] . "-" . $end_call_date[1];
            }
            $stardatenew = date('Y-m-d', strtotime($stardate . " -1 day"));
            $enddatenew = date('Y-m-d', strtotime($enddate . " +1 day"));
            if ($stardate == $enddate) {
                $usersCount = $this->Log->query("SELECT p.id, p.first_name, p.last_name,p.email, p.assigned_number, COUNT( c.id ) as count FROM users p JOIN logs c ON c.user_id = p.id where c.user_id !=0 and c.route in ('outbox','inbox') and c.msg_type='text' and c.created like '" . $enddate . "%' GROUP BY p.id ORDER BY COUNT( c.id ) DESC limit  " . $perpg . "");

            } else {
                $usersCount = $this->Log->query("SELECT p.id, p.first_name, p.last_name,p.email, p.assigned_number, COUNT( c.id ) as count FROM users p JOIN logs c ON c.user_id = p.id where c.user_id !=0 and c.route in ('outbox','inbox') and c.msg_type='text' and c.created between'" . $stardate . "' and '" . $enddatenew . "' GROUP BY p.id ORDER BY COUNT( c.id ) DESC limit  " . $perpg . "");

            }

        } else {
            $currentDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $usersCount = $this->Log->query("SELECT p.id, p.first_name, p.last_name,p.email, p.assigned_number, COUNT( c.id ) as count FROM users p JOIN logs c ON c.user_id = p.id where c.user_id !=0 and c.route in ('outbox','inbox') and c.msg_type='text' and c.created LIKE '" . $currentDate . "%' GROUP BY p.id ORDER BY COUNT( c.id ) DESC limit  " . $perpg . "");
        }
        foreach ($usersCount as $userCount) {
            $first_name = $userCount['p']['first_name'];
            $total_no[$first_name] = $userCount[0]['count'];
        }
        $i = 0;
        foreach ($total_no as $key => $value) {
            $n_list[$i] = str_replace('"', '\'', json_encode(array($key, (int)$value)));
            $i++;
        }
        $number_list = str_replace('"', '', json_encode(array_values($n_list)));
        $this->set('number_list', $number_list);
        $this->set('top_users', $usersCount);

    }

    function admin_non_users()
    {
        $perpg = 5;
        if (isset($_REQUEST['_pn']) && $_REQUEST['_pn'] > 0 && $_REQUEST['_pn'] != '') {
            $pageNumber = $_REQUEST['_pn'];
            $perpg = "" . ($perpg * ($pageNumber - 1)) . "," . $perpg;
        } else {
            $pageNumber = 1;
        }
        if ($_REQUEST['all']) {
            $this->Session->write('todatenon', '');
            $this->Session->write('fromdatenon', '');
        }
        app::import('Model', 'Log');
        $this->Log = new Log();

        if (isset($this->request->data['date']['to']) && !empty($this->request->data['date']['from'])) {
            $this->Session->write('todatenon', $this->request->data['date']['to']);
            $this->Session->write('fromdatenon', $this->request->data['date']['from']);
            $start_call_date = explode('/', $this->request->data['date']['from']);
            $end_call_date = explode('/', $this->request->data['date']['to']);
            if (count($start_call_date) == 3) {
                $stardate = $start_call_date[2] . "-" . $start_call_date[0] . "-" . $start_call_date[1];
            }
            if (count($end_call_date) == 3) {
                $enddate = $end_call_date[2] . "-" . $end_call_date[0] . "-" . $end_call_date[1];
            }
            $stardatenew = date('Y-m-d', strtotime($stardate . " -1 day"));
            $enddatenew = date('Y-m-d', strtotime($enddate . " +1 day"));
            if ($stardate == $enddate) {
                $logs = ("select * from users where id not in(SELECT user_id from  logs WHERE  logs.created like '" . $stardate . "%' group by logs.user_id)");
            } else {
                $logs = ("select * from users where id not in(SELECT user_id from  logs WHERE logs.created between '" . $stardate . "' and '" . $enddatenew . "' group by logs.user_id)");
            }
            $data = $this->Log->query($logs);
            $this->set('non_users', $data);
        }

    }

    /*function reports()
    {
        $this->layout = 'admin_new_layout';
        $user = $this->Session->read('User');
        $perpg = 5;

        if (isset($_REQUEST['_pn']) && $_REQUEST['_pn'] > 0 && $_REQUEST['_pn'] != '') {
            $pageNumber = $_REQUEST['_pn'];
            $perpg = "" . ($perpg * ($pageNumber - 1)) . "," . $perpg;
        } else {
            $pageNumber = 1;
        }

        if (isset($this->request->data['User']['start']) && !empty($this->request->data['User']['start'])) {
            $call_date = explode('/', $this->request->data['User']['start']);
            if (count($call_date) == 3) {
                $date_call_start = $call_date[2] . "-" . $call_date[0] . "-" . $call_date[1];
            }
            $date_call_end = explode('/', $this->request->data['User']['end']);
            if (count($date_call_end) == 3) {
                $date_call_end = $date_call_end[2] . "-" . $date_call_end[0] . "-" . $date_call_end[1];
            }
            $date_call_start_actuall = $date_call_start;
            $date_call_end_actuall = $date_call_end;
            $date_call_start = date('Y-m-d', strtotime($date_call_start . " -1 day"));
            $date_call_end = date('Y-m-d', strtotime($date_call_end . " +1 day"));

        } elseif (isset($_REQUEST['_pn'])) {
            $date_call_start = $this->Session->read('startdate');
            $date_call_end = $this->Session->read('enddate');
            $date_call_start_actuall = $this->Session->read('date_call_start_actuall');
            $date_call_end_actuall = $this->Session->read('date_call_end_actuall');
        } else {
            $date_call_start = '';
            $date_call_end = '';

        }
        if ($date_call_start_actuall != '') {
            app::import('Model', 'Log');
            $this->Log = new Log();
            $conditions = array();

            if ($date_call_start_actuall == $date_call_end_actuall) {


                $query1 = "SELECT * FROM logs  left join groups  on groups.id = logs.group_id WHERE logs.user_id = " . $user['id'] . " and msg_type='text' and  logs.created like '%" . $date_call_start_actuall . "%' order by logs.id desc limit " . $perpg . "";
                $query = "SELECT * FROM logs  left join groups  on groups.id = logs.group_id WHERE logs.user_id = " . $user['id'] . " and msg_type='text' and  logs.created like '%" . $date_call_start_actuall . "%'";

            } else {

                $query1 = "SELECT * FROM logs  left join groups  on groups.id = logs.group_id WHERE logs.user_id = " . $user['id'] . " and msg_type='text' and  logs.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by logs.id desc limit " . $perpg . "";

                $query = "SELECT * FROM logs  left join groups  on groups.id = logs.group_id WHERE logs.user_id = " . $user['id'] . " and msg_type='text' and  logs.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "'";

            }
            $Messege = $this->Log->query($query1);
            $Messege15 = $this->Log->query($query);
            $total = count($Messege15);
            $this->Session->write('total', $total);
        } else {
            app::import('Model', 'Log');
            $this->Log = new Log();
            $month = date('m');

            $query1 = "SELECT * FROM logs  left join groups  on groups.id = logs.group_id WHERE logs.user_id = " . $user['id'] . " and msg_type='text' and  MONTH(logs.created) = '$month'";

            $query = "SELECT * FROM logs  left join groups  on groups.id = logs.group_id WHERE logs.user_id = " . $user['id'] . " and  MONTH(logs.created) = '$month'  and msg_type='text' order by logs.id desc limit " . $perpg . "";

            $Messege = $this->Log->query($query);
            $Messege15 = $this->Log->query($query1);
            $total = count($Messege15);
        }


        foreach ($Messege15 as $m_list) {
            $day = date("d", strtotime($m_list['logs']['created']));
            if (isset($month_list[$day])) {
                $month_list[$day] = $month_list[$day] + 1;
            } else {
                $month_list[$day] = 1;
            }
        }

        $mon_list = array();
        for ($i = 0; $i < 31; $i++) {
            $j = $i + 1;
            if (strlen($j) == 1)
                $j = '0' . $j;
            if (isset($month_list[$j]) && $month_list[$j] != '')
                $mon_list[$i] = $month_list[$j];
            else
                $mon_list[$i] = 0;
        }
        $caller_list = json_encode($mon_list);
        $this->set('caller_list', $caller_list);
        $this->set('Messege', $Messege);
        $this->set('start', $date_call_start_actuall);
        $this->set('end', $date_call_end_actuall);

        $this->Paginationclass->intPageSize = 5;
        $this->Paginationclass->strFunctionName = "showExtension";
        $this->Paginationclass->arrVariables = array();
        $this->Paginationclass->arrVariables[0] = $total;
        $this->Paginationclass->setTotalRecords($total);

        $TotalPages = $this->Paginationclass->getTotalPages();
        $strPagination = $this->Paginationclass->showPagination($pageNumber);
        $this->Session->write('startdate', $date_call_start);
        $this->Session->write('enddate', $date_call_end);
        $this->Session->write('date_call_start_actuall', $date_call_start_actuall);
        $this->Session->write('date_call_end_actuall', $date_call_end_actuall);
        $this->set('TotalPages', $TotalPages);
        $this->set('strPagination', $strPagination);
    }*/

    function subscribers()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        $perpg = 5;
        if (isset($_REQUEST['_pn']) && $_REQUEST['_pn'] > 0 && $_REQUEST['_pn'] != '') {
            $pageNumber = $_REQUEST['_pn'];
            $perpg = "" . ($perpg * ($pageNumber - 1)) . "," . $perpg;
        } else {
            $pageNumber = 1;
        }
        if (isset($this->request->data['User']['start']) && !empty($this->request->data['User']['start'])) {
            $call_date = explode('/', $this->request->data['User']['start']);
            if (count($call_date) == 3) {
                $date_call_start = $call_date[2] . "-" . $call_date[0] . "-" . $call_date[1];
            }
            $date_call_end = explode('/', $this->request->data['User']['end']);
            if (count($date_call_end) == 3) {
                $date_call_end = $date_call_end[2] . "-" . $date_call_end[0] . "-" . $date_call_end[1];
            }
            $date_call_start_actuall = $date_call_start;
            $date_call_end_actuall = $date_call_end;
            $date_call_start = date('Y-m-d', strtotime($date_call_start . " -1 day"));
            $date_call_end = date('Y-m-d', strtotime($date_call_end . " +1 day"));
            //$date_call_start_actuall = date('Y-m-d',strtotime($date_call_start ." +1 day"));
            //$date_call_end_actuall = date('Y-m-d',strtotime($date_call_end ." -1 day"));
        } elseif (isset($_REQUEST['_pn'])) {
            $date_call_start = $this->Session->read('startdate');
            $date_call_end = $this->Session->read('enddate');
            $date_call_start_actuall = $this->Session->read('date_call_start_actuall');
            $date_call_end_actuall = $this->Session->read('date_call_end_actuall');
        } else {
            $date_call_start = '';
            $date_call_end = '';
        }

        if ($date_call_start_actuall != '') {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');
            if ($date_call_start_actuall == $date_call_end_actuall) {

                $query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created like '%" . $date_call_start_actuall . "%' order by contact_groups.created desc limit " . $perpg . "";
                $query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created like '%" . $date_call_start_actuall . "%'";
                //$querynolimit = "SELECT * FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.un_subscribers=0 and contact_groups.user_id = " . $user_id . " and  contact_groups.created like '%" . $date_call_start_actuall . "%' order by contact_groups.created desc";

            } else {

                $query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by contact_groups.created desc limit " . $perpg . "";
                $query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "'";
                //$querynolimit = "SELECT * FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id   WHERE contact_groups.un_subscribers=0 and contact_groups.user_id = " . $user_id . " and  contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by contact_groups.created desc";

            }
            $subscribers = $this->Contact->query($query1);
            //$subscribersnolimit = $this->Contact->query($querynolimit);
            //$this->Session->write('subscribers', $subscribersnolimit);
            $subscribers1 = $this->Contact->query($query);
            $this->Session->write('subscribers', $subscribers1);
            $total = count($subscribers1);
            $this->Session->write('total', $total);
        } else {

            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');
            
            $firstdaymonth = new DateTime('first day of this month');
            $firstdaymonth = $firstdaymonth->format('Y-m-d 00:00:00');
            $lastdaymonth = new DateTime('last day of this month');
            $lastdaymonth = $lastdaymonth->format('Y-m-d 23:59:59');

            //$query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' order by contact_groups.created desc limit " . $perpg . "";
            $query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created >= '" . $firstdaymonth . "' and contact_groups.created <= '" . $lastdaymonth . "' order by contact_groups.created desc limit " . $perpg . "";

            //$querynolimit = "SELECT * FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.un_subscribers=0 and contact_groups.user_id = " . $user_id . " and MONTH(contact_groups.created) = '$month' order by contact_groups.created desc";

            //$query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month'";
            $query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created >= '" . $firstdaymonth . "' and contact_groups.created <= '" . $lastdaymonth . "'";

            $subscribers = $this->Contact->query($query1);
            //$subscribersnolimit = $this->Contact->query($querynolimit);
            //$this->Session->write('subscribers', $subscribersnolimit);
            $subscribers1 = $this->Contact->query($query);
            $this->Session->write('subscribers', $subscribers1);
            $total = count($subscribers1);
            $this->Session->write('total', $total);
        }


        foreach ($subscribers1 as $m_list) {
            $day = date("d", strtotime($m_list['contact_groups']['created']));
            if (isset($month_list[$day])) {
                $month_list[$day] = $month_list[$day] + 1;
            } else {
                $month_list[$day] = 1;
            }
        }

        $mon_list = array();
        for ($i = 0; $i < 31; $i++) {
            $j = $i + 1;
            if (strlen($j) == 1)
                $j = '0' . $j;
            if (isset($month_list[$j]) && $month_list[$j] != '')
                $mon_list[$i] = $month_list[$j];
            else
                $mon_list[$i] = 0;
        }
        $caller_list = json_encode($mon_list);
        $this->set('caller_list', $caller_list);
        $this->set('start', $date_call_start_actuall);
        $this->set('end', $date_call_end_actuall);
        $this->set('subscribers', $subscribers);
        $this->Paginationclass->intPageSize = 5;
        $this->Paginationclass->strFunctionName = "showExtension";
        $this->Paginationclass->arrVariables = array();
        $this->Paginationclass->arrVariables[0] = $total;
        $this->Paginationclass->setTotalRecords($total);
        $TotalPages = $this->Paginationclass->getTotalPages();
        $strPagination = $this->Paginationclass->showPagination($pageNumber);
        $this->Session->write('startdate', $date_call_start);
        $this->Session->write('enddate', $date_call_end);
        $this->Session->write('date_call_start_actuall', $date_call_start_actuall);
        $this->Session->write('date_call_end_actuall', $date_call_end_actuall);
        $this->set('TotalPages', $TotalPages);
        $this->set('strPagination', $strPagination);
    }

    function unsubscribers()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        $perpg = 5;
        if (isset($_REQUEST['_pn']) && $_REQUEST['_pn'] > 0 && $_REQUEST['_pn'] != '') {
            $pageNumber = $_REQUEST['_pn'];
            $perpg = "" . ($perpg * ($pageNumber - 1)) . "," . $perpg;
        } else {
            $pageNumber = 1;
        }

        if (isset($this->request->data['User']['start']) && !empty($this->request->data['User']['start'])) {
            $call_date = explode('/', $this->request->data['User']['start']);
            if (count($call_date) == 3) {
                $date_call_start = $call_date[2] . "-" . $call_date[0] . "-" . $call_date[1];
            }
            $date_call_end = explode('/', $this->request->data['User']['end']);
            if (count($date_call_end) == 3) {
                $date_call_end = $date_call_end[2] . "-" . $date_call_end[0] . "-" . $date_call_end[1];
            }
            $date_call_start_actuall = $date_call_start;
            $date_call_end_actuall = $date_call_end;
            $date_call_start = date('Y-m-d', strtotime($date_call_start . " -1 day"));
            $date_call_end = date('Y-m-d', strtotime($date_call_end . " +1 day"));
            //$date_call_start_actuall = date('Y-m-d',strtotime($date_call_start ." +1 day"));
            //$date_call_end_actuall = date('Y-m-d',strtotime($date_call_end ." -1 day"));
        } elseif (isset($_REQUEST['_pn'])) {
            $date_call_start = $this->Session->read('startdate');
            $date_call_end = $this->Session->read('enddate');
            $date_call_start_actuall = $this->Session->read('date_call_start_actuall');
            $date_call_end_actuall = $this->Session->read('date_call_end_actuall');
        } else {
            $date_call_start = '';
            $date_call_end = '';

        }

        if ($date_call_start_actuall != '') {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');
            if ($date_call_start_actuall == $date_call_end_actuall) {
                
                $query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.user_id = " . $user_id . " and  contact_groups.un_subscribers=1 and contact_groups.created like '%" . $date_call_start_actuall . "%' order by contact_groups.created desc limit " . $perpg . "";
                $query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=1 and contact_groups.created like '%" . $date_call_start_actuall . "%'";

                //$querynolimit = "SELECT * FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id   WHERE contact_groups.un_subscribers=1 and contact_groups.user_id = " . $user_id . " and  contact_groups.created like '%" . $date_call_start_actuall . "%' order by contact_groups.created desc";

            } else {
                $query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=1 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by contact_groups.created desc limit " . $perpg . "";
                $query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=1 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "'";

                //$querynolimit = "SELECT * FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id   WHERE contact_groups.un_subscribers=1 and contact_groups.user_id = " . $user_id . " and  contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by contact_groups.created desc";
            }
            $subscribers = $this->Contact->query($query1);
            //$unsubscribersnolimit = $this->Contact->query($querynolimit);
            //$this->Session->write('unsubscribers', $unsubscribersnolimit);
            $subscribers1 = $this->Contact->query($query);
            $this->Session->write('unsubscribers', $subscribers1);
            $total = count($subscribers1);
            $this->Session->write('total', $total);
        } else {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');
            
            $firstdaymonth = new DateTime('first day of this month');
            $firstdaymonth = $firstdaymonth->format('Y-m-d 00:00:00');
            $lastdaymonth = new DateTime('last day of this month');
            $lastdaymonth = $lastdaymonth->format('Y-m-d 23:59:59');
            
            //$query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.un_subscribers=1 and contact_groups.user_id = " . $user_id . " and MONTH(contact_groups.created) = '$month' order by contact_groups.created desc limit " . $perpg . "";
            $query1 = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.created FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.un_subscribers=1 and contact_groups.user_id = " . $user_id . " and contact_groups.created >= '" . $firstdaymonth . "' and contact_groups.created <= '" . $lastdaymonth . "' order by contact_groups.created desc limit " . $perpg . "";

            //$querynolimit = "SELECT * FROM contact_groups left join contacts on contacts.id=contact_groups.contact_id left join groups on groups.id= contact_groups.group_id WHERE contact_groups.un_subscribers=1 and contact_groups.user_id = " . $user_id . " and MONTH(contact_groups.created) = '$month' order by contact_groups.created desc";

            //$query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.subscribed_by_sms,contact_groups.created FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=1 and MONTH(contact_groups.created) = '$month'";
            $query = "SELECT contacts.name,groups.group_name,contacts.phone_number,contact_groups.created FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=1 and contact_groups.created >= '" . $firstdaymonth . "' and contact_groups.created <= '" . $lastdaymonth . "'";

            $subscribers = $this->Contact->query($query1);
            //$unsubscribersnolimit = $this->Contact->query($querynolimit);
            //$this->Session->write('unsubscribers', $unsubscribersnolimit);;
            $subscribers1 = $this->Contact->query($query);
            $this->Session->write('unsubscribers', $subscribers1);
            $total = count($subscribers1);
            $this->Session->write('total', $total);

        }


        foreach ($subscribers1 as $m_list) {
            $day = date("d", strtotime($m_list['contact_groups']['created']));
            if (isset($month_list[$day])) {
                $month_list[$day] = $month_list[$day] + 1;
            } else {
                $month_list[$day] = 1;
            }
        }

        $mon_list = array();
        for ($i = 0; $i < 31; $i++) {
            $j = $i + 1;
            if (strlen($j) == 1)
                $j = '0' . $j;
            if (isset($month_list[$j]) && $month_list[$j] != '') {
                $mon_list[$i] = $month_list[$j];
            } else {
                $mon_list[$i] = 0;
            }
        }
        $caller_list = json_encode($mon_list);
        $this->set('caller_list', $caller_list);
        $this->set('start', $date_call_start_actuall);
        $this->set('end', $date_call_end_actuall);
        $this->set('subscribers', $subscribers);

        $this->Paginationclass->intPageSize = 5;
        $this->Paginationclass->strFunctionName = "showExtension";
        $this->Paginationclass->arrVariables = array();
        $this->Paginationclass->arrVariables[0] = $total;
        $this->Paginationclass->setTotalRecords($total);

        $TotalPages = $this->Paginationclass->getTotalPages();
        $strPagination = $this->Paginationclass->showPagination($pageNumber);
        $this->Session->write('startdate', $date_call_start);
        $this->Session->write('enddate', $date_call_end);
        $this->Session->write('date_call_start_actuall', $date_call_start_actuall);
        $this->Session->write('date_call_end_actuall', $date_call_end_actuall);
        $this->set('TotalPages', $TotalPages);
        $this->set('strPagination', $strPagination);
    }

    /*function keywords()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Group');
        $this->Group = new Group();
        $group = $this->Group->find('list', array('conditions' => array('Group.user_id' => $user_id), 'fields' => 'Group.group_name', 'order' => array('Group.group_name' => 'asc')));
        $this->set('groups', $group);
        $perpg = 5;
        if (isset($_REQUEST['_pn']) && $_REQUEST['_pn'] > 0 && $_REQUEST['_pn'] != '') {
            $pageNumber = $_REQUEST['_pn'];
            $perpg = "" . ($perpg * ($pageNumber - 1)) . "," . $perpg;
        } else {
            $pageNumber = 1;
        }
        if (isset($this->request->data['User']['start']) && !empty($this->request->data['User']['start'])) {
            $call_date = explode('/', $this->request->data['User']['start']);
            if (count($call_date) == 3) {
                $date_call_start = $call_date[2] . "-" . $call_date[0] . "-" . $call_date[1];
            }
            $date_call_end = explode('/', $this->request->data['User']['end']);
            if (count($date_call_end) == 3) {
                $date_call_end = $date_call_end[2] . "-" . $date_call_end[0] . "-" . $date_call_end[1];
            }
            $date_call_start_actuall = $date_call_start;
            $date_call_end_actuall = $date_call_end;
            $date_call_start = date('Y-m-d', strtotime($date_call_start . " -1 day"));
            $date_call_end = date('Y-m-d', strtotime($date_call_end . " +1 day"));
        } elseif (isset($_REQUEST['_pn'])) {
            $date_call_start = $this->Session->read('startdate');
            $date_call_end = $this->Session->read('enddate');
            $this->request->data['Group']['id'] = $this->Session->read('Groupid');
            $this->request->data['Group']['groupsubscribers'] = $this->Session->read('groupsubscribers');
            $date_call_start_actuall = $this->Session->read('date_call_start_actuall');
            $date_call_end_actuall = $this->Session->read('date_call_end_actuall');
        } else {
            $date_call_start = '';
            $date_call_end = '';
        }
        if ($date_call_start_actuall != '' && $this->request->data['Group']['id'] != 0) {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');
            if ($date_call_start_actuall == $date_call_end_actuall) {
                $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and  contact_groups.created like '%" . $date_call_start_actuall . "%' and  groups.id= " . $this->request->data['Group']['id'] . " order by contact_groups.created desc limit " . $perpg . "";

                $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and  contact_groups.created like '%" . $date_call_start_actuall . "%' and  groups.id= " . $this->request->data['Group']['id'] . " order by contact_groups.created desc";

                $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.created like '%" . $date_call_start_actuall . "%' and contact_groups.un_subscribers=0 and groups.id= " . $this->request->data['Group']['id'] . "";

            } else {
                $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and  contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' and  groups.id= " . $this->request->data['Group']['id'] . " order by contact_groups.created desc limit " . $perpg . "";

                $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and  contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' and  groups.id= " . $this->request->data['Group']['id'] . " order by contact_groups.created desc";

                $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE contact_groups.user_id = " . $user_id . " and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' and contact_groups.un_subscribers=0 and groups.id= " . $this->request->data['Group']['id'] . "";

            }
            $subscribers = $this->Contact->query($query1);
            $keywords = $this->Contact->query($keywordnolimit);
            $this->Session->write('keyword', $keywords);
            $subscribers1 = $this->Contact->query($query);
            $total = count($subscribers1);
            $this->Session->write('total', $total);
        } else if ($date_call_start_actuall != '') {
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');

            if ($date_call_start_actuall == $date_call_end_actuall) {
                $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and  contact_groups.un_subscribers=0 and contact_groups.created like '%" . $date_call_start_actuall . "%' order by contact_groups.created desc limit " . $perpg . "";

                $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and  contact_groups.un_subscribers=0 and contact_groups.created like '%" . $date_call_start_actuall . "%' order by contact_groups.created desc";

                $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created like '%" . $date_call_start_actuall . "%'";

            } else {

                $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and  contact_groups.un_subscribers=0 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by contact_groups.created desc limit " . $perpg . "";

                $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and  contact_groups.un_subscribers=0 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "' order by contact_groups.created desc";

                $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and contact_groups.created between '" . $date_call_start_actuall . "' and '" . $date_call_end . "'";
            }

            $subscribers = $this->Contact->query($query1);
            $keywords = $this->Contact->query($keywordnolimit);
            $this->Session->write('keyword', $keywords);
            $subscribers1 = $this->Contact->query($query);
            $total = count($subscribers1);
            $this->Session->write('total', $total);
        } else if ($this->request->data['Group']['id'] != 0) {

            $this->Session->write('Groupid', $this->request->data['Group']['id']);
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');
            if ($this->request->data['Group']['groupsubscribers'] != '') {
                $this->Session->write('groupsubscribers', $this->request->data['Group']['groupsubscribers']);

                $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' and  groups.id= " . $this->request->data['Group']['id'] . "  and  contact_groups.group_subscribers= '" . $this->request->data['Group']['groupsubscribers'] . "' order by contact_groups.created desc limit " . $perpg . "";

                $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' and  groups.id= " . $this->request->data['Group']['id'] . "  and  contact_groups.group_subscribers= '" . $this->request->data['Group']['groupsubscribers'] . "' order by contact_groups.created desc";

                $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' and  groups.id= " . $this->request->data['Group']['id'] . " and contact_groups.group_subscribers='" . $this->request->data['Group']['groupsubscribers'] . "'";

            } else {

                $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' and  groups.id= " . $this->request->data['Group']['id'] . " order by contact_groups.created desc limit " . $perpg . "";

                $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' and  groups.id= " . $this->request->data['Group']['id'] . " order by contact_groups.created desc";

                $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id   WHERE  contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' and  groups.id= " . $this->request->data['Group']['id'] . "";
            }
            $subscribers = $this->Contact->query($query1);
            $keywords = $this->Contact->query($keywordnolimit);
            $this->Session->write('keyword', $keywords);
            $subscribers1 = $this->Contact->query($query);
            $total = count($subscribers1);
            $this->Session->write('total', $total);

        } else {
            $this->Session->write('Groupid', 0);
            $this->Session->write('groupsubscribers', '');
            app::import('Model', 'Contact');
            $this->Contact = new Contact();
            $month = date('m');

            $query1 = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' order by contact_groups.created desc limit " . $perpg . "";

            $keywordnolimit = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month' order by contact_groups.created desc";

            $query = "SELECT * FROM contact_groups left join groups on groups.id=contact_groups.group_id left join contacts on contacts.id= contact_groups.contact_id  WHERE contact_groups.user_id = " . $user_id . " and contact_groups.un_subscribers=0 and MONTH(contact_groups.created) = '$month'";

            $subscribers = $this->Contact->query($query1);
            $keywords = $this->Contact->query($keywordnolimit);
            $this->Session->write('keyword', $keywords);
            $subscribers1 = $this->Contact->query($query);
            $total = count($subscribers1);
            $this->Session->write('total', $total);
        }

        foreach ($subscribers1 as $m_list) {
            $day = date("d", strtotime($m_list['contact_groups']['created']));
            if (isset($month_list[$day])) {
                $month_list[$day] = $month_list[$day] + 1;
            } else {
                $month_list[$day] = 1;
            }

        }

        $mon_list = array();
        for ($i = 0; $i < 31; $i++) {
            $j = $i + 1;
            if (strlen($j) == 1)
                $j = '0' . $j;
            if (isset($month_list[$j]) && $month_list[$j] != '') {
                $mon_list[$i] = $month_list[$j];
            } else {
                $mon_list[$i] = 0;
            }

        }
        $caller_list = json_encode($mon_list);
        $this->set('caller_list', $caller_list);
        $this->set('groupsubscribers', $this->request->data['Group']['groupsubscribers']);
        $this->set('start', $date_call_start_actuall);
        $this->set('end', $date_call_end_actuall);
        $this->set('subscribers', $subscribers);
        $this->Paginationclass->intPageSize = 5;
        $this->Paginationclass->strFunctionName = "showExtension";
        $this->Paginationclass->arrVariables = array();
        $this->Paginationclass->arrVariables[0] = $total;
        $this->Paginationclass->setTotalRecords($total);
        $TotalPages = $this->Paginationclass->getTotalPages();
        $strPagination = $this->Paginationclass->showPagination($pageNumber);
        $this->Session->write('startdate', $date_call_start);
        $this->Session->write('enddate', $date_call_end);
        $this->set('TotalPages', $TotalPages);
        $this->set('strPagination', $strPagination);
    }*/

    function checkkeyword($id = null)
    {
        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $Subscriber1 = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $id, 'ContactGroup.group_subscribers <>' => ''), 'fields' => 'ContactGroup.group_subscribers', 'group' => array('ContactGroup.group_subscribers')));
        echo 'Please select A keyword';
        echo '<br/>';
        echo '<select id="groupsubscribers" class="form-control" name="data[Group][groupsubscribers]"> ';
        echo "<option value=''>All</option>";
        foreach ($Subscriber1 as $Subscriber) {
            echo '<option value="' . $Subscriber['ContactGroup']['group_subscribers'] . '">' . $Subscriber['ContactGroup']['group_subscribers'] . '</option>';
        }
        echo '</select>';
    }

    function checksubscriber($id = null)
    {
        $this->layout = null;
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $totalsubscriber = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.group_id' => $id, 'ContactGroup.un_subscribers' => 0, 'ContactGroup.group_subscribers <>' => ''), 'fields' => 'ContactGroup.group_subscribers,count(*) as total', 'group' => array('ContactGroup.group_subscribers')));
        $this->set('totalsubscribers', $totalsubscriber);
    }

    function setting()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        if (!empty($this->request->data)) {
            app::import('Model', 'User');
            $this->User = new User();
            $this->request->data['User']['id'] = $user_id;
            $this->User->save($this->request->data);
            $this->Session->setFlash('Settings have been saved');
            $this->redirect(array('controller' => 'users', 'action' => 'setting'));
        } else {

            $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('users', $user);
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            $UserNumber = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.voice' => 1), 'fields' => 'UserNumber.number', 'order' => array('UserNumber.number' => 'asc')));
            $this->set('UserNumber', $UserNumber);
        }
    }

    function email_services()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'User');
        $this->User = new User();

        if (!empty($this->request->data)) {
            $this->request->data['User']['id'] = $user_id;

            if ($this->request->data['User']['email_service'] == 4) {

                try {
                    $authorization_code = $this->request->data['User']['email_apikey'];

                    $auth = AWeberAPI::getDataFromAweberID($authorization_code);

                    list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $auth;

                    $this->request->data['User']['consumerkey'] = $consumerKey;
                    $this->request->data['User']['consumersecret'] = $consumerSecret;
                    $this->request->data['User']['accesskey'] = $accessKey;
                    $this->request->data['User']['accesssecret'] = $accessSecret;

                } catch (AWeberAPIException $e) {
                    $this->Session->setFlash($e->message);
                }

            }
            $this->User->save($this->request->data);
            $this->Session->setFlash('Email settings have been saved');
            $this->redirect(array('controller' => 'users', 'action' => 'email_services'));
        } else {

            $users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('users', $users);

            if ($users['User']['email_apikey'] != '') {
                if ($users['User']['email_service'] == 1) { //Mailchimp

                    try {
                        $MailChimp = new MailChimp($users['User']['email_apikey']);

                        $result = $MailChimp->get('lists');
                        //print_r($result);

                        $emaillists = array();

                        if (!empty($result)) {
                            foreach ($result['lists'] as $key => $list_object) {
                                $emaillists[] = array(
                                    'id' => $list_object['id'],
                                    'name' => $list_object['name'],
                                );
                            }
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash($e->getMessage());
                    }

                } else if ($users['User']['email_service'] == 2) { //GetResponse

                    try {
                        $GetResponse = new GetResponse($users['User']['email_apikey']);

                        $result = $GetResponse->getCampaigns();
                        //print_r($result);

                        $emaillists = array();

                        if (!empty($result)) {
                            foreach ($result as $key => $list_object) {
                                $emaillists[] = array(
                                    'id' => $list_object['campaignId'],
                                    'name' => $list_object['name'],
                                );
                            }
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash($e->getMessage());
                    }

                } else if ($users['User']['email_service'] == 3) { //ActiveCampaign

                    try {

                        $ac = new ActiveCampaign($users['User']['email_apiurl'], $users['User']['email_apikey']);

                        $results = $ac->api("list/list?ids=all");

                        //print_r($results);

                        $emaillists = array();

                        if (!empty($results)) {
                            foreach ($results as $key => $list_object) {
                                if ($list_object->id != '') {
                                    $emaillists[] = array(
                                        'id' => $list_object->id,
                                        'name' => $list_object->name,
                                    );
                                }
                            }
                        }


                    } catch (Exception $e) {
                        $this->Session->setFlash($e->getMessage());
                    }

                } else if ($users['User']['email_service'] == 4) { //AWeber

                    try {

                        $aweber = new AWeberAPI($users['User']['consumerkey'], $users['User']['consumersecret']);

                        $account = $aweber->getAccount($users['User']['accesskey'], $users['User']['accesssecret']);

                        $emaillists = array();

                        if (!empty($account)) {
                            foreach ($account->lists as $key => $list_object) {
                                $emaillists[] = array(
                                    'id' => $list_object->id,
                                    'name' => $list_object->name,
                                );
                            }
                        }


                    } catch (AWeberAPIException $e) {
                        $this->Session->setFlash($e->message);
                    }

                } else if ($users['User']['email_service'] == 5) { //Sendinblue

                    try {

                        $mailin = new Mailin(SENDINBLUE_APIURL, $users['User']['email_apikey']);

                        $data = array("page" => 1,
                            "page_limit" => 50
                        );

                        $results = $mailin->get_lists($data);

                        //print_r($results);

                        $emaillists = array();

                        if (!empty($results)) {
                            foreach ((array)$results['data']['lists'] as $key => $list_object) {
                                $emaillists[] = array(
                                    'id' => $list_object['id'],
                                    'name' => $list_object['name'],
                                );
                            }
                        }


                    } catch (Exception $e) {
                        $this->Session->setFlash($e->getMessage());
                    }

                }


                $this->set('emaillists', $emaillists);
                $this->set('listid', $users['User']['email_listid']);
                $this->set('emailservice', $users['User']['email_service']);

            }


            /*$subscriber_hash = $MailChimp->subscriberHash('josh@gmail.com');

            $result = $MailChimp->patch("lists/$list_id/members/$subscriber_hash", [
				'merge_fields' => ['FNAME'=>'Davy', 'LNAME'=>'Jones'],
						]);

			$result = $MailChimp->patch("lists/$list_id/members/$subscriber_hash", [
				'email_address' => 'josh2@gmail.com',
						]);

           */

        }


    }


    function antispampolicy()
    {
    }

    function shortlinkadd()
    {
        $this->layout = 'admin_new_layout';
        if (!empty($this->request->data)) {
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Shortlink');
            $this->Shortlink = new Shortlink();

            //$urldata = 'http://api.bitly.com/v3/shorten';
            $urldata = 'https://api-ssl.bitly.com/v4/shorten';
            $fields = array('long_url' => $this->request->data['Shortlink']['url']);
            $ch = curl_init($urldata);
            $data_string = json_encode($fields); 
            $headers = array(
		    'Authorization: Bearer '. BITLY_ACCESS_TOKEN,
		    'Connection' => 'close'
		    );
		    array_push($headers, "Content-Type:application/json");
			array_push($headers, 'Content-Length: '.strlen($data_string));
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            $jsonresponse = json_decode($result);
            
            if (isset($jsonresponse->link)) {
                $short_url = $jsonresponse->link;
                $this->request->data['Shortlink']['id'] = '';
                $this->request->data['Shortlink']['user_id'] = $user_id;
                $this->request->data['Shortlink']['shortname'] = $this->request->data['Shortlink']['name'];
                $this->request->data['Shortlink']['url'] = $this->request->data['Shortlink']['url'];
                $this->request->data['Shortlink']['clicks'] = 0;
                $this->request->data['Shortlink']['short_url'] = $short_url;
                $this->request->data['Shortlink']['created'] = date('y-m-d H:i:s');
                app::import('Model', 'Shortlink');
                $this->Shortlink = new Shortlink();
                $this->Shortlink->save($this->request->data);

                $this->Session->setFlash('Short Link has been saved');
                $this->redirect(array('controller' => 'users', 'action' => 'shortlinks'));
            } else {
                $this->Session->setFlash($jsonresponse->message);
            }
        }
    }

    function shortlinks()
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'Shortlink');
        $this->Shortlink = new Shortlink();
        $user_id = $this->Session->read('User.id');
        $this->paginate = array('conditions' => array('Shortlink.user_id' => $user_id), 'order' => array('Shortlink.id' => 'asc'));
        $data = $this->paginate('Shortlink');
        $this->set('shortlink', $data);
    }

    function qrcodes()
    {
        $this->layout = 'admin_new_layout';
        if (!empty($this->request->data)) {
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'Qrcod');
            $this->Qrcod = new Qrcod();
            $data['user_id'] = $user_id;
            $data['name'] = $this->request->data['Code']['qrcode'];
            $this->Qrcod->save($data);
            $this->Session->setFlash('Web page URL QR code has been saved');
            $this->redirect(array('controller' => 'users', 'action' => 'qrcodeindex'));
        }
    }

    function qrcodeindex()
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'Qrcod');
        $this->Qrcod = new Qrcod();
        $user_id = $this->Session->read('User.id');
        $this->paginate = array('conditions' => array('Qrcod.user_id' => $user_id), 'order' => array('Qrcod.id' => 'asc'));
        $data = $this->paginate('Qrcod');
        $this->set('qrdata', $data);
    }

    function qrcodeview($id = null)
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Qrcod');
        $this->Qrcod = new Qrcod();
        $codegenrate = $this->Qrcod->find('first', array('conditions' => array('Qrcod.user_id' => $user_id, 'Qrcod.id' => $id)));
        if (!empty($codegenrate)) {
            $test = $codegenrate['Qrcod']['name'];
            $p = 'qr/' . $test . '-large.png';
            $url = SITE_URL;
            copy('test/test.png', $p);
            QRcode::png($test, 'qr/' . $test . '-large.png', 'L', 8, 2);
            $this->set('qrimage1', $url . '/qr/' . $test . '-large.png');
        }
    }

    function qrcodedelete($id = null)
    {
        app::import('Model', 'Qrcod');
        $this->Qrcod = new Qrcod();
        if ($this->Qrcod->delete($id)) {
            $this->Session->setFlash(__('QR code deleted', true));
            $this->redirect(array('controller' => 'users', 'action' => 'qrcodeindex'));
        }
    }

    function shortlinkdelete($id = null)
    {
        app::import('Model', 'Shortlink');
        $this->Shortlink = new Shortlink();
        if ($this->Shortlink->delete($id)) {
            $this->Session->setFlash(__('Short link deleted', true));
            $this->redirect(array('controller' => 'users', 'action' => 'shortlinks'));
        }
    }

    function terms_conditions()
    {

    }

    function privacy_policy()
    {

    }

    function faq()
    {

    }

    function order_confirm()
    {
        $amount = $_POST['amount'];
        //$package_name = "package2";
        $package_name = $_POST['package_name'];
        $user_id = $_POST['user_id'];
        $recurring_email = $_POST['recurring_email'];
        app::import('Model', 'User');
        $this->User = new User();
        $userresponse = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $userresponse['User']['package'])));
        $profilestartdate = PROFILESTARTDATE;
        $response = $this->Expresscheckout->CreateRecurringPayments($amount, $package_name, $profilestartdate);
        $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
        if (!empty($response['PROFILEID'])) {
            if ($response['ACK'] == 'Success') {
                $this->User->id = $_POST['user_id'];
                $this->request->data['User']['active'] = 1;
                $this->request->data['User']['next_renewal_dates'] = $nextdate;
                $this->request->data['User']['recurring_paypal_email'] = $recurring_email;
                $this->request->data['User']['sms_balance'] = $userresponse['User']['sms_balance'] + $monthlypackage['MonthlyPackage']['text_messages_credit'];
                $this->request->data['User']['voice_balance'] = $userresponse['User']['voice_balance'] + $monthlypackage['MonthlyPackage']['voice_messages_credit'];
                $this->request->data['User']['sms_credit_balance_email_alerts'] = 0;
                $this->request->data['User']['VM_credit_balance_email_alerts'] = 0;
                $this->User->save($this->request->data);
                app::import('Model', 'Referral');
                $this->Referral = new Referral();
                $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $_POST['user_id'])));
                if (!empty($Referraldetails)) {
                    $referral['id'] = $Referraldetails['Referral']['id'];
                    $referral['account_activated'] = 1;
                    $this->Referral->save($referral);
                }
                app::import('Model', 'Invoice');
                $this->Invoice = new Invoice();
                $invoice['user_id'] = $user_id;
                $invoice['amount'] = $monthlypackage['MonthlyPackage']['amount'];
                $invoice['txnid'] = $response['PROFILEID'];
                $invoice['type'] = 0;
                $invoice['created'] = date("Y-m-d");
                $this->Invoice->save($invoice);
                $this->Session->write('User.active', 1);
                $this->Session->write('User.package', $userresponse['User']['package']);
                $this->Session->setFlash(__($package_name . '  package is activated.', true));
            }
        } else {
            $this->Session->setFlash(__('Payment is not processed, Try again', true));
        }
        $this->redirect(array('controller' => 'users', 'action' => 'profile'));
    }

    function paypalpayment($user_id = null)
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $user_name = trim($this->Session->read('User.username'));
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $country = $userdetails['User']['user_country'];

        /*app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . ''), 'order' => array('MonthlyPackage.amount' => 'asc')));
        $this->set('monthlydetails', $monthlydetails);
        
        app::import('Model', 'Package');
        $this->Package = new Package();
        $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text', 'Package.user_country' => '' . trim($country) . ''), 'order' => array('Package.amount' => 'asc')));
        $this->set('Packagedetails', $Packagedetails);
        
        $Packagevoicedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice', 'Package.user_country' => '' . trim($country) . ''), 'order' => array('Package.amount' => 'asc')));
        $this->set('Packagevoicedetails', $Packagevoicedetails);*/
        
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . '', 'MonthlyPackage.username' => $user_name), 'order' => array('MonthlyPackage.amount' => 'asc')));
        
        if (empty($monthlydetails)){
            $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . '', 'MonthlyPackage.username ' => ''), 'order' => array('MonthlyPackage.amount' => 'asc')));
        }
        $this->set('monthlydetails', $monthlydetails);
        
        app::import('Model', 'Package');
        $this->Package = new Package();
        $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text', 'Package.user_country' => '' . trim($country) . '', 'Package.username' => $user_name), 'order' => array('Package.amount' => 'asc')));
        
        if (empty($Packagedetails)){
           $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text', 'Package.user_country' => '' . trim($country) . '', 'Package.username ' => ''), 'order' => array('Package.amount' => 'asc'))); 
        }
        $this->set('Packagedetails', $Packagedetails);
        
        $Packagevoicedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice', 'Package.user_country' => '' . trim($country) . '', 'Package.username' => $user_name), 'order' => array('Package.amount' => 'asc')));
        if (empty($Packagevoicedetails)){
            $Packagevoicedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice', 'Package.user_country' => '' . trim($country) . '', 'Package.username ' => ''), 'order' => array('Package.amount' => 'asc')));
        }
        $this->set('Packagevoicedetails', $Packagevoicedetails);
        
        if (!empty($this->request->data)) {
            $packageAmount = $this->request->data['User']['amount'];
            $user_id = $this->request->data['User']['id'];
            $packageName = $this->request->data['User']['package_name'];
            app::import('Model', 'User');
            $this->User = new User();
            $userdetails_arr['id'] = $this->request->data['User']['id'];
            $userdetails_arr['package'] = $this->request->data['MonthlyPackage']['packageid'];
            $this->User->save($userdetails_arr);
            $response = $this->Expresscheckout->sendrequest($packageAmount, $user_id, $packageName);
        }
    }

    function stripepayment($user_id = null)
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $user_name = trim($this->Session->read('User.username'));
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $country = $userdetails['User']['user_country'];
        
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . '', 'MonthlyPackage.username' => $user_name), 'order' => array('MonthlyPackage.amount' => 'asc')));
        
        if (empty($monthlydetails)){
            $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . '', 'MonthlyPackage.username ' => ''), 'order' => array('MonthlyPackage.amount' => 'asc')));
        }
        $this->set('monthlydetails', $monthlydetails);
        
        app::import('Model', 'Package');
        $this->Package = new Package();
        $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text', 'Package.user_country' => '' . trim($country) . '', 'Package.username' => $user_name), 'order' => array('Package.amount' => 'asc')));
        
        if (empty($Packagedetails)){
           $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text', 'Package.user_country' => '' . trim($country) . '', 'Package.username ' => ''), 'order' => array('Package.amount' => 'asc'))); 
        }
        $this->set('Packagedetails', $Packagedetails);
        
        $Packagevoicedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice', 'Package.user_country' => '' . trim($country) . '', 'Package.username' => $user_name), 'order' => array('Package.amount' => 'asc')));
        if (empty($Packagevoicedetails)){
            $Packagevoicedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice', 'Package.user_country' => '' . trim($country) . '', 'Package.username ' => ''), 'order' => array('Package.amount' => 'asc')));
        }
        $this->set('Packagevoicedetails', $Packagevoicedetails);
    }

    function paypalnumbers($user_id = null)
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $user_name = trim($this->Session->read('User.username'));
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $country = $userdetails['User']['user_country'];
        
        app::import('Model', 'MonthlyNumberPackage');
        $this->MonthlyNumberPackage = new MonthlyNumberPackage();
        $monthlydetails = $this->MonthlyNumberPackage->find('all', array('conditions' => array('MonthlyNumberPackage.status' => 1, 'MonthlyNumberPackage.country' => '' . trim($country) . '', 'MonthlyNumberPackage.username' => $user_name), 'order' => array('MonthlyNumberPackage.amount' => 'asc')));
        
        if (empty($monthlydetails)){
            $monthlydetails = $this->MonthlyNumberPackage->find('all', array('conditions' => array('MonthlyNumberPackage.status' => 1, 'MonthlyNumberPackage.country' => '' . trim($country) . '', 'MonthlyNumberPackage.username' => ''), 'order' => array('MonthlyNumberPackage.amount' => 'asc')));
        }
        $this->set('monthlydetails', $monthlydetails);
    }

    function stripenumbers($user_id = null)
    {
        $this->layout = 'admin_new_layout';
        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $user_name = trim($this->Session->read('User.username'));
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $country = $userdetails['User']['user_country'];
        
        app::import('Model', 'MonthlyNumberPackage');
        $this->MonthlyNumberPackage = new MonthlyNumberPackage();
        $monthlydetails = $this->MonthlyNumberPackage->find('all', array('conditions' => array('MonthlyNumberPackage.status' => 1, 'MonthlyNumberPackage.country' => '' . trim($country) . '', 'MonthlyNumberPackage.username' => $user_name), 'order' => array('MonthlyNumberPackage.amount' => 'asc')));
        
        if (empty($monthlydetails)){
            $monthlydetails = $this->MonthlyNumberPackage->find('all', array('conditions' => array('MonthlyNumberPackage.status' => 1, 'MonthlyNumberPackage.country' => '' . trim($country) . '', 'MonthlyNumberPackage.username' => ''), 'order' => array('MonthlyNumberPackage.amount' => 'asc')));
        }
        $this->set('monthlydetails', $monthlydetails);
    }

    function review($user_id = null)
    {
        $this->layout = 'admin_new_layout';
        $token = "";
        $this->set('user_id', $user_id);
        app::import('Model', 'User');
        $this->User = new User();
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $userdetails['User']['package'])));
        $monthlypackage['MonthlyPackage']['package_name'];
        $packageName = $monthlypackage['MonthlyPackage']['package_name'];
        if (isset($_REQUEST['token'])) {
            $token = $_REQUEST['token'];
        }
        if ($token != "") {
            $response = $this->Expresscheckout->GetShippingDetail($token, $packageName);
            $this->set('response', $response);
        }
        if (!empty($user_id)) {
            /* $this->User->id = $user_id;
			$this->request->data['User']['payment_paid'] = 1;
			$this->User->save($this->request->data);
			$this->set('user_id',$user_id); */
        }
    }

    function checkoutpayment()
    {
        $this->layout = 'admin_new_layout';

        app::import('Model', 'User');
        $this->User = new User();
        $user_id = $this->Session->read('User.id');
        $userdetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $country = $userdetails['User']['user_country'];

        app::import('Model', 'MonthlyPackage');
        $this->MonthlyPackage = new MonthlyPackage();
        $monthlydetails = $this->MonthlyPackage->find('all', array('conditions' => array('MonthlyPackage.status' => 1, 'MonthlyPackage.user_country' => '' . trim($country) . ''), 'order' => array('MonthlyPackage.amount' => 'asc')));
        $this->set('monthlydetails', $monthlydetails);

        app::import('Model', 'Config');
        $this->Config = new Config();
        $config = $this->Config->find('first');
        $this->set('config', $config);

        app::import('Model', 'Package');
        $this->Package = new Package();
        $Packagedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'text', 'Package.user_country' => '' . trim($country) . ''), 'order' => array('Package.amount' => 'asc')));
        $Packagevoicedetails = $this->Package->find('all', array('conditions' => array('Package.status' => 1, 'Package.type' => 'voice', 'Package.user_country' => '' . trim($country) . ''), 'order' => array('Package.amount' => 'asc')));
        $this->set('Packagedetails', $Packagedetails);
        $this->set('Packagevoicedetails', $Packagevoicedetails);
    }

    function invoices()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Invoice');
        $this->Invoice = new Invoice();
        $invoicedetil = $this->Invoice->find('all', array('conditions' => array('Invoice.user_id' => $user_id), 'order' => array('Invoice.id' => 'desc'), 'limit' => 5));
        $this->set('invoicedetils', $invoicedetil);

    }

    function viewallreceipt()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'Invoice');
        $this->Invoice = new Invoice();
        $this->paginate = array('conditions' => array('Invoice.user_id' => $user_id), 'order' => array('Invoice.id' => 'desc'));
        $data = $this->paginate('Invoice');
        $this->set('invoicedetils', $data);
    }

    function subscriberexport()
    {
        $this->autoRender = false;
        ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
        $subscriber = $this->Session->read('subscribers');
        $filename = "subscriber" . date("Y.m.d") . ".csv";


        $csv_file = fopen('php://output', 'w');

        header('Content-type: application/csv');
        //header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $header_row = array("Subscriber Name", "Group Name", "Phone", "Source", "Subscribed Date");
        fputcsv($csv_file, $header_row, ',', '"');
        foreach ($subscriber as $result) {
            if ($result['contact_groups']['subscribed_by_sms'] == 0) {
                $type = 'Import';
            } else if ($result['contact_groups']['subscribed_by_sms'] == 1) {
                $type = 'SMS';
            } else if ($result['contact_groups']['subscribed_by_sms'] == 2){
                $type = 'Widget';
            } else if ($result['contact_groups']['subscribed_by_sms'] == 3){
                $type = 'Kiosk';
            }
            // Array indexes correspond to the field names in your db table(s)
            $row = array(
                $result['contacts']['name'],
                $result['groups']['group_name'],
                $result['contacts']['phone_number'],
                $type,
                $result['contact_groups']['created']
            );
            fputcsv($csv_file, $row, ',', '"');
        }
        fclose($csv_file);
    }

    function unsubscriberexport()
    {
        $this->autoRender = false;
        ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
        $unsubscriber = $this->Session->read('unsubscribers');
        $filename = "Un-subscriber" . date("Y.m.d") . ".csv";
        $csv_file = fopen('php://output', 'w');
        header('Content-type: application/csv');
        //header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $header_row = array("Subscriber Name", "Group Name", "Phone", "Unsubscribed Date");
        fputcsv($csv_file, $header_row, ',', '"');
        foreach ($unsubscriber as $result) {
            //if ($result['contact_groups']['subscribed_by_sms'] == 0) {
            //    $type = 'IMP';
            //} else {
            //    $type = 'SMS';
            //}
            // Array indexes correspond to the field names in your db table(s)
            $row = array(
                $result['contacts']['name'],
                $result['groups']['group_name'],
                $result['contacts']['phone_number'],
                //$type,
                $result['contact_groups']['created']
            );
            fputcsv($csv_file, $row, ',', '"');
        }
        fclose($csv_file);
    }


    function keywordexport()
    {
        $this->autoRender = false;
        ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
        $keyword = $this->Session->read('keyword');
        $filename = "KeywordReport" . date("Y.m.d") . ".csv";
        $csv_file = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $header_row = array("Subscriber Name", "Group Name", "Keyword", "Phone", "Subscribed Date");
        fputcsv($csv_file, $header_row, ',', '"');
        foreach ($keyword as $result) {
            // Array indexes correspond to the field names in your db table(s)
            $row = array(
                $result['contacts']['name'],
                $result['groups']['group_name'],
                $result['groups']['keyword'],
                $result['contacts']['phone_number'],
                $result['contact_groups']['created']
            );
            fputcsv($csv_file, $row, ',', '"');
        }
        fclose($csv_file);

    }

    function admin_number_release($id = null,$deletefromgateway=1)
    {
        $this->autoRender = false;
        
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        app::import('Model', 'Contact');
        $this->Contact = new Contact();

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for user', true));
            $this->redirect(array('action' => 'index'));
        }
        $user_details = $this->UserNumber->find('first', array('conditions' => array('UserNumber.id' => $id)));

        $user_api = $this->User->find('first', array('conditions' => array('User.id' => $user_details['UserNumber']['user_id'])));
        $SID = trim($user_api['User']['sid']);
        $AUTHTOKEN = trim($user_api['User']['authtoken']);
        $BANDWIDTHUSERID = trim($user_api['User']['bandwidthuserid']);
        $SIGNALWIRESPACE = trim($user_api['User']['signalwirespace']);
        $API_TYPE = $user_api['User']['api_type'];

        if (!empty($user_details)) {
            if($deletefromgateway==1){
                if ($API_TYPE == 0) {
                    if ($user_details['UserNumber']['phone_sid'] != '') {
                        if ($SID == ''){
                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Twilio->AccountSid = $SID;
                            $this->Twilio->AuthToken = $AUTHTOKEN;
                        }
                        $this->Twilio->releasenumber($user_details['UserNumber']['phone_sid']);
                    }
                } else if ($API_TYPE == 3) {
                    if ($SID == ''){
                        $this->Plivo->AuthId = PLIVO_KEY;
                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                    }else{
                        $this->Plivo->AuthId = $SID;
                        $this->Plivo->AuthToken = $AUTHTOKEN;
                    }
                    $this->Plivo->delete_phone_number($user_details['UserNumber']['number']);
                    
                } else if ($API_TYPE == 4) {
                    if ($SID == ''){
                        $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$user_details['UserNumber']['number']);  
                        $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);   
                    }else{
                        $response = $this->Bandwidth->getnumberdetails($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$user_details['UserNumber']['number']); 
                        $this->Bandwidth->delete_phone_number($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$response->id);   
                    }
                    
                } else if ($API_TYPE == 5) {
                    if ($SID == ''){
                        $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$user_details['UserNumber']['phone_sid']);   
                    }else{
                        $response = $this->Signalwire->delete_phone_number($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$user_details['UserNumber']['phone_sid']);   
                    }   
                    
                    if(trim($response->code) == '22121'){
                        $error = $response->message;
                    }
                    
                } else if ($API_TYPE == 6) {
                    if ($SID == ''){
                        $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$user_details['UserNumber']['number']);   
                    }else{
                        $response = $this->Ytel->delete_phone_number($SID,$AUTHTOKEN,$user_details['UserNumber']['number']);   
                    }  
                    
                } else if ($API_TYPE == 7) {
                    if ($SID == ''){
                        $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$user_details['UserNumber']['phone_sid']);   
                    }else{
                        $response = $this->Telnyx->delete_phone_number($SID,$user_details['UserNumber']['phone_sid']);   
                    } 
     
                } else {
                    if ($SID == ''){
                        $api_key = NEXMO_KEY;
                        $api_secret = NEXMO_SECRET;
                    }else{
                        $api_key = $SID;
                        $api_secret = $AUTHTOKEN;
                    }
                    $this->Nexmo->releasenumber($user_details['UserNumber']['country_code'], $user_details['UserNumber']['number'], $api_key, $api_secret);
                }
            }

            if(trim($error)==''){
                $this->request->data['User']['id'] = $user_details['User']['id'];
                $this->request->data['User']['number_limit_count'] = $user_details['User']['number_limit_count'] - 1;
                $this->User->save($this->request->data);
    
                $this->UserNumber->delete($id);
    
                $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $user_details['UserNumber']['number']));
    
                $this->Session->setFlash(__('User number deleted', true));
            }else{
                $this->Session->setFlash(__($error, true));
            }

            $this->redirect(array('action' => 'index'));

        }
        $this->Session->setFlash(__('User number not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function user_number_release($id = null)
    {
        $this->autoRender = false;
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();

        app::import('Model', 'Contact');
        $this->Contact = new Contact();

        $user_details = $this->UserNumber->find('first', array('conditions' => array('UserNumber.id' => $id)));
        $API_TYPE = $user_details['User']['api_type'];

        if (!empty($user_details)) {
            if ($API_TYPE == 0) {
                if ($user_details['UserNumber']['phone_sid'] != '') {
                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                    $this->Twilio->releasenumber($user_details['UserNumber']['phone_sid']);
                }
            } else if ($API_TYPE == 3) {
                $this->Plivo->AuthId = PLIVO_KEY;
                $this->Plivo->AuthToken = PLIVO_TOKEN;
                $this->Plivo->delete_phone_number($user_details['UserNumber']['number']);
            } else if ($API_TYPE == 4) {
                $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$user_details['UserNumber']['number']);
                $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);
            } else if ($API_TYPE == 5) {
                $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$user_details['UserNumber']['phone_sid']);
                
                if(trim($response->code) == '22121'){
                    $error = $response->message;
                }
            } else if ($API_TYPE == 6) {
                $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$user_details['UserNumber']['number']);   
            } else if ($API_TYPE == 7) {
                $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$user_details['UserNumber']['phone_sid']);     
            } else {
                $api_key = NEXMO_KEY;
                $api_secret = NEXMO_SECRET;
                $this->Nexmo->releasenumber($user_details['UserNumber']['country_code'], $user_details['UserNumber']['number'], $api_key, $api_secret);
            }

            if(trim($error)==''){
                $this->request->data['User']['id'] = $user_details['User']['id'];
                $this->request->data['User']['number_limit_count'] = $user_details['User']['number_limit_count'] - 1;
                $this->User->save($this->request->data);
    
                $this->UserNumber->delete($id);
    
                $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $user_details['UserNumber']['number']));
    
                $this->Session->setFlash(__('User number deleted', true));
            }else{
                $this->Session->setFlash(__($error, true));
            }

            if ($API_TYPE == 0) {
                $this->redirect(array('action' => 'viewallnumber_twillio'));
            } else if ($API_TYPE == 3) {
                $this->redirect(array('action' => 'viewallnumber_plivo'));
            } else if ($API_TYPE == 4) {
                $this->redirect(array('action' => 'viewallnumber_bandwidth'));
            } else if ($API_TYPE == 5) {
                $this->redirect(array('action' => 'viewallnumber_signalwire')); 
            } else if ($API_TYPE == 6) {
                $this->redirect(array('action' => 'viewallnumber_ytel'));
            } else if ($API_TYPE == 7) {
                $this->redirect(array('action' => 'viewallnumber_telnyx')); 
            } else if ($API_TYPE == 1) {
                $this->redirect(array('action' => 'viewallnumber_nexmo'));
            }

        }
    }

    function admin_number_release_user($id = null,$deletefromgateway=1)
    {
        $this->autoRender = false;

        app::import('Model', 'Contact');
        $this->Contact = new Contact();

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for user', true));
            $this->redirect(array('action' => 'index'));
        }
        $user_details = $this->User->find('first', array('conditions' => array('User.id' => $id)));
        $SID = trim($user_details['User']['sid']);
        $AUTHTOKEN = trim($user_details['User']['authtoken']);
        $BANDWIDTHUSERID = trim($user_details['User']['bandwidthuserid']);
        $SIGNALWIRESPACE = trim($user_details['User']['signalwirespace']);
        $API_TYPE = $user_details['User']['api_type'];
        
        if (!empty($user_details)) {
            if($deletefromgateway==1){
                if ($API_TYPE == 0) {
                    if ($user_details['User']['phone_sid'] != '') {
                        if ($SID == ''){
                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Twilio->AccountSid = $SID;
                            $this->Twilio->AuthToken = $AUTHTOKEN;
                        }
                        $this->Twilio->releasenumber($user_details['User']['phone_sid']);
                    }
                } else if ($API_TYPE == 3) {
                    if ($SID == ''){
                        $this->Plivo->AuthId = PLIVO_KEY;
                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                    }else{
                        $this->Plivo->AuthId = $SID;
                        $this->Plivo->AuthToken = $AUTHTOKEN;
                    }
                    $this->Plivo->delete_phone_number($user_details['User']['assigned_number']);
                } else if ($API_TYPE == 4) {
                    if ($SID == ''){
                        $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$user_details['User']['assigned_number']);
                        $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);   
                    }else{
                        $response = $this->Bandwidth->getnumberdetails($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$user_details['User']['assigned_number']);
                        $this->Bandwidth->delete_phone_number($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$response->id);   
                    }  
                } else if ($API_TYPE == 5) {
                    if ($SID == ''){
                        $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$user_details['User']['phone_sid']);   
                    }else{
                        $response = $this->Signalwire->delete_phone_number($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$user_details['User']['phone_sid']);   
                    }  
                    
                    if(trim($response->code) == '22121'){
                        $error = $response->message;
                    }
                } else if ($API_TYPE == 6) {
                    if ($SID == ''){
                        $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$user_details['User']['assigned_number']);   
                    }else{
                        $response = $this->Ytel->delete_phone_number($SID,$AUTHTOKEN,$user_details['User']['assigned_number']);   
                    } 
                } else if ($API_TYPE == 7) {
                    if ($SID == ''){
                        $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$user_details['User']['phone_sid']);   
                    }else{
                        $response = $this->Telnyx->delete_phone_number($SID,$user_details['User']['phone_sid']);   
                    }   
                } else {
                    if ($SID == ''){
                        $api_key = NEXMO_KEY;
                        $api_secret = NEXMO_SECRET;
                    }else{
                        $api_key = $SID;
                        $api_secret = $AUTHTOKEN;
                    }
                    $this->Nexmo->releasenumber($user_details['User']['country_code'], $user_details['User']['assigned_number'], $api_key, $api_secret);
                }
            }
            
            if(trim($error)==''){
                $this->request->data['User']['id'] = $id;
                $this->request->data['User']['assigned_number'] = 0;
                $this->request->data['User']['number_limit_count'] = $user_details['User']['number_limit_count'] - 1;
                //$this->request->data['User']['api_type']=API_TYPE;
                app::import('Model', 'UserNumber');
                $this->UserNumber = new UserNumber();
                $user_numbers = $this->UserNumber->find('first', array('conditions' => array('UserNumber.user_id' => $id)));
                if (!empty($user_numbers)) {
                    $this->request->data['User']['phone_sid'] = $user_numbers['UserNumber']['phone_sid'];
                    $this->request->data['User']['assigned_number'] = $user_numbers['UserNumber']['number'];
                    $this->request->data['User']['country_code'] = $user_numbers['UserNumber']['country_code'];
                    $this->request->data['User']['sms'] = $user_numbers['UserNumber']['sms'];
                    $this->request->data['User']['mms'] = $user_numbers['UserNumber']['mms'];
                    $this->request->data['User']['voice'] = $user_numbers['UserNumber']['voice'];
                    $this->UserNumber->delete($user_numbers['UserNumber']['id']);
                }
    
                $this->User->save($this->request->data);
    
                $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $user_details['User']['assigned_number']));
    
                $this->Session->setFlash(__('User number deleted', true));
            }else{
                $this->Session->setFlash(__($error, true));
            }
            $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('User number not deleted', true));
        $this->redirect(array('action' => 'index'));
    }


    function admin_contactdelete($id = null)
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
            foreach ($contacts_members as $contacts_member) {
                $group_id = $contacts_member['ContactGroup']['group_id'];
                $un_subscribers = $contacts_member['ContactGroup']['un_subscribers'];
                if ($un_subscribers == 1) {
                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $Group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    $this->request->data['Group']['id'] = $group_id;
                    $this->request->data['Group']['totalsubscriber'] = $Group['Group']['totalsubscriber'];
                    $this->Group->save($this->request->data);
                } else {

                    app::import('Model', 'Group');
                    $this->Group = new Group();
                    $Group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    $this->request->data['Group']['id'] = $group_id;
                    $this->request->data['Group']['totalsubscriber'] = $Group['Group']['totalsubscriber'] - 1;
                    $this->Group->save($this->request->data);
                }
            }
            $this->ContactGroup->deleteAll(array('ContactGroup.contact_id' => $id));
            $this->Session->setFlash(__('Contact deleted', true));

            $this->redirect(array('action' => 'index'));

        }
        $this->Session->setFlash(__('Contact was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function admin_deleteimportsall($user_id = null)
    {
        $this->autoRender = false;

        app::import('Model', 'Group');
        $this->Group = new Group();

        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();

        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        $contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id, 'ContactGroup.subscribed_by_sms' => 0)));

        if ($this->ContactGroup->deleteAll(array('ContactGroup.user_id' => $user_id, 'ContactGroup.subscribed_by_sms' => 0))) {

            foreach ($contacts as $contacts_member) {
                $group_id = $contacts_member['ContactGroup']['group_id'];
                $un_subscribers = $contacts_member['ContactGroup']['un_subscribers'];
                $contact_id = $contacts_member['ContactGroup']['contact_id'];
                if ($un_subscribers == 0) {
                    $Group = $this->Group->find('first', array('conditions' => array('Group.id' => $group_id)));
                    $this->request->data['Group']['id'] = $group_id;
                    $this->request->data['Group']['totalsubscriber'] = $Group['Group']['totalsubscriber'] - 1;
                    $this->Group->save($this->request->data);
                }
                $this->Contact->delete($contact_id);
            }

            $this->Session->setFlash(__('All imported contacts deleted', true));
            $this->redirect(array('action' => 'index'));

        }
    }

    /*****************************************************************nexmo number list **********************************************************/

    function numberlist_nexmo()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $nexmodetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 1), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('nexmodetails', $nexmodetail);
    }

    function viewallnumber_nexmo()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 1), 'order' => array('UserNumber.id' => 'desc'));
        $nexmo_data = $this->paginate('UserNumber');
        $this->set('nexmoall_data', $nexmo_data);
    }

    function numberlist_plivo()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $plivodetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 3), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('plivodetails', $plivodetail);
    }

    function viewallnumber_plivo()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 3), 'order' => array('UserNumber.id' => 'desc'));
        $plivo_data = $this->paginate('UserNumber');
        $this->set('plivoall_data', $plivo_data);
    }
    
    function numberlist_bandwidth()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $bandwidthdetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 4), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('bandwidthdetails', $bandwidthdetail);
    }

    function viewallnumber_bandwidth()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 4), 'order' => array('UserNumber.id' => 'desc'));
        $bandwidth_data = $this->paginate('UserNumber');
        $this->set('bandwidthall_data', $bandwidth_data);
    }
    
    function numberlist_signalwire()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $signalwiredetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 5), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('signalwiredetails', $signalwiredetail);
    }
    
    function viewallnumber_signalwire()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 5), 'order' => array('UserNumber.id' => 'desc'));
        $signalwire_data = $this->paginate('UserNumber');
        $this->set('signalwireall_data', $signalwire_data);
    }
    
    function numberlist_ytel()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $yteldetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 6), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('yteldetails', $yteldetail);
    }
    
    function viewallnumber_ytel()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 6), 'order' => array('UserNumber.id' => 'desc'));
        $ytel_data = $this->paginate('UserNumber');
        $this->set('ytelall_data', $ytel_data);
    }
    
    function numberlist_telnyx()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $yteldetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 7), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('yteldetails', $yteldetail);
    }
    
    function viewallnumber_telnyx()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 7), 'order' => array('UserNumber.id' => 'desc'));
        $telnyx_data = $this->paginate('UserNumber');
        $this->set('telnyxall_data', $telnyx_data);
    }

    /*****************************************************************twillio number list **********************************************************/
    function numberlist_twillio()
    {
        $this->layout = 'popup';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $twilliodetail = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 0), 'order' => array('UserNumber.id' => 'desc'), 'limit' => 5));
        $this->set('twilliodetails', $twilliodetail);
    }

    function viewallnumber_twillio()
    {
        $this->layout = 'admin_new_layout';
        $user_id = $this->Session->read('User.id');
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $this->paginate = array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => 0), 'order' => array('UserNumber.id' => 'desc'));
        $twilliall_data = $this->paginate('UserNumber');
        $this->set('twilli_data', $twilliall_data);
    }

    function admin_allnumbers($user_id = null, $password = null)
    {
        $this->layout = 'popup';
        $user_id = base64_decode($user_id);
        $password = base64_decode($password);
        app::import('Model', 'User');
        $this->User = new User();
        $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.password' => $password)));
        $API_TYPE = $someone['User']['api_type'];
        
        if (!empty($someone)) {
            app::import('Model', 'UserNumber');
            $this->UserNumber = new UserNumber();
            app::import('Model', 'User');
            $this->User = new User();
            $numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id, 'UserNumber.api_type' => $API_TYPE), 'order' => array('UserNumber.id' => 'desc')));
            $usernumbers = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('numbers', $numbers);
            $this->set('usernumbers', $usernumbers);
            $this->set('api_type', $API_TYPE);
        } else {
            $this->Session->setFlash(__('Numbers for this user could not be found. Please, try again.', true));
            $this->redirect(array('action' => 'index'));

        }
    }

    function admin_userpermissions($user_id = null, $submit = null, $password = null)
    {
        $this->layout = 'popup';


        if (!empty($this->request->data) || $submit == 1) {
            app::import('Model', 'User');
            $this->User = new User();

            $user_id = base64_decode($user_id);
            $this->request->data['User']['id'] = $user_id;
            
            if ($this->request->data['User']['autoresponders'] == '') {
                $this->request->data['User']['autoresponders'] = 0;
                $subaccountpermissions['autoresponders'] = 0;
            } else {
                $this->request->data['User']['autoresponders'] = $this->request->data['User']['autoresponders'];
            }
            if ($this->request->data['User']['importcontacts'] == '') {
                $this->request->data['User']['importcontacts'] = 0;
                $subaccountpermissions['importcontacts'] = 0;

            } else {
                $this->request->data['User']['importcontacts'] = $this->request->data['User']['importcontacts'];
            }
            if ($this->request->data['User']['shortlinks'] == '') {
                $this->request->data['User']['shortlinks'] = 0;
                $subaccountpermissions['shortlinks'] = 0;
            } else {
                $this->request->data['User']['shortlinks'] = $this->request->data['User']['shortlinks'];
            }
            if ($this->request->data['User']['voicebroadcast'] == '') {
                $this->request->data['User']['voicebroadcast'] = 0;
                $subaccountpermissions['voicebroadcast'] = 0;
            } else {
                $this->request->data['User']['voicebroadcast'] = $this->request->data['User']['voicebroadcast'];
            }
            if ($this->request->data['User']['polls'] == '') {
                $this->request->data['User']['polls'] = 0;
                $subaccountpermissions['polls'] = 0;
            } else {
                $this->request->data['User']['polls'] = $this->request->data['User']['polls'];
            }
            if ($this->request->data['User']['contests'] == '') {
                $this->request->data['User']['contests'] = 0;
                $subaccountpermissions['contests'] = 0;
            } else {
                $this->request->data['User']['contests'] = $this->request->data['User']['contests'];
            }
            if ($this->request->data['User']['loyaltyprograms'] == '') {
                $this->request->data['User']['loyaltyprograms'] = 0;
                $subaccountpermissions['loyaltyprograms'] = 0;
            } else {
                $this->request->data['User']['loyaltyprograms'] = $this->request->data['User']['loyaltyprograms'];
            }
            if ($this->request->data['User']['kioskbuilder'] == '') {
                $this->request->data['User']['kioskbuilder'] = 0;
                $subaccountpermissions['kioskbuilder'] = 0;
            } else {
                $this->request->data['User']['kioskbuilder'] = $this->request->data['User']['kioskbuilder'];
            }
            if ($this->request->data['User']['birthdaywishes'] == '') {
                $this->request->data['User']['birthdaywishes'] = 0;
                $subaccountpermissions['birthdaywishes'] = 0;
            } else {
                $this->request->data['User']['birthdaywishes'] = $this->request->data['User']['birthdaywishes'];
            }
            if ($this->request->data['User']['mobilepagebuilder'] == '') {
                $this->request->data['User']['mobilepagebuilder'] = 0;
                $subaccountpermissions['mobilepagebuilder'] = 0;
            } else {
                $this->request->data['User']['mobilepagebuilder'] = $this->request->data['User']['mobilepagebuilder'];
            }
            if ($this->request->data['User']['webwidgets'] == '') {
                $this->request->data['User']['webwidgets'] = 0;
                $subaccountpermissions['webwidgets'] = 0;
            } else {
                $this->request->data['User']['webwidgets'] = $this->request->data['User']['webwidgets'];
            }
            if ($this->request->data['User']['qrcodes'] == '') {
                $this->request->data['User']['qrcodes'] = 0;
                $subaccountpermissions['qrcodes'] = 0;
            } else {
                $this->request->data['User']['qrcodes'] = $this->request->data['User']['qrcodes'];
            }
            if ($this->request->data['User']['smschat'] == '') {
                $this->request->data['User']['smschat'] = 0;
                $subaccountpermissions['smschat'] = 0;
            } else {
                $this->request->data['User']['smschat'] = $this->request->data['User']['smschat'];
            }
            if ($this->request->data['User']['calendarscheduler'] == '') {
                $this->request->data['User']['calendarscheduler'] = 0;
                $subaccountpermissions['calendarscheduler'] = 0;
            } else {
                $this->request->data['User']['calendarscheduler'] = $this->request->data['User']['calendarscheduler'];
            }
            if ($this->request->data['User']['appointments'] == '') {
                $this->request->data['User']['appointments'] = 0;
                $subaccountpermissions['appointments'] = 0;
            } else {
                $this->request->data['User']['appointments'] = $this->request->data['User']['appointments'];
            }
            if ($this->request->data['User']['groups'] == '') {
                $this->request->data['User']['groups'] = 0;
                $subaccountpermissions['groups'] = 0;
            } else {
                $this->request->data['User']['groups'] = $this->request->data['User']['groups'];
            }
            if ($this->request->data['User']['contactlist'] == '') {
                $this->request->data['User']['contactlist'] = 0;
                $subaccountpermissions['contactlist'] = 0;
            } else {
                $this->request->data['User']['contactlist'] = $this->request->data['User']['contactlist'];
            }
            if ($this->request->data['User']['sendsms'] == '') {
                $this->request->data['User']['sendsms'] = 0;
                $subaccountpermissions['sendsms'] = 0;
            } else {
                $this->request->data['User']['sendsms'] = $this->request->data['User']['sendsms'];
            }
            if ($this->request->data['User']['logs'] == '') {
                $this->request->data['User']['logs'] = 0;
                $subaccountpermissions['logs'] = 0;
            } else {
                $this->request->data['User']['logs'] = $this->request->data['User']['logs'];
            }
            if ($this->request->data['User']['reports'] == '') {
                $this->request->data['User']['reports'] = 0;
                $subaccountpermissions['reports'] = 0;
            } else {
                $this->request->data['User']['reports'] = $this->request->data['User']['reports'];
            }
            if ($this->request->data['User']['affiliates'] == '') {
                $this->request->data['User']['affiliates'] = 0;
                $subaccountpermissions['affiliates'] = 0;
            } else {
                $this->request->data['User']['affiliates'] = $this->request->data['User']['affiliates'];
            }
            if ($this->request->data['User']['getnumbers'] == '') {
                $this->request->data['User']['getnumbers'] = 0;
                $subaccountpermissions['getnumbers'] = 0;
            } else {
                $this->request->data['User']['getnumbers'] = $this->request->data['User']['getnumbers'];
            }
            if ($this->request->data['User']['makepurchases'] == '') {
                $this->request->data['User']['makepurchases'] = 0;
                $subaccountpermissions['makepurchases'] = 0;
            } else {
                $this->request->data['User']['makepurchases'] = $this->request->data['User']['makepurchases'];
            }
            
            if ($this->request->data['User']['mobilecoupons'] == '') {
                $this->request->data['User']['mobilecoupons'] = 0;
                $subaccountpermissions['mobilecoupons'] = 0;
            } else {
                $this->request->data['User']['mobilecoupons'] = $this->request->data['User']['mobilecoupons'];
            }
            if ($this->request->data['User']['qabots'] == '') {
                $this->request->data['User']['qabots'] = 0;
                $subaccountpermissions['qabots'] = 0;
            } else {
                $this->request->data['User']['qabots'] = $this->request->data['User']['qabots'];
            }

            $this->User->save($this->request->data);

            app::import('Model', 'Subaccount');
            $this->Subaccount = new Subaccount();
            $subaccounts = $this->Subaccount->find('all', array('conditions' => array('Subaccount.user_id' => $user_id)));

            if (!empty($subaccounts)) {
                foreach ($subaccounts as $subaccount) {
                    $this->Subaccount->id = $subaccount['Subaccount']['id'];
                    $this->Subaccount->save($subaccountpermissions);

                }
            }
            $this->Session->setFlash('User permissions have been saved');
            $this->redirect(array('action' => 'index'));
        } else {
            app::import('Model', 'User');
            $this->User = new User();
            $this->set('id', $user_id);
            $user_id = base64_decode($user_id);
            $password = base64_decode($password);
            $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.password' => $password)));
            if (!empty($user)) {
                $this->set('userpermissions', $user);
            } else {
                $this->Session->setFlash(__('User permissions for this user could not be found. Please, try again.', true));
                $this->redirect(array('action' => 'index'));
            }


        }
    }
    
    
    function admin_usercontacts($user_id = null, $password = null)
    {
        $this->layout = 'popup';
        $user_id = base64_decode($user_id);
        $password = base64_decode($password);
        app::import('Model', 'User');
        $this->User = new User();
        $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.password' => $password)));
        if (!empty($someone)) {

            app::import('Model', 'ContactGroup');
            $this->ContactGroup = new ContactGroup();
            $this->paginate = array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.created' => 'desc'));
            $data = $this->paginate('ContactGroup');
            $this->set('contacts', $data);
            $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $this->set('users', $user);
        } else {
            $this->Session->setFlash(__('Contacts for this user could not be found. Please, try again.', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_subaccounts($user_id = null)
    {
        $this->layout = 'popup';
        $user_id = base64_decode($user_id);
        app::import('Model', 'Subaccount');
        $this->Subaccount = new Subaccount();
        $someone = $this->Subaccount->find('all', array('conditions' => array('Subaccount.user_id' => $user_id)));
        if (!empty($someone)) {
            $this->set('subaccounts', $someone);
        }
    }
    
    function admin_contactsexport($user_id = null)
    {
        $this->autoRender = false;
        $user_id = base64_decode($user_id);
        app::import('Model', 'ContactGroup');
        $this->ContactGroup = new ContactGroup();
        $contacts = $this->ContactGroup->find('all', array('conditions' => array('ContactGroup.user_id' => $user_id), 'order' => array('ContactGroup.created' => 'desc')));

        $filename = "contacts" . date("Y.m.d") . ".csv";
        $csv_file = fopen('php://output', 'w');
        header('Content-type: application/csv');
        //header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $header_row = array("Subscriber Name", "Email", "Birthday", "Phone", "Carrier", "Location", "Phone Country", "Line Type", "Group Name", "Sub", "Source");
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
                $result['Contact']['carrier'],
                $result['Contact']['location'],
                $result['Contact']['phone_country'],
                $result['Contact']['line_type'],
                $result['Group']['group_name'],
                $type,
                $subscribed_by_sms);
            fputcsv($csv_file, $row, ',', '"');
        }
        fclose($csv_file);
    }

    function admin_resend_email($id = null)
    {
        $this->autoRender = false;
        if ($id > 0) {
            function random_generator($digits)
            {
                srand((double)microtime() * 10000000);
                //Array of alphabets
                $input = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q",
                    "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
                $random_generator = "";// Initialize the string to store random numbers
                for ($i = 1; $i < $digits + 1; $i++) { // Loop the number of times of required digits
                    if (rand(1, 2) == 1) {// to decide the digit should be numeric or alphabet
                        // Add one random alphabet
                        $rand_index = array_rand($input);
                        $random_generator .= $input[$rand_index]; // One char is added
                    } else {
                        // Add one numeric digit between 1 and 10
                        $random_generator .= rand(1, 10); // one number is added
                    } // end of if else
                } // end of for loop
                return $random_generator;
            } // end of function
            $random_number = random_generator(4);

// var_dump($random_number);
// exit();
            $data = $this->User->find('first', array('conditions' => array('User.id' => $id)));


            $email = $data['User']['email'];
            $username = $data['User']['username'];
            $this->request->data['User']['id'] = $id;
            $this->request->data['User']['account_activated'] = $random_number;
            if ($this->User->save($this->request->data)) {
                $subject = SITENAME . " Activate Account";
                $url = SITE_URL . "/users/user_activate_account/" . $random_number;
                /*$this->Email->to = $email;
                $this->Email->subject = $subject;
                $this->Email->from = $sitename;
                $this->Email->template = 'account_login_resend';
                $this->Email->sendAs = 'html';
                $this->Email->Controller->set('username', $username);
                $this->Email->Controller->set('url', $url);
                $this->Email->Controller->set('email', $email);
                $this->Email->send();*/
                
                $Email = new CakeEmail();

                if(EMAILSMTP==1){
                    $Email->config('smtp');
                }


                $Email->from(array(SUPPORT_EMAIL => SITENAME));
                $Email->to($email);
                $Email->subject($subject);
                $Email->template('account_login_resend');
                $Email->emailFormat('html');
                $Email->viewVars(array('username' => $username));
                $Email->viewVars(array('email' => $email));
                $Email->viewVars(array('url' => $url));

                // var_dump($Email);
                // exit();
                
                try {
                    $Email->send();
                } catch (\Exception $e) {
                    echo "Email sending error: ". $e->getMessage();
                    exit();
                }


                $this->Session->setFlash(__('Account activation email has been re-sent', true));
            } else {
                $this->Session->setFlash(__('Please try again', true));
            }
            $this->redirect(array('action' => 'index'));
        }
    }

    function redeem($unique_key = null, $code = null)
    {
        $this->layout = null;
        if ($unique_key != '') {
            app::import('Model', 'SmsloyaltyUser');
            $this->SmsloyaltyUser = new SmsloyaltyUser();
            $loyaltyuser = $this->SmsloyaltyUser->find('first', array('conditions' => array('SmsloyaltyUser.unique_key' => $unique_key)));
            $this->set('loyaltyuser', $loyaltyuser);
            if (!empty($loyaltyuser)) {
                if ($loyaltyuser['SmsloyaltyUser']['redemptions'] == 0) {
                    $loyalty_arr['SmsloyaltyUser']['id'] = $loyaltyuser['SmsloyaltyUser']['id'];
                    $loyalty_arr['SmsloyaltyUser']['redemptions'] = 1;
                    $this->SmsloyaltyUser->save($loyalty_arr);

                    //*********** Save to activity timeline
                    app::import('Model', 'ActivityTimeline');
                    $this->ActivityTimeline = new ActivityTimeline();
                    app::import('Model', 'User');
                    $this->User = new User();
                    $someone = $this->User->find('first', array('conditions' => array('User.id' => $loyaltyuser['SmsloyaltyUser']['user_id'])));
                    if (!empty($someone)) {
                        $timezone = $someone['User']['timezone'];
                        date_default_timezone_set($timezone);
                    }
                    $timeline['ActivityTimeline']['user_id'] = $loyaltyuser['SmsloyaltyUser']['user_id'];
                    $timeline['ActivityTimeline']['contact_id'] = $loyaltyuser['SmsloyaltyUser']['contact_id'];
                    $timeline['ActivityTimeline']['activity'] = 8;
                    $timeline['ActivityTimeline']['title'] = 'Loyalty Program Redemption';
                    $timeline['ActivityTimeline']['description'] = 'Contact redeemed their loyalty program reward.';
                    $timeline['ActivityTimeline']['created'] = date('Y-m-d H:i:s');
                    $this->ActivityTimeline->save($timeline);
                    //*************

                    if ($code != '') {
                        app::import('Model', 'Kiosks');
                        $this->Kiosks = new Kiosks();
                        $kiosks = $this->Kiosks->find('first', array('conditions' => array('Kiosks.unique_id' => $code), 'order' => array('Kiosks.id' => 'asc')));
                        $this->set('code', $code);
                        $this->set('kiosks', $kiosks);
                    }
                }
            } else {
                $this->set('notfound', 1);
                $this->Session->setFlash(__('Redeem code not found. Please make sure you are not editing the redeem code.', true));
                //$this->redirect(array('action' => 'login'));
            }
        } else {
            $this->set('empty', 1);
            $this->Session->setFlash(__('Redeem code is empty. Please make sure you are not editing the redeem code.', true));
            //$this->redirect(array('action' => 'login'));

        }
    }

    function cancel_monthly_subscription($closeaccount = 0)
    {
        $this->autoRender = false;
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        if (!empty($users)) {
            \Stripe\Stripe::setApiKey(SECRET_KEY);
            try {

                $subscription = \Stripe\Subscription::retrieve($users['User']['monthly_stripe_subscription_id']);
                if ($closeaccount == 0) {
                    $subscription->cancel();
                    $this->Session->setFlash(__('Your monthly credit subscription has been canceled.', true));
                } else {
                    $subscription->cancel();
                }
            } catch (\Stripe\Error\Card $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                $this->Session->setFlash($e->getMessage());
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage());
            }
        } else {
            $this->Session->setFlash(__('User not found.please try again', true));
        }
        if ($closeaccount == 0) {
            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
        }
    }

    function cancel_monthly_numbers_subscription($closeaccount = 0)
    {
        $this->autoRender = false;
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        if (!empty($users)) {
            \Stripe\Stripe::setApiKey(SECRET_KEY);
            try {
                $subscription = \Stripe\Subscription::retrieve($users['User']['monthly_number_subscription_id']);
                if ($closeaccount == 0) {
                    $subscription->cancel();
                    $this->Session->setFlash(__('Your monthly numbers subscription has been canceled.', true));
                } else {
                    $subscription->cancel();
                }
            } catch (\Stripe\Error\Card $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                $this->Session->setFlash($e->getMessage());
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage());
            }
        } else {
            $this->Session->setFlash(__('User not found.please try again', true));
        }
        if ($closeaccount == 0) {
            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
        }
    }

    function upgrade_monthly_numbers_subscription($package_id = null)
    {
        $this->autoRender = false;
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        if (!empty($users)) {
            $this->loadModel('MonthlyNumberPackage');
            $monthlypackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $package_id)));
            $oldmonthlypackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $users['User']['number_package'])));
            \Stripe\Stripe::setApiKey(SECRET_KEY);
            try {
                if ($package_id == $users['User']['number_package']) {
                    $this->Session->setFlash(__('You are currently subscribed to this plan. Please choose a different plan you want to upgrade/downgrade.', true));
                } else {
                    $subscription = \Stripe\Subscription::retrieve($users['User']['monthly_number_subscription_id']);
                    $subscription->plan = $monthlypackage['MonthlyNumberPackage']['plan'];
                    $subscription->save();
                    if (isset($subscription->id)) {
                        $user_arr['User']['id'] = $this->Session->read('User.id');
                        $user_arr['User']['monthly_number_subscription_id'] = $subscription->id;
                        $user_arr['User']['number_package'] = $monthlypackage['MonthlyNumberPackage']['id'];
                        $user_arr['User']['number_limit'] = $users['User']['number_limit'] + $monthlypackage['MonthlyNumberPackage']['total_secondary_numbers'] - $oldmonthlypackage['MonthlyNumberPackage']['total_secondary_numbers'];

                        $this->User->save($user_arr);
                    }
                    $this->Session->setFlash(__('Your monthly numbers plan was successfully updated. You will be charged a prorated amount on your next invoice.', true));
                }
            } catch (\Stripe\Error\Card $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                $this->Session->setFlash($e->getMessage());
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage());
            }
        } else {
            $this->Session->setFlash(__('User not found.please try again', true));
        }
        $this->redirect(array('controller' => 'users', 'action' => 'profile'));
    }

    function upgrade_monthly_subscription($package_id = null)
    {
        $this->autoRender = false;
        $users = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        if (!empty($users)) {
            $this->loadModel('MonthlyPackage');
            $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $package_id)));
            $oldmonthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $users['User']['package'])));
            \Stripe\Stripe::setApiKey(SECRET_KEY);
            try {
                if ($package_id == $users['User']['package']) {
                    $this->Session->setFlash(__('You are currently subscribed to this plan. Please choose a different plan you want to upgrade/downgrade.', true));
                } else {
                    $subscription = \Stripe\Subscription::retrieve($users['User']['monthly_stripe_subscription_id']);
                    $subscription->plan = $monthlypackage['MonthlyPackage']['product_id'];
                    $subscription->save();
                    if (isset($subscription->id)) {
                        $user_arr['User']['id'] = $this->Session->read('User.id');
                        $user_arr['User']['monthly_stripe_subscription_id'] = $subscription->id;
                        $user_arr['User']['sms_balance'] = $users['User']['sms_balance'] + $monthlypackage['MonthlyPackage']['text_messages_credit'] - $oldmonthlypackage['MonthlyPackage']['text_messages_credit'];
                        $user_arr['User']['voice_balance'] = $users['User']['voice_balance'] + $monthlypackage['MonthlyPackage']['voice_messages_credit'] - $oldmonthlypackage['MonthlyPackage']['voice_messages_credit'];
                        $user_arr['User']['sms_credit_balance_email_alerts'] = 0;
                        $user_arr['User']['VM_credit_balance_email_alerts'] = 0;
                        $user_arr['User']['package'] = $monthlypackage['MonthlyPackage']['id'];
                        //$nextdate= date("Y-m-d",mktime(0, 0, 0, date("m")+1 , date("d"), date("Y")));
                        //$user_arr['User']['next_renewal_dates'] =$nextdate;
                        $this->User->save($user_arr);
                    }
                    $this->Session->setFlash(__('Your monthly plan was successfully updated. You will be charged a prorated amount on your next invoice.', true));
                }
            } catch (\Stripe\Error\Card $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
                $this->Session->setFlash($e->getMessage());
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                $this->Session->setFlash($e->getMessage());
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage());
            }
        } else {
            $this->Session->setFlash(__('User not found.please try again', true));
        }
        $this->redirect(array('controller' => 'users', 'action' => 'profile'));
    }

    function getnotification()
    {
        sleep(1);
        $this->autoRender = false;
        $out = @file_get_contents('php://input');
        $event_json = json_decode('[' . $out . ']');
        $jsonObject = $event_json[0];
        //echo $evt_id = $jsonObject->id;
        $type = trim($jsonObject->type);
        if(isset($jsonObject->data->object->subscription)){
			$subscription_id = $jsonObject->data->object->subscription;
		}else{
			$subscription_id = $jsonObject->data->object->id;
		}
        $customer_id = $jsonObject->data->object->customer;
        if (($type == 'customer.subscription.deleted') && ($customer_id != '')) {
            $monthlysubscription = $this->User->find('first', array('conditions' => array('User.stripe_customer_id' => $customer_id, 'User.monthly_stripe_subscription_id' => $subscription_id)));
            $monthly_number_subscription = $this->User->find('first', array('conditions' => array('User.stripe_customer_id' => $customer_id, 'User.monthly_number_subscription_id' => $subscription_id)));
            if (!empty($monthlysubscription)) {
                $this->loadModel('MonthlyPackage');
                $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $monthlysubscription['User']['package'])));
                $user_arr['User']['id'] = $monthlysubscription['User']['id'];
                //$user_arr['User']['active'] = 0;
                $user_arr['User']['package'] = 0;
                $user_arr['User']['next_renewal_dates'] = '';
                $user_arr['User']['monthly_stripe_subscription_id'] = '';
                $user_arr['User']['sms_credit_balance_email_alerts'] = 0;
                $user_arr['User']['VM_credit_balance_email_alerts'] = 0;
                
                //***Go back to default user permissions when they cancel their subscription
                $user_arr['User']['autoresponders'] = AUTORESPONDERS;
                $user_arr['User']['importcontacts'] = IMPORTCONTACTS;
                $user_arr['User']['shortlinks'] = SHORTLINKS;
                $user_arr['User']['voicebroadcast'] = VOICEBROADCAST;
                $user_arr['User']['polls'] = POLLS;
                $user_arr['User']['contests'] = CONTESTS;
                $user_arr['User']['loyaltyprograms'] = LOYALTYPROGRAMS;
                $user_arr['User']['kioskbuilder'] = KIOSKBUILDER;
                $user_arr['User']['birthdaywishes'] = BIRTHDAYWISHES;
                $user_arr['User']['mobilepagebuilder'] = MOBILEPAGEBUILDER;
                $user_arr['User']['webwidgets'] = WEBWIDGETS;
                $user_arr['User']['smschat'] = SMSCHAT;
                $user_arr['User']['qrcodes'] = QRCODES;
                $user_arr['User']['calendarscheduler'] = CALENDARSCHEDULER;
                $user_arr['User']['appointments'] = APPOINTMENTS;
                $user_arr['User']['groups'] = GROUPS;
                $user_arr['User']['contactlist'] = CONTACTLIST;
                $user_arr['User']['sendsms'] = SENDSMS;
                $user_arr['User']['affiliates'] = AFFILIATES;
                $user_arr['User']['getnumbers'] = GETNUMBERS;
                $user_arr['User']['mobilecoupons'] = MOBILECOUPONS;
                $user_arr['User']['qabots'] = QABOTS;
            
                if ($this->User->save($user_arr)) {
                    $sitename = str_replace(' ', '', SITENAME);
                    $subject = "Your Monthly Credit Subscription with " . SITENAME . " has been canceled";
                    /*$this->Email->subject = $subject;
                    $this->Email->from = $sitename;
                    $this->Email->to = $monthlysubscription['User']['email'];
                    $this->Email->template = 'stripe_subscription_cancel';
                    $this->Email->sendAs = 'html';
                    $this->set('data', $monthlysubscription);
                    $this->Email->send();*/
                    
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){                                                  
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($monthlysubscription['User']['email']);
                    $Email->subject($subject);
                    $Email->template('stripe_subscription_cancel');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('data' => $monthlysubscription));
                    $Email->send();
                }
            } else if (!empty($monthly_number_subscription)) {
                $this->loadModel('MonthlyNumberPackage');
                $monthlynumberpackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $monthly_number_subscription['User']['number_package'])));
                $user_arr['User']['id'] = $monthly_number_subscription['User']['id'];
                $user_arr['User']['number_limit'] = $monthly_number_subscription['User']['number_limit'] - $monthlynumberpackage['MonthlyNumberPackage']['total_secondary_numbers'];
                $user_arr['User']['number_package'] = 0;
                $user_arr['User']['number_next_renewal_dates'] = '';
                $user_arr['User']['monthly_number_subscription_id'] = '';
                $user_arr['User']['number_limit_set'] = 0;
                //$user_arr['User']['active'] = 0;
                if ($this->User->save($user_arr)) {
                    $sitename = str_replace(' ', '', SITENAME);
                    $subject = "Your Monthly Numbers Subscription with " . SITENAME . " has been canceled";
                    /*$this->Email->subject = $subject;
                    $this->Email->from = $sitename;
                    $this->Email->to = $monthly_number_subscription['User']['email'];
                    $this->Email->template = 'stripe_number_subscription_cancel';
                    $this->Email->sendAs = 'html';
                    $this->set('data', $monthly_number_subscription);
                    $this->Email->send();*/
                    
                    $Email = new CakeEmail();
                    if(EMAILSMTP==1){                                                  
                        $Email->config('smtp');
                    }
                    $Email->from(array(SUPPORT_EMAIL => SITENAME));
                    $Email->to($monthly_number_subscription['User']['email']);
                    $Email->subject($subject);
                    $Email->template('stripe_number_subscription_cancel');
                    $Email->emailFormat('html');
                    $Email->viewVars(array('data' => $monthly_number_subscription));
                    $Email->send();
                }
            }
        } else if (($type == 'invoice.payment_succeeded') && ($customer_id != '')) {
            if(empty($subscription_id)){
                $subscription_id = $jsonObject->data->object->lines->data[0]->id;
                if(substr($subscription_id,0,4)=="sli_"){
                    $subscription_id = $jsonObject->data->object->subscription;
                }
            }
            
            if(isset($jsonObject->data->object->lines->data[0]->plan->amount)){
                $amount = $jsonObject->data->object->lines->data[0]->plan->amount / 100;
            }else{
                $amount = $jsonObject->data->object->amount_paid / 100;
            }
            
            //$amount = $jsonObject->data->object->lines->data[0]->plan->amount / 100;
            $monthlysubscription = $this->User->find('first', array('conditions' => array('User.stripe_customer_id' => $customer_id, 'User.monthly_stripe_subscription_id' => $subscription_id)));
            $monthly_number_subscription = $this->User->find('first', array('conditions' => array('User.stripe_customer_id' => $customer_id, 'User.monthly_number_subscription_id' => $subscription_id)));
            if (!empty($monthlysubscription)) {
                $this->loadModel('MonthlyPackage');
                $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $monthlysubscription['User']['package'])));
                $user_arr['User']['id'] = $monthlysubscription['User']['id'];
                $user_arr['User']['active'] = 1;
                $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
                $user_arr['User']['next_renewal_dates'] = $nextdate;

                if($monthlypackage['MonthlyPackage']['credits_carryover'] == 1){
                    $user_arr['User']['sms_balance'] = $monthlysubscription['User']['sms_balance'] + $monthlypackage['MonthlyPackage']['text_messages_credit'];
                    $user_arr['User']['voice_balance'] = $monthlysubscription['User']['voice_balance'] + $monthlypackage['MonthlyPackage']['voice_messages_credit'];
                }else{
                    $user_arr['User']['sms_balance'] = $monthlypackage['MonthlyPackage']['text_messages_credit'];
                    $user_arr['User']['voice_balance'] = $monthlypackage['MonthlyPackage']['voice_messages_credit']; 
                }
                $user_arr['User']['sms_credit_balance_email_alerts'] = 0;
                $user_arr['User']['VM_credit_balance_email_alerts'] = 0;
                
                //***Set user permissions based on credit package
                $user_arr['User']['autoresponders'] = $monthlypackage['MonthlyPackage']['autoresponders'];
                $user_arr['User']['importcontacts'] = $monthlypackage['MonthlyPackage']['importcontacts'];
                $user_arr['User']['shortlinks'] = $monthlypackage['MonthlyPackage']['shortlinks'];
                $user_arr['User']['voicebroadcast'] = $monthlypackage['MonthlyPackage']['voicebroadcast'];
                $user_arr['User']['polls'] = $monthlypackage['MonthlyPackage']['polls'];
                $user_arr['User']['contests'] = $monthlypackage['MonthlyPackage']['contests'];
                $user_arr['User']['loyaltyprograms'] = $monthlypackage['MonthlyPackage']['loyaltyprograms'];
                $user_arr['User']['kioskbuilder'] = $monthlypackage['MonthlyPackage']['kioskbuilder'];
                $user_arr['User']['birthdaywishes'] = $monthlypackage['MonthlyPackage']['birthdaywishes'];
                $user_arr['User']['mobilepagebuilder'] = $monthlypackage['MonthlyPackage']['mobilepagebuilder'];
                $user_arr['User']['webwidgets'] = $monthlypackage['MonthlyPackage']['webwidgets'];
                $user_arr['User']['smschat'] = $monthlypackage['MonthlyPackage']['smschat'];
                $user_arr['User']['qrcodes'] = $monthlypackage['MonthlyPackage']['qrcodes'];
                $user_arr['User']['calendarscheduler'] = $monthlypackage['MonthlyPackage']['calendarscheduler'];
                $user_arr['User']['appointments'] = $monthlypackage['MonthlyPackage']['appointments'];
                $user_arr['User']['groups'] = $monthlypackage['MonthlyPackage']['groups'];
                $user_arr['User']['contactlist'] = $monthlypackage['MonthlyPackage']['contactlist'];
                $user_arr['User']['sendsms'] = $monthlypackage['MonthlyPackage']['sendsms'];
                $user_arr['User']['affiliates'] = $monthlypackage['MonthlyPackage']['affiliates'];
                $user_arr['User']['getnumbers'] = $monthlypackage['MonthlyPackage']['getnumbers'];
                $user_arr['User']['mobilecoupons'] = $monthlypackage['MonthlyPackage']['mobilecoupons'];
                $user_arr['User']['qabots'] = $monthlypackage['MonthlyPackage']['qabots'];

                if ($this->User->save($user_arr)) {
                    app::import('Model', 'Invoice');
                    $this->Invoice = new Invoice();
                    $invoice['id'] = '';
                    $invoice['user_id'] = $monthlysubscription['User']['id'];
                    $invoice['txnid'] = $subscription_id;
                    $invoice['type'] = 2;
                    $invoice['package_name'] = $monthlypackage['MonthlyPackage']['package_name'];
                    //$invoice['amount']=$monthlypackage['MonthlyPackage']['amount'];
                    $invoice['amount'] = $amount;
                    $invoice['created'] = date("Y-m-d");
                    $this->Invoice->save($invoice);

                    app::import('Model', 'Referral');
                    $this->Referral = new Referral();

                    if ($monthlysubscription['User']['active'] == 1) {
                        $referraldetails = $this->Referral->find('all', array('conditions' => array('Referral.referred_by' => $monthlysubscription['User']['id'])));
                        $percentage = $monthlypackage['MonthlyPackage']['amount'] * RECURRING_REFERRAL_PERCENT / 100;

                        if (!empty($referraldetails)) {
                            foreach ($referraldetails as $referraldetail) {
                                $referral['id'] = $referraldetail['Referral']['id'];
                                $referral['amount'] = $percentage;
                                $referral['paid_status'] = 0;
                                $this->Referral->save($referral);
                            }
                        }
                    }
                }
            } else if (!empty($monthly_number_subscription)) {
                $this->loadModel('MonthlyNumberPackage');
                $monthlynumberpackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $monthly_number_subscription['User']['number_package'])));
                $user_arr['User']['id'] = $monthly_number_subscription['User']['id'];
                $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
                $user_arr['User']['number_next_renewal_dates'] = $nextdate;
                if ($this->User->save($user_arr)) {
                    app::import('Model', 'Invoice');
                    $this->Invoice = new Invoice();
                    $invoice['id'] = '';
                    $invoice['user_id'] = $monthly_number_subscription['User']['id'];
                    $invoice['txnid'] = $subscription_id;
                    $invoice['type'] = 2;
                    $invoice['package_name'] = $monthlynumberpackage['MonthlyNumberPackage']['package_name'];
                    //$invoice['amount']=$monthlynumberpackage['MonthlyNumberPackage']['amount'];
                    $invoice['amount'] = $amount;
                    $invoice['created'] = date("Y-m-d");
                    $this->Invoice->save($invoice);
                }
            }
        } else if (($type == 'invoice.payment_failed') && ($customer_id != '')) {
        
            /*$subscription_id = $jsonObject->data->object->lines->data[0]->id;
            
            if(substr($subscription_id,0,4)=="sli_"){
                $subscription_id = $jsonObject->data->object->subscription;
            }*/
            
            if(empty($subscription_id)){
                $subscription_id = $jsonObject->data->object->lines->data[0]->id;
                if(substr($subscription_id,0,4)=="sli_"){
                    $subscription_id = $jsonObject->data->object->subscription;
                }
            }
            
            $monthlysubscription = $this->User->find('first', array('conditions' => array('User.stripe_customer_id' => $customer_id, 'User.monthly_stripe_subscription_id' => $subscription_id)));
            $monthly_number_subscription = $this->User->find('first', array('conditions' => array('User.stripe_customer_id' => $customer_id, 'User.monthly_number_subscription_id' => $subscription_id)));

            if (!empty($monthlysubscription)) {
                $this->loadModel('MonthlyPackage');
                $to = $monthlysubscription['User']['email'];
                $first_name = $monthlysubscription['User']['first_name'];
                $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $monthlysubscription['User']['package'])));
                $packagename = $monthlypackage['MonthlyPackage']['package_name'];
                
                //$this->User->id = $monthlysubscription['User']['id'];
                //$this->User->saveField('package', 0);

            } else if (!empty($monthly_number_subscription)) {
                $this->loadModel('MonthlyNumberPackage');
                $to = $monthly_number_subscription['User']['email'];
                $first_name = $monthly_number_subscription['User']['first_name'];
                $monthlynumberpackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $monthly_number_subscription['User']['number_package'])));
                $packagename = $monthlynumberpackage['MonthlyNumberPackage']['package_name'];
                
                //$this->User->id = $monthly_number_subscription['User']['id'];
                //$this->User->saveField('number_package', 0);
            }

            $sitename = str_replace(' ', '', SITENAME);
            $subject = "Your monthly credit card payment with " . SITENAME . " has failed";
            /*$this->Email->subject = $subject;
            $this->Email->from = $sitename;
            $this->Email->to = $to;
            $this->Email->template = 'stripe_status_failed';
            $this->Email->sendAs = 'html';
            $this->Email->Controller->set('firstname', $first_name);
            $this->Email->Controller->set('packagename', $packagename);
            $this->Email->send();*/
            
            $Email = new CakeEmail();
            if(EMAILSMTP==1){                                                  
                $Email->config('smtp');
            }
            $Email->from(array(SUPPORT_EMAIL => SITENAME));
            $Email->to($to);
            $Email->subject($subject);
            $Email->template('stripe_status_failed');
            $Email->emailFormat('html');
            $Email->viewVars(array('firstname' => $first_name));
            $Email->viewVars(array('packagename' => $packagename));
            $Email->send();
        }
        
        /*ob_start();
        print_r('<pre>');
        print_r($jsonObject);
        print_r("----------");
        print_r($subscription_id);
        print_r("----------");
        print_r($amount);
        print_r('</pre>');
        $out1 = ob_get_contents();
        ob_end_clean();
        $file = fopen("debug/getnotification" . time() . ".txt", "w");
        fwrite($file, $out1);
        fclose($file);*/
    }

    function api()
    {
        $this->layout = 'admin_new_layout';
        $userDetails = $this->getLoggedUserDetails();
        if ($userDetails['User']['apikey'] == '') {
            $this->User->id = $this->Session->read('User.id');
            $apikey = 'INS' . rand(82342, 23423456) . $this->User->id;
            if ($this->User->id != '') {
                $this->User->saveField('apikey', $apikey);
            }
        } else {
            $apikey = $userDetails['User']['apikey'];
        }
        $this->set('apiKey', $apikey);
    }
    
    function regenerate()
    {
        $this->autoRender = false;
        $this->User->id = $this->Session->read('User.id');
        $apikey = 'INS' . rand(82342, 23423456) . $this->User->id;
        if ($this->User->id != '') {
            $this->User->saveField('apikey', $apikey);
        }
        $this->set('apiKey', $apikey);
        $this->Session->setFlash(__('API Key has been re-generated.', true));
        $this->redirect(array('controller' => 'users', 'action' => 'api'));
    }

    function closeaccount()
    {
        $this->autoRender = false;
        $user_id = $this->Session->read('User.id');

        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id), 'order' => array('UserNumber.id' => 'desc')));

        if (!empty($numbers)) {
            foreach ($numbers as $number) {
                $API_TYPE = $number['UserNumber']['api_type'];
                $error = '';
                if ($API_TYPE == 0) {
                    if ($number['UserNumber']['phone_sid'] != '') {
                        $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                        $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        $this->Twilio->releasenumber($number['UserNumber']['phone_sid']);
                    }
                } else if ($API_TYPE == 3) {
                    $this->Plivo->AuthId = PLIVO_KEY;
                    $this->Plivo->AuthToken = PLIVO_TOKEN;
                    $this->Plivo->delete_phone_number($number['UserNumber']['number']);
                } else if ($API_TYPE == 4) {
                    $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$number['UserNumber']['number']);
                    $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);  
                } else if ($API_TYPE == 5) {
                    $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$number['UserNumber']['phone_sid']); 
                    
                    if(trim($response->code) == '22121'){
                        $error = $response->message;
                    }
                } else if ($API_TYPE == 6) {
                    $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$number['UserNumber']['number']);   
                } else if ($API_TYPE == 7) {
                    $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$number['UserNumber']['phone_sid']); 
                } else {
                    $api_key = NEXMO_KEY;
                    $api_secret = NEXMO_SECRET;
                    $this->Nexmo->releasenumber($number['UserNumber']['country_code'], $number['UserNumber']['number'], $api_key, $api_secret);
                }
                
                if(trim($error)==''){
                    $this->UserNumber->delete($number['UserNumber']['id']);
                    $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $number['UserNumber']['number']));
                }
            }
        }

        $user_details = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $API_TYPE = $user_details['User']['api_type'];
        $error = '';
        
        if (!empty($user_details)) {
            if ($API_TYPE == 0) {
                if ($user_details['User']['phone_sid'] != '') {
                    $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                    $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                    $this->Twilio->releasenumber($user_details['User']['phone_sid']);
                }
            } else if ($API_TYPE == 3) {
                $this->Plivo->AuthId = PLIVO_KEY;
                $this->Plivo->AuthToken = PLIVO_TOKEN;
                $this->Plivo->delete_phone_number($user_details['User']['assigned_number']);
            } else if ($API_TYPE == 4) {
                $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$user_details['User']['assigned_number']);
                $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);  
            } else if ($API_TYPE == 5) {
                $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$user_details['User']['phone_sid']);
                
                if(trim($response->code) == '22121'){
                    $error = $response->message;
                }
            } else if ($API_TYPE == 6) {
                $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$user_details['User']['assigned_number']);   
            } else if ($API_TYPE == 7) {
                $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$user_details['User']['phone_sid']);
            } else {
                $api_key = NEXMO_KEY;
                $api_secret = NEXMO_SECRET;
                $this->Nexmo->releasenumber($user_details['User']['country_code'], $user_details['User']['assigned_number'], $api_key, $api_secret);
            }
            $this->request->data['User']['id'] = $user_id;
            if(trim($error)==''){
                $this->request->data['User']['assigned_number'] = 0;
                $this->request->data['User']['fax_number'] = 0;
                $this->request->data['User']['number_limit_count'] = 0;
            }
            $this->request->data['User']['active'] = 0;
            $this->request->data['User']['sms_balance'] = 0;
            $this->request->data['User']['voice_balance'] = 0;
            $this->request->data['User']['sms_credit_balance_email_alerts'] = 0;
            $this->request->data['User']['VM_credit_balance_email_alerts'] = 0;
            $this->request->data['User']['getnumbers'] = 0;
            $this->request->data['User']['autoresponders'] = 0;
            $this->request->data['User']['importcontacts'] = 0;
            $this->request->data['User']['shortlinks'] = 0;
            $this->request->data['User']['voicebroadcast'] = 0;
            $this->request->data['User']['polls'] = 0;
            $this->request->data['User']['contests'] = 0;
            $this->request->data['User']['loyaltyprograms'] = 0;
            $this->request->data['User']['kioskbuilder'] = 0;
            $this->request->data['User']['birthdaywishes'] = 0;
            $this->request->data['User']['mobilepagebuilder'] = 0;
            $this->request->data['User']['webwidgets'] = 0;
            $this->request->data['User']['smschat'] = 0;
            $this->request->data['User']['qrcodes'] = 0;
            $this->request->data['User']['calendarscheduler'] = 0;
            $this->request->data['User']['appointments'] = 0;
            $this->request->data['User']['groups'] = 0;
            $this->request->data['User']['contactlist'] = 0;
            $this->request->data['User']['sendsms'] = 0;
            $this->request->data['User']['logs'] = 0;
            $this->request->data['User']['reports'] = 0;
            $this->request->data['User']['affiliates'] = 0;
            $this->request->data['User']['makepurchases'] = 0;
            $this->request->data['User']['mobilecoupons'] = 0;
            $this->request->data['User']['qabots'] = 0;
                 
            $this->User->save($this->request->data);
            if(trim($error)==''){
                $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $user_details['User']['assigned_number']));
            }
            
            /**** remove all sub-account permissions for the user *****/
            $userperm['getnumbers'] = 0;
            $userperm['autoresponders'] = 0;
            $userperm['importcontacts'] = 0;
            $userperm['shortlinks'] = 0;
            $userperm['voicebroadcast'] = 0;
            $userperm['polls'] = 0;
            $userperm['contests'] = 0;
            $userperm['loyaltyprograms'] = 0;
            $userperm['kioskbuilder'] = 0;
            $userperm['birthdaywishes'] = 0;
            $userperm['mobilepagebuilder'] = 0;
            $userperm['webwidgets'] = 0;
            $userperm['smschat'] = 0;
            $userperm['qrcodes'] = 0;
            $userperm['calendarscheduler'] = 0;
            $userperm['appointments'] = 0;
            $userperm['groups'] = 0;
            $userperm['contactlist'] = 0;
            $userperm['sendsms'] = 0;
            $userperm['logs'] = 0;
            $userperm['reports'] = 0;
            $userperm['affiliates'] = 0;
            $userperm['makepurchases'] = 0;
            $userperm['mobilecoupons'] = 0;
            $userperm['qabots'] = 0;

            app::import('Model', 'Subaccount');
            $this->Subaccount = new Subaccount();
            $subaccounts = $this->Subaccount->find('all', array('conditions' => array('Subaccount.user_id' => $user_id)));

            if (!empty($subaccounts)) {
                foreach ($subaccounts as $subaccount) {
                    $this->Subaccount->id = $subaccount['Subaccount']['id'];
                    $this->Subaccount->save($userperm);
                }
            }
            /********/
       
            if ($user_details['User']['monthly_stripe_subscription_id'] != '') {
                $this->cancel_monthly_subscription(1);
            }

            if ($user_details['User']['monthly_number_subscription_id'] != '') {
                $this->cancel_monthly_numbers_subscription(1);
            }

            if ($user_details['User']['recurring_paypal_email'] != '') {
                $this->Session->setFlash(__('Account has been closed and numbers have been released. Your PayPal subscription still needs to be cancelled from within your PayPal account.', true));
            } else {
                $this->Session->setFlash(__('Account has been closed and numbers have been released.', true));
            }

            $this->redirect(array('controller' => 'users', 'action' => 'edit'));
        }

    }
    
    function admin_number_allrelease($user_id = null,$deletefromgateway=1)
    {
        $this->autoRender = false;
        //$user_id = base64_decode($id);

        $user_details = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $SID = trim($user_details['User']['sid']);
        $AUTHTOKEN = trim($user_details['User']['authtoken']);
        $BANDWIDTHUSERID = trim($user_details['User']['bandwidthuserid']);
        $SIGNALWIRESPACE = trim($user_details['User']['signalwirespace']);

        app::import('Model', 'Contact');
        $this->Contact = new Contact();
        app::import('Model', 'UserNumber');
        $this->UserNumber = new UserNumber();
        $numbers = $this->UserNumber->find('all', array('conditions' => array('UserNumber.user_id' => $user_id), 'order' => array('UserNumber.id' => 'desc')));
        $released = true;

        if (!empty($numbers)) {
            foreach ($numbers as $number) {
                $API_TYPE = $number['UserNumber']['api_type'];
                $error='';
                if($deletefromgateway==1){
                    if ($API_TYPE == 0) {
                        if ($number['UserNumber']['phone_sid'] != '') {
                            if ($SID == ''){
                                $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                                $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                            }else{
                                $this->Twilio->AccountSid = $SID;
                                $this->Twilio->AuthToken = $AUTHTOKEN;
                            }
                            $this->Twilio->releasenumber($number['UserNumber']['phone_sid']);
                        }
                    } else if ($API_TYPE == 3) {
                        if ($SID == ''){
                            $this->Plivo->AuthId = PLIVO_KEY;
                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                        }else{
                            $this->Plivo->AuthId = $SID;
                            $this->Plivo->AuthToken = $AUTHTOKEN;
                        }
                        $this->Plivo->delete_phone_number($number['UserNumber']['number']);
                    } else if ($API_TYPE == 4) {
                        if ($SID == ''){
                            $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$number['UserNumber']['number']);
                            $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);   
                        }else{
                            $response = $this->Bandwidth->getnumberdetails($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$number['UserNumber']['number']);
                            $this->Bandwidth->delete_phone_number($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$response->id);   
                        }  
                    } else if ($API_TYPE == 5) {
                        if ($SID == ''){
                            $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$number['UserNumber']['phone_sid']);   
                        }else{
                            $response = $this->Signalwire->delete_phone_number($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$number['UserNumber']['phone_sid']);   
                        }
                        
                        if(trim($response->code) == '22121'){
                            $error = $response->message;
                            $released = false;
                        }
                    } else if ($API_TYPE == 6) {
                        if ($SID == ''){
                            $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$number['UserNumber']['number']);   
                        }else{
                            $response = $this->Ytel->delete_phone_number($SID,$AUTHTOKEN,$number['UserNumber']['number']);   
                        }   
                    } else if ($API_TYPE == 7) {
                        if ($SID == ''){
                            $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$number['UserNumber']['phone_sid']);   
                        }else{
                            $response = $this->Telnyx->delete_phone_number($SID,$number['UserNumber']['phone_sid']);   
                        }  
                    
                    } else {
                        if ($SID == ''){
                            $api_key = NEXMO_KEY;
                            $api_secret = NEXMO_SECRET;
                        }else{
                            $api_key = $SID;
                            $api_secret = $AUTHTOKEN;
                        }
                        $this->Nexmo->releasenumber($number['UserNumber']['country_code'], $number['UserNumber']['number'], $api_key, $api_secret);
                    }
                }
                    
                if(trim($error)==''){
                    $this->UserNumber->delete($number['UserNumber']['id']);
                    $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $number['UserNumber']['number']));
                }
                
            }
        }

        $API_TYPE = $user_details['User']['api_type'];
        $error='';

        if (!empty($user_details)) {
            if($deletefromgateway==1){
                if ($API_TYPE == 0) {
                    if ($user_details['User']['phone_sid'] != '') {
                        if ($SID == ''){
                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Twilio->AccountSid = $SID;
                            $this->Twilio->AuthToken = $AUTHTOKEN;
                        }
                        $this->Twilio->releasenumber($user_details['User']['phone_sid']);
                    }
                } else if ($API_TYPE == 3) {
                    if ($SID == ''){
                        $this->Plivo->AuthId = PLIVO_KEY;
                        $this->Plivo->AuthToken = PLIVO_TOKEN;
                    }else{
                        $this->Plivo->AuthId = $SID;
                        $this->Plivo->AuthToken = $AUTHTOKEN;
                    }
                    $this->Plivo->delete_phone_number($user_details['User']['assigned_number']);
                } else if ($API_TYPE == 4) {
                    if ($SID == ''){
                        $response = $this->Bandwidth->getnumberdetails(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$user_details['User']['assigned_number']);
                        $this->Bandwidth->delete_phone_number(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$response->id);   
                    }else{
                        $response = $this->Bandwidth->getnumberdetails($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$user_details['User']['assigned_number']);
                        $this->Bandwidth->delete_phone_number($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$response->id);   
                    }
                } else if ($API_TYPE == 5) {
                    if ($SID == ''){
                        $response = $this->Signalwire->delete_phone_number(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE,$user_details['User']['phone_sid']);   
                    }else{
                        $response = $this->Signalwire->delete_phone_number($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$user_details['User']['phone_sid']);   
                    }
                    
                    if(trim($response->code) == '22121'){
                        $error = $response->message;
                        $released = false;
                    }
                } else if ($API_TYPE == 6) {
                        if ($SID == ''){
                            $response = $this->Ytel->delete_phone_number(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN,$user_details['User']['assigned_number']);   
                        }else{
                            $response = $this->Ytel->delete_phone_number($SID,$AUTHTOKEN,$user_details['User']['assigned_number']);   
                        }
                } else if ($API_TYPE == 7) {
                        if ($SID == ''){
                            $response = $this->Telnyx->delete_phone_number(TELNYX_KEY,$user_details['User']['phone_sid']);   
                        }else{
                            $response = $this->Telnyx->delete_phone_number($SID,$user_details['User']['phone_sid']);   
                        }   
                } else {
                    if ($SID == ''){
                        $api_key = NEXMO_KEY;
                        $api_secret = NEXMO_SECRET;
                    }else{
                        $api_key = $SID;
                        $api_secret = $AUTHTOKEN;
                    }
                    $this->Nexmo->releasenumber($user_details['User']['country_code'], $user_details['User']['assigned_number'], $api_key, $api_secret);
                }
            }
           
            if(trim($error)==''){
                $this->Contact->updateAll(array('Contact.stickysender' => 0), array('Contact.stickysender' => $user_details['User']['assigned_number']));
                
                $this->request->data['User']['id'] = $user_id;
                $this->request->data['User']['number_limit_count'] = 0;
                $this->request->data['User']['assigned_number'] = 0;
                $this->request->data['User']['fax_number'] = 0;
                $this->User->save($this->request->data);
            }
        }
        if(!$released){
            $this->Session->setFlash(__('Some numbers have not been released due to trying to release them within 24 hours after purchasing them.', true));
        }else{
           $this->Session->setFlash(__('All numbers have been released', true));
        }
        
        $this->redirect(array('action' => 'index'));

    }
    
    function admin_addnumber($user_id)
    {
        $this->layout = 'popup';
        $this->set('id', $user_id);
        
        app::import('Model', 'User');
        $this->User = new User();
        $someone = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        $API_TYPE = $someone['User']['api_type'];
        $SID = trim($someone['User']['sid']);
        $AUTHTOKEN = trim($someone['User']['authtoken']);
        $BANDWIDTHUSERID = trim($someone['User']['bandwidthuserid']);
        $SIGNALWIRESPACE = trim($someone['User']['signalwirespace']);
        $appid = trim($someone['User']['app_id']);
        $callappid = trim($someone['User']['call_app_id']);
        $billinggroupid = trim($someone['User']['billinggroupid']);
        $username = trim($someone['User']['username']);
        $phone_number = str_replace('+','',$this->request->data['User']['number']);

        if (!empty($this->request->data)) {
            
            $numberexists = $this->User->find('first', array('conditions' => array('assigned_number' => $phone_number)));
            if (empty($numberexists)) {
                app::import('Model', 'UserNumber');
                $this->UserNumber = new UserNumber();
                $numberexists = $this->UserNumber->find('first', array('conditions' => array('UserNumber.number' => $phone_number)));
            }
            
            if(empty($numberexists)) {
                if ($someone['User']['assigned_number'] == 0) {
                    $this->User->id = $user_id;
                    $this->User->saveField('assigned_number', $phone_number);
                    $this->User->saveField('number_limit_count', 1);
                    if($API_TYPE==0){
                        if ($SID == ''){
                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Twilio->AccountSid = $SID;
                            $this->Twilio->AuthToken = $AUTHTOKEN;
                        }
                        $this->User->saveField('phone_sid', $this->request->data['User']['phonesid']);
                        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                        $this->User->saveField('mms', $this->request->data['User']['mms']);
                        $this->User->saveField('fax', $this->request->data['User']['fax']);
                        $this->Twilio->updatenumberurls($this->request->data['User']['phonesid']);
                    }else if($API_TYPE==5){
                        if ($SID == ''){
                            $this->Signalwire->updatethisnumber(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE, $this->request->data['User']['phonesid']);
                        }else{
                            $this->Signalwire->updatethisnumber($SID,$AUTHTOKEN,$SIGNALWIRESPACE, $this->request->data['User']['phonesid']);
                        }
                        $this->User->saveField('phone_sid', $this->request->data['User']['phonesid']);
                        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                        $this->User->saveField('mms', $this->request->data['User']['mms']);
                        $this->User->saveField('fax', $this->request->data['User']['fax']);
                    }else if($API_TYPE==6){
                        if ($SID == ''){
                            $this->Ytel->updatethisnumber(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN, $phone_number);
                        }else{
                            $this->Ytel->updatethisnumber($SID,$AUTHTOKEN, $phone_number);
                        }
                        //$this->User->saveField('phone_sid', $this->request->data['User']['phonesid']);
                        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                        $this->User->saveField('mms', $this->request->data['User']['mms']);
                    }else if($API_TYPE==1){
                        if ($SID == ''){
                            $api_key = NEXMO_KEY;
                            $api_secret = NEXMO_SECRET;
                        }else{
                            $api_key = $SID;
                            $api_secret = $AUTHTOKEN;
                        }
                        $country_code = $this->request->data['User']['country'];
                        $siteurl = SITE_URL;
                        //$PhoneNumber = $this->request->data['User']['number'];
                        $this->Nexmo->updatenumber($country_code, $phone_number, $siteurl, $api_key, $api_secret);
                        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                    }else if($API_TYPE==3){
                        
                        if (PLIVOAPP_ID == '' && $SID == '') {
                            $this->Plivo->AuthId = PLIVO_KEY;
                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                            $response = $this->Plivo->create_application();
                            if (isset($response['response']['app_id'])) {
                                app::import('Model', 'Config');
                                $this->Config = new Config();
                                $config['Config']['id'] = 1;
                                $config['Config']['plivoapp_id'] = $response['response']['app_id'];
                                $appid = $response['response']['app_id'];
                                $this->Config->save($config);
                            }
                        }else if ($SID != '' && trim($someone['User']['app_id']) == '') {
                            $this->Plivo->AuthId = $SID;
                            $this->Plivo->AuthToken = $AUTHTOKEN;
                            $response = $this->Plivo->create_application();
                            if (isset($response['response']['app_id'])) {
                                $this->User->id = $user_id;
                                $this->User->saveField('app_id', $response['response']['app_id']);
                                $appid = $response['response']['app_id'];
                            }
                        }
                        
                        if($appid==''){
                            $appid = PLIVOAPP_ID;
                        }
                        if ($SID == ''){
                            $this->Plivo->AuthId = PLIVO_KEY;
                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                        }else{
                            $this->Plivo->AuthId = $SID;
                            $this->Plivo->AuthToken = $AUTHTOKEN;
                        }
                        $link_application_response = $this->Plivo->application_number($phone_number, $appid);
                        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                        $this->User->saveField('mms', $this->request->data['User']['mms']);
                    }else if($API_TYPE==4){
                        
                        if(BANDWIDTH_APPID=='' && $SID == ''){
            				$this->Bandwidth->createapplication(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID);
            				$response = $this->Bandwidth->getapplication(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID);
            				if(!empty($response)){
            					foreach($response as $response_arr){
            						if($response_arr->name ==SITENAME){
            							app::import('Model', 'Config');
            							$this->Config = new Config();
            							$config['Config']['id'] = 1;
            							$config['Config']['bandwidth_appid'] = $response_arr->id;
            							if($this->Config->save($config)){
            								$appid = $response_arr->id;
            							}
            						}
            					}
            				}
            			}else if ($SID != '' && trim($someone['User']['app_id']) == '') {
            			    $this->Bandwidth->createapplication($SID,$AUTHTOKEN,$BANDWIDTHUSERID);
            				$response = $this->Bandwidth->getapplication($SID,$AUTHTOKEN,$BANDWIDTHUSERID);
            				if(!empty($response)){
            					foreach($response as $response_arr){
            						if($response_arr->name ==SITENAME){
            							$this->User->id = $user_id;
                                        $this->User->saveField('app_id', $response_arr->id);
                                        $appid = $response_arr->id;
            						}
            					}
            				}
            			}
            			
            			if($appid==''){
                            $appid = BANDWIDTH_APPID;
                        }
            			if ($SID == ''){
               			    $response = $this->Bandwidth->updatethisnumber(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$phone_number,$appid);
            			}else{
            			    $response = $this->Bandwidth->updatethisnumber($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$phone_number,$appid);
            			}
                        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                        $this->User->saveField('mms', $this->request->data['User']['mms']);
                    }else if($API_TYPE==7){
                        
                        if($billinggroupid==''){
                            if($SID !=''){
                                $response = $this->Telnyx->createbillinggroup($SID,$username); 
                            }else{
                                $response = $this->Telnyx->createbillinggroup(TELNYX_KEY,$username);   
                            }
                            if(!isset($response->errors)){
                                $billinggroupid = $response->data->id;
                                $this->User->id = $user_id;
    			                $this->User->saveField('billinggroupid', $billinggroupid);
                            }
                        }
                        
                        /*if(TELNYX_MESSAGEPROFILE=='' && $SID == ''){
            				$response = $this->Telnyx->createmessagingprofile(TELNYX_KEY);
            				if(!isset($response->errors)){
            				    $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY);
            				    $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billinggroupid);
            			        $updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
            			        
            					app::import('Model', 'Config');
            					$this->Config = new Config();
            					$config['Config']['id'] = 1;
            					$config['Config']['telnyx_messageprofile'] = $response->data->id;
            					$config['Config']['telnyx_callapp'] = $callresponse->data->id;
            					if($this->Config->save($config)){
            						$appid = $response->data->id;
            						$callappid = $callresponse->data->id;
            					}
            				}
            			}else if (trim($SID) != '' && trim($someone['User']['app_id']) == '') {
            			    $response = $this->Telnyx->createmessagingprofile($SID);
            				if(!isset($response->errors)){
            				    $callresponse = $this->Telnyx->createcallapplication($SID);
            				    $voiceresponse = $this->Telnyx->createvoiceprofile($SID,$billinggroupid);
            			        $updatecallresponse = $this->Telnyx->updatecallapplication($SID,$callresponse->data->id,$voiceresponse->data->id);
            			        
            			      	$this->User->id = $user_id;
                                $this->User->saveField('app_id', $response->data->id);
                                $this->User->saveField('call_app_id', $callresponse->data->id);
                                $appid = $response->data->id;
                                $callappid = $callresponse->data->id;
            				}
            			}
            			
            			if (trim($appid) == '') {
                            $appid = TELNYX_MESSAGEPROFILE;
                        }
                        if (trim($callappid) == '') {
                            $callappid = TELNYX_CALLAPP;
                        }
                        */
               			
            			if ($SID == ''){
            			    $numbersresponse = $this->Telnyx->listallnumbers(TELNYX_KEY);
            			}else{
            			    $numbersresponse = $this->Telnyx->listallnumbers($SID);
            			}
            			foreach($numbersresponse as $phone_arr){
            			    if($phone_number == str_replace('+','',$phone_arr->phone_number)){
            			        $phoneid = $phone_arr->id;
            			        break;
            			    }
            			    
            			}
            			if ($SID == ''){
               			    $response = $this->Telnyx->updatemessagingnumber(TELNYX_KEY,$phoneid,$appid);
            			}else{
            			    $response = $this->Telnyx->updatemessagingnumber($SID,$phoneid,$appid);
            			}
            			if ($SID == ''){
               			    $response = $this->Telnyx->updatecallnumber(TELNYX_KEY,$phoneid,$callappid,$billinggroupid);
            			}else{
            			    $response = $this->Telnyx->updatecallnumber($SID,$phoneid,$callappid,$billinggroupid);
            			}
            			
            	        $this->User->saveField('voice', $this->request->data['User']['voice']);
                        $this->User->saveField('sms', $this->request->data['User']['sms']);
                        $this->User->saveField('mms', $this->request->data['User']['mms']);
                        $this->User->saveField('fax', $this->request->data['User']['fax']);
                        $this->User->saveField('phone_sid', $phoneid);
                    }
                
                    
                } else {
                    app::import('Model', 'UserNumber');
                    $this->UserNumber = new UserNumber();
                    $arr['UserNumber']['user_id'] = $user_id;
                    $arr['UserNumber']['number'] = $phone_number;
                    $arr['UserNumber']['api_type']  = $API_TYPE;
                    
                    if($API_TYPE==0){
                        if ($SID == ''){
                            $this->Twilio->AccountSid = TWILIO_ACCOUNTSID;
                            $this->Twilio->AuthToken = TWILIO_AUTH_TOKEN;
                        }else{
                            $this->Twilio->AccountSid = $SID;
                            $this->Twilio->AuthToken = $AUTHTOKEN;
                        }
                        $arr['UserNumber']['phone_sid'] = $this->request->data['User']['phonesid'];
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $arr['UserNumber']['mms'] =  $this->request->data['User']['mms'];
                        $arr['UserNumber']['fax'] =  $this->request->data['User']['fax'];
                        $this->UserNumber->save($arr);
                        $this->Twilio->updatenumberurls($this->request->data['User']['phonesid']);
                    }else if($API_TYPE==5){
                        if ($SID == ''){
                            $this->Signalwire->updatethisnumber(SIGNALWIRE_KEY,SIGNALWIRE_TOKEN,SIGNALWIRE_SPACE, $this->request->data['User']['phonesid']);
                        }else{
                            $this->Signalwire->updatethisnumber($SID,$AUTHTOKEN,$SIGNALWIRESPACE, $this->request->data['User']['phonesid']);
                        }
                        $arr['UserNumber']['phone_sid'] = $this->request->data['User']['phonesid'];
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $arr['UserNumber']['mms'] =  $this->request->data['User']['mms'];
                        $arr['UserNumber']['fax'] =  $this->request->data['User']['fax'];
                        $this->UserNumber->save($arr);
                    }else if($API_TYPE==6){
                        if ($SID == ''){
                            $this->Ytel->updatethisnumber(YTEL_ACCOUNTSID,YTEL_AUTH_TOKEN, $phone_number);
                        }else{
                            $this->Ytel->updatethisnumber($SID,$AUTHTOKEN, $phone_number);
                        }
                        //$arr['UserNumber']['phone_sid'] = $this->request->data['User']['phonesid'];
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $arr['UserNumber']['mms'] =  $this->request->data['User']['mms'];
                        $this->UserNumber->save($arr);
                    }else if($API_TYPE==1){
                        
                        if ($SID == ''){
                            $api_key = NEXMO_KEY;
                            $api_secret = NEXMO_SECRET;
                        }else{
                            $api_key = $SID;
                            $api_secret = $AUTHTOKEN;
                        }
                        $country_code = $this->request->data['User']['country'];
                        $siteurl = SITE_URL;
                        //$PhoneNumber = $this->request->data['User']['number'];
                        $this->Nexmo->updatenumber($country_code, $phone_number, $siteurl, $api_key, $api_secret);
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $this->UserNumber->save($arr);
                        
                    }else if($API_TYPE==3){
                        
                        if (PLIVOAPP_ID == '' && $SID == '') {
                            $this->Plivo->AuthId = PLIVO_KEY;
                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                            $response = $this->Plivo->create_application();
                            if (isset($response['response']['app_id'])) {
                                app::import('Model', 'Config');
                                $this->Config = new Config();
                                $config['Config']['id'] = 1;
                                $config['Config']['plivoapp_id'] = $response['response']['app_id'];
                                $appid = $response['response']['app_id'];
                                $this->Config->save($config);
                            }
                        }else if ($SID != '' && trim($someone['User']['app_id']) == '') {
                            $this->Plivo->AuthId = $SID;
                            $this->Plivo->AuthToken = $AUTHTOKEN;
                            $response = $this->Plivo->create_application();
                            if (isset($response['response']['app_id'])) {
                                $this->UserNumber->id = $user_id;
                                $this->UserNumber->saveField('app_id', $response['response']['app_id']);
                                $appid = $response['response']['app_id'];
                            }
                        }
                        
                        if($appid==''){
                            $appid = PLIVOAPP_ID;
                        }
                        if ($SID == ''){
                            $this->Plivo->AuthId = PLIVO_KEY;
                            $this->Plivo->AuthToken = PLIVO_TOKEN;
                        }else{
                            $this->Plivo->AuthId = $SID;
                            $this->Plivo->AuthToken = $AUTHTOKEN;
                        }
                        $link_application_response = $this->Plivo->application_number($phone_number, $appid);
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $arr['UserNumber']['mms'] =  $this->request->data['User']['mms'];
                        $this->UserNumber->save($arr);
                    }else if($API_TYPE==4){
                        
                        if(BANDWIDTH_APPID=='' && $SID == ''){
            				$this->Bandwidth->createapplication(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID);
            				$response = $this->Bandwidth->getapplication(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID);
            				if(!empty($response)){
            					foreach($response as $response_arr){
            						if($response_arr->name ==SITENAME){
            							app::import('Model', 'Config');
            							$this->Config = new Config();
            							$config['Config']['id'] = 1;
            							$config['Config']['bandwidth_appid'] = $response_arr->id;
            							if($this->Config->save($config)){
            								$appid = $response_arr->id;
            							}
            						}
            					}
            				}
            			}else if ($SID != '' && trim($someone['User']['app_id']) == '') {
            			    $this->Bandwidth->createapplication($SID,$AUTHTOKEN,$BANDWIDTHUSERID);
            				$response = $this->Bandwidth->getapplication($SID,$AUTHTOKEN,$BANDWIDTHUSERID);
            				if(!empty($response)){
            					foreach($response as $response_arr){
            						if($response_arr->name ==SITENAME){
            							$this->UserNumber->id = $user_id;
                                        $this->UserNumber->saveField('app_id', $response_arr->id);
                                        $appid = $response_arr->id;
            						}
            					}
            				}
            			}
            			
            			if($appid==''){
                            $appid = BANDWIDTH_APPID;
                        }
            			if ($SID == ''){
               			    $response = $this->Bandwidth->updatethisnumber(BANDWIDTH_KEY,BANDWIDTH_TOKEN,BANDWIDTH_USER_ID,$phone_number,$appid);
            			}else{
            			    $response = $this->Bandwidth->updatethisnumber($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$phone_number,$appid);
            			}
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $arr['UserNumber']['mms'] =  $this->request->data['User']['mms'];
                        $this->UserNumber->save($arr);
                    }else if($API_TYPE==7){
                        
                        if($billinggroupid==''){
                            if($SID !=''){
                                $response = $this->Telnyx->createbillinggroup($SID,$username); 
                            }else{
                                $response = $this->Telnyx->createbillinggroup(TELNYX_KEY,$username);   
                            }
                            if(!isset($response->errors)){
                                $billinggroupid = $response->data->id;
                                $this->User->id = $user_id;
    			                $this->User->saveField('billinggroupid', $billinggroupid);
                            }
                        }
                        
                        /*if(TELNYX_MESSAGEPROFILE=='' && $SID == ''){
            				$response = $this->Telnyx->createmessagingprofile(TELNYX_KEY);
            				if(!isset($response->errors)){
            				    $callresponse = $this->Telnyx->createcallapplication(TELNYX_KEY);
            				    $voiceresponse = $this->Telnyx->createvoiceprofile(TELNYX_KEY,$billinggroupid);
            			        $updatecallresponse = $this->Telnyx->updatecallapplication(TELNYX_KEY,$callresponse->data->id,$voiceresponse->data->id);
            			        
            					app::import('Model', 'Config');
            					$this->Config = new Config();
            					$config['Config']['id'] = 1;
            					$config['Config']['telnyx_messageprofile'] = $response->data->id;
            					$config['Config']['telnyx_callapp'] = $callresponse->data->id;
            					if($this->Config->save($config)){
            						$appid = $response->data->id;
            						$callappid = $callresponse->data->id;
            					}
            				}
            			}else if (trim($SID) != '' && trim($someone['User']['app_id']) == '') {
            			    $response = $this->Telnyx->createmessagingprofile($SID);
            				if(!isset($response->errors)){
            				    $callresponse = $this->Telnyx->createcallapplication($SID);
            				    $voiceresponse = $this->Telnyx->createvoiceprofile($SID,$billinggroupid);
            			        $updatecallresponse = $this->Telnyx->updatecallapplication($SID,$callresponse->data->id,$voiceresponse->data->id);
            			        
            					$this->User->id = $user_id;
                                $this->User->saveField('app_id', $response->data->id);
                                $this->User->saveField('call_app_id', $callresponse->data->id);
                                $appid = $response->data->id;
                                $callappid = $callresponse->data->id;
            				}
            			}
            			
            			if (trim($appid) == '') {
                            $appid = TELNYX_MESSAGEPROFILE;
                        }
                        if (trim($callappid) == '') {
                            $callappid = TELNYX_CALLAPP;
                        }
                        */
                        
               			if ($SID == ''){
            			    $numbersresponse = $this->Telnyx->listallnumbers(TELNYX_KEY);
            			}else{
            			    $numbersresponse = $this->Telnyx->listallnumbers($SID);
            			}
            			foreach($numbersresponse as $phone_arr){
            			    if($phone_number == str_replace('+','',$phone_arr->phone_number)){
            			        $phoneid = $phone_arr->id;
            			        break;
            			    }
            			    
            			}
            			if ($SID == ''){
               			    $response = $this->Telnyx->updatemessagingnumber(TELNYX_KEY,$phoneid,$appid);
            			}else{
            			    $response = $this->Telnyx->updatemessagingnumber($SID,$phoneid,$appid);
            			}
            			if ($SID == ''){
               			    $response = $this->Telnyx->updatecallnumber(TELNYX_KEY,$phoneid,$callappid,$billinggroupid);
            			}else{
            			    $response = $this->Telnyx->updatecallnumber($SID,$phoneid,$callappid,$billinggroupid);
            			}
            			
                        $arr['UserNumber']['voice'] = $this->request->data['User']['voice'];
                        $arr['UserNumber']['sms'] =  $this->request->data['User']['sms'];
                        $arr['UserNumber']['mms'] =  $this->request->data['User']['mms'];
                        $arr['UserNumber']['fax'] =  $this->request->data['User']['fax'];
                        $arr['UserNumber']['phone_sid'] = $phoneid;
                        $this->UserNumber->save($arr);
                    }
        
                    $arr_number['User']['id'] = $user_id;
                    $arr_number['User']['number_limit_count'] = $someone['User']['number_limit_count'] + 1;
                    $this->User->save($arr_number);
    
                }
                $this->Session->setFlash(__('Number Assigned', true));
                $this->redirect(array('action' => 'index'));
            }else{
                $this->Session->setFlash(__('The number you are trying to add already exists for a user account.', true));
                $this->redirect(array('action' => 'index'));
            }
        }else{
            $this->set('api_type', $API_TYPE);
        }
        
    }

    /*function recurringpayment(){
		$this->autoRender = false;
		if($_REQUEST['payment_status']=='Processed'){
	        app::import('Model','User');			
			$this->User=new User();
		    $reccuring_email = $this->User->find('first',array('conditions' => array('User.recurring_paypal_email'=>$_REQUEST['payer_email'])));
				if(!empty($reccuring_email)){
				app::import('Model','MonthlyPackage');	
				$this->MonthlyPackage=new MonthlyPackage();
				$monthlyPackages = $this->MonthlyPackage->find('first',array('conditions' => array('MonthlyPackage.id'=>$reccuring_email['User']['package'])));
				$this->request->data['User']['id']=$reccuring_email['User']['id'];
				$this->request->data['User']['sms_balance']=$reccuring_email['User']['sms_balance']+$monthlyPackages['MonthlyPackage']['text_messages_credit'];
				$this->request->data['User']['voice_balance']=$reccuring_email['User']['voice_balance']+$monthlyPackages['MonthlyPackage']['voice_messages_credit'];
				$this->request->data['User']['sms_credit_balance_email_alerts']=0;
				$this->request->data['User']['VM_credit_balance_email_alerts']=0;
				$nextdate= date("Y-m-d",mktime(0, 0, 0, date("m")+1 , date("d"), date("Y")));				
				$this->request->data['User']['next_renewal_dates'] =$nextdate;	
				$this->User->save($this->request->data);
				app::import('Model','Invoice');
				$this->Invoice=new Invoice();		
				$invoice['user_id']=$reccuring_email['User']['id'];
				$invoice['txnid']=$_REQUEST['txn_id'];	
				$invoice['amount']=$monthlyPackages['MonthlyPackage']['amount'];
				$invoice['type']=0;	
				$invoice['created']=date("Y-m-d");	
				$this->Invoice->save($invoice);
					if($reccuring_email['User']['active']==1){
						app::import('Model','Referral');
						$this->Referral=new Referral();
						$referraldetails = $this->Referral->find('all',array('conditions' => array('Referral.referred_by'=>$reccuring_email['User']['id'])));
						$percentage=$monthlyPackages['MonthlyPackage']['amount']*RECURRING_REFERRAL_PERCENT/100;
			
						if(!empty($referraldetails)){
							foreach($referraldetails as $referraldetail){
								$referral['id']=$referraldetail['Referral']['id'];
								$referral['amount']=$percentage;
								$referral['paid_status']=0;
								$this->Referral->save($referral);
							}
						}
					}
				}
		} else if($_REQUEST['payment_status']=='Completed'){
			app::import('Model','User');		
			$this->User=new User();
			$reccuring_email = $this->User->find('first',array('conditions' => array('User.recurring_paypal_email'=>$_REQUEST['payer_email'])));
			if(!empty($reccuring_email)){
				app::import('Model','MonthlyPackage');	
				$this->MonthlyPackage=new MonthlyPackage();
				$monthlyPackages = $this->MonthlyPackage->find('first',array('conditions' => array('MonthlyPackage.id'=>$reccuring_email['User']['package'])));
				$this->request->data['User']['id']=$reccuring_email['User']['id'];	
				$this->request->data['User']['sms_balance']=$reccuring_email['User']['sms_balance']+$monthlyPackages['MonthlyPackage']['text_messages_credit'];	
				$this->request->data['User']['voice_balance']=$reccuring_email['User']['voice_balance']+$monthlyPackages['MonthlyPackage']['voice_messages_credit'];
				$this->request->data['User']['sms_credit_balance_email_alerts']=0;		
				$this->request->data['User']['VM_credit_balance_email_alerts']=0;
				$nextdate= date("Y-m-d",mktime(0, 0, 0, date("m")+1 , date("d"), date("Y")));				
				$this->request->data['User']['next_renewal_dates'] =$nextdate;	
				$this->User->save($this->request->data);
				
				app::import('Model','Invoice');	
				$this->Invoice=new Invoice();	
				$invoice['user_id']=$reccuring_email['User']['id'];
				$invoice['txnid']=$_REQUEST['txn_id'];	
				$invoice['amount']=$monthlyPackages['MonthlyPackage']['amount'];
				$invoice['type']=0;	
				$invoice['created']=date("Y-m-d");	
				$this->Invoice->save($invoice);
				if($reccuring_email['User']['active']==1){
					app::import('Model','Referral');
					$this->Referral=new Referral();
					$referraldetails = $this->Referral->find('all',array('conditions' => array('Referral.referred_by'=>$reccuring_email['User']['id'])));
					$percentage=$monthlyPackages['MonthlyPackage']['amount']*RECURRING_REFERRAL_PERCENT/100;
					if(!empty($referraldetails)){
						foreach($referraldetails as $referraldetail){
							$referral['id']=$referraldetail['Referral']['id'];
							$referral['amount']=$percentage;
							$referral['paid_status']=0;
							$this->Referral->save($referral);
						}
					}
				}
			}
	
		}
		ob_start();
		print_r($_REQUEST);
		print_r($_POST);
		print_r($_GET);	
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("payment/recurringpayment".time().".txt", "w");
		fwrite($file, $out1); 
		fclose($file);
	}
	function payment(){
		$this->layout = 'default';
		$response = $this->Checkout->signup();
	}
	function returnurl(){
		ob_start();
		print_r($_REQUEST);
		$this->autoRender = false;
		$user_id=$this->Session->read('User.id');
		app::import('Model','Package');
		$this->Package=new Package();
		$Packageid = $this->Package->find('first',array('conditions' => array('Package.product_id'=>$_REQUEST['product_id'])));
		$balance = $this->User->find('first',array('conditions' => array('User.id'=>$user_id)));
		app::import('Model','MonthlyPackage');				
		$this->MonthlyPackage=new MonthlyPackage();
		$monthlyPackageid = $this->MonthlyPackage->find('first',array('conditions' => array('MonthlyPackage.id'=>$balance['User']['package'])));
		app::import('Model','Config');	
		$this->Config = new Config();
		$configdetails=$this->Config->find('first');		               
		if($_REQUEST['credit_card_processed']=='Y'){
			if($_REQUEST['product_id']==$configdetails['Config']['2CO_account_activation_prod_ID']){
				$this->request->data['User']['id']=$this->Session->read('User.id');
				$this->request->data['User']['sms_balance']=$configdetails['Config']['free_sms'];
				$this->request->data['User']['voice_balance']=$configdetails['Config']['free_voice'];
				$this->request->data['User']['active']=1;	
				$this->User->save($this->request->data);
					
				app::import('Model','Referral');	
				$this->Referral = new Referral();
				$Referraldetails = $this->Referral->find('first',array('conditions' => array('Referral.user_id'=>$this->Session->read('User.id'))));
				if(!empty($Referraldetails)){
					$referral['id']=$Referraldetails['Referral']['id'];
					$referral['account_activated']=1;	
					$this->Referral->save($referral);	
				}	
				$balance1 = $this->User->find('first',array('conditions' => array('User.id'=>$this->Session->read('User.id'))));	
			
				$sitename=str_replace(' ','',SITENAME);
				$this->Email->to = $balance1['User']['email'];
				$this->Email->subject = 'Account has been activated';
				$this->Email->from = $sitename;
				$this->Email->template = 'membership'; 
				$this->Email->sendAs = 'html'; 
				$this->set('data', $balance1);
				$this->Email->send();
				//------------------------------------------------------------------//
				$this->Session->setFlash(__('Thank you for activating your account with us.', true));  
			}else if($_REQUEST['product_id']==$monthlyPackageid['MonthlyPackage']['product_id']){
			        $this->request->data['User']['id']=$user_id;
					$this->request->data['User']['sms_balance']=$balance['User']['sms_balance']+$monthlyPackageid['MonthlyPackage']['text_messages_credit'];
					$this->request->data['User']['voice_balance']=$balance['User']['voice_balance']+$monthlyPackageid['MonthlyPackage']['voice_messages_credit'];
					$this->request->data['User']['sms_credit_balance_email_alerts']=0;
					$this->request->data['User']['recurring_checkout_email']=$_REQUEST['email'];
					$this->request->data['User']['VM_credit_balance_email_alerts']=0;
					$nextdate= date("Y-m-d",mktime(0, 0, 0, date("m")+1 , date("d"), date("Y")));				
					$this->request->data['User']['next_renewal_dates'] =$nextdate;
					$this->User->save($this->request->data);
					
					app::import('Model','Invoice');
					$this->Invoice=new Invoice();
					$invoice['user_id']=$user_id;
					$invoice['txnid']=$_REQUEST['order_number'];
					$invoice['type']=1;
					$invoice['amount']=$monthlyPackageid['MonthlyPackage']['amount'];
					$invoice['created']=date("Y-m-d");
					$this->Invoice->save($invoice);
					
					app::import('Model','Referral');	
					$this->Referral = new Referral();
					$Referraldetails = $this->Referral->find('first',array('conditions' => array('Referral.user_id'=>$user_id)));
					if(!empty($Referraldetails)){
						$referral['id']=$Referraldetails['Referral']['id'];
						$referral['account_activated']=1;	
						$this->Referral->save($referral);
					}
			}else if(!empty($Packageid)){
				$user_id=$this->Session->read('User.id');
				app::import('Model','User');	
				$this->User=new User();
				$smsbalance = $this->User->find('first',array('conditions' => array('User.id'=>$user_id)));
				if($Packageid['Package']['type']=='text'){
				    $this->request->data['User']['id']=$user_id;
					$this->request->data['User']['sms_balance']=$smsbalance['User']['sms_balance']+$Packageid['Package']['credit'];
					$this->request->data['User']['sms_credit_balance_email_alerts']=0;
					$this->User->save($this->request->data);
					
					app::import('Model','Invoice');
					$this->Invoice=new Invoice();
					$invoice['user_id']=$user_id;
					$invoice['txnid']=$_REQUEST['order_number'];
					$invoice['type']=1;
					$invoice['amount']=$Packageid['Package']['amount'];
					$invoice['created']=date("Y-m-d");
					$this->Invoice->save($invoice);
					$this->Session->setFlash(__('Thank you for your SMS credit package purchase!', true)); 
				}else if($Packageid['Package']['type']=='voice'){
				    $this->request->data['User']['id']=$user_id;
					$this->request->data['User']['voice_balance']=$smsbalance['User']['voice_balance']+$Packageid['Package']['credit'];
					$this->request->data['User']['VM_credit_balance_email_alerts']=0;
					$this->User->save($this->request->data);
					
					app::import('Model','Invoice');
					$this->Invoice=new Invoice();
					$invoice['user_id']=$user_id;
					$invoice['txnid']=$_REQUEST['order_number'];
					$invoice['type']=1;
					$invoice['amount']=$Packageid['Package']['amount'];
					$invoice['created']=date("Y-m-d");
					$this->Invoice->save($invoice);
					$this->Session->setFlash(__('Thank you for your voice credit package purchase!', true));
				}
			}
		}
			
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("payment/payments".time().".txt", "w");
		fwrite($file, $out1); 
		fclose($file);	
	}	

    function paymentsucess(){
	
		$this->autoRender = false;
		if($_POST['message_type']=='RECURRING_INSTALLMENT_SUCCESS'){
			app::import('Model','User');		
			$this->User=new User();
		    $customeremail = $this->User->find('first',array('conditions' => array('User.recurring_checkout_email'=>$_POST['customer_email'])));
			if(!empty($customeremail)){
				app::import('Model','MonthlyPackage');	
				$this->MonthlyPackage=new MonthlyPackage();
				$monthlyPackages = $this->MonthlyPackage->find('first',array('conditions' => array('MonthlyPackage.id'=>$customeremail['User']['package'])));
				$this->request->data['User']['id']=$customeremail['User']['id'];	
				$this->request->data['User']['sms_balance']=$customeremail['User']['sms_balance']+$monthlyPackages['MonthlyPackage']['text_messages_credit'];	
				$this->request->data['User']['voice_balance']=$customeremail['User']['voice_balance']+$monthlyPackages['MonthlyPackage']['voice_messages_credit'];
				$this->request->data['User']['sms_credit_balance_email_alerts']=0;	
				$this->request->data['User']['VM_credit_balance_email_alerts']=0;
				$nextdate= date("Y-m-d",mktime(0, 0, 0, date("m")+1 , date("d"), date("Y")));				
				$this->request->data['User']['next_renewal_dates'] =$nextdate;	
				$this->User->save($this->request->data);
				
				app::import('Model','Invoice');	
				$this->Invoice=new Invoice();	
				$invoice['user_id']=$customeremail['User']['id'];
				$invoice['txnid']=$_POST['sale_id'];	
				$invoice['amount']=$monthlyPackages['MonthlyPackage']['amount'];
				$invoice['type']=1;		
				$invoice['created']=date("Y-m-d");	
				$this->Invoice->save($invoice);
				if($customeremail['User']['active']==1){
					app::import('Model','Referral');
					$this->Referral=new Referral();
					$referraldetail = $this->Referral->find('all',array('conditions' => array('Referral.referred_by'=>$customeremail['User']['id'])));
					$percentage=$monthlyPackages['MonthlyPackage']['amount']*RECURRING_REFERRAL_PERCENT/100;
				
					if(!empty($referraldetail)){
						foreach($referraldetail as $referralnew){
							$referral['id']=$referralnew['Referral']['id'];
							$referral['amount']=$percentage;
							$referral['paid_status']=0;
							$this->Referral->save($referral);
						}
					}
				}
			}
		}else if($_POST['message_type']=='RECURRING_STOPPED'){	
		app::import('Model','User');		
		$this->User=new User();
		$customerdetails = $this->User->find('first',array('conditions' => array('User.recurring_checkout_email'=>$_POST['customer_email'])));
			if(!empty($customerdetails)){
				$this->request->data['User']['id']=$customerdetails['User']['id'];			
				$this->request->data['User']['active'] = 0;	
				$this->User->save($this->request->data);
			}	
		}
	}	
				
	function checksale(){
		$this->autoRender = false;
		$response = $this->Checkout->checksale();
	}
	*/
}