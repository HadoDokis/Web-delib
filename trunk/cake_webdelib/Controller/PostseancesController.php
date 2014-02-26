<?php

class PostseancesController extends AppController {
	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Fck', 'Html2' );
	var $components = array('Date', 'Gedooo', 'Cmis', 'Progress', 'Conversion');
	var $uses = array('Deliberation','Infosup', 'Seance', 'User',  'Listepresence', 'Vote', 'ModelOdtValidator.Modeltemplate', 'Theme', 'Typeseance', 'Typeacte', 'Nature', 'TdtMessage');

	var $demandeDroit = array('index');

    // Gestion des droits
    var $aucunDroit = array(
        'getNom',
        'getPresence',
        'getVote',
        'sendToGed'
    );
    var $commeDroit = array(
        'changeObjet' => 'Postseances:index',
        'afficherProjets' => 'Postseances:index',
        'changeStatus' => 'Postseances:index',
        'downloadPV' => 'Postseances:index'
    );

    function index() {
        $format = $this->Session->read('user.format.sortie');

        if (empty($format))
            $format = 0;

        $this->set('format', $format);

        $actions = array();
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:sendToGed") && Configure::read('USE_GED'))
            array_push($actions, 'ged');

        $this->Seance->Behaviors->attach('Containable');
        $seances = $this->Seance->find('all', array(
            'conditions' => array('Seance.traitee' => 1),
            'order' => 'Seance.date DESC',
            'fields' => array('Seance.id', 'Seance.date', 'Seance.type_id', 'Seance.pv_figes'),
            'contain' => array(
                'Typeseance.libelle',
                'Typeseance.action',
                'Typeseance.modelconvocation_id',
                'Typeseance.modelordredujour_id',
                'Typeseance.modelpvsommaire_id',
                'Typeseance.modelpvdetaille_id')));

        for ($i = 0; $i < count($seances); $i++) {
            $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));
            $seances[$i]['Seance']['Actions'] = $actions;
        }
        $this->set('use_s2low', Configure::read('USE_S2LOW'));
        $this->set('seances', $seances);
    }

    function afficherProjets($id = null, $return = null) {
        $format = $this->Session->read('user.format.sortie');
        if (empty($format))
            $format = 0;
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

    function getVote($id_delib) {
        $condition = "delib_id = $id_delib";
        $votes = $this->Vote->findAll($condition);
        if (!empty($votes)) {
            $resultat = $votes[0]['Vote']['commentaire'];
            return $resultat;
        }
    }

    function getPresence($id_delib, $present) {
        $condition = "delib_id =$id_delib AND present=$present";
        $presences = $this->Listepresence->findAll($condition);
        return $presences;
    }

    function getNom($id) {
        $data = $this->User->findAll("User.id = $id");
        return $data['0']['User']['prenom'] . ' ' . $data['0']['User']['nom'];
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

    function changeStatus($seance_id) {
        if (!$this->_stockPvs($seance_id))
            $this->Session->setFlash("Au moins un PV n'a pas été généré correctement... Impossible de figer les débats", 'growl', array('type' => 'error'));

        $this->redirect($this->referer());
    }

    function _stockPvs($seanceId) {
        // début de transaction
        $this->Seance->begin();

        try {
            // lecture de la séance
            $seance = $this->Seance->find('first', array(
                'recursive' => 0,
                'fields' => array('Seance.id', 'Seance.pv_figes', 'Typeseance.modelpvsommaire_id', 'Typeseance.modelpvdetaille_id'),
                'conditions' => array('Seance.id'=>$seanceId)));
            if (empty($seance)) throw new Exception();

            // fusion du pv sommaire
            $this->Seance->Behaviors->load('OdtFusion', array(
                'id' => $seanceId,
                'modelTemplateId' => $seance['Typeseance']['modelpvsommaire_id'],
                'modelOptions' => array('modelTypeName' => 'pvsommaire')));
            $this->Seance->odtFusion();
            $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', 'pdf');
            unset($this->Seance->odtFusionResult);
            if (empty($content)) throw new Exception();
            $seance['Seance']['pv_sommaire'] = &$content;
            if (!$this->Seance->save($seance['Seance'], false)) throw new Exception();
            unset($seance['Seance']['pv_sommaire']);

            // fusion du pv détaillé
            if ($seance['Typeseance']['modelpvsommaire_id'] != $seance['Typeseance']['modelpvdetaille_id']) {
                unset($content);
                $this->Seance->Behaviors->unload('OdtFusion');
                $this->Seance->Behaviors->load('OdtFusion', array(
                    'id' => $seanceId,
                    'modelTemplateId' => $seance['Typeseance']['modelpvdetaille_id'],
                    'modelOptions' => array('modelTypeName' => 'pvdetaille')));
                $this->Seance->odtFusion();
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', 'pdf');
                unset($this->Seance->odtFusionResult);
                if (empty($content)) throw new Exception();
            }
            $seance['Seance']['pv_complet'] = &$content;
            $seance['Seance']['pv_figes'] = true;
            if (!$this->Seance->save($seance['Seance'], false)) throw new Exception();
            unset($seance);
            unset($content);

            $this->Seance->commit();
            return true;
        } catch (Exception $e) {
            $this->Seance->rollback();
            return false;
        }
    }

    function downloadPV($seance_id, $type) {
        $seance = $this->Seance->read(null, $seance_id);
        header('Content-type: application/pdf');
        if ($type == "sommaire") {
            header('Content-Length: ' . strlen($seance['Seance']['pv_sommaire']));
            header('Content-Disposition: attachment; filename=pv_sommaire.pdf');
            die($seance['Seance']['pv_sommaire']);
        } else {
            header('Content-Length: ' . strlen($seance['Seance']['pv_complet']));
            header('Content-Disposition: attachment; filename=pv_complet.pdf');
            die($seance['Seance']['pv_complet']);
        }
    }

    /** Envoie pour un ged en protocole CMIS
     * 
     * @param type $seance_id
     */
    function sendToGed($seance_id) {

        $this->Progress->start(200, 100,200,'#000000','#FFCC00','#006699');

        $this->{'_sendToGedVersion' . Configure::read('GED_XML_VERSION')}($seance_id);

        $this->Progress->end('/postseances/index');
        $this->redirect('/postseances/index');
    }

    function _createElement($domObj, $tag_name, $value = NULL, $attributes = NULL) {
        $element = ($value != NULL ) ? $domObj->createElement($tag_name, $value) : $domObj->createElement($tag_name);

        if ($attributes != NULL) {
            foreach ($attributes as $attr => $val) {
                $element->setAttribute($attr, $val);
            }
        }
        return $element;
    }

    function _createElementInfosups(&$zip, &$dom, &$domObj, $id, $model) {
        $aInfosup = $this->Infosup->export($id, $model);
        if (isset($aInfosup) && !empty($aInfosup)) {
            $infosup = $this->_createElement($dom, 'infosup' . $model, null, array('type' => 'infosup'));

            foreach ($aInfosup as $code => $value) {
                if ($value['type'] == 'string')
                    $infosup->appendChild($this->_createElement($dom, $code, $value['content'], null));

                if ($value['type'] == 'file') {
                    $filename = $value['id'] . '.pdf';
                    $filedata = $this->Conversion->convertirFlux($value['content'], 'pdf');
                    $document = $this->_createElement($dom, 'document', null, array('nom' => $filename, 'relname' => $filename));
                    $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                    $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                    ${$code} = $this->_createElement($dom, $code, null, null);
                    ${$code}->appendChild($document);
                    $infosup->appendChild(${$code});

                    if ($model == 'Seance')
                        $zip->addFromString('infosup' . $model . DS . $filename, $filedata);
                    else
                        $zip->addFromString('infosup' . $model . DS . $filename, $filedata);
                }
            }
            $domObj->appendChild($infosup);
        }
    }

    function _deletetoGed(&$cmis, $libelle_seance) {
        // Règle de gestion on écrase les documents existants
        try {
            //On recherche le dossier
            $objet_cmis = $cmis->client->getObjectByPath(Configure::read('GED_REPO') . DS . $libelle_seance);
            if (is_object($objet_cmis)) {
                $this->Progress->at(10, 'Suppression du dernier export...' . $libelle_seance);
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
            //  echo $e->getCode();exit;
            // L'objet n'existe pas encore : ne rien faire
        }
    }

    function _getTdtForGed($iTdt) {

        $flux = $this->S2low->getFluxRetour($iTdt);
        $codeRetour = substr($flux, 3, 1);

        if ($codeRetour == 4) {
            return mb_substr($flux, strpos($flux, '<?xml version'), strlen($flux));
        }

        return false;
    }

    function _getTdtMessageForGed($delib_id) {

        $messages = $this->TdtMessage->find('all', array(
            'conditions' => array('TdtMessage.delib_id' => $delib_id),
            'recursive' => '-1')
        );
        //debug($messages);
        foreach ($messages as &$message) {

            switch ($message['TdtMessage']['type_message']) {
                case 2:
                    $message['TdtMessage']['libelle_message'] = 'Courrier simple';
                    break;
                case 3:
                    $message['TdtMessage']['libelle_message'] = 'Demande de pièces complémentaires';
                    break;
                case 4:
                    $message['TdtMessage']['libelle_message'] = 'Lettre d\'observation';
                    break;
                case 5:
                    $message['TdtMessage']['libelle_message'] = 'Déféré au tribunal administratif';
                    break;
                default:
                    break;
            }
            $message['TdtMessage']['reponse'] = 1;
            $flux = $this->S2low->getDocument($message['TdtMessage']['message_id']);

            $contents = gzread($flux);
            $message['TdtMessage']['data_'] = $flux;
            var_dump($message);
            exit;
        }

        return $messages;
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

    function _sendToGedVersion1($seance_id) {

        $path = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . $seance_id . DS . 'null' . DS;

        try {
            $cmis = new CmisComponent;
            $cmis->CmisComponent_Service();
            $this->Conversion = new ConversionComponent();

            // Création du répertoire de séance
            $result = $cmis->client->getFolderTree($cmis->folder->id, 1);

            $this->Progress->at(0, 'Création du répertoire de séance...');

            $this->Seance->Behaviors->attach('Containable');
            $seance = $this->Seance->find('first', array(
                'conditions' => array('Seance.id' => $seance_id),
                'contain' => array('Typeseance.libelle', 'Typeseance.modelconvocation_id', 'Typeseance.modelprojet_id', 'Typeseance.modelordredujour_id')));

            $this->Progress->at(5, 'Recherche de la séance en base de données...');

            $date_seance = $seance['Seance']['date'];
            $date_convocation = $seance['Seance']['date_convocation'];
            $type_seance = $seance['Typeseance']['libelle'];
            $libelle_seance = $seance['Typeseance']['libelle'] . " " . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));


            $this->_deletetoGed($cmis, $libelle_seance);

            $zip = new ZipArchive;
            @unlink($path . 'documents.zip');
            $zip->open($path . 'documents.zip', ZipArchive::CREATE);

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
            $idDepot = $seance['Seance']['numero_depot'] + 1;

            $dom_depot = $this->_createElement($dom, 'depot', null, array(
                'idDepot' => $idDepot,
                'xmlns:webdelibdossier' => 'http://www.adullact.org/webdelib/infodossier/1.0',
                'xmlns:xm' => 'http://www.w3.org/2005/05/xmlmine'));

            $dom_seance = $this->_createElement($dom, 'seance', null, array('idSeance' => $seance_id));
            $dom_seance->appendChild($this->_createElement($dom, 'typeSeance', $type_seance));
            $dom_seance->appendChild($this->_createElement($dom, 'dateSeance', $date_seance));
            $dom_seance->appendChild($this->_createElement($dom, 'dateConvocation', $date_convocation));

            //Noeud document[convocation]
            $this->Progress->at(25, 'Génération de la convocation...');
            $document = $this->_createElement($dom, 'document', null, array('nom' => 'convocation.pdf', 'type' => 'convocation'));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            //Génération du fichier et ajout au zip
            $this->requestAction('/models/generer/null/' . $seance_id . '/' . $seance['Typeseance']['modelconvocation_id'] . '/0/0/retour/0/true');
            $projet_filename = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . $seance_id . DS . 'null' . DS . 'Document.pdf';
            $zip->addFromString('convocation.pdf', file_get_contents($projet_filename));

            //Noeud document[odj]
            $this->Progress->at(40, 'Génération de l\'ordre du jour...');
            $document = $this->_createElement($dom, 'document', null, array('nom' => 'odj.pdf', 'type' => 'odj'));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            //Génération du fichier et ajout au zip
            $this->requestAction('/models/generer/null/' . $seance_id . '/' . $seance['Typeseance']['modelordredujour_id'] . '/0/0/retour/0/true');
            $odj_filename = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . $seance_id . DS . 'null' . DS . 'Document.pdf';
            $zip->addFromString('odj.pdf', file_get_contents($odj_filename));

            //Noeud document[pv_sommaire]
            $this->Progress->at(50, 'Ajout du PV sommaire...');
            $document = $this->_createElement($dom, 'document', null, array('nom' => 'pv.pdf', 'type' => 'pv_sommaire'));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            //Ajout au zip
            if (!empty($seance['Seance']['pv_sommaire']))
                $zip->addFromString('pv.pdf', $seance['Seance']['pv_sommaire']);

            //Noeud document[pv_complet]
            $this->Progress->at(55, 'Ajout du PV complet...');
            $document = $this->_createElement($dom, 'document', null, array('nom' => 'pvcomplet.pdf', 'type' => 'pv_complet'));
            $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
            $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
            $dom_seance->appendChild($document);
            if (!empty($seance['Seance']['pv_complet']))
                $zip->addFromString('pvcomplet.pdf', $seance['Seance']['pv_complet']);
            //Infos supps
            $this->Progress->at(60, 'Ajout des informations supplémentaires de séance...');
            $this->_createElementInfosups($zip, $dom, $dom_seance, $seance_id, 'Seance');

            //Insertion du noeud xml seance
            $dom_depot->appendChild($dom_seance);

            $this->Progress->at(66, 'Ajout des délibérations...');
            foreach ($delibs_id as $delib_id) {

                $doc = $this->_createElement($dom, 'dossierActe', null, array('idActe' => $delib_id, 'refSeance' => $seance_id));

                $this->Deliberation->Behaviors->attach('Containable');
                $delib = $this->Deliberation->find('first', array(
                    'conditions' => array('Deliberation.id' => $delib_id),
                    'fields' => array('Deliberation.id', 'Deliberation.num_delib', 'Deliberation.objet_delib', 'Deliberation.titre',
                        'Deliberation.delib_pdf', 'Deliberation.tdt_data_pdf', 'Deliberation.tdt_data_bordereau_pdf', 'Deliberation.deliberation',
                        'Deliberation.deliberation_size', 'Deliberation.signature', 'Deliberation.dateAR'),
                    'contain' => array(
                        'Service' => array('fields' => array('libelle')),
                        'Theme' => array('fields' => array('libelle')),
                        'Typeacte' => array('fields' => array('nature_id')),
                        'Redacteur' => array('fields' => array('nom', 'prenom')),
                        'Rapporteur' => array('fields' => array('nom', 'prenom')),
                )));

                $nature = $this->Nature->find('first', array(
                    'fields' => array('libelle'),
                    'conditions' => array('Nature.id' => $delib['Typeacte']['nature_id'])));

                $doc->appendChild($this->_createElement($dom, 'natureACTE', $nature['Nature']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'dateACTE', $date_seance));
                $doc->appendChild($this->_createElement($dom, 'numeroACTE', $delib['Deliberation']['num_delib']));
                $doc->appendChild($this->_createElement($dom, 'themeACTE', $delib['Theme']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'emetteurACTE', $delib['Service']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'redacteurACTE', $delib['Redacteur']['prenom'] . ' ' . $delib['Redacteur']['nom']));
                $doc->appendChild($this->_createElement($dom, 'rapporteurACTE', $delib['Rapporteur']['prenom'] . ' ' . $delib['Rapporteur']['nom']));
                $doc->appendChild($this->_createElement($dom, 'typeseanceACTE', $type_seance));
                $doc->appendChild($this->_createElement($dom, 'dateAR', $delib['Deliberation']['dateAR'])); // utile ??
                //<listeCommissions>
                $seances_id = $this->Deliberation->getSeancesid($delib_id);

                $listeCommissions = '';
                foreach ($seances_id as $commission_id) {
                    if (!$this->Deliberation->Seance->isSeanceDeliberante($commission_id)) {
                        $typeSeance = $this->Deliberation->Seance->Typeseance->getLibelle($this->Deliberation->Seance->getType($commission_id));
                        $listeCommissions .= $typeSeance . ' : ' . $this->Deliberation->Seance->getDate($commission_id) . ', ';
                    }
                }

                $doc->appendChild($this->_createElement($dom, 'listeCommissions', $listeCommissions));
                //</listeCommissions>
                //Infos supps de délibération
                $this->_createElementInfosups($zip, $dom, $doc, $delib_id, 'Deliberation');

                //Noeud document[TexteActe]
                $delib_filename = $delib_id . '-' . $delib['Deliberation']['num_delib'] . '.pdf';
                $document = $this->_createElement($dom, 'document', null, array('nom' => $delib_filename, 'relname' => $delib_filename, 'type' => 'TexteActe'));
                $document->appendChild($this->_createElement($dom, 'titre', $delib['Deliberation']['objet_delib']));
                $document->appendChild($this->_createElement($dom, 'description', $delib['Deliberation']['titre']));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $doc->appendChild($document);
                //Ajout au zip
                $zip->addFromString($delib_filename, (!empty($delib['Deliberation']['tdt_data_pdf']) ? $delib['Deliberation']['tdt_data_pdf'] : $delib['Deliberation']['delib_pdf']));

                //Noeud document[Rapport]
                if (!empty($delib['Deliberation']['deliberation_size'])) {
                    $document = $this->_createElement($dom, 'document', null, array('nom' => $delib_filename, 'relname' => $delib_filename, 'type' => 'Rapport'));
                    $document->appendChild($this->_createElement($dom, 'titre', $delib['Deliberation']['objet_delib']));
                    $document->appendChild($this->_createElement($dom, 'description', $delib['Deliberation']['titre']));
                    $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                    $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                    $doc->appendChild($document);
                    //Génération du rapport et ajout au zip
                    $this->requestAction('/models/generer/' . $delib_id . '/null/' . $seance['Typeseance']['modelprojet_id'] . '/0/2/retour/0/true/false');
                    $projet_filename = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . 'null' . DS . $delib_id . DS . 'retour.pdf2';
                    //Ajout au zip
                    $zip->addFromString('Rapports' . DS . $delib_filename, file_get_contents($projet_filename));
                }

                //Ajout de la signature (XML+ZIP)
                $signatureName = $delib['Deliberation']['id'] . '-signature.zip';
                //Création du noeud XML
                $document = $this->_createElement($dom, 'document', null, array('nom' => $signatureName, 'relname' => $signatureName, 'type' => 'Signature'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/zip'));
                $doc->appendChild($document);
                //Ajout à l'archive
                $zip->addFromString('Annexes' . DS . $signatureName, $delib['Deliberation']['signature']);

                //Ajout du bordereau (XML+ZIP)
                $bordereauName = $delib['Deliberation']['id'] . '-bordereau.pdf';
                //Création du noeud XML
                $document = $this->_createElement($dom, 'document', null, array('nom' => $bordereauName, 'relname' => $bordereauName, 'type' => 'Bordereau'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $doc->appendChild($document);
                //Ajout à l'archive
                $zip->addFromString('Annexes' . DS . $bordereauName, $delib['Deliberation']['tdt_data_bordereau_pdf']);


                //Ajout des annexes
                $annexes_id =  $this->Deliberation->Annex->getAnnexesFromDelibId($delib_id, true);
                if (!empty($annexes_id)) {
                    foreach ($annexes_id as $annex_id) {
                        $annex_id = $annex_id['Annex']['id'];
                        $annex = $this->Deliberation->Annex->find('first', array(
                            'conditions' => array('Annex.id' => $annex_id),
                            'fields' => array('Annex.id', 'Annex.titre', 'Annex.filename', 'Annex.filetype', 'Annex.data_pdf', 'Annex.data'),
                            'recursive' => -1));

                        switch ($annex['Annex']['filetype']) {
                            case 'application/pdf':
                                $annexe_content = $annex['Annex']['data_pdf'];
                                $annexe_filetype = 'application/pdf';
                                $annexe_filename = $annex['Annex']['filename'];
                                break;

                            case 'application/vnd.oasis.opendocument.text':
                                $annexe_content = $this->Conversion->convertirFlux($annex['Annex']['data'], 'pdf');
                                $annexe_filetype = 'application/pdf';
                                $annexe_filename = str_replace('odt', 'pdf', $annex['Annex']['filename']);
                                break;

                            default:
                                $annexe_content = $annex['Annex']['data'];
                                $annexe_filetype = $annex['Annex']['filetype'];
                                $annexe_filename = $annex['Annex']['filename'];
                                break;
                        }
                        //Création du noeud XML <document> de l'annexe
                        $document = $this->_createElement($dom, 'document', null, array('nom' => $annexe_filename, 'relname' => $annex['Annex']['id'] . '.pdf', 'type' => 'Annexe'));
                        $document->appendChild($this->_createElement($dom, 'titre', $annex['Annex']['titre']));
                        $document->appendChild($this->_createElement($dom, 'mimetype', $annexe_filetype));
                        $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                        $doc->appendChild($document);
                        //Ajout du fichier annexe à l'archive
                        $zip->addFromString('Annexes' . DS . $annex['Annex']['id'] . '.pdf', $annexe_content);
                    }
                }
                $dom_depot->appendChild($doc);
            }

            $zip->close();
            $dom->appendChild($dom_depot);
            $xmlContent = $dom->saveXML();

            $this->Progress->at(95, 'Envoi du XML à la GED...');

            $cmis->client->createDocument(
                    $my_seance_folder->id, "XML_DESC_$seance_id.xml", array(), $xmlContent, 'application/xml');

            $cmis->client->createDocument(
                    $my_seance_folder->id, 'documents.zip', array(), file_get_contents($path . 'documents.zip'), 'application/zip');

            $this->Progress->at(100, 'Opération terminée. Redirection...');
            $this->Seance->id = $seance_id;
            $this->Seance->saveField('numero_depot', $idDepot);
            $this->Session->setFlash('Le dossier \"' . $libelle_seance . '\" a été ajouté (Depot n°' . $idDepot . ')', 'growl', array('type' => 'important'));
        } catch (Exception $e) {
            if ($e->getCode() == 500)
                $this->Session->setFlash('Le dossier \"' . $libelle_seance . '\" existe déjà', 'growl', array('type' => 'erreur'));
            $this->log('Export CMIS: Erreur ' . $e->getCode() . "! \n" . $e->getMessage(), 'error');
        }
    }

    function _sendToGedVersion2($seance_id) {

        $path = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . $seance_id . DS . 'null' . DS;

        try {
            $cmis = new CmisComponent;
            $cmis->CmisComponent_Service();
            $this->Conversion = new ConversionComponent();
            // Création du répertoire de séance
            $result = $cmis->client->getFolderTree($cmis->folder->id, 1);

            $this->Progress->at(0, 'Création du répertoire de séance...');

            $this->Seance->Behaviors->attach('Containable');
            $seance = $this->Seance->find('first', array(
                'conditions' => array('Seance.id' => $seance_id),
                'contain' => array('Typeseance.libelle', 'Typeseance.modelconvocation_id', 'Typeseance.modelprojet_id', 'Typeseance.modelordredujour_id')));

            $this->Progress->at(5, 'Recherche de la séance en base de données...');

            $date_seance = $seance['Seance']['date'];
            $date_convocation = $seance['Seance']['date_convocation'];
            $type_seance = $seance['Typeseance']['libelle'];
            $libelle_seance = $seance['Typeseance']['libelle'] . " " . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));

            $this->_deletetoGed($cmis, $libelle_seance);

            $zip = new ZipArchive;
            @unlink($path . 'documents.zip');
            $zip->open($path . 'documents.zip', ZipArchive::CREATE);

            $this->Progress->at(15, 'Création des dossiers...');
            // Création des dossiers
            $my_seance_folder = $cmis->client->createFolder($cmis->folder->id, $libelle_seance);
            // Création des dossiers vides
            $zip->addEmptyDir('Rapports');
            $zip->addEmptyDir('Annexes');

            $delibs_id = $this->Seance->getDeliberationsId($seance_id);

            //$this->Progress->at(20, 'Création du fichier XML...');
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->formatOutput = true;
            $idDepot = $seance['Seance']['numero_depot'] + 1;

            $dom_depot = $this->_createElement($dom, 'depot', null, array(
                'versionDepot' => 2,
                'idDepot' => $idDepot,
                'dateDepot' => date("Y-m-d H:i:s"),
                'xmlns:webdelibdossier' => 'http://www.adullact.org/webdelib/infodossier/1.0',
                'xmlns:xm' => 'http://www.w3.org/2005/05/xmlmine'));

            $dom_seance = $this->_createElement($dom, 'seance', null, array('idSeance' => $seance_id));
            $dom_seance->appendChild($this->_createElement($dom, 'typeSeance', $type_seance));
            $dom_seance->appendChild($this->_createElement($dom, 'dateSeance', $date_seance));
            $dom_seance->appendChild($this->_createElement($dom, 'dateConvocation', $date_convocation));

            if (true) {
                //Noeud document[convocation]
                $this->Progress->at(25, 'Génération de la convocation...');
                $document = $this->_createElement($dom, 'document', null, array('nom' => 'convocation.pdf', 'type' => 'convocation'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $dom_seance->appendChild($document);
                //Génération du fichier et ajout au zip
                $this->requestAction('/models/generer/null/' . $seance_id . '/' . $seance['Typeseance']['modelconvocation_id'] . '/0/0/retour/0/true');
                $projet_filename = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . $seance_id . DS . 'null' . DS . 'Document.pdf';
                $zip->addFromString('convocation.pdf', file_get_contents($projet_filename));

                //Noeud document[odj]
                $this->Progress->at(40, 'Génération de l\'ordre du jour...');
                $document = $this->_createElement($dom, 'document', null, array('nom' => 'odj.pdf', 'type' => 'odj'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $dom_seance->appendChild($document);
                //Génération du fichier et ajout au zip
                $this->requestAction('/models/generer/null/' . $seance_id . '/' . $seance['Typeseance']['modelordredujour_id'] . '/0/0/retour/0/true');
                $odj_filename = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . $seance_id . DS . 'null' . DS . 'Document.pdf';
                $zip->addFromString('odj.pdf', file_get_contents($odj_filename));

                //Noeud document[pv_sommaire]
                $this->Progress->at(50, 'Ajout du PV sommaire...');
                $document = $this->_createElement($dom, 'document', null, array('nom' => 'pv.pdf', 'type' => 'pv_sommaire'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $dom_seance->appendChild($document);
                //Ajout au zip
                if (!empty($seance['Seance']['pv_sommaire']))
                    $zip->addFromString('pv.pdf', $seance['Seance']['pv_sommaire']);

                //Noeud document[pv_complet]
                $this->Progress->at(55, 'Ajout du PV complet...');
                $document = $this->_createElement($dom, 'document', null, array('nom' => 'pvcomplet.pdf', 'type' => 'pv_complet'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $dom_seance->appendChild($document);
                if (!empty($seance['Seance']['pv_complet']))
                    $zip->addFromString('pvcomplet.pdf', $seance['Seance']['pv_complet']);
            }

            //Infos supps
            $this->Progress->at(60, 'Ajout des informations supplémentaires de séance...');
            $this->_createElementInfosups($zip, $dom, $dom_seance, $seance_id, 'Seance');

            //Insertion du noeud xml seance
            $dom_depot->appendChild($dom_seance);

            $this->Progress->at(66, 'Ajout des délibérations...');
            foreach ($delibs_id as $delib_id) {

                $doc = $this->_createElement($dom, 'dossierActe', null, array('idActe' => $delib_id, 'refSeance' => $seance_id));

                $this->Deliberation->Behaviors->attach('Containable');
                $delib = $this->Deliberation->find('first', array(
                    'conditions' => array('Deliberation.id' => $delib_id),
                    'fields' => array('Deliberation.id', 'Deliberation.num_delib', 'Deliberation.objet_delib', 'Deliberation.titre',
                        'Deliberation.delib_pdf', 'Deliberation.tdt_data_pdf', 'Deliberation.tdt_data_bordereau_pdf', 'Deliberation.deliberation',
                        'Deliberation.deliberation_size', 'Deliberation.signature', 'Deliberation.dateAR', 'Deliberation.signee', 'Deliberation.etat_parapheur', 'Deliberation.tdt_id'),
                    'contain' => array(
                        'Service' => array('fields' => array('libelle')),
                        'Theme' => array('fields' => array('libelle')),
                        'Typeacte' => array('fields' => array('nature_id')),
                        'Redacteur' => array('fields' => array('nom', 'prenom')),
                        'Rapporteur' => array('fields' => array('nom', 'prenom')),
                )));

                $nature = $this->Nature->find('first', array(
                    'fields' => array('libelle'),
                    'conditions' => array('Nature.id' => $delib['Typeacte']['nature_id'])));

                $doc->appendChild($this->_createElement($dom, 'libelle', $delib['Deliberation']['objet_delib']));
                $doc->appendChild($this->_createElement($dom, 'titre', $delib['Deliberation']['titre']));
                $doc->appendChild($this->_createElement($dom, 'natureACTE', $nature['Nature']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'dateACTE', $date_seance));
                $doc->appendChild($this->_createElement($dom, 'numeroACTE', $delib['Deliberation']['num_delib']));
                $doc->appendChild($this->_createElement($dom, 'themeACTE', $delib['Theme']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'emetteurACTE', $delib['Service']['libelle']));
                $doc->appendChild($this->_createElement($dom, 'redacteurACTE', $delib['Redacteur']['prenom'] . ' ' . $delib['Redacteur']['nom']));
                $doc->appendChild($this->_createElement($dom, 'rapporteurACTE', $delib['Rapporteur']['prenom'] . ' ' . $delib['Rapporteur']['nom']));
                $doc->appendChild($this->_createElement($dom, 'typeseanceACTE', $type_seance));
                $doc->appendChild($this->_createElement($dom, 'dateAR', $delib['Deliberation']['dateAR'])); // utile ??
                //<listeCommissions>
                $seances_id = $this->Deliberation->getSeancesid($delib_id);

                $document = $this->_createElement($dom, 'listeCommissions', null);
                $listeCommissions = '';
                foreach ($seances_id as $commission_id) {
                    if (!$this->Deliberation->Seance->isSeanceDeliberante($commission_id)) {
                        $commission = $this->_createElement($dom, 'commission', null, array('idCommission' => $commission_id));
                        $commission->appendChild($this->_createElement($dom, 'typeSeance', $this->Deliberation->Seance->Typeseance->getLibelle($this->Deliberation->Seance->getType($commission_id))));
                        $commission->appendChild($this->_createElement($dom, 'dateSeance', $this->Deliberation->Seance->getDate($commission_id)));
                        $document->appendChild($commission);
                    }
                }
                if (!empty($document))
                    $doc->appendChild($document);

                //Infos supps de délibération
                $this->_createElementInfosups($zip, $dom, $doc, $delib_id, 'Deliberation');

                $aDocuments = array();
                $i = 1;
                //Noeud document[TexteActe]
                $aDocuments['TexteActe'] = $i++;
                $delib_filename = $delib_id . '-' . $delib['Deliberation']['num_delib'] . '.pdf';
                $document = $this->_createElement($dom, 'document', null, array(
                    'idDocument' => $aDocuments['TexteActe'],
                    'nom' => $delib_filename, 'relname' => $delib_filename,
                    'type' => 'TexteActe'));
                if ($delib['Deliberation']['signee'] == 1 && $delib['Deliberation']['etat_parapheur'] == 1)
                    $document->appendChild($this->_createElement($dom, 'signature', 'application/pdf', array('formatSignature' => 'p7s')));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $doc->appendChild($document);
                //Ajout au zip
                $zip->addFromString($delib_filename, $delib['Deliberation']['delib_pdf']);

                //Noeud document[TexteActe]
                if (!empty($delib['Deliberation']['tdt_data_pdf'])) {
                    $aDocuments['ActeTampon'] = $i++;
                    $delib_filename = $delib_id . '-' . $delib['Deliberation']['num_delib'] . '.pdf';
                    $document = $this->_createElement($dom, 'document', null, array(
                        'idDocument' => $aDocuments['ActeTampon'],
                        'nom' => $delib_filename, 'relname' => ActeTampon . DS . $delib_filename,
                        'type' => 'ActeTampon'));
                    $document->appendChild($this->_createElement($dom, 'signature', 'false'));
                    $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                    $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                    $doc->appendChild($document);
                    //Ajout au zip
                    $zip->addFromString($delib_filename, 'ActeTampon' . DS . $delib['Deliberation']['tdt_data_pdf']);
                }

                //Noeud document[Rapport]
                if (!empty($delib['Deliberation']['deliberation_size'])) {
                    $aDocuments['Rapport'] = $i++;
                    $document = $this->_createElement($dom, 'document', null, array(
                        'idDocument' => $aDocuments['Rapport'],
                        'nom' => $delib_filename,
                        'relName' => 'Rapports' . DS . $delib_filename,
                        'type' => 'Rapport'));
                    $document->appendChild($this->_createElement($dom, 'signature', 'false'));
                    $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                    $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                    $doc->appendChild($document);
                    //Génération du rapport et ajout au zip /0/0/retour/0/true
                    $this->requestAction('/models/generer/' . $delib_id . '/null/' . $seance['Typeseance']['modelprojet_id'] . '/0/2/retour/0/true');
                    $projet_filename = WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'fd' . DS . 'null' . DS . $delib_id . DS . 'retour.pdf';
                    //Ajout au zip
                    $zip->addFromString('Rapports' . DS . $delib_filename, file_get_contents($projet_filename));
                }

                //Ajout de la signature (XML+ZIP)
                $signatureName = $delib['Deliberation']['id'] . '-signature.zip';
                $aDocuments['Signature'] = $i++;
                //Création du noeud XML
                $document = $this->_createElement($dom, 'document', null, array(
                    'idDocument' => $aDocuments['Signature'],
                    'refDocument' => $aDocuments['TexteActe'],
                    'nom' => $signatureName, 'relName' => 'Signatures' . DS . $signatureName,
                    'type' => 'Signature'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/zip'));
                $doc->appendChild($document);
                //Ajout à l'archive
                $zip->addFromString('Signature' . DS . $signatureName, $delib['Deliberation']['signature']);

                //Ajout du bordereau (XML+ZIP)
                $bordereauName = $delib['Deliberation']['id'] . '-bordereau.pdf';
                $aDocuments['Bordereau'] = $i++;
                //Création du noeud XML
                $document = $this->_createElement($dom, 'document', null, array(
                    'idDocument' => $aDocuments['Bordereau'],
                    'refDocument' => $aDocuments['ActeTampon'],
                    'nom' => $bordereauName,
                    'relname' => 'Bordereaux' . DS . $bordereauName,
                    'type' => 'Bordereau'));
                $document->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $doc->appendChild($document);
                //Ajout à l'archive
                $zip->addFromString('Bordereaux' . DS . $bordereauName, $delib['Deliberation']['tdt_data_bordereau_pdf']);

                //Ajout des annexes
                $annexes_id = $this->Deliberation->Annex->getAnnexesFromDelibId($delib_id, 1);
                if (!empty($annexes_id)) {
                    foreach ($annexes_id as $annex_id) {
                        $aDocuments['Annexe'] = $i++;
                        $annexe = $this->Deliberation->Annex->getContentToGed($annex_id['Annex']['id']);
                        //Création du noeud XML <document> de l'annexe
                        $document = $this->_createElement($dom, 'document', null, array(
                            'idDocument' => $aDocuments['Annexe'],
                            'nom' => $annexe['filename'],
                            'relName' => 'Annexes' . DS . $annexe['filename'],
                            'type' => 'Annexe'));
                        $document->appendChild($this->_createElement($dom, 'titre', $annexe['titre']));
                        $document->appendChild($this->_createElement($dom, 'signature', 'false'));
                        if ($annexe['joindre_ctrl_legalite'])
                            $document->appendChild($this->_createElement($dom, 'transmisprefecture', 'true'));
                        $document->appendChild($this->_createElement($dom, 'mimetype', $annexe['filetype']));
                        $document->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                        $doc->appendChild($document);
                        //Ajout du fichier annexe à l'archive
                        $zip->addFromString('Annexes' . DS . $annexe['filename'], $annexe['data']);
                    }
                }
                $dom_depot->appendChild($doc);

                //Ajout de la signature (XML+ZIP)
                //
                //Création du noeud XML
                if (!empty($delib['Deliberation']['tdt_id'])) {
                    $aDocuments['ARacte'] = $i++;
                    $sARacte = $this->_getTdtForGed($delib['Deliberation']['tdt_id']);
                    $document = $this->_createElement($dom, 'document', null, array(
                        'idDocument' => $aDocuments['ARacte'],
                        'nom' => 'ARacte.xml',
                        'relname' => 'ARacte/ARacte.xml',
                        'type' => 'ARacte'));
                    $document->appendChild($this->_createElement($dom, 'mimetype', 'application/xml'));
                    $doc->appendChild($document);
                    //Ajout à l'archive
                    $zip->addFromString('ARacte' . DS . 'ARacte.xml', $sARacte);
                    unset($sARacte);
                }
            }

            $zip->close();
            $dom->appendChild($dom_depot);
            $xmlContent = $dom->saveXML();

            $this->Progress->at(95, 'Envoi du XML à la GED...');

            $cmis->client->createDocument(
                    $my_seance_folder->id, "XML_DESC_$seance_id.xml", array(), $xmlContent, 'application/xml');

            $cmis->client->createDocument(
                    $my_seance_folder->id, 'documents.zip', array(), file_get_contents($path . 'documents.zip'), 'application/zip');

            $this->Progress->at(100, 'Opération terminée. Redirection...');
            $this->Seance->id = $seance_id;
            $this->Seance->saveField('numero_depot', $idDepot);
            $this->Session->setFlash('Le dossier \"' . $libelle_seance . '\" a été ajouté (Depot n°' . $idDepot . ')', 'growl', array('type' => 'important'));
        } catch (Exception $e) {
            if ($e->getCode() == 500)
                $this->Session->setFlash('Le dossier \"' . $libelle_seance . '\" existe déjà', 'growl', array('type' => 'erreur'));
            $this->log('Export CMIS: Erreur ' . $e->getCode() . "! \n" . $e->getMessage(), 'error');
        }
    }

}
?>
