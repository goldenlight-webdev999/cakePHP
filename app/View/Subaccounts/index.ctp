<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title">Sub-Accounts
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
					<span>Sub-Accounts</span>
				</li>
			</ul> 
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
						<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li>
							<a href="<?php echo SITE_URL;?>/subaccounts/add" title="Add Sub-Account">
								<i class="fa fa-plus-square-o"></i> Add Sub-Account
							</a>
						</li>
					</ul>
				</div>
			</div>		
		</div>
		<div class="clearfix"></div>
		<?php echo $this->Session->flash(); ?>
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-users font-red"></i><span class="caption-subject font-red sbold uppercase">Sub-Accounts</span> </div>
				<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->
			</div>
			<div class="portlet-body">
			<!--	<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>
								<th>Username</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th>
								
								<th class="actions"><?php echo('Actions');?></th>
							</tr>
						</thead>
						<?php
							$i = 0;
							foreach ($subaccounts as $subaccount):
								$class = null;
								if ($i++ % 2 == 0) {
									$class = ' class="altrow"';
								}
						?>
						<!--<tbody>-->
							<tr <?php echo $class;?>>
							    <td><?php echo $subaccount['Subaccount']['username'];?>&nbsp;</td>
								<td><?php echo $subaccount['Subaccount']['first_name'];?>&nbsp;</td>
								<td><?php echo $subaccount['Subaccount']['last_name'];?>&nbsp;</td>
								<td><?php echo $subaccount['Subaccount']['email'];?>&nbsp;</td>
								<td class="actions">
									<!--<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $birthday['Birthday']['id']),array('class' => 'btn green btn-outline btn-sm')); ?>
									<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $birthday['Birthday']['id'],'0'), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this birthday wish?', true))); ?>-->

<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$subaccount['Subaccount']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$subaccount['Subaccount']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete','style'=>'margin-right:0px'), sprintf(__('Are you sure you want to delete this sub-account?', true))); ?>

<?php echo $this->Html->link('<i class="icon-shield" style="font-size:14px"></i>',array('action'=>'permissions',$subaccount['Subaccount']['id']),array('class'=> 'btn purple-plum btn-sm nyroModal','escape'=> false,'title'=>'View Permissions','style'=>'margin-right:0px')); ?>
								</td>
							</tr>
						<!--</tbody>-->
						<?php endforeach; ?>
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