<?php

class TypeactesController extends AppController {

    var $name = 'Typeactes';
    var $uses = array('Typeacte', 'ModelOdtValidator.Modeltemplate', 'Compteur', 'Nature');
    
    public $components = array('Email', 'Gedooo', 'Conversion', 'Progress', 'S2low', 'ModelOdtValidator.Fido','SabreDav',
            'Auth' => array(
            'mapActions' => array(
                'create' => array(
                    'admin_add','admin_edit',
                    'admin_index','admin_view',
                    'admin_delete',
                    'admin_deleteGabarit', 'admin_downloadGabarit')
            )
        )
    );

    function admin_index() {
        $this->Typeacte->Behaviors->attach('Containable');
        $typeactes = $this->Typeacte->find('all', array(
            'contain' => array(
                'Nature.name',
                'Compteur.nom',
                'Modelprojet.name',
                'Modeldeliberation.name'),
            'order' => array('Typeacte.name' => 'ASC')));
        for ($i = 0; $i < count($typeactes); $i++)
            $typeactes[$i]['Typeacte']['is_deletable'] = $this->Typeacte->isDeletable($typeactes[$i]['Typeacte']['id']);
        $this->set('typeactes', $typeactes);
    }

    function admin_view($id = null) {
        $typeacte = $this->Typeacte->find('first', array('conditions' => array('Typeacte.id' => $id),
            'contain' => array('Nature.name',
                'Compteur.nom',
                'Modelprojet.name',
                'Modeldeliberation.name')));
        if (empty($typeacte)) {
            $this->Session->setFlash('Invalide id pour le type de acte.', 'growl', array('type' => 'erreur'));
            $this->redirect('/typeactes/index');
        }
        $this->set('typeacte', $typeacte);
    }

    function admin_add() {
        $sortie = false;
        $success = true;

        if (!empty($this->data)) {
            $this->Typeacte->set($this->request->data);
            if ($this->Typeacte->validates()) {

                if (!empty($this->request->data['Typeacte']['gabarit_projet_upload']) && $this->request->data['Typeacte']['gabarit_projet_upload']['error'] != 4) {
                    if (strlen($this->request->data['Typeacte']['gabarit_projet_upload']['name']) > 75)
                        $this->Typeacte->invalidate('gabarit_projet_upload', 'Nom de fichier invalide : maximum 75 caractères');
                    $this->request->data['Typeacte']['gabarit_projet'] = file_get_contents($this->request->data['Typeacte']['gabarit_projet_upload']['tmp_name']);
                    $this->request->data['Typeacte']['gabarit_projet_name'] = $this->request->data['Typeacte']['gabarit_projet_upload']['name'];
                }

                if (!empty($this->request->data['Typeacte']['gabarit_synthese_upload']) && $this->request->data['Typeacte']['gabarit_synthese_upload']['error'] != 4) {
                    if (strlen($this->request->data['Typeacte']['gabarit_synthese_upload']['name'])>75)
                        $this->Typeacte->invalidate('gabarit_synthese_upload', 'Nom de fichier invalide : maximum 75 caractères');
                    $this->request->data['Typeacte']['gabarit_synthese'] = file_get_contents($this->request->data['Typeacte']['gabarit_synthese_upload']['tmp_name']);
                    $this->request->data['Typeacte']['gabarit_synthese_name'] = $this->request->data['Typeacte']['gabarit_synthese_upload']['name'];
                }

                if (!empty($this->request->data['Typeacte']['gabarit_acte_upload']) && $this->request->data['Typeacte']['gabarit_acte_upload']['error'] != 4) {
                    if (strlen($this->request->data['Typeacte']['gabarit_acte_upload']['name'])>75)
                        $this->Typeacte->invalidate('gabarit_acte_upload', 'Nom de fichier invalide : maximum 75 caractères');
                    $this->request->data['Typeacte']['gabarit_acte'] = file_get_contents($this->request->data['Typeacte']['gabarit_acte_upload']['tmp_name']);
                    $this->request->data['Typeacte']['gabarit_acte_name'] = $this->request->data['Typeacte']['gabarit_acte_upload']['name'];
                }
                if (empty($this->Typeacte->validationErrors) && $this->Typeacte->save($this->request->data)) {
                    $this->Session->setFlash('Le type d\'acte \'' . $this->data['Typeacte']['name'] . '\' a été sauvegardé', 'growl');
                    $sortie = true;
                } else {
                    $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                }
            }
        }
        if ($sortie) {
            $this->redirect($this->previous);
        } else {
            $this->set('compteurs', $this->Typeacte->Compteur->find('list'));
            $this->set('models_projet', $this->Modeltemplate->getModels(MODEL_TYPE_PROJET));
            $this->set('models_docfinal', $this->Modeltemplate->getModelsByTypes(array(MODEL_TYPE_TOUTES, MODEL_TYPE_PROJET, MODEL_TYPE_DELIBERATION)));
            $this->set('natures', $this->Typeacte->Nature->generateList('Nature.name'));
            $this->set('selectedNatures', null);
            $this->render('admin_edit');
        }
    }

