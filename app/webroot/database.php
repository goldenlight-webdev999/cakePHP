<?php
    error_reporting(E_ALL ^ (E_STRICT | E_DEPRECATED | E_NOTICE | E_WARNING));

	$con = mysqli_connect("bicomm.cluster-czzqbbvrt5jf.us-west-2.rds.amazonaws.com","admin","b1c0m#2020Com");
	
    if (!$con){
	   die('Could not connect: ' . mysqli_error());
	}


	// $servername = "bicomm.cluster-czzqbbvrt5jf.us-west-2.rds.amazonaws.com";
	// $username = "bicomm";
	// $password = "b1c0m#2020Com";

	// $con = null;
	// try {
	//   $con = new PDO("mysql:host=$servername;dbname=bicomm", $username, $password);
	//   // set the PDO error mode to exception
	//   $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//   echo "Connected successfully";
	// } catch(PDOException $e) {
	//   echo "Connection failed: " . $e->getMessage();
	// }

    mysqli_select_db($con,"bicomm"); 

	//fetch data from configs	   
    $configs = mysqli_query($con,"SELECT * FROM configs"); 
	$configdata = mysqli_fetch_array($configs); 

	$dbconfigs = mysqli_query($con,"SELECT * FROM dbconfigs");
	$dbconfigsdata = mysqli_fetch_array($dbconfigs);

	define('SITEURL',$configdata['site_url']);
	define('NEXMO_KEY',$configdata['nexmo_key']);
	define('NEXMO_SECRET',$configdata['nexmo_secret']);

	define('TWILIO_ACCOUNTSID',$configdata['twilio_accountSid']);
	define('TWILIO_AUTH_TOKEN',$configdata['twilio_auth_token']);

    define('PLIVO_KEY',$configdata['plivo_key']);
	define('PLIVO_TOKEN',$configdata['plivo_token']);
    define('PLIVOAPP_ID',$configdata['plivoapp_id']);

	define('DB_USERNAME',$dbconfigsdata['dbusername']);
	define('DB_NAME',$dbconfigsdata['dbname']);
	define('DB_PASSWORD',$dbconfigsdata['dbpassword']);
	  

?>
