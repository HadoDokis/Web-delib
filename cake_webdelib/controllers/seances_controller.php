<?php
class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2');
	var $components = array('Date','Email', 'Gedooo');
	var $uses = array('Deliberation', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'Model', 'Annex');
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
		'generateConvocationList'=>'Seances:listerFuturesSeances',
		'generateOrdresDuJour'=>'Seances:listerFuturesSeances',
		'saisirDebatGlobal'=>'Seances:listerFuturesSeances',
		'details'=>'Seances:listerFuturesSeances',
		'saisirDebat'=>'Seances:listerFuturesSeances',
		'voter'=>'Seances:listerFuturesSeances',
		'changeRapporteur'=>'Seances:listerFuturesSeances',
		'changeStatus'=>'Seances:listerFuturesSeances',
		'detailsAvis'=>'Seances:listerFuturesSeances',
		'donnerAvis'=>'Seances:listerFuturesSeances'
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
//			$this->set('rapporteurs', $this->Deliberation->User->generateList('statut=1'));
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
    		//redirection sur la page o√π on √©tait avant de changer de service
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
       // debug($objCourant);
		return $objCourant['0']['Seance']['date'];}
    }

	function getType($id)
    {
		$condition = "Seance.id = $id";
        return $this->Seance->findAll($condition);
    }

	function generateConvocationList ($id_seance=null) {
	    $seance = $this->Seance->read(null, $id_seance);
	    $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
	    $model = $this->Model->read(null, $seance['Typeseance']['modelconvocation_id']);
	    $jour = $this->Date->days[intval(date('w'))];
	    $mois = $this->Date->months[intval(date('m'))];
	    $collectivite = $this->Collectivite->findAll();
            $date_seance = $this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));

            $search = array(
			"#LOGO_COLLECTIVITE#",
			"#NOM_COLLECTIVITE#",
			"#ADRESSE_COLLECTIVITE#",
			"#CP_COLLECTIVITE#",
			"#VILLE_COLLECTIVITE#",
			"#TELEPHONE_COLLECTIVITE#",
			"#NOM_ACTEUR#",
			"#PRENOM_ACTEUR#",
			"#SALUTATION_ACTEUR#",
			"#TITRE_ACTEUR#",
			"#ADRESSE1_ACTEUR#",
			"#ADRESSE2_ACTEUR#",
			"#CP_ACTEUR#",
			"#VILLE_ACTEUR#",
			"#DATE_DU_JOUR#",
			"#TYPE_SEANCE#",
			"#DATE_SEANCE#",
			"#LISTE_PROJETS_SOMMAIRES#",
			"#LISTE_PROJETS_DETAILLES#"
		);

	    vendor('fpdf/html2fpdf');
	    $pdf = new HTML2FPDF();
            foreach($acteursConvoques as $acteur) {
                $pdf->AddPage();
		$emailPdf = new HTML2FPDF();
		$emailPdf->AddPage();
	        $replace = array(
			'<img src="files/image/logo.jpg">',
			$collectivite[0]['Collectivite']['nom'],
			$collectivite[0]['Collectivite']['adresse'],
			$collectivite[0]['Collectivite']['CP'],
			$collectivite[0]['Collectivite']['ville'],
			$collectivite[0]['Collectivite']['telephone'],
			$acteur['Acteur']['nom'],
			$acteur['Acteur']['prenom'],
			$acteur['Acteur']['salutation'],
			$acteur['Acteur']['titre'],
			$acteur['Acteur']['adresse1'],
			$acteur['Acteur']['adresse2'],
			$acteur['Acteur']['cp'],
			$acteur['Acteur']['ville'],
			$jour.' '.date('d').' '.$mois.' '.date('Y'),
			$seance['Typeseance']['libelle'],
			$date_seance,
			$this->requestAction("/models/listeProjets/$id_seance/0"),
			$this->requestAction("/models/listeProjets/$id_seance/1")
		);
	        $generation = str_replace($search,$replace,$model['Model']['content']);
	        $pdf->WriteHTML($generation);
		$emailPdf->WriteHTML($generation);

                $pos =  strrpos ( getcwd(), 'webroot');
		$path = substr(getcwd(), 0, $pos);
		$convoc_path = $path."webroot/files/convocations/convoc_".$acteur['Acteur']['id'].".pdf";
		$emailPdf->Output($convoc_path ,'F');
		$this->sendConvoc($acteur['Acteur']['id'], $convoc_path, $seance['Typeseance']['libelle'], $date_seance);
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

	function generateOrdresDuJour ($id_seance = null) {
		$seance = $this->Seance->read(null, $id_seance);
		$acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
		$model = $this->Model->read(null, $seance['Typeseance']['modelordredujour_id']);
		$jour=$this->Date->days[intval(date('w'))];
		$mois=$this->Date->months[intval(date('m'))];
		$collectivite=  $this->Collectivite->findAll();
		$date_seance=  $this->Date->frenchDate(strtotime($seance['Seance']['date']));

		vendor('fpdf/html2fpdf');
		$pdf = new HTML2FPDF();

		$search = array(
			"#LOGO_COLLECTIVITE#",
			"#NOM_COLLECTIVITE#",
			"#ADRESSE_COLLECTIVITE#",
			"#CP_COLLECTIVITE#",
			"#VILLE_COLLECTIVITE#",
			"#TELEPHONE_COLLECTIVITE#",
			"#NOM_ACTEUR#",
			"#PRENOM_ACTEUR#",
			"#SALUTATION_ACTEUR#",
			"#TITRE_ACTEUR#",
			"#ADRESSE1_ACTEUR#",
			"#ADRESSE2_ACTEUR#",
			"#CP_ACTEUR#",
			"#VILLE_ACTEUR#",
			"#DATE_DU_JOUR#",
			"#TYPE_SEANCE#",
			"#DATE_SEANCE#",
			"#LISTE_PROJETS_SOMMAIRES#",
			"#LISTE_PROJETS_DETAILLES#"
		);

   		foreach($acteursConvoques as $acteur) {
			$pdf->AddPage();
			$replace = array(
				'<img src="files/image/logo.jpg">',
				$collectivite[0]['Collectivite']['nom'],
				$collectivite[0]['Collectivite']['adresse'],
				$collectivite[0]['Collectivite']['CP'],
				$collectivite[0]['Collectivite']['ville'],
				$collectivite[0]['Collectivite']['telephone'],
				$acteur['Acteur']['nom'],
				$acteur['Acteur']['prenom'],
				$acteur['Acteur']['salutation'],
				$acteur['Acteur']['titre'],
				$acteur['Acteur']['adresse1'],
				$acteur['Acteur']['adresse2'],
				$acteur['Acteur']['cp'],
				$acteur['Acteur']['ville'],
				$jour.' '.date('d').' '.$mois.' '.date('Y'),
				$seance['Typeseance']['libelle'],
				$date_seance,
				$this->requestAction("/models/listeProjets/$id_seance/0"),
				$this->requestAction("/models/listeProjets/$id_seance/1")
			);
			$generation = str_replace($search,$replace,$model['Model']['content']);
			$pdf->WriteHTML($generation);

    	}
		$pdf->Output('odj.pdf','D');

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
		$deliberations=$this->afficherProjets($seance_id, 0);
		$ToutesVotees = true;
		for ($i=0; $i<count($deliberations); $i++){
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
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

	function voter ($deliberation_id=null) {
		$seance_id = $this->requestAction('/deliberations/getCurrentSeance/'.$deliberation_id);

		if (empty($this->data)) {
			$donnees = $this->Vote->findAll("delib_id = $deliberation_id");
			foreach($donnees as $donnee){
				$this->data['Vote'][$donnee['Vote']['acteur_id']]=$donnee['Vote']['resultat'];
			    $this->data['Vote']['commentaire'] = $donnee['Vote']['commentaire'];
			}
			$this->set('deliberation' , $this->Deliberation->findAll("Deliberation.id=$deliberation_id"));
			$this->set('presents' , $this->requestAction('/deliberations/afficherListePresents/'.$deliberation_id));
		} else {
 			$pour = 0;
 			$abstenu = 0;
			$this->effacerVote($deliberation_id);
			$nb_votant = count($this->data['Vote']);
			foreach($this->data['Vote']as $acteur_id => $vote){
				if(is_numeric($acteur_id)==true){
					$this->Vote->create();
					$this->data['Vote']['acteur_id']=$acteur_id;
					$this->data['Vote']['delib_id']=$deliberation_id;
					$this->data['Vote']['resultat']=$vote;
					if ($vote == 3)
					     $pour ++;
					if (($vote == 2)||($vote == 4))
						$abstenu ++;

				    $this->Vote->save($this->data['Vote']);
				}
			}

			$this->data = $this->Deliberation->read(null, $deliberation_id);


			if ($pour >= (($nb_votant -$abstenu) /2))
			{
			     $this->data['Deliberation']['etat']=3;
			     $this->data['Deliberation']['num_delib'] = $this->requestAction("/compteurs/suivant/1");
			}

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


	function getFileData($fileName, $fileSize) {
		return fread(fopen($fileName, "r"), $fileSize);
	}

	function saisirDebatGlobal ($id = null) {

		if (empty($this->data)) {
			$this->data = $this->Seance->read(null, $id);
			$this->set('annexes',$this->Annex->findAll('Annex.seance_id='.$id.' AND type="A"'));
		} else{
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


        function generer ($seance_id, $model_id, $editable=0){
	    include ('vendors/progressbar.php');
            $cpt = 1;
            // Pr√©paration des r√©pertoires et URL pour la cr√©ation des fichiers
            $dyn_path = "/files/generee/seances/$seance_id/";
            $path = WEBROOT_PATH.$dyn_path;
	    $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$this->base.$dyn_path;
            $urlFiles =  'http://'.$_SERVER['HTTP_HOST'].$this->base.'/files/generee/modeles/';
	    if (!$this->Gedooo->checkPath($path))
                die("Webdelib ne peut pas ecrire dans le repertoire : $path");

            //Cr√©ation du model ott
            $content = $this->requestAction("/models/getModel/$model_id");
	    $data = $this->Model->read(null, $model_id);
	    $nomModel = $data['Model']['modele'];
            $model = $this->Gedooo->createFile($path,'model_'.$model_id, $content);

	    $data = $this->Seance->read(null, $seance_id);
            $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($data['Seance']['type_id']);
	    $nbActeurs = count($acteursConvoques);
	    $listFiles = array();
	    Initialize(200, 100,200, 30,'#000000','#FFCC00','#006699');

	    foreach ($acteursConvoques as $acteur ) {
	        //
                //*****************************************
                //* Cr√©ation du fichier XML de donn√©es    *
                //*****************************************
                // Informations sur la collectivit√©
                $this->Gedooo->createFile($path, 'logo.html', '<img src="'. 'http://'.$_SERVER['HTTP_HOST'].$this->base.'/files/image/logo.jpg" />');
                $dataColl = $this->Collectivite->read(null, 1);
                $balises  = $this->Gedooo->CreerBalise('nom_collectivite', $dataColl['Collectivite']['nom'], 'string');
                $balises .= $this->Gedooo->CreerBalise('adresse_collectivite', $dataColl['Collectivite']['adresse'], 'string');
                $balises .= $this->Gedooo->CreerBalise('cp_collectivite', $dataColl['Collectivite']['CP'], 'string');
                $balises .= $this->Gedooo->CreerBalise('ville_collectivite', $dataColl['Collectivite']['ville'], 'string');
                $balises .= $this->Gedooo->CreerBalise('telephone_collectivite', $dataColl['Collectivite']['telephone'], 'string');
                $balises .= $this->Gedooo->CreerBalise('logo_collectivite', $urlWebroot.'logo.html', 'content');

                // Informations sur l'acteur
                $balises .= $this->Gedooo->CreerBalise('type_acteur', $acteur['Typeacteur']['nom'], 'string');
                $balises .= $this->Gedooo->CreerBalise('type_commentaire_acteur', $acteur['Typeacteur']['commentaire'], 'string');
                $balises .= $this->Gedooo->CreerBalise('nom_acteur', $acteur['Acteur']['nom'], 'string');
                $balises .= $this->Gedooo->CreerBalise('prenom_acteur', $acteur['Acteur']['prenom'], 'string');
                $balises .= $this->Gedooo->CreerBalise('salutation_acteur', $acteur['Acteur']['salutation'], 'string');
                $balises .= $this->Gedooo->CreerBalise('titre_acteur', $acteur['Acteur']['titre'], 'string');
                $balises .= $this->Gedooo->CreerBalise('date_naissance_acteur', $acteur['Acteur']['date_naissance'], 'string');
                $balises .= $this->Gedooo->CreerBalise('adresse1_acteur', $acteur['Acteur']['adresse1'], 'string');
                $balises .= $this->Gedooo->CreerBalise('adresse2_acteur', $acteur['Acteur']['adresse2'], 'string');
                $balises .= $this->Gedooo->CreerBalise('cp_acteur', $acteur['Acteur']['cp'], 'string');
                $balises .= $this->Gedooo->CreerBalise('ville_acteur', $acteur['Acteur']['ville'], 'string');
                $balises .= $this->Gedooo->CreerBalise('email_acteur', $acteur['Acteur']['email'], 'string');
                $balises .= $this->Gedooo->CreerBalise('telfixe_acteur', $acteur['Acteur']['telfixe'], 'string');
                $balises .= $this->Gedooo->CreerBalise('telmobile_acteur', $acteur['Acteur']['telmobile'], 'string');
                $balises .= $this->Gedooo->CreerBalise('note_acteur', $acteur['Acteur']['note'], 'string');
                $balises .= $this->Gedooo->CreerBalise('position_acteur', $acteur['Acteur']['position'], 'string');

	        // Informations sur la seance
                $balises .= $this->Gedooo->CreerBalise('seance_id', $seance_id, 'string');
                // Informations sur la seance
	        if (isset($data['Seance']['date']))
                    $balises .= $this->Gedooo->CreerBalise('date_seance', $this->Date->frDate($data['Seance']['date']), 'date');
                $balises .= $this->Gedooo->CreerBalise('type_seance', $this->requestAction('/typeseances/getField/'.$data['Seance']['type_id'].'/libelle'), 'string');
                if (GENERER_DOC_SIMPLE==false){
                    $nameDebat = $data['Seance']['debat_global_name'];
                }
                else {
                   $nameDebat =  'debat.html';
                }

                //Cr√©ation du fichier des d√©bats globaux √† la s√©ance
                $this->Gedooo->createFile($path, $nameDebat, $data['Seance']['debat_global']);
                $balises .= $this->Gedooo->CreerBalise('debat_seance', $urlWebroot.$nameDebat, 'content');

	        // Cr√©ation de la liste des projets detailles
	        $listeProjetsDetailles = $this->requestAction("/models/listeProjets/$seance_id/1");
                $this->Gedooo->createFile($path, 'ProjetsDetailles.html',  $listeProjetsDetailles);
                $balises .= $this->Gedooo->CreerBalise('projets_detailles', $urlWebroot.'ProjetsDetailles.html', 'content');

	        // Cr√©ation de la liste des projets sommaires
	        $listeProjetsSommaires = $this->requestAction("/models/listeProjets/$seance_id/0");
                $this->Gedooo->createFile($path, 'ProjetsSommaires.html',  $listeProjetsSommaires);
                $balises .= $this->Gedooo->CreerBalise('projets_sommaires', $urlWebroot.'ProjetsSommaires.html', 'content');

                // cr√©ation du fichier XML
                $datas    = $this->Gedooo->createFile($path,'data.xml', $balises);

		// Envoi du fichier √† GEDOOo
                if ($editable == 0)
                    $extension = 'pdf';
                else
                    $extension = 'odt';
                $nomFichier =  $acteur['Acteur']['id'].'.'.$extension;
		$this->Gedooo->sendFiles($model, $datas, $editable, 1,  $nomFichier);

		// Cr√©ation d'un tableau pour l'affichage et le stockage des fichiers √† r√©cuperer
		$listFiles[$urlFiles.$nomFichier] = $acteur['Acteur']['prenom']." ".$acteur['Acteur']['nom'];
                ProgressBar($cpt*(100/$nbActeurs), 'Document g&eacute;n&eacute;r&eacute; pour : <b>'. $acteur['Acteur']['prenom']." ".$acteur['Acteur']['nom'].'</b>');
                $cpt++;
            }
	    $listFiles[$urlFiles.'documents.zip'] = 'Tous les documents';
	    $this->set('listFiles', $listFiles);
            $this->render();
        }

	function detailsAvis ($seance_id=null) {
		// initialisations
		$deliberations=$this->afficherProjets($seance_id, 0);
		$date_tmpstp = strtotime($this->GetDate($seance_id));
		$toutesVisees = true;

		for ($i=0; $i<count($deliberations); $i++){
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
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
			// En fonction de l'avis sÈlectionnÈ
			if (!array_key_exists('avis', $this->data['Deliberation'])) {
				$this->Seance->invalidate('avis');
			} elseif ($this->data['Deliberation']['avis'] == 2) {
				// DÈfavorable : le projet repasse en Ètat = 0
				$this->data['Deliberation']['etat'] = 0;
				unset($this->data['Deliberation']['seance_id']);
				$this->Deliberation->save($this->data);
				// ajout du commentaire
				$this->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
				$this->data['Commentaire']['texte'] = 'A reÁu un avis dÈfavorable en '
					. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$deliberation['Seance']['type_id'])
					. ' du ' . $deliberation['Seance']['date'];
				$this->Deliberation->Commentaire->save($this->data);

				$sortie = true;
			} elseif ($this->data['Deliberation']['avis'] == 1) {
				// Favorable : on attribue une nouvelle date de sÈance si elle est sÈlectionnÈe
				if (empty($this->data['Deliberation']['seance_id']))
					unset($this->data['Deliberation']['seance_id']);
				else
					$this->data['Deliberation']['position'] = $this->Deliberation->findCount("seance_id =".$this->data['Deliberation']['seance_id']." AND (etat != -1 )")+1;
				$this->Deliberation->save($this->data['Deliberation']);
				// ajout du commentaire
				$this->data['Commentaire']['delib_id'] = $this->data['Deliberation']['id'];
				$this->data['Commentaire']['texte'] = 'A reÁu un avis favorable en '
					. $this->Seance->Typeseance->field('Typeseance.libelle', 'Typeseance.id = '.$deliberation['Seance']['type_id'])
					. ' du ' . $deliberation['Seance']['date'];
				$this->Deliberation->Commentaire->save($this->data);

				$sortie = true;
			}

			$this->data = $deliberation;
		}
		if ($sortie)
			$this->redirect('/seances/detailsAvis/'.$seanceIdCourante);
		else {
			$this->data = $deliberation;
			$this->set('avis', array(1 => 'Favorable', 2 => 'DÈfavorable'));
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('seances', $this->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
		}
	}
}
?>
