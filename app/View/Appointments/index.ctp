<script type="text/javascript" src="<?php echo SITE_URL; ?>/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" type="text/css"  href="<?php echo SITE_URL; ?>/fancybox/jquery.fancybox.css">
<style>
.fancybox-outer, .fancybox-inner {
    position: relative;
    height: 500px!important;
}
</style>
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
	$('.fancybox').fancybox();
});

function scheduleappt(apptid,contactid,apptdate,cancelkeyword,confirmkeyword,reschedulekeyword){
    
    currentDate='<?php echo date('Y-m-d'); ?>';
	var d = new Date(currentDate).toISOString();
	var res = d.split("T"); 
	var cur_res = res[0].split("-");			
	var dates =  [ cur_res[0] , cur_res[1] ,cur_res[2]].join("-");
		
	var res = d.replace("T", " "); 
	res = res.replace(".000Z", ""); 
	
	url="<?php echo SITE_URL; ?>/schedulers/add?date="+res+"&apptid="+apptid+"&contactid="+contactid+"&apptdate="+apptdate+"&cancelkeyword="+encodeURI(cancelkeyword)+"&confirmkeyword="+encodeURI(confirmkeyword)+"&reschedulekeyword="+encodeURI(reschedulekeyword);
	//alert(url);
	$("#pop_fancy"+apptid).attr("href",url);			
	$("#pop_fancy"+apptid).trigger('click');	
	
}
</script>

		
		<div class="page-content-wrapper">
			<div class="page-content">              
				<h3 class="page-title"> <?php echo('Appointment List ');?></h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<span><?php echo('Appointment List');?></span>
						</li>
					</ul>  
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
							<i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li>
									<a href="<?php echo SITE_URL;?>/appointments/add" title="Add Appointment"><i class="fa fa-plus-square-o"></i> Add  Appointment</a>
									
								</li>
								<li>
									<a href="<?php echo SITE_URL;?>/appointments/upload" title="Import Appointments"><i class="fa fa-upload"></i> Import  Appointments</a>
									
								</li>
							</ul>
						</div>
					</div>				
				</div>
				<?php echo $this->Session->flash(); ?>				
				<div class="clearfix"></div>
				<div class="portlet light bordered">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-calendar font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Appointment List');?></span> </div>
					</div>
					<div class="portlet-body">
						<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover">-->
							<table  id="datatable_apptindex" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>
										<th scope="col">Name</th>
										<th scope="col">Number</th>
										<th scope="col">Email</th>
										<th scope="col">Status</th>
										<th scope="col">Reminder Scheduled</th>
										<th scope="col">Appt Date</th>
										<th scope="col">Created</th>
										<th scope="col"> Action </th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($appointment as $appointment_arr){?>
									<tr> 
										<td class="tc"><?php echo $appointment_arr['Contact']['name'];?></td>
										<td class="tc"><?php echo $appointment_arr['Contact']['phone_number'];?></td>
										<td class="tc"><?php echo $appointment_arr['Contact']['email'];?></td>
										<td class="tc" >
																				
											 <?php if($appointment_arr['Appointment']['appointment_status']==0){ ?>
												<span style="padding:2px 8px;color:#FFF;background-color:#B7378D">Unconfirmed</span>
											 <?php }elseif($appointment_arr['Appointment']['appointment_status']==1){ ?>	
												<span style="padding:2px 8px;color:#FFF;background-color:#B7378D">Confirmed</span>
											<?php }elseif($appointment_arr['Appointment']['appointment_status']==2){ ?>	
												<span style="padding:2px 8px;color:#FFF;background-color:#B7378D">Cancelled</span>
											 <?php }else{ ?>
												<span style="padding:2px 8px;color:#FFF;background-color:#B7378D">Reschedule</span>
											 <?php } ?>
											
										</td>
										<td class="tc">
										    <?php if($appointment_arr['Appointment']['scheduled']==0){ ?>
										         NO
										    <?php }else{ ?>
										         YES
										    <?php } ?>
										</td>
										<td class="tc"><?php echo date('Y-m-d h:i A', strtotime($appointment_arr['Appointment']['app_date_time']));?></td>
										<td class="tc"><?php echo date('Y-m-d H:i:s', strtotime($appointment_arr['Appointment']['created']));?></td>
										<td class="tc">
										    
										    <?php if($appointment_arr['Contact']['un_subscribers']==0){ ?>
										       <a id="pop_fancy<?php echo $appointment_arr['Appointment']['id']?>" href="#null" class="btn purple-plum btn-sm fancybox fancybox.iframe" onclick="scheduleappt(<?php echo $appointment_arr['Appointment']['id']?>,<?php echo $appointment_arr['Contact']['id']?>,'<?php echo $appointment_arr['Appointment']['app_date_time']?>','<?php echo $appointmentsetting['AppointmentSetting']['cancel_keyword'];?>','<?php echo $appointmentsetting['AppointmentSetting']['confirm_keyword'];?>','<?php echo $appointmentsetting['AppointmentSetting']['reschedule_keyword'];?>')" style="margin-right:0px" title="Schedule Appointment Reminder"><i class="icon-calendar" style="font-size:14px"></i></a>
										    <?php } ?>
										    
										    <?php if($appointment_arr['Contact']['un_subscribers']==0){ ?>
										    <?php if($userperm['sendsms']=='1'){ ?>
										    <?php if(API_TYPE==0){?>
							
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

 								<?php }else if(API_TYPE==1){ ?>
							
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'nexmo_send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								<?php }else if(API_TYPE==2){ ?>
								
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'slooce_send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								<?php }else if(API_TYPE==3){ ?>
								
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'plivo_send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

                                <?php }else if(API_TYPE==4){ ?>
								
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'bandwidth_send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

                                <?php }else if(API_TYPE==5){ ?>
								
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'signalwire_send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
                                <?php }else if(API_TYPE==6){ ?>
								
<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'ytel_send_sms',$appointment_arr['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								<?php }}} ?>
								
								<?php if(trim($appointment_arr['Contact']['email']) !='') { ?>
								<a id="pop_fancy" href="<?php echo SITE_URL; ?>/contacts/send_email/<?php echo $appointment_arr['Contact']['email'];?>" title='Send Email' style="margin-right:0px" class="btn yellow-casablanca btn-sm fancybox fancybox.iframe"><i class="fa fa-envelope-o" style="font-size:14px"></i></a>
<?} ?>
								
								<?php if(API_TYPE==0 || API_TYPE==5 || API_TYPE==7){?>
								
								<?php if(trim($appointment_arr['Contact']['fax_number']) !='') { ?>

<?php echo $this->Html->link('<i class="fa fa-fax" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'send_fax',$appointment_arr['Contact']['fax_number'],'appointments'),array('class'=> 'btn yellow-gold btn-sm nyroModal','escape'=> false,'title'=>'Send Fax','style'=>'margin-right:0px'));?>

<?} ?>
								
								<?php } ?>
										    <?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$appointment_arr['Appointment']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>
											<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$appointment_arr['Appointment']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this appointment?', true))); ?>

										</td>
										
										
									</tr>
								<?php }?>
								</tbody>
							</table>
						
						<!--</div>-->
						<!--<div class="dataTables_paginate paging_bootstrap_number">
						<ul class="pagination" style="visibility: visible;">
							<ul class="pagination">
							    <li class="paginate_button first" aria-controls="dynamic-table" tabindex="0" id="dynamic-table_first"><?php echo $this->Paginator->first(__('<< First', true), array('class' => 'disabled'));?></li>    
								<li class="paginate_button previous disabled" aria-controls="dynamic-table" tabindex="0" id="dynamic-table_previous"><?php
								echo $this->Paginator->prev('<', array(), null, array('class' => 'prev disabled'));?>
								</li>
								<li>
								<?php echo $this->Paginator->numbers();?>
								</li>
								<li class="paginate_button next" aria-controls="dynamic-table" tabindex="0" id="dynamic-table_next"><?php echo $this->Paginator->next('>', array(), null, array('class' => 'next disabled'));?>
								<li class="paginate_button last" aria-controls="dynamic-table" tabindex="0" id="dynamic-table_last"><?php echo $this->Paginator->last(__('Last >>', true), array('class' => 'disabled'));?></li>
								</li>
							</ul>
						</ul>
					</div>-->
					
					<div class="pagination pagination-large">
                        <ul class="pagination">
                        <?php
                            echo $this->Paginator->first(__('<< First'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                            echo $this->Paginator->prev(__('<'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                            echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
                            echo $this->Paginator->next(__('>'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                            echo $this->Paginator->last(__('Last >>'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                        ?>
                        </ul>
                    </div>
                    
					</div>
				</div>
			</div>
		</div>
		