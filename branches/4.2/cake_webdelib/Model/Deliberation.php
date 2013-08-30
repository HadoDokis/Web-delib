<?php
App::uses('DateFrench', 'Utility');
class Deliberation extends AppModel {

	/**
	 * Définition de constantes nommées pour le champ etat.
	 *
	 * Puisque les valeurs sont consécutives du circuit de traitement, on
	 * pourrait ajouter une valeur permettant de savoir si on a déjà dépassé
	 * l'étape de votes (ex.: 2.5) ?
	 */
	const refuse = -1;
	const enCoursRedaction = 0;
	const dansCircuit = 1;
	const valide = 2;
	const votePour = 3;
	const voteContre = 4;
	const envoyeCtrlLegalite = 5;

	var $name = 'Deliberation';

	var $validate = array( 'objet'     => array(
			array( 'rule'    => 'notEmpty',
					'message' => 'L\'objet est obligatoire')),
			'typeacte_id' => array(
					array( 'rule'    => array('canSaveNature', 'notEmpty'),
						'message' => "Type d'acte invalide")),
                        'theme_id' => array(
					array( 'rule'    => array('notEmpty'),
						'message' => "Theme invalide")),
			'texte_projet_type'   => array(
					array('rule' => array('checkMimetype', 'texte_projet', array('application/vnd.oasis.opendocument.text')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'texte_synthese_type' => array(
					array('rule' => array('checkMimetype', 'texte_synthese',  array('application/vnd.oasis.opendocument.text')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'deliberation_type'   => array(
					array('rule' => array('checkMimetype', 'deliberation',  array('application/vnd.oasis.opendocument.text')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'debat_type'           => array(
					array('rule' => array('checkMimetype', 'debat',  array('application/vnd.oasis.opendocument.text')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'commission_type'      => array(
					array('rule' => array('checkMimetype', 'commission',  array('application/vnd.oasis.opendocument.text')),
							'message' => "Ce type de fichier n'est pas autorisé")));



	//dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
	var $belongsTo = array(
	/*                'Nomenclature'=>array(
	 'className'    => 'Nomenclature',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'num_pref'
         * ),
	*/
			'Service'=>array(
					'className'    => 'Service',
					'conditions'   => '',
					'order'        => '',
					'dependent'    => false,
					'foreignKey'   => 'service_id'
                            ),
			'Theme'=>array(
					'className'    => 'Theme',
					'conditions'   => '',
					'order'        => '',
					'dependent'    => false,
					'foreignKey'   => 'theme_id'
                            ),
			'Circuit'=>array(
					'className'    => 'Cakeflow.Circuit',
					'conditions'   => '',
					'order'        => '',
					'dependent'    => false,
					'foreignKey'   => 'circuit_id'
                            ),
			'Redacteur' =>array(
					'className'    => 'User',
					'conditions'   => '',
					'order'        => '',
					'dependent'    =>  true,
					'foreignKey'   => 'redacteur_id'
                            ),
			'Rapporteur'=> array(
					'className'    => 'Acteur',
					'conditions'   => '',
					'order'        => '',
					'dependent'    =>  true,
					'foreignKey'   => 'rapporteur_id'
                            ),
			'Typeacte'=> array(
					'className'    => 'Typeacte',
					'conditions'   => '',
					'order'        => '',
					'dependent'    =>  true,
					'foreignKey'   => 'typeacte_id'
                            )
	);

	var $hasMany = array(
			'TdtMessage' => array (
					'className'    => 'TdtMessage',
					'foreignKey'   => 'delib_id'
                            ),
			'Historique' =>array(
					'className'    => 'Historique',
					'foreignKey'   => 'delib_id'
                            ),
			'Traitement'=>array(
					'className'    => 'Cakeflow.Traitement',
					'foreignKey'   => 'target_id'
                            ),
			'Annex'=>array(	'className'    => 'Annex',
					'foreignKey'   => 'foreign_key',
                                        'order'        => array('Annex.id' => 'ASC'),
					'dependent'    => true
                            ),
			'Commentaire'=>array(
					'className'    => 'Commentaire',
					'foreignKey'   => 'delib_id'
                            ),
			'Listepresence'=>array(
					'className'    => 'Listepresence',
					'foreignKey'   => 'delib_id'
                            ),
			'Vote'=>array(
					'className'    => 'Vote',
					'foreignKey'   => 'delib_id'
                            ),
			'Infosup'=>array(
					'dependent' => true,
					'foreignKey' => 'foreign_key',
					'conditions' => array('Infosup.model' => 'Deliberation')
                            ),
			'Multidelib'=>array(
					'className'    => 'Deliberation',
					'foreignKey'   => 'parent_id',
					'order' => 'id ASC',
					'dependent' => false),
			'Deliberationseance' =>array(
					'className'    => 'Deliberationseance',
					'foreignKey'   => 'deliberation_id',
                                        'order'      => 'Deliberationseance.position ASC'
                         ),
                       'Deliberationtypeseance' =>array(
                                        'className'    => 'Deliberationtypeseance',
                                        'foreignKey'   => 'deliberation_id'
                           ),


                 );

	var $hasAndBelongsToMany = array(
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
	function estModifiable($delibId, $userId, $canEdit=false) {
		/* lecture en base */
		$delib = $this->find('first', array('conditions' => array('Deliberation.id' => $delibId),
				'recursive'  => '-1',
				'fields'     => array('etat', 'redacteur_id', 'signee')));
		if (empty($delib)) return false;
		if ($delib['Deliberation']['signee'] == 1)
			return false;

		/* traitement en fonction de l'état */
		switch($delib['Deliberation']['etat']) {
			case -1 :
			case 2 :
				$ret =  $canEdit;
				break;
			case 3 :
				$ret =  $canEdit;
				break;
			case 4 :
				$ret =  $canEdit;
				break;
			case 5 :
				$ret = false;
				break;
			case 0 :
				$ret = ($delib['Deliberation']['redacteur_id'] == $userId);
				break;
			case 1 :
				$ret = ($this->Traitement->positionTrigger($userId, $delibId) > -1);
				break;
		}

		return $ret;
	}
	/*
	 function saveSeances($delib_id, $seances) {
	foreach($seances as $key=>$seance_id) {
	$this->Seance->DeliberationsSeance->deliberation_id = $delib_id;
	$this->Seance->DeliberationsSeance->seance_id = $seance_id;
	if (!$this->Seance->DeliberationsSeance->save())
		die('toto');
	}
	}
	*/

	/*
	 * retourne le libellé correspondant à l'état $etat des projets et délibérations
	* si $codesSpeciaux = true, retourne les libellés avec les codes spéciaux des accents
	* si $codesSpeciaux = false, retourne les libellés sans les accents (listes)
	*/
	function libelleEtat($etat, $codesSpeciaux=false) {
		switch($etat) {
			case -1 :
				return $codesSpeciaux ? 'Version&eacute;' : 'Versionné';
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

	function changeClassification($delib_id, $classification){
		$this->id = $delib_id;
		$this->saveField('num_pref', $classification);
	}

	function changeDateAR($delib_id, $dateAR){
		$this->id = $delib_id;
		$this->saveField('dateAR', $dateAR);
	}

	function getModelId($delib_id, $seance_id) {
		$this->Seance->Behaviors->attach('Containable');
		$data = $this->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
				'recursive'  => -1,
				'fields'     => array('Deliberation.id', 'Deliberation.etat')));
		$seance = $this->Seance->find('first', array(
				'conditions' => array('Seance.id' => $seance_id),
				'fields'     => array('Seance.id'),
				'contain'    => array('Typeseance.modelprojet_id', 'Typeseance.modeldeliberation_id') ));

		if (!empty($seance)){
			if ($data['Deliberation']['etat']<3)
				return $seance['Typeseance']['modelprojet_id'];
			else
				return $seance['Typeseance']['modeldeliberation_id'];
		}
		else {
			return 1;
		}
	}

	function refusDossier($id) {
		// lecture en base de données
		$this->Behaviors->attach('Containable');
		$delib=$this->find('first', array(
				'contain' => array('Annex', 'Infosup', 'Typeseance'),
				'conditions' => array('Deliberation.id'=>$id)));

		// maj de l'etat de la delib dans la table deliberations
                $this->id = $id;
		$this->saveField('etat', '-1');

		// création de la nouvelle version
		$delib['Deliberation']['id']=null;
		$delib['Deliberation']['created']=null;
		$delib['Deliberation']['modified']=null;
		$delib['Deliberation']['etat']=0;
		$delib['Deliberation']['anterieure_id']=$id;
                $this->create();
                $this->save($delib);
                $delib_id = $this->getLastInsertID();
	        $this->copyPositionsDelibs($id, $delib_id);

		// copie des annexes du projet refusé vers le nouveau projet
		$annexes = $delib['Annex'];
		foreach($annexes as $annexe) {
			$tmp['Annex']= $annexe;
			$tmp['Annex']['id']=null;
			$tmp['Annex']['foreign_key']= $delib_id;
			$this->Annex->save( $tmp, false);
		}

		// copie des infos supplémentaires du projet refusé vers le nouveau projet
		$infoSups = $delib['Infosup'];
		foreach($infoSups as $infoSup) {
			$infoSup['id'] = null;
			$infoSup['foreign_key'] = $delib_id;
			$infoSup['model'] = 'Deliberation';
			$this->Infosup->save($infoSup, false);
		}

		// copie des délibérations rattachées vers le nouveau projet
		$delibRattachees = $this->find('all', array(
				'contain' => array('Annex'),
				'conditions'=>array('Deliberation.parent_id'=>$id)));
		foreach($delibRattachees as $delibRattachee) {
			// maj de l'etat de la delib dans la table deliberations
			$delibRattachee['Deliberation']['etat']=-1; //etat -1 : refuse
			// Retour de la position a 0 pour ne pas qu'il y ait de confusion
			//$delibRattachee['Deliberation']['position']=0;
			$this->save($delibRattachee['Deliberation']);

			// création de la nouvelle version
			$this->create();
			$delibRattachee['Deliberation']['id']=null;
			$delibRattachee['Deliberation']['parent_id']=$delib_id;
			$delibRattachee['Deliberation']['etat']=0;
			$delibRattachee['Deliberation']['anterieure_id']=null;
			$delibRattachee['Deliberation']['date_envoi']=null;
			//		$delibRattachee['Deliberation']['circuit_id']=0;
			$delibRattachee['Deliberation']['created']=date('Y-m-d H:i:s', time());
			$delibRattachee['Deliberation']['modified']=date('Y-m-d H:i:s', time());
			$this->save($delibRattachee['Deliberation']);
			$delibRattachee_id = $this->getLastInsertID();

			// copie des annexes du projet refusé vers le nouveau projet
			$annexes = $delibRattachee['Annex'];
			foreach($annexes as $annexe) {
				$tmp['Annex']= $annexe;
				$tmp['Annex']['id']=null;
				$tmp['Annex']['foreign_key']= $delibRattachee_id ;
				$this->Annex->save( $tmp, false);
			}
		}
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
     * Permet de générer un seul document à partir des projets retournés par
     * une recherche multi-critères.
     *
     * @see (utilise)
     * 	- ConversionComponent::convertirFichier()
     * 	- Deliberation::makeBalisesProjet()
     * 	- Modelprojet::find()
     *  - Seance::makeBalise()
     *
     * @see (utilisé par)
     * 	- DeliberationController::mesProjetsRecherche()
     * 	- DeliberationController::tousLesProjetsRecherche()
     * 	- DeliberationController::traitementLot()
     *
     * @param array $projets Un ensemble de projets (résultats de find CakePHP).
     * @param integer $model_id La clé primaire du modèle de document à utiliser (classe Model)
     * @param integer $format Le format de sortie, @see Pages/format.ctp array( 0=>'pdf', 1=>'odt' )
     * @param array $multiSeances La liste des clés primaires des séances.
     * @param array $conditions Utlisé comme conditions pour les appels à Seance::makeBalise()
     */
    public function genererRecherche($projets, $model_id = 1, $format = 0, $multiSeances = array(), $conditions = array()) {
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');

        include_once (ROOT . DS . APP_DIR . DS . 'Controller/Component/ConversionComponent.php');
        $this->Conversion = new ConversionComponent;

        if ($format == 0) {
            $sMimeType = "application/pdf";
            $format = "pdf";
        } elseif ($format == 1) {
            $sMimeType = "application/vnd.oasis.opendocument.text";
            $format = "odt";
        }
        $dyn_path = "/files/generee/deliberations/";
        $nomFichier = "recherche";
        $path = WEBROOT_PATH . $dyn_path;
        if (!file_exists($path))
            mkdir($path);

        $content = $this->Seance->Typeseance->Modelprojet->find('first', array('conditions' => array('id' => $model_id),
            'fields' => array('content'),
            'recursive' => -1));
        $oTemplate = new GDO_ContentType("", "modele.odt", "application/vnd.oasis.opendocument.text", "binary", $content['Modelprojet']['content']);
        $oMainPart = new GDO_PartType();

        if (empty($multiSeances)) {
            $nbProjets = count($projets);
            if ($nbProjets > 1) {
                $blocProjets = new GDO_IterationType("Projets");
            }
            foreach ($projets as $projet) {
                $oDevPart = new GDO_PartType();
                $this->makeBalisesProjet($projet, $oDevPart);
                if ($nbProjets > 1)
                    $blocProjets->addPart($oDevPart);
            }
            if ($nbProjets > 1)
                $oMainPart->addElement($blocProjets);
            else
                $oMainPart = $oDevPart;
        }
        else {
            $seances = new GDO_IterationType("Seances");
            foreach ($multiSeances as $seance_id)
                $seances->addPart($this->Seance->makeBalise($seance_id, null, true, $conditions));
            $oMainPart->addElement($seances);
        }

        $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
        $oFusion->process();

        $oFusion->SendContentToFile($path . $nomFichier . ".odt");
        $content = $this->Conversion->convertirFichier($path . $nomFichier . ".odt", $format);

        header("Content-type: $sMimeType");
        header("Content-Disposition: attachment; filename=recherche.$format");
        die($content);
    }

    /**
     * Complète un objet GDO_PartType avec les informations d'un projet de
     * délibération ou d'une délibération.
     *
     * Le répertoire "racine" est app/webroot/files/generee/projet/$delib_id,
     * il existe un sous-répertoire "annexes".
     *
     * Variables gedooo:
     * 	Si l'on prend en compte la séance
     * 		- nombre_seance/count(Deliberationseance)/text
     * 		- identifiant_projet/Deliberation.id/text
     * 		Si la délibération n'est passée que dans une seule séance
     * 			- position_projet
     * 			- @see Seance::makeBalise()
     * 			- Seances.{n}... (@see Seance::makeBalise())
     * 		Sinon
     * 			- @see Seance::makeBalise() (pour la délibérante)
     * 			- Seances.{n}... (@see Seance::makeBalise())

     * 	- position_projet/Deliberationseance.position/text
     * 	- titre_projet/Deliberation.titre/lines
     * 	- objet_projet/Deliberation.objet/lines
     * 	- libelle_projet/Deliberation.objet/lines
     * 	- objet_delib/Deliberation.objet_delib/lines
     * 	- libelle_delib/Deliberation.objet_delib/lines
     * 	- identifiant_projet/Deliberation.id/text
     * 	- etat_projet/Deliberation.etat/text
     * 	- numero_deliberation/Deliberation.num_delib/text
     * 	- numero_acte/Deliberation.num_delib/text
     * 	- classification_deliberation/Deliberation.num_pref/text
     * 	- date_envoi_signature/Date::frDate(Deliberation.date_envoi_signature)/date

     * 	S'il s'agit d'un acte adopté
     * 		- acte_adopte/1/text
     * 	Sinon
     * 		- acte_adopte/0/text
     * 		- nombre_pour/Deliberation.vote_nb_oui/text
     * 		- nombre_abstention/Deliberation.vote_nb_abstention/text
     * 		- nombre_contre/Deliberation.vote_nb_non/text
     * 		- nombre_sans_participation/Deliberation.vote_nb_retrait/text

     * 	- nombre_votant/Deliberation.vote_nb_oui + Deliberation.vote_nb_abstention + Deliberation.vote_nb_non/text
     * 	- date_reception/Deliberation.dateAR/text
     * 	- commentaire_vote/Deliberation.vote_commentaire/lines

     * 	Pour tous les commentaires
     * 		S'il s'agit d'un commentaire_auto
     * 			- AvisCommission.{n}.avis/Commentaire.texte/text
     * 		Sinon
     * 			- Commentaires.{n}.texte_commentaire/Commentaire.texte/text

     * 	Pour toutes les entrées de Deliberationseance concernant notre délibération
     * 		Si l'action du type de séance lié est un avis
     * 			- AvisProjet.{n}
     * 				* avis/A reçu un avis favorable/défavorable (Deliberationseance.avis)  en Typeseance.libelle du Date::frenchDate(Seance.date)/text
     * 				* avis_favorable/Deliberationseance.avis/text
     * 				* commentaire/Deliberationseance.commentaire/lines

     * 	Pour chacune des entrées d'historiques de la délibération
     * 		- Historique.{n}.log/Historique.commentaire/text

     * 	Pour chacune des Infosup de la délibération
     * 		On ajoute l'information dans la partie principale

     * 	Si on est une délibération parente d'autres délibérations (mode multi-délibérations)
     * 		Pour chacune des délibérations dont on est le parent
     * 			- Deliberations.{n}.libelle_multi_delib/Multidelib.objet/text
     * 			- Deliberations.{n}.id_multi_delib/Multidelib.id/text
     * 	Sinon
     * 		- Deliberations.0.libelle_multi_delib//text
     * 		- Deliberations.0.id_multi_delib//text

     * 	Si GENERER_DOC_SIMPLE
     * 		- texte_projet/texte_projet.odt/application/vnd.oasis.opendocument.text/binary/Deliberation.texte_projet (GDO_ContentType, conversion en image puis en odt)
     * 		- note_synthese/texte_synthese.odt/application/vnd.oasis.opendocument.text/binary/Deliberation.texte_synthese (GDO_ContentType, conversion en image puis en odt)
     * 		- texte_deliberation/deliberation.odt/application/vnd.oasis.opendocument.text/binary/Deliberation.deliberation (GDO_ContentType, conversion en image puis en odt)
     * 		- debat_deliberation/debat.odt/application/vnd.oasis.opendocument.text/binary/Deliberation.debat (GDO_ContentType, conversion en image puis en odt)
     * 		- debat_commission/commission.odt/application/vnd.oasis.opendocument.text/binary/Deliberation.commission (GDO_ContentType, conversion en image puis en odt)
     * 	Sinon, la même chose, sans la conversion (on prend le contenu du champ)

     * 	- nombre_annexe/count(annexes de la délibération)/text

     * 	Pour chacune des annexes de la délibération
     * 		- Annexes.{n}.titre_annexe/Annex.titre/text
     * 		- Annexes.{n}.nom_fichier/Annex.filename/text
     * 		Si le type de fichier annexe est odt
     * 			- Annexes.{n}.fichier/Annex.filename/mime::odt/binary/Annex.data (GDO_ContentType)
     * 		Sinon si le type de fichier annexe est pdf et que Annex.data n'est pas vide
     * 			- Annexes.{n}.fichier/Annex.filename/mime::pdf/binary/Annex.data (GDO_ContentType)

     * 	Pour chacun des acteurs de la Listepresence de la délibération
     * 		Si l'acteur était présent et qu'il n'a pas de suppléant
     * 			L'acteur présent est l'acteur réel
     * 		Sinon si l'acteur était présent et qu'il a un suppléant
     * 			L'acteur présent est le suppléant
     * 		Sinon si l'acteur était absent et qu'il a un mandataire
     * 			L'acteur remplacé est composé par l'acteur réel (suffixe _acteur) et le mandataire (suffixe _mandate)
     * 		Sinon si l'acteur était absent et qu'il n'a pas de mandataire
     * 			L'acteur absent est l'acteur réel

     * 	À partir de l'ensemble des votes de la délibération, on crée les itérations suivantes (@see Deliberation::_makeBlocsActeurs()):
     * 	- ActeursPresents.{n}...
     * 	- ActeursAbsents.{n}...
     * 	- ActeursMandates.{n}...
     * 	- ActeursContre.{n}...
     * 	- ActeursPour.{n}...
     * 	- ActeursAbstention.{n}...
     * 	- ActeursSansParticipation.{n}...
     *
     * @see Configure
     * 	- boolean GENERER_DOC_SIMPLE
     * 		@see app/Config/webdelib.inc
     *
     * @see (appellée par):
     * 	- ModelsController::generer()
     * 	- SeancesController::_generer()
     * 	- Deliberation::genererRecherche()
     * 	- Seance::makeBalise()
     *
     * @see (appelle):
     * 	- ConversionComponent::convertirFichier()
     * 	- DateComponent::frenchDate()
     * 	- GedoooComponent::createFile()
     * 	- GedoooComponent::checkPath()
     * 	- Annex::find()
     * 	- Commentaire::find()
     * 	- Deliberation::_makeBlocsActeurs()
     * 	- Deliberation::_url2pathImage()
     * 	- Deliberation::getPosition()
     * 	- Deliberation::getSeancesid()
     * 	- Deliberationseance::find()
     * 	- Historique::find()
     * 	- Infosup::addField()
     * 	- Infosup::find()
     * 	- Infosupdef:::find()
     * 	- Deliberation::find()
     * 	- Listepresence::find()
     * 	- Rapporteur::makeBalise()
     * 	- Redacteur::makeBalise()
     * 	- Seance::getSeanceDeliberante()
     * 	- Seance::makeBalise()
     * 	- Service::makeBalise()
     * 	- Theme::makeBalise()
     * 	- Vote::find()
     *
     * @param array $delib
     * @param GDO_PartType $oMainPart
     * @param boolean $exceptSeance
     * @param integer $seance_id
     */
    public function makeBalisesProjet( $delib, &$oMainPart, $exceptSeance = false, $seance_id = null ) {
        include_once (ROOT . DS . APP_DIR . DS . 'Controller/Component/GedoooComponent.php');
        include_once (ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
        include_once (ROOT . DS . APP_DIR . DS . 'Controller/Component/ConversionComponent.php');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
        $isDelib = ( $delib['Deliberation']['etat'] >= self::votePour ); // Signifie déjà voté
        $u = new GDO_Utility();

        $this->Conversion = new ConversionComponent;
        $this->Date = new DateComponent;
        $this->Gedooo = new GedoooComponent;

        $dyn_path = "/files/generee/projet/" . $delib['Deliberation']['id'] . "/";
        $path = WEBROOT_PATH . $dyn_path;
        // Itération sur les séances
        if (!$exceptSeance) {
            $delibseances = $this->getSeancesid($delib['Deliberation']['id']);
            $oMainPart->addElement(new GDO_FieldType('nombre_seance', count($delibseances), 'text'));
            $oMainPart->addElement(new GDO_FieldType('identifiant_projet', $delib['Deliberation']['id'], 'text'));
            if (count($delibseances) == 1) {
                $this->Seance->makeBalise($delibseances[0], $oMainPart);
                $position = $this->getPosition($delib['Deliberation']['id'], $delibseances[0]);
                $oMainPart->addElement(new GDO_FieldType('position_projet', $position, 'text'));
                $seances = new GDO_IterationType("Seances");
                $seances->addPart($this->Seance->makeBalise($delibseances[0]));
                $oMainPart->addElement($seances);
            } elseif (count($delibseances) > 1) {
                $seance_deliberante = $this->Seance->getSeanceDeliberante($delibseances);

                $this->Seance->makeBalise($seance_deliberante, $oMainPart);

                $seances = new GDO_IterationType("Seances");
                foreach ($delibseances as $key => $delibseances_seance_id) {
                    $seances->addPart($this->Seance->makeBalise($delibseances_seance_id));
                }
                $oMainPart->addElement($seances);
            }
        }
        /* $this->log('$seance_id->'.$seance_id);
          if ($seance_id != null) {
          $position = $this->getPosition($delib['Deliberation']['id'], $seance_id);
          $this->log($delib['Deliberation']['id'].', '.$seance_id.'=>'.$position);
          $oMainPart->addElement(new GDO_FieldType('position_projet', $delib['Deliberationseance']['position'], 'text'));
          } */
        $oMainPart->addElement(new GDO_FieldType('position_projet', (isset($delib['Deliberationseance']) && isset($delib['Deliberationseance']['position']) ? $delib['Deliberationseance']['position'] : ''), 'text'));
        $oMainPart->addElement(new GDO_FieldType('titre_projet', ($delib['Deliberation']['titre']), 'lines'));
        $oMainPart->addElement(new GDO_FieldType('objet_projet', ($delib['Deliberation']['objet']), 'lines'));
        $oMainPart->addElement(new GDO_FieldType('libelle_projet', ($delib['Deliberation']['objet']), 'lines'));
        $oMainPart->addElement(new GDO_FieldType('objet_delib', ($delib['Deliberation']['objet_delib']), 'lines'));
        $oMainPart->addElement(new GDO_FieldType('libelle_delib', ($delib['Deliberation']['objet_delib']), 'lines'));
        $oMainPart->addElement(new GDO_FieldType('identifiant_projet', $delib['Deliberation']['id'], 'text'));
        $oMainPart->addElement(new GDO_FieldType('etat_projet', $delib['Deliberation']['etat'], 'text'));
        $oMainPart->addElement(new GDO_FieldType('numero_deliberation', $delib['Deliberation']['num_delib'], 'text'));
        $oMainPart->addElement(new GDO_FieldType('numero_acte', $delib['Deliberation']['num_delib'], 'text'));
        $oMainPart->addElement(new GDO_FieldType('classification_deliberation', $delib['Deliberation']['num_pref'], 'text'));
        $oMainPart->addElement(new GDO_FieldType("date_envoi_signature", $this->Date->frDate($delib['Deliberation']['date_envoi_signature']), 'date'));

        $this->Service->makeBalise($oMainPart, $delib['Deliberation']['service_id']);
        // Informations sur la nature
//	$this->Typeacte->makeBalise($oMainPart, $delib['Deliberation']['typeacte_id']);
        // Informations sur le thème
        $this->Theme->makeBalise($oMainPart, $delib['Deliberation']['theme_id']);
        // Informations sur le rapporteur
        $this->Rapporteur->makeBalise($oMainPart, $delib['Deliberation']['rapporteur_id']);
        // Informations sur le rédacteur
        $this->Redacteur->makeBalise($oMainPart, $delib['Deliberation']['redacteur_id']);

        // Informations sur la délibération

        $nb_votant = $delib['Deliberation']['vote_nb_oui'] + $delib['Deliberation']['vote_nb_abstention'] + $delib['Deliberation']['vote_nb_non'];
        // S'il s'agit d'un acte
        if (($delib['Deliberation']['etat'] == self::votePour ) && ($delib['Deliberation']['vote_nb_oui'] == 0 ))
            $oMainPart->addElement(new GDO_FieldType('acte_adopte', '1', 'text'));
        // S'il s'agit d'une délibération
        else {
            $oMainPart->addElement(new GDO_FieldType('acte_adopte', '0', 'text'));
            $oMainPart->addElement(new GDO_FieldType('nombre_pour', ($delib['Deliberation']['vote_nb_oui']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('nombre_abstention', ( $delib['Deliberation']['vote_nb_abstention']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('nombre_contre', ($delib['Deliberation']['vote_nb_non']), 'text'));
            $oMainPart->addElement(new GDO_FieldType('nombre_sans_participation', ( $delib['Deliberation']['vote_nb_retrait']), 'text'));
        }
        $oMainPart->addElement(new GDO_FieldType('nombre_votant', $nb_votant, 'text'));
        $oMainPart->addElement(new GDO_FieldType('date_reception', ($delib['Deliberation']['dateAR']), 'text'));
        $oMainPart->addElement(new GDO_FieldType('commentaire_vote', $delib['Deliberation']['vote_commentaire'], 'lines'));

        $coms = $this->Commentaire->find('all', array('conditions' => array('Commentaire.delib_id' => $delib['Deliberation']['id']),
            'fields' => array('texte', 'commentaire_auto'),
            'recursive' => -1));

        if (!empty($coms)) {
            $commentaires = new GDO_IterationType("Commentaires");
            foreach ($coms as $commentaire) {
                $oDevPart = new GDO_PartType();
                if ($commentaire['Commentaire']['commentaire_auto'] == 0) {
                    $oDevPart->addElement(new GDO_FieldType("texte_commentaire", ($commentaire['Commentaire']['texte']), "text"));
                    $commentaires->addPart($oDevPart);
                }
            }
            @$oMainPart->addElement($commentaires);

            $avisCommission = new GDO_IterationType("AvisCommission");
            foreach ($coms as $commentaire) {
                $oDevPart = new GDO_PartType();
                if ($commentaire['Commentaire']['commentaire_auto'] == 1) {
                    $oDevPart->addElement(new GDO_FieldType("avis", ($commentaire['Commentaire']['texte']), "text"));
                    $avisCommission->addPart($oDevPart);
                }
            }
            @$oMainPart->addElement($avisCommission);
        }
        $this->Deliberationseance->Behaviors->attach('Containable');
        $avisSeances = $this->Deliberationseance->find('all', array(
            'conditions' => array('Deliberationseance.deliberation_id' => $delib['Deliberation']['id']),
            'contain' => array('Seance.date', 'Seance.Typeseance')));
        include_once (ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
        $this->Date = new DateComponent;


        if (!empty($avisSeances)) {
            $aviss = new GDO_IterationType("AvisProjet");
            foreach ($avisSeances as $avisSeance) {
                // Les avis sont donnés sur les projets en séance non délibérante
                if ($avisSeance['Seance']['Typeseance']['action'] == Typeseance::actionAvis) {
                    $oDevPart = new GDO_PartType();
                    $typeseance = $avisSeance['Seance']['Typeseance']['libelle'];
                    $dateSeance = $this->Date->frenchDate(strtotime($avisSeance['Seance']['date']));
                    if ($avisSeance['Deliberationseance']['avis'] == 1) {
                        $message = "A reçu un avis favorable  en $typeseance du $dateSeance";
                    } else {
                        $message = "A reçu un avis défavorable  en $typeseance du $dateSeance";
                    }
                    $oDevPart->addElement(new GDO_FieldType("avis", $message, "text"));
                    $oDevPart->addElement(new GDO_FieldType("avis_favorable", $avisSeance['Deliberationseance']['avis'], "text"));
                    $oDevPart->addElement(new GDO_FieldType("commentaire", ($avisSeance['Deliberationseance']['commentaire']), "lines"));
                    $aviss->addPart($oDevPart);
                }
            }
            @$oMainPart->addElement($aviss);
        }

        $historik = $this->Historique->find('all', array('conditions' => array('Historique.delib_id' => $delib['Deliberation']['id']),
            'fields' => array('commentaire'),
            'recursive' => -1));

        if (!empty($historik)) {
            @$historique = new GDO_IterationType("Historique");
            foreach ($historik as $histo) {
                $oDevPart = new GDO_PartType();
                $oDevPart->addElement(new GDO_FieldType("log", ($histo['Historique']['commentaire']), "text"));
                $historique->addPart($oDevPart);
            }
            @$oMainPart->addElement($historique);
        }

        $infosup = $this->Infosup->find('all', array('conditions' => array('Infosup.foreign_key' => $delib['Deliberation']['id'],
                'Infosup.model' => 'Deliberation'),
            'recursive' => -1));
        if (!empty($infosup)) {
            foreach ($infosup as $champs)
                $oMainPart->addElement($this->Infosup->addField($champs['Infosup'], $delib['Deliberation']['id'], 'Deliberation'));
        } else {
            $defs = $this->Infosup->Infosupdef->find('all', array('conditions' => array('model' => 'Deliberation'), 'recursive' => -1));
            foreach ($defs as $def) {
                $oMainPart->addElement(new GDO_FieldType($def['Infosupdef']['code'], (' '), 'text'));
            }
        }

        $multidelibs = $this->find('first', array('conditions' => array('Deliberation.parent_id' => $delib['Deliberation']['id']),
            'fields' => array('id', 'objet')));
        @$Multi = new GDO_IterationType("Deliberations");
        if (!empty($multidelibs['Multidelib'])) {
            foreach ($multidelibs['Multidelib'] as $multidelib) {
                $oDevPart = new GDO_PartType();
                $oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", ($multidelib['objet']), "text"));
                $oDevPart->addElement(new GDO_FieldType("id_multi_delib", ($multidelib['id']), "text"));
                $Multi->addPart($oDevPart);
            }
        } else {
            $oDevPart = new GDO_PartType();
            $oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", " ", "text"));
            $oDevPart->addElement(new GDO_FieldType("id_multi_delib", " ", "text"));
            $Multi->addPart($oDevPart);
        }
        @$oMainPart->addElement($Multi);

        if (Configure::read('GENERER_DOC_SIMPLE')) {
            if (isset($delib['Deliberation']['texte_projet'])) {
                $filename = $path . "texte_projet.html";
                $delib['Deliberation']['texte_projet'] = $this->_url2pathImage($delib['Deliberation']['texte_projet']);
                $this->Gedooo->createFile($path, "texte_projet.html", $delib['Deliberation']['texte_projet']);
                $content = $this->Conversion->convertirFichier($filename, "odt");
                $oMainPart->addElement(new GDO_ContentType('texte_projet', 'texte_projet.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("texte_projet", "", "text"));
            if (isset($delib['Deliberation']['texte_synthese'])) {
                $filename = $path . "texte_synthese.html";
                $this->Gedooo->createFile($path, "texte_synthese.html", $delib['Deliberation']['texte_synthese']);
                $content = $this->Conversion->convertirFichier($filename, "odt");
                $oMainPart->addElement(new GDO_ContentType('note_synthese', 'texte_synthese.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("note_synthese", "", "text"));
            if (isset($delib['Deliberation']['deliberation'])) {
                $filename = $path . "texte_deliberation.html";
                $this->Gedooo->createFile($path, "texte_deliberation.html", $delib['Deliberation']['deliberation']);
                $content = $this->Conversion->convertirFichier($filename, "odt");
                $oMainPart->addElement(new GDO_ContentType('texte_deliberation', 'deliberation.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("texte_deliberation", "", "text"));
            if (isset($delib['Deliberation']['debat'])) {
                $filename = $path . "debat_deliberation.html";
                $this->Gedooo->createFile($path, "debat_deliberation.html", $delib['Deliberation']['debat']);
                $content = $this->Conversion->convertirFichier($filename, "odt");
                $oMainPart->addElement(new GDO_ContentType('debat_deliberation', 'debat.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("debat_deliberation", "", "text"));
            if (isset($delib['Deliberation']['commission'])) {
                $filename = $path . "commission.html";
                $this->Gedooo->createFile($path, "commission.html", $delib['Deliberation']['commission']);
                $content = $this->Conversion->convertirFichier($filename, "odt");
                $oMainPart->addElement(new GDO_ContentType('debat_commission', 'commission.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("debat_commission", "", "text"));
        }
        else {

            if (!$this->Gedooo->checkPath($path))
                die("Webdelib ne peut pas ecrire dans le repertoire : $path");

            $urlWebroot = 'http://' . $_SERVER['HTTP_HOST'] . $dyn_path;
            if (!empty($delib['Deliberation']['texte_projet'])) {
                $oMainPart->addElement(new GDO_ContentType('texte_projet', 'text_projet.odt', 'application/vnd.oasis.opendocument.text', 'binary', $delib['Deliberation']['texte_projet']));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("texte_projet", "", "text"));
            if (!empty($delib['Deliberation']['deliberation'])) {
                $oMainPart->addElement(new GDO_ContentType('texte_deliberation', 'td.odt', 'application/vnd.oasis.opendocument.text', 'binary', $delib['Deliberation']['deliberation']));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("texte_deliberation", "", "text"));
            if (!empty($delib['Deliberation']['texte_synthese'])) {
                $oMainPart->addElement(new GDO_ContentType('note_synthese', 'ns.odt', 'application/vnd.oasis.opendocument.text', 'binary', $delib['Deliberation']['texte_synthese']));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("note_synthese", "", "text"));
            if (!empty($delib['Deliberation']['debat'])) {
                $oMainPart->addElement(new GDO_ContentType('debat_deliberation', 'debat.odt', 'application/vnd.oasis.opendocument.text', 'binary', $delib['Deliberation']['debat']));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("debat_deliberation", "", "text"));
            if (!empty($delib['Deliberation']['commission'])) {
                $oMainPart->addElement(new GDO_ContentType('debat_commission', 'debat_commission.odt', 'application/vnd.oasis.opendocument.text', 'binary', $delib['Deliberation']['commission']));
            }
            else
                $oMainPart->addElement(new GDO_FieldType("debat_commission", "", "text"));
        }

        // $annexe_ids = $this->Annex->getAnnexesFromDelibId($delib['Deliberation']['id'], 0, 1);
        $annexe_ids = array();
        $anns = $this->Annex->find('all', array('conditions' => array(
                'Annex.foreign_key' => $delib['Deliberation']['id']),
            'fields' => array('Annex.id', 'Annex.filetype'),
            'order' => array('Annex.id' => 'ASC'),
            'recursive' => -1));
        foreach ($anns as $ann)
            $annexe_ids[] = $ann['Annex']['id'];
        $oMainPart->addElement(new GDO_FieldType('nombre_annexe', count($annexe_ids), 'text'));

        @$annexes = new GDO_IterationType("Annexes");
        foreach ($annexe_ids as $key => $annexe_id) {
            unset($annexe);
            $oDevPart = new GDO_PartType();
            $annexe = $this->Annex->find('first', array('conditions' => array('Annex.id' => $annexe_id),
                'recursive' => -1));
            $oDevPart->addElement(new GDO_FieldType('titre_annexe', $annexe['Annex']['titre'], 'text'));
            if (($annexe['Annex']['filetype'] == "application/vnd.oasis.opendocument.text")) {
                $oDevPart->addElement(new GDO_FieldType('nom_fichier', $annexe['Annex']['filename'], 'text'));
                $oDevPart->addElement(new GDO_ContentType('fichier', $annexe['Annex']['filename'], 'application/vnd.oasis.opendocument.text', 'binary', $annexe['Annex']['data']));
                $annexes->addPart($oDevPart);
                //file_put_contents('/tmp/Annexe_'.$annexe_id.'.odt', $annexe['Annex']['data']);
            } elseif (($annexe['Annex']['filetype'] == "application/pdf") && !empty($annexe['Annex']['data'])) {
                $oDevPart->addElement(new GDO_FieldType('nom_fichier', $annexe['Annex']['filename'], 'text'));
                $oDevPart->addElement(new GDO_ContentType('fichier', $annexe['Annex']['filename'], 'application/vnd.oasis.opendocument.text', 'binary', $annexe['Annex']['data']));
                $annexes->addPart($oDevPart);
                //file_put_contents('/tmp/Annexe_'.$annexe_id.'.odt', $annexe['Annex']['data']);
            }
        }
        @$oMainPart->addElement($annexes);

        //LISTE DES PRESENCES...
        $this->Listepresence->Behaviors->attach('Containable');
        $this->Vote->Behaviors->attach('Containable');
        $acteurs_presents = array();
        $acteurs_absents = array();
        $acteurs_remplaces = array();
        $acteurs_contre = array();
        $acteurs_pour = array();
        $acteurs_abstention = array();
        $acteurs_sans_participation = array();

        $acteurs = $this->Listepresence->find('all', array('conditions' => array("delib_id" => $delib['Deliberation']['id']),
            'contain' => array('Acteur', 'Mandataire', 'Suppleant'),
            'order' => 'Acteur.position ASC'));
        if (!empty($acteurs)) {
            foreach ($acteurs as $acteur) {
                $aActeur = array('nom_acteur' => $acteur['Acteur']['nom'],
                    'prenom_acteur' => $acteur['Acteur']['prenom'],
                    'salutation_acteur' => $acteur['Acteur']['salutation'],
                    'titre_acteur' => $acteur['Acteur']['titre'],
                    'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                    'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                    'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                    'cp_acteur' => $acteur['Acteur']['cp'],
                    'ville_acteur' => $acteur['Acteur']['ville'],
                    'email_acteur' => $acteur['Acteur']['email'],
                    'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                    'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                    'note_acteur' => $acteur['Acteur']['note']);

                if ($acteur['Listepresence']['present'] == true && empty($acteur['Listepresence']['suppleant_id'])) {
                    $acteurs_presents[] = $aActeur;
                } elseif (($acteur['Listepresence']['present'] == true) && !empty($acteur['Listepresence']['suppleant_id'])) {
                    $aSuppleant = array('nom_acteur' => $acteur['Suppleant']['nom'],
                        'prenom_acteur' => $acteur['Suppleant']['prenom'],
                        'salutation_acteur' => $acteur['Suppleant']['salutation'],
                        'titre_acteur' => $acteur['Suppleant']['titre'],
                        'date_naissance_acteur' => $acteur['Suppleant']['date_naissance'],
                        'adresse1_acteur' => $acteur['Suppleant']['adresse1'],
                        'adresse2_acteur' => $acteur['Suppleant']['adresse2'],
                        'cp_acteur' => $acteur['Suppleant']['cp'],
                        'ville_acteur' => $acteur['Suppleant']['ville'],
                        'email_acteur' => $acteur['Suppleant']['email'],
                        'telfixe_acteur' => $acteur['Suppleant']['telfixe'],
                        'telmobile_acteur' => $acteur['Suppleant']['telmobile'],
                        'note_acteur' => $acteur['Suppleant']['note']);
                    $acteurs_presents[] = $aSuppleant;
                } elseif (($acteur['Listepresence']['present'] == false) && !empty($acteur['Listepresence']['mandataire'])) {
                    $acteur_mandataire = array(
                        'nom_mandate' => $acteur['Mandataire']['nom'],
                        'prenom_mandate' => $acteur['Mandataire']['prenom'],
                        'salutation_mandate' => $acteur['Mandataire']['salutation'],
                        'titre_mandate' => $acteur['Mandataire']['titre'],
                        'date_naissance_mandate' => $acteur['Mandataire']['date_naissance'],
                        'adresse1_mandate' => $acteur['Mandataire']['adresse1'],
                        'adresse2_mandate' => $acteur['Mandataire']['adresse2'],
                        'cp_mandate' => $acteur['Mandataire']['cp'],
                        'ville_mandate' => $acteur['Mandataire']['ville'],
                        'email_mandate' => $acteur['Mandataire']['email'],
                        'telfixe_mandate' => $acteur['Mandataire']['telfixe'],
                        'telmobile_mandate' => $acteur['Mandataire']['telmobile'],
                        'note_mandate' => $acteur['Mandataire']['note']);
                    $acteurs_remplaces[] = Hash::merge($aActeur, $acteur_mandataire);
                } elseif (($acteur['Listepresence']['present'] == false) && empty($acteur['Listepresence']['mandataire'])) {
                    $acteurs_absents[] = $aActeur;
                }
            }
        }

        $acteurs = $this->Vote->find('all', array('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                "Vote.resultat" => Vote::voteContre),
            'contain' => array('Acteur'),
            'order' => 'Acteur.position ASC'));

        foreach ($acteurs as $acteur) {
            $acteurs_contre[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                'prenom_acteur' => $acteur['Acteur']['prenom'],
                'salutation_acteur' => $acteur['Acteur']['salutation'],
                'titre_acteur' => $acteur['Acteur']['titre'],
                'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                'cp_acteur' => $acteur['Acteur']['cp'],
                'ville_acteur' => $acteur['Acteur']['ville'],
                'email_acteur' => $acteur['Acteur']['email'],
                'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                'note_acteur' => $acteur['Acteur']['note']);
        }

        $acteurs = $this->Vote->find('all', array('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                "Vote.resultat" => Vote::votePour),
            'contain' => array('Acteur'),
            'order' => 'Acteur.position ASC'));

        foreach ($acteurs as $acteur) {
            $acteurs_pour[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                'prenom_acteur' => $acteur['Acteur']['prenom'],
                'salutation_acteur' => $acteur['Acteur']['salutation'],
                'titre_acteur' => $acteur['Acteur']['titre'],
                'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                'cp_acteur' => $acteur['Acteur']['cp'],
                'ville_acteur' => $acteur['Acteur']['ville'],
                'email_acteur' => $acteur['Acteur']['email'],
                'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                'note_acteur' => $acteur['Acteur']['note']);
        }

        $acteurs = $this->Vote->find('all', array('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                "Vote.resultat" => Vote::abstention),
            'contain' => array('Acteur'),
            'order' => 'Acteur.position ASC'));

        foreach ($acteurs as $acteur) {
            $acteurs_abstention[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                'prenom_acteur' => $acteur['Acteur']['prenom'],
                'salutation_acteur' => $acteur['Acteur']['salutation'],
                'titre_acteur' => $acteur['Acteur']['titre'],
                'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                'cp_acteur' => $acteur['Acteur']['cp'],
                'ville_acteur' => $acteur['Acteur']['ville'],
                'email_acteur' => $acteur['Acteur']['email'],
                'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                'note_acteur' => $acteur['Acteur']['note']);
        }
        $acteurs = $this->Vote->find('all', array('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                "Vote.resultat" => Vote::sansParticipation),
            'contain' => array('Acteur'),
            'order' => 'Acteur.position ASC'));

        foreach ($acteurs as $acteur) {
            $acteurs_sans_participation[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                'prenom_acteur' => $acteur['Acteur']['prenom'],
                'salutation_acteur' => $acteur['Acteur']['salutation'],
                'titre_acteur' => $acteur['Acteur']['titre'],
                'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                'cp_acteur' => $acteur['Acteur']['cp'],
                'ville_acteur' => $acteur['Acteur']['ville'],
                'email_acteur' => $acteur['Acteur']['email'],
                'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                'note_acteur' => $acteur['Acteur']['note']);
        }

        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursPresents", $acteurs_presents, false, '_present'));
        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursAbsents", $acteurs_absents, false, '_absent'));
        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursMandates", $acteurs_remplaces, true, '_mandataire'));
        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursContre", $acteurs_contre, false, '_contre'));
        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursPour", $acteurs_pour, false, '_pour'));
        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursAbstention", $acteurs_abstention, false, '_abstention'));
        $oMainPart->addElement($this->_makeBlocsActeurs("ActeursSansParticipation", $acteurs_sans_participation, false, '_sans_participation'));
    }

    /**
     * Crée et complète une itération avec les champs concernant un ensemble
     * d'acteurs.
     *
     * Si la liste d'acteurs est vide, on aura néanmoins tous les champs dans
     * la première itération, et ceux-ci seront vides.
     *
     * Variables Gedooo, pour chacun des acteurs de la liste:
     *  - nombre_acteur<suffixe>/count($listeActeurs)/text
     *
     *  - nom_acteur<suffixe>/Acteur.nom/text
     *  - prenom_acteur<suffixe>/Acteur.prenom/text
     *  - salutation_acteur<suffixe>/Acteur.salutation/text
     *  - titre_acteur<suffixe>/Acteur.titre/text
     *  - date_naissance_acteur<suffixe>/Acteur.date_naissance/date
     *  - adresse1_acteur<suffixe>/Acteur.adresse1/text
     *  - adresse2_acteur<suffixe>/Acteur.adresse2/text
     *  - cp_acteur<suffixe>/Acteur.cp/text
     *  - ville_acteur<suffixe>/Acteur.ville/text
     *  - email_acteur<suffixe>/Acteur.email/text
     *  - telfixe_acteur<suffixe>/Acteur.telfixe/text
     *  - telmobile_acteur<suffixe>/Acteur.telmobile/text
     *  - note_acteur<suffixe>/Acteur.note/text
     *
     *  Si l'acteur est mandaté
     *  - nom_acteur_mandate/Mandataire.nom_acteur/text
     *  - prenom_acteur_mandate/Mandataire.prenom_acteur/text
     *  - salutation_acteur_mandate/Mandataire.salutation_acteur/text
     *  - titre_acteur_mandate/Mandataire.titre_acteur/text
     *  - date_naissance_acteur_mandate/Mandataire.date_naissance_acteur/text
     *  - adresse1_acteur_mandate/Mandataire.adresse1_acteur/text
     *  - adresse2_acteur_mandate/Mandataire.adresse2_acteur/text
     *  - cp_acteur_mandate/Mandataire.cp_acteur/text
     *  - ville_acteur_mandate/Mandataire.ville_acteur/text
     *  - email_acteur_mandate/Mandataire.email_acteur/text
     *  - telfixe_acteur_mandate/Mandataire.telfixe_acteur/text
     *  - telmobile_acteur_mandate/Mandataire.telmobile_acteur/text
     *  - note_acteur_mandate/Mandataire.note_acteur/text
     *
     * @param string $nomBloc Le nom de l'itération.
     * @param array $listActeur La liste d'acteurs résultant d'un Model::find( 'all' )
     * @param boolean $isMandate Permet de savoir si l'on a affaire à un acteur mandaté
     * @param string $type Le suffixe à appliquer aux noms de champs
     * @return GDO_IterationType
     */
	protected function _makeBlocsActeurs ($nomBloc, $listActeur, $isMandate, $type) {
		$acteurs = new GDO_IterationType("$nomBloc");

		if ( count($listActeur) == 0 ) {
			$oDevPart = new GDO_PartType();
			$oDevPart->addElement(new GDO_FieldType("nombre_acteur".$type,        '0', "text"));

			$oDevPart->addElement(new GDO_FieldType("nom_acteur".$type,            ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type,         ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,     ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("titre_acteur".$type,          ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, ' ', "date"));
			$oDevPart->addElement(new GDO_FieldType("adresse1_acteur".$type,       ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("adresse2_acteur".$type,       ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("cp_acteur".$type,             ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("ville_acteur".$type,          ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("email_acteur".$type,          ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("telfixe_acteur".$type,        ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("telmobile_acteur".$type,      ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType("note_acteur".$type,           ' ', "text"));

			$oDevPart->addElement(new GDO_FieldType('nom_acteur_mandate',                 ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('prenom_acteur_mandate',              ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('salutation_acteur_mandate',          ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('titre_acteur_mandate',               ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('date_naissance_acteur_mandate',      ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('adresse1_acteur_mandate',            ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('adresse2_acteur_mandate',            ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('cp_acteur_mandate',                  ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('ville_acteur_mandate',               ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('email_acteur_mandate',               ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('telfixe_acteur_mandate',             ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('telmobile_acteur_mandate',           ' ', "text"));
			$oDevPart->addElement(new GDO_FieldType('note_acteur_mandate',                ' ', "text"));
			$acteurs->addPart($oDevPart);
			return $acteurs;
		}
		else {
			$nbre_acteurs = count($listActeur);
			foreach($listActeur as $acteur) {
				$oDevPart = new GDO_PartType();
				$oDevPart->addElement(new GDO_FieldType("nombre_acteur".$type, $nbre_acteurs , "text"));
				$oDevPart->addElement(new GDO_FieldType("nom_acteur".$type, ($acteur['nom_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type, ($acteur['prenom_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,($acteur['salutation_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("titre_acteur".$type, ($acteur['titre_acteur']), "text"));
				if ($acteur['date_naissance_acteur'] != null)
					$oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type,  $this->Date->frDate($acteur['date_naissance_acteur']), "date"));
				else
					$oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, '', "date"));

				$oDevPart->addElement(new GDO_FieldType("adresse1_acteur".$type, ($acteur['adresse1_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("adresse2_acteur".$type, ($acteur['adresse2_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("cp_acteur".$type, ($acteur['cp_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("ville_acteur".$type, ($acteur['ville_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("email_acteur".$type, ($acteur['email_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("telfixe_acteur".$type,($acteur['telfixe_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("telmobile_acteur".$type,  ($acteur['prenom_acteur']), "text"));
				$oDevPart->addElement(new GDO_FieldType("note_acteur".$type, ($acteur['note_acteur']), "text"));
				if ($isMandate) {
					$oDevPart->addElement(new GDO_FieldType('nom_acteur_mandate', ($acteur['nom_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('prenom_acteur_mandate', ($acteur['prenom_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('salutation_acteur_mandate', ($acteur['salutation_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('titre_acteur_mandate', ($acteur['titre_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('date_naissance_acteur_mandate', ($acteur['date_naissance_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('adresse1_acteur_mandate', ($acteur['adresse1_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('adresse2_acteur_mandate', ($acteur['adresse2_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('cp_acteur_mandate', ($acteur['cp_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('ville_acteur_mandate', ($acteur['ville_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('email_acteur_mandate', ($acteur['email_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('telfixe_acteur_mandate', ($acteur['telfixe_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('telmobile_acteur_mandate', ($acteur['telmobile_mandate']), "text"));
					$oDevPart->addElement(new GDO_FieldType('note_acteur_mandate', ($acteur['note_mandate']), "text"));
				}
				$acteurs->addPart($oDevPart);
			}
			return $acteurs;

		}
	}


	function _url2pathImage($url) {
		$content = str_replace('http://webdelib/app/', Configure::read('WEBDELIB_PATH'), $url);
		$content = str_replace( '\"', '"', $content);
		return $content;
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
	 * sauvergarde des délibérations attachées
	 * @param integer $parentId id de la délibération principale
	 * @param array $delib délibération rattachée retourné par le formulaire 'edit'
	 */
	function saveDelibRattachees($parentId, $delib, $objet_projet) {
		// initialisations
		$newDelib = array();

		if (isset($delib['id'])) {
                    // modification
                    $this->id =  $delib['id'];
                    $newDelib['Deliberation']['id'] = $delib['id'];
		 } else {
                    // ajout
                    $newDelib = $this->create();
                    $newDelib['Deliberation']['parent_id'] = $parentId;
                }
                $newDelib['Deliberation']['num_pref'] = '';
                $newDelib['Deliberation']['objet'] = $delib['objet_delib'];
                $newDelib['Deliberation']['objet_delib'] = $delib['objet_delib'];
                $newDelib['Deliberation']['titre'] = $delib['titre'];

		if (Configure::read('GENERER_DOC_SIMPLE')){
			$newDelib['Deliberation']['deliberation'] = $delib['deliberation'];
		} else {
			if (isset($delib['deliberation'])) {
                            $newDelib['Deliberation']['deliberation_name'] = $delib['deliberation']['name'];
                            $newDelib['Deliberation']['deliberation_type'] = $delib['deliberation']['type'];
                            $newDelib['Deliberation']['deliberation_size'] = $delib['deliberation']['size'];
                            if (empty($delib['deliberation']['tmp_name']))
                                    $newDelib['Deliberation']['deliberation'] = '';
                            else
                                    $newDelib['Deliberation']['deliberation'] = file_get_contents($delib['deliberation']['tmp_name']);
                        }else{
                                $pos  =  strrpos ( getcwd(), 'webroot');
                                $path = substr(getcwd(), 0, $pos);
                                $path_projet = $path.'webroot/files/generee/projet/'.$this->id.'/';
                                if(file_exists($path_projet.'deliberation.odt'))
                                    $newDelib['Deliberation']['deliberation'] = file_get_contents($path_projet.'deliberation.odt');

                        }
		}

		if(!$this->save($newDelib['Deliberation'], false)) {
			$this->Session->setFlash('Erreur lors de la sauvegarde des délibérations rattachées.', 'growl', array('type'=>'erreur'));
			return false;
		}
		$tabs = array();
		$seances = $this->Deliberationseance->find('all', array(
				'conditions' => array('Deliberationseance.deliberation_id' => $parentId),
				'recursive'  => -1));
		foreach($seances as $seance)
			$tabs[] = $seance['Deliberationseance']['seance_id'];
		$this->Seance->reOrdonne($this->id, $tabs);
		return $this->id;
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

		// suppression de la délib
		$this->delete($delibId);
		// suppression du répertoire des docs
		$repFichier = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$delibId.DS;
		$this->rmDir($repFichier);
		// gestion de la séance
		if (!empty($delib['Deliberation']['seance_id'])) {
			$this->reOrdonnePositionSeance($delib['Deliberation']['seance_id']);
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
		}

		// suppression des délib antérieures
		if ( $delib['Deliberation']['anterieure_id'] != 0)
			$this->supprimer($delib['Deliberation']['anterieure_id']);
	}

	/**
	 * Supprime un répertoire et son contenu
	 * @param string $dossier chemin du répertoire à supprimer
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
                        'conditions' =>  array(
                            'Deliberationseance.deliberation_id' => $deliberation_id
                        ),
                        'order'=>'Deliberationseance.position ASC',
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
	       $typeseances = $this->Seance->Typeseance->find('list', array('recursive'=> -1, 'fields' => array('libelle')));
                $seances = array();
		if (isset($projets) && !empty($projets))
			foreach ($projets as $projet)
			if (isset($projet['Seance']) && (!empty($projet['Seance']))) {
			foreach($projet['Seance'] as $seance) {
	                   $typeseance=$typeseances[$seance['type_id']];

              $seances[$seance['id']] = $typeseance.' : '.$seance['date'];
			}
		}
		return $seances;
	}

	function getTypeseancesFromArray($projets) {

	       $list = $this->Seance->Typeseance->find('list', array('recursive'=> -1, 'fields' => array('libelle')));

                 $typeseances = array();
		if  (isset($projets) && !empty($projets))
			foreach ($projets as $projet) {
			if  (isset($projet['Seance']) && !empty($projet['Seance']))
				foreach($projet['Seance'] as $seance)
	                           if (isset($seance['type_id'])) {
                                    $typeseance=$list[$seance['type_id']];
				$typeseances[$seance['type_id']] = $typeseance;
	                      }
                }
		return $typeseances;
	}

	function getPosition($deliberation_id, $seance_id) {
		$deliberationseance = $this->Deliberationseance->find(
                    'first',
                    array(
                        'fields' => array( 'Deliberationseance.position' ),
                        'recursive' => -1,
                        'conditions' => array(
                            'Deliberationseance.seance_id' => $seance_id,
                            'Deliberationseance.deliberation_id' => $deliberation_id
                        ),
                        'order' => array( 'Deliberationseance.position ASC' ),
                    )
                );

		return $deliberationseance['Deliberationseance']['position'];
	}

	function afficherListePresents($delib_id=null, $seance_id)      {
                $this->Listepresence->Behaviors->attach('Containable');
		$presents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
		                                         	    'order'      => array("Acteur.position ASC"),
                                                                    'contain'    => array('Acteur', 'Acteur.Typeacteur')));
		if (empty($presents) && $this->isFirstDelib($delib_id, $seance_id)) {
			$presents = $this->_buildFirstList($delib_id, $seance_id);
		}

		// Si la liste est vide, on recupere la liste des present lors de la derbiere deliberation.
		// Verifier que la liste precedente n'est pas vide...
		if (empty($presents))
			$presents = $this->_copyFromPreviousList($delib_id, $seance_id);

                if (!empty($presents))
		foreach($presents as &$acteur){
                    if (!empty($acteur['Listepresence']['mandataire'])) {
                        $mandataire = $this->Seance->Typeseance->Acteur->read('nom, prenom', $acteur['Listepresence']['mandataire']);
                            $acteur['Listepresence']['mandataire'] = $mandataire['Acteur']['prenom']." ".$mandataire['Acteur']['nom'];
                    }elseif (!empty($acteur['Listepresence']['suppleant_id'])) {
                        $suppleant= $this->Seance->Typeseance->Acteur->read('nom, prenom', $acteur['Listepresence']['suppleant_id']);
                            $acteur['Listepresence']['suppleant'] = $suppleant['Acteur']['prenom']." ".$suppleant['Acteur']['nom'];
                    }
		}
		return $presents;
	}

	function _buildFirstList($delib_id, $seance_id) {
		$seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
		                                             'recursive'  => -1,
				                             'fields'     => array('Seance.type_id')));
		$elus = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
		foreach ($elus as $elu){
			$this->Listepresence->create();
			$params['data']['Listepresence']['acteur_id']=$elu['Acteur']['id'];
			$params['data']['Listepresence']['present']= 1;
			$params['data']['Listepresence']['delib_id']= $delib_id;
			$this->Listepresence->save($params['data']);
		}

		return  $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
		                                       	        'order'      => array("Acteur.position ASC"),
                                                                'contain'    => array('Acteur', 'Acteur.Typeacteur')));
	}

	function _copyFromPreviousList($delib_id, $seance_id){
		$this->Listepresence->Behaviors->attach('Containable');

		$position = $this->getPosition($delib_id, $seance_id);
                if($position==1)return NULL;
		$previousDelibId= $this->_getDelibIdByPosition($seance_id, $position);
		$previousPresents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $previousDelibId),
		                                                            'recursive'  => -1));

		foreach ($previousPresents as $present){
                    $this->Listepresence->create();
		    $params['data']['Listepresence']['acteur_id']=$present['Listepresence']['acteur_id'];
                    $params['data']['Listepresence']['mandataire'] = $present['Listepresence']['mandataire'];
		    $params['data']['Listepresence']['suppleant_id'] = $present['Listepresence']['suppleant_id'];
		    $params['data']['Listepresence']['present']= $present['Listepresence']['present'];
		    $params['data']['Listepresence']['delib_id']= $delib_id;
		    $this->Listepresence->save($params['data']);
		}
                $liste = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
                                                                 'order'      => array("Acteur.position ASC"),
                                                                 'contain'    => array('Acteur', 'Acteur.Typeacteur')));
		if (!empty($liste))
			return  $liste;
		else
			return ($this->_buildFirstList($delib_id, $seance_id));
	}

	function _effacerListePresence($delib_id) {
		$this->Listepresence->deleteAll(array("delib_id" => $delib_id));
	}

	function _getDelibIdByPosition ($seance_id, $position){
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();
		$delib = $this->Deliberationseance->find('first', array('conditions' => array('Deliberationseance.position'  =>  $position-1,
				'Seance.id' => $seance_id),
				'fields'    => array('Deliberation.id')));

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

        function getActesExceptDelib($conditions=array(), $fields, $contain) {
            $code_delib = 'DE';
            if (!isset($conditions['Deliberation.typeacte_id']))  {
                $nature_ids = $this->Typeacte->Nature->find('all', array('conditions' => array('Nature.code !=' => $code_delib),
                                                                         'recursive'  => -1,
                                                                         'fields'     => array('Nature.id')));

                $typeacte_ids = $this->Typeacte->find('all', array('conditions' => array('Typeacte.nature_id' => Set::extract('/Nature/id', $nature_ids)),
                                                            'recursive'  => -1,
                                                            'fields'     => array('Typeacte.id')));

                $conditions = array_merge($conditions,  array('Deliberation.typeacte_id' =>Set::extract('/Typeacte/id', $typeacte_ids)));
            }
            $this->Behaviors->attach('Containable');
            $actes = $this->find('all', array('conditions' => $conditions,
                                              'contain'    => $contain,
                                              'fields'     => $fields));
            foreach ($actes as &$acte) {
                $acte['Model']['modeleprojet_id'] = $this->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modeleprojet_id');
                $acte['Model']['modelefinal_id'] = $this->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modelefinal_id');
            }
            return $actes;
        }

        function  is_delib($acte_id) {
            $this->Behaviors->attach('Containable');
            $acte = $this->find('first', array('conditions' => array('Deliberation.id' => $acte_id),
                                               'contain'    => array('Typeacte.nature_id'),
                                               'fields'     => array('Deliberation.typeacte_id')));
            $nature = $this->Typeacte->Nature->find('first', array('conditions' => array('Nature.id' => $acte['Typeacte']['nature_id']),
                                                                   'fields'     => array('Nature.code'),
                                                                   'recursive'  => -1));
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
                $Deliberationseance = $this->Deliberationseance->find('first',
                    array('conditions' => array('Deliberationseance.deliberation_id' => $delib_id,
                                                'Deliberationseance.seance_id'       => $seance_id),
                          'fields'     => array('Deliberationseance.id'),
                          'recursive'  => -1));
                $this->Deliberationseance->id =  $Deliberationseance['Deliberationseance']['id'];
                $this->Deliberationseance->saveField('position',  $position);
            }
            return $seances;
        }

        /**
         *
         * @param type $parafhisto
         * @param type $delib_id
         * @param type $circuit_id
         */
        function setHistorique($parafhisto,$delib_id, $circuit_id){
            $histo = $this->Historique->create();
            $histo['Historique']['delib_id'] = $delib_id;
            $histo['Historique']['user_id'] = -1;
            $histo['Historique']['commentaire'] = $parafhisto;
            $histo['Historique']['circuit_id'] = $circuit_id;
            $this->Historique->save($histo['Historique']);
        }
        /**
         *
         * @param type $delib_id
         * @param type $logdossier
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

        // ---------------------------------------------------------------------

		/**
		 * Lecture des enregistrements.
		 *
         * @param array $conditions
         * @param boolean $readSeances
         * @return array
		 */
		public function gedoooReadAll( array $conditions, $readSeances = true ) {
            $projets = $this->find(
                'all',
                array(
                    'fields' => array_merge(
						$this->fields(),
						$this->Rapporteur->fields(),
						$this->Redacteur->fields(),
						$this->Theme->fields()
                    ),
                    'recursive' => -1,
                    'joins' => array(
						$this->join( 'Rapporteur', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Redacteur', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Theme', array( 'type' => 'LEFT OUTER' ) ),
                    ),
					'conditions' => array(
						'Deliberation.etat >=' => self::enCoursRedaction,
                        $conditions
					),
                )
            );

            foreach( $projets as $indexProjet => $projet ) {
                // Lecture des séances du projet si nécessaire
                if( $readSeances ) {
                    // TODO: séance principale ou délibérante, etc...
                    $projet['Seances'] = $this->Deliberationseance->gedoooRead( $projet['Deliberation']['id'] );
                    if( !empty( $projet['Seances'] ) ) {
                        foreach( $projet['Seances'] as $indexSeance => $seance ) {
                            // TODO: Obtention des acteurs convoqués, 5 reqûetes à transformer en une ?
                            $type_id = $this->Seance->getType( $seance['Seance']['id'] );
                            $seance['Convoques'] = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId( $type_id );
// TODO: AvisSeance
                            // Itération AvisSeance
                            $seance['AvisSeance'] = $this->Deliberationseance->find(
                                'all',
                                array(
                                    'fields' => array(
                                        'Deliberationseance.commentaire'
                                    ),
                                    'conditions' => array(
                                        'Deliberationseance.seance_id' => $seance['Seance']['id']
                                    ),
                                    'recursive' => -1,
                                    'order' => array( 'Deliberationseance.position ASC' )
                                )
                            );

                            $projet['Seances'][$indexSeance] = $seance;
                        }
                    }
                }

                // Thème et thèmes parents d'un projet. TODO: Donnera [T1_theme,T10_theme] comme variables Gedooo
                $projet['Themes'] = $this->Theme->postgresFindParents( $projet['Deliberation']['theme_id'], array( 'libelle' ) );

                // Service et services parents d'un projet. TODO: Donnera service_emetteur et service_avec_hierarchie comme variables Gedooo
                $projet['Services'] = $this->Service->postgresFindParents( $projet['Deliberation']['service_id'], array( 'libelle' ) );

                // Obtention des historiques
                $historiques = $this->Historique->find(
                    'all',
                    array(
                        'fields' => array(
                            'Historique.commentaire'
                        ),
                        'recursive' => -1,
                        'conditions' => array(
                            'Historique.delib_id' => $projet['Deliberation']['id']
                        ),
                        'order' => array( 'Historique.created ASC' )
                    )
                );
                $projet['Historiques'] = $historiques;

                // Informations supplémentaires du projet
                $projet['Infossups'] = $this->Infosup->gedoooReadAll( 'Deliberation', $projet['Deliberation']['id'] );

                // Liste de présences du projet
                $projet['Listespresences'] = $this->Listepresence->gedoooReadAll( $projet['Deliberation']['id'] );

                // Commentaires du projet
                $projet['Commentaires'] = $this->Commentaire->gedoooReadAll( $projet['Deliberation']['id'] );

                // Projet multi-délibérations ? // TODO: dans le modèle Multidelib, se servir d'un querydata commun ? Fields id, objet ?
                $projet['Deliberations'] = $this->Multidelib->find(
                    'all',
                    array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Multidelib.parent_id' => $projet['Deliberation']['id']
                        )
                    )
                );

                // Annexes
                $projet['Annexes'] = $this->Annex->getAnnexesFromDelibId2( $projet['Deliberation']['id'], 0, 1 );

                // Fin du traitement
                $projets[$indexProjet] = $projet;
            }

			return $projets;
        }

        /**
         * Lecture d'un enregistrement.
         *
         * @param integer $id
         * @param boolean $readSeances
         * @return array
         */
        public function gedoooRead( $id, $readSeances = true ) {
            $results = $this->gedoooReadAll( array( 'Deliberation.id' => $id ), $readSeances );

            return ( isset( $results[0] ) ? $results[0] : array() );
        }

		/**
		 * Normalisation des enregistrement: ajout des valeurs calculées, ...
         *
         * @todo gedoooNormalizeAll()
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooNormalize( array $data ) {
            $data['Deliberation']['acte_adopte'] = ( ( ( $data['Deliberation']['etat'] == self::votePour ) && ( $data['Deliberation']['vote_nb_oui'] == 0 ) ) ? '1' : '0' );
            if( !$data['Deliberation']['acte_adopte'] ) {
                $data['Deliberation']['nombre_pour'] = $data['Deliberation']['vote_nb_oui'];
                $data['Deliberation']['nombre_abstention'] = $data['Deliberation']['vote_nb_abstention'];
                $data['Deliberation']['nombre_contre'] = $data['Deliberation']['vote_nb_non'];
                $data['Deliberation']['nombre_sans_participation'] = $data['Deliberation']['vote_nb_retrait'];
            }

            $data['Deliberation']['date_envoi_signature'] = DateFrench::frDate( $data['Deliberation']['date_envoi_signature'] );

            if( !empty( $data['Commentaires'] ) ) {
                $data = $this->Commentaire->gedoooNormalizeAll( $data );
            }

            // Normalisation des infosup de la délibération
            $data = Hash::merge( $data, $this->Infosup->gedoooNormalizeAll( 'Deliberation', $data['Infossups'] ) );
            unset( $data['Infossups'] );

            // Normalisation des infosup des séances
            if( !empty( $data['Seances'] ) ) {
                foreach( $data['Seances'] as $indexSeance => $dataSeance ) {
                    // Infossups
                    $data['Seances'][$indexSeance] = Hash::merge(
                        $this->Deliberationseance->Seance->gedoooNormalize($data['Seances'][$indexSeance]),
                        $this->Infosup->gedoooNormalizeAll( 'Seance', $dataSeance['Infossups'] )
                    );
                    unset( $data['Seances'][$indexSeance]['Infossups'] );
                }
            }

            // Thème et thèmes parents d'un projet. TODO: Donnera [T1_theme,T10_theme] comme variables Gedooo
            if( !empty( $data['Themes'] ) ) {
                for( $i = 0 ; $i < 10 ; $i++ ) {
                    $data["T".( $i + 1 )."_theme"] = Hash::get( $data, "Themes.{$i}.Theme.libelle" );
                }
                unset( $data['Themes'] );
            }

            // Service et services parents d'un projet. TODO: Donnera service_emetteur et service_avec_hierarchie comme variables Gedooo
            $services = (array)Hash::extract( $data, 'Services.{n}.Service.libelle' );
            $count = count( $services );
            $data['service_emetteur'] = @$services[$count-1];
            $data['service_avec_hierarchie'] = implode( '/', $services );
            unset( $data['Services'] );

            // TODO: iteration Convoques
            // $type_id = $this->Seance->getType( $projet['Seance']['id'] );
            // $projet['Convoques'] = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($type_id);
            /*$data['Convoques'] = array();
            if( !empty( $data['Listespresences'] ) ) {
                foreach( $data['Listespresences'] as $listepresence ) {
                    $data['Convoques'][] = array(
                        'Acteur' => array(
                            'nom' => $listepresence['Acteur']['nom'],
                            'prenom' => $listepresence['Acteur']['prenom'],
                            'salutation' => $listepresence['Acteur']['salutation'],
                            'titre' => $listepresence['Acteur']['titre'],
                            'note' => $listepresence['Acteur']['note'],
                        )
                    );
                }
            }*/

            // Traitement des votes et des présences ... FIXME: devrait être pour une séance donnée ?, pour AvisSeance
            $data = Hash::merge( $data, $this->Listepresence->gedoooNormalizeAll( $data['Listespresences'] ) );
            unset( $data['Listespresences'] );

            // Normalisation des séances
            if( !empty( $data['Seances'] ) ) {
                $seances_keys = array_combine( array_keys( $data['Seances'] ), Hash::extract( $data['Seances'], '{n}.Typeseance.action' ) );
                if( count( $seances_keys ) == 1 ) {
                    // TODO: Deliberationseance.position (seance_id/deliberation_id)
                    // $position = $this->getPosition($delib['Deliberation']['id'], $delibseances[0]);
                    // $oMainPart->addElement(new GDO_FieldType('position_projet', $position, 'text'));
                    $data = Hash::merge( $data, $data['Seances'][0] );
                }
                else if( count( $seances_keys ) >= 1 ) {
                    $seancedeliberante_key = array_search( 0, $seances_keys, true );
                    if( $seancedeliberante_key !== false ) {
                        $data = Hash::merge( $data, $data['Seances'][$seancedeliberante_key] );
                    }
                }
            }
            $data['nombre_seance'] = count((array)@$data['Seances']);

            $data['AvisSeance'] = Hash::extract( $data['AvisSeance'], '{n}.Deliberationseance' );

			return $data;
		}


		/**
		 * Retourne une correspondance entre les champs CakePHP (même calculés)
		 * et les champs Gedooo.
		 *
		 * @return array
		 */
		public function gedoooPaths() {
			$correspondances = array(
                'identifiant_projet' => 'Deliberation.id',
                'objet_projet' => 'Deliberation.objet',
                'libelle_projet' => 'Deliberation.objet',
                'objet_delib' => 'Deliberation.objet_delib',
                'libelle_delib' => 'Deliberation.objet_delib',
                'etat_projet' => 'Deliberation.etat',
                'date_envoi_signature' => 'Deliberation.date_envoi_signature',
                'date_reception' => 'Deliberation.dateAR',
                'position_projet' => 'Deliberationseance.position',
                'commentaire' => 'Deliberationseance.commentaire',
                'titre_projet' => 'Deliberation.titre',
                'numero_deliberation' => 'Deliberation.num_delib',
                'classification_deliberation' => 'Deliberation.num_pref',
                'theme_projet' => 'Theme.libelle',
                'nom_redacteur' => 'Redacteur.nom',
                'prenom_redacteur' => 'Redacteur.prenom',
                'email_redacteur' => 'Redacteur.email',
                'telmobile_redacteur' => 'Redacteur.telmobile',
                'telfixe_redacteur' => 'Redacteur.telfixe',
                'note_redacteur' => 'Redacteur.note',
                // Séance.Projet.Historique -> TODO: dans une sorte d'itération ?
                'log' => 'Historique.commentaire',
                // Service
                'service_emetteur' => 'service_emetteur',
                'service_avec_hierarchie' => 'service_avec_hierarchie',
                // Champs supplémentaires
                'acte_adopte' => 'Deliberation.acte_adopte',
                'nombre_pour' => 'Deliberation.nombre_pour',
                'nombre_abstention' => 'Deliberation.nombre_abstention',
                'nombre_contre' => 'Deliberation.nombre_contre',
                'nombre_sans_participation' => 'Deliberation.nombre_sans_participation',
                'nombre_seance' => 'nombre_seance',
                'libelle_multi_delib' => 'Multidelib.objet',
                'id_multi_delib' => 'Multidelib.id',
			);

            // Thèmes
            for( $i = 0 ; $i < 10 ; $i++ ) {
                $key = "T".( $i + 1 )."_theme";
                $correspondances[$key] = $key;
            }

            $correspondances = Hash::merge(
                $correspondances,
                array(
                    'nom_acteur_convoque_seance' => 'Acteur.nom',
                    'prenom_acteur_convoque_seance' => 'Acteur.prenom',
                    'salutation_acteur_convoque_seance' => 'Acteur.salutation',
                    'titre_acteur_convoque_seance' => 'Acteur.titre',
                    'note_acteur_convoque_seance' => 'Acteur.note',
                    'commentaire' => 'commentaire',
                    'commentaire_vote' => 'Deliberation.vote_commentaire',
                    'critere_trie_theme' => 'Theme.order',
                    'numero_acte' => 'Deliberation.num_delib',
                )
            );

            $correspondances = Hash::merge(
                $correspondances,
                $this->Listepresence->gedoooPaths(),
                $this->Rapporteur->gedoooPaths( 'rapporteur' ),
                $this->Seance->Secretaire->gedoooPaths( 'secretaire' ),
                $this->Seance->President->gedoooPaths( 'president' ),
                $this->Seance->gedoooPaths()
            );

			return $correspondances;
		}

		/**
		 * Retourne une correspondance entre les champs CakePHP (même calculés)
		 * et les types Gedooo.
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooTypes() {
            $types = array_merge(
				$this->types(),
				$this->Deliberationseance->types(),
				$this->Theme->types(),
				$this->Redacteur->types(),
				$this->Historique->types()
			);

            // Thèmes
            for( $i = 0 ; $i < 10 ; $i++ ) {
                $key = "T".( $i + 1 )."_theme";
                $types[$key] = 'text';
            }
            $types['critere_trie_theme'] = 'text';

            // Services
            $types['service_emetteur'] = 'text';
            $types['service_avec_hierarchie'] = 'text';

            // Séance
            $types['nombre_seance'] = 'text';

            // Acteurs convoqués: au singulier pour une delib, au pluriel pour les séances
            $types = Hash::merge(
                $types,
                array(
                    'Acteur.nom' => 'text',
                    'Acteur.prenom' => 'text',
                    'Acteur.salutation' => 'text',
                    'Acteur.titre' => 'text',
                    'Acteur.note' => 'text',
                    'commentaire' => 'text',
                )
            );

            // Liste de présence
            $types = Hash::merge(
                $types,
                $this->Listepresence->gedoooTypes(),
				//$this->Rapporteur->types(),
                $this->Rapporteur->gedoooPaths( 'rapporteur' ),
                $this->Seance->Secretaire->gedoooTypes( 'secretaire' ),
                $this->Seance->President->gedoooTypes( 'president' ),
                $this->Seance->gedoooTypes(),
                array(
                    'Deliberation.acte_adopte' => 'text',
                    'Deliberation.nombre_pour' => 'text',
                    'Deliberation.nombre_abstention' => 'text',
                    'Deliberation.nombre_contre' => 'text',
                    'Deliberation.nombre_sans_participation' => 'text',
                    'Deliberation.num_delib' => 'text',
                    'Deliberation.date_envoi_signature' => 'date',
                    'Deliberation.dateAR' => 'text',
                    'Multidelib.objet' => 'text',
                    'Multidelib.id' => 'text',
                )
            );

            return $types;
		}
}
?>
