<?php
class DeliberationsController extends AppController {
/*
 * Deliberation.etat = -1 : refusé
 *	Deliberation.etat = 0 : en cours de rédaction
 *  Deliberation.etat = 1 : dans un circuit
 * 	Deliberation.etat = 2 : validé
 *  Deliberation.etat = 3 : Voté pour
 * 	Deliberation.etat = 4 : Voté contre
 * 	Deliberation.etat = 5 : envoyé
 *
 *  Deliberation.avis = 0 ou null : pas d'avis donné
 *  Deliberation.avis = 1 : avis favorable
 *  Deliberation.avis = 2 : avis défavorable
 */
	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $uses = array('Acteur', 'Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Localisation','Seance', 'TypeSeance', 'Commentaire','Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Infosupdef');
	var $components = array('Gedooo','Date','Utils','Email','Acl');

	// Gestion des droits
	var $demandeDroit = array(
		'add',
		'mesProjetsRefuses',
		'mesProjetsRedaction',
		'mesProjetsValidation',
		'mesProjetsValides',
		'mesProjetsVotesOui',
		'mesProjetsVotesNon',
		'mesProjetsTransmis',
		'listerProjetsATraiter',
		'listerProjetsDansMesCircuits',
		'mesProjetsRecherche',
		'tousLesProjetsSansSeance',
		'tousLesProjetsValidation',
		'tousLesProjetsAFaireVoter',
		'tousLesProjetsRecherche',
		'editerProjetValide'
	);
	var $commeDroit = array(
		'view'=>array('Pages:mes_projets', 'Pages:tous_les_projets'),
		'edit'=>array('Deliberations:mesProjetsRedaction', 'Deliberations:editerProjetValide', 'Deliberations:listerProjetsATraiter'),
		'delete'=>'Deliberations:mesProjetsRedaction',
		'attribuercircuit'=>'Deliberations:mesProjetsRedaction',
		'addIntoCircuit'=>'Deliberations:mesProjetsRedaction',
		'traiter'=>'Deliberations:listerProjetsATraiter',
		'attribuerSeance'=>'Deliberations:tousLesProjetsSansSeance',
		'validerEnUrgence'=>'Deliberations:tousLesProjetsValidation'
	);
	var $libelleControleurDroit = 'Projets';
	var $libellesActionsDroit = array('editerProjetValide' => 'Editer projets valid&eacute;s');


	function listerProjetsDansMesCircuits() {
		/**
		 * TODO BUG SI UNE PERSONNE QUI APPARAIT a PLUSIEURS SERVICES APPARAIT PLUSIEURS FOIS DANS UN
		 * MEME CIRCUIT
		 * PB : si une personne apparait plusieurs fois dans le circuit mais sous des services diffÃ©rents
		 * A FAIRE : verifier aussi le service, voir si un meme user peut appartenir Ã  plusieurs services
		 * et apparaitre plusieurs fois dans le meme circuit
		 * CSQ : qui se connecte? un user ou un user service? remise en cause de la relation "un user
		 * peut appartenir Ã  plusieurs services
		 */
		//liste les projets apparais dans le circuit de validation
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		//recherche de tous les circuits ou apparait l'utilisateur logue
		$data_circuit=$this->UsersCircuit->findAll("user_id=$user_id", null, "UsersCircuit.position ASC");
		$conditions="etat=1 ";
		$delib=array();
		$cpt=0;

		if ($data_circuit!=null)
		{
			foreach ($data_circuit as $data)
			{
				if ($cpt>0)
					$conditions=$conditions." OR ";
				else
					$conditions=$conditions." AND (";

				$conditions=$conditions." circuit_id = ".$data['UsersCircuit']['circuit_id'];
				$cpt++;
			}
			if ($cpt>=0)
				$conditions=$conditions." )";

			$deliberations = $this->Deliberation->findAll($conditions);

			for ($i=0; $i<count($deliberations); $i++){
				if(!empty($deliberations[$i]['Seance']['date']))
		    		    $deliberations[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberations[$i]['Seance']['date']));
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
				$deliberations[$i]['Model']['id'] = $this->getModelId($deliberations[$i]['Deliberation']['id']);
			}

			foreach ($deliberations as $deliberation)
			{
                             if (isset($deliberation['Deliberation']['date_limite'])){
					$deliberation['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($deliberation['Deliberation']['date_limite']));
				}
				//on recupere la position courante de la deliberation
				$lastTraitement=array_pop($deliberation['Traitement']);
				$deliberation['positionDelib']=$lastTraitement['position'];

				//on recupere la position de l'user dans le circuit
				foreach ($data_circuit as $data)
				{
					if ($data['UsersCircuit']['circuit_id']==$lastTraitement['circuit_id'])
					{
						$position_user=$data['UsersCircuit']['position'];
						$deliberation['positionUser']=$position_user;
					}
				}
				// on n'affiche que les delib traitees ou qui sont en attente
				$deliberation['action']="view";
				$deliberation['act']="voir";

				if ($deliberation['positionUser'] < $deliberation['positionDelib'])
					{
						$deliberation['image']='/icons/fini.png';
						$deliberation['etat']="Trait&eacute";
						array_push($delib, $deliberation);
					}elseif ($deliberation['positionUser'] > $deliberation['positionDelib'])
					{
						$deliberation['image']='/icons/attente.png';
						$deliberation['etat']="En attente";
						array_push($delib, $deliberation);
					}
			}
		}
		$this->set('deliberations', $delib);
		$this->set('USE_GEDOOO', USE_GEDOOO);
	}

	function listerProjetsATraiter() {
		/**
		 * TODO BUG SI UNE PERSONNE QUI APPARAIT a PLUSIEURS SERVICES APPARAIT PLUSIEURS FOIS DANS UN
		 * MEME CIRCUIT
		 * PB : si une personne apparait plusieurs fois dans le circuit mais sous des services diffÃ©rents
		 * A FAIRE : verifier aussi le service, voir si un meme user peut appartenir Ã  plusieurs services
		 * et apparaitre plusieurs fois dans le meme circuit
		 * CSQ : qui se connecte? un user ou un user service? remise en cause de la relation "un user
		 * peut appartenir Ã  plusieurs services
		 */
		//liste les projets ou j'apparais dans le circuit de validation
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		//recherche de tous les circuits ou apparait l'utilisateur logue
		$data_circuit=$this->UsersCircuit->findAll("user_id=$user_id", null, "UsersCircuit.position ASC");
		$conditions="etat=1 ";
		$delib=array();
		$cpt=0;

		if ($data_circuit!=null)
		{
			foreach ($data_circuit as $data)
			{
				if ($cpt>0)
					$conditions=$conditions." OR ";
				else
					$conditions=$conditions." AND (";

				$conditions=$conditions."circuit_id = ".$data['UsersCircuit']['circuit_id'];
				$cpt++;
			}
			if ($cpt>=0)
				$conditions=$conditions.")";

			$deliberations = $this->Deliberation->findAll($conditions);


			for ($i=0; $i<count($deliberations); $i++){
				if(!empty($deliberations[$i]['Seance']['date']))
		    		$deliberations[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberations[$i]['Seance']['date']));
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($id_service);
				$deliberations[$i]['Model']['id'] = $this->getModelId( $deliberations[$i]['Deliberation']['id']);
			}

			foreach ($deliberations as $deliberation)
			{

				if (isset($deliberation['Deliberation']['date_limite']))
				    $deliberation['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($deliberation['Deliberation']['date_limite']));
				//on recupere la position courante de la deliberation
				$lastTraitement=array_pop($deliberation['Traitement']);
				// Le +1 pour compter le 0
				$posCourante = count($deliberation['Traitement'])+1;

				//on recupere la position de l'user dans le circuit
				foreach ($data_circuit as $data)
					if ($data['UsersCircuit']['circuit_id']==$lastTraitement['circuit_id']){
						$position_user=$data['UsersCircuit']['position'];
					}

				if (	$posCourante == $position_user  ){
					$deliberation['action'] = "traiter";
					$deliberation['act'] = "traiter";
					$deliberation['image']='icons/atraiter.png';
					array_push($delib, $deliberation);
				}
			}
		}
		$this->set('deliberations', $delib);
		$this->render('listerProjetsATraiter');
	}

