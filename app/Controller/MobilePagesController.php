<?php

class MobilePagesController extends AppController
{
    var $name = 'MobilePages';
    var $components = array('Cookie', 'Qr');
    var $helpers = array('Time', 'Form', 'Html', 'Session', 'Fck');
    var $layout = "default";

    function pagedetails()
    {
        if ($this->Session->check('User')) {
            $this->layout = 'admin_new_layout';
            $user_id = $this->Session->read('User.id');
            $phoneno = $this->MobilePage->find('all', array('conditions' => array('MobilePage.user_id' => $user_id), 'order' => array('MobilePage.id' => 'asc')));
            $this->set('mobilespages', $phoneno);
        } else {
            $this->redirect('/users/login');
        }

    }

    function upload()
    {
        $this->layout = null;
        $filename = $_FILES['data']['name']['image'];
        $type = $_FILES['data']['type']['image'];
        $tmp_name = $_FILES['data']['tmp_name']['image'];
        
        if (!empty($filename)) {
            if(trim($type)=='image/png' || trim($type)=='image/jpeg' || trim($type)=='image/gif'){
                $time = time();
                move_uploaded_file($tmp_name, "uploadimages/" . $time . $filename);
                $siteurl = SITE_URL;
                $imagepath = $siteurl . '/uploadimages/' . $time . $filename;
                $this->set('imagename', $imagepath);
            }
        }
    }

