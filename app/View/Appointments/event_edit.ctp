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
			

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
			<div class="clearfix"></div>
				<div class="portlet light">
					<div class="portlet-title">
						<div class="caption font-green-sharp"><i class="fa fa-calendar-check-o font-green-sharp"></i>
							Edit Appointment
						</div>
					</div>
					<div class="portlet-body form">
					<?php echo $this->Session->flash(); ?>
						<form  method="post" >
							<input type="hidden" name="data[Appointment][id]" value="<?php echo $appointment['Appointment']['id']?>" >
							<div class="form-body">
								<div class="form-group">
									<label for="some21" style="display:block;">Contact<span class="required_star"></span></label>	
									
									<?php if(trim($appointment['Contact']['name']) == '') { ?>
									<input type="text" name="data[Appointment][contact_name]" class="form-control" value="<?php echo $appointment['Contact']['phone_number'];?>" disabled>
									<? } else { ?>
									<input type="text" name="data[Appointment][contact_name]" class="form-control" value="<?php echo $appointment['Contact']['name'];?> - <?php echo $appointment['Contact']['phone_number'];?>" disabled>
									<? } ?>
								</div>							
								<div class="form-group">
									<label>Appointment Date/Time</label>
									<input type="text"  name="data[Appointment][app_date_time]" id="sent_on" class="form-control" placeholder="Appointment date/time" value="<?php echo date('d-m-Y H:i', strtotime($appointment['Appointment']['app_date_time']));?>">
								</div>
								<div class="form-group">
									<label for="some21">Appointment Status<span class="required_star"></span></label>	
									
									<select id="contacts" class="form-control"  name="data[Appointment][appointment_status]" >
										<option value="0" <?php if($appointment['Appointment']['appointment_status']==0){ echo 'selected';} ?>selected>Unconfirmed</option>
										<option value="1" <?php if($appointment['Appointment']['appointment_status']==1){ echo 'selected';} ?>>Confirmed</option>
										<option value="2" <?php if($appointment['Appointment']['appointment_status']==2){ echo 'selected';} ?>>Cancelled</option>
										<option value="3" <?php if($appointment['Appointment']['appointment_status']==3){ echo 'selected';} ?>>Reschedule</option>
									</select>
									
								</div>
								<div class="form-group">
        							<label>Notify&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="If checked, it will notify the contact via SMS of their updated appointment details." data-original-title="Notify" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
        							</label>
        							<div class="radio-list" >				
        								<input name="data[Appointment][notify]" value="0" type="checkbox" id="notify" onclick="checknotify()"/>
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

<script type="text/javascript">
$('#sent_on').datetimepicker({
     minDate: 0,
    showSecond: false,
    //showMinute: false,
	dateFormat: 'dd-mm-yy',
	//timeFormat: 'hh',
	timeFormat: 'hh:mm',
	stepHour: 1,
	stepMinute: 5,
	stepSecond: 10,
	
});


</script>
