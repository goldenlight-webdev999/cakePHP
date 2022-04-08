<style>
.fancybox-outer, .fancybox-inner {
    position: relative;
    height: 500px!important;
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
<script>
 /* <![CDATA[ */
            jQuery(function(){
			 jQuery("#namephone").validate({
                    /*expression: "if (VAL) return true; else return false;",
                    message: "Please enter  name or phone number"*/
                });jQuery("#KeywordId").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Select Group"
               
                });
				
				jQuery("#lastName").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter value"
               
                });
                
                
				
            });
            /* ]]> */	
            
function clearfilters(){

/*document.forms[0].elements["namephone"].value='';
document.forms[0].elements["ContactPhone"].value=1;*/
document.forms[0].elements["GroupId"].value=0;
document.forms[0].elements["ContactSource"].value=4;

} 

function deletestickysenders(){
		
		var a = confirm('Are you sure you want to delete ALL sticky senders from ALL contacts? NOTE: When sending Bulk SMS and rotating through multiple numbers in your account, that number becomes stickied to a contact so that on all subsequent message blasts they receive the message from the same number. If you want to remove all the sticky senders from all contacts, you can do that here.');
		if(a==true){
		  window.location="<?php echo SITE_URL;?>/contacts/delete_stickysenders";
		}
}


</script>
<style>
.ValidationErrors{
color:red;
margin-bottom: 10px;
 float:right;
width:290px; 
 
}
</style>
	<div class="page-content-wrapper">
		<div class="page-content">              
			<h3 class="page-title"> Contacts</h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
								<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li><span>Contacts  </span></li>
					</ul> 
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
								<i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li>
									 <a  class="nyroModal" href="<?php echo SITE_URL;?>/contacts/add" title="Add Contact"><i class="fa fa-plus-square-o"></i> Add Contact </a>
								</li>	
								<li>
									 <a href="<?php echo SITE_URL;?>/contacts/export" title="Export Contacts"><i class="fa fa-file-excel-o"></i> Export ALL Contacts  </a>
								</li>
								<?php if($userperm['importcontacts']=='1'){ ?>
								<li>
									 <a href="<?php echo SITE_URL;?>/contacts/upload" title="Import Contacts"><i class="fa fa-upload"></i> Import Contacts  </a>
								</li>
								<?php } ?>
								<li>
									 <a onclick="deletestickysenders()" title="Delete Sticky Senders"><i class="fa fa-trash"></i> Delete Sticky Senders  </a>
								</li>	
						
							</ul>
						</div>
					</div>	                
				</div>
			<div class="">
				<div class="row">
					<!--<div class="dashboard-stat blue">
						<div class="visual">
							<i class="fa fa-user"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-counter="counterup" data-value="<?php echo $Subscribercount;?>"></span>
							</div>
							<div class="desc"> Total Subscribers </div>
						</div>
					</div>-->

<div class="tiles" style="margin-right:0px; width:100%;">
<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" >
<div class="tile selected bg-blue-hoki" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Subscribers </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $totsubscribercount;?>">                </span> </div>
                                    </div>
</div>
</div>

<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" >
<div class="tile selected bg-red-sunglo" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-user-times"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Un-Subscribers </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $unsubscribercount ;?>"></span> </div>
                                    </div>
</div>
</div>

<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
<div class="tile selected bg-green-turquoise" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-comment"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Subs by SMS </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $Subscribercountsms ;?>"></span> </div>
                                    </div>
</div>
</div>

<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" >
<div class="tile selected bg-yellow-saffron" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-file-text-o"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Subs by Widget </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $Subscribercountwidget ;?>"></span> </div>
                                    </div>
</div>
</div>

<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" >
<div class="tile selected bg-blue-madison" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-tablet"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Subs by Kiosk </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $Subscribercountkiosk ;?>"></span> </div>
                                    </div>
</div>
</div>

<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" >
<div class="tile selected bg-green" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-upload"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Imports </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $Subscribercountimport ;?>"></span> </div>
                                    </div>
</div>
</div>

<!--<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="width: 19.8%">
<div class="tile selected bg-purple-studio" style="width:100% !important">
                                    <div class="corner"> </div>
                                    <div class="tile-body">
                                        <i class="fa fa-upload"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name"> Imports </div>
                                        <div class="number" style="font-size:17px"> <span data-counter="counterup" data-value="<?php echo $Subscribercountimport;?>"></span> </div>
                                    </div>
