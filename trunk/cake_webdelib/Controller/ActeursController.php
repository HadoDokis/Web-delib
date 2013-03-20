<?php
class ActeursController extends AppController
{
	var $name = 'Acteurs';

	var $helpers = array('Html', 'Html2', 'Form', 'Form2');

	var $uses = array('Acteur', 'Deliberation', 'Vote');

	// Gestion des droits : identiques aux droits des acteurs
	var $commeDroit = array(
		'add' => 'Acteurs:index',
		'edit' => 'Acteurs:index',
		'delete' => 'Acteurs:index',
		'view' => 'Acteurs:index'
	);
		
	var $paginate = array(
		'Acteur' => array(
                       'conditions' => array ('Acteur.actif'=> 1),
                       'fields' => array('DISTINCT Acteur.id', 'Acteur.nom', 'Acteur.prenom', 'Acteur.salutation', 'Acteur.telfixe', 'Acteur.telmobile',  'Acteur.suppleant_id', 'Acteur.titre', 'Acteur.position', 'Typeacteur.nom',  'Typeacteur.elu', 'Suppleant.nom', 'Suppleant.prenom'),
			'limit' => 20,
			'joins' => array(
				array(
					'table' => 'acteurs_services',
					'alias' => 'ActeursServices',
					'type' => 'LEFT',
					'conditions'=> array('ActeursServices.acteur_id = Acteur.id')
				),
				array(
					'table' => 'services',
					'alias' => 'Service',
					'type' => 'LEFT',
					'conditions'=> array('Service.id = ActeursServices.service_id')
				)
			),
			'order' => array('Acteur.position' => 'asc')
		)
	);

	function index() {

            $this->Acteur->Behaviors->attach('Containable');
            $this->paginate = array('Acteur' => array(
                                    'conditions' => array('Acteur.actif' => 1),
                                    'limit' => 20,
                                    'contain' => array( 'Service', 'Suppleant', 'Typeacteur'),
                                    'order' => array( 'Acteur.position' => 'asc')));

             $acteurs =  $this->paginate('Acteur');
             foreach( $acteurs as &$acteur) {
                 $acteur['Typeacteur']['elu'] = $this->Acteur->Typeacteur->libelleElu($acteur['Typeacteur']['elu']);
                 $acteur['Acteur']['libelleOrdre'] = $this->Acteur->libelleOrdre($acteur['Acteur']['position']);
             }
	     $this->set('acteurs', $acteurs);
	}

	function view($id = null) {
		$acteur = $this->Acteur->read(null, $id);
		if (empty($acteur)) {
			$this->Session->setFlash('Invalide id pour l\'acteur');
			$this->redirect('/acteurs/index');
		} else
			$this->set('acteur', $acteur);
	}

	function add() {
		$sortie = false;
		if (!empty($this->data)) {
		  //  $this->request->data['Acteur']['date_naissance_day'] = $this->data['Acteur']['date_naissance']['day'];
		  //  $this->request->data['Acteur']['date_naissance_month'] = $this->data['Acteur']['_month'];
		  //  $this->request->data['Acteur']['date_naissance_year'] = $this->data['Acteur']['_year'];
			if ($this->_controleEtSauve()) {
				$this->Session->setFlash('L\'acteur \''.$this->data['Acteur']['prenom'].' '.$this->data['Acteur']['nom'].'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/acteurs/index');
		else {
			$this->Acteur->Typeacteur->recursive = 0;
			$this->set('acteurs', $this->Acteur->generateListElus() );
			$this->set('typeacteurs', $this->Acteur->Typeacteur->find('all', array('fields'=>array( 'id', 'nom', 'elu'))));
			$this->set('services', $this->Acteur->Service->generateTreeList(array('Service.actif'=>'1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
			$this->set('selectedServices', null);
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->request->data = $this->Acteur->read(null, $id);
			$this->set('acteurs', $this->Acteur->generateListElus() );
                        if ($this->data['Acteur']['date_naissance'] != null) 
                            $date = date('d/m/Y', strtotime($this->data['Acteur']['date_naissance']));

			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'acteur');
				$sortie = true;
			} else
				$this->set('selectedServices', $this->_selectedArray($this->data['Service']));
		} else {
                    $this->request->data['Acteur']['date_naissance'] =  $this->Utils->FrDateToUkDate($this->data['date']);
                    
                    if ($this->_controleEtSauve()) {
                        $this->Session->setFlash('L\'acteur \''.$this->data['Acteur']['prenom'].' '.$this->data['Acteur']['nom'].'\' a &eacute;t&eacute; modifi&eacute;');
			$sortie = true;
	            } else {
		        $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			if (array_key_exists('Service', $this->data))
			    $this->set('selectedServices', $this->data['Service']['Service']);
			else
			    $this->set('selectedServices', null);			}
                         
		}
		if ($sortie)
		    $this->redirect('/acteurs/index');
		else {
		    $this->Acteur->Typeacteur->recursive = 0;
		    $this->set('typeacteurs', $this->Acteur->Typeacteur->find('all', array('fields'=>array( 'id', 'nom', 'elu'))));
		    $this->set('services', $this->Acteur->Service->generateTreeList(array('Service.actif'=>'1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
                    if (isset($date)) $this->set('date', $date);
 
		}
	}

function _controleEtSauve() {

	if (!empty($this->data['Acteur']['typeacteur_id'])) {

		if ($this->Acteur->Typeacteur->field('elu', 'id = '. $this->data['Acteur']['typeacteur_id'])) {
			// pour un élu : initialisation de 'position' si non définie
			if (!$this->data['Acteur']['position'])
				$this->request->data['Acteur']['position'] = $this->Acteur->getPostionMaxParActeursElus() + 1;
		} else {
			// pour un non élu : suppression des informations éventuellement saisies (service, position, date naissance)
			if (array_key_exists('Service', $this->data))
				$this->request->data['Service']['Service'] = array();
                      $this->request->data['Acteur']['position'] = 999;
		}
	}
	return $this->Acteur->save($this->data);
}

/* dans le controleur car utilisé dans la vue index pour l'affichage */
	function _isDeletable($acteur, &$message) {
		if ($this->Deliberation->find('count',array('conditions'=>array('rapporteur_id'=>$acteur['Acteur']['id'])))) {
			$message = 'L\'acteur \''.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'\' ne peut pas être supprimé car il est le rapporteur de délibérations';
			return false;
		}
		if ($this->Vote->find('count',array('conditions'=>array('acteur_id'=>$acteur['Acteur']['id'])))) {
			$message = 'L\'acteur \''.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'\' ne peut pas être supprimé car il est le rapporteur de délibérations';
			return false;
		}

		return true;
	}

	function delete($id = null) {
		$messageErreur = '';
		$acteur = $this->Acteur->read('id, nom, prenom', $id);
                $this->log($acteur);
		if (empty($acteur))
			$this->Session->setFlash('Invalide id pour l\'acteur');
		else {
                     $this->Acteur->id = $id ;
                     $this->Acteur->saveField('actif', 0);
                }
		$this->redirect('/acteurs/index');
	}

}
?>
