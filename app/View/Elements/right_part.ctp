<?php if($this->Session->check('User')):?>
	<div class="page-sidebar-wrapper">
        <div class="page-sidebar navbar-collapse collapse">
            <ul class="page-sidebar-menu page-header-fixed  " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">


				<?php 
					$pay_activation_fee=PAY_ACTIVATION_FEES;
						if($loggedUser['User']['active']=='0' && $pay_activation_fee==1){?>
                        
                            <li class="active">
                            <a href="<?php echo SITE_URL;?>/users/dashboard" class="nav-link nav-toggle">
                                <i class="fa fa-usd"></i>
                                <span class="title">Pay Activation Fee</span>
                                <!--<span class="arrow"></span>-->
                            </a>     
			    </li>
			    <?php }else if($loggedUser['User']['assigned_number']=='0' && $loggedUser['User']['active']=='1' && (REQUIRE_MONTHLY_GETNUMBER == 0 || (REQUIRE_MONTHLY_GETNUMBER == 1 && $loggedUser['User']['package'] > 0)) && !($this->params['controller'] == 'users' && $this->params['action'] == 'thank_you')){ ?>
			    
            <?php if($userperm['getnumbers']=='1'){ ?>             
			<?php if(API_TYPE==1){?>
                        <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/nexmos/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                        </li>
			<?php }else if(API_TYPE==3){?>
						<li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/plivos/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                        </li>
			<?php }else if(API_TYPE==4){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/bandwidths/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                        </li>
            <?php }else if(API_TYPE==5){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/signalwires/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                        </li>
            <?php }else if(API_TYPE==6){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/ytels/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                        </li>     
            <?php }else if(API_TYPE==7){?>
					    <li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/telnyxs/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                        </li>     
			<?php }else if(API_TYPE==0){?>
						<li class="">
							<a class="nyroModal" href="<?php echo SITE_URL;?>/twilios/searchcountry">
							<i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
								</a>
						</li>
                            
                        
			<?php }} ?>
                        <!--<li class="nav-item">
                            <a href="<?php echo SITE_URL;?>/users/profile" class="nav-link nav-toggle">
                                <i class="fa fa-tachometer"></i>
                                <span class="title">Dashboard</span>
                            </a> 
                        </li>-->
                        
            <?php } else if($loggedUser['User']['assigned_number']>0 && $loggedUser['User']['active']=='1' && $userperm['getnumbers']=='1' && (REQUIRE_MONTHLY_GETNUMBER == 0 || (REQUIRE_MONTHLY_GETNUMBER == 1 && $loggedUser['User']['package'] > 0)) && !($this->params['controller'] == 'users' && $this->params['action'] == 'thank_you')){ ?>
						<?php if($loggedUser['User']['number_limit_count'] < $loggedUser['User']['number_limit']){ ?>
							<?php if(API_TYPE==1){?>
							<li class="">
                            <a class="nyroModal" href="<?php echo SITE_URL;?>/nexmos/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                            </a>
                            </li>
							<?php }else if(API_TYPE==3){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/plivos/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                                </a>
                            </li>
							<?php }else if(API_TYPE==4){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/bandwidths/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                                </a>
                            </li>
                            <?php }else if(API_TYPE==5){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/signalwires/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                                </a>
                            </li>
                            <?php }else if(API_TYPE==6){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/ytels/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                                </a>
                            </li>
                            <?php }else if(API_TYPE==7){?>
								<li class="">
                                <a class="nyroModal" href="<?php echo SITE_URL;?>/telnyxs/searchcountry">
                                <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
                                </a>
                            </li>
							<?php }else if(API_TYPE==0){?>
								<li class="">
							    <a class="nyroModal" href="<?php echo SITE_URL;?>/twilios/searchcountry">
							    <i class="fa fa-phone"></i>
                                <span class="title">Get Number</span>
                                <!--<span class="arrow"></span>-->
								</a>
						    </li>
							<?php } ?>
                        <?php }else if($loggedUser['User']['number_limit_count'] == $loggedUser['User']['number_limit'] && CHARGE_FOR_ADDITIONAL_NUMBERS == 1 && $userperm['getnumbers']=='1'){ ?>
							<li class="">
							<a class="nyroModal" href="<?php echo SITE_URL;?>/users/purchasenumber"> <i class="fa fa-phone"></i>
                            <span class="title">Get Number</span>
                            <!--<span class="arrow"></span>-->
							</a>
			            <?php } ?>
                        <!--<li class="nav-item">			    
                            <a href="<?php echo SITE_URL;?>/users/profile" class="nav-link nav-toggle">
                                <i class="fa fa-tachometer"></i>
                                <span class="title">Dashboard</span>
                            </a>
			            </li>-->
            <?php }  ?>     
			
			            <li class="nav-item <?php if ($this->params['controller'] == 'users' && $this->params['action'] == 'profile') { ?>  active open <?php } ?>">			    
                            <a href="<?php echo SITE_URL;?>/users/profile" class="nav-link nav-toggle">
                                <i class="fa fa-tachometer"></i>
                                <span class="title">Dashboard</span>
                                <!--<span class="arrow"></span>-->
                            </a>
			            </li>
                        <?php if($userperm['groups']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'groups' && ($this->params['action'] == 'index' || $this->params['action'] == 'add' || $this->params['action'] == 'edit' || $this->params['action'] == 'view')) { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/groups/index" class="nav-link nav-toggle">
                                <i class="fa fa-users"></i>
                                <span class="title">Groups (Keywords)</span>
                            </a>
                        </li>
                        <?php } ?>
                        
						<?php if($userperm['contactlist']=='1' || $userperm['importcontacts']=='1'){ ?>
                        <li class="nav-item <?php if ($this->params['controller'] == 'contacts' && ($this->params['action'] == 'index' || $this->params['action'] == 'upload' || $this->params['action'] == 'activity_timeline')) { ?>  active open <?php } ?> ">
                            <?php if($userperm['contactlist']=='1'){ ?>
                               <a href="<?php echo SITE_URL;?>/contacts/index" class="nav-link nav-toggle">
                            <?php } else{ ?>
                               <a href="" class="nav-link nav-toggle">
                            <?php } ?>
                                <i class="fa fa-user"></i>
                                <span class="title">Contacts</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <?php if($userperm['contactlist']=='1'){ ?>
                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/contacts/index" class="nav-link ">
                                        <span class="title">Manage Contacts</span>
                                    </a>
                                </li> 
                                <?php } ?>
                                
                                <?php if($userperm['importcontacts']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/contacts/upload" class="nav-link nav-toggle">
										<i class=""></i>
										<span class="title">Import Contacts</span>

									</a>
								</li> <?php } ?>  
                            </ul>
                        </li>
                        <?php } ?>

                        <?php if($userperm['sendsms']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'messages') { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/messages/send_message" class="nav-link nav-toggle">
                                <i class="fa fa-comment-o"></i>
							
                                <span class="title">Messages</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/messages/send_message" class="nav-link ">
                                        <span class="title">Send Bulk SMS</span>
                                    </a>
                                </li> 
                                                                
                                <li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/messages/schedule_message" class="nav-link nav-toggle">
										<i class=""></i>
										<span class="title">Groups Message Queue</span>

									</a>
								</li> 
								
                                <li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/messages/singlemessages" class="nav-link nav-toggle">
										<i class=""></i>
										<span class="title">Contacts Message Queue</span>

									</a>
								</li> 
								
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/messages/template_message" class="nav-link nav-toggle">
										<i class=""></i>
										<span class="title">Message Templates</span>

									</a>
								</li> 
                            </ul>
                        </li>
                        <?php } ?>
                        
                        <?php if($userperm['calendarscheduler']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'schedulers') { ?>  active open <?php } ?>">
					        <a href="<?php echo SITE_URL;?>/schedulers/view" class="nav-link nav-toggle">
						      <i class="fa fa-calendar"></i>
						      <span class="title">Scheduled Calendar</span>
					        </a>
			            </li>   
			            <?php } ?>

                        <?php if($userperm['smschat']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'chats') { ?>  active open <?php } ?>">
					        <a href="<?php echo SITE_URL;?>/chats" class="nav-link nav-toggle">
						      <i class="fa fa-commenting-o"></i>
						      <span class="title">SMS Chat&nbsp;&nbsp;<span class="badge badge-danger"><?php echo $unreadTextMsg;?></span></span>

					        </a>
			            </li> 
                        <?php } ?>  
                        
                        <?php if($userperm['appointments']=='1'){ ?>
                        <li class="nav-item  <?php if ($this->params['controller'] == 'appointments') { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/appointments/index" class="nav-link nav-toggle">
                                <i class="fa fa-clock-o"></i>
							
                                <span class="title">Appointments</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/appointments/settings" class="nav-link ">
                                        <span class="title">Settings</span>
                                    </a>
                                </li>  
                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/appointments/index" class="nav-link ">
                                        <span class="title">Appointment List</span>
                                    </a>
                                </li>  
                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/appointments/view" class="nav-link ">
                                        <span class="title">Appointment Calendar</span>
                                    </a>
                                </li>  
								<li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/appointments/upload" class="nav-link ">
                                        <span class="title">Import Appointments</span>
                                    </a>
                                </li> 
                            </ul>
                        </li><?php } ?>  
                        
                        <?php if(API_TYPE !=2 && API_TYPE !=1){ ?> 
                        <?php if($userperm['voicebroadcast']=='1'){ ?>                       
                        <li class="nav-item  <?php if ($this->params['controller'] == 'groups' && ($this->params['action'] == 'broadcast_list' || $this->params['action'] == 'voicebroadcast' || $this->params['action'] == 'edit_broadcast')) { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/groups/broadcast_list" class="nav-link nav-toggle">
                                <i class="fa fa-bullhorn"></i>
                                <span class="title">Voice Broadcast</span>

                            </a>     
			            </li>
                        <?php }} ?>
                        

			            <li class="nav-item  <?php if ($this->params['controller'] == 'mobilecoupons' || $this->params['controller'] == 'multiquestions' || $this->params['controller'] == 'responders' || $this->params['controller'] == 'polls' || $this->params['controller'] == 'contests' || $this->params['controller'] == 'smsloyalty' 
			            || $this->params['controller'] == 'kiosks' || $this->params['controller'] == 'birthday' || $this->params['controller'] == 'mobile_pages' || $this->params['controller'] == 'webwidgets' || ($this->params['controller'] == 'users' && $this->params['action'] == 'qrcodeindex') || ($this->params['controller'] == 'users' && ($this->params['action'] == 'shortlinks' || $this->params['action'] == 'shortlinkadd'))) { ?>  active open <?php } ?>">
                            <?php if($userperm['autoresponders']=='1'){ ?> 
                            <a href="<?php echo SITE_URL;?>/responders/index" class="nav-link nav-toggle">
                            <?php } else{ ?>
                            <a href="" class="nav-link nav-toggle">
                            <?php } ?>
                                <i class="fa fa-wrench"></i>
                                <span class="title">Tools</span>
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <?php if($userperm['autoresponders']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/responders/index" class="nav-link nav-toggle">
										<i class=""></i>
										<span class="title">Auto Responders</span>
										<!--<span class="arrow"></span>-->
									</a>     
								</li> <?php } ?>      
								
								<?php if($userperm['polls']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/polls/question_list" class="nav-link nav-toggle">
										<span class="title">Polls</span>

									</a>     
								</li><?php } ?>
                                                                <?php if($userperm['contests']=='1'){ ?>
								<li class="nav-item">
									<a href="<?php echo SITE_URL;?>/contests/index" class="nav-link nav-toggle">
										<span class="title">Contests</span>

									</a>     
								</li><?php } ?>
								<?php if($userperm['qabots']=='1' && NUMACCOUNTS >=30){ ?>
								<li class="nav-item">
									<a href="<?php echo SITE_URL;?>/multiquestions/index" class="nav-link nav-toggle">
										<span class="title">Q&A SMS Bots&nbsp;&nbsp;<span class="badge badge-success">NEW</span></span>

									</a>     
								</li><?php } ?>
								
								<?php if($userperm['mobilecoupons']=='1' && NUMACCOUNTS >=100){ ?>
								<li class="nav-item">
									<a href="<?php echo SITE_URL;?>/mobilecoupons" class="nav-link nav-toggle">
										<span class="title">Mobile Coupons&nbsp;&nbsp;<span class="badge badge-success">NEW</span></span>

									</a>     
								</li><?php } ?>
								
                                                                <?php if($userperm['loyaltyprograms']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/smsloyalty/index" class="nav-link nav-toggle">
										<span class="title">SMS Loyalty Programs</span>

									</a>     
								</li> <?php } ?> 
                                                                <?php if($userperm['kioskbuilder']=='1'){ ?>
                                                                <li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/kiosks/index" class="nav-link nav-toggle">
										<span class="title">Kiosk Builder</span>

									</a>     
								</li><?php } ?>
                                                                <?php if($userperm['birthdaywishes']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/birthday/index" class="nav-link nav-toggle">
										<span class="title">Birthday SMS Wishes</span>

									</a>     
								</li><?php } ?>
                                                                <?php if($userperm['mobilepagebuilder']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/mobile_pages/pagedetails" class="nav-link nav-toggle">
										<span class="title">Mobile Page Builder</span>

									</a>     
								</li><?php } ?>
                                                                <?php if($userperm['webwidgets']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/weblinks/index" class="nav-link nav-toggle">
										<span class="title">Web Sign-up Widgets</span>

									</a>     
								</li><?php } ?>
                                                                <?php if($userperm['qrcodes']=='1'){ ?>
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/users/qrcodeindex" class="nav-link nav-toggle">
										<span class="title">QR Codes</span>

									</a>     
								</li><?php } ?>
								 <?php if(BITLY_ACCESS_TOKEN !=''){ ?>
<?php if($userperm['shortlinks']=='1'){ ?>
                                <li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/users/shortlinks" class="nav-link nav-toggle">
										<i class=""></i>
										<span class="title">Short Links</span>

									</a>
								</li> 
                                <?php }} ?>  
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/messages/nongsm" class="nav-link nav-toggle nyroModal">
										<span class="title">Non-GSM Character Checker</span>

									</a>     
								</li>							
                            </ul>
                        </li>
                        
                        <?php if($userperm['logs']=='1'){ ?>
						<li class="nav-item  <?php if ($this->params['controller'] == 'logs') { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/logs/index" class="nav-link nav-toggle">
                                <i class="fa fa-file-text-o"></i>
                                <span class="title"> Logs   </span>

                            </a>     
						</li>
						<?php } ?>
						
						<?php if($userperm['reports']=='1'){ ?>
						<li class="nav-item  <?php if ($this->params['controller'] == 'users' && ($this->params['action'] == 'subscribers' || $this->params['action'] == 'unsubscribers')) { ?>  active open <?php } ?>">
                            <a href="<?php echo SITE_URL;?>/users/subscribers" class="nav-link nav-toggle">
                                <i class="fa fa-bar-chart"></i>
                                <span class="title"> Reports   </span>

                            </a>     
						</li>
						<?php } ?>
						
						<?php if($userperm['affiliates']=='1'){ ?>
						<li class="nav-item  <?php if (($this->params['controller'] == 'users' && $this->params['action'] == 'affiliates') || $this->params['controller'] == 'referrals') { ?>  active open <?php } ?>">
                           <a href="<?php echo SITE_URL;?>/users/affiliates" class="nav-link nav-toggle">
                                <i class="fa fa-dollar"></i>
                                <span class="title">Affiliates</span>
                                <span class="arrow"></span>
                            </a>
							<ul class="sub-menu">
                                <li class="nav-item  ">
                                    <a href="<?php echo SITE_URL;?>/users/affiliates" class="nav-link ">
                                        <span class="title">
                                            Affiliate URLs</span>
                                    </a>
                                </li> 
								<li class="nav-item  ">
									<a href="<?php echo SITE_URL;?>/referrals/index" class="nav-link nav-toggle">
										<span class="title"> Referrals </span>

									</a>     
								</li>                               
                            </ul>
                        </li>
                        <?php } ?>
                        
						<li class="nav-item  ">
                            <a href="<?php echo SITE_URL;?>/users/logout" class="nav-link nav-toggle">
                                <i class="fa fa-sign-out"></i>
                                <span class="title"> Logout  </span>

                            </a>     
						</li>
					</ul>	
                </div>
            </div>			
				<!--h4><span>Statistics</span></h4>
				<div class="sidebarbox">
					<p>Primary Number: <b><?php echo $loggedUser['User']['assigned_number'];?></b>
					<?php if(API_TYPE==1){?>
					Number List: (<b><?php echo $this->Html->link('Number List',array('controller' =>'users', 'action' =>'numberlist_nexmo'), array('class' => 'nyroModal', 'style' => 'color:#12759E;'));?></b>)
					Timezone: <b><?php echo $loggedUser['User']['timezone'];?></b>
					<?php }else{?>
					Number List: (<b><?php echo $this->Html->link('Number List',array('controller' =>'users', 'action' =>'numberlist_twillio'), array('class' => 'nyroModal', 'style' => 'color:#12759E;'));?></b>)
					Timezone: <b><?php echo $loggedUser['User']['timezone'];?></b>
					
					<?php } ?></p>
					Referred Users (activated/paid): <b><?php echo $statistic['referredUser'];?></b><br/>
					Overall Credited Commissions: <b>
				<?php 
						   $currencycode=PAYMENT_CURRENCY_CODE;
							
						   if($currencycode=='MXN' || $currencycode=='USD' || $currencycode=='AUD' || $currencycode=='CAD' || $currencycode=='HKD' || $currencycode=='NZD' || $currencycode=='SGD'){?>
							  
							  $<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='JPY'){ ?>
							  ¥<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='EUR'){ ?>
							  €<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='GBP'){ ?>
							  £<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='DKK' || $currencycode=='NOK' || $currencycode=='SEK'){ ?>
							  kr<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='CHF'){ ?>
							  CHF<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='BRL'){ ?>
							  R$<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='PHP'){ ?>
							  ₱<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php } else if($currencycode=='ILS'){ ?>
							  ₪<?php echo $statistic['overAllCredit'];?></b><br/>
							  <?php }?>
					
					Unpaid Commissions: <b>
					<?php
					 if($currencycode=='MXN' || $currencycode=='USD' || $currencycode=='AUD' || $currencycode=='CAD' || $currencycode=='HKD' || $currencycode=='NZD' || $currencycode=='SGD'){?>             
							  $<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='JPY'){ ?>
							  ¥<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='EUR'){ ?>
							  €<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='GBP'){ ?>
							  £<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='DKK' || $currencycode=='NOK' || $currencycode=='SEK'){ ?>
							  kr<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='CHF'){ ?>
							  CHF<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='BRL'){ ?>
							  R$<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='PHP'){ ?>
							  ₱<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php } else if($currencycode=='ILS'){ ?>
							  ₪<?php echo $statistic['unPaidCommision'];?></b><br/><br/>
							  <?php }?>
				<?php if($loggedUser['User']['email_alert_credit_options']==0 && $loggedUser['User']['sms_balance'] <= $loggedUser['User']['low_sms_balances']){?>		
						SMS Credits: <b style="color:red;"> <?php echo $loggedUser['User']['sms_balance']?></b><br>		
						<?php }else{?>
						SMS Credits:	<b><?php echo $loggedUser['User']['sms_balance']?></b><br>
						<?php } ?>
						<?php if($loggedUser['User']['email_alert_credit_options']==0 && $loggedUser['User']['voice_balance'] <= $loggedUser['User']['low_voice_balances']){?>		
						Voice Credits: <b style="color:red;"> <?php echo $loggedUser['User']['voice_balance']?></b><br>		
						<?php }else{?>
							Voice Credits:	<b><?php echo $loggedUser['User']['voice_balance']?></b><br>
						<?php } ?>
						<br/>
						Next Renewal Date: 
						<?php 
						$date=strtotime($loggedUser['User']['next_renewal_dates']); 
						list($year, $month, $day) = explode('-', $loggedUser['User']['next_renewal_dates']); 
						if (checkdate($month,$day,$year)){?>			
						(<?php echo $datereplace=date('Y-m-d',$date);?>)
						<?php } else { ?>
						<?php echo "(<font style='color: green'><b>None</b></font>)"; }?>			
						<?php ?>
						<br/>		
						Past Receipts: (<b><?php echo $this->Html->link('Past Receipts',array('controller' =>'users', 'action' =>'invoices'), array('class' => 'nyroModal', 'style' => 'color:#12759E;'));?></b>)
						<br/>
						<br/>
					<?php 
							 $payment=PAYMENT_GATEWAY;			
							if($payment=='1'){?>			
							Purchase Credits <?php echo $this->Html->link(__('PayPal', true), array('controller' =>'users', 'action' =>'paypalpayment'),array('class' => 'paypalpayment'))?><br />
							<?php }else if($payment=='2'){ ?>			
							Purchase Credits <?php echo $this->Html->link('2Checkout', array('controller' =>'users', 'action' =>'checkoutpayment'),array('class' => 'checkoutpayment'))?><br />
							<?php }else if($payment=='3'){ ?>
							Purchase Credits <?php echo $this->Html->link('PayPal', array('controller' =>'users', 'action' =>'paypalpayment'),array('class' => 'paypalpayment'))?> or <?php echo $this->Html->link('2Checkout', array('controller' =>'users', 'action' =>'checkoutpayment'),array('class' => 'checkoutpayment'))?><br />
							<?php } ?>
				<br/>
				<font color="red">ATTENTION:</font> Web developers, utilize our incredibly simple 
						<b><?php echo $this->Html->link(__('PHP API', true), array('controller' => 'twilios','action' => 'apibox', $loggedUser['User']['id']), array('class' => 'nyroModal', 'style' => 'color:#12759E;')); ?>	</b>
				</div-->
           <?php endif;?>