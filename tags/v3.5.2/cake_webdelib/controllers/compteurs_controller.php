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
		$this->set('compteurs', $this->Compteur->findAll());
	}

	function view($id = null) {
		if (!$this->Compteur->exists()) {
			$this->Session->setFlash('Invalide id pour le compteur');
			$this->redirect('/compteurs/index');
		} else
			$this->set('compteur', $this->Compteur->read(null, $id));
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			if ($this->Compteur->save($this->data)) {
				$this->Session->setFlash('Le compteur \''.$this->data['Compteur']['nom'].'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
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
				$this->Session->setFlash('Invalide id pour le compteur');
				$sortie = true;
			}
		} else {
			if ($this->Compteur->save($this->data)) {
				$this->Session->setFlash('Le compteur \''.$this->data['Compteur']['nom'].'\' a &eacute;t&eacute; modifi&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/compteurs/index');
		else
			$this->set('sequences', $this->Compteur->Sequence->find('list'));
	}

	function delete($id = null) {
		$compteur = $this->Compteur->read('id, nom', $id);
		if (empty($compteur)) {
			$this->Session->setFlash('Invalide id pour le compteur');
		}
		elseif (!empty($compteur['Typeseance'])) {
			$this->Session->setFlash('Le compteur \''.$compteur['Compteur']['nom'].'\' est utilis&eacute; par un type de s&eacute;ance. Suppression impossible.');
		}
		elseif ($this->Compteur->del($id)) {
			$this->Session->setFlash('La compteur \''.$compteur['Compteur']['nom'].'\' a &eacute;t&eacute; supprim&eacute;');
		}
		$this->redirect('/compteurs/index');
  }

}
?>
