<?php
	class ModelsController extends AppController {
		
		var $name = 'Models';
		var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Localisation', 'Seance', 'Service', 'Commentaire', 'Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Acteur');
		var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
		var $components = array('Date','Utils','Email', 'Acl', 'Gedooo');

		// Gestion des droits
		var $aucunDroit = array('getModel', 'makeBalisesProjet', 'generer');
		var $commeDroit = array('edit'=>'Models:index', 'add'=>'Models:index', 'delete'=>'Models:index', 'view'=>'Models:index', 'import'=>'Models:index', 'getFileData'=>'Models:index');
                

		function index() {
		    $this->set('USE_GEDOOO', USE_GEDOOO);
		    $this->set('models', $this->Model->findAll(null, null, 'type ASC '));
		}

		function add() {
			if (empty($this->data)) {
				$this->render();
			} else{
				$this->data['Model']['type']='Document';
				if ($this->Model->save($this->data)) {
					$this->redirect('/models/index');
				}
			}
		}

		function edit($id=null) {
			$data = $this->Model->findAll("Model.id = $id");
			$this->set('libelle', $data['0']['Model']['modele']);

			if (empty($this->data)) {
				$this->data = $this->Model->read(null, $id);
			} else{
				$this->data['Model']['id']=$id;
				if ($this->Model->save($this->data)) {
					$this->redirect('/models/index');
				}
			}
		}

		function delete($id = null) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la deliberation');
				$this->redirect('/models/index');
			}
			$data = $this->Model->read(null, $id);
			if ($data['Model']['type'] == 'Document') {
			    if ($this->Model->del($id)) {
				$this->Session->setFlash('Le model a &eacute;t&eacute; supprim&eacute;e.');
				$this->redirect('/models/index');
			    }
			}
			else{
			    $this->Session->setFlash('Impossible de supprimer ce type de modele');
			    $this->redirect('/models/index');
			}

		}

		function view($id = null) {
		      $this->set('USE_GEDOOO', USE_GEDOOO);
		      $data = $this->Model->read(null, $id);
		      if (USE_GEDOOO && ($data['Model']['type'] == 'Document') ) {
			  header('Content-type: '.$this->_getFileType($id));
			  header('Content-Length: '.$this->_getSize($id));
			  header('Content-Disposition: attachment; filename='.$this->_getFileName($id));
			  echo $this->_getData($id);
			  exit();
		       }
		       else {
			   $this->set('model', $this->Model->read(null, $id));
		       }
		}


	function import($model_id) {
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$this->set('model_id', $model_id);
                $Model = $this->Model->read(null, $model_id);
		$this->set('libelle', $Model['Model']['modele']);
		if (! empty($this->data)){
			if (isset($this->data['Model']['template'])){
				if ($this->data['Model']['template']['size']!=0){
					$this->data['Model']['id']        = $model_id;
					$this->data['Model']['name']      = $this->data['Model']['template']['name'];
					$this->data['Model']['size']      = $this->data['Model']['template']['size'];
					$this->data['Model']['extension'] = $this->data['Model']['template']['type'];
					$this->data['Model']['content']   = $this->getFileData($this->data['Model']['template']['tmp_name'], $this->data['Model']['template']['size']);
					if ($this->Model->save($this->data))
						$this->redirect('/models/index');
				}
			}
		} else {
                $this->data = $this->Model->read(null, $model_id);
	    }
	}

	function getFileData($fileName, $fileSize) {
		return fread(fopen($fileName, "r"), $fileSize);
	}

	function _getFileType($id=null) {
		$condition = "Model.id = $id";
		$objCourant = $this->Model->findAll($condition);
		return $objCourant['0']['Model']['extension'];
	}

	function _getFileName($id=null) {
		$condition = "Model.id = $id";
		$objCourant = $this->Model->findAll($condition);
		return $objCourant['0']['Model']["name"];
	}

	function _getSize($id=null) {
		$condition = "Model.id = $id";
		$objCourant = $this->Model->findAll($condition);
		return $objCourant['0']['Model']["size"];
	}

	function _getData($id=null) {
		$condition = "Model.id = $id";
		$objCourant = $this->Model->findAll($condition);
		return $objCourant['0']['Model']['content'];
	}

	function getModel($id=null) {
		$condition = "Model.id = $id";
		$objCourant = $this->Model->findAll($condition);
		return $objCourant['0']['Model']['content'];
	}

	function makeBalisesProjet ($delib, $oMainPart, $isDelib, $u=null)  {
	       $oMainPart->addElement(new GDO_FieldType('date_seance',                 $this->Date->frDate($delib['Seance']['date']),   'date'));
	       $date_lettres =  $this->Date->dateLettres(strtotime($delib['Seance']['date']));
	       $oMainPart->addElement(new GDO_FieldType('date_seance_lettres',         utf8_encode($date_lettres),                      'text'));
               $oMainPart->addElement(new GDO_FieldType('heure_seance',                $this->Date->Hour($delib['Seance']['date']),     'text'));
               
               $oMainPart->addElement(new GDO_FieldType('titre_projet',                utf8_encode($delib['Deliberation']['titre']),    'text'));
               $oMainPart->addElement(new GDO_FieldType('objet_projet',                utf8_encode($delib['Deliberation']['objet']),    'text'));
               $oMainPart->addElement(new GDO_FieldType('position_projet',             utf8_encode($delib['Deliberation']['position']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('identifiant_projet',          utf8_encode($delib['Deliberation']['id']),       'text'));
               $oMainPart->addElement(new GDO_FieldType('identifiant_seance',          utf8_encode($delib['Deliberation']['seance_id']),'text'));
               $oMainPart->addElement(new GDO_FieldType('numero_deliberation',         utf8_encode($delib['Deliberation']['num_delib']),'text'));
               $oMainPart->addElement(new GDO_FieldType('classification_deliberation', utf8_encode($delib['Deliberation']['num_pref']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('service_emetteur',            utf8_encode($delib['Service']['libelle']) ,      'text'));
               $oMainPart->addElement(new GDO_FieldType('theme_projet',                utf8_encode($delib['Theme']['libelle']),         'text'));
               
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
               
               // Information sur le secretaire
               $secretaire = $this->Acteur->read(null, $delib['Seance']['secretaire_id'] );
               $oMainPart->addElement(new GDO_FieldType('nom_secretaire', utf8_encode($secretaire['Acteur']['nom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('prenom_secretaire', utf8_encode($secretaire['Acteur']['prenom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('salutation_secretaire', utf8_encode($secretaire['Acteur']['salutation']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('titre_secretaire', utf8_encode($secretaire['Acteur']['titre']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('note_secretaire', utf8_encode($secretaire['Acteur']['note']), 'text'));
               
               // Informations sur le rédacteur
               $oMainPart->addElement(new GDO_FieldType('prenom_redacteur', utf8_encode($delib['Redacteur']['prenom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('nom_redacteur', utf8_encode($delib['Redacteur']['nom']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('email_redacteur', utf8_encode($delib['Redacteur']['email']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('telmobile_redacteur', utf8_encode($delib['Redacteur']['telmobile']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('telfixe_redacteur', utf8_encode($delib['Redacteur']['telfixe']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('date_naissance_redacteur', utf8_encode($delib['Redacteur']['date_naissance']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('note_redacteur', utf8_encode($delib['Redacteur']['note']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('position_redacteur', utf8_encode($delib['Redacteur']['position']), 'text'));
               
               // Informations sur la délibération
               $oMainPart->addElement(new GDO_FieldType('nombre_pour',  utf8_encode($delib['Deliberation']['vote_nb_oui'])   , 'text'));
               $oMainPart->addElement(new GDO_FieldType('nombre_abstention', utf8_encode( $delib['Deliberation']['vote_nb_abstention']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('nombre_contre',  utf8_encode($delib['Deliberation']['vote_nb_non']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('nombre_sans_participation', utf8_encode( $delib['Deliberation']['vote_nb_retrait']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('commentaire_vote',  utf8_encode($delib['Deliberation']['vote_commentaire']), 'text'));
               
               $commentaires = new GDO_IterationType("Commentaires");
               foreach($delib['Commentaire'] as $commentaire) {
                   $oDevPart = new GDO_PartType();
                   $oDevPart->addElement(new GDO_FieldType("texte_commentaire", utf8_encode($commentaire['texte']), "text"));
                   $commentaires->addPart($oDevPart);
                }
               @$oMainPart->addElement($commentaires);

               if (GENERER_DOC_SIMPLE) {
                   $oMainPart->addElement(new GDO_ContentType('texte_projet', '', 'text/html', 'text', $delib['Deliberation']['texte_projet']));
                   $oMainPart->addElement(new GDO_ContentType('note_synthese', '', 'text/html', 'text', $delib['Deliberation']['texte_synthese']));
                   $oMainPart->addElement(new GDO_ContentType('texte_deliberation', '', 'text/html', 'text', $delib['Deliberation']['deliberation']));
                   $oMainPart->addElement(new GDO_ContentType('debat_deliberation', '', 'text/html', 'text', $delib['Deliberation']['debat']));
                   $oMainPart->addElement(new GDO_ContentType('debat_commission', '', 'text/html', 'text', $delib['Deliberation']['commission']));
               }
               else {
                   $dyn_path = "/files/generee/deliberations/".$delib['Deliberation']['id']."/";
                   $path = WEBROOT_PATH.$dyn_path;
                   
                   if (!$this->Gedooo->checkPath($path))
                       die("Webdelib ne peut pas ecrire dans le repertoire : $path");

                   $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;

                   if ($delib['Deliberation']['texte_projet_name']== "")
                       $nameTP = "vide";
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
               if (!$isDelib)
                  return $oMainPart;
	       //LISTE DES PRESENCES...
               $acteurs = $this->Listepresence->findAll("delib_id = ".$delib['Deliberation']['id'], null, 'Acteur.position ASC');
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
               @$oMainPart->addElement($this->makeBlocsActeurs("ActeursPresents", $acteurs_presents, false, '_present'));
               @$oMainPart->addElement($this->makeBlocsActeurs("ActeursAbsents", $acteurs_absents, false, '_absent'));
               @$oMainPart->addElement($this->makeBlocsActeurs("ActeursMandates", $acteurs_remplaces, true, '_mandataire'));

               return $oMainPart;
        }

        function makeBlocsActeurs ($nomBloc, $listActeur, $isMandate, $type) {
	  $acteurs = new GDO_IterationType("$nomBloc");
          if ( count($listActeur) == 0 ) {
              $oDevPart = new GDO_PartType();
              $oDevPart->addElement(new GDO_FieldType("nom_acteur".$type,            ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type,         ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,     ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("titre_acteur".$type,          ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, ' ', "text"));
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
            
            foreach($listActeur as $acteur) {
                $oDevPart = new GDO_PartType();
                $oDevPart->addElement(new GDO_FieldType("nom_acteur".$type, utf8_encode($acteur['nom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type, utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("titre_acteur".$type, utf8_encode($acteur['titre_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, utf8_encode($acteur['date_naissance_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("adresse1_acteur".$type, utf8_encode($acteur['adresse1_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("adresse2_acteur".$type, utf8_encode($acteur['adresse2_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("cp_acteur".$type, utf8_encode($acteur['cp_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("ville_acteur".$type, utf8_encode($acteur['ville_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("email_acteur".$type, utf8_encode($acteur['email_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("telfixe_acteur".$type,utf8_encode($acteur['telfixe_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("telmobile_acteur".$type,  utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("note_acteur".$type, utf8_encode($acteur['note_acteur']), "text"));
                if (isset($acteur['nom_mandate'])) {
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

        function generer ($delib_id=null, $seance_id=null,  $model_id, $editable=null, $dl=0, $nomFichier='retour', $isPV=0) {
            include_once ('vendors/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_AxisTitleType.class');

            //*****************************************
            // Choix du format de sortie
            //*****************************************
	    $sMimeType = "application/pdf";
	    if ($editable=='null') 
                if ($this->Session->read('user.format.sortie')==0)
	            $sMimeType = "application/pdf";
		else 
	            $sMimeType = "odt";
            //*****************************************
	    // Préparation des répertoires pour la création des fichiers
            //*****************************************
            $dyn_path = "/files/generee/fd/$seance_id/$delib_id/";
            $path = WEBROOT_PATH.$dyn_path;
            if (!$this->Gedooo->checkPath($path))
                die("Webdelib ne peut pas ecrire dans le repertoire : $path");
            $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;
            
            //*****************************************
	    //Création du model ott
            //*****************************************
	    $u = new GDO_Utility();
            $content = $this->requestAction("/models/getModel/$model_id");
            $sModele = $this->Gedooo->createFile($path,'model_'.$model_id.'.odt', $content);
	    $path_model = $path.'model_'.$model_id.'.odt';
            
            $bTemplate = $u->ReadFile($path_model);
	    $oTemplate = new GDO_ContentType("",
                                "modele.odt",
                                $u->getMimeType($path_model),
                                "binary",
                                $bTemplate);
            
            //*****************************************
	    // Organisation des données
            //*****************************************
	    $oMainPart = new GDO_PartType();

	    // Informations sur la collectivité
            $data = $this->Collectivite->read(null, 1);
            $oMainPart->addElement(new GDO_FieldType('nom_collectivite',utf8_encode($data['Collectivite']['nom']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('adresse_collectivite',utf8_encode($data['Collectivite']['adresse']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('cp_collectivite',utf8_encode($data['Collectivite']['CP']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('ville_collectivite',utf8_encode($data['Collectivite']['ville']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('telephone_collectivite',utf8_encode($data['Collectivite']['telephone']) , "text"));
            $oMainPart->addElement(new GDO_FieldType('date_jour_courant',utf8_encode($this->Date->frenchDate(strtotime("now"))), 'text'));
            
            //*****************************************
	    // Génération d'une délibération ou d'un texte de projet
            //*****************************************
            if ($delib_id != "null") {
	        $delib = $this->Deliberation->read(null,$delib_id);
                $oMainPart = $this->makeBalisesProjet($delib, $oMainPart, true, $u);
            }
            
            //*****************************************
	    // Génération d'une convocation, ordre du jour ou PV 
            //*****************************************
             if ($seance_id != "null") {
                 $projets  = $this->Deliberation->findAll("seance_id=$seance_id AND etat>=0",null,'Deliberation.position ASC');
                 $blocProjets = new GDO_IterationType("Projets");
		 foreach ($projets as $projet) {
		     $oDevPart = new GDO_PartType();
		     if ($isPV)
		         $oDevPart = $this->makeBalisesProjet($projet,  $oDevPart, true, $u);
		     else 
		         $oDevPart = $this->makeBalisesProjet($projet,  $oDevPart, false, $u);
		     $blocProjets->addPart($oDevPart);
                 }
                 $oMainPart->addElement($blocProjets);
		 $seance = $this->Seance->read(null, $seance_id);
                 
                 $oMainPart->addElement(new GDO_FieldType('date_seance',  $this->Date->frDate($seance['Seance']['date']),   'date'));
	         $date_lettres =  $this->Date->dateLettres(strtotime($seance['Seance']['date']));
	         $oMainPart->addElement(new GDO_FieldType('date_seance_lettres', utf8_encode($date_lettres),                     'text'));
                 $oMainPart->addElement(new GDO_FieldType('heure_seance', $this->Date->Hour  ($seance['Seance']['date']),   'date'));
                 $oMainPart->addElement(new GDO_FieldType('type_seance',$seance['Typeseance']['libelle'] , "text"));
                 $oMainPart->addElement(new GDO_FieldType('identifiant_seance',  utf8_encode($seance['Seance']['id']),'text'));
                 $oDevPart->addElement(new GDO_FieldType("nom_secretaire", utf8_encode($seance['Secretaire']['nom']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("prenom_secretaire", utf8_encode($seance['Secretaire']['prenom']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("salutation_secretaire",utf8_encode($seance['Secretaire']['salutation']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("titre_secretaire", utf8_encode($seance['Secretaire']['titre']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("date_naissance_secretaire", utf8_encode($seance['Secretaire']['date_naissance']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("adresse1_secretaire", utf8_encode($seance['Secretaire']['adresse1']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("adresse2_secretaire", utf8_encode($seance['Secretaire']['adresse2']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("cp_secretaire", utf8_encode($seance['Secretaire']['cp']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("ville_secretaire", utf8_encode($seance['Secretaire']['ville']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("email_secretaire", utf8_encode($seance['Secretaire']['email']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("telfixe_secretaire",utf8_encode($seance['Secretaire']['telfixe']), "text"));
                 $oDevPart->addElement(new GDO_FieldType("note_secretaire", utf8_encode($seance['Secretaire']['note']), "text"));
		 
                 if (!$isPV) { // une convocation ou un ordre du jour
                     require_once ('vendors/progressbar.php');
                     Initialize(200, 100,200, 30,'#000000','#FFCC00','#006699');
                     $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
                     $nbActeurs = count($acteursConvoques);
                     $cpt=0;
                     if (empty($acteursConvoques))
		        return "";
                     foreach ($acteursConvoques as $acteur) {
                         $cpt++;
                         $zip = new ZipArchive;
                         ProgressBar($cpt*(100/$nbActeurs), 'Lecture des donn&eacute;es pour : <b>'. $acteur['Acteur']['prenom']." ".$acteur['Acteur']['nom'].'</b>');
                         $oMainPart->addElement(new GDO_FieldType("nom_acteur", utf8_encode($acteur['Acteur']['nom']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("prenom_acteur", utf8_encode($acteur['Acteur']['prenom']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("salutation_acteur",utf8_encode($acteur['Acteur']['salutation']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("titre_acteur", utf8_encode($acteur['Acteur']['titre']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("date_naissance_acteur", utf8_encode($acteur['Acteur']['date_naissance']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("adresse1_acteur", utf8_encode($acteur['Acteur']['adresse1']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("adresse2_acteur", utf8_encode($acteur['Acteur']['adresse2']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("cp_acteur", utf8_encode($acteur['Acteur']['cp']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("ville_acteur", utf8_encode($acteur['Acteur']['ville']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("email_acteur", utf8_encode($acteur['Acteur']['email']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("telfixe_acteur",utf8_encode($acteur['Acteur']['telfixe']), "text"));
                         $oMainPart->addElement(new GDO_FieldType("note_acteur", utf8_encode($acteur['Acteur']['note']), "text"));
                         $oFusion = new GDO_FusionType($oTemplate, 'application/pdf', $oMainPart);
                         $oFusion->process();
                         $nomFichier = $acteur['Acteur']['id'].'-'.utf8_encode($acteur['Acteur']['nom']).'.pdf';
                         $listFiles[$urlWebroot.$nomFichier] = $acteur['Acteur']['prenom']." ".$acteur['Acteur']['nom'];
                         $oFusion->SendContentToFile($path.$nomFichier);
                         if ($zip->open($path.'documents.zip', ZipArchive::CREATE) === TRUE) {
                             $zip->addFile($path.$nomFichier, $nomFichier);
                             $zip->close();
                         } 
                         // envoi des mails si le champ est renseigné
                        $this->sendDocument($acteur['Acteur']['email'], $nomFichier, $path, '');                         
                     }
                     $listFiles[$urlWebroot.'documents.zip'] = 'Documents.zip';
                     $this->set('listFiles', $listFiles);
                     $this->render();
                     exit;
		}
	    }

            //*****************************************
            // Lancement de la fusion
            //*****************************************
            $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
            $oFusion->process();
            if ($dl ==1) 
	        $oFusion->SendContentToFile($path.$nomFichier);
            else 
	        $oFusion->SendContentToClient();  
        }

        function sendDocument($to, $fichier, $path, $doc) {
            if ($to != '') {
                $this->Email->template = 'email/convoquer';
                $this->Email->attachments = null;
                $this->Email->to = $to;
                $this->Email->subject = utf8_encode("Vous venez de recevoir un document de Webdelib ");
                $this->set('data',  utf8_encode("Vous venez de recevoir un document de Webdelib "));
                $this->Email->attach($path.$fichier, $fichier);
                $res =  $this->Email->send();
                unset($res);
            }
        }
}
?>
