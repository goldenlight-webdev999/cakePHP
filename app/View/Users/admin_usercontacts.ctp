<script type="text/javascript" charset="utf-8">
function deleteall(id){
		
		var a = confirm('Are you sure you want to delete ALL imported contacts?');
		if(a==true){
		  window.location="<?php echo SITE_URL;?>/admin/users/deleteimportsall/"+id;
		}
}
</script>
<!-- login box-->


<!--<div class="loginbox">-->
<div class="loginner" style="margin-top: 15px;width:800px">

	<div style="text-align:right;float:right; padding-bottom: 5px">
	<!--<a class="nyroModal" href="<?php echo SITE_URL;?>/contacts/add" title="Add Contact"><img src="<?php echo SITE_URL;?>/img/add_contact.png"></a>-->
	<?php if(!empty($contacts)){?>
	<a href="#null" onclick="deleteall(<?php echo $users['User']['id']?>)" title="Delete ALL Imports"><img src="<?php echo SITE_URL;?>/img/deleteall_logs.png"></a>
	<a href="<?php echo SITE_URL;?>/admin/users/contactsexport/<?php echo base64_encode($users['User']['id'])?>" title="Export Contacts"><img src="<?php echo SITE_URL;?>/img/export_excel.png"></a>
	<?php }?>
	</td></tr>
	</div>
	<?php if(empty($contacts)){?>
	<div style="font-weight: bold; font-size: 15px;text-align: center;">No contacts found for this user.</div>

	<?php  }else{ ?>
	<table cellpadding="0" cellspacing="0" width="100%">
	
	
	<tr>
	
	
	<th>Name</th>
		<?php if($users['User']['capture_email_name']==0){ ?>
		<th>Email</th>
		<?php } ?>
		<?php if($users['User']['birthday_wishes']==0){ ?>
		<th>Birthday</th>
 	        <?php } ?>
		
		
		<th>Number</th>
			<th>Group</th>
			<th>Subscriber</th>
			<th>Source</th>
	
	
	<th class="actions" style="text-align: center">Action</th>
	
	
	</tr>


	
	
	<?php 
	$i = 0;
	
	
	
	foreach ($contacts as $contact):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
?>


	
	
	<tr<?php echo $class;?>>
		

		
		<td style="text-align: left;"><?php echo $contact['Contact']['name']; ?>&nbsp;</td>
		<?php if($users['User']['capture_email_name']==0){ ?>
		<td style="text-align: left;"><?php echo $contact['Contact']['email']; ?>&nbsp;</td>
		<?php } ?>
		<?php if($users['User']['birthday_wishes']==0){ ?>
		<td style="text-align: left;"><?php echo $contact['Contact']['birthday']; ?>&nbsp;</td>
		<?php } ?>
		
		<?php if($contact['ContactGroup']['un_subscribers']==0){ ?>
			<td style="text-align: left;"><?php echo $contact['Contact']['phone_number']; ?>&nbsp;</td>	
		<?php }else {?>
			<td style="text-align: left;"><?php echo substr_replace($contact['Contact']['phone_number'], '****', -4); ?>&nbsp;</td>	
		<?php } ?>
					        
		<td style="text-align: left;"><?php echo $contact['Group']['group_name']; ?>&nbsp;</td>
		
		<?php if($contact['ContactGroup']['un_subscribers']==0){ ?>
				<td style="text-align: center;color:#26C281;font-weight:bold">YES
				</td>
		<?php }elseif($contact['ContactGroup']['un_subscribers']==1){ ?>
				<td style="text-align: center;color:red;font-weight:bold">NO
				</td>
		<?php }else {?>
				<td style="text-align: center;color:#F7CA18;font-weight:bold">PENDING
				</td>
		<?php } ?>
		

		<?php if($contact['ContactGroup']['subscribed_by_sms']==0){ ?>
		<td style="text-align: left;">Import</td>
		<?php }else if($contact['ContactGroup']['subscribed_by_sms']==1) { ?>
		<td style="text-align: left;">SMS</td>
		<?php }else { ?>
		<td style="text-align: left;">Widget</td>
		<?php } ?>
		
				
		<td class="tc" style="text-align: center;">
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'contactdelete', $contact['Contact']['id']), null, sprintf(__('Are you sure you want to delete this contact?',true))); ?>
		<!--<a href="<?php echo SITE_URL;?>/contacts/delete/<?php echo $contact['Contact']['id'];?>/1" title="Delete Contact">Delete</a>-->

		</td>

		
	</tr>

<?php endforeach; ?>

	
	</table>
	<div class="paging">

		<?php echo $this->Paginator->prev(__('<< Previous'), array('tag' => false,'class'=>'nyroModal'), null, array('class' => 'disabled','disabledTag' => 'a'))?>
		<?php echo $this->Paginator->next('Next >>', array('tag' => false,'class'=>'nyroModal'), null, array('class' => 'disabled','disabledTag' => 'a'))?>
		
	</div>
	<?php } ?>
</div>
</html>
<!--</div>-->
<!-- login box-->