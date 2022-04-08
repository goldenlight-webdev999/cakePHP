
<div class="page-content-wrapper">
		<div class="page-content">              
			<h3 class="page-title"><?php echo('Fax');?></h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<span><?php echo('Fax');?></span>
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
								if($fax=='faxinbox'){
								    $navigation = array(
								    'Back' => '/logs/index/faxinbox',
								    );
								}else{
								    $navigation = array(
								    'Back' => '/logs/index/faxoutbox',
								    );
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
						<i class="fa fa-fax font-red"></i>
						<span class="caption-subject font-red sbold uppercase"> <?php echo('View Fax');?></span>
					</div>
					
<!--<div class="tools">
<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>
<a class="fullscreen" href="javascript:;" data-original-title="" title=""> </a>
<a class="remove" href="javascript:;" data-original-title="" title=""> </a>
</div>
-->
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
                                <!--<iframe id="iframeID" name="iframeID" src="https://docs.google.com/gview?url=<?php echo $faxurl?>&embedded=true" style="width:100%; height:800px;" frameborder="0"></iframe>   -->
                                <iframe id="iframeID" name="iframeID" src="<?php echo $faxurl?>" style="width:100%; height:800px;" frameborder="0"></iframe>  
						</div>
                    </div>
                </div>
            </div>
            
