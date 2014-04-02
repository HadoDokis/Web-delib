<?php
class ServicesController extends AppController {

	var $name = 'Services';
	var $helpers = array('Session', 'Tree', 'Html2');
	var $uses = array('Service', 'Cakeflow.Circuit');

	// Gestion des droits
	var $aucunDroit = array(
		'changeService',
		'isEditable',
		'view', 
                'autoComplete', 'test'
	);
	var $commeDroit = array(
		'edit'=>'Services:index',
		'add'=>'Services:index',
		'delete'=>'Services:index'
	);

    function changeService($newServiceActif) {
    	$this->Session->delete('user.User.service');
       	$this->Session->write('user.User.service',$newServiceActif);
	//redirection sur la page où on était avant de changer de service
       	$this->Redirect($this->Referer());
    }

	function index() {
                $services = $this->Service->find('threaded',array('conditions'=>array('actif'=>1), 'order'=>'Service.id ASC','recursive'=>-1));
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
			if (empty($this->data['Service']['parent_id'])) 
                            $this->request->data['Service']['parent_id']=0;
			if (empty($this->data['Service']['circuit_defaut_id'])) 
                            $this->request->data['Service']['circuit_defaut_id']=0;
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
		$this->set('services', $this->Service->generateTreeList(array('Service.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
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
			if (empty($this->data['Service']['parent_id'])) 
                            $this->request->data['Service']['parent_id']=0;
			if (empty($this->data['Service']['circuit_defaut_id'])) 
                            $this->request->data['Service']['circuit_defaut_id']=0;
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
		$services=$this->Service->generateTreeList(array('Service.id <>'=>$id,'Service.actif'=>1),null,null,'&nbsp;&nbsp;&nbsp;&nbsp;');
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

	function isEditable ($id) {
		$condition = "parent_id = $id";
		$liste = $this->Service->find('all', array('conditions' => array('Service.parent_id' => $id),
		                                            'recursive'  => -1));
		return empty($liste);
	}

        function autoComplete() {
 
            $this->layout = 'ajax';
            $data = $this->Service->find('all', array( 'conditions'=>array('Service.libelle LIKE'=>$this->params['url']['q'].'%'),
                                                                           'fields'=>array('libelle', 'id')));
 
            $this->set('data', $data);
        }

    function test () {
        Configure::write('debug', '1');
        if (!empty($this->data))
            debug($this->data);
        $this->set('to', 'to');
    }
     
}
?>
