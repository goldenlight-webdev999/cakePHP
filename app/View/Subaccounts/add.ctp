<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<style>
.error-message{
color:red;
}
</style>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo('Add Sub-Account');?>
			<small></small>
		</h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo SITE_URL;?>/subaccounts/index">Sub-Accounts</a>
				</li>
			</ul>
		</div>
		<div class="clearfix"></div>
		<?php echo $this->Session->flash(); ?>
		<div class="portlet mt-element-ribbon light portlet-fit  ">
<div class="ribbon ribbon-right ribbon-clip ribbon-shadow ribbon-border-dash-hor ribbon-color-success uppercase">
<div class="ribbon-sub ribbon-clip ribbon-right"></div>
create sub-account form
</div>
			<div class="portlet-title">
				<div class="caption font-red-sunglo">
					<i class="icon-user-follow font-red-sunglo"></i>
					<span class="caption-subject bold uppercase"> </span>
				</div>
			</div>
			<div class="portlet-body form">
				<div id="validationMessages" style="display:none"></div>
				<?php echo $this->Form->create('Subaccount',array('action'=> 'add','name'=>'subaccountform','id'=>'subaccountform'));?>
					<div class="form-body">
					    <div class="form-group">
							<label>User Name<span class="required_star"></span></label>
							<?php echo $this->Form->input('Subaccount.username',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'username'))?>
						</div>
						<div class="form-group">
							<label>First Name<span class="required_star"></span></label>
							<?php echo $this->Form->input('Subaccount.first_name',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'firstname'))?>
						</div>
						<div class="form-group">
							<label>Last Name<span class="required_star"></span></label>
							<?php echo $this->Form->input('Subaccount.last_name',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'lastname'))?>
						</div>
						<div class="form-group">
							<label>Email<span class="required_star"></span></label>
							<?php echo $this->Form->input('Subaccount.email',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'email'))?>
						</div>
					    <div class="form-group">
							<label>Password<span class="required_star"></span></label>
							<?php echo $this->Form->input('Subaccount.password',array('div'=>false,'label'=>false, 'class' => 'form-control','type' => 'password','id'=>'password'))?>
						</div>
						<div class="form-group">
			                <label>Confirm Password </label>
		                    <?php echo $this->Form->input('Subaccount.confirm_password',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'confirm_password','type' => 'password'))?>
		                </div>
		                <fieldset>
		                    <legend>Permissions</legend>
		                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
			                 <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger" name="data[Subaccount][getnumbers]" value="1" <?php if($loggedUser['User']['getnumbers']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
			                 Get Numbers
			                 </label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][sendsms]" value="1" <?php if($loggedUser['User']['sendsms']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Send SMS</label>
                    
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger" name="data[Subaccount][groups]" value="1" <?php if($loggedUser['User']['groups']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Groups</label>
                        </div>
                        
	                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][autoresponders]" value="1" <?php if($loggedUser['User']['autoresponders']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Autresponders</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][contactlist]" value="1" <?php if($loggedUser['User']['contactlist']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Contact List</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][importcontacts]" value="1" <?php if($loggedUser['User']['importcontacts']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Import Contacts</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger" name="data[Subaccount][shortlinks]" value="1" <?php if($loggedUser['User']['shortlinks']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Short Links</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][voicebroadcast]" value="1" <?php if($loggedUser['User']['voicebroadcast']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Voice Broadcast</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][polls]" value="1" <?php if($loggedUser['User']['polls']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Polls</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][contests]" value="1" <?php if($loggedUser['User']['contests']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Contests</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][loyaltyprograms]" value="1" <?php if($loggedUser['User']['loyaltyprograms']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Loyalty Programs</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][kioskbuilder]" value="1" <?php if($loggedUser['User']['kioskbuilder']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Kiosk Builder</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][birthdaywishes]" value="1" <?php if($loggedUser['User']['birthdaywishes']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Birthday Wishes</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][mobilepagebuilder]" value="1" <?php if($loggedUser['User']['mobilepagebuilder']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Page Builder</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                        <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][webwidgets]" value="1" <?php if($loggedUser['User']['webwidgets']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Web-Widgets</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][qrcodes]" value="1" <?php if($loggedUser['User']['qrcodes']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            QR Codes</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][smschat]" value="1" <?php if($loggedUser['User']['smschat']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            2-way SMS Chat</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][calendarscheduler]" value="1" <?php if($loggedUser['User']['calendarscheduler']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Scheduler</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][appointments]" value="1" <?php if($loggedUser['User']['appointments']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Appointments</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][logs]" value="1" <?php if($loggedUser['User']['logs']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Logs</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][reports]" value="1" <?php if($loggedUser['User']['reports']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Reports</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][affiliates]" value="1" <?php if($loggedUser['User']['affiliates']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Affiliates</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][makepurchases]" value="1" <?php if($loggedUser['User']['makepurchases']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Make Purchases</label>
                        </div>
                        
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][mobilecoupons]" value="1" <?php if($loggedUser['User']['mobilecoupons']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Mobile Coupons</label>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
	                         <label class="checkbox-inline">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="btn green-meadow" data-offstyle="danger"  name="data[Subaccount][qabots]" value="1" <?php if($loggedUser['User']['qabots']==1){?> checked <?php } else { ?> disabled <?php } ?>/>
                            Q&A SMS Bots</label>
                        </div>
                        </fieldset>
        
					</div>
					<div class="form-actions">
						<?php echo $this->Form->submit('Save',array('div'=>false,'class'=>'btn blue'));?>
					</div>
				<?php echo $this->Form->end();
				echo $this->Validation->rules(array('Subaccount'),array('formId'=>'subaccountform'));
				?>
			</div>
		</div>                     
		
	</div>		
</div>		