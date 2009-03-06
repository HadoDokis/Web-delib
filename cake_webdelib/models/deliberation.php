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
/*
 * Indique si le projet de d�lib�ration $delibId est modifiable pour $userId.
 * Attention : ne tient pas compte des droits qui sont fait dans le controller
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
	function estModifiable($delibId, $userId) {
		/* lecture en base */
		$delib = $this->find('id = '.$delibId, 'etat, avis, redacteur_id, circuit_id', null, -1);
		if (empty($delib)) return false;

		/* traitement en fonction de l'�tat */
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
 * retourne le libell� correspondant � l'�tat $etat des projets et d�lib�rations
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
 * retourne un tableau array('image'=>, 'titre'=>) correspondant � l'�tat $etat des projets et d�lib�rations
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