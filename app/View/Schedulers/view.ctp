
<script>
var siteurl = '<?php echo SITE_URL ?>';
</script>
<link href='<?php echo SITE_URL; ?>/scheduler/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo SITE_URL; ?>/scheduler/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo SITE_URL; ?>/scheduler/fullcalendar/moment.min.js'></script>
<script src='<?php echo SITE_URL; ?>/scheduler/fullcalendar/fullcalendar.js'></script>
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
				add_url="<?php echo SITE_URL; ?>/schedulers/add?date="+res;
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
			<?php foreach($singlemessage as $singlemessages) { ?>
				{	
					sent: "<?php echo $singlemessages['ScheduleMessage']['sent'];?>",
					
					event_id: "<?php echo $singlemessages['ScheduleMessage']['id'];?>",
				
					recurring_id: "<?php echo $singlemessages['ScheduleMessage']['recurring_id'];?>",
				
					<?php if($singlemessages['ScheduleMessage']['msg_type']==2){ ?>
					    <?php if(trim($singlemessages['Contact']['name'])!='') { ?>
					          title: "<?php echo $singlemessages['Contact']['name'];?> : <?php echo str_replace(array("’",'"',"'"),'',preg_replace('/\s+/', ' ', $singlemessages['ScheduleMessage']['mms_text']));?>",
					    <?} else {?>       
						      title: "<?php echo $singlemessages['Contact']['phone_number'];?> : <?php echo str_replace(array("’",'"',"'"),'',preg_replace('/\s+/', ' ', $singlemessages['ScheduleMessage']['mms_text']));?>",
						<?} ?>      
					<?php }else{?>
					    <?php if(trim($singlemessages['Contact']['name'])!='') { ?>
					          title: "<?php echo $singlemessages['Contact']['name'];?>: <?php echo str_replace(array("’",'"',"'"),'',preg_replace('/\s+/', ' ', $singlemessages['ScheduleMessage']['message']));?>",
					    <?} else {?>       
						      title: "<?php echo $singlemessages['Contact']['phone_number'];?>: <?php echo str_replace(array("’",'"',"'"),'',preg_replace('/\s+/', ' ', $singlemessages['ScheduleMessage']['message']));?>",
						<?} ?>  
					<?php } ?>
					url: '<?php echo SITE_URL;?>/schedulers/events_edit_pop/<?php echo $singlemessages['ScheduleMessage']['id'];?>/<?php echo $singlemessages['ScheduleMessage']['recurring_id'];?>',
					start: '<?php echo date('Y-m-d H:i',strtotime($singlemessages['ScheduleMessage']['send_on'])); ?>',
					<?php if($singlemessages['ScheduleMessage']['sent']==1){?>
					backgroundColor: '#1BBC9B !important',
					<?php }else{ ?>
					backgroundColor: '#3a87ad !important',
					<?php } ?>
				},
			<?php } ?>
			<?php foreach($message as $events) { ?>
				{				
					sent: "<?php echo $events['ScheduleMessage']['sent'];?>",
					event_id: "<?php echo $events['ScheduleMessage']['id'];?>",
					recurring_id: "<?php echo $events['ScheduleMessage']['recurring_id'];?>",
					msg_type: "<?php echo $events['ScheduleMessage']['msg_type'];?>",
				
					<?php if($events['ScheduleMessage']['msg_type']==2){ ?>
						title: "<?php echo $events['Group']['group_name'];?> : <?php echo str_replace(array("’",'"',"'"),'',preg_replace('/\s+/', ' ', $events['ScheduleMessage']['mms_text']));?>",
					<?php }else{?>
						title: "<?php echo $events['Group']['group_name'];?> : <?php echo str_replace(array("’",'"',"'"),'',preg_replace('/\s+/', ' ', $events['ScheduleMessage']['message']));?>",
					<?php } ?>
					<?php if($events['ScheduleMessage']['msg_type']==3){ ?>
					    url: '<?php echo SITE_URL;?>/groups/edit_broadcast/<?php echo $events['ScheduleMessage']['voice_id'];?>',
					<?php }else{?>
					    url: '<?php echo SITE_URL;?>/schedulers/events_edit_pop/<?php echo $events['ScheduleMessage']['id'];?>/<?php echo $events['ScheduleMessage']['recurring_id'];?>',
					<?php } ?>
					start: '<?php echo date('Y-m-d H:i',strtotime($events['ScheduleMessage']['send_on'])); ?>',
					<?php if($events['ScheduleMessage']['sent']==1){?>
					backgroundColor: '#1BBC9B !important',
					<?php }else{ ?>
					backgroundColor: '#3a87ad !important',
					<?php } ?>
				},
			<?php } ?>				
		],			
		timeFormat: 'h(:mm)T',
		});
	$('.fancybox').fancybox();
});  
function confirmfunction(url){
	var r = confirm("Are you sure you want to delete this single event?");
	if (r == true) {
		window.parent.location.assign(url);
	}else{
		return false;
	} 
}
function confirmfunction_multi(url){
	var r = confirm("Are you sure you want to delete all events in this series?");
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
		<h3 class="page-title"> Calendar/Scheduler</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<span>Calendar</span>
					</li>
				</ul>  			
			</div>
			<?php echo $this->Session->flash(); ?>				
			<div class="clearfix"></div>
			<div class="portlet box red">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-calendar"></i>Scheduled Calendar 
					</div>
					<div class="tools">
						<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
						<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
						<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
					</div>
				</div>
				<div class="portlet-body">
				    <div class="input">
				        <span style="padding:2px 8px;color:white;background-color:#B7378D">SMS Scheduled</span>
				        <span style="padding:2px 8px;color:white;background-color:#B7378D">Voice Scheduled</span>
						<span style="padding:2px 8px;color:white;background-color:#B7378D">Sent</span>
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

