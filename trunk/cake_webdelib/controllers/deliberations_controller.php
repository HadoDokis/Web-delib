<?php
class DeliberationsController extends AppController {
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
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'Html2', 'Session');
	var $uses = array('Acteur', 'Deliberation', 'User', 'Annex', 'Typeseance', 'Seance', 'TypeSeance', 'Commentaire','Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Infosupdef', 'Infosup', 'Historique', 'Cakeflow.Circuit',  'Cakeflow.Composition', 'Cakeflow.Etape', 'Cakeflow.Traitement', 'Cakeflow.Visa');
	var $components = array('Gedooo','Date','Utils','Email','Acl','Xacl', 'Iparapheur', 'Filtre', 'Cmis', 'Progress');

	// Gestion des droits
	var $demandeDroit = array(
		'add',
		'edit',
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
		'editerProjetValide', 
		'goNext', 
		'validerEnUrgence',
		'rebond',
                'sendToParapheur',
                'sendToGed'
	);

        var $commeDroit = array(
		'view'=>array('Pages:mes_projets', 'Pages:tous_les_projets', 'downloadDelib'),
		'delete'=>'Deliberations:mesProjetsRedaction',
		'attribuercircuit'=>'Deliberations:mesProjetsRedaction',
		'addIntoCircuit'=>'Deliberations:mesProjetsRedaction',
		'traiter'=>'Deliberations:mesProjetsATraiter',
		'retour'=>'Deliberations:mesProjetsATraiter',
		'attribuerSeance'=>'Deliberations:tousLesProjetsSansSeance'
	);
	var $libelleControleurDroit = 'Projets';
	var $ajouteDroit = array(
                'edit',
		'editerProjetValide',
		'goNext',
                'validerEnUrgence',
                'rebond'
	);
	var $libellesActionsDroit = array(
		'edit' => "Modification d'un projet",
		'editerProjetValide' => 'Editer projets valid&eacute;s',
		'goNext'=> 'Sauter une &eacute;tape',
                'validerEnUrgence'=> 'Valider un projet en urgence',
                'rebond'=> 'Effectuer un rebond',
                'sendToParapheur' => 'Envoie à la signature',
                'sendToGed' => 'Envoie &agrave; une GED'
	);
        var $aucunDroit= array('test');
        var $paginate = array(
                'Deliberation' => array(
                        'fields' => array('Deliberation.id', 'Deliberation.objet',  'Deliberation.num_delib', 'Deliberation.dateAR' ,
                                          'Deliberation.num_pref', 'Deliberation.etat', 'Deliberation.titre', 'Deliberation.tdt_id', 'Deliberation.seance_id', 'Seance.date'),
                        'conditions' => array('Deliberation.etat'=>5),
                        'limit' => 10
                ),
        );


        function test () {
            $delib = $this->Deliberation->read(null, 59);
            $this->Progress->start(200, 100,200, '#000000','#000000','#006699');
            for ($i=0; $i<=2; $i++) {
                $this->Deliberation->create();
                unset($delib['Deliberation']['id']);
                $delib['Deliberation']['objet'] = $delib['Deliberation']['objet']." : $i";
                $this->Deliberation->save($delib); 
            }
            exit;
        }

	function view($id = null) {
		$this->set('previous', $this->referer());

		$this->data = $this->Deliberation->findById($id);
		if (empty($this->data)) {
			$this->Session->setFlash('Invalide id pour la d&eacute;lib&eacute;ration : affichage de la vue impossible.', 'growl');
			$this->redirect('/deliberations/mesProjetsRedaction');
		}
		// Compactage des informations supplémentaires
		$this->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup'], false);

		// Lecture des versions anterieures
		$listeAnterieure=array();
		$tab_anterieure=$this->_chercherVersionAnterieure($id, $this->data, 0, $listeAnterieure, 'view');
		$this->set('tab_anterieure',$tab_anterieure);


		// Lecture des droits en modification
		$user_id = $this->Session->read('user.User.id');
		if ($this->Droits->check($user_id, "Deliberations:edit") && $this->Deliberation->estModifiable($id, $user_id))
	            $this->set('userCanEdit', true);
		else
		    $this->set('userCanEdit', false);

		// Lecture et initialisation des commentaires
                $commentaires = $this->Commentaire->find('all', array('conditions' => array ('Commentaire.delib_id' => $id ,
                                                                                                 'Commentaire.pris_en_compte' => 0),
                                                                           'order' =>  'created ASC'));
                for($i=0; $i< count($commentaires) ; $i++) {
                    $agent = $this->User->find('first', array('conditions' => array(
                                                   'User.id' => $commentaires[$i]['Commentaire']['agent_id']) ,
                                                   'recursive' => -1,
                                                   'fields'    => array('nom', 'prenom') ));
                    $commentaires[$i]['Commentaire']['nomAgent'] = $agent['User']['nom'];
                    $commentaires[$i]['Commentaire']['prenomAgent'] =  $agent['User']['prenom'];
                }
                $this->set ('commentaires', $commentaires );

		$this->set('historiques',$this->Historique->find('all', array('conditions' => array("Historique.delib_id" => $id))));

		// Mise en forme des données du projet ou de la délibération
		$this->data['Deliberation']['libelleEtat'] = $this->Deliberation->libelleEtat($this->data['Deliberation']['etat']);
		if(!empty($this->data['Seance']['date']))
			$this->data['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data['Seance']['date']));
   
		$this->data['Service']['libelle'] = $this->Deliberation->Service->doList($this->data['Service']['id']);
		$this->data['Circuit']['libelle'] = $this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);

		// Définitions des infosup
		//$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
		$this->set('infosupdefs', $this->Infosupdef->find('all', array('order'=> 'ordre', 
                                                                               'recursive'=> -1)));
                $this->set('visu', $this->requestAction('/cakeflow/traitements/visuTraitement/'.$id, array('return')));
	}

	function _getFileData($fileName, $fileSize) {
		return @fread(fopen($fileName, "r"), $fileSize);
	}

	function add() {
		// initialisations
		$sortie = false;
		/* initialisation du lien de redirection */
		$redirect = '/pages/mes_projets';
		/* initialisation du rédateur et du service emetteur */
		$user = $this->Session->read('user');
		if ($this->Xacl->check($user['User']['id'], "Deliberations:editerProjetValide"))
			$afficherTtesLesSeances = true;
		else
			$afficherTtesLesSeances = false;

		if (!empty($this->data)) {
			$this->data['Deliberation']['redacteur_id'] = $user['User']['id'];
			$this->data['Deliberation']['service_id'] = $user['User']['service'];

			if (($this->data['Deliberation']['seance_id'])!=null)
				$this->data['Deliberation']['position'] = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);
			$this->data['Deliberation']['date_limite']= $this->Utils->FrDateToUkDate($this->params['form']['date_limite']);
			if (!Configure::read('GENERER_DOC_SIMPLE')){
				// Initialisation du texte de projet
				if (array_key_exists('texte_projet', $this->data['Deliberation'])) {
					$this->data['Deliberation']['texte_projet_name'] = $this->data['Deliberation']['texte_projet']['name'];
					$this->data['Deliberation']['texte_projet_size'] = $this->data['Deliberation']['texte_projet']['size'];
					$this->data['Deliberation']['texte_projet_type'] = $this->data['Deliberation']['texte_projet']['type'] ;
					if (empty($this->data['Deliberation']['texte_projet']['tmp_name']))
						$this->data['Deliberation']['texte_projet'] = '';
					else {
						$tp = $this->_getFileData($this->data['Deliberation']['texte_projet']['tmp_name'], $this->data['Deliberation']['texte_projet']['size']);
						$this->data['Deliberation']['texte_projet'] = $tp;
					}
				}
				// Initialisation de la note de synthèse
				if (array_key_exists('texte_synthese', $this->data['Deliberation'])) {
					$this->data['Deliberation']['texte_synthese_name'] = $this->data['Deliberation']['texte_synthese']['name'];
					$this->data['Deliberation']['texte_synthese_size'] = $this->data['Deliberation']['texte_synthese']['size'];
					$this->data['Deliberation']['texte_synthese_type'] = $this->data['Deliberation']['texte_synthese']['type'] ;
					if (empty($this->data['Deliberation']['texte_synthese']['tmp_name']))
						$this->data['Deliberation']['texte_synthese'] = '';
					else {
						$ts = $this->_getFileData($this->data['Deliberation']['texte_synthese']['tmp_name'], $this->data['Deliberation']['texte_synthese']['size']);
						$this->data['Deliberation']['texte_synthese'] = $ts;
					}
				}
				// Initialisation du texte de délibération
				if (array_key_exists('deliberation', $this->data['Deliberation'])) {
					$this->data['Deliberation']['deliberation_name'] = $this->data['Deliberation']['deliberation']['name'];
					$this->data['Deliberation']['deliberation_size'] = $this->data['Deliberation']['deliberation']['size'];
					$this->data['Deliberation']['deliberation_type'] = $this->data['Deliberation']['deliberation']['type'] ;
					if (empty($this->data['Deliberation']['deliberation']['tmp_name']))
						$this->data['Deliberation']['deliberation'] = '';
					else {
						$td = $this->_getFileData($this->data['Deliberation']['deliberation']['tmp_name'], $this->data['Deliberation']['deliberation']['size']);
						$this->data['Deliberation']['deliberation'] = $td;
					}
				}
			}

			if ($this->Deliberation->save($this->data)) {
				$this->Filtre->Supprimer();
				$delibId = $this->Deliberation->getLastInsertId();
				// création des fichiers des textes
				if (!Configure::read('GENERER_DOC_SIMPLE')){
					$repDest = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$delibId.DS;
                                        if (!file_exists($repDest)){
					    mkdir($repDest, 0770, true);
                                        }
					$this->Gedooo->createFile($repDest, 'texte_projet.odt',  $this->data['Deliberation']['texte_projet']);
					$this->Gedooo->createFile($repDest, 'texte_synthese.odt', $this->data['Deliberation']['texte_synthese']);
					$this->Gedooo->createFile($repDest, 'deliberation.odt',  $this->data['Deliberation']['deliberation']);
				}
				/* sauvegarde des informations supplémentaires */
				if (array_key_exists('Infosup', $this->data))
					$this->Deliberation->Infosup->saveCompacted($this->data['Infosup'], $delibId);
				/* sauvegarde des annexes */
				if (array_key_exists('AnnexesG', $this->data))
					foreach($this->data['AnnexesG'] as $annexe) $this->_saveAnnexe($delibId, $annexe, 'G');
				if (array_key_exists('AnnexesP', $this->data))
					foreach($this->data['AnnexesP'] as $annexe) $this->_saveAnnexe($delibId, $annexe, 'P');
				if (array_key_exists('AnnexesS', $this->data))
					foreach($this->data['AnnexesS'] as $annexe) $this->_saveAnnexe($delibId, $annexe, 'S');
				if (array_key_exists('AnnexesD', $this->data))
					foreach($this->data['AnnexesD'] as $annexe) $this->_saveAnnexe($delibId, $annexe, 'D');

				$this->Session->setFlash('Le projet \''.$delibId.'\' a &eacute;t&eacute; ajout&eacute;',  'growl');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
		}
		if ($sortie)
			$this->redirect($redirect);
		else {
			$this->data['Service']['libelle'] = $this->Deliberation->Service->doList($user['User']['service']);
			$this->data['Redacteur']['nom'] = $this->User->field('nom', array('User.id' => $user['User']['id']));
			$this->data['Redacteur']['prenom'] = $this->User->field('prenom', array('User.id' => $user['User']['id']));
			
			$this->data['Deliberation']['created'] = date('Y-m-d H:i:s');
			$this->data['Deliberation']['modified'] = date('Y-m-d H:i:s');

			$this->set('themes', $this->Deliberation->Theme->generatetreelist(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
			$this->set('rapporteurs', $this->Acteur->generateListElus('Acteur.nom'));
			$this->set('selectedRapporteur', $this->Acteur->selectActeurEluIdParDelegationId($user['User']['service']));
			$this->set('date_seances',$this->Seance->generateList(null, 
                                                                             $afficherTtesLesSeances, 
                                                                             array_keys($this->Session->read('user.Nature'))));
			$this->set('infosupdefs', $this->Infosupdef->find('all', array('order' => 'ordre', 'recursive'=> -1)));
			$this->set('infosuplistedefs', $this->Infosupdef->generateListes());
			$this->set('redirect', $redirect);

			/* valeurs initiales des info supplémentaires */
			$this->data['Infosup'] = $this->Infosupdef->valeursInitiales();

			$this->render('edit');
		}
	}

	function download($id=null, $file){
		$fileType = $file.'_type';
		$fileSize = $file.'_size';
		$fileName = $file.'_name';
		$fields = "$fileType, $fileSize, $fileName, $file";
		$delib = $this->Deliberation->find("id = $id", $fields, '', -1);

		header('Content-type: '.$delib['Deliberation'][$fileType]);
		header('Content-Length: '.$delib['Deliberation'][$fileSize]);
		header('Content-Disposition: attachment; filename='.$delib['Deliberation'][$fileName]);
		echo $delib['Deliberation'][$file];
		exit();
	}


         function deleteDebat($id, $isCommission){
              $this->Deliberation->id = $id;
              if (! $isCommission)
                  $data = array( 'id'      => $id,
                                 'debat'      => '',
                                 'debat_name' => '',
                                 'debat_size' => 0,
                                 'debat_type' => '' );
              else
                  $data = array( 'id'      => $id,
                                 'commission'      => '',
                                 'commission_name' => '',
                                 'commission_size' => 0,
                                 'commission_type' => '' );
                 
               if ($this->Deliberation->save($data) )
                   $this->redirect("/seances/SaisirDebat/$id");
               
         }

        function downloadDelib($delib_id) {
            $delib = $this->Deliberation->read(null, $delib_id);
            header('Content-type: application/pdf');
            header('Content-Length: '.strlen($delib['Deliberation']['delib_pdf']));
            header('Content-Disposition: attachment; filename='.$delib['Deliberation']['num_delib'].'.pdf');
            echo $delib['Deliberation']['delib_pdf'];
            exit();
	}

	function downloadSignature($delib_id) {
	    $delib = $this->Deliberation->read(null, $delib_id);
            header('Content-type: application/zip');
            header('Content-Length: '.strlen($delib['Deliberation']['signature']));
            header('Content-Disposition: attachment; filename='.$delib['Deliberation']['num_delib'].'.zip');
            echo $delib['Deliberation']['signature'];
            exit();
	}

	function _saveAnnexe ($id, $file, $type) {
                if (is_array($file) && !empty($file['name'])){
                    $this->Annex->create();
                    $this->data['Annex']['deliberation_id'] = $id;
                    $this->data['Annex']['seance_id'] = 0;
                    $this->data['Annex']['titre'] = 'titre'; //$form['titre_'.$counter];
                    $this->data['Annex']['type'] =  $type;
                    $this->data['Annex']['filename'] = $file['name'];
                    $this->data['Annex']['filetype'] = $file['type'];
                    $this->data['Annex']['size'] = $file['size'];
                    $this->data['Annex']['data'] = $this->_getFileData($file['tmp_name'], $file['size']);
                    if(!$this->Annex->save($this->data['Annex'])){
                        echo "pb de sauvegarde de l\'annexe ";
                    }
                }
            return true;
	}


	function _PositionneDelibsSeance($seance_id, $position) {
		$conditions= "Deliberation.seance_id = $seance_id AND Deliberation.position > $position ";
		$delibs = $this->Deliberation->find('all', array ('conditions'=> $conditions));
		foreach ($delibs as $delib) {
			// on enleve pour 1 la delib qui a change de seance..
			$delib['Deliberation']['position']= $delib['Deliberation']['position'] -1;
			$this->Deliberation->save($delib['Deliberation']);
		}
	}

	function edit($id=null) {
		$user=$this->Session->read('user');
		/* initialisation du lien de redirection   */
		$redirect = $this->Session->read('user.User.lasturl');
		$afficherTtesLesSeances = $this->Xacl->check($user['User']['id'], "Deliberations:editerProjetValide");
		
		$pos  =  strrpos ( getcwd(), 'webroot');
		$path = substr(getcwd(), 0, $pos);
		$path_projet = $path."webroot/files/generee/projet/$id/";

		if (empty($this->data)) {
			$this->data = $this->Deliberation->find('first',array('conditions'=>array('Deliberation.id'=> $id)));
			$natures =  array_keys($this->Session->read('user.Nature'));
			if (!in_array($this->data['Deliberation']['nature_id'], $natures)){
				$this->Session->setFlash("Vous ne pouvez pas editer le projet '$id'.", 'growl', array('type'=>'erreur'));
				$this->redirect($redirect);
			}
			/* teste si le projet est modifiable par l'utilisateur connecté */
			if (!$this->Deliberation->estModifiable($id, $user['User']['id'], $this->Xacl->check($user['User']['id'], "Deliberations:editerProjetValide"))) {
				$this->Session->setFlash("Vous ne pouvez pas editer le projet '$id'.", 'growl', array('type'=>'erreur'));
				$this->redirect($redirect);
			}

			// initialisation des fichiers des textes
			if (!Configure::read('GENERER_DOC_SIMPLE')) {
				$this->Gedooo->createFile($path_projet, 'texte_projet.odt',  $this->data['Deliberation']['texte_projet']);
				$this->Gedooo->createFile($path_projet, 'texte_synthese.odt', $this->data['Deliberation']['texte_synthese']);
				$this->Gedooo->createFile($path_projet, 'deliberation.odt',  $this->data['Deliberation']['deliberation']);
			}
			// initialisation des fichiers des infosup de type odtFile
			foreach ($this->data['Infosup']  as $infosup) {
				$infoSupDef = $this->Infosupdef->find('first', array('recursive'=>-1, 'fields'=>array('type'), 'conditions'=>array('id'=>$infosup['infosupdef_id'])));
				if ($infoSupDef['Infosupdef']['type'] == 'odtFile' && !empty($infosup['file_name']) && !empty($infosup['content']))
					$this->Gedooo->createFile($path_projet, $infosup['file_name'] , $infosup['content']);
			}

			$this->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup']);
			$this->data['Deliberation']['date_limite'] = date("d/m/Y",(strtotime($this->data['Deliberation']['date_limite'])));
			$this->data['Service']['libelle'] = $this->Deliberation->Service->doList($this->data['Service']['id']);

			$this->set('themes', $this->Deliberation->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"));
			$this->set('rapporteurs', $this->Acteur->generateListElus('nom'));
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateList(null, $afficherTtesLesSeances, array_keys($this->Session->read('user.Nature'))));
			$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
			$this->set('infosuplistedefs', $this->Infosupdef->generateListes());
			$this->set('redirect', $redirect);
			$this->render();
	
		} else {
			$oldDelib = $this->Deliberation->find('first', array(
				'conditions' =>array('Deliberation.id'=> $id),
				'fields'    => array('seance_id', 'position'))); 
			// Si on definit une seance a une delib, on la position en derniere position de la seance...
			if (!($this->data['Deliberation']['seance_id'] === $oldDelib['Deliberation']['seance_id'])) {
				if ($this->data['Deliberation']['seance_id'])
					$this->data['Deliberation']['position'] = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);
			    else
					$this->data['Deliberation']['position'] = 0;
			}
	
			if (!Configure::read('GENERER_DOC_SIMPLE')) {
				if (array_key_exists('texte_projet', $this->data['Deliberation'])) {
					$this->data['Deliberation']['texte_projet_name'] = $this->data['Deliberation']['texte_projet']['name'];
					$this->data['Deliberation']['texte_projet_size'] = $this->data['Deliberation']['texte_projet']['size'];
					$this->data['Deliberation']['texte_projet_type'] = $this->data['Deliberation']['texte_projet']['type'] ;
					if (empty($this->data['Deliberation']['texte_projet']['tmp_name'])) {
						$this->data['Deliberation']['texte_projet'] = '';
					} else {
						$tp = $this->_getFileData($this->data['Deliberation']['texte_projet']['tmp_name'], $this->data['Deliberation']['texte_projet']['size']);
						$this->data['Deliberation']['texte_projet'] = $tp;
					}
				} else {
					$stat = stat($path_projet.'texte_projet.odt');
					$td = $this->_getFileData($path_projet.'texte_projet.odt', $stat['size'] );
					$this->data['Deliberation']['texte_projet'] = $td;
				}
				// Initialisation de la note de synthèse
				if (array_key_exists('texte_synthese', $this->data['Deliberation'])) {
					$this->data['Deliberation']['texte_synthese_name'] = $this->data['Deliberation']['texte_synthese']['name'];
					$this->data['Deliberation']['texte_synthese_size'] = $this->data['Deliberation']['texte_synthese']['size'];
					$this->data['Deliberation']['texte_synthese_type'] = $this->data['Deliberation']['texte_synthese']['type'] ;
					if (empty($this->data['Deliberation']['texte_synthese']['tmp_name']))
						$this->data['Deliberation']['texte_synthese'] = '';
					else {
						$ts = $this->_getFileData($this->data['Deliberation']['texte_synthese']['tmp_name'], $this->data['Deliberation']['texte_synthese']['size']);
						$this->data['Deliberation']['texte_synthese'] = $ts;
					}
			    } else {
					$stat = stat($path_projet.'texte_synthese.odt');
					$ts = $this->_getFileData($path_projet.'texte_synthese.odt', $stat['size'] );
					$this->data['Deliberation']['texte_synthese'] = $ts;
				}
	
				// Initialisation du texte de délibération
				if (array_key_exists('deliberation', $this->data['Deliberation'])) {
					$this->data['Deliberation']['deliberation_name'] = $this->data['Deliberation']['deliberation']['name'];
					$this->data['Deliberation']['deliberation_size'] = $this->data['Deliberation']['deliberation']['size'];
					$this->data['Deliberation']['deliberation_type'] = $this->data['Deliberation']['deliberation']['type'] ;
					if (empty($this->data['Deliberation']['deliberation']['tmp_name']))
						$this->data['Deliberation']['deliberation'] = '';
					else {
						$td = $this->_getFileData($this->data['Deliberation']['deliberation']['tmp_name'], $this->data['Deliberation']['deliberation']['size']);
						$this->data['Deliberation']['deliberation'] = $td;
					}
				} else {
					$stat = stat($path_projet.'deliberation.odt');
					$ts = $this->_getFileData($path_projet.'deliberation.odt', $stat['size'] );
					$this->data['Deliberation']['deliberation'] = $ts;
				}
			}
	
			$this->data['Deliberation']['date_limite']=$this->Utils->FrDateToUkDate($this->params['form']['date_limite']);
	
			if ($this->Deliberation->save($this->data)) {
				$this->Filtre->supprimer();
				// Si on change une delib de seance, il faut reclasser toutes les delibs de l'ancienne seance...
				if (!empty($oldDelib['Deliberation']['seance_id']) AND ($oldDelib['Deliberation']['seance_id'] != $this->data['Deliberation']['seance_id']))
					$this->_PositionneDelibsSeance($oldDelib['Deliberation']['seance_id'], $oldDelib['Deliberation']['position'] );
				// sauvegarde des informations supplémentaires
				$infossupDefs = $this->Infosupdef->findAll("type='odtFile'", '', '', 0);
				foreach ( $infossupDefs as $infodef) {
					$infodef_id = $infodef['Infosupdef']['id'];
					$infosups = $this->Infosup->findAll("Infosup.infosupdef_id = $infodef_id AND Infosup.deliberation_id = $id");
					foreach ( $infosups  as $infosup) {
						$name = $infosup['Infosup']['file_name'] ;
						if (file_exists($path_projet.$name)){
							$code = $infosup['Infosupdef']['code'];
							$stat = stat($path_projet.$name);
							if ($stat > 0) {
								$infosup['Infosup']['content'] = $this->_getFileData($path_projet.$name, $stat['size'] );
								$this->Infosup->save($infosup);
							}
						}
					}
				}

				if (array_key_exists('Infosup', $this->data)) {
				    $this->Deliberation->Infosup->saveCompacted($this->data['Infosup'], $this->data['Deliberation']['id']);
				}
				/* suppression des annexes */
				if (array_key_exists('AnnexesASupprimer', $this->data))
					foreach($this->data['AnnexesASupprimer'] as $annexeId) $this->Annex->delete($annexeId);
				/* sauvegarde des annexes */
				if (array_key_exists('AnnexesG', $this->data))
					foreach($this->data['AnnexesG'] as $annexe) $this->_saveAnnexe($id, $annexe, 'G');
				if (array_key_exists('AnnexesP', $this->data))
					foreach($this->data['AnnexesP'] as $annexe) $this->_saveAnnexe($id, $annexe, 'P');
				if (array_key_exists('AnnexesS', $this->data))
					foreach($this->data['AnnexesS'] as $annexe) $this->_saveAnnexe($id, $annexe, 'S');
				if (array_key_exists('AnnexesD', $this->data))
					foreach($this->data['AnnexesD'] as $annexe) $this->_saveAnnexe($id, $annexe, 'D');

			        $this->Session->setFlash("Le projet $id a &eacute;t&eacute; enregistr&eacute;", 'growl' );
				$this->redirect($redirect);
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur') );
				$this->set('services', $this->Deliberation->Service->find('list',array('conditions'=>array('Service.actif'=>'1'))));
				$this->set('themes', $this->Deliberation->Theme->find('list',array('conditions'=>array('Theme.actif'=>'1'))));
				$this->set('circuits', $this->Deliberation->Circuit->find('list'));
				$this->set('datelim',$this->data['Deliberation']['date_limite']);
				$this->set('annexes',$this->Annex->find('all', array('conditions'=> array('deliberation_id'=>$id))));
				$this->set('rapporteurs', $this->Acteur->generateListElus('nom'));
				$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
				$this->set('redirect', $redirect);

				$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
				$seances = $this->Seance->findAll($condition);
				foreach ($seances as $seance){
					$retard=$seance['Typeseance']['retard'];
					if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y"))))
						$tab[$seance['Seance']['id']]=$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
					else
						$tab[$seance['Seance']['id']]=$this->Seance->generateList(null, $afficherTtesLesSeances, array_keys($this->Session->read('user.Nature')));
				}
				$this->set('date_seances',$tab);
				$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
				$this->set('infosuplistedefs', $this->Infosupdef->generateListes());
			}
		}
	}

    function recapitulatif($id = null) {
        $user=$this->Session->read('user');
        if (empty($this->data)) {
            if (!$id) {
                $this->Session->setFlash('Invalide id pour la deliberation', 'growl', array('type'=>'erreur'));
                $this->redirect('/deliberations/mesProjetsRedaction');
            }
            $delib = $this->Deliberation->read(null, $id);
            if(!empty($delib['Seance']['date']))
                $delib['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($delib['Seance']['date']));
                if(!empty($delib['Deliberation']['date_limite']))
                    $delib['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($delib['Deliberation']['date_limite']));
                    $delib['Deliberation']['created'] = $this->Date->frenchDateConvocation(strtotime($delib['Deliberation']['created']));
                    $delib['Deliberation']['modified'] = $this->Date->frenchDateConvocation(strtotime($delib['Deliberation']['modified']));
                    $id_service = $delib['Service']['id'];
                    $delib['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
                    $tab_circuit=$delib['Deliberation']['circuit_id'];
                    $delib['Circuit']['libelle']=$this->Circuit->getLibelle($tab_circuit);
                    //on recupere la position de l'user dans le circuit
                    $this->set('deliberation', $delib);
                    $this->set('visu', $this->requestAction('/cakeflow/circuits/visuCircuit/'.$tab_circuit, array('return')));
		}
	}

	function delete($id = null) {
		$delib = $this->Deliberation->read(null, $id);
		if (empty($delib)) {
			$this->Session->setFlash('Invalide id pour le projet de deliberation : suppression impossible', 'growl', array('type'=>'erreur'));
		} else {
                    // Suppression des projets antérieurs potentiels
                    $this->_delAnteProjet($id);
                    
                    $repFichier = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$id.DS;
                    if (is_dir($repFichier)) 
                        $this->_rmDir($repFichier);
                    // Il faut reclasser toutes les delibs de la seance
                    if (!empty($delib['Deliberation']['seance_id']))
                        $this->_PositionneDelibsSeance($delib['Deliberation']['seance_id'], $delib['Deliberation']['position'] );

                    $this->Session->setFlash('Le projet \''.$id.'\' a &eacute;t&eacute; supprim&eacute;.', 'growl');
		}
		$this->redirect('/deliberations/mesProjetsRedaction');
	}

    function _delAnteProjet($delib_id) { 
        $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                                                          'fields'     => array('id', 'anterieure_id'),
                                                          'recursive'  => -1    ));
        if ($this->Deliberation->del($delib_id)) {
            if ( $delib['Deliberation']['anterieure_id'] != 0){
                $this->_delAnteProjet($delib['Deliberation']['anterieure_id']);
            }
            else
                return true;
        }
    }

    function addIntoCircuit($id = null){
        $this->data = $this->Deliberation->find('first',array('conditions' => array('Deliberation.id' => $id)));
        $user_connecte = $this->Session->read('user.User.id');
        if ($this->data['Deliberation']['circuit_id']!= 0){
            // enregistrement de l'historique
            $message = "Projet injecté au circuit : ".$this->Circuit->getLibelle($this->data['Deliberation']['circuit_id']);
            $this->Historique->enregistre($id, $user_connecte, $message);
            $this->data['Deliberation']['date_envoi']=date('Y-m-d H:i:s', time());
            $this->data['Deliberation']['etat']='1';
            if ($this->Deliberation->save($this->data)) {
		// insertion dans le circuit de traitement
                $this->Deliberation->id = $id;
		if ($this->Traitement->targetExists($id)) {
	 	    $this->Circuit->ajouteCircuit($this->data['Deliberation']['circuit_id'], $id, $user_connecte);
                    $members = $this->Traitement->whoIsNext($id);
                    if (empty($members)) {
                        $this->Historique->enregistre($id, $user_connecte, 'Projet valide' );
                        $this->Deliberation->saveField('etat', 2);
                    }
                }
		else {
			$this->Circuit->insertDansCircuit($this->data['Deliberation']['circuit_id'], $id, $user_connecte);
			$options = array(
				'insertion' => array(
					'0' => array(
						'Etape' => array(
							'etape_nom'=>'Rédacteur',
							'etape_type'=>1
							),
						'Visa' => array(
							'0'=>array(
								'trigger_id'=>$user_connecte,
								'type_validation'=>'V'
								)))));
			  $traitementTermine =  $this->Traitement->execute('IN', $user_connecte, $id, $options);
                          if ($traitementTermine) {
                              $this->Historique->enregistre($id, $user_connecte, 'Projet valide' );
                              $this->Deliberation->id = $id;
                              $this->Deliberation->saveField('etat', 2);
                          }
		}
            
		// envoi un mail a tous les membres du circuit
                $listeUsers = $this->Circuit->getAllMembers($this->data['Deliberation']['circuit_id']);
                
                for($i = 1; $i <= count($listeUsers); $i++){
                    if ($i ==1){
                        foreach( $listeUsers[$i] as $user_id)
                            $this->_notifier($id, $user_id, 'traiter');
                    }
                    else {
                        foreach( $listeUsers[$i] as $user_id)
                            $this->_notifier($id, $user_id, 'insertion');
                    }
                }
                $this->Session->setFlash('Projet ins&eacute;r&eacute; dans le circuit', 'growl');
                $this->redirect('/deliberations/mesProjetsRedaction');
            } 
            else {
                $this->Session->setFlash('Probl&egrave;me de sauvegarde.', 'growl', array('type'=>'erreur'));
                $this->redirect('/deliberations/attribuercircuit/'.$id);
            }
        }
        else{
            $this->Session->setFlash('Vous devez assigner un circuit au projet de délibération.', 'growl', array('type'=>'erreur'));
            $this->redirect('/deliberations/recapitulatif/'.$id);
        }
    }

	function attribuercircuit ($id = null, $circuit_id=null, $autoAppel=false) {
                $circuits = $this->Circuit->getList();
		$this->set('circuits', $circuits);

		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('lastPosition', '-1');
			$old_circuit  = $this->data['Deliberation']['circuit_id'];

			//circuit par défaut de l'utilisateur connecté
			if($circuit_id == null)
				$circuit_id = $this->User->circuitDefaut($this->Session->read('user.User.id'), 'id');

			//affichage du circuit existant
			if($circuit_id == null)
				$circuit_id=$this->data['Deliberation']['circuit_id'];
				
			if (isset($circuit_id)){
				$this->set('circuit_id', $circuit_id);
				$this->set('visu', $this->requestAction('/cakeflow/circuits/visuCircuit/'.$circuit_id, array('return')));
			}else
				$this->set('circuit_id','0');
			// initalisation du lien de retour
			if ($autoAppel) {
				$this->set('lien_retour', $this->Session->read('attribuerCircuit.lienRetour'));
			} else {
				$this->Session->write('attribuerCircuit.lienRetour', $this->referer());
				$this->set('lien_retour', $this->referer());
			}
		} else {
		        $this->Deliberation->id = $id;
			$this->data = $this->Deliberation->find('first',array('conditions'=>array("Deliberation.id"=>$id),'recursive'=>-1));

			if ($this->Deliberation->saveField('circuit_id', $circuit_id)) {
                          // cas pour l'editeur en ligne
                           if ((Configure::read('GENERER_DOC_SIMPLE'))&& ($this->data['Deliberation']['texte_projet']=='<br />'))
		 	        $this->Session->setFlash('Attention, le texte projet est vide', 
                                                         'growl', array('type'=>'erreur'));
                           // Cas pour le mode OpenOffice
                            if ((!Configure::read('GENERER_DOC_SIMPLE')) && ($this->data['Deliberation']['texte_projet']==''))
                               $this->Session->setFlash('Attention, le texte projet est vide', 
                                                         'growl', array('type'=>'erreur'));

			    $this->redirect('/deliberations/recapitulatif/'.$id);
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.',  'growl', array('type'=>'erreur'));
		}
	}

	function retour($delib_id) {
		$delib = $this->Deliberation->read(null, $delib_id);
		if (empty($delib))
			$this->redirect($this->referer());

		if (empty($this->data)) {
			$etapes = $this->Traitement->listeEtapes($delib['Deliberation']['id'], array('debut'=>2));
			if (empty($etapes))
				$this->redirect($this->referer());
			$this->set('delib_id', $delib_id);
			$this->set('etapes', $etapes);
		} else {
			$this->Traitement->execute('JP', $this->Session->read('user.User.id'), $delib_id, array('numero_traitement'=>$this->data['Traitement']['etape']));
                        $destinataires = $this->Traitement->whoIsNext($delib_id);
                        foreach($destinataires as $destinataire_id)
                            $this->_notifier($delib_id, $destinataire_id, 'traiter');

			$this->Historique->enregistre($delib_id, $this->Session->read('user.User.id'), "Projet retourné");
			$this->redirect('/');
		}
	}

	function traiter($id = null, $valid=null) {
            $projet = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $id)));
	    if (empty($projet)) {
		$this->Session->setFlash('identifiant invalide pour le projet : '.$id, 'growl', array('type'=>'erreur'));
		$this->redirect('/deliberations/mesProjetsATraiter');
	    }
	    else {
                if ($valid==null) {
                    $nb_recursion=0;
		    $action='view';
                    $listeAnterieure=array();
                    $tab_anterieure=$this->_chercherVersionAnterieure($id, $projet, $nb_recursion, $listeAnterieure, $action);
                    $this->set('tab_anterieure',$tab_anterieure);
                    $commentaires = $this->Commentaire->find('all', array('conditions' => array ('Commentaire.delib_id' => $id ,
                                                                                                 'Commentaire.pris_en_compte' => 0),
                                                                           'order' =>  'created ASC'));
                    for($i=0; $i< count($commentaires) ; $i++) {
                        $agent = $this->User->find('first', array('conditions' => array(
                                                       'User.id' => $commentaires[$i]['Commentaire']['agent_id']) ,
                                                       'recursive' => -1,
                                                       'fields'    => array('nom', 'prenom') ));
                        $commentaires[$i]['Commentaire']['nomAgent'] = $agent['User']['nom'];
			$commentaires[$i]['Commentaire']['prenomAgent'] =  $agent['User']['prenom'];
                    }
		    $this->set('commentaires', $commentaires);
                    if (!empty($projet['Seance']['date']))
                        $projet['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($projet['Seance']['date']));
                        $id_service = $projet['Service']['id'];
                        $projet['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
	                $projet['Circuit']['libelle'] = $this->Circuit->getLibelle($projet['Deliberation']['circuit_id']);
                        $this->set('visu', $this->requestAction('/cakeflow/traitements/visuTraitement/'.$id, array('return')));
                        $this->set('deliberation', $projet);
                        $this->set('historiques',$this->Historique->find('all', array('conditions'=>array("Historique.delib_id" => $id))));
                
                        // Compactage des informations supplémentaires
                        $this->data['Infosup'] = $this->Deliberation->Infosup->compacte($projet['Infosup'], false);
                        $this->set('infosupdefs', $this->Infosupdef->find('all', array(
                                                                          'recursive'=> -1,
                                                                          'order'    => 'ordre')));

		    }
                    else {
	                if ($valid=='1') {
                            $user_id = $this->Session->read('user.User.id'); 
                            $traitementTermine = $this->Traitement->execute('OK', $user_id, $id);
                            $this->Historique->enregistre($id, $user_id, 'Projet vis&eacute;' );
                            if ($traitementTermine) {
                                $this->Deliberation->id = $id;
                                $this->Deliberation->saveField('etat', 2);
                            }
                            else {
                                $destinataires = $this->Traitement->whoIsNext($id);
                                foreach( $destinataires as $destinataire_id)
                                    $this->_notifier($id, $destinataire_id, 'traiter');
                            }
	       	            $this->redirect('/deliberations/mesProjetsATraiter');
	                }
		        else {
                            $this->Deliberation->refusDossier($id);
                            $this->Traitement->execute('KO', $this->Session->read('user.User.id'), $id);
                            // TODO notifier par mail toutes les personnes qui ont deja vise le projet
                            $destinataires = $this->Traitement->whoIsPrevious($id);
                            foreach( $destinataires as $destinataire_id)
                                $this->_notifier($id, $destinataire_id, 'refus');

		            $this->Historique->enregistre($id, $this->Session->read('user.User.id'),  'Projet refusé' );
                            $this->Session->setFlash('Vous venez de refuser le projet : '.$id, 'growl');
                            $this->redirect('/deliberations/mesProjetsATraiter');
	                }
	            }
		}
	}

	function _chercherVersionAnterieure($delib_id, $tab_delib, $nb_recursion, $listeAnterieure, $action)
	{
		$anterieure_id=$tab_delib['Deliberation']['anterieure_id'];

		if ($anterieure_id!=0) {

			$ant=$this->Deliberation->find('first', array('conditions' =>array( "Deliberation.id"=> $anterieure_id),
                                                                      'recursive'  => -1,
                                                                      'fields'     => array('created', 'anterieure_id')));
			$lien=$this->base.'/deliberations/'.$action.'/'.$anterieure_id;
			$date_version=$ant['Deliberation']['created'];

			$listeAnterieure[$nb_recursion]['id']=$anterieure_id;
			$listeAnterieure[$nb_recursion]['lien']=$lien;
			$listeAnterieure[$nb_recursion]['date_version']=$date_version;

			//on stocke les id des delibs anterieures
			$listeAnterieure=$this->_chercherVersionAnterieure($anterieure_id, $ant, $nb_recursion+1, $listeAnterieure, $action);
		}
		return $listeAnterieure;
	}

    function transmit( $message=null, $page=1){
       if ($message!='null')
             $this->set('message', $message);

        $this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
	$this->set('host', Configure::read('HOST') );
        $this->set('dateClassification', $this->_getDateClassification());

        // On affiche que les delibs vote pour.
        $deliberations = $this->paginate('Deliberation');
	for($i = 0; $i < count( $deliberations); $i++) {
	    if (empty($deliberations[$i]['Deliberation']['DateAR'])) {
	        if (isset($deliberations[$i]['Deliberation']['tdt_id'])){
                    $flux   = $this->_getFluxRetour($deliberations[$i]['Deliberation']['tdt_id']);
                    $codeRetour = substr($flux, 3, 1);
		    $deliberations[$i]['Deliberation']['code_retour'] = $codeRetour;

                    if($codeRetour==4) {
                        $dateAR = $this->_getDateAR($res = mb_substr( $flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
                        $this->Deliberation->changeDateAR($deliberations[$i]['Deliberation']['id'], $dateAR);
		        $deliberations[$i]['Deliberation']['DateAR'] =  $dateAR;
                    }
		}
	    }
        }
        $this->set('deliberations', $deliberations);

    }

    function _getDateAR($fluxRetour) {
       // +21 Correspond a la longueur du string : actes:DateReception"
       $date = substr($fluxRetour, strpos($fluxRetour, 'actes:DateReception')+21, 10);
       return ($this->Date->frenchDate(strtotime($date )));
    }

    function _getFluxRetour ($tdt_id) {
        $url = 'https://'.Configure::read('HOST')."/modules/actes/actes_transac_get_status.php?transaction=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
        curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        return($curl_return);
    }

    function getAR($tdt_id, $toFile = false) {
        $toFile = (boolean)$toFile;
        $url = 'https://'.Configure::read('HOST')."/modules/actes/actes_create_pdf.php?trans_id=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
        curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
	if ($toFile == false){
            header('Content-type: application/pdf');
            header('Content-Length: '.strlen($curl_return));
	    header('Content-Disposition: attachment; filename=Acquittement.pdf');
	    echo $curl_return;
	    exit();
	}
	else {
	    return $curl_return;
        }
    }

	function toSend ($seance_id = null){
             $this->Deliberation->Behaviors->attach('Containable');
             if (isset($this->params['filtre']) && ($this->params['filtre']=='hide'))
                    $limit = Configure::read('LIMIT');
                else
                    $limit = null;

                $this->Filtre->initialisation($this->name.':'.$this->action, $this->data);

		$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
		$this->set('host', Configure::read('HOST') );
                $date_classification = $this->_getDateClassification();
                if ($date_classification != false){  
		    $this->set('dateClassification', $date_classification);
		    $this->set('tabNature',          $this->_getNatureListe());
	            $this->set('tabMatiere',         $this->_getMatiereListe());
                } 
                else 
                    $this->set('dateClassification', "Récupérer la classification");

		// On affiche que les delibs vote pour.
                $conditions =  $this->Filtre->conditions();
                $conditions['Deliberation.etat'] = 3;
                $conditions['Deliberation.signee'] = 1;
                $conditions['Deliberation.delib_pdf <>'] = '';
                if ($seance_id != null)
                    $conditions['Deliberation.seance_id'] = $seance_id;
		$deliberations = $this->Deliberation->find('all',array('conditions' => $conditions,
                                                                       'fields' => array( 'Deliberation.objet', 'Deliberation.titre', 'Deliberation.num_pref', 'Deliberation.etat', 'Deliberation.num_delib', 'Deliberation.id', 'Deliberation.seance_id'),
                                                                       'contain'    => array('Seance.id','Seance.traitee', 'Seance.date', 'Seance.Typeseance.libelle', 'Service.libelle', 'Theme.libelle', 'Nature.libelle')));

		for($i = 0; $i < count($deliberations); $i++)
                    $deliberations[$i]['Deliberation'][$deliberations[$i]['Deliberation']['id'].'_num_pref'] = $deliberations[$i]['Deliberation']['num_pref'];
                if (!$this->Filtre->critereExists()){
                     $this->Filtre->addCritere('SeanceId', array('field' => 'Deliberation.seance_id',
                                                                 'inputOptions' => array(
                                                                 'label'=>__('Séances', true),
                                                                 'empty' =>'toutes',
                                                                 'options' => $this->Utils->listFromArray($deliberations,
                                                                                        '/Seance/id',
                                                                                        array('/Seance/date',
                                                                                        '/Seance/Typeseance/libelle'),
                                                                                        '%s : %s'))));
                }
		$this->set('deliberations', $deliberations);
	}


    function _getNatureListe(){
        $tab = array();
    	$doc = new DOMDocument('1.0', 'UTF-8');
        if(!@$doc->load(Configure::read('FILE_CLASS')))
            return false;
        $NaturesActes = $doc->getElementsByTagName('NatureActe');
		foreach ($NaturesActes as $NatureActe)
   		    $tab[$NatureActe->getAttribute('actes:CodeNatureActe')]= utf8_decode($NatureActe->getAttribute('actes:Libelle'));

		return $tab;
    }

	function classification(){
		$this->layout = 'fckeditor';
		$this->set('classification',$this->_getMatiereListe());
	}

    function _getMatiereListe(){

 		$tab = array();
		$xml = @simplexml_load_file(Configure::read('FILE_CLASS'));
                if ($xml===false)
                    return false;
		$namespaces = $xml->getDocNamespaces();
		$xml=$xml->children($namespaces["actes"]);


		foreach ($xml->Matieres->children($namespaces["actes"]) as $matiere1) {
			$mat1=$this->_object2array($matiere1);
			$tab[$mat1['@attributes']['CodeMatiere']] = utf8_decode($mat1['@attributes']['Libelle']);
    		foreach ($matiere1->children($namespaces["actes"]) as $matiere2) {
    			$mat2=$this->_object2array($matiere2);
    			$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere']] = utf8_decode($mat2['@attributes']['Libelle']);
        		foreach ($matiere2->children($namespaces["actes"]) as $matiere3) {
        			$mat3=$this->_object2array($matiere3);
    				$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere'].'.'.$mat3['@attributes']['CodeMatiere']] = utf8_decode($mat3['@attributes']['Libelle']);
        			foreach ($matiere3->children($namespaces["actes"]) as $matiere4) {
        				$mat4=$this->_object2array($matiere4);
    					$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere'].'.'.$mat3['@attributes']['CodeMatiere'].'.'.$mat4['@attributes']['CodeMatiere']] = utf8_decode($mat4['@attributes']['Libelle']);
        				foreach ($matiere4->children($namespaces["actes"]) as $matiere5) {
                			$mat5=$this->_object2array($matiere5);
    						$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere'].'.'.$mat3['@attributes']['CodeMatiere'].'.'.$mat4['@attributes']['CodeMatiere'].'.'.$mat5['@attributes']['CodeMatiere']] = utf8_decode($mat5['@attributes']['Libelle']);
        				}
        			}
				}
			}
		}
        return $tab;
	}

	function _object2array($object){
   		$return = NULL;
    	if(is_array($object)) {
        	foreach($object as $key => $value)
           		$return[$key] = $this->_object2array($value);
    	}
    	else{
        	$var = @get_object_vars($object);
        	if($var)
        	{
            	foreach($var as $key => $value)
               		$return[$key] = $this->_object2array($value);
        	}
        	else
            	return $object;
    	}
		return $return;
	}

        function sendActe ($delib_id = null) {
            $erreur = '';
	    $Tabclassification = array();
	    if (!is_file(Configure::read('FILE_CLASS')))
	     $this->getClassification();
	    $url = 'https://'.Configure::read('HOST').'/modules/actes/actes_transac_create.php';
            $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);
	    foreach ($this->data['Deliberation'] as $id => $bool ){
                 if ($bool == 1){
		     $delib_id = substr($id, 3, strlen($id));
		     if (!isset($this->params['form'][$delib_id."classif2"]))
		         continue;
                     $Tabclassification[$delib_id]= $this->params['form'][$delib_id."classif2"];
		 }
            }
            $nbDelibAEnvoyer = count($Tabclassification);
            $nbEnvoyee = 1;
	    foreach ($this->data['Deliberation'] as $id => $bool ){
	        if ($bool == 1){
		    $delib_id = substr($id, 3, strlen($id));
		    $classification =   $Tabclassification[$delib_id];
                    if (!empty( $classification))
		        $this->Deliberation->changeClassification($delib_id, $classification);
 
  		    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id)));
                    $classification = $delib['Deliberation']['num_pref'];          
                    if (strpos($classification, ' -') != false)
                        $classification = (substr($classification, 0, strpos($classification, ' -')));

		    $class1 = substr($classification , 0, strpos ($classification , '.' ));
		    $rest = substr($classification , strpos ($classification , '.' )+1, strlen($classification));
		    $class2=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class3=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class4=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class5=substr($rest , 0, strpos ($classification , '.' ));

		    //Création du fichier de délibération au format pdf (on ne passe plus par la génération)
                    $file =  $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/fd/null/$delib_id/", "D_$delib_id.pdf",  $delib['Deliberation']['delib_pdf']);
                    if (!file_exists( $file ))
		        die ("Problème lors de la récupération du fichier");
        	    // Checker le code classification
        	    $acte = array(
      	                 'api'           => '1',
     	                 'nature_code'   => $delib['Deliberation']['nature_id'],
     	                 'classif1'      => $class1 ,
     	                 'classif2'      => $class2,
     	                 'classif3'      => $class3,
     	                 'classif4'      => $class4,
     	                 'classif5'      => $class5,
			 //'number'        => $delib['Deliberation']['num_delib'],
			 'number'        => time(),
     	                 'decision_date' => date("Y-m-d", strtotime($delib['Seance']['date'])),
      	                 'subject'       => $delib['Deliberation']['objet'],
      	                 'acte_pdf_file' => "@$file",
     	                 'acte_pdf_file_sign' => "",
   	                 );
		    $nb_pj=0;
		    foreach ($delib['Annex'] as $annexe) {
                        if ($annexe['type'] == 'G') {
			    $pj_file = $this->Gedooo->createFile($path."webroot/files/generee/fd/null/$delib_id/", $annexe['filename'], $annexe['data']);
			    $data["acte_attachments[$nb_pj]"] = "@$pj_file";
      	                    $data["acte_attachments_sign[$nb_pj]"] = "";
		         }
			 $nb_pj++;
                    }

	                 $ch = curl_init();
                         curl_setopt($ch, CURLOPT_URL, $url);
			// curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
                         curl_setopt($ch, CURLOPT_POST, TRUE);
                         curl_setopt($ch, CURLOPT_POSTFIELDS, $acte );
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                         curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
                         curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
                         curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
                         curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
                         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                         curl_setopt($ch, CURLOPT_VERBOSE, true);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			 $curl_return = curl_exec($ch);

			 $pos    = strpos($curl_return, 'OK');
			 $tdt_id = substr  ($curl_return , 3 , strlen($curl_return) );
			 if ($pos === false) {
                             $order   = array("\r\n", "\n", "\r");
                             $replace = '<br />';
                             $curl_return = str_replace($order, $replace, $curl_return);
                             $erreur .= $delib['Deliberation']['objet'].'(' . $delib['Deliberation']['num_delib'].') : '.$curl_return.'<br />';
                         }
			 else {
                              $nbEnvoyee ++;
                              $this->Deliberation->id = $delib_id;
                              $this->Deliberation->saveField('etat', 5);
                              $this->Deliberation->saveField('tdt_id', $tdt_id);

			      curl_close($ch);
		              unlink ($file);
			    }
			}
		    }
                    if ($erreur == '') {
                        $this->Session->setFlash('Actes envoyés correctement au TdT', 'growl');
                        $this->redirect(array('controllers'=>'deliberations', 'action'=>'transmit'));
                    }
                    else {
                        $this->Session->setFlash('Erreur : '. $erreur, 'growl', array('type'=>'erreurTDT'));
                        $this->redirect(array('controllers'=>'deliberations', 'action'=>'toSend'));
                    }
		}


       function _getDateClassification(){
           $doc = new DOMDocument();
	   if(!@$doc->load(Configure::read('FILE_CLASS')))
	       return false;
	   $date = $doc->getElementsByTagName('DateClassification')->item(0)->nodeValue;
	   return ($this->Date->frenchDate(strtotime($date )));
           //return true;
        }

	 	function getClassification($id=null){
			$pos =  strrpos ( getcwd(), 'webroot');
			$path = substr(getcwd(), 0, $pos);

			$url = 'https://'.Configure::read('HOST').'/modules/actes/actes_classification_fetch.php';
		        $data = array(
		     	'api'           => '1',
		         );
		    $url .= '?'.http_build_query($data);
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		//  curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
		    curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
		    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
                    curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('SSLKEY'));
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $reponse = curl_exec($ch);

		    if (curl_errno($ch))
				print curl_error($ch);
		    curl_close($ch);

		    // Assurons nous que le fichier est accessible en ecriture
             		if (!$handle = fopen(Configure::read('FILE_CLASS'), 'w')) {
					echo "Impossible d'ouvrir le fichier (".Configure::read('FILE_CLASS').")";
					exit;
		    	}
		    	// Ecrivons quelque chose dans notre fichier.
		    	elseif (fwrite($handle, $reponse) === FALSE) {
		        	echo "Impossible d'ecrire dans le fichier ($filename)";
		        	exit;
		   	 	}
		    	else
		        	$this->redirect('/deliberations/toSend');
		    	fclose($handle);
		}

        function positionner($id=null, $delta) {
            $projet_courant =  $this->Deliberation->find('first', array(
                                                         'conditions'=>array('Deliberation.id'=> $id, 
                                                                             'Deliberation.etat <>'=>  '-1'),
                                                         'fields' => array('id', 'position', 'seance_id'), 
                                                         'recursive' => '-1'));

            $projet_interve =  $this->Deliberation->find('first', array(
                                                         'conditions'=>array(
                                                           'position'=> $projet_courant['Deliberation']['position']+$delta, 
                                                           'seance_id'=> $projet_courant['Deliberation']['seance_id'],
                                                           'etat <>'   =>  '-1'),
                                                         'recursive' => '-1',
                                                         'fields'    => array('id', 'position')));

            if (!empty($projet_interve)){
                $projet_courant['Deliberation']['position'] += $delta;
                $projet_interve['Deliberation']['position'] -= $delta;
                $this->Deliberation->save($projet_courant);
                $this->Deliberation->save($projet_interve);
            }
            $this->redirect("/seances/afficherProjets/".$projet_courant['Deliberation']['seance_id']);
        }

        function sortby($seance_id, $sortby) {
		    $condition = array ("seance_id"=>$seance_id, "etat <>" => "-1");
		    // Critere de tri
		    if ($sortby == 'theme_id')
		        $sortby = 'Theme.order';
	            elseif  ($sortby == 'service_id')
		        $sortby = 'Service.order';
		    elseif ($sortby == 'rapporteur_id')
		        $sortby = 'Rapporteur.nom';
		    elseif ($sortby == 'titre')
		        $sortby = 'Deliberation.titre';

  		    $deliberations = $this->Deliberation->find('all', array('conditions' => $condition, 'order' => array ("$sortby ASC")));
		    for($i=0; $i<count($deliberations); $i++){
                       $this->Deliberation->id = $deliberations[$i]['Deliberation']['id'];   
                       $this->Deliberation->saveField('position', $i+1);
		    }
		    $this->redirect("/seances/afficherProjets/$seance_id");
	    }

	function textprojetvue ($id = null) {
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="P"'));
		$this->set('deliberation', $this->Deliberation->read(null, $id));
		$this->set('delib_id', $id);
	}

	function textsynthesevue ($id = null) {
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="S"'));
		$this->set('deliberation', $this->Deliberation->read(null, $id));
		$this->set('delib_id', $id);
	}

	function deliberationvue ($id = null) {
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="D"'));
		$this->set('deliberation', $this->Deliberation->read(null, $id));
		$this->set('delib_id', $id);
	}

        function _notifier($delib_id, $user_id, $type) {
            $user = $this->User->read(null, $user_id);

            // Si l'utilisateur accepte les mails
            if ($user['User']['accept_notif']){
                if (Configure::read("SMTP_USE")) {
                    $this->Email->smtpOptions = array( 'port'    => Configure::read("SMTP_PORT"),
                                                       'timeout' => Configure::read("SMTP_TIMEOUT"),
                                                       'host'    => Configure::read("SMTP_HOST"),
                                                       'username'=> Configure::read("SMTP_USERNAME"),
                                                       'password'=> Configure::read("SMTP_PASSWORD"),
                                                       'client'  => Configure::read("SMTP_CLIENT"));
                    $this->Email->delivery = 'smtp';
                }
		else
                    $this->Email->delivery = 'mail';

                $this->Email->from = Configure::read("MAIL_FROM");
                $this->Email->to =  $user['User']['email'];
                $this->Email->sendAs = 'text';
                $this->Email->charset = 'UTF-8';

                $delib = $this->Deliberation->read(null, $delib_id);
                if ($type == 'insertion'){
                    if ($user['User']['mail_insertion']) {
                        $this->set('data',  $this->_paramMails('insertion', $delib,  $user['User']));
                        $this->Email->subject = "vous allez recevoir la delib : $delib_id";
                        $this->Email->template = 'insertion';
                    }
                }
                if ($type == 'traiter'){
                    if ($user['User']['mail_traitement']) {
                        $this->Email->subject = "vous avez le projet (id : $delib_id) à traiter";
                        $this->set('data',  $this->_paramMails('traiter', $delib,  $user['User']));
                        $this->Email->template = 'traiter';
                    }
                }
                if ($type == 'refus'){
                    if ($user['User']['mail_refus']) {
                        $this->Email->subject = "Le projet (id : $delib_id) a été refusé";
                        $this->set('data', $this->_paramMails('refus', $delib,  $user['User']));
                        $this->Email->template = 'refus';
                    }
                }
                $this->Email->attachments = null;
                $this->Email->send();
            }
	}

	function _getListPresent($delib_id){
	    return $this->Listepresence->find('all', array('conditions'=>array("Listepresence.delib_id" => $delib_id), 'order'=>array("Acteur.position ASC")));
	}

	function listerPresents($delib_id) {
		if (empty($this->data)) {
			$presents = $this->_getListPresent($delib_id);
			foreach($presents as $present){
		    	$this->data[$present['Listepresence']['acteur_id']]['present'] = $present['Listepresence']['present'];
			    $this->data[$present['Listepresence']['acteur_id']]['mandataire'] = $present['Listepresence']['mandataire'];
			}
			$this->set('presents',$presents);
			$this->set('mandataires', $this->Acteur->generateListElus());
			$this->set('delib_id', $delib_id);
		} else {
			$nbConvoques = 0;
			$nbVoix = 0;
			$nbPresents = 0;
			$this->_effacerListePresence($delib_id);
			foreach($this->data as $acteur_id => $tab){
				$this->Listepresence->create();
				if (!is_int($acteur_id))
					continue;

				$nbConvoques++;
			    $this->data['Listepresence']['acteur_id'] = $acteur_id;

			    if (isset($tab['present'])){
			        $this->data['Listepresence']['present'] = $tab['present'];
			    	if ($tab['present']==1) {
			    	    $nbPresents++;
			    	    $nbVoix++;
			    	}
			    }
			    if (isset($tab['mandataire']) && !empty($tab['mandataire'])) {
					$this->data['Listepresence']['mandataire'] = $tab['mandataire'];
		    	    $nbVoix++;
			    } else
			    	$this->data['Listepresence']['mandataire'] =0;

 			    $this->data['Listepresence']['delib_id']=$delib_id;
			 	$this->Listepresence->save($this->data['Listepresence']);
			}

			//if ($nbVoix < ($nbConvoques/2)) {
			     //   $this->_reporteDelibs($delib_id);
                       // }
			$this->redirect('/seances/voter/'.$delib_id);
		}

	}

	function _reporteDelibs($delib_id) {
		$seance_id = $this->Deliberation->getCurrentSeance($delib_id);
		$position  = $this->Deliberation->getCurrentPosition($delib_id);
		$conditions = "Deliberation.seance_id=$seance_id AND Deliberation.position>=$position";
		$delibs = $this->Deliberation->findAll($conditions);
		foreach ($delibs as $delib)
			$this->Deliberation->changeSeance($delib['Deliberation']['id'], 0);
		$this->Session->setFlash("Le quorum n\'est plus atteint...", 'growl', array('type'=>'erreur'));
		$this->redirect('seances/listerFuturesSeances');
		exit;
	}

	function _effacerListePresence($delib_id) {
		$this->Listepresence->deleteAll(array("delib_id" => $delib_id));
		/*$condition = "delib_id = $delib_id";
		$presents = $this->Listepresence->findAll($condition);
		foreach($presents as $present)
  		    $this->Listepresence->del($present['Listepresence']['id']);*/
	}

	function _buildFirstList($delib_id) {
		$seanceId = $this->Deliberation->field('seance_id', "Deliberation.id=$delib_id");
		$typeSeanceId = $this->Seance->field('type_id', "Seance.id=$seanceId");
		$elus = $this->Typeseance->acteursConvoquesParTypeSeanceId($typeSeanceId, true);

		foreach ($elus as $elu){
			$this->Listepresence->create();
			$this->params['data']['Listepresence']['acteur_id']=$elu['Acteur']['id'];
			$this->params['data']['Listepresence']['mandataire'] = '0';
			$this->params['data']['Listepresence']['present']= 1;
			$this->params['data']['Listepresence']['delib_id']= $delib_id;
			$this->Listepresence->save($this->params['data']);
		}
		return $this->Listepresence->findAll("delib_id =$delib_id");
	}

	function _copyFromPreviousList($delib_id){
		$position = $this->Deliberation->getCurrentPosition($delib_id);
		$seance_id = $this->Deliberation->getCurrentSeance($delib_id);
		$previousDelibId= $this->_getDelibIdByPosition($seance_id, $position);
		$condition = "delib_id = $previousDelibId";
		$previousPresents = $this->Listepresence->findAll($condition);

		foreach ($previousPresents as $present){
			$this->Listepresence->create();
			$this->params['data']['Listepresence']['acteur_id']=$present['Listepresence']['acteur_id'];
			$this->params['data']['Listepresence']['mandataire'] = $present['Listepresence']['mandataire'];
			$this->params['data']['Listepresence']['present']= $present['Listepresence']['present'];
			$this->params['data']['Listepresence']['delib_id']= $delib_id;
			$this->Listepresence->save($this->params['data']);
		}
                $liste = $this->Listepresence->findAll("delib_id =$delib_id");
                if (!empty($liste))
                    return  $liste;
                else
                    return ($this->_buildFirstList($delib_id));
	}

	function _getDelibIdByPosition ($seance_id, $position){
        $condition = "seance_id = $seance_id AND Deliberation.position = $position -1 AND Deliberation.etat != -1";
		$delib = $this->Deliberation->findAll($condition);
		if (isset($delib['0']['Deliberation']['id']))
			return $delib['0']['Deliberation']['id'];
		else
			return 0;
	}

	function afficherListePresents($delib_id=null)	{
		$condition = "Listepresence.delib_id= $delib_id";
		$presents = $this->Listepresence->findAll($condition, null, "Acteur.position ASC");
		if ($this->Deliberation->isFirstDelib($delib_id) and (empty($presents)))
			$presents = $this->_buildFirstList($delib_id);

		// Si la liste est vide, on recupere la liste des present lors de la derbiere deliberation.
		// Verifier que la liste precedente n'est pas vide...
		if (empty($presents))
			$presents = $this->_copyFromPreviousList($delib_id);

		for($i=0; $i<count($presents); $i++){
			if ($presents[$i]['Listepresence']['mandataire'] !='0') {
				$mandataire = $this->Acteur->read('nom, prenom', $presents[$i]['Listepresence']['mandataire']);
			    $presents[$i]['Listepresence']['mandataire'] = $mandataire['Acteur']['prenom'].$mandataire['Acteur']['nom'];
			}
		}
		return ($presents);
        }

        function _getModelId($delib_id) {
             $data = $this->Deliberation->read(null, $delib_id);
	     $seance = $this->Seance->read(null, $data['Deliberation']['seance_id'] );
	     if (!empty($seance)){
	         if ($data['Deliberation']['etat']<3)
		     return $seance['Typeseance']['modelprojet_id'];
	         else
                     return $seance['Typeseance']['modeldeliberation_id'];
	     }
	     else {
                  return 1;
	     }
	}

	function _paramMails($type, $delib, $acteur) {
		$handle  = fopen(CONFIG_PATH.'/emails/'.$type.'.txt', 'r');
		$content = fread($handle, filesize(CONFIG_PATH.'/emails/'.$type.'.txt'));
		$addr1    = "http://".$_SERVER['SERVER_NAME'].$this->base."/deliberations/traiter/".$delib['Deliberation']['id'];
		$addr2    = "http://".$_SERVER['SERVER_NAME'].$this->base."/deliberations/view/".$delib['Deliberation']['id'];

		$searchReplace = array(
			"#NOM#" => $acteur['nom'],
			"#PRENOM#" => $acteur['prenom'],
			"#IDENTIFIANT_PROJET#"=> $delib['Deliberation']['id'],
			"#OBJET_PROJET#"=> $delib['Deliberation']['objet'],
			"#TITRE_PROJET#"=> $delib['Deliberation']['titre'],
			"#LIBELLE_CIRCUIT#"=>  $this->Circuit->getLibelle($delib['Circuit']['id']),
			"#ADRESSE_A_TRAITER#" =>  $addr1,
			"#ADRESSE_A_VISUALISER#" =>  $addr2
		);

		return utf8_encode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $content))));
	}

