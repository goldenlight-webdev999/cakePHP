<style>
.nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {
    max-width: 1000px;
    min-height: 71px;
    min-width: 650px;
    padding: 10px;
    position: relative;
}
</style>
 			<div class="portlet box blue-dark">
				<div class="portlet-title">
						<div class="caption">
						<?php  echo ('Notes');?>
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
					   <div class="page-toolbar">  
        					<div class="btn-group pull-right" style="padding-right:2px;padding-bottom:2px">
        						<?php echo $this->Html->link('<i class="icon-plus" style="font-size:14px"></i>',array('action'=>'addnote/'.$contactID),array('class'=> 'btn green-meadow btn-sm nyroModal','escape'=> false,'title'=>'Add Note','style'=>'margin-right:0px;z-index:9000')); ?>
        					</div>
				        </div>
			    
						<div class="table-scrollable">
							<!--div><//?php $i = 0; $class = ' class="altrow"';?></div-->
							<table class="table table-bordered table-striped">
								<thead>
                        		<tr>
                        			<th class="tc">Name</th>
                        			<th class="tc">Note</th>
                        			<th class="tc">Date/Time</th>
                        			<th class="tc">Actions</th>
                        		</tr>
                        	    </thead>
                        		<tbody>		
                        		<?php 
                        		if(!empty($contactNotes)){
                        		$i = 0;
                        		foreach($contactNotes as $contactNote) { 
                        		$class = null;
                        		
                        		if ($i++ % 2 == 0) {
                        			$class = ' class="altrow"';
                        		}
                        		
                        		?>
                        	
                        	    <tr <?php echo $class;?>> 
                        			   <td style="text-align:left;"><?php echo $contactNote['ContactNote']['name'] ?></td>
                        			   <td style="text-align:left;" ><?php echo $contactNote['ContactNote']['note'] ?></td>
                        			   <td style="text-align:left;" ><?php echo $contactNote['ContactNote']['created'] ?></td>
                        			   <td style="text-align:left;width:100px" class="actions">
                        			       <?php echo $this->Html->link('<i class="icon-note" style="font-size:14px"></i>',array('action'=>'editnote',$contactNote['ContactNote']['id']),array('class'=> 'btn green btn-sm nyroModal','escape'=> false,'title'=>'Edit Note','style'=>'margin-right:0px;z-index:9000')); ?>
                        			       <?php echo $this->Html->link('<i class="icon-trash" style="font-size:14px"></i>',array('action'=>'deletenote', $contactNote['ContactNote']['id']),array('class'=> 'btn red-thunderbird btn-sm','escape'=> false,'title'=>'Delete Note'), sprintf(__('Are you sure you want to delete this note?',true))); ?>
                        			   </td> 
                        		</tr>
                        	    <?php }} ?>
                        		</tbody>	
							</table>							
						</div>
						
					</div>
				</div>
			</div>
