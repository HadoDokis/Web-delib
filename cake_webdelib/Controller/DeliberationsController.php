<?php
/**
 * Class DeliberationsController
 * @property Deliberation $Deliberation
 */
class DeliberationsController extends AppController
{
    /*
     * Deliberation.etat = -1 : refusé
     * Deliberation.etat = 0 : en cours de rédaction
     * Deliberation.etat = 1 : dans un circuit
     * Deliberation.etat = 2 : validé
     * Deliberation.etat = 3 : Voté pour
     * Deliberation.etat = 4 : Voté contre
     * Deliberation.etat = 5 : envoyé
     *
     * Deliberation.avis = 0 ou null : pas d'avis donné
     * Deliberation.avis = 1 : avis favorable
     * Deliberation.avis = 2 : avis défavorable
     */

    var $name = 'Deliberations';
    var $helpers = array('Fck');
    var $uses = array('Acteur', 'Deliberation', 'User', 'Annex', 'Typeseance', 'Seance', 'TypeSeance', 'Commentaire', 'ModelOdtValidator.Modeltemplate', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Infosupdef', 'Infosup', 'Historique', 'Cakeflow.Circuit', 'Cakeflow.Composition', 'Cakeflow.Etape', 'Cakeflow.Traitement', 'Cakeflow.Visa', 'Nomenclature', 'Deliberationseance', 'Deliberationtypeseance');
    var $components = array('Fido', 'Gedooo', 'Date', 'Utils', 'Email', 'Acl', 'Droits', 'Iparapheur', 'Filtre', 'Cmis', 'Progress', 'Conversion', 'Pastell', 'S2low', 'Pdf', 'Paginator');
    var $aucunDroit = array('getTypeseancesParTypeacteAjax', 'quicksearch');
    // Gestion des droits
    var $demandeDroit = array(
        'add',
        'edit',
        'delete',
        'mesProjetsRedaction',
        'mesProjetsValidation',
        'mesProjetsValides',
        'mesProjetsATraiter',
        'mesProjetsRecherche',
        'projetsMonService',
        'tousLesProjetsSansSeance',
        'tousLesProjetsValidation',
        'tousLesProjetsAFaireVoter',
        'tousLesProjetsRecherche',
        'goNext',
        'validerEnUrgence',
        'rebond',
        'sendToParapheur',
        'sendToGed',
        'autresActesAValider',
        'toSend',
        'transmit',
        'verserAsalae',
        'autreActesAEnvoyer',
        'autreActesEnvoyes',
        'editerTous',
    );
    var $commeDroit = array(
        'view' => array('Pages:mes_projets', 'Pages:tous_les_projets', 'downloadDelib'),
        'attribuercircuit' => 'Deliberations:mesProjetsRedaction',
        'addIntoCircuit' => 'Deliberations:mesProjetsRedaction',
        'traiter' => 'Deliberations:mesProjetsATraiter',
        'retour' => 'Deliberations:mesProjetsATraiter',
        'attribuerSeance' => 'Deliberations:tousLesProjetsSansSeance',
        'sendToPastell' => 'Deliberations:sendToParapheur',
        'refreshPastell' => 'Deliberations:sendToParapheur',
        'autreActesValides' => 'Deliberations:autresActesAValider',
        'autresActesAEnvoyer' => 'Deliberations:autresActesAValider',
        'autresActesEnvoyes' => 'Deliberations:autresActesAValider',
        'getTampon' => 'Deliberations:transmit'
    );
    var $libelleControleurDroit = 'Projets';
    var $ajouteDroit = array(
        'edit',
        'delete',
        'goNext',
        'validerEnUrgence',
        'rebond',
        'editerTous',
    );
    var $libellesActionsDroit = array(
        'edit' => "Modification d'un projet",
        'delete' => "Suppression d'un projet",
        'goNext' => 'Sauter une étape',
        'validerEnUrgence' => 'Valider un projet en urgence',
        'rebond' => 'Effectuer un rebond',
        'sendToParapheur' => 'Envoie à la signature',
        'sendToGed' => 'Envoie à une GED',
        'editerTous' => 'Editer tous les projets',
    );

    function view($id = null)
    {
        $this->set('previous', $this->referer());

        $this->Deliberation->Behaviors->attach('Containable');
        $this->request->data = $this->Deliberation->find('first', array(
            'fields' => array(
                'id', 'anterieure_id', 'service_id', 'circuit_id', 'typeacte_id',
                'etat', 'num_delib', 'titre', 'objet', 'objet_delib', 'num_pref',
                'texte_projet_name', 'texte_synthese_name', 'deliberation_name',
                'created', 'modified', 'deliberation', 'texte_projet', 'texte_synthese'),
            'contain' => array('Typeacte.libelle', 'Theme.libelle', 'Service.libelle',
                'Seance.date', 'Redacteur.id', 'Redacteur.nom', 'Redacteur.prenom', 'Seance.Typeseance.libelle',
                'Rapporteur.nom', 'Rapporteur.prenom', 'Annex', 'Seance.type_id',
                'Infosup', 'Multidelib.id', 'Multidelib.objet', 'Multidelib.objet_delib',
                'Multidelib.num_delib', 'Multidelib.etat', 'Multidelib.deliberation',
                'Multidelib.deliberation_name', 'Multidelib.Annex', 'Deliberationseance.id'),
            'conditions' => array('Deliberation.id' => $id)
        ));
        $this->request->data['Deliberation']['num_pref'] = $this->data['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->data['Deliberation']['num_pref']);

        if (empty($this->data)) {
            $this->Session->setFlash('Invalide id pour la délibération : affichage de la vue impossible.', 'growl');
            $this->redirect(array('action'=>'mesProjetsRedaction'));
        }
        $userId = $this->Session->read('user.User.id');

        $user = $this->Session->read('user');
        if (!$this->Droits->check($user['User']['id'], "Pages:tous_les_projets")) {
            $conditions['Deliberation.id'] = $id;
            $conditions['OR']['redacteur_id'] = $userId;

            if ($this->Droits->check($user['User']['id'], "Deliberations:projetsMonService")) {
                $services = array();
                $conditions['Deliberation.id'] = $id;
                $conditions['OR']['redacteur_id'] = $userId;
                $this->User->Behaviors->attach('Containable');
                $user_services = $this->User->find('first', array('conditions' => array('User.id' => $userId),
                    'fields' => array('User.id'),
                    'contain' => array('Service.id')));
                foreach ($user_services['Service'] as $service)
                    $services[] = $service['id'];

                $conditions['OR']['service_id'] = $services;
            }
            $acte = $this->Deliberation->find('first', array('conditions' => $conditions,
                'fields' => array('Deliberation.id'),
                'recursive' => -1));
            $estDansCircuit = $this->Traitement->triggerDansTraitementCible($userId, $id);
            if (empty($acte) && ($estDansCircuit == false)) {
                $this->Session->setFlash("Vous n'avez pas les droits pour visualiser cet acte", 'growl');
                $this->redirect('/deliberations/mesProjetsRedaction');
            }
        }

        // Compactage des informations supplémentaires
        $this->request->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup'], false);

        // Lecture des versions anterieures
        $listeAnterieure = array();
        $tab_anterieure = $this->_chercherVersionAnterieure($this->data, 0, $listeAnterieure, 'view');
        $this->set('tab_anterieure', $tab_anterieure);

        // Lecture des droits en modification
        $user_id = $this->Session->read('user.User.id');

        if ($this->Droits->check($user_id, "Deliberations:edit") && $this->Deliberation->estModifiable($id, $user_id))
            $this->set('userCanEdit', true);
        else
            $this->set('userCanEdit', false);

        // Lecture et initialisation des commentaires

        $commentaires = $this->Commentaire->find('all', array('conditions' => array('Commentaire.delib_id' => $id),
            'order' => 'created DESC'));
        for ($i = 0; $i < count($commentaires); $i++) {
            if ($commentaires[$i]['Commentaire']['agent_id'] == -1) {
                $commentaires[$i]['Commentaire']['prenomAgent'] = "i-Parapheur";
                $commentaires[$i]['Commentaire']['nomAgent'] = "Adullact";
            } else {
                $agent = $this->User->find('first', array('conditions' => array(
                    'User.id' => $commentaires[$i]['Commentaire']['agent_id']),
                    'recursive' => -1,
                    'fields' => array('nom', 'prenom')));
                $commentaires[$i]['Commentaire']['nomAgent'] = $agent['User']['nom'];
                $commentaires[$i]['Commentaire']['prenomAgent'] = $agent['User']['prenom'];
            }
        }
        $this->set('commentaires', $commentaires);
        $this->set('historiques', $this->Historique->find('all', array('conditions' => array("Historique.delib_id" => $id),
            'order' => 'Historique.created ASC')));

        //Récupération du model_id (pour lien bouton generer)
        $model_id = $this->Deliberation->Typeacte->getModelId($this->data['Deliberation']['typeacte_id'], 'modeleprojet_id');
        $this->request->data['Modeltemplate']['id'] = $model_id;


        // Mise en forme des données du projet ou de la délibération
        $this->request->data['Deliberation']['libelleEtat'] = $this->Deliberation->libelleEtat($this->data['Deliberation']['etat']);
        if (!empty($this->data['Seance']['date']))
            $this->request->data['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data['Seance']['date']));

        $this->request->data['Service']['libelle'] = $this->Deliberation->Service->doList($this->data['Deliberation']['service_id']);
        $this->request->data['Circuit']['libelle'] = $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);

        // Définitions des infosup
        $this->set('infosupdefs', $this->Infosupdef->find('all', array(
            'recursive' => -1,
            'conditions' => array('model' => 'Deliberation', 'actif' => true),
            'order' => 'ordre')));

        //Test si le projet a été inséré dans un circuit, si oui charger l'affichage
        $wkf_exist = $this->Traitement->find('count', array('recursive' => -1, 'conditions' => array('target_id' => $id)));
        if ($wkf_exist !== 0)
            $this->set('visu', $this->requestAction('/cakeflow/traitements/visuTraitement/' . $id, array('return')));
        else
            $this->set('visu', null, array('return'));

        $this->set('etat', $this->data['Deliberation']['etat']);
        //si bloqué à une étape de délégation
        $visa = false;
        $traitement = $this->Traitement->findByTargetId($id);
        if ($traitement != null) {
            //Si il n'y a pas eu de jump
            $jump = array(
                'Visa.traitement_id' => $traitement['Traitement']['id'],
                'Visa.action' => "JS"
            );
            //si reste des étapes de délégation en attente (passées)
            $delegation_restante = array(
                'Visa.traitement_id' => $traitement['Traitement']['id'],
                'Visa.trigger_id' => -1,
                'Visa.action' => "RI");
            if (!$traitement['Traitement']['treated']) {
                $conditions = array('traitement_id' => $traitement['Traitement']['id'],
                    'numero_traitement <=' => $traitement['Traitement']['numero_traitement'],
                    'trigger_id' => -1,
                    'action' => 'RI');
                $visa = $this->Visa->hasAny($conditions);

                $delegation_restante['Visa.numero_traitement <'] = $traitement['Traitement']['numero_traitement'];
            } else { // pour voir bouton actualiser sur derniere etape de délégation
                $delegation_restante['Visa.numero_traitement <='] = $traitement['Traitement']['numero_traitement'];
            }

            $visas_retard = array();
            if (!$this->Visa->hasAny($jump))
                $visas_retard = $this->Visa->find('all', array("conditions" => $delegation_restante, "recursive" => -1));

            //boutons MàJ visas en retard
            $this->set('visas_retard', $visas_retard);
        }
        //Afficher bouton MàJ
        $this->set('majDeleg', $visa);
    }

    function majEtatParapheur($id = null)
    {
        $this->requestAction("/cakeflow/traitements/majTraitementsParapheur/" . $id . "/true");
        $this->redirect("/deliberations/view/" . $id);
    }

    function _getFileData($fileName, $fileSize)
    {
        return @fread(fopen($fileName, "r"), $fileSize);
    }

    function add()
    {
        // initialisations
        $sortie = false;
        /* initialisation du lien de redirection */
        $redirect = '/deliberations/mesProjetsRedaction';
        /* initialisation du rédateur et du service emetteur */
        $user = $this->Session->read('user');
        $canEditAll = $this->Droits->check($user['User']['id'], "Deliberations:editerTous");

        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        if (Configure::read('USE_PASTELL')) {
            $res = $this->Nomenclature->generateTreeList(null, null, null, '___');
            $this->set('nomenclatures', $res);
        }

        if ($this->request->isPost()) {

            $this->Deliberation->begin();
            $this->request->data['Deliberation']['redacteur_id'] = $user['User']['id'];
            $this->request->data['Deliberation']['service_id'] = $user['User']['service'];
            if (!isset($this->data['Deliberation']['is_multidelib']) || ($this->data['Deliberation']['is_multidelib'] == 0))
                $this->request->data['Deliberation']['objet_delib'] = $this->data['Deliberation']['objet'];

            $this->request->data['Deliberation']['date_limite'] = $this->Utils->FrDateToUkDate($this->data['date_limite']);
            $this->Deliberation->unbindModel(array('hasAndBelongsToMany' => array('Seance')));
            // Si on definit une seance a une delib, on la place en derniere position de la seance
            if (isset($this->data['Seance'])) {
                if (!$this->Deliberation->canSaveSeances($this->data['Seance']['Seance'])) {
                    $this->Session->setFlash("Vous ne pouvez enregistrer une seule séance délibérante", 'growl', array('type' => 'erreur'));
                    $this->redirect("/deliberations/add");
                }
            }

            //gabarits pour ce type d'acte ?
            $typeacte = $this->Deliberation->Typeacte->find('first', array(
                'recursive' => -1,
                'fields' => array('gabarit_projet', 'gabarit_synthese', 'gabarit_acte'),
                'conditions' => array(
                    'id' => $this->data['Deliberation']['typeacte_id'],
                )
            ));
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (!empty($typeacte['Typeacte']['gabarit_projet'])){
                $this->request->data['Deliberation']['texte_projet'] = $typeacte['Typeacte']['gabarit_projet'];
                $this->request->data['Deliberation']['texte_projet_name'] = 'gabarit_projet.odt';
                $this->request->data['Deliberation']['texte_projet_size'] = strlen($typeacte['Typeacte']['gabarit_projet']);
                $this->request->data['Deliberation']['texte_projet_type'] = $finfo->buffer($typeacte['Typeacte']['gabarit_projet']);
            }
            if (!empty($typeacte['Typeacte']['gabarit_synthese'])){
                $this->request->data['Deliberation']['texte_synthese'] = $typeacte['Typeacte']['gabarit_synthese'];
                $this->request->data['Deliberation']['texte_synthese_name'] = 'gabarit_synthese.odt';
                $this->request->data['Deliberation']['texte_synthese_size'] = strlen($typeacte['Typeacte']['gabarit_synthese']);
                $this->request->data['Deliberation']['texte_synthese_type'] = $finfo->buffer($typeacte['Typeacte']['gabarit_synthese']);
            }
            if (!empty($typeacte['Typeacte']['gabarit_acte'])){
                $this->request->data['Deliberation']['deliberation'] = $typeacte['Typeacte']['gabarit_acte'];
                $this->request->data['Deliberation']['deliberation_name'] = 'gabarit_acte.odt';
                $this->request->data['Deliberation']['deliberation_size'] = strlen($typeacte['Typeacte']['gabarit_acte']);
                $this->request->data['Deliberation']['deliberation_type'] = $finfo->buffer($typeacte['Typeacte']['gabarit_acte']);
            }

            if ($this->Deliberation->save($this->data)) {
                $this->Filtre->Supprimer();
                $delibId = $this->Deliberation->getLastInsertId();

                $seances = array();
                if (isset($this->data['Seance']['Seance'])) {
                    if (!empty($this->data['Seance']['Seance'])) {
                        foreach ($this->data['Seance']['Seance'] as $seance_id) {
                            $seances[] = $seance_id;
                        }
                    }
                }

                if (array_key_exists('Infosup', $this->data)) {
                    $this->Deliberation->Infosup->saveCompacted($this->data['Infosup'], $delibId, 'Deliberation');
                }

                $this->Seance->reOrdonne($delibId, $seances);
                unset($this->request->data['Seance']);

                /* if (isset($this->data['Deliberation']['seance_id']) && !empty($this->data['Deliberation']['seance_id'])) {
                   foreach($this->data['Deliberation']['seance_id']as $key => $seance_id) {
                   $this->Deliberationseance->create();
                   $this->request->data['Deliberationseance']['deliberation_id'] = $delibId;
                   $this->request->data['Deliberationseance']['seance_id'] = $seance_id;
                   $this->request->data['Deliberationseance']['position'] = $this->Seance->getLastPosition($seance_id);
                   $this->Deliberationseance->save($this->data['Deliberationseance']);
                   }
                 }*/

                $this->Session->setFlash('Le synthese \'' . $delibId . '\' a été ajouté', 'growl');
                $this->Deliberation->commit();
                $sortie = true;
            } else {
                $this->Seance->rollback();
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }

        if ($sortie) {
            if (isset($this->request->data['lienTab'])) {
                return $this->redirect(array('controller' => 'deliberations', 'action' => 'edit', $delibId, 'lienTab' => $this->request->data['lienTab']));
            }
            return $this->redirect($redirect);
        } else {
            $this->request->data['Service']['libelle'] = $this->Deliberation->Service->doList($user['User']['service']);
            $this->request->data['Redacteur']['nom'] = $this->User->field('nom', array('User.id' => $user['User']['id']));
            $this->request->data['Redacteur']['prenom'] = $this->User->field('prenom', array('User.id' => $user['User']['id']));

            if (!empty($this->data['Deliberation']['num_pref'])) {
                $this->request->data['Deliberation']['num_pref_libelle'] = $this->data['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->data['Deliberation']['num_pref']);
                $this->request->data['Deliberation']['num_pref'] = $this->data['Deliberation']['num_pref'];
            }

            $this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
            $this->set('rapporteurs', $this->Acteur->generateListElus('Acteur.nom'));
            $this->set('selectedRapporteur', $this->Acteur->selectActeurEluIdParDelegationId($user['User']['service']));
            $this->set('date_seances', $this->Seance->generateList(null, $canEditAll, array_keys($this->Session->read('user.Nature'))));

            if (!empty($this->request->data['Deliberation']['date_limite'])) {
                App::uses('CakeTime', 'Utility');
                $this->set('date_limite', CakeTime::format($this->request->data['Deliberation']['date_limite'], '%d/%m/%Y', 'invalid'));

            }

            $this->set('profil_id', $user['User']['profil_id']);
            $this->Infosupdef->Behaviors->attach('Containable');
            $this->set('infosupdefs', $this->Infosupdef->find('all', array('conditions' => array('model' => 'Deliberation', 'actif' => true),
                'order' => 'ordre',
                'contain' => array('Profil.id'))));
            $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Deliberation'));
            $this->set('DELIBERATIONS_MULTIPLES', Configure::read('DELIBERATIONS_MULTIPLES'));
            $this->set('redirect', $redirect);

            /* valeurs initiales des info supplémentaires */
            $this->request->data['Infosup'] = $this->Infosupdef->valeursInitiales('Deliberation');

            // initialisation de la liste des types des séances
            $typeseances = array();
            if (!empty($this->data['Deliberation']['typeacte_id'])) {
                App::import('model', 'TypeseancesTypeacte');
                $TypeseancesTypeacte = new TypeseancesTypeacte();
                $typeseance_ids = $TypeseancesTypeacte->getTypeseanceParNature($this->data['Deliberation']['typeacte_id']);
                $typeseances = $this->Typeseance->find('list', array('conditions' => array('Typeseance.id' => $typeseance_ids)));
            }
            $this->set('typeseances', $typeseances);

            // initialisation de la liste des séances
            $seances = array();
            if (!empty($this->request->data['Typeseance']['Typeseance'])) {
                $selectedTypeseanceIds = set::extract('/Typeseance/Typeseance', $this->request->data);

                $seances_tmp = $this->Seance->find('all', array('conditions' => array('Seance.type_id' => $selectedTypeseanceIds,
                    'Seance.traitee' => 0),
                    'order' => array('Seance.date' => 'ASC'),
                    'contain' => array('Typeseance.libelle', 'Typeseance.retard'),
                    'fields' => array('Seance.id', 'Seance.type_id', 'Seance.date')));
                foreach ($seances_tmp as $seance)
                    $seances[$seance['Seance']['id']] = $seance['Typeseance']['libelle'] . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));

                foreach ($seances_tmp as $seance) {
                    $bSeanceok = false;

                    if ($canEditAll)
                        $bSeanceok = true;

                    if (!$bSeanceok) {
                        $iTime = strtotime($seance['Seance']['date']);
                        if (time() < mktime(0, 0, 0, date("m", $iTime), date("d", $iTime) - $seance['Typeseance']['retard'], date("Y", $iTime)))
                            $bSeanceok = true;

                    }

                    if ($bSeanceok)
                        $seances[$seance['Seance']['id']] = $seance['Typeseance']['libelle'] . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
                }
            }
            $this->set('seances', $seances);
            $this->render('add');

        }
    }

    function download($id, $file)
    {

        $this->autoRender = false;

        $fileType = $file . '_type';
        $fileSize = $file . '_size';
        $fileName = $file . '_name';
        $delib = $this->Deliberation->find('first', array('conditions' => array("Deliberation.id" => $id),
            'fields' => array($fileType, $fileSize, $fileName, $file),
            'recursive' => -1));
        $this->response->type($delib['Deliberation'][$fileType]);
        $this->response->download($delib['Deliberation'][$fileName]);
        $this->response->body($delib['Deliberation'][$file]);
    }

    function deleteDebat($id, $isCommission, $seance_id)
    {
        $this->Deliberation->id = $id;
        if (!$isCommission)
            $data = array('id' => $id,
                'debat' => '',
                'debat_name' => '',
                'debat_size' => 0,
                'debat_type' => '');
        else
            $data = array('id' => $id,
                'commission' => '',
                'commission_name' => '',
                'commission_size' => 0,
                'commission_type' => '');

        if ($this->Deliberation->save($data))
            $this->redirect("/seances/SaisirDebat/$id/$seance_id");
    }

    function downloadDelib($delib_id)
    {
        $delib = $this->Deliberation->read(null, $delib_id);

        if ($delib['Deliberation']['typeacte_id'] > 1)
            $name = "Acte_$delib_id.pdf";
        else {
            if (!empty($delib['Deliberation']['num_delib']))
                $name = $delib['Deliberation']['num_delib'] . '.pdf';
            else
                $name = "projet_$delib_id.pdf";
        }

        header('Content-type: application/pdf');
        header('Content-Length: ' . strlen($delib['Deliberation']['delib_pdf']));
        header('Content-Disposition: attachment; filename=' . $name);
        echo $delib['Deliberation']['delib_pdf'];
        exit();
    }

    function downloadSignature($delib_id)
    {
        $delib = $this->Deliberation->read(null, $delib_id);
        header('Content-type: application/zip');
        header('Content-Length: ' . strlen($delib['Deliberation']['signature']));
        header('Content-Disposition: attachment; filename=signature_acte.zip');
        echo $delib['Deliberation']['signature'];
        exit();
    }

    function _saveAnnexe($delibId, $annexe, &$annexesErrors)
    {
        App::uses('File', 'Utility');
        $this->Fido = new FidoComponent();
        $return = false;
        if ($annexe['ref'] == 'delibPrincipale')
            $Model = 'Projet';
        else
            $Model = 'Deliberation';

        $titre = !empty($annexe['titre']) ? $annexe['titre'] : $annexe['file']['name'];

        if (ini_get('upload_max_filesize') > $annexe['file']['size'])
            $annexesErrors[$titre][] = 'Limite de taille par fichier : ' . ini_get('upload_max_filesize');
        elseif ($annexe['file']['error'] != 0)
            $annexesErrors[$titre][] = 'Erreur lors de l&apos;envoi';
        elseif (is_array($annexe) && $this->Annex->isUploadedFile(array('file' => $annexe['file']))) {
            $newAnnexe = $this->Annex->create();
            $newAnnexe['Annex']['model'] = $Model;
            $newAnnexe['Annex']['foreign_key'] = $delibId;
            $newAnnexe['Annex']['titre'] = $annexe['titre'];
            $newAnnexe['Annex']['joindre_ctrl_legalite'] = $annexe['ctrl'];
            $newAnnexe['Annex']['joindre_fusion'] = $annexe['fusion'];

            $file = new File($annexe['file']['tmp_name'], false);

            //scan FIDO
            $allowed = $this->Fido->checkFile($file->path);
            $results = $this->Fido->lastResults;
            $newAnnexe['Annex']['filename'] = $annexe['file']['name'];
            if ($results['result'] == 'KO') {
                $annexesErrors[$titre][] = 'Format de fichier non reconnu. Veuillez contacter votre administrateur';
                $file->close();
                return false;
            } elseif (!$allowed) {
                $annexesErrors[$titre][] = 'Fichiers ' . $results['formatname'] . ' non autorisés. Veuillez contacter votre administrateur';
                $file->close();
                return false;
            }

            $newAnnexe['Annex']['filetype'] = $results['mimetype'];

            $newAnnexe['Annex']['size'] = $file->size();
            $newAnnexe['Annex']['data'] = $file->read();

            if ($results['extension'] == 'pdf') {
                $newAnnexe['Annex']['data_pdf'] = $file->read();
                $newAnnexe['Annex']['filename_pdf'] = $annexe['file']['name'];
                $newAnnexe['Annex']['data'] = $this->Pdf->toOdt($file->pwd());
                $newAnnexe['Annex']['filename'] = $annexe['file']['name'] . '.odt';
            } elseif ($results['extension'] == 'odt') {
                $newAnnexe['Annex']['data_pdf'] = $this->Conversion->convertirFichier($file->pwd(), 'pdf');
                $newAnnexe['Annex']['filename_pdf'] = $annexe['file']['name'] . '.pdf';
            }
            $file->close();

            if (!$this->Annex->save($newAnnexe['Annex'])) {
                foreach ($this->Annex->validationErrors as $error_annexe)
                    $annexesErrors[$titre][] = implode(',', $error_annexe);
                $this->Annex->validationErrors = array();
            } else $return = true;
        } else
            $annexesErrors[$titre][] = 'Erreur inconnue';

        return $return;
    }

    function edit($id = null)
    {
        $annexesErrors = array();
        $user = $this->Session->read('user');
        /* initialisation du lien de redirection */
        $redirect = $this->Session->read('user.User.lasturl');
        $canEditAll = $this->Droits->check($user['User']['id'], "Deliberations:editerTous");

        $pos = strrpos(getcwd(), 'webroot');
        $path = substr(getcwd(), 0, $pos);
        $path_projet = $path . "webroot/files/generee/projet/$id/";
        $typeseances_selected = array();
        $seances = array();
        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        if (!$this->request->isPut()) {
            $this->Deliberation->Behaviors->attach('Containable');
            $this->Seance->Behaviors->attach('Containable');

            $this->request->data = $this->Deliberation->find('first', array(
                'contain' => array('Annex.id', 'Annex.filetype', 'Annex.model',
                    'Annex.foreign_key', 'Annex.filename', 'Annex.filename_pdf',
                    'Annex.titre', 'Annex.joindre_ctrl_legalite', 'Annex.joindre_fusion',
                    'Infosup', 'Seance', 'Typeseance', 'Redacteur.id', 'Redacteur.nom', 'Redacteur.prenom'),
                'conditions' => array('Deliberation.id' => $id)));
            App::import('Model', 'TypeseancesTypeacte');
            $TypeseancesTypeacte = new TypeseancesTypeacte();
            $typeseance_ids = $TypeseancesTypeacte->getTypeseanceParNature($this->request->data['Deliberation']['typeacte_id']);
            $typeseances = $this->Typeseance->find('list', array('conditions' => array('Typeseance.id' => $typeseance_ids), 'order' => 'libelle'));
            foreach ($this->request->data['Typeseance'] as $typeseance)
                $typeseances_selected[] = $typeseance['id'];

            $seances_tmp = $this->Seance->find('all', array(
                'conditions' => array(
                    'Seance.type_id' => $typeseances_selected,
                    'Seance.traitee' => 0
                ),
                'order' => array('Typeseance.libelle' => 'ASC', 'Seance.date' => 'ASC'),
                'contain' => array('Typeseance.libelle', 'Typeseance.retard'),
                'fields' => array('Seance.id', 'Seance.type_id', 'Seance.date')));

            $seances_selected = $this->Deliberation->getCurrentSeances($id, false);
            foreach ($seances_tmp as $seance) {
                $bSeanceok = false;

                if ($canEditAll)
                    $bSeanceok = true;

                if (!$bSeanceok && !empty($seances_selected) && $seances_selected[0] == $seance['Seance']['id'])
                    $bSeanceok = true;

                if (!$bSeanceok) {
                    $iTime = strtotime($seance['Seance']['date']);
                    if (time() < mktime(0, 0, 0, date("m", $iTime), date("d", $iTime) - $seance['Typeseance']['retard'], date("Y", $iTime)))
                        $bSeanceok = true;

                }

                if ($bSeanceok)
                    $seances[$seance['Seance']['id']] = $seance['Typeseance']['libelle'] . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
            }

            if (Configure::read('DELIBERATIONS_MULTIPLES')) {
                $this->Deliberation->Multidelib->Behaviors->attach('Containable');
                $multiDelibs = $this->Deliberation->Multidelib->find('all', array(
                    'fields' => array('Multidelib.id', 'Multidelib.objet',
                        'Multidelib.deliberation', 'Multidelib.deliberation_name',
                        'Multidelib.objet_delib', 'Multidelib.deliberation_type',
                        'Multidelib.deliberation_name'),
                    'contain' => array('Annex.id', 'Annex.model',
                        'Annex.filetype', 'Annex.foreign_key',
                        'Annex.filename', 'Annex.filename_pdf',
                        'Annex.titre', 'Annex.joindre_ctrl_legalite',
                        'Annex.joindre_fusion'),
                    'conditions' => array('Multidelib.parent_id' => $id),
                    'order' => array('Multidelib.id')));
                foreach ($multiDelibs as $imd => $multiDelib) {
                    $this->request->data['Multidelib'][$imd] = $multiDelib['Multidelib'];
                    $this->request->data['Multidelib'][$imd]['Annex'] = $multiDelib['Annex'];
                }
            }
            $natures = array_keys($this->Session->read('user.Nature'));

            if (!in_array($this->data['Deliberation']['typeacte_id'], $natures)) {
                $this->Session->setFlash("Vous ne pouvez pas editer le projet '$id' en raison de son type d'acte.", 'growl', array('type' => 'erreur'));
                return $this->redirect($redirect);
            }

            // teste si le projet est modifiable par l'utilisateur connecté
            if (!$this->Deliberation->estModifiable($id, $user['User']['id'], $this->Droits->check($user['User']['id'], "Deliberations:editerTous"))) {
                $this->Session->setFlash("Vous ne pouvez pas editer le projet '$id'.", 'growl', array('type' => 'erreur'));
                return $this->redirect($redirect);
            }

            // initialisation des fichiers des textes
            if (!Configure::read('GENERER_DOC_SIMPLE')) {
                $this->Gedooo->createFile($path_projet, 'texte_projet.odt', $this->data['Deliberation']['texte_projet']);
                $this->Gedooo->createFile($path_projet, 'texte_synthese.odt', $this->data['Deliberation']['texte_synthese']);
                $this->Gedooo->createFile($path_projet, 'deliberation.odt', $this->data['Deliberation']['deliberation']);
            } else {
                $content = str_replace('\&quot;', '', $this->data['Deliberation']['texte_projet']);
                $content = str_replace('\\"', '"', $content);
                $content = str_replace('"\\', '"', $content);
                $this->Gedooo->createFile($path_projet, 'texte_projet.html', $content);
            }
            // création des fichiers des infosup de type odtFile
            foreach ($this->data['Infosup'] as $infosup) {
                $infoSupDef = $this->Infosupdef->find('first', array(
                    'recursive' => -1,
                    'fields' => array('type'),
                    'conditions' => array('id' => $infosup['infosupdef_id'], 'model' => 'Deliberation', 'actif' => true)));
                if ($infoSupDef['Infosupdef']['type'] == 'odtFile' && !empty($infosup['file_name']) && !empty($infosup['content'])) {
                    $this->Gedooo->createFile($path_projet, $infosup['file_name'], $infosup['content']);
                }
            }
            // création des fichiers des annexes de type vnd.oasis.opendocument
            $annexes = $this->Annex->find('all', array(
                'recursive' => -1,
                'fields' => array('filename', 'data'),
                'conditions' => array(
                    //  'Annex.Model'=> 'Deliberation',
                    'Annex.foreign_key' => $id,
                    'Annex.filetype like' => '%vnd.oasis.opendocument%')));

            foreach ($annexes as $annexe) {
                $this->Gedooo->createFile($path_projet, $annexe['Annex']['filename'], $annexe['Annex']['data']);
            }

            // initialisation des délibérations rattachées
            if (array_key_exists('Multidelib', $this->data)) {
                foreach ($this->data['Multidelib'] as $delibRattachee) {
                    $path_projet_delibRattachee = $path . "webroot/files/generee/projet/" . $delibRattachee['id'] . "/";
                    if (!Configure::read('GENERER_DOC_SIMPLE')) {
                        $this->Gedooo->createFile($path_projet_delibRattachee, 'deliberation.odt', $delibRattachee['deliberation']);
                    }
                    // création des fichiers des annexes de type vnd.oasis.opendocument
                    $annexes = $this->Annex->find('all', array(
                        'recursive' => -1,
                        'fields' => array('filename', 'data'),
                        'conditions' => array(
                            'Annex.model' => 'Deliberation',
                            'Annex.foreign_key' => $delibRattachee['id'],
                            'Annex.filetype like' => '%vnd.oasis.opendocument%')));
                    foreach ($annexes as $annexe) {
                        $this->Gedooo->createFile($path_projet_delibRattachee, $annexe['Annex']['filename'], $annexe['Annex']['data']);
                    }
                }
            }
            if (!empty($this->data['Deliberation']['num_pref']))
                $this->request->data['Deliberation']['num_pref_libelle'] = $this->data['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->data['Deliberation']['num_pref']);

            $this->request->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->request->data['Infosup']);
            $this->request->data['Deliberation']['date_limite'] = date("d/m/Y", (strtotime($this->data['Deliberation']['date_limite'])));
            $this->request->data['Service']['libelle'] = $this->Deliberation->Service->doList($this->request->data['Deliberation']['service_id']);
            $this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));

            $this->set('selectedTypeacteId', $this->request->data['Deliberation']['typeacte_id']);

            //Pour l'affichage de l'onglet
            if (isset($this->request['named']['lienTab']))
                $this->set('lienTab', $this->request['named']['lienTab']);

            $this->set('seances', $seances);
            $this->set('typeseances_selected', $typeseances_selected);
            $this->set('typeseances', $typeseances);
            $this->set('seances_selected', $seances_selected);
            $this->set('rapporteurs', $this->Acteur->generateListElus('Acteur.nom'));
            $this->set('selectedRapporteur', $this->request->data['Deliberation']['rapporteur_id']);
            $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Deliberation'));
            $this->set('profil_id', $user['User']['profil_id']);
            $this->Infosupdef->Behaviors->attach('Containable');
            $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                'conditions' => array('model' => 'Deliberation', 'actif' => true),
                'order' => 'ordre',
                'contain' => array('Profil.id'))));

            if (Configure::read('USE_PASTELL')) {
                $res = $this->Nomenclature->generatetreelist(null, null, null, '___');
                $this->set('nomenclatures', $res);
            }

            $this->set('DELIBERATIONS_MULTIPLES', Configure::read('DELIBERATIONS_MULTIPLES'));
            $this->set('is_multi', $this->request->data['Deliberation']['is_multidelib']);
            $this->set('redirect', $redirect);
            if ($this->request->data['Deliberation']['etat_parapheur'] >= 1) {
                $this->Session->setFlash("Attention, l'acte est en cours de signature!", 'growl', array('type' => 'erreur'));
            }
            $this->render();
        } else {
            $this->Deliberation->begin();

            $oldDelib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $id)));
            // Si on definit une seance a une delib, on la place en derniere position de la seance
            if (isset($this->data['Seance'])) {
                if (!$this->Deliberation->canSaveSeances($this->data['Seance']['Seance'])) {
                    $this->Session->setFlash("Vous ne pouvez enregistrer une seule séance délibérante", 'growl', array('type' => 'erreur'));
                    $this->redirect(array('action'=>'edit', $id));
                }
            }

            if (empty($this->data['Deliberation']['theme_id']))
                unset($this->request->data['Deliberation']['theme_id']);

            $seances_selected = array();
            if (isset($this->data['Seance']['Seance'])) {
                if (!empty($this->data['Seance']['Seance'])) {
                    foreach ($this->data['Seance']['Seance'] as $seance_id) {
                        $seances_selected[] = $seance_id;
                    }
                }
            }
            //TODO Le reOrdonne devrait etre testé pour ne pasle faire si les dates de séance ne change pas
            $this->Seance->reOrdonne($id, $seances_selected);
            unset ($this->request->data['Seance']['Seance']);

            if (!Configure::read('GENERER_DOC_SIMPLE')) {
                if (array_key_exists('texte_projet', $this->data['Deliberation'])) {
                    $this->request->data['Deliberation']['texte_projet_name'] = $this->data['Deliberation']['texte_projet']['name'];
                    $this->request->data['Deliberation']['texte_projet_size'] = $this->data['Deliberation']['texte_projet']['size'];
                    $this->request->data['Deliberation']['texte_projet_type'] = $this->data['Deliberation']['texte_projet']['type'];
                    if (empty($this->data['Deliberation']['texte_projet']['tmp_name'])) {
                        $this->request->data['Deliberation']['texte_projet'] = '';
                    } else {
                        $tp = $this->_getFileData($this->data['Deliberation']['texte_projet']['tmp_name'], $this->data['Deliberation']['texte_projet']['size']);
                        $this->request->data['Deliberation']['texte_projet'] = $tp;
                    }
                } else {
                    $this->request->data['Deliberation']['texte_projet'] = file_get_contents($path_projet . 'texte_projet.odt');
                }
                // Initialisation de la note de synthèse
                if (array_key_exists('texte_synthese', $this->data['Deliberation'])) {
                    $this->request->data['Deliberation']['texte_synthese_name'] = $this->data['Deliberation']['texte_synthese']['name'];
                    $this->request->data['Deliberation']['texte_synthese_size'] = $this->data['Deliberation']['texte_synthese']['size'];
                    $this->request->data['Deliberation']['texte_synthese_type'] = $this->data['Deliberation']['texte_synthese']['type'];
                    if (empty($this->data['Deliberation']['texte_synthese']['tmp_name']))
                        $this->request->data['Deliberation']['texte_synthese'] = '';
                    else {
                        $ts = $this->_getFileData($this->data['Deliberation']['texte_synthese']['tmp_name'], $this->data['Deliberation']['texte_synthese']['size']);
                        $this->request->data['Deliberation']['texte_synthese'] = $ts;
                    }
                } else {
                    $this->request->data['Deliberation']['texte_synthese'] = file_get_contents($path_projet . 'texte_synthese.odt');
                }

                // Initialisation du texte de délibération
                if (array_key_exists('deliberation', $this->data['Deliberation'])) {
                    $this->request->data['Deliberation']['deliberation_name'] = $this->data['Deliberation']['deliberation']['name'];
                    $this->request->data['Deliberation']['deliberation_size'] = $this->data['Deliberation']['deliberation']['size'];
                    $this->request->data['Deliberation']['deliberation_type'] = $this->data['Deliberation']['deliberation']['type'];
                    if (empty($this->data['Deliberation']['deliberation']['tmp_name']))
                        $this->request->data['Deliberation']['deliberation'] = '';
                    else {
                        $td = $this->_getFileData($this->data['Deliberation']['deliberation']['tmp_name'], $this->data['Deliberation']['deliberation']['size']);
                        $this->request->data['Deliberation']['deliberation'] = $td;
                    }
                } else {
                    $this->request->data['Deliberation']['deliberation'] = file_get_contents($path_projet . 'deliberation.odt');
                }
            }
            if ($oldDelib['Deliberation']['is_multidelib'] != 1)
                if (empty($this->data['Deliberation']['is_multidelib']) OR (@$this->data['Deliberation']['is_multidelib'] == 0))
                    $this->request->data['Deliberation']['objet_delib'] = $this->data['Deliberation']['objet'];

            $this->request->data['Deliberation']['date_limite'] = $this->Utils->FrDateToUkDate($this->data['date_limite']);

            if ($success = $this->Deliberation->save($this->request->data)) {
                $this->Historique->enregistre($id, $user['User']['id'], "Modification du projet");
                $this->Filtre->supprimer();

                // sauvegarde des informations supplémentaires
                $infossupDefs = $this->Infosupdef->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id', 'code'),
                    'conditions' => array(
                        'type' => 'odtFile',
                        'model' => 'Deliberation',
                        'actif' => true
                    )));
                foreach ($infossupDefs as $infossupDef) {
                    $infosup = $this->Infosup->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'file_name', 'file_type'),
                        'conditions' => array('foreign_key' => $id, 'model' => 'Deliberation', 'infosupdef_id' => $infossupDef['Infosupdef']['id'])));
                    if (empty($infosup) || empty($infosup['Infosup']['file_name']))
                        continue;
                    $odtFileUri = $path_projet . $infosup['Infosup']['file_name'];

                    if (file_exists($odtFileUri)) {
                        $stat = stat($odtFileUri);
                        if ($stat > 0) {
                            $infosup['Infosup']['content'] = file_get_contents($odtFileUri);
                            $infosup['Infosup']['file_size'] = $stat['size'];
                            $this->Infosup->save($infosup);
                        }
                    }
                }

                if (array_key_exists('Infosup', $this->data)) {
                    $success = $this->Deliberation->Infosup->saveCompacted($this->data['Infosup'], $this->data['Deliberation']['id'], 'Deliberation');
                }

                // sauvegarde des nouvelles annexes
                if ($success && array_key_exists('Annex', $this->data))
                    foreach ($this->data['Annex'] as $annexe) {
                        //Cas bloc annexe vide
                        if (empty($annexe['file']['name']))
                            continue;
                        if ($annexe['ref'] == 'delibPrincipale')
                            $success = $this->_saveAnnexe($id, $annexe, $annexesErrors) && $success;
                        if ($annexe['ref'] == 'delibRattachee' . $id)
                            $success = $this->_saveAnnexe($id, $annexe, $annexesErrors) && $success;
                    }

                if ($success) {
                    // suppression des annexes
                    if (array_key_exists('AnnexesASupprimer', $this->data))
                        foreach ($this->data['AnnexesASupprimer'] as $annexeId) $this->Annex->delete($annexeId);
                    // modification des annexes
                    if (array_key_exists('AnnexesAModifier', $this->data)) {
                        foreach ($this->data['AnnexesAModifier'] as $annexeId => $annexe) {
                            $annex_filename = $this->Annex->find('first', array(
                                'recursive' => -1,
                                'fields' => array('filename', 'filetype', 'id', 'data'),
                                'conditions' => array('Annex.id' => $annexeId)));
                            $pos = strpos($annex_filename['Annex']['filetype'], 'vnd.oasis.opendocument');
                            if ($pos !== false) {
                                $path = WEBROOT_PATH . "/files/generee/projet/" . $id . "/" . $annex_filename['Annex']['filename'];
                                $data_pdf = $this->Conversion->convertirFichier($path, 'pdf');

                                if (is_array($data_pdf)) $data_pdf = null;
                                $this->Annex->save(array(
                                    'id' => $annexeId,
                                    'titre' => $annexe['titre'],
                                    'joindre_ctrl_legalite' => $annexe['joindre_ctrl_legalite'],
                                    'joindre_fusion' => $annexe['joindre_fusion'],
                                    'data' => file_get_contents($path),
                                    'data_pdf' => $data_pdf));
                            } else {
                                $this->Annex->save(array(
                                    'id' => $annexeId,
                                    'titre' => $annexe['titre'],
                                    'joindre_ctrl_legalite' => $annexe['joindre_ctrl_legalite'],
                                    'joindre_fusion' => $annexe['joindre_fusion']));
                            }
                            if (!empty($this->Annex->validationErrors)) {
                                $success = false;
                                $titre = !empty($annexe['titre']) ? $annexe['titre'] : $annex_filename['Annex']['filename'];
                                foreach ($this->Annex->validationErrors as $validationError) {
                                    $annexesErrors[$titre][] = implode(',', $validationError);
                                }
                            }
                        }
                    }
                    // suppression des délibérations rattachées
                    if (array_key_exists('MultidelibASupprimer', $this->data)) {
                        foreach ($this->data['MultidelibASupprimer'] as $delibId) {
                            $this->Deliberation->supprimer($delibId);
                            unset($this->request->data['Multidelib'][$delibId]);
                        }
                    }
                    // sauvegarde de délibérations rattachées
                    if (array_key_exists('Multidelib', $this->data)) {
                        foreach ($this->data['Multidelib'] as $iref => $multidelib) {
                            $delibRattacheeId = $this->Deliberation->saveDelibRattachees($id, $multidelib, $this->data['Deliberation']['objet']);
                            // sauvegarde des nouvelles annexes pour cette delib rattachée
                            if (array_key_exists('Annex', $this->data))
                                foreach ($this->data['Annex'] as $annexe)
                                    if ($annexe['ref'] == 'delibRattachee' . $iref) if (!$this->_saveAnnexe($delibRattacheeId, $annexe, $annexesErrors)) $this->redirect($redirect);
                        }
                    }

                    //Mise à jour des Multi-délibération
                    $multis = $this->Deliberation->find('all', array(
                        'conditions' => array('Deliberation.parent_id' => $id),
                        'recursive' => -1,
                        'fields' => array('Deliberation.id')));
                    foreach ($multis as $projetRatt) {
                        $this->Deliberation->id = $projetRatt['Deliberation']['id'];
                        $multi_Delib['service_id'] = $oldDelib['Deliberation']['service_id'];
                        if (!empty($this->data['Deliberation']['theme_id']))
                            $multi_Delib['theme_id'] = $this->request->data['Deliberation']['theme_id'];
                        $multi_Delib['rapporteur_id'] = $this->request->data['Deliberation']['rapporteur_id'];
                        $multi_Delib['typeacte_id'] = $this->request->data['Deliberation']['typeacte_id'];
                        $multi_Delib['redacteur_id'] = $oldDelib['Deliberation']['redacteur_id'];
                        $multi_Delib['circuit_id'] = $oldDelib['Deliberation']['circuit_id'];
                        $multi_Delib['is_multidelib'] = false;
                        $multi_Delib['etat'] = $oldDelib['Deliberation']['etat'];

                        $success = $this->Deliberation->save($multi_Delib) && $success;
                    }
                }
            }

            if ($success) {
                $this->Deliberation->commit();
                $this->Session->setFlash("Le projet $id a été enregistré", 'growl');
                $sortie = true;
                //FIXME Rediriger vers la page de provenance
                $redirect = '/';

                //Envoi d'une notification de modification au rédacteur
                $currentUser = $this->Session->read('user.User.id');
                $redacteurId = $oldDelib['Deliberation']['redacteur_id'];
                if ($currentUser != $redacteurId){
                    $this->_notifier($id, $redacteurId, 'modif_projet_cree');
                }
                //Envoi d'une notification de modification aux utilisateurs qui ont déjà validé le projet
                $destinataires = $this->Traitement->whoIsPrevious($id);
                foreach ($destinataires as $destinataire_id)
                    if ($destinataire_id != $currentUser)
                        $this->_notifier($id, $destinataire_id, 'modif_projet_valide');
            } else {
                $this->Deliberation->rollback();
                $this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                if (!empty($annexesErrors)) {
                    $msg_annexe_error = "";
                    foreach ($annexesErrors as $annexeName => $annexError) {
                        $msg_annexe_error .= "<strong>Annexe &apos;" . $annexeName . "&apos; :</strong><br>";
                        foreach ($annexError as $error) {
                            $msg_annexe_error .= "- " . $error . "<br/>";
                        }
                    }
                    $this->Session->setFlash($msg_annexe_error, 'growl', array('type' => 'erreur'));
                }
                $this->set('errors_Infosup', $this->Deliberation->Infosup->invalidFields());
                $sortie = false;
            }

            if ($sortie)
                $this->redirect($redirect);
            else {
                $this->set('validationErrorsArray', $this->Deliberation->validationErrors);

                if (!isset($this->data['Deliberation']['texte_projet_name']))
                    $this->request->data['Deliberation']['texte_projet_name'] = $oldDelib['Deliberation']['texte_projet_name'];
                if (!isset($this->data['Deliberation']['texte_synthese_name']))
                    $this->request->data['Deliberation']['texte_synthese_name'] = $oldDelib['Deliberation']['texte_synthese_name'];
                if (!isset($this->data['Deliberation']['deliberation_name']))
                    $this->request->data['Deliberation']['deliberation_name'] = $oldDelib['Deliberation']['deliberation_name'];

                App::import('model', 'TypeseancesTypeacte');
                $TypeseancesTypeacte = new TypeseancesTypeacte();
                $typeseance_ids = $TypeseancesTypeacte->getTypeseanceParNature($this->request->data['Deliberation']['typeacte_id']);
                $typeseances = $this->Typeseance->find('list', array(
                    'conditions' => array('Typeseance.id' => $typeseance_ids),
                    'order' => array('Typeseance.libelle' => 'ASC')));

                if (isset($this->request->data['Typeseance']) && !empty($this->request->data['Typeseance']))
                    foreach ($typeseances as $key => $typeseance)
                        if (isset($this->request->data['Typeseance']['Typeseance']) && !empty($this->request->data['Typeseance']['Typeseance']))
                            foreach ($this->request->data['Typeseance']['Typeseance'] as $num_type)
                                if ($num_type == $key)
                                    $typeseances_selected[] = $key;

                $seances = array();
                $seances_tmp = $this->Seance->find('all', array(
                    'conditions' => array('Seance.type_id' => $typeseances_selected, 'Seance.traitee' => 0),
                    'order' => array('Typeseance.libelle' => 'ASC', 'Seance.date' => 'ASC'),
                    'contain' => array('Typeseance.libelle', 'Typeseance.retard'),
                    'fields' => array('Seance.id', 'Seance.type_id', 'Seance.date')));
                $bSeanceok = false;
                foreach ($seances_tmp as $seance) {
                    $bSeanceok = ($canEditAll || (!$bSeanceok && $seances_selected[0] == $seance['Seance']['id']));

                    if (!$bSeanceok) {
                        $iTime = strtotime($seance['Seance']['date']);
                        if (time() < mktime(0, 0, 0, date("m", $iTime), date("d", $iTime) - $seance['Typeseance']['retard'], date("Y", $iTime)))
                            $bSeanceok = true;
                    }else
                        $seances[$seance['Seance']['id']] = $seance['Typeseance']['libelle'] . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
                }

                if (Configure::read('DELIBERATIONS_MULTIPLES')) {
                    if (isset($this->request->data['Multidelib']))
                        unset($this->request->data['Multidelib']);
                    $this->Deliberation->Multidelib->Behaviors->attach('Containable');
                    $multiDelibs = $this->Deliberation->Multidelib->find('all', array(
                        'fields' => array('Multidelib.id', 'Multidelib.objet',
                            'Multidelib.deliberation', 'Multidelib.deliberation_name',
                            'Multidelib.objet_delib', 'Multidelib.deliberation_type',
                            'Multidelib.deliberation_name'),
                        'contain' => array('Annex.id', 'Annex.model',
                            'Annex.filetype', 'Annex.foreign_key',
                            'Annex.filename', 'Annex.filename_pdf',
                            'Annex.titre', 'Annex.joindre_ctrl_legalite',
                            'Annex.joindre_fusion'),
                        'conditions' => array('Multidelib.parent_id' => $id),
                        'order' => array('Multidelib.id')));
                    foreach ($multiDelibs as $imd => $multiDelib) {
                        $this->request->data['Multidelib'][$imd] = $multiDelib['Multidelib'];
                        $this->request->data['Multidelib'][$imd]['Annex'] = $multiDelib['Annex'];
                    }
                }

                $this->set('seances', $seances);
                $this->set('seances_selected', $seances_selected);
                $this->set('typeseances', $typeseances);
                $this->set('typeseances_selected', $typeseances_selected);

                $this->set('services', $this->Deliberation->Service->find('list', array('conditions' => array('Service.actif' => '1'))));
                $this->set('themes', $this->Deliberation->Theme->find('list', array('conditions' => array('Theme.actif' => '1'))));
                $this->set('circuits', $this->Deliberation->Circuit->find('list'));
                $this->set('datelim', $this->data['Deliberation']['date_limite']);
                $annexes = $this->Annex->find('all', array('conditions' => array('model' => 'Projet', 'foreign_key' => $id)));
                foreach ($annexes as $id => $annexe)
                    $this->request->data['Annex'][$id] = $annexe['Annex'];

                if (!empty($this->data['Deliberation']['num_pref']))
                    $this->request->data['Deliberation']['num_pref_libelle'] = $this->data['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->data['Deliberation']['num_pref']);


                $this->set('rapporteurs', $this->Acteur->generateListElus('Acteur.nom'));
                $this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
                $this->set('redirect', $redirect);

                $this->set('profil_id', $user['User']['profil_id']);
                //$this->set('date_seances', $this->Seance->generateList(null, $canEditAll, array_keys($this->Session->read('user.Nature'))));
                $this->set('infosupdefs', $this->Infosupdef->find('all', array('conditions' => array('model' => 'Deliberation', 'actif' => true),
                    'order' => 'ordre',
                    'contain' => array('Profil.id'))));

                $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Deliberation'));
                $this->set('is_multi', $oldDelib['Deliberation']['is_multidelib']);
                $this->set('DELIBERATIONS_MULTIPLES', Configure::read('DELIBERATIONS_MULTIPLES'));
                $this->set('selectedTypeacteId', $this->request->data['Deliberation']['typeacte_id']);
            }
        }
    }

    function recapitulatif($id = null)
    {
        if (empty($this->data)) {
            if (!$id) {
                $this->Session->setFlash('Invalide id pour la deliberation', 'growl', array('type' => 'erreur'));
                $this->redirect('/deliberations/mesProjetsRedaction');
            }
            $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $id)));
            for ($i = 0; $i < count($delib['Seance']); $i++) {
                $type = $this->Seance->Typeseance->find('first', array('conditions' => array('Typeseance.id' => $delib['Seance'][$i]['type_id']),
                    'recursive' => -1,
                    'fields' => array('libelle')));
                $delib['Seance'][$i]['Typeseance']['libelle'] = $type['Typeseance']['libelle'];
            }

            if (!empty($delib['Deliberation']['date_limite']))
                $delib['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($delib['Deliberation']['date_limite']));
            $delib['Deliberation']['created'] = $this->Date->frenchDateConvocation(strtotime($delib['Deliberation']['created']));
            $delib['Deliberation']['modified'] = $this->Date->frenchDateConvocation(strtotime($delib['Deliberation']['modified']));
            $id_service = $delib['Service']['id'];
            $delib['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
            $tab_circuit = $delib['Deliberation']['circuit_id'];
            $delib['Circuit']['libelle'] = $this->Circuit->getLibelle($tab_circuit);
            //on recupere la position de l'user dans le circuit
            $this->set('deliberation', $delib);
            $this->set('visu', $this->requestAction('/cakeflow/circuits/visuCircuit/' . $tab_circuit, array('return')));
        }
    }

    function delete($id = null)
    {
        $delib = $this->Deliberation->find('first', array(
            'recursive' => -1,
            'fields' => array('Deliberation.id', 'Deliberation.redacteur_id', 'Deliberation.etat'),
            'conditions' => array('id' => $id)));

        if (empty($delib)) {
            $this->Session->setFlash('Invalide id pour le projet de deliberation : suppression impossible', 'growl', array('type' => 'erreur'));
        } else {
            $user_connecte = $this->Session->read('user.User.id');
            $canDelete = $this->Droits->check($user_connecte, "Deliberations:delete");
            if ((($delib['Deliberation']['redacteur_id'] == $user_connecte) && ($delib['Deliberation']['etat'] == 0)) || ($canDelete)) {
                $this->Deliberation->supprimer($id);
                $this->Session->setFlash('Le projet \'' . $id . '\' a été supprimé.', 'growl');
            } else {
                $this->Session->setFlash('Vous ne pouvez pas supprimer ce projet', 'growl');
            }
        }
        $this->redirect('/deliberations/mesProjetsRedaction');
    }

    function addIntoCircuit($id = null)
    {
        $this->request->data = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $id)));
        $user_connecte = $this->Session->read('user.User.id');
        if ($this->request->data['Deliberation']['circuit_id'] != 0) {
            // enregistrement de l'historique
            $message = "Projet injecté au circuit : " . $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);
            $this->Historique->enregistre($id, $user_connecte, $message);
            $this->request->data['Deliberation']['date_envoi'] = date('Y-m-d H:i:s', time());
            $this->request->data['Deliberation']['etat'] = '1';
            if ($this->Deliberation->save($this->request->data)) {
                // insertion dans le circuit de traitement
                $this->Deliberation->id = $id;
                if ($this->Traitement->targetExists($id)) {
                    $this->Circuit->ajouteCircuit($this->data['Deliberation']['circuit_id'], $id, $user_connecte);
                    $this->Traitement->Visa->replaceDynamicTrigger($id);
                    $members = $this->Traitement->whoIsNext($id);
                    if (empty($members)) {
                        $this->Historique->enregistre($id, $user_connecte, 'Projet validé');
                        $this->Deliberation->saveField('etat', 2);
                    } else {
                        while ($members[0] == $user_connecte) {
                            $traitementTermine = $this->Traitement->execute('OK', $user_connecte, $id);
                            $this->Historique->enregistre($id, $user_connecte, 'Projet visé (auto)');
                            if ($traitementTermine) {
                                $this->Historique->enregistre($id, $user_connecte, 'Projet validé');
                                $this->Deliberation->saveField('etat', 2);
                                $this->Session->setFlash('Projet inséré dans le circuit et validé', 'growl');
                                $this->redirect('/deliberations/mesProjetsValides');
                            }

                            $members = $this->Traitement->whoIsNext($id);
                        }
                        foreach ($members as $destinataire_id)
                            $this->_notifier($id, $destinataire_id, 'traiter');
                        $this->Session->setFlash('Projet inséré dans le circuit et visé', 'growl');
                        $this->redirect('/deliberations/mesProjetsRedaction');
                    }
                } else {
                    $this->Circuit->insertDansCircuit($this->data['Deliberation']['circuit_id'], $id, $user_connecte);
                    $options = array(
                        'insertion' => array(
                            '0' => array(
                                'Etape' => array(
                                    'etape_id' => null,
                                    'etape_nom' => 'Rédacteur',
                                    'etape_type' => 1
                                ),
                                'Visa' => array(
                                    '0' => array(
                                        'trigger_id' => $user_connecte,
                                        'type_validation' => 'V'
                                    )
                                )
                            )
                        )
                    );
                    $traitementTermine = $this->Traitement->execute('IN', $user_connecte, $id, $options);

                    if ($traitementTermine) {
                        $this->Historique->enregistre($id, $user_connecte, 'Projet validé');
                        $this->Deliberation->id = $id;
                        $this->Deliberation->saveField('etat', 2);
                    }
                    $this->Traitement->Visa->replaceDynamicTrigger($id);
                }

                // envoi un mail a tous les membres du circuit
                $listeUsers = $this->Circuit->getAllMembers($this->data['Deliberation']['circuit_id']);

                $prem = true;
                foreach ($listeUsers as $etape) {
                    if ($prem) {
                        foreach ($etape as $user_id)
                            $this->_notifier($id, $user_id, 'traiter');
                        $prem = false;
                    } else {
                        foreach ($etape as $user_id)
                            $this->_notifier($id, $user_id, 'insertion');
                    }
                }
                $this->Session->setFlash('Projet inséré dans le circuit', 'growl');
                $this->redirect('/deliberations/mesProjetsRedaction');
            } else {
                $this->Session->setFlash('Problème de sauvegarde.', 'growl', array('type' => 'erreur'));
                $this->redirect('/deliberations/attribuercircuit/' . $id);
            }
        } else {
            $this->Session->setFlash('Vous devez assigner un circuit au projet de délibération.', 'growl', array('type' => 'erreur'));
            $this->redirect('/deliberations/recapitulatif/' . $id);
        }
    }

    function attribuercircuit($id = null, $circuit_id = null, $autoAppel = false)
    {
        $user_id = $this->Session->read('user.User.id');

        $circuits = $this->User->getCircuits($user_id);
        $this->set('circuits', $circuits);

        if (empty($this->data)) {
            $this->data = $this->Deliberation->read(null, $id);
            $this->set('lastPosition', '-1');

            //circuit par défaut de l'utilisateur connecté
            if ($circuit_id == null || !array_key_exists($circuit_id, $circuits))
                $circuit_id = $this->User->circuitDefaut($user_id, 'id');

            //affichage du circuit existant
            if ($circuit_id == null)
                $circuit_id = $this->data['Deliberation']['circuit_id'];

            if (isset($circuit_id)) {
                $this->set('circuit_id', $circuit_id);
                $this->set('visu', $this->requestAction('/cakeflow/circuits/visuCircuit/' . $circuit_id, array('return')));
            } else
                $this->set('circuit_id', '0');
            // initalisation du lien de retour
            if ($autoAppel) {
                $this->set('lien_retour', $this->Session->read('attribuerCircuit.lienRetour'));
            } else {
                $this->Session->write('attribuerCircuit.lienRetour', $this->referer());
                $this->set('lien_retour', $this->referer());
            }
        } else {
            $this->Deliberation->id = $id;
            $this->request->data = $this->Deliberation->find('first', array('conditions' => array("Deliberation.id" => $id), 'recursive' => -1));

            if ($this->Deliberation->saveField('circuit_id', $circuit_id)) {
                // cas pour l'editeur en ligne
                if ((Configure::read('GENERER_DOC_SIMPLE')) && ($this->data['Deliberation']['texte_projet'] == '<br />'))
                    $this->Session->setFlash('Attention, le texte projet est vide', 'growl', array('type' => 'important'));
                // Cas pour le mode OpenOffice
                if ((!Configure::read('GENERER_DOC_SIMPLE')) && ($this->data['Deliberation']['texte_projet'] == ''))
                    $this->Session->setFlash('Attention, le texte projet est vide', 'growl', array('type' => 'important'));

                $this->redirect('/deliberations/recapitulatif/' . $id);
            } else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
        }
    }

    function retour($delib_id)
    {
        $delib = $this->Deliberation->find('first', array(
            'recursive'=> -1,
            'conditions'=> array('Deliberation.id'=> $delib_id)
        ));

        if (empty($delib))
            $this->redirect($this->referer());

        if (empty($this->data)) {
            $etapes = $this->Traitement->listeEtapesPrecedentes($delib['Deliberation']['id']);
            if (empty($etapes)){
                $this->Session->setFlash('Opération impossible, l&apos;étape courante est la première du circuit.', 'growl', array('type' => 'erreur'));
                $this->redirect($this->referer());
            }
            $this->set('delib_id', $delib_id);
            $this->set('etapes', $etapes);
        } else {
            $this->Traitement->execute('JP', $this->Session->read('user.User.id'), $delib_id, array('etape_id' => $this->data['Traitement']['etape']));
            $destinataires = $this->Traitement->whoIsNext($delib_id);
            foreach ($destinataires as $destinataire_id)
                $this->_notifier($delib_id, $destinataire_id, 'traiter');

            $this->Historique->enregistre($delib_id, $this->Session->read('user.User.id'), "Projet retourné");
            $this->Session->setFlash('Opération effectuée !', 'growl');
            $this->redirect('/');
        }
    }

    function traiter($id = null, $valid = null)
    {
        $this->Deliberation->Behaviors->attach('Containable');
        $projet = $this->Deliberation->find('first', array(
            'fields' => array(
                'id', 'anterieure_id', 'service_id', 'circuit_id',
                'etat', 'num_delib', 'titre', 'objet', 'objet_delib', 'num_pref',
                'texte_projet', 'texte_projet_name', 'typeacte_id',
                'texte_synthese', 'texte_synthese_name',
                'deliberation', 'deliberation_name',
                'created', 'modified'),
            'contain' => array(
                'Typeacte.libelle',
                'Theme.libelle',
                'Service.libelle',
                'Seance.date', 'Seance.Typeseance.libelle',
                'Redacteur.id', 'Redacteur.nom', 'Redacteur.prenom',
                'Rapporteur.nom', 'Rapporteur.prenom',
                'Annex',
                'Infosup',
                'Multidelib.id', 'Multidelib.objet', 'Multidelib.objet_delib', 'Multidelib.num_delib', 'Multidelib.Annex',
                'Multidelib.etat', 'Multidelib.deliberation', 'Multidelib.deliberation_name', 'Multidelib.Typeacte.libelle',
            ),
            'conditions' => array('Deliberation.id' => $id)));

        $projet['Modeltemplate']['id'] = $this->Deliberation->Typeacte->getModelId($projet['Deliberation']['typeacte_id'], 'modeleprojet_id');
        $projet['Deliberation']['num_pref'] = $projet['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($projet['Deliberation']['num_pref']);

        if (empty($projet)) {
            $this->Session->setFlash('identifiant invalide pour le projet : ' . $id, 'growl', array('type' => 'erreur'));
            $this->redirect('/deliberations/mesProjetsATraiter');
        } else {
            if ($valid == null) {
                $nb_recursion = 0;
                $action = 'view';
                $listeAnterieure = array();
                $tab_anterieure = $this->_chercherVersionAnterieure($projet, $nb_recursion, $listeAnterieure, $action);
                $this->set('tab_anterieure', $tab_anterieure);
                $commentaires = $this->Commentaire->find('all', array('conditions' => array(
                    'Commentaire.delib_id' => $id,
                    'Commentaire.pris_en_compte' => 0),
                    'order' => 'created ASC'));
                for ($i = 0; $i < count($commentaires); $i++) {
                    $agent = $this->User->find('first', array('conditions' => array(
                        'User.id' => $commentaires[$i]['Commentaire']['agent_id']),
                        'recursive' => -1,
                        'fields' => array('nom', 'prenom')));
                    if (empty($agent))
                        $this->Session->setFlash('Identité de l\'auteur de(s) commentaire(s) inconnue.', 'growl');
                    $commentaires[$i]['Commentaire']['nomAgent'] = $agent['User']['nom'];
                    $commentaires[$i]['Commentaire']['prenomAgent'] = $agent['User']['prenom'];
                }
                $this->set('commentaires', $commentaires);
                if (!empty($projet['Seance']['date']))
                    $projet['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($projet['Seance']['date']));
                $id_service = $projet['Deliberation']['service_id'];
                $projet['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
                $projet['Circuit']['libelle'] = $this->Circuit->getLibelle($projet['Deliberation']['circuit_id']);
                $this->set('visu', $this->requestAction('/cakeflow/traitements/visuTraitement/' . $id, array('return')));
                $this->set('deliberation', $projet);
                $this->set('historiques', $this->Historique->find('all', array(
                    'conditions' => array("Historique.delib_id" => $id),
                    'order' => array('Historique.modified DESC')
                )));

                // Compactage des informations supplémentaires
                $this->request->data['Infosup'] = $this->Deliberation->Infosup->compacte($projet['Infosup'], false);
                $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('actif' => true, 'model' => 'Deliberation'),
                    'order' => 'ordre')));

                //si bloqué à une étape de délégation
                $visa = false;
                $traitement = $this->Traitement->findByTargetId($id);
                if ($traitement != null) {
                    //Si il n'y a pas eu de jump
                    $jump = array(
                        'Visa.traitement_id' => $traitement['Traitement']['id'],
                        'Visa.action' => "JS"
                    );
                    //si reste des étapes de délégation en attente (passées)
                    $delegation_restante = array(
                        'Visa.traitement_id' => $traitement['Traitement']['id'],
                        'Visa.trigger_id' => -1,
                        'Visa.action' => "RI");
                    if (!$traitement['Traitement']['treated']) {
                        $conditions = array('traitement_id' => $traitement['Traitement']['id'],
                            'numero_traitement <=' => $traitement['Traitement']['numero_traitement'],
                            'trigger_id' => -1,
                            'action' => 'RI');
                        $visa = $this->Visa->hasAny($conditions);

                        $delegation_restante['Visa.numero_traitement <'] = $traitement['Traitement']['numero_traitement'];
                    } else { // pour voir bouton actualiser sur derniere etape de délégation
                        $delegation_restante['Visa.numero_traitement <='] = $traitement['Traitement']['numero_traitement'];
                    }
                    $visas_retard = array();
                    if (!$this->Visa->hasAny($jump))
                        $visas_retard = $this->Visa->find('all', array("conditions" => $delegation_restante, "recursive" => -1));

                    //boutons MàJ visas en retard
                    $this->set('visas_retard', $visas_retard);
                }
                //Afficher bouton MàJ
                $this->set('majDeleg', $visa);
            } else {
                if ($valid == '1') {
                    $this->_accepteDossier($id);
                    $this->Session->setFlash('Vous venez de valider le projet : ' . $id, 'growl');
                } else {
                    $this->_refuseDossier($id);
                    $this->Session->setFlash('Vous venez de refuser le projet : ' . $id, 'growl');
                }
                $this->redirect('/deliberations/mesProjetsATraiter');
            }
        }
    }

    function _refuseDossier($id)
    {
        $nouvelId = $this->Deliberation->refusDossier($id);
        $this->Traitement->execute('KO', $this->Session->read('user.User.id'), $id);
        // TODO notifier par mail toutes les personnes qui ont deja vise le projet
        $destinataires = $this->Traitement->whoIsPrevious($id);
        foreach ($destinataires as $destinataire_id)
            $this->_notifier($nouvelId, $destinataire_id, 'refus');

        $this->Historique->enregistre($id, $this->Session->read('user.User.id'), 'Projet refusé');
    }

    function _accepteDossier($id)
    {
        $user_id = $this->Session->read('user.User.id');
        $traitementTermine = $this->Traitement->execute('OK', $user_id, $id);
        $this->Historique->enregistre($id, $user_id, 'Projet visé');
        if ($traitementTermine) {
            $this->Deliberation->id = $id;
            if ($this->Deliberation->saveField('etat', 2)) {
                //FIXME : variable projet ???
                /*if (isset($projet['Multidelib']) && !empty($projet['Multidelib'])) {
                    foreach ($projet['Multidelib'] as $multidelib) {
                        $this->Deliberation->id = $multidelib['id'];
                        $this->Deliberation->saveField('etat', 2);
                    }
                }*/
            }
        } else {
            $destinataires = $this->Traitement->whoIsNext($id);
            foreach ($destinataires as $destinataire_id)
                $this->_notifier($id, $destinataire_id, 'traiter');
        }
    }

    function _chercherVersionAnterieure($tab_delib, $nb_recursion, $listeAnterieure, $action)
    {
        $anterieure_id = $tab_delib['Deliberation']['anterieure_id'];

        if ($anterieure_id != 0) {

            $ant = $this->Deliberation->find('first', array('conditions' => array("Deliberation.id" => $anterieure_id),
                'recursive' => -1,
                'fields' => array('created', 'anterieure_id')));
            $lien = $this->base . '/deliberations/' . $action . '/' . $anterieure_id;
            $date_version = $ant['Deliberation']['created'];

            $listeAnterieure[$nb_recursion]['id'] = $anterieure_id;
            $listeAnterieure[$nb_recursion]['lien'] = $lien;
            $listeAnterieure[$nb_recursion]['date_version'] = $date_version;

            //on stocke les id des delibs anterieures
            $listeAnterieure = $this->_chercherVersionAnterieure($ant, $nb_recursion + 1, $listeAnterieure, $action);
        }
        return $listeAnterieure;
    }

    function transmit($seance_id = null)
    {
        $this->Deliberation->Behaviors->attach('Containable');
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        if (isset($conditions['Seance.id'])) {
            $seance_id = $conditions['Seance.id'];
            unset($conditions['Seance.id']);
        }
        if ($seance_id != null)
            $conditions['Deliberation.id'] = $this->Seance->getDeliberationsId($seance_id);
        $conditions['Deliberation.typeacte_id'] = $this->Deliberation->Typeacte->getIdDesNaturesDelib();
        $conditions['Deliberation.etat'] = 5;

        $this->paginate = array('Deliberation' => array(
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.objet_delib',
                'Deliberation.num_delib', 'Deliberation.dateAR', 'Deliberation.pastell_id',
                'Deliberation.num_pref', 'Deliberation.etat',
                'Deliberation.titre', 'Deliberation.tdt_id'),
            'contain' => array('TdtMessage', 'Seance'),
            'order' => array('Deliberation.num_delib' => 'ASC'),
            'conditions' => $conditions,
            'limit' => 20));

        $this->set('host', Configure::read('HOST'));
        $this->set('dateClassification', $this->S2low->getDateClassification());
        $id_e=null;
        if (Configure::read('USE_PASTELL')) {
            $coll = $this->Session->read('user.Collectivite');
            $id_e = $coll['Collectivite']['id_entity'];
        }

        // On affiche que les delibs vote pour.
        $deliberations = $this->Paginator->paginate('Deliberation');
        $toutes_seances = array();
        for ($i = 0; $i < count($deliberations); $i++) {

            $deliberations[$i]['Deliberation']['num_pref'] = $deliberations[$i]['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($deliberations[$i]['Deliberation']['num_pref']);

            $seances = $deliberations[$i]['Seance'];
            if (count($seances) == 1) {
                $deliberations[$i]['Seance']['id'] = $seances[0]['id'];
                $deliberations[$i]['Seance']['date'] = $seances[0]['date'];
                $deliberations[$i]['Seance']['type_id'] = $seances[0]['type_id'];
            } elseif (count($seances) > 1) {
                $tab_seances = array();
                foreach ($deliberations[$i]['Seance'] as $seance) {
                    $tab_seances[] = $seance['id'];
                }
                $seance_id = $this->Seance->getSeanceDeliberante($tab_seances);
                $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
                    'fields' => array('id', 'date', 'type_id'),
                    'recursive' => -1));
                $deliberations[$i]['Seance']['id'] = $seance['Seance']['id'];
                $deliberations[$i]['Seance']['date'] = $seance['Seance']['date'];
                $deliberations[$i]['Seance']['type_id'] = $seance['Seance']['type_id'];
            }
            if (empty($deliberations[$i]['Deliberation']['DateAR'])) {
                if (Configure::read('USE_PASTELL')) {
                    if (isset($deliberations[$i]['Deliberation']['pastell_id']))
                        $id_d = $deliberations[$i]['Deliberation']['pastell_id'];
                    if (isset($id_d)) {
                        $result = $this->Pastell->action($id_e, $id_d, 'verif-tdt');
                        $result = (array)$result;
                        if (isset($result['result']) && ($result['result'] == 1)) {
                            $infos = $this->Pastell->getInfosDocument($id_e, $id_d);
                            $infos = (array)$infos;
                            $infos['data'] = (array)$infos['data'];
                            $this->Deliberation->id = $deliberations[$i]['Deliberation']['id'];
                            $this->Deliberation->saveField('tdt_id', $infos['data']['tedetis_transaction_id']);
                            $this->Deliberation->saveField('dateAR', $infos['data']['date_ar']);
                            $this->Deliberation->saveField('bordereau', $this->Pastell->getFile($id_e, $id_d, 'bordereau'));
                        }
                    }
                }
                if (isset($deliberations[$i]['Deliberation']['tdt_id'])) {
                    $flux = $this->S2low->getFluxRetour($deliberations[$i]['Deliberation']['tdt_id']);
                    $codeRetour = substr($flux, 3, 1);
                    $deliberations[$i]['Deliberation']['code_retour'] = $codeRetour;

                    if ($codeRetour == 4) {
                        $dateAR = $this->_getDateAR($res = mb_substr($flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
                        $this->Deliberation->changeDateAR($deliberations[$i]['Deliberation']['id'], $dateAR);
                        $deliberations[$i]['Deliberation']['DateAR'] = $dateAR;
                    }
                }
            }
        }
        $seances = $this->Seance->find('all', array('conditions' => array('Seance.traitee' => 1),
            'recursive' => -1,
            'fields' => array('Seance.id', 'Seance.date')));
        foreach ($seances as $seance)
            $toutes_seances[$seance['Seance']['id']] = $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
        if (!$this->Filtre->critereExists()) {
            $this->Filtre->addCritere('SeanceId', array('field' => 'Seance.id',
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'toutes',
                    'options' => $toutes_seances)));
        }
        $this->set('deliberations', $deliberations);
    }

    function getAR($tdt_id)
    {
        return $this->S2low->getAR($tdt_id);
    }

    function getTampon($tdt_id)
    {
        return ($this->S2low->getActeTampon($tdt_id));
    }

    function _getDateAR($fluxRetour)
    {
        // +21 Correspond a la longueur du string : actes:DateReception"
        $date = substr($fluxRetour, strpos($fluxRetour, 'actes:DateReception') + 21, 10);
        return ($this->Date->frenchDate(strtotime($date)));
    }

    function toSend($seance_id = null)
    {
        $this->Deliberationseance->Behaviors->attach('Containable');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('host', Configure::read('HOST'));
        $date_classification = $this->S2low->getDateClassification();
        if ($date_classification != false) {
            $this->set('dateClassification', $date_classification);
            $this->set('tabNature', $this->_getNatureListe());
            $this->set('tabMatiere', $this->_getMatiereListe());
        } else
            $this->set('dateClassification', "Récupérer la classification");

        $defaut = array(
            'fields' => array('Deliberationseance.seance_id', 'Deliberationseance.deliberation_id',
                'Deliberationseance.position'),
            'contain' => array('Deliberation.id', 'Deliberation.objet_delib'
            , 'Deliberation.num_delib'
            , 'Deliberation.titre'
            , 'Deliberation.etat'
            , 'Deliberation.tdt_id', 'Deliberation.num_pref',
                'Seance' => array('fields' => array('id', 'date'), 'Typeseance' => array('libelle'))),
            'order' => array('Deliberationseance.position ASC'),
            'conditions' => array());

        $conditions = $this->_handleConditions($this->Filtre->conditions());

        if (empty($seance_id))
            $conditions['Deliberation.etat <'] = 5;

        $conditions['Deliberation.etat >='] = 3;
        //$conditions['Deliberation.signee'] = true;
        $conditions['Deliberation.delib_pdf <>'] = '';
        if (isset($conditions['Seance.id'])) {
            $seance_id = $conditions['Seance.id'];
            unset($conditions['Seance.id']);
        }

        $options = array_merge($defaut, array('conditions' => $conditions));

        if (!empty($seance_id)) {
            $options['conditions']['Deliberationseance.seance_id'] = $seance_id;
        } else {
            $options['order'] = array('Deliberation.num_delib ASC');
        }
        $deliberations = $this->Deliberationseance->find('all', $options);

        for ($i = 0; $i < count($deliberations); $i++) {
            $deliberations[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($deliberations[$i]['Deliberation']['num_pref']);

        }
        if (!$this->Filtre->critereExists()) {
            $this->Filtre->addCritere('SeanceId', array('field' => 'Seance.id',
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'toutes',
                    'options' => $this->Utils->listFromArray($deliberations, '/Seance/id', array('/Seance/date',
                            '/Seance/Typeseance/libelle'), '%s : %s'))));
        }

        if (!empty($seance_id))
            $this->set('seance_id', $seance_id);
        $this->set('deliberations', $deliberations);
    }

    function _getNatureListe()
    {
        $tab = array();
        $doc = new DOMDocument('1.0', 'UTF-8');
        if (!@$doc->load(Configure::read('FILE_CLASS')))
            return false;
        $NaturesActes = $doc->getElementsByTagName('NatureActe');
        foreach ($NaturesActes as $NatureActe)
            $tab[$NatureActe->getAttribute('actes:CodeNatureActe')] = utf8_decode($NatureActe->getAttribute('actes:Libelle'));

        return $tab;
    }

    function classification()
    {
        $this->layout = 'popup';
        $this->set('title_for_layout', 'Classification');
        $this->set('classification', $this->_getMatiereListe());
    }

    function _getMatiereListe()
    {

        $tab = array();
        $xml = @simplexml_load_file(Configure::read('FILE_CLASS'));
        if ($xml === false)
            return false;
        $namespaces = $xml->getDocNamespaces();
        $xml = $xml->children($namespaces["actes"]);


        foreach ($xml->Matieres->children($namespaces["actes"]) as $matiere1) {
            $mat1 = $this->_object2array($matiere1);
            $tab[$mat1['@attributes']['CodeMatiere']] = ($mat1['@attributes']['Libelle']);
            foreach ($matiere1->children($namespaces["actes"]) as $matiere2) {
                $mat2 = $this->_object2array($matiere2);
                $tab[$mat1['@attributes']['CodeMatiere'] . '.' . $mat2['@attributes']['CodeMatiere']] = ($mat2['@attributes']['Libelle']);
                foreach ($matiere2->children($namespaces["actes"]) as $matiere3) {
                    $mat3 = $this->_object2array($matiere3);
                    $tab[$mat1['@attributes']['CodeMatiere'] . '.' . $mat2['@attributes']['CodeMatiere'] . '.' . $mat3['@attributes']['CodeMatiere']] = ($mat3['@attributes']['Libelle']);
                    foreach ($matiere3->children($namespaces["actes"]) as $matiere4) {
                        $mat4 = $this->_object2array($matiere4);
                        $tab[$mat1['@attributes']['CodeMatiere'] . '.' . $mat2['@attributes']['CodeMatiere'] . '.' . $mat3['@attributes']['CodeMatiere'] . '.' . $mat4['@attributes']['CodeMatiere']] = ($mat4['@attributes']['Libelle']);
                        foreach ($matiere4->children($namespaces["actes"]) as $matiere5) {
                            $mat5 = $this->_object2array($matiere5);
                            $tab[$mat1['@attributes']['CodeMatiere'] . '.' . $mat2['@attributes']['CodeMatiere'] . '.' . $mat3['@attributes']['CodeMatiere'] . '.' . $mat4['@attributes']['CodeMatiere'] . '.' . $mat5['@attributes']['CodeMatiere']] = ($mat5['@attributes']['Libelle']);
                        }
                    }
                }
            }
        }
        return $tab;
    }

    /** Retourne la matière par rapport a une clé donnée en parametre
     *
     * @param string $key
     * @return string
     */
    function _getMatiereByKey($key)
    {
        $aMatiere = $this->_getMatiereListe();
        return isset($aMatiere[$key]) ? $aMatiere[$key] : NULL;
    }

    function _object2array($object)
    {
        $return = NULL;
        if (is_array($object)) {
            foreach ($object as $key => $value)
                $return[$key] = $this->_object2array($value);
        } else {
            $var = @get_object_vars($object);
            if ($var) {
                foreach ($var as $key => $value)
                    $return[$key] = $this->_object2array($value);
            } else
                return $object;
        }
        return $return;
    }

    function sendActe()
    {
        $erreur = '';
        if (isset($this->data['Deliberation']['id']) && !empty($this->data['Deliberation']['id'])) {

            if (!is_file(Configure::read('FILE_CLASS')))
                $this->S2low->getClassification();
            $pos = strrpos(getcwd(), 'webroot');
            $path = substr(getcwd(), 0, $pos);

            foreach ($this->data['Deliberation']['id'] as $delib_id => $bool) {
                if ($bool == 1) {
                    $this->Deliberation->id = $delib_id;
                    if (!isset($this->data[$delib_id . "classif2"]))
                        continue;

                    $this->Deliberation->saveField('num_pref', $this->data[$delib_id . "classif2"]);
                }
            }
            $nbEnvoyee = 1;
            $id_e = null;
            if (Configure::read('USE_PASTELL')) {
                $coll = $this->Session->read('user.Collectivite');
                $id_e = $coll['Collectivite']['id_entity'];
            }
            $this->Deliberation->Typeacte->Behaviors->attach('Containable');
            foreach ($this->data['Deliberation']['id'] as $delib_id => $bool) {
                if ($bool == 1) {
                    $this->Deliberation->id = $delib_id;
                    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id)));
                    $typeacte = $this->Deliberation->Typeacte->find('first', array('conditions' => array('Typeacte.id' => $delib['Typeacte']['id']),
                        'contain' => array('Nature.code')));
                    if ($typeacte['Nature']['code'] == "DE")
                        $nature_code = 1;
                    elseif ($typeacte['Nature']['code'] == "AR")
                        $nature_code = 2;
                    elseif ($typeacte['Nature']['code'] == "AI")
                        $nature_code = 3;
                    elseif ($typeacte['Nature']['code'] == "CC")
                        $nature_code = 4;
                    elseif ($typeacte['Nature']['code'] == "AU")
                        $nature_code = 5;

                    if (Configure::read('USE_PASTELL')) {
                        $id_d = $delib['Deliberation']['pastell_id'];
                        $infos = $this->Pastell->getInfosDocument($id_e, $id_d);
                        $infos = (array)$infos;
                        $infos['data'] = (array)$infos['data'];

                        if (isset($infos['action-possible']))
                            if (isset($infos['data']['has_signature']))
                                $result = $this->Pastell->action($id_e, $id_d, 'send-tdt-2');
                            else
                                $result = $this->Pastell->action($id_e, $id_d, 'send-tdt');


                        $result = (array)$result;
                        if (isset($result['status']) && ($result['status'] == 'error'))
                            $erreur .= $result['error-message'];
                        if ($erreur == '')
                            $this->Deliberation->saveField('etat', 5);
                        continue;
                    }

                    if (file_exists(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/D_$delib_id.pdf"))
                        unlink(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/D_$delib_id.pdf");
                    //$this->Deliberation->changeClassification($delib_id, $classification);

                    $classification = $delib['Deliberation']['num_pref'];
                    if (strpos($classification, ' -') != false)
                        $classification = (substr($classification, 0, strpos($classification, ' -')));

                    $class1 = substr($classification, 0, strpos($classification, '.'));
                    $rest = substr($classification, strpos($classification, '.') + 1, strlen($classification));
                    $class2 = substr($rest, 0, strpos($classification, '.'));
                    $rest = substr($rest, strpos($classification, '.') + 1, strlen($rest));
                    $class3 = substr($rest, 0, strpos($classification, '.'));
                    $rest = substr($rest, strpos($classification, '.') + 1, strlen($rest));
                    $class4 = substr($rest, 0, strpos($classification, '.'));
                    $rest = substr($rest, strpos($classification, '.') + 1, strlen($rest));
                    $class5 = substr($rest, 0, strpos($classification, '.'));

                    //Création du fichier de délibération au format pdf (on ne passe plus par la génération)
                    $file = $this->Gedooo->createFile(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/", "D_$delib_id.pdf", $delib['Deliberation']['delib_pdf']);
                    $sigFileName = '';
                    if (isset($delib['Deliberation']['signature'])) {
                        $signature = $this->Gedooo->createFile(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/", "signature_$delib_id.zip", $delib['Deliberation']['signature']);
                        $zip = zip_open($signature);
                        if (is_resource($zip)) {
                            while ($zip_entry = zip_read($zip)) {
                                $fichier = basename(zip_entry_name($zip_entry));
                                $tmp = substr($fichier, 0, 3);
                                if ($tmp == '1- ') {

                                    $sigFileName = WEBROOT_PATH . "/files/generee/fd/null/$delib_id/signature.pkcs7";
                                    $fp = fopen($sigFileName, "w+");

                                    if (zip_entry_open($zip, $zip_entry, "r")) {
                                        $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                                        zip_entry_close($zip_entry);
                                    }

                                    fwrite($fp, $buf);
                                    fclose($fp);
                                    zip_close($zip);
                                    break;
                                }
                            }
                            @zip_close($zip);
                        }
                    }
                    if (!file_exists($file))
                        die("Problème lors de la récupération du fichier");
                    // Checker le code classification
                    if (isset($delib['Deliberation']['date_acte']))
                        $decision_date = date("Y-m-d", strtotime($delib['Deliberation']['date_acte']));
                    else {
                        $seances = array();
                        foreach ($delib['Seance'] as $seance)
                            $seances[] = $seance['id'];
                        $seance_id = $this->Seance->getSeanceDeliberante($seances);
                        $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
                            'fields' => array('Seance.date'),
                            'recursive' => -1));
                        $decision_date = date("Y-m-d", strtotime($seance['Seance']['date']));
                    }

                    if ($class1 == false)
                        $class1 = null;
                    if ($class2 == false)
                        $class2 = null;
                    if ($class3 == false)
                        $class3 = null;
                    if ($class4 == false)
                        $class4 = null;
                    if ($class5 == false)
                        $class5 = null;

                    $acte = array(
                        'api' => '1',
                        'nature_code' => utf8_decode($nature_code),
                        'classif1' => utf8_decode($class1),
                        'classif2' => utf8_decode($class2),
                        'classif3' => utf8_decode($class3),
                        'classif4' => utf8_decode($class4),
                        'classif5' => utf8_decode($class5),
                        'number' => utf8_decode($delib['Deliberation']['num_delib']),
                        'decision_date' => $decision_date,
                        'subject' => utf8_decode($delib['Deliberation']['objet_delib']),
                        'acte_pdf_file' => "@$file",
                    );

                    if ($sigFileName != '') {
                        $acte['acte_pdf_file_sign'] = "@$sigFileName";
                    }

                    $annexes_id = $this->Annex->getAnnexesFromDelibId($delib_id, 1);
                    $nb_pj = 0;
                    if (isset($annexes_id) && !empty($annexes_id)) {
                        foreach ($annexes_id as $annex_id) {
                            $annexe = $this->Annex->getContentToTdT($annex_id['Annex']['id']);
                            $pj_file = $this->Gedooo->createFile($path . "webroot/files/generee/fd/null/$delib_id/annexes/", $annex_id['Annex']['id'] . '.' . $annexe['type'], $annexe['data']);
                            $acte["acte_attachments[$nb_pj]"] = "@$pj_file";
                            $acte["acte_attachments_sign[$nb_pj]"] = "";
                            $nb_pj++;
                        }
                    }

                    $curl_return = utf8_encode($this->S2low->send($acte));
                    $pos = strpos($curl_return, 'OK');
                    $tdt_id = substr($curl_return, 3, strlen($curl_return));
                    if ($pos === false) {
                        $order = array("\r\n", "\n", "\r");
                        $replace = '<br />';
                        $curl_return = str_replace($order, $replace, $curl_return);
                        $erreur .= $delib['Deliberation']['objet'] . '(' . $delib['Deliberation']['num_delib'] . ') : ' . $curl_return . '<br />';
                    } else {
                        $nbEnvoyee++;
                        $this->Deliberation->saveField('etat', 5);
                        $this->Deliberation->saveField('tdt_id', $tdt_id);
                        unlink($file);
                    }
                    sleep(5);
                }
            }
        } else $erreur = 'Aucun Acte(s) selectionné(s)';

        if (empty($erreur))
            $this->Session->setFlash('Acte(s) envoyé(s) correctement au TdT', 'growl');
        else
            $this->Session->setFlash('Erreur : ' . $erreur, 'growl', array('type' => 'erreurTDT'));

        if (isset($this->data['Seance']['id']) && !empty($this->data['Seance']['id']))
            $this->redirect('/deliberations/toSend/' . $this->data['Seance']['id']);
        else
            $this->redirect('/deliberations/toSend/');
    }

    function getClassification()
    {
        if ($this->S2low->getClassification())
            $this->redirect('/deliberations/toSend');
        else {
            $this->Session->setFlash('Erreur lors de la récupération de la classification ', 'growl', array('type' => 'erreur'));
            $this->redirect(array('controllers' => 'deliberations', 'action' => 'toSend'));
        }
    }

    function positionner($seance_id, $id = null, $delta)
    {
        $projet_courant = $this->Deliberationseance->find('first', array(
            'conditions' => array('Deliberation.id' => $id, 'Seance.id' => $seance_id, 'Deliberation.etat <>' => '-1'),
            'fields' => array('id', 'deliberation_id', 'position')));

        $projet_interve = $this->Deliberationseance->find('first', array(
            'conditions' => array(
                'Deliberationseance.position' => $projet_courant['Deliberationseance']['position'] + $delta,
                'Deliberationseance.seance_id' => $seance_id),
            'recursive' => '-1',
            'fields' => array('id', 'position')));

        if (!empty($projet_interve)) {
            $projet_courant['Deliberationseance']['position'] += $delta;
            $projet_interve['Deliberationseance']['position'] -= $delta;
            $this->Deliberationseance->id = $projet_courant['Deliberationseance']['id'];
            $this->Deliberationseance->save($projet_courant);
            $this->Deliberationseance->id = $projet_interve['Deliberationseance']['id'];
            $this->Deliberationseance->save($projet_interve);
        }
        $this->redirect("/seances/afficherProjets/$seance_id");
    }

    function sortby($seance_id, $sortby)
    {
        $tab_projets = array();
        $this->Deliberation->Behaviors->attach('Containable');
        $projets = $this->Seance->getDeliberations($seance_id);
        foreach ($projets as $projet)
            $tab_projets[] = $projet['Deliberation']['id'];

        $condition = array("Deliberation.id" => $tab_projets, "Deliberation.etat <>" => "-1");
        // Critere de tri
        if ($sortby == 'theme_id')
            $sortby = 'Theme.order';
        elseif ($sortby == 'service_id')
            $sortby = 'Service.order';
        elseif ($sortby == 'rapporteur_id')
            $sortby = 'Rapporteur.nom';
        elseif ($sortby == 'titre')
            $sortby = 'Deliberation.titre';

        $deliberations = $this->Deliberation->find('all', array(
            'conditions' => $condition,
            'fields' => array('Deliberation.id'),
            'contain' => array('Theme.order', 'Service.order', 'Rapporteur.nom'),
            'order' => array("$sortby  ASC")));

        for ($i = 0; $i < count($deliberations); $i++) {
            $ds = $this->Deliberationseance->find('first', array(
                'conditions' => array('Deliberationseance.seance_id' => $seance_id,
                    'Deliberationseance.deliberation_id' => $deliberations[$i]['Deliberation']['id']),
                'fields' => array('Deliberationseance.id'),
                'recursive' => -1));


            $this->Deliberationseance->id = $ds['Deliberationseance']['id'];
            $this->Deliberationseance->saveField('position', $i + 1);
        }
        $this->redirect("/seances/afficherProjets/$seance_id");
    }

    function textprojetvue($id = null)
    {
        $this->set('deliberation', $this->Deliberation->read(null, $id));
        $this->set('delib_id', $id);
    }

    function textsynthesevue($id = null)
    {
        $this->set('deliberation', $this->Deliberation->read(null, $id));
        $this->set('delib_id', $id);
    }

    function deliberationvue($id = null)
    {
        $this->set('deliberation', $this->Deliberation->read(null, $id));
        $this->set('delib_id', $id);
    }

    function _notifier($delib_id, $user_id, $type)
    {
        if ($this->User->exists($user_id)) {
            $user = $this->User->read(null, $user_id);

            // Si l'utilisateur accepte les mails
            if ($user['User']['accept_notif']) {
                if (Configure::read("SMTP_USE")) {
                    $this->Email->smtpOptions = array('port' => Configure::read("SMTP_PORT"),
                        'timeout' => Configure::read("SMTP_TIMEOUT"),
                        'host' => Configure::read("SMTP_HOST"),
                        'username' => Configure::read("SMTP_USERNAME"),
                        'password' => Configure::read("SMTP_PASSWORD"),
                        'client' => Configure::read("SMTP_CLIENT"));
                    $this->Email->delivery = 'smtp';
                } else
                    $this->Email->delivery = 'mail';

                $this->Email->from = Configure::read("MAIL_FROM");
                $this->Email->to = $user['User']['email'];
                $this->Email->sendAs = 'text';
                $this->Email->charset = 'UTF-8';

                $delib = $this->Deliberation->find('first', array(
                    'conditions' => array('Deliberation.id' => $delib_id),
                    'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.titre', 'Deliberation.circuit_id')
                ));

                $this->Email->layout = 'default';
                $this->Email->attachments = null;
                if ($type == 'insertion') {
                    if ($user['User']['mail_insertion']) {
                        $this->Email->subject = "Vous allez recevoir le projet : $delib_id";
                        $this->Email->send($this->_paramMails($type, $delib, $user['User']));
                    }
                }
                if ($type == 'traiter') {
                    if ($user['User']['mail_traitement']) {
                        $this->Email->subject = "Vous avez le projet (id : $delib_id) à traiter";
                        $this->Email->send($this->_paramMails($type, $delib, $user['User']));
                    }
                }
                if ($type == 'refus') {
                    if ($user['User']['mail_refus']) {
                        $this->Email->subject = "Le projet << " . $delib['Deliberation']['objet'] . " >> a été refusé";
                        $this->Email->send($this->_paramMails($type, $delib, $user['User']));
                    }
                }
                if ($type == 'modif_projet_cree') {
                    if ($user['User']['mail_modif_projet_cree']) {
                        $this->Email->subject = "Votre projet (id : $delib_id) a été modifié";
                        $this->Email->send($this->_paramMails($type, $delib, $user['User']));
                    }
                }
                if ($type == 'modif_projet_valide') {
                    if ($user['User']['mail_modif_projet_valide']) {
                        $this->Email->subject = "Un projet que j'ai visé (id : $delib_id) a été modifié";
                        $this->Email->send($this->_paramMails($type, $delib, $user['User']));
                    }
                }
            }
        }
    }

    function _getListPresent($delib_id)
    {
        $this->Listepresence->Behaviors->attach('Containable');

        $acteurs = $this->Listepresence->find('all', array(
            'conditions' => array("Listepresence.delib_id" => $delib_id),
            'contain' => array('Acteur')));

        foreach ($acteurs as &$acteur) {

            //if($acteur['Acteur']['actif'] == false)
            //  continue;

            if (isset($acteur['Acteur']['suppleant_id']) && !empty($acteur['Acteur']['suppleant_id'])) {
                $suppleant = $this->Acteur->find('first', array(
                    'conditions' => array('Acteur.id' => $acteur['Acteur']['suppleant_id'],
                        'Acteur.actif' => true),
                    'order' => 'position ASC',
                    'recursive' => -1,
                    'fields' => array('id', 'nom', 'prenom')));
                $acteur['Suppleant'] = $suppleant['Acteur'];
            }

            if (isset($acteur['Listepresence']['mandataire']) && !empty($acteur['Listepresence']['mandataire'])) {
                $mandataire = $this->Acteur->find('first', array(
                    'conditions' => array('Acteur.id' => $acteur['Listepresence']['mandataire'],
                        'Acteur.actif' => true),
                    'order' => 'position ASC',
                    'recursive' => -1,
                    'fields' => array('id', 'nom', 'prenom')));
                $acteur['Mandataire'] = $mandataire['Acteur'];
            }
            /*
            $is_suppleant = $this->Acteur->find('first', array(
                'conditions' => array(  'Acteur.suppleant_id' => $acteur['Acteur']['id'],
                                        'Acteur.actif' => true),
                'recursive' => -1,
                'fields' => array('id', 'nom', 'prenom')));
            
           if (!empty($is_suppleant)) {
                $acteur['Acteur']['is_suppleant'] = $is_suppleant;
                
                //if(!empty($acteur['Acteur']['suppleant_id'])){
                   // $acteur['Titulaire']['id'] = $is_suppleant['Acteur']['id'];
                    //$acteur['Titulaire']['prenom'] = $is_suppleant['Acteur']['prenom'];
                    //$acteur['Titulaire']['nom'] = $is_suppleant['Acteur']['nom'];
               // }
            }*/
        }


        return $acteurs;
    }

    function listerPresents($delib_id, $seance_id)
    {
        if (empty($this->data)) {

            $presents = $this->_getListPresent($delib_id);

            //Pour sélectionner les acteurs
            foreach ($presents as $present) {
                $this->request->data[$present['Listepresence']['acteur_id']]['present'] = $present['Listepresence']['present'];
                $this->request->data[$present['Listepresence']['acteur_id']]['mandataire'] = $present['Listepresence']['mandataire'];
            }

            $this->set('presents', $presents);
            $this->set('mandataires', $this->Acteur->generateListElus());
            $this->set('delib_id', $delib_id);
            $this->set('seance_id', $seance_id);
        } else {
            $nbConvoques = 0;
            $nbVoix = 0;
            $nbPresents = 0;
            $this->Deliberation->_effacerListePresence($delib_id);

            foreach ($this->data['Acteur'] as $acteur_id => $tab) {
                if ($acteur_id == 0)
                    continue;

                if (isset($this->data['Acteur'][$acteur_id]['suppleant_id']) && $acteur_id != $this->data['Acteur'][$acteur_id]['suppleant_id']) {
                    $this->request->data['Listepresence']['suppleant_id'] = $tab['suppleant_id'];

                } else
                    $this->request->data['Listepresence']['suppleant_id'] = NULL;

                $this->Listepresence->create();

                $nbConvoques++;
                $this->request->data['Listepresence']['acteur_id'] = $acteur_id;

                //Pour savoir si l'acteur principal est present
                if (isset($tab['present'])) {
                    $this->request->data['Listepresence']['present'] = $tab['present'];
                    if ($tab['present'] == 1) {
                        $nbPresents++;
                        $nbVoix++;
                    }
                }

                if (isset($tab['mandataire']) && !empty($tab['mandataire'])) {
                    $this->request->data['Listepresence']['mandataire'] = $tab['mandataire'];
                    $nbVoix++;
                } else
                    $this->request->data['Listepresence']['mandataire'] = NULL;

                $this->request->data['Listepresence']['delib_id'] = $delib_id;
                $this->Listepresence->save($this->data['Listepresence']);

            }

            //if ($nbVoix < ($nbConvoques/2)) {
            //   $this->_reporteDelibs($delib_id);
            // }
            $this->redirect("/seances/voter/" . $delib_id . "/" . $seance_id);
        }
    }

    function _reporteDelibs($delib_id)
    {
        $seance_id = $this->Deliberation->getCurrentSeance($delib_id);
        $position = $this->Deliberation->getCurrentPosition($delib_id);
        $conditions = "Deliberation.seance_id=$seance_id AND Deliberation.position>=$position";
        $delibs = $this->Deliberation->findAll($conditions);
        foreach ($delibs as $delib)
            $this->Deliberation->changeSeance($delib['Deliberation']['id'], 0);
        $this->Session->setFlash("Le quorum n\'est plus atteint...", 'growl', array('type' => 'erreur'));
        $this->redirect('seances/listerFuturesSeances');
        exit;
    }

    function _paramMails($type, $delib, $acteur)
    {
        $handle = fopen(CONFIG_PATH . '/emails/' . $type . '.txt', 'r');
        $content = fread($handle, filesize(CONFIG_PATH . '/emails/' . $type . '.txt'));

        $addrTraiter = FULL_BASE_URL . "/deliberations/traiter/" . $delib['Deliberation']['id'];
        $addrView = FULL_BASE_URL . "/deliberations/view/" . $delib['Deliberation']['id'];
        $addrEdit = FULL_BASE_URL . "/deliberations/edit/" . $delib['Deliberation']['id'];
        $searchReplace = array(
            "#NOM#" => $acteur['nom'],
            "#PRENOM#" => $acteur['prenom'],
            "#IDENTIFIANT_PROJET#" => $delib['Deliberation']['id'],
            "#OBJET_PROJET#" => $delib['Deliberation']['objet'],
            "#TITRE_PROJET#" => $delib['Deliberation']['titre'],
            "#LIBELLE_CIRCUIT#" => $this->Circuit->getLibelle($delib['Deliberation']['circuit_id']),
            "#ADRESSE_A_TRAITER#" => $addrTraiter,
            "#ADRESSE_A_VISUALISER#" => $addrView,
            "#ADRESSE_A_MODIFIER#" => $addrEdit,
        );

        return (str_replace(array_keys($searchReplace), array_values($searchReplace), $content));
    }

    /*
     * Affiche la liste des projets en cours de redaction (etat = 0) dont l'utilisateur connecté
     * est le rédacteur.
     */

    function mesProjetsRedaction()
    {
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        // Gestion par lot
        $this->set('traitement_lot', true);
        $this->set('actions_possibles', array('suppression' => 'Suppression'));
        $this->set('modeles', $this->Modeltemplate->find('list', array(
            'recursive' => -1,
            'fields' => array('Modeltemplate.name'),
            'conditions' => array('Modeltemplate.modeltype_id' => array(MODEL_TYPE_TOUTES,MODEL_TYPE_RECHERCHE))
        )));

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');

        $userId = $this->Session->read('user.User.id');
        $listeLiens = $this->Droits->check($userId, "Deliberations:add") ? array('add') : array();

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        //$conditions =  $this->Filtre->conditions();
        if (!isset($conditions['Deliberation.typeacte_id']))
            $conditions['Deliberation.typeacte_id'] = array_keys($this->Session->read('user.Nature'));
        $conditions['Deliberation.etat'] = 0;
        $conditions['Deliberation.redacteur_id'] = $userId;
        $conditions['Deliberation.parent_id'] = null;

        $ordre = array('Deliberation.id' => 'DESC');
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));
        $projets = $this->Deliberation->find('all', array('conditions' => $conditions,
            'limit' => $limit,
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'order' => $ordre,
            'contain' => array('Service.libelle', 'Theme.libelle', 'Typeacte.libelle', 'Deliberationseance.seance_id', 'Seance.date', 'Seance.id', 'Seance.type_id', 'Circuit.nom', 'Deliberationtypeseance.typeseance_id', 'Typeseance.libelle', 'Typeseance.id')));
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets(
            $projets, 'Mes projets en cours de rédaction', array('view', 'edit', 'delete', 'attribuerCircuit', 'generer'), $listeLiens, $nbProjets);
    }

    /*
     * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
     * de validation de l'utilisateur connecté et dont le tour de validation est venu.
     */

    function mesProjetsATraiter()
    {
        $this->Deliberation->Behaviors->attach('Containable');
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = intval(Configure::read('LIMIT'));
        else
            $limit = null;

        $this->set('traitement_lot', true);
        $this->set('actions_possibles', array('valider' => 'Valider', 'refuser' => 'Refuser'));
        $this->set('modeles', $this->Modeltemplate->find('list', array(
            'recursive' => -1,
            'fields' => array('Modeltemplate.name'),
            'conditions' => array('modeltype_id' => array(MODEL_TYPE_TOUTES,MODEL_TYPE_RECHERCHE))
        )));

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $conditions['Deliberation.etat'] = 1;
        $delibs_ids = $this->Traitement->listeTargetId($this->Session->read('user.User.id'), array('etat' => 'NONTRAITE',
            'traitement' => 'AFAIRE'));
        if (isset($conditions['Deliberation.id'])) {
            $conditions['Deliberation.id'] = array_intersect($conditions['Deliberation.id'], $delibs_ids);
        } else {
            $conditions['Deliberation.id'] = $delibs_ids;
        }
        $conditions['Deliberation.parent_id'] = NULL;
        $projets = $this->Deliberation->find('all', array(
            'conditions' => $conditions,
            'order' => array('Deliberation.id' => 'DESC'),
            'limit' => $limit,
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array(
                'Seance.id', 'Seance.traitee', 'Seance.date', 'Seance.type_id', 'Circuit.nom',
                'Service.libelle',
                'Theme.libelle',
                'Typeacte.libelle',
                'Typeseance.id', 'Typeseance.libelle')));
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($projets, 'Mes projets &agrave; traiter', array('view', 'traiter', 'generer'), array(), $nbProjets);
    }

    /*
     * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
     * de validation de l'utilisateur connecté et dont ce n'est pas le tour de valider et les projets
     * dont il est le rédacteur
     */

    function mesProjetsValidation()
    {
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        $userId = $this->Session->read('user.User.id');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $conditions['Deliberation.etat'] = 1;

        $delibs_ids = $this->Traitement->listeTargetId($this->Session->read('user.User.id'), array('etat' => 'NONTRAITE', 'traitement' => 'NONAFAIRE'));
        if (isset($conditions['Deliberation.id'])) {
            $conditions['OR']['Deliberation.id'] = array_intersect($conditions['Deliberation.id'], $delibs_ids);
        } else {
            $conditions['OR']['Deliberation.id'] = $delibs_ids;
        }
        $conditions['OR']['Deliberation.redacteur_id'] = $userId;
        $conditions['Deliberation.parent_id'] = NULL;

        $ordre = array('Deliberation.id' => 'DESC');
        $projets = $this->Deliberation->find('all', array(
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'conditions' => $conditions,
            'order' => $ordre,
            'limit' => $limit,
            'contain' => array('Seance.id', 'Seance.traitee', 'Seance.date', 'Seance.type_id', 'Circuit.nom',
                'Service.libelle',
                'Theme.libelle',
                'Typeacte.libelle',
                'Typeseance.id', 'Typeseance.libelle')));
        $this->_ajouterFiltre($projets);
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));

        $this->_afficheProjets(
            $projets, 'Mes projets en cours d\'élaboration et de validation', array('view', 'generer'), array(), $nbProjets);
    }

    /*
     * Affiche les projets validés (etat = 2) dont l'utilisateur connecté est le rédacteur
     * ou qu'il est dans les circuits de validation des projets
     */

    function mesProjetsValides()
    {
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        //    $conditions =  $this->Filtre->conditions();

        $userId = $this->Session->read('user.User.id');

        $conditions['Deliberation.etat'] = 2;
        $conditions['OR']['Deliberation.id'] = $this->Traitement->listeTargetId(
            $this->Session->read('user.User.id'),
            array(
                'etat' => 'TRAITE',
                'targetConditions' => array('Deliberation.etat' => 2)
            ));
        $conditions['OR']['Deliberation.redacteur_id'] = $userId;

        $conditions['Deliberation.parent_id'] = NULL;

        $ordre = 'Deliberation.id DESC';

        $projets = $this->Deliberation->find('all', array(
            'conditions' => $conditions,
            'order' => $ordre,
            'limit' => $limit,
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array(
                'Seance.id', 'Seance.traitee', 'Seance.date', 'Seance.type_id',
                'Service.libelle',
                'Theme.libelle', 'Circuit.nom',
                'Typeacte.libelle',
                'Typeseance.id', 'Typeseance.libelle')));

        $this->_ajouterFiltre($projets);
        $this->_afficheProjets(
            $projets, 'Mes projets validés', array('view', 'generer'));
    }

    /**
     * fonction générique pour afficher les projets sour forme d'index
     */
    function _afficheProjets(&$projets, $titreVue, $listeActions, $listeLiens = array(), $nbProjets = null)
    {
        // initialisation de l'utilisateur connecté et des droits
        $this->set('typeseances', $this->Seance->Typeseance->find('list', array('recursive' => -1)));

        $userId = $this->Session->read('user.User.id');
        $editerTous = $this->Droits->check($userId, "Deliberations:editerTous");
        $this->request->data = $projets;

        /* initialisation pour chaque projet ou délibération */
        foreach ($this->request->data as $i => $projet) {
            // initialisation des icônes
            if (isset($projet[0]))
                $projet['Deliberation'] = $projet[0];
            $this->request->data[$i]['last_viseur'] = $this->Traitement->dernierVisaTrigger($projet['Deliberation']['id']);
            $this->request->data[$i]['Deliberation']['num_pref'] = $this->request->data[$i]['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->request->data[$i]['Deliberation']['num_pref']);

            if ($projet['Deliberation']['etat'] == 0 && $projet['Deliberation']['anterieure_id'] != 0)
                $this->request->data[$i]['iconeEtat'] = $this->_iconeEtat(-2);
            elseif ($projet['Deliberation']['etat'] == 1) {
                $estDansCircuit = $this->Traitement->triggerDansTraitementCible($userId, $projet['Deliberation']['id']);
                $tourDansCircuit = $estDansCircuit ? $this->Traitement->positionTrigger($userId, $projet['Deliberation']['id']) : 0;
                $estRedacteur = ($userId == $projet['Deliberation']['redacteur_id']);
                $this->request->data[$i]['iconeEtat'] = $this->_iconeEtat(1, false, $estDansCircuit, $estRedacteur, $tourDansCircuit);
            } else {
                $this->request->data[$i]['iconeEtat'] = $this->_iconeEtat($projet['Deliberation']['etat'], $editerTous);
            }
            // initialisation des actions
            $this->request->data[$i]['Actions'] = $listeActions;
            if ($projet['Deliberation']['etat'] != 1) {
                $this->request->data[$i]['Actions'] = array_flip($this->data[$i]['Actions']);
                unset($projet['Actions']['goNext']);
                unset($projet['Actions']['validerEnUrgence']);
                $this->request->data[$i]['Actions'] = array_flip($this->data[$i]['Actions']);
            }
            if ($projet['Deliberation']['etat'] == 2 && $editerTous) {
                $this->request->data[$i]['Actions'][] = 'edit';
                $this->request->data[$i]['Actions'][] = 'attribuerCircuit';
            }
            // initialisation des dates, modèle et service
            $seances_id = array();

            if (isset($projet['Seance']) && !empty($projet['Seance']))
                foreach ($projet['Seance'] as &$seance) {
                    $seances_id[] = $seance['id'];
                }
            $typeseance_id = $this->Seance->getSeanceDeliberante($seances_id);
            if ($typeseance_id != null) {
                $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $typeseance_id),
                    'recursive' => -1,
                    'fields' => array('type_id')));
                $this->request->data[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['Seance']['type_id'], $projet['Deliberation']['etat']);
            } else {
                $model_id = $this->Deliberation->Typeacte->getModelId($projet['Deliberation']['typeacte_id'], 'modeleprojet_id');
                $this->request->data[$i]['Modeltemplate']['id'] = $model_id;
            }
            if (isset($this->data[$i]['Service']['id']))
                $this->request->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($projet['Service']['id']);
            if (isset($this->data[$i]['Deliberation']['date_limite']))
                $this->request->data[$i]['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($projet['Deliberation']['date_limite']));
        }

        // passage des variables à la vue
        $this->set('titreVue', $titreVue);
        $this->set('listeLiens', $listeLiens);
        if ($nbProjets == null)
            $nbProjets = count($projets);
        $this->set('nbProjets', $nbProjets);

        // on affiche la vue index
        $this->render('index');
    }

    /*
     * Affiche la liste de tous les projets dont le rédacteur fait parti de mon/mes services
     * Permet de valider en urgence un projet
     */

    function projetsMonService()
    {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        if (!isset($conditions['Deliberation.service_id'])) {
            $aService_id = array();
            foreach ($this->Session->read('user.Service') as $service_id => $service) {
                foreach ($this->User->Service->doListId($service_id) as $aService)
                    $aService_id[] = $aService;
            }
            $conditions['Deliberation.service_id'] = $aService_id;
        }

        $conditions['Deliberation.etat !='] = -1;
        $conditions['Deliberation.etat <'] = 3;
        $conditions['Deliberation.parent_id'] = NULL;
        $ordre = 'Deliberation.id DESC';
        $projets = $this->Deliberation->find('all', array('conditions' => $conditions,
            'order' => array($ordre),
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array('Seance.id',
                'Seance.traitee',
                'Seance.date',
                'Circuit.nom',
                'Typeseance.libelle', 'Typeseance.id',
                'Seance.type_id',
                'Service.libelle',
                'Theme.libelle',
                'Typeacte.libelle')));
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:validerEnUrgence"))
            array_push($actions, 'validerEnUrgence');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:goNext"))
            array_push($actions, 'goNext');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:delete"))
            array_push($actions, 'delete');


        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($projets, 'Projets dont le rédacteur fait partie de mon service', $actions);
    }

    /*
     * Affiche la liste de tous les projets en cours de validation
     * Permet de valider en urgence un projet
     */

    function tousLesProjetsValidation()
    {
        $this->set('traitement_lot', true);
        $this->set('actions_possibles', array('validerUrgence' => 'Valider en urgence'));
        $this->set('modeles', $this->Modeltemplate->find('list', array(
            'recursive' => -1,
            'fields' => array('Modeltemplate.name'),
            'conditions' => array('modeltype_id' => array(MODEL_TYPE_TOUTES,MODEL_TYPE_RECHERCHE))
        )));

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');
        $conditions = $this->_handleConditions($this->Filtre->conditions());
        //$conditions =  $this->Filtre->conditions();
        // lecture en base
        $conditions['Deliberation.etat'] = 1;
        $conditions['Deliberation.parent_id'] = null;

        $ordre = 'Deliberation.id DESC';
        $projets = $this->Deliberation->find('all', array('conditions' => $conditions,
            'order' => array($ordre),
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array('Seance.id', 'Seance.traitee', 'Seance.date', 'Seance.type_id', 'Typeseance.libelle', 'Typeseance.id',
                'Service.libelle', 'Theme.libelle', 'Typeacte.libelle', 'Circuit.nom')));
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:validerEnUrgence"))
            array_push($actions, 'validerEnUrgence');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:goNext"))
            array_push($actions, 'goNext');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:delete"))
            array_push($actions, 'delete');
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets(
            $projets, 'Projets en cours d\'élaboration et de validation', $actions);
    }

    /*
     * Affiche la liste de tous les projets en cours de redaction, validation, validés sans séance
     * Permet de modifier un projet validé si l'utilisateur à les droits editerTous
     */

    function tousLesProjetsSansSeance()
    {
        $canEditAll = $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:editerTous");

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');
        $conditions = $this->_handleConditions($this->Filtre->conditions());
        //$conditions =  $this->Filtre->conditions();
        if (!isset($conditions['Deliberation.typeacte_id']))
            $natures_id = array_keys($this->Session->read('user.Nature'));
        else
            $natures_id = array($conditions['Deliberation.typeacte_id']);

        // lecture en base
        $projets = $this->Deliberation->getDeliberationsSansSeance('id', $natures_id);
        $delibs = array();

        foreach ($projets as $tmp_id) {
            $delib_id = $tmp_id['0']['id'];
            $conditions['Deliberation.id'] = $delib_id;

            $acte = $this->Deliberation->find('first', array('conditions' => $conditions,
                'contain' => array('Service.libelle', 'Theme.libelle', 'Circuit.nom',
                    'Typeacte.libelle'),
                'fields' => array('Deliberation.id', 'Deliberation.objet',
                    'Deliberation.etat', 'Deliberation.signee',
                    'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                    'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                    'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id')));
            $acte['Seances'] = $this->Seance->generateList(null, $canEditAll, $acte['Deliberation']['typeacte_id']);
            if (!empty($acte['Seances']))
                $delibs[] = $acte;
        }

        //  $this->set('date_seances',$this->Seance->generateList(null, $canEditAll,  array_keys($this->Session->read('user.Nature'))));
        $this->_ajouterFiltre($delibs, false);
        $this->Filtre->delCritere('DeliberationseanceId');
        $this->Filtre->delCritere('DeliberationtypeseanceId');
        $actions = array('view', 'generer', 'attribuerSeance');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:delete"))
            array_push($actions, 'delete');

        $this->_afficheProjets($delibs, 'Projets non associés &agrave; une séance', $actions);
    }

    /*
     * Affiche la liste de tous les projets validés liés à une séance
     */

    function tousLesProjetsAFaireVoter()
    {
        $projets_id = array();

        $this->Deliberationseance->Behaviors->attach('Containable');
        $this->Deliberation->Behaviors->attach('Containable');
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        //$conditions =  $this->Filtre->conditions();
        if (!isset($conditions['Deliberation.typeacte_id']))
            $conditions['Deliberation.typeacte_id'] = array_keys($this->Session->read('user.Nature'));
        $conditions['Deliberation.etat'] = 2;
//        $conditions['Seance.id'] = $this->Seance->getSeancesDeliberantes();
        $conditions['Deliberation.parent_id'] = null;

        $projets = $this->Deliberationseance->find('all', array('conditions' => $conditions,
            'order' => array('Deliberation.id DESC'),
            'fields' => array('Deliberationseance.deliberation_id',
                'Deliberationseance.seance_id'),
            'contain' => array('Deliberation.id', 'Seance.id')));
        if (!empty($projets))
            foreach ($projets as $projet)
                $projets_id[] = $projet['Deliberationseance']['deliberation_id'];

        $projets = $this->Deliberation->find('all', array('conditions' => array('Deliberation.id' => $projets_id),
            'order' => array('Deliberation.created DESC'),
            'fields' => array('Deliberation.id', 'Deliberation.objet',
                'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite',
                'Deliberation.anterieure_id', 'Deliberation.num_pref',
                'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id',
                'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array('Seance.id', 'Seance.traitee', 'Seance.type_id',
                'Circuit.nom', 'Seance.date', 'Theme.libelle',
                'Typeseance.id', 'Typeseance.libelle',
                'Typeacte.libelle', 'Service.libelle')));

        $this->_ajouterFiltre($projets);
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:delete"))
            array_push($actions, 'delete');

        $this->_afficheProjets(
            $projets, 'Projets validés associés &agrave; une séance', $actions);
    }

    function _ajouterFiltre(&$projets)
    {
        if (!$this->Filtre->critereExists()) {
            $this->Filtre->addCritere('DeliberationseanceId', array('field' => 'Deliberationseance.seance_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'toutes',
                    'options' => $this->Deliberation->getSeancesFromArray($projets))));
            $typeseances = array();
            foreach ($projets as $projet) {
                if (isset($projet['Typeseance']) && (!empty($projet['Typeseance']))) {
                    foreach ($projet['Typeseance'] as $typeseance)
                        $typeseances[$typeseance['id']] = $typeseance['libelle'];
                }
            }
            $this->Filtre->addCritere('DeliberationtypeseanceId', array(
                'field' => 'Deliberationtypeseance.typeseance_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Type de séance', true),
                    'options' => $typeseances)));
            $this->Filtre->addCritere('Typeacte', array(
                'field' => 'Deliberation.typeacte_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Type d\'acte', true),
                    'empty' => 'tous',
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/typeacte_id', array('/Typeacte/libelle'), '%s'))));
            $this->Filtre->addCritere('ThemeId', array(
                'field' => 'Deliberation.theme_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Thème', true),
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/theme_id', array('/Theme/libelle'), '%s'))));
            $this->Filtre->addCritere('ServiceId', array(
                'field' => 'Deliberation.service_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Service émetteur', true),
                    'multiple' => true,
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/service_id', array('/Service/libelle'), '%s'))));
        }
    }

    /*
     * Attribue une séance à un projet
     * Appelée depuis la vue deliberations/tous_les_projets
     */

    function attribuerSeance()
    {
        if (!empty($this->data['Deliberation']['seance_id'])) {
            $nbSeancesDeliberantes = 0;
            $this->Seance->Behaviors->attach('Containable');
            foreach ($this->data['Deliberation']['seance_id'] as $seance_id) {
                $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
                    'fields' => array('Seance.id', 'Seance.type_id'),
                    'contain' => array('Typeseance.action')));

                $this->Deliberationtypeseance->create();
                $this->Deliberationtypeseance->save(array('deliberation_id' => $this->data['Deliberation']['id'],
                    'typeseance_id' => $seance['Seance']['type_id']));

                if ($seance['Typeseance']['action'] == 0)
                    $nbSeancesDeliberantes++;
            }
            if ($nbSeancesDeliberantes > 1) {
                $this->Session->setFlash('Une seule séance délibérante par projet', 'growl', array('type' => 'erreur'));
            } else {
                $this->Seance->reOrdonne($this->data['Deliberation']['id'], $this->data['Deliberation']['seance_id']);
                $this->Session->setFlash('Séance enregistrée', 'growl');
            }
        } else {
            $this->Session->setFlash('Vous devez selectionner une séance', 'growl', array('type' => 'erreur'));
        }
        $this->redirect('/deliberations/tousLesProjetsSansSeance');
    }

    /*
     * Permet de valider un projet en cours de validation en court-circuitant le circuit de validation
     * Appelée depuis la vue deliberations/tous_les_projets
     */

    function validerEnUrgence($delibId, $redirect = true)
    {
        // Lecture de la délibération
        $this->Deliberation->recursive = -1;
        $this->request->data = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delibId),
            'fields' => array('Deliberation.id', 'Deliberation.etat'),
            'recursive' => -1));
        if (empty($this->data))
            $this->Session->setFlash('Invalide id pour le projet de délibération', 'growl', array('type' => 'erreur'));
        else {
            if ($this->data['Deliberation']['etat'] != 1)
                $this->Session->setFlash('Le projet de délibération doit &ecirc;tre en cours d\'élaboration', 'growl', array('type' => 'erreur'));
            else {
                // initialisation du visa si utilisateur connecté est hors traitement
                $options = array(
                    'insertion' => array(
                        '0' => array(
                            'Etape' => array(
                                'etape_id' => null,
                                'etape_nom' => 'Validation en urgence',
                                'etape_type' => 1
                            ),
                            'Visa' => array(
                                '0' => array(
                                    'trigger_id' => $this->Session->read('user.User.id'),
                                    'type_validation' => 'V'
                                )))));
                $this->Traitement->execute('ST', $this->Session->read('user.User.id'), $delibId, $options);
                $this->Deliberation->id = $delibId;
                $this->Deliberation->saveField('etat', 2);
                $this->Deliberation->saveField('etat_parapheur', 0);
                $this->Historique->enregistre($delibId, $this->Session->read('user.User.id'), 'Projet validé en urgence');
            }
        }
        $this->Session->setFlash('Le projet ' . $this->data['Deliberation']['id'] . ' a été validé en urgence', 'growl');
        if ($redirect)
            $this->redirect($this->Session->read('user.User.oldurl'));
    }

    function mesProjetsRecherche()
    {
        if (empty($this->data)) {
            $this->set('action', array('controller'=>'deliberations', 'action'=>'mesProjetsRecherche'));
            $this->set('titreVue', 'Recherche multi-critères parmi mes projets');

            $this->set('rapporteurs', $this->Acteur->generateListElus());
            //  $this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
            $this->set('date_seances', $this->Seance->generateAllList());
            $this->set('services', $this->Deliberation->Service->generateTreeList(null, null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
            $this->set('themes', $this->Deliberation->Theme->find('list', array('order' => array('libelle asc'),
                'recursive' => -1,
                'fields' => array('Theme.id', 'Theme.libelle'))));
            $this->set('circuits', $this->Circuit->find('list', array('order' => array('Circuit.nom asc'),
                'fields' => array('Circuit.id', 'Circuit.nom'),
                'recursive' => -1)));
            $this->set('etats', $this->Deliberation->generateListEtat());
            $this->set('infosupdefs', $this->Infosupdef->find('all', array('conditions' => array('recherche' => 1, 'actif' => true),
                'fields' => array('id', 'code', 'nom', 'commentaire', 'type'),
                'order' => array('ordre'),
                'recursive' => -1)));
            $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Deliberation'));
            $this->set('listeBoolean', $this->Infosupdef->listSelectBoolean);
            $this->set('models', $this->Modeltemplate->find('list', array(
                'recursive' => -1,
                'conditions' => array('modeltype_id' => array(MODEL_TYPE_TOUTES,MODEL_TYPE_RECHERCHE)),
                'fields' => array('Modeltemplate.id', 'Modeltemplate.name'))));

            $this->render('rechercheMultiCriteres');
        } else {
            $conditions = array();
            $multiseances = array();

            if (!empty($this->data['Deliberation']['id'])) {
                if (!is_numeric($this->data['Deliberation']['id'])) {
                    $this->Session->setFlash('Vous devez saisir un identifiant valide', 'growl', array('type' => 'erreur'));
                    $this->redirect('/deliberations/mesProjetsRecherche');
                }
                $conditions["Deliberation.id"] = $this->data['Deliberation']['id'];
            }
            if (!empty($this->data['Deliberation']['rapporteur_id']))
                $conditions["Deliberation.rapporteur_id"] = $this->data['Deliberation']['rapporteur_id'];
            if (!empty($this->data['Deliberation']['service_id']))
                $conditions["Deliberation.service_id"] = $this->data['Deliberation']['service_id'];
            if (!empty($this->data['Deliberation']['typeacte_id']))
                $conditions['Deliberation.typeacte_id'] = $this->data['Deliberation']['typeacte_id'];
            if (!empty($this->data['Deliberation']['theme_id']))
                $conditions["Deliberation.theme_id"] = $this->data['Deliberation']['theme_id'];
            if (!empty($this->data['Deliberation']['circuit_id']))
                $conditions["Deliberation.circuit_id"] = $this->data['Deliberation']['circuit_id'];
            if ($this->data['Deliberation']['etat'] != '')
                $conditions["Deliberation.etat"] = $this->data['Deliberation']['etat'];

            if (!empty($this->data['Deliberation']['texte'])) {
                $texte = $this->data['Deliberation']['texte'];
                $conditions['AND']["OR"]["Deliberation.objet ILIKE"] = $texte;
                $conditions['AND']["OR"]["Deliberation.titre ILIKE"] = $texte;
            }
            if (empty($conditions["Deliberation.id"]) || (!isset($conditions["Deliberation.id"]))) {
                if ((isset($this->data['Deliberation']['seance_id'])) && (!empty($this->data['Deliberation']['seance_id']))) {
                    $projet_ids = array();
                    foreach ($this->data['Deliberation']['seance_id'] as $seance_id) {
                        $multiseances[] = $seance_id;
                        $projet_ids = $this->Seance->getDeliberationsId($seance_id);
                    }
                    $conditions['Deliberation.id'] = $projet_ids;
                }
            }
            if (array_key_exists('Infosup', $this->data)) {
                $rechercheInfoSup = $this->Deliberation->Infosup->selectInfosup($this->data['Infosup']);
                if (!empty($rechercheInfoSup))
                    $conditions["Deliberation.id"] = $rechercheInfoSup;
            }
            if (empty($conditions)) {
                $this->Session->setFlash('Vous devez saisir au moins un critère.', 'growl', array('type' => 'erreur'));
                $this->redirect('/deliberations/mesProjetsRecherche');
            } else {
                $userId = $this->Session->read('user.User.id');
                $listeCircuits = explode(',', $this->Circuit->listeCircuitsParUtilisateur($userId));
                if (!empty($listeCircuits))
                    $conditions['OR']['Deliberation.circuit_id'] = $listeCircuits;
                $conditions['OR']['Deliberation.redacteur_id'] = $userId;
                //Récupère la liste des délib que l'utilisateur a visé (résolution bug changement circuit non visible)
                $listeDelibsParticipe = explode(',', $this->Traitement->getListTargetByTrigger($userId));
                if (!empty($listeDelibsParticipe))
                    $conditions['OR']['Deliberation.id'] = $listeDelibsParticipe;
                $ordre = 'Deliberation.id DESC';

                //TODO on peut voir certain projet mecanique à revoir
                $projets = $this->Deliberation->find('all', array('conditions' => $conditions,
                    'order' => array($ordre)));

                if ($this->data['Deliberation']['generer'] == 0) {
                    $this->_afficheProjets($projets, 'Résultat de la recherche parmi mes projets', array('view', 'generer'), array('mesProjetsRecherche'));
                } else {
                    if (count($projets) > 0) {
                        $format = $this->Session->read('user.format.sortie');
                        if (empty($format))
                            $format = 0;

                        $this->Deliberation->genererRecherche($projets, $this->data['Deliberation']['model'], $format, $multiseances, $conditions);
                    } else {
                        $this->Session->setFlash('Aucun résultat à la recherche effectuée.', 'growl', array('type' => 'erreur'));
                        $this->redirect('/deliberations/mesProjetsRecherche');
                    }
                }
            }
        }
    }

    function tousLesProjetsRecherche()
    {
        if (empty($this->data)) {
            $this->set('action', array('controller'=>'deliberations', 'action'=>'tousLesProjetsRecherche'));
            $this->set('titreVue', 'Recherche multi-critères parmi tous les projets');

            $this->set('rapporteurs', $this->Acteur->generateListElus());
            //    $this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
            $this->set('date_seances', $this->Seance->generateAllList());
            $this->set('services', $this->Deliberation->Service->generateTreeList(null, null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
            $this->set('themes', $this->Deliberation->Theme->find('list', array('order' => array('libelle asc'),
                'recursive' => -1,
                'fields' => array('Theme.id', 'Theme.libelle'))));
            $this->set('circuits', $this->Circuit->find('list', array('order' => array('Circuit.nom asc'),
                'fields' => array('Circuit.id', 'Circuit.nom'),
                'recursive' => -1)));
            $this->set('etats', $this->Deliberation->generateListEtat());
            $this->set('infosupdefs', $this->Infosupdef->find('all', array('conditions' => array('recherche' => 1, 'actif' => true),
                'fields' => array('id', 'code', 'nom', 'commentaire', 'type'),
                'order' => array('ordre'),
                'recursive' => -1)));
            $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Deliberation'));
            $this->set('models', $this->Modeltemplate->find('list', array(
                'conditions' => array('modeltype_id' => array(MODEL_TYPE_TOUTES,MODEL_TYPE_RECHERCHE)),
                'fields' => array('Modeltemplate.id', 'Modeltemplate.name'))));
            $this->set('listeBoolean', $this->Infosupdef->listSelectBoolean);

            $this->render('rechercheMultiCriteres');
        } else {
            $conditions = array();

            if (!empty($this->data['Deliberation']['id'])) {
                if (!is_numeric($this->data['Deliberation']['id'])) {
                    $this->Session->setFlash('Vous devez saisir un identifiant valide', 'growl', array('type' => 'erreur'));
                    $this->redirect('/deliberations/mesProjetsRecherche');
                }
                $conditions["Deliberation.id"] = $this->data['Deliberation']['id'];
            }

            if (!empty($this->data['Deliberation']['rapporteur_id']))
                $conditions["Deliberation.rapporteur_id"] = $this->data['Deliberation']['rapporteur_id'];
            if (!empty($this->data['Deliberation']['service_id']))
                $conditions["Deliberation.service_id"] = $this->data['Deliberation']['service_id'];
            if (!empty($this->data['Deliberation']['typeacte_id']))
                $conditions['Deliberation.typeacte_id'] = $this->data['Deliberation']['typeacte_id'];
            if (!empty($this->data['Deliberation']['theme_id']))
                $conditions["Deliberation.theme_id"] = $this->data['Deliberation']['theme_id'];
            if (!empty($this->data['Deliberation']['circuit_id']))
                $conditions["Deliberation.circuit_id"] = $this->data['Deliberation']['circuit_id'];
            if ($this->data['Deliberation']['etat'] != '')
                $conditions["Deliberation.etat"] = $this->data['Deliberation']['etat'];
            if (!empty($this->data['Deliberation']['texte'])) {
                $texte = $this->data['Deliberation']['texte'];
                $conditions["OR"]["Deliberation.objet ILIKE"] = $texte;
                $conditions["OR"]["Deliberation.titre ILIKE"] = $texte;
            }
            if (empty($conditions["Deliberation.id"]) || (!isset($conditions["Deliberation.id"]))) {
                if ((isset($this->data['Deliberation']['seance_id'])) && (!empty($this->data['Deliberation']['seance_id']))) {
                    $multiseances = array();
                    foreach ($this->data['Deliberation']['seance_id'] as $seance_id) {
                        //      $multiseances[] = $seance_id;
                        $projet_ids = $this->Seance->getDeliberationsId($seance_id);
                        $multiseances = array_merge($projet_ids, $multiseances);
                    }
                    // $conditions['Deliberation.id'] =  $projet_ids;
                    $conditions['Deliberation.id'] = $multiseances;
                }
            }
            if (array_key_exists('Infosup', $this->data)) {
                $rechercheInfoSup = $this->Deliberation->Infosup->selectInfosup($this->data['Infosup']);
                if (!empty($rechercheInfoSup))
                    $conditions["Deliberation.id"] = $rechercheInfoSup;
            }
            if (empty($conditions)) {
                $this->Session->setFlash('Vous devez saisir au moins un critère.', 'growl', array('type' => 'erreur'));
                $this->redirect('/deliberations/tousLesProjetsRecherche');
            } else {
                // lecture en base
                $this->Deliberation->Behaviors->attach('Containable');
                $projets = $this->Deliberation->find('all', array(
                    'conditions' => $conditions,
                    'order' => 'num_delib',
                    /* 'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                      'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                      'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                      'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'), */
                    'contain' => array('Service.libelle', 'Theme.libelle', 'Typeacte.libelle', 'Circuit.nom',
                        'Deliberationseance.seance_id', 'Seance.date', 'Seance.id', 'Seance.type_id')));
                for ($i = 0; $i < count($projets); $i++) {
                    for ($j = 0; $j < count($projets[$i]['Seance']); $j++) {
                        $typeseance = $this->Seance->Typeseance->find('first', array('conditions' => array('Typeseance.id' => $projets[$i]['Seance'][$j]['type_id']),
                            'recursive' => -1,
                            'fields' => array('libelle')));
                        $projets[$i]['Seance'][$j]['Typeseance']['libelle'] = $typeseance['Typeseance']['libelle'];
                    }
                }
                if ($this->data['Deliberation']['generer'] == 0) {
                    $userId = $this->Session->read('user.User.id');

                    $actions = array('view', 'generer');
                    if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:delete"))
                        array_push($actions, 'delete');

                    if ($this->Droits->check($userId, "Deliberations:editerTous"))
                        $actions[] = 'edit';
                    $this->_afficheProjets($projets, 'Résultat de la recherche parmi tous les projets', $actions, array('tousLesProjetsRecherche'));
                } else {
                    $format = $this->Session->read('user.format.sortie');
                    if (empty($this->data['Deliberation']['model'])) {
                        $this->Session->setFlash("Vous devez choisir un modèle de document", 'growl', array('type' => 'erreur'));
                        $this->redirect('/deliberations/tousLesProjetsRecherche');
                    }
                    if (empty($format))
                        $format = 0;
                    //if (count($multiseances) == 1)
                    $multiseances = array();
                    if (!empty($projets) || !empty($multiseances))
                        $this->Deliberation->genererRecherche($projets, $this->data['Deliberation']['model'], $format, $multiseances, $conditions);
                    else {
                        $this->Session->setFlash('Aucun projet correspondant aux critères de recherche.', 'growl', array('type' => 'erreur'));
                        $this->redirect('/deliberations/tousLesProjetsRecherche');
                    }
                }
            }
        }
    }

    /*
     * retourne un tableau array('image'=>, 'titre'=>) pour l'affichage de l'icône dans les listes en fonction de :
     *  $etat : état du projet ou de la délibération
     *  $editerTous : droit d'éditer les projets validés
     *
     */

    function _iconeEtat($etat, $editerTous = false, $estDansCircuit = false, $estRedacteur = false, $tourDansCircuit = 0)
    {
        switch ($etat) {
            case -2 : // refusé
                return array(
                    'image' => '/img/icons/refuse.png',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;

            case -1 : // refusé
                return array(
                    'image' => '/img/icons/versionne.png',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 0 : // en cours de rédaction
                return array(
                    'image' => '/img/icons/encours.png',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 1: // en cours de validation
                if ($estDansCircuit) {
                    if ($tourDansCircuit == -1)
                        return array(
                            'image' => '/img/icons/fini.png',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : traité');
                    elseif ($tourDansCircuit == 0)
                        return array(
                            'image' => '/img/icons/atraiter.png',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : à traiter');
                    else
                        return array(
                            'image' => '/img/icons/attente.png',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : en attente');
                } else {
                    if ($estRedacteur)
                        return array(
                            'image' => '/img/icons/fini.png',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : projet dont je suis le rédacteur');
                    else
                        return array(
                            'image' => '/img/icons/fini.png',
                            'titre' => $this->Deliberation->libelleEtat($etat));
                }
                break;
            case 2: // validé
                if ($editerTous)
                    return array(
                        'image' => '/img/icons/valide_editable.png',
                        'titre' => $this->Deliberation->libelleEtat($etat));
                else
                    return array(
                        'image' => '/img/icons/fini.png',
                        'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 3: // voté et adopté
                return array(
                    'image' => '/img/icons/fini.png',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 4: // voté et non adopté
                return array(
                    'image' => '/img/icons/fini.png',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 5: // transmis au contrôle de légalité
                return array(
                    'image' => '/img/icons/fini.png',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
        }
    }

    /**
     * TOFIX
     */
    function sendToPastell($seance_id)
    {
        $this->set('seance_id', $seance_id);
        $coll = $this->Session->read('user.Collectivite');
        $id_e = $coll['Collectivite']['id_entity'];

        if (empty($this->data)) {
            $circuits['-1'] = 'Signature manuscrite';
            try {
                $tmp_id_d = $this->Pastell->createDocument($id_e);
                $this->Pastell->insertInParapheur($id_e, $tmp_id_d);

                $circuits[] = $this->Pastell->getInfosField($id_e, $tmp_id_d, 'iparapheur_sous_type');
                $this->Pastell->action($id_e, $tmp_id_d, 'suppression');

                $this->Deliberation->Behaviors->attach('Containable');

                $delibs = $this->Deliberationseance->find('all', array(
                        'fields' => array('Deliberationseance.seance_id', 'Deliberationseance.deliberation_id', 'Deliberationseance.position', 'Deliberation.*'),
                        'contain' => array('Deliberation', 'Seance'),
                        'recursive' => 1,
                        'order' => 'Deliberationseance.position ASC',
                        'conditions' => array(
                            'Deliberationseance.seance_id' => $seance_id,
                            'Deliberation.etat >=' => 0,
                        )
                    )
                );

                for ($i = 0; $i < count($delibs); $i++) {
                    $delibs[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($delibs[$i]['Seance']['type_id'], 3);
                }
                $this->set('deliberations', $delibs);
                $this->set('circuits', $circuits);
            } catch (Exception $exc) {
                $this->Session->setFlash("Erreur Pastell :<br>" . $exc->getMessage(), 'growl', array('type' => 'erreurTDT'));
                $this->redirect($this->referer());
            }
        } else {
            $message = '';

            $circuit_id = $this->data['Pastell']['circuit_id'];
            foreach ($this->data['Deliberation'] as $id => $bool) {
                if ($bool == 1) {
                    $delib_id = substr($id, 3, strlen($id));
                    $this->Deliberation->id = $delib_id;
                    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id)));

                    $annexes_id = array();
                    $annexes = array();
                    $tmp_annexes = $this->Deliberation->Annex->getAnnexesFromDelibId($delib_id, 1);
                    $dyn_path = "/files/generee/fd/$seance_id/$delib_id/";
                    $path = WEBROOT_PATH . $dyn_path;
                    if (!empty($tmp_annexes))
                        array_push($annexes_id, $tmp_annexes);
                    $path_annexes = $path . 'annexes/';
                    foreach ($annexes_id as $annex_ids) {
                        foreach ($annex_ids as $annex_id) {
                            $annexFile = $this->Deliberation->Annex->find('first', array('conditions' => array('Annex.id' => $annex_id['Annex']['id']),
                                'recursive' => -1));
                            if ($annexFile['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text') {
                                $annexData = $annexFile['Annex']['data_pdf'];
                                $annexName = $annexFile['Annex']['filename_pdf'];
                            } else {
                                $annexData = $annexFile['Annex']['data'];
                                $annexName = $annexFile['Annex']['filename'];
                            }
                            $fichierAnnex = $this->Gedooo->createFile($path_annexes, $annexName, $annexData);
                            array_push($annexes, $fichierAnnex);
                        }
                    }

                    $model_id = $this->Typeseance->modeleProjetDelibParTypeSeanceId($delib['Seance']['type_id'], 3);
                    $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/delib/1/false");
                    $id_d = $this->Pastell->createDocument($id_e);
                    $file_path = WEBROOT_PATH . "/files/generee/fd/null/$delib_id/delib.pdf";
                    $this->Deliberation->saveField('delib_pdf', file_get_contents($file_path));
                    $res = $this->Pastell->modifyDocument($id_e, $id_d, $delib, $annexes);
                    if ($res == 1) {
                        $this->Deliberation->saveField('pastell_id', $id_d);
                        if ($circuit_id > -1) {
                            $this->Pastell->insertInParapheur($id_e, $id_d);
                            $this->Pastell->insertInCircuit($id_e, $id_d, $circuit_id);
                            $message = +$this->Pastell->action($id_e, $id_d, 'send-iparapheur');
                        } else {
                            $this->Deliberation->saveField('signee', 1);
                        }
                        $message = $message . $delib['Deliberation']['num_delib'] . " : Envoyé avec succès<br />";
                    } else {
                        $this->Pastell->action($id_e, $id_d, "supression");
                        $message = $message . $delib['Deliberation']['num_delib'] . " : " . $res . "<br />";
                    }
                }
            }
            $this->Session->setFlash($message, 'growl', array('type' => 'erreurTDT'));
            $this->redirect("/deliberations/sendToPastell/$seance_id");
        }
    }

    function sendToParapheur($seance_id = null)
    {
        $erreur = false;
        $message = '';
        $this->set('seance_id', $seance_id);
        if (Configure::read('USE_PARAPH')) {
            $this->Parafwebservice = new IparapheurComponent();
            $circuits = $this->Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
        }
        $circuits['soustype']['-1'] = 'Signature manuscrite';
        if (empty($this->data)) {
            $this->Deliberation->Behaviors->attach('Containable');
            $conditions["Deliberation.etat >"] = -1;
            if ($seance_id == null) {
                $conditions["Deliberation.etat_parapheur != "] = null;
                $conditions["Deliberation.etat >"] = 2;
                $delibs = $this->Deliberation->find('all', array('conditions' => $conditions));
            } else {
                $delibs = $this->Seance->getDeliberations($seance_id, array('conditions' => $conditions));
            }

            for ($i = 0; $i < count($delibs); $i++) {
                if ($seance_id == null) {
                    $tab_seances = array();
                    foreach ($delibs[$i]['Seance'] as $seance) {
                        $tab_seances[] = $seance['id'];
                    }
                    $seance_id = $this->Seance->getSeanceDeliberante($tab_seances);
                    $type_id = $this->Seance->getType($seance_id);
                } else
                    $type_id = $this->Seance->getType($seance_id);

                $delibs[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($type_id, 3);
            }
            $this->set('deliberations', $delibs);
            $this->set('circuits', $circuits['soustype']);
        } else {
            if ($this->data['Deliberation']['circuit_id'] == '') {
                $this->Session->setFlash("Vous devez saisir un circuit avant l'envoi.", 'growl', array('type' => 'erreur'));
                $this->redirect('/deliberations/sendToParapheur');
                exit;
            }
            if (!isset($this->data['Deliberation']['id'])) {
                $this->Session->setFlash("Vous devez sélectionner un acte à envoyer.", 'growl', array('type' => 'erreur'));
                $this->redirect('/deliberations/sendToParapheur/' . $seance_id);
                exit;
            }
            foreach ($this->data['Deliberation']['id'] as $delib_id => $bool) {
                if ($bool == 1) {
                    $seance_id = $this->Deliberation->getSeanceDeliberanteId($delib_id);
                    $type_id = $this->Seance->getType($seance_id);

                    $this->Deliberation->id = $delib_id;
                    $this->Deliberation->saveField('date_envoi_signature', date("Y-m-d H:i:s", strtotime("now")));
                    if ($this->data['Deliberation']['circuit_id'] == -1) {
                        $this->Deliberation->saveField('signee', true);
                        $this->Deliberation->saveField('etat_parapheur', 0);
                        continue;
                    }
                    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id)));
                    $soustype = $circuits['soustype'][$this->data['Deliberation']['circuit_id']];
                    $objetDossier = $this->Parafwebservice->handleObject($delib['Deliberation']['objet']);
                    $annexes = array();
                    $tmp1 = 0;
                    foreach ($delib['Annex'] as $annex) {
                        if ($annex['joindre_ctrl_legalite']) {
                            $annexes[$tmp1][3] = $annex['filename'];
                            $annexes[$tmp1][2] = 'UTF-8';
                            $annexes[$tmp1][1] = $annex['filetype'];
                            $annexes[$tmp1][0] = $annex['data'];
                            $tmp1++;
                        }
                    }
                    $model_id = $this->Typeseance->modeleProjetDelibParTypeSeanceId($type_id, $delib['Deliberation']['etat']);
                    $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/rapport/1/false");

                    $content = file_get_contents(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/rapport.pdf");
                    $creerdos = $this->Parafwebservice->creerDossierWebservice(
                        "[" . $delib_id . "] " . $objetDossier,
                        Configure::read('TYPETECH'),
                        $soustype,
                        Configure::read('VISIBILITY'),
                        $content,
                        $annexes);
                    $delib['Deliberation']['etat_parapheur'] = 1;
                    if ($creerdos['messageretour']['coderetour'] == 'OK') {
                        $this->Deliberation->saveField('etat_parapheur', 1);
                        $this->Deliberation->saveField('id_parapheur', $creerdos['dossierID']);
                    } else {
                        $erreur = true;
                        $message = $creerdos['messageretour']['message'];
                    }
                }
            }
            if ($erreur)
                $this->Session->setFlash(utf8_decode($message), 'growl', array('type' => 'erreur'));
            else {
                if ($this->data['Deliberation']['circuit_id'] == -1) {
                    $this->Session->setFlash("Les documents ont été déclarés signés.", 'growl');
                } else {
                    $this->Session->setFlash("Les documents ont été envoyés au parapheur électronique.", 'growl');
                }
            }
            $this->redirect('/deliberations/sendToParapheur/' . $seance_id);
            exit;
        }
    }

    function verserAsalae()
    {
        require_once(ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'pcltar' . DS . 'pcltar.lib.php');
        if (empty($this->data)) {
            $this->Deliberation->Behaviors->attach('Containable');
            $this->paginate = array('conditions' => array('Deliberation.etat' => 5),
                'fields' => array('Deliberation.id', 'Deliberation.objet_delib', 'Deliberation.titre',
                    'Deliberation.num_delib', 'etat_asalae'),
                'contain' => array('Service.libelle', 'Theme.libelle'),
                'limit' => 20);

            $delibs = $this->Paginate('Deliberation');
            $this->set('deliberations', $delibs);
        } else {
            $client = new SoapClient(Configure::read('ASALAE_WSDL'));

            foreach ($this->data['Deliberation'] as $id => $bool) {
                if ($bool == 1) {
                    $delib_id = substr($id, 3, strlen($id));
                    $delib = $this->Deliberation->read(null, $delib_id);
                    $path = WEBROOT_PATH . "/files/generee/delibs/$delib_id/";
                    $this->Gedooo->createFile($path, "delib.pdf", $delib['Deliberation']['delib_pdf']);
                    // Création de l'archive
                    @PclTarCreate($path . "versement.tgz");

                    // Ajout du fichier de délibération
                    @PclTarAddList($path . "versement.tgz", $path . "delib.pdf", '.', $path);
                    $Docs = array('Attachment' =>
                        array('@attributes' =>
                            array('format' => 'fmt/18',
                                'mimeCode' => 'application/pdf',
                                'filename' => 'delib.pdf'),
                            '@value' => ''
                        ),
                        'Description' => 'Acte',
                        'Type' => array(
                            '@attributes' => array(
                                'listVersionID' => 'edition 2009'),
                            '@value' => 'CDO')
                    );

                    if ($delib['Deliberation']['tdt_id'] != null) {
                        $AR = $this->S2low->getAR($delib['Deliberation']['tdt_id'], true);
                        $this->Gedooo->createFile($path, "bordereau.pdf", $AR, '.', $path);
                        // Ajout du fichier de bordereau
                        @PclTarAddList($path . "versement.tgz", $path . "bordereau.pdf", '.', $path);
                        array_push($Docs, array('Attachment' =>
                                array('@attributes' =>
                                    array('format' => 'fmt/18',
                                        'mimeCode' => 'application/pdf',
                                        'filename' => 'bordereau.pdf'),
                                    '@value' => ''
                                ),
                                'Description' => 'Bordereau',
                                'Type' => array(
                                    '@attributes' => array('listVersionID' => 'edition 2009'),
                                    '@value' => 'CDO')
                            )
                        );
                    }
                    $document = file_get_contents($path . "versement.tgz");

                    $options = array(
                        'TransferIdentifier' => Configure::read('IDENTIFIANT_VERSANT') . '_' . $delib['Deliberation']['num_delib'],
                        'Comment' => $delib['Deliberation']['objet_delib'],
                        'Date' => date('c'),
                        'TransferringAgency' => array('Identification' => Configure::read('IDENTIFIANT_VERSANT')),
                        'ArchivalAgency' => array('Identification' => Configure::read('SIREN_ARCHIVE')),
                        'Contains' => array(
                            'ArchivalAgreement' => Configure::read('NUMERO_AGREMENT'),
                            'DescriptionLanguage' => array(
                                '@attributes' => array('listVersionID' => 'edition 2009'),
                                '@value' => 'fr'),
                            'DescriptionLevel' => array(
                                '@attributes' => array('listVersionID' => 'edition 2009'),
                                '@value' => 'file'),
                            'Name' => 'Déliberation envoyee depuis WebDelib',
                            'ContentDescription' => array(
                                'CustodialHistory' => 'Délibération en provenance de Webdelib',
                                'Description' => $delib['Deliberation']['objet_delib'],
                                'Language' => array(
                                    '@attributes' => array('listVersionID' => 'edition 2009'),
                                    '@value' => 'fr'),
                                'OriginatingAgency' => array('Identification' => Configure::read('IDENTIFIANT_VERSANT')),
                                'ContentDescriptive' => array('KeywordContent' => 'Deliberation',
                                    'KeywordReference' => '1',
                                    'KeywordType' => array(
                                        '@attributes' => array('listVersionID' => 'edition 2009'),
                                        '@value' => 'genreform')
                                ),
                            ),
                            'Appraisal' => array(
                                'Code' => array(
                                    '@attributes' => array('listVersionID' => 'edition 2009'),
                                    '@value' => 'conserver'),
                                'Duration' => 'P1Y',
                                'StartDate' => date('Y-m-d')),
                            'AccessRestriction' => array(
                                'Code' => array(
                                    '@attributes' => array('listVersionID' => 'edition 2009'),
                                    '@value' => 'AR038'),
                                'StartDate' => date('Y-m-d')),
                            'Document' => $Docs
                        )
                    );

                    $seda = $client->__soapCall("wsGSeda", array($options,
                        Configure::read('IDENTIFIANT_VERSANT'),
                        Configure::read('MOT_DE_PASSE')));
                    $ret = $client->__soapCall("wsDepot", array("bordereau.xml",
                        base64_encode($seda),
                        "versement.tgz",
                        base64_encode($document), 'TARGZ',
                        Configure::read('IDENTIFIANT_VERSANT'),
                        Configure::read('MOT_DE_PASSE')));
                    // Changement d'état de la délibération
                    if ($ret == 0) {
                        $this->Session->setFlash("Les documents ont été transférés à AS@LAE", 'growl');
                        $this->Deliberation->id = $delib_id;
                        $this->Deliberation->saveField('etat_asalae', 1);
                    } else {
                        $this->Session->setFlash("Code retour de AS@LAE : $ret", 'growl', array('type' => 'erreur'));
                    }
                }
            }
            $this->redirect('/deliberations/verserAsalae');
            exit;
        }
    }

    function goNext($delib_id)
    {
        $delib = $this->Deliberation->read(null, $delib_id);
        if (empty($delib))
            $this->redirect($this->referer());

        if (empty($this->data)) {
            $etapes = $this->Traitement->listeEtapes($delib['Deliberation']['id'], array('selection' => 'APRES'));
            if (empty($etapes)) {
                $this->Session->setFlash("Le projet n'a pas d'étape suivante", 'growl', array('type' => 'erreur'));
                $this->redirect($this->referer());
            }
            $this->set('delib_id', $delib_id);
            $this->set('etapes', $etapes);
        } else {
            $insertion = array(
                '0' => array(
                    'Etape' => array(
                        'etape_id' => null,
                        'etape_nom' => 'Aller à une étape suivante',
                        'etape_type' => 1
                    ),
                    'Visa' => array(
                        '0' => array(
                            'trigger_id' => $this->Session->read('user.User.id'),
                            'type_validation' => 'V'
                        ))));
            $this->Traitement->execute('JS', $this->Session->read('user.User.id'), $delib_id, array(
                'insertion' => $insertion,
                'numero_traitement' => $this->data['Traitement']['etape']
            ));

            $destinataires = $this->Traitement->whoIsNext($delib_id);
            foreach ($destinataires as $destinataire_id)
                $this->_notifier($delib_id, $destinataire_id, 'traiter');

            $this->Historique->enregistre($delib_id, $this->Session->read('user.User.id'), "Le projet a sauté l'étape  ");
            $this->Session->setFlash("Le projet est maintenant à l'étape suivante ", 'growl');
            $this->redirect('/deliberations/tousLesProjetsValidation');
        }
    }

    function rebond($delib_id)
    {
        $this->set('delib_id', $delib_id);
        $acte = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $delib_id),
            'fields' => array('Deliberation.redacteur_id'),
            'recursive' => -1));
        $redacteur_id = $acte['Deliberation']['redacteur_id'];
        if (empty($this->data)) {
            $this->request->data['Insert']['retour'] = true;
            $users = $this->User->listFields(array('order' => 'User.nom'));
            $users[$redacteur_id] = $users[$redacteur_id] . " <Rédacteur du projet>";
            $this->set('users', $users);
            $this->set('typeEtape', $this->Traitement->typeEtape($delib_id));
        } else {
            $user_connecte = $this->Session->read('user.User.id');
            $user = $this->User->read(null, $this->data['Insert']['user_id']);
            $destinataire = $user['User']['prenom'] . ' ' . $user['User']['nom'] . ' (' . $user['User']['login'] . ')';

            // initialisation des visas a ajouter au traitement
            $options = array(
                'insertion' => array(
                    '0' => array(
                        'Etape' => array(
                            'etape_id' => null,
                            'etape_nom' => $user['User']['prenom'] . ' ' . $user['User']['nom'],
                            'etape_type' => 1
                        ),
                        'Visa' => array(
                            '0' => array(
                                'trigger_id' => $this->data['Insert']['user_id'],
                                'type_validation' => 'V'
                            )))));
            if ($this->data['Insert']['option'] == 'retour') {
                $action_com = " avec l'option  de retour";
                $action = 'IL';
            } elseif ($this->data['Insert']['option'] == 'detour') {
                $action_com = "";
                $action = 'IP';
            } elseif ($this->data['Insert']['option'] == 'validation') {
                $action_com = " pour validation finale";
                $action = 'VF';
            }

            $this->Traitement->execute($action, $user_connecte, $delib_id, $options);
            $this->Historique->enregistre($delib_id, $user_connecte, "Le projet a été envoyé à $destinataire $action_com");
            $this->_notifier($delib_id, $this->data['Insert']['user_id'], 'traiter');

            $this->redirect('/');
        }
    }

    function refreshPastell()
    {
        $this->Pastell->refresh();
        $this->Redirect($this->Referer());
    }

    function sendToGed($delib_id)
    {
        $delib = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $delib_id)));
        $cmis = new CmisComponent();
        // Création du répertoire
        $my_new_folder = $cmis->client->createFolder($cmis->folder->id, $delib_id);

        // Dépôt de la délibération et du rapport dans le répertoire que l'on vient de créer
        $cmis->client->createDocument($my_new_folder->id, "deliberation.pdf", array(), $delib['Deliberation']['delib_pdf'], "application/pdf");

        // Dépôt du rapport de projet (on fixe l'etat à 2 pour etre sur d'avoir le rapport et non la délibération
        if (isset($delib['Seance']['date']))
            $this->Typeseance->modeleProjetDelibParTypeSeanceId($delib['Seance']['type_id'], '2');

        //        $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/rapport.pdf/1/false");
        //        $rapport = file_get_contents(WEBROOT_PATH."/files/generee/fd/null/$delib_id/rapport.pdf");
        //        $obj_rapport = $cmis->client->createDocument($my_new_folder->id,
        //                                                     "rapport.pdf",
        //                                                     array (),
        //                                                     $rapport,
        //                                                     "application/pdf");

        if (count($delib['Annex']) > 0) {
            $annex_folder = $cmis->client->createFolder($my_new_folder->id, 'Annexes');
            foreach ($delib['Annex'] as $annex) {
                $cmis->client->createDocument($annex_folder->id, $annex['filename'], array(), $annex['data'], $annex['filetype']);
            }
        }
    }

    function _handleConditions($conditions)
    {
        $projet_type_ids = array();
        $projet_seance_ids = array();

        if (isset($conditions['Deliberationtypeseance.typeseance_id'])) {
            $type_id = $conditions['Deliberationtypeseance.typeseance_id'];
            $typeseances = $this->Deliberationtypeseance->find('all', array('conditions' => array('Deliberationtypeseance.typeseance_id' => $type_id),
                'recursive' => -1));
            foreach ($typeseances as $typeseance) {
                $projet_type_ids[] = $typeseance['Deliberationtypeseance']['deliberation_id'];
            }
            unset($conditions['Deliberationtypeseance.typeseance_id']);
        }
        if (isset($conditions['Deliberationseance.seance_id'])) {
            $projet_seance_ids = $this->Seance->getDeliberationsId($conditions['Deliberationseance.seance_id']);
            unset($conditions['Deliberationseance.seance_id']);
        }
        $result = null;
        if (!empty($projet_type_ids) && !empty($projet_seance_ids))
            $result = array_intersect($projet_type_ids, $projet_seance_ids);
        elseif (empty($projet_type_ids) && empty($projet_seance_ids))
            return $conditions;
        elseif (empty($projet_type_ids)) {
            $result = $projet_seance_ids;
        } elseif (empty($projet_seance_ids)) {
            $result = $projet_type_ids;
        }

        if (isset($conditions['Deliberation.id']))
            $conditions['Deliberation.id'] = array_intersect($conditions['Deliberation.id'], $result);
        elseif (!empty($result))
            $conditions['Deliberation.id'] = $result;
        return ($conditions);
    }

    function _addFiltresAutresActes($actes)
    {
        if (!$this->Filtre->critereExists()) {
            $this->Filtre->addCritere('DeliberationseanceId', array('field' => 'Deliberationseance.seance_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'toutes',
                    'options' => $this->Deliberation->getSeancesFromArray($actes))));
            $typeseances = array();
            foreach ($actes as $projet) {
                if (isset($projet['Typeseance']) && (!empty($projet['Typeseance']))) {
                    foreach ($projet['Typeseance'] as $typeseance)
                        $typeseances[$typeseance['id']] = $typeseance['libelle'];
                }
            }
            $this->Filtre->addCritere('DeliberationtypeseanceId', array(
                'field' => 'Deliberationtypeseance.typeseance_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Type de séance', true),
                    'options' => $typeseances)));
            $this->Filtre->addCritere('Typeacte', array(
                'field' => 'Deliberation.typeacte_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Type d\'acte', true),
                    'empty' => 'tous',
                    'options' => $this->Utils->listFromArray($actes, '/Deliberation/typeacte_id', array('/Typeacte/libelle'), '%s'))));
            $this->Filtre->addCritere('ThemeId', array(
                'field' => 'Deliberation.theme_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Thème', true),
                    'options' => $this->Utils->listFromArray($actes, '/Deliberation/theme_id', array('/Theme/libelle'), '%s'))));
            $this->Filtre->addCritere('ServiceId', array(
                'field' => 'Deliberation.service_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Service émetteur', true),
                    'multiple' => true,
                    'options' => $this->Utils->listFromArray($actes, '/Deliberation/service_id', array('/Service/libelle'), '%s'))));
            $this->Filtre->addCritere('CircuitId', array('field' => 'Deliberation.circuit_id',
                'inputOptions' => array(
                    'label' => __('Circuit de validation', true),
                    'empty' => 'Tous',
                    'options' => $this->Utils->listFromArray($actes, '/Circuit/id', array('/Circuit/nom'), ' %s'))));
        }
    }

    function autresActesAValider()
    {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('titreVue', 'Autres actes en cours d\'élaboration');

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $conditions['Deliberation.etat <'] = 2;
        $conditions['Deliberation.etat >'] = -1;
        $fields = array(
            'Deliberation.id',
            'Deliberation.objet',
            'Deliberation.titre',
            'Deliberation.etat',
            'Deliberation.signee',
            'Deliberation.typeacte_id',
            'Deliberation.etat_parapheur',
            'Deliberation.theme_id',
            'Deliberation.service_id'
        );
        $contain = array(
            'Typeacte.libelle',
            'Service.libelle',
            'Circuit.nom',
            'Theme.libelle',
            'Seance.id',
            'Seance.type_id',
            'Seance.date',
            'Typeseance.id',
            'Typeseance.libelle',
        );
        $actes = $this->Deliberation->getActesExceptDelib($conditions, $fields, $contain);
        $this->_addFiltresAutresActes($actes);
        $this->set('canGoNext', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:goNext"));
        $this->set('peuxValiderEnUrgence', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:validerEnUrgence"));
        $this->set('actes', $actes);

        $this->render('autres_actes');
    }

    function autreActesValides()
    {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('titreVue', 'Autres actes validés');
        if (Configure::read('USE_PARAPH')) {
            $this->Parafwebservice = new IparapheurComponent();
            $circuits = $this->Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
            if ($circuits == null)
                $this->Session->setFlash("Erreur lors de la récupération des circuits du parapheur", 'growl', array('type' => 'warning'));
        }
        $circuits['soustype']['-1'] = 'Signature manuscrite';
        $conditions = $this->Filtre->conditions();
        $conditions['Deliberation.etat'] = array('2', '3', '4');
        $conditions['Deliberation.signee'] = null;
        $fields = array(
            'Deliberation.id',
            'Deliberation.objet',
            'Deliberation.titre',
            'Deliberation.etat',
            'Deliberation.signee',
            'Deliberation.etat_parapheur',
            'Deliberation.typeacte_id',
            'Deliberation.theme_id',
            'Deliberation.service_id'
        );
        $contain = array(
            'Typeacte.libelle',
            'Typeacte.modeleprojet_id',
            'Typeacte.modelefinal_id',
            'Service.libelle',
            'Circuit.nom',
            'Theme.libelle',
            'Seance.id',
            'Seance.type_id',
            'Seance.date',
            'Typeseance.id',
            'Typeseance.libelle',
        );

        $actes = $this->Deliberation->getActesExceptDelib($conditions, $fields, $contain);
        $this->_addFiltresAutresActes($actes);

        $userId = $this->Session->read('user.User.id');
        $editerTous = $this->Droits->check($userId, "Deliberations:editerTous");

        $this->set('canEdit', $editerTous);
        $this->set('actes', $actes);
        $this->set('circuits', $circuits['soustype']);
        $this->render('autres_actes');
    }

    function sendActesToSignature()
    {
        if (Configure::read('USE_PARAPH') && $this->data['Parapheur']['circuit_id'] != -1) {
            $this->Parafwebservice = new IparapheurComponent();
            $circuits = $this->Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
        }
        $circuits['soustype']['-1'] = 'Signature manuscrite';
        $this->Deliberation->Behaviors->attach('Containable');
        foreach ($this->data['Deliberation'] as $tmp_id => $bool) {
            if ($bool) {
                $acte_id = substr($tmp_id, 3, strlen($tmp_id));
                $this->Deliberation->id = $acte_id;
                $acte = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $acte_id),
                    'contain' => array('Annex', 'Typeacte.compteur_id')));

                //On génére le numéro de l'acte lors de l'envoi a signature
                $num = $this->Seance->Typeseance->Compteur->genereCompteur($acte['Typeacte']['compteur_id']);
                $this->Deliberation->saveField('num_delib', $num);
                $this->Deliberation->saveField('date_acte', date("Y-m-d H:i:s", strtotime("now")));

                $model_id = $this->Deliberation->Typeacte->getModelId($acte['Deliberation']['typeacte_id'], 'modelefinal_id');
                $this->requestAction("/models/generer/$acte_id/null/$model_id/0/1/D_$acte_id.odt");
                $filename = WEBROOT_PATH . "/files/generee/fd/null/$acte_id/D_$acte_id.odt.pdf";
                $content = file_get_contents($filename);
                $this->Deliberation->saveField('delib_pdf', $content);
                if ($this->data['Parapheur']['circuit_id'] == -1) {
                    $this->Deliberation->saveField('signee', 1);
                    $this->Deliberation->saveField('etat', 3);
                    $this->Deliberation->saveField('date_envoi_signature', date("Y-m-d H:i:s", strtotime("now")));
                } else {
                    $this->Parafwebservice = new IparapheurComponent();
                    $objetDossier = $this->Parafwebservice->handleObject($acte['Deliberation']['objet']);
                    $annexes = array();
                    $tmp1 = 0;
                    foreach ($acte['Annex'] as $annex) {
                        if ($annex['joindre_ctrl_legalite']) {
                            $annexes[$tmp1][3] = $annex['filename'];
                            $annexes[$tmp1][2] = 'UTF-8';
                            $annexes[$tmp1][1] = $annex['filetype'];
                            $annexes[$tmp1][0] = $annex['data'];
                            $tmp1++;
                        }
                    }

                    $creerdos = $this->Parafwebservice->creerDossierWebservice(
                        "[" . $acte_id . "] " . $objetDossier,
                        Configure::read('TYPETECH'),
                        $circuits['soustype'][$this->data['Parapheur']['circuit_id']],
                        Configure::read('VISIBILITY'),
                        $content,
                        $annexes);

                    if ($creerdos['messageretour']['coderetour'] == 'OK') {
                        $this->Deliberation->saveField('etat_parapheur', 1);
                        $this->Deliberation->saveField('etat', 3);
                        $this->Deliberation->saveField('date_envoi_signature', date("Y-m-d H:i:s", strtotime("now")));
                        $this->Deliberation->saveField('id_parapheur', $creerdos['dossierID']);
                    } else {
                        $this->Session->setFlash($creerdos['messageretour']['coderetour'], 'growl', array('type' => 'erreur'));
                    }
                }
            }
        }
        $this->redirect('/deliberations/autreActesValides');
    }

    function autreActesAEnvoyer()
    {
        $this->set('titreVue', 'Autres actes à envoyer au contrôle de légalité');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = array(3, 4);
        $conditions['Deliberation.signee'] = 1;
        $fields = array(
            'Deliberation.id',
            'Deliberation.num_delib',
            'Deliberation.objet',
            'Deliberation.objet_delib',
            'Deliberation.titre',
            'Deliberation.num_pref',
            'Deliberation.signee',
            'Deliberation.etat',
            'Deliberation.typeacte_id',
            'Deliberation.theme_id',
            'Deliberation.service_id'
        );
        $contain = array(
            'Typeacte.libelle',
            'Service.libelle',
            'Circuit.nom',
            'Theme.libelle',
            'Seance.id',
            'Seance.type_id',
            'Seance.date',
            'Typeseance.id',
            'Typeseance.libelle',
        );
        $order = array('Deliberation.num_delib ASC');

        if (Configure::read('USE_PARAPH')) {
            $this->Parafwebservice = new IparapheurComponent();
            $circuits = $this->Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
        }

        $actes = $this->Deliberation->getActesATeletransmettre($conditions, $fields, $contain, $order);
        $this->_addFiltresAutresActes($actes);
        for ($i = 0; $i < count($actes); $i++) {
            $actes[$i]['Deliberation'][$actes[$i]['Deliberation']['id'] . '_num_pref'] = $actes[$i]['Deliberation']['num_pref'];
            $actes[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($actes[$i]['Deliberation']['num_pref']);
        }

        $circuits['soustype']['-1'] = 'Signature manuscrite';
        $this->set('deliberations', $actes);
        $this->set('circuits', $circuits['soustype']);
        $this->set('dateClassification', $this->S2low->getDateClassification());

        $this->render('to_send');
    }

    public function nonTransmis()
    {
        $typeacte_ids = $this->Deliberation->Typeacte->find('all', array(
            'recursive' => -1,
            'conditions' => array('Typeacte.teletransmettre' => false),
            'fields' => array('Typeacte.id')));
        $this->Deliberation->Behaviors->attach('Containable');
        $this->request->data = $this->Deliberation->find('all', array(
            'conditions' => array(
                'Deliberation.etat' => array(3, 4),
                'Deliberation.signee' => 1,
                'Deliberation.typeacte_id' => Set::extract('/Typeacte/id', $typeacte_ids)
            ),
            'contain' => array(
                'Typeacte.libelle',
                'Service.libelle',
            ),
            'fields' => array(
                'Deliberation.id',
                'Deliberation.num_delib',
                'Deliberation.objet',
                'Deliberation.objet_delib',
                'Deliberation.titre',
                'Deliberation.num_pref',
                'Deliberation.signee',
                'Deliberation.etat',
                'Deliberation.typeacte_id'
            ),
            'order' => array('Deliberation.num_delib ASC')));
    }

    function autreActesEnvoyes()
    {
        $delibs_id = array();
        $this->set('titreVue', 'Autres actes envoyés au contrôle de légalité');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = 5;
        $conditions['Deliberation.signee'] = 1;
        $ids = $this->Deliberation->getActesExceptDelib(array(), array('Deliberation.id', 'Deliberation.typeacte_id'), array());
        foreach ($ids as $did)
            $delibs_id [] = $did['Deliberation']['id'];
        $conditions['Deliberation.id'] = $delibs_id;

        $fields = array(
            'Deliberation.id',
            'Deliberation.num_delib',
            'Deliberation.objet',
            'Deliberation.objet_delib',
            'Deliberation.titre',
            'Deliberation.num_pref',
            'Deliberation.etat',
            'Deliberation.tdt_id',
            'Deliberation.dateAR',
            'Deliberation.typeacte_id',
            'Deliberation.date_acte',
            'Deliberation.theme_id',
            'Deliberation.service_id'
        );
        $contain = array(
            'Typeacte.libelle',
            'Service.libelle',
            'Circuit.nom',
            'Theme.libelle',
            'Seance.id',
            'Seance.type_id',
            'Seance.date',
            'Typeseance.id',
            'Typeseance.libelle',
        );

        $this->paginate = array('Deliberation' => array('conditions' => $conditions,
            'fields' => $fields,
            'contain' => $contain,
            'limit' => 20));
        $deliberations = $this->Paginator->paginate('Deliberation');
        $this->_addFiltresAutresActes($deliberations);

        $this->set('dateClassification', $this->S2low->getDateClassification());
        for ($i = 0; $i < count($deliberations); $i++) {
            if (isset($deliberations[$i]['Deliberation']['tdt_id'])) {
                $flux = $this->S2low->getFluxRetour($deliberations[$i]['Deliberation']['tdt_id']);
                $codeRetour = substr($flux, 3, 1);
                $deliberations[$i]['Deliberation']['code_retour'] = $codeRetour;

                if ($codeRetour == 4) {
                    $dateAR = $this->_getDateAR($res = mb_substr($flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
                    $this->Deliberation->changeDateAR($deliberations[$i]['Deliberation']['id'], $dateAR);
                    $deliberations[$i]['Deliberation']['DateAR'] = $dateAR;
                }
            }
        }
        $this->set('deliberations', $deliberations);
        $this->render('transmit');
    }

    function getTypeseancesParTypeacteAjax($typeacte_id = null)
    {
        if ($typeacte_id == null)
            exit;
        //$typeacte_id =
        App::import('model', 'TypeseancesTypeacte');
        $TypeseancesTypeacte = new TypeseancesTypeacte();
        $typeseance_ids = $TypeseancesTypeacte->getTypeseanceParNature($typeacte_id);
        $typeseances = $this->Typeseance->find('list', array('conditions' => array('Typeseance.id' => $typeseance_ids),
            'order' => array('Typeseance.libelle' => 'ASC')));
        $this->set('typeseances', $typeseances);
        $this->layout = 'ajax';
    }

    function getSeancesParTypeseanceAjax($typeseances_id)
    {
        $result = array();

        //Gestion des droits : editer des projets Validés
        $user = $this->Session->read('user');
        $canEditAll = $this->Droits->check($user['User']['id'], "Deliberations:editerTous");

        if (strpos($typeseances_id, ',') !== false) {
            $typeseances_id = explode(',', $typeseances_id);
        }
        $this->Seance->Behaviors->attach('Containable');
        if ($typeseances_id != 'null') {
            $seances = $this->Seance->find('all', array('conditions' => array('Seance.type_id' => $typeseances_id,
                'Seance.traitee' => 0,),
                'contain' => array('Typeseance.libelle', 'Typeseance.retard'),
                'order' => array('Typeseance.libelle' => 'ASC', 'Seance.date' => 'ASC'),
                'fields' => array('Seance.id', 'Seance.type_id', 'Seance.date')));

            foreach ($seances as $seance) {
                $iTime = strtotime($seance['Seance']['date']);
                //Voir tous les projets ou tous les futurs dates avec un delais respecté
                if ($canEditAll || time() < mktime(0, 0, 0, date('m', $iTime), date('d', $iTime) - $seance['Typeseance']['retard'], date('Y', $iTime)))
                    $result[$seance['Seance']['id']] = $seance['Typeseance']['libelle'] . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
            }
        }
        $this->set('seances', $result);
        $this->layout = 'ajax';
    }

    function copyFromPrevious($delib_id, $seance_id)
    {
        $this->Deliberation->_effacerListePresence($delib_id);
        $this->Deliberation->_copyFromPreviousList($delib_id, $seance_id);
        $this->redirect("/seances/voter/$delib_id/$seance_id");
    }

    function traitementLot()
    {
        $ids = array();
        //$redirect = $this->Session->read('user.User.lasturl');
        $redirect = $this->referer();
        if (isset($this->data['Deliberation']['action']) && empty($this->data['Deliberation']['action'])) {
            $this->Session->setFlash('Veuillez sélectionner une action.', 'growl', array('type' => 'erreur'));
            return $this->redirect($redirect);
        } else
            $action = $this->data['Deliberation']['action'];

        if (isset($this->data['Deliberation_check']))
            foreach ($this->data['Deliberation_check'] as $id => $bool) {
                if ($bool == 1) {
                    $delib_id = substr($id, 3, strlen($id));
                    $this->Deliberation->id = $delib_id;
                    if ($action == 'suppression') {
                        $this->Deliberation->supprimer($delib_id);
                    }
                    if ($action == 'valider') {
                        $this->_accepteDossier($delib_id);
                    }
                    if ($action == 'refuser') {
                        $this->_refuseDossier($delib_id);
                    }
                    if ($action == 'validerUrgence') {
                        $this->validerEnUrgence($delib_id, false);
                    }
                    $ids[] = $delib_id;
                }
            }

        if (!isset($ids) || (isset($ids) && count($ids) == 0)) {
            $this->Session->setFlash('Veuillez sélectionner une délibération.', 'growl', array('type' => 'erreur'));
            $this->redirect($redirect);
        }

        if ($action == 'generation') {
            $model_id = $this->data['Deliberation']['modele'];
            $projets = $this->Deliberation->find('all', array('conditions' => array('Deliberation.id' => $ids)));
            $format = $this->Session->read('user.format.sortie');
            if (empty($format))
                $format = 0;
            $this->Deliberation->genererRecherche($projets, $model_id, $format);
        }
        $this->Session->setFlash('Action effectuée avec succès', 'growl');
        $this->redirect($redirect);
    }

    function quicksearch()
    {
        $field = trim($this->params->query['data']['field']);
        if (empty($field)) {
            $this->Session->setFlash('Action effectuée avec succès', 'growl');
            $this->redirect($this->referer());
        }
        $conditions = array();
        $userId = $this->Session->read('user.User.id');
        if (!$this->Droits->check($userId, 'Deliberations:tousLesProjetsRecherche')) {
            $listeCircuits = explode(',', $this->Circuit->listeCircuitsParUtilisateur($userId));
            if (!empty($listeCircuits))
                $conditions['AND']['OR']['Deliberation.circuit_id'] = $listeCircuits;
            $conditions['AND']['OR']['Deliberation.redacteur_id'] = $userId;
        }
        if (ctype_digit($field)) {
            $conditions['OR']['Deliberation.id'] = $field;
        }
        $conditions['OR']['Deliberation.objet ILIKE'] = "%$field%";
        $conditions['OR']['Deliberation.titre ILIKE'] = "%$field%";

        $ordre = 'Deliberation.created DESC';
        $this->Deliberation->Behaviors->attach('Containable');
        $projets = $this->Deliberation->find('all', array('conditions' => $conditions,
            'order' => array($ordre),
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array('Seance.id', 'Seance.traitee', 'Seance.date', 'Seance.type_id',
                'Service.libelle', 'Theme.libelle', 'Typeacte.libelle', 'Circuit.nom')));

        $this->_afficheProjets($projets, 'Résultat de la recherche parmi mes projets', array('view', 'generer'), array());
    }

}