/*
 * Affiche la liste des projets en cours de redaction (etat = 0) dont l'utilisateur connecté
 * est le rédacteur.
 */
	function mesProjetsRedaction() {
                if (isset($this->params['filtre']) && ($this->params['filtre']=='hide'))
                    $limit = Configure::read('LIMIT');
                else
                    $limit = null; 

                $this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
                $this->Deliberation->Behaviors->attach('Containable');

		$userId=$this->Session->read('user.User.id');
		$listeLiens = $this->Xacl->check($userId, "Deliberations:add") ? array('add') : array();

                $conditions =  $this->Filtre->conditions();
                if (!isset($conditions['Deliberation.nature_id']))
                    $conditions['Deliberation.nature_id'] = array_keys($this->Session->read('user.Nature'));
		$conditions['Deliberation.etat'] = 0;
                $conditions['Deliberation.redacteur_id'] = $userId;

		$ordre = array('Deliberation.created DESC');

		$projets = $this->Deliberation->find('all', array('conditions' => $conditions, 
                                                                  'limit'     => $limit,
                                                                  'ordre' => $ordre, 
                                                                  'contain'    => array( 'Seance.id','Seance.traitee', 'Seance.date', 'Seance.Typeseance.libelle', 'Service.libelle', 'Theme.libelle', 'Nature.libelle')));
                $this->_ajouterFiltre($projets);
		$this->_afficheProjets(
			$projets,
			'Mes projets en cours de r&eacute;daction',
			array('view', 'edit', 'delete', 'attribuerCircuit', 'generer'),
			$listeLiens);
	}

