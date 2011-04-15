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
                          'message' => "Cette séance ne peux pas enregistrer cette nature d'acte"
                        )
                )
	);

	//dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
	var $belongsTo = array(
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
                 'Historique' =>array(
                        'className'    => 'Historique',
                        'foreignKey'   => 'delib_id'),

		'Traitement'=>array(
			'className'    => 'Cakeflow.Traitement',
			'foreignKey'   => 'target_id'), 

		'Annex'=>array(
			'className'    => 'Annex',
			'foreignKey'   => 'deliberation_id',
			'dependent' => true),
		'Commentaire'=>array(
			'className'    => 'Commentaire',
			'foreignKey'   => 'delib_id'),
		'Infosup'=>array(
			'dependent' => true)
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
                                                    'fields'     => array('etat', 'redacteur_id')));
		if (empty($delib)) return false;

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
 * retourne le libellé correspondant à l'état $etat des projets et délibérations
 * si $codesSpeciaux = true, retourne les libellés avec les codes spéciaux des accents
 * si $codesSpeciaux = false, retourne les libellés sans les accents (listes)
 */
	function libelleEtat($etat, $codesSpeciaux=true) {
 		switch($etat) {
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
		$this->data['Deliberation']['id']=$delib_1id;
		$this->data['Deliberation']['seance_id'] = $seance_id;
		$this->save($this->data);
	}

	function changeClassification($delib_id, $classification){
		$this->data = $this->read(null, $delib_id);
		$this->data['Deliberation']['id']=$delib_id;
		$this->data['Deliberation']['num_pref'] = $classification;
		$this->save($this->data);
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
             // maj de l'etat de la delib dans la table deliberations
             $delib=$this->read(null, $id);
             $delib['Deliberation']['etat']=-1; //etat -1 : refuse

             // Retour de la position a 0 pour ne pas qu'il y ait de confusion
             $delib['Deliberation']['position']=0;
             $delib['Deliberation']['id']=$id;
             $this->save($delib['Deliberation']);

             //enregistrement d'une nouvelle delib
             $this->create();
             $delib['Deliberation']['id']='';
             $delib['Deliberation']['etat']=0;
             $delib['Deliberation']['anterieure_id']=$id;
             $delib['Deliberation']['date_envoi']=0;
//             $delib['Deliberation']['circuit_id']=0;
             $delib['Deliberation']['created']=date('Y-m-d H:i:s', time());
             $delib['Deliberation']['modified']=date('Y-m-d H:i:s', time());
             $this->save($delib['Deliberation']);
 
             $delib_id = $this->getLastInsertId();
             // Copie des annexes du projet refusé vers le nouveau projet
             $annexes = $delib['Annex'];
             foreach($annexes as $annexe) {
		 $tmp['Annex']= $annexe;
		 $tmp['Annex']['id']=null;
		 $tmp['Annex']['deliberation_id']= $delib_id ;
		 $this->Annex->save( $tmp, false);
             }
             // Copie des infos supplémentaires du projet refusé vers le nouveau projet
             $infoSups = $delib['Infosup'];
             foreach($infoSups as $infoSup) {
                 $infoSup['id'] = null;
                 $infoSup['deliberation_id'] = $delib_id;
                 $this->Infosup->save($infoSup, false);
             }
             // Copie des commentaires du projet refusé vers le nouveau projet
            /*
             $commentaires = $delib['Commentaire'];
             foreach($commentaires as $commentaire) {
                 $commentaire['id'] = null;
                 $commentaire['created'] = null;
                 $commentaire['modified'] = null;
                 $commentaire['delib_id'] = $delib_id;
                 $this->Commentaire->save($commentaire, false);
	     }
             */
        }

       function canSave(){
           return $this->Seance->NaturecanSave($this->data['Deliberation']['seance_id'], $this->data['Deliberation']['nature_id']);
       }

       function genererRecherche($projets, $model_id=10023){
            include_once ('vendors/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_AxisTitleType.class');
            
            $sMimeType = "application/pdf";
            $content = $this->Seance->Typeseance->Modelprojet->find('first', array('conditions'=> array('id' => $model_id),
                                                                                   'fields'    => array('content')));
            $oTemplate = new GDO_ContentType("",
                                "modele.odt",
                                "application/vnd.oasis.opendocument.text",
                                "binary",
                                $content['Modelprojet']['content']);

            $oMainPart = new GDO_PartType();

            $blocProjets = new GDO_IterationType("Projets");
            foreach ($projets as $projet) {
                $isDelib = false;
                $oDevPart = new GDO_PartType();
                if ($projet['Deliberation']['etat'] >= 3)
                    $isDelib = true;
                $oDevPart = $this->makeBalisesProjet($projet,  $oDevPart, $isDelib);
                $blocProjets->addPart($oDevPart);
            }
            $oMainPart->addElement($blocProjets);

            $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
            $oFusion->process();
            $oFusion->SendContentToClient();
        }

        function makeBalisesProjet ($delib, $oMainPart, $isDelib, $u=null, $isPV=false)  {
               if (($delib['Deliberation']['seance_id'] != 0 )&& ($isPV==false)) {
  //                 $oMainPart->addElement(new GDO_FieldType('date_seance',                 $this->Date->frDate($delib['Seance']['date']),   'date'));
 //                  $date_lettres =  $this->Date->dateLettres(strtotime($delib['Seance']['date']));
 //                  $oMainPart->addElement(new GDO_FieldType('date_seance_lettres',         utf8_encode($date_lettres),                      'text'));
//                   $oMainPart->addElement(new GDO_FieldType('heure_seance',                $this->Date->Hour($delib['Seance']['date']),     'text'));
                   $seance = $this->Seance->find('first', array(
                                                 'conditions' => array(
                                                 'Seance.id' =>$delib['Seance']['id'])));
                   $oMainPart->addElement(new GDO_FieldType('type_seance',                utf8_encode($seance['Typeseance']['libelle']),    'text'));
                   $oMainPart->addElement(new GDO_FieldType('commentaire_seance',         utf8_encode($seance['Seance']['commentaire']),    'text'));

               }
               $titre = utf8_encode($delib['Deliberation']['titre']);
               $titre =  str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC), $titre);
               $oMainPart->addElement(new GDO_FieldType('titre_projet',                $titre,    'text'));

               $objet = utf8_encode($delib['Deliberation']['objet']);
               $objet = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC), $objet);

               $oMainPart->addElement(new GDO_FieldType('objet_projet',                $objet,     'text'));
               $oMainPart->addElement(new GDO_FieldType('libelle_projet',              $objet,    'text'));
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

               if (Configure::read('GENERER_DOC_SIMPLE')) {
                   if (isset($delib['Deliberation']['texte_projet']))
                       $oMainPart->addElement(new GDO_ContentType('texte_projet', '', 'text/html', 'text',       '<small></small>'.$delib['Deliberation']['texte_projet']));
                   if (isset($delib['Deliberation']['texte_synthese']))
                       $oMainPart->addElement(new GDO_ContentType('note_synthese', '', 'text/html', 'text',      '<small></small>'.$delib['Deliberation']['texte_synthese']));
                   if (isset($delib['Deliberation']['deliberation']))
                       $oMainPart->addElement(new GDO_ContentType('texte_deliberation', '', 'text/html', 'text', '<small></small>'.$delib['Deliberation']['deliberation']));
                   if (isset($delib['Deliberation']['debat']))
                       $oMainPart->addElement(new GDO_ContentType('debat_deliberation', '', 'text/html', 'text', '<small></small>'.$delib['Deliberation']['debat']));
                   if (isset($delib['Deliberation']['commission']))
                       $oMainPart->addElement(new GDO_ContentType('debat_commission', '', 'text/html', 'text',   '<small></small>'.$delib['Deliberation']['commission']));
               }
               else {
                   $dyn_path = "/files/generee/deliberations/".$delib['Deliberation']['id']."/";
                   $path = WEBROOT_PATH.$dyn_path;

                   if (!$this->Gedooo->checkPath($path))
                       die("Webdelib ne peut pas ecrire dans le repertoire : $path");

                   $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;

                   if ($delib['Deliberation']['texte_projet_name']== "") {
                       $nameTP = "vide";
                       $oMainPart->addElement(new GDO_ContentType('texte_projet', '', 'text/html', 'text',''));
                   }
                   else {
                                   $infos = (pathinfo($delib['Deliberation']['texte_projet_name']));
                       $nameTP = 'tp.'.$infos['extension'];
                       $this->Gedooo->createFile($path, $nameTP, $delib['Deliberation']['texte_projet']);
                       $extTP = $u->getMimeType($path.$nameTP);
                       $oMainPart->addElement(new GDO_ContentType('texte_projet',       '',  $extTP,    'url', $urlWebroot.$nameTP ));
                   }

                  if ($delib['Deliberation']['deliberation_name']=="")
                       $nameTD = "vide";
                   else{
                       $infos = (pathinfo($delib['Deliberation']['deliberation_name']));
                       $nameTD = 'td.'.$infos['extension'];
                       $this->Gedooo->createFile($path, $nameTD, $delib['Deliberation']['deliberation']);
                       $extTD  = $u->getMimeType($path.$nameTD);
                       $oMainPart->addElement(new GDO_ContentType('texte_deliberation', '',  $extTD ,   'url', $urlWebroot.$nameTD));
                   }

                   if ($delib['Deliberation']['texte_synthese_name']=="")
                       $nameNS = "vide";
                   else {
                       $infos = (pathinfo($delib['Deliberation']['texte_synthese_name']));
                       $nameNS = 'ns.'.$infos['extension'];
                       $this->Gedooo->createFile($path, $nameNS,  $delib['Deliberation']['texte_synthese']);
                       $extNS   = $u->getMimeType($path.$nameNS);
                       $oMainPart->addElement(new GDO_ContentType('note_synthese',      '',  $extNS ,   'url', $urlWebroot.$nameNS));
                   }

                   if ($delib['Deliberation']['debat_name']=="")
                       $nameDebat = "debat";
                   else {
                       $infos = (pathinfo($delib['Deliberation']['debat_name']));
                       $nameDebat = 'debat.'.$infos['extension'];
                       $this->Gedooo->createFile($path,  $nameDebat,  $delib['Deliberation']['debat']);
                       $extDebat =  $u->getMimeType($path.$nameDebat);
                       $oMainPart->addElement(new GDO_ContentType('debat_deliberation', '',  $extDebat, 'url', $urlWebroot.$nameDebat));
                   }

                   if ($delib['Deliberation']['commission_name']=="")
                       $nameCommission = "commission";
                   else {
                       $infos = (pathinfo($delib['Deliberation']['commission_name']));
                       $nameCommission = 'commission.'.$infos['extension'];
                       $this->Gedooo->createFile($path,  $nameCommission,  $delib['Deliberation']['commission']);
                       $extCommi =  $u->getMimeType($path.$nameCommission);
                       $oMainPart->addElement(new GDO_ContentType('debat_commission', '',  $extCommi, 'url', $urlWebroot.$nameCommission));
                   }
              }
          return $oMainPart;
      }

}
?>
