<?php

class PostseancesController extends AppController {
	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Fck', 'Html2' );
	var $components = array('Date', 'Gedooo', 'Cmis', 'Progress', 'Conversion');
	var $uses = array('Deliberation','Infosup', 'Seance', 'User',  'Listepresence', 'Vote', 'ModelOdtValidator.Modeltemplate', 'Theme', 'Typeseance', 'Typeacte', 'Nature');

	var $demandeDroit = array('index');

	// Gestion des droits
	var $aucunDroit = array(
			'getNom',
			'getPresence',
			'getVote',
			'sendToGed'
	);
	var $commeDroit = array(
			'changeObjet'=>'Postseances:index',
			'afficherProjets'=>'Postseances:index',
			'changeStatus'=>'Postseances:index',
			'downloadPV'=>'Postseances:index'
	);

	function index() {
		$format =  $this->Session->read('user.format.sortie');

		if (empty($format))
			$format = 0;

		$this->set('format', $format);

        $actions=array();
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:sendToGed") && Configure::read('USE_GED'))
            array_push($actions, 'ged');

		$this->Seance->Behaviors->attach('Containable');
		$seances = $this->Seance->find('all', array(
            'conditions'=> array('Seance.traitee'=> 1),
            'order'     => 'Seance.date DESC',
            'fields'    => array('Seance.id', 'Seance.date', 'Seance.type_id', 'Seance.pv_figes'),
            'contain'   => array(
                'Typeseance.libelle',
                'Typeseance.action',
                'Typeseance.modelconvocation_id',
                'Typeseance.modelordredujour_id',
                'Typeseance.modelpvsommaire_id',
                'Typeseance.modelpvdetaille_id')));

		for ($i=0; $i<count($seances); $i++){
			$seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));
            $seances[$i]['Seance']['Actions']=$actions;
        }
        $this->set('use_s2low', Configure::read('USE_S2LOW'));
		$this->set('seances', $seances);
	}

	function afficherProjets ($id=null, $return=null) {
		$format =  $this->Session->read('user.format.sortie');
		if (empty($format))
			$format =0;
		$this->set('format', $format);
		$delibs = array();
		$this->Seance->id = $id;
		$this->set('pv_figes', $this->Seance->field('pv_figes'));

		if (!isset($return)) {
			$this->set('lastPosition', $this->Seance->getLastPosition($id));
			$typeseance_id = $this->Seance->getType($id);
			$deliberations = $this->Seance->getDeliberationsId($id);
			$num_delib = 0;
			foreach ($deliberations as $delib_id) {
			
				$this->Deliberation->Behaviors->attach('Containable');
				$delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
						                              	  'contain'  =>array('Theme.libelle', 'Rapporteur.nom', 'Rapporteur.prenom'),
						                                  'fields'     => array('objet_delib', 'titre', 'etat', 'Deliberation.id', 'num_delib') ));
				$delibs[ $num_delib ] = $delib;
				$delibs[ $num_delib ]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($typeseance_id, $delib['Deliberation']['etat']);
				$num_delib++;
			}
			$this->set('seance_id', $id);
			$this->set('projets', $delibs);
			$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->Seance->getDate($id))));
		}
		else {
			$condition = array("seance_id"=>$id, "etat >="=>2);
			return ($this->Deliberation->find('all', array('conditions'=>$condition, 'order'=>array('Deliberation.position ASC'))));
		}
	}

	function getVote($id_delib){
		$condition = "delib_id = $id_delib";
		$votes = $this->Vote->findAll($condition);
		if (!empty($votes)){
			$resultat =$votes[0]['Vote']['commentaire'];
			return $resultat;
		}
	}

	function getPresence($id_delib,$present){
		$condition ="delib_id =$id_delib AND present=$present";
		$presences = $this->Listepresence->findAll($condition);
		return $presences;
	}

	function getNom($id)
	{
		$data = $this->User->findAll("User.id = $id");
		return $data['0']['User']['prenom'].' '.$data['0']['User']['nom'];
	}

	function changeObjet($delib_id) {
		$this->set('delib_id', $delib_id);

		if (!empty($this->data)) {
			$data = $this->Deliberation->read(null, $delib_id);

			$data['Deliberation']['objet'] = $this->data['Deliberation']['objet'];
			if ($this->Deliberation->save($data))
				$this->redirect('/deliberations/transmit');
		}
	}

	function changeStatus ($seance_id) {
		// Avant de cloturer la séance, on stock les délibérations en base de données au format pdf
		$result = $this->_stockPvs($seance_id);
		if ($result){
			$this->Progress->end('/postseances/afficherProjets/'.$seance_id);
			exit;
		}
		else {
            $this->Session->setFlash("Au moins un PV n'a pas été généré correctement... Impossible de figer les débats", array('type' => 'error'));
            $this->redirect('/postseances/index');
        }
	}

	function _stockPvs($seance_id) {
		$this->Progress->start(200, 100,200,'#000000','#FFCC00','#006699');

		$path = WEBROOT_PATH."/files/generee/PV/$seance_id";
		$this->Gedooo->createFile("$path/", 'empty', '');

		$seance = $this->Seance->read(null, $seance_id);
		$this->Progress->at(0, 'Préparation PV Sommaire : '.$seance['Typeseance']['libelle']);
		$model_pv_sommaire = $seance['Typeseance']['modelpvsommaire_id'];
		$model_pv_complet  = $seance['Typeseance']['modelpvdetaille_id'];
		$retour1 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_sommaire/0/1/pv_sommaire/1/false");
		$this->Progress->at(50, 'Préparation du PV Complet : '.$seance['Typeseance']['libelle']);
		$retour2 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_complet/0/1/pv_complet/1/false");
		$this->Progress->at(99, 'Sauvegarde des PVs');
		$path = WEBROOT_PATH."/files/generee/PV/$seance_id";

		$pv_sommaire = file_get_contents("$path/pv_sommaire.pdf");
		$pv_complet = file_get_contents("$path/pv_complet.pdf");

		if (!empty($pv_sommaire) && !empty($pv_complet)) {
			$this->Seance->id = $seance_id;
			$this->Seance->saveField('pv_sommaire', $pv_sommaire );
			$this->Seance->saveField('pv_complet', $pv_complet);
			$this->Seance->saveField('pv_figes', 1);
			return true;
		}
		else {
            return false;
		}
	}

	function downloadPV($seance_id, $type) {
		$seance = $this->Seance->read(null, $seance_id);
		header('Content-type: application/pdf');
		if ($type == "sommaire") {
			header('Content-Length: '.strlen($seance['Seance']['pv_sommaire']));
			header('Content-Disposition: attachment; filename=pv_sommaire.pdf');
			die($seance['Seance']['pv_sommaire']);
		}
		else {
			header('Content-Length: '.strlen($seance['Seance']['pv_complet']));
			header('Content-Disposition: attachment; filename=pv_complet.pdf');
			die($seance['Seance']['pv_complet']);
		}
	}
        
    /** Envoie pour un ged en protocole CMIS
     * 
     * @param type $seance_id
     */
    function sendToGed($seance_id) {
        
        $path = WEBROOT_PATH.DS.'files'.DS.'generee'.DS.'fd'.DS.$seance_id.DS.'null'.DS;

        $this->Progress->start(200, 100,200,'#000000','#FFCC00','#006699');

        try {
            $cmis = new CmisComponent;
            $cmis->CmisComponent_Service();
            $this->Conversion = new ConversionComponent;

            // Création du répertoire de séance
            $result = $cmis->client->getFolderTree($cmis->folder->id, 1);

            $this->Progress->at(0, 'Création du répertoire de séance...');

            $this->Seance->Behaviors->attach('Containable');
            $seance = $this->Seance->find('first', array(
                'conditions' => array('Seance.id' => $seance_id),
                'contain'    => array('Typeseance.libelle', 'Typeseance.modelconvocation_id', 'Typeseance.modelprojet_id', 'Typeseance.modelordredujour_id')));

            $this->Progress->at(5, 'Recherche de la séance en base de données...');

            $date_seance = $seance['Seance']['date'];
            $date_convocation = $seance['Seance']['date_convocation'];
            $type_seance = $seance['Typeseance']['libelle'];
            $libelle_seance = $seance['Typeseance']['libelle']." ".$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));


            // Règle de gestion on écrase les documents existants
            try {
                //On recherche le dossier
                $objet_cmis = $cmis->client->getObjectByPath( Configure::read('GED_REPO') . DS . $libelle_seance );
                if(is_object($objet_cmis)) {
                    $this->Progress->at(10, 'Suppression du dernier export...'.$libelle_seance);
                    //On recherche tous les enfants du dossier
                    $children = $cmis->client->getChildren($objet_cmis->id);
                    //On boucle sur les enfants
                    foreach ($children->objectList as $child) {
                        //On supprimer l'enfant selectionné
                        $cmis->client->deleteObject($child->id);
                    }
                    //On peut maitenant supprimer le dossier
                    $cmis->client->deleteObject($objet_cmis->id);
                }
            } catch (CmisObjectNotFoundException $e) {
                // L'objet n'existe pas encore : ne rien faire
            }
            
            $zip = new ZipArchive;
            @unlink($path.'documents.zip');
            $zip->open($path.'documents.zip', ZipArchive::CREATE);
            
            $this->Progress->at(15, 'Création des dossiers...');
            // Création des dossiers
            $my_seance_folder = $cmis->client->createFolder($cmis->folder->id, $libelle_seance);
            // Création des dossiers vides
            $zip->addEmptyDir('Rapports');
            $zip->addEmptyDir('Annexes');
            //$my_seance_folder_rapport = $cmis->client->createFolder($my_seance_folder->id, 'Rapport');
            //$my_seance_folder_annexe = $cmis->client->createFolder($my_seance_folder->id, 'Annexe');

            $delibs_id = $this->Seance->getDeliberationsId($seance_id);

            $this->Progress->at(20, 'Création du fichier XML...');
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->formatOutput = true;
            $idDepot = $seance['Seance']['numero_depot']+1;

            $dom_depot = $this->_createElement($dom, 'depot', null, array(
                'idDepot' => $idDepot,
                'xmlns:webdelibdossier' => 'http://www.adullact.org/webdelib/infodossier/1.0',
                'xmlns:xm' => 'http://www.w3.org/2005/05/xmlmine'));

            $dom_seance = $this->_createElement($dom, 'seance', null, array('idSeance'=>$seance_id));
            $dom_seance->appendChild($this->_createElement($dom, 'typeSeance', $type_seance));
            $dom_seance->appendChild($this->_createElement($dom, 'dateSeance', $date_seance));
            $dom_seance->appendChild($this->_createElement($dom, 'dateConvocation', $date_convocation));

            //Noeud document[convocation]
            $this->Progress->at(25, 'Génération de la convocation...');
            $document = $this->_createElement($dom, 'document', null, array('nom'=>'convocation.pdf', 'type' => 'convocation' ));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            //Génération du fichier et ajout au zip
            $this->requestAction('/models/generer/null/'.$seance_id.'/'.$seance['Typeseance']['modelconvocation_id'].'/0/0/retour/0/true');
            $projet_filename =  WEBROOT_PATH.DS.'files'.DS.'generee'.DS.'fd'.DS.$seance_id.DS.'null'.DS.'Document.pdf';
            $zip->addFromString('convocation.pdf', file_get_contents($projet_filename));

            //Noeud document[odj]
            $this->Progress->at(40, 'Génération de l\'ordre du jour...');
            $document = $this->_createElement($dom, 'document', null, array('nom'=>'odj.pdf', 'type' => 'odj' ));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            //Génération du fichier et ajout au zip
            $this->requestAction('/models/generer/null/'.$seance_id.'/'.$seance['Typeseance']['modelordredujour_id'].'/0/0/retour/0/true');
            $odj_filename = WEBROOT_PATH.DS.'files'.DS.'generee'.DS.'fd'.DS.$seance_id.DS.'null'.DS.'Document.pdf';
            $zip->addFromString('odj.pdf', file_get_contents($odj_filename));

            //Noeud document[pv_sommaire]
            $this->Progress->at(50, 'Ajout du PV sommaire...');
            $document = $this->_createElement($dom, 'document', null, array('nom'=>'pv.pdf', 'type' => 'pv_sommaire' ));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            //Ajout au zip
            if(!empty($seance['Seance']['pv_sommaire']))
                $zip->addFromString('pv.pdf', $seance['Seance']['pv_sommaire']);

            //Noeud document[pv_complet]
            $this->Progress->at(55, 'Ajout du PV complet...');
            $document = $this->_createElement($dom, 'document', null, array('nom'=>'pvcomplet.pdf', 'type' => 'pv_complet' ));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            if(!empty($seance['Seance']['pv_complet']))
                $zip->addFromString('pvcomplet.pdf', $seance['Seance']['pv_complet']);
            //Infos supps
            $this->Progress->at(60, 'Ajout des informations supplémentaires de séance...');
            $this->_createElementInfosups($zip, $dom, $dom_seance, $seance_id, 'Seance');

            //Insertion du noeud xml seance
            $dom_depot->appendChild($dom_seance);

            $this->Progress->at(66, 'Ajout des délibérations...');
            foreach ($delibs_id as $delib_id) {
                
                $doc = $this->_createElement($dom, 'dossierActe', null,  array('idActe'=>$delib_id, 'refSeance' => $seance_id));

                $this->Deliberation->Behaviors->attach('Containable');
                $delib = $this->Deliberation->find('first', array(
                    'conditions' => array('Deliberation.id' => $delib_id),
                    'fields'     => array('Deliberation.id', 'Deliberation.num_delib', 'Deliberation.objet_delib', 'Deliberation.titre',
                        'Deliberation.delib_pdf','Deliberation.tdt_data_pdf','Deliberation.tdt_data_bordereau_pdf', 'Deliberation.deliberation', 
                        'Deliberation.deliberation_size','Deliberation.signature','Deliberation.dateAR'),
                    'contain'    => array(
                        'Service'   => array('fields' => array('libelle')),
                        'Theme'     => array('fields' => array('libelle')),
                        'Typeacte'  => array('fields' => array('nature_id')),
                        'Redacteur' => array('fields' => array('nom', 'prenom')),
                        'Rapporteur' => array('fields' => array('nom', 'prenom')),
                        )));
             
                $nature = $this->Nature->find('first', array(
                     'fields'     => array('libelle'),
                     'conditions' => array('Nature.id' => $delib['Typeacte']['nature_id'])));
 
                $doc->appendChild($this->_createElement($dom, 'natureACTE', $nature['Nature']['libelle'] ));
                $doc->appendChild($this->_createElement($dom, 'dateACTE',  $date_seance ));
                $doc->appendChild($this->_createElement($dom, 'numeroACTE', $delib['Deliberation']['num_delib']));
                $doc->appendChild($this->_createElement($dom, 'themeACTE', $delib['Theme']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'emetteurACTE', $delib['Service']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'redacteurACTE', $delib['Redacteur']['prenom'].' '.$delib['Redacteur']['nom']));
                $doc->appendChild($this->_createElement($dom, 'rapporteurACTE', $delib['Rapporteur']['prenom'].' '.$delib['Rapporteur']['nom']));
                $doc->appendChild($this->_createElement($dom, 'typeseanceACTE', $type_seance));
                $doc->appendChild($this->_createElement($dom, 'dateAR',  $delib['Deliberation']['dateAR'])); // utile ??
 
                //<listeCommissions>
                $seances_id = $this->Deliberation->getSeancesid($delib_id);
               
                $listeCommissions = '';
                foreach ($seances_id as $commission_id) {
                    if (!$this->Deliberation->Seance->isSeanceDeliberante($commission_id)) {
                        $typeSeance = $this->Deliberation->Seance->Typeseance->getLibelle( $this->Deliberation->Seance->getType($commission_id));
                        $listeCommissions .=  $typeSeance.' : '.  $this->Deliberation->Seance->getDate($commission_id).', ';
                    }
                }
                 
                $doc->appendChild($this->_createElement($dom, 'listeCommissions', $listeCommissions));
                //</listeCommissions>

                //Infos supps de délibération
                $this->_createElementInfosups($zip, $dom, $doc, $delib_id, 'Deliberation');

                //Noeud document[TexteActe]
                $delib_filename = $delib_id.'-'.$delib['Deliberation']['num_delib'].'.pdf';
                $document = $this->_createElement($dom, 'document', null, array('nom' => $delib_filename, 'relname' => $delib_filename, 'type' => 'TexteActe'));
                $document->appendChild($this->_createElement($dom, 'titre', $delib['Deliberation']['objet_delib']));
                $document->appendChild($this->_createElement($dom, 'description', $delib['Deliberation']['titre']));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $doc->appendChild($document);
                //Ajout au zip
                $zip->addFromString($delib_filename, (!empty($delib['Deliberation']['tdt_data_pdf'])?$delib['Deliberation']['tdt_data_pdf']:$delib['Deliberation']['delib_pdf']));

                //Noeud document[Rapport]
                if(!empty($delib['Deliberation']['deliberation_size'])){
                    $document = $this->_createElement($dom, 'document', null, array('nom' => $delib_filename, 'relname' => $delib_filename, 'type' => 'Rapport'));
                    $document->appendChild($this->_createElement($dom, 'titre', $delib['Deliberation']['objet_delib']));
                    $document->appendChild($this->_createElement($dom, 'description', $delib['Deliberation']['titre']));
                    $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                    $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                    $doc->appendChild($document);
                    //Génération du rapport et ajout au zip
                    $this->requestAction('/models/generer/'.$delib_id.'/null/'.$seance['Typeseance']['modelprojet_id'].'/0/2/retour/0/true/false');
                    $projet_filename =  WEBROOT_PATH.DS.'files'.DS.'generee'.DS.'fd'.DS.'null'.DS.$delib_id.DS.'retour.pdf2';
                    //Ajout au zip
                    $zip->addFromString('Rapports'.DS.$delib_filename, file_get_contents($projet_filename));
                }

                //Ajout de la signature (XML+ZIP)
                $signatureName = $delib['Deliberation']['id'].'-signature.zip';
                //Création du noeud XML
                $document = $this->_createElement($dom, 'document', null, array('nom' => $signatureName, 'relname' => $signatureName, 'type' => 'Signature'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/zip'));
                $doc->appendChild($document);
                //Ajout à l'archive
                $zip->addFromString('Annexes'.DS.$signatureName, $delib['Deliberation']['signature']);
                
                //Ajout du bordereau (XML+ZIP)
                $bordereauName = $delib['Deliberation']['id'].'-bordereau.pdf';
                //Création du noeud XML
                $document = $this->_createElement($dom, 'document', null, array('nom' => $bordereauName, 'relname' => $bordereauName, 'type' => 'Bordereau'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $doc->appendChild($document);
                //Ajout à l'archive
                $zip->addFromString('Annexes'.DS.$bordereauName, $delib['Deliberation']['tdt_data_bordereau_pdf']);


                //Ajout des annexes
                $annexes_id =  $this->Deliberation->Annex->getAnnexesFromDelibId($delib_id, true);
                if (!empty($annexes_id)) {
                    foreach ($annexes_id as $annex_id) {
                        $annex_id = $annex_id['Annex']['id'];
                        $annex = $this->Deliberation->Annex->find('first', array(
                            'conditions' => array('Annex.id' => $annex_id),
                            'fields'     => array('Annex.id','Annex.titre', 'Annex.filename', 'Annex.filetype', 'Annex.data_pdf','Annex.data'),
                            'recursive'  => -1));

                        switch ($annex['Annex']['filetype']) {
                            case 'application/pdf':
                                $annexe_content=$annex['Annex']['data_pdf'];
                                $annexe_filetype='application/pdf';
                                $annexe_filename=$annex['Annex']['filename'];
                                break;

                            case 'application/vnd.oasis.opendocument.text':    
                                 $annexe_content=$this->Conversion->convertirFlux($annex['Annex']['data'], 'pdf');
                                 $annexe_filetype='application/pdf';
                                 $annexe_filename=str_replace('odt','pdf', $annex['Annex']['filename']);
                                break;

                            default:
                                $annexe_content=$annex['Annex']['data'];
                                $annexe_filetype=$annex['Annex']['filetype'];
                                $annexe_filename=$annex['Annex']['filename'];
                                break;
                        }
                        //Création du noeud XML <document> de l'annexe
                        $document = $this->_createElement($dom, 'document', null, array('nom' => $annexe_filename, 'relname' => $annex['Annex']['id'].'.pdf', 'type' => 'Annexe'));
                        $document->appendChild($this->_createElement($dom, 'titre', $annex['Annex']['titre']));
                        $document->appendChild($this->_createElement($dom, 'mimetype', $annexe_filetype));
                        $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                        $doc->appendChild($document);
                        //Ajout du fichier annexe à l'archive
                        $zip->addFromString('Annexes'.DS.$annex['Annex']['id'].'.pdf', $annexe_content);
                    }
                }
                $dom_depot->appendChild($doc);
            }

            $zip->close();
            $dom->appendChild($dom_depot);
            $xmlContent = $dom->saveXML();
            
            $this->Progress->at(95, 'Envoi du XML à la GED...');

            $cmis->client->createDocument(
                $my_seance_folder->id,
                "XML_DESC_$seance_id.xml",
                array (),
                $xmlContent,
                'application/xml');

            $cmis->client->createDocument(
                $my_seance_folder->id,
                'documents.zip',
                array (),
                file_get_contents($path.'documents.zip'),
                'application/zip');

            $this->Progress->at(100, 'Opération terminée. Redirection...');
            $this->Seance->id = $seance_id;
            $this->Seance->saveField('numero_depot', $idDepot);
            $this->Session->setFlash('Le dossier \"'.$libelle_seance.'\" a été ajouté (Depot n°'.$idDepot.')', 'growl', array('type'=>'important') );
        } catch (Exception $e) {
            if($e->getCode() == 500)
                $this->Session->setFlash('Le dossier \"'.$libelle_seance.'\" existe déjà', 'growl', array('type'=>'erreur') );
            $this->log('Export CMIS: Erreur '.$e->getCode()."! \n".$e->getMessage(), 'error');
        }
        $this->Progress->end('/postseances/index');
        $this->redirect('/postseances/index');
    }

function _createElement($domObj, $tag_name, $value = NULL, $attributes = NULL) {
    $element = ($value != NULL ) ? $domObj->createElement($tag_name, $value) : $domObj->createElement($tag_name);

    if( $attributes != NULL ) {
        foreach ($attributes as $attr=>$val) {
            $element->setAttribute($attr, $val);
        }
    }
    return $element;
}

function _createElementInfosups(&$zip, &$dom, &$domObj, $id, $model) {
    $aInfosup=$this->Infosup->export($id, $model);
    if (isset($aInfosup) && !empty($aInfosup)) {
        $infosup = $this->_createElement($dom, 'infosup'.$model, null, array('type' => 'infosup'));
        
        foreach($aInfosup as  $code=>$value) {
            if ($value['type']=='string')
                $infosup->appendChild($this->_createElement($dom, $code,  $value['content'], null));
            
            if ($value['type']=='file'){
                $filename=$value['id'].'.pdf';
                $filedata=$this->Conversion->convertirFlux($value['content'], 'pdf');
                $document = $this->_createElement($dom, 'document', null, array('nom'=> $filename, 'relname'=> $filename));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                ${$code} = $this->_createElement($dom, $code, null, null);
                ${$code}->appendChild($document);
                $infosup->appendChild(${$code});

                if($model=='Seance')
                    $zip->addFromString('infosup'.$model.DS.$filename, $filedata);
                else
                    $zip->addFromString('infosup'.$model.DS.$filename, $filedata);
            }
            
        }
        $domObj->appendChild($infosup);
     }
}


	function _sendToGed($seance_id) {
		$cmis = new CmisComponent();
		// Création du répertoire de séance
		$result = $cmis->client->getFolderTree($cmis->folder->id, 1);
		$seance = $this->Seance->find('first', array('conditions'=>array('Seance.id' =>$seance_id )));
		$my_seance_folder = $cmis->client->createFolder($cmis->folder->id, utf8_encode($seance['Typeseance']['libelle'])." ".utf8_encode($this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']))));

		$condition = array("seance_id"=> $seance_id,"etat >="  => 2 );
 
		$deliberations = $this->Deliberation->find('all', array('conditions'=>$condition,
				'order'     =>'Deliberation.position ASC'));
		foreach ($deliberations as $delib) {
			// Dépôt de la délibération et du rapport dans le répertoire que l'on vient de créer
			$my_new_folder = $cmis->client->createFolder($my_seance_folder->id, $delib['Deliberation']['id']);
			$obj_delib = $cmis->client->createDocument($my_new_folder->id,
					"deliberation.pdf",
					array (),
					$delib['Deliberation']['delib_pdf'],
					"application/pdf");

			if (!empty($deliberation['Deliberation']['signature'])) {
				$obj_delib = $cmis->client->createDocument($my_new_folder->id,
						"signature.zip",
						array (),
						$delib['Deliberation']['signature'],
						"application/zip");
			}

			// Dépôt du rapport de projet (on fixe l'etat à 2 pour etre sur d'avoir le rapport et non la délibération
			$model_id = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['Seance']['type_id'], '2');

			$this->requestAction("/models/generer/".$delib['Deliberation']['id']."/null/$model_id/0/1/rapport.pdf/1/false");
			$rapport = file_get_contents(WEBROOT_PATH."/files/generee/fd/null/".$delib['Deliberation']['id']."/rapport.pdf");
			$obj_rapport = $cmis->client->createDocument($my_new_folder->id,
					"rapport.pdf",
					array (),
					$rapport,
					"application/pdf");
			if (count($delib['Annex']) > 0) {
				$annex_folder = $cmis->client->createFolder($my_new_folder->id, 'Annexes');
				foreach ($delib['Annex'] as $annex) {
					$obj_annexe = $cmis->client->createDocument($annex_folder->id,
							$annex['filename'],
							array (),
							$annex['data'],
							$annex['filetype']);
				}
			}
		}
	}

	/*
	 Lorsque la GED est archiland

	function sendToGed($seance_id) {
	$paramsAuth  = array('username'     => 'adullact',
			'password'     => 'adullact');
	$clientAuth  = new SoapClient('http://ged-test.archiland.org:8080/alfresco/wsdl/authentication-service.wsdl');
	$reponseAuth = $clientAuth->__soapCall("startSession", array('parameters' => $paramsAuth));
	// Création du répertoire de séance
	$seance = $this->Seance->find('first', array('conditions'=>array('Seance.id' =>$seance_id )));

	$condition = array("Deliberation.seance_id"=> $seance_id,
			"Deliberation.etat >="  => 2 );
	$deliberations = $this->Deliberation->find('all', array('conditions'=>$condition,
			'order'     =>'Deliberation.position ASC'));
	foreach ($deliberations as $delib) {
	$requete = $this->_createActeRequestArchiland($reponseAuth, $delib);
	$clientArchi = new SoapClient('http://ged-test.archiland.org:8080/alfresco/wsdl/archiland-service.wsdl');
	$reponseArchi   = $clientArchi->__doRequest( $requete,
			'http://ged-test.archiland.org:8080/alfresco/api/ArchilandService',
			'createActe',
			$clientArchi->_soap_version);
	}
	$this->redirect('/postseances/index');
	}
	function _createActeRequestArchiland($reponseAuth, $delib) {
	$requete  = '<?xml version="1.0" encoding="UTF-8" ?'.'>';
	$requete .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
	xmlns:ns="http://www.atolcd.com/alpi/ws/1.0"
	xmlns:ns1="http://www.atolcd.com/alpi/wsmodel/1.0">';
	$requete .= '<soapenv:Header>';
	$requete .= '<wsse:Security soapenv:mustUnderstand="1"
	xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">';
	$requete .= '<wsu:Timestamp wsu:Id="Timestamp-14"
	xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">';
	$requete .= '<wsu:Created>'.gmdate("Y-m-d\TH:i:s\Z", time()-100).'</wsu:Created>';
	$requete .= '<wsu:Expires>'.gmdate("Y-m-d\TH:i:s\Z", time()+7200).'</wsu:Expires>';
	$requete .= '</wsu:Timestamp>';
	$requete .= '<wsse:UsernameToken wsu:Id="UsernameToken-666"
	xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">';
	$requete .= '<wsse:Username>'.$reponseAuth->startSessionReturn->username.'</wsse:Username>';
	$requete .= '<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">';
	$requete .= $reponseAuth->startSessionReturn->ticket;
	$requete .= '</wsse:Password>';
	$requete .= '</wsse:UsernameToken>';
	$requete .= '</wsse:Security>';
	$requete .= '</soapenv:Header>';
	$requete .= '<soapenv:Body>';
	$requete .= '<ns:createActe>';
	$requete .= '       <ns:requete>';
	$requete .= '<ns1:collectivite>491011698</ns1:collectivite>';
	//    $requete .= '<ns1:service>test_01</ns1:service>';
	$requete .= '<ns1:nom>'.utf8_encode($delib['Deliberation']['objet']).'</ns1:nom>';
	$requete .= '<ns1:type>deliberation</ns1:type>';
	$requete .= '<ns1:dateDebutDUA>'.gmdate("Y-m-d\TH:i:s\Z", time()).'</ns1:dateDebutDUA>';
	$requete .= '<ns1:dateSeance>'.str_replace(' ', 'T', $delib['Seance']['date']).'</ns1:dateSeance>';

	$requete .= '<ns1:fichiers>';
	$requete .= '<ns1:nom>deliberation.pdf</ns1:nom>';
	$requete .= '<ns1:type>deliberation</ns1:type>';
	$requete .= '<ns1:fichier>'.base64_encode($delib['Deliberation']['delib_pdf']).'</ns1:fichier>';
	$requete .= '</ns1:fichiers>';

	// Envoie du bordereau de s2low
	if (!empty($delib["Deliberation"]['tdt_id'])) {
	$ar =   $this->requestAction("/deliberations/getAR/".$delib["Deliberation"]['tdt_id']."/true");
	$requete .=  $this->_addFichier('bordereau.pdf', $ar, 'annexeDeliberation');
	}
	if (count($delib['Annex']) > 0)
		foreach ($delib['Annex'] as $annex)
		$requete .=  $this->_addFichier($annex['filename'], $annex['data'], 'annexeDeliberation');

	if (!empty($deliberation['Deliberation']['signature']))
		$requete .=  $this->_addFichier('signature.zip', $delib['Deliberation']['signature'], 'signatureDeliberation');

	$requete .= '</ns:requete>';
	$requete .= '</ns:createActe>';
	$requete .= '</soapenv:Body>';
	$requete .= '</soapenv:Envelope>';
	return $requete;
	}

	function _addFichier ($filename, $filecontent, $type) {
	$requete  = '<ns1:fichiers>';
	$requete .= '<ns1:nom>'.$filename.'</ns1:nom>';
	$requete .= "<ns1:type>$type</ns1:type>";
	$requete .= '<ns1:fichier>'.base64_encode($filecontent).'</ns1:fichier>';
	$requete .= '</ns1:fichiers>';
	return $requete;
	}


	*/

}
?>