    function admin_edit($id = null) {
        $sortie = false;
        if (empty($this->request->data)) {
            $this->request->data = $this->Typeacte->find('first', array(
                'conditions' => array('Typeacte.id' => $id),
                'contain' => array('Nature')));
            if (empty($this->request->data)) {
                $this->Session->setFlash('Type d\'acte introuvable.', 'growl', array('type' => 'erreur'));
                $sortie = true;
            } else
                $this->set('selectedNatures', $this->request->data['Nature']['id']);
        } else {
            $this->Typeacte->set($this->request->data);
            if ($this->Typeacte->validates()) {
                if (!empty($this->request->data['Typeacte']['gabarit_projet_upload']) && $this->request->data['Typeacte']['gabarit_projet_upload']['error'] != 4) {
                    if (strlen($this->request->data['Typeacte']['gabarit_projet_upload']['name'])>75)
                        $this->Typeacte->invalidate('gabarit_projet_upload', 'Nom de fichier invalide : maximum 75 caractères');
                    $this->request->data['Typeacte']['gabarit_projet'] = file_get_contents($this->request->data['Typeacte']['gabarit_projet_upload']['tmp_name']);
                    $this->request->data['Typeacte']['gabarit_projet_name'] = $this->request->data['Typeacte']['gabarit_projet_upload']['name'];
                }

                if (!empty($this->request->data['Typeacte']['gabarit_synthese_upload']) && $this->request->data['Typeacte']['gabarit_synthese_upload']['error'] != 4) {
                    if (strlen($this->request->data['Typeacte']['gabarit_synthese_upload']['name'])>75)
                        $this->Typeacte->invalidate('gabarit_synthese_upload', 'Nom de fichier invalide : maximum 75 caractères');
                    $this->request->data['Typeacte']['gabarit_synthese'] = file_get_contents($this->request->data['Typeacte']['gabarit_synthese_upload']['tmp_name']);
                    $this->request->data['Typeacte']['gabarit_synthese_name'] = $this->request->data['Typeacte']['gabarit_synthese_upload']['name'];
                }

                if (!empty($this->request->data['Typeacte']['gabarit_acte_upload']) && $this->request->data['Typeacte']['gabarit_acte_upload']['error'] != 4) {
                    if (strlen($this->request->data['Typeacte']['gabarit_acte_upload']['name'])>75)
                        $this->Typeacte->invalidate('gabarit_acte_upload', 'Nom de fichier invalide : maximum 75 caractères');
                    $this->request->data['Typeacte']['gabarit_acte'] = file_get_contents($this->request->data['Typeacte']['gabarit_acte_upload']['tmp_name']);
                    $this->request->data['Typeacte']['gabarit_acte_name'] = $this->request->data['Typeacte']['gabarit_acte_upload']['name'];
                }

                if (empty($this->Typeacte->validationErrors) && $this->Typeacte->save($this->data)) {
                    $this->Session->setFlash('Le type d\'acte \'' . $this->data['Typeacte']['name'] . '\' a été modifié', 'growl');
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
        if ($sortie){
            $this->redirect($this->previous);
        }else {
            $this->set('compteurs', $this->Typeacte->Compteur->find('list'));
            $this->set('models_projet', $this->Modeltemplate->getModels(MODEL_TYPE_PROJET));
            $this->set('models_docfinal', $this->Modeltemplate->getModelsByTypes(array(MODEL_TYPE_TOUTES, MODEL_TYPE_PROJET, MODEL_TYPE_DELIBERATION)));
            $this->set('actions', array(
                0 => $this->Typeacte->libelleAction(0, true),
                1 => $this->Typeacte->libelleAction(1, true),
                2 => $this->Typeacte->libelleAction(2, true)));
            $this->set('natures', $this->Typeacte->Nature->generateList('Nature.name'));
            //remplie les champs files
            $typeacte = $this->Typeacte->find('first', array(
                'conditions' => array('Typeacte.id' => $id)));
            $this->request->data = $typeacte;
            $this->set('typeacte_id', $id);
            if (!empty($this->request->data['Typeacte']['gabarit_projet']))
            {
                $this->set('file_gabarit_projet', $this->SabreDav->newFileDav($this->request->data['Typeacte']['gabarit_projet_name'], $this->request->data['Typeacte']['gabarit_projet']));
            }
            if (!empty($this->request->data['Typeacte']['gabarit_synthese']))
            {
                $this->set('file_gabarit_synthese', $this->SabreDav->newFileDav($this->request->data['Typeacte']['gabarit_synthese_name'], $this->request->data['Typeacte']['gabarit_synthese']));
            }
            if (!empty($this->request->data['Typeacte']['gabarit_acte']))
            {
                $this->set('file_gabarit_acte', $this->SabreDav->newFileDav($this->request->data['Typeacte']['gabarit_acte_name'], $this->request->data['Typeacte']['gabarit_acte']));
            }
        }
    }
    
    
    function admin_delete($id = null) {
        $typeacte = $this->Typeacte->read('id, name', $id);
        if (empty($typeacte)) {
            $message = 'Type d\'acte introuvable';
        } elseif (!$this->Typeacte->isDeletable($id)) {
            $message = 'Le type d\'acte \'' . $typeacte['Typeacte']['name'] . '\' ne peut pas être supprimé car il est utilisé par un acte';
        } elseif ($this->Typeacte->delete($id)) {
            $message = 'Le type d\'acte \'' . $typeacte['Typeacte']['name'] . '\' a été supprimé';
        } else {
            $message = 'Erreur lors de la tentative de suppression du type d\'acte ' . $typeacte['Typeacte']['name'];
        }
        $this->Session->setFlash($message, 'growl');
        $this->redirect(array('action' => 'index'));
    }
    
    public function admin_deleteGabarit($id,$type=null) {
        $this->_deleteGabarit($id,$type);
    }

    private function _deleteGabarit($id,$type=null) {
        $type = 'gabarit_' . $type;
        $this->Typeacte->id = $id;
        $typeacte = $this->Typeacte->find('first', array(
            'recursive' => -1,
            'conditions' => array('Typeacte.id' => $id),
            'fields' => array('Typeacte.name')
        ));
        $data = array(
            'name' => $typeacte['Typeacte']['name'],
            $type => '',
            $type . '_name' => ''
        );
        if ($this->Typeacte->save($data, false)) {
            $this->Session->setFlash('Fichier supprimé !', 'growl');
            return $this->redirect($this->previous);
        } else {
            $this->Session->setFlash("Problème survenu lors de la suppression des débats généraux", 'growl', array('type' => 'error'));
            return $this->redirect($this->here);
        }
    }

    public function admin_downloadGabarit($id,$type=null) {
        return $this->_downloadGabarit($id,$type);
    }
    
    private function _downloadGabarit($id,$type=null) {
        $type = 'gabarit_' . $type;
        $typeacte = $this->Typeacte->find('first', array(
            'recursive' => -1,
            'conditions' => array('Typeacte.id' => $id),
            'fields' => array('Typeacte.' . $type, 'Typeacte.' . $type . '_name')
        ));
        $this->response->disableCache();
        $this->response->body($typeacte['Typeacte'][$type]);
        $this->response->type('application/odt');
        $this->response->download($typeacte['Typeacte'][$type .'_name']);
        return $this->response;
    }
}