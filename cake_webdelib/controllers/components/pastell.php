<?php
 
class PastellComponent extends Object {

    function _initCurl ($api, $data=array()) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
        curl_setopt($curl, CURLOPT_USERPWD, Configure::read("PASTELL_LOGIN").":".Configure::read("PASTELL_PWD"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $api = Configure::read("PASTELL_HOST").'/web/'.$api;
        $this->log($api);
	curl_setopt($curl, CURLOPT_URL, $api);
	if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
        }
        return $curl;  
    }

    function getInfosField($id_e, $id_d, $type) {
        $nomenclature = array();
        $curl = $this->_initCurl("api/external-data.php?id_e=$id_e&id_d=$id_d&field=$type");
        $result = curl_exec($curl);
        curl_close($curl);
	return  json_decode($result);
    }

    function listEntities() {
	$entities = array();
        $collectivites = array();
        $curl = $this->_initCurl('api/list-entite.php'); 
        $result = curl_exec($curl);
        curl_close($curl);
	$entities = json_decode($result);
        
	foreach ($entities as $entity) {
            $entity = (array) $entity;
	    $collectivites[$entity['id_e']] = $entity['denomination'];
        }
        return ($collectivites);
    }
 
    function getDocumentsType() {
	$documents = array();
        $curl = $this->_initCurl('api/document-type.php'); 
        $result = curl_exec($curl);
        curl_close($curl);

        $natures = (json_decode($result));
        foreach($natures as $nature){
            $type = utf8_decode($nature->type);
            $nom  = utf8_decode($nature->nom);
            $documents[$type][] = $nom;
        }
        return $documents;
    }

    function getInfosType($type) {
        $infos = array();
        $curl = $this->_initCurl("api/document-type-info.php?type=$type");
        $result = curl_exec($curl);
        curl_close($curl);

        $infos = json_decode($result);
        return($infos);
    }

    function createDocument($id_e, $type='actes') {
        $infos = array();
        $curl = $this->_initCurl("api/create-document.php?id_e=$id_e&type=$type");
        $result = curl_exec($curl);
	curl_close($curl);

	$infos = json_decode($result);
	$infos = (array) $infos;
        return($infos['id_d']);
    }

    function modifyDocument($id_e, $id_d, $delib=array(), $annexes=array() ) {
	$file = WEBROOT_PATH."/files/generee/fd/null/".$delib['Deliberation']['id']."/delib.pdf";
	$acte = array('id_e'                    => $id_e,
                      'id_d'                    => $id_d,
                      'objet'                   => $delib['Deliberation']['objet_delib'],
                      'date_de_lacte'           => $delib['Seance']['date'],
                      'numero_de_lacte'         => $delib['Deliberation']['num_delib'],
                      'type'                    => $delib['Nomenclature']['code'],
                      'arrete'                  => "@$file",
                      'acte_nature'             => $delib['Deliberation']['nature_id'],
		     );
	$curl = $this->_initCurl('api/modif-document.php', $acte);
	$result = curl_exec($curl);
	curl_close($curl);
        foreach ($annexes as $annex) 
            $this->sendAnnex($id_e, $id_d,  $annex);
    }

    function insertInParapheur($id_e, $id_d, $sous_type = null) {
        $curl = $this->_initCurl("api/modif-document.php?id_e=$id_e&id_d=$id_d&envoi_iparapheur=true");
	$result = curl_exec($curl);
	curl_close($curl);
    }

    function insertInCircuit($id_e, $id_d, $sous_type) {
        $infos = array('id_e'                    => $id_e,
                      'id_d'                    => $id_d,
                      'iparapheur_sous_type'    => $sous_type);
        $curl = $this->_initCurl("api/modif-document.php", $infos);
	$result = curl_exec($curl);
	curl_close($curl);
    }

    function getInfosDocument($id_e, $id_d) { 
        $acte = array('id_e'                    => $id_e,
                      'id_d'                    => $id_d
		    );
        $curl = $this->_initCurl('api/detail-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function action($id_e, $id_d, $action) {
        $acte = array('id_e'                    => $id_e,
		      'id_d'                    => $id_d,
                      'action'                  => $action
                    );
        $curl = $this->_initCurl('api/action.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function sendAnnex($id_e, $id_d, $annex) {
        $acte = array('id_e'                    => $id_e,
                      'id_d'                    => $id_d,
                      'autre_document_attache'  => "@$annex"
                     );
        $curl = $this->_initCurl('api/modif-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    function getFile($id_e, $id_d, $field) {
        $url = Configure::read("PASTELL_HOST")."/web/document/recuperation-fichier.php?id_e=$id_e&id_d=$id_d&field=$field";
        $hostfile = fopen($url, 'r');
        $filename = tempnam ("/tmp/", "$field_");
        $fh = fopen($filename, 'w');

        while (!feof($hostfile)) {
            $output = fread($hostfile, 8192);
            fwrite($fh, $output);
        }
   
        fclose($hostfile);
        fclose($fh);
        $content = file_get_contents($filename); 
        unlink($filename);
        return ($content);
    }
}

?>
