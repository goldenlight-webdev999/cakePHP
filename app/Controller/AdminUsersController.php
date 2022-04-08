<?php
class AdminUsersController extends AppController
{
    var $name = 'AdminUsers';
    var $helpers = array('Html', 'Form');
    var $components = array('Session');
    var $layout = 'admin';

    function index()
    {
        if (!$this->Session->check('AdminUser')) {
            $this->Session->setFlash('Please login first');
            $this->redirect('/admin/');
            return;
        }
    }

    function login()
    {
        if (!empty($this->request->data)) {
            
            $someone = $this->AdminUser->find('first', array('conditions' => array('username' => $this->request->data['username'])));
            
            if (!empty($someone['AdminUser']['password']) && $someone['AdminUser']['password'] == md5($this->request->data['password'])) {
                $this->Session->write('AdminUser', $someone['AdminUser']);
                $this->redirect('/admin_users/index');
            } else {
                // Remember the $error var in the view? Let's set that to true:
                $this->set('error', true);
                $this->Session->setFlash('Username or Password Wrong');
                $this->redirect('/admin/');
            }
        }
    }

    function change_password()
    {
        if (!empty($this->request->data)) {
            if (empty($this->request->data['AdminUser']['New Password'])) {
                $this->Session->setFlash('Please fill new password');
                return;
            }
            $someone = $this->AdminUser->find('first', array('conditions' => array('username' => $this->request->data['AdminUser']['username'])));
            if ($someone['AdminUser']['password'] == md5($this->request->data['AdminUser']['Old password'])) {
                if ($this->request->data['AdminUser']['New Password Again'] != $this->request->data['AdminUser']['New Password']) {
                    $this->Session->setFlash('New password does not match. Please enter new password again');
                    return;
                }
                $someone['AdminUser']['password'] = $this->request->data['AdminUser']['New Password'];
                if ($this->AdminUser->save($someone['AdminUser'])) {
                    $this->Session->setFlash('Password changed successfully');
                    $this->redirect('/admin_users/index');
                } else {
                    $this->Session->setFlash('Error in changing password');
                }
            } else {
                $this->Session->setFlash('Old password does not match current password');
            }
        }
    }

    function logout()
    {
        $this->Session->delete('AdminUser');
        $this->Session->delete('adminstats');
        $this->Session->delete('activeclientsweek');
        $this->Session->delete('activeclientsmonth');
        $this->Session->delete('activeclientsyear');
        $this->Session->delete('activeclientsall');
        $this->Session->delete('smsreceivedweek');
        $this->Session->delete('smsreceivedmonth');
        $this->Session->delete('smsreceivedyear');
        $this->Session->delete('smsreceivedall');
        $this->Session->delete('smssentweek');
        $this->Session->delete('smssentmonth');
        $this->Session->delete('smssentyear');
        $this->Session->delete('smssentall');
        $this->Session->delete('revenueweek');
        $this->Session->delete('revenuemonth');
        $this->Session->delete('revenueyear');
        $this->Session->delete('revenueall');

        $this->Session->setFlash('You have successfully logged out');
        $this->redirect('/admin');
    }
}

?>