<?php

class ThemesController extends AppController {

    public $name = 'Themes';
    public $helpers = array('Tree');
    
    public $components = array(
        'Auth' => array(
            'mapActions' => array(
                'admin_index' => array('admin_index','admin_add','admin_edit','admin_delete','admin_view',
                    'getLibelle','isEditable','view')
            )
        )
    );

    function getLibelle($id = null) {
        $objCourant = $this->Theme->find('first', array(
            'conditions' => array('Theme.id' => $id),
            'recursive' => -1,
            'fields' => array('libelle')));
        return $objCourant['Theme']['libelle'];
    }

    function admin_index() {
        $themes = $this->Theme->find('threaded', array(
            'conditions' => array('actif' => 1),
            'order' => 'Theme.order ASC',
            'recursive' => -1));
        $this->set('data', $themes);
    }

    function admin_view($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le Thême.', 'growl', array('type' => 'erreur'));
            $this->redirect($this->referer());
        }
        $this->set('theme', $this->Theme->read(null, $id));
        $this->set('user_id', $this->user_id);
        $this->set('Droits', $this->Droits);
    }

    function admin_add() {
        $themes = $this->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        $this->set('themes', $themes);
        if (!empty($this->data)) {
            if (empty($this->data['Theme']['parent_id']))
                $this->request->data['Theme']['parent_id'] = 0;
            $this->request->data['Theme']['actif'] = 1;
            if ($this->Theme->save($this->data)) {
                $this->Session->setFlash('Le thème a été sauvegardé', 'growl');
                return $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
    }

    function admin_edit($id = null) {
        if (empty($id)) {
            $this->Session->setFlash('Invalide id pour le Thème', 'growl', array('type' => 'erreur'));
            $this->redirect($this->referer());
        }
        if (!empty($this->request->data)) {
            if (empty($this->request->data['Theme']['parent_id'])) $this->request->data['Theme']['parent_id'] = 0;
            if ($this->Theme->save($this->request->data)) {
                $this->Session->setFlash('Le thème a été modifié', 'growl');
                return $this->redirect($this->previous);
            } else {
                $error_msg = 'Veuillez corriger les erreurs ci-dessous.';
                if (empty($this->Theme->validationErrors)){
                    $error_msg = 'Impossible de déplacer ce thème';
                }
                $this->Session->setFlash($error_msg, 'growl', array('type' => 'erreur'));
            }
        }
        $this->data = $this->Theme->read(null, $id);
        $themes = $this->Theme->generateTreeList(array('Theme.id <>' => $id, 'Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        $this->set('isEditable', $this->isEditable($id));
        $this->set('themes', $themes);
        $this->set('selectedTheme', $this->data['Theme']['parent_id']);
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le Thème', 'growl', array('type' => 'erreur'));
            $this->redirect(array('controller'=>'themes','action'=>'index'));
        }
        $theme = $this->Theme->read(null, $id);
        $theme['Theme']['actif'] = 0;
        if ($this->Theme->save($theme)) {
            $this->Session->setFlash('Le Thème a été désactivé', 'growl');
            $this->redirect(array('controller'=>'themes','action'=>'index'));
        }
    }

    function isEditable($id) {
        $liste = $this->Theme->find("first", array(
            'conditions' => array('Theme.parent_id' => $id),
            'recursive' => -1));
        return empty($liste);
    }
}