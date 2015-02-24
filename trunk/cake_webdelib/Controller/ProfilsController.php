<?php

class ProfilsController extends AppController {

    var $helpers = array('Tree', 'Fck');
    var $components = array('Progress', 'Email',
        'Auth' => array(
            'mapActions' => array(
                'create' => array('admin_add'),
                'read' => array('admin_index', 'view','notifier'),
                'update' => array('admin_edit'),
                'delete' => array('admin_delete')),
        ),
        'AuthManager.AclManager',
        );
    var $uses = array('Profil');

    function admin_index() {
        $profils = $this->Profil->find('threaded', array('order' => 'Profil.id ASC', 'recursive' => -1));
        $this->_isDeletable($profils);
        $this->set('data', $profils);
    }

    function _isDeletable(&$profils) {
        foreach ($profils as &$profil) {
            if ($this->Profil->User->find('first', array('conditions' => array('User.profil_id' => $profil['Profil']['id']), 'recursive' => -1)))
                $profil['Profil']['deletable'] = false;
            else
                $profil['Profil']['deletable'] = true;
            if ($profil['children'] != array())
                $this->_isDeletable($profil['children']);
        }
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le profil.');
            $this->redirect(array('action'=>'index'));
        }
        $this->set('profil', $this->Profil->read(null, $id));
    }

    function admin_add() {
        
        $sortie = false;
        if (!empty($this->data)) {
            if (empty($this->data['Profil']['parent_id']))
                $this->request->data['Profil']['parent_id'] = 0;
            if ($this->Profil->save($this->data)) {
                $this->Session->setFlash('Le profil a été sauvegardé', 'growl');
                $sortie = true;
            }
            else{
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
                $this->Profil->rollback(); 
            }
           
        }
        if ($sortie)
            $this->redirect(array('action'=>'index'));
        else {
            $this->set('profils', $this->Profil->find('list', array('order' => array('Profil.name ASC'))));
        }
    }

    function admin_edit($id = null) {
        $sortie = false;
        if (empty($this->data)) {
            $this->data = $this->Profil->find('first', array('conditions' => array("Profil.id" => $id)));
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour le profil', 'growl', array('type' => 'erreur'));
                $this->redirect($this->previous);
            }
            
            $this->AclManager->permissionsTypeacte($id);
            $this->AclManager->permissionsProfil($id);
            $this->AclManager->permissionsUser($id);
            
        } else {
            $this->Progress->start(200, 100, 200, '#FFCC00', '#006699');
            if (empty($this->data['Profil']['parent_id']))
                $this->request->data['Profil']['parent_id'] = 0;
            $profil = $this->Profil->read(null, $id);
            if ($this->Profil->save($this->data)) {
                
                $this->AclManager->setPermissionsTypeacte('Profil', $id, $this->data['Aco']['Typeacte']);
                $this->AclManager->setPermissionsProfil('Profil', $id, $this->data['Aco']['Profil']);
                $this->AclManager->setPermissionsUser('Profil',$id, $this->data['Aco']['User']);
            
                /*$aro_id = $this->Aro->find('first', array('conditions' => array('model' => 'Profil', 'foreign_key' => $id), 'fields' => array('id')));
                $this->Aro->id = $aro_id['Aro']['id'];
                $this->Aro->saveField('parent_id', $this->data['Profil']['parent_id']);*/
                $Users = $this->Profil->User->find('all', array('conditions' => array('User.profil_id' => $this->data['Profil']['id']), 'recursive' => -1));
                $nbUsers = count($Users);
                $cpt = 0;
                foreach ($Users as $User) {
                    $cpt++;
                    $this->Progress->at($cpt * (100 / $nbUsers), 'Mise à jour des données pour : ' . $User['User']['login'] . '...');
                    /*$this->Dbdroits->MajCruDroits(
                            array(
                        'model' => 'User', 'foreign_key' => $User['User']['id'], 'alias' => $User['User']['login']), array(
                        'model' => 'Profil', 'foreign_key' => $User['User']['profil_id']), $this->data['Droits']
                    );*/
                }
                $this->Session->setFlash('Le profil a été modifié', 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
        }
        if ($sortie) {
            $this->Progress->end($this->previous);
        } else {
            $profils = $this->Profil->find('list', array(
                'conditions' => array('Profil.id <>' => $id),
                'order' => array('Profil.name ASC')));
            $this->set('profils', $profils);
            $this->set('selectedProfil', $this->data['Profil']['parent_id']);
        }
    }

    function admin_delete($id = null) {
        if (!$id) {
            $tab = $this->Profil->findAll("Profil.id=$id");
            $this->Session->setFlash('Invalide id pour le profil', 'growl');
            $this->redirect(array('action'=>'index'));
        }
        if (!$this->Profil->User->find('first', array('conditions' => array('User.profil_id' => $id)))) {
            if ($this->Profil->delete($id)) {
                $this->Session->setFlash('Le profil a été supprimé', 'growl');
                $this->redirect(array('action'=>'index'));
            }
            else {
                $this->Session->setFlash('Impossible de supprimer ce profil', 'growl', array('type'=>'erreur'));
                $this->redirect(array('action'=>'index'));
            }
        } else {
            $this->Session->setFlash('Impossible de supprimer ce profil car il est attribué.', 'growl', array('type'=>'erreur'));
            $this->redirect(array('action'=>'index'));
        }
    }

    function notifier($profil_id) {
        $profil = $this->Profil->find('first', array('conditions' => array('Profil.id' => $profil_id),
            'recursive' => -1));

        if (empty($this->data)) {
            $this->set('libelle_profil', $profil['Profil']['name']);
            $this->set('id', $profil['Profil']['id']);
        } else {
            $this->Progress->start(200, 100, 200, '#FFCC00', '#006699');

            $conditions['AND']['User.profil_id'] = $profil['Profil']['id'];
            $conditions['AND']['User.email ILIKE'] = "%@%";
            $users = $this->Profil->User->find('all', array('conditions' => $conditions,
                'recursive' => -1)
            );
            $nbUsers = count($users);
            $cpt = 0;
            if (Configure::read("SMTP_USE")) {
                $this->Email->smtpOptions = array('port' => Configure::read("SMTP_PORT"),
                    'timeout' => Configure::read("SMTP_TIMEOUT"),
                    'host' => Configure::read("SMTP_HOST"),
                    'username' => Configure::read("SMTP_USERNAME"),
                    'password' => Configure::read("SMTP_PASSWORD"),
                    'client' => Configure::read("SMTP_CLIENT"));
                $this->Email->delivery = 'smtp';
            }
            else
                $this->Email->delivery = 'mail';

            $this->Email->from = Configure::read("MAIL_FROM");
            $this->Email->subject = "Notification aux utilisateurs du profil : " . $profil['Profil']['name'];
            $this->Email->template = 'default';
            $this->Email->layout = 'default';
            $this->Email->sendAs = 'html';
            $this->Email->charset = 'UTF-8';
            $this->Email->attachments = null;

            foreach ($users as $user) {
                $this->Progress->at($cpt * (100 / $nbUsers), $user['User']['email'] . '...');
                $cpt++;
                $this->Email->to = $user['User']['email'];
                $this->Email->send($this->data['Profil']['content']);
                sleep(1);
            }
            $this->Progress->end('/profils/index');
        }
    }
    
    public function beforeFilter() {
        parent::beforeFilter();
    }

}
