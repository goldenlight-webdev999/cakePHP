
<script>
var siteurl = '<?php echo SITE_URL ?>';
</script>
<link href='<?php echo SITE_URL; ?>/appointment/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo SITE_URL; ?>/appointment/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo SITE_URL; ?>/appointment/fullcalendar/moment.min.js'></script>
<script src='<?php echo SITE_URL; ?>/appointment/fullcalendar/fullcalendar.js'></script>

<style>
.fancybox-outer, .fancybox-inner {
    position: relative;
    height: 500px!important;
}
</style>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" type="text/css"  href="<?php echo SITE_URL; ?>/fancybox/jquery.fancybox.css">


<script>

$(document).ready(function() {			
	$.fancybox.update();
	$.fancybox.reposition();
	$(".fancybox").fancybox({
		closeClick  : false, // prevents closing when clicking INSIDE fancybox
		helpers     : { 
		overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
		}
	});
	$(function() {	
		$("#requests").fancybox();
	});
	setTimeout(function(){
		$('#flashMessage').hide();
	},5000);
	
	
});
</script>
<script>
$(document).ready(function() {
	$('#calendar').fullCalendar({		
		defaultDate: '<?php echo date('Y-m-d'); ?>',
		selectable: true,
		selectHelper: true,
		dragScroll : false,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay',

		},
		select: function(start, end) {
			currentDate='<?php echo date('Y-m-d'); ?>';
			var d = new Date(start).toISOString();
			var res = d.split("T"); 
			var cur_res = res[0].split("-");			
			var dates =  [ cur_res[0] , cur_res[1] ,cur_res[2]].join("-");
			if(dates >= currentDate){
				var res = d.replace("T", " "); 
				res = res.replace(".000Z", ""); 
				add_url="<?php echo SITE_URL; ?>/appointments/event_add?date="+res;
				$("#pop_fancy").attr("href",add_url);			
				$("#pop_fancy").trigger('click');
			}else{
				alert('You cannot select a date prior to the current date');
				$('#calendar').fullCalendar('unselect');
			}
		},
		editable: false,
		eventLimit: true, // allow "more" link when too many events
		events: [
			<?php foreach($appointment as $appointment_arr) { ?>
				{	
					
					event_id: "<?php echo $appointment_arr['Appointment']['id'];?>",
					
					title: "<?php echo $appointment_arr['Contact']['name'];?> - <?php echo $appointment_arr['Contact']['phone_number'];?>",
					
					<?php if($appointment_arr['Appointment']['appointment_status']==1){ ?>
						bgcolor: '<?php echo $appointmentsetting['AppointmentSetting']['confirm_color_picker'];?> !important',
					<?php } elseif($appointment_arr['Appointment']['appointment_status']==2){ ?>
						bgcolor: '<?php echo $appointmentsetting['AppointmentSetting']['cancel_color_picker'];?> !important',
					<?php } elseif($appointment_arr['Appointment']['appointment_status']==3){ ?>
						bgcolor: '<?php echo $appointmentsetting['AppointmentSetting']['reschedule_color_picker'];?> !important',
					<?php } ?>
					
					url: '<?php echo SITE_URL;?>/appointments/event_edit/<?php echo $appointment_arr['Appointment']['id'];?>',
					start: '<?php echo date('Y-m-d H:i',strtotime($appointment_arr['Appointment']['app_date_time'])); ?>',
					
				},
			<?php } ?>
						
		],			
		timeFormat: 'h(:mm)T',
		});
	$('.fancybox').fancybox();
});  
function confirmfunction(url){
	var r = confirm("Are you sure you want to delete?");
	if (r == true) {
		window.parent.location.assign(url);
	}else{
		return false;
	} 
}
	
function viewfunction(url){
	if(url!=''){	
		$("#pop_fancy_view").attr("href",url);			
		$("#pop_fancy_view").trigger('click');	
	}
}
function editfunction(url){
	if(url!=''){	
		$("#pop_fancy_edit").attr("href",url);			
		$("#pop_fancy_edit").trigger('click');	
	}
}
</script>

<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title">Appointments</h3>
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
			<?php echo $this->Session->flash(); ?>				
			<div class="clearfix"></div>
			<div class="portlet box red">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-calendar"></i>Appointment Calendar
					</div>
					<div class="tools">
						<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
						<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
						<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
					</div>
				</div>
				
				<div class="portlet-body">
				    <div class="input">
				        <span style="padding:2px 8px;color:white;background-color:#B7378D">Unconfirmed</span>
				        <span style="padding:2px 8px;color:white;background-color:#B7378D">Confirmed</span>
						<span style="padding:2px 8px;color:white;background-color:#B7378D">Cancelled</span>
						<span style="padding:2px 8px;color:white;background-color:#B7378D">Reschedule</span>
					</div><br/>
					<div id='calendar'></div>
					<a id="pop_fancy" href="#null" class="fancybox fancybox.iframe"></a>
					<a id="pop_fancy_view" href="#null" class="fancybox fancybox.iframe"></a>
					<a id="pop_fancy_edit" href="#null" class="fancybox fancybox.iframe"></a>
				</div>
		   </div>           
	</div>
</div> 

<script>
	function close(){
		window.location.reload();
	}
</script>		

