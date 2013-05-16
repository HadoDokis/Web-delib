<?php

class ActeShell extends AppShell {

    public $uses = array('Deliberation');

    public function main() {
        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('S2lowComponent', 'Controller/Component');
        $collection = new ComponentCollection();
        $this->S2low =new S2lowComponent($collection);

        $delibs = $this->Deliberation->find('all', array('conditions' => array('Deliberation.tdt_id !=' => null,
                                                                               'Deliberation.dateAR' => null),
                                                         'fields'     => array('Deliberation.id', 'Deliberation.tdt_id'),
                                                          'recursive' => -1)); 

        foreach ($delibs as $delib) {
            if (isset($delib['Deliberation']['tdt_id'])){
                $flux   = $this->S2low->getFluxRetour($delib['Deliberation']['tdt_id']);
                $codeRetour = substr($flux, 3, 1);
              
                if($codeRetour==4) {
                    $dateAR = $this->_getDateAR($res = mb_substr( $flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
                    $this->Deliberation->changeDateAR($delib['Deliberation']['id'], $dateAR);
                }
            }
        }
    }

    function _getDateAR($fluxRetour) {
        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('DateComponent', 'Controller/Component');
        $collection = new ComponentCollection();
        $this->Date =new DateComponent($collection);
        // +21 Correspond a la longueur du string : actes:DateReception"
        $date = substr($fluxRetour, strpos($fluxRetour, 'actes:DateReception')+21, 10);
        return ($this->Date->frenchDate(strtotime($date )));
    }

}


?>
