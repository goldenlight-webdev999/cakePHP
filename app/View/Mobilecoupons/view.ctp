<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		<link rel='stylesheet' href='<?php echo SITE_URL;?>/couponsassets/mobilestyle.min.css' type='text/css' />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?php  echo SITE_URL ?>/couponsassets/mobilejquery.min.js"></script>
		<script type="text/javascript" src="<?php echo SITE_URL ?>/couponsassets/mobilescript.min.js"></script>
		<style type="text/css">
			#mobileCoupon #header.themecolor, #mobileCoupon .button.themecolor{
				background-color:<?php echo $mobilecoupons['MobileCoupon']['coupon_colorway'];?>;
			}
			#mobileCoupon #offer.themecolor,	#mobileCoupon #redeemAction.themecolor, 	#mobileCoupon #couponStatus.redeemed span{
				color:<?php echo $mobilecoupons['MobileCoupon']['coupon_colorway'];?>;	
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
		<script type="text/javascript">
		    function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            function getCookie(cname) {
				var name = cname + "=";
				var decodedCookie = decodeURIComponent(document.cookie);
				var ca = decodedCookie.split(';');
				for(var i = 0; i <ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					if (c.indexOf(name) == 0) {
						return c.substring(name.length, c.length);
					}
				}
				return "";
            }
            
			var mobilecouponHash = '<?php echo $unique_id; ?>';
    		var settingOnePerPerson = <?php echo $mobilecoupons['MobileCoupon']['one_per_person'];?>;
			<?php if($mobilecoupons['MobileCoupon']['redemption_type']==0){ ?>
			  var settingRedemptionType = 'standard';
			<?php }else{ ?>
			    var settingRedemptionType = 'barcode';
			<?php } ?>
			var settingRedemptionCode = '<?php echo $mobilecoupons['MobileCoupon']['redemption_code'];?>';
			<?php if($mobilecoupons['MobileCoupon']['coupon_expiration']==0){ ?>
				var settingExpirationDate = '<?php echo date("m/d/Y H:i:s",strtotime($mobilecoupons['MobileCoupon']['expiration_date']));?>';
				var previewMode = false;
			<?php }else{ ?>
				//var settingExpirationDate_first = getCookie('settingExpirationDate');
				var settingExpirationDate_first = getCookie('settingExpirationDate_' + mobilecouponHash);
				if(settingExpirationDate_first !=''){
				    var settingExpirationDate = settingExpirationDate_first;
				}else{
				    //setCookie('settingExpirationDate','<?php echo date('m/d/Y H:i:s', strtotime(' + '.$mobilecoupons['MobileCoupon']['dynamic_days'].' days'));?>','<?php echo $mobilecoupons['MobileCoupon']['dynamic_days'];?>');
				    setCookie('settingExpirationDate_' + mobilecouponHash,'<?php echo date('m/d/Y H:i:s', strtotime(' + '.$mobilecoupons['MobileCoupon']['dynamic_days'].' days'));?>','<?php echo $mobilecoupons['MobileCoupon']['dynamic_days'];?>');
				    var settingExpirationDate = '<?php echo date('m/d/Y H:i:s', strtotime(' + '.$mobilecoupons['MobileCoupon']['dynamic_days'].' days'));?>';   
				}
				var previewMode = false;
			<?php } ?>
			var settingRedemptionTimeout = '';
			var settingRedemptionReset = <?php echo $mobilecoupons['MobileCoupon']['redemption_reset'];?>;
			var eventScriptPath_URL = "<?php echo SITE_URL ?>/mobilecoupons/mobile_coupon_analytics";
		</script>
		<title><?php echo ucfirst($mobilecoupons['MobileCoupon']['coupon_header']);?></title>
	</head>
	<body id="mobileCoupon">
		<div id="container">
			<div id="header" class="themecolor" style="font-size:18px !important"><?php echo ucfirst($mobilecoupons['MobileCoupon']['coupon_header']);?></div>
			<div id="content">
				<div id="redemptionContainer">
					<h1 id="redeemAction" class="themecolor"></h1>
					<h2 id="redeemCode">{{redeemCode}}</h2>
					<div id="redeemQRcodeContainer">
                    	<img id="qrcode" src="<?php echo $qrimage; ?>" />
                        <div id="barcodeCode">{{redeemCode}}</div>
                    </div>
					<a id="btnClose" href="javascript:void(0)" class="button large teal themecolor">Complete Redemption</a>
				</div>
				<div id="couponContainer">
				    
				    <?php if($mobilecoupons['MobileCoupon']['offer_image'] !=''){ ?>
				    	<img id="heroImage" src="<?php echo SITE_URL;?>/mobile_coupons/<?php echo $mobilecoupons['MobileCoupon']['offer_image'];?>" />
					<?php } ?>
					
					<h1 id="offer" class="stitched themecolor"><?php echo ucfirst($mobilecoupons['MobileCoupon']['offer_name']);?></h1>
    				<p id="offerDescription"><?php echo ucfirst($mobilecoupons['MobileCoupon']['offer_description']);?></p>
					<h3 id="couponStatus"></h3>
					<div id="expirationTimer">
						<div id="days" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 44px !important;"></span>
							<span id="daysText" class="label">Days</span>
						</div>
						<div id="hours" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 44px !important;"></span>
							<span id="hoursText" class="label">Hours</span>
						</div>
						<div id="minutes" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 44px !important;"></span>
							<span id="minutesText" class="label">Minutes</span>
						</div>
						<div id="seconds" class="section">
							<span class="count"  style="color:red;font-family:'Custom';font-size: 44px !important;"></span>
							<span id="secondsText" class="label">Seconds</span>
						</div>
					</div>
	
					<div id="businessContainer">
						<?php if($mobilecoupons['MobileCoupon']['showCallLink']==1){ 
						      if(trim($mobilecoupons['MobileCoupon']['call_number'])==''){?>
						            <a id="call" href="" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/call.png" /><span>Call</span></a>
						       <?php }else { ?> 
							        <a id="call" href="tel:<?php echo $mobilecoupons['MobileCoupon']['call_number'];?>" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/call.png" /><span>Call</span></a>
							   <?php } ?> 
						<?php } ?>
						<?php if($mobilecoupons['MobileCoupon']['showCallLink']==1){ ?>	
							<a id="website" href="<?php echo $mobilecoupons['MobileCoupon']['website'];?>" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/website.png" /><span>Website</span></a>
						<?php } ?>
						<?php if($mobilecoupons['MobileCoupon']['showDirectionsLink']==1){ ?>	
							<a id="directions" href="<?php echo $mobilecoupons['MobileCoupon']['directions'];?>" target="_blank"><img src="<?php echo SITE_URL;?>/couponsassets/images/directions.png" /><span>Directions</span></a>
						<?php } ?>
					</div>
					<?php if($mobilecoupons['MobileCoupon']['showVoteContainer']==1){ ?>
    					<div id="voteContainer">
    					    <h3 style="font-size:13px;padding-bottom:8px">LOVE or HATE this offer? Let us know.</h3>
    						<a href="javascript:void(0)" id="voteUp" class="button small grey"><img src="<?php echo SITE_URL;?>/couponsassets/images/happyface.png" /></a>
    						<a href="javascript:void(0)" id="voteDown" class="button small grey"><img src="<?php echo SITE_URL;?>/couponsassets/images/angryface.png" /></a>
    					</div>
					<?php } ?>
					<a id="btnRedeem" href="javascript:void(0)" class="button large teal themecolor"><?php echo ucfirst($mobilecoupons['MobileCoupon']['redeem_button_text']);?></a>
					<div id="socialContainer">
					    <?php if($mobilecoupons['MobileCoupon']['showFacebookLink']==1){ ?>	
							<a id="facebook" href="<?php echo $mobilecoupons['MobileCoupon']['facebook'];?>" target="_blank" style="margin: 10px 2%"><img style="height:42px !important" src="<?php echo SITE_URL;?>/couponsassets/images/facebook.png" /></a>
						<?php } ?>
						<?php if($mobilecoupons['MobileCoupon']['showInstagramLink']==1){ ?>	
							<a id="instagram" href="<?php echo $mobilecoupons['MobileCoupon']['instagram'];?>" target="_blank" style="margin: 10px 2%"><img style="height:42px !important" src="<?php echo SITE_URL;?>/couponsassets/images/instagram.png" /></a>
						<?php } ?>
					</div>
					<p id="terms"><?php echo $mobilecoupons['MobileCoupon']['fine_print'];?></p>
				</div>
			</div>
		</div>
	</body>
</html>