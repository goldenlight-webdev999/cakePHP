<style>
.flag-sec label { width: 100%;}
.flag-sec #image { margin: 0 12px 0 0;}
.cntry-slct {   width: auto; }
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile { 
    max-width: 1000px;
    min-height: 300px;
    min-width: 500px;
    padding: 10px;
    position: relative;
}
.feildbox img{
	width:30px!important;
}
</style>
<script>
function selectcountry(){
    
    //var areacode;
    //areacode = document.getElementById("areaCode").value;
    
    //if(areacode==''){
    //    alert("Please enter an area code");
    //    return false;
    //}
	
	$.ajax({
		type: "POST",
		url:"<?php echo SITE_URL ?>/telnyxs/searchbynumber",
		data: {
			//type:$('#type').val(),
			state:$('#state1').val(),
			city:$('#city1').val(),
			areaCode:$('#areaCode').val(),
			country:$('#country').val(),
		},
		success: function(result) {
			$('.nyroModalLink').html(result);
		}
	});
}
</script>
<div class="portlet box blue-dark">
	<div class="portlet-title">
		<div class="caption">
			<!--<i class="fa fa-users"></i>-->
			Phone Number Search
		</div>
	</div>
<div class="portlet-body">
<div><?php echo $this->Session->Flash();?></div> 
 <?php $active = $this->Session->read('User.active');
$usercountry = $this->Session->read('User.user_country');
$package = $this->Session->read('User.package');
$getnumbers = $this->Session->read('User.getnumbers');
?>
<?php if ($usercountry == 'Canada') { ?>
	<input type="hidden" id="country" name="data[country]" value="CA"/>
<?} else if ($usercountry == 'United States') { ?>
    <input type="hidden" id="country" name="data[country]" value="US"/>
<?}else if ($usercountry == 'United Kingdom') { ?>
    <input type="hidden" id="country" name="data[country]" value="GB"/>
<?}else if ($usercountry == 'Australia') { ?>
    <input type="hidden" id="country" name="data[country]" value="AU"/>
<?}
?>


<?php if ($getnumbers == 0){?>
      <h3>You need a security permission enabled before getting a number.</h3><br>
<?php } elseif (REQUIRE_MONTHLY_GETNUMBER == 1 && $package == 0){?>
      <h3>You need to be subscribed to a monthly plan before getting a number.</h3><br>
<?php } elseif ($active==0){?>
					<h3>Oops! You need to activate your account to use this feature.</h3><br>
					<?php $payment=PAYMENT_GATEWAY;
					if($payment=='1' && PAY_ACTIVATION_FEES=='1'){?>
						Activate account with PayPal by <?php echo $this->Html->link('Clicking Here', array('controller' =>'users', 'action' =>'activation/'.$payment))?>.<br />
					<?php }else if($payment=='2' && PAY_ACTIVATION_FEES=='1'){?>
						Activate account with Credit Card by <?php echo $this->Html->link('Clicking Here', array('controller' =>	'users', 'action' =>'activation/'.$payment))?>.<br />
					<?php }else if($payment=='3' && PAY_ACTIVATION_FEES=='1'){ ?>
						Activate account with <b><?php echo $this->Html->link('PayPal', array('controller' =>'users', 'action' =>'activation/1'))?></b> or <b><?php echo $this->Html->link('Credit Card', array('controller' =>'users', 'action' =>'activation/2'))?></b><br />
					<?php } ?> 

	<?php   }else{ ?>
		<div id="validationMessages" style="display:none"></div>
		<div class="form-body">
			<!--<div class="form-group">
				<label>Select Number Features</label>
				<select multiple class="form-control input-large"  id="type" name="data[features]">
					<option value="sms" selected>SMS</option>
					<option value="mms">MMS</option>
					<option value="voice">Voice</option>
					<option value="fax">Fax</option>
				</select>
			</div>-->
			<div class="form-group">
			    <label><b>Country:</b></label>&nbsp;&nbsp;<?php echo $usercountry?>
			</div>
			<div id="local">
			    <?php if($usercountry=='United States') {?>
    			    <button class="btn default" id="area_code_button">AreaCode</button>
    				<button class="btn default" id="city_button">City</button>
    				<button class="btn default" id="state_button">State</button>
    				<div id="state" style="display:none">
    					<div class="form-group">
    						<label>State </label>
    						<input type="text" name="data[state]" id="state1" class="form-control input-large" maxlength="2"/>
    					</div>
    				</div>
    				<div id="city" style="display:none">
    					<div class="form-group">
    						<label>City </label>
    						<input type="text" name="data[city]" id="city1" class="form-control input-large" />
    					</div>
    				</div>
    				<div class="form-group" id="area_codes" >
    					<label>AreaCode </label>
    					<input type="text" name="data[areaCode]" id="areaCode" class="form-control input-large" maxlength="3"/>
    				</div> 
				<?php }elseif($usercountry=='Canada') {?>
    				<div class="form-group" id="area_codes" >
    					<label>AreaCode </label>
    					<input type="text" name="data[areaCode]" id="areaCode" class="form-control input-large" maxlength="3"/>
    				</div> 
				<?php }else {?>
				    <!--<button class="btn default" id="area_code_button">AreaCode</button>
    				<button class="btn default" id="city_button">City</button>
    				<button class="btn default" id="state_button">State</button>
    				<div id="state" style="display:none">
    					<div class="form-group">
    						<label>State </label>
    						<input type="text" name="data[state]" id="state1" class="form-control input-large" maxlength="2"/>
    					</div>
    				</div>
    				<div id="city" style="display:none">
    					<div class="form-group">
    						<label>City </label>
    						<input type="text" name="data[city]" id="city1" class="form-control input-large" />
    					</div>
    				</div>
    				<div class="form-group" id="area_codes" >
    					<label>AreaCode </label>
    					<input type="text" name="data[areaCode]" id="areaCode" class="form-control input-large" maxlength="3"/>
    				</div> -->
    			<?php }?>
			</div> 
			
			<div class="form-group">
				<?php echo $this->Form->button('Search',array('div'=>false,'class'=>'btn blue ','onClick'=>'selectcountry()'));?>
			</div>
			<?php echo $this->Form->end(); ?>		
		</div>
	</div>
<?php } ?>	
</div>

<script>
$(function() {
    /*$('#type').change(function(){
        if($('#type').val() == 'Local') {
            $('#local').show();
        } else {
            $('#local').hide();
       } 
    });*/
	$("#area_code_button").click(function(){
		$("#city").hide();
		$("#state").hide();
		$("#area_codes").show();
	});
	$("#city_button").click(function(){
		$("#city").show();
		$("#state").hide();
		$("#area_codes").hide();
	});
	$("#state_button").click(function(){
		$("#city").hide();
		$("#state").show();
		$("#area_codes").hide();
	});
});
</script>
