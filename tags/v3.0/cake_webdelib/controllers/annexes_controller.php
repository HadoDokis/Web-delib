<?php
class AnnexesController extends AppController {

	// Gestion des droits
	var $aucunDroit;

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le commentaire');
			$this->redirect('/');
		}
		if ($this->Annex->del($id)) {
		    $this->redirect($this->Session->read('user.User.lasturl'));
		}
	}

	function download($id=null){
		header('Content-type: '.$this->getFileType($id));
		header('Content-Length: '.$this->getSize($id));
		header('Content-Disposition: attachment; filename='.$this->getFileName($id));
		echo $this->getData($id);
		exit();
	}

	function getFileType($id=null) {
		$condition = array("Annex.id" => $id);
       	$objCourant = $this->Annex->find('all', array('conditions'=>$condition));
		return $objCourant['0']['Annex']['filetype'];
	}

	function getFileName($id=null) {
		$condition = array("Annex.id" => $id);
       	$objCourant = $this->Annex->find('all', array('conditions'=>$condition));
		return $objCourant['0']['Annex']['filename'];
	}

	function getSize($id=null) {
		$condition = array("Annex.id" => $id);
       	$objCourant = $this->Annex->find('all', array('conditions'=>$condition));
		return $objCourant['0']['Annex']['size'];
	}

	function getData($id=null) {
		$condition = array("Annex.id" => $id);
       	$objCourant = $this->Annex->find('all', array('conditions'=>$condition));
		return $objCourant['0']['Annex']['data'];
	}


}
?>
