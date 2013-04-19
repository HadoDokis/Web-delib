<?php
class InfosuplistedefsController extends AppController{
var $name = 'Infosuplistedefs';

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

/**
 * liste des éléments de la liste d'une information supplémentaire de type 'list'
 */
function index($infosupdefId) {
	$sortie = false;
	// recherche de l'infosup
	//$infosupdef = $this->{$this->modelClass}->Infosupdef->findById($infosupdefId, null, null, -1);
	$infosupdef = $this->{$this->modelClass}->Infosupdef->find('first', array('conditions' => array('Infosupdef.id' => $infosupdefId),
                                                                                  'recursive'  => -1));
	if (empty($infosupdef)) {
		$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : &eacute;dition impossible');
		$sortie = true;
	} elseif ($infosupdef['Infosupdef']['type'] != 'list') {
		$this->Session->setFlash('Cette information suppl&eacute;mentaire n\'est pas de type liste : &eacute;dition impossible');
		$sortie = true;
	}
	if ($sortie)
		$this->redirect('/infosupdefs/index');
	else {
		$this->set('infosupdef', $infosupdef);
		$this->request->data = $this->{$this->modelClass}->find('all', array('conditions' => array('actif' => 1,
                                                                                                           'infosupdef_id' => $infosupdefId),
                                                                                      'order'     => 'ordre',
                                                                                      'recursive' => -1));
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
			$this->Session->setFlash('Invalide id pour l\'information suppl&eacute;mentaire : &eacute;dition impossible');
			$redirect = '/infosupdefs/index';
			$sortie = true;
		} elseif ($infosupdef['Infosupdef']['type'] != 'list') {
			$this->Session->setFlash('Cette information suppl&eacute;mentaire n\'est pas de type liste : &eacute;dition impossible');
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
			$this->Session->setFlash('L\'&eacute;l&eacute;ment \''.$this->data['Infosuplistedef']['nom'].'\' a &eacute;t&eacute; ajout&eacute;');
			$redirect = '/infosuplistedefs/index/'.$this->data['Infosuplistedef']['infosupdef_id'];
			$sortie = true;
		} else {
			$infosupdef = $this->{$this->modelClass}->Infosupdef->findById($this->data['Infosuplistedef']['infosupdef_id'], null, null, -1);
			$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
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
			$this->Session->setFlash('Invalide id pour l\'&eacute;l&eacute;ment de l\'information suppl&eacute;mentaire : &eacute;dition impossible');
			$redirect = '/infosupdefs/index';
			$sortie = true;
		}
	} else {
		if ($this->{$this->modelClass}->save($this->data)) {
			$this->Session->setFlash('L\'&eacute;l&eacute;ment \''.$this->data['Infosuplistedef']['nom'].'\' a &eacute;t&eacute; modifi&eacute;');
			$redirect = '/infosuplistedefs/index/'.$this->data['Infosuplistedef']['infosupdef_id'];
			$sortie = true;
		} else {
			$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
	}
	if ($sortie)
		$this->redirect($redirect);
	else {
		$this->set('infosupdef', $this->{$this->modelClass}->Infosupdef->findById($this->data['Infosuplistedef']['infosupdef_id'], null, null, -1));
	}
}

/**
 * Rend inactif l'élément $id de la liste d'une info supplémentaire
 */
function delete($id=0) {
	// lecture de l'infosuplistedef
	$this->request->data = $this->{$this->modelClass}->find('first', array('conditions' => array('id' =>$id), 
                                                                       'recursive' => -1));
	if (empty($this->data)) {
		$this->Session->setFlash('Invalide id pour l\'&eacute;l&eacute;ment de l\'information suppl&eacute;mentaire : suppression impossible');
		$redirect = '/infosupdefs/index';
		$sortie = true;
	} else {
		$this->request->data['Infosuplistedef']['actif'] = false;
		$this->request->data['Infosuplistedef']['ordre'] = 0;
		if ($this->{$this->modelClass}->save($this->data)) {
			$this->{$this->modelClass}->reOrdonne($this->data['Infosuplistedef']['infosupdef_id']);
			$this->Session->setFlash('L\'&eacute;l&eacute;ment \''.$this->data['Infosuplistedef']['nom'].'\' a &eacute;t&eacute; supprim&eacute;');
			$redirect = '/infosuplistedefs/index/'.$this->data['Infosuplistedef']['infosupdef_id'];
			$sortie = true;
		} else {
			$this->Session->setFlash('Erreur lors de la suppression.');
		}
	}
	$this->redirect($redirect);
}


function test($infosupdef_id) {
    $this->{$this->modelClass}->reOrdonne($infosupdef_id);
}

function test2($infosupdef_id, $var1, $var2) {
    $this->{$this->modelClass}->reOrdonne($infosupdef_id, $var1, $var2);
}



/**
 * intervertit l'élément de la liste $id avec son suivant ou son précédent
 */
function changerOrdre($id = null, $suivant = true){
	$this->request->data = $this->{$this->modelClass}->find('first', array('conditions' => array('id' =>$id), 
                                                                       'recursive' => -1));
	if (empty($this->data))
		$this->Session->setFlash('Invalide id : deplacement impossible.');
	else
		$this->{$this->modelClass}->invert($id, $suivant);

	$this->redirect('/infosuplistedefs/index/'.$this->data['Infosuplistedef']['infosupdef_id']);
}

}?>
