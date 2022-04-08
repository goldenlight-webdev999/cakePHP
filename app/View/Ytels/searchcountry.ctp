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
    
    var areacode;
    areacode = document.getElementById("areaCode").value;
    
    if(areacode==''){
        alert("Please enter an area code");
        return false;
    }
	
	$.ajax({
		type: "POST",
		url:"<?php echo SITE_URL ?>/ytels/searchbynumber",
		data: {
			type:$('#type').val(),
			state:$('#state').val(),
			city:$('#city').val(),
			zip:$('#zip').val(),
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
<?}?>


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
			<div class="form-group">
				<label>Select Type</label>
				<select class="form-control input-large"  id="type" name="data[type]">
					<option value="sms" selected>SMS</option>
					<option value="voice" selected>Voice</option>
					<option value="all" selected>All</option>
				</select>
			</div>
			<div id="local">
				<!--<button class="btn default" id="area_code_button" >AreaCode</button>
				<button class="btn default" id="zip_code_button"  >ZipCode</button>
				<button class="btn default" id="state_button"   >State</button>-->
				<!--<div id="state_city" style="display:none">
					<div class="form-group">
						<label>State </label>
						<input type="text" name="data[state]" id="state" class="form-control input-large" maxlength="2"/>
					</div>
					<!--div class="form-group">
						<label>City </label>
						<input type="text" name="data[city]" id="city" class="form-control input-large" />
					</div
				</div>-->
				<!--<div class="form-group" id="zip_codes"  style="display:none">
					<label>ZipCode </label>
					<input type="text" name="data[zip]" id="zip" class="form-control input-large" maxlength="5"/>
				</div> 	-->
				<div class="form-group" id="area_codes" >
					<label>AreaCode </label>
					<input type="text" name="data[areaCode]" id="areaCode" class="form-control input-large" maxlength="3"/>
				</div> 
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
		$("#zip_codes").hide();
		$("#state_city").hide();
		$("#area_codes").show();
	});
	$("#zip_code_button").click(function(){
		$("#zip_codes").show();
		$("#state_city").hide();
		$("#area_codes").hide();
	});
	$("#state_button").click(function(){
		$("#zip_codes").hide();
		$("#state_city").show();
		$("#area_codes").hide();
	});
});
</script>
