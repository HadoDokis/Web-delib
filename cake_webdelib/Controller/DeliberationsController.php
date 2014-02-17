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

    public $helpers = array('Fck');
    public $uses = array('Acteur', 'Deliberation', 'User', 'Annex', 'Typeseance', 'Seance', 'TypeSeance', 'Commentaire', 'ModelOdtValidator.Modeltemplate', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Infosupdef', 'Infosup', 'Historique', 'Cakeflow.Circuit', 'Cakeflow.Composition', 'Cakeflow.Etape', 'Cakeflow.Traitement', 'Cakeflow.Visa', 'Nomenclature', 'Deliberationseance', 'Deliberationtypeseance');
    public $components = array('ModelOdtValidator.Fido', 'Gedooo', 'Date', 'Utils', 'Email', 'Acl', 'Droits', 'Iparapheur', 'Filtre', 'Cmis', 'Progress', 'Conversion', 'Pastell', 'S2low', 'Pdf', 'Paginator', 'Pastell', 'Cookie');
    public $aucunDroit = array('getTypeseancesParTypeacteAjax', 'quicksearch', 'genereFusionToClient');
    // Gestion des droits
    public $demandeDroit = array(
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
    public $commeDroit = array(
        'view' => array('Pages:mes_projets', 'Pages:tous_les_projets', 'downloadDelib'),
        'attribuercircuit' => 'Deliberations:mesProjetsRedaction',
        'addIntoCircuit' => 'Deliberations:mesProjetsRedaction',
        'traiter' => 'Deliberations:mesProjetsATraiter',
        'retour' => 'Deliberations:mesProjetsATraiter',
        'attribuerSeance' => 'Deliberations:tousLesProjetsSansSeance',
        'refreshSignature' => 'Deliberations:sendToParapheur',
        'autreActesValides' => 'Deliberations:autresActesAValider',
        'autresActesAEnvoyer' => 'Deliberations:autresActesAValider',
        'autresActesEnvoyes' => 'Deliberations:autresActesAValider',
        'getTampon' => 'Deliberations:transmit'
    );
    public $libelleControleurDroit = 'Projets';
    public $ajouteDroit = array(
        'edit',
        'delete',
        'goNext',
        'validerEnUrgence',
        'rebond',
        'editerTous',
    );
    public $libellesActionsDroit = array(
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

        $this->Deliberation->Behaviors->load('Containable');
        $this->request->data = $this->Deliberation->find('first', array(
            'fields' => array(
                'id', 'anterieure_id', 'service_id', 'circuit_id', 'typeacte_id',
                'etat', 'num_delib', 'titre', 'objet', 'objet_delib', 'num_pref',
                'texte_projet_name', 'texte_synthese_name', 'deliberation_name',
                'created', 'modified', 'deliberation', 'texte_projet', 'texte_synthese'),
            'contain' => array(
                'Multidelib' => array('fields' => array('id', 'objet', 'objet_delib', 'num_delib', 'etat', 'deliberation', 'deliberation_name')),
                'Redacteur' => array('fields' => array('id', 'nom', 'prenom')),
                'Rapporteur' => array('fields' => array('id', 'nom', 'prenom')),
                'Infosup',
                'Annex' => array('fields' => array('id', 'titre', 'joindre_ctrl_legalite', 'filename')),
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),

                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
            'conditions' => array('Deliberation.id' => $id)
        ));
        $this->request->data['Deliberationseance'] = Hash::sort($this->request->data['Deliberationseance'], '{n}.Seance.Typeseance.action', 'asc');

        $this->request->data['Deliberation']['num_pref']=$this->data['Deliberation']['num_pref'].' - '.$this->_getMatiereByKey($this->data['Deliberation']['num_pref']);
        
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
                $this->User->Behaviors->load('Containable');
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
                $this->redirect(array('action'=>'mesProjetsRedaction'));
            }
        }

        // Compactage des informations supplémentaires
        $this->request->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup'], false);

        // Lecture des versions anterieures
        $listeAnterieure = array();
        $tab_anterieure = $this->Deliberation->chercherVersionAnterieure($this->data, 0, $listeAnterieure, 'view');
        $this->set('tab_anterieure', $tab_anterieure);

        //Lecture de la version supérieure
        $versionsup = $this->Deliberation->chercherVersionSuivante($id);
        $this->set('versionsup', $versionsup);

        if ($this->Droits->check($this->user_id, "Deliberations:edit") && $this->Deliberation->estModifiable($id, $this->user_id))
            $this->set('userCanEdit', true);
        else
            $this->set('userCanEdit', false);

        // Lecture et initialisation des commentaires
        $commentaires = $this->Commentaire->find('all', array(
            'conditions' => array('Commentaire.delib_id' => $id),
            'order' => 'created DESC'));
        for ($i = 0; $i < count($commentaires); $i++) {
            if ($commentaires[$i]['Commentaire']['agent_id'] == -1) {
                $commentaires[$i]['Commentaire']['prenomAgent'] = "i-Parapheur";
                $commentaires[$i]['Commentaire']['nomAgent'] = "Adullact";
            } else {
                $agent = $this->User->find('first', array(
                    'conditions' => array('User.id' => $commentaires[$i]['Commentaire']['agent_id']),
                    'recursive' => -1,
                    'fields' => array('nom', 'prenom')));
                $commentaires[$i]['Commentaire']['nomAgent'] = $agent['User']['nom'];
                $commentaires[$i]['Commentaire']['prenomAgent'] = $agent['User']['prenom'];
            }
        }
        $this->set('commentaires', $commentaires);
        $this->set('historiques', $this->Historique->find('all', array(
            'conditions' => array("Historique.delib_id" => $id),
            'order' => 'Historique.created DESC')));

        //Récupération du model_id (pour lien bouton generer)
        $model_id = $this->Deliberation->getModelId($id);
        $this->request->data['Modeltemplate']['id'] = $model_id;


        // Mise en forme des données du projet ou de la délibération
        $this->request->data['Deliberation']['libelleEtat'] = $this->Deliberation->libelleEtat($this->data['Deliberation']['etat']);
        if (!empty($this->data['Seance']['date']))
            $this->request->data['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data['Seance']['date']));
        // initialisation des séances
        $listeTypeSeance=array();
        $this->request->data['listeSeances']=array();
        if (isset($this->request->data['Deliberationseance']) && !empty($this->request->data['Deliberationseance'])) {
            foreach ($this->request->data['Deliberationseance'] as $keySeance => $seance) {
                $this->request->data['listeSeances'][] = array(
                    'seance_id' => $seance['Seance']['id'],
                    'type_id' => $seance['Seance']['type_id'],
                    'action' => $seance['Seance']['Typeseance']['action'],
                    'libelle' => $seance['Seance']['Typeseance']['libelle'],
                    'date' => $seance['Seance']['date']);
                $listeTypeSeance[] = $seance['Seance']['type_id'];
            }
        }

        if (isset($this->request->data['Deliberationtypeseance']) && !empty($this->request->data['Deliberationtypeseance'])) {
            foreach ($this->request->data['Deliberationtypeseance'] as $keyType => $typeseance) {
                if (!in_array($typeseance['Typeseance']['id'], $listeTypeSeance))
                    $this->request->data['listeSeances'][] = array(
                        'seance_id' => NULL,
                        'type_id' => $typeseance['Typeseance']['id'],
                        'action' => $typeseance['Typeseance']['action'],
                        'libelle' => $typeseance['Typeseance']['libelle'],
                        'date' => NULL);
            }
        }
        $this->request->data['listeSeances'] = Hash::sort($this->request->data['listeSeances'], '{n}.action', 'asc');

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
                $conditions = array(
                    'traitement_id' => $traitement['Traitement']['id'],
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

    function majEtatParapheur($id = null) {
        $this->requestAction(array('plugin'=>'cakeflow','controller'=>'traitements', 'action'=>'majTraitementsParapheur', $id, 'true'));
        return $this->redirect(array('action'=>'view', $id));
    }

    function _getFileData($fileName, $fileSize) {
        return @fread(fopen($fileName, "r"), $fileSize);
    }

    function add() {
        // initialisations
        $sortie = false;
        /* initialisation du lien de redirection */
        $redirect = '/deliberations/mesProjetsRedaction';
        /* initialisation du rédateur et du service emetteur */
        $user = $this->Session->read('user');
        $canEditAll = $this->Droits->check($user['User']['id'], "Deliberations:editerTous");

        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        if (Configure::read('USE_PASTELL')) {
            App::uses('Tdt', 'Lib');
            $Tdt = new Tdt();
            $res = $Tdt->listClassification();
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

                $this->Session->setFlash('Projet créé. Identifiant: ' . $delibId, 'growl');
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
            $this->Infosupdef->Behaviors->load('Containable');
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

    function download($id, $file) {

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

    function deleteDebat($id, $isCommission, $seance_id) {
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

    function downloadDelib($delib_id) {
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
        exit;
    }

    function downloadBordereau($delib_id)
    {
        $this->Deliberation->id = $delib_id;
        $bordereau = $this->Deliberation->field('parapheur_bordereau');
        header('Content-type: application/pdf');
        header('Content-Length: ' . strlen($bordereau));
        header('Content-Disposition: attachment; filename=bordereau_'.$this->Deliberation->field('num_delib').'.pdf');
        echo $bordereau;
        exit;
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
            $newAnnexe['Annex']['filename'] = $annexe['file']['name'];
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
        $canEditAll = $this->Droits->check($user['User']['id'], "Deliberations:editerTous");

        $pos = strrpos(getcwd(), 'webroot');
        $path = substr(getcwd(), 0, $pos);
        $path_projet = $path . "webroot/files/generee/projet/$id/";
        $typeseances_selected = array();
        $seances = array();
        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        if (!$this->request->isPut()) {
            $this->Deliberation->Behaviors->load('Containable');
            $this->Seance->Behaviors->load('Containable');

            /* initialisation du lien de redirection */
            $history = $this->Session->read('user.history');
            if (stripos($this->previous, 'deliberations/add') === false
                && stripos($this->previous, 'ajax') === false
            )
                $redirect = $this->previous;
            elseif (stripos($this->previous, 'deliberations/add'))
                $redirect = '/';
            else
                foreach ($history as $h)
                    if (stripos($h, 'deliberations/add') === false
                        && stripos($h, 'deliberations/edit') === false
                        && stripos($h, 'ajax') === false
                    ) {
                        $redirect = $h;
                        break;
                    }


            $this->set('redirect', $redirect);

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
                $this->Deliberation->Multidelib->Behaviors->load('Containable');
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
                $this->Session->setFlash("Vous n'avez pas les droits pour editer le projet '$id'.", 'growl', array('type' => 'erreur'));
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
            $this->set('redirect', $redirect);
            $this->Infosupdef->Behaviors->load('Containable');
            $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                'conditions' => array('model' => 'Deliberation', 'actif' => true),
                'order' => 'ordre',
                'contain' => array('Profil.id'))));

            if (Configure::read('USE_PASTELL')) {
                App::uses('Tdt', 'Lib');
                $Tdt = new Tdt();
                $res = $Tdt->listClassification();
                $this->set('nomenclatures', $res);
            }

            $this->set('DELIBERATIONS_MULTIPLES', Configure::read('DELIBERATIONS_MULTIPLES'));
            $this->set('is_multi', $this->request->data['Deliberation']['is_multidelib']);
            if ($this->request->data['Deliberation']['parapheur_etat'] >= 1) {
                $this->Session->setFlash("Attention, l'acte est en cours de signature!", 'growl', array('type' => 'erreur'));
            }
            $this->render();
        } else {
            $this->Deliberation->begin();
            $redirect = $this->data['Deliberation']['redirect'];
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
                                    if ($annexe['ref'] == 'delibRattachee' . $iref)
                                        if (!$this->_saveAnnexe($delibRattacheeId, $annexe, $annexesErrors)) {
                                            $this->redirect($redirect);
                                        }
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
                $cmd = 'nohup nice -n 10 '.APP.'Console'.DS.'cake Maintenance conversionAnnexe -i '.$id.' >/dev/null 2>&1';
                $pid = shell_exec($cmd);
                $this->Session->setFlash("Le projet $id a été enregistré", 'growl');
                $sortie = true;

                //Envoi d'une notification de modification au rédacteur
                $currentUser = $this->Session->read('user.User.id');
                $redacteurId = $oldDelib['Deliberation']['redacteur_id'];
                if ($currentUser != $redacteurId){
                    $this->User->notifier($id, $redacteurId, 'modif_projet_cree');
                }
                //Envoi d'une notification de modification aux utilisateurs qui ont déjà validé le projet
                $destinataires = $this->Traitement->whoIsPrevious($id);
                foreach ($destinataires as $destinataire_id)
                    if ($destinataire_id != $currentUser)
                        $this->User->notifier($id, $destinataire_id, 'modif_projet_valide');
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
                    $this->Deliberation->Multidelib->Behaviors->load('Containable');
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
                $this->set('redirect', $redirect);
                $annexes = $this->Annex->find('all', array('conditions' => array('model' => 'Projet', 'foreign_key' => $id)));
                foreach ($annexes as $id => $annexe)
                    $this->request->data['Annex'][$id] = $annexe['Annex'];

                if (!empty($this->data['Deliberation']['num_pref']))
                    $this->request->data['Deliberation']['num_pref_libelle'] = $this->data['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->data['Deliberation']['num_pref']);


                $this->set('rapporteurs', $this->Acteur->generateListElus('Acteur.nom'));
                $this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);

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
    
    function recapitulatif($id = null) {
        if (empty($this->data)) {
            if (!$id) {
                $this->Session->setFlash('Invalide id pour la deliberation', 'growl', array('type' => 'erreur'));
                return $this->redirect(array('action'=>'mesProjetsRedaction'));
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

    function delete($id = null) {
        $delib = $this->Deliberation->find('first', array(
            'recursive' => -1,
            'fields' => array('Deliberation.id', 'Deliberation.redacteur_id', 'Deliberation.etat'),
            'conditions' => array('id' => $id)));

        if (empty($delib)) {
            $this->Session->setFlash('Invalide id pour le projet de deliberation : suppression impossible', 'growl', array('type' => 'erreur'));
        } else {
            $canDelete = $this->Droits->check($this->user_id, "Deliberations:delete");
            if ((($delib['Deliberation']['redacteur_id'] == $this->user_id) && ($delib['Deliberation']['etat'] == 0)) || ($canDelete)) {
                $this->Deliberation->supprimer($id);
                $this->Session->setFlash('Le projet \'' . $id . '\' a été supprimé.', 'growl');
            } else {
                $this->Session->setFlash('Vous ne pouvez pas supprimer ce projet', 'growl');
            }
        }
        $this->redirect($this->referer());
    }

    function addIntoCircuit($id = null) {
        $this->request->data = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $id)));
        // envoi un mail a tous les membres du circuit
        if (!empty($this->request->data['Deliberation']['circuit_id'])) {
            // enregistrement de l'historique
            $message = "Projet injecté au circuit : " . $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);
            $this->Historique->enregistre($id, $this->user_id, $message);
            $this->request->data['Deliberation']['date_envoi'] = date('Y-m-d H:i:s', time());
            $this->request->data['Deliberation']['etat'] = '1';
            $this->Deliberation->id = $id;
            if ($this->Circuit->hasEtapeDelegation($this->request->data['Deliberation']['circuit_id'])){
                //On génére le document principale si une etape d'envoi au parapheur est prévue
                $model_id = $this->Deliberation->getModelId($id);
                // FIXME changer appel génération document
                $this->requestAction(array('controller'=>'models', 'action'=>'generer', $id, 'null', $model_id, '0', '1', 'parapheur'));
            }
            if ($this->Deliberation->save($this->request->data)) {
                // insertion dans le circuit de traitement
                if ($this->Traitement->targetExists($id)) {
                    $this->Circuit->ajouteCircuit($this->data['Deliberation']['circuit_id'], $id, $this->user_id);
                    $this->Traitement->Visa->replaceDynamicTrigger($id, $this->user_id);
                    $members = $this->Traitement->whoIs($id);
                    if (empty($members)) {
                        $this->Historique->enregistre($id, $this->user_id, 'Projet validé');
                        $this->Deliberation->saveField('etat', 2);
                    } else {
                        while (in_array($this->user_id, $members)) {
                            $traitementTermine = $this->Traitement->execute('OK', $this->user_id, $id);
                            $this->Historique->enregistre($id, $this->user_id, 'Projet visé (auto)');
                            if ($traitementTermine) {
                                $this->Historique->enregistre($id, $this->user_id, 'Projet validé');
                                $this->Deliberation->saveField('etat', 2);
                                $this->Session->setFlash('Projet inséré dans le circuit et validé', 'growl');
                                $this->redirect(array('action'=>'mesProjetsValides'));
                            }
                            $members = $this->Traitement->whoIs($id);
                        }
                        foreach ($members as $destinataire_id)
                            $this->User->notifier($id, $destinataire_id, 'traitement');

                        $members = $this->Traitement->whoIs($id, 'after');
                        foreach ($members as $user_id)
                            $this->User->notifier($id, $user_id, 'insertion');

                        $this->Session->setFlash('Projet inséré dans le circuit et visé', 'growl');
                        $this->redirect(array('action'=>'mesProjetsRedaction'));
                    }
                } else {
                    $this->Circuit->insertDansCircuit($this->data['Deliberation']['circuit_id'], $id, $this->user_id);
                    $options = array(
                        'insertion' => array(
                            '0' => array(
                                'Etape' => array(
                                    'etape_id' => null,
                                    'etape_nom' => 'Rédacteur',
                                    'etape_type' => 1,
                                    'cpt_retard' => null
                                ),
                                'Visa' => array(
                                    '0' => array(
                                        'trigger_id' => $this->user_id,
                                        'type_validation' => 'V'
                                    )
                                ),
                            )
                        ),
                        'optimisation'=> configure::read('Cakeflow.optimisation')
                    );
                    $traitementTermine = $this->Traitement->execute('IN', $this->user_id, $id, $options);
                    
                    //FIX Devrait enregistrer un historique des actions effectés en optimisation et autre mais pas que sur l'état final
                    if ($traitementTermine) {
                        $this->Historique->enregistre($id, $this->user_id, 'Projet validé');
                        $this->Deliberation->id = $id;
                        $this->Deliberation->saveField('etat', 2);
                    }
                    $this->Traitement->Visa->replaceDynamicTrigger($id, $this->user_id);

                    $members = $this->Traitement->whoIs($id);
                    foreach ($members as $current_id)
                        $this->User->notifier($id, $current_id, 'traitement');

                    $members = $this->Traitement->whoIs($id, 'after');
                    foreach ($members as $user_id)
                        $this->User->notifier($id, $user_id, 'insertion');

                    $this->Session->setFlash('Projet inséré dans le circuit', 'growl');
                    return $this->redirect(array('action'=>'mesProjetsRedaction'));
                }
            } else {
                $this->Session->setFlash('Problème de sauvegarde.', 'growl', array('type' => 'erreur'));
                return $this->redirect(array('action'=>'attribuercircuit', $id));
            }
        } else {
            $this->Session->setFlash('Vous devez assigner un circuit au projet de délibération.', 'growl', array('type' => 'erreur'));
            return $this->redirect(array('action'=>'recapitulatif', $id));
        }
    }

    function attribuercircuit($id = null, $circuit_id = null, $autoAppel = false) {
        $circuits = $this->User->getCircuits($this->user_id);
        $this->set('circuits', $circuits);

        if (empty($this->data)) {
            $this->data = $this->Deliberation->read(null, $id);
            $this->set('lastPosition', '-1');

            //circuit par défaut de l'utilisateur connecté
            if ($circuit_id == null || !array_key_exists($circuit_id, $circuits))
                $circuit_id = $this->User->circuitDefaut($this->user_id, 'id');

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
                return $this->redirect($this->referer());
            }
            $this->set('delib_id', $delib_id);
            $this->set('etapes', $etapes);
        } else {
            $this->Traitement->execute('JP', $this->Session->read('user.User.id'), $delib_id, array('etape_id' => $this->data['Traitement']['etape']));
            $destinataires = $this->Traitement->whoIs($delib_id);
            foreach ($destinataires as $destinataire_id)
                $this->User->notifier($delib_id, $destinataire_id, 'traitement');
            $this->Historique->enregistre($delib_id, $this->Session->read('user.User.id'), "Projet retourné");
            $this->Session->setFlash('Opération effectuée !', 'growl');
            return $this->redirect('/');
        }
    }

    function traiter($id = null, $valid = null)
    {
        $this->Deliberation->Behaviors->load('Containable');
        $projet = $this->Deliberation->find('first', array(
            'fields' => array(
                'id',
                'anterieure_id',
                'service_id',
                'circuit_id',
                'etat',
                'num_delib',
                'titre',
                'objet',
                'objet_delib',
                'num_pref',
                'texte_projet',
                'texte_projet_name',
                'typeacte_id',
                'texte_synthese',
                'texte_synthese_name',
                'deliberation',
                'deliberation_name',
                'created',
                'modified'
            ),
            'contain' => array(
                'Typeacte.libelle',
                'Theme.libelle',
                'Service.libelle',
                'Seance.date',
                'Seance.Typeseance.libelle',
                'Redacteur.id',
                'Redacteur.nom',
                'Redacteur.prenom',
                'Rapporteur.nom',
                'Rapporteur.prenom',
                'Annex',
                'Infosup',
                'Multidelib.id',
                'Multidelib.objet',
                'Multidelib.objet_delib',
                'Multidelib.num_delib',
                'Multidelib.Annex',
                'Multidelib.etat',
                'Multidelib.deliberation',
                'Multidelib.deliberation_name',
                'Multidelib.Typeacte.libelle',
            ),
            'conditions' => array('Deliberation.id' => $id)));

        $projet['Modeltemplate']['id'] = $this->Deliberation->getModelId($id);
        $projet['Deliberation']['num_pref'] = $projet['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($projet['Deliberation']['num_pref']);

        if (empty($projet)) {
            $this->Session->setFlash('identifiant invalide pour le projet : ' . $id, 'growl', array('type' => 'erreur'));
            return $this->redirect(array('action'=>'mesProjetsATraiter'));
        } else {
            if ($valid == null) {
                $nb_recursion = 0;
                $action = 'view';
                $listeAnterieure = array();
                $tab_anterieure = $this->Deliberation->chercherVersionAnterieure($projet, $nb_recursion, $listeAnterieure, $action);
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
        $destinataires = $this->Traitement->whoIsPrevious($id);
        foreach ($destinataires as $destinataire_id)
            $this->User->notifier($nouvelId, $destinataire_id, 'refus');

        $this->Historique->enregistre($id, $this->Session->read('user.User.id'), 'Projet refusé');
    }

    function _accepteDossier($id)
    {
        $traitementTermine = $this->Traitement->execute('OK', $this->user_id, $id);
        $this->Historique->enregistre($id, $this->user_id, 'Projet visé');
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
            $destinataires = $this->Traitement->whoIs($id);
            foreach ($destinataires as $destinataire_id)
                $this->User->notifier($id, $destinataire_id, 'traitement');
        }
    }

    function transmit($seance_id = null) {
        if (!Configure::read('USE_TDT')){
            $this->Session->setFlash('Le tiers de télétransmission est désactivé. Veuillez contacter votre administrateur','growl');
            return $this->redirect($this->previous);
        }
        $this->Deliberation->Behaviors->load('Containable');
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
        // $conditions['Deliberationseance.Seance.Typeseance.action'] = 0;   
        $conditions[] = 'Deliberation.id IN ('
            . 'SELECT deliberations_seances.deliberation_id'
                . ' FROM deliberations_seances '
                . ' INNER JOIN seances  ON ( seances.id=deliberations_seances.seance_id )'
                . ' INNER JOIN typeseances ON ( typeseances.id=seances.type_id )'
                . ' INNER JOIN typeactes  ON ( typeactes.id=Deliberation.typeacte_id )'
                . ' WHERE typeseances.action = 0 AND Typeacte.teletransmettre = TRUE'
        . ' )';
       
        
        $this->Deliberation->Behaviors->load('Containable');
        $this->paginate = array('Deliberation' =>  array(
             'fields' => array(
                 'Deliberation.id',
                 'Deliberation.objet',
                 'Deliberation.objet_delib',
                 'Deliberation.num_delib',
                 'Deliberation.tdt_dateAR',
                 'Deliberation.parapheur_id',
                 'Deliberation.num_pref',
                 'Deliberation.etat',
                 'Deliberation.titre',
                 'Deliberation.tdt_id',
                 'Deliberation.pastell_id',
                 'Deliberation.typeacte_id',
                 'Deliberation.theme_id',
                 'Deliberation.service_id',
                 'Deliberation.circuit_id',),
            'conditions' => $conditions,
            'contain' => array(
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array(
                    'fields' => array('libelle', 'teletransmettre'),
                    'conditions' => array('Typeacte.teletransmettre' => true)),
                'Circuit' => array('fields' => array('nom')),
                'TdtMessage' => array('fields' => array('message_id', 'type_message')),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array(
                            'fields' => array('id', 'libelle', 'action'),
                            'conditions' => array('Typeseance.action' => 0)
                        )))),
            'order' => 'Deliberation.num_delib ASC',
            'limit' => 20));

        $this->set('host', Configure::read(Configure::read('TDT').'_HOST'));
        $this->set('dateClassification', $this->S2low->getDateClassification());

        // On affiche que les delibs vote pour.
        $deliberations = $this->Paginator->paginate('Deliberation');
        
        $toutes_seances = array();
        for ($i = 0; $i < count($deliberations); $i++) {

            $deliberations[$i]['Deliberation']['num_pref'] = $deliberations[$i]['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($deliberations[$i]['Deliberation']['num_pref']);
            
            foreach ($deliberations[$i]['Deliberationseance'] as $Deliberationseance){
                $deliberations[$i]['Seance']['id'] = $Deliberationseance['Seance']['id'];
                $deliberations[$i]['Seance']['date'] = $Deliberationseance['Seance']['date'];
                $deliberations[$i]['Seance']['type_id'] = $Deliberationseance['Seance']['type_id'];
                break;
            }
        }

        $seances = $this->Seance->find('all', array(
            'conditions' => array('Seance.traitee' => 1),
            'recursive' => -1,
            'fields' => array('Seance.id', 'Seance.date')));

        foreach ($seances as $seance)
            $toutes_seances[$seance['Seance']['id']] = $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));

        $this->_ajouterFiltre($deliberations);

        if(!empty($seance_id)){
            $this->Filtre->delCritere('DeliberationseanceId');
            $this->Filtre->delCritere('DeliberationtypeseanceId');
        }

        $this->set('deliberations', $deliberations);
    }

    function toSend($seance_id = null) {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('host', Configure::read('S2LOW_HOST'));
        $date_classification = $this->S2low->getDateClassification();
        if ($date_classification != false) {
            $this->set('dateClassification', $date_classification);
            $this->set('tabNature', $this->_getNatureListe());
            $this->set('tabMatiere', $this->_getMatiereListe());
        } else
            $this->set('dateClassification', "Récupérer la classification");

        if (empty($seance_id)){
            $conditions = $this->_handleConditions($this->Filtre->conditions());
            $conditions['Deliberation.etat <'] = 5;
        }else {
            //Ajout de la condition sur séance par le filtre
            $conditions = $this->Filtre->conditions();
            $conditions['Deliberationseance.seance_id'] = $seance_id;
            $conditions = $this->_handleConditions($conditions);
        }

        $conditions['Deliberation.etat >='] = 3;
        $conditions['Deliberation.delib_pdf <>'] = '';
        $conditions[] = 'Deliberation.id IN ('
            . 'SELECT deliberations_seances.deliberation_id'
                . ' FROM deliberations_seances '
                . ' INNER JOIN seances  ON ( seances.id=deliberations_seances.seance_id )'
                . ' INNER JOIN typeseances ON ( typeseances.id=seances.type_id )'
                . ' INNER JOIN typeactes  ON ( typeactes.id=Deliberation.typeacte_id )'
                . ' WHERE typeseances.action = 0 AND Typeacte.teletransmettre = TRUE'
        . ' )';
        
        $order = array('Deliberation.num_delib ASC');

        $this->Deliberation->Behaviors->load('Containable');
        $projets = $this->Deliberation->find('all', array(
            'fields' => array(
                'Deliberation.id',
                'Deliberation.objet_delib',
                'Deliberation.num_delib',
                'Deliberation.titre',
                'Deliberation.etat',
                'Deliberation.tdt_id',
                'Deliberation.num_pref',
                'Deliberation.circuit_id',
                'Deliberation.typeacte_id',
                'Deliberation.theme_id',
                'Deliberation.pastell_id',
                'Deliberation.service_id'),
            'conditions' => $conditions,
            'contain' => array(
                'Service.libelle',
                'Theme.libelle',
                'Typeacte.libelle',
                'Circuit.nom',
                'Deliberationtypeseance' => array(
                    'fields' => array('id'),
                    'Typeseance' => array(
                        'fields' => array('id', 'libelle', 'action'),
                    )
                ),
                'Deliberationseance' => array(
                    'fields' => array('id'),
                    'Seance' => array(
                        'fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array(
                            'fields' => array('id', 'libelle', 'action')
                        )))),
            'order' => array($order)));

        for ($i = 0; $i < count($projets); $i++) {
            $projets[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($projets[$i]['Deliberation']['num_pref']);
        }

        $this->_ajouterFiltre($projets);

        if (!empty($seance_id)) {
            $this->Filtre->delCritere('DeliberationseanceId');
            $this->Filtre->delCritere('DeliberationtypeseanceId');
            $this->set('seance_id', $seance_id);
        }

        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        if (Configure::read('USE_PASTELL')) {
            App::uses('Tdt', 'Lib');
            $Tdt = new Tdt();
            $res = $Tdt->listClassification();
            $this->set('nomenclatures', $res);
        }

        $this->set('deliberations', $projets);
    }
    
    /* Tri pour les dates de séance
     * 
     */
    function _sortProjetSeanceDate(&$projets){
        foreach ($projets as $keyProjet=>$projet) {
            $projets[$keyProjet]['Deliberationtypeseance'] = Hash::sort($projet['Deliberationtypeseance'], '{n}.Typeseance.action', 'asc');
            $projets[$keyProjet]['Deliberationseance'] = Hash::sort($projet['Deliberationseance'], '{n}.Seance.date', 'asc');
            $projets[$keyProjet]['Deliberationseance'] = Hash::sort($projets[$keyProjet]['Deliberationseance'], '{n}.Seance.Typeseance.action', 'asc');
        }
    }

    function _getNatureListe() {
        $tab = array();
        $doc = new DOMDocument('1.0', 'UTF-8');
        if (!@$doc->load(Configure::read('S2LOW_CLASSIFICATION')))
            return false;
        $NaturesActes = $doc->getElementsByTagName('NatureActe');
        foreach ($NaturesActes as $NatureActe)
            $tab[$NatureActe->getAttribute('actes:CodeNatureActe')] = utf8_decode($NatureActe->getAttribute('actes:Libelle'));

        return $tab;
    }

    function classification() {
        $this->layout = 'popup';
        $this->set('title_for_layout', 'Classification');
        $aClassification=$this->_getMatiereListe();
        if($aClassification!=false)
        $this->set('classification', $aClassification);
    }

    function _getMatiereListe() {
        $tab = array();
        if (Configure::read('TDT') == 'S2LOW'){
            try {
                $xmlObject = Xml::build(Configure::read('S2LOW_CLASSIFICATION'));
            } catch (XmlException $e) {
                //throw new InternalErrorException();
                return false;
            }

            $xmlArray = Xml::toArray($xmlObject);
            foreach ($xmlArray['RetourClassification']['actes:Matieres'] as $matiere)
                foreach ($matiere as $matiere1) {
                    $tab[$matiere1['@actes:CodeMatiere']] = $matiere1['@actes:Libelle'];
                    if(isset($matiere1['actes:Matiere2'])){
                        $matiere1['actes:Matiere2'] = Hash::sort($matiere1['actes:Matiere2'], '{n}.@actes:CodeMatiere', 'asc');
                        foreach ($matiere1['actes:Matiere2'] as $matiere2) {
                            $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere']] = $matiere2['@actes:Libelle'];
                            if(isset($matiere1['actes:Matiere3'])){
                                $matiere1['actes:Matiere3'] = Hash::sort($matiere1['actes:Matiere3'], '{n}.@actes:CodeMatiere', 'asc');
                                foreach ($matiere2['actes:Matiere3'] as $matiere3) {
                                    $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere'] . '.' . $matiere3['@actes:CodeMatiere']] = $matiere3['@actes:Libelle'];
                                    if(isset($matiere1['actes:Matiere4'])){
                                        $matiere1['actes:Matiere4'] = Hash::sort($matiere1['actes:Matiere4'], '{n}.@actes:CodeMatiere', 'asc');
                                        foreach ($matiere3['actes:Matiere4'] as $matiere4) {
                                            $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere'] . '.' . $matiere3['@actes:CodeMatiere'] . '.' . $matiere4['@actes:CodeMatiere']] = $matiere4['@actes:Libelle'];
                                             if(isset($matiere1['actes:Matiere5'])){
                                                $matiere1['actes:Matiere5'] = Hash::sort($matiere1['actes:Matiere5'], '{n}.@actes:CodeMatiere', 'asc');
                                                foreach ($matiere1['actes:Matiere5'] as $matiere5) {
                                                    $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere'] . '.' . $matiere3['@actes:CodeMatiere'] . '.' . $matiere4['@actes:CodeMatiere'] . '.' . $matiere5['@actes:CodeMatiere']] = $matiere2['@actes:Libelle'];
                                                }
                                             }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
        }
        else{ //TODO
//            $tab = $this->Deliberation->Nomenclature->find('list',array('fields'=>array('id','libelle')));
        }
        return $tab;
    }
    
    /** Retourne la matière par rapport a une clé donnée en parametre
     * 
     * @param type $key
     * @return String
     */
    function _getMatiereByKey($key)
    {
        if (Configure::read('TDT') == 'S2LOW'){
            $aMatiere = $this->_getMatiereListe();
            return isset($aMatiere[$key]) ? $aMatiere[$key] : NULL;
        }
        else{
            App::uses('Nomenclature', 'Model');
            $Nomenclature = new Nomenclature();
            $Nomenclature->id = $key;
            return $Nomenclature->field('libelle');
        }
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

    function sendToTdt() {
        if (!Configure::read('USE_TDT')){
            $this->Session->setFlash('Erreur : TDT désactivé. Pour activer ce service, veuillez contacter votre administrateur.', 'growl', array('type' => 'erreurTDT'));
            return $this->redirect($this->referer());
        }
        App::uses('Tdt', 'Lib');
        $Tdt = new Tdt();
        $erreur = '';
        if (!empty($this->data['Deliberation']['id'])) {

            if (Configure::read('TDT') == 'S2LOW' && !is_file(Configure::read('S2LOW_CLASSIFICATION')))
                $this->S2low->getClassification();

            foreach ($this->data['Deliberation']['id'] as $delib_id => $bool) {
                if ($bool == 1 && !empty($this->data[$delib_id . "classif2"])) {
                    $this->Deliberation->id = $delib_id;
                    $this->Deliberation->saveField('num_pref', $this->data[$delib_id . "classif2"]);
                }
            }
            $nbEnvoyee = 1;
            $this->Deliberation->Typeacte->Behaviors->load('Containable');
            foreach ($this->data['Deliberation']['id'] as $delib_id => $bool) {
                if ($bool == 1 && !empty($this->data[$delib_id . "classif2"])) {
                    $this->Deliberation->id = $delib_id;
                    $delib = $this->Deliberation->find('first', array(
                        'conditions' => array('Deliberation.id' => $delib_id)
                    ));
                    if (Configure::read('TDT') == 'PASTELL' && empty($delib['Deliberation']['pastell_id'])) {
                        $erreur .= $delib['Deliberation']['objet'] . ' (' . $delib['Deliberation']['num_delib'] . ') : Identifiant Pastell inconnu.';
                        continue;
                    }
                    if (Configure::read('USE_PASTELL') && !empty($delib['Deliberation']['pastell_id'])) {
                        if ($Tdt->send($delib['Deliberation']['pastell_id'], $delib['Deliberation']['num_pref'])){
                            $this->Deliberation->saveField('etat', 5);
                        }else{
                            $erreur .=  $delib['Deliberation']['objet'] . ' (' . $delib['Deliberation']['num_delib'] . ') : Envoi au TDT échoué';
                            continue;
                        }
                    }elseif(Configure::read('TDT') == 'S2LOW' && Configure::read('USE_S2LOW')){
                        $typeacte = $this->Deliberation->Typeacte->find('first', array(
                            'conditions' => array('Typeacte.id' => $delib['Typeacte']['id']),
                            'contain' => array('Nature.code')
                        ));
                        switch ($typeacte['Nature']['code']){
                            case 'DE': $nature_code = 1; break;
                            case 'AR': $nature_code = 2; break;
                            case 'AI': $nature_code = 3; break;
                            case 'CC': $nature_code = 4; break;
                            case 'AU': $nature_code = 5; break;
                            default: continue;
                        }

                        if (file_exists(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/D_$delib_id.pdf"))
                            unlink(WEBROOT_PATH . "/files/generee/fd/null/$delib_id/D_$delib_id.pdf");
                        //$this->Deliberation->changeClassification($delib_id, $classification);

                        $classification = $delib['Deliberation']['num_pref'];
                        if (strpos($classification, ' -') != false)
                            $classification = substr($classification, 0, strpos($classification, ' -'));

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
                            $seance = $this->Seance->find('first', array(
                                'conditions' => array('Seance.id' => $seance_id),
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

                        $annexes = $this->Annex->getAnnexesFromDelibId($delib_id, true);
                        $nb_pj = 0;
                        foreach ($annexes as $annex) {
                            if (!empty($annex['Annex']['data_pdf'])){
                                $pj_file = $this->Gedooo->createFile( WEBROOT_PATH . "/files/generee/fd/null/$delib_id/annexes/", $annex['Annex']['id'] . '.pdf', $annex['Annex']['data_pdf']);
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
                    }else{
                        $erreur .= $delib['Deliberation']['objet'] . '(' . $delib['Deliberation']['num_delib'] . ') : Aucun connecteur TDT valide.';
                    }
                }
            }
        } else $erreur = 'Aucun Acte(s) selectionné(s)';

        if (empty($erreur))
            $this->Session->setFlash('Acte(s) envoyé(s) correctement au TdT', 'growl');
        else
            $this->Session->setFlash('Erreur : ' . $erreur, 'growl', array('type' => 'erreurTDT'));

        if (!empty($this->data['Seance']['id']))
            return $this->redirect(array('action'=>'toSend', $this->data['Seance']['id']));
        else
            return $this->redirect(array('action'=>'toSend'));
    }

    function getClassification() {
        App::uses('Tdt','Lib');
        $this->Tdt = new Tdt();
        if ($this->Tdt->updateClassification()){
            $this->Session->setFlash('Les données de classification sont à jour', 'growl');
            return $this->redirect(array('action'=>'toSend'));
        } else {
            $this->Session->setFlash('Erreur lors de la récupération de la classification ', 'growl', array('type' => 'erreur'));
            return $this->redirect(array('action' => 'toSend'));
        }
    }

    function positionner($seance_id, $id = null, $delta) {
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
        return $this->redirect(array('controller'=>'seances', 'action'=>'afficherProjets', $seance_id));
    }

    function sortby($seance_id, $sortby) {
        $tab_projets = array();
        $this->Deliberation->Behaviors->load('Containable');
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
                'conditions' => array(
                    'Deliberationseance.seance_id' => $seance_id,
                    'Deliberationseance.deliberation_id' => $deliberations[$i]['Deliberation']['id']
                ),
                'fields' => array('Deliberationseance.id'),
                'recursive' => -1));


            $this->Deliberationseance->id = $ds['Deliberationseance']['id'];
            $this->Deliberationseance->saveField('position', $i + 1);
        }
        return $this->redirect("/seances/afficherProjets/$seance_id");
    }

    function textprojetvue($id = null) {
        $this->set('deliberation', $this->Deliberation->read(null, $id));
        $this->set('delib_id', $id);
    }

    function textsynthesevue($id = null) {
        $this->set('deliberation', $this->Deliberation->read(null, $id));
        $this->set('delib_id', $id);
    }

    function deliberationvue($id = null) {
        $this->set('deliberation', $this->Deliberation->read(null, $id));
        $this->set('delib_id', $id);
    }

    function _getListPresent($delib_id)
    {
        $this->Listepresence->Behaviors->load('Containable');

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
            return $this->redirect(array('controller'=>'seances', 'action'=>'voter', $delib_id, $seance_id));
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
        $this->Session->setFlash("Le quorum n'est plus atteint...", 'growl', array('type' => 'erreur'));
        return $this->redirect(array('controller'=>'seances','action'=>'listerFuturesSeances'));
    }


    /*
     * Affiche la liste des projets en cours de redaction (etat = 0) dont l'utilisateur connecté
     * est le rédacteur.
     */

    function mesProjetsRedaction() {
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
        $this->Deliberation->Behaviors->load('Containable');

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
        $projets = $this->Deliberation->find('all', array(
            'conditions' => $conditions,
            'limit' => $limit,
            'fields' => array(
                'Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'
            ),
            'contain' => array(
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
            'order' => $ordre));
        $this->_sortProjetSeanceDate($projets);
        $this->_ajouterFiltre($projets);

        $this->_afficheProjets(
            $projets,
            'Mes projets en cours de rédaction',
            array('view', 'edit', 'delete', 'attribuerCircuit', 'generer'),
            $listeLiens,
            $nbProjets
        );
    }

    /**
     * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
     * de validation de l'utilisateur connecté et dont le tour de validation est venu.
     */
    function mesProjetsATraiter() {
        $this->Deliberation->Behaviors->load('Containable');
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
        $delibs_ids = $this->Traitement->listeTargetId($this->Session->read('user.User.id'), array(
            'etat' => 'NONTRAITE',
            'traitement' => 'AFAIRE'
        ));
        if (isset($conditions['Deliberation.id'])) {
            $conditions['Deliberation.id'] = array_intersect($conditions['Deliberation.id'], $delibs_ids);
        } else {
            $conditions['Deliberation.id'] = $delibs_ids;
        }
        $conditions['Deliberation.parent_id'] = NULL;
        $projets = $this->Deliberation->find('all', array(
            'conditions' => $conditions,
            'limit' => $limit,
            'fields' => array(
                'Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'
            ),
            'contain' => array(
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
            'order' => array('Deliberation.id' => 'DESC')));

        $this->_sortProjetSeanceDate($projets);
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($projets, 'Mes projets &agrave; traiter', array('view', 'traiter', 'generer'), array(), $nbProjets);
    }

    /*
     * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
     * de validation de l'utilisateur connecté et dont ce n'est pas le tour de valider et les projets
     * dont il est le rédacteur
     */

    function mesProjetsValidation() {
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        $userId = $this->Session->read('user.User.id');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->load('Containable');

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
            'fields' => array(
                'Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'conditions' => $conditions,
            'order' => $ordre,
            'limit' => $limit,
            'contain' => array(
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'action'))),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
            'order' => $ordre));

        $this->_sortProjetSeanceDate($projets);
        $this->_ajouterFiltre($projets);
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));

        $this->_afficheProjets(
            $projets, 'Mes projets en cours d\'élaboration et de validation', array('view', 'generer'), array(), $nbProjets);
    }

    /*
     * Affiche les projets validés (etat = 2) dont l'utilisateur connecté est le rédacteur
     * ou qu'il est dans les circuits de validation des projets
     */

    function mesProjetsValides() {
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->load('Containable');

        $conditions = $this->_handleConditions($this->Filtre->conditions());

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
            'fields' => array(
                'Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'contain' => array(
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'action'))),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
            'order' => $ordre));

        $this->_sortProjetSeanceDate($projets);
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($projets, 'Mes projets validés', array('view', 'generer'));
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
            
            // initialisation des séances
            $listeTypeSeance=array();
            $this->request->data[$i]['listeSeances']=array();
            if (isset($projet['Deliberationseance']) && !empty($projet['Deliberationseance'])) {
                foreach ($projet['Deliberationseance'] as $keySeance => $seance) {
                    $this->request->data[$i]['listeSeances'][]=array('seance_id' => $seance['Seance']['id'],
                                                                    'type_id' => $seance['Seance']['type_id'],
                                                                    'action' => $seance['Seance']['Typeseance']['action'],
                                                                    'libelle' => $seance['Seance']['Typeseance']['libelle'],
                                                                    'date' => $seance['Seance']['date']);
                    $listeTypeSeance[]=$seance['Seance']['type_id'];
                }
            }
            if (isset($projet['Deliberationtypeseance']) && !empty($projet['Deliberationtypeseance'])) {
                foreach ($projet['Deliberationtypeseance'] as $keyType => $typeseance) {
                    if(!in_array($typeseance['Typeseance']['id'], $listeTypeSeance))
                    $this->request->data[$i]['listeSeances'][]=array('seance_id' => NULL,
                                                                    'type_id' => $typeseance['Typeseance']['id'],
                                                                    'action' => $typeseance['Typeseance']['action'],
                                                                    'libelle' => $typeseance['Typeseance']['libelle'],
                                                                    'date' => NULL);
                    
                }
            }
            
            $this->request->data[$i]['listeSeances'] = Hash::sort($this->request->data[$i]['listeSeances'], '{n}.action', 'asc');
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

            if (isset($this->request->data[$i]['listeSeances']) && !empty($this->request->data[$i]['listeSeances']))
                foreach ($this->request->data[$i]['listeSeances'] as $seance) {
                    if($seance['action']===0){
                    $this->request->data[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['type_id'], $projet['Deliberation']['etat']);
                    break;
                    }
            }
            if(!isset($this->request->data[$i]['Modeltemplate']['id'])) {
                $this->request->data[$i]['Modeltemplate']['id'] = $this->Deliberation->Typeacte->getModelId($projet['Deliberation']['typeacte_id'], 'modeleprojet_id');
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
        $this->Deliberation->Behaviors->load('Containable');
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
        $projets = $this->Deliberation->find('all', array('fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'conditions' => $conditions,
            'contain' => array( 'Service'=>array('fields'=>array('libelle')),
                                'Theme'=>array('fields'=>array('libelle')),
                                'Typeacte'=>array('fields'=>array('libelle')),
                                'Circuit'=>array('fields'=>array('nom')),
                                'Deliberationtypeseance'=>array('fields'=>array('id'),
                                                   'Typeseance'=>array('fields'=>array('id','libelle','action'),
                                                                       )),
                                'Deliberationseance'=>array('fields'=>array('id'),
                                                            'Seance'=>array('fields'=>array('id','date','type_id'),
                                        
                                        'Typeseance'=>array('fields'=>array('id','libelle','action'))))),
                  'order' => array($ordre)));
        $this->_sortProjetSeanceDate($projets);
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
        $this->Deliberation->Behaviors->load('Containable');
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
             'contain' => array( 'Service'=>array('fields'=>array('libelle')),
                                'Theme'=>array('fields'=>array('libelle')),
                                'Typeacte'=>array('fields'=>array('libelle')),
                                'Circuit'=>array('fields'=>array('nom')),
                                'Deliberationtypeseance'=>array('fields'=>array('id'),
                                                   'Typeseance'=>array('fields'=>array('id','libelle','action'),
                                                                       )),
                                'Deliberationseance'=>array('fields'=>array('id'),
                                                            'Seance'=>array('fields'=>array('id','date','type_id'),
                                        
                                        'Typeseance'=>array('fields'=>array('id','libelle','action'))))),
                 'order' => array($ordre)));
        $this->_sortProjetSeanceDate($projets);
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
        $this->set('canEditAll', $canEditAll);
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->load('Containable');
        $conditions = $this->_handleConditions($this->Filtre->conditions());
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

            $acte = $this->Deliberation->find('first', array(
                'fields' => array('Deliberation.id', 'Deliberation.objet',
                    'Deliberation.etat', 'Deliberation.signee',
                    'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                    'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                    'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
                'contain' => array('Service' => array('fields' => array('libelle')),
                    'Theme' => array('fields' => array('libelle')),
                    'Typeacte' => array('fields' => array('libelle')),
                    'Circuit' => array('fields' => array('nom')),
                    'Deliberationtypeseance' => array('fields' => array('id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'),
                        )),
                    'Deliberationseance' => array('fields' => array('id'),
                        'Seance' => array('fields' => array('id', 'date', 'type_id'),
                            'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
                'conditions' => $conditions,
                'order' => array('Deliberation.created DESC')));
            $acte['Seances'] = $this->Seance->generateList(null, false, $acte['Deliberation']['typeacte_id']);
            if (!empty($acte['Seances']))
                $delibs[] = $acte;
        }

        $this->_ajouterFiltre($delibs);
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

        $this->Deliberationseance->Behaviors->load('Containable');
        $this->Deliberation->Behaviors->load('Containable');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        if (!isset($conditions['Deliberation.typeacte_id']))
            $conditions['Deliberation.typeacte_id'] = array_keys($this->Session->read('user.Nature'));
        $conditions['Deliberation.etat'] = 2;
//        $conditions['Seance.id'] = $this->Seance->getSeancesDeliberantes();
        $conditions['Deliberation.parent_id'] = null;

        $projets = $this->Deliberationseance->find('all', array('conditions' => $conditions,
            'order' => array('Deliberation.id DESC'),
            'fields' => array(
                'Deliberationseance.deliberation_id',
                'Deliberationseance.seance_id'
            ),
            'contain' => array('Deliberation.id', 'Seance.id')));
        if (!empty($projets))
            foreach ($projets as $projet)
                $projets_id[] = $projet['Deliberationseance']['deliberation_id'];

        $projets = $this->Deliberation->find('all', array('conditions' => array('Deliberation.id' => $projets_id),
            'fields' => array('Deliberation.id', 'Deliberation.objet',
                'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite',
                'Deliberation.anterieure_id', 'Deliberation.num_pref',
                'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id',
                'Deliberation.theme_id', 'Deliberation.service_id'),
              'contain' => array( 'Service'=>array('fields'=>array('libelle')),
                                'Theme'=>array('fields'=>array('libelle')),
                                'Typeacte'=>array('fields'=>array('libelle')),
                                'Circuit'=>array('fields'=>array('nom')),
                                'Deliberationtypeseance'=>array('fields'=>array('id'),
                                                   'Typeseance'=>array('fields'=>array('id','libelle','action'),
                                                                       )),
                                'Deliberationseance'=>array('fields'=>array('id'),
                                                            'Seance'=>array('fields'=>array('id','date','type_id'),
                                        
                                        'Typeseance'=>array('fields'=>array('id','libelle','action'))))),
                  'order' => array('Deliberation.created DESC')));
        $this->_sortProjetSeanceDate($projets);
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
            $Deliberationseances = array();
            foreach ($projets as $projet) {
                if (!empty($projet['Deliberationseance'])) {
                    foreach ($projet['Deliberationseance'] as $Deliberationseance)
                        if(!array_key_exists($Deliberationseance['Seance']['id'], $Deliberationseances))
                        $Deliberationseances[$Deliberationseance['Seance']['id']] =$Deliberationseance['Seance']['Typeseance']['libelle'].' : '.$Deliberationseance['Seance']['date'];
                }
            }
            $this->Filtre->addCritere('DeliberationseanceId', array('field' => 'Deliberationseance.seance_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'toutes',
                    'options' => $Deliberationseances)));
            $typeseances = array();
            foreach ($projets as $projet) {
                if (!empty($projet['Deliberationtypeseance'])) {
                    foreach ($projet['Deliberationtypeseance'] as $typeseance)
                        if(!array_key_exists($typeseance['id'], $typeseances))
                        $typeseances[$typeseance['Typeseance']['id']] = $typeseance['Typeseance']['libelle'];
                }
            }
            $this->Filtre->addCritere('DeliberationtypeseanceId', array(
                'field' => 'Deliberationtypeseance.typeseance_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Type de séance', true),
                    'options' => $typeseances)));

            /*$this->Filtre->addCritere('DeliberationseanceId', array('field' => 'Deliberationseance.seance_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'toutes',
                    'options' => $this->Deliberation->getSeancesFromArray($projets))));*/

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
                'inputOptions' => array(
                    'label' => __('Service émetteur', true),
                    'multiple' => true,
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/service_id', array('/Service/libelle'), '%s'))));

            $this->Filtre->addCritere('CircuitId', array(
                'field' => 'Deliberation.circuit_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Circuit de validation', true),
                    'empty' => 'Tous',
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/circuit_id', array('/Circuit/nom'), ' %s'))));
        }
    }

    function _ajouterFiltreSeance(&$projets)
    {
        if (!$this->Filtre->critereExists()) {
            $this->Filtre->addCritere('Typeacte', array(
                'field' => 'Deliberation.typeacte_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Type d\'acte', true),
                    'empty' => 'tous',
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/typeacte_id', array('/Deliberation/Typeacte/libelle'), '%s'))));
            $this->Filtre->addCritere('ThemeId', array(
                'field' => 'Deliberation.theme_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Thème', true),
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/theme_id', array('/Deliberation/Theme/libelle'), '%s'))));
            $this->Filtre->addCritere('ServiceId', array(
                'field' => 'Deliberation.service_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Service émetteur', true),
                    'multiple' => true,
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/service_id', array('/Deliberation/Service/libelle'), '%s'))));

            $this->Filtre->addCritere('CircuitId', array(
                'field' => 'Deliberation.circuit_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Circuit de validation', true),
                    'empty' => 'Tous',
                    'options' => $this->Utils->listFromArray($projets, '/Deliberation/circuit_id', array('/Deliberation/Circuit/nom'), ' %s'))));
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
            $this->Seance->Behaviors->load('Containable');
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
                $this->Deliberation->saveField('parapheur_etat', 0);
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
            if (!empty($this->data['Deliberation']['service_id'])) {
                $aService_id = array();
                foreach ($this->User->Service->doListId($this->data['Deliberation']['service_id']) as $aService)
                    $aService_id[] = $aService;
                $conditions['Deliberation.service_id'] = $aService_id;
            }
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
                $this->Deliberation->Behaviors->load('Containable');
                $projets = $this->Deliberation->find('all', array(
                     'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                      'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                      'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                      'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
                    'conditions' => $conditions,
                    'contain' => array( 'Service'=>array('fields'=>array('libelle')),
                                'Theme'=>array('fields'=>array('libelle')),
                                'Typeacte'=>array('fields'=>array('libelle')),
                                'Circuit'=>array('fields'=>array('nom')),
                                'Deliberationtypeseance'=>array('fields'=>array('id'),
                                                   'Typeseance'=>array('fields'=>array('id','libelle','action'),
                                                                       )),
                                'Deliberationseance'=>array('fields'=>array('id'),
                                                            'Seance'=>array('fields'=>array('id','date','type_id'),
                                        
                                        'Typeseance'=>array('fields'=>array('id','libelle','action'))))),
                  'order' => array($ordre)));
                    $this->_sortProjetSeanceDate($projets);

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
            if (!empty($this->data['Deliberation']['service_id'])) {
                $aService_id = array();
                foreach ($this->User->Service->doListId($this->data['Deliberation']['service_id']) as $aService)
                    $aService_id[] = $aService;
                $conditions['Deliberation.service_id'] = $aService_id;
            }
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
                    $projet_ids = array();
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
                $this->redirect(array('action'=>'tousLesProjetsRecherche'));
            } else {
                // lecture en base
                $this->Deliberation->Behaviors->load('Containable');
                $projets = $this->Deliberation->find('all', array(
                     'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                      'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                      'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                      'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
                    'contain' => array( 'Service'=>array('fields'=>array('libelle')),
                                        'Theme'=>array('fields'=>array('libelle')),
                                        'Typeacte'=>array('fields'=>array('libelle')),
                                        'Circuit'=>array('fields'=>array('nom')),
                                        'Deliberationtypeseance'=>array('fields'=>array('id'),
                                                           'Typeseance'=>array('fields'=>array('id','libelle','action'),
                                                                               )),
                                        'Deliberationseance'=>array('fields'=>array('id'),
                                                                    'Seance'=>array('fields'=>array('id','date','type_id'),
                                                                                    
                                                                                    'Typeseance'=>array('fields'=>array('id','libelle','action'))))),
                    'conditions' => $conditions,
                    'order' => 'num_delib'
                    ));
               $this->_sortProjetSeanceDate($projets);
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
     * Envoi un/des projet(s) dans le parapheur
     * ATTENTION : Cette méthode n'est pour l'instant opérationnelle que lorsque Pastell est désigné comme parapheur
     * @param null $seance_id
     * @return mixed
     */
    function sendToParapheur($seance_id = null) {
        $this->Filtre->initialisation($this->name . ':' . $this->action . ':' . $seance_id, $this->data, array('url' => $this->here));
        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $this->set('seance_id', $seance_id);
        App::uses('Signature', 'Lib');
        try{
            $this->Signature = new Signature();
        }catch (Exception $e){
            $this->Session->setFlash($e->getMessage(), 'growl');
            $this->redirect($this->referer());
        }
        if (empty($seance_id)) {
            $this->Deliberation->Behaviors->load('Containable');
            $conditions['Deliberation.parapheur_etat != '] = null;
            $conditions['Deliberation.etat >'] = 2;

            $conditions[] = 'Deliberation.id IN ('
                . 'SELECT deliberations_seances.deliberation_id'
                . ' FROM deliberations_seances '
                . ' INNER JOIN seances  ON ( seances.id=deliberations_seances.seance_id )'
                . ' INNER JOIN typeseances ON ( typeseances.id=seances.type_id )'
                . ' INNER JOIN typeactes  ON ( typeactes.id=Deliberation.typeacte_id )'
                . ' WHERE typeseances.action = 0 AND Typeacte.teletransmettre = TRUE'
                . ' )';

            $order = array('Deliberation.num_delib ASC');

            $this->Deliberation->Behaviors->load('Containable');
            $delibs = $this->Deliberation->find('all', array(
                'fields' => array('Deliberation.id',
                    'Deliberation.objet_delib',
                    'Deliberation.num_delib',
                    'Deliberation.titre',
                    'Deliberation.etat',
                    'Deliberation.circuit_id',
                    'Deliberation.parapheur_etat',
                    'Deliberation.signee',
                    'Deliberation.typeacte_id',
                    'Deliberation.theme_id',
                    'Deliberation.service_id'),
                'conditions' => $conditions,
                'contain' => array(
                    'Service.libelle',
                    'Theme.libelle',
                    'Typeacte.libelle',
                    'Circuit.nom',
                    'Deliberationtypeseance' => array(
                        'fields' => array('id'),
                        'Typeseance' => array(
                            'fields' => array('id', 'libelle', 'action'),
                        )
                    ),
                    'Deliberationseance' => array(
                        'fields' => array('id'),
                        'Seance' => array(
                            'fields' => array('id', 'date', 'type_id'),
                            'Typeseance' => array(
                                'fields' => array('id', 'libelle', 'action')
                            )))),
                'order' => array($order)));

            $this->_ajouterFiltre($delibs);

            for ($i = 0; $i < count($delibs); $i++) {
                $delibs[$i]['Modeltemplate']['id'] = $this->Deliberation->getModelId($delibs[$i]['Deliberation']['id']);
            }

            try {
                $circuits = $this->Signature->printCircuits();
                $this->set('deliberations', $delibs);
                $this->set('circuits', $circuits);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->Session->setFlash($message, 'growl', array('type' => 'erreur'));
                return $this->redirect($this->referer());
            }
            return;
        }
        $conditions['Deliberationseance.seance_id'] = $seance_id;
        $conditions['Deliberation.etat >='] = 0;
        // Formulaire non envoyé
        if (!isset($this->data['Parapheur']['circuit_id'])) {
            $this->Deliberationseance->Behaviors->load('Containable');
            $this->Deliberation->Behaviors->load('Containable');
            $delibs = $this->Deliberationseance->find('all', array(
                    'recursive' => 2,
                    'fields' => array(
                        'Deliberationseance.seance_id',
                        'Deliberationseance.deliberation_id',
                        'Deliberationseance.position',
                        'Seance.id',
                        'Seance.type_id',
                        'Deliberation.id',
                        'Deliberation.service_id',
                        'Deliberation.theme_id',
                        'Deliberation.circuit_id',
                        'Deliberation.typeacte_id',
                        'Deliberation.etat',
                        'Deliberation.parapheur_id',
                        'Deliberation.num_pref',
                        'Deliberation.parapheur_etat',
                        'Deliberation.objet_delib',
                        'Deliberation.titre',
                        'Deliberation.num_delib',
                        'Deliberation.parapheur_bordereau',
                        'Deliberation.signee',
                        'Deliberation.signature',
                        'Deliberation.parapheur_commentaire',
                    ),
                    'contain' => array(
                        'Seance',
                        'Deliberation' => array(
                            'Typeacte.nature_id',
                            'Typeacte.libelle',
                            'Service.libelle',
                            'Theme.libelle',
                            'Circuit.nom'
                        )
                    ),
                    'conditions' => $conditions,
                    'order' => 'Deliberationseance.position ASC',
                )
            );
            $typeseance = $this->Seance->getType($seance_id);
            for ($i = 0; $i < count($delibs); $i++) {
                $delibs[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($typeseance, $delibs[$i]['Deliberation']['etat']);
            }

            $this->_ajouterFiltreSeance($delibs);
            try{
                $circuits = $this->Signature->printCircuits();
                $this->set('deliberations', $delibs);
                $this->set('circuits', $circuits);
            }catch (Exception $e){
                $message = $e->getMessage();
                $this->Session->setFlash($message, 'growl', array('type' => 'erreur'));
                return $this->redirect($this->referer());
            }
        } else { // Formulaire envoyé
            $message = '';
            $circuit_id = $this->data['Parapheur']['circuit_id'];
            foreach ($this->data['Deliberation'] as $id => $bool) { // Parcours les checkboxes
                if ($bool == 1) { // Checkbox cochée
                    $delib_id = substr($id, 3, strlen($id));
                    $this->Deliberation->id = $delib_id;
                    if ($circuit_id == -1) { //Signature manuscrite
                        $this->Deliberation->saveField('signee', 1);
                        continue;
                    }
                    $delib = $this->Deliberation->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Deliberation.id' => $delib_id),
                        'fields' => array(
                            'Deliberation.id',
                            'Deliberation.num_delib',
                            'Deliberation.delib_pdf',
                            'Deliberation.etat',
                            'Deliberation.parapheur_etat',
                            'Deliberation.objet_delib',
                            'Deliberation.objet',
                            'Deliberation.signee',
                            'Deliberation.typeacte_id',
                        )
                    ));

                    $this->Deliberation->Typeacte->id = $delib['Deliberation']['typeacte_id'];
                    $delib['Typeacte']['nature_id'] = $this->Deliberation->Typeacte->field('nature_id');

                    $this->Deliberation->Seance->id = $seance_id;
                    $delib['Seance']['date'] = $this->Deliberation->Seance->field('date');

                    if (empty($delib['Deliberation']['delib_pdf'])){
                        $model_id = $this->Deliberation->getModelId($delib_id);
                        //FIXME changer appel génération document
                        $this->requestAction(array('controller'=>'models', 'action'=>'generer', $delib_id, 'null', $model_id, '0', '1', 'acte'));
                        $filename = WEBROOT_PATH . "/files/generee/fd/null/$delib_id/acte.pdf";
                        $delib['Deliberation']['delib_pdf'] = file_get_contents($filename);
                        $this->Deliberation->saveField('delib_pdf', $delib['Deliberation']['delib_pdf']);
                    }

                    $annexes = $this->Annex->getAnnexesFromDelibId($delib_id, true);
                    $ret = $this->Signature->send($delib, $circuit_id, $annexes);
                    if ($ret) {
                        $this->Deliberation->saveField('parapheur_id', $ret);
                        $this->Deliberation->saveField('parapheur_cible', Configure::read('PARAPHEUR'));
                        if (Configure::read('PARAPHEUR') == 'PASTELL')
                            $this->Deliberation->saveField('pastell_id', $ret);
                        $this->Deliberation->saveField('parapheur_etat', '1');
                        $message = $message . $delib['Deliberation']['num_delib'] . " : Envoyé avec succès<br />";
                    } else {
                        $message = $message . $delib['Deliberation']['num_delib'] . " : Erreur<br />";
                    }
                }
            }
            $this->Session->setFlash($message, 'growl', array('type' => 'erreurTDT'));
            return $this->redirect($this->referer());
        }
    }

    function verserAsalae()
    {
        require_once(ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'pcltar' . DS . 'pcltar.lib.php');
        if (empty($this->data)) {
            $this->Deliberation->Behaviors->load('Containable');
            $this->paginate = array('conditions' => array('Deliberation.etat' => 5),
                'fields' => array('Deliberation.id', 'Deliberation.objet_delib', 'Deliberation.titre',
                    'Deliberation.num_delib', 'sae_etat'),
                'contain' => array('Service.libelle', 'Theme.libelle'),
                'limit' => 20);

            $delibs = $this->Paginator->paginate('Deliberation');
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
                        'TransferIdentifier' => Configure::read('ASALAE_LOGIN') . '_' . $delib['Deliberation']['num_delib'],
                        'Comment' => $delib['Deliberation']['objet_delib'],
                        'Date' => date('c'),
                        'TransferringAgency' => array('Identification' => Configure::read('ASALAE_LOGIN')),
                        'ArchivalAgency' => array('Identification' => Configure::read('ASALAE_SIREN_ARCHIVE')),
                        'Contains' => array(
                            'ArchivalAgreement' => Configure::read('ASALAE_NUMERO_AGREMENT'),
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
                                'OriginatingAgency' => array('Identification' => Configure::read('ASALAE_LOGIN')),
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
                        Configure::read('ASALAE_LOGIN'),
                        Configure::read('ASALAE_PWD')));
                    $ret = $client->__soapCall("wsDepot", array("bordereau.xml",
                        base64_encode($seda),
                        "versement.tgz",
                        base64_encode($document), 'TARGZ',
                        Configure::read('ASALAE_LOGIN'),
                        Configure::read('ASALAE_PWD')));
                    // Changement d'état de la délibération
                    if ($ret == 0) {
                        $this->Session->setFlash("Les documents ont été transférés à AS@LAE", 'growl');
                        $this->Deliberation->id = $delib_id;
                        $this->Deliberation->saveField('sae_etat', 1);
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
            return $this->redirect($this->referer());

        if (empty($this->data)) {
            $etapes = $this->Traitement->listeEtapes($delib['Deliberation']['id'], array('selection' => 'APRES'));
            if (empty($etapes)) {
                $this->Session->setFlash("Le projet n'a pas d'étape suivante", 'growl', array('type' => 'erreur'));
                return $this->redirect($this->referer());
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

            $destinataires = $this->Traitement->whoIs($delib_id);
            foreach ($destinataires as $destinataire_id)
                $this->User->notifier($delib_id, $destinataire_id, 'traitement');

            $this->Historique->enregistre($delib_id, $this->Session->read('user.User.id'), "Le projet a sauté l'étape  ");
            $this->Session->setFlash("Le projet est maintenant à l'étape suivante ", 'growl');
            return $this->redirect(array('action'=>'tousLesProjetsValidation'));
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

            $this->Traitement->execute($action, $this->user_id, $delib_id, $options);
            $this->Historique->enregistre($delib_id, $this->user_id, "Le projet a été envoyé à $destinataire $action_com");
            $this->User->notifier($delib_id, $this->data['Insert']['user_id'], 'traitement');

            return $this->redirect('/');
        }
    }

    function refreshSignature()
    {
        App::uses('Signature', 'Lib');
        $Signature = new Signature;
        $ret = $Signature->updateAll();
        $ret = trim(preg_replace('/\s+/', ' ', nl2br(htmlspecialchars($ret,ENT_QUOTES))));
        $this->Session->setFlash($ret, 'growl', array());
        return $this->redirect($this->referer());
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
            'Deliberation.parapheur_etat',
            'Deliberation.theme_id',
            'Deliberation.circuit_id',
            'Deliberation.num_pref',
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
        for ($i = 0; $i < count($actes); $i++) {
            $actes[$i]['Deliberation'][$actes[$i]['Deliberation']['id'] . '_num_pref'] = $actes[$i]['Deliberation']['num_pref'];
            $actes[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($actes[$i]['Deliberation']['num_pref']);
        }
        $this->_ajouterFiltre($actes);
        $this->Filtre->delCritere('DeliberationseanceId');
        $this->Filtre->delCritere('DeliberationtypeseanceId');
        $this->set('canGoNext', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:goNext"));
        $this->set('peuxValiderEnUrgence', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:validerEnUrgence"));
        $this->set('actes', $actes);

        $this->render('autres_actes');
    }

    function autreActesValides()
    {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('titreVue', 'Autres actes validés');

        if (Configure::read('USE_PARAPHEUR')) {
            App::uses('Signature', 'Lib');
            $this->Signature = new Signature;
            $circuits = $this->Signature->printCircuits();
        }

        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = array('2', '3', '4');
        $conditions['OR'] = array('Deliberation.signee IS NULL', 'Deliberation.signee'=>0);

        $fields = array(
            'Deliberation.id',
            'Deliberation.num_delib',
            'Deliberation.num_pref',
            'Deliberation.objet',
            'Deliberation.titre',
            'Deliberation.etat',
            'Deliberation.signee',
            'Deliberation.parapheur_etat',
            'Deliberation.typeacte_id',
            'Deliberation.theme_id',
            'Deliberation.service_id',
            'Deliberation.circuit_id',
        );
        $contain = array(
            'Typeacte.libelle',
            'Typeacte.modeleprojet_id',
            'Typeacte.modelefinal_id',
            'Typeacte.nature_id',
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
        $this->_ajouterFiltre($actes);

        $userId = $this->Session->read('user.User.id');
        $editerTous = $this->Droits->check($userId, "Deliberations:editerTous");

        for ($i = 0; $i < count($actes); $i++) {
            $actes[$i]['Deliberation'][$actes[$i]['Deliberation']['id'] . '_num_pref'] = $actes[$i]['Deliberation']['num_pref'];
            $actes[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($actes[$i]['Deliberation']['num_pref']);
        }

        $this->set('canEdit', $editerTous);
        $this->set('actes', $actes);
        $this->set('circuits', $circuits);
        $this->render('autres_actes');
    }

    function sendActesToSignature()
    {
        if (!Configure::read('USE_PARAPHEUR') && $this->data['Parapheur']['circuit_id'] != -1){
            $this->Session->setFlash('Parapheur désactivé', 'growl');
            return $this->redirect($this->referer());
        }

        $circuits = array();
        if ($this->data['Parapheur']['circuit_id'] != -1) {
            App::uses('Signature', 'Lib');
            $this->Signature = new Signature;
            $circuits = $this->Signature->listCircuits();
        }
        $circuits['-1'] = 'Signature manuscrite';
        $this->Deliberation->Behaviors->load('Containable');
        foreach ($this->data['Deliberation'] as $tmp_id => $bool) {
            if ($bool) {
                $acte_id = substr($tmp_id, 3, strlen($tmp_id));

                $acte = $this->Deliberation->find('first', array(
                    'conditions' => array('Deliberation.id' => $acte_id),
                    'contain' => array('Typeacte.compteur_id', 'Typeacte.nature_id')
                ));

                $model_id = $this->Deliberation->getModelId($acte['Deliberation']['id']);
                //FIXME changer appel génération document
                $this->requestAction(array('controller'=>'models', 'action'=>'generer', $acte_id, 'null', $model_id, '0', '1', 'acte'));
                $filename = WEBROOT_PATH . "/files/generee/fd/null/$acte_id/acte.pdf";

                $content = file_get_contents($filename);
                $acte['Deliberation']['delib_pdf'] = $content;
                $num = $this->Seance->Typeseance->Compteur->genereCompteur($acte['Typeacte']['compteur_id']);
                if ($this->data['Parapheur']['circuit_id'] == -1) {
                    $acte['Deliberation']['signee'] = 1;
                    $acte['Deliberation']['etat'] = 3;
                    $acte['Deliberation']['date_envoi_signature'] = date("Y-m-d H:i:s", strtotime("now"));
                    $acte['Deliberation']['num_delib'] = $num;
                    $acte['Deliberation']['date_acte'] = date("Y-m-d H:i:s", strtotime("now"));
                    $this->Deliberation->save($acte);
                    $this->Historique->enregistre($acte_id, $this->user_id, "Signature manuscrite");
                } else {
                    $annexes = $this->Annex->getAnnexesFromDelibId($acte_id, true);
                    $ret = $this->Signature->send($acte, $this->data['Parapheur']['circuit_id'], $annexes);
                    if ($ret !== false) {
                        $acte['Deliberation']['parapheur_etat'] = 1;
                        $acte['Deliberation']['parapheur_cible'] = Configure::read('PARAPHEUR');
                        if (Configure::read('PARAPHEUR') == 'PASTELL')
                            $acte['Deliberation']['pastell_id'] = $ret;
                        $acte['Deliberation']['etat'] = 3;
                        $acte['Deliberation']['date_envoi_signature'] = date("Y-m-d H:i:s", strtotime("now"));
                        $acte['Deliberation']['num_delib'] = $num;
                        $acte['Deliberation']['date_acte'] = date("Y-m-d H:i:s", strtotime("now"));
                        $acte['Deliberation']['parapheur_id'] = $ret;
                        $this->Deliberation->save($acte);
                        $this->Historique->enregistre($acte_id, $this->user_id, "Envoi au parapheur pour signature");
                    } else {
                        $this->Session->setFlash("Erreur lors de l'envoi au parapheur. Pour plus d'informations, consultez le fichier parapheur.log", 'growl', array('type' => 'erreur'));
                    }
                }
            }
        }
        return $this->redirect(array('action'=>'autreActesValides'));
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
            'Deliberation.service_id',
            'Deliberation.circuit_id',
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

        $actes = $this->Deliberation->getActesATeletransmettre($conditions, $fields, $contain, $order);

        $this->_ajouterFiltre($actes);

        for ($i = 0; $i < count($actes); $i++) {
            $actes[$i]['Deliberation'][$actes[$i]['Deliberation']['id'] . '_num_pref'] = $actes[$i]['Deliberation']['num_pref'];
            $actes[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($actes[$i]['Deliberation']['num_pref']);
        }

        $this->set('deliberations', $actes);
        $this->set('dateClassification', $this->S2low->getDateClassification());

        $this->render('to_send');
    }

    public function nonTransmis()
    {
        $typeacte_ids = $this->Deliberation->Typeacte->find('all', array(
            'recursive' => -1,
            'conditions' => array('Typeacte.teletransmettre' => false),
            'fields' => array('Typeacte.id')));
        $this->Deliberation->Behaviors->load('Containable');
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
            'Deliberation.tdt_dateAR',
            'Deliberation.typeacte_id',
            'Deliberation.date_acte',
            'Deliberation.theme_id',
            'Deliberation.circuit_id',
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

        $this->_ajouterFiltre($deliberations);

        $this->set('dateClassification', $this->S2low->getDateClassification());
        if (Configure::read('TDT') == 'S2LOW'){
            for ($i = 0; $i < count($deliberations); $i++) {
                if (!empty($deliberations[$i]['Deliberation']['tdt_id'])) {
                    $flux = $this->S2low->getFluxRetour($deliberations[$i]['Deliberation']['tdt_id']);
                    $codeRetour = substr($flux, 3, 1);
                    $deliberations[$i]['Deliberation']['code_retour'] = $codeRetour;
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
        $this->Seance->Behaviors->load('Containable');
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
        return $this->redirect(array('controller' => 'seances', 'action' => 'voter', $delib_id, $seance_id));
    }

    function traitementLot()
    {
        $ids = array();
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
            return $this->redirect($redirect);
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
        return $this->redirect($redirect);
    }

    function quicksearch() {
        $field = trim($this->request->data['User']['search']);
        if (empty($field) OR (ctype_digit($field) XOR strlen($field)<4)) {
            $this->Session->setFlash('Vous devez saisir au moins un mot. (plus de 3 caractères)', 'growl',array('type' => 'erreur'));
            return $this->redirect($this->referer());
        }
        $conditions = array();
        $userId = $this->Session->read('user.User.id');
        if (!$this->Droits->check($userId, 'Deliberations:tousLesProjetsRecherche')) {
            $listeCircuits = explode(',', $this->Circuit->listeCircuitsParUtilisateur($userId));
            if (!empty($listeCircuits))
                $conditions['AND']['OR']['Deliberation.circuit_id'] = $listeCircuits;
            $conditions['AND']['OR']['Deliberation.redacteur_id'] = $userId;
       
            //Récupère la liste des délib que l'utilisateur a visé (résolution bug changement circuit non visible)
            $listeDelibsParticipe = explode(',', $this->Traitement->getListTargetByTrigger($userId));
            if (!empty($listeDelibsParticipe))
                $conditions['OR']['Deliberation.id'] = $listeDelibsParticipe;
         }
                
        if (ctype_digit($field)) {
            $conditions['OR']['Deliberation.id'][] = $field;
        }
        $conditions['OR']['Deliberation.objet ILIKE'] = "%$field%";
        $conditions['OR']['Deliberation.titre ILIKE'] = "%$field%";

        $ordre = 'Deliberation.created DESC';
        $this->Deliberation->Behaviors->load('Containable');
        $projets = $this->Deliberation->find('all', array(
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'conditions' => $conditions,
             'contain' => array( 'Service'=>array('fields'=>array('libelle')),
                                'Theme'=>array('fields'=>array('libelle')),
                                'Typeacte'=>array('fields'=>array('libelle')),
                                'Circuit'=>array('fields'=>array('nom')),
                                'Deliberationtypeseance'=>array('fields'=>array('id'),
                                                   'Typeseance'=>array('fields'=>array('id','libelle','action'),
                                                                       )),
                                'Deliberationseance'=>array('fields'=>array('id'),
                                                            'Seance'=>array('fields'=>array('id','date','type_id'),
                                        
                                        'Typeseance'=>array('fields'=>array('id','libelle','action'))))),
            'order' => $ordre));
        $this->_sortProjetSeanceDate($projets);
        $this->_afficheProjets($projets, 'R&eacute;sultat de la recherche parmi mes projets', array('view', 'generer'), array());
    }



    public function majArTdt($id=null){
        if (empty($id)){
            $this->Deliberation->majArAll();
            $this->Session->setFlash('Mise à jour des accusés de réception effectuée.', 'growl');
        }else{
            if ($ar = $this->Deliberation->majAr($id)){
                $this->Session->setFlash("Dossier reçu par le tdt le $ar.", 'growl');
            }else{
                $this->Session->setFlash("Impossible de contacter le TDT.", 'growl');
            }
        }
        return $this->redirect($this->previous);
    }


    public function majEchangesTdt($id = null){
        if (empty($id)){
            $this->Deliberation->majEchangesTdtAll();
        }else{
            $this->Deliberation->majEchangesTdt($id);
        }
        $this->Session->setFlash('Mise à jour des échanges avec le TDT effectuée.', 'growl');
        return $this->redirect($this->previous);
    }

    function downloadTdtMessage($message_id = null) {
        if (empty($message_id)){
            $this->Session->setFlash('Merci d\indiquer l\'identifiant du message.', 'growl');
            return $this->redirect($this->previous);
        }

        $data = $this->Deliberation->TdtMessage->find('first', array(
            'fields' => array('data'),
            'conditions' => array('message_id' => $message_id)
        ));

        if(empty($data['TdtMessage']['data'])){
            $this->Session->setFlash('Message introuvable en base de données.', 'growl');
            return $this->redirect($this->previous);
        }

        parent::sendNoCacheHeaders();
        header('Content-type: application/x-gzip');
        header('Content-Length: ' . strlen($data['TdtMessage']['data']));
        header('Content-Disposition: attachment; filename=tdt_message_'.$message_id.'.tar.gz');
        echo $data['TdtMessage']['data'];
        exit;
    }

    function getTampon($delib_id) {

        $delib = $this->Deliberation->find('first', array(
            'conditions' => array('id' => $delib_id),
            'fields' => array('num_delib','tdt_id','tdt_data_pdf', 'pastell_id'),
            'recursive' => -1
        ));

        if(empty($delib['Deliberation']['tdt_data_pdf'])){
            App::uses('Tdt', 'Lib');
            $Tdt = new Tdt;
            $tdt_id = Configure::read('TDT') == 'PASTELL' ? $delib['Deliberation']['pastell_id'] : $delib['Deliberation']['tdt_id'];
            $tampon = $Tdt->getTampon($tdt_id);
            if (!empty($tampon)){
                $delib['Deliberation']['tdt_data_pdf'] = $tampon;
                $this->Deliberation->id = $delib_id;
                $this->Deliberation->saveField('tdt_data_pdf', $tampon);
            } else {
                $this->Session->setFlash('Erreur lors de la récupération de l\'acte tamponné', 'growl');
                return $this->referer($this->previous);
            }
        }

        parent::sendNoCacheHeaders();
        header('Content-type: application/pdf');
        header('Content-Length: ' . strlen($delib['Deliberation']['tdt_data_pdf']));
        header('Content-Disposition: attachment; filename=Acte_'.$delib['Deliberation']['num_delib'].'.pdf');
        echo $delib['Deliberation']['tdt_data_pdf'];
        exit;
    }

    function getBordereauTdt($delib_id) {

        $delib = $this->Deliberation->find('first', array(
            'conditions' => array('id' => $delib_id),
            'fields' => array('num_delib','tdt_id','tdt_data_bordereau_pdf', 'pastell_id'),
            'recursive' => -1
        ));

        if(empty($delib['Deliberation']['tdt_data_bordereau_pdf'])){
            App::uses('Tdt', 'Lib');
            $Tdt = new Tdt;
            $tdt_id = Configure::read('TDT') == 'PASTELL' ? $delib['Deliberation']['pastell_id'] : $delib['Deliberation']['tdt_id'];
            $bordereau = $Tdt->getBordereau($tdt_id);
            if (!empty($bordereau)){
                $delib['Deliberation']['tdt_data_bordereau_pdf'] = $bordereau;
                $this->Deliberation->id = $delib_id;
                $this->Deliberation->saveField('tdt_data_bordereau_pdf', $bordereau);
            } else {
                $this->Session->setFlash('Erreur : Impossible de récupérer le bordereau.', 'growl');
                return $this->referer($this->previous);
            }
        }

        parent::sendNoCacheHeaders();
        header('Content-type: application/pdf');
        header('Content-Length: ' . strlen($delib['Deliberation']['tdt_data_bordereau_pdf']));
        header('Content-Disposition: attachment; filename=Acte_'.$delib['Deliberation']['num_delib'].'_bordereau_tdt.pdf');
        echo $delib['Deliberation']['tdt_data_bordereau_pdf'];
        exit;
    }

    /**
     * fonction de fusion d'un projet ou d'une délibération avec envoi du résultat vers le client
     * @param integer $id id du projet ou de la délibération
     * @param integer $cookieToken numéro de cookie du client pour masquer la fenêtre attendable
     * @return CakeResponse
     */
    function genereFusionToClient($id, $cookieToken = null) {
        try {
            // vérification de l'existence du projet/délibération en base de données
            if (!$this->Deliberation->hasAny(array('id' => $id)))
                throw new Exception('Projet/délibération id:' . $id . ' non trouvé(e) en base de données');

            // fusion du document
            $this->Deliberation->Behaviors->load('OdtFusion', array('id' => $id));
            $filename = $this->Deliberation->fusionName();
            $this->Deliberation->odtFusion();

            // selon le format d'envoi du document (pdf ou odt)
            if ($this->Session->read('user.format.sortie') == 0) {
                $mimeType = "application/pdf";
                $filename = $filename . '.pdf';
                $content = $this->Conversion->convertirFlux($this->Deliberation->odtFusionResult->content->binary, 'odt', 'pdf');
            } else {
                $mimeType = "application/vnd.oasis.opendocument.text";
                $filename = $filename . '.odt';
                $content = $this->Conversion->convertirFlux($this->Deliberation->odtFusionResult->content->binary, 'odt', 'odt');
            }
            unset($this->Deliberation->odtFusionResult->content->binary);

            // envoi au client
            $this->Cookie->destroy();
            $this->Cookie->write('downloadToken', $cookieToken, false, 3600);
            $this->response->disableCache();
            $this->response->body($content);
            $this->response->type($mimeType);
            $this->response->download($filename);
            return $this->response;
        } catch (Exception $e) {
            $this->Session->setFlash('erreur lors de la génération du document : ' . $e->getMessage(), 'growl', array('type' => 'erreur'));
            $this->redirect($this->referer());
        }
    }

    public function beforeFilter() {
        parent::beforeFilter();
        //Pour la fonction generer réglage du cookie
        $this->Cookie->name = 'Generer';
        $this->Cookie->time = 3600;  // ou '1 hour'
        $this->Cookie->path = '/';
        $this->Cookie->domain = $_SERVER["HTTP_HOST"];
        $this->Cookie->secure = false;  // HTTPS sécurisé seulement (NON)
        $this->Cookie->httpOnly = false; // Pour accès javascript
    }
}
