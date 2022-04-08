</script><link href='http://learnwp.me/theme/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='http://learnwp.me/theme/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='http://learnwp.me/theme/fullcalendar/moment.min.js'></script>
<script src='http://learnwp.me/theme/fullcalendar/fullcalendar.js'></script>

<script>

	$(document).ready(function() {

		$('#calendar').fullCalendar({		
			defaultDate: '2017-04-25',		
			
			selectable: true,
			selectHelper: true,
			dragScroll : false,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month'
           	},
			select: function(start, end) {

			currentDate='2017-04-25';
			var d = new Date(start).toISOString();	
			
			var res = d.split("T"); 
			var cur_res = res[0].split("-");			
			var dates =  [ cur_res[0] , cur_res[1] ,cur_res[2]].join("-");
						
			if(dates >= currentDate){
				add_url="http://learnwp.me/calendars/events_add_pop/"+dates;	
				$("#pop_fancy").attr("href",add_url);			
				$("#pop_fancy").trigger('click');
			}else{
				alert('Sorry you cannot select prevous date');
				$('#calendar').fullCalendar('unselect');
			}
			
			},
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			
			events: [
								{
					event_id: "340",
					title: "07:30 AM-12:00 PM",
					url: 'http://learnwp.me/calendars/events_edit_pop/340',
					start: '2017-04-25',
				},
							{
					event_id: "341",
					title: "07:30 AM-12:00 PM",
					url: 'http://learnwp.me/calendars/events_edit_pop/341',
					start: '2017-04-26',
				},
							{
					event_id: "342",
					title: "07:30 AM-12:00 PM",
					url: 'http://learnwp.me/calendars/events_edit_pop/342',
					start: '2017-04-27',
				},
							{
					event_id: "343",
					title: "07:30 AM-12:00 PM",
					url: 'http://learnwp.me/calendars/events_edit_pop/343',
					start: '2017-04-28',
				},
							{
					event_id: "344",
					title: "07:30 AM-12:00 PM",
					url: 'http://learnwp.me/calendars/events_edit_pop/344',
					start: '2017-04-29',
				},
							{
					event_id: "345",
					title: "07:30 AM-12:00 PM",
					url: 'http://learnwp.me/calendars/events_edit_pop/345',
					start: '2017-04-30',
				},
							{
					event_id: "480",
					title: "06:15 AM-10:30 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/480',
					start: '2017-05-01',
				},
							{
					event_id: "481",
					title: "09:00 AM-10:00 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/481',
					start: '2017-05-02',
				},
							{
					event_id: "482",
					title: "06:15 AM-10:30 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/482',
					start: '2017-05-03',
				},
							{
					event_id: "483",
					title: "06:15 AM-10:30 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/483',
					start: '2017-05-04',
				},
							{
					event_id: "484",
					title: "06:15 AM-10:30 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/484',
					start: '2017-05-05',
				},
							{
					event_id: "485",
					title: "06:15 AM-10:30 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/485',
					start: '2017-05-06',
				},
							{
					event_id: "486",
					title: "06:15 AM-10:30 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/486',
					start: '2017-05-07',
				},
							{
					event_id: "487",
					title: "06:00 AM-06:00 AM",
					url: 'http://learnwp.me/calendars/events_edit_pop/487',
					start: '2017-05-02',
				},
							
			],
			
			timeFormat: 'h(:mm)T',
		
					});
		
		$('.fancybox').fancybox();
		
		


	});  
function confirmfunction(url){
	
		var r = confirm("Are you sure ?");
		if (r == true) {
		
		window.parent.location.assign(url);

		} else {
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

<div id='calendar'></div>
	<a id="pop_fancy" href="#null" class="fancybox fancybox.iframe"></a>
	<a id="pop_fancy_view" href="#null" class="fancybox fancybox.iframe"></a>
	<a id="pop_fancy_edit" href="#null" class="fancybox fancybox.iframe"></a>

</div>