<?php

class IparapheurComponent extends Object {

	var $requestPayloadString;
	var $responseMessage;
	var $responseMessageStr;
	var $wsto;
	var $clientcert;
	var $passphrase;
	var $userpwd;

	function IparapheurComponent() {
		$this->wsto       = configure::read('parapheur.wsto');
		$this->clientcert = configure::read('parapheur.clientcert');
		$this->passphrase = configure::read('parapheur.passphrase');
		$this->userpwd    = configure::read('parapheur.httpauth') .":". configure::read('parapheur.httppasswd');
	}

	function setWsto($wsto) {
		$this->wsto = $wsto;
	}

	function setLogin($login, $passwd, $clientcert, $passphrase) {
		$this->userpwd = $login . ":" . $passwd;
		$this->clientcert = $clientcert;
		$this->passphrase = $passphrase;
	}

	function SOAPMessage($request, $params) {

		$soap = "";

		if (! isset($params["attachments"])) {
			$soap = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
   <soapenv:Header/>
   <soapenv:Body>';
			$soap .= $this->requestPayloadString;
			$soap .= '</soapenv:Body></soapenv:Envelope>';
		} else {
			//
			// Le message contien des pi?ces jointes
			//
			$soap="MIME-Version: 1.0
Content-Type: Multipart/Related; boundary=MIMEBoundary5eca3d4a-35d8-1e01-32da-005056b32ce6; type=text/xml;
        start=\"<i-Parapheur-query@adullact.org>\"
Content-Description: This is the optional message description.";

			$soap="\n--MIMEBoundary5eca3d4a-35d8-1e01-32da-005056b32ce6
Content-Type: application/xop+xml;charset=UTF-8;type=\"text/xml\"
Content-Transfer-Encoding: 8bit
Content-ID: <i-Parapheur-query@adullact.org>

<?xml version='1.0' ?>
<SOAP-ENV:Envelope
xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
<SOAP-ENV:Body>";
			//
			// Ajout des pieces jointes
			//
			$soap .= $this->requestPayloadString;
			$soap .= "</SOAP-ENV:Body></SOAP-ENV:Envelope>\n\n--MIMEBoundary5eca3d4a-35d8-1e01-32da-005056b32ce6\n";

			$attachments = $params["attachments"];
			foreach ($attachments as $key => $content) {
				$soap .= "Content-Type: " . $content[1] .
                "\nContent-Transfer-Encoding: " . $content[2] .
                "\nContent-id: <" . $key . ">\n\n";
				$soap .= $content[0]. "\n--MIMEBoundary5eca3d4a-35d8-1e01-32da-005056b32ce6\n";
			}
		}

		return $soap;
	}

