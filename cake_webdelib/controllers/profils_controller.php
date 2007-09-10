<?php
class ProfilsController extends AppController {

	var $name = 'Profils';
	var $helpers = array('Html', 'Form','Tree' );

	function index()
	{
		$this->set('data', $this->Profil->findAllThreaded(null, null, 'Profil.id ASC'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Profil.');
			$this->redirect('/profils/index');
		}
		$this->set('profil', $this->Profil->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$profils = $this->Profil->generateList(null,'id ASC');
			$this->set('profils', $profils);
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('The Profil has been saved');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Profil');
				$this->redirect('/profils/index');
			}
			$this->data = $this->Profil->read(null, $id);
			$profils = $this->Profil->generateList();
			$this->set('profils', $profils);
			$this->set('selectedProfil',$this->data['Profil']['parent_id']);			
		} else {
			$this->cleanUpFields();
			if ($this->Profil->save($this->data)) {
				$this->Session->setFlash('The Profil has been saved');
				$this->redirect('/profils/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Profil');
			$this->redirect('/profils/index');
		}
		
		if ($this->Profil->del($id)) {
			
			$this->Session->setFlash('The Profil deleted: id '.$id.'');
			$this->redirect('/profils/index');
		}
	}
	
	function changeParentId($curruentParentId, $newParentId)
	{
//		$sql = "update profils set parent_id = $newParentId where parent_id = $currentParentId";
//		$this->Profil->query($sql);

		$this->data = $this->Profil->findByParentId(null, $id);
		debug($this->data);exit;
	}

}
?>