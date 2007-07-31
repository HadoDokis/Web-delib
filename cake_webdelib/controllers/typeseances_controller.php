<?php
class TypeseancesController extends AppController {

	var $name = 'Typeseances';
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->Typeseance->recursive = 0;
		$this->set('typeseances', $this->Typeseance->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Typeseance.');
			$this->redirect('/typeseances/index');
		}
		$this->set('typeseance', $this->Typeseance->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Typeseance->save($this->data)) {
				$this->Session->setFlash('The Typeseance has been saved');
				$this->redirect('/typeseances/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Typeseance');
				$this->redirect('/typeseances/index');
			}
			$this->data = $this->Typeseance->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Typeseance->save($this->data)) {
				$this->Session->setFlash('The Typeseance has been saved');
				$this->redirect('/typeseances/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Typeseance');
			$this->redirect('/typeseances/index');
		}
		if ($this->Typeseance->del($id)) {
			$this->Session->setFlash('The Typeseance deleted: id '.$id.'');
			$this->redirect('/typeseances/index');
		}
	}

}
?>