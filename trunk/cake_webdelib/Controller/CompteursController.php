<?php

class CompteursController extends AppController {

    var $name = 'Compteurs';
    
    public $components = array(
        //'Security',
        'Auth' => array(
            'mapActions' => array(
                'create' => array('admin_add','admin_edit','admin_index','admin_view','admin_delete')
            )
        )
    );
    
    function beforeFilter() {
        if (property_exists($this, 'demandePost'))
            call_user_func_array(array($this->Security, 'requirePost'), $this->demandePost);
        parent::beforeFilter();
    }

    function admin_index() {
        $this->set('compteurs', $this->Compteur->find('all', array('recursive' => 1)));
    }

    function admin_view($id = null) {
        if (!$this->Compteur->exists($id)) {
            $this->Session->setFlash('Invalide id pour le compteur', 'growl', array('type' => 'erreur'));
            $this->redirect($this->previous);
        }
        else
            $this->set('compteur', $this->Compteur->read(null, $id));
    }

    function admin_add() {
        $sortie = false;
        if (!empty($this->data)) {
            if ($this->Compteur->save($this->data)) {
                $this->Session->setFlash('Le compteur \'' . $this->data['Compteur']['nom'] . '\' a été ajouté', 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            $this->redirect($this->previous);
        else {
            $this->set('sequences', $this->Compteur->Sequence->find('list'));
            $this->render('admin_edit');
        }
    }

    function admin_edit($id = null) {
        $sortie = false;
        if (empty($this->data)) {
            $this->data = $this->Compteur->read(null, $id);
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour le compteur', 'growl', array('type' => 'erreur'));
                $sortie = true;
            }
        } else {
            if (strlen(str_replace('#', '', $this->data['Compteur']['def_compteur'])) <= 15){
                if ($this->Compteur->save($this->data)) {
                    $this->Session->setFlash('Le compteur \'' . $this->data['Compteur']['nom'] . '\' a été modifié', 'growl');
                    $sortie = true;
                }
                else
                    $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            } else
                $this->Session->setFlash("La valeur générée par l'attribut \"Définition du compteur\" ne doit pas comporter plus de 15 caractères. (sans les dièses)", 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            $this->redirect($this->previous);
        else
            $this->set('sequences', $this->Compteur->Sequence->find('list'));
    }

    function admin_delete($id = null) {
        $compteur = $this->Compteur->read('id, nom', $id);
        if (empty($compteur)) {
            $this->Session->setFlash('Invalide id pour le compteur', 'growl', array('type' => 'erreur'));
        } elseif (!empty($compteur['Typeseance'])) {
            $this->Session->setFlash('Le compteur \'' . $compteur['Compteur']['nom'] . '\' est utilisé par un type de séance. Suppression impossible.', 'growl', array('type' => 'erreur'));
        } elseif ($this->Compteur->delete($id)) {
            $this->Session->setFlash('La compteur \'' . $compteur['Compteur']['nom'] . '\' a été supprimé', 'growl');
        }
        $this->redirect($this->previous);
    }

}

?>