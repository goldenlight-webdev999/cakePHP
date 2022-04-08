<script>
function selectcountry(){
  $.ajax({type: "POST",url:"<?php echo SITE_URL ?>/signalwires/searchcountry",
	success: function(result) {
		$('.nyroModalLink').html(result);
	}});	
}
function assignthisnumber(numbertoassign){
	$.ajax({
		type: "POST",
		url:"<?php echo SITE_URL ?>/signalwires/assignthisnumber",
		data: {
			number:numbertoassign,
			type:'<?php echo $type;?>',
		},
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
       <div class="row">
		<?php if(!empty($response)){?>
			 <div class="table-scrollable">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Number</th>
								<?php if($type == 'Local'){?>
									<th>Rate Center</th>
									<th>State</th>
								<?php }?>
								<!--th>Price</th-->
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($response as $phone_arr){	
						?>
						<tr>
							<td><?php echo $phone_arr->phone_number; ?></td>
							<?php if($type == 'Local'){?>
								<td><?php echo $phone_arr->rate_center; ?></td>
								<td><?php echo $phone_arr->region; ?></td>
							<?php }?>
							<td><a  class="btn blue btn-outline btn-sm" href="#" onClick="assignthisnumber(<?php echo $phone_arr->phone_number; ?>)" >Get this number</a></td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>	
		<?php  }else{?>
			<a class="btn blue" href="#null" onClick="selectcountry()">Search Again</a>
		<?php }?>
	</div>
</div>
</div>
</div>
</div>
</div>