</div>
</div>-->
</div>
                                    

				</div>
			</div>

			<div class="clearfix"></div>
			<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption font-blue">
						<i class="fa fa-search font-blue"></i>
						<span class="caption-subject bold uppercase"> Contact Search </span>
					</div>					
				</div>
				<div class="portlet-body">
                                           <div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
								   <i class="fa fa-search"></i> </div>
							     <div class="tools">
							         <a href="javascript:;" class="expand"></a>
							     </div>
							</div>
                                        <div class="portlet-body portlet-collapsed">
					<?php echo $this->Form->create('Contact',array('action'=> 'index'));?>
						<div class="form-body">
							
							<div class="form-group">
								<label>Select field you want to search on</label>
								<?php
									$Option=array('1'=>'Name','2'=>'Phone number','3'=>'City','4'=>'State/Province','5'=>'Postal Code');
									echo $this->Form->input('Contact.phone', array(
									'class'=>'form-control',
									'label'=>false,
									'default'=>1,
									'type'=>'select',
									'options' => $Option
									));
								?>
							</div>	
							
							<div class="form-group">
								<label for="exampleInputPassword1">Search on value</label>
								<div class="input-group">
								   <?php echo $this->Form->input('Contact.name', array('label'=>false,'div'=>false,'id'=>'namephone','class'=>'form-control')); ?>
									<span class="input-group-addon">
										<i class="fa fa-search font-red"></i>
									</span>
								</div>
                            </div>
                            
							<div class="form-actions">
								<?php echo $this->Form->submit('Search',array('div'=>false,'class'=>'btn blue'));?>
								<!--<input class="btn default" style="cursor: pointer;" type="button" class="inputbutton" onclick="clearfilters();" value="Clear Filters">-->
								
							</div>
							<div class="form-group">
								<label>Groups</label>
								<?php
									$Group[0]='';
									echo $this->Form->input('Group.id', array(
									'class'=>'form-control',
									'label'=>false,
									'default'=>0,
									'type'=>'select',
									'onchange'=>'confirmmessage(this.value);',
									'options' => $Group
									));
								?>
							</div>
							 <div class="form-group">
								<label>Source</label>
								<?php
									$Option1=array('4'=>'','0'=>'Import','1'=>'SMS','2'=>'Widget','3'=>'Kiosk');
									echo $this->Form->input('Contact.source', array(
									'class'=>'form-control',
									'label'=>false,
									'default'=>4,
									'type'=>'select',
									'options' => $Option1
									));
								?>
							</div>
						</div>
						<div class="form-actions">
							<?php echo $this->Form->submit('Filter',array('div'=>false,'class'=>'btn blue'));?>
							<input class="btn default" style="cursor: pointer;" type="button" class="inputbutton" onclick="clearfilters();" value="Clear Filters">

						</div>
					<?php echo $this->Form->end(); ?>
				</div>		
			</div></div></div>
			<?php  echo $this->Session->flash(); ?>			
			<div class="portlet light bordered">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user font-red"></i><span class="caption-subject font-red sbold uppercase">Contacts</span>  
					</div>
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>-->

				</div>
					
				<div class="portlet-body">
				<!--<a onclick="deletestickysenders()" class="btn red" style="float:right"><i class="fa fa-trash-o"></i> Delete ALL Sticky Senders</a>-->
					<!--<div class="table-scrollable">-->
					<?php if(empty($contacts)){?>
					<div style="font-weight: bold; font-size: 15px;text-align: center;">No contacts found. Please try again</div>
					<?php  }else{ ?>
					<!--<table class="table table-striped table-bordered table-hover table-condensed">-->
					<table  id="datatable_contactindex" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead><tr>
							
							<th>&nbsp;</th>
							<th>Name</th>
							<th>Email</th>
							<th>Birthday</th>
							<th>Number</th>
							<th>Carrier</th>
							<th>Fax Number</th>
							<th>Group</th>
							<th>Subscriber</th>
							<th>Source</th>
							<th>City</th>
							<th>State/Province</th>
							<th>Postal Code</th>
							<th>Custom 1</th>
							<th>Custom 2</th>
							<th>Custom 3</th>
							<th>Date</th>
							<th class="actions" >Action</th>
						</tr></thead>
							<?php 
								$i = 0;
								foreach ($contacts as $contact):
								$class = null;
								if ($i++ % 2 == 0) {
								$class = ' class="altrow"';
								}
							?>
						<tr <?php echo $class;?>>
							<td style="text-align: center">
							    <?php if($userperm['sendsms']=='1'){ ?>
								<?php if($contact['ContactGroup']['un_subscribers']==0){ ?>
								<a href="<?php echo SITE_URL;?>/messages/send_message/<?php echo $contact['Contact']['id']; ?>/contacts" data-container="body" data-trigger="hover" data-content="Schedule SMS for Contact" data-original-title="Schedule SMS" class="popovers"><i class="fa fa-calendar-plus-o" style="font-size:18px"></i></a>
								<?php }} ?>
							<td style="text-align: left;"><?php echo $contact['Contact']['name']; ?>&nbsp;</td>
							<td style="text-align: left;"><?php echo $contact['Contact']['email']; ?>&nbsp;</td>
							<td style="text-align: left;"><?php echo $contact['Contact']['birthday'] == '0000-00-00'?"":$contact['Contact']['birthday']; ?></td>
							<?php if($contact['ContactGroup']['un_subscribers']==0){ ?>
					        <td style="text-align: left;"><?php echo $contact['Contact']['phone_number']; ?>&nbsp;</td>	
					        <?php }else {?>
					        <td style="text-align: left;"><?php echo substr_replace($contact['Contact']['phone_number'], '****', -4); ?>&nbsp;</td>	
					        <?php } ?>
							<td style="text-align: left;"><?php echo $contact['Contact']['carrier']; ?>&nbsp;</td>
							<td style="text-align: left;"><?php echo $contact['Contact']['fax_number']; ?>&nbsp;</td>
							<td style="text-align: left;"><?php echo $contact['Group']['group_name']; ?>&nbsp;</td>
							<?php if($contact['ContactGroup']['un_subscribers']==0){ ?>
							<td style="text-align: center;color:#26C281;font-weight:bold">YES
							</td>
							<?php }elseif($contact['ContactGroup']['un_subscribers']==1){ ?>
							<td style="text-align: center;color:red;font-weight:bold">NO
							</td>
							<?php }else {?>
							<td style="text-align: center;color:#F7CA18;font-weight:bold">PENDING
							</td>
							<?php } ?>
							
							<?php if($contact['ContactGroup']['subscribed_by_sms']==0){ ?>
							<td style="text-align: left;">Import</td>
							<?php }else if($contact['ContactGroup']['subscribed_by_sms']==1) { ?>
							<td style="text-align: left;">SMS</td>
							<?php }else if($contact['ContactGroup']['subscribed_by_sms']==2){ ?>
							<td style="text-align: left;">Widget</td>
							<?php }else { ?>
                            <td style="text-align: left;">Kiosk</td>
                              <?php } ?>
                              
                            <td style="text-align: left;"><?php echo $contact['Contact']['location']; ?>&nbsp;</td>
                            <td style="text-align: left;"><?php echo $contact['Contact']['state']; ?>&nbsp;</td>
                            <td style="text-align: left;"><?php echo $contact['Contact']['zip']; ?>&nbsp;</td>
                            <td style="text-align: left;"><?php echo $contact['Contact']['custom1']; ?>&nbsp;</td>
                            <td style="text-align: left;"><?php echo $contact['Contact']['custom2']; ?>&nbsp;</td>
                            <td style="text-align: left;"><?php echo $contact['Contact']['custom3']; ?>&nbsp;</td>
                               
                            <td style="text-align: left;"><?php echo $contact['ContactGroup']['created']; ?>&nbsp;</td>	
								<?php if($contact['ContactGroup']['un_subscribers']==0){ ?>
							<td class="actions">
							    <?php if($userperm['sendsms']=='1'){ ?>
								<?php if(API_TYPE==0){?>
								<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal'));?> -->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

								<?php }else if(API_TYPE==1){ ?>
								<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'nexmo_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'nexmo_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

								<?php }else if(API_TYPE==2){ ?>
								<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'slooce_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'slooce_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

								<?php }else if(API_TYPE==3){ ?>
									<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'plivo_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'plivo_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>								
								<?php }else if(API_TYPE==4){ ?>
									<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'bandwidth_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'bandwidth_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
                                <?php }else if(API_TYPE==5){ ?>
									<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'signalwire_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'signalwire_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

                                <?php }else if(API_TYPE==6){ ?>
									<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'ytel_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'ytel_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								
								<?php }else if(API_TYPE==7){ ?>
									<!--<?php echo $this->Html->link(__('Send SMS', true), array('action' => 'ytel_send_sms', $contact['Contact']['phone_number']), array('class' => 'btn blue btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'telnyx_send_sms',$contact['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								
								<?php }} ?>
								
								<?php if(trim($contact['Contact']['email']) !='') { ?>
								<a id="pop_fancy" href="<?php echo SITE_URL; ?>/contacts/send_email/<?php echo $contact['Contact']['email'];?>" title='Send Email' style="margin-right:0px" class="btn yellow-casablanca btn-sm fancybox fancybox.iframe"><i class="fa fa-envelope-o" style="font-size:14px"></i></a>
<?} ?>
								
								<?php if(API_TYPE==0 || API_TYPE==5 || API_TYPE==7){?>
								
								<?php if(trim($contact['Contact']['fax_number']) !='') { ?>

<?php echo $this->Html->link('<i class="fa fa-fax" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'send_fax',$contact['Contact']['fax_number'],'contacts'),array('class'=> 'btn yellow-gold btn-sm nyroModal','escape'=> false,'title'=>'Send Fax','style'=>'margin-right:0px'));?>

<?} ?>
								
								<?php } ?>

<?php echo $this->Html->link('<i class="glyphicon glyphicon-sort-by-attributes" style="font-size:14px"></i>',array('action'=>'activity_timeline', $contact['Contact']['id'], 0),array('class'=> 'btn purple-soft btn-sm','escape'=> false,'title'=>'Incoming SMS Activity Timeline','style'=>'margin-right:0px')); ?>

								<!--<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $contact['Contact']['id']), array('class' => 'btn green btn-outline btn-sm nyroModal')); ?>-->

<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$contact['Contact']['id']),array('class'=> 'btn green btn-sm nyroModal','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px;z-index:9000')); ?>


								<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $contact['Contact']['id']), array('class' => 'btn red btn-outline btn-sm'),sprintf(__('Are you sure you want to delete this contact?',true))) ; ?>-->

