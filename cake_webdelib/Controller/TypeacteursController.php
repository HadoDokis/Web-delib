<?php
class TypeacteursController extends AppController
{
	var $name = 'Typeacteurs';
        public $helpers = array('Html2');
	// Gestion des droits : identiques aux droits des acteurs
	var $commeDroit = array(
		'add' => 'Typeacteurs:index',
		'edit' => 'Typeacteurs:index',
		'delete' => 'Typeacteurs:index',
		'view' => 'Typeacteurs:index'
		);

	function index()
	{
		$this->set('typeacteurs', $this->Typeacteur->find('all', array('recursive' => -1 )));
	}

	function view($id = null) 	{
		$typeacteur = $this->Typeacteur->find('first', array('conditions' => array('Typeacteur.id'=> $id)));
		if (empty($typeacteur)) {
			$this->Session->setFlash('Invalide id pour le type d\'acteur', 'growl', array('type'=>'erreur'));
            $this->redirect(array('action'=>'index'));
		} else
			$this->set('typeacteur', $typeacteur);
	}

	function add() {
		$sortie = false;
		if (!empty($this->request->data)) {
			$this->Typeacteur->create($this->request->data);
			if ($this->Typeacteur->save()) {
				$this->Session->setFlash('Le type d\'acteur \''.$this->request->data['Typeacteur']['nom'].'\' a été ajouté', 'growl');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
		}
		if ($sortie)
            $this->redirect(array('action'=>'index'));
		else {
			$this->set('eluNonElu',array('1'=>'élu','0'=>'non élu'));
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->request->data)) {
			$this->request->data = $this->Typeacteur->read(null, $id);
			if (empty($this->request->data)) {
				$this->Session->setFlash('Invalide id pour le type d\'acteur', 'growl', array('type'=>'erreur'));
				$sortie = true;
			}
		} else {
			if ($this->Typeacteur->save($this->request->data)) {
				$this->Session->setFlash('Le type d\'acteur \''.$this->request->data['Typeacteur']['nom'].'\' a été modifié', 'growl');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
		}
		if ($sortie)
            $this->redirect(array('action'=>'index'));
		else
			$this->set('eluNonElu', array('1'=>'élu','0'=>'non élu'));
	}

	function delete($id = null) {
		$typeacteur = $this->Typeacteur->read('id, nom', $id);
		if (empty($typeacteur)) {
			$this->Session->setFlash('Invalide id pour le type d\'acteur', 'growl', array('type'=>'erreur'));
		}
		elseif (!empty($typeacteur['Acteur'])) {
			$this->Session->setFlash('Le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' est utilisé par un acteur. Suppression impossible.', 'growl', array('type'=>'erreur'));
		}
		elseif ($this->Typeacteur->delete($id)) {
			$this->Session->setFlash('Le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' a été supprimé', 'growl');
		}
        $this->redirect(array('action'=>'index'));
	}
}
