<?php
class PvsommairesController extends AppController {

	var $name = 'Pvsommaires';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Pvsommaire->recursive = 0;
		$this->set('pvsommaires', $this->Pvsommaire->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Pvsommaire.');
			$this->redirect('/pvsommaires/index');
		}
		$this->set('pvsommaire', $this->Pvsommaire->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Pvsommaire->save($this->data)) {
				$this->Session->setFlash('The Pvsommaire has been saved');
				$this->redirect('/pvsommaires/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Pvsommaire');
				$this->redirect('/pvsommaires/index');
			}
			$this->data = $this->Pvsommaire->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Pvsommaire->save($this->data)) {
				$this->Session->setFlash('The Pvsommaire has been saved');
				$this->redirect('/pvsommaires/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Pvsommaire');
			$this->redirect('/pvsommaires/index');
		}
		if ($this->Pvsommaire->del($id)) {
			$this->Session->setFlash('The Pvsommaire deleted: id '.$id.'');
			$this->redirect('/pvsommaires/index');
		}
	}

}
?>