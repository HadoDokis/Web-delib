<?php
	class Service extends AppModel {

		var $name = 'Service';
	
		var $displayField="libelle";
	
		var $actsAs = array('Tree');
	
		var $validate = array(
			'libelle' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Entrer le libellé.'
				)
			)
		);

		var $hasAndBelongsToMany = array(
			'User' => array(
				'classname'=>'User',
				'joinTable'=>'users_services',
				'foreignKey'=>'service_id',
				'associationForeignKey'=>'user_id',
				'conditions'=>'',
				'order'=>'',
				'limit'=>'',
				'unique'=>true,
				'finderQuery'=>'',
				'deleteQuery'=>''),
			'Acteur' => array(
				'classname'=>'Acteur',
				'joinTable'=>'acteurs_services',
				'foreignKey'=>'service_id',
				'associationForeignKey'=>'acteur_id',
				'conditions'=>'',
				'order'=>'',
				'limit'=>'',
				'unique'=>true,
				'finderQuery'=>'',
				'deleteQuery'=>'')
		);

		/* retourne le libelle du service $id et de ses parents sous la forme parent1/parent12/service_id */
		function doList($id) {
			return $this->_doList($id);
		}

		/* fonction récursive de doList */
		function _doList($id) {
			$service = $this->find("id = $id", 'libelle, parent_id', null, -1);
			if (!Configure::read('AFFICHE_HIERARCHIE_SERVICE'))
				return $service['Service']['libelle'];

			if (empty($service['Service']['parent_id']))
				return $service['Service']['libelle'];
			else
				return $this->_doList($service['Service']['parent_id']). '/'. $service['Service']['libelle'];
		}

	}
?>
