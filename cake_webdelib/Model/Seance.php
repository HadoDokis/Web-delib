<?php

class Seance extends AppModel
{
    public $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    public $months = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

    public $validate = array(
        'type_id' => array(
            array(
                'rule' => 'notEmpty',
                'required'   => true,
                'message' => 'Entrer le type de seance'
            )
        ),
        'avis' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner un avis'
            )
        ),
        'date' => array(
            array(
                'rule'       => array('datetime', 'ymd'),
                'required'   => true,
                'allowEmpty' => false,
                'message'    => 'Entrer une date valide'
            )
        ),
        'commission' => array(
            array(
                'rule' => 'notEmpty',
                'allowEmpty' => false,
                'message' => 'Entrer le texte de débat'
            )
        ),
    );

    public $belongsTo = array(
        'Typeseance' => array(
            'className' => 'Typeseance',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'type_id'),
        'Secretaire' => array('className' => 'Acteur',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'secretaire_id'),
        'President' => array('className' => 'Acteur',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'president_id'));

    public $hasMany = array(
        'Infosup' => array('dependent' => true,
            'foreignKey' => 'foreign_key',
            'conditions' => array('Infosup.model' => 'Seance')),
        'Deliberationseance' => array(
            'className' => 'Deliberationseance',
            //'joinTable' => 'Deliberation',
            'foreignKey' => 'seance_id',
            //'conditions' => array('Deliberation.etat >='=>0),
            'order' => 'Deliberationseance.position ASC'
        ),


    );

    public $hasAndBelongsToMany = array(
        'Deliberation' => array(
            'className' => 'Deliberation',
            'joinTable' => 'deliberations_seances',
            'foreignKey' => 'seance_id',
            'associationForeignKey' => 'deliberation_id',
            'unique' => true,
            'conditions' => array('Deliberation.etat >=' => 0),
            'fields' => '',
            'order' => 'DeliberationsSeance.position ASC',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''));

    
    public function beforeSave($options = array()) {
        if (!empty($this->data['Seance']['texte_doc'])){
            $analyse=$this->analyzeFile($this->data['Seance']['texte_doc']['tmp_name']);
            $this->data['Seance']['debat_global_type']=$analyse['mimetype'];
            $this->data['Seance']['debat_global_name']=$this->data['Seance']['texte_doc']['name'];
            $this->data['Seance']['debat_global_size']=$this->data['Seance']['texte_doc']['size'];
            $this->data['Seance']['debat_global']=file_get_contents($this->data['Seance']['texte_doc']['tmp_name']);
        }
        return true;
    }
    
    public function beforeFind($query = array()) {
        $query['conditions'] = (is_array($query['conditions'])) ? $query['conditions'] : array();
        $db = $this->getDataSource();
        
        //Gestion des droits sur les types d'actes
        if (!isset($query['Seance.typeseance_id']))
        {
            $Aro = ClassRegistry::init('Aro');

            $Aro->Behaviors->attach( 'Database.DatabaseTable' );
            $Aro->Permission->Behaviors->attach( 'Database.DatabaseTable' );
            $Aro->Permission->Aco->Behaviors->attach( 'Database.DatabaseTable' );
            $Aro->Permission->Aco->bindModel(
                array('belongsTo' => array(
                        'Typeacte' => array(
                            'className' => 'Typeacte',
                            'foreignKey'=> false,
                            'conditions'=> array(
                                'Aco.model' => 'Typeacte',
                                'Typeacte.id = Aco.foreign_key'
                            ),
                        )
                    )
                )
            );

            $subQuery = array(
                'fields' => array(
                    'Typeseance.id'
                ),
                'contain' => false,
                'joins' => array(
                    $Aro->join( 'Permission', array( 'type' => 'INNER' ) ),
                    $Aro->Permission->join( 'Aco', array( 'type' => 'INNER' ) ),
                    $Aro->Permission->Aco->join( 'Typeacte', array( 'type' => 'INNER') ),
                    $Aro->Permission->Aco->Typeacte->join( 'TypeseancesTypeacte', array( 'type' => 'INNER') ),
                    $Aro->Permission->Aco->Typeacte->TypeseancesTypeacte->join( 'Typeseance', array( 'type' => 'INNER') ),
                ),
                'conditions' => array(
                    'Aro.foreign_key' => AuthComponent::user('id'),
                    'Permission._read' => 1,
                    'Aro.model' => 'User'
                )
            );
            
            $subQuery=$Aro->sql($subQuery);
            $subQuery = ' "Seance"."type_id" IN (' . $subQuery . ') ';
            $subQueryExpression = $db->expression($subQuery);
            $conditions[] = $subQueryExpression;
            
            $query['conditions'] = array_merge($query['conditions'], $conditions);
        }
        
        
        return $query;
    }
    
    /*
     * 
     */
    public function SaveDebatGen($data)
    {
        $this->validate=array();
        $this->validator()->add('texte_doc', array(
            'required' => array(
                'rule' => 'isUploadedFile',
                'message' => 'Veuillez mettre un fichier pour enregistrer la saisie des débats généraux'
            ),
            'upload' => array(
                'rule' => 'uploadError',
                'message' => 'Une erreur est survenue lors de l\'upload du document'
            ),
            'checkFormat' => array(
                'rule' => array('checkFormat', 'odt', true),
                'message'       => 'Format du document invalide. Autorisé : fichier ODT'
            )
        ));
        
        return parent::save($data);
    }
    
    /**
     * retourne la liste des séances futures avec le nom du type de séance
     */
    function generateList($conditionSup = null, $afficherTtesLesSeances = false, $natures = array())
    {
        $generateList = array();
        App::import('model', 'TypeseancesTypeacte');
        $TypeseancesTypeacte = new TypeseancesTypeacte();
        
        
        //$typeseances = $TypeseancesTypeacte->getTypeseanceParNature($natures);

        $conditions = array();
        //$conditions['Seance.type_id'] = $typeseances;
        $conditions['Seance.traitee'] = '0';

        if (!empty($conditionSup))
            $conditions = Set::pushDiff($conditions, $conditionSup);

        $seances = $this->find('all', array(
            'conditions' => $conditions,
            'order' => array('date ASC'),
            'fields' => array('Seance.id', 'Seance.date'),
            'contain' => array('Typeseance.action', 'Typeseance.retard', 'Typeseance.libelle')));

        foreach ($seances as $seance) {
            $deliberante = "----";
            if ($seance['Typeseance']['action'] == 0)
                $deliberante = "(*)";
            if ($afficherTtesLesSeances) {
                $dateTimeStamp = strtotime($seance['Seance']['date']);
                $dateFr = $this->days[date('w', $dateTimeStamp)] . ' ' . date('d', $dateTimeStamp) . ' ' .
                    $this->months[date('n', $dateTimeStamp)] . ' ' . date('Y', $dateTimeStamp) . ' - ' .
                    date('H', $dateTimeStamp) . ':' . date('i', $dateTimeStamp);
                $generateList[$seance['Seance']['id']] = "$deliberante " . $seance['Typeseance']['libelle'] . " du " . $dateFr;
            } else {
                $retard = $seance['Typeseance']['retard'];

                if ($seance['Seance']['date'] >= date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $retard, date("Y")))) {
                    $dateTimeStamp = strtotime($seance['Seance']['date']);
                    $dateFr = $this->days[date('w', $dateTimeStamp)] . ' ' . date('d', $dateTimeStamp) . ' ' .
                        $this->months[date('n', $dateTimeStamp)] . ' ' . date('Y', $dateTimeStamp) . ' - ' .
                        date('H', $dateTimeStamp) . ':' . date('i', $dateTimeStamp);
                    $generateList[$seance['Seance']['id']] = "$deliberante " . $seance['Typeseance']['libelle'] . " du " . $dateFr;
                }
            }
        }
        return $generateList;
    }

    function generateAllList()
    {
        $generateList = array();
        $seances = $this->find('all', array('order' => 'date DESC',
            'fields' => array('Seance.id', 'Seance.type_id', 'Seance.date'),
            'contain' => array('Typeseance.libelle', 'Typeseance.action')));

        foreach ($seances as $seance) {
            $deliberante = "";
            if ($seance['Typeseance']['action'] == 0)
                $deliberante = "(*)";

            $dateTimeStamp = strtotime($seance['Seance']['date']);
            $dateFr = $this->days[date('w', $dateTimeStamp)] . ' ' . date('d', $dateTimeStamp) . ' ' .
                $this->months[date('n', $dateTimeStamp)] . ' ' . date('Y', $dateTimeStamp) . ' - ' .
                date('H', $dateTimeStamp) . ':' . date('i', $dateTimeStamp);
            $generateList[$seance['Seance']['id']] = "$deliberante " . $seance['Typeseance']['libelle'] . " du " . $dateFr;
        }
        return $generateList;
    }

    function NaturecanSave($seance_id, $nature_id)
    {
        if (empty($seance_id))
            return true;
        $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'recursive' => -1,
            'fields' => array('Seance.type_id')));
        App::import('Model', 'TypeseancesTypeacte');
        $this->TypeseancesTypeacte = new TypeseancesTypeacte();
        $natures = $this->TypeseancesTypeacte->getNaturesParTypeseance($seance['Seance']['type_id']);
        return in_array($nature_id, $natures);
    }

    function getDeliberationsId($seance_id)
    {
        $tab = array();
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();

        $deliberations = $this->Deliberationseance->find('all', array(
            'conditions' => array('Deliberationseance.seance_id' => $seance_id, 'Deliberation.etat >=' => 0),
            'fields' => array('Deliberationseance.deliberation_id','Deliberationseance.position'),
            'contain' => array('Deliberation.id', 'Deliberation.etat'),
            'order' => 'Deliberationseance.position ASC',
        ));
        foreach ($deliberations as $deliberation)
            $tab[$deliberation['Deliberationseance']['position']] = $deliberation['Deliberationseance']['deliberation_id'];
        return $tab;
    }

    function getLastPosition($seance_id)
    {
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $deliberations = $this->Deliberationseance->find('count', array('conditions' => array('Seance.id' => $seance_id,
            'Deliberation.etat !=' => -1)));
        return ($deliberations + 1);
    }

    function reOrdonne($delib_id, $seances_selectionnees)
    {
        if (!is_array($seances_selectionnees)) {
            
            $seance_id = $seances_selectionnees;
            unset($seances_selectionnees);
            $seances_selectionnees = array();
            $seances_selectionnees[0] = $seance_id;
        } elseif (!isset($seances_selectionnees) || empty($seances_selectionnees) || ($seances_selectionnees[0] == null))
            $seances_selectionnees = array();

        $seances_enregistrees = array();

        $delibs = $this->Deliberationseance->find('all', array(
            'fields' => array('deliberation_id', 'id', 'position', 'seance_id'),
            'conditions' => array('deliberation_id' => $delib_id),
            'order' => array('position ASC'),
            'recursive'=>-1,
            ));
        
        if (!empty($delibs)) {
            foreach ($delibs as $delib) {
                $seances_enregistrees[] = $delib['Deliberationseance']['seance_id'];
            }

            $seances_a_retirer = array_diff($seances_enregistrees, $seances_selectionnees);

            foreach ($seances_a_retirer as $seance_id) {
                $this->Deliberationseance->deleteDeliberationseance($delib_id, $seance_id);
            }
        }

        if (is_array($seances_enregistrees) and  (!empty($seances_enregistrees))) {
            $seances_a_ajouter = array_diff($seances_selectionnees, $seances_enregistrees);
        } else
            $seances_a_ajouter = $seances_selectionnees;

        foreach ($seances_a_ajouter as $seance_id) {
            $this->Deliberationseance->addDeliberationseance($delib_id, $seance_id);
        }
    }

    /**
     * 
     * @param type $seance_id
     * @return string
     */
    function getDate($seance_id)
    {
        if (empty($seance_id))
            return '';
        else {
            $objCourant = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
                'fields' => array('Seance.date'),
                'recursive' => -1));
            return $objCourant['Seance']['date'];
        }
    }

    function getType($seance_id)
    {
        if (empty($seance_id))
            return '';
        else {
            $objCourant = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
                'fields' => array('Seance.type_id'),
                'recursive' => -1));
            return $objCourant['Seance']['type_id'];
        }
    }

    function getSeanceDeliberante($tab_seances) {
        foreach ($tab_seances as $seance_id) {
            $seance = $this->find('first', array(
                'conditions' => array('Seance.id' => $seance_id),
                'fields' => array('Seance.id'),
                'contain' => array('Typeseance.action'),
                'recursive' => -1
            ));
            if ($seance['Typeseance']['action'] == 0) {
                return $seance['Seance']['id'];
            }
        }
        return null;
    }

    function isSeanceDeliberante($seance_id)
    {
        $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'fields' => array('Seance.id', 'Seance.type_id'),
            'contain' => array('Typeseance.action')));
        return ($seance['Typeseance']['action'] == 0);
    }

    function getSeancesDeliberantes() {
        $tab_seances = array();
        $seances = $this->find('all', array(
            'conditions' => array(
                'Typeseance.action' => 0,
                'Seance.traitee' => 0),
            'fields' => array('Seance.id'),
            'contain' => array('Typeseance.action')));
        foreach ($seances as $seance)
            $tab_seances[] = $seance['Seance']['id'];
        return ($tab_seances);
    }
    
    /**
     * fonction d'initialisation des variables de fusion pour plusieurs séances
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $ids liste des id des séances
     */
    function setVariablesFusionSeances(&$aData, &$modelOdtInfos, $ids, $addProjetIterations=true) {
        // pour toutes les séances
        $aData['nombre_seance']=count($ids);//, 'text'));
        foreach($ids as $id) {
            $this->setVariablesFusion($aData['Seances'][], $modelOdtInfos, $id, 'seances', $addProjetIterations);
        }
    }

    /**
     * fonction d'initialisation des variables de fusion pour une séance
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id de l'occurence en base
     * @param string $suffixe suffixe des variables de fusion de la séance
     * @param boolean $addProjetIterations ajoute les itérations sur les projets
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id, $suffixe='seance', $addProjetIterations=true) {
        
        // lecture de la séance en base
        $seance = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'date', 'date_convocation', 'commentaire', 'type_id', 'president_id', 'secretaire_id'),
            'conditions' => array('id' => $id)));
        if (empty($seance))
            throw new Exception('séance  id:'.$id.' non trouvée en base de données');

        // fusion des variables
        $dateSeanceTimeStamp = strtotime($seance['Seance']['date']);
        if ($modelOdtInfos->hasUserFieldDeclared('date_'.$suffixe.'_lettres')) {
            App::uses('CakeTime', 'Utility');
            $aData['date_'.$suffixe.'_lettres'] = array('value'=> AppTools::dateLettres($dateSeanceTimeStamp), 'type'=>'text');
        }
        if ($modelOdtInfos->hasUserFieldDeclared('date_'.$suffixe))
            $aData['date_'.$suffixe]= array('value'=> date('d/m/Y', $dateSeanceTimeStamp), 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('heure_'.$suffixe))
            $aData['heure_'.$suffixe]= array('value'=> date('H:i', $dateSeanceTimeStamp), 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('hh_'.$suffixe))
            $aData['hh_'.$suffixe]= array('value'=> date('H', $dateSeanceTimeStamp), 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('mm_'.$suffixe))
            $aData['mm_'.$suffixe]= array('value'=> date('i', $dateSeanceTimeStamp), 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('date_convocation_'.$suffixe))
            $aData['date_convocation_'.$suffixe]= array('value'=> (empty($seance['Seance']['date_convocation'])?'':date('d/m/Y', strtotime($seance['Seance']['date_convocation']))), 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('identifiant_'.$suffixe))
            $aData['identifiant_'.$suffixe]= array('value'=> $seance['Seance']['id'], 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('commentaire_'.$suffixe))
            $aData['commentaire_'.$suffixe]= array('value'=> $seance['Seance']['commentaire'], 'type'=>'text');
        if ($modelOdtInfos->hasUserFieldDeclared('type_'.$suffixe))
            $aData['type_'.$suffixe]= array('value'=> $this->Typeseance->field('libelle', array('id' => $seance['Seance']['type_id'])), 'type'=>'text');

        // président de séance
        if (!empty($seance['Seance']['president_id'])){
            $this->President->setVariablesFusion($aData, $modelOdtInfos, $seance['Seance']['president_id']);
        }
        // secrétaire de séance
        if (!empty($seance['Seance']['secretaire_id']))
            $this->Secretaire->setVariablesFusion($aData, $modelOdtInfos, $seance['Seance']['secretaire_id']);
        // Informations supplémentaires
        $this->Infosup->setVariablesFusion($aData, $modelOdtInfos, 'Seance', $id);

        // acteurs convoqués
        if ($modelOdtInfos->hasUserFieldsDeclared(
                'salutation_acteur_convoque', 'prenom_acteur_convoque', 'nom_acteur_convoque', 'titre_acteur_convoque', 'position_acteur_convoque',
                'email_acteur_convoque', 'telmobile_acteur_convoque', 'telfixe_acteur_convoque', 'date_naissance_acteur_convoque',
                'adresse1_acteur_convoque', 'adresse2_acteur_convoque', 'cp_acteur_convoque', 'ville_acteur_convoque', 'note_acteur_convoque')) {
            $convoques = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], null, array('id'));
            $aSeance['nombre_acteur_'.$suffixe]=count($convoques);//, "text"));
            if (!empty($convoques)) {
                $aConvoques=array();
                foreach($convoques as $convoque) {
                    $aConvoques[]=$this->Secretaire->setVariablesFusion($aData['Convoques'],$modelOdtInfos, $convoque['Acteur']['id'], 'acteur_convoque');
                }
            }
        }
        
        if ($modelOdtInfos->hasUserFieldDeclared('debat_'.$suffixe)){
            $debat=$this->findById($id,'debat_global');
            if (!empty($debat['Seance']['debat_global'])) {
                $aData['fileodt.debat_seance']=$debat['Seance']['debat_global'];
            }
        }
    //    debug($aData);
        // projets/délibérations de la séance
        if ($addProjetIterations) {
            $aProjetIds=$this->getDeliberationsId($id);
            if (!empty($aProjetIds)) {
                foreach($aProjetIds as $iProjetId) {
                    $this->Deliberation->setVariablesFusion($aData['Projets'][], $modelOdtInfos, $iProjetId, $id);
                }
            }
        }
    }

    /**
     * fonction de callback du behavior OdtFusion
     * initialise les variables de fusion Gedooo
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id de l'occurence en base de données
     * @param array modelOptions options gérées par la classe appelante
     * @return void
     */
    function beforeFusion(&$aData, &$modelOdtInfos, $id, $modelOptions) {
        switch($modelOptions['modelTypeName']) {
            case 'convocation' :
            case 'ordredujour' :
            case 'pvsommaire' :
            case 'pvdetaille' :
                if (!empty($modelOptions['acteurId']))
                    $this->Secretaire->setVariablesFusion($aData, $modelOdtInfos, $modelOptions['acteurId'], $suffixe='acteur');
                    $this->setVariablesFusion($aData, $modelOdtInfos, $id, 'seance', true);
                break;
            case 'multiseances' :
                $this->setVariablesFusionSeances($aData, $modelOdtInfos, $modelOptions['seanceIds']);
                break;
        }
    }

    /**
     * fonction de callback du behavior OdtFusion
     * retourne l'id du model odt à utiliser pour la fusion
     * @param integer $id id de l'occurence en base de données
     * @param array modelOptions options gérées par la classe appelante
     * @return integer id du modele odt à utiliser
     * @throws Exception
     */
    function getModelTemplateId($id, $modelOptions) {
        // initialisation
        $allowedModelTypes = array('projet', 'deliberation', 'convocation', 'ordredujour', 'pvsommaire', 'pvdetaille');
        if (!in_array($modelOptions['modelTypeName'], $allowedModelTypes))
            throw new Exception('le type de modèle d\'édition '.$modelOptions['modelTypeName'].' n\'est par autorisé');

        // lecture de la séance en base de données
        $typeSeanceId = $this->field('type_id', array('id'=>$id));
        if (empty($typeSeanceId))
            throw new Exception('détermination du type de séance de la séance id:'.$id.' non trouvée');

        // lecture du modele_id liée au type de séance et au type du model d'édition
        $modelTemplateId = $this->Typeseance->field('model'.$modelOptions['modelTypeName'].'_id', array('id'=>$typeSeanceId));
        if (empty($modelTemplateId))
            throw new Exception('détermination du modèle d\'édition '.$modelOptions['modelTypeName'].' pour le type de séance id:'.$typeSeanceId.' non trouvé');
        return $modelTemplateId;
    }

    /**
     * Ordonne la fusion et retourne le résultat sous forme de flux
     * @param int|string $id identifiant de la séance
     * @param string $modeltype type de fusion
     * @param int|string $modelTemplateId
     * @param string $format format du fichier de sortie
     * @return string flux du fichier généré
     */
    public function fusion($id, $modeltype, $modelTemplateId = null, $format = 'pdf') {
        $this->Behaviors->load('OdtFusion', array(
            'id' => $id,
            'fileNameSuffixe' => $id,
            'modelTemplateId' => $modelTemplateId,
            'modelOptions' => array('modelTypeName' => $modeltype)
        ));
        $this->odtFusion();
        $content = $this->getOdtFusionResult($format);
        $this->deleteOdtFusionResult();
        $this->Behaviors->unload('OdtFusion');
        return $content;
    }

    /**
     * Ordonne la fusion et retourne le résultat sous forme de flux
     * @param int|string $id identifiant de la séance
     * @param string $modeltype type de fusion
     * @param int|string $modelTemplateId
     * @param string $outputdir fichier vers lequel faire la fusion
     * @param string $format format du fichier de sortie
     * @return array [filename => content]
     */
    public function fusionToFile($id, $modeltype, $modelTemplateId = null, $outputdir = TMP, $format = 'pdf') {
        $this->Behaviors->load('OdtFusion', array(
            'id' => $id,
            'fileNameSuffixe' => $id,
            'modelTemplateId' => $modelTemplateId,
            'modelOptions' => array('modelTypeName' => $modeltype)
        ));
        $filename = $this->fusionName();
        $this->odtFusion();
        $content = $this->getOdtFusionResult($format);
        $this->deleteOdtFusionResult();
        $this->Behaviors->unload('OdtFusion');
        $file = new File($outputdir . DS . $filename . '.' . $format, true);
        $file->write($content);
        return $file->path;
    }
}
