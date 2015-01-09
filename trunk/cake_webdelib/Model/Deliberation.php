<?php
/**
 * Class Deliberation
 * @property Deliberation $Deliberation
 */
class Deliberation extends AppModel {
    public $validate = array(
        'objet' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'L\'objet est obligatoire'
            )
        ),
        'typeacte_id' => array(
            array(
                'rule' => array('canSaveNature', 'notEmpty'),
                'message' => "Type d'acte invalide"
            )
        ),
        'theme_id' => array(
            array(
                'rule' => array('notEmpty'),
                'message' => "Theme invalide"
            )
        ),
        'texte_projet_upload' => array(
            array(
                'rule' => array('checkFormat', 'odt', false),
                'message' => "Format du document invalide. Autorisé : fichier ODT"
            )
        ),
        'texte_synthese_upload' => array(
            array(
                'rule' => array('checkFormat', 'odt', false),
                'message' => "Format du document invalide. Autorisé : fichier ODT"
            )
        ),
        'deliberation_upload' => array(
            array(
                'rule' => array('checkFormat', 'odt', false),
                'message' => "Format du document invalide. Autorisé : fichier ODT"
            )
        ),
        'texte_doc' => array(
            array('rule' => array('checkFormat', 'odt', false),
                'message' => "Format du document invalide. Autorisé : fichier ODT"
            )
        ),
        'vote_commentaire' => array(
            'rule' => array('maxLength', 1000),
            'message' => 'Le commentaire de vote ne doit pas dépasser 1000 caractères.'
        )
    );


    //dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
    public $belongsTo = array(
        'Service' => array(
            'className' => 'Service',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'service_id'
        ),
        'Theme' => array(
            'className' => 'Theme',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'theme_id'
        ),
        'Circuit' => array(
            'className' => 'Cakeflow.Circuit',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'circuit_id'
        ),
        'Redacteur' => array(
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'redacteur_id'
        ),
        'Rapporteur' => array(
            'className' => 'Acteur',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'rapporteur_id'
        ),
        'President' => array(
            'className' => 'Acteur',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'president_id'),
        'Typeacte' => array(
            'className' => 'Typeacte',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'typeacte_id'
        )
    );

    public $hasMany = array(
        'TdtMessage' => array(
            'className' => 'TdtMessage',
            'foreignKey' => 'delib_id'
        ),
        'Historique' => array(
            'className' => 'Historique',
            'foreignKey' => 'delib_id'
        ),
        'Traitement' => array(
            'className' => 'Cakeflow.Traitement',
            'foreignKey' => 'target_id'
        ),
        'Annex' => array('className' => 'Annex',
            'foreignKey' => 'foreign_key',
            'order' => array('Annex.id' => 'ASC'),
            'dependent' => true
        ),
        'Commentaire' => array(
            'className' => 'Commentaire',
            'foreignKey' => 'delib_id'
        ),
        'Listepresence' => array(
            'className' => 'Listepresence',
            'foreignKey' => 'delib_id'
        ),
        'Vote' => array(
            'className' => 'Vote',
            'foreignKey' => 'delib_id'
        ),
        'Infosup' => array(
            'dependent' => true,
            'foreignKey' => 'foreign_key',
            'conditions' => array('Infosup.model' => 'Deliberation')
        ),
        'Multidelib' => array(
            'className' => 'Deliberation',
            'foreignKey' => 'parent_id',
            'order' => 'id ASC',
            'dependent' => false),
        'Deliberationseance' => array(
            'className' => 'Deliberationseance',
            'foreignKey' => 'deliberation_id',
            'order' => 'Deliberationseance.position ASC'
        ),
        'Deliberationtypeseance' => array(
            'className' => 'Deliberationtypeseance',
            'foreignKey' => 'deliberation_id'
        )
    );

    public $hasAndBelongsToMany = array(
            'Seance' => array( 'className' => 'Seance',
			'joinTable' => 'deliberations_seances',
			'foreignKey' => 'deliberation_id',
			'associationForeignKey' => 'seance_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => 'Seance.date ASC',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''),
            'Typeseance',
            
	);

    /**
     * Si vous avez besoin d’exécuter de la logique juste après chaque opération de sauvegarde,
     * placez-la dans cette méthode de rappel. Les données sauvegardées seront disponibles dans $this->data
     * @param bool $created La valeur de $created sera true si un nouvel objet a été créé (plutôt qu’un objet mis à jour)
     * @param array $options Le tableau $options est le même que celui passé dans Model::save()
     */
    public function afterSave($created, $options = array()) {
        if (!empty($this->data['Deliberation']['id'])) {
            $hasChildren = $this->hasAny(array('Deliberation.parent_id' => $this->data['Deliberation']['id']));
            //Si la delib a des enfants
            if ($hasChildren) {
                //Recopie des attributs etat (si < ou = à 2) et circuit_id
                if (array_key_exists('etat', $this->data['Deliberation']) && $this->data['Deliberation']['etat'] <= 2) {
                    $this->updateAll(
                        array('Deliberation.etat' => $this->data['Deliberation']['etat']),
                        array('Deliberation.parent_id' => $this->data['Deliberation']['id'])
                    );
                    $this->log('changement d\'état délibs enfants', 'debug');
                }
                if (array_key_exists('circuit_id', $this->data['Deliberation'])) {
                    $this->updateAll(
                        array('Deliberation.circuit_id' => $this->data['Deliberation']['circuit_id']),
                        array('Deliberation.parent_id' => $this->data['Deliberation']['id'])
                    );
                }
            }
        }
    }

    /*
     * Indique si le projet de délibération $delibId est modifiable pour $userId.
    * Attention : ne tient pas compte des droits qui sont fait dans le controller
    * En fonction de l'état du projet on a :
    * - le projet est refusé (etat = -1) : non modifiable
    * - le projet est en cours de rédaction (etat = 0) :
    *   + l'utilisateur connecté est le rédacteur du projet : modifiable
    *   + l'utilisateur connecté n'est pas le rédacteur du projet : non modifiable
    *  - le projet est en cours de validation (etat = 1) :
    *    + l'utilisateur connecté n'est pas dans le circuit de validation : non modifiable
    *    + l'utilisateur connecté est dans le circuit de validation :
    *      * il a déja validé le projet : non modifiable
    *      * c'est à son tour de traiter le projet : modifiable
    *      * son tour n'est pas encore passé : modifiable
    *  - le projet est validé (etat = 2) : non modifiable
    *  - le projet a été voté (etat = 3 ou 4) : non modifiable
    *  - le projet a été envoyé (etat = 5) : non modifiable
    *  - le projet a recu un avis (avis = 1 ou 2) : non modifiable
    */
    function estModifiable($delibId, $userId, $canEdit = false) {
        /* lecture en base */
        $delib = $this->find('first', array(
            'conditions' => array(
                'Deliberation.id' => $delibId,
                'Deliberation.signee' => false
            ),
            'recursive' => '-1',
            'fields' => array('etat', 'redacteur_id', 'signee')));

        if (!empty($delib))
            switch ($delib['Deliberation']['etat']) { /* traitement en fonction de l'état */
                case 0 :
                    return ($canEdit || $delib['Deliberation']['redacteur_id'] == $userId);
                case 1 :
                    return ($canEdit || $this->Traitement->positionTrigger($userId, $delibId) == 0);
                case 2 :
                    return $canEdit;
                case 3 :
                    return $canEdit;
                case 4 :
                    return $canEdit;
                default :
                    return false;
            }

        return false;
    }

    /**
	 * retourne le libellé correspondant à l'état $etat des projets et délibérations
	 * si $codesSpeciaux = true, retourne les libellés avec les codes spéciaux des accents
	 * si $codesSpeciaux = false, retourne les libellés sans les accents (listes)
     * @param  $etat
     * @param  bool $codesSpeciaux
     * @return string
     */
    function libelleEtat($etat, $codesSpeciaux = false) {
		switch($etat) {
			case -1 :
				return $codesSpeciaux ? 'Versionn&eacute;' : 'Versionné';
				break;
			case -1 :
				return $codesSpeciaux ? 'Refus&eacute;' : 'Refusé';
				break;
			case 0 :
				return $codesSpeciaux ? 'En cours de r&eacute;daction' : 'En cours de rédaction';
				break;
			case 1:
				return $codesSpeciaux ? 'En cours d\'&eacute;laboration et de validation' : 'En cours d\'élaboration et de validation';
				break;
			case 2:
				return $codesSpeciaux ? 'Valid&eacute;' : 'Validé';
				break;
			case 3:
				return $codesSpeciaux ? 'Vot&eacute; et adopt&eacute;' : 'Voté et adopté';
				break;
			case 4:
				return $codesSpeciaux ? 'Vot&eacute; et non adopt&eacute;' : 'Voté et non adopté';
				break;
			case 5:
				return $codesSpeciaux ? 'Transmis au contr&ocirc;le de l&eacute;galit&eacute;' : 'Transmis au contrôle de légalité';
				break;
            default:
                return $codesSpeciaux ? 'Code Erron&eacute;' : 'Code Erroné';
		}
	}

	function generateListEtat() {
		$ret = array();
		for($i=-1; $i <= 5; $i++) $ret[$i] = $this->libelleEtat($i, false);
		return $ret;
	}

	function getCurrentPosition($id){
		$delib = $this->find('first', array('conditions' => array("Deliberation.id" => $id),
				'fields' => array('Deliberation.position'),
				'recursive' =>  -1));
		return  $delib['Deliberation']['position'];
	}

	function getCurrentSeances($id, $retourDetaillee) {
		$this->Behaviors->attach('Containable');
		$delib = $this->find('first', array('conditions' => array("Deliberation.id" => $id),
		                     'fields'     => 'Deliberation.id',
			             'contain'    => 'Seance'));
		if ( $retourDetaillee)
			return $delib['Seance'];
		else {
			$seances = array();
			foreach($delib['Seance'] as $seance)
				$seances[] =  $seance['id'];
			return $seances;
		}
	}

	function isFirstDelib($delib_id, $seance_id) {
		$position = $this->getPosition($delib_id, $seance_id);
		return ($position == 1);
	}

	function changeDateAR($delib_id, $dateAR){
		$this->id = $delib_id;
		$this->saveField('tdt_dateAR', $dateAR);
	}

    /**
     * Retourne l'identifiant du modèle à utiliser selon le projet
     * @param string $delib_id identifiant du projet
     * @return integer identifiant du modèle
     */
    function getModelId($delib_id){
// initialisations
        $etat = $this->field('etat', array('id' => $delib_id));
        $seanceDeliberanteId = $this->getSeanceDeliberanteId($delib_id);
        if (!empty($seanceDeliberanteId)){
            // lecture des modèles de la séance délibérante
            $seance = $this->Seance->find('first', array(
                'fields' => array('type_id'),
                'contain' => array('Typeseance.modelprojet_id','Typeseance.modeldeliberation_id'),
                'conditions' => array('Seance.id' => $seanceDeliberanteId)));
            if ($etat < 3)
                return $seance['Typeseance']['modelprojet_id'];
            else
                return $seance['Typeseance']['modeldeliberation_id'];
        } else {
            $typeacte_id = $this->field('typeacte_id', array('id' => $delib_id));
            $typeacte = $this->Typeacte->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('id' => $typeacte_id),
                            'fields' => array('modeleprojet_id','modelefinal_id')));
            if ($etat < 3)
                return $typeacte['Typeacte']['modeleprojet_id'];
            else
                return $typeacte['Typeacte']['modelefinal_id'];
        }
    }

	function getModelForSeance($delib_id, $seance_id) {
		$this->Seance->Behaviors->attach('Containable');
		$delib = $this->find('first', array(
            'conditions' => array('Deliberation.id' => $delib_id),
				'recursive'  => -1,
				'fields'     => array('Deliberation.id', 'Deliberation.etat', 'Deliberation.typeacte_id')
        ));
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields'     => array('Seance.id'),
            'contain'    => array('Typeseance.modelprojet_id', 'Typeseance.modeldeliberation_id') ));

        if (!empty($seance)){
            if ($delib['Deliberation']['etat']<3)
                return $seance['Typeseance']['modelprojet_id'];
            else
                return $seance['Typeseance']['modeldeliberation_id'];
        }
		else { // Pas de séance associée, chercher le model du type d'acte
            if ($delib['Deliberation']['etat']<3)
                return $this->Typeacte->getModelId($delib['Deliberation']['typeacte_id'], 'modeleprojet_id');
            else
                return $this->Typeacte->getModelId($delib['Deliberation']['typeacte_id'], 'modelefinal_id');
		}
	}

    public function refusDossier($id) {
        // lecture en base de données
        $this->Behaviors->load('Containable');
        $delib = $this->find('first', array(
            'contain' => array('Annex', 'Infosup', 'Typeseance'),
            'conditions' => array('Deliberation.id' => $id)));

        // maj de l'etat de la delib dans la table deliberations
        $this->id = $id;
        $this->saveField('etat', '-1');

        // création de la nouvelle version
        unset($delib['Deliberation']['id']);
        unset($delib['Deliberation']['created']);
        unset($delib['Deliberation']['modified']);
        $delib['Deliberation']['etat'] = 0;
        $delib['Deliberation']['anterieure_id'] = $id;
        $this->create();
        $this->save($delib);
        $delib_id = $this->getLastInsertID();
        $this->copyPositionsDelibs($id, $delib_id);

        // copie des annexes du projet refusé vers le nouveau projet
        $annexes = $delib['Annex'];
        foreach ($annexes as $annexe) {
            $tmp['Annex'] = $annexe;
            $tmp['Annex']['id'] = null;
            $tmp['Annex']['foreign_key'] = $delib_id;
            $this->Annex->save($tmp, false);
        }

        // copie des infos supplémentaires du projet refusé vers le nouveau projet
        $infoSups = $delib['Infosup'];
        foreach ($infoSups as $infoSup) {
            $infoSup['id'] = null;
            $infoSup['foreign_key'] = $delib_id;
            $infoSup['model'] = 'Deliberation';
            $this->Infosup->save($infoSup, false);
        }

        // copie des délibérations rattachées vers le nouveau projet
        $delibRattachees = $this->find('all', array(
            'contain' => array('Annex'),
            'conditions' => array('Deliberation.parent_id' => $id)));

        foreach ($delibRattachees as $delibRattachee) {
            // création de la nouvelle version
            $this->create();
            $anterieure_id = $delibRattachee['Deliberation']['id'];
            unset($delibRattachee['Deliberation']['id']);
            $delibRattachee['Deliberation']['parent_id'] = $delib_id;
            $delibRattachee['Deliberation']['etat'] = 0;
            $delibRattachee['Deliberation']['anterieure_id'] = $anterieure_id;
            unset($delibRattachee['Deliberation']['date_envoi']);
            $delibRattachee['Deliberation']['created'] = date('Y-m-d H:i:s', time());
            $delibRattachee['Deliberation']['modified'] = date('Y-m-d H:i:s', time());
            $this->save($delibRattachee);
            $delibRattachee_id = $this->getLastInsertID();

            // copie des annexes du projet refusé vers le nouveau projet
            foreach ($delibRattachee['Annex'] as $annexe) {
                unset($annexe['id']);
                $tmp['Annex'] = $annexe;
                $tmp['Annex']['foreign_key'] = $delibRattachee_id;
                $this->Annex->save($tmp, false);
            }
        }
        return $delib_id;
    }

    function canSaveSeances($seances_id){
            $result = false;
            $nb_seances_deliberante = 0;
            $this->Seance->Behaviors->attach('Containable');
            //$seances_id = $this->data['Seance']['Seance'];
            if (isset ($seances_id)) {
                $seances = $this->Seance->find('all', array('conditions' => array('Seance.id' => $seances_id),
		                                            'fields'     => array('Seance.id'),
                                                            'contain'    => array('Typeseance.action')));
		foreach($seances as $seance) {
                    if($seance['Typeseance']['action'] == 0)
                       $nb_seances_deliberante ++;
		}
		if ($nb_seances_deliberante > 1)
		    return false;
		else
		    return true;
            } 
            else 
                return true;
	}

	function canSaveNature() {
            if ($this->data['Deliberation']['typeacte_id']=='')
                return false;

	    if (isset($this->data['Deliberation']['seance_id']) && (!empty($this->data['Deliberation']['seance_id']))) {
                foreach ($this->data['Deliberation']['seance_id'] as $key => $seance_id) {
                    $result = $this->Seance->NaturecanSave($seance_id, $this->data['Deliberation']['typeacte_id']);
	            if ($result == false)
	                return false;
		    }
		    return true;
	    }
	    else
                return true;
	}

	/**
	 * opérations post sauvegarde des délibérations :
	 * - gestion des séances et de l'ordre des projets
	 * - mise à jour des délibérations rattachées
	 * @param integer $delibId id du projet à traiter
	 */
	function majDelibRatt($delibId) {
		// initialisation
		$position = 0;
		$majPosition = false;
		$majFields = array(
				'typeacte_id', 'theme_id', 'service_id', 'redacteur_id', 'rapporteur_id',
				'titre', 'num_pref', 'etat',
				'texte_projet', 'texte_projet_size', 'texte_projet_type', 'texte_projet_name',
				'texte_synthese', 'texte_synthese_size', 'texte_synthese_type', 'texte_synthese_name',
				'date_limite');

		// lecture en base
		$this->Behaviors->attach('Containable');
		$delib = $this->find('first', array(
				'fields' => $majFields,
				'contain' => array('Multidelib.id'),
				'conditions' => array('Deliberation.id' => $delibId)));


		if (empty($delib['Multidelib'])) return;

		// faut-il mettre a jour la position dans la séance
              //  if (isset($this->data['Deliberation']['seance_id']) && !empty($delib['Deliberation']['seance_id']))
              //      $this->Seance->reOrdonne($id, $this->data['Deliberation']['seance_id']);

		// mise à jour des délibérations rattachées
		$majDelibRatt = array();
		foreach ($majFields as $fieldName)
			$majDelibRatt['Deliberation'][$fieldName] = $delib['Deliberation'][$fieldName];
		foreach ($delib['Multidelib'] as $delibRattachee) {
			$majDelibRatt['Deliberation']['id'] = $delibRattachee['id'];
			if ($majPosition || (!empty($delib['Deliberation']['seance_id']) && empty($delibRattachee['position']))) {
				if ($position > 0) $position++;
				$majDelibRatt['Deliberation']['position'] = $position;
			} else
				unset($majDelibRatt['Deliberation']['position']);

			$this->save($majDelibRatt['Deliberation'], array('validate' => false, 'callbacks' => false));
		}
	}

	/**
	 * reordonne les positions de la séance $seanceId
	 */
	function reOrdonnePositionSeance($seanceId) {
		// initialisations
		$position = 0;
		// lecture des delibs de la séance
		$delibs = $this->find('all', array(
				'recursive' => -1,
				'fields' => array('id', 'position'),
				'conditions' => array(
						'etat <>' => -1,
						'seance_id' => $seanceId),
				'order' => 'position ASC'));
		// pour toutes les délibs
		foreach($delibs as $delib) {
			$position++;
			if ($position != $delib['Deliberation']['position'])
				$this->save(array('id'=>$delib['Deliberation']['id'], 'position'=>$position), array('validate' => false, 'callbacks' => false));
		}
	}

    /**
     * sauvegarde des délibérations attachées
     * @param integer $parentId id de la délibération principale
     * @param array $delib délibération rattachée retourné par le formulaire 'edit'
     * @return bool
     */
    function saveDelibRattachees($parentId, $delib)
    {
        $this->begin();
        // initialisations
        $newDelib = array();

        if (isset($delib['id'])) {
            // modification
            $this->id = $delib['id'];
            $newDelib['Deliberation']['id'] = $delib['id'];
        } else {
            // ajout
            $newDelib = $this->create();
            $newDelib['Deliberation']['parent_id'] = $parentId;
        }
        if (!empty($delib['num_pref']))
        $newDelib['Deliberation']['num_pref'] = $delib['num_pref'];
        $newDelib['Deliberation']['objet'] = $delib['objet_delib'];
        $newDelib['Deliberation']['objet_delib'] = $delib['objet_delib'];
        $newDelib['Deliberation']['titre'] = !empty($delib['titre']) ? $delib['titre'] : null;

        if (isset($delib['deliberation'])) {
            $newDelib['Deliberation']['deliberation_name'] = $delib['deliberation']['name'];
            $newDelib['Deliberation']['deliberation_type'] = $delib['deliberation']['type'];
            $newDelib['Deliberation']['deliberation_size'] = $delib['deliberation']['size'];
            if (empty($delib['deliberation']['tmp_name']))
                $newDelib['Deliberation']['deliberation'] = '';
            else
                $newDelib['Deliberation']['deliberation'] = file_get_contents($delib['deliberation']['tmp_name']);
        } elseif (isset($delib['id'])) {
            $path_projet = APP . 'webroot/files/generee/projet/' . $delib['id'] . '/';
            if (file_exists($path_projet . 'deliberation.odt'))
                $newDelib['Deliberation']['deliberation'] = file_get_contents($path_projet . 'deliberation.odt');
        } elseif (!empty($delib['gabarit'])){
            $typeacte = $this->Typeacte->find('first', array(
                'recursive' => -1,
                'fields' => array('gabarit_acte', 'gabarit_acte_name'),
                'conditions' => array('id' => $delib['typeacte_id'])
            ));
            $newDelib['Deliberation']['deliberation_name'] = $typeacte['Typeacte']['gabarit_acte_name'];
            //Calcul mimetype
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $newDelib['Deliberation']['deliberation_type'] = $finfo->buffer($typeacte['Typeacte']['gabarit_acte']);
            $newDelib['Deliberation']['deliberation_size'] = strlen($typeacte['Typeacte']['gabarit_acte']);
            $newDelib['Deliberation']['deliberation'] = $typeacte['Typeacte']['gabarit_acte'];

        }

        if (!$this->save($newDelib['Deliberation'], false)) {
            $this->Session->setFlash('Erreur lors de la sauvegarde des délibérations rattachées.', 'growl', array('type' => 'erreur'));
            return false;
        }
        $tabs = array();
        $seances = $this->Deliberationseance->find('all', array(
            'conditions' => array('Deliberationseance.deliberation_id' => $parentId),
            'recursive' => -1));
        foreach ($seances as $seance)
            $tabs[] = $seance['Deliberationseance']['seance_id'];
        $this->Seance->reOrdonne($this->id, $tabs);

        $typeseances = $this->Deliberationtypeseance->find('all', array(
            'conditions' => array('deliberation_id' => $parentId),
            'recursive' => -1
        ));
        foreach ($typeseances as $typeseance){
            unset($typeseance['Deliberationtypeseance']['id']);
            $typeseance['Deliberationtypeseance']['deliberation_id'] = $this->id;
            $this->Deliberationtypeseance->create();
            $this->Deliberationtypeseance->save($typeseance);
        }
        $this->commit();
        return $this->id;
    }
    
        /*
     * 
     */
    public function SaveDebat($data)
    {
        $this->validate=array();
        $this->validator()->add('texte_doc', array(
            'required' => array(
                'rule' => 'isUploadedFile',
                'message' => 'Veuillez mettre un fichier pour enregistrer la saisie des débats généraux'
            ),
            'upload' => array(
                'rule' => 'uploadError',
                'message' => 'Une erreur est survenue lors de l\'upload du document'
            ),
            'checkFormat' => array(
                'rule' => array('checkFormat', 'odt', true),
                'message'       => 'Format du document invalide. Autorisé : fichier ODT'
            )
        ));
        
        
        return parent::save($data);
    }
    
    /**
	 * fonction récursive de suppression de la délibération $delib8d, de ses versions antérieures et de ses délibérations rattachées
	 * @param integer $delibId id de la délib à supprimer
	 */
	function supprimer($delibId) {
		// lecture de la délib en base
		$delib = $this->find('first', array(
				'recursive' => -1,
				'fields' => array('anterieure_id', 'parent_id'),
				'conditions' => array('id' => $delibId)));
		if (empty($delib)) return;

		// suppression du répertoire des docs
		$repFichier = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$delibId.DS;
		$this->rmDir($repFichier);
		
                // gestion de la séance
                $aSeanceId=$this->getSeancesid($delibId);
                foreach ($aSeanceId as $seance_id) {
                    $this->Deliberationseance->deleteDeliberationseance($delibId, $seance_id);
                }

		// pour les délib rattachées, le traitement finit ici
		if (!empty($delib['Deliberation']['parent_id'])) return;

		// suppression des délib rattachées
		$delibRattachees = $this->find('all', array(
				'recursive' => -1,
				'fields' => array('id'),
				'conditions' => array('parent_id' => $delibId)));
		foreach($delibRattachees as $delibRattachee) {
			$this->supprimer($delibRattachee['Deliberation']['id']);
                        // gestion de la séance
                        $aSeanceId=$this->getSeancesid($delibRattachee['Deliberation']['id']);
                        foreach ($aSeanceId as $seance_id) {
                            $this->Deliberationseance->deleteDeliberationseance($delibRattachee['Deliberation']['id'], $seance_id);
                        }
		}

		// suppression des délib antérieures
		if ( $delib['Deliberation']['anterieure_id'] != 0){
                    $this->supprimer($delib['Deliberation']['anterieure_id']);
                    // gestion de la séance
                    $aSeanceId=$this->getSeancesid($delib['Deliberation']['anterieure_id']);
                    foreach ($aSeanceId as $seance_id) {
                        $this->Deliberationseance->deleteDeliberationseance($delib['Deliberation']['anterieure_id'], $seance_id);
                    }
                }
                
                // suppression de la délib
		$this->delete($delibId);
	}


	/**
	 * Supprime un répertoire et son contenu
	 * @param string $dossier chemin du répertoire à supprimer
     * @return bool
     */
    function rmDir($dossier) {
		$ouverture=@opendir($dossier);
		if (!$ouverture) return;
		while($fichier=readdir($ouverture)) {
			if ($fichier == '.' || $fichier == '..') continue;
			if (is_dir($dossier."/".$fichier)) {
				$r = $this->rmDir($dossier."/".$fichier);
				if (!$r) return false;
			} else {
				$r=@unlink($dossier."/".$fichier);
				if (!$r) return false;
			}
		}
		closedir($ouverture);
		$r=@rmdir($dossier);
		if (!$r) return false;
		return true;
	}

        //function Erroné
	function getSeancesid($deliberation_id) {
		$seances = $this->Deliberationseance->find(
            'all',
            array(
                'fields' => array('Deliberationseance.seance_id'),
                'recursive' => -1,
                'conditions' => array(
                    'Deliberationseance.deliberation_id' => $deliberation_id
                ),
                'order' => 'Deliberationseance.position ASC',
            )
        );

        $seances = (array)Hash::extract($seances, '{n}.Deliberationseance.seance_id');
        return $seances;
    }

	function getSeanceDeliberanteId($deliberation_id) {
		$seances = $this->getSeancesid($deliberation_id);
		return ($this->Seance->getSeanceDeliberante($seances));
	}

	function getNbSeances($deliberation_id) {
		return count($this->getSeancesid($deliberation_id));
	}

	function getSeancesFromArray($projets) {
        $typeseances = $this->Seance->Typeseance->find('list', array('recursive' => -1, 'fields' => array('libelle')));
        $seances = array();
        if (!empty($projets))
            foreach ($projets as $projet)
                if (!empty($projet['Seance'])) {
                    foreach ($projet['Seance'] as $seance) {
                        $typeseance = $typeseances[$seance['type_id']];
                        $seances[$seance['id']] = $typeseance . ' : ' . $seance['date'];
                    }
                }
        return $seances;
    }

    function getTypeseancesFromArray($projets)
    {

        $list = $this->Seance->Typeseance->find('list', array('recursive' => -1, 'fields' => array('libelle')));

        $typeseances = array();
        if (isset($projets) && !empty($projets))
            foreach ($projets as $projet) {
                if (isset($projet['Seance']) && !empty($projet['Seance']))
                    foreach ($projet['Seance'] as $seance)
                        if (isset($seance['type_id'])) {
                            $typeseance = $list[$seance['type_id']];
                            $typeseances[$seance['type_id']] = $typeseance;
                        }
            }
        return $typeseances;
    }

    function getPosition($deliberation_id, $seance_id)
    {
        $deliberationseance = $this->Deliberationseance->find(
            'first',
            array(
                'fields' => array('Deliberationseance.position'),
                'recursive' => -1,
                'conditions' => array(
                    'Deliberationseance.seance_id' => $seance_id,
                    'Deliberationseance.deliberation_id' => $deliberation_id
                ),
                'order' => array('Deliberationseance.position ASC'),
            )
        );

        return $deliberationseance['Deliberationseance']['position'];
    }

    function afficherListePresents($delib_id = null, $seance_id)
    {
        $this->Listepresence->Behaviors->attach('Containable');
        $presents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
            'order' => array("Acteur.position ASC"),
            'contain' => array('Acteur', 'Acteur.Typeacteur')));
        if (empty($presents) && $this->isFirstDelib($delib_id, $seance_id)) {
            $presents = $this->_buildFirstList($delib_id, $seance_id);
        }

        // Si la liste est vide, on recupere la liste des present lors de la derbiere deliberation.
        // Verifier que la liste precedente n'est pas vide...
        if (empty($presents))
            $presents = $this->_copyFromPreviousList($delib_id, $seance_id);

        if (!empty($presents))
            foreach ($presents as &$acteur) {
                if (!empty($acteur['Listepresence']['mandataire'])) {
                    $mandataire = $this->Seance->Typeseance->Acteur->read('nom, prenom', $acteur['Listepresence']['mandataire']);
                    $acteur['Listepresence']['mandataire'] = $mandataire['Acteur']['prenom'] . " " . $mandataire['Acteur']['nom'];
                } elseif (!empty($acteur['Listepresence']['suppleant_id'])) {
                    $suppleant = $this->Seance->Typeseance->Acteur->read('nom, prenom', $acteur['Listepresence']['suppleant_id']);
                    $acteur['Listepresence']['suppleant'] = $suppleant['Acteur']['prenom'] . " " . $suppleant['Acteur']['nom'];
                }
            }
        return $presents;
    }

    function _buildFirstList($delib_id, $seance_id)
    {
        $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'recursive' => -1,
            'fields' => array('Seance.type_id')));
        $elus = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], true);
        foreach ($elus as $elu) {
            $this->Listepresence->create();
            $params['data']['Listepresence']['acteur_id'] = $elu['Acteur']['id'];
            $params['data']['Listepresence']['present'] = 1;
            $params['data']['Listepresence']['delib_id'] = $delib_id;
            $this->Listepresence->save($params['data']);
        }

        return $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
            'order' => array("Acteur.position ASC"),
            'contain' => array('Acteur', 'Acteur.Typeacteur')));
    }

    function _copyFromPreviousList($delib_id, $seance_id)
    {
        $this->Listepresence->Behaviors->attach('Containable');

        $position = $this->getPosition($delib_id, $seance_id);
        if ($position == 1) return NULL;
        $previousDelibId = $this->_getDelibIdByPosition($seance_id, $position);
        $previousPresents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $previousDelibId),
            'recursive' => -1));

        foreach ($previousPresents as $present) {
            $this->Listepresence->create();
            $params['data']['Listepresence']['acteur_id'] = $present['Listepresence']['acteur_id'];
            $params['data']['Listepresence']['mandataire'] = $present['Listepresence']['mandataire'];
            $params['data']['Listepresence']['suppleant_id'] = $present['Listepresence']['suppleant_id'];
            $params['data']['Listepresence']['present'] = $present['Listepresence']['present'];
            $params['data']['Listepresence']['delib_id'] = $delib_id;
            $this->Listepresence->save($params['data']);
        }
        $liste = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
            'order' => array("Acteur.position ASC"),
            'contain' => array('Acteur', 'Acteur.Typeacteur')));
        if (!empty($liste))
            return $liste;
        else
            return ($this->_buildFirstList($delib_id, $seance_id));
    }

    function _effacerListePresence($delib_id)
    {
        $this->Listepresence->deleteAll(array("delib_id" => $delib_id));
    }

    function _getDelibIdByPosition($seance_id, $position)
    {
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $delib = $this->Deliberationseance->find('first', array('conditions' => array('Deliberationseance.position' => $position - 1,
            'Seance.id' => $seance_id),
            'fields' => array('Deliberation.id')));

        if (isset($delib['Deliberation']['id']))
            return $delib['Deliberation']['id'];
        else
            return 0;
    }

    function getDeliberationsSansSeance($fields=array(), $natures_id=array()) {
		if (empty($fields))
			$fields = 'Deliberation.id, Deliberation.objet, Deliberation.circuit_id,
					Deliberation.etat, Deliberation.anterieure_id, Deliberation.etat,
					Deliberation.date_limite, Deliberation.num_pref, Deliberation.titre,
					Deliberation.signee, Deliberation.redacteur_id, Deliberation.typeacte_id,
					Deliberation.service_id, Service.libelle, Nature.libelle, Theme.libelle,
					Deliberation.theme_id';
		elseif($fields == 'id')
			$fields = 'Deliberation.id';
                $natures_id =  implode(", ", $natures_id);
                       
		$requete = "SELECT $fields
					FROM deliberations as Deliberation,
						 services as Service,
						 themes as Theme,
						 typeactes as Typeacte
					WHERE Deliberation.id NOT IN (SELECT deliberation_id FROM deliberations_seances)
						  AND Deliberation.parent_id is null
						  AND Deliberation.theme_id  = Theme.id
						  AND Deliberation.typeacte_id = Typeacte.id
						  AND Deliberation.service_id = Service.id
						  AND Deliberation.typeacte_id IN ($natures_id)
						  AND Deliberation.etat != -1
					ORDER BY Deliberation.created DESC;";

		return ($this->query($requete));

	}
        
       /* function getMultidelibs($delib_id){
            $this->Multidelib->Behaviors->attach('Containable');
            $multidelib=$this->Multidelib->find('all', array(
                    'fields' => array('Deliberation.id'),
                    'contain' => array('Deliberationseance.position'),
                    'conditions' => array('Deliberation.parent_id'=>$delib_id),
                    'order' => array('Deliberationseance.position'),
                    'recursive'   => -1));
        }*/

    function getMultidelibs($delib_id)
    {
        $deliberations = $this->find(
            'all',
            array(
                'fields' => array('Deliberation.id'),
                'recursive' => -1,
                'conditions' => array(
                    'Deliberation.parent_id' => $delib_id
                ),
                'order' => 'Deliberation.id ASC',
            )
        );

        $deliberations = (array)Hash::extract($deliberations, '{n}.Deliberation.id');
        return $deliberations;
    }
        
        /*  function getMultidelibs($delib_id) {
                $this->Behaviors->attach('Containable');
		$deliberations = $this->Deliberationseance->find(
                    'all',
                    array(
                        'fields' => array('Deliberation.parent_id'),
                         'contain'    => array('Deliberationseance','Deliberation'),
                        'recursive' => -1,
                        'conditions' =>  array(
                            'Deliberation.parent_id' => $delib_id
                        ),
                        'order'=>'Deliberationseance.position ASC',
                    )
                );
                debug($deliberations);exit;
              

		$deliberations = (array)Hash::extract($deliberations, '{n}.Deliberationseance.deliberation_id');
		return $deliberations;
	}*/

	function copyPositionsDelibs($delib_id, $new_id) {
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();
		$positions = $this->Deliberationseance->find('all',
				array('conditions' => array('Deliberationseance.deliberation_id' => $delib_id),
		                      'fields'     => array('Deliberationseance.position', 'Deliberationseance.id',
				                            'Deliberationseance.seance_id'),
						'recursive'  => -1));
		foreach($positions as $position) {
			$this->Deliberationseance->id = $position['Deliberationseance']['id'];
			$Deliberationseance['Deliberationseance']['position']  = $position['Deliberationseance']['position'];
			$Deliberationseance['Deliberationseance']['seance_id'] = $position['Deliberationseance']['seance_id'];
			$Deliberationseance['Deliberationseance']['deliberation_id'] = $new_id;
			$this->Deliberationseance->save($Deliberationseance);
		}
	}
  
    function getActesExceptDelib($conditions=array(), $fields, $contain, $order=null) {
        $code_delib = 'DE';
        if (!isset($conditions['Deliberation.typeacte_id']))  {
            $nature_ids = $this->Typeacte->Nature->find('all', array(
                'conditions' => array('Nature.code !=' => $code_delib),
                'recursive'  => -1,
                'fields'     => array('Nature.id')
            ));

            $typeacte_ids = $this->Typeacte->find('all', array(
                'conditions' => array('Typeacte.nature_id' => Set::extract('/Nature/id', $nature_ids)),
                'recursive'  => -1,
                'fields'     => array('Typeacte.id')
            ));

            $conditions = array_merge($conditions,  array('Deliberation.typeacte_id' => Set::extract('/Typeacte/id', $typeacte_ids)));
        }

        $this->Behaviors->load('Containable');
        $actes = $this->find('all', array(
            'conditions' => $conditions,
            'contain'    => $contain,
            'fields'     => $fields,
            'order'      => $order
        ));

        foreach ($actes as &$acte) {
            $acte['Modeltemplate']['modeleprojet_id'] = $this->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modeleprojet_id');
            $acte['Modeltemplate']['modelefinal_id'] = $this->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modelefinal_id');
        }
        return $actes;
    }

    function getActesATeletransmettre($conditions=array(), $fields, $contain, $order=null) {
        if (!isset($conditions['Deliberation.typeacte_id'])){
            $typeacte_ids = $this->Typeacte->find('all', array(
                'recursive'  => -1,
                'conditions' => array('Typeacte.teletransmettre' => true),
                'fields'     => array('Typeacte.id')));
            $conditions = array_merge($conditions,  array('Deliberation.typeacte_id' => Set::extract('/Typeacte/id', $typeacte_ids)));
        }
        $this->Behaviors->load('Containable');
        $actes = $this->find('all', array(
            'conditions' => $conditions,
            'contain' => $contain,
            'fields' => $fields,
            'order' => $order));
        foreach ($actes as &$acte) {
            $acte['Modeltemplate']['modeleprojet_id'] = $this->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modeleprojet_id');
            $acte['Modeltemplate']['modelefinal_id'] = $this->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modelefinal_id');
        }
        return $actes;
    }

    function is_delib($acte_id) {
        $this->Behaviors->load('Containable');
        $acte = $this->find('first', array(
            'conditions' => array('Deliberation.id' => $acte_id),
            'contain' => array('Typeacte.nature_id'),
            'fields' => array('Deliberation.typeacte_id')));
        $nature = $this->Typeacte->Nature->find('first', array(
            'conditions' => array('Nature.id' => $acte['Typeacte']['nature_id']),
            'fields' => array('Nature.code'),
            'recursive' => -1));
        return ($nature['Nature']['code'] == 'DE');
    }

    function is_arrete($acte_id) {
        return !($this->is_delib($acte_id));
    }

    function reportePositionToCommissions($delib_id, $seance_deliberante_id) {
        $position = $this->getPosition($delib_id, $seance_deliberante_id);
        $seances = $this->getSeancesid($delib_id);

        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();

        foreach ($seances as $seance_id) {
            $Deliberationseance = $this->Deliberationseance->find('first', array(
                'conditions' => array(
                    'Deliberationseance.deliberation_id' => $delib_id,
                    'Deliberationseance.seance_id' => $seance_id
                ),
                'fields' => array('Deliberationseance.id'),
                'recursive' => -1));
            $this->Deliberationseance->id = $Deliberationseance['Deliberationseance']['id'];
            $this->Deliberationseance->saveField('position', $position);
        }
        return $seances;
    }

    /**
     * @param string $message
     * @param integer $delib_id
     * @param integer $circuit_id
     * @param integer $user_id
     * @return bool
     */
    function setHistorique($message, $delib_id, $circuit_id=null, $user_id = -1){
        $histo = $this->Historique->create();
        $histo['Historique']['delib_id'] = $delib_id;
        $histo['Historique']['user_id'] = $user_id;
        $histo['Historique']['commentaire'] = $message;
        $histo['Historique']['circuit_id'] = $circuit_id;
        return $this->Historique->save($histo, false);
    }

    /**
     * @param integer $delib_id
     * @param string $logdossier
     */
    function setCommentaire($delib_id, $logdossier){
        $com = $this->Commentaire->create();
        $com['Commentaire']['delib_id'] = $delib_id;
        $com['Commentaire']['agent_id'] = -1;
        $com['Commentaire']['pris_en_compte'] = 0;
        $com['Commentaire']['commentaire_auto'] = false;
        $com['Commentaire']['texte'] = $logdossier['nom'] . " : " . $logdossier['annotation'];
        $this->Commentaire->save($com['Commentaire']);
    }

    /**
     * @return string status d'exécution et rapport
     */
    function majActesParapheur() {
        // status sous forme de chaines de caractères a insérer dans le rapport d'exécution des procédures appelées par les crons
        $ret_success = 'TRAITEMENT_TERMINE_OK';
        $ret_error = 'TRAITEMENT_TERMINE_ERREUR';
        try{
            //Si service désactivé ==> quitter
            if (!Configure::read('USE_PARAPHEUR')) {
                return $ret_error.'i-Parapheur désactivé';
            }
            App::import('Component', 'Iparapheur');
            $this->Iparapheur = new IparapheurComponent();

            // Controle de l'avancement des délibérations dans le parapheur
            $delibs = $this->find('all', array(
                'conditions' => array(
                    'Deliberation.etat >' => 2,
                    'Deliberation.parapheur_etat' => 1
                ),
                'recursive' => -1,
                'fields' => array('id', 'objet')));

            $rapport = "\n";
            foreach ($delibs as $delib) {
                $objetDossier = $delib['Deliberation']['objet'];
                $rapport .= 'Projet "['.$delib['Deliberation']['id'].'] - '.$delib['Deliberation']['objet'].'" : ';
                $success_dos = $this->majActeParapheur($delib['Deliberation']['id'], $objetDossier, false);
                if (!$success_dos)
                    $rapport .= "En attente de fin de circuit\n";
                else
                    $rapport .= "Circuit terminé, dossier effacé du i-Parapheur\n";
            }
            return $ret_success.$rapport;
        }catch (Exception $e){
            return $ret_error.$e->getTraceAsString();
        }
    }

    /**
     * @param integer $delib_id identifiant du projet à mettre à jour
     * @param string $objet
     * @param bool $tdt
     * @return bool true si le dossier à terminé son circuit
     */
    function majActeParapheur($delib_id, $objet, $tdt = false) {
        $this->id = $delib_id;
        $delib = $this->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $delib_id,
                'etat >' => 2,
                'parapheur_etat' => 1
            )
        ));

        if (!empty($delib['Deliberation']['parapheur_id']))
            $id_dossier = $delib['Deliberation']['parapheur_id'];
        else //@DEPRECATED (rétro-compatibilité vieux dossiers parapheur)
            $id_dossier = "$delib_id $objet";

        App::import('Component', 'Iparapheur');
        $this->Iparapheur = new IparapheurComponent();
        // Récupération log historique coté parapheur
        $histo = $this->Iparapheur->getHistoDossierWebservice($id_dossier);
        if (isset($histo['logdossier'])){
            // Parcours des étapes du log
            for ($i = 0; $i < count($histo['logdossier']); $i++) {
                // Cas envoi direct au tdt depuis parapheur (surement inutilisé)
                if ($tdt && $histo['logdossier'][$i]['status'] == 'EnCoursTransmission')
                    return true;
                if (in_array($histo['logdossier'][$i]['status'], array('Vise', 'Signe', 'Archive'))) {
                    if (in_array($histo['logdossier'][$i]['status'], array('Signe', 'Vise'))) { // Etape visa ou signature
                        //Annotations
                        $this->setCommentaire($delib_id, $histo['logdossier'][$i]);
                        $this->saveField('signee', true);
                    }
                    elseif ($histo['logdossier'][$i]['status'] == 'Archive'){ // Dernière étape : archive
                        $dossier = $this->Iparapheur->GetDossierWebservice($id_dossier);
                        // Sauvegarde du bordereau (pdf)
                        if (!empty($dossier['getdossier']['bordereau']))
                            $this->saveField('parapheur_bordereau', base64_decode($dossier['getdossier']['bordereau']));
                        // Sauvegarde des signatures (zip)
                        if (!empty($dossier['getdossier']['signature']))
                            $this->saveField('signature', base64_decode($dossier['getdossier']['signature']));
                        // Etat retour ok
                        $this->saveField('parapheur_etat', 2);
                        $this->Iparapheur->archiverDossierWebservice($id_dossier, 'EFFACER');
                        // Ajout de l'info d'approbation parapheur dans l'historique
                        $this->setHistorique('Dossier approuvé par le parapheur', $delib_id);
                        return true;
                    }
                } elseif ($histo['logdossier'][$i]['status'] == 'RejetSignataire'
                    || $histo['logdossier'][$i]['status'] == 'RejetVisa') { // Cas de refus dans le parapheur
                    // Etat refusé
                    $this->saveField('parapheur_etat', -1);
                    // Motif de rejet
                    $this->saveField('parapheur_commentaire', $histo['logdossier'][$i]['annotation']);
                    // Supprimer le dossier du parapheur
                    $this->Iparapheur->effacerDossierRejeteWebservice($id_dossier);
                    // Annotation (motif de rejet)
                    $this->setCommentaire($delib_id, $histo['logdossier'][$i]);
                    // Ajout de l'info de rejet parapheur dans l'historique
                    $this->setHistorique('Dossier rejeté par le parapheur', $delib_id);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @link Signature::updateAll()
     * @link DeliberationsController::refreshPastell()
     * @return string status d'exécution et rapport
     */
    function majSignaturesPastell() {
        // status sous forme de chaines de caractères a insérer dans le rapport d'exécution des procédures appelées par les crons
        try{
            //Si service désactivé ==> quitter
            if (!Configure::read('USE_PASTELL')) {
                return "TRAITEMENT_TERMINE_ERREUR\nPastell désactivé";
            }

            // Controle de l'avancement des délibérations dans le parapheur
            $delibs = $this->find('all', array(
                'conditions' => array(
                    'Deliberation.etat >' => 2,
                    'Deliberation.parapheur_etat' => 1,
                    'Deliberation.parapheur_id !=' => null
                ),
                'recursive' => -1,
                'fields' => array('id', 'objet')));

            $rapport = '';
            foreach ($delibs as $delib) {
                $rapport .= "\nProjet n°".$delib['Deliberation']['id'].' ('.$delib['Deliberation']['objet'].") :\n";
                $success_dos = $this->majSignaturePastell($delib['Deliberation']['id']);
                if (!$success_dos)
                    $rapport .= "En attente de fin de circuit";
                else
                    $rapport .= "Circuit terminé, dossier rappatrié";
            }
            return 'TRAITEMENT_TERMINE_OK'.$rapport;
        }catch (Exception $e){
            return 'TRAITEMENT_TERMINE_ERREUR'."\n".$e->getMessage();
        }
    }

    /**
     * @param integer $delib_id identifiant du projet à mettre à jour
     * @return bool true si le dossier à terminé son circuit
     * @throws Exception
     */
    function majSignaturePastell($delib_id) {
        $delib = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id','objet','pastell_id'),
            'conditions' => array(
                'Deliberation.id' => $delib_id,
                'Deliberation.etat >' => 2,
                'Deliberation.parapheur_etat' => 1
            )));
        if (empty($delib))
            return false;
        App::uses('Signature','Lib');
        $this->Signature = new Signature;
        $infos = $this->Signature->updateInfosPastell($delib['Deliberation']['pastell_id']);
        $this->id = $delib_id;
        
        if ($infos['last_action']['action'] == 'rejet-iparapheur'){
            $this->saveField('parapheur_etat', '-1');
            $this->saveField('parapheur_commentaire', $infos['last_action']['message']);
            //Ajout de l'action à l'historique
            $this->setHistorique($infos['last_action']['message'], $delib_id, 0);
            //Commentaire refus
            $this->Commentaire->create();
            $com = array();
            $com['Commentaire']['delib_id'] = $delib_id;
            $com['Commentaire']['agent_id'] = -1;
            $com['Commentaire']['texte'] = $infos['last_action']['message'];
            $com['Commentaire']['commentaire_auto'] = 0;
            $this->Commentaire->save($com);
            return true;
        }elseif ($infos['last_action']['action'] == 'recu-iparapheur' || !empty($infos['data']['has_signature'])){
            //Récupération du bordereau
            $bordereau = $this->Signature->getBordereau($delib['Deliberation']['pastell_id']);
            if (!empty($bordereau))
                $this->saveField('parapheur_bordereau', $bordereau);
            //Signature
            if (!empty($infos['data']['has_signature'])){
                $signature = $this->Signature->getSignature($delib['Deliberation']['pastell_id']);
                if (!empty($signature))
                    $this->saveField('signature', $signature);
                $this->saveField('signee', true);
            }
            $this->saveField('parapheur_etat', '2');
            $this->saveField('parapheur_commentaire', $infos['last_action']['message']);

            $this->setHistorique($infos['last_action']['message'], $delib_id, 0);
            return true;
        }
        return false;
    }

    /**
     * Met à jour la date d'AR des dossiers envoyés au TDT
     */
    public function majArAll() {
        $rapport = 'Rien à faire';
        $actes = $this->find('all', array(
            'conditions' => array(
                'etat' => 5,
                'tdt_dateAR' => null
            ),
           'fields' => array(
                'id',
                'tdt_id',
                'pastell_id',
                'tdt_dateAR',
                'num_delib'
            ),
            'recursive' => -1
        ));

        foreach ($actes as $acte) {
            if ($ar = $this->majAr($acte)) {
                $rapport .= "Délibération " . $acte['Deliberation']['num_delib'] . " reçue le " . date('d/m/Y', strtotime($ar)) . ".\n";
                //$rapport .= $this->majEchangesTdtAll($acte['Deliberation']['id']); //Recuperation du tampon et bordereau initial
            } else {
                $rapport .= "Délibération " . $acte['Deliberation']['num_delib'] . " en attente de réception.\n";
            }
        }

        return $rapport;
    }

    /**
     * Met à jour la date d'AR d'une délib
     *
     * @param Object $acte Deliberation
     * @return bool succès
     */
    public function majAr($acte) {
        App::uses('Tdt', 'Lib');
        $Tdt = new Tdt;
        $ar = $Tdt->getDateAr($acte);
        if ($ar) {
            $this->id = $acte['Deliberation']['id'];
//            $this->saveField('tdt_data_bordereau_pdf', $Tdt->getBordereau($id));
            $this->saveField('tdt_dateAR', $ar);
            return $ar;
        } else
            return false;
    }

    /**
     * Mise à jour des echange TDT / Préfecture pour les envois de moins de 2 mois
     */
    public function majEchangesTdtAll($delib_id=null) {
        App::uses('Tdt', 'Lib');
        $Tdt = new Tdt;
        $rapport = '';
        if(!empty($delib_id)){
            $conditions=array('id' => $delib_id);
        }
        else
        {
        $conditions = array();
        $conditions['AND'] = array(
            'OR' => array(
                    array('AND' => array('date_envoi_signature >=' => date('Y-m-d 00:00:00', strtotime("-80 days")))),
                    array('AND' => array('date_acte >=' => date('Y-m-d 00:00:00', strtotime("-80 days"))))
                ),'etat' => 5
            );
        }
        $actes = $this->find('all', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array(
                'id',
                'tdt_id',
                'pastell_id',
                'tdt_dateAR',
                'num_delib',
                'tdt_ar',
                'tdt_data_pdf',
                'tdt_data_bordereau_pdf'
            )
        ));
        foreach ($actes as $acte) {
        try{    
            $this->id = $acte['Deliberation']['id'];
            if(empty($acte['Deliberation']['tdt_ar'])){
            $tdtArActe=$Tdt->getArActe($acte);
            if (!empty($tdtArActe)) {
                //Récupération de l'ARacte
                $this->saveField('tdt_ar', $tdtArActe);
                $rapport .= "Délibération " . $acte['Deliberation']['num_delib'] . " : Echanges mis à jour.\n";
            }}
            if(!empty($acte['Deliberation']['tdt_ar'])){
                $tdt_data_pdf=$this->findById($this->id,'tdt_data_pdf');
                if(empty($acte['Deliberation']['tdt_data_pdf'])){
                $tampon=$Tdt->getTampon($acte);
                if (!empty($tampon)) {
                    //Récupération du tampon
                    $this->saveField('tdt_data_pdf', $tampon);
                    $rapport .= "Délibération " . $acte['Deliberation']['num_delib'] . " : Echanges mis à jour.\n";
                }}

                if(empty($acte['Deliberation']['tdt_data_bordereau_pdf'])){
                $bordereau=$Tdt->getBordereau($acte);
                if (!empty($bordereau)) {
                    //Récupération du bordereau
                    $this->saveField('tdt_data_bordereau_pdf', $bordereau);
                    $rapport .= "Délibération " . $acte['Deliberation']['num_delib'] . " : Echanges mis à jour.\n";
                }}
                if ($this->majEchangesTdt($acte)) {
                    $rapport .= "Délibération " . $acte['Deliberation']['num_delib'] . " : Echanges mis à jour.\n";
                }
            }
        } catch (Exception $e) {
            continue;
        }

        }
        return $rapport;
    }

    /**
     * Met à jour la date d'AR d'une délib
     * @param array $acte objet Deliberation
     * @return bool
     */
    public function majEchangesTdt($acte) {
        App::uses('Tdt', 'Lib');
        try {
            $Tdt = new Tdt;
            if(!$infos = $Tdt->getReponses($acte))
                return false;
            
            $this->TdtMessage->begin();
            foreach ($infos as $info) {
                //Si Le message existe déjà ne rien faire
                $message=$this->TdtMessage->find('first', array(
                    'fields' => array('id','tdt_id'),
                    'conditions' => array('tdt_id' => $info['id']),
                    'recursive' => -1,
                ));
                
                if(!empty($message))
                    continue;
                
                //Recherche un parent a ce message
                $message_parent=$this->TdtMessage->find('first', array(
                    'fields' => array('id','tdt_id'),
                    'conditions' => array('tdt_type' => $info['type'],'delib_id' => $acte['Deliberation']['id'], 'parent_id IS NULL'),
                    'recursive' => -1,
                ));
                
                if (!empty($message_parent) && !empty($info['status']) && in_array($info['status'], array(7,8))){
                    new Exception("Attention, il y a plusieurs messages de même type, cette situation n'est pas traitée par web-delib (num_delib: ".$acte['Deliberation']['id'] . '=>' . $info['type'].')');
                }
                
                $this->TdtMessage->create();
                $TdtMessage=array(
                                    'delib_id' => $acte['Deliberation']['id'],
                                    'tdt_id' => $info['id'],
                                    'tdt_type' => $info['type'],
                                    'tdt_etat' => empty($info['status'])?NULL:$info['status'],
                                    'tdt_data' => $info['data'],
                                    'date_message' => empty($info['date_message'])?NULL:$info['date_message'],
                                    'parent_id' => !empty($message_parent)?$message_parent['TdtMessage']['id']:NULL,
                                 );
                $this->TdtMessage->save($TdtMessage);
            }
            $this->TdtMessage->commit();
            return true;
        } catch (Exception $e) {
            $this->TdtMessage->rollback();
            $this->log($e->getTraceAsString(), 'tdt');
            return false;
        }
    }

    function chercherVersionAnterieure($anterieure_id, $nb_recursion, $listeAnterieure, $action)
    {
        if ($anterieure_id != 0) {
            $projet = $this->find('first', array(
                'fields' => array('created', 'anterieure_id'),
                'conditions' => array("Deliberation.id" => $anterieure_id),
                'recursive' => -1,
                ));
            $date_version = $projet['Deliberation']['created'];
            $listeAnterieure[$nb_recursion]['id'] = $anterieure_id;
            
            $listeAnterieure[$nb_recursion]['lien'] = Router::url(array('controller' => 'deliberations', 'action' => $action, $anterieure_id));
            $listeAnterieure[$nb_recursion]['date_version'] = $date_version;
            //on stocke les id des delibs anterieures
            $listeAnterieure = $this->chercherVersionAnterieure($projet['Deliberation']['anterieure_id'], $nb_recursion + 1, $listeAnterieure, $action);
        }
        return $listeAnterieure;
    }

    function chercherVersionSuivante($delib_id){
        $delib = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => array(
                'anterieure_id' => $delib_id
            )
        ));
        if (empty($delib))
            return false;

        return $delib['Deliberation']['id'];
    }


    /**
     * fonction de callback du behavior OdtFusion
     * retourne l'id du model odt à utiliser pour la fusion
     * @param integer $id id de l'occurence en base de données
     * @param array modelOptions options gérées par la classe appelante
     * @return integer id du modele odt à utiliser
     */
    function getModelTemplateId($id, $modelOptions) {
        return $this->getModelId($id);
    }

    public function beforeSave($options = array()) {
        if (!empty($this->data['Deliberation']['texte_doc'])){
            $analyse=$this->analyzeFile($this->data['Deliberation']['texte_doc']['tmp_name']);
            $this->data['Deliberation']['debat_type']=$analyse['mimetype'];
            $this->data['Deliberation']['debat_name']=$this->data['Deliberation']['texte_doc']['name'];
            $this->data['Deliberation']['debat_size']=$this->data['Deliberation']['texte_doc']['size'];
            $this->data['Deliberation']['debat']=file_get_contents($this->data['Deliberation']['texte_doc']['tmp_name']);
        }
        return true;
    }
    
    /**
     * fonction de callback du behavior OdtFusion
     * initialise les variables de fusion Gedooo
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id de l'occurence en base de données
     * @param array modelOptions options gérées par la classe appelante
     * @return void
     */
    function beforeFusion(&$aData, &$modelOdtInfos, $id, $modelOptions) {
        if (!empty($modelOptions['deliberationIds']))
            $this->setVariablesFusionDeliberations($aData, $modelOdtInfos, $modelOptions['deliberationIds']);
        else
            $this->setVariablesFusion($aData, $modelOdtInfos, $id);
    }

    /**
     * fonction d'initialisation des variables de fusion pour un projet ou une délibération
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param phpOdtApi $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id l'id à fusionner
     * @param integer $fusionSeanceId id de la séance de la fusion (optionnel, null par défaut)
     * @throws Exception
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id, $fusionSeanceId=null) {
        App::uses('DateComponent', 'Controller/Component');
        App::uses('Component', 'Controller');
        // initialisations
        $collection = new ComponentCollection();
        $this->Date = new DateComponent($collection);
        // liste des champs à lire en base de données
        $fields = array('id', 'service_id', 'theme_id', 'rapporteur_id', 'redacteur_id', 'president_id', 'is_multidelib',
            'titre', 'objet', 'objet_delib', 'etat', 'num_delib', 'num_pref', 'date_envoi_signature',
            'vote_nb_oui', 'vote_nb_abstention', 'vote_nb_non', 'vote_nb_retrait', 'tdt_dateAR', 'vote_commentaire');
        if ($modelOdtInfos->hasUserField('texte_projet')) $fields[] = 'texte_projet';
        if ($modelOdtInfos->hasUserFields('texte_deliberation', 'texte_acte')) $fields[] = 'deliberation';
        if ($modelOdtInfos->hasUserField('note_synthese')) $fields[] = 'texte_synthese';
        if ($modelOdtInfos->hasUserField('debat_deliberation')) $fields[] = 'debat';
        if ($modelOdtInfos->hasUserField('debat_commission')) $fields[] = 'commission';

        // lecture de l'occurence en base de données
        $delib = $this->find('first', array(
            'recursive' => -1,
            'fields' => $fields,
            'conditions' => array('id'=>$id)));
        if (empty($delib))
            throw new Exception('délibération id:'.$id.' non trouvée en base de données');

        // variables du projet (en dehors de toute section)
        if ($modelOdtInfos->hasUserFieldDeclared('titre_projet'))
            $aData['titre_projet']=$delib['Deliberation']['titre'];// 'lines'));
        if ($modelOdtInfos->hasUserFieldDeclared('objet_projet')) {
            if (empty($delib['Deliberation']['is_multidelib']))
                $aData['objet_projet']=$delib['Deliberation']['objet'];// 'lines'));
            else
                $aData['objet_projet']=$delib['Deliberation']['objet_delib'];// 'lines'));
        }

        if ($modelOdtInfos->hasUserFieldDeclared('libelle_projet'))
            $aData['libelle_projet']=$delib['Deliberation']['objet'];//, 'lines'));
        if ($modelOdtInfos->hasUserFieldDeclared('objet_delib'))
            $aData['objet_delib']=$delib['Deliberation']['objet_delib'];//, 'lines'));
        if ($modelOdtInfos->hasUserFieldDeclared('libelle_delib'))
            $aData['libelle_delib']=$delib['Deliberation']['objet_delib'];//, 'lines'));
        if ($modelOdtInfos->hasUserFieldDeclared('identifiant_projet'))
            $aData['identifiant_projet']=$delib['Deliberation']['id'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('service_emetteur'))
            $aData['service_emetteur']=$this->Service->field('libelle', array('id'=>$delib['Deliberation']['service_id']));//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('service_avec_hierarchie'))
            $aData['service_avec_hierarchie']=$this->Service->_doList($delib['Deliberation']['service_id']);//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('etat_projet'))
            $aData['etat_projet']= $delib['Deliberation']['etat'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('classification_deliberation'))
            $aData['classification_deliberation']=$delib['Deliberation']['num_pref'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('date_envoi_signature'))
            $aData['date_envoi_signature']=$this->Date->frDate($delib['Deliberation']['date_envoi_signature']);//, 'date'));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_seance'))
            $aData['nombre_seance']=$this->getNbSeances($delib['Deliberation']['id']);//, 'text'));
      
        // Information du service émetteur
        if ($modelOdtInfos->hasUserFieldsDeclared('service_emetteur', 'service_avec_hierarchie'))
            $this->Service->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['service_id']);
        // Informations sur le thème
        $this->Theme->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['theme_id']);
        
        // Informations sur le Redacteur
        if (!empty($delib['Deliberation']['redacteur_id']))
            $aProjet[]=$this->Redacteur->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['redacteur_id']);
        // Informations sur le rapporteur
        if (!empty($delib['Deliberation']['rapporteur_id']))
            $aProjet[]=$this->Rapporteur->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['rapporteur_id']);
        // Liste des commentaires
        if ($modelOdtInfos->hasUserFieldDeclared('texte_commentaire'))
            $aProjet[]=$this->Commentaire->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['id']);
        // Hitoriques
        if ($modelOdtInfos->hasUserFieldDeclared('log'))
            $aProjet[]=$this->Historique->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['id']);

        // Informations supplémentaires
        $this->Infosup->setVariablesFusion($aData, $modelOdtInfos, 'Deliberation', $delib['Deliberation']['id']);

        // variables de la délibération (en dehors de toute section)
        if ($modelOdtInfos->hasUserFieldDeclared('numero_acte'))
            $aProjet['numero_acte']=$delib['Deliberation']['num_delib'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('numero_deliberation'))
            $aProjet['numero_deliberation']=$delib['Deliberation']['num_delib'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('acte_adopte'))
            $aProjet['acte_adopte']=($delib['Deliberation']['etat'] == 3 || $delib['Deliberation']['etat'] == 5) ? '1' : '0';//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_pour'))
            $aProjet['nombre_pour']=$delib['Deliberation']['vote_nb_oui'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_abstention'))
            $aProjet['nombre_abstention']=$delib['Deliberation']['vote_nb_abstention'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_contre'))
            $aProjet['nombre_contre']=$delib['Deliberation']['vote_nb_non'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_sans_participation'))
            $aProjet['nombre_sans_participation']=$delib['Deliberation']['vote_nb_retrait'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_votant'))
            $aProjet['nombre_votant']=$delib['Deliberation']['vote_nb_oui'] + $delib['Deliberation']['vote_nb_abstention'] + $delib['Deliberation']['vote_nb_non'];//, 'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('commentaire_vote'))
            $aProjet['commentaire_vote']=$delib['Deliberation']['vote_commentaire'];//, 'lines'));
        if ($modelOdtInfos->hasUserFieldDeclared('date_reception'))
            $aProjet['date_reception']=$delib['Deliberation']['tdt_dateAR'];//, 'text'));
        
        // variables des multi délibérations
        if ($delib['Deliberation']['is_multidelib'] && $modelOdtInfos->hasUserFieldsDeclared('libelle_multi_delib', 'id_multi_delib')) {
            $multidelibs = $this->find('all', array(
                'recursive' => -1,
                'fields'     => array('id', 'objet'),
                'conditions' => array('parent_id' => $delib['Deliberation']['id'])));
            if (!empty($multidelibs)) {
                $oIteration = new GDO_IterationType('Deliberations');
                foreach($multidelibs as $multidelib) {
                    $oDevPart = new GDO_PartType();
                    if ($modelOdtInfos->hasUserFieldDeclared('libelle_multi_delib'))
                        $oDevPart->addElement(new GDO_FieldType('libelle_multi_delib', $multidelib['Deliberation']['objet'], 'text'));
                    if ($modelOdtInfos->hasUserFieldDeclared('id_multi_delib'))
                        $oDevPart->addElement(new GDO_FieldType('id_multi_delib', $multidelib['Deliberation']['id'], 'text'));
                    $oIteration->addPart($oDevPart);
                }
                $oMainPart->addElement($oIteration);
            }
        }

        $fileodts=array(//champs->variables modèle
            'texte_projet'=>'texte_projet',
            'texte_deliberation'=>'deliberation',
            'deliberation'=>'texte_acte',
            'texte_synthese'=>'note_synthese',
            'debat'=>'debat_deliberation',
            'commission'=>'debat_commission',
            'texte_deliberation'=>'texte_deliberation',
        );
        
        foreach($fileodts as $champ=>$variable )
        if ($modelOdtInfos->hasUserField($variable)) {
            if (!empty($delib['Deliberation'][$champ])) {
               $aData['fileodt.'.$variable]=$delib['Deliberation'][$champ];
            }
        }

        // annexes
        $this->Annex->setVariablesFusion($aData, $modelOdtInfos, 'Projet', $id);

        // nombre de séances du projet
        $seanceIds = $this->Deliberationseance->nfield('seance_id', array('Deliberationseance.deliberation_id'=>$delib['Deliberation']['id']), array('Seance.date'));
        $aData['nombre_seance']=count($seanceIds);//, 'text'));

        // position du projet dans la séance de l'édition ou de la séance délibérante
        if ($modelOdtInfos->hasUserFieldDeclared('position_projet')) {
            if (empty($fusionSeanceId))
                $positionSeanceId = empty($seanceIds)?null:$seanceIds[count($seanceIds)-1];
            else
                $positionSeanceId = $fusionSeanceId;
            $position = empty($positionSeanceId)?0:$this->getPosition($delib['Deliberation']['id'], $positionSeanceId);
            $aProjet['position_projet']=$position;//, 'text'));
        }

        // itération sur les séances
        if (empty($fusionSeanceId) && !empty($seanceIds)) {
            // dernière séance (merci M. Eddy) : délibérante
            $this->Seance->setVariablesFusion($aData['seance'], $modelOdtInfos, $seanceIds[count($seanceIds)-1], 'seance', false);
            // pour toutes les séances
            $this->Seance->setVariablesFusionSeances($aData,$modelOdtInfos, $seanceIds, false);
        }

        //avis du projet
        if (!empty($fusionSeanceId) && $modelOdtInfos->hasUserFieldDeclared('ProjetAvis_avis', 'ProjetAvis_commentaire', 'ProjetAvis_commentaire'))
            $this->Deliberationseance->setVariablesFusionAvis($aData, $modelOdtInfos, $id, $fusionSeanceId);
        // avis des séances
        if ($modelOdtInfos->hasUserFieldsDeclared('avis', 'avis_favorable', 'commentaire'))
            $this->Deliberationseance->setVariablesFusionPourUnProjet($aData, $modelOdtInfos, $id, $fusionSeanceId);

        // listes des présents et suppléants, absents, mandatés
        $this->Listepresence->setVariablesFusionPresents($aData, $modelOdtInfos, $delib['Deliberation']['id']);
        $this->Listepresence->setVariablesFusionAbsents($aData, $modelOdtInfos, $delib['Deliberation']['id']);
        $this->Listepresence->setVariablesFusionMandates($aData, $modelOdtInfos, $delib['Deliberation']['id']);

        // votes
        $aProjet[]=$this->Vote->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['id']);

        // président de séance de la délibération
        if (!empty($delib['Deliberation']['president_id']))
            $this->President->setVariablesFusion($aData, $modelOdtInfos, $delib['Deliberation']['president_id']);
    }

    /**
     * Construit le tableau à envoyer au parapheur
     * @param $acte_id
     * @return array
     */
    public function getDocumentsForDelegation($acte_id){
        $docs = array(
            'docPrincipale' => $this->getDocument($acte_id),
            'annexes' => array()
        );
        foreach ($this->Annex->getAnnexesWithoutFusion($acte_id) as $annexe) {
            $docs['annexes'][] = array(
                'content' => $annexe['Annex']['data'],
                'mimetype' => $annexe['Annex']['filetype'],
                'filename' => $annexe['Annex']['filename']
            );
        }
        return $docs;
    }

    function getDocument($acte_id, $format = 'pdf') {
        // fusion du document
        return $this->fusion($acte_id, null, null, $format);
    }

    public function getAnnexesToSend($acte_id){
        $annexes = array();
        $to_send = $this->Annex->getAnnexesFromDelibId($acte_id, true);
        $docs_type = Configure::read('DOC_TYPE');
        foreach ($to_send as $annexe) {
            if ($docs_type[$annexe['Annex']['filetype']]['convertir']){
                if (!empty($annexe['Annex']['data_pdf']))
                    $annexes[] = array(
                        'content' => $annexe['Annex']['data_pdf'],
                        'mimetype' => 'application/pdf',
                        'filename' => AppTools::getNameFile($annexe['Annex']['filename']) . '.pdf'
                    );
            } else
                $annexes[] = array(
                    'content' => $annexe['Annex']['data'],
                    'mimetype' => $annexe['Annex']['filetype'],
                    'filename' => $annexe['Annex']['filename']
                );
        }
        return $annexes;
    }

    /**
     * Retour les annexes à joindre au controle de légalité
     */
    function getAnnexes($acte_id, $extention='pdf'){
        $annexes=array();
        $i=0;
        $found = $this->Annex->getAnnexesFromDelibId($acte_id, true);
        foreach ($found as $annexe) {
            $annexes[$i]['id'] = $annexe['Annex']['id'];
            switch ($extention) {
               case 'pdf':
               default:
                   if (!empty($annexe['Annex']['data_pdf'])){
                       $annexes[$i]['content'] = $annexe['Annex']['data_pdf'];
                       $annexes[$i]['mimetype'] = 'application/pdf';
                       $annexes[$i]['filename'] = AppTools::getNameFile($annexe['Annex']['filename']).'.pdf';//Replace avec .pdf
                   }
                break;
            }
            $i++;
        }

        return $annexes;
    }

    /**
     * fonction d'initialisation des variables de fusion pour un ou plusieurs projets ou délibérations
     * @param object $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $ids liste des id des délibérations
     */
    function setVariablesFusionDeliberations(&$aData, &$modelOdtInfos, $ids) {
        // pour tous les projets/délibérations
        foreach($ids as $id) {
            $this->setVariablesFusion($aData['Projets'][], $modelOdtInfos, $id);
        }
    }

    /**
     * Fonction de signature manuscrite
     * Signe l'acte
     * Génère le document delib_pdf
     * Génère un numéro de delib si c'est un arrêté
     * Enregistre dans l'historique de l'acte
     * @param int $acte_id
     * @param int $user_id
     * @return bool
     */
    public function signatureManuscrite($acte_id, $user_id){
        $success = true;
        $this->begin();
        $acte = $this->find('first', array(
            'conditions' => array('Deliberation.id' => $acte_id),
            'contain' => array('Typeacte.compteur_id', 'Typeacte.nature_id')
        ));
        if (empty($acte['Deliberation']['num_delib'])) {
            $acte['Deliberation']['etat'] = 3;
            $acte['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($acte['Typeacte']['compteur_id']);
        }
        $acte['Deliberation']['date_acte'] = date("Y-m-d H:i:s", strtotime("now"));
        $acte['Deliberation']['date_envoi_signature'] = date("Y-m-d H:i:s", strtotime("now"));
        $acte['Deliberation']['signee'] = true;

        $this->begin();
        $this->save($acte);
        $this->commit();
        
        //Document delib_pdf
        $content = $this->getDocument($acte_id);
        if (!empty($content)) { // On stoque le fichier en base de données
            $acte['Deliberation']['delib_pdf'] = $content;
        } else $success = false;

        //Historique
        $success &= $this->Historique->enregistre($acte_id, $user_id, "Signature manuscrite");

        if ($success && $this->save($acte)){
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }


    /**
     * Fonction d'envoi au parapheur
     * Génère le document delib_pdf
     * Génère un numéro de delib si c'est un arrêté
     * Enregistre dans l'historique de l'acte
     * @param int $acte_id identifiant de l'acte
     * @param int $circuit_id circuit identifiant du circuit parapheur
     * @param int $user_id identifiant de l'utilisateur (pour historique)
     * @return bool success
     */
    public function envoyerAuParapheur($acte_id, $circuit_id, $user_id){
        $success = true;
        $this->begin();
        $acte = $this->find('first', array(
            'conditions' => array('Deliberation.id' => $acte_id),
            'contain' => array('Typeacte.compteur_id', 'Typeacte.nature_id')
        ));

        if (empty($acte['Deliberation']['num_delib'])) {
            $acte['Deliberation']['etat'] = 3;
            $acte['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($acte['Typeacte']['compteur_id']);
        }

        //Document delib_pdf
        $content = $this->getDocument($acte_id);
        if (!empty($content)) { // On stoque le fichier en base de données
            $acte['Deliberation']['delib_pdf'] = $content;

            App::uses('Signature','Lib');
            try{
                $this->Signature = new Signature;
                $reponse = $this->Signature->send($acte, $circuit_id, $acte['Deliberation']['delib_pdf'], $this->getAnnexesToSend($acte_id));
                $success &= !empty($reponse);
                //Historique
                $success &= $this->Historique->enregistre($acte_id, $user_id, "Envoi du projet au parapheur");
                $acte['Deliberation']['date_envoi_signature'] = date("Y-m-d H:i:s", strtotime("now"));
                $acte['Deliberation']['parapheur_id'] = $reponse;
                if (Configure::read('PARAPHEUR') == 'PASTELL')
                    $acte['Deliberation']['pastell_id'] = $reponse;
                $acte['Deliberation']['parapheur_cible'] = Configure::read('PARAPHEUR');
                $acte['Deliberation']['parapheur_etat'] = 1;
            }catch (Exception $e){
                $this->log($e->getMessage(),'debug');
                $success = false;
            }
        } else $success = false;

        if ($success && $this->save($acte)){
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * Ordonne la fusion et retourne le résultat sous forme de flux
     * @param int|string $id identifiant de la séance
     * @param string $modeltype type de fusion
     * @param int|string $modelTemplateId
     * @param string $format format du fichier de sortie
     * @return string flux du fichier généré
     */
    public function fusion($id, $modeltype = null, $modelTemplateId = null, $format = 'pdf') {
        $this->Behaviors->load('OdtFusion', array(
            'id' => $id,
            'fileNameSuffixe' => $id,
            'modelTemplateId' => $modelTemplateId,
            'modelOptions' => array('modelTypeName' => $modeltype)
        ));
        $this->odtFusion();
        $content = $this->getOdtFusionResult($format);
        $this->deleteOdtFusionResult();
        $this->Behaviors->unload('OdtFusion');
        return $content;
    }

    /**
     * Ordonne la fusion et retourne le résultat sous forme de flux
     * @param int|string $id identifiant de la séance
     * @param string $modeltype type de fusion
     * @param int|string $modelTemplateId
     * @param string $outputdir fichier vers lequel faire la fusion
     * @param string $format format du fichier de sortie
     * @return array [filename => content]
     */
    public function fusionToFile($id, $modeltype, $modelTemplateId = null, $outputdir = TMP, $format = 'pdf') {
        $this->Behaviors->load('OdtFusion', array(
            'id' => $id,
            'fileNameSuffixe' => $id,
            'modelTemplateId' => $modelTemplateId,
            'modelOptions' => array('modelTypeName' => $modeltype)
        ));
        $filename = $this->fusionName();
        $this->odtFusion();
        $content = $this->getOdtFusionResult($format);
        $this->deleteOdtFusionResult();
        $file = new File($outputdir . DS . $filename . '.' . $format, true);
        $file->write($content);
        $this->Behaviors->unload('OdtFusion');
        return $file->path;
    }
}