	function getPosition($circuit_id, $delib_id) {
		$odjCourant=array();
		$conditions = "Traitement.circuit_id = $circuit_id AND Traitement.delib_id=$delib_id ";
        $objCourant = $this->Traitement->findAll($conditions, null, "Traitement.position DESC");
		return $objCourant['0']['Traitement']['position'];
	}

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
		$tab_anterieure=$this->chercherVersionAnterieure($id, $this->data, 0, $listeAnterieure, 'view');
		$this->set('tab_anterieure',$tab_anterieure);

		// Lecture des droits en modification
		$user_id = $this->Session->read('user.User.id');
		if ($this->Droits->check($user_id, "Deliberations:edit") &&
			$this->Deliberation->estModifiable($id, $user_id)
		)
			$this->set('userCanEdit', true);
		else
			$this->set('userCanEdit', false);

		// Lecture et initialisation des commentaires
		$commentaires = $this->Commentaire->findAll("delib_id =  $id");
		for($i=0; $i< count($commentaires) ; $i++) {
			$nomAgent = $this->requestAction("users/getNom/".$commentaires[$i]['Commentaire']['agent_id']);
			$prenomAgent = $this->requestAction("users/getPrenom/".$commentaires[$i]['Commentaire']['agent_id']);
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
		$this->set('user_circuit', $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit", null, 'UsersCircuit.position ASC'));
		// Définitions des infosup
		$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));

	}

	function getFileData($fileName, $fileSize) {
		return fread(fopen($fileName, "r"), $fileSize);
	}

	function saveLocation($id=null,$idLoc=0,$zone) 	{
		$this->layout = 'fckeditor';
		if($zone==1)
			$this->params['data']['Deliberation']['localisation1_id'] = $idLoc;
		elseif($zone==2)
			$this->params['data']['Deliberation']['localisation2_id'] = $idLoc;
		elseif($zone==3)
			$this->params['data']['Deliberation']['localisation3_id'] = $idLoc;

		$this->params['data']['Deliberation']['id'] = $id;

		if ($this->Deliberation->save($this->params['data'])){
			$this->redirect('/deliberations/changeLocation/'.$id);
		}
	}

	function getParent($id_loc) {
		if ($id_loc!=0){
		    $condition = "id = $id_loc";
		    $parent = $this->Localisation->findAll($condition);
		    if ($parent[0]['Localisation']['parent_id']==0)
		        return $id_loc;
		    else
		        return $parent[0]['Localisation']['parent_id'];
		}else
		    return 0;
	}

        function _hasNoSon ($id_loc) {
               $condition = "parent_id = $id_loc";
	       $result = $this->Localisation->findAll($condition);
               return (0 == count($result));
	}

	function changeLocation($id=null,$pzone1=0,$pzone2=0,$pzone3=0) {
		$this->layout = 'fckeditor';
		if(empty($this->data))
		{
			$data= $this->Deliberation->read(null,$id);
			$this->data = $this->Deliberation->read(null, $id);

			$this->set('id',$id);

			$conditions = "Localisation.parent_id= 0";
			$this->set('localisations', $this->Deliberation->Localisation->generateList($conditions));
			$selectedLocalisation1 =$this->getParent($this->data['Deliberation']['localisation1_id']);
			$this->set('selectedLocalisation1', $selectedLocalisation1);
			$selectedLocalisation2 =$this->getParent($this->data['Deliberation']['localisation2_id']);
			$this->set('selectedLocalisation2', $selectedLocalisation2);
			$selectedLocalisation3 =$this->getParent($this->data['Deliberation']['localisation3_id']);
			$this->set('selectedLocalisation3', $selectedLocalisation3);

			if (($pzone1 != $this->data['Deliberation']['localisation1_id']) AND ($this->_hasNoSon($pzone1)) ){
                            $this->data['Deliberation']['id'] = $id;
                            $this->data['Deliberation']['localisation1_id']= $pzone1;
			    if ($this->Deliberation->save($this->data))
                              $this->redirect('/deliberations/changeLocation/'.$id);
                        }
                        if (($pzone2 != $this->data['Deliberation']['localisation2_id']) AND ($this->_hasNoSon($pzone2)) ){
                            $this->data['Deliberation']['id'] = $id;
                            $this->data['Deliberation']['localisation2_id']= $pzone2;
                            if ($this->Deliberation->save($this->data))
                              $this->redirect('/deliberations/changeLocation/'.$id);
                        }
                       if (($pzone3 != $this->data['Deliberation']['localisation3_id']) AND ($this->_hasNoSon($pzone3)) ){
                            $this->data['Deliberation']['id'] = $id;
                            $this->data['Deliberation']['localisation3_id']= $pzone3;
                            if ($this->Deliberation->save($this->data))
                              $this->redirect('/deliberations/changeLocation/'.$id);
                        }

			if($pzone1!=0){
				$conditions = "Localisation.parent_id= $pzone1";
				$zone1 = $this->Localisation->generateList($conditions);
				$this->set('zone1',$zone1);
				$this->set('selectedLocalisation1',$pzone1);
			}else{
				if($selectedLocalisation1!=0){
					$conditions = "Localisation.parent_id= $selectedLocalisation1";
					$zone1 = $this->Localisation->generateList($conditions);
					$this->set('zone1',$zone1);
				}else{
					$this->set('zone1',0);
					$this->set('selectedzone1',0);
				}
			}

			if($pzone2!=0){
				$conditions = "Localisation.parent_id= $pzone2";
				$zone2 = $this->Localisation->generateList($conditions);
				$this->set('zone2',$zone2);
				$this->set('selectedLocalisation2',$pzone2);
			}else{
				if($selectedLocalisation2!=0){
					$conditions = "Localisation.parent_id= $selectedLocalisation2";
					$zone2 = $this->Localisation->generateList($conditions);
					$this->set('zone2',$zone2);
				}else{
					$this->set('zone2',0);
					$this->set('selectedzone2',0);
					$this->data['Deliberation']['localisation2_id']=0;
				}
			}

			if($pzone3!=0){
				$conditions = "Localisation.parent_id= $pzone3";
				$zone3 = $this->Localisation->generateList($conditions);
				$this->set('zone3',$zone3);
				$this->set('selectedLocalisation3',$pzone3);
			}else{
				if($selectedLocalisation3!=0){
					$conditions = "Localisation.parent_id= $selectedLocalisation3";
					$zone3 = $this->Localisation->generateList($conditions);
					$this->set('zone3',$zone3);
				}else{
					$this->set('zone3',0);
					$this->set('selectedzone3',0);
					$this->data['Deliberation']['localisation3_id']=0;
				}
			}
		}
		else{
			$this->data['Deliberation']['id']=$id;
			$this->Deliberation->save($this->data);
		}
	}

	function add() {
		/* initialisations */
		$this->Deliberation->create();
		$user=$this->Session->read('user');
		$this->data['Deliberation']['redacteur_id']=$user['User']['id'];
		$this->data['Deliberation']['service_id']=$user['User']['service'];

		$this->Deliberation->save($this->data);
		$this->redirect('/deliberations/edit/'.$this->Deliberation->getLastInsertId().'/1');
	}

	/* Supprime les projets de délibération de l'utilisateur connecté pour lesquels le titre et l'bjet sont vides */
	function checkEmptyDelib () {
	    $userId = $this->Session->read('user.User.id');
		$conditions = "Deliberation.objet = '' AND Deliberation.titre = '' AND Deliberation.redacteur_id = ".$userId;
		$delibs_vides = $this->Deliberation->findAll($conditions);
		foreach ($delibs_vides as $delib)
			$this->Deliberation->del($delib['Deliberation']['id']);
	}

	function textsynthese ($id = null) {
	 $this->layout = 'fckeditor';
	 $this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="S"'));

	if (empty($this->data)) {
        $this->data = $this->Deliberation->read(null, $id);
	 $this->set('delib', $this->data);
	}
    else {
	     if (isset($this->data['Deliberation']['texte_doc'])){
                if ($this->data['Deliberation']['texte_doc']['size']!=0){
                    $this->data['Deliberation']['texte_synthese_name'] = $this->data['Deliberation']['texte_doc']['name'];
                    $this->data['Deliberation']['texte_synthese_size'] = $this->data['Deliberation']['texte_doc']['size'];
                    $this->data['Deliberation']['texte_synthese_type'] = $this->data['Deliberation']['texte_doc']['type'];
                    $this->data['Deliberation']['texte_synthese']      = $this->getFileData($this->data['Deliberation']['texte_doc']['tmp_name'], $this->data['Deliberation']['texte_doc']['size']);
                    $this->Deliberation->save($this->data);
                     unset($this->data['Deliberation']['texte_doc']);
                 }
             }
	     $this->data['Deliberation']['id']=$id;
	     if(!empty($this->params['form'])) {
	        $form = $this->params['form'];
		 $deliberation = array_shift($this->params['form']);
		 $annexes = $this->params['form'];
		 $uploaded = true;
	         $size = count($this->params['form']);
		 $counter = 1;

		 while($counter <= ($size/2)) {
		     if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name'])) {
	    		$uploaded = false;
	         }
                    $counter++;
 	    }

				if($uploaded) {
					if ($this->Deliberation->save($this->data)) {
						$counter = 1;
						while($counter <= ($size/2)) {
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $id;
							$this->data['Annex']['seance_id'] = 0;
							$this->data['Annex']['titre'] = $form['titre_'.$counter];
							$this->data['Annex']['type'] = 'S';
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
						$this->redirect('/deliberations/textsynthese/'.$id);

					} else {
					$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					}
				}
			}
		}
	}

	function deliberation ($id = null) {
		$this->layout = 'fckeditor';
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="D"'));

		if (empty($this->data)) {
		    $this->data = $this->Deliberation->read(null, $id);
                    $this->set('delib', $this->data);
		} else{
                    if (isset($this->data['Deliberation']['texte_doc'])){
                        if ($this->data['Deliberation']['texte_doc']['size']!=0){
                            $this->data['Deliberation']['deliberation_name'] = $this->data['Deliberation']['texte_doc']['name'];
                            $this->data['Deliberation']['deliberation_size'] = $this->data['Deliberation']['texte_doc']['size'];
                            $this->data['Deliberation']['deliberation_type'] = $this->data['Deliberation']['texte_doc']['type'];
                            $this->data['Deliberation']['deliberation']      = $this->getFileData($this->data['Deliberation']['texte_doc']['tmp_name'], $this->data['Deliberation']['texte_doc']['size']);
                            $this->Deliberation->save($this->data);
                            unset($this->data['Deliberation']['texte_doc']);
                         }
                    }
	                $this->data['Deliberation']['id']=$id;
			if(!empty($this->params['form']))
			{
			        $form = $this->params['form'];
				$deliberation = array_shift($this->params['form']);
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
					if ($this->Deliberation->save($this->data)) {
					$counter = 1;

						while($counter <= ($size/2)) {
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $id;
							$this->data['Annex']['seance_id'] = 0;
							$this->data['Annex']['titre'] = $form['titre_'.$counter];
							$this->data['Annex']['type'] = 'D';
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
						$this->redirect('/deliberations/deliberation/'.$id);
					} else {
						$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					}
				}
			}
		}
	}


	function getFileType($id=null, $file) {
		$condition = "Deliberation.id = $id";
       	$objCourant = $this->Deliberation->findAll($condition);
		return $objCourant['0']['Deliberation'][$file."_type"];
	}

	function getFileName($id=null, $file) {
		$condition = "Deliberation.id = $id";
       	$objCourant = $this->Deliberation->findAll($condition);
		return $objCourant['0']['Deliberation'][$file."_name"];
	}

	function getSize($id=null, $file) {
		$condition = "Deliberation.id = $id";
       	$objCourant = $this->Deliberation->findAll($condition);
		return $objCourant['0']['Deliberation'][$file."_size"];
	}

	function getData($id=null, $file) {
		$condition = "Deliberation.id = $id";
       	$objCourant = $this->Deliberation->findAll($condition);
		return $objCourant['0']['Deliberation'][$file];
	}

	function download($id=null, $file){
		header('Content-type: '.$this->getFileType($id, $file));
		header('Content-Length: '.$this->getSize($id, $file));
		header('Content-Disposition: attachment; filename='.$this->getFileName($id, $file));
		echo $this->getData($id, $file);
		exit();
	}

	function textprojet ($id = null) {
		$this->layout = 'fckeditor';
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="P"'));

		if (empty($this->data)) {
		    $this->data = $this->Deliberation->read(null, $id);
		    $this->set('delib', $this->data);
		    $dyn_path = "/files/generee/fd/null/".$this->data['Deliberation']['id']."/";
		    $path = WEBROOT_PATH.$dyn_path;
		    if (empty( $this->data['Deliberation']['texte_projet_name']))
		         $this->data['Deliberation']['texte_projet_name'] = 'vide.html';
		    if (!empty( $this->data['Deliberation']['texte_projet']))
		        $this->Gedooo->createFile($path, $this->data['Deliberation']['texte_projet_name'], $this->data['Deliberation']['texte_projet']);
		    $this->set('url', $dyn_path.$this->data['Deliberation']['texte_projet_name']);
		} else{
	             if (isset($this->data['Deliberation']['texte_doc'])){
                         if ($this->data['Deliberation']['texte_doc']['size']!=0){
                             $this->data['Deliberation']['texte_projet_name'] = $this->Utils->strtocamel($this->data['Deliberation']['texte_doc']['name']);
                             $this->data['Deliberation']['texte_projet_size'] = $this->data['Deliberation']['texte_doc']['size'];
                             $this->data['Deliberation']['texte_projet_type'] = $this->data['Deliberation']['texte_doc']['type'];
                             $this->data['Deliberation']['texte_projet']      = $this->getFileData($this->data['Deliberation']['texte_doc']['tmp_name'], $this->data['Deliberation']['texte_doc']['size']);
                             $this->Deliberation->save($this->data);
                             unset($this->data['Deliberation']['texte_doc']);
                         }
                     }

                         $this->data['Deliberation']['id']=$id;
			if(!empty($this->params['form']))
			{
				$form = $this->params['form'];
				$deliberation = array_shift($this->params['form']);
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
			            if ($this->Deliberation->save($this->data)) {
					$counter = 1;
					while($counter <= ($size/2)) {
				            $this->data['Annex']['id'] = null;
					    $this->data['Annex']['deliberation_id'] = $id;
					    $this->data['Annex']['seance_id'] = 0;
					    $this->data['Annex']['titre'] = $form['titre_'.$counter];
					    $this->data['Annex']['type'] = 'P';
					    $this->data['Annex']['filename'] = $annexes['file_'.$counter]['name'];
					    $this->data['Annex']['filetype'] = $annexes['file_'.$counter]['type'];
					    $this->data['Annex']['size'] = $annexes['file_'.$counter]['size'];
					    $this->data['Annex']['data'] = $this->getFileData($annexes['file_'.$counter]['tmp_name'], $annexes['file_'.$counter]['size']);
					    if(!$this->Annex->save($this->data)){
				                echo "pb de sauvegarde de l\'annexe ".$counter;
					    }
					    $counter++;
					}
					$this->redirect('/deliberations/textprojet/'.$id);
				    } else {
				        $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				    }
				}
			}
		}
	}

	function PositionneDelibsSeance($seance_id, $position) {
		$conditions= "Deliberation.seance_id = $seance_id AND Deliberation.position > $position ";
		$delibs = $this->Deliberation->findAll($conditions);
		foreach ($delibs as $delib) {
			// on enleve pour 1 la delib qui a change de seance..
			$delib['Deliberation']['position']= $delib['Deliberation']['position'] -1;
			$this->Deliberation->save($delib['Deliberation']);
		}
	}

	function edit($id=null, $nouveau=false) {
	    $user=$this->Session->read('user');
	    /* initialisation du lien de redirection */
		if ($this->Acl->check($user['User']['id'], "Pages:mes_projets"))
			$redirect = '/pages/mes_projets';
		elseif ($this->Acl->check($user['User']['id'], "Pages:tous_les_projets"))
			$redirect = '/pages/tous_les_projets';
		else
			$redirect = '/';

		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);

			/* teste si le projet est modifiable par l'utilisateur connecté */
			if (!$this->Deliberation->estModifiable($id, $user['User']['id']) &&
				!($this->data['Deliberation']['etat'] == 2 && $this->Acl->check($user['User']['id'], "Deliberations:editerProjetValide"))
			) {
				$this->Session->setFlash("Vous ne pouvez pas editer le projet '$id'.");
				$this->redirect($redirect);
			}

			$this->data['Infosup'] = $this->Deliberation->Infosup->compacte($this->data['Infosup']);

			$this->data['Deliberation']['date_limite'] = date("d/m/Y",(strtotime($this->data['Deliberation']['date_limite'])));

			$this->set('titreFormulaire', $nouveau ? 'Nouveau projet' : 'Modification du projet');
			$this->set('servEm', $this->Deliberation->Service->doList($this->data['Service']['id']));
			$this->set('deliberation', $this->data);
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="G"'));
			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus('nom'));
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateList());
			$this->set('infosupdefs', $this->Infosupdef->findAll('', array(), 'ordre', null, 1, -1));
			$this->set('redirect', $redirect);
			$this->render();

		} else {
			$oldDelib =  $this->Deliberation->read(null, $id);
			// Si on change une delib de seance, il faut reclasser toutes les delibs de l'ancienne seance...
			if ((($oldDelib['Deliberation']['seance_id'] != 0) AND ($oldDelib['Deliberation']['seance_id'] != null)) AND (($oldDelib['Deliberation']['seance_id'] != $this->data['Deliberation']['seance_id']) AND ($this->data['Deliberation']['seance_id'] != null))){
                            $this->PositionneDelibsSeance($oldDelib['Deliberation']['seance_id'], $oldDelib['Deliberation']['position'] );
			}
			// Si on definie une seance a une delib, on la position en derniere position de la seance...
			 if (($this->data['Deliberation']['seance_id'])!=null )
                             $this->data['Deliberation']['position'] = $this->getLastPosition($this->data['Deliberation']['seance_id']);

			$this->data['Deliberation']['id']=$id;
			$this->data['Deliberation']['date_limite']= $this->Utils->FrDateToUkDate($this->params['form']['date_limite']);
		        unset($this->params['form']['date_limite']);
			$this->data['Deliberation']['redacteur_id']=$user['User']['id'];
			$this->data['Deliberation']['service_id']=$user['User']['service'];

			$this->cleanUpFields();

			if(!empty($this->params['form']))
			{
				$deliberation = array_shift($this->params['form']);
				$annexes = $this->params['form'];

				$uploaded = true;
				$size = count($this->params['form']);
				$counter = 1;

				while($counter <= ($size/2))
				{
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name']))
						$uploaded = false;
					$counter++;
				}

				if($uploaded)
				{

					if ($this->Deliberation->save($this->data))
					{
						/* sauvegarde des informations supplémentaires */
						if (array_key_exists('Infosup', $this->data))
							$this->Deliberation->Infosup->saveCompacted($this->data['Infosup'], $this->data['Deliberation']['id']);

						$delib_id = $id;
						$counter = 1;

						while($counter <= ($size/2)){
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $delib_id;
							$this->data['Annex']['seance_id'] = 0;
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
							$this->data['Annex']['type'] = 'G';
							$this->data['Annex']['filename'] = $annexes['file_'.$counter]['name'];
							$this->data['Annex']['filetype'] = $annexes['file_'.$counter]['type'];
							$this->data['Annex']['size'] = $annexes['file_'.$counter]['size'];
							$this->data['Annex']['data'] = $this->getFileData($annexes['file_'.$counter]['tmp_name'], $annexes['file_'.$counter]['size']);
							if(!$this->Annex->save($this->data))
								echo "pb de sauvegarde de l\'annexe ".$counter;

							$counter++;
						}
						$this->redirect($redirect);
					} else {
						$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
						$this->set('services', $this->Deliberation->Service->generateList());
						$this->set('themes', $this->Deliberation->Theme->generateList());
						$this->set('circuits', $this->Deliberation->Circuit->generateList());
						$this->set('datelim',$this->data['Deliberation']['date_limite']);
						$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="G"'));
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
					}
				}
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
			$this->set('user_circuit', $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit", null, 'UsersCircuit.position ASC'));
		}
	}

	function getTextSynthese ($id) {
            $condition = "Deliberation.id = $id";
	    $fields = "texte_synthese";
	    $dataValeur = $this->Deliberation->findAll($condition, $fields);
	    return $dataValeur['0'] ['Deliberation']['texte_synthese'];
	}

	function getTextProjet ($id) {
	    $condition = "Deliberation.id = $id";
	    $fields = "texte_projet";
	    $dataValeur = $this->Deliberation->findAll($condition, $fields);
	    return $dataValeur['0'] ['Deliberation']['texte_projet'];
	}

	function getField($id = null, $field =null) {
	    $condition = "Deliberation.id = $id";
	    $dataValeur = $this->Deliberation->findAll($condition, $field);
	    if(!empty ($dataValeur['0']['Deliberation'][$field]))
	   		return $dataValeur['0'] ['Deliberation'][$field];
	   	else
	   		return '';
	}

        function getUrlFile ($name) {
            return URL_FILES.$name;
        }

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation');
			$this->redirect('/deliberations/mesProjetsRedaction');
		}
		if ($this->Deliberation->del($id)) {
			$this->Session->setFlash('La deliberation a &eacute;t&eacute; supprim&eacute;e.');
			$this->redirect('/deliberations/mesProjetsRedaction');
		}
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
					//debug($traite);
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
				foreach($listeUsers as $user)
					$this->notifierInsertionCircuit($id, $user['User']['id']);

				$this->redirect('/deliberations/mesProjetsRedaction');
			} else
				$this->Session->setFlash('Probleme de sauvegarde.');
    	}else{
    		$this->Session->setFlash('Vous devez assigner un circuit a la deliberation	.');
    		$this->redirect('/deliberations/recapitulatif/'.$id);
    	}
    }

	function changeCircuit ($delib_id, $circuit_id) {
	    $traitements = $this->Traitement->findAll("delib_id =$delib_id ");
	    foreach($traitements as $traitement ){
	        $this->Traitement->delete($traitement['Traitement']['id']);
	    }
    }

	function attribuercircuit ($id = null, $circuit_id=null) {
		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);

			$this->set('lastPosition', '-1');
			$listeUsers['id']=array();
			$listeUsers['nom']=array();
			$listeUsers['prenom']=array();
			$listeUserCircuit['id']=array();
	       	$listeUserCircuit['circuit_id']=array();
	       	$listeUserCircuit['libelle']=array();
	       	$listeUserCircuit['user_id']=array();
	       	$listeUserCircuit['nom']=array();
	       	$listeUserCircuit['prenom']=array();
	       	$listeUserCircuit['service_id']=array();
	       	$listeUserCircuit['position']=array();
	       	$listeUserCircuit['service_libelle']=array();
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
			    $condition = "UsersCircuit.circuit_id = $circuit_id";
			    $desc = 'UsersCircuit.position ASC';

    	   		$tmplisteUserCircuit = $this->UsersCircuit->findAll($condition, null, $desc);

    	   		for ($i=0; $i<count($tmplisteUserCircuit);$i++) {
    	   			array_push($listeUserCircuit['id'], $tmplisteUserCircuit[$i]['UsersCircuit']['id']);
    	   			array_push($listeUserCircuit['circuit_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['circuit_id']);
    	   			array_push($listeUserCircuit['libelle'], $tmplisteUserCircuit[$i]['Circuit']['libelle']);
    	   			array_push($listeUserCircuit['user_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['user_id']);
    	   			array_push($listeUserCircuit['nom'], $tmplisteUserCircuit[$i]['User']['nom']);
    	   			array_push($listeUserCircuit['prenom'], $tmplisteUserCircuit[$i]['User']['prenom']);
    	   			array_push($listeUserCircuit['service_libelle'], $tmplisteUserCircuit[$i]['Service']['libelle']);
    	   			array_push($listeUserCircuit['service_id'], $tmplisteUserCircuit[$i]['UsersCircuit']['service_id']);
    	   			array_push($listeUserCircuit['position'], $tmplisteUserCircuit[$i]['UsersCircuit']['position']);
    	   		}

  				$this->set('listeUserCircuit', $listeUserCircuit);
  			}else
				$this->set('circuit_id','0');

			$this->set('circuits', $circuits);
		} else {
			$this->data['Deliberation']['id']=$id;
			$old = $this->Deliberation->findAll("Deliberation.id=$id");

			if($old['0']['Deliberation']['circuit_id'] != $circuit_id )
				$this->changeCircuit($id, $circuit_id);

			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/deliberations/recapitulatif/'.$id);
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
	}


	function traiter($id = null, $valid=null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation.');
			$this->redirect('/deliberations/listerProjetsATraiter');
		}
		else
		{
			if ($valid==null)
			{
				$nb_recursion=0;
				$action='view';
				$listeAnterieure=array();
				$tab_delib=$this->Deliberation->find("Deliberation.id = $id");
				$tab_anterieure=$this->chercherVersionAnterieure($id, $tab_delib, $nb_recursion, $listeAnterieure, $action);
				$this->set('tab_anterieure',$tab_anterieure);
				$commentaires = $this->Commentaire->findAll("delib_id = $id and pris_en_compte = 0", null, "created ASC");
				for($i=0; $i< count($commentaires) ; $i++) {
					$nomAgent = $this->requestAction("users/getNom/".$commentaires[$i]['Commentaire']['agent_id']);
					$prenomAgent = $this->requestAction("users/getPrenom/".$commentaires[$i]['Commentaire']['agent_id']);
					$commentaires[$i]['Commentaire']['nomAgent'] = $nomAgent;
					$commentaires[$i]['Commentaire']['prenomAgent'] = $prenomAgent;
				}
				$this->set('commentaires', $commentaires);
				$deliberation= $this->Deliberation->read(null, $id);
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
				$this->set('user_circuit', $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit",null,'UsersCircuit.position ASC'));

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
						    		$position = $this->getLastPosition($this->data['Deliberation']['seance_id']);
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
					$lastposcircuit=count($this->UsersCircuit->findAll("circuit_id = $circuit_id"));

					if ($lastposcircuit==$lastposprojet) //on est sur la derniere personne, on va faire sortir le projet du workflow et le passer au service des assemblees
					{
						// passage au service des assemblee : etat dans la table deliberations passea2
						$tab=$this->Deliberation->findAll("Deliberation.id = $id");
						$this->data['Deliberation']['etat']=2;
						$this->data['Deliberation']['id']=$id;
						$this->Deliberation->save($this->data['Deliberation']);
						$this->redirect('/deliberations/listerProjetsATraiter');
					}
					else
					{
						$this->notifierDossierAtraiter($circuit_id, $tab[$lastpos]['Traitement']['position']+1, $id);
						//sinon on fait passerala personne suivante
						$this->data['Traitement']['id']='';
						$this->data['Traitement']['position']=$tab[$lastpos]['Traitement']['position']+1;
						$this->data['Traitement']['delib_id']=$id;
						$this->data['Traitement']['circuit_id']=$circuit_id;
						$this->Traitement->save($this->data['Traitement']);
						$this->redirect('/deliberations/listerProjetsATraiter');
					}
				}
				else
				{
					$tab=$this->Traitement->findAll("delib_id = $id", null, "id ASC");
					$lastpos=count($tab)-1;

					//MAJ de la date de traitement de la derniere position courante $lastpos
					$tab[$lastpos]['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
					$this->Traitement->save($tab[$lastpos]['Traitement']);

					$this->data['Traitement']['id']='';
					//maj de la table traitements
					$this->data['Traitement']['position']=0;
					$circuit_id=$tab[$lastpos]['Traitement']['circuit_id'];
					$this->data['Traitement']['delib_id']=$id;
					$this->data['Traitement']['circuit_id']=$circuit_id;
					$this->Traitement->save($this->data['Traitement']);

					//TODO notifier par mail toutes les personnes qui ont deja vise le projet
					$condition = "circuit_id = $circuit_id";
					$listeUsers = $this->UsersCircuit->findAll($condition);
					foreach($listeUsers as $user)
						$this->notifierDossierRefuse($id, $user['User']['id']);

					//maj de l'etat de la delib dans la table deliberations
					$tab=$this->Deliberation->findAll("Deliberation.id = $id");
					$this->data['Deliberation']['etat']=-1; //etat -1 : refuse

				    // Retour de la position a 0 pour ne pas qu'il y ait de confusion
					$this->data['Deliberation']['position']=0;
					$this->data['Deliberation']['id']=$id;
					$this->Deliberation->save($this->data['Deliberation']);

					//enregistrement d'une nouvelle delib
					$delib['Deliberation']=$tab[0]['Deliberation'];
					$delib['Deliberation']['id']='';
					$delib['Deliberation']['etat']=0;
					$delib['Deliberation']['anterieure_id']=$id;
					$delib['Deliberation']['date_envoi']=0;
					//$delib['Deliberation']['circuit_id']=0;
					$delib['Deliberation']['created']='';
					$delib['Deliberation']['modified']='';
					$this->Deliberation->save($delib['Deliberation']);

					$this->redirect('/deliberations/listerProjetsATraiter');
				}
			}
		}
	}

	function chercherVersionAnterieure($delib_id, $tab_delib, $nb_recursion, $listeAnterieure, $action)
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
			$listeAnterieure=$this->chercherVersionAnterieure($anterieure_id, $ant, $nb_recursion+1, $listeAnterieure, $action);
		}
		return $listeAnterieure;
	}

    function transmit($id=null, $message= null){
       if (!empty( $message))
             $this->set('message', $message);

        $this->set('USE_GEDOOO', USE_GEDOOO);
        $this->set('dateClassification', $this->getDateClassification());
        $this->set('tabNature',          $this->getNatureListe());
        $this->set('tabMatiere',         $this->getMatiereListe());
        // On affiche que les delibs vote pour.
        $deliberations =   $this->Deliberation->findAll("Deliberation.etat=3 OR Deliberation.etat=5 ");

        for($i = 0; $i < count($deliberations); $i++) {
        	$deliberations[$i]['Deliberation'][$deliberations[$i]['Deliberation']['id'].'_num_pref'] = $deliberations[$i]['Deliberation']['num_pref'];
        	$deliberations[$i]['Model']['id'] = $this->getModelId($deliberations[$i]['Deliberation']['id']);
        }

        $this->set('deliberations', $deliberations);
    }

    function getNatureListe(){
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
		$this->set('classification',$this->getMatiereListe());
	}

    function getMatiereListe(){

 		$tab = array();
		$xml = simplexml_load_file(FILE_CLASS);
		$namespaces = $xml->getDocNamespaces();
		$xml=$xml->children($namespaces["actes"]);


		foreach ($xml->Matieres->children($namespaces["actes"]) as $matiere1) {
			$mat1=$this->object2array($matiere1);
			$tab[$mat1['@attributes']['CodeMatiere']] = utf8_decode($mat1['@attributes']['Libelle']);
    		foreach ($matiere1->children($namespaces["actes"]) as $matiere2) {
    			$mat2=$this->object2array($matiere2);
    			$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere']] = utf8_decode($mat2['@attributes']['Libelle']);
        		foreach ($matiere2->children($namespaces["actes"]) as $matiere3) {
        			$mat3=$this->object2array($matiere3);
    				$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere'].'.'.$mat3['@attributes']['CodeMatiere']] = utf8_decode($mat3['@attributes']['Libelle']);
        			foreach ($matiere3->children($namespaces["actes"]) as $matiere4) {
        				$mat4=$this->object2array($matiere4);
    					$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere'].'.'.$mat3['@attributes']['CodeMatiere'].'.'.$mat4['@attributes']['CodeMatiere']] = utf8_decode($mat4['@attributes']['Libelle']);
        				foreach ($matiere4->children($namespaces["actes"]) as $matiere5) {
                			$mat5=$this->object2array($matiere5);
    						$tab[$mat1['@attributes']['CodeMatiere'].'.'.$mat2['@attributes']['CodeMatiere'].'.'.$mat3['@attributes']['CodeMatiere'].'.'.$mat4['@attributes']['CodeMatiere'].'.'.$mat5['@attributes']['CodeMatiere']] = utf8_decode($mat5['@attributes']['Libelle']);
        				}
        			}
				}
			}
		}
        return $tab;
	}

	function object2array($object){
   		$return = NULL;
    	if(is_array($object)) {
        	foreach($object as $key => $value)
           		$return[$key] = $this->object2array($value);
    	}
    	else{
        	$var = get_object_vars($object);
        	if($var)
        	{
            	foreach($var as $key => $value)
               		$return[$key] = $this->object2array($value);
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
		    $this->changeClassification($delib_id, $classification);
		    $class1 = substr($classification , 0, strpos ($classification , '.' ));
		    $rest = substr($classification , strpos ($classification , '.' )+1, strlen($classification));
		    $class2=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class3=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class4=substr($rest , 0, strpos ($classification , '.' ));
		    $rest = substr($rest , strpos ($classification , '.' )+1, strlen($rest));
		    $class5=substr($rest , 0, strpos ($classification , '.' ));
                    if (!USE_GEDOOO) {
		        $file = $path."webroot/files/delibs/DELIBERATION_$delib_id.pdf";
		        if (!file_exists($file))
		            $err = $this->requestAction("/postseances/generateDeliberation/$delib_id");
		    }
		    else {
			$model_id = $this->getModelId($delib_id);
			$err = $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/D_$delib_id.pdf");
		        $file =  WEBROOT_PATH."/files/generee/fd/null/$delib_id/D_$delib_id.pdf";
		   }
                    ProgressBar($nbEnvoyee*(100/$nbDelibAEnvoyer), 'Document G&eacute;n&eacute;r&eacute; ');
		    $delib = $this->Deliberation->findAll("Deliberation.id = $delib_id");
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
			$pj_file = $this->Gedooo->createFile($path."webroot/files/generee/fd/null/$delib_id/", $annexe['filename'], $annexe['data']);
			$data["acte_attachments[$nb_pj]"] = "@$pj_file";
      	                $data["acte_attachments_sign[$nb_pj]"] = "";
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

			 $pos = strpos($curl_return, 'OK');
			 if ($pos === false) {
                              echo ('<script>');
                              echo ('    document.getElementById("pourcentage").style.display="none"; ');
                              echo ('    document.getElementById("progrbar").style.display="none";');
                              echo ('    document.getElementById("affiche").style.display="none";');
                              echo ('    document.getElementById("contTemp").style.display="none";');
                              echo ('</script>');
			      echo($curl_return);
			      die ('<br /><a href ="/deliberations/transmit"> Retour &agrave; la page pr&eacute;c&eacute;dente </a>');
                         }
			 else {
                              ProgressBar($nbEnvoyee*(100/$nbDelibAEnvoyer), 'Delib&eacute;ration '.$delib[0]['Deliberation']['num_delib'].' envoy&eacute;e ');
                              $nbEnvoyee ++;
			      $this->changeEtat($delib_id, '5');
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
			      die ('<br /><a href ="/deliberations/transmit"> Retour &agrave; la page pr&eacute;c&eacute;dente </a>');
		}

		function changeEtat($delib_id, $etat){
			$this->data = $this->Deliberation->read(null, $delib_id);
			$this->data['Deliberation']['id']=$delib_id;
			$this->data['Deliberation']['etat'] = $etat;
			$this->Deliberation->save($this->data);
		}

		function changeSeance($delib_id, $seance_id){
			$this->data = $this->Deliberation->read(null, $delib_id);
			$this->data['Deliberation']['id']=$delib_id;
			$this->data['Deliberation']['seance_id'] = $seance_id;
			$this->Deliberation->save($this->data);
		}

		function changeClassification($delib_id, $classification){
			$this->data = $this->Deliberation->read(null, $delib_id);
			$this->data['Deliberation']['id']=$delib_id;
			$this->data['Deliberation']['num_pref'] = $classification;
			$this->Deliberation->save($this->data);
		}

       function getDateClassification(){
	   $doc = new DOMDocument();
           if(!$doc->load(FILE_CLASS))
               die("Error opening xml file");
     //      return($doc->getElementsByTagName('DateClassification')->item(0)->nodeValue);
           return true;
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
        	$positionCourante = $this->getCurrentPosition($id);
	   	$lastPosition = $this->getLastPosition($seance_id);
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
			if ($sortby == 'theme_id') $sortby = 'Theme.order';
			elseif ($sortby == 'rapporteur_id') $sortby = 'Rapporteur.nom';
  		    $deliberations = $this->Deliberation->findAll($condition,null, "$sortby ASC");
		    for($i=0; $i<count($deliberations); $i++){
			    $deliberations[$i]['Deliberation']['position']=$i+1;
		    	$this->Deliberation->save($deliberations[$i]['Deliberation']);
		    }
		    $this->redirect("seances/afficherProjets/$seance_id");
	    }

        function getCurrentPosition($id){
    		$conditions = "Deliberation.id = $id";
    		$field = 'Deliberation.position';
    		$obj = $this->Deliberation->findAll($conditions);

    		return  $obj['0']['Deliberation']['position'];
  		}

   		function getCurrentSeance($id) {
			$condition = "Deliberation.id = $id";
        	$objCourant = $this->Deliberation->findAll($condition);
			return $objCourant['0']['Deliberation']['seance_id'];
    	}

   		function getLastPosition($seance_id) {
			return count($this->Deliberation->findAll("seance_id =$seance_id AND (etat != -1 )"))+1;
    	}

	function getNextId() {
		$tmp = $this->Deliberation->findAll('Deliberation.id in (select max(id) from deliberations)');
		return $tmp['0']['Deliberation']['id'] +1 ;
	}

    function getRapporteur($id_delib){
    	$condition= "Deliberation.id=$id_delib";
    	$deliberation = $this->Deliberation->findAll($condition);
    	if (!empty ($deliberation[0]['Rapporteur']['id']))
    		return $deliberation[0]['Rapporteur']['id'];
    	else
    		return null;
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

        function notifierDossierAtraiter($circuit_id, $pos, $delib_id){
            $conditions = "UsersCircuit.circuit_id=$circuit_id and UsersCircuit.position=$pos";
            $data = $this->UsersCircuit->findAll($conditions);
            // Si l'utilisateur accepte les mails
            if ($data['0']['User']['accept_notif']){
                $to_mail = $data['0']['User']['email'];
                $this->Email->template = 'email/traiter';
                $this->set('data',  $this->paramMails('traiter', $this->Deliberation->read(null, delib_id),  $data['0']['User']));
                $this->Email->to = $to_mail;
                $this->Email->subject = "DELIB $delib_idatraiter";
                $result = $this->Email->send();
            }
	}

        function notifierDossierRefuse($delib_id,$user_id){
            $condition = "Deliberation.id = $delib_id";
            $data = $this->Deliberation->findAll($condition);
            $redacteur_id = $data['0']['Deliberation']['redacteur_id'];
            $data_comm = $this->Commentaire->findAll("delib_id = $delib_id");

            $condition = "User.id = $user_id";
            $data = $this->User->findAll($condition);

            // Si l'utilisateur accepte les mails
            if ($data['0']['User']['accept_notif']){
                $this->Email->template = 'email/refuse';
                $this->set('data', $this->paramMails('refus', $this->Deliberation->read(null, $delib_id),  $data['0']['User']));
                $this->Email->to =  $data['0']['User']['email'];
                $this->Email->subject = "DELIB $delib_id Refusee !";
                $result = $this->Email->send();
            }
        }

        function notifierInsertionCircuit ($delib_id, $user_id) {
            $condition = "User.id = $user_id";
            $data = $this->User->findAll($condition);

            // Si l'utilisateur accepte les mails
            if ($data['0']['User']['accept_notif']){
                $this->Email->template = 'email/circuit';
                $this->set('data',  $this->paramMails('insertion', $this->Deliberation->read(null, $delib_id),  $data['0']['User']));
                $this->Email->to = $data['0']['User']['email'];
                $this->Email->subject = "vous allez recevoir la delib : $delib_id";
                $result = $this->Email->send();
            }
	}

	function getListPresent($delib_id){
	    return $this->Listepresence->findAll("Listepresence.delib_id= $delib_id", null, "Acteur.position ASC");
	}

	function listerPresents($delib_id) {

		if (empty($this->data)) {
			$presents = $this->getListPresent($delib_id);
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
			$this->effacerListePresence($delib_id);
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
				   $this->reporteDelibs($delib_id);

			$this->redirect('/seances/voter/'.$delib_id);
		}

	}

	function reporteDelibs($delib_id) {
		$seance_id = $this->getCurrentSeance($delib_id);
		$position  = $this->getCurrentPosition($delib_id);
		$conditions = "Deliberation.seance_id=$seance_id AND Deliberation.position>=$position";
		$delibs = $this->Deliberation->findAll($conditions);
		foreach ($delibs as $delib)
			$this->changeSeance($delib['Deliberation']['id'], 0);
		$this->Session->setFlash('Le quorum n\'est plus atteint, toutes les projets suivants sont &agrave; attribuer...');
		$this->redirect('seances/listerFuturesSeances');
		exit;
	}

	function effacerListePresence($delib_id) {
		$condition = "delib_id = $delib_id";
		$presents = $this->Listepresence->findAll($condition);
		foreach($presents as $present)
  		    $this->Listepresence->del($present['Listepresence']['id']);
	}

	function isFirstDelib($delib_id) {
		$seance_id = $this->getCurrentSeance($delib_id);
		$position  = $this->getCurrentPosition($delib_id);
		return  ($position == 1);
	}

	function buildFirstList($delib_id) {
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

	function copyFromPreviousList($delib_id){
		$position = $this->getCurrentPosition($delib_id);
		$seance_id = $this->getCurrentSeance($delib_id);
		$previousDelibId= $this->getDelibIdByPosition($seance_id, $position);
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
                    return ($this->buildFirstList($delib_id));
	}

	function getDelibIdByPosition ($seance_id, $position){
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
		if ($this->isFirstDelib($delib_id) and (empty($presents)))
			$presents = $this->buildFirstList($delib_id);

		// Si la liste est vide, on recupere la liste des present lors de la derbiere deliberation.
		// Verifier que la liste precedente n'est pas vide...
		if (empty($presents))
			$presents = $this->copyFromPreviousList($delib_id);

		for($i=0; $i<count($presents); $i++){
			if ($presents[$i]['Listepresence']['mandataire'] !='0') {
				$mandataire = $this->Acteur->read('nom, prenom', $presents[$i]['Listepresence']['mandataire']);
			    $presents[$i]['Listepresence']['mandataire'] = $mandataire['Acteur']['prenom'].$mandataire['Acteur']['nom'];
			}
		}
		return ($presents);
        }

        function getModelId($delib_id) {
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

       function paramMails($type, $delib, $acteur) {
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

	function mesProjetsRefuses() {
		$this->_mesProjets(
			'Mes projets refus&eacute;s',
			array('view', 'generer'),
			'Deliberation.etat = -1');
	}

	function mesProjetsRedaction() {
		// Suppression des projets ajoutés mais vierges
		$this->checkEmptyDelib();

		$this->_mesProjets(
			'Mes projets en cours de r&eacute;daction',
			array('view', 'edit', 'delete', 'attribuercircuit', 'generer'),
			'Deliberation.etat = 0');
	}

	function mesProjetsValidation() {
		$this->_mesProjets(
			'Mes projets en cours d\'&eacute;laboration et de validation',
			array('view', 'generer'),
			'Deliberation.etat = 1');
	}

	function mesProjetsValides() {
		$this->_mesProjets(
			'Mes projets valid&eacute;s',
			array('view', 'generer'),
			'Deliberation.etat = 2');
	}

	function mesProjetsVotesOui() {
		$this->_mesProjets(
			'Mes projets vot&eacute;s et adopt&eacute;s',
			array('view', 'generer'),
			'Deliberation.etat = 3');
	}

	function mesProjetsVotesNon() {
		$this->_mesProjets(
			'Mes projets vot&eacute;s et non adopt&eacute;s',
			array('view', 'generer'),
			'Deliberation.etat = 4');
	}

	function mesProjetsTransmis() {
		$this->_mesProjets(
			'Mes projets transmis au contr&ocirc;le de l&eacute;galit&eacute;',
			array('view', 'generer'),
			'Deliberation.etat = 5');
	}

	function _mesProjets($titreVue, $listeActions, $conditions, $ordre = 'Deliberation.created DESC') {

		/* lecture en base */
		$userId = $this->Session->read('user.User.id');
		$conditions .= empty($conditions) ? '' : " AND ";
		$conditions .= "Deliberation.redacteur_id = $userId";
		$this->data = $this->Deliberation->findAll($conditions, null, $ordre, null, null, 0);

		/* initialisation pour chaque projet ou délibération */
        for($i = 0; $i < count($this->data); $i++) {
        	if ($this->data[$i]['Deliberation']['etat'] == 0 && $this->data[$i]['Deliberation']['anterieure_id']!=0)
				$this->data[$i]['iconeEtat'] = $this->Deliberation->iconeEtat(-1);
        	else
				$this->data[$i]['iconeEtat'] = $this->Deliberation->iconeEtat($this->data[$i]['Deliberation']['etat']);

			if (isset($this->data[$i]['Seance']['date'])) {
				$this->data[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data[$i]['Seance']['date']));
				$this->data[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($this->data[$i]['Seance']['type_id'], $this->data[$i]['Deliberation']['etat']);
			} else
				$this->data[$i]['Model']['id'] = 1;

			$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);
		}

		/* passage des variables à la vue */
		$this->set('titreVue', $titreVue);
		$this->set('listeActions', $listeActions);
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$this->set('UserCanAdd', $this->Acl->check($userId, "Deliberations:add"));

		/* on affiche la vue */
		$this->render('mesProjets');
	}

/*
 * Affiche la liste de tous les projets en cours de validation
 * Permet de valider en urgence un projet
 */
	function tousLesProjetsValidation() {
		/* Initialisations */
		$titreVue = 'Projets en cours d\'&eacute;laboration et de validation';
		$iconeEtat = array(
			'image' => '/icons/fini.png',
			'titre' => 'En cous d\'&eacute;laboration et de validation');

		/* lecture en base */
		$userId = $this->Session->read('user.User.id');
		$conditions = "Deliberation.etat = 1";
		$this->data = $this->Deliberation->findAll($conditions, null, 'Deliberation.created DESC', null, null, 0);

		/* initialisation pour chaque projet */
        for($i = 0; $i < count($this->data); $i++) {
			$this->data[$i]['iconeEtat'] = $iconeEtat;

			if (isset($this->data[$i]['Seance']['date'])) {
				$this->data[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data[$i]['Seance']['date']));
				$this->data[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($this->data[$i]['Seance']['type_id'], 1);
			} else
				$this->data[$i]['Model']['id'] = 1;

			$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);

			$this->data[$i]['Actions'] = array('view', 'validerEnUrgence', 'generer');
		}

		/* passage des variables à la vue */
		$this->set('titreVue', $titreVue);
		$this->set('USE_GEDOOO', USE_GEDOOO);

		/* on affiche la vue */
		$this->render('tousLesProjets');
	}

/*
 * Affiche la liste de tous les projets en cours de redaction, validation, validés sans séance
 * Permet de modifier un projet validé si l'utilisateur à les droits editerProjetValide
 */
	function tousLesProjetsSansSeance() {
		/* Initialisations */
		$titreVue = 'Projets en cours de r&eacute;daction, d\'&eacute;laboration, valid&eacute;s non associ&eacute;s &agrave; une s&eacute;ance';
		$userId = $this->Session->read('user.User.id');
		$editerProjetValide = $this->Acl->check($userId, "Deliberations:editerProjetValide");

		/* lecture en base */
		$conditions = "(Deliberation.seance_id is null OR Deliberation.seance_id=0) AND (Deliberation.etat=0 OR Deliberation.etat=1 OR Deliberation.etat=2)";
		$this->data = $this->Deliberation->findAll($conditions, null, 'Deliberation.created DESC', null, null, 0);

		/* initialisation pour chaque projet */
        for($i = 0; $i < count($this->data); $i++) {
			$this->data[$i]['iconeEtat'] = array(
				'image' => '/icons/fini.png',
				'titre' => $this->Deliberation->libelleEtat($this->data[$i]['Deliberation']['etat']));

			$this->data[$i]['Model']['id'] = 1;

			$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);

			$this->data[$i]['Actions'] = array('view', 'generer', 'attribuerSeance');
			if ($this->data[$i]['Deliberation']['etat'] == 2 && $editerProjetValide) {
				$this->data[$i]['Actions'][] = 'edit';
				$this->data[$i]['iconeEtat']['image'] = '/icons/encours.png';
			}
		}

		/* passage des variables à la vue */
		$this->set('titreVue', $titreVue);
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$this->set('date_seances', $this->Seance->generateList());

		/* on affiche la vue */
		$this->render('tousLesProjets');
	}

/*
 * Affiche la liste de tous les projets validés liés à une séance
 */
	function tousLesProjetsAFaireVoter() {
		/* Initialisations */
		$titreVue = 'Projets valid&eacute;s associ&eacute;s &agrave; une s&eacute;ance';
		$userId = $this->Session->read('user.User.id');
		$editerProjetValide = $this->Acl->check($userId, "Deliberations:editerProjetValide");

		/* lecture en base */
		$conditions = "Deliberation.seance_id!=0 AND Deliberation.etat=2";
		$this->data = $this->Deliberation->findAll($conditions, null, 'Deliberation.created DESC', null, null, 0);

		/* initialisation pour chaque projet */
        for($i = 0; $i < count($this->data); $i++) {
			$this->data[$i]['iconeEtat'] = array(
				'image' => '/icons/fini.png',
				'titre' => $this->Deliberation->libelleEtat($this->data[$i]['Deliberation']['etat']));

			$this->data[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data[$i]['Seance']['date']));
			$this->data[$i]['Model']['id'] = $this->getModelId($this->data[$i]['Deliberation']['id']);

			$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);

			$this->data[$i]['Actions'] = array('view', 'generer');
			if ($this->data[$i]['Deliberation']['etat'] == 2 && $editerProjetValide) {
				$this->data[$i]['Actions'][] = 'edit';
				$this->data[$i]['iconeEtat']['image'] = '/icons/encours.png';
			}
		}

		/* passage des variables à la vue */
		$this->set('titreVue', $titreVue);
		$this->set('USE_GEDOOO', USE_GEDOOO);

		/* on affiche la vue */
		$this->render('tousLesProjets');
	}

/*
 * Attribue une séance à un projet
 * Appelée depuis la vue deliberations/tous_les_projets
 */
	function attribuerSeance () {
		if (!empty($this->data)) {
			$this->data['Deliberation']['position'] = $this->getLastPosition($this->data['Deliberation']['seance_id']);
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
			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus());
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateAllList());
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('etats', $this->Deliberation->generateListEtat());
			$this->set('action', '/deliberations/mesProjetsRecherche/');
			$this->set('titreVue', 'Recherche multi-crit&egrave;res parmi mes projets');

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
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.id = ".$this->data['Deliberation']['id'];
			}

			if (!empty($this->data['Deliberation']['theme_id'])){
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.theme_id = ".$this->data['Deliberation']['theme_id'];
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

			if (empty($conditions)) {
				$this->Session->setFlash('Vous devez saisir au moins un crit&egrave;re.');
				$this->redirect('/deliberations/mesProjetsRecherche');
			} else
				$this->_mesProjets(
					'R&eacute;sultat de la recherche parmi mes projets',
					array('view', 'generer'),
					$conditions);
		}
	}

	function tousLesProjetsRecherche() {
		if (empty($this->data)) {
			$this->set('rapporteurs', $this->Deliberation->Acteur->generateListElus());
			$this->set('selectedRapporteur', $this->data['Deliberation']['rapporteur_id']);
			$this->set('date_seances',$this->Seance->generateAllList());
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('etats', $this->Deliberation->generateListEtat());
			$this->set('action', '/deliberations/tousLesProjetsRecherche/');
			$this->set('titreVue', 'Recherche multi-crit&egrave;res parmi tous les projets');

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
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.id = ".$this->data['Deliberation']['id'];
			}

			if (!empty($this->data['Deliberation']['theme_id'])){
				if ($conditions != "")
					$conditions .= " AND ";
				$conditions .= " Deliberation.theme_id = ".$this->data['Deliberation']['theme_id'];
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

			if (empty($conditions)) {
				$this->Session->setFlash('Vous devez saisir au moins un crit&egrave;re.');
				$this->redirect('/deliberations/tousLesProjetsRecherche');
			} else {
				/* Initialisations */
				$titreVue = 'R&eacute;sultat de la recherche parmi tous les projets';

				/* lecture en base */
				$this->data = $this->Deliberation->findAll($conditions, null, 'Deliberation.created DESC', null, null, 0);

				/* initialisation pour chaque projet */
		        for($i = 0; $i < count($this->data); $i++) {
					$this->data[$i]['iconeEtat'] = $this->Deliberation->iconeEtat($this->data[$i]['Deliberation']['etat']);

					if (isset($this->data[$i]['Seance']['date'])) {
						$this->data[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($this->data[$i]['Seance']['date']));
						$this->data[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($this->data[$i]['Seance']['type_id'], 1);
					} else
						$this->data[$i]['Model']['id'] = 1;

					$this->data[$i]['Service']['libelle'] = $this->Deliberation->Service->doList($this->data[$i]['Service']['id']);

					$this->data[$i]['Actions'] = array('view', 'generer');
				}

			/* passage des variables à la vue */
			$this->set('titreVue', $titreVue);
			$this->set('USE_GEDOOO', USE_GEDOOO);

			/* on affiche la vue */
			$this->render('tousLesProjets');
			}
		}
	}

}
?>
