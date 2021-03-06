<?php

class IparapheurComponent extends Component {

    public $responseMessage;
    public $responseMessageStr;
    public $wsto;
    public $wsdl;
    public $clientcert;
    public $passphrase;
    public $userpwd;
    public $boundary;

    function IparapheurComponent() {
        $this->setWsto(
            Configure::read('IPARAPHEUR_HOST'),
            Configure::read('IPARAPHEUR_WSDL')
        );
        $this->setLogin(
            Configure::read('IPARAPHEUR_LOGIN'),
            Configure::read('IPARAPHEUR_PWD'),
            Configure::read('IPARAPHEUR_CLIENTCERT'),
            Configure::read('IPARAPHEUR_CERTPWD')
        );
        $this->boundary = "5eca3d4a-35d8-1e01-32da-005056b32ce6";
    }

    function setWsto($wsto, $wsdl) {
        $this->wsdl = $wsdl;
        if (stripos($wsto, $wsdl) === false) {
            if (substr($wsto, strlen($wsto) - 1, strlen($wsto)) == '/')
                $this->wsto = $wsto . $wsdl;
            else
                $this->wsto = $wsto . '/' . $wsdl;
        } else
            $this->wsto = $wsto;
    }

    function setLogin($login, $passwd, $clientcert, $passphrase) {
        $this->userpwd = $login . ":" . $passwd;
        $this->clientcert = $clientcert;
        $this->passphrase = $passphrase;
    }

    function SOAPMessage($requestPayloadString, $params) {
        if (!isset($params["attachments"])) {
            $soap = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
   <soapenv:Header/>
   <soapenv:Body>';
            $soap .= $requestPayloadString;
            $soap .= '</soapenv:Body></soapenv:Envelope>';
        } else {
            //
            // Le message contient des pièces jointes
            //
            $soap = "\n--MIMEBoundary" . $this->boundary . "
Content-Type: application/xop+xml;charset=UTF-8;type=\"text/xml\"
Content-Transfer-Encoding: 8bit
Content-ID: <i-Parapheur-query@adullact.org>

<?xml version='1.0' ?>
<SOAP-ENV:Envelope
xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
<SOAP-ENV:Body>";
            // Ajout des pieces jointes
            $soap .= $requestPayloadString;
            $soap .= "</SOAP-ENV:Body></SOAP-ENV:Envelope>\n\n--MIMEBoundary" . $this->boundary . "\n";

            $attachments = $params["attachments"];
            foreach ($attachments as $key => $content) {
                $soap .= "Content-Type: " . $content[1] .
                    "\nContent-Transfer-Encoding: " . $content[2] .
                    "\nContent-id: <" . $key . ">\n\n";
                $soap .= $content[0] . "\n--MIMEBoundary" . $this->boundary . "\n";
            }
        }
        return $soap;
    }

