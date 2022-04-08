<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * This is email configuration file.
 *
 * Use it to configure SMTP email transports.
 *
*/
 

class EmailConfig {

    //>>>>> Enter in your SMTP server details below. Could be Sendgrid, Mailjet, SMTP2GO, etc... <<<<<<
    //>>>>> SENDGRID SMTP CONFIG SAMPLE BELOW <<<<<<
    //>>>>> https://app.sendgrid.com/guide/integrate/langs/smtp <<<<<<
    
    //Non-secured
    // public $smtp = array(
	   //  'transport' => 'smtp',
    //     'host' => 'email-smtp.us-west-2.amazonaws.com',
    //     'port' => 587, 
    //     'timeout' => 30,
    //     'username' => 'AKIAY7TZ6SZHN7VRUFOT',
    //     'password' => 'BHleMjBNskQqkQe/ZzCm2x7LPUBJRxiIP0g2zPcTYVHc',
    //     'tls' => true,
    // );
    
    //Secured
    public $smtp = array(
	   'transport' => 'Smtp',
       'host' => 'email-smtp.us-west-2.amazonaws.com',
       'port' => 587, 
       'timeout' => 30,
       'username' => 'AKIAY7TZ6SZHN7VRUFOT',
       'password' => 'BHleMjBNskQqkQe/ZzCm2x7LPUBJRxiIP0g2zPcTYVHc',
       'tls'=>true
    );
}

