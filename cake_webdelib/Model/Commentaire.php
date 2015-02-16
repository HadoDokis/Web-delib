<?php

class Commentaire extends AppModel {

    var $name = 'Commentaire';
    var $validate = array( 'texte' => array(
                                      array( 'rule'    => 'notEmpty',
                                             'message' => 'Entrer un commentaire.')));
    
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'agent_id'),
    );

    /**
     * fonction d'initialisation des variables de fusion pour les commentaires d'un projet/délibération
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id du projet/délibération
     */
    function setVariablesFusion(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        //lecture des commentaires
        $commentaires = $this->find('all', array(
            'recursive' => -1,
            'fields' => array('texte'),
            'conditions' => array('delib_id' => $deliberationId, 'commentaire_auto' => false)));
        if (empty($commentaires)) return;

        $oStyleIteration = new GDO_IterationType("Commentaires");
        foreach($commentaires as $commentaire) {
            $oDevPart = new GDO_PartType();
            $oDevPart->addElement(new GDO_FieldType("texte_commentaire", $commentaire['Commentaire']['texte'], "text"));
            $oStyleIteration->addPart($oDevPart);
        }
        $oMainPart->addElement($oStyleIteration);
    }
    
    function beforeFind($query)
    {
        // Préparation de la requete pour le afterFind
        if(isset($query['fields']) && !in_array('Commentaire.agent_id', $query['fields']))
                $query['fields'][] = 'Commentaire.agent_id';

        if(isset($query['fields']) && !in_array('Commentaire.commentaire_auto', $query['fields']))
                $query['fields'][] = 'Commentaire.commentaire_auto';

        return $query;
    }
    
    function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if ($val['Commentaire']['agent_id'] == -1) {
                $results[$key]['User']['prenom'] = "commentaire auto (Parapheur)";
                $results[$key]['User']['nom'] = '';
            } elseif ($val['Commentaire']['commentaire_auto']==true){
                $results[$key]['User']['nom'] = 'commentaire auto';
                $results[$key]['User']['prenom'] = '';
            } 
        }
            
        return $results;
    }
}