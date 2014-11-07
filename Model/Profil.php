<?php
class Profil extends AppModel {

	var $name = 'Profil';
	var $displayField="libelle";
	var $validate = array (
		'libelle' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libellé.'
			)
		)
	);

	var $belongsTo = array
	(
    	'ProfilParent' => array
    	(
    		'className' => 'Profil',
            'foreignKey' => 'parent_id'
        ),
     );

 	var $hasMany = array
 	(
 		'ProfilEnfant' => array
 		(
 			'className' => 'Profil',
 			'foreignKey' => 'parent_id'
        ),
 		'User' => array
 		(
 			'className' => 'User',
 			'foreignKey' => 'profil_id'
        )
    );

        var $hasAndBelongsToMany = array('Infosupdef');
}
?>
