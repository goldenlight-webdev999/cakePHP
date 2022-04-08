<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<div class="configs form"><br/><br/>
<div style="height:170px"><div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-user" style="color:#26C281"></i>&nbsp;Client Stats :: Active Clients</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsall')?></td></tr></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Sent</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments-o" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Received</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-usd" aria-hidden="true" style="color:#26C281"></i> Financial Stats :: Revenue</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenuemonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueall')?></td></tr></table> </div>
</div>

<!--<div class="adminUsers index" >

<h2><?php __('Menu');?></h2>

<table cellpadding="0" cellspacing="0" style="width:270px;background: none repeat scroll 0 0 #fbfbfb;border: 1px solid #f1f1f1;border-radius: 0;box-shadow: 0 0 rgba(0, 0, 0, 0.1), 0 0 0 1px #ffffff inset; margin-bottom: 20px; padding: 10px;">
<tr >
	<td  style="text-align:left; padding-left:10px;border-top: 1px solid #ededed;">
	<?php echo $this->Html->link('Main Config','/admin/configs/index')?>
	</td>
</tr>


<tr >
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('DB Config','/admin/paypals/dbconfig')?>
	</td>
</tr>


<tr class="menualtrow">
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Add-On Packages','/admin/packages/index')?>
	</td>
</tr>

<tr >
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Add Add-On Packages','/admin/packages/add')?>
	</td>
</tr>

<tr class="menualtrow">
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Monthly Packages','/admin/packages/monthlypackage')?>
	</td>
</tr>

<tr >
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Add Monthly Packages','/admin/packages/addmonthlypackage')?>
	</td>
</tr>

<tr class="menualtrow">
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Monthly Number Packages','/admin/packages/monthlynumberpackage')?>
	</td>
</tr>

<tr >
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Add Monthly Number Packages','/admin/packages/addmonthlynumberpackage')?>
	</td>
</tr>

<tr class="menualtrow">
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Users','/admin/users/index')?>
	</td>
</tr>

<tr >
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Referrals','/admin/referrals/index')?>
	</td>
</tr>

<tr class="menualtrow">
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Reports','/admin/users/user_messages?all=show')?>
	</td>
</tr>

<tr >
	<td  style="text-align:left; padding-left:10px;">
	<?php echo $this->Html->link('Change Password','/admin_users/change_password')?>
	</td>
</tr>

</table>
</div>-->
