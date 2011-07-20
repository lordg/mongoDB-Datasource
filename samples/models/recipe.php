<?php

class Recipe extends AppModel {
	var $useDbConfig = 'mongo';
    var $name = 'Recipe'; 
	var $actsAs = array('Mongodb.Reference');
	
	var $mongoSchema = array(
		'name' => array('type' => 'string'),
		'description' => array('type' => 'string'),
		'ingredients' => array('type' => 'array'),
		'cook' => array('type' => 'string', 'length' => 24),
		'created' => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime'),
	);
	
    var $hasReference = array(
        'Ingredient' => array(
	        'className' 	=> 'Ingredient',
	        'referenceKey'	=> 'ingredients',
	        'foreignKey'	=> '_id',
	        'multiple'		=> true
	    ),
        'Cook' => array(
	        'className' 	=> 'Cook',
	        'referenceKey'	=> 'cook',
	        'foreignKey'	=> '_id',
	    )
    );
}
