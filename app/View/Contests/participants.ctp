<div class="portlet box blue-dark">
		<div class="portlet-title">
			<div class="caption">
                                  Contest Participants
			</div>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>Number</th>
								</tr>
								<?php
									$i = 0;
									foreach ($participant as $participant):
									$class = null;
									if ($i++ % 2 == 0) {
									$class = ' class="altrow"';
									}
								?>
							</thead>
							<!--<tbody>-->
								<tr <?php echo $class;?>>
								<td><?php echo ucfirst($participant['ContestSubscriber']['phone_number']);?></td>
								</tr>
								<?php endforeach; ?>
							<!--</tbody>-->
						</table>
						
					</div>
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
	</div>