<?php
class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $components = array('Date','Email');
	var $uses = array('Deliberation','Seance','User','SeancesUser', 'Collectivite', 'Listepresence', 'Vote','Model');

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

	function add() {
		if (empty($this->data)) {
			$this->set('typeseances', $this->Seance->Typeseance->generateList());
			$this->set('selectedTypeseances', null);
			$this->render();
		} else {
			$this->cleanUpFields('Seance');

			$this->data['Seance']['date']=  $this->Utils->FrDateToUkDate($this->params['form']['date']);
			$this->data['Seance']['date'] = $this->data['Seance']['date'].' '.$this->data['Seance']['date_hour'].':'.$this->data['Seance']['date_min'];

			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/seances/index');
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

	function listerFuturesSeances()
	{
		if (empty ($this->data)) {
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$seances = $this->Seance->findAll(($condition),null,'date asc');

			for ($i=0; $i<count($seances); $i++)
			    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

			$this->set('seances', $seances);
		}
	}

	function listerAnciennesSeances()
	{
			if (empty ($this->data)) {
			$condition= 'date <= "'.date('Y-m-d H:i:s').'"';
			$seances = $this->Seance->findAll(($condition),null,'date asc');

			for ($i=0; $i<count($seances); $i++)
			    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

			$this->set('seances', $seances);
		}
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
		            $calendrier .=  "<td><p class=\"$balise\">".$Day->thisDay()."</p></td>\n" ;
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
		$condition= "seance_id=$id AND etat>=2";
		if (!isset($return)) {
		    $this->set('lastPosition', $this->requestAction("deliberations/getLastPosition/$id"));
			$deliberations = $this->Deliberation->findAll($condition,null,'position ASC');
			for ($i=0; $i<count($deliberations); $i++){
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
			}
			$this->set('seance_id', $id);
		    $this->set('projets', $deliberations);
			$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->GetDate($id))));

		}
		else
		    return ($this->Deliberation->findAll($condition,null,'position ASC'));
	}


	function getDate($id=null)
    {
		if (empty($id))
			return '';
		else{
		$condition = "Seance.id = $id";
        $objCourant = $this->Seance->findAll($condition);
       // debug($objCourant);
		return $objCourant['0']['Seance']['date'];}
    }

	function getType($id)
    {
		$condition = "Seance.id = $id";
        return $this->Seance->findAll($condition);
    }

	function addListUsers($seance_id=null) {
		if (empty($this->data)) {
			$this->data=$this->Seance->read(null,$seance_id);
			$this->set('seance_id',$seance_id);
			$this->set('users', $this->User->generateList('statut=1'));
			if (empty($this->data['SeancesUser'])) {
				$this->data['SeancesUser'] = null;
			}
			$this->set('selectedUsers', $this->_selectedArray($this->data['SeancesUser'],'user_id'));
			$this->render();
		} else {

/*			$seance_id = $this->data['Seance']['id'];
			$seancesUser = $this->SeancesUser->find("seance_id=$seance_id");
			foreach ($seancesUser as $seanceUser){
				$this->EffacerListe($seanceUser['id']);
			}*/


			$this->EffacerListe($this->data['Seance']['id']);

			foreach($this->data['User']['id']as $user_id) {
				$this->params['data']['SeancesUser']['id']='';
			    $this->params['data']['SeancesUser']['seance_id'] = $this->data['Seance']['id'];
			    $this->params['data']['SeancesUser']['user_id'] = $user_id ;

			    if ($this->SeancesUser->save($this->params['data']))
			    {
			    	$this->redirect('/seances/listerFuturesSeances');
			    }else
			    {
			    	$this->Session->setFlash('Corrigez les erreurs ci-dessous.');
			    	$this->set('seance_id',$seance_id);
					$this->set('users', $this->User->generateList());
					$this->set('selectedUsers',null);
			    }

			}
		}
	}

	function effacerListe($seance_id=null) {
		$condition = "seance_id = $seance_id";
		$presents = $this->SeancesUser->findAll($condition);
		foreach($presents as $present)
  		    $this->SeancesUser->del($present['SeancesUser']['id']);
	}

	function generateConvocationList ($id=null) {
		$data =  $this->SeancesUser->findAll("seance_id =$id");
		$type_infos = $this->getType($id);
		$model=$this->Model->findAll();
		$projets= $this->afficherProjets($id, 1);
		$jour= $this->Date->days[intval(date('w'))];
		$mois= $this->Date->months[intval(date('m'))];
		$collectivite= $this->Collectivite->findAll();
		$date_seance= $this->Date->frenchDateConvocation(strtotime($type_infos[0]['Seance']['date']));

	    vendor('fpdf/html2fpdf');
	    $pdf = new HTML2FPDF();
        foreach($data as $seanceUser) {
            $pdf->AddPage();
		    $emailPdf = new HTML2FPDF();
		    $emailPdf->AddPage();
	        $search = array("#LOGO_COLLECTIVITE#","#ADRESSE_COLLECTIVITE#","#NOM_ELU#","#ADRESSE_ELU#","#VILLE_ELU#","#VILLE_COLLECTIVITE#","#DATE_DU_JOUR#","#TYPE_SEANCE#","#DATE_SEANCE#","#LIEU_SEANCE#");
	        $replace = array('<img src="files/image/logo.jpg">',
		        $collectivite[0]['Collectivite']['nom'].'<br>'.$collectivite[0]['Collectivite']['adresse'].'<br>'.$collectivite[0]['Collectivite']['CP'].' '.$collectivite[0]['Collectivite']['ville'],
			    $seanceUser['User']['prenom'].' '.$seanceUser['User']['nom'],
			    $seanceUser['User']['adresse'],
			    $seanceUser['User']['CP'].' '.$seanceUser['User']['ville'],
			    $collectivite[0]['Collectivite']['ville'],
			    $jour.' '.date('d').' '.$mois.' '.date('Y'),
			    $type_infos[0]['Typeseance']['libelle'],
			    $date_seance, "un lieu a definir"
	        );
	        $generation = str_replace($search,$replace,$model[0]['Model']['texte']);
	        $pdf->WriteHTML($generation);
		    $emailPdf->WriteHTML($generation);
            foreach($projets as $projet) {
                $pdf->WriteHTML($projet['Deliberation']['position'].')&nbsp; ');
                $pdf->WriteHTML($projet['Deliberation']['titre'].'<br>');
                $pdf->WriteHTML('<b><u>Thème</u> : </b><br>'.$projet['Theme']['libelle'].'<br>');
         	    $pdf->WriteHTML('<b><u>Rapporteur</u> : </b><br>'.$projet['Rapporteur']['nom'].' '.$projet['Rapporteur']['prenom'].'<br>');
         	    $pdf->WriteHTML('<b><u>Service emetteur</u> : </b><br>'.$projet['Service']['libelle'].'<br><br>');
			    // Pour la création des convocs à envoyer par mails
			    $emailPdf->WriteHTML($projet['Deliberation']['position'].')&nbsp; ');
                $emailPdf->WriteHTML($projet['Deliberation']['titre'].'<br>');
                $emailPdf->WriteHTML('<b><u>Thème</u> : </b><br>'.$projet['Theme']['libelle'].'<br>');
         	    $emailPdf->WriteHTML('<b><u>Rapporteur</u> : </b><br>'.$projet['Rapporteur']['nom'].' '.$projet['Rapporteur']['prenom'].'<br>');
         	    $emailPdf->WriteHTML('<b><u>Service emetteur</u> : </b><br>'.$projet['Service']['libelle'].'<br><br>');
            }
            $pos =  strrpos ( getcwd(), 'webroot');
		    $path = substr(getcwd(), 0, $pos);
		    $convoc_path = $path."webroot/files/convocations/convoc_".$seanceUser['User']['id'].".pdf";
		    $emailPdf->Output($convoc_path ,'F');
		    $this->sendConvoc($seanceUser['User']['id'], $convoc_path, $type_infos[0]['Typeseance']['libelle'], $date_seance);
            unlink($convoc_path);
    	}
        $pdf->Output('convocations.pdf','D');
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

	function generateConvocationAnonyme ($id=null) {
		$this->set('data', $this->SeancesUser->findAll("seance_id =$id"));
		$type_infos = $this->getType($id);
		$this->set('type_infos', $type_infos );
		$this->set('model',$this->Model->findAll());
		$this->set('projets', $this->afficherProjets($id, 1));
		$this->set('jour', $this->Date->days[intval(date('w'))]);
		$this->set('mois', $this->Date->months[intval(date('m'))]);
		$this->set('collectivite',  $this->Collectivite->findAll());
		$this->set('date_seance',  $this->Date->frenchDateConvocation(strtotime($type_infos[0]['Seance']['date'])));
	}

	function generateOrdresDuJour ($id=null) {
		$this->set('data', $this->SeancesUser->findAll("seance_id =$id"));
		$type_infos = $this->getType($id);
		$this->set('model',$this->Model->findAll());
		$this->set('type_infos', $type_infos );
		$this->set('projets', $this->afficherProjets($id, 1));
		$this->set('jour', $this->Date->days[intval(date('w'))]);
		$this->set('mois', $this->Date->months[intval(date('m'))]);
		$this->set('collectivite',  $this->Collectivite->findAll());
		$this->set('date_seance',  $this->Date->frenchDate(strtotime($type_infos[0]['Seance']['date'])));
	}

	function checkLists($seance_id){
	    $condition = "seance_id = $seance_id";
		$inscrits = $this->SeancesUser->findAll($condition);
		$presents = $this->Listepresence->findAll($condition);

		foreach($presents as $present)
		    if (! $this->isInList($present['User']['id'], $inscrits)){
				//echo ("On efface ".$present['User']['nom']);
			    $this->delUserFromList($present['User']['id'], $seance_id );
		}

		foreach($inscrits as $inscrit)
		    if (!$this->isInList($inscrit['User']['id'], $presents)){
				//echo ("On enregistre  ".$inscrit['User']['nom'].'<br>');
			    $this->addUserFromList($inscrit['User']['id'], $seance_id );
		    }
	}

	function listerPresents($seance_id=null) {
		// Controle la cohérence entre la liste des inscrits et les présents
	    $this->checkLists($seance_id);
	    unset ($this->cacheQueries);

		if (empty($this->data)) {
	    	$presents = $this->Listepresence->findAll("seance_id = $seance_id");
	    	// Si l'on a encore rien saisi, on prend la table thï¿½orique des prï¿½sents
			if(empty($presents))
			   $presents = $this->SeancesUser->findAll("seance_id = $seance_id");
			else {
				foreach($presents as $present){
				    	$this->data[$present['Listepresence']['user_id']]['present'] = $present['Listepresence']['present'];
					    $this->data[$present['Listepresence']['user_id']]['mandataire'] = $present['Listepresence']['mandataire'];
				}
			}
			$this->set('presents',  $presents);
			$this->set('seance_id',$seance_id);
			$this->set('mandataires', $this->User->generateList('statut = 1'));
		}
		else {
			$this->data['Listepresence']['seance_id']=$seance_id;
			$this->effacerListePresence($seance_id);
			foreach($this->data as $user_id=>$tab){
				$this->Listepresence->create();
				if (!is_int($user_id))
					continue;
			    $this->data['Listepresence']['user_id'] = $user_id;
			    if (isset($tab['present']))
			        $this->data['Listepresence']['present'] = $tab['present'];
			    if (isset($tab['mandataire']))
			         $this->data['Listepresence']['mandataire'] = $tab['mandataire'];
			    else
			    	$this->data['Listepresence']['mandataire'] =0;

			 	$this->Listepresence->save($this->data);
			}
			$this->redirect('/seances/listerFuturesSeances');
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

	function effacerListePresence($seance_id=null) {
		$condition = "seance_id = $seance_id";
		$presents = $this->Listepresence->findAll($condition);
		foreach($presents as $present)
  		    $this->Listepresence->del($present['Listepresence']['id']);
	}

	function afficherListePresents($seance_id=null)	{
		$conditions = "Listepresence.seance_id= $seance_id ";
		$presents = $this->Listepresence->findAll($conditions);

		for($i=0; $i<count($presents); $i++){
			if ($presents[$i]['Listepresence']['mandataire'] !='0')
			    $presents[$i]['Listepresence']['mandataire'] = $this->User->requestAction('/users/getPrenom/'.$presents[$i]['Listepresence']['mandataire']).' '.$this->User->requestAction('/users/getNom/'.$presents[$i]['Listepresence']['mandataire']);
		}
		return ($presents);
	}

	function details ($seance_id=null) {
		$deliberations=$this->afficherProjets($seance_id, 0);
		for ($i=0; $i<count($deliberations); $i++){
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
		}
		$this->set('deliberations',$deliberations);
		$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->GetDate($seance_id))));

	}

	function effacerVote($deliberation_id=null) {
		$condition = "delib_id = $deliberation_id";
		$votes = $this->Vote->findAll($condition);
		foreach($votes as $vote)
  		    $this->Vote->del($vote['Vote']['id']);
	}

	function voter ($deliberation_id=null) {
		$seance_id = $this->requestAction('/deliberations/getCurrentSeance/'.$deliberation_id);

		if (empty($this->data)) {
			$donnees = $this->Vote->findAll("delib_id = $deliberation_id");

			foreach($donnees as $donnee){
				$this->data['Vote'][$donnee['Vote']['user_id']]=$donnee['Vote']['resultat'];
			    $this->data['Vote']['commentaire'] = $donnee['Vote']['commentaire'];

			}
			$this->set('deliberation' , $this->Deliberation->findAll("Deliberation.id=$deliberation_id"));
			$this->set('presents' , $this->afficherListePresents($seance_id));

		}
		else {
 			$pour = 0;
 			$abstenu = 0;
			$this->effacerVote($deliberation_id);
			$nb_votant = count($this->data['Vote']);

			foreach($this->data['Vote']as $user_id => $vote){
				if(is_numeric($user_id)==true){
					$this->Vote->create();

					$this->data['Vote']['user_id']=$user_id;
					$this->data['Vote']['delib_id']=$deliberation_id;
					$this->data['Vote']['resultat']=$vote;
					if ($vote == 1 )
					     $pour ++;
					if (($vote == 2)||($vote == 3))
						$abstenu ++;
				    $this->Vote->save($this->data['Vote']);

				}
			}

			$this->data = $this->Deliberation->read(null, $deliberation_id);
			if ($pour >= (($nb_votant -$abstenu) /2))
			     $this->data['Deliberation']['etat']=3;
			else
				 $this->data['Deliberation']['etat']=4;
			$this->Deliberation->save($this->data);

			$this->redirect('seances/details/'.$seance_id);


		}
	}

	function saisirDebat ($id = null)	{
		$seance_id = $this->requestAction('/deliberations/getCurrentSeance/'.$id);

		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
		} else {
			$this->data['Deliberation']['id']=$id;
			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/seances/details/'.$seance_id);
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}
}
?>