	function LancerRequeteCurl($attachments=null) {
		$errors = fopen("/tmp/parafError.log", "w");
		$ch = curl_init(configure::read('parapheur.wsto'));

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSLCERT, $this->clientcert);
		curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->passphrase);
		curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,  1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $this->wsto);

		if ($attachments != null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type:  Multipart/Related; boundary=MIMEBoundary5eca3d4a-35d8-1e01-32da-005056b32ce6; type=\"application/xop+xml\"; charset=utf-8; start=\"<i-Parapheur-query@adullact.org>\"",'SOAPAction: ""'));
			$params = array ("to"=>configure::read('parapheur.wsto'), "attachments"=>$attachments);
		} else {
			$params = array ("to"=>configure::read('parapheur.wsto'));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8",'SOAPAction: ""'));
		}
		$soap = $this->SOAPMessage($this->requestPayloadString, $params);


		curl_setopt($ch, CURLOPT_POSTFIELDS, $soap);

		$respons = curl_exec($ch);
		if ($respons == false) {
			echo curl_error($ch);
			return;
		}
		$lines = explode("\n",$respons);
		$ideb = 0;
		foreach ($lines as $line) {
			$ideb++;
			if ($line == "\r" || $line == "") break;
		}
		$xmlLines = array_slice($lines, $ideb, count($lines)-$ideb-1, true);
		$this->responseMessageStr = implode("\n", $xmlLines);

		curl_close($ch);
		unset($ch);
		fclose($errors);
	}

	function echoWebservice(){
		$this->requestPayloadString = '<ns:echoRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">coucou marie claude repondit l echo</ns:echoRequest>';
		$this->lancerRequeteCurl();
		return $this->responseMessageStr;

	}

	function getListeTypesWebservice(){
		$this->requestPayloadString = '<ns:GetListeTypesRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0"></ns:GetListeTypesRequest>';
		$this->lancerRequeteCurl();
		return array_merge($this->traiteXMLTypeTechnique(),$this->traiteXMLMessageRetour());
	}

	function getListeSousTypesWebservice($type){
		$this->requestPayloadString = '<ns:GetListeSousTypesRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$type.'</ns:GetListeSousTypesRequest>';
		$this->lancerRequeteCurl();
		if ($this->traiteXMLSousType()!= null)
		return array_merge($this->traiteXMLSousType(),$this->traiteXMLMessageRetour());
	}


	function getCircuit($typetech, $soustype){
		$this->requestPayloadString = '<ns:GetCircuitRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:TypeTechnique>'.$typetech.'</ns:TypeTechnique>
								         <ns:SousType>'.$soustype.'</ns:SousType>
								      </ns:GetCircuitRequest>';
		$this->lancerRequeteCurl();
		return $this->traiteXMLCircuit();
	}

	function getHistoDossierWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:GetHistoDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$nom_dossier.'</ns:GetHistoDossierRequest>';
		$this->lancerRequeteCurl();
		return array_merge($this->traiteXMLLogDossier(),$this->traiteXMLMessageRetour());

	}

	function rechercherDossierWebservice($typetech='', $soustype='', $status='', $nbdossiers='', $dossierid=''){
		$this->requestPayloadString = '<ns:RechercherDossiersRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
		                                   <ns:TypeTechnique>'.$typetech.'</ns:TypeTechnique>
				                   <ns:SousType>'.$soustype.'</ns:SousType>
				                   <ns:Status>'.$status.'</ns:Status>
				                   <ns:NombreDossiers>'.$nbdossiers.'</ns:NombreDossiers>
				                   <ns:DossierID>'.$dossierid.'</ns:DossierID>
				               </ns:RechercherDossiersRequest>';
		$this->lancerRequeteCurl();
		if ($this->traiteXMLLogDossier())
		return array_merge($this->traiteXMLLogDossier(),$this->traiteXMLMessageRetour());

	}

	function archiverDossierWebservice($nom_dossier, $typearchivage="ARCHIVER"){
		$this->requestPayloadString = '<ns:ArchiverDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:DossierID>'.$nom_dossier.'</ns:DossierID>
								         <ns:ArchivageAction>'.$typearchivage.'</ns:ArchivageAction>
								      </ns:ArchiverDossierRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return array_merge($this->traiteXMLArchiverDossier(), $this->traiteXMLMessageRetour());
	}

	function effacerDossierRejeteWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:EffacerDossierRejeteRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:EffacerDossierRejeteRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return $this->traiteXMLMessageRetour();
	}

	function exercerDroitRemordWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:ExercerDroitRemordDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$nom_dossier.'</ns:ExercerDroitRemordDossierRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return $this->traiteXMLMessageRetour();
	}

	function creerDossierWebservice($typetech, $soustype, $emailemetteur, $dossierid, $annotpub='', $annotpriv='', $visibilite, $datelim='', $pdf, $docsannexes=array()){
		$attachments = array('fichierPDF'=>array($pdf, "application/pdf", "binary", "document.pdf"));
		$this->requestPayloadString = '<ns:CreerDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0" xmlns:xm="http://www.w3.org/2005/05/xmlmime">
								         <ns:TypeTechnique>'.$typetech.'</ns:TypeTechnique>
								         <ns:SousType>'.$soustype.'</ns:SousType>
								         <ns:EmailEmetteur>'.$emailemetteur.'</ns:EmailEmetteur>
								         <ns:DossierID>'.$dossierid.'</ns:DossierID>
								         <ns:DocumentPrincipal xm:contentType="application/pdf">
								         	<xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:fichierPDF"></xop:Include>
								         </ns:DocumentPrincipal>';
		$this->requestPayloadString .= '<ns:DocumentsAnnexes>';
			
		for($i=0; $i<count($docsannexes); $i++){
			$this->requestPayloadString .= '<ns:DocAnnexe>
		               <ns:nom>'.$docsannexes[$i][3].'</ns:nom>
		               <ns:fichier xm:contentType="'.$docsannexes[$i][1].'">
		               <xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:annexe_'.$i.'"></xop:Include>
		               </ns:fichier>
		               <ns:mimetype>'.$docsannexes[$i][1].'</ns:mimetype>
		               <ns:encoding>'.$docsannexes[$i][2].'</ns:encoding>
		            </ns:DocAnnexe>';
			$attachments = array_merge($attachments, array("annexe_".$i => $docsannexes[$i]));
		}
		 
		$this->requestPayloadString .= '</ns:DocumentsAnnexes>';
		$this->requestPayloadString .= '<ns:XPathPourSignature></ns:XPathPourSignature>
								         <ns:AnnotationPublique>'.$annotpub.'</ns:AnnotationPublique>
								         <ns:AnnotationPrivee>'.$annotpriv.'</ns:AnnotationPrivee>
								         <ns:Visibilite>'.$visibilite.'</ns:Visibilite>
								         <ns:DateLimite>'.$datelim.'</ns:DateLimite>
									   </ns:CreerDossierRequest>';
		$this->LancerRequeteCurl($attachments);
		//return $this->responseMessageStr;
		return $this->traiteXMLMessageRetour();
	}

	function getDossierWebservice($nom_dossier) {

		$this->requestPayloadString = '<ns:GetDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:GetDossierRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return array_merge($this->traiteXMLGetDossier(),$this->traiteXMLMessageRetour());

		/*$dom = new DomDocument();
		 $dom->loadXML($responseMessageStr);
		 $fichiersPES = $dom->documentElement->getElementsByTagName('FichierPES');
		 $fichierPES = $fichiersPES->item(0);
		 $str = base64_decode($fichierPES->nodeValue);
		 if(!file_put_contents("dossier/recup/" . $nom_dossier . "_recup.xml", $str)){
		 }
		 else echo "le fichier a bien ?t? r?cup?r? !";*/
	}


	function envoyerDossierTdTWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:EnvoyerDossierTdTRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:EnvoyerDossierTdTRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return $this->traiteXMLMessageRetour();
	}

	function getStatutTdTWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:GetStatutTdTRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$nom_dossier.'</ns:GetStatutTdTRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return array_merge($this->traiteXMLLogDossier(),$this->traiteXMLMessageRetour());
	}

	function forcerEtapeWebservice($nom_dossier, $codetransit, $annotpub='', $annotpriv=''){
		$this->requestPayloadString = '<ns:ForcerEtapeRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:DossierID>'.$nom_dossier.'</ns:DossierID>
								         <ns:CodeTransition>'.$codetransit.'</ns:CodeTransition>
								         <ns:AnnotationPublique>'.$annotpub.'</ns:AnnotationPublique>
								         <ns:AnnotationPrivee>'.$annotpriv.'</ns:AnnotationPrivee>
								      </ns:ForcerEtapeRequest>';
		$this->lancerRequeteCurl();
		//return $this->responseMessageStr;
		return array_merge($this->traiteXMLLogDossier(),$this->traiteXMLMessageRetour());
	}

	function traiteXMLMessageRetour(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessageStr);
		$codesretour = $dom->documentElement->getElementsByTagName('codeRetour');
		$coderetour = $codesretour->item(0)->nodeValue;
		$messages = $dom->documentElement->getElementsByTagName('message');
		$message = $messages->item(0)->nodeValue;
		$severites = $dom->documentElement->getElementsByTagName('severite');
		$severite = $severites->item(0)->nodeValue;
		$response['messageretour'] = array("coderetour"=>$coderetour,"message"=>$message, "severite"=>$severite);
		return $response;
	}

	function traiteXMLLogDossier(){
		$response=array();
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessageStr);
		$dataset = $dom->getElementsByTagName( "LogDossier" );
		foreach( $dataset as $row )
		{
			$timestamps = $row->getElementsByTagName( "timestamp" );
			$timestamp = $timestamps->item(0)->nodeValue;
				
			$noms = $row->getElementsByTagName( "nom" );
			$nom = $noms->item(0)->nodeValue;
				
			$status = $row->getElementsByTagName( "status" );
			$statu = $status->item(0)->nodeValue;

			$annotations = $row->getElementsByTagName( "annotation" );
			$annotation = $annotations->item(0)->nodeValue;

			$accessibles = $row->getElementsByTagName( "accessible" );
			$accessible = $accessibles->length>0?$accessibles->item(0)->nodeValue:"";
				
			$response['logdossier'][] = array("timestamp"=>$timestamp, "nom"=>$nom, "status"=>$statu, "annotation"=>$annotation, "accessible"=>$accessible);
		}
		return $response;
	}

	function traiteXMLTypeTechnique(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessageStr);
		$dataset = $dom->getElementsByTagName( "TypeTechnique" );
		foreach( $dataset as $row )
		{
			$response['typetechnique'][] = $row->nodeValue;
		}
		return $response;
	}

	function traiteXMLSousType(){
		$dom = new DomDocument();
		if ($this->responseMessageStr!=null){
			$response = array();
			$dom->loadXML($this->responseMessageStr);
			$dataset = $dom->getElementsByTagName("SousType");
			foreach( $dataset as $row ) {
				$response['soustype'][] = $row->nodeValue;
			}
		}else{
			$response='aucun sous-type';
		}
		return $response;
	}

	function traiteXMLCircuit(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessageStr);
		$dataset = $dom->getElementsByTagName( "EtapeCircuit" );
		foreach( $dataset as $row )
		{
			$parapheurs = $row->getElementsByTagName( "Parapheur" );
			$parapheur = $parapheurs->item(0)->nodeValue;
				
			$prenoms = $row->getElementsByTagName( "Prenom" );
			$prenom = $prenoms->item(0)->nodeValue;
				
			$noms = $row->getElementsByTagName( "Nom" );
			$nom = $noms->item(0)->nodeValue;

			$roles = $row->getElementsByTagName( "Role" );
			$role = $roles->item(0)->nodeValue;
				
			$response['etapecircuit'][] = array("Parapheur"=>$parapheur, "Prenom"=>$prenom, "Nom"=>$nom, "Role"=>$role);
		}
		return $response;
	}

	function traiteXMLArchiverDossier(){
		$response = array();
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessageStr);
		$url = $dom->documentElement->getElementsByTagName('URL');
		if($url->length>0){
			$response['URL'] = $url->item(0)->nodeValue;
		}
		return  $response;
	}

	function traiteXMLGetDossier(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessageStr);
		$signdocprinc='';
		$datelim='';

		$typestech = $dom->documentElement->getElementsByTagName('TypeTechnique');
		$typetech = $typestech->item(0)->nodeValue;
		$soustypes = $dom->documentElement->getElementsByTagName('SousType');
		$soustype = $soustypes->item(0)->nodeValue;
		$emailsemetteur = $dom->documentElement->getElementsByTagName('EmailEmetteur');
		$emailemetteur = $emailsemetteur->item(0)->nodeValue;
		$dossiersid = $dom->documentElement->getElementsByTagName('DossierID');
		$dossierid = $dossiersid->item(0)->nodeValue;
		$annotspub = $dom->documentElement->getElementsByTagName('AnnotationPublique');
		$annotpub = $annotspub->item(0)->nodeValue;
		$annotspriv = $dom->documentElement->getElementsByTagName('AnnotationPrivee');
		$annotpriv = $annotspriv->item(0)->nodeValue;
		$visus = $dom->documentElement->getElementsByTagName('Visibilite');
		$visu = $visus->item(0)->nodeValue;
		$dateslim = $dom->documentElement->getElementsByTagName('DateLimite');
		if($dateslim->length>0){
			$datelim = $dateslim->item(0)->nodeValue;
		}
		$docsprinc = $dom->documentElement->getElementsByTagName('DocPrincipal');
		$docprinc = $docsprinc->item(0)->nodeValue;
		$nomsdocprinc = $dom->documentElement->getElementsByTagName('NomDocPrincipal');
		$nomdocprinc = $nomsdocprinc->item(0)->nodeValue;
		$signsdocprinc = $dom->documentElement->getElementsByTagName('SignatureDocPrincipal');
		if($signsdocprinc->length>0){
			$signdocprinc = $signsdocprinc->item(0)->nodeValue;
		}
	  
		$response['getdossier']=array($typetech, $soustype, $emailemetteur, $dossierid,
		$annotpub, $annotpriv, $visu, $datelim,
		$docprinc, $nomdocprinc, $signdocprinc);
			
		return $response;
	}

}

?>
