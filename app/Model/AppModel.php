<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model 
{


	// we want all validation errors with i18n support
	var $validationSet;
	function invalidate($field, $value = true) 
	{
		return parent::invalidate($field, __($value, true));
	}

	protected function getCurrentId($id=null) 
	{
		if(!$id) 
		{
			if(!$this->id) 
			{
				return false;
			}
			$id = $this->id;
		}
		return $id;
	}
	
	/*
	Copied from 
	http://snook.ca/archives/cakephp/multiple_validation_sets_cakephp/

	------------ Method 1
	
	class User extends AppModel 
	{
	   // performs normal validation
	   var $validate = array( ... ); 
	   // used in an edit action like /users/edit/1
	   var $validateEdit = array( ... ); 
	   // used in a forgotpassword action like /users/forgotpassword
	   var $validateForgotpassword = array( ... );
	}
	
	class UsersController extends AppController 
	{
	   function forgotpassword() 
	   {
		  $this->User->set($this->data);
		  if ($this->User->validates()) 
		  {
			 // send email to reset password and show success message
		  }
	   }
	}
	
	------------ Method 2
	
	class User extends AppModel 
	{
		var $validateForgotpassword = array( ... );
	}

	class UsersController extends AppController 
	{
		function forgot() 
		{
			$this->User->set($this->data);
			$this->User->validationSet = 'forgotpassword';
			if ($this->User->validates()) 
			{
				// send email to reset password and show success message
			}
	   }
	}
	*/
	
	function validates( $options = array() ) 
	{
		// copy the data over from a custom var, otherwise
		
		$actionSet = 'validate' . Inflector::camelize(Router::getParam('action'));
		
		if (isset($this->validationSet)) 
		{
			$temp = $this->validate;
			$param = 'validate' . $this->validationSet;
			$this->validate = $this->{$param};
		} 
		elseif (isset($this->{$actionSet})) 
		{
			$temp = $this->validate;
			$param = $actionSet;
			$this->validate = $this->{$param};
		} 
    
		$errors = $this->invalidFields($options);

		// copy it back
		if (isset($temp)) 
		{
			$this->validate = $temp;
			unset($this->validationSet);
		}
		
		if (is_array($errors)) 
		{
			return count($errors) === 0;
		}
		return $errors;
	}
}
?>
