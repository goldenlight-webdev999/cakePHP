<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
	</head>
	<?php echo $this->Session->flash(); ?>
    <div style="padding: 7px; border: solid 1px #cde4a2; background: #e8fcc2; text-align:center;">
        <h2>REDEEMED</h2>
    </div>
    <div>
    	<p style="font-size:20px;"><b>Mobile Coupon:</b> <?php echo $mobilecoupons['MobileCoupon']['name'];?></p>
    	<p style="font-size:20px;"><b>Total Redemptions:</b> <?php echo $mobilecoupons['MobileCoupon']['total_redemption'];?></p>
    	<p style="font-size:20px;"><b>Redemption Code:</b> <?php echo $mobilecoupons['MobileCoupon']['redemption_code'];?></p>
	</div>
</html>
