<link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
<script src="<?php echo SITE_URL; ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>		
<script type="text/javascript" src="https://api.filepicker.io/v1/filepicker.js"></script> 
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript" ></script>
<script src="<?php echo SITE_URL; ?>/assets/pages/scripts/components-bootstrap-maxlength.js" type="text/javascript" ></script>
<script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.0.0.js"></script>

<?php
	echo $this->Html->css('nyroModal');
	echo $this->Html->script('jQvalidations/jquery.validation.functions');
	echo $this->Html->script('jQvalidations/jquery.validate');
	echo $this->Html->script('jquery.nyroModal.custom');
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('a.nyroModal').nyroModal();

	});
</script>
<?php if($_GET['status']=="success"){ ?>
	<script>
		jQuery(document).ready(function($) {
		  setTimeout(function(){ 
		  window.parent.jQuery.fancybox.close();
		  //window.parent.close(); 
		  window.parent.location.reload()
		  }, 200);  
		 });
	</script>
<?php } ?>
<style>			
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
			
<script type="text/javascript">

var SPINTAX_PATTERN = /\{[^"\r\n\}]*\}/;
var spin = function (spun) {
	var match;
	while (match = spun.match(SPINTAX_PATTERN)) {
		match = match[0];
		var candidates = match.substring(1, match.length - 1).split("|");
		spun = spun.replace(match, candidates[Math.floor(Math.random() * candidates.length)])
	}
	return spun;
}
var spin_countVariations = function (spun) {
	spun = spun.replace(/[^{|}]+/g, '1');
	spun = spun.replace(/\{/g, '(');
	spun = spun.replace(/\|/g, '+');
	spun = spun.replace(/\}/g, ')');
	spun = spun.replace(/\)\(/g, ')*(');
	spun = spun.replace(/\)1/g, ')*1');
	spun = spun.replace(/1\(/g, '1*(');
	return eval(spun);
}
function spintext(){
//	$("#Preview1").val(spin($('#message1').val())+ ' STOP to End');
//$("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
if (document.getElementById("api_type").value==0 || document.getElementById("api_type").value==3 || document.getElementById("api_type").value==4 || document.getElementById("api_type").value==5 || document.getElementById("api_type").value==6 || document.getElementById("api_type").value==7){
    if($('#mms').prop('checked')==false){
        if($('#mandatorymessage').val()!=''){
    	 $("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
        }else{
    	 $("#Preview1").val(spin($('#message1').val()));
        }
    }else{
        if($('#mandatorymessage').val()!=''){
    	    $("#Preview1").val(spin($('#message2').val())+ ' ' + $('#mandatorymessage').val());
        }else{
    	    $("#Preview1").val(spin($('#message2').val()));
        }
    }
}else{
    if($('#mandatorymessage').val()!=''){
    	 $("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
    }else{
    	 $("#Preview1").val(spin($('#message1').val()));
    }
}
	var str = $("#Preview1").val();
	//var strutf8decode = utf8_decode($("#Preview1").val());
	var gsm = is_gsm(str);
	if($('#mms').prop('checked')==true){	
	   var credits = <?php echo CHARGECREDITSMMS?>;
	}else if (!gsm){//if (str.length != strutf8decode.length){
	   var credits = Math.ceil(str.length/70);
	}else{
	   var credits = Math.ceil(str.length/160);
	}
	characters.value = '# of Characters: ' + str.length;
	//var credits = Math.ceil(str.length/160);
	if (document.forms[0].elements["groups"].checked){
			var selectedValues = [];   
			var groupcnt=0;
			var holdgroupcnt=0;
			$("#KeywordId :selected").each(function(){
				selectedValues.push($(this).val()); 
			});
			for (var i=0, iLen=selectedValues.length; i<iLen; i++) {
				holdgroupcnt = parseInt(holdgroupcnt) + parseInt(groupcnt);
				groupcnt = document.getElementById(selectedValues[i]).value;
			}
			holdgroupcnt = (parseInt(holdgroupcnt) + parseInt(groupcnt)) * parseInt(credits);
			//document.getElementById("showCredits").style.display = "block";
			//if (document.getElementById("smsbalance").value < holdgroupcnt){
			//	document.getElementById("creditsused").style.color= "red";
			//	document.getElementById("creditsused").innerHTML= holdgroupcnt;
			//}else{
			//	document.getElementById("creditsused").style.color= "white";
			//	document.getElementById("creditsused").innerHTML= holdgroupcnt;
			//}
		}else{
			var selectedValues = [];   
			var contactcnt=0;
			$("#KeywordId1 :selected").each(function(){
			selectedValues.push($(this).val()); 
			});
			for (var i=0, iLen=selectedValues.length; i<iLen; i++) {   
			}
			//document.getElementById("showCredits").style.display = "block";
			//if (document.getElementById("smsbalance").value < (i * parseInt(credits))){
			//	document.getElementById("creditsused").style.color= "red";
			//	document.getElementById("creditsused").innerHTML= i * parseInt(credits);
			//}else{
			//	document.getElementById("creditsused").style.color= "white";
			//	document.getElementById("creditsused").innerHTML= i * parseInt(credits);
			//}
		}
	}
	function spinvariations(){
		if (document.getElementById("api_type").value==0 || document.getElementById("api_type").value==3 || document.getElementById("api_type").value==4 || document.getElementById("api_type").value==5 || document.getElementById("api_type").value==6 || document.getElementById("api_type").value==7){
        if($('#mms').prop('checked')==false){
            alert('# of possible variations: ' + spin_countVariations($('#message1').val())); /* 9 */
        }else{
            alert('# of possible variations: ' + spin_countVariations($('#message2').val())); 
        }
    }else{
        alert('# of possible variations: ' + spin_countVariations($('#message1').val()));
    }
	}
	function spintext2(){
		//$("#Preview1").val(spin($('#message2').val())+ ' STOP to End');
		//$("#Preview1").val(spin($('#message2').val())+ ' ' + $('#mandatorymessage').val());
		if($('#mandatorymessage').val()!=''){
	    $("#Preview1").val(spin($('#message2').val())+ ' ' + $('#mandatorymessage').val());
    }else{
	    $("#Preview1").val(spin($('#message2').val()));
    }
		var str = $("#Preview1").val();
		characters.value = '# of Characters: ' + str.length;
		var credits = <?php echo CHARGECREDITSMMS?>;
		if (document.forms[0].elements["groups"].checked){
			var selectedValues = [];   
			var groupcnt=0;
			var holdgroupcnt=0;
			$("#KeywordId :selected").each(function(){
				selectedValues.push($(this).val()); 
			});  
			for (var i=0, iLen=selectedValues.length; i<iLen; i++) { 
				holdgroupcnt = parseInt(holdgroupcnt) + parseInt(groupcnt);
				groupcnt = document.getElementById(selectedValues[i]).value; 
			}
				holdgroupcnt = (parseInt(holdgroupcnt) + parseInt(groupcnt)) * parseInt(credits);    
				//document.getElementById("showCredits").style.display = "block"; 
			//if (document.getElementById("smsbalance").value < holdgroupcnt){
			//	document.getElementById("creditsused").style.color= "red";
			//	document.getElementById("creditsused").innerHTML= holdgroupcnt ;
			//}else{
			//	document.getElementById("creditsused").style.color= "white";
			//	document.getElementById("creditsused").innerHTML= holdgroupcnt;
			//}
		}else{
			var selectedValues = [];   
			var contactcnt=0;
			$("#KeywordId1 :selected").each(function(){
				selectedValues.push($(this).val()); 
			});
			for (var i=0, iLen=selectedValues.length; i<iLen; i++) {
			}
			//document.getElementById("showCredits").style.display = "block";
			//if (document.getElementById("smsbalance").value < (i * parseInt(credits))){
			//	document.getElementById("creditsused").style.color= "red";
			//	document.getElementById("creditsused").innerHTML= i * parseInt(credits);
			//}else{
			//	document.getElementById("creditsused").style.color= "white";
			//	document.getElementById("creditsused").innerHTML= i * parseInt(credits);
			//}
		}
	}

	function spinvariations2(){
		alert('# of possible variations: ' + spin_countVariations($('#message2').val())); 
	}

	function checkrotate(){
		var rotate_number = $('#rotate_number').val();
		if(rotate_number==0){
			$('#rotate_number').val(1);
		}else{
			$('#rotate_number').val(0);
		}


	}

	function checkalphasender(){

		var alphasenderid = $('#alphasenderid').val();
		if(alphasenderid==0){
			$('#alphasenderid').val(1);
		}else{
			$('#alphasenderid').val(0);
		}

	}
</script>
<script>
	jQuery(function(){
		jQuery("#lastName").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please enter value"
		});
		jQuery("#sent_on").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please select the date/time"
		});
	$('#alphasenderid').click(function(){
			if($('#alphasenderid').prop('checked')==true){
				$('#alphasenderid_show').show();
							$('#message').val("");
							$('#mandatory_message_show').hide();
							$('#rotate_number').val(0);
							$('#rotate_number').prop('disabled', true);
							$('#mandatorymessage').val("txt STOP to <?php echo $users['User']['assigned_number']?> to end");
			}else if($('#alphasenderid').prop('checked')==false){
				$('#alphasenderid_show').hide();
							$('#message').val("STOP to end");
							$('#mandatory_message_show').show();
							$('#alphasenderid_input').val("");
							$('#rotate_number').prop('disabled', false);
							$('#mandatorymessage').val("STOP to end");
							
			}
		});

	});
	
	function is_gsm(instring){
     var valid_gsm_keycodes = ['@','Δ',' ','0','¡','P','¿','p',
        '£','_','!','1','A','Q','a','q',
        '$','Φ','"','2','B','R','b','r',
        '¥','Γ','#','3','C','S','c','s',
        'è','Λ','¤','4','D','T','d','t',
        'é','Ω','%','5','E','U','e','u',
        'ù','Π','&','6','F','V','f','v',
        'ì','Ψ','\\','7','G','W','g','w',
        'ò','Σ','(','8','H','X','h','x',
        'Ç','Θ',')','9','I','Y','i','y',
        "\n",'Ξ','*',':','J','Z','j','z',
        'Ø',"\x1B",'+',';','K','Ä','k','ä',
        'ø','Æ',',','<','L','Ö','l','ö',
        "\r",'æ','-','=','M','Ñ','m','ñ',
        'Å','ß','.','>','N','Ü','n','ü',
        'å','É','/','?','O','§','o','à','^','{','}','~','`','[',']','€','|',"'"];
        
        for(var i = 0; i < instring.length; i++) {
            if(!inArray(instring[i], valid_gsm_keycodes)) 
            return false;
        }

        return true;
    
}

