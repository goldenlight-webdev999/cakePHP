<?php
class MultiContest  extends AppModel {
	var $name = 'MultiContest';
	var $hasMany = array(
			'MultiContestQuestion' => array(
			'className' => 'MultiContestQuestion',
			'foreignKey' => 'multi_contest_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	 var $belongsTo = array(
        'Group' => array(
        'className' => 'Group',
        'foreignKey' => 'group_id',
        'conditions' => '',
        'fields' => '',
        'order' => ''
        )
    );
}
?>