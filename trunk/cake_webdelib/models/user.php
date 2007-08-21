<?php
class User extends AppModel {

	var $name = 'User';
	var $validate = array(
		'login' => VALID_NOT_EMPTY,
		'password' => VALID_NOT_EMPTY,
		'nom' => VALID_NOT_EMPTY,
		'prenom' => VALID_NOT_EMPTY,
	);

	var $recursive = 2;
	var $displayField="nom";
	var $belongsTo=array('Profil'=>array('className'=>'Profil', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'profil_id'));
	var $hasAndBelongsToMany = array('Service' => array('classname'=>'Service',
														'joinTable'=>'users_services',
														'foreignKey'=>'user_id',
														'associationForeignKey'=>'service_id',
														'conditions'=>'',
														'order'=>'',
														'limit'=>'',
														'unique'=>true,
														'finderQuery'=>'',
														'deleteQuery'=>'')
														,
									'Circuit' => array('className' => 'Circuit',
														'joinTable' => 'users_circuits',
														'foreignKey' => 'user_id',
														'associationForeignKey' => 'circuit_id',
														'conditions' => '',
														'fields' => '',
														'order' => '',
														'limit' => '',
														'offset' => '',
														'unique' => '',
														'finderQuery' => '',
														'deleteQuery' => '',
														'insertQuery' => ''),		
								  	
								'Listepresence'=> array('classname'=>'Listepresence',
														'joinTable'=>'users_listepresences',
														'foreignKey'=>'user_id',
														'associationForeignKey'=>'liste_id',
														'conditions'=>'',
														'order'=>'',
														'limit'=>'',
														'unique'=>true,
														'finderQuery'=>'',
														'deleteQuery'=>'')
										);

}
?>