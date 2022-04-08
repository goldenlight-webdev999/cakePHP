<script>
function selectcountry(){
  $.ajax({type: "POST",url:"<?php echo SITE_URL ?>/telnyxs/searchcountry",
	success: function(result) {
		$('.nyroModalLink').html(result);
	}});	
}
function assignthisnumber(numbertoassign,invoice,insms,inmms,infax){
    voice=invoice;
	sms=insms;
	mms=inmms;
	fax=infax;

	$.ajax({
		type: "POST",
		url:"<?php echo SITE_URL ?>/telnyxs/assignthisnumber",
		data: {number:numbertoassign,voice:voice,mms:mms,sms:sms,fax:fax},
			success: function(result) {
			if(result=='sucess'){
			   window.location = '<?php echo SITE_URL;?>/users/profile';
			}
		}
	});
}
</script>
<div class="portlet box blue-dark">
	<div class="portlet-title">
		<div class="caption">
			Choose a Phone Number
		</div>
	</div>
<div class="portlet-body">
<div class="form-body">
<div class="form-group">
             <div><?php echo $this->Session->Flash();?></div>   
 <div class="portlet light portlet-fit ">
     
       <div class="caption">
			<span class="caption-subject font-blue sbold uppercase"><b>Please allow 8 seconds or so after clicking the "Get this number" button to assign the number.</b></span>
       </div>
       
       <div class="row">
		<?php if(!empty($response)){?>
			 <div class="table-scrollable">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Number</th>
								<!--<?php if($type == 'Local'){?>
									<th>Rate Center</th>
									<th>State</th>
								<?php }?>-->
								<!--th>Price</th-->
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($response as $phone_arr){	
					
						?>
						<tr>
							<td>
							    <?php echo $phone_arr->phone_number; ?>
							    <br/><label>Capabilities:</label>
								<br/><label style="color:green;">
								    <?php 
								    $voice=0;
								    $sms=0;
								    $mms=0;
								    $fax=0;
								    foreach($phone_arr->features as $feature_arr){	
								    ?> 
								    <?php if($feature_arr->name!='emergency'){
								         echo strtoupper($feature_arr->name); 
								    }  
								    if($feature_arr->name=='voice') {
									    $voice=1;
									}
									if($feature_arr->name=='sms') {
									    $sms=1;
								    }
								    if($feature_arr->name=='mms') {
									    $mms=1;
									}
									if($feature_arr->name=='fax') {
									    $fax=1;
									}} ?>
							</td>
							<td style="vertical-align:middle"><a  class="btn blue btn-outline btn-sm" href="#" onClick="assignthisnumber(<?php echo $phone_arr->phone_number?>, <?php echo $voice?>, <?php echo $sms?>, <?php echo $mms?>, <?php echo $fax ?>)" >Get this number</a></td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>	
		<?php  }else{?>
		    <b>There were no numbers found with those search parameters.</b><br><br>
			<a class="btn blue" href="#null" onClick="selectcountry()">Search Again</a>
		<?php }?>
	</div>
</div>
</div>
</div>
</div>
</div>

