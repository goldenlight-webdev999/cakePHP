<?php 
class TelnyxComponent extends Component{
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
	
	function numberlookup($api_key,$number){
	    //*** Private lookup API - Needs API credentials
		//$url = "https://api.telnyx.com/v2/number_lookup/".$number;
		
		//*** Public lookup API - Free to use and doesn't need API credentials. Rate limited
		$url = "https://api.telnyx.com/anonymous/v2/number_lookup/".$number;
		
        $ch = curl_init();
		//$headers = array(
		// 'Authorization: Bearer '. $api_key,
		// 'Connection' => 'close'
		// );
		array_push($headers, "Content-Type:application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	
	function listNumbers($api_key, $fields = array()){
		$url = "https://api.telnyx.com/v2/available_phone_numbers";
		
		$url = $url . '?' . http_build_query($fields);
		
        $ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
		array_push($headers, "Content-Type:application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function assignthisnumber($api_key,$number){
	    $number='+'.$number;
		$url = "https://api.telnyx.com/v2/number_orders";
		
		$fields = ['phone_numbers' =>[['phone_number'=>$number]]];
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function createmessagingprofile($api_key,$username){
		$url = "https://api.telnyx.com/v2/messaging_profiles";
		
		$fields = array(
		    'name' => SITENAME.'_'.$username,
			'webhook_url' => SITE_URL.'/telnyxs/sms',
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function updatemessagingnumber($api_key,$phone_id,$messaging_profile_id){
		$url = "https://api.telnyx.com/v2/messaging_phone_numbers/".$phone_id;
		
		$fields = array(
		    'messaging_profile_id' => $messaging_profile_id,
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		
		/*ob_start();	
        print_r($response);
     	$out1 = ob_get_contents();
    	ob_end_clean();
    	$file = fopen("debug/telnyx_updatemessagingnumber".time().".txt", "w");
    	fwrite($file, $out1);
    	fclose($file);*/
    	
		return $response;
	}
	function sendsms($api_key,$tonumber,$from,$body){
	    $this->getObject();
	    
        $from='+'.$from;
        $to='+'.$tonumber;
    	$url = "https://api.telnyx.com/v2/messages";
    	
    	$fields = array(
    		'from' => $from,
    		'to' => $to,
    		'text' => $body,
    		'use_profile_webhooks' => 'false',
    		'webhook_url' => SITE_URL.'/telnyxs/status',
    	);

		$data_string = json_encode($fields);  
		// initialize a new curl object   
		if($this->bulksms == 1){
		   $ch = $this->curlinit; 
		}else{
		   $ch = curl_init();  
		}
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function sendmms($api_key,$tonumber,$from,$body,$media){
	    $this->getObject();
	    
		$to='+'.$tonumber;
		$from='+'.$from;
		$url = "https://api.telnyx.com/v2/messages";

		$fields = array(
			'from' => $from,
			'to' => $to,
			'text' => $body,
			'use_profile_webhooks' => 'false',
			'media_urls' => $media,
    		'webhook_url' => SITE_URL.'/telnyxs/status',
		);
		$data_string = json_encode($fields);

		// initialize a new curl object   
		if($this->bulksms == 1){
		   $ch = $this->curlinit; 
		}else{
		   $ch = curl_init();  
		}
		
		$headers = array(
	    'Authorization: Bearer '. $api_key,
	    'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function createcallapplication($api_key,$username){
		$url = "https://api.telnyx.com/v2/texml_applications";
		
		$fields = array(
		    'friendly_name' => SITENAME.'_'.$username.'_Call_TeXML',
			'voice_url' => SITE_URL.'/telnyxs/voice',
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function updatecallnumber($api_key,$phone_id,$call_app_id,$billing_group_id){
		$url = "https://api.telnyx.com/v2/phone_numbers/".$phone_id;
		
		$fields = array(
		    'connection_id' => $call_app_id,
		    'billing_group_id' => $billing_group_id,
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function listallnumbers($api_key){
		$url = "https://api.telnyx.com/v2/phone_numbers";
		
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		$numbersarray = $response->data;
		return $numbersarray;
	}
	function createvoiceprofile($api_key,$billing_group_id,$username){
		$url = "https://api.telnyx.com/v2/outbound_voice_profiles";
		
		$fields = array(
		    'name' => SITENAME.'_'.$username.'_Voice',
		    'billing_group_id' => $billing_group_id,
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function updatecallapplication($api_key,$callappid,$voiceprofileid){
		$url = "https://api.telnyx.com/v2/texml_applications/".$callappid;
		
		$fields = ['outbound' =>['outbound_voice_profile_id'=>$voiceprofileid]];

		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function sendfax($api_key, $tonumber, $fromnumber, $connection, $quality, $media){
	    $to='+'.$tonumber;
	    $from='+'.$fromnumber;
	    $url = "https://api.telnyx.com/v2/faxes";
	    $fields = array(
			'to'=> $to,
			'from'=> $from,
			'connection_id'=> $connection,
			'quality'=> $quality,
			'media_url'=> $media,
			'webhook_url'=> SITE_URL.'/telnyxs/faxcallstatus',
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function callNumberToPEOPLE($api_key,$callappid,$callnumber,$callerPhone, $group_id, $lastinsertID, $repeat, $language, $pause, $forward, $forward_number){
	    //$this->getObject();
	    $url = "https://api.telnyx.com/v2/texml/calls/".$callappid;
		$callurl = SITE_URL.'/telnyxs/peoplecallrecordscript/'.$group_id.'/'.$repeat.'/'.$language.'/'.$pause.'/'.$forward.'/'.$forward_number;
		$callbackUrl= SITE_URL.'/telnyxs/sendcallStatus/'.$lastinsertID;
		$fields = array(
			'To'=> '+'.$callnumber,
			'From'=> '+'.$callerPhone,
			'Url'=> $callurl,
			'StatusCallback'=> $callbackUrl,
		);
		$data_string = json_encode($fields);  
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function delete_phone_number($api_key,$numberid){
	    $url = "https://api.telnyx.com/v2/phone_numbers/".$numberid;
		
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function createbillinggroup($api_key,$user_name){
		$url = "https://api.telnyx.com/v2/billing_groups";
		
		$fields = array(
		    'name' => $user_name,
		);
		
		$data_string = json_encode($fields);  
		
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;

	}
	function messagedetails($api_key, $msgid){
		$url = "https://api.telnyx.com/v2/messages/".$msgid;
		
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	/*function calldetails($api_key, $callsid){
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
    */
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
	/*function createfaxprofile($api_key){
		$url = "https://api.telnyx.com/v2/outbound_voice_profiles";
		
		$fields = array(
		    'name' => SITENAME. '_Fax',
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
	
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		
		ob_start();	
        print_r($response);
        print_r($url);
        print_r($data_string);
    	$out1 = ob_get_contents();
    	ob_end_clean();
    	$file = fopen("debug/telnyx_voiceprofile".time().".txt", "w");
    	fwrite($file, $out1);
    	fclose($file);
                		
                		
		return $response;
	}
	function createfaxapplication($api_key){
		$url = "https://api.telnyx.com/v2/texml_applications";
		
		$fields = array(
		    'friendly_name' => SITENAME. '_Fax_TeXML',
			'voice_url' => SITE_URL.'/telnyxs/fax',
			'status_callback' => SITE_URL.'/telnyxs/faxcallstatus',
		);
		
		$data_string = json_encode($fields);  
	
		$ch = curl_init();
		$headers = array(
		  'Authorization: Bearer '. $api_key,
		  'Connection' => 'close'
		 );
		array_push($headers, "Content-Type:application/json");
		array_push($headers, "Accept: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}*/
}
?>