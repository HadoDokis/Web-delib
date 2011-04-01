<?php
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
		),/*
		'retard' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Insérer un chiffre pour le nombre de jours avant retard.'
			)
		),*/
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
  var $hasMany = array('TypeseancesNature');

  var $belongsTo = array(
    'Compteur' => array(
      'className'  => 'Compteur',
      'foreignKey' => 'compteur_id'),
    'Modelprojet' => array(
      'className'  => 'Model',
      'foreignKey' => 'modelprojet_id'),
    'Modeldeliberation' => array(
     'className'  => 'Model',
     'foreignKey' => 'modeldeliberation_id'),
    'Modelconvocation' => array(
      'className'  => 'Model',
      'foreignKey' => 'modelconvocation_id'),
    'Modelordredujour' => array(
      'className'  => 'Model',
      'foreignKey' => 'modelordredujour_id'),
    'Modelpvsommaire' => array(
      'className'  => 'Model',
      'foreignKey' => 'modelpvsommaire_id'),
    'Modelpvdetaille' => array(
      'className'  => 'Model',
      'foreignKey' => 'modelpvdetaille_id')
  );

  var $hasAndBelongsToMany = array(
    'Typeacteur' => array(
      'classname'=>'Typeacteur',
      'joinTable'=>'typeseances_typeacteurs',
      'foreignKey'=>'typeseance_id',
      'associationForeignKey'=>'typeacteur_id',
      'conditions'=>'',
      'order'=>'',
      'limit'=>'',
      'unique'=>true,
      'finderQuery'=>'',
      'deleteQuery'=>''),
    'Acteur' => array(
      'classname'=>'Acteur',
      'joinTable'=>'typeseances_acteurs',
      'foreignKey'=>'typeseance_id',
      'associationForeignKey'=>'acteur_id',
      'conditions'=>'',
      'order'=>'',
      'limit'=>'',
      'unique'=>true,
      'finderQuery'=>'',
      'deleteQuery'=>''),
    'Nature' => array(
        'classname'=>'Nature',
        'joinTable'=>'typeseances_natures',
        'foreignKey'=>'typeseance_id',
        'associationForeignKey'=>'nature_id',
        'conditions'=>'',
        'order'=>'',
        'limit'=>'',
        'unique'=>true,
        'finderQuery'=>'',
        'deleteQuery'=>'')
    );


	/* retourne le libellé correspondant au champ action 0 : voter, 1 donner un avis , 2 sans action*/
	function libelleAction($action = null, $majuscule = false) {
		switch ($action) {
		case 0:
    		return $majuscule ? 'Voter':'voter';
    		break;
		case 1:
    		return ($majuscule ? 'D':'d') . 'onner un avis';
    		break;
		case 2:
    		return ($majuscule ? 'S':'s') . 'ans action';
    		break;
              
		}
	}

	/* retourne un tableau d'acteurs correspondant a la liste des convocations */
	/* pour le type de seance $typeseance_id ordonnée par position et nom      */
	/* selon le paramètre $elu on retourne les acteurs suivants :              */
	/* - null : tous les acteurs élus et non élus                              */
	/* - true : les acteurs élus                                               */
	/* - false : les acteurs non élus                                          */
	function acteursConvoquesParTypeSeanceId($typeseance_id = null, $elu = null) {
		$typeseance = $this->find('first', array('conditions' => array('Typeseance.id'=> $typeseance_id),
                                                          'fields'    => array ('id'))) ;
		if (empty($typeseance)) return null;

		/* Par type d'acteur */
		$inTypeacteur = '';
                $preCond = "Acteur.actif = 1";
		foreach($typeseance['Typeacteur'] as $typeacteur)
			$inTypeacteur .= ($inTypeacteur ? ', ' : '') . $typeacteur['id'];
		/* Par acteur */
		$inId = '';
		foreach($typeseance['Acteur'] as $acteur)
			$inId .= ($inId ? ', ' : '') . $acteur['id'];

		$condIn = ($inTypeacteur ? 'Acteur.typeacteur_id in ('.$inTypeacteur.')' : '').
				(($inTypeacteur && $inId) ? ' or ' : '').
				($inId ? 'Acteur.id in ('.$inId.')' : '');
		$condElu = isset($elu) ? ('Typeacteur.elu=' . ($elu ? '1':'0')) : '';
		$condition = ($condElu ? '(':'') . $condIn . ($condElu ? ') and ':'') . $condElu;
                if (empty($condition))
                    $condition =  $preCond;
                else
                     $condition =  $preCond ." AND ( ". $condition." )";

		return ($this->Acteur->find('all', array ('conditions' => $condition,
                                                         'order' => 'Acteur.position, Acteur.nom ASC')));
                  
	}

	/* retourne d'id du modèle d 'édition du type de séance $typeseance_id en sonction de l'état du projet de délibération */
	function modeleProjetDelibParTypeSeanceId($typeseance_id, $etat) {
		$typeseance = $this->find("id = $typeseance_id", 'modelprojet_id, modeldeliberation_id', null, -1);
		if ($etat==3 || $etat==5)
			return $typeseance['Typeseance']['modeldeliberation_id'];
		else
			return $typeseance['Typeseance']['modelprojet_id'];
	}

	function getLibelle ($type_id) {
              $libelle = $this->read('libelle', $type_id);
              return $libelle['Typeseance']['libelle'];
	}

}
?>
