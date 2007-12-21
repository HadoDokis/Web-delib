<?php
class Seance extends AppModel {

	var $name = 'Seance';
	var	$cacheQueries = false;
	var $validate = array(
		'type_id' => VALID_NOT_EMPTY,
	);

	var $displayField="libelle";
	var $belongsTo=array('Typeseance'=>array('className'=>'Typeseance',
											'conditions'=>'',
											'order'=>'',
											'dependent'=>false,
											'foreignKey'=>'type_id'));

	var $hasMany = array ('SeancesUser' => array('className'=>'SeancesUser'));
}
?>