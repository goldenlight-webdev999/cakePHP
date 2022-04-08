<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		<link rel='stylesheet' href='<?php echo SITE_URL;?>/couponsassets/mobilestyle.min.css' type='text/css' />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?php  echo SITE_URL ?>/couponsassets/mobilejquery.min.js"></script>
		<style type="text/css">
			#mobileCoupon #header.themecolor, #mobileCoupon .button.themecolor{
				background-color:#0488d3;
			}
			#mobileCoupon #offer.themecolor,	#mobileCoupon #redeemAction.themecolor, 	#mobileCoupon #couponStatus.redeemed span{
				color:#0488d3;	
			}
			@font-face {
              font-family: 'Custom';
              font-style: normal;
              font-weight: 300;
              src: url('<?php echo SITE_URL ?>/fonts/DS-DIGI.TTF') format('truetype');
           }
           
           .stitched {
               padding: 15px !important;
               margin: 3px !important;
               background: red !important;
               color: #fff !important;
               font-size: 18px !important;
               font-weight: bold;
               line-height: 1.3em;
               border: 2px dashed #fff !important;
               border-radius: 0px !important;
               box-shadow: 0 0 0 4px #ff0030, 2px 1px 6px 4px rgba(10, 10, 0, 0.5) !important;
               text-shadow: -1px -1px #aa3030 !important;
               font-weight: normal;
        }
		</style>
		<title>Coupon</title>
	</head>
	<body id="mobileCoupon">
		<div id="container">
			<div id="header" class="themecolor" style="font-size:18px !important">Header Text</div>
			<div id="content">
				<div id="redemptionContainer">
					<h1 id="redeemAction" class="themecolor"></h1>
					<h2 id="redeemCode">{{redeemCode}}</h2>
					<a id="btnClose" href="javascript:void(0)" class="button large teal themecolor">Complete Redemption</a>
				</div>
				<div id="couponContainer">
				    <img id="heroImage" src="<?php echo SITE_URL;?>/couponsassets/images/image_placeholder.jpg" />
					<h1 id="offer" class="stitched themecolor">Offer Here</h1>
    				<p id="offerDescription">Set the coupon offer description here. This should provide a little more detail on your offer.</p>
					<h3 id="couponStatus">EXPIRES IN...</h3>
					<div id="expirationTimer">
						<div id="days" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 40px !important;"></span>
							<span id="daysText" class="label">Days</span>
						</div>
						<div id="hours" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 40px !important;"></span>
							<span id="hoursText" class="label">Hours</span>
						</div>
						<div id="minutes" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 40px !important;"></span>
							<span id="minutesText" class="label">Minutes</span>
						</div>
						<div id="seconds" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 40px !important;"></span>
							<span id="secondsText" class="label">Seconds</span>
						</div>
					</div>
					<div id="businessContainer">
						<a id="call" href="tel:01234567890" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/call.png" /><span>Call</span></a>
						<a id="website" href="<?php echo SITE_URL;?>" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/website.png" /><span>Website</span></a>
						<a id="directions" href="http://google.com" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/directions.png" /><span>Directions</span></a>
					</div>
					<div id="voteContainer" style="display:none;">
						<h3 style="font-size:13px;padding-bottom:8px">LOVE or HATE this offer? Let us know.</h3>
						<a href="javascript:void(0)" id="voteUp" class="button small grey"><img src="<?php echo SITE_URL;?>/couponsassets/images/happyface.png" /></a>
						<a href="javascript:void(0)" id="voteDown" class="button small grey"><img src="<?php echo SITE_URL;?>/couponsassets/images/angryface.png" /></a>
					</div>
					<a id="btnRedeem" href="javascript:void(0)" class="button large teal themecolor">Redeem Button Text</a>
					<div id="socialContainer">
						<a id="facebook" href="http://facebook.com" target="_blank" style="display:none;margin: 10px 2%"><img style="height:42px !important" src="<?php echo SITE_URL;?>/couponsassets/images/facebook.png" /></a>
						<a id="instagram" href="http://instagram.com" target="_blank" style="display:none;margin: 10px 2%"><img style="height:42px !important" src="<?php echo SITE_URL;?>/couponsassets/images/instagram.png" /></a>
					</div>
					<p id="terms"></p>
				</div>
			</div>
		</div>
	</body>
</html>