<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>		
<script type="text/javascript" src="https://api.filepicker.io/v1/filepicker.js"></script> 
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>

<?php
echo $this->Html->css('nyroModal');
echo $this->Html->script('jQvalidations/jquery.validation.functions');
echo $this->Html->script('jQvalidations/jquery.validate');
echo $this->Html->script('jquery.nyroModal.custom');
?>

<script type="text/javascript">
		$(document).ready(function() {
                $('a.nyroModal').nyroModal();

		});
</script>

<style>
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
</style>
<script type="text/javascript">
	var SPINTAX_PATTERN = /\{[^"\r\n\}]*\}/;
	var spin = function (spun) {
		var match;
		while (match = spun.match(SPINTAX_PATTERN)) {
			match = match[0];
			var candidates = match.substring(1, match.length - 1).split("|");
			spun = spun.replace(match, candidates[Math.floor(Math.random() * candidates.length)])
		}
		return spun;
	}
	var spin_countVariations = function (spun) {
		spun = spun.replace(/[^{|}]+/g, '1');
		spun = spun.replace(/\{/g, '(');
		spun = spun.replace(/\|/g, '+');
		spun = spun.replace(/\}/g, ')');
		spun = spun.replace(/\)\(/g, ')*(');
		spun = spun.replace(/\)1/g, ')*1');
		spun = spun.replace(/1\(/g, '1*(');
		return eval(spun);
	}
	function spintext(){
		$("#Preview1").val(spin($('#message1').val())+ ' STOP to End');
		var str = $("#Preview1").val();
		characters.value = '# of Characters: ' + str.length;
	}
	function spinvariations(){
		alert('# of possible variations: ' + spin_countVariations($('#message1').val())); /* 9 */
	}
	function spintext2(){
		$("#Preview1").val(spin($('#message2').val())+ ' STOP to End');
		var str = $("#Preview1").val();
		characters.value = '# of Characters: ' + str.length;
	}
	function spinvariations2(){
		alert('# of possible variations: ' + spin_countVariations($('#message2').val())); /* 9 */
	}

</script>
<script>
jQuery(function(){
 jQuery("#KeywordId").validate({
		expression: "if (VAL) return true; else return false;",
		message: "Please Select Group"
	});
	jQuery("#lastName").validate({
		expression: "if (VAL) return true; else return false;",
		message: "Please enter value"
	});
});
</script>
<script type="text/javascript">
 $(document).ready(function (){
	$('textarea[maxlength]').live('keyup change', function() {
		var str = $(this).val()
		var mx = parseInt($(this).attr('maxlength'))
		if (str.length > mx) {
			$(this).val(str.substr(0, mx))
			return false;
			}
		}
	)
});
function confirmmessage(id){ 
	var message= $('#message1').val();
	if(id>0){
		$.ajax({
			url: "<?php  echo SITE_URL ?>/messages/checktemplatedata/"+id,
			type: "POST",
			dataType: "html",
			success: function(response) {
				if(message!=''){
				if($('#sms').prop('checked')==true){
				$('#message1').val(response);
				}else{
				$('#message1').val(response);
				}
				if($('#mms').prop('checked')==true){
				$('#message2').val(response);
				}
				$('#Preview1').val(response);
				return;
				}else{
				$('#message1').html(response);
				$('#Preview1').html(response);
				}
			}
		});
	}
}
function confirmpagemessage(id){
	var messageview= $('#message1').val();
	if(id>0){
		$.ajax({
			url: "<?php  echo SITE_URL ?>/messages/editmobile_pages/"+id,
			type: "POST",
			dataType: "html",
			success: function(response) {
				if(messageview!=''){
				if($('#sms').prop('checked')==true){
				$('#message1').val(response);
				}else{
				$('#message1').val(response);
				}
				if($('#mms').prop('checked')==true){
				$('#message2').val(response);
				}
				$('#Preview1').val(response);
				return;
				}else{
				if($('#sms').prop('checked')==true){
				$('#message1').html(response);
				}else{
				$('#message1').html(response);
				}
				if($('#mms').prop('checked')==true){
				$('#message2').html(response);
				}
				$('#Preview1').html(response);
				}
			}
		});
	}
}
</script>
<script>
$(document).ready(function(){
	if($('#sms').prop('checked')==true){
	$('#textmsg').show();
	$('#pickfile').show();
	}
	if($('#mms').prop('checked')==true){
	$('#upload').show();
	$('#pickfile').hide();
	}
	$('#mms').click(function(){
	$('#textmsg').hide();
	$('#upload').show();
	$('#pickfile').hide();
	});
	$('#sms').click(function(){
	$('#upload').hide();
	$('#message1').html('');
	$('#textmsg').show();
	$('#pickfile').show();
	});
});

			
</script>
<?php
echo $this->Html->css('jquery-ui-1.8.16.custom');
echo $this->Html->script('jquery-ui-timepicker-addon');
?>
<script>
function updateslooce(){
	var tex = $("#message1").val();
	var msg = $("#Preview1").val();
	tex = tex.replace('[','');
	tex = tex.replace(']','');
	tex = tex.replace('~','');
	tex = tex.replace(';','');
	tex = tex.replace('`','');
	tex = tex.replace('"','');
	var len = tex.length;
	var count1 = (148-(len));
	$("#message1").val(tex);
	if(len > 148){
	tex = tex.substring(0,count1);
	$("#Preview1").val(tex);
	return false;
	}
	$("#limit2").val(count1);
	$("#Preview1").val(tex);
	$("#Preview1").val(tex + " STOP to End");
	var str = $("#Preview1").val();
	characters.value = '# of Characters: ' + str.length;
}
var count = "1588";
function update(){
	var tex = $("#message1").val();
	var msg = $("#Preview1").val();
	tex = tex.replace('[','');
	tex = tex.replace(']','');
	tex = tex.replace('~','');
	tex = tex.replace(';','');
	tex = tex.replace('`','');
	tex = tex.replace('"','');
	var len = tex.length;
	var count1 = (1588-(len));
	$("#message1").val(tex);
	if(len > count){
	tex = tex.substring(0,count1);
	$("#Preview1").val(tex);
	return false;
	}
	$("#limit2").val(count1);
	$("#Preview1").val(tex);
	$("#Preview1").val(tex + " STOP to End");
	var str = $("#Preview1").val();
	characters.value = '# of Characters: ' + str.length;
}
function update1(){
	var tex = $("#message2").val();
	var msg = $("#Preview1").val();
	tex = tex.replace('[','');
	tex = tex.replace(']','');
	tex = tex.replace('~','');
	tex = tex.replace(';','');
	tex = tex.replace('`','');
	tex = tex.replace('"','');
	var len = tex.length;
	var count1 = (1588-(len));
	$("#message2").val(tex);
	if(len > count){
	tex = tex.substring(0,count1);
	$("#Preview1").val(tex);
	return false;
	}
	$("#limit3").val(count1);
	$("#Preview1").val(tex);
	$("#Preview1").val(tex + " STOP to End");
	var str = $("#Preview1").val();
	characters.value = '# of Characters: ' + str.length;
}
function popmessagepickwidget(value){
	if($('#sms').prop('checked')==true){
		$('#message1').val(value);
		$('#Preview1').val(value);
		$('#pick_button').val('');
	}else if($('#mms').prop('checked')==true){
		$('#message3').val(value);
		$('#Preview1').val();
		$('#pick_button').val('set');
		var check=$('img').hasClass('thumbnail');
		if(check==true){
		$('.thumbnail').remove();
		}
		$('#resultpick').html('<div style="width: 80px;"><img style="height:70px!important;width: 70px!important;" class="thumbnail" title="preview" src='+value+'></div>');
	}
}
function popmessagepickwidgetnexmo(value){
$('#message1').val(value);
$('#Preview1').val(value);
$('#pick_button').val('');
}
</script>


<div class="clearfix"></div>
<div class="portlet box blue-dark">
	<div class="portlet-title">
		<div class="caption">
			Edit Group Scheduled Message
		</div>
	</div>
	<div class="portlet-body">
		<?php echo $this->Session->flash(); ?>
		<div class="loginbox">
			<div class="loginner">
				<div class="login-left">
					<div class="contacts form">
						<div class="form-body">	
								<?php echo $this->Form->create('Scheduler',array('action'=> 'events_edit_group/'.$id,'enctype'=>'multipart/form-data','onsubmit'=>'return ValidateForm()'));?>
<input type="hidden" id="api_type" value="<?php echo API_TYPE?>"/>
<input type="hidden" id="smsbalance" value="<?php echo $this->Session->read('User.sms_balance')?>"/>

<div class="form-body">
							<div class="showmessage">
								<div style="float: right; margin-top: 0px; margin-right: 26px; width: 223px;"
									class="feildbox"><label>Preview</label>
									<?php echo $this->Form->input('Message.id',array('type'=>'hidden','value'=>$id));?>
									<?php if($ScheduleMessage['ScheduleMessage']['msg_type']!=2){ 
										$preview = $ScheduleMessage['ScheduleMessage']['message'].' STOP to End';
									}else{
										$preview = $ScheduleMessage['ScheduleMessage']['mms_text'].' STOP to End';
									}
									$chrcount = strlen($preview);
									?>
									<?php echo $this->Form->input('Preview', array('type' => 'textarea', 'cols' => '15','rows'=>'12', 'escape' => false,'class'=>'form-control','label'=>false,'div'=>false,'id'=>"Preview1",'value'=>$preview,'readonly'=>true,'style'=>'font-size:20px','data-role'=>'none')); ?>
									<input type="text" class="form-control" id="characters" value="# of Characters:<?php echo $chrcount ?>" size="33" readonly style="margin-top:5px"/>
								</div>
							</div>			
</div>
									<div class="form-group">
										<div class="picklist">
											<label for="some21">Groups</label>		
											<select id="KeywordId" class="form-control input-xlarge" multiple="multiple" name="data[Keyword][id][]" >
												<?php
												foreach($Group as $Groups){ 
												if($Groups['Group']['keyword']!='?')
												{				
												?>	
												<option <?php if(isset($groupid[$Groups['Group']['id']])){ ?> selected <?php } ?>value="<?php echo $Groups['Group']['id']; ?>"><?php echo ucwords($Groups['Group']['group_name']).'('.$Groups['Group']['totalsubscriber'].')'; ?></option>
												<?php }} ?>	
											</select>
											<?php
												foreach( $Group as $Groups){ 
												if($Groups['Group']['keyword']!='?')
												{
												?>
												<input type="hidden" id="<?php echo $Groups['Group']['id']; ?>" value="<?php echo $Groups['Group']['totalsubscriber']; ?>"/>
												<?php }} ?>
												<input type="hidden" id="holdcount" value="0"/>	
										</div>
									</div>



									<div class="form-group">
										<label>Schedule Delivery 
											<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Please schedule your messages to be sent from 8:00AM - 9:00PM local time to adhere to the applicable guidelines published by the CTIA and the Mobile Marketing Association" data-original-title="Schedule Time" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
										</label>
											<?php 
											$dates = $dates=date("d-m-Y H:i", strtotime($ScheduleMessage['ScheduleMessage']['send_on'])); 
											echo $this->Form->input('User.shedule',array('div'=>false,'label'=>false, 'class' => 'form-control input-medium','id'=>'sent_on','value'=>$dates))
										?> 
									</div>
									<div class="form-group">
										<label>Message Template</label>
										<?php
										$Smstemplate[0]='Please select';
										echo $this->Form->input('Smstemplate.id', array(
										'class'=>'form-control input-xlarge',
										'label'=>false,
										'default'=>0,
										'type'=>'select',
										'onchange'=>'confirmmessage(this.value)',
										'options' => $Smstemplate));
										?>
									</div>
									<div class="form-group">
										<label>Mobile Splash Page</label>
										<?php
										$mobilespages[0]='Please select';
										echo $this->Form->input('mobilespages.id', array(
										'class'=>'form-control input-xlarge',
										'label'=>false,
										'default'=>0,
										'type'=>'select',
										'onchange'=>'confirmpagemessage(this.value)',
										'options' => $mobilespages));
										?>
									</div>
									<input type="hidden" name="data[Message][pick_old]" id="pick_old" value=" <?php echo $ScheduleMessage['ScheduleMessage']['pick_file']; ?>"/>
									<?php if(API_TYPE==0){ ?>
										<div class="form-group">
											<?php
											if((!empty($numbers_sms))||($users['User']['sms']==1)){ ?>
											SMS&nbsp;<input type="radio" value="1" name="data[Message][msg_type]" id="sms" <?php if($ScheduleMessage['ScheduleMessage']['msg_type']==1) { ?> checked <?php } ?> />
											<?php } ?>
											<?php if((!empty($numbers_mms))||($users['User']['mms']==1)){ ?>
											MMS&nbsp;<input type="radio" value="2" name="data[Message][msg_type]" id="mms" <?php if($ScheduleMessage['ScheduleMessage']['msg_type']==2) { ?> checked <?php } ?>/>
											<?php } ?>
										</div>
										<?php if (FILEPICKERON == 'Yes'){?>
											<div id="pickfile" style="display:none;margin-bottom:10px">
												<input onchange="popmessagepickwidget(event.fpfile.url)" data-fp-container="modal" data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
												<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
												<input type="hidden" name="data[Message][pick_file]" id="message3" value=""/>
											</div>
										<?php } ?>		
									<?php }else if(API_TYPE==4 || API_TYPE==5 || API_TYPE==6){ ?>
										<div class="form-group">
											<?php
											if((!empty($numbers_sms))||($users['User']['sms']==1)){ ?>
											SMS&nbsp;<input type="radio" value="1" name="data[Message][msg_type]" id="sms" <?php if($ScheduleMessage['ScheduleMessage']['msg_type']==1) { ?> checked <?php } ?> />
											<?php } ?>
											<?php if((!empty($numbers_mms))||($users['User']['mms']==1)){ ?>
											MMS&nbsp;<input type="radio" value="2" name="data[Message][msg_type]" id="mms" <?php if($ScheduleMessage['ScheduleMessage']['msg_type']==2) { ?> checked <?php } ?>/>
											<?php } ?>
										</div>
										<?php if (FILEPICKERON == 'Yes'){?>
											<div id="pickfile" style="display:none;margin-bottom:10px">
												<input onchange="popmessagepickwidget(event.fpfile.url)" data-fp-container="modal" data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
												<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
												<input type="hidden" name="data[Message][pick_file]" id="message3" value=""/>
											</div>
										<?php } ?>
									<?php }else if(API_TYPE==2){ ?>
										<?php if (FILEPICKERON == 'Yes'){?>
											<div style="margin-bottom:10px>
												<input onchange="popmessagepickwidgetnexmo(event.fpfile.url)" data-fp-container="modal" data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
												<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
												<input type="hidden" name="data[Message][pick_file]" id="message3" value=""/>
											</div>
										<?php } ?>
										<div class="form-group">
											
												<label for="some21">Message<span class="required_star"></span></label>  
												<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class'=>'form-control input-xlarge ','rows'=>'6','id'=>'message1','value'=>$ScheduleMessage['ScheduleMessage']['message'],'maxlength'=>'148','onKeyup'=>'return updateslooce()'))?>									
										</div>
										<div id='counter'>Remaining Characters
											<input class="form-control input-xsmall" type=text name=limit2 id=limit2 size=4 readonly value="148">
										</div>
										Special characters not allowed such as ~ [ ] ; "
								<?php }else{ ?>
									<?php if (FILEPICKERON == 'Yes'){?>
										<div style="margin-bottom:10px">
											<input onchange="popmessagepickwidgetnexmo(event.fpfile.url)" data-fp-container="modal" data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker" >	
											<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
											<input type="hidden" name="data[Message][pick_file]" id="message3" value=""/>
										</div>
									<?php } ?>	
								<div class="form-group">							
										<label for="some21">Message<span class="required_star"></span></label>  
										<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class'=>'form-control input-xlarge','rows'=>'6','id'=>'message1','value'=>$ScheduleMessage['ScheduleMessage']['message'],'maxlength'=>'1588','onKeyup'=>'return update()'))?>
										<?php if(API_TYPE !=2){?>
										&nbsp;<input type="button" value="Variations" onclick="spinvariations();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller'=>'messages','action' => 'spinhelp'), array('escape' =>false, 'style'=>'margin-top:10px','class' => 'nyroModal')); ?>
										<?php } ?>
									
								</div>
								<div id='counter'>Remaining Characters
									<input class="form-control input-xsmall" type=text name=limit2 id=limit2 size=4 readonly value="1588">
								</div>
									Special characters not allowed such as ~ [ ] ; "
									<?php } ?>
									<?php if(API_TYPE==0){ ?>
										<div id='textmsg' style="display:none;">	
											<div class="form-group">
												<?php
												if($ScheduleMessage['ScheduleMessage']['msg_type']==1){
												$messg = $ScheduleMessage['ScheduleMessage']['message'];
												}else{
												$messg = '';
												}
												?>
												<label for="some21">Message<span class="required_star"></span>&nbsp;
													<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="1 credit is charged for each 160 character segment. If you have a message that is 300 characters, and you are sending to 10 people, 20 credits will be deducted (2 credits for each person).<br/><br/><b>NOTE:</b> Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment." data-original-title="Credits" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
													<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>
												</label>  
													<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class'=>'form-control input-xlarge','rows'=>'6','id'=>'message1','maxlength'=>'1588','onKeyup'=>'return update()','value'=>$messg))?>
												<?php if(API_TYPE !=2){?>
													&nbsp;<input type="button" value="Variations" onclick="spinvariations();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'spinhelp'), array('escape' =>false, 'style'=>'margin-top:10px','class' => 'nyroModal')); ?>
												<?php } ?>
											</div>
											<div id='counter'>Remaining Characters
												<input class="form-control input-xsmall" type=text name=limit2 id=limit2 size=4 readonly value="1588">
											</div>
											Special characters not allowed such as ~ [ ] ; "
										</div>
										<?php if($ScheduleMessage['ScheduleMessage']['msg_type']==2){  ?>
											<input type="hidden" name="data[Message][image]" value="<?php echo $ScheduleMessage['ScheduleMessage']['message']; ?>" />
										<?php } ?>
								<div id='upload' style="display:none;">
									<div class="form-group" >								
										<div data-provides="fileinput" class="fileinput fileinput-new">
											<input type="hidden" id="check_img_validation" value="0" />
											<div style="width: 200px; height: 150px;" class="fileinput-new thumbnail">
												<img alt="" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=new+image"> 
											</div>
											<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-preview fileinput-exists thumbnail"></div>&nbsp;
												<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Please be aware that mobile carriers are much more restrictive when sending images to many numbers in rapid succession from 1 long code number since there is much more bandwidth to factor in." data-original-title="Images" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
												<div>
													<span class="btn default btn-file">
														<span class="fileinput-new"> Select image </span>
														<span class="fileinput-exists"> Change </span>
														<input type="hidden" value="" name="..."><input type="file" id="files" name="data[Message][new_image][]" onclick="check_image()" > </span>
													<a onclick="remove_image()" data-dismiss="fileinput" class="btn red fileinput-exists" href="javascript:;"> Remove </a>
												</div>
										</div>
									 <?php 
										  if($ScheduleMessage['ScheduleMessage']['msg_type']==2){ 
											$check=strpos($ScheduleMessage['ScheduleMessage']['message'],":");
											if($check!=''){
											$comma=strpos($ScheduleMessage['ScheduleMessage']['message'],",");
											if($comma!=''){
											$image_arr=explode(",",$ScheduleMessage['ScheduleMessage']['message']);
											foreach($image_arr as $value){	
											?>
												<img  class="thumbnail" src="<?php echo $value; ?>" style="height:70px!important;width:70px!important;float: left;" />
											<?php
											}
											}else{
											?>						
												<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-exists thumbnail" ><img class="thumbnail" src="<?php echo $ScheduleMessage['ScheduleMessage']['message'] ?>" style="max-width: 190px; max-height: 140px;margin-bottom:0;border:0;padding:0" /></div>
											<?php
											}
											}else if($ScheduleMessage['ScheduleMessage']['pick_file']!=''){
											?>
											<img class="thumbnail" src="<?php echo $ScheduleMessage['ScheduleMessage']['pick_file'] ?>" style="height:70px!important;width:70px!important;float: left;" />
											<?php
											}
											}
											?>
									</div>
									<div class="form-group" style="margin-top:0px">
										<label for="some21">Message<span class="required_star"></span>&nbsp;
											<a href="javascript:;" data-container="body" data-trigger="hover" data-content="2 credits will be charged for each contact when sending MMS messages." data-original-title="Credits" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
											<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>
										</label>  
										<?php echo $this->Form->textarea( 'Message.mms_text',array('div'=>false,'label'=>false,'class'=>'form-control input-xlarge','rows'=>'6','id'=>'message2','maxlength'=>'1588','onKeyup'=>'return update1()','value'=>$ScheduleMessage['ScheduleMessage']['mms_text']))?>&nbsp;<input type="button" value="Variations" onclick="spinvariations2();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext2();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'spinhelp'), array('escape' =>false, 'style'=>'margin-top:10px','class' => 'nyroModal')); ?>
									</div> 
									<div id='counter'>Remaining Characters
										<input type=text class="form-control input-xsmall" name=limit3 id=limit3 size=4 readonly value="1588">
									</div>
									Special characters not allowed such as ~ [ ] ; "
								</div>
							<?php  }else if(API_TYPE==4 || API_TYPE==5 || API_TYPE==6){?>
							<div id='textmsg' style="display:none;">	
											<div class="form-group">
												<?php
												if($ScheduleMessage['ScheduleMessage']['msg_type']==1){
												$messg = $ScheduleMessage['ScheduleMessage']['message'];
												}else{
												$messg = '';
												}
												?>
												<label for="some21">Message<span class="required_star"></span>&nbsp;
													<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="1 credit is charged for each 160 character segment. If you have a message that is 300 characters, and you are sending to 10 people, 20 credits will be deducted (2 credits for each person).<br/><br/><b>NOTE:</b> Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment." data-original-title="Credits" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
													<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>
												</label>  
													<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class'=>'form-control input-xlarge','rows'=>'6','id'=>'message1','maxlength'=>'1588','onKeyup'=>'return update()','value'=>$messg))?>
												<?php if(API_TYPE !=2){?>
													&nbsp;<input type="button" value="Variations" onclick="spinvariations();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'spinhelp'), array('escape' =>false, 'style'=>'margin-top:10px','class' => 'nyroModal')); ?>
												<?php } ?>
											</div>
											<div id='counter'>Remaining Characters
												<input class="form-control input-xsmall" type=text name=limit2 id=limit2 size=4 readonly value="1588">
											</div>
											Special characters not allowed such as ~ [ ] ; "
										</div>
										<?php if($ScheduleMessage['ScheduleMessage']['msg_type']==2){  ?>
											<input type="hidden" name="data[Message][image]" value="<?php echo $ScheduleMessage['ScheduleMessage']['message']; ?>" />
										<?php } ?>
								<div id='upload' style="display:none;">
									<div class="form-group" >								
										<div data-provides="fileinput" class="fileinput fileinput-new">
											<input type="hidden" id="check_img_validation" value="0" />
											<div style="width: 200px; height: 150px;" class="fileinput-new thumbnail">
												<img alt="" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=new+image"> 
											</div>
											<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-preview fileinput-exists thumbnail"></div>&nbsp;
												<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Please be aware that mobile carriers are much more restrictive when sending images to many numbers in rapid succession from 1 long code number since there is much more bandwidth to factor in." data-original-title="Images" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
												<div>
													<span class="btn default btn-file">
														<span class="fileinput-new"> Select image </span>
														<span class="fileinput-exists"> Change </span>
														<input type="hidden" value="" name="..."><input type="file" id="files" name="data[Message][new_image][]" onclick="check_image()" > </span>
													<a onclick="remove_image()" data-dismiss="fileinput" class="btn red fileinput-exists" href="javascript:;"> Remove </a>
												</div>
										</div>
									 <?php 
										  if($ScheduleMessage['ScheduleMessage']['msg_type']==2){ 
											$check=strpos($ScheduleMessage['ScheduleMessage']['message'],":");
											if($check!=''){
											$comma=strpos($ScheduleMessage['ScheduleMessage']['message'],",");
											if($comma!=''){
											$image_arr=explode(",",$ScheduleMessage['ScheduleMessage']['message']);
											foreach($image_arr as $value){	
											?>
												<img  class="thumbnail" src="<?php echo $value; ?>" style="height:70px!important;width:70px!important;float: left;" />
											<?php
											}
											}else{
											?>						
												<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-exists thumbnail" ><img class="thumbnail" src="<?php echo $ScheduleMessage['ScheduleMessage']['message'] ?>" style="max-width: 190px; max-height: 140px;margin-bottom:0;border:0;padding:0" /></div>
											<?php
											}
											}else if($ScheduleMessage['ScheduleMessage']['pick_file']!=''){
											?>
											<img class="thumbnail" src="<?php echo $ScheduleMessage['ScheduleMessage']['pick_file'] ?>" style="height:70px!important;width:70px!important;float: left;" />
											<?php
											}
											}
											?>
									</div>
									<div class="form-group" style="margin-top:0px">
										<label for="some21">Message<span class="required_star"></span>&nbsp;
											<a href="javascript:;" data-container="body" data-trigger="hover" data-content="2 credits will be charged for each contact when sending MMS messages." data-original-title="Credits" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
											<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>
										</label>  
										<?php echo $this->Form->textarea( 'Message.mms_text',array('div'=>false,'label'=>false,'class'=>'form-control input-xlarge','rows'=>'6','id'=>'message2','maxlength'=>'1588','onKeyup'=>'return update1()','value'=>$ScheduleMessage['ScheduleMessage']['mms_text']))?>&nbsp;<input type="button" value="Variations" onclick="spinvariations2();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext2();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px;margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'spinhelp'), array('escape' =>false, 'style'=>'margin-top:10px','class' => 'nyroModal')); ?>
									</div> 
									<div id='counter'>Remaining Characters
										<input type=text class="form-control input-xsmall" name=limit3 id=limit3 size=4 readonly value="1588">
									</div>
									Special characters not allowed such as ~ [ ] ; "
								</div>
							<?php } ?>
							<div id="mandatory_message_show" <?php if($ScheduleMessage['ScheduleMessage']['alphasender']==1){ ?>style="display:none;" <?php }else{ ?> style="display:block;" <?php } ?>>
								<div class="form-group" style="margin-top:10px">
										<label>Mandatory Appended Message</label>
										<?php echo $this->Form->input('Message.systemmsg',array('div'=>false,'label'=>false, 'class' => 'form-control input-xlarge','id'=>'message','maxlength'=>'1588','rows'=>'1','cols'=>32,'value'=>'STOP to end','readonly'=>'readonly','style'=>'color:#808080'))?>
								</div>
								<?php if(API_TYPE !=2){?>	
										<div class="form-group" style="margin-top:10px">
											<input type="checkbox" name="data[User][rotate_number]" value="1" <?php if($ScheduleMessage['ScheduleMessage']['rotate_number']==1){?> checked <?php } ?> />
											Rotate through your long codes&nbsp;
											<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="Useful if you have a large number of opt-in contacts(>500), you can spread your workload across multiple numbers in your account.<br/><br/><b>STICKY SENDER:</b> Even when rotating through your numbers, your contacts will always get the message from the same recognizable phone number to create a consistent experience and maintain conversation history." data-original-title="Longcodes Rotate" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
										</div>
									</div>
									<div class="form-group" style="margin-top:10px">
										<label>Sending Throttle</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Per carrier restriction, the fastest(default setting) you can send SMS on 1 long code is 1 SMS/second. However, you can slow that down to exercise extra caution. The slowest setting will send at rate of 10 SMS per minute or 1 SMS every 6 seconds." data-original-title="Sending Throttle" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
										<?php	
																		
											$Option=array('1'=>'1 SMS Every 1 Second Per Long Code','2'=>'1 SMS Every 2 Seconds Per Long Code','3'=>'1 SMS Every 3 Seconds Per Long Code','4'=>'1 SMS Every 4 Seconds Per Long Code','5'=>'1 SMS Every 5 Seconds Per Long Code','6'=>'1 SMS Every 6 Seconds Per Long Code');
											echo $this->Form->input('User.throttle', array(
											'class'=>'form-control input-xlarge',
											'label'=>false,
											'type'=>'select',  'options' => $Option, 'default'=>$ScheduleMessage['ScheduleMessage']['throttle']));
										?>
									</div>
								<?php } ?>
						</div>
					</div>
					<div class="form-actions">
						<?php 
if ($ScheduleMessage['ScheduleMessage']['sent'] == 0){
echo $this->Form->submit('Save Message',array('div'=>false,'class'=>'btn blue'));

}?>
					</div>						
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
	
<script type="text/javascript">
	function ValidateForm(form){

		if($('#mms').prop('checked')==true){
			var mms_image=document.getElementById("check_img_validation").value;
			var check=$('img').hasClass('thumbnail');
			if(mms_image==0 && check==false){
			alert( "Please Upload a image" );
			return false;
			}
			var message2=$('#message2').val();
		}
		if($('#sms').prop('checked')==true){
			var message1=document.getElementById("message1").value;
			if(message1==''){
			alert( "Please enter your message" );
			return false;
			}
		}
		if (document.getElementById("api_type").value==1 || document.getElementById("api_type").value==2 || document.getElementById("api_type").value==3){
			var sms_msg=document.getElementById("message1").value;
			if(sms_msg==''){
			alert( "Please enter a message" );
			return false;
			}
		}
		if (document.getElementById("api_type").value==0 || document.getElementById("api_type").value==4 || document.getElementById("api_type").value==5 || document.getElementById("api_type").value==6){
			if($('#sms').prop('checked')==true){
			$("#Preview1").val(spin($('#message1').val())+ ' STOP to End');
			}else{
			$("#Preview1").val(spin($('#message2').val())+ ' STOP to End');
			}
		}   
		else{
		$("#Preview1").val(spin($('#message1').val())+ ' STOP to End');
		}
		var str = $("#Preview1").val();

		var strutf8decode = utf8_decode($("#Preview1").val());

		if($('#mms').prop('checked')==true){	
		   var credits = 2;
		   var mms = 1;
		}else if (str.length != strutf8decode.length){
		   var credits = Math.ceil(str.length/70);
		   var unicode = 1;
		}else{
		   var credits = Math.ceil(str.length/160);
		   var unicode = 0;
		   var mms = 0;  
		}

			characters.value = '# of Characters: ' + str.length;
			//var credits = Math.ceil(str.length/160);
			var selectedValues = [];   
			var groupcnt=0;
			var holdgroupcnt=0;
			$("#KeywordId :selected").each(function(){
			selectedValues.push($(this).val()); 
			});
			for (var i=0, iLen=selectedValues.length; i<iLen; i++) {
				holdgroupcnt = parseInt(holdgroupcnt) + parseInt(groupcnt);
				groupcnt = document.getElementById(selectedValues[i]).value;
			}
			holdgroupcnt = (parseInt(holdgroupcnt) + parseInt(groupcnt)) * parseInt(credits);
		if (document.getElementById("smsbalance").value < holdgroupcnt){
			if (unicode == 0 && mms == 0){
				alert('You do not have enough credits to send this message.');
			}else if(mms == 1){
				alert('You do not have enough credits to send this message. Keep in mind this message type is MMS and requires 2 credits per contact.');
			}else if(unicode == 1){
				alert('You do not have enough credits to send this message. This message contains unicode characters and any SMS that includes 1 or more non-GSM characters will be charged 1 credit for each 70 character segment.');
			}
			return false;
		}
		if (unicode == 1 && str.length > 70){
			var r = confirm ("This message contains unicode characters and is over 70 characters in length. Any SMS that includes 1 or more non-GSM characters will be charged 1 credit for each 70 character segment. Do you still want to send?");

			if (r == true) {
				return true;
			} else {
				return false;
			} 
		}

	}
		
	function check_image(){
		if($('#mms').prop('checked')==true){
		$('#check_img_validation').val(2);
		}
	}	
	
	function remove_image(){
		if($('#mms').prop('checked')==true){
			$('#check_img_validation').val(0);
		}
	}
		
	
	$('#sent_on').datetimepicker({
		 minDate: 0,
		showSecond: false,
		dateFormat: 'dd-mm-yy',
		timeFormat: 'hh:mm',
		stepHour: 1,
		stepMinute: 5,
		stepSecond: 10
	});
	
	function utf8_decode(str) {
		var output = "";
		var i = c = c1 = c2 = 0;
		while ( i < str.length ) {
			c = str.charCodeAt(i);
			if (c < 128) {
			  output += String.fromCharCode(c);
			  i++;
			}
			else if((c > 191) && (c < 224)) {
			  c2 = str.charCodeAt(i+1);
			  output += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			  i += 2;
			}
			else {
			  c2 = str.charCodeAt(i+1);
			  c3 = str.charCodeAt(i+2);
			  output += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			  i += 3;
			}
		}
		return output;
	}
</script>