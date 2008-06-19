<?php
class Typeseance extends AppModel {

  var $name = 'Typeseance';

  var $displayField = 'libelle';

  var $validate = array(
    'libelle' => VALID_NOT_EMPTY,
    'action' => VALID_NOT_EMPTY,
    'compteur_id' => VALID_NOT_EMPTY,
    'modelconvocation_id' => VALID_NOT_EMPTY,
    'modelordredujour_id' => VALID_NOT_EMPTY,
    'modelpvsommaire_id' => VALID_NOT_EMPTY,
    'modelpvdetaille_id' => VALID_NOT_EMPTY
  );

  var $belongsTo = array(
    'Compteur' => array(
      'className'  => 'Compteur',
      'foreignKey' => 'compteur_id'),
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
      'deleteQuery'=>'')
    );


	function validates() {
		// unicité du libelle
		$this->isUnique('libelle', $this->data['Typeseance']['libelle'], $this->data['Typeseance']['id']);

		// teste la présence d'au moins un type d'acteur ou d'un acteur pour les convocations
		if ( (!array_key_exists('Typeacteur', $this->data) || empty($this->data['Typeacteur']['Typeacteur'][0]))
		&& (!array_key_exists('Acteur', $this->data) || empty($this->data['Acteur']['Acteur'][0])) )
			$this->invalidate('typeacteur');

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

	/* retourne le libellé correspondant au champ action 0 : voter, 1 donner un avis */
	function libelleAction($action = null, $majuscule = false) {
		switch ($action) {
		case 0:
    		return $majuscule ? 'Voter':'voter';
    		break;
		case 1:
    		return ($majuscule ? 'D':'d') . 'onner un avis';
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
		$typeseance = $this->read('id', $typeseance_id);
		if (empty($typeseance)) return null;

		/* Par type d'acteur */
		$inTypeacteur = '';
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

		return $this->Acteur->findAll($condition, null, 'Acteur.position, Acteur.nom ASC', null, 1, 0);
	}

}
?>
