
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo('Q&A SMS Bot Participants');?></h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<span><?php echo('Q&A SMS Bot Participants');?></span>
				</li>
			</ul>  
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
					<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li>
							<a href="<?php echo SITE_URL;?>/multiquestions/export/<?php echo $id;?>" title="Export Q & A SMS Bots"><i class="fa fa-file-excel-o"></i> Export Q&A SMS Bot</a>
							<a href="<?php echo SITE_URL;?>/multiquestions" title="Back"><i class="fa fa-arrow-left"></i> Back</a>
						</li>
					</ul>
				</div>
			</div>				
		</div>
		<?php echo $this->Session->flash(); ?>				
		<div class="clearfix"></div>
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-question font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Q&A SMS Bot Participants');?></span> </div>
			</div>
			<div class="portlet-body">
					<table  id="datatable_receiptsnumbersindex" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>
								<th>Name</th>
								<th>Phone</th>
								<th>Question</th>
								<th>Answer</th>
								<th>Responded On</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($participant as $participant){ ?>
								<tr> 
									<td><?php echo ucfirst($participant['Contact']['name']);?></td>
									<td><?php echo ucfirst($participant['MultiContestSubscriberArchive']['phone']);?></td>
									<td><?php echo $participant['MultiContestSubscriberArchive']['question'];?></td>
									<td><?php echo $participant['MultiContestSubscriberArchive']['answer'];?></td>
									<!--<td><?php echo date('m/d/Y',strtotime($participant['MultiContestSubscriberArchive']['created']));?></td>-->
									<td><?php echo date('Y-m-d H:i:s',strtotime($participant['MultiContestSubscriberArchive']['created']));?></td>
								</tr>
							<?php } ?>
						</tbody>
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
	</div>
</div>
		