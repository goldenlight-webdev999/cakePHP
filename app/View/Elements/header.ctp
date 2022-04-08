<script>
function openhelpdesk(){
	window.open('<?php echo SITE_URL ?>/helpdesk','_blank','toolbar=0,status=0,scrollbars=1,width=826,height=636');
    return false;
}
</script>
	<div class="page-header navbar navbar-fixed-top" style="z-index:8000">
		<div class="page-header-inner ">
                <div class="page-logo">                 
                      <!--<a href=""><img src="<?php echo SITE_URL;?>/img/logo.png" alt="logo" class="logo-default" />-->
<?php if(LOGOUT_URL == ''){?>
      <?php echo $this->Html->link($this->Html->image(LOGO), '/',array('escape' =>false, 'class' =>'logo-default'));?>
<?}else{?>
      <?php echo $this->Html->link($this->Html->image(LOGO), LOGOUT_URL,array('escape' =>false, 'class' =>'logo-default'));?>
<?}?>
 <!--</a>     -->
                    <div class="menu-toggler sidebar-toggler"></div>
                </div>             
					<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"></a>     
<!--<div class="page-actions">
      <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <a class="dropdown-toggle" onclick="openhelpdesk()" style="color: #7f96ac"><i class="fa fa-life-ring" style="font-size:16px"></i>&nbsp;Helpdesk</a>&nbsp;&nbsp;
							</li>
                            <li class="dropdown dropdown-extended dropdown-inbox" id="header_task_bar">
                                <a class="dropdown-toggle" href="mailto:<?php echo SUPPORT_EMAIL ?>" style="color: #7f96ac"><i class="icon-envelope-open" style="font-size:16px"></i>&nbsp;<?php echo SUPPORT_EMAIL ?></a>
							</li>
</div>   -->  
			<div class="page-top">              
				<div class="top-menu db-nav">


                    <ul class="nav navbar-nav pull-right">

<!--*******-->
<li id="header_task_bar">
                                
                               
                                <?php if (substr(THEME_COLOR,0,2) == 'l2') {?>	
                                    <div style="padding-top:21px">
                                    <font style="color:#7f96ac">Timezone </font><span class="badge" style="background-color:#26C281"><?php echo $loggedUser['User']['timezone']?></span>   
                                    <font style="color:#7f96ac">&nbsp;&nbsp;&nbsp;&nbsp;SMS Credits</font>
                                <?php }else { ?>
                                    <div style="padding-top:12px">
                                      <font style="color:#fff">Timezone </font><span class="badge" style="background-color:#26C281"><?php echo $loggedUser['User']['timezone']?></span>  
                                      <font style="color:#fff">&nbsp;&nbsp;&nbsp;&nbsp;SMS Credits</font>
                                <?php } ?>

                                    <span class="badge badge-warning" id="smscreditbalance"><?php echo $loggedUser['User']['sms_balance']?></span>
                                </div>
  </li>
<?php if (API_TYPE !=2 && API_TYPE !=1) {?>	
<li id="header_task_bar">
                               
                                 <?php if (substr(THEME_COLOR,0,2) == 'l2') {?>	
                                    <div style="padding-top:21px;padding-right:30px">
                                        <?php if (API_TYPE == 0 || API_TYPE == 5 || API_TYPE == 7) {?>	
                                          <font style="color:#7f96ac">&nbsp;&nbsp;&nbsp;&nbsp;Voice/Fax Credits</font>
                                        <?php } else { ?>
                                          <font style="color:#7f96ac">&nbsp;&nbsp;&nbsp;&nbsp;Voice Credits</font>
                                        <?php } ?>
                                <?php }else { ?>
                                    <div style="padding-top:12px;padding-right:30px">
                                        <?php if (API_TYPE == 0 || API_TYPE == 5 || API_TYPE == 7) {?>
                                            <font style="color:#fff">&nbsp;&nbsp;&nbsp;&nbsp;Voice/Fax Credits</font>
                                        <?php } else { ?>
                                            <font style="color:#fff">&nbsp;&nbsp;&nbsp;&nbsp;Voice Credits</font>
                                        <?php } ?>
                                <?php } ?>
                                  
                                    <span class="badge badge-warning" id="voicecreditbalance"> <?php echo $loggedUser['User']['voice_balance']?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                                </div>
  </li>
<?php } ?>
<!--*******-->

