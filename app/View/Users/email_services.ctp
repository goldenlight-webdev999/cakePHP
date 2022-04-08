<script>
$(document).ready(function (){
	$('#emailservice3').click(function (){
		$('#apiurl').show();
		$('#authcode').hide();
		document.getElementById("apiauthid").innerHTML= "API Key";
		document.getElementById("listname").innerHTML= "List";
	});
	$('#emailservice1').click(function (){
		$('#apiurl').hide();
		$('#authcode').hide();
		document.getElementById("apiauthid").innerHTML= "API Key";
		document.getElementById("listname").innerHTML= "List";
	});
	$('#emailservice2').click(function (){
		$('#apiurl').hide();
		$('#authcode').hide();
		document.getElementById("apiauthid").innerHTML= "API Key";
		document.getElementById("listname").innerHTML= "Campaign";
	});
	$('#emailservice4').click(function (){
		$('#apiurl').hide();
		$('#authcode').show();
		document.getElementById("apiauthid").innerHTML= "Authorization Code";
		document.getElementById("listname").innerHTML= "List";
	});
	$('#emailservice5').click(function (){
		$('#apiurl').hide();
		$('#authcode').hide();
		document.getElementById("apiauthid").innerHTML= "API Key";
		document.getElementById("listname").innerHTML= "List";
	});
	
	var emailservice  = $('#emailservice').val();
	
	if (emailservice == 1){
	    $('#apiurl').hide();
	    $('#authcode').hide();
	    document.getElementById("apiauthid").innerHTML= "API Key";
	    document.getElementById("listname").innerHTML= "List";
    }else if(emailservice == 2){ 
        $('#apiurl').hide();
        $('#authcode').hide();
        document.getElementById("apiauthid").innerHTML= "API Key";
	    document.getElementById("listname").innerHTML= "Campaign";
	}else if(emailservice == 3){ 
	    $('#apiurl').show();
	    $('#authcode').hide();
	    document.getElementById("apiauthid").innerHTML= "API Key";
	    document.getElementById("listname").innerHTML= "List";
	}else if(emailservice == 4){ 
	    $('#apiurl').hide();
	    $('#authcode').show();
	    document.getElementById("apiauthid").innerHTML= "Authorization Code";
	    document.getElementById("listname").innerHTML= "List";
  	}else if(emailservice == 5){ 
        $('#apiurl').hide();
        $('#authcode').hide();
        document.getElementById("apiauthid").innerHTML= "API Key";
	    document.getElementById("listname").innerHTML= "List";
	}else{
	    $('#apiurl').hide();
        $('#authcode').hide();
        document.getElementById("apiauthid").innerHTML= "API Key";
	    document.getElementById("listname").innerHTML= "List";
	
	}
	
});

function ValidateForm(){
	var mailchimp  = $('#emailservice1').prop("checked");
	var getresponse  = $('#emailservice2').prop("checked");
	var activecampaign  = $('#emailservice3').prop("checked");
	var aweber  = $('#emailservice4').prop("checked");
	var sendinblue  = $('#emailservice5').prop("checked");

	var apiurl  = $('#email_apiurl').val();
	var listid  = $('#email_listid').val();
	var apikey  = $('#email_apikey').val();
	
	if (mailchimp==false && getresponse==false && activecampaign==false && aweber==false && sendinblue==false){
	    alert('You must select an email service provider');
		return false;
    }
		  
	if (apikey == ''){
	   if(aweber==false){
	      alert('Please enter your API key from your email services account');
		  return false;
	   }else{
	      alert('Please enter your authorization code obtained from the Authorize App link');
		  return false;
	   }
	}
	
	if(activecampaign==true && apiurl==''){
		alert('Please enter your API URL from your Active Campaign account found under Settings/Developer');
		return false;
	}
	
	/*if (listid == ''){
	    alert('Please select a list that new subscribers will get added to if they provide their email address');
		return false;
	}*/
}
		
</script> 

