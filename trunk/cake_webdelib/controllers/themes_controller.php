<?php
class ThemesController extends AppController {

	var $name = 'Themes';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Theme->recursive = 0;
		$this->set('themes', $this->Theme->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le theme.');
			$this->redirect('/themes/index');
		}
		$this->set('theme', $this->Theme->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('Le theme a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le theme');
				$this->redirect('/themes/index');
			}
			$this->data = $this->Theme->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('Le theme a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le theme');
			$this->redirect('/themes/index');
		}
		if ($this->Theme->del($id)) {
			$this->Session->setFlash('Le theme a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/themes/index');
		}
	}
	
	function getLibelle ($id = null) {
		$condition = "Theme.id = $id";
        $objCourant = $this->Theme->findAll($condition);
		return $objCourant['0']['Theme']['libelle'];
	}

}
?>