<?php
class ActeursController extends AppController
{
	var $name = 'Acteurs';

	var $helpers = array('Html', 'Html2');

	var $uses = array('Acteur', 'Deliberation');

	// Gestion des droits : identiques aux droits des acteurs
	var $commeDroit = array(
		'add' => 'Acteurs:index',
		'edit' => 'Acteurs:index',
		'delete' => 'Acteurs:index',
		'view' => 'Acteurs:index'
		);

	function index($champTri = null, $sensTri = 'ASC')
	{
		$order = $champTri ? $champTri.' '.$sensTri : null;
		$this->set('acteurs', $this->Acteur->findAll(null, null, $order));
	}

	function view($id = null) {
		$acteur = $this->Acteur->read(null, $id);
		if (empty($acteur)) {
			$this->Session->setFlash('Invalide id pour l\'acteur');
			$this->redirect('/acteurs/index');
		} else
			$this->set('acteur', $acteur);
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			if ($this->_controleEtSauve()) {
				$this->Session->setFlash('L\'acteur \''.$this->data['Acteur']['prenom'].' '.$this->data['Acteur']['nom'].'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/acteurs/index');
		else {
			$this->Acteur->Typeacteur->recursive = 0;
			$this->set('typeacteurs', $this->Acteur->Typeacteur->findAll(null, 'id, nom, elu'));
			$this->set('services', $this->Acteur->Service->generateList());
			$this->set('selectedServices', null);
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->Acteur->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'acteur');
				$sortie = true;
			} else
				$this->set('selectedServices', $this->_selectedArray($this->data['Service']));
		} else {
			if ($this->_controleEtSauve()) {
				$this->Session->setFlash('L\'acteur \''.$this->data['Acteur']['prenom'].' '.$this->data['Acteur']['nom'].'\' a &eacute;t&eacute; modifi&eacute;');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				if (array_key_exists('Service', $this->data))
					$this->set('selectedServices', $this->data['Service']['Service']);
				else
					$this->set('selectedServices', null);			}
		}
		if ($sortie)
			$this->redirect('/acteurs/index');
		else {
			$this->Acteur->Typeacteur->recursive = 0;
			$this->set('typeacteurs', $this->Acteur->Typeacteur->findAll(null, 'id, nom, elu'));
			$this->set('services', $this->Acteur->Service->generateList());
		}
	}

function _controleEtSauve() {

	if (!empty($this->data['Acteur']['typeacteur_id'])) {
		if ($this->Acteur->Typeacteur->field('elu', 'id = '. $this->data['Acteur']['typeacteur_id'])) {
			// pour un élu : initialisation de 'position' si non définie
			if (!$this->data['Acteur']['position'])
				$this->data['Acteur']['position'] = $this->Acteur->getPostionMaxParActeursElus() + 1;
		} else {
			// pour un non élu : suppression des informations éventuellement saisies (service, position, date naissance)
			if (array_key_exists('Service', $this->data))
				$this->data['Service']['Service'] = array();
			$this->data['Acteur']['position'] = 999;
			$this->data['Acteur']['date_naissance_day'] = 0;
			$this->data['Acteur']['date_naissance_month'] = 0;
			$this->data['Acteur']['date_naissance_year'] = 0;
		}
	}

	$this->cleanUpFields();
	return $this->Acteur->save($this->data);
}

/* dans le controleur car utilisé dans la vue index pour l'affichage */
	function _isDeletable($acteur, &$message) {
		if ($this->Deliberation->findCount(array('rapporteur_id'=>$acteur['Acteur']['id']))) {
			$message = 'L\'acteur \''.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'\' ne peut pas être supprimé car il est le rapporteur de délibérations';
			return false;
		}
		return true;
	}

	function delete($id = null) {
		$messageErreur = '';
		$acteur = $this->Acteur->read('id, nom, prenom', $id);
		if (empty($acteur))
			$this->Session->setFlash('Invalide id pour l\'acteur');
		elseif (!$this->_isDeletable($acteur, $messageErreur))
			$this->Session->setFlash($messageErreur);
		elseif ($this->Acteur->del($id))
			$this->Session->setFlash('L\'acteur \''.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'\' a &eacute;t&eacute; supprim&eacute;');

		$this->redirect('/acteurs/index');
	}

}
?>