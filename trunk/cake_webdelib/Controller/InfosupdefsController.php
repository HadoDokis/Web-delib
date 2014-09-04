<?php
class InfosupdefsController extends AppController
{
	public $uses = array( 'Infosupdef', 'Profil');
	public $helpers = array();
    public $components = array('Filtre');

	// Gestion des droits : identiques aux droits de l'index
	public $commeDroit = array(
		'add' => 'Infosupdefs:index',
		'edit' => 'Infosupdefs:index',
		'delete' => 'Infosupdefs:index',
		'view' => 'Infosupdefs:index',
		'changerOrdre' => 'Infosupdefs:index'
	);
	
	function beforeFilter() {
		$this->set('Infosupdef',$this->Infosupdef);
		parent::beforeFilter();
	}

	function index() {
		$this->Filtre->initialisation($this->name.':'.$this->request->action, $this->request->data);
		$conditions =  $this->Filtre->conditions();
		if (!$this->Filtre->critereExists('Actif')) $conditions['actif'] = 1;
		$conditions['model'] = 'Deliberation';
		$this->request->data = $this->Infosupdef->find('all', array(
			'recursive' => -1 ,
			'conditions' => $conditions,
			'order' => 'ordre'));
		$this->set('titre', 'Liste des informations supplémentaires des projets');
		$this->set('lienAdd', '/infosupdefs/add/Deliberation');
		if (!$this->Filtre->critereExists()) {
			$this->Filtre->addCritere('Actif', array('field' => 'Infosupdef.actif',
				'inputOptions' => array(
					'label'=>__('Active', true),
					'empty' =>'Toutes',
					'options' => array(1 => 'Oui', 0 => 'Non'))));
			$this->Filtre->setCritere('Actif', 1);
		}
	}

	function index_seance() {
		$this->Filtre->initialisation($this->name.':'.$this->request->action, $this->request->data);
		$conditions =  $this->Filtre->conditions();
		if (!$this->Filtre->critereExists('Actif')) $conditions['actif'] = 1;
		$conditions['model'] = 'Seance';
		$this->data = $this->Infosupdef->find('all', array(
			'recursive' => -1 ,
			'conditions' => $conditions,
			'order' => 'ordre'));
		$this->set('titre', 'Liste des informations supplémentaires des séances');
		$this->set('lienAdd', '/infosupdefs/add/Seance');
		if (!$this->Filtre->critereExists()) {
			$this->Filtre->addCritere('Actif', array('field' => 'Infosupdef.actif',
				'inputOptions' => array(
					'label'=>__('Active', true),
					'empty' =>'toutes',
					'options' => array(1 => 'Oui', 0 => 'Non'))));
			$this->Filtre->setCritere('Actif', 1);
		}
		$this->render('index');
	}


