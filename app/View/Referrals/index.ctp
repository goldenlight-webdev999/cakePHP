<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Referrals
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
					<span>Referrals</span>
				</li>
			</ul>                
		</div>
		<div class="clearfix"></div>
		<?php echo $this->Session->flash(); ?>
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-sitemap font-red-sunglo"></i>
					<span class="caption-subject font-red-sunglo bold uppercase"> Referrals</span>
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
								<!--<th><?php echo $this->Paginator->sort('id');?></th>
								<th><?php echo $this->Paginator->sort('Name', 'User.first_name');?></th>
								<th><?php echo $this->Paginator->sort('amount');?></th>
								<th><?php echo $this->Paginator->sort('paid_status');?></th>
								<th><?php echo $this->Paginator->sort('type');?></th>
								<th><?php echo $this->Paginator->sort('created');?></th>-->
								
								<th>Name</th>
								<th>Amount</th>
								<th>Paid Status</th>
								<th>Type</th>
								<th>Created</th>
							</tr>
						</thead>
						<!--<tbody>-->
							<?php
							$i = 0;
							foreach ($referrals as $referral):
								$class = null;
								if ($i++ % 2 == 0) {
									$class = ' class="altrow"';
								}
							?>
							<tr <?php echo $class;?>>
								<!--<td><?php echo $referral['Referral']['id']; ?>&nbsp;</td>-->
								<td><?php echo $referral['User']['first_name'].' '.$referral['User']['last_name'] ?></td>
								<td><?php echo '$'.$referral['Referral']['amount']; ?>&nbsp;</td>
								<td><?php echo $referral['Referral']['paid_status'] == 1?'Piad' :'Not Paid'; ?>&nbsp;</td>
								<td><?php echo $referral['Referral']['type'] == 0?'Referral' :'Recurring Referral'; ?>&nbsp;</td>
								<td><?php echo $referral['Referral']['created']; ?>&nbsp;</td>
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