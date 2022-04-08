<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title">Kiosk Builder</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
									<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<span>Kiosk Builder</span>
					</li>
				</ul>  
					<div class="page-toolbar">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/kiosks/add" title="Add Group"><i class="fa fa-plus-square-o"></i> Create New Kiosk</a>
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
                                        <i class="fa fa-tablet font-red"></i><span class="caption-subject font-red sbold uppercase">Kiosks</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <!--<div class="table-scrollable">
					<table class="table table-striped table-bordered table-hover table-condensed">-->
					<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Kiosk URL</th>
                                                    <th>Created</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <!--<tbody>-->
											<?php foreach ($kiosks as $kiosk): ?>
                                                <tr>
                                                    <td><?php echo $kiosk['Kiosks']['name']; ?></td>
                                                    <td><a href="<?php echo SITE_URL.'/kiosks/view/'.$kiosk['Kiosks']['unique_id'];?>" target="_blank" style="color:#005580; text-decoration: underline"><?php echo SITE_URL.'/kiosks/view/'.$kiosk['Kiosks']['unique_id'];?></a> &nbsp;</td>
                                                    <td><?php echo $kiosk['Kiosks']['created']; ?></td>
													<td>
													<!--<a target="_blank" class="btn green btn-outline btn-sm" href="<?php echo SITE_URL;?>/kiosks/view/<?php echo $kiosk['Kiosks']['unique_id'];?>">View</a>
													<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $kiosk['Kiosks']['id']),array('class' => 'btn green btn-outline btn-sm')); ?>
													<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $kiosk['Kiosks']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this kiosk ?', true))); ?>		-->

<?php echo $this->Html->link('<i class="icon-screen-tablet" style="font-size:14px"></i>',array('action'=>'view/'.$kiosk['Kiosks']['unique_id']),array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'View Kiosk','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$kiosk['Kiosks']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$kiosk['Kiosks']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this kiosk?', true))); ?>

		
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