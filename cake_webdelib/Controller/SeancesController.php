<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');

class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Fck');
        var $uses = array('Deliberation', 'Deliberationseance', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'ModelOdtValidator.Modeltemplate', 'Annex', 'Typeseance', 'Acteur', 'Infosupdef', 'Infosup');
	var $components = array('Date','Email', 'Gedooo', 'Conversion', 'Droits', 'Progress', 'S2low', 'ModelOdtValidator.Fido','SabreDav',
            'Auth' => array(
            'mapActions' => array(
                'create' => array('add','getSeancesParTypeseanceAjax'),
                'read' => array('index','listerFuturesSeances', 'calendrier', 'listerAnciennesSeances',
                    'delete','edit', 'sortby',           
			'afficherProjets', 
			'reportePositionsSeanceDeliberante',  
			'genererConvoc',   
			'multiodj',
			'changePosition',
			'saisirDebatGlobal',
			'details',
			'saisirDebat',
			'voter',
			'changeRapporteur',
			'detailsAvis',
			'donnerAvis',
			'saisirSecretaire',
			'getListActeurs',
                        'sendConvocations',
                        'sendToIdelibre',
			'saisirCommentaire','genereFusionToFiles','genereFusionMultiSeancesToClient'
                    ,'genererConvocation','genererOrdredujour','downloadZip','genereFusionToClient',
                    'download','getFileType','getFileName','getSize','getData'
                ),
                'update' => array('edit','clore','sendOrdredujour','getSeancesParTypeseanceAjax'),
                'delete' => array('delete','deleteDebatGlobal','effacerVote','resetVote')),
        ));
	
	var $cacheAction = 0;


    function add($timestamp = null) {
        // initialisation
        $success = false;
        $date = '';
        if (empty($this->data)) {
            if (isset($timestamp))
                $date = date('d/m/Y H:i', $timestamp);
        } else {
            $this->Seance->begin();
            $this->request->data['Seance']['date'] = CakeTime::format(str_replace('/', '-', $this->data['Seance']['date']) . ':00', '%Y-%m-%d %H:%M:00');
            if ($success = $this->Seance->save($this->data)) {
                // sauvegarde des informations supplémentaires
                if (array_key_exists('Infosup', $this->data)) {
                    $success &= $this->Infosup->saveCompacted($this->request->data['Infosup'], $this->Seance->id, 'Seance');
                }
            }
            if (!$success) {
                $this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                $this->Seance->rollback();
            }
        }
        if ($success) {
            $this->Seance->commit();
            $this->Session->setFlash('La séance a été sauvegardée', 'growl');
            $this->redirect(array('action' => 'listerFuturesSeances'));
        } else {
            $this->set('date', $date);
            $natures = array_keys($this->Session->read('user.Nature'));
            App::import('model', 'TypeseancesTypeacte');
            $TypeseancesTypeacte = new TypeseancesTypeacte();
            $types = $TypeseancesTypeacte->getTypeseanceParNature($natures);

            $this->set('typeseances', $this->Typeseance->find('list', array('conditions' => array('Typeseance.id' => $types))));
            $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                        'recursive' => -1,
                        'conditions' => array('model' => 'Seance', 'actif' => true),
                        'order' => 'ordre')));
            $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Seance'));
            $this->request->data['Infosup'] = $this->Infosupdef->valeursInitiales('Seance');
            $this->render('edit');
        }
    }

    function edit($id = null) {
        $sortie = false;
        $date = '';
        $path_seance = WWW_ROOT . 'files' . DS . 'generee' . DS . 'seance' . DS . $id . DS;
        if (empty($this->data)) { // not is post
            $this->Seance->Behaviors->attach('Containable');
            $this->request->data = $this->Seance->find('first', array(
                'contain' => array('Infosup'),
                'conditions' => array('Seance.id' => $id)
            ));
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour la seance', 'growl', array('type' => 'erreur'));
                $sortie = true;
            } else {
                $date = date('d/m/Y H:i', strtotime($this->data['Seance']['date']));
                foreach ($this->data['Infosup'] as $infosup) {
                    $infoSupDef = $this->Infosupdef->find('first', array(
                        'recursive' => -1,
                        'fields' => array('type'),
                        'conditions' => array('id' => $infosup['infosupdef_id'], 'model' => 'Seance', 'actif' => true)));
                    if ($infoSupDef['Infosupdef']['type'] == 'odtFile' && !empty($infosup['file_name']) && !empty($infosup['content']))
                        $this->Gedooo->createFile($path_seance, $infosup['file_name'], $infosup['content']);
                }
                $this->request->data['Infosup'] = $this->Infosup->compacte($this->request->data['Infosup']);
            }
        } else {
            $success = true;
            $this->Seance->begin();
            $this->request->data['Seance']['date'] = CakeTime::format(str_replace('/', '-', $this->data['Seance']['date']) . ':00', '%Y-%m-%d %H:%M:00');
            $success &= $this->Seance->save($this->data);
            if ($success) {
                // sauvegarde des fichiers odt car possibilité modifiés en webdav sur le serveur
                $infossupDefs = $this->Infosupdef->find('all', array(
                    'recursive' => -1,
                    'fields' => array('id'),
                    'conditions' => array('type' => 'odtFile', 'model' => 'Seance', 'actif' => true)));
                foreach ($infossupDefs as $infossupDef) {
                    $infosup = $this->Infosup->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'file_name'),
                        'conditions' => array('foreign_key' => $id, 'model' => 'Seance', 'infosupdef_id' => $infossupDef['Infosupdef']['id'])));
                    if (empty($infosup) || empty($infosup['Infosup']['file_name']))
                        continue;
                    $odtFileUri = $path_seance . $infosup['Infosup']['file_name'];

                    if (file_exists($odtFileUri)) {
                        $stat = stat($odtFileUri);
                        if ($stat > 0) {
                            $infosup['Infosup']['content'] = file_get_contents($odtFileUri);
                            $infosup['Infosup']['file_size'] = $stat['size'];
                            $success &= $this->Infosup->save($infosup);
                        }
                    }
                }
                // sauvegarde des informations supplémentaires
                if (array_key_exists('Infosup', $this->data))
                    $success &= $this->Infosup->saveCompacted($this->request->data['Infosup'], $id, 'Seance');
                //exit; // FIXME
            }

            if ($success) {
                $this->Seance->commit();
                $this->Session->setFlash('La séance a été sauvegardée', 'growl');
                $sortie = true;
            } else {
                $this->Seance->rollback();
                $this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
                $msg_error = '';
                $InfosupErrors = $this->Infosup->invalidFields();
                if (!empty($InfosupErrors)) {
                    foreach ($InfosupErrors as $InfosupName => $InfosupError) {
                        $msg_error .= "<strong>Information supplémentaire :</strong><br>";
                        foreach ($InfosupError as $error) {
                            $msg_error .= "- " . $error . "<br/>";
                        }
                    }
                    $this->Session->setFlash($msg_error, 'growl', array('type' => 'erreur'));
                }
            }
        }
        if ($sortie)
            $this->redirect(array('action' => 'listerFuturesSeances'));
        else {
            $this->set('date', $date);
            $natures = array_keys($this->Session->read('user.Nature'));
            App::import('model', 'TypeseancesTypeacte');
            $TypeseancesTypeacte = new TypeseancesTypeacte();
            $types = $TypeseancesTypeacte->getTypeseanceParNature($natures);

            $this->set('typeseances', $this->Typeseance->find('list', array('conditions' => array('Typeseance.id' => $types))));
            $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                        'recursive' => -1,
                        'conditions' => array('model' => 'Seance', 'actif' => true),
                        'order' => 'ordre')));
            $this->set('infosuplistedefs', $this->Infosupdef->generateListes('Seance'));
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour la seance', 'growl', array('type' => 'erreur'));
            $this->redirect('/seances/index');
        }
        $delibs = $this->Seance->getDeliberationsId($id);
        if (count($delibs) != 0) {
            $this->Session->setFlash('Cette séance contient des actes. Vous ne pouvez pas la supprimer.', 'growl', array('type' => 'erreur'));
            $this->redirect('/seances/listerFuturesSeances');
        }
        if ($this->Seance->delete($id)) {
            $this->Session->setFlash('La séance a été supprimée', 'growl');
            $this->redirect('/seances/listerFuturesSeances');
        } else {
            $this->Session->setFlash('Invalide id pour la seance', 'growl', array('type' => 'erreur'));
            $this->redirect('/seances/index');
        }
    }

    function index() {
        $this->set('AFFICHE_CONVOCS_ANONYME', Configure::read('AFFICHE_CONVOCS_ANONYME'));
        $this->set('use_pastell', Configure::read('USE_PASTELL'));
        $this->set('canSign', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:sendToParapheur"));
        $format = $this->Session->read('user.format.sortie');
        $this->set('models', $this->Modeltemplate->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('modeltype_id' => array(MODEL_TYPE_MULTISEANCE)),
                    'fields' => array('name'))));
        if (empty($format))
            $format = 0;

        //Choix du rendu à appliquer
        if (isset($this->params['endDiv']))
            $this->set('endDiv', $this->params['endDiv']);
        else
            $this->set('endDiv', false);

        $this->set('format', $format);

        if (empty($this->data)) {
            $seances = $this->Seance->find('all', array('conditions' => array('Seance.traitee' => 0),
                'order' => array('date ASC'),
                'fields' => array('id', 'date', 'type_id', 'idelibre_id'),
                'contain' => array('Typeseance.libelle', 'Typeseance.color', 'Typeseance.action',
                    'Typeseance.modelconvocation_id',
                    'Typeseance.modelordredujour_id',
                    'Typeseance.modelpvsommaire_id',
                    'Typeseance.modelpvdetaille_id')));

            for ($i = 0; $i < count($seances); $i++) {
                $seances[$i]['Seance']['dateEn'] = $seances[$i]['Seance']['date'];
            }
            $this->set('seances', $seances);
        }
    }

    function listerAnciennesSeances() {
        $this->Seance->Behaviors->attach('Containable');
        if (empty($this->data)) {
            $seances = $this->Seance->find('all', array('conditions' => array('Seance.traitee' => 1),
                'contain' => array('Typeseance.libelle'),
                'fields' => array('Seance.id', 'Seance.date', 'Seance.type_id'),
                'ordre' => 'date asc'));
            $this->set('seances', $seances);
        }
    }

    function calendrier($annee = null) {
        // initialisations
        $seances_calendrier = array();
        $annee = empty($annee) ? date('Y') : $annee;
        $droitAdd = $this->Droits->check($this->Session->read('user.User.id'), 'Seances:add');
        $droitEdit = $this->Droits->check($this->Session->read('user.User.id'), 'Seances:edit');

        // lecture des séances non traitées en DB
        $this->Seance->Behaviors->attach('Containable');
        $seances = $this->Seance->find('all', array(
            'fields' => array('Seance.id', 'Seance.date', 'Seance.type_id'),
            'contain' => array('Typeseance.libelle'),
            'conditions' => array('Seance.traitee' => 0),
            'order' => 'date ASC'));
        foreach ($seances as $seance) {
            $seances_calendrier[] = array(
                'id' => $seance['Seance']['id'],
                'libelle' => $seance['Typeseance']['libelle'],
                'strtotime' => strtotime($seance['Seance']['date']),
            );
        }

        $this->set('seances', $seances_calendrier);
        $this->set('annee', $annee);
    }

    /*
      function getDeliberations($seance_id)
      {
      $deliberations = $this->Deliberationseance->find(
      'all',
      array(
      'fields' => array(
      'Deliberationseance.seance_id',
      'Deliberationseance.deliberation_id',
      'Deliberationseance.position',
      'Deliberation.*',
      ),
      'contain' => array(
      'Deliberation'=>array(
      'Typeacte.nature_id','Typeacte.name',
      'Service.libelle',
      'Theme.libelle',
      'Circuit.nom'
      ),
      ),
      'recursive' => 2,
      'conditions' => array('Deliberationseance.seance_id' => $seance_id, 'Deliberation.etat >=' => 0),
      'order' => 'Deliberationseance.position ASC',
      )
      );
      for ($i = 0; $i < count($deliberations); $i++) {
      if (isset($deliberations[$i]['Deliberation']['theme_id'])) {
      $theme = $this->Deliberation->Theme->find('first',
      array('conditions' => array('Theme.id' => $deliberations[$i]['Deliberation']['theme_id']),
      'fields' => array('Theme.id', 'Theme.libelle'),
      'recursive' => -1));
      $deliberations[$i]['Theme']['libelle'] = $theme['Theme']['libelle'];
      }
      if (isset ($deliberations[$i]['Deliberation']['rapporteur_id'])) {
      $rapporteur = $this->Deliberation->Rapporteur->find('first',
      array('conditions' => array('Rapporteur.id' => $deliberations[$i]['Deliberation']['rapporteur_id']),
      'fields' => array('Rapporteur.id', 'Rapporteur.nom', 'Rapporteur.prenom'),
      'recursive' => -1));
      $deliberations[$i]['Rapporteur']['nom'] = $rapporteur['Rapporteur']['nom'];
      $deliberations[$i]['Rapporteur']['prenom'] = $rapporteur['Rapporteur']['prenom'];
      }
      }
      return ($deliberations);
      } */

    function sortby($seance_id, $sortby) {

        $this->Deliberationseance->ordonneSeanceByValue($seance_id, $sortby);

        return $this->redirect($this->previous);
    }

    function afficherProjets($id = null, $sortby = null) {
        $this->set('lastPosition', $this->Seance->getLastPosition($id) - 1);

        // Critere de tri
        switch ($sortby) {
            case 'theme_id':
                $sortby_champ = 'Theme.order';
                break;
            case 'service_id':
                $sortby_champ = 'Service.order';
                break;
            case 'rapporteur_id':
                $sortby_champ = 'Rapporteur.nom';
                break;
            case 'titre':
                $sortby_champ = 'Deliberation.titre';
                break;
            default:
                $sortby_champ = 'Deliberationseance.position'; //'Deliberationseance.position';
                break;
        }

        $aProjetIds = $this->Seance->getDeliberationsId($id);
            $projets = $this->Deliberation->find('all', array(
            'conditions' => array("Deliberation.id" => $aProjetIds),
            'fields' => array(
                    'objet_delib',
                    'titre',
                    'id',
                    'etat',
                    'typeacte_id',
                    'num_delib',
                    'rapporteur_id',
                    'Deliberationseance.position'
                ),
            'contain' => array(
                'Theme'=>array('fields'=>array('order','libelle')), 
                'Service'=>array('fields'=>array('order','libelle')),  
                ),
            'joins' => array($this->Deliberation->join('Deliberationseance',array( 'type' => 'INNER' ))),
            'order' => array("$sortby_champ  ASC")));
            
            $this->set('seance_id', $id);
            $this->set('rapporteurs', $this->Acteur->generateListElus());
            $this->set('projets', $projets);
            $this->set('date_seance', $this->Seance->getDate($id));
            $aPosition=array();
            foreach($aProjetIds as $key=>$value)
            {
                $aPosition[$key]=$key;
            }
            $this->set('aPosition', $aPosition);
            $this->set('is_deletable', true);
            $this->set('is_deliberante', $this->Seance->isSeanceDeliberante($id));
	}
        
        function reportePositionsSeanceDeliberante ($seance_id) {
            if ($this->Seance->Deliberationseance->reportePositionsSeanceDeliberante($seance_id))
                $this->Session->setFlash('Report effectué.', 'growl');
            else
                $this->Session->setFlash('Report non effectué.', 'growl', array('type'=>'erreur'));
                
            $this->redirect('/seances/afficherProjets/'.$seance_id);
        }

    function changeRapporteur($seance_id, $newRapporteur, $delib_id) {
        $this->Deliberation->id = $delib_id;
        if ($this->Deliberation->saveField('rapporteur_id', $newRapporteur))
            $this->redirect(array('action' => 'afficherProjets', $seance_id));
    }

    function details ($seance_id = null) {
        
        $this->set('seance_id', $seance_id);

        $seance = $this->Seance->find('first', array(
        'conditions' => array('Seance.id'=> $seance_id),
        'fields' => array('Seance.type_id'),
        'contain' => array('Typeseance.libelle', 'Typeseance.action'))
        );

        $delibs = $this->Seance->getDeliberationsId($seance_id);
        $deliberations = $this->Deliberation->find('all',
            array(
                'fields' => array(
                    'Deliberation.objet_delib',
                    'Deliberation.titre',
                    'Deliberation.id',
                    'Deliberation.etat',
                    'Deliberation.typeacte_id',
                    'Deliberation.num_delib',
                    'Deliberationseance.position'
                ),

                'contain' => array(
                    'Theme'=>array('fields'=>array('libelle')), 
                    'Service'=>array('fields'=>array('libelle')),
                    'Rapporteur'=>array('fields'=>array('nom','prenom')),
                    'President'=>array('fields'=>array('nom','prenom')),
                ),
                'conditions' => array('Deliberation.id' => $delibs),
                'joins' => array($this->Deliberation->join('Deliberationseance',array( 'type' => 'INNER' ))),
                'order'=>'Deliberationseance.position ASC'
                ));
        
        for ($i=0; $i<count($deliberations); $i++) {
            $deliberations[$i]['Deliberation']['is_delib'] = $this->Deliberation->is_delib($deliberations[$i]['Deliberation']['id']);
            $deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($deliberations[$i]['Service']['id']);
            $deliberations[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['Seance']['type_id'], $deliberations[$i]['Deliberation']['etat']);
        }
        
        $this->set('seance',$seance);
        $this->set('deliberations',$deliberations);
        $this->set('date_seance', $this->Seance->getDate($seance_id));
        $this->set('seance_id', $seance_id);
    }

    function effacerVote($deliberation_id = null) {
        $votes = $this->Vote->find('all', array(
            'conditions' => array('Vote.delib_id' => $deliberation_id),
            'fields' => array('Vote.id'),
            'recursive' => -1));
        foreach ($votes as $vote)
            $this->Vote->delete($vote['Vote']['id']);
    }

    function voter($deliberation_id, $seance_id) {
        $this->Seance->Behaviors->attach('Containable');
        $deliberation = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $deliberation_id)));
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields' => array('Seance.date', 'Seance.president_id', 'Seance.type_id'),
            'contain' => array('Typeseance.compteur_id')));
        if (empty($this->data)) {
            //Initialisation président de séance
            $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], true, array('id', 'nom', 'prenom'));

            $tab = array();
            if (!empty($acteursConvoques)) {
                foreach ($acteursConvoques as $acteurConvoque)
                    $tab[$acteurConvoque['Acteur']['id']] = $acteurConvoque['Acteur']['prenom'] . ' ' . $acteurConvoque['Acteur']['nom'];
            }
            $this->set('acteurs', $tab);

            if (empty($deliberation['Deliberation']['president_id']))
                $this->set('selectedPresident', $seance['Seance']['president_id']);
            else
                $this->set('selectedPresident', $deliberation['Deliberation']['president_id']);

            $nbAbsent = 0;
            // Initialisation du détail du vote
            $donnees = $this->Vote->find('all', array('conditions' => array("Vote.delib_id" => $deliberation_id)));
            foreach ($donnees as $donnee) {
                $this->request->data['detailVote'][$donnee['Vote']['acteur_id']] = $donnee['Vote']['resultat'];
            }
            // Initialisation du total des voix
            $this->request->data['Deliberation']['vote_nb_oui'] = $deliberation['Deliberation']['vote_nb_oui'];
            $this->request->data['Deliberation']['vote_nb_non'] = $deliberation['Deliberation']['vote_nb_non'];
            $this->request->data['Deliberation']['vote_nb_abstention'] = $deliberation['Deliberation']['vote_nb_abstention'];
            $this->request->data['Deliberation']['vote_nb_retrait'] = $deliberation['Deliberation']['vote_nb_retrait'];
            // Initialisation du resultat
            $this->request->data['Deliberation']['etat'] = $deliberation['Deliberation']['etat'];
            // Initialisation du commentaire
            $this->request->data['Deliberation']['vote_commentaire'] = $deliberation['Deliberation']['vote_commentaire'];

            $this->set('seance_id', $seance_id);
            $this->set('deliberation', $deliberation);

            $listPresents = $this->Deliberation->afficherListePresents($deliberation_id, $seance_id);
            $typeacteurs = array();
            foreach ($listPresents as $present) {
                $typeacteurs[$present['Acteur']['Typeacteur']['id']] = $present['Acteur']['Typeacteur']['nom'];
            }
            $this->set('typeacteurs', $typeacteurs);
            $this->set('presents', $listPresents);

            $nbPresent = count($listPresents);
            foreach ($listPresents as $present)
                if (empty($present['Listepresence']['present']) && empty($present['Listepresence']['mandataire']))
                    $nbAbsent++;
                else
                    $nbPresent++;
            if ($nbPresent / 2 < $nbAbsent)
                $this->set('message', 'Attention, le quorum n\'est plus atteint...');
        } else {
            $this->request->data['Deliberation']['id'] = $deliberation_id;

            $this->Deliberation->id = $deliberation_id;
            $this->effacerVote($deliberation_id);
            switch ($this->data['Vote']['typeVote']) {
                case 1:
                    // Saisie du détail du vote
                    $this->request->data['Deliberation']['vote_nb_oui'] = 0;
                    $this->request->data['Deliberation']['vote_nb_non'] = 0;
                    $this->request->data['Deliberation']['vote_nb_abstention'] = 0;
                    $this->request->data['Deliberation']['vote_nb_retrait'] = 0;
                    if (!empty($this->data['detailVote'])) {
                        foreach ($this->data['detailVote'] as $acteur_id => $vote) {
                            $this->Vote->create();
                            $this->request->data['Vote']['acteur_id'] = $acteur_id;
                            $this->request->data['Vote']['delib_id'] = $deliberation_id;
                            $this->request->data['Vote']['resultat'] = $vote;
                            $this->Vote->save($this->data['Vote']);
                            if ($vote == 3)
                                $this->request->data['Deliberation']['vote_nb_oui'] ++;
                            elseif ($vote == 2)
                                $this->request->data['Deliberation']['vote_nb_non'] ++;
                            elseif ($vote == 4)
                                $this->request->data['Deliberation']['vote_nb_abstention'] ++;
                            elseif ($vote == 5)
                                $this->request->data['Deliberation']['vote_nb_retrait'] ++;
                        }
                    }
                    if ($this->data['Deliberation']['vote_nb_oui'] > $this->data['Deliberation']['vote_nb_non'])
                        $this->request->data['Deliberation']['etat'] = 3;
                    else
                        $this->request->data['Deliberation']['etat'] = 4;
                    break;
                case 2:
                    // Saisie du total du vote
                    if ($this->data['Deliberation']['vote_nb_oui'] > $this->data['Deliberation']['vote_nb_non'])
                        $this->request->data['Deliberation']['etat'] = 3;
                    else
                        $this->request->data['Deliberation']['etat'] = 4;
                    break;
                case 3:
                    // Saisie du resultat global
                    $this->request->data['Deliberation']['vote_nb_oui'] = 0;
                    $this->request->data['Deliberation']['vote_nb_non'] = 0;
                    $this->request->data['Deliberation']['vote_nb_abstention'] = 0;
                    $this->request->data['Deliberation']['vote_nb_retrait'] = 0;
                    break;
            }

            // Attribution du numéro de la délibération si pas déjà attribué
            if (empty($deliberation['Deliberation']['num_delib'])) {
                $this->request->data['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($seance['Typeseance']['compteur_id'], $seance['Seance']['date']);
                $this->request->data['Deliberation']['num_delib'] = str_replace(
                        '#p#', $this->Deliberation->getPosition($deliberation_id, $seance_id), $this->data['Deliberation']['num_delib']);
            }
            if ($this->Deliberation->save($this->data['Deliberation'])) {
                $this->redirect(array('action' => 'details', $seance_id));
            }
        }
    }
    
    function resetVote($seance_id, $projet_id)
    {
        try {
            $this->Deliberation->resetVote($projet_id, $seance_id);
            $this->Session->setFlash('Le vote du projet a été supprimé', 'growl', array('type' => 'sucess'));
        
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'growl', array('type' => 'danger'));
        }

        $this->redirect($this->previous);
    }

    function saisirDebat($delib_id = null, $seance_id = null) {

        $this->set('seance_id', $seance_id);
        $this->set('delib_id', $delib_id);

        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields' => array('traitee', 'pv_figes', 'date'),
            'contain' => array('Typeseance.action')
        ));
        
        $this->set('seance', $seance);

        if ($seance['Seance']['pv_figes'] == 1) {
            $this->Session->setFlash('Les pvs ont été figés, vous ne pouvez plus saisir de débat pour cette délibération...', 'growl', array('type' => 'erreur'));
            return $this->redirect(array('controller' => 'postseances', 'action' => 'index'));
        }

        if ($this->request->isPost()) {
            $this->request->data['Deliberation']['id'] = $delib_id;
            if ($this->Deliberation->SaveDebat($this->data['Deliberation'])) {
                $this->Session->setFlash('Débat global enregistré', 'growl');
                return $this->redirect($this->previous);
            } else {
                $validationErrors = $this->Seance->invalidFields();
                $msg_error = '';
                if (!empty($validationErrors)) {
                    foreach ($validationErrors as $key => $Error) {
                        $msg_error .= "<strong>$key</strong><br>";
                        foreach ($Error as $error) {
                            $msg_error .= "- " . $error . "<br/>";
                        }
                    }
                }
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous : ' . $msg_error, 'growl', array('type' => 'erreur'));
            }
        }

        $deliberation = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $delib_id),
            'recursive' => -1));

        $this->request->data = $deliberation;
        if (!empty($deliberation['Deliberation']['debat']))
            $this->set('file_debat', $this->SabreDav->newFileDav('Debat_' . $delib_id . '.odt', $deliberation['Deliberation']['debat']));
    }

    public function saisirDebatGlobal($id = null) {
        $this->set('seance_id', $id);
        if ($this->request->is('post')) {

            $this->request->data['Seance']['id'] = $id;
            if ($this->Seance->SaveDebatGen($this->data)) {
                $this->Session->setFlash('Débat global enregistré', 'growl');
                return $this->redirect($this->previous);
            } else {
                $msg_error = '';
                if (!empty($this->Seance->validationErrors)) {
                    foreach ($this->Seance->validationErrors as $key => $Error) {
                        $msg_error .= "<strong>$key</strong><br>";
                        foreach ($Error as $error) {
                            $msg_error .= "- " . $error . "<br/>";
                        }
                    }
                }
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous : ' . $msg_error, 'growl', array('type' => 'erreur'));
            }
        }

        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $id),
            'recursive' => -1
        ));

        $this->request->data = $seance;
        if (!empty($seance['Seance']['debat_global']))
            $this->set('file_debat', $this->SabreDav->newFileDav('DebatGlobal_' . $id . '.odt', $seance['Seance']['debat_global']));
    }

    function deleteDebatGlobal($id) {
        $this->Seance->id = $id;
        $data = array(
            'debat_global' => '',
            'debat_global_name' => '',
            'debat_global_size' => 0,
            'debat_global_type' => ''
        );

        if ($this->Seance->save($data, false)) {
            $this->Session->setFlash('Débat supprimé !', 'growl');
            return $this->redirect(array('controller' => 'seances', 'action' => 'SaisirDebatGlobal', $id));
        } else {
            $this->Session->setFlash("Problème survenu lors de la suppression des débats généraux", 'growl', array('type' => 'error'));
            return $this->redirect($this->here);
        }
    }

    function detailsAvis($seance_id = null) {
        $this->Deliberation->Behaviors->attach('Containable');

        // initialisations
        $deliberations = array();
        $delibs = $this->Seance->getDeliberationsId($seance_id);
        foreach ($delibs as $delib_id) {
            $deliberations[] = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                'fields' => array('id', 'objet', 'objet_delib', 'titre', 'etat', 'num_delib'),
                'contain' => array('Theme.libelle', 'Rapporteur.nom', 'Rapporteur.prenom', 'Service.libelle')));
        }
        $date_tmpstp = strtotime($this->Seance->getDate($seance_id));
        $toutesVisees = true;
        $type_id = $this->Seance->getType($seance_id);

        for ($i = 0; $i < count($deliberations); $i++) {
            $deliberation_id = $deliberations[$i]['Deliberation']['id'];
            $delib_seance = $this->Deliberation->Deliberationseance->find('first', array('conditions' => array('Deliberationseance.seance_id' => $seance_id,
                    'Deliberationseance.deliberation_id' => $deliberation_id),
                'recursive' => -1));
            $deliberations[$i]['Deliberation']['avis'] = $delib_seance['Deliberationseance']['avis'];
            $id_service = $deliberations[$i]['Service']['id'];
            $deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
            $deliberations[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($type_id, $deliberations[$i]['Deliberation']['etat']);
            if (empty($deliberations[$i]['Deliberation']['avis']))
                $toutesVisees = false;
        }

        $this->set('deliberations', $deliberations);
        $this->set('date_seance', $date_tmpstp);
        $this->set('seance_id', $seance_id);
        $this->set('canClose', (($date_tmpstp <= strtotime(date('Y-m-d H:i:s'))) && $toutesVisees));
    }

    function donnerAvis($deliberation_id, $seance_id) {
        // Initialisations
        $deliberation = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $deliberation_id),
            'fields' => array('Deliberation.id', 'Deliberation.typeacte_id', 'Deliberation.objet', 'Deliberation.objet_delib', 'Deliberation.etat')));
        $delib_seance = $this->Deliberation->Deliberationseance->find('first', array('recursive' => -1,
            'conditions' => array(
                'Deliberationseance.seance_id' => $seance_id,
                'Deliberationseance.deliberation_id' => $deliberation_id
        )));

        if (!empty($this->data)) {
            if (!array_key_exists('avis', $this->data['Deliberation'])) {
                $this->Seance->invalidate('avis');
            } else {
                $this->Deliberation->Deliberationseance->id = $delib_seance['Deliberationseance']['id'];
                $this->Deliberation->Deliberationseance->set('deliberation_id', $deliberation_id);
                $this->Deliberation->Deliberationseance->set('seance_id', $seance_id);
                $this->Deliberation->Deliberationseance->set('avis', $this->data['Deliberation']['avis'] == 1 ? true : false);
                $this->Deliberation->Deliberationseance->set('commentaire', $this->data['Deliberation']['commentaire']);
                $this->Deliberation->Deliberationseance->save();
                if (!empty($this->data['Deliberation']['seance_id'])) {
                    foreach ($this->data['Deliberation']['seance_id'] as $seance)
                        $this->Deliberation->Deliberationseance->addDeliberationseance($deliberation_id, $seance);
                }

                // ajout du commentaire

                $this->request->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
                $this->request->data['Commentaire']['texte'] = 'A reçu un avis ';
                $this->request->data['Commentaire']['texte'].= ($this->data['Deliberation']['avis'] == 1) ? 'favorable' : 'défavorable';
                if (!empty($this->data['Deliberation']['seance_id']))
                    $this->request->data['Commentaire']['texte'].= ' en ' . $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = ' . $this->Seance->getType($this->data['Deliberation']['seance_id'][0]));
                else
                    $this->request->data['Commentaire']['texte'] .= ' en ' . $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = ' . $this->Seance->getType($seance_id));
                if (!empty($this->data['Deliberation']['seance_id'])) {
                    $this->request->data['Commentaire']['texte'] .= ' du ' . CakeTime::i18nFormat($this->Seance->getDate($this->data['Deliberation']['seance_id'][0]), '%A %d %B %G à %k:%M');
                } else {
                    $this->request->data['Commentaire']['texte'] .= ' du ' . CakeTime::i18nFormat($this->Seance->getDate($seance_id), '%A %d %B %G à %k:%M');
                }
                $this->request->data['Commentaire']['commentaire_auto'] = 1;
                $this->Deliberation->Commentaire->save($this->data);

                $this->redirect(array('controller' => 'seances', 'action' => 'detailsAvis', $seance_id));
            }
        }

        $this->request->data = $deliberation;

        $user = $this->Session->read('user');
        if ($this->Droits->check($user['User']['id'], "Deliberations:editerTous"))
            $afficherTtesLesSeances = true;
        else
            $afficherTtesLesSeances = false;

        //On retire les séances ou le projet est déja inclus
        $deliberationseance = $this->Deliberation->Deliberationseance->find('all', array(
            'fields' => array('Deliberationseance.seance_id'),
            'conditions' => array('Deliberationseance.deliberation_id' => $deliberation_id),
            'recursive' => -1));
        $seance_notinclude = array();
        foreach ($deliberationseance as $seance)
            $seance_notinclude[] = array('Seance.id <>' => $seance['Deliberationseance']['seance_id']);

        $this->set('seances', $this->Seance->generateList($seance_notinclude, $afficherTtesLesSeances, array_keys($this->Session->read('user.Nature'))));
        $this->set('avis', array(true => 'Favorable', false => 'Défavorable'));
        $this->set('avis_selected', $delib_seance['Deliberationseance']['avis']);
        $this->set('commentaire', $delib_seance['Deliberationseance']['commentaire']);
        $this->set('seances_selected', $this->Deliberation->getCurrentSeances($deliberation_id, false));
        $this->set('seance_id', $seance_id);
    }

    function saisirSecretaire($seance_id) {
        $this->set('seance_id', $seance_id);
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'recursive' => -1,
            'fields' => array('id', 'type_id', 'president_id', 'secretaire_id')));
        //Récupération des acteurs convoqué pour la séance
        $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], null, array('id', 'nom', 'prenom'));
        if (empty($acteursConvoques)) {
            $this->Session->setFlash('Aucun acteur convoqué.', 'growl', array('type' => 'erreur'));
            $this->redirect(array('action' => 'listerFuturesSeances'));
        }
        $tab = array();
        foreach ($acteursConvoques as $acteurConvoque)
            $tab[$acteurConvoque['Acteur']['id']] = $acteurConvoque['Acteur']['prenom'] . ' ' . $acteurConvoque['Acteur']['nom'];

        $this->set('acteurs', $tab);

        if (empty($this->data)) {
            $this->set('selectedPresident', $seance['Seance']['president_id']);
            $this->set('selectedActeurs', $seance['Seance']['secretaire_id']);
        } else {
            $this->Seance->id = $seance_id;
            $this->Seance->saveField('president_id', $this->data['Acteur']['president_id']);
            if ($this->Seance->saveField('secretaire_id', $this->data['Acteur']['secretaire_id']))
                $this->redirect(array('action' => 'listerFuturesSeances'));
        }
    }

    function getListActeurs($seance_id, $choixListe = 1) {
        $presents = array();
        $absents = array();
        $mandats = array();
        $mouvements = array();
        $tab = array();

        $delibs = $this->Deliberation->findAll("Deliberation.seance_id = $seance_id");
        $nb_delib = count($delibs);
        foreach ($delibs as $delib)
            array_push($tab, $delib['Deliberation']['id']);

        $conditions = "Listepresence.delib_id=";
        $conditions .= implode(" OR Listepresence.delib_id=", $tab);
        $presences = $this->Listepresence->findAll($conditions, null, 'Acteur.position');
        foreach ($presences as $presence) {
            $acteur_id = $presence['Listepresence']['acteur_id'];
            $tot_presents = $this->Listepresence->findAll("Listepresence.acteur_id =  $acteur_id AND ($conditions) AND Listepresence.present=1");
            $nb_presence = count($tot_presents);
            if ($nb_presence == $nb_delib)
                array_push($presents, $acteur_id);
            elseif ($nb_presence == 0) {
                $tmp = $this->Listepresence->findAll("Listepresence.acteur_id =  $acteur_id AND ($conditions) AND Listepresence.present=0 AND Listepresence.mandataire=0");
                $nb_absence = count($tmp);
                if ($nb_absence == $nb_delib)
                    array_push($absents, $acteur_id);
                else {
                    $tmp2 = $this->Listepresence->findAll("Listepresence.acteur_id =  $acteur_id AND ($conditions) AND Listepresence.present=0 AND Listepresence.mandataire!=0");
                    foreach ($tmp2 as $mandat) {
                        if (!isset($mandat['Listepresence']['acteur_id']))
                            $mandat['Listepresence']['acteur_id'] = array();
                        $mandats[$mandat['Listepresence']['acteur_id']] = $mandat['Listepresence']['mandataire'];
                    }
                }
            }
            else {
                foreach ($tot_presents as $pres) {
                    if (!isset($mouvements[$acteur_id]))
                        $mouvements[$acteur_id] = array();
                    $mouvements[$acteur_id] = $pres['Listepresence']['delib_id'];
                }
            }
        }

        if ($choixListe == 1)
            return(array_unique($presents));
        elseif ($choixListe == 2)
            return(array_unique($absents));
        elseif ($choixListe == 3)
            return(array_unique($mandats));
        elseif ($choixListe == 4)
            return(array_unique($mouvements));
    }

    function download($id = null, $file) {

        $this->autoRender = false;

        $fileType = $file . '_type';
        $fileSize = $file . '_size';
        $fileName = $file . '_name';
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $id),
            'fields' => array($fileType, $fileSize, $fileName, $file),
            'recursive' => -1
        ));

        $this->response->type($seance['Seance'][$fileType]);
        $this->response->download($seance['Seance'][$fileName]);
        $this->response->body($seance['Seance'][$file]);
    }

