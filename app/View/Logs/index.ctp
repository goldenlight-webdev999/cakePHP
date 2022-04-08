<script type="text/javascript" charset="utf-8">
	function delete1(id){
	//alert(id);
	var a = confirm('Are you sure you want to delete?');
	if(a==true){
	//alert('incondition');
	window.location="<?php echo SITE_URL?>/logs/deleteall/"+id;
	}
	}
</script>
		<div class="page-content-wrapper">
			<div class="page-content">              
				<h3 class="page-title"> <?php echo('Logs');?></h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
						<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<span><?php echo('Logs');?> </span>
						</li>
					</ul>
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button data-close-others="true" data-delay="1000" data-hover="dropdown" data-toggle="dropdown" class="btn btn-fit-height grey-salt dropdown-toggle" type="button"> Actions
								<i class="fa fa-angle-down"></i>
							</button>
							<ul role="menu" class="dropdown-menu pull-right">
								<li>


									<?php
									if(API_TYPE==2 || API_TYPE==1){
									$navigation = array(
									'SMS Inbox' => '/logs/index/smsinbox',
									'Single SMS Outbox' => '/logs/index/singlesmsoutbox',
									'Group SMS Outbox' => '/logs/index/groupsmsoutbox'
									);	
									}else{
									    if(API_TYPE==0 || API_TYPE==5){
									        $navigation = array(
									        'SMS Inbox' => '/logs/index/smsinbox',
									        'Single SMS Outbox' => '/logs/index/singlesmsoutbox',
									        'Group SMS Outbox' => '/logs/index/groupsmsoutbox',
									        'Voicemail' => '/logs/index/voice',
									        'Voice Broadcast' => '/logs/index/broadcast',
                                            'Call Forward' => '/logs/index/callforward',
                                            'Fax Inbox' => '/logs/index/faxinbox',
                                            'Fax Outbox' => '/logs/index/faxoutbox'
									        );
									    }else if(API_TYPE==7){
									       $navigation = array(
									        'SMS Inbox' => '/logs/index/smsinbox',
									        'Single SMS Outbox' => '/logs/index/singlesmsoutbox',
									        'Group SMS Outbox' => '/logs/index/groupsmsoutbox',
									        'Voicemail' => '/logs/index/voice',
									        'Voice Broadcast' => '/logs/index/broadcast',
                                            'Call Forward' => '/logs/index/callforward',
                                            'Fax Outbox' => '/logs/index/faxoutbox'
									        );
									    }else{
									        $navigation = array(
									        'SMS Inbox' => '/logs/index/smsinbox',
									        'Single SMS Outbox' => '/logs/index/singlesmsoutbox',
									        'Group SMS Outbox' => '/logs/index/groupsmsoutbox',
									        'Voicemail' => '/logs/index/voice',
									        'Voice Broadcast' => '/logs/index/broadcast',
                                            'Call Forward' => '/logs/index/callforward'
									        );	
									    }
									}
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
									$out[] = '<li class="divider"> </li>';
									if ($type == 'groupsmsoutbox'){
									    $out[] = '<li><a href="#null" onclick="delete1(5)">Delete ALL Group SMS Outbox(Prior Years Only)</a></li>';
									    
									}else if ($type == 'voice'){
									    $out[] = '<li><a href="#null" onclick="delete1(4)">Delete ALL Voicemail</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/voice>Export ALL Voicemail</a></li>';
									    
									}else if ($type == 'callforward'){
									    $out[] = '<li><a href="#null" onclick="delete1(7)">Delete ALL Call Forward</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/callforward>Export ALL Call Forward</a></li>';
									    
									}else if ($type == 'broadcast'){
									    $out[] = '<li><a href="#null" onclick="delete1(6)">Delete ALL Voice Broadcast</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/broadcast>Export ALL Broadcast</a></li>';
								
									}else if ($type == 'smsinbox'){
									    $out[] = '<li><a href="#null" onclick="delete1(1)">Delete ALL SMS Inbox(Prior Years Only)</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/smsinbox>Export ALL SMS Inbox</a></li>';
									    
									}else if ($type == 'smsoutbox'){
									    $out[] = '<li><a href="#null" onclick="delete1(2)">Delete ALL SMS Outbox(Prior Years Only)</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/smsoutbox>Export ALL SMS Outbox</a></li>';
								
									}else if ($type == 'singlesmsoutbox'){
									    $out[] = '<li><a href="#null" onclick="delete1(3)">Delete ALL SMS Outbox(Prior Years Only)</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/singlesmsoutbox>Export ALL Single SMS Outbox</a></li>';
								
									}else if ($type == 'faxinbox'){
									    $out[] = '<li><a href="#null" onclick="delete1(8)">Delete ALL Fax Inbox</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/faxinbox>Export ALL Fax Inbox</a></li>';
									    
									}else if ($type == 'faxoutbox'){
									    $out[] = '<li><a href="#null" onclick="delete1(9)">Delete ALL Fax Outbox</a></li>';
									    $out[] = '<li><a href='.SITE_URL.'/logs/export/faxoutbox>Export ALL Fax Outbox</a></li>';
									}                       
									echo join("\n", $out);
									?>	
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
							<i class="fa fa-database font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Logs');?></span> </div>
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->
					</div>			
					<?php if($type == 'groupsmsoutbox'){?>					
					<div class="portlet-body">
<!--<a href="#null" onclick="delete1(5)" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered tabdatatable_groupsmsoutboxle-hover table-condensed">-->
							<table  id="datatable_groupsmsoutbox" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
							<thead>
								<tr>										
									<th>Group Name</th>
									<th>Group Count</th>
									<th>Sent on</th>
									<th class="actions"><?php echo('Actions');?></th>
									<!--<th>
											<?php 
								if($type=="groupsmsoutbox"){?>
								<a href="#null" onclick="delete1(5)" class="btn red"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>
								<?php	}?>
									</th>-->
								</tr>
								<?php
									$i = 0;
									foreach ($logs as $log):
									$class = null;
									if ($i++ % 2 == 0) {
									$class = ' class="altrow"';
									}
								?>
							</thead>
							<!--<tbody>-->
								<tr <?php echo $class;?>>
									<td><?php echo $log['Group']['group_name']; ?>&nbsp;</td>
									<td><?php echo $log['Group']['totalsubscriber']; ?>&nbsp;</td>
									<td><?php echo $log['GroupSmsBlast']['created']; ?>&nbsp;</td>
									<td class="actions">
							
										<!--<?php echo $this->Html->link(__('Sent Statistics', true), array('action' => 'sentstatistics', $log['GroupSmsBlast']['id']), array('class' => 'btn blue btn-outline btn-sm')); ?>-->

<?php echo $this->Html->link('<i class="icon-graph" style="font-size:14px"></i>',array('action'=>'sentstatistics',$log['GroupSmsBlast']['id']),array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'Sent Statistics','style'=>'margin-right:0px')); ?>
										<?php
										if(API_TYPE==0){
										
echo $this->Html->link('<i class="icon-reload" style="font-size:14px"></i>',array('action'=>'refreshstatistics',$log['GroupSmsBlast']['id']),array('class'=> 'btn blue btn-sm','escape'=> false,'title'=>'Refresh Statistics','style'=>'margin-right:0px')); 
										}?>		
										<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'grouplogdelete', $log['GroupSmsBlast']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this record?', true)));
										?>-->