    function add()
    {
        if ($this->Session->check('User')) {
            $this->layout = 'admin_new_layout';
            $user_id = $this->Session->read('User.id');
            if (!empty($this->request->data)) {
                $countpage = $this->MobilePage->find('count', array('conditions' => array('MobilePage.user_id' => $user_id)));
                //echo $countpage;
                $pagelimit = MOBILE_PAGE_LIMIT;
                if ($countpage < $pagelimit) {
                    app::import('Model', 'MobilePage');
                    $this->MobilePage = new MobilePage();
                    $filename = $this->request->data['MobilePage']['header_logo']['name'];
                    $type = $this->request->data['MobilePage']['header_logo']['type'];
                    $tmp_name = $this->request->data['MobilePage']['header_logo']['tmp_name'];
                    $time = time();
                    if (!empty($filename)) {
                        $extension = strtolower(pathinfo($this->request->data['MobilePage']['header_logo']['name'], PATHINFO_EXTENSION));
						$extension_arr = array('jpg','jpeg','png','gif');
						if (!in_array($extension, $extension_arr)){
							$this->Session->setFlash(__('Please select image file type only for the header image', true));
							$this->redirect(array('action'=>'pagedetails'));
						}else{
                            move_uploaded_file($tmp_name, "pages/" . $time . $filename);
                            $this->request->data['MobilePage']['header_logo'] = $time . $filename;
                        }
                    } else {
                        $this->request->data['MobilePage']['header_logo'] = '';
                    }
                    $this->request->data['MobilePage']['user_id'] = $user_id;
                    $this->request->data['MobilePage']['title'] = $this->request->data['MobilePage']['title'];
                    $this->request->data['MobilePage']['description'] = $this->request->data['MobilePage']['description'];
                    $this->request->data['MobilePage']['header_color'] = $this->request->data['MobilePage']['header_color'];
                    $this->request->data['MobilePage']['headerfont_color'] = $this->request->data['MobilePage']['headerfont_color'];
                    $this->request->data['MobilePage']['bodybg_color'] = $this->request->data['MobilePage']['bodybg_color'];
                    $this->request->data['MobilePage']['footertext_color'] = $this->request->data['MobilePage']['footertext_color'];
                    $this->request->data['MobilePage']['footertext'] = $this->request->data['MobilePage']['footertext'];
                    $this->request->data['MobilePage']['footer_color'] = $this->request->data['MobilePage']['footer_color'];
                    $this->request->data['MobilePage']['map_url'] = $this->request->data['MobilePage']['mapurl'];
                    $this->MobilePage->save($this->request->data);
                    $this->Session->setFlash(__('The MobilePage has been saved', true));
                    $this->redirect(array('action' => 'pagedetails'));
                } else {
                    $this->Session->setFlash(__('You are already at the limit to create a page', true));
                    $this->redirect(array('action' => 'pagedetails'));
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
            app::import('Model', 'MobilePage');
            $this->MobilePage = new MobilePage();
            $user_id = $this->Session->read('User.id');
            $page = $this->MobilePage->find('first', array('conditions' => array('MobilePage.id' => $id, 'MobilePage.user_id' => $user_id), 'order' => array('MobilePage.id' => 'asc')));
            $this->set('pagedetails', $page);
            if (!empty($this->request->data)) {
                $time = time();
                $pagedetails = $this->MobilePage->find('first', array('conditions' => array('MobilePage.id' => $this->request->data['MobilePage']['id'], 'MobilePage.user_id' => $user_id), 'order' => array('MobilePage.id' => 'asc')));
                $filename = $this->request->data['MobilePage']['header_logo']['name'];
                $type = $this->request->data['MobilePage']['header_logo']['type'];
                $tmp_name = $this->request->data['MobilePage']['header_logo']['tmp_name'];
                if (!empty($filename)) {
                    $extension = strtolower(pathinfo($this->request->data['MobilePage']['header_logo']['name'], PATHINFO_EXTENSION));
					$extension_arr = array('jpg','jpeg','png','gif');
					if (!in_array($extension, $extension_arr)){
						$this->Session->setFlash(__('Please select image file type only for the header image', true));
						$this->redirect(array('action'=>'pagedetails'));
					}else{
                        move_uploaded_file($tmp_name, "pages/" . $time . $filename);
                        $this->request->data['MobilePage']['header_logo'] = $time . $filename;
					}
                } else {
                    $this->request->data['MobilePage']['header_logo'] = '';
                }
                $this->request->data['MobilePage']['id'] = $this->request->data['MobilePage']['id'];
                $this->request->data['MobilePage']['user_id'] = $user_id;
                $this->request->data['MobilePage']['title'] = $this->request->data['MobilePage']['title'];
                $this->request->data['MobilePage']['description'] = $this->request->data['MobilePage']['description'];
                $this->request->data['MobilePage']['header_color'] = $this->request->data['MobilePage']['header_color'];
                $this->request->data['MobilePage']['headerfont_color'] = $this->request->data['MobilePage']['headerfont_color'];
                $this->request->data['MobilePage']['bodybg_color'] = $this->request->data['MobilePage']['bodybg_color'];
                $this->request->data['MobilePage']['footertext_color'] = $this->request->data['MobilePage']['footertext_color'];
                $this->request->data['MobilePage']['footertext'] = $this->request->data['MobilePage']['footertext'];
                $this->request->data['MobilePage']['footer_color'] = $this->request->data['MobilePage']['footer_color'];
                $this->request->data['MobilePage']['map_url'] = $this->request->data['MobilePage']['mapurl'];
                $this->MobilePage->save($this->request->data);
                $this->Session->setFlash(__('The MobilePage has been edited', true));
                $this->redirect(array('action' => 'pagedetails'));
            }
        } else {
            $this->redirect('/users/login');
        }
    }

    function delete($id = null)
    {
        app::import('Model', 'MobilePage');
        $this->MobilePage = new MobilePage();
        if ($this->MobilePage->delete($id)) {
            $this->Session->setFlash(__('MobilePage deleted', true));
            $this->redirect(array('action' => 'pagedetails'));
        }
    }

    function view($id)
    {
        if ($this->Session->check('User')) {
            app::import('Model', 'Qrcod');
            $this->Qrcod = new Qrcod();
            app::import('Model', 'MobilePage');
            $this->MobilePage = new MobilePage();
            $user_id = $this->Session->read('User.id');
            $viewpage = $this->MobilePage->find('first', array('conditions' => array('MobilePage.id' => $id, 'MobilePage.user_id' => $user_id), 'order' => array('MobilePage.id' => 'asc')));
            $this->set('pageview', $viewpage);
        } else {
            $this->redirect('/users/login');
        }
    }

    function qrcodeview($id = null)
    {
        if ($this->Session->check('User')) {
            $this->layout = 'admin_new_layout';
            $user_id = $this->Session->read('User.id');
            app::import('Model', 'MobilePage');
            $this->MobilePage = new MobilePage();
            $viewpage = $this->MobilePage->find('first', array('conditions' => array('MobilePage.id' => $id, 'MobilePage.user_id' => $user_id)));
            if (!empty($viewpage)) {
                $base64 = base64_encode($viewpage['MobilePage']['id']);
                $changeid = str_replace('=', '', $base64);
                $pageid = str_replace('+', '', $changeid);
                $url = SITE_URL;
                $test = $pageid;
                $page = $url . '/lp.php?id=' . $pageid;
                $p = 'pagesqr/' . $test . '-large.png';
                copy('test/test.png', $p);
                QRcode::png($page, 'pagesqr/' . $test . '-large.png', 'L', 8, 2);
                $this->set('qrimage1', $url . '/pagesqr/' . $test . '-large.png');
            }
        } else {
            $this->redirect('/users/login');
        }
    }
}