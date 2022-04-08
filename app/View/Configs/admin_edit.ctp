<script>
/*$(document).ready(function (){
	$('#ConfigApiType1').click(function (){
		$('#twilio').hide();
		$('#nexmo').show();
		$('#plivo').hide();
    });
	$('#ConfigApiType2').click(function (){
		$('#twilio').hide();
		$('#nexmo').hide();
		$('#plivo').hide();
    });
	$('#ConfigApiType0').click(function (){
		$('#twilio').show();
		$('#nexmo').hide();
		$('#plivo').hide();
	});
	$('#ConfigApiType3').click(function (){
		$('#plivo').show();
		$('#nexmo').hide();
		$('#twilio').hide();
	});

	
    var ConfigApiType1=<?php echo $api_type;?>;
    if(ConfigApiType1==0){
	    $('#twilio').show();
		$('#nexmo').hide();
		$('#plivo').hide();
    }else if(ConfigApiType1==2){
		$('#twilio').hide();
		$('#nexmo').hide();
		$('#plivo').hide();
    }else if(ConfigApiType1==3){
		$('#twilio').hide();
		$('#nexmo').hide();
		$('#plivo').show();
    }else{
		$('#twilio').hide();
		$('#nexmo').show();
		$('#plivo').hide();
    }
});*/
   </script>
<ul  class="secondary_nav" style="margin-top:-19px">
				<?php
				$navigation = array(
					  	//'List Config' => '/admin/configs/index',
						'Config' => '/admin/configs/edit/1',
						'Stripe Config' => '/admin/paypals/stripeindex',
						'SMS Gateway/Country Config'=>'/admin/configs/gatewayindex/'
					   					   
				);				
				$matchingLinks = array();
				
				foreach ($navigation as $link) {
						if (preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base)))) {
								$matchingLinks[strlen($link)] = $link;
						}
				}
				
				krsort($matchingLinks);
				
				//$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
				$activeLink = !empty($matchingLinks)?array_shift($matchingLinks):'';
				$out = array();
				
				foreach ($navigation as $title => $link) {
						//$out[] = '<li>'.$html->link($title, $link, ife($link == $activeLink, array('class' => 'current'))).'</li>';
						$out[] = '<li>'.$this->Html->link($title, $link, $link == $activeLink ? array('class' => 'current'):'').'</li>';
				}
				
				echo join("\n", $out);
				?>			
</ul>
<br/><br/>
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<div style="display:table;width:100%"><div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-user" style="color:#26C281"></i>&nbsp;Client Stats :: Active Clients</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsall')?></td></tr></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Sent</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments-o" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Received</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-usd" aria-hidden="true" style="color:#26C281"></i> Financial Stats :: Revenue</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenuemonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueall')?></td></tr></table> </div>
</div>

