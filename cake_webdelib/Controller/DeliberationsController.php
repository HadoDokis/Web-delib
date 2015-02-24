<?php

/**
 * === Deliberation.etat ===
 * Deliberation.etat = -1 : Refusé
 * Deliberation.etat = 0 : En cours de rédaction
 * Deliberation.etat = 1 : Dans un circuit
 * Deliberation.etat = 2 : Validé
 * Deliberation.etat = 3 : Adopté (Voté pour)
 * Deliberation.etat = 4 : Rejeté (Voté contre)
 * Deliberation.etat = 5 : Envoyé au TDT
 *
 * === Deliberation.avis ===
 * Deliberation.avis = 0 ou null : Pas d'avis donné
 * Deliberation.avis = 1 : Avis favorable
 * Deliberation.avis = 2 : Avis défavorable
 */

/**
 * Class DeliberationsController
 * @property Deliberation $Deliberation
 */
class DeliberationsController extends AppController {

    public $helpers = array('Fck');
    public $uses = array('Acteur', 'Deliberation', 'User', 'Annex', 'Typeseance', 'Seance', 'TypeSeance', 'Commentaire', 'ModelOdtValidator.Modeltemplate', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Infosupdef', 'Infosup', 'Historique', 'Cakeflow.Circuit', 'Cakeflow.Composition', 'Cakeflow.Etape', 'Cakeflow.Traitement', 'Cakeflow.Visa', 'Nomenclature', 'Deliberationseance', 'Deliberationtypeseance');
    public $components = array('ModelOdtValidator.Fido', 'Gedooo', 'Date', 'Email', 'Acl'/*, 'Droits'*/, 'Iparapheur', 'Filtre', 'Cmis', 'Progress', 'Conversion', 'S2low', 'Paginator',
        'Auth' => array(
            'mapActions' => array(
                'read' => array('view','download','downloadDelib',
                    'getTypeseancesParTypeacteAjax','quicksearch', 'genereFusionToClient',
                    'textsynthesevue','deliberationvue','textprojetvue'),
                'create' => array('add'),
                'update'=> array('edit','admin_add'),
                'delete'=> array('delete','admin_add'),
                
                'mesProjetsRedaction' => array('attribuercircuit'),
                'mesProjetsRedaction' => array('addIntoCircuit'),
                'mesProjetsATraiter' => array('traiter'),
                'mesProjetsATraiter' => array('retour'),
                'tousLesProjetsSansSeance' => array('attribuerSeance'),
                'autresActesAValider' => array('autreActesValides'),
                'autresActesAValider' => array('autresActesAEnvoyer'),
                'autresActesAValider' => array('autresActesEnvoyes'),
                'sendActesToSignature' => array('sendToParapheur','refreshSignature','majEtatParapheur',
                    'downloadSignature','downloadBordereau'),
                'sendToTdt' => array('getTampon','getClassification','getBordereauTdt','transmit',
                    'downloadTdtMessage','majEchangesTdt','majArTdt','classification'),
                
            )
        )
        );
    /*public $libelleControleurDroit = 'Projets';
    public $libellesActionsDroit = array(
        'edit' => "Modification d'un projet",
        'delete' => "Suppression d'un projet",
        'goNext' => 'Sauter une étape',
        'validerEnUrgence' => 'Valider un projet en urgence',
        'rebond' => 'Effectuer un rebond',
        'sendToParapheur' => 'Envoie à la signature',
        'editerTous' => 'Editer tous les projets',
    );*/

    function view($id = null) {
        $projet = $this->Deliberation->find('first', array(
            'fields' => array(
                'id', 'anterieure_id', 'service_id', 'circuit_id', 'typeacte_id',
                'etat', 'num_delib', 'titre', 'objet', 'objet_delib', 'num_pref', 'parent_id',
                'texte_projet_name', 'texte_synthese_name', 'deliberation_name',
                'Deliberation.created', 'Deliberation.modified', 'deliberation', 'texte_projet', 'texte_synthese'),
            'contain' => array(
                'User' => array('id','nom','prenom'),
                'Multidelib' => array(
                    'fields' => array('id', 'objet', 'objet_delib', 'num_delib', 'etat', 'deliberation', 'deliberation_name'),
                    'Annex' => array('fields' => array('id', 'titre', 'joindre_ctrl_legalite', 'filename'))
                ),
                'Redacteur' => array('fields' => array('id', 'nom', 'prenom')),
                'Rapporteur' => array('fields' => array('id', 'nom', 'prenom')),
                'Infosup',
                'Annex' => array('fields' => array('id', 'titre', 'joindre_ctrl_legalite', 'filename')),
                'Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'action'))),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'))
                    ))),
            'conditions' => array('Deliberation.id' => $id),
            'recursive' => -1
        ));

        if (empty($projet)) {
            $this->Session->setFlash("Le projet n&deg;$id est introuvable !", 'growl');
            return $this->redirect($this->previous);
        }

        $projet['Deliberationseance'] = Hash::sort($projet['Deliberationseance'], '{n}.Seance.Typeseance.action', 'asc');

        $projet['Deliberation']['num_pref'] = $projet['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($projet['Deliberation']['num_pref']);

        if (!$this->Droits->check($this->user_id, "Pages:tous_les_projets")) {
            $conditions['Deliberation.id'] = $id;
            $conditions['OR']['redacteur_id'] = $this->user_id;

            if ($this->Droits->check($this->user_id, "Deliberations:projetsMonService")) {
                $services = array();
                $conditions['Deliberation.id'] = $id;
                $conditions['OR']['redacteur_id'] = $this->user_id;
                $user = $this->Deliberation->find('first', array(
                'conditions' => array('Deliberation.id' => $id),
                'contain' => array('User' => array('conditions' => array('User.id' => $this->user_id))),
                'fields' => array('Deliberation.redacteur_id'),
                'recursive' => -1));
        
                if(!empty($user['User'])){
                    $conditions['OR']['redacteur_id'] = $user['Deliberation']['redacteur_id'];
                }
                $this->User->Behaviors->load('Containable');
                $user_services = $this->User->find('first', array('conditions' => array('User.id' => $this->user_id),
                    'fields' => array('User.id'),
                    'contain' => array('Service.id')));
                foreach ($user_services['Service'] as $service)
                    $services[] = $service['id'];

                $conditions['OR']['service_id'] = $services;
            }
            $acte = $this->Deliberation->find('first', array(
                'conditions' => $conditions,
                'fields' => array('Deliberation.id'),
                'recursive' => -1));
            
            if(!empty($user['User'])){
                $estDansCircuit = $this->Traitement->triggerDansTraitementCible($user['Deliberation']['redacteur_id'], $id);
            }else{
                $estDansCircuit = $this->Traitement->triggerDansTraitementCible($this->user_id, $id);
            }

            if (empty($acte) && ($estDansCircuit == false)) {
                $this->Session->setFlash("Vous n'avez pas les droits pour visualiser cet acte", 'growl');
                return $this->redirect(array('action' => 'mesProjetsRedaction'));
            }
        }

        // Compactage des informations supplémentaires
        if (!empty($projet['Infosup']))
            $this->set('infosupdefs', $projet['Infosup'] = $this->Deliberation->Infosup->compacte($projet['Infosup'], false));
        // Lecture des versions anterieures
        if (!empty($projet['Deliberation']['anterieure_id']))
            $this->set('tab_anterieure', $this->Deliberation->chercherVersionAnterieure($projet['Deliberation']['anterieure_id'], 0, array(), 'view'));

        //Lecture de la version supérieure
        $this->set('versionsup', $this->Deliberation->chercherVersionSuivante($id));

        $this->set('userCanEdit', $this->Droits->check($this->user_id, "Deliberations:edit") && $this->Deliberation->estModifiable($id, $this->user_id, $this->Droits->check($this->user_id, "Deliberations:editerTous")));

        $this->set('userCanadd', $this->Droits->check($this->user_id, "Deliberations:add"));
        
        $this->set('inBannette', in_array($this->user_id, $this->Traitement->whoIs($id, 'current', 'RI', false)));

        $this->set('commentaires', $this->Commentaire->find('all', array(
                    'fields' => array('Commentaire.texte', 'Commentaire.created'),
                    'contain' => array('User.nom', 'User.prenom'),
                    'conditions' => array(
                        'Commentaire.delib_id' => $id,
                        'Commentaire.pris_en_compte' => 0
                    ),
                    'order' => array('Commentaire.created ASC'),
                    'recursive' => -1
        )));
        $this->set('historiques', $this->Historique->find('all', array(
                    'fields' => array('Historique.commentaire', 'Historique.created'),
                    'contain' => array('User.nom', 'User.prenom'),
                    'conditions' => array('Historique.delib_id' => $id),
                    //'joins' => array($this->Historique->join('User',array( 'type' => 'INNER' ))),
                    'order' => array('Historique.modified DESC'),
                    'recursive' => -1
        )));

        //Récupération du model_id (pour lien bouton generer)
        $model_id = $this->Deliberation->getModelId($id);
        $projet['Modeltemplate']['id'] = $model_id;


        // Mise en forme des données du projet ou de la délibération
        $projet['Deliberation']['libelleEtat'] = $this->Deliberation->libelleEtat($projet['Deliberation']['etat']);
        /* if (!empty($this->data['Seance']['date']))
          $projet['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($projet['Seance']['date'])); */
        // initialisation des séances
        $listeTypeSeance = array();
        $projet['listeSeances'] = array();
        if (isset($projet['Deliberationseance']) && !empty($projet['Deliberationseance'])) {
            foreach ($projet['Deliberationseance'] as $seance) {
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
            foreach ($this->request->data['Deliberationtypeseance'] as $typeseance) {
                if (!in_array($typeseance['Typeseance']['id'], $listeTypeSeance))
                    $this->request->data['listeSeances'][] = array(
                        'seance_id' => NULL,
                        'type_id' => $typeseance['Typeseance']['id'],
                        'action' => $typeseance['Typeseance']['action'],
                        'libelle' => $typeseance['Typeseance']['libelle'],
                        'date' => NULL);
            }
        }
        $projet['listeSeances'] = Hash::sort($projet['listeSeances'], '{n}.action', 'asc');

        $projet['Service']['libelle'] = $this->Deliberation->Service->doList($projet['Deliberation']['service_id']);
        $projet['Circuit']['libelle'] = $this->Circuit->getLibelle($projet['Deliberation']['circuit_id']);

        // Définitions des infosup
        $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('model' => 'Deliberation', 'actif' => true),
                    'order' => 'ordre')));

        $target_id = empty($this->request->data['Deliberation']['parent_id']) ? $id : $this->request->data['Deliberation']['parent_id'];

        //Test si le projet a été inséré dans un circuit, si oui charger l'affichage
        $wkf_exist = $this->Traitement->find('count', array('recursive' => -1, 'conditions' => array('target_id' => $target_id)));
        if (!empty($wkf_exist))
            $this->set('visu', $this->requestAction(array('plugin' => 'cakeflow',
                        'controller' => 'traitements',
                        'action' => 'visuTraitement', $target_id), array('return')));
        else
            $this->set('visu', null);
        
        $this->set('projet', $projet);


        /*         * *************************** */
        //si bloqué à une étape de délégation
        /* $visa = false;
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
          $this->set('majDeleg', $visa); */
        /*         * *************************** */
    }

    function majEtatParapheur($id = null) {
        $this->requestAction(array('plugin' => 'cakeflow', 'controller' => 'traitements', 'action' => 'majTraitementsParapheur', $id, 'true'));
        return $this->redirect(array('action' => 'view', $id));
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
        $canEditAll = $this->Droits->check($this->user_id, "Deliberations:editerTous");

        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        if (Configure::read('TDT') == 'PASTELL' && Configure::read('USE_PASTELL') && Configure::read('USE_TDT')) {
            App::uses('Tdt', 'Lib');
            $Tdt = new Tdt();
            $res = $Tdt->listClassification();
            $this->set('nomenclatures', $res);
        }

        if ($this->request->isPost()) {
            $success = true;
            $this->Deliberation->begin();
            $this->request->data['Deliberation']['redacteur_id'] = $this->user_id;
            $this->request->data['Deliberation']['service_id'] = $user['User']['service'];
            if (empty($this->data['Deliberation']['objet_delib']))
                $this->request->data['Deliberation']['objet_delib'] = $this->data['Deliberation']['objet'];

            if (!empty($this->request->data['Deliberation']['date_limite'])) {
                App::uses('CakeTime', 'Utility');
                $this->request->data['Deliberation']['date_limite'] = CakeTime::format($this->data['Deliberation']['date_limite'], '%Y-%m-%d 00:00:00');
            }
            $this->Deliberation->unbindModel(array('hasAndBelongsToMany' => array('Seance')));
            // Si on definit une seance a une delib, on la place en derniere position de la seance
            if (isset($this->data['Seance'])) {
                if (!$this->Deliberation->canSaveSeances($this->data['Seance']['Seance'])) {
                    $this->Session->setFlash("Vous ne pouvez enregistrer une seule séance délibérante", 'growl', array('type' => 'erreur'));
                    return $this->redirect(array('action' => 'add'));
                }
            }

            //gabarits pour ce type d'acte ?
            $typeacte = $this->Deliberation->Typeacte->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $this->data['Deliberation']['typeacte_id'])
            ));

            if (!empty($typeacte['Typeacte']['gabarit_projet'])) {
                $this->request->data['Deliberation']['texte_projet'] = $typeacte['Typeacte']['gabarit_projet'];
                $this->request->data['Deliberation']['texte_projet_name'] = $typeacte['Typeacte']['gabarit_projet_name'];
                $this->request->data['Deliberation']['texte_projet_size'] = strlen($typeacte['Typeacte']['gabarit_projet']);
                $this->request->data['Deliberation']['texte_projet_type'] = 'application/vnd.oasis.opendocument.text';
            }
            if (!empty($typeacte['Typeacte']['gabarit_synthese'])) {
                $this->request->data['Deliberation']['texte_synthese'] = $typeacte['Typeacte']['gabarit_synthese'];
                $this->request->data['Deliberation']['texte_synthese_name'] = $typeacte['Typeacte']['gabarit_synthese_name'];
                $this->request->data['Deliberation']['texte_synthese_size'] = strlen($typeacte['Typeacte']['gabarit_synthese']);
                $this->request->data['Deliberation']['texte_synthese_type'] = 'application/vnd.oasis.opendocument.text';
            }
            if (!empty($typeacte['Typeacte']['gabarit_acte'])) {
                $this->request->data['Deliberation']['deliberation'] = $typeacte['Typeacte']['gabarit_acte'];
                $this->request->data['Deliberation']['deliberation_name'] = $typeacte['Typeacte']['gabarit_acte_name'];
                $this->request->data['Deliberation']['deliberation_size'] = strlen($typeacte['Typeacte']['gabarit_acte']);
                $this->request->data['Deliberation']['deliberation_type'] = 'application/vnd.oasis.opendocument.text';
            }

            //on récupère la liste des utilisateurs vouluent dans la combobox
            //et on les sauvegarde de facon a mettre a jour la table users_deliberations
                if(!empty($this->request->data['Deliberation']['multiRedactor'])){
                    $multiusers = array();
                    foreach ($this->request->data['Deliberation']['multiRedactor'] as $user){
                      $multiusers[] = array('id' => $user,
                                'UsersDeliberation' => array(
                                    'user_id' => $user,
                                ));
                    }
                $this->request->data['User'] = $multiusers;    
                }
            // saveAll permet d'enregistrer ds la table de liason                    
            $success &= $this->Deliberation->saveAll($this->data);

            if ($success) {
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
                    $success &= $this->Deliberation->Infosup->saveCompacted($this->request->data['Infosup'], $delibId, 'Deliberation');
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
                  } */

                $sortie = true;
            }

            if ($success) {
                $this->Session->setFlash('Projet créé. Identifiant: ' . $delibId, 'growl');
                $this->Deliberation->commit();
            } else {
                $this->Deliberation->rollback();
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }

        if ($sortie) {
            if (isset($this->request->data['nameTab'])) {
                return $this->redirect(array('controller' => 'deliberations', 'action' => 'edit', $delibId, 'nameTab' => $this->request->data['nameTab']));
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

            // initialisation dekjhlkjlkjlkj la liste des séances
            $seances = array();
            if (!empty($this->request->data['Typeseance']['Typeseance'])) {
                $selectedTypeseanceIds = set::extract('/Typeseance/Typeseance', $this->request->data);

                $seances_tmp = $this->Seance->find('all', array(
                    'conditions' => array('Seance.type_id' => $selectedTypeseanceIds,
                        'Seance.traitee' => 0),
                    'order' => array('Seance.date' => 'ASC'),
                    'contain' => array('Typeseance.libelle', 'Typeseance.retard'),
                    'fields' => array('Seance.id', 'Seance.type_id', 'Seance.date')));
                foreach ($seances_tmp as $seance)
                    $seances[$seance['Seance']['id']] = $seance['Typeseance']['libelle'];// . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
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
                        $seances[$seance['Seance']['id']] = $seance['Typeseance']['libelle'];// . ' : ' . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
                }
            }


            $nature_ids = $this->Deliberation->Typeacte->Nature->find('all', array(
                'conditions' => array('Nature.code' => 'DE'),
                'recursive' => -1,
                'fields' => array('Nature.id')
            ));

            $typeacte_ids = $this->Deliberation->Typeacte->find('all', array(
                'conditions' => array('Typeacte.nature_id' => Set::extract('/Nature/id', $nature_ids)),
                'recursive' => -1,
                'fields' => array('Typeacte.id')
            ));
            $services = $this->Deliberation->Redacteur->find('first',array(
                'fields' => array('Redacteur.id'),
                'conditions' => array('Redacteur.id' => $this->user_id),
                'contain' => array('Service' => array('fields' => array('Service.id'),)),
                'recursive' => -1,
            ));
            $listeservices = array();
            foreach ($services['Service'] as $service){
                $listeservices[] = $service['id'];
            }
            $condition = array();
            if(!empty($listeservices)){
                if(count($listeservices) > 1){
                    $condition['Service.id IN'] = $listeservices;
                }else{
                    $condition['Service.id'] = $listeservices;
                }
            }
            $users = $this->Deliberation->Service->find('all',array(
                'fields' => array('Service.id'),
                'conditions' => $condition,
                'contain' => array('User' => array('fields' => array('User.id','User.login','User.nom','User.prenom'),'conditions' => array('User.id <>' => $this->user_id))),
                'recursive' => -1,
                ));
            $listeusers = array();
            foreach($users as $user){
                foreach($user['User'] as $data){
                   $listeusers[$data['id']] = '('.$data['login'].') '.$data['prenom'].' '.$data['nom']; 
                }
            }
            $this->set('redacteurs', $listeusers);

            $this->set('typesactemulti', Set::extract('/Typeacte/id', $typeacte_ids));
            $this->set('seances', $seances);
            return $this->render('add');
        }
    }

    function download($id, $file) {

        $this->autoRender = false;

        $fileType = $file . '_type';
        $fileSize = $file . '_size';
        $fileName = $file . '_name';
        $delib = $this->Deliberation->find('first', array(
            'conditions' => array("Deliberation.id" => $id),
            'fields' => array($fileType, $fileSize, $fileName, $file),
            'recursive' => -1
        ));
        $this->response->type($delib['Deliberation'][$fileType]);
        $this->response->download($delib['Deliberation'][$fileName]);
        $this->response->body($delib['Deliberation'][$file]);
    }

    function deleteDebat($delib_id, $seance_id) {
        $this->Deliberation->id = $delib_id;
        $data = array(
            'debat' => '',
            'debat_name' => '',
            'debat_size' => 0,
            'debat_type' => ''
        );

        if ($this->Deliberation->save($data, false)) {
            $this->Session->setFlash('Débat supprimé !', 'growl');
            return $this->redirect(array('controller' => 'seances', 'action' => 'SaisirDebat', $delib_id, $seance_id));
        } else {
            $this->Session->setFlash("Problème survenu lors de la suppression du débat", 'growl', array('type' => 'erreur'));
            return $this->redirect($this->here);
        }
    }

    function downloadDelib($delib_id) {
        $this->Deliberation->id = $delib_id;
        $delib_pdf = $this->Deliberation->field('delib_pdf');
        $num_delib = $this->Deliberation->field('num_delib');

        if (!empty($num_delib))
            $filename = $num_delib . '.pdf';
        else
            $filename = "projet_$delib_id.pdf";

        // envoi au client
        $this->response->disableCache();
        $this->response->body($delib_pdf);
        $this->response->type('application/pdf');
        $this->response->download($filename);
        return $this->response;
    }

    function downloadSignature($delib_id) {
        $this->Deliberation->id = $delib_id;
        $signature = $this->Deliberation->field('signature');
        $num_delib = $this->Deliberation->field('num_delib');

        // envoi au client
        $this->response->disableCache();
        $this->response->body($signature);
        $this->response->type('application/zip');
        $this->response->download($num_delib . '_signature_.zip');
        return $this->response;
    }

    function downloadBordereau($delib_id) {
        $this->Deliberation->id = $delib_id;
        $bordereau = $this->Deliberation->field('parapheur_bordereau');
        $num_delib = $this->Deliberation->field('num_delib');

        // envoi au client
        $this->response->disableCache();
        $this->response->body($bordereau);
        $this->response->type('application/pdf');
        $this->response->download('bordereau_signature_' . $num_delib . '.pdf');
        return $this->response;
    }

    /**
     * @param int|string $delibId
     * @param array $annexe
     * @param array $annexesErrors
     * @return bool
     */
    function _saveAnnexe($delibId, $annexe, &$annexesErrors) {
        App::uses('File', 'Utility');
        if ($annexe['ref'] == 'delibPrincipale')
            $Model = 'Projet';
        else
            $Model = 'Deliberation';

        //Pour la gestion des erreurs des annexes
        $titre = !empty($annexe['titre']) ? $annexe['titre'] : $annexe['file']['name'];

        if (ini_get('upload_max_filesize') > $annexe['file']['size'])
            $annexesErrors[$titre][] = 'Limite de taille par fichier : ' . ini_get('upload_max_filesize');
        elseif ($annexe['file']['error'] != 0)
            $annexesErrors[$titre][] = 'Erreur lors de l&apos;envoi';
        elseif (is_array($annexe) && $this->Annex->isUploadedFile(array('file' => $annexe['file']))) {
            $this->Annex->begin();
            $newAnnexe = $this->Annex->create();
            $newAnnexe['Annex']['model'] = $Model;
            $newAnnexe['Annex']['foreign_key'] = $delibId;
            $newAnnexe['Annex']['titre'] = $annexe['titre'];
            $newAnnexe['Annex']['joindre_ctrl_legalite'] = $annexe['ctrl'];
            $newAnnexe['Annex']['joindre_fusion'] = $annexe['fusion'];

            $file = new File($annexe['file']['tmp_name'], false);
            $allowed = $this->Fido->checkFile($file->path);
            $results = $this->Fido->lastResults;
            if ($results['result'] == 'KO') {
                $annexesErrors[$titre][] = 'Format de fichier non reconnu. Veuillez contacter votre administrateur';
                $file->close();
                return false;
            } elseif (!$allowed) {
                $annexesErrors[$titre][] = 'Fichiers ' . $results['formatname'] . ' (' . $results['puid'] . ') non autorisés. Veuillez contacter votre administrateur';
                $file->close();
                return false;
            }

            $newAnnexe['Annex']['filetype'] = $results['mimetype'];
            $newAnnexe['Annex']['size'] = $file->size();
            $newAnnexe['Annex']['data'] = $file->read();
            $newAnnexe['Annex']['filename'] = $annexe['file']['name'];
            $file->close();

            if (!$this->Annex->save($newAnnexe['Annex'])) {
//                $this->Annex->rollback();
                foreach ($this->Annex->validationErrors as $error_annexe)
                    $annexesErrors[$titre][] = implode(',', $error_annexe);
                $this->Annex->validationErrors = array();
            } else {
                $this->Annex->commit();
                return true;
            }
        } else
            $annexesErrors[$titre][] = 'Erreur inconnue';

        return false;
    }

    function edit($id = null) {
        
        if (!$this->Deliberation->hasAny(array('id' => $id))) {
            $this->Session->setFlash("Le projet n&deg;$id est introuvable !", 'growl');
            return $this->redirect($this->previous);
        }
        $annexesErrors = array();
        $user = $this->Session->read('user');
        $canEditAll = $this->Droits->check($user['User']['id'], "Deliberations:editerTous");
        $redirect = '/';
        $pos = strrpos(getcwd(), 'webroot');
        $path = substr(getcwd(), 0, $pos);
        $path_projet = $path . 'webroot' . DS . 'files' . DS . 'generee' . DS . 'projet' . DS . $id . DS;
        $path_webroot = '/files/generee/projet/' . $id . '/';
        $typeseances_selected = array();
        $seances = array();
        $this->set('USE_PASTELL', Configure::read('USE_PASTELL'));
        $extensions = array();
        $extensionsFusion = array();
        $extensionsCtrl = array();
        foreach (Configure::read('DOC_TYPE') as $format) {
            if (!is_array($format['extension'])) {
                $extensions[] = $format['extension'];
                if (!empty($format['joindre_fusion']))
                    $extensionsFusion[] = $format['extension'];
                if (!empty($format['joindre_ctrl_legalite']))
                    $extensionsCtrl[] = $format['extension'];
            } else
                foreach ($format['extension'] as $extension) {
                    $extensions[] = $extension;
                    if (!empty($format['joindre_fusion']))
                        $extensionsFusion[] = $extension;
                    if (!empty($format['joindre_ctrl_legalite']))
                        $extensionsCtrl[] = $extension;
                }
        }
        $this->set('extensions', $extensions);
        $this->set('extensionsFusion', $extensionsFusion);
        $this->set('extensionsCtrl', $extensionsCtrl);

        //TODO chercher les types d'acte de nature delib pour autorisation multi-delib

        if (!$this->request->isPut()) {
            $this->Deliberation->Behaviors->load('Containable');
            $this->Seance->Behaviors->load('Containable');

            /* initialisation du lien de redirection */
            $history = $this->Session->read('user.history');
            if ($this->previous['action'] != 'add' && $this->previous['action'] != 'edit')
                $redirect = $this->previous;
            elseif ($this->previous['action'] == 'add')
                $redirect = array('action' => 'mesProjetsRedaction');
            else
                foreach ($history as $h)
                    if (stripos($h, 'deliberations/add') === false && stripos($h, 'deliberations/edit') === false
                    ) {
                        $redirect = $h;
                        break;
                    }
            $this->request->data = $this->Deliberation->find('first', array(
                'contain' => array(/* 'Annex.id', 'Annex.filetype', 'Annex.model',
                      'Annex.foreign_key', 'Annex.filename',
                      'Annex.titre', 'Annex.joindre_ctrl_legalite', 'Annex.joindre_fusion', */
                    'Infosup', 'Seance', 'Typeseance', 'Redacteur.id', 'Redacteur.nom', 'Redacteur.prenom'),
                'conditions' => array('Deliberation.id' => $id)));
            if (!empty($this->request->data['Deliberation']['parent_id'])) {
                return $this->redirect(array('action' => 'edit', $this->request->data['Deliberation']['parent_id']));
            }
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

                if (!$bSeanceok && !empty($seances_selected) && in_array($seance['Seance']['id'], $seances_selected))
                    $bSeanceok = true;

                if (!$bSeanceok) {
                    $iTime = strtotime($seance['Seance']['date']);
                    if (time() < mktime(0, 0, 0, date("m", $iTime), date("d", $iTime) - $seance['Typeseance']['retard'], date("Y", $iTime)))
                        $bSeanceok = true;
                }

                if ($bSeanceok) {
                    $seances[$seance['Seance']['id']]['libelle'] = $seance['Typeseance']['libelle'];
                    $seances[$seance['Seance']['id']]['date'] = $seance['Seance']['date'];  
                }
            }

            if (Configure::read('DELIBERATIONS_MULTIPLES')) {
                $this->Deliberation->Multidelib->Behaviors->load('Containable');
                $multiDelibs = $this->Deliberation->Multidelib->find('all', array(
                    'fields' => array('Multidelib.id', 'Multidelib.objet',
                        'Multidelib.deliberation', 'Multidelib.deliberation_name',
                        'Multidelib.objet_delib', 'Multidelib.deliberation_type',
                        'Multidelib.deliberation_name'),
                    'contain' => array(/* 'Annex.id', 'Annex.model',
                      'Annex.filetype', 'Annex.foreign_key',
                      'Annex.filename',
                      'Annex.titre', 'Annex.joindre_ctrl_legalite',
                      'Annex.joindre_fusion' */),
                    'conditions' => array('Multidelib.parent_id' => $id),
                    'order' => array('Multidelib.id')));
                foreach ($multiDelibs as $imd => $multiDelib) {
                    $this->request->data['Multidelib'][$imd] = $multiDelib['Multidelib'];
                }
            }
            $natures = array_keys($this->Session->read('user.Nature'));

            if (!in_array($this->data['Deliberation']['typeacte_id'], $natures)) {
                $this->Session->setFlash("Vous ne pouvez pas editer le projet '$id' en raison de son type d'acte.", 'growl', array('type' => 'erreur'));
                return $this->redirect($this->referer());
            }

            // teste si le projet est modifiable par l'utilisateur connecté
            if (!$this->Droits->check($this->user_id, "Deliberations:edit") || !$this->Deliberation->estModifiable($id, $this->user_id, $this->Droits->check($this->user_id, "Deliberations:editerTous"))) {
                if(!$this->Droits->check($this->data['Deliberation']['redacteur_id'], "Deliberations:edit")){
                $this->Session->setFlash("Vous n'avez pas les droits pour editer le projet '$id'.", 'growl', array('type' => 'erreur'));
                return $this->redirect($this->referer());
                }
            }
            // initialisation des fichiers des textes
            $this->Gedooo->createFile($path_projet, 'texte_projet.odt', $this->data['Deliberation']['texte_projet']);
            $this->Gedooo->createFile($path_projet, 'texte_synthese.odt', $this->data['Deliberation']['texte_synthese']);
            $this->Gedooo->createFile($path_projet, 'deliberation.odt', $this->data['Deliberation']['deliberation']);

            // création des fichiers des infosup de type odtFile
            foreach ($this->data['Infosup'] as $infosup) {
                $infoSupDef = $this->Infosupdef->find('first', array(
                    'recursive' => -1,
                    'fields' => array('type'),
                    'conditions' => array('id' => $infosup['infosupdef_id'], 'model' => 'Deliberation', 'actif' => true)));
                if (!empty($infoSupDef['Infosupdef']['type']) && $infoSupDef['Infosupdef']['type'] == 'odtFile' && !empty($infosup['file_name']) && !empty($infosup['content'])) {
                    $this->Gedooo->createFile($path_projet, $infosup['file_name'], $infosup['content']);
                }
            }
            // création des fichiers des annexes de type vnd.oasis.opendocument
            $annexes = $this->Annex->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'filename', 'filetype', 'titre', 'joindre_ctrl_legalite', 'joindre_fusion'),
                'conditions' => array('foreign_key' => $id),
                'order' => array('id ASC')
            ));

            foreach ($annexes as &$annexe) {
                if ($annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text' || $annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.spreadsheet') {
                    $annexeData = $this->Annex->find('first', array(
                        'fields' => array('data'),
                        'conditions' => array('id' => $annexe['Annex']['id']),
                        'recursive' => -1
                    ));
                    $annexe['Annex']['edit'] = true;
                    $this->Gedooo->createFile($path_projet, $annexe['Annex']['filename'], $annexeData['Annex']['data']);
                    $annexe['Annex']['link'] = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . $path_webroot . $annexe['Annex']['filename'];
                }
            }
            //$this->request->data['Annex']= Hash::sort($this->request->data['Annex'], '{n}.id', 'asc');
            $this->set('annexes', $annexes);
            unset($annexes);

            // initialisation des délibérations rattachées
            if (array_key_exists('Multidelib', $this->request->data)) {
                foreach ($this->request->data['Multidelib'] as &$delibRattachee) {
                    $path_projet_delibRattachee = $path . 'webroot' . DS . 'files' . DS . 'generee' . DS . 'projet' . DS . $delibRattachee['id'] . DS;
                    $path_webroot_delibRattachee = '/files/generee/projet/' . $delibRattachee['id'] . '/';
                    $this->Gedooo->createFile($path_projet_delibRattachee, 'deliberation.odt', $delibRattachee['deliberation']);
                    // création des fichiers des annexes de type vnd.oasis.opendocument
                    $annexes_delibRattachee = $this->Annex->find('all', array(
                        'recursive' => -1,
                        'fields' => array('id', 'foreign_key', 'data', 'filename', 'filetype', 'titre', 'joindre_ctrl_legalite', 'joindre_fusion'),
                        'conditions' => array('foreign_key' => $delibRattachee['id']),
                        'order' => 'id asc'));
                    foreach ($annexes_delibRattachee as &$annexe) {
                        if ($annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text' || $annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.spreadsheet') {
                            $annexeData = $this->Annex->find('first', array(
                                'fields' => 'data',
                                'conditions' => array('id' => $annexe['Annex']['id']),
                                'recursive' => -1));
                            $annexe['Annex']['edit'] = true;
                            $this->Gedooo->createFile($path_projet_delibRattachee, $annexe['Annex']['filename'], $annexe['Annex']['data']);
                            $annexe['Annex']['link'] = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . $path_webroot_delibRattachee . $annexe['Annex']['filename'];
                        }
                        //$this->request->data['Multidelib'][$delibRattachee['id']]['Annex'] = $multiDelib['Annex'];
                    }
                    $delibRattachee['Annexes'] = $annexes_delibRattachee;
                }
            }
            if (!empty($this->data['Deliberation']['num_pref']))
                $this->request->data['Deliberation']['num_pref_libelle'] = $this->data['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->data['Deliberation']['num_pref']);

            $this->request->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->request->data['Infosup']);
            $this->request->data['Deliberation']['date_limite'] = date("d/m/Y", (strtotime($this->data['Deliberation']['date_limite'])));
            $this->request->data['Service']['libelle'] = $this->Deliberation->Service->doList($this->request->data['Deliberation']['service_id']);

            $this->set('gabarits_acte', $this->Deliberation->Typeacte->find('list', array('fields' => array('id', 'gabarit_acte_name'))));
            $this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));

            $this->set('selectedTypeacteId', $this->request->data['Deliberation']['typeacte_id']);

            //Pour l'affichage de l'onglet
            if (isset($this->request['named']['nameTab']))
                $this->set('nameTab', $this->request['named']['nameTab']);

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
            if (Configure::read('TDT') == 'PASTELL' && Configure::read('USE_PASTELL') && Configure::read('USE_TDT')) {
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
            //on récupaire tout les utilisateurs sauf l'utilisateur courrant
            $user = $this->Deliberation->find('first',array('conditions' => array('Deliberation.id' => $id),'fields' => array('redacteur_id'),'recursive' => -1,));
            // on format les données pour le select id => text
            $select = array();
            $users = $this->Deliberation->find('first',
                        array('fields' => 'id',
                            'contain' => array('User' => array('fields' => array('id'))),
                            'conditions' => array('Deliberation.id' => $id)));
            // listes des utilisateurs deja rataché à la délibération
            foreach ($users['User'] as $users){
                $select[$users['id']] = $users['id'];
            }
            // liste de tout les autres rédacteurs possibles
            $services = $this->Deliberation->Redacteur->find('first',array(
                'fields' => array('Redacteur.id'),
                'conditions' => array('Redacteur.id' => $user['Deliberation']['redacteur_id']),
                'contain' => array('Service' => array('fields' => array('Service.id'),)),
                'recursive' => -1,
            ));
            $listeservices = array();
            foreach ($services['Service'] as $service){
                $listeservices[] = $service['id'];
            }
            $condition = array();
            if(!empty($listeservices)){
                if(count($listeservices) > 1){
                    $condition['Service.id IN'] = $listeservices;
                }else{
                    $condition['Service.id'] = $listeservices;
                }
            }
            $users = $this->Deliberation->Service->find('all',array(
                'fields' => array('Service.id'),
                'conditions' => $condition,
                'contain' => array('User' => array('fields' => array('User.id','User.login','User.nom','User.prenom'),'conditions' => array('User.id <>' => $user['Deliberation']['redacteur_id']))),
                'recursive' => -1,
                ));
            $listeusers = array();
            foreach($users as $user){
                foreach($user['User'] as $data){
                   $listeusers[$data['id']] = '('.$data['login'].') '.$data['prenom'].' '.$data['nom']; 
                }
            }
            $this->set('select', $select);
            $this->set('redacteurs', $listeusers);
            $this->render();
        } else {
            $this->Deliberation->begin();
            $success = true;
            $redirect = !empty($this->data['Deliberation']['redirect'])?$this->data['Deliberation']['redirect']:$this->previous;
            $oldDelib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $id)));
            // Si on definit une seance a une delib, on la place en derniere position de la seance
            if (isset($this->data['Seance'])) {
                if (!$this->Deliberation->canSaveSeances($this->data['Seance']['Seance'])) {
                    $this->Session->setFlash("Vous ne pouvez affecter le projet qu'à une seule séance délibérante", 'growl', array('type' => 'erreur'));
                    $this->redirect(array('action' => 'edit', $id));
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
            unset($this->request->data['Seance']['Seance']);

            $this->Deliberation->set($this->data);
            $textes = array('texte_projet', 'texte_synthese', 'deliberation');
            $validUpload = true;
            foreach ($textes as $texte) {
                if (array_key_exists($texte . '_upload', $this->data['Deliberation'])) {
                    $validUpload = $validUpload && $this->Deliberation->validates(array('fieldList' => array($texte . '_upload')));
                }
            }
            if ($validUpload)
                foreach ($textes as $texte) {
                    if (array_key_exists($texte . '_upload', $this->data['Deliberation'])) {
                        $this->request->data['Deliberation'][$texte . '_name'] = $this->data['Deliberation'][$texte . '_upload']['name'];
                        $this->request->data['Deliberation'][$texte . '_size'] = $this->data['Deliberation'][$texte . '_upload']['size'];
                        $this->request->data['Deliberation'][$texte . '_type'] = $this->data['Deliberation'][$texte . '_upload']['type'];
                        $this->request->data['Deliberation'][$texte] = !empty($this->data['Deliberation'][$texte . '_upload']['tmp_name']) ? file_get_contents($this->data['Deliberation'][$texte . '_upload']['tmp_name']) : '';
                    } else {
                        $this->request->data['Deliberation'][$texte] = file_get_contents($path_projet . $texte . '.odt');
                    }
                }

            if ($oldDelib['Deliberation']['is_multidelib'] != 1)
                if (empty($this->data['Deliberation']['is_multidelib']) OR ( @$this->data['Deliberation']['is_multidelib'] == 0))
                    $this->request->data['Deliberation']['objet_delib'] = $this->data['Deliberation']['objet'];

            if (!empty($this->request->data['Deliberation']['date_limite'])) {
                App::uses('CakeTime', 'Utility');
                $this->request->data['Deliberation']['date_limite'] = CakeTime::format($this->data['date_limite'], '%Y-%m-%d 00:00:00');
            }
            //on récupère la liste des utilisateurs vouluent et on les sauvegarde
            $multiusers = array();
                if(!empty($this->request->data['Deliberation']['multiRedactor'])){
                    foreach ($this->request->data['Deliberation']['multiRedactor'] as $user){
                      $multiusers[] = array('id' => $user,
                                'UsersDeliberation' => array(
                                    'user_id' => $user,
                                ));
                    }
                } else {
                    $multiusers = array('id' => null,
                                'UsersDeliberation' => array());
                }
                $deliberation = array('Deliberation' => array('id' => $id));
                $deliberation['User'] = $multiusers;
                $this->Deliberation->saveAll($deliberation);

                $success = $this->Deliberation->save($this->request->data);
                
            if ($success) {
                $this->Historique->enregistre($id, $this->user_id, "Modification du projet");
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
                    $success &= $this->Infosup->saveCompacted($this->request->data['Infosup'], $this->request->data['Deliberation']['id'], 'Deliberation');
                    if (!$success)
                        unset($this->request->data['Infosup']);
                }
                // sauvegarde des nouvelles annexes
                if (array_key_exists('Annex', $this->data))
                    foreach ($this->data['Annex'] as $annexe) {
                        //Cas bloc annexe vide
                        if (empty($annexe['file']['name']))
                            continue;
                        if ($annexe['ref'] == 'delibPrincipale' || $annexe['ref'] == 'delibRattachee' . $id)
                            $success &= $this->_saveAnnexe($id, $annexe, $annexesErrors);
                    }

                // suppression des annexes
                if (array_key_exists('AnnexesASupprimer', $this->data))
                    foreach ($this->data['AnnexesASupprimer'] as $annexeId)
                        $this->Annex->delete($annexeId);

                // Modification des annexes
                if (array_key_exists('AnnexesAModifier', $this->data)) {
                    foreach ($this->data['AnnexesAModifier'] as $annexeId => $annexe) {
                        $this->Annex->begin();
                        $annex_filename = $this->Annex->find('first', array(
                            'recursive' => -1,
                            'fields' => array('filename', 'filetype', 'id', 'foreign_key'),
                            'conditions' => array('Annex.id' => $annexeId)));
                        if ($annex_filename['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text' || $annex_filename['Annex']['filetype'] == 'application/vnd.oasis.opendocument.spreadsheet') {
                            $this->Annex->save(array(
                                'id' => $annexeId,
                                'titre' => $annexe['titre'],
                                'joindre_ctrl_legalite' => $annexe['joindre_ctrl_legalite'],
                                'joindre_fusion' => $annexe['joindre_fusion'],
                                'data' => file_get_contents(WEBROOT_PATH . DS . 'files' . DS . 'generee' . DS . 'projet' . DS . $annex_filename['Annex']['foreign_key'] . DS . $annex_filename['Annex']['filename']),
                                'edition_data' => NULL,
                                'data_pdf' => NULL));
                        } else {
                            $this->Annex->save(array(
                                'id' => $annexeId,
                                'titre' => $annexe['titre'],
                                'joindre_ctrl_legalite' => $annexe['joindre_ctrl_legalite'],
                                'joindre_fusion' => $annexe['joindre_fusion'],
                                'edition_data' => NULL,
                                'data_pdf' => NULL));
                        }
                        if (!empty($this->Annex->validationErrors)) {
                            $this->Annex->rollback();
                            $success = false;
                            $titre = !empty($annexe['titre']) ? $annexe['titre'] : $annex_filename['Annex']['filename'];
                            foreach ($this->Annex->validationErrors as $validationError) {
                                $annexesErrors[$titre][] = implode(',', $validationError);
                            }
                        } else {
                            $this->Annex->commit();
                        }
                    }
                }

                // suppression des délibérations rattachées
                if (array_key_exists('MultidelibASupprimer', $this->data))
                    foreach ($this->data['MultidelibASupprimer'] as $delibId) {
                        $this->Deliberation->begin();
                        $this->Deliberation->supprimer($delibId);
                        unset($this->request->data['Multidelib'][$delibId]);
                        $this->Deliberation->commit();
                    }

                // sauvegarde des délibérations rattachées
                if ($success && array_key_exists('Multidelib', $this->data)) {
                    foreach ($this->data['Multidelib'] as $iref => $multidelib) {
                        if (!empty($this->data['Deliberation']['num_pref']))
                            $multidelib['num_pref'] = $this->data['Deliberation']['num_pref'];
                        $multidelib['typeacte_id'] = $this->data['Deliberation']['typeacte_id'];
                        $delibRattacheeId = $this->Deliberation->saveDelibRattachees($id, $multidelib);
                        // sauvegarde des nouvelles annexes pour cette delib rattachée
                        if (array_key_exists('Annex', $this->data))
                            foreach ($this->data['Annex'] as $annexe)
                                if ($annexe['ref'] == 'delibRattachee' . $iref)
                                    $success &= $this->_saveAnnexe($delibRattacheeId, $annexe, $annexesErrors);
                    }
                }
                
                if ($success) {
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
                        $success &= $this->Deliberation->save($multi_Delib);
                    }
                }
            }
            
            if ($success) {
                $this->Deliberation->commit();
//                $this->Annex->commit();
                $cmd = 'nohup nice -n 10 ' . APP . 'Console' . DS . 'cake Maintenance conversionAnnexe -i ' . $id . ' >/dev/null 2>&1  & echo $!';
                $PID = shell_exec($cmd);
                $this->Session->setFlash("Le projet $id a été enregistré", 'growl');
                $sortie = true;

                //Envoi d'une notification de modification au rédacteur
                $currentUser = $this->user_id;
                $redacteurId = $oldDelib['Deliberation']['redacteur_id'];
                if ($currentUser != $redacteurId) {
                    $this->User->notifier($id, $redacteurId, 'modif_projet_cree');
                }
                //Envoi d'une notification de modification aux utilisateurs qui ont déjà validé le projet
                $destinataires = $this->Traitement->whoIs($id, 'before', array('OK', 'IN'));
                foreach ($destinataires as $destinataire_id)
                    if (!in_array($destinataire_id, array($currentUser, $redacteurId)))
                        $this->User->notifier($id, $destinataire_id, 'modif_projet_valide');
            } else {
                $this->Deliberation->rollback();
                $this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                $msg_error = '';
                if (!empty($annexesErrors)) {
                    foreach ($annexesErrors as $annexeName => $annexError) {
                        $msg_error .= "<strong>Annexe &apos;" . $annexeName . "&apos; :</strong><br>";
                        foreach ($annexError as $error) {
                            $msg_error .= "- " . $error . "<br/>";
                        }
                    }
                    $this->Session->setFlash($msg_annexe_error, 'growl', array('type' => 'erreur'));
                }
                $InfosupErrors = $this->Deliberation->Infosup->invalidFields();
                if (!empty($InfosupErrors)) {
                    foreach ($InfosupErrors as $InfosupName => $InfosupError) {
                        $msg_error .= "<strong>Information supplémentaire :</strong><br>";
                        foreach ($InfosupError as $error) {
                            $msg_error .= "- " . $error . "<br/>";
                        }
                    }
                }
                $this->Session->setFlash($msg_error, 'growl', array('type' => 'erreur'));
                $this->set('errors_Infosup', $InfosupErrors);
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
                    } else
                        $seances[$seance['Seance']['id']]['libelle'] = $seance['Typeseance']['libelle'];
                        $seances[$seance['Seance']['id']]['date'] = $seance['Seance']['date'];  
                }

                if (Configure::read('DELIBERATIONS_MULTIPLES')) {
                    $this->set('gabarits_acte', $this->Deliberation->Typeacte->find('list', array('fields' => array('id', 'gabarit_acte_name'))));
                    if (isset($this->request->data['Multidelib']))
                        unset($this->request->data['Multidelib']);
                    $this->Deliberation->Multidelib->Behaviors->load('Containable');
                    $multiDelibs = $this->Deliberation->Multidelib->find('all', array(
                        'fields' => array(
                            'Multidelib.id', 'Multidelib.objet',
                            'Multidelib.deliberation', 'Multidelib.deliberation_name',
                            'Multidelib.objet_delib', 'Multidelib.deliberation_type',
                            'Multidelib.deliberation_name'
                        ),
                        'conditions' => array('Multidelib.parent_id' => $id),
                        'order' => array('Multidelib.id')
                    ));
                    foreach ($multiDelibs as $imd => $delibRattachee) {
                        $this->request->data['Multidelib'][$imd] = $delibRattachee['Multidelib'];

                        $path_projet_delibRattachee = $path . 'webroot' . DS . 'files' . DS . 'generee' . DS . 'projet' . DS . $delibRattachee['Multidelib']['id'] . DS;
                        $path_webroot_delibRattachee = '/files/generee/projet/' . $delibRattachee['Multidelib']['id'] . '/';
                        $this->Gedooo->createFile($path_projet_delibRattachee, 'deliberation.odt', $delibRattachee['Multidelib']['deliberation']);
                        // création des fichiers des annexes de type vnd.oasis.opendocument
                        $annexes_delibRattachee = $this->Annex->find('all', array(
                            'recursive' => -1,
                            'fields' => array('id', 'filename', 'filetype', 'titre', 'joindre_ctrl_legalite', 'joindre_fusion'),
                            'conditions' => array(
                                'foreign_key' => $delibRattachee['Multidelib']['id']),
                            'order' => 'id asc'));
                        foreach ($annexes_delibRattachee as &$annexe) {
                            if ($annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text' || $annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.spreadsheet') {
                                $annexeData = $this->Annex->find('first', array(
                                    'fields' => array('data'),
                                    'conditions' => array(
                                        'id' => $annexe['Annex']['id']),
                                    'recursive' => -1));
                                $annexe['Annex']['edit'] = true;
                                //Pour ne pas perdre les modifications suite a une erreur je commente la ligne
                                //$this->Gedooo->createFile($path_projet_delibRattachee, $annexe['Annex']['filename'], $annexeData['Annex']['data']);
                                $annexe['Annex']['link'] = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . $path_webroot_delibRattachee . $annexe['Annex']['filename'];
                            }
                        }
                        $this->request->data['Multidelib'][$imd]['Annexes'] = $annexes_delibRattachee;
                    }
                }

                $this->set('seances', $seances);
                $this->set('seances_selected', $seances_selected);
                $this->set('typeseances', $typeseances);
                $this->set('typeseances_selected', $typeseances_selected);

                $this->set('services', $this->Deliberation->Service->find('list', array('conditions' => array('Service.actif' => '1'))));
                $this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
                $this->set('circuits', $this->Deliberation->Circuit->find('list'));
                $this->set('datelim', $this->data['Deliberation']['date_limite']);
                $this->set('redirect', $redirect);
                //FIX
                // création des fichiers des annexes de type vnd.oasis.opendocument
                $annexes = $this->Annex->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id', 'filename', 'filetype', 'titre', 'joindre_ctrl_legalite', 'joindre_fusion'),
                    'conditions' => array(
                        'foreign_key' => $id)));

                foreach ($annexes as &$annexe) {
                    if ($annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text' || $annexe['Annex']['filetype'] == 'application/vnd.oasis.opendocument.spreadsheet') {
                        $annexeData = $this->Annex->find('first', array(
                            'fields' => array('data'),
                            'conditions' => array('id' => $annexe['Annex']['id']),
                            'recursive' => -1));
                        $annexe['Annex']['edit'] = true;
                        $this->Gedooo->createFile($path_projet, $annexe['Annex']['filename'], $annexeData['Annex']['data']);
                        $annexe['Annex']['link'] = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . $path_webroot . $annexe['Annex']['filename'];
                    }
                }
                $this->set('annexes', $annexes);
                unset($annexes);

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

    function delete($id = null) {

        $delib = $this->Deliberation->find('first', array(
            'recursive' => -1,
            'fields' => array('Deliberation.id', 'Deliberation.redacteur_id', 'Deliberation.etat', 'Deliberation.parapheur_etat'),
            'conditions' => array('id' => $id)));

        if (empty($delib)) {
            $this->Session->setFlash('Invalide id pour le projet de deliberation : suppression impossible', 'growl', array('type' => 'erreur'));
        } elseif ($delib['Deliberation']['parapheur_etat'] == 1) {
            $this->Session->setFlash('Le projet est dans une étape parapheur, il ne peut être supprimé.', 'growl', array('type' => 'erreur'));
        } else {
            //$this->request->allowMethod('post');

            $canDelete = $this->Droits->check($this->user_id, "Deliberations:delete");
            if ((($delib['Deliberation']['redacteur_id'] == $this->user_id) && ($delib['Deliberation']['etat'] == 0)) || ($canDelete)) {
                $this->Deliberation->supprimer($id);
                $this->Session->setFlash('Le projet \'' . $id . '\' a été supprimé.', 'growl');
            } else {
                $this->Session->setFlash('Vous ne pouvez pas supprimer ce projet', 'growl');
            }
        }
        $this->redirect($this->previous);
    }
    /**
     * 
     * @param type $id
     * @param type $users
     */
    function _addUsersIntoCircuit($id = null,$users = array()){
        $message = "Projet injecté au circuit : " . $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);
        foreach ($users as $user){
             $this->Historique->enregistre($id, $user, $message);
        }
        
        
    }
    /**
     * Ajoute un projet dans un circuit
     * 
     * @param type $id id du projet
     * @param type $users listes des corédacteurs possibles
     */
    function _addIntoCircuit($id = null,$users = array()) {

        try {
            // enregistrement de l'historique
            $message = "Projet injecté au circuit : " . $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);
            $this->Historique->enregistre($id, $this->user_id, $message);

            $data = array(
                'id' => $id,
                'date_envoi' => date('Y-m-d H:i:s'),
                'etat' => '1',
            );

            if (!$this->Deliberation->save($data)) {
                new Exception('Problème de sauvegarde.', 'error');
            }
            // insertion dans le circuit de traitement
            
            if ($this->Traitement->targetExists($id)) {
                $this->Circuit->ajouteCircuit($this->data['Deliberation']['circuit_id'], $id, $this->user_id);
                $this->Traitement->Visa->replaceDynamicTrigger($id, $this->user_id);
                $members = $this->Traitement->whoIs($id, 'current', 'RI');
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
                            $this->redirect(array('action' => 'mesProjetsValides'));
                        }
                        $members = $this->Traitement->whoIs($id, 'current', 'RI');
                    }
                    foreach ($members as $destinataire_id)
                        $this->User->notifier($id, $destinataire_id, 'traitement');

                    $members = $this->Traitement->whoIs($id, 'after', 'RI');
                    foreach ($members as $user_id)
                        $this->User->notifier($id, $user_id, 'insertion');

                    $this->Session->setFlash('Projet inséré dans le circuit et visé', 'growl');
                    $this->redirect(array('action' => 'mesProjetsRedaction'));
                }
            } else {
                //si users est vide on initialise avec avec l'id de l'utilisateur soit on le rajoute à la liste
                $redactor = $this->Deliberation->find('first',  array(
                    'fields' => array('id','redacteur_id'),
                    'conditions' => array('id' => $id),
                    'recursive' => -1,
                ));
                $users[] = $redactor['Deliberation']['redacteur_id'];
                // création des étapes du circuit dans le visas
                $this->Circuit->insertDansCircuit($this->data['Deliberation']['circuit_id'], $id,$redactor['Deliberation']['redacteur_id'] );
                //tableau des rédacteurs
                $options = array(
                    'insertion' => array(
                        '0' => array(
                            'Etape' => array(
                                'etape_id' => null,
                                'etape_nom' => 'Rédacteur',
                                'etape_type' => count($users) > 1?2:1,
                                'cpt_retard' => null
                            ),
                            'Visa' => array(
                                '0' => array(
                                    'trigger_id' => $users,
                                    'type_validation' => 'V'
                                )
                            ),
                        ),

                    ),
                    'optimisation' => configure::read('Cakeflow.optimisation')
                );
                $traitementTermine = $this->Traitement->execute('IN', $redactor['Deliberation']['redacteur_id'], $id, $options);
                //FIX Devrait enregistrer un historique des actions effectés en optimisation et autre mais pas que sur l'état final
                if ($traitementTermine) {
                    $this->Historique->enregistre($id, $redactor['Deliberation']['redacteur_id'], 'Projet validé');
                    $this->Deliberation->id = $id;
                    $this->Deliberation->saveField('etat', 2);
                }
                $this->Traitement->Visa->replaceDynamicTrigger($id, $this->user_id);

                $members = $this->Traitement->whoIs($id, 'current', 'RI');
                foreach ($members as $current_id)
                    $this->User->notifier($id, $current_id, 'traitement');

                $members = $this->Traitement->whoIs($id, 'after', 'RI');
                foreach ($members as $user_id)
                    $this->User->notifier($id, $user_id, 'insertion');
            }
        } catch (Exception $e) {
            new Exception($e->getMessage(), $e->getCode());
        }
    }

    function attribuercircuit($id = null) {

        if (!$id) {
            $this->Session->setFlash('Invalide id pour la deliberation', 'growl', array('type' => 'erreur'));
            return $this->redirect(array('action' => 'mesProjetsRedaction'));
        }

        $this->set('circuits', $this->User->getCircuits($this->user_id));

        if ($this->request->is('post')) {

            $this->Deliberation->id = $id;
            if (!empty($this->request->data['Deliberation']['circuit_id']) && $this->Deliberation->saveField('circuit_id', $this->request->data['Deliberation']['circuit_id'])) {
                
                // on récupère les utilisateurs associé multideliberation(table de liason)
                $multiusers = array();
                $users = $this->Deliberation->find('first',
                        array('fields' => array('id'),
                            'contain' => array('User' => array('fields' => array('id'))),
                            'conditions' => array('Deliberation.id' => $id)));
                //on récupère tout les utilisateurs secondaires
                        foreach ($users['User'] as $user){
                            $multiusers[] = $user['id'];
                        }
                $this->_addIntoCircuit($id,$multiusers);

                $message = "Projet injecté au circuit : " . $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);
                $this->Session->setFlash($message, 'growl');
                return $this->redirect(array(
                    'plugin' => null,
                    'controller' => 'deliberations',
                    'action' => 'mesProjetsRedaction',
                ));
            } else// if ($this->Deliberation->saveField('circuit_id', $circuit_id)) {
                $this->Session->setFlash('Veuillez sélectionner un circuit', 'growl', array('type' => 'erreur'));
        }


        $projet = $this->Deliberation->find('first', array(
            'fields' => array('modified', 'created', 'texte_projet', 'objet', 'num_pref'),
            'contain' => array(
                'Typeacte.name',
                'Theme.libelle',
                'Service.libelle',
                'Seance.date',
                'Seance.Typeseance.libelle',
                'Redacteur.nom',
                'Redacteur.prenom',
                'Rapporteur.nom',
                'Rapporteur.prenom',
            ),
            'conditions' => array('Deliberation.id' => $id),
            'recursive' => -1
                )
        );

        $projet['Deliberation']['num_pref'] = $projet['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($projet['Deliberation']['num_pref']);

        if (empty($projet['Deliberation']['texte_projet']))
            $this->Session->setFlash('Attention, le texte projet est vide', 'growl', array('type' => 'important'));
        /*
          for ($i = 0; $i < count($delib['Seance']); $i++) {
          $type = $this->Seance->Typeseance->find('first', array('conditions' => array('Typeseance.id' => $delib['Seance'][$i]['type_id']),
          'recursive' => -1,
          'fields' => array('libelle')));
          $delib['Seance'][$i]['Typeseance']['libelle'] = $type['Typeseance']['libelle'];
          } */
        /*

          $id_service = $delib['Service']['id'];
          $delib['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
          $tab_circuit = $delib['Deliberation']['circuit_id'];
          $delib['Circuit']['libelle'] = $this->Circuit->getLibelle($tab_circuit);
          //on recupere la position de l'user dans le circuit
          $this->set('deliberation', $delib);
         */
        $this->set('projet', $projet);

        //circuit par défaut de l'utilisateur connecté
        $userCircuitDefaultId = $this->User->circuitDefaut($this->user_id, 'id');

        //affichage du circuit &&existant
        if (empty($userCircuitDefaultId) && !empty($this->data['Deliberation']['circuit_id']))
            $userCircuitDefaultId = $this->data['Deliberation']['circuit_id'];

        if (isset($circuit_id)) {
            $this->set('userCircuitDefaultId', $userCircuitDefaultId);
            $this->set('visu', $this->requestAction(
                            array('controller' => 'cakeflow', 'action' => 'circuits', 'visuCircuit', $userCircuitDefaultId), array('return')
                    )
            );
        }
        //on récupaire tout les utilisateurs
            $redacteurs = $this->Deliberation->Redacteur->find('all',array(
               'fields' => array('Redacteur.id','Redacteur.login','Redacteur.nom','Redacteur.prenom'), 
                'recursive' => -1,
            ));
            // on format les données pour le select id => text
            foreach($redacteurs as $id => $redacteur){
                $redacteurs[$redacteur['Redacteur']['id']] = '('.$redacteur['Redacteur']['login'].') '.$redacteur['Redacteur']['prenom'].' '.$redacteur['Redacteur']['nom'];
                unset($redacteurs[$id]);
            }
            $this->set('redacteurs', $redacteurs);

        // initalisation du lien de retour
        $this->set('previous', $this->previous);
    }

    function retour($delib_id) {
        $delib = $this->Deliberation->find('first', array(
            'recursive' => -1,
            'conditions' => array('Deliberation.id' => $delib_id)
        ));

        if (empty($delib))
            $this->redirect($this->referer());

        if (empty($this->data)) {
            $etapes = $this->Traitement->listeEtapesPrecedentes($delib['Deliberation']['id']);
            if (empty($etapes)) {
                $this->Session->setFlash('Opération impossible, l&apos;étape courante est la première du circuit.', 'growl', array('type' => 'erreur'));
                return $this->redirect($this->referer());
            }
            $this->set('delib_id', $delib_id);
            $this->set('etapes', $etapes);
        } else {
            $this->Traitement->execute('JP', $this->user_id, $delib_id, array('etape_id' => $this->data['Traitement']['etape']));
            $destinataires = $this->Traitement->whoIs($delib_id, 'current', 'RI');
            foreach ($destinataires as $destinataire_id)
                $this->User->notifier($delib_id, $destinataire_id, 'traitement');
            $this->Historique->enregistre($delib_id, $this->user_id, "Projet retourné");
            $this->Session->setFlash('Opération effectuée !', 'growl');
            return $this->redirect('/');
        }
    }

    function traiter($id = null, $valid = null) {

        $this->Deliberation->id = $id;
        if (!$this->Deliberation->exists()) {
            $this->Session->setFlash("Le projet n&deg;$id est introuvable !", 'growl');
            return $this->redirect($this->previous);
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($valid == '1') {
                $this->_accepteDossier($id);
                $this->Session->setFlash('Vous venez de valider le projet : ' . $id, 'growl');
            } else {
                $this->_refuseDossier($id);
                $this->Session->setFlash('Vous venez de refuser le projet : ' . $id, 'growl');
            }

            return $this->redirect(array('action' => 'mesProjetsATraiter'));
        }

        $projet = $this->Deliberation->find('first', array(
            'fields' => array(
                'id',
                'anterieure_id',
                'parent_id',
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
                'User',
                'Typeacte.name',
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
                'Multidelib.Typeacte.name',
            ),
            'conditions' => array('Deliberation.id' => $id),
            'recursive' => -1)
        );

        //Si traitement d'une delib "enfant" rediriger vers traitement delib "parent"
        if (!empty($projet['Deliberation']['parent_id'])) {
            return $this->redirect(array('action' => 'traiter', $projet['Deliberation']['parent_id']));
        }

        $projet['Modeltemplate']['id'] = $this->Deliberation->getModelId($id);
        $projet['Deliberation']['num_pref'] = $projet['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($projet['Deliberation']['num_pref']);

        if (empty($projet)) {
            $this->Session->setFlash('identifiant invalide pour le projet : ' . $id, 'growl', array('type' => 'erreur'));
            return $this->redirect($this->previous);
        } else {
            $triggers = $this->Deliberation->Traitement->whoIs($id, 'current', 'RI');
            if (!in_array($this->user_id, $triggers)) {
                $this->redirect(array('action' => 'view', $id));
            }
            if ($valid == null) {
                $nb_recursion = 0;
                $action = 'view';
                $this->set('tab_anterieure', $this->Deliberation->chercherVersionAnterieure($projet['Deliberation']['id'], $nb_recursion, array(), $action));

                $id_service = $projet['Deliberation']['service_id'];
                $projet['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
                $projet['Circuit']['libelle'] = $this->Circuit->getLibelle($projet['Deliberation']['circuit_id']);
                $this->set('visu', $this->requestAction('/cakeflow/traitements/visuTraitement/' . $id, array('return')));
                $this->set('projet', $projet);
                $this->set('commentaires', $this->Commentaire->find('all', array(
                            'fields' => array('Commentaire.texte', 'Commentaire.created'),
                            'contain' => array('User.nom', 'User.prenom'),
                            'conditions' => array(
                                'Commentaire.delib_id' => $id,
                                'Commentaire.pris_en_compte' => 0
                            ),
                            'order' => array('Commentaire.created ASC'),
                            'recursive' => -1
                )));
                $this->set('historiques', $this->Historique->find('all', array(
                            'fields' => array('Historique.commentaire', 'Historique.created'),
                            'contain' => array('User.nom', 'User.prenom'),
                            'conditions' => array('Historique.delib_id' => $id),
                            //'joins' => array($this->Historique->join('User',array( 'type' => 'INNER' ))),
                            'order' => array('Historique.modified DESC'),
                            'recursive' => -1
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
            }
        }
    }

    function _refuseDossier($id) {
        $nouvelId = $this->Deliberation->refusDossier($id);
        $this->Traitement->execute('KO', $this->user_id, $id);
        $destinataires = $this->Traitement->whoIs($id, 'in', array('OK', 'IN'));
        foreach ($destinataires as $destinataire_id){
            $this->User->notifier($nouvelId, $destinataire_id, 'refus');
        }
        $this->Historique->enregistre($id, $this->user_id, 'Projet refusé');
    }

    function _accepteDossier($id) {
        $traitementTermine = $this->Traitement->execute('OK', $this->user_id, $id);
        $this->Historique->enregistre($id, $this->user_id, 'Projet accepté');
        if ($traitementTermine) {
            $this->Deliberation->id = $id;
            $this->Deliberation->saveField('etat', 2);
            $this->Historique->enregistre($id, $this->user_id, 'Projet validé');
        } else {
            $destinataires = $this->Traitement->whoIs($id, 'current', 'RI');
            foreach ($destinataires as $destinataire_id)
                $this->User->notifier($id, $destinataire_id, 'traitement');
        }
    }

    function transmit($seance_id = null) {
        if (!Configure::read('USE_TDT')) {
            $this->Session->setFlash('Le tiers de télétransmission est désactivé. Veuillez contacter votre administrateur', 'growl');
            return $this->redirect($this->previous);
        }
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

        $this->paginate = array('Deliberation' => array(
                'fields' => array(
                    'Deliberation.id',
                    'Deliberation.objet',
                    'Deliberation.objet_delib',
                    'Deliberation.num_delib',
                    'Deliberation.tdt_ar_date',
                    'Deliberation.tdt_ar',
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
                    'Annex' => array(
                        'fields' => array('id', 'filename_pdf'),
                        'conditions' => array('joindre_ctrl_legalite' => true)
                    ),
                    'Typeacte' => array(
                        'fields' => array('libelle', 'teletransmettre'),
                        'conditions' => array('Typeacte.teletransmettre' => true)),
                    'Circuit' => array('fields' => array('nom')),
                    'TdtMessage' => array('fields' => array('tdt_id', 'tdt_type', 'tdt_etat', 'parent_id'),
                        'conditions' => array('parent_id is null'),
                        'Reponse' => array('fields' => array('tdt_id', 'tdt_type', 'tdt_etat'))),
                    'Deliberationtypeseance' => array('fields' => array('id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'action'),
                        )),
                    'Deliberationseance' => array('fields' => array('id'),
                        'Seance' => array('fields' => array('id', 'date', 'type_id'),
                            'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
                'order' => 'Deliberation.id DESC',
                'limit' => 10));

        $this->set('tdt', Configure::read('TDT'));
        $this->set('tdt_host', Configure::read(Configure::read('TDT') . '_HOST'));
        $this->set('dateClassification', $this->S2low->getDateClassification());

        // On affiche que les delibs vote pour.
        $deliberations = $this->Paginator->paginate('Deliberation');
        $this->_sortProjetSeanceDate($deliberations);
        //debug($deliberations);
        $listeTypeSeance = array();
        foreach ($deliberations as $i => $projet) {
            $deliberations[$i]['Deliberation']['num_pref'] = $projet['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($projet['Deliberation']['num_pref']);
            if (!empty($deliberations[$i]['Deliberation']['tdt_ar'])) {
                $xmlArray = Xml::toArray(Xml::build($deliberations[$i]['Deliberation']['tdt_ar']));
                if (count($deliberations[$i]['Annex']) == $xmlArray['Acte']['actes:Annexes']['@actes:Nombre']) {
                    $deliberations[$i]['Deliberation']['tdt_ar_annexes_status'] = 'success';
                    $deliberations[$i]['Deliberation']['tdt_ar_annexes_status_libelle'] = __('Transmises');
                } else {
                    $deliberations[$i]['Deliberation']['tdt_ar_annexes_status'] = 'danger';
                    $deliberations[$i]['Deliberation']['tdt_ar_annexes_status_libelle'] = __('Non Transmises');
                }
            }

            $deliberations[$i]['listeSeances'] = array();
            if (isset($projet['Deliberationseance']) && !empty($projet['Deliberationseance'])) {
                foreach ($projet['Deliberationseance'] as $keySeance => $seance) {
                    $deliberations[$i]['listeSeances'][] = array('seance_id' => $seance['Seance']['id'],
                        'type_id' => $seance['Seance']['type_id'],
                        'action' => $seance['Seance']['Typeseance']['action'],
                        'libelle' => $seance['Seance']['Typeseance']['libelle'],
                        'date' => $seance['Seance']['date']);
                }
            }
            /* if (!empty($deliberations[$i]['Deliberation']['tdt_ar_date'])) {
              $deliberations[$i]['Deliberation']['tdt_ar_date'] = $this->Time->i18nFormat($deliberations[$i]['Deliberation']['tdt_ar_date'], '%d/%m/%Y à %k:%M');
              } */
        }

        $seances = $this->Seance->find('all', array(
            'conditions' => array('Seance.traitee' => 1),
            'recursive' => -1,
            'fields' => array('Seance.id', 'Seance.date')));

        foreach ($seances as $seance)
            $toutes_seances[$seance['Seance']['id']] = $seance['Seance']['date'];

        $this->_ajouterFiltre($deliberations);

        if (!empty($seance_id)) {
            $this->Filtre->delCritere('DeliberationseanceId');
            $this->Filtre->delCritere('DeliberationtypeseanceId');
        }

        $this->set('deliberations', $deliberations);
    }

    function toSend($seance_id = null) {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        App::uses('Tdt', 'Lib');
        $Tdt = new Tdt();
        $this->set('dateClassification', $Tdt->getDateClassification());
        $this->set('tabNature', $this->_getNatureListe());
        $this->set('tabMatiere', $this->_getMatiereListe());
        $this->set('nomenclatures', $Tdt->listClassification());

        if (empty($seance_id)) {
            $conditions = $this->_handleConditions($this->Filtre->conditions());
            $conditions['Deliberation.etat <'] = 5;
        } else {
            //Ajout de la condition sur séance par le filtre
            $conditions = $this->Filtre->conditions();
            $conditions['Deliberationseance.seance_id'] = $seance_id;
            $conditions = $this->_handleConditions($conditions);
        }

        $conditions['Deliberation.etat >='] = 3;
        $conditions['Deliberation.signee'] = true;
        $conditions['NOT']['Deliberation.delib_pdf'] = null;

        $conditions['Deliberation.typeacte_id'] = $this->Deliberation->Typeacte->getIdDesNaturesDelib();

        $conditions[] = 'Deliberation.id IN ('
                . 'SELECT deliberations_seances.deliberation_id'
                . ' FROM deliberations_seances '
                . ' INNER JOIN seances  ON ( seances.id=deliberations_seances.seance_id )'
                . ' INNER JOIN typeseances ON ( typeseances.id=seances.type_id )'
                . ' INNER JOIN typeactes  ON ( typeactes.id=Deliberation.typeacte_id )'
                . ' WHERE typeseances.action = 0 AND Typeacte.teletransmettre = TRUE'
                . ' )';

        $order = array('Deliberation.num_delib ASC');

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
                'Typeacte.name',
                'Circuit.nom',
                'Annex' => array(
                    'fields' => array('id', 'filename_pdf'),
                    'conditions' => array('joindre_ctrl_legalite' => true)
                ),
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

        //debug($this->_getMatiereListe());
        $optionsNumPref = array();
        foreach ($this->_getMatiereListe() as $key => $value) {
            //$val=addslashes($value);
            if (is_int($key) && !is_string($key)) {
                $groupName = $key . ' - ' . $value;
            } else
                $optionsNumPref[$groupName]["$key"] = $key . ' - ' . $value;
        }

        $this->set('optionsNumPref', $optionsNumPref);

        $this->set('deliberations', $projets);
    }

    /**
     * Tri pour les dates de séance
     */
    function _sortProjetSeanceDate(&$projets) {
        foreach ($projets as $keyProjet => $projet) {
            if (!empty($projets[$keyProjet]['Deliberationtypeseance']))
                $projets[$keyProjet]['Deliberationtypeseance'] = Hash::sort($projet['Deliberationtypeseance'], '{n}.Typeseance.action', 'asc');
            if (!empty($projets[$keyProjet]['Deliberationseance'])) {
                $projets[$keyProjet]['Deliberationseance'] = Hash::sort($projet['Deliberationseance'], '{n}.Seance.date', 'asc');
                $projets[$keyProjet]['Deliberationseance'] = Hash::sort($projets[$keyProjet]['Deliberationseance'], '{n}.Seance.Typeseance.action', 'asc');
            }
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
        $aClassification = $this->_getMatiereListe();
        if ($aClassification != false)
            $this->set('classification', $aClassification);
    }

    function _getMatiereOrdonne($matiere, $key) {
        //debug($matiere);
        if (count($matiere) == 1) {
            if (count($matiere[$key]) == 1)
                return array($matiere[$key]);
            else
                return $matiere[$key];
        }
        if (isset($matiere['@actes:CodeMatiere']))
            return array($matiere);

        $matiere = Hash::sort($matiere, '{n}.@actes:CodeMatiere', 'asc');

        return $matiere;
    }

    function _getMatiereListe() {
        $tab = array();
        if (Configure::read('TDT') == 'S2LOW') {
            try {
                $xmlObject = Xml::build(Configure::read('S2LOW_CLASSIFICATION'));
            } catch (XmlException $e) {
                //throw new InternalErrorException();
                return false;
            }

            $xmlArray = Xml::toArray($xmlObject);
            $aMatiere = $this->_getMatiereOrdonne($xmlArray['RetourClassification']['actes:Matieres'], 'actes:Matiere1');
            foreach ($aMatiere as $matiere1) {
                $tab[$matiere1['@actes:CodeMatiere']] = $matiere1['@actes:Libelle'];
                if (isset($matiere1['actes:Matiere2'])) {
                    $aMatiere2 = $this->_getMatiereOrdonne($matiere1['actes:Matiere2'], 'actes:Matiere2');
                    foreach ($aMatiere2 as $matiere2) {
                        $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere']] = $matiere2['@actes:Libelle'];
                        if (isset($matiere2['actes:Matiere3'])) {
                            $aMatiere3 = $this->_getMatiereOrdonne($matiere2['actes:Matiere3'], 'actes:Matiere3');
                            foreach ($aMatiere3 as $matiere3) {
                                $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere'] . '.' . $matiere3['@actes:CodeMatiere']] = $matiere3['@actes:Libelle'];
                                if (isset($matiere3['actes:Matiere4'])) {
                                    $aMatiere4 = $this->_getMatiereOrdonne($matiere3['actes:Matiere4'], 'actes:Matiere5');
                                    foreach ($aMatiere4 as $matiere4) {
                                        $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere'] . '.' . $matiere3['@actes:CodeMatiere'] . '.' . $matiere4['@actes:CodeMatiere']] = $matiere4['@actes:Libelle'];

                                        if (isset($matiere4['actes:Matiere5'])) {
                                            $aMatiere5 = $this->_getMatiereOrdonne($matiere4['actes:Matiere5'], 'actes:Matiere6');
                                            foreach ($aMatiere5 as $matiere5) {
                                                $tab[$matiere1['@actes:CodeMatiere'] . '.' . $matiere2['@actes:CodeMatiere'] . '.' . $matiere3['@actes:CodeMatiere'] . '.' . $matiere4['@actes:CodeMatiere'] . '.' . $matiere5['@actes:CodeMatiere']] = $matiere5['@actes:Libelle'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else { //TODO test
            App::uses('Tdt', 'Lib');
            $Tdt = new Tdt();
            $tab = $Tdt->listClassification();
        }

        return $tab;
    }

    /** Retourne la matière par rapport a une clé donnée en parametre
     * 
     * @param type $key
     * @return String
     */
    function _getMatiereByKey($key) {
        if (Configure::read('TDT') == 'S2LOW') {
            $aMatiere = $this->_getMatiereListe();
            return isset($aMatiere[$key]) ? $aMatiere[$key] : NULL;
        } else {
            App::uses('Nomenclature', 'Model');
            $Nomenclature = new Nomenclature();
            $Nomenclature->id = $key;
            return $Nomenclature->field('libelle');
        }
    }

    function _object2array($object) {
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

    /**
     * Envoi par lot de deliberations au TDT (s2low ou pastell)
     * @return mixed
     */
    function sendToTdt() {
        App::uses('Tdt', 'Lib');
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');

        if (!Configure::read('USE_TDT')) {
            $this->Session->setFlash('Erreur : TDT désactivé. Pour activer ce service, veuillez contacter votre administrateur.', 'growl', array('type' => 'erreurTDT'));
            return $this->redirect($this->referer());
        }
        $Tdt = new Tdt();
        $erreur = '';
        //Cases sélectionnées ?
        if (!empty($this->data['Deliberation'])) {
            $aDelibToSend = array();
            foreach ($this->data['Deliberation'] as $id => $dataDeliberation) {
                $this->log($id, 'debug');
                $this->log($dataDeliberation['num_pref'], 'debug');
                $this->Deliberation->id = $id;
                $this->Deliberation->saveField('num_pref', $dataDeliberation['num_pref']);

                if (!empty($dataDeliberation['send']) && $dataDeliberation == true)
                    $aDelibToSend[] = $id;
            }

            try {
                $nbEnvoyee = 0;
                //Pour chaque délib
                foreach ($aDelibToSend as $delib_id) {
                    //Si non cochée passer
                    $deliberation = $this->Deliberation->find('first', array(
                        'conditions' => array('Deliberation.id' => $delib_id)
                    ));

                    if (empty($delib['num_pref'])) {
                        throw new Exception($this->Deliberation->field('objet_delib') . ' (' . $this->Deliberation->field('num_delib') . ') : Aucune classification sélectionnée.');
                    }

                    if (Configure::read('TDT') == 'PASTELL' && Configure::read('USE_PASTELL')) {
                        try {
                            if (empty($acte['Deliberation']['pastell_id'])) {
                                $sent = $Tdt->send($deliberation, $deliberation['Deliberation']['delib_pdf'], $this->Deliberation->getAnnexesToSend($deliberation['Deliberation']['id']));
                            } else
                                $sent = $Tdt->send($deliberation);
                            if ($sent) {
                                $this->Deliberation->saveField('etat', 5);
                                $this->Historique->enregistre($delib_id, $this->user_id, 'Acte envoyé au tiers de télétransmission');
                            }
                        } catch (Exception $e) {
                            throw new Exception($this->Deliberation->field('num_delib') . ' :' . $e->getMessage());
                        }
                    } elseif (Configure::read('TDT') == 'S2LOW' && Configure::read('USE_S2LOW')) {
                        $typeacte = $this->Deliberation->Typeacte->find('first', array(
                            'conditions' => array('Typeacte.id' => $deliberation['Typeacte']['id']),
                            'contain' => array('Nature.code')
                        ));
                        switch ($typeacte['Nature']['code']) {
                            case 'DE':
                                $nature_code = 1;
                                break;
                            case 'AR':
                                $nature_code = 2;
                                break;
                            case 'AI':
                                $nature_code = 3;
                                break;
                            case 'CC':
                                $nature_code = 4;
                                break;
                            case 'AU':
                                $nature_code = 5;
                                break;
                            default:
                                continue;
                        }

                        $classification = $deliberation['Deliberation']['num_pref'];
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

                        if (empty($deliberation['Deliberation']['delib_pdf'])) {
                            throw new Exception($deliberation['Deliberation']['objet_delib'] . ' (' . $deliberation['Deliberation']['num_delib'] . ') : Fichier Vide.');
                        }
                        $folder = new Folder(AppTools::newTmpDir(TMP . 'files' . DS . 'tdt' . DS), true, 0777);
                        $errors = $folder->errors();
                        if (!empty($errors)) {
                            throw new Exception($deliberation['Deliberation']['objet_delib'] . ' (' . $deliberation['Deliberation']['num_delib'] . ') : ' . implode($errors) . '.');
                        }

                        $file = new File($folder->pwd() . DS . 'D_' . $deliberation_id . '.pdf');
                        $file->write($deliberation['Deliberation']['delib_pdf']);
                        $file->close();

                        if (!empty($deliberation['Deliberation']['signature'])) {
                            $fileSignature = new File($folder->pwd() . DS . 'signature_' . $delib_id . '.zip');
                            $fileSignature->write($deliberation['Deliberation']['signature']);

                            $zip = new ZipArchive();
                            $res = $zip->open($fileSignature->pwd(), ZIPARCHIVE::OVERWRITE);
                            if ($res === TRUE) {
                                $zip->extractTo($folder->pwd() . DS, array('signature.pkcs7'));
                                $zip->close();
                            }
                            $fileSignature->close();
                        }
                        // Checker le code classification
                        if (isset($deliberation['Deliberation']['date_acte']))
                            $decision_date = date("Y-m-d", strtotime($deliberation['Deliberation']['date_acte']));
                        else {
                            $seances = array();
                            foreach ($deliberation['Seance'] as $seance)
                                $seances[] = $seance['id'];
                            $this->Seance->id = $this->Seance->getSeanceDeliberante($seances);
                            $decision_date = date("Y-m-d", strtotime($this->Seance->field('date')));
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
                            'number' => utf8_decode($deliberation['Deliberation']['num_delib']),
                            'decision_date' => $decision_date,
                            'subject' => utf8_decode($deliberation['Deliberation']['objet_delib']),
                            'acte_pdf_file' => '@' . $file->pwd(),
                        );

                        if (file_exists($folder->pwd() . DS . 'signature.pkcs7')) {
                            $acte['acte_pdf_file_sign'] = '@' . $folder->pwd() . DS . 'signature.pkcs7';
                        }

                        $annexes = $this->Deliberation->getAnnexesToSend($delib_id);
                        if (!empty($annexes)) {
                            $acte['acte_attachments_sign[' . count($annexes) . ']'] = "";
                            foreach ($annexes as $key => $annex) {
                                $fileAnnexe = new File($folder->pwd() . DS . $annex['filename']);
                                $fileAnnexe->append($annex['content']);
                                $acte["acte_attachments[$key]"] = '@' . $fileAnnexe->pwd();
                                $file->close();
                            }
                        }

                        $curl_return = utf8_encode($this->S2low->send($acte));
                        $pos = strpos($curl_return, 'OK');
                        $tdt_id = substr($curl_return, 3, strlen($curl_return));
                        if ($pos === false) {
                            $order = array("\r\n", "\n", "\r");
                            $replace = '<br />';
                            $curl_return = str_replace($order, $replace, $curl_return);
                            throw new Exception($deliberation['Deliberation']['objet_delib'] . ' (' . $deliberation['Deliberation']['num_delib'] . ') : ' . $curl_return);
                        } else {
                            $nbEnvoyee++;
                            $this->Deliberation->saveField('etat', 5);
                            $this->Deliberation->saveField('tdt_id', trim($tdt_id));
                            $this->Historique->enregistre($delib_id, $this->user_id, 'Acte envoyé au tiers de télétransmission');
                        }
                        $folder->delete();
                        sleep(5);
                    } else {
                        throw new Exception('Aucun connecteur TDT valide !');
                    }
                }
                $this->Session->setFlash($nbEnvoyee . ' Acte(s) envoyé(s) correctement au TdT', 'growl', array('type' => 'info'));
            } catch (Exception $e) {
                if (isset($folder))
                    $folder->delete(); //Purge du dernier dossier en cas d'erreur
                $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'error'));
            }
        } else
            $this->Session->setFlash('Aucun Acte(s) selectionné(s)', 'growl', array('type' => 'error'));

        return $this->redirect($this->referer());
    }

    function getClassification() {
        App::uses('Tdt', 'Lib');
        $this->Tdt = new Tdt();
        if ($this->Tdt->updateClassification()) {
            $this->Session->setFlash('Les données de classification sont à jour', 'growl');
            return $this->redirect(array('action' => 'toSend'));
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
        return $this->redirect(array('controller' => 'seances', 'action' => 'afficherProjets', $seance_id));
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

    function _getListPresent($delib_id) {
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
              } */
        }
        return $acteurs;
    }

    function listerPresents($delib_id, $seance_id) {
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
            return $this->redirect(array('controller' => 'seances', 'action' => 'voter', $delib_id, $seance_id));
        }
    }

    function _reporteDelibs($delib_id) {
        $seance_id = $this->Deliberation->getCurrentSeance($delib_id);
        $position = $this->Deliberation->getCurrentPosition($delib_id);
        $conditions = "Deliberation.seance_id=$seance_id AND Deliberation.position>=$position";
        $delibs = $this->Deliberation->findAll($conditions);
        foreach ($delibs as $delib)
            $this->Deliberation->changeSeance($delib['Deliberation']['id'], 0);
        $this->Session->setFlash("Le quorum n'est plus atteint...", 'growl', array('type' => 'erreur'));
        return $this->redirect(array('controller' => 'seances', 'action' => 'listerFuturesSeances'));
    }

    /**
     * Duplique le projet d'on l'id est passé, duplique les 
     * informations de la table délibération et infosup, un petit ménage est fait
     * 
     * @param type $id id de la délibération
     */
    function duplicate($id = null){
        $newId = $this->Deliberation->duplicate($id);
        //$previous = $this->Session->read('User');
        $this->Session->setFlash('Le projet a été dupliqué.', 'growl', array('type' => 'info'));
        //$this->redirect(array('controller' => $previous['History']['data'][1]['controller'], 'action' => $previous['History']['data'][1]['action'], !empty($previous['History']['data'][1][0])?$previous['History']['data'][1][0]:'' ));
        $buff = $this->Deliberation->Infosup->duplicateById($id,$newId);
        $this->redirect($this->previous);
    }
    /*
     * Affiche la liste des projets en cours de redaction (etat = 0) dont l'utilisateur connecté
     * est le rédacteur.
     */

    function mesProjetsRedaction() {
        
        if (isset($this->params['render']) && ($this->params['render'] == 'banette')) {
            $limit = Configure::read('LIMIT');
        } else {
            $limit = null;
        }

        //Choix du rendu à appliquer
        if (isset($this->params['render'])) {
            $render = $this->params['render'];
        } else {
            $render = 'index';
        }

        // Gestion par lot
        if ($render != 'banette') {
            $this->set('traitement_lot', true);
        }
        $this->set('actions_possibles', array('suppression' => 'Suppression'));
        $this->set('modeles', $this->Modeltemplate->find('list', array(
                    'recursive' => -1,
                    'fields' => array('Modeltemplate.name'),
                    'conditions' => array('Modeltemplate.modeltype_id' => array(MODEL_TYPE_RECHERCHE))
        )));

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->load('Containable');

        $listeLiens = $this->Acl->check(array('User' => array('id' => $this->Auth->user('id'))), 'Deliberations', 'create') ? array('add') : array();

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        //$conditions =  $this->Filtre->conditions();
        //debug($this->Auth->user());exit;
        //$this->Acl->check( array('User' => array('id' => $this->Auth->user('id'))), 'Deliberations', 'update');
        
        $conditions['Deliberation.etat'] = 0;
        $conditions['Deliberation.redacteur_id'] = $this->user_id;
        $ordre = array('Deliberation.id' => 'DESC');
        $redacteur = $this->Deliberation->Redacteur->find('all',array('fields' => 'id','contain' => array('Deliberation' => array('conditions' => array('Deliberation.etat' => 0))),'conditions' => array('Redacteur.id' => $this->user_id)));
        
        $id = array();
        foreach ($redacteur[0]['Deliberation'] as $deliberation_id){
            $id[] =  $deliberation_id['id'];
        }
        if(empty($id)){
            $conditions['Deliberation.parent_id'] = null;
        }else{
            if(count($id) != 1){
               $conditions['Deliberation.parent_id IS NULL OR "Deliberation"."id" IN '] =  $id ;
            }else{
               $conditions['Deliberation.parent_id IS NULL OR "Deliberation"."id"'] = $id;
            }
        }
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions));
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
                'Typeacte' => array('fields' => array('name')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action')))),
                ),
            'order' => $ordre));
        $this->_sortProjetSeanceDate($projets);
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($render, $projets, 'Mes projets en cours de rédaction', array('view', 'edit', 'delete', 'attribuerCircuit', 'generer'), $listeLiens, $nbProjets
        );
    }

    /**
     * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
     * de validation de l'utilisateur connecté et dont le tour de validation est venu.
     */
    function mesProjetsATraiter() {
        if (isset($this->params['filtre']) && ($this->params['filtre'] == 'hide'))
            $limit = intval(Configure::read('LIMIT'));
        else
            $limit = null;

        //Choix du rendu à appliquer
        if (isset($this->params['render']))
            $render = $this->params['render'];
        else
            $render = 'index';

        if ($render != 'banette') {
            $this->set('traitement_lot', true);
        }
        $this->set('actions_possibles', array('valider' => 'Valider', 'refuser' => 'Refuser'));
        $this->set('modeles', $this->Modeltemplate->find('list', array(
                    'recursive' => -1,
                    'fields' => array('Modeltemplate.name'),
                    'conditions' => array('modeltype_id' => array(MODEL_TYPE_TOUTES, MODEL_TYPE_RECHERCHE))
        )));

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $conditions['Deliberation.etat'] = 1;
        $delibs_ids = $this->Traitement->listeTargetId($this->user_id, array(
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
                'Typeacte' => array('fields' => array('name')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'))))),
            'order' => array('Deliberation.id' => 'DESC')));

        $this->_sortProjetSeanceDate($projets);
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($render, $projets, 'Mes projets &agrave; traiter', array('view', 'traiter', 'generer'), array(), $nbProjets);
    }

    /*
     * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
     * de validation de l'utilisateur connecté et dont ce n'est pas le tour de valider et les projets
     * dont il est le rédacteur
     */

    function mesProjetsValidation() {
        if (isset($this->params['render']) && ($this->params['render'] == 'banette'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        //Choix du rendu à appliquer
        if (isset($this->params['render']))
            $render = $this->params['render'];
        else
            $render = 'index';

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->load('Containable');

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $conditions['Deliberation.etat'] = 1;

        $delibs_ids = $this->Traitement->listeTargetId($this->user_id, array('etat' => 'NONTRAITE', 'traitement' => 'NONAFAIRE'));
        if (isset($conditions['Deliberation.id'])) {
            $conditions['OR']['Deliberation.id'] = array_intersect($conditions['Deliberation.id'], $delibs_ids);
        } else {
            $conditions['OR']['Deliberation.id'] = $delibs_ids;
        }
        $conditions['OR']['Deliberation.redacteur_id'] = $this->user_id;
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
                'Typeacte' => array('fields' => array('name')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'))),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'))))),
            'order' => $ordre));

        $this->_sortProjetSeanceDate($projets);
        $this->_ajouterFiltre($projets);
        $nbProjets = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));

        $this->_afficheProjets($render, $projets, 'Mes projets en cours d\'élaboration et de validation', array('view', 'generer'), array(), $nbProjets);
    }

    /*
     * Affiche les projets validés (etat = 2) dont l'utilisateur connecté est le rédacteur
     * ou qu'il est dans les circuits de validation des projets
     */

    function mesProjetsValides() {
        if (isset($this->params['render']) && ($this->params['render'] == 'banette'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $this->Deliberation->Behaviors->load('Containable');

        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = 2;
        $conditions['OR']['Deliberation.id'] = $this->Traitement->listeTargetId(
                $this->user_id, array(
            'etat' => 'TRAITE',
            'targetConditions' => array('Deliberation.etat' => 2)
        ));
        $conditions['OR']['Deliberation.redacteur_id'] = $this->user_id;

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
        $this->_afficheProjets('index', $projets, 'Mes projets validés', array('view', 'generer'));
    }

    /**
     * fonction générique pour afficher les projets sour forme d'index
     */
    function _afficheProjets($render = 'index', &$projets, $titreVue, $listeActions, $listeLiens = array(), $nbProjets = null) {
        // initialisation de l'utilisateur connecté et des droits
        $this->set('typeseances', $this->Seance->Typeseance->find('list', array('recursive' => -1)));

        $editerTous = $this->Acl->check( array('User' => array('id' => $this->Auth->user('id'))), 'Deliberations', 'update');
        
        $this->request->data = $projets;

        /* initialisation pour chaque projet ou délibération */
        foreach ($this->request->data as $i => $projet) {
            // initialisation des icônes
            if (isset($projet[0]))
                $projet['Deliberation'] = $projet[0];
            $this->request->data[$i]['last_viseur'] = $this->Traitement->getLastVisaTrigger($projet['Deliberation']['id']);
            $this->request->data[$i]['Deliberation']['num_pref'] = $this->request->data[$i]['Deliberation']['num_pref'] . ' - ' . $this->_getMatiereByKey($this->request->data[$i]['Deliberation']['num_pref']);
                            
            if ($projet['Deliberation']['etat'] == 0 && $projet['Deliberation']['anterieure_id'] != 0)
                $this->request->data[$i]['iconeEtat'] = $this->_iconeEtat($projet['Deliberation']['id'], -2);
            elseif ($projet['Deliberation']['etat'] == 1) {
                $estDansCircuit = $this->Traitement->triggerDansTraitementCible($this->user_id, $projet['Deliberation']['id']);
                $tourDansCircuit = $estDansCircuit ? $this->Traitement->positionTrigger($this->user_id, $projet['Deliberation']['id']) : 0;
                $estRedacteur = ($this->user_id == $projet['Deliberation']['redacteur_id']);
                $this->request->data[$i]['iconeEtat'] = $this->_iconeEtat($projet['Deliberation']['id'], 1, false, $estDansCircuit, $estRedacteur, $tourDansCircuit);
            } else {
                $this->request->data[$i]['iconeEtat'] = $this->_iconeEtat($projet['Deliberation']['id'], $projet['Deliberation']['etat'], $editerTous);
            }

            // initialisation des séances
            $listeTypeSeance = array();
            $this->request->data[$i]['listeSeances'] = array();
            if (isset($projet['Deliberationseance']) && !empty($projet['Deliberationseance'])) {
                foreach ($projet['Deliberationseance'] as $keySeance => $seance) {
                    if(!empty($seance['Seance']['id'])){
                    $this->request->data[$i]['listeSeances'][] = array('seance_id' => $seance['Seance']['id'],
                        'type_id' => $seance['Seance']['type_id'],
                        'color' => !empty($seance['Seance']['Typeseance']['color'])?$seance['Seance']['Typeseance']['color']:'',
                        'action' => $seance['Seance']['Typeseance']['action'],
                        'libelle' => $seance['Seance']['Typeseance']['libelle'],
                        'date' => $seance['Seance']['date']);
                    $listeTypeSeance[] = $seance['Seance']['type_id'];
                    }
                }
            }
            if (isset($projet['Deliberationtypeseance']) && !empty($projet['Deliberationtypeseance'])) {
                foreach ($projet['Deliberationtypeseance'] as $keyType => $typeseance) {
                    if (!in_array($typeseance['Typeseance']['id'], $listeTypeSeance))
                        $this->request->data[$i]['listeSeances'][] = array('seance_id' => NULL,
                            'type_id' => $typeseance['Typeseance']['id'],
                            'color' => !empty($typeseance['Typeseance']['color'])?$typeseance['Typeseance']['color']:'',
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
                $this->request->data[$i]['Actions'][] = 'attribuerCircuit';
            }
            if ($projet['Deliberation']['etat'] < 3 && $editerTous) {
                $this->request->data[$i]['Actions'][] = 'edit';
            }
            if (!in_array('generer', $this->request->data[$i]['Actions']) && $projet['Deliberation']['signee']) {
                $this->request->data[$i]['Actions'][] = 'telecharger';
            }
            if (!in_array('telecharger', $this->request->data[$i]['Actions']))
                $this->request->data[$i]['Actions'][] = 'generer';
            // initialisation des dates, modèle et service
            $seances_id = array();

            if (isset($this->request->data[$i]['listeSeances']) && !empty($this->request->data[$i]['listeSeances']))
                foreach ($this->request->data[$i]['listeSeances'] as $seance) {
                    if ($seance['action'] === 0) {
                        $this->request->data[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['type_id'], $projet['Deliberation']['etat']);
                        break;
                    }
                }
            if (!isset($this->request->data[$i]['Modeltemplate']['id'])) {
                $this->request->data[$i]['Modeltemplate']['id'] = $this->Deliberation->Typeacte->getModelId($projet['Deliberation']['typeacte_id'], 'modeleprojet_id');
            }

            if (isset($this->data[$i]['Service']['id']))
                $this->request->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($projet['Service']['id']);
            if (isset($this->data[$i]['Deliberation']['date_limite']))
                $this->request->data[$i]['Deliberation']['date_limite'] = $projet['Deliberation']['date_limite'];
        }

        // passage des variables à la vue
        $this->set('titreVue', $titreVue);
        $this->set('listeLiens', $listeLiens);
        if ($nbProjets == null)
            $nbProjets = count($projets);
        $this->set('nbProjets', $nbProjets);
        $this->render($render);
    }

    /**
     * Affiche la liste de tous les projets dont le rédacteur fait parti de mon/mes services
     * Permet de valider en urgence un projet
     */
    function projetsMonService() {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
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
            'contain' => array('Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'))))),
            'order' => array($ordre)));
        $this->_sortProjetSeanceDate($projets);
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->user_id, "Deliberations:validerEnUrgence"))
            array_push($actions, 'validerEnUrgence');
        if ($this->Droits->check($this->user_id, "Deliberations:goNext"))
            array_push($actions, 'goNext');
        if ($this->Droits->check($this->user_id, "Deliberations:delete"))
            array_push($actions, 'delete');


        $this->_ajouterFiltre($projets);
        $this->_afficheProjets('index', $projets, 'Projets dont le rédacteur fait partie de mon service', $actions);
    }

    /*
     * Affiche la liste de tous les projets en cours de validation
     * Permet de valider en urgence un projet
     */

    function tousLesProjetsValidation() {
        $this->set('traitement_lot', true);
        $this->set('actions_possibles', array('validerUrgence' => 'Valider en urgence'));
        $this->set('modeles', $this->Modeltemplate->find('list', array(
                    'recursive' => -1,
                    'fields' => array('Modeltemplate.name'),
                    'conditions' => array('modeltype_id' => array(MODEL_TYPE_TOUTES, MODEL_TYPE_RECHERCHE))
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
            'contain' => array('Service' => array('fields' => array('libelle')),
                'Theme' => array('fields' => array('libelle')),
                'Typeacte' => array('fields' => array('libelle')),
                'Circuit' => array('fields' => array('nom')),
                'Deliberationtypeseance' => array('fields' => array('id'),
                    'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'),
                    )),
                'Deliberationseance' => array('fields' => array('id'),
                    'Seance' => array('fields' => array('id', 'date', 'type_id'),
                        'Typeseance' => array('fields' => array('id', 'libelle', 'color', 'action'))))),
            'order' => array($ordre)));
        $this->_sortProjetSeanceDate($projets);
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->user_id, "Deliberations:validerEnUrgence"))
            array_push($actions, 'validerEnUrgence');
        if ($this->Droits->check($this->user_id, "Deliberations:goNext"))
            array_push($actions, 'goNext');
        if ($this->Droits->check($this->user_id, "Deliberations:delete"))
            array_push($actions, 'delete');
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets('index', $projets, 'Projets en cours d\'élaboration et de validation', $actions);
    }

    /*
     * Affiche la liste de tous les projets en cours de redaction, validation, validés sans séance
     * Permet de modifier un projet validé si l'utilisateur à les droits editerTous
     */

    function tousLesProjetsSansSeance() {
        $canEditAll = $this->Droits->check($this->user_id, "Deliberations:editerTous");
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
        if ($this->Droits->check($this->user_id, "Deliberations:delete"))
            array_push($actions, 'delete');

        $this->_afficheProjets('index', $delibs, 'Projets non associés &agrave; une séance', $actions);
    }

    /*
     * Affiche la liste de tous les projets validés liés à une séance
     */

    function tousLesProjetsAFaireVoter() {
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
            'order' => array('Deliberation.created DESC')));
        $this->_sortProjetSeanceDate($projets);
        $this->_ajouterFiltre($projets);
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->user_id, "Deliberations:delete"))
            array_push($actions, 'delete');

        $this->_afficheProjets('index', $projets, 'Projets validés associés &agrave; une séance', $actions);
    }

    function _ajouterFiltre(&$projets) {
        if (!$this->Filtre->critereExists()) {
            $Deliberationseances = array();
            foreach ($projets as $projet) {
                if (!empty($projet['Deliberationseance'])) {
                    foreach ($projet['Deliberationseance'] as $Deliberationseance)
                        if (!array_key_exists($Deliberationseance['Seance']['id'], $Deliberationseances))
                            $Deliberationseances[$Deliberationseance['Seance']['id']] = $Deliberationseance['Seance']['Typeseance']['libelle'] . ' : ' . date('d/m/Y \à H:i:s', strtotime($Deliberationseance['Seance']['date']));
                }
            }
            $this->Filtre->addCritere('DeliberationseanceId', array('field' => 'Deliberationseance.seance_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Séances', true),
                    'empty' => 'Toutes',
                    'options' => $Deliberationseances)));
            $typeseances = array();
            foreach ($projets as $projet) {
                if (!empty($projet['Deliberationtypeseance'])) {
                    foreach ($projet['Deliberationtypeseance'] as $typeseance)
                        if (!array_key_exists($typeseance['id'], $typeseances))
                            $typeseances[$typeseance['Typeseance']['id']] = $typeseance['Typeseance']['libelle'];
                }
            }
            $this->Filtre->addCritere('DeliberationtypeseanceId', array(
                'field' => 'Deliberationtypeseance.typeseance_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Type de séance', true),
                    'options' => $typeseances)));

            /* $this->Filtre->addCritere('DeliberationseanceId', array('field' => 'Deliberationseance.seance_id',
              'classeDiv' => 'demi',
              'retourLigne' => true,
              'inputOptions' => array(
              'label' => __('Séances', true),
              'empty' => 'toutes',
              'options' => $this->Deliberation->getSeancesFromArray($projets)))); */

            $this->Filtre->addCritere('Typeacte', array(
                'field' => 'Deliberation.typeacte_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Type d\'acte', true),
                    'empty' => 'tous',
                    'options' => Hash::combine($projets, '{n}.Deliberation.typeacte_id', '{n}.Typeacte.name')
            )));
            $this->Filtre->addCritere('ThemeId', array(
                'field' => 'Deliberation.theme_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Thème', true),
                    'options' => Hash::combine($projets, '{n}.Deliberation.theme_id', '{n}.Theme.libelle')
            )));
            $this->Filtre->addCritere('ServiceId', array(
                'field' => 'Deliberation.service_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Service émetteur', true),
                    'multiple' => true,
                    'options' => Hash::combine($projets, '{n}.Deliberation.service_id', '{n}.Service.libelle')
            )));

            $this->Filtre->addCritere('CircuitId', array(
                'field' => 'Deliberation.circuit_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Circuit de validation', true),
                    'empty' => 'Tous',
                    'options' => Hash::combine($projets, '{n}.Deliberation.circuit_id', '{n}.Circuit.nom')
            )));
        }
    }

    function _ajouterFiltreSeance(&$projets) {
        if (!$this->Filtre->critereExists()) {
            $this->Filtre->addCritere('Typeacte', array(
                'field' => 'Deliberation.typeacte_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Type d\'acte', true),
                    'empty' => 'tous',
                    'options' => Hash::combine($projets, '{n}.Deliberation.typeacte_id', '{n}.Deliberation.Typeacte.name')
            )));
            $this->Filtre->addCritere('ThemeId', array(
                'field' => 'Deliberation.theme_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Thème', true),
                    'options' => Hash::combine($projets, '{n}.Deliberation.theme_id', '{n}.Deliberation.Theme.libelle')
            )));
            $this->Filtre->addCritere('ServiceId', array(
                'field' => 'Deliberation.service_id',
                'classeDiv' => 'demi',
                'inputOptions' => array(
                    'label' => __('Service émetteur', true),
                    'multiple' => true,
                    'options' => Hash::combine($projets, '{n}.Deliberation.service_id', '{n}.Service.libelle')
            )));

            $this->Filtre->addCritere('CircuitId', array(
                'field' => 'Deliberation.circuit_id',
                'classeDiv' => 'demi',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Circuit de validation', true),
                    'empty' => 'Tous',
                    'options' => Hash::combine($projets, '{n}.Deliberation.circuit_id[circuit_id!=0]', '{n}.Deliberation.Circuit.nom')
            )));
        }
    }

    /*
     * Attribue une séance à un projet
     * Appelée depuis la vue deliberations/tous_les_projets
     */

    function attribuerSeance() {
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
        return $this->redirect(array('action' => 'tousLesProjetsSansSeance'));
    }

    /*
     * Permet de valider un projet en cours de validation en court-circuitant le circuit de validation
     * Appelée depuis la vue deliberations/tous_les_projets
     */

    function validerEnUrgence($delibId, $redirect = true) {
        // Lecture de la délibération
        $this->Deliberation->recursive = -1;
        $this->request->data = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $delibId),
            'fields' => array('Deliberation.id', 'Deliberation.etat', 'Deliberation.parapheur_etat'),
            'recursive' => -1));
        if (empty($this->data))
            $this->Session->setFlash('Invalide id pour le projet de délibération.', 'growl', array('type' => 'erreur'));
        else {
            if ($this->data['Deliberation']['etat'] != 1)
                $this->Session->setFlash('Le projet doit être dans un circuit pour être validé.', 'growl', array('type' => 'erreur'));
            elseif ($this->data['Deliberation']['parapheur_etat'] == 1) {
                $this->Session->setFlash('Le projet est dans une étape parapheur, il ne peut être validé en urgence.', 'growl', array('type' => 'erreur'));
            } else {
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
                                    'trigger_id' => $this->user_id,
                                    'type_validation' => 'V'
                )))));
                $this->Traitement->execute('ST', $this->user_id, $delibId, $options);
                $this->Deliberation->id = $delibId;
                $this->Deliberation->saveField('etat', 2);
                $this->Deliberation->saveField('parapheur_etat', 0);
                $this->Historique->enregistre($delibId, $this->user_id, 'Validation en urgence');
                $this->Session->setFlash('Le projet ' . $this->data['Deliberation']['id'] . ' a été validé en urgence.', 'growl');
            }
        }
        if ($redirect)
            $this->redirect($this->previous);
    }

    function mesProjetsRecherche() {
        if (empty($this->data)) {
            $this->set('action', array('controller' => 'deliberations', 'action' => 'mesProjetsRecherche'));
            $this->set('titreVue', 'Recherche multi-critères parmi mes projets');

            $this->set('rapporteurs', $this->Acteur->generateListElus());
            //  $this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
            $this->set('date_seances', $this->Seance->generateAllList());
            $this->set('services', $this->Deliberation->Service->generateTreeList(null, null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
            $this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
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
                        'conditions' => array('modeltype_id' => array(MODEL_TYPE_RECHERCHE)),
                        'fields' => array('Modeltemplate.id', 'Modeltemplate.name'))));

            $this->render('rechercheMultiCriteres');
        } else {
            $conditions = array();
            $multiseances = array();

            if (!empty($this->data['Deliberation']['id'])) {
                if (!is_numeric($this->data['Deliberation']['id'])) {
                    $this->Session->setFlash('Vous devez saisir un identifiant valide', 'growl', array('type' => 'erreur'));
                    $this->redirect(array('action' => 'mesProjetsRecherche'));
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
                $conditions['AND']["OR"]["Deliberation.objet ILIKE"] = '%'.$texte.'%';
                $conditions['AND']["OR"]["Deliberation.titre ILIKE"] = '%'.$texte.'%';
            }
            if (!empty($this->data['Deliberation']['dateDebut'])) {
                $conditions['Deliberation.created >= DATE'] = $this->data['Deliberation']['dateDebut'];
            }
            if (!empty($this->data['Deliberation']['dateFin'])) {
                $conditions['Deliberation.created <= DATE'] = $this->data['Deliberation']['dateFin'];
            }
            if (!empty($this->data['Deliberation']['dateDebutAr'])) {
                $conditions['Deliberation.tdt_ar_date >= DATE'] = $this->data['Deliberation']['dateDebutAr'];
            }
            if (!empty($this->data['Deliberation']['dateFinAr'])) {
                $conditions['Deliberation.tdt_ar_date <= DATE'] = $this->data['Deliberation']['dateFinAr'];
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
                $this->redirect(array('action' => 'mesProjetsRecherche'));
            } else {
                $listeCircuits = explode(',', $this->Circuit->listeCircuitsParUtilisateur($this->user_id));
                if (!empty($listeCircuits))
                    $conditions['OR']['Deliberation.circuit_id'] = $listeCircuits;
                $conditions['OR']['Deliberation.redacteur_id'] = $this->user_id;
                //Récupère la liste des délib que l'utilisateur a visé (résolution bug changement circuit non visible)
                $listeDelibsParticipe = explode(',', $this->Traitement->getListTargetByTrigger($this->user_id));
                if (!empty($listeDelibsParticipe))
                    $conditions['OR']['Deliberation.id'] = $listeDelibsParticipe;

                //TODO on peut voir certain projet mecanique à revoir
                $this->Deliberation->Behaviors->load('Containable');
                $projets = $this->Deliberation->find('all', array(
                    'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                        'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                        'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                        'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
                    'conditions' => $conditions,
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
                    'order' => 'Deliberation.id DESC'));
                $this->_sortProjetSeanceDate($projets);

                if ($this->data['Deliberation']['generer'] == 0) {
                    $this->_afficheProjets('index', $projets, 'Résultat de la recherche parmi mes projets', array('view', 'generer'), array('mesProjetsRecherche'));
                } else {
                    if (count($projets) > 0) {
                        $deliberationIds = array();
                        foreach ($projets as &$projet)
                            $deliberationIds[] = $projet['Deliberation']['id'];
                        return $this->_genereFusionRechercheToClient($deliberationIds, $this->data['Deliberation']['model'], $this->data['waiter']['token']);
                    } else {
                        $this->Session->setFlash('Aucun résultat à la recherche effectuée.', 'growl', array('type' => 'erreur'));
                        $this->redirect(array('action' => 'mesProjetsRecherche'));
                    }
                }
            }
        }
    }

    function tousLesProjetsRecherche() {

        if (empty($this->data)) {
            $this->set('action', array('controller' => 'deliberations', 'action' => 'tousLesProjetsRecherche'));
            $this->set('titreVue', 'Recherche multi-critères parmi tous les projets');

            $this->set('rapporteurs', $this->Acteur->generateListElus());
            //    $this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
            $this->set('date_seances', $this->Seance->generateAllList());
            $this->set('services', $this->Deliberation->Service->generateTreeList(null, null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
            $this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
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
                        'conditions' => array('modeltype_id' => array(MODEL_TYPE_RECHERCHE)),
                        'fields' => array('Modeltemplate.id', 'Modeltemplate.name'))));
            $this->set('listeBoolean', $this->Infosupdef->listSelectBoolean);

            $this->render('rechercheMultiCriteres');
        } else {
            $conditions = array();
            if (!empty($this->data['Deliberation']['id'])) {
                if (!is_numeric($this->data['Deliberation']['id'])) {
                    $this->Session->setFlash('Vous devez saisir un identifiant valide', 'growl', array('type' => 'erreur'));
                    $this->redirect(array('action' => 'mesProjetsRecherche'));
                }
                $conditions["Deliberation.id"] = $this->data['Deliberation']['id'];
            }

            if (!empty($this->data['Deliberation']['rapporteur_id']))
                $conditions["Deliberation.rapporteur_id"] = $this->data['Deliberation']['rapporteur_id'];
            if (!empty($this->data['Deliberation']['service_id']))
                $conditions['Deliberation.service_id'] = $this->User->Service->doListId($this->data['Deliberation']['service_id']);
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
                $conditions["OR"]["Deliberation.objet ILIKE"] = '%'.$texte.'%';
                $conditions["OR"]["Deliberation.titre ILIKE"] = '%'.$texte.'%';
            }
            if (!empty($this->data['Deliberation']['dateDebut'])) {
                $conditions['Deliberation.created >= DATE'] = $this->data['Deliberation']['dateDebut'];
            }
            if (!empty($this->data['Deliberation']['dateFin'])) {
                $conditions['Deliberation.created <= DATE'] = $this->data['Deliberation']['dateFin'];
            }
            if (!empty($this->data['Deliberation']['dateDebutAr'])) {
                $conditions['Deliberation.tdt_ar_date >= DATE'] = $this->data['Deliberation']['dateDebutAr'];
            }
            if (!empty($this->data['Deliberation']['dateFinAr'])) {
                $conditions['Deliberation.tdt_ar_date <= DATE'] = $this->data['Deliberation']['dateFinAr'];
            }
            if (empty($conditions["Deliberation.id"]) && !empty($this->data['Deliberation']['seance_id'])) {
                $multiseances = array();
                foreach ($this->data['Deliberation']['seance_id'] as $seance_id) {
                    $projet_ids = $this->Seance->getDeliberationsId($seance_id);
                    $multiseances = array_merge($projet_ids, $multiseances);
                }
                $conditions['Deliberation.id'] = $multiseances;
            }
            if (array_key_exists('Infosup', $this->data)) {
                $rechercheInfoSup = $this->Deliberation->Infosup->selectInfosup($this->data['Infosup']);
                if (!empty($rechercheInfoSup))
                    $conditions["Deliberation.id"] = $rechercheInfoSup;
            }
            if (empty($conditions)) {
                $this->Session->setFlash('Vous devez saisir au moins un critère.', 'growl', array('type' => 'erreur'));
                $this->redirect(array('action' => 'tousLesProjetsRecherche'));
            } else {
                // lecture en base
                $projets = $this->Deliberation->find('all', array(
                    'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                        'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                        'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                        'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
                    'contain' => array('Service' => array('fields' => array('libelle')),
                        'Theme' => array('fields' => array('libelle')),
                        'Typeacte' => array('fields' => array('libelle')),
                        'Circuit' => array('fields' => array('nom')),
                        'Deliberationtypeseance' => array('fields' => array('id'),
                            'Typeseance' => array('fields' => array('id', 'libelle', 'action'))),
                        'Deliberationseance' => array('fields' => array('id'),
                            'Seance' => array('fields' => array('id', 'date', 'type_id'),
                                'Typeseance' => array('fields' => array('id', 'libelle', 'action'))))),
                    'conditions' => $conditions,
                    'order' => 'Deliberation.id DESC'
                ));
                $this->_sortProjetSeanceDate($projets);
                if ($this->data['Deliberation']['generer'] == 0) {
                    
                    $actions = array('view', 'generer');
                    if ($this->Droits->check($this->user_id, "Deliberations:delete"))
                        array_push($actions, 'delete');

                    if ($this->Droits->check($this->user_id, "Deliberations:editerTous"))
                        $actions[] = 'edit';
                    $this->_afficheProjets('index', $projets, 'Résultat de la recherche parmi tous les projets', $actions, array('tousLesProjetsRecherche'));
                    
                } else {
                    if (empty($this->data['Deliberation']['model'])) {
                        $this->Session->setFlash("Vous devez choisir un modèle de document", 'growl', array('type' => 'erreur'));
                        return $this->redirect(array('action' => 'tousLesProjetsRecherche'));
                    }
                    $multiseances = array();
                    if (!empty($projets) || !empty($multiseances)) {
                        $deliberationIds = array();
                        foreach ($projets as &$projet)
                            $deliberationIds[] = $projet['Deliberation']['id'];
                        return $this->_genereFusionRechercheToClient($deliberationIds, $this->data['Deliberation']['model'], $this->data['waiter']['token']);
                    } else {
                        $this->Session->setFlash('Aucun projet correspondant aux critères de recherche.', 'growl', array('type' => 'erreur'));
                        return $this->redirect(array('action' => 'tousLesProjetsRecherche'));
                    }
                }
            }
        }
    }

    /**
     * retourne un tableau array('image'=>, 'titre'=>) pour l'affichage de l'icône dans les listes en fonction de :
     * 
     * @param int $id identifiant du projet 
     * @param $etat état du projet ou de la délibération
     * @param $editerTous droit d'éditer les projets validés
     *
     */
    function _iconeEtat($id, $etat, $editerTous = false, $estDansCircuit = false, $estRedacteur = false, $tourDansCircuit = 0) {
        switch ($etat) {
            case -2 : // refusé
                return array(
                    'image' => 'refuse',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;

            case -1 : // refusé
                return array(
                    'image' => 'versionne',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 0 : // en cours de rédaction
                return array(
                    'image' => 'encours',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 1: // en cours de validation
                if ($estDansCircuit) {
                    if ($tourDansCircuit == -1)
                        return array(
                            'image' => 'fini',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : traité');
                    elseif ($tourDansCircuit == 0) {
                        return array(
                            'image' => 'atraiter',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : à traiter',
                            'status' => $this->Traitement->isDelayStatus($id)
                        );
                    } else
                        return array(
                            'image' => 'attente',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : en attente');
                } else {
                    if ($estRedacteur)
                        return array(
                            'image' => 'fini',
                            'titre' => $this->Deliberation->libelleEtat($etat) . ' : projet dont je suis le rédacteur');
                    else
                        return array(
                            'image' => 'fini',
                            'titre' => $this->Deliberation->libelleEtat($etat));
                }
                break;
            case 2: // validé
                if ($editerTous)
                    return array(
                        'image' => 'valide_editable',
                        'titre' => $this->Deliberation->libelleEtat($etat));
                else
                    return array(
                        'image' => 'fini',
                        'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 3: // voté et adopté
                return array(
                    'image' => 'fini',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 4: // voté et non adopté
                return array(
                    'image' => 'fini',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
            case 5: // transmis au contrôle de légalité
                return array(
                    'image' => 'fini',
                    'titre' => $this->Deliberation->libelleEtat($etat));
                break;
        }
    }

    /**
     * Envoi un/des projet(s) dans le parapheur
     * ATTENTION : Cette méthode n'est pour l'instant opérationnelle que lorsque Pastell est désigné comme parapheur
     * @see DeliberationsController::sendActesToSignature() pour la méthode i-parapheur
     * @param null $seance_id
     * @return mixed
     */
    function sendToParapheur($seance_id = null) {
        App::uses('Signature', 'Lib');
        try {
            $this->Signature = new Signature();
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl');
            return $this->redirect($this->referer());
        }

        // Formulaire envoyé
        if (!empty($this->data['Deliberation'])) {
            $message = '';
            try {
                foreach ($this->data['Deliberation'] as $id => $bool) { // Parcours les checkboxes
                    if ($bool == 1) { // Checkbox cochée
                        $delib_id = substr($id, 3, strlen($id));
                        $this->Deliberation->id = $delib_id;
                        $num_delib = $this->Deliberation->field('num_delib');
                        if (!empty($this->data[$delib_id . "classif2"])) {
                            $this->Deliberation->saveField('num_pref', $this->data[$delib_id . "classif2"]);
                        } elseif (Configure::read('PARAPHEUR') == 'PASTELL') {
                            $message .= "$num_delib : Classification manquante";
                            continue;
                        }
                        if ($this->data['Parapheur']['circuit_id'] == -1) { //Signature manuscrite
                            $signee = $this->Deliberation->signatureManuscrite($delib_id, $this->user_id);
                            $message .= $num_delib . ($signee ? " : Signé correctement<br />" : " : Erreur de signature<br />");
                        } else { //Signature électronique
                            $envoye = $this->Deliberation->envoyerAuParapheur($delib_id, $this->data['Parapheur']['circuit_id'], $this->user_id);
                            $message .= $num_delib . ($envoye ? " : Envoyé<br />" : " : Echec de l'envoi<br />");
                        }
                    }
                }
                $this->Session->setFlash($message, 'growl');
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'error'));
            }
            return $this->redirect($this->referer());
        }

        $this->Filtre->initialisation($this->name . ':' . $this->action . ':' . $seance_id, $this->data, array('url' => $this->here));
        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $this->set('seance_id', $seance_id);

        if (empty($seance_id)) {
            $conditions['OR']['Deliberation.parapheur_etat >'] = 0;
            $conditions['OR']['Deliberation.signee'] = true;
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
            $delibs = $this->Deliberation->find('all', array(
                'fields' => array('Deliberation.id',
                    'Deliberation.objet_delib',
                    'Deliberation.num_delib',
                    'Deliberation.titre',
                    'Deliberation.etat',
                    'Deliberation.circuit_id',
                    'Deliberation.parapheur_etat',
                    'Deliberation.parapheur_bordereau',
                    'Deliberation.num_pref',
                    'Deliberation.signee',
                    'Deliberation.signature',
                    'Deliberation.typeacte_id',
                    'Deliberation.theme_id',
                    'Deliberation.service_id'),
                'conditions' => $conditions,
                'contain' => array(
                    'Service.libelle',
                    'Theme.libelle',
                    'Typeacte.name',
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
        } else {
            $conditions['Deliberationseance.seance_id'] = $seance_id;
            $conditions['Deliberation.etat >='] = 0;
            // Formulaire non envoyé
            if (!isset($this->data['Parapheur']['circuit_id'])) {
                $delibs = $this->Deliberationseance->find('all', array(
                    'recursive' => -1,
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
                            'Typeacte.name',
                            'Service.libelle',
                            'Theme.libelle',
                            'Circuit.nom'
                        )
                    ),
                    'conditions' => $conditions,
                    'order' => 'Deliberationseance.position ASC',
                        )
                );
                $this->_ajouterFiltreSeance($delibs);
            }
        }

        for ($i = 0; $i < count($delibs); $i++) {
            $delibs[$i]['Deliberation'][$delibs[$i]['Deliberation']['id'] . '_num_pref'] = $delibs[$i]['Deliberation']['num_pref'];
            $delibs[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($delibs[$i]['Deliberation']['num_pref']);
        }

        $this->set('nomenclatures', $this->Signature->listClassification());

        $circuits = $this->Signature->printCircuits();
        $this->set('deliberations', $delibs);
        $this->set('circuits', $circuits);
    }

    function sendToSae() {
        if (!Configure::read('USE_SAE')) {
            $this->Session->setFlash('Erreur : SAE désactivé. Pour activer ce service, veuillez contacter votre administrateur.', 'growl', array('type' => 'erreurSAE'));
            return $this->redirect($this->referer());
        }
        App::uses('Sae', 'Lib');
        try {
            $this->Sae = new Sae();
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl');
            return $this->redirect($this->referer());
        }

        // Formulaire envoyé
        if (!empty($this->data['Deliberation'])) {
            try {
                foreach ($this->data['Deliberation'] as $id => $bool) { // Parcours les checkboxes
                    if ($bool == 1) { // Checkbox cochée
                        $acte_id = substr($id, 3, strlen($id));
                        $this->Deliberation->id = $acte_id;

                        if (!empty($this->data[$acte_id . "classif2"])) {
                            $this->Deliberation->saveField('num_pref', $this->data[$acte_id . "classif2"]);
                        } elseif (Configure::read('PARAPHEUR') == 'PASTELL') {
                            throw new Exception("Acte n°" . $acte_id . ': Classification manquante');
                        }

                        $acte = $this->Deliberation->find('first', array(
                            'field' => array('id', 'objet_delib', 'num_delib', 'pastell_id'),
                            'conditions' => array('Deliberation.id' => $acte_id)
                        ));

                        if (empty($acte['Deliberation']['pastell_id'])) {
                            $this->Sae->send($acte, $acte['Deliberation']['tdt_data_bordereau_pdf'], $this->Deliberation->getAnnexesToSend($acte['Deliberation']['id']));
                        }
                        $sent = $this->Sae->send($acte);
                        if ($sent) {
                            $this->Deliberation->saveField('sae_etat', 1);
                            $this->Historique->enregistre($acte_id, $this->user_id, 'Acte envoyé au SAE');
                        } else {
                            throw new Exception($acte['Deliberation']['objet_delib'] . ' (' . $acte['Deliberation']['num_delib'] . ') : Envoi au SAE échoué');
                        }
                    }
                }

                $this->Session->setFlash('Acte(s) envoyé(s) correctement au SAE', 'growl');
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'error'));
            }

            return $this->redirect($this->referer());
        }

        //require_once(ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'pcltar' . DS . 'pcltar.lib.php');
        //if (empty($this->data)) {
        $this->Deliberation->Behaviors->load('Containable');
        $this->paginate = array('conditions' => array('Deliberation.etat' => 5),
            'fields' => array('Deliberation.id', 'Deliberation.objet_delib', 'Deliberation.num_pref',
                'Deliberation.num_delib', 'sae_etat'),
            'contain' => array('Service.libelle', 'Theme.libelle'),
            'limit' => 20,
            'order' => 'Deliberation.id DESC');

        $delibs = $this->Paginator->paginate('Deliberation');

        for ($i = 0; $i < count($delibs); $i++) {
            $delibs[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($delibs[$i]['Deliberation']['num_pref']);
        }
        $this->set('deliberations', $delibs);

        $this->set('nomenclatures', $this->Sae->listClassification());
        // } 

        /*
         * else {
         */

        /*
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
         * 
          // Changement d'état de la délibération
          if ($ret == 0) {
          $this->Session->setFlash("Les documents ont été transférés à AS@LAE", 'growl');
          $this->Deliberation->id = $delib_id;
          $this->Deliberation->saveField('sae_etat', 1);
          } else {
          $this->Session->setFlash("Code retour de AS@LAE : $ret", 'growl', array('type' => 'erreur'));
          }
          } */
        /* }
          return $this->redirect(array('action'=>'verserAsalae')); */
    }

    function goNext($delib_id) {
        $delib = $this->Deliberation->read(null, $delib_id);

        if (empty($delib)) {
            $this->Session->setFlash("Le projet n'existe pas", 'growl', array('type' => 'erreur'));
            return $this->redirect($this->referer());
        }

        if ($delib['Deliberation']['parapheur_etat'] == 1) {
            $this->Session->setFlash('Le projet est dans une étape parapheur, on ne peut pas sauter d\'étapes.', 'growl', array('type' => 'erreur'));
            return $this->redirect($this->referer());
        }

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
                            'trigger_id' => $this->user_id,
                            'type_validation' => 'V'
            ))));
            $this->Traitement->execute('JS', $this->user_id, $delib_id, array(
                'insertion' => $insertion,
                'numero_traitement' => $this->data['Traitement']['etape']
            ));

            $destinataires = $this->Traitement->whoIs($delib_id, 'current', 'RI');
            foreach ($destinataires as $destinataire_id)
                $this->User->notifier($delib_id, $destinataire_id, 'traitement');

            $this->Historique->enregistre($delib_id, $this->user_id, "Saut d'étape du projet");
            $this->Session->setFlash("Le projet est maintenant à l'étape suivante ", 'growl');
            return $this->redirect(array('action' => 'tousLesProjetsValidation'));
        }
    }

    /**
     * La fonction gère l'affichage de la de la page "envoyer a" puis gère 
     * l'envoient des données nécessaires pour Cakeflow 
     * @param type $delib_id id de la délibération
     * @return type Page courante
     */
    function rebond($delib_id) {
        $this->set('delib_id', $delib_id);
        $acte = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $delib_id),
            'fields' => array('Deliberation.redacteur_id'),
            'recursive' => -1));
        $redacteur_id = $acte['Deliberation']['redacteur_id'];
        if (empty($this->request->data)) {
            $this->request->data['Insert']['retour'] = true;
            $users = $this->User->listFields(array('order' => 'User.nom'));
            $users[$redacteur_id] = $users[$redacteur_id] . " <Rédacteur du projet>";
            $this->set('users', $users);
            $this->set('typeEtape', $this->Traitement->typeEtape($delib_id));
        } else {
            if($this->data['Insert']['etape_choisie'] != 1){
                if(count($this->data['Insert']['users_id']) <= 1){
                     $this->Session->setFlash(__('Veuillez sélectioner au moin 2 utilisateurs.', true), 'growl', array('type' => 'erreur'));
                     return $this->redirect(array('action' => 'rebond',$delib_id));
                }
            }
            //on récupaire les données de l'utilisateur courant
            // initialisation des visas a ajouter au traitement 
            //on cré le tableau de mise a jour
            $user = $this->Session->read('user');
            $options = array(
                'insertion' => array(0 => array(
                        'Etape' => array(
                            'etape_id' => null,
                            'etape_nom' => $user['User']['prenom'] . ' ' . $user['User']['nom'],
                            //simple 2ou 3et
                            'etape_type' => $this->data['Insert']['etape_choisie'],
                        ),
                    
                    'Visa' =>  array(0 => array(
                        'type_validation' => 'V',
                            'trigger_id' => $this->data['Insert']['users_id'],
                            
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
            $destinataires = '';
            //Cakeflow insertion de/des utilisateurs dans le circuits la gestion et/ou est faite par l'envoie d'un tableau id voulue ds le trriger_id au lieu d'un seul
            $this->Traitement->execute($action, $this->user_id, $delib_id, $options);
            $destinataires = $user['User']['prenom'] . ' ' . $user['User']['nom'] . ' (' . $user['User']['login'] . ')';
            $this->Historique->enregistre($delib_id, $this->user_id, 'Le projet a été envoyé à'.$destinataire.' '.$action_com);
            foreach ($this->data['Insert']['users_id'] as $id) {
                $this->User->notifier($delib_id, $id, 'traitement');
            }

            return $this->redirect('/');
        }
    }

    /**
     * Mise à jour de l'état des dossiers envoyés au parapheur pour signature
     */
    public function refreshSignature() {
        App::uses('Signature', 'Lib');
        try {
            $Signature = new Signature;
            /** @noinspection PhpParamsInspection ne pas avertir de la non-existence de la fonction (passage par __call()) */
            $ret = $Signature->updateAll();
            $ret = trim(preg_replace('/\s+/', ' ', nl2br(htmlspecialchars($ret, ENT_QUOTES))));
            $this->Session->setFlash($ret, 'growl', array());
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'error'));
        }
        return $this->redirect($this->referer());
    }

    function _handleConditions($conditions) {
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

    function autresActesAValider() {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('titreVue', 'Autres actes en cours d\'élaboration');

        $conditions = $this->_handleConditions($this->Filtre->conditions());
        $conditions['Deliberation.etat <'] = 2;
        $conditions['Deliberation.etat >'] = -1;
        $conditions['Deliberation.signee'] = false;

        $fields = array(
            'Deliberation.id',
            'Deliberation.objet',
            'Deliberation.objet_delib',
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
            'Typeacte.name',
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
        $this->set('canGoNext', $this->Droits->check($this->user_id, "Deliberations:goNext"));
        $this->set('peuxValiderEnUrgence', $this->Droits->check($this->user_id, "Deliberations:validerEnUrgence"));
        $this->set('actes', $actes);

        $this->render('autres_actes');
    }

    function autreActesValides() {
        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);

        $this->set('titreVue', 'Autres actes validés');
        App::uses('Signature', 'Lib');
        try {
            $this->Signature = new Signature;
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'error'));
            $this->redirect($this->referer());
        }
        $circuits = $this->Signature->printCircuits();
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = array(2, 3, 4);
        $conditions['Deliberation.signee'] = false;

        $fields = array(
            'Deliberation.id',
            'Deliberation.num_delib',
            'Deliberation.num_pref',
            'Deliberation.objet',
            'Deliberation.objet_delib',
            'Deliberation.titre',
            'Deliberation.etat',
            'Deliberation.signee',
            'Deliberation.parapheur_etat',
            'Deliberation.parapheur_commentaire',
            'Deliberation.typeacte_id',
            'Deliberation.theme_id',
            'Deliberation.service_id',
            'Deliberation.circuit_id',
        );
        $contain = array(
            'Typeacte.name',
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

        $editerTous = $this->Droits->check($this->user_id, "Deliberations:editerTous");

        for ($i = 0; $i < count($actes); $i++) {
            $actes[$i]['Deliberation'][$actes[$i]['Deliberation']['id'] . '_num_pref'] = $actes[$i]['Deliberation']['num_pref'];
            $actes[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($actes[$i]['Deliberation']['num_pref']);
            $actes[$i]['Deliberation']['signature_encours'] = $actes[$i]['Deliberation']['etat'] == 3 && $actes[$i]['Deliberation']['parapheur_etat'] == 1;
        }

        $this->set('nomenclatures', $this->Signature->listClassification());
        $this->set('canEdit', $editerTous);
        $this->set('actes', $actes);
        $this->set('circuits', $circuits);
        //$this->render('autres_actes');
    }

    /**
     * Envoi d'actes en signature
     * @return mixed
     */
    function sendActesToSignature() {
        if (!Configure::read('USE_PARAPHEUR') && $this->data['Parapheur']['circuit_id'] != -1) {
            $this->Session->setFlash('Parapheur désactivé', 'growl');
            return $this->redirect($this->referer());
        }
        try {
            $circuits = array();
            if ($this->data['Parapheur']['circuit_id'] != -1) {
                App::uses('Signature', 'Lib');
                $this->Signature = new Signature();
                $circuits = $this->Signature->listCircuits();
            }
            $circuits['-1'] = 'Signature manuscrite';
            $this->Deliberation->Behaviors->load('Containable');
            $message = '';
            foreach ($this->data['Deliberation'] as $tmp_id => $bool) {
                if ($bool) {
                    $acte_id = substr($tmp_id, 3, strlen($tmp_id));
                    $this->Deliberation->id = $acte_id;
                    if (!empty($this->data[$acte_id . "classif2"])) {
                        $this->Deliberation->saveField('num_pref', $this->data[$acte_id . "classif2"]);
                    } elseif (Configure::read('PARAPHEUR') == 'PASTELL') {
                        $message .= "Acte n°" . $acte_id . ': Classification manquante';
                        continue;
                    }
                    if ($this->data['Parapheur']['circuit_id'] == -1) {
                        $signee = $this->Deliberation->signatureManuscrite($acte_id, $this->user_id);
                        $message .= "Acte n°" . $acte_id . ($signee ? " : Signé correctement<br />" : " : Erreur de signature<br />");
                    } else {
                        $envoye = $this->Deliberation->envoyerAuParapheur($acte_id, $this->data['Parapheur']['circuit_id'], $this->user_id);
                        $message .= "Acte n°" . $acte_id . ($envoye ? " : Envoyé<br />" : " : Echec de l'envoi<br />");
                    }
                }
            }
            $this->Session->setFlash($message, 'growl');
            return $this->redirect(array('action' => 'autreActesValides'));
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'error'));
            return $this->redirect($this->referer());
        }
    }

    function autreActesAEnvoyer() {

        if (!Configure::read('USE_TDT')) {
            $this->Session->setFlash('TDT désactivé. Veuillez contacter votre administrateur.', 'growl');
            return $this->redirect($this->previous);
        }

        App::uses('Tdt', 'Lib');
        $Tdt = new Tdt();
        $date_classification = $Tdt->getDateClassification();
        $this->set('dateClassification', $date_classification);

        if (Configure::read('TDT') == 'PASTELL') {
            $res = $Tdt->listClassification();
            $this->set('nomenclatures', $res);
        }

        $this->set('titreVue', 'Autres actes à envoyer au contrôle de légalité');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = array(3, 4);
        $conditions['Deliberation.signee'] = true;
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
            'Typeacte.name',
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

        $actes = $this->Deliberation->getActesExceptDelib(array(), array('Deliberation.id', 'Deliberation.typeacte_id'), array());
        $conditions['Deliberation.id'] = Hash::extract($actes, '{n}.Deliberation.id');

        $actes = $this->Deliberation->getActesATeletransmettre($conditions, $fields, $contain, $order);

        $this->_ajouterFiltre($actes);

        for ($i = 0; $i < count($actes); $i++) {
            $actes[$i]['Deliberation'][$actes[$i]['Deliberation']['id'] . '_num_pref'] = $actes[$i]['Deliberation']['num_pref'];
            $actes[$i]['Deliberation']['num_pref_libelle'] = $this->_getMatiereByKey($actes[$i]['Deliberation']['num_pref']);
        }
        $this->set('deliberations', $actes);

        $this->render('to_send');
    }

    public function nonTransmis() {
        $typeacte_ids = $this->Deliberation->Typeacte->find('all', array(
            'recursive' => -1,
            'conditions' => array('Typeacte.teletransmettre' => false),
            'fields' => array('Typeacte.id')));
        $this->Deliberation->Behaviors->load('Containable');
        $this->request->data = $this->Deliberation->find('all', array(
            'conditions' => array(
                'Deliberation.etat' => array(3, 4),
                'Deliberation.signee' => true,
                'Deliberation.typeacte_id' => Set::extract('/Typeacte/id', $typeacte_ids)
            ),
            'contain' => array(
                'Typeacte.name',
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

    function autreActesEnvoyes() {
        $delibs_id = array();
        $this->set('titreVue', 'Autres actes envoyés au contrôle de légalité');

        $this->Filtre->initialisation($this->name . ':' . $this->action, $this->data);
        $conditions = $this->_handleConditions($this->Filtre->conditions());

        $conditions['Deliberation.etat'] = 5;
        $conditions['NOT'][] = 'Deliberation.tdt_id IS NULL';

        $actes = $this->Deliberation->getActesExceptDelib(array(), array('Deliberation.id', 'Deliberation.typeacte_id'), array());
        $conditions['Deliberation.id'] = Hash::extract($actes, '{n}.Deliberation.id');

        $fields = array(
            'Deliberation.id',
            'Deliberation.num_delib',
            'Deliberation.objet',
            'Deliberation.objet_delib',
            'Deliberation.titre',
            'Deliberation.num_pref',
            'Deliberation.etat',
            'Deliberation.tdt_id',
            'Deliberation.tdt_ar_date',
            'Deliberation.typeacte_id',
            'Deliberation.date_acte',
            'Deliberation.theme_id',
            'Deliberation.circuit_id',
            'Deliberation.service_id'
        );
        $contain = array(
            'Typeacte.name',
            'Service.libelle',
            'Circuit.nom',
            'Theme.libelle',
            'Seance.id',
            'Seance.type_id',
            'Seance.date',
            'Typeseance.id',
            'Typeseance.libelle',
        );

        $this->paginate = array('Deliberation' => array(
                'conditions' => $conditions,
                'fields' => $fields,
                'contain' => $contain,
                'limit' => 20
        ));

        $deliberations = $this->Paginator->paginate('Deliberation');

        $this->_ajouterFiltre($deliberations);

        $this->set('dateClassification', $this->S2low->getDateClassification());
        if (Configure::read('TDT') == 'S2LOW') {
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

    function getTypeseancesParTypeacteAjax($typeacte_id = null) {
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

    function getSeancesParTypeseanceAjax($typeseances_id) {
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
                    $result[$seance['Seance']['id']] = $seance['Typeseance']['libelle'] . ' : ' . CakeTime::i18nFormat($seance['Seance']['date'], '%A %d %B %G à %k:%M');
            }
        }
        $this->set('seances', $result);
        $this->layout = 'ajax';
    }

    function copyFromPrevious($delib_id, $seance_id) {
        $this->Deliberation->_effacerListePresence($delib_id);
        $this->Deliberation->_copyFromPreviousList($delib_id, $seance_id);
        return $this->redirect(array('controller' => 'seances', 'action' => 'voter', $delib_id, $seance_id));
    }

    function traitementLot() {
        $deliberationIds = array();
        $redirect = $this->referer();
        if (isset($this->data['Deliberation']['action']) && empty($this->data['Deliberation']['action'])) {
            $this->Session->setFlash('Veuillez sélectionner une action.', 'growl', array('type' => 'erreur'));
            return $this->redirect($redirect);
        } else
            $action = $this->data['Deliberation']['action'];
        if (isset($this->data['Deliberation']['check']))
            foreach ($this->data['Deliberation']['check'] as $tmp_id => $bool) {
                if ($bool) {
                    $delib_id = substr($tmp_id, 3, strlen($tmp_id));
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
                    $deliberationIds[] = $delib_id;
                }
            }

        if (!isset($deliberationIds) || (isset($deliberationIds) && count($deliberationIds) == 0)) {
            $this->Session->setFlash('Veuillez sélectionner une délibération.', 'growl', array('type' => 'erreur'));
            return $this->redirect($redirect);
        }

        if ($action == 'generation') {
            return $this->_genereFusionRechercheToClient($deliberationIds, $this->data['Deliberation']['modele'], $this->data['waiter']['token']);
        }
        $this->Session->setFlash('Action effectuée avec succès', 'growl');
        return $this->redirect($redirect);
    }

    function quicksearch() {
        if (empty($this->request->data['User']['search']) OR ( !ctype_digit(trim($this->request->data['User']['search'])) && strlen(trim($this->request->data['User']['search'])) < 4)) {
            $this->Session->setFlash('Vous devez saisir au moins un mot. (plus de 3 caractères)', 'growl', array('type' => 'erreur'));
            return $this->redirect($this->previous);
        }
        $field = trim($this->request->data['User']['search']);
        $conditionsDroits = array();
        if (!$this->Droits->check($this->user_id, 'Deliberations:tousLesProjetsRecherche')) {
            $listeCircuits = $this->Circuit->listeCircuitsParUtilisateur($this->user_id);
            if (!empty($listeCircuits))
                $conditionsDroits['OR']['Deliberation.circuit_id'] = explode(',', $listeCircuits);

            $conditionsDroits['OR']['Deliberation.redacteur_id'] = $this->user_id;
            //Récupère la liste des délib que l'utilisateur a visé (résolution bug changement circuit non visible)
            $listeDelibsParticipe = explode(',', $this->Traitement->getListTargetByTrigger($this->user_id));
            if (!empty($listeDelibsParticipe))
                $conditionsDroits['OR']['Deliberation.id'] = $listeDelibsParticipe;
        }

        $conditionsRecherche = array();
        if (ctype_digit($field)) {
            $conditionsRecherche['OR']['Deliberation.id'][] = $field;
        }
        $conditionsRecherche['OR']['Deliberation.objet ILIKE'] = "%$field%";
        $conditionsRecherche['OR']['Deliberation.titre ILIKE'] = "%$field%";

        $conditions = array('AND' => array($conditionsDroits, $conditionsRecherche));

        $ordre = 'Deliberation.created DESC';
        $this->Deliberation->Behaviors->load('Containable');
        $projets = $this->Deliberation->find('all', array(
            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.etat', 'Deliberation.signee',
                'Deliberation.titre', 'Deliberation.date_limite', 'Deliberation.anterieure_id',
                'Deliberation.num_pref', 'Deliberation.redacteur_id', 'Deliberation.circuit_id',
                'Deliberation.typeacte_id', 'Deliberation.theme_id', 'Deliberation.service_id'),
            'conditions' => $conditions,
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
            'order' => $ordre));
        $this->_sortProjetSeanceDate($projets);
        $this->_afficheProjets('index', $projets, 'R&eacute;sultat de la recherche parmi mes projets', array('view'), array());
    }

    public function majArTdt($cookieToken = null) {
        $this->Deliberation->majArAll();
        $this->Session->setFlash('Mise à jour des accusés de réception effectuée.', 'growl');
        // envoi au client         
        if (!empty($cookieToken))
            $this->Session->write('Generer.downloadToken', $cookieToken, false, 3600);

        return $this->redirect($this->referer());
    }

    public function majEchangesTdt($cookieToken = null) {
        $this->Deliberation->majEchangesTdtAll();
        $this->Session->setFlash('Mise à jour des échanges avec le TDT effectuée.', 'growl');

        // envoi au client         
        if (!empty($cookieToken))
            $this->Session->write('Generer.downloadToken', $cookieToken, false, 3600);

        return $this->redirect($this->referer());
    }

    function downloadTdtMessage($tdt_id = null) {

        try {
            if (empty($tdt_id)) {
                throw new Exception('Merci d\indiquer l\'identifiant du message.');
            }

            $data = $this->Deliberation->TdtMessage->find('first', array(
                'fields' => array('tdt_data'),
                'conditions' => array('tdt_id' => $tdt_id),
                'recursive' => -1
            ));

            if (empty($data['TdtMessage']['tdt_data'])) {
                throw new Exception('Le message est indiponible.');
            }
            $tdt_data = $this->Deliberation->TdtMessage->RecupMessagePdfFromTar($data['TdtMessage']['tdt_data']);
            // envoi au client
            $this->response->disableCache();
            $this->response->body($tdt_data['content']);
            $this->response->type('application/pdf');
            $this->response->download($tdt_data['filename']);
            return $this->response;
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'warning'));
            return $this->redirect($this->here);
        }
    }

    function getTampon($delib_id) {
        
        $delib = $this->Deliberation->find('first', array(
            'conditions' => array('id' => $delib_id),
            'fields' => array('num_delib', 'tdt_data_pdf'),
            'recursive' => -1
        ));

        if (empty($delib['Deliberation']['tdt_data_pdf'])) {
            $this->Session->setFlash('l\'acte tamponné n\'est pas encore disponible', 'growl', array('type' => 'warning'));
            $this->redirect($this->here);
        }
        // envoi au client
        $this->response->disableCache();
        $this->response->body($delib['Deliberation']['tdt_data_pdf']);
        $this->response->type('application/pdf');
        $this->response->download('tampon_tdt_' . $delib['Deliberation']['num_delib'] . '.pdf');
        return $this->response;
    }

    /**
     * @param int $delib_id
     * @return CakeResponse|string
     */
    function getBordereauTdt($delib_id) {
        $delib = $this->Deliberation->find('first', array(
            'conditions' => array('id' => $delib_id),
            'fields' => array('num_delib', 'tdt_data_bordereau_pdf'),
            'recursive' => -1
        ));
        
        if (empty($delib['Deliberation']['tdt_data_bordereau_pdf'])) {
            $this->Session->setFlash('le bordereau n\'est pas encore disponible', 'growl', array('type' => 'warning'));
            $this->redirect($this->here);
        }
        // envoi au client
        $this->response->disableCache();
        $this->response->body($delib['Deliberation']['tdt_data_bordereau_pdf']);
        $this->response->type('application/pdf');
        $this->response->download('bordereau_tdt_' . $delib['Deliberation']['num_delib'] . '.pdf');
        return $this->response;
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

            $annexesInvalide = $this->Deliberation->Annex->find('count', array(
                'fields' => 'Annex.id',
                'conditions' => array('foreign_key' => $id, 'joindre_fusion' => true, 'edition_data IS NULL'),
                'recursive' => -1
            ));

            if (!empty($annexesInvalide))
                throw new Exception('Toutes les annexes du projet :' . $id . ' ne sont pas encore converties pour générer le document. Veuillez réessayer dans quelques instants ...');

            // fusion du document
            $this->Deliberation->Behaviors->load('OdtFusion', array('id' => $id));
            $filename = $this->Deliberation->fusionName();
            $this->Deliberation->odtFusion();

            // selon le format d'envoi du document (pdf ou odt)
            if ($this->Session->read('user.format.sortie') == 0) {
                $mimeType = "application/pdf";
                $filename = $filename . '.pdf';
                $content = $this->Conversion->convertirFlux($this->Deliberation->odtFusionResult, 'odt', 'pdf');
            } else {
                $mimeType = "application/vnd.oasis.opendocument.text";
                $filename = $filename . '.odt';
                $content = $this->Conversion->convertirFlux($this->Deliberation->odtFusionResult, 'odt', 'odt');
            }
            unset($this->Deliberation->odtFusionResult);

            // envoi au client
            $this->Session->write('Generer.downloadToken', $cookieToken, false, 3600);
            $this->response->disableCache();
            $this->response->body($content);
            $this->response->type($mimeType);
            $this->response->download($filename);
            return $this->response;
        } catch (Exception $e) {
            $this->log('Fusion :' . $e->getMessage() . ' File:' . $e->getFile() . ' Line:' . $e->getLine(), 'error');
            $this->Session->setFlash('erreur lors de la génération du document : ' . $e->getMessage(), 'growl', array('type' => 'erreur'));
            $this->redirect($this->referer());
        }
    }

    /**
     * fonction de fusion de plusieurs projets ou délibérations avec envoi du résultat vers le client
     * @param $ids
     * @param integer $modelTemplateId
     * @param integer $cookieToken numéro de cookie du client pour masquer la fenêtre attendable
     * @return CakeResponse
     */
    function _genereFusionRechercheToClient($ids, $modelTemplateId, $cookieToken) {
        // fusion du document
        $this->Seance->Behaviors->load('OdtFusion', array('modelTemplateId' => $modelTemplateId));
        $this->Deliberation->Behaviors->load('OdtFusion', array('modelTemplateId' => $modelTemplateId));
        $filename = $this->Deliberation->fusionName();
        $this->Deliberation->odtFusion(array('modelOptions' => array('deliberationIds' => $ids)));

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
        $this->Session->write('Generer.downloadToken', $cookieToken, false, 3600);
        $this->response->disableCache();
        $this->response->body($content);
        $this->response->type($mimeType);
        $this->response->download($filename);
        return $this->response;
    }
    
    
    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->History->deny('getBordereauTdt','attribuercircuit','getTampon','sendToTdt','downloadDelib');
    }
}
