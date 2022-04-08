
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title">Appointments</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
							<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
					       <a href="<?php echo SITE_URL;?>/appointments/index">Appointment List</a>
				    </li>
					
				</ul>  			
			</div>			
			<div class="clearfix"></div>
			
	         <div class="portlet light ">
			 <div class="portlet-title">
                                    <div class="caption font-red-sunglo">
                                        <i class="fa fa-upload font-red-sunglo"></i>
                                        <span class="caption-subject bold uppercase">Import  Appointments </span>
                                    </div>
                                
                                </div>
                          <?php echo $this->Session->flash(); ?>	
                                <div class="portlet-body form">
                                  <?php echo $this->Form->create('appointments',array('action'=> 'show_next','name'=>'loginForm','id'=>'loginForm','enctype'=>'multipart/form-data','method'=>'post'));?>
                                        <div class="form-body">                                          
                                            <div class="form-group">
                                                <label for="exampleInputFile1">CSV Appointments File to Upload</label>
                                                <input id="appointment_csv" type="file" name="data[appointment_csv]" accept=".csv">                                 
                                            </div>   
                                        </div>
                                        <i class="fa fa-file-excel-o" aria-hidden="true" style="font-size:24px;color:darkgreen"></i>&nbsp;Only .CSV files accepted
                                        <br/><br/>
                                        <div class="note note-info">
                                        <span class="text-muted"><b>NOTE:</b> The 1st row of the file should be a <u>header</u> row. 1st column is contact phone number, 2nd column is appt date/time, 3rd column is status.
                                        <br/><br/><b>Appt date/time format:</b> YYYY-MM-DD hh:mm AM/PM - EXAMPLE: 2018-09-25 12:30 PM
                                        <br/><b>Available statuses:</b> Confirmed, Unconfirmed, Cancelled, Reschedule
                                        <br/><br/><b>** Please save your appointment settings before importing your appointments so that all statuses are saved.</b>
                                        </span>
                                        </div>
                                        <br/>
                                        <div class="form-actions">                                            
											<?php echo $this->Form->submit('Import',array('div'=>false,'class'=>'btn blue'));?>              
                                        </div>
                                    </form>
                                </div>
                            </div>
		</div>
  </div> 
