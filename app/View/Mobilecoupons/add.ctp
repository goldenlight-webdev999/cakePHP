<script src="<?php echo SITE_URL;?>/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" type="text/javascript" ></script>
<link href="<?php echo SITE_URL;?>/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
 <style>
 .ValidationErrors{
 color:red;
	}
	
input:focus {
  background-color: #edf6ff;
}
textarea:focus {
  background-color: #edf6ff;
}
#right {
    background-image: url(<?php echo SITE_URL;?>/couponsassets/phone.png);
    background-repeat: no-repeat;
    background-position: top right;
    width: 375px;
    min-height: 660px;
}
#right #mobileCoupon {
    height: 428px;
    width: 304px;
}
#right #mobileCoupon #mobileCouponPreview {
    width: 100%;
    height: 466px;
    overflow: scroll;
    overflow-x: hidden;
    overflow-y: scroll;
	margin: 88px 0 0 45px;
	border: none;
}
</style>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo ('Mobile Coupons');?></h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo SITE_URL;?>/mobilecoupons"><?php echo ('Mobile Coupons');?></a>
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
		<div class="portlet mt-element-ribbon light portlet-fit  ">
			<div class="ribbon ribbon-right ribbon-clip ribbon-shadow ribbon-border-dash-hor ribbon-color-success uppercase">
				<div class="ribbon-sub ribbon-clip ribbon-right"></div>
				Create Mobile Coupons Form
			</div>
			<div class="portlet-title">
				<div class="caption">
				<i class="fa fa-gift font-red"></i> </div>
			</div>
			<div class="portlet-body form">
			<?php echo $this->Session->flash(); ?>	
				<form  method="post" class="forma" enctype="multipart/form-data">
					<div class="form-body">	
						<div class="row">
						<div class="col-sm-7">
                            <ul class="nav nav-tabs">
								<li class="active">
								   <a href="#tab_1_1" data-toggle="tab" aria-expanded="false"> Coupon Information </a>
								</li>
								<li class="">
									<a href="#tab_1_2" data-toggle="tab" aria-expanded="true"> Coupon Content </a>
								</li>
								<li class="">
									<a href="#tab_1_3" data-toggle="tab" aria-expanded="true"> Business Information</a>
								</li>
								<li class="">
									<a href="#tab_1_4" data-toggle="tab" aria-expanded="true"> Redemption Settings</a>
								</li>
									   
							</ul>
								
                            <div class="tab-content">
                              <div class="tab-pane fade active in" id="tab_1_1">
    							<div class="portlet light bordered">
    								<div class="portlet-body"> 
    									<div class="form-group ">
    										<label>Coupon Name </label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Give your coupon a name. Customers will not see this as it is for reference only." data-original-title="Coupon Name" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<input type="text" id="coupon_name" name="data[MobileCoupon][name]" class="form-control" placeholder="Coupon Name" value="">
    									</div>
    									<div class="form-group ">
    										<label>Coupon Description</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Give your coupon a description. Customers will not see this as it is for reference only." data-original-title="Coupon Description" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<textarea id="coupon_description" maxlength="1600" name="data[MobileCoupon][description]" cols="5" rows="3" class="form-control" placeholder="Description"></textarea>
    									</div>
    									<div class="form-group ">
    										<label>Coupon Theme Color</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set your main coupon theme color. The color will be reflected on the header and redeem button." data-original-title="Coupon Theme Color" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<input type="color" id="coupon_colorway" name="data[MobileCoupon][coupon_colorway]" class="form-control" placeholder="Coupon Theme Color" value="#0488d3" onchange="return colorreflect(this.value);">
    									</div>
    									<div class="form-group ">
    										<label>Allow Only 1 Redemption Per Person?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="You can restrict the # of redemptions per person. If turned on and the person has redeemed, it will state this on the coupon even if they reload it.<br /><br />However, if you do have the 1 redemption per person restriction turned on, you can set it so that it is available in the future to be redeemed again." data-original-title="Redemptions Per Person" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<select class="form-control" name="data[MobileCoupon][one_per_person]" onchange ="return one_per_person(this.value)">
    											<option value="0">No</option>
    											<option value="1">Yes...</option>
    										</select>
    									</div>
    									<div class="form-group" id="one_per_person_div" style="display:none;">
    										<select id="redemptionReset" class="form-control" name="data[MobileCoupon][redemption_reset]">
    											<option value="0" selected="selected">...and can never be redeemed again.</option>
    											<option value="1">...but can be redeemed again after 1 day.</option>
    											<option value="7">...but can be redeemed again after 1 week.</option>
    											<option value="30">...but can be redeemed again after 1 month.</option>
    											<option value="365">...but can be redeemed again after 1 year.</option>
    										</select>
    									</div>
    									<div class="form-group ">
    										<label>Coupon Expiration</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Choose whether you want to set a hard coded date for coupon expiration or dynamically set the # of days to expiration after the coupon has been opened." data-original-title="Coupon Expiration" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<select class="form-control" name="data[MobileCoupon][coupon_expiration]" onchange ="return coupon_expiration(this.value)">
    											<option value="0">Expiration date</option>
    											<option value="1">Dynamic days to Expiration</option>
    										</select>
    									</div>
    									<div class="form-group" id="coupon_expiration_div">
    										<label>Expiration Date</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="This allows you to set an expiration date for your coupon." data-original-title="Expiration Date" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<input type="text" id="expiration_date" name="data[MobileCoupon][expiration_date]" class="form-control" placeholder="Expiration Date" value="" onchange="return change_expiration_date()">
    									</div>
    									<div class="form-group" id="coupon_dynamic_days_div" style="display:none;">
    										<label>Dynamic days to Expiration</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="This allows you to dynamically set the # of days to expiration after the coupon has been opened. Place these types of coupons in the auto-reply after someone joins your list and/or when sending out your birthday SMS wishes." data-original-title="Dynamic days to Expiration" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<input type="number" id="dynamic_days" name="data[MobileCoupon][dynamic_days]" class="form-control" placeholder="Dynamic days to Expiration" value="1" min="1" onchange="return change_dynamic_days()" onkeyup="return change_dynamic_days()">
    									</div>
    									<div class="form-group ">
    										<label>Allow Customers to Vote?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Here you can allow your customers to vote on whether they like your coupon offer or not. When this is turned on, the coupon will display a happy face button to vote positive and an angry face button to vote negative. Each customer is allowed to vote only once." data-original-title="Allow Customers to Vote?" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    										<select id="showVoteContainer" class="form-control" name="data[MobileCoupon][showVoteContainer]" onchange="return change_vote_div(this.value)">
    											<option value="0">No</option>
    											<option value="1">Yes</option>
    										</select>
    									</div>
    								</div>
    							</div>
						    </div>
					
						
							<div class="tab-pane fade" id="tab_1_2">
								<div class="portlet light bordered">
									<div class="portlet-body"> 
										<div class="form-group ">
											<label>Coupon Header</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set your coupon header text like your business name. This text shows up at the top of your coupon." data-original-title="Coupon Header" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="coupon_header" name="data[MobileCoupon][coupon_header]" class="form-control" placeholder="Header" value="" onkeyup="return change_offer_header_coupon();">
										</div>
										<div class="form-group ">
											<!--<label>Coupon Image</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set the coupon image that will display right beneath your header. Most people will use a product image, logo or whatever signifies your offer best." data-original-title="Offer Image" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="file" accept="image/*" id="offer_image" name="data[MobileCoupon][offer_image]" class="form-control" placeholder="Offer Image" value="">-->
											
											<label>Coupon Image</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set the coupon image that will display right beneath your header. Most people will use a product image, logo or whatever signifies your offer best." data-original-title="Offer Image" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<br><div data-provides="fileinput" class="fileinput fileinput-new">
												<input type="hidden" id="check_img_validation" value="0" />
												<div style="width: 200px; height: 150px;" class="fileinput-new thumbnail">
													<img alt="" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"> </div>
												<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-preview fileinput-exists thumbnail"></div>
												<div>
													<span class="btn default btn-file">
														<span class="fileinput-new"> Select image </span>
														<span class="fileinput-exists"> Change </span>
														<input type="hidden" value="" name="..."><input type="file" accept="image/*" id="offer_image" name="data[MobileCoupon][offer_image]" class="form-control" placeholder="Offer Image" value="" > </span>
													<a data-dismiss="fileinput" class="btn red fileinput-exists" href="javascript:;"> Remove </a>
												</div>
											</div>
										</div>
										<div class="form-group ">
											<label>Coupon Offer</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set the primary coupon offer here. Keep this short and sweet." data-original-title="Offer" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="offer_name" name="data[MobileCoupon][offer_name]" class="form-control" placeholder="Offer" value="" onkeyup="return change_offer_coupon();">
										</div>
										<div class="form-group ">
											<label>Offer Description</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set the coupon offer description here. This will display right below your primary coupon offer above and should provide a little more detail on your offer." data-original-title="Offer Description" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<textarea id="offer_description" maxlength="1600" name="data[MobileCoupon][offer_description]" cols="5" rows="3" class="form-control" placeholder="Offer Description" onkeyup="return change_offer_description_coupon();"></textarea>
										</div>
										<div class="form-group ">
											<label>Redeem Button Text</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set the button text on your redeem button." data-original-title="Redeem Button Text" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="redeem_button_text" name="data[MobileCoupon][redeem_button_text]" class="form-control" placeholder="Redeem Button Text" value="" onkeyup="return change_redeem_button_coupon();">
										</div>
										<div class="form-group ">
											<label>Terms & Conditions</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Include your terms & conditions here and it will show up at the very bottom of your coupon." data-original-title="Fine Print" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<textarea id="fine_prints" maxlength="1600" name="data[MobileCoupon][fine_print]" cols="5" rows="3" class="form-control" placeholder="Terms & Conditions" onkeyup="return change_terms_coupon();"></textarea>
										</div>
									</div>
								</div>
							</div>
							
							<div class="tab-pane fade" id="tab_1_3">
								<div class="portlet light bordered">
									<div class="portlet-body"> 
										<div class="form-group ">
											<label>Show Call Icon?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set whether to show a call icon on the coupon which will be populated with the phone number below." data-original-title="Call Number" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<select class="form-control" name="data[MobileCoupon][showCallLink]" onchange ="return showCallLink(this.value)">
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</div>
										<div class="form-group" id="showCallLink_div">
											<label>Call Number</label>
											<input type="text" id="call_number" name="data[MobileCoupon][call_number]" class="form-control" placeholder="Call Number" value="" onkeyup="return updateCallLinkInPreview();">
										</div>
										<div class="form-group">
											<label>Show Website Icon?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set whether to show a website icon on the coupon which will be directed to the website URL below when clicked on." data-original-title="Website URL" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<select class="form-control" name="data[MobileCoupon][showWebsiteLink]" onchange ="return showWebsiteLink(this.value)">
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</div>
										<div class="form-group" id="showWebsiteLink_div">
											<label>Website URL</label>
											<input type="text" id="website" name="data[MobileCoupon][website]" class="form-control" placeholder="https://yourwebsite.com" value="" onkeyup="return updatewebsiteLinkInPreview();">
										</div>
										<div class="form-group ">
											<label>Show Directions Icon?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set whether to show a directions icon on the coupon which will be directed to Google maps when clicked on. Go to Google Maps and search for the address, then copy the exact URL and paste it below." data-original-title="Directions URL" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<select class="form-control" name="data[MobileCoupon][showDirectionsLink]" onchange ="return showDirectionsLink(this.value)">
												<!--<option value="1">No</option>
												<option value="0">Yes</option>-->
												
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</div>
										<div class="form-group" id="showDirectionsLink_div" style="display:block;">
											<label>Directions URL</label>
											<input type="text" id="directions" name="data[MobileCoupon][directions]" class="form-control" placeholder="https://google.com/maps" value="" onkeyup="return updatedirectionsLinkInPreview();">
										</div>
										<div class="form-group">
											<label>Show Facebook Icon?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set whether to show a Facebook icon on the coupon which will be directed to the Facebook URL below when clicked on." data-original-title="Facebook URL" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<select class="form-control" name="data[MobileCoupon][showFacebookLink]" onchange ="return showFacebookLink(this.value)">
												<option value="0">No</option>
												<option value="1">Yes</option>
											</select>
										</div>
										<div class="form-group" id="showFacebookLink_div" style="display:none;">
											<label>Facebook URL</label>
											<input type="text" id="facebook" name="data[MobileCoupon][facebook]" class="form-control" placeholder="https://facebook.com/yourpage" value="" onkeyup="return updatefacebookLinkInPreview();">
										</div>
										<div class="form-group">
											<label>Show Instagram Icon?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Set whether to show an Instagram icon on the coupon which will be directed to the Instagram URL below when clicked on." data-original-title="Instagram URL" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<select class="form-control" name="data[MobileCoupon][showInstagramLink]" onchange ="return showInstagramLink(this.value)">
												<!--<option value="1">No</option>
												<option value="0">Yes</option>-->
												<option value="0">No</option>
												<option value="1">Yes</option>
											</select>
										</div>
										<div class="form-group" id="showInstagramLink_div" style="display:none;">
											<label>Instagram URL</label>
											<input type="text" id="instagram" name="data[MobileCoupon][instagram]" class="form-control" placeholder="https://instagram.com/yourpage" value="" onkeyup="return updateinstagramLinkInPreview();">
										</div>
									</div>
								</div>
							</div>
							
							<div class="tab-pane fade" id="tab_1_4">
								<div class="portlet light bordered">
									
									<div class="portlet-body"> 
										<div class="form-group ">
											<label>Redemption Type?</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The standard redemption type will display the redemption code you enter below when redeemed. The QR Code will redirect to a status page when scanned that will show the redemption code and total # of redemptions for the coupon." data-original-title="Redemption Type" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<select id="redemption_type" class="form-control" name="data[MobileCoupon][redemption_type]">
												<option value="0">Standard</option>
												<option value="1">QR Code</option>
											</select>
										</div>
										<div class="form-group ">
											<label>Redemption Code</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="This is the redemption code that will display on the coupon after it has been redeemed. This code is usually generated by the POS system and can be copied here." data-original-title="Redemption Code" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="redemption_code" name="data[MobileCoupon][redemption_code]" class="form-control" placeholder="Redemption Code" value="">
										</div>
									</div>
								</div>
							</div>
							</div>
						</div>
						<div class="col-sm-5">
							<div id="right" style="margin-top: 0px;">
								<div id="mobileCoupon">
									<iframe id="mobileCouponPreview" src="<?php echo SITE_URL;?>/mobilecoupons/preview"></iframe>
								</div>
							</div>
						</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-12">
								<div style="margin-top:15px;">
									<button type="submit" name="submit" class="btn blue">Save</button>
									<?php echo $this->Html->link('Cancel','javascript:window.history.back();',array('class' => 'btn default'));?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
function colorreflect(colorway){
	$("#mobileCouponPreview").contents().find("#header.themecolor").css('background-color', colorway);
	$("#mobileCouponPreview").contents().find(".button.themecolor").css('background-color', colorway);
}
function change_offer_coupon(){
	var offer_name_text = $("#offer_name").val();
	$("#mobileCouponPreview").contents().find("#offer").text(offer_name_text);
}
function change_offer_description_coupon(){
	var offer_description_text = $("#offer_description").val();
	$("#mobileCouponPreview").contents().find("#offerDescription").text(offer_description_text);
}
function change_offer_header_coupon(){
	var offer_header_text = $("#coupon_header").val();
	$("#mobileCouponPreview").contents().find("#header").text(offer_header_text);
}
function change_redeem_button_coupon(){
	var redeem_button_text_text = $("#redeem_button_text").val();
	$("#mobileCouponPreview").contents().find("#btnRedeem").text(redeem_button_text_text);
}
function change_terms_coupon(){
	var terms_coupon_text = $("#fine_prints").val();
	$("#mobileCouponPreview").contents().find("#terms").text(terms_coupon_text);
}
$(function () {
	$('#expiration_date').datetimepicker({  
	    startDate: '<?php echo date("Y-m-d H:i:s");?>',
		autoclose: true
	});
});
function showCallLink(id){
	if(id==0){
		$('#showCallLink_div').hide();
		$("#mobileCouponPreview").contents().find("#businessContainer #call").hide();
	}else{
		$('#showCallLink_div').show();
		$("#mobileCouponPreview").contents().find("#businessContainer #call").show();
	}
}
function showWebsiteLink(id){
	if(id==0){
		$('#showWebsiteLink_div').hide();
		$("#mobileCouponPreview").contents().find("#businessContainer #website").hide();
	}else{
		$('#showWebsiteLink_div').show();
		$("#mobileCouponPreview").contents().find("#businessContainer #website").show();
	}
}
function showFacebookLink(id){
	if(id==0){
		$('#showFacebookLink_div').hide();
		$("#mobileCouponPreview").contents().find("#socialContainer #facebook").hide();
	}else{
		$('#showFacebookLink_div').show();
		$("#mobileCouponPreview").contents().find("#socialContainer #facebook").show();
	}
}
function showInstagramLink(id){
	if(id==0){
		$('#showInstagramLink_div').hide();
		$("#mobileCouponPreview").contents().find("#socialContainer #instagram").hide();
	}else{
		$('#showInstagramLink_div').show();
		$("#mobileCouponPreview").contents().find("#socialContainer #instagram").show();
	}
}
function showDirectionsLink(id){
	if(id==0){
		$('#showDirectionsLink_div').hide();
		$("#mobileCouponPreview").contents().find("#businessContainer #directions").hide();
	}else{
		$('#showDirectionsLink_div').show();
		$("#mobileCouponPreview").contents().find("#businessContainer #directions").show();
	}
}
function one_per_person(id){
	if(id==1){
		$('#one_per_person_div').show();
	}else{
		$('#one_per_person_div').hide();
	}
}
function coupon_expiration(id){
	if(id==1){
		$('#coupon_dynamic_days_div').show();
		$('#coupon_expiration_div').hide();
	}else{
    	$('#coupon_dynamic_days_div').hide();
		$('#coupon_expiration_div').show();
	}
}
function coupon_expiration(id){
	if(id==1){
		$('#coupon_dynamic_days_div').show();
		$('#coupon_expiration_div').hide();
	}else{
    	$('#coupon_dynamic_days_div').hide();
		$('#coupon_expiration_div').show();
	}
}
function change_dynamic_days(){
	var dynamic_days = $("#dynamic_days").val();
	setTimer(dynamic_days);
}
function change_expiration_date(){
	var expiration_date = $("#expiration_date").val();
	setTimer(expiration_date);
}
function change_vote_div(id){
	if(id==1){
		$("#mobileCouponPreview").contents().find("#voteContainer").show();
	}else{
		$("#mobileCouponPreview").contents().find("#voteContainer").hide();
	}
}
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
		reader.onload = function(e) {
			$("#mobileCouponPreview").contents().find("#heroImage").attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}
$("#offer_image").change(function() {
	readURL(this);
});
function setTimer(endtime){				
	if(/^\d+$/.test(endtime)){
		var expirationDate = new Date();
		expirationDate.setDate(expirationDate.getDate() + parseInt(endtime));
		endtime = expirationDate;
	}
	if(typeof(timeinterval) !== "undefined")
	{
		clearInterval(timeinterval);
	}
	$("#mobileCouponPreview").contents().find("#couponStatus").text("EXPIRES IN...");
	$("#mobileCouponPreview").contents().find("#expirationTimer").show();
	$("#mobileCouponPreview").contents().find("#expirationTimer #days").show();
	$("#mobileCouponPreview").contents().find("#expirationTimer #hours").show();
	$("#mobileCouponPreview").contents().find("#expirationTimer #minutes").show();
	$("#mobileCouponPreview").contents().find("#expirationTimer #seconds").show();
	$("#mobileCouponPreview").contents().find("#btnRedeem").show();
	
	// Update the clock
	var timeRemaining = getExpirationRemaining(endtime);
	updateExpirationRemaining(timeRemaining);
	timeinterval = setInterval(function(){
		timeRemaining = getExpirationRemaining(endtime);
		updateExpirationRemaining(timeRemaining);	
		if(timeRemaining.total<=0){
			clearInterval(timeinterval);
		}
	},1000);
}
function getExpirationRemaining(endtime){
	var distance = Date.parse(endtime) - Date.now();
	var days = Math.floor((distance % (1000 * 60 * 60 * 24 * 30)) / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 *  60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	return {
		'total': distance <= 0 ? 0 : distance,
		'days': days <= 0 ? 0 : days,
		'hours': hours <= 0 ? 0 : hours,
		'minutes': minutes <= 0 ? 0 : minutes,
		'seconds': seconds <= 0 ? 0 :  seconds
	};
}
function updateExpirationRemaining(timeRemaining){
	if(timeRemaining.total > 0){
		$("#mobileCouponPreview").contents().find("#seconds .count").text(timeRemaining.seconds);
		$("#mobileCouponPreview").contents().find("#minutes .count").text(timeRemaining.minutes);
		$("#mobileCouponPreview").contents().find("#hours .count").text(timeRemaining.hours);
		$("#mobileCouponPreview").contents().find("#days .count").text(timeRemaining.days);
	}else{
		setmobileCouponExpired();
	}
}
function setmobileCouponExpired(){
	$("#mobileCouponPreview").contents().find("#couponStatus").text("This coupon has ended, but stayed tuned for more.");
	$("#mobileCouponPreview").contents().find("#expirationTimer").hide();
	$("#mobileCouponPreview").contents().find("#expirationTimer #days").hide();
	$("#mobileCouponPreview").contents().find("#expirationTimer #hours").hide();
	$("#mobileCouponPreview").contents().find("#expirationTimer #minutes").hide();
	$("#mobileCouponPreview").contents().find("#expirationTimer #seconds").hide();
	$("#mobileCouponPreview").contents().find("#btnRedeem").hide();
}
function updateCallLinkInPreview(){
	var call_number_val = $("#call_number").val();
	$("#mobileCouponPreview").contents().find("#call").attr("href", "tel:" + call_number_val);	
}
function updatewebsiteLinkInPreview(){
	var website_val = $("#website").val();
	$("#mobileCouponPreview").contents().find("#website").attr("href", website_val);	
}
function updatedirectionsLinkInPreview(){
	var directions_val = $("#directions").val();
	$("#mobileCouponPreview").contents().find("#directions").attr("href", directions_val);	
}
function updatefacebookLinkInPreview(){
	var facebook_val = $("#facebook").val();
	$("#mobileCouponPreview").contents().find("#facebook").attr("href", facebook_val);	
}
function updateinstagramLinkInPreview(){
	var instagram_val = $("#instagram").val();
	$("#mobileCouponPreview").contents().find("#instagram").attr("href", instagram_val);	
}
</script>