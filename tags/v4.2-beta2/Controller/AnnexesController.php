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
        
//      $this->Annex->id=$id;
//        $this->Annex->saveField('edition_data', NULL);
//        $this->Annex->save();
//        
//        $this->CronJob->convertionAnnexesJob(341);
//        exit;
        // lecture en base
        $annexe = $this->Annex->find('first', array(
            'fields' => 'data,edition_data,data_pdf,filename,filename_pdf,filetype',
            'conditions' => array('id' => $id),
            'recursive' => -1
            ));
        
        switch ($type) {
            case 'edition_data':
            $content = $annexe['Annex']['edition_data'];
            $filename = 'edition_data.odt';
            $typemime = 'application/vnd.oasis.opendocument.text';
                break;
            
            case 'pdf':
            $content = $annexe['Annex']['data_pdf'];
            $filename = $annexe['Annex']['filename_pdf'];
            $typemime = 'application/pdf';
                break;

            default:
                    $content = $annexe['Annex']['data'];
                    $filename = $annexe['Annex']['filename'];
                    $typemime = $annexe['Annex']['filetype'];
                break;
        }
        $this->response->disableCache();
        $this->response->body($content);
        $this->response->type($typemime);
        $this->response->download($filename);
        
        return $this->response;
    }

}