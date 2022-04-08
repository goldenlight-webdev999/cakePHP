	
	<style>
	.text-muted a.btn {
	float:left}
	
	</style>
	<div class="page-content-wrapper">
		<div class="page-content">              
			<h3 class="page-title"><?php echo('Sent Statistics');?></h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<span><?php echo('Sent Statistics');?></span>
					</li>
				</ul>  
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
							<i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li>
								<?php
								$navigation = array(
								'Back' => '/logs/index/groupsmsoutbox',
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
								if(API_TYPE==0 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7 || (API_TYPE == 4 && empty($groupContacts['Log'][0]['image_url']))){ 
								//if(API_TYPE==0 || API_TYPE == 4){ 
								    $out[] = '<li><a href='.SITE_URL.'/logs/refreshstatistics/'.$groupContacts['GroupSmsBlast']['id'].'>Refresh Statistics</a></li>';
								}
								$out[] = '<li class="divider"> </li>';
								$out[] = '<li><a href='.SITE_URL.'/logs/export/groupsmsoutbox/'.$groupContacts['GroupSmsBlast']['id'].'>Export ALL Sent Statistics</a></li>';
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
					<div class="caption font-red-sunglo">
						<i class="fa fa-bar-chart font-red"></i>
						<span class="caption-subject font-red sbold uppercase"> <?php echo('Sent Statistics');?></span>
					</div>
					<!--<div class="actions">
						<div class="btn-group">
							<a class="btn btn-sm green dropdown-toggle" href="javascript:;" data-toggle="dropdown"> Actions
								<i class="fa fa-angle-down"></i>
							</a>
							<ul class="dropdown-menu pull-right">
								<li>
									<?php
									if(API_TYPE==0 || API_TYPE==4){ 
									echo $this->Html->link(__('Refresh Statistics', true), array('action' => 'refreshstatistics', $groupContacts['GroupSmsBlast']['id']), array('class' => 'btn btn-success '));?>
									<?php } ?>	
								</li>
								
							</ul>
						</div>
					</div>-->
<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<?php $percentage=$groupContacts['GroupSmsBlast']['total_successful_messages']/$groupContacts['GroupSmsBlast']['totals']*100; ?>
							<!--<table class="table table-bordered table-striped" id="user" >
								<tbody>-->
									<?php
									if(API_TYPE==0 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7 || (API_TYPE==4 && empty($groupContacts['Log'][0]['image_url']))){?>
						
									<!--<tr>												
										<td style="width:100%">
											<span class="text-muted"> 
												<b>NOTE:</b> Delivery stats are updated on a live basis. We update the counts and statuses as soon as we get receipt from the gateway, however there are times we never receive the status update after sending, so to keep all stats accurate, click the "Refresh Statistics" button below.
											</span>-->
<div class="note note-warning"><b>NOTE:</b> Delivery stats are updated on a live basis. We update the counts and statuses as soon as we get receipt from the gateway, however there are times we never receive the status update after sending, so to keep all stats accurate, click the "Refresh Statistics" link in the "Actions" menu.</div>
										<!--</td>
									</tr>-->
									<?php } ?>																
								<!--</tbody>
							</table>-->
							<table class="table table-bordered table-striped" id="user" >
								<tbody>
									<tr>												
										<td style="width:15%"><b>Group</b></td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo $groupContacts['Group']['group_name'];?>
											</span>
										</td>
									</tr> 
									<tr>												
										<td style="width:15%"><b>Sent On</b></td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo $groupContacts['GroupSmsBlast']['created']; ?>
											</span>
										</td>
									</tr> 
									<tr>												
										<td style="width:15%"><b>Recipients</b></td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo $groupContacts['GroupSmsBlast']['totals']; ?>
											</span>
										</td>
									</tr> 
									<tr>												
										<td style="width:15%"><b>Successful Messages</b><!--<?php if(API_TYPE==4) {?>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="For 'SMS' type messages, only 'delivered' messages count as successful. There are times we don't receive delivery receipt from the gateway. The status on these will be 'waiting'. In most cases these have been sent successfully as well, we just didn't receive the delivery receipt." data-original-title="Successful Messages" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a><?php } ?>-->
										</td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo $groupContacts['GroupSmsBlast']['total_successful_messages']; ?>
											</span>
										</td>
									</tr>								
									<tr>												
										<td style="width:15%"><b>Unsuccessful Messages</b><!--<?php if(API_TYPE==4) {?>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="For 'SMS' type messages, only 'not-delivered' and 'failed' messages count as unsuccessful. There are times we don't receive delivery receipt from the gateway. The status on these will be 'waiting'. In some cases these were not sent successfully and there will be an error message indicating why." data-original-title="Unsuccessful Messages" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a><?php } ?>-->
										</td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo $groupContacts['GroupSmsBlast']['total_failed_messages']; ?>
											</span>
										</td>	
									</tr>
									<tr>												
										<td style="width:15%"><b>% Successful</b></td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo ROUND($percentage,1).'%'; ?>
											</span>
										</td>
									</tr> 
									<tr>												
										<td style="width:15%"><b>Message</b></td>
										<td style="width:35%">
											<span class="text-muted"> 
										<?php echo $groupContacts['Log'][0]['text_message']; ?>
											</span>
										</td>
									</tr> 						
								</tbody>
							</table>
							<table class="table table-bordered table-striped" id="user" >
								
												<?php if($groupContacts['GroupSmsBlast']['isdeleted']==0 && $groupContacts['GroupSmsBlast']['total_failed_messages'] > 0){?> 
<tbody>
									<tr>												
										<td style="width:15%">
											<span class="text-muted"> 
												<?php echo $this->Html->link(__('Delete Contacts', true), array('action' => 'unsubscribe', $groupContacts['GroupSmsBlast']['id']), array('class' =>'btn btn-danger  btn-sm'), sprintf(__('Are you sure you want to delete failed contacts?', true)));
												?><a href="javascript:;" data-container="body" data-trigger="hover" data-content="Delete only these contacts from your list where the status is 'Failed'. Contacts whose messages were marked 'Undelivered' will NOT be deleted." data-original-title="Delete Failed Contacts" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
</span>
										</td>
									</tr>
												<?php } ?>
											
									<?php if(!empty($groupContacts['Log'][0]['image_url'])){?>
									<tr>
										<th style="text-align: left;width: 15%; vertical-align:top">Images:</th>
										<td style="width:35%">
											
										
										<div class="col-md-9">
                                                    <div data-provides="fileinput" class="fileinput fileinput-new">
                                                       
														
														<?php 
											$check=strpos($groupContacts['Log'][0]['image_url'],":");
											if($check!=''){
												$comma=strpos($groupContacts['Log'][0]['image_url'],",");
												if($comma!=''){
												$image_arr=explode(",",$groupContacts['Log'][0]['image_url']);
												foreach($image_arr as $value){	
												?>
												<img src="<?php echo $value; ?>" height="100px" width="100px" />
												<?php
												}
												}else{
												?>
												<img src="<?php echo $groupContacts['Log'][0]['image_url'] ?>" height="100px" width="100px" />
												<?php
												}
											}		
											?>
												
											</div>
											</div>
												</td>
									</tr>
									<?php } ?>
									<!--<tr>
									<td></td>
									</tr>-->
									
										<?php if($groupContacts['GroupSmsBlast']['isdeleted']==1){
										//$message='Failed contacts have already been deleted';
										?>
<tr>
										<td colspan=2><div style="width:auto;font-weight: bold;">Failed contacts for this bulk SMS delivery have been deleted</div></td>
</tr>
										<?php } ?>
					
								</tbody>
							</table>
						</div>
					</div>					
				</div>
				<div class="portlet light bordered">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-list font-red"></i><span class="caption-subject font-red sbold uppercase"><?php __('Group Sent Records');?></span></div>
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->
					</div>
					<div class="portlet-body">

								
									<!--<?php
									if(API_TYPE==0){ 
									echo $this->Html->link(__('Refresh Statistics', true), array('action' => 'refreshstatistics', $groupContacts['GroupSmsBlast']['id']), array('class' => 'btn btn-success', 'style' => 'float:right'));?>
									<?php } ?>-->	
								
								
							
						<!--<div class="table-scrollable">				
							<table class="table table-striped table-bordered table-hover table-condensed">-->
							<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
								<thead>
									<tr>
									    <th scope="col">Sent On</th>
									    <th scope="col">Sent From</th>
										<th scope="col">Sent To</th>
										<th scope="col">Status</th>
										<th scope="col">Actions</th>									
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
									    <td class="stattd"><?php echo $log['Log']['created']; ?>&nbsp;</td>
									    <td class="stattd"><?php echo $log['Log']['sendfrom']; ?>&nbsp;</td>
										<td class="stattd"><?php echo $log['Log']['phone_number']; ?>&nbsp;</td>
										<?php if ($log['Log']['sms_status']=='undelivered' || $log['Log']['sms_status']=='failed' || $log['Log']['sms_status']=='not-delivered' || trim($log['Log']['error_message']) !=''){?>
										<td class="stattd"><?php echo $log['Log']['sms_status']; ?>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('action' => 'errormessage', $log['Log']['id']), array('escape' =>false, 'class' => 'nyroModal')); ?></td>
										<?php }else{?>
										<td class="stattd"><?php echo $log['Log']['sms_status']; ?>&nbsp;</td>
										<?php }?>
										<td class="actions" style="border-right:1px solid #e0e0e0; border-top:1px solid #e0e0e0">
											<?php if($userperm['sendsms']=='1'){ ?>
											<?php if(API_TYPE==0){?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
<?php }else if(API_TYPE==4){?>
									<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'bandwidth_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
									<?php }else if(API_TYPE==5){?>
									<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'signalwire_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>	
									<?php }else if(API_TYPE==6){?>
									<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'ytel_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
									<?php }else if(API_TYPE==7){?>
									<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'telnyx_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>	
											<?php }else if(API_TYPE==1){?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'nexmo_send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'nexmo_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
											<?php }else if(API_TYPE==2){?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'slooce_send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'slooce_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

											<?php }else if(API_TYPE==3){ ?>
											<!--<?php echo $this->Html->link(__('Send SMS', true), array('controller' => 'contacts','action' => 'plivo_send_sms', $log['Log']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('controller' => 'contacts','action' => 'plivo_send_sms', $log['Log']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
											<?php }} ?>
											<!--<?php echo $this->Html->link(__('Delete Log', true), array('action' => 'delete', $log['Log']['id'],$type), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this record?', true), $log['Log']['id'])); ?>-->

<!--<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$log['Log']['id'],$type),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this record?', true))); ?>-->
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
	</div>