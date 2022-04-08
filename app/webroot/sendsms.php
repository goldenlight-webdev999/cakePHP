#!/usr/local/bin/php -q
<?php
    $server_root = str_replace('/sendsms.php','',$_SERVER['SCRIPT_FILENAME']);
    error_reporting(E_ALL ^ (E_STRICT | E_DEPRECATED | E_NOTICE | E_WARNING));
    ini_set('display_startup_errors',0);
    ini_set('display_errors',0);
    include($server_root.'/database.php');
    include($server_root.'/nexmo.php');
    include($server_root.'/slooce.php');
    include($server_root.'/bandwidthcomponent.php');
    include($server_root.'/signalwirecomponent.php');
    include($server_root.'/ytelcomponent.php');
    include($server_root.'/telnyxcomponent.php');
    include($server_root.'/plivo.php');
    include($server_root.'/twiliocomponent.php');
    include($server_root.'/class.email.php');

    $email = email::parseSTDIN();
    $subject = $email->getSubject();
	$message = $email->getTextContent();
    $config_settings = mysqli_query($con,"SELECT * FROM configs");  
    $config_details = mysqli_fetch_array($config_settings);
   
	$ticket_id = explode('-',$subject);
	$message_new =ucfirst($message);
	$ticketdetails = mysqli_query($con,"SELECT * FROM logs where ticket='".$ticket_id[1]."'");  
	$ticket_arr = mysqli_fetch_array($ticketdetails);
	
	$user_api_type = mysqli_query($con,"SELECT api_type, sid, authtoken, bandwidthuserid, signalwirespace FROM users where id='".$ticket_arr['user_id']."'");  
	$api_type_temp = mysqli_fetch_array($user_api_type);
	$api_type = $api_type_temp['api_type'];
	$SID = trim($api_type_temp['sid']);
    $AUTHTOKEN = trim($api_type_temp['authtoken']);
    $BANDWIDTHUSERID = trim($api_type_temp['bandwidthuserid']);
    $SIGNALWIRESPACE = trim($api_type_temp['signalwirespace']);
    
    $length = mb_strlen($message_new,"UTF-8");
	$gsm = is_gsm($message_new);
			
	if(!$gsm){
        $credits = ceil($length / 70);
    } else {
        $credits = ceil($length / 160);
    }

	//if($config_details['api_type']==0){
    if($api_type==0){	    
		$obj = new TwilioComponent();
		
		if(isset($ticket_arr['id'])){
			
			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;
			
			if ($SID == ''){
                $obj->AccountSid = $config_details['twilio_accountSid'];
                $obj->AuthToken = $config_details['twilio_auth_token'];
            }else{
                $obj->AccountSid = $SID;
                $obj->AuthToken = $AUTHTOKEN;
            }

            /*if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message. Either send a shorter message or replenish your account with more credits.";
                $response = $obj->sendsms($ticket_arr['email_to_sms_number'],$ticket_arr['email_to_sms_number'],$message);
                exit;
            }*/
            
            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message.";
				mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
        		exit;
            }
			
			$response = $obj->sendsms($ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
			$smsid=$response->ResponseXml->Message->Sid;
			if($smsid!=''){
				$status = 'delivered';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}else{
				$status = 'failed';
                $errortext=$response->ResponseXml->RestException->Message;
			}
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$smsid."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
	
			
		}
	}else if($api_type==4){
		$obj = new BandwidthComponent();
		if(isset($ticket_arr['id'])){

			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;

            /*if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message. Either send a shorter message or replenish your account with more credits.";
                $response = $obj->sendsms($config_details['bandwidth_key'],$config_details['bandwidth_token'],$config_details['bandwidth_user_id'],$ticket_arr['email_to_sms_number'],$ticket_arr['email_to_sms_number'],$message);
                exit;
            }*/
            
            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message.";
				mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
        		exit;
            }
			
			if ($SID == ''){
               $response = $obj->sendsms($config_details['bandwidth_key'],$config_details['bandwidth_token'],$config_details['bandwidth_user_id'],$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }else{
               $response = $obj->sendsms($SID,$AUTHTOKEN,$BANDWIDTHUSERID,$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }
			$errortext='';
			$status='';
			$message_id='';
			if(isset($response->message)){
				$errortext = $response->message;
				$status = 'failed';
			}else{
				$status = 'sent';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
	
		}
	}else if($api_type==5){
		$obj = new SignalwireComponent();
		if(isset($ticket_arr['id'])){

			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;

            /*if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message. Either send a shorter message or replenish your account with more credits.";
                $response = $obj->sendsms($config_details['signalwire_key'],$config_details['signalwire_token'],$config_details['signalwire_space'],$ticket_arr['email_to_sms_number'],$ticket_arr['email_to_sms_number'],$message);
                exit;
            }*/
            
            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message.";
				mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
        		exit;
            }
			
			if ($SID == ''){
               $response = $obj->sendsms($config_details['signalwire_key'],$config_details['signalwire_token'],$config_details['signalwire_space'],$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }else{
               $response = $obj->sendsms($SID,$AUTHTOKEN,$SIGNALWIRESPACE,$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }
			$errortext='';
			$status='';
			$message_id='';
			if(isset($response->error_message)){
				$errortext = $response->error_message;
				$status = 'failed';
			}else{
			    $message_id = $response->sid;
				$status = 'sent';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
	
		}
		
	}else if($api_type==6){
		$obj = new YtelComponent();
		if(isset($ticket_arr['id'])){

			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;

            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message.";
				mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
        		exit;
            }
			
			if ($SID == ''){
               $response = $obj->sendsms($config_details['ytel_accountSid'],$config_details['ytel_auth_token'],$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }else{
               $response = $obj->sendsms($SID,$AUTHTOKEN,$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }
			$errortext='';
			$status='';
			$message_id='';
			if($response->Message360->ResponseStatus == 0){
				$errortext = $response->Message360->Errors->Error[0]->Message;
				$status = 'failed';
			}else{
			    $message_id = $response->Message360->Message->MessageSid;
				$status = 'sent';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
	
		}
		
	}else if($api_type==7){
		$obj = new TelnyxComponent();
		if(isset($ticket_arr['id'])){

			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;

            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message.";
				mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
        		exit;
            }
			
			if ($SID == ''){
               $response = $obj->sendsms($config_details['telnyx_key'],$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }else{
               $response = $obj->sendsms($SID,$ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
            }
			$errortext='';
			$status='';
			$message_id='';
			if(isset($response->errors)){
				$errortext = $response->errors[0]->detail;
				$status = 'failed';
			}else{
			    $message_id = $response->data->id;
				$status = 'sent';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
	
		}
		
	}else if($api_type==2){
		$obj = new SlooceComponent();
		
		if(isset($ticket_arr['id'])){

            $credits = 1;
			
			$user_sms_balance = mysqli_query($con,"SELECT * FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;

            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message.";
				mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
        		exit;
            }
			
			$response = $obj->mt($usercredits['api_url'],$usercredits['partnerid'],$usercredits['partnerpassword'],$ticket_arr['phone_number'],$usercredits['keyword'],$message_new);
			$message_id = '';
			$status = '';
			if(isset($response['id'])){
				if($response['result']=='ok'){
					$message_id = $response['id'];
				}
				$status = $response['result'];
			}
			if($status !='ok'){
				$status = 'failed';
			}else{
				$status = 'sent';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$status."','".date('Y-m-d H:i:s')."')");	
			
				
		}
	//}else if($config_details['api_type']==1){
      }else if($api_type==1){
		$obj = new NexmoComponent();
		
		if(isset($ticket_arr['id'])){
			
			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;
			
			if ($SID == ''){
                $obj->Key = $config_details['nexmo_key'];
                $obj->Secret = $config_details['nexmo_secret'];
            }else{
                $obj->Key = $SID;
                $obj->Secret = $AUTHTOKEN;
            }

            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message. Either send a shorter message or replenish your account with more credits.";
            	mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
                exit;
            }
			
            $response = $obj->sendsms($ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);
			foreach($response->messages  as $doc){
				$message_id= $doc->messageid; 
				if($message_id!=''){
					$status= $doc->status;	  
					$message_id= $doc->messageid;	  
				}else{
					$status= $doc->status;
					$errortext= $doc->errortext; 
				}			  
			}
			if($status!=0){
				$status = 'failed';
			}else{
				$status = 'sent';
				mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
			
				
		}
	//}else if ($config_details['api_type']==3){
      }else if($api_type==3){
		$obj = new PlivoComponent();
		
		if(isset($ticket_arr['id'])){

			$user_sms_balance = mysqli_query($con,"SELECT sms_balance FROM users where id='".$ticket_arr['user_id']."'");  
			$usercredits = mysqli_fetch_array($user_sms_balance);
			$usercreditbalance = $usercredits['sms_balance'] - $credits;
			
			if ($SID == ''){
                $obj->auth_id = $config_details['plivo_key'];
                $obj->auth_token = $config_details['plivo_token'];
            }else{
                $obj->auth_id = $SID;
                $obj->auth_token = $AUTHTOKEN;
            }

            if($usercredits['sms_balance'] < $credits){
                $message = "You do not have enough credits to respond to this email by SMS. You need ".$credits." credits to send this email to SMS message. Either send a shorter message or replenish your account with more credits.";
            	mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['email_to_sms_number']."','".$message."','','','inbox','text','received','','".date('Y-m-d H:i:s')."')");	
                exit;
            }
			
			$response = $obj->sendsms($ticket_arr['phone_number'],$ticket_arr['email_to_sms_number'],$message_new);

            $errortext = '';
			$message_id = '';
			if(isset($response['response']['error'])){
				$errortext = $response['response']['error'];
                $status = 'failed';
			}
			if(isset($response['response']['message_uuid'][0])){
				$message_id = $response['response']['message_uuid'][0];
                $status = 'sent';
                mysqli_query($con,"UPDATE users set sms_balance = '".$usercreditbalance."' where id='".$ticket_arr['user_id']."'"); 
			}
			
			mysqli_query($con,"insert into logs(group_sms_id,sms_id,ticket,user_id,group_id,phone_number,text_message,image_url,voice_url,route,msg_type,sms_status,error_message,  created)values('0','".$message_id."','".$ticket_id[1]."',".$ticket_arr['user_id'].",'0','".$ticket_arr['phone_number']."','".$message_new."','','','outbox','text','".$status."','".$errortext."','".date('Y-m-d H:i:s')."')");	
	
		}
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
	