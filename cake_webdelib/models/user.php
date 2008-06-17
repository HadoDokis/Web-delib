<?php
class User extends AppModel {

	var $name = 'User';

	var $validate = array(
		'login' => VALID_NOT_EMPTY,
		'password' => VALID_NOT_EMPTY,
		'password2' => VALID_NOT_EMPTY,
		'nom' => VALID_NOT_EMPTY,
		'prenom' => VALID_NOT_EMPTY,
		'profil_id' => VALID_NOT_EMPTY
	);

	var $displayField = "nom";

	var $belongsTo = array(
		'Profil'=>array(
			'className'  => 'Profil',
			'conditions' => '',
			'order'      => '',
			'dependent'  => false,
			'foreignKey' => 'profil_id')
		 );

	var $hasAndBelongsToMany = array(
		'Service' => array(
			'classname'=>'Service',
			'joinTable'=>'users_services',
			'foreignKey'=>'user_id',
			'associationForeignKey'=>'service_id',
			'conditions'=>'',
			'order'=>'',
			'limit'=>'',
			'unique'=>true,
			'finderQuery'=>'',
			'deleteQuery'=>''),
		'Circuit' => array(
			'className' => 'Circuit',
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
		);

	function validates()
	{
		// unicité du login
		$this->isUnique('login', $this->data['User']['login'], $this->data['User']['id']);

		// mot de passe confirmé
		if (!empty($this->data['User']['password'])
			&& !empty($this->data['User']['password2'])
			&& ($this->data['User']['password']!=$this->data['User']['password2']))
			$this->invalidate('passwordDifferents');

		// adresse mail valide si présente
		if (!empty($this->data['User']['email'])
			&& !preg_match(VALID_EMAIL, $this->data['User']['email'] ) )
            $this->invalidate('email');

		// mail obligatoire si notification mail
		if ($this->data['User']['accept_notif'] && empty($this->data['User']['email']))
            $this->invalidate('emailDemande');

		// service obligatoire
		if (!array_key_exists('Service', $this->data))
            $this->invalidate('service');

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

	function beforeSave() {
		if (array_key_exists('password', $this->data['User']))
			$this->data['User']['password'] = md5($this->data['User']['password']);
		return true;
	}

}
?>