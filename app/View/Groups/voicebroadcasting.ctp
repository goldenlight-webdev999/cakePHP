<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>		
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>
<?php
	echo $this->Html->script('jQvalidations/jquery.validation.functions');
	echo $this->Html->script('jQvalidations/jquery.validate');
	echo $this->Html->css('jquery-ui-1.8.16.custom');
	echo $this->Html->script('jquery-ui-timepicker-addon');
?>
<style>
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {
    min-height: 300px;
    min-width: 450px;
    position: relative;
}
#flashMessage {
	font-size: 16px;
	font-weight: normal;
}       
.message {
	background: #f6fff5 url("<?php echo SITE_URL; ?>/app/webroot/img/flashimportant.png") no-repeat scroll 15px 12px / 24px 24px;
	border: 1px solid #97db90;
	border-radius: 5px;
	color: #000;
	font-size: 13px;
	margin-bottom: 10px;
	padding: 13px 13px 13px 48px;
	text-decoration: none;
	text-shadow: 1px 1px 0 #fff;
}
.nyroModalCont {
    top: 0 !important;
}
.nyroModalCloseButton{
	top: 5px !important;
}
</style>
<?php if($_GET['status']=="success"){ ?>
	<script>
		jQuery(document).ready(function($) {
		  setTimeout(function(){ 
		  window.parent.jQuery.fancybox.close();
		  //window.parent.location.reload()
		  }, 2000);  
		 });
	</script>
<?php } ?>
<style>

.ValidationErrors{
color:red;
}
</style>

<script>

function checkrotate(){

    var rotate_number = $('#rotate_number').val();
	if(rotate_number==0){
	$('#rotate_number').val(1);
	}else{
	$('#rotate_number').val(0);
	}

}

function checkforward(){

    var forward = $('#forward').val();
	if(forward==0){
	$('#forward').val(1);
	}else{
	$('#forward').val(0);
	}

}

function checkbroadcast(){

    var broadcast = $('#schedule_broadcast').val();
	if(broadcast==0){
	$('#schedule_broadcast').val(1);
	}else{
	$('#schedule_broadcast').val(0);
	}

}

function ValidateForm(form){
    
    if($('#forward').prop('checked')==true){
			var mobile_number_input=$('#mobile_number_input').val();
			if(mobile_number_input == ''){
				alert( "Please enter your forwarding number" );
				return false;
			}else{
                var phone =(/^[+0-9]+$/);

                if(!mobile_number_input.match(phone)){
                    alert("Please enter correct phone number with NO spaces, dashes, or parentheses.");
                    return false;  
                }
            }
	}
	
	if($('#schedule_broadcast').prop('checked')==true){
			var broadcast_date=$('#broadcast_date').val();
			if(broadcast_date == ''){
				alert( "Please enter a broadcast date" );
				return false;
			}
    }
}

    jQuery(function(){
    
    $('#forward').click(function(){
    		if($('#forward').prop('checked')==true){
    			$('#forward_show').show();
    		}else if($('#forward').prop('checked')==false){
    			$('#forward_show').hide();
    		}
    	});
    	
    $("#schedule_broadcast").click(function () {
    			if ($(this).is(":checked")) {
    				$("#schedule").show();
    			} else {
    				$("#schedule").hide();
    			}
    		});	
    });
   
</script>	
<div>
<div class="clearfix"></div>
<div class="portlet box blue-dark">
<div class="portlet-title">
	<div class="caption">
		Send Broadcast
	</div>
</div>



<div class="portlet-body">
<!--h1>Send Broadcast</h1-->


