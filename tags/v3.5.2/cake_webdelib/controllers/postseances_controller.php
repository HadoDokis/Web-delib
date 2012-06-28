<?php

class PostseancesController extends AppController {

	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'Html2' );
	var $components = array('Date', 'Gedooo', 'Cmis', 'Progress');
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
		//'generateDeliberation'=>'Postseances:index',
		//'generatePvComplet'=>'Postseances:index',
		//'generatePvSommaire'=>'Postseances:index',
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
                                                         'order '    => 'Seance.date DESC',
                                                         'fields'    => array('id', 'date', 'type_id', 'pv_figes'),
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

	    $condition = array("seance_id"=>$id, "etat >="=>2);
	    if (!isset($return)) {
	        $this->set('lastPosition', $this->Seance->getLastPosition($id));
                $typeseance_id = $this->Seance->getType($id);
	        $deliberations = $this->Seance->getDeliberations($id);
	        for ($i=0; $i<count($deliberations); $i++)
		    	$deliberations[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($typeseance_id, $deliberations[$i]['Deliberation']['etat']);
		$this->set('seance_id', $id);
		$this->set('projets', $deliberations);
		$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->Seance->getDate($id))));
	    }
	    else
	        return ($this->Deliberation->find('all', array('conditions'=>$condition, 'order'=>array('Deliberation.position ASC'))));
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
        // Avant de cloturer la s�ance, on stock les d�lib�rations en base de donn�es au format pdf
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
		$this->Progress->at(0, 'Pr�paration PV Sommaire : '.$seance['Typeseance']['libelle']);
		$model_pv_sommaire = $seance['Typeseance']['modelpvsommaire_id'];
		$model_pv_complet  = $seance['Typeseance']['modelpvdetaille_id'];
		$retour1 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_sommaire/0/1/pv_sommaire.pdf/1/false");
		$this->Progress->at(50, 'Pr�paration du PV Complet : '.$seance['Typeseance']['libelle']);
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
			echo('Au moins une g�n�ration a �chou�e, les pvs ne peuvent �tre fig�s');
			die ("<br> <a href='/postseances/index'>Retour en Post-S�ances</a>'");
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
            // Cr�ation du r�pertoire de s�ance
            $result = $cmis->client->getFolderTree($cmis->folder->id, 1); 
            $seance = $this->Seance->find('first', array('conditions'=>array('Seance.id' =>$seance_id )));
            $my_seance_folder = $cmis->client->createFolder($cmis->folder->id, utf8_encode($seance['Typeseance']['libelle'])." ".utf8_encode($this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']))));

            $condition = array("seance_id"=> $seance_id, 
                               "etat >="  => 2 );
            $deliberations = $this->Deliberation->find('all', array('conditions'=>$condition, 
                                                                    'order'     =>'Deliberation.position ASC'));
            foreach ($deliberations as $delib) {
                // D�p�t de la d�lib�ration et du rapport dans le r�pertoire que l'on vient de cr�er
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

                // D�p�t du rapport de projet (on fixe l'etat � 2 pour etre sur d'avoir le rapport et non la d�lib�ration
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
            // Cr�ation du r�pertoire de s�ance
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
