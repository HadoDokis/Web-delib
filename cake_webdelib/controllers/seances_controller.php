<?php
class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2');
	var $components = array('Date','Email', 'Gedooo');
	var $uses = array('Deliberation', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'Model', 'Annex', 'Typeseance', 'Acteur');
	var $cacheAction = 0;

	// Gestion des droits
	var $demandeDroit = array('listerFuturesSeances', 'add', 'afficherCalendrier');
	var $commeDroit = array(
		'index'=>'Seances:listerFuturesSeances',
		'view'=>'Seances:listerFuturesSeances',
		'delete'=>'Seances:listerFuturesSeances',
		'edit'=>'Seances:listerFuturesSeances',
		'afficherProjets'=>'Seances:listerFuturesSeances',
		'addListUsers'=>'Seances:listerFuturesSeances',
		'saisirDebatGlobal'=>'Seances:listerFuturesSeances',
		'details'=>'Seances:listerFuturesSeances',
		'saisirDebat'=>'Seances:listerFuturesSeances',
		'voter'=>'Seances:listerFuturesSeances',
		'changeRapporteur'=>'Seances:listerFuturesSeances',
		'changeStatus'=>'Seances:listerFuturesSeances',
		'detailsAvis'=>'Seances:listerFuturesSeances',
		'donnerAvis'=>'Seances:listerFuturesSeances',
		'saisirSecretaire'=>'Seances:listerFuturesSeances',
		'getListActeurs'=>'Seances:listerFuturesSeances'
	);

	function index() {
		$this->Seance->recursive = 0;
		$seances = $this->Seance->findAll(null,null,'date asc');
		for ($i=0; $i<count($seances); $i++)
		    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

		$this->set('seances', $seances);
	}


	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la seance.');
			$this->redirect('/seances/index');
		}
		$this->set('seance', $this->Seance->read(null, $id));
	}

	function add($timestamp=null) {
		if (empty($this->data)) {
			if (isset($timestamp))
			    $this->set('date', date('d/m/Y',$timestamp));

			$this->set('typeseances', $this->Seance->Typeseance->generateList());
			$this->set('selectedTypeseances', null);
			$this->render();
		} else {
			$this->cleanUpFields('Seance');
			$this->data['Seance']['date']=  $this->Utils->FrDateToUkDate($this->params['form']['date']);
			$this->data['Seance']['date'] = $this->data['Seance']['date'].' '.$this->data['Seance']['date_hour'].':'.$this->data['Seance']['date_min'];

			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/seances/listerFuturesSeances');
			} else {
				$this->Session->setFlash('Corrigez les erreurs ci-dessous.');
				$this->set('typeseances', $this->Seance->Typeseance->generateList());
				if (empty($this->data['Typeseance']['Typeseance'])) {
					$this->data['Typeseance']['Typeseance'] = null;
				}
				$this->set('selectedTypeseances', $this->data['Typeseance']['Typeseance']);
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la seance');
				$this->redirect('/seances/index');
			}
			$this->data = $this->Seance->read(null, $id);
			$this->set('typeseances', $this->Seance->Typeseance->generateList());
			if (empty($this->data['Typeseance'])) { $this->data['Typeseance'] = null; }
				$this->set('selectedTypeseances', $this->_selectedArray($this->data['Typeseance']));
		} else {
			$this->cleanUpFields('Seance');
			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/seances/index');
			} else {
				$this->Session->setFlash('Corrigez les erreurs ci-dessous.');
				if (empty($this->data['Typeseance']['Typeseance'])) { $this->data['Typeseance']['Typeseance'] = null; }
					$this->set('selectedTypeseances', $this->data['Typeseance']['Typeseance']);
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la seance');
			$this->redirect('/seances/index');
		}
		if ($this->Seance->del($id)) {
			$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; suprim&eacute;e');
			$this->redirect('/seances/index');
		}
	}

	function listerFuturesSeances() {
            $this->set('USE_GEDOOO', USE_GEDOOO);
            if (empty ($this->data)) {
                $condition= 'Seance.traitee = 0';
	        $seances = $this->Seance->findAll(($condition),null,'date asc');

	 	for ($i=0; $i<count($seances); $i++){
		    $seances[$i]['Seance']['dateEn'] =  $seances[$i]['Seance']['date'];
		    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));
	       }
                $this->set('seances', $seances);
	    }
	}

	function listerAnciennesSeances() {
			if (empty ($this->data)) {
			//$condition= 'date <= "'.date('Y-m-d H:i:s').'"';
			$condition= 'Seance.traitee = 1';
			$seances = $this->Seance->findAll(($condition),null,'date asc');

			for ($i=0; $i<count($seances); $i++)
			    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

			$this->set('seances', $seances);
		}
	}

	function changeStatus ($seance_id) {
		$this->data=$this->Seance->read(null,$seance_id);
		$this->data['Seance']['traitee']=1;
		if ($this->Seance->save($this->data))
			$this->redirect('/seances/listerFuturesSeances');
	}

	function afficherCalendrier ($annee=null){

		vendor('Calendar/includeCalendarVendor');

		define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS);

		if (!isset($annee))
		     $annee = date('Y');

 		$tabJoursSeances = array();
 		$fields = 'date';
        $condition = "annee = $annee";
        $joursSeance = $this->Seance->findAll(null, $fields);
        foreach ($joursSeance as $date) {
        	$date = strtotime(substr($date['Seance']['date'], 0, 10));
        	array_push($tabJoursSeances,  $date);
        }

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
		        }
		        else {
					$timestamp = $Day->thisDay('timestamp');
		            if ($today == $Day->thisDay('timestamp')){
		                 $balise="today";
		            }
		            elseif (in_array ($Day->thisDay('timestamp'), $tabJoursSeances) )
		            {
		            	$balise="seance";
		            }
		            else {
		            	$balise="normal";
		            }
		            $calendrier .=  "<td><a href =\"add/$timestamp\"><p class=\"$balise\">".$Day->thisDay()."</p></a></td>\n" ;
		        }
		        if ( $Day->isLast() ) {
		           $calendrier .=  "</tr>\n" ;
		        }
			}

     		$calendrier .= "</table>\n</td>\n" ;

	    	if ($i==5)
	        	$calendrier .= "</tr><tr   style=\"vertical-align:top;\">\n" ;

	    	$i++;
		}
		$calendrier .=  "</tr>\n</table>\n" ;

		$this->set('annee', $annee);
		$this->set('calendrier',$calendrier);
	}

	function afficherProjets ($id=null, $return=null)
	{
		$condition= "seance_id=$id AND (etat != -1 )";
		if (!isset($return)) {
		    $this->set('lastPosition', $this->requestAction("deliberations/getLastPosition/$id") - 1 );
			$deliberations = $this->Deliberation->findAll($condition,null,'Deliberation.position ASC');
			for ($i=0; $i<count($deliberations); $i++) {
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
				$deliberations[$i]['rapp_id'] = $this->requestAction("deliberations/getRapporteur/".$deliberations[$i]['Deliberation']['id']);
			}
			$this->set('seance_id', $id);
			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus());
			$this->set('projets', $deliberations);
			$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->GetDate($id))));
		}
		else
		    return ($this->Deliberation->findAll($condition,null,'Deliberation.position ASC'));
	}

    function changeRapporteur($newRapporteur,$delib_id) {
    	$this->Deliberation->create();
    	$this->data['Deliberation']['id']=$delib_id;
    	$this->data['Deliberation']['rapporteur_id']= $newRapporteur;
		if ($this->Deliberation->save($this->data['Deliberation'])){
    		//redirection sur la page où on était avant de changer de service
       		$this->Redirect($this->Session->read('user.User.lasturl'));
       	}
    }

    function getDate($id=null)
    {
		if (empty($id))
			return '';
		else{
		$condition = "Seance.id = $id";
        $objCourant = $this->Seance->findAll($condition);
		return $objCourant['0']['Seance']['date'];}
    }

    function getType($id)
    {
		$condition = "Seance.id = $id";
        return $this->Seance->findAll($condition);
    }

	function sendConvoc($user_id,  $convoc_path, $type_seance, $date_seance) {
		$condition = "User.id = $user_id";
		$data = $this->User->findAll($condition);
		// Si l'utilisateur accepte les mails
		if ($data['0']['User']['accept_notif']){
			$to_mail = $data['0']['User']['email'];
			$to_nom = $data['0']['User']['nom'];
			$to_prenom = $data['0']['User']['prenom'];

			$this->Email->template = 'email/convoquer';
			$text = "Convocation";
            $this->set('data', utf8_encode( "Vous venez de recevoir une convocation au  $type_seance du $date_seance"));
            $this->Email->to = $to_mail;
            $this->Email->subject = utf8_encode("Convocation au $type_seance du $date_seance");
       	    $this->Email->attach($convoc_path, 'convocation.pdf');
            $result = $this->Email->send();
		}
	}

	function delUserFromList($user_id, $seance_id) {
		$data = $this->Listepresence->findAll("seance_id = $seance_id AND user_id = $user_id");
		$this->Listepresence->del($data[0]['Listepresence']['id']);
	}

	function addUserFromList($user_id, $seance_id) {
		$this->params['data']['Listepresence']['id']='';
		$this->params['data']['Listepresence']['seance_id'] = $seance_id;
		$this->params['data']['Listepresence']['user_id'] = $user_id ;
		$this->Listepresence->save($this->params['data']);
	}

	function isInList($user_id, $listInscrits){
		$isIn = false;
		foreach ($listInscrits as $inscrit)
			if ($inscrit['User']['id'] == $user_id){
			   	//echo($inscrit['User']['nom']." est dans la liste <br>");
			    return true;
			}
	     return $isIn;
	}

	function details ($seance_id=null) {
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$deliberations=$this->afficherProjets($seance_id, 0);
		$ToutesVotees = true;
		for ($i=0; $i<count($deliberations); $i++){
                    $id_service = $deliberations[$i]['Service']['id'];
		    $deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
		    $deliberations[$i]['Model']['id'] = $this->requestAction("deliberations/getModelId/". $deliberations[$i]['Deliberation']['id']);
                    if (($deliberations[$i]['Deliberation']['etat']!=3)AND($deliberations[$i]['Deliberation']['etat']!=4))
                        $ToutesVotees = false;
		}
		$this->set('deliberations',$deliberations);
		$date_tmpstp = strtotime($this->GetDate($seance_id));
		$this->set('date_tmpstp', $date_tmpstp);
		$this->set('date_seance', $this->Date->frenchDateConvocation($date_tmpstp));
		$this->set('seance_id', $seance_id);
		$this->set('canClose', $ToutesVotees);
	}

	function effacerVote($deliberation_id=null) {
		$condition = "delib_id = $deliberation_id";
		$votes = $this->Vote->findAll($condition);
		foreach($votes as $vote)
  		    $this->Vote->del($vote['Vote']['id']);
	}

	function voter($deliberation_id=null) {
		$this->Deliberation->recursive = -1;
		$deliberation = $this->Deliberation->read(null, $deliberation_id);
		$seance = $this->Seance->read(null, $deliberation['Deliberation']['seance_id']);

		if (empty($this->data)) {
			// Initialisation du d�tail du vote
			$donnees = $this->Vote->findAll("delib_id = $deliberation_id");
			foreach($donnees as $donnee){
				$this->data['detailVote'][$donnee['Vote']['acteur_id']]=$donnee['Vote']['resultat'];
			}
			// Initialisation du total des voix
			$this->data['Deliberation']['vote_nb_oui'] = $deliberation['Deliberation']['vote_nb_oui'];
			$this->data['Deliberation']['vote_nb_non'] = $deliberation['Deliberation']['vote_nb_non'];
			$this->data['Deliberation']['vote_nb_abstention'] = $deliberation['Deliberation']['vote_nb_abstention'];
			$this->data['Deliberation']['vote_nb_retrait'] = $deliberation['Deliberation']['vote_nb_retrait'];
			// Initialisation du resultat
			$this->data['Deliberation']['etat'] = $deliberation['Deliberation']['etat'];
			// Initialisation du commentaire
			$this->data['Deliberation']['vote_commentaire'] = $deliberation['Deliberation']['vote_commentaire'];

			$this->set('deliberation' , $deliberation);
			$this->set('presents' , $this->requestAction('/deliberations/afficherListePresents/'.$deliberation_id));
		} else {
			$this->data['Deliberation']['id'] = $deliberation_id;
			$this->effacerVote($deliberation_id);
			switch ($this->data['Vote']['typeVote']) {
			case 1:
				// Saisie du d�tail du vote
				$this->data['Deliberation']['vote_nb_oui'] = 0;
				$this->data['Deliberation']['vote_nb_non'] = 0;
				$this->data['Deliberation']['vote_nb_abstention'] = 0;
				$this->data['Deliberation']['vote_nb_retrait'] = 0;
				foreach($this->data['detailVote']as $acteur_id => $vote){
					$this->Vote->create();
					$this->data['Vote']['acteur_id']=$acteur_id;
					$this->data['Vote']['delib_id']=$deliberation_id;
					$this->data['Vote']['resultat']=$vote;
			    	$this->Vote->save($this->data['Vote']);
					if ($vote == 3)
						$this->data['Deliberation']['vote_nb_oui']++;
					elseif ($vote == 2)
						$this->data['Deliberation']['vote_nb_non']++;
					elseif ($vote == 4)
						$this->data['Deliberation']['vote_nb_abstention']++;
					elseif ($vote == 5)
						$this->data['Deliberation']['vote_nb_retrait']++;
				}
				if ($this->data['Deliberation']['vote_nb_oui']>$this->data['Deliberation']['vote_nb_non'])
					$this->data['Deliberation']['etat'] = 3;
				else
					$this->data['Deliberation']['etat'] = 4;
    			break;
			case 2:
				// Saisie du total du vote
				if ($this->data['Deliberation']['vote_nb_oui']>$this->data['Deliberation']['vote_nb_non'])
					$this->data['Deliberation']['etat'] = 3;
				else
					$this->data['Deliberation']['etat'] = 4;
    			break;
			case 3:
				// Saisie du resultat global
				$this->data['Deliberation']['vote_nb_oui'] = 0;
				$this->data['Deliberation']['vote_nb_non'] = 0;
				$this->data['Deliberation']['vote_nb_abstention'] = 0;
				$this->data['Deliberation']['vote_nb_retrait'] = 0;
			    break;
			}

		    // Attribution du num�ro de la d�lib�ration si adopt�e et si pas d�j� attribu�
			if ( ($this->data['Deliberation']['etat'] == 3)
				&& empty($deliberation['Deliberation']['num_delib']) )
				$this->data['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($seance['Typeseance']['compteur_id']);

			$this->Deliberation->save($this->data);
			$this->redirect('seances/details/'.$deliberation['Deliberation']['seance_id']);
		}
	}


	function saisirDebat ($id = null)	{
            $seance_id = $this->requestAction('/deliberations/getCurrentSeance/'.$id);
            $seance = $this->Seance->read(null, $seance_id);
            $isCommission = $seance['Typeseance']['action'];

            if (empty($this->data)) {
                $this->data = $this->Deliberation->read(null, $id);        
	        $this->set('isCommission', $isCommission);  
	        $this->set('delib', $this->data);  
	    } 
            else { 
                if ( $isCommission == true) {
                    if (isset($this->data['Deliberation']['texte_doc'])){
                        if ($this->data['Deliberation']['texte_doc']['size']!=0){
                            $this->data['Deliberation']['commission_name'] = $this->data['Deliberation']['texte_doc']['name'];
                            $this->data['Deliberation']['commission_size'] = $this->data['Deliberation']['texte_doc']['size'];
                            $this->data['Deliberation']['commission_type'] = $this->data['Deliberation']['texte_doc']['type'];
                            $this->data['Deliberation']['commission_content']      = $this->getFileData($this->data['Deliberation']['texte_doc']['tmp_name'], $this->data['Deliberation']['texte_doc']['size']);
                            unset($this->data['Deliberation']['texte_doc']);
                        }
                    }    
                }
                else {
		    if (isset($this->data['Deliberation']['texte_doc'])){
                        if ($this->data['Deliberation']['texte_doc']['size']!=0){
                            $this->data['Deliberation']['debat_name'] = $this->data['Deliberation']['texte_doc']['name'];
                            $this->data['Deliberation']['debat_size'] = $this->data['Deliberation']['texte_doc']['size'];
                            $this->data['Deliberation']['debat_type'] = $this->data['Deliberation']['texte_doc']['type'];
                            $this->data['Deliberation']['debat']      = $this->getFileData($this->data['Deliberation']['texte_doc']['tmp_name'], $this->data['Deliberation']['texte_doc']['size']);
                            unset($this->data['Deliberation']['texte_doc']);
                        }
                    }
                }

		$this->data['Deliberation']['id']=$id;
                if ($this->Deliberation->save($this->data)) {
                    $this->redirect('/seances/saisirDebat/'.$id);
                } else {
                    $this->Session->setFlash('Please correct errors below.');
                }
            }
	}


	function getFileData($fileName, $fileSize) {
		return fread(fopen($fileName, "r"), $fileSize);
	}

	function saisirDebatGlobal ($id = null) {

		if (empty($this->data)) {
			$this->data = $this->Seance->read(null, $id);
			$this->set('annexes',$this->Annex->findAll('Annex.seance_id='.$id.' AND type="A"'));
		        $this->set('seance', $this->data);
		} else{
                     if (isset($this->data['Seance']['texte_doc'])){
			if ($this->data['Seance']['texte_doc']['size']!=0){
                             $this->data['Seance']['id'] = $id;
			     $this->data['Seance']['debat_global_name'] = $this->data['Seance']['texte_doc']['name'];
                             $this->data['Seance']['debat_global_size'] = $this->data['Seance']['texte_doc']['size'];
                             $this->data['Seance']['debat_global_type'] = $this->data['Seance']['texte_doc']['type'];
                             $this->data['Seance']['debat_global']      = $this->getFileData($this->data['Seance']['texte_doc']['tmp_name'], $this->data['Seance']['texte_doc']['size']);
                             $this->Seance->save($this->data);
                             unset($this->data['Seance']['texte_doc']);
                         }
                     }
		
		        $this->data['Seance']['id']=$id;
			if(!empty($this->params['form']))
			{
				$seance = array_shift($this->params['form']);
				$annexes = $this->params['form'];

				$uploaded = true;
				$size = count($this->params['form']);
				$counter = 1;

				while($counter <= ($size/2))
				{
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name'])){
						$uploaded = false;
					}
					$counter++;
				}

				if($uploaded) {
					if ($this->Seance->save($this->data)) {
					$counter = 1;

						while($counter <= ($size/2)) {
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = 0;
							$this->data['Annex']['seance_id'] = $id;
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
							$this->data['Annex']['type'] = 'A';
							$this->data['Annex']['filename'] = $annexes['file_'.$counter]['name'];
							$this->data['Annex']['filetype'] = $annexes['file_'.$counter]['type'];
							$this->data['Annex']['size'] = $annexes['file_'.$counter]['size'];
							$this->data['Annex']['data'] = $this->getFileData($annexes['file_'.$counter]['tmp_name'], $annexes['file_'.$counter]['size']);
							if(!$this->Annex->save($this->data))
							{
								echo "pb de sauvegarde de l\'annexe ".$counter;
							}
						$counter++;
						}
						$this->redirect('/seances/listerFuturesSeances');
					} else {
						$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					}
				}
			}
		}
	}

	function detailsAvis ($seance_id=null) {
		// initialisations
		$deliberations=$this->afficherProjets($seance_id, 0);
		$date_tmpstp = strtotime($this->GetDate($seance_id));
		$toutesVisees = true;

		for ($i=0; $i<count($deliberations); $i++){
                    $id_service = $deliberations[$i]['Service']['id'];
		    $deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
		    $deliberations[$i]['Model']['id'] = $this->requestAction("deliberations/getModelId/". $deliberations[$i]['Deliberation']['id']);
		    if (empty($deliberations[$i]['Deliberation']['avis']))
		        $toutesVisees = false;
		}

		$this->set('USE_GEDOOO', USE_GEDOOO);
		$this->set('deliberations',$deliberations);
		$this->set('date_seance', $this->Date->frenchDateConvocation($date_tmpstp));
		$this->set('seance_id', $seance_id);
		$this->set('canClose', (($date_tmpstp <= strtotime(date('Y-m-d H:i:s'))) && $toutesVisees));
	}

	function donnerAvis ($deliberation_id=null) {
		// Initialisations
		$sortie = false;
		$deliberation = $this->Deliberation->read(null, $deliberation_id);
		$seanceIdCourante = $deliberation['Seance']['id'];

		if (!empty($this->data)) {
			// En fonction de l'avis s�lectionn�
			if (!array_key_exists('avis', $this->data['Deliberation'])) {
				$this->Seance->invalidate('avis');
			} elseif ($this->data['Deliberation']['avis'] == 2) {
				// D�favorable : le projet repasse en �tat = 0
				$this->data['Deliberation']['etat'] = 0;
				unset($this->data['Deliberation']['seance_id']);
				$this->Deliberation->save($this->data);
				// ajout du commentaire
				$this->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
				$this->data['Commentaire']['texte'] = 'A re�u un avis d�favorable en '
					. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$deliberation['Seance']['type_id'])
					. ' du ' . $this->Date->frenchDate(strtotime($deliberation['Seance']['date']));
				 $this->Deliberation->Commentaire->save($this->data);

				$sortie = true;
			} elseif ($this->data['Deliberation']['avis'] == 1) {
				// Favorable : on attribue une nouvelle date de s�ance si elle est s�lectionn�e
				if (empty($this->data['Deliberation']['seance_id'])) {
					unset($this->data['Deliberation']['seance_id']);
					// on calcule le num�ro de la d�lib�ration car il n'y a pas de s�ance suivante attribu�e
					if (empty($deliberation['Deliberation']['num_delib'])) {
						$compteurId = $this->Seance->Typeseance->field('compteur_id', 'Typeseance.id = '.$deliberation['Seance']['type_id']);
						$this->data['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($compteurId);
					}
				} else
					$this->data['Deliberation']['position'] = $this->Deliberation->findCount("seance_id =".$this->data['Deliberation']['seance_id']." AND (etat != -1 )")+1;
				$this->Deliberation->save($this->data['Deliberation']);
				// ajout du commentaire
				$this->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
				$this->data['Commentaire']['texte'] = 'A re�u un avis favorable en '
					. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$deliberation['Seance']['type_id'])
					. ' du ' .$this->Date->frenchDate(strtotime($deliberation['Seance']['date']));
				$this->Deliberation->Commentaire->save($this->data);

				$sortie = true;
			}

			$this->data = $deliberation;
		}
		if ($sortie)
			$this->redirect('/seances/detailsAvis/'.$seanceIdCourante);
		else {
			$this->data = $deliberation;
			$this->set('avis', array(1 => 'Favorable', 2 => 'D�favorable'));
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('seances', $this->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
		}
	}

        function saisirSecretaire($seance_id) {
            $this->set('seance_id', $seance_id);
            $seance = $this->Seance->read(null, $seance_id);
            $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
            foreach( $acteursConvoques as  $acteurConvoque)
	        $tab[$acteurConvoque['Acteur']['id']] =  $acteurConvoque['Acteur']['prenom'].' '. $acteurConvoque['Acteur']['nom'];
            $this->set('acteurs', $tab);

	    if (empty($this->data)) {
	        $this->set('selectedActeurs', $seance['Seance']['secretaire_id']);
            }
	    else {
		$seance['Seance']['secretaire_id'] = $this->data['Acteur']['Acteur'];
		if ($this->Seance->save($seance))
		    $this->redirect('/seances/listerFuturesSeances');
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

       	function listeActeursPresents($seance_id) {
	    // Lecture du modele
	    $texte = $this->Model->field('content', 'id=8');
	    $listeActeurs = "";
            $acteurs = $this->getListActeurs($seance_id, 1);
            foreach($acteurs as $key => $acteur_id) {
                $acteur = $this->Acteur->findById($acteur_id );
	        $searchReplace = array(
		    "#NOUVELLE_PAGE#" => "<newpage>",
		    "#NOM_PRESENT#" => $acteur['Acteur']['nom'],
		    "#PRENOM_PRESENT#" => $acteur['Acteur']['prenom'],
		    "#SALUTATION_PRESENT#" => $acteur['Acteur']['salutation'],
		    "#TITRE_PRESENT#" => $acteur['Acteur']['titre'],
		    "#ADRESSE1_PRESENT#" => $acteur['Acteur']['adresse1'],
		    "#ADRESSE2_PRESENT#" => $acteur['Acteur']['adresse2'],
		    "#CP_PRESENT#" => $acteur['Acteur']['cp'],
		    "#VILLE_PRESENT#" => $acteur['Acteur']['ville']
		 );
		$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
            }
	    return($listeActeurs);
	}
 
        function listeActeursAbsents($seance_id) {
	     // Lecture du modele
	    $texte = $this->Model->field('content', 'id=9');
	    $listeActeurs = "";
            $acteurs = $this->getListActeurs($seance_id, 2);
            foreach($acteurs as $id =>$acteur_id ) {
                $acteur = $this->Acteur->findById($acteur_id);
	        $searchReplace = array(
		    "#NOUVELLE_PAGE#" => "<newpage>",
		    "#NOM_ABSENT#" => $acteur['Acteur']['nom'],
		    "#PRENOM_ABSENT#" => $acteur['Acteur']['prenom'],
		    "#SALUTATION_ABSENT#" => $acteur['Acteur']['salutation'],
		    "#TITRE_ABSENT#" => $acteur['Acteur']['titre'],
		    "#ADRESSE1_ABSENT#" => $acteur['Acteur']['adresse1'],
		    "#ADRESSE2_ABSENT#" => $acteur['Acteur']['adresse2'],
		    "#CP_ABSENT#" => $acteur['Acteur']['cp'],
		    "#VILLE_ABSENT#" => $acteur['Acteur']['ville']
		 );
		 $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
            }
	    return( $listeActeurs); 
	}

        function listeActeursMandates($seance_id) {
            // Lecture du modele
            $texte = $this->Model->field('content', 'id=10');
            $listeActeurs = "";
            $acteurs = $this->getListActeurs($seance_id, 3);
            foreach($acteurs as $mandate_id => $mandataire_id) {
                $mandataire = $this->Acteur->findById($mandataire_id);
                $mandate = $this->Acteur->findById($mandate_id);
                $searchReplace = array(
                    "#NOUVELLE_PAGE#" => "<newpage>",
                    "#NOM_MANDATE#" => $mandate['Acteur']['nom'],
                    "#PRENOM_MANDATE#" => $mandate['Acteur']['prenom'],
                    "#SALUTATION_MANDATE#" => $mandate['Acteur']['salutation'],
                    "#TITRE_MANDATE#" => $mandate['Acteur']['titre'],
                    "#NOM_MANDATAIRE#" => $mandataire['Acteur']['nom'],
                    "#PRENOM_MANDATAIRE#" => $mandataire['Acteur']['prenom'],
                    "#SALUTATION_MANDATAIRE#" => $mandataire['Acteur']['salutation'],
                    "#TITRE_MANDATAIRE#" => $mandataire['Acteur']['titre'],
                    "#ADRESSE1_MANDATAIRE#" => $mandataire['Acteur']['adresse1'],
                    "#ADRESSE2_MANDATAIRE#" => $mandataire['Acteur']['adresse2'],
                    "#CP_MANDATAIRE#" => $mandataire['Acteur']['cp'],
                    "#VILLE_MANDATAIRE#" => $mandataire['Acteur']['ville']
                );
                $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
            }
            return($listeActeurs);
        }

        function listeActeursMouvements($seance_id) {
            // Lecture du modele
            $texte = $this->Model->field('content', 'id=7');
            $listeActeurs = "";
            $acteurs = $this->getListActeurs($seance_id, 4);
            foreach($acteurs as $acteur_id => $delib_id) {
                $mandate = $this->Acteur->findById($acteur_id);
		$delib = $this->Deliberation->findById($delib_id);
                $searchReplace = array(
                    "#NOUVELLE_PAGE#" => "<newpage>",
                    "#NOM_ACTEUR#" => $mandate['Acteur']['nom'],
                    "#PRENOM_ACTEUR#" => $mandate['Acteur']['prenom'],
                    "#SALUTATION_ACTEUR#" => $mandate['Acteur']['salutation'],
                    "#TITRE_ACTEUR#" => $mandate['Acteur']['titre'],
                    "#ADRESSE1_ACTEUR#" => $mandate['Acteur']['adresse1'],
                    "#ADRESSE2_ACTEUR#" => $mandate['Acteur']['adresse2'],
                    "#CP_ACTEUR#" => $mandate['Acteur']['cp'],
                    "#VILLE_ACTEUR#" => $mandate['Acteur']['ville'],
                    "#IDENTIFIANT_DELIB#" => $delib['Deliberation']['id'],
                    "#TITRE_DELIB#" => $delib['Deliberation']['titre'],
                    "#OBJET_DELIB#" => $delib['Deliberation']['objet'],
                    "#NUMERO_DELIB#" => $delib['Deliberation']['num_delib']
                );
                $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
            }
            return($listeActeurs);
        }

        function download($id=null, $file){
            header('Content-type: '.$this->getFileType($id, $file));
            header('Content-Length: '.$this->getSize($id, $file));
            header('Content-Disposition: attachment; filename='.$this->getFileName($id, $file));
            echo $this->getData($id, $file);
            exit();
        }


        function getFileType($id=null, $file) {
            $objCourant = $this->Seance->read(null, $id);
            return $objCourant['Seance'][$file."_type"];
        }

        function getFileName($id=null, $file) {
            $objCourant = $this->Seance->read(null, $id);
            return $objCourant['Seance'][$file."_name"];
        }

        function getSize($id=null, $file) {
             $objCourant = $this->Seance->read(null, $id);
            return $objCourant['Seance'][$file."_size"];
        }

        function getData($id=null, $file) {
            $objCourant = $this->Seance->read(null, $id);
            return $objCourant['Seance'][$file];
        }

}
?>
