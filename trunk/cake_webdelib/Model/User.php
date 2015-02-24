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
            'foreignKey' => 'profil_id')
    );
    
    public $hasOne = array(
        'Aro' => array(
            'className' => 'Aro',
            'foreignKey' => false,
            'conditions' => array(
                'User.id = Aro.foreign_key',
                'Aro.model' => 'User'
            ),
            'dependent' => false
        )
    );

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
            'foreignKey' => 'user_id'
        ),
        'Composition' => array(
            'className' => 'Cakeflow.Composition',
            'foreignKey' => 'trigger_id'
        )
    );
    
    public $actsAs = array('Acl' => array('type' => 'requester'));

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

    function beforeSave($options=array())
    {
        if (array_key_exists('password', $this->data['User']))
            $this->data['User']['password'] = md5($this->data['User']['password']);
        return true;
    }

    function beforeValidate($options=array())
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
            $circuit = $this->Composition->Etape->Circuit->find('first', array('conditions' => array('Circuit.id' => $circuitDefautId)));

            if (empty($field) && !empty($circuit))
                return $circuit;
            elseif (!empty($circuit['Circuit'][$field]))
                return $circuit['Circuit'][$field];
            else
                return null;
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

    function getCircuits($user_id)
    {
        $circuits = array();
        $user = $this->find('first', array(
                'contain' => array(
                    'Circuit'=>array('fields' => array(
                                                        'Circuit.id',
                                                        'Circuit.nom',
                                                        'Circuit.actif'
                                                        ),
                                     'order'=>array('Circuit.nom'=>'ASC')       
                                    )
                    ),
                'conditions' => array('User.id' => $user_id),
                'recursive'=>-1)
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
        App::uses('Deliberation', 'Model');
        App::uses('Seance', 'Model');
        App::uses('CakeEmail', 'Network/Email');
        
        $user = $this->find('first', array(
            'field'=>array('nom','prenom','email','accept_notif','mail_' . $type),
            'conditions' => array('id' => $user_id),
            'recursive' => -1,
        ));

        // utilisateur existe et accepte les mails ?
        if (empty($user)
            || empty($user['User']['accept_notif'])
            || empty($user['User']['email']) 
            || empty($user['User']["mail_$type"])
        ) return false;

        $config_mail = Configure::read('SMTP_USE') ? 'smtp' : 'default';
        $Email = new CakeEmail($config_mail);

        $this->Deliberation = ClassRegistry::init('Deliberation');
        $delib = $this->Deliberation->find('first', array(
            'fields' => array('Deliberation.id', 'objet', 'titre', 'circuit_id'),
            'conditions' => array('Deliberation.id' => $delib_id),
            'contain'=>array(
                'Commentaire'=>array(
                    'fields' => array('texte'),
                    'conditions' => array('commentaire_auto' => false),
                    'order'=>'Commentaire.created DESC',
                    'limit'=> 1
                ),
                'Theme'=>array(
                    'fields' => array('libelle'),
                ),
             ),
            'recursive' => -1,
        ));
        
        $seance_id = $this->Deliberation->getSeanceDeliberanteId($delib_id);
        $libelle = '';
        if(!empty($seance_id)){
            $seance = $this->Deliberation->Seance->find('first', array(
                'fields' => array('Seance.id', 'date'),
                'conditions' => array('Seance.id' => $seance_id),
                'contain'=>array(
                    'Typeseance'=>array(
                        'fields' => array('libelle')
                    ),
                 ),
                'recursive' => -1,
            ));
            $libelle='['.$seance['Typeseance']['libelle'].'] ';
            $delib['Deliberation']['SeanceDeliberante']['texte']= $seance['Typeseance']['libelle'] . ' du ' . CakeTime::i18nFormat(strtotime($seance['Seance']['date']), '%A %e %B %Y à %k h %M');
        }

        switch ($type) {
            case 'insertion':
                $template = 'projet_insertion';
                $subject = $libelle . "Vous allez recevoir le projet : $delib_id";
                break;
            case 'traitement':
                $template = 'projet_traitement';
                $subject = $libelle . "Vous avez le projet (id : $delib_id) à traiter";
                break;
            case 'refus':
                $template = 'projet_refus';
                $subject = $libelle . "Le projet \"" . $delib['Deliberation']['objet'] . "\" a été refusé";
                break;
            case 'modif_projet_cree':
                $template = 'projet_modif_cree';
                $subject = $libelle . "Votre projet (id : $delib_id) a été modifié";
                break;
            case 'modif_projet_valide':
                $template = 'projet_modif_valide';
                $subject = $libelle . "Un projet que j'ai visé (id : $delib_id) a été modifié";
                break;
            case 'retard_validation':
                $template = 'projet_insertion';
                $subject = $libelle . "Retard sur le projet : $delib_id";
                break;
        }
        $aVariables = array(
            'nom' => $user['User']['nom'],
            'prenom' => $user['User']['prenom'],
            'projet_identifiant' => $delib['Deliberation']['id'],
            'projet_objet' => $delib['Deliberation']['objet'],
            'projet_theme' => $delib['Theme']['libelle'],
            'seance_deliberante' => !empty($delib['Deliberation']['SeanceDeliberante']['texte'])?$delib['Deliberation']['SeanceDeliberante']['texte']:'',
            'projet_dernier_commentaire' => !empty($delib['Commentaire'][0]['texte'])?$delib['Commentaire'][0]['texte']:'Aucun commentaire',
            'projet_titre' => $delib['Deliberation']['titre'],
           // 'LIBELLE_CIRCUIT' => $this->Circuit->getLibelle($delib['Deliberation']['circuit_id']),
            'projet_url_traiter' => Configure::read('WEBDELIB_URL') . '/deliberations/traiter/' . $delib['Deliberation']['id'],
            'projet_url_visualiser' => Configure::read('WEBDELIB_URL') . '/deliberations/view/' . $delib['Deliberation']['id'],
            'projet_url_modifier' => Configure::read('WEBDELIB_URL') . '/deliberations/edit/' . $delib['Deliberation']['id'],
        );
        
        $Email->viewVars($aVariables);
        
        return $Email->template($template, 'default')
            ->to($user['User']['email'])
            ->subject($subject)
            ->send();
    }

    /**
     * fonction d'initialisation des variables de fusion pour l'allias utilisé pour la liaison (Redacteur)
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id du modèle lié
     * @param string $suffixe suffixe des variables de fusion
     * @throws Exception
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id, $suffixe='') {
        // initialisations
        if (empty($suffixe))
            $suffixe = trim(strtolower($this->alias));
        $fields = array();
        $variables = array(
            'prenom',
            'nom',
            'email',
            'telmobile',
            'telfixe',
            'note'
        );

        // liste des variables présentes dans le modèle d'édition
        foreach ($variables as $variable)
            if ($modelOdtInfos->hasUserFieldDeclared($variable.'_'.$suffixe)) $fields[]= $variable;
        if (empty($fields)) return;

        // lecture en base de données
        $user = $this->find('first', array(
            'recursive' => -1,
            'fields' => $fields,
            'conditions' => array('id' => $id)));
        
        if (empty($user))
            throw new Exception('user '.$suffixe.' id:'.$id.' non trouvé en base de données');
        foreach($user[$this->alias] as $field => $val)
            $aData[$field.'_'.$suffixe]= $val;//, 'text'));
    }
    
    public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['profil_id'])) {
            $groupId = $this->data['User']['profil_id'];
        } else {
            $groupId = $this->field('profil_id');
        }
        if (!$groupId) {
            return null;
        }
        return array('Profil' => array('id' => $groupId));
    }
    
    public function parentNodeAlias() {
        if (!$this->id && empty($this->data)) {
        return null;
        }
        $data = $this->data;
        if (empty($this->data)) {
            $data = $this->read();
        }
        
        return array('User' => array('alias' => $data['User']['username']));
    }
}