<script language="javascript">

function validation(){

 var phonenumber = document.getElementById('phonenumber').value;
 var api_type = document.getElementById('api_type').value;
 
 if(api_type==0 || api_type==5){
   var phonesid = document.getElementById('phonesid').value;
 }
 
 if(phonenumber==''){
    alert('Please enter a phone number.');
    return false;
 }else{
    var phone =(/^[+0-9]+$/);

    if(!phonenumber.match(phone)){
      alert("Please enter correct phone number, including country code, with NO spaces, dashes, or parentheses.");
      return false;  
    }
 }
 
 if(api_type==0 || api_type==5){ 
    if(phonesid==''){
        alert('Please enter the phone SID/ID associated to this number.');
        return false;
    }
 } 

 
}
</script>
<span style="background-color:#F4D03F;color:#fff;font-weight:bold;padding:7px;">This is for adding any pre-existing numbers you already have in your gateway account to use in the platform</span>
<div>
<?php echo $this->Form->create('users',array('action'=>'addnumber/'.$id,'onsubmit'=>'return validation();'));?>
<input type='hidden' id="api_type" value="<?php echo $api_type?>">
<label>Phone Number</label>
<input type="text" id="phonenumber" name="data[User][number]" size="50" placeholder="Format: 12025248725">

<?php 
    if($api_type==0 || $api_type==5){ ?>
        <label>Phone SID/ID</label>
        <input type="text" id="phonesid" name="data[User][phonesid]" size="50">
<? } ?>      
<?php 
    if($api_type==1){ ?>
        <label>Country</label>
        <select name="data[User][country]">
            <option value="AU">Australia</option>
            <option value="CA">Canada</option>
            <option value="GB">United Kingdom</option>
            <option value="US">United States</option>
    </select>
<? } ?>       
<fieldset style="margin-bottom:20px">
		  <legend><font style="font-size: 18px">Phone Capabilities</font></legend>
<label>SMS</label>
<select name="data[User][sms]">
    <option value=1>Yes</option>
    <option value=0>No</option>
</select><br><br>
<?php 
    if($api_type==0 || $api_type==3 || $api_type==4 || $api_type==5 || $api_type==6 || $api_type==7){ ?>
    <label>MMS</label>
    <select name="data[User][mms]">
        <option value=1>Yes</option>
        <option value=0>No</option>
    </select><br><br>
<? } ?>
<label>Voice</label>
<select name="data[User][voice]">
    <option value=1>Yes</option>
    <option value=0>No</option>
</select><br><br>
<?php 
    if($api_type==0 || $api_type==5 || $api_type==7){ ?>
    <label>Fax</label>
    <select name="data[User][fax]">
        <option value=1>Yes</option>
        <option value=0>No</option>
    </select>
<? } ?>  
</fieldset>
<br>
<?php echo $this->Form->end(__('Submit', true));?>
</form> 
</div>
