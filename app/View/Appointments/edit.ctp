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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
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
						<?php echo('Edit Appointment Form');?>
					</div>
					<div class="portlet-title">
						<div class="caption font-red-sunglo">
							<i class="fa fa-calendar font-red-sunglo"></i>
						</div>
					</div>
					<div class="portlet-body form">	
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
									<input type="text"  name="data[Appointment][app_date_time]" id="sent_on" class="form-control" placeholder="Appointment date/time" value="<?php echo  date('Y-m-d H:i', strtotime($appointment['Appointment']['app_date_time']));?>" >
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
											<button type="submit" name="submit" class="btn blue">Update</button>
										
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
			jQuery(function(){
				jQuery("#sent_on").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Please select the date/time"        
				});				
			});		
		</script>