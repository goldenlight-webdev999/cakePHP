<?php

class ActivityTimeline extends AppModel {

	var $name = 'ActivityTimeline';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

var $belongsTo = array(
   'Contact' => array(
   'className' => 'Contact',
   'foreignKey' => 'contact_id',
   'conditions' => '',
   'fields' => '',
   'order' => ''
  )
 );


}