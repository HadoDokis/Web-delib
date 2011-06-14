<?php
class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form', 'Form2', 'Javascript', 'Fck', 'Html2');
	var $components = array('Date','Email', 'Gedooo');
	var $uses = array('Deliberation', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'Model', 'Annex', 'Typeseance', 'Acteur');
	var $cacheAction = 0;

	// Gestion des droits
	var $demandeDroit = array(
		'listerFuturesSeances',
		'add',
		'afficherCalendrier'
	);
	var $commeDroit = array(
		'index'=>'Seances:listerFuturesSeances',
		'view'=>'Seances:listerFuturesSeances',
		'delete'=>'Seances:listerFuturesSeances',
		'edit'=>'Seances:listerFuturesSeances',
		'afficherProjets'=>'Seances:listerFuturesSeances',
		'changePosition'=>'Seances:listerFuturesSeances',
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
		'getListActeurs'=>'Seances:listerFuturesSeances',
		'saisirCommentaire'=>'Seances:listerFuturesSeances'
	);

	function index() {
		$this->Seance->recursive = 0;
		$seances = $this->Seance->findAll(null,null,'date asc');
		for ($i=0; $i<count($seances); $i++)
		    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

		$this->set('seances', $seances);
	}


	function view($id = null) {
	        $seance = $this->Seance->read(null, $id);
		if (!$id || empty($seance)) {
			$this->Session->setFlash('identifiant invalide pour la seance.', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/index');
		}
		$this->set('seance', $seance);
	}

	function add($timestamp=null) {
                $natures = array_keys($this->Session->read('user.Nature'));        
                $types = $this->Typeseance->TypeseancesNature->getTypeseanceParNature($natures);
		$this->set('typeseances', $this->Seance->Typeseance->find('list', array('conditions'=>array('Typeseance.id'=> $types) )));
		$this->set('selectedTypeseances', null);
		
		if (empty($this->data)) {
			if (isset($timestamp))
			    $this->set('date', date('d/m/Y',$timestamp));
			$this->render();
		} else {
			if (count(explode('/',$this->params['form']['date']))!=3) {
				$this->Session->setFlash('La date n\'est pas dans un format correct', 'growl', array('type'=>'erreur'));
			}
			else {
				$this->data['Seance']['date']['date'] =  $this->Utils->FrDateToUkDate($this->params['form']['date']);
				$this->data['Seance']['date'] = $this->data['Seance']['date']['date'].' '.$this->data['Seance']['date']['hour'].':'.$this->data['Seance']['date']['min'];
				if ($this->Seance->save($this->data)) {
					$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;e', 'growl');
					$this->redirect('/seances/listerFuturesSeances');
				} else {
					$this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
					if (empty($this->data['Typeseance']['Typeseance'])) {
						$this->data['Typeseance']['Typeseance'] = null;
					}
					$this->set('selectedTypeseances', $this->data['Typeseance']['Typeseance']);
				}
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la seance', 'growl', array('type'=>'erreur'));
				$this->redirect('/seances/listerFuturesSeances');
			}
			$this->data = $this->Seance->read(null, $id);
			if (empty($this->data['Typeseance'])) { $this->data['Typeseance'] = null; }
				$this->set('selectedTypeseances', $this->_selectedArray($this->data['Typeseance']));
		} else {
			$this->data['Seance']['date'] = $this->data['Seance']['date']['year'].'-'.$this->data['Seance']['date_month'].'-'.$this->data['Seance']['date']['day'].' '.$this->data['Seance']['date']['hour'].':'.$this->data['Seance']['date']['min'];
			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;e', 'growl');
				$this->redirect('/seances/listerFuturesSeances');
			} else {
				$this->Session->setFlash('Corrigez les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
				if (empty($this->data['Typeseance']['Typeseance'])) { $this->data['Typeseance']['Typeseance'] = null; }
					$this->set('selectedTypeseances', $this->data['Typeseance']['Typeseance']);
			}
		}
                $natures = array_keys($this->Session->read('user.Nature'));
                $types = $this->Typeseance->TypeseancesNature->getTypeseanceParNature($natures);
                $this->set('typeseances', $this->Seance->Typeseance->find('list', array('conditions'=>array('Typeseance.id'=> $types) )));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la seance', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/index');
		}
		if ($this->Seance->del($id)) {
			$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; suprim&eacute;e');
			$this->redirect('/seances/index');
		}
		else {
			$this->Session->setFlash('Invalide id pour la seance', 'growl', array('type'=>'erreur'));
			$this->redirect('/seances/index');
		}
	}

	function listerFuturesSeances() {
            $this->set('AFFICHE_CONVOCS_ANONYME', Configure::read('AFFICHE_CONVOCS_ANONYME'));
            $this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
            $this->set('canSign', $this->Droits->check($this->Session->read('user.User.id'), "Deliberations:sendToParapheur"));
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
	    if ($result || $this->data['Typeseance']['action']== 1) { 
                if ($this->Seance->saveField('traitee', 1)){
                    return true;
                }
	    }
	    else 
                return false;
	}

        function _stockDelibs($seance_id, $isArrete=false, $compteur_id=null) {
	    $result = true;
            $delibs = $this->Deliberation->find("all", array('conditions' => array("Deliberation.seance_id"=>$seance_id),
                                                             'order'      => "Deliberation.position ASC"));
            $nbDelibs = count($delibs );
            foreach ($delibs as $delib) {
	        $delib_id = $delib['Deliberation']['id']; 
                $this->Deliberation->id =  $delib_id;

                if ($delib['Deliberation']['nature_id']==1) 
                    $isArrete = false;
                else
                    $isArrete = true;

                if ($isArrete){
                    $this->Deliberation->saveField('etat', 3);
                    if ( $compteur_id != null) {  
                        $num =  $this->Seance->Typeseance->Compteur->genereCompteur($compteur_id);
                        $this->Deliberation->saveField('num_delib', $num);
                    }
                }
                 
		// On génère la délibération au format PDF
                $model_id = $this->Deliberation->getModelId($delib_id);
                $err = $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/D_$delib_id.pdf");
	        $filename =  WEBROOT_PATH."/files/generee/fd/null/$delib_id/D_$delib_id.pdf";
                $tmp_delib = $this->Deliberation->read(null, $delib_id);
             
                 //On récupère le contenu du fichier
                 $content = file_get_contents($filename);
                 if (strlen($content) == 0)
		     $result = false;
                 // On stock le fichier en base de données.
		 $this->Deliberation->saveField('delib_pdf', $content);
	    }
	    return  $result;
	}

	function afficherCalendrier ($annee=null){
	
		require_once(APP_PATH.'vendors'.DS.'Calendar'.DS.'includeCalendarVendor.php');

		Configure::write('CALENDAR_MONTH_STATE',Configure::read('CALENDAR_USE_MONTH_WEEKDAYS'));

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

	function afficherProjets ($id=null, $return=null) {
               $this->Deliberation->Behaviors->attach('Containable');
		$condition= array ("seance_id"=>$id, "etat <>"=>"-1");
		if (!isset($return)) {
		    $this->set('lastPosition', $this->Deliberation->getLastPosition($id) - 1 );
			$deliberations = $this->Deliberation->find('all', array ('conditions' =>$condition,
                                                                                 'order'      =>'Deliberation.position ASC',
                                                                                 'fields'     => array('position', 'objet', 'titre', 'id', 'theme_id', 'rapporteur_id'  ),
                                                                                 'contain'    => array('Service.libelle', 'Rapporteur')));
			$lst_pos=array();
			for ($i=0; $i<count($deliberations); $i++) {
                                $theme = $this->Deliberation->Theme->find('first', array('conditions' => array('Theme.id' => $deliberations[$i]['Deliberation']['theme_id'] ),
                                                                                         'recursive'  => -1)); 
                                $deliberations[$i]['Theme'] = $theme['Theme'];
				$lst_pos[$i+1] = $i+1;
			}
			$this->set('seance_id', $id);
			$this->set('rapporteurs', $this->Acteur->generateListElus());
			$this->set('projets', $deliberations);
			$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->GetDate($id))));
			$this->set('lst_pos', $lst_pos);
		}
		else
		    return ($this->Deliberation->find('all',array('conditions'=>$condition,'order'=>'Deliberation.position ASC')));
	}

    function changeRapporteur($newRapporteur,$delib_id) {
        $this->Deliberation->id = $delib_id;
        $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id ),
                                                          'fields'     => array('seance_id'),
                                                          'recursive'  => -1 ) );
        if ($this->Deliberation->saveField('rapporteur_id', $newRapporteur)) {
       	    $this->Redirect('/seances/afficherProjets/'.$delib['Deliberation']['seance_id']);
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
		$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
		$deliberations=$this->afficherProjets($seance_id, 0);
		for ($i=0; $i<count($deliberations); $i++){
                    $id_service = $deliberations[$i]['Service']['id'];
		    $deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
			$deliberations[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($deliberations[$i]['Seance']['type_id'], $deliberations[$i]['Deliberation']['etat']);
		}
		$this->set('deliberations',$deliberations);
		$date_tmpstp = strtotime($this->GetDate($seance_id));
		$this->set('date_tmpstp', $date_tmpstp);
		$this->set('date_seance', $this->Date->frenchDateConvocation($date_tmpstp));
		$this->set('seance_id', $seance_id);
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
		        $nbAbsent = 0;
			// Initialisation du détail du vote
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
			$listPresents =  $this->requestAction('/deliberations/afficherListePresents/'.$deliberation_id);
			$this->set('presents', $listPresents);

			$nbPresent = count ($listPresents);
			foreach ( $listPresents as $present)
                            if(($present['Listepresence']['present']==0)&&($present['Listepresence']['mandataire']==0))
			        $nbAbsent++;
			if ($nbPresent/2 < $nbAbsent)
			    $this->set('message', 'Attention, le quorum n\'est plus atteint...');

		} else {
			$this->data['Deliberation']['id'] = $deliberation_id;
			$this->effacerVote($deliberation_id);
			switch ($this->data['Vote']['typeVote']) {
			case 1:
				// Saisie du détail du vote
				$this->data['Deliberation']['vote_nb_oui'] = 0;
				$this->data['Deliberation']['vote_nb_non'] = 0;
				$this->data['Deliberation']['vote_nb_abstention'] = 0;
				$this->data['Deliberation']['vote_nb_retrait'] = 0;
				if (!empty($this->data['detailVote'])) {
					foreach($this->data['detailVote'] as $acteur_id => $vote){
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

		    // Attribution du numéro de la délibération si adoptée et si pas déjà attribué
			if ( ($this->data['Deliberation']['etat'] == 3)
				&& empty($deliberation['Deliberation']['num_delib']) )
				$this->data['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($seance['Typeseance']['compteur_id']);
			if ($this->Deliberation->save($this->data['Deliberation'])) {
			    $this->redirect($this->Session->read('user.User.lasturl'));
                        }
		}
	}


	function saisirDebat ($id = null)	{
		$seance_id = $this->Deliberation->getCurrentSeance($id);
		$seance = $this->Seance->read(null, $seance_id);
		if ($seance['Seance']['pv_figes']==1) {
			$this->Session->setFlash('Les pvs ont été figés, vous ne pouvez plus saisir de débat pour cette délibération...', 'growl', array('type'=>'erreur'));
			$this->redirect('/postseances/index');
			exit;
		}

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
						$this->data['Deliberation']['commission']      = $this->getFileData($this->data['Deliberation']['texte_doc']['tmp_name'], $this->data['Deliberation']['texte_doc']['size']);
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
				$this->Session->setFlash('Please correct errors below.', 'growl', array('type'=>'erreur'));
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
			if ($this->Seance->save($this->data)) {
				$this->redirect('/seances/listerFuturesSeances');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type'=>'erreur'));
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
		    $deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
			$deliberations[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($deliberations[$i]['Seance']['type_id'], $deliberations[$i]['Deliberation']['etat']);
		    if (empty($deliberations[$i]['Deliberation']['avis']))
		        $toutesVisees = false;
		}

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
			if (!array_key_exists('avis', $this->data['Deliberation'])) {
				$this->Seance->invalidate('avis');
			} else {
				// Initialisations liées à la nouvelle date de séance ou non
				if (empty($this->data['Deliberation']['seance_id'])) {
					unset($this->data['Deliberation']['seance_id']);
					// Calcul du numéro de la délibération car il n'y a pas de séance suivante attribuée
				//	if (empty($deliberation['Deliberation']['num_delib'])) {
				//		$compteurId = $this->Seance->Typeseance->field('compteur_id', 'Typeseance.id = '.$deliberation['Seance']['type_id']);
				//		$this->data['Deliberation']['num_delib'] = $this->Seance->Typeseance->Compteur->genereCompteur($compteurId);
			        //      }
				} else
					$this->data['Deliberation']['position'] = $this->Deliberation->findCount("seance_id =".$this->data['Deliberation']['seance_id']." AND (etat != -1 )")+1;

				// Sauvegarde de la délibération
				$this->Deliberation->save($this->data['Deliberation']);

				// ajout du commentaire
				$this->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
				$this->data['Commentaire']['texte'] = 'A reçu un avis ';
				$this->data['Commentaire']['texte'].= ($this->data['Deliberation']['avis'] == 1) ? 'favorable' : 'défavorable';
				$this->data['Commentaire']['texte'].= ' en '. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$deliberation['Seance']['type_id']);
				$this->data['Commentaire']['texte'].= ' du ' .$this->Date->frenchDate(strtotime($deliberation['Seance']['date']));
				$this->data['Commentaire']['commentaire_auto'] = 1;
				$this->Deliberation->Commentaire->save($this->data);

				$sortie = true;
			}
		}
		if ($sortie)
			$this->redirect('/seances/detailsAvis/'.$seanceIdCourante);
		else {
			$this->data = $deliberation;
			$this->set('avis', array(1 => 'Favorable', 2 => 'Défavorable'));
		    $user = $this->Session->read('user');
			if ($this->Xacl->check($user['User']['id'], "Deliberations:editerProjetValide"))
			    $afficherTtesLesSeances = true;
		    else
		        $afficherTtesLesSeances = false;
			$this->set('seances', $this->Seance->generateList(array('Seance.id <>'=> $seanceIdCourante), $afficherTtesLesSeances,  array_keys($this->Session->read('user.Nature'))));
		}
	}

        function saisirSecretaire($seance_id) {
            $this->set('seance_id', $seance_id);
            $seance = $this->Seance->find('first', array('conditions' => array('Seance.id' => $seance_id)));
            $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
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

	function saisirCommentaire($seance_id) {
            $seance = $this->Seance->read(null, $seance_id); 
            $this->set('seance_id',$seance_id);
            if (empty($this->data)) {
	       		$this->data =  $seance;
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

	function changePosition ($new_position, $delib_id) {
            $delib =  $this->Deliberation->find('first', array(
                                                'conditions'=>array('Deliberation.id'=> $delib_id),
                                                'fields' => array('id', 'position', 'seance_id'),
                                                'recursive' => '-1'));
 
	    $old_position = $delib['Deliberation']['position'];
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
            $this->Deliberation->updateAll(array('Deliberation.position' => "Deliberation.position+$delta"), 
                                           array("Deliberation.position >= " => $start, 
                                                 "Deliberation.position <= " => $end,
                                                 "Deliberation.seance_id"    => $delib['Deliberation']['seance_id'], 
                                                 "Deliberation.etat <> "     => -1));

	    $delib['Deliberation']['position'] = $new_position; 
	    $this->Deliberation->save($delib); 

            $this->Session->setFlash("Projet [id:$delib_id] déplacée en position : $new_position, ancienne position : $old_position ",  'growl');
            $this->redirect("/seances/afficherProjets/".$delib['Deliberation']['seance_id']);
	}

       function clore($seance_id) {
           $actes = $this->Deliberation->find('all', array('conditions' => array('Deliberation.seance_id' => $seance_id,
                                                                                 'Deliberation.etat > '   => '1', 
                                                                                 'Deliberation.signee'   => null),
                                                           'fields' => 'id, seance_id',
                                                           'recursive' => -1));
           
           if (count($actes) > 0) {
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

}
?>
