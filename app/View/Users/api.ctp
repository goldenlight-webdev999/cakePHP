<link href="<?php echo SITE_URL; ?>/assets/global/plugins/codemirror/lib/codemirror.css" rel="stylesheet" type="text/css">
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/codemirror/theme/ambiance.css" rel="stylesheet" type="text/css">
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/pages/scripts/components-code-editors.js" type="text/javascript"></script>
<style>
    .CodeMirror {
   height: auto;
}
</style>
<div class="page-content-wrapper">
	<div class="page-content">
		<h3 class="page-title">API Integration</h3>
		<div class="page-bar">
		  <ul class="page-breadcrumb">
			<li> <i class="icon-home"></i> <a href="<?php echo SITE_URL; ?>/users/dashboard">Home</a> <i class="fa fa-angle-right"></i> </li>
			<li> <span>API</span> </li>
		  </ul>
		</div>
		<div class="row">
<div class="col-md-12 "> 
				<!-- BEGIN SAMPLE FORM PORTLET-->
<?php  echo $this->Session->flash(); ?>	
				<div class="portlet light ">
					<div class="portlet-title">
						<div class="caption font-red-sunglo">
							<i class="fa fa-code font-red-sunglo"></i>
							<span class="caption-subject bold uppercase">API Integration</span>
						</div>
					</div>
					<div class="portlet-body form">
					   <!-- <div align="center">-->
				        <font size="5">Your API Key: <b><?php echo $apiKey;?></b></font>&nbsp;&nbsp;&nbsp;<?php echo $this->Html->link('<i class="icon-refresh" style="font-size:16px;"></i>',array('action'=>'regenerate'),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Re-Generate API Key','style'=>'padding:4px;padding-bottom:2px;margin-right:0px;margin-top:-5px')); ?>
				        <br><br>
				        <div class="note note-warning"><b>NOTE:</b> DO NOT SHARE YOUR API KEY WITH <u>ANYONE</u></div>
				        
<div style="border-style: solid; border-width:1px; border-color: #e3e3e3">
<div style="text-align:center;margin-top:-40px">
<br/><h3 style="background-color:#ededed;padding: 10px 0px 10px 0px">Table of Contents</h3><br/>
</div>
<div>
<ul><li>
					<a href="#AddContact">Contacts</a>
<ul><li>
							<a href="#AddContact">Add Contacts</a>
						</li>
<li>
							<a href="#GetContacts">Get Contacts</a>
						</li>

</ul></li>
<br/>
<li>
					<a href="#SendingBulk">Sending/Scheduling Messages</a>
<ul><li>
							<a href="#SendingBulk">Send/Schedule Bulk SMS to a Group</a>
						</li>
<li>
							<a href="#SendingContact">Send/Schedule SMS to a Contact</a>
						</li>
<li>
							<a href="#GetStatsBulk">Get Bulk SMS Delivery Stats For a Group</a>
						</li>
<li>
							<a href="#GetStatsContact">Get SMS Delivery Stats For a Contact</a>
						</li>
</ul></li>
<br/>
<li>
					<a href="#CheckCredits">Credits</a>
<ul><li>
							<a href="#CheckCredits">Check SMS/Voice Credit Balance</a>
						</li>
</ul></li>
<br/>
<li>
					<a href="#AddAppointments">Appointments</a>
<ul><li>
							<a href="#AddAppointments">Add Appointments</a>
						</li>
</ul></li>

</ul></div>
<br/>
</div>
<br/>
<a id="AddContact" name="AddContact"></a>
				        <h3>
		<strong>Add Contacts</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
    <tbody><tr><td>
<h4>
						Description<br /></h4>
</td>
</tr><tr><td>
					Add contacts to group specified. Accepts GET or POST requests.
				</td>
</tr></tbody></table>
				        
				        <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
					<a href="<?php echo SITE_URL; ?>/apis/addcontact/"><?php echo SITE_URL; ?>/apis/addcontact/</a>
				</td>
</tr><tr><td style="word-wrap: break-word">
					Example call:<br />
				<a href="<?php echo SITE_URL; ?>/apis/addcontact/?apikey=APIKEY&amp;group=GROUPNAME&amp;number=PHONENUMBER&amp;name=NAME&amp;email=EMAIL&amp;bday=BDAY"><?php echo SITE_URL; ?>/apis/addcontact/?apikey=APIKEY&amp;group=GROUPNAME&amp;number=PHONENUMBER&amp;name=NAME&amp;email=EMAIL&amp;bday=BDAY</a>
				</td>
</tr>
<tr><td style="word-wrap: break-word">
					PHP Code Sample (cURL):<br />
					<textarea id="code_editor_demo_1">$url = '<?php echo SITE_URL; ?>/apis/addcontact/';		
$fields = array('apikey' => $api_key,
'group' => $group_name,
'number' => $number,
'name' => $name,
'email' => $email,
'bday' => $bday,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
				        </textarea>
				</td>
</tr>
</tbody>
</table>


<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
						Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>apikey</strong>
					</div>
</td>
<td>
					(Required) The API key of your account.
				</td>
</tr><tr><td>
<div>
						<strong>group</strong>
					</div>
</td>
<td>
					(Required) The name of the group you are associating this contact to. Must be a valid group name that already exists in the system.
				</td>
</tr><tr><td>
					<strong>number</strong>
				</td>
<td>
					(Required) The number of the contact. NO spaces, dashes, or parentheses in phone numbers.<br/><br/><b>Include country code in the number 
US Example: 12025248725 UK Example: 447481340516</b>

				</td>
</tr><tr><td>
<div>
						<strong>name</strong>
					</div>
</td>
<td>
					(Optional) The name of the contact.
				</td>
</tr><tr><td>
<div>
						<strong>email</strong>
					</div>
</td>
<td>
					(Optional) The email address of the contact.
				</td>
</tr><tr><td>
					<strong>bday</strong>
				</td>
<td>
					(Optional) The birthday of the contact. <b>Format: YYYY-MM-DD</b>
				</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
						Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>0</strong>
					</div>
</td>
<td>
					0 is passed back on successful API call.
				</td>
</tr><tr><td>
<div>
						<strong>-1</strong>
					</div>
</td>
<td>
					Invalid API key passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-2</strong>
					</div>
</td>
<td>
					There was no number passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-3</strong>
					</div>
</td>
<td>
					Group and number required.
				</td>
</tr><tr><td>
<div>
						<strong>-4</strong>
					</div>
</td>
<td>
					There was no group passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-5</strong>
					</div>
</td>
<td>
					The group passed in is invalid or does not exist in the group list.
				</td>
</tr>
<tr><td>
<div>
<strong>-6</strong>
</div>
</td>
<td>
Other error. 
</td>
</tr>
<tr><td>
<div>
<strong>-7</strong>
</div>
</td>
<td>
Number is already subscribed for [group name].
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
						<textarea id="code_editor_demo_6">{
"status":"-7",
"msg":"Number is already subscribed for home"
}</textarea>
</td>
</tr>

</tbody></table>
<br/>
<a id="GetContacts" name="GetContacts"></a>
<h3> <strong>Get Contacts</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
   <tbody><tr><td>
<h4>
Description<br /></h4>
</td>
</tr><tr><td>
Return contacts based on optional search parameters passed in. Accepts GET or POST requests.
</td>
</tr></tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%"><tbody><tr><td>
<h4>
URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
<a href="<?php echo SITE_URL; ?>/apis/getcontacts/"><?php echo SITE_URL; ?>/apis/getcontacts/</a>
</td>
</tr><tr><td style="word-wrap: break-word">
Example call:<br />
<a href="<?php echo SITE_URL; ?>/apis/getcontacts/?apikey=APIKEY&amp;type=TYPE"><?php echo SITE_URL; ?>/apis/getcontacts/?apikey=APIKEY&amp;name=jimmy</a>
</td>
</tr>
<tr><td style="word-wrap: break-word">
PHP Code Sample (cURL):<br />
<textarea id="code_editor_demo_11">$url = '<?php echo SITE_URL; ?>/apis/getcontacts/';
$fields = array('apikey' => $api_key,
'name' => $name,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
       </textarea>
</td>
</tr>
</tbody>
</table>
       

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
<strong>apikey</strong>
</div>
</td>
<td>
(Required) The API key of your account.
</td>
</tr><tr><td>
<div>
<strong>name</strong>
</div>
</td>
<td>
(Optional) Name passed in to search on.
</td>
</tr>
<tr><td>
<div>
<strong>email</strong>
</div>
</td>
<td>
(Optional) Email passed in to search on.
</td>
</tr>
<tr><td>
<div>
<strong>number</strong>
</div>
</td>
<td>
(Optional) Number passed in to search on.
</td>
</tr>
<tr><td>
<div>
<strong>group</strong>
</div>
</td>
<td>
(Optional) The group name to filter the contacts. Must pass in a valid group name that exists in the group list.
</td>
</tr>
<tr><td>
<div>
<strong>source</strong>
</div>
</td>
<td>
(Optional) The signup source to filter the contacts. Valid values are 0, 1, 2, 3.
0=>import, 1=>sms, 2=>web widget, 3=>kiosk
</td>
</tr>
<tr><td>
<div>
<strong>subscribed</strong>
</div>
</td>
<td>
(Optional) Opted out / opted in contacts to filter on. Valid values are 0, 1. 0=>subscribed, 1=>unsubscribed.
</td>
</tr>
<tr><td>
<div>
<strong>sortby</strong>
</div>
</td>
<td>
(Optional) Field to sort by. Valid values are name, phone_number, created. Default is created in desc order.
</td>
</tr>
<tr><td>
<div>
<strong>sortdir</strong>
</div>
</td>
<td>
(Optional) Direction of sorting. Available values are asc, desc. Default is created in desc order.
</td>
</tr>
<tr><td>
<div>
<strong>limit</strong>
</div>
</td>
<td>
(Optional) Used to return maximum number of contacts per page. Default is 50.
</td>
</tr>
<tr><td>
<div>
<strong>page</strong>
</div>
</td>
<td>
(Optional) Page of results to retrieve.
</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
<strong>Contacts</strong>
</div>
</td>
<td>
List of contacts returned on successful API call.
</td>
</tr><tr><td>
<div>
<strong>-1</strong>
</div>
</td>
<td>
Invalid API key passed in.
</td>
</tr>
<tr><td>
<div>
<strong>-2</strong>
</div>
</td>
<td>
The group passed in is invalid or does not exist.
</td>
</tr>
<tr><td>
<div>
<strong>-3</strong>
</div>
</td>
<td>
The source passed in is invalid. Proper values are 0, 1, 2, 3. 
</td>
</tr>
<tr><td>
<div>
<strong>-4</strong>
</div>
</td>
<td>
The subscribed passed in is invalid. Proper values are 0, 1. 
</td>
</tr>
<tr><td>
<div>
<strong>-5</strong>
</div>
</td>
<td>
The sortby passed in is invalid. Proper values are name, phone_number, created. 
</td>
</tr>
<tr><td>
<div>
<strong>-6</strong>
</div>
</td>
<td>
The sortdir passed in is invalid. Proper values are asc, desc. 
</td>
</tr>
<tr><td>
<div>
<strong>-7</strong>
</div>
</td>
<td>
No contacts found. 
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_12">{ 
	"status":"0",		
    "contacts": [
    {
        "id": "23",
        "name": "John Doe",
        "birthday": "1974-03-23",
        "email": "johndoe@gmail.com",
        "number": "14154009186",
        "group": "Group One",
        "subscriber": "Yes",
        "source": "SMS",
        "carrier":"Sprint",
        "date": "2017-03-12 11:02:04"
        },
    {
        "id": "24",
        "name": "John Smith",
        "birthday": "1978-01-13",
        "email": "",
        "number": "18159819712",
        "group": "Group Two",
        "subscriber": "Yes",
        "source": "Kiosk",
        "carrier":"",
        "date": "2016-12-17 13:12:04"
        }
    ]
}</textarea>
				</td>
</tr>

</tbody></table>

<br/>
<a id="SendingBulk" name="SendingBulk"></a>
<h3>
		<strong>Send/Schedule Bulk SMS to a Group</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped"><tbody><tr><td>
<h4>
						Description<br /></h4>
</td>
</tr><tr><td>
					Send or Schedule a bulk SMS to a specific group. Accepts GET or POST requests.
				</td>
</tr></tbody></table>
				        
				        <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%"><tbody><tr><td>
<h4>
						URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
					<a href="<?php echo SITE_URL; ?>/apis/smsgroup/"><?php echo SITE_URL; ?>/apis/smsgroup/</a>
				</td>
</tr><tr><td style="word-wrap: break-word">
					Example call:<br />
					<a href="<?php echo SITE_URL; ?>/apis/smsgroup/?apikey=APIKEY&amp;from=FROMNUMBER&amp;to=GROUPNAME&amp;message=MESSAGE&amp;rotate=1"><?php echo SITE_URL; ?>/apis/smsgroup/?apikey=APIKEY&amp;from=FROMNUMBER&amp;to=GROUPNAME&amp;message=MESSAGE&amp;rotate=1</a>
				</td>
</tr>
<tr><td style="word-wrap: break-word">
					PHP Code Sample (cURL):<br />
					<textarea id="code_editor_demo_2">$url = '<?php echo SITE_URL; ?>/apis/smsgroup/';
$fields = array('apikey' => $api_key,
'from' => $from,
'to' => $to,
'message' => $message,
'rotate' => $rotate,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
				        </textarea>
				</td>
</tr>
</tbody>
</table>
      

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
						Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>apikey</strong>
					</div>
</td>
<td>
					(Required) The API key of your account.
				</td>
</tr><tr><td>
<div>
						<strong>from</strong>
					</div>
</td>
<td>
					(Required) The number you are sending from. Could be any SMS-enabled number in your account.
				</td>
</tr><tr><td>
					<strong>to</strong>
				</td>
<td>
					(Required) The group name you are sending the message to. Must be a valid group name that exists in your group list.</b>

				</td>
</tr><tr><td>
<div>
						<strong>message</strong>
					</div>
</td>
<td>
					(Required) The message text you are sending. 1 credit is charged for each 160 character segment. If you have a message that is 300 characters, and you are sending to 10 people, 20 credits will be deducted (2 credits for each person).
<br/><br/>NOTE: Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment.

				</td>
</tr><tr><td>
<div>
						<strong>alphasender</strong>
					</div>
</td>
<td>
					(Optional) This is the alphanumeric sender ID you want to send the message from. Only certain countries can send from an alphanumeric sender id, which are included below:
AUSTRALIA, AUSTRIA, DENMARK, ESTONIA, FINLAND, FRANCE, GERMANY, HONG KONG, IRELAND, ISRAEL, LITHUANIA, NETHERLANDS, NORWAY, POLAND, SPAIN, SWEDEN, SWITZERLAND, UNITED KINGDOM<br/><br/>
Alphanumeric SenderID requirements: Any combination of 1 to 11 letters(A-Z/a-z) and numbers(0-9). 1 letter and no more than 11 alphanumeric characters may be used.

				</td>
</tr><tr><td>
					<strong>rotate</strong>
				</td>
<td>
					(Optional) Flag if you want to rotate through all your numbers in your account when sending the message. Set to 1 if you want to rotate. If nothing is passed in, it will NOT rotate through your numbers.
				</td>
</tr>
<tr><td>
					<strong>throttle</strong>
				</td>
<td>
					(Optional) Sending throttle to control the sending rate of the message being sent. The default is 1, which will send the message at a rate of 1 SMS every 1 second per long code. If nothing is passed in, it will send at this rate. If you want to send a slower rate, you can pass in a 2, 3, 4, 5 or 6 which will send at a rate of 1 SMS every 2 seconds per long code, every 3 seconds, etc…The slowest setting which can be used is 6.
				</td>
</tr>
<tr><td>
					<strong>sendondate</strong>
				</td>
<td>
					(Optional) If scheduling bulk SMS, this is the date/time message will be sent. If it's a recurring event, this is the 1st date/time in the series.<br/><br/>
Format: DD-MM-YYYY HH:MM<br/>
Example: 20-03-2017 19:30

				</td>
</tr>
<tr><td>
					<strong>recurring</strong>
				</td>
<td>
					(Optional) Create recurring events. Set to 1 if you want to create a recurring event.
				</td>
</tr>
<tr><td>
					<strong>repeat</strong>
				</td>
<td>
					(Optional) Type of recurring event you want to schedule. Valid values are 'Daily', 'Weekly', 'Monthly', 'Yearly'.
				</td>
</tr>
<tr><td>
					<strong>frequency</strong>
				</td>
<td>
					(Optional) How often you want the recurring events to happen based on the Repeat parameter above. For example, if you pass in 'Daily' for repeat and '3' for Frequency, it will schedule the events every 3 days. Valid values are 1-30.
				</td>
</tr>
<tr><td>
					<strong>enddate</strong>
				</td>
<td>
					(Optional) End date you want the recurring events to end. For example, if you pass in a sendondate(start date) of Oct 10 at 6:30 and you want the last event to run on Nov 10, you must pass in an end date of Nov 10 at 6:30 so Nov 10 will be included as the last date.
<br/><br/>Format: DD-MM-YYYY HH:MM<br/>
Example: 20-03-2017 19:30

				</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
						Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>0 or group SMS ID</strong>
					</div>
</td>
<td>
					0 is passed back on successful API call when <u>scheduling</u> bulk SMS. The group SMS ID will be passed back on successful API call when sending bulk SMS immediately.
				</td>
</tr><tr><td>
<div>
						<strong>-1</strong>
					</div>
</td>
<td>
					Invalid API key passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-2</strong>
					</div>
</td>
<td>
					There was no 'from' number passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-3</strong>
					</div>
</td>
<td>
					The 'from' number passed in doesn't exist in your account or isn't SMS-enabled.
				</td>
</tr><tr><td>
<div>
						<strong>-4</strong>
					</div>
</td>
<td>
					There was no message passed in to be sent.
				</td>
</tr><tr><td>
<div>
						<strong>-5</strong>
					</div>
</td>
<td>
					There was no group passed in.
				</td>
</tr>
<tr><td>
<div>
						<strong>-6</strong>
					</div>
</td>
<td>
					The group passed in is invalid or does not exist.
				</td>
</tr>
<tr><td>
<div>
						<strong>-7</strong>
					</div>
</td>
<td>
					The alphanumeric sender ID passed in is either invalid(doesn't match requirements below) OR the user account does not have permission to send from an alphanumeric sender ID.<br/>
Requirements: You may use any combination of 1 to 11 letters(A-Z/a-z) and numbers(0-9). 1 letter and no more than 11 alphanumeric characters may be used.

				</td>
</tr>
<tr><td>
<div>
						<strong>-8</strong>
					</div>
</td>
<td>
					The rotate flag passed in is invalid. Proper values are either 1 or 0.
				</td>
</tr>
<tr><td>
<div>
						<strong>-9</strong>
					</div>
</td>
<td>
					The sending throttle flag passed is invalid. Proper values are 1, 2, 3, 4, 5, or 6.
				</td>
</tr>
<tr><td>
<div>
						<strong>-10</strong>
					</div>
</td>
<td>
					The recurring flag passed in is invalid. Proper values are either 1 or 0.
				</td>
</tr>
<tr><td>
<div>
						<strong>-11</strong>
					</div>
</td>
<td>
					The repeat parameter passed in is invalid. Proper values are 'Daily', 'Weekly', 'Monthly', 'Yearly'. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.
				</td>
</tr>
<tr><td>
<div>
						<strong>-12</strong>
					</div>
</td>
<td>
					The frequency parameter passed in is invalid. Proper values are 1-30. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.
				</td>
</tr>
<tr><td>
<div>
						<strong>-13</strong>
					</div>
</td>
<td>
				Must have a recurring end date if you want to schedule a recurring event. The recurring flag is 1 and nothing passed in for enddate. 
				</td>
</tr>
<tr><td>
<div>
						<strong>-14</strong>
					</div>
</td>
<td>
					Not enough credits to send the message.
				</td>
</tr>
<tr><td>
<div>
<strong>-15</strong>
</div>
</td>
<td>
No subscribers found in group.
</td>
</tr>
<tr><td>
<div>
<strong>-16</strong>
</div>
</td>
<td>
Other error.
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_7">{
"status":"-4",
"msg":"There was no message passed in to be sent."
}</textarea>
				</td>
</tr>
</tbody></table>
<br/>
<a id="GetStatsBulk" name="GetStatsBulk"></a>
<h3>
<strong>Get Bulk SMS Delivery Stats For a Group</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped"><tbody><tr><td>
<h4>
Description<br /></h4>
</td>
</tr><tr><td>
This gets the summarized delivery stats for the bulk SMS sent for the group SMS ID. # of successful and # of failed messages. Accepts GET or POST requests.
</td>
</tr></table>
       
  <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%"><tbody><tr><td>
<h4>
URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
<a href="<?php echo SITE_URL; ?>/apis/getbulksmsdeliveryreport/"><?php echo SITE_URL; ?>/apis/getbulksmsdeliveryreport/</a>
</td>
</tr><tr><td style="word-wrap: break-word">
Example call:<br />
<a href="<?php echo SITE_URL; ?>/apis/getbulksmsdeliveryreport/?apikey=APIKEY&amp;groupsmsid=GROUPSMSID"><?php echo SITE_URL; ?>/apis/getbulksmsdeliveryreport/?apikey=APIKEY&amp;groupsmsid=GROUPSMSID</a>
</td>
</tr>
<tr><td style="word-wrap: break-word">
PHP Code Sample (cURL):<br />
<textarea id="code_editor_demo_13">$url = '<?php echo SITE_URL; ?>/apis/getbulksmsdeliveryreport/';
$fields = array('apikey' => $api_key,
'groupsmsid' => $groupsmsid,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
       </textarea>
</td>
</tr>
</tbody>
</table>
       

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
<strong>apikey</strong>
</div>
</td>
<td>
(Required) The API key of your account.
</td>
</tr><tr><td>
<div>
<strong>groupsmsid</strong>
</div>
</td>
<td>
(Required) The group SMS ID you want to get the delivery stats for. This ID will be returned on successful API call for Send Bulk SMS to Group. It will NOT be returned when scheduling bulk SMS.
</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
<strong>successful</strong>
</div>
</td>
<td>
The number of successful messages.
</td>
</tr>
<tr><td>
<div>
<strong>failed</strong>
</div>
</td>
<td>
The number of failed messages.
</td>
</tr>
<tr><td>
<div>
<strong>-1</strong>
</div>
</td>
<td>
Invalid API key passed in.
</td>
</tr><tr><td>
<div>
<strong>-2</strong>
</div>
</td>
<td>
The group SMS ID is missing. You must pass this in to get delivery stats for this group.
</td>
</tr>
<tr><td>
<div>
<strong>-3</strong>
</div>
</td>
<td>
The group SMS ID is invalid or does not exist.
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_14">{
"successful":"145",
"failed":"7"
}</textarea>
				</td>
</tr>

</tbody></table>


<br/>
<a id="SendingContact" name="SendingContact"></a>
<h3>
		<strong>Send/Schedule SMS to a Contact</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped"><tbody><tr><td>
<h4>
						Description<br /></h4>
</td>
</tr><tr><td>
					Send or Schedule a SMS to a specific contact. Accepts GET or POST requests.
				</td>
</tr></table>
				        
				        <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%"><tbody><tr><td>
<h4>
						URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
					<a href="<?php echo SITE_URL; ?>/apis/smscontact/"><?php echo SITE_URL; ?>/apis/smscontact/</a>
				</td>
</tr><tr><td style="word-wrap: break-word">
					Example call:<br />
					<a href="<?php echo SITE_URL; ?>/apis/smscontact/?apikey=APIKEY&amp;from=FROMNUMBER&amp;to=CONTACTNUMBER&amp;message=MESSAGE"><?php echo SITE_URL; ?>/apis/smscontact/?apikey=APIKEY&amp;from=FROMNUMBER&amp;to=CONTACTNUMBER&amp;message=MESSAGE</a>
				</td>
</tr>
<tr><td style="word-wrap: break-word">
					PHP Code Sample (cURL):<br />
					<textarea id="code_editor_demo_3">$url = '<?php echo SITE_URL; ?>/apis/smscontact/';
$fields = array('apikey' => $api_key,
'from' => $from,
'to' => $to,
'message' => $message,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
				        </textarea>
				</td>
</tr>
</tbody>
</table>
				        

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
						Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>apikey</strong>
					</div>
</td>
<td>
					(Required) The API key of your account.
				</td>
</tr><tr><td>
<div>
						<strong>from</strong>
					</div>
</td>
<td>
					(Required) The number you are sending from. Could be any SMS-enabled number in your account.
				</td>
</tr><tr><td>
					<strong>to</strong>
				</td>
<td>
					(Required) The contact number you are sending the message to.<br/><br/>
Example: 18159819712<br/>
NO spaces, dashes, or parentheses in phone numbers. Include country code in the number 

</b>

				</td>
</tr><tr><td>
<div>
						<strong>message</strong>
					</div>
</td>
<td>
					(Required) The message text you are sending. 1 credit is charged for each 160 character segment.
<br/><br/>NOTE: Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment.

				</td>
</tr><tr><td>
<div>
						<strong>alphasender</strong>
					</div>
</td>
<td>
					(Optional) This is the alphanumeric sender ID you want to send the message from. Only certain countries can send from an alphanumeric sender id, which are included below:
AUSTRALIA, AUSTRIA, DENMARK, ESTONIA, FINLAND, FRANCE, GERMANY, HONG KONG, IRELAND, ISRAEL, LITHUANIA, NETHERLANDS, NORWAY, POLAND, SPAIN, SWEDEN, SWITZERLAND, UNITED KINGDOM<br/><br/>
Alphanumeric SenderID requirements: Any combination of 1 to 11 letters(A-Z/a-z) and numbers(0-9). 1 letter and no more than 11 alphanumeric characters may be used.

				</td>
</tr>
<tr><td>
					<strong>sendondate</strong>
				</td>
<td>
					(Optional) If scheduling SMS, this is the date/time message will be sent. If it's a recurring event, this is the 1st date/time in the series.<br/><br/>
Format: DD-MM-YYYY HH:MM<br/>
Example: 20-03-2017 19:30

				</td>
</tr>
<tr><td>
					<strong>recurring</strong>
				</td>
<td>
					(Optional) Create recurring events. Set to 1 if you want to create a recurring event.
				</td>
</tr>
<tr><td>
					<strong>repeat</strong>
				</td>
<td>
					(Optional) Type of recurring event you want to schedule. Valid values are 'Daily', 'Weekly', 'Monthly', 'Yearly'.
				</td>
</tr>
<tr><td>
					<strong>frequency</strong>
				</td>
<td>
					(Optional) How often you want the recurring events to happen based on the Repeat parameter above. For example, if you pass in 'Daily' for repeat and '3' for Frequency, it will schedule the events every 3 days. Valid values are 1-30.
				</td>
</tr>
<tr><td>
					<strong>enddate</strong>
				</td>
<td>
					(Optional) End date you want the recurring events to end. For example, if you pass in a sendondate(start date) of Oct 10 at 6:30 and you want the last event to run on Nov 10, you must pass in an end date of Nov 10 at 6:30 so Nov 10 will be included as the last date.
<br/><br/>Format: DD-MM-YYYY HH:MM<br/>
Example: 20-03-2017 19:30

				</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
						Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>0</strong>
					</div>
</td>
<td>
					0 is passed back on successful API call.
				</td>
</tr><tr><td>
<div>
						<strong>-1</strong>
					</div>
</td>
<td>
					Invalid API key passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-2</strong>
					</div>
</td>
<td>
					There was no 'from' number passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-3</strong>
					</div>
</td>
<td>
					The 'from' number passed in doesn't exist in your account or isn't SMS-enabled.
				</td>
</tr><tr><td>
<div>
						<strong>-4</strong>
					</div>
</td>
<td>
					There was no message passed in to be sent.
				</td>
</tr><tr><td>
<div>
						<strong>-5</strong>
					</div>
</td>
<td>
					There was no contact number passed in.
				</td>
</tr>
<tr><td>
<div>
						<strong>-6</strong>
					</div>
</td>
<td>
					The contact number passed in is either invalid, does not exist in your contact list, or has unsubscribed.
				</td>
</tr>
<tr><td>
<div>
						<strong>-7</strong>
					</div>
</td>
<td>
					The alphanumeric sender ID passed in is either invalid(doesn't match requirements below) OR the user account does not have permission to send from an alphanumeric sender ID.<br/>
Requirements: You may use any combination of 1 to 11 letters(A-Z/a-z) and numbers(0-9). 1 letter and no more than 11 alphanumeric characters may be used.

				</td>
</tr>

<tr><td>
<div>
						<strong>-8</strong>
					</div>
</td>
<td>
					The recurring flag passed in is invalid. Proper values are either 1 or 0.
				</td>
</tr>
<tr><td>
<div>
						<strong>-9</strong>
					</div>
</td>
<td>
					The repeat parameter passed in is invalid. Proper values are 'Daily', 'Weekly', 'Monthly', 'Yearly'. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.
				</td>
</tr>
<tr><td>
<div>
						<strong>-10</strong>
					</div>
</td>
<td>
					The frequency parameter passed in is invalid. Proper values are 1-30. Also, if recurring flag is 1 and nothing passed in for this field will trip this error.
				</td>
</tr>
<tr><td>
<div>
						<strong>-11</strong>
					</div>
</td>
<td>
				Must have a recurring end date if you want to schedule a recurring event. The recurring flag is 1 and nothing passed in for enddate. 
				</td>
</tr>
<tr><td>
<div>
						<strong>-12</strong>
					</div>
</td>
<td>
					Not enough credits to send the message.
				</td>
</tr>
<tr><td>
<div>
<strong>-16</strong>
</div>
</td>
<td>
Other error.
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_8">{
"status":"-3",
"msg":"The 'from' number passed in doesn't exist in your account or isn't SMS-enabled."
}</textarea>
				</td>
</tr>
</tbody></table>
<br/>
<a id="GetsStatsContact" name="GetStatsContact"></a>
<h3>
<strong>Get SMS Delivery Stats For a Contact</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped"><tbody><tr><td>
<h4>
Description<br /></h4>
</td>
</tr><tr><td>
This gets the delivery status for the most recent SMS sent to the contact. Accepts GET or POST requests.
</td>
</tr></table>
       
  <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%"><tbody><tr><td>
<h4>
URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
<a href="<?php echo SITE_URL; ?>/apis/getcontactsmsdeliveryreport/"><?php echo SITE_URL; ?>/apis/getcontactsmsdeliveryreport/</a>
</td>
</tr><tr><td style="word-wrap: break-word">
Example call:<br />
<a href="<?php echo SITE_URL; ?>/apis/getcontactsmsdeliveryreport/?apikey=APIKEY&amp;number=NUMBER"><?php echo SITE_URL; ?>/apis/getcontactsmsdeliveryreport/?apikey=APIKEY&amp;number=NUMBER</a>
</td>
</tr>
<tr><td style="word-wrap: break-word">
PHP Code Sample (cURL):<br />
<textarea id="code_editor_demo_15">$url = '<?php echo SITE_URL; ?>/apis/getcontactsmsdeliveryreport/';
$fields = array('apikey' => $api_key,
'number' => $number,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
       </textarea>
</td>
</tr>
</tbody>
</table>
       

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
<strong>apikey</strong>
</div>
</td>
<td>
(Required) The API key of your account.
</td>
</tr><tr><td>
<div>
<strong>number</strong>
</div>
</td>
<td>
(Required) Number for the contact you want to get the most recent delivery status for. It will only return most recent outbound message status for this number. <br/><br/>
Include country code in the number<br/>
US Example: 12025248725 UK Example: 447481340516

</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
<strong>smsstatus</strong>
</div>
</td>
<td>
The sms status of the most recent message sent to contact.
</td>
</tr>
<tr><td>
<div>
<strong>errormsg</strong>
</div>
</td>
<td>
The error message of the sms if it failed or was undelivered.
</td>
</tr>
<tr><td>
<div>
<strong>-1</strong>
</div>
</td>
<td>
Invalid API key passed in.
</td>
</tr><tr><td>
<div>
<strong>-2</strong>
</div>
</td>
<td>
Number passed in is blank.
</td>
</tr>
<tr><td>
<div>
<strong>-3</strong>
</div>
</td>
<td>
Number passed in is invalid or can't be found for outbound SMS sent to this number.
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_16">{
"smsstatus":"delivered",
"errormsg":""
}</textarea>
				</td>
</tr>

</tbody></table>

<br/>
<a id="CheckCredits" name="CheckCredits"></a>
<h3>
		<strong>Check SMS/Voice Credit Balance</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped"><tbody><tr><td>
<h4>
						Description<br /></h4>
</td>
</tr><tr><td>
					Check SMS or voice credit balances on user account. Accepts GET or POST requests.
				</td>
</tr></table>
				        
				        <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%"><tbody><tr><td>
<h4>
						URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
					<a href="<?php echo SITE_URL; ?>/apis/getcreditbalance/"><?php echo SITE_URL; ?>/apis/getcreditbalance/</a>
				</td>
</tr><tr><td style="word-wrap: break-word">
					Example call:<br />
					<a href="<?php echo SITE_URL; ?>/apis/getcreditbalance/?apikey=APIKEY&amp;type=TYPE"><?php echo SITE_URL; ?>/apis/getcreditbalance/?apikey=APIKEY&amp;type=TYPE</a>
				</td>
</tr>
<tr><td style="word-wrap: break-word">
					PHP Code Sample (cURL):<br />
					<textarea id="code_editor_demo_4">$url = '<?php echo SITE_URL; ?>/apis/getcreditbalance/';
$fields = array('apikey' => $api_key,
'type' => $type,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
				        </textarea>
				</td>
</tr>
</tbody>
</table>
        

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
						Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>apikey</strong>
					</div>
</td>
<td>
					(Required) The API key of your account.
				</td>
</tr><tr><td>
<div>
						<strong>type</strong>
					</div>
</td>
<td>
					(Required) Flag to determine SMS or Voice credit balance. 1 to return SMS credit balance and 2 for Voice.
				</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
						Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>Credit balance number</strong>
					</div>
</td>
<td>
					Your credit balance is returned on successful API call.
				</td>
</tr><tr><td>
<div>
						<strong>-1</strong>
					</div>
</td>
<td>
					Invalid API key passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-2</strong>
					</div>
</td>
<td>
					Invalid type passed in. Should only be either a 1 or 2 depending on what credit balance you want to retrieve. 1 for SMS and 2 for Voice.
				</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_9">{
"creditbalancenumber":"145",
"msg":"Your credit balance is returned on successful API call"
}</textarea>
				</td>
</tr>


</tbody></table>
<br/>
<a id="AddAppointments" name="AddAppointments"></a>
<h3>
		<strong>Add Appointments</strong><br /></h3>
<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
    <tbody><tr><td>
<h4>
						Description<br /></h4>
</td>
</tr><tr><td>
					Add contact appointments. Accepts GET or POST requests.
				</td>
</tr></tbody></table>
				        
				        <table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						URL<br /></h4>
</td>
</tr><tr><td style="word-wrap: break-word">
					<a href="<?php echo SITE_URL; ?>/apis/addappointment/"><?php echo SITE_URL; ?>/apis/addappointment/</a>
				</td>
</tr><tr><td style="word-wrap: break-word">
					Example call:<br />
				<a href="<?php echo SITE_URL; ?>/apis/addappointment/?apikey=APIKEY&amp;number=PHONENUMBER&amp;apptdate=APPTDATE&amp;status=STATUS"><?php echo SITE_URL; ?>/apis/addappointment/?apikey=APIKEY&amp;number=PHONENUMBER&amp;apptdate=APPTDATE&amp;status=STATUS</a>
				</td>
</tr>
<tr><td style="word-wrap: break-word">
					PHP Code Sample (cURL):<br />
					<textarea id="code_editor_demo_5">$url = '<?php echo SITE_URL; ?>/apis/addappointment/';		
$fields = array('apikey' => $api_key,
'number' => $number,
'apptdate' => $apptdate,
'status' => $status,
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$result = curl_exec($ch);
return $result;
				        </textarea>
				</td>
</tr>
</tbody>
</table>


<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody>
<tr><td colspan="2">
<h4>
						Parameters<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>apikey</strong>
					</div>
</td>
<td>
					(Required) The API key of your account.
				</td>
</tr><tr><td>
<div>
						<strong>number</strong>
					</div>
</td>
<td>
					(Required) The number of the contact. NO spaces, dashes, or parentheses in phone numbers.<br/><br/><b>Include country code in the number 
US Example: 12025248725 UK Example: 447481340516
				</td>
</tr><tr><td>
					<strong>apptdate</strong>
				</td>
<td>
					(Required) The appointment date/time.<br/><br/>
Format: DD-MM-YYYY HH:MM<br/>
Example: 20-03-2017 19:30

				</td>
</tr><tr><td>
<div>
						<strong>status</strong>
					</div>
</td>
<td>
					(Required) The status of the appointment. Valid values are 0, 1, 2, 3.<br/><br/>
					0=>Unconfirmed, 1=>Confirmed, 2=>Cancelled, 3=>Rescheduled 	
					
				</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped">
<tbody><tr><td colspan="2">
<h4>
						Return Values<br /></h4>
</td>
</tr><tr><td>
<div>
						<strong>0</strong>
					</div>
</td>
<td>
					0 is passed back on successful API call.
				</td>
</tr><tr><td>
<div>
						<strong>-1</strong>
					</div>
</td>
<td>
					Invalid API key passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-2</strong>
					</div>
</td>
<td>
					There was no contact number passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-3</strong>
					</div>
</td>
<td>
					Number passed in should only contain numbers, NO spaces, dashes, or parentheses in phone numbers.
				</td>
</tr><tr><td>
<div>
						<strong>-4</strong>
					</div>
</td>
<td>
					There was no appt date/time passed in.
				</td>
</tr><tr><td>
<div>
						<strong>-5</strong>
					</div>
</td>
<td>
					There was no status passed in.
				</td>
</tr>
<tr><td>
<div>
						<strong>-6</strong>
					</div>
</td>
<td>
					The status passed in is invalid. Valid values are 0, 1, 2, 3.
				</td>
</tr>
<tr><td>
<div>
<strong>-7</strong>
</div>
</td>
<td>
Contact not found.
</td>
</tr>
<tr><td>
<div>
<strong>-8</strong>
</div>
</td>
<td>
Appointment already exists for this contact and datetime.
</td>
</tr>
<tr><td>
<div>
<strong>-9</strong>
</div>
</td>
<td>
Other error.
</td>
</tr>
<tr><td>
<div>
<strong>-20</strong>
</div>
</td>
<td>
Level 1 does not have access to the API. API can only be used with Levels 2, 3, and 4.
</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" class="table table-striped" style="table-layout: fixed; width: 100%">
<tbody><tr><td>
<h4>
						Example API Response (data is returned in universal and lightweight JSON format)<br /></h4>
			<textarea id="code_editor_demo_10">{
"status":"-2",
"msg":"There was no contact number passed in."
}</textarea>
				</td>
</tr>
</tbody></table>
				        <!--</div>-->
						
				</div>
			</div>
		</div>
	</div>
</div>