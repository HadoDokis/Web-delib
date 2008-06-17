<?php
class SequencesController extends AppController
{
	var $name = 'Sequences';

	// Gestion des droits : identiques aux droits des compteurs
	var $commeDroit = array(
		'index' => 'Pages:compteurs_sequences',
		'add' => 'Pages:compteurs_sequences',
		'edit' => 'Pages:compteurs_sequences',
		'delete' => 'Pages:compteurs_sequences',
		'view' => 'Pages:compteurs_sequences'
		);

	function index()
	{
		$this->set('sequences', $this->Sequence->findAll());
	}

	function view($id = null)
	{
		if (!$this->Sequence->exists()) {
			$this->Session->setFlash('Invalide id pour la séquence');
			$this->redirect('/pages/compteurs_sequences');
		} else
			$this->set('sequence', $this->Sequence->read(null, $id));
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			$this->cleanUpFields();
			if ($this->Sequence->save($this->data)) {
				$this->Session->setFlash('La s&eacute;quence \''.$this->data['Sequence']['nom'].'\' a &eacute;t&eacute; ajout&eacute;e');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/pages/compteurs_sequences');
		else
			$this->render('edit');
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->Sequence->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour la séquence');
				$sortie = true;
			}
		} else {
			$this->cleanUpFields();
			if ($this->Sequence->save($this->data)) {
				$this->Session->setFlash('La s&eacute;quence \''.$this->data['Sequence']['nom'].'\' a &eacute;t&eacute; modifi&eacute;e');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/pages/compteurs_sequences');
	}

	function delete($id = null) {
		$sequence = $this->Sequence->read('id, nom', $id);
		if (empty($sequence)) {
			$this->Session->setFlash('Invalide id pour la s&eacute;quence');
			$this->redirect('/pages/compteurs_sequences');
		}
		elseif (!empty($sequence['Compteur'])) {
			$this->Session->setFlash('La s&eacute;quence \''.$sequence['Sequence']['nom'].'\' est utilis&eacute;e par un compteur. Suppression impossible.');
			$this->redirect('/pages/compteurs_sequences');
		}
		elseif ($this->Sequence->del($id)) {
			$this->Session->setFlash('La s&eacute;quence \''.$sequence['Sequence']['nom'].'\' a &eacute;t&eacute; supprim&eacute;e');
			$this->redirect('/pages/compteurs_sequences');
		}
	}

}
?>