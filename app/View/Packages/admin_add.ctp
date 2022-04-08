<ul  class="secondary_nav">
				<?php
				$navigation = array(
					  	'List Packages' => '/admin/packages/index',
						'Add Packages' => '/admin/packages/add',
						'List Monthly Packages' => '/admin/packages/monthlypackage',
						'Add Monthly Packages' => '/admin/packages/addmonthlypackage',
						'List Secondary Number Packages' => '/admin/packages/monthlynumberpackage',
						'Add Secondary Number Packages' => '/admin/packages/addmonthlynumberpackage'
					   					   
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
<div class="packages form">
<?php echo $this->Form->create('Package',array('style'=>'width:98%'));?>
	<fieldset>
		<legend><?php echo('Add Package'); ?></legend>
	<?php 

$Option4=array('Afghanistan'=>'Afghanistan',
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

$Option=array('1'=>'Active','0'=>'Inactive');
$TypeOption=array('text'=>'Text','voice'=>'Voice');

		echo $this->Form->input('name');
		echo $this->Form->input('amount');
		echo $this->Form->input('credit');
		//echo $this->Form2->input('type', array('empty' => 'Select Type'));
		echo $this->Form->input('type', array('type'=>'select','options'=>$TypeOption));
        echo $this->Form->input('user_country',array('type' =>'select','div'=>false,'label'=>'<strong>Country</strong>', 'options'=>$Option4));
        echo '<br/><br/>';
        
        echo '<div class="input select"><label for="PackageUser">User</label>';
        echo '<select name="data[Package][username]">';
        echo '<option value="" selected></option>';
	    foreach($users as $user){ 
				echo '<option value="'.$user["username"].'"';
				echo '>'.$user["username"];
				echo '</option>';
		   } 
	    echo '</select></div>';
	    
		//echo $this->Form->input('status', array('label' =>'Active/DeActive', 'checked' =>'checked'));
        echo $this->Form->input('status',array('type'=>'select','options'=>$Option));
    

	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
