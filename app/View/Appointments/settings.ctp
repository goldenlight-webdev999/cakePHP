
 <style>
 .ValidationErrors{
 color:red;
	}
 </style>
<?
echo $this->Html->css('colorPicker');
echo $this->Html->script('jquery.colorPicker');
//App::import('Helper', 'Fck');
//$this->Fck = new FckHelper(); 
echo $this->Html->script('ckeditor/ckeditor');
?> 
		<div class="page-content-wrapper">
			<div class="page-content">              
				<h3 class="page-title"> <?php echo('Appointment Settings');?></h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<a href="<?php echo SITE_URL;?>/appointments">Appointments</a>
						</li>
					</ul>  
				</div>
				<?php echo $this->Session->flash(); ?>
				
				<!--<div class="portlet mt-element-ribbon light portlet-fit  ">
					
					<div class="portlet-body form">
						<?php echo $this->Session->flash(); ?>	
					
						<div class="form-body">		
							<div class="row">
								<div class="col-md-12">-->
								    
									<div class="portlet light">
										<div class="portlet-title">
											<div class="caption font-red-sunglo">
						<i class="fa fa-cog font-red-sunglo"></i>
						<span class="caption-subject bold uppercase">Appointment Settings </span>
					</div>
											<!--div class="actions">
												<a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
													<i class="icon-cloud-upload"></i>
												</a>
												<a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
													<i class="icon-wrench"></i>
												</a>
												<a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
													<i class="icon-trash"></i>
												</a>
											</div-->
										</div>
										<div class="portlet-body">
										       
											<ul class="nav nav-tabs">
												<li class="active">
													<a href="#tab_1_1" data-toggle="tab" aria-expanded="false"> Cancel Settings </a>
												</li>
												<li class="">
													<a href="#tab_1_2" data-toggle="tab" aria-expanded="true"> Confirm Settings </a>
												</li>
												<li class="">
													<a href="#tab_1_3" data-toggle="tab" aria-expanded="true"> Reschedule Settings</a>
												</li>
											   
											</ul>
											<div class="tab-content">
											    
												<div class="tab-pane fade active in" id="tab_1_1">
												    <?php if (empty($AppointmentSetting['AppointmentSetting']['cancel_email_to'])) {?>
										                 <div class="note note-warning">These are pre-loaded default cancel settings. You must manually save them before they can be used.</div>
										            <? } ?>   
													<form  method="post" >
													<input type="hidden" name="data[AppointmentSetting][id]" value="<?php echo $AppointmentSetting['AppointmentSetting']['id']?>">
														<div class="form-group">
															<label>Cancel Keyword</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Keyword when texted in will mark the appointment as cancelled, send the cancel message back to the contact, and also send the cancel email below." data-original-title="Cancel Keyword" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input type="text"  name="data[AppointmentSetting][cancel_keyword]" id="keyword" class="form-control" required value="<?php echo $AppointmentSetting['AppointmentSetting']['cancel_keyword']?>"placeholder="Cancel Keyword" >
														</div>
														<div class="form-group ">
															<label>Cancel Message</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="The message that will be sent back to the contact when they text in the cancel keyword." data-original-title="Cancel Message" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<textarea   name="data[AppointmentSetting][cancel_message]" cols="30" rows="6" id="message" maxlength="160" required class="form-control"  placeholder="Cancel Message" ><?php echo $AppointmentSetting['AppointmentSetting']['cancel_message']?></textarea>
														</div>
														<div class="form-group">
															<label>Appointment Color</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="The appointment calendar color and status background color that will be set when the contact texts in the cancel keyword." data-original-title="Appointment Color" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<div class="input">
															   <input style="display: none;" name="data[AppointmentSetting][cancel_color_picker]"  value="<?php echo $AppointmentSetting['AppointmentSetting']['cancel_color_picker']?>" id="headerColor_1" type="text">
															</div>
														</div>
														<div class="form-group ">
															<label>Email To</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Email sent to this address when the contact texts in the cancel keyword. This might be an office manager or appointment setter who needs to be notified of the cancellation." data-original-title="Email To" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input   type="email" name="data[AppointmentSetting][cancel_email_to]"  id="email_to" required class="form-control" value="<?php echo $AppointmentSetting['AppointmentSetting']['cancel_email_to']?>" placeholder="Email To" >
														</div>
														<!--<div class="form-group ">
															<label>Email From</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Email sent from this address to the email to address when the contact texts in the cancel keyword. It's best to use our support email address - <?php echo SUPPORT_EMAIL?>" data-original-title="Email From" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input   type="email"  name="data[AppointmentSetting][cancel_email_from]" id="email_fron"  readonly class="form-control" value="<?php echo SUPPORT_EMAIL?>" placeholder="<?php echo SUPPORT_EMAIL?>" >
														</div>-->
														<div class="form-group ">
															<label>Email Subject</label>
															<input   type="text"  name="data[AppointmentSetting][cancel_email_subject]" id="email_subject" required class="form-control" value="<?php echo $AppointmentSetting['AppointmentSetting']['cancel_email_subject']?>" placeholder="Email Subject" >
														</div>
														<div class="form-group">
															<label>Email Body</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b> to output the contact name<br/><b>%%Email%%</b> to output the contact email<br/><b>%%Number%%</b> to output the contact phone number<br/><b>%%ApptDate%%</b> to output the contact appointment date and time<br/>" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i> </a>
															
															<textarea  name="data[AppointmentSetting][cancel_email_body]"  id="editor_office2003" class="ckeditor"   placeholder="Email Body" ><?php echo $AppointmentSetting['AppointmentSetting']['cancel_email_body']?></textarea>
														</div>
														<div class="form-group">
															<label></label>
															<button type="submit" name="submit" class="btn blue">Save Cancel Settings</button>
															<?php echo $this->Html->link('Cancel','javascript:window.history.back();',array('class' => 'btn default'));?>
														</div>
													</form>
												</div>
												<div class="tab-pane fade " id="tab_1_2">
												    <?php if (empty($AppointmentSetting['AppointmentSetting']['confirm_email_to'])) {?>
										                 <div class="note note-warning">These are pre-loaded default confirm settings. You must manually save them before they can be used.</div>
										            <? } ?>
													<form  method="post" >
													<input type="hidden" name="data[AppointmentSetting][id]" value="<?php echo $AppointmentSetting['AppointmentSetting']['id']?>">
														<div class="form-group">
															<label>Confirm Keyword</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Keyword when texted in will mark the appointment as confirmed, send the confirm message back to the contact, and also send the confirm email below." data-original-title="Confirm Keyword" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input type="text"  name="data[AppointmentSetting][confirm_keyword]" required id="keyword" class="form-control" placeholder="Confirm Keyword" value="<?php echo $AppointmentSetting['AppointmentSetting']['confirm_keyword']?>">
														</div>
														<div class="form-group ">
															<label>Confirm Message</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="The message that will be sent back to the contact when they text in the confirm keyword." data-original-title="Confirm Message" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<textarea   name="data[AppointmentSetting][confirm_message]" required cols="30" rows="6" id="message1" maxlength="160" class="form-control" placeholder="Confirm Message" ><?php echo $AppointmentSetting['AppointmentSetting']['confirm_message']?></textarea>
														</div>
														<div class="form-group">
															<label>Appointment Color</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="The appointment calendar color and status background color that will be set when the contact texts in the confirm keyword." data-original-title="Appointment Color" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<div class="input">
															   <input style="display: none;" name="data[AppointmentSetting][confirm_color_picker]"   id="headerColor_2" type="text" value="<?php echo $AppointmentSetting['AppointmentSetting']['confirm_color_picker']?>">
															</div>
														</div>
														<div class="form-group ">
															<label>Email To</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Email sent to this address when the contact texts in the confirm keyword. This might be an office manager or appointment setter who needs to be notified of the confirmation." data-original-title="Email To" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input   type="email" name="data[AppointmentSetting][confirm_email_to]"  required id="email_to" class="form-control" placeholder="Email To" value="<?php echo $AppointmentSetting['AppointmentSetting']['confirm_email_to']?>">
														</div>
														<!--<div class="form-group ">
															<label>Email From</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Email sent from this address to the email to address when the contact texts in the confirm keyword. It's best to use our support email address - <?php echo SUPPORT_EMAIL?>" data-original-title="Email From" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input   type="email"  name="data[AppointmentSetting][confirm_email_from]" id="email_fron"  readonly class="form-control" placeholder="Email From" value="<?php echo SUPPORT_EMAIL?>">
														</div>-->
														<div class="form-group ">
															<label>Email Subject</label>
															<input   type="text"  name="data[AppointmentSetting][confirm_email_subject]" required id="email_subject" class="form-control" placeholder="Email Subject" value="<?php echo $AppointmentSetting['AppointmentSetting']['confirm_email_subject']?>" >
														</div>
														<div class="form-group">
															<label>Email Body</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b> to output the contact name<br/><b>%%Email%%</b> to output the contact email<br/><b>%%Number%%</b> to output the contact phone number<br/><b>%%ApptDate%%</b> to output the contact appointment date and time<br/>" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i> </a>
															
															<textarea   name="data[AppointmentSetting][confirm_email_body]"   id="editor_office2003" class="ckeditor" placeholder="Email Body" ><?php echo $AppointmentSetting['AppointmentSetting']['confirm_email_body']?></textarea>
														</div>
														<div class="form-group">
															<label></label>
															<button type="submit" name="submit" class="btn blue">Save Confirm Settings</button>
															<?php echo $this->Html->link('Cancel','javascript:window.history.back();',array('class' => 'btn default'));?>
														</div>
													</form>
													
												</div>
												<div class="tab-pane fade" id="tab_1_3">
												    <?php if (empty($AppointmentSetting['AppointmentSetting']['reschedule_email_to'])) {?>
										                 <div class="note note-warning">These are pre-loaded default reschedule settings. You must manually save them before they can be used.</div>
										            <? } ?>
													<form  method="post" >
													<input type="hidden" name="data[AppointmentSetting][id]" value="<?php echo $AppointmentSetting['AppointmentSetting']['id']?>">
														<div class="form-group">
															<label>Reschedule Keyword</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Keyword when texted in will mark the appointment as reschedule, send the reschedule message back to the contact, and also send the reschedule email below." data-original-title="Reschedule Keyword" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input type="text"  name="data[AppointmentSetting][reschedule_keyword]" required id="keyword" class="form-control" placeholder="Reschedule Keyword" value="<?php echo $AppointmentSetting['AppointmentSetting']['reschedule_keyword']?>">
														</div>
														<div class="form-group ">
															<label>Reschedule Message</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="The message that will be sent back to the contact when they text in the reschedule keyword." data-original-title="Reschedule Message" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<textarea   name="data[AppointmentSetting][reschedule_message]" required cols="30" rows="6" id="message2" maxlength="160" class="form-control" placeholder="Reschedule Message"><?php echo $AppointmentSetting['AppointmentSetting']['reschedule_message']?></textarea>
														</div>
														<div class="form-group">
															<label>Appointment Color</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="The appointment calendar color and status background color that will be set when the contact texts in the reschedule keyword." data-original-title="Appointment Color" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<div class="input">
															   <input style="display: none;" name="data[AppointmentSetting][reschedule_color_picker]"  id="headerColor_3" type="text" value="<?php echo $AppointmentSetting['AppointmentSetting']['reschedule_color_picker']?>">
															</div>
														</div>
														<div class="form-group ">
															<label>Email To</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Email sent to this address when the contact texts in the reschedule keyword. This might be an office manager or appointment setter who needs to be notified of the reschedule." data-original-title="Email To" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input   type="email" name="data[AppointmentSetting][reschedule_email_to]" required id="email_to" class="form-control" placeholder="Email To" value="<?php echo $AppointmentSetting['AppointmentSetting']['reschedule_email_to']?>" >
														</div>
														<!--<div class="form-group ">
															<label>Email From</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Email sent from this address to the email to address when the contact texts in the reschedule keyword. It's best to use our support email address - <?php echo SUPPORT_EMAIL?>" data-original-title="Email From" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
															<input   type="email"  name="data[AppointmentSetting][reschedule_email_from]" id="email_fron" readonly class="form-control"  placeholder="Email From" value="<?php echo SUPPORT_EMAIL?>" >
														</div>-->
														<div class="form-group ">
															<label>Email Subject</label>
															<input   type="text"  name="data[AppointmentSetting][reschedule_email_subject]" required id="email_subject" class="form-control" placeholder="Email Subject" value="<?php echo $AppointmentSetting['AppointmentSetting']['reschedule_email_subject']?>">
														</div>
														<div class="form-group">
															<label>Email Body</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b> to output the contact name<br/><b>%%Email%%</b> to output the contact email<br/><b>%%Number%%</b> to output the contact phone number<br/><b>%%ApptDate%%</b> to output the contact appointment date and time<br/>" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i> </a>
															
															<textarea  name="data[AppointmentSetting][reschedule_email_body]"   id="editor_office2003" class="ckeditor" placeholder="Email Body" ><?php echo $AppointmentSetting['AppointmentSetting']['reschedule_email_body']?></textarea>
														</div>
														<div class="form-group">
															<label></label>
															<button type="submit" name="submit" class="btn blue">Save Reschedule Settings</button>
															<?php echo $this->Html->link('Cancel','javascript:window.history.back();',array('class' => 'btn default'));?>
														</div>
													</form>
												</div>
											</div>
											
										</div>
									</div>
								</div>		
							</div>	
						<!--</div>
					</div>
				</div>
			</div>
		</div>-->
	<script>
$(function() {    
       $('#headerColor_1').colorPicker();
       $('#headerColor_2').colorPicker();
       $('#headerColor_3').colorPicker();
     
      });
</script>
	