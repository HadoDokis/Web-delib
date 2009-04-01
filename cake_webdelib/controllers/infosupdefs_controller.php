<?php
class InfosupdefsController extends AppController
{
	var $name = 'Infosupdefs';

	var $helpers = array('Html', 'Html2');

	// Gestion des droits : identiques aux droits de l'index
	var $commeDroit = array(
		'add' => 'Infosupdefs:index',
		'edit' => 'Infosupdefs:index',
		'delete' => 'Infosupdefs:index',
		'view' => 'Infosupdefs:index',
		'changerOrdre' => 'Infosupdefs:index'
		);

	function index() {
		$this->data = $this->{$this->modelClass}->findAll(null, null, 'ordre', null, 1, -1);
	}

	function view($id = null) {
		$this->data = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($this->data)) {
			$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : &eacute;dition impossible');
			$this->redirect('/infosupdefs/index');
		} else {
			$this->data['Infosupdef']['libelleType'] = $this->Infosupdef->libelleType($this->data['Infosupdef']['type']);
			$this->data['Infosupdef']['libelleRecherche'] = $this->Infosupdef->libelleRecherche($this->data['Infosupdef']['recherche']);
		}
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$this->data['Infosupdef']['nom'].'\' a &eacute;t&eacute; ajout&eacute;e');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/infosupdefs/index');
		else {
			$this->set('types', $this->{$this->modelClass}->generateListType());
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->{$this->modelClass}->findById($id, null, null, -1);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : &eacute;dition impossible');
				$sortie = true;
			}
		} else {
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$this->data['Infosupdef']['nom'].'\' a &eacute;t&eacute; modifi&eacute;e');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/infosupdefs/index');
		else
			$this->set('types', $this->{$this->modelClass}->generateListType());
	}

	function delete($id = null) {
		$messageErreur = '';
		$aSupprimer = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($aSupprimer))
			$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : suppression impossible');
		elseif (!$this->{$this->modelClass}->isDeletable($aSupprimer, $messageErreur))
			$this->Session->setFlash($messageErreur);
		elseif ($this->{$this->modelClass}->del($id))
			$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$aSupprimer['Infosupdef']['nom'].'\' a &eacute;t&eacute; supprim&eacute;e');

		$this->redirect('/infosupdefs/index');
	}

	function changerOrdre($id = null, $suivant = true)
	{
		$this->data = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($this->data))
			$this->Session->setFlash('Invalide id : deplacement impossible.');
		else
			$this->{$this->modelClass}->invert($id, $suivant);

		$this->redirect('/infosupdefs/index');
	}

}
?>