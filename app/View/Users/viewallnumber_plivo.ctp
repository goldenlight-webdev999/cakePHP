<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Number List
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
					<span>Number List</span>
				</li>
			</ul>                
		</div>
		<div class="clearfix"></div>
		<?php echo $this->Session->flash(); ?>
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-phone font-red"></i><span class="caption-subject font-red sbold uppercase">Number List</span></div>
			</div>
			<div class="portlet-body">
				<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>
							    <th>Purchase Date</th>
								<th>Number</th>
								<th>SMS</th>
								<th>Voice</th>
								<th>Actions</th>
							</tr>
						</thead>
						<?php 
						$i = 0;
						foreach($plivoall_data as $invoicedetil) { 
						$class = null;
						if ($i++ % 2 == 0) {
							$class = ' class="altrow"';
						}
						?>
						<!--<tbody>-->
							<tr <?php echo $class;?>> 
								<td>
								<?php echo $invoicedetil['UserNumber']['created'] ?>
								</td>
								<td>
								<?php echo $invoicedetil['UserNumber']['number'] ?>
								</td>
								<td><?php if($invoicedetil['UserNumber']['sms']==1){ ?>
                                      YES
								<?php }else{ ?>
								      NO
								<?php } ?>
								</td>
								<td><?php if($invoicedetil['UserNumber']['voice']==1){ ?>
                                    YES
								<?php }else{ ?>
								NO
								<?php } ?>
								</td>
						
								<td style="text-align:center;" class="actions">
								<?php if(API_TYPE!=2){ ?>
	                               <?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'user_number_release',$invoicedetil['UserNumber']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Release Number'), sprintf(__('Are you sure you want to release this number?', true))); ?>
				                <?php } ?>
				                </td>
							</tr>
						<!--</tbody>-->
						<?php } ?>
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
		