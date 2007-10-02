<?php
class ProfilsController extends AppController {

	var $name = 'Profils';
	var $helpers = array('Html', 'Form','Tree' );

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
		if (empty($this->data)) {
			$profils = $this->Profil->generateList(null,'id ASC');
			$this->set('profils', $profils);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('Le profil a &eacute;t&eacute;sauvegard&eacute;');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le profil');
				$this->redirect('/profils/index');
			}
			$this->data = $this->Profil->read(null, $id);
			$profils = $this->Profil->generateList();
			$this->set('profils', $profils);
			$this->set('selectedProfil',$this->data['Profil']['parent_id']);
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('Le profil a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le profil');
			$this->redirect('/profils/index');
		}

		if ($this->Profil->del($id)) {

			$this->Session->setFlash('Le profil a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/profils/index');
		}
	}

	function changeParentId($curruentParentId, $newParentId)
	{
		$this->data = $this->Profil->findByParentId(null, $id);
		debug($this->data);exit;
	}

}
?>