<?php echo $this->Html->link('<i class="icon-ban" style="font-size:14px"></i>',array('action'=>'stop', $contact['Contact']['id']),array('class'=> 'btn blue-ebonyclay  btn-sm','escape'=> false,'title'=>'Unsubscribe Contact'), sprintf(__('Are you sure you want to unsubscribe this contact?',true))); ?>
<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete', $contact['Contact']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this contact?',true))); ?>


								<?php if(API_TYPE==2){?>
								<!--<?php echo $this->Html->link(__('Stop', true), array('action' => 'stoppartner/'.$contact['ContactGroup']['group_id'], $contact['Contact']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to stop this contact?',true))); ?>-->

<?php echo $this->Html->link('<i class="icon-user-unfollow" style="font-size:14px"></i>',array('action'=>'stoppartner/'.$contact['ContactGroup']['group_id'],$contact['Contact']['id']),array('class'=> 'btn red btn-sm','escape'=> false,'title'=>'Stop','style'=>'margin-left:-5px'), sprintf(__('Are you sure you want to stop this contact?',true))); ?>
								<?php } ?>
							</td>
								<?php }else{ ?>
							<td>
							    
							    <?php if(API_TYPE==0 && $contact['ContactGroup']['un_subscribers']!=2){?>
								
								<?php if(trim($contact['Contact']['fax_number']) !='') { ?>

<?php echo $this->Html->link('<i class="fa fa-fax" style="font-size:14px"></i>',array('controller'=>'contacts','action'=>'send_fax',$contact['Contact']['fax_number'],'contacts'),array('class'=> 'btn yellow-gold btn-sm nyroModal','escape'=> false,'title'=>'Send Fax','style'=>'margin-right:0px'));?>

<?} ?>
								
								<?php } ?>
<?php if($contact['ContactGroup']['un_subscribers']!=2){ ?>
<?php echo $this->Html->link('<i class="glyphicon glyphicon-sort-by-attributes" style="font-size:14px"></i>',array('action'=>'activity_timeline', $contact['Contact']['id'], 1),array('class'=> 'btn purple-soft btn-sm','escape'=> false,'title'=>'Incoming SMS Activity Timeline','style'=>'margin-right:0px')); ?>
<?} ?>
								<!--<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $contact['Contact']['id']), array('class' => 'btn green btn-outline btn-sm nyroModal')); ?>-->

								<!--<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $contact['Contact']['id']), array('class' => 'btn red btn-outline btn-sm'), sprintf(__('Are you sure you want to delete this contact?',true))); ?>-->

<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete', $contact['Contact']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this contact?',true))); ?>


							</td>
							<?php } ?>
						</tr>
							<?php endforeach; ?>
					</table>
						
						<?php } ?>
					<!--</div>-->
				
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