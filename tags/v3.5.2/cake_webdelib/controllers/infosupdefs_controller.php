<?php
class InfosupdefsController extends AppController
{
	var $name = 'Infosupdefs';

	var $helpers = array('Html', 'Html2');

	// Gestion des droits : identiques aux droits de l'index
	var $commeDroit = array(
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
		$this->data = $this->Infosupdef->find('all', array(
			'recursive' => -1 ,
			'conditions' => array('model' => 'Deliberation'),
			'order' => 'ordre'));
		$this->set('titre', 'Liste des informations suppl&eacute;mentaires des projets');
		$this->set('lienAdd', '/infosupdefs/add/Deliberation');
	}

	function index_seance() {
		$this->data = $this->Infosupdef->find('all', array(
			'recursive' => -1 ,
			'conditions' => array('model' => 'Seance'),
			'order' => 'ordre'));
		$this->set('titre', 'Liste des informations suppl&eacute;mentaires des s&eacute;ances');
		$this->set('lienAdd', '/infosupdefs/add/Seance');
		$this->render('index');
	}


	function view($id = null) {
		$this->data = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($this->data)) {
			$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : &eacute;dition impossible', 'growl');
			$this->redirect('/infosupdefs/index');
		} else {
			$this->data['Infosupdef']['libelleType'] = $this->Infosupdef->libelleType($this->data['Infosupdef']['type']);
			$this->data['Infosupdef']['libelleRecherche'] = $this->Infosupdef->libelleRecherche($this->data['Infosupdef']['recherche']);
			$this->set('titre', 'Fiche information suppl&eacute;mentaire de '.($this->data['Infosupdef']['model']=='Deliberation'?'d&eacute;lib&eacute;ration':'s&eacute;ance'));
			$this->set('lienRetour', '/infosupdefs/'.($this->data['Infosupdef']['model']=='Deliberation'?'index':'index_seance'));
		}
	}

	function add($model=null) {
		// initialisations
		$sortie = false;
		$codePropose = '';
		
		if (empty($this->data)) {
			$this->data['Infosupdef']['model'] = $model;
		} else {
			/* traitement de la valeur par defaut */
			if ($this->data['Infosupdef']['type'] == 'date')
				$this->data['Infosupdef']['val_initiale'] = $this->data['Infosupdef']['val_initiale_date'];
			elseif ($this->data['Infosupdef']['type'] == 'boolean')
				$this->data['Infosupdef']['val_initiale'] = $this->data['Infosupdef']['val_initiale_boolean'];
			elseif ($this->data['Infosupdef']['type'] == 'file')
				$this->data['Infosupdef']['val_initiale'] = '';

			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$this->data['Infosupdef']['nom'].'\' a &eacute;t&eacute; ajout&eacute;e', 'growl');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
				$codePropose = Inflector::variable($this->data['Infosupdef']['code']);
			}
		}
		$lienRetour = '/infosupdefs/'.($this->data['Infosupdef']['model']=='Deliberation'?'index':'index_seance');
		if ($sortie)
			$this->redirect($lienRetour);
		else {
			$this->set('types', $this->{$this->modelClass}->generateListType());
			$this->set('listEditBoolean', $this->{$this->modelClass}->listEditBoolean);
			$this->set('codePropose', $codePropose);
			$this->set('titre', 'Ajout d\'une information suppl&eacute;mentaire de '.($this->data['Infosupdef']['model']=='Deliberation'?'d&eacute;lib&eacute;ration':'s&eacute;ance'));
			$this->set('lienRetour', $lienRetour);

			$this->render('edit');
		}
	}

	function edit($id = null) {
		// initialisations
		$sortie = false;
		$codePropose = '';

		if (empty($this->data)) {
			$this->data = $this->{$this->modelClass}->findById($id, null, null, -1);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : &eacute;dition impossible', 'growl');
				$sortie = true;
			}
			/* traitement de la valeur par defaut pour les dates et les booleens */
			if ($this->data['Infosupdef']['type'] == 'date')
				$this->data['Infosupdef']['val_initiale_date'] = $this->data['Infosupdef']['val_initiale'];
			elseif ($this->data['Infosupdef']['type'] == 'boolean')
				$this->data['Infosupdef']['val_initiale_boolean'] = $this->data['Infosupdef']['val_initiale'];
		} else {
			// traitement de la valeur par defaut
			if ($this->data['Infosupdef']['type'] == 'date')
				$this->data['Infosupdef']['val_initiale'] = $this->data['Infosupdef']['val_initiale_date'];
			elseif ($this->data['Infosupdef']['type'] == 'boolean')
				$this->data['Infosupdef']['val_initiale'] = $this->data['Infosupdef']['val_initiale_boolean'];
			elseif ($this->data['Infosupdef']['type'] == 'file')
				$this->data['Infosupdef']['val_initiale'] = '';

			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$this->data['Infosupdef']['nom'].'\' a &eacute;t&eacute; modifi&eacute;e', 'growl');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
				$codePropose = Inflector::variable($this->data['Infosupdef']['code']);
			}
		}
		$lienRetour = '/infosupdefs/'.($this->data['Infosupdef']['model']=='Deliberation'?'index':'index_seance');
		if ($sortie)
			$this->redirect($lienRetour);
		else {
			$this->set('types', $this->{$this->modelClass}->generateListType());
			$this->set('listEditBoolean', $this->{$this->modelClass}->listEditBoolean);
			$this->set('codePropose', $codePropose);
			$this->set('titre', 'Edition d\'une information suppl&eacute;mentaire de '.($this->data['Infosupdef']['model']=='Deliberation'?'d&eacute;lib&eacute;ration':'s&eacute;ance'));
			$this->set('lienRetour', $lienRetour);
		}
	}

	function delete($id = null) {
		$messageErreur = '';
		$aSupprimer = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($aSupprimer))
			$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : suppression impossible', 'growl');
		elseif (!$this->{$this->modelClass}->isDeletable($aSupprimer, $messageErreur))
			$this->Session->setFlash($messageErreur, 'growl');
		elseif ($this->{$this->modelClass}->delete($id)) {
			$this->{$this->modelClass}->Infosuplistedef->delList($id);
			$this->Session->setFlash('L\'information suppl&eacute;mentaire \''.$aSupprimer['Infosupdef']['nom'].'\' a &eacute;t&eacute; supprim&eacute;e', 'growl');
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
?>
