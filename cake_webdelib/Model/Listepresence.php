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
    function setVariablesFusionPresents(&$aData ,&$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');
        $conditions = array('Listepresence.delib_id' => $deliberationId, 'Listepresence.present' => true);

        // nombre d'acteurs présents
        $nbActeurs = $this->find('count', array('recursive'=>-1, 'conditions'=>$conditions));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_acteur_present')) {
            $aData['nombre_acteur_present']= $nbActeurs;//, 'text'));
        }
        // liste des variables utilisées dans le template
        $acteurFields = $aliasActeurFields = array();
        foreach($fusionVariables as $fusionVariable)
            if ($modelOdtInfos->hasUserFieldDeclared($fusionVariable.'_acteur_present')) {
                $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                $acteurFields[] = $fusionVariable;
            }
        if (empty($aliasActeurFields)) return;

        // lecture des données en base de données
        $this->Behaviors->load('Containable');
        $acteurs = $this->find('all', array (
            'fields' => array('Listepresence.suppleant_id'),
            'contain' => $aliasActeurFields,
            'conditions' => $conditions,
            'order' => 'Acteur.position ASC'));

        // itérations sur les acteurs présents
        if ($nbActeurs==0){
           $oDevPart = new GDO_PartType();
            foreach($acteurFields as $fieldname)
                $oDevPart->addElement(new GDO_FieldType($fieldname.'_acteur_present', $acteur['Acteur'][$fieldname], "text"));
            $oStyleIteration->addPart($oDevPart);
            $oMainPart->addElement($oStyleIteration);
            return;
        }
        
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
            $aActeursPresents=array();
            foreach($acteurFields as $fieldname)
                $aActeursPresents[$fieldname.'_acteur_present']=$acteur['Acteur'][$fieldname];//, "text"));
        }
        
        $aData['ActeursPresents']=$aActeursPresents;
    }

    /**
     * fonction d'initialisation des variables de fusion pour la liste des absents pour le vote d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id de la délibération
     */
    function setVariablesFusionAbsents(&$aData ,&$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');
        $conditions = array('Listepresence.delib_id'=>$deliberationId, 'Listepresence.present'=>false, 'Listepresence.mandataire'=>null);

        // nombre d'acteurs absents
        $nbActeurs = $this->find('count', array('recursive'=>-1, 'conditions'=>$conditions));
        
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_acteur_absent')) {
            $aData['nombre_acteur_absent']= array('value'=> $nbActeurs, 'type'=>'text');
        }
        // liste des variables utilisées dans le template
        $acteurFields = $aliasActeurFields = array();
        foreach($fusionVariables as $fusionVariable)
            if ($modelOdtInfos->hasUserFieldDeclared($fusionVariable.'_acteur_absent')) {
                $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                $acteurFields[] = $fusionVariable;
            }
        if (empty($aliasActeurFields)) return;
        
        // lecture des données en base de données
        $aActeursAbsents = array();
        $acteurs = $this->find('all', array (
            'fields' => array('Listepresence.id'),
            'contain' => $aliasActeurFields,
            'conditions' => $conditions,
            'order' => 'Acteur.position ASC'));

        if ($nbActeurs==0){
            foreach ($acteurFields as $fieldname) {
                $aActeursAbsents[$fieldname . '_acteur_absent'] = array('value' => '', 'type' => 'text');
            }
        }
        
        foreach($acteurs as &$acteur) {
            // traitement de la date de naissance
            if (!empty($acteur['Acteur']['date_naissance'])) {
                $acteur['Acteur']['date_naissance'] = array('value' => date("d/m/Y", strtotime($acteur['Acteur']['date_naissance'])), 'type' => 'date');
            }
            foreach ($acteurFields as $fieldname) {
                $aActeursAbsents[$fieldname . '_acteur_absent'] = array('value' => $acteur['Acteur'][$fieldname], 'type' => 'text');
            }
        }
        $aData['ActeursAbsents']=$aActeursAbsents;
    }

    /**
     * fonction d'initialisation des variables de fusion pour la liste des mandatés pour le vote d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id de la délibération
     */
    function setVariablesFusionMandates(&$aData ,&$modelOdtInfos, $deliberationId) {
        // initialisations
        $fusionVariables = array('nom', 'prenom', 'salutation', 'titre', 'date_naissance', 'adresse1', 'adresse2', 'cp', 'ville', 'email', 'telfixe', 'telmobile', 'note');
        $conditions = array('Listepresence.delib_id'=>$deliberationId, 'Listepresence.present'=>false, 'Listepresence.mandataire <>'=>null);

        // nombre d'acteurs mandatés
        $nbActeurs = $this->find('count', array('recursive'=>-1, 'conditions'=>$conditions));
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_acteur_mandataire')) {
            $aData['nombre_acteur_mandataire']= $nbActeurs;//, 'text'));
        }
        // liste des variables utilisées dans le template
        $acteurFields = $aliasActeurFields = $mandateFields = $aliasMandateFields = array();
        foreach($fusionVariables as $fusionVariable)  {
            if ($modelOdtInfos->hasUserFieldDeclared($fusionVariable.'_acteur_mandataire')) {
                $aliasActeurFields[] = 'Acteur.'.$fusionVariable;
                $acteurFields[] = $fusionVariable;
            }
            if ($modelOdtInfos->hasUserFieldDeclared($fusionVariable.'_acteur_mandate')) {
                $aliasMandateFields[] = 'Mandataire.'.$fusionVariable;
                $mandateFields[] = $fusionVariable;
            }
        }
        if (empty($aliasActeurFields) && empty($aliasMandateFields)) return;

        // lecture des données en base de données
        $acteurs = $this->find('all', array (
            'fields' => array('Listepresence.id'),
            'contain' => array_merge($aliasActeurFields,$aliasMandateFields),
            'conditions' => $conditions,
            'order' => 'Acteur.position ASC'));

        $aActeursMandates=array();
        if ($nbActeurs==0){
            foreach ($acteurFields as $fieldname) {
                $aActeursMandates[$fieldname . '_acteur_mandataire'] = array('value' => '', 'type' => 'text');
            }
            foreach ($mandateFields as $fieldname) {
                $aActeursMandates[$fieldname . '_acteur_mandate'] = array('value' => '', 'type' => 'text');
            }
        }
        
        foreach($acteurs as &$acteur) {
            // traitement de la date de naissance
            if (!empty($acteur['Acteur']['date_naissance'])) {
                $acteur['Acteur']['date_naissance'] = date("d/m/Y", strtotime($acteur['Acteur']['date_naissance']));
            }
            if (!empty($acteur['Mandataire']['date_naissance'])) {
                $acteur['Mandataire']['date_naissance'] = date("d/m/Y", strtotime($acteur['Mandataire']['date_naissance']));
            }

            foreach ($acteurFields as $fieldname) {
                $aActeursMandates[$fieldname . '_acteur_mandataire'] = array('value' => $acteur['Acteur'][$fieldname], 'type' => 'text');
            }
            foreach ($mandateFields as $fieldname) {
                $aActeursMandates[$fieldname . '_acteur_mandate'] = array('value' => $acteur['Mandataire'][$fieldname], 'type' => 'text');
            }
        }
        
        $aData['ActeursMandates']=$aActeursMandates;
    }
}