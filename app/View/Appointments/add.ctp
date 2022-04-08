<style>
.ValidationErrors{color:red;}
div#ui-datepicker-div {z-index:9000 !important}
</style>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>
<script>
    
function checknotify(){

    var notify = $('#notify').val();
	if(notify==0){
	    $('#notify').val(1);
	}else{
	    $('#notify').val(0);
	}

}
</script>
<?php
	echo $this->Html->script('jQvalidations/jquery.validation.functions');
	echo $this->Html->script('jQvalidations/jquery.validate');
	echo $this->Html->css('jquery-ui-1.8.16.custom');
	echo $this->Html->script('jquery-ui-timepicker-addon');
?>
   <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/css/bootstrap-select.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.min.js"></script>
	
	<!--<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />
	<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>-->
	
		<div class="page-content-wrapper">
			<div class="page-content">              
				<h3 class="page-title"> <?php echo('Appointments');?></h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<a href="<?php echo SITE_URL;?>/appointments">Appointments </a>
						</li>
					</ul>  
				</div>
				<div class="portlet mt-element-ribbon light portlet-fit  ">
					<div class="ribbon ribbon-right ribbon-clip ribbon-shadow ribbon-border-dash-hor ribbon-color-success uppercase">
						<div class="ribbon-sub ribbon-clip ribbon-right"></div>
						<?php echo('Create Appointment Form');?>
					</div>
					<div class="portlet-title">
						<div class="caption font-red-sunglo">
							<i class="fa fa-calendar font-red-sunglo"></i>
						</div>
					</div>
					<div class="portlet-body form">
						<form  method="post" >
							<div class="form-body">
								<div class="form-group">
									<label for="some21" style="display:block;">Contacts</label>	
									<select data-show-subtext="true" data-live-search="true" id="contacts" class="form-control selectpicker"  name="data[Appointment][contact_id]" >	
									
										<?php foreach($contact as $contact_arr){ ?>
										<!--if(trim($contact_arr['Contact']['name'])!=''){  ?>-->
					                       <option data-subtext="<?php echo $contact_arr['Contact']['phone_number']?>" value="<?php echo $contact_arr['Contact']['id']?>"><?php echo $contact_arr['Contact']['name']?> </option>
				                      	<?php } ?>									
									</select>
								</div>							
								<div class="form-group">
									<label>Appointment Date/Time</label>
									<input type="text"  name="data[Appointment][app_date_time]" id="sent_on" class="form-control" onchange="reminder_app(this.value)" placeholder="Appointment date/time">
								</div>
								<div class="form-group">
									<label for="some21">Appointment Status<span class="required_star"></span></label>	
									<select id="contacts" class="form-control"  name="data[Appointment][appointment_status]" >
										<option value="0" selected>Unconfirmed</option>
										<option value="1" >Confirmed</option>
										<option value="2">Cancelled</option>
										<option value="3">Reschedule</option>
									</select>
								</div>
								<div class="form-group">
        							<label>Notify&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="If checked, it will notify the contact via SMS of their appointment details when their new appointment is created." data-original-title="Notify" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
        							</label>
        							<div class="radio-list" >				
        								<input name="data[Appointment][notify]" value="0" type="checkbox" id="notify" onclick="checknotify()"/>
        							</div>
					            </div>
					            <?php
									$confrmkeyword =$appointmentsetting['AppointmentSetting']['confirm_keyword'];
									$cancelkeyword =$appointmentsetting['AppointmentSetting']['cancel_keyword'];
									$reschedulekeyword =$appointmentsetting['AppointmentSetting']['reschedule_keyword'];
									$remindermsg = "This is a reminder that your appointment is scheduled for {{date}}. To confirm txt ".$confrmkeyword.". To cancel txt ".$cancelkeyword.". To reschedule txt ".$reschedulekeyword;
								?>
								<div class="form-group">
									<label>Appointment Reminder&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Schedule the appointment reminder, after which it will be scheduled to go out on the scheduled SMS calendar. If you want more options when creating your reminder, you can do so from the appointment list and clicking the purple Schedule Appointment Reminder button." data-original-title="Appointment Reminder" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
									</label>
									<div class="radio-list" >				
										<input name="data[Appointment][appointment_reminder]" value="1" type="checkbox" id="appointment_reminder" />
									</div>
								</div>	
								<div id="schedule" style="display:none">
									<div class="form-group">
										<label>Appointment Reminder Date/Time</label>
										<input type="text"  name="data[Appointment][appointment_reminder_date]" id="app_date" class="form-control">
									</div>								
									<div class="form-group">
										<label>Appointment Reminder Message</label>
										<textarea name="data[Appointment][appointment_reminder_message]" class="form-control"  id="messages" placeholder="Message"><?php echo $remindermsg;?></textarea>
									</div>								
								</div>	
								<div class="row">
									<div class="col-sm-12">
										<div style="margin-top:15px;">
											<button type="submit" name="submit" class="btn blue">Save</button>
											
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
    		function reminder_app(){
    			var date = new Date($('#sent_on').val());
    			var reminder_date = formatDate(date);
    			var text = "This is a reminder that your appointment is scheduled for "+reminder_date+". To confirm txt <?php echo $confrmkeyword;?>. To cancel txt <?php echo $cancelkeyword;?>. To reschedule txt <?php echo $reschedulekeyword;?>";
    			$('#messages').val(text);
    		}
    		$(function () {
    			$("#appointment_reminder").click(function () {
    				if ($(this).is(":checked")) {
    					$("#schedule").show();
    				} else {
    					$("#schedule").hide();
    				}
    			});
    		});
    		
    		function formatDate(date) {
              var hours = date.getHours();
              var minutes = date.getMinutes();
              var ampm = hours >= 12 ? 'pm' : 'am';
              hours = hours % 12;
              hours = hours ? hours : 12; // the hour '0' should be '12'
              minutes = minutes < 10 ? '0'+minutes : minutes;
              var strTime = hours + ':' + minutes + ' ' + ampm;
              return date.getMonth()+1 + "/" + date.getDate() + "/" + date.getFullYear() + "  " + strTime;
            }
			
			$('#app_date').datetimepicker({
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
			$('#sent_on').datetimepicker({
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
			jQuery(function(){
				jQuery("#sent_on").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Please select the date/time"        
				});	
				
				jQuery("#contacts").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Please select a contact"        
				});	
			});		
		</script>