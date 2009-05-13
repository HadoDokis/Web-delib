<?php
class ServicesController extends AppController {

	var $name = 'Services';
	var $helpers = array('Html', 'Form','Tree' );
	var $uses = array('Service', 'Circuit');

	// Gestion des droits
	var $aucunDroit = array('changeParentId', 'changeService', 'isEditable', 'view');
	var $commeDroit = array('edit'=>'Services:index', 'add'=>'Services:index', 'delete'=>'Services:index');

    function changeService($newServiceActif) {
    	$this->Session->del('user.User.service');
       	$this->Session->write('user.User.service',$newServiceActif);
		//redirection sur la page où on était avant de changer de service
       	$this->Redirect($this->Session->read('user.User.lasturl'));
    }

	function index() {
		$this->set('data', $this->Service->findAllThreaded(null, null, 'Service.id ASC'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le Service.');
			$this->redirect('/services/index');
		}
		$this->set('service', $this->Service->read(null, $id));
		$this->set('circuitDefaut', $this->Circuit->findById($this->Service->field('circuit_defaut_id', 'id = '.$id)));
	}

	function add() {
		if (empty($this->data)) {
			$this->set('services', $this->Service->generateList('Service.actif = 1','id ASC'));
			$this->set('circuits', $this->Circuit->generateList());
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				$this->set('services', $this->Service->generateList('Service.actif = 1','id ASC'));
				$this->set('circuits', $this->Circuit->generateList());
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le service');
				$this->redirect('/services/index');
			}
			$this->data = $this->Service->read(null, $id);
			$services = $this->Service->generateList("Service.id != $id AND Service.actif = 1");
			$this->set('isEditable', $this->isEditable($id));
			$this->set('services', $services);
			$this->set('selectedService',$this->data['Service']['parent_id']);
			$this->set('circuits', $this->Circuit->generateList());
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				$services = $this->Service->generateList("Service.id != $id  AND Service.actif = 1");

				$this->set('isEditable', $this->isEditable($id));
				$this->set('services', $services);
				$this->set('selectedService',$this->data['Service']['parent_id']);
				$this->set('circuits', $this->Circuit->generateList());
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le service');
			$this->redirect('/services/index');
		}
		$service = $this->Service->read(null, $id);
		$service['Service']['actif'] = 0;

		if ($this->Service->save($service)) {
			$this->Session->setFlash('Le service a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/services/index');
		}
	}

	function changeParentId($curruentParentId, $newParentId) {
		$this->data = $this->Service->findByParentId($curruentParentId);
		//debug($this->data);exit;
	}


	function isEditable ($id) {
		$condition = "parent_id = $id";
		$liste = $this->Service->findAll($condition);
		return empty($liste);
	}
}
?>