function arrayCompare(a1, a2) {
    if (a1.length != a2.length) return false;
    var length = a2.length;
    for (var i = 0; i < length; i++) {
        if (a1[i] !== a2[i]) return false;
    }
    return true;
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(typeof haystack[i] == 'object') {
            if(arrayCompare(haystack[i], needle)) return true;
        } else {
            if(haystack[i] == needle) return true;
        }
    }
    return false;
}
</script>
<script type="text/javascript">
	window.onload=function() {
		if (document.forms[0].elements["contacts"].checked){
			document.forms[0].elements["contacts"].onClick=showHidediv();
		}
		if (document.forms[0].elements["groups"].checked){
			document.forms[0].elements["groups"].onClick=showHide();
		}

	}
	$(document).ready(function (){
		$('textarea[maxlength]').live('keyup change', function() {
			var str = $(this).val()
			var mx = parseInt($(this).attr('maxlength'))
			if (str.length > mx) {
				$(this).val(str.substr(0, mx))
				return false;
			}
		})
		if (document.forms[0].elements["groups"].checked){
			document.forms[0].elements["groups"].onClick=showHide();
		}
		if (document.forms[0].elements["contacts"].checked){
			document.forms[0].elements["contacts"].onClick=showHidediv();
		}
		var messageview= $('#message1').val();
		var id= $('#mobilespagesId').val();
		if(id>0){
			$.ajax({
				url: "<?php  echo SITE_URL ?>/messages/mobile_pages/"+id,
				type: "POST",
				dataType: "html",
				success: function(response) {
					if(messageview!=''){
						var data = response;
						if($('#sms').prop('checked')==true){
							$('#message1').val(data);
						}else{
							$('#message1').val(data);
						}
						if($('#mms').prop('checked')==true){
							$('#message2').val(data);
						}
							$('#Preview1').val(data);
						return;
					}else{
						if($('#sms').prop('checked')==true){
							$('#message1').html(response);
						}else{
							$('#message1').html(response);
						}
						if($('#mms').prop('checked')==true){
							$('#message2').val(response);
						}
							$('#Preview1').html(response);
					}
				}
			});
		}
	});
	function confirmmessage(id){
		var message= $('#message1').val();
		if(id>0){
			$.ajax({
			url: "<?php  echo SITE_URL ?>/messages/checktemplatedata/"+id,
			type: "POST",
			dataType: "html",
			success: function(response) {
					if(message!=''){
						var data = response;
						if($('#sms').prop('checked')==true){
							$('#message1').val(data);
						}else{
							$('#message1').val(data);
						}
						if($('#mms').prop('checked')==true){
							$('#message2').val(data);
						}
							$('#Preview1').val(data);
						return;
					}else{
						if($('#sms').prop('checked')==true){
							$('#message1').html(response);
						}else{
							$('#message1').html(response);
						}
						if($('#mms').prop('checked')==true){
							$('#message2').val(response);
						}
							$('#Preview1').html(response);
					}
				}
			});
		}
	}
	function confirmpagemessage(id){ 
		var messageview= $('#message1').val();
		if(id>0){
			$.ajax({
				url: "<?php  echo SITE_URL ?>/messages/mobile_pages/"+id,
				type: "POST",
				dataType: "html",
				success: function(response) {
					if(messageview!=''){
						var data = response;
						if($('#sms').prop('checked')==true){
							$('#message1').val(data);
						}else{
							$('#message1').val(data);
						}
						if($('#mms').prop('checked')==true){
							$('#message2').val(data);
						}
							$('#Preview1').val(data);
						return;
					}else{
						if($('#sms').prop('checked')==true){
							$('#message1').val(response);
						}else{
							$('#message1').val(response);
						}
						if($('#mms').prop('checked')==true){
							$('#message2').val(response);
						}
							$('#Preview1').html(response);
					}
				}
			});
		}
	}
	function check_image(){
		if($('#mms').prop('checked')==true){
			$('#check_img_validation').val(2);
		}
	}
	function remove_image(){
		if($('#mms').prop('checked')==true){
			$('#check_img_validation').val(0);
		}
	}
</script>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId      :'<?php echo FACEBOOK_APPID;?>', 
			status     : true, 
			cookie     : true, 
			xfbml      : true  
		});
	};
	(function(d){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
		ref.parentNode.insertBefore(js, ref);
	}(document));
	function Fblogin(){
		FB.login(function(response) {  
			if (response.authResponse) {
				$('#fbloginid').empty();	
				$('#fblogoutid').append("<table style='margin-top: 7px'><tr><td style='vertical-align:center;padding:0px'><img src='<?php echo SITE_URL?>/img/FBShare.png' title='Post this message to your Facebook wall.'/></td><td style='vertical-align:center;padding:0px'><input type='checkbox' onchange='logout()' value='0' name='data[Message][logout]' id='fblogout' checked></td></tr></table>");
				var opts = {
				message : document.getElementById('message1').value,
				};
			} else {  
			alert('User cancelled login or did not fully authorize.');  
			}  
		}, {scope: 'publish_actions,user_photos',display:'dialog'}); 
	}	
	function logout(){
		FB.logout(function(response) {  
			if (response.authResponse){  
				$('#fblogoutid').empty();	
				$('#fbloginid').append("<table style='margin-top: 7px'><tr><td style='vertical-align:center;padding:0px'><img src='<?php echo SITE_URL?>/img/FBShare.png' title='Post this message to your Facebook wall.'/></td><td style='vertical-align:center;padding:0px'><input type='checkbox' onchange='Fblogin()' value='1' name='data[Message][Fblogin]' id='fblog'></td></tr></table>");
				alert('Facebook logout sucessful. Message will not be shared on Facebook.');  
			}
		});	
	}
