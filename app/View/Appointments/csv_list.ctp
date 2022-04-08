
		<div class="page-content-wrapper">
			<div class="page-content">              
				<h3 class="page-title"> <?php echo('Unknown Appointment Numbers');?></h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
					        <a href="<?php echo SITE_URL;?>/appointments/index">Appointment List</a>
				        </li>
	
					</ul>  
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
							<i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li>
									<a href="<?php echo SITE_URL;?>/appointments/upload" title="Add Appointment"><i class="fa fa-plus-square-o"></i> Back</a>
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
							<i class="fa fa-user-times font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Unknown Appointment Numbers');?></span> </div>
					</div>
					<div class="portlet-body">
						<table  id="datatable_receiptsnumbersindex" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
							<thead>
								<tr>
									<th scope="col">Number</th>
									<th scope="col">Status</th>
									<th scope="col">Appointment Date/Time</th>
									<!--<th scope="col">Created</th>-->
								</tr>
							</thead>
							<tbody>
								<?php foreach($appointment_arr as $appointment){?>
									<tr> 
										<td class="tc"><?php echo $appointment['AppointmentCsv']['phone_number'];?></td>
										<td class="tc"><?php echo $appointment['AppointmentCsv']['appointment_status'];?></td>
										<td class="tc"><?php echo date('Y-m-d h:i A', strtotime($appointment['AppointmentCsv']['app_date_time']));?></td>
										<!--<td class="tc"><?php echo date('Y-m-d H:i:s', strtotime($appointment['AppointmentCsv']['created']));?></td>-->
									</tr> 
								<?php }?>
							</tbody>
						</table>
						<div class="dataTables_paginate paging_bootstrap_number">
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
						</div>
					</div>
				</div>
			</div>
		</div>
		