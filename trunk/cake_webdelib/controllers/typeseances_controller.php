<?php
class TypeseancesController extends AppController {

	var $name = 'Typeseances';
	var $uses = array('Typeseance', 'Model', 'Compteur', 'Seance');

	// Gestion des droits
	var $commeDroit = array(
		'edit'=>'Typeseances:index',
		'add'=>'Typeseances:index',
		'delete'=>'Typeseances:index',
		'view'=>'Typeseances:index'
	);

	function index() {
		$this->set('typeseances', $this->Typeseance->findAll());
		$this->set('Typeseance', $this->Typeseance);
		$this->set('Typeseances', $this);
	}

	function view($id = null) {
		$typeseance = $this->Typeseance->read(null, $id);
		if (empty($typeseance)) {
			$this->Session->setFlash('Invalide id pour le type de seance.');
			$this->redirect('/typeseances/index');
		}
		$this->set('typeseance', $typeseance);
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			if ($this->Typeseance->save($this->data)) {
				$this->Session->setFlash('Le type de seance \''.$this->data['Typeseance']['libelle'].'\' a &eacute;t&eacute; sauvegard&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/typeseances/index');
		else {
			$this->set('compteurs', $this->Typeseance->Compteur->find('list'));
			$this->set('models', $this->Model->find('list',array('conditions'=>array('type'=>'Document'),'fields' => array('Model.id','Model.modele'))));
			$this->set('actions', array(0 => $this->Typeseance->libelleAction(0, true), 1 => $this->Typeseance->libelleAction(1, true)));
			$this->set('typeacteurs', $this->Typeseance->Typeacteur->find('list'));
			$this->set('selectedTypeacteurs', null);
			$this->set('acteurs', $this->Typeseance->Acteur->generateList('nom'));
			$this->set('selectedActeurs', null);
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->Typeseance->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour le type de s&eacute;ance');
				$sortie = true;
			} else {
				$this->set('selectedTypeacteurs', $this->_selectedArray($this->data['Typeacteur']));
				$this->set('selectedActeurs', $this->_selectedArray($this->data['Acteur']));
			}
		} else {
			if ($this->Typeseance->save($this->data)) {
				$this->Session->setFlash('Le type de s&eacute;ance \''.$this->data['Typeseance']['libelle'].'\' a &eacute;t&eacute; modifi&eacute;');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
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
			$this->redirect('/typeseances/index');
		else {
			$this->set('compteurs', $this->Typeseance->Compteur->find('list'));
			$this->set('models', $this->Model->find('list',array('conditions'=>array('type'=>'Document'), 'fields' => array('Model.id','Model.modele'))));
			$this->set('actions', array(0 => $this->Typeseance->libelleAction(0, true), 1 => $this->Typeseance->libelleAction(1, true)));
			$this->set('typeacteurs', $this->Typeseance->Typeacteur->find('list'));
			$this->set('acteurs', $this->Typeseance->Acteur->generateList('nom'));
		}
	}

/* dans le controleur car utilisé dans la vue index pour l'affichage */
	function _isDeletable($typeseance, &$message) {
		if ($this->Seance->findCount(array('type_id'=>$typeseance['Typeseance']['id']))) {
			$message = 'Le type de s&eacute;ance \''.$typeseance['Typeseance']['libelle'].'\' ne peut pas être supprim&eacute; car il est utilis&eacute; par une s&eacute;ance';
			return false;
		}
		return true;
	}

	function delete($id = null) {
		$messageErreur = '';
		$typeseance = $this->Typeseance->read('id, libelle', $id);
		if (empty($typeseance))
			$this->Session->setFlash('Invalide id pour le type de s&eacute;ance');
		elseif (!$this->_isDeletable($typeseance, $messageErreur))
			$this->Session->setFlash($messageErreur);
		elseif ($this->Typeseance->del($id))
			$this->Session->setFlash('Le type de s&eacute;ance \''.$typeseance['Typeseance']['libelle'].'\' a &eacute;t&eacute; supprim&eacute;');

		$this->redirect('/typeseances/index');
	}

}
?>
