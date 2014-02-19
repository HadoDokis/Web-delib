<?php

class ServicesController extends AppController {

    public $name = 'Services';
    public $helpers = array('Session', 'Tree', 'Html2');
    public $uses = array('Service', 'Cakeflow.Circuit');

    // Gestion des droits
    public $aucunDroit = array(
        'changeService',
        'isEditable',
        'view',
        'autoComplete', 'test'
    );
    public $commeDroit = array(
        'edit' => 'Services:index',
        'add' => 'Services:index',
        'delete' => 'Services:index'
    );

    function changeService($newServiceActif) {
        $this->Session->delete('user.User.service');
        $this->Session->write('user.User.service', $newServiceActif);
        //redirection sur la page où on était avant de changer de service
        $this->redirect($this->referer());
    }

    function index() {
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

    function view($id = null) {
        $service = $this->Service->read(null, $id);
        if (!$id || empty($service)) {
            $this->Session->setFlash('Invalide id pour le Service.');
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('service', $service);
        $this->set('circuitDefaut', $this->Circuit->findById($this->Service->field('circuit_defaut_id', 'id = ' . $id)));
    }

    function add() {
        if (!empty($this->data)) {
            if (empty($this->data['Service']['parent_id']))
                $this->request->data['Service']['parent_id'] = 0;
            if (empty($this->data['Service']['circuit_defaut_id']))
                $this->request->data['Service']['circuit_defaut_id'] = 0;
            if ($this->Service->save($this->data)) {
                $this->Session->setFlash('Le service a été sauvegardé');
                return $this->redirect(array('action' => 'index'));
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
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            if (empty($this->data['Service']['parent_id']))
                $this->request->data['Service']['parent_id'] = 0;
            if (empty($this->data['Service']['circuit_defaut_id']))
                $this->request->data['Service']['circuit_defaut_id'] = 0;
            if ($this->Service->save($this->data)) {
                $this->Session->setFlash('Le service a été sauvegardé');
                $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
            }
        }
        $services = $this->Service->generateTreeList(array('Service.id <>' => $id, 'Service.actif' => 1), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        $this->set('isEditable', $this->isEditable($id));
        $this->set('services', $services);
        $this->set('selectedService', $this->data['Service']['parent_id']);
        $this->set('circuits', $this->Circuit->find('list'));
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le service');
            return $this->redirect(array('action' => 'index'));
        }

        if (!$this->Service->find('first', array('conditions' => array('parent_id' => $id, 'actif' => 1), 'recursive' => -1))) {
            $this->Service->id = $id;
            if ($this->Service->saveField('actif', 0)) {
                $this->Session->setFlash('Le service a été supprimé');
                $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Impossible de supprimer ce service');
                $this->redirect($this->previous);
            }
        } else {
            $this->Session->setFlash('Ce service possède au moins un fils, il ne peut donc être supprimé');
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

}