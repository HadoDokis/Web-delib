<?php
class MotclesController extends AppController {

	var $name = 'Motcles';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Motcle->recursive = 0;
		$this->set('motcles', $this->Motcle->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Motcle.');
			$this->redirect('/motcles/index');
		}
		$this->set('motcle', $this->Motcle->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Motcle->save($this->data)) {
				$this->Session->setFlash('The Motcle has been saved');
				$this->redirect('/motcles/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Motcle');
				$this->redirect('/motcles/index');
			}
			$this->data = $this->Motcle->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Motcle->save($this->data)) {
				$this->Session->setFlash('The Motcle has been saved');
				$this->redirect('/motcles/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Motcle');
			$this->redirect('/motcles/index');
		}
		if ($this->Motcle->del($id)) {
			$this->Session->setFlash('The Motcle deleted: id '.$id.'');
			$this->redirect('/motcles/index');
		}
	}

}
?>