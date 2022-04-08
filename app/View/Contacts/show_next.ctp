<?php 
	echo $this->Html->script('jQvalidations/jquery.validation.functions');
	echo $this->Html->script('jQvalidations/jquery.validate');
?>
<style>
.ValidationErrors{
color:red;
}
</style>
<script>
 /* <![CDATA[ */
	jQuery(function(){
	 jQuery("#GroupId").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please Choose Group"
		});jQuery("#name").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please Choose name"
		});jQuery("#phone").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please Choose phone"
		});
	});
</script>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Import Contacts</h3>
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption font-red-sunglo">
					<i class="icon-settings font-red-sunglo"></i>
					<span class="caption-subject bold uppercase"> Import Contacts </span>
				</div>					
			</div>
			<div class="portlet-body form">
				<?php echo $this->Form->create('Contact',array('action'=> 'check_csvdata','name'=>'loginForm','id'=>'loginForm','enctype'=>'multipart/form-data','method'=>'post'));?>
					<div class="form-body">
						<div class="form-group">
							<label for="exampleInputPassword1">Group Name</label>
							<?php 
								echo $this->Form->input('Group.id', array(
								'div'=>false,
								'label'=>false,
								'class'=>'form-control',
								'default'=>0,
								'multiple'=>true,
								'onchange'=>'group(this.value)',	
								'options' => $Group));
							?>
						</div>
						<fieldset>
						    <legend>CSV Column Mapping</legend>
						    <div class="note note-info">
						    Map the CSV columns to the correct contact field. If a field below is not part of your CSV file import, leave as is.
						    </div>
    						<div class="form-group">
    							<label>Name</label>
    							<?php echo $this->Form->select('name', $header, array('label' => false, 'empty' => 'Select Name Column from CSV','id'=>'name','class'=>'form-control'));?>
    						</div>	
    						<div class="form-group">
    							<label>Phone #</label>
    							<?php echo $this->Form->select('phone', $header, array('label' => false, 'empty' => 'Select Phone Column from CSV','id'=>'phone','class'=>'form-control'));?>
    						</div>	
    						
    						<div class="form-group">
    							<label>Fax #</label>
    							<?php echo $this->Form->select('fax', $header, array('label' => false, 'empty' => 'Select Fax Column from CSV','id'=>'fax','class'=>'form-control'));?>
    						</div>	
    						<div class="form-group">
    							<label>Email</label>
    							<?php echo $this->Form->select('email', $header, array('label' => false, 'empty' => 'Select Email Column from CSV','id'=>'email','class'=>'form-control'));?>
    						</div>	
    						<div class="form-group">
    							<label>Birthday</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Date format in your CSV file for birthday should be YYYY-MM-DD" data-original-title="Birthday Format" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
    							<?php echo $this->Form->select('birthday', $header, array('label' => false, 'empty' => 'Select Birthday Column from CSV. Date Format: YYYY-MM-DD','id'=>'phone','class'=>'form-control'));?>
    						</div>	
    						<div class="form-group">
    							<label>City</label>
    							<?php echo $this->Form->select('city', $header, array('label' => false, 'empty' => 'Select City Column from CSV','id'=>'city','class'=>'form-control'));?>
    						</div>	
    						<div class="form-group">
    							<label>State/Province</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="State/Province in your CSV file should be in 2 digit abbreviation format. For example, Illinois should be entered as IL." data-original-title="2 Digit State/Province" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a></label>
    							<?php echo $this->Form->select('state', $header, array('label' => false, 'empty' => 'Select State/Province Column from CSV','id'=>'state','class'=>'form-control'));?>
    						</div>	
    						<div class="form-group">
    							<label>Postal Code</label>
    							<?php echo $this->Form->select('zip', $header, array('label' => false, 'empty' => 'Select Postal Code Column from CSV','id'=>'zip','class'=>'form-control'));?>
    						</div>
    						<fieldset>
                                <legend>Custom Fields</legend>
                                <div class="note note-info">
                        	        Use these custom fields for anything you want. Might be an account #, employee ID, department name, role, etc...Then use the merge tags (<b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>) to output the data when sending bulk SMS
                        	    </div>
        						<div class="form-group">
        							<label>Custom 1</label>
        							<?php echo $this->Form->select('custom1', $header, array('label' => false, 'empty' => 'Select Custom Field 1 Column from CSV','id'=>'custom1','class'=>'form-control'));?>
        						</div>
        						<div class="form-group">
        							<label>Custom 2</label>
        							<?php echo $this->Form->select('custom2', $header, array('label' => false, 'empty' => 'Select Custom Field 2 Column from CSV','id'=>'custom2','class'=>'form-control'));?>
        						</div>
        						<div class="form-group">
        							<label>Custom 3</label>
        							<?php echo $this->Form->select('custom3', $header, array('label' => false, 'empty' => 'Select Custom Field 3 Column from CSV','id'=>'custom3','class'=>'form-control'));?>
        						</div>
    						</fieldset>
						</fieldset>
						
					</div>
					<div class="form-actions">
						<?php echo $this->Form->submit('Next',array('div'=>false,'class'=>'btn blue'));?>
					</div>
				<?php echo $this->Form->end(); ?>
			</div>		
		</div>
	</div>
</div>
<div class="clearfix"></div>