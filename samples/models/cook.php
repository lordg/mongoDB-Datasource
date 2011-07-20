<?php

class Cook extends AppModel {
	var $useDbConfig = 'mongo';
    var $name = 'Cook';   
	var $actsAs = array('Mongodb.Reference');
	
	var $mongoSchema = array(
		'name' => array('type' => 'string'),
		'created' => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime'),
	);
	
	var $isReference = array(
        'Recipe' => array(
	        'className' 	=> 'Recipe',
	        'foreignKey'	=> 'cook',
	    )
    );
}