//Obsolete
    function getFileType($id = null, $file) {
        $objCourant = $this->Seance->read(null, $id);
        return $objCourant['Seance'][$file . "_type"];
    }

//Obsolete
    function getFileName($id = null, $file) {
        $objCourant = $this->Seance->read(null, $id);
        return $objCourant['Seance'][$file . "_name"];
    }

//Obsolete
    function getSize($id = null, $file) {
        $objCourant = $this->Seance->read(null, $id);
        return $objCourant['Seance'][$file . "_size"];
    }

//Obsolete
    function getData($id = null, $file) {
        $objCourant = $this->Seance->find('first', array('conditions' => array('Seance.id' => $id),
            'fields' => array("Seance.$file")));
        return $objCourant['Seance'][$file];
    }

    function saisirCommentaire($seance_id) {
        $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'recursive' => -1));
        $this->set('seance_id', $seance_id);
        if (empty($this->data)) {
            $this->request->data = $seance;
        } else {
            $this->Seance->id = $seance_id;
            if ($this->Seance->saveField('commentaire', $this->data['Seance']['commentaire'])) {
                $this->redirect('/seances/listerFuturesSeances');
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
    }

    function changePosition($seance_id, $new_position, $delib_id) {
        $delib = $this->Deliberation->Deliberationseance->find('first', array(
            'conditions' => array('deliberation_id' => $delib_id,
                'seance_id' => $seance_id),
            'fields' => array('id', 'Deliberationseance.position'),
            'recursive' => '-1'));

        $old_position = $delib['Deliberationseance']['position'];
        if ($new_position < $old_position) {
            $delta = 1;
            $start = $new_position;
            $end = $old_position - 1;
        } else {
            $delta = -1;
            $start = $old_position + 1;
            $end = $new_position;
        }
        $this->Deliberation->Deliberationseance->updateAll(array('Deliberationseance.position' => "Deliberationseance.position+$delta"), array("Deliberationseance.position >= " => $start,
            "Deliberationseance.position <= " => $end,
            "Deliberationseance.seance_id" => $seance_id,
            "Deliberation.etat <> " => -1));

        $this->Deliberation->Deliberationseance->id = $delib['Deliberationseance']['id'];
        $this->Deliberation->Deliberationseance->saveField('position', $new_position);

        $this->Session->setFlash("Projet [id:$delib_id] déplacée en position : $new_position, ancienne position : $old_position ", 'growl');
        $this->redirect("/seances/afficherProjets/$seance_id");
    }

    function clore($seance_id) {
        $this->Seance->Behaviors->attach('Containable');
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields' => array('Seance.type_id', 'Seance.date'),
            'contain' => array('Typeseance.action', 'Typeseance.id')));
        $date_seance = strtotime($seance['Seance']['date']);
        $date_now = strtotime(date('Y-m-d H:i:s'));
        if ($date_seance > $date_now) {
            $this->Session->setFlash('Vous ne pouvez pas clôturer une séance future', 'growl', array('type' => 'erreur'));
            return $this->redirect(array('controller' => 'seances', 'action' => 'listerFuturesSeances'));
        }

        $ids = $this->Seance->getDeliberationsId($seance_id);

        $nbActesNonSigne = $this->Deliberation->find('count', array(
            'conditions' => array(
                'Deliberation.id' => $ids,
                'Deliberation.etat > ' => 1,
                'Deliberation.signee' => false
            ),
            'fields' => array('id'),
            'recursive' => -1
        ));

        if ($nbActesNonSigne > 0 && $seance['Typeseance']['action'] == 0) {
            $this->Session->setFlash('Tous les actes ne sont pas signés.', 'growl', array('type' => 'erreur'));
        } else {
            $this->Seance->id = $seance_id;
            if ($this->Seance->saveField('traitee', 1))
                $this->Session->setFlash("La séance a été cloturée", 'growl', array('type' => 'important'));
            else {
                $this->Session->setFlash("La séance n'a pas été cloturée", 'growl', array('type' => 'erreur'));
            }
        }
        return $this->redirect(array('controller' => 'seances', 'action' => 'listerFuturesSeances'));
    }

    function sendConvocations($seance_id, $model_id) {
        $this->loadModel('Acteurseance');
        $this->Seance->Behaviors->attach('Containable');
        $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'order' => array('date ASC'),
            'fields' => array('id', 'date', 'type_id', 'date_convocation'),
            'contain' => array('Typeseance.libelle', 'Typeseance.action',
                'Typeseance.modelconvocation_id',
                'Typeseance.modelordredujour_id',
                'Typeseance.modelpvsommaire_id',
                'Typeseance.modelpvdetaille_id')));
        $this->set('use_mail_securise', Configure::read('S2LOW_MAILSEC'));

        if (empty($this->data)) {
            $acteurs = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
            foreach ($acteurs as &$acteur) {
                $dates = $this->Acteurseance->find('first', array(
                    'conditions' => array(
                        'Acteurseance.seance_id' => $seance_id,
                        'Acteurseance.model' => 'convocation',
                        'Acteurseance.acteur_id' => $acteur['Acteur']['id']),
                    'recursive' => -1,
                    'fields' => array(
                        'Acteurseance.date_envoi',
                        'Acteurseance.date_reception'
                )));
                $acteur['Acteur']['date_envoi'] = !empty($dates['Acteurseance']['date_envoi']) ? $dates['Acteurseance']['date_envoi'] : null;
                $acteur['Acteur']['date_reception'] = !empty($dates['Acteurseance']['date_reception']) ? $dates['Acteurseance']['date_reception'] : null;
            }

            $model = $this->Modeltemplate->find('first', array(
                'recursive' => -1,
                'conditions' => array('Modeltemplate.id' => $model_id),
                'fields' => array('name')));

            $this->set('model', $model);
            $this->set('acteurs', $acteurs);
            $this->set('seance_id', $seance_id);
            $this->set('date_convocation', $seance['Seance']['date_convocation']);
            $this->set('model_id', $model_id);
        } else {
            $message = '';
            $i = 0;
            foreach ($this->data['Acteur'] as $tmp_id => $bool) {
                $data = array();

                if ($bool) {
                    $i++;
                    $acteur_id = substr($tmp_id, 3, strlen($tmp_id));
                    $acteur = $this->Acteur->find('first', array('conditions' => array('Acteur.id' => $acteur_id),
                        'recursive' => -1));

                    if (file_exists(WEBROOT_PATH . DS . 'files' . DS . 'seances' . DS . $seance_id . DS . $model_id . DS . $acteur['Acteur']['id'] . '.pdf')) {
                        $filepath = WEBROOT_PATH . DS . 'files' . DS . 'seances' . DS . $seance_id . DS . $model_id . DS . $acteur['Acteur']['id'] . '.pdf';
                    } else if (file_exists(WEBROOT_PATH . DS . 'files' . DS . 'seances' . DS . $seance_id . DS . $model_id . DS . $acteur['Acteur']['id'] . '.odt')) {
                        $filepath = WEBROOT_PATH . DS . 'files' . DS . 'seances' . DS . $seance_id . DS . $model_id . DS . $acteur['Acteur']['id'] . '.odt';
                    } else {
                        $message .= $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . ' : Pas de Document' . "<br />";
                        continue;
                    }

                    $searchReplace = array("#NOM#" => $acteur['Acteur']['nom'], "#PRENOM#" => $acteur['Acteur']['prenom']);
                    $template = file_get_contents(CONFIG_PATH . DS . 'emails' . DS . 'convocation.txt');

                    if (Configure::read('S2LOW_MAILSEC')) {
                        //S2low est encodé en iso
                        $content = utf8_decode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $template))));
                        $subject = utf8_decode('Convocation à la séance \'' . $seance['Typeseance']['libelle'] . '\' du : '
                                . CakeTime::i18nFormat($seance['Seance']['date'], '%A %d %B %G à %k:%M'));

                        $data['mailto'] = $acteur['Acteur']['email'];
                        $data['objet'] = $subject;
                        $data['message'] = $content;
                        $data['uploadFile1'] = "@$filepath";

                        $password = Configure::read('S2LOW_MAILSECPWD');
                        if (!empty($password)) {
                            $data['send_password'] = 1;
                            $data['password'] = $password;
                        }
                        $retour = $this->S2low->sendMail($data);
                    } else {
                        $content = str_replace(array_keys($searchReplace), array_values($searchReplace), $template);
                        $subject = 'Convocation à la séance \'' . $seance['Typeseance']['libelle'] . '\' du : '
                                . CakeTime::i18nFormat($seance['Seance']['date'], '%A %d %B %G à %k:%M');

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
                        $this->Email->to = $acteur['Acteur']['email'];
                        $this->Email->sendAs = 'text';
                        $this->Email->charset = 'UTF-8';
                        $this->Email->layout = 'default';
                        $this->Email->subject = $subject;
                        $this->Email->attachments = array($filepath);
                        if ($this->Email->send($content))
                            $retour = 'OK:0';
                        else
                            $retour = 'KO';
                    }

                    if (strpos($retour, 'OK:') !== false) {
                        $mail_id = substr($retour, 3, strlen($retour));
                        $this->Acteurseance->create();
                        $acteurseance['seance_id'] = $seance_id;
                        $acteurseance['acteur_id'] = $acteur_id;
                        $acteurseance['mail_id'] = $mail_id;
                        $acteurseance['date_envoi'] = date("Y-m-d H:i:s", strtotime("now"));
                        $acteurseance['model'] = 'convocation';
                        $this->Acteurseance->save($acteurseance);
                    } else {
                        $message .= $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . ' : Non envoyé' . "<br />";
                    }
                    sleep(5);
                }
            }

            if ($i == 0)
                $this->Session->setFlash('Veuillez sélectionner un acteur au minimum.', 'growl', array('type' => 'erreur'));
            elseif (!empty($message))
                $this->Session->setFlash($message, 'growl', array('type' => 'error'));
            else
                $this->Session->setFlash('Envoi des convocations effectué avec succès', 'growl');

            return $this->redirect(array('controller' => 'seances', 'action' => 'sendConvocations', $seance_id, $model_id));
        }
    }

    function sendOrdredujour($seance_id, $model_id) {
        $this->loadModel('Acteurseance');
        $this->Seance->Behaviors->attach('Containable');
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'order' => array('date ASC'),
            'fields' => array('id', 'date', 'type_id', 'date_convocation'),
            'contain' => array(
                'Typeseance.libelle', 'Typeseance.action',
                'Typeseance.modelconvocation_id',
                'Typeseance.modelordredujour_id',
                'Typeseance.modelpvsommaire_id',
                'Typeseance.modelpvdetaille_id')));
        $this->set('use_mail_securise', Configure::read('S2LOW_MAILSEC'));
        if (empty($this->data)) {
            $acteurs = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
            foreach ($acteurs as &$acteur) {
                $dates = $this->Acteurseance->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('seance_id' => $seance_id,
                        'model' => 'ordredujour',
                        'acteur_id' => $acteur['Acteur']['id']),
                    'fields' => array('date_envoi', 'date_reception')));

                $acteur['Acteur']['date_envoi'] = !empty($dates) ? $dates['Acteurseance']['date_envoi'] : null;
                $acteur['Acteur']['date_reception'] = !empty($dates) ? $dates['Acteurseance']['date_reception'] : null;
            }
            $model = $this->Modeltemplate->find('first', array(
                'conditions' => array('Modeltemplate.id' => $model_id),
                'fields' => array('name'),
                'recursive' => -1));
            $this->set('model', $model);
            $this->set('acteurs', $acteurs);
            $this->set('seance_id', $seance_id);
            $this->set('date_convocation', $seance['Seance']['date_convocation']);
            $this->set('model_id', $model_id);
        } else {
            $i = 0;
            $message = '';
            foreach ($this->data['Acteur'] as $tmp_id => $bool) {
                $data = array();
                if ($bool) {
                    $i++;
                    $acteur_id = substr($tmp_id, 3, strlen($tmp_id));
                    $acteur = $this->Acteur->find('first', array(
                        'conditions' => array('Acteur.id' => $acteur_id),
                        'recursive' => -1));

                    if (file_exists(WEBROOT_PATH . "/files/seances/$seance_id/$model_id/{$acteur['Acteur']['id']}.pdf")) {
                        $filepath = WEBROOT_PATH . "/files/seances/$seance_id/$model_id/{$acteur['Acteur']['id']}.pdf";
                    } else if (file_exists(WEBROOT_PATH . "/files/seances/$seance_id/$model_id/{$acteur['Acteur']['id']}.odt")) {
                        $filepath = WEBROOT_PATH . "/files/seances/$seance_id/$model_id/{$acteur['Acteur']['id']}.odt";
                    } else {
                        $message .= $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . " : Pas de Document <br />";
                        continue;
                    }

                    $searchReplace = array("#NOM#" => $acteur['Acteur']['nom'], "#PRENOM#" => $acteur['Acteur']['prenom']);
                    $template = file_get_contents(CONFIG_PATH . '/emails/ordredujour.txt');
                    $content = utf8_decode(str_replace(array_keys($searchReplace), array_values($searchReplace), $template));
                    $subject = utf8_decode('Ordre du jour de la séance \'' . $seance['Typeseance']['libelle'] . '\' du : '
                            . $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date'])));
                    if (Configure::read('S2LOW_MAILSEC')) {
                        $data['mailto'] = $acteur['Acteur']['email'];
                        $data['objet'] = $subject;
                        $data['message'] = $content;
                        $data['uploadFile1'] = "@$filepath";
                        if (Configure::read('S2LOW_MAILSECPWD') != '') {
                            $data['send_password'] = 1;
                            $data['password'] = Configure::read('S2LOW_MAILSECPWD');
                        }
                        $retour = $this->S2low->sendMail($data);
                    } else {
                        if (Configure::read("SMTP_USE")) {
                            $this->Email->smtpOptions = array(
                                'port' => Configure::read("SMTP_PORT"),
                                'timeout' => Configure::read("SMTP_TIMEOUT"),
                                'host' => Configure::read("SMTP_HOST"),
                                'username' => Configure::read("SMTP_USERNAME"),
                                'password' => Configure::read("SMTP_PASSWORD"),
                                'client' => Configure::read("SMTP_CLIENT")
                            );
                            $this->Email->delivery = 'smtp';
                        } else
                            $this->Email->delivery = 'mail';

                        $this->Email->from = Configure::read("MAIL_FROM");
                        $this->Email->to = $acteur['Acteur']['email'];
                        $this->Email->sendAs = 'text';
                        $this->Email->charset = 'UTF-8';
                        $this->Email->subject = utf8_encode($subject);
                        $this->Email->layout = 'default';
                        $this->Email->attachments = array($filepath);
                        if ($this->Email->send(utf8_encode($content)))
                            $retour = 'OK:0';
                        else
                            $retour = 'KO';
                    }

                    if (strpos($retour, 'OK:') !== false) {
                        $mail_id = substr($retour, 3, strlen($retour));
                        $this->Acteurseance->create();
                        $acteurseance['seance_id'] = $seance_id;
                        $acteurseance['acteur_id'] = $acteur_id;
                        $acteurseance['mail_id'] = $mail_id;
                        $acteurseance['date_envoi'] = date("Y-m-d H:i:s", strtotime("now"));
                        $acteurseance['model'] = 'ordredujour';
                        $this->Acteurseance->save($acteurseance);
                    } else {
                        $message .= $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . ' : Non envoyé' . "<br />";
                    }
                    sleep(5);
                }
            }

            if ($i == 0) {
                $this->Session->setFlash('Veuillez sélectionner au moins un acteur.', 'growl', array('type' => 'erreur'));
            } elseif (!empty($message))
                $this->Session->setFlash($message, 'growl', array('type' => 'error'));

            return $this->redirect(array('controller' => 'seances', 'action' => 'sendOrdredujour', $seance_id, $model_id));
        }
    }

    function genererConvocation($seance_id, $model_id) {
        $this->_generer($seance_id, $model_id, "/seances/sendConvocations/$seance_id/$model_id");
        exit;
    }

    function genererOrdredujour($seance_id, $model_id) {
        $this->_generer($seance_id, $model_id, "/seances/sendOrdredujour/$seance_id/$model_id");
        exit;
    }

    function downloadZip($seance_id, $model_id) {
        $dirpath = WEBROOT_PATH . DS . 'files' . DS . 'seances' . DS . $seance_id . DS . $model_id;
        if (file_exists($dirpath . DS . 'convocations.zip'))
            unlink($dirpath . DS . 'convocations.zip');

        $dir = new Folder($dirpath);
        $zip = new ZipArchive;

        $files = $dir->find('.*\.pdf');
        try {
            if ($zip->open($dirpath . DS . 'convocations.zip', ZIPARCHIVE::CREATE))
                foreach ($files as $file) {
                    $file = new File($dir->pwd() . DS . $file);
                    $acteur_id = $file->name();
                    $acteur = $this->Acteur->find('first', array('conditions' => array('Acteur.id' => $acteur_id),
                        'recursive' => -1,
                        'fields' => array('Acteur.nom')));
                    $zip->addFile($file->path, $acteur['Acteur']['nom'] . '.pdf');
                    $file->close();
                }
            $zip->close();
        } catch (Exception $e) {
            $this->Session->setFlash('Une erreur est survenu lors de la génération de l\'archive', 'growl');
        }

        $content = file_get_contents($dirpath . DS . 'convocations.zip');
        header('Content-type: application/zip');
        header('Content-Length: ' . strlen($content));
        header('Content-Disposition: attachment; filename="Convocation.zip"');
        die($content);
    }
    
    /**
     * Envoi d'une séance et ses projets à i-delibRE
     * @param integer $seance_id
     * @return mixed
     */
    function sendToIdelibre($seance_id) {
        if (!(Configure::read('USE_IDELIBRE'))) {
            $this->Session->setFlash('Le connecteur Idélibre n&apos;est pas activé.<br>Veuillez contacter l&apos;administrateur pour plus d&apos;infos.', 'growl');
            return $this->redirect($this->referer());
        }
        $this->Progress->start(200, 100, 200, '#FFCC00', '#006699');
        $this->Progress->at(0, 'Initialisation');

        $this->Seance->Behaviors->attach('Containable');
        $this->Deliberation->Behaviors->attach('Containable');

        $this->Progress->at(1, 'Récupération des données de la séance...');

        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields' => array('id', 'date', 'type_id'),
            'contain' => array('Typeseance.libelle', 'Typeseance.action', 'Typeseance.id', 'Typeseance.modelconvocation_id', 'Typeseance.action')));

        $acteurs_convoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Typeseance']['id']);

        $tmpDir = new Folder(AppTools::newTmpDir(TMP . 'files' . DS . 'idelibre'));

        // fusion de la convocation
        $filename = $this->Seance->fusionToFile($seance_id, 'convocation', null, $tmpDir->path);

        $this->Progress->at(5, "Génération de la convocation à la séance...");

        $data = array(
            'username' => Configure::read('IDELIBRE_LOGIN'),
            'password' => Configure::read('IDELIBRE_PWD'),
            'conn' => Configure::read('IDELIBRE_CONN'),
            'convocation' => "@$filename"
        );

        $jsonData = array(
            'date_seance' => $seance['Seance']['date'],
            'type_seance' => $seance['Typeseance']['libelle'],
            'acteurs_convoques' => json_encode($acteurs_convoques),
        );

        $this->Progress->at(10, 'Récupération des délibérations de la séance...');
        $i = 0;
        $delibs = $this->Seance->getDeliberationsId($seance_id);
        $num_delib = count($delibs);

        foreach ($delibs as $delib_id) {
            $projet = array();
            $this->Progress->at(10 + ($i + 1) * (50 / $num_delib), 'Génération du projet ' . ($i + 1) . '/' . $num_delib . '...');
            $delib = $this->Deliberation->find('first', array(
                'conditions' => array('Deliberation.id' => $delib_id),
                'contain' => array('Theme.libelle'),
                'fields' => array(
                    'Deliberation.objet',
                    'Deliberation.objet_delib',
                    'Deliberation.typeacte_id',
                    'Deliberation.theme_id',
                    'Deliberation.etat'
            )));

            // fusion du rapport
            $projet_filename = $this->Deliberation->fusionToFile($delib_id, 'rapport', null, $tmpDir->path);
            $projet['libelle'] = $delib['Deliberation']['objet_delib'];
            $projet['ordre'] = $i;
            $projet['theme'] = implode(',', $this->Deliberation->Theme->getLibelleParent($delib['Deliberation']['theme_id']));
            $data['projet_' . $i . '_rapport'] = "@$projet_filename";

            $j = 0;
            $annexes = $this->Deliberation->Annex->getAnnexesWithoutFusion($delib_id);
            $annexesToSend = array();
            foreach ($annexes as $annex) {
                $file = new File($tmpDir->path . DS . $annex['Annex']['filename'], true);
                $file->write($annex['Annex']['data']);
                $data['projet_' . $i . '_' . $j . '_annexe'] = "@" . $file->path;
                $annexesToSend[] = array(
                    'libelle' => $annex['Annex']['titre'],
                    'ordre' => $j
                );
                $j++;
            }
            $projet['annexes'] = $annexesToSend;
            $jsonData['projets'][] = $projet;
            $i++;
        }
        // Encodage en json
        $data['jsonData'] = json_encode($jsonData);
        $this->Progress->at(85, 'Envoi des informations à i-DelibRE...');
        // Initialisation des paramètres curl
        $url = Configure::read('IDELIBRE_HOST') . '/seances.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        /*
          // FIXME certificat https ?
          if (Configure::read('IDELIBRE_USE_CERT')) {
          curl_setopt($ch, CURLOPT_CAPATH, Configure::read('IDELIBRE_CAPATH'));
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
          curl_setopt($ch, CURLOPT_CERTINFO, TRUE);
          curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('IDELIBRE_CERT'));
          curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('IDELIBRE_CERTPWD'));
          curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('IDELIBRE_KEY'));
          }
         */
        // Exécution de la requête
        $retour = curl_exec($ch);
        // Décodage en tableau du retour
        $retour = json_decode($retour, true);
        // Fermeture de la connexion
        curl_close($ch);
        // Log
        $this->log($data, 'idelibre');
        $this->log('Taille totale : ' . AppTools::human_filesize($tmpDir->dirsize(), 4), 'idelibre');
        if (!empty($retour))
            $this->log($retour, 'idelibre');
        else
            $this->log('Aucune réponse', 'idelibre');

        // Suppression du dossier d'annexes temporaire
        $tmpDir->delete();

        try {
            if (empty($retour))
                throw new Exception('Erreur de communication avec i-delibRE.');
            elseif (!empty($retour['error']))
                if (Configure::read('debug') && !empty($retour['name']))
                    throw new Exception(preg_replace('/\s/', ' ', nl2br($retour['name'])));
                else
                    throw new Exception('L\'export vers i-delibRE a échoué.');
            elseif (!empty($retour['success']))
                if (!empty($retour['uuid'])) {
                    $this->Seance->id = $seance_id;
                    $this->Seance->saveField('idelibre_id', $retour['uuid']);

                    if (!empty($retour['message']))
                        $this->Session->setFlash('<strong>Message i-delibRE : </strong><br>' . html_entity_decode($retour['message']), 'growl');
                    else
                        $this->Session->setFlash('Séance envoyée avec succès à i-delibRE.', 'growl');
                } else
                    throw new Exception('Impossible de récupérer l\'identifiant i-delibRE de la séance.');
            else
                throw new Exception('Réponse i-delibRE incorrecte');
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage() . '<br>Veuillez contacter votre administrateur.', 'growl', array('type' => 'error'));
        }


        //Voir pourquoi ne garde pas le message en session
        $this->Progress->end($this->referer());
        return $this->redirect($this->referer());
    }

    /**
     * génération de la fusion d'un modèle pour le premier acteur convoqué et envoi du résultat vers le client
     * @param integer $id id de la séance
     * @param string $typeFusion type de la fusion à générer : conv, adj, ...
     * @param integer $cookieToken numéro de cookie du client pour masquer la fenêtre attendable
     * @return CakeResponse
     */
    function genereFusionToClient($id, $typeFusion, $cookieToken = null) {
        try {
            // vérification de l'existence de la séance
            if (!$this->Seance->hasAny(array('id' => $id)))
                throw new Exception('Séance id:' . $id . ' non trouvée en base de données');

            // vérification des types de fusion
            $allowedFusionTypes = array('convocation', 'ordredujour', 'pvsommaire', 'pvdetaille');
            if (!in_array($typeFusion, $allowedFusionTypes))
                throw new Exception('le type de modèle d\'édition ' . $typeFusion . ' n\'est par autorisé');

            // fusion du document
            $this->Seance->Behaviors->load('OdtFusion', array('id' => $id, 'modelOptions' => array('modelTypeName' => $typeFusion)));
            $filename = $this->Seance->fusionName();
            $this->Seance->odtFusion();

            // selon le format d'envoi du document (pdf ou odt)
            if ($this->Session->read('user.format.sortie') == 0) {
                $mimeType = "application/pdf";
                $filename = $filename . '.pdf';
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult, 'odt', 'pdf');
            } else {
                $mimeType = "application/vnd.oasis.opendocument.text";
                $filename = $filename . '.odt';
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult, 'odt', 'odt');
            }
            unset($this->Seance->odtFusionResult);

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
     * fonction de génération des convocations des acteurs convoqués à une séance et stockage sur file system
     * @param integer $id id de la séance
     * @param integer $modelTemplateId id du template de fusion
     * @param string $typeFusion type de la fusion à générer : conv, adj, ...
     * @param integer $cookieToken numéro de cookie du client pour masquer la fenêtre attendable
     * @return CakeResponse
     */
    function genereFusionToFiles($id, $modelTemplateId, $typeFusion, $cookieToken = null) {
        try {
            // vérification de l'existence de la séance
            if (!$this->Seance->hasAny(array('id' => $id)))
                throw new Exception('Séance id:' . $id . ' non trouvée en base de données');

            // vérification des types de fusion
            $allowedFusionTypes = array('convocation', 'ordredujour');
            if (!in_array($typeFusion, $allowedFusionTypes))
                throw new Exception('le type de modèle d\'édition ' . $typeFusion . ' n\'est pas autorisé');

            // lecture de la liste des acteurs convoqués
            $typeSeanceId = $this->Seance->field('type_id', array('id' => $id));
            $convoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($typeSeanceId, null, array('id'));
            if (empty($convoques))
                throw new Exception('Aucun acteur convoqué pour la séance id:' . $id);

            // format de conversion
            $formatConversion = $this->Session->read('user.format.sortie') == 0 ? 'pdf' : 'odt';

            // initialisation du répertoire de destination des convocations
            App::import('Lib', 'AppGestfichiers');
            $dirpath = AppGestfichiers::formatDirName(WEBROOT_PATH, 'files', 'seances', $id, $modelTemplateId);
            if (is_dir($dirpath))
                AppGestfichiers::clearDir($dirpath);
            AppGestfichiers::creeRepertoire($dirpath);

            // chargement  du behavior de fusion du document
            $this->Seance->Behaviors->load('OdtFusion', array('id' => $id, 'modelTemplateId' => $modelTemplateId, 'modelOptions' => array('modelTypeName' => $typeFusion)));
            // le modèle template possede-t-il des variables de fusion des acteurs
            $acteurPresentTemplate = $this->Seance->modelTemplateOdtInfos->hasUserFieldsDeclared('salutation_acteur', 'prenom_acteur', 'nom_acteur', 'titre_acteur', 'position_acteur', 'email_acteur', 'telmobile_acteur', 'telfixe_acteur', 'date_naissance_acteur', 'adresse1_acteur', 'adresse2_acteur', 'cp_acteur', 'ville_acteur', 'note_acteur');
            // traitement différent en fonction de la présence de variables acteur dans le template
            //FIX les convocations sont automatiquement regénérées au lieu d'être gardées lorsqu'elle sont envoyées
            if ($acteurPresentTemplate) {
                foreach ($convoques as $acteur) {
                    $filename = $acteur['Acteur']['id'] . '.' . $formatConversion;
                    $this->Seance->odtFusion(array('modelOptions' => array('acteurId' => $acteur['Acteur']['id'])));
                    $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', $formatConversion);
                    unset($this->Seance->odtFusionResult);
                    file_put_contents($dirpath . $filename, $content);
                    unset($content);
                }
            } else {
                $this->Seance->odtFusion();
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', $formatConversion);
                unset($this->Seance->odtFusionResult);
                foreach ($convoques as $acteur) {
                    $filename = $acteur['Acteur']['id'] . '.' . $formatConversion;
                    file_put_contents($dirpath . $filename, $content);
                }
                unset($content);
            }

            // mise à jour de la date de génération des convocations
            $this->Seance->save(array('id' => $id, 'date_convocation' => date("Y-m-d H:i:s", strtotime("now"))), false);
        } catch (Exception $e) {
            $this->log('Fusion :' . $e->getMessage() . ' File:' . $e->getFile() . ' Line:' . $e->getLine(), 'error');
            $this->Session->setFlash('erreur lors de la génération du document : ' . $e->getMessage(), 'growl', array('type' => 'erreur'));
        }
        $this->redirect($this->here);
    }

    /**
     * génération de la fusion pour plusieurs séances : l'id du modèle de fusion et les séances a fusionner sont passés dans les données du formulaire
     * @return CakeResponse
     */
    function genereFusionMultiSeancesToClient() {
        try {
            // initialisation de l'id du modèle de fusion
            $modelTemplateId = $this->request->data['Seance']['model_id'];
            unset($this->request->data['Seance']['model_id']);

            // initialisation de la liste des séances sélectionnées
            $seancesIds = array();
            foreach ($this->request->data['Seance'] as $seanceId => $selected)
                if ($selected) {
                    $seanceId = explode('_', $seanceId);
                    $seancesIds[] = $seanceId[1];
                }
            if (empty($seancesIds))
                throw new Exception('aucune séance sélectionnée');

            // fusion du document
            $this->Seance->Behaviors->load('OdtFusion', array('modelTemplateId' => $modelTemplateId, 'modelOptions' => array('modelTypeName' => 'multiseances')));
            $filename = $this->Seance->fusionName();
            $this->Seance->odtFusion(array('modelOptions' => array('seanceIds' => $seancesIds)));

            // selon le format d'envoi du document (pdf ou odt)
            if ($this->Session->read('user.format.sortie') == 0) {
                $mimeType = "application/pdf";
                $filename = $filename . '.pdf';
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', 'pdf');
            } else {
                $mimeType = "application/vnd.oasis.opendocument.text";
                $filename = $filename . '.odt';
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', 'odt');
            }
            unset($this->Seance->odtFusionResult->content->binary);

            // envoi au client
            $this->Session->write('Generer.downloadToken', $this->data['waiter']['token'], false, 3600);
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

}
