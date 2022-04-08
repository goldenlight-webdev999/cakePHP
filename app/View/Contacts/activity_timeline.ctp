<?php
	echo $this->Html->css('activitytimeline');
?>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Activity Timeline</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
									<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<span>Activity Timeline</span>
					</li>
				</ul>  
					<div class="page-toolbar">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">Actions <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li>
									 <a href="<?php echo SITE_URL;?>/contacts/index" title="Back"><i class=""></i> Back  </a>
								</li>			
                                    
                                </ul>
                            </div>
                    </div>			
			</div>
				<?php echo $this->Session->flash(); ?>				
			<div class="clearfix"></div>
				<div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
						<div class="caption">
							<i class="icon-list font-green"></i>
<span class="caption-subject bold font-green uppercase"> Activity Timeline                                            
</span>
<span class="caption-helper">Incoming SMS Contact Activity Timeline</span>
						</div>
<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>
					</div>
					<div class="portlet-body">
                                <div class="mt-timeline-2">
                                    <div class="mt-timeline-line border-grey-steel"></div>
                                    <ul class="mt-container">
                                    <?php 
					$i = 0;
					foreach ($timeline as $contact_timeline):
					$class = '';
					if ($i++ % 2 == 0) {
					    $class = ' bg-white border-grey-steel';
					}
				    ?>
                                        <li class="mt-item">
                                        <?php if($contact_timeline['ActivityTimeline']['activity'] == 1 || $contact_timeline['ActivityTimeline']['activity'] == 15) { 
                                            $buttoncolor = 'green-jungle';?>
                                            <div class="mt-timeline-icon bg-green-jungle bg-font-green-jungle border-grey-steel">
                                                <i class="icon-user-follow"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 2) { 
                                            $buttoncolor = 'blue';?> 
                                            <div class="mt-timeline-icon bg-blue bg-font-blue border-grey-steel">
                                                <i class="icon-list"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 3) { 
                                            $buttoncolor = 'green-turquoise';?>
                                            <div class="mt-timeline-icon bg-green-turquoise bg-font-green-turquoise border-grey-steel">
                                                <i class="icon-trophy"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 5) {  
                                            $buttoncolor = 'blue-steel';?>
                                            <div class="mt-timeline-icon bg-blue-steel bg-font-blue-steel border-grey-steel">
                                                <i class="icon-target"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 6) {  
                                            $buttoncolor = 'blue-hoki';?>
                                            <div class="mt-timeline-icon bg-blue-hoki bg-font-blue-hoki border-grey-steel">
                                                <i class="icon-check"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 7) {  
                                            $buttoncolor = 'blue-chambray';?>
                                            <div class="mt-timeline-icon bg-blue-chambray bg-font-blue-chambray border-grey-steel">
                                                <i class="icon-badge"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 8) {  
                                            $buttoncolor = 'yellow-gold';?>
                                            <div class="mt-timeline-icon bg-yellow-gold bg-font-yellow-gold border-grey-steel">
                                                <i class="icon-star"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 9) {  
                                            $buttoncolor = 'green';?>
                                            <div class="mt-timeline-icon bg-green bg-font-green border-grey-steel">
                                                <i class="icon-wallet"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 10) {  
                                            $buttoncolor = 'yellow';?>
                                            <div class="mt-timeline-icon bg-yellow bg-font-yellow border-grey-steel">
                                                <i class=" icon-bubbles"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 11) {  
                                            $buttoncolor = 'red-haze';?>
                                            <div class="mt-timeline-icon bg-red-haze bg-font-red-haze border-grey-steel">
                                                <i class="icon-tag"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 12) {  
                                            $buttoncolor = 'purple-sharp';?>
                                            <div class="mt-timeline-icon bg-purple-sharp bg-font-purple-sharp border-grey-steel">
                                                <i class="icon-envelope-letter"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 13) {  
                                            $buttoncolor = 'yellow-mint';?>
                                            <div class="mt-timeline-icon bg-yellow-mint bg-font-yellow-mint border-grey-steel">
                                                <i class="icon-present"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 14) {  
                                            $buttoncolor = 'red';?>
                                            <div class="mt-timeline-icon bg-red bg-font-red border-grey-steel">
                                                <i class="icon-user-unfollow"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 16) {  
                                            $buttoncolor = 'purple-medium';?>
                                            <div class="mt-timeline-icon bg-purple-medium bg-font-purple-medium border-grey-steel">
                                                <i class="icon-earphones-alt"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 17) {  
                                            $buttoncolor = 'green-haze';?>
                                            <div class="mt-timeline-icon bg-green-haze bg-font-green-haze border-grey-steel">
                                                <i class="icon-clock"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 18) {  
                                            $buttoncolor = 'red-thunderbird';?>
                                            <div class="mt-timeline-icon bg-red-thunderbird bg-font-red-thunderbird border-grey-steel">
                                                <i class="icon-close"></i>
                                            </div>
                                        <?php } else if ($contact_timeline['ActivityTimeline']['activity'] == 19) {  
                                            $buttoncolor = 'yellow-lemon';?>
                                            <div class="mt-timeline-icon bg-yellow-lemon bg-font-yellow-lemon border-grey-steel">
                                                <i class="icon-plus"></i>
                                            </div>
                                        <?php } ?>

                                            <div class="mt-timeline-content">
                                                <div class="mt-content-container <?php echo $class;?>">
                                                    <div class="mt-title">
                                                        <h3 class="mt-content-title"><?php echo $contact_timeline['ActivityTimeline']['title'];?></h3>
                                                    </div>
                                                    <div class="mt-author">
                                                        <div class="mt-avatar">
                                                            <!--<img src="../assets/pages/media/users/avatar80_2.jpg">-->
                                              <?php if($contact_timeline['Contact']['email'] !=''){?> 																					                                      <?php
																						                                     $url = 'https://www.gravatar.com/avatar/' . md5( strtolower( trim ( $contact_timeline['Contact']['email'] ) ) ) . '?d=404';
																						                                     $headers = @get_headers( $url );
																						                      if(preg_match( '|200|', $headers[0])){																			                                     echo '<img src="'.$url.'">';																					                                          																                                  }else{
																							                                    echo '<span class="btn btn-circle btn-icon-only '.$buttoncolor.'">
                                                <i class="icon-user"></i>
                                            </span>';	
																						                                          }
																					              ?>
																				<?php               }else{ ?>
																					<span class="btn btn-circle btn-icon-only <?php echo $buttoncolor;?>">
                                                <i class="icon-user"></i>
                                            </span>
																				<?php } ?>
                                                        </div>
                                                        <div class="mt-author-name">
                                                            <a href="javascript:;" class="font-blue-madison">
                                                            <?php
                                                            if(trim($contact_timeline['Contact']['name']) !=''){  
                                                               echo $contact_timeline['Contact']['name'];
                                                            }else{
                                                               echo $contact_timeline['Contact']['phone_number'];
                                                            }
                                                            ?>
                                                            </a>
                                                        </div>
                                                        <div class="mt-author-notes font-grey-mint"><?php echo $contact_timeline['ActivityTimeline']['created'];?></div>
                                                    </div>
                                                    <div class="mt-content border-grey-salt">
                                                        <p><?php echo $contact_timeline['ActivityTimeline']['description'];?></p>
                                                        
                                                        
                                                            <?php if ($unsub == 0)  {?>       
                                                            <div class="btn-group pull-right">
                                                            <button class="btn btn-circle <?php echo $buttoncolor?> dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Actions
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                <li>

<?php if($userperm['sendsms']=='1'){ ?>
                                                                <?php if(API_TYPE==0){?>
								<a href="<?php echo SITE_URL;?>/contacts/send_sms/<?php echo $contact_timeline['Contact']['phone_number']?>" class="nyroModal"><i class="icon-bubble"></i> Send SMS </a>

								<?php }else if(API_TYPE==1){ ?>
								
<a href="<?php echo SITE_URL;?>/contacts/nexmo_send_sms/<?php echo $contact_timeline['Contact']['phone_number']?>" class="nyroModal"><i class="icon-bubble"></i> Send SMS </a>
								<?php }else if(API_TYPE==2){ ?>
								
<a href="<?php echo SITE_URL;?>/contacts/slooce_send_sms/<?php echo $contact_timeline['Contact']['phone_number']?>" class="nyroModal"><i class="icon-bubble"></i> Send SMS </a>
								<?php }else if(API_TYPE==3){ ?>
									
<a href="<?php echo SITE_URL;?>/contacts/plivo_send_sms/<?php echo $contact_timeline['Contact']['phone_number']?>" class="nyroModal"><i class="icon-bubble"></i> Send SMS </a>

								<?php }} ?>
                                                              </li>
                                                              <li>
                                                              <a href="<?php echo SITE_URL;?>/chats/"><i class="icon-bubbles"></i> SMS Chat </a>
                                                              </li>
                                                              <li>
                                                                    <a class="nyroModal" href="<?php echo SITE_URL;?>/contacts/edit/<?php echo $contact_timeline['ActivityTimeline']['contact_id']?>"><i class="icon-note"></i> Edit </a>
                                                              </li>
                                                              </ul>
                                                              </div>
                                                            <?} ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                      <?php endforeach; ?>
            

                                    </ul>
                                </div>
                            </div>
</div>