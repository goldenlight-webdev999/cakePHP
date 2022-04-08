	<div class="page-content-wrapper">
		<div class="page-content">              
			<h3 class="page-title"><?php  echo ('Mobile Coupon Statistics');?> - <?php echo $mobilecoupons['MobileCoupon']['name'];?></h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
						<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="<?php echo SITE_URL;?>/mobilecoupons">Mobile Coupons</a>
					</li>
				</ul>  
				<div class="page-toolbar">
					<div class="btn-group pull-right">
						<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
						<i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li>
								<a href="<?php echo SITE_URL;?>/mobilecoupons" title="Back"><i class="fa fa-arrow-left"></i> Back</a>
								
							</li>
						</ul>
					</div>
				</div>				
			</div>
			<?php echo $this->Session->flash(); ?>
			<div class="portlet box green-meadow">		
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-money"></i>
						<?php  echo ('Coupon Redemptions');?>
					</div>
					<div class="pull-right">
				    	<a onclick="if (confirm(&quot;Are you sure you want to reset redemptions back to 0?&quot;)) { return true; } return false;" href="<?php echo SITE_URL;?>/mobilecoupons/reset_redemptions/<?php echo $mobilecoupons['MobileCoupon']['id'];?>" class="btn default red-stripe" title="Reset Redemptions" style="margin-top: 4px;"><i class="fa fa-refresh"></i> Reset</a>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-striped">
								<tbody>
									<tr>												
										<th style="width:15%"><font style="font-size: 18px">Total Redemptions</font></th>
										<td style="width:35%">
											<span class="text-muted">												
												<font style="font-size: 18px;color:#1BBC9B;font-weight:bold"><?php echo $mobilecoupons['MobileCoupon']['total_redemption'];?></font>
											</span>
										</td>
									</tr>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
			<div class="portlet box purple-plum">		
				<div class="portlet-title">
						<div class="caption">
						<i class="fa fa-eye"></i>
						<?php  echo ('Coupon Views');?>
					</div>
					<div class="pull-right">
				    	<a onclick="if (confirm(&quot;Are you sure you want to reset views back to 0?&quot;)) { return true; } return false;" href="<?php echo SITE_URL;?>/mobilecoupons/reset_views/<?php echo $mobilecoupons['MobileCoupon']['id'];?>" class="btn default red-stripe" title="Reset Views" style="margin-top: 4px;"><i class="fa fa-refresh"></i> Reset</a>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-striped">
								<tbody>
									<tr>												
										<th style="width:15%">Total Views</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $totalview;?>
											</span>
										</td>
									</tr>
									<tr>
									    <th style="width:15%">Unique Views</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php if(!empty($uniquetotalview)){ echo $uniquetotalview; }else{ echo "0"; } ?>
											</span>
										</td>
									</tr>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
			<div class="portlet box yellow-mint">		
				<div class="portlet-title">
						<div class="caption">
						<i class="fa fa-thumbs-up"></i>
						<?php  echo ('Coupon Votes');?>
					</div>
					<div class="pull-right">
				    	<a onclick="if (confirm(&quot;Are you sure you want to reset votes back to 0?&quot;)) { return true; } return false;" href="<?php echo SITE_URL;?>/mobilecoupons/reset_votes/<?php echo $mobilecoupons['MobileCoupon']['id'];?>" class="btn default red-stripe" title="Reset Votes" style="margin-top: 4px;"><i class="fa fa-refresh"></i> Reset</a>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-striped">
								<tbody>
									<tr>												
										<th style="width:15%">Up Votes</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $mobilecoupons['MobileCoupon']['up_votes'];?>
											</span>
										</td>
									</tr>
									<tr>
									    <th style="width:15%">Down Votes</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $mobilecoupons['MobileCoupon']['down_votes'];?>
											</span>
										</td>
									</tr>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		
		
						