</script>
<?php
	echo $this->Html->css('jquery-ui-1.8.16.custom');
	echo $this->Html->script('jquery-ui-timepicker-addon');
?>
<script>
	function updateslooce(){
		var tex = $("#message1").val();
		var msg = $("#Preview1").val();
		tex = tex.replace('[','');
		tex = tex.replace(']','');
		tex = tex.replace('~','');
		tex = tex.replace(';','');
		tex = tex.replace('`','');
		tex = tex.replace('"','');
		var len = tex.length;
		var count1 = (148-(len));
		$("#message1").val(tex);
		if(len > 148){
			tex = tex.substring(0,count1);
			$("#Preview1").val(tex);
			return false;
		}
		$("#limit2").val(count1);
		$("#Preview1").val(tex);
		$("#Preview1").val(tex + " STOP to End");
		var str = $("#Preview1").val();
		characters.value = '# of Characters: ' + str.length;
	}
	var count = "1588";
	function update(){
		var tex = $("#message1").val();
		var msg = $("#Preview1").val();
		/*tex = tex.replace('[','');
		tex = tex.replace(']','');
		tex = tex.replace('~','');
		tex = tex.replace(';','');
		tex = tex.replace('`','');
		tex = tex.replace('"','');*/
		var len = tex.length;
		var count1 = (1588-(len));
		$("#message1").val(tex);
		if(len > count){
			tex = tex.substring(0,count1);
			$("#Preview1").val(tex);
			return false;
		}
		$("#limit2").val(count1);
		//$("#Preview1").val(tex + " STOP to End");
		//$("#Preview1").val(tex + ' ' + $('#mandatorymessage').val());
		if($('#mandatorymessage').val()!=''){
	  $("#Preview1").val(tex + ' ' + $('#mandatorymessage').val());
	}else{
	  $("#Preview1").val(tex); 
	}
		var str = $("#Preview1").val();
		characters.value = '# of Characters: ' + str.length;
	}
	function update1(){
		var tex = $("#message2").val();
		var msg = $("#Preview1").val();
		/*tex = tex.replace('[','');
		tex = tex.replace(']','');
		tex = tex.replace('~','');
		tex = tex.replace(';','');
		tex = tex.replace('`','');
		tex = tex.replace('"','');*/
		var len = tex.length;
		var count1 = (1588-(len)); 
		$("#message2").val(tex);
		if(len > count){
			tex = tex.substring(0,count1);
			$("#Preview1").val(tex);
			return false;
		}
		$("#limit3").val(count1);
		$("#Preview1").val(tex);
		//$("#Preview1").val(tex + " STOP to End");
		//$("#Preview1").val(tex + ' ' + $('#mandatorymessage').val());
		if($('#mandatorymessage').val()!=''){
	  $("#Preview1").val(tex + ' ' + $('#mandatorymessage').val());
	}else{
	  $("#Preview1").val(tex); 
	}
		var str = $("#Preview1").val();
		characters.value = '# of Characters: ' + str.length;
	}
	function showHide() {
		var ele = document.getElementById("showHideDiv");
		var ele1 = document.getElementById("showHideDiv1");
		var ele2 = document.getElementById("showHideDiv2");
		var ele3 = document.getElementById("showHideDiv3");
		if(ele.style.display == "block") {
			ele.style.display = "block";
			ele1.style.display = "none";
			ele2.style.display = "block";
			ele3.style.display = "block";
		}
		else {
			ele.style.display = "block";
			ele1.style.display = "none";
			ele2.style.display = "block";
			ele3.style.display = "block";
		}
	}
	function showHidediv() {
		var ele = document.getElementById("showHideDiv");
		var ele1 = document.getElementById("showHideDiv1");
		var ele2 = document.getElementById("showHideDiv2");
		var ele3 = document.getElementById("showHideDiv3");
		if(ele1.style.display == "block") {
			ele1.style.display = "block";
			ele2.style.display = "block";
			ele.style.display = "none";
			ele3.style.display = "none";
				
		}
		else {
			ele.style.display = "none";
			ele2.style.display = "block";
			ele1.style.display = "block";
			ele3.style.display = "none";
		}
	}
