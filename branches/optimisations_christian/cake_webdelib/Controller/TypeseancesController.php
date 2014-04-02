<?php
class TypeseancesController extends AppController {

	var $name = 'Typeseances';
	var $uses = array('Typeseance', 'Typeacte', 'Model', 'Compteur', 'Seance');

	// Gestion des droits
	var $commeDroit = array(
		'edit'=>'Typeseances:index',
		'add'=>'Typeseances:index',
		'delete'=>'Typeseances:index',
		'view'=>'Typeseances:index'
	);

	function index() {
                $this->Typeseance->Behaviors->attach('Containable');
		$typeseances = $this->Typeseance->find('all', array('contain' =>array('Modelpvdetaille.modele', 'Modelpvdetaille.id',
                                                                                               'Modelpvsommaire.modele', 'Modelpvsommaire.id',
                                                                                               'Modelordredujour.modele', 'Modelordredujour.id',
                                                                                               'Modelconvocation.modele', 'Modelconvocation.id',
                                                                                               'Modeldeliberation.modele', 'Modeldeliberation.id',
                                                                                               'Modelprojet.modele', 'Modelprojet.id',
                                                                                               'Compteur.id', 'Compteur.nom', 'Acteur',
                                                                                               'Typeacteur', 'Typeacte' )));
		for($i=0; $i < count($typeseances); $i++) {
			$typeseances[$i]['Typeseance']['is_deletable'] = $this-> _isDeletable($typeseances[$i], $message);
			$typeseances[$i]['Typeseance']['action'] = $this->Typeseance->libelleAction($typeseances[$i]['Typeseance']['action'], true);
		}
		$this->set('typeseances',	$typeseances );
	}

	function view($id = null) {
		$typeseance = $this->Typeseance->find('first', array( 'conditions' => array('Typeseance.id' => $id),
                                                                      'contain' =>array('Modelpvdetaille.modele', 'Modelpvdetaille.id',
                                                                                               'Modelpvsommaire.modele', 'Modelpvsommaire.id',
                                                                                               'Modelordredujour.modele', 'Modelordredujour.id',
                                                                                               'Modelconvocation.modele', 'Modelconvocation.id',
                                                                                               'Modeldeliberation.modele', 'Modeldeliberation.id',
                                                                                               'Modelprojet.modele', 'Modelprojet.id',
                                                                                               'Compteur.id', 'Compteur.nom', 'Acteur',
                                                                                               'Typeacteur', 'Typeacte' )));
		for($i=0; $i < count($typeseances); $i++) {
			$typeseances[$i]['Typeseance']['is_deletable'] = $this-> _isDeletable($typeseances[$i], $message);
			$typeseances[$i]['Typeseance']['action'] = $this->Typeseance->libelleAction($typeseances[$i]['Typeseance']['action'], true);
		}
		$this->set('typeseance', $typeseance);
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
			if ($this->Typeseance->save($this->data)) {
				$this->Session->setFlash('Le type de seance \''.$this->data['Typeseance']['libelle'].'\' a &eacute;t&eacute; sauvegard&eacute;', 'growl');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
		}
		if ($sortie)
			$this->redirect('/typeseances/index');
		else {   
			$this->set('compteurs', $this->Typeseance->Compteur->find('list'));
			$this->set('models', $this->Model->find('list',array('conditions'=>array('type'=>'Document'),
                                                                'fields' => array('Model.id','Model.modele'))));
			$this->set('actions', array(0 => $this->Typeseance->libelleAction(0, true), 
                                                    1 => $this->Typeseance->libelleAction(1, true),
                                                    2 => $this->Typeseance->libelleAction(2, true)));
			$this->set('typeacteurs', $this->Typeseance->Typeacteur->find('list'));
			$this->set('selectedTypeacteurs', null);
			$this->set('acteurs', $this->Typeseance->Acteur->generateList('Acteur.nom'));
			$this->set('selectedActeurs', null);
                        $this->set('natures', $this->Typeacte->find('list', array('fields' => array('Typeacte.libelle') )));
                        $this->set('selectedNatures', null);
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
                $this->Typeseance->Behaviors->attach('Containable');

		if (empty($this->data)) {
			$this->data = $this->Typeseance->find('first', array('conditions' => array('Typeseance.id' => $id),
                                                                            'contain' => array('Modelpvdetaille.modele', 'Modelpvdetaille.id',
                                                                                               'Modelpvsommaire.modele', 'Modelpvsommaire.id',
                                                                                               'Modelordredujour.modele', 'Modelordredujour.id',
                                                                                               'Modelconvocation.modele', 'Modelconvocation.id',
                                                                                               'Modeldeliberation.modele', 'Modeldeliberation.id',
                                                                                               'Modelprojet.modele', 'Modelprojet.id', 
                                                                                               'Compteur.id', 'Compteur.nom', 'Acteur', 
                                                                                               'Typeacteur', 'Typeacte')));
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour le type de s&eacute;ance');
				$sortie = true;
			} else {
				$this->set('selectedTypeacteurs', $this->_selectedArray($this->data['Typeacteur']));
				$this->set('selectedActeurs', $this->_selectedArray($this->data['Acteur']));
				$this->set('selectedNatures', $this->_selectedArray($this->data['Typeacte']));
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
			$this->set('actions', array(0 => $this->Typeseance->libelleAction(0, true), 
                                                    1 => $this->Typeseance->libelleAction(1, true),
                                                    2 => $this->Typeseance->libelleAction(2, true)));
			$this->set('typeacteurs', $this->Typeseance->Typeacteur->find('list'));
			$this->set('acteurs', $this->Typeseance->Acteur->generateList('Acteur.nom'));
                        $this->set('natures', $this->Typeacte->find('list', array('fields' => array('Typeacte.libelle') )));
		}
	}

/* dans le controleur car utilisé dans la vue index pour l'affichage */
	function _isDeletable($typeseance, &$message) {
		if ($this->Seance->find('count', array('type_id'=>$typeseance['Typeseance']['id']))) {
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
