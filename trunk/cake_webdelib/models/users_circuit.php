<?php
class UsersCircuit extends AppModel {

	var $name = 'UsersCircuit';
	//var $primaryKey = 'user_id';
	var $validate = array(
		'circuit_id' => VALID_NOT_EMPTY,
		'position' => VALID_NOT_EMPTY,
	);

	var $useTable="users_circuits";

	var $belongsTo = array(
			'Circuit' =>
				array('className' => 'Circuit',
						'foreignKey' => 'circuit_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),

			'User' =>
				array('className' => 'User',
						'foreignKey' => 'user_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Service' =>
				array('className' => 'Service',
						'foreignKey' => 'service_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				)

	);

/*
 * retourne la liste des id des circuits de l'utilisateur $userId sous forme : circuit1Id, circuit2Id, ...
 */
	function listeCircuitsParUtilisateur($userId) {
		$listeCircuits = '';

		$circuits = $this->findAll('user_id = '. $userId, 'circuit_id', null, null, 1, -1);
		foreach($circuits as $circuit) {
			$listeCircuits .= (empty($listeCircuits) ? '' : ', ') . $circuit['UsersCircuit']['circuit_id'];
		}

		return $listeCircuits;
	}

/*
 * retourne true si l'utilisateur $userId est dans le circuit $circuitId
 */
 	function estDansCircuit($userId, $circuitId) {
 		return $this->findCount("user_id = $userId AND circuit_id = $circuitId", -1);
 	}
}
?>