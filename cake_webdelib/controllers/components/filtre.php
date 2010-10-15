<?php
/**
 * Gestion des filtres dans les vues index
 * Stockage des listes des options des filtres dans la session
 * Passage des crit�res du filtre au controlleur par $this->data
 * Utilise la variable de session Filtre qui a la structure suivante :
 * - nom : non du filtre courant
 * - Criteres('nomCritere') : liste des ciriteres qui ont la structure suivante :
 * 		-'field' : nom du champ a filtrer (ex : User.name)
 *  	-'inputOptions' : liste des options du select du formulaire du filtre
 * 		-'classeDiv' : nom de la classe de la div qui contient l'input
 * 		-'retourLigne' : boobleen qui indique si il faut ajouter un div spacer
 */

class FiltreComponent extends Object{

var $components = array('Session');

/**
 * Initialisation du filtre : cr�ation et sauvegarde des valeurs des crit�res saisis dans la vue
 * @param string $name : nom du filtre
 * @param string $dataFiltre : data du formulaire de saisi des crit�re du filtre
 */
function initialisation($name, $dataFiltre) {
	// Initilisations
	$filtreActif = false;
	
	// Si on a d�j� un filtre en session et qu'il est diff�rent alors on supprime l'ancien filtre
	if ($this->Session->check('Filtre') && $this->Session->read('Filtre.nom')!=$name) {
		$this->Session->delete('Filtre');
	}
	// Initialisation
	if ($this->Session->check('Filtre')) {
		if (!empty($dataFiltre)) {
			// Sauvegarde des valeurs des crit�res s�lectionn�s dans la vue
			foreach($dataFiltre['Critere'] as $nomCritere => $valCritere) {
				if ($this->Session->read('Filtre.Criteres.'.$nomCritere.'.inputOptions.type') == 'date') {
					// crit�re de type date
					$dateVide = array('day'=>'', 'month'=>'', 'year'=>'');
					// initialisation de la valeur en session
					$valSessionSelected = $this->Session->read('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected'); 
					if (empty($valSessionSelected))
						$this->Session->write('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected', $dateVide);
					// Si la date est incomplete : on garde la valeur pr�c�dente en session
					if (	(!empty($valCritere['day']) || !empty($valCritere['month']) || !empty($valCritere['year']))
						&&	(empty($valCritere['day']) || empty($valCritere['month']) || empty($valCritere['year']))) {
						$this->Session->write('Filtre.Criteres.'.$nomCritere.'.changed', false);
						$valCritere = $this->Session->read('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected');
					} else {
						if ($this->Session->read('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected') === $valCritere) {
							$this->Session->write('Filtre.Criteres.'.$nomCritere.'.changed', false);
						} else {
							$this->Session->write('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected', $valCritere);
							$this->Session->write('Filtre.Criteres.'.$nomCritere.'.changed', true);
						}
					}
					$filtreActif = $filtreActif || ($valCritere != $dateVide);
				} else {
					if ($this->Session->read('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected') === $valCritere) {
						$this->Session->write('Filtre.Criteres.'.$nomCritere.'.changed', false);
					} else {
						$this->Session->write('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected', $valCritere);
						$this->Session->write('Filtre.Criteres.'.$nomCritere.'.changed', true);
					}
					$filtreActif = $filtreActif || ($valCritere !== '');
				}
			}
			// Sauvegarde des valeurs du fonctionnement du filtre
			$this->Session->write('Filtre.Fonctionnement.affiche', $dataFiltre['filtreFonc']['affiche']);
			$this->Session->write('Filtre.Fonctionnement.actif', $filtreActif);
		}
	} else {
		$this->Session->write('Filtre.nom', $name);
		$this->Session->write('Filtre.Fonctionnement.affiche', false);
		$this->Session->write('Filtre.Fonctionnement.actif', false);
	}
}

/**
 * Test l'existence du filtre $name
 * @param string $name : nom du filtre � tester
 * @return booleen true si il existe, false dans le cas contraire
 */
function exists($name) {
	return ($this->Session->check('Filtre') && $this->Session->read('Filtre.nom') == $name);
}

/**
 * Test l'existence d'un crit�re du filtre
 * @param string $name : nom du critere du filtre � tester, si vide test l'existence de la pr�sence de crit�res
 * @return booleen true si il existe, false dans le cas contraire
 */
function critereExists($name=null) {
	if (empty($name))
		return ($this->Session->check('Filtre.Criteres'));
	else
		return $this->Session->check('Filtre.Criteres.'.$name);
}

/**
 * Ajoute un critere au filtre courant (en session)
 * @param string $nomCritere : nom du crit�re
 * @param array $params param�tres de la fonction sous forme de tableau avec les entr�es suivantes :
 * 	- string $field : nom du model.champ a filtrer (ex : User.name)
 * 	- array $inputOptions : options du select affich� dans le formulaire
 *  	'label' : texte affich� devant le select
 *  	'options' : liste des options du select, ....
 * 	- string $classeDiv nom de la classe du div contenant l'input
 * 	- booleen $retourLigne indique si il faut ajouter un div spacer
 */
function addCritere($nomCritere, $params) {
	// Initialisation des valeurs par d�faut
	$defaut = array(
		'classeDiv' => 'demi',
		'retourLigne' => false);
	$params = array_merge($defaut, $params);
	// Initialisation des valeurs par d�faut des options du select
	$inputOptionsDefaut = array();
	if (!array_key_exists('multiple', $params['inputOptions']) || !$params['inputOptions']['multiple'])
		$inputOptionsDefaut = array(
			'empty' => __('tous', true));
	$params['inputOptions'] = array_merge($inputOptionsDefaut, $params['inputOptions']);

	$this->Session->write('Filtre.Criteres.'.$nomCritere, $params);

	$this->Session->write('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected', '');
	$this->Session->write('Filtre.Criteres.'.$nomCritere.'.changed', false);

}

/**
 * Supprime un critere au filtre courant (en session)
 * @param string $nomCritere : nom du crit�re � supprimer
 */
function delCritere($nomCritere) {
	$this->Session->delete('Filtre.Criteres.'.$nomCritere);
}

/**
 * Supprime tous les criteres du filtre (en session)
 */
function supprimer() {
	$this->Session->delete('Filtre');
}
/**
 * Retourne un tableau de conditions en fonction de la valeur des filtres de la vue
 */
function conditions() {
	$conditions = array();

	if (!$this->Session->check('Filtre.Criteres')) return $conditions;

	$criteres = $this->Session->read('Filtre.Criteres');
	foreach($criteres as $critere) {
		if (array_key_exists('type', $critere['inputOptions']) && $critere['inputOptions']['type'] == 'date') {
			// date
			if (array_key_exists('selected', $critere['inputOptions'])
				&&	!empty($critere['inputOptions']['selected'])
				&&	strlen($critere['inputOptions']['selected']['day'])>0
				&&	strlen($critere['inputOptions']['selected']['month'])>0
				&&	strlen($critere['inputOptions']['selected']['year'])>0	) {
				// la date est renseign�e
				if (strpos($critere['field'], '>') !== false)
					$conditions[$critere['field']] = sprintf("%s-%s-%s 00:00:00", $critere['inputOptions']['selected']['year'], $critere['inputOptions']['selected']['month'], $critere['inputOptions']['selected']['day']);
				elseif (strpos($critere['field'], '<') !== false)
					$conditions[$critere['field']] = sprintf("%s-%s-%s 23:59:59", $critere['inputOptions']['selected']['year'], $critere['inputOptions']['selected']['month'], $critere['inputOptions']['selected']['day']);
				else
					$conditions[$critere['field']] = sprintf("%s-%s-%s", $critere['inputOptions']['selected']['year'], $critere['inputOptions']['selected']['month'], $critere['inputOptions']['selected']['day']);
			}
		} else {
			// select
			if (	array_key_exists('selected', $critere['inputOptions'])
				&&	(
						(is_array($critere['inputOptions']['selected']) && !empty($critere['inputOptions']['selected']))
						||
						(!is_array($critere['inputOptions']['selected']) && strlen($critere['inputOptions']['selected'])>0)
					)
				)
				$conditions[$critere['field']] = $critere['inputOptions']['selected'];
		}
	}
	return $conditions;
}

/**
 * Test la valeur du crit�re $name a chang�
 * @param string $name : nom du filtre � tester si vide test l'existence de la pr�sence de crit�res
 * @return booleen true si la valeur a chang�, false dans le cas contraire ou si le crit�re n'existe pas
 */
function critereChanged($nomCritere) {
	if (!$this->critereExists($nomCritere)) return false;

	return $this->Session->read('Filtre.Criteres.'.$nomCritere.'.changed');
}

/**
 * Retourne la valeur s�lectionn�e du crit�re $name
 * @param string $name : nom du filtre � tester si vide test l'existence de la pr�sence de crit�res
 * @return multi valeur s�lectionn�e
 */
function critereSelected($nomCritere) {
	if (!$this->critereExists($nomCritere)) return false;

	return $this->Session->read('Filtre.Criteres.'.$nomCritere.'.inputOptions.selected');
}

}?>
