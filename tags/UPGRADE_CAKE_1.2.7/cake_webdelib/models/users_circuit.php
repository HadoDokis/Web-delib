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

		$circuits = $this->find('all',array('conditions'=>array('user_id'=>$userId), 'fields'=>array('circuit_id'), 'recursive'=>-1));
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

        function afficheListeCircuit($circuit_id,  $listCircuitsParaph=null){ 

            $listeUsers['id']=array();
            $listeUsers['nom']=array();
            $listeUsers['prenom']=array();
            $listeUserCircuit['id']=array();
            $listeUserCircuit['circuit_id']=array();
            $listeUserCircuit['libelle']=array();
            $listeUserCircuit['user_id']=array();
            $listeUserCircuit['nom']=array();
            $listeUserCircuit['prenom']=array();
            $listeUserCircuit['service_id']=array();
            $listeUserCircuit['position']=array();
            $listeUserCircuit['service_libelle']=array();

            $condition = "UsersCircuit.circuit_id = $circuit_id";
            $desc = 'UsersCircuit.position ASC';
            $tmplisteUserCircuit = $this->findAll($condition, null, $desc);
         

            for ($i=0; $i<count($tmplisteUserCircuit);$i++) {
                if ($tmplisteUserCircuit[$i]['UsersCircuit']['service_id']== -1) {
                    array_push($listeUserCircuit['id'],   $tmplisteUserCircuit[$i]['UsersCircuit']['id']);
                    array_push($listeUserCircuit['nom'], $listCircuitsParaph['soustype'][$tmplisteUserCircuit[$i]['UsersCircuit']['user_id']]);
                    array_push($listeUserCircuit['prenom'], Configure::read('TYPETECH'));
                    array_push($listeUserCircuit['service_id'], -1);
                    array_push($listeUserCircuit['service_libelle'], 'i-parapheur');
                    array_push($listeUserCircuit['user_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['user_id']);
                    array_push($listeUserCircuit['position'],  $tmplisteUserCircuit[$i]['UsersCircuit']['position']);
                }
                else {
                    array_push($listeUserCircuit['id'], $tmplisteUserCircuit[$i]['UsersCircuit']['id']);
                    array_push($listeUserCircuit['circuit_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['circuit_id']);
                    array_push($listeUserCircuit['libelle'], $tmplisteUserCircuit[$i]['Circuit']['libelle']);
                    array_push($listeUserCircuit['user_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['user_id']);
                    array_push($listeUserCircuit['nom'], $tmplisteUserCircuit[$i]['User']['nom']);
                    array_push($listeUserCircuit['prenom'], $tmplisteUserCircuit[$i]['User']['prenom']);
                    array_push($listeUserCircuit['service_libelle'], $tmplisteUserCircuit[$i]['Service']['libelle']);
                    array_push($listeUserCircuit['service_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['service_id']);
                    array_push($listeUserCircuit['position'], $tmplisteUserCircuit[$i]['UsersCircuit']['position']);
                }
           }
           return  $listeUserCircuit; 
        }

	function positionExists($circuit_id, $position) {
            $conditions = "UsersCircuit.circuit_id = $circuit_id AND UsersCircuit.position=$position";
            $users   = $this->find($conditions);
            return (!empty($users));
        }
}
?>
