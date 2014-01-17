<?php

class TypeactesController extends AppController
{

    var $name = 'Typeactes';
    var $uses = array('Typeacte', 'ModelOdtValidator.Modeltemplate', 'Compteur', 'Nature', 'Ado');
    // Gestion des droits
    var $commeDroit = array(
        'edit' => 'Typeactes:index',
        'add' => 'Typeactes:index',
        'delete' => 'Typeactes:index',
        'view' => 'Typeactes:index',
        'downloadgabarit' => 'Typeactes:index'
    );

    function index()
    {
        $this->Typeacte->Behaviors->attach('Containable');
        $typeactes = $this->Typeacte->find('all', array(
            'contain' => array('Nature.libelle',
                'Compteur.nom',
                'Modelprojet.name',
                'Modeldeliberation.name'),
            'order' => array('Typeacte.libelle' => 'ASC')));
        for ($i = 0; $i < count($typeactes); $i++)
            $typeactes[$i]['Typeacte']['is_deletable'] = $this->Typeacte->isDeletable($typeactes[$i]['Typeacte']['id']);
        $this->set('typeactes', $typeactes);
    }

    function view($id = null)
    {
        $this->Typeacte->Behaviors->attach('Containable');
        $typeacte = $this->Typeacte->find('first', array('conditions' => array('Typeacte.id' => $id),
            'contain' => array('Nature.libelle',
                'Compteur.nom',
                'Modelprojet.name',
                'Modeldeliberation.name')));
        if (empty($typeacte)) {
            $this->Session->setFlash('Invalide id pour le type de acte.', 'growl', array('type' => 'erreur'));
            $this->redirect('/typeactes/index');
        }
        $this->set('typeacte', $typeacte);
    }

    function add()
    {
        $sortie = false;
        if (!empty($this->data)) {
            $this->Typeacte->set($this->request->data);
            if ($this->Typeacte->validates()) {

                if (!empty($this->request->data['Typeacte']['gabarit_projet_upload']) && $this->request->data['Typeacte']['gabarit_projet_upload']['error'] != 4)
                    $this->request->data['Typeacte']['gabarit_projet'] = file_get_contents($this->request->data['Typeacte']['gabarit_projet_upload']['tmp_name']);
                if (!empty($this->request->data['Typeacte']['gabarit_synthese_upload']) && $this->request->data['Typeacte']['gabarit_synthese_upload']['error'] != 4)
                    $this->request->data['Typeacte']['gabarit_synthese'] = file_get_contents($this->request->data['Typeacte']['gabarit_synthese_upload']['tmp_name']);
                if (!empty($this->request->data['Typeacte']['gabarit_acte_upload']) && $this->request->data['Typeacte']['gabarit_acte_upload']['error'] != 4)
                    $this->request->data['Typeacte']['gabarit_acte'] = file_get_contents($this->request->data['Typeacte']['gabarit_acte_upload']['tmp_name']);

                if ($this->Typeacte->save($this->data)) {
                    $this->Ado->create();
                    $this->Ado->save(array(
                        'model' => 'Typeacte',
                        'foreign_key' => $this->Typeacte->id,
                        'parent_id' => 0,
                        'alias' => 'Typeacte:' . $this->data['Typeacte']['libelle']));
                    $this->Session->setFlash('Le type de acte \'' . $this->data['Typeacte']['libelle'] . '\' a été sauvegardé', 'growl');
                    $sortie = true;
                } else {
                    $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                }
            }
        }

        if ($sortie)
            $this->redirect(array('action'=>'index'));
        else {
            $this->set('compteurs', $this->Typeacte->Compteur->find('list'));
            $this->set('models_projet', $this->Modeltemplate->getModels(MODEL_TYPE_PROJET));
            $this->set('models_delib', $this->Modeltemplate->getModels(MODEL_TYPE_DELIBERATION));
            $this->set('natures', $this->Typeacte->Nature->generateList('Nature.libelle'));
            $this->set('selectedNatures', null);
            $this->render('edit');
        }
    }

