<?php
class Listepresence extends AppModel {

	var $name = 'Listepresence';
	var	$cacheQueries = false;
	var $belongsTo = array(
			'Deliberation' =>
				array('className' => 'Deliberation',
						'foreignKey' => 'delib_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Acteur' =>
				array('className' => 'Acteur',
						'foreignKey' => 'acteur_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Suppleant' =>
				array('className' => 'Acteur',
						'foreignKey' => 'suppleant_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Mandataire' =>
				array('className' => 'Acteur',
						'foreignKey' => 'mandataire',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				)

	);

    /**
     * fonction d'initialisation des variables de fusion pour la liste des présents pour le vote d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id de la délibération
     */
    function setVariablesFusionPresents(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');

        // liste des variables utilisées dans le template
        $acteurFields = $aliasActeurFields = array();
        foreach($fusionVariables as $fusionVariable)
            if ($modelOdtInfos->hasUserField($fusionVariable.'_acteur_present')) {
                $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                $acteurFields[] = $fusionVariable;
            }
        if (empty($aliasActeurFields)) return;

        // lecture des données en base de données
        $this->Behaviors->load('Containable');
        $acteurs = $this->find('all', array (
            'fields' => array('Listepresence.suppleant_id'),
            'contain' => $aliasActeurFields,
            'conditions' => array('Listepresence.delib_id' => $deliberationId, 'Listepresence.present' => true),
            'order' => 'Acteur.position ASC'));
        if ($modelOdtInfos->hasUserField('nombre_acteur_present'))
            $oMainPart->addElement(new GDO_FieldType('nombre_acteur_present', count($acteurs), 'text'));
        if (empty($acteurs)) return;

        // itérations sur les acteurs présents
        $oStyleIteration = new GDO_IterationType("ActeursPresents");
        foreach($acteurs as &$acteur) {
            // traitement du suppléant
            if (!empty($acteur['Listepresence']['suppleant_id'])) {
                $suppleant = $this->Acteur->find('first', array(
                    'recursive' => -1,
                    'fields' => $aliasActeurFields,
                    'conditions' => array('id' => $acteur['Listepresence']['suppleant_id'])));
                if (empty($suppleant))
                    throw new Exception('suppléant non trouvé id:'.$acteur['Listepresence']['suppleant_id']);
                $acteur = &$suppleant;
            }
            // traitement de la date de naissance
            if (!empty($acteur['Acteur']['date_naissance']))
                $acteur['Acteur']['date_naissance'] = date("d/m/Y", strtotime($acteur['Acteur']['date_naissance']));
            $oDevPart = new GDO_PartType();
            foreach($acteurFields as $fieldname)
                $oDevPart->addElement(new GDO_FieldType($fieldname.'_acteur_present', $acteur['Acteur'][$fieldname], "text"));
            $oStyleIteration->addPart($oDevPart);
        }
        $oMainPart->addElement($oStyleIteration);
    }

    /**
     * fonction d'initialisation des variables de fusion pour la liste des absents pour le vote d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id de la délibération
     */
    function setVariablesFusionAbsents(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');

        // liste des variables utilisées dans le template
        $acteurFields = $aliasActeurFields = array();
        foreach($fusionVariables as $fusionVariable)
            if ($modelOdtInfos->hasUserField($fusionVariable.'_acteur_absent')) {
                $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                $acteurFields[] = $fusionVariable;
            }
        if (empty($aliasActeurFields)) return;

        // lecture des données en base de données
        $this->Behaviors->load('Containable');
        $acteurs = $this->find('all', array (
            'fields' => array('Listepresence.id'),
            'contain' => $aliasActeurFields,
            'conditions' => array(
                'Listepresence.delib_id' => $deliberationId,
                'Listepresence.present' => false,
                'Listepresence.mandataire' => null),
            'order' => 'Acteur.position ASC'));
        if ($modelOdtInfos->hasUserField('nombre_acteur_absent'))
            $oMainPart->addElement(new GDO_FieldType('nombre_acteur_absent', count($acteurs), 'text'));
        if (empty($acteurs)) return;

        // itérations sur les acteurs absents
        $oStyleIteration = new GDO_IterationType("ActeursAbsents");
        foreach($acteurs as &$acteur) {
            // traitement de la date de naissance
            if (!empty($acteur['Acteur']['date_naissance']))
                $acteur['Acteur']['date_naissance'] = date("d/m/Y", strtotime($acteur['Acteur']['date_naissance']));
            $oDevPart = new GDO_PartType();
            foreach($acteurFields as $fieldname)
                $oDevPart->addElement(new GDO_FieldType($fieldname.'_acteur_absent', $acteur['Acteur'][$fieldname], "text"));
            $oStyleIteration->addPart($oDevPart);
        }
        $oMainPart->addElement($oStyleIteration);
    }

    /**
     * fonction d'initialisation des variables de fusion pour la liste des mandatés pour le vote d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id de la délibération
     */
    function setVariablesFusionMandates(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');

        // liste des variables utilisées dans le template
        $acteurFields = $aliasActeurFields = $mandateFields = $aliasMandateFields = array();
        foreach($fusionVariables as $fusionVariable)  {
            if ($modelOdtInfos->hasUserField($fusionVariable.'_acteur_mandataire')) {
                $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                $acteurFields[] = $fusionVariable;
            }
            if ($modelOdtInfos->hasUserField($fusionVariable.'_acteur_mandate')) {
                $aliasMandateFields[] = 'Mandataire.'.$fusionVariable;
                $mandateFields[] = $fusionVariable;
            }
        }
        if (empty($aliasActeurFields) && empty($aliasMandateFields)) return;

        // lecture des données en base de données
        $this->Behaviors->load('Containable');
        $acteurs = $this->find('all', array (
            'fields' => array('Listepresence.id'),
            'contain' => array_merge($aliasActeurFields,$aliasMandateFields),
            'conditions' => array(
                'Listepresence.delib_id' => $deliberationId,
                'Listepresence.present' => false,
                'Listepresence.mandataire <>' => null),
            'order' => 'Acteur.position ASC'));
        if ($modelOdtInfos->hasUserField('nombre_acteur_mandataire'))
            $oMainPart->addElement(new GDO_FieldType('nombre_acteur_mandataire', count($acteurs), 'text'));
        if (empty($acteurs)) return;

        // itérations sur les acteurs mandatés
        $oStyleIteration = new GDO_IterationType("ActeursMandates");
        foreach($acteurs as &$acteur) {
            // traitement de la date de naissance
            if (!empty($acteur['Acteur']['date_naissance']))
                $acteur['Acteur']['date_naissance'] = date("d/m/Y", strtotime($acteur['Acteur']['date_naissance']));
            if (!empty($acteur['Mandataire']['date_naissance']))
                $acteur['Mandataire']['date_naissance'] = date("d/m/Y", strtotime($acteur['Mandataire']['date_naissance']));
            $oDevPart = new GDO_PartType();
            foreach($acteurFields as $fieldname)
                $oDevPart->addElement(new GDO_FieldType($fieldname.'_acteur_mandataire', $acteur['Acteur'][$fieldname], "text"));
            foreach($mandateFields as $fieldname)
                $oDevPart->addElement(new GDO_FieldType($fieldname.'_acteur_mandate', $acteur['Mandataire'][$fieldname], "text"));
            $oStyleIteration->addPart($oDevPart);
        }
        $oMainPart->addElement($oStyleIteration);
    }
}
?>
