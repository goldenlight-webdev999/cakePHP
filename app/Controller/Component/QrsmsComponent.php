<?php 

App::import('Vendor','Qrsms',array('file'=>'BarcodeQR/BarcodeQR.php'));
class QrsmsComponent extends Component {
	function __construct(){
		$this->getObject();
	}
    function startup(Controller $controller ) {
        $this->controller = $controller;
    }
	function getObject(){		
			$this->client = new BarcodeQR();
	}
	function sms($phone,$body){
			 $response =  $this->client->sms($phone,$body);
	}
	function draw($size,$filename){
			 $response =  $this->client->draw($size,$filename);
	}
	function url($url){
			 $response =  $this->client->url($url);
	}
	function text($text){
			 $response =  $this->client->url($text);
	}
}
?>