/*
 * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
 * de validation de l'utilisateur connecté et dont le tour de validation est venu.
 */
    function mesProjetsATraiter() {
        if (isset($this->params['filtre']) && ($this->params['filtre']=='hide'))
            $limit = Configure::read('LIMIT');
        else
            $limit = null;

		$this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
		$this->Deliberation->Behaviors->attach('Containable');
		$conditions =  $this->Filtre->conditions();
		$conditions['Deliberation.etat'] =  1;
		$conditions['Deliberation.id'] = $this->Traitement->listeTargetId($this->Session->read('user.User.id'), array('etat'=>'NONTRAITE', 'traitement'=>'AFAIRE')); 

		$ordre = 'Deliberation.created DESC'; 
		$projets = $this->Deliberation->find('all', array(
			'conditions' => $conditions,
			'order'      =>  $ordre, 
			'limit'      => $limit,
			'contain'    => array( 
				'Seance.id','Seance.traitee', 'Seance.date', 'Seance.Typeseance.libelle',
				'Service.libelle',
				'Theme.libelle',
				'Nature.libelle')));
        $this->_ajouterFiltre($projets);
        $this->_afficheProjets($projets, 'Mes projets &agrave; traiter', array('traiter', 'generer'));
    }

/*
 * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
 * de validation de l'utilisateur connecté et dont ce n'est pas le tour de valider et les projets
 * dont il est le rédacteur
 */
	function mesProjetsValidation() {
		if (isset($this->params['filtre']) && ($this->params['filtre']=='hide'))
			$limit = Configure::read('LIMIT');
		else
			$limit = null;

		$userId=$this->Session->read('user.User.id');

		$this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
		$this->Deliberation->Behaviors->attach('Containable');

		$conditions =  $this->Filtre->conditions();
		$conditions['Deliberation.etat'] =  1;
		$conditions['OR']['Deliberation.id'] = $this->Traitement->listeTargetId($this->Session->read('user.User.id'), array('etat'=>'NONTRAITE', 'traitement'=>'NONAFAIRE')); 
		$conditions['OR']['Deliberation.redacteur_id'] = $userId;

		$ordre = 'Deliberation.created DESC';
		$projets = $this->Deliberation->find('all', array(
			'conditions' => $conditions,
			'order'      => $ordre, 
			'limit'      => $limit, 
			'contain'    => array(
				'Seance.id', 'Seance.traitee', 'Seance.date', 'Seance.Typeseance.libelle',
				'Service.libelle',
				'Theme.libelle',
				'Nature.libelle')));
		$this->_ajouterFiltre($projets);
		$this->_afficheProjets(
			$projets,
			'Mes projets en cours d\'&eacute;laboration et de validation',
			array('view', 'generer'));
	}

