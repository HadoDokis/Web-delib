<?php
class Deliberation extends AppModel {

	var $name = 'Deliberation';

	var	$cacheQueries = false;

	//dependent : pour les suppression en cascades. ici � false pour ne pas modifier le referentiel
	var $belongsTo = array(
		'Service'=>array(
			'className'    => 'Service',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'service_id'),
		'Theme'=>array(
			'className'    => 'Theme',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'theme_id'),
		'Circuit'=>array(
			'className'    => 'Circuit',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'circuit_id'),
		'Redacteur' =>array(
			'className'    => 'User',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'redacteur_id'),
		'Rapporteur'=> array(
			'className'    => 'Acteur',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'rapporteur_id'),
		'Seance'=> array(
			'className'    => 'Seance',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'seance_id'),
		'Localisation'=> array(
			'className'    => 'Localisation',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'localisation1_id')
		);
	var $hasMany = array(
		'Traitement'=>array(
			'className'    => 'Traitement',
			'foreignKey'   => 'delib_id'),
		'Annexe'=>array(
			'className'    => 'Annex',
			'foreignKey'   => 'deliberation_id',
			'dependent' => true),
		'Commentaire'=>array(
			'className'    => 'Commentaire',
			'foreignKey'   => 'delib_id'),
		'Infosup'=>array(
			'dependent' => true)
		);

	/* fonction qui indique si le projet de d�lib�ration $delibId est modifiable ou non.
	 * Attention : ne teste pas ici les droits sur l'action deliberations/edit
	 * En fonction de l'�tat du projet on a :
	 * - le projet est refus� (etat = -1) : non modifiable
	 * - le projet est en cours de r�daction (etat = 0) :
	 *   + l'utilisateur connect� est le r�dacteur du projet : modifiable
	 *   + l'utilisateur connect� n'est pas le r�dacteur du projet : non modifiable
	 *  - le projet est en cours de validation (etat = 1) :
	 *    + l'utilisateur connect� n'est pas dans le circuit de validation : non modifiable
	 *    + l'utilisateur connect� est dans le circuit de validation :
	 *      * il a d�ja valid� le projet : non modifiable
	 *      * c'est � son tour de traiter le projet : modifiable
	 *      * son tour n'est pas encore pass� : modifiable
	 *  - le projet est valid� (etat = 2) : non modifiable
	 *  - le projet a �t� vot� (etat = 3 ou 4) : non modifiable
	 *  - le projet a �t� envoy� (etat = 5) : non modifiable
	 *  - le projet a recu un avis (avis = 1 ou 2) : non modifiable
	 */
	function isModifiable($delibId, $userId) {
		/* lecture en base */
		$delib = $this->find('id = '.$delibId, 'etat, avis, redacteur_id, circuit_id', null, -1);
		if (empty($delib)) return false;

		/* traitement en fonction de l'�tat */
		switch ($delib['Deliberation']['etat']) {
		case -1 :
		case 2 :
		case 3 :
		case 4 :
		case 5 :
			$isModifiable = false;
			break;
		case 0 :
			$isModifiable = ($delib['Deliberation']['redacteur_id'] == $userId);
			break;
		case 1 :
			$isModifiable = ($this->Traitement->tourUserDansCircuit($userId, $delibId) > -1);
			break;
		}

		return $isModifiable;
	}

}
?>