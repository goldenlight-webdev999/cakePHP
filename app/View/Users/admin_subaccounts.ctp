
		 <?php 
		 if(!empty($subaccounts)){?>
		 
		 <table id="tableOne" cellspacing="0" cellpadding="0"style="width:100%;" >
		 <thead>
		 <tr>
		
			<th class="tc">User Name</th>
			<th class="tc">First Name</th>
			<th class="tc">Last Name</th>
			<th class="tc">Email</th>
	
		 </tr>
				
	     </thead>
		 <tbody>
		 <?php		
		        $i = 0;
		            foreach($subaccounts as $subaccount) { 
		            $class = null;
		
		            if ($i++ % 2 == 0) {
			            $class = ' class="altrow"';
		            }
	            
	    ?>
	    
	    <tr <?php echo $class;?>> 
			 
			   <td><?php echo $subaccount['Subaccount']['username'] ?></td>
			   <td><?php echo $subaccount['Subaccount']['first_name'] ?></td>
			   <td><?php echo $subaccount['Subaccount']['last_name'] ?></td>
			   <td><?php echo $subaccount['Subaccount']['email'] ?></td>
	    </tr>		   
			   
	    <?php } ?>
	    
	    </tbody>
        </table>
     
		<?php }else{
	    echo '<div style="font-weight: bold; font-size: 15px;text-align: center;">There are no sub-accounts for this user.</div>';
	    
	    }?>
			  
			 
