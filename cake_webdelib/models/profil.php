<?php
class Profil extends AppModel {

	var $name = 'Profil';
	var $displayField="libelle";
	var $validate = array
	(
		'libelle' => VALID_NOT_EMPTY,
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
    );
}
?>