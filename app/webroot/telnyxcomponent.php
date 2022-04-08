<?php 
include("database.php");
	
class TelnyxComponent
{
    var $controller;
    function startup( &$controller ) {
        $this->controller = &$controller;
	
    }
	
	function sendsms($api_key,$tonumber,$from,$body){
        $from='+'.$from;
        $to='+'.$tonumber;
    	$url = "https://api.telnyx.com/v2/messages";
    	
    	$fields = array(
    		'from' => $from,
    		'to' => $to,
    		'text' => $body,
    		'use_profile_webhooks' => 'false',
    		'webhook_url' => SITEURL.'/telnyxs/status',
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
}
?>