<!--<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'grouplogdelete',$log['GroupSmsBlast']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>-->
										
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
					<!--h1>voice</h1-->
					<?php }else if($type == 'voice'){?>
					<div class="portlet-body">
<!--<a href="<?php echo SITE_URL;?>/logs/export/voice" class="btn green" style="float:left"><i class="fa fa-file-excel-o"></i> Export Voicemail</a>
&nbsp;&nbsp;<a href="#null" onclick="delete1(4)" class="btn red" style="float:left"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>										
										<th>Read</th>
										<th>Phone Number</th>
										<th>Created</th>
										<th class="actions"><?php echo('Actions');?></th>
										<!--<th>
											<?php 
										if($type=="voice"){?>
										<a href="<?php echo SITE_URL;?>/logs/export" class="btn dark">Export Voicemail</a>
										<a href="#null" onclick="delete1(4)" class="btn default">Delete ALL</a>
										<?php 	}
										?>
										</th>-->
									</tr>
									
									<?php
										$i = 0;
										foreach ($logs as $log):
										$class = null;
										if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
										}
									?>
								</thead>
								<!--<tbody>-->
									<tr	<?php echo $class;?>>
										<td><?php echo $status = ($log['Log']['read']==0) ? 'New' : 'Heard'; ?>&nbsp;</td>		
										<td><?php echo $log['Log']['phone_number']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['created']; ?>&nbsp;</td>
										<td class="actions">

									

											<?php if(API_TYPE==0){?>
<!--<?php echo $this->Html->link(__('Read', true), array('action' => 'view', $log['Log']['id']), array('class' => 'btn blue btn-outline  btn-sm nyroModal')); ?>-->
<?php } ?>
											<?php if(strpos($log['Log']['voice_url'],"http") !== false && (API_TYPE==0 || API_TYPE==3 || API_TYPE==4 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7)){ ?>
											<!--<?php echo $this->Html->link(__('Listen', true),''.$log['Log']['voice_url'].'', array('class' => 'btn blue btn-outline btn-sm ')); ?>-->

<?php echo $this->Html->link('<i class="icon-volume-2" style="font-size:14px"></i>',''.$log['Log']['voice_url'].'',array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'Listen','style'=>'margin-right:0px')); ?>


											<?php } ?>
											<?php if(API_TYPE==1){?>
											<!--<?php echo $this->Html->link(__('Listen', true),''.$log['Log']['voice_url'].'', array('class' => 'btn blue btn-outline btn-sm')); ?>-->
<?php echo $this->Html->link('<i class="icon-volume-2" style="font-size:14px"></i>',''.$log['Log']['voice_url'].'',array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'Listen','style'=>'margin-right:0px')); ?>
											<?php } ?>
											<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $log['Log']['id'],$type), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this record?', true), $log['Log']['id']));
											?>-->

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$log['Log']['id'],$type),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>
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
					<!--h1>Broadcast</h1-->
					<?php	}else if($type == 'callforward'){ ?>
						<div class="portlet-body">
						<!--<a href="<?php echo SITE_URL;?>/logs/export/callforward" class="btn green" style="float:right"><i class="fa fa-file-excel-o"></i> Export Call Forward</a>
						&nbsp;&nbsp;<a href="#null" onclick="delete1(7)" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>
										<th>Read</th>
										<th>Phone Number</th>
										<th>Call Duration</th>
										<th>Created</th>
										<th class="actions"><?php echo('Actions');?></th>
									</tr>
									<?php
										$i = 0;
										foreach ($logs as $log):
										$class = null;
										if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
										}
									?>
								</thead>
								<!--<tbody>-->
									<tr <?php echo $class;?>>
										<td><?php echo $status = ($log['Log']['read']==0) ? 'New' : 'Heard'; ?>&nbsp;</td>		
										<td><?php echo $log['Log']['phone_number']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['call_duration']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['created']; ?>&nbsp;</td>
										<td class="actions">

											
											<?php if(strpos($log['Log']['voice_url'],"http") !== false) { ?>
											<!--<?php echo $this->Html->link(__('Listen', true),''.$log['Log']['voice_url'].'', array('class' => 'btn blue btn-outline btn-sm')); ?>-->
<?php echo $this->Html->link('<i class="icon-volume-2" style="font-size:14px"></i>',''.$log['Log']['voice_url'].'',array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'Listen','style'=>'margin-right:0px')); ?>
											<?php } ?>
											<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $log['Log']['id'],$type), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this record?', true), $log['Log']['id']));
											?>-->

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$log['Log']['id'],$type),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>
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
					<?php	}else if($type == 'faxinbox' || $type == 'faxoutbox'){ ?>
						<div class="portlet-body">
				
							<table  id="datatable_fax" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>
									    <th>Fax Date</th>
									    <th>Number</th>
										<th>Pages</th>
										<?php if(API_TYPE!=7){?>
										    <th>Fax Duration</th>
										<? }?>
										<?php if($type == 'faxoutbox') { ?>
										<th>Status</th>
										<?php } ?>
										<th class="actions"><?php echo('Actions');?></th>
									</tr>
									<?php
										$i = 0;
										foreach ($logs as $log):
										$class = null;
										if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
										}
									?>
								</thead>
								<!--<tbody>-->
									<tr <?php echo $class;?>>
									    <td><?php echo $log['Log']['created']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['phone_number']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['inbox_type']; ?>&nbsp;</td>
										<?php if(API_TYPE!=7){?>
										<td><?php echo $log['Log']['call_duration']; ?>&nbsp;</td>
										<? }?>
										<?php if($type == 'faxoutbox') { ?>
										<td><?php echo $log['Log']['sms_status']; ?>&nbsp;
										<?php if ($log['Log']['sms_status']=='failed'){?>
										<?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'errormessage', $log['Log']['id']), array('escape' =>false, 'class' => 'nyroModal')); ?>
										</td>
										<?php }} ?>
										<td class="actions">

											
											<?php if(strpos($log['Log']['voice_url'],"http") !== false) { ?>
											<?php //echo $this->Html->link('<i class="fa fa-fax" style="font-size:14px"></i>',''.$log['Log']['voice_url'].'',array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'View','style'=>'margin-right:0px')); ?>
											<?php echo $this->Html->link('<i class="fa fa-fax" style="font-size:14px"></i>',array('action'=>'viewfax',$log['Log']['id'],$type),array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'View Fax','style'=>'margin-right:0px')); ?>
											<?php } ?>
										
