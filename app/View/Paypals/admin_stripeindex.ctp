<ul  class="secondary_nav" style="width:100%">
				<?php
				$navigation = array(
					  	'List Stripe' => '/admin/paypals/stripeindex',
						'Edit Stripe' => '/admin/paypals/stripeedit/1'
					   					   
				);				
				$matchingLinks = array();
				
				foreach ($navigation as $link) {
						if (preg_match('/^'.preg_quote($link, '/').'/', substr($this->here, strlen($this->base)))) {
								$matchingLinks[strlen($link)] = $link;
						}
				}
				
				krsort($matchingLinks);
				
				//$activeLink = ife(!empty($matchingLinks), array_shift($matchingLinks));
				$activeLink = !empty($matchingLinks)?array_shift($matchingLinks):'';
				$out = array();
				
				foreach ($navigation as $title => $link) {
						//$out[] = '<li>'.$html->link($title, $link, ife($link == $activeLink, array('class' => 'current'))).'</li>';
						$out[] = '<li>'.$this->Html->link($title, $link, $link == $activeLink ? array('class' => 'current'):'').'</li>';
				}
				
				echo join("\n", $out);
				?>			
</ul>
<style>

h3 {
    color: #C6C65B;
    font-family: 'Gill Sans','lucida grande',helvetica,arial,sans-serif;
    font-size: 150%;
    padding-top: 0px;
}
table tr.altrow td {
    text-align: center!important;
}
</style>

<br/><br/>
<link href="<?php echo SITE_URL; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<div style="display:table;width:100%"><div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-user" style="color:#26C281"></i>&nbsp;Client Stats :: Active Clients</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('activeclientsall')?></td></tr></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Sent</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smssentall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-comments-o" aria-hidden="true" style="color:#26C281"></i> SMS Stats :: SMS Received</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedmonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedyear')?></td></tr><!--<tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('smsreceivedall')?></td></tr>--></table> </div>

<div style="height:100%; width: 24%; margin-right: 10px; float:left; border: solid 1px; border-color: rgb(204, 204, 204);"> <h3 style="color: rgb(51, 51, 51); background-color: #fffdc9;margin-top:0px;margin-bottom:0px;padding:5px;text-align:center"><i class="fa fa-usd" aria-hidden="true" style="color:#26C281"></i> Financial Stats :: Revenue</h3><table style="width:100%;height:140px;padding:5px"><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Past 7 days</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueweek')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Month</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenuemonth')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Current Year</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueyear')?></td></tr><tr><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);">Overall</td><td style="font-weight:bold;color: #3598dc;border: solid 1px; border-color: rgb(204, 204, 204);"><?php echo $this->Session->read('revenueall')?></td></tr></table> </div>
</div>

<div class="configs index" style="margin-right:20px">
	<h2><?php echo('Stripe Config');?></h2>
	
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Id</th>
			<th>Secret Key</th>
			<th>Publishable Key</th>
			<th class="actions"><?php echo('Actions');?></th>
	</tr>
	<tr>
		<td><?php echo $stripes['Stripe']['id']; ?>&nbsp;</td>
		<td><?php echo $stripes['Stripe']['secret_key']; ?>&nbsp;</td>
		<td><?php echo $stripes['Stripe']['publishable_key']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'stripeedit', $stripes['Stripe']['id'])); ?>
		</td>
	</tr>
	</table>
	
</div>