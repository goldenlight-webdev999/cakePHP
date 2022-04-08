<style>
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {
    max-width: 1000px;
    min-height: 71px;
    min-width: 650px;
    padding: 10px;
    position: relative;
}
</style>
<script language="javascript">

function checknote(){

    var note = document.getElementById('contactnote').value;
    
    if(note==''){
     
     alert('Please enter a note');
     return false;
    }
}

</script>
 
<div class="clearfix"></div>
<div class="portlet box blue-dark">
<div class="portlet-title">
	<div class="caption">
		Edit Note
	</div>
</div>


<div class="portlet-body">
    <div class="contacts form">
    <?php echo $this->Form->create('Note', array('id' => 'addNoteForm','onsubmit'=>'return checknote();'));?>
        <input type="hidden" id="contactid" value="<?php echo $contactid ?>"/>
    	<div class="form-group">	
    	<label>Note</label>	
    	<?php echo $this->Form->textarea('ContactNote.note',array('div'=>false,'label'=>false,'rows'=>'7','id'=>'contactnote','maxlength'=>'1600','value'=>$contactNotes['ContactNote']['note'],'style'=>'width:600px'))?>
    	</div>
    	
        <br/><input type="Submit" value="Save" class="btn btn-primary">
        <?php 
        echo $this->Form->end();
        ?>
    </div>
</div>
</div>
</div>
</div>