<?php

$currentPlan = filter_input(INPUT_GET, "plan");    
?>
<style>

    .flag-sec label { width: 100%;}
    .flag-sec #image { margin: 0 12px 0 0;}
    .cntry-slct {   width: auto; }

    .nyroModalLink, .nyroModalDom, .nyroModalForm, .nyroModalFormFile {

        max-width: 1000px;
        min-height : 100px;
        position: relative;
        box-sizing : border-box;
    }
</style>
<style>
    .feildbox img{
        width:30px!important;
    }

    /* The container */
    .option-container {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 20px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-color : #32c5d2;
        color : white;
        padding-left : 45px;
        text-align: left;
    }
    .option-container:hover {
        background-color: royalblue;
        color : skyblue;
    }

    /* Hide the browser's default radio button */
    .option-container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark {
        margin: 10px;
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border-radius: 50%;
    }

    /* On mouse-over, add a grey background color */
    .option-container:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .option-container input:checked ~ .checkmark {
        background-color: orange;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .option-container input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .option-container .checkmark:after {
        top: 9px;
        left: 9px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
    }
    div.options-container{
        position : relative;
        margin : 0px;
        margin-bottom : 10px;
        width : 450px;
    }
    .nyroModalCont{
        width : auto !important;
        height : auto !important;
    }
</style>
<div class="portlet box blue-dark" style = "margin-bottom : 0px;">
    <div class="portlet-title">
        <div class="caption">
            Upgrade Plan
        </div>
    </div>
    <div class="portlet-body" style = "">
        <h3 style = "margin-top:0px;font-weight:bold;"><small>Your current plan is</small> <?php print $currentPlan; ?></h3>
<?php 
            $currencycode=PAYMENT_CURRENCY_CODE;
            $symbol = "\$";
	    if($currencycode=='MXN' || $currencycode=='USD' || $currencycode=='AUD' || $currencycode=='CAD' || $currencycode=='HKD' || $currencycode=='NZD' || $currencycode=='SGD'){
                $symbol = "\$";
            } else if($currencycode=='JPY'){
                $symbol = "¥";
            } else if($currencycode=='EUR'){ 
                $symbol ="€";
            } else if($currencycode=='GBP'){ 
                $symbol ="£";
            } else if($currencycode=='DKK' || $currencycode=='NOK' || $currencycode=='SEK'){ 
                $symbol ="kr";
            } else if($currencycode=='CHF'){ 
                $symbol = "CHF";
            } else if($currencycode=='BRL'){
                $symbol ="R$";
            } else if($currencycode=='PHP'){
                $symbol = "₱";
            } else if($currencycode=='ILS'){ 
                $symbol = "₪";
            }
            
            ?>
        <div class="options-container">
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_content1" id="monthly-plans-tab" role="tab" data-toggle="tab" aria-expanded="true">Monthly Plans</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content2" role="tab" id="pay-and-go-tab" data-toggle="tab" aria-expanded="false">Pay & Go</a>
                    </li>
                </ul>
                <div id="radio-options" class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="monthly-plans-tab">
                        Please select your desired monthly package.
                        <br/><br/>
                        <div style = "max-height: 300px;overflow-y: auto;">
                    <?php
                        //$qry = DB::$db->prepare("SELECT * FROM `monthly_packages` WHERE `user_country` = :country ORDER BY `monthly_packages`.`amount` ASC");
                        //$qry->bindParam("country", $_SESSION["User"]["user_country"]);
                        //$qry->execute();
                        //$rows = $qry->fetchAll(PDO::FETCH_ASSOC);
                        foreach($monthlydetails as $row){
                    ?>
                            <label class="option-container btn btn-blue"><?php print $row["MonthlyPackage"]["package_name"]; ?>
                                <input type="radio" name="option-price" onchange="enablePayment(this, 'subscription')" data-id="<?php print $row["MonthlyPackage"]["id"]; ?>"/>
                                <span class="checkmark"></span>
                                <div style="float : right">
                                    <table>
                                        <tr>
                                            <td style = "width : 40px;">
                                                <label style="font-size : 12px;"><b><?php print $row["MonthlyPackage"]["text_messages_credit"]; ?></b> <i class="fa fa-comment-o"></i></label>
                                                <label style="font-size : 12px;"><b><?php print $row["MonthlyPackage"]["voice_messages_credit"]; ?></b> <i class="fa fa-phone"></i></label>
                                            </td>
                                            <td style = "width : 130px; text-align : right;">
                                                 <?php print "<font color = 'whitesmoke'>" . $symbol . "</font> <b>" . $row["MonthlyPackage"]["amount"] . "</b>"; ?>/<small style = "font-size : 11px;">month</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </label>
                    <?php
                        }
                    ?> 
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="pay-and-go-tab">
                        Please select your desired credit package.
                        <br/><br/>
                        <div style = "max-height: 300px;overflow-y: auto;">
                    <?php
                        //$qry = DB::$db->prepare("SELECT * FROM `packages` WHERE `user_country` = :country ORDER BY `packages`.`amount` ASC");
                        //$qry->bindParam("country", $_SESSION["User"]["user_country"]);
                        //$qry->execute();
                        //$rows = $qry->fetchAll(PDO::FETCH_ASSOC);
                        foreach($packagedetails as $row){
                    ?>
                            <label class="option-container btn btn-blue"><?php print $row["Package"]["name"]; ?>
                                <input type="radio" name="option-price" onchange="enablePayment(this, 'credit')" data-id="<?php print $row["Package"]["id"]; ?>"/>
                                <span class="checkmark"></span>
                                <div style="float : right">
                                    <table>
                                        <tr>
                                            <td style = "width : 40px;">
                                                <label style="font-size : 12px;"><b><?php print $row["Package"]["credit"]; ?></b> 
                                                <?php if($row["Package"]["type"]=='text') {?>
                                                    <i class="fa fa-comment-o"></i></label>
                                                <?}else{?>
                                                    <i class="fa fa-phone"></i></label>
                                                <?}?>
                                            </td>
                                            <td style = "width : 130px; text-align : right;">
                                                 <?php print "<font color = 'whitesmoke'>" . $symbol . "</font> <b>" . $row["Package"]["amount"] . "</b>"; ?></small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </label>
                    <?php
                        }
                    ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
            <?php
            $payment=PAYMENT_GATEWAY;
            if(PAYMENT_GATEWAY ==1){?>
                <a class="nyroModal" id = "paypal-button"><img src = "<?php echo SITE_URL ?>/img/buy-logo-medium.png" style="height:30px"/></a><br />
    	    <?php }else if(PAYMENT_GATEWAY==2){?>
                <a class="nyroModal" id = "stripe-button"><img src = "<?php echo SITE_URL ?>/img/stripe-pay-button.png" style="height:30px"/></a><br />
    	    <?php }else if(PAYMENT_GATEWAY == 3){ ?>
                <a class="nyroModal" id = "paypal-button"><img src = "<?php echo SITE_URL ?>/img/buy-logo-medium.png" style="height:30px"/></a>
                 
                <a class="nyroModal" id = "stripe-button"><img src = "<?php echo SITE_URL ?>/img/stripe-pay-button.png" style="height:30px"/></a>
    	<?php } ?> 
        <br/>
        <script>
            function enablePayment(sender, mode) {
                $("#paypal-button").attr("href", "<?php print SITE_URL; ?>/users/purchase_" + mode + "/" + $(sender).attr("data-id"));
                $("#stripe-button").attr("href", "<?php print SITE_URL; ?>/users/purchase_" + mode + "_stripe/" + $(sender).attr("data-id"));
                $(".nyroModal").nyroModal();
            }

        </script>
    </div>