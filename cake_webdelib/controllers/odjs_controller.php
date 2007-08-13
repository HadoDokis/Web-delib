<?php
class OdjsController extends AppController {

	var $name = 'Odjs';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Odj->recursive = 0;
		$this->set('odjs', $this->Odj->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Odj.');
			$this->redirect('/odjs/index');
		}
		$this->set('odj', $this->Odj->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Odj->save($this->data)) {
				$this->Session->setFlash('The Odj has been saved');
				$this->redirect('/odjs/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Odj');
				$this->redirect('/odjs/index');
			}
			$this->data = $this->Odj->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Odj->save($this->data)) {
				$this->Session->setFlash('The Odj has been saved');
				$this->redirect('/odjs/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Odj');
			$this->redirect('/odjs/index');
		}
		if ($this->Odj->del($id)) {
			$this->Session->setFlash('The Odj deleted: id '.$id.'');
			$this->redirect('/odjs/index');
		}
	}

}
?>