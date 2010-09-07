<?php
class ServicesController extends AppController {

	var $name = 'Services';
	var $helpers = array('Session', 'Tree');
	var $uses = array('Service', 'Cakeflow.Circuit');

	// Gestion des droits
	var $aucunDroit = array(
		'changeParentId',
		'changeService',
		'isEditable',
		'view'
	);
	var $commeDroit = array(
		'edit'=>'Services:index',
		'add'=>'Services:index',
		'delete'=>'Services:index'
	);

    function changeService($newServiceActif) {
    	$this->Session->del('user.User.service');
       	$this->Session->write('user.User.service',$newServiceActif);
		//redirection sur la page où on était avant de changer de service
       	$this->Redirect($this->Session->read('user.User.lasturl'));
    }

	function index() {
		$services = $this->Service->find('threaded',array('order'=>'Service.id ASC','recursive'=>-1));
		$this->_isDeletable($services);
		$this->set('data', $services);
	}

	function _isDeletable(&$services) {
		foreach($services as &$service) {
			if ($this->Service->find('first', array(
					'conditions'=>array('UserService.service_id'=>$service['Service']['id']),
					'joins' => array(
						array('table' => 'users_services',
							'alias' => 'UserService',
							'type' => 'LEFT',
							'conditions' => array(
								'Service.id = UserService.service_id',
							)
						)
					),
					'recursive'=>-1)))
				$service['Service']['deletable'] = false;
			else
				$service['Service']['deletable'] = true;
			if ($service['children'] != array())
				$this->_isDeletable($service['children']);
		}
	}

	function view($id = null) {
		$service = $this->Service->read(null, $id);
		if (!$id || empty($service)) {
			$this->Session->setFlash('Invalide id pour le Service.');
			$this->redirect('/services/index');
		}
		$this->set('service', $service);
		$this->set('circuitDefaut', $this->Circuit->findById($this->Service->field('circuit_defaut_id', 'id = '.$id)));
	}

	function add() {
		if (!empty($this->data)) {
			if (empty($this->data['Service']['parent_id'])) $this->data['Service']['parent_id']=0;
			if (empty($this->data['Service']['circuit_defaut_id'])) $this->data['Service']['circuit_defaut_id']=0;
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
		$this->set('services', $this->Service->generatetreelist(array('Service.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
		$this->set('circuits', $this->Circuit->find('list'));
	}

	function edit($id = null) {
		if (empty($this->data)) {
			$this->data = $this->Service->read(null, $id);
			if ((!$id) || (empty($this->data))) {
				$this->Session->setFlash('Invalide id pour le service');
				$this->redirect('/services/index');
			}
		}
		else {
			if (empty($this->data['Service']['parent_id'])) $this->data['Service']['parent_id']=0;
			if (empty($this->data['Service']['circuit_defaut_id'])) $this->data['Service']['circuit_defaut_id']=0;
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
		$services=$this->Service->generatetreelist(array('Service.id <>'=>$id,'Service.actif'=>1),null,null,'&nbsp;&nbsp;&nbsp;&nbsp;');
		$this->set('isEditable', $this->isEditable($id));
		$this->set('services', $services);
		$this->set('selectedService',$this->data['Service']['parent_id']);
		$this->set('circuits', $this->Circuit->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le service');
			$this->redirect('/services/index');
		}
		
		if (!$this->Service->find('first',array('conditions'=>array('parent_id'=>$id, 'actif'=>1),'recursive'=>-1))) {
			$this->Service->id=$id;
			if ($this->Service->saveField('actif',0)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; supprim&eacute;');
				$this->redirect('/services/index');
			}
			else {
				$this->Session->setFlash('Impossible de supprimer ce service');
				$this->redirect('/services/index');
			}
		}
		else {
			$this->Session->setFlash('Ce service poss&egrave;de au moins un fils, il ne peut donc être supprim&eacute;');
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
