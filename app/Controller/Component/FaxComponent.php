<?php 

class FaxComponent extends Component{
	var $controller;
	var	$AuthId = '';
	var	$AuthToken = '';
	var $curlinit = '';
	var $bulksms=0;
	
	
	function getObject(){
		$url = 'https://fax.twilio.com/v1/Faxes';
		$this->api = $url;
		$this->auth_id = $this->AuthId;
		$this->auth_token = $this->AuthToken;
	}

	function sendfax($tonumber,$fromnumber,$media,$quality){
		$this->getObject();
	    $to='+'.$tonumber;
	    $from='+'.$fromnumber;
		$vars['From']=$from;
		$vars['To']=$to;
		$vars['MediaUrl']=$media;
		$vars['Quality']=$quality;
		$vars['StatusCallback']=SITE_URL.'/twilios/faxcallstatus';
		$response = $this->send_fax($vars);
	   	return $response;
	}
	
	function getfax($faxid){
		$this->getObject();
		$response = $this->curl_request("GET",$faxid,'');
	   	return $response;
	}
        
    public function send_fax($params=array()) {
        return $this->curl_request('POST', '', $params);
    }
	
	private function curl_request($method, $path, $params) {
        // construct full url
		$url = $this->api;
		
		if (($method == "GET") || ($method == "DELETE")) {
		   $url = "{$this->api}/$path";
		}

		// initialize a new curl object   
		if($this->bulksms == 1){
		   $curl = $this->curlinit; 
		   curl_setopt($curl, CURLOPT_URL, $url);
		}else{
		   $curl = curl_init($url);  
		}
				
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	

		switch(strtoupper($method)) {
			case "GET":
				curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
				break;
			case "POST":
				curl_setopt($curl, CURLOPT_POST, TRUE);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
				//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				break;
			case "DELETE":
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
		 }
		
        curl_setopt($curl, CURLOPT_USERPWD,
                $pwd = "{$this->auth_id}:{$this->auth_token}");
                
        $options = array(
		        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		        CURLOPT_TIMEOUT => 30,
		        CURLOPT_CONNECTTIMEOUT => 30,
		        CURLOPT_HEADER => FALSE,
		        CURLOPT_VERBOSE => FALSE,
		    );

		curl_setopt_array($curl, $options);  
	
		if(FALSE === ($result = curl_exec($curl))){
			$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$err = curl_error($curl);
			curl_close($curl);
			return array("status" => $responseCode, "response" => array("error" => $err));
		}
	   $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	   if($this->bulksms == 0){
	     curl_close($curl);
	   }
	   
	   $result2 = json_decode($result, TRUE);
	   return array("status" => $responseCode, "response" => $result2);       
	   //return $result;       
    }
}
?>