function ValidateForm(form){
	if($('#alphasenderid').prop('checked')==true ){
		var alphasender = document.getElementById("alphasenderid_input").value;
		  if (alphasender == ''){
			 alert ("Please enter an alphanumeric sender ID");
			 return false;
		  }
	}
	var option1 = document.getElementById("groups").checked;
	var option2 = document.getElementById("contacts").checked;
	if(option1==false && option2==false){
		alert('Please pick a list, groups or contacts');
		return false;
	}
	var optionnew1 = document.getElementById("groups").value;
	var optionnew2 = document.getElementById("contacts").value;
	if(document.getElementById("KeywordId").value=='' && document.getElementById("KeywordId1").value=='')
	{
		alert( "Please select at least one from the list" );
		return false;
	}
	
	if($('#recurring').prop('checked')==true && $('#sent_on_one').val() ==''){
	    alert( "Please select an end date you want the recurring events to end" );
		return false;
	}  
	
	
	if($('#mms').prop('checked')==true){
		var mms_image=document.getElementById("check_img_validation").value;
		var pk_file=$('#pick_button').val();
		if(mms_image==0 && pk_file == ''){
			alert( "Please Upload a image" );
			return false;
		}
		var message2=$('#message2').val();	
	}
	if($('#sms').prop('checked')==true){
		var sms_msg=document.getElementById("message1").value;
		if(sms_msg==''){
			alert( "Please enter a message" );
			return false;
		}
	}
	if (document.getElementById("api_type").value==1 || document.getElementById("api_type").value==2){// || document.getElementById("api_type").value==3){
		var sms_msg=document.getElementById("message1").value;
		if(sms_msg==''){
			alert( "Please enter a message" );
			return false;
		}
	}
	if (document.getElementById("api_type").value==0 || document.getElementById("api_type").value==3 || document.getElementById("api_type").value==4 || document.getElementById("api_type").value==5 || document.getElementById("api_type").value==6 || document.getElementById("api_type").value==7){
		if (document.forms[0].elements["sms"].checked){
			//$("#Preview1").val(spin($('#message1').val())+ ' STOP to End');
			//$("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
			if($('#mandatorymessage').val()!=''){
	            $("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
            }else{
	            $("#Preview1").val(spin($('#message1').val()));
            }
		}else{
			//$("#Preview1").val(spin($('#message2').val())+ ' STOP to End');
			//$("#Preview1").val(spin($('#message2').val())+ ' ' + $('#mandatorymessage').val());
			if($('#mandatorymessage').val()!=''){
	            $("#Preview1").val(spin($('#message2').val())+ ' ' + $('#mandatorymessage').val());
            }else{
	            $("#Preview1").val(spin($('#message2').val()));
            }
		}
	}else{
		//$("#Preview1").val(spin($('#message1').val())+ ' STOP to End');
		//$("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
		if($('#mandatorymessage').val()!=''){
	        $("#Preview1").val(spin($('#message1').val())+ ' ' + $('#mandatorymessage').val());
        }else{
	        $("#Preview1").val(spin($('#message1').val()));
        }
	}
	var str = $("#Preview1").val();
	characters.value = '# of Characters: ' + str.length;
    //var strutf8decode = utf8_decode($("#Preview1").val());
    var gsm = is_gsm(str);

	if($('#mms').prop('checked')==true){	
	   var credits = <?php echo CHARGECREDITSMMS?>;
	   var mms = 1;
	}else if (!gsm){//if (str.length != strutf8decode.length){
	   var credits = Math.ceil(str.length/70);
	   var unicode = 1;
	}else{
	   var credits = Math.ceil(str.length/160);
	   var unicode = 0;
	   var mms = 0;  
	}
	if (document.forms[0].elements["groups"].checked){
			var selectedValues = [];   
			var groupcnt=0;
			var holdgroupcnt=0;
		$("#KeywordId :selected").each(function(){
			selectedValues.push($(this).val()); 
		});
		for (var i=0, iLen=selectedValues.length; i<iLen; i++) {
			holdgroupcnt = parseInt(holdgroupcnt) + parseInt(groupcnt);
			groupcnt = document.getElementById(selectedValues[i]).value;
		}
		holdgroupcnt = (parseInt(holdgroupcnt) + parseInt(groupcnt)) * parseInt(credits);
		//document.getElementById("showCredits").style.display = "block";

		if (document.getElementById("smsbalance").value < holdgroupcnt){

			//document.getElementById("creditsused").style.color= "red";
			//document.getElementById("creditsused").innerHTML= holdgroupcnt;

			if (unicode == 0 && mms == 0){
				alert('You do not have enough credits to send this message.');
			}else if(mms == 1){
				alert('You do not have enough credits to send this message. Keep in mind this message type is MMS and requires <?php echo CHARGECREDITSMMS?> credits per contact.');
			}else if(unicode == 1){
				alert('You do not have enough credits to send this message. This message contains unicode characters and any SMS that includes 1 or more non-GSM characters will be charged 1 credit for each 70 character segment.');
			}
				return false;
		}else{

			//document.getElementById("creditsused").style.color= "white";
			//document.getElementById("creditsused").innerHTML= holdgroupcnt;

			if (unicode == 1 && str.length > 70){
				var r = confirm ("This message contains unicode characters and is over 70 characters in length. Any SMS that includes 1 or more non-GSM characters will be charged 1 credit for each 70 character segment. Do you still want to send?");

				if (r == true) {
					return true;
				} else {
					return false;
				} 
			}
		}
	}else{
		var selectedValues = [];   
		var contactcnt=0;
		$("#KeywordId1 :selected").each(function(){
			selectedValues.push($(this).val()); 
		});
		for (var i=0, iLen=selectedValues.length; i<iLen; i++) { 
		}
		//document.getElementById("showCredits").style.display = "block";
		if (document.getElementById("smsbalance").value < (i * parseInt(credits))){
			//document.getElementById("creditsused").style.color= "red";
			//document.getElementById("creditsused").innerHTML= i * parseInt(credits);
			if (unicode == 0 && mms == 0){
				alert('You do not have enough credits to send this message.');
			}else if(mms == 1){
				alert('You do not have enough credits to send this message. Keep in mind this message type is MMS and requires <?php echo CHARGECREDITSMMS?> credits per contact.');
			}else if(unicode == 1){
				alert('You do not have enough credits to send this message. This message contains unicode characters and any SMS that includes 1 or more non-GSM characters will be charged 1 credit for each 70 character segment.');
			}
			return false;
		}else{
			//document.getElementById("creditsused").style.color= "white";
			//document.getElementById("creditsused").innerHTML= i * parseInt(credits);

			if (unicode == 1 && str.length > 70){
				var r = confirm ("This message contains unicode characters and is over 70 characters in length. Any SMS that includes 1 or more non-GSM characters will be charged 1 credit for each 70 character segment. Do you still want to send?");

				if (r == true) {
					return true;
				} else {
					return false;
				} 
			}
		}
	}
}
function CalcCredits(){
}
function popmessagepickwidget(value){
	if($('#sms').prop('checked')==true){
		$('#message1').val(value);
		$('#Preview1').val(value);
		$('#pick_button').val('');
	}else if($('#mms').prop('checked')==true){
		$('#message3').val(value);
		$('#Preview1').val();
		$('#resultpick').html('<div style="width: 80px;"><img style="height:70px!important;width: 70px!important;" class="thumbnail" title="preview" src='+value+'></div>');
		$('#pick_button').val('set');
	}
}
function popmessagepickwidgetnexmo(value){
	$('#message1').val(value);
	$('#Preview1').val(value);
	$('#pick_button').val('');
}
</script>
<script>
	$(document).ready(function(){
		if($('#sms').prop('checked')==true){
			$('#textmsg').show();
			$('#pickfile').show();
		}
		$('#mms').click(function(){
			$('#textmsg').hide();
			$('#upload').show();
			$('#pickfile').hide();
		});
		$('#sms').click(function(){
			$('#upload').hide();
			$('#textmsg').show();
			$('#pickfile').show();
		});
	});
	function twitterlogin(){
		window.location.href = "<?php echo SITE_URL?>/messages/send_message_twitter";
	}
</script>
<?php
$alphasender = $users['User']['alphasender'];
$country = $users['User']['user_country'];
if ($_REQUEST['contactid'] != ''){
    $apptid = $_REQUEST['apptid'];
    $contactid = $_REQUEST['contactid'];
    $apptdatetime = date('F j, Y \a\t g:i a',strtotime($_REQUEST['apptdate']));
    $cancelkeyword = $_REQUEST['cancelkeyword'];
    $confrmkeyword = $_REQUEST['confirmkeyword'];
    $reschedulekeyword = $_REQUEST['reschedulekeyword'];
    $remindermsg = "This is a reminder that your appointment is scheduled for ".$apptdatetime.". To confirm txt ".$confrmkeyword.". To cancel txt ".$cancelkeyword.". To reschedule txt ".$reschedulekeyword;
}
?>	
<div class="clearfix"></div>
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption font-green-sharp"><i class="fa fa-calendar-plus-o font-green-sharp"></i>
			Schedule Message 
		</div>
	</div>
	<div class="portlet-body">
		<?php echo $this->Session->flash(); ?>
		<!--<div class="loginbox">
			<div class="loginner">
				<div class="login-left">
					<div class="contacts form">-->
							<div class="form-body">		
								<?php echo $this->Form->create('Scheduler',array('action'=> 'add','name'=>'formname','enctype'=>'multipart/form-data','onsubmit'=>' return ValidateForm(this)'));?>
<input type="hidden" id="apptid" name="data[Message][apptid]" value="<?php echo $apptid?>"/>								
<input type="hidden" id="api_type" value="<?php echo API_TYPE?>"/>
<input type="hidden" id="smsbalance" value="<?php echo $this->Session->read('User.sms_balance')?>"/>
								<div class="form-group" style="width:420px;margin-top:5px">
									<label for="some21">Send From</label>
									<select class="form-control input-xlarge" name="data[Message][sendernumber]">
										<?php foreach($numbers as $number){ ?>
											<option value="<?php echo $number['number'];?>"><?php echo $number['number_details'] ?></option>
										<?php } ?>
									</select>
								</div>

<div class="form-body">
							<div class="showmessage" >
								<div style="float: right; margin-top: 0px; margin-right: 26px; width: 223px;"
								class="feildbox"><label>Preview</label>
									<?php echo $this->Form->input('Preview', array('type' => 'textarea', 'cols' => '15','rows'=>'12', 'class'=>'form-control','escape' => false,'label'=>false,'div'=>false,'id'=>"Preview1",'readonly'=>true,'style'=>'font-size:20px','maxlength'=>'160','data-role'=>'none')); ?>
									<input type="text" id="characters" value="# of Characters: " class="form-control" size="25" readonly style="margin-top:5px"/>
									<?php if(API_TYPE !=2){?>
									<input type="button" value="Variations" onclick="spinvariations();" class="btn green-meadow" style="padding-top:1px;padding-bottom:1px; margin-top:10px" />
									&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple-plum" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller'=>'messages','action' => 'spinhelp'), array('escape' =>false, 'class' => 'nyroModal','style'=>'margin-top:10px')); ?>
									<?php } ?>
								</div>
							</div>
</div>

							<div class="form-group">
								<?php if ($alphasender == 1){?>
									<div class="form-group">
										<label>Send from Alphanumeric Sender ID&nbsp;<a href="javascript:;" data-container="body" data-html="true" data-trigger="hover" data-content="You can send from an alphanumeric sender ID, however you must offer your users the ability to opt out by writing to your support team, calling your support phone line, or texting STOP to your actual number.<br/><br/>We recommend that you provide your users with a clear description in your terms of services or when they intially sign-up." data-original-title="Alphanumeric Sender ID" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
										</label>
										<div class="radio-list" >				
											<input name="data[Message][alphasenderid]" value="0" type="checkbox" id="alphasenderid" onclick="checkalphasender()"/>
										</div>
									</div>
									<div id="alphasenderid_show" style="display:none;width:50%">							
										<div class="form-group">
											<label>Sender ID&nbsp;<a href="javascript:;" data-container="body" data-html="true" data-trigger="hover" data-content="You may use any combination of 1 to 11 letters(A-Z/a-z) and numbers(0-9). 1 letter and no more than 11 alphanumeric characters may be used." data-original-title="Character Requirements" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
											</label>								                                                  
												<?php echo $this->Form->input('Message.alphasenderid_input',array('div'=>false,'label'=>false, 'class' => 'form-control input-xlarge','id'=>'alphasenderid_input','maxlength'=>'11','placeholder'=>'Aphanumeric Sender ID','pattern'=>'[a-zA-Z0-9]+', 'title'=>'Alphanumeric Sender ID should only contain any combination of 1 to 11 letters(A-Z/a-z) and numbers(0-9)'))?>						
										</div>						
                                    </div>
								<?php } ?>
								<div class="picklist">
									<label for="some21">Pick List&nbsp;<span class="required_star"></span></label>	
										<?php if($groupid!= '') { ?>
											<input type="radio" id="groups" name="data[pick][id]" value="1" checked onclick="return showHide();" /> Groups
										<?php }else{ ?>
											<input type="radio" checked id="groups" name="data[pick][id]" value="1" onclick="return showHide();" /> Groups
										<?php } ?>										
										<?php if($contactid != '') { ?>
											<input type="radio" id="contacts" name="data[pick][id]" value="2" checked onclick="return showHidediv();" /> Contacts
										<?php }else{ ?>
											<input type="radio" id="contacts" name="data[pick][id]" value="2" onclick="return showHidediv();" /> Contacts
										<?php } ?>
								</div>		
							</div>
							<div id="showHideDiv" style="display:none;" class="form-group" style="width:200px;margin-top:5px">	
								<label for="some21">Groups<span class="required_star"></span></label>	
								<select id="KeywordId" class="form-control input-xlarge" multiple="multiple" name="data[Keyword][id][]" >
									<?php
									foreach( $Group as $Groups){ 
										if($Groups['Group']['keyword']!='?'){ ?>
											<option <?php if($Groups['Group']['id']==$groupid){?>selected <?php }?>value="<?php echo $Groups['Group']['id']; ?>">		<?php echo ucwords($Groups['Group']['group_name']).'('.$Groups['Group']['totalsubscriber'].')'; ?></option>
									<?php }
									} ?>
								</select>
									<?php
									foreach( $Group as $Groups){ 
										if($Groups['Group']['keyword']!='?'){ ?>
											<input type="hidden" id="<?php echo $Groups['Group']['id']; ?>" value="<?php echo $Groups['Group']['totalsubscriber']; ?>"/>
									<?php }
									} ?>
								<input type="hidden" id="holdcount" value="0"/>
							</div>
							<div id="showHideDiv1" style="display:none;">
								<label for="some21">Name|Group Name|Phone Number&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Only the most recent 1000 contacts added will be returned in this list. This is to prevent slow load times with very large lists and large form submissions." data-original-title="Contacts Returned" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a></label>	
								<!--<select id="KeywordId1" class="form-control input-xlarge" multiple="multiple"
									  name="data[Contact][phone][]" >-->
									<select data-show-subtext="true" data-live-search="true" id="KeywordId1" class="form-control input-xlarge selectpicker" multiple="multiple" name="data[Contact][phone][]" >
									<?php
									foreach( $contactnumber as $contactnumber){ 
										if($contactnumber['Contact']['name']==''){
											$name= "";
										}else{
											$name=$contactnumber['Contact']['name'];
										}
									?>
										<option <?php if($contactnumber['Contact']['id']==$contactid){?>selected <?php }?> style="text-align:left;" value="<?php echo $contactnumber['Contact']['id'];?>"><?php echo $name; ?> <b>|</b> <?php echo $contactnumber['Group']['group_name']; ?> <b>|</b> <?php echo $contactnumber['Contact']['phone_number']; ?></option>
									<?php } ?>
								</select>
							</div>
							
							<!--******** NEW CITY/STATE/ZIP SEACH FIELDS *********-->
							<div id="showHideDiv3" style="display:none;">
							  <fieldset><legend style="border-bottom:2px dotted #e5e5e5">Location Filters</legend>
                              <div class="form-group">
                              <label>City</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Optional city segment filter within group selected. It will send Bulk SMS to all contacts that fall under group selected and city entered." data-original-title="City Segment Search" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>
							  <?php echo $this->Form->input('Message.city',array('div'=>false,'label'=>false, 'class' => 'form-control input-xlarge','id'=>'city','maxlength'=>'50','placeholder'=>'', 'title'=>''))?>
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
                                    <div class="form-group">
                           			<label>State</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Optional state segment filter within group selected. It will send Bulk SMS to all contacts that fall under group and state selected." data-original-title="State Segment Search" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>	
                                    <?php echo $this->Form->input('Message.state',array('type' =>'select','class' =>'form-control input-xlarge', 'div'=>false,'label'=>false, 'options'=>$states))?>
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
                                    <div class="form-group">
                        			<label>Province</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Optional province segment filter within group selected. It will send Bulk SMS to all contacts that fall under group and province selected." data-original-title="Province Segment Search" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>	
                                    <?php echo $this->Form->input('Message.state',array('type' =>'select','class' =>'form-control input-xlarge', 'div'=>false,'label'=>false, 'options'=>$states))?>
                                    </div>
                                    <?php } ?>
                                    
                                    <div class="form-group">
                        			<label>Postal Code</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Optional postal code segment filter within group selected. It will send Bulk SMS to all contacts that fall under group selected and postal code entered." data-original-title="Postal Code Segment Search" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i> </a>	
                                    <input type="text" class="form-control input-xlarge" id="zipcode" name="data[Message][zip]"  />
                                    </div>
                                    </fieldset>
                                    <hr style="border-top:2px dotted #e5e5e5">
							</div>
							<!--***************************************************-->
							
							<div class="form-group" id="showHideDiv2" style="display:none;margin-top:10px">
								<label>Schedule Delivery 
									<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Please schedule your messages to be sent from 8:00AM - 9:00PM local time to adhere to the applicable guidelines published by the CTIA and the Mobile Marketing Association" data-original-title="Schedule Time" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
								</label>
								<?php 
								echo $this->Form->input('User.shedule',array('readonly' => 'readonly','required' => 'required','div'=>false,'label'=>false, 'class' => 'form-control input-xlarge','id'=>'sent_on','value'=>date('d-m-Y H:i', strtotime($_REQUEST['date']))))
								?>								
								(Please select the date/time)
							</div> 
							<div class="form-group">
								<label>Recurring</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Check this to create recurring events in a series based on parameters selected below." data-original-title="Recurring Events" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
								<div class="radio-list" >				
									<input name="data[ScheduleMessage][recurring]" value="1" type="checkbox" id="recurring" />
								</div>
							</div>
							<div id="div_recurring" style="display: none">
								<div class="form-group">
									<label>Repeat</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Select the type of recurring event you want to schedule, whether events occur daily, weekly, monthly, or yearly." data-original-title="Repeat" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<select class="form-control input-xlarge" name="data[ScheduleMessage][repeat_type]">
											<option value="Daily">Daily</option>
											<option value="Weekly">Weekly</option>
											<option value="Monthly">Monthly</option>
											<option value="Yearly">Yearly</option>
									</select>
								</div>
								<div class="form-group">
									<label>Frequency</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Select how often you want the recurring events to happen based on the Repeat parameter. For example, if you select 'Daily' as repeat and '3' as the frequency, it will schedule the events every 3 days. If you select 'Weekly' as repeat and '1' as the frequency, it will schedule the events every week." data-original-title="Frequency of Events" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<select class="form-control input-xlarge" name="data[ScheduleMessage][frequency]">
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
											<option value="11">11</option>
											<option value="12">12</option>
											<option value="13">13</option>
											<option value="14">14</option>
											<option value="15">15</option>
											<option value="16">16</option>
											<option value="17">17</option>
											<option value="18">18</option>
											<option value="19">19</option>
											<option value="20">20</option>
											<option value="21">21</option>
											<option value="22">22</option>
											<option value="23">23</option>
											<option value="24">24</option>
											<option value="25">25</option>
											<option value="26">26</option>
											<option value="27">27</option>
											<option value="28">28</option>
											<option value="29">29</option>
											<option value="30">30</option>
									</select>
								</div>
								<div class="form-group">
									<label>End Date</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Select the end date you want the recurring events to end. For example, if you selected a Schedule Delivery(start date) of Oct 10 at 6:30 and you want the last event to run on Nov 10, you must select an end date of Nov 10 at 6:30 so Nov 10 will be included as the last date." data-original-title="End Date" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<input type="text" name="data[ScheduleMessage][shedule]" class="form-control input-xlarge" id="sent_on_one" value="<?php echo date('d-m-Y H:i', strtotime($_REQUEST['date'])); ?>">
									(Please select the date)
								</div> 
							</div>
							<div class="form-group">
								<label>Message Template</label>
								<?php
								$Smstemplate[0]='Please select';
								echo $this->Form->input('Smstemplate.id', array(
								'class'=>'form-control input-xlarge',
								'label'=>false,
								'default'=>0,
								'type'=>'select',
								'onchange'=>'confirmmessage(this.value)',
								'options' => $Smstemplate));
								?>
							</div>
							
							<div class="form-group">
								<label>Mobile Splash Page</label>
								<select id="mobilespagesId" class="form-control input-xlarge"  name="data[mobilespages][id]" onchange="confirmpagemessage(this.value)">
									<?php
									$mobilespages[0]="Please Select";	
									foreach($mobilespages as $row => $value){
									$selected = '';
									if($row == $mobilepageid){
									$selected = ' selected="selected"';
									}?>
									<option "<?php echo  $selected; ?> " value="<?php echo  $row; ?>"><?php echo  $value; ?></option>
									<?php }
									?>
								</select>
							</div>
							<div class="form-group" id="socialsharing">
								<?php if (FBTWITTERSHARING == 0){?>
									<?php if (FACEBOOK_APPID != ''){?>
										<div class="checkbox-list">
											<label class="checkbox-inline">
												<span>
													<img src="<?php echo SITE_URL?>/img/FBShare.png" title="Post this message to your Facebook wall."/>
												</span>
											</label>
											<label class="checkbox-inline" style="margin-left:0px;padding-left:0px">
												<span>
													<input style="margin: -5px 0px 0px;" type="checkbox" onchange="Fblogin()" value="1" name='data[Message][Fblogin]' id="fblog">
												</span>
											</label>
											<label class="checkbox-inline" style="margin-left:0px;padding-left:0px">
												<span>
													<div id="fblogoutid">
													</div>
												</span>
											</label>
											<label class="checkbox-inline" style="margin-left:0px;padding-left:0px">
												<span>
													<div id="fblogoutidtol" >										
														<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Checking this box will post the message to your Facebook wall after clicking send. If you are sending multiple MMS images, then only the 1st image will be posted to your wall" data-original-title="Facebook Post" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
													</div>	
												</span>
											</label>
										</div>
									<?php } ?>
								<?php } ?>
							</div>
							<?php if(API_TYPE==0){ ?>	
								<div class="form-group">
									<div class="radio-list">
										<?php
										if((!empty($numbers_sms))||($users['User']['sms']==1)){ ?>
										SMS&nbsp;<input type="radio" value="1" name="data[Message][msg_type]" id="sms" checked />
										<?php } ?>
										<?php if((!empty($numbers_mms))||($users['User']['mms']==1)){ ?>
										MMS&nbsp;<input type="radio" value="2" name="data[Message][msg_type]" id="mms"/>	
										<?php } else { ?>
                						MMS&nbsp;<input type="radio" value="2" name="data[Message][msg_type]" id="mms" disabled/>
                						<? } ?>
									</div>
								</div>
								<?php if (FILEPICKERON == 'Yes'){?>
									<div id="pickfile" style="display:none;margin-bottom:10px">
										<input onchange="popmessagepickwidget(event.fpfile.url)" data-fp-container="modal" 	data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
										<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
									</div>
								<?php } ?>
							<?php } else if(API_TYPE==3 || API_TYPE==4 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7){?>
							
							<div class="form-group">
									<div class="radio-list">
										<?php
										if((!empty($numbers_sms))||($users['User']['sms']==1)){ ?>
										SMS&nbsp;<input type="radio" value="1" name="data[Message][msg_type]" id="sms" checked />
										<?php } ?>
										<?php if((!empty($numbers_mms))||($users['User']['mms']==1)){ ?>
										MMS&nbsp;<input type="radio" value="2" name="data[Message][msg_type]" id="mms"/>	
										<?php } else { ?>
                						MMS&nbsp;<input type="radio" value="2" name="data[Message][msg_type]" id="mms" disabled/>
                						<? } ?>
									</div>
								</div>
								<?php if (FILEPICKERON == 'Yes'){?>
									<div id="pickfile" style="display:none;margin-bottom:10px">
										<input onchange="popmessagepickwidget(event.fpfile.url)" data-fp-container="modal" 	data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
										<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
									</div>
								<?php } ?>
							<?php } else if(API_TYPE==2){?>
								<?php if (FILEPICKERON == 'Yes'){?>
									<div style="margin-bottom:10px">
										<input onchange="popmessagepickwidgetnexmo(event.fpfile.url)" data-fp-container="modal" data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
										<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
									</div>
								<?php } ?>
								<?php 
								if(isset($short_url)){
									$short_url_msg = $short_url;
								}else{
									$short_url_msg = '';
								}	
								?>
								<div class="form-group">
									<label for="some21">Message<span class="required_star"></span></label>  
									<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class' => 'form-control input-xlarge','rows'=>'7','id'=>'message1','maxlength'=>'148','onKeyup'=>'return updateslooce()','value'=>$short_url_msg))?>
								</div>
								<div  class="form-group" style="margin-bottom:5px">Remaining Characters
									<input type=text name=limit2 id=limit2   class="form-control input-xsmall" size=4 readonly value="148">
								</div>
								Special characters not allowed such as ~ [ ] ; "
							<?php }else{?>
								<?php if (FILEPICKERON == 'Yes'){?>
									<div style="margin-bottom:10px">
										<input onchange="popmessagepickwidgetnexmo(event.fpfile.url)" data-fp-container="modal" data-fp-mimetypes="*/*" data-fp-apikey=<?php echo FILEPICKERAPIKEY?> type="filepicker">	
										<input type="hidden" name="data[Message][pick_button]" value="" id="pick_button" />
									</div>
								<?php } ?>
								<?php 
								if(isset($short_url)){
									$short_url_msg = $short_url;
								}else{
									$short_url_msg = '';
								}	
								?>
								<div class="form-group">
									<label for="some21">Message<span class="required_star"></span></label>&nbsp;									
									<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="1 credit is charged for each 160 character segment. If you have a message that is 300 characters, and you are sending to 10 people, 20 credits will be deducted (2 credits for each person).<br/><br/><b>NOTE:</b> Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment." data-original-title="Credits" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									
									<!--<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>-->
									<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b>, <b>%%Email%%</b>, <b>%%City%%</b>, <b>%%State%%</b>, <b>%%Postal%%</b>, <b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>" data-original-title="Available Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px;color:#26C281"></i></a>
									
									<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class' => 'form-control input-xlarge','rows'=>'7','id'=>'message1','maxlength'=>'1588','onKeyup'=>'return update()','value'=>$remindermsg))?>
									<!--<?php if(API_TYPE !=2){?>
										<input type="button" value="Variations" onclick="spinvariations();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;
										<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller' => 'messages','controller' => 'messages','action' => 'spinhelp'), array('escape' =>false, 'class' => 'nyroModal','style'=>'margin-top:10px')); ?>
									<?php } ?>-->
								</div>
								<!--<div  class="form-group" style="margin-bottom:5px">Remaining Characters
									<input type=text name=limit2 id=limit2   class="form-control input-xsmall" size=4 readonly value="1588">
								</div>
								Special characters not allowed such as ~ [ ] ; "-->
							<?php } ?>
							<input type="hidden" name="data[Message][pick_file]" id="message3" value=""/>
						<?php if(API_TYPE==0){ ?>
								<?php 
								if(isset($short_url)){
									$short_url_msg = $short_url;
								}else{
									$short_url_msg = '';
								}	
								?>
							<div id='textmsg' style="display:none;">
								<div class="form-group">
									<label for="some21">Message<span class="required_star"></span>&nbsp;
									<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="1 credit is charged for each 160 character segment. If you have a message that is 300 characters, and you are sending to 10 people, 20 credits will be deducted (2 credits for each person).<br/><br/><b>NOTE:</b> Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment." data-original-title="Credits" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<!--<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a></label>  -->
									<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b>, <b>%%Email%%</b>, <b>%%City%%</b>, <b>%%State%%</b>, <b>%%Postal%%</b>, <b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>" data-original-title="Available Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px;color:#26C281"></i></a>
									<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class' => 'form-control input-xlarge','rows'=>'7','id'=>'message1','maxlength'=>'1588','onKeyup'=>'return update()','value'=>$remindermsg))?>
									<!--<?php if(API_TYPE !=2){?>	
									<input type="button" value="Variations" onclick="spinvariations();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px" />
									&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller' => 'messages','action' => 'spinhelp'), array('escape' =>false, 'class' => 'nyroModal','style'=>'margin-top:10px')); ?>
									<?php } ?>-->
								</div>
								<!--<div  class="form-group" style="margin-bottom:5px">Remaining Characters
									<input type=text name=limit2 id=limit2   class="form-control input-xsmall" size=4 readonly value="1588">
								</div>
								Special characters not allowed such as ~ [ ] ; "-->
							</div>
							<div id='upload' style="display:none;" >
								<div class="form-group" >					
									<div data-provides="fileinput" class="fileinput fileinput-new">
										<input type="hidden" id="check_img_validation" value="0" />
										<div style="width: 200px; height: 150px;" class="fileinput-new thumbnail">
											<img alt="" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image">
										</div>
										<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-preview fileinput-exists thumbnail"></div>&nbsp;
										<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Please be aware that mobile carriers are much more restrictive when sending images to many numbers in rapid succession from 1 long code number since there is much more bandwidth to factor in." data-original-title="Images" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
										<div>
											<span class="btn default btn-file">
												<span class="fileinput-new"> Select image </span>
												<span class="fileinput-exists"> Change </span>
												<input type="hidden" value="" name="..."><input type="file" id="files" name="data[Message][image][]" onclick="check_image()" > 
											</span>
											<a onclick="remove_image()" data-dismiss="fileinput" class="btn red fileinput-exists" href="javascript:;"> Remove </a>
										</div>
									</div>
								</div>
								<div class="form-group" style="margin-top:0px">
									<label for="some21">Message<span class="required_star"></span>&nbsp;
									<a href="javascript:;" data-container="body" data-trigger="hover" data-content="<?php echo CHARGECREDITSMMS?> credits will be charged for each contact when sending MMS messages." data-original-title="Credits" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<!--<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>-->
									<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b>, <b>%%Email%%</b>, <b>%%City%%</b>, <b>%%State%%</b>, <b>%%Postal%%</b>, <b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>" data-original-title="Available Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px;color:#26C281"></i></a>

									</label>  </label>  
									<?php echo $this->Form->textarea( 'Message.msg',array('div'=>false,'label'=>false,'class' => 'form-control input-xlarge','rows'=>'7','id'=>'message2','maxlength'=>'1588','onKeyup'=>'return update1()','value'=>$remindermsg))?>
									<!--<?php if(API_TYPE !=2){?>
									<input type="button" value="Variations" onclick="spinvariations2();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px;"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext2();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px;"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller' => 'messages','action' => 'spinhelp'), array('escape' =>false, 'class' => 'nyroModal','style'=>'margin-top:10px')); ?>
									<?php } ?>-->
								</div>
								<!--<div  class="form-group" style="margin-bottom:5px">Remaining Characters
									<input type=text name=limit3 id=limit3   class="form-control input-xsmall" size=4 readonly value="1588">
								</div>
								Special characters not allowed such as ~ [ ] ; "-->
							</div>
					<?php  }else if(API_TYPE==3 || API_TYPE==4 || API_TYPE==5 || API_TYPE==6 || API_TYPE==7){ ?>
					<?php 
								if(isset($short_url)){
									$short_url_msg = $short_url;
								}else{
									$short_url_msg = '';
								}	
								?>
							<div id='textmsg' style="display:none;">
								<div class="form-group">
									<label for="some21">Message<span class="required_star"></span>&nbsp;
									<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="1 credit is charged for each 160 character segment. If you have a message that is 300 characters, and you are sending to 10 people, 20 credits will be deducted (2 credits for each person).<br/><br/><b>NOTE:</b> Messages containing non-GSM(unicode) characters will be charged 1 credit for each 70 character segment." data-original-title="Credits" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<!--<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a></label>  -->
									<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b>, <b>%%Email%%</b>, <b>%%City%%</b>, <b>%%State%%</b>, <b>%%Postal%%</b>, <b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>" data-original-title="Available Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px;color:#26C281"></i></a>
									<?php echo $this->Form->textarea( 'Keyword.message',array('div'=>false,'label'=>false,'class' => 'form-control input-xlarge','rows'=>'7','id'=>'message1','maxlength'=>'1588','onKeyup'=>'return update()','value'=>$remindermsg))?>
									<!--<?php if(API_TYPE !=2){?>	
									<input type="button" value="Variations" onclick="spinvariations();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px" />
									&nbsp;<input type="button" value="Spin {|}" onclick="spintext();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller' => 'messages','action' => 'spinhelp'), array('escape' =>false, 'class' => 'nyroModal','style'=>'margin-top:10px')); ?>
									<?php } ?>-->
								</div>
								<!--<div  class="form-group" style="margin-bottom:5px">Remaining Characters
									<input type=text name=limit2 id=limit2   class="form-control input-xsmall" size=4 readonly value="1588">
								</div>
								Special characters not allowed such as ~ [ ] ; "-->
							</div>
							<div id='upload' style="display:none;" >
								<div class="form-group" >					
									<div data-provides="fileinput" class="fileinput fileinput-new">
										<input type="hidden" id="check_img_validation" value="0" />
										<div style="width: 200px; height: 150px;" class="fileinput-new thumbnail">
											<img alt="" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image">
										</div>
										<div style="max-width: 200px; max-height: 150px; line-height: 10px;" class="fileinput-preview fileinput-exists thumbnail"></div>&nbsp;
										<a href="javascript:;" data-container="body" data-trigger="hover" data-content="Please be aware that mobile carriers are much more restrictive when sending images to many numbers in rapid succession from 1 long code number since there is much more bandwidth to factor in." data-original-title="Images" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
										<div>
											<span class="btn default btn-file">
												<span class="fileinput-new"> Select image </span>
												<span class="fileinput-exists"> Change </span>
												<input type="hidden" value="" name="..."><input type="file" id="files" name="data[Message][image][]" onclick="check_image()" > 
											</span>
											<a onclick="remove_image()" data-dismiss="fileinput" class="btn red fileinput-exists" href="javascript:;"> Remove </a>
										</div>
									</div>
								</div>
								<div class="form-group" style="margin-top:0px">
									<label for="some21">Message<span class="required_star"></span>&nbsp;
									<a href="javascript:;" data-container="body" data-trigger="hover" data-content="<?php echo CHARGECREDITSMMS?> credits will be charged for each contact when sending MMS messages." data-original-title="Credits" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<!--<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Use <b>%%Name%%</b> to output the name of the subscriber in the message if a name exists for one" data-original-title="Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px"></i></a>-->
									<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="<b>%%Name%%</b>, <b>%%Email%%</b>, <b>%%City%%</b>, <b>%%State%%</b>, <b>%%Postal%%</b>, <b>%%Custom1%%</b>, <b>%%Custom2%%</b>, <b>%%Custom3%%</b>" data-original-title="Available Merge Tags" class="popovers"><i class="fa fa-tags" style="font-size:18px;color:#26C281"></i></a>

									</label>  </label>  
									<?php echo $this->Form->textarea( 'Message.msg',array('div'=>false,'label'=>false,'class' => 'form-control input-xlarge','rows'=>'7','id'=>'message2','maxlength'=>'1588','onKeyup'=>'return update1()','value'=>$remindermsg))?>
									<!--<?php if(API_TYPE !=2){?>
									<input type="button" value="Variations" onclick="spinvariations2();" class="btn blue btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px;"/>&nbsp;<input type="button" value="Spin {|}" onclick="spintext2();" class="btn purple btn-outline" style="padding-top:1px;padding-bottom:1px; margin-top:10px;"/>&nbsp;<?php echo $this->Html->link($this->Html->image('note-error.png'), array('controller' => 'messages','action' => 'spinhelp'), array('escape' =>false, 'class' => 'nyroModal','style'=>'margin-top:10px')); ?>
									<?php } ?>-->
								</div>
								<!--<div  class="form-group" style="margin-bottom:5px">Remaining Characters
									<input type=text name=limit3 id=limit3   class="form-control input-xsmall" size=4 readonly value="1588">
								</div>
								Special characters not allowed such as ~ [ ] ; "-->
							</div>
					<?php } ?>
                                                        
							<!--<div id="mandatory_message_show">-->
								<div class="form-group" style="margin-top:10px">
									<label>Mandatory Appended Message</label>
									<?php echo $this->Form->input('Message.systemmsg',array('div'=>false,'label'=>false, 'class' => 'form-control input-xlarge','id'=>'mandatorymessage','maxlength'=>'1588','rows'=>'1','cols'=>'32','value'=>'STOP to end','readonly'=>'','style'=>'color:#808080'))?>
								</div> 
								<div id="mandatory_message_show">
								<?php if(API_TYPE !=2){?>	
								<div class="form-group">
									<input id="rotate_number" type="checkbox" value="0" name="data[User][rotate_number]" onclick="checkrotate()">&nbsp;Rotate through your long codes&nbsp;									
										<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="Useful if you have a large number of opt-in contacts(>500), you can spread your workload across multiple numbers in your account.<br/><br/><b>STICKY SENDER:</b> Even when rotating through your numbers, your contacts will always get the message from the same recognizable phone number to create a consistent experience and maintain conversation history." data-original-title="Longcodes Rotate" class="popovers"> <i class="fa fa-question-circle" style="font-size:18px"></i> </a>
									<!--</font>    -->
								</div>	
							</div>
						<div class="form-group" style="margin-top:10px">
							<label>Sending Throttle</label>&nbsp;<a href="javascript:;" data-html="true" data-container="body" data-trigger="hover" data-content="Per carrier restriction, the fastest(default setting) you can send SMS to numbers in the US and Canada on 1 long code is 1 SMS/second. However, you can slow that down to exercise extra caution. The slowest setting will send at rate of 10 SMS per minute or 1 SMS every 6 seconds.<br><br><b>NOTE:</b> Sending to international numbers outside the US and Canada will send at a rate of <b>10 SMS every second</b> per long code. Keep it at the default setting for that rate." data-original-title="Sending Throttle" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
							<?php	
								$Option=array('1'=>'1 SMS Every 1 Second Per Long Code','2'=>'1 SMS Every 2 Seconds Per Long Code','3'=>'1 SMS Every 3 Seconds Per Long Code','4'=>'1 SMS Every 4 Seconds Per Long Code','5'=>'1 SMS Every 5 Seconds Per Long Code','6'=>'1 SMS Every 6 Seconds Per Long Code');
								echo $this->Form->input('User.throttle', array(
								'class'=>'form-control input-xlarge',
								'label'=>false,
								'type'=>'select',  'options' => $Option, 'default'=>'1'));
							?>
						</div>
							<?php } ?>	
						<div class="form-actions">
							<?php echo $this->Form->submit('Save Message',array('div'=>false,'class'=>'btn blue'));?>
						</div>                           
							<?php echo $this->Form->end(); ?>
					<!--</div>

					</div>
				</div>-->
			</div>

		</div>
	</div>

