<?php
class Typeacte extends AppModel {

	var $name = 'Typeacte';

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
			'nature' => array(
					array(
							'rule' => 'notEmpty',
							'message' => 'Selectionnez au moins une nature'
					)
			)
	);

	var $belongsTo = array(
			'Compteur' => array(
					'className'  => 'Compteur',
					'foreignKey' => 'compteur_id'),
                        'Nature' => array(
                                        'className'  => 'Nature',
                                        'foreignKey' => 'nature_id'),
			'Modelprojet' => array(
					'className'  => 'Model',
					'foreignKey' => 'modeleprojet_id'),
			'Modeldeliberation' => array(
					'className'  => 'Model',
					'foreignKey' => 'modelefinal_id'),
	);

       var $hasMany = array('Deliberation');

	function getLibelle ($type_id) {
		$libelle = $this->find('first', array('conditions' => array('Typeacte.id' => $type_id),
                                                      'recursive'  => -1,
                                                      'fields'     => array('Typeacte.libelle')));
		return $libelle['Typeacte']['libelle'];
	}

        function getModelId ($type_id, $field) {
            $libelle = $this->find('first', array('conditions' => array('Typeacte.id' => $type_id),
                                                  'recursive'  => -1,
                                                  'fields'     => array($field)));
            return $libelle['Typeacte'][$field];
        }
 
        function getIdDesNaturesDelib() {
            $natures = $this->Nature->find('all', array('conditions' => array('Nature.code' => 'DE'), 
                                                        'fields'     => array('Nature.id'),
                                                        'recursive'  => -1)); 
            $typeactes = $this->find('all', array('conditions' => array('Typeacte.nature_id' => Set::extract('/Nature/id', $natures)),
                                                  'fields'     => array('Typeacte.id'), 
                                                  'recursive'  => -1));
            return Set::extract('/Typeacte/id', $typeactes);
        }
}
?>
