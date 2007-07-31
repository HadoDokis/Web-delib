<?php
class AgentsController extends AppController {

	var $name = 'Agents';
	var $helpers = array('Html', 'Form' );
	var $uses = array('Circuit', 'Agent', 'Service', 'AgentsService');
	
	function index() {
		$this->Agent->recursive = 0;
		$this->set('agents', $this->Agent->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Agent.');
			$this->redirect('/agents/index');
		}
		$this->set('agent', $this->Agent->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->set('services', $this->Agent->Service->generateList());
			$this->set('selectedServices', null);
			$this->set('circuits', $this->Agent->Circuit->generateList());
			$this->set('selectedCircuits', null);
			$this->set('profils', $this->Agent->Profil->generateList());
			$this->set('selectedProfils', null);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Agent->save($this->data)) {
				$this->Session->setFlash('The Agent has been saved');
				$this->redirect('/agents/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Agent->Service->generateList());
				if (empty($this->data['Service']['Service'])) { 
				    $this->data['Service']['Service'] = null;
				}
				$this->set('selectedServices', $this->data['Service']['Service']);
				$this->set('circuits', $this->Agent->Circuit->generateList());
				if (empty($this->data['Circuit']['Circuit'])) { 
				    $this->data['Circuit']['Circuit'] = null; 
				}
				$this->set('selectedCircuits', $this->data['Circuit']['Circuit']);
				$this->set('profils', $this->Agent->Profil->generateList());
				if (empty($this->data['Profil']['Profil'])) {
					$this->data['Profil']['Profil'] = null;
				}
				$this->set('selectedProfils', $this->data['Profil']['Profil']);
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Agent');
				$this->redirect('/agents/index');
			}
			$this->data = $this->Agent->read(null, $id);
			$this->set('services', $this->Agent->Service->generateList());
			if (empty($this->data['Service'])) { $this->data['Service'] = null; }
			$this->set('selectedServices', $this->_selectedArray($this->data['Service']));
			$this->set('circuits', $this->Agent->Circuit->generateList());
			if (empty($this->data['Circuit'])) { $this->data['Circuit'] = null; }
			$this->set('selectedCircuits', $this->_selectedArray($this->data['Circuit']));
			$this->set('profils', $this->Agent->Profil->generateList());
			if (empty($this->data['Profil'])) { $this->data['Profil'] = null; }
			$this->set('selectedProfils', $this->_selectedArray($this->data['Profil']));
		} else {
			$this->cleanUpFields();
			if ($this->Agent->save($this->data)) {
				$this->Session->setFlash('The Agent has been saved');
				$this->redirect('/agents/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Agent->Service->generateList());
				if (empty($this->data['Service']['Service'])) { $this->data['Service']['Service'] = null; }
				$this->set('selectedServices', $this->data['Service']['Service']);
				$this->set('circuits', $this->Agent->Circuit->generateList());
				if (empty($this->data['Circuit']['Circuit'])) { $this->data['Circuit']['Circuit'] = null; }
				$this->set('selectedCircuits', $this->data['Circuit']['Circuit']);
				$this->set('profils', $this->Agent->Profil->generateList());
				if (empty($this->data['Profil']['Profil'])) { $this->data['Profil']['Profil'] = null; }
				$this->set('selectedProfils', $this->data['Profil']['Profil']);
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Agent');
			$this->redirect('/agents/index');
		}
		if ($this->Agent->del($id)) {
			$this->Session->setFlash('The Agent deleted: id '.$id.'');
			$this->redirect('/agents/index');
		}
	}

	
    function getNom ($id) {
		$condition = "Agent.id = $id";
	    $fields = "nom";
	    $dataValeur = $this->Agent->findAll($condition, $fields);
	   	return $dataValeur['0'] ['Agent']['nom'];
	}
	
    function getPrenom ($id) {
		$condition = "Agent.id = $id";
	    $fields = "prenom";
	    $dataValeur = $this->Agent->findAll($condition, $fields);
	   	return $dataValeur['0'] ['Agent']['prenom'];
	}
}
?>