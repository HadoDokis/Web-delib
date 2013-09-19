<?php

class ActeShell extends AppShell {

    public $uses = array('Deliberation', 'TdtMessage');

    public function main() {
        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('S2lowComponent', 'Controller/Component');
        
        //Si service désactivé ==> quitter
        if (!Configure::read('USE_S2LOW')) exit("Service S2LOW désactivé");
        
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

        $delibs = $this->Deliberation->find('all',
                                             array('conditions' => array('Deliberation.etat' => 5),
                                                   'recursive'  => -1));
            foreach ($delibs as $delib) {
                $result = $this->_getNewFlux($delib['Deliberation']['tdt_id']);        
                $result_array = explode("\n", $result);
                foreach (  $result_array  as  $result ) {
                    if (!empty($result)) {
                        $type = substr($result, 0,1);
                        $reponse = substr($result, 2,1);
                        $message_id = substr($result, 4, strlen($result));
                        if ($this->_isNewMessage($delib['Deliberation']['id'], $type,  $reponse,  $message_id)) {
                            $this->TdtMessage->create();
                            $message['TdtMessage']['delib_id']     = $delib['Deliberation']['id'];
                            $message['TdtMessage']['type_message'] = $type;
                            $message['TdtMessage']['message_id']   = $message_id;
                            $message['TdtMessage']['reponse']       = $reponse;
                            $this->TdtMessage->save($message);
                        }
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

     function _getNewFlux ($tdt_id) {
            $url = 'https://'.Configure::read('HOST')."/modules/actes/actes_transac_get_document.php?id=$tdt_id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
            curl_setopt($ch, CURLOPT_POST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
           // curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_CAINFO, Configure::read('WEBDELIB_PATH').'Config/cert_s2low/bundle.pem');
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_return = curl_exec($ch);
            return($curl_return);
        }

        function _isNewMessage ($delib_id, $type,  $reponse, $message_id) {
             $message = $this->TdtMessage->find('first', array('conditions' => 
                                                         array('TdtMessage.delib_id'     => $delib_id,
                                                               'TdtMessage.type_message' => $type,
                                                               'TdtMessage.reponse'      => $reponse,
                                                               'TdtMessage.message_id'   => $message_id )));
             return (empty($message));
        }
          

}


?>
