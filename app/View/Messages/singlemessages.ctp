<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>-->
<script type="text/javascript">
 $(function() {
	$("table tr:nth-child(odd)").addClass("odd-row");
	$("table td:first-child, table th:first-child").addClass("first");
	$("table td:last-child, table th:last-child").addClass("last");
});
</script>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo('Single Scheduled Messages');?>
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
					<span><?php echo('Single Scheduled Messages');?></span>
				</li>
			</ul>  
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
						<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
					<?php
						$navigation = array(
							'Send Message' => '/messages/send_message',
							'View Group Scheduled Messages' => '/messages/schedule_message',
							'View Contacts Scheduled Messages' => '/messages/singlemessages',
																				   
						);				
						$matchingLinks = array();
						foreach ($navigation as $link) {
							if (preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base)))) {
									$matchingLinks[strlen($link)] = $link;
							}
						}
						krsort($matchingLinks);
						$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
						$out = array();
						foreach ($navigation as $title => $link) {
							$out[] = '<li>'.$this->Html->link($title, $link, ife($link == $activeLink, array('class' => 'current'))).'</li>';
						}
						echo join("\n", $out);
					?>	
					</ul>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php echo $this->Session->flash(); ?>
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-clock-o font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Single Scheduled Messages');?></span> 
				</div>
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->
			</div>
			<div class="portlet-body">
							<!--<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td>
								<b>NOTE:</b> There may be small delays (1-15 minutes) depending on how often the jobs on server are set to run and when the job was last executed. The max delay there will ever be is 15 minutes.
							</td>
						</tr>
					</tbody>
				</table>-->
<!--<div class="note note-warning"><b>NOTE:</b> There may be small delays (1-15 minutes) depending on how often the jobs on server are set to run and when the job was last executed. The max delay there will ever be is 15 minutes.</div>-->
					<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>

								<th>Send On</th>
                                <th>Send From</th>
								<th>Send To</th>
								<th>Message</th>
								
								<?php if((!empty($users)) || !empty($numbers_mms)){ ?>
								<th>Type</th>
								<th>Media</th>		 
								<?php } ?>
								<th>Action</th>
							</tr>
						</thead>
						<?php 
							$i = 0;
							foreach($ScheduleMessage as $ScheduleMessages) { 
							$class = null;
							if ($i++ % 2 == 0) {
								$class = ' class="altrow"';
							}
							if($ScheduleMessages['ScheduleMessage']['msg_type']==1){
							$message=$ScheduleMessages['ScheduleMessage']['message'];
							$msg_type='SMS';
							}else if($ScheduleMessages['ScheduleMessage']['msg_type']==2){
							$message=$ScheduleMessages['ScheduleMessage']['mms_text'];
							$image_url=$ScheduleMessages['ScheduleMessage']['message'];
							$msg_type='MMS';
							}
						?>
						<!--<tbody>-->
							<tr <?php echo $class;?>> 
                                                                <td><?php echo $ScheduleMessages['ScheduleMessage']['send_on'] ?></td>
                                                                <td><?php echo $ScheduleMessages['ScheduleMessage']['sendfrom'] ?></td>
								<td><?php echo $ScheduleMessages['Contact']['phone_number'] ?></td>
								<td style="word-break: break-all;"><?php echo $message ?></td>
								
								<?php 			
								if((!empty($users)) || !empty($numbers_mms)){ 
								?>
								<td ><?php echo $msg_type; ?></td>
								<td style="word-break: break-all;">
								<?php 
								if($ScheduleMessages['ScheduleMessage']['msg_type']==2){
									if($image_url!=''){
										$check=strpos($image_url,":");
										if($check!=''){
											$comma=strpos($image_url,",");
											if($comma!=''){
												$image_arr=explode(",",$image_url);
												foreach($image_arr as $value){	
												?>
												<img src="<?php echo $value; ?>" height="80px" width="65px" />
												<?php
												}
											}else{
											?>
											<img src="<?php echo $image_url ?>" height="80px" width="65px" />
											<?php
											}
										}
									}else{
										?>
										<img src="<?php echo $ScheduleMessages['ScheduleMessage']['pick_file'] ?>" height="80px" width="70px" />
										<?php
									}
								}
								?>
								</td>
								<?php } ?>
								<!--<td><?php echo $ScheduleMessages['ScheduleMessage']['created'] ?></td>-->
								<td class="actions" style="padding: 2px; width:215px; vertical-align: middle">
								<!--<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit_singlemessage', $ScheduleMessages['ScheduleMessage']['id']), array('class' => 'btn green btn-outline btn-sm')); ?>
								<?php echo $this->Html->link(__('Delete', true), array('action' => 'singlesmsdelete', $ScheduleMessages['SingleScheduleMessage']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete?', true))); ?>
								<?php echo $this->Html->link(__('Copy WI', true), array('action' => 'copycontactschedule', $ScheduleMessages['ScheduleMessage']['id'], 1), array('class' => 'btn blue btn-outline btn-sm')); ?>
								<?php echo $this->Html->link(__('Copy MI', true), array('action' => 'copycontactschedule', $ScheduleMessages['ScheduleMessage']['id'], 0), array('class' => 'btn blue btn-outline btn-sm')); ?>-->


<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit_singlemessage',$ScheduleMessages['ScheduleMessage']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'singlesmsdelete',$ScheduleMessages['SingleScheduleMessage']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete','style'=>'margin-right:0px'), sprintf(__('Are you sure you want to delete this scheduled message?', true))); ?>

<?php echo $this->Html->link('<i class="icon-docs" style="font-size:14px"></i>',array('action'=>'copycontactschedule',$ScheduleMessages['ScheduleMessage']['id'], 1),array('class'=> 'btn yellow-gold btn-sm','escape'=> false,'title'=>'Copy Weekly Increment','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-docs" style="font-size:14px"></i>',array('action'=>'copycontactschedule',$ScheduleMessages['ScheduleMessage']['id'], 0),array('class'=> 'btn purple-soft btn-sm','escape'=> false,'title'=>'Copy Monthly Increment','style'=>'margin-right:0px')); ?>

								</td>
							</tr>
							<?php } ?>
						<!--</tbody>-->
					</table>
					<?php 
					if ($i > 0){?>
					<table class="table table-bordered ">
						<tbody>
							<tr>
								<td>
									<b>Copy WI(Weekly Increment)</b> - If you want to create a recurring weekly scheduled SMS, "Copy WI" will create an exact duplicate of that scheduled SMS, but with a send on date 1 week later. 
									<br/><br/>
									<b>Copy MI(Monthly Increment)</b> - If you want to create a recurring monthly scheduled SMS, "Copy MI" will create an exact duplicate of that scheduled SMS, but with a send on date 1 month later. 
									<!--<br/><br/>
									This is very fast and easy way to create recurring SMS, rather than manually creating every scheduled SMS from the Bulk SMS page. Of course, you can edit the details of each scheduled SMS as well.
									-->
								</td>
							</tr>
						</tbody>		
					</table>
					<?php }?>
					
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