	function view($id = null) {
		$this->request->data = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($this->data)) {
			$this->Session->setFlash('Invalide id pour l\'information supplémentaire : édition impossible', 'growl');
			$this->redirect($this->referer());
		} else {
			$this->request->data['Infosupdef']['libelleType'] = $this->Infosupdef->libelleType($this->data['Infosupdef']['type']);
			$this->request->data['Infosupdef']['libelleRecherche'] = $this->Infosupdef->libelleRecherche($this->data['Infosupdef']['recherche']);
			$this->request->data['Infosupdef']['libelleActif'] = $this->Infosupdef->libelleActif($this->data['Infosupdef']['actif']);
			$this->set('titre', 'Détails de l\'information supplémentaire de '.($this->data['Infosupdef']['model']=='Deliberation'?'projet':'séance'));
			$this->set('lienRetour', '/infosupdefs/'.($this->data['Infosupdef']['model']=='Deliberation'?'index':'index_seance'));
		}
	}

	function add($model=null) {
		// initialisations
		$sortie = false;
		$codePropose = '';
		
		if (empty($this->request->data)) {
			$this->request->data['Infosupdef']['model'] = $model;
			$this->request->data['Infosupdef']['actif'] = true;
		} else {
			// traitement de la valeur par defaut
			if ($this->request->data['Infosupdef']['type'] == 'date')
				$this->request->data['Infosupdef']['val_initiale'] = $this->request->data['Infosupdef']['val_initiale_date'];
			elseif ($this->request->data['Infosupdef']['type'] == 'boolean')
				$this->request->data['Infosupdef']['val_initiale'] = $this->request->data['Infosupdef']['val_initiale_boolean'];
			elseif ($this->request->data['Infosupdef']['type'] == 'file')
				$this->request->data['Infosupdef']['val_initiale'] = '';

			if ($this->{$this->modelClass}->save($this->request->data)) {
				$this->Session->setFlash('L\'information supplémentaire \''.$this->request->data['Infosupdef']['nom'].'\' a été ajoutée', 'growl');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
				$codePropose = Inflector::variable($this->request->data['Infosupdef']['code']);
			}
		}
		$lienRetour = '/infosupdefs/'.($this->request->data['Infosupdef']['model']=='Deliberation'?'index':'index_seance');
		if ($sortie)
			return $this->redirect($lienRetour);
		else {
			$this->set('titre', 'Ajout d\'une information supplémentaire de '.($this->request->data['Infosupdef']['model']=='Deliberation'?'délibération':'séance'));
			$this->set('types', $this->{$this->modelClass}->generateListType());
			$this->set('listEditBoolean', $this->{$this->modelClass}->listEditBoolean);
			$this->set('codePropose', $codePropose);
            $this->set('profils', $this->Profil->find('list', array('conditions' => array ('Profil.actif' => 1))));
			$this->set('lienRetour', $lienRetour);

			$this->render('edit');
		}
	}

	function edit($id = null) {
		// initialisations
		$sortie = false;
		$codePropose = '';

		if (empty($this->request->data)) {
			$this->{$this->modelClass}->Behaviors->attach('Containable');
			$this->request->data = $this->{$this->modelClass}->find('first', array(
				'contain' => array('Profil'),
				'conditions' => array("Infosupdef.id" => $id)));
			if (empty($this->request->data)) {
				$this->Session->setFlash('Invalide id pour l\'information supplémentaire : édition impossible', 'growl');
				$sortie = true;
			}
			// traitement de la valeur par defaut pour les dates et les booleens
			if ($this->request->data['Infosupdef']['type'] == 'date')
				$this->request->data['Infosupdef']['val_initiale_date'] = $this->request->data['Infosupdef']['val_initiale'];
			elseif ($this->request->data['Infosupdef']['type'] == 'boolean')
				$this->request->data['Infosupdef']['val_initiale_boolean'] = $this->request->data['Infosupdef']['val_initiale'];
		} else {
			// traitement de la valeur par defaut
			if ($this->request->data['Infosupdef']['type'] == 'date')
				$this->request->data['Infosupdef']['val_initiale'] = $this->request->data['Infosupdef']['val_initiale_date'];
			elseif ($this->request->data['Infosupdef']['type'] == 'boolean')
				$this->request->data['Infosupdef']['val_initiale'] = $this->request->data['Infosupdef']['val_initiale_boolean'];
			elseif ($this->request->data['Infosupdef']['type'] == 'file')
				$this->request->data['Infosupdef']['val_initiale'] = '';

			if ($this->{$this->modelClass}->save($this->request->data)) {
				$this->Session->setFlash('L\'information supplémentaire \''.$this->request->data['Infosupdef']['nom'].'\' a été modifiée', 'growl');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
				$codePropose = Inflector::variable($this->request->data['Infosupdef']['code']);
			}
		}
		$lienRetour =array('controllers'=>'infosupdefs',
                    'action' => $this->request->data['Infosupdef']['model']=='Deliberation'?'index':'index_seance');
		if ($sortie)
			$this->redirect($lienRetour);
		else {
			$this->set('titre', 'Edition d\'une information supplémentaire de '.($this->request->data['Infosupdef']['model']=='Deliberation'?'délibération':'séance'));
			$this->set('types', $this->{$this->modelClass}->generateListType());
			$this->set('listEditBoolean', $this->{$this->modelClass}->listEditBoolean);
			$this->set('codePropose', $codePropose);
			$this->set('profils', $this->Profil->find('list', array('conditions' => array ('Profil.actif' => 1))));
			$this->set('lienRetour', $lienRetour);
		}
	}

	function delete($id = null) {
		$data = $this->{$this->modelClass}->find('first', array(
			'recursive' => -1,
			'conditions' => array('id'=>$id)));
		if (empty($data))
			$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : suppression impossible', 'growl');
		elseif (!$this->{$this->modelClass}->isDeletable($id))
			$this->Session->setFlash('Cette information supplémentaire ne peut pas être supprimée', 'growl');
		elseif ($this->{$this->modelClass}->delete($id)) {
			$this->{$this->modelClass}->Infosuplistedef->delList($id);
			$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$data['Infosupdef']['nom'].'\' a &eacute;t&eacute; supprim&eacute;e', 'growl');
		}


		$this->redirect($this->referer());
	}

	function changerOrdre($id = null, $suivant = true) {
		$infoSup = $this->{$this->modelClass}->find('first', array(
			'recursive'=>-1,
			'fields' => array('id', 'model'),
			'conditions' => array('id'=>$id)));
		if (empty($infoSup))
			$this->Session->setFlash('Invalide id : deplacement impossible.', 'growl');
		else
			$this->{$this->modelClass}->invert($id, $suivant);

		$this->redirect($this->referer());
	}

}