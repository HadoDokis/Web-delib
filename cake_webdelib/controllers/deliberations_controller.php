<?php
class DeliberationsController extends AppController {

	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf' );
    
	function index() {
		$this->Deliberation->recursive = 0;
		$this->set('deliberations', $this->Deliberation->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Deliberation.');
			$this->redirect('/deliberations/index');
		}
		$this->set('deliberation', $this->Deliberation->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList());
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('agents', $this->Deliberation->Agent->generateList());
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Deliberation->save($this->data)) {
				$this->Session->setFlash('The Deliberation has been saved');
				$this->redirect('/deliberations/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Deliberation->Service->generateList());
				$this->set('themes', $this->Deliberation->Theme->generateList());
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('agents', $this->Deliberation->Agent->generateList());
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Deliberation');
				$this->redirect('/deliberations/index');
			}
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList());
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('agents', $this->Deliberation->Agent->generateList());
		} else {
			$this->cleanUpFields();
			if ($this->Deliberation->save($this->data)) {
				$this->Session->setFlash('The Deliberation has been saved');
				$this->redirect('/deliberations/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Deliberation->Service->generateList());
				$this->set('themes', $this->Deliberation->Theme->generateList());
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('agents', $this->Deliberation->Agent->generateList());
			}
		}
	}
	
	function getTextSynthese ($id) {
		$condition = "Deliberation.id = $id";
	    $fields = "texte_synthese";
	    $dataValeur = $this->Deliberation->findAll($condition, $fields);
	   	return $dataValeur['0'] ['Deliberation']['texte_synthese'];
	}
	

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Deliberation');
			$this->redirect('/deliberations/index');
		}
		if ($this->Deliberation->del($id)) {
			$this->Session->setFlash('The Deliberation deleted: id '.$id.'');
			$this->redirect('/deliberations/index');
		}
	}
 
   function convert($id=null)
        {
            $this->layout = 'pdf'; //this will use the pdf.thtml layout
            $this->set('data',$this->getTextSynthese($id));
            $this->render();
        } 
}
?>