<?php

App::import(array('Model', 'AppModel', 'File'));
App::import(array('Model', 'Deliberation', 'File'));
App::import(array('Model', 'Commentaire', 'File'));
/**
 * @deprecated au profit des méthodes de même nom dans Deliberation (Model)
 * Class ParapheurShell
 */
class ParapheurShell extends Shell {

    var $uses = array('Deliberation', 'Commentaire');

    function startup() {

    }

    function main() {

        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('IparapheurComponent', 'Controller/Component');
        
        //Si service désactivé ==> quitter
        if (!Configure::read('USE_PARAPHEUR')) {
            $this->out("Service i-Parapheur désactivé");
            exit;
        }
        $collection = new ComponentCollection();
        $this->Parafwebservice = new IparapheurComponent($collection);
        // Controle de l'avancement des délibérations dans le parapheur
        $delibs = $this->Deliberation->find('all', array(
            'conditions' => array(
                'Deliberation.etat >' => 2,
                'Deliberation.parapheur_etat' => 1),
            'recursive' => -1,
            'fields' => array('id', 'objet', 'typeacte_id')));

        foreach ($delibs as $delib) {
            //FIXME : pourquoi aller chercher compteur_id ?
            $compteur_id = 0;
            $typeacte = $this->Deliberation->Typeacte->find('first', array('conditions' => array('Typeacte.id' => $delib['Deliberation']['typeacte_id'])));
            if ($typeacte['Nature']['code'] != 'DE')
                $compteur_id = $typeacte['Typeacte']['compteur_id'];
            $objetDossier = $this->Parafwebservice->handleObject($delib['Deliberation']['objet']);
            $this->_checkEtatParapheur($delib['Deliberation']['id'], $objetDossier, false, $compteur_id);
        }
    }

    function _checkEtatParapheur($delib_id, $objet, $tdt = false, $compteur_id = 0) {
        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('IparapheurComponent', 'Controller/Component');
        $collection = new ComponentCollection();
        $this->Parafwebservice = new IparapheurComponent($collection);
        
        $this->Deliberation->id = $delib_id;
        $delib = $this->Deliberation->find('first', array('conditions' => array("Deliberation.id" => $delib_id), 'recursive' => -1));
        
        if ($delib['Deliberation']['parapheur_id'] != "")
                $id_dossier = $delib['Deliberation']['parapheur_id'];
        else //DEPRECATED (rétro-compatibilité vieux dossiers parapheur)
                $id_dossier = "$delib_id $objet";

        $histo = $this->Parafwebservice->getHistoDossierWebservice($id_dossier);
        if (isset($histo['logdossier'])){
            for ($i = 0; $i < count($histo['logdossier']); $i++) {
                if (!$tdt) {
                    if ($histo['logdossier'][$i]['status'] == 'Signe'
                        || $histo['logdossier'][$i]['status'] == 'Archive') {

                        $this->Commentaire->create();
                        $comm ['Commentaire']['delib_id'] = $delib_id;
                        $comm ['Commentaire']['agent_id'] = -1;
                        $comm ['Commentaire']['texte'] = $histo['logdossier'][$i]['nom'] . " : " . $histo['logdossier'][$i]['annotation'];
                        $comm ['Commentaire']['commentaire_auto'] = 0;
                        $this->Commentaire->save($comm['Commentaire']);

                        if ($delib['Deliberation']['parapheur_etat'] == 1) {
                            if ($histo['logdossier'][$i]['status'] == 'Signe') {
                                $dossier = $this->Parafwebservice->GetDossierWebservice($id_dossier);
                                if (!empty($dossier['getdossier']['signature'])) {
                                    //$this->Deliberation->saveField('delib_pdf', base64_decode($dossier['getdossier'][8]));
                                    $this->Deliberation->saveField('parapheur_bordereau', base64_decode($dossier['getdossier']['bordereau']));
                                    $this->Deliberation->saveField('signature', base64_decode($dossier['getdossier']['signature']));
                                }
                                $this->Deliberation->saveField('signee', 1);
                            }
                            if ($histo['logdossier'][$i]['status'] == 'Archive'){
                                $this->Deliberation->saveField('parapheur_etat', 2);
                                $this->Parafwebservice->archiverDossierWebservice($id_dossier, "EFFACER");
                            }
                        }
                    } elseif ($histo['logdossier'][$i]['status'] == 'RejetSignataire'
                        || $histo['logdossier'][$i]['status'] == 'RejetVisa') { // Cas de refus dans le parapheur
                        $this->Commentaire->create();
                        $comm ['Commentaire']['delib_id'] = $delib_id;
                        $comm ['Commentaire']['agent_id'] = -1;
                        $comm ['Commentaire']['texte'] = $histo['logdossier'][$i]['nom'] . " : " . $histo['logdossier'][$i]['annotation'];
                        $comm ['Commentaire']['commentaire_auto'] = 0;
                        $this->Commentaire->save($comm['Commentaire']);
                        $this->Deliberation->saveField('parapheur_etat', -1);
                        // Supprimer le dossier du parapheur
                        $this->Parafwebservice->effacerDossierRejeteWebservice($id_dossier);
                    }
                } else {
                    if ($histo['logdossier'][$i]['status'] == 'EnCoursTransmission')
                        return true;
                }
            }
        }
        return false;
    }

}
