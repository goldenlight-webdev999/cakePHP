<?php
App::uses('CakeEmail', 'Network/Email');
class MobilecouponsController extends AppController {
	var $name = 'Mobilecoupons';	
	var $components = array('Twilio', 'Mms', 'Nexmomessage', 'Slooce', 'Plivo', 'Bandwidth', 'Signalwire', 'Ytel','Qrsms');
	public $uses = array('ContactGroup','User','Contact','MobileCoupon','MobileCouponAnalytics');
	function index(){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');	
			$this->paginate = array('conditions' => array('MobileCoupon.user_id' =>$user_id),'order' =>array('MobileCoupon.id' => 'DESC'));
			$mobilecoupon_arr = $this->paginate('MobileCoupon');
			$this->set('mobilecoupon_arr',$mobilecoupon_arr);
		} else {
            $this->redirect('/users/login');
        }
	}
	
    function add(){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $timezone = $users['User']['timezone'];
            date_default_timezone_set($timezone);
			app::import('Model', 'Group');
			if(!empty($this->request->data)){
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$extension = strtolower(pathinfo($this->request->data['MobileCoupon']['offer_image']['name'], PATHINFO_EXTENSION));
						$extension_arr = array('jpg','jpeg','png','gif');
						if (!in_array($extension, $extension_arr)){
							$this->Session->setFlash(__('Please select image file type only for the coupon image', true));
							$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'add'));
						}
					}
				}
				$mobilecoupon_arr['MobileCoupon']['id'] = '';
				$mobilecoupon_arr['MobileCoupon']['unique_id'] = $this->unique_code(8);
				$mobilecoupon_arr['MobileCoupon']['user_id'] = $this->Session->read('User.id');
				$mobilecoupon_arr['MobileCoupon']['name'] = $this->request->data['MobileCoupon']['name'];
				$mobilecoupon_arr['MobileCoupon']['description'] = $this->request->data['MobileCoupon']['description'];
				$mobilecoupon_arr['MobileCoupon']['one_per_person'] = $this->request->data['MobileCoupon']['one_per_person'];
				$mobilecoupon_arr['MobileCoupon']['redemption_reset'] = $this->request->data['MobileCoupon']['redemption_reset'];
				$mobilecoupon_arr['MobileCoupon']['expiration_date'] = $this->request->data['MobileCoupon']['expiration_date'];
				$mobilecoupon_arr['MobileCoupon']['coupon_expiration'] = $this->request->data['MobileCoupon']['coupon_expiration'];
                $mobilecoupon_arr['MobileCoupon']['dynamic_days'] = $this->request->data['MobileCoupon']['dynamic_days'];
				$mobilecoupon_arr['MobileCoupon']['showVoteContainer'] = $this->request->data['MobileCoupon']['showVoteContainer'];
				$mobilecoupon_arr['MobileCoupon']['coupon_colorway'] = $this->request->data['MobileCoupon']['coupon_colorway'];
				$mobilecoupon_arr['MobileCoupon']['coupon_header'] = $this->request->data['MobileCoupon']['coupon_header'];
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$filename = time().$this->request->data['MobileCoupon']['offer_image']['name'];
						move_uploaded_file($this->request->data['MobileCoupon']['offer_image']['tmp_name'], "mobile_coupons/".$filename);
						$mobilecoupon_arr['MobileCoupon']['offer_image'] = $filename;
					}
				}
				$mobilecoupon_arr['MobileCoupon']['offer_name'] = $this->request->data['MobileCoupon']['offer_name'];
				$mobilecoupon_arr['MobileCoupon']['offer_description'] = $this->request->data['MobileCoupon']['offer_description'];
				$mobilecoupon_arr['MobileCoupon']['redeem_button_text'] = $this->request->data['MobileCoupon']['redeem_button_text'];
				$mobilecoupon_arr['MobileCoupon']['fine_print'] = $this->request->data['MobileCoupon']['fine_print'];
				$mobilecoupon_arr['MobileCoupon']['showCallLink'] = $this->request->data['MobileCoupon']['showCallLink'];
				$mobilecoupon_arr['MobileCoupon']['call_number'] = $this->request->data['MobileCoupon']['call_number'];
				$mobilecoupon_arr['MobileCoupon']['showWebsiteLink'] = $this->request->data['MobileCoupon']['showWebsiteLink'];
				$mobilecoupon_arr['MobileCoupon']['website'] = $this->request->data['MobileCoupon']['website'];
				$mobilecoupon_arr['MobileCoupon']['showFacebookLink'] = $this->request->data['MobileCoupon']['showFacebookLink'];
				$mobilecoupon_arr['MobileCoupon']['facebook'] = $this->request->data['MobileCoupon']['facebook'];
				$mobilecoupon_arr['MobileCoupon']['showInstagramLink'] = $this->request->data['MobileCoupon']['showInstagramLink'];
				$mobilecoupon_arr['MobileCoupon']['instagram'] = $this->request->data['MobileCoupon']['instagram'];
				$mobilecoupon_arr['MobileCoupon']['showDirectionsLink'] = $this->request->data['MobileCoupon']['showDirectionsLink'];
				$mobilecoupon_arr['MobileCoupon']['directions'] = $this->request->data['MobileCoupon']['directions'];
				$mobilecoupon_arr['MobileCoupon']['redemption_type'] = $this->request->data['MobileCoupon']['redemption_type'];
				$mobilecoupon_arr['MobileCoupon']['Redemption_Action'] = $this->request->data['MobileCoupon']['Redemption_Action'];
				$mobilecoupon_arr['MobileCoupon']['redemption_code'] = $this->request->data['MobileCoupon']['redemption_code'];
				$mobilecoupon_arr['MobileCoupon']['created'] = date("Y-m-d H:i:s");
				if($this->MobileCoupon->save($mobilecoupon_arr)){
					$this->Session->setFlash(__('Mobile Coupon has been saved', true));
					$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'index'));		
				}else{
					$this->Session->setFlash(__('Mobile Coupon has not been saved', true));
				}
			}
		}else{
            $this->redirect('/users/login');
        }
	}
	function edit($id=null){
	    
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $timezone = $users['User']['timezone'];
            date_default_timezone_set($timezone);
			$mobilecoupons = $this->MobileCoupon->find('first', array('conditions' =>array('MobileCoupon.id'=>$id,'MobileCoupon.user_id' =>$user_id))); 
			$this->set('mobilecoupons',$mobilecoupons);
			if(!empty($this->request->data)){
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$extension = strtolower(pathinfo($this->request->data['MobileCoupon']['offer_image']['name'], PATHINFO_EXTENSION));
						$extension_arr = array('jpg','jpeg','png','gif');
						if (!in_array($extension, $extension_arr)){
							$this->Session->setFlash(__('Please select image file type only for the coupon image', true));
							$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'edit/'.$id));
						}
					}
				}
				$mobilecoupon_arr['MobileCoupon']['id'] = $id;
				$mobilecoupon_arr['MobileCoupon']['user_id'] = $this->Session->read('User.id');
				$mobilecoupon_arr['MobileCoupon']['name'] = $this->request->data['MobileCoupon']['name'];
				$mobilecoupon_arr['MobileCoupon']['description'] = $this->request->data['MobileCoupon']['description'];
				$mobilecoupon_arr['MobileCoupon']['one_per_person'] = $this->request->data['MobileCoupon']['one_per_person'];
				$mobilecoupon_arr['MobileCoupon']['redemption_reset'] = $this->request->data['MobileCoupon']['redemption_reset'];
				$mobilecoupon_arr['MobileCoupon']['expiration_date'] = $this->request->data['MobileCoupon']['expiration_date'];
				$mobilecoupon_arr['MobileCoupon']['coupon_expiration'] = $this->request->data['MobileCoupon']['coupon_expiration'];
                $mobilecoupon_arr['MobileCoupon']['dynamic_days'] = $this->request->data['MobileCoupon']['dynamic_days'];
				$mobilecoupon_arr['MobileCoupon']['showVoteContainer'] = $this->request->data['MobileCoupon']['showVoteContainer'];
				$mobilecoupon_arr['MobileCoupon']['coupon_colorway'] = $this->request->data['MobileCoupon']['coupon_colorway'];
				$mobilecoupon_arr['MobileCoupon']['coupon_header'] = $this->request->data['MobileCoupon']['coupon_header'];
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$filename = time().$this->request->data['MobileCoupon']['offer_image']['name'];
						move_uploaded_file($this->request->data['MobileCoupon']['offer_image']['tmp_name'], "mobile_coupons/".$filename);
						$mobilecoupon_arr['MobileCoupon']['offer_image'] = $filename;
					}
				}
				$mobilecoupon_arr['MobileCoupon']['offer_name'] = $this->request->data['MobileCoupon']['offer_name'];
				$mobilecoupon_arr['MobileCoupon']['offer_description'] = $this->request->data['MobileCoupon']['offer_description'];
				$mobilecoupon_arr['MobileCoupon']['redeem_button_text'] = $this->request->data['MobileCoupon']['redeem_button_text'];
				$mobilecoupon_arr['MobileCoupon']['fine_print'] = $this->request->data['MobileCoupon']['fine_print'];
				$mobilecoupon_arr['MobileCoupon']['showCallLink'] = $this->request->data['MobileCoupon']['showCallLink'];
				$mobilecoupon_arr['MobileCoupon']['call_number'] = $this->request->data['MobileCoupon']['call_number'];
				$mobilecoupon_arr['MobileCoupon']['showWebsiteLink'] = $this->request->data['MobileCoupon']['showWebsiteLink'];
				$mobilecoupon_arr['MobileCoupon']['website'] = $this->request->data['MobileCoupon']['website'];
				$mobilecoupon_arr['MobileCoupon']['showFacebookLink'] = $this->request->data['MobileCoupon']['showFacebookLink'];
				$mobilecoupon_arr['MobileCoupon']['facebook'] = $this->request->data['MobileCoupon']['facebook'];
				$mobilecoupon_arr['MobileCoupon']['showInstagramLink'] = $this->request->data['MobileCoupon']['showInstagramLink'];
				$mobilecoupon_arr['MobileCoupon']['instagram'] = $this->request->data['MobileCoupon']['instagram'];
				$mobilecoupon_arr['MobileCoupon']['showDirectionsLink'] = $this->request->data['MobileCoupon']['showDirectionsLink'];
				$mobilecoupon_arr['MobileCoupon']['directions'] = $this->request->data['MobileCoupon']['directions'];
				$mobilecoupon_arr['MobileCoupon']['redemption_type'] = $this->request->data['MobileCoupon']['redemption_type'];
				$mobilecoupon_arr['MobileCoupon']['Redemption_Action'] = $this->request->data['MobileCoupon']['Redemption_Action'];
				$mobilecoupon_arr['MobileCoupon']['redemption_code'] = $this->request->data['MobileCoupon']['redemption_code'];
				$mobilecoupon_arr['MobileCoupon']['created'] = date("Y-m-d H:i:s");
				if($this->MobileCoupon->save($mobilecoupon_arr)){
					$this->Session->setFlash(__('Mobile Coupon has been updated', true));
					$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'index'));		
				}else{
					$this->Session->setFlash(__('Mobile Coupon has not been updated', true));
				}
			}
		}else{
            $this->redirect('/users/login');
        }
	}
	function copy_coupons($id=null){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $timezone = $users['User']['timezone'];
            date_default_timezone_set($timezone);
			$mobilecoupons = $this->MobileCoupon->find('first', array('conditions' =>array('MobileCoupon.id'=>$id,'MobileCoupon.user_id' =>$user_id))); 
			$this->set('mobilecoupons',$mobilecoupons);
			if(!empty($this->request->data)){
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$extension = strtolower(pathinfo($this->request->data['MobileCoupon']['offer_image']['name'], PATHINFO_EXTENSION));
						$extension_arr = array('jpg','jpeg','png','gif');
						if (!in_array($extension, $extension_arr)){
							$this->Session->setFlash(__('Please select image file type only for the coupon image', true));
							$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'copy_coupons/'.$id));
						}
					}
				}
				$mobilecoupon_arr['MobileCoupon']['id'] = '';
				$mobilecoupon_arr['MobileCoupon']['unique_id'] = $this->unique_code(8);
				$mobilecoupon_arr['MobileCoupon']['user_id'] = $this->Session->read('User.id');
				$mobilecoupon_arr['MobileCoupon']['name'] = $this->request->data['MobileCoupon']['name'];
				$mobilecoupon_arr['MobileCoupon']['description'] = $this->request->data['MobileCoupon']['description'];
				$mobilecoupon_arr['MobileCoupon']['one_per_person'] = $this->request->data['MobileCoupon']['one_per_person'];
				$mobilecoupon_arr['MobileCoupon']['redemption_reset'] = $this->request->data['MobileCoupon']['redemption_reset'];
				$mobilecoupon_arr['MobileCoupon']['expiration_date'] = $this->request->data['MobileCoupon']['expiration_date'];
				$mobilecoupon_arr['MobileCoupon']['coupon_expiration'] = $this->request->data['MobileCoupon']['coupon_expiration'];
                $mobilecoupon_arr['MobileCoupon']['dynamic_days'] = $this->request->data['MobileCoupon']['dynamic_days'];
				$mobilecoupon_arr['MobileCoupon']['showVoteContainer'] = $this->request->data['MobileCoupon']['showVoteContainer'];
				$mobilecoupon_arr['MobileCoupon']['coupon_colorway'] = $this->request->data['MobileCoupon']['coupon_colorway'];
				$mobilecoupon_arr['MobileCoupon']['coupon_header'] = $this->request->data['MobileCoupon']['coupon_header'];
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$filename = time().$this->request->data['MobileCoupon']['offer_image']['name'];
						move_uploaded_file($this->request->data['MobileCoupon']['offer_image']['tmp_name'], "mobile_coupons/".$filename);
						$mobilecoupon_arr['MobileCoupon']['offer_image'] = $filename;
					}else{
					    $mobilecoupon_arr['MobileCoupon']['offer_image'] = $mobilecoupons['MobileCoupon']['offer_image'];
					}
				}else{
				    $mobilecoupon_arr['MobileCoupon']['offer_image'] = $mobilecoupons['MobileCoupon']['offer_image'];
				}
				$mobilecoupon_arr['MobileCoupon']['offer_name'] = $this->request->data['MobileCoupon']['offer_name'];
				$mobilecoupon_arr['MobileCoupon']['offer_description'] = $this->request->data['MobileCoupon']['offer_description'];
				$mobilecoupon_arr['MobileCoupon']['redeem_button_text'] = $this->request->data['MobileCoupon']['redeem_button_text'];
				$mobilecoupon_arr['MobileCoupon']['fine_print'] = $this->request->data['MobileCoupon']['fine_print'];
				$mobilecoupon_arr['MobileCoupon']['showCallLink'] = $this->request->data['MobileCoupon']['showCallLink'];
				$mobilecoupon_arr['MobileCoupon']['call_number'] = $this->request->data['MobileCoupon']['call_number'];
				$mobilecoupon_arr['MobileCoupon']['showWebsiteLink'] = $this->request->data['MobileCoupon']['showWebsiteLink'];
				$mobilecoupon_arr['MobileCoupon']['website'] = $this->request->data['MobileCoupon']['website'];
				$mobilecoupon_arr['MobileCoupon']['showFacebookLink'] = $this->request->data['MobileCoupon']['showFacebookLink'];
				$mobilecoupon_arr['MobileCoupon']['facebook'] = $this->request->data['MobileCoupon']['facebook'];
				$mobilecoupon_arr['MobileCoupon']['showInstagramLink'] = $this->request->data['MobileCoupon']['showInstagramLink'];
				$mobilecoupon_arr['MobileCoupon']['instagram'] = $this->request->data['MobileCoupon']['instagram'];
				$mobilecoupon_arr['MobileCoupon']['showDirectionsLink'] = $this->request->data['MobileCoupon']['showDirectionsLink'];
				$mobilecoupon_arr['MobileCoupon']['directions'] = $this->request->data['MobileCoupon']['directions'];
				$mobilecoupon_arr['MobileCoupon']['redemption_type'] = $this->request->data['MobileCoupon']['redemption_type'];
				$mobilecoupon_arr['MobileCoupon']['Redemption_Action'] = $this->request->data['MobileCoupon']['Redemption_Action'];
				$mobilecoupon_arr['MobileCoupon']['redemption_code'] = $this->request->data['MobileCoupon']['redemption_code'];
				$mobilecoupon_arr['MobileCoupon']['created'] = date("Y-m-d H:i:s");
				if($this->MobileCoupon->save($mobilecoupon_arr)){
					$this->Session->setFlash(__('Mobile Coupon has been created', true));
					$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'index'));		
				}else{
					$this->Session->setFlash(__('Mobile Coupon has not been created', true));
				}
			}
		}else{
            $this->redirect('/users/login');
        }
	}
	function statistics($id=null){
	    if ($this->Session->check('User')) {
        	$this->layout= 'admin_new_layout';
        	$mobilecoupons = $this->MobileCoupon->find('first', array('conditions' =>array('MobileCoupon.id'=>$id))); 
        	$this->set('mobilecoupons',$mobilecoupons);
        	$totalview = $this->MobileCouponAnalytics->find('count', array('conditions' =>array('MobileCouponAnalytics.mobile_coupon_id'=>$id))); 
        	$this->set('totalview',$totalview);
			$uniquetotalview = $this->MobileCouponAnalytics->find('count', array('conditions' =>array('MobileCouponAnalytics.mobile_coupon_id'=>$id),'group' => array('MobileCouponAnalytics.ip'))); 
        	$this->set('uniquetotalview',$uniquetotalview);
		}else{
            $this->redirect('/users/login');
        }
	}
	function preview(){
	    if ($this->Session->check('User')) {
        	$this->layout= '';
  		}else{
            $this->redirect('/users/login');
        }
	}
	function delete($id=null){
		$this->autoRender = false;	
		if ($this->MobileCoupon->delete($id)){
		    $this->MobileCouponAnalytics->deleteAll(array('MobileCouponAnalytics.mobile_coupon_id' => $id));
			$this->Session->setFlash(__('Mobile Coupon has been deleted', true));
			$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'index'));
		}
	}
	function view($unique_id=null){
    	$this->layout= '';
    	$mobilecoupons = $this->MobileCoupon->find('first', array('conditions' =>array('MobileCoupon.unique_id'=>$unique_id))); 
    	$users = $this->User->find('first', array('conditions' => array('User.id' => $mobilecoupons['MobileCoupon']['user_id'])));
        $timezone = $users['User']['timezone'];
        date_default_timezone_set($timezone);
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
		if(!empty($mobilecoupons)){
			$this->set('mobilecoupons',$mobilecoupons);
			$this->set('unique_id',$unique_id);
			if($mobilecoupons['MobileCoupon']['redemption_type']==1){
			    $this->Qrsms->url(SITE_URL.'/mobilecoupons/redeemed/'.$unique_id);
    			$this->Qrsms->draw(300, "qr/" . $unique_id);
    			$this->set('qrimage', SITE_URL . '/qr/' . $unique_id . '.png');
			}
			$client  = @$_SERVER['HTTP_CLIENT_IP'];
			$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			$remote  = $_SERVER['REMOTE_ADDR'];
			if(filter_var($client, FILTER_VALIDATE_IP)){
				$ip = $client;
			}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
				$ip = $forward;
			}else{
				$ip = $remote;
			}
			$mobilecoupons_analytics_arr['MobileCouponAnalytics']['id'] = '';
			$mobilecoupons_analytics_arr['MobileCouponAnalytics']['user_id'] = $mobilecoupons['MobileCoupon']['user_id'];
			$mobilecoupons_analytics_arr['MobileCouponAnalytics']['mobile_coupon_id'] = $mobilecoupons['MobileCoupon']['id'];
			$mobilecoupons_analytics_arr['MobileCouponAnalytics']['ip'] = $ip;
			$mobilecoupons_analytics_arr['MobileCouponAnalytics']['created'] = date("Y-m-d H:i:s");
			$this->MobileCouponAnalytics->save($mobilecoupons_analytics_arr);
		}else{
            $this->redirect('/users/login');
        }
	}
	function redeemed($unique_id=null){
    	$this->layout= '';
    	$mobilecoupons = $this->MobileCoupon->find('first', array('conditions' =>array('MobileCoupon.unique_id'=>$unique_id), 'fields' => array('MobileCoupon.name', 'MobileCoupon.total_redemption', 'MobileCoupon.redemption_code'))); 
    	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
		if(!empty($mobilecoupons)){
			$this->set('mobilecoupons',$mobilecoupons);
			$this->set('unique_id',$unique_id);
		}else{
            $this->Session->setFlash(__('Mobile Coupon is not found', true));
        }
	}
	function mobile_coupon_analytics(){
    	$this->autoRender = false;	
    	$mobilecoupons = $this->MobileCoupon->find('first', array('conditions' =>array('MobileCoupon.unique_id'=>$_REQUEST['coupon']))); 
		if(!empty($mobilecoupons)){
			$mobilecoupons_analytics_arr['MobileCoupon']['id'] = $mobilecoupons['MobileCoupon']['id'];
			if(isset($_REQUEST['event']) && ($_REQUEST['event']=='vote_up')){
				$mobilecoupons_analytics_arr['MobileCoupon']['up_votes'] = $mobilecoupons['MobileCoupon']['up_votes'] + 1;
			}else if(isset($_REQUEST['event']) && ($_REQUEST['event']=='vote_down')){
				$mobilecoupons_analytics_arr['MobileCoupon']['down_votes'] = $mobilecoupons['MobileCoupon']['down_votes'] + 1;
			}else if(isset($_REQUEST['event']) && ($_REQUEST['event']=='redemption')){
				$mobilecoupons_analytics_arr['MobileCoupon']['total_redemption'] = $mobilecoupons['MobileCoupon']['total_redemption'] + 1;
			}
			$this->MobileCoupon->save($mobilecoupons_analytics_arr);
		}
	}
    function unique_code($digits){
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
                $random_generator .= rand(1, 9); // one number is added
            }
        } // end of for loop
        return $random_generator;
    } // end of function
	function reset_redemptions($id=null){
		$this->autoRender = false;
		$mobilecoupons_analytics_arr['MobileCoupon']['id'] = $id;
		$mobilecoupons_analytics_arr['MobileCoupon']['total_redemption'] = 0;
		if($this->MobileCoupon->save($mobilecoupons_analytics_arr)){
			$this->Session->setFlash(__('Mobile Coupon redemptions reset', true));
			$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'statistics/'.$id));
		}
	}
	function reset_views($id=null){
		$this->autoRender = false;	
		if ($this->MobileCouponAnalytics->deleteAll(array('MobileCouponAnalytics.mobile_coupon_id' => $id))){
			$this->Session->setFlash(__('Mobile Coupon views reset', true));
			$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'statistics/'.$id));
		}
	}
	function reset_votes($id=null){
		$this->autoRender = false;
		$mobilecoupons_analytics_arr['MobileCoupon']['id'] = $id;
		$mobilecoupons_analytics_arr['MobileCoupon']['up_votes'] = 0;
		$mobilecoupons_analytics_arr['MobileCoupon']['down_votes'] = 0;
		if($this->MobileCoupon->save($mobilecoupons_analytics_arr)){
			$this->Session->setFlash(__('Mobile Coupon votes reset', true));
			$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'statistics/'.$id));
		}
	}
	
	/*function addnew(){
		if ($this->Session->check('User')) {
			$this->layout= 'admin_new_layout';
			$user_id=$this->Session->read('User.id');
			$users = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
            $timezone = $users['User']['timezone'];
            date_default_timezone_set($timezone);
			app::import('Model', 'Group');
			if(!empty($this->request->data)){
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$extension = strtolower(pathinfo($this->request->data['MobileCoupon']['offer_image']['name'], PATHINFO_EXTENSION));
						$extension_arr = array('jpg','jpeg','png','gif');
						if (!in_array($extension, $extension_arr)){
							$this->Session->setFlash(__('Please select image file type only for the coupon image', true));
							$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'add'));
						}
					}
				}
				$mobilecoupon_arr['MobileCoupon']['id'] = '';
				$mobilecoupon_arr['MobileCoupon']['unique_id'] = $this->unique_code(8);
				$mobilecoupon_arr['MobileCoupon']['user_id'] = $this->Session->read('User.id');
				$mobilecoupon_arr['MobileCoupon']['name'] = $this->request->data['MobileCoupon']['name'];
				$mobilecoupon_arr['MobileCoupon']['description'] = $this->request->data['MobileCoupon']['description'];
				$mobilecoupon_arr['MobileCoupon']['one_per_person'] = $this->request->data['MobileCoupon']['one_per_person'];
				$mobilecoupon_arr['MobileCoupon']['redemption_reset'] = $this->request->data['MobileCoupon']['redemption_reset'];
				$mobilecoupon_arr['MobileCoupon']['expiration_date'] = $this->request->data['MobileCoupon']['expiration_date'];
				$mobilecoupon_arr['MobileCoupon']['coupon_expiration'] = $this->request->data['MobileCoupon']['coupon_expiration'];
                $mobilecoupon_arr['MobileCoupon']['dynamic_days'] = $this->request->data['MobileCoupon']['dynamic_days'];
				$mobilecoupon_arr['MobileCoupon']['showVoteContainer'] = $this->request->data['MobileCoupon']['showVoteContainer'];
				$mobilecoupon_arr['MobileCoupon']['coupon_colorway'] = $this->request->data['MobileCoupon']['coupon_colorway'];
				$mobilecoupon_arr['MobileCoupon']['coupon_header'] = $this->request->data['MobileCoupon']['coupon_header'];
				if(isset($this->request->data['MobileCoupon']['offer_image']['name'])){
					if($this->request->data['MobileCoupon']['offer_image']['name'] !=''){
						$filename = time().$this->request->data['MobileCoupon']['offer_image']['name'];
						move_uploaded_file($this->request->data['MobileCoupon']['offer_image']['tmp_name'], "mobile_coupons/".$filename);
						$mobilecoupon_arr['MobileCoupon']['offer_image'] = $filename;
					}
				}
				$mobilecoupon_arr['MobileCoupon']['offer_name'] = $this->request->data['MobileCoupon']['offer_name'];
				$mobilecoupon_arr['MobileCoupon']['offer_description'] = $this->request->data['MobileCoupon']['offer_description'];
				$mobilecoupon_arr['MobileCoupon']['redeem_button_text'] = $this->request->data['MobileCoupon']['redeem_button_text'];
				$mobilecoupon_arr['MobileCoupon']['fine_print'] = $this->request->data['MobileCoupon']['fine_print'];
				$mobilecoupon_arr['MobileCoupon']['showCallLink'] = $this->request->data['MobileCoupon']['showCallLink'];
				$mobilecoupon_arr['MobileCoupon']['call_number'] = $this->request->data['MobileCoupon']['call_number'];
				$mobilecoupon_arr['MobileCoupon']['showWebsiteLink'] = $this->request->data['MobileCoupon']['showWebsiteLink'];
				$mobilecoupon_arr['MobileCoupon']['website'] = $this->request->data['MobileCoupon']['website'];
				$mobilecoupon_arr['MobileCoupon']['showFacebookLink'] = $this->request->data['MobileCoupon']['showFacebookLink'];
				$mobilecoupon_arr['MobileCoupon']['facebook'] = $this->request->data['MobileCoupon']['facebook'];
				$mobilecoupon_arr['MobileCoupon']['showInstagramLink'] = $this->request->data['MobileCoupon']['showInstagramLink'];
				$mobilecoupon_arr['MobileCoupon']['instagram'] = $this->request->data['MobileCoupon']['instagram'];
				$mobilecoupon_arr['MobileCoupon']['showDirectionsLink'] = $this->request->data['MobileCoupon']['showDirectionsLink'];
				$mobilecoupon_arr['MobileCoupon']['directions'] = $this->request->data['MobileCoupon']['directions'];
				$mobilecoupon_arr['MobileCoupon']['redemption_type'] = $this->request->data['MobileCoupon']['redemption_type'];
				$mobilecoupon_arr['MobileCoupon']['Redemption_Action'] = $this->request->data['MobileCoupon']['Redemption_Action'];
				$mobilecoupon_arr['MobileCoupon']['redemption_code'] = $this->request->data['MobileCoupon']['redemption_code'];
				$mobilecoupon_arr['MobileCoupon']['created'] = date("Y-m-d H:i:s");
				if($this->MobileCoupon->save($mobilecoupon_arr)){
					$this->Session->setFlash(__('Mobile Coupon has been saved', true));
					$this->redirect(array('controller' =>'mobilecoupons', 'action'=>'index'));		
				}else{
					$this->Session->setFlash(__('Mobile Coupon has not been saved', true));
				}
			}
		}else{
            $this->redirect('/users/login');
        }
	}*/
}
?>