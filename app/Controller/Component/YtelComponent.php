<?php 
class YtelComponent extends Component{
    var $controller;
    var $curlinit = '';
	var $bulksms=0;

	function __construct(){
		$this->getObject();
	}
	
    function startup(Controller $controller ) {
        $this->controller = $controller;
    }
    
    function getObject(){
		$curlinit = $this->curlinit;
		$bulksms = $this->bulksms;
	}
	
	function listNumbers($api_key, $api_token, $areacode, $type){
		$url = "https://api.ytel.com/api/v3/incomingphone/availablenumber.json";
		$fields = array(
			'areacode' => $areacode,
			'numbertype' => $type,
			'pagesize' => '20',
		);
		$data_string = json_encode($fields); 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
	   	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string))); 
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
			
		/*ob_start();
        print_r($response->Message360->Phones->Phone);
		$out1 = ob_get_contents();
		ob_end_clean();
		$file = fopen("debug/ytellistnumbers".time().".txt", "w");
		fwrite($file, $out1);
		fclose($file);*/
		return $response;
	}
	function assignthisnumber($api_key,$api_token,$number){
	    $number='+'.$number;
		$url = "https://api.ytel.com/api/v3/incomingphone/buynumber.json";
		$fields = array(
			'PhoneNumber' => $number,
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string))); 
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function updatethisnumber($api_key,$api_token,$number){
	    $number='+'.$number;
		$url = "https://api.ytel.com/api/v3/incomingphone/updatenumber.json";
		$fields = array(
		    'PhoneNumber' => $number,
			'SmsUrl' => SITE_URL.'/ytels/sms',
			'VoiceUrl' => SITE_URL.'/ytels/voice',
			'VoiceMethod' => 'POST',
			'SmsMethod' => 'POST',
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string))); 
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function sendsms($api_key,$api_token,$tonumber,$from,$body,$group_sms_id=null,$group_id=null,$userid=null){
	    $this->getObject();
	    
	    if(mb_strlen($from,"UTF-8") > 6 ){
    	    $from='+'.$from;
    	    $to='+'.$tonumber;
    		$url = "https://api.ytel.com/api/v3/sms/sendsms.json";
    		
    		$fields = array(
    			'From' => $from,
    			'To' => $to,
    			'Body' => $body,
    			'MessageStatusCallback' => SITE_URL.'/ytels/status/'.$group_sms_id.'/'.$group_id.'/'.$userid,
    			'DeliveryStatus' => 'true',
    			'Smartsms' => 'false',
    		);
	    }else{
	        //$to='+'.$tonumber;
    		$url = "https://api.ytel.com/api/v3/dedicatedshortcode/sendsms.json";
    		
    		$fields = array(
    			'shortcode' => $from,
    			'to' => $tonumber,
    			'body' => $body,
    			'messagestatuscallback' => SITE_URL.'/ytels/status/'.$group_sms_id.'/'.$group_id.'/'.$userid,
    		);
	        
	    }
		$data_string = json_encode($fields);  
		// initialize a new curl object   
		if($this->bulksms == 1){
		   $ch = $this->curlinit; 
		}else{
		   $ch = curl_init();  
		}
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string))); 
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function sendmms($api_key,$api_token,$tonumber,$from,$body,$media,$group_sms_id=null,$group_id=null,$userid=null){
	    $this->getObject();
		$to='+'.$tonumber;
		$from='+'.$from;
		$url = "https://api.ytel.com/api/v3/sms/sendsms.json";
		$fields = array(
			'From' => $from,
			'To' => $to,
			'Body' => $body,
			'MessageStatusCallback' => SITE_URL.'/ytels/status/'.$group_sms_id.'/'.$group_id.'/'.$userid,
		);
		//$data_string = json_encode($fields);
		$encoded = '';
        foreach($fields as $name => $value){
            $encoded .= urlencode($name).'='.urlencode($value).'&';
        }
        foreach($media as $mediaurl){
            $encoded .= urlencode("MediaURL").'='.urlencode($mediaurl).'&';
        }
        // chop off the last ampersand
        $encoded = substr($encoded, 0, strlen($encoded)-1);
        
		// initialize a new curl object   
		if($this->bulksms == 1){
		   $ch = $this->curlinit; 
		}else{
		   $ch = curl_init();  
		}
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function callNumberToPEOPLE($api_key,$api_token,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number){
	    //$this->getObject();
	    $url = "https://api.ytel.com/api/v3/calls/makecall.json";
		$callurl = SITE_URL.'/ytels/peoplecallrecordscript/'.$group_id.'/'.$repeat.'/'.$language.'/'.$pause.'/'.$forward.'/'.$forward_number;
		$callbackUrl= SITE_URL.'/ytels/sendcallStatus/'.$lastinsertID.'/'.$forward;
		$fields = array(
			'To'=> '+'.$callnumber,
			'From'=> '+'.$callerPhone,
			'Url'=> $callurl,
			'StatusCallBackUrl'=> $callbackUrl,
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string))); 
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function delete_phone_number($api_key, $api_token, $number){
	    $number='+'.$number;
		$url = "https://api.ytel.com/api/v3/incomingphone/releasenumber.json";
		$fields = array(
			'PhoneNumber'=> $number,
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function sendrvm($api_key, $api_token, $tonumber, $from, $rvmcallerid, $audio, $user_id, $group_id, $voicebalance){
	    $to='+'.$tonumber;
		$from='+'.$from;
		$rvmcallerid='+'.$rvmcallerid;
		$url = "https://api.ytel.com/api/v3/calls/makervm.json";
		$audio_url = SITE_URL . '/voice/' . $audio; 
		$fields = array(
			'From'=> $from,
			'To'=> $to,
			'RVMCallerId'=> $rvmcallerid,
			'VoiceMailURL' => $audio_url,
			'StatusCallBackUrl' => SITE_URL.'/ytels/rvmstatus/'.$user_id.'/'.$group_id.'/'.$voicebalance,
		);
		
		$data_string = json_encode($fields); 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function messagedetails($api_key, $api_token, $msgid){
		$url = "https://api.ytel.com/api/v3/sms/viewdetailsms.json";
		$fields = array(
			'MessageSid'=> $msgid,
		);
		$data_string = json_encode($fields); 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function calldetails($api_key, $api_token, $callsid){
		$url = "https://api.ytel.com/api/v3/calls/viewcalldetail.json";
		$fields = array(
			'callSid'=> $callsid,
		);
		$data_string = json_encode($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function lookupcarrier($api_key, $api_token, $number){
	    $number='+'.$number;
		$url = "https://api.ytel.com/api/v3/carrier/lookup.json";
		$fields = array(
			'PhoneNumber'=> $number,
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	/*
	function recordingdetails($api_key, $api_token, $api_user_id, $callsid){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/calls/".$callsid."/recordings";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function getnumberdetails($api_key, $api_token, $api_user_id, $number){
		$number='+'.$number;
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/phoneNumbers/".urlencode($number);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}*/
}
?>