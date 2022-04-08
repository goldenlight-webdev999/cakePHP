<style>
td.actions {
    text-align: left;
    white-space: nowrap;
}

</style>
<ul  class="secondary_nav" style="width:100%">
				<?php
				$navigation = array(
					  	'List Users' => '/admin/users/index',
					  	'Add User' => '/admin/users/add',
						
					   					   
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
<?php echo $this->Form->create('users',array('action'=> 'index','style'=>'width:98%'));?>
<div class="users index" style="overflow-x:scroll;">

<fieldset style="margin-top:0px; padding: 20px 0 0 10px"><legend>Search Parameters</legend>
	 <table style="background-color: #fff;border-bottom: 0px solid #ccc;border-left: 0px solid #ccc;border-top: 0px solid #ccc;border-right: 0px solid #ccc;clear: both; color: #333; "><tr>
	     <!--<td style="border-right: 0px solid #ccc; font-size: 14px; font-weight: bold;width:90px">
	 	Search By</td>--><td style="border-right: 0px solid #ccc;width:100px">
			


<?php
	$Option=array('6'=>'Search By','1'=>'User Name','2'=>'First Name','3'=>'Last Name','4'=>'Email Address','5'=>'Phone Number','7'=>'IP Address');
	
    echo $this->Form->input('Users.phone', array(
    'style'=>'width:250px;height:30px',
    'label'=>false,
    'default'=>6,
    'type'=>'select',
    'options' => $Option
	));
?>
			</td>
			
	</td>
	
	<td style="border-right: 0px solid #ccc;width:100px">
			


<?php
	$Option3=array(''=>'Select SMS Gateway','4'=>'Bandwidth','1'=>'Nexmo','3'=>'Plivo','5'=>'Signal Wire','2'=>'Slooce','7'=>'Telnyx','0'=>'Twilio','6'=>'Ytel');
    echo $this->Form->input('Users.api_type', array(
    'style'=>'width:175px;height:30px',
    'label'=>false,
    //'default'=>4
    'type'=>'select',
    'options' => $Option3
	));
?>
			</td>	
			

		
		<!--<td style="border-right: 0px solid #ccc; font-size: 14px; font-weight: bold; width:90px">
		Search For</td>--><td style="border-right: 0px solid #ccc; width:100px">
<?php echo $this->Form->input('users.name', array('style'=>'width:250px;','label'=>false,'div'=>false,'id'=>'namephone','placeholder'=>'Search')); ?></td>
	<td style="border-right: 0px solid #ccc;"><div class="submit" style="margin-top:-3px"><?php echo $this->Form->submit('Search',array('div'=>false,'class'=>'inputbutton'));?></div> 	
			
</td>
</tr>
</table>
</fieldset>

			


	<!--<h2 style="padding-top: 0px"><?php __('Users');?></h2>-->
	<table cellpadding="0" cellspacing="1" style="width: 3800px;margin-top:20px">
	<tr>
	        <th><?php echo __('SMS Gateway');?></th>
	        <th><?php echo __('IP Address');?></th>
			<th><?php echo __('Username');?></th>
			<th><?php echo __('First Name');?></th>
			<th><?php echo __('Last Name');?></th>
			<th><?php echo __('Email');?></th>
			<th><?php echo __('Personal Phone #');?></th>
			<th><?php echo __('Company Name');?></th>
			<th><?php echo __('Paypal Email');?></th>
			<th><?php echo __('Timezone');?></th>
            <th><?php echo __('Country');?></th>
<th><?php echo __('Plan Name');?></th>
			<th><?php echo __('Next Renewal Date');?></th>
			<th><?php echo __('SMS Credits');?></th>
			<th><?php echo __('Voice Credits');?></th>
			<th><?php echo __('Number');?></th>
			<th><?php echo __('Number Limit');?></th>
			<th><?php echo __('Created On');?></th>
			<th><?php echo __('Status');?></th>
			<th><?php echo __('Last Login Username');?></th>
			<th><?php echo __('Last Login Date/Time');?></th>
			<th class="actions"><?php echo('Actions');?></th>
			
	</tr>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}

	?>
	<tr<?php echo $class;?>>
	       <?php if($user['User']['api_type'] == 0) { ?>
	        <td style="background-color: #ff596d;color:#fff;font-weight:bold">Twilio
	        <? } elseif($user['User']['api_type'] == 1) { ?>
	        <td style="background-color: #5cadff;color:#fff;font-weight:bold">Nexmo
	        <? } elseif($user['User']['api_type'] == 2) { ?>
	        <td>Slooce
	        <? } elseif($user['User']['api_type'] == 3) { ?>
	        <td style="background-color: #43a046;color:#fff;font-weight:bold">Plivo
	        <? } elseif($user['User']['api_type'] == 4) { ?>
	        <td style="background-color: #651f44;color:#fff;font-weight:bold">Bandwidth
	        <?} elseif($user['User']['api_type'] == 5) { ?>
	        <td style="background-color: #2b355d;color:#fff;font-weight:bold">Signal Wire
	        <?} elseif($user['User']['api_type'] == 6) { ?>
	        <td style="background-color: #06529e;color:#fff;font-weight:bold">Ytel
	        <?} elseif($user['User']['api_type'] == 7) { ?>
	        <td style="background-color: #00ba84;color:#fff;font-weight:bold">Telnyx
	        <?} ?>
	    </td>
        <td><?php echo $user['User']['IP_address']; ?>&nbsp;</td>
		<td><?php echo $user['User']['username']; ?>&nbsp;</td>
		<td><?php echo $user['User']['first_name']; ?>&nbsp;</td>
		<td><?php echo $user['User']['last_name']; ?>&nbsp;</td>
		<td><?php echo $user['User']['email']; ?>&nbsp;</td>
		<td><?php echo $user['User']['phone']; ?>&nbsp;</td>
		<td><?php echo $user['User']['company_name']; ?>&nbsp;</td>
		<td><?php echo $user['User']['paypal_email']; ?>&nbsp;</td>
		<td><?php echo $user['User']['timezone']; ?>&nbsp;</td>
        <td><?php echo $user['User']['user_country']; ?>&nbsp;</td>
        <td><?php echo $user['MonthlyPackage']['package_name'];?>&nbsp;</td>
		 
		 <td><?php 
		if($user['User']['next_renewal_dates']>0){
		
		echo $user['User']['next_renewal_dates'];
		}
		
		 ?>&nbsp;</td>
			<td><?php echo $user['User']['sms_balance']; ?>&nbsp;</td>
			<td><?php echo $user['User']['voice_balance']; ?>&nbsp;</td>
			<td><?php echo $user['User']['assigned_number']; ?>&nbsp; <?php 
		//if($user['User']['assigned_number']!=0){ echo $this->Html->link(__('Release Number', true), array('action' => 'release_number', $user['User']['id']));  }?></td>
		    <td><?php echo $user['User']['number_limit']; ?>&nbsp;</td>
		    <td><?php echo $user['User']['created']; ?>&nbsp;</td>
		    <td>
		    <?php if($user['User']['active'] == 1) { ?>
            Active
            <?php }else {?>
            Inactive
            <?php }?>
            </td>
		    <!--<td><?php echo $user['User']['active'] == 1?"Active":"Inactive"; ?>&nbsp;</td>-->
		    <td><?php echo $user['User']['lastlogin_username']; ?>&nbsp;</td>
		    <td><?php echo $user['User']['lastlogin_datetime']; ?>&nbsp;</td>
		    <td class="actions">

			<?php 
			if ($user['User']['active'] == 0) {
				echo $this->Html->link(__('Resend Email', true), array('action' => 'resend_email', $user['User']['id'])); 
			}
			?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $user['User']['id'],base64_encode($user['User']['password']))); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', base64_encode($user['User']['id']),base64_encode($user['User']['password'])),array('class' => 'delete'), sprintf(__('Are you sure you want to delete? It is recommended that you first release all numbers associated to this account before deleting the user.', true))); ?>

			<?php echo $this->Html->link(__('Contacts', true), array('controller' =>'users', 'action' => 'usercontacts',base64_encode($user['User']['id']),base64_encode($user['User']['password'])), array('class' => 'nyroModal'));?>		
			<?php 
			if($user['User']['api_type'] !=2){
			//if($user['User']['assigned_number'] > 0){
			   echo $this->Html->link(__('Numbers', true), array('controller' =>'users', 'action' => 'allnumbers',base64_encode($user['User']['id']),base64_encode($user['User']['password'])), array('class' => 'forgetpass nyroModal'));
			//} 
			}?>
			<!--<a href="<?php echo SITE_URL;?>/users/autologin/<?php echo $user['User']['id'];?>" target="_blank" class="forgetpass" onclick="return confirm('Reminder: You must log-out of other user accounts you are currently logged into before logging into this one. Are you logged out of all other accounts?');">Login</a>-->
            <a href="<?php echo SITE_URL;?>/users/autologin/<?php echo base64_encode($user['User']['id']);?>/<?php echo base64_encode($user['User']['password']);?>" target="_blank" class="forgetpass" onclick="return confirm('Reminder: You must log-out of other user accounts you are currently logged into before logging into this one. Are you logged out of all other accounts?');">Login</a>

            <?php echo $this->Html->link(__('Permissions', true), array('action' => 'userpermissions', base64_encode($user['User']['id']),0,base64_encode($user['User']['password'])),array('class' => 'darkblue nyroModal')); ?>
            <?php echo $this->Html->link(__('Sub-Accounts', true), array('action' => 'subaccounts', base64_encode($user['User']['id'])),array('class' => 'nyroModal')); ?>
			
	
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
