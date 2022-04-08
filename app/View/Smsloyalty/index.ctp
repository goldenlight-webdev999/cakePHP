		<div class="page-content-wrapper">
			<div class="page-content">              
				<h3 class="page-title"> Loyalty Programs</h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
								<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<span>Loyalty Programs</span>
						</li>
					</ul>  
						<div class="page-toolbar">
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
									<i class="fa fa-angle-down"></i>
								</button>
								<ul class="dropdown-menu pull-right" role="menu">
									<li>
									   <a href="<?php echo SITE_URL;?>/smsloyalty/add" title="Add Loyalty Program"><i class="fa fa-plus-square-o"></i> Add Loyalty Program</a>
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
								<i class="fa fa-gift font-red"></i><span class="caption-subject font-red sbold uppercase">Loyalty Programs</span>
							</div>
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->
						</div>
						<div class="portlet-body">
							<!--<div class="table-scrollable">
								<table class="table table-striped table-bordered table-hover table-condensed">-->
								<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
									<thead>
											<tr>                                                   
												<!--<th scope="col"><?php echo $this->Paginator->sort('Program','program_name');?></th>								
												<th scope="col"><?php echo $this->Paginator->sort('Group','group_id');?></th>								
												<th scope="col"><?php echo $this->Paginator->sort('Start','startdate');?></th>								
												<th scope="col"><?php echo $this->Paginator->sort('End','enddate');?></th>								
												<th scope="col"><?php echo $this->Paginator->sort('Goal','reachgoal');?></th>								
												<th scope="col"><?php echo $this->Paginator->sort('Punch Code','coupancode');?></th>-->
												
												<th>Program</th>								
												<th>Group</th>								
												<th>Start</th>								
												<th>End</th>								
												<th>Goal</th>								
												<th>Punch Code</th>
												<th class="actions"><?php echo('Actions');?></th>
											</tr>
											<?php
											$i = 0;
											foreach ($loyaltys as $loyalty):
											$class = null;
											if ($i++ % 2 == 0) {
											$class = ' class="altrow"';
											}
											?>
									</thead>
									<!--<tbody>-->
										<tr <?php echo $class;?>>
											<td><?php echo ucfirst($loyalty['Smsloyalty']['program_name']);?></td>
											<td><?php echo ucfirst($loyalty['Group']['group_name']);?></td>
											<td><?php echo date('m/d/Y',strtotime($loyalty['Smsloyalty']['startdate']));?></td>
											<td><?php echo date('m/d/Y',strtotime($loyalty['Smsloyalty']['enddate']));?></td>
											<td><?php echo ucfirst($loyalty['Smsloyalty']['reachgoal']);?></td>
											<td><?php echo $loyalty['Smsloyalty']['coupancode'];?></td>
											<td class="actions">
												<!--<?php echo $this->Html->link(__('View', true), array('action' => 'view', $loyalty['Smsloyalty']['id']),array('class' => 'btn blue btn-outline btn-sm')); ?>
												<?php echo $this->Html->link(__('Participants', true), array('action' => 'participants', $loyalty['Smsloyalty']['id']),array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>
												<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $loyalty['Smsloyalty']['id']),array('class' => 'btn green btn-outline btn-sm')); ?>
												<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $loyalty['Smsloyalty']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this loyalty program?', true))); ?>-->

<?php echo $this->Html->link('<i class="icon-badge" style="font-size:14px"></i>',array('action'=>'view',$loyalty['Smsloyalty']['id']),array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'View Program Details','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-users" style="font-size:14px"></i>',array('action'=>'participants',$loyalty['Smsloyalty']['id']),array('class'=> 'btn yellow-gold btn-sm nyroModal','escape'=> false,'title'=>'View Participants','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$loyalty['Smsloyalty']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$loyalty['Smsloyalty']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this loyalty program?', true))); ?>
											</td>
										</tr>
										<?php endforeach; ?>
									<!--</tbody>-->
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