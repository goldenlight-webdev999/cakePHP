<div id="index_form">

				<h2 class="login">Administration Login</h2>
				<div class="loginbox">
				
				
				<form method="post" action="<?PHP echo $this->Html->url('/admin_users/login') ?>" style="width:55%">	
					<?php echo $this->Form->input('username'); ?> 
					<div class="input">
					<label for="password1">Password</label> 
					<?php echo $this->Form->password('password'); ?>
					</div>
					</div>
					<?php echo  $this->Form->end('Submit');?></form>
</div>
