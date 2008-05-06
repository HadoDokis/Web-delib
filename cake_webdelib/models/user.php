<?php
class User extends AppModel {

	var $name = 'User';
	var $validate = array(
		'login' => VALID_NOT_EMPTY,
		'password' => VALID_NOT_EMPTY,
		'password2' => VALID_NOT_EMPTY,
		'nom' => VALID_NOT_EMPTY,
		'prenom' => VALID_NOT_EMPTY,
		//'email' => VALID_EMAIL,
	);
	var $displayField="nom";
	var $belongsTo = array(	'Profil'=>array('className'=>'Profil',
											'conditions'=>'',
											'order'=>'',
											'dependent'=>false,
											'foreignKey'=>'profil_id'),

							'ServiceElu' =>array('className' => 'Service',
                                 'conditions' => '',
                                 'order'      => '',
                                 'foreignKey' => ''),

						 );
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
														'insertQuery' => '')
														,
									'Seance' => array ('className' => 'Seance',
															'joinTable' => 'seances_users',
															'foreignKey' => 'seance_id',
															'associationForeignKey' => 'user_id',
															'conditions' => '',
															'fields' => '',
															'order' => '',
															'limit' => '',
															'offset' => '',
															'unique' => '',
															'finderQuery' => '',
															'deleteQuery' => '',
															'insertQuery' => '')/*,

								'Listepresence'=> array('classname'=>'Listepresence',
														'joinTable'=>'users_listepresences',
														'foreignKey'=>'user_id',
														'associationForeignKey'=>'liste_id',
														'conditions'=>'',
														'order'=>'',
														'limit'=>'',
														'unique'=>true,
														'finderQuery'=>'',
														'deleteQuery'=>'')*/
										);

	 function validates()
    {
        $user = $this->data["User"];
        if (!isset($user["password2"])) {
            $errors = $this->invalidFields();
            return count($errors) == 0;
        }

        if($user["password"] != md5($user["password2"]) ){
            $this->invalidate('password2');
         }

          $errors = $this->invalidFields();
          return count($errors) == 0;
    }

}
?>