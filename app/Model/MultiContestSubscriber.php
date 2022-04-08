<?php
class MultiContestSubscriber  extends AppModel {
	var $name = 'MultiContestSubscriber';
	var $belongsTo = array(
			'MultiContest' => array(
				'className' => 'MultiContest',
				'foreignKey' => 'multi_contest_id',
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => ''
			),
			'MultiContestQuestion' => array(
				'className' => 'MultiContestQuestion',
				'foreignKey' => 'multi_contest_question_id',
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => ''
			),
			'Contact' => array(
				'className' => 'Contact',
				'foreignKey' => 'contact_id',
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => ''
			)
		);
}
?>