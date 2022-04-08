<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		
		<?php echo $title_for_layout;?>
	</title>

	<?php echo $this->Html->charset(); ?>

	<link rel="icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
	<?php echo $this->Html->css('admin');?>
	<?php echo $this->Html->css('nyroModal');?>
	<?php echo $this->Html->script('jquery-1.7.1.js');?>
	<?php echo $this->Html->script('jquery.nyroModal.custom');?>
	<?php echo $scripts_for_layout;?>
	
</head>
<style>
#flashMessage{
font-size:16px;

}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$('a.nyroModal').nyroModal();
	
});
</script>



<body>
	<div id="container">
		<div id="header">
			
				<h1><?php echo "Admin Panel" ; ?></h1>
				
				<?php if($this->Session->check('AdminUser')): ?>
				<?php $admin=$this->Session->read('AdminUser') ?>
				<p>Welcome&nbsp;&nbsp;<?php echo ucfirst($admin['username']) ; ?></p>
				<?php endif; ?>	
		</div>
			
				<ul id="nav">	
                
<?php
		if($this->Session->check('AdminUser')):
		$navigation = array(
       //'Config' => '/admin/configs/index',
       'Config' => '/admin/configs/edit/1',
	   'Users' => '/admin/users/index',
	   'Packages' => '/admin/packages/index',
	   'Referrals' => '/admin/referrals/index',
	   'Reports' => '/admin/users/user_messages?all=show',
	   'Change Password' => '/admin_users/change_password',
	   'Logout' => '/admin_users/logout'		
);

$matchingLinks = array();

foreach ($navigation as $link) {
        if (preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base)))) {
                $matchingLinks[strlen($link)] = $link;
        }
}

krsort($matchingLinks);

//$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
$activeLink = !empty($matchingLinks) ? array_shift($matchingLinks) : '';

$out = array();

foreach ($navigation as $title => $link) {
        if($link == $activeLink){
            $class = "array('class' => 'current')";
        }
        //$out[] = '<li>'.$this->Html->link($title, $link, ife($link == $activeLink, array('class' => 'current'))).'</li>';
        $out[] = '<li>'.$this->Html->link($title, $link, $link == $activeLink ? array('class' => 'current') : '').'</li>';
}

echo join("\n", $out);
endif;
?>
</ul>		

							
			
			
		</div>
		<div id="content">
			<?php
				if ($this->Session->check('Message.flash')):
						echo $this->Session->flash();
				endif;
			?>

			<?php echo $content_for_layout;?>

		</div>
		
		</div>
	</div>
	<!--<?php echo $this->element('sql_dump'); ?>-->
</body>
</html>