/*
 * Affiche les projets validés (etat = 2) dont l'utilisateur connecté est le rédacteur
 * ou qu'il est dans les circuits de validation des projets
 */
	function mesProjetsValides() {
		if (isset($this->params['filtre']) && ($this->params['filtre']=='hide'))
			$limit = Configure::read('LIMIT');
		else
			$limit = null;

		$this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
		$this->Deliberation->Behaviors->attach('Containable');

		$conditions =  $this->Filtre->conditions();

		$userId=$this->Session->read('user.User.id');
		$editerProjetValide = $this->Xacl->check($userId, "Deliberations:editerProjetValide");

		$conditions['Deliberation.etat'] =  2;
		$conditions['OR']['Deliberation.id'] = $this->Traitement->listeTargetId($this->Session->read('user.User.id'), array('etat'=>'TRAITE', 'targetConditions' => array('Deliberation.etat'=>2))); 
		$conditions['OR']['Deliberation.redacteur_id'] = $userId;

		$ordre = 'Deliberation.created DESC';

		$projets = $this->Deliberation->find('all', array(
			'conditions' => $conditions,
			'order'      =>  $ordre,
			'limit'      => $limit,
			'contain'    => array(
				'Seance.id','Seance.traitee', 'Seance.date', 'Seance.Typeseance.libelle',
				'Service.libelle',
				'Theme.libelle',
				'Nature.libelle')));

		$this->_ajouterFiltre($projets);
		$this->_afficheProjets(
			$projets,
			'Mes projets valid&eacute;s',
			array('view', 'generer'));
	}

