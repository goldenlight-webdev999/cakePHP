<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" >
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
        <title><?php echo SITENAME;?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/quicky-assets/webfonts/inter/inter.css"> 
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/quicky-assets/css/app.min.css">
        
        
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	    <link href="<?php echo SITE_URL; ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo SITE_URL; ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css">
        <link href="<?php echo SITE_URL; ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
     
        
        <?php if(THEME_COLOR == 'l1grey') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/themes/grey.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l1light') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/themes/light.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l1blue') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/themes/blue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l1dark') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l1default') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l2grey') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/themes/grey.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l2light') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/themes/light.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l2blue') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/themes/blue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l2dark') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/themes/dark.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else if(THEME_COLOR == 'l2default') {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout2/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <?}else {?>
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
               <link href="<?php echo SITE_URL; ?>/assets/layouts/layout/css/themes/blue.min.css" rel="stylesheet" type="text/css" id="style_color" />        
        <?}?>

        <link href="<?php echo SITE_URL; ?>/assets/pages/css/pricing.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="<?php echo SITE_URL; ?>/app/webroot/favicon.ico" />
	
	    <script src="<?php echo SITE_URL; ?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script type="text/javascript" src="https://api.filepicker.io/v1/filepicker.js"></script> 
	    <script src="<?php echo SITE_URL; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>	
	    <style>
			
        #flashMessage {
        font-size: 16px;
        font-weight: normal;
        }
       
       .message {
          background: #f6fff5 url("<?php echo SITE_URL; ?>/app/webroot/img/flashimportant.png") no-repeat scroll 15px 12px / 24px 24px;
          border: 1px solid #97db90;
          border-radius: 5px;
          color: #000;
          font-size: 13px;
          margin-bottom: 10px;
          padding: 13px 13px 13px 48px;
          text-decoration: none;
          text-shadow: 1px 1px 0 #fff;
      }
       </style>
			
		<?php
		  echo $this->Html->css('nyroModal');
          echo $this->Html->css('cycle');
		  echo $this->Html->script('jQvalidations/jquery.validation.functions');
		  echo $this->Html->script('jQvalidations/jquery.validate');
		  echo $this->Html->script('jquery.nyroModal.custom');
		  echo $this->Html->script('jquery.cycle.all');

		?>
		
        
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/quicky-assets/css/es_custom.css?t=<?php echo time(); ?>">
	</head>
	<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-sidebar-fixed chats-tab-open">
		
    <!-- Main Layout Start -->
    <div class="main-layout">
        
        <?php echo $this->element('siderbar'); ?>	

        

        <!-- Main Start -->
        <main class="main">
            <?php echo $this->Session->flash(); ?>				
            <?php  echo $content_for_layout; ?>  
        </main>
        <!-- Main End -->

        

        <div class="backdrop"></div>

        
    </div>
    <!-- Main Layout End -->
    
    <!-- Javascript Files -->
    <script src="<?php echo SITE_URL; ?>/quicky-assets/vendors/jquery/jquery-3.5.0.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/quicky-assets/vendors/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/quicky-assets/vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/quicky-assets/vendors/svg-inject/svg-inject.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/quicky-assets/vendors/modal-stepes/modal-steps.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/quicky-assets/js/app.js"></script>
    <script src="<?php echo SITE_URL; ?>/quicky-assets/js/require.js"></script>
    </body>
</html>
