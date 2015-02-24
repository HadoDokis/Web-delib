<?php

class ServicesController extends AppController {

    public $name = 'Services';
    public $helpers = array('Tree');
    public $uses = array('Service', 'UserService', 'Cakeflow.Circuit');
    
    public $components = array(
        'Auth' => array(
            'mapActions' => array(
                'admin_index' => array('admin_index','admin_add','admin_edit','admin_delete','admin_view','admin_fusionner',
                    'changeService','isEditable','view','autoComplete')
            )
        )
    );

    function changeService($newServiceActif) {
        $this->Session->delete('user.User.service');
        $this->Session->write('user.User.service', $newServiceActif);
        //redirection sur la page où on était avant de changer de service
        $this->redirect($this->referer());
    }

    function admin_index() {
        $this->view='index';
        $services = $this->Service->generateTreeList(array( 'Service.actif' => 1), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        $this->set('services', $services);
        $services = $this->Service->find('threaded', array('conditions' => array('actif' => 1), 'order' => 'Service.id ASC', 'recursive' => -1));
        $this->_isDeletable($services);
        $this->set('data', $services);
    }

    function _isDeletable(&$services) {
        foreach ($services as &$service) {
            if ($this->Service->find('first', array(
                'conditions' => array('UserService.service_id' => $service['Service']['id']),
                'joins' => array(
                    array('table' => 'users_services',
                        'alias' => 'UserService',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Service.id = UserService.service_id',
                        )
                    )
                ),
                'recursive' => -1))
            )
                $service['Service']['deletable'] = false;
            else
                $service['Service']['deletable'] = true;
            if ($service['children'] != array())
                $this->_isDeletable($service['children']);
        }
    }

    function admin_view($id = null) {
        $service = $this->Service->read(null, $id);
        if (!$id || empty($service)) {
            $this->Session->setFlash('Invalide id pour le Service.', 'growl');
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('service', $service);
        $this->set('circuitDefaut', $this->Circuit->findById($this->Service->field('circuit_defaut_id', 'id = ' . $id)));
    }

    function admin_add() {
        if (!empty($this->data)) {
            if (empty($this->data['Service']['parent_id']))
                $this->request->data['Service']['parent_id'] = 0;
            if (empty($this->data['Service']['circuit_defaut_id']))
                $this->request->data['Service']['circuit_defaut_id'] = 0;
            if ($this->Service->save($this->data)) {
                $this->Session->setFlash('Le service a été sauvegardé', 'growl');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
            }
        }
        $this->set('services', $this->Service->generateTreeList(array('Service.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
        $this->set('circuits', $this->Circuit->find('list'));
    }

    function admin_edit($id = null) {
        if (empty($this->data)) {
            $this->data = $this->Service->read(null, $id);
            if ((!$id) || (empty($this->data))) {
                $this->Session->setFlash('Invalide id pour le service', 'growl');
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            if (empty($this->data['Service']['parent_id']))
                $this->request->data['Service']['parent_id'] = 0;
            if (empty($this->data['Service']['circuit_defaut_id']))
                $this->request->data['Service']['circuit_defaut_id'] = 0;
            if ($this->Service->save($this->data)) {
                $this->Session->setFlash('Le service a été sauvegardé', 'growl');
                $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
            }
        }
        $services = $this->Service->generateTreeList(array('Service.id <>' => $id, 'Service.actif' => 1), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        $this->set('isEditable', $this->isEditable($id));
        $this->set('services', $services);
        $this->set('selectedService', $this->data['Service']['parent_id']);
        $this->set('circuits', $this->Circuit->find('list'));
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le service', 'growl', array('type'=>'danger'));
            return $this->redirect(array('action' => 'index'));
        }

        if (!$this->Service->find('first', array('conditions' => array('parent_id' => $id, 'actif' => 1), 'recursive' => -1))) {
            if ( $this->Service->desactive($id)) {
                $this->Session->setFlash('Le service a été supprimé', 'growl', array('type'=>'sucess'));
                $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Impossible de supprimer ce service', 'growl', array('type'=>'danger'));
                $this->redirect($this->previous);
            }
        } else {
            $this->Session->setFlash('Impossible de supprimer ce service : il possède au moins un fils', 'growl', array('type'=>'warning'));
            $this->redirect($this->previous);
        }
    }

    function isEditable($id) {
        return $this->Service->hasAny(array('parent_id' => $id));
    }

    function autoComplete() {
        $this->layout = 'ajax';
        $data = $this->Service->find('all', array(
            'conditions' => array('Service.libelle LIKE' => $this->params['url']['q'] . '%'),
            'fields' => array('libelle', 'id')));
        $this->set('data', $data);
    }
    
    function admin_fusionner()
    {
        if (empty($this->data['service_a_fusionner']) 
            OR empty($this->data['Service']['id'])) {
            $this->Session->setFlash(__('Invalide id pour fusionner le service'), 'growl', array('type'=>'danger'));
        
            $this->redirect($this->previous);
        }
        
        try{
           $this->UserService->fusion($this->data['service_a_fusionner'], $this->data['Service']['id']); 
           $this->Session->setFlash(__('le service a été fusionné'), 'growl', array('type'=>'sucess'));
        } catch (Exception $e) {
            $this->Session->setFlash( $e->getMessage(), 'growl', array('type'=>'danger'));
        }
        
        $this->redirect($this->previous);
    }

}