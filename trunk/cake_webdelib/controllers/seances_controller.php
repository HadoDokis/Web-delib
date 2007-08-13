<?php
class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Seance->recursive = 0;
		$this->set('seances', $this->Seance->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Seance.');
			$this->redirect('/seances/index');
		}
		$this->set('seance', $this->Seance->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('The Seance has been saved');
				$this->redirect('/seances/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Seance');
				$this->redirect('/seances/index');
			}
			$this->data = $this->Seance->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('The Seance has been saved');
				$this->redirect('/seances/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Seance');
			$this->redirect('/seances/index');
		}
		if ($this->Seance->del($id)) {
			$this->Session->setFlash('The Seance deleted: id '.$id.'');
			$this->redirect('/seances/index');
		}
	}

}
?>