    function LancerRequeteCurl($request, $attachments = null) {
        $ch = curl_init($this->wsto);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->clientcert);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->passphrase);
        curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->wsto);

        if ($attachments != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type:  Multipart/Related; boundary=MIMEBoundary" . $this->boundary . "; type=\"application/xop+xml\"; charset=utf-8; start=\"<i-Parapheur-query@adullact.org>\"", 'SOAPAction: ""'));
            $params = array("to" => $this->wsto, "attachments" => $attachments);
        } else {
            $params = array("to" => $this->wsto);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8", 'SOAPAction: ""'));
        }
        $soap = $this->SOAPMessage($request, $params);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $soap);

        $respons = curl_exec($ch);
        if ($respons === false) {
            $this->log(curl_error($ch), 'parapheur');
            return false;
        }

        $lines = explode("\n", $respons);
        $ideb = 0;
        foreach ($lines as $line) {
            $ideb++;
            if ($line == "\r" || $line == "")
                break;
        }
        $xmlLines = array_slice($lines, $ideb, count($lines) - $ideb - 1, true);
        $this->responseMessageStr = implode("\n", $xmlLines);

        curl_close($ch);

        return true;
    }

    function echoWebservice() {
        $request = '<ns:echoRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">coucou marie claude repondit l echo</ns:echoRequest>';
        $this->lancerRequeteCurl($request);
        return $this->responseMessageStr;
    }

    function getListeTypesWebservice() {
        $request = '<ns:GetListeTypesRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0"></ns:GetListeTypesRequest>';
        $this->lancerRequeteCurl($request);
        return array_merge($this->traiteXMLTypeTechnique(), $this->traiteXMLMessageRetour());
    }

    function getListeSousTypesWebservice($type) {
        $request = '<ns:GetListeSousTypesRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $type . '</ns:GetListeSousTypesRequest>';
        $this->lancerRequeteCurl($request);
        $soustypes = $this->traiteXMLSousType();
        if (!empty($soustypes))
            return array_merge($soustypes, $this->traiteXMLMessageRetour());
        else
            return $this->traiteXMLMessageRetour();
    }

    function getCircuit($typetech, $soustype) {
        $request = '<ns:GetCircuitRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:TypeTechnique>' . $typetech . '</ns:TypeTechnique>
								         <ns:SousType>' . $soustype . '</ns:SousType>
								      </ns:GetCircuitRequest>';
        $this->lancerRequeteCurl($request);
        return $this->traiteXMLCircuit();
    }

    function getHistoDossierWebservice($nom_dossier) {
        $request = '<ns:GetHistoDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:GetHistoDossierRequest>';
        $this->lancerRequeteCurl($request);
        return array_merge($this->traiteXMLLogDossier(), $this->traiteXMLMessageRetour());
    }

    function rechercherDossierWebservice($typetech = '', $soustype = '', $status = '', $nbdossiers = '', $dossierid = '') {
        $request = '<ns:RechercherDossiersRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
		                                   <ns:TypeTechnique>' . $typetech . '</ns:TypeTechnique>
				                   <ns:SousType>' . $soustype . '</ns:SousType>
				                   <ns:Status>' . $status . '</ns:Status>
				                   <ns:NombreDossiers>' . $nbdossiers . '</ns:NombreDossiers>
				                   <ns:DossierID>' . $dossierid . '</ns:DossierID>
				               </ns:RechercherDossiersRequest>';
        $this->lancerRequeteCurl($request);
        if ($this->traiteXMLLogDossier())
            return array_merge($this->traiteXMLLogDossier(), $this->traiteXMLMessageRetour());
    }

    function archiverDossierWebservice($nom_dossier, $typearchivage = "ARCHIVER") {
        $request = '<ns:ArchiverDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:DossierID>' . $nom_dossier . '</ns:DossierID>
								         <ns:ArchivageAction>' . $typearchivage . '</ns:ArchivageAction>
								      </ns:ArchiverDossierRequest>';
        $this->lancerRequeteCurl($request);
        return array_merge($this->traiteXMLArchiverDossier(), $this->traiteXMLMessageRetour());
    }

    function effacerDossierRejeteWebservice($nom_dossier) {
        $request = '<ns:ForcerEtapeRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:ForcerEtapeRequest>';
        $this->lancerRequeteCurl($request);
        return $this->traiteXMLMessageRetour();
    }

    function exercerDroitRemordWebservice($nom_dossier) {
        $request = '<ns:ExercerDroitRemordDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:ExercerDroitRemordDossierRequest>';
        $this->lancerRequeteCurl($request);
        return $this->traiteXMLMessageRetour();
    }

    function creerDossierWebservice($titre, $typetech, $soustype, $visibilite, $pdf, $docsannexes = array(), $datelim = '', $annotpub = '', $annotpriv = '', $metas = array()) {
        $attachments = array('fichierPDF' => array($pdf, 'application/pdf', 'binary', 'document.pdf'));
        $request = '<ns:CreerDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0" xmlns:xm="http://www.w3.org/2005/05/xmlmime">
								         <ns:TypeTechnique>' . $typetech . '</ns:TypeTechnique>
								         <ns:SousType>' . $soustype . '</ns:SousType>
								         <ns:DossierID></ns:DossierID>
								         <ns:DossierTitre>' . AppTools::xml_entity_encode($this->reformatNameForIparapheur($titre)) . '</ns:DossierTitre>
								         <ns:DocumentPrincipal xm:contentType="application/pdf">
								         	<xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:fichierPDF"></xop:Include>
								         </ns:DocumentPrincipal>';
        if (isset($metas) && !empty($metas)) {
            $request .= '<ns:MetaData>';
            foreach ($metas as $nom => $valeur) {
                $request .= "<ns:MetaDonnee><ns:nom>$nom</ns:nom><ns:valeur>$valeur</ns:valeur></ns:MetaDonnee>";
            }
            $request .= '</ns:MetaData>';
        }

        $request .= '<ns:DocumentsAnnexes>';
        for ($i = 0; $i < count($docsannexes); $i++) {
            $encoding = !empty($docsannexes[$i]['encoding']) ? $docsannexes[$i]['encoding'] : 'UTF-8';
            $request .= '<ns:DocAnnexe>
		               <ns:nom>' . AppTools::xml_entity_encode($docsannexes[$i]['filename']) . '</ns:nom>
		               <ns:fichier xm:contentType="' . $docsannexes[$i]['mimetype'] . '">
		               <xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:annexe_' . $i . '"></xop:Include>
		               </ns:fichier>
		               <ns:mimetype>' . $docsannexes[$i]['mimetype'] . '</ns:mimetype>
		               <ns:encoding>' . $encoding . '</ns:encoding>
		            </ns:DocAnnexe>';
            $attachments = array_merge($attachments, array('annexe_' . $i => array($docsannexes[$i]['content'], $docsannexes[$i]['mimetype'], 'binary', $docsannexes[$i]['filename'])));
        }
        $request .= '</ns:DocumentsAnnexes>';
        $request .= '<ns:XPathPourSignature></ns:XPathPourSignature>
                    <ns:AnnotationPublique>' . $annotpub . '</ns:AnnotationPublique>
                    <ns:AnnotationPrivee>' . $annotpriv . '</ns:AnnotationPrivee>
                    <ns:Visibilite>' . $visibilite . '</ns:Visibilite>
                    <ns:DateLimite>' . $datelim . '</ns:DateLimite>
                    </ns:CreerDossierRequest>';
        $this->LancerRequeteCurl($request, $attachments);


        return $this->traiteXMLMessageRetour();
    }

    function getDossierWebservice($nom_dossier) {

        $request = '<ns:GetDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:GetDossierRequest>';
        $this->lancerRequeteCurl($request);
        return array_merge($this->traiteXMLGetDossier(), $this->traiteXMLMessageRetour());
    }

    function envoyerDossierTdTWebservice($nom_dossier) {
        $request = '<ns:EnvoyerDossierTdTRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:EnvoyerDossierTdTRequest>';
        $this->lancerRequeteCurl($request);
        return $this->traiteXMLMessageRetour();
    }

    function getStatutTdTWebservice($nom_dossier) {
        $request = '<ns:GetStatutTdTRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:GetStatutTdTRequest>';
        $this->lancerRequeteCurl($request);
        return array_merge($this->traiteXMLLogDossier(), $this->traiteXMLMessageRetour());
    }

    function forcerEtapeWebservice($nom_dossier, $codetransit, $annotpub = '', $annotpriv = '') {
        $request = '<ns:ForcerEtapeRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:DossierID>' . $nom_dossier . '</ns:DossierID>
								         <ns:CodeTransition>' . $codetransit . '</ns:CodeTransition>
								         <ns:AnnotationPublique>' . $annotpub . '</ns:AnnotationPublique>
								         <ns:AnnotationPrivee>' . $annotpriv . '</ns:AnnotationPrivee>
								      </ns:ForcerEtapeRequest>';
        $this->lancerRequeteCurl($request);
        return array_merge($this->traiteXMLLogDossier(), $this->traiteXMLMessageRetour());
    }

    function traiteXMLMessageRetour() {
        $xml = simplexml_load_string($this->responseMessageStr);
        if ($xml !== false) {
            $result = $xml->xpath('S:Body/S:Fault');
            if (!empty($result)) {
                $response['messageretour'] = array('coderetour' => -1, 'message' => 'Erreur soap : Veuillez contacter votre administrateur', 'severite' => 'grave');
                $this->log($result[0]->faultstring, 'parapheur');
                return $response;
            }
        }

        $dom = new DomDocument();
        try {
            if (empty($this->responseMessageStr)) throw new Exception("Aucune réponse du parapheur");
            $dom->loadXML($this->responseMessageStr);
            $codesretour = $dom->documentElement->getElementsByTagName('codeRetour');
            $coderetour = @$codesretour->item(0)->nodeValue;
            $messages = $dom->documentElement->getElementsByTagName('message');
            $message = @$messages->item(0)->nodeValue;
            $severites = $dom->documentElement->getElementsByTagName('severite');
            $severite = @$severites->item(0)->nodeValue;
            $dossierIDs = $dom->documentElement->getElementsByTagName('DossierID');
            $dossierID = @$dossierIDs->item(0)->nodeValue;
            $response['messageretour'] = array("coderetour" => $coderetour, "message" => $message, "severite" => $severite);
            $response['dossierID'] = $dossierID;
        } catch (Exception $e) {
            $response['messageretour'] = array("coderetour" => -1, "message" => "Erreur de connexion au parapheur: " . $e->getMessage(), "severite" => "grave");
        }
        return $response;
    }

    function traiteXMLLogDossier() {
        $response = array();
        $dom = new DomDocument();
        $dom->loadXML($this->responseMessageStr);
        $dataset = $dom->getElementsByTagName("LogDossier");
        foreach ($dataset as $row) {
            $timestamps = $row->getElementsByTagName("timestamp");
            $timestamp = $timestamps->item(0)->nodeValue;

            $noms = $row->getElementsByTagName("nom");
            $nom = $noms->item(0)->nodeValue;

            $status = $row->getElementsByTagName("status");
            $statu = $status->item(0)->nodeValue;

            $annotations = $row->getElementsByTagName("annotation");
            $annotation = $annotations->item(0)->nodeValue;

            $accessibles = $row->getElementsByTagName("accessible");
            $accessible = $accessibles->length > 0 ? $accessibles->item(0)->nodeValue : "";

            $response['logdossier'][] = array("timestamp" => $timestamp, "nom" => $nom, "status" => $statu, "annotation" => $annotation, "accessible" => $accessible);
        }
        return $response;
    }

    function traiteXMLTypeTechnique() {
        $dom = new DomDocument();
        $dom->loadXML($this->responseMessageStr);
        $dataset = $dom->getElementsByTagName("TypeTechnique");
        $response = array();
        foreach ($dataset as $row) {
            $response['typetechnique'][] = $row->nodeValue;
        }
        return $response;
    }

    function traiteXMLSousType() {
        $dom = new DomDocument();
        $response = array();
        if ($this->responseMessageStr != null) {
            $dom->loadXML($this->responseMessageStr);
            $dataset = $dom->getElementsByTagName("SousType");
            foreach ($dataset as $row) {
                $response['soustype'][] = $row->nodeValue;
            }
        }
        return $response;
    }

    function traiteXMLCircuit() {
        $dom = new DomDocument();
        $dom->loadXML($this->responseMessageStr);
        $dataset = $dom->getElementsByTagName("EtapeCircuit");
        $response = array();
        foreach ($dataset as $row) {
            $parapheurs = $row->getElementsByTagName("Parapheur");
            $parapheur = $parapheurs->item(0)->nodeValue;

            $prenoms = $row->getElementsByTagName("Prenom");
            $prenom = $prenoms->item(0)->nodeValue;

            $noms = $row->getElementsByTagName("Nom");
            $nom = $noms->item(0)->nodeValue;

            $roles = $row->getElementsByTagName("Role");
            $role = $roles->item(0)->nodeValue;

            $response['etapecircuit'][] = array("Parapheur" => $parapheur, "Prenom" => $prenom, "Nom" => $nom, "Role" => $role);
        }
        return $response;
    }

    function traiteXMLArchiverDossier() {
        $response = array();
        $dom = new DomDocument();
        $dom->loadXML($this->responseMessageStr);
        $url = $dom->documentElement->getElementsByTagName('URL');
        if ($url->length > 0) {
            $response['URL'] = $url->item(0)->nodeValue;
        }
        return $response;
    }

    function traiteXMLGetDossier() {
//        FIXME : récupérer le document avec bordereau de signature
        $dom = new DomDocument();
//        $this->log($this->responseMessageStr,'debug');
        $dom->loadXML($this->responseMessageStr);
        $signdocprinc = '';
        $datelim = '';

        $typestech = $dom->documentElement->getElementsByTagName('TypeTechnique');
        $typetech = $typestech->item(0)->nodeValue;
        $soustypes = $dom->documentElement->getElementsByTagName('SousType');
        $soustype = $soustypes->item(0)->nodeValue;
        // FIXME Ce noeud n'existe pas 'EmailEmetteur'
        $dossiersid = $dom->documentElement->getElementsByTagName('DossierID');
        $dossierid = $dossiersid->item(0)->nodeValue;
        $annotspub = $dom->documentElement->getElementsByTagName('AnnotationPublique');
        $annotpub = $annotspub->item(0)->nodeValue;
        $annotspriv = $dom->documentElement->getElementsByTagName('AnnotationPrivee');
        $annotpriv = $annotspriv->item(0)->nodeValue;
        $visus = $dom->documentElement->getElementsByTagName('Visibilite');
        $visu = $visus->item(0)->nodeValue;
        $dateslim = $dom->documentElement->getElementsByTagName('DateLimite');
        if ($dateslim->length > 0) {
            $datelim = $dateslim->item(0)->nodeValue;
        }
        $docsprinc = $dom->documentElement->getElementsByTagName('DocPrincipal');
        $docprinc = $docsprinc->item(0)->nodeValue;
        $nomsdocprinc = $dom->documentElement->getElementsByTagName('NomDocPrincipal');
        $nomdocprinc = $nomsdocprinc->item(0)->nodeValue;
        $signsdocprinc = $dom->documentElement->getElementsByTagName('SignatureDocPrincipal');
        if ($signsdocprinc->length > 0) {
            $signdocprinc = $signsdocprinc->item(0)->nodeValue;
        }
        
        $annexesNode = $dom->documentElement->getElementsByTagName('DocAnnexe');
        if (!empty($annexesNode)) {
            foreach ($annexesNode as $Node) {
                if ($Node->getElementsByTagName("nom")->item(0)->nodeValue == '"iParapheur_impression_dossier.pdf"') {
                    $bordereau = $Node->getElementsByTagName("fichier")->item(0)->nodeValue;
                }
            }
        }

        $response['getdossier'] = array(
            'type' => $typetech,
            'soustype' => $soustype,
            'dossierid' => $dossierid,
            'annotpub' => $annotpub,
            'annotpriv' => $annotpriv,
            'visu' => $visu,
            'datelim' => $datelim,
            'docprinc' => $docprinc,
            'nomdocprinc' => $nomdocprinc,
            'signature' => $signdocprinc,
            'bordereau' => !empty($bordereau)?$bordereau:''
        );

        return $response;
    }

    /* Modification pour le nom de dossier
     *   
     */
    function reformatNameForIparapheur($objetDossier) {
        $search = array( '/', ':', '"', '+', "\n", "\t", "\r");
        $replace = array("-", "-", "'", "PLUS", '', '', '');
        $objetDossier = str_replace($search, $replace, $objetDossier);
        if (strlen($objetDossier) > 190) {
            $objetDossier = substr($objetDossier, 0, 185);
        }
        if ($objetDossier[strlen($objetDossier) - 1] == '.')
            $objetDossier[strlen($objetDossier) - 1] = null;
        return (trim($objetDossier));
    }
}