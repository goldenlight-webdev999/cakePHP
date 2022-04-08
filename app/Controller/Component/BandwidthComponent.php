<?php 
class BandwidthComponent extends Component{
    var $controller;
    function startup(Controller $controller ) {
        $this->controller = $controller;
    }
	function getapplication($api_key, $api_token, $api_user_id){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/applications"; 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response=json_decode($result);
		return $response;
	}
	function createapplication($api_key, $api_token, $api_user_id){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/applications";
		$fields = array(
			'name' =>SITENAME,
			'incomingCallUrl' =>SITE_URL.'/bandwidths/voice',
			'incomingMessageUrl' =>SITE_URL.'/bandwidths/sms',
			'callbackHttpMethod' =>'GET',
			'autoAnswer' =>'true',
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
	function listNumbers($api_key, $api_token, $api_user_id, $type, $fields = array()){
		$url = 'https://api.catapult.inetwork.com/v1/availableNumbers/'.$type;
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
	function assignthisnumber($api_key,$api_token,$api_user_id,$number,$applicationId){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/phoneNumbers";
		$fields = array(
			'number' => $number,
			'applicationId' => $applicationId,
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
	function updatethisnumber($api_key,$api_token,$api_user_id,$number,$applicationId){
	    $number='+'.$number;
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/phoneNumbers/".urlencode($number);
		$fields = array(
			'applicationId' => $applicationId,
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
	function sendsms($api_key,$api_token,$api_user_id,$tonumber,$from,$body,$tag=0){
	    //if(strlen($tonumber) > 10){
		//	$tonumber=str_replace('+','',$tonumber);
			$to='+'.$tonumber;
		//}else{
		//	$to='+'.$tonumber;
		//}
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/messages";
		$fields = array(
			'from' => $from,
			'to' => $to,
			'text' => $body,
			'receiptRequested' => 'all',
			'tag' => $tag,
			'callbackUrl' => SITE_URL.'/bandwidths/status',
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
	function sendmms($api_key,$api_token,$api_user_id,$tonumber,$from,$body,$media,$tag=0){
	    //if(strlen($tonumber) > 10){
		//	$tonumber=str_replace('+','',$tonumber);
			$to='+'.$tonumber;
		//}else{
		//	$to='+'.$tonumber;
		//}
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/messages";
		$fields = array(
			'from' => $from,
			'to' => $to,
			'text' => $body,
			//'receiptRequested' => 'all',
			'tag' => $tag,
			'callbackUrl' => SITE_URL.'/bandwidths/status',
			'media' => $media,
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
	function callNumberToPEOPLE($api_key,$api_token,$api_user_id,$callnumber,$callerPhone, $group_id, $lastinsertID, $language, $pause, $forward, $forward_number){
	    //if(strlen($callnumber) > 10){
		//	$callnumber=str_replace('+','',$callnumber);
			$callnumber='+'.$callnumber;
		//}else{
		//	$callnumber='+'.$callnumber;
		//}
		$callbackUrl = SITE_URL.'/bandwidths/peoplecallrecordscript/'.$group_id.'/'.$language.'/'.$pause.'/'.$forward.'/'.$forward_number;
		$url ='https://api.catapult.inetwork.com/v1/users/'.$api_user_id.'/calls';
		$fields = array(
			'to'=> $callnumber,
			'from'=> '+'.$callerPhone,
			'callbackUrl'=> $callbackUrl,
			'callbackHttpMethod'=>'GET',
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
	function calldetails($api_key, $api_token, $api_user_id, $callsid){
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
	function messagedetails($api_key, $api_token, $api_user_id, $msgid){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/messages/".$msgid;
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
	function delete_phone_number($api_key, $api_token, $api_user_id, $numberid){
		$url = "https://api.catapult.inetwork.com/v1/users/".$api_user_id."/phoneNumbers/".$numberid;
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
	}
}
?>