<script language="javascript">

function flashvalidation11(){

 var a = document.getElementById('ContactName').value;
 var b = document.getElementById('ContactPhoneNumber').value;
 var group_id = document.getElementById('KeywordId').value;
 var bday = document.getElementById('birthday').value;
 var countrycodetel = document.getElementById('countrycodetel').value;
 <?php if(API_TYPE==0 || API_TYPE==5 || API_TYPE==7) {?>
 var fax = document.getElementById('ContactFaxNumber').value;
 var faxcountrycodetel = document.getElementById('faxcountrycodetel').value;
 <?} ?>
 //alert(group_id);
 

if(group_id==''){
 
 alert('Please select  a group');
 return false;
 
 }
 
 if(countrycodetel==''){
 
 alert('Please select a phone country code');
 return false;
 
 }
 
 /*if(a==''){
 
 alert('Please enter a name');
 return false;
 
 }*/
 if(b==''){
 
 alert('Please enter a phone number');
 return false;
 
 }else{
    var phone =(/^[+0-9]+$/);

    if(!b.match(phone)){
      alert("Please enter correct phone number with NO spaces, dashes, or parentheses.");
      return false;  
    }
 }
 
 <?php if(API_TYPE==0 || API_TYPE==5 || API_TYPE==7) {?>
 if(fax!=''){
 
    var faxphone =(/^[+0-9]+$/);

    if(!fax.match(faxphone)){
      alert("Please enter correct fax number with NO spaces, dashes, or parentheses.");
      return false;  
    }
 }
 <?} ?>

 if (bday !=''){
    if (!isValidDate(bday)) {
        alert('Please enter date in format: YYYY-MM-DD');
        return false;
    }
 }
 
}

function isValidDate(dateString) {

  var regEx = /^\d{4}-\d{2}-\d{2}$/;
  //alert (dateString.match(regEx));
  
  if(!dateString.match(regEx)){
    return false;  // Invalid format
  }else{

     var d;

     d = new Date(dateString);

     if(!((d = new Date(dateString))|0)){
        return false;
     }else{
        return true;
     }
     
  }

  
}

</script>

<?php include('./qrcdr/lib/countrycodes.php');?>

<div>
<div class="clearfix"></div>
<div class="portlet box blue-dark">
<div class="portlet-title">
	<div class="caption">
		Add Contact
	</div>
</div>



<div class="portlet-body">

<!-- login box-->
<div class="loginbox">
	<div class="loginner">
		<div class="login-left">


<div class="contacts form">
<?php echo $this->Form->create('Contact', array('id' => 'addContactForm','onsubmit'=>'return flashvalidation11();'));?>
	<input type="hidden" value="<?php echo $source; ?>" name="data[Contact][source]">
	<div class="feildbox form-group" >
			<label for="some21">Groups</label>	

		
		
			<select id="KeywordId" class="form-control txt" multiple="multiple" name="data[Group][id][]" >
			<?php
			
			foreach($groupname as $Groups){ 
		
			//$groupid=$messages['Group']['id'];
			if($Groups['Group']['keyword']!='?')
			{
			
			?>
    <option value="<?php echo $Groups['Group']['id']; ?>"><?php echo ucwords($Groups['Group']['group_name']).'('.$Groups['Group']['totalsubscriber'].')'; ?></option>
    <?php }} ?>
			
