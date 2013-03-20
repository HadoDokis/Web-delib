<?php

class PostseancesController extends AppController {
	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'Html2' );
	var $components = array('Date', 'Gedooo', 'Cmis', 'Progress', 'Conversion');
	var $uses = array('Deliberation', 'Seance', 'User',  'Listepresence', 'Vote', 'Model', 'Theme', 'Typeseance');

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
			$format =0;
		$this->set('format', $format);

		$this->set ('USE_GEDOOO', Configure::read('USE_GEDOOO'));

		$this->Seance->Behaviors->attach('Containable');
		$seances = $this->Seance->find('all', array('conditions'=> array('Seance.traitee'=> 1),
				'order'     => 'Seance.date DESC',
				'fields'    => array('Seance.id', 'Seance.date', 'Seance.type_id', 'Seance.pv_figes'),
				'contain'   => array('Typeseance.libelle', 'Typeseance.action',
									  'Typeseance.modelconvocation_id',
									  'Typeseance.modelordredujour_id',
									  'Typeseance.modelpvsommaire_id',
									  'Typeseance.modelpvdetaille_id')));

		for ($i=0; $i<count($seances); $i++)
			$seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

		$this->set('seances', $seances);
	}

	function afficherProjets ($id=null, $return=null) {
		$format =  $this->Session->read('user.format.sortie');
		if (empty($format))
			$format =0;
		$this->set('format', $format);
		$this->set ('USE_GEDOOO', Configure::read('USE_GEDOOO'));
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
				$delibs[ $num_delib ]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($typeseance_id, $delib['Deliberation']['etat']);
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
		$result = false;
		// Avant de cloturer la séance, on stock les délibérations en base de données au format pdf
		$result = $this->_stockPvs($seance_id);
		if ($result){
			$this->Progress->end('/postseances/afficherProjets/'.$seance_id);
			exit;
		}
		else
			$this->Session->setFlash("Au moins un PV n'a pas &eacute;t&eacute; g&eacute;n&eacute;r&eacute; correctement...");
	}

	function _stockPvs($seance_id) {
		$this->Progress->start(200, 100,200,'#000000','#FFCC00','#006699');
		$result = true;

		$path = WEBROOT_PATH."/files/generee/PV/$seance_id";
		$this->Gedooo->createFile("$path/", 'empty', '');

		$seance = $this->Seance->read(null, $seance_id);
		$this->Progress->at(0, 'Préparation PV Sommaire : '.$seance['Typeseance']['libelle']);
		$model_pv_sommaire = $seance['Typeseance']['modelpvsommaire_id'];
		$model_pv_complet  = $seance['Typeseance']['modelpvdetaille_id'];
		$retour1 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_sommaire/0/1/pv_sommaire.pdf/1/false");
		$this->Progress->at(50, 'Préparation du PV Complet : '.$seance['Typeseance']['libelle']);
		$retour2 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_complet/0/1/pv_complet.pdf/1/false");
		$this->Progress->at(99, 'Sauvegarde des PVs');
		$path = WEBROOT_PATH."/files/generee/PV/$seance_id";
		$pv_sommaire = file_get_contents("$path/pv_sommaire.pdf");
		$pv_complet = file_get_contents("$path/pv_complet.pdf");

		if (!empty($pv_sommaire) && !empty($pv_complet)) {
			$this->Seance->id = $seance_id;
			$this->Seance->saveField('pv_sommaire', $pv_sommaire );
			$this->Seance->saveField('pv_complet', $pv_complet);
			$this->Seance->saveField('pv_figes',1);
			return true;
		}
		else {
			echo('Au moins une génération a échouée, les pvs ne peuvent être figés');
			die ("<br> <a href='/postseances/index'>Retour en Post-Séances</a>'");
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


        function sendToGed($seance_id) {
            $cmis = new CmisComponent();
            // Création du répertoire de séance
            $result = $cmis->client->getFolderTree($cmis->folder->id, 1); 


            $this->Seance->Behaviors->attach('Containable');
            $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
                                                         'contain'    => array('Typeseance.libelle') ));

            $my_seance_folder = $cmis->client->createFolder($cmis->folder->id, $seance['Typeseance']['libelle']." ".$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date'])));

            $delibs_id = $this->Seance->getDeliberationsId($seance_id);
            $output = array();

            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->formatOutput = true;
            $seance = $this->_createElement($dom, 'seance', null, array('id'=>$seance_id."-".$seance['Seance']['date'],
                                                                      'xmlns:webdelibdossier' => 'http://www.adullact.org/webdelib/infodossier/1.0',
                                                                      'xmlns:xm'  => 'http://www.w3.org/2005/05/xmlmine'));

            $seance->appendChild($this->_createElement($dom, 'typeDoc', 'Déliberation'));
            $doc = $dom->createElement('documents');
            foreach ($delibs_id as $delib_id) {
		$this->Deliberation->Behaviors->attach('Containable');
                $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                                                                  'fields'     => array('Deliberation.num_delib', 'Deliberation.objet_delib',
                                                                                        'Deliberation.titre', 'deliberation'),
                                                                  'contain'  => array('Service.libelle', 'Theme.libelle')));
                $dir=  TMP."delib_$delib_id/";
                $odtFile =  $dir."delib_$delib_id";
                if (!file_exists($dir))
                    mkdir($dir);
                file_put_contents($odtFile.".odt", $delib['Deliberation']['deliberation']);
                $contenu_delib =  $this->Conversion->convertirFichier($odtFile.".odt", 'txt');
                unlink($odtFile.".odt"); 
                rmdir($dir);
              
                $delib_filename = $delib_id.'-'.$delib['Deliberation']['num_delib'].'.pdf';
                $document = $this->_createElement($dom, 'document', null, array('nom'=>$delib_filename));

                $type = $this->_createElement($dom, 'type', null, array('id'=>'Délibération')); 
                $type->appendChild($this->_createElement($dom, 'titre', $delib['Deliberation']['objet_delib']));
                $type->appendChild($this->_createElement($dom, 'description', $delib['Deliberation']['titre']));
                $type->appendChild($this->_createElement($dom, 'contenuDeliberation', $contenu_delib));

                $type->appendChild($this->_createElement($dom, 'mimetype', 'application/pdf'));
                $type->appendChild($this->_createElement($dom, 'encoding', 'utf-8'));
                $type->appendChild($this->_createElement($dom, 'nomServiceEmetteur', $delib['Service']['libelle']));
                $type->appendChild($this->_createElement($dom, 'themeRAAD', $delib['Theme']['libelle']));

                $document->appendChild($type);
                $doc->appendChild($document);
            }
            $seance->appendChild($doc); 
            $dom->appendChild($seance);
            $xmlContent =  $dom->saveXML();
            $xml_desc = $cmis->client->createDocument($my_seance_folder->id,
                                                      "XML_DESC_$seance_id.xml",
                                                      array (),
                                                      $xmlContent,
                                                      "application/xml");
            $this->redirect('/postseances/index');

        }


   
function _createElement($domObj, $tag_name, $value = NULL, $attributes = NULL)
{
    $element = ($value != NULL ) ? $domObj->createElement($tag_name, $value) : $domObj->createElement($tag_name);

    if( $attributes != NULL )
    {
        foreach ($attributes as $attr=>$val)
        {
            $element->setAttribute($attr, $val);
        }
    }

    return $element;
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
