<?php 
class SignalwireComponent extends Component{
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
	
	function listNumbers($api_key, $api_token, $api_space_url, $type, $country, $fields = array()){
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/AvailablePhoneNumbers/".$country."/".$type;
		$url = $url . '?' . http_build_query($fields);
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
	function assignthisnumber($api_key,$api_token,$api_space_url,$number){
	    $number='+'.$number;
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/IncomingPhoneNumbers";
		$fields = array(
			'PhoneNumber' => $number,
			'SmsUrl' => SITE_URL.'/signalwires/sms',
			'VoiceUrl' => SITE_URL.'/signalwires/voice',
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
	function updatethisnumber($api_key,$api_token,$api_space_url,$numberid){
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/IncomingPhoneNumbers/".$numberid;
		$fields = array(
			'SmsUrl' => SITE_URL.'/signalwires/sms',
			'VoiceUrl' => SITE_URL.'/signalwires/voice',
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
	function updatefaxnumber($api_key,$api_token,$api_space_url,$numberid,$mode){
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/IncomingPhoneNumbers/".$numberid;
		$fields = array(
			'VoiceReceiveMode' => $mode,
			'VoiceUrl' => SITE_URL.'/signalwires/'.$mode,
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
	function sendsms($api_key,$api_token,$api_space_url,$tonumber,$from,$body){
	    $this->getObject();
	    $to='+'.$tonumber;
	    $from='+'.$from;
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Messages";
		$fields = array(
			'From' => $from,
			'To' => $to,
			'Body' => $body,
			'StatusCallback' => SITE_URL.'/signalwires/status',
		);
		$data_string = json_encode($fields);  
		// initialize a new curl object   
		if($this->bulksms == 1){
		   $ch = $this->curlinit; 
		}else{
		   $ch = curl_init();  
		}
		//$ch = curl_init();
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
	function sendmms($api_key,$api_token,$api_space_url,$tonumber,$from,$body,$media){
	    $this->getObject();
		$to='+'.$tonumber;
		$from='+'.$from;
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Messages";
		$fields = array(
			'From' => $from,
			'To' => $to,
			'Body' => $body,
			'StatusCallback' => SITE_URL.'/signalwires/status',
		);
		
		$encoded = '';
        foreach($fields as $name => $value){
            $encoded .= urlencode($name).'='.urlencode($value).'&';
        }
        foreach($media as $mediaurl){
            $encoded .= urlencode("MediaUrl").'='.urlencode($mediaurl).'&';
        }
        // chop off the last ampersand
        $encoded = substr($encoded, 0, strlen($encoded)-1);

		// initialize a new curl object   
		if($this->bulksms == 1){
		   $ch = $this->curlinit; 
		}else{
		   $ch = curl_init();  
		}
		//$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function callNumberToPEOPLE($api_key,$api_token,$api_space_url,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number){
	    $url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Calls";
		$callurl = SITE_URL.'/signalwires/peoplecallrecordscript/'.$group_id.'/'.$repeat.'/'.$language.'/'.$pause.'/'.$forward.'/'.$forward_number;
		$callbackUrl= SITE_URL.'/signalwires/sendcallStatus/'.$lastinsertID;
		$fields = array(
			'To'=> '+'.$callnumber,
			'From'=> '+'.$callerPhone,
			'Url'=> $callurl,
			'StatusCallback'=> $callbackUrl,
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
	function delete_phone_number($api_key, $api_token, $api_space_url, $numberid){
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/IncomingPhoneNumbers/".$numberid;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function sendfax($api_key, $api_token, $api_space_url, $tonumber, $fromnumber, $media, $quality){
	    $to='+'.$tonumber;
	    $from='+'.$fromnumber;
	    $url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Faxes";
	    $callbackUrl = SITE_URL.'/signalwires/faxcallstatus';
	    $fields = array(
			'To'=> $to,
			'From'=> $from,
			'MediaUrl'=> $media,
			'StatusCallback'=> $callbackUrl,
			'Quality'=> $quality,
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
	function getfax($api_key, $api_token, $api_space_url, $faxid){
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Faxes/".$faxid;
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
	function messagedetails($api_key, $api_token, $api_space_url, $msgid){
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Messages/".$msgid;
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
	/*function calldetails($api_key, $api_token, $api_user_id, $callsid){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/calls/".$callsid;
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