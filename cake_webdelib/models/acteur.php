<?php
/**
* Gestion des s�quences utilis�es par les compteurs param�trables
*
* PHP versions 4 and 5
* @filesource
* @copyright
* @link			http://www.adullact.org
* @package			web-delib
* @subpackage
* @since
* @version			1.0
* @modifiedby
* @lastmodified	$Date: 2007-10-14
* @license
*/

class Acteur extends AppModel
{
	var $name = 'Acteur';

	var $displayField = "nom";

	var $validate = array(
		'typeacteur_id' => VALID_NOT_EMPTY,
		'nom' => VALID_NOT_EMPTY,
		'prenom' => VALID_NOT_EMPTY
	);

	var $belongsTo = 'Typeacteur';

	var $hasAndBelongsToMany = array(
		'Service' => array(
			'classname'=>'Service',
			'joinTable'=>'acteurs_services',
			'foreignKey'=>'acteur_id',
			'associationForeignKey'=>'service_id',
			'conditions'=>'',
			'order'=>'',
			'limit'=>'',
			'unique'=>true,
			'finderQuery'=>'',
			'deleteQuery'=>'')
		);

	function validates() {
		// adresse mail valide si pr�sente
		if (!empty($this->data['Acteur']['email'])
			&& !preg_match(VALID_EMAIL, $this->data['Acteur']['email'] ) )
            $this->invalidate('email');

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

	/* retourne la liste des acteurs �lus [id]=>[prenom et nom] pour utilisation html->selectTag */
	function generateListElus() {
		$generateListElus = array();

		$acteurs = $this->findAll('Typeacteur.elu=1', 'id, nom, prenom', 'position ASC');
		foreach($acteurs as $acteur) {
			$generateListElus[$acteur['Acteur']['id']] = $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'];
		}

		return $generateListElus;
	}

	/* retourne la liste des acteurs [id]=>[prenom et nom] pour utilisation html->selectTag */
	function generateList() {
		$generateList = array();

		$acteurs = $this->findAll(null, 'id, nom, prenom', 'position ASC');
		foreach($acteurs as $acteur) {
			$generateList[$acteur['Acteur']['id']] = $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'];
		}

		return $generateList;
	}

	/* retourne l'id du premier acteur �lu associ� � la d�l�gation $serviceId */
	/* retourne null si non trouv�                                            */
	function selectActeurEluIdParDelegationId($delegationId) {
		$users = $this->findAll('Typeacteur.elu=1', 'id', 'position ASC');
		foreach($users as $user) {
			foreach($user['Service'] as $service) {
				if ($service['id'] == $delegationId) return $user['Acteur']['id'];
			}
		}
		return null;
	}

	/* retourne le num�ro de position max pour un Typeacteur donn�e */
	/* pour rester compatible avec le plus grand nombre de bd, on ne passe pas de requ�te */
	/* mais on fait le calcul en php */
	function getPostionMaxParTypeActeurId($typeActeurId) {
		$acteur = $this->findAll("Typeacteur.id=$typeActeurId", 'position', 'position DESC', 1);
		return empty($acteur) ? 0 : $acteur[0]['Acteur']['position'];
	}

	/* retourne le num�ro de position max pour tous les acteurs �lus */
	/* pour rester compatible avec le plus grand nombre de bd, on ne passe pas de requ�te */
	/* mais on fait le calcul en php */
	function getPostionMaxParActeursElus() {
		$acteur = $this->findAll('Typeacteur.elu=1', 'position', 'position DESC', 1);
		return empty($acteur) ? 0 : $acteur[0]['Acteur']['position'];
	}

	/* retourne le libell� correspondant au champ position : = 999 : en dernier, <999 : position */
	function libelleOrdre($ordre = null, $majuscule = false) {
		return ($ordre == 999) ? ($majuscule ? 'En dernier' : 'en dernier') : $ordre;
	}

}
?>