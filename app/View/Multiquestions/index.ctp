
<div class="page-content-wrapper">
	<div class="page-content">              
		<h3 class="page-title"> <?php echo('Q&A SMS Bots');?></h3>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="<?php echo SITE_URL;?>/users/dashboard">Dashboard</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<span><?php echo('Q&A SMS Bots');?></span>
				</li>
			</ul>  
			<div class="page-toolbar">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
					<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li>
							<a href="<?php echo SITE_URL;?>/multiquestions/add" title="Add Q&A SMS Bot"><i class="fa fa-plus-square-o"></i> Add Q&A SMS Bot</a>
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
					<i class="fa fa-question font-red"></i><span class="caption-subject font-red sbold uppercase"><?php echo('Q&A SMS Bots');?></span> </div>
			</div>
			<div class="portlet-body">
					<table  id="datatable_MultiContest" class="table table-striped table-bordered table-hover order-column dataTable no-footer" role="grid" aria-describedby="sample_1_info">
						<thead>
							<tr>
								<th scope="col">Name</th>
								<th scope="col">Group</th>
								<th scope="col">keyword</th>
								<th scope="col">Created</th>
								<th scope="col">Action </th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($contest_arr as $contest_arrs){ ?>
								<tr>
									<td class="tc"><?php echo $contest_arrs['MultiContest']['name'];?></td>
									<td class="tc"><?php echo $contest_arrs['Group']['group_name']; ?></td>
									<td class="tc"><?php echo $contest_arrs['MultiContest']['keyword']; ?></td>
									<td class="tc"><?php echo date('Y-m-d H:i:s',strtotime($contest_arrs['MultiContest']['created'])) ?></td>
									<td class="tc">
									    <?php echo $this->Html->link('<i class="icon-users" style="font-size:14px"></i>',array('action'=>'participants',$contest_arrs['MultiContest']['id']),array('class'=> 'btn yellow-gold btn-sm','escape'=> false,'title'=>'View Participant Answers','style'=>'margin-right:0px')); ?>
										<?php echo $this->Html->link('<i class="icon-eye" style="font-size:14px"></i>',array('action'=>'view',$contest_arrs['MultiContest']['id']),array('class'=> 'btn purple btn-sm','escape'=> false,'title'=>'View Q&A Bot','style'=>'margin-right:0px')); ?>
										<?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'edit',$contest_arrs['MultiContest']['id']),array('class'=> 'btn green btn-sm','escape'=> false,'title'=>'Edit','style'=>'margin-right:0px')); ?>
										<?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'delete',$contest_arrs['MultiContest']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete'), sprintf(__('Are you sure you want to delete this Q&A SMS Bot?', true))); ?>
									</td>
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
		