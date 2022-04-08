<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>		
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>

<?php
	echo $this->Html->css('nyroModal');
	echo $this->Html->script('jQvalidations/jquery.validation.functions');
	echo $this->Html->script('jQvalidations/jquery.validate');
	echo $this->Html->script('jquery.nyroModal.custom');
	echo $this->Html->css('jquery-ui-1.8.16.custom');
	echo $this->Html->script('jquery-ui-timepicker-addon');
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('a.nyroModal').nyroModal();

	});
	
	function checknotify(){

    var notify = $('#notify').val();
	if(notify==0){
	    $('#notify').val(1);
	}else{
	    $('#notify').val(0);
	}

    }
	
</script>
<?php if($_GET['status']=="success"){ ?>
	<script>
		jQuery(document).ready(function($) {
		  setTimeout(function(){ 
		  window.parent.jQuery.fancybox.close();
		  //window.parent.close(); 
		  window.parent.location.reload()
		  }, 200);  
		 });
	</script>
<?php } ?>
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
.nyroModalCont {
    top: 0 !important;
}
.nyroModalCloseButton{
	top: 5px !important;
}
</style>
			

    <!--<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />
	<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>-->
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/css/bootstrap-select.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.min.js"></script>
	
	<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>-->
	
	  	<div class="clearfix"></div>
				<div class="portlet light">
					<div class="portlet-title">
						<div class="caption font-green-sharp"><i class="fa fa-calendar-plus-o font-green-sharp"></i>
							Create Appointment 
						</div>
					</div>
					<div class="portlet-body form">
					<?php echo $this->Session->flash(); ?>
						<form  method="post" >
							<div class="form-body">
								<div class="form-group">
									<label for="some21" style="display:block;">Contacts</label>	
									<select data-show-subtext="true" data-live-search="true" id="contacts" class="form-control selectpicker"  name="data[Appointment][contact_id]" >	
									
										<?php foreach($contact as $contact_arr){ ?>
										<!--if(trim($contact_arr['Contact']['name'])!=''){  ?>-->
					                       <option data-subtext="<?php echo $contact_arr['Contact']['phone_number']?>" value="<?php echo $contact_arr['Contact']['id']?>"><?php echo $contact_arr['Contact']['name']?></option>
				                      	<?php } ?>
				                      
									</select>
								</div>							
								<div class="form-group">
									<label>Appointment Date/Time</label>
									<input type="text"  name="data[Appointment][app_date_time]" id="sent_on" class="form-control" onchange="reminder_app(this.value)" placeholder="Appointment date/time" value="<?php echo date('d-m-Y H:i', strtotime($_REQUEST['date']));?>">
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
										<input type="text"  name="data[Appointment][appointment_reminder_date]" id="app_date"  class="form-control">
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