<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$log['Log']['id'],$type),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>
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
					<?php	}else if($type == 'broadcast'){ ?>
					
					<div class="portlet-body">
					<!--<a href="<?php echo SITE_URL;?>/logs/export/broadcast" class="btn green" style="float:right"><i class="fa fa-file-excel-o"></i>Export Broadcast</a>
					&nbsp;&nbsp;<a href="#null" onclick="delete1(6)" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<!--<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>
										<th>Read</th>
										<th>Phone Number</th>
										<th>Status</th>
										<th>Created</th>
										<th class="actions"><?php echo('Actions');?></th>
										<!--<th>
										<?php 
											if($type=="broadcast"){?>
											<a href="<?php echo SITE_URL;?>/logs/export" class="btn dark">Export BroadCast</a>
											<a href="#null" onclick="delete1(6)" class="btn default">Delete ALL</a>
											<?php 	}
											?>
										
										</th>-->
										
									</tr>
									<?php
										$i = 0;
										foreach ($logs as $log):
										$class = null;
										if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
										}
									?>
								</thead>
								<!--<tbody>-->
									<tr <?php echo $class;?>>
										<td><?php echo $status = ($log['Log']['read']==0) ? 'New' : 'Heard'; ?>&nbsp;</td>		
										<td><?php echo $log['Log']['phone_number']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['sms_status']; ?>&nbsp;</td>
										<td><?php echo $log['Log']['created']; ?>&nbsp;</td>
										<td class="actions">

											
											
                                                                                        <?php if(strpos($log['Log']['voice_url'],"http") !== false) { ?>
											<!--<?php echo $this->Html->link(__('Listen', true),''.$log['Log']['voice_url'].'', array('class' => 'btn blue btn-outline btn-sm')); ?>-->

<?php echo $this->Html->link('<i class="icon-volume-2" style="font-size:14px"></i>',''.$log['Log']['voice_url'].'',array('class'=> 'btn green-jungle btn-sm','escape'=> false,'title'=>'Listen','style'=>'margin-right:0px')); ?>
											<?php } ?>
											<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $log['Log']['id'],$type), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this record?', true), $log['Log']['id']));
											?>-->

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$log['Log']['id'],$type),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>
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
					<?php }else{ ?>							
					<div class="portlet-body">
						<?php if($type=="smsinbox"){?>	
						<!--<a href="<?php echo SITE_URL;?>/logs/export/smsinbox" class="btn green" style="float:right"><i class="fa fa-file-excel-o"></i> Export SMS Inbox</a>
						&nbsp;&nbsp;<a href="#null" onclick="delete1(1)" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<?php }else if($type=="smsoutbox"){ ?>
						<!--<a href="<?php echo SITE_URL;?>/logs/export/smsoutbox" class="btn green" style="float:right"><i class="fa fa-file-excel-o"></i>Export SMS Outbox</a>
						&nbsp;&nbsp;<a href="#null" onclick="delete1(2)" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<?php }else if($type=="singlesmsoutbox"){ ?>
						<!--<a href="<?php echo SITE_URL;?>/logs/export/singlesmsoutbox" class="btn green" style="float:right"><i class="fa fa-file-excel-o"></i> Export Single SMS Outbox</a>
						&nbsp;&nbsp;<a href="#null" onclick="delete1(3)" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>-->
						<?php }?>

						<!--<div class="table-scrollable">							
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>
										<?php if($type=="smsinbox"){?>			
										<th>Read</th>
										<th>From Number</th>
										<th>To Number</th>
                                        <th>Name</th>
                                        <th>Message</th>
										<th>Created</th>
										<th>Status</th>
										<th class="actions"><?php echo('Actions');?></th>
										
										<!--<th><a href="<?php echo SITE_URL;?>/logs/export" class="btn dark">Export SMS Inbox</a>
										<a href="#null" onclick="delete1(1)" class="btn red">Delete ALL <i class="fa fa-trash-o"></i></a>
										</th>-->
									   
										<?php }else if($type=="smsoutbox"){ ?>
										<th>Read</th>
										<th>Phone Number</th>
										<th>Created</th>
										<th>&nbsp;</th> 
										<th>Status</th>
										<th class="actions"><?php echo('Actions');?></th>
									<!--<th>
											<a href="<?php echo SITE_URL;?>/logs/export" class="btn yellow" >Export SMS Outbox <i class="fa fa-file-excel-o"></i></a>
											<a href="#null" onclick="delete1(2)" class="btn red"><i class="fa fa-trash-o"></i> Delete ALL (Prior Years Only)</a>
										</th>-->
										<?php }else if($type=="singlesmsoutbox"){ ?>
											<th>Read</th>
											<th>Phone Number</th>
											<th>Created</th>
											<th>&nbsp;</th> 
											<th>Status</th>
											<th class="actions"><?php echo('Actions');?></th>											
<!--											<th>
											<a href="<?php echo SITE_URL;?>/logs/export" class="btn dark">Export Single SMS Outbox</a>
											<a href="#null" onclick="delete1(3)" class="btn default">Delete ALL</a>
										</th>-->
										  <?php }?>
										
									</tr>
								</thead>
								<?php
								$i = 0;
								foreach ($logs as $log):	
								$class = null;
								if ($i++ % 2 == 0) {
								$class = ' class="altrow"';
								}
								?>
								<!--<tbody>-->
									<tr <?php echo $class;?>>
										<td><?php echo $status = ($log['Log']['read']==0) ? 'New' : 'Read'; ?>&nbsp;</td>		
										<td><?php echo $log['Log']['phone_number']; ?>&nbsp;</td>
                                                                                
										<?php if($type=="smsinbox"){ ?>
										      <td><?php echo $log['Log']['email_to_sms_number']; ?>&nbsp;</td>
											  <td><?php echo $log['Log']['name']; ?>&nbsp;</td>
                                              <td><?php echo $log['Log']['text_message']; ?>&nbsp;</td>
										<?}?>
                                                                                
										<td><?php echo $log['Log']['created']; ?>&nbsp;</td>
										<?php if($log['Log']['route']=='outbox'){?>
										<?php if ($log['Log']['sms_status']=='undelivered' || $log['Log']['sms_status']=='failed' || $log['Log']['sms_status']=='not-delivered' || trim($log['Log']['error_message']) !=''){?>
										<td><?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'errormessage', $log['Log']['id']), array('escape' =>false, 'class' => 'nyroModal')); ?></td>
										<td><?php echo $log['Log']['sms_status']; ?>&nbsp;</td>
										<?php }else{ ?>
										<td>&nbsp;</td>
										<td><?php echo $log['Log']['sms_status']; ?>&nbsp;</td>
										<?php } ?>
										<?php }else{ ?>
										<td><?php echo $log['Log']['sms_status']; ?>&nbsp;</td>
										<?php }?>
										<td class="actions">
										
											<!--<?php echo $this->Html->link(__('Read', true), array('action' => 'view', $log['Log']['id']), array('class' => 'btn blue btn-outline  btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-eye" style="font-size:14px"></i>',array('action'=>'view',$log['Log']['id']),array('class'=> 'btn green btn-sm nyroModal','escape'=> false,'title'=>'Read','style'=>'margin-right:0px')); ?>
											<?php //echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'send_sms', $log['Log']['phone_number']), array('class' => 'nyroModal')); ?>
											<?php if($userperm['sendsms']=='1'){ ?>
											<?php if(API_TYPE==0){?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline  btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

											<?php }else if(API_TYPE==4){?>
											<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'bandwidth_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
											<?php }else if(API_TYPE==5){?>
											<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'signalwire_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
											<?php }else if(API_TYPE==6){?>
											<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'ytel_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
											<?php }else if(API_TYPE==7){?>
											<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'telnyx_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
											<?php }else if(API_TYPE==3){?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'plivo_send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline  btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'plivo_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

											<?php }else if(API_TYPE==2){?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'slooce_send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline  btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'slooce_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

											<?php }else{?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'nexmo_send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline  btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'nexmo_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

											<?php }} ?>
											<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $log['Log']['id'],$type), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this record?', true), $log['Log']['id']));
											?>-->

<!--<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$log['Log']['id'],$type),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>-->
										</td>
									</tr>
								<!--</tbody>-->
										
                                    <?php endforeach; ?>									
								</tbody>
							</table>
							

							<?php }?>
						<!--</div>-->
						<?php if($type=="smsinbox" || $type=="singlesmsoutbox" ){ ?>	
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
							<?php } ?>

					</div>
				</div>
			</div>	
		</div>		
                           