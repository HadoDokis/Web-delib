<?php
class AnnexesController extends AppController {

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Commentaire');
			$this->redirect($action);
		}
		if ($this->Annex->del($id)) {
			$this->redirect($this->Session->read('user.User.lasturl'));
		}
	}
}
?>