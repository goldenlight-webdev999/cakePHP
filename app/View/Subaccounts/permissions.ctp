<style>
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {
    
    max-width: 1000px;
    min-height: 321px;
    min-width: 750px;
    padding: 10px;
    position: relative;
}
</style>
    <div class="portlet box blue-dark">
				<div class="portlet-title">
						<div class="caption">
						<?php  echo('Permissions');?>
					</div>
				</div>
    			
				<div class="portlet-body">
					
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                        <table cellspacing=5>
                            <tr><td>
	                         <label class="checkbox-inline">
			                 <?php if($subaccount['Subaccount']['getnumbers']==1) {
			                     echo 'Get Numbers&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Get Numbers&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->

                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                        <td>
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['sendsms']==1) {
			                     echo 'Send SMS&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Send SMS&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
                        </td>
                        <!--</div>-->

                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                        <td>
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['groups']==1) {
			                     echo 'Groups&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Groups&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			             </td>
                        <!--</div>-->

                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                        <td>
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['autoresponders']==1) {
			                     echo 'Autoresponders&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Autoresponders&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			             </td></tr>
                       <!--</div>-->

                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                        <tr><td>
	                         <label class="checkbox-inline">
                           <?php if($subaccount['Subaccount']['contactlist']==1) {
			                     echo 'Contact List&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Contact List&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
                        </td>			                 
                        <!--</div>-->
                        
                       <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                       <td>
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['importcontacts']==1) {
			                     echo 'Import Contacts&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Import Contacts&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>   
			            </td>
			            <!--</div>-->
                        
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['shortlinks']==1) {
			                     echo 'Short Links&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Short Links&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
                            </label>
                        </td>                            
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
                            <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['voicebroadcast']==1) {
			                     echo 'Voice Broadcast&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Voice Broadcast&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			            </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                           <?php if($subaccount['Subaccount']['polls']==1) {
			                     echo 'Polls&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Polls&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			             </td></tr>
                        <!--</div>-->
                        <tr><td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['contests']==1) {
			                     echo 'Contests&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Contests&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			             </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['loyaltyprograms']==1) {
			                     echo 'Loyalty Programs&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Loyalty Programs&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			             </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['kioskbuilder']==1) {
			                     echo 'Kiosk Builder&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Kiosk Builder&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			             </td>
                        <!--</div>-->
                        
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['birthdaywishes']==1) {
			                     echo 'Birthday Wishes&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Birthday Wishes&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['mobilepagebuilder']==1) {
			                     echo 'Mobile Page Builder&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Mobile Page Builder&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td></tr>
                       <!--</div>-->
                        <tr><td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                        <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['webwidgets']==1) {
			                     echo 'Web-Widgets&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Web-Widgets&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                       <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['qrcodes']==1) {
			                     echo 'QR Codes&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'QR Codes&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['smschat']==1) {
			                     echo 'SMS Chat&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'SMS Chat&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['calendarscheduler']==1) {
			                     echo 'Calendar/Scheduler&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Calendar/Scheduler&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
                       <!--</div>-->
                       </td>
                        
                       <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['appointments']==1) {
			                     echo 'Appointments&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Appointments&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td></tr>
                        <!--</div>-->
                        <tr><td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['logs']==1) {
			                     echo 'Logs&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Logs&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['reports']==1) {
			                     echo 'Reports&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Reports&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                       <!--</div>-->
                        <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['affiliates']==1) {
			                     echo 'Affiliates&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Affiliates&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->
                        <td>
                       <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['makepurchases']==1) {
			                     echo 'Make Purchases&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Make Purchases&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
			             <td>
                        <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['mobilecoupons']==1) {
			                     echo 'Mobile Coupons&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Mobile Coupons&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td>
                        <!--</div>-->
                        <td>
                       <!--<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">-->
	                         <label class="checkbox-inline">
                            <?php if($subaccount['Subaccount']['qabots']==1) {
			                     echo 'Q&A SMS Bots&nbsp;&nbsp<font style="color:green;font-weight:bold">YES</font>';
			                 }else{
			                     echo 'Q&A SMS Bots&nbsp;&nbsp<font style="color:red;font-weight:bold">NO</font>';
			                 }?>
			                 </label>
			                 </td></tr>
                        <!--</div>-->
                        </table>
                   
			</div>		
    </div>
