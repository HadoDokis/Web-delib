<?php

class AnnexesController extends AppController {

    // Gestion des droits
    public $aucunDroit;
    
    public $uses = array('Annex','CronJob');
    
    public $components = array('Conversion');

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
        
        //$this->CronJob->convertionAnnexesJob(341);

        // lecture en base
        $annexe = $this->Annex->find('first', array(
            'fields' => 'data,edition_data,data_pdf,filename,filename_pdf,filetype',
            'conditions' => array('id' => $id),
            'recursive' => -1
            ));
        
        
        switch ($type) {
            case 'data_edition':
            $content = $annexe['Annex']['edition_data'];
            //$content = $this->Conversion->toOdt($annexe['Annex']['data'], $annexe['Annex']['filetype']); // POUR LES TEST
            $filename = 'data_edition.odt';
            $typemime = 'application/vnd.oasis.opendocument.text';
                break;
            
            case 'pdf':
            $content = $annexe['Annex']['data_pdf'];
            $filename = $annexe['Annex']['filename_pdf'];
            $typemime = 'application/pdf';
                break;

            default:
                if ($DOC_TYPE[$annexe['Annex']['filetype']]['extension'] != 'pdf') {
                    $content = $annexe['Annex']['data'];
                    $filename = $annexe['Annex']['filename'];
                    $typemime = $annexe['Annex']['filetype'];
                } else {
                    $content = $annexe['Annex']['data_pdf'];
                    $filename = $annexe['Annex']['filename_pdf'];
                    $typemime = $annexe['Annex']['filetype'];
                }
                break;
        }

        
        $this->response->disableCache();
        $this->response->body($content);
        $this->response->type($typemime);
        $this->response->download($filename.'.'.$DOC_TYPE[$typemime]['extension']);
        
        return $this->response;
    }

}