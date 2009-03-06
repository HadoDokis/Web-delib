<?php
class Deliberation extends AppModel {

	var $name = 'Deliberation';

	var	$cacheQueries = false;

	//dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
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
/*
 * Indique si le projet de délibération $delibId est modifiable pour $userId.
 * Attention : ne tient pas compte des droits qui sont fait dans le controller
 * En fonction de l'état du projet on a :
 * - le projet est refusé (etat = -1) : non modifiable
 * - le projet est en cours de rédaction (etat = 0) :
 *   + l'utilisateur connecté est le rédacteur du projet : modifiable
 *   + l'utilisateur connecté n'est pas le rédacteur du projet : non modifiable
 *  - le projet est en cours de validation (etat = 1) :
 *    + l'utilisateur connecté n'est pas dans le circuit de validation : non modifiable
 *    + l'utilisateur connecté est dans le circuit de validation :
 *      * il a déja validé le projet : non modifiable
 *      * c'est à son tour de traiter le projet : modifiable
 *      * son tour n'est pas encore passé : modifiable
 *  - le projet est validé (etat = 2) : non modifiable
 *  - le projet a été voté (etat = 3 ou 4) : non modifiable
 *  - le projet a été envoyé (etat = 5) : non modifiable
 *  - le projet a recu un avis (avis = 1 ou 2) : non modifiable
 */
	function estModifiable($delibId, $userId) {
		/* lecture en base */
		$delib = $this->find('id = '.$delibId, 'etat, avis, redacteur_id, circuit_id', null, -1);
		if (empty($delib)) return false;

		/* traitement en fonction de l'état */
		switch($delib['Deliberation']['etat']) {
		case -1 :
		case 2 :
		case 3 :
		case 4 :
		case 5 :
			$ret = false;
			break;
		case 0 :
			$ret = ($delib['Deliberation']['redacteur_id'] == $userId);
			break;
		case 1 :
			if ($this->Circuit->UsersCircuit->findCount("user_id = $userId AND circuit_id = ".$delib['Deliberation']['circuit_id']) == 0 )
				$ret = false;
			else
				$ret = ($this->Traitement->tourUserDansCircuit($userId, $delibId) > -1);
			break;
		}

		return $ret;
	}

/*
 * retourne le libellé correspondant à l'état $etat des projets et délibérations
 */
	function libelleEtat($etat) {
 		switch($etat) {
		case -1 :
			return 'Refus&eacute;';
			break;
		case 0 :
			return 'En cours de r&eacute;daction';
			break;
		case 1:
			return 'En cous d\'&eacute;laboration et de validation';
			break;
		case 2:
			return 'Valid&eacute;';
			break;
		case 3:
			return 'Vot&eacute; et adopt&eacute;';
			break;
		case 4:
			return 'Vot&eacute; et non adopt&eacute;';
			break;
		case 5:
			return 'Transmis au contr&ocirc;le de l&eacute;galit&eacute;';
			break;
		}
	}

/*
 * retourne un tableau array('image'=>, 'titre'=>) correspondant à l'état $etat des projets et délibérations
 * pour l'affichage dans les vues
 *
 */
 	function iconeEtat($etat) {
 		switch($etat) {
		case -1 :
			return array(
				'image' => '/icons/refuse.png',
				'titre' => $this->libelleEtat($etat));
			break;
		case 0 :
			return array(
				'image' => '/icons/encours.png',
				'titre' => $this->libelleEtat($etat));
			break;
		case 1:
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->libelleEtat($etat));
			break;
		case 2:
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->libelleEtat($etat));
			break;
		case 3:
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->libelleEtat($etat));
			break;
		case 4:
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->libelleEtat($etat));
			break;
		case 5:
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->libelleEtat($etat));
			break;
		}
 	}


}
?>