<?php
 
class PastellComponent extends Object {

    function _initCurl ($api) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
        curl_setopt($curl, CURLOPT_USERPWD, Configure::read("PASTELL_LOGIN").":".Configure::read("PASTELL_PWD"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, Configure::read("PASTELL_HOST").'/web/api/'.$api);
        return $curl;  
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

        $infos = (json_decode($result));
        return($infos);
    }

     


}

?>
