<?php
class Deliberation extends AppModel {

	var $name = 'Deliberation';

	var $cacheQueries = false;
	
	var $validate = array(
		'objet' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'L\'objet est obligatoire'
			)
		),
                'nature_id' => array(
                         array(
                          'rule'=>array('canSave'),
                          'message' => "Cette s�ance ne peux pas enregistrer cette nature d'acte"
                        )
                )
	);

	//dependent : pour les suppression en cascades. ici � false pour ne pas modifier le referentiel
	var $belongsTo = array(
                'Nomenclature'=>array(
                        'className'    => 'Nomenclature',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    => false,
                        'foreignKey'   => 'num_pref'),
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
		'Seance'=> array(
			'className'    => 'Seance',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'seance_id'),
                'Nature'=> array(
                        'className'    => 'Nature',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    =>  true,
                        'foreignKey'   => 'nature_id')
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
		'Annex'=>array(
			'className'    => 'Annex',
			'foreignKey' => 'foreign_key',
			//'conditions' => array('Annex.model' => 'Deliberation'),
			'dependent' => true),
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
			'dependent' => false)
		);

/*
 * Indique si le projet de d�lib�ration $delibId est modifiable pour $userId.
 * Attention : ne tient pas compte des droits qui sont fait dans le controller
 * En fonction de l'�tat du projet on a :
 * - le projet est refus� (etat = -1) : non modifiable
 * - le projet est en cours de r�daction (etat = 0) :
 *   + l'utilisateur connect� est le r�dacteur du projet : modifiable
 *   + l'utilisateur connect� n'est pas le r�dacteur du projet : non modifiable
 *  - le projet est en cours de validation (etat = 1) :
 *    + l'utilisateur connect� n'est pas dans le circuit de validation : non modifiable
 *    + l'utilisateur connect� est dans le circuit de validation :
 *      * il a d�ja valid� le projet : non modifiable
 *      * c'est � son tour de traiter le projet : modifiable
 *      * son tour n'est pas encore pass� : modifiable
 *  - le projet est valid� (etat = 2) : non modifiable
 *  - le projet a �t� vot� (etat = 3 ou 4) : non modifiable
 *  - le projet a �t� envoy� (etat = 5) : non modifiable
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

		/* traitement en fonction de l'�tat */
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
 * retourne le libell� correspondant � l'�tat $etat des projets et d�lib�rations
 * si $codesSpeciaux = true, retourne les libell�s avec les codes sp�ciaux des accents
 * si $codesSpeciaux = false, retourne les libell�s sans les accents (listes)
 */
	function libelleEtat($etat, $codesSpeciaux=false) {
 		switch($etat) {
		case -1 :
			return $codesSpeciaux ? 'Refus&eacute;' : 'Refus�';
			break;
		case 0 :
			return $codesSpeciaux ? 'En cours de r&eacute;daction' : 'En cours de r�daction';
			break;
		case 1:
			return $codesSpeciaux ? 'En cours d\'&eacute;laboration et de validation' : 'En cours d\'�laboration et de validation';
			break;
		case 2:
			return $codesSpeciaux ? 'Valid&eacute;' : 'Valid�';
			break;
		case 3:
			return $codesSpeciaux ? 'Vot&eacute; et adopt&eacute;' : 'Vot� et adopt�';
			break;
		case 4:
			return $codesSpeciaux ? 'Vot&eacute; et non adopt&eacute;' : 'Vot� et non adopt�';
			break;
		case 5:
			return $codesSpeciaux ? 'Transmis au contr&ocirc;le de l&eacute;galit&eacute;' : 'Transmis au contr�le de l�galit�';
			break;
		}
	}

	function generateListEtat() {
		$ret = array();
		for($i=-1; $i <= 5; $i++) $ret[$i] = $this->libelleEtat($i, false);
		return $ret;
	}

	function getCurrentPosition($id){
		$delib = $this->find("Deliberation.id = $id", 'position', null, -1);
		return  $delib['Deliberation']['position'];
	}

	function getCurrentSeance($id) {
		$delib = $this->find("Deliberation.id = $id", 'seance_id', null, -1);
		return  $delib['Deliberation']['seance_id'];
	}

	function getLastPosition($seance_id) {
		return $this->findCount("seance_id =$seance_id AND (etat != -1 )") + 1;
	}

	function isFirstDelib($delib_id) {
		$position  = $this->getCurrentPosition($delib_id);
		return  ($position == 1);
	}

	function changeSeance($delib_id, $seance_id){
		$this->data = $this->read(null, $delib_id);
		$this->data['Deliberation']['id']=$delib_id;
		$this->data['Deliberation']['seance_id'] = $seance_id;
		$this->save($this->data);
	}

	function changeClassification($delib_id, $classification){
            $this->id = $delib_id;
            $this->saveField('num_pref', $classification);
	}

        function changeDateAR($delib_id, $dateAR){
            $this->id = $delib_id;
            $this->saveField('dateAR', $dateAR);
        }

        function getModelId($delib_id) {
             $data = $this->read(null, $delib_id);
             $seance = $this->Seance->read(null, $data['Deliberation']['seance_id'] );
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
		// lecture en base de donn�es
		$this->Behaviors->attach('Containable');
		$delib=$this->find('first', array(
			'contain' => array('Annex', 'Infosup'),
			'conditions' => array('Deliberation.id'=>$id)));

		// maj de l'etat de la delib dans la table deliberations
		$delib['Deliberation']['etat']=-1; //etat -1 : refuse
		// Retour de la position a 0 pour ne pas qu'il y ait de confusion
		//$delib['Deliberation']['position']=0;
		$delib['Deliberation']['id']=$id;
		$this->save($delib['Deliberation']);

		// cr�ation de la nouvelle version
		$this->create();
		$delib['Deliberation']['id']='';
		$delib['Deliberation']['etat']=0;
		$delib['Deliberation']['anterieure_id']=$id;
		$delib['Deliberation']['date_envoi']=0;
//		$delib['Deliberation']['circuit_id']=0;
		$delib['Deliberation']['created']=date('Y-m-d H:i:s', time());
		$delib['Deliberation']['modified']=date('Y-m-d H:i:s', time());
		$this->save($delib['Deliberation']);
		$delib_id = $this->id;

		// copie des annexes du projet refus� vers le nouveau projet
		$annexes = $delib['Annex'];
		foreach($annexes as $annexe) {
			$tmp['Annex']= $annexe;
			$tmp['Annex']['id']=null;
			$tmp['Annex']['model']='Deliberation';
			$tmp['Annex']['foreign_key']= $delib_id;
			$this->Annex->save( $tmp, false);
		}

		// copie des infos suppl�mentaires du projet refus� vers le nouveau projet
		$infoSups = $delib['Infosup'];
		foreach($infoSups as $infoSup) {
			$infoSup['id'] = null;
			$infoSup['foreign_key'] = $delib_id;
			$infoSup['model'] = 'Deliberation';
			$this->Infosup->save($infoSup, false);
		}

		// copie des d�lib�rations rattach�es vers le nouveau projet
		$delibRattachees = $this->find('all', array(
			'contain' => array('Annex'),
			'conditions'=>array('Deliberation.parent_id'=>$id)));
		foreach($delibRattachees as $delibRattachee) {
			// maj de l'etat de la delib dans la table deliberations
			$delibRattachee['Deliberation']['etat']=-1; //etat -1 : refuse
			// Retour de la position a 0 pour ne pas qu'il y ait de confusion
			//$delibRattachee['Deliberation']['position']=0;
			$this->save($delibRattachee['Deliberation']);
	
			// cr�ation de la nouvelle version
			$this->create();
			$delibRattachee['Deliberation']['id']='';
			$delibRattachee['Deliberation']['parent_id']=$delib_id;
			$delibRattachee['Deliberation']['etat']=0;
			$delibRattachee['Deliberation']['anterieure_id']=0;
			$delibRattachee['Deliberation']['date_envoi']=0;
	//		$delibRattachee['Deliberation']['circuit_id']=0;
			$delibRattachee['Deliberation']['created']=date('Y-m-d H:i:s', time());
			$delibRattachee['Deliberation']['modified']=date('Y-m-d H:i:s', time());
			$this->save($delibRattachee['Deliberation']);
			$delibRattachee_id = $this->id;
	
			// copie des annexes du projet refus� vers le nouveau projet
			$annexes = $delibRattachee['Annex'];
			foreach($annexes as $annexe) {
				$tmp['Annex']= $annexe;
				$tmp['Annex']['id']=null;
				$tmp['Annex']['model']='Deliberation';
				$tmp['Annex']['foreign_key']= $delibRattachee_id ;
				$this->Annex->save( $tmp, false);
			}
		}
	}

       function canSave(){
           return $this->Seance->NaturecanSave($this->data['Deliberation']['seance_id'], $this->data['Deliberation']['nature_id']);
       }

       function genererRecherche($projets, $model_id=1, $format=0){
            include_once ('vendors/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
	    include_once ('vendors/GEDOOo/phpgedooo/GDO_AxisTitleType.class');

            include_once ('controllers/components/progress.php');
	    include_once ('controllers/components/conversion.php');

            $this->Conversion = new ConversionComponent;
	    $this->Progress = new ProgressComponent;

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
            $urlpath =  'http://'.$_SERVER['HTTP_HOST'].$dyn_path.$nomFichier.".$format";

            $content = $this->Seance->Typeseance->Modelprojet->find('first', array('conditions'=> array('id' => $model_id),
                                                                                   'fields'    => array('content')));
            $oTemplate = new GDO_ContentType("",
                                "modele.odt",
                                "application/vnd.oasis.opendocument.text",
                                "binary",
                                $content['Modelprojet']['content']);

            $oMainPart = new GDO_PartType();
            $nbProjets = count($projets);
            if ($nbProjets > 1) {
                $i =0;
                $blocProjets = new GDO_IterationType("Projets");
                $this->Progress->start(200, 100,200, '#000000','#000000','#006699');
            }
            foreach ($projets as $projet) {
                $isDelib = false;
                if ($projet['Deliberation']['etat']>=3)
                     $isDelib = true;
                $oDevPart = new GDO_PartType();
                $oDevPart = $this->makeBalisesProjet($projet,  $oDevPart, $isDelib);
                if ($nbProjets > 1)
                    $blocProjets->addPart($oDevPart);
            }
            if ( $nbProjets > 1)
                $oMainPart->addElement($blocProjets);
            else
                $oMainPart =  $oDevPart;

            $this->Progress->at(100,"G�n�ration en cours...");

            $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
            $oFusion->process();

	    $oFusion->SendContentToFile($path.$nomFichier.".odt");      
            $content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", $format);
            $this->Gedooo->createFile($path, $nomFichier.".$format", $content);

            $this->Progress->endPopup($urlpath);
            $this->Progress->end($_SERVER['HTTP_REFERER']);
        }

        function makeBalisesProjet ($delib, $oMainPart, $isDelib, $u=null, $isPV=false)  {
           include_once (ROOT.DS.APP_DIR.DS.'controllers/components/gedooo.php');
	   include_once (ROOT.DS.APP_DIR.DS.'controllers/components/date.php');
	   include_once (ROOT.DS.APP_DIR.DS.'controllers/components/conversion.php');

           $this->Conversion = new ConversionComponent;
           $this->Date = new DateComponent;
           $this->Gedooo = new GedoooComponent;

               $dyn_path = "/files/generee/projet/".$delib['Deliberation']['id']."/";
	       $path = WEBROOT_PATH.$dyn_path;
               
               if ($delib['Deliberation']['seance_id'] != 0 ) {
                   $oMainPart->addElement(new GDO_FieldType('heure_seance',                $this->Date->Hour($delib['Seance']['date']),     'text'));
                   $seance = $this->Seance->find('first', array(
						 'conditions' => array('Seance.id' =>$delib['Seance']['id'])));
		   $oMainPart->addElement(new GDO_FieldType('type_seance',                utf8_encode($seance['Typeseance']['libelle']),        'text'));

                   
                   $oMainPart->addElement(new GDO_FieldType('date_seance',                $this->Date->frDate($seance['Seance']['date']),       'date'));
                   $oMainPart->addElement(new GDO_FieldType('hh_seance',           $this->Date->Hour($seance['Seance']['date'], 'hh'), 'string'));
                   $oMainPart->addElement(new GDO_FieldType('mm_seance',           $this->Date->Hour($seance['Seance']['date'], 'mm'), 'string'));
                   $oMainPart->addElement(new GDO_FieldType('date_convocation',                 $this->Date->frDate($seance['Seance']['date_convocation']),   'date'));
                   $date_lettres =  $this->Date->dateLettres(strtotime($seance['Seance']['date']));
		   $oMainPart->addElement(new GDO_FieldType('date_seance_lettres',         utf8_encode($date_lettres),                      'text')); 
                   foreach($seance['Infosup'] as $champs) {
                       $oMainPart->addElement($this->_addField($champs, $u, $delib['Seance']['id'], 'Seance'));
                   }
               }
          
               $titre = utf8_encode($delib['Deliberation']['titre']);
               $titre =  str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC), $titre);
               $oMainPart->addElement(new GDO_FieldType('titre_projet',                $titre,    'text'));

               $objet = utf8_encode($delib['Deliberation']['objet']);
               $objet = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC), $objet);
	       $oMainPart->addElement(new GDO_FieldType('objet_projet',                $objet,     'text'));
	       $oMainPart->addElement(new GDO_FieldType('libelle_projet',              $objet,    'text'));

               $objet_delib = utf8_encode($delib['Deliberation']['objet_delib']);
               $objet_delib = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC), $objet_delib);
	       $oMainPart->addElement(new GDO_FieldType('objet_delib',                $objet_delib,     'text'));
	       $oMainPart->addElement(new GDO_FieldType('libelle_delib',              $objet_delib,    'text'));

               $oMainPart->addElement(new GDO_FieldType('nature_projet', utf8_encode($delib['Nature']['libelle']),     'text'));
               $oMainPart->addElement(new GDO_FieldType('position_projet',             utf8_encode($delib['Deliberation']['position']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('identifiant_projet',          utf8_encode($delib['Deliberation']['id']),       'text'));
               $oMainPart->addElement(new GDO_FieldType('identifiant_seance',          utf8_encode($delib['Deliberation']['seance_id']),'text'));
               $oMainPart->addElement(new GDO_FieldType('numero_deliberation',         utf8_encode($delib['Deliberation']['num_delib']),'text'));
               $oMainPart->addElement(new GDO_FieldType('classification_deliberation', utf8_encode($delib['Deliberation']['num_pref']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('service_emetteur',            utf8_encode($delib['Service']['libelle']) ,      'text'));
               $oMainPart->addElement(new GDO_FieldType('theme_projet',                utf8_encode($delib['Theme']['libelle']),         'text'));
               $oMainPart->addElement(new GDO_FieldType('T1_theme',                    utf8_encode($delib['Theme']['libelle']),         'text'));
               $oMainPart->addElement(new GDO_FieldType('critere-trie_theme',          utf8_encode($delib['Theme']['order']),         'text'));
 
                // Information sur le rapporteur
               $oMainPart->addElement(new GDO_FieldType('salutation_rapporteur',       utf8_encode($delib['Rapporteur']['salutation']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('prenom_rapporteur',           utf8_encode($delib['Rapporteur']['prenom']),     'text'));
               $oMainPart->addElement(new GDO_FieldType('nom_rapporteur',              utf8_encode($delib['Rapporteur']['nom']),        'text'));
               $oMainPart->addElement(new GDO_FieldType('titre_rapporteur',            utf8_encode($delib['Rapporteur']['titre']),      'text'));
               $oMainPart->addElement(new GDO_FieldType('position_rapporteur',         utf8_encode($delib['Rapporteur']['position']),   'text'));
               $oMainPart->addElement(new GDO_FieldType('email_rapporteur',            utf8_encode($delib['Rapporteur']['email']),      'text'));
               $oMainPart->addElement(new GDO_FieldType('telmobile_rapporteur',        utf8_encode($delib['Rapporteur']['telmobile']),  'text'));
               $oMainPart->addElement(new GDO_FieldType('telfixe_rapporteur',          utf8_encode($delib['Rapporteur']['telfixe']),    'text'));
               $oMainPart->addElement(new GDO_FieldType('date_naissance_rapporteur',   utf8_encode($delib['Rapporteur']['date_naissance']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('adresse1_rapporteur',         utf8_encode($delib['Rapporteur']['adresse1']),   'text'));
               $oMainPart->addElement(new GDO_FieldType('adresse2_rapporteur',         utf8_encode($delib['Rapporteur']['adresse2']),   'text'));
               $oMainPart->addElement(new GDO_FieldType('cp_rapporteur',               utf8_encode($delib['Rapporteur']['cp']),         'text'));
               $oMainPart->addElement(new GDO_FieldType('ville_rapporteur',            utf8_encode($delib['Rapporteur']['ville']),      'text'));
               $oMainPart->addElement(new GDO_FieldType('note_rapporteur',             utf8_encode($delib['Rapporteur']['note']),       'text'));

               // Information sur le president
               $president = $this->Rapporteur->read(null, $delib['Seance']['president_id'] );
               $oMainPart->addElement(new GDO_FieldType('nom_president', utf8_encode($president['Rapporteur']['nom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('prenom_president', utf8_encode($president['Rapporteur']['prenom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('salutation_president', utf8_encode($president['Rapporteur']['salutation']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('titre_president', utf8_encode($president['Rapporteur']['titre']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('note_president', utf8_encode($president['Rapporteur']['note']), 'text'));
 
               // Information sur le secretaire
               $secretaire = $this->Rapporteur->read(null, $delib['Seance']['secretaire_id'] );
               $oMainPart->addElement(new GDO_FieldType('nom_secretaire', utf8_encode($secretaire['Rapporteur']['nom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('prenom_secretaire', utf8_encode($secretaire['Rapporteur']['prenom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('salutation_secretaire', utf8_encode($secretaire['Rapporteur']['salutation']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('titre_secretaire', utf8_encode($secretaire['Rapporteur']['titre']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('note_secretaire', utf8_encode($secretaire['Rapporteur']['note']), 'text'));

               // Informations sur le r�dacteur
               $oMainPart->addElement(new GDO_FieldType('prenom_redacteur', utf8_encode($delib['Redacteur']['prenom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('nom_redacteur', utf8_encode($delib['Redacteur']['nom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('email_redacteur', utf8_encode($delib['Redacteur']['email']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('telmobile_redacteur', utf8_encode($delib['Redacteur']['telmobile']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('telfixe_redacteur', utf8_encode($delib['Redacteur']['telfixe']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('note_redacteur', utf8_encode($delib['Redacteur']['note']), 'text'));


	       // Informations sur la d�lib�ration
               
	       $nb_votant = $delib['Deliberation']['vote_nb_oui']+$delib['Deliberation']['vote_nb_abstention']+$delib['Deliberation']['vote_nb_non'];
	       if (($delib['Deliberation']['etat'] == 3 ) &&  ($delib['Deliberation']['vote_nb_oui']==0 )) 
		    $oMainPart->addElement(new GDO_FieldType('acte_adopte',  '1', 'text'));
               else {
		   $oMainPart->addElement(new GDO_FieldType('acte_adopte',  '0', 'text'));
                   $oMainPart->addElement(new GDO_FieldType('nombre_pour',  utf8_encode($delib['Deliberation']['vote_nb_oui'])   , 'text'));
                   $oMainPart->addElement(new GDO_FieldType('nombre_abstention', utf8_encode( $delib['Deliberation']['vote_nb_abstention']), 'text'));
                   $oMainPart->addElement(new GDO_FieldType('nombre_contre',  utf8_encode($delib['Deliberation']['vote_nb_non']), 'text'));
	           $oMainPart->addElement(new GDO_FieldType('nombre_sans_participation', utf8_encode( $delib['Deliberation']['vote_nb_retrait']), 'text'));
               }
               $oMainPart->addElement(new GDO_FieldType('nombre_votant', $nb_votant, 'text'));
               $oMainPart->addElement(new GDO_FieldType('date_reception',  utf8_encode($delib['Deliberation']['dateAR']), 'text'));
               //$oMainPart->addElement(new GDO_FieldType('commentaire_vote',  utf8_encode($delib['Deliberation']['vote_commentaire']), 'text'));
                    if (isset($delib['Deliberation']['vote_commentaire'])) {
                       $filename = $path."commentaire_vote.html";
                       $vote_commentaire = "<html><head></head><body><p>".nl2br($delib['Deliberation']['vote_commentaire'])."</p></body></html>";
                       $filepath_comm = $this->Gedooo->createFile($path, "commentaire.html",  $vote_commentaire);
		       $content = $this->Conversion->convertirFichier($filepath_comm, "odt");
		       $oMainPart->addElement(new GDO_ContentType('commentaire_vote', 'commentaire.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
                   }


               $commentaires = new GDO_IterationType("Commentaires");
               foreach($delib['Commentaire'] as $commentaire) {
                   $oDevPart = new GDO_PartType();
		   if ($commentaire['commentaire_auto']==0){
                       $oDevPart->addElement(new GDO_FieldType("texte_commentaire", utf8_encode($commentaire['texte']), "text"));
                       $commentaires->addPart($oDevPart);
		   }
                }
               @$oMainPart->addElement($commentaires);

               $avisCommission = new GDO_IterationType("AvisCommission");
               foreach($delib['Commentaire'] as $commentaire) {
                   $oDevPart = new GDO_PartType();
		   if ($commentaire['commentaire_auto']==1) {
                       $oDevPart->addElement(new GDO_FieldType("avis", utf8_encode($commentaire['texte']), "text"));
                       $avisCommission->addPart($oDevPart);
		   }
               }
               @$oMainPart->addElement($avisCommission);

               @$historique =  new GDO_IterationType("Historique");
	       foreach($delib['Historique'] as $histo) {
                   $oDevPart = new GDO_PartType();
                   $oDevPart->addElement(new GDO_FieldType("log", utf8_encode($histo['commentaire']), "text"));
                   $historique->addPart($oDevPart);
               }
               @$oMainPart->addElement($historique);
 
               if (!empty($delib['Infosup'])) {
                   foreach($delib['Infosup'] as  $champs)
                       $oMainPart->addElement($this->_addField($champs, $u, $delib['Deliberation']['id'], 'Deliberation'));
               }
               else {
                   $defs = $this->Infosup->Infosupdef->find('all', array('conditions'=>array('model' => 'Deliberation'), 'recursive' => -1));
                   foreach($defs as $def) {
                        $oMainPart->addElement(new GDO_FieldType($def['Infosupdef']['code'],  utf8_encode(' '), 'text')) ;
                   }
               }
 
               @$Multi =  new GDO_IterationType("Deliberations");
               if (!empty($delib['Multidelib'])) {
                   foreach($delib['Multidelib'] as $multidelib ){
                       $oDevPart = new GDO_PartType();
                       $oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", utf8_encode($multidelib['objet']), "text"));
                       $oDevPart->addElement(new GDO_FieldType("id_multi_delib",      utf8_encode($multidelib['id']),    "text"));
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
                   include_once (ROOT.DS.APP_DIR.DS.'vendors/GEDOOo/phpgedooo/GDO_Utility.class');
                   $u = new GDO_Utility();

                   if (!$this->Gedooo->checkPath($path))
                       die("Webdelib ne peut pas ecrire dans le repertoire : $path");

                   $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$dyn_path;
                   if (!empty($delib['Deliberation']['texte_projet'])) {
                       $oMainPart->addElement(new GDO_ContentType('texte_projet', 'text_projet.odt' ,'application/vnd.oasis.opendocument.text',  'binary', $delib['Deliberation']['texte_projet']));
                  }
                  if (!empty($delib['Deliberation']['deliberation'])) {
                      $oMainPart->addElement(new GDO_ContentType('texte_deliberation', 'td.odt', 'application/vnd.oasis.opendocument.text' ,   'binary', $delib['Deliberation']['deliberation']));
                  } 
                  if (!empty($delib['Deliberation']['texte_synthese'])) {
                      $oMainPart->addElement(new GDO_ContentType('note_synthese', 'ns.odt', 'application/vnd.oasis.opendocument.text' , 'binary', $delib['Deliberation']['texte_synthese']));
                   }
                   if (!empty($delib['Deliberation']['debat'])) {
                       $oMainPart->addElement(new GDO_ContentType('debat_deliberation', 'debat.odt',  'application/vnd.oasis.opendocument.text' , 'binary' , $delib['Deliberation']['debat']));
                   }
                   if (!empty($delib['Deliberation']['commission'])) {
                       $oMainPart->addElement(new GDO_ContentType('debat_commission', 'debat_commission.odt',  'application/vnd.oasis.opendocument.text', 'binary', $delib['Deliberation']['commission']));
                   }

	       }

	       $annexe_ids = $this->Annex->getAnnexesIFromDelibId($delib['Deliberation']['id'], 0, 1);
	       $oMainPart->addElement(new GDO_FieldType('nombre_annexe', count($annexe_ids), 'text'));

	       @$annexes =  new GDO_IterationType("Annexes");
	       foreach($annexe_ids as $annexe_id) {
                   $annexe = $this->Annex->find('first', array ('conditions' => array('Annex.id' => $annexe_id['Annex']['id']),
                                                               'recursive'  => -1));
                   if (($annexe['Annex']['joindre_fusion'] == 1) && ($annexe['Annex']['filetype'] == "application/vnd.oasis.opendocument.text")) {  
                       $oDevPart = new GDO_PartType();
                       $oDevPart->addElement(new GDO_FieldType('nom_fichier',  utf8_encode($annexe['Annex']['filename']), 'text'));
                       $oDevPart->addElement(new GDO_FieldType('titre_annexe', utf8_encode($annexe['Annex']['titre']), 'text'));
                       $oDevPart->addElement(new GDO_ContentType('fichier',    utf8_encode($annexe['Annex']['filename']),  'application/vnd.oasis.opendocument.text', 'binary', $annexe['Annex']['data']));
		       $annexes->addPart($oDevPart);
		   }
	       }
               @$oMainPart->addElement($annexes);



               if (!$isDelib)
                  return $oMainPart;
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
               return $oMainPart;

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
                $oDevPart->addElement(new GDO_FieldType("nom_acteur".$type, utf8_encode($acteur['nom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type, utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,utf8_encode($acteur['salutation_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("titre_acteur".$type, utf8_encode($acteur['titre_acteur']), "text"));
                if ($acteur['date_naissance_acteur'] != null)
                    $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type,  $this->Date->frDate($acteur['date_naissance_acteur']), "date"));
                else
                      $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, '', "date"));

                $oDevPart->addElement(new GDO_FieldType("adresse1_acteur".$type, utf8_encode($acteur['adresse1_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("adresse2_acteur".$type, utf8_encode($acteur['adresse2_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("cp_acteur".$type, utf8_encode($acteur['cp_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("ville_acteur".$type, utf8_encode($acteur['ville_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("email_acteur".$type, utf8_encode($acteur['email_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("telfixe_acteur".$type,utf8_encode($acteur['telfixe_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("telmobile_acteur".$type,  utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("note_acteur".$type, utf8_encode($acteur['note_acteur']), "text"));
                if ($isMandate) {
                    $oDevPart->addElement(new GDO_FieldType('nom_acteur_mandate', utf8_encode($acteur['nom_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('prenom_acteur_mandate', utf8_encode($acteur['prenom_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('salutation_acteur_mandate', utf8_encode($acteur['salutation_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('titre_acteur_mandate', utf8_encode($acteur['titre_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('date_naissance_acteur_mandate', utf8_encode($acteur['date_naissance_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('adresse1_acteur_mandate', utf8_encode($acteur['adresse1_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('adresse2_acteur_mandate', utf8_encode($acteur['adresse2_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('cp_acteur_mandate', utf8_encode($acteur['cp_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('ville_acteur_mandate', utf8_encode($acteur['ville_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('email_acteur_mandate', utf8_encode($acteur['email_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('telfixe_acteur_mandate', utf8_encode($acteur['telfixe_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('telmobile_acteur_mandate', utf8_encode($acteur['telmobile_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('note_acteur_mandate', utf8_encode($acteur['note_mandate']), "text"));
                }
                $acteurs->addPart($oDevPart);
            }
            return $acteurs;

            }
        }

        function _addField($champs, $u, $id, $model='Deliberation') {
            $champs_def = $this->Infosup->Infosupdef->read(null, $champs['infosupdef_id']);

            if(($champs_def['Infosupdef']['type'] == 'list' )&&($champs['text']!= "")) {
                $tmp= $this->Infosup->Infosupdef->Infosuplistedef->find('id = '.$champs['text'], 'nom', null, -1);
                $champs['text'] = $tmp['Infosuplistedef']['nom'];
            }
            elseif (($champs_def['Infosupdef']['type'] == 'list' )&&($champs['text']== ""))
                 return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode(' '), 'text'));

            if ($champs['text'] != '') { 
                return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode($champs['text']), 'text'));
            }
	    elseif ($champs['date'] != '0000-00-00') {
                include_once ('controllers/components/date.php');
                $this->Date = new DateComponent;
		return  (new GDO_FieldType($champs_def['Infosupdef']['code'], $this->Date->frDate($champs['date']),   'date'));
             }
             elseif ($champs['file_size'] != 0 ) {
                 $name = utf8_decode(str_replace(" ", "_", $champs['file_name']));
                 return (new GDO_ContentType($champs_def['Infosupdef']['code'], $name  ,'application/vnd.oasis.opendocument.text',  'binary', $champs['content']));
             }
             elseif ((!empty($champs['content'])) && ($champs['file_size']==0) ) {
                 include_once ('controllers/components/gedooo.php');
                 include_once ('controllers/components/conversion.php');

                 $this->Gedooo = new GedoooComponent;
                 $this->Conversion = new ConversionComponent; 
                 if ( $model == 'Deliberation' ) { 
                     $filename = WEBROOT_PATH."/files/generee/projet/$id/".$champs_def['Infosupdef']['code'].".html";
                     $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/projet/$id/", $champs_def['Infosupdef']['code'].".html", $champs['content']);
                     $content = $this->Conversion->convertirFichier($filename, "odt");
		     return (new GDO_ContentType($champs_def['Infosupdef']['code'], $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));
                 }
		 elseif ( $model == 'Seance' ) {
                     $filename = WEBROOT_PATH."/files/generee/seance/$id/".$champs_def['Infosupdef']['code'].".html";
                     $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/seance/$id/", $champs_def['Infosupdef']['code'].".html", $champs['content']);
                     $content = $this->Conversion->convertirFichier($filename, "odt");
                     return (new GDO_ContentType($champs_def['Infosupdef']['code'], $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));

                 } 
             }
            elseif  ($champs['text'] == '' )
                 return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode(' '), 'text'));
        }

        function _url2pathImage($url) {
            $content = str_replace('http://webdelib/app/', Configure::read('WEBDELIB_PATH'), $url);
            $content = str_replace( '\"', '"', $content);
            return $content;
        }
/**
 * op�rations post sauvegarde des d�lib�rations :
 * - gestion des s�ances et de l'ordre des projets
 * - mise � jour des d�lib�rations rattach�es
 * @param integer $delibId id du projet � traiter
 * @param integer $oldSeanceId id de la s�ance pr�c�dente avant sauvegarde
 */
function majDelibRatt($delibId, $oldSeanceId) {
	// initialisation
	$position = 0;
	$majPosition = false;
	$majFields = array(
		'nature_id', 'theme_id', 'service_id', 'redacteur_id', 'rapporteur_id',
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

	// faut-il mettre a jour la position dans la s�ance
	if (!empty($delib['Deliberation']['seance_id']) && (empty($oldSeanceId)))
		// attribution d'une s�ance
		$majPosition = true;
	elseif (!empty($delib['Deliberation']['seance_id']) && (!empty($oldSeanceId) && ($delib['Deliberation']['seance_id'] !== $oldSeanceId)))
		// changement de s�ance
		$majPosition = true;
	elseif (empty($delib['Deliberation']['seance_id']) && !empty($oldSeanceId))
		// suppression de la s�ance
		$majPosition = true;

	if (!empty($delib['Deliberation']['seance_id']))
 		$position = $this->getLastPosition($delib['Deliberation']['seance_id'])-1;

	// mise � jour des d�lib�rations rattach�es
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
 * reordonne les posistions de la s�ance $seanceId
 */
function reOrdonnePositionSeance($seanceId) {
	// initialisations
	$position = 0;
	// lecture des delibs de la s�ance
	$delibs = $this->find('all', array(
		'recursive' => -1,
		'fields' => array('id', 'position'),
		'conditions' => array(
			'etat <>' => -1,
			'seance_id' => $seanceId),
		'order' => 'position ASC'));
	// pour toutes les d�libs
	foreach($delibs as $delib) {
		$position++;
		if ($position != $delib['Deliberation']['position'])
			$this->save(array('id'=>$delib['Deliberation']['id'], 'position'=>$position), array('validate' => false, 'callbacks' => false));
	}
}

/**
 * sauvergarde des d�lib�rations attach�es
 * @param integer $parentId id de la d�lib�ration principale
 * @param array $delib d�lib�ration rattach�e retourn� par le formulaire 'edit'
 */
function saveDelibRattachees($parentId, $delib) {
	// initialisations
	$newDelib = array();
	if (!isset($delib['objet'])) {
		$this->Session->setFlash('Libell� obligatoire.', 'growl', array('type'=>'erreur'));
		return false;
	}

	if (isset($delib['id'])) {
		// modification
		$newDelib['Deliberation']['id'] = $delib['id'];
	} else {
		// ajout
		$newDelib = $this->create();
		$newDelib['Deliberation']['parent_id'] = $parentId;
	}

	$newDelib['Deliberation']['objet'] = $delib['objet'];
	if (Configure::read('GENERER_DOC_SIMPLE')){
		$newDelib['Deliberation']['deliberation'] = $delib['deliberation'];
	} else {
		if (isset($delib['deliberation'])) {
 			$newDelib['Deliberation']['objet_delib'] = $delib['objet'];
 			$newDelib['Deliberation']['deliberation_name'] = $delib['deliberation']['name'];
			$newDelib['Deliberation']['deliberation_size'] = $delib['deliberation']['size'];
			$newDelib['Deliberation']['deliberation_size'] = $delib['deliberation']['size'];
			if (empty($delib['deliberation']['tmp_name']))
				$newDelib['Deliberation']['deliberation'] = '';
			else
				$newDelib['Deliberation']['deliberation'] = file_get_contents($delib['deliberation']['tmp_name']);
		}
	}

	if(!$this->save($newDelib['Deliberation'], false)) {
		$this->Session->setFlash('Erreur lors de la sauvegarde des d�lib�rations rattach�es.', 'growl', array('type'=>'erreur'));
		return false;
	}
	return $this->id;
}

/**
 * fonction r�cursive de suppression de la d�lib�ration $delib8d, de ses versions ant�rieures et de ses d�lib�rations rattach�es
 * @param integer $delibId id de la d�lib � supprimer
 */
function supprimer($delibId) {
	// lecture de la d�lib en base
    $delib = $this->find('first', array(
    	'recursive' => -1,
		'fields' => array('anterieure_id', 'seance_id', 'parent_id'),
		'conditions' => array('id' => $delibId)));
	if (empty($delib)) return;

	// suppression de la d�lib
	$this->del($delibId);
	// suppression du r�pertoire des docs
	$repFichier = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$delibId.DS;
	$this->rmDir($repFichier);
	// gestion de la s�ance
	if (!empty($delib['Deliberation']['seance_id'])) {
		$this->reOrdonnePositionSeance($delib['Deliberation']['seance_id']);
	}

	// pour les d�lib rattach�es, le traitement finit ici
	if (!empty($delib['Deliberation']['parent_id'])) return;

	// suppression des d�lib rattach�es
	$delibRattachees = $this->find('all', array(
    	'recursive' => -1,
		'fields' => array('id'),
		'conditions' => array('parent_id' => $delibId)));
	foreach($delibRattachees as $delibRattachee) {
		$this->supprimer($delibRattachee['Deliberation']['id']);
	}

	// suppression des d�lib ant�rieures
	if ( $delib['Deliberation']['anterieure_id'] != 0)
		$this->supprimer($delib['Deliberation']['anterieure_id']);
}

/**
 * Supprime un r�pertoire et son contenu
 * @param string $dossier chemin du r�pertoire � supprimer
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

}
?>
