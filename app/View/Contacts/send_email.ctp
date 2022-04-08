<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>		
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>
<style>
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {
    min-height: 300px;
    min-width: 450px;
    position: relative;
}
#flashMessage {
	font-size: 16px;
	font-weight: normal;
}       
.message {
	background: #f6fff5 url("<?php echo SITE_URL; ?>/app/webroot/img/flashimportant.png") no-repeat scroll 15px 12px / 24px 24px;
	border: 1px solid #97db90;
	border-radius: 5px;
	color: #000;
	font-size: 13px;
	margin-bottom: 10px;
	padding: 13px 13px 13px 48px;
	text-decoration: none;
	text-shadow: 1px 1px 0 #fff;
}
.nyroModalCont {
    top: 0 !important;
}
.nyroModalCloseButton{
	top: 5px !important;
}
</style>
<?php if($_GET['status']=="success"){ ?>
	<script>
		jQuery(document).ready(function($) {
		  setTimeout(function(){ 
		  window.parent.jQuery.fancybox.close();
		  //window.parent.location.reload()
		  }, 2000);  
		 });
	</script>
<?php } ?>
<?
echo $this->Html->script('ckeditor/ckeditor');
?> 
<div>
<div class="clearfix"></div>
<div class="portlet box blue-dark">
<div class="portlet-title">
	<div class="caption">
		Send Email
	</div>
</div>

<div class="portlet-body">

<div class="contacts form">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->Form->create('contacts', array('controller'=> 'contacts', 'enctype'=>'multipart/form-data', 'action'=>'send_email/'.$email, 'id' => 'sendEmailForm')); ?>
       
	<div class="form-group">
	<!--Javascript validation goes Here---------->
		<div id="validationMessages" style="display:none"></div>
		<!--Javascript validation goes Here---------->
	<label>From</label>
	    <?php echo $this->Form->input('emailfrom', array('class' =>'form-control','label' => false,'readonly'=>false,'div' => false,'value'=>$fromemail));?>
	</div>
	
	<div class="form-group">
	 <label>To</label>
		<?php echo $this->Form->input('emailto', array('class' =>'form-control','label' => false,'readonly'=>true,'div' => false,'value'=>$email));?>
	</div>
	
	<div class="form-group">
	 <label>Cc</label>
		<?php echo $this->Form->input('emailcc', array('class' =>'form-control','label' => false,'readonly'=>false,'div' => false,'value'=>''));?>
	</div>
	
	<div class="form-group">
	 <label>Bcc</label>
		<?php echo $this->Form->input('emailbcc', array('class' =>'form-control','label' => false,'readonly'=>false,'div' => false,'value'=>''));?>
	</div>
	
	<div class="form-group">
	<label>Reply To</label>
	    <?php echo $this->Form->input('replyto', array('class' =>'form-control','label' => false,'readonly'=>false,'div' => false,'value'=>$fromemail));?>
	</div>
	
	<div class="form-group">
	<label>Request Read Receipt</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="This will send a request read receipt when email is sent. However, this does not always get added and it depends greatly on the different email servers." data-original-title="Read Receipt" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
	<div class="radio-list" >	
	    <input name="data[contacts][readreceipt]" value="1" type="checkbox" id="readreceipt" />
	</div>
	</div>
	
	<div class="form-group">
            <label>File Attachment</label>
            <div class="input">
                <input type="file" name="data[contacts][attachments]" id="file">
			</div>
    </div>
	
	<div class="form-group">
	 <label>Subject</label>
		<?php echo $this->Form->input('emailsubject', array('class' =>'form-control','label' => false,'readonly'=>false,'div' => false,'value'=>''));?>
	</div>
	
	<div class="form-group">
            <label>Body</label>
            <div class="input">
               <textarea  name="data[contacts][emailbody]"  id="editor_office2003" class="ckeditor" placeholder="Email Body"></textarea>
			</div>
    </div>
    <!--<div class="note note-info">
    <span class="text-muted"><b>Credits:</b> 1 credit will be deducted for each page faxed + 1 credit per minute for fax duration. 
    <br/>Example: If you are faxing 5 pages, and it takes 1 minute and 45 seconds, 7 credits will be deducted.
    </span>
    </div>-->
	<br/>
	<div class="form-action">
	<input type="Submit" value="Send" class="btn btn-primary">
	</div>
<?php 
echo $this->Form->end();
?>
</div>
</div>
</div>
</div>
</div>
</div>