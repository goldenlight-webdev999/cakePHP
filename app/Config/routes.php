<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
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
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	//Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	//Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

	Router::connect('/', array('controller' => 'users', 'action' => 'home'));
	//Router::connect('/', array('controller' => 'users', 'action' => 'login'));

	Router::connect('/admin', array('controller' => 'admin_users', 'action' => 'login'));

/**

 * ...and connect the rest of 'Pages' controller's urls.
 */

    Router::connect('/weblinks', array('controller' => 'webwidgets'));

	Router::connect('/weblinks/:action/*', array('controller' => 'webwidgets', 'action' => 'index'));

	Router::connect('/page1', array('controller' => 'pages', 'action' => 'page1'));

	Router::connect('/page2', array('controller' => 'pages', 'action' => 'page2'));

	//Router::connect('/contacts/index/page/:page', array('controller'=>'contacts', 'action'=>'index'), array('page'=>':[0-9]+'));

	/* Paypal IPN plugin */

	Router::connect('/paypal_ipn/process', array('plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'process'));

	Router::connect('/paypal_ipn/purchase_credit/*', array('plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'purchase_credit'));

    Router::connect('/paypal_ipn/purchase_subscription/*', array('plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'purchase_subscription'));

    Router::connect('/paypal_ipn/purchase_subscription_numbers/*', array('plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'purchase_subscription_numbers'));


	  /* Optional Route, but nice for administration */

	Router::connect('/paypal_ipn/:action/*', array('admin' => 'true', 'plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'index'));
	Router::connect('/coupons/*', array('controller' => 'mobilecoupons', 'action' => 'view'));

  /* End Paypal IPN plugin */

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