<?php echo $this->Form->create('Config',array('enctype'=>'multipart/form-data','style'=>'width:98%'));?>
	<fieldset>
		<legend><?php echo('Config'); ?></legend>
			<!--<h3 style="margin-top: -40px">Server Time <?php echo date("Y-m-d h:i:s A",$_SERVER['REQUEST_TIME']);?></h3><br/>
			<p><font color=red><b>NOTE:</b></font> Select your current timezone at the bottom to display the correct date and time. The server date/time above will display the correct date/time based on your timezone that you select.</p><br/>-->
			<?php
				$Option7=array('Afghanistan'=>'Afghanistan',
				'Albania'=>'Albania',
				'Algeria'=>'Algeria',
				'American Samoa'=>'American Samoa',
				'Andorra'=>'Andorra',
				'Angola'=>'Angola',
				'Anguilla'=>'Anguilla',
				'Antarctica'=>'Antarctica',
				'Antigua and Barbuda'=>'Antigua and Barbuda',
				'Argentina'=>'Argentina',
				'Armenia'=>'Armenia',
				'Aruba'=>'Aruba',
				'Australia'=>'Australia',
				'Austria'=>'Austria',
				'Azerbaijan'=>'Azerbaijan',
				'Bahamas'=>'Bahamas',
				'Bahrain'=>'Bahrain',
				'Bangladesh'=>'Bangladesh',
				'Barbados'=>'Barbados',
				'Belarus'=>'Belarus',
				'Belgium'=>'Belgium',
				'Belize'=>'Belize',
				'Benin'=>'Benin',
				'Bermuda'=>'Bermuda',
				'Bhutan'=>'Bhutan',
				'Bolivia'=>'Bolivia',
				'Bosnia and Herzegovina'=>'Bosnia and Herzegovina',
				'Botswana'=>'Botswana',
				'Bouvet Island'=>'Bouvet Island',
				'Brazil'=>'Brazil',
				'British Indian Ocean Territory'=>'British Indian Ocean Territory',
				'Brunei Darussalam'=>'Brunei Darussalam',
				'Bulgaria'=>'Bulgaria',
				'Burkina Faso'=>'Burkina Faso',
				'Burundi'=>'Burundi',
				'Cambodia'=>'Cambodia',
				'Cameroon'=>'Cameroon',
				'Canada'=>'Canada',
				'Cape Verde'=>'Cape Verde',
				'Cayman Islands'=>'Cayman Islands',
				'Central African Republic'=>'Central African Republic',
				'Chad'=>'Chad',
				'Chile'=>'Chile',
				'China'=>'China',
				'Christmas Island'=>'Christmas Island',
				'Cocos Keeling Islands'=>'Cocos Keeling Islands',
				'Colombia'=>'Colombia',
				'Comoros'=>'Comoros',
				'Congo'=>'Congo',
				'Congo The Democratic Republic of The'=>'Congo The Democratic Republic of The',
				'Cook Islands'=>'Cook Islands',
				'Costa Rica'=>'Costa Rica',
				'Croatia'=>'Croatia',
				'Cuba'=>'Cuba',
				'Cyprus'=>'Cyprus',
				'Czech Republic'=>'Czech Republic',
				'Denmark'=>'Denmark',
				'Djibouti'=>'Djibouti',
				'Dominica'=>'Dominica',
				'Dominican Republic'=>'Dominican Republic',
				'Ecuador'=>'Ecuador',
				'Egypt'=>'Egypt',
				'El Salvador'=>'El Salvador',
				'Equatorial Guinea'=>'Equatorial Guinea',
				'Eritrea'=>'Eritrea',
				'Estonia'=>'Estonia',
				'Ethiopia'=>'Ethiopia',
				'Falkland Islands Malvinas'=>'Falkland Islands Malvinas',
				'Faroe Islands'=>'Faroe Islands',
				'Fiji'=>'Fiji',
				'Finland'=>'Finland',
				'France'=>'France',
				'French Guiana'=>'French Guiana',
				'French Polynesia'=>'French Polynesia',
				'French Southern Territories'=>'French Southern Territories',
				'Gabon'=>'Gabon',
				'Gambia'=>'Gambia',
				'Georgia'=>'Georgia',
				'Germany'=>'Germany',
				'Ghana'=>'Ghana',
				'Gibraltar'=>'Gibraltar',
				'Greece'=>'Greece',
				'Greenland'=>'Greenland',
				'Grenada'=>'Grenada',
				'Guadeloupe'=>'Guadeloupe',
				'Guam'=>'Guam',
				'Guatemala'=>'Guatemala',
				'Guernsey'=>'Guernsey',
				'Guinea'=>'Guinea',
				'Guinea-bissau'=>'Guinea-bissau',
				'Guyana'=>'Guyana',
				'Haiti'=>'Haiti',
				'Heard Island and Mcdonald Islands'=>'Heard Island and Mcdonald Islands',
				'Honduras'=>'Honduras',
				'Hong Kong'=>'Hong Kong',
				'Hungary'=>'Hungary',
				'Iceland'=>'Iceland',
				'India'=>'India',
				'Indonesia'=>'Indonesia',
				'Iran Islamic Republic of'=>'Iran Islamic Republic of',
				'Iraq'=>'Iraq',
				'Ireland'=>'Ireland',
				'Isle of Man'=>'Isle of Man',
				'Israel'=>'Israel',
				'Italy'=>'Italy',
				'Jamaica'=>'Jamaica',
				'Japan'=>'Japan',
				'Jersey'=>'Jersey',
				'Jordan'=>'Jordan',
				'Kazakhstan'=>'Kazakhstan',
				'Kenya'=>'Kenya',
				'Kiribati'=>'Kiribati',
				'Korea Republic of'=>'Korea Republic of',
				'Kuwait'=>'Kuwait',
				'Kyrgyzstan'=>'Kyrgyzstan',
				'Latvia'=>'Latvia',
				'Lebanon'=>'Lebanon',
				'Lesotho'=>'Lesotho',
				'Liberia'=>'Liberia',
				'Libyan Arab Jamahiriya'=>'Libyan Arab Jamahiriya',
				'Liechtenstein'=>'Liechtenstein',
				'Lithuania'=>'Lithuania',
				'Luxembourg'=>'Luxembourg',
				'Macao'=>'Macao',
				'Macedonia The Former Yugoslav Republic of'=>'Macedonia The Former Yugoslav Republic of',
				'Madagascar'=>'Madagascar',
				'Malawi'=>'Malawi',
				'Malaysia'=>'Malaysia',
				'Maldives'=>'Maldives',
				'Mali'=>'Mali',
				'Malta'=>'Malta',
				'Marshall Islands'=>'Marshall Islands',
				'Martinique'=>'Martinique',
				'Mauritania'=>'Mauritania',
				'Mauritius'=>'Mauritius',
				'Mayotte'=>'Mayotte',
				'Mexico'=>'Mexico',
				'Micronesi Federated States of'=>'Micronesia Federated States of',
				'Moldova Republic of'=>'Moldova Republic of',
				'Monaco'=>'Monaco',
				'Mongolia'=>'Mongolia',
				'Montenegro'=>'Montenegro',
				'Montserrat'=>'Montserrat',
				'Morocco'=>'Morocco',
				'Mozambique'=>'Mozambique',
				'Myanmar'=>'Myanmar',
				'Namibia'=>'Namibia',
				'Nauru'=>'Nauru',
				'Nepal'=>'Nepal',
				'Netherlands'=>'Netherlands',
				'Netherlands Antilles'=>'Netherlands Antilles',
				'New Caledonia'=>'New Caledonia',
				'New Zealand'=>'New Zealand',
				'Nicaragua'=>'Nicaragua',
				'Niger'=>'Niger',
				'Nigeria'=>'Nigeria',
				'Niue'=>'Niue',
				'Norfolk Island'=>'Norfolk Island',
				'Northern Mariana Islands'=>'Northern Mariana Islands',
				'Norway'=>'Norway',
				'Oman'=>'Oman',
				'Pakistan'=>'Pakistan',
				'Palau'=>'Palau',
				'Palestinian Territory Occupied'=>'Palestinian Territory Occupied',
				'Panama'=>'Panama',
				'Papua New Guinea'=>'Papua New Guinea',
				'Paraguay'=>'Paraguay',
				'Peru'=>'Peru',
				'Philippines'=>'Philippines',
				'Pitcairn'=>'Pitcairn',
				'Poland'=>'Poland',
				'Portugal'=>'Portugal',
				'Puerto Rico'=>'Puerto Rico',
				'Qatar'=>'Qatar',
				'Reunion'=>'Reunion',
				'Romania'=>'Romania',
				'Russian Federation'=>'Russian Federation',
				'Rwanda'=>'Rwanda',
				'Saint Helena'=>'Saint Helena',
				'Saint Kitts and Nevis'=>'Saint Kitts and Nevis',
				'Saint Lucia'=>'Saint Lucia',
				'Saint Pierre and Miquelon'=>'Saint Pierre and Miquelon',
				'Saint Vincent and The Grenadines'=>'Saint Vincent and The Grenadines',
				'Samoa'=>'Samoa',
				'San Marino'=>'San Marino',
				'Sao Tome and Principe'=>'Sao Tome and Principe',
				'Saudi Arabia'=>'Saudi Arabia',
				'Senegal'=>'Senegal',
				'Serbia'=>'Serbia',
				'Seychelles'=>'Seychelles',
				'Sierra Leone'=>'Sierra Leone',
				'Singapore'=>'Singapore',
				'Slovakia'=>'Slovakia',
				'Slovenia'=>'Slovenia',
				'Solomon Islands'=>'Solomon Islands',
				'Somalia'=>'Somalia',
				'South Africa'=>'South Africa',
				'South Georgia and The South Sandwich Islands'=>'South Georgia and The South Sandwich Islands',
				'Spain'=>'Spain',
				'Sri Lanka'=>'Sri Lanka',
				'Sudan'=>'Sudan',
				'Suriname'=>'Suriname',
				'Svalbard and Jan Mayen'=>'Svalbard and Jan Mayen',
				'Swaziland'=>'Swaziland',
				'Sweden'=>'Sweden',
				'Switzerland'=>'Switzerland',
				'Syrian Arab Republic'=>'Syrian Arab Republic',
				'Taiwan, Province of China'=>'Taiwan, Province of China',
				'Tajikistan'=>'Tajikistan',
				'Tanzania, United Republic of'=>'Tanzania, United Republic of',
				'Thailand'=>'Thailand',
				'Timor-leste'=>'Timor-leste',
				'Togo'=>'Togo',
				'Tokelau'=>'Tokelau',
				'Tonga'=>'Tonga',
				'Trinidad and Tobago'=>'Trinidad and Tobago',
				'Tunisia'=>'Tunisia',
				'Turkey'=>'Turkey',
				'Turkmenistan'=>'Turkmenistan',
				'Turks and Caicos Islands'=>'Turks and Caicos Islands',
				'Tuvalu'=>'Tuvalu',
				'Uganda'=>'Uganda',
				'Ukraine'=>'Ukraine',
				'United Arab Emirates'=>'United Arab Emirates',
				'United Kingdom'=>'United Kingdom',
				'United States'=>'United States',
				'United States Minor Outlying Islands'=>'United States Minor Outlying Islands',
				'Uruguay'=>'Uruguay',
				'Uzbekistan'=>'Uzbekistan',
				'Vanuatu'=>'Vanuatu',
				'Venezuela'=>'Venezuela',
				'Viet Nam'=>'Viet Nam',
				'Virgin Islands - British'=>'Virgin Islands - British',
				'Virgin Islands - U.S.'=>'Virgin Islands - U.S.',
				'Wallis and Futuna'=>'Wallis and Futuna',
				'Western Sahara'=>'Western Sahara',
				'Yemen'=>'Yemen',
				'Zambia'=>'Zambia',
				'Zimbabwe'=>'Zimbabwe');

		$Option=array('1'=>'PayPal','2'=>'Stripe','3'=>'Both');
		$Option4=array('0'=>'On','1'=>'Off');
		$Option1=array('1'=>'On','2'=>'Off');
		$Option5=array('0'=>'On','1'=>'Off');
		$Optioncharge=array('1'=>'On','0'=>'Off');
		$Option2=array('AUD'=>'Australian Dollar','BRL'=>'Brazilian Real','GBP'=>'British Pound','CAD'=>'Canadian Dollar','DKK'=>'Danish Krone','EUR'=>'Euro','HKD'=>'Hong Kong Dollar','ILS'=>'Israeli New Shekel','JPY'=>'Japanese Yen','MXN'=>'Mexican Peso','NZD'=>'New Zealand Dollar','NOK'=>'Norwegian Krone','PHP'=>'Philippine Peso','SGD'=>'Singapore Dollar','SEK'=>'Swedish Krona','CHF'=>'Swiss Franc','USD'=>'United States Dollar');
	
		$Option3=array('0'=>'Twilio','1'=>'Nexmo');
        $Option6=array('l1grey'=>'Layout 1 Grey','l1default'=>'Layout 1 Default','l1blue'=>'Layout 1 Blue','l1dark'=>'Layout 1 Dark','l1light'=>'Layout 1 Light','l2grey'=>'Layout 2 Grey','l2default'=>'Layout 2 Default','l2blue'=>'Layout 2 Blue','l2dark'=>'Layout 2 Dark','l2light'=>'Layout 2 Light');
        //$Option6=array('grey'=>'Grey','grey2'=>'Grey2','brown'=>'Brown','blue'=>'Blue','darkblue'=>'Dark Blue','light'=>'White');
	    $creditsrvm =array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10');
		echo $this->Form->input('upload_logo',array('type'=>'file'));
	    ?>
		 <p>Upload your logo. Recommended logo size is 300 * 60 pixels<p>
		 <?php if($this->data['Config']['logo'] !=''){ ?>
		 <img src="<?php echo SITE_URL?>/img/<?php echo $this->data['Config']['logo'];?>"/>
		 <?php } ?>
		<?php 
		echo $this->Form->input('id');
		echo $this->Form->input('registration_charge');
		echo $this->Form->input('free_sms',array('label'=>'<strong>Free SMS</strong>'));
		echo $this->Form->input('free_voice');
		echo $this->Form->input('paypal_email');
		echo $this->Form->input('site_url',array('label'=>'<strong>Site URL</strong>'));
		echo $this->Form->input('sitename',array('label'=>'<strong>Site Name</strong>'));
		echo $this->Form->input('support_email');
		
		//echo $this->Form->input('2CO_account_ID');
		//echo $this->Form->input('2CO_account_activation_prod_ID');
		echo $this->Form->input('referral_amount');
		echo $this->Form->input('recurring_referral_percent');
		
        echo $this->Form->input('mobile_page_limit');
		//echo $this->Form->input('profilestartdate');
		echo $this->Form->input('require_monthly_getnumber',array('type' =>'select','div'=>false,'label'=>'<strong>Require Monthly Plan to Get Numbers</strong>', 'options'=>$Optioncharge));
        echo '<br/><br/>';
	    echo $this->Form->input('charge_for_additional_numbers',array('type'=>'select','options'=>$Optioncharge));
	    echo $this->Form->input('chargeincomingsms',array('type'=>'select','label'=>'<strong>Charge For Incoming SMS</strong>','options'=>$Optioncharge));
	    echo $this->Form->input('chargecreditsrvm',array('type'=>'select','label'=>'<strong>Voice Credits Charged For Ringless Voicemail</strong>','options'=>$creditsrvm));
	    echo $this->Form->input('chargecreditsmms',array('type'=>'select','label'=>'<strong>MMS Credits Charged For Sending MMS</strong>','options'=>$creditsrvm));
		echo $this->Form->input('require_real_email',array('type' =>'select','div'=>false,'label'=>'<strong>Require Real Email Address On Sign-Up</strong>', 'options'=>$Optioncharge));
        echo '<br/><br/>';
        echo $this->Form->input('emailsmtp',array('type' =>'select','div'=>false,'label'=>'<strong>Email SMTP For System Email</strong>', 'options'=>$Optioncharge));
        echo '<br/><br/>';
		echo $this->Form->input('payment_gateway',array('type'=>'select','options'=>$Option));
		echo $this->Form->input('pay_activation_fees',array('type'=>'select','options'=>$Option1));
  		echo $this->Form->input('payment_currency_code',array('type'=>'select','options'=>$Option2));
        echo $this->Form->input('country',array('type' =>'select','div'=>false,'label'=>'<strong>Country</strong>', 'options'=>$Option7));
        echo '<br/><br/>';
          
  	    echo $this->Form->input('AllowTollFreeNumbers',array('type'=>'select','options'=>$Option4));
  		echo $this->Form->input('FBTwitterSharing',array('type'=>'select','label'=>'<strong>Facebook Sharing</strong>','options'=>$Option5));
		  /*if($apicount==0){ 
		  
		    $Option3=array('0'=>'Twilio','1'=>'Nexmo','2'=>'Slooce','3'=>'Plivo');
			$attributes=array('style'=>'width: 19px;margin-bottom: 2px;');
			echo $this->Form->radio('api_type',$Option3,$attributes); 

		  }else{ */?>
		  
		  <!--<div style="font-weight:bold;color:red;">You can’t change the SMS API unless you release all the users numbers from the system. Once the new SMS API is chosen, each user would then have to purchase a new number.</div>
		  -->
		 <?//php } ?>
		  <!--<div id="twilio" style = "display:none;">-->
		  <fieldset style="margin-bottom:20px;">
		  <legend><font style="font-size: 18px"><?php echo('Twilio API Keys'); ?></font></legend>
		  <div id="twilio">
		  
		  	 <?php 

			   echo $this->Form->input('twilio_accountSid');
		       echo $this->Form->input('twilio_auth_token');

			   ?>
		  </div>
		  </fieldset>
		  
		  <fieldset style="margin-bottom:20px;">
		  <legend><font style="font-size: 18px"><?php echo('Ytel API Keys'); ?></font></legend>
		  <div id="twilio">
		  
		  	 <?php 

			   echo $this->Form->input('ytel_accountSid');
		       echo $this->Form->input('ytel_auth_token');

			   ?>
		  </div>
		  </fieldset>
		   
		   <!--<div id="nexmo" style = "display:none;">-->
		  <fieldset style="margin-bottom:20px">
		  <legend><font style="font-size: 18px"><?php echo('Nexmo API Keys'); ?></font></legend>
		   <div id="nexmo">
           <?php
		   
			   echo $this->Form->input('nexmo_key');
		       echo $this->Form->input('nexmo_secret');
			   
			   ?>
		  </div>
		  </fieldset>
		  <!--<div id="plivo" style = "display:none;">-->
		  <fieldset style="margin-bottom:20px">
		  <legend><font style="font-size: 18px"><?php echo('Plivo API Keys'); ?></font></legend>
		  <div id="plivo">
           <?php
		   
			   echo $this->Form->input('plivo_key');
		       echo $this->Form->input('plivo_token');
			   
			   ?>
		  </div>
		  </fieldset>
		  <fieldset style="margin-bottom:20px">
		  <legend><font style="font-size: 18px"><?php echo('Bandwidth API Keys'); ?></font></legend>
		  <div id="bandwidth_key">
           <?php
		   
			   echo $this->Form->input('bandwidth_key',array('type'=>'text','label'=>'Bandwidth Token'));
		       echo $this->Form->input('bandwidth_token',array('type'=>'text','label'=>'Bandwidth Secret'));
		       echo $this->Form->input('bandwidth_user_id',array('type'=>'text','label'=>'Bandwidth User ID'));
			   
			   ?>
		  </div>
		  </fieldset>
		  <fieldset style="margin-bottom:20px">
		  <legend><font style="font-size: 18px"><?php echo('Signal Wire API Keys'); ?></font></legend>
		  <div id="signalwire_key">
           <?php
		   
			   echo $this->Form->input('signalwire_key',array('type'=>'text','label'=>'Signal Wire Key'));
		       echo $this->Form->input('signalwire_token',array('type'=>'text','label'=>'Signal Wire Token'));
		       echo $this->Form->input('signalwire_space',array('type'=>'text','label'=>'Signal Wire Space URL'));
			   
			   ?>
		  </div>
		  </fieldset>
		  <fieldset style="margin-bottom:20px">
		  <legend><font style="font-size: 18px"><?php echo('Telnyx API Key'); ?></font></legend>
		  <div id="telnyx_key">
           <?php
		   
			   echo $this->Form->input('telnyx_key',array('type'=>'text','label'=>'Telnyx API Key'));

			   ?>
		  </div>
		  </fieldset>
		  <?php 
			echo $this->Form->input('facebook_appid');
			echo $this->Form->input('facebook_appsecret');
			echo $this->Form->input('birthday_msg');
			echo $this->Form->input('name_capture_msg');
			echo $this->Form->input('email_capture_msg');
			//echo $this->Form->input('bitly_username');
			//echo $this->Form->input('bitly_api_key');
			echo $this->Form->input('bitly_access_token');
			
			echo $this->Form->input('filepickerapikey',array('label'=>'File Picker API Key'));
			$FPOptions=array('Yes'=>'Yes','No'=>'No');
			echo $this->Form->input('filepickeron',array('type'=>'select','options'=>$FPOptions,'label'=>'File Picker On'));
			echo $this->Form->input('numverify',array('label'=>'Numverify API Key'));
			echo $this->Form->input('numverifyurl',array('label'=>'Numverify URL'));
			
			echo $this->Form->input('logout_url',array('label'=>'<strong>Logout URL</strong>'));
			echo $this->Form->input('theme_color',array('type'=>'select','options'=>$Option6));
			?>
			<fieldset>
		    <legend><font style="font-size: 18px"><?php echo('Default User Permissions Upon Sign-Up'); ?></font></legend>
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
	                 <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger" name="data[Config][getnumbers]" value="1" <?php if($configinfo['Config']['getnumbers']==1){?> checked <?php } ?>/>
	                 Get Numbers
	                 </label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][sendsms]" value="1" <?php if($configinfo['Config']['sendsms']==1){?> checked <?php }  ?>/>
                    Send SMS</label>
            
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger" name="data[Config][groups]" value="1" <?php if($configinfo['Config']['groups']==1){?> checked <?php } ?>/>
                    Groups</label>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][autoresponders]" value="1" <?php if($configinfo['Config']['autoresponders']==1){?> checked <?php } ?>/>
                    Autresponders</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][contactlist]" value="1" <?php if($configinfo['Config']['contactlist']==1){?> checked <?php } ?>/>
                    Contact List</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][importcontacts]" value="1" <?php if($configinfo['Config']['importcontacts']==1){?> checked <?php } ?>/>
                    Import Contacts</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger" name="data[Config][shortlinks]" value="1" <?php if($configinfo['Config']['shortlinks']==1){?> checked <?php } ?>/>
                    Short Links</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][voicebroadcast]" value="1" <?php if($configinfo['Config']['voicebroadcast']==1){?> checked <?php } ?>/>
                    Voice Broadcast</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][polls]" value="1" <?php if($configinfo['Config']['polls']==1){?> checked <?php } ?>/>
                    Polls</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][contests]" value="1" <?php if($configinfo['Config']['contests']==1){?> checked <?php } ?>/>
                    Contests</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][loyaltyprograms]" value="1" <?php if($configinfo['Config']['loyaltyprograms']==1){?> checked <?php } ?>/>
                    Loyalty Programs</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][kioskbuilder]" value="1" <?php if($configinfo['Config']['kioskbuilder']==1){?> checked <?php } ?>/>
                    Kiosk Builder</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][birthdaywishes]" value="1" <?php if($configinfo['Config']['birthdaywishes']==1){?> checked <?php } ?>/>
                    Birthday Wishes</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][mobilepagebuilder]" value="1" <?php if($configinfo['Config']['mobilepagebuilder']==1){?> checked <?php } ?>/>
                    Page Builder</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][webwidgets]" value="1" <?php if($configinfo['Config']['webwidgets']==1){?> checked <?php } ?>/>
                    Web-Widgets</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][qrcodes]" value="1" <?php if($configinfo['Config']['qrcodes']==1){?> checked <?php } ?>/>
                    QR Codes</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][smschat]" value="1" <?php if($configinfo['Config']['smschat']==1){?> checked <?php } ?>/>
                    2-way SMS Chat</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][calendarscheduler]" value="1" <?php if($configinfo['Config']['calendarscheduler']==1){?> checked <?php } ?>/>
                    Scheduler</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][appointments]" value="1" <?php if($configinfo['Config']['appointments']==1){?> checked <?php } ?>/>
                    Appointments</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][affiliates]" value="1" <?php if($configinfo['Config']['affiliates']==1){?> checked <?php } ?>/>
                    Affiliates</label>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][mobilecoupons]" value="1" <?php if($configinfo['Config']['mobilecoupons']==1){?> checked <?php } ?>/>
                    Mobile Coupons</label>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                     <label class="checkbox-inline">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Config][qabots]" value="1" <?php if($configinfo['Config']['qabots']==1){?> checked <?php } ?>/>
                    Q&A SMS Bots</label>
                </div>
                  
    	</fieldset>
			
		 
		 
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>