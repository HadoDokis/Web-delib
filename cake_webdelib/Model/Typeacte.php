<?php

class Typeacte extends AppModel
{

    public $name = 'Typeacte';
    public $displayField = 'libelle';
    public $belongsTo = array(
        'Compteur' => array(
            'className' => 'Compteur',
            'foreignKey' => 'compteur_id'),
        'Nature' => array(
            'className' => 'Nature',
            'foreignKey' => 'nature_id'),
        'Modelprojet' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'conditions' => array('Modelprojet.modeltype_id' => array(MODEL_TYPE_TOUTES, MODEL_TYPE_PROJET)),
            'foreignKey' => 'modeleprojet_id'),
        'Modeldeliberation' => array(
            'className' => 'ModelOdtValidator.Modeltemplate',
            'conditions' => array('Modelprojet.modeltype_id' => array(MODEL_TYPE_TOUTES, MODEL_TYPE_DELIBERATION)),
            'foreignKey' => 'modelefinal_id'),
    );
    public $hasMany = array('Deliberation');


    public $validate = array(
        'libelle' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le libellé.'
            ),
            array(
                'rule' => 'isUnique',
                'message' => 'Entrez un autre libellé, celui-ci est déjà utilisé.'
            )
        ),
        'compteur_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner un compteur.'
            )
        ),
        'modelprojet_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle de la projet.'
            )
        ),
        'modeldeliberation_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionner le modèle de délibération.'
            )
        ),
        'nature' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Selectionnez au moins une nature.'
            )
        ),
        'gabarit_projet_upload' => array(
            'rule' => array('checkFormat','odt', false),
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Le gabarit doit être au format ODT.'
        ),
        'gabarit_synthese_upload' => array(
            'rule' => array('checkFormat','odt', false),
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Le gabarit doit être au format ODT.'
        ),
        'gabarit_acte_upload' => array(
            'rule' => array('checkFormat','odt', false),
            'required' => false,
            'allowEmpty' => true,
            'message' => 'Le gabarit doit être au format ODT.'
        ),
        'teletransmettre' => array(
            'rule' => array('boolean'),
            'message' => 'Valeur incorrecte pour l\'attribut "Télétransmettre".'
        )
    );

    public function getLibelle($type_id)
    {
        $libelle = $this->find('first', array(
            'conditions' => array('Typeacte.id' => $type_id),
            'recursive' => -1,
            'fields' => array('Typeacte.libelle')));
        return $libelle['Typeacte']['libelle'];
    }

    public function getModelId($type_id, $field)
    {
        $libelle = $this->find('first', array(
            'conditions' => array('Typeacte.id' => $type_id),
            'recursive' => -1,
            'fields' => array($field)));
        return $libelle['Typeacte'][$field];
    }

    public function getIdDesNaturesDelib()
    {
        $natures = $this->Nature->find('all', array(
            'conditions' => array('Nature.code' => 'DE'),
            'fields' => array('Nature.id'),
            'recursive' => -1));
        $typeactes = $this->find('all', array(
            'conditions' => array('Typeacte.nature_id' => Set::extract('/Nature/id', $natures)),
            'fields' => array('Typeacte.id'),
            'recursive' => -1));
        return Set::extract('/Typeacte/id', $typeactes);
    }

    /**
     * Test la possibilité de supprimer un type d'acte (le type est il affécté à un acte ?)
     * @param integer $id identifiant du type d'acte à éliminer
     * @return boolean true si aucun acte n'est associée à ce type d'acte, false sinon
     */
    public function isDeletable($id)
    {
        $nbSeancesEnCours = $this->Deliberation->find('count', array(
            'conditions' => array('typeacte_id' => $id)
        ));
        return empty($nbSeancesEnCours);
    }

}