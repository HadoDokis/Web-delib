<?php

class SequencesController extends AppController {

    public $name = 'Sequences';
    public $components = array(
        'Auth' => array(
            'mapActions' => array(
                'admin_index' => array('admin_index','admin_add','admin_edit','admin_delete','admin_view')
            )
        )
    );

    function admin_index() {
        $this->set('sequences', $this->Sequence->find('all', array('recursive' => 1)));
    }

    function admin_view($id = null) {
        if (!$this->Sequence->exists($id)) {
            $this->Session->setFlash('Invalide id pour la s&eacute;quence', 'growl', array('type' => 'erreur'));
            $this->redirect('/sequences/index');
        }
        else
            $this->set('sequence', $this->Sequence->read(null, $id));
    }

    function admin_add() {
        $sortie = false;
        if (!empty($this->data)) {
            $this->Sequence->create($this->request->data);
            if ($this->Sequence->save()) {
                $this->Session->setFlash('La s&eacute;quence \'' . $this->data['Sequence']['nom'] . '\' a &eacute;t&eacute; ajout&eacute;e', 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            $this->redirect('/sequences/index');
        else
            $this->render('edit');
    }

    function admin_edit($id = null) {
        $sortie = false;
        if (empty($this->data)) {
            $this->data = $this->Sequence->read(null, $id);
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour la s&eacute;quence', 'growl', array('type' => 'erreur'));
                $sortie = true;
            }
        } else {
            if ($this->Sequence->save($this->data)) {
                $this->Session->setFlash('La s&eacute;quence \'' . $this->data['Sequence']['nom'] . '\' a &eacute;t&eacute; modifi&eacute;e', 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            $this->redirect('/sequences/index');
    }

    function admin_delete($id = null) {
        $sequence = $this->Sequence->read('id, nom', $id);
        if (empty($sequence)) {
            $this->Session->setFlash('Invalide id pour la s&eacute;quence', 'growl', array('type' => 'erreur'));
        } elseif (!empty($sequence['Compteur'])) {
            $this->Session->setFlash('La s&eacute;quence \'' . $sequence['Sequence']['nom'] . '\' est utilis&eacute;e par un compteur. Suppression impossible.', 'growl', array('type' => 'erreur'));
        } elseif ($this->Sequence->delete($id)) {
            $this->Session->setFlash('La s&eacute;quence \'' . $sequence['Sequence']['nom'] . '\' a &eacute;t&eacute; supprim&eacute;e', 'growl');
        }
        $this->redirect('/sequences/index');
    }

}

?>