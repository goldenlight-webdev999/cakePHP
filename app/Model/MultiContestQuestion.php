<?php
class MultiContestQuestion extends AppModel {
	var $name = 'MultiContestQuestion';
	
		var $hasMany = array(
			'MultiContestQuestionsOption' => array(
			'className' => 'MultiContestQuestionsOption',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => array("MultiContestQuestionsOption.id" => "asc")
			)
		);
}
?>