<?php
class AnnexesController extends AppController {

	// Gestion des droits
	var $aucunDroit;

	function delete($id = null) {
		if ($this->Annex->del($id)) {
		    $this->redirect($this->Session->read('user.User.lasturl'));
		}
	}

	function download($id=null){
		// lecture en base
		$annexe = $this->Annex->find('first', array(
			'recursive' => -1,
			'conditions' => array('id'=>$id)));
		if (empty($annexe)) return;
		header('Content-type: '.$annexe['Annex']['filetype']);
		header('Content-Length: '.$annexe['Annex']['size']);
		header('Content-Disposition: attachment; filename='.$annexe['Annex']['filename']);
		echo $annexe['Annex']['data'];
		exit();
	}

}
?>
