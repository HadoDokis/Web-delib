<?php
class RefannexesController extends AppController {

	var $name = 'Refannexes';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Refannex->recursive = 0;
		$this->set('refannexes', $this->Refannex->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Refannex.');
			$this->redirect('/refannexes/index');
		}
		$this->set('refannex', $this->Refannex->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Refannex->save($this->data)) {
				$this->Session->setFlash('The Refannex has been saved');
				$this->redirect('/refannexes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Refannex');
				$this->redirect('/refannexes/index');
			}
			$this->data = $this->Refannex->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Refannex->save($this->data)) {
				$this->Session->setFlash('The Refannex has been saved');
				$this->redirect('/refannexes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Refannex');
			$this->redirect('/refannexes/index');
		}
		if ($this->Refannex->del($id)) {
			$this->Session->setFlash('The Refannex deleted: id '.$id.'');
			$this->redirect('/refannexes/index');
		}
	}

}
?>