<?php
class CommentairesController extends AppController {

	function add($delib_id=null) {
		if (empty($this->data)) {
			$this->set('delib_id',$delib_id);
		} else {
				$this->data['Commentaire']['agent_id'] = $this->Session->read('user.User.id');
			if ($this->Commentaire->save($this->data)) {
				$this->redirect('/deliberations/traiter/'.$this->data['Commentaire']['delib_id']);
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}
	
	function edit($id = null,$delib_id=null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le commentaire');
				$this->redirect('/deliberations/traiter/'.$delib_id);
			}
			$this->set('delib_id',$delib_id);
			$this->data = $this->Commentaire->read(null, $id);
			} else {
				$this->data['Commentaire']['agent_id'] = $this->Session->read('user.User.id');
			if ($this->Commentaire->save($this->data)) {
				$this->redirect('/deliberations/traiter/'.$this->data['Commentaire']['delib_id']);
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}
	
	
	
	function delete($id = null,$delib_id) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le commentaire');
			$this->redirect('/deliberations/traiter/'.$delib_id);
		}
		if ($this->Commentaire->del($id)) {
			$this->redirect('/deliberations/traiter/'.$delib_id);
		}
	}
	
	
	function view($id = null,$delib_id=null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le commentaire.');
			$this->redirect('/deliberations/traiter'.$delib_id);
		}
		$this->set('commentaire', $this->Commentaire->read(null, $id));
	}
	
}
?>