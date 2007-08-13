<?php
class ThemesController extends AppController {

	var $name = 'Themes';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Theme->recursive = 0;
		$this->set('themes', $this->Theme->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Theme.');
			$this->redirect('/themes/index');
		}
		$this->set('theme', $this->Theme->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('The Theme has been saved');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Theme');
				$this->redirect('/themes/index');
			}
			$this->data = $this->Theme->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('The Theme has been saved');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Theme');
			$this->redirect('/themes/index');
		}
		if ($this->Theme->del($id)) {
			$this->Session->setFlash('The Theme deleted: id '.$id.'');
			$this->redirect('/themes/index');
		}
	}

}
?>