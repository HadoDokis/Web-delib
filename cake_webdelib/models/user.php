<?php
class User extends AppModel {

	var $name = 'User';
	var $validate = array(
		'login' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrez le login.'
			),
			array(
				'rule' => 'isUnique',
				'message' => 'Entrez un autre login, celui-ci est d�j� utilis�.'
			)
		),
		'password' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrez le mot de passe.'
			)
		),
		'password2' => array(
			array(
				'rule' => array('samePassword'),
				'message' => 'Les mots de passe sont diff�rents.'
			),
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrez le mot de passe de confirmation.'
			)
		),
		'nom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrez le nom.'
			)
		),
		'prenom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrez le pr�nom.'
			)
		),
		'email' => array(
			array(
				'rule' => 'emailDemande',
				'message' => 'Entrez l\'email.'
			),
			array(
				'rule' => 'email',
				'allowEmpty' => true,
				'message' => 'Adresse email non valide.'
			)
		),
		'profil_id' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Selectionner le profil utilisateur'
			)
		)
	);

	var $displayField = "nom";

var $displayFields = array(
	'fields' => array('prenom', 'nom', 'login'),
	'format' => '%s %s (%s)');

	var $belongsTo = array(
		'Profil'=>array(
			'className'  => 'Profil',
			'conditions' => '',
			'order'      => '',
			'dependent'  => false,
			'foreignKey' => 'profil_id'));

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
			'deleteQuery'=>'')
		);

         var $hasMany = array(
                'Historique' =>array(
                        'className'    => 'Historique',
                        'foreignKey'   => 'user_id'),
                'Composition' => array(
                        'className' => 'Cakeflow.Composition',
						'foreignKey'=>'trigger_id'
                )
                 );

	function samePassword() {
		return ((!empty($this->data['User']['password'])) && ($this->data['User']['password']==$this->data['User']['password2']));
	}

	function validatesPassword($data) {
		return ((!empty($data['User']['password'])) && ($data['User']['password']==$data['User']['password2']));
	}
	
	function validOldPassword($data) {
		$oldPass = $this->find('first',array('conditions'=>array('id'=>$data['User']['id']),'fields'=>array('password'),'recursive'=>-1));
		return (md5($data['User']['oldpassword'])==$oldPass['User']['password']);
	}

	function emailDemande() {
		return (!($this->data['User']['accept_notif'] && empty($this->data['User']['email'])));
	}

	function beforeSave() {
		if (array_key_exists('password', $this->data['User']))
			$this->data['User']['password'] = md5($this->data['User']['password']);
		return true;
	}
	
	function beforeValidate() {
		if (empty($this->data['Service']['Service'])) {
			$this->invalidate('Service', true);
		}
	}

	/* Retourne le circuit par d�faut d�fini pour l'utilisateur $id */
	/* Si l'utilisateur n'a pas de circuit par d�faut, retourne le circuit d�fini */
	/* au niveau du premier service de l'utilisateur. */
	/* Si $field est vide alors retourne la structure de la classe circuit */
	/* Si $field est sp�cifi�e, retourne la valeur du champ $field */
	function circuitDefaut($id = null, $field = '') {
		$circuitDefautId = 0;
		$user = $this->findById($id);
		// Circuit par d�faut d�fini au niveau de l'utilisateur
		if (!empty($user['User']['circuit_defaut_id']))
			$circuitDefautId = $user['User']['circuit_defaut_id'];
		else {
			// Premier circuit par d�faut d�fini pour les services de l'utilisateur
			foreach ($user['Service'] as $service) {
				if (!empty($service['circuit_defaut_id'])) {
					$circuitDefautId = $service['circuit_defaut_id'];
					break;
				}
			}
		}
		if ($circuitDefautId > 0) {
			$this->Circuit->recursive = -1;
			$circuit = $this->Composition->Etape->Circuit->findById($circuitDefautId);
			if (empty($field))
				return $circuit;
			else
				return $circuit['Circuit'][$field];
		} else
			return null;
	}

/*
 * retourne le prenom, nom et (login) de l'utilisateur $id
 *
 */
	function prenomNomLogin($id) {
		$this->recursive=-1;
		$this->data = $this->read('prenom, nom, login', $id);
		if (empty($this->data))
			return '';
		else
			return $this->data['User']['prenom'].' '.$this->data['User']['nom'].' ('.$this->data['User']['login'].')';
	}

        function makeBalise(&$oMainPart, $user_id) {
            $user = $this->find('first', 
                                array('conditions' => array($this->alias.'.id' => $user_id),
                                      'recursive'  => -1));
            $oMainPart->addElement(new GDO_FieldType('prenom_redacteur',    utf8_encode($user[$this->alias]['prenom']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('nom_redacteur',       utf8_encode($user[$this->alias]['nom']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('email_redacteur',     utf8_encode($user[$this->alias]['email']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('telmobile_redacteur', utf8_encode($user[$this->alias]['telmobile']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('telfixe_redacteur',   utf8_encode($user[$this->alias]['telfixe']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('note_redacteur',      utf8_encode($user[$this->alias]['note']), 'text'));
        }

}
?>
