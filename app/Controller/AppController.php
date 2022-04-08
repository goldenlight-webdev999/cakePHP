<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('ConnectionManager', 'Model');
include('./accounts.php');
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    //var $helpers = array('Html', 'Form', 'Session', 'Form2', 'PaypalIpn.Paypal', 'Javascript', 'Validation');
    var $helpers = array('Html', 'Form', 'Session', 'Form2', 'Validation', 'PaypalIpn.Paypal');
    var $components = array('Cookie', 'Session');


    function beforeFilter()
    {
        
        if (!empty($_GET['ref'])) {
            $refArray = array();
            $refArray['id'] = $_GET['ref'];
            $refArray['url'] = @$trace_referer['host'];
            $trace_referer = parse_url(@$_SERVER['HTTP_REFERER']);
            $this->loadModel('User');
            $record = $this->User->find('count', array('conditions' => array('User.id' => $_GET['ref'])));
            if ($record > 0) {
                $this->Cookie->time = '7 Days';
                $this->Cookie->write('refArray', $refArray);
            }


        } else if (!empty($_GET['recurring_ref'])) {
            $refArraynew = array();
            $refArraynew['id'] = $_GET['recurring_ref'];
            $refArraynew['url'] = @$trace_referer['host'];
            $trace_referer = parse_url(@$_SERVER['HTTP_REFERER']);
            $this->loadModel('User');
            $record = $this->User->find('count', array('conditions' => array('User.id' => $_GET['recurring_ref'])));
            if ($record > 0) {
                $this->Cookie->time = '7 Days';
                $this->Cookie->write('recurringrefArray', $refArraynew);
            }
        }

        if (($this->params['controller'] == 'admin_users' && $this->params['action'] != 'login') || isset($this->params['admin'])) {
            $this->layout = 'admin';
            if (!$this->Session->check('AdminUser')) {
                $this->Session->setFlash('You need to login first');
                $this->redirect('/admin_users/login');
            }
        } else if (!($this->params['controller'] == 'telnyxs' || $this->params['controller'] == 'ytels' || $this->params['controller'] == 'signalwires' || $this->params['controller'] == 'bandwidths' || $this->params['controller'] == 'apis' || $this->params['controller'] == 'demos' || $this->params['controller'] == 'twilios' || 
        $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos' || $this->params['controller'] == 'cronjobs' || $this->params['action'] == 'send_message' || 
        $this->params['controller'] == 'emailalerts' || $this->params['action'] == 'sendemail' || $this->params['controller'] == 'instant_payment_notifications' || $this->params['action'] == 'home' || 
        $this->params['action'] == 'captcha_image' || $this->params['action'] == 'success' || $this->params['action'] == 'forgot_password' || $this->params['action'] == 'sms' || 
        $this->params['action'] == 'voice' || $this->params['action'] == 'pwdreset' || $this->params['action'] == 'login' || $this->params['action'] == 'about' || $this->params['action'] == 'terms_conditions' || 
        $this->params['action'] == 'privacy_policy' || $this->params['action'] == 'faq' || $this->params['action'] == 'antispampolicy' || $this->params['action'] == 'paymentsucess' || 
        $this->params['action'] == 'recurringpayment' || $this->params['action'] == 'returnurl' || $this->params['action'] == 'user_activate_account' || $this->params['action'] == 'add' || 
        $this->params['action'] == 'qrcodes' || $this->params['controller'] == 'pages' || $this->params['action'] == 'peoplecallrecordscript' || 
        ($this->params['controller'] == 'webwidgets' && $this->params['action'] == 'subscribe') || 
        ($this->params['controller'] == 'users' && $this->params['action'] == 'updateurl' || $this->params['action'] == 'redeem' || $this->params['action'] == 'autologin') || 
        ($this->params['controller'] == 'kiosks' && $this->params['action'] == 'view' || $this->params['action'] == 'joins' || $this->params['action'] == 'checkpoints' || $this->params['action'] == 'punchcard' || $this->params['action'] == 'success' || $this->params['action'] == 'getnotification') || ($this->params['controller'] == 'mobilecoupons' && ($this->params['action'] == 'view' || $this->params['action'] == 'mobile_coupon_analytics' || $this->params['action'] == 'redeemed'))) 
        && !$this->Session->check('User')) {
            $this->redirect('/users/login');
        }
        
        $someone = $this->getLoggedUserDetails();
        if (!empty($someone)) {
            $timezone = $someone['User']['timezone'];
            date_default_timezone_set($timezone);

        }
        
        $this->getuserdetails();
        $this->getadminstats();
        $this->defineConstant();
        
        $setsqlmode = $this->Session->read('setsqlmode');

        if (!isset($setsqlmode)) {
            $this->Session->write('setsqlmode', 1);
            
            $dataSource = ConnectionManager::getDataSource('default');
            $username = $dataSource->config['login'];
            $password = $dataSource->config['password'];
            $database = $dataSource->config['database'];
        
            $con = mysqli_connect("localhost",$username,$password);
	        mysqli_select_db($con,$database);
            mysqli_query($con,"SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
        }
        
        if ($this->Session->check('Subaccount')) {
            $this->loadModel('Subaccount');
            $someone = $this->Subaccount->find('first', array('conditions' => array('Subaccount.id' => $this->Session->read('Subaccount.id'))));

            if ($this->params['controller'] == 'birthday' && $someone['Subaccount']['birthdaywishes'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the birthday SMS wishes module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'responders' && $someone['Subaccount']['autoresponders'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the autoresponders module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'contacts' && $this->params['action'] == 'upload') && $someone['Subaccount']['importcontacts'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the import contacts module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'users' && $this->params['action'] == 'shortlinks') && $someone['Subaccount']['shortlinks'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the short links module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'groups' && ($this->params['action'] == 'broadcast_list' || $this->params['action'] == 'voicebroadcasting')) && $someone['Subaccount']['voicebroadcast'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the voice broadcast module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'polls' and $someone['Subaccount']['polls'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the polls module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'contests' and $someone['Subaccount']['contests'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the contests module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'smsloyalty' and $someone['Subaccount']['loyaltyprograms'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the loyalty programs module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'kiosks' and $this->params['action'] != 'joins' and $this->params['action'] != 'success' and $this->params['action'] != 'checkpoints' and $this->params['action'] != 'view' and $this->params['action'] != 'punchcard' and $someone['Subaccount']['kioskbuilder'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the kiosks module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'mobile_pages' and $someone['Subaccount']['mobilepagebuilder'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the mobile splash page builder module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'webwidgets' and $this->params['action'] != 'subscribe' and $someone['Subaccount']['webwidgets'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the web sign-up widgets module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'chats' && $someone['Subaccount']['smschat'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the 2-way SMS chat module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'users' && $this->params['action'] == 'qrcodeindex' && $someone['Subaccount']['qrcodes'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the QR codes module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'schedulers' && $someone['Subaccount']['calendarscheduler'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the calendar/scheduler module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'appointments' && $someone['Subaccount']['appointments'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the appointments module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'groups' && ($this->params['action'] == 'index' || $this->params['action'] == 'add' || $this->params['action'] == 'edit' || $this->params['action'] == 'delete' || $this->params['action'] == 'contactlist')) && $someone['Subaccount']['groups'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the groups module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'users' && $this->params['action'] == 'subscribers' && $someone['Subaccount']['reports'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the reports module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'users' && $this->params['action'] == 'affiliates' && $someone['Subaccount']['affiliates'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the affiliates module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'logs' && $someone['Subaccount']['logs'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the logs module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'contacts' && $this->params['action'] == 'index') && $someone['Subaccount']['contactlist'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the contacts list module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'messages' && ($this->params['action'] == 'send_message' || $this->params['action'] == 'schedule_message' || $this->params['action'] == 'singlemessages' || $this->params['action'] == 'template_message')) && $someone['Subaccount']['sendsms'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to send or schedule SMS');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'contacts' && ($this->params['action'] == 'send_sms' || $this->params['action'] == 'nexmo_send_sms' || $this->params['action'] == 'plivo_send_sms' || $this->params['action'] == 'slooce_send_sms')) && $someone['Subaccount']['sendsms'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to send or schedule SMS');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if //((($this->params['controller'] == 'twilios' || $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos') && $this->params['action'] == 'searchcountry') && $someone['Subaccount']['getnumbers'] == 0) {
            (((($this->params['controller'] == 'twilios' || $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos') && $this->params['action'] == 'searchcountry' && $someone['Subaccount']['getnumbers'] == 0)) || ($this->params['controller'] == 'twilios' || $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos') && $this->params['action'] == 'searchcountry' && REQUIRE_MONTHLY_GETNUMBER == 1 && $someone['User']['package'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to get numbers');
                //$this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'users' && ($this->params['action'] == 'paypalpayment' || $this->params['action'] == 'stripepayment' || $this->params['action'] == 'paypalnumbers' || $this->params['action'] == 'stripenumbers' || $this->params['action'] == 'activation')) && $someone['Subaccount']['makepurchases'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to make purchases');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'multiquestions' && ($someone['Subaccount']['qabots'] == 0 || NUMACCOUNTS < 30)) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to Q&A SMS bots');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'mobilecoupons' && $this->params['action'] != 'view' && $this->params['action'] != 'mobile_coupon_analytics' && $this->params['action'] != 'redeemed' && ($someone['Subaccount']['mobilecoupons'] == 0 || NUMACCOUNTS < 100)) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to Mobile Coupons');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            }
        } else {
            if ($this->params['controller'] == 'birthday' && $someone['User']['birthdaywishes'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the birthday SMS wishes module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'responders' && $someone['User']['autoresponders'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the autoresponders module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'contacts' && $this->params['action'] == 'upload') && $someone['User']['importcontacts'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the import contacts module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'users' && $this->params['action'] == 'shortlinks') && $someone['User']['shortlinks'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the short links module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'groups' && ($this->params['action'] == 'broadcast_list' || $this->params['action'] == 'voicebroadcasting')) && $someone['User']['voicebroadcast'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the voice broadcast module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'polls' and $someone['User']['polls'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the polls module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'contests' and $someone['User']['contests'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the contests module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'smsloyalty' and $someone['User']['loyaltyprograms'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the loyalty programs module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'kiosks' and $this->params['action'] != 'joins' and $this->params['action'] != 'success' and $this->params['action'] != 'checkpoints' and $this->params['action'] != 'view' and $this->params['action'] != 'punchcard' and $someone['User']['kioskbuilder'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the kiosks module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'mobile_pages' and $someone['User']['mobilepagebuilder'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the mobile splash page builder module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'webwidgets' and $this->params['action'] != 'subscribe' and $someone['User']['webwidgets'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the web sign-up widgets module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'chats' && $someone['User']['smschat'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the 2-way SMS chat module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'users' && $this->params['action'] == 'qrcodeindex' && $someone['User']['qrcodes'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the QR codes module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'schedulers' && $someone['User']['calendarscheduler'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the calendar/scheduler module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'appointments' && $someone['User']['appointments'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the appointments module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'groups' && ($this->params['action'] == 'index' || $this->params['action'] == 'add' || $this->params['action'] == 'edit' || $this->params['action'] == 'delete' || $this->params['action'] == 'contactlist')) && $someone['User']['groups'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the groups module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'users' && $this->params['action'] == 'subscribers' && $someone['User']['reports'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the reports module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'users' && $this->params['action'] == 'affiliates' && $someone['User']['affiliates'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the affiliates module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'logs' && $someone['User']['logs'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the logs module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'contacts' && $this->params['action'] == 'index') && $someone['User']['contactlist'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to the contacts list module');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'messages' && ($this->params['action'] == 'send_message' || $this->params['action'] == 'schedule_message' || $this->params['action'] == 'singlemessages' || $this->params['action'] == 'template_message')) && $someone['User']['sendsms'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to send or schedule SMS');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'contacts' && ($this->params['action'] == 'send_sms' || $this->params['action'] == 'nexmo_send_sms' || $this->params['action'] == 'plivo_send_sms' || $this->params['action'] == 'slooce_send_sms')) && $someone['User']['sendsms'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to send or schedule SMS');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if //((($this->params['controller'] == 'twilios' || $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos') && $this->params['action'] == 'searchcountry') && $someone['User']['getnumbers'] == 0) {
            (((($this->params['controller'] == 'twilios' || $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos') && $this->params['action'] == 'searchcountry' && $someone['User']['getnumbers'] == 0)) || ($this->params['controller'] == 'twilios' || $this->params['controller'] == 'nexmos' || $this->params['controller'] == 'plivos') && $this->params['action'] == 'searchcountry' && REQUIRE_MONTHLY_GETNUMBER == 1 && $someone['User']['package'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to get numbers');
                //$this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if (($this->params['controller'] == 'users' && ($this->params['action'] == 'paypalpayment' || $this->params['action'] == 'stripepayment' || $this->params['action'] == 'paypalnumbers' || $this->params['action'] == 'stripenumbers' || $this->params['action'] == 'activation')) && $someone['User']['makepurchases'] == 0) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to make purchases');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'multiquestions' && ($someone['User']['qabots'] == 0 || NUMACCOUNTS < 30)) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to Q&A SMS bots');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else if ($this->params['controller'] == 'mobilecoupons' && $this->params['action'] != 'view' && $this->params['action'] != 'mobile_coupon_analytics' && $this->params['action'] != 'redeemed' && ($someone['User']['mobilecoupons'] == 0 || NUMACCOUNTS < 100)) {
                $this->layout = 'admin_new_layout';
                $this->Session->setFlash('You do not have access to Mobile Coupons');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            }
        }

        
    }

    function beforeRender()
    {
        $this->loadModel('User');

        if ($this->isLoggedIn()) {

            $this->set('statistic', $this->statistic());
            $user = $this->getLoggedUserDetails();
            $this->set('loggedUser', $user);

            if ($this->Session->check('Subaccount')) {
                $user = $this->getLoggedSubaccountDetails();
                $userperm['getnumbers'] = $user['Subaccount']['getnumbers'];
                $userperm['autoresponders'] = $user['Subaccount']['autoresponders'];
                $userperm['importcontacts'] = $user['Subaccount']['importcontacts'];
                $userperm['shortlinks'] = $user['Subaccount']['shortlinks'];
                $userperm['voicebroadcast'] = $user['Subaccount']['voicebroadcast'];
                $userperm['polls'] = $user['Subaccount']['polls'];
                $userperm['contests'] = $user['Subaccount']['contests'];
                $userperm['loyaltyprograms'] = $user['Subaccount']['loyaltyprograms'];
                $userperm['kioskbuilder'] = $user['Subaccount']['kioskbuilder'];
                $userperm['birthdaywishes'] = $user['Subaccount']['birthdaywishes'];
                $userperm['mobilepagebuilder'] = $user['Subaccount']['mobilepagebuilder'];
                $userperm['webwidgets'] = $user['Subaccount']['webwidgets'];
                $userperm['smschat'] = $user['Subaccount']['smschat'];
                $userperm['qrcodes'] = $user['Subaccount']['qrcodes'];
                $userperm['calendarscheduler'] = $user['Subaccount']['calendarscheduler'];
                $userperm['appointments'] = $user['Subaccount']['appointments'];
                $userperm['groups'] = $user['Subaccount']['groups'];
                $userperm['contactlist'] = $user['Subaccount']['contactlist'];
                $userperm['sendsms'] = $user['Subaccount']['sendsms'];
                $userperm['logs'] = $user['Subaccount']['logs'];
                $userperm['reports'] = $user['Subaccount']['reports'];
                $userperm['affiliates'] = $user['Subaccount']['affiliates'];
                $userperm['makepurchases'] = $user['Subaccount']['makepurchases'];
                $userperm['mobilecoupons'] = $user['Subaccount']['mobilecoupons'];
                $userperm['qabots'] = $user['Subaccount']['qabots'];
            } else {
                $userperm['getnumbers'] = $user['User']['getnumbers'];
                $userperm['autoresponders'] = $user['User']['autoresponders'];
                $userperm['importcontacts'] = $user['User']['importcontacts'];
                $userperm['shortlinks'] = $user['User']['shortlinks'];
                $userperm['voicebroadcast'] = $user['User']['voicebroadcast'];
                $userperm['polls'] = $user['User']['polls'];
                $userperm['contests'] = $user['User']['contests'];
                $userperm['loyaltyprograms'] = $user['User']['loyaltyprograms'];
                $userperm['kioskbuilder'] = $user['User']['kioskbuilder'];
                $userperm['birthdaywishes'] = $user['User']['birthdaywishes'];
                $userperm['mobilepagebuilder'] = $user['User']['mobilepagebuilder'];
                $userperm['webwidgets'] = $user['User']['webwidgets'];
                $userperm['smschat'] = $user['User']['smschat'];
                $userperm['qrcodes'] = $user['User']['qrcodes'];
                $userperm['calendarscheduler'] = $user['User']['calendarscheduler'];
                $userperm['appointments'] = $user['User']['appointments'];
                $userperm['groups'] = $user['User']['groups'];
                $userperm['contactlist'] = $user['User']['contactlist'];
                $userperm['sendsms'] = $user['User']['sendsms'];
                $userperm['logs'] = $user['User']['logs'];
                $userperm['reports'] = $user['User']['reports'];
                $userperm['affiliates'] = $user['User']['affiliates'];
                $userperm['makepurchases'] = $user['User']['makepurchases'];
                $userperm['mobilecoupons'] = $user['User']['mobilecoupons'];
                $userperm['qabots'] = $user['User']['qabots'];
            }

            $this->set('userperm', $userperm);

        }
        $this->_setErrorLayout();
    }

    function _setErrorLayout()
    {
        if ($this->name == 'CakeError') {
            if ($this->isLoggedIn()) {
                $this->layout = 'login_layout';
            } else {
                $this->layout = '';
            }
        }
    }

    function getLoggedUserDetails()
    {
        $this->loadModel('User');
        return $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
    }

    function getLoggedSubaccountDetails()
    {
        $this->loadModel('Subaccount');
        return $this->Subaccount->find('first', array('conditions' => array('Subaccount.id' => $this->Session->read('Subaccount.id'))));
    }

    function isLoggedIn()
    {
        if ($this->Session->read('User.id'))
            return true;
        else
            return false;
    }

    function getLoggedInUserId()
    {
        return $this->Session->read('User.id');
    }

    function getLoggedInUserName()
    {
        return $this->Session->read('User.username');
    }

    function defineConstant()
    {
        $this->loadModel('Config');
        $this->loadModel('Stripe');
        
        $configs = $this->Config->find('first');
        unset($configs['Config']['id']);
        /*foreach ($configs['Config'] as $key => $value) {
            define(strtoupper($key), $value);
        }*/
        $loguser = $this->getLoggedUserDetails();
        
        
        foreach ($configs['Config'] as $key => $value) {
            if (strtoupper(trim($key)) == "TWILIO_ACCOUNTSID" || strtoupper(trim($key)) == "PLIVO_KEY" || strtoupper(trim($key)) == "NEXMO_KEY" || strtoupper(trim($key)) == "BANDWIDTH_KEY" || strtoupper(trim($key)) == "SIGNALWIRE_KEY" || strtoupper(trim($key)) == "YTEL_ACCOUNTSID" || strtoupper(trim($key)) == "TELNYX_KEY") {
                if(trim($loguser['User']['sid']) !=''){
                    define(strtoupper($key), trim($loguser['User']['sid']));
                }else{
                    define(strtoupper($key), $value);
                }
            }else if(strtoupper(trim($key)) == "TWILIO_AUTH_TOKEN" || strtoupper(trim($key)) == "PLIVO_TOKEN" || strtoupper(trim($key)) == "NEXMO_SECRET" || strtoupper(trim($key)) == "BANDWIDTH_TOKEN" || strtoupper(trim($key)) == "SIGNALWIRE_TOKEN" || strtoupper(trim($key)) == "YTEL_AUTH_TOKEN") {
                if(trim($loguser['User']['authtoken']) !=''){
                    define(strtoupper($key), trim($loguser['User']['authtoken']));
                }else{
                    define(strtoupper($key), $value);
                }
            }else if(strtoupper(trim($key)) == "BANDWIDTH_USER_ID") {
                if(trim($loguser['User']['bandwidthuserid']) !=''){
                    define(strtoupper($key), trim($loguser['User']['bandwidthuserid']));
                }else{
                    define(strtoupper($key), $value);
                }
            }else if(strtoupper(trim($key)) == "SIGNALWIRE_SPACE") {
                if(trim($loguser['User']['signalwirespace']) !=''){
                    define(strtoupper($key), trim($loguser['User']['signalwirespace']));
                }else{
                    define(strtoupper($key), $value);
                }
            }else if(strtoupper(trim($key)) == "TELNYX_CALLAPP") {
                if(trim($loguser['User']['call_app_id']) !=''){
                    define(strtoupper($key), trim($loguser['User']['call_app_id']));
                }else{
                    define(strtoupper($key), $value);
                }
            }else{
                define(strtoupper($key), $value);
            }
            
        }
        
        $stripes = $this->Stripe->find('first');
        unset($stripes['Stripe']['id']);
        foreach ($stripes['Stripe'] as $key => $value) {
            define(strtoupper($key), $value);
        }
        //$loguser = $this->getLoggedUserDetails();
        define('API_TYPE', $loguser['User']['api_type']);
    }

    function getRealUserIp()
    {
        switch (true) {
            case (!empty($_SERVER['HTTP_X_REAL_IP'])) :
                return $_SERVER['HTTP_X_REAL_IP'];
            case (!empty($_SERVER['HTTP_CLIENT_IP'])) :
                return $_SERVER['HTTP_CLIENT_IP'];
            case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) :
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            default :
                return $_SERVER['REMOTE_ADDR'];
        }
    }
    

    function getuserdetails()
    {
        $this->loadModel('Log');
        //$this->loadModel('ContactGroup');
        $user_id = $this->Session->read('User.id');
        if (isset($user_id)) {
            $month = date('m');
            //$this->Log->recursive = -1;
            $logs_inboxdata = $this->Log->find('all', array('conditions' => array('Log.user_id' => $user_id, 'Log.route' => 'inbox', 'msg_type' => 'text', 'read' => '0'), 'limit' => 10, 'recursive' => -1, 'order' => array('Log.created DESC'), 'fields' => array('Log.id', 'Log.phone_number', 'Log.created', 'Log.text_message', 'Log.image_url')));
            $this->set('logs_inboxdetails', $logs_inboxdata);
            
            $logs_inboxvoicedata = $this->Log->find('all', array('conditions' => array('Log.user_id' => $user_id, 'Log.route' => 'inbox', 'msg_type' => 'voice', 'read' => '0'), 'limit' => 10, 'recursive' => -1, 'order' => array('Log.created DESC'), 'fields' => array('Log.voice_url', 'Log.phone_number')));
            $this->set('logs_inboxvoicedetails', $logs_inboxvoicedata);
            
            $logs_inboxfaxdata = $this->Log->find('all', array('conditions' => array('Log.user_id' => $user_id, 'Log.route' => 'inbox', 'msg_type' => 'fax', 'read' => '0'), 'limit' => 10, 'recursive' => -1, 'order' => array('Log.created DESC'), 'fields' => array('Log.voice_url', 'Log.phone_number')));
            $this->set('logs_inboxfaxdetails', $logs_inboxfaxdata);
            
            $this->set('unreadTextMsg', $this->Log->find('count', array('conditions' => array('Log.user_id' => $user_id, 'Log.route' => 'inbox', 'msg_type' => 'text', 'Log.read' => 0,), 'recursive' => -1)));
            $this->set('unreadVoiceMsg', $this->Log->find('count', array('conditions' => array('Log.user_id' => $user_id, 'Log.route' => 'inbox', 'msg_type' => 'voice', 'Log.read' => 0,), 'recursive' => -1)));
            $this->set('unreadFaxMsg', $this->Log->find('count', array('conditions' => array('Log.user_id' => $user_id, 'Log.route' => 'inbox', 'msg_type' => 'fax', 'Log.read' => 0,), 'recursive' => -1)));
        }
    }

    function getadminstats()
    {

        $adminstatssessiondata = $this->Session->read('adminstats');

        if ($this->Session->check('AdminUser') && !isset($adminstatssessiondata)) {

            $this->Session->write('adminstats', 1);
            
            $someone = $this->getLoggedUserDetails();
            if (!empty($someone)) {
                $timezone = $someone['User']['timezone'];
                date_default_timezone_set($timezone);
    
            }

            app::import('Model', 'Invoice');
            $this->Invoice = new Invoice();
            $this->loadModel('Log');

            $month = date('m');
            $year = date('Y');
            $week = date('W');
            $current_date = date('Y-m-d');
            $one_week_date = date('Y-m-d', strtotime('-7 days'));

            $firstdaymonth = new DateTime('first day of this month');
            $firstdaymonth = $firstdaymonth->format('Y-m-d 00:00:00');
            $lastdaymonth = new DateTime('last day of this month');
            $lastdaymonth = $lastdaymonth->format('Y-m-d 23:59:59');

            $firstdayyear = new DateTime('first day of january this year');
            $firstdayyear = $firstdayyear->format('Y-m-d 00:00:00');
            $lastdayyear = new DateTime('last day of december this year');
            $lastdayyear = $lastdayyear->format('Y-m-d 23:59:59');

            $current_date_week = new DateTime('today');
            $current_date_week = $current_date_week->format('Y-m-d 23:59:59');
            $one_week_date_week = new DateTime('-7 days');
            $one_week_date_week = $one_week_date_week->format('Y-m-d 00:00:00');

            $this->Log->recursive = -1;

            $activeclientsweek = $this->User->find('count', array('conditions' => array('User.active' => 1, 'DATE(User.created) >=' => $one_week_date, 'DATE(User.created) <=' => $current_date)));
            $activeclientsmonth = $this->User->find('count', array('conditions' => array('User.active' => 1, 'MONTH(User.created)' => $month, 'YEAR(User.created)' => $year)));
            $activeclientsyear = $this->User->find('count', array('conditions' => array('User.active' => 1, 'YEAR(User.created)' => $year)));
            $activeclientsall = $this->User->find('count', array('conditions' => array('User.active' => 1)));

            $this->Session->write('activeclientsweek', number_format($activeclientsweek));
            $this->Session->write('activeclientsmonth', number_format($activeclientsmonth));
            $this->Session->write('activeclientsyear', number_format($activeclientsyear));
            $this->Session->write('activeclientsall', number_format($activeclientsall));

            //$smsreceivedweek =  $this->Log->find('count', array('conditions' => array('Log.route' => 'inbox' ,'msg_type' => 'text','sms_status' => 'received','DATE(Log.created) >=' =>$one_week_date,'DATE(Log.created) <=' =>$current_date)));
            //$smsreceivedmonth =  $this->Log->find('count', array('conditions' => array('Log.route' => 'inbox' ,'msg_type' => 'text','sms_status' => 'received','MONTH(Log.created)' =>$month,'YEAR(Log.created)' =>$year)));
            //$smsreceivedyear =  $this->Log->find('count', array('conditions' => array('Log.route' => 'inbox' ,'msg_type' => 'text','sms_status' => 'received','YEAR(Log.created)' =>$year)));

            $smsreceivedweek = $this->Log->find('count', array('conditions' => array('Log.route' => 'inbox', 'msg_type' => 'text', 'sms_status' => array('received', 'success'), 'Log.created >=' => $one_week_date_week, 'Log.created <=' => $current_date_week)));
            $smsreceivedmonth = $this->Log->find('count', array('conditions' => array('Log.route' => 'inbox', 'msg_type' => 'text', 'sms_status' => array('received', 'success'), 'Log.created >=' => $firstdaymonth, 'Log.created <=' => $lastdaymonth)));
            $smsreceivedyear = $this->Log->find('count', array('conditions' => array('Log.route' => 'inbox', 'msg_type' => 'text', 'sms_status' => array('received', 'success'), 'Log.created >=' => $firstdayyear, 'Log.created <=' => $lastdayyear)));

            $this->Session->write('smsreceivedweek', number_format($smsreceivedweek));
            $this->Session->write('smsreceivedmonth', number_format($smsreceivedmonth));
            $this->Session->write('smsreceivedyear', number_format($smsreceivedyear));

            //$smssentweek =  $this->Log->find('count', array('conditions' => array('Log.route' => 'outbox' ,'msg_type' => 'text','sms_status' => array('sent','delivered'),'DATE(Log.created) >=' =>$one_week_date,'DATE(Log.created) <=' =>$current_date)));
            //$smssentmonth =  $this->Log->find('count', array('conditions' => array('Log.route' => 'outbox' ,'msg_type' => 'text','sms_status' => array('sent','delivered'),'MONTH(Log.created)' =>$month,'YEAR(Log.created)' =>$year)));
            //$smssentyear =  $this->Log->find('count', array('conditions' => array('Log.route' => 'outbox' ,'msg_type' => 'text','sms_status' => array('sent','delivered'),'YEAR(Log.created)' =>$year)));

            $smssentweek = $this->Log->find('count', array('conditions' => array('Log.route' => 'outbox', 'msg_type' => 'text', 'sms_status' => array('sent', 'delivered', 'success'), 'Log.created >=' => $one_week_date_week, 'Log.created <=' => $current_date_week)));
            $smssentmonth = $this->Log->find('count', array('conditions' => array('Log.route' => 'outbox', 'msg_type' => 'text', 'sms_status' => array('sent', 'delivered', 'success'), 'Log.created >=' => $firstdaymonth, 'Log.created <=' => $lastdaymonth)));
            $smssentyear = $this->Log->find('count', array('conditions' => array('Log.route' => 'outbox', 'msg_type' => 'text', 'sms_status' => array('sent', 'delivered', 'success'), 'Log.created >=' => $firstdayyear, 'Log.created <=' => $lastdayyear)));

            $this->Session->write('smssentweek', number_format($smssentweek));
            $this->Session->write('smssentmonth', number_format($smssentmonth));
            $this->Session->write('smssentyear', number_format($smssentyear));

            $revenueweek = $this->Invoice->find('all', array('conditions' => array('DATE(Invoice.created) >=' => $one_week_date, 'DATE(Invoice.created) <=' => $current_date), 'fields' => array('sum(Invoice.amount) as total_sum')));
            $revenuemonth = $this->Invoice->find('all', array('conditions' => array('MONTH(Invoice.created)' => $month, 'YEAR(Invoice.created)' => $year), 'fields' => array('sum(Invoice.amount) as total_sum')));
            $revenueyear = $this->Invoice->find('all', array('conditions' => array('YEAR(Invoice.created)' => $year), 'fields' => array('sum(Invoice.amount) as total_sum')));
            $revenueall = $this->Invoice->find('all', array('fields' => array('sum(Invoice.amount) as total_sum')));

            $this->Session->write('revenueweek', number_format($revenueweek[0][0]['total_sum'], 2, '.', ','));
            $this->Session->write('revenuemonth', number_format($revenuemonth[0][0]['total_sum'], 2, '.', ','));
            $this->Session->write('revenueyear', number_format($revenueyear[0][0]['total_sum'], 2, '.', ','));
            $this->Session->write('revenueall', number_format($revenueall[0][0]['total_sum'], 2, '.', ','));


        }
    }

    function afterPaypalNotification($txnId)
    {
        //Here is where you can implement code to apply the transaction to your app.
        //for example, you could now mark an order as paid, a subscription, or give the user premium access.
        //retrieve the transaction using the txnId passed and apply whatever logic your site needs.
        $transaction = ClassRegistry::init('PaypalIpn.InstantPaymentNotification')->find('first', array('conditions' => array('id' => $txnId)));
        $this->log($transaction['InstantPaymentNotification']['id'], 'paypal');
        //Tip: be sure to check the payment_status is complete because failure
        //     are also saved to your database for review.
        if ($transaction['InstantPaymentNotification']['payment_status'] == 'Completed') {
            //Yay!  We have monies!
            $this->log('completed', 'paypal');
            $this->loadModel('User');
            //$this->User->id = $transaction['InstantPaymentNotification']['custom'];
            //$this->User->saveField('active', 1);
            //$this->User->saveField('sms_balance', FREE_SMS);
            //$this->User->saveField('voice_balance', FREE_VOICE);

            $this->request->data['User']['id'] = $transaction['InstantPaymentNotification']['custom'];
            $this->request->data['User']['sms_balance'] = FREE_SMS;
            $this->request->data['User']['voice_balance'] = FREE_VOICE;
            $this->request->data['User']['active'] = 1;
            $this->User->save($this->request->data);

            app::import('Model', 'Invoice');
            $this->Invoice = new Invoice();

            $pptxnid = $transaction['InstantPaymentNotification']['txn_id'];

            $InvoiceData = $this->Invoice->find('first', array('conditions' => array('Invoice.txnid' => $pptxnid)));

            if (empty($InvoiceData)) {

                $users = $this->getLoggedUserDetails();
                //$this->Session->delete('User');
                //$this->Session->write('User', $user['User']);

                $this->Session->write('User.sms_balance', $users['User']['sms_balance']);
                $this->Session->write('User.assigned_number', $users['User']['assigned_number']);
                $this->Session->write('User.pay_activation_fees_active', $users['User']['pay_activation_fees_active']);
                $this->Session->write('User.active', $users['User']['active']);

                $invoice['user_id'] = $transaction['InstantPaymentNotification']['custom'];
                $invoice['txnid'] = $pptxnid;
                $invoice['type'] = 0;
                $invoice['package_name'] = 'Activation Fee';
                $invoice['amount'] = $transaction['InstantPaymentNotification']['mc_gross'];
                $invoice['created'] = date("Y-m-d");
                $this->Invoice->save($invoice);

                $this->loadModel('Referral');
                $this->Referral->updateAll(array('Referral.account_activated' => 1), array('Referral.user_id' => $transaction['InstantPaymentNotification']['custom']));
                $this->sendActivationEmail($transaction['InstantPaymentNotification']['custom']);
            }


            //$this->redirect(array('controller' => 'users', 'action'=> 'profile'));


        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Pending') {
            $this->sendPayPalActivationPendingEmail($transaction['InstantPaymentNotification']['custom'], $transaction['InstantPaymentNotification']['payment_status'], $transaction['InstantPaymentNotification']['pending_reason']);

        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Failed') {
            $this->sendPayPalActivationFailingEmail($transaction['InstantPaymentNotification']['custom']);
        } else {
            //Oh no, better look at this transaction to determine what to do; like email a decline letter.
            $this->log('not completed', 'paypal');
        }

    }

    function sendActivationEmail($uid)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        /*$this->Email->to = $UserData['User']['email'];
        $this->Email->subject = 'Account has been activated';
        $this->Email->from = $sitename;
        $this->Email->template = 'membership'; // note no '.ctp'
        $this->Email->sendAs = 'html'; // because we like to send pretty mail
        $this->set('data', $UserData);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject('Account has been activated');
        $Email->template('membership');
        $Email->emailFormat('html');
        $Email->viewVars(array('data' => $UserData));
        $Email->send();

    }


    function afterPaypalNotificationCredit($txnId, $package_id)
    {
        $transaction = ClassRegistry::init('PaypalIpn.InstantPaymentNotification')->find('first', array('conditions' => array('id' => $txnId)));
        $this->log($transaction['InstantPaymentNotification']['id'], 'paypal');
        //Tip: be sure to check the payment_status is complete because failure
        //     are also saved to your database for review.

        $this->loadModel('Package');
        $package = $this->Package->find('first', array('conditions' => array('Package.id' => $package_id)));
        $package_name = $package['Package']['name'];

        if ($transaction['InstantPaymentNotification']['payment_status'] == 'Completed') {
       
            $this->log('creditcompleted', 'paypal');
            $this->log($package_id, 'paypal');
            $this->log($txnId, 'paypal');
            //$this->loadModel('Package');
            //$package = $this->Package->find('first', array('conditions' => array('Package.id' =>$package_id)));


            $this->loadModel('User');
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $transaction['InstantPaymentNotification']['custom'])));
            $this->User->id = $transaction['InstantPaymentNotification']['custom'];
            $this->User->saveField('active', 1);

            if ($package['Package']['type'] == 'text') {

                app::import('Model', 'Invoice');
                $this->Invoice = new Invoice();

                $pptxnid = $transaction['InstantPaymentNotification']['txn_id'];

                $InvoiceData = $this->Invoice->find('first', array('conditions' => array('Invoice.txnid' => $pptxnid)));

                if (empty($InvoiceData)) {
                    $sms_balance = $someone['User']['sms_balance'] + $package['Package']['credit'];
                    $this->User->saveField('sms_balance', $sms_balance);
                    $this->User->saveField('sms_credit_balance_email_alerts', 0);

                    $invoice['user_id'] = $transaction['InstantPaymentNotification']['custom'];
                    $invoice['txnid'] = $pptxnid;
                    $invoice['type'] = 0;
                    $invoice['package_name'] = $package_name;
                    $invoice['amount'] = $package['Package']['amount'];
                    $invoice['created'] = date("Y-m-d");
                    $this->Invoice->save($invoice);
                }

            } elseif ($package['Package']['type'] == 'voice') {

                app::import('Model', 'Invoice');
                $this->Invoice = new Invoice();

                $pptxnid = $transaction['InstantPaymentNotification']['txn_id'];

                $InvoiceData = $this->Invoice->find('first', array('conditions' => array('Invoice.txnid' => $pptxnid)));

                if (empty($InvoiceData)) {
                    $voice_balance = intval($someone['User']['voice_balance']) + intval($package['Package']['credit']);
                    $this->User->saveField('voice_balance', $voice_balance);
                    $this->User->saveField('VM_credit_balance_email_alerts', 0);

                    $invoice['user_id'] = $transaction['InstantPaymentNotification']['custom'];
                    $invoice['txnid'] = $transaction['InstantPaymentNotification']['txn_id'];
                    $invoice['type'] = 0;
                    $invoice['amount'] = $package['Package']['amount'];
                    $invoice['package_name'] = $package_name;
                    $invoice['created'] = date("Y-m-d");
                    $this->Invoice->save($invoice);
                }

            }
        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Pending') {
            $this->sendPayPalPendingEmail($transaction['InstantPaymentNotification']['custom'], $package_name, $transaction['InstantPaymentNotification']['payment_status'], $transaction['InstantPaymentNotification']['pending_reason']);

        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Failed') {
            $this->sendPayPalFailingEmail($transaction['InstantPaymentNotification']['custom'], $package_name);
        } else {
            //Oh no, better look at this transaction to determine what to do; like email a decline letter.
            $this->log('not completed', 'paypal');
        }
    }

    function statistic()
    {
        //$this->loadModel('Referral');
        app::import('Model', 'Referral');
        $this->Referral = new Referral();

        $referredUser = $this->Referral->find('count', array('conditions' => array('Referral.referred_by' => $this->Session->read('User.id'), 'Referral.account_activated' => 1)));

        $referredpaid = $this->Referral->find('count', array('conditions' => array('Referral.referred_by' => $this->Session->read('User.id'), 'Referral.paid_status' => 1)));

        $record = $this->Referral->find('all', array('conditions' => array('Referral.referred_by' => $this->Session->read('User.id'), 'Referral.account_activated' => 1, 'Referral.paid_status' => 1), 'fields' => "SUM(amount) as total"));
        $overAllCredit = $record['0']['0']['total'];

        $invoice = $this->Referral->find('all', array('conditions' => array('Referral.referred_by' => $this->Session->read('User.id'), 'Referral.account_activated' => 1, 'Referral.paid_status' => 0, "Referral.created <" => date('Y-m-d', strtotime("next saturday"))), 'fields' => "SUM(amount) as commission"));
        return array('referredUser' => $referredUser, 'referredpaid' => $referredpaid, 'overAllCredit' => number_format($overAllCredit, 2), 'unPaidCommision' => number_format($invoice['0']['0']['commission'], 2));
    }


    function afterPaypalNotificationSubscribe($txnId, $package_id)
    {
        $transaction = ClassRegistry::init('PaypalIpn.InstantPaymentNotification')->find('first', array('conditions' => array('id' => $txnId)));
        $this->log($transaction['InstantPaymentNotification']['id'], 'paypal');
        //Tip: be sure to check the payment_status is complete because failure
        //     are also saved to your database for review.

        $this->loadModel('MonthlyPackage');
        $monthlypackage = $this->MonthlyPackage->find('first', array('conditions' => array('MonthlyPackage.id' => $package_id)));
        $package_name = $monthlypackage['MonthlyPackage']['package_name'];

        if ($transaction['InstantPaymentNotification']['payment_status'] == 'Completed') {
            $this->log('creditcompleted', 'paypal');
            $this->log($package_id, 'paypal');
            $this->log($txnId, 'paypal');

            $this->loadModel('User');
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $transaction['InstantPaymentNotification']['custom'])));

            $firstsubpayment = $this->User->find('first', array('conditions' => array('User.recurring_paypal_email' => $transaction['InstantPaymentNotification']['payer_email'])));
            $this->User->id = $transaction['InstantPaymentNotification']['custom'];
            $recurring_email = $transaction['InstantPaymentNotification']['payer_email'];
            $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));

            $this->request->data['User']['active'] = 1;
            $this->request->data['User']['package'] = $package_id;
            $this->request->data['User']['next_renewal_dates'] = $nextdate;
            $this->request->data['User']['recurring_paypal_email'] = $recurring_email;
            
            if($monthlypackage['MonthlyPackage']['credits_carryover'] == 1){
                $this->request->data['User']['sms_balance'] = $someone['User']['sms_balance'] + $monthlypackage['MonthlyPackage']['text_messages_credit'];
                $this->request->data['User']['voice_balance'] = $someone['User']['voice_balance'] + $monthlypackage['MonthlyPackage']['voice_messages_credit'];
            }else{
                $this->request->data['User']['sms_balance'] = $monthlypackage['MonthlyPackage']['text_messages_credit'];
                $this->request->data['User']['voice_balance'] = $monthlypackage['MonthlyPackage']['voice_messages_credit']; 
            }
            $this->request->data['User']['sms_credit_balance_email_alerts'] = 0;
            $this->request->data['User']['VM_credit_balance_email_alerts'] = 0;
            
            //***Set user permissions based on credit package
            $this->request->data['User']['autoresponders'] = $monthlypackage['MonthlyPackage']['autoresponders'];
            $this->request->data['User']['importcontacts'] = $monthlypackage['MonthlyPackage']['importcontacts'];
            $this->request->data['User']['shortlinks'] = $monthlypackage['MonthlyPackage']['shortlinks'];
            $this->request->data['User']['voicebroadcast'] = $monthlypackage['MonthlyPackage']['voicebroadcast'];
            $this->request->data['User']['polls'] = $monthlypackage['MonthlyPackage']['polls'];
            $this->request->data['User']['contests'] = $monthlypackage['MonthlyPackage']['contests'];
            $this->request->data['User']['loyaltyprograms'] = $monthlypackage['MonthlyPackage']['loyaltyprograms'];
            $this->request->data['User']['kioskbuilder'] = $monthlypackage['MonthlyPackage']['kioskbuilder'];
            $this->request->data['User']['birthdaywishes'] = $monthlypackage['MonthlyPackage']['birthdaywishes'];
            $this->request->data['User']['mobilepagebuilder'] = $monthlypackage['MonthlyPackage']['mobilepagebuilder'];
            $this->request->data['User']['webwidgets'] = $monthlypackage['MonthlyPackage']['webwidgets'];
            $this->request->data['User']['smschat'] = $monthlypackage['MonthlyPackage']['smschat'];
            $this->request->data['User']['qrcodes'] = $monthlypackage['MonthlyPackage']['qrcodes'];
            $this->request->data['User']['calendarscheduler'] = $monthlypackage['MonthlyPackage']['calendarscheduler'];
            $this->request->data['User']['appointments'] = $monthlypackage['MonthlyPackage']['appointments'];
            $this->request->data['User']['groups'] = $monthlypackage['MonthlyPackage']['groups'];
            $this->request->data['User']['contactlist'] = $monthlypackage['MonthlyPackage']['contactlist'];
            $this->request->data['User']['sendsms'] = $monthlypackage['MonthlyPackage']['sendsms'];
            $this->request->data['User']['affiliates'] = $monthlypackage['MonthlyPackage']['affiliates'];
            $this->request->data['User']['getnumbers'] = $monthlypackage['MonthlyPackage']['getnumbers'];
            $this->request->data['User']['mobilecoupons'] = $monthlypackage['MonthlyPackage']['mobilecoupons'];
            $this->request->data['User']['qabots'] = $monthlypackage['MonthlyPackage']['qabots'];
                
            $this->User->save($this->request->data);

            app::import('Model', 'Referral');
            $this->Referral = new Referral();

            if (!empty($firstsubpayment)) {
                if ($firstsubpayment['User']['active'] == 1) {
                    $referraldetails = $this->Referral->find('all', array('conditions' => array('Referral.referred_by' => $firstsubpayment['User']['id'])));
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
            } else {

                $Referraldetails = $this->Referral->find('first', array('conditions' => array('Referral.user_id' => $transaction['InstantPaymentNotification']['custom'])));
                if (!empty($Referraldetails)) {
                    $referral['id'] = $Referraldetails['Referral']['id'];
                    $referral['account_activated'] = 1;
                    $this->Referral->save($referral);
                }
            }
            app::import('Model', 'Invoice');
            $this->Invoice = new Invoice();
            $invoice['user_id'] = $transaction['InstantPaymentNotification']['custom'];
            $invoice['amount'] = $monthlypackage['MonthlyPackage']['amount'];
            $invoice['txnid'] = $transaction['InstantPaymentNotification']['txn_id'];
            $invoice['type'] = 0;
            $invoice['package_name'] = $package_name;
            $invoice['created'] = date("Y-m-d");
            $this->Invoice->save($invoice);

            $this->Session->write('User.active', 1);
            $this->Session->write('User.package', $package_id);
            //$this->Session->setFlash(__( $package_name. '  package is activated.', true));
        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Pending') {
            $this->sendPayPalPendingEmail($transaction['InstantPaymentNotification']['custom'], $package_name, $transaction['InstantPaymentNotification']['payment_status'], $transaction['InstantPaymentNotification']['pending_reason']);

        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Failed') {
            $this->sendPayPalFailingEmail($transaction['InstantPaymentNotification']['custom'], $package_name);

        } else if ($transaction['InstantPaymentNotification']['txn_type'] == 'subscr_cancel') {
            $this->loadModel('User');
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $transaction['InstantPaymentNotification']['custom'])));
            $this->User->id = $transaction['InstantPaymentNotification']['custom'];
            //$this->request->data['User']['active'] = 0;
            $this->request->data['User']['package'] = 0;
            $this->request->data['User']['next_renewal_dates'] = '';
            $this->request->data['User']['sms_credit_balance_email_alerts'] = 0;
            $this->request->data['User']['VM_credit_balance_email_alerts'] = 0;
            
            //***Go back to default user permissions when they cancel their subscription
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
            
            $this->User->save($this->request->data);
            $this->sendSubscriptionCancelEmail($transaction['InstantPaymentNotification']['custom']);
        } else {
            //Oh no, better look at this transaction to determine what to do; like email a decline letter.
            $this->log('not completed', 'paypal');
        }
    }

    function afterPaypalNotificationSubscribenumbers($txnId, $package_id)
    {
        $transaction = ClassRegistry::init('PaypalIpn.InstantPaymentNotification')->find('first', array('conditions' => array('id' => $txnId)));
        $this->log($transaction['InstantPaymentNotification']['id'], 'paypal');
        //Tip: be sure to check the payment_status is complete because failure
        // are also saved to your database for review.

        $this->loadModel('MonthlyNumberPackage');
        $monthlynumberpackage = $this->MonthlyNumberPackage->find('first', array('conditions' => array('MonthlyNumberPackage.id' => $package_id)));
        $package_name = $monthlynumberpackage['MonthlyNumberPackage']['package_name'];

        if ($transaction['InstantPaymentNotification']['payment_status'] == 'Completed') {
            //Yay!  We have monies!
            $this->log('creditcompleted', 'paypal');
            $this->log($package_id, 'paypal');
            $this->log($txnId, 'paypal');
            $this->loadModel('User');
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $transaction['InstantPaymentNotification']['custom'])));
            $number_limit_set = $someone['User']['number_limit_set'];

            $this->User->id = $transaction['InstantPaymentNotification']['custom'];
            $nextdate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
            $this->request->data['User']['number_next_renewal_dates'] = $nextdate;
            $this->request->data['User']['id'] = $transaction['InstantPaymentNotification']['custom'];
            $this->request->data['User']['active'] = 1;

            if ($number_limit_set == 0) {

                $this->request->data['User']['number_limit'] = $someone['User']['number_limit'] + $monthlynumberpackage['MonthlyNumberPackage']['total_secondary_numbers'];
                $this->request->data['User']['number_package'] = $package_id;
                $this->request->data['User']['number_limit_set'] = 1;

            }
            $this->User->save($this->request->data);

            app::import('Model', 'Invoice');
            $this->Invoice = new Invoice();
            $invoice['user_id'] = $transaction['InstantPaymentNotification']['custom'];
            $invoice['amount'] = $monthlynumberpackage['MonthlyNumberPackage']['amount'];
            $invoice['txnid'] = $transaction['InstantPaymentNotification']['txn_id'];
            $invoice['type'] = 0;
            $invoice['package_name'] = $package_name;
            $invoice['created'] = date("Y-m-d");
            $this->Invoice->save($invoice);
            $this->Session->write('User.number_package', $package_id);
        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Pending') {
            $this->sendPayPalPendingEmail($transaction['InstantPaymentNotification']['custom'], $package_name, $transaction['InstantPaymentNotification']['payment_status'], $transaction['InstantPaymentNotification']['pending_reason']);
        } else if ($transaction['InstantPaymentNotification']['payment_status'] == 'Failed') {
            $this->sendPayPalFailingEmail($transaction['InstantPaymentNotification']['custom'], $package_name);
        } else if ($transaction['InstantPaymentNotification']['txn_type'] == 'subscr_cancel') {
            $this->loadModel('User');
            $someone = $this->User->find('first', array('conditions' => array('User.id' => $transaction['InstantPaymentNotification']['custom'])));
            $this->User->id = $transaction['InstantPaymentNotification']['custom'];
            $this->request->data['User']['number_limit'] = $someone['User']['number_limit'] - $monthlynumberpackage['MonthlyNumberPackage']['total_secondary_numbers'];
            $this->request->data['User']['number_package'] = 0;
            $this->request->data['User']['number_next_renewal_dates'] = '';
            $this->request->data['User']['number_limit_set'] = 0;
            $this->request->data['User']['active'] = 0;
            $this->User->save($this->request->data);
            $this->sendSubscriptionNumberCancelEmail($transaction['InstantPaymentNotification']['custom']);
        } else {
            //Oh no, better look at this transaction to determine what to do; like email a decline letter.
            $this->log('not completed', 'paypal');
        }
    }

    function sendSubscriptionCancelEmail($uid)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        $subject = "Your PayPal subscription with " . SITENAME . " has been canceled";
        /*$this->Email->subject = $subject;
        $this->Email->from = $sitename;
        $this->Email->to = $UserData['User']['email'];
        $this->Email->template = 'paypal_subscription_cancel';
        $this->Email->sendAs = 'html';
        $this->set('data', $UserData);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject($subject);
        $Email->template('paypal_subscription_cancel');
        $Email->emailFormat('html');
        $Email->viewVars(array('data' => $UserData));
        $Email->send();
    }

    function sendSubscriptionNumberCancelEmail($uid)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        $subject = "Your PayPal subscription with " . SITENAME . " has been canceled";
        /*$this->Email->subject = $subject;
        $this->Email->from = $sitename;
        $this->Email->to = $UserData['User']['email'];
        $this->Email->template = 'paypal_number_subscription_cancel';
        $this->Email->sendAs = 'html';
        $this->set('data', $UserData);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject($subject);
        $Email->template('paypal_number_subscription_cancel');
        $Email->emailFormat('html');
        $Email->viewVars(array('data' => $UserData));
        $Email->send();
    }

    function sendPayPalPendingEmail($uid, $packagename, $status, $reason)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        $subject = "Your PayPal payment with " . SITENAME . " is Pending";
        /*$this->Email->subject = $subject;
        $this->Email->from = $sitename;
        $this->Email->to = $UserData['User']['email'];
        $this->Email->template = 'paypal_status_pending';
        $this->Email->sendAs = 'html';*/
        $first_name = $UserData['User']['first_name'];
        /*$this->Email->Controller->set('firstname', $first_name);
        $this->Email->Controller->set('packagename', $packagename);
        $this->Email->Controller->set('status', $status);
        $this->Email->Controller->set('reason', $reason);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject($subject);
        $Email->template('paypal_status_pending');
        $Email->emailFormat('html');
        $Email->viewVars(array('firstname' => $first_name));
        $Email->viewVars(array('packagename' => $packagename));
        $Email->viewVars(array('status' => $status));
        $Email->viewVars(array('reason' => $reason));
        $Email->send();
    }

    function sendPayPalFailingEmail($uid, $packagename)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        $subject = "Your PayPal payment with " . SITENAME . " has failed";
        /*$this->Email->subject = $subject;
        $this->Email->from = $sitename;
        $this->Email->to = $UserData['User']['email'];
        $this->Email->template = 'paypal_status_failed';
        $this->Email->sendAs = 'html';*/
        $first_name = $UserData['User']['first_name'];
        /*$this->Email->Controller->set('firstname', $first_name);
        $this->Email->Controller->set('packagename', $packagename);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject($subject);
        $Email->template('paypal_status_failed');
        $Email->emailFormat('html');
        $Email->viewVars(array('firstname' => $first_name));
        $Email->viewVars(array('packagename' => $packagename));
        $Email->send();

    }

    function sendPayPalActivationPendingEmail($uid, $status, $reason)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        $subject = "Your PayPal payment with " . SITENAME . " is Pending";
        /*$this->Email->subject = $subject;
        $this->Email->from = $sitename;
        $this->Email->to = $UserData['User']['email'];
        $this->Email->template = 'paypal_activation_status_pending';
        $this->Email->sendAs = 'html';*/
        $first_name = $UserData['User']['first_name'];
        /*$this->Email->Controller->set('firstname', $first_name);
        $this->Email->Controller->set('status', $status);
        $this->Email->Controller->set('reason', $reason);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject($subject);
        $Email->template('paypal_activation_status_pending');
        $Email->emailFormat('html');
        $Email->viewVars(array('firstname' => $first_name));
        $Email->viewVars(array('status' => $status));
        $Email->viewVars(array('reason' => $reason));
        $Email->send();
    }

    function sendPayPalActivationFailingEmail($uid)
    {
        $this->loadModel('User');
        $UserData = $this->User->find('first', array('conditions' => array('User.id' => $uid)));
        $sitename = str_replace(' ', '', SITENAME);
        $subject = "Your PayPal payment with " . SITENAME . " has failed";
        /*$this->Email->subject = $subject;
        $this->Email->from = $sitename;
        $this->Email->to = $UserData['User']['email'];
        $this->Email->template = 'paypal_activation_status_failed';
        $this->Email->sendAs = 'html';*/
        $first_name = $UserData['User']['first_name'];
        /*$this->Email->Controller->set('firstname', $first_name);
        $this->Email->send();*/
        
        $Email = new CakeEmail();
        if(EMAILSMTP==1){
            $Email->config('smtp');
        }
        $Email->from(array(SUPPORT_EMAIL => SITENAME));
        $Email->to($UserData['User']['email']);
        $Email->subject($subject);
        $Email->template('paypal_activation_status_failed');
        $Email->emailFormat('html');
        $Email->viewVars(array('firstname' => $first_name));
        $Email->send();

    }

    function choosecolor()
    {
        $this->autoRender = false;
        $color = array('#1abc9c', '#3498db', '#9b59b6', '#34495e', '#16a085', '#f1c40f', '#27ae60', '#e67e22', '#f39c12', '#d35400', '#2980b9', '#8e44ad', '#2c3e50', '#e74c3c', '#a71065', '#c0392b', '#ff6a28', '#7f8c8d', '#0a8351', '#D91E18', '#32c5d2', '#16565c', '#d0006c');
        $k = array_rand($color);
        $firstcolor = $color[$k];
        return $firstcolor;
    }

    function validateNumber($phone_number)
    {
        $this->autoRender = false;
        $access_key = NUMVERIFY;
        $url = NUMVERIFYURL . '?access_key=' . $access_key . '&number=' . $phone_number;
        // Initialize CURL:
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        // Decode JSON response:
        $validationResult = json_decode($json, true);
        return $validationResult;
        //print_r($validationResult['valid']);
        //print_r($validationResult['carrier']);
        //print_r($validationResult['line_type']);
        //print_r($validationResult['location']);

    }
    
    function is_gsm($instring){
        $valid_gsm_keycodes = array('@','Δ',' ','0','¡','P','¿','p','£','_','!','1','A','Q','a','q','$','Φ','"','2','B','R','b','r','¥','Γ','#','3','C','S','c','s','è','Λ','¤','4','D','T','d','t','é','Ω','%','5','E','U','e','u','ù','Π','&','6','F','V','f','v','ì','Ψ','\\','7','G','W','g','w','ò','Σ','(','8','H','X','h','x','Ç','Θ',')','9','I','Y','i','y',"\n",'Ξ','*',':','J','Z','j','z','Ø',"\x1B",'+',';','K','Ä','k','ä','ø','Æ',',','<','L','Ö','l','ö',"\r",'æ','-','=','M','Ñ','m','ñ','Å','ß','.','>','N','Ü','n','ü','å','É','/','?','O','§','o','à','^','{','}','~','`','[',']','€','|',"'");
     
    	$len = mb_strlen($instring,"UTF-8");
    	
    	for($i = 0; $i < $len; $i++) {
            if(!in_array(mb_substr($instring,$i,1), $valid_gsm_keycodes)){
		        return false;
            }
        }

        return true;
    }
    
    function smsmail($user_id = null){
        $this->autoRender = false;
        app::import('Model', 'User');
        $this->User = new User();
        $this->User->recursive = -1;
        $usersmail = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
        if ($usersmail['User']['email_alert_credit_options'] == 0) {
            if ($usersmail['User']['sms_balance'] <= $usersmail['User']['low_sms_balances']) {
                if ($usersmail['User']['sms_credit_balance_email_alerts'] == 0) {
                    $sitename = str_replace(' ', '', SITENAME);
                    $username = $usersmail['User']['username'];
                    $email = $usersmail['User']['email'];
                    $subject = "Low SMS Credit Balance";
                    
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
}//class