</select>
			
	
	</div>
		
		
	<div class="form-group" >	
			<label>Name</label>	
	<?php echo $this->Form->input('name',array('class' =>'form-control','div'=>false,'label'=>false));?>
	</div>
	
	<div class="form-group" style="width:35%;float:left">
	    <label>Prefix</label>
	    
	    <?php
            $output = "<select class=\"form-control\" name=\"data[Contact][countrycodetel]\" id=\"countrycodetel\">";
            foreach ($countries as $i=>$row) {
                if(COUNTRY==substr($row['name'],0,strpos($row['name'],'(')-1)){
                    $selected="selected";
                }else{
                    $selected="";
                }
                $output .= "<option value=\"".$row['code']."\" label=\"".$row['name']."\" ".$selected.">".$row['name']."</option>\n";
           }
           $output .= '</select>';
           echo $output;
        ?> 
        
	</div>
	
	<div class="form-group" style="width:62%;float:right">	
			<label>Phone Number</label>	
			
	        <?php echo $this->Form->input('phone_number',array('class' =>'form-control','div'=>false,'label'=>false,'placeholder'=>'Numeric Only. Format: 2025248725')); ?>
	</div>
	
	<?php if(API_TYPE==0 || API_TYPE==5 || API_TYPE==7) {?>
	
	<div class="form-group" style="width:35%;float:left">
	    <label>Prefix</label>
	    
	    <?php
            $output = "<select class=\"form-control\" name=\"data[Contact][faxcountrycodetel]\" id=\"faxcountrycodetel\">";
            foreach ($countries as $i=>$row) {
                if(COUNTRY==substr($row['name'],0,strpos($row['name'],'(')-1)){
                    $selected="selected";
                }else{
                    $selected="";
                }
                $output .= "<option value=\"".$row['code']."\" label=\"".$row['name']."\" ".$selected.">".$row['name']."</option>\n";
           }
           $output .= '</select>';
           echo $output;
        ?> 
        
	</div>
	
	<div class="form-group" style="width:62%;float:right">	
			<label>Fax Number</label>	
			
	        <?php echo $this->Form->input('fax_number',array('class' =>'form-control','div'=>false,'label'=>false,'placeholder'=>'Numeric Only. Format: 2025248725')); ?>
	</div>
	
	<?} ?>
	
    <div class="form-group" >	
			<label>Email</label>	
            <input type="email" class="form-control" id="email" name="data[Contact][email]"  />
    </div>
    
    <div class="form-group" >	
			<label>Birthday</label>	
            <input type="date" class="form-control" id="birthday" name="data[Contact][birthday]" placeholder="Format: YYYY-MM-DD" />
    </div>
    
    <div class="form-group" >	
			<label>City</label>	
            <input type="text" class="form-control" id="city" name="data[Contact][city]"  />
    </div>
    <?php if($country=="United States") {
    $states = array(
      '' => "",
      'AL' => 'Alabama',
      'AK' => 'Alaska',
      'AZ' => 'Arizona',
      'AR' => 'Arkansas',
      'CA' => 'California',
      'CO' => 'Colorado',
      'CT' => 'Connecticut',
      'DE' => 'Delaware',
      'DC' => 'District Of Columbia',
      'FL' => 'Florida',
      'GA' => 'Georgia',
      'HI' => 'Hawaii',
      'ID' => 'Idaho',
      'IL' => 'Illinois',
      'IN' => 'Indiana',
      'IA' => 'Iowa',
      'KS' => 'Kansas',
      'KY' => 'Kentucky',
      'LA' => 'Louisiana',
      'ME' => 'Maine',
      'MD' => 'Maryland',
      'MA' => 'Massachusetts',
      'MI' => 'Michigan',
      'MN' => 'Minnesota',
      'MS' => 'Mississippi',
      'MO' => 'Missouri',
      'MT' => 'Montana',
      'NE' => 'Nebraska',
      'NV' => 'Nevada',
      'NH' => 'New Hampshire',
      'NJ' => 'New Jersey',
      'NM' => 'New Mexico',
      'NY' => 'New York',
      'NC' => 'North Carolina',
      'ND' => 'North Dakota',
      'OH' => 'Ohio',
      'OK' => 'Oklahoma',
      'OR' => 'Oregon',
      'PA' => 'Pennsylvania',
      'RI' => 'Rhode Island',
      'SC' => 'South Carolina',
      'SD' => 'South Dakota',
      'TN' => 'Tennessee',
      'TX' => 'Texas',
      'UT' => 'Utah',
      'VT' => 'Vermont',
      'VA' => 'Virginia',
      'WA' => 'Washington',
      'WV' => 'West Virginia',
      'WI' => 'Wisconsin',
      'WY' => 'Wyoming',
    ); 
    ?>
    <div class="form-group" >	
			<label>State</label>	
            <?php echo $this->Form->input('state',array('type' =>'select','class' =>'form-control', 'div'=>false,'label'=>false, 'options'=>$states))?>
    </div>
    <?php } elseif($country=="Canada") {
    $states = array(
        '' => "",
        'AB' => "Alberta",
        'BC' => "British Columbia",
        'MB' => "Manitoba",
        'NB' => "New Brunswick",
        'NL' => "Newfoundland",
        'NT' => "Northwest Territories",
        'NS' => "Nova Scotia",
        'NU' => "Nunavut",
        'ON' => "Ontario",
        'PE' => "Prince Edward Island",
        'QC' => "Quebec",
        'SK' => "Saskatchewan",
        'YT' => "Yukon",
        );
    ?>
    <div class="form-group" >	
			<label>Province</label>	
            <?php echo $this->Form->input('state',array('type' =>'select','class' =>'form-control', 'div'=>false,'label'=>false, 'options'=>$states))?>
    </div>
    <?php } ?>
    
    <div class="form-group" >	
			<label>Postal Code</label>	
            <input type="text" class="form-control" id="zipcode" name="data[Contact][zip]"  />
    </div>
    
    <fieldset>
        <legend>Custom Fields</legend>
        <div class="note note-info">
	        Use these custom fields for anything you want. Might be an account #, employee ID, department name, role, etc...Then use the merge tags (<b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>) to output the data when sending bulk SMS
	    </div>
        <div class="form-group" >	
			<label>Custom Field 1</label>	
            <input type="text" class="form-control" id="custom1" name="data[Contact][custom1]"  />
        </div>
        <div class="form-group" >	
			<label>Custom Field 2</label>	
            <input type="text" class="form-control" id="custom2" name="data[Contact][custom2]"  />
        </div>
        <div class="form-group" >	
			<label>Custom Field 3</label>	
            <input type="text" class="form-control" id="custom3" name="data[Contact][custom3]"  />
        </div>
    </fieldset>
	<!--<label>NO spaces, dashes, or parentheses in phone numbers</label>
      <br/><label><font style="color:red">Include country code in the number</font></label>
        <br/><label>US Example: 2025248725</label>
        <label>UK Example: 7481340516</label>-->
        
<br/><input type="Submit" value="Save" class="btn btn-primary">
<?php 
echo $this->Form->end();

?>
</div>
	</div>
	</div>
</div>
</div>
	</div>
	</div>
</div>