<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Email Services</h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li><span>Email Services  </span></li>
			</ul> 
		</div>
		<?php  echo $this->Session->flash(); ?>		
		<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption font-red-sunglo">
						<i class="fa fa-envelope-o font-red-sunglo"></i>
						<span class="caption-subject bold uppercase"> Email Services </span>
					</div>
				</div>
			<div class="portlet-body form">
				<?php echo $this->Form->create('User',array('action'=> 'email_services','id'=>'emailform','onsubmit'=>'return ValidateForm()'));?>
				<div class="form-body">
				<input type="hidden" id="emailservice" value="<?php echo $emailservice?>"/>
				
				
					<fieldset>
						<legend><font style="font-size:20px">Integrate with your Email Marketing Service</font></legend>
						<div class="row">
						<div class="feildbox" style=" float: left;">
							<!--<label>Mailchimp<span class="required_star"></span></label>-->
							<img src="<?php echo SITE_URL ?>/app/webroot/img/mailchimp.png"/>
							<?php if($users['User']['email_service']==1){?>
								<input name="data[User][email_service]" type="radio" value="1" id="emailservice1" checked />
								<?php }else{ ?>
								<input name="data[User][email_service]" type="radio" value="1" id="emailservice1" />
							<?php } ?>
						</div>
						<div class="feildbox" style=" float: left;">
							<!--<label>&nbsp;&nbsp;GetResponse<span class="required_star"></span></label>-->
							&nbsp;&nbsp;<img src="<?php echo SITE_URL ?>/app/webroot/img/getresponse.png" style="margin-top:1px"/>
								<?php if($users['User']['email_service']==2){?>
								<input name="data[User][email_service]" type="radio" value="2" id="emailservice2" checked />
								<?php }else{ ?>
								<input name="data[User][email_service]" type="radio" value="2" id="emailservice2" />
								<?php } ?>
						</div>
						<div class="feildbox" style=" float: left;">
							<!--<label>&nbsp;&nbsp;Active Campaign<span class="required_star"></span></label>-->
							&nbsp;&nbsp;<img src="<?php echo SITE_URL ?>/app/webroot/img/activecampaign.png"/>
								<?php if($users['User']['email_service']==3){?>
								<input name="data[User][email_service]" type="radio" value="3" id="emailservice3" checked />
								<?php }else{ ?>
								<input name="data[User][email_service]" type="radio" value="3" id="emailservice3" />
								<?php } ?>
						</div>
						<div class="feildbox" style=" float: left;">
							<!--<label>&nbsp;&nbsp;AWeber<span class="required_star"></span></label>-->
							&nbsp;&nbsp;<img src="<?php echo SITE_URL ?>/app/webroot/img/aweber.png"/>
								<?php if($users['User']['email_service']==4){?>
								<input name="data[User][email_service]" type="radio" value="4" id="emailservice4" checked />
								<?php }else{ ?>
								<input name="data[User][email_service]" type="radio" value="4" id="emailservice4" />
								<?php } ?>
						</div>
						<div class="feildbox" style=" float: left;">
							<!--<label>&nbsp;&nbsp;SendinBlue<span class="required_star"></span></label>-->
							&nbsp;&nbsp;<img src="<?php echo SITE_URL ?>/app/webroot/img/sendinblue.png"/>
								<?php if($users['User']['email_service']==5){?>
								<input name="data[User][email_service]" type="radio" value="5" id="emailservice5" checked />
								<?php }else{ ?>
								<input name="data[User][email_service]" type="radio" value="5" id="emailservice5" />
								<?php } ?>
						</div>
						</div>
						<br/>
						<div class="feildbox" id="authcode">
							<label>Click the link to get your authorization code:&nbsp;&nbsp<span class="required_star"></span></label>
							<a href="https://auth.aweber.com/1.0/oauth/authorize_app/a455f940" target="_blank">Authorize App</a>&nbsp<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Once you receive your Authorization Code from the link, paste in the entire code below and click Save to refresh your lists." data-original-title="Authorization Code" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
						</div>
						<br/>
						<div class="feildbox">
							<label id="apiauthid">API Key<span class="required_star"></span></label>&nbsp<a href="javascript:;" data-container="body" data-trigger="hover" data-content="After entering in your API Key/Auth Code, click Save to refresh your lists from your email provider. You can then select the list you want your new contacts to be added to when providing their email address." data-original-title="API Key/Auth Code" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
							<input name="data[User][email_apikey]" type="text" class="form-control" value="<?php echo $users['User']['email_apikey'];?>" id="email_apikey" />
						</div>
						<br/>
						<div class="feildbox" id="apiurl">
							<label>API URL<span class="required_star"></span></label>&nbsp<a href="javascript:;" data-container="body" data-trigger="hover" data-content="This is found in your ActiveCampaign account under My Settings and clicking the Developer link. Once you have both the API Key and URL entered, click Save to refresh your lists below." data-original-title="ActiveCampaign API URL" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
							<input name="data[User][email_apiurl]" type="text" class="form-control" value="<?php echo $users['User']['email_apiurl'];?>" id="email_apiurl" />
							<br/>
						</div>
						
						<div class="feildbox">
						<label id="listname">List<span class="required_star"></span></label>
						<select class="form-control" name="data[User][email_listid]" id="email_listid">
						   <option value=""></option>
					       <?php foreach((array)$emaillists as $emaillist){ ?>
					       <option 
					          <?php if($listid == $emaillist['id']){ ?> selected <?php } ?>
						      value="<?php echo $emaillist['id'];?>"><?php echo $emaillist['name'] ?>
						   </option>
					       <?php } ?>
					   </select>
					   </div>

						
					</fieldset>

				</div>
				<div class="form-actions">
						<?php echo $this->Form->submit('Save',array('div'=>false,'class'=>'btn blue'));?>
			
				</div>
				<?php echo $this->Form->end();?>
			
			</div>
		</div>
	</div>
</div>