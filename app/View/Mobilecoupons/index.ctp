<script>
    function copyFunction(couponid) {
  /* Get the text field */
  var trigger = $(this);
  var copyText = document.getElementById(couponid);
  var successid = "#alert" + couponid;

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  //alert("Copied the text: " + copyText.value);
  $(trigger).addClass("text-success");
  $(successid).show();
  setTimeout(function() { $(successid).hide(); }, 2000);
} 
</script>
<style>
   .key-input-btn {
   display: flex;
   width: 100%;
   }
   .key-input-btn .form-control {
   border-radius: 4px 0px 0px 4px !important;
   }
   .key-input-btn .btn {
   border-radius: 0px 4px 4px 0px !important;
   }
   .key-input-btn .btn.clsCopyBtn{
      background-color: #667884;
      border-color: #667884;
   }
   .alert.cs-alert-success{
      background-color: #d8f5da;
      border-color: #a3ef9a;
      color: #11c505;
      border-radius: 4px 0px 0px 4px !important;
   }
</style>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo('Mobile Coupons');?></h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<span><?php echo('Mobile Coupons');?></span>
				</li>
			</ul>  
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
					<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li>
							<a href="<?php echo SITE_URL;?>/mobilecoupons/add" title="Add Mobile Coupons"><i class="fa fa-plus-square-o"></i> Create Mobile Coupon</a>
						</li>
					</ul>
				</div>
			</div>				
		</div>
		<?php echo $this->Session->flash(); ?>				
		<div class="clearfix"></div>
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-gift font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Mobile Coupons');?></span> </div>
			</div>
			<div class="portlet-body">
					<table  id="datatable_Mobilecoupons" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>
								<th scope="col">Name</th>
								<!--<th scope="col">1 Redemption/Person</th>-->
								<th scope="col">Link</th>
								<th scope="col">Created</th>
								<th scope="col">Action </th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($mobilecoupon_arr as $mobilecoupon_arr_arrs){ ?>
								<tr>
									<td class="tc"><?php echo $mobilecoupon_arr_arrs['MobileCoupon']['name'];?></td>
								
									<!--<td class="tc"><?php echo SITE_URL;?>/coupons/<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['unique_id']; ?></td>-->
									<td class="tc" width="55%">
									<!--<input width="100%" type'text' id="<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['id']?>" value="<?php echo SITE_URL;?>/coupons/<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['unique_id']; ?>">-->
                                        <div class="key-input-btn">
                                            <input type="text" class='form-control' readonly=readonly id="<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['id']?>" value="<?php echo SITE_URL;?>/coupons/<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['unique_id']; ?>"><button class="btn grey-salt clsCopyBtn" onclick="copyFunction(<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['id']?>)">
                                            <i class="fa fa-copy"></i>
                                            Copy
                                            </button>
                                        </div>
                                        <div class="alert alert-info cs-alert-success" id="alert<?php echo $mobilecoupon_arr_arrs['MobileCoupon']['id']?>" style="display:none">Coupon URL Copied</div>
                                    </td>
									<td class="tc"><?php echo date('Y-m-d H:i:s',strtotime($mobilecoupon_arr_arrs['MobileCoupon']['created'])) ?></td>
									<td class="tc">
									    <?php echo $this->Html->link('<i class="fa fa-line-chart" style="font-size:14px"></i>',array('action'=>'statistics',$mobilecoupon_arr_arrs['MobileCoupon']['id']),array('class'=> 'btn blue btn-sm','escape'=> false,'title'=>'Statistics','style'=>'margin-right:0px')); ?>
									    <?php echo $this->Html->link('<i class="fa fa-copy" style="font-size:14px"></i>',array('action'=>'copy_coupons',$mobilecoupon_arr_arrs['MobileCoupon']['id']),array('class'=> 'btn yellow btn-sm','escape'=> false,'title'=>'Clone & Create','style'=>'margin-right:0px')); ?>
										<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$mobilecoupon_arr_arrs['MobileCoupon']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>
										<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$mobilecoupon_arr_arrs['MobileCoupon']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this Mobile Coupon?', true))); ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
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
		