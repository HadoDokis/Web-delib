<?php
class ThemesController extends AppController {

	var $name = 'Themes';
	var $helpers = array('Html', 'Form', 'Tree');

	// Gestion des droits
	var $aucunDroit = array('changeParentId', 'getLibelle', 'isEditable', 'view');
	var $commeDroit = array('edit'=>'Themes:index', 'add'=>'Themes:index', 'delete'=>'Themes:index');

	function getLibelle ($id = null) {
		$condition = "Theme.id = $id";
        $objCourant = $this->Theme->findAll($condition);
		return $objCourant['0']['Theme']['libelle'];
	}

	function index() {
		$this->set('data', $this->Theme->findAllThreaded(null, null, 'Theme.id ASC'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le Th&egrave;me.');
			$this->redirect('/themes/index');
		}
		$this->set('theme', $this->Theme->read(null, $id));
	}

    function add() {
		if (empty($this->data)) {
			$themes = $this->Theme->generateList(null);
			$this->set('themes', $themes);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('Le th&egrave;me a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le Th&egrave;me');
				$this->redirect('/Themes/index');
			}
			$this->data = $this->Theme->read(null, $id);
			$themes = $this->Theme->generateList("Theme.id != $id");
			$this->set('isEditable', $this->isEditable($id));
			$this->set('themes', $themes);
			$this->set('selectedTheme',$this->data['Theme']['parent_id']);
		} else {
			$this->cleanUpFields();
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('Le th&egrave;me a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le Th&egrave;me');
			$this->redirect('/themes/index');
		}

		if ($this->Theme->del($id)) {
			$this->Session->setFlash('Le Th&egrave;me a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/themes/index');
		}
	}

	function changeParentId($curruentParentId, $newParentId) {
		$this->data = $this->Theme->findByParentId($curruentParentId);
	}

	function isEditable ($id) {
		$condition = "parent_id = $id";
		$liste = $this->Theme->findAll($condition);
		return empty($liste);
	}

}
?>
