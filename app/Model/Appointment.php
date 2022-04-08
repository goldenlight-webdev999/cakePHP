<?php
	class Appointment extends AppModel {
		var $name = 'Appointment';
		var $belongsTo = array(
			'Contact' => array(
				'className' => 'Contact',
				'foreignKey' => 'contact_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}?>
