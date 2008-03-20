<?php
class VotesController extends AppController {

	var $name = 'Votes';
	var $helpers = array('Html', 'Form' );

	// Gestion des droits
	var $aucunDroit;

	function index() {
		$this->Vote->recursive = 0;
		$this->set('votes', $this->Vote->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Vote.');
			$this->redirect('/votes/index');
		}
		$this->set('vote', $this->Vote->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Vote->save($this->data)) {
				$this->Session->setFlash('The Vote has been saved');
				$this->redirect('/votes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Vote');
				$this->redirect('/votes/index');
			}
			$this->data = $this->Vote->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Vote->save($this->data)) {
				$this->Session->setFlash('The Vote has been saved');
				$this->redirect('/votes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Vote');
			$this->redirect('/votes/index');
		}
		if ($this->Vote->del($id)) {
			$this->Session->setFlash('The Vote deleted: id '.$id.'');
			$this->redirect('/votes/index');
		}
	}

}
?>