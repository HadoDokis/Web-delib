<?php

class Commentaire extends AppModel {

    var $name = 'Commentaire';
    var $validate = array( 'texte' => array(
                                      array( 'rule'    => 'notEmpty',
                                             'message' => 'Entrer un commentaire.')));

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

}
?>
