<?php
class ModelsController extends AppController {

	public $uses = array(
        'Deliberation', 'User',  'Annex', 'Typeseance', 'Seance', 'Service', 'Commentaire',
        'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Acteur', 'Infosupdef', 'Infosuplistedef', 'Historique', 'ModelOdtValidator.Modeltemplate'
    );
    public $helpers = array('Html', 'Form', 'Fck', 'Html2', 'Session');
    public $components = array('Date','Utils','Email', 'Acl', 'Gedooo', 'Conversion', 'Pdf', 'Progress', 'Fido');

	// Gestion des droits
    public $aucunDroit = array(
			'generer',
			'getGeneration',
			'paramMails'
	);

    public $commeDroit = array(
			'getFileData'  => 'Models:index',
			'changeStatus' => 'Models:index'
	);

	function _getFileName($id=null) {
		$objCourant = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'filename'));
		return utf8_encode($objCourant['Modeltemplate']['filename']);
	}

	function _getSize($id=null) {
		$objCourant = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'filesize'));
		return $objCourant['Modeltemplate']['filesize'];
	}

	function _getModel($id=null) {
		$objCourant = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'content'));
		return $objCourant['Modeltemplate']['content'];
	}


	function generer ($delib_id=null, $seance_id=null,  $model_id, $editable=-1, $dl=0, $nomFichier='retour', $isPV=0, $unique=false, $progress=false, $token=null) {
		$time_start = microtime(true);
                
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
		include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');
		$genereConvocation = false;
		//*****************************************
		// Choix du format de sortie
		//*****************************************
			
		if ($this->Session->read('user.format.sortie') == 0 || $editable == 0) {
			$sMimeType = "application/pdf";
			$format    = "pdf";
		}
		else {
			$sMimeType = "application/vnd.oasis.opendocument.text";
			$format    = "odt";
		}

		//*****************************************
		// Préparation des répertoires pour la création des fichiers
		//*****************************************
		$dyn_path = "/files/generee/fd/$seance_id/$delib_id/";
		$path = WEBROOT_PATH.$dyn_path;

		if (!$this->Gedooo->checkPath($path))
			die("Webdelib ne peut pas ecrire dans le repertoire : $path");
                $protocol = "http://";
                if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                        !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 )
                    $protocol = "https://";
		$urlWebroot =  $protocol.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;

		//*****************************************
		//Création du model ott
		//*****************************************
		$u = new GDO_Utility();
		$model = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $model_id),
				'recursive' => '-1',
				'fields'    => array('content')));

		$content = $model['Modeltemplate']['content'];

		$oTemplate = new GDO_ContentType("",
				$this->_getFileName($model_id),
				"application/vnd.oasis.opendocument.text",
				"binary",
				$content);

		//*****************************************
		// Organisation des données
		//*****************************************
		$oMainPart = new GDO_PartType();

		// Informations sur la collectivité
		$data = $this->Collectivite->find('first', array(
				'conditions'=>array('Collectivite.id'=>1)));

		$oMainPart->addElement(new GDO_FieldType('nom_collectivite',utf8_encode($data['Collectivite']['nom']) , "text"));
		$oMainPart->addElement(new GDO_FieldType('adresse_collectivite',utf8_encode($data['Collectivite']['adresse']) , "text"));
		$oMainPart->addElement(new GDO_FieldType('cp_collectivite',utf8_encode($data['Collectivite']['CP']) , "text"));
		$oMainPart->addElement(new GDO_FieldType('ville_collectivite',utf8_encode($data['Collectivite']['ville']) , "text"));
		$oMainPart->addElement(new GDO_FieldType('telephone_collectivite',utf8_encode($data['Collectivite']['telephone']) , "text"));
		$oMainPart->addElement(new GDO_FieldType('date_jour_courant',utf8_encode($this->Date->frenchDate(strtotime("now"))), 'text'));
		$oMainPart->addElement(new GDO_FieldType('date_du_jour', date("d/m/Y", strtotime("now")), 'date'));

		$annexes_id = array();
		//*****************************************
		// Génération d'un acte
		//*****************************************
		if ($delib_id != "null") {
			$delib = $this->Deliberation->find('first', array(
					'conditions' => array('Deliberation.id'=>$delib_id),
					'recursive'  => -1));
			$this->Deliberation->makeBalisesProjet($delib, $oMainPart);
			$tmp_annexes = $this->Deliberation->Annex->getAnnexesFromDelibId($delib_id, false, true, true);
			if (!empty($tmp_annexes))
				array_push($annexes_id,  $tmp_annexes);
			$path_annexes = $path.'annexes/';
			$annexes = array();
			foreach ($annexes_id as $annex_ids) {
				foreach($annex_ids as $annex_id) {
					$annexFile = $this->Deliberation->Annex->find('first', array(
							'conditions' => array('Annex.id' => $annex_id['Annex']['id']),
							'recursive'  => -1));
                                        array_push($annexes, $this->Gedooo->createFile($path_annexes, "annex_". $annexFile['Annex']['id'].'.pdf', $annexFile['Annex']['data_pdf']));
				}
			}
		}
		//*****************************************
		// Génération d'une convocation, ordre du jour ou PV (séance)
		//*****************************************
		if ($seance_id != "null") {
                        if ($progress) {
                            $this->Progress->start(200, 100,200, '#FFCC00','#006699');
                            $this->Progress->at(0, 'Démarrage de la génération');
                        }
			$projets  =  $this->Seance->getDeliberations($seance_id);
			$blocProjets = new GDO_IterationType("Projets");
                        $cpt = 0;
			foreach ($projets as $projet) {
                                $cpt++;
                                if ($progress)
                                    $this->Progress->at($cpt*(50/count($projets)), 'Génération des projets...');
				$oDevPart = new GDO_PartType();
				$this->Deliberation->makeBalisesProjet($projet,  $oDevPart);
				$blocProjets->addPart($oDevPart);

				$tmp_annexes = $this->Deliberation->Annex->getAnnexesFromDelibId($projet['Deliberation']['id'], false, true);
				if (!empty($tmp_annexes))
					array_push($annexes_id,  $tmp_annexes);
                                
			}
			$path_annexes = $path.'annexes/';
			$annexes = array();
                        $cpt = 0;
                        if ($progress) 
                            $this->Progress->at(50, 'Démarrage de la génération des annexes...');
			foreach ($annexes_id as $annex_ids) {
                            foreach($annex_ids as $annex_id) {
                                $cpt++;
                                if ($progress) 
                                    $this->Progress->at($cpt*(10/count($annexes_id))+50, 'Génération des annexes...');
                                $annexFile = $this->Deliberation->Annex->find('first', array(
                                                'conditions' => array('Annex.id' => $annex_id['Annex']['id']),
                                                'recursive'  => -1));
                                array_push($annexes, $this->Gedooo->createFile($path_annexes, "annex_". $annexFile['Annex']['id'].'.pdf', $annexFile['Annex']['data_pdf']));
                            }
			}
                        if ($progress) 
                            $this->Progress->at(60, 'Démarrage de la génération du document...');
			$oMainPart->addElement($blocProjets);
			$this->Seance->makeBalise($seance_id, $oMainPart);
			if (!$isPV) { // une convocation ou un ordre du jour
				$this->Seance->id = $seance_id;
				$this->Seance->saveField('date_convocation',  date("Y-m-d H:i:s", strtotime("now")));
				$type_id = $this->Seance->getType($seance_id);
				$acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($type_id);
				if (file_exists($path.'documents.zip'))
					unlink($path.'documents.zip');

				$model_tmp = $this->Modeltemplate->read(null, $model_id);
				$this->set('nom_modele',  $model_tmp['Modeltemplate']['name']);
				if (empty($acteursConvoques))
					return "";
				$zip = new ZipArchive;
                                $cpt=0;
				foreach ($acteursConvoques as $acteur) {
					$cpt++; 
                    if ($progress)
                        $this->Progress->at($cpt*((40/3)/count($acteursConvoques))+60, 'Génération du document : '. $acteur['Acteur']['nom'] .'...');
                    $this->set('unique', $unique);
					if ($unique== false) {
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
						$nomFichier = $acteur['Acteur']['id'].'-'.Inflector::camelize($this->Utils->strSansAccent($acteur['Acteur']['nom']));
						$listFiles[$urlWebroot.$nomFichier] = $acteur['Acteur']['prenom']." ".$acteur['Acteur']['nom'];
					}
					else {
						$nomFichier ='Document';
						$listFiles[$urlWebroot.$nomFichier] = 'Document généré';
					}

					try {
						Configure::write('debug', 1);
						error_reporting(0);

						$time_end = microtime(true);
						$time = $time_end - $time_start;
						$this->log("Temps création de requete :". $time );
                        if ($progress)
                            $this->Progress->at($cpt*((2*40/3)/count($acteursConvoques))+60, 'Fusion des documents...');

						$time_start = microtime(true);
						$oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
						$oFusion->process();
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						$this->log("Temps création de fusion : ". $time );
                        if ($progress)
                            $this->Progress->at($cpt*((3*40/3)/count($acteursConvoques))+60, 'Conversion et concaténation...');
						$time_start = microtime(true);
                        $oFusion->SendContentToFile($path.$nomFichier.".odt");
                        $content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", 'odt');
                        file_put_contents  ($path.$nomFichier.".odt", $content);

                        $content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", $format);
						$chemin_fichier = $this->Gedooo->createFile($path, $nomFichier.".$format", $content);
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						$this->log("Temps conversion et concaténation : ". $time );
					}
					catch (Exception $e){
                        $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'erreur'));
                        $this->redirect('/seances/listerFuturesSeances');
                        die;
					}
                                        
					if ($unique== false) {
						$res = $zip->open($path.'documents.zip', ZIPARCHIVE::CREATE);
						if ($res === TRUE) {
							$zip->addFile($chemin_fichier, $nomFichier.".$format");
							$res2 = $zip->close();
						}
					}
					else
						break;
					// envoi des mails si le champ est renseigné
					$this->_sendDocument($acteur['Acteur'], $nomFichier, $path, '');
				}
                if ($progress)
                    $this->Progress->at(100, 'Chargement des résultats...');
				if ($unique== false)
					$listFiles[$urlWebroot.'documents.zip'] = 'Documents.zip';
                                
                $this->Session->write('tmp.listFiles', $listFiles);
                $this->Session->write('tmp.format', $format);
                if ($progress)
                    $this->Progress->end('/models/getGeneration');
				$genereConvocation = true;
			}
			else {
            if ($progress)
                $this->Progress->at(65, 'Génération du PV...');
		 	$seance = $this->Seance->find('first', array(
		 			'conditions' => array('Seance.id' => $seance_id),
		 			'recursive'  => -1));
		 	$dyn_path = "/files/generee/PV/$seance_id/";
		 	$path = WEBROOT_PATH.$dyn_path;
                        
		 	if (!$this->Gedooo->checkPath($path))
		 		die("Webdelib ne peut pas ecrire dans le repertoire : $path");
                        $protocol = "http://";
                        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                            !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 )
                        $protocol = "https://";
                        $urlWebroot =  $protocol.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;
		 	if (Configure::read('GENERER_DOC_SIMPLE')) {
		 		include_once ('controllers/components/conversion.php');
                if ($progress)
                    $this->Progress->at(70, 'Conversion du document...');
		 		$this->Conversion = new ConversionComponent;

		 		$filename = $path."debat_seance.html";
		 		$this->Gedooo->createFile($path, "debat_seance.html",  $seance['Seance']['debat_global']);
		 		$content = $this->Conversion->convertirFichier($filename, "odt");

		 		$oMainPart->addElement(new GDO_ContentType('debat_seance',  $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));
		 	}
		 	else {
                                
                /**
                 * FIXME variable inutilisée !!?
                 */
		 		if ($seance['Seance']['debat_global_name']== "")
		 			$nameDSeance = "vide";
		 		else {
		 			$oMainPart->addElement(new GDO_ContentType('debat_seance', 'debat_seance.odt', "application/vnd.oasis.opendocument.text" , 'binary', $seance['Seance']['debat_global'] ));
		 		}
		 	}
		 }
		}
                
		if ($genereConvocation == false) {
			//*****************************************
			// Lancement de la fusion
			//*****************************************
                if ($progress)
                    $this->Progress->at(80, 'Fusion du document...');
				Configure::write('debug', 1);
				$oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
				$oFusion->process();
				$time_end = microtime(true);
				$time = $time_end - $time_start;
      
				if ($dl == 1) {
                    if ($progress)
                        $this->Progress->at(90, 'Conversion et concaténation...');
					$oFusion->SendContentToFile($path.$nomFichier);
					$content = $this->Conversion->convertirFichier($path.$nomFichier, $format);
					$chemin_fichier = $this->Gedooo->createFile($path, "$nomFichier.$format", $content);
					$listFiles[$urlWebroot.$nomFichier] = 'Document généré';
                    Configure::write('debug', 0);
                    if ($progress){
                        $this->Progress->at(100, 'Chargement des résultats...');
                        $this->Session->write('tmp.listFiles', $listFiles);
                        $this->Session->write('tmp.format', $format);
                        $this->Progress->end('/models/getGeneration');
                    }
                    else{
                        $this->set('listFiles', $listFiles);
                        $this->set('format', $format);
                    }
				}
				else {
                    $nomSansFormat = $nomFichier;
					$nomFichier = "$nomFichier.$format";
					$fichier = $this->Gedooo->createFile($path, $nomFichier, '');
					$oFusion->SendContentToFile($fichier);
					$content = $this->Conversion->convertirFichier($fichier, $format );

					$chemin_fichier = $this->Gedooo->createFile($path,  $nomFichier.'2', $content);
					$content = file_get_contents($chemin_fichier);

					$time_end = microtime(true);
					$time = $time_end - $time_start;
                    Configure::write('debug', 0);
                    if ($dl == 2) { //Pour l'export CMIS
                    }elseif ($progress){
                        $this->Progress->at(100, 'Chargement des résultats...');
                        $listFiles[$urlWebroot.$nomSansFormat] = 'Document généré';
                        $this->Session->write('tmp.listFiles', $listFiles);
                        $this->Session->write('tmp.format', $format.'2');
                        $this->Progress->end('/models/getGeneration');
                    } else {
                        $this->setCookieToken( "downloadToken", $token, false );
                        header("Content-type: $sMimeType");
                        $nomFichier = str_replace(array('pdf2','odt2'), array('pdf','odt'), $nomFichier);
                        header("Content-Disposition: attachment; filename=\"$nomFichier\"");
                        header("Content-Length: ".strlen($content));
                        die ($content);
                    }
				}
		}
	}
        
        function getGeneration(){
            $listFiles = $this->Session->read('tmp.listFiles');
            $this->Session->delete('tmp.listFiles');
            $format = $this->Session->read('tmp.format');
            $this->Session->delete('tmp.format');
            
            $this->set('listFiles', $listFiles);
            $this->set('format', $format);
            $lastUrl = $this->Session->read('user.User.lasturl');
            if (strpos($lastUrl, 'getGeneration'))
                $lastUrl = $this->Session->read('user.User.myUrl');
            $this->set('urlRetour', $lastUrl);
            $this->render('generer');
        }

	function _sendDocument($acteur, $fichier, $path) {
		if (($this->Session->read('user.format.sortie')==0) )
			$format    = ".pdf";
		else
			$format    = ".odt";

		if ($acteur['email'] != '') {
			if (Configure::read("SMTP_USE")) {
				$this->Email->smtpOptions = array( 'port'     => Configure::read("SMTP_PORT"),
						'timeout'  => Configure::read("SMTP_TIMEOUT"),
						'host'     => Configure::read("SMTP_HOST"),
						'username' => Configure::read("SMTP_USERNAME"),
						'password' => Configure::read("SMTP_PASSWORD"),
						'client'   => Configure::read("SMTP_CLIENT")
				);
				$this->Email->delivery = 'smtp';
			}
			else
				$this->Email->delivery = 'mail';

			$this->Email->from = Configure::read("MAIL_FROM");
			$this->Email->to = $acteur['email'];
			$this->Email->charset = 'UTF-8';

			$this->Email->subject = utf8_encode("Vous venez de recevoir un document de Webdelib ");
			$this->Email->sendAs = 'text';
			$this->Email->layout = 'default';
			$this->Email->template = 'convocation';
			$this->set('data',   $this->paramMails('convocation',  $acteur ));

			$this->Email->attachments = array($path.$fichier.$format);
			$this->Email->send();
		}
	}

	function paramMails($type,  $acteur) {
		$handle  = fopen(CONFIG_PATH.'/emails/'.$type.'.txt', 'r');
		$content = fread($handle, filesize(CONFIG_PATH.'/emails/'.$type.'.txt'));
		$searchReplace = array(
				"#NOM#" => $acteur['nom'],
				"#PRENOM#" => $acteur['prenom'],
		);
		return utf8_encode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $content))));
	}

	function changeStatus($field, $id) {

             $data = $this->Modeledition->find('first', array('conditions' => array("Modeledition.id" => $id),
                                               'recursive'  => -1,
                                               'fields'     => array("$field")));
             $this->Modeledition->id = $id;
             if ($data['Modeledition'][$field] == 0)
                 $this->Modeledition->saveField($field, 1);
             elseif($data['Modeledition'][$field] == 1)
                 $this->Modeledition->saveField($field, 0);

            $this->Session->setFlash('Modification prise en compte', 'growl');
	    $this->redirect('/models/index');

	}

}