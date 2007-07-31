<?php
class AnnexesController extends AppController {

	var $name = 'Annexes';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Annex->recursive = 0;
		$this->set('annexes', $this->Annex->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Annex.');
			$this->redirect('/annexes/index');
		}
		$this->set('annex', $this->Annex->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Annex->save($this->data)) {
				$this->Session->setFlash('The Annex has been saved');
				$this->redirect('/annexes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Annex');
				$this->redirect('/annexes/index');
			}
			$this->data = $this->Annex->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Annex->save($this->data)) {
				$this->Session->setFlash('The Annex has been saved');
				$this->redirect('/annexes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Annex');
			$this->redirect('/annexes/index');
		}
		if ($this->Annex->del($id)) {
			$this->Session->setFlash('The Annex deleted: id '.$id.'');
			$this->redirect('/annexes/index');
		}
	}

}
?>