<style>
audio{
position:relative;
top:calc(50% - 30px);    
}
</style>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" type="text/css"  href="<?php echo SITE_URL; ?>/fancybox/jquery.fancybox.css">

<script>
$(document).ready(function() {	
    
	$.fancybox.update();
	$.fancybox.reposition();
	$(".fancybox").fancybox({
		closeClick  : false, // prevents closing when clicking INSIDE fancybox
		helpers     : { 
		overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
		}
	});
	$(function() {	
		$("#requests").fancybox();
	});
	setTimeout(function(){
		$('#flashMessage').hide();
	},3000);
	
});
</script>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Voice Broadcasts</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li><span>Voice Broadcasts </span></li>
				</ul>  
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
							<i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li>
								 <a href="<?php echo SITE_URL;?>/groups/voicebroadcast" title="Add Voice Broadcast"><i class="fa fa-plus-square-o"></i> Add Voice Broadcast </a>
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
						<i class="fa fa-bullhorn font-red"></i><span class="caption-subject font-red sbold uppercase">Voice Broadcast</span>
					</div>
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
									<th scope="col">Group Name</th> 
									<th scope="col">Message Type</th>
									<th scope="col">Message</th>
									<th class="actions"><?php echo('Actions');?></th>										
								</tr>
								<?php
									$i = 0;
									foreach ($vioce_broad as $group):
									$class = null;
									if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
									}
									if($group['VoiceMessage']['message_type']==0){
										$msg_type='Text-to-Voice';
									}else{
										$msg_type='MP3 Audio';
									}
								?>
							</thead>
								<!--<tbody>-->
									<tr>
										<td><?php echo $group['Group']['group_name']; ?></td> 
										<td><?php echo $msg_type; ?></td>
										<?php if($group['VoiceMessage']['message_type']==1){?>	
						                   <td>
							                <audio class="audio" controls="controls">
								            <source src="<?php echo SITE_URL ?>/voice/<?php echo $group['VoiceMessage']['audio']; ?>" type="audio/mpeg">
							                </audio>
						                   </td>
						                <? }else { ?>  
						                    <td><?php echo $group['VoiceMessage']['text_message']; ?></td> 
						                <? } ?>
										<td> 
											<?php if(API_TYPE==0 || API_TYPE==3 || API_TYPE==4 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7){?>
											<!--<?php echo $this->Html->link(__('Do Not Call List', true), array('action' => 'do_not_call', $group['VoiceMessage']['group_id']),array('class' => ' btn blue btn-outline btn-sm nyroModal')); ?>	-->

<?php echo $this->Html->link('<i class="icon-user-unfollow" style="font-size:14px"></i>',array('action'=>'do_not_call',$group['VoiceMessage']['group_id']),array('class'=> 'btn blue-ebonyclay btn-sm nyroModal','escape'=> false,'title'=>'Do Not Call List','style'=>'margin-right:0px')); ?>	
											<?php } ?>
											<!--<?php echo $this->Html->link(__('Voice Broadcast', true), array('action' => 'voicebroadcasting', $group['VoiceMessage']['id'],$group['Group']['id']),array('class' => 'btn blue btn-outline btn-sm nyroModal ')); ?>    -->

<!--<?php echo $this->Html->link('<i class="icon-call-out" style="font-size:14px"></i>',array('action'=>'voicebroadcasting',$group['VoiceMessage']['id'],$group['Group']['id']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Voice Broadcast','style'=>'margin-right:0px')); ?>	-->

<a id="pop_fancy" href="<?php echo SITE_URL; ?>/groups/voicebroadcasting/<?php echo $group['VoiceMessage']['id']?>/<?php echo $group['Group']['id']?>" title='Voice Broadcast' style="margin-right:0px" class="btn green-jungle btn-sm fancybox fancybox.iframe"><i class="icon-call-out" style="font-size:14px"></i></a>

<?php if($group['VoiceMessage']['message_type']==1 && API_TYPE==6){?>
<?php echo $this->Html->link('<i class="icon-volume-off" style="font-size:14px"></i>',array('action'=>'sendrvm',$group['VoiceMessage']['id'],$group['Group']['id']),array('class'=> 'btn purple-plum btn-sm nyroModal','escape'=> false,'title'=>'Send Ringless Voicemail','style'=>'margin-right:0px')); ?>	
<?php } ?>

											<!--<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit_broadcast', $group['VoiceMessage']['id']),array('class' => 'btn green btn-outline btn-sm')); ?>
											<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete_broadcast', $group['VoiceMessage']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this broad cast?', true))); ?>-->

<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit_broadcast',$group['VoiceMessage']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete_broadcast',$group['VoiceMessage']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this broadcast?', true))); ?>
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