<script type="text/javascript">
$('#sent_on').datetimepicker({
     minDate: 0,
    showSecond: false,
    //showMinute: false,
	dateFormat: 'dd-mm-yy',
	//timeFormat: 'hh',
	timeFormat: 'hh:mm',
	stepHour: 1,
	stepMinute: 5,
	stepSecond: 10,
	
});
$('#sent_on_one').datetimepicker({
     minDate: 0,
    showSecond: false,
    //showMinute: false,
	dateFormat: 'dd-mm-yy',
	//timeFormat: 'hh',
	timeFormat: 'hh:mm',
	stepHour: 1,
	stepMinute: 5,
	stepSecond: 10,
	
});
//$('#sent_on_one').datepicker({
//	dateFormat: 'dd-mm-yy',
//});
$(function () {
	$("#recurring").click(function () {
		if ($(this).is(":checked")) {
			$("#div_recurring").show();
		} else {
			$("#div_recurring").hide();
		}
	});
});

/*$('#recurring').click(function(){
		if($('#recurring').prop('checked')==true){
			$('#div_recurring').show();
		}else if($('#recurring').prop('checked')==false){
			$('#div_recurring').hide();
		}
	});
});*/
	
function utf8_decode(str) {
  var output = "";
  var i = c = c1 = c2 = 0;
  while ( i < str.length ) {
    c = str.charCodeAt(i);
    if (c < 128) {
      output += String.fromCharCode(c);
      i++;
    }
    else if((c > 191) && (c < 224)) {
      c2 = str.charCodeAt(i+1);
      output += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
      i += 2;
    }
    else {
      c2 = str.charCodeAt(i+1);
      c3 = str.charCodeAt(i+2);
      output += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
      i += 3;
    }
  }
  return output;
}
</script>
