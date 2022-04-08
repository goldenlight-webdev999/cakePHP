<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Short Links</h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
								<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<span>Short Links </span>
				</li>
			</ul>  
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
						<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li><a href="<?php echo SITE_URL;?>/users/shortlinkadd" title="Add Short Link URL"><i class="fa fa-plus-square-o"></i> Add Short Link URL</a></li>
						<li><a href="<?php echo SITE_URL;?>/cronjobs/updateclicks" title="Refresh Clicks"><i class="fa fa-refresh"></i>Refresh Clicks</a></li>						
					</ul>
				</div>
			</div>				
		</div>
		<?php echo $this->Session->flash(); ?>				
		<div class="portlet light bprdered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-link font-red"></i><span class="caption-subject font-red sbold uppercase">Short Links</span>
				</div>				
			</div>
			<div class="portlet-body">
				<!--<div class="table-scrollable">
					<table class="table table-striped table-bordered table-hover">-->
					<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>
								<th>Name</th>
								<th>URL</th>
								<th>Short URL</th>
								<th>Clicks</th>
								<th class="actions"><?php echo('Actions');?></th>
							</tr>
						</thead>
							<?php
								$i = 0;
								foreach ($shortlink as $shortlinks):
								$class = null;
								if ($i++ % 2 == 0) {
								$class = ' class="altrow"';
								}
							?>
						<!--<tbody>-->
							<tr <?php echo $class;?>>
								<td><?php echo $shortlinks['Shortlink']['shortname']; ?> &nbsp;</td>
								<td><?php echo $shortlinks['Shortlink']['url']; ?> &nbsp;</td>
								<td><a href="<?php echo $shortlinks['Shortlink']['short_url']; ?>" target="_blank" style="color:#005580; text-decoration: underline"><?php echo $shortlinks['Shortlink']['short_url']; ?></a> &nbsp;</td>
								<td><?php echo $shortlinks['Shortlink']['clicks']; ?> &nbsp;</td>
								<td class="actions">
									<!--<a href="<?php echo SITE_URL;?>/messages/send_message?shortlink=<?php echo $shortlinks['Shortlink']['id']; ?>" class="btn blue btn-outline btn-sm">Send</a>
									<?php echo $this->Html->link(__('Delete', true), array('action' => 'shortlinkdelete', $shortlinks['Shortlink']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete?', true))); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller'=>'messages','action'=>'send_message?shortlink='.$shortlinks['Shortlink']['id']),array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'Send','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'shortlinkdelete',$shortlinks['Shortlink']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this short link?', true))); ?>
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