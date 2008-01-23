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
		$aro = new Aro();
		if (empty($this->data)) {
			$profils = $this->Profil->generateList(null,'id ASC');
			$this->set('profils', $profils);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
                $aro->create(0, null, $this->data['Profil']['libelle']); // Création du groupe
				$this->Session->setFlash('Le profil a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function edit($id = null) {
		$aro = new Aro();
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
			$tab = $this->Profil->findAll("Profil.id=$id");
			$aro->delete($aro->id($tab[0]['Profil']['libelle']));

			if ($this->Profil->save($this->data)) {
			    $aro->create(0, null, $this->data['Profil']['libelle']);
				$this->Session->setFlash('Le profil a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		$aro = new Aro();
		if (!$id) {
			$tab = $this->Profil->findAll("Profil.id=$id");
			$aro->delete($aro->id($tab[0]['Profil']['libelle']));
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