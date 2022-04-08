<style>
.ValidationErrors{

color:red;

}
</style>
<div>
<div class="clearfix"></div>
<div class="portlet box blue-dark">
<div class="portlet-title">
	<div class="caption">
		Send Ringless Voicemail
	</div>
</div>



<div class="portlet-body">
<!--h1>Send Broadcast</h1-->



<div class="loginbox">
	<div class="loginner">

<?php if($users['User']['active']==0){?>

	<h3>
          Oops! You need to activate your account to use this feature.</h3>
         <br>
         <?php 
         $payment=PAYMENT_GATEWAY;
         if($payment=='1' && PAY_ACTIVATION_FEES=='1'){
         ?>
           Activate account with PayPal by <?php echo $this->Html->link('Clicking Here', array('controller' =>'users', 'action' =>'activation/'.$payment))?>.<br />
         <?php }else if($payment=='2' && PAY_ACTIVATION_FEES=='1'){?>
           Activate account with Credit Card by <?php echo $this->Html->link('Clicking Here', array('controller' =>'users', 'action' =>'activation/'.$payment))?>.<br />

          <?php }else if($payment=='3' && PAY_ACTIVATION_FEES=='1'){ ?>
           Activate account with <b><?php echo $this->Html->link('PayPal', array('controller' =>'users', 'action' =>'activation/1'))?></b> or <b><?php echo $this->Html->link('Credit Card',  array('controller' =>'users', 'action' =>'activation/2'))?></b><br />
         <?php } 
}else {


	if((!empty($users)) || (!empty($usernumber))){ ?>
		<?php echo $this->Form->create('Group',array('action'=> 'sendrvm','enctype'=>'multipart/form-data'));?>

  <input type="hidden" value="<?php echo $group_id?>" id="groupid" name="data[Group][id]">
  
 
		<div class="feildbox form-group">
		<label>Caller ID (Number that will display on customer's phone when they receive the call)</label>
	
	<select id="number1" class="form-control txt" name="data[Group][callerid]" >
		<?php if($users['User']['assigned_number']!=''){ ?>
	<option value="<?php echo $users['User']['assigned_number']; ?>"><?php echo $users['User']['assigned_number']; ?></option>
	
	<?php } if(!empty($usernumber)){?>
	
		  <?php foreach($usernumber as $values){   ?>
		
				 
		<option value="<?php echo $values; ?>" ><?php echo $values; ?></option>
		
      
		<?php  } }?>
       
			
       </select>
				

	   </div> 
	   
	   <div class="feildbox form-group">
		<label>2nd Number (Number will be initiating the phone call - MUST be a different # than the caller ID)</label>
	
	<select id="number2" class="form-control txt" name="data[Group][number]" >
		<?php if($users['User']['assigned_number']!=''){ ?>
	<option value="<?php echo $users['User']['assigned_number']; ?>"><?php echo $users['User']['assigned_number']; ?></option>
	
	<?php } if(!empty($usernumber)){?>
	
		  <?php foreach($usernumber as $values){   ?>
		
				 
		<option value="<?php echo $values; ?>" ><?php echo $values; ?></option>
		
      
		<?php  } }?>
       
			
       </select>

	   </div> 	
	   
	   <div class="form-group">
			<input id="rotate_callerid" type="checkbox" value="0" name="data[Group][rotate_callerid]" onclick="checkcalleridrotate()">
					<!--<font style="font-weight: bold">-->
			Randomly Rotate Through Caller IDs&nbsp;&nbsp;&nbsp;
			<input id="rotate_number" type="checkbox" value="0" name="data[Group][rotate_number]" onclick="checknumberrotate()">
					<!--<font style="font-weight: bold">-->
			Randomly Rotate Through 2nd Numbers&nbsp;
		</div>
	   
	   <div class="note note-warning">If you want to send out more RVMs at a time, you will need to have more numbers in your account. RVMs sent to 101 contacts are required to have 10 numbers for a Ringless Voicemail broadcast. If the contacts are over 5000, the RVM broadcast needs to have 2 additional numbers per 1000.
	   <br/><br/>
	   <ul>
        <li>1 number = 0 to 100 Contacts</li>
        <li>10 numbers = 101 to 5000 Contacts</li>
        <li>12 numbers = 5001 to 6000 Contacts</li>
        <li>16 numbers = 6001 to 7000 Contacts</li>
        <li>18 numbers = 7001 to 8000 Contacts</li>
        </ul>
       Common reasons for RVM failures are customer's handset voice mail is not working or setup, customer's mailbox is full, or carrier did not accept and blocks the drop. Be assured you will only be charged credits for a successful RVM. 
       </div>
		
		<?php echo $this->Form->submit('Send Ringless Voicemail',array('div'=>false,'class'=>'inputbutton btn btn-primary'));?>
			
		</div>
		
		
		<?php echo $this->Form->end(); ?>
	<?php  }else{

		echo 'No numbers in your account exist with voice capability. You need to get a voice enabled number.';

	}
}
	   ?>
	</div>
</div>



</div>
</div>
</div>
				<style>
	legend{
		font-weight:bold!important;
	}
	fieldset label{
		display:inline!important;
	}
	</style>
	
<script>

  function checknumberrotate(){
 
     var rotate_number = $('#rotate_number').val();
     
     if(rotate_number==0){
     
     $('#rotate_number').val(1);
     
     }else{
     $('#rotate_number').val(0);
     
     }

 }  
 
 function checkcalleridrotate(){
 
     var rotate_number = $('#rotate_callerid').val();
     
     if(rotate_number==0){
     
     $('#rotate_callerid').val(1);
     
     }else{
     $('#rotate_callerid').val(0);
     
     }
 
 }
</script>	