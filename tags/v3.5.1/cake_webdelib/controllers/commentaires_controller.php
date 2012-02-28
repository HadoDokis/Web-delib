<?php
class CommentairesController extends AppController {

	// Gestion des droits
	var $aucunDroit;

	function add($delib_id=null) {
		if (!$delib_id) {
			$this->Session->setFlash('Invalide id pour la d&eacute;lib&eacute;ration du commentaire.');
			$this->redirect('/');
		}
		$this->set('delib_id',$delib_id);
		if (!empty($this->data)) {
		       $this->Commentaire->create();
			$this->data['Commentaire']['agent_id'] = $this->Session->read('user.User.id');
			$this->data['Commentaire']['commentaire_auto'] = 0;
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

	function prendreEnCompte($id = null, $delib_id) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le commentaire');
			$this->redirect('/deliberations/traiter/'.$delib_id);
		}
		$this->data = $this->Commentaire->read(null, $id);
		$this->data['Commentaire']['pris_en_compte'] = 1;
		if ($this->Commentaire->save($this->data)) {
			$this->redirect('/deliberations/traiter/'.$delib_id);
		}
	}

}
?>
