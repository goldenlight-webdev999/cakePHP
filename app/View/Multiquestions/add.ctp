	
<!--<script>
	jQuery(function(){
		jQuery("#name").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please enter name"              
		});				
		jQuery("#group_id").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please select group"              
		});
		jQuery("#keyword").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please enter keyword"              
		});
		jQuery("#questions").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please enter questions"              
		});
		jQuery("#answer").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please enter answer"              
		});
		jQuery("#response").validate({
			expression: "if (VAL) return true; else return false;",
			message: "Please enter response"              
		});
	});	
</script>-->
 <style>
 .ValidationErrors{
 color:red;
	}
	
input:focus {
  background-color: #edf6ff;
}
textarea:focus {
  background-color: #edf6ff;
}
</style>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo ('Q&A SMS Bots');?></h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo SITE_URL;?>/multiquestions"><?php echo ('Q&A SMS Bots');?></a>
				</li>
			</ul>
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
					<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li>
							<a href="<?php echo SITE_URL;?>/multiquestions" title="Back"><i class="fa fa-arrow-left"></i> Back</a>
							
						</li>
					</ul>
				</div>
			</div>						
		</div>
		<?php echo $this->Session->flash(); ?>
		<div class="portlet mt-element-ribbon light portlet-fit  ">
			<div class="ribbon ribbon-right ribbon-clip ribbon-shadow ribbon-border-dash-hor ribbon-color-success uppercase">
				<div class="ribbon-sub ribbon-clip ribbon-right"></div>
				Create Q&A SMS Bot Form
			</div>
			<div class="portlet-title">
				<div class="caption">
				<i class="fa fa-question font-red"></i> </div>
			</div>
			<div class="portlet-body form">
			<?php echo $this->Session->flash(); ?>	
				<form  method="post" class="forma">
					<div class="form-body">	
						<div class="form-group">
							<label>Name</label>
							<input type="text"  name="data[MultiContest][name]" id="name" class="form-control" placeholder="Name" required>
						</div>
						<div class="form-group ">
							<label>Keyword</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Texting in this keyword will kick off the 1st question from the Q&A Bot. After responding to each question, you will get the next question in the sequence until all questions have been sent." data-original-title="Keyword" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
							<input type="text"  name="data[MultiContest][keyword]" id="keyword" class="form-control" placeholder="Keyword" required>
						</div>
						<div class="form-group">
							<label>Group</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The group to add the new contact if person texting the Q&A Bot is not currently in your contact list." data-original-title="Add to Group" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
							<select id="group_id" class="form-control" name="data[MultiContest][group_id]">
								<?php if(!empty($Group)){ ?>
									<?php foreach( $Group as $Groups){ ?>
										<option value="<?php echo $Groups['Group']['id']; ?>"><?php echo ucwords($Groups['Group']['group_name']); ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label>Finish All Questions</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="If checked, the person must finish all questions being asked in the sequence before they can text in the keyword again. If not checked, they can text in the keyword before they finish and they will get the 1st question in the sequence again." data-original-title="Finish All Questions" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
							<div class="radio-list" >				
								<input name="data[MultiContest][finish_all_questions]" type="checkbox" id="finish_all_questions" />
							</div>
					    </div>
						<div id="section" class="section">
							<!--<div class="portlet box blue-dark">-->
							<div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption font-yellow-casablanca">
                                        <i class="icon-question font-yellow-casablanca"></i>
                                        <span class="caption-subject bold uppercase"> Question 1</span>
                                        <span class="caption-helper">Kicked off by keyword...</span>
                                    </div>
                                    <div class="tools">
                                        <a href="" class="collapse"> </a>
                                    </div>
                                    <div class="actions">
                                        <a style="background:#26C281" class="btn btn-circle btn-icon-only btn-default" href="#null" onclick="add_answers()" title="Add Answer Option">
                                            <i style="color:#fff" class="icon-plus"></i>
                                        </a>
                                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"> </a>
                                        <a class="btn btn-circle btn-icon-only btn-default collapse" href="javascript:;"> </a>
                                    </div>
                                    
                                </div>
								<div class="portlet-body"> 
									<div class="form-group ">
										<label>Question <span class="required_star"><b>* (Required)</b></span></label>
										<textarea id="questions" maxlength="1600" name="data[MultiContestQuestion][1][question]" cols="5" rows="3" class="form-control" placeholder="Question" required></textarea>
									</div>
									<div id="answers" class="answers">
										<div class="form-group">
											<label>Answer <span class="required_star"><b>* (Required)</b></span></label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The letter choice associated with the description that will be texted in. Leave this as is, unless you delete options and need to re-order the letters." data-original-title="Answer Choice" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="answer" name="data[MultiContestQuestionsOption][1][answers][]" class="form-control" placeholder="Answer" value="A" required>
										</div>
										<div class="form-group">
											<label>Description <span class="required_star"><b>* (Required)</b></span></label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The answer description associated with the letter choice." data-original-title="Answer Description" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="description" name="data[MultiContestQuestionsOption][1][description][]" class="form-control" placeholder="Description" value="" required>
										</div>
										<div class="form-group ">
											<label>Response </label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The response texted back to the contact after receiving the answer choice from them." data-original-title="Response" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="response"  value="Thanks for your response." name="data[MultiContestQuestionsOption][1][response][]" class="form-control" placeholder="Response" >
										</div>
										<div class="form-group">
											<label>Alert Email(s) </label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Enter the email(s) to be alerted of the answer choice. The email message will contain the alert message field below. If alerting multiple email addresses, separate each email address with a comma." data-original-title="Alert Email(s)" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="email" name="data[MultiContestQuestionsOption][1][email][]" class="form-control" placeholder="Email" value="">
    									</div>
										<div class="form-group">
											<label>Alert Phone(s) </label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Enter the phone number(s) to be alerted of the answer choice. The text message will contain the alert message field below. If alerting multiple phone numbers, separate each number with a comma. <br/><br/><b>NOTE:</b> Enter mobile number with country code. US format example: 12025248725" data-original-title="Alert Phones(s)" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<input type="text" id="phone" name="data[MultiContestQuestionsOption][1][phone][]" class="form-control" placeholder="Phone" value="">
										</div>
										<div class="form-group ">
											<label>Alert Message </label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Enter the alert message that will be texted and/or emailed to the fields you entered above. You can use <b>%%Name%%</b> to output the name of the contact texting in and <b>%%Number%%</b> to output the number of the contact texting in." data-original-title="Alert Message" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
											<textarea id="text" maxlength="1600" name="data[MultiContestQuestionsOption][1][text][]" cols="5" rows="3" class="form-control" placeholder="Text" ></textarea>
										</div>
										
									</div>
									<div id="retry_answers"></div>
									<!--<div class="form-group">
										<button type="button" class="btn green btn-sm" href="#null"  onclick="add_answers()"><i class="fa fa-plus"></i> Insert Option</button>
									</div>-->
								</div>
							</div>
						</div>
						<div id="retry_section"></div>
						<div class="row">
							<div class="col-sm-12">
								<div style="margin-top:15px;">
									<button type="button" class="btn default green-stripe btn-lg" href="#null"  onclick="add_more()">Add Question</button>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-12">
								<div style="margin-top:15px;">
									<button type="submit" name="submit" class="btn blue">Save</button>
						
									<?php echo $this->Html->link('Cancel','javascript:window.history.back();',array('class' => 'btn default'));?>
								</div>
							</div>
						</div>
					
				</div></form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">	
    /*function add_more(){
		var n = $(".section").length;
		n = n + 1;
		var html = '<div id="section_'+n+'" class="section" style="margin-top:15px;"><div class="portlet box blue-dark"><div class="portlet-title"><div class="caption">Question '+n+'</div></div><div class="portlet-body"><div class="form-group"><label>Question </label><textarea id="question_'+n+'" maxlength="1600" onKeyup="return updates('+n+')" name="data[MultiContestQuestion]['+n+'][question]" cols="5" rows="3" class="form-control" placeholder="Question" required></textarea></div><div id="answer_'+n+'" class="answers_'+n+'" style="margin-bottom: 15px;"><div class="form-group"><label>Answer </label><input type="text"  name="data[MultiContestQuestionsOption]['+n+'][answers][]" value="A" class="form-control" required placeholder="Answer"></div><div class="form-group"><label>Description </label><input type="text"  name="data[MultiContestQuestionsOption]['+n+'][description][]" value="" class="form-control" placeholder="Description" required></div><div class="form-group"><label>Response </label><input type="text"  name="data[MultiContestQuestionsOption]['+n+'][response][]" value="Thanks for your response." class="form-control" placeholder="Response" ></div><div class="form-group"><label>Alert Email(s) </label><input type="text" id="email_'+n+'" name="data[MultiContestQuestionsOption]['+n+'][email][]" class="form-control" placeholder="Email" value=""></div><div class="form-group"><label>Alert Phone(s) </label><input type="text" id="phone_'+n+'" name="data[MultiContestQuestionsOption]['+n+'][phone][]" class="form-control" placeholder="Phone" value=""></div><div class="form-group "><label>Alert Message </label><textarea id="text_'+n+'" maxlength="1600" onKeyup="return updatetexts('+n+')" name="data[MultiContestQuestionsOption]['+n+'][text][]" cols="5" rows="3" class="form-control" placeholder="Text" ></textarea></div><a class="remove_field btn btn-danger btn-sm" href="#null" onclick="delete_answers('+n+')"><i class="fa fa-times"></i> Delete Option</a></div><div id="retry_answers_'+n+'"></div><div class="form-group"><button type="button" class="btn green btn-sm" href="#null" onclick="add_answer('+n+')"><i class="fa fa-plus"></i> Insert Option</button></div>';
		html += '<a class="remove_field btn default red-stripe btn-lg" href="#null" onclick="delete_div('+n+')">Delete Question</a></div></div></div>';
		$("#retry_section").append(html);
		updates(n);
		updatetexts(n);
	}*/
	
	function add_more(){
		var n = $(".section").length;
		n = n + 1;
		var html = '<div id="section_'+n+'" class="section" style="margin-top:15px;"><div class="portlet light bordered"><div class="portlet-title"><div class="caption font-yellow-casablanca"><i class="icon-question font-yellow-casablanca"></i><span class="caption-subject bold uppercase"> Question '+n+'</span>';
        html += '<span class="caption-helper"> Next question in sequence...</span></div><div class="tools"><a href="" class="collapse"></a>';
        html += '</div><div class="actions"><a style="background:#26C281" class="btn btn-circle btn-icon-only btn-default" href="#null" onclick="add_answer('+n+')" title="Add Answer Option"><i style="color:#fff" class="icon-plus"></i></a>&nbsp;<a style="background:#D91E18" class="btn btn-circle btn-icon-only btn-default" href="#null" onclick="delete_div('+n+')" title="Delete Question"><i style="color:#fff" class="icon-trash"></i> </a>&nbsp;<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"> </a><a class="btn btn-circle btn-icon-only btn-default collapse" href="javascript:;"> </a></div></div><div class="portlet-body"><div class="form-group"><label>Question <span class="required_star"><b>* (Required)</b></span></label><textarea id="question_'+n+'" maxlength="1600" onKeyup="return updates('+n+')" name="data[MultiContestQuestion]['+n+'][question]" cols="5" rows="3" class="form-control" placeholder="Question" required></textarea></div><div id="retry_answers_'+n+'"><div id="answer_1" class="answers_'+n+'" style="margin-bottom: 15px;"><div class="form-group"><label>Answer <span class="required_star"><b>* (Required)</b></span></label><input type="text"  name="data[MultiContestQuestionsOption]['+n+'][answers][]" value="A" class="form-control" required placeholder="Answer"></div><div class="form-group"><label>Description <span class="required_star"><b>* (Required)</b></span></label><input type="text"  name="data[MultiContestQuestionsOption]['+n+'][description][]" value="" class="form-control" placeholder="Description" required></div><div class="form-group"><label>Response </label><input type="text"  name="data[MultiContestQuestionsOption]['+n+'][response][]" value="Thanks for your response." class="form-control" placeholder="Response" ></div><div class="form-group"><label>Alert Email(s) </label><input type="text" id="email_'+n+'" name="data[MultiContestQuestionsOption]['+n+'][email][]" class="form-control" placeholder="Email" value=""></div><div class="form-group"><label>Alert Phone(s) </label><input type="text" id="phone_'+n+'" name="data[MultiContestQuestionsOption]['+n+'][phone][]" class="form-control" placeholder="Phone" value=""></div><div class="form-group "><label>Alert Message </label><textarea id="text_'+n+'" maxlength="1600" onKeyup="return updatetexts('+n+')" name="data[MultiContestQuestionsOption]['+n+'][text][]" cols="5" rows="3" class="form-control" placeholder="Text" ></textarea></div></div></div>';
		html += '</div></div></div>';
		$("#retry_section").append(html);
		updates(n);
		updatetexts(n);
	}
	
	function delete_div(id){
		$("#section_"+id).remove();
	}
	function delete_answers(id,question){ 
		$("#retry_answers_"+question+" #answer_"+id).remove(); 
	}
	function add_answer(id){
		var n = $(".answers_"+id).length;
		var i = n;
		n = n + 1;
		var answer_arr = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"]; 
		var answer = '';
		if(jQuery.inArray(i, answer_arr )){ 
			if (typeof answer_arr[i] != 'undefined') { 
				var answer = answer_arr[i]; 
			}else{	 
				var answer = ''; 
			} 
			i = i + 1; 
		} 
		var html = '<div id="answer_'+n+'" class="answers_'+id+'" style="margin-top:15px;margin-bottom: 15px;"><div class="form-group"><label>Answer <span class="required_star"><b>* (Required)</b></span></label><input type="text"  name="data[MultiContestQuestionsOption]['+id+'][answers][]" value="'+answer+'" class="form-control" required placeholder="Answer"></div><div class="form-group"><label>Description <span class="required_star"><b>* (Required)</b></span></label><input type="text"  name="data[MultiContestQuestionsOption]['+id+'][description][]" value="" class="form-control" placeholder="Description" required></div><div class="form-group"><label>Response </label><input type="text"  name="data[MultiContestQuestionsOption]['+id+'][response][]" value="Thanks for your response." class="form-control" placeholder="Response" ></div>';
		html += '<div class="form-group"><label>Alert Email(s) </label><input type="text" id="email_'+id+'" name="data[MultiContestQuestionsOption]['+id+'][email][]" class="form-control" placeholder="Email" value=""></div><div class="form-group"><label>Alert Phone(s) </label><input type="text" id="phone_'+id+'" name="data[MultiContestQuestionsOption]['+id+'][phone][]" class="form-control" placeholder="Phone" value=""></div><div class="form-group "><label>Alert Message </label><textarea id="text_'+id+'" maxlength="1600" onKeyup="return updatetexts('+id+')"  name="data[MultiContestQuestionsOption]['+id+'][text][]" cols="5" rows="3" class="form-control" placeholder="Text" ></textarea></div>'; 
		html += '<a class="remove_field btn btn-danger btn-sm" href="#null" onclick="delete_answers('+n+','+id+')"><i class="fa fa-times"></i> Delete Option</a></div>'; 
		$('#retry_answers_'+id+'').append(html);
		updatetexts(id);
	}
	function add_answers(){
		var n = $(".answers").length; 
		var i = n;
		n = n + 1;
		var answer_arr = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"]; 
		var answer = '';
		if(jQuery.inArray(i, answer_arr )){ 
			if (typeof answer_arr[i] != 'undefined') { 
				var answer = answer_arr[i]; 
			}else{	 
				var answer = ''; 
			} 
			i = i + 1; 
		} 
		var html = '<div id="answers_'+n+'" class="answers" style="margin-top:15px;margin-bottom: 15px;"><div class="form-group"><label>Answer <span class="required_star"><b>* (Required)</b></span></label><input type="text"  name="data[MultiContestQuestionsOption][1][answers][]" value="'+answer+'" class="form-control" required placeholder="Answer"></div><div class="form-group"><label>Description <span class="required_star"><b>* (Required)</b></span></label><input type="text"  name="data[MultiContestQuestionsOption][1][description][]" value="" class="form-control" placeholder="Description" required></div><div class="form-group"><label>Response </label><input type="text"  name="data[MultiContestQuestionsOption][1][response][]" value="Thanks for your response." class="form-control" placeholder="Response"></div>';
		html += '<div class="form-group"><label>Alert Email(s) </label><input type="text" id="email_'+n+'" name="data[MultiContestQuestionsOption][1][email][]" class="form-control" placeholder="Email" value=""></div><div class="form-group"><label>Alert Phone(s) </label><input type="text" id="phone_'+n+'" name="data[MultiContestQuestionsOption][1][phone][]" class="form-control" placeholder="Phone" value=""></div><div class="form-group "><label>Alert Message </label><textarea id="text_'+n+'" maxlength="1600" onKeyup="return updatetexts('+n+')"  name="data[MultiContestQuestionsOption][1][text][]" cols="5" rows="3" class="form-control" placeholder="Text" ></textarea></div>'; 
		html += '<a class="remove_field btn btn-danger btn-sm" href="#null" onclick="delete_answer('+n+')"><i class="fa fa-times"></i> Delete Option</a></div>'; 
		$("#retry_answers").append(html);
		updatetexts(n);		
	} 
	function delete_answer(id){ 
		$("#retry_answers #answers_"+id).remove(); 
	}
	function updates(id){
		$("#section_"+id+" #question_"+id).maxlength({
			alwaysShow: true,
			twoCharLinebreak: false,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			separator: ' out of ',
			preText: 'You typed ',
			postText: ' characters',
			validate: true
		});
	}
	function updatetexts(id){
		$("#retry_answers_"+id+" #text_"+id).maxlength({
			alwaysShow: true,
			twoCharLinebreak: false,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			separator: ' out of ',
			preText: 'You typed ',
			postText: ' characters',
			validate: true
		});
		$("#retry_answers #text_"+id).maxlength({
			alwaysShow: true,
			twoCharLinebreak: false,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			separator: ' out of ',
			preText: 'You typed ',
			postText: ' characters',
			validate: true
		});
		$("#answers_"+id+" #text_"+id).maxlength({
			alwaysShow: true,
			twoCharLinebreak: false,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			separator: ' out of ',
			preText: 'You typed ',
			postText: ' characters',
			validate: true
		});
	}
	jQuery(document).ready(function() { 
		$('#questions').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
        });
		$('#text').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
        });
	});
</script>