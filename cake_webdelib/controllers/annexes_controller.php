<?php
class AnnexesController extends AppController {

	// Gestion des droits
	var $aucunDroit;

	function delete($id = null) {
		if ($this->Annex->del($id)) {
		    $this->redirect($this->Session->read('user.User.lasturl'));
		}
	}

	function download($id=null, $pdf=null){
		// lecture en base
		$annexe = $this->Annex->find('first', array(
			'recursive' => -1,
			'conditions' => array('id'=>$id)));

                if ($pdf == null) {
		    $content  = $annexe['Annex']['data'];
                    $filename = $annexe['Annex']['filename'];
                    $type     = $annexe['Annex']['filetype'];
                }
                else {
		    $content  = $annexe['Annex']['data_pdf'];
                    $filename = $annexe['Annex']['filename'].'.pdf';
                    $type     = 'application/pdf';
                }

		if (empty($annexe)) return;
		header('Content-type: '.$type);
		header('Content-Length: '.strlen($content));
		header('Content-Disposition: attachment; filename='.$filename);
                die($content);
	}

}
?>
