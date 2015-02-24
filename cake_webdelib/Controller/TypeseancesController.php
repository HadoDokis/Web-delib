<?php

class TypeseancesController extends AppController {

    var $name = 'Typeseances';
    var $uses = array('Typeseance', 'Typeacte', 'ModelOdtValidator.Modeltemplate', 'Compteur');
    
    public $components = array(
        'Auth' => array(
            'mapActions' => array(
                'create' => array('admin_add','admin_edit','admin_index','admin_view','admin_delete')
            )
        )
    );

    function admin_index() {
        $typeseances = $this->Typeseance->find('all', array('contain' => array('Modelpvdetaille.name', 'Modelpvdetaille.id',
                'Modelpvsommaire.name', 'Modelpvsommaire.id',
                'Modelordredujour.name', 'Modelordredujour.id',
                'Modelconvocation.name', 'Modelconvocation.id',
                'Modeldeliberation.name', 'Modeldeliberation.id',
                'Modelprojet.name', 'Modelprojet.id',
                'Compteur.id', 'Compteur.nom', 'Acteur',
                'Typeacteur', 'Typeacte')));
        for ($i = 0; $i < count($typeseances); $i++) {
            $typeseances[$i]['Typeseance']['is_deletable'] = $this->Typeseance->isDeletable($typeseances[$i]['Typeseance']['id']);
            $typeseances[$i]['Typeseance']['action'] = $this->Typeseance->libelleAction($typeseances[$i]['Typeseance']['action'], true);
        }
        $this->set('typeseances', $typeseances);
    }

    function admin_view($id = null) {
        $typeseance = $this->Typeseance->find('first', array('conditions' => array('Typeseance.id' => $id),
            'contain' => array('Modelpvdetaille.name', 'Modelpvdetaille.id',
                'Modelpvsommaire.name', 'Modelpvsommaire.id',
                'Modelordredujour.name', 'Modelordredujour.id',
                'Modelconvocation.name', 'Modelconvocation.id',
                'Modeldeliberation.name', 'Modeldeliberation.id',
                'Modelprojet.name', 'Modelprojet.id',
                'Compteur.id', 'Compteur.nom', 'Acteur',
                'Typeacteur', 'Typeacte')));
        $this->set('typeseance', $typeseance);
    }

    function admin_add() {
        $sortie = false;
        if (!empty($this->data)) {
            if ($this->Typeseance->save($this->data)) {
                $this->Session->setFlash('Le type de seance \'' . $this->data['Typeseance']['libelle'] . '\' a été sauvegardé', 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            return $this->redirect($this->previous);
        else {
            $this->set('compteurs', $this->Typeseance->Compteur->find('list'));

            $this->set('models_projet', $this->Modeltemplate->getModels(MODEL_TYPE_PROJET));
            $this->set('models_delib', $this->Modeltemplate->getModelsByTypes(array(MODEL_TYPE_TOUTES, MODEL_TYPE_PROJET, MODEL_TYPE_DELIBERATION)));
            $this->set('models_convoc', $this->Modeltemplate->getModels(MODEL_TYPE_CONVOCATION));
            $this->set('models_odj', $this->Modeltemplate->getModels(MODEL_TYPE_ODJ));
            $this->set('models_pvdetaille', $this->Modeltemplate->getModels(MODEL_TYPE_PVDETAILLE));
            $this->set('models_pvsommaire', $this->Modeltemplate->getModels(MODEL_TYPE_PVSOMMAIRE));

            $this->set('actions', array(0 => $this->Typeseance->libelleAction(0, true),
                1 => $this->Typeseance->libelleAction(1, true),
                2 => $this->Typeseance->libelleAction(2, true)));
            $this->set('typeacteurs', $this->Typeseance->Typeacteur->find('list'));
            $this->set('selectedTypeacteurs', null);
            $this->set('acteurs', $this->Typeseance->Acteur->generateList('Acteur.nom'));
            $this->set('selectedActeurs', null);
            $this->set('natures', $this->Typeacte->find('list', array('fields' => array('Typeacte.name'))));
            $this->set('selectedNatures', null);
            $this->render('edit');
        }
    }

    function admin_edit($id = null) {
        $sortie = false;

        if (empty($this->data)) {
            $this->data = $this->Typeseance->find('first', array('conditions' => array('Typeseance.id' => $id),
                'contain' => array(
                    'Modelpvdetaille.name', 'Modelpvdetaille.id',
                    'Modelpvsommaire.name', 'Modelpvsommaire.id',
                    'Modelordredujour.name', 'Modelordredujour.id',
                    'Modelconvocation.name', 'Modelconvocation.id',
                    'Modeldeliberation.name', 'Modeldeliberation.id',
                    'Modelprojet.name', 'Modelprojet.id',
                    'Compteur.id', 'Compteur.nom', 'Acteur',
                    'Typeacteur', 'Typeacte')));
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour le type de séance');
                $sortie = true;
            } else {
                $this->set('selectedTypeacteurs', $this->_selectedArray($this->data['Typeacteur']));
                $this->set('selectedActeurs', $this->_selectedArray($this->data['Acteur']));
                $this->set('selectedNatures', $this->_selectedArray($this->data['Typeacte']));
            }
        } else {
            if ($this->Typeseance->save($this->data)) {
                $this->Session->setFlash('Le type de séance \'' . $this->data['Typeseance']['libelle'] . '\' a été modifié','growl');
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
            return $this->redirect($this->previous);
        else {
            $this->set('compteurs', $this->Typeseance->Compteur->find('list'));
            $this->set('actions', array(
                0 => $this->Typeseance->libelleAction(0, true),
                1 => $this->Typeseance->libelleAction(1, true),
                2 => $this->Typeseance->libelleAction(2, true)));
            $this->set('typeacteurs', $this->Typeseance->Typeacteur->find('list'));
            $this->set('acteurs', $this->Typeseance->Acteur->generateList('Acteur.nom'));
            $this->set('natures', $this->Typeacte->find('list', array('fields' => array('Typeacte.name'))));
            //Modèles
            $this->set('models_projet', $this->Modeltemplate->getModels(MODEL_TYPE_PROJET));
            $this->set('models_delib', $this->Modeltemplate->getModelsByTypes(array(MODEL_TYPE_TOUTES, MODEL_TYPE_PROJET, MODEL_TYPE_DELIBERATION)));
            $this->set('models_convoc', $this->Modeltemplate->getModels(MODEL_TYPE_CONVOCATION));
            $this->set('models_odj', $this->Modeltemplate->getModels(MODEL_TYPE_ODJ));
            $this->set('models_pvdetaille', $this->Modeltemplate->getModels(MODEL_TYPE_PVDETAILLE));
            $this->set('models_pvsommaire', $this->Modeltemplate->getModels(MODEL_TYPE_PVSOMMAIRE));
        }
    }

    function admin_delete($id = null) {
        $typeseance = $this->Typeseance->read('id, libelle', $id);
        if (empty($typeseance)) {
            $message = 'Type de séance introuvable';
        } elseif (!$this->Typeseance->isDeletable($id)) {
            $message = 'Le type de séance \'' . $typeseance['Typeseance']['libelle'] . '\' ne peut pas être supprimé car il est utilisé par une séance';
        } elseif ($this->Typeseance->delete($id)) {
            $message = 'Le type de séance \'' . $typeseance['Typeseance']['libelle'] . '\' a été supprimé';
        } else {
            $message = 'Erreur lors de la tentative de suppression du type de séance ' . $typeseance['Typeseance']['libelle'];
        }
        $this->Session->setFlash($message, 'growl');
        return $this->redirect($this->referer());
    }

}
