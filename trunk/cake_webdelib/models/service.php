<?php
class Service extends AppModel {

	var $name = 'Service';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);
//	var $hasAndBelongsToMany=array('Agent'=>array('classname'=>'Agent',
//													'joinTable'=>'agents_services',
//													'foreignKey'=>'service_id',
//													'associationForeignKey'=>'agent_id',
//													'conditions'=>'',
//													'order'=>'',
//													'limit'=>'',
//													'unique'=>true,
//													'finderQuery'=>'',
//													'deleteQuery'=>''));

}
?>