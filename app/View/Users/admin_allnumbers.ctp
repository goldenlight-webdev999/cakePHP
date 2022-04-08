<script type="text/javascript" charset="utf-8">
function deleteall(id){
		
		var a = confirm('Are you sure you want to release ALL numbers for this user? This will also DELETE them from the gateway.');
		if(a==true){
		  window.location="<?php echo SITE_URL;?>/admin/users/number_allrelease/"+id;
		}
}

function removeall(id,removefromgateway){
		
		var a = confirm('Are you sure you want to remove ALL numbers for this user? This will keep them in the gateway, and just remove them from the user account.');
		if(a==true){
		  window.location="<?php echo SITE_URL;?>/admin/users/number_allrelease/"+id+"/"+removefromgateway;
		}
}

function add(id){
		
		  window.location="<?php echo SITE_URL;?>/admin/users/addnumber/"+id;
	
}
</script>
<div style="font-size:11px;background-color:#F4D03F;color:#fff;"><b>Release</b> - This will remove the number from the user account AND the SMS gateway account.
	<br><b>Remove</b> - This will remove the number from the user account ONLY.</div><br>
	<div style="float:right;padding-bottom:5px">
    <!--<a  href="#null" onclick="add(<?php echo $usernumbers['User']['id']?>)" title="Add Existing Number"><img src="<?php echo SITE_URL;?>/img/add_contact.png"></a>-->
    <?php echo $this->Html->link(__('Add Existing Number', true), array('controller' =>'users', 'action' => 'addnumber', $usernumbers['User']['id']), array('class' => 'nyroModal'));	
    ?>
	<?php if($usernumbers['User']['assigned_number'] > 0){ ?>
	<!--<a href="#null" onclick="deleteall(<?php echo $usernumbers['User']['id']?>)" title="Release ALL Numbers"><img src="<?php echo SITE_URL;?>/img/deleteall_logs.png"></a>-->
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#null" onclick="deleteall(<?php echo $usernumbers['User']['id']?>)" title="Release ALL Numbers">Release ALL Numbers</a>
		<a href="#null" onclick="removeall(<?php echo $usernumbers['User']['id']?>,0)" title="Remove ALL Numbers">Remove ALL Numbers</a>
	<?php }?>
	</div>
	
	<!--<table cellspacing="0" cellpadding="0" style="border:0">
	<td class="actions" style="background-color:#f9f9f9;"><?php echo $this->Html->link(__('Release ALL Numbers', true), array('action' => 'number_allrelease', base64_encode($usernumbers['User']['id'])), array('class' => 'delete'), sprintf(__('Are you sure you want to release ALL numbers for this user?', true)));  ?>
	</td>
	</table>
	</div>-->

	<table id="tableOne" cellspacing="0" cellpadding="0"style="width:100%;" >
	    <thead>
		<tr>
		
			<th class="tc">Number</th>
			<th class="tc">Type</th>
			<th class="tc">Action</th>
		
		</tr>
				
	    </thead>
		 <tbody>		
		<?php 
		if(!empty($numbers)){
		$i = 0;
		foreach($numbers as $invoicedetil) { 
		$class = null;
		
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
		?>
	
	<tr <?php echo $class;?>> 
			 
			   <td style="text-align:left;"><?php echo $invoicedetil['UserNumber']['number'] ?>
			   <br>
			   <font style="color:green;font-size:11px">
			       <?php if($invoicedetil['UserNumber']['sms']==1){?>
			       (SMS)
			       <?php }if($invoicedetil['UserNumber']['mms']==1){?>
			       (MMS)
			       <?php }if($invoicedetil['UserNumber']['voice']==1){?>
			       (VOICE)
			       <?php }if($invoicedetil['UserNumber']['fax']==1){?>
			       (FAX)
			       <?php } ?>
			   </font>
			   
			   </td>
			   <td style="text-align:left;" >Secondary</td>
			   <td style="text-align:left;" class="actions"><?php echo $this->Html->link(__('Release Number', true), array('action' => 'number_release', $invoicedetil['UserNumber']['id']), array('class' => 'delete'), sprintf(__('Are you sure you want to release this number for this user? This will also DELETE it from the gateway.', true)));  ?>
			   <?php echo $this->Html->link(__('Remove Number', true), array('action' => 'number_release', $invoicedetil['UserNumber']['id'],0), array('class' => 'forgetpass'), sprintf(__('Are you sure you want to remove this number for this user? This will keep it in the gateway, and just remove it from the user account.', true)));  ?>
			   
			   </td> </tr>
			
			  <?php }} ?>
			  
			  <?php 
			  
			  if($usernumbers['User']['assigned_number'] > 0){ ?>
			 </tr>  
			 <td style="text-align:left;"><?php echo $usernumbers['User']['assigned_number'] ?>
			 <br>
			   <font style="color:green;font-size:11px">
			       <?php if($usernumbers['User']['sms']==1){?>
			       (SMS)
			       <?php }if($usernumbers['User']['mms']==1){?>
			       (MMS)
			       <?php }if($usernumbers['User']['voice']==1){?>
			       (VOICE)
			       <?php }if($usernumbers['User']['fax']==1){?>
			       (FAX)
			       <?php } ?>
			   </font>
			 </td>
			   <td style="text-align:left;">Primary</td>
			    <?php if(API_TYPE!=2){ ?>
	         <td style="text-align:left;" class="actions"><?php echo $this->Html->link(__('Release Number', true), array('action' => 'number_release_user', $usernumbers['User']['id']), array('class' => 'delete'), sprintf(__('Are you sure you want to release this number for this user? This will also DELETE it from the gateway.', true)));  ?>
	         <?php echo $this->Html->link(__('Remove Number', true), array('action' => 'number_release_user', $usernumbers['User']['id'],0), array('class' => 'forgetpass'), sprintf(__('Are you sure you want to remove this number for this user? This will keep it in the gateway, and just remove it from the user account.', true)));  ?>
	         
	         
	         </td> </tr>
			  <?php } ?>
			  <?php } ?>
			 
			  </tbody>
</table>
	<!--<br><span style="font-size:11px;background-color:#F4D03F;color:#fff;"><b>Release</b> - This will remove the number from the user account AND the SMS gateway account.
	<br><b>Remove</b> - This will remove the number from the user account ONLY.</span>-->
		