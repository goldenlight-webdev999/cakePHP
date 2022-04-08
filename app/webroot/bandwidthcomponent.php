<?php 
include("database.php");
	
class BandwidthComponent
{
     var $controller;
    function startup( &$controller ) {
        $this->controller = &$controller;
	
    }
	function sendsms($api_key,$api_token,$api_user_id,$tonumber,$from,$body){
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
			//'callbackUrl' => SITEURL.'/bandwidths/status',
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
}
?>