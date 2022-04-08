<?php
class MultiContestSubscriberArchive  extends AppModel {
	var $name = 'MultiContestSubscriberArchive';
	var $belongsTo = array(
			'MultiContest' => array(
				'className' => 'MultiContest',
				'foreignKey' => 'multi_contest_id',
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