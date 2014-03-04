<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form', 'Form2', 'Fck', 'Html2');
	var $components = array('Date','Email', 'Gedooo', 'Conversion', 'Droits', 'Progress', 'S2low', 'ModelOdtValidator.Fido');
	var $uses = array('Deliberation', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'ModelOdtValidator.Modeltemplate', 'Annex', 'Typeseance', 'Acteur', 'Infosupdef', 'Infosup');
	var $cacheAction = 0;

	// Gestion des droits
	var $demandeDroit = array(
			'listerFuturesSeances',
			'add',
			'afficherCalendrier',
			'listerAnciennesSeances');

	var $commeDroit = array(
			'view'             => 'Seances:listerFuturesSeances',
			'delete'           => 'Seances:listerFuturesSeances',
			'edit'             => 'Seances:listerFuturesSeances',
			'afficherProjets'  => 'Seances:listerFuturesSeances',
			'reportePositionsSeanceDeliberante'  => 'Seances:listerFuturesSeances',
			'genererConvoc'    => 'Seances:listerFuturesSeances',
			'multiodj'         => 'Seances:listerFuturesSeances',
			'changePosition'   => 'Seances:listerFuturesSeances',
			'saisirDebatGlobal'=> 'Seances:listerFuturesSeances',
			'details'          => 'Seances:listerFuturesSeances',
			'saisirDebat'      => 'Seances:listerFuturesSeances',
			'voter'            => 'Seances:listerFuturesSeances',
			'changeRapporteur' => 'Seances:listerFuturesSeances',
			'changeStatus'     => 'Seances:listerFuturesSeances',
			'detailsAvis'      => 'Seances:listerFuturesSeances',
			'donnerAvis'       => 'Seances:listerFuturesSeances',
			'saisirSecretaire' => 'Seances:listerFuturesSeances',
			'getListActeurs'   => 'Seances:listerFuturesSeances',
            'sendConvocations' => 'Seances:listerFuturesSeances',
            'sendToIdelibre' => 'Seances:listerFuturesSeances',
			'saisirCommentaire'=>'Seances:listerFuturesSeances');


	function view($id = null) {
		$seance = $this->Seance->read(null, $id);
		if (!$id || empty($seance)) {
			$this->Session->setFlash('identifiant invalide pour la seance.', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/index');
		}
		$this->set('seance', $seance);
	}

	function add($timestamp=null) {
		// initialisation
		$sortie = false;
		$date = '';
		if (empty($this->data)) {
			if (isset($timestamp)) $date = date('d/m/Y',$timestamp);
		} else {
//			$date = date('d/m/Y', strtotime($this->data['date']));
                        $date = $this->data['date'];
			if (count(explode('/', $date))!=3) {
				$this->Session->setFlash('La date n\'est pas dans un format correct', 'growl', array('type'=>'erreur'));
			} else {
				$this->request->data['Seance']['date']['date'] =  $this->Utils->FrDateToUkDate($date);
				$this->request->data['Seance']['date'] = $this->data['Seance']['date']['date'].' '.$this->data['Seance']['date']['hour'].':'.$this->data['Seance']['date']['min'];
				if ($this->Seance->save($this->data)) {
					$seanceId = $this->Seance->id;
					// sauvegarde des informations supplémentaires
					if (array_key_exists('Infosup', $this->data))
						$this->Infosup->saveCompacted($this->data['Infosup'], $seanceId, 'Seance');
					$this->Session->setFlash('La séance a été sauvegardée', 'growl');
					$sortie = true;
				} else {
					$this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
				}
			}
		}
		if ($sortie)
			$this->redirect(array('action'=>'listerFuturesSeances'));
		else {
			$this->set('date', $date);
			$natures = array_keys($this->Session->read('user.Nature'));
                        App::import('model','TypeseancesTypeacte');
                        $TypeseancesTypeacte = new TypeseancesTypeacte();
                        $types = $TypeseancesTypeacte->getTypeseanceParNature($natures);

			$this->set('typeseances', $this->Typeseance->find('list', array('conditions'=>array('Typeseance.id'=> $types) )));
			$this->set('infosupdefs', $this->Infosupdef->find('all', array(
					'recursive'=> -1,
					'conditions'=> array('model' => 'Seance', 'actif' => true),
					'order' => 'ordre')));
			$this->set('infosuplistedefs', $this->Infosupdef->generateListes('Seance'));
			$this->request->data['Infosup'] = $this->Infosupdef->valeursInitiales('Seance');
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		$date = '';
		$path_seance= WWW_ROOT.'files'.DS.'generee'.DS.'seance'.DS.$id.DS;
		if (empty($this->data)) { // not is post
			$this->Seance->Behaviors->attach('Containable');
			$this->request->data = $this->Seance->find('first', array( 'contain'=>array('Infosup'),
					'conditions'=>array('Seance.id'=> $id)));
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour la seance', 'growl', array('type'=>'erreur'));
				$sortie = true;
			} else {
				$date = date('d/m/Y', strtotime($this->data['Seance']['date']));
				foreach ($this->data['Infosup']  as $infosup) {
					$infoSupDef = $this->Infosupdef->find('first', array(
							'recursive' => -1,
							'fields' => array('type'),
							'conditions' => array('id' =>$infosup['infosupdef_id'], 'model' => 'Seance', 'actif'=>true)));
					if ($infoSupDef['Infosupdef']['type'] == 'odtFile' && !empty($infosup['file_name']) && !empty($infosup['content']))
						$this->Gedooo->createFile($path_seance, $infosup['file_name'], $infosup['content']);
				}
				$this->request->data['Infosup'] = $this->Infosup->compacte($this->data['Infosup']);
			}
		} else {
			$date = $this->data['date'];
			if (count(explode('/',$date))!=3) {
				$this->Session->setFlash('La date n\'est pas dans un format correct', 'growl', array('type'=>'erreur'));
			} else {
                                $success = true;
                                $this->Seance->begin();

				$this->request->data['Seance']['date']['date'] =  $this->Utils->FrDateToUkDate($date);
				$this->request->data['Seance']['date'] = $this->data['Seance']['date']['date'].' '.$this->data['Seance']['date']['hour'].':'.$this->data['Seance']['date']['min'];
                                
                                $success = $this->Seance->save($this->data) && $success;
				if ( $success ) {
					// sauvegarde des fichiers odt car possibilité modifiés en webdav sur le serveur
					$infossupDefs = $this->Infosupdef->find('all', array(
							'recursive' => -1,
							'fields' => array('id'),
							'conditions' => array('type' => 'odtFile', 'model' => 'Seance', 'actif' => true)));
					foreach ($infossupDefs as $infossupDef) {
						$infosup = $this->Infosup->find('first', array(
								'recursive' => -1,
								'fields' => array('id', 'file_name'),
								'conditions' => array('foreign_key'=>$id, 'model'=>'Seance', 'infosupdef_id'=>$infossupDef['Infosupdef']['id'])));
						if (empty($infosup) || empty($infosup['Infosup']['file_name']))
							continue;
						$odtFileUri = $path_seance.$infosup['Infosup']['file_name'] ;
                                     
						if (file_exists($odtFileUri)){
							$stat = stat($odtFileUri);
							if ($stat > 0) {
								$infosup['Infosup']['content'] = file_get_contents($odtFileUri);
								$infosup['Infosup']['file_size'] = $stat['size'];
								$success = $this->Infosup->save($infosup) && $success;
							}
						}
					}
					// sauvegarde des informations supplémentaires
					if (array_key_exists('Infosup', $this->data))
						$success = $this->Infosup->saveCompacted($this->data['Infosup'], $id, 'Seance') && $success;
                                        //exit; // FIXME
				}
                                
                                if( $success ) {
                                    $this->Seance->commit();
                                    $this->Session->setFlash('La séance a été sauvegardée', 'growl');
                                    $sortie = true;
                                } else {
                                    $this->Seance->rollback();
                                    $this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
                                    $errors_Infosup = $this->Infosup->invalidFields();	
                                    $this->set('errors_Infosup', $errors_Infosup);
                                    $errors_Seance = $this->Seance->invalidFields();
                                    $this->set('errors_Seance', $errors_Seance);
                                    
                                }
			}
		}
		if ($sortie)
			$this->redirect(array('action'=>'listerFuturesSeances'));
		else {
			$this->set('date', $date);
			$natures = array_keys($this->Session->read('user.Nature'));
                        App::import('model','TypeseancesTypeacte');
                        $TypeseancesTypeacte = new TypeseancesTypeacte();
                        $types = $TypeseancesTypeacte->getTypeseanceParNature($natures);

			$this->set('typeseances', $this->Typeseance->find('list', array('conditions'=>array('Typeseance.id'=> $types) )));
			$this->set('infosupdefs', $this->Infosupdef->find('all', array(
					'recursive'=> -1,
					'conditions'=> array('model' => 'Seance', 'actif' => true),
					'order' => 'ordre')));
			$this->set('infosuplistedefs', $this->Infosupdef->generateListes('Seance'));
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la seance', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/index');
		}
		$delibs = $this->Seance->getDeliberationsId($id);
		if (count($delibs) != 0) {
			$this->Session->setFlash('Cette séance contient des actes. Vous ne pouvez pas la supprimer.', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/listerFuturesSeances');
		}
		if ($this->Seance->delete($id)) {
			$this->Session->setFlash('La séance a été suprimée');
			$this->redirect('/seances/listerFuturesSeances');
		}
		else {
			$this->Session->setFlash('Invalide id pour la seance', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/index');
		}
	}

	function listerFuturesSeances() {
		$this->set('AFFICHE_CONVOCS_ANONYME', Configure::read('AFFICHE_CONVOCS_ANONYME'));
		$this->set('use_pastell', Configure::read('USE_PASTELL'));
		$this->set('canSign', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:sendToParapheur"));
		$format =  $this->Session->read('user.format.sortie');
        $this->set('models', $this->Modeltemplate->find('list', array(
            'recursive' => -1,
            'conditions' => array('modeltype_id' => array(MODEL_TYPE_MULTISEANCE)),
            'fields' => array('name'))));
		if (empty($format))
			$format =0;
		$this->set('format', $format);
			
		if (empty ($this->data)) {
			$this->Seance->Behaviors->attach('Containable');
			$seances = $this->Seance->find('all', array('conditions'=> array('Seance.traitee'=>0),
					'order'    =>array('date ASC'),
					'fields'    => array('id', 'date', 'type_id'),
					'contain'   => array('Typeseance.libelle', 'Typeseance.action',
							'Typeseance.modelconvocation_id',
							'Typeseance.modelordredujour_id',
							'Typeseance.modelpvsommaire_id',
							'Typeseance.modelpvdetaille_id')));

			for ($i=0; $i<count($seances); $i++){
				$seances[$i]['Seance']['dateEn'] =  $seances[$i]['Seance']['date'];
				$seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));
			}
			$this->set('seances', $seances);
		}
	}

	function listerAnciennesSeances() {
		$this->Seance->Behaviors->attach('Containable');
		if (empty ($this->data)) {
			$seances = $this->Seance->find('all',
					                       array('conditions' => array('Seance.traitee'=> 1),
					                       		  'contain'    => array('Typeseance.libelle'),
					                       		  'fields'     => array('Seance.id', 'Seance.date', 'Seance.type_id'),
					                       		  'ordre'      => 'date asc'));

			for ($i=0; $i<count($seances); $i++)
				$seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));
			$this->set('seances', $seances);
		}
	}

    /**
     * FIXME $result non utilisé en retour
     * @param $seance_id
     * @return bool
     */
    function changeStatus ($seance_id) {
		$result = false;
		$isArrete = false;
		$compteur_id = null;
			
		$this->data=$this->Seance->find('first', array('conditions'=> array('Seance.id' => $seance_id)));
		// Avant de cloturer la séance, on stock les délibérations en base de données au format pdf
               
		if (($this->data['Typeseance']['action'] == 0) || ($this->data['Typeseance']['action'] == 2)) {
			if($this->data['Typeseance']['action'] == 2) {
				$isArrete =true;
				$compteur_id = $this->data['Typeseance']['compteur_id'];
			}
			$result = $this->_stockDelibs($seance_id,  $isArrete, $compteur_id);
		}
		if ($result || $this->data['Typeseance']['action'] == 1) {
			$result = $this->_stockDelibs($seance_id,  $isArrete, $compteur_id);
			$this->Seance->id = $seance_id;
			if ($this->Seance->saveField('traitee', 1)){
				return true;
			}
		}
		else
            return false;
	}

	function _stockDelibs($seance_id, $isArrete=false, $compteur_id=null) {
		$result = true;
		$ids = $this->Seance->getDeliberationsId($seance_id);
		$delibs = $this->Deliberation->find("all", array('conditions' => array("Deliberation.id"=>$ids)));
        if (empty($delibs)) return false;

        // chargement du comportement de fusion pour la première délibération
        $this->Deliberation->Behaviors->load('OdtFusion', array('id' => $delibs[0]['Deliberation']['id']));
        // pour toutes les délibérations
        foreach ($delibs as $delib) {
			$delib_id = $delib['Deliberation']['id'];
			$this->Deliberation->id =  $delib_id;
			$isArrete = $this->Deliberation->is_arrete($delib_id);

			if ($isArrete){
				$this->Deliberation->saveField('etat', 3);
				if ( $compteur_id != null) {
					$num =  $this->Seance->Typeseance->Compteur->genereCompteur($compteur_id);
					$position = $this->Deliberation->getPosition( $delib['Deliberation']['id'], $seance_id);
					$num = str_replace('#pos#', $position, $num);
					$this->Deliberation->saveField('num_delib', $num);
				}
			}
			if ($delib['Deliberation']['parapheur_etat'] == 2 && !empty($delib['Deliberation']['delib_pdf']))
				continue;
			// On génère la délibération au format PDF
//			$model_id = $this->Deliberation->getModelForSeance($delib_id, $seance_id);
//			$this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/D_$delib_id.odt");
//			$filename =  WEBROOT_PATH."/files/generee/fd/null/$delib_id/D_$delib_id.odt.pdf";
//			$content = file_get_contents($filename);
            $this->Deliberation->odtFusion(array('id'=>$delib_id));
            $content = $this->Conversion->convertirFlux($this->Deliberation->odtFusionResult->content->binary, 'odt', 'pdf');
            unset($this->Deliberation->odtFusionResult);

			if (strlen($content) == 0)
				$result = false;
			// On stock le fichier en base de données.
			$this->Deliberation->saveField('delib_pdf', $content);
            unset($content);
		}
		return $result;
	}

	function afficherCalendrier ($annee=null){
		// initialisations
		require_once(APP.'Vendor'.DS.'Calendar'.DS.'includeCalendarVendor.php');
		Configure::write('CALENDAR_MONTH_STATE', Configure::read('CALENDAR_USE_MONTH_WEEKDAYS'));
		$tabJoursSeances = array();
		$annee = empty($annee) ? date('Y') : $annee;
		$droitAdd = $this->Droits->check($this->Session->read('user.User.id'), 'Seances:add');
		$droitEdit = $this->Droits->check($this->Session->read('user.User.id'), 'Seances:edit');

		// lecture des séances non traitées en DB
		$this->Seance->Behaviors->attach('Containable');
		$seances = $this->Seance->find('all', array(
			'fields' => array('Seance.id', 'Seance.date', 'Seance.type_id'),
			'contain' => array('Typeseance.libelle'),
			'conditions' => array('Seance.traitee'=> 0),
			'order' => 'date ASC'));
		foreach ($seances as $seance) {
			$date = strtotime(substr($seance['Seance']['date'], 0, 10));
			$tabJoursSeances[$date][] = array(
				'seanceId' => $seance['Seance']['id'],
				'seanceLibelle' => $seance['Typeseance']['libelle'].' à '.date('H\Hi', strtotime($seance['Seance']['date'])));
		}

		// contruction du html du calendrier
		$Year = new Calendar_Year($annee);
		$Year->build();
		$today = mktime('0','0','0');
		$i = 0;

		$calendrier = "<table>\n<tr   style=\"vertical-align:top;\">\n";
		while ( $Month = $Year->fetch() ) {

			$calendrier .= "<td><table class=\"month\">\n" ;
			$calendrier .= "<caption class=\"month\">".$this->Date->months[$Month->thisMonth('int')]."</caption>\n" ;
			$calendrier .= "<tr><th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th><th>Di</th></tr>\n";
			$Month->build();

			while ( $Day = $Month->fetch() ) {
				if ( $Day->isFirst() == 1 ) {
					$calendrier .= "<tr>\n" ;
				}
				if ( $Day->isEmpty() ) {
					$calendrier .=  "<td>&nbsp;</td>\n" ;
				} else {
					$class="normal";
					$url = $droitAdd ? 'add/'.$Day->thisDay('timestamp') : ''; 
					$title = '';
					$infoPlusDuneSeance = '';
					if ($today == $Day->thisDay('timestamp')){
						$class="today";
					} elseif (!empty($tabJoursSeances[$Day->thisDay('timestamp')]) ) {
						if ($droitEdit) {
							$class="seance";
							foreach($tabJoursSeances[$Day->thisDay('timestamp')] as $jourSeance)
								$title .= (empty($title)?'':', ').$jourSeance['seanceLibelle'].' ';
							$infoPlusDuneSeance = count($tabJoursSeances[$Day->thisDay('timestamp')])>1 ? ' *' : ''; 
							$url = 'edit/'.$tabJoursSeances[$Day->thisDay('timestamp')][0]['seanceId'];
						}
					}
					if (empty($url))
						$calendrier .=  "<td>".$Day->thisDay()."</td>\n" ;
					else
						$calendrier .=  "<td><a href =\"$url\"><p class=\"$class\" title=\"$title\">".$Day->thisDay()."$infoPlusDuneSeance</p></a></td>\n" ;
				}
				if ( $Day->isLast() ) {
					$calendrier .=  "</tr>\n" ;
				}
			}

			$calendrier .= "</table>\n</td>\n" ;

			if ($i==5)
				$calendrier .= "</tr><tr style=\"vertical-align:top;\">\n" ;

			$i++;
		}
		$calendrier .=  "</tr>\n</table>\n" ;

		$this->set('annee', $annee);
		$this->set('calendrier',$calendrier);
	}

	function afficherProjets ($id=null, $return=null) {
            if (!isset($return)) {
		$this->set('lastPosition', $this->Seance->getLastPosition($id) - 1 );
		$deliberations =  $this->Seance->getDeliberations($id);
		$lst_pos=array();
		for ($i=0; $i<count($deliberations); $i++) {
			$theme = $this->Deliberation->Theme->find('first',
					array('conditions' => array('Theme.id' => $deliberations[$i]['Deliberation']['theme_id'] ),
												 'recursive'  => -1));

			$service = $this->Deliberation->Service->find('first', array('conditions' => array('Service.id' => $deliberations[$i]['Deliberation']['service_id'] ),
																		  'recursive'  => -1));
			$deliberations[$i]['Theme'] = $theme['Theme'];
			$deliberations[$i]['Service'] = $service['Service'];
			$lst_pos[$i+1] = $i+1;
		}
		$this->set('seance_id', $id);
		$this->set('rapporteurs', $this->Acteur->generateListElus());
		$this->set('projets', $deliberations);
		$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->Seance->getDate($id))));
		$this->set('lst_pos', $lst_pos);
                $this->set('is_deliberante', $this->Seance->isSeanceDeliberante($id));
           } 
	   else
	       return ($this->Seance->getDeliberationsId($id));
	}
        
        function reportePositionsSeanceDeliberante ($seance_id) {
            if ($this->Seance->Deliberationseance->reportePositionsSeanceDeliberante($seance_id))
                $this->Session->setFlash('Report effectué.', 'growl');
            else
                $this->Session->setFlash('Report non effectué.', 'growl', array('type'=>'erreur'));
                
            $this->redirect('/seances/afficherProjets/'.$seance_id);
        }

	function changeRapporteur($seance_id, $newRapporteur, $delib_id)
    {
        $this->Deliberation->id = $delib_id;
        if ($this->Deliberation->saveField('rapporteur_id', $newRapporteur))
            $this->redirect(array('action' => 'afficherProjets', $seance_id));
    }

	function details ($seance_id = null) {
		$this->set('seance_id', $seance_id);
		$this->Deliberation->Behaviors->attach('Containable');

        $this->Seance->Behaviors->attach('Containable');
		$seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id'=> $seance_id),
            'fields' => array('Seance.type_id'),
            'contain' => array('Typeseance.libelle', 'Typeseance.action'))
        );

		$delibs = $this->Seance->getDeliberationsId($seance_id);
		$deliberations = array();
        foreach ($delibs as $delib_id) {
            $deliberations[] = $this->Deliberation->find('first',
                array(
                    'conditions' => array('Deliberation.id' => $delib_id),
                    'contain' => array(
                        'Theme.libelle',
                        'Rapporteur.nom',
                        'Rapporteur.prenom',
                        'President.nom',
                        'President.prenom',
                        'Service.libelle'
                    ),
                    'fields' => array(
                        'Deliberation.objet_delib',
                        'Deliberation.titre',
                        'Deliberation.id',
                        'Deliberation.etat',
                        'Deliberation.typeacte_id',
                        'Deliberation.num_delib'
                    )));
        }
		for ($i=0; $i<count($deliberations); $i++) {
            $id_service = $deliberations[$i]['Service']['id'];
            $deliberations[$i]['Deliberation']['is_delib'] = $this->Deliberation->is_delib($deliberations[$i]['Deliberation']['id']);
            $deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
            $deliberations[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['Seance']['type_id'],
	                                                                                                        $deliberations[$i]['Deliberation']['etat']);
		}
        $this->set('seance',$seance);
		$this->set('deliberations',$deliberations);
		$date_tmpstp = strtotime($this->Seance->getDate($seance_id));
		$this->set('date_tmpstp', $date_tmpstp);
		$this->set('date_seance', $this->Date->frenchDateConvocation($date_tmpstp));
		$this->set('seance_id', $seance_id);
	}

	function effacerVote($deliberation_id=null) {
		$votes = $this->Vote->find('all', array(
            'conditions' => array('Vote.delib_id' => $deliberation_id),
            'fields'     => array('Vote.id'),
            'recursive'  => -1));
		foreach($votes as $vote)
			$this->Vote->delete($vote['Vote']['id']);
	}

    function voter($deliberation_id, $seance_id)
    {
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
                                $this->request->data['Deliberation']['vote_nb_oui']++;
                            elseif ($vote == 2)
                                $this->request->data['Deliberation']['vote_nb_non']++;
                            elseif ($vote == 4)
                                $this->request->data['Deliberation']['vote_nb_abstention']++;
                            elseif ($vote == 5)
                                $this->request->data['Deliberation']['vote_nb_retrait']++;
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
            if (empty($deliberation['Deliberation']['num_delib'])){
                $this->request->data['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($seance['Typeseance']['compteur_id'], $seance['Seance']['date']);
                $this->request->data['Deliberation']['num_delib'] = str_replace(
                    '#p#',
                    $this->Deliberation->getPosition($deliberation_id, $seance_id),
                    $this->data['Deliberation']['num_delib']);
            }
            if ($this->Deliberation->save($this->data['Deliberation'])){
                $this->redirect(array('action' => 'details', $seance_id));
            }
        }
    }

    function saisirDebat($delib_id = null, $seance_id = null) {
        $this->set('seance_id', $seance_id);
        $this->set('delib_id', $delib_id);
        $this->Seance->Behaviors->attach('Containable');
        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields' => array('Seance.traitee', 'Seance.pv_figes'),
            'contain' => array('Typeseance.action')
        ));

        if ($seance['Seance']['pv_figes'] == 1) {
            $this->Session->setFlash('Les pvs ont été figés, vous ne pouvez plus saisir de débat pour cette délibération...', 'growl', array('type' => 'erreur'));
            return $this->redirect(array('controller' => 'postseances', 'action' => 'index'));
        }

        if (!empty($this->data['Deliberation']['texte_doc'])) {
            $this->Deliberation->set($this->data);
            if ($this->Deliberation->validates(array('fieldList' => array('texte_doc')))) {
                $details = $this->Fido->analyzeFile($this->data['Deliberation']['texte_doc']['tmp_name']);
                $type = $seance['Typeseance']['action'] ? 'commission' : 'debat';
                $deliberation = array(
                    'Deliberation' => array(
                        'id' => $delib_id,
                        $type . '_name' => $this->data['Deliberation']['texte_doc']['name'],
                        $type . '_size' => $this->data['Deliberation']['texte_doc']['size'],
                        $type . '_type' => $details['mimetype'],
                        $type => file_get_contents($this->data['Deliberation']['texte_doc']['tmp_name'])
                    )
                );
                if ($this->Deliberation->save($deliberation)) {
                    return $this->redirect($this->previous);
                }
            }
            $this->Session->setFlash('Erreur : Format du fichier incorrect', 'growl', array('type' => 'erreur'));
        }
        $this->request->data = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $delib_id),
            'recursive' => -1));
        $this->set('seance', $seance);
    }

    public function saisirDebatGlobal($id = null) {
        $this->Seance->Behaviors->load('Containable');
        $this->set('seance_id', $id);
        if (!empty($this->data['Seance']['texte_doc'])) {
            $this->Seance->set($this->data);
            if ($this->Seance->validates(array('fieldList' => array('texte_doc')))) {
                $details = $this->Fido->analyzeFile($this->data['Seance']['texte_doc']['tmp_name']);
                $seance = array(
                    'Seance' => array(
                        'id' => $id,
                        'debat_global_name' => $this->data['Seance']['texte_doc']['name'],
                        'debat_global_size' => $this->data['Seance']['texte_doc']['size'],
                        'debat_global_type' => $details['mimetype'],
                        'debat_global' => file_get_contents($this->data['Seance']['texte_doc']['tmp_name'])
                    )
                );
                if ($this->Seance->save($seance)) {
                    $this->Session->setFlash('Débat global enregistré', 'growl');
                    return $this->redirect($this->previous);
                }
            }
            $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous : format de fichier invalide', 'growl', array('type' => 'erreur'));
        }
        $this->request->data = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $id)
        ));
    }

    function detailsAvis ($seance_id=null) {
		$this->Deliberation->Behaviors->attach('Containable');

		// initialisations
		$deliberations = array();
		$delibs = $this->Seance->getDeliberationsId($seance_id);
		foreach ($delibs as $delib_id) {
			$deliberations[] = $this->Deliberation->find('first',
				                      	array('conditions' => array('Deliberation.id'=>$delib_id),
                                                        'fields'  => array('id', 'objet', 'objet_delib', 'titre', 'etat', 'num_delib'),
							'contain' => array('Theme.libelle', 'Rapporteur.nom',  'Rapporteur.prenom', 'Service.libelle')));
		}
		$date_tmpstp = strtotime($this->Seance->getDate($seance_id));
		$toutesVisees = true;
		$type_id = $this->Seance->getType($seance_id);
			
		for ($i=0; $i<count($deliberations); $i++){
			$deliberation_id = $deliberations[$i]['Deliberation']['id'];
			$delib_seance=$this->Deliberation->Deliberationseance->find('first', array('conditions' => array('Deliberationseance.seance_id' => $seance_id,
					'Deliberationseance.deliberation_id' => $deliberation_id),
					'recursive'  => -1 ));
			$deliberations[$i]['Deliberation']['avis'] = $delib_seance['Deliberationseance']['avis'];
			$id_service = $deliberations[$i]['Service']['id'];
			$deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
			$deliberations[$i]['Modeltemplate']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($type_id, $deliberations[$i]['Deliberation']['etat']);
			if (empty($deliberations[$i]['Deliberation']['avis']))
				$toutesVisees = false;
		}
			
		$this->set('deliberations',$deliberations);
		$this->set('date_seance', $this->Date->frenchDateConvocation($date_tmpstp));
		$this->set('seance_id', $seance_id);
		$this->set('canClose', (($date_tmpstp <= strtotime(date('Y-m-d H:i:s'))) && $toutesVisees));
	}
        
    function donnerAvis ($deliberation_id, $seance_id) {
        // Initialisations
        $deliberation = $this->Deliberation->find('first', array(
            'conditions' => array('Deliberation.id' => $deliberation_id),
            'fields'     => array('Deliberation.id', 'Deliberation.typeacte_id', 'Deliberation.objet','Deliberation.objet_delib', 'Deliberation.etat')));
        $delib_seance=$this->Deliberation->Deliberationseance->find('first', array( 'recursive' => -1,
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
                $this->Deliberation->Deliberationseance->set('avis', $this->data['Deliberation']['avis']==1?true:false);
                $this->Deliberation->Deliberationseance->set('commentaire', $this->data['Deliberation']['commentaire']);
                $this->Deliberation->Deliberationseance->save();
                if (!empty($this->data['Deliberation']['seance_id'])) {
                    foreach($this->data['Deliberation']['seance_id'] as $seance )
                        $this->Deliberation->Deliberationseance->addDeliberationseance($deliberation_id, $seance);
                }

                // ajout du commentaire
                $this->request->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
                $this->request->data['Commentaire']['texte'] = 'A reçu un avis ';
                $this->request->data['Commentaire']['texte'].= ($this->data['Deliberation']['avis'] == 1) ? 'favorable' : 'défavorable';
                if (!empty($this->data['Deliberation']['seance_id']))
                    $this->request->data['Commentaire']['texte'].= ' en '. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$this->Seance->getType($this->data['Deliberation']['seance_id'][0]));
                else
                    $this->request->data['Commentaire']['texte'].= ' en '. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$this->Seance->getType($seance_id));
                if (!empty($this->data['Deliberation']['seance_id']))
                    $this->request->data['Commentaire']['texte'].= ' du ' .$this->Date->frenchDate(strtotime($this->Seance->getDate($this->data['Deliberation']['seance_id'][0])));
                else
                    $this->request->data['Commentaire']['texte'].= ' du ' .$this->Date->frenchDate(strtotime($this->Seance->getDate($seance_id)));
                $this->request->data['Commentaire']['commentaire_auto'] = 1;
                $this->Deliberation->Commentaire->save($this->data);

                $this->redirect('/seances/detailsAvis/'.$seance_id);
            }
        }

        $this->request->data = $deliberation;

        $user = $this->Session->read('user');
        if ($this->Droits->check($user['User']['id'], "Deliberations:editerTous"))
            $afficherTtesLesSeances = true;
        else
            $afficherTtesLesSeances = false;

        //On retire les séances ou le projet est déja inclus
        $deliberationseance=$this->Deliberation->Deliberationseance->find('all', array(
            'fields'=> array('Deliberationseance.seance_id'),
            'conditions' => array( 'Deliberationseance.deliberation_id' => $deliberation_id ),
            'recursive'  => -1 ));
        $seance_notinclude = array();
        foreach($deliberationseance as $seance)
            $seance_notinclude[] = array('Seance.id <>'=> $seance['Deliberationseance']['seance_id']);

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
            'recursive'  => -1,
            'fields'     => array('id', 'type_id', 'president_id', 'secretaire_id')));
		$acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], true, array('id', 'nom', 'prenom'));
        if (empty($acteursConvoques)){
            $this->Session->setFlash('Aucun acteur convoqué.', 'growl', array('type'=>'erreur'));
            $this->redirect(array('action' => 'listerFuturesSeances'));
        }
        $tab = array();
		foreach( $acteursConvoques as  $acteurConvoque)
			$tab[$acteurConvoque['Acteur']['id']] =  $acteurConvoque['Acteur']['prenom'].' '. $acteurConvoque['Acteur']['nom'];

		$this->set('acteurs', $tab);
			
		if (empty($this->data)) {
			$this->set('selectedPresident', $seance['Seance']['president_id']);
			$this->set('selectedActeurs', $seance['Seance']['secretaire_id']);
		}
		else {
			$this->Seance->id = $seance_id;
			$this->Seance->saveField('president_id',$this->data['Acteur']['president_id']);
			if ($this->Seance->saveField('secretaire_id',$this->data['Acteur']['secretaire_id']))
				$this->redirect(array('action' => 'listerFuturesSeances'));
		}
	}

	function getListActeurs($seance_id, $choixListe=1) {
		$presents = array();
		$absents  = array();
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
		foreach( $presences as  $presence) {
			$acteur_id = $presence['Listepresence']['acteur_id'];
			$tot_presents = $this->Listepresence->findAll("Listepresence.acteur_id =  $acteur_id AND ($conditions) AND Listepresence.present=1");
			$nb_presence = count($tot_presents);
			if ($nb_presence == $nb_delib)
				array_push($presents, $acteur_id);
			elseif ( $nb_presence == 0) {
				$tmp = $this->Listepresence->findAll("Listepresence.acteur_id =  $acteur_id AND ($conditions) AND Listepresence.present=0 AND Listepresence.mandataire=0");
				$nb_absence=count($tmp);
				if ( $nb_absence ==  $nb_delib)
					array_push($absents, $acteur_id);
				else {
					$tmp2 = $this->Listepresence->findAll("Listepresence.acteur_id =  $acteur_id AND ($conditions) AND Listepresence.present=0 AND Listepresence.mandataire!=0");
					foreach($tmp2 as $mandat) {
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
					$mouvements[$acteur_id] =  $pres['Listepresence']['delib_id'];
				}
			}
		}
			
		if ($choixListe ==1 )
			return(array_unique($presents));
		elseif ($choixListe ==2 )
		return(array_unique($absents));
		elseif ($choixListe ==3 )
		return(array_unique($mandats));
		elseif ($choixListe ==4 )
		return(array_unique($mouvements));
	}

	function download($id=null, $file){
            
                $objCourant = $this->Seance->find('first', array(
                                                        'fields'     => array('Seance.'.$file,'Seance.'.$file.'_type'
                                                            ,'Seance.'.$file.'_size','Seance.'.$file.'_name'),
                                                        'conditions' => array('Seance.id' => $id),
                                                        'recursive'=>-1)
                                                          );
            
		header('Content-type: '.$objCourant['Seance'][$file."_type"]);
		header('Content-Length: '.$objCourant['Seance'][$file."_size"]);
		header('Content-Disposition: attachment; filename="'.$objCourant['Seance'][$file."_name"].'"');
		echo $objCourant['Seance'][$file];
		exit();
	}
//Obsolete
	function getFileType($id=null, $file) {
		$objCourant = $this->Seance->read(null, $id);
		return $objCourant['Seance'][$file."_type"];
	}
//Obsolete
	function getFileName($id=null, $file) {
		$objCourant = $this->Seance->read(null, $id);
		return $objCourant['Seance'][$file."_name"];
	}
//Obsolete
	function getSize($id=null, $file) {
		$objCourant = $this->Seance->read(null, $id);
		return $objCourant['Seance'][$file."_size"];
	}
//Obsolete
	function getData($id=null, $file) {
		$objCourant = $this->Seance->find('first', array('conditions' => array('Seance.id' => $id),
		                                                  'fields'     => array("Seance.$file")));
		return $objCourant['Seance'][$file];
	}

	function saisirCommentaire($seance_id) {
		$seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id),
		                                              'recursive'  => -1));
		$this->set('seance_id',$seance_id);
		if (empty($this->data)) {
			$this->request->data =  $seance;
		}
		else {
			$this->Seance->id=$seance_id;
			if ($this->Seance->saveField('commentaire',$this->data['Seance']['commentaire'])) {
				$this->redirect('/seances/listerFuturesSeances');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
			}
		}
	}

	function changePosition ($seance_id, $new_position, $delib_id) {
		$delib =  $this->Deliberation->Deliberationseance->find('first', array(
				'conditions'=>array('deliberation_id'=> $delib_id,
						'seance_id'      => $seance_id),
				'fields' => array('id', 'Deliberationseance.position'),
				'recursive' => '-1'));
			
		$old_position = $delib['Deliberationseance']['position'];
		if ($new_position < $old_position) {
			$delta = 1;
			$start = $new_position;
			$end   = $old_position -1;
		}
		else {
			$delta = -1;
			$start = $old_position+1;
			$end   = $new_position;
		}
		$this->Deliberation->Deliberationseance->updateAll(array('Deliberationseance.position' => "Deliberationseance.position+$delta"),
				array("Deliberationseance.position >= " => $start,
						"Deliberationseance.position <= " => $end,
						"Deliberationseance.seance_id"    => $seance_id,
						"Deliberation.etat <> "     => -1));
			
		$this->Deliberation->Deliberationseance->id = $delib['Deliberationseance']['id'];
		$this->Deliberation->Deliberationseance->saveField('position', $new_position);
			
		$this->Session->setFlash("Projet [id:$delib_id] déplacée en position : $new_position, ancienne position : $old_position ",  'growl');
		$this->redirect("/seances/afficherProjets/$seance_id");
	}

	function clore($seance_id) {
		$this->Seance->Behaviors->attach('Containable');
		$seance = $this->Seance->find('first', array(
				'conditions' => array('Seance.id' => $seance_id),
				'fields'     => array('Seance.type_id', 'Seance.date'),
				'contain'    => array('Typeseance.action', 'Typeseance.id')));
		$date_seance = strtotime($seance['Seance']['date']);
		$date_now = strtotime(date('Y-m-d H:i:s'));
		if ( $date_seance  >  $date_now) {
			$this->Session->setFlash('Vous ne pouvez pas clôturer une séance future', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/listerFuturesSeances');
		}

		$ids = $this->Seance->getDeliberationsId($seance_id);

		$actes = $this->Deliberation->find('all', array(
				'conditions' => array( 'Deliberation.id' => $ids,
						'Deliberation.etat > '   => '1',
						'Deliberation.signee'   => null),
				'fields'     => array('id'),
				'recursive'  => -1));
			
		if ((count($actes) > 0)  &&  ($seance['Typeseance']['action'] == 0)) {
			$this->Session->setFlash('Tous les actes ne sont pas signés.', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/listerFuturesSeances');
		}
		else {
			if ($this->changeStatus($seance_id))
				$this->redirect('/postseances/index');
			else {
				$this->Session->setFlash("Tous les actes n'ont pas été stockés", 'growl', array('type'=>'erreur'));
				$this->redirect('/seances/listerFuturesSeances');
			}
		}
	}

	function deleteDebatGlobal($id ){
		$this->Seance->id = $id;
		$data = array( 'id'      => $id,
				'debat_global'      => '',
				'debat_global_name' => '',
				'debat_global_size' => 0,
				'debat_global_type' => '' );
			
		if ($this->Seance->save($data, false))
			$this->redirect("/seances/SaisirDebatGlobal/$id");
		else
			die ("Suppression impossible!");
			
	}
  
        function sendConvocations ($seance_id, $model_id) {
            $this->loadModel('Acteurseance');
	        $this->Seance->Behaviors->attach('Containable');
            $seance = $this->Seance->find('first', array('conditions' => array('Seance.id'=>$seance_id),
                                                         'order'      => array('date ASC'),
                                                         'fields'     => array('id', 'date', 'type_id', 'date_convocation'),
                                                         'contain'    => array('Typeseance.libelle', 'Typeseance.action',
                                                                               'Typeseance.modelconvocation_id', 
                                                                               'Typeseance.modelordredujour_id',
                                                                               'Typeseance.modelpvsommaire_id',
                                                                               'Typeseance.modelpvdetaille_id')));
            $this->set('use_mail_securise', Configure::read('S2LOW_MAILSEC') );

            if (empty($this->data) ) {
                $acteurs = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
                foreach ($acteurs as &$acteur) {
                    $dates = $this->Acteurseance->find('first', array(
                        'conditions'=> array(
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

                $this->set('model',     $model);
                $this->set('acteurs',   $acteurs);
                $this->set('seance_id', $seance_id);
                $this->set('date_convocation', $seance['Seance']['date_convocation']);
                $this->set('model_id',  $model_id);
            }
            else {
                $message = '';
                $i=0;
                foreach ($this->data['Acteur'] as $tmp_id => $bool ){
                    $data = array(); 
                    
                    if ($bool) {
                        $i++;
                        $acteur_id = substr($tmp_id, 3, strlen($tmp_id));
                        $acteur = $this->Acteur->find('first', array('conditions' => array('Acteur.id' => $acteur_id),
                                                                     'recursive'  => -1));

                        if (file_exists(WEBROOT_PATH.DS.'files'.DS.'seances'.DS.$seance_id.DS.$model_id.DS.$acteur['Acteur']['id'].'.pdf')) {
                            $filepath = WEBROOT_PATH.DS.'files'.DS.'seances'.DS.$seance_id.DS.$model_id.DS.$acteur['Acteur']['id'].'.pdf';
                        } else if (file_exists(WEBROOT_PATH.DS.'files'.DS.'seances'.DS.$seance_id.DS.$model_id.DS.$acteur['Acteur']['id'].'.odt')) {
                            $filepath = WEBROOT_PATH.DS.'files'.DS.'seances'.DS.$seance_id.DS.$model_id.DS.$acteur['Acteur']['id'].'.odt';
                        } else {
                            $message .=  $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].' : Pas de Document'."<br />";
                            continue;
                        }
                        
                        $searchReplace = array("#NOM#" => $acteur['Acteur']['nom'], "#PRENOM#" => $acteur['Acteur']['prenom'] );
                        $template = file_get_contents(CONFIG_PATH.DS.'emails'.DS.'convocation.txt');

                        if (Configure::read('S2LOW_MAILSEC')) {
                            //S2low est encodé en iso
                            $content = utf8_decode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $template))));
                            $subject = utf8_decode('Convocation à la séance \''.$seance['Typeseance']['libelle'].'\' du : '
                                .$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date'])));

                            $data['mailto']  = $acteur['Acteur']['email'];
                            $data['objet']   = $subject;
                            $data['message'] = $content;
                            $data['uploadFile1']  = "@$filepath";
                            
                            $password=Configure::read('S2LOW_MAILSECPWD');
                            if (!empty($password))  {
                                $data['send_password'] = 1;
                                $data['password'] = $password;
                            }
                            $retour = $this->S2low->sendMail($data);
                        }
                        else {
                            $content = str_replace(array_keys($searchReplace), array_values($searchReplace), $template);
                            $subject = 'Convocation à la séance \''.$seance['Typeseance']['libelle'].'\' du : '
                                .$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));

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
                            $this->Email->to = $acteur['Acteur']['email'];
                            $this->Email->sendAs = 'text';
                            $this->Email->charset = 'UTF-8';
                            $this->Email->layout = 'default';
                            $this->Email->subject =  $subject;
                            $this->Email->attachments = array($filepath);
                            if ($this->Email->send($content))
                                $retour = 'OK:0';
                            else
                                $retour = 'KO';
                        }

                        if ( strpos($retour, 'OK:') !== false ){
                            $mail_id = substr($retour, 3, strlen($retour));
                            $this->Acteurseance->create();
                            $acteurseance['seance_id']  = $seance_id;
                            $acteurseance['acteur_id']  = $acteur_id;
                            $acteurseance['mail_id']    = $mail_id;
                            $acteurseance['date_envoi'] = date("Y-m-d H:i:s", strtotime("now"));
                            $acteurseance['model']      = 'convocation';
                            $this->Acteurseance->save( $acteurseance );
                        }
                        else {
                            $message .=  $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].' : Non envoyé'."<br />";
                        }
                        sleep(5);
                    }
                }

                if ($i==0)
                    $this->Session->setFlash('Veuillez sélectionner un acteur au minimum.', 'growl', array('type' => 'erreur'));
                elseif (!empty($message))
                    $this->Session->setFlash($message, 'growl', array('type'=>'error'));
                else
                    $this->Session->setFlash('Envoi des convocations effectué avec succès', 'growl');

                $this->redirect("/seances/sendConvocations/$seance_id/$model_id");
            }
        }
        
        function sendOrdredujour ($seance_id, $model_id) {
            $this->loadModel('Acteurseance');
	    $this->Seance->Behaviors->attach('Containable');
            $seance = $this->Seance->find('first', array('conditions' => array('Seance.id'=>$seance_id),
                                                         'order'      => array('date ASC'),
                                                         'fields'     => array('id', 'date', 'type_id', 'date_convocation'),
                                                         'contain'    => array('Typeseance.libelle', 'Typeseance.action',
                                                                               'Typeseance.modelconvocation_id', 
                                                                               'Typeseance.modelordredujour_id',
                                                                               'Typeseance.modelpvsommaire_id',
                                                                               'Typeseance.modelpvdetaille_id')));
            $this->set('use_mail_securise', Configure::read('S2LOW_MAILSEC') );
            if (empty($this->data) ) {
                $acteurs = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
                foreach ($acteurs as &$acteur) {
                     $dates = $this->Acteurseance->find('first', array( 'recursive' => -1,
                                                                        'conditions'=> array('seance_id' => $seance_id,
                                                                                             'model' => 'ordredujour',
                                                                                             'acteur_id' => $acteur['Acteur']['id']),
                                                                        'fields'    => array('date_envoi', 'date_reception')));
                    
                    $acteur['Acteur']['date_envoi'] = !empty($dates) ? $dates['Acteurseance']['date_envoi'] : null;
                    $acteur['Acteur']['date_reception'] = !empty($dates) ? $dates['Acteurseance']['date_reception'] : null;
                }
                $model   = $this->Modeltemplate->find('first', array('conditions' => array('Modeltemplate.id' => $model_id),
                                                             'fields'     => array('name'),
                                                             'recursive'  => -1));
                $this->set('model',     $model);
                $this->set('acteurs',   $acteurs);
                $this->set('seance_id', $seance_id);
                $this->set('date_convocation', $seance['Seance']['date_convocation']);
                $this->set('model_id',  $model_id);
            }
            else {
                $i=0;
                $message = '';
                foreach ($this->data['Acteur'] as $tmp_id => $bool ){
                    $data = array(); 
                    if($bool) {
                        $i++;
                        $acteur_id = substr($tmp_id, 3, strlen($tmp_id));
                        $acteur = $this->Acteur->find('first', array('conditions' => array('Acteur.id' => $acteur_id),
                                                                     'recursive'  => -1));
                        
                        if (file_exists(WEBROOT_PATH.'/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.pdf')){
                            $filepath = WEBROOT_PATH.'/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.pdf';
                        }else if (file_exists(WEBROOT_PATH.'/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.odt')){
                            $filepath = WEBROOT_PATH.'/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.odt';
                        }else{
                            $message .=  $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].' : Pas de Document'."<br />";
                            continue;
                        }
                        
                        $searchReplace = array("#NOM#" => $acteur['Acteur']['nom'], "#PRENOM#" => $acteur['Acteur']['prenom'] );
                        $template = file_get_contents(CONFIG_PATH.'/emails/convocation.txt');
                        $content = utf8_decode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $template))));
                        $subject = utf8_decode('Ordre du jour de la séance \''.$seance['Typeseance']['libelle'].'\' du : '
                                              .$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date'])));
                        if (Configure::read('S2LOW_MAILSEC')) {
                            $data['mailto']  = $acteur['Acteur']['email'];
                            $data['objet']   = $subject;
                            $data['message'] = $content;
                            $data['uploadFile1']  = "@$filepath";
                            if (Configure::read('S2LOW_MAILSECPWD') != '')  {
                                $data['send_password'] = 1;
                                $data['password'] = Configure::read('S2LOW_MAILSECPWD');
                            }
                            $retour = $this->S2low->sendMail($data);
                        }
                        else {
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
                            $this->Email->to =  $acteur['Acteur']['email'];
                            $this->Email->sendAs = 'both';
                            $this->Email->charset = 'UTF-8';
                            $this->Email->subject =  utf8_encode($subject);
			    $this->Email->layout = 'default';
                            $this->Email->attachments = array($filepath);
                            if ($this->Email->send( utf8_encode($content)) )
                                $retour = 'OK:0';
                            else
                                $retour = 'KO';
                        }

                        if ( strpos($retour, 'OK:') !== false ){
                            $mail_id = substr($retour, 3, strlen($retour));
                            $this->Acteurseance->create();
                            $acteurseance['seance_id'] = $seance_id;
                            $acteurseance['acteur_id'] = $acteur_id;
                            $acteurseance['mail_id']   = $mail_id;
                            $acteurseance['date_envoi']   = date("Y-m-d H:i:s", strtotime("now"));
                            $acteurseance['model']   = 'ordredujour';
                            $this->Acteurseance->save( $acteurseance );
                        }
                        else {
                            $message .=  $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].' : Non envoyé'."<br />";
                        }
                        sleep(5);
                    }
                }
               if($i==0) {
                    $this->Session->setFlash('Veuillez sélectionner un acteur au minimum.', 'growl', array('type' => 'erreur'));
                }
                elseif (!empty($message)) 
                    $this->Session->setFlash($message, 'growl', array('type'=>'error'));
                
                $this->redirect("/seances/sendOrdredujour/$seance_id/$model_id");
            }
        }

        /**
         * Génération de la convocation ou de l'odre du jour
         * @param $seance_id
         * @param $model_id
         */
        function _generer($seance_id, $model_id, $url_retour) { 

            $this->Seance->id = $seance_id;
            $time_start = microtime(true);
            $annexes_id = array();

            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');
  
            $this->Progress->start(200, 100,200, '#FFCC00','#006699');
           
            if (($this->Session->read('user.format.sortie')==1)) {
                $sMimeType = "application/vnd.oasis.opendocument.text";
                $format    = "odt";
            
            } else {
                $sMimeType = "application/pdf";
                $format    = "pdf";
            }

            //*****************************************
            // Préparation des répertoires pour la création des fichiers
            //*****************************************
            $dirpath = WEBROOT_PATH."/files/seances/$seance_id/$model_id/";
            
            //Suppression des fichiers éventuellement existants
            $dir = glob($dirpath."*");
            foreach ($dir as $fileuri) {
                if (is_file($fileuri)) unlink($fileuri);
            }

            if (!$this->Gedooo->checkPath($dirpath))
                die("Webdelib ne peut pas ecrire dans le repertoire : $dirpath");
            
            $this->Progress->at(5, 'Début de préparation du document');

            //*****************************************
            //Création du model ott
            //*****************************************
            $model = $this->Modeltemplate->find('first', array(
                                        'conditions'=> array('Modeltemplate.id'=> $model_id),
                                        'recursive' => '-1',
                                        'fields'    => array('content', 'name')));
            $oTemplate = new GDO_ContentType("",
                                             $model['Modeltemplate']['name'],
                                             "application/vnd.oasis.opendocument.text",
                                             "binary",
                                             $model['Modeltemplate']['content']);

            $oMainPart = new GDO_PartType();

            $oMainPart->addElement(new GDO_FieldType('date_jour_courant',utf8_encode($this->Date->frenchDate(strtotime("now"))), 'text'));
            $oMainPart->addElement(new GDO_FieldType('date_du_jour', date("d/m/Y", strtotime("now")), 'date'));

            // Informations sur la collectivité
            $this->Collectivite->makeBalise($oMainPart, 1);

            $blocProjets = new GDO_IterationType("Projets");
            
            $projets  =  $this->Seance->getDeliberations($seance_id);
            
            foreach ($projets as $projet) {
                $oDevPart = new GDO_PartType();
                $this->Deliberation->makeBalisesProjet($projet, $oDevPart);
                $blocProjets->addPart($oDevPart);

                $annexes = array();
                $tmp_annexes = $this->Deliberation->Annex->getAnnexesFromDelibId($projet['Deliberation']['id'], false, true);
                if (!empty($tmp_annexes))
                    array_push($annexes_id,  $tmp_annexes);

            }
            $path_annexes = $dirpath.'annexes/';
            foreach ($annexes_id as $annex_ids) {
                foreach($annex_ids as $annex_id) {
                    $annexFile = $this->Deliberation->Annex->find('first', array('conditions' => array('Annex.id' => $annex_id['Annex']['id']),
                                                                                 'recursive'  => -1));
                    if ($annexFile['Annex']['filetype'] == 'application/pdf')
                        $datAnnex =  $annexFile['Annex']['data_pdf'];
                    elseif ($annexFile['Annex']['filetype'] == 'application/vnd.oasis.opendocument.text')
                        $datAnnex =  $annexFile['Annex']['data_pdf'];
                    elseif ($annexFile['Annex']['filetype'] == 'application/vnd.oasis.opendocument.spreadsheet')
                        $datAnnex =  $annexFile['Annex']['data_pdf'];

                    $fichierAnnex = $this->Gedooo->createFile($path_annexes, "annex_". $annexFile['Annex']['id'].'.pdf', $datAnnex);
                    array_push($annexes, $fichierAnnex);
                }
            }
            $this->Progress->at(20, 'Fin de préparation du document');
            $oMainPart->addElement($blocProjets);
            $this->Seance->makeBalise($seance_id, $oMainPart);
            $this->Seance->saveField('date_convocation',  date("Y-m-d H:i:s", strtotime("now")));   
            $typeseance_id = $this->Seance->getType($seance_id);
            $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($typeseance_id);
            $cpt =2;
            $nbActeurs = count($acteursConvoques)+3;
            foreach ($acteursConvoques as $acteur) {
    
                $cpt++;
                $this->Progress->at($cpt*(100/$nbActeurs), 'Génération du document : '.$acteur['Acteur']['nom']." ".$acteur['Acteur']['prenom']);
                
                $oMainPart->addElement(new GDO_FieldType("nom_acteur", ($acteur['Acteur']['nom']), "text"));
                $oMainPart->addElement(new GDO_FieldType("prenom_acteur", ($acteur['Acteur']['prenom']), "text"));
                $oMainPart->addElement(new GDO_FieldType("salutation_acteur",($acteur['Acteur']['salutation']), "text"));
                $oMainPart->addElement(new GDO_FieldType("titre_acteur", ($acteur['Acteur']['titre']), "text"));
                $oMainPart->addElement(new GDO_FieldType("date_naissance_acteur", ($acteur['Acteur']['date_naissance']), "text"));
                $oMainPart->addElement(new GDO_FieldType("adresse1_acteur", ($acteur['Acteur']['adresse1']), "text"));
                $oMainPart->addElement(new GDO_FieldType("adresse2_acteur", ($acteur['Acteur']['adresse2']), "text"));
                $oMainPart->addElement(new GDO_FieldType("cp_acteur", ($acteur['Acteur']['cp']), "text"));
                $oMainPart->addElement(new GDO_FieldType("ville_acteur", ($acteur['Acteur']['ville']), "text"));
                $oMainPart->addElement(new GDO_FieldType("email_acteur", ($acteur['Acteur']['email']), "text"));
                $oMainPart->addElement(new GDO_FieldType("telfixe_acteur",($acteur['Acteur']['telfixe']), "text"));
                $oMainPart->addElement(new GDO_FieldType("note_acteur", ($acteur['Acteur']['note']), "text"));

                $nomFichier = $acteur['Acteur']['id'];

                try {
                    Configure::write('debug', 0);
                    error_reporting(0);

                    $time_end = microtime(true);
                    $time = $time_end - $time_start;
                    $this->log("Temps création de requete :". $time );

                    $time_start = microtime(true);
                    $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
                    $oFusion->process();
                    $time_end = microtime(true);
                    $time = $time_end - $time_start;
                    $this->log("Temps création de fusion : ". $time );

                    $time_start = microtime(true);
                    $oFusion->SendContentToFile($dirpath.$nomFichier.".".$format);

                    $this->Conversion->convertirFichier($dirpath.$nomFichier.".odt", 'odt', $format);

                    $time_end = microtime(true);
                    $time = $time_end - $time_start;
                    $this->log("Temps conversion et concaténation : ". $time );
                }
                catch (Exception $e) {
                    $this->cakeError('gedooo', array('error'=>$e, 'url'=> $this->Session->read('user.User.lasturl')));
                }
            }
            $this->Progress->at(100, 'Fin de Génération des documents');
            sleep(1);
            $this->Progress->end($url_retour);
        }
        
        function genererConvocation($seance_id, $model_id) { 
            $this->_generer($seance_id,$model_id ,"/seances/sendConvocations/$seance_id/$model_id");
            exit;
        }
        
                
        function genererOrdredujour($seance_id, $model_id) {
            $this->_generer($seance_id,$model_id, "/seances/sendOrdredujour/$seance_id/$model_id");
            exit;
        }



        function recuperer_zip($seance_id, $model_id) {
            $dirpath = WEBROOT_PATH.DS.'files'.DS.'seances'.DS.$seance_id.DS.$model_id;
            if (file_exists($dirpath.DS.'convocations.zip')) 
                unlink($dirpath.DS.'convocations.zip');

            $dir = new Folder($dirpath);
            $zip = new ZipArchive;
            
            $files = $dir->find('.*\.pdf');
            try{
                if($zip->open($dirpath.DS.'convocations.zip', ZIPARCHIVE::CREATE))
                foreach ($files as $file) {
                    $file = new File($dir->pwd() . DS . $file);
                    $acteur_id = $file->name();
                    $acteur = $this->Acteur->find('first', array('conditions' => array('Acteur.id' => $acteur_id), 
                                                                 'recursive'  => -1,
                                                                 'fields'     => array('Acteur.nom')));
                    $zip->addFile($file->path, $acteur['Acteur']['nom'].'.pdf');
                    $file->close();
                }
                $zip->close();
            }
            catch(Exception $e) {
                 $this->Session->setFlash('Une erreur est survenu lors de la génération de l\'archive', 'growl');
            }
            
            $content = file_get_contents($dirpath.DS.'convocations.zip');
            header('Content-type: application/zip');
            header('Content-Length: '.strlen($content));
            header('Content-Disposition: attachment; filename="Convocation.zip"');
            die ($content);
        }

        function multiodj () {
            $this->Progress->start(200, 100,200, '#FFCC00','#006699');
            $this->Progress->at(0, 'Démarrage de la génération');
            Configure::write('debug', 0);
            $model_id = $this->data['Seance']['model_id'];
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
            include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');
            $format =  $this->Session->read('user.format.sortie');
            $dyn_path = "/files/generee/seances/";
            $nomFichier = "odj";
            $path = WEBROOT_PATH.$dyn_path;
            if (!file_exists($path))
                    mkdir($path);

            if (empty($format)) $format =0;

            if ($format == 0) {
                $sMimeType = "application/pdf";
                $format    = "pdf";
            }
            elseif ($format ==1) {
                $sMimeType = "application/vnd.oasis.opendocument.text";
                $format    = "odt";
            }
            $this->Progress->at(5, 'Recherche en base de données');
            $content = $this->Typeseance->Modelprojet->find('first', array('conditions' => array('id' => $model_id),
                                                                           'fields'     => array('content'),
                                                                           'recursive'  => -1));
            $oTemplate = new GDO_ContentType("",
                                             "modele.odt",
                                             "application/vnd.oasis.opendocument.text",
                                             "binary",
                                             $content['Modelprojet']['content']);
            $oMainPart = new GDO_PartType();

            $seances = new GDO_IterationType("Seances");
            $cpt=1;
            foreach ($this->data['Seance'] as $id => $bool ) {
                $this->Progress->at(5+$cpt*(50/count($this->data['Seance'])), 'Envoi les informations des séances à Gedooo...');
                if ($bool == 1) {
                    $seance_id = substr($id, 3, strlen($id));
                    $seances->addPart($this->Seance->makeBalise($seance_id, null, true, array('Deliberation.etat >=' => 0)));
                }
                $cpt++;
            }
            $oMainPart->addElement($seances);
            $this->Progress->at(60, 'Fusion en cours...');
            $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
            $oFusion->process();
            $oFusion->SendContentToFile($path.$nomFichier.".odt");
            $this->Progress->at(80, 'Conversion du fichier au bon format...');
            $content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", 'odt', $format);
            $this->Gedooo->createFile($path,  $nomFichier.'.'.$format, $content);
            $this->Progress->at(100, 'Chargement des résultats...');
            $listFiles[FULL_BASE_URL.$dyn_path.$nomFichier] = 'Document généré';
            $this->Session->write('user.User.lasturl', '/seances/listerFuturesSeances');
            $this->Session->write('tmp.listFiles', $listFiles);
            $this->Session->write('tmp.format', $format);
            $this->Progress->end('/models/getGeneration');
        }

    function sendToIdelibre($seance_id) {
        if (!(Configure::read('USE_IDELIBRE'))){
            $this->Session->setFlash('Le connecteur Idélibre n&apos;est pas activé.<br>Veuillez contacter l&apos;administrateur pour plus d&apos;infos.', 'growl');
            $this->redirect(array('controller' => 'seances', 'action' => 'listerFuturesSeances'));
        }

        $this->Progress->start(200, 100,200, '#FFCC00','#006699');
        $this->Progress->at(0, 'Initialisation');
        
        $this->Seance->Behaviors->attach('Containable');
        $this->Deliberation->Behaviors->attach('Containable');

        $this->Progress->at(1, 'Récupération des données de la séance...');

        $seance = $this->Seance->find('first', array(
            'conditions' => array('Seance.id' => $seance_id),
            'fields'     => array('id', 'date', 'type_id'),
            'contain'    => array('Typeseance.libelle', 'Typeseance.action', 'Typeseance.id', 'Typeseance.modelconvocation_id', 'Typeseance.action')));

        $acteurs_convoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Typeseance']['id']);

        $model_seance_id =  $seance['Typeseance']['modelconvocation_id'];

        $this->Progress->at(5, "Génération de l'ordre du jour de la séance...");

        $this->requestAction("/models/generer/null/$seance_id/$model_seance_id/0/0/Document/0/1");
        $filename = WEBROOT_PATH."/files/generee/fd/$seance_id/null/Document.pdf";

        $data = array(
            'username' => Configure::read('IDELIBRE_LOGIN'),
            'password' => Configure::read('IDELIBRE_PWD'),
            'conn' => Configure::read('IDELIBRE_CONN'),
            'convocation' => "@$filename"
        );

        $jsonData = array(
            'date_seance'       => $seance['Seance']['date'],
            'type_seance'       => $seance['Typeseance']['libelle'],
            'acteurs_convoques' => json_encode($acteurs_convoques),
        );

        $this->Progress->at(10, 'Récupération des délibérations de la séance...');
        $i = 0;
        $delibs = $this->Seance->getDeliberationsId($seance_id, array('Deliberation.etat >' =>0 ));
        $num_delib = count($delibs );
        foreach ($delibs as $delib_id) {
            $projet = array();
            $this->Progress->at(10+($i+1)*(50/$num_delib), 'Génération du projet '.($i+1).'/'.$num_delib.'...');
            $delib = $this->Deliberation->find('first', array(
                'conditions' => array('Deliberation.id' => $delib_id),
                'contain'    => array('Theme.libelle'),
                'fields'     => array(
                    'Deliberation.objet',
                    'Deliberation.typeacte_id',
                    'Deliberation.theme_id',
                    'Deliberation.etat'
                )));

            if ($seance['Typeseance']['action'] == 0) {
                $model_id = $this->Typeseance->modeleProjetDelibParTypeSeanceId($seance['Seance']['type_id'], $delib['Deliberation']['etat']);
            }
            else {
                $model_id = $this->Deliberation->Typeacte->getModelId($delib['Deliberation']['typeacte_id'], 'modeleprojet_id');
            }

            $this->requestAction("/models/generer/$delib_id/null/$model_id/0/2/P_$delib_id");

            $projet_filename = WEBROOT_PATH."/files/generee/fd/null/$delib_id/P_$delib_id.pdf";
            $projet['libelle'] = $delib['Deliberation']['objet'];
            $projet['ordre'] = $i;
            //TODO --- POUR Stéphance + ardoressence getLibelleParent
            $projet['theme'] = implode(',', $this->Deliberation->Theme->getLibelleParent($delib['Deliberation']['theme_id']));
            $data['projet_'.$i.'_rapport'] = "@$projet_filename";

            $j=0;
            $points = array('.', '..');
            $annexes = array();
            if (is_dir(WEBROOT_PATH."/files/generee/fd/null/$delib_id/annexes/")) {
                if ($dh = opendir(WEBROOT_PATH."/files/generee/fd/null/$delib_id/annexes/")) {
                    while (($file = readdir($dh)) !== false) {
                        if (!in_array($file, $points)) {
                            $annex_filename =  WEBROOT_PATH."/files/generee/fd/null/$delib_id/annexes/".$file;
                            $data['projet_'.$i.'_'.$j.'_annexe']  = "@$annex_filename";
                            $annexes[] = array(
                                'libelle' => $file,
                                'ordre' => $j
                            );
                            $j++;
                        }
                    }
                    closedir($dh);
                }
            }
            $projet['annexes'] = $annexes;
            $jsonData['projets'][] = $projet;
            $i++;
        }
        $data['jsonData'] = json_encode($jsonData);
        $this->Progress->at(85, 'Envoi des informations à i-DelibRE...');
        $url = Configure::read('IDELIBRE_HOST').'/seances.json';

        $request = curl_init();
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
        // TODO : implémenter l'utilisation de certificat
//        curl_setopt($request, CURLOPT_CAINFO, getcwd() . Configure::read('IDELIBRE_CERT'));
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $data);
        $success = curl_exec($request);
        $this->log(print_r($success,true), 'debug');
        $this->log(print_r($data,true), 'debug');
        curl_close($request);
        if (!$success)
            $this->Session->setFlash('Une erreur est survenue lors de l&apos;envoi à i-delibRE.', 'growl', array('error'));
        else
            $this->Session->setFlash('Convocations envoyés avec succès à i-delibRE.', 'growl');
        $this->Progress->end('/seances/listerFuturesSeances');
        return $this->redirect(array('controller'=>'seances', 'action'=>'listerFuturesSeances'));
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
                throw new Exception('le type de modèle d\'édition '.$typeFusion.' n\'est par autorisé');

            // fusion du document
            $this->Seance->Behaviors->load('OdtFusion', array('id'=>$id, 'modelOptions'=>array('modelTypeName'=>$typeFusion)));
            $filename = $this->Seance->fusionName();
            $this->Seance->odtFusion();

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
            $this->Session->write('Generer.downloadToken', $cookieToken, false, 3600);
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

    /**
     * fonction de génération des convocations des acteurs convoqués à une séance et stockage sur file system
     * @param integer $id id de la séance
     * @param integer $modelTemplateId id du template de fusion
     * @param string $typeFusion type de la fusion à générer : conv, adj, ...
     * @param integer $cookieToken numéro de cookie du client pour masquer la fenêtre attendable
     * @return CakeResponse
     */
    function genereFusionToFiles($id, $modelTemplateId, $typeFusion, $cookieToken) {
        try {
            // vérification de l'existence de la séance
            if (!$this->Seance->hasAny(array('id' => $id)))
                throw new Exception('Séance id:' . $id . ' non trouvée en base de données');

            // vérification des types de fusion
            $allowedFusionTypes = array('convocation', 'ordredujour');
            if (!in_array($typeFusion, $allowedFusionTypes))
                throw new Exception('le type de modèle d\'édition '.$typeFusion.' n\'est par autorisé');

            // lecture de la liste des acteurs convoqués
            $typeSeanceId = $this->Seance->field('type_id', array('id'=>$id));
            $convoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($typeSeanceId, null, array('id'));
            if (empty($convoques))
                throw new Exception('Aucun acteur convoqué pour la séance id:'.$id);

            // format de conversion
            $formatConversion = $this->Session->read('user.format.sortie')==0?'pdf':'odt';

            // initialisation du répertoire de destination des convocations
            App::import('Lib', 'AppGestfichiers');
            $dirpath = AppGestfichiers::formatDirName(WEBROOT_PATH, 'files', 'seances', $id, $modelTemplateId);
            if (is_dir($dirpath)) AppGestfichiers::clearDir($dirpath);
            AppGestfichiers::creeRepertoire($dirpath);

            // chargement  du behavior de fusion du document
            $this->Seance->Behaviors->load('OdtFusion', array('id'=>$id, 'modelOptions'=>array('modelTypeName'=>$typeFusion)));

            // le modèle template possede-t-il des variables de fusion des acteurs
            $acteurPresentTemplate = $this->Seance->modelTemplateOdtInfos->hasUserFields(array(
                'salutation_acteur', 'prenom_acteur', 'nom_acteur', 'titre_acteur', 'position_acteur',
                'email_acteur', 'telmobile_acteur', 'telfixe_acteur',
                'date_naissance_acteur', 'adresse1_acteur', 'adresse2_acteur', 'cp_acteur', 'ville_acteur', 'note_acteur'));

            // traitement différent en fonction de la présence de variables acteur dans le template
            if ($acteurPresentTemplate) {
                foreach($convoques as $acteur) {
                    $filename = $acteur['Acteur']['id'].'.'.$formatConversion;
                    $this->Seance->odtFusion(array('modelOptions'=>array('acteurId'=>$acteur['Acteur']['id'])));
                    $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', $formatConversion);
                    unset($this->Seance->odtFusionResult);
                    file_put_contents($dirpath.$filename, $content);
                    unset($content);
                }
            } else {
                $this->Seance->odtFusion();
                $content = $this->Conversion->convertirFlux($this->Seance->odtFusionResult->content->binary, 'odt', $formatConversion);
                unset($this->Seance->odtFusionResult);
                foreach($convoques as $acteur) {
                    $filename = $acteur['Acteur']['id'].'.'.$formatConversion;
                    file_put_contents($dirpath.$filename, $content);
                }
                unset($content);
            }

            // mise à jour de la date de génération des convocations
            $this->Seance->save(array('id'=>$id, 'date_convocation'=>date("Y-m-d H:i:s", strtotime("now"))), false);
        } catch (Exception $e) {
            $this->Session->setFlash('erreur lors de la génération du document : ' . $e->getMessage(), 'growl', array('type' => 'erreur'));
        }
        $this->redirect($this->referer());
    }

    /**
     * génération de la fusion pour plusieurs séances : l'id du modèle de fusion et les séances a fusionner sont passés dans les données du formulaire
     * @param integer $cookieToken numéro de cookie du client pour masquer la fenêtre attendable
     * @return CakeResponse
     */
    function genereFusionMultiSeancesToClient($cookieToken = null) {
        try {
            // initialisation de l'id du modèle de fusion
            $modelTemplateId = $this->request->data['Seance']['model_id'];
            unset($this->request->data['Seance']['model_id']);

            // initialisation de la liste des séances sélectionnées
            $seancesIds = array();
            foreach($this->request->data['Seance'] as $seanceId=>$selected)
                if ($selected) {
                    $seanceId = explode('_', $seanceId);
                    $seancesIds[] = $seanceId[1];
                }
            if (empty($seancesIds))
                throw new Exception('aucune séance sélectionnée');

            // fusion du document
            $this->Seance->Behaviors->load('OdtFusion', array('modelTemplateId'=>$modelTemplateId, 'modelOptions'=>array('modelTypeName'=>'multiseances')));
            $filename = $this->Seance->fusionName();
            $this->Seance->odtFusion(array('modelOptions'=>array('seanceIds'=>$seancesIds)));

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
            $this->Session->write('Generer.downloadToken', $cookieToken, false, 3600);
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
}
