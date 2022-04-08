<?php
echo $this->Html->css('dhtmlgoodies_calendar');
echo $this->Html->script('dhtmlgoodies_calendarnew1.js');
?>
<style>
.ValidationErrors{
color:red;
}
.error-message{
color:red;
}
</style>

<script>
jQuery(function(){
	jQuery("#contestname").validate({
		expression: "if (VAL) return true; else return false;",
		message: "Please enter a contest name"
	});jQuery("#keyword").validate({
		expression: "if (VAL) return true; else return false;",
		message: "Please enter a contest keyword"
	});jQuery("#message").validate({
		expression: "if (VAL) return true; else return false;",
		message: "Please enter a message"
	});jQuery("#maxentries").validate({
		expression: "if (VAL) return true; else return false;",
		message: "Please select max entries for this contest"
	});jQuery("#group").validate({
		expression: "if (VAL > 0) return true; else return false;",
		message: "Please select a group"
	});
});
</script>
<script type="text/javascript">
$(document).ready(function (){
	$('textarea[maxlength]').live('keyup change', function(){
		var str = $(this).val()
		var mx = parseInt($(this).attr('maxlength'))
		if (str.length > mx){
			$(this).val(str.substr(0, mx))
			return false;
		}
	})
});

function validation(){
    if($('#startdate1').val()==''){
			alert( "Please select a start date" );
			return false;
		}
	if($('#enddate1').val()==''){
			alert( "Please select an end date" );
			return false;
	}
}
</script>
<script>
var count = "160";
function update(){
	var tex = $("#message").val();
	$count1=tex.length;
	tex = tex.replace('{','');
	tex = tex.replace('}','');
	tex = tex.replace('[','');
	tex = tex.replace(']','');
	tex = tex.replace('~','');
	tex = tex.replace(';','');
	tex = tex.replace('`','');
	tex = tex.replace("'","");
	tex = tex.replace('"','');
	var len = tex.length;
	$("#message").val(tex);
	if(len > count){
		tex = tex.substring(0,count1);
		return false;
	}
	$("#limit").val(count-len);
}
function update1(){
	var tex = $("#contestname").val();
	tex = tex.replace('{','');
	tex = tex.replace('`','');
	tex = tex.replace('}','');
	tex = tex.replace('[','');
	tex = tex.replace(']','');
	tex = tex.replace('~','');
	tex = tex.replace(';','');
	tex = tex.replace("'","");
	tex = tex.replace('"','');
	$("#contestname").val(tex);
}
</script>
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> Contests
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
						<a href="<?php echo SITE_URL;?>/contests/index">Contests</a>
					</li>
				</ul>
                <!--<div class="page-toolbar">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
							<li>
								<?php
								$navigation = array(
								'  Contests' => '/contests/index',
								' Create Contest' => '/contests/add',
								);				
								$matchingLinks = array();
								foreach ($navigation as $link){
									if(preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base	)))){
										$matchingLinks[strlen($link)] = $link;
									}
								}
								krsort($matchingLinks);
								$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
								$out = array();
								foreach ($navigation as $title => $link){
									$out[] = '<li>'.$this->Html->link($title, $link, ife($link == $activeLink, array('class' => 'current'))).'</li>';
								}
								echo join("\n", $out);
								?>	
                            </li>
                        </ul>
                    </div>
                </div>	-->
			</div>
			<div class="clearfix"></div>
			<div class="portlet mt-element-ribbon light portlet-fit">
<div class="ribbon ribbon-right ribbon-clip ribbon-shadow ribbon-border-dash-hor ribbon-color-success uppercase">
<div class="ribbon-sub ribbon-clip ribbon-right"></div>
edit contest form
</div>
				<div class="portlet-title">
					<div class="caption font-red-sunglo">
						<i class="fa fa-trophy font-red-sunglo"></i>
						<span class="caption-subject bold uppercase"> </span>
					</div>
				</div>
				 <?php echo $this->Session->flash(); ?>
				<div class="portlet-body form">
					<?php echo $this->Form->create('Contest',array('action'=> 'edit','enctype'=>'multipart/form-data','onsubmit'=>'return validation()'));?>
						<div class="form-body">
							<div class="form-group">
								<label>Contest Name<span class="required_star"></span></label>
								<?php echo $this->Form->input('Contest.group_name',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'contestname','onKeyup'=>'return update1()'))?>
							</div>
							<div class="form-group">
								<label>Contest Keyword <span class="required_star"></span></label>
									<?php echo $this->Form->input('Contest.keyword',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'keyword'))?>
							</div>
							<div class="form-group">
						        <label>Group<span class="required_star"></span></label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The group to add the new contact if person entering the contest is not currently in your contact list." data-original-title="Add to Group" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
						        <?php
							        $Group[0]='Select Group';
							        echo $this->Form->input('Contest.group_id', array(
							        'class'=>'form-control',
							        'id'=>'group',
							        'label'=>false,
							        'type'=>'select',
							        'default'=>0,
							        'options' => $Group));
						        ?>
					        </div>
						    <div class="form-group">
							    <label>Start Date<span class="required_star"></span></label>
							    <div class="input">                                                   
								<?php echo $this->Form->input('Contest.start',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'startdate1','value'=>date('m/d/Y',strtotime($contest['Contest']['startdate'])),'Placeholder'=>'Select Start Date','onclick'=>"displayCalendar(startdate1,'mm/dd/yyyy',this)"));?> 
							    </div>
						    </div> 
						    <div class="form-group">
							    <label>End Date<span class="required_star"></span></label>
							    <div class="input">												
								<?php echo $this->Form->input('Contest.end',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'enddate1','value'=>date('m/d/Y',strtotime($contest['Contest']['enddate'])),'Placeholder'=>'Select End Date','onclick'=>"displayCalendar(enddate1,'mm/dd/yyyy',this)"));?>
						        </div>
						    </div> 
						    <div class="form-group">
							<label style="margin-top:5px">Max Entries:</label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="The maximum number of entries each person is allowed to enter the contest, from 1 - 10." data-original-title="Max Entries" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
							    <?php echo $this->Form->text('Contest.maxentries',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'maxentries','type'=>'number','min'=>'1','max'=>'10','value'=>$contest['Contest']['maxentries']))?>
					        </div>
							<div class="form-group">
								<label>Auto Reply Message<span class="required_star"></label>&nbsp;<a href="javascript:;" data-container="body" data-trigger="hover" data-html="true" data-content="Autoreply person receives after entering the contest. If the person entering is not currently in your contact list, they will be added to the group and join your list. This autoreply overrides the group's autoreply." data-original-title="Contest Autoreply" class="popovers"><i class="fa fa-question-circle" style="font-size:18px"></i></a>
									<?php echo $this->Form->input('Contest.system_message',array('div'=>false,'label'=>false, 'class' => 'form-control','id'=>'message','maxlength'=>'160','rows'=>'6','cols'=>46))?>
								<!--<div id='counter' style="margin-top:5px">Remaining Characters&nbsp;
									<script type="text/javascript">
										document.write("<input type=text  class='form-control input-xsmall' name=limit id=limit size=4 readonly value="+count+">");
									</script>
								</div>
									Special characters not allowed such as ~ { } [ ] ;-->
										<span id="messageErr" class="ValidationErrors"></span>
							</div>
						</div>
						<div class="form-actions">
							<?php echo $this->Form->submit('Update',array('div'=>false,'class'=>'btn blue'));?>
							<?php echo $this->Html->link(__('Cancel', true), array('controller' => 'contests','action' => 'index'),array('class'=>'btn default','style'=>'margin:0px;')); ?>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>                     
	</div>
</div>         