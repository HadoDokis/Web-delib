<?php
class ListepresencesController extends AppController {

	var $name = 'Listepresences';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Listepresence->recursive = 0;
		$this->set('listepresences', $this->Listepresence->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Listepresence.');
			$this->redirect('/listepresences/index');
		}
		$this->set('listepresence', $this->Listepresence->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Listepresence->save($this->data)) {
				$this->Session->setFlash('The Listepresence has been saved');
				$this->redirect('/listepresences/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Listepresence');
				$this->redirect('/listepresences/index');
			}
			$this->data = $this->Listepresence->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Listepresence->save($this->data)) {
				$this->Session->setFlash('The Listepresence has been saved');
				$this->redirect('/listepresences/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Listepresence');
			$this->redirect('/listepresences/index');
		}
		if ($this->Listepresence->del($id)) {
			$this->Session->setFlash('The Listepresence deleted: id '.$id.'');
			$this->redirect('/listepresences/index');
		}
	}

}
?>