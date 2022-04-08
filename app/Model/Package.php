<?php
class Package extends AppModel {
	var $name = 'Package';
	
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
		'name' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'Enter Package Name',
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
		'credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter Number of Credit',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
		'type' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'Select Package Type',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
	);
	

}
