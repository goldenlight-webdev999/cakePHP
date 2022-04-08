<?php

class Contest extends AppModel {

	var $name = 'Contest';
	
	/* var $belongsTo =array(
	  'Group' =>array(
	  'className' => 'Group',
	  'foreignKey' => 'user_id',
	  'conditions' => '',
	  'fields' => '',
	  'order' => ''
	  )
	  ); */
	  
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

