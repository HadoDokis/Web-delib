<?php

App::uses('Seance', 'Model');
class Typeseance extends AppModel {

    var $name = 'Typeseance';
    var $displayField = 'libelle';
    var $validate = array(
        'libelle' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le libellé.'
            ),
            array(
                'rule' => 'isUnique',
                'message' => 'Entrez un autre libellé, celui-ci est déjà utilisé.'
            )
        ), /*
          'retard' => array(
          array(
          'rule' => 'notEmpty',
          'message' => 'Insérer un chiffre pour le nombre de jours avant retard.'
          )
          ), */
        'action' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner une action'
            )
        ),
        'compteur_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner un compteur'
            )
        ),
        'modelprojet_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle de la projet'
            )
        ),
        'modeldeliberation_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle de délibération'
            )
        ),
        'modelconvocation_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle de la convocation'
            )
        ),
        'modelordredujour_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle de l\'ordre du jour'
            )
        ),
        'modelpvsommaire_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle du PV sommaire'
            )
        ),
        'modelpvdetaille_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Selectionner le modèle du PV détaillé'
            )
        ),
        'typeacteur' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Selectionnez au moins un type d\'acteur ou au moins un acteur'
            )
        ),
        'nature' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Selectionnez au moins une nature'
            )
        )
    );
    var $hasMany = array('TypeseancesTypeacte');
    var $belongsTo = array(
        'Compteur' => array(
            'className' => 'Compteur',
            'foreignKey' => 'compteur_id'),
        'Modelprojet' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'foreignKey' => 'modelprojet_id'),
        'Modeldeliberation' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'foreignKey' => 'modeldeliberation_id'),
        'Modelconvocation' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'foreignKey' => 'modelconvocation_id'),
        'Modelordredujour' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'foreignKey' => 'modelordredujour_id'),
        'Modelpvsommaire' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'foreignKey' => 'modelpvsommaire_id'),
        'Modelpvdetaille' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'foreignKey' => 'modelpvdetaille_id')
    );
    var $hasAndBelongsToMany = array(
        'Typeacteur' => array(
            'classname' => 'Typeacteur',
            'joinTable' => 'typeseances_typeacteurs',
            'foreignKey' => 'typeseance_id',
            'associationForeignKey' => 'typeacteur_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => ''),
        'Acteur' => array(
            'classname' => 'Acteur',
            'joinTable' => 'typeseances_acteurs',
            'foreignKey' => 'typeseance_id',
            'associationForeignKey' => 'acteur_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => ''),
        'Typeacte' => array(
            'classname' => 'Typeacte',
            'joinTable' => 'typeseances_typeactes',
            'foreignKey' => 'typeseance_id',
            'associationForeignKey' => 'typeacte_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => '')
    );
    
    public function beforeFind($query = array()) {
        $query['conditions'] = (is_array($query['conditions'])) ? $query['conditions'] : array();
        $db = $this->getDataSource();
        
        //Gestion des droits sur les types d'actes
        if (!empty($query['allow']) && in_array('Typeseance.id', $query['allow']))
        {
            $Aro = ClassRegistry::init('Aro');

            $Aro->Behaviors->attach( 'DatabaseTable' );
            $Aro->Permission->Behaviors->attach( 'DatabaseTable' );
            $Aro->Permission->Aco->Behaviors->attach( 'DatabaseTable' );
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
            $subQuery=$Aro->sq($subQuery);
            $subQuery = ' "Typeseance"."id" IN (' . $subQuery . ') ';
            $subQueryExpression = $db->expression($subQuery);
            $conditions[] = $subQueryExpression;
            
            $query['conditions'] = array_merge($query['conditions'], $conditions);
        }
        
        
        return $query;
    }

    /**
     * retourne le libellé correspondant au champ action 0 : voter, 1 donner un avis , 2 sans action
     */
    function libelleAction($action = null, $majuscule = false) {
        switch ($action) {
            case 0:
                return $majuscule ? 'Voter' : 'voter';
            case 1:
                return ($majuscule ? 'D' : 'd') . 'onner un avis';
            case 2:
                return ($majuscule ? 'S' : 's') . 'ans action';
        }
    }

    /* retourne un tableau d'acteurs correspondant a la liste des convocations */
    /* pour le type de seance $typeseance_id ordonnée par position et nom      */
    /* selon le paramètre $elu on retourne les acteurs suivants :              */
    /* - null : tous les acteurs élus et non élus                              */
    /* - true : les acteurs élus                                               */
    /* - false : les acteurs non élus                                          */

    function acteursConvoquesParTypeSeanceId($typeseance_id, $elu = null, $fields=array()) {

        $typeseance = $this->find('first', array(
            'contain' => array('Typeacteur.id','Acteur.id'),
            'fields' => array('id'),
            'conditions' => array('Typeseance.id' => $typeseance_id)));
        if (empty($typeseance)) {
            return null;
        }

        /* Par type d'acteur */
        $inTypeacteur = array();
        foreach ($typeseance['Typeacteur'] as $typeacteur) {
            $inTypeacteur[] = $typeacteur['id'];
        }

        /* Par acteur */
        $inId = array();
        foreach ($typeseance['Acteur'] as $acteur) {
            $inId[] = $acteur['id'];
        }

        $condition['Acteur.actif'] = 1;
        if ($elu !== null) {
            $condition['Typeacteur.elu'] = $elu;
        }
        if (!empty($inTypeacteur) && !empty($inId)) {
            $condition['OR']['Acteur.id'] = $inId;
            $condition['OR']['Acteur.typeacteur_id'] = $inTypeacteur;
        } elseif (!empty($inTypeacteur)) {
            $condition['Acteur.typeacteur_id'] = $inTypeacteur;
        } elseif (!empty($inId)) {
            $condition['Acteur.id'] = $inId;
        }

        return $this->Acteur->find('all', array(
            'recursive' => 0,
            'fields' => $fields,
            'order' => array('Acteur.position ASC'),
            'conditions' => $condition));
    }

    /* retourne d'id du modèle d 'édition du type de séance $typeseance_id en sonction de l'état du projet de délibération */

    function modeleProjetDelibParTypeSeanceId($typeseance_id, $etat) {
        $typeseance = $this->find('first', array(
            'conditions' => array('Typeseance.id' => $typeseance_id),
            'fields' => array('modelprojet_id', 'modeldeliberation_id'),
            'recursive' => -1));
        if ($etat >= 3) {
            return $typeseance['Typeseance']['modeldeliberation_id'];
        } else {
            return $typeseance['Typeseance']['modelprojet_id'];
        }
    }

    function getLibelle($type_id) {
        $libelle = $this->read('libelle', $type_id);
        return $libelle['Typeseance']['libelle'];
    }

    /**
     * Test la possibilité de supprimer un type de séance (le type est il affécté à une séance ?)
     * @param integer $id identifiant du type séance à éliminer
     * @return boolean true si aucune séance n'est associée à ce type de séance, false sinon
     */
    function isDeletable($id) {
        $this->Seance = new Seance();
        $nbSeancesEnCours = $this->Seance->find('count', array(
            'conditions' => array('type_id' => $id)
        ));
        return empty($nbSeancesEnCours);
    }

}