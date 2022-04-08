<!-- Navigation Start -->
        <div class="navigation navbar navbar-light bg-primary" id="side-bar">
            <!-- Logo Start -->
            <a class="d-none d-xl-block bg-light rounded p-1" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>/img/1601777106icon-01.png" style="max-width:35px;" />
            </a>
            <!-- Logo End -->

            <!-- Expand Sidebar Icon Start -->
            <!-- <a class="text-light mt-2 nav-link nav-toggle expand-toggle-btn" href="javascript:void(0)">
                <i class="fa fa-bars"></i>
                <span class="nav-name">Collapse Sidebar</span>
            </a> -->
            <!--  Expand Sidebar Icon End -->
            
            
            <ul class="nav nav-minimal flex-row flex-grow-1 justify-content-between flex-xl-column justify-content-xl-center"  role="tablist">


				<?php 
					$pay_activation_fee=PAY_ACTIVATION_FEES;
						if($loggedUser['User']['active']=='0' && $pay_activation_fee==1){?>
                        
                            <li class="active">
                                <a href="<?php echo SITE_URL;?>/users/dashboard" class="nav-link nav-toggle" title="Pay Activation Fee">
                                    <i class="fa fa-usd"></i>
                                </a>                                
			    </li>
			    <?php }else if($loggedUser['User']['assigned_number']=='0' && $loggedUser['User']['active']=='1' && (REQUIRE_MONTHLY_GETNUMBER == 0 || (REQUIRE_MONTHLY_GETNUMBER == 1 && $loggedUser['User']['package'] > 0)) && !($this->params['controller'] == 'users' && $this->params['action'] == 'thank_you')){ ?>
			    
            <?php if($userperm['getnumbers']=='1'){ ?>             
			<?php if(API_TYPE==1){?>
                        <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/nexmos/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                        </li>
			<?php }else if(API_TYPE==3){?>
						<li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/plivos/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                        </li>
			<?php }else if(API_TYPE==4){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/bandwidths/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                        </li>
            <?php }else if(API_TYPE==5){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/signalwires/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                        </li>
            <?php }else if(API_TYPE==6){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/ytels/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                        </li>     
            <?php }else if(API_TYPE==7){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/telnyxs/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                        </li>     
			<?php }else if(API_TYPE==0){?>
						<li class="">
							<a class="nyroModal" href="<?php echo SITE_URL;?>/twilios/searchcountry" title="Get Number">
							<i class="fa fa-phone"></i>
								</a>
						</li>
                            
                        
			<?php }} ?>
                        
            <?php } else if($loggedUser['User']['assigned_number']>0 && $loggedUser['User']['active']=='1' && $userperm['getnumbers']=='1' && (REQUIRE_MONTHLY_GETNUMBER == 0 || (REQUIRE_MONTHLY_GETNUMBER == 1 && $loggedUser['User']['package'] > 0)) && !($this->params['controller'] == 'users' && $this->params['action'] == 'thank_you')){ ?>
						<?php if($loggedUser['User']['number_limit_count'] < $loggedUser['User']['number_limit']){ ?>
							<?php if(API_TYPE==1){?>
							<li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/nexmos/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                            </a>
                            </li>
							<?php }else if(API_TYPE==3){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/plivos/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                                </a>
                            </li>
							<?php }else if(API_TYPE==4){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/bandwidths/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                                </a>
                            </li>
                            <?php }else if(API_TYPE==5){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/signalwires/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                                </a>
                            </li>
                            <?php }else if(API_TYPE==6){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/ytels/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                                </a>
                            </li>
                            <?php }else if(API_TYPE==7){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/telnyxs/searchcountry" title="Get Number">
                                <i class="fa fa-phone"></i>
                                </a>
                            </li>
							<?php }else if(API_TYPE==0){?>
								<li class="">
							    <a class="nyroModal" href="<?php echo SITE_URL;?>/twilios/searchcountry" title="Get Number">
							    <i class="fa fa-phone"></i>
								</a>
						    </li>
							<?php } ?>
                        <?php }else if($loggedUser['User']['number_limit_count'] == $loggedUser['User']['number_limit'] && CHARGE_FOR_ADDITIONAL_NUMBERS == 1 && $userperm['getnumbers']=='1'){ ?>
							<li class="">
							<a class="nyroModal" href="<?php echo SITE_URL;?>/users/purchasenumber" title="Get Number"> 
                                                            <i class="fa fa-phone"></i>
							</a>
			            <?php } ?>
                        
            <?php }  ?>     
			
			            <li class="nav-item <?php if ($this->params['controller'] == 'users' && $this->params['action'] == 'profile') { ?>  active open <?php } ?>">			    
                            <a href="<?php echo SITE_URL;?>/users/profile" class="nav-link nav-toggle" title="Dashboard">
                                <i class="fa fa-tachometer"></i>
                                <span class="nav-name">Dashboard</span>
                            </a>                            
                            
			            </li>
                        <?php if($userperm['groups']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'groups' && ($this->params['action'] == 'index' || $this->params['action'] == 'add' || $this->params['action'] == 'edit' || $this->params['action'] == 'view')) { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/groups/index" class="nav-link nav-toggle" title="Dashboard">
                                <i class="fa fa-users"></i>
                                <span class="nav-name">Groups</span>
                            </a>                            
                        </li>
                        <?php } ?>
                        
						<?php if($userperm['contactlist']=='1' || $userperm['importcontacts']=='1'){ ?>
                            <li class="nav-item <?php if ($this->params['controller'] == 'contacts' && ($this->params['action'] == 'index' || $this->params['action'] == 'upload' || $this->params['action'] == 'activity_timeline')) { ?>  active open <?php } ?> ">
                                <?php if($userperm['contactlist']=='1'){ ?>
                                <a href="javscript:void(0)" class="nav-link nav-toggle caret-toggle" title="Contacts">
                                <?php } else{ ?>
                                <a href="" class="nav-link nav-toggle" title="Contacts">
                                <?php } ?>
                                    <i class="fa fa-user"></i>
                                    <span class="nav-name">Contacts</span>
                                    <b class="caret"></b>
                                </a>
                                <?php if($userperm['contactlist']=='1' || $userperm['importcontacts']=='1'){ ?>
                                    <ul class="sub-menu">
                                        <?php if($userperm['contactlist']=='1'){ ?>
                                            <li class="nav-item <?php if ($this->params['controller'] == 'contacts' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                                <a href="<?php echo SITE_URL;?>/contacts/index" class="nav-link " title="Manage Contacts">
                                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                                    <span class="nav-name">Manage Contacts</span>
                                                </a>
                                            </li> 
                                        <?php } ?>
                                            
                                        <?php if($userperm['importcontacts']=='1'){ ?>
                                            <li class="nav-item <?php if ($this->params['controller'] == 'contacts' && $this->params['action'] == 'upload') { ?>  active open <?php } ?>">
                                                <a href="<?php echo SITE_URL;?>/contacts/upload" class="nav-link nav-toggle" title="Import Contacts">
                                                    <i class="fa fa-cloud-download" aria-hidden="true"></i>
                                                    <span class="nav-name">Import Contacts</span>
                                                </a>
                                                
                                            </li> 
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                                
                                
                            </li>
                        <?php } ?>

                        <?php if($userperm['sendsms']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'messages') { ?>  active open <?php } ?>">
                            <a href="javascript:void(0)" class="nav-link nav-toggle caret-toggle" title="Messages">
                                <i class="fa fa-comment-o"></i>
                                <span class="nav-name">Messages</span>
                                <b class="caret"></b>
                            </a>
                            
                            <ul class="sub-menu">
                                <li class="nav-item <?php if ($this->params['controller'] == 'messages' && $this->params['action'] == 'send_message') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/messages/send_message" class="nav-link " title="Send Bulk SMS">
                                        <i class="fa fa-comments" aria-hidden="true"></i>
                                        <span class="nav-name">Send Bulk SMS</span>
                                    </a>
                                    
                                </li> 
                                
                                <li class="nav-item <?php if ($this->params['controller'] == 'messages' && $this->params['action'] == 'schedule_message') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/messages/schedule_message" class="nav-link nav-toggle" title="Groups Message Queue">
                                        <i class="fa fa-commenting" aria-hidden="true"></i>
                                        <span class="nav-name">Groups Message Queue</span>
                                    </a>
                                    
                                </li>
                                
                                <li class="nav-item <?php if ($this->params['controller'] == 'messages' && $this->params['action'] == 'singlemessages') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/messages/singlemessages" class="nav-link nav-toggle" title="Contacts Message Queue">
                                        <i class="fa fa-commenting-o" aria-hidden="true"></i>
                                        <span class="nav-name">Contacts Message Queue</span>
                                    </a>
                                    
                                </li> 
                                
                                <li class="nav-item <?php if ($this->params['controller'] == 'messages' && $this->params['action'] == 'template_message') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/messages/template_message" class="nav-link nav-toggle" title="Message Templates">
                                        <i class="fa fa-comment" aria-hidden="true"></i>
                                        <span class="nav-name">Message Templates</span>
                                    </a>
                                    
                                </li>
                            </ul>
                        </li>   
                            
                        <?php } ?>
                        
                        <?php if($userperm['calendarscheduler']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'schedulers') { ?>  active open <?php } ?>">
					        <a href="<?php echo SITE_URL;?>/schedulers/view" class="nav-link nav-toggle" title="Scheduled Calendar">
						      <i class="fa fa-calendar"></i>
                              <span class="nav-name">Scheduled Calendar</span>
					        </a>
			            </li>   
			            <?php } ?>

                        <?php if($userperm['smschat']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'chats') { ?>  active open <?php } ?>">
					        <a href="<?php echo SITE_URL;?>/chats" class="nav-link nav-toggle" title="SMS Chat">
						      <i class="fa fa-commenting-o"></i>
                              <span class="nav-name">SMS Chat</span>
					        </a>
			            </li> 
                        <?php } ?>  
                        
                        <?php if($userperm['appointments']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'appointments') { ?>  active open <?php } ?>">
                            <a href="javascript:void(0)" class="nav-link nav-toggle caret-toggle" title="Appointments">
                                <i class="fa fa-clock-o"></i>
                                <span class="nav-name">Appointments</span>
                                <b class="caret"></b>
                            </a>

                            <ul class="sub-menu">
                                <li class="nav-item <?php if ($this->params['controller'] == 'appointments' && $this->params['action'] == 'settings') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/appointments/settings" class="nav-link " title="Settings">
                                        <i class="fa fa-cogs" aria-hidden="true"></i>
                                        <span class="nav-name">Settings</span>
                                    </a>
                                </li>  
                                    
                                <li class="nav-item <?php if ($this->params['controller'] == 'appointments' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/appointments/index" class="nav-link " title="Appointment List">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                                        <span class="nav-name">Appointment List</span>
                                    </a>
                                </li>  
                                
                                <li class="nav-item <?php if ($this->params['controller'] == 'appointments' && $this->params['action'] == 'view') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/appointments/view" class="nav-link " title="Appointment Calendar">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                        <span class="nav-name">Appointment Calendar</span>
                                    </a>
                                </li>  
                                
                                <li class="nav-item <?php if ($this->params['controller'] == 'appointments' && $this->params['action'] == 'upload') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/appointments/upload" class="nav-link " title="Import Appointments">
                                        <i class="fa fa-arrow-circle-o-down" aria-hidden="true"></i>
                                        <span class="nav-name">Import Appointments</span>
                                    </a>
                                </li>
                            </ul>
                        <?php } ?>  
                        
                        <?php if(API_TYPE !=2 && API_TYPE !=1){ ?> 
                        <?php if($userperm['voicebroadcast']=='1'){ ?>                       
                        <li class="nav-item  <?php if ($this->params['controller'] == 'groups' && ($this->params['action'] == 'broadcast_list' || $this->params['action'] == 'voicebroadcast' || $this->params['action'] == 'edit_broadcast')) { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/groups/broadcast_list" class="nav-link nav-toggle" title="Voice Broadcast">
                                <i class="fa fa-bullhorn"></i>
                                <span class="nav-name">Voice Broadcast</span>
                            </a>
			            </li>
                        <?php }} ?>
                        

			            <li class="nav-item  <?php if ($this->params['controller'] == 'mobilecoupons' || $this->params['controller'] == 'multiquestions' || $this->params['controller'] == 'responders' || $this->params['controller'] == 'polls' || $this->params['controller'] == 'contests' || $this->params['controller'] == 'smsloyalty' 
			            || $this->params['controller'] == 'kiosks' || $this->params['controller'] == 'birthday' || $this->params['controller'] == 'mobile_pages' || $this->params['controller'] == 'webwidgets' || ($this->params['controller'] == 'users' && $this->params['action'] == 'qrcodeindex') || ($this->params['controller'] == 'users' && ($this->params['action'] == 'shortlinks' || $this->params['action'] == 'shortlinkadd'))) { ?>  active open <?php } ?>">
                            <?php if($userperm['autoresponders']=='1'){ ?> 
                            <a href="javascript:void(0)" class="nav-link nav-toggle caret-toggle" title="Tools">
                            <?php } else{ ?>
                            <a href="" class="nav-link nav-toggle caret-toggle" title="Tools">
                            <?php } ?>
                                <i class="fa fa-wrench"></i>
                                <span class="nav-name">Tools</span>
                                <b class="caret"></b>
                            </a>
                            
                            <ul class="sub-menu">
                                <?php if($userperm['autoresponders']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'responders' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/responders/index" class="nav-link nav-toggle" title="Auto Responders">
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                            <span class="nav-name">Auto Responders</span>
                                        </a>
                                    </li>
                                <?php } ?>      
                                    
                                <?php if($userperm['polls']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'polls' && $this->params['action'] == 'question_list') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/polls/question_list" class="nav-link nav-toggle" title="Polls">
                                            <i class="fa fa-question" aria-hidden="true"></i>
                                            <span class="nav-name">Polls</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['contests']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'contests' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/contests/index" class="nav-link nav-toggle" title="Contests">
                                            <i class="fa fa-bars" aria-hidden="true"></i>
                                            <span class="nav-name">Contests</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['qabots']=='1' && NUMACCOUNTS >=30){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'multiquestions' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/multiquestions/index" class="nav-link nav-toggle" title="Q & A SMS Bots">
                                            <i class="fa fa-question" aria-hidden="true"></i>
                                            <span class="nav-name">Q & A SMS Bots</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                    
                                <?php if($userperm['mobilecoupons']=='1' && NUMACCOUNTS >=100){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'mobilecoupons') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/mobilecoupons" class="nav-link nav-toggle" title="Mobile Coupons - NEW">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            <span class="nav-name">Mobile Coupons - NEW</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                    
                                <?php if($userperm['loyaltyprograms']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'smsloyalty' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/smsloyalty/index" class="nav-link nav-toggle" title="SMS Loyalty Programs">
                                            <i class="fa fa-envelope-open-o" aria-hidden="true"></i>
                                            <span class="nav-name">SMS Loyalty Programs</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['kioskbuilder']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'kiosks' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/kiosks/index" class="nav-link nav-toggle" title="Kiosk Builder">
                                            <i class="fa fa-shield" aria-hidden="true"></i>
                                            <span class="nav-name">Kiosk Builder</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['birthdaywishes']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'birthday' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/birthday/index" class="nav-link nav-toggle" title="Birthday SMS Wishes">
                                            <i class="fa fa-birthday-cake" aria-hidden="true"></i>
                                            <span class="nav-name">Birthday SMS Wishes</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['mobilepagebuilder']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'mobile_pages' && $this->params['action'] == 'pagedetails') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/mobile_pages/pagedetails" class="nav-link nav-toggle" title="Mobile Page Builder">
                                            <i class="fa fa-mobile" aria-hidden="true"></i>
                                            <span class="nav-name">Mobile Page Builder</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['webwidgets']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'weblinks' && $this->params['action'] == 'index') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/weblinks/index" class="nav-link nav-toggle" title="Web Sign-Up Widgets">
                                            <i class="fa fa-globe" aria-hidden="true"></i>
                                            <span class="nav-name">Web Sign-Up Widgets</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if($userperm['qrcodes']=='1'){ ?>
                                    <li class="nav-item <?php if ($this->params['controller'] == 'users' && $this->params['action'] == 'qrcodeindex') { ?>  active open <?php } ?>">
                                        <a href="<?php echo SITE_URL;?>/users/qrcodeindex" class="nav-link nav-toggle" title="QR Codes">
                                            <i class="fa fa-qrcode" aria-hidden="true"></i>
                                            <span class="nav-name">QR Codes</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                    <?php if(BITLY_ACCESS_TOKEN !=''){ ?>
                                        <?php if($userperm['shortlinks']=='1'){ ?>
                                            <li class="nav-item <?php if ($this->params['controller'] == 'users' && $this->params['action'] == 'shortlinks') { ?>  active open <?php } ?>">
                                                <a href="<?php echo SITE_URL;?>/users/shortlinks" class="nav-link nav-toggle" title="Short Links">
                                                    <i class="fa fa-link" aria-hidden="true"></i>
                                                    <span class="nav-name">Short Links</span>
                                                </a>
                                            </li> 
                                    <?php }} ?>
                                    
                                <li class="nav-item <?php if ($this->params['controller'] == 'messages' && $this->params['action'] == 'nongsm') { ?>  active open <?php } ?>">
                                    <a href="<?php echo SITE_URL;?>/messages/nongsm" class="nav-link nav-toggle nyroModal" title="Non-GSM Character Checker">
                                        <i class="fa fa-file-word-o" aria-hidden="true"></i>
                                        <span class="nav-name">Non-GSM Character Checker</span>
                                    </a>
                                </li>
                            </ul>                            
                        
                        <?php if($userperm['logs']=='1'){ ?>
						<li class="nav-item  <?php if ($this->params['controller'] == 'logs') { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/logs/index" class="nav-link nav-toggle" title="Logs">
                                <i class="fa fa-file-text-o"></i>
                                <span class="nav-name">Logs</span>
                            </a>
						</li>
						<?php } ?>
						
						<?php if($userperm['reports']=='1'){ ?>
						<li class="nav-item  <?php if ($this->params['controller'] == 'users' && ($this->params['action'] == 'subscribers' || $this->params['action'] == 'unsubscribers')) { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/users/subscribers" class="nav-link nav-toggle" title="Reports">
                                <i class="fa fa-bar-chart"></i>
                                <span class="nav-name">Reports</span>
                            </a>
						</li>
						<?php } ?>
						
						<?php if($userperm['affiliates']=='1'){ ?>
						<li class="nav-item  <?php if (($this->params['controller'] == 'users' && $this->params['action'] == 'affiliates') || $this->params['controller'] == 'referrals') { ?>  active open <?php } ?>">
                           <a href="<?php echo SITE_URL;?>/users/affiliates" class="nav-link nav-toggle" title="Affiliates">
                                <i class="fa fa-dollar"></i>
                                <span class="nav-name">Affiliates</span>
                            </a>
                                                </li>
                                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/users/affiliates" class="nav-link " title="Affiliate URLs">
                                        <i class="fa fa-external-link" aria-hidden="true"></i>
                                        <span class="nav-name">Affiliate URLs</span>
                                    </a>
                                </li> 
                                
                                <li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/referrals/index" class="nav-link nav-toggle" title="Referrals ">
										<i class="fa fa-anchor" aria-hidden="true"></i>
                                        <span class="nav-name">Referrals</span>
									</a>
								</li>                               
							
                        <?php } ?>
                        
						<li class="nav-item  ">
                            <a href="<?php echo SITE_URL;?>/users/logout" class="nav-link nav-toggle" title="Logout">
                                <i class="fa fa-sign-out"></i>
                                <span class="nav-name">Logout</span>
                            </a>
						</li>
					</ul>
            <!-- Main Nav Start -->
            <ul style="display:none;" class="nav nav-minimal flex-row flex-grow-1 justify-content-between flex-xl-column justify-content-xl-center" id="mainNavTab" role="tablist">
                
                <!-- Chats Tab Start -->
                <li class="nav-item">
                    <a class="nav-link p-0 py-xl-3 active" id="chats-tab" href="#chats-content" title="Chats">
                        <!-- Default :: Inline SVG -->
                        <svg class="hw-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>

                        <!-- Alternate :: External File link -->
                        <!-- <img class="injectable hw-24" src="./../assets/media/heroicons/outline/chat-alt-2.svg" alt="Chat icon"> -->
                    </a>
                </li>
                <!-- Chats Tab End -->
                
            </ul>
            <!-- Main Nav End -->
        </div>
        <!-- Navigation End -->