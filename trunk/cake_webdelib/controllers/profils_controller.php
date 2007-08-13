<?php
class ProfilsController extends AppController {

	var $name = 'Profils';
	var $helpers = array('Html', 'Form' );

	function index() {
		$data=$this->Profil->findAll();
		$this->set('data',$data);
		
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Profil.');
			$this->redirect('/profils/index');
		}
		$this->set('profil', $this->Profil->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('The Profil has been saved');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Profil');
				$this->redirect('/profils/index');
			}
			$this->data = $this->Profil->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('The Profil has been saved');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Profil');
			$this->redirect('/profils/index');
		}
		if ($this->Profil->del($id)) {
			$this->Session->setFlash('The Profil deleted: id '.$id.'');
			$this->redirect('/profils/index');
		}
	}

}
?>