/*
 * fonction générique pour afficher les projets sour forme d'index
 */
	function _afficheProjets(&$projets, $titreVue, $listeActions, $listeLiens=array()) {
		// initialisation de l'utilisateur connecté et des droits
		$userId = $this->Session->read('user.User.id');
		$editerProjetValide = $this->Xacl->check($userId, "Deliberations:editerProjetValide");

		$this->data = $projets;

		/* initialisation pour chaque projet ou délibération */
                foreach($this->data as $i=>$projet) {
			// initialisation des icônes
                     $this->data[$i]['last_viseur'] = $this->Traitement->dernierVisaTrigger($projet['Deliberation']['id']);
                     $this->data[$i]['Circuit']['libelle'] = $this->Circuit->getLibelle($this->data[$i]['Deliberation']['circuit_id']);
        	if ($this->data[$i]['Deliberation']['etat'] == 0 && $this->data[$i]['Deliberation']['anterieure_id']!=0)
	            $this->data[$i]['iconeEtat'] = $this->_iconeEtat(-1);
        	elseif ($this->data[$i]['Deliberation']['etat'] == 1) {
				$estDansCircuit = $this->Traitement->triggerDansTraitementCible($userId, $this->data[$i]['Deliberation']['id']);
				$tourDansCircuit = $estDansCircuit ? $this->Traitement->positionTrigger($userId, $this->data[$i]['Deliberation']['id']) : 0;
				$estRedacteur = $userId == $this->data[$i]['Deliberation']['redacteur_id'];
				$this->data[$i]['iconeEtat'] = $this->_iconeEtat(1, false, $estDansCircuit, $estRedacteur, $tourDansCircuit);
				$this->data[$i]['Circuit']['libelle'] = $this->Circuit->getLibelle($this->data[$i]['Deliberation']['circuit_id']);
        	}
        	else{
                     $this->data[$i]['iconeEtat'] = $this->_iconeEtat($this->data[$i]['Deliberation']['etat'], $editerProjetValide);
		}		
			// initialisation des actions
			$this->data[$i]['Actions'] = $listeActions;
                        if ($this->data[$i]['Deliberation']['etat'] != 1){
                            $this->data[$i]['Actions'] = array_flip ($this->data[$i]['Actions']);
                            unset($this->data[$i]['Actions']['goNext']);
                            unset($this->data[$i]['Actions']['validerEnUrgence']);
                            $this->data[$i]['Actions'] = array_flip ($this->data[$i]['Actions']);
                        }
			if ($this->data[$i]['Deliberation']['etat'] == 2 && $editerProjetValide) {
				$this->data[$i]['Actions'][] = 'edit';
				$this->data[$i]['Actions'][] = 'attribuerCircuit';
		    }
			// initialisation des dates, modèle et service
			if (isset($this->data[$i]['Seance']['date'])) {
				$this->data[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data[$i]['Seance']['date']));
				$this->data[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($this->data[$i]['Seance']['type_id'], $this->data[$i]['Deliberation']['etat']);
			}
			else
				$this->data[$i]['Model']['id'] = 1;
			
			if (isset($this->data[$i]['Service']['id']))
				$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);
	        $this->data[$i]['Seance']['libelle'] = $this->Typeseance->getLibelle($this->data[$i]['Seance']['type_id']);
			if (isset($this->data[$i]['Deliberation']['date_limite']))
				$this->data[$i]['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($this->data[$i]['Deliberation']['date_limite']));
		}

		// passage des variables à la vue
		$this->set('titreVue', $titreVue);
		$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
		$this->set('listeLiens', $listeLiens);
		
		// on affiche la vue index
		$this->render('index');
	}


