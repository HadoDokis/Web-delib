<?php
class ParafwebserviceComponent extends Object {
	
	var $requestPayloadString;
	var $responseMessage;
	
	
	/*function startup(&$controller)
	{
		// This method takes a reference to the controller which is loading it.
		// Perform controller initialization here.
	}*/
	
	function test(){
		return "test";
	}	
	
	function lancerRequete($attachment=false){
		try {
		    if(!$attachment){
				$requestMessage = new WSMessage($this->requestPayloadString, 
			        array(
			        		"to"=>WSTO
			        	));
		    }
		    else {
		    	$requestMessage = new WSMessage($this->requestPayloadString, 
										        array(
										        	"to" => WSTO,
										        	"attachments" => $attachment
										       	));
		    }
		    
		    $client = new WSClient(array (     								
		     								"useSOAP"   => VERSSOAP,
			              					"useMTOM"   => USEMTOM,
			      							"action"    => WSACTION,
			      							
		      							   	"CACert"    => CACERT,         
					                        "clientCert"=> CLIENTCERT,     
					                        "passphrase"=> PASSPHRASE,     
		     								
		     								"httpAuthUsername" => HTTPAUTH,    
					                       	"httpAuthPassword" => HTTPPASSWD,
					                       	"httpAuthType"     => HTTPTYPE,     
		     
			      							));
		    
		    $this->responseMessage = $client->request($requestMessage);		    
		    //echo $this->responseMessage->str;
	
		} catch (Exception $e) {
		    if ($e instanceof WSFault) {
		        printf("Soap Fault: %s\n", $e->Reason);
		    } else {
		        printf("Message = %s\n",$e->getMessage());
		    }
		}		
	} 
	
	function echoWebservice(){
		$this->requestPayloadString = '<ns:echoRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">test</ns:echoRequest>';
		$this->lancerRequete();
		return $this->responseMessage->str;		
	}
	
	function getListeTypesWebservice(){
		$this->requestPayloadString = '<ns:GetListeTypesRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0"></ns:GetListeTypesRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return $this->traiteXMLTypeTechnique();	
	} 
	
	function getListeSousTypesWebservice($type){
		$this->requestPayloadString = '<ns:GetListeSousTypesRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$type.'</ns:GetListeSousTypesRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return $this->traiteXMLSousType();	
	}
	
	function getCircuit($typetech, $soustype){
		$this->requestPayloadString = '<ns:GetCircuitRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:TypeTechnique>'.$typetech.'</ns:TypeTechnique>
								         <ns:SousType>'.$soustype.'</ns:SousType>
								      </ns:GetCircuitRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return $this->traiteXMLCircuit();	
	} 
	
