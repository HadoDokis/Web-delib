<?php
class Seance extends AppModel
{

    public $name = 'Seance';
    public $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    public $months = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

    public $validate = array(
        'type_id' => array(array('rule' => 'notEmpty',
            'message' => 'Entrer le type de seance associé.')),
        'avis' => array(array('rule' => 'notEmpty',
            'message' => 'Sélectionner un avis')),
        'date' => array(array('rule' => 'notEmpty',
            'message' => 'Entrer une date valide.')),
        'commission' => array(array('rule' => 'notEmpty',
            'message' => 'Entrer le texte de débat.')),
        'debat_global_upload' => array(array('rule' => array('checkFormat','odt', false),
            'message' => "Ce type de fichier n'est pas autorisé")),
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

    /**
     * retourne la liste des séances futures avec le nom du type de séance
     */
    function generateList($conditionSup = null, $afficherTtesLesSeances = false, $natures = array())
    {
        $generateList = array();
        App::import('model', 'TypeseancesTypeacte');
        $TypeseancesTypeacte = new TypeseancesTypeacte();
        $typeseances = $TypeseancesTypeacte->getTypeseanceParNature($natures);

        $conditions = array();
        $conditions['Seance.type_id'] = $typeseances;
        $conditions['Seance.traitee'] = '0';

        if (!empty($conditionSup))
            $conditions = Set::pushDiff($conditions, $conditionSup);

        $this->Behaviors->attach('Containable');
        $seances = $this->find('all', array('conditions' => $conditions,
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
        $this->Behaviors->attach('Containable');
        $generateList = array();
        $seances = $this->find('all', array('order' => 'date ASC',
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

    function getDeliberations($seance_id, $conditions = array())
    {
        $conditions['Deliberationseance.seance_id'] = $seance_id;

        $deliberations = $this->Deliberationseance->find(
            'all',
            array(
                'fields' => array(
                    'Deliberationseance.seance_id',
                    'Deliberationseance.deliberation_id',
                    'Deliberationseance.position',
                    'Deliberation.*',
                ),
                'contain' => array(
                    'Deliberation'=>array(
                        'Typeacte.nature_id','Typeacte.libelle',
                        'Service.libelle',
                        'Theme.libelle',
                        'Circuit.nom'
                    ),
                ),
                'recursive' => 2,
                'conditions' => $conditions,
                'order' => 'Deliberationseance.position ASC',
            )
        );
        for ($i = 0; $i < count($deliberations); $i++) {
            if (isset($deliberations[$i]['Deliberation']['theme_id'])) {
                $theme = $this->Deliberation->Theme->find('first',
                    array('conditions' => array('Theme.id' => $deliberations[$i]['Deliberation']['theme_id']),
                        'fields' => array('Theme.id', 'Theme.libelle'),
                        'recursive' => -1));
                $deliberations[$i]['Theme']['libelle'] = $theme['Theme']['libelle'];
            }
            if (isset ($deliberations[$i]['Deliberation']['rapporteur_id'])) {
                $rapporteur = $this->Deliberation->Rapporteur->find('first',
                    array('conditions' => array('Rapporteur.id' => $deliberations[$i]['Deliberation']['rapporteur_id']),
                        'fields' => array('Rapporteur.id', 'Rapporteur.nom', 'Rapporteur.prenom'),
                        'recursive' => -1));
                $deliberations[$i]['Rapporteur']['nom'] = $rapporteur['Rapporteur']['nom'];
                $deliberations[$i]['Rapporteur']['prenom'] = $rapporteur['Rapporteur']['prenom'];
            }
        }
        return ($deliberations);
    }

    function getDeliberationsId($seance_id)
    {
        $tab = array();
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();

        $this->Deliberationseance->Behaviors->load('Containable');
        $deliberations = $this->Deliberationseance->find('all', array(
            'conditions' => array('Deliberationseance.seance_id' => $seance_id, 'Deliberation.etat >=' => 0),
            'fields' => array('Deliberationseance.deliberation_id'),
            'contain' => array('Deliberation.id', 'Deliberation.etat', 'Seance.id'),
            'order' => 'Deliberationseance.position ASC',
        ));
        foreach ($deliberations as $deliberation)
            $tab[] = $deliberation['Deliberationseance']['deliberation_id'];
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
        if (is_int($seances_selectionnees)) {
            $seance_id = $seances_selectionnees;
            unset($seances_selectionnees);
            $seances_selectionnees = array();
            $seances_selectionnees[0] = $seance_id;
        } elseif (!isset($seances_selectionnees) || empty($seances_selectionnees) || ($seances_selectionnees[0] == null))
            $seances_selectionnees = array();

        $seances_enregistrees = array();

        $delibs = $this->Deliberationseance->find('all', array(
            'conditions' => array('Deliberation.id' => $delib_id),
            'fields' => array('Seance.id',
                'Deliberationseance.deliberation_id', 'Deliberationseance.id',
                'Deliberationseance.position', 'Deliberationseance.seance_id'),
            'order' => array('Deliberationseance.position ASC')));
        foreach ($delibs as $delib)
            $seances_enregistrees[] = $delib['Seance']['id'];

        $seances_a_retirer = array_diff($seances_enregistrees, $seances_selectionnees);

        foreach ($seances_a_retirer as $seance_id) {
            $this->Deliberationseance->deleteDeliberationseance($delib_id, $seance_id);
        }

        if (is_array($seances_enregistrees) and  (!empty($seances_enregistrees))) {
            $seances_a_ajouter = array_diff($seances_selectionnees, $seances_enregistrees);
        } else
            $seances_a_ajouter = $seances_selectionnees;

        foreach ($seances_a_ajouter as $seance_id) {
            $this->Deliberationseance->addDeliberationseance($delib_id, $seance_id);
        }
    }

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

    function getSeanceDeliberante($tab_seances)
    {
        $this->Behaviors->attach('Containable');
        foreach ($tab_seances as $seance_id) {
            $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
                'fields' => array('Seance.id'),
                'contain' => array('Typeseance.action')));
            if ($seance['Typeseance']['action'] == 0) {
                return $seance['Seance']['id'];
            }
        }
        return null;
    }

    function isSeanceDeliberante($seance_id)
    {
        $this->Behaviors->attach('Containable');
        $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'fields' => array('Seance.id', 'Seance.type_id'),
            'contain' => array('Typeseance.action')));
        return ($seance['Typeseance']['action'] == 0);
    }

    function makeBalise($seance_id, $oDevPart = null, $include_projets = false, $conditions = array())
    {
        include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
        $this->Date = new DateComponent;

        $this->Behaviors->attach('Containable');
        $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'contain' => array('Deliberation.id')));

        $return = false;
        if ($oDevPart == null) {
            $oDevPart = new GDO_PartType();
            $return = true;
            $suffixe = "_seances";
        } else {
            $suffixe = "_seance";
        }
        if (!empty($seance['Seance']['date']))
            $date_lettres = $this->Date->dateLettres(strtotime($seance['Seance']['date']));
        else $date_lettres = '';
        //$oDevPart->addElement(new GDO_FieldType('date_seance_lettres'.$suffixe, ($date_lettres), 'text'));
        $oDevPart->addElement(new GDO_FieldType('date_seance_lettres', $date_lettres, 'text'));
        $oDevPart->addElement(new GDO_FieldType("heure" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->Hour($seance['Seance']['date']) : ''), 'text'));
        $oDevPart->addElement(new GDO_FieldType("date" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->frDate($seance['Seance']['date']) : ''), 'date'));
        $oDevPart->addElement(new GDO_FieldType("hh" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->Hour($seance['Seance']['date'], 'hh') : ''), 'string'));
        $oDevPart->addElement(new GDO_FieldType("mm" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->Hour($seance['Seance']['date'], 'mm') : ''), 'string'));
        $oDevPart->addElement(new GDO_FieldType("date_convocation" . $suffixe, $this->Date->frDate($seance['Seance']['date_convocation']), 'date'));
        $oDevPart->addElement(new GDO_FieldType("identifiant" . $suffixe, (!empty($seance['Seance']['id']) ? $seance['Seance']['id'] : ''), 'text'));
        $oDevPart->addElement(new GDO_FieldType("commentaire" . $suffixe, (!empty($seance['Seance']['commentaire']) ? $seance['Seance']['commentaire'] : ''), 'lines'));

        $elus = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
        if (!empty($elus)) {
            $oDevPart->addElement(new GDO_FieldType("nombre_acteur" . $suffixe, count($elus), "text"));
            $blocConvoque = new GDO_IterationType("Convoques");
            foreach ($elus as $elu) {
                $oDevPartConvoque = new GDO_PartType();
                $oDevPartConvoque->addElement(new GDO_FieldType("nom_acteur_convoque" . $suffixe, ($elu['Acteur']['nom']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("prenom_acteur_convoque" . $suffixe, ($elu['Acteur']['prenom']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("salutation_acteur_convoque" . $suffixe, ($elu['Acteur']['salutation']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("titre_acteur_convoque" . $suffixe, ($elu['Acteur']['titre']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("note_acteur_convoque" . $suffixe, ($elu['Acteur']['note']), "text"));
                $blocConvoque->addPart($oDevPartConvoque);
            }
            $oDevPart->addElement($blocConvoque);
        }

        $this->President->makeBalise($oDevPart, $seance['Seance']['president_id']);
        $this->Secretaire->makeBalise($oDevPart, $seance['Seance']['secretaire_id']);
                
        $typeSeance = $this->Typeseance->find('first',
            array('conditions' => array('Typeseance.id' => $seance['Seance']['type_id']),
                'recursive' => -1));
        $oDevPart->addElement(new GDO_FieldType('type' . $suffixe, (!empty($typeSeance['Typeseance']['libelle']) ? $typeSeance['Typeseance']['libelle'] : ''), 'text'));

        if (isset($infosups) && !empty($infosups)) {
            foreach ($infosups as $champs) {
                $infosup = $this->Infosup->addField($champs['Infosup'], $seance_id, 'Seance');
                if (!empty($infosup))
                    $oDevPart->addElement($infosup);
            }
        }
        /*else {
            $defs = $this->Infosup->Infosupdef->find('all', array('conditions'=>array('model' => 'Seance'), 'recursive' => -1));
            foreach($defs as $def) {
                
                $oDevPart->addElement(new GDO_FieldType($def['Infosupdef']['code'],  '', 'text')) ;
            }
        }*/
        if ($include_projets) {
            if (isset($seance['Deliberation']) && !empty($seance['Deliberation'])) {
                $blocProjets = new GDO_IterationType("Projets");
                foreach ($seance['Deliberation'] as $deliberation) {
                    $conditions['Deliberation.id'] = $deliberation['DeliberationsSeance']['deliberation_id'];
                    $projet = $this->Deliberation->find('first', array('conditions' => $conditions,
                        'recursive' => -1));
                    $Part = new GDO_PartType();
                    $this->Deliberation->makeBalisesProjet($projet, $Part, true, $seance['Seance']['id']);
                    $blocProjets->addPart($Part);
                }
                $oDevPart->addElement($blocProjets);
            }
        }

        if ($return) return $oDevPart;
    }

    function getSeancesDeliberantes()
    {
        $tab_seances = array();
        $this->Behaviors->attach('Containable');
        $seances = $this->find('all', array('conditions' => array('Typeseance.action' => 0,
            'Seance.traitee' => 0),
            'fields' => array('Seance.id'),
            'contain' => array('Typeseance.action')));
        if (isset($seances) && (!empty($seances)))
            foreach ($seances as $seance)
                $tab_seances[] = $seance['Seance']['id'];
        return ($tab_seances);
    }

    /**
     * fonction d'initialisation des variables de fusion pour un projet ou une délibération
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id du projet/délibération
     */
    function setVariablesFusionPourUnProjet(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        // lectures des séances du projet ou de la délibération
        $seanceIds = $this->Deliberationseance->nfield('seance_id', array('Deliberationseance.deliberation_id'=>$deliberationId), array('Seance.date'));
        if (empty($seanceIds)) return;

        // dernière séance hors itération séances
        if ($modelOdtInfos->hasUserField('position_projet'))
            $oMainPart->addElement(new GDO_FieldType('position_projet', $this->Deliberation->getPosition($deliberationId, $seanceIds[count($seanceIds)-1]), 'text'));
        $this->setVariablesFusion($oMainPart, $modelOdtInfos, $seanceIds[count($seanceIds)-1], 'seance', false);

        // pour toutes les séances
        $oMainPart->addElement(new GDO_FieldType('nombre_seance', count($seanceIds), 'text'));
        $oSectionIteration = new GDO_IterationType("Seances");
        foreach($seanceIds as $seanceId) {
            $oDevPart = new GDO_PartType();
            $this->setVariablesFusion($oDevPart, $modelOdtInfos, $seanceId, 'seances', false);
            $oSectionIteration->addPart($oDevPart);
        }
        $oMainPart->addElement($oSectionIteration);
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
    function setVariablesFusion(&$oMainPart, &$modelOdtInfos, $id, $suffixe='seance', $addProjetIterations=true) {
        // lecture de la séance en base
        $seance = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'date', 'date_convocation', 'commentaire', 'type_id', 'president_id', 'secretaire_id'),
            'conditions' => array('id' => $id)));
        if (empty($seance))
            throw new Exception('séance  id:'.$id.' non trouvée en base de données');

        // fusion des variables
        $dateSeanceTimeStamp = strtotime($seance['Seance']['date']);
        if ($modelOdtInfos->hasUserField('date_'.$suffixe.'_lettres')) {
            include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
            $this->Date = new DateComponent;
            $oMainPart->addElement(new GDO_FieldType('date_'.$suffixe.'_lettres', $this->Date->dateLettres($dateSeanceTimeStamp), 'text'));
        }
        if ($modelOdtInfos->hasUserField('date_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('date_'.$suffixe, date('d/m/Y', $dateSeanceTimeStamp), 'text'));
        if ($modelOdtInfos->hasUserField('heure_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('heure_'.$suffixe, date('H:i', $dateSeanceTimeStamp), 'text'));
        if ($modelOdtInfos->hasUserField('hh_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('hh_'.$suffixe, date('H', $dateSeanceTimeStamp), 'text'));
        if ($modelOdtInfos->hasUserField('mm_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('mm_'.$suffixe, date('i', $dateSeanceTimeStamp), 'text'));
        if ($modelOdtInfos->hasUserField('date_convocation_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('date_convocation_'.$suffixe, (empty($seance['Seance']['date_convocation'])?'':date('d/m/Y', strtotime($seance['Seance']['date_convocation']))), 'text'));
        if ($modelOdtInfos->hasUserField('identifiant_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('identifiant_'.$suffixe, $seance['Seance']['id'], 'text'));
        if ($modelOdtInfos->hasUserField('commentaire_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('commentaire_'.$suffixe, $seance['Seance']['commentaire'], 'text'));
        if ($modelOdtInfos->hasUserField('type_'.$suffixe))
            $oMainPart->addElement(new GDO_FieldType('type_'.$suffixe, $this->Typeseance->field('libelle', array('id' => $seance['Seance']['type_id'])), 'text'));

        // président de séance
        if (!empty($seance['Seance']['president_id']))
            $this->President->setVariablesFusion($oMainPart, $modelOdtInfos, $seance['Seance']['president_id']);
        // secrétaire de séance
        if (!empty($seance['Seance']['secretaire_id']))
            $this->Secretaire->setVariablesFusion($oMainPart, $modelOdtInfos, $seance['Seance']['secretaire_id']);
        // Informations supplémentaires
        $this->Infosup->setVariablesFusion($oMainPart, $modelOdtInfos, 'Seance', $id);

        // acteurs convoqués
        if ($modelOdtInfos->hasUserFields(
                'salutation_acteur_convoque', 'prenom_acteur_convoque', 'nom_acteur_convoque', 'titre_acteur_convoque', 'position_acteur_convoque',
                'email_acteur_convoque', 'telmobile_acteur_convoque', 'telfixe_acteur_convoque', 'date_naissance_acteur_convoque',
                'adresse1_acteur_convoque', 'adresse2_acteur_convoque', 'cp_acteur_convoque', 'ville_acteur_convoque', 'note_acteur_convoque')) {
            $convoques = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], null, array('id'));
            $oMainPart->addElement(new GDO_FieldType('nombre_acteur_'.$suffixe, count($convoques), "text"));
            if (!empty($convoques)) {
                $oStyleIteration = new GDO_IterationType("Convoques");
                foreach($convoques as $convoque) {
                    $oDevPart = new GDO_PartType();
                    $this->Secretaire->setVariablesFusion($oDevPart, $modelOdtInfos, $convoque['Acteur']['id'], 'acteur_convoque');
                    $oStyleIteration->addPart($oDevPart);
                }
                $oMainPart->addElement($oStyleIteration);
            }
        }

        // projets/délibérations de la séance
        if ($addProjetIterations) {
            $this->Behaviors->load('Containable');
            $seance = $this->find('first', array(
                'fields' => array('id'),
                'contain' => array('Deliberation.id'),
                'conditions' => array('Seance.id' => $id)));
            if (!empty($seance['Deliberation'])) {
                $oSectionIteration = new GDO_IterationType("Projets");
                foreach($seance['Deliberation'] as $deliberation) {
                    $oDevPart = new GDO_PartType();
                    $this->Deliberation->setVariablesFusion($oDevPart, $modelOdtInfos, $deliberation['id'], false);
                    $oSectionIteration->addPart($oDevPart);
                }
                $oMainPart->addElement($oSectionIteration);
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
    function beforeFusion(&$oMainPart, &$modelOdtInfos, $id, $modelOptions) {
        switch($modelOptions['modelTypeName']) {
            case 'Convocation' :
                $this->Secretaire->setVariablesFusion($oMainPart, $modelOdtInfos, $modelOptions['acteurId'], $suffixe='acteur');
                $this->setVariablesFusion($oMainPart, $modelOdtInfos, $id, 'seance', true);
                break;
        }
    }

    /**
     * fonction de callback du behavior OdtFusion
     * retourne l'id du model odt à utiliser pour la fusion
     * @param integer $id id de l'occurence en base de données
     * @param array modelOptions options gérées par la classe appelante
     * @return integer id du modele odt à utiliser
     */
    function getModelTemplateId($id, $modelOptions) {
        // initialisation
        $field = '';
        $allowedModelTypeNames = array('Projet', 'Délibération', 'Convocation', 'Ordre du jour', 'PV sommaire', 'PV détaillé');
        if (!in_array($modelOptions['modelTypeName'], $allowedModelTypeNames))
            throw new Exception('le nom du type de modèle d\'édition '.$modelOptions['modelTypeName'].' n\'est par autorisé');

        // lecture de la séance en base de données
        $typeSeanceId = $this->field('type_id', array('id'=>$id));
        if (empty($typeSeanceId))
            throw new Exception('détermination du type de séance de la séance id:'.$id.' non trouvée');

        // lecture du modele_id liée au type de séance et au type du model d'édition
        if ($modelOptions['modelTypeName'] == 'Projet')
            $field = 'modelprojet_id';
        elseif ($modelOptions['modelTypeName'] == 'Délibération')
            $field = 'modeldeliberation_id';
        elseif ($modelOptions['modelTypeName'] == 'Convocation')
            $field = 'modelconvocation_id';
        elseif ($modelOptions['modelTypeName'] == 'Ordre du jour')
            $field = 'modelordredujour_id';
        elseif ($modelOptions['modelTypeName'] == 'PV sommaire')
            $field = 'modelpvsommaire_id';
        elseif ($modelOptions['modelTypeName'] == 'PV détaillé')
            $field = 'modelpvdetaille_id';
        $modelTemplateId = $this->Typeseance->field($field, array('id'=>$typeSeanceId));
        if (empty($modelTemplateId))
            throw new Exception('détermination du modèle d\'édition '.$modelOptions['modelTypeName'].' pour le type de séance id:'.$typeSeanceId.' non trouvé');
        return $modelTemplateId;
    }
}