    function edit($id = null)
    {
        $sortie = false;
        $this->Typeacte->Behaviors->attach('Containable');

        if (empty($this->data)) {
            $this->request->data = $this->Typeacte->find('first', array(
                'conditions' => array('Typeacte.id' => $id),
                'contain' => array('Nature')));
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour le type de séance', 'growl', array('type' => 'erreur'));
                $sortie = true;
            } else
                $this->set('selectedNatures', $this->data['Nature']['id']);
        } else {
            $this->Typeacte->set($this->request->data);
            if ($this->Typeacte->validates()) {

                if (!empty($this->request->data['Typeacte']['gabarit_projet_upload']) && $this->request->data['Typeacte']['gabarit_projet_upload']['error'] != 4)
                    $this->request->data['Typeacte']['gabarit_projet'] = file_get_contents($this->request->data['Typeacte']['gabarit_projet_upload']['tmp_name']);

                if (!empty($this->request->data['Typeacte']['gabarit_synthese_upload']) && $this->request->data['Typeacte']['gabarit_synthese_upload']['error'] != 4)
                    $this->request->data['Typeacte']['gabarit_synthese'] = file_get_contents($this->request->data['Typeacte']['gabarit_synthese_upload']['tmp_name']);

                if (!empty($this->request->data['Typeacte']['gabarit_acte_upload']) && $this->request->data['Typeacte']['gabarit_acte_upload']['error'] != 4)
                    $this->request->data['Typeacte']['gabarit_acte'] = file_get_contents($this->request->data['Typeacte']['gabarit_acte_upload']['tmp_name']);

                $ado = $this->Ado->find('first', array('conditions' => array(
                    'Ado.model' => 'Typeacte',
                    'Ado.foreign_key' => $this->data['Typeacte']['id']),
                    'fields' => array('Ado.id'),
                    'recursive' => -1));
                if ($this->Typeacte->save($this->data)) {
                    $this->Ado->id = $ado['Ado']['id'];
                    $this->Ado->saveField('alias', 'Typeacte:' . $this->data['Typeacte']['libelle']);
                    $this->Session->setFlash('Le type de séance \'' . $this->data['Typeacte']['libelle'] . '\' a été modifié', 'growl');
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
        }
        if ($sortie)
            $this->redirect(array('action'=>'index'));
        else {
            $this->set('compteurs', $this->Typeacte->Compteur->find('list'));
            $this->set('models_projet', $this->Modeltemplate->getModels(MODEL_TYPE_PROJET));
            $this->set('models_delib', $this->Modeltemplate->getModels(MODEL_TYPE_DELIBERATION));
            $this->set('actions', array(
                0 => $this->Typeacte->libelleAction(0, true),
                1 => $this->Typeacte->libelleAction(1, true),
                2 => $this->Typeacte->libelleAction(2, true)));
            $this->set('natures', $this->Typeacte->Nature->generateList('Nature.libelle'));
        }
    }

    function delete($id = null)
    {
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
        $this->redirect(array('action'=>'index'));
    }

    function downloadgabarit($id = null, $type = null){
        if (empty($id)){
            $this->Session->setFlash('identifiant incorrect', 'growl');
            return $this->redirect(array('action'=>'index'));
        }
        if (empty($type) || !in_array($type,array('projet','synthese','acte'))){
            $this->Session->setFlash('Type de gabarit incorrect. Types de gabarit disponibles : projet, synthese, acte', 'growl');
            return $this->redirect(array('action'=>'index'));
        }

        $typeacte = $this->Typeacte->find('first', array(
            'recursive' => -1,
            'conditions' => array('Typeacte.id' => $id),
            'fields' => array('Typeacte.gabarit_' . $type)
        ));

        if (!empty($typeacte)) {
            header('Content-type: application/vnd.oasis.opendocument.text');
            header('Content-Disposition: attachment; filename=gabarit_' . $type . '.odt');
            echo $typeacte['Typeacte']['gabarit_' . $type];
            exit();
        } else {
            $this->Session->setFlash('Type d\'acte introuvable', 'growl');
            return $this->redirect(array('action'=>'index'));
        }
    }

}