	function getHistoDossierWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:GetHistoDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$nom_dossier.'</ns:GetHistoDossierRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
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
		$this->lancerRequete();
		//return $this->responseMessage->str;	
		return $this->traiteXMLLogDossier();
	}
	
	function archiverDossierWebservice($nom_dossier, $typearchivage="ARCHIVER"){
		$this->requestPayloadString = '<ns:ArchiverDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:DossierID>'.$nom_dossier.'</ns:DossierID>
								         <ns:ArchivageAction>'.$typearchivage.'</ns:ArchivageAction>
								      </ns:ArchiverDossierRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return array_merge($this->traiteXMLArchiverDossier(), $this->traiteXMLMessageRetour());			
	}
	
	function effacerDossierRejeteWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:EffacerDossierRejeteRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:EffacerDossierRejeteRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return $this->traiteXMLMessageRetour();								
	}
	
	function exercerDroitRemordWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:ExercerDroitRemordDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$nom_dossier.'</ns:ExercerDroitRemordDossierRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return $this->traiteXMLMessageRetour();		
	}
	
	function creerDossierWebservice($typetech, $soustype, $emailemetteur, $dossierid, $annotpub='', $annotpriv='', $visibilite, $datelim='', $pdf, $docsannexes=array()){
		$attachments = array('fichierPDF'=>$pdf);
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
		         $attachments = array_merge($attachments, array("annexe_".$i => $docsannexes[$i][0]));
		}      
     
        $this->requestPayloadString .= '</ns:DocumentsAnnexes>';
		$this->requestPayloadString .= '<ns:XPathPourSignature></ns:XPathPourSignature>
								         <ns:AnnotationPublique>'.$annotpub.'</ns:AnnotationPublique>
								         <ns:AnnotationPrivee>'.$annotpriv.'</ns:AnnotationPrivee>
								         <ns:Visibilite>'.$visibilite.'</ns:Visibilite>
								         <ns:DateLimite>'.$datelim.'</ns:DateLimite>
									   </ns:CreerDossierRequest>';
		$this->lancerRequete($attachments);
		//return $this->responseMessage->str;	
		return $this->traiteXMLMessageRetour();			
	}
	
	function getDossierWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:GetDossierRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:GetDossierRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;			
		return array_merge($this->traiteXMLGetDossier('ACTE'),$this->traiteXMLMessageRetour());

		/*$dom = new DomDocument();
		$dom->loadXML($responseMessage->str);
		$fichiersPES = $dom->documentElement->getElementsByTagName('FichierPES');
	    $fichierPES = $fichiersPES->item(0);
	    $str = base64_decode($fichierPES->nodeValue);
		if(!file_put_contents("dossier/recup/" . $nom_dossier . "_recup.xml", $str)){
		}
		else echo "le fichier a bien été récupéré !";*/
	}
	
	
	function envoyerDossierTdTWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:EnvoyerDossierTdTRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">' . $nom_dossier . '</ns:EnvoyerDossierTdTRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return $this->traiteXMLMessageRetour();
	}
	
	function getStatutTdTWebservice($nom_dossier){
		$this->requestPayloadString = '<ns:GetStatutTdTRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">'.$nom_dossier.'</ns:GetStatutTdTRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return array_merge($this->traiteXMLLogDossier(),$this->traiteXMLMessageRetour());
	}
	
	function forcerEtapeWebservice($nom_dossier, $codetransit, $annotpub='', $annotpriv=''){		
        $this->requestPayloadString = '<ns:ForcerEtapeRequest xmlns:ns="http://www.adullact.org/spring-ws/iparapheur/1.0">
								         <ns:DossierID>'.$nom_dossier.'</ns:DossierID>
								         <ns:CodeTransition>'.$codetransit.'</ns:CodeTransition>
								         <ns:AnnotationPublique>'.$annotpub.'</ns:AnnotationPublique>
								         <ns:AnnotationPrivee>'.$annotpriv.'</ns:AnnotationPrivee>
								      </ns:ForcerEtapeRequest>';
		$this->lancerRequete();
		//return $this->responseMessage->str;
		return array_merge($this->traiteXMLLogDossier(),$this->traiteXMLMessageRetour());
	}
	
	function traiteXMLMessageRetour(){		
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessage->str);
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
		$dom->loadXML($this->responseMessage->str);		
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
		$dom->loadXML($this->responseMessage->str);		
		$dataset = $dom->getElementsByTagName( "TypeTechnique" );
		foreach( $dataset as $row )
		{
			$response['typetechnique'][] = $row->nodeValue;
		}
		return $response;
	}
	
	function traiteXMLSousType(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessage->str);		
		$dataset = $dom->getElementsByTagName( "SousType" );
		foreach( $dataset as $row )
		{
			$response['soustype'][] = $row->nodeValue;
		}
		return $response;
	}
	
	function traiteXMLCircuit(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessage->str);		
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
		$dom->loadXML($this->responseMessage->str);
		$url = $dom->documentElement->getElementsByTagName('URL');
		if($url->length>0){
			$response['URL'] = $url->item(0)->nodeValue;
		}
		return  $response;
	}
	
	function traiteXMLGetDossier(){
		$dom = new DomDocument();
		$dom->loadXML($this->responseMessage->str);
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
