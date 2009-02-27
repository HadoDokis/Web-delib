<?php
class Traitement extends AppModel {

	var $name = 'Traitement';

	/* Determine le tour de traitment de l'utilisateur $userId dans le circuit du projet $delibID
	 * Attention : on considère que l'utilisateur $userId fait forcement parti du circuit de $delibId
	 * retourne :
	 * -1 si le tour de l'utilisateur est déjà passé
	 * 0 si c'est le tour de l'utilisateur
	 * 1 si le tour de l'utilisateur n'est pas encore passé
	 */
	function tourUserDansCircuit($userId, $delibId) {
		/* on passe par une requete sql car on a une jointure sur 2 champs */
		$userTraitement = $this->query(
			"SELECT Traitement.date_traitement ".
			"FROM users_circuits UsersCircuit, traitements Traitement ".
			"WHERE UsersCircuit.circuit_id = Traitement.circuit_id ".
			"AND UsersCircuit.position = Traitement.position ".
			"AND UsersCircuit.user_id = ".$userId." ".
			"AND Traitement.delib_id = ".$delibId." ".
			"ORDER BY Traitement.position ASC"
		);

		if (empty($userTraitement))
			return 1;
		elseif (empty($userTraitement[0]['Traitement']['date_traitement']) ||
				$userTraitement[0]['Traitement']['date_traitement'] == '0000-00-00 00:00:00')
			return 0;
		else
			return -1;
	}

}
?>