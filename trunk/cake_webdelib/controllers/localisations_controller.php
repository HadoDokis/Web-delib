<?php
class LocalisationsController extends AppController {

	var $name = 'Localisations';
	var $helpers = array('Html', 'Form', 'Tree');

	function getLibelle ($id = null) {
		$condition = "Localisation.id = $id";
        $objCourant = $this->Localisation->findAll($condition);
		return $objCourant['0']['Localisation']['libelle'];
	}

	function index() {
		$this->set('data', $this->Localisation->findAllThreaded(null, null, 'Localisation.id ASC'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour La localisation.');
			$this->redirect('/localisations/index');
		}
		$this->set('localisations', $this->Localisation->read(null, $id));
	}

    function add() {
		if (empty($this->data)) {
			$localisations = $this->Localisation->generateList(null);
			$this->set('localisations', $localisations);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Localisation->save($this->data)) {
				$this->Session->setFlash('La localisation a &eacute;t&eacute; sauvegard&eacute;e');
				$this->redirect('/localisations/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				$localisations = $this->Localisation->generateList(null);
				$this->set('localisations', $localisations);
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la localisation');
				$this->redirect('/localisations/index');
			}
			$this->data = $this->Localisation->read(null, $id);
			$localisations = $this->Localisation->generateList("Localisation.id != $id");
			$this->set('isEditable', $this->isEditable($id));
			$this->set('localisations', $localisations);
			$this->set('selectedLocalisation',$this->data['Localisation']['parent_id']);
		} else {
			$this->cleanUpFields();
			if ($this->Localisation->save($this->data)) {
				$this->Session->setFlash('La localisation a &eacute;t&eacute; modifi&eacute;e');
				$this->redirect('/localisations/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour La localisation');
			$this->redirect('/localisations/index');
		}

		if ($this->Localisation->del($id)) {
			$this->Session->setFlash('La localisation a &eacute;t&eacute; supprim&eacute;e');
			$this->redirect('/localisations/index');
		}
	}

	function changeParentId($curruentParentId, $newParentId) {
		$this->data = $this->Localisation->findByParentId($curruentParentId);
	}

	function isEditable ($id) {
		$condition = "parent_id = $id";
		$liste = $this->Localisation->findAll($condition);
		return empty($liste);
	}
}
?>