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
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $uses = array('Acteur', 'Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Seance', 'TypeSeance', 'Commentaire','Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Infosupdef');
	var $components = array('Gedooo','Date','Utils','Email','Acl', 'Parafwebservice');

	// Gestion des droits
	var $demandeDroit = array(
		'add',
		'mesProjetsRedaction',
		'mesProjetsValidation',
		'mesProjetsValides',
		'mesProjetsATraiter',
		'mesProjetsRecherche',
		'tousLesProjetsSansSeance',
		'tousLesProjetsValidation',
		'tousLesProjetsAFaireVoter',
		'tousLesProjetsRecherche',
		'editerProjetValide'
	);
	var $commeDroit = array(
		'view'=>array('Pages:mes_projets', 'Pages:tous_les_projets', 'downloadDelib'),
		'edit'=>array('Deliberations:add', 'Deliberations:mesProjetsRedaction', 'Deliberations:editerProjetValide'),
		'delete'=>'Deliberations:mesProjetsRedaction',
		'attribuercircuit'=>'Deliberations:mesProjetsRedaction',
		'addIntoCircuit'=>'Deliberations:mesProjetsRedaction',
		'traiter'=>'Deliberations:mesProjetsATraiter',
		'attribuerSeance'=>'Deliberations:tousLesProjetsSansSeance',
		'validerEnUrgence'=>'Deliberations:tousLesProjetsValidation'
	);
	var $libelleControleurDroit = 'Projets';
	var $libellesActionsDroit = array('editerProjetValide' => 'Editer projets valid&eacute;s');


	function view($id = null) {
		$this->data = $this->Deliberation->findById($id);
		if (empty($this->data)) {
			$this->Session->setFlash('Invalide id pour la d&eacute;lib&eacute;ration : affichage de la vue impossible.');
			$this->redirect('/deliberations/mesProjetsRedaction');
		}

		// Compactage des informations supplémentaires
		$this->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup']);

		// Lecture des versions anterieures
		$listeAnterieure=array();
		$tab_anterieure=$this->_chercherVersionAnterieure($id, $this->data, 0, $listeAnterieure, 'view');
		$this->set('tab_anterieure',$tab_anterieure);

		// Lecture des droits en modification
		$user_id = $this->Session->read('user.User.id');
		if ($this->Droits->check($user_id, "Deliberations:add") &&
			$this->Deliberation->estModifiable($id, $user_id)
		)
			$this->set('userCanEdit', true);
		else
			$this->set('userCanEdit', false);

		// Lecture et initialisation des commentaires
		$commentaires = $this->Commentaire->findAll("delib_id =  $id");
		for($i=0; $i< count($commentaires) ; $i++) {
		        if($commentaires[$i]['Commentaire']['agent_id'] == -1) {
			    $nomAgent = 'i-parapheur';
			    $prenomAgent = '';
			}  
			else {
			    $nomAgent = $this->requestAction("users/getNom/".$commentaires[$i]['Commentaire']['agent_id']);
			    $prenomAgent = $this->requestAction("users/getPrenom/".$commentaires[$i]['Commentaire']['agent_id']);
			}
			$commentaires[$i]['Commentaire']['nomAgent'] = $nomAgent;
			$commentaires[$i]['Commentaire']['prenomAgent'] = $prenomAgent;
		}
		$this->set('commentaires',$commentaires);

		// Mise en forme des données du projet ou de la délibération
		$this->data['Deliberation']['libelleEtat'] = $this->Deliberation->libelleEtat($this->data['Deliberation']['etat']);
		if(!empty($this->data['Seance']['date']))
			$this->data['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data['Seance']['date']));

		$id_service = $this->data['Service']['id'];
		$this->data['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);

		$tab_circuit=$this->data['Deliberation']['circuit_id'];
		$delib=array();
		//on recupere la position courante de la deliberation
		$lastTraitement=array_pop($this->data['Traitement']);
		$this->data['positionDelib']=$lastTraitement['position'];
		//on recupere la position de l'user dans le circuit
		$userscircuit = $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit", null, 'UsersCircuit.position ASC');
		if (USE_PARAPH)
	            $soustypes    = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
		for ($i = 0; $i <count ($userscircuit); $i++){
                    if($userscircuit[$i]['UsersCircuit']['service_id']== -1){
		        $userscircuit[$i]['User']['prenom']= TYPETECH;
		        $userscircuit[$i]['User']['nom']= $soustypes['soustype'][$userscircuit[$i]['UsersCircuit']['user_id']];
		        $userscircuit[$i]['Service']['libelle']= 'i-parapheur';
                    }
		}
		$this->set('user_circuit', $userscircuit);
		// Définitions des infosup
		$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));

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

		if (!empty($this->data)) {
			$this->data['Deliberation']['redacteur_id'] = $user['User']['id'];
			$this->data['Deliberation']['service_id'] = $user['User']['service'];

			if (($this->data['Deliberation']['seance_id'])!=null )
				$this->data['Deliberation']['position'] = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);

			$this->data['Deliberation']['date_limite']= $this->Utils->FrDateToUkDate($this->params['form']['date_limite']);

			if (!GENERER_DOC_SIMPLE){
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
			$this->cleanUpFields();

			if ($this->Deliberation->save($this->data)) {
				$delibId = $this->Deliberation->getLastInsertId();
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

				$this->Session->setFlash('Le projet \''.$delibId.'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect($redirect);
		else {
			$this->data['Service']['libelle'] = $this->Deliberation->Service->doList($user['User']['service']);
			$this->data['Redacteur']['nom'] = $this->Deliberation->User->field('nom', 'User.id = ' . $user['User']['id']);
			$this->data['Redacteur']['prenom'] = $this->Deliberation->User->field('prenom', 'User.id = ' . $user['User']['id']);

			$this->set('themes', $this->Deliberation->Theme->generateList('Theme.actif=1','libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus('nom'));
			$this->set('selectedRapporteur', $this->Deliberation->Acteur->selectActeurEluIdParDelegationId($user['User']['service']));
			$this->set('date_seances',$this->Seance->generateList());
			$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
			$this->set('redirect', $redirect);

			/* valeurs initiales des info supplémentaires */
			$this->data['Infosup'] = $this->Infosupdef->valeursInitiales();

			$this->render('edit');
		}
	}

	/* Supprime les projets de délibération de l'utilisateur connecté pour lesquels le titre et l'bjet sont vides */
	function _checkEmptyDelib () {
	    $userId = $this->Session->read('user.User.id');
		$conditions = "Deliberation.objet = '' AND Deliberation.titre = '' AND Deliberation.redacteur_id = ".$userId;
		$delibs_vides = $this->Deliberation->findAll($conditions);
		foreach ($delibs_vides as $delib)
			$this->Deliberation->del($delib['Deliberation']['id']);
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

        function downloadDelib($delib_id) {
            $delib = $this->Deliberation->read(null, $delib_id);
            header('Content-type: application/pdf');
            header('Content-Length: '.strlen($delib['Deliberation']['delib_pdf']));
            header('Content-Disposition: attachment; filename='.$delib['Deliberation']['num_delib'].'.pdf');
            echo $delib['Deliberation']['delib_pdf'];
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
		$delibs = $this->Deliberation->findAll($conditions);
		foreach ($delibs as $delib) {
			// on enleve pour 1 la delib qui a change de seance..
			$delib['Deliberation']['position']= $delib['Deliberation']['position'] -1;
			$this->Deliberation->save($delib['Deliberation']);
		}
	}

        function edit($id=null) {
	         $user=$this->Session->read('user');
	        /* initialisation du lien de redirection */
		if ($this->Acl->check($user['User']['id'], "Pages:mes_projets"))
			$redirect = '/pages/mes_projets';
		elseif ($this->Acl->check($user['User']['id'], "Pages:tous_les_projets"))
			$redirect = '/pages/tous_les_projets';
		else
			$redirect = '/';
                if ($this->Acl->check($user['User']['id'], "Deliberations:editerProjetValide"))
	           $afficherTtesLesSeances = true;
                else
	           $afficherTtesLesSeances = null;

                $pos  =  strrpos ( getcwd(), 'webroot');
		$path = substr(getcwd(), 0, $pos);
                $path_projet = $path."webroot/files/generee/projet/$id/";

		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);

			/* teste si le projet est modifiable par l'utilisateur connecté */
			if (!$this->Deliberation->estModifiable($id, $user['User']['id']) &&
				!($this->data['Deliberation']['etat'] == 2 && $this->Acl->check($user['User']['id'], "Deliberations:editerProjetValide"))
			) {
				$this->Session->setFlash("Vous ne pouvez pas editer le projet '$id'.");
				$this->redirect($redirect);
			}

			if (!GENERER_DOC_SIMPLE) {
			    $this->Gedooo->createFile($path_projet, 'texte_projet.odt',  $this->data['Deliberation']['texte_projet']);
			    $this->Gedooo->createFile($path_projet, 'texte_synthese.odt', $this->data['Deliberation']['texte_synthese']);
			    $this->Gedooo->createFile($path_projet, 'deliberation.odt',  $this->data['Deliberation']['deliberation']);
			    foreach ($this->data['Infosup']  as $infosup)
                                if(($infosup['file_name']!="") && (!empty( $infosup['content'])))    
                                    $this->Gedooo->createFile($path_projet, 'infosup'.$infosup['infosupdef_id'] .'.odt', $infosup['content']);
			}
			$this->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup']);
			$this->data['Deliberation']['date_limite'] = date("d/m/Y",(strtotime($this->data['Deliberation']['date_limite'])));
			$this->data['Service']['libelle'] = $this->Deliberation->Service->doList($this->data['Service']['id']);

			$this->set('themes', $this->Deliberation->Theme->generateList('Theme.actif=1','libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus('nom'));
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateList(null ,$afficherTtesLesSeances ));
			$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
			$this->set('redirect', $redirect);
			$this->render();

		} else {
			$oldDelib =  $this->Deliberation->find('Deliberation.id = '.$id, 'seance_id, position', '');
			// Si on definit une seance a une delib, on la position en derniere position de la seance...
			if (!($this->data['Deliberation']['seance_id'] === $oldDelib['Deliberation']['seance_id'])) {
				if ($this->data['Deliberation']['seance_id'])
					$this->data['Deliberation']['position'] = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);
				else
					$this->data['Deliberation']['position'] = 0;
			}

			$this->data['Deliberation']['date_limite']= $this->Utils->FrDateToUkDate($this->params['form']['date_limite']);

			if (!GENERER_DOC_SIMPLE) {
                            if (array_key_exists('texte_projet', $this->data['Deliberation'])) {
				$this->data['Deliberation']['texte_projet_name'] = $this->data['Deliberation']['texte_projet']['name'];
				$this->data['Deliberation']['texte_projet_size'] = $this->data['Deliberation']['texte_projet']['size'];
				$this->data['Deliberation']['texte_projet_type'] = $this->data['Deliberation']['texte_projet']['type'] ;
				if (empty($this->data['Deliberation']['texte_projet']['tmp_name'])) {
		                    $this->data['Deliberation']['texte_projet'] = '';
                                }
				else {
				    $tp = $this->_getFileData($this->data['Deliberation']['texte_projet']['tmp_name'], $this->data['Deliberation']['texte_projet']['size']);
				    $this->data['Deliberation']['texte_projet'] = $tp;
				}
			    }
			    else {
                                $stat = stat($path_projet.'texte_projet.odt');
			        $td = $this->_getFileData($path_projet.'texte_projet.odt', $stat['size'] );
			        $this->data['Deliberation']['texte_projet'] = $td;
			    }
			    // Initialisation de la note de synth¿se
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
                            else {
                                $stat = stat($path_projet.'texte_synthese.odt');
                                $ts = $this->_getFileData($path_projet.'texte_synthese.odt', $stat['size'] );
                                $this->data['Deliberation']['texte_synthese'] = $ts;
                            }

			    // Initialisation du texte de d¿lib¿ration
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
                            else {
                                $stat = stat($path_projet.'deliberation.odt');
                                $ts = $this->_getFileData($path_projet.'deliberation.odt', $stat['size'] );
                                $this->data['Deliberation']['deliberation'] = $ts;
                            }
			}
			$this->cleanUpFields();

			if ($this->Deliberation->save($this->data)) {

				// Si on change une delib de seance, il faut reclasser toutes les delibs de l'ancienne seance...
				if (!empty($oldDelib['Deliberation']['seance_id']) AND ($oldDelib['Deliberation']['seance_id'] != $this->data['Deliberation']['seance_id']))
					$this->_PositionneDelibsSeance($oldDelib['Deliberation']['seance_id'], $oldDelib['Deliberation']['position'] );
				/* sauvegarde des informations supplémentaires */
			        $infossups = $this->Infosupdef->findAll("type='file'", '', '', 0);
                                foreach ( $infossups  as $infosup) {
				             $name = 'infosup'.$infosup['Infosupdef']['id'] .'.odt' ;
				             if (file_exists($path_projet.$name)){
				                 $code = $infosup['Infosupdef']['code'];
				                 $stat = stat($path_projet.$name);
				                 if ($stat > 0) {
				                     $content = $this->_getFileData($path_projet.$name, $stat['size'] );
                                                     $this->data['Infosup'][$code] = $content  ;
				                 }
				             }
				}
				if (array_key_exists('Infosup', $this->data))
				     $this->Deliberation->Infosup->saveCompacted($this->data['Infosup'], $this->data['Deliberation']['id']);
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

				$this->redirect($redirect);
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				$this->set('services', $this->Deliberation->Service->generateList('Service.actif=1'));
				$this->set('themes', $this->Deliberation->Theme->generateList('Theme.actif=1'));
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('datelim',$this->data['Deliberation']['date_limite']);
				$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id));
				$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus('nom'));
				$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
				$this->set('redirect', $redirect);

				$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
				$seances = $this->Seance->findAll($condition);
				foreach ($seances as $seance){
					$retard=$seance['Typeseance']['retard'];
					if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y"))))
						$tab[$seance['Seance']['id']]=$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
				}
				$this->set('date_seances',$tab);
				$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
			}
		}
	}

	function recapitulatif($id = null) {
		$user=$this->Session->read('user');
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la deliberation');
				$this->redirect('/deliberations/mesProjetsRedaction');
			}
			$deliberation = $this->Deliberation->read(null, $id);
			if(!empty($deliberation['Seance']['date']))
				$deliberation['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Seance']['date']));
			if(!empty($deliberation['Deliberation']['date_limite']))
				$deliberation['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($deliberation['Deliberation']['date_limite']));
			$deliberation['Deliberation']['created'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Deliberation']['created']));
			$deliberation['Deliberation']['modified'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Deliberation']['modified']));
			$id_service = $deliberation['Service']['id'];
			$deliberation['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);

			$tab_circuit=$deliberation['Deliberation']['circuit_id'];
			$delib=array();
			//on recupere la position courante de la deliberation
			$lastTraitement=array_pop($deliberation['Traitement']);
			$deliberation['positionDelib']=$lastTraitement['position'];
			//on recupere la position de l'user dans le circuit
			array_push($delib, $deliberation);
			$this->set('deliberation', $delib);
                        $userscircuit = $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit", null, 'UsersCircuit.position ASC');
                        if (USE_PARAPH)
                             $soustypes = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
                        for ($i = 0; $i <count ($userscircuit); $i++){
                            if($userscircuit[$i]['UsersCircuit']['service_id']== -1){
                                $userscircuit[$i]['User']['prenom']= TYPETECH;
                                $userscircuit[$i]['User']['nom']= $soustypes['soustype'][$userscircuit[$i]['UsersCircuit']['user_id']];
                                $userscircuit[$i]['Service']['libelle']= 'i-parapheur';
                            }
                        }
			$this->set('user_circuit', $userscircuit);

		}
	}

	function delete($id = null) {
		$delib = $this->Deliberation->read(null, $id);
		if (empty($delib)) {
			$this->Session->setFlash('Invalide id pour le projet de deliberation : suppression impossible');
		} else if ($this->Deliberation->del($id)) {
			// Il faut reclasser toutes les delibs de la seance
			if (!empty($delib['Deliberation']['seance_id']))
				$this->_PositionneDelibsSeance($delib['Deliberation']['seance_id'], $delib['Deliberation']['position'] );

			$this->Session->setFlash('Le projet \''.$id.'\' a &eacute;t&eacute; supprim&eacute;.');
		}
		$this->redirect('/deliberations/mesProjetsRedaction');
	}

   function convert($id=null) {
            vendor('fpdf/html2fpdf');
	    $pdf = new HTML2FPDF();
	    $pdf->AddPage();
	    $pdf->WriteHTML($this->requestAction("/models/generateProjet/$id"));
	    $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);
	    $projet_path = $path."webroot/files/delibs/PROJET_$id.pdf";
	    $pdf->Output($projet_path ,'F');
	    $pdf->Output("projet_$id.pdf",'D');
    }

    function addIntoCircuit($id = null){
        $this->data = $this->Deliberation->read(null,$id);
    	if ($this->data['Deliberation']['circuit_id']!= 0){
            $this->data['Deliberation']['id'] = $id;
	    $this->data['Deliberation']['date_envoi']=date('Y-m-d H:i:s', time());
            $this->data['Deliberation']['etat']='1';
	    if ($this->Deliberation->save($this->data)) {
	        //on doit tester si la delib a une version anterieure, si c le cas il faut mettre a jour l'action dans la table traitement
		$delib=$this->Deliberation->find("Deliberation.id = $id");
		if ($delib['Deliberation']['anterieure_id']!=0) {
		    //il existe une version anterieure de la delib
		    //on met a jour le traitement anterieure
	  	    $anterieure=$delib['Deliberation']['anterieure_id'];
	 	    $condition="delib_id = $anterieure AND Traitement.position = '0'";
		    $traite=$this->Traitement->find($condition);
		    $traite['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
		    $this->Traitement->save($traite);
		}
		//enregistrement dans la table traitements
		// TODO Voir comment ameliorer ce point (associations cakephp).
		$circuit_id = $delib['Deliberation']['circuit_id'];
		$this->data['Traitement']['id']='';
		$this->data['Traitement']['delib_id']=$id;
		$this->data['Traitement']['circuit_id']=$circuit_id;
		$this->data['Traitement']['position']='1';
		$this->Traitement->save($this->data['Traitement']);

		//Envoi un mail a tous les membres du circuit
		$condition = "circuit_id = $circuit_id";
		$listeUsers = $this->UsersCircuit->findAll($condition);
		foreach($listeUsers as $user){
		    if ($user['UsersCircuit']['service_id']!= -1)
		        $this->_notifierInsertionCircuit($id, $user['User']['id']);
	            else{
                        $model_id = $this->_getModelId($id);
                        $err = $this->requestAction("/models/generer/$id/null/$model_id/0/1/P_$id.pdf");
                        $file =  WEBROOT_PATH."/files/generee/fd/null/$id/P_$id.pdf";

                        $soustypes = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
                        $soustype = $soustypes ['soustype'][$user['UsersCircuit']['user_id']];
                        $emailemetteur = "htexier@cogitis.fr";
                        $nomfichierpdf = "P_$id.pdf";
                        $pdf = file_get_contents($file);
                        $creerdos = $this->Parafwebservice->creerDossierWebservice(TYPETECH, $soustype, $emailemetteur, PREFIX_WEBDELIB.$id, '', '', VISIBILITY, '', $pdf);
		    }

                }


                $this->redirect('/deliberations/mesProjetsRedaction');
	    } else
		$this->Session->setFlash('Probleme de sauvegarde.');
    	}else{
    		$this->Session->setFlash('Vous devez assigner un circuit a la deliberation	.');
    		$this->redirect('/deliberations/recapitulatif/'.$id);
    	}
    }

	function _changeCircuit ($delib_id, $circuit_id) {
	    $traitements = $this->Traitement->findAll("delib_id =$delib_id ");
	    foreach($traitements as $traitement ){
	        $this->Traitement->delete($traitement['Traitement']['id']);
	    }
    }

	function attribuercircuit ($id = null, $circuit_id=null) {
            if (USE_PARAPH)
                $listCircuitsParaph = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);

            if (empty($this->data)) {
                $this->data = $this->Deliberation->read(null, $id);
                $this->set('lastPosition', '-1');
		$circuits=$this->Deliberation->Circuit->generateList(null, "libelle ASC");
                $old_circuit  = $this->data['Deliberation']['circuit_id'];

			//circuit par défaut de l'utilisateur connecté
			if($circuit_id == null)
				$circuit_id = $this->User->circuitDefaut($this->Session->read('user.User.id'), 'id');

			//affichage du circuit existant
			if($circuit_id == null)
				$circuit_id=$this->data['Deliberation']['circuit_id'];
			if (isset($circuit_id)){
			    $this->set('circuit_id', $circuit_id);
                             $listeUserCircuit = $this->UsersCircuit->afficheListeCircuit($circuit_id, $listCircuitsParaph);

  				$this->set('listeUserCircuit', $listeUserCircuit);
  			}else
				$this->set('circuit_id','0');

			$this->set('circuits', $circuits);
		} else {
			$this->data['Deliberation']['id']=$id;
			$old = $this->Deliberation->findAll("Deliberation.id=$id");

			if($old['0']['Deliberation']['circuit_id'] != $circuit_id )
				$this->_changeCircuit($id, $circuit_id);

			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/deliberations/recapitulatif/'.$id);
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
	}


	function traiter($id = null, $valid=null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation.');
			$this->redirect('/deliberations/mesProjetsATraiter');
		}
		else
		{
			if ($valid==null)
			{
				$nb_recursion=0;
				$action='view';
				$listeAnterieure=array();
				$tab_delib=$this->Deliberation->find("Deliberation.id = $id");
				$tab_anterieure=$this->_chercherVersionAnterieure($id, $tab_delib, $nb_recursion, $listeAnterieure, $action);
				$this->set('tab_anterieure',$tab_anterieure);
				$commentaires = $this->Commentaire->findAll("delib_id = $id and pris_en_compte = 0", null, "created ASC");
				for($i=0; $i< count($commentaires) ; $i++) {
                                    if($commentaires[$i]['Commentaire']['agent_id'] == -1) {
                                        $nomAgent = 'i-parapheur';
                                        $prenomAgent = '';
                                    }
                                    else {
                                        $nomAgent = $this->requestAction("users/getNom/".$commentaires[$i]['Commentaire']['agent_id']);
                                        $prenomAgent = $this->requestAction("users/getPrenom/".$commentaires[$i]['Commentaire']['agent_id']);
                                    }
				    $commentaires[$i]['Commentaire']['nomAgent'] = $nomAgent;
			            $commentaires[$i]['Commentaire']['prenomAgent'] = $prenomAgent;
				}
				$this->set('commentaires', $commentaires);
				$deliberation= $this->Deliberation->read(null, $id);
				if (!empty($deliberation['Seance']['date']))
				    $deliberation['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Seance']['date']));
				$id_service = $deliberation['Service']['id'];
				$deliberation['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);

				$tab_circuit=$tab_delib['Deliberation']['circuit_id'];
				$delib=array();
				//on recupere la position courante de la deliberation
				$lastTraitement=array_pop($deliberation['Traitement']);
				$deliberation['positionDelib']=$lastTraitement['position'];
				//on recupere la position de l'user dans le circuit
				array_push($delib, $deliberation);
				$this->set('deliberation', $delib);
                                $userscircuit = $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit", null, 'UsersCircuit.position ASC');
                                if (USE_PARAPH)
                                    $soustypes = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
                                for ($i = 0; $i <count ($userscircuit); $i++){
                                    if($userscircuit[$i]['UsersCircuit']['service_id']== -1){
                                        $userscircuit[$i]['User']['prenom']= TYPETECH;
                                        $userscircuit[$i]['User']['nom']= $soustypes['soustype'][$userscircuit[$i]['UsersCircuit']['user_id']];
                                        $userscircuit[$i]['Service']['libelle']= 'i-parapheur';
                                    }
                                }
                                $this->set('user_circuit', $userscircuit); 
			}
			else
			{
				if ($valid=='1')
				{
					//verification du projet, s'il n'est pas pret ->reporte a la seance suivante
					$delib = $this->Deliberation->findAll("Deliberation.id = $id");
			                $type_id =$delib[0]['Seance']['type_id'];
					if(isset($type_id)){
						$type = $this->Typeseance->findAll("Typeseance.id = $type_id");
						$date_seance = $delib[0]['Seance']['date'];;
						$retard = $type[0]['Typeseance']['retard'];

						$condition= 'date > "'.date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y"))).'"';
						$seances = $this->Seance->findAll(($condition),null,'date asc');
						if (!empty($date_seance)){
						    if (mktime(date("H") , date("i") ,date("s") , date("m") , date("d")+$retard , date("Y"))>= strtotime($date_seance)){
						        $this->data['Deliberation']['seance_id']=$seances[0]['Seance']['id'];
							$this->data['Deliberation']['reporte']=1;
							$this->data['Deliberation']['id']=$id;
							if (isset($this->data['Deliberation']['seance_id']))
						    	    $position = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);
							else
						    	    $position = 0;
							$this->data['Deliberation']['position']=$position;
							$this->Deliberation->save($this->data);
						    }
						}
					}
					//on a valide le projet, il passe a la personne suivante
					$tab=$this->Traitement->findAll("delib_id = $id", null, "id ASC");

					$lastpos=count($tab)-1;
					$circuit_id=$tab[$lastpos]['Traitement']['circuit_id'];

					//MAJ de la date de traitement de la derniere position courante $lastpos
					$tab[$lastpos]['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
					$this->Traitement->save($tab[$lastpos]['Traitement']);

					//il faut verifier que le projet n'est pas arrive en fin de circuit
					//position courante du projet : lastposprojet : $tab[$lastpos]['Traitement']['position'];
					//derniere position theorique : lastposcircuit
					$lastposprojet=$tab[$lastpos]['Traitement']['position'];
					//$lastposcircuit=$this->Circuit->getLastPosition($circuit_id);
					$usersCircuit = $this->UsersCircuit->findAll("circuit_id = $circuit_id", null, "UsersCircuit.position ASC");
					$lastposcircuit=count($usersCircuit);

					if ($lastposcircuit==$lastposprojet) //on est sur la derniere personne, on va faire sortir le projet du workflow et le passer au service des assemblees
					{
						// passage au service des assemblee : etat dans la table deliberations passea2
						$tab=$this->Deliberation->findAll("Deliberation.id = $id");
						$this->data['Deliberation']['etat']=2;
						$this->data['Deliberation']['id']=$id;
						$this->Deliberation->save($this->data['Deliberation']);
						$this->redirect('/deliberations/mesProjetsATraiter');
					}
					else
					{
                                            // l'étape suivante est la création d'un dossier
					    if ($usersCircuit[$lastposprojet]['UsersCircuit']['service_id'] == -1) {
                                                $model_id = $this->_getModelId($id);
			                        $err = $this->requestAction("/models/generer/$id/null/$model_id/0/1/P_$id.pdf");
		                                $file =  WEBROOT_PATH."/files/generee/fd/null/$id/P_$id.pdf";

                                                $soustypes = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
                                                $soustype = $soustypes ['soustype'][$usersCircuit[$lastposprojet]['UsersCircuit']['user_id']];
                                                $emailemetteur = "htexier@cogitis.fr";
                                                $nomfichierpdf = "P_$id.pdf";
                                                $pdf = file_get_contents($file);
                                                $creerdos = $this->Parafwebservice->creerDossierWebservice(TYPETECH, $soustype, $emailemetteur, PREFIX_WEBDELIB.$id, '', '', VISIBILITY, '', $pdf); 
                                            }
					    else {
					        //sinon on fait passerala personne suivante
					        $this->_notifierDossierAtraiter($circuit_id, $tab[$lastpos]['Traitement']['position']+1, $id);
			                    }
					    $this->data['Traitement']['id']='';
					    $this->data['Traitement']['position']=$tab[$lastpos]['Traitement']['position']+1;
					    $this->data['Traitement']['delib_id']=$id;
					    $this->data['Traitement']['circuit_id']=$circuit_id;
					    $this->Traitement->save($this->data['Traitement']);
					    $this->redirect('/deliberations/mesProjetsATraiter');
				        }
				}
				else
				{
                                    $this->Deliberation->refusDossier($id);
                                    $delibTmp = $this->Deliberation->read(null, $id);

                                    // TODO notifier par mail toutes les personnes qui ont deja vise le projet
                                    $this->Deliberation->_notifierDossierRefuse($id, $delibTmp['Deliberation']['redacteur_id']);
              
                                    $tab=$this->Traitement->findAll("delib_id = $id", null, "id ASC");
                                    $circuit_id=$delibTmp['Deliberation']['circuit_id'];

                                    $condition = "circuit_id = $circuit_id";
                                    $listeUsers = $this->UsersCircuit->findAll($condition);
                                    foreach($listeUsers as $user) {
                                       if ($user['UsersCircuit']['service_id'] != -1)
                                           $this->_notifierDossierRefuse($id, $user['User']['id']);
                                    }

	       			    $this->redirect('/deliberations/mesProjetsATraiter');
				}
			}
		}
	}

	function _chercherVersionAnterieure($delib_id, $tab_delib, $nb_recursion, $listeAnterieure, $action)
	{
		$anterieure_id=$tab_delib['Deliberation']['anterieure_id'];

		if ($anterieure_id!=0) {

			$ant=$this->Deliberation->find("Deliberation.id=$anterieure_id");
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
       $nbDelibParPage = 5;
       $nbDelibs = 0;
       if ($message!='null')
             $this->set('message', $message);

        $this->set('USE_GEDOOO', USE_GEDOOO);
	$this->set('host', HOST );
        $this->set('dateClassification', $this->_getDateClassification());

        // On affiche que les delibs vote pour.
	$deliberations = $this->Deliberation->findAll("Deliberation.etat=5", null, "num_delib ASC",  $nbDelibParPage,  $page);
        $nbDelibs = count($deliberations);
	for($i = 0; $i < $nbDelibs; $i++) {
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
	$this->set('nbDelibs',  $nbDelibs );
        $this->set('deliberations', $deliberations);
	if ($page>1)
	    $this->set('previous', $page-1);
        if  ($nbDelibs > $nbDelibParPage*$page )
	    $this->set('next', $page+1);

    }

    function _getDateAR($fluxRetour) {
       // +21 Correspond a la longueur du string : actes:DateReception"
       $date = substr($fluxRetour, strpos($fluxRetour, 'actes:DateReception')+21, 10);
       return ($this->Date->frenchDate(strtotime($date )));
    }

    function _getFluxRetour ($tdt_id) {
        $url = 'https://'.HOST."/modules/actes/actes_transac_get_status.php?transaction=$tdt_id"; 
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
        curl_setopt($ch, CURLOPT_POST, TRUE);
      //  curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
        curl_setopt($ch, CURLOPT_SSLCERT, PEM);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
        curl_setopt($ch, CURLOPT_SSLKEY,  SSLKEY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        return($curl_return);
    }

    function getAR($tdt_id) {
        $url = 'https://'.HOST."/modules/actes/actes_create_pdf.php?trans_id=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
        curl_setopt($ch, CURLOPT_POST, TRUE);
      //  curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
        curl_setopt($ch, CURLOPT_SSLCERT, PEM);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
        curl_setopt($ch, CURLOPT_SSLKEY,  SSLKEY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        header('Content-type: application/pdf');
        header('Content-Length: '.strlen($curl_return));
	header('Content-Disposition: attachment; filename=Acquittement.pdf');
	echo $curl_return;
	exit();
    }

    function toSend ($id=null, $message= null){
        if (!empty( $message))
             $this->set('message', $message);

        $this->set('USE_GEDOOO', USE_GEDOOO);
        $this->set('host', HOST );
        $this->set('dateClassification', $this->_getDateClassification());
        $this->set('tabNature',          $this->_getNatureListe());
        $this->set('tabMatiere',         $this->_getMatiereListe());
        // On affiche que les delibs vote pour.
        $deliberations = $this->Deliberation->findAll("Deliberation.etat=3 and Deliberation.delib_pdf != '' ");

        for($i = 0; $i < count($deliberations); $i++) {
                $deliberations[$i]['Deliberation'][$deliberations[$i]['Deliberation']['id'].'_num_pref'] = $deliberations[$i]['Deliberation']['num_pref'];
             //           $deliberations[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($deliberations[$i]['Seance']['type_id'], $deliberations[$i]['Deliberation']['etat']);
	         
        }

        $this->set('deliberations', $deliberations);
    }


    function _getNatureListe(){
        $tab = array();
    	$doc = new DOMDocument('1.0', 'UTF-8');
        if(!$doc->load(FILE_CLASS))
            die("Error opening xml file");
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
		$xml = simplexml_load_file(FILE_CLASS);
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
        	$var = get_object_vars($object);
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
	    if (!is_file(FILE_CLASS))
	     $this->getClassification();
	    include ('vendors/progressbar.php');
            Initialize(200, 100,200, 30,'#000000','#FFCC00','#006699');
	    $url = 'https://'.HOST.'/modules/actes/actes_transac_create.php';
            $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);
	    foreach ($this->data['Deliberation'] as $id => $bool ){
                 if ($bool == 1){
		     $delib_id = substr($id, 3, strlen($id));
		     if (!isset($this->data['Deliberation'][$delib_id."_num_pref"]))
		         continue;
                     $Tabclassification[$delib_id]= $this->data['Deliberation'][$delib_id."_num_pref"];
		 }
            }
            $nbDelibAEnvoyer = count($Tabclassification);
            $nbEnvoyee = 1;
	    foreach ($this->data['Deliberation'] as $id => $bool ){
	        if ($bool == 1){
                    ProgressBar($nbEnvoyee*(100/$nbDelibAEnvoyer), 'G&eacute;n&eacute;ration du document ');
		    $delib_id = substr($id, 3, strlen($id));
		    $classification =   $Tabclassification[$delib_id];
		    $this->Deliberation->changeClassification($delib_id, $classification);
		    $class1 = substr($classification , 0, strpos ($classification , '.' ));
		    $rest = substr($classification , strpos ($classification , '.' )+1, strlen($classification));
		    $class2=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class3=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class4=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class5=substr($rest , 0, strpos ($classification , '.' ));

                    ProgressBar($nbEnvoyee*(100/$nbDelibAEnvoyer), 'Document G&eacute;n&eacute;r&eacute; ');
		    $delib = $this->Deliberation->findAll("Deliberation.id = $delib_id");

		    //Création du fichier de délibération au format pdf (on ne passe plus par la génération)
		    $file =  WEBROOT_PATH."/files/generee/fd/null/$delib_id/D_$delib_id.pdf";
                    $fp = fopen($file, 'w');
		    fwrite($fp, $delib[0]['Deliberation']['delib_pdf']);
		    fclose($fp);
        	        // Checker le code classification
        	        $data = array(
      	                 'api'           => '1',
     	                 'nature_code'   => '1',
     	                 'classif1'      => $class1 ,
     	                 'classif2'      => $class2,
     	                 'classif3'      => $class3,
     	                 'classif4'      => $class4,
     	                 'classif5'      => $class5,
      	                 'number'        => time(),
			 //'number'        => $delib[0]['Deliberation']['num_delib'],
     	                 'decision_date' => date("Y-m-d", strtotime($delib[0]['Seance']['date'])),
      	                 'subject'       => $delib[0]['Deliberation']['objet'],
      	                 'acte_pdf_file' => "@$file",
     	                 'acte_pdf_file_sign' => "",
   	                 );
		    $nb_pj=0;
		    foreach ($delib['0']['Annexe'] as $annexe) {
                        if ($annexe['type'] == 'G') {
			    $pj_file = $this->Gedooo->createFile($path."webroot/files/generee/fd/null/$delib_id/", $annexe['filename'], $annexe['data']);
			    $data["acte_attachments[$nb_pj]"] = "@$pj_file";
      	                    $data["acte_attachments_sign[$nb_pj]"] = "";
		         }
			 $nb_pj++;
                    }
                    ProgressBar($nbEnvoyee*(100/$nbDelibAEnvoyer), 'Pr&eacute;paration de l\'envoi ');

	                 $ch = curl_init();
                         curl_setopt($ch, CURLOPT_URL, $url);
			// curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
                         curl_setopt($ch, CURLOPT_POST, TRUE);
                         curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                         curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
                         curl_setopt($ch, CURLOPT_SSLCERT, PEM);
                         curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
                         curl_setopt($ch, CURLOPT_SSLKEY,  SSLKEY);
                         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                         curl_setopt($ch, CURLOPT_VERBOSE, true);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			 $curl_return = curl_exec($ch);

			 $pos    = strpos($curl_return, 'OK');
			 $tdt_id = substr  ($curl_return , 3 , strlen($curl_return) );
			 if ($pos === false) {
                              echo ('<script>');
                              echo ('    document.getElementById("pourcentage").style.display="none"; ');
                              echo ('    document.getElementById("progrbar").style.display="none";');
                              echo ('    document.getElementById("affiche").style.display="none";');
                              echo ('    document.getElementById("contTemp").style.display="none";');
                              echo ('</script>');
			      echo 'Erreur Curl : ' .  $curl_return;
			      die ('<br /><a href ="/deliberations/transmit"> Retour &agrave; la page pr&eacute;c&eacute;dente </a>');
                         }
			 else {
                              ProgressBar($nbEnvoyee*(100/$nbDelibAEnvoyer), 'Delib&eacute;ration '.$delib[0]['Deliberation']['num_delib'].' envoy&eacute;e ');
                              $nbEnvoyee ++;
			      $this->Deliberation->changeEtat($delib_id, '5');
			      $this->Deliberation->changeIdTdt($delib_id, $tdt_id);
			      curl_close($ch);
		              unlink ($file);
			    }
			}
		    }
                              echo ('<script>');
                              echo ('    document.getElementById("pourcentage").style.display="none"; ');
                              echo ('    document.getElementById("progrbar").style.display="none";');
                              echo ('    document.getElementById("affiche").style.display="none";');
                              echo ('    document.getElementById("contTemp").style.display="none";');
                              echo ('</script>');
			      echo ('<br />Les d&eacute;lib&eacute;rations ont &eacute;t&eacute; correctement envoy&eacute;es.');
			      die ('<br /><a href ="/deliberations/transmit" id="retour"> Retour &agrave; la page pr&eacute;c&eacute;dente </a>');
		}


       function _getDateClassification(){
	   $doc = new DOMDocument();
           if(!$doc->load(FILE_CLASS))
               die("Error opening xml file");
	   $date = $doc->getElementsByTagName('DateClassification')->item(0)->nodeValue;
	   return ($this->Date->frenchDate(strtotime($date )));
           //return true;
        }

 	function getClassification($id=null){
	    $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);

	    $url = 'https://'.HOST.'/modules/actes/actes_classification_fetch.php';
            $data = array(
         	'api'           => '1',
             );
        $url .= '?'.http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
    //	curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
        curl_setopt($ch, CURLOPT_SSLCERT,  PEM);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
	curl_setopt($ch, CURLOPT_SSLKEY,  SSLKEY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $reponse = curl_exec($ch);

        if (curl_errno($ch))
          print curl_error($ch);
        curl_close($ch);

        // Assurons nous que le fichier est accessible en ecriture
       if (is_writable(FILE_CLASS)) {
           if (!$handle = fopen(FILE_CLASS, 'w')) {
               echo "Impossible d'ouvrir le fichier (".FILE_CLASS.")";
               exit;
        	}
        	// Ecrivons quelque chose dans notre fichier.
        	if (fwrite($handle, utf8_encode($reponse)) === FALSE) {
            	echo "Impossible d'ecrire dans le fichier ($filename)";
            	exit;
       	 	}
        	else
            	$this->redirect('/deliberations/transmit');
        	fclose($handle);
        }
        else
            echo "Le fichier ".FILE_CLASS." n'est pas accessible en ecriture.";
}

        function positionner($id=null, $sens, $seance_id)
        {
			$positionCourante = $this->Deliberation->getCurrentPosition($id);
			$lastPosition = $this->Deliberation->getLastPosition($seance_id);
        	if ($sens != 0)
            	$conditions = "Deliberation.seance_id = $seance_id  AND Deliberation.position = $positionCourante-1 AND etat!=-1";
       		else
   		    	$conditions = "Deliberation.seance_id = $seance_id  AND Deliberation.position = $positionCourante+1 AND etat!=-1";

   		    $obj = $this->Deliberation->findAll($conditions);
			//position du suivant ou du precedent
       		$id_obj = $obj['0']['Deliberation']['id'];
			$newPosition = $obj['0']['Deliberation']['position'];

   		    $this->data = $this->Deliberation->read(null, $id);
			$this->data['Deliberation']['position'] = $newPosition;

   		    //enregistrement de l'objet courant avec la nouvelle position
			if (!$this->Deliberation->save($this->data)) {
			   die('Erreur durant l\'enregistrement');
			}
			// On recupere les informations de l'objet a deplacer
			$this->data = $this->Deliberation->read(null, $id_obj);
			$this->data['Deliberation']['position']= $positionCourante;

			//enregistrement de l'objet a deplacer avec la position courante
			if ($this->Deliberation->save($this->data)) {

			$this->redirect("/seances/afficherProjets/$seance_id/");
			}
			else {
		 	   $this->Session->setFlash('Erreur durant l\'enregistrement');
			}
        }

        function sortby($seance_id, $sortby) {
		    $condition= "seance_id=$seance_id AND etat != -1";
		    // Critere de tri
		    if ($sortby == 'theme_id') 
		        $sortby = 'Theme.order';
	            elseif  ($sortby == 'service_id') 
		        $sortby = 'Service.order';
		    elseif ($sortby == 'rapporteur_id') 
		        $sortby = 'Rapporteur.nom';

  		    $deliberations = $this->Deliberation->findAll($condition,null, "$sortby ASC");
		    for($i=0; $i<count($deliberations); $i++){
			    $deliberations[$i]['Deliberation']['position']=$i+1;
		    	$this->Deliberation->save($deliberations[$i]['Deliberation']);
		    }
		    $this->redirect("seances/afficherProjets/$seance_id");
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

        function _notifierDossierAtraiter($circuit_id, $pos, $delib_id){
            $conditions = "UsersCircuit.circuit_id=$circuit_id and UsersCircuit.position=$pos";
            $data = $this->UsersCircuit->findAll($conditions);
            // Si l'utilisateur accepte les mails
            if ($data['0']['User']['accept_notif']){
                $to_mail = $data['0']['User']['email'];
                $this->Email->template = 'email/traiter';
                $this->set('data',  $this->_paramMails('traiter', $this->Deliberation->read(null, $delib_id),  $data['0']['User']));
                $this->Email->to = $to_mail;
                $this->Email->subject = "DELIB $delib_id";
                $result = $this->Email->send();
            }
	}

        function _notifierDossierRefuse($delib_id,$user_id){
            $condition = "Deliberation.id = $delib_id";
            $data = $this->Deliberation->findAll($condition);
            $redacteur_id = $data['0']['Deliberation']['redacteur_id'];
            $data_comm = $this->Commentaire->findAll("delib_id = $delib_id");

            $condition = "User.id = $user_id";
            $data = $this->User->findAll($condition);

            // Si l'utilisateur accepte les mails
            if ($data['0']['User']['accept_notif']){
                $this->Email->template = 'email/refuse';
                $this->set('data', $this->_paramMails('refus', $this->Deliberation->read(null, $delib_id),  $data['0']['User']));
                $this->Email->to =  $data['0']['User']['email'];
                $this->Email->subject = "DELIB $delib_id Refusee !";
                $result = $this->Email->send();
            }
        }

        function _notifierInsertionCircuit ($delib_id, $user_id) {
            $condition = "User.id = $user_id";
            $data = $this->User->findAll($condition);

            // Si l'utilisateur accepte les mails
            if ($data['0']['User']['accept_notif']){
                $this->Email->template = 'email/circuit';
                $this->set('data',  $this->_paramMails('insertion', $this->Deliberation->read(null, $delib_id),  $data['0']['User']));
                $this->Email->to = $data['0']['User']['email'];
                $this->Email->subject = "vous allez recevoir la delib : $delib_id";
                $result = $this->Email->send();
            }
	}

	function _getListPresent($delib_id){
	    return $this->Listepresence->findAll("Listepresence.delib_id= $delib_id", null, "Acteur.position ASC");
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

			if ($nbVoix < ($nbConvoques/2))
				   $this->_reporteDelibs($delib_id);

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
		$this->Session->setFlash('Le quorum n\'est plus atteint, toutes les projets suivants sont &agrave; attribuer...');
		$this->redirect('seances/listerFuturesSeances');
		exit;
	}

	function _effacerListePresence($delib_id) {
		$condition = "delib_id = $delib_id";
		$presents = $this->Listepresence->findAll($condition);
		foreach($presents as $present)
  		    $this->Listepresence->del($present['Listepresence']['id']);
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
			"#LIBELLE_CIRCUIT#"=> $delib['Circuit']['libelle'],
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
		$userId=$this->Session->read('user.User.id');
		$listeLiens = $this->Acl->check($userId, "Deliberations:add") ? array('add') : array();

		$conditions = 'Deliberation.etat = 0 AND Deliberation.redacteur_id = ' . $userId;
		$ordre = 'Deliberation.created DESC';

		$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

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
		$projets = array();
		$userId = $this->Session->read('user.User.id');

		$listeCircuits = $this->UsersCircuit->listeCircuitsParUtilisateur($userId);

		if (!empty($listeCircuits)) {
			$conditions = 'Deliberation.etat = 1 AND Deliberation.circuit_id IN ('.$listeCircuits.')';
			$ordre = 'Deliberation.created DESC';
			$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

			// suppression des projets non concernés
    	    foreach($projets as $i=>$delib)
				if (!($this->Traitement->tourUserDansCircuit($userId, $projets[$i]['Deliberation']['id']) === 0))
					unset($projets[$i]);
		}

		$this->_afficheProjets(
			$projets,
			'Mes projets &agrave; traiter',
			array('traiter', 'generer'));
	}

/*
 * Affiche la liste des projets en cours de validation (etat = 1) qui sont dans les circuits
 * de validation de l'utilisateur connecté et dont ce n'est pas le tour de valider et les projets
 * dont il est le rédacteur
 */
	function mesProjetsValidation() {
		$userId=$this->Session->read('user.User.id');

		$listeCircuits = $this->UsersCircuit->listeCircuitsParUtilisateur($userId);
		$conditions = 'Deliberation.etat = 1 AND ';
		$conditions .= empty($listeCircuits) ? '' : '(Deliberation.circuit_id IN ('.$listeCircuits.') OR ';
		$conditions .= 'Deliberation.redacteur_id = ' . $userId;
		$conditions .= empty($listeCircuits) ? '' : ')';

		$ordre = 'Deliberation.created DESC';
		$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

		/* initialisation pour chaque projet et suppression des projets non concernés */
   	    foreach($projets as $i=>$delib) {
			$estRedacteurHorsCircuits = empty($listeCircuits) || strpos($listeCircuits, $projets[$i]['Deliberation']['circuit_id'])===false;
			if ($estRedacteurHorsCircuits)
				$tourDansCircuit = 0;
			else
				$tourDansCircuit = $this->Traitement->tourUserDansCircuit($userId, $projets[$i]['Deliberation']['id']);

			if (!$estRedacteurHorsCircuits && $tourDansCircuit == 0)
				unset($projets[$i]);
		}

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
		$userId=$this->Session->read('user.User.id');
                $editerProjetValide = $this->Acl->check($userId, "Deliberations:editerProjetValide");
		$listeCircuits = $this->UsersCircuit->listeCircuitsParUtilisateur($userId);
		$conditions = 'Deliberation.etat = 2 AND ';
		$conditions .= empty($listeCircuits) ? '' : '(Deliberation.circuit_id IN ('.$listeCircuits.') OR ';
		$conditions .= 'Deliberation.redacteur_id = ' . $userId;
		$conditions .= empty($listeCircuits) ? '' : ')';
		$ordre = 'Deliberation.created DESC';

		$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

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
		$editerProjetValide = $this->Acl->check($userId, "Deliberations:editerProjetValide");

		$this->data = $projets;

		/* initialisation pour chaque projet ou délibération */
        foreach($this->data as $i=>$projet) {
			// initialisation des icônes
        	if ($this->data[$i]['Deliberation']['etat'] == 0 && $this->data[$i]['Deliberation']['anterieure_id']!=0)
	            $this->data[$i]['iconeEtat'] = $this->_iconeEtat(-1);
        	elseif ($this->data[$i]['Deliberation']['etat'] == 1) {
			$estDansCircuit = $this->UsersCircuit->estDansCircuit($userId, $this->data[$i]['Deliberation']['circuit_id']);
			$tourDansCircuit = $estDansCircuit ? $this->Traitement->tourUserDansCircuit($userId, $this->data[$i]['Deliberation']['id']) : 0;
			$estRedacteur = $userId == $this->data[$i]['Deliberation']['redacteur_id'];
			$this->data[$i]['iconeEtat'] = $this->_iconeEtat(1, false, $estDansCircuit, $estRedacteur, $tourDansCircuit);
        	}
        	else
			$this->data[$i]['iconeEtat'] = $this->_iconeEtat($this->data[$i]['Deliberation']['etat'], $editerProjetValide);

		// initialisation des actions
		$this->data[$i]['Actions'] = $listeActions;
		if ($this->data[$i]['Deliberation']['etat'] == 2 && $editerProjetValide) {
			$this->data[$i]['Actions'][] = 'edit';
			$this->data[$i]['Actions'][] = 'attribuerCircuit';
                }
			// initialisation des dates, modèle et service
			if (isset($this->data[$i]['Seance']['date'])) {
				$this->data[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data[$i]['Seance']['date']));
				$this->data[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($this->data[$i]['Seance']['type_id'], $this->data[$i]['Deliberation']['etat']);
			} else
				$this->data[$i]['Model']['id'] = 1;

			$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);

			if (isset($this->data[$i]['Deliberation']['date_limite']))
				$this->data[$i]['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($this->data[$i]['Deliberation']['date_limite']));
		}

		// passage des variables à la vue
		$this->set('titreVue', $titreVue);
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$this->set('listeLiens', $listeLiens);

		// on affiche la vue index
		$this->render('index');
	}

/*
 * Affiche la liste de tous les projets en cours de validation
 * Permet de valider en urgence un projet
 */
	function tousLesProjetsValidation() {
		// lecture en base
		$conditions = "Deliberation.etat = 1";
		$ordre = 'Deliberation.created DESC';
		$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

		$this->_afficheProjets(
			$projets,
			'Projets en cours d\'&eacute;laboration et de validation',
			array('view', 'validerEnUrgence', 'generer'));
	}

/*
 * Affiche la liste de tous les projets en cours de redaction, validation, validés sans séance
 * Permet de modifier un projet validé si l'utilisateur à les droits editerProjetValide
 */
	function tousLesProjetsSansSeance() {
		// lecture en base
		$conditions = "(Deliberation.seance_id is null OR Deliberation.seance_id=0) AND (Deliberation.etat=0 OR Deliberation.etat=1 OR Deliberation.etat=2)";
		$ordre = 'Deliberation.created DESC';
		$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);
		$this->set('date_seances', $this->Seance->generateList());
		$this->_afficheProjets(
			$projets,
			'Projets non associ&eacute;s &agrave; une s&eacute;ance',
			 array('view', 'generer', 'attribuerSeance'));
	}

/*
 * Affiche la liste de tous les projets validés liés à une séance
 */
	function tousLesProjetsAFaireVoter() {
		// lecture en base
		$conditions = "Deliberation.seance_id!=0 AND Deliberation.etat=2";
		$ordre = 'Deliberation.created DESC';
		$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

		$this->_afficheProjets(
			$projets,
			'Projets valid&eacute;s associ&eacute;s &agrave; une s&eacute;ance',
			array('view', 'generer'));
	}

/*
 * Attribue une séance à un projet
 * Appelée depuis la vue deliberations/tous_les_projets
 */
	function attribuerSeance () {
		if (!empty($this->data)) {
			$this->data['Deliberation']['position'] = $this->Deliberation->getLastPosition($this->data['Deliberation']['seance_id']);
			$this->Deliberation->save($this->data);
		}

		$this->redirect('/deliberations/tousLesProjetsSansSeance');
	}

/*
 * Permet de valider un projet en cours de validation en court-circuitant le circuit de validation
 * Appelée depuis la vue deliberations/tous_les_projets
 */
	function validerEnUrgence($delibId) {
		// Lecture de la délibération
		$this->Deliberation->recursive = -1;
		$this->data = $this->Deliberation->read('id, etat', $delibId);

		if (empty($this->data))
			$this->Session->setFlash('Invalide id pour le projet de d&eacute;lib&eacute;ration');
		else {
			if ($this->data['Deliberation']['etat']!=1)
				$this->Session->setFlash('Le projet de d&eacute;lib&eacute;ration doit &ecirc;tre en cours d\'&eacute;laboration');
			else {
				$this->data['Deliberation']['etat'] = 2;
				if ($this->Deliberation->save($this->data)) {
					// ajout du commentaire
					$this->data['Commentaire']['id'] = '';
					$this->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
					$this->data['Commentaire']['commentaire_auto'] = 1;
					$this->data['Commentaire']['texte'] = 'Validé en urgence le '.date('d-m-Y à H:i:s');
					$this->data['Commentaire']['texte'].= ', par '. $this->User->prenomNomLogin($this->Session->read('user.User.id'));
					$this->Deliberation->Commentaire->save($this->data);
				}
			}
		}

		$this->redirect('/deliberations/tousLesProjetsValidation');
	}

	function mesProjetsRecherche() {
		if (empty($this->data)) {
			$this->set('action', '/deliberations/mesProjetsRecherche/');
			$this->set('titreVue', 'Recherche multi-crit&egrave;res parmi mes projets');

			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus());
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateAllList());
			$this->set('services', $this->Deliberation->Service->generateList(null));
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList(null,'libelle asc',null,'{n}.Circuit.id','{n}.Circuit.libelle'));
			$this->set('etats', $this->Deliberation->generateListEtat());
			$this->set('infosupdefs', $this->Infosupdef->findAll('recherche = 1', 'id, nom, commentaire, type, taille', 'ordre', null, 1, -1));
			$this->set('listeBoolean', $this->Infosupdef->listSelectBoolean);

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
			        $this->Session->setFlash('Vous devez saisir un identifiant valide');
                                $this->redirect('/deliberations/mesProjetsRecherche');
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
				$this->Session->setFlash('Vous devez saisir au moins un crit&egrave;re.');
				$this->redirect('/deliberations/mesProjetsRecherche');
			} else {
				$userId=$this->Session->read('user.User.id');
				$listeCircuits = $this->UsersCircuit->listeCircuitsParUtilisateur($userId);
				$conditions .= ' AND ';
				$conditions .= empty($listeCircuits) ? '' : '(Deliberation.circuit_id IN ('.$listeCircuits.') OR ';
				$conditions .= 'Deliberation.redacteur_id = ' . $userId;
				$conditions .= empty($listeCircuits) ? '' : ')';
				$ordre = 'Deliberation.created DESC';

				$projets = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

				$this->_afficheProjets(
					$projets,
					'R&eacute;sultat de la recherche parmi mes projets',
					array('view', 'generer'),
					array('mesProjetsRecherche'));
			}
		}
	}

	function tousLesProjetsRecherche() {
		if (empty($this->data)) {
			$this->set('action', '/deliberations/tousLesProjetsRecherche/');
			$this->set('titreVue', 'Recherche multi-crit&egrave;res parmi tous les projets');

			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus());
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateAllList());
			$this->set('services', $this->Deliberation->Service->generateList(null));
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList(null,'libelle asc',null,'{n}.Circuit.id','{n}.Circuit.libelle'));
			$this->set('etats', $this->Deliberation->generateListEtat());
			$this->set('infosupdefs', $this->Infosupdef->findAll('recherche = 1', 'id, nom, commentaire, type, taille', 'ordre', null, 1, -1));
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
                                $this->Session->setFlash('Vous devez saisir un identifiant valide');
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
				$this->Session->setFlash('Vous devez saisir au moins un crit&egrave;re.');
				$this->redirect('/deliberations/tousLesProjetsRecherche');
			} else {
				// lecture en base
				$projets = $this->Deliberation->findAll($conditions, null, 'Deliberation.created DESC', null, null, 0);

				$this->_afficheProjets(
					$projets,
					'R&eacute;sultat de la recherche parmi tous les projets',
					array('view', 'generer'),
					array('tousLesProjetsRecherche'));
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
				'image' => '/icons/refuse.png',
				'titre' => $this->Deliberation->libelleEtat($etat));
			break;
		case 0 : // en cours de rédaction
			return array(
				'image' => '/icons/encours.png',
				'titre' => $this->Deliberation->libelleEtat($etat));
			break;
		case 1: // en cours de validation
			if ($estDansCircuit) {
				if ($tourDansCircuit == -1)
					return array(
						'image' => '/icons/fini.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : trait&eacute');
				elseif ($tourDansCircuit == 0)
					return array(
						'image' => '/icons/atraiter.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : &agrave; traiter');
				else
					return array(
						'image' => '/icons/attente.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : en attente');
			} else {
				if ($estRedacteur)
					return array(
						'image' => '/icons/fini.png',
						'titre' => $this->Deliberation->libelleEtat($etat) . ' : projet dont je suis le r&eacute;dacteur');
				else
					return array(
						'image' => '/icons/fini.png',
						'titre' => $this->Deliberation->libelleEtat($etat));
			}
			break;
		case 2: // validé
			if ($editerProjetValide)
				return array(
					'image' => '/icons/valide_editable.png',
					'titre' => $this->Deliberation->libelleEtat($etat));
			else
				return array(
					'image' => '/icons/fini.png',
					'titre' => $this->Deliberation->libelleEtat($etat));
			break;
		case 3: // voté et adopté
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->Deliberation->libelleEtat($etat));
			break;
		case 4: // voté et non adopté
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->Deliberation->libelleEtat($etat));
			break;
		case 5: // transmis au contrôle de légalité
			return array(
				'image' => '/icons/fini.png',
				'titre' => $this->Deliberation->libelleEtat($etat));
			break;
		}
 	}

}
?>
