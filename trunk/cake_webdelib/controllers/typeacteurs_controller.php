<?php
class TypeacteursController extends AppController
{
	var $name = 'Typeacteurs';

	// Gestion des droits : identiques aux droits des acteurs
	var $commeDroit = array(
		'add' => 'Typeacteurs:index',
		'edit' => 'Typeacteurs:index',
		'delete' => 'Typeacteurs:index',
		'view' => 'Typeacteurs:index'
		);

	function index()
	{
		$this->set('typeacteurs', $this->Typeacteur->findAll());
	}

	function view($id = null)
	{
		if (!$this->Typeacteur->exists()) {
			$this->Session->setFlash('Invalide id pour le type d\'acteur');
			$this->redirect('/typeacteurs/index');
		} else
			$this->set('typeacteur', $this->Typeacteur->read(null, $id));
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			$this->cleanUpFields();
			if ($this->Typeacteur->save($this->data)) {
				$this->Session->setFlash('Le type d\'acteur \''.$this->data['Typeacteur']['nom'].'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/typeacteurs/index');
		else {
			$this->set('eluNonElu',array('1'=>'élu','0'=>'non élu'));
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->Typeacteur->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour le type d\'acteur');
				$sortie = true;
			}
		} else {
			$this->cleanUpFields();
			if ($this->Typeacteur->save($this->data)) {
				$this->Session->setFlash('Le type d\'acteur \''.$this->data['Typeacteur']['nom'].'\' a &eacute;t&eacute; modifi&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/typeacteurs/index');
		else
			$this->set('eluNonElu',array('1'=>'élu','0'=>'non élu'));
	}

	function delete($id = null) {
		$typeacteur = $this->Typeacteur->read('id, nom', $id);
		if (empty($typeacteur)) {
			$this->Session->setFlash('Invalide id pour le type d\'acteur');
		}
		elseif (!empty($typeacteur['Acteur'])) {
			$this->Session->setFlash('Le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' est utilis&eacute; par un acteur. Suppression impossible.');
		}
		elseif ($this->Typeacteur->del($id)) {
			$this->Session->setFlash('Le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' a &eacute;t&eacute; supprim&eacute;');
		}
		$this->redirect('/typeacteurs/index');
	}

}
?>