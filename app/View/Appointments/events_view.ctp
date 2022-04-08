<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
			
			<div class="portlet box blue-dark">		
				<div class="portlet-title">
						<div class="caption">
						<!--<i class="fa fa-users"></i>-->
						<?php  echo('Appointment Details');?>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<!--div><//?php $i = 0; $class = ' class="altrow"';?></div-->
							<table class="table table-bordered table-striped">
									<tr>												
										<td style="width:15%"><?php echo('Name'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $appointment['Contact']['name']; ?>
											</span>
										</td>
									</tr>	
									
									<tr>												
										<td style="width:15%"><?php echo('Email'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $appointment['Contact']['email']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Number'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $appointment['Contact']['phone_number']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Status'); ?> </td>
										<td style="width:35%">
											<!--<span class="text-muted">												
												 <?php if($appointment['Appointment']['appointment_status']==0){ ?>
												<button type="button"  class="btn btn-danger btn-xs disabled ">Unconfirmed</button>
											 <?php }elseif($appointment['Appointment']['appointment_status']==1){ ?>	
												<button   type="button" class="btn btn-success btn-xs disabled">Confirmed</button>
											<?php }elseif($appointment['Appointment']['appointment_status']==2){ ?>	
												<button type="button"  class="btn btn-info btn-xs disabled">Canceled</button>
											 <?php }else{ ?>
												<button  type="button" class="btn btn-warning btn-xs disabled">Reschedule</button>
											 <?php } ?>
											</span>-->
											
											<?php if($appointment['Appointment']['appointment_status']==0){ ?>
												<button type="button"  class="btn btn-info btn-xs disabled " >Unconfirmed</button>
												<!--<td class="tc">Unconfirmed</td>-->
											 <?php }elseif($appointment['Appointment']['appointment_status']==1){ ?>	
												<button   type="button" class="btn btn-xs disabled" style="color:white;background-color:<?php echo $appointmentsetting['AppointmentSetting']['confirm_color_picker'];?>">Confirmed</button>
												<!--<td class="tc" style="background-color:<?php echo $appointmentsetting['AppointmentSetting']['confirm_color_picker'];?>">Confirmed</td>-->
											<?php }elseif($appointment['Appointment']['appointment_status']==2){ ?>	
												<button type="button"  class="btn btn-xs disabled" style="color:white;background-color:<?php echo $appointmentsetting['AppointmentSetting']['cancel_color_picker'];?>">Cancelled</button>
												<!--<td class="tc" style="background-color:<?php echo $appointmentsetting['AppointmentSetting']['cancel_color_picker'];?>">Cancelled</td>-->
											 <?php }else{ ?>
												<button  type="button" class="btn btn-xs disabled" style="color:white;background-color:<?php echo $appointmentsetting['AppointmentSetting']['reschedule_color_picker'];?>">Reschedule</button>
												<!--<td class="tc" style="background-color:<?php echo $appointmentsetting['AppointmentSetting']['reschedule_color_picker'];?>">Reschedule</td>-->
											 <?php } ?>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Appointment Date'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo date('Y-m-d h:i A', strtotime($appointment['Appointment']['app_date_time']));?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Created'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo date('Y-m-d H:i:s', strtotime($appointment['Appointment']['created']));?>
											</span>
										</td>
									</tr>
									
								
							</table>							
						</div>
					</div>
				</div>
				
			</div>
	
					
			
			