/*
 * Affiche la liste de tous les projets dont le rédacteur fait parti de mon/mes services
 * Permet de valider en urgence un projet
 */

    function projetsMonService() {
        $services_id = array();
        $this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
        $this->Deliberation->Behaviors->attach('Containable');
        $conditions =  $this->Filtre->conditions();
        // lecture en base
        foreach ($this->Session->read('user.Service') as $service_id => $service)
            $services_id[] = $service_id;
        $conditions['Deliberation.service_id'] = $services_id;
        $conditions['Deliberation.etat !=']    = -1;
        $conditions['Deliberation.etat <']     = 3;
        $ordre = 'Deliberation.created DESC';
        $projets = $this->Deliberation->find('all', array( 'conditions' => $conditions,
                                                           'order'      => $ordre,
                                                           'contain'    => array( 'Seance.id',
                                                                                 'Seance.traitee',
                                                                                 'Seance.date', 
                                                                                 'Seance.Typeseance.libelle',
                                                                                 'Service.libelle', 
                                                                                 'Theme.libelle',
                                                                                 'Nature.libelle')));
        $actions = array('view', 'generer');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:validerEnUrgence"))
            array_push($actions, 'validerEnUrgence');
        if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:goNext"))
            array_push($actions, 'goNext');

            $this->_ajouterFiltre($projets);
            $this->_afficheProjets($projets,
                                   'Projets dont le rédacteur fait partie de mon service',
                                   $actions);

}

/*
 * Affiche la liste de tous les projets en cours de validation
 * Permet de valider en urgence un projet
 */
	function tousLesProjetsValidation() {
                $this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
                $this->Deliberation->Behaviors->attach('Containable');
                $conditions =  $this->Filtre->conditions();
		// lecture en base
		$conditions['Deliberation.etat'] = 1;
		$ordre = 'Deliberation.created DESC';
		$projets = $this->Deliberation->find('all', array('conditions' => $conditions,
                                                                  'order'      =>  $ordre,
                                                                  'contain'    => array( 'Seance.id','Seance.traitee', 
                                                                                         'Seance.date', 'Seance.Typeseance.libelle', 
                                                                                         'Service.libelle', 'Theme.libelle', 
                                                                                         'Nature.libelle')));


                $actions = array('view', 'generer');
                if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:validerEnUrgence"))
		    array_push($actions, 'validerEnUrgence');
                if ($this->Droits->check($this->Session->read('user.User.id'), "Deliberations:goNext"))
		    array_push($actions, 'goNext');

                $this->_ajouterFiltre($projets);
		$this->_afficheProjets(
			$projets,
			'Projets en cours d\'&eacute;laboration et de validation',
			$actions);
	}

/*
 * Affiche la liste de tous les projets en cours de redaction, validation, validés sans séance
 * Permet de modifier un projet validé si l'utilisateur à les droits editerProjetValide
 */
	function tousLesProjetsSansSeance() {
                $this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
                $this->Deliberation->Behaviors->attach('Containable');
                $conditions =  $this->Filtre->conditions();
                if (!isset( $conditions['Deliberation.nature_id']))
                    $conditions['Deliberation.nature_id'] = array_keys($this->Session->read('user.Nature'));
		// lecture en base
		$projets = $this->Deliberation->find('all',array('conditions'=>array($conditions,
								'OR'=>array(
									array('Deliberation.seance_id'=>null),
									array('Deliberation.seance_id'=>0)
								),
								'AND'=>array(
									array(
										'OR'=>array(
											array('Deliberation.etat'=>0),
											array('Deliberation.etat'=>1),
											array('Deliberation.etat'=>2)
								)))),
								'order'=>array('Deliberation.created DESC'), 
                                                                'contain'    => array( 'Seance.id','Seance.traitee', 
                                                                                       'Seance.date', 'Seance.Typeseance.libelle', 
                                                                                       'Service.libelle', 'Theme.libelle', 
                                                                                       'Nature.libelle')));

        $afficherTtesLesSeances = $this->Xacl->check($this->Session->read('user.User.id'), "Deliberations:editerProjetValide");
        $this->set('date_seances',$this->Seance->generateList(null, 
                                                              $afficherTtesLesSeances,  
                                                              array_keys($this->Session->read('user.Nature'))));
        $this->_ajouterFiltre($projets);

        $this->_afficheProjets(
			$projets,
			'Projets non associ&eacute;s &agrave; une s&eacute;ance',
			 array('view', 'generer', 'attribuerSeance'));
	}

/*
 * Affiche la liste de tous les projets validés liés à une séance
 */
	function tousLesProjetsAFaireVoter() {
                $this->Filtre->initialisation($this->name.':'.$this->action, $this->data);
                $this->Deliberation->Behaviors->attach('Containable');

                $conditions =  $this->Filtre->conditions();
                if (!isset($conditions['Deliberation.nature_id']))
                    $conditions['Deliberation.nature_id'] = array_keys($this->Session->read('user.Nature'));
		$conditions['Deliberation.etat'] = 2;
		$conditions['Deliberation.seance_id !='] = 0;
		$projets = $this->Deliberation->find('all',array('conditions' => $conditions, 
                                                                 'order'      => 'Deliberation.created DESC',
                                                                 'contain'    => array( 'Seance.id','Seance.traitee', 'Seance.date', 'Seance.Typeseance.libelle', 'Service.libelle', 'Theme.libelle', 'Nature.libelle')));

                $this->_ajouterFiltre($projets);
		$this->_afficheProjets(
			$projets,
			'Projets valid&eacute;s associ&eacute;s &agrave; une s&eacute;ance',
			array('view', 'generer'));
	}

    function _ajouterFiltre(&$projets) {
        if (!$this->Filtre->critereExists()){
            $this->Filtre->addCritere('SeanceId', array(
                                          'field' => 'Deliberation.seance_id',
                                          'classeDiv' => 'demi',
                                          'inputOptions' => array(
                                              'label'=>__('Séances', true),
                                              'empty' =>'toutes',
                                              'options' => $this->Utils->listFromArray($projets, 
                                                                                        '/Seance/id', 
                                                                                        array('/Seance/date', 
                                                                                        '/Seance/Typeseance/libelle'), 
                                                                                        '%s : %s'))));
            $this->Filtre->addCritere('Typeseance', array(
                                          'field' => 'Seance.type_id',
                                          'classeDiv'  => 'demi',
                                          'retourLigne' => true,
                                          'inputOptions' => array(
                                              'label'=>__('Type de séance', true),
                                              'options' => $this->Utils->listFromArray($projets, 
                                                                                       '/Seance/type_id', 
                                                                                       array('/Seance/Typeseance/libelle'), 
                                                                                       '%s'))));
            $this->Filtre->addCritere('Nature', array(
                                          'field' => 'Deliberation.nature_id',
                                          'classeDiv' => 'tiers',
                                          'inputOptions' => array(
                                              'label'=>__('Nature', true),
                                              'empty' =>'toutes',
                                              'options' => $this->Utils->listFromArray($projets, 
                                                                                       '/Deliberation/nature_id', 
                                                                                       array('/Nature/libelle'), 
                                                                                       '%s'))));
            $this->Filtre->addCritere('ServiceId', array(
                                          'field' => 'Deliberation.Service_id',
                                          'classeDiv'  => 'tiers',
                                          'inputOptions' => array(
                                              'label'=>__('Service émetteur', true),
                                              'multiple' => true,
                                              'options' => $this->Utils->listFromArray($projets, 
                                                                                       '/Deliberation/service_id', 
                                                                                       array('/Service/libelle'), 
                                                                                       '%s'))));
            $this->Filtre->addCritere('ThemeId', array(
                                          'field' => 'Deliberation.theme_id',
                                          'classeDiv' => 'tiers',
                                          'inputOptions' => array(
                                              'label'=>__('Thème', true),
                                              'options' => $this->Utils->listFromArray($projets, 
                                                                                       '/Deliberation/theme_id', 
                                                                                       array('/Theme/libelle'), 
                                                                                       '%s'))));
        }
    }

/*
 * Attribue une séance à un projet
 * Appelée depuis la vue deliberations/tous_les_projets
 */
	function attribuerSeance () {
		if (isset($this->data['Deliberation']['seance_id']) && !empty($this->data['Deliberation']['seance_id'])) {
			$this->data['Deliberation']['position'] = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);
		    $this->Deliberation->save($this->data);
                    $this->Filtre->supprimer();
		    $this->Session->setFlash('La s&eacute;ance a bien &eacute;t&eacute; attribu&eacute;e', 'growl');
		}
		else
			$this->Session->setFlash('Erreur lors de l`attribution de la s&eacute;ance', 'growl');
		$this->redirect('/deliberations/tousLesProjetsSansSeance');
	}

