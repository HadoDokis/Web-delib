<?php
class Deliberation extends AppModel {

	var $name = 'Deliberation';
	//dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
	var $belongsTo=array('Service'=>array('className'=>'Service', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'service_id'),
						'Theme'=>array('className'=>'Theme', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'theme_id'),
						'Circuit'=>array('className'=>'Circuit', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'circuit_id'),
						'Agent'=>array('className'=>'Agent', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'agent_id'));
											
//	var $hasAndBelongsToMany=array('Agent'=>array('classname'=>'Agent',
//													'joinTable'=>'agents_profils',
//													'foreignKey'=>'agent_id',
//													'associationForeignKey'=>'profil_id',
//													'conditions'=>'',
//													'order'=>'',
//													'limit'=>'',
//													'unique'=>true,
//													'finderQuery'=>'',
//													'deleteQuery'=>''));
	
	

}
?>