<?php
class Deliberation extends AppModel {

	var $name = 'Deliberation';

	var $validate = array( 'objet'     => array(
			array( 'rule'    => 'notEmpty',
					'message' => 'L\'objet est obligatoire')),
			'typeacte_id' => array(
					array( 'rule'    => array('canSaveNature', 'notEmpty'),
						'message' => "Type d'acte invalide")),
			'texte_projet_type'   => array(
					array('rule' => array('checkMimetype', 'texte_projet', array('application/vnd.oasis.opendocument.text','application/zip')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'texte_synthese_type' => array(
					array('rule' => array('checkMimetype', 'texte_synthese',  array('application/vnd.oasis.opendocument.text','application/zip')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'deliberation_type'   => array(
					array('rule' => array('checkMimetype', 'deliberation',  array('application/vnd.oasis.opendocument.text','application/zip')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'debat_type'           => array(
					array('rule' => array('checkMimetype', 'debat',  array('application/vnd.oasis.opendocument.text','application/zip')),
							'message' => "Ce type de fichier n'est pas autorisé")),
			'commission_type'      => array(
					array('rule' => array('checkMimetype', 'commission',  array('application/vnd.oasis.opendocument.text','application/zip')),
							'message' => "Ce type de fichier n'est pas autorisé")));



	//dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
	var $belongsTo = array(
	/*                'Nomenclature'=>array(
	 'className'    => 'Nomenclature',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'num_pref'),
	*/
			'Service'=>array(
					'className'    => 'Service',
					'conditions'   => '',
					'order'        => '',
					'dependent'    => false,
					'foreignKey'   => 'service_id'),
			'Theme'=>array(
					'className'    => 'Theme',
					'conditions'   => '',
					'order'        => '',
					'dependent'    => false,
					'foreignKey'   => 'theme_id'),
			'Circuit'=>array(
					'className'    => 'Cakeflow.Circuit',
					'conditions'   => '',
					'order'        => '',
					'dependent'    => false,
					'foreignKey'   => 'circuit_id'),
			'Redacteur' =>array(
					'className'    => 'User',
					'conditions'   => '',
					'order'        => '',
					'dependent'    =>  true,
					'foreignKey'   => 'redacteur_id'),
			'Rapporteur'=> array(
					'className'    => 'Acteur',
					'conditions'   => '',
					'order'        => '',
					'dependent'    =>  true,
					'foreignKey'   => 'rapporteur_id'),
			'Typeacte'=> array(
					'className'    => 'Typeacte',
					'conditions'   => '',
					'order'        => '',
					'dependent'    =>  true,
					'foreignKey'   => 'typeacte_id')
	);

	var $hasMany = array(
			'TdtMessage' => array (
					'className'    => 'TdtMessage',
					'foreignKey'   => 'delib_id'),
			'Historique' =>array(
					'className'    => 'Historique',
					'foreignKey'   => 'delib_id'),
			'Traitement'=>array(
					'className'    => 'Cakeflow.Traitement',
					'foreignKey'   => 'target_id'),
			'Annex'=>array(	'className'    => 'Annex',
					'foreignKey'   => 'foreign_key',
                                        'order'        => array('Annex.id' => 'ASC'),
					'dependent'    => true),
			'Commentaire'=>array(
					'className'    => 'Commentaire',
					'foreignKey'   => 'delib_id'),
			'Listepresence'=>array(
					'className'    => 'Listepresence',
					'foreignKey'   => 'delib_id'),
			'Vote'=>array(
					'className'    => 'Vote',
					'foreignKey'   => 'delib_id'),
			'Infosup'=>array(
					'dependent' => true,
					'foreignKey' => 'foreign_key',
					'conditions' => array('Infosup.model' => 'Deliberation')),
			'Multidelib'=>array(
					'className'    => 'Deliberation',
					'foreignKey'   => 'parent_id',
					'dependent' => false),
			'Deliberationseance' =>array(
					'className'    => 'Deliberationseance',
					'foreignKey'   => 'deliberation_id'),
                       'Deliberationtypeseance' =>array(
                                        'className'    => 'Deliberationtypeseance',
                                        'foreignKey'   => 'deliberation_id'),


                 );

	var $hasAndBelongsToMany = array(
	    'Seance',  
            'Typeseance'
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

	function genererRecherche($projets, $model_id=1, $format=0, $multiSeances=array(), $conditions=array() ){
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');

		include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/ConversionComponent.php');
		$this->Conversion = new ConversionComponent;

		if ($format == 0) {
			$sMimeType = "application/pdf";
			$format    = "pdf";
		}
		elseif ($format ==1) {
			$sMimeType = "application/vnd.oasis.opendocument.text";
			$format    = "odt";
		}
		$dyn_path = "/files/generee/deliberations/";
		$nomFichier = "recherche";
		$path = WEBROOT_PATH.$dyn_path;
		if (!file_exists($path))
			mkdir($path);

		$content = $this->Seance->Typeseance->Modelprojet->find('first', array('conditions' => array('id' => $model_id),
				'fields'     => array('content'),
				'recursive'  => -1));
		$oTemplate = new GDO_ContentType("",
				"modele.odt",
				"application/vnd.oasis.opendocument.text",
				"binary",
				$content['Modelprojet']['content']);
		$oMainPart = new GDO_PartType();

		if (empty($multiSeances)) {
			$nbProjets = count($projets);
			if ($nbProjets > 1) {
				$i =0;
				$blocProjets = new GDO_IterationType("Projets");
			}
			foreach ($projets as $projet) {
				$oDevPart = new GDO_PartType();
				$this->makeBalisesProjet($projet,  $oDevPart);
				if ($nbProjets > 1)
					$blocProjets->addPart($oDevPart);
			}
			if ( $nbProjets > 1)
				$oMainPart->addElement($blocProjets);
			else
				$oMainPart =  $oDevPart;
		}
		else {
			$seances = new GDO_IterationType("Seances");
			foreach($multiSeances as $key => $seance_id)
				$seances->addPart($this->Seance->makeBalise($seance_id, null, true, $conditions));
			$oMainPart->addElement($seances);
		}

		$oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
		$oFusion->process();

		$oFusion->SendContentToFile($path.$nomFichier.".odt");
		$content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", $format);

		header("Content-type: $sMimeType");
		header("Content-Disposition: attachment; filename=recherche.$format");
		die($content);

		 

	}

	function makeBalisesProjet ($delib, &$oMainPart, $exceptSeance=false, $seance_id=null)  {

		include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/GedoooComponent.php');
		include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/DateComponent.php');
		include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/ConversionComponent.php');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
		$isDelib = ($delib['Deliberation']['etat'] >= 3);
		$u = new GDO_Utility();

		$this->Conversion = new ConversionComponent;
		$this->Date = new DateComponent;
		$this->Gedooo = new GedoooComponent;

		$dyn_path = "/files/generee/projet/".$delib['Deliberation']['id']."/";
		$path = WEBROOT_PATH.$dyn_path;

		// Itération sur les séances
		if (!$exceptSeance) {
			$delibseances = $this->getSeancesid($delib['Deliberation']['id']);
			$oMainPart->addElement(new GDO_FieldType('nombre_seance', count($delibseances), 'text'));
			if (count($delibseances) == 1) {
				$this->Seance->makeBalise($delibseances[0], $oMainPart);
				$position = $this->getPosition($delib['Deliberation']['id'], $delibseances[0]);
				$oMainPart->addElement(new GDO_FieldType('position_projet', $position, 'text'));
				$seances = new GDO_IterationType("Seances");
				$seances->addPart($this->Seance->makeBalise($delibseances[0]));
				$oMainPart->addElement($seances);
			}
			elseif(count($delibseances) >1) {
				$seance_deliberante = $this->Seance->getSeanceDeliberante($delibseances);

				$this->Seance->makeBalise($seance_deliberante, $oMainPart);

				$seances = new GDO_IterationType("Seances");
				foreach($delibseances as $key => $seance_id) {
					$seances->addPart($this->Seance->makeBalise($seance_id));
				}
				$oMainPart->addElement($seances);
			}
		}
		if ($seance_id != null) {
			$position = $this->getPosition($delib['Deliberation']['id'], $seance_id);
			$oMainPart->addElement(new GDO_FieldType('position_projet', $position, 'text'));
		}
		$oMainPart->addElement(new GDO_FieldType('titre_projet',   ($delib['Deliberation']['titre']),    'lines'));
		$oMainPart->addElement(new GDO_FieldType('objet_projet',   ($delib['Deliberation']['objet']),     'lines'));
		$oMainPart->addElement(new GDO_FieldType('libelle_projet', ($delib['Deliberation']['objet']),      'lines'));
		$oMainPart->addElement(new GDO_FieldType('objet_delib',    ($delib['Deliberation']['objet_delib']), 'lines'));
		$oMainPart->addElement(new GDO_FieldType('libelle_delib',  ($delib['Deliberation']['objet_delib']), 'lines'));
		$oMainPart->addElement(new GDO_FieldType('identifiant_projet',          $delib['Deliberation']['id'],       'text'));
		$oMainPart->addElement(new GDO_FieldType('etat_projet',                 $delib['Deliberation']['etat'],       'text'));
		$oMainPart->addElement(new GDO_FieldType('numero_deliberation',         $delib['Deliberation']['num_delib'],'text'));
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

		$nb_votant = $delib['Deliberation']['vote_nb_oui']+$delib['Deliberation']['vote_nb_abstention']+$delib['Deliberation']['vote_nb_non'];
		if (($delib['Deliberation']['etat'] == 3 ) &&  ($delib['Deliberation']['vote_nb_oui']==0 ))
			$oMainPart->addElement(new GDO_FieldType('acte_adopte',  '1', 'text'));
		else {
			$oMainPart->addElement(new GDO_FieldType('acte_adopte',  '0', 'text'));
			$oMainPart->addElement(new GDO_FieldType('nombre_pour',  ($delib['Deliberation']['vote_nb_oui'])   , 'text'));
			$oMainPart->addElement(new GDO_FieldType('nombre_abstention', ( $delib['Deliberation']['vote_nb_abstention']), 'text'));
			$oMainPart->addElement(new GDO_FieldType('nombre_contre',  ($delib['Deliberation']['vote_nb_non']), 'text'));
			$oMainPart->addElement(new GDO_FieldType('nombre_sans_participation', ( $delib['Deliberation']['vote_nb_retrait']), 'text'));
		}
		$oMainPart->addElement(new GDO_FieldType('nombre_votant', $nb_votant, 'text'));
		$oMainPart->addElement(new GDO_FieldType('date_reception',  ($delib['Deliberation']['dateAR']), 'text'));
		$oMainPart->addElement(new GDO_FieldType('commentaire_vote', $delib['Deliberation']['vote_commentaire'], 'lines'));

		$coms = $this->Commentaire->find('all',
				array('conditions' => array('Commentaire.delib_id' => $delib['Deliberation']['id']),
						'fields'     => array('texte', 'commentaire_auto'),
						'recursive'  => -1));

		if (!empty($coms)) {
			$commentaires = new GDO_IterationType("Commentaires");
			foreach($coms as $commentaire) {
				$oDevPart = new GDO_PartType();
				if ($commentaire['Commentaire']['commentaire_auto']==0){
					$oDevPart->addElement(new GDO_FieldType("texte_commentaire", ($commentaire['Commentaire']['texte']), "text"));
					$commentaires->addPart($oDevPart);
				}
			}
			@$oMainPart->addElement($commentaires);

			$avisCommission = new GDO_IterationType("AvisCommission");
			foreach($coms as $commentaire) {
				$oDevPart = new GDO_PartType();
				if ($commentaire['Commentaire']['commentaire_auto']==1) {
					$oDevPart->addElement(new GDO_FieldType("avis", ($commentaire['Commentaire']['texte']), "text"));
					$avisCommission->addPart($oDevPart);
				}
			}
			@$oMainPart->addElement($avisCommission);
		}
		$this->Deliberationseance->Behaviors->attach('Containable');
                $avisSeances =  $this->Deliberationseance->find('all', array(
                                'conditions' => array('Deliberationseance.deliberation_id' => $delib['Deliberation']['id']),
                                'contain'  => array('Seance.date', 'Seance.Typeseance')));
                include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/DateComponent.php');
                $this->Date = new DateComponent;


                if (!empty($avisSeances)) {
                    $aviss =  new GDO_IterationType("AvisProjet");
                    foreach($avisSeances as $avisSeance) {
                        if ( $avisSeance['Seance']['Typeseance']['action'] == 1) {
			    $oDevPart = new GDO_PartType();
                            $typeseance = $avisSeance['Seance']['Typeseance']['libelle'];
                            $dateSeance =  $this->Date->frenchDate(strtotime($avisSeance['Seance']['date']));  
                            if ($avisSeance['Deliberationseance']['avis'] == 2) {
                                $message = "A reçu un avis défavorable  en $typeseance du $dateSeance";
                                $avisFavorable = 1;
                            }
                            elseif  ($avisSeance['Deliberationseance']['avis'] == 1) {
                                $message = "A reçu un avis favorable  en $typeseance du $dateSeance";
                                $avisFavorable = 0;
                            } 
                            elseif ($avisSeance['Deliberationseance']['avis'] == null) {
                                $message = "";
                                $avisFavorable = null;
                            }
		            $oDevPart->addElement(new GDO_FieldType("avis", $message, "text"));
			    $oDevPart->addElement(new GDO_FieldType("avis_favorable",  $avisFavorable, "text"));
		            $oDevPart->addElement(new GDO_FieldType("commentaire", ($avisSeance['Deliberationseance']['commentaire']), "lines"));
			    $aviss->addPart($oDevPart);
                        }
                    }
		    @$oMainPart->addElement($aviss);
                }
                 
		$historik = $this->Historique->find('all',
				array('conditions' => array('Historique.delib_id' => $delib['Deliberation']['id']),
						'fields'     => array('commentaire'),
						'recursive'  => -1));

		if (!empty($historik)) {
			@$historique =  new GDO_IterationType("Historique");
			foreach($historik as $histo) {
				$oDevPart = new GDO_PartType();
				$oDevPart->addElement(new GDO_FieldType("log", ($histo['Historique']['commentaire']), "text"));
				$historique->addPart($oDevPart);
			}
			@$oMainPart->addElement($historique);
		}

		$infosup = $this->Infosup->find('all',
				array('conditions' => array('Infosup.foreign_key' => $delib['Deliberation']['id'],
						'Infosup.model'       => 'Deliberation'),
						'recursive'  => -1));
		if (!empty($infosup)) {
			foreach($infosup as  $champs)
				$oMainPart->addElement($this->Infosup->addField($champs['Infosup'], $delib['Deliberation']['id'], 'Deliberation'));
		}
		else {
			$defs = $this->Infosup->Infosupdef->find('all', array('conditions'=>array('model' => 'Deliberation'), 'recursive' => -1));
			foreach($defs as $def) {
				$oMainPart->addElement(new GDO_FieldType($def['Infosupdef']['code'],  (' '), 'text')) ;
			}
		}

		$multidelibs = $this->find('first', array('conditions' => array('Deliberation.parent_id' => $delib['Deliberation']['id']),
				'fields'     => array('id', 'objet')));
		@$Multi =  new GDO_IterationType("Deliberations");
		if (!empty($multidelibs['Multidelib'])) {
			foreach($multidelibs['Multidelib'] as $multidelib ){
				$oDevPart = new GDO_PartType();
				$oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", ($multidelib['objet']), "text"));
				$oDevPart->addElement(new GDO_FieldType("id_multi_delib",      ($multidelib['id']),    "text"));
				$Multi->addPart($oDevPart);
			}
		}
		else {
			$oDevPart = new GDO_PartType();
			$oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", " ", "text"));
			$oDevPart->addElement(new GDO_FieldType("id_multi_delib",      " ",    "text"));
			$Multi->addPart($oDevPart);
		}
		@$oMainPart->addElement($Multi);

		if (Configure::read('GENERER_DOC_SIMPLE')) {
			if (isset($delib['Deliberation']['texte_projet'])) {
				$filename = $path."texte_projet.html";
				$delib['Deliberation']['texte_projet'] = $this->_url2pathImage($delib['Deliberation']['texte_projet']);
				$this->Gedooo->createFile($path, "texte_projet.html",  $delib['Deliberation']['texte_projet']);
				$content = $this->Conversion->convertirFichier($filename, "odt");
				$oMainPart->addElement(new GDO_ContentType('texte_projet', 'texte_projet.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
			}
			if (isset($delib['Deliberation']['texte_synthese'])) {
				$filename = $path."texte_synthese.html";
				$this->Gedooo->createFile($path, "texte_synthese.html",  $delib['Deliberation']['texte_synthese']);
				$content = $this->Conversion->convertirFichier($filename, "odt");
				$oMainPart->addElement(new GDO_ContentType('note_synthese', 'texte_synthese.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
			}
			if (isset($delib['Deliberation']['deliberation'])) {
				$filename = $path."texte_deliberation.html";
				$this->Gedooo->createFile($path, "texte_deliberation.html",  $delib['Deliberation']['deliberation']);
				$content = $this->Conversion->convertirFichier($filename, "odt");
				$oMainPart->addElement(new GDO_ContentType('texte_deliberation', 'deliberation.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
			}
			if (isset($delib['Deliberation']['debat'])) {
				$filename = $path."debat_deliberation.html";
				$this->Gedooo->createFile($path, "debat_deliberation.html",  $delib['Deliberation']['debat']);
				$content = $this->Conversion->convertirFichier($filename, "odt");
				$oMainPart->addElement(new GDO_ContentType('debat_deliberation', 'debat.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
			}
			if (isset($delib['Deliberation']['commission'])) {
				$filename = $path."commission.html";
				$this->Gedooo->createFile($path, "commission.html",  $delib['Deliberation']['commission']);
				$content = $this->Conversion->convertirFichier($filename, "odt");
				$oMainPart->addElement(new GDO_ContentType('debat_commission', 'commission.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
			}
		}
		else {

			if (!$this->Gedooo->checkPath($path))
				die("Webdelib ne peut pas ecrire dans le repertoire : $path");

			$urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$dyn_path;
			if (!empty($delib['Deliberation']['texte_projet'])) {
				$oMainPart->addElement(new GDO_ContentType('texte_projet',
						'text_projet.odt' ,
						'application/vnd.oasis.opendocument.text',
						'binary',
						$delib['Deliberation']['texte_projet']));
			}
			if (!empty($delib['Deliberation']['deliberation'])) {
				$oMainPart->addElement(new GDO_ContentType('texte_deliberation',
						'td.odt',
						'application/vnd.oasis.opendocument.text' ,
						'binary',
						$delib['Deliberation']['deliberation']));
			}
			if (!empty($delib['Deliberation']['texte_synthese'])) {
				$oMainPart->addElement(new GDO_ContentType('note_synthese',
						'ns.odt',
						'application/vnd.oasis.opendocument.text' ,
						'binary',
						$delib['Deliberation']['texte_synthese']));
			}
			if (!empty($delib['Deliberation']['debat'])) {
				$oMainPart->addElement(new GDO_ContentType('debat_deliberation',
						'debat.odt',
						'application/vnd.oasis.opendocument.text' ,
						'binary',
						$delib['Deliberation']['debat']));
			}
			if (!empty($delib['Deliberation']['commission'])) {
				$oMainPart->addElement(new GDO_ContentType('debat_commission',
						'debat_commission.odt',
						'application/vnd.oasis.opendocument.text',
						'binary',
						$delib['Deliberation']['commission']));
			}

		}

		// $annexe_ids = $this->Annex->getAnnexesFromDelibId($delib['Deliberation']['id'], 0, 1);
		$annexe_ids = array();
		$anns = $this->Annex->find('all', array('conditions' =>  array(
				'Annex.foreign_key' => $delib['Deliberation']['id']),
				'fields' => array('Annex.id', 'Annex.filetype'), 
                                'order' => array('Annex.id' => 'ASC'),
				'recursive' => -1));
		foreach( $anns as $ann )
			$annexe_ids[] = $ann['Annex']['id'];
		$oMainPart->addElement(new GDO_FieldType('nombre_annexe', count($annexe_ids), 'text'));

		@$annexes =  new GDO_IterationType("Annexes");
		foreach($annexe_ids as $key => $annexe_id) {
                        unset ($annexe);
		        $oDevPart = new GDO_PartType();
			$annexe = $this->Annex->find('first', array ('conditions' => array('Annex.id' => $annexe_id),
				                                     'recursive'  => -1));
			$oDevPart->addElement(new GDO_FieldType('titre_annexe', $annexe['Annex']['titre'], 'text'));
			if (($annexe['Annex']['filetype'] == "application/vnd.oasis.opendocument.text")) {
			    $oDevPart->addElement(new GDO_FieldType('nom_fichier',  $annexe['Annex']['filename'], 'text'));
			    $oDevPart->addElement(new GDO_ContentType('fichier',    $annexe['Annex']['filename'],
			 			'application/vnd.oasis.opendocument.text',
						'binary',
						$annexe['Annex']['data']));
			    $annexes->addPart($oDevPart);
                            file_put_contents('/tmp/Annexe_'.$annexe_id, $annexe['Annex']['data']);
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

		$acteurs = $this->Listepresence->find('all',
				array ('conditions' => array("delib_id" => $delib['Deliberation']['id']),
						'contain'   => array('Acteur', 'Mandataire'),
						'order' => 'Acteur.position ASC'));
		if (!empty($acteurs)) {
			foreach($acteurs as $acteur) {
				if ( $acteur['Listepresence']['present'] == 1 ){
					$acteurs_presents[] = array('nom_acteur' => $acteur['Acteur']['nom'],
							'prenom_acteur' => $acteur['Acteur']['prenom'],
							'salutation_acteur'=> $acteur['Acteur']['salutation'],
							'titre_acteur'=> $acteur['Acteur']['titre'],
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
				elseif(($acteur['Listepresence']['present'] == 0) AND ($acteur['Listepresence']['mandataire']==0)) {
					$acteurs_absents[] = array('nom_acteur' => $acteur['Acteur']['nom'],
							'prenom_acteur' => $acteur['Acteur']['prenom'],
							'salutation_acteur'=> $acteur['Acteur']['salutation'],
							'titre_acteur'=> $acteur['Acteur']['titre'],
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
				elseif(($acteur['Listepresence']['present'] == 0) AND ($acteur['Listepresence']['mandataire']!=0)) {
					$acteurs_remplaces[] = array(
							'nom_acteur' => $acteur['Acteur']['nom'],
							'prenom_acteur' => $acteur['Acteur']['prenom'],
							'salutation_acteur'=> $acteur['Acteur']['salutation'],
							'titre_acteur'=> $acteur['Acteur']['titre'],
							'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
							'adresse1_acteur' => $acteur['Acteur']['adresse1'],
							'adresse2_acteur' => $acteur['Acteur']['adresse2'],
							'cp_acteur' => $acteur['Acteur']['cp'],
							'ville_acteur' => $acteur['Acteur']['ville'],
							'email_acteur' => $acteur['Acteur']['email'],
							'telfixe_acteur' => $acteur['Acteur']['telfixe'],
							'telmobile_acteur' => $acteur['Acteur']['telmobile'],
							'note_acteur' => $acteur['Acteur']['note'],
							'nom_mandate' => $acteur['Mandataire']['nom'],
							'prenom_mandate' => $acteur['Mandataire']['prenom'],
							'salutation_mandate'=> $acteur['Mandataire']['salutation'],
							'titre_mandate'=> $acteur['Mandataire']['titre'],
							'date_naissance_mandate' => $acteur['Mandataire']['date_naissance'],
							'adresse1_mandate' => $acteur['Mandataire']['adresse1'],
							'adresse2_mandate' => $acteur['Mandataire']['adresse2'],
							'cp_mandate' => $acteur['Mandataire']['cp'],
							'ville_mandate' => $acteur['Mandataire']['ville'],
							'email_mandate' => $acteur['Mandataire']['email'],
							'telfixe_mandate' => $acteur['Mandataire']['telfixe'],
							'telmobile_mandate' => $acteur['Mandataire']['telmobile'],
							'note_mandate' => $acteur['Mandataire']['note']);
				}
			}
		}

		$acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
				"Vote.resultat"=> 2),
				'contain'   => array('Acteur'),
				'order' => 'Acteur.position ASC'));

		foreach ($acteurs as $acteur) {
			$acteurs_contre[] = array('nom_acteur' => $acteur['Acteur']['nom'],
					'prenom_acteur' => $acteur['Acteur']['prenom'],
					'salutation_acteur'=> $acteur['Acteur']['salutation'],
					'titre_acteur'=> $acteur['Acteur']['titre'],
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
		 
		$acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
				"Vote.resultat"=> 3),
				'contain'   => array('Acteur'),
				'order' => 'Acteur.position ASC'));

		foreach ($acteurs as $acteur) {
			$acteurs_pour[] = array('nom_acteur' => $acteur['Acteur']['nom'],
					'prenom_acteur' => $acteur['Acteur']['prenom'],
					'salutation_acteur'=> $acteur['Acteur']['salutation'],
					'titre_acteur'=> $acteur['Acteur']['titre'],
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
		 
		$acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
				"Vote.resultat"=> 4),
				'contain'   => array('Acteur'),
				'order' => 'Acteur.position ASC'));

		foreach ($acteurs as $acteur) {
			$acteurs_abstention[] = array('nom_acteur' => $acteur['Acteur']['nom'],
					'prenom_acteur' => $acteur['Acteur']['prenom'],
					'salutation_acteur'=> $acteur['Acteur']['salutation'],
					'titre_acteur'=> $acteur['Acteur']['titre'],
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
		$acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
				"Vote.resultat"=> 5),
				'contain'   => array('Acteur'),
				'order' => 'Acteur.position ASC'));

		foreach ($acteurs as $acteur) {
			$acteurs_sans_participation[] = array('nom_acteur' => $acteur['Acteur']['nom'],
					'prenom_acteur' => $acteur['Acteur']['prenom'],
					'salutation_acteur'=> $acteur['Acteur']['salutation'],
					'titre_acteur'=> $acteur['Acteur']['titre'],
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

		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursPresents",   $acteurs_presents, false, '_present'));
		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursAbsents",    $acteurs_absents, false, '_absent'));
		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursMandates",   $acteurs_remplaces, true, '_mandataire'));
		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursContre",     $acteurs_contre, false, '_contre'));
		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursPour",       $acteurs_pour, false, '_pour'));
		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursAbstention", $acteurs_abstention, false, '_abstention'));
		$oMainPart->addElement($this->_makeBlocsActeurs("ActeursSansParticipation", $acteurs_sans_participation, false, '_sans_participation'));
	}

	function _makeBlocsActeurs ($nomBloc, $listActeur, $isMandate, $type) {
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
				'seance_id', 'position',
				'titre', 'num_pref', 'etat',
				'texte_projet', 'texte_projet_size', 'texte_projet_type', 'texte_projet_name',
				'texte_synthese', 'texte_synthese_size', 'texte_synthese_type', 'texte_synthese_name',
				'date_limite');

		// lecture en base
		$this->Behaviors->attach('Containable');
		$delib = $this->find('first', array(
				'fields' => $majFields,
				'contain' => array('Multidelib.id', 'Multidelib.position'),
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
	function saveDelibRattachees($parentId, $delib) {
		// initialisations
		$newDelib = array();
                
		if (isset($delib['objet_delib'])) {
                        $delib['objet'] = $delib['objet_delib'];
                } 
                else  
                    return false;
                
		if (isset($delib['id'])) {
                    // modification
                    $this->id =  $delib['id'];
                    $newDelib['Deliberation']['id'] = $delib['id'];
		} else {
                    // ajout
                    $newDelib = $this->create();
                    $newDelib['Deliberation']['parent_id'] = $parentId;
              }
              
                $newDelib['Deliberation']['objet'] = $delib['objet'];
                $newDelib['Deliberation']['objet_delib'] = $delib['objet_delib'];
		
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

	function getSeancesid($deliberation_id) {
		$seances = array();
		$delib_seances = $this->Deliberationseance->find( 'all',
				array('conditions' =>  array(
						'Deliberationseance.deliberation_id' => $deliberation_id),
						'fields' => array('Deliberationseance.seance_id'),
						'recursive' => -1));
		if (!empty( $delib_seances ))
			foreach( $delib_seances as $seance)
			$seances[] = $seance['Deliberationseance']['seance_id'];
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
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();
		$deliberation = $this->Deliberationseance->find('first', array('conditions' => array('Seance.id' => $seance_id,
				'Deliberation.id' => $deliberation_id),
				'fields'     => array('Deliberationseance.position')));
		return($deliberation['Deliberationseance']['position']);
	}

	function afficherListePresents($delib_id=null, $seance_id)      {
                $this->Listepresence->Behaviors->attach('Containable');
		$presents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
		                                         	    'order'      => array("Acteur.position ASC"),
                                                                    'contain'    => array('Acteur', 'Acteur.Typeacteur')));
		if ($this->isFirstDelib($delib_id, $seance_id) and (empty($presents))) {
			$presents = $this->_buildFirstList($delib_id, $seance_id);
		}

		// Si la liste est vide, on recupere la liste des present lors de la derbiere deliberation.
		// Verifier que la liste precedente n'est pas vide...
		if (empty($presents))
			$presents = $this->_copyFromPreviousList($delib_id, $seance_id);
		for($i=0; $i<count($presents); $i++){
			if ($presents[$i]['Listepresence']['mandataire'] !='0') {
				$mandataire = $this->Seance->Typeseance->Acteur->read('nom, prenom', $presents[$i]['Listepresence']['mandataire']);
				$presents[$i]['Listepresence']['mandataire'] = $mandataire['Acteur']['prenom']." ".$mandataire['Acteur']['nom'];
			}
		}
		return ($presents);
	}

	function _buildFirstList($delib_id, $seance_id) {
		$seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
		                                             'recursive'  => -1,
				                             'fields'     => array('Seance.type_id')));
		$elus = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
		foreach ($elus as $elu){
			$this->Listepresence->create();
			$params['data']['Listepresence']['acteur_id']=$elu['Acteur']['id'];
			$params['data']['Listepresence']['mandataire'] = '0';
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
		$previousDelibId= $this->_getDelibIdByPosition($seance_id, $position);
		$previousPresents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $previousDelibId),
		                                                            'recursive'  => -1));

		foreach ($previousPresents as $present){
                    $this->Listepresence->create();
		    $params['data']['Listepresence']['acteur_id']=$present['Listepresence']['acteur_id'];
		    $params['data']['Listepresence']['mandataire'] = $present['Listepresence']['mandataire'];
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
}
?>
