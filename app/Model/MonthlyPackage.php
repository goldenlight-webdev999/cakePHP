<?php



class MonthlyPackage extends AppModel {

	var $name = 'MonthlyPackage';
	
	/*var $belongsTo = array(
    'User' => array(
    'className' => 'User',
    'foreignKey' => 'user_id',
    'conditions' => '',
    'fields' => '',
    'order' => ''
    )
    );*/

        var $validate = array(
		'package_name' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'Enter a Package Name',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter Amount for this Package',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'text_messages_credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter Number of SMS Credits',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
		'voice_messages_credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter Number of Voice Credits',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
	);

}
