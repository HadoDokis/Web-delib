<?php

App::uses('File', 'Utility');

class Collectivite extends AppModel {

    var $name = 'Collectivite';
    var $cacheSources = 'false';
    var $validate = array(
        'nom' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le nom de la collectivité'
            )
        ),
        'adresse' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer l\'adresse.'
            )
        ),
        'CP' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le code postal.'
            )
        ),
        'ville' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer la ville.'
            )
        ),
        'telephone' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le numéro de téléphone.'
            )
        ),
        'logo' => array(
            array(
                'rule' => array('checkFormatLogo', array('jpg', 'png'), false),
                'message' => "Format de l'image invalide. Autorisé : fichier 'jpg', 'jpeg' et 'png'"
            )
        ),
    );

    /**
     * fonction d'initialisation des variables de fusion pour la collectivité
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $aData variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param int $id id des données à fusionner
     * @throws Exception
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id) {
        // lecture de la collectivité en  base de données
        $collectivite = $this->find('first', array(
            'recursive'  => -1,
            'fields' => array('id', 'nom', 'adresse', 'CP', 'ville', 'telephone'),
            'conditions' => array('id' => $id)));
        if (empty($collectivite))
            throw new Exception('collectivité id:'.$id.' non trouvé en base de données');

        // initialisation des variables
        if ($modelOdtInfos->hasUserFieldDeclared('nom_collectivite'))
            $aData['nom_collectivite']=$collectivite['Collectivite']['nom'];
        if ($modelOdtInfos->hasUserFieldDeclared('adresse_collectivite'))
            $aData['adresse_collectivite']=$collectivite['Collectivite']['adresse'];
        if ($modelOdtInfos->hasUserFieldDeclared('cp_collectivite'))
            $aData['cp_collectivite']= $collectivite['Collectivite']['CP'];
        if ($modelOdtInfos->hasUserFieldDeclared('ville_collectivite'))
            $aData['ville_collectivite']=$collectivite['Collectivite']['ville'];
        if ($modelOdtInfos->hasUserFieldDeclared('telephone_collectivite'))
            $aData['telephone_collectivite']=$collectivite['Collectivite']['telephone'];
    }
    
    function checkFormatLogo($data, $extension = null, $required = false) {
        return parent::checkFormat($data['logo'], $extension, $required);
    }


}
