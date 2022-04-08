	<div class="page-content-wrapper">
		<div class="page-content">              
			<h3 class="page-title"><?php  echo ('Q&A SMS Bots View');?></h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="icon-home"></i>
						<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="<?php echo SITE_URL;?>/multicontests">Q&A SMS Bots </a>
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
			<div class="portlet box red">		
				<div class="portlet-title">
						<div class="caption">
						<i class="fa fa-question"></i>
						<?php  echo ('Q&A SMS Bots View');?>
					</div>
				</div>				
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-striped">
								<tbody>
									<tr>												
										<th style="width:15%">Name</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $question_arr['MultiContest']['name'];?>
											</span>
										</td>
									</tr>
									<tr>
									    <th style="width:15%">Keyword</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php echo $question_arr['MultiContest']['keyword'];?>
											</span>
										</td>
									</tr>
									<tr>
									    <th style="width:15%">Finish all Questions</th>
										<td style="width:35%">
											<span class="text-muted">												
												<?php if($question_arr['MultiContest']['finish_all_questions']==1){?>
												Yes
												<?}else{?>
												No
												<?}?>
											</span>
										</td>
									</tr>
									
									<?php $i=1; foreach ($ContestQuestion as $ContestQuestions){?>
										<tr>												
											<th style="width:15%"><font style="font-size:18px;color:#e7505a">Question <?php echo $i;?></font></th>
											<td style="width:35%">
												<span class="text-muted">												
												<?php echo $ContestQuestions['MultiContestQuestion']['question'];?>
												</span>
											</td>
										</tr>
										<?php foreach($ContestQuestions['MultiContestQuestionsOption'] as $contest_questions_option_arr){ 
										//echo"<pre>";print_r($ContestQuestions);die;
										
										
										?>
										<tr>
											<th style="width:15%">Answer</th>
											<td style="width:35%">
												<span class="text-muted">												
													<?php echo $contest_questions_option_arr['answer'];?>.&nbsp;<?php echo $contest_questions_option_arr['description'];?>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:15%">Response</th>
											<td style="width:35%">
												<span class="text-muted">												
													<?php echo $contest_questions_option_arr['response'];?>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:15%">Alert Email(s)</th>
											<td style="width:35%">
												<span class="text-muted">												
													<?php echo $contest_questions_option_arr['email'];?>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:15%">Alert Phone(s)</th>
											<td style="width:35%">
												<span class="text-muted">												
													<?php echo $contest_questions_option_arr['phone'];?>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:15%">Alert Message</th>
											<td style="width:35%">
												<span class="text-muted">												
													<?php echo $contest_questions_option_arr['text'];?>
												</span>
											</td>
										</tr>
										
									<?php }?>
									<?php $i++; }?>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		
		
						