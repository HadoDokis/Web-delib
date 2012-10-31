<?php
    App::import(array('Model', 'AppModel', 'File'));
    App::import(array('Model', 'Deliberation', 'File'));

    class PastellShell extends Shell{
	
        var $uses = array('Deliberation', 'Collectivite');

        function startup() {
        }

	function main() {
            $collectivite = $this->Collectivite->find('first', array('conditions'=> array('id'=>1)));
            $id_e =  $collectivite['Collectivite']['id_entity'];
            // Controle de l'avancement des délibérations dans le parapheur
            $delibs = $this->Deliberation->find('all',
						array('conditions' => array('Deliberation.pastell_id !=' => null ),
                                                      'recursive'  => -1, 
                                                      'fields'     =>array('id', 'pastell_id', 'signee')));
	    if (!empty($delibs)){
                 App::import('Component','Pastell');
                 $this->Pastell = new PastellComponent();

		foreach ($delibs as $delib) {
                    $delib_id = $delib['Deliberation']['id'];
                    $this->Deliberation->id = $delib_id;
		    $id_d     = $delib['Deliberation']['pastell_id'];
		    $infos = $this->Pastell->getInfosDocument($id_e, $id_d);
                    $infos = (array)$infos;
                    if (isset( $infos['data'])) {
                        $infos['data'] = (array)$infos['data'];
                    }
                    // Cas ou l'acte n'existe plus dans PASTELL
                    if (isset($infos['error-message']) && ( strpos($infos['error-message'], 'Acces interdit' )))
                      $this->Deliberation->savefield("pastell_id", null);

                    // Cas ou l'acte a été envoyé au tdt depuis PASTELL 
                    if (isset($infos['data']['tedetis_transaction_id']))
                        $this->stockTdtInfo($id_e, $id_d, $delib_id, $infos);                      

                    // Cas ou la signature de l'acte a été récupérée par PASTELL
                    if ($delib['Deliberation']['signee'] ==1)
                        continue;

                    if (isset($infos['data']['has_signature']) && ($infos['data']['has_signature'] == 1)) {
                        $this->stockSignature($id_e, $id_d, $delib_id);
                    }
                    else {
                        if (isset($infos['action-possible'])) {
                            foreach($infos['action-possible'] as $id=>$action) {
                                if ($action == 'verif-iparapheur') {
                                    $return = $this->Pastell->action($id_e, $id_d, $action);
                                    $return = (array) $return;
		                    $infos = $this->Pastell->getInfosDocument($id_e, $id_d);
                                    $infos = (array)$infos;
                                    if (isset($infos['info']))
                                        $infos['info'] = (array) $infos['info'];
                                    if ($infos['info']['last_action'] == 'rejet-iparapheur') 
                                        $this->refusParapheur($id_e, $id_d, $delib_id); 
                                    elseif (isset($return['result']) && ($return['result'])) 
                                        $this->stockSignature($id_e, $id_d, $delib_id);
                                }
                            }
                        }
                    } 
                }
            }
        }

        function stockSignature ($id_e, $id_d, $delib_id) {
            App::import('Component','Pastell');
            $this->Pastell = new PastellComponent();

            $this->Deliberation->id = $delib_id;
            $signature =  $this->Pastell->getFile($id_e, $id_d, "signature&num=0");

            $this->Deliberation->savefield("signee", 1); 
            $this->Deliberation->savefield("signature", $signature); 
            $this->Deliberation->saveField('etat_parapheur', null); 

            return true; 
        }

        function refusParapheur ($id_e, $id_d, $delib_id) {
            App::import('Component','Pastell');
            $this->Pastell = new PastellComponent();

            $this->Deliberation->id = $delib_id;

            $this->Deliberation->saveField('pastell_id', null); 
            $this->Deliberation->saveField('etat_parapheur', -1); 
            $journal = $this->Pastell->journal($id_e, $id_d);
            $journal = (array) $journal;
            $journal[0] = (array)$journal[0];
            $this->Deliberation->saveField('commentaire_refus_parapheur', utf8_decode($journal[0]['message'])); 
        }

        function stockTdtInfo($id_e, $id_d, $delib_id, $infos) {
            App::import('Component','Pastell');
            $this->Pastell = new PastellComponent();

            $this->Deliberation->id = $delib_id;
            $bordereau =  $this->Pastell->getFile($id_e, $id_d, "bordereau&num=0");

            @$this->Deliberation->savefield("bordereau", $bordereau);
            @$this->Deliberation->savefield("signee", 1); 
            @$this->Deliberation->savefield("tdt_id", $infos['data']['tedetis_transaction_id']);
            @$this->Deliberation->savefield("dateAR", $infos['data']['date_ar']);

            return true;
        }

    }
?>
