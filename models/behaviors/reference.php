<?php
/**
 * Mongo behavior.
 *
 * Adds functionality specific to MongoDB dbs
 *
 */

/**
 * MongoBehavior class
 *
 * @uses          ModelBehavior
 */
class ReferenceBehavior extends ModelBehavior {

/**
 * name property
 *
 * @var string 'Schemaless'
 * @access public
 */
	public $name = 'Reference';

/**
 * settings property
 *
 * @var array
 * @access public
 */
	public $settings = array();

/**
 * defaultSettings property
 *
 * @var array
 * @access protected
 */
	protected $_defaultSettings = array(
	);

/**
 * setup method
 *
 * Don't currently have any settings at all - disabled
 *
 * @param mixed $Model
 * @param array $config array()
 * @return void
 * @access public
 */
	public function setup(&$Model, $config = array()) {
		//$this->settings[$Model->alias] = array_merge($this->_defaultSettings, $config);
	}

/**
 * afterDelete method
 *
 * Checks if the model has references and acts accordingly
 *
 * @param mixed $Model
 * @return void
 * @access public
 */
	public function afterDelete(&$Model) {
		if (isset($Model->isReference)) {
			$this->_deleteReference($Model);
		}
	}
	
/**
 * _deleteReference function
 * 
 * Pulls out all the references to the given Model from other Models
 * 
 * @param mixed $Model
 * @return void
 * @access private
 */
	private function _deleteReference(&$Model)
	{
		foreach ($Model->isReference as $assoc => $assocData) {
			$model =& ClassRegistry::init($assocData['className'], 'Model');
			if ($model) {
				$id = new MongoId($Model->id);
				$model->updateAll(array('$pull' => array($assocData['foreignKey'] => $id)), array($assocData['foreignKey'] => $id));
			}
		}
	}
	
/**
 * afterFind method
 *
 * Checks if the model has references and acts accordingly
 *
 * @param mixed $Model
 * @return void
 * @access public
 */
	public function afterFind(&$Model, $results, $primary) {
		if (isset($Model->hasReference) && $Model->recursive > -1) {
			return $this->_fetchReference($Model, $results, $primary);
		}
	}
	
/**
 * _deleteReference function
 * 
 * Pulls out all the references to the given Model from other Models
 * 
 * Currently only support recursive 0 or 1
 * 
 * @param mixed $Model
 * @return void
 * @access private
 */
	private function _fetchReference(&$Model, $results, $primary)
	{
		foreach ($Model->hasReference as $assoc => $assocData) {
			//check that the field is being requested in the Model
			$model =& ClassRegistry::init($assocData['className'], 'Model');
			$_recursive = $model->recursive;
			$model->recursive = -1;
			for ($i=0;$i<count($results);$i++) {
				if (isset($results[$i][$Model->alias][$assocData['referenceKey']])) {
					if (isset($assocData['multiple']) && $assocData['multiple']) {
						$references = $model->find('all', array('conditions' => array($assocData['foreignKey'] => array('$in' => $results[$i][$Model->alias][$assocData['referenceKey']]))));
					} else {
						$references = $model->find('first', array('conditions' => array($assocData['foreignKey'] => $results[$i][$Model->alias][$assocData['referenceKey']])));
					}
					if (!empty($references)) {
						if (isset($assocData['multiple']) && $assocData['multiple']) {
							foreach ($references as $reference) {
								$results[$i][$assoc][] = $reference[$model->alias];
							}
						} else {
							$results[$i][$assoc] = $references[$model->alias];
						}
					} else {
						$results[$i][$assoc] = array();
					}
				}
			}
			$model->recursive = $_recursive;
		}
		return $results;
	}
}


















