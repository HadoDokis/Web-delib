<?php
class CompteursController extends AppController
{
  var $name = 'Compteurs';
  var $components = array('Security');


  // Gestion des droits
  var $commeDroit = array(
    'edit' => 'Compteurs:index',
    'view' => 'Compteurs:index',
    'add' => 'Compteurs:index',
    'delete' => 'Compteurs:index');

	function beforeFilter() {
		if (property_exists($this, 'demandePost'))
		call_user_func_array(array($this->Security, 'requirePost'), $this->demandePost);
		parent::beforeFilter();
    }

	function index() {
		$this->set('compteurs', $this->Compteur->find('all', array('recursive' => 1)));
	}

	function view($id = null) {
		if (!$this->Compteur->exists()) {
			$this->Session->setFlash('Invalide id pour le compteur', 'growl',array('type'=>'erreur'));
			$this->redirect('/compteurs/index');
		} else
			$this->set('compteur', $this->Compteur->read(null, $id));
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			if ($this->Compteur->save($this->data)) {
				$this->Session->setFlash('Le compteur \''.$this->data['Compteur']['nom'].'\' a &eacute;t&eacute; ajout&eacute;', 'growl');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
		}
		if ($sortie)
			$this->redirect('/compteurs/index');
		else {
			$this->set('sequences', $this->Compteur->Sequence->find('list'));
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->Compteur->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour le compteur', 'growl',array('type'=>'erreur'));
				$sortie = true;
			}
		} else {
			if ($this->Compteur->save($this->data)) {
				$this->Session->setFlash('Le compteur \''.$this->data['Compteur']['nom'].'\' a &eacute;t&eacute; modifi&eacute;', 'growl');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
		}
		if ($sortie)
			$this->redirect('/compteurs/index');
		else
			$this->set('sequences', $this->Compteur->Sequence->find('list'));
	}

	function delete($id = null) {
		$compteur = $this->Compteur->read('id, nom', $id);
		if (empty($compteur)) {
			$this->Session->setFlash('Invalide id pour le compteur', 'growl',array('type'=>'erreur'));
		}
		elseif (!empty($compteur['Typeseance'])) {
			$this->Session->setFlash('Le compteur \''.$compteur['Compteur']['nom'].'\' est utilis&eacute; par un type de s&eacute;ance. Suppression impossible.', 'growl',array('type'=>'erreur'));
		}
		elseif ($this->Compteur->delete($id)) {
			$this->Session->setFlash('La compteur \''.$compteur['Compteur']['nom'].'\' a &eacute;t&eacute; supprim&eacute;', 'growl');
		}
		$this->redirect('/compteurs/index');
  }

}
?>