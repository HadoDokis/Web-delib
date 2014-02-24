<?php

/**
 * Code source de la classe ActeShell.
 *
 * PHP 5.3
 *
 * @package app.Console.Command.Shell
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('S2lowComponent', 'Controller/Component');

/**
 * Classe ActeShell.
 *
 * @package app.Test.Case.Model
 * 
 */
class ActeShell extends AppShell {

    public $uses = array('Deliberation', 'TdtMessage');

    public function main() {

        //Si service désactivé ==> quitter
        if (!Configure::read('USE_S2LOW')) {
            $this->out("Service S2LOW désactivé");
            exit;
        }

        $collection = new ComponentCollection();
        $this->S2low = new S2lowComponent($collection);

        $delibs = $this->Deliberation->find('all', array(
            'conditions' => array(
                'Deliberation.tdt_id !=' => null,
                'Deliberation.tdt_dateAR' => null),
            'fields' => array('Deliberation.id', 'Deliberation.tdt_id'),
            'recursive' => -1));

        foreach ($delibs as $delib) {
            if (isset($delib['Deliberation']['tdt_id'])) {
                $flux = $this->S2low->getFluxRetour($delib['Deliberation']['tdt_id']);
                $codeRetour = substr($flux, 3, 1);

                if ($codeRetour == 4) {
                    $dateAR = $this->S2low->getDateAR($res = mb_substr($flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
                    $this->Deliberation->changeDateAR($delib['Deliberation']['id'], $dateAR);
                }
            }
        }

        $delibs = $this->Deliberation->find('all', array(
            'conditions' => array('Deliberation.etat' => 5),
            'recursive' => -1));
        foreach ($delibs as $delib) {
            $result = $this->S2low->getNewFlux($delib['Deliberation']['tdt_id']);
            $result_array = explode("\n", $result);
            if($result_array[0]!='KO')
            foreach ($result_array as $result) {
                if (!empty($result)) {
                    $infos = explode('-', $result);
                    $type = $infos[0];
                    $reponse = $infos[1];
                    $message_id = $infos[2];
                    if (!$this->TdtMessage->existe($message_id)) {
                        $this->TdtMessage->create();
                        $message['TdtMessage']['delib_id'] = $delib['Deliberation']['id'];
                        $message['TdtMessage']['type_message'] = $type;
                        $message['TdtMessage']['message_id'] = $message_id;
                        $message['TdtMessage']['type_reponse'] = $reponse;
                        $this->TdtMessage->save($message);
                    }
                }
            }
            else
                $this->out('Deliberation=>'.$delib['Deliberation']['id'].' (Erreur:'.utf8_encode($result_array[1]).')');
        }
        
        //Recupération des délibérations tanponné et des bodereaux de télétransmissions
        $delibs = $this->Deliberation->find('all', array(
            'fields' => array('Deliberation.id', 'Deliberation.tdt_id', 'Deliberation.tdt_dateAR'),
            'conditions' => array(
                'Deliberation.tdt_id !=' => null,
                'OR' => array(
                    'Deliberation.tdt_data_pdf =' => null,
                    'Deliberation.tdt_data_bordereau_pdf =' => null
                )),
            'order' => array('Deliberation.id DESC'),
            'limit' => 50,
            'recursive' => -1));

        foreach ($delibs as $delib) {
            $this->Deliberation->id = $delib['Deliberation']['id'];
            if (empty($delib['Deliberation']['tdt_data_pdf'])) {
                $flux = $this->S2low->getActeTampon($delib['Deliberation']['tdt_id']);
                $this->Deliberation->saveField('tdt_data_pdf', $flux);
            }
            if (!empty($delib['Deliberation']['tdt_dateAR']) && empty($delib['Deliberation']['tdt_data_bordereau_pdf'])) {
                $flux = $this->S2low->getAR($delib['Deliberation']['tdt_id']);
                $this->Deliberation->saveField('tdt_data_bordereau_pdf', $flux);
            }
        }
    }

   

}
