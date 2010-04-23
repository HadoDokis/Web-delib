<?php
class ProfilsController extends AppController {

	var $name = 'Profils';
	var $helpers = array('Html', 'Form','Tree' );

	// Gestion des droits
	var $aucunDroit = array('changeParentId');
	var $commeDroit = array('add'=>'Profils:index', 'delete'=>'Profils:index', 'edit'=>'Profils:index', 'view'=>'Profils:index');

	function index()
	{
		$this->set('data', $this->Profil->findAllThreaded(null, null, 'Profil.id ASC'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le profil.');
			$this->redirect('/profils/index');
		}
		$this->set('profil', $this->Profil->read(null, $id));
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('Le profil a &eacute;t&eacute; sauvegard&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/profils/index');
		else
			$this->set('profils', $this->Profil->generateList(null,'Profil.libelle ASC'));
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->Profil->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour le profil');
				$sortie = true;
			}
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('Le profil a &eacute;t&eacute; modifi&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/profils/index');
		else {
			$this->set('profils', $this->Profil->generateList("Profil.id != $id", 'Profil.libelle ASC'));
			$this->set('selectedProfil',$this->data['Profil']['parent_id']);
		}

	}

	function delete($id = null) {
		if (!$id) {
			$tab = $this->Profil->findAll("Profil.id=$id");
			$this->Session->setFlash('Invalide id pour le profil');
			$this->redirect('/profils/index');
		}
		if ($this->Profil->del($id)) {
			$this->Session->setFlash('Le profil a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/profils/index');
		}
	}

	function changeParentId($curruentParentId, $newParentId) {
		$this->data = $this->Profil->findByParentId(null, $id);
		debug($this->data);exit;
	}

}
?>