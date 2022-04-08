
<!--<link href="<?php echo SITE_URL; ?>/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo SITE_URL; ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/pages/scripts/table-datatables-managed.js" type="text/javascript"></script>-->


<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo('Contests');?>
			<small></small>
		</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
						<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<span><?php echo('Contests');?></span>
					</li>
				</ul> 
				<div class="page-toolbar">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
							

<li>
<a href="<?php echo SITE_URL;?>/contests/add" title="Add Contest"><i class="fa fa-plus-square-o"></i> Add Contest</a>
								
                            </li>
                        </ul>
                    </div>
                </div>	
				<div class="clearfix"></div>
			</div>
			<?php echo $this->Session->flash(); ?>
			<div class="portlet light bordered">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-trophy font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Contests');?></span>
					</div>
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->
				</div>
				<div class="portlet-body">
				  
					<!--<div class="table-scrollable">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
							<thead>
								<tr>
									<th>Name </th>
									<th>keyword</th>
									<th>Group</th>
									<th>Start</th>
									<th>End</th>
									<th>Max Entries</th>
									<th>Winning #</th>
									<th><?php echo('Actions');?></th>
								   
								</tr>
									<?php
								$i = 0;
								foreach ($contests as $contest):
									$class = null;
									if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
									}
								?>
							</thead>
							<!--<tbody>-->
								<tr <?php echo $class;?>>
									<td>
										<?php echo ucfirst($contest['Contest']['group_name']).'('.$contest['Contest']['totalsubscriber'].')'; ?> &nbsp;
									</td>
									<td>
										<?php echo $contest['Contest']['keyword']; ?>&nbsp;
									</td>
									
									<td>
										<?php echo $contest['Group']['group_name']; ?>&nbsp;
									</td>
									<td>
										<?php echo $contest['Contest']['startdate']; ?>&nbsp;
									</td>
									<td>
										<?php echo $contest['Contest']['enddate']; ?>&nbsp;
									</td>
									<td>
										<?php echo $contest['Contest']['maxentries']; ?>&nbsp;
									</td>
									
									<?php //echo $contest['Contest']['totalsubscriber']; ?>
									<td>
										<?php echo $contest['Contest']['winning_phone_number']; ?>&nbsp;
									</td>
									
									<td class="actions">
									    <!--<?php echo $this->Html->link(__('Send Contest', true), array('action' => 'sendcontest', $contest['Contest']['id']),array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>
										<?php echo $this->Html->link(__('Pick Winner', true), array('action' => 'contest_winner', $contest['Contest']['id']),array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>
										<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $contest['Contest']['id']),array('class' => 'btn green btn-outline btn-sm')); ?>
										<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $contest['Contest']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete?', true))); ?>-->

<?php 
if($userperm['sendsms']=='1' && $contest['Contest']['winning_phone_number']==''){
echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'sendcontest',$contest['Contest']['id']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send Contest','style'=>'margin-right:0px'));
}
?>

<?php echo $this->Html->link('<i class="icon-users" style="font-size:14px"></i>',array('action'=>'participants',$contest['Contest']['id']),array('class'=> 'btn purple-plum btn-sm nyroModal','escape'=> false,'title'=>'View Participants','style'=>'margin-right:0px')); ?>

<?php 
if($contest['Contest']['winning_phone_number']==''){
echo $this->Html->link('<i class="icon-trophy" style="font-size:14px"></i>',array('action'=>'contest_winner',$contest['Contest']['id']),array('class'=> 'btn yellow-gold btn-sm nyroModal','escape'=> false,'title'=>'Pick a Winner','style'=>'margin-right:0px'));
}
?>

<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$contest['Contest']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$contest['Contest']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this contest?', true))); ?>

									</td>
								</tr>
								<?php endforeach; ?>
							<!--</tbody>-->
						</table>
						
				<!--	</div>-->
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