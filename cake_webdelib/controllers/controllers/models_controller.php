<?php
class ModelsController extends AppController {
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );

	function index() {
		$this->set('models', $this->Model->findAll());
	}	


	function add() {
		if (empty($this->data)) {
			$this->render();
		} else{
			
			if ($this->Model->save($this->data)) {
				$this->redirect('/models/index');
			}
		}
	}

	function edit($id=null) {
		if (empty($this->data)) {
			$this->data = $this->Model->read(null, $id);
		} else{
			$this->data['Model']['id']=$id;
			if ($this->Model->save($this->data)) {
				$this->redirect('/models/index');
			}
		}
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation');
			$this->redirect('/models/index');
		}
		if ($this->Model->del($id)) {
			$this->Session->setFlash('Le model a &eacute;t&eacute; supprim&eacute;e.');
			$this->redirect('/models/index');
		}
	}
	
	function view($id = null) {
		$this->set('model', $this->Model->read(null, $id));
		//$this->set('delib_id', $id);
	}
}
?>