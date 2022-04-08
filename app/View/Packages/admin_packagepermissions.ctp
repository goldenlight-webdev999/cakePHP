    <?php echo $this->Form->create('Package',array('action'=>'packagepermissions/'.$id.'/1'));?>

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
<input type="checkbox" name="data[MonthlyPackage][getnumbers]" value="1" <?php if($packagepermissions['MonthlyPackage']['getnumbers']==1){?> checked <?php }?>/>


</td>
</tr>		
		
		<tr class="altrow"> 
			 
			<td width="350px">Send SMS</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[MonthlyPackage][sendsms]" value="1" <?php if($packagepermissions['MonthlyPackage']['sendsms']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td width="350px">Groups</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[MonthlyPackage][groups]" value="1" <?php if($packagepermissions['MonthlyPackage']['groups']==1){?> checked <?php } ?>/>


</td>
</tr>
	           <tr class="altrow"> 
			 
			<td width="350px">Autoresponders</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[MonthlyPackage][autoresponders]" value="1" <?php if($packagepermissions['MonthlyPackage']['autoresponders']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td width="350px">Contact List</td>
			<td width="350px" style="align:right">
<input type="checkbox" name="data[MonthlyPackage][contactlist]" value="1" <?php if($packagepermissions['MonthlyPackage']['contactlist']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Import Contacts</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][importcontacts]" value="1" <?php if($packagepermissions['MonthlyPackage']['importcontacts']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Short Links</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][shortlinks]" value="1" <?php if($packagepermissions['MonthlyPackage']['shortlinks']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Voice Broadcast</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][voicebroadcast]" value="1" <?php if($packagepermissions['MonthlyPackage']['voicebroadcast']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Polls</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][polls]" value="1" <?php if($packagepermissions['MonthlyPackage']['polls']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Contests</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][contests]" value="1" <?php if($packagepermissions['MonthlyPackage']['contests']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Loyalty Programs</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][loyaltyprograms]" value="1" <?php if($packagepermissions['MonthlyPackage']['loyaltyprograms']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Kiosk Builder</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][kioskbuilder]" value="1" <?php if($packagepermissions['MonthlyPackage']['kioskbuilder']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Birthday SMS Wishes</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][birthdaywishes]" value="1" <?php if($packagepermissions['MonthlyPackage']['birthdaywishes']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>Mobile Page Builder</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][mobilepagebuilder]" value="1" <?php if($packagepermissions['MonthlyPackage']['mobilepagebuilder']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>Web Sign-up Widgets</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][webwidgets]" value="1" <?php if($packagepermissions['MonthlyPackage']['webwidgets']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr class="altrow"> 
			 
			<td>QR Codes</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][qrcodes]" value="1" <?php if($packagepermissions['MonthlyPackage']['qrcodes']==1){?> checked <?php } ?>/>


</td>
</tr>
<tr> 
			 
			<td>2-way SMS Chat</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][smschat]" value="1" <?php if($packagepermissions['MonthlyPackage']['smschat']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td >Calendar / Scheduler</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][calendarscheduler]" value="1" <?php if($packagepermissions['MonthlyPackage']['calendarscheduler']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td>Appointments</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][appointments]" value="1" <?php if($packagepermissions['MonthlyPackage']['appointments']==1){?> checked <?php } ?>/>


</td>
</tr>

<!--<tr class="altrow"> 
			 
			<td>Logs</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][logs]" value="1" <?php if($packagepermissions['MonthlyPackage']['logs']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr> 
			 
			<td>Reports</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][reports]" value="1" <?php if($packagepermissions['MonthlyPackage']['reports']==1){?> checked <?php } ?>/>


</td>
</tr>-->

<tr class="altrow"> 
			 
			<td>Affiliates</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][affiliates]" value="1" <?php if($packagepermissions['MonthlyPackage']['affiliates']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Mobile Coupons</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][mobilecoupons]" value="1" <?php if($packagepermissions['MonthlyPackage']['mobilecoupons']==1){?> checked <?php } ?>/>


</td>
</tr>

<tr class="altrow"> 
			 
			<td>Q&A SMS Bots</td>
			<td style="text-align:center;">
<input type="checkbox" name="data[MonthlyPackage][qabots]" value="1" <?php if($packagepermissions['MonthlyPackage']['qabots']==1){?> checked <?php } ?>/>


</td>
</tr>


			  
			 
               </tbody>
        </table><br/>
<div class="submit" style="margin-top:-3px">
<?php echo $this->Form->submit('Save',array('div'=>false,'class'=>'inputbutton'));?>
</div>
</form>
