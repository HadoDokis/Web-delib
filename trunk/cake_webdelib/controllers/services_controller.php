<?php
class ServicesController extends AppController {

	var $name = 'Services';
	var $helpers = array('Html', 'Form' );

	function index() {
		$data=$this->Service->findAll();
		$this->set('data',$data);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Service.');
			$this->redirect('/services/index');
		}
		$this->set('service', $this->Service->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('The Service has been saved');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Service');
				$this->redirect('/services/index');
			}
			$this->data = $this->Service->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('The Service has been saved');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Service');
			$this->redirect('/services/index');
		}
		if ($this->Service->del($id)) {
			$this->Session->setFlash('The Service deleted: id '.$id.'');
			$this->redirect('/services/index');
		}
	}
	
    function changeService($newServiceActif)
    {
    	$this->Session->del('user.User.service');
       	$this->Session->write('user.User.service',$newServiceActif);
       	$this->Redirect("/");
    }
	

}
?>