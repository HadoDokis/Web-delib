<?php
class Vote extends AppModel {

    var $name = 'Vote';
    var $belongsTo = array(
        'Acteur' => array('className' => 'Acteur',
            'foreignKey' => 'acteur_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => ''
        )
    );

    /**
     * fonction d'initialisation des variables de fusion pour les votes d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id de la délibération
     */
    function setVariablesFusion(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');
        $voteIterations = array(
            array('iterationName' => 'ActeursContre', 'fusionVariableSuffixe' => 'contre', 'voteResultat' => 2),
            array('iterationName' => 'ActeursPour', 'fusionVariableSuffixe' => 'pour', 'voteResultat' => 3),
            array('iterationName' => 'ActeursAbstention', 'fusionVariableSuffixe' => 'abstention', 'voteResultat' => 4),
            array('iterationName' => 'ActeursSansParticipation', 'fusionVariableSuffixe' => 'sans_participation', 'voteResultat' => 5));

        // pour chaque itération de vote
        foreach($voteIterations as &$voteIteration) {
            // initialisations
            $conditions = array('Vote.delib_id'=>$deliberationId, 'Vote.resultat'=>$voteIteration['voteResultat']);

            // nombre de votes
            $nbVotes = $this->find('count', array('recursive'=>-1, 'conditions'=>$conditions));
            $oMainPart->addElement(new GDO_FieldType('nombre_acteur_'.$voteIteration['fusionVariableSuffixe'], $nbVotes, 'text'));
            if ($nbVotes==0) continue;

            // liste des variables utilisées dans le template
            $acteurFields = $aliasActeurFields = array();
            foreach($fusionVariables as $fusionVariable)
                if ($modelOdtInfos->hasUserFieldDeclared($fusionVariable.'_acteur_'.$voteIteration['fusionVariableSuffixe'])) {
                    $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                    $acteurFields[] = $fusionVariable;
                }
            if (empty($aliasActeurFields)) continue;

            // lecture des données en base
            $this->Behaviors->load('Containable');
            $acteurs = $this->find('all', array (
                'fields' => array('Vote.id'),
                'contain' => $aliasActeurFields,
                'conditions' => array('Vote.delib_id' => $deliberationId, 'Vote.resultat' => $voteIteration['voteResultat']),
                'order' => 'Acteur.position ASC'));

            // itérations sur les acteurs
            $oStyleIteration = new GDO_IterationType($voteIteration['iterationName']);
            foreach($acteurs as &$acteur) {
                // traitement de la date de naissance
                if (!empty($acteur['Acteur']['date_naissance']))
                    $acteur['Acteur']['date_naissance'] = date("d/m/Y", strtotime($acteur['Acteur']['date_naissance']));
                $oDevPart = new GDO_PartType();
                foreach($acteurFields as $fieldname)
                    $oDevPart->addElement(new GDO_FieldType($fieldname.'_acteur_'.$voteIteration['fusionVariableSuffixe'], $acteur['Acteur'][$fieldname], "text"));
                $oStyleIteration->addPart($oDevPart);
            }
            $oMainPart->addElement($oStyleIteration);
        }
    }
}
?>
