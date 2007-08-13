<?php
class PvcompletsController extends AppController {

	var $name = 'Pvcomplets';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Pvcomplet->recursive = 0;
		$this->set('pvcomplets', $this->Pvcomplet->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Pvcomplet.');
			$this->redirect('/pvcomplets/index');
		}
		$this->set('pvcomplet', $this->Pvcomplet->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Pvcomplet->save($this->data)) {
				$this->Session->setFlash('The Pvcomplet has been saved');
				$this->redirect('/pvcomplets/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Pvcomplet');
				$this->redirect('/pvcomplets/index');
			}
			$this->data = $this->Pvcomplet->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Pvcomplet->save($this->data)) {
				$this->Session->setFlash('The Pvcomplet has been saved');
				$this->redirect('/pvcomplets/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Pvcomplet');
			$this->redirect('/pvcomplets/index');
		}
		if ($this->Pvcomplet->del($id)) {
			$this->Session->setFlash('The Pvcomplet deleted: id '.$id.'');
			$this->redirect('/pvcomplets/index');
		}
	}

}
?>