<?php
	class ModelsController extends AppController {

		var $name = 'Models';
		var $uses = array('Deliberation', 'User',  'Annex', 'Typeseance', 'Seance', 'Service', 'Commentaire', 'Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Acteur', 'Infosupdef', 'Infosuplistedef', 'Historique');
		var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'Html2', 'Session');
		var $components = array('Date','Utils','Email', 'Acl', 'Gedooo', 'Conversion');

		// Gestion des droits
		var $aucunDroit = array(
			'generer',
			'paramMails'
		);
		var $commeDroit = array(
			'edit'=>'Models:index',
			'add'=>'Models:index',
			'delete'=>'Models:index',
			'view'=>'Models:index',
			'import'=>'Models:index',
			'getFileData'=>'Models:index'
		);

		function index() {
		    $this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
		    $models=$this->Model->find('all', array('fields'=>array('id'), 'recursive'=>-1));
		    $deletable=array();
		    foreach ($models as $model) {
		    	$id=$model['Model']['id'];
		    	if ($this->Typeseance->find('first',array('conditions'=>array(
					                          'OR'=>array(
                                                                  'Typeseance.modelprojet_id'=>$id,
                                                                  'Typeseance.modeldeliberation_id'=>$id,
                                                                  'Typeseance.modelconvocation_id'=>$id,
                                                                  'Typeseance.modelordredujour_id'=>$id,
                                                                  'Typeseance.modelpvsommaire_id'=>$id,
                                                                  'Typeseance.modelpvdetaille_id'=>$id)))))
			    $deletable[$id]=false;
                        else $deletable[$id]=true;
		    }
		    $this->set('deletable',$deletable);
		    $this->set('models', $this->Model->find('all', array('order'=>array('type ASC '))));
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
				$this->Session->setFlash('id invalide pour le modèle de  délibération');
				$this->redirect('/models/index');
			}
			$data = $this->Model->read(null, $id);
			if (($data['Model']['type'] == 'Document')&&(!$this->Typeseance->find('first',array('conditions'=>array(
																									'OR'=>array(
																										'Typeseance.modelprojet_id'=>$id,
																										'Typeseance.modeldeliberation_id'=>$id,
																										'Typeseance.modelconvocation_id'=>$id,
																										'Typeseance.modelordredujour_id'=>$id,
																										'Typeseance.modelpvsommaire_id'=>$id,
																										'Typeseance.modelpvdetaille_id'=>$id
																									)
																								)
			)))) {
				if ($this->Model->delete($id)) {
						$this->Session->setFlash('Le modèle a été supprimé.');
						$this->redirect('/models/index');
					}
				else{
					$this->Session->setFlash('Impossible de supprimer ce type de modele');
					$this->redirect('/models/index');
				}
			}
			else{
				$this->Session->setFlash('Impossible de supprimer ce type de modele');
				$this->redirect('/models/index');
			}
		}

		function view($id = null) {
		    $data = $this->Model->read(null, $id);
		    if (!empty($data['Model']['name'])) {
				$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
				header('Content-type: '.$this->_getFileType($id));
				header('Content-Length: '.$this->_getSize($id));
				header('Content-Disposition: attachment; filename='.$this->_getFileName($id));
				echo $this->_getModel($id);
				exit();
			}
			else {
				$this->Session->setFlash('Aucun fichier li&eacute; &agrave; ce mod&egrave;le');
				$this->redirect('/models/index');
			}
		}


	function import($model_id) {
            $this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
	    $this->set('model_id', $model_id);
            $this->Model->id = $model_id;
            $Model = $this->Model->find('first', array('conditions'=> array('Model.id'=> $model_id),
                                                       'recursive' => -1,
                                                       'fields'    => array('modele', 'recherche')));
	    $this->set('libelle', $Model['Model']['modele']);
	    if (! empty($this->data)){
	        if (isset($this->data['Model']['template'])){
                
		    if ($this->data['Model']['template']['size']!=0){
                        $this->data['Model']['id']        = $model_id;
                        $this->data['Model']['name']      = $this->data['Model']['template']['name'];
                        $this->data['Model']['size']      = $this->data['Model']['template']['size'];
                        $this->data['Model']['extension'] = $this->data['Model']['template']['type'];
                        $this->data['Model']['content']   = $this->getFileData($this->data['Model']['template']['tmp_name'], $this->data['Model']['template']['size']);
                    }
                }
                if ($this->Model->save($this->data))
                    $this->redirect('/models/index');
            } else {
                $this->data = $Model;
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
                $objCourant = $this->Model->find('first', array(
                                                 'conditions'=> array('Model.id'=> $id),
                                                 'recursive' => '-1',
                                                 'fields'    => 'name'));
                return $objCourant['Model']['name'];

	}

	function _getSize($id=null) {
		$condition = "Model.id = $id";
		$objCourant = $this->Model->findAll($condition);
		return $objCourant['0']['Model']["size"];
	}

        function _getModel($id=null) {
                $objCourant = $this->Model->find('first', array(
                                                 'conditions'=> array('Model.id'=> $id),
                                                 'recursive' => '-1',
                                                 'fields'    => 'content'));
                return $objCourant['Model']['content'];
        }


        function generer ($delib_id=null, $seance_id=null,  $model_id, $editable=null, $dl=0, $nomFichier='retour', $isPV=0, $unique=false) {
            include_once ('vendors/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_AxisTitleType.class');
            $genereConvocation = false;
            //*****************************************
            // Choix du format de sortie
            //*****************************************
            if ($this->Session->read('user.format.sortie')==0) {
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
            $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;

            //*****************************************
	    //Création du model ott
            //*****************************************
	    $u = new GDO_Utility();
            $content = $this->_getModel($model_id);

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
                                          'conditions'=>array(
                                          'Collectivite.id'=>1)));

        $oMainPart->addElement(new GDO_FieldType('nom_collectivite',utf8_encode($data['Collectivite']['nom']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('adresse_collectivite',utf8_encode($data['Collectivite']['adresse']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('cp_collectivite',utf8_encode($data['Collectivite']['CP']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('ville_collectivite',utf8_encode($data['Collectivite']['ville']) , "text"));
	    $oMainPart->addElement(new GDO_FieldType('telephone_collectivite',utf8_encode($data['Collectivite']['telephone']) , "text"));
        $oMainPart->addElement(new GDO_FieldType('date_jour_courant',utf8_encode($this->Date->frenchDate(strtotime("now"))), 'text'));
        $oMainPart->addElement(new GDO_FieldType('date_du_jour', date("d/m/Y", strtotime("now")), 'date'));

            //*****************************************
	    // Génération d'une délibération ou d'un texte de projet
            //*****************************************
            if ($delib_id != "null") {
	        $delib = $this->Deliberation->find('first', array(
                                                   'conditions'=>array(
                                                   'Deliberation.id'=>$delib_id)));
                $oMainPart = $this->Deliberation->makeBalisesProjet($delib, $oMainPart, true, $u);
            }

            //*****************************************
	    // Génération d'une convocation, ordre du jour ou PV
            //*****************************************
             if ($seance_id != "null") {
                 $projets  = $this->Deliberation->find('all',array(
                                                       'conditions'=>array(
                                                           "seance_id"=>$seance_id, 
                                                           "etat >="=>0), 
                                                       'order' =>'Deliberation.position ASC'));
                 $blocProjets = new GDO_IterationType("Projets");
		 foreach ($projets as $projet) {
		 //$projet =  $projets['0'];
		     $oDevPart = new GDO_PartType();
		     if ($isPV){
		         $oDevPart = $this->Deliberation->makeBalisesProjet($projet,  $oDevPart, true, $u, true);
		     }
		     else
		         $oDevPart = $this->Deliberation->makeBalisesProjet($projet,  $oDevPart, false, $u, true);
		     $blocProjets->addPart($oDevPart);
                 }
                 $oMainPart->addElement($blocProjets);
		 $seance = $this->Seance->read(null, $seance_id);

                 $oMainPart->addElement(new GDO_FieldType('date_seance',  $this->Date->frDate($seance['Seance']['date']),   'date'));
                 $oMainPart->addElement(new GDO_FieldType('date_convocation',  $this->Date->frDate($seance['Seance']['date_convocation']),   'date'));
		 $oMainPart->addElement(new GDO_FieldType('commentaire_seance',         utf8_encode($seance['Seance']['commentaire']),    'text'));
	         $date_lettres =  $this->Date->dateLettres(strtotime($seance['Seance']['date']));
	         $oMainPart->addElement(new GDO_FieldType('date_seance_lettres', utf8_encode($date_lettres),                     'text'));
                 $oMainPart->addElement(new GDO_FieldType('heure_seance', $this->Date->Hour  ($seance['Seance']['date']),   'date'));
                 $oMainPart->addElement(new GDO_FieldType('type_seance',utf8_encode($seance['Typeseance']['libelle']) , "text"));
                 $oMainPart->addElement(new GDO_FieldType('identifiant_seance',  utf8_encode($seance['Seance']['id']),'text'));

                 $oMainPart->addElement(new GDO_FieldType("nom_secretaire", utf8_encode($seance['Secretaire']['nom']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("prenom_secretaire", utf8_encode($seance['Secretaire']['prenom']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("salutation_secretaire",utf8_encode($seance['Secretaire']['salutation']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("titre_secretaire", utf8_encode($seance['Secretaire']['titre']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("date_naissance_secretaire", utf8_encode($seance['Secretaire']['date_naissance']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("adresse1_secretaire", utf8_encode($seance['Secretaire']['adresse1']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("adresse2_secretaire", utf8_encode($seance['Secretaire']['adresse2']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("cp_secretaire", utf8_encode($seance['Secretaire']['cp']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("ville_secretaire", utf8_encode($seance['Secretaire']['ville']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("email_secretaire", utf8_encode($seance['Secretaire']['email']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("telfixe_secretaire",utf8_encode($seance['Secretaire']['telfixe']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("note_secretaire", utf8_encode($seance['Secretaire']['note']), "text"));

                 $oMainPart->addElement(new GDO_FieldType("nom_president", utf8_encode($seance['President']['nom']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("prenom_president", utf8_encode($seance['President']['prenom']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("salutation_president",utf8_encode($seance['President']['salutation']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("titre_president", utf8_encode($seance['President']['titre']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("date_naissance_president", utf8_encode($seance['President']['date_naissance']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("adresse1_president", utf8_encode($seance['President']['adresse1']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("adresse2_president", utf8_encode($seance['President']['adresse2']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("cp_president", utf8_encode($seance['President']['cp']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("ville_president", utf8_encode($seance['President']['ville']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("email_president", utf8_encode($seance['President']['email']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("telfixe_president",utf8_encode($seance['President']['telfixe']), "text"));
                 $oMainPart->addElement(new GDO_FieldType("note_president", utf8_encode($seance['President']['note']), "text"));

                 if (!$isPV) { // une convocation ou un ordre du jour
                   $this->Seance->id = $seance_id;
                   $this->Seance->saveField('date_convocation',  date("Y-m-d H:i:s", strtotime("now")));
                     $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
		     if (file_exists($path.'documents.zip'))
		         unlink($path.'documents.zip');

                     $nbActeurs = count($acteursConvoques);
                     $cpt=0;
                     $model_tmp = $this->Model->read(null, $model_id);
                     $this->set('nom_modele',  $model_tmp['Model']['modele']);
                     if (empty($acteursConvoques))
					 	 return "";
                     foreach ($acteursConvoques as $acteur) {
                         $cpt++;
                         $zip = new ZipArchive;

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
                             $nomFichier ='Apercu';
                             $listFiles[$urlWebroot.$nomFichier] = 'Apercu';
                         }
                   
                         try {
                             Configure::write('debug', 1);
                             error_reporting(0);
                             $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
                             $oFusion->process();
                             $oFusion->SendContentToFile($path.$nomFichier.".odt");
                             $content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", $format);
                             $this->Gedooo->createFile($path, $nomFichier.".$format", $content);

                         }
                         catch (Exception $e){
                             $this->cakeError('gedooo', array('error'=>$e, 'url'=> $this->Session->read('user.User.lasturl')));
                         }
                         if ($unique== false) {
                             if ($zip->open($path.'documents.zip', ZipArchive::CREATE) === TRUE) {
                                 $zip->addFile($path.$nomFichier, $nomFichier);
                                 $zip->close();
                             }
			 			 }
						 else
							 break;
                         // envoi des mails si le champ est renseigné
                        $this->_sendDocument($acteur['Acteur'], $nomFichier, $path, '');
                     }
		     if ($unique== false)
                         $listFiles[$urlWebroot.'documents.zip'] = 'Documents.zip';
                     $this->set('listFiles', $listFiles);
                     $this->set('format', $format);
                     $this->render('generer');
		     $genereConvocation = true;
		}
		else {
                   $dyn_path = "/files/generee/PV/".$seance['Seance']['id']."/";
                   $path = WEBROOT_PATH.$dyn_path;
                   if (!$this->Gedooo->checkPath($path))
                       die("Webdelib ne peut pas ecrire dans le repertoire : $path");

                   if (Configure::read('GENERER_DOC_SIMPLE')) {
                       include_once ('controllers/components/conversion.php');
                       $this->Conversion = new ConversionComponent;

                       $filename = $path."debat_seance.html";
                       $this->Gedooo->createFile($path, "debat_seance.html",  $seance['Seance']['debat_global']);
                       $content = $this->Conversion->convertirFichier($filename, "odt");

                       $oMainPart->addElement(new GDO_ContentType('debat_seance',  $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));
                   }
                   else {
                       $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;

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
                try {
                    Configure::write('debug', 1);
                    error_reporting(0);
                    $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
                    $oFusion->process();
                    if ($dl ==1) {
	                $oFusion->SendContentToFile($path.$nomFichier);
                        $content = $this->Conversion->convertirFichier($path.$nomFichier, $format);
                        $this->Gedooo->createFile($path, $nomFichier, $content);
                    }
                    else {
                        $nomFichier = "$nomFichier.$format";
                        $this->Gedooo->createFile($path, $nomFichier, '');
	                $oFusion->SendContentToFile($path.$nomFichier);
                        $content = $this->Conversion->convertirFichier($path.$nomFichier, $format );                 
                        header("Content-type: $sMimeType");
                        header("Content-Disposition: attachment; filename=\"$nomFichier\"");
                        header("Content-Length: ".strlen($content));
                        die ($content);
                    }
                }
                catch (Exception $e){
                    $this->cakeError('gedooo', array('error'=>$e, 'url'=> $this->Session->read('user.User.lasturl')));
                }
            }
        }

		function _sendDocument($acteur, $fichier, $path, $doc) {
			if ($acteur['email'] != '') {
				if (Configure::read("SMTP_USE")) {
					$this->Email->smtpOptions = array(
						'port'=>Configure::read("SMTP_PORT"), 
						'timeout'=>Configure::read("SMTP_TIMEOUT"),
						'host' => Configure::read("SMTP_HOST"),
						'username'=>Configure::read("SMTP_USERNAME"),
						'password'=>Configure::read("SMTP_PASSWORD"),
						'client' =>Configure::read("SMTP_CLIENT")
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
				$this->Email->template = 'convocation';
				$this->set('data',   $this->paramMails('convocation',  $acteur ));
				$this->Email->attachments = array($path.$fichier);

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

}
?>
