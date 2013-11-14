<?php
class InfosuplistedefsController extends AppController{
var $name = 'Infosuplistedefs';

var $components = array('Filtre');

// Gestion des droits : identiques aux droits de l'index
var $commeDroit = array(
	'index' => 'Infosupdefs:index',
	'add' => 'Infosupdefs:index',
	'edit' => 'Infosupdefs:index',
	'delete' => 'Infosupdefs:index',
	'changerOrdre' => 'Infosupdefs:index',
        'test' => 'Infosupdefs:index',
        'test2' => 'Infosupdefs:index'
	);

function beforeFilter() {
	$this->set('Infosuplistedef',$this->Infosuplistedef);
	parent::beforeFilter();
}

/**
 * liste des éléments de la liste d'une information supplémentaire de type 'list'
 */
function index($infosupdefId) {
	$sortie = false;
	// lecture de l'infosup
	$infosupdef = $this->{$this->modelClass}->Infosupdef->find('first', array(
		'recursive' => -1,
		'conditions' => array('id' => $infosupdefId)));
	if (empty($infosupdef)) {
		$this->Session->setFlash('Invalide id pour l\'information supplémentaire : édition impossible', 'growl', array('type'=>'erreur'));
		$sortie = true;
	} elseif ($infosupdef['Infosupdef']['type'] != 'list') {
		$this->Session->setFlash('Cette information supplémentaire n\'est pas de type liste : édition impossible', 'growl', array('type'=>'erreur'));
		$sortie = true;
	}
	if ($sortie)
		$this->redirect('/infosupdefs/index');
	else {
		$this->set('infosupdef', $infosupdef);
		$this->Filtre->initialisation($this->name.':'.$this->request->action, $this->request->data, array(
			'url'=>array('controller'=> $this->params['controller'], 'action'=>$this->action, $infosupdefId)));
		$conditions =  $this->Filtre->conditions();
		$conditions['infosupdef_id'] = $infosupdefId;
		$this->request->data = $this->{$this->modelClass}->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'order' => 'ordre'));
		if (!$this->Filtre->critereExists()) {
			$this->Filtre->addCritere('Actif', array('field' => 'Infosuplistedef.actif',
				'inputOptions' => array(
					'label'=>__('Actif', true),
					'empty' =>'Tous',
					'options' => array(1 => 'Oui', 0 => 'Non'))));
			$this->Filtre->setCritere('Actif', '');
		}
	}
}

/**
 * Ajoute un éléments à la liste de l'info. sup. $infosupId
 */
function add($infosupId=0) {
	$sortie = false;

	if (empty($this->data)) {
		// recherche de l'infosupdef
		$infosupdef = $this->{$this->modelClass}->Infosupdef->find('first', array('conditions' => array('Infosupdef.id'=> $infosupId) ,
                                                                                          'fields'     => array('id', 'type', 'nom'),
                                                                                           'recursive' => -1));
		if (empty($infosupdef)) {
			$this->Session->setFlash('Invalide id pour l\'information supplémentaire : édition impossible', 'growl', array('type'=>'erreur'));
			$redirect = '/infosupdefs/index';
			$sortie = true;
		} elseif ($infosupdef['Infosupdef']['type'] != 'list') {
			$this->Session->setFlash('Cette information supplémentaire n\'est pas de type liste : édition impossible', 'growl', array('type'=>'erreur'));
			$redirect = '/infosupdefs/index';
			$sortie = true;
		} else {
			// initialisations
			$this->request->data['Infosuplistedef']['infosupdef_id'] = $infosupId;
			$this->request->data['Infosuplistedef']['actif'] = true;
		}
	} else {
                $nb_liste = $this->Infosuplistedef->find('count', array('conditions' => array('Infosuplistedef.infosupdef_id' => $infosupId),
                                                                        'recursive'  => -1 ));
                $this->request->data['Infosuplistedef']['ordre'] = $nb_liste+1;
		if ($this->Infosuplistedef->save($this->data['Infosuplistedef'])) {
			$this->Session->setFlash('L\'élément \''.$this->data['Infosuplistedef']['nom'].'\' a été ajouté', 'growl');
			$redirect = '/infosuplistedefs/index/'.$this->data['Infosuplistedef']['infosupdef_id'];
			$sortie = true;
		} else {
			$infosupdef = $this->{$this->modelClass}->Infosupdef->findById($this->data['Infosuplistedef']['infosupdef_id'], null, null, -1);
			$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
		}
	}

	if ($sortie)
		$this->redirect($redirect);
	else {
		$this->set('infosupdef', $infosupdef);
		$this->render('edit');
	}
}

/**
 * Edition de l'élément $id de la liste d'une info supplémentaire
 */
function edit($id=0) {
	$sortie = false;

	if (empty($this->data)) {
		// lecture de l'infosuplistedef
		$this->data = $this->{$this->modelClass}->findById($id, null, null, -1);
		if (empty($this->data)) {
			$this->Session->setFlash('Invalide id pour l\'élément de l\'information supplémentaire : édition impossible', 'growl', array('type'=>'erreur'));
			$redirect = '/infosupdefs/index';
			$sortie = true;
		}
	} else {
		if ($this->{$this->modelClass}->save($this->data)) {
			$this->Session->setFlash('L\'élément \''.$this->data['Infosuplistedef']['nom'].'\' a été modifié', 'growl');
			$redirect = '/infosuplistedefs/index/'.$this->data['Infosuplistedef']['infosupdef_id'];
			$sortie = true;
		} else {
			$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
		}
	}
	if ($sortie)
		$this->redirect($redirect);
	else {
		$this->set('infosupdef', $this->{$this->modelClass}->Infosupdef->findById($this->data['Infosuplistedef']['infosupdef_id'], null, null, -1));
	}
}

/**
 * supprime l'élément $id de la liste d'une info supplémentaire
 */
function delete($id=0) {
	$data = $this->{$this->modelClass}->find('first', array(
		'recursive' => -1,
		'conditions' => array('id'=>$id)));
	if (empty($data))
		$this->Session->setFlash('Invalide id pour l\'élément de l\'information supplémentaire : suppression impossible', 'growl');
	elseif (!$this->{$this->modelClass}->isDeletable($id))
		$this->Session->setFlash('Cet élément de l\'information supplémentaire ne peut pas être supprimé', 'growl');
	elseif ($this->{$this->modelClass}->delete($id)) {
		$this->{$this->modelClass}->reOrdonne($data['Infosuplistedef']['infosupdef_id']);
		$this->Session->setFlash('L\'information supplémentaire \''.$data['Infosuplistedef']['nom'].'\' a été supprimée', 'growl');
	}
	else
		$this->Session->setFlash('Erreur lors de la suppression.', 'growl', array('type'=>'erreur'));

	$this->redirect($this->referer());
}


/**
 * intervertit l'élément de la liste $id avec son suivant ou son précédent
 */
function changerOrdre($id = null, $suivant = true){
	$data = $this->{$this->modelClass}->find('first', array(
		'recursive' => -1,
		'conditions' => array('id' => $id)));
	if (empty($data))
		$this->Session->setFlash('Invalide id : deplacement impossible.', 'growl', array('type'=>'erreur'));
	else
		$this->{$this->modelClass}->invert($id, $suivant);

	$this->redirect('/infosuplistedefs/index/'.$data['Infosuplistedef']['infosupdef_id']);
}

}?>