/*
 * Permet de valider un projet en cours de validation en court-circuitant le circuit de validation
 * Appelée depuis la vue deliberations/tous_les_projets
 */
	function validerEnUrgence($delibId) {
		// Lecture de la délibération
		$this->Deliberation->recursive = -1;
		$this->data = $this->Deliberation->read(null, $delibId);
		if (empty($this->data))
			$this->Session->setFlash('Invalide id pour le projet de d&eacute;lib&eacute;ration', 
                                                 'growl', 
                                                 array('type'=>'erreur'));
		else {
			if ($this->data['Deliberation']['etat']!=1)
				$this->Session->setFlash('Le projet de d&eacute;lib&eacute;ration doit &ecirc;tre en cours d\'&eacute;laboration', 'growl', array('type'=>'erreur'));
			else {
				// initialisation du visa si utilisateur connecté est hors traitement
				$options = array(
					'insertion' => array(
						'0' => array(
							'Etape' => array(
								'etape_nom'=>'Validation en urgence',
								'etape_type'=>1
								),
							'Visa' => array(
								'0'=>array(
									'trigger_id'=>$this->Session->read('user.User.id'),
									'type_validation'=>'V'
									)))));
				$this->Traitement->execute('ST', $this->Session->read('user.User.id'), $delibId, $options);
                                $this->Deliberation->id = $delibId;
				$this->Deliberation->saveField('etat', 2);
			        $this->Historique->enregistre($delibId, $this->Session->read('user.User.id'), 'Projet validé en urgence' );
			}
		}
                 $this->Session->setFlash('Le projet '.$this->data['Deliberation']['id'].' a &eacute;t&eacute; valid&eacute; en urgence', 'growl');
		$this->redirect('/deliberations/tousLesProjetsValidation');
	}

	function mesProjetsRecherche() {
		if (empty($this->data)) {
			$this->set('action', '/deliberations/mesProjetsRecherche/');
			$this->set('titreVue', 'Recherche multi-crit&egrave;res parmi mes projets');

			$this->set('rapporteurs', $this->Acteur->generateListElus());
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateAllList());
			$this->set('services', $this->Deliberation->Service->generatetreelist(null, null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
			$this->set('themes', $this->Deliberation->Theme->find('list',array('order'=>array('libelle asc'),'fields' => array('Theme.id', 'Theme.libelle'))));
			$this->set('circuits', $this->Circuit->find('list',array('order'=>array('nom asc'),'fields' => array('Circuit.id', 'Circuit.nom'))));
			$this->set('etats', $this->Deliberation->generateListEtat());
			$this->set('infosupdefs', $this->Infosupdef->findAll('recherche = 1', 'id, code, nom, commentaire, type, taille', 'ordre', null, 1, -1));
			$this->set('infosuplistedefs', $this->Infosupdef->generateListes());
			$this->set('listeBoolean', $this->Infosupdef->listSelectBoolean);
			$this->set('models', $this->Model->find('list',array('conditions'=>array('type'=>'Document', 'Model.recherche' => 1),
                                                                             'fields' => array('Model.id','Model.modele'))));


			$this->render('rechercheMutliCriteres');
		} else {
			$conditions = "";

			if (!empty($this->data['Deliberation']['rapporteur_id'])){
			        $conditions .= " Deliberation.rapporteur_id = ".$this->data['Deliberation']['rapporteur_id'];
                        }
			if (!empty($this->data['Deliberation']['service_id'])){
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.service_id = ".$this->data['Deliberation']['service_id'];
			}

			if (!empty($this->data['Deliberation']['id'])){
			    if (!is_numeric($this->data['Deliberation']['id'])) {
			        $this->Session->setFlash('Vous devez saisir un identifiant valide', 'growl', array('type'=>'erreur'));
                                $this->redirect('/deliberations/mesProjetsRecherche');
		            }
		            if ($conditions != "")
			        $conditions .= " AND ";
			    $conditions .= " Deliberation.id = ".$this->data['Deliberation']['id'];
			}
                        if (!empty($this->data['Deliberation']['nature_id'])){
                            if ($conditions != "")
                                        $conditions .= " AND ";
                                $conditions .= " Deliberation.nature_id = ".$this->data['Deliberation']['nature_id'];
                        }
			if (!empty($this->data['Deliberation']['theme_id'])){
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.theme_id = ".$this->data['Deliberation']['theme_id'];
			}

                        if (!empty($this->data['Deliberation']['circuit_id'])){
                                if ($conditions != "")
                                        $conditions .= " AND ";
                                $conditions .= " Deliberation.circuit_id = ".$this->data['Deliberation']['circuit_id'];
                        }

			if (!empty($this->data['Deliberation']['texte'])) {
				$texte = $this->data['Deliberation']['texte'];
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " (Deliberation.objet LIKE '%$texte%' OR Deliberation.titre LIKE '%$texte%')";
			}

			$seanced = $this->Seance->read(null, $this->data['Deliberation']['seance1_id']);
			$seance1 =  $seanced['Seance']['date'];
			$seanced2 = $this->Seance->read (null, $this->data['Deliberation']['seance2_id']);
			$seance2 =  $seanced2['Seance']['date'];

			$seances = $this->Seance->findAll("Seance.date BETWEEN '$seance1' AND '$seance2' ");
			$tab_seances = array();
			if (!empty($seances)){
				foreach($seances as $seance)
					array_push ($tab_seances,  $seance['Seance']['id']);
				$values = (implode(', ', $tab_seances));
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.seance_id IN ($values)";
			}

			if ($this->data['Deliberation']['etat'] != '') {
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.etat = " . $this->data['Deliberation']['etat'];
			}

			if (array_key_exists('Infosup', $this->data)) {
				$rechercheInfoSup = $this->Deliberation->Infosup->selectInfosup($this->data['Infosup']);
				if (!empty($rechercheInfoSup))
					$conditions .= (empty($conditions)?'':' AND') . " Deliberation.id IN ($rechercheInfoSup)";
			}

			if (empty($conditions)) {
				$this->Session->setFlash('Vous devez saisir au moins un crit&egrave;re.', 'growl', array('type'=>'erreur'));
				$this->redirect('/deliberations/mesProjetsRecherche');
			} else {
				$userId=$this->Session->read('user.User.id');
				$listeCircuits = $this->Circuit->listeCircuitsParUtilisateur($userId);
				$conditions .= ' AND ';
				$conditions .= empty($listeCircuits) ? '' : '(Deliberation.circuit_id IN ('.$listeCircuits.') OR ';
				$conditions .= 'Deliberation.redacteur_id = ' . $userId;
				$conditions .= empty($listeCircuits) ? '' : ')';
				$ordre = 'Deliberation.created DESC';

				//$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);
                                 $projets = $this->Deliberation->find('all', array ('conditions' => $conditions));
                         
                                if ($this->data['Deliberation']['generer'] == 0 ) {
           			    $this->_afficheProjets( $projets,
				                            'R&eacute;sultat de la recherche parmi mes projets',
					                    array('view', 'generer'),
					                    array('mesProjetsRecherche'));
                                }
                                else {
                                    if(count($projets)>0){
                                        $this->Deliberation->genererRecherche($projets, $this->data['Deliberation']['model']);
                                    }
                                    else {
				        $this->Session->setFlash('Aucun résultat à la recherche effectuée.', 'growl', array('type'=>'erreur'));
				        $this->redirect('/deliberations/mesProjetsRecherche');
                                    }
                                }
			}
		}
	}

	function tousLesProjetsRecherche() {
		if (empty($this->data)) {
			$this->set('action', '/deliberations/tousLesProjetsRecherche/');
			$this->set('titreVue', 'Recherche multi-crit&egrave;res parmi tous les projets');

			$this->set('rapporteurs', $this->Acteur->generateListElus());
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateAllList());
			$this->set('services', $this->Deliberation->Service->generatetreelist(null, null, null, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
			$this->set('themes', $this->Deliberation->Theme->find('list',array('order'=>array('libelle asc'),'fields' => array('Theme.id','Theme.libelle'))));
			$this->set('circuits', $this->Deliberation->Circuit->find('list',array('order'=>array('nom asc'),'fields' => array('Circuit.id', 'Circuit.nom'))));
			$this->set('etats', $this->Deliberation->generateListEtat());
			$this->set('infosupdefs', $this->Infosupdef->findAll('recherche = 1', 'id, code, nom, commentaire, type, taille', 'ordre', null, 1, -1));
			$this->set('infosuplistedefs', $this->Infosupdef->generateListes());
                        $this->set('models', $this->Model->find('list',array('conditions'=>array('type'=>'Document', 'Model.recherche' => 1),
                                                                             'fields' => array('Model.id','Model.modele'))));


			$this->set('listeBoolean', $this->Infosupdef->listSelectBoolean);

			$this->render('rechercheMutliCriteres');
		} else {
			$conditions = "";
			if (!empty($this->data['Deliberation']['rapporteur_id']))
				$conditions .= " Deliberation.rapporteur_id = ".$this->data['Deliberation']['rapporteur_id'];

			if (!empty($this->data['Deliberation']['service_id'])){
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.service_id = ".$this->data['Deliberation']['service_id'];
			}

			if (!empty($this->data['Deliberation']['id'])){
                            if (!is_numeric($this->data['Deliberation']['id'])) {
                                $this->Session->setFlash('Vous devez saisir un identifiant valide', 'growl', array('type'=>'erreur'));
                                $this->redirect('/deliberations/tousLesProjetsRecherche');
                            }
		            if ($conditions != "")
			        $conditions .= " AND ";
			     $conditions .= " Deliberation.id = ".$this->data['Deliberation']['id'];
			}

			if (!empty($this->data['Deliberation']['theme_id'])){
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.theme_id = ".$this->data['Deliberation']['theme_id'];
			}

                        if (!empty($this->data['Deliberation']['circuit_id'])){
                                if ($conditions != "")
                                        $conditions .= " AND ";
                                $conditions .= " Deliberation.circuit_id = ".$this->data['Deliberation']['circuit_id'];
                        }
                        if (!empty($this->data['Deliberation']['nature_id'])){
                            if ($conditions != "")
                                        $conditions .= " AND ";
                                $conditions .= " Deliberation.nature_id = ".$this->data['Deliberation']['nature_id'];
                        }

			if (!empty($this->data['Deliberation']['texte'])) {
				$texte = $this->data['Deliberation']['texte'];
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " (Deliberation.objet LIKE '%$texte%' OR Deliberation.titre LIKE '%$texte%')";
			}

			$seanced = $this->Seance->read(null, $this->data['Deliberation']['seance1_id']);
			$seance1 =  $seanced['Seance']['date'];
			$seanced2 = $this->Seance->read (null, $this->data['Deliberation']['seance2_id']);
			$seance2 =  $seanced2['Seance']['date'];
			$seances = $this->Seance->findAll("Seance.date BETWEEN '$seance1' AND '$seance2'");
			$tab_seances = array();
			if (!empty($seances)){
				foreach($seances as $seance)
					array_push ($tab_seances,  $seance['Seance']['id']);
				$values = (implode(', ', $tab_seances));
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.seance_id IN ($values)";
			}

			if ($this->data['Deliberation']['etat'] != '') {
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.etat = " . $this->data['Deliberation']['etat'];
			}

			if (array_key_exists('Infosup', $this->data)) {
				$rechercheInfoSup = $this->Deliberation->Infosup->selectInfosup($this->data['Infosup']);
				if (!empty($rechercheInfoSup))
					$conditions .= (empty($conditions)?'':' AND') . " Deliberation.id IN ($rechercheInfoSup)";
			}

			if (empty($conditions)) {
				$this->Session->setFlash('Vous devez saisir au moins un crit&egrave;re.', 'growl', array('type'=>'erreur'));
				$this->redirect('/deliberations/tousLesProjetsRecherche');
			} else {
				// lecture en base
                                $projets = $this->Deliberation->find('all', array ('conditions' => $conditions,
                                                                                   'oder'       => 'Deliberation.seance_id'));
                                if ($this->data['Deliberation']['generer'] == 0) {
                                    $this->_afficheProjets( $projets,
                                                            'R&eacute;sultat de la recherche parmi mes projets',
                                                            array('view', 'generer'),
                                                            array('mesProjetsRecherche'));
                                }
                                else {
                                    $this->Deliberation->genererRecherche($projets, $this->data['Deliberation']['model']);
                                }

			}
		}
	}

/*
 * retourne un tableau array('image'=>, 'titre'=>) pour l'affichage de l'icône dans les listes en fonction de :
 *  $etat : état du projet ou de la délibération
 *  $editerProjetValide : droit d'éditer les projets validés
 *
 */
 	function _iconeEtat($etat, $editerProjetValide=false, $estDansCircuit = false, $estRedacteur = false, $tourDansCircuit = 0) {
 		switch($etat) {
		case -1 : // refusé
			return array(
				'image' => '/img/icons/refuse.png',
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
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : trait&eacute');
				elseif ($tourDansCircuit == 0)
					return array(
						'image' => '/img/icons/atraiter.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : &agrave; traiter');
				else
					return array(
						'image' => '/img/icons/attente.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : en attente');
			} else {
				if ($estRedacteur)
					return array(
						'image' => '/img/icons/fini.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : projet dont je suis le r&eacute;dacteur');
				else
					return array(
						'image' => '/img/icons/fini.png',
						'titre' => $this->Deliberation->libelleEtat($etat));
			}
			break;
		case 2: // validé
			if ($editerProjetValide)
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

	function sendToParapheur($seance_id) {
                $erreur = false;
                $this->set('seance_id', $seance_id);
                $this->Parafwebservice = new IparapheurComponent();
		$circuits = $this->Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
                foreach ($circuits['soustype'] as &$libelle) 
                    $libelle = 'IParapheur : '.$libelle; 
                 
                $circuits['soustype']['-1'] = 'Webdelib : Signature manuscrite';
                ksort($circuits['soustype']);
		if (empty($this->data)) {
			$delibs = $this->Deliberation->find('all',array('conditions'=>array("Deliberation.seance_id"=>$seance_id ,
                                                                                            "Deliberation.etat <>"   =>-1),
                                                                        'order'      => 'Deliberation.position'));
                        for ($i=0; $i<count($delibs); $i++){
                            $delibs[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($delibs[$i]['Seance']['type_id'], $delibs[$i]['Deliberation']['etat']);
                        }
			$this->set('deliberations', $delibs);
			$this->set('circuits', $circuits['soustype']);
		}
		else {
			if ($this->data['Deliberation']['circuit_id'] == '') {
				$this->Session->setFlash("Vous devez saisir un circuit avant l'envoi.", 'growl', array('type'=>'erreur'));
				$this->redirect('/deliberations/sendToParapheur');
				exit;
			}
			foreach ($this->data['Deliberation'] as $id => $bool ) {
				if ($bool == 1) {
					$delib_id = substr($id, 3, strlen($id));
                                        if ($this->data['Deliberation']['circuit_id'] == -1) {
                                            $this->Deliberation->id =   $delib_id ;
                                            $this->Deliberation->saveField('signee', true); 
                                            continue;
                                        }
					$delib = $this->Deliberation->find('first', array('conditions'=>array('Deliberation.id' => $delib_id)));
					$soustype = $circuits['soustype'][$this->data['Deliberation']['circuit_id']];
					$nomfichierpdf = "D_$id.pdf";
					$objetDossier = utf8_encode($this->_objetParaph("$delib_id ".$delib['Deliberation']['objet']));
					$annexes = array();
					$tmp1=0;
					foreach ($delib['Annex'] as $annex) {
						if ($annex['type'] == 'G')
						$annexes[$tmp1][3] = $annex['filename'];
						$annexes[$tmp1][2] = 'UTF-8';
						$annexes[$tmp1][1] = $annex['filetype'];
						$annexes[$tmp1][0] = $annex['data'];
						$tmp1++;
					}
                                        $model_id = $this->Typeseance->modeleProjetDelibParTypeSeanceId($delib['Seance']['type_id'], $delib['Deliberation']['etat']);
                                        $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/rapport.pdf/1/false");
                                        $content = file_get_contents(WEBROOT_PATH."/files/generee/fd/null/$delib_id/rapport.pdf");
 
					$creerdos = $this->Parafwebservice->creerDossierWebservice(Configure::read('TYPETECH'), 
                                                                                                   $soustype, 
                                                                                                   Configure::read('EMAILEMETTEUR'), 
                                                                                                   $objetDossier, 
                                                                                                   '', 
                                                                                                   '', 
                                                                                                   Configure::read('VISIBILITY'), 
                                                                                                   '', 
                                                                                                   $content, 
                                                                                                   $annexes);
                                        
					$delib['Deliberation']['etat_parapheur']= 1;
                                        if ($creerdos['messageretour']['coderetour']== 'OK') {
                                            $this->Deliberation->id = $delib_id;
                                            $this->Deliberation->saveField('etat_parapheur',  1);
                                        }
                                        else {
                                            $erreur = true;
                                            $message = $creerdos['messageretour']['message'];
                                        }
				}
			}
                        if ($erreur)
                            $this->Session->setFlash(utf8_decode($message), 'growl',  array('type'=>'erreur'));
                        else {
			    $this->Session->setFlash( "Les documents ont &eacute;t&eacute; envoy&eacute;s au parapheur &eacute;lectronique.", 'growl');
                        }
			$this->redirect('/deliberations/sendToParapheur/'.$seance_id);
			exit;
		}
	}

    function _objetParaph($objet){
        return str_replace("&", "&amp;", $objet);
    }

    function verserAsalae() {
        require_once(APP_DIR.'/vendors/pcltar/pcltar.lib.php');
        if (empty($this->data)) {
            $delibs = $this->Deliberation->find('all', array('conditions'=> array('Deliberation.etat' => 5)));
            $this->set ('deliberations', $delibs);
        }
        else {
            $client = new SoapClient(ASALAE_WSDL);

            foreach ($this->data['Deliberation'] as $id => $bool ){
                if ($bool == 1){
                    $delib_id = substr($id, 3, strlen($id));
                    $delib = $this->Deliberation->read(null, $delib_id);
                    $path = WEBROOT_PATH."/files/generee/delibs/$delib_id/";
                    $pathDelib = $this->Gedooo->createFile($path, "delib.pdf", $delib['Deliberation']['delib_pdf']);
                    // Création de l'archive
                    @PclTarCreate($path."versement.tgz");

                    // Ajout du fichier de délibération
                    @PclTarAddList($path."versement.tgz", $path."delib.pdf", '.', $path) ;
                    $Docs =  array('Attachment' =>
                                        array('@attributes'=>
                                              array('format'=>'fmt/18',
                                                    'mimeCode'=>'application/pdf',
                                                    'filename'=>'delib.pdf'),
                                                     '@value'=>''
                                                   ),
                                             'Description'=>'Acte',
                                             'Type'  => array(
                                '@attributes' => array(
                                'listVersionID' => 'edition 2009'),
                                '@value' => 'CDO')
                                      ) ;

             if ( $delib['Deliberation']['tdt_id'] != null) {
                        $AR = $this->getAR($delib['Deliberation']['tdt_id'], true);
                        $path_AR =  $this->Gedooo->createFile($path, "bordereau.pdf",$AR, '.', $path);
                        // Ajout du fichier de bordereau
                        @PclTarAddList($path."versement.tgz", $path."bordereau.pdf", '.', $path) ;
                        array_push ($Docs,  array('Attachment' =>
                                        array('@attributes'=>
                                               array('format'=>'fmt/18',
                                                     'mimeCode'=>'application/pdf',
                                                     'filename'=>'bordereau.pdf'),
                                                     '@value'=>''
                                                    ),
                                              'Description'=>'Bordereau',
                                              'Type'  => array(
                                '@attributes' => array('listVersionID' => 'edition 2009'),
                                '@value' => 'CDO')
                                        )
                                    );

                    }
                    $document  = file_get_contents($path."versement.tgz");

                    $options = array(
                                     'TransferIdentifier' => IDENTIFIANT_VERSANT.'_'.$delib['Deliberation']['num_delib'],
                                     'Comment'            =>  utf8_encode($delib['Deliberation']['objet']),
                                     'Date'               => date('c'),
                                     'TransferringAgency' => array('Identification'=>IDENTIFIANT_VERSANT),
                                     'ArchivalAgency'     => array('Identification'=>SIREN_ARCHIVE),
                                     'Contains'           => array(
                                                                    'ArchivalAgreement'    => NUMERO_AGREMENT,
                                                                    'DescriptionLanguage' =>  array(
                                                        '@attributes' => array('listVersionID' => 'edition 2009'),
                                                        '@value' => 'fr'),
                                                                    'DescriptionLevel'     => array(
                                                        '@attributes' => array('listVersionID' => 'edition 2009'),
                                                        '@value' => 'file'),
                                                                    'Name'=> utf8_encode('Déliberation envoyee depuis WebDelib'),

                                                                    'ContentDescription' => array(
                                                                        'CustodialHistory' => utf8_encode("Délibération en provenance de Webdelib"),
                                                                        'Description' => utf8_encode($delib['Deliberation']['objet']),
                                                                        'Language'             =>  array(
                                                                                      '@attributes' => array('listVersionID' => 'edition 2009'),
                                                                                      '@value' => 'fr'),
                                                          'OriginatingAgency' => array('Identification'=>IDENTIFIANT_VERSANT),
                                                                        'ContentDescriptive' => array('KeywordContent' =>'Deliberation',
                                                                                                      'KeywordReference' =>'1',
                                                                                                      'KeywordType' =>array(
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

                    $seda = $client->__soapCall("wsGSeda", array($options, IDENTIFIANT_VERSANT, MOT_DE_PASSE));
                    $ret  = $client->__soapCall("wsDepot", array("bordereau.xml", base64_encode($seda), "versement.tgz", base64_encode($document), 'TARGZ', IDENTIFIANT_VERSANT, MOT_DE_PASSE, ));
                   // Changement d'état de la délibération
                    if ($ret == 0){
                        $this->Deliberation->id = $delib_id; 
                        $this->Deliberation->saveField('etat_asalae', 1);
                    }
                }
            }
            $this->Session->setFlash( "Les documents ont été transférés à AS@LAE", 'growl');
            $this->redirect('/deliberations/verserAsalae');
            exit;

        }
    }

    function goNext($delib_id) {
		$delib = $this->Deliberation->read(null, $delib_id);
		if (empty($delib))
			$this->redirect($this->referer());

		if (empty($this->data)) {
			$etapes = $this->Traitement->listeEtapes($delib['Deliberation']['id'], array('selection'=>'APRES'));
			if (empty($etapes))
				$this->redirect($this->referer());
			$this->set('delib_id', $delib_id);
			$this->set('etapes', $etapes);
		} else {
			$insertion = array(
					'0' => array(
						'Etape' => array(
							'etape_nom'=>'Aller à une étape suivante',
							'etape_type'=>1
							),
						'Visa' => array(
							'0'=>array(
								'trigger_id'=>$this->Session->read('user.User.id'),
								'type_validation'=>'V'
								))));
			$this->Traitement->execute('JS', $this->Session->read('user.User.id'), $delib_id, array('insertion'=> $insertion, 'numero_traitement'=>$this->data['Traitement']['etape']));
                        $destinataires = $this->Traitement->whoIsNext($delib_id);
                        foreach($destinataires as $destinataire_id)
                            $this->_notifier($delib_id, $destinataire_id, 'traiter');

			$this->Historique->enregistre($delib_id, $this->Session->read('user.User.id'), "Le projet a sauté l'étape  ");
			$this->Session->setFlash("Le projet est maintenant à l'étape suivante ", 'growl');
			$this->redirect('/deliberations/tousLesProjetsValidation');
		}


    }
   
    function rebond($delib_id) {
        $this->set('delib_id', $delib_id);
	if (empty($this->data)) {
            $this->data['Insert']['retour'] = true;
	    $this->set('users', $this->User->listFields(array('order'=>'User.nom')));
	    $this->set('typeEtape', $this->Traitement->typeEtape($delib_id));
        } else {
            $user_connecte = $this->Session->read('user.User.id');
            
            $user = $this->User->read(null, $this->data['Insert']['user_id']);
            $destinataire = $user['User']['prenom'].' '.$user['User']['nom'].' ('.$user['User']['login'].')';
            $this->Historique->enregistre($delib_id, $user_connecte, "Le projet a  été envoyé à $destinataire");

			// initialisation des visas a ajouter au traitement
			$options = array(
				'insertion' => array(
					'0' => array(
						'Etape' => array(
							'etape_nom'=>$user['User']['prenom'].' '.$user['User']['nom'],
							'etape_type'=>1
							),
						'Visa' => array(
							'0'=>array(
								'trigger_id'=>$this->data['Insert']['user_id'],
								'type_validation'=>'V'
								)))));
			$action = $this->data['Insert']['retour'] ? 'IL': 'IP';
                        $this->_notifier($delib_id, $this->data['Insert']['user_id'], 'traiter'); 
			$this->Traitement->execute($action, $user_connecte, $delib_id, $options);
			$this->redirect('/');
        }
    }

    function sendToGed($delib_id) {  
        $delib = $this->Deliberation->find( 'first', array(
                                            'conditions' => array('Deliberation.id' => $delib_id)));
        $cmis = new CmisComponent();
        // Création du répertoire
        $my_new_folder = $cmis->client->createFolder($cmis->folder->id, 
                                                     $delib_id);

        // Dépôt de la délibération et du rapport dans le répertoire que l'on vient de créer
        $obj_delib = $cmis->client->createDocument($my_new_folder->id, 
                                                   "deliberation.pdf", 
                                                   array (), 
                                                   $delib['Deliberation']['delib_pdf'], 
                                                   "application/pdf");

        // Dépôt du rapport de projet (on fixe l'etat à 2 pour etre sur d'avoir le rapport et non la délibération
        if (isset($delib['Seance']['date']))
            $model_id = $this->Typeseance->modeleProjetDelibParTypeSeanceId($delib['Seance']['type_id'], '2');
        else
            $model_id = 1;

//        $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/rapport.pdf/1/false");
//        $rapport = file_get_contents(WEBROOT_PATH."/files/generee/fd/null/$delib_id/rapport.pdf");
//        $obj_rapport = $cmis->client->createDocument($my_new_folder->id, 
//                                                     "rapport.pdf", 
//                                                     array (), 
//                                                     $rapport, 
//                                                     "application/pdf");

        if (count($delib['Annex'])> 0) {
            $annex_folder = $cmis->client->createFolder($my_new_folder->id, 'Annexes');
            foreach ($delib['Annex'] as $annex) {
                $obj_annexe = $cmis->client->createDocument($annex_folder->id,
                                                            $annex['filename'],
                                                            array (),
                                                            $annex['data'],
                                                            $annex['filetype']);
            }
        }


    }

/**
 * Supprime un répertoire et son contenu
 * @param string $dossier chemin du répertoire à supprimer
 */
 function _rmDir($dossier) {
	$ouverture=@opendir($dossier);
	if (!$ouverture) return;
	while($fichier=readdir($ouverture)) {
		if ($fichier == '.' || $fichier == '..') continue;
		if (is_dir($dossier."/".$fichier)) {
			$r = $this->_rmDir($dossier."/".$fichier);
			if (!$r) return false;
		} else {
			$r=@unlink($dossier."/".$fichier);
			if (!$r) return false;
		}
	}
	closedir($ouverture);
	$r=@rmdir($dossier);
	if (!$r) return false;
	return true;
}

}
?>
