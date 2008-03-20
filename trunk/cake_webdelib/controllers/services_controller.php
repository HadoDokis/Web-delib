<?php
class ServicesController extends AppController {

	var $name = 'Services';
	var $helpers = array('Html', 'Form','Tree' );

	// Gestion des droits
	var $aucunDroit = array('changeParentId', 'changeService', 'doList', 'getLibelle', 'getParentList', 'isEditable');
	var $commeDroit = array('edit'=>'Services:index', 'add'=>'Services:index', 'delete'=>'Services:index', 'view'=>'Services:index');

    function changeService($newServiceActif) {
    	$this->Session->del('user.User.service');
       	$this->Session->write('user.User.service',$newServiceActif);
		//redirection sur la page où on était avant de changer de service
       	$this->Redirect($this->Session->read('user.User.lasturl'));
    }

	function getLibelle ($id = null) {
		$condition = "Service.id = $id";
        $objCourant = $this->Service->findAll($condition);
		return $objCourant['0']['Service']['libelle'];
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
	}

	function add() {
		if (empty($this->data)) {
			$services = $this->Service->generateList(null,'id ASC');
			$this->set('services', $services);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
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
			$services = $this->Service->generateList("Service.id != $id");
			$this->set('isEditable', $this->isEditable($id));
			$this->set('services', $services);
			$this->set('selectedService',$this->data['Service']['parent_id']);
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le service');
			$this->redirect('/services/index');
		}

		if ($this->Service->del($id)) {
			$this->Session->setFlash('Le service a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/services/index');
		}
	}

	function changeParentId($curruentParentId, $newParentId) {
		$this->data = $this->Service->findByParentId($curruentParentId);
		//debug($this->data);exit;
	}

	function doList($id){

		$liste = $this->GetParentList($id).$this->getLibelle($id);
		return $liste;
	}

	function getParentList($id){
		$tab = "";
		$condition = "id = $id";
		$liste = $this->Service->findAll($condition);
		$list = $liste[0];
		$parent_id = $list['Service']['parent_id'];
		if ($parent_id != 0){
			$parentliste = $this->Service->findAll("id = $parent_id");
			$libelle_parent = $parentliste[0]['Service']['libelle'];
			$id_parent = $parentliste[0]['Service']['id'];

			$tab = $this->getParentList($id_parent).$libelle_parent.'/';
		}

		return $tab;
	}

	function isEditable ($id) {
		$condition = "parent_id = $id";
		$liste = $this->Service->findAll($condition);
		return empty($liste);
	}
}
?>