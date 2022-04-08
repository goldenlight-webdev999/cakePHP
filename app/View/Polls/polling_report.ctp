	<script src="https://ajax.googleapis.com/ajax/libs/mootools/1.3.0/mootools-yui-compressed.js" type="text/javascript"></script>


<?php

 echo $this->Html->script('charts/highcharts.js');



   //$graphTitle = 'Blank';
	//echo $number_list.'fff';
		for($i=0;$i<=6;$i++)
		{
		    if($i == 0){
			    $j = 'A';
			}
			if($i == 1){
				$j = 'B';
			}
			if($i == 2){
				$j = 'C';
			}
			if($i == 3){
				$j = 'D';
			}
			if($i == 4){
				$j = 'E';
			}
			if($i == 5){
				$j = 'F';
			}
		
			$cat[]=$j;
		}
		$graphTitle=$questions;
        $cat_List = json_encode($cat);	

?>
	<div class="page-content-wrapper">
		<div class="page-content">              
			<h3 class="page-title"><?php echo('View Polls');?></h3>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="icon-home"></i>
								<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li><span><?php echo('View Polls');?> </span></li>
					</ul>  
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
								<i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li>
									<?php
										$navigation = array(
										'Back' => '/polls/question_list',
										);				
										$matchingLinks = array();
										foreach ($navigation as $link) {
										if (preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base)))) {
										$matchingLinks[strlen($link)] = $link;
										}
										}
										krsort($matchingLinks);
										$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
										$out = array();
										foreach ($navigation as $title => $link) {
										$out[] = '<li>'.$this->Html->link($title, $link, ife($link == $activeLink, array('class' => 'current'))).'</li>';
										}
										echo join("\n", $out);
									?>
								</li>							
							</ul>
						</div>
					</div>				
				</div>			
				<div class="clearfix"></div>
				  <?php echo $this->Session->flash(); ?>	
				  <div class="m-heading-1 border-white m-bordered">								  	
					
						<div id="container_graph">
							<p></p>
						</div>
						
				</div>
				
				<!--*****************-->
			    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-list-ol font-red-sunglo"></i><span class="caption-subject font-red sbold uppercase">Individual Poll Responses</span>
                            </div>
                        </div>
                <div class="portlet-body">

						<table  id="datatable_index" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
							<thead>
								<tr>
									<th>Name</th>
          							<th>Number</th>
                                    <th>Response</th>
                                    <th>Answer</th>
									<th class="actions"><?php echo('Actions');?></th>
								</tr>
								<?php
									$i = 0;
									foreach ($contactvotes as $contactvote):
									$class = null;
									if ($i++ % 2 == 0) {
									$class = ' class="altrow"';
									}
								?>
							</thead>
							<!--<tbody>-->
								<tr <?php echo $class;?>>
									<td> <?php echo $contactvote['Contact']['name'];?>&nbsp; </td>
									<td> <?php echo $contactvote['Contact']['phone_number'];?>&nbsp; </td>
                                    <td> <?php echo $contactvote['Option']['optionb'];?>&nbsp;</td>
                                    <td> <?php echo $contactvote['Option']['optiona'];?>&nbsp;</td>
					
									<td class="actions">
									    
									    <?php if($userperm['sendsms']=='1'){ ?>
								<?php if(API_TYPE==0){?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

								<?php }else if(API_TYPE==1){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'nexmo_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

								<?php }else if(API_TYPE==2){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'slooce_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

								<?php }else if(API_TYPE==3){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'plivo_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>								
								<?php }else if(API_TYPE==4){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'bandwidth_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
                                <?php }else if(API_TYPE==5){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'signalwire_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>

                                <?php }else if(API_TYPE==6){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'ytel_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								
								<?php }else if(API_TYPE==7){ ?>

<?php echo $this->Html->link('<i class="icon-bubble" style="font-size:14px"></i>',array('action'=>'telnyx_send_sms','controller'=>'contacts',$contactvote['Contact']['phone_number']),array('class'=> 'btn green-jungle btn-sm nyroModal','escape'=> false,'title'=>'Send SMS','style'=>'margin-right:0px'));?>
								<?php }} ?>

									</td>
								</tr>
								<?php endforeach; ?>
							<!--</tbody>-->
						</table>
					
					<div class="pagination pagination-large">
                        <ul class="pagination">
                        <?php
                            echo $this->Paginator->first(__('<< First'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                            echo $this->Paginator->prev(__('<'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                            echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
                            echo $this->Paginator->next(__('>'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                            echo $this->Paginator->last(__('Last >>'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
                        ?>
                        </ul>
                    </div>
				</div>
			</div> 
				
				
				
<script type="text/javascript">
var chart;
var chart_graph;

function callFirst(){

  chart_graph = new Highcharts.Chart({
      chart: {
         renderTo: 'container_graph',
         defaultSeriesType: 'column'
      },
      title: {
         text: '<?php echo $graphTitle;?>'
      },
      subtitle: {
         text: ''
      },
      xAxis: {
         //categories: "'A', 'B', 'C', 'D'",
		 categories: <?php echo $cat_List;?>
         
      },
      yAxis: {
         min: 0,
         title: {
            text: '# of Votes'
         }
      },
      legend: {
         layout: 'vertical',
         backgroundColor: '#FFFFFF',
         align: 'left',
         verticalAlign: 'top',
         x: 100,
         y: 70,
         floating: true,
         shadow: true
      },
      tooltip: {
         formatter: function() {
            return ''+
               this.x +': '+ this.y +' Vote';
         }
      },
      plotOptions: {
         column: {
            pointPadding: 0.2,
            
            borderWidth: 0
         }
      },
           series: [{
         name: ' Answer',
		data:  <?php echo $caller_list;?>
     
   
      }]
   });	
 }
 
 window.onload=callFirst();
$(window).resize();
 
/*setTimeout(
function(){
	callFirst();
},
5000
);*/
</script>				