<?php if($userperm['logs']=='1'){ ?>
							<li class="dropdown dropdown-extended dropdown-inbox dropdown-dark" id="header_task_bar">
                                <a href="<?php echo SITE_URL;?>/logs/index/smsinbox" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" data-close-others="true">
                                    <!--<i class="icon-envelope-letter"></i>-->
                                    <i class="icon-bubble"></i>
                                    <span class="badge badge-success"> <?php echo $unreadTextMsg;?></span>
                                </a>

<!--*************************-->
<ul class="dropdown-menu">
                                    <li class="external">
                                        <h3>You have
                                            <span class="bold"><?php echo $unreadTextMsg;?> New</span> Messages</h3>
                                        <a href="<?php echo SITE_URL;?>/logs/index/smsinbox">view all</a>
                                    </li>
<li>
<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#272727">
<?php
						$i = 0;
	                                        foreach ($logs_inboxdetails as $inboxdetail):
		                                 $i++;                                   
?>
<li>

                                                <a href="<?php echo SITE_URL;?>/logs/view/<?php echo $inboxdetail['Log']['id']?>" class="nyroModal">
                                                    
                                                    <span class="photo">
                                                        <img alt="" class="img-circle" src="<?php echo SITE_URL;?>/assets/layouts/layout2/img/avatar<?php echo $i;?>.jpg"> 
                                                    </span>
                                                   
                                                    <span class="subject">
                                                        <span class="from"> <?php echo $inboxdetail['Log']['phone_number']; ?> </span>
                                                        <span class="time"> <?php echo $inboxdetail['Log']['created']; ?></span>
                                                    </span>
                                                    <span class="message1"> <?php echo substr($inboxdetail['Log']['text_message'],0,60); ?>

<?php if(!empty($inboxdetail['Log']['image_url'])){
		
						$check=strpos($inboxdetail['Log']['image_url'],":");
			
						if($check!=''){
							$comma=strpos($inboxdetail['Log']['image_url'],",");
							if($comma!=''){
							$image_arr=explode(",",$inboxdetail['Log']['image_url']);
							foreach($image_arr as $value){	
			?>
							<img src="<?php echo $value; ?>" height="50px" width="50px" />
						<?php } } else{?>
							<img src="<?php echo $inboxdetail['Log']['image_url'] ?>" height="50px" width="50px" />
					<?php } }		?>
											
<?php } ?>	


</span>
                                                </a>
                                            </li>

<?php endforeach; ?>

</ul>
                                    </li>
                                </ul>	
<!--************************-->


							</li>
<?php } ?>

<?php if($userperm['logs'] =='1'){ ?>
<?php if (API_TYPE !=2 && API_TYPE !=1) {?>							
<li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_task_bar">
                                <a href="<?php echo SITE_URL;?>/logs/index/voice" class="dropdown-toggle" data-hover="dropdown" data-close-others="true">
                                    <i class="icon-call-in"></i>
                                    <span class="badge badge-danger"> <?php echo $unreadVoiceMsg;?></span>
                                </a>

<!--*************************-->
<ul class="dropdown-menu">
                                    <li class="external">
                                        <h3>You have
                                            <span class="bold"><?php echo $unreadVoiceMsg;?> New</span> Voicemails</h3>
                                        <a href="<?php echo SITE_URL;?>/logs/index/voice">view all</a>
                                    </li>
<li>
<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#272727">
<?php
						$i = 0;
	                                        foreach ($logs_inboxvoicedetails as $inboxvoicedetail):
		                                 $i++;                                   
?>
<li>
                                                <?php if(strpos($inboxvoicedetail['Log']['voice_url'],"http") !== false && (API_TYPE==0 || API_TYPE==3 || API_TYPE==4 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7)){ ?>
                                                <a href="<?php echo $inboxvoicedetail['Log']['voice_url'];?>" >
                                                <?php } else if (strpos($inboxvoicedetail['Log']['voice_url'],"http") == false && (API_TYPE==0 || API_TYPE==3 || API_TYPE==4 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7)) { ?> 
                                                <a href="">
                                                <?php } ?>
                                                <?php if (API_TYPE==1){?>
                                                <a href="<?php echo $inboxvoicedetail['Log']['voice_url'];?>">
                                                <?php } ?>
                                                    <span class="time">Listen</span>
                                                    <span class="details">
                                                        <span class="label label-sm label-icon label-danger">
                                                            <i class="icon-call-in" style="font-size:15px"></i>
                                                        </span> <?php echo $inboxvoicedetail['Log']['phone_number']; ?> 


                                                   </span>
                                                </a>
                                            </li>

<?php endforeach; ?>

</ul>
</ul>
                                    </li>
<?php }} ?>

