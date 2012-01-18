<?php
 
class PastellComponent extends Object {

    function _initCurl ($api, $data=array()) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
        curl_setopt($curl, CURLOPT_USERPWD, Configure::read("PASTELL_LOGIN").":".Configure::read("PASTELL_PWD"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, Configure::read("PASTELL_HOST").'/web/api/'.$api);
	if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
        }
        return $curl;  
    }

    function getInfosField($id_e, $id_d, $type) {
        $nomenclature = array();
        $curl = $this->_initCurl("external-data.php?id_e=$id_e&id_d=$id_d&field=$type");
        $result = curl_exec($curl);
        curl_close($curl);
	return  json_decode($result);
    }

    function listEntities() {
	$entities = array();
        $collectivites = array();
        $curl = $this->_initCurl('list-entite.php'); 
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
        $curl = $this->_initCurl('document-type.php'); 
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
        $curl = $this->_initCurl("document-type-info.php?type=$type");
        $result = curl_exec($curl);
        curl_close($curl);

        $infos = json_decode($result);
        return($infos);
    }

    function createDocument($id_e, $type='actes') {
        $infos = array();
        $curl = $this->_initCurl("create-document.php?id_e=$id_e&type=$type");
        $result = curl_exec($curl);
	curl_close($curl);

	$infos = json_decode($result);
	$infos = (array) $infos;
        return($infos['id_d']);
    }

    function modifyDocument($id_e, $id_d, $delib=array()) {
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

	$curl = $this->_initCurl('modif-document.php', $acte);
	$result = curl_exec($curl);
	curl_close($curl);
    }

    function insertInParapheur($id_e, $id_d, $sous_type = null) {
        $curl = $this->_initCurl("modif-document.php?id_e=$id_e&id_d=$id_d&envoi_iparapheur=true");
	$result = curl_exec($curl);
	curl_close($curl);
    }

    function insertInCircuit($id_e, $id_d, $sous_type) {
        $infos = array('id_e'                    => $id_e,
                      'id_d'                    => $id_d,
                      'iparapheur_sous_type'    => $sous_type);
        $curl = $this->_initCurl("modif-document.php", $infos);
	$result = curl_exec($curl);
	curl_close($curl);
    }

    function getInfosDocument($id_e, $id_d) { 
        $acte = array('id_e'                    => $id_e,
                      'id_d'                    => $id_d
		    );
        $curl = $this->_initCurl('detail-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function action($id_e, $id_d, $action) {
        $acte = array('id_e'                    => $id_e,
		      'id_d'                    => $id_d,
                      'action'                  => $action
                    );
        $curl = $this->_initCurl('action.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }
}

?>
