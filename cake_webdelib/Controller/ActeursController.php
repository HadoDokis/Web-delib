<?php
App::uses('CakeTime', 'Utility');

class ActeursController extends AppController
{
    public $helpers = array();
    public $components = array('Paginator',
        'Auth' => array(
            'mapActions' => array(
                'create' => array('add'),
                'read' => array('index', 'view'),
                'update' => array('edit'),
                'delete' => array('delete')),
        )
        );
    public $uses = array('Acteur', 'Deliberation', 'Vote');
    
    public $paginate = array(
        'Acteur' => array(
            'conditions' => array('Acteur.actif' => 1),
            'fields' => array('DISTINCT Acteur.id', 'Acteur.nom', 'Acteur.prenom', 'Acteur.salutation', 'Acteur.telfixe', 'Acteur.telmobile', 'Acteur.suppleant_id', 'Acteur.titre', 'Acteur.position', 'Typeacteur.nom', 'Typeacteur.elu', 'Suppleant.nom', 'Suppleant.prenom'),
            'limit' => 20,
            'joins' => array(
                array(
                    'table' => 'acteurs_services',
                    'alias' => 'ActeursServices',
                    'type' => 'LEFT',
                    'conditions' => array('ActeursServices.acteur_id = Acteur.id')
                ),
                array(
                    'table' => 'services',
                    'alias' => 'Service',
                    'type' => 'LEFT',
                    'conditions' => array('Service.id = ActeursServices.service_id')
                )
            ),
            'order' => array('Acteur.position' => 'asc')
        )
    );

    public function admin_index()
    {
        $this->Acteur->Behaviors->attach('Containable');
        $this->paginate = array('Acteur' => array(
            'limit' => 20,
            'conditions' => array('Acteur.actif' => 1),
            'contain' => array('Service', 'Suppleant', 'Typeacteur'),
            'order' => array('Acteur.position' => 'asc')));
        $this->Paginator->settings = $this->paginate;
        $acteurs = $this->Paginator->paginate('Acteur');
        foreach ($acteurs as &$acteur) {
            $acteur['Typeacteur']['elu'] = $this->Acteur->Typeacteur->libelleElu($acteur['Typeacteur']['elu']);
            $acteur['Acteur']['libelleOrdre'] = $this->Acteur->libelleOrdre($acteur['Acteur']['position']);
        }
        $this->set('acteurs', $acteurs);
    }

    public function view($id = null)
    {
        $acteur = $this->Acteur->read(null, $id);
        if (empty($acteur)) {
            $this->Session->setFlash('Invalide id pour l\'acteur');
            $this->redirect(array('action'=>'index'));
        } else{
            $this->set('acteur', $acteur);
            $this->set('canEdit', $this->Droits->check($this->user_id, 'Acteurs:edit'));
        }
    }

