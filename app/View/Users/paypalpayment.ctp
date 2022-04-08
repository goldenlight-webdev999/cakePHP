<div class="page-content-wrapper">
  <div class="page-content">
    <h3 class="page-title">Packages<img style="float:right;margin-top:-17px" src="https://www.paypalobjects.com/webstatic/mktg/logo/bdg_secured_by_pp_2line.png"></h3>
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li> <i class="icon-home"></i> <a href="<?php echo SITE_URL; ?>/users/dashboard">Home</a> <i class="fa fa-angle-right"></i> </li>
        <li> <span>Packages</span> </li>
      </ul>
    </div>
    <div class="portlet light portlet-fit ">
      <div class="portlet-title">
        <div class="caption"> <i class="fa fa-cart-plus font-blue"></i> <span class="caption-subject font-blue bold uppercase">MONTHLY PACKAGES</span> </div>
      </div>
      <div class="portlet-body">
        <div class="pricing-content-1">
          <div class="row">
		  <?php 
		  foreach ($monthlydetails as $monthlydetail){ $i=0;?>
            <div class="col-md-4">
              <div class="price-column-container border-active">
                <div class="price-table-head bg-blue">
                  <h2 class="no-margin"><?php echo ucfirst($monthlydetail['MonthlyPackage']['package_name']);?></h2>
                </div>
                <div class="arrow-down border-top-blue"></div>
                <div class="price-table-pricing">
					<?php 
					$currencycode=PAYMENT_CURRENCY_CODE;
					if($currencycode=='MXN' || $currencycode=='USD' || $currencycode=='AUD' || $currencycode=='CAD' || $currencycode=='HKD' || $currencycode=='NZD' || $currencycode=='SGD'){ ?>
						<h3><span class="price-sign">$</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='JPY'){ ?>
						<h3><span class="price-sign">¥</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='EUR'){ ?>
						<h3><span class="price-sign">€</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='GBP'){ ?>
						<h3><span class="price-sign">£</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='DKK' || $currencycode=='NOK' || $currencycode=='SEK'){ ?>
						<h3><span class="price-sign">kr</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='CHF'){ ?>
						<h3><span class="price-sign">CHF</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<h3><?php } else if($currencycode=='BRL'){ ?>
						<h3><span class="price-sign">R$</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='PHP'){ ?>
						<h3><span class="price-sign">₱</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
						<?php } else if($currencycode=='ILS'){ ?>
						<h3><span class="price-sign">₪</span><?php echo $monthlydetail['MonthlyPackage']['amount'];?></h3><p>per month</p>
					<?php } ?>
                </div>
                <div class="price-table-content">
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i class="icon-bubbles"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding"><b><?php echo $monthlydetail['MonthlyPackage']['text_messages_credit'];?> SMS Credits</b></div>
                  </div>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i class="icon-volume-2"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding"><b><?php echo $monthlydetail['MonthlyPackage']['voice_messages_credit'];?> Voice Credits</b></div>
                  </div>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i class="icon-key"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding"><b>Unlimited Keywords</b></div>
                  </div>
                  <div class="price-table-head bg-purple-plum" style="margin-top:5px">
                  <h2 class="no-margin">Features</h2>
                  </div>
                  <div class="arrow-down border-top-purple-plum"></div>
                  <?php if($monthlydetail['MonthlyPackage']['getnumbers'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Get Numbers</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Get Numbers</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['sendsms'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Send SMS</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Send SMS</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['groups'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Group Management</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Group Management</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['contactlist'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Contact Management</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Contact Management</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['importcontacts'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Import Contacts</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Import Contacts</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['autoresponders'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Autoresponders</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Autoresponders</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['smschat'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">2-Way SMS Chat</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">2-Way SMS Chat</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['calendarscheduler'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Calendar/Scheduler</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Calendar/Scheduler</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['appointments'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Appointment Management</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Appointment Management</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['voicebroadcast'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Voice Broadcast</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Voice Broadcast</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['polls'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Polls</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Polls</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['contests'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Contests</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Contests</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['loyaltyprograms'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Loyalty Programs</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Loyalty Programs</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['kioskbuilder'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Kiosk Builder</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Kiosk Builder</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['birthdaywishes'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Birthday SMS Wishes</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Birthday SMS Wishes</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['mobilepagebuilder'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Mobile Page Builder</div>
                  </div>
                  <?}else{
                     $closediv[$i] ='<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Mobile Page Builder</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['webwidgets'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Web Widgets</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Web Widgets</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['qrcodes'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">QR Codes</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">QR Codes</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['shortlinks'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Short Links</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Short Links</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  <?php if($monthlydetail['MonthlyPackage']['affiliates'] == 1) {?>
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#26C281;font-size:18px" class="icon-check"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Affiliate System</div>
                  </div>
                  <?}else{
                     $closediv[$i] = '<div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i style="color:#e35b5a;font-size:18px" class="icon-close"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding">Affiliate System</div>
                  </div>';
                  $i=$i+1;
                  
                  } ?>
                  
                  <?php
                  for ($j=0;$j<$i;$j++) { 
                      echo $closediv[$j];
                     //<div class="row mobile-padding">
                      //  <div class="col-xs-9 text-left mobile-padding">&nbsp;</div>
                    //</div>-->
                  } ?>
                </div>
                <div class="arrow-down arrow-grey"></div>
                <div class="price-table-footer">
					<!--<?php echo $this->Form->create('User',array('action'=> 'paypalpayment/'.$this->Session->read('User.id'),'name'=>'signupForm','id'=>'signupForm'));?>
						<input type="hidden" name="data[MonthlyPackage][packageid]" value="<?php echo $monthlydetail['MonthlyPackage']['id']?>">
						<input type="hidden" name="data[User][id]" value="<?php echo $this->Session->read('User.id'); ?>">
						<input type="hidden" name="data[User][amount]" value="<?php echo $monthlydetail['MonthlyPackage']['amount']?>">
						<input type="hidden" name="data[User][package_name]" value="<?php echo $monthlydetail['MonthlyPackage']['package_name']?>">
						<button type="submit" class="btn green btn-outline sbold uppercase price-button">Sign Up</button>
					<?php echo $this->Form->end();?>-->

                
<a class="nyroModal" href="<?php echo SITE_URL; ?>/users/purchase_subscription/<?php echo $monthlydetail['MonthlyPackage']['id']?>"><button type="button" class="btn blue btn-outline sbold uppercase price-button">Confirm</button></a>

                </div>
              </div>
            </div>
			<?php } ?>
          </div>
        </div>
      </div>
    </div>
	<div class="portlet light portlet-fit ">
      <div class="portlet-title">
        <div class="caption"> <i class="fa fa-comment font-red"></i> <span class="caption-subject font-red bold uppercase">SMS ADD-ON PACKAGES</span> </div>
      </div>
      <div class="portlet-body">
        <div class="pricing-content-1">
          <div class="row">
		  <?php foreach ($Packagedetails as $Packagedetail){ ?>
            <div class="col-md-4">
              <div class="price-column-container border-active">
                <div class="price-table-head bg-red">
                  <h2 class="no-margin"><?php echo ucfirst($Packagedetail['Package']['name']);?></h2>
                </div>
                <div class="arrow-down border-top-red"></div>
                <div class="price-table-pricing">
                 <?php 
					$currencycode=PAYMENT_CURRENCY_CODE;
					if($currencycode=='MXN' || $currencycode=='USD' || $currencycode=='AUD' || $currencycode=='CAD' || $currencycode=='HKD' || $currencycode=='NZD' || $currencycode=='SGD'){ ?>
						<h3><span class="price-sign">$</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='JPY'){ ?>
						<h3><span class="price-sign">¥</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='EUR'){ ?>
						<h3><span class="price-sign">€</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='GBP'){ ?>
						<h3><span class="price-sign">£</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='DKK' || $currencycode=='NOK' || $currencycode=='SEK'){ ?>
						<h3><span class="price-sign">kr</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='CHF'){ ?>
						<h3><span class="price-sign">CHF</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<h3><?php } else if($currencycode=='BRL'){ ?>
						<h3><span class="price-sign">R$</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='PHP'){ ?>
						<h3><span class="price-sign">₱</span><?php echo $Packagedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='ILS'){ ?>
						<h3><span class="price-sign">₪</span><?php echo $Packagedetail['Package']['amount'];?></h3>
					<?php } ?>
                </div>
                <div class="price-table-content">
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i class="icon-bubbles font-red"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding"><?php echo $Packagedetail['Package']['credit'];?> SMS Credits</div>
                  </div>
                </div>
                <div class="arrow-down arrow-grey"></div>
                <div class="price-table-footer">
                   <a class="nyroModal" href="<?php echo SITE_URL; ?>/users/purchase_credit/<?php echo $Packagedetail['Package']['id']?>"><button type="button" class="btn red btn-outline sbold uppercase price-button">Confirm</button></a>

                   
                </div>
              </div>
            </div>
			<?php } ?>
          </div>
        </div>
      </div>
    </div>
	<div class="portlet light portlet-fit ">
      <div class="portlet-title">
        <div class="caption"> <i class="fa fa-bullhorn font-green"></i> <span class="caption-subject font-green bold uppercase">VOICE ADD-ON PACKAGES</span> </div>
      </div>
      <div class="portlet-body">
        <div class="pricing-content-1">
          <div class="row">
				<?php foreach ($Packagevoicedetails as $Packagevoicedetail){ ?>
            <div class="col-md-4">

              <div class="price-column-container border-active">
                <div class="price-table-head bg-green">
                  <h2 class="no-margin"><?php echo ucfirst($Packagevoicedetail['Package']['name']);?></h2>
                </div>
                <div class="arrow-down border-top-green"></div>
                <div class="price-table-pricing">
                  <?php 
					$currencycode=PAYMENT_CURRENCY_CODE;
					if($currencycode=='MXN' || $currencycode=='USD' || $currencycode=='AUD' || $currencycode=='CAD' || $currencycode=='HKD' || $currencycode=='NZD' || $currencycode=='SGD'){ ?>
						<h3><span class="price-sign">$</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='JPY'){ ?>
						<h3><span class="price-sign">¥</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='EUR'){ ?>
						<h3><span class="price-sign">€</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='GBP'){ ?>
						<h3><span class="price-sign">£</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='DKK' || $currencycode=='NOK' || $currencycode=='SEK'){ ?>
						<h3><span class="price-sign">kr</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='CHF'){ ?>
						<h3><span class="price-sign">CHF</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<h3><?php } else if($currencycode=='BRL'){ ?>
						<h3><span class="price-sign">R$</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='PHP'){ ?>
						<h3><span class="price-sign">₱</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
						<?php } else if($currencycode=='ILS'){ ?>
						<h3><span class="price-sign">₪</span><?php echo $Packagevoicedetail['Package']['amount'];?></h3>
					<?php } ?>
                </div>
                <div class="price-table-content">
                  <div class="row mobile-padding">
                    <div class="col-xs-3 text-right mobile-padding"> <i class="icon-volume-2 font-green"></i> </div>
                    <div class="col-xs-9 text-left mobile-padding"><?php echo $Packagevoicedetail['Package']['credit'];?> Voice Credits</div>
                  </div>
                </div>
                <div class="arrow-down arrow-grey"></div>
                <div class="price-table-footer">
                  <a class="nyroModal" href="<?php echo SITE_URL; ?>/users/purchase_credit/<?php echo $Packagevoicedetail['Package']['id']?>"><button type="button" class="btn green btn-outline sbold uppercase price-button">confirm</button></a>


                </div>
              </div>
            </div>
			<?php } ?>
          </div>
        </div>
      </div>
    </div>
	
  </div>
</div>