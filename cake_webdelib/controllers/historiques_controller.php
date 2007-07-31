<?php
class HistoriquesController extends AppController {

	var $name = 'Historiques';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Historique->recursive = 0;
		$this->set('historiques', $this->Historique->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Historique.');
			$this->redirect('/historiques/index');
		}
		$this->set('historique', $this->Historique->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Historique->save($this->data)) {
				$this->Session->setFlash('The Historique has been saved');
				$this->redirect('/historiques/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Historique');
				$this->redirect('/historiques/index');
			}
			$this->data = $this->Historique->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Historique->save($this->data)) {
				$this->Session->setFlash('The Historique has been saved');
				$this->redirect('/historiques/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Historique');
			$this->redirect('/historiques/index');
		}
		if ($this->Historique->del($id)) {
			$this->Session->setFlash('The Historique deleted: id '.$id.'');
			$this->redirect('/historiques/index');
		}
	}

}
?>