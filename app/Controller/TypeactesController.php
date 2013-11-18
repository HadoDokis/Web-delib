<?php

class TypeactesController extends AppController {

    var $name = 'Typeactes';
    var $uses = array('Typeacte', 'Model', 'Compteur', 'Nature', 'Ado');
    // Gestion des droits
    var $commeDroit = array(
        'edit' => 'Typeactes:index',
        'add' => 'Typeactes:index',
        'delete' => 'Typeactes:index',
        'view' => 'Typeactes:index'
    );

    function index() {
        $this->Typeacte->Behaviors->attach('Containable');
        $typeactes = $this->Typeacte->find('all', array('contain' => array('Nature.libelle',
                'Compteur.nom',
                'Modelprojet.modele',
                'Modeldeliberation.modele'),
            'order' => array('Typeacte.libelle' => 'ASC')));
        for ($i = 0; $i < count($typeactes); $i++)
            $typeactes[$i]['Typeacte']['is_deletable'] = $this->Typeacte->isDeletable($typeactes[$i]['Typeacte']['id']);
        $this->set('typeactes', $typeactes);
    }

    function view($id = null) {
        $this->Typeacte->Behaviors->attach('Containable');
        $typeacte = $this->Typeacte->find('first', array('conditions' => array('Typeacte.id' => $id),
            'contain' => array('Nature.libelle',
                'Compteur.nom',
                'Modelprojet.modele',
                'Modeldeliberation.modele')));
        if (empty($typeacte)) {
            $this->Session->setFlash('Invalide id pour le type de acte.', 'growl', array('type' => 'erreur'));
            $this->redirect('/typeactes/index');
        }
        $this->set('typeacte', $typeacte);
    }

    function add() {
        $sortie = false;
        if (!empty($this->data)) {
            if ($this->Typeacte->save($this->data)) {
                $this->Ado->create();
                $this->Ado->save(array('model' => 'Typeacte',
                    'foreign_key' => $this->Typeacte->id,
                    'parent_id' => 0,
                    'alias' => 'Typeacte:' . $this->data['Typeacte']['libelle']));
                $this->Session->setFlash('Le type de acte \'' . $this->data['Typeacte']['libelle'] . '\' a &eacute;t&eacute; sauvegard&eacute;', 'growl');
                $sortie = true;
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
        if ($sortie)
            $this->redirect('/typeactes/index');
        else {
            $this->set('compteurs', $this->Typeacte->Compteur->find('list'));
            $this->set('models', $this->Model->find('list', array('conditions' => array('type' => 'Document'),
                        'fields' => array('Model.id', 'Model.modele'))));
            $this->set('natures', $this->Typeacte->Nature->generateList('Nature.libelle'));
            $this->set('selectedNatures', null);
            $this->render('edit');
        }
    }

    function edit($id = null) {
        $sortie = false;
        $this->Typeacte->Behaviors->attach('Containable');

        if (empty($this->data)) {

            $this->request->data = $this->Typeacte->find('first', array('conditions' => array('Typeacte.id' => $id),
                'contain' => array('Nature')));
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour le type de s&eacute;ance', 'growl', array('type' => 'erreur'));
                $sortie = true;
            } else {
                $this->set('selectedNatures', $this->data['Nature']['id']);
            }
        } else {
            $ado = $this->Ado->find('first', array('conditions' => array('Ado.model' => 'Typeacte',
                    'Ado.foreign_key' => $this->data['Typeacte']['id']),
                'fields' => array('Ado.id'),
                'recursive' => -1));
            if ($this->Typeacte->save($this->data)) {
                $this->Ado->id = $ado['Ado']['id'];
                $this->Ado->saveField('alias', 'Typeacte:' . $this->data['Typeacte']['libelle']);
                $this->Session->setFlash('Le type de s&eacute;ance \'' . $this->data['Typeacte']['libelle'] . '\' a &eacute;t&eacute; modifi&eacute;', 'growl');
                $sortie = true;
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                if (array_key_exists('Typeacteur', $this->data)) {
                    $this->set('selectedTypeacteurs', $this->data['Typeacteur']['Typeacteur']);
                    $this->set('selectedActeurs', $this->data['Acteur']['Acteur']);
                } else {
                    $this->set('selectedTypeacteurs', null);
                    $this->set('selectedActeurs', null);
                }
            }
        }
        if ($sortie)
            $this->redirect('/typeactes/index');
        else {
            $this->set('compteurs', $this->Typeacte->Compteur->find('list'));
            $this->set('models', $this->Model->find('list', array('conditions' => array('type' => 'Document'), 'fields' => array('Model.id', 'Model.modele'))));
            $this->set('actions', array(0 => $this->Typeacte->libelleAction(0, true),
                1 => $this->Typeacte->libelleAction(1, true),
                2 => $this->Typeacte->libelleAction(2, true)));
            $this->set('natures', $this->Typeacte->Nature->generateList('Nature.libelle'));
        }
    }

    function delete($id = null) {
        $typeacte = $this->Typeacte->read('id, libelle', $id);
        if (empty($typeacte)) {
            $message = 'Type d\'acte introuvable';
        } elseif (!$this->Typeacte->isDeletable($id)) {
            $message = 'Le type d\'acte \'' . $typeacte['Typeacte']['libelle'] . '\' ne peut pas être supprimé car il est utilisé par un acte';
        } elseif ($this->Typeacte->delete($id)) {
            $message = 'Le type d\'acte \'' . $typeacte['Typeacte']['libelle'] . '\' a été supprimé';
        } else {
            $message = 'Erreur lors de la tentative de suppression du type d\'acte ' . $typeacte['Typeacte']['libelle'];
        }
        $this->Session->setFlash($message, 'growl');
        $this->redirect('/typeactes/index');
    }

}

?>