<!--*********FAX INBOX**************-->
<?php if($userperm['logs'] =='1'){ ?>
<?php if (API_TYPE ==0 || API_TYPE == 5) {?>							
<li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_task_bar">
                                <a href="<?php echo SITE_URL;?>/logs/index/faxinbox" class="dropdown-toggle" data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-fax"></i>
                                    <span class="badge badge-info"> <?php echo $unreadFaxMsg;?></span>
                                </a>

<!--*************************-->
<ul class="dropdown-menu">
                                    <li class="external">
                                        <h3>You have
                                            <span class="bold"><?php echo $unreadFaxMsg;?> New</span> Faxes</h3>
                                        <a href="<?php echo SITE_URL;?>/logs/index/faxinbox">view all</a>
                                    </li>
<li>
<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#272727">
<?php
						$i = 0;
	                                        foreach ($logs_inboxfaxdetails as $inboxfaxdetail):
		                                 $i++;                                   
?>
<li>
                                                <?php if(strpos($inboxfaxdetail['Log']['voice_url'],"http") !== false){ ?>
                                                <a href="<?php echo $inboxfaxdetail['Log']['voice_url'];?>" >
                                                <?php } ?>
                                                    <span class="time">View Fax</span>
                                                    <span class="details">
                                                        <span class="label label-sm label-icon label-info">
                                                            <i class="fa fa-fax" style="font-size:15px"></i>
                                                        </span> <?php echo $inboxfaxdetail['Log']['phone_number']; ?> 


                                                   </span>
                                                </a>
                                            </li>

<?php endforeach; ?>

</ul>
</ul>
                                    </li>
<?php }} ?>
                                
<!--************************-->

             
                            <li class="dropdown dropdown-user" style="float:right">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <!--img alt="" class="img-circle" src="../assets/layouts/layout2/img/avatar3_small.jpg"--/-->
                                    <span class="username "> 
                                    
                                    <?php 
                                     if(!$this->Session->check('Subaccount')){
                                        echo ucfirst($this->Session->read('User.first_name'));
                                     }else{
                                        echo ucfirst($this->Session->read('Subaccount.first_name')).'<font style="font-weight:bold"> (Sub-Account)</font>'; 
                                     }
                                    ?> 
                                    
                                    </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
								<!--	<?php if($userperm['logs'] =='1'){ ?>
									<li>
                                        <a href="<?php echo SITE_URL;?>/logs/index/smsinbox">
                                           <i class="icon-envelope-letter"></i>New SMS<span class="badge badge-success"><?php echo $unreadTextMsg;?> </span></a>
                                    </li>
<?php if (API_TYPE !=2) {?>
									<li>
                                        <a href="<?php echo SITE_URL;?>/logs/index/voice">
                                           <i class="icon-bell"></i>New VMs<span class="badge badge-danger"> <?php echo $unreadVoiceMsg;?> </span></a>
                                    </li>
<?php }} ?>-->

                                <?php 
                                if(!$this->Session->check('Subaccount')){ ?>
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/users/edit">
                                           <i class="icon-user"></i>Account</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/users/change_password">
                                             <i class="icon-key"></i>Change Password</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/users/setting">
                                            <i class="icon-settings"></i>Settings</a>                         
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/users/email_services">
                                            <i class="icon-envelope"></i>Email Services</a>                         
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/subaccounts/index">
                                            <i class="icon-users" aria-hidden="true" style="font-size:14px"></i>Sub-Accounts</a>                         
                                    </li>
                                    <li>
                                        <a href="<?php echo SITE_URL;?>/users/api">
                                            <i class="fa fa-code" aria-hidden="true" style="font-size:14px"></i>API</a>                         
                                    </li>
                               
                                    <li class="divider"> </li>
                                <?php } ?>
                                    <li>
                                         <a href="<?php echo SITE_URL;?>/users/logout">
                                            <i class="icon-logout"></i>Log Out</a>
                                    </li>
                                    
                                </ul>
                            </li>


					</ul>

				</div>
			</div>
		</div>
    </div>