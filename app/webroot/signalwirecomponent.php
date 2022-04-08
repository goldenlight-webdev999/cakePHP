<?php 
include("database.php");
	
class SignalwireComponent
{
    var $controller;
    function startup( &$controller ) {
        $this->controller = &$controller;
	
    }
	function sendsms($api_key,$api_token,$api_space_url,$tonumber,$from,$body){
	    $to='+'.$tonumber;
	    $from='+'.$from;
		$url = "https://".$api_space_url."/api/laml/2010-04-01/Accounts/".$api_key."/Messages";
		$fields = array(
			'From' => $from,
			'To' => $to,
			'Body' => $body,
			'StatusCallback' => SITEURL.'/signalwires/status',
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