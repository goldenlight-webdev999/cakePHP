<div class="portlet box blue-dark">
	<div class="portlet-title">
		<div class="caption">
			Do Not Call List
		</div>
	</div>
	<div class="portlet-body">
			<!-- <div style="text-align:right;float:right; padding-bottom: 10px">
			<a class="nyroModal" href="<?php //echo SITE_URL;?>/groups/addcontact" title="Add New Contact"><img src="<?php //echo SITE_URL;?>/img/add_contact.png"></a>
			</div> -->
			<div style="font-size:18px; color:#186c8f; padding-bottom: 10px;"><b>Group Name: <font style="color: #83c03a"><?php echo ucfirst($group_name['Group']['group_name']); ?></b></font></div>
			<div style="font-size:18px; color:#186c8f; padding-bottom: 10px;"><b>Total Members: <font style="color: #83c03a"><?php echo $count_user; ?></b></font></div>
		<div class="table-responsive">		
			<table  class="table table-striped table-bordered table-hover">
				<tr>
						<th>Name</th>
						<th>Number</th>
						<!-- <th class="actions"><?php echo('Actions');?></th> -->
				</tr>
					<?php 
					$i = 0;
					foreach ($calls as $group):
					$class = null;
					if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
					}
					?>
				<tr <?php echo $class;?>>
					<td><?php echo $group['Contact']['name']; ?></td>
					<td><?php echo $group['Contact']['phone_number']; ?></td>
					
				</tr>
					<?php endforeach; ?>
			</table>
			<!--<div class="dataTables_paginate paging_bootstrap_number" id="sample_2_paginate">
				<ul class="pagination" style="visibility: visible;">
					<ul class="pagination">
					<li class="paginate_button previous disabled" aria-controls="dynamic-table" tabindex="0" id="dynamic-table_previous"><?php
					echo $this->Paginator->prev('<', array(), null, array('class' => 'prev disabled'));?></li>
					<li class="paginate_button next" aria-controls="dynamic-table" tabindex="0" id="dynamic-table_next"><?php echo $this->Paginator->next('>', array(), null, array('class' => 'next disabled'));?></li>
					</ul>
				</ul>
			</div>-->
			
			<div class="pagination pagination-large">
                <ul class="pagination">
                    <?php
                        //echo '<li>'.$this->Paginator->first('<< First', array('tag' => false,'class'=>'nyroModal'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a')).'</li>';
                        echo '<li>'.$this->Paginator->prev(__('<'), array('tag' => false,'class'=>'nyroModal'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a')).'</li>';
                        //echo '<li>'.$this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => false,'first' => 1,'class'=>'nyroModal')).'</li>';
                        echo '<li>'.$this->Paginator->next('>', array('tag' => false,'class'=>'nyroModal'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a')).'</li>';
                        //echo '<li>'.$this->Paginator->last('Last >>', array('tag' => false,'class'=>'nyroModal'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a')).'</li>';
                    ?>
                </ul>
            </div>
                    
		</div>
	</div>
</div>

<!-- login box-->