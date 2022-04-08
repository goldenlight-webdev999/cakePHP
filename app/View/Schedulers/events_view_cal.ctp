<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
			<?php if(!empty($ScheduleMessage)){ ?>
				<?php 
					if($ScheduleMessage['ScheduleMessage']['msg_type']==1){
						$message=$ScheduleMessage['ScheduleMessage']['message'];
						 $msg_type='SMS';
					}else if($ScheduleMessage['ScheduleMessage']['msg_type']==2){
						$message=$ScheduleMessage['ScheduleMessage']['mms_text'];
						$image_url=$ScheduleMessage['ScheduleMessage']['message'];
						 $msg_type='MMS';
					}
				
				?>
			<div class="portlet box blue-dark">		
				<div class="portlet-title">
						<div class="caption">
						<!--<i class="fa fa-users"></i>-->
						<?php  echo('Contact Scheduled Message Details');?>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<!--div><//?php $i = 0; $class = ' class="altrow"';?></div-->
							<table class="table table-bordered table-striped">
									<tr>												
										<td style="width:15%"><?php echo('Send On'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessage['ScheduleMessage']['send_on']; ?>
											</span>
										</td>
									</tr>	
									<tr>												
										<td style="width:15%"><?php echo('Send From'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
                                                                                    <?php if($ScheduleMessage['ScheduleMessage']['alphasender_input'] == '') { 
										           echo $ScheduleMessage['ScheduleMessage']['sendfrom']; 
                                                                                    }else{
                                                                                           echo $ScheduleMessage['ScheduleMessage']['alphasender_input']; 
                                                                                    }?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Send To'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessage['Contact']['phone_number']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Message'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
													 <?php echo $message; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Type'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
											 <?php echo $msg_type; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Media'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php 
													if($ScheduleMessage['ScheduleMessage']['msg_type']==2){
														if($image_url!=''){
															$check=strpos($image_url,":");
															if($check!=''){
																$comma=strpos($image_url,",");
																if($comma!=''){
																	$image_arr=explode(",",$image_url);
																		foreach($image_arr as $value){	?>
																			<img src="<?php echo $value; ?>" height="80px" width="65px" />
																		<?php }
																}else{	?>
																	<img src="<?php echo $image_url ?>" height="80px" width="65px" />
																<?php }
															}
														}else{ ?>
														<img src="<?php echo $ScheduleMessage['ScheduleMessage']['pick_file'] ?>" height="80px" width="70px" />
															<?php
														}
													}
													?>
											</span>
										</td>
									</tr>
							</table>							
						</div>
					</div>
				</div>
				
			</div>
			<?php } ?>
						<?php if(!empty($ScheduleMessageGroup)){ ?>
						<?php 
							if($ScheduleMessageGroup['ScheduleMessage']['msg_type']==1){
								$message=$ScheduleMessageGroup['ScheduleMessage']['message'];
								$msg_type='SMS';
							}else if($ScheduleMessageGroup['ScheduleMessage']['msg_type']==2){
								$message=$ScheduleMessageGroup['ScheduleMessage']['mms_text'];
								$image_url=$ScheduleMessageGroup['ScheduleMessage']['message'];
								$msg_type='MMS';
							}else if($ScheduleMessageGroup['ScheduleMessage']['msg_type']==3){
							    $message=$ScheduleMessageGroup['ScheduleMessage']['message'];
							    $msg_type='VOICE';
							}
						
						?>
					
			<div class="portlet box blue-dark">		
				<div class="portlet-title">
						<div class="caption">
						<!--<i class="fa fa-users"></i>-->
						<?php  echo('Group Scheduled Message Details');?>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<!--div><//?php $i = 0; $class = ' class="altrow"';?></div-->
							<table class="table table-bordered table-striped">
									<tr>												
										<td style="width:15%"><?php echo('Send On'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessageGroup['ScheduleMessage']['send_on']; ?>
											</span>
										</td>
									</tr>	
									<tr>												
										<td style="width:15%"><?php echo('Send From'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												

                                                                                    <?php if($ScheduleMessageGroup['ScheduleMessage']['alphasender_input'] == '') { 
										           echo $ScheduleMessageGroup['ScheduleMessage']['sendfrom']; 
                                                                                    }else{
                                                                                           echo $ScheduleMessageGroup['ScheduleMessage']['alphasender_input']; 
                                                                                    }?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Group'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessageGroup['Group']['group_name']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('City'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessageGroup['ScheduleMessage']['city']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('State/Province'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessageGroup['ScheduleMessage']['state']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Postal Code'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $ScheduleMessageGroup['ScheduleMessage']['zip']; ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Message'); ?> </td>
										
										<td style="width:35%">
											<span class="text-muted">										
											
												<?php if(strpos($ScheduleMessageGroup['ScheduleMessage']['message'],"http") !== false && $ScheduleMessageGroup['ScheduleMessage']['msg_type']==3) {?>
            						                <audio class="audio" controls="controls">
            							            <source src=<?php echo $message; ?> type="audio/mpeg">
            						                </audio>
            							        <? }else { ?>        
                								    <?php echo $message; ?>
                								<? } ?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Type'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $msg_type;?>
											</span>
										</td>
									</tr>
									<tr>												
										<td style="width:15%"><?php echo('Media'); ?> </td>
										<td style="width:35%">
											<span class="text-muted">												
												<?php 
													if($ScheduleMessageGroup['ScheduleMessage']['msg_type']==2){
														if($image_url!=''){
															$check=strpos($image_url,":");
															if($check!=''){
																$comma=strpos($image_url,",");
																if($comma!=''){
																	$image_arr=explode(",",$image_url);
																		foreach($image_arr as $value){	?>
																			<img src="<?php echo $value; ?>" height="80px" width="65px" />
																		<?php }
																}else{	?>
																	<img src="<?php echo $image_url ?>" height="80px" width="65px" />
																<?php }
															}
														}else{ ?>
														<img src="<?php echo $ScheduleMessageGroup['ScheduleMessage']['pick_file'] ?>" height="80px" width="70px" />
															<?php
														}
													}
													?>
											</span>
										</td>
									</tr>
							</table>							
						</div>
					</div>
				</div>
				
			</div>
			<?php } ?>