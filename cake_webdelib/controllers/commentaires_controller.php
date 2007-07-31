<?php
class CommentairesController extends AppController {

	var $name = 'Commentaires';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Commentaire->recursive = 0;
		$this->set('commentaires', $this->Commentaire->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Commentaire.');
			$this->redirect('/commentaires/index');
		}
		$this->set('commentaire', $this->Commentaire->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Commentaire->save($this->data)) {
				$this->Session->setFlash('The Commentaire has been saved');
				$this->redirect('/commentaires/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Commentaire');
				$this->redirect('/commentaires/index');
			}
			$this->data = $this->Commentaire->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Commentaire->save($this->data)) {
				$this->Session->setFlash('The Commentaire has been saved');
				$this->redirect('/commentaires/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Commentaire');
			$this->redirect('/commentaires/index');
		}
		if ($this->Commentaire->del($id)) {
			$this->Session->setFlash('The Commentaire deleted: id '.$id.'');
			$this->redirect('/commentaires/index');
		}
	}

}
?>