<?php echo $this->Session->flash(); ?>
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
		<?php echo $this->Form->create('Group',array('action'=> 'voicebroadcasting','enctype'=>'multipart/form-data','onsubmit'=>' return ValidateForm(this)'));?>

  <input type="hidden" value="<?php echo $group_id?>" id="groupid" name="data[Group][id]">
  <input type="hidden" value="<?php echo $id?>" id="id" name="data[voice][id]">
  
 
		<div class="feildbox form-group">
		<label>Caller ID (Send from)</label>
	
	<select id="number" class="form-control txt" name="data[Group][number]" >
		<?php if($users['User']['assigned_number']!=''){ ?>
	<option value="<?php echo $users['User']['assigned_number']; ?>"><?php echo $users['User']['assigned_number']; ?></option>
	
	<?php } if(!empty($usernumber)){?>
	
		  <?php foreach($usernumber as $values){   ?>
		
				 
		<option value="<?php echo $values; ?>" ><?php echo $values; ?></option>
		
      
		<?php  } }?>
       
			
       </select>
	   
	   
				

	   </div> 	
			
			
			
		<div class="feildbox" style="margin-top: 10px">
		<?php if ($users['User']['api_type']==0 || $users['User']['api_type']==5 || $users['User']['api_type']==7){
			$Option3=array('da-DK'=>'Danish, Denmark',
				'de-DE'=>'German, Germany',
				'en-AU'=>'English, Australia',
				'en-CA'=>'English, Canada',
				'en-GB'=>'English, UK',
				'en-IN'=>'English, India',
				'en-US'=>'English, United States',
				'ca-ES'=>'Catalan, Spain',
				'es-ES'=>'Spanish, Spain',
				'es-MX'=>'Spanish, Mexico',
				'fi-FI'=>'Finnish, Finland',
				'fr-CA'=>'French, Canada',
				'fr-FR'=>'French, France',
				'it-IT'=>'Italian, Italy',
				'ja-JP'=>'Japanese, Japan',
				'ko-KR'=>'Korean, Korea',
				'nb-NO'=>'Norwegian, Norway',
				'nl-NL'=>'Dutch, Netherlands',
				'pl-PL'=>'Polish-Poland',
				'pt-BR'=>'Portuguese, Brazil',
				'pt-PT'=>'Portuguese, Portugal',
				'ru-RU'=>'Russian, Russia',
				'sv-SE'=>'Swedish, Sweden',
				'zh-CN'=>'Chinese (Mandarin)',
				'zh-HK'=>'Chinese (Cantonese)',
				'zh-TW'=>'Chinese (Taiwanese Mandarin)'
			);
		}else if ($users['User']['api_type']==3){
			$Option3=array(
				'da-DK'=>'Danish',
				'nl-NL'=>'Dutch',
				'en-AU'=>'English - Australian',
				'en-GB'=>'English - British',
				'en-US'=>'English - USA',
				'fr-FR'=>'French',
				'fr-CA'=>'French - Canadian',
				'de-DE'=>'German',
				'it-IT'=>'Italian',
				'pl-PL'=>'Polish',
				'pt-PT'=>'Portuguese',
				'pt-BR'=>'Portuguese - Brazilian',
				'ru-RU'=>'Russian',
				'es-ES'=>'Spanish',
				'es-US'=>'Spanish - USA',
				'sv-SE'=>'Swedish'
			);
		}else if ($users['User']['api_type']==4){
			$Option3=array(
				'en_US'=>'English US',
				'en_UK'=>'English UK',
				'es_mx'=>'Spanish',
				//'fr_FR'=>'French',
				//'de_DE'=>'German',
				//'it_IT'=>'Italian'
			);
		}else if ($users['User']['api_type']==6){
			$Option3=array(
				'nl-nl-standard-female-1'=>'standard female',
                'en-us-standard-female-1' =>'american standard female 1',
                'en-us-standard-female-2' =>'american standard female 2',
                'en-us-standard-male-1' =>'american standard male 1',
                'en-us-standard-male-2' =>'american standard male 2',
                'en-au-standard-female-1' =>'australian standard female 1',
                'en-au-standard-female-2' =>'australian standard female 2',
                'en-au-standard-male-1' =>'australian standard male 1',
                'en-au-standard-male-2' =>'australian standard male 2',
                'en-gb-standard-female-1' =>'british standard female 1',
                'en-gb-standard-female-2' =>'british standard female 2',
                'en-gb-standard-male-1' =>'british standard male 1',
                'en-gb-standard-male-2' =>'british standard male 2',
                'fr-ca-standard-female-1' =>'canadain standard female 1',
                'fr-ca-standard-female-2' =>'canadain standard female 2',
                'fr-ca-standard-male-1' =>'canadain standard male 1',
                'fr-ca-standard-male-2' =>'canadain standard male 2',
                'fr-fr-standard-female-1' =>'french standard female 1',
                'fr-fr-standard-female-2' =>'french standard female 2',
                'fr-fr-standard-male-1' =>'french standard male 1',
                'fr-fr-standard-male-2' =>'french standard male 2',
                'de-de-standard-female-1' =>'german standard female 1',
                'de-de-standard-male-1' =>'german standard male 1',
                'it-it-standard-female-1' =>'italian standard female 1',
                'ja-jp-standard-female-1' =>'japanese standard female 1',
                'ko-kr-standard-female-1' =>'korean standard female 1',
                'pt-br-standard-female-1' =>'portuguese standard female 1',
                'es-es-standard-female-1' =>'spanish standard female 1',
                'nl-nl-standard-female-1' =>'standard female',
                'sv-se-standard-female-1' =>'swedish standard female 1',
                'tr-tr-standard-female-1' =>'turkish standard female 1'
			);
			
		}else{
			$Option3=array('de-de'=>'German, Germany',
			'en-au'=>'English, Australia',
			'en-gb'=>'English, UK',
			'en-in'=>'English, India',
			'en-us'=>'English, United States',
			'es-es'=>'Spanish, Spain',
			'es-mx'=>'Spanish, Mexico',
			'es-us'=>'Spanish, US',
			'fr-ca'=>'French, Canada',
			'fr-fr'=>'French, France',
			'it-it'=>'Italian, Italy',
			'is-is'=>'Icelandic, Iceland',
			'ja-jp'=>'Japanese, Japan',
			'ko-kr'=>'Korean, Korea',
			'nl-nl'=>'Dutch, Netherlands',
			'pl-pl'=>'Polish-Poland',
			'pt-br'=>'Portuguese, Brazil',
			'pt-pt'=>'Portuguese, Portugal',
			'ro-ro'=>'Romanian, Romania',
			'ru-ru'=>'Russian, Russia',
			'sv-se'=>'Swedish, Sweden',
			'tr-tr'=>'Turkish, Turkey',
			'zh-cn'=>'Chinese (Mandarin)');
		}
	?>
    			
				
			<div class="form-group">	
				<label>Text-to-Voice Language</label>
				<?php echo $this->Form->input('voice.language',array('type' =>'select','class' =>'form-control', 'div'=>false,'label'=>false, 'options'=>$Option3))?>
			</div>
		<?php if($users['User']['api_type'] !=4){?>
			<div class="form-group">
			<label>Repeat Msg(# of times)</label>
			<?php for($i=1;$i<=5;$i++){
				$repeat[$i] = $i;
			}?>
			<?php echo $this->Form->input('voice.repeat',array('type' =>'select','class' =>'form-control', 'div'=>false,'label'=>false, 'options'=>$repeat))?>
			</div>
		<?php } ?>
		<div class="form-group">
		<?php for($i=1;$i<=5;$i++){
				$repeat[$i] = $i;
			}?>
		<label>Pause(# of seconds before playing msg)</label>
		<?php echo $this->Form->input('voice.pause',array('type' =>'select','class' =>'form-control', 'div'=>false,'label'=>false, 'options'=>$repeat))?>
		</div>
		
		<div class="form-group">
			<input id="rotate_number" type="checkbox" value="0" name="data[voice][rotate_number]" onclick="checkrotate()">
					<!--<font style="font-weight: bold">-->
			Rotate through your long codes&nbsp;
		</div>	
                                                       
        <div class="form-group" style="margin-top:10px">
			<label>Sending Throttle</label>
				<?php	
					$Option=array('1'=>'1 Call Every 1 Second Per Long Code','2'=>'1 Call Every 2 Seconds Per Long Code','3'=>'1 Call Every 3 Seconds Per Long Code','4'=>'1 Call Every 4 Seconds Per Long Code','5'=>'1 Call Every 5 Seconds Per Long Code','6'=>'1 Call Every 6 Seconds Per Long Code');
					echo $this->Form->input('voice.throttle', array(
					'class'=>'form-control',
					'label'=>false,
					'type'=>'select',  'options' => $Option, 'default'=>'1'));
				?>
		</div>
				
		<?php if(API_TYPE == 0 || API_TYPE == 3 || API_TYPE == 4 || API_TYPE == 5 || API_TYPE == 6 || API_TYPE == 7){?>
				
		<div class="form-group">
					<label>IVR Forward to Number 
					</label>
					<div class="radio-list" >				
						<input name="data[voice][forward]" type="checkbox" id="forward" value="0" onclick="checkforward()"/>
					</div><font style="font-size:10px">(after broadcast, contact will be prompted to enter 1 to speak with a representative. It will forward to the # below.)</font>
		</div>
		<div id="forward_show" style="display:none;">
							
					<div class="form-group">
						<label>Forward Number
						</label>
						
						<div class="input" id="mobile_number_show" >                                                   
							<?php echo $this->Form->input('voice.mobile_number_input',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'mobile_number_input','placeholder'=>'Mobile number with country code'))?>
						</div>
					</div>
						
        </div>
		<?php } ?>
		
		<div class="form-group">
			<label>Schedule Broadcast</a>
			</label>
			<div class="radio-list" >				
				<input name="data[voice][schedule_broadcast]" value="0" type="checkbox" id="schedule_broadcast" onclick="checkbroadcast()"/>
			</div>
		</div>	
		<div id="schedule" style="display:none">
			<div class="form-group">
				<label>Voice Broadcast Date/Time</label>
				<input type="text" name="data[voice][broadcast_date]" id="broadcast_date" class="form-control">
			</div>
		</div>
		
		<?php echo $this->Form->submit('Send Voice Broadcast',array('div'=>false,'class'=>'inputbutton btn btn-primary'));?>
			
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
			$('#broadcast_date').datetimepicker({
				 minDate: 0,
				showSecond: false,
				//showMinute: false,
				dateFormat: 'yy-mm-dd',
				//timeFormat: 'hh',
				timeFormat: 'hh:mm',
				stepHour: 1,
				stepMinute: 5,
				stepSecond: 10,
			});
	</script>
	
