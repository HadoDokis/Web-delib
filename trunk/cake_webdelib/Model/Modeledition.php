<?php
class Modeledition extends AppModel {
    public $name = 'Modeledition';
//    public $actsAs = array('Containable');
//    public $hasOne = 'ModelValidator.Modeltype';
    public $displayField = 'modele';
    //Validations
    public $validate = array(
		'modele' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Veuillez attribuer un nom au modèle.'
			)
		)
	);
}
