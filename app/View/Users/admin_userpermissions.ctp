    <?php echo $this->Form->create('users',array('action'=>'userpermissions/'.$id.'/1'));?>

	<table id="tableOne" cellspacing="0" cellpadding="0"style="width:450px" >
		<thead>
		<tr>
		
			<th class="tc">Feature</th>
			<th class="tc">Access</th>
		
		</tr>
				
	    </thead>
		 <tbody>
		     
		     <tr> 
			 
			<td>Get Numbers</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][getnumbers]" value="1" <?php if($userpermissions['User']['getnumbers']==1){?> checked <?php }?>/>


</td>
</tr>		
		
		<tr class="altrow"> 
			 
			<td width="350px">Send SMS</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[User][sendsms]" value="1" <?php if($userpermissions['User']['sendsms']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td width="350px">Groups</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[User][groups]" value="1" <?php if($userpermissions['User']['groups']==1){?> checked <?php } ?>/>


</td>
</tr>
	           <tr class="altrow"> 
			 
			<td width="350px">Autoresponders</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[User][autoresponders]" value="1" <?php if($userpermissions['User']['autoresponders']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td width="350px">Contact List</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[User][contactlist]" value="1" <?php if($userpermissions['User']['contactlist']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Import Contacts</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][importcontacts]" value="1" <?php if($userpermissions['User']['importcontacts']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Short Links</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][shortlinks]" value="1" <?php if($userpermissions['User']['shortlinks']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Voice Broadcast</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][voicebroadcast]" value="1" <?php if($userpermissions['User']['voicebroadcast']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Polls</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][polls]" value="1" <?php if($userpermissions['User']['polls']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Contests</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][contests]" value="1" <?php if($userpermissions['User']['contests']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Loyalty Programs</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][loyaltyprograms]" value="1" <?php if($userpermissions['User']['loyaltyprograms']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Kiosk Builder</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][kioskbuilder]" value="1" <?php if($userpermissions['User']['kioskbuilder']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Birthday SMS Wishes</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][birthdaywishes]" value="1" <?php if($userpermissions['User']['birthdaywishes']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Mobile Page Builder</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][mobilepagebuilder]" value="1" <?php if($userpermissions['User']['mobilepagebuilder']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Web Sign-up Widgets</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][webwidgets]" value="1" <?php if($userpermissions['User']['webwidgets']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>QR Codes</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][qrcodes]" value="1" <?php if($userpermissions['User']['qrcodes']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>2-way SMS Chat</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][smschat]" value="1" <?php if($userpermissions['User']['smschat']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td >Calendar / Scheduler</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][calendarscheduler]" value="1" <?php if($userpermissions['User']['calendarscheduler']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td>Appointments</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][appointments]" value="1" <?php if($userpermissions['User']['appointments']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Logs</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][logs]" value="1" <?php if($userpermissions['User']['logs']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td>Reports</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][reports]" value="1" <?php if($userpermissions['User']['reports']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Affiliates</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][affiliates]" value="1" <?php if($userpermissions['User']['affiliates']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td>Make Purchases</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][makepurchases]" value="1" <?php if($userpermissions['User']['makepurchases']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Mobile Coupons</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][mobilecoupons]" value="1" <?php if($userpermissions['User']['mobilecoupons']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td>Q&A SMS Bots</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[User][qabots]" value="1" <?php if($userpermissions['User']['qabots']==1){?> checked <?php } ?>/>


</td>
</tr>


			  
			 
               </tbody>
        </table><br/>
<div class="submit" style="margin-top:-3px">
<?php echo $this->Form->submit('Save',array('div'=>false,'class'=>'inputbutton'));?>
</div>
</form>