    public function admin_add()
    {
        $sortie = false;
        if (!empty($this->data)) {
            if ($this->_controleEtSauve()) {
                $this->Session->setFlash('L\'acteur \'' . $this->data['Acteur']['prenom'] . ' ' . $this->data['Acteur']['nom'] . '\' a été ajouté', 'growl');
                $sortie = true;
            } else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'error'));
        }
        if ($sortie)
            $this->redirect(array('action'=>'index'));
        else {
            $this->Acteur->Typeacteur->recursive = 0;
            $this->set('acteurs', $this->Acteur->generateListElus());
            $typeacteurs = $this->Acteur->Typeacteur->find('all', array('fields' => array('id', 'nom', 'elu')));
            if (empty($typeacteurs)) {
                $this->Session->setFlash('Veuillez créer un type d&apos;acteur.', 'growl', array('type' => 'erreur'));
                $this->redirect(array('controller' => 'typeacteurs', 'action' => 'add'));
            }
            $this->set('typeacteurs', $typeacteurs=Hash::combine($typeacteurs, '{n}.Typeacteur.id', '{n}.Typeacteur.nom'));
            $this->set('services', $this->Acteur->Service->generateTreeList(array('Service.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
            $this->set('selectedServices', null);
            $this->render('admin_edit');
        }
    }

    public function admin_edit($id = null)
    {
        $sortie = false;
        if (empty($this->data)) {
            $acteur=$this->Acteur->read(null, $id);
            
            if (!empty($acteur['Acteur']['date_naissance']))
                $acteur['Acteur']['date_naissance'] = date('d/m/Y', strtotime($acteur['Acteur']['date_naissance']));
            
            $this->set('acteurs', $this->Acteur->generateListElus());
            $this->set('acteur', $acteur);
            if (empty($acteur)) {
                $this->Session->setFlash('Invalide id d\'acteur', 'growl');
                $sortie = true;
            } else
                $this->set('selectedServices', $this->_selectedArray($acteur['Service']));
            
            if (!$this->request->data) {
                $this->request->data = $acteur;
            }
        } else {
            $this->request->data['Acteur']['date_naissance'] = CakeTime::format( $this->data['date'], '%Y-%m-%d 00:00:00');
            if ($this->_controleEtSauve()) {
                $this->Session->setFlash('L\'acteur \'' . $this->request->data['Acteur']['prenom'] . ' ' . $this->request->data['Acteur']['nom'] . '\' a été modifié', 'growl');
                $sortie = true;
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
                if (array_key_exists('Service', $this->request->data))
                    $this->set('selectedServices', $this->request->data['Service']['Service']);
                else
                    $this->set('selectedServices', null);
            }
        }
        if ($sortie)
            $this->redirect(array('action'=>'index'));
        else {
            $this->Acteur->Typeacteur->recursive = 0;
            $this->set('typeacteurs', $this->Acteur->Typeacteur->find('all', array('fields' => array('id', 'nom', 'elu'))));
            $this->set('services', $this->Acteur->Service->generateTreeList(array('Service.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
            if (isset($date)) $this->set('date', $date);
        }
    }

    public function _controleEtSauve()
    {
        if (!empty($this->request->data['Acteur']['typeacteur_id'])) {
            if ($this->Acteur->Typeacteur->field('elu', 'id = ' . $this->request->data['Acteur']['typeacteur_id'])) {
                // pour un élu : initialisation de 'position' si non définie
                if (!$this->request->data['Acteur']['position'])
                    $this->request->data['Acteur']['position'] = $this->Acteur->getPostionMaxParActeursElus() + 1;
            } else {
                // pour un non élu : suppression des informations éventuellement saisies (service, position, date naissance)
                if (array_key_exists('Service', $this->request->data))
                    $this->request->data['Service']['Service'] = array();
                $this->request->data['Acteur']['position'] = 999;
            }
        }
        return $this->Acteur->save($this->request->data);
    }

    /* dans le controleur car utilisé dans la vue index pour l'affichage */
    public function _isDeletable($acteur, &$message)
    {
        if ($this->Deliberation->find('count', array('conditions' => array('rapporteur_id' => $acteur['Acteur']['id'])))) {
            $message = 'L\'acteur \'' . $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . '\' ne peut pas être supprimé car il est le rapporteur de délibérations';
            return false;
        }
        if ($this->Vote->find('count', array('conditions' => array('acteur_id' => $acteur['Acteur']['id'])))) {
            $message = 'L\'acteur \'' . $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . '\' ne peut pas être supprimé car il est le rapporteur de délibérations';
            return false;
        }
        return true;
    }

    public function admin_delete($id = null)
    {
        $acteur = $this->Acteur->read('id, nom, prenom', $id);
        if (empty($acteur))
            $this->Session->setFlash('Invalide id d\'acteur');
        else {
            $this->Acteur->id = $id;
            $this->Acteur->saveField('actif', 0);
        }
        $this->redirect(array('action'=>'index'));
    }
    
    public function beforeFilter() {
        parent::beforeFilter();
        
        /*$this->Auth->mapActions(array(
            'create' => array('add'),
            'view' => array('index', 'view')
        ));*/
        
    }

}
