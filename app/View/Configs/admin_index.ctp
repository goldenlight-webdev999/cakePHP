<ul  class="secondary_nav" style="width:100%">
				<?php
				$navigation = array(
					  	//'List Config' => '/admin/configs/index',
						'Config' => '/admin/configs/edit/1',
						'Stripe Config' => '/admin/paypals/stripeindex',
						//'Database Config'=>'/admin/paypals/dbconfig/',
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
<style>

h3 {
    color: #C6C65B;
    font-family: 'Gill Sans','lucida grande',helvetica,arial,sans-serif;
    font-size: 150%;
    padding-top: 0px;
}
</style>

<br/><br/>
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<div style="display:table;width:100%"><div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-user" style="color:#26C281"></i>&nbsp;Client Stats :: Active Clients</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsall')?></td></tr></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Sent</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments-o" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Received</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-usd" aria-hidden="true" style="color:#26C281"></i> Financial Stats :: Revenue</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenuemonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueall')?></td></tr></table> </div>
</div>

<div class="configs index" style="overflow-x:scroll;margin-right:20px">
	<h2><?php echo('Configs');?></h2>
	
	<table cellpadding="0" cellspacing="0" style="width:8200px;">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('registration_charge');?></th>
			<th><?php echo $this->Paginator->sort('free_sms');?></th>
			<th><?php echo $this->Paginator->sort('free_voice');?></th>
			<th><?php echo $this->Paginator->sort('paypal_email');?></th>
			<th><?php echo $this->Paginator->sort('site_url');?></th>
			<th><?php echo $this->Paginator->sort('sitename');?></th>
			<th><?php echo $this->Paginator->sort('support_email');?></th>
			<th><?php echo $this->Paginator->sort('twilio_accountSid');?></th>
			<th><?php echo $this->Paginator->sort('twilio_auth_token');?></th>
			<th><?php echo $this->Paginator->sort('bandwidth_key');?></th>
			<th><?php echo $this->Paginator->sort('bandwidth_token');?></th>
			<th>Bandwidth User ID</th>
			<th><?php echo $this->Paginator->sort('nexmo_key');?></th>
			<th><?php echo $this->Paginator->sort('nexmo_secret');?></th>
			<th><?php echo $this->Paginator->sort('plivo_key');?></th>
			<th><?php echo $this->Paginator->sort('plivo_token');?></th>

			<!--<th><?php echo $this->Paginator->sort('2CO_account_ID');?></th>
			<th><?php echo $this->Paginator->sort('2CO_account_activation_prod_ID');?></th>-->
			
			<th><?php echo $this->Paginator->sort('referral_amount');?></th>
			<th><?php echo $this->Paginator->sort('recurring_referral_percent');?></th>
			<!--<th><?php echo $this->Paginator->sort('profilestartdate');?></th>-->
			<th><?php echo $this->Paginator->sort('payment_gateway');?></th>
			<th><?php echo $this->Paginator->sort('pay_activation_fees');?></th>
			<th><?php echo $this->Paginator->sort('mobile_page_limit');?></th>
			<th><?php echo $this->Paginator->sort('payment_currency_code');?></th>
            <th><?php echo $this->Paginator->sort('country');?></th>
			<th>Require Monthly Plan to Get Numbers</th>
			<th>Charge For Additional(Secondary) Numbers</th>
			<th>Charge For Incoming SMS</th>
			<th>Require Real Email Address on Sign-up</th>
			<th>Email SMTP</th>
			<th><?php echo $this->Paginator->sort('AllowTollFreeNumbers');?></th>
			<th><?php echo $this->Paginator->sort('FBTwitterSharing');?></th>
			<th><?php echo $this->Paginator->sort('facebook_appid');?></th>
			<th><?php echo $this->Paginator->sort('facebook_appsecret');?></th>
			<th><?php echo $this->Paginator->sort('bitly_username');?></th>
			<th><?php echo $this->Paginator->sort('bitly_api_key');?></th>
			<th><?php echo $this->Paginator->sort('bitly_access_token');?></th>
			<th><?php echo $this->Paginator->sort('filepickerapikey','File Picker API Key');?></th>
			<th><?php echo $this->Paginator->sort('filepickeron','File Picker On');?></th>
			<th><?php echo $this->Paginator->sort('numverify','Numverify API Key');?></th>
			<th><?php echo $this->Paginator->sort('numverifyurl','Numverify URL');?></th>
            <th><?php echo $this->Paginator->sort('logout_url');?></th>
            <th><?php echo $this->Paginator->sort('theme_color');?></th>
			<!--<th><?php echo $this->Paginator->sort('twitter_consumer_key');?></th>
			<th><?php echo $this->Paginator->sort('twitter_consumer_secret');?></th>
-->			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($configs as $config):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $config['Config']['id']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['registration_charge']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['free_sms']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['free_voice']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['paypal_email']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['site_url']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['sitename']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['support_email']; ?>&nbsp;</td>
			  <!--<td><?php 
		
		if($config['Config']['api_type']==0){
			echo "Twilio";
		}else if($config['Config']['api_type']==1){
			echo "Nexmo";
		}else if($config['Config']['api_type']==2){
			echo "Slooce";
		}else if($config['Config']['api_type']==3){
			echo "Plivo";
		}
		
		 ?>&nbsp;</td>-->
		
		<?php //if($config['Config']['api_type']==0){ ?>
		
		<td><?php echo $config['Config']['twilio_accountSid']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['twilio_auth_token']; ?>&nbsp;</td>
		
		<td><?php echo $config['Config']['bandwidth_key']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['bandwidth_token']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['bandwidth_user_id']; ?>&nbsp;</td>
		
		<?php //}else if($config['Config']['api_type']==1){ ?>
		
		<td><?php echo $config['Config']['nexmo_key']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['nexmo_secret']; ?>&nbsp;</td>
		
		<?php //}else if($config['Config']['api_type']==3){ ?>
		
		<td><?php echo $config['Config']['plivo_key']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['plivo_token']; ?>&nbsp;</td>
                <!--<td><?php echo $config['Config']['plivoapp_id']; ?>&nbsp;</td>-->
		
	    <?php //} ?>
		
		<!--<td><?php echo $config['Config']['2CO_account_ID']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['2CO_account_activation_prod_ID']; ?>&nbsp;</td>-->
		
		<td><?php echo $config['Config']['referral_amount']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['recurring_referral_percent'].'%'; ?>&nbsp;</td>
		<!--<td><?php echo $config['Config']['profilestartdate']; ?>&nbsp;</td>-->
		<td><?php echo $config['Config']['payment_gateway']; ?>&nbsp;</td>
		
		<td><?php 
		
		if($config['Config']['pay_activation_fees']==1){
		echo "On";
		
		}else if($config['Config']['pay_activation_fees']==2){
		
		echo "Off";
		}
		
		//echo $config['Config']['pay_activation_fees']; ?>&nbsp;</td>
		
		
		
		<td><?php echo $config['Config']['mobile_page_limit']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['payment_currency_code']; ?>&nbsp;</td>
        <td><?php echo $config['Config']['country']; ?>&nbsp;</td>
        
        <td><?php 
        if($config['Config']['require_monthly_getnumber']==1){
		echo "On";
		
		}else if($config['Config']['require_monthly_getnumber']==0){
		
		echo "Off";
		}
		?>&nbsp;</td>

		 
		 <td><?php 
		
		if($config['Config']['charge_for_additional_numbers']==1){
		echo "On";
		
		}else if($config['Config']['charge_for_additional_numbers']==0){
		
		echo "Off";
		}
		?>&nbsp;</td>
		
		<td><?php 
		
		if($config['Config']['chargeforincomingsms']==1){
		echo "On";
		
		}else if($config['Config']['chargeforincomingsms']==0){
		
		echo "Off";
		}
		?>&nbsp;</td>
		
		<td><?php 
        if($config['Config']['require_real_email']==1){
		echo "On";
		
		}else if($config['Config']['require_real_email']==0){
		
		echo "Off";
		}
		?>&nbsp;</td>
		
		<td><?php 
        if($config['Config']['emailsmtp']==1){
		echo "On";
		
		}else if($config['Config']['emailsmtp']==0){
		
		echo "Off";
		}
		?>&nbsp;</td>
		
		<td><?php 
		
		if($config['Config']['AllowTollFreeNumbers']==0){
		echo "On";
		
		}else if($config['Config']['AllowTollFreeNumbers']==1){
		
		echo "Off";
		}
		
		 ?>&nbsp;</td>
				 <td><?php 
		
		if($config['Config']['FBTwitterSharing']==0){
		echo "On";
		
		}else if($config['Config']['FBTwitterSharing']==1){
		
		echo "Off";
		
		}
		
		//echo $config['Config']['pay_activation_fees']; ?>&nbsp;</td>
		
		<td><?php echo $config['Config']['facebook_appid']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['facebook_appsecret']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['bitly_username']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['bitly_api_key']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['bitly_access_token']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['filepickerapikey']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['filepickeron']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['numverify']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['numverifyurl']; ?>&nbsp;</td>
        <td><?php echo $config['Config']['logout_url']; ?>&nbsp;</td>
        <td><?php echo $config['Config']['theme_color']; ?>&nbsp;</td>
		<!--<td><?php echo $config['Config']['twitter_consumer_key']; ?>&nbsp;</td>
		<td><?php echo $config['Config']['twitter_consumer_secret']; ?>&nbsp;</td>
-->		
		
		
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $config['Config']['id'])); ?>
			
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
</div>