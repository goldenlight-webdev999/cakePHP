<div class="changepassword">
<?php
echo $this->Form->create('AdminUser',array('action'=>'change_password'));
if($this->Session->check('AdminUser'))
	$admin=$this->Session->read('AdminUser');
echo $this->Form->hidden('username',array('value'=>$admin['username']));
echo $this->Form->input('Old password');
echo $this->Form->input('New Password',array('type'=>'password'));
echo $this->Form->input('New Password Again',array('type'=>'password'));
echo $this->Form->end('Submit');
?>
</div>
