<?php
class ServicesController extends AppController {

	var $name = 'Services';
	var $helpers = array('Html', 'Form' );

	function index() {
		$data=$this->Service->findAll();
		$this->set('data',$data);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le service');
			$this->redirect('/services/index');
		}
		$this->set('service', $this->Service->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le service');
				$this->redirect('/services/index');
			}
			$this->data = $this->Service->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Le service a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/services/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le service');
			$this->redirect('/services/index');
		}
		if ($this->Service->del($id)) {
			$this->Session->setFlash('Le service a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/services/index');
		}
	}
	
    function changeService($newServiceActif)
    {
    	$this->Session->del('user.User.service');
       	$this->Session->write('user.User.service',$newServiceActif);
		//redirection sur la page où on était avant de changer de service
       	$this->Redirect($this->Session->read('user.User.lasturl'));
    }
	
	function getLibelle ($id = null) {
		$condition = "Service.id = $id";
        $objCourant = $this->Service->findAll($condition);
		return $objCourant['0']['Service']['libelle'];
	}

}
?>