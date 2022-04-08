
<style>
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {
    min-height: 300px;
    min-width: 400px;
    position: relative;
}

</style>
<div>
<div class="clearfix"></div>
<div class="portlet box blue-dark">
<div class="portlet-title">
	<div class="caption">
		Send a Fax
	</div>
</div>



<div class="portlet-body">


<!--h1>Send a text message</h1-->
<!-- login box-->
<!--div class="portlet light">
	<div class="portlet-title">
		<div class="login-left"-->
		<?php 
		 $voice_balance=$this->Session->read('User.voice_balance');
		 $assigned_number=$this->Session->read('User.assigned_number');
		 $active=$this->Session->read('User.active');
		 //$this->Session->write('User.pay_activation_fees_active',$users['User']['pay_activation_fees_active']);
		
	if($active == 1 && $assigned_number !=0 && $voice_balance > 0){ ?>
	
<div class="contacts form">
    <?php if(API_TYPE==0){
        echo $this->Form->create('twilios', array('controller'=> 'twilios', 'enctype'=>'multipart/form-data', 'action'=>'faxsend/'.$sourcepage, 'id' => 'sendMsgForm')); 
    }elseif (API_TYPE==5){
        echo $this->Form->create('signalwires', array('controller'=> 'signalwires', 'enctype'=>'multipart/form-data', 'action'=>'faxsend/'.$sourcepage, 'id' => 'sendMsgForm')); 
    }elseif (API_TYPE==7){?>
        <form name="telnyxs" method="post" action="<?php echo SITE_URL?>/telnyxs/faxsend/<?php echo $sourcepage?>" enctype="multipart/form-data" id="sendMsgForm">
    <?php }?>
    
    
	<div class="form-group">
	<!--Javascript validation goes Here---------->
		<div id="validationMessages" style="display:none"></div>
		<!--Javascript validation goes Here---------->
	<label>Send From</label>
	<select class="form-control" name="data[Contact][fax_number]">
		  <?php foreach($numbers as $number){ ?>
				<option  value="<?php echo $number['number'];?>"><?php echo $number['number_details'] ?>
				</option>
		  <?php } ?>
	</select>
	</div>
	
	
	<?php if(API_TYPE!=7){?>
    	<div class="form-group">
    	 <label>Send To</label>
    		<?php echo $this->Form->input('phone_number', array('class' =>'form-control','label' => false,'readonly'=>true,'div' => false,'value'=>$phoneno));?>
    	</div>
    	<div class="form-group">
    	    <label>Quality</label>
    	    <select class="form-control" name="data[Contact][quality]">
                     <option value="standard">Standard (A low quality [204x98] fax resolution that should be supported by all devices)</option>
                     <option selected value="fine">Fine (A medium quality [204x196] fax resolution; this quality boasts wide device support)</option>
                     <option value="superfine">Super Fine (A high quality [204x392] fax resolution; this quality may not be supported by many devices)</option>
    		</select>
    	</div>  
	<?php }else{?>
    	<div class="form-group">
    	 <label>Send To</label>
    		<!--<?php echo $this->Form->input('to', array('class' =>'form-control','label' => false,'readonly'=>true,'div' => false,'value'=>$phoneno));?>-->
    		<input name="data[Contact][to]" type="text" value="<?php echo $phoneno?>" class="form-control" readonly/>
    	</div>
	    <div class="form-group">
    	    <label>Quality</label>
    	    <select class="form-control" name="data[Contact][quality]">
                     <option value="normal">Normal (A normal quality [204x98] fax resolution that should be supported by all devices)</option>
                     <option selected value="high">High (A high quality [204x196] fax resolution; this quality boasts wide device support)</option>
                     <option value="very_high">Very High (A very high quality [204x392] fax resolution; this quality may not be supported by many devices)</option>
    		</select>
    	</div>
	<?php } ?>
	
	<div class="form-group">
            <label>PDF File to Fax</label>
            <div class="input">
                <input type="file" name="data[Contact][fax]" id="file" accept="application/pdf">
			</div><br/>
			<span class="help-block"><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size:24px;color:#e35b5a"></i>&nbsp;Only PDF files accepted</span>
    </div>
    <?php if(API_TYPE!=7){?>
    <div class="note note-info">
    <span class="text-muted"><b>Credits:</b> 1 credit will be deducted for each page faxed + 1 credit per minute for fax duration. 
    <br/>Example: If you are faxing 5 pages, and it takes 1 minute and 45 seconds, 7 credits will be deducted.
    </span>
    </div>
    <?php }else{ ?>
    <div class="note note-info">
    <span class="text-muted"><b>Credits:</b> 1 credit will be deducted for each page faxed.
    </span>
    </div>
    <?php } ?>
	<br/>
	<div class="form-action">
	<input type="Submit" value="Send" class="btn btn-primary">
	</div>
<?php 
echo $this->Form->end();
//echo $this->Validation->rules('twilios',array('formId'=>'sendMsgForm','validationBlock' =>'sendMsg'));
?>
</div>
<?php
     }else {?>
	
	
	<?php 
	//$pay_activation_fees=PAY_ACTIVATION_FEES;
if($active==1 && $assigned_number ==0){?>
	
	<h3>
You need to get an online number to use this feature.</h3>
<br>
Purchase Number to use this feature by 

<?php
	if(API_TYPE==0){
	 echo $this->Html->link('Get Number', array('controller' =>'twilios', 'action' =>'searchcountry'), array('class' => 'nyroModal' ,'style'=>'color:#ff0000;'));
	 }else if(API_TYPE==5){
	 echo $this->Html->link('Get Number', array('controller' =>'signalwires', 'action' =>'searchcountry'), array('class' => 'nyroModal' ,'style'=>'color:#ff0000;'));
     }else if(API_TYPE==7){
	 echo $this->Html->link('Get Number', array('controller' =>'telnyxs', 'action' =>'searchcountry'), array('class' => 'nyroModal' ,'style'=>'color:#ff0000;'));
     }
?>

<?php }else if($active==0 || $assigned_number ==0){?>


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
Activate account with <b><?php echo $this->Html->link('PayPal', array('controller' =>'users', 'action' =>'activation/1'))?></b> or <b><?php echo $this->Html->link('Credit Card', array('controller' =>'users', 'action' =>'activation/2'))?></b><br />
<?php } ?>

<?php }else{?>

<h3>
	Voice/Fax balance is 0. Please purchase additional voice credits to allow sending fax messages.
</h3>
<br>
<?php 
 $payment=PAYMENT_GATEWAY;
 if($payment=='1'){
?>
Purchase voice/fax credits to use this feature by <b><?php echo $this->Html->link('PayPal', array('controller' =>'users', 'action' =>'paypalpayment'))?>.</b><br />
<?php }else if($payment=='2'){?>
Purchase voice/fax credits to use this feature by <b><?php echo $this->Html->link('Credit Card', array('controller' =>'users', 'action' =>'stripepayment'))?>.</b><br />

<?php }else if($payment=='3'){ ?>
Purchase voice/fax credits to use this feature by <b><?php echo $this->Html->link('PayPal', array('controller' =>'users', 'action' =>'paypalpayment'))?></b> or <b><?php echo $this->Html->link('Credit Card', array('controller' =>'users', 'action' =>'stripepayment'))?></b><br />
<?php } ?>


<?php } ?>
</div>
</div>
</div>
</div>
</div>
<?php } ?>