<?php 
include("database.php");
	
class YtelComponent
{
    var $controller;
    function startup( &$controller ) {
        $this->controller = &$controller;
	
    }
	
	function sendsms($api_key,$api_token,$tonumber,$from,$body){
	    $to='+'.$tonumber;
	    $from='+'.$from;
		$url = "https://api.ytel.com/api/v3/sms/sendsms.json";
		$fields = array(
			'From' => $from,
			'To' => $to,
			'Body' => $body,
			'MessageStatusCallback' => SITEURL.'/ytels/status',
			'DeliveryStatus' => 'true',
			'Smartsms' => 'false',
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