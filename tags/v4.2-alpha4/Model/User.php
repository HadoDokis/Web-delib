<?php
class User extends AppModel
{

    public $name = 'User';
    public $validate = array(
        'login' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrez le login.'
            ),
            array(
                'rule' => 'isUnique',
                'message' => 'Entrez un autre login, celui-ci est déjà utilisé.'
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
                'message' => 'Les mots de passe sont différents.'
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
                'message' => 'Entrez le prénom.'
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

    public $displayField = "nom";

    public $displayFields = array(
        'fields' => array('nom', 'prenom', 'login'),
        'format' => '%s %s (%s)');

    public $belongsTo = array(
        'Profil' => array(
            'className' => 'Profil',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'profil_id'));

    public $hasAndBelongsToMany = array(
        'Service' => array(
            'classname' => 'Service',
            'joinTable' => 'users_services',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'service_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => ''),
        'Circuit' => array(
            'className' => 'Cakeflow.Circuit',
            'joinTable' => 'circuits_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'circuit_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => '')
    );

    public $hasMany = array(
        'Historique' => array(
            'className' => 'Historique',
            'foreignKey' => 'user_id'),
        'Composition' => array(
            'className' => 'Cakeflow.Composition',
            'foreignKey' => 'trigger_id'
        )
    );

    function samePassword()
    {
        return (!empty($this->data['User']['password']) && $this->data['User']['password'] == $this->data['User']['password2']);
    }

    function validatesPassword($data)
    {
        return (!empty($data['User']['password']) && $data['User']['password'] == $data['User']['password2']);
    }

    function validOldPassword($data)
    {
        $oldPass = $this->find('first', array('conditions' => array('id' => $data['User']['id']), 'fields' => array('password'), 'recursive' => -1));
        return (md5($data['User']['oldpassword']) == $oldPass['User']['password']);
    }

    function emailDemande()
    {
        return !($this->data['User']['accept_notif'] && empty($this->data['User']['email']));
    }

    function beforeSave()
    {
        if (array_key_exists('password', $this->data['User']))
            $this->data['User']['password'] = md5($this->data['User']['password']);
        return true;
    }

    function beforeValidate()
    {
        if (empty($this->data['Service']['Service'])) {
            $this->invalidate('Service', true);
        }
    }

    /* Retourne le circuit par défaut défini pour l'utilisateur $id */
    /* Si l'utilisateur n'a pas de circuit par défaut, retourne le circuit défini */
    /* au niveau du premier service de l'utilisateur. */
    /* Si $field est vide alors retourne la structure de la classe circuit */
    /* Si $field est spécifiée, retourne la valeur du champ $field */
    function circuitDefaut($id = null, $field = '')
    {
        $circuitDefautId = 0;
        $user = $this->findById($id);
        // Circuit par défaut défini au niveau de l'utilisateur
        if (!empty($user['User']['circuit_defaut_id']))
            $circuitDefautId = $user['User']['circuit_defaut_id'];
        else {
            // Premier circuit par défaut défini pour les services de l'utilisateur
            foreach ($user['Service'] as $service) {
                if (!empty($service['circuit_defaut_id'])) {
                    $circuitDefautId = $service['circuit_defaut_id'];
                    break;
                }
            }
        }
        if ($circuitDefautId > 0) {
            $this->Circuit->recursive = -1;
            $circuit = $this->Composition->Etape->Circuit->find('first',
                array('conditions' => array('Circuit.id' => $circuitDefautId)));
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
    function prenomNomLogin($id)
    {
        $this->recursive = -1;
        $this->data = $this->read('prenom, nom, login', $id);
        if (empty($this->data))
            return '';
        else
            return $this->data['User']['prenom'] . ' ' . $this->data['User']['nom'] . ' (' . $this->data['User']['login'] . ')';
    }

    function makeBalise(&$oMainPart, $user_id)
    {
        $user = $this->find('first', array(
            'conditions' => array($this->alias . '.id' => $user_id),
            'recursive' => -1
        ));
        $oMainPart->addElement(new GDO_FieldType('prenom_redacteur', ($user[$this->alias]['prenom']), 'text'));
        $oMainPart->addElement(new GDO_FieldType('nom_redacteur', ($user[$this->alias]['nom']), 'text'));
        $oMainPart->addElement(new GDO_FieldType('email_redacteur', ($user[$this->alias]['email']), 'text'));
        $oMainPart->addElement(new GDO_FieldType('telmobile_redacteur', ($user[$this->alias]['telmobile']), 'text'));
        $oMainPart->addElement(new GDO_FieldType('telfixe_redacteur', ($user[$this->alias]['telfixe']), 'text'));
        $oMainPart->addElement(new GDO_FieldType('note_redacteur', ($user[$this->alias]['note']), 'text'));
    }

    function getCircuits($user_id)
    {
        $this->Behaviors->load('Containable');
        $circuits = array();
        $user = $this->find('first', array(
                'conditions' => array('User.id' => $user_id),
                'contain' => array('Circuit'))
        );
        foreach ($user['Circuit'] as $circuit) {
            if ($circuit['actif'])
                $circuits[$circuit['id']] = $circuit['nom'];
        }
        return $circuits;
    }

    /**
     * Envoi une notification par mail à un utilisateur sur l'état d'un dossier
     *
     * @param integer $delib_id identifiant du dossier
     * @param integer $user_id identifiant de l'utilisateur à notifier
     * @param string $type notification à envoyer
     * @return bool succès de l'envoi
     */
    function notifier($delib_id, $user_id, $type)
    {
        $user = $this->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $user_id)
        ));

        // utilisateur existe et accepte les mails ?
        if (empty($user)
            || empty($user['User']['accept_notif'])
            || empty($user['User']["mail_$type"])
        ) return false;

        App::uses('CakeEmail', 'Network/Email');
        $config_mail = Configure::read('SMTP_USE') ? 'smtp' : 'default';
        $this->Email = new CakeEmail($config_mail);
        $this->Email->to($user['User']['email']);

        App::uses('Deliberation', 'Model');
        $this->Deliberation = new Deliberation();
        $delib = $this->Deliberation->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $delib_id),
            'fields' => array('id', 'objet', 'titre', 'circuit_id')
        ));

        switch ($type) {
            case 'insertion':
                $subject = "Vous allez recevoir le projet : $delib_id";
                break;
            case 'traitement':
                $subject = "Vous avez le projet (id : $delib_id) à traiter";
                break;
            case 'refus':
                $subject = "Le projet << " . $delib['Deliberation']['objet'] . " >> a été refusé";
                break;
            case 'modif_projet_cree':
                $subject = "Votre projet (id : $delib_id) a été modifié";
                break;
            case 'modif_projet_valide':
                $subject = "Un projet que j'ai visé (id : $delib_id) a été modifié";
                break;
            case 'retard_validation':
                $subject = "Retard sur le projet : $delib_id";
                break;
        }
        $this->Email->subject($subject);
        $content = $this->_paramMails($type, $delib['Deliberation'], $user['User']);
        $this->Email->send($content);

        return true;
    }

    /**
     * Détermine le contenu du mail à envoyer en fonction du type de mail, le projet et l'utilisateur
     * @param string $type
     * @param array $delib
     * @param array $acteur
     * @return string
     */
    function _paramMails($type, $delib, $acteur)
    {
        $handle = fopen(CONFIG_PATH . "/emails/$type.txt", 'r');
        $content = fread($handle, filesize(CONFIG_PATH . "/emails/$type.txt"));

        $addrTraiter = FULL_BASE_URL . '/deliberations/traiter/' . $delib['id'];
        $addrView = FULL_BASE_URL . '/deliberations/view/' . $delib['id'];
        $addrEdit = FULL_BASE_URL . '/deliberations/edit/' . $delib['id'];

        $searchReplace = array(
            "#NOM#" => $acteur['nom'],
            "#PRENOM#" => $acteur['prenom'],
            "#IDENTIFIANT_PROJET#" => $delib['id'],
            "#OBJET_PROJET#" => $delib['objet'],
            "#TITRE_PROJET#" => $delib['titre'],
            "#LIBELLE_CIRCUIT#" => $this->Circuit->getLibelle($delib['circuit_id']),
            "#ADRESSE_A_TRAITER#" => $addrTraiter,
            "#ADRESSE_A_VISUALISER#" => $addrView,
            "#ADRESSE_A_MODIFIER#" => $addrEdit,
        );

        return str_replace(array_keys($searchReplace), array_values($searchReplace), $content);
    }
}