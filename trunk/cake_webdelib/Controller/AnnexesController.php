<?php

class AnnexesController extends AppController {

    // Gestion des droits
    public $aucunDroit;

    function delete($id = null) {
        if ($this->Annex->del($id)) {
            $this->Session->setFlash('Annexe supprimée', 'growl');
        } else {
            $this->Session->setFlash('Impossible de supprimer cette annexe', 'growl');
        }
        $this->redirect($this->previous);
    }

    function download($id = null, $type = '') {
        $DOC_TYPE = Configure::read('DOC_TYPE');

        // lecture en base
        $annexe = $this->Annex->find('first', array(
            'fields' => 'data,data_pdf,filename,filename_pdf,filetype',
            'recursive' => -1,
            'conditions' => array('id' => $id)));

        if ($type == 'odt') {
            $content = $annexe['Annex']['data'];
            $filename = $annexe['Annex']['filename'];
            $type = 'application/vnd.oasis.opendocument.text';
        } elseif ($type == 'pdf') {
            $content = $annexe['Annex']['data_pdf'];
            $filename = $annexe['Annex']['filename_pdf'];
            $type = 'application/pdf';
        } elseif ($DOC_TYPE[$annexe['Annex']['filetype']]['extention'] != 'pdf') {
            $content = $annexe['Annex']['data'];
            $filename = $annexe['Annex']['filename'];
            $type = $annexe['Annex']['filetype'];
        } else {
            $content = $annexe['Annex']['data_pdf'];
            $filename = $annexe['Annex']['filename_pdf'];
            $type = $annexe['Annex']['filetype'];
        }

        if (empty($annexe)) return;
        header('Content-type: ' . $type);
        header('Content-Length: ' . strlen($content));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $content;
        exit;
    }

}