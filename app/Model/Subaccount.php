<?php
	class Subaccount extends AppModel {
		var $name = 'Subaccount';
		
		var $validate = array(

		'first_name' => array(

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Please enter a first name',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),
		
		'last_name' => array(

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Please enter a last name',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),

		'username' => array(

			'isUnique' => array(

				'rule' => array('isUnique'),

				'message' => 'This username has already been taken',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Please enter a username',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),
		
	    'email' => array(

			'isUnique' => array(

				'rule' => array('isUnique'),

				'message' => 'This Email has already been taken',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

			'email' => array(

				'rule' => array('email'),

				'message' => 'Enter a valid Email Address',

			),

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Enter Email Address',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),

	
		'password' => array(

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Please enter a password',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),

		'confirm_password' => array(

			'confirm_password' => array(

				'rule' => array('confirm_password'),

				'message' => 'Password and confirm password are not the same',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Please enter the password again',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),

		
	);
	
	var $validateUserlogin = array(

		'usrname' => array(

				'notblank' => array(

					'rule' => array('notblank'),

					'message' => 'Enter username',

					//'allowEmpty' => false,

					//'required' => false,

					//'last' => false, // Stop validation after this rule

					//'on' => 'create', // Limit validation to 'create' or 'update' operations

				),

			),


		

		'passwrd' => array(

			'notblank' => array(

				'rule' => array('notblank'),

				'message' => 'Enter password',

				//'allowEmpty' => false,

				//'required' => false,

				//'last' => false, // Stop validation after this rule

				//'on' => 'create', // Limit validation to 'create' or 'update' operations

			),

		),

	

	);
	
	function confirm_password()

	{ 

	    if(trim($this->data['Subaccount']['password'])== trim($this->data['Subaccount']['confirm_password']))
			return true;
		else
			return false;

    }
	
}?>