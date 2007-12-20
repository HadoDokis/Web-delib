<?php
class DeliberationsController extends AppController {
/*
 * Deliberation.etat = -1 : refus�
 *	Deliberation.etat = 0 : en cours de r�daction
 *  Deliberation.etat = 1 : dans un circuit
 * 	Deliberation.etat = 2 : valid�
 *  Deliberation.etat = 3 : Vot� pour
 * 	Deliberation.etat = 4 : Vot� contre
 * 	Deliberation.etat = 5 : envoy�
 */

	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Localisation','Seance', 'Commentaire','Model', 'Theme', 'Collectivite', 'Vote','SeancesUser', 'Listepresence');
	var $components = array('Date','Utils','Email', 'Acl');

	function index() {
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		$this->Deliberation->recursive = 0;
		$this->set('deliberations', $this->Deliberation->findAll(null,null, 'Seance.date'));
	}

	function listerMesProjets()
	{
		//liste les projets dont je suis le redacteur et qui sont en cours de rédaction
		//il faut verifier la position du projet de delib dans la table traitement s'il existe car
		//si la position est � 0 cela notifie un refus
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		$conditions="etat = 0 AND redacteur_id = $user_id";
		$deliberations=$this->Deliberation->findAll($conditions);

		for ($i=0; $i<count($deliberations); $i++){
			if (isset($deliberations[$i]['Seance']['date']))
		        $deliberations[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberations[$i]['Seance']['date']));
			$id_service = $deliberations[$i]['Service']['id'];
			$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");

		}
		if ($this->Acl->check($user_id, "Deliberations:add"))
			$this->set('UserCanAdd', true);
		else
			$this->set('UserCanAdd', false);

		$this->set('deliberations', $deliberations);
	}

	function listerProjetsAttribues() {
		if (empty ($this->data)) {
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
			$conditions="seance_id != 0";
			$this->set('deliberations', $this->Deliberation->findAll($conditions));
		}
	}

	function listerProjetsNonAttribues(){
		if (empty ($this->data))
		{
			$user=$this->Session->read('user');
			$user_id=$user['User']['id'];
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
			$conditions="seance_id is null OR seance_id= 0 AND redacteur_id=$user_id";
			$deliberations= $this->Deliberation->findAll($conditions);
			$delib=array();
			foreach ($deliberations as $deliberation){

				$etat = $deliberation['Deliberation']['etat'];
				switch ($etat){
					case 0 :
					$deliberation['etatProjet'] = 'en cours de redaction'; break;
					case 1:
					$deliberation['etatProjet'] = 'en cours de validation';	break;
					case 2:
					$deliberation['etatProjet'] = 'valid�';	break;
					default:
					$deliberation['etatProjet'] = 'inconnu'; break;
				}
				array_push($delib, $deliberation);
			}
			$this->set('deliberations',$delib);

		}
		else
		{
			$deliberation['Deliberation']['seance_id']= $this->data['Deliberation']['seance_id'];

			if ($this->Deliberation->save($this->data)) {

				$position = $this->getLastPosition($this->data['Deliberation']['seance_id']);
				$this->data['Deliberation']['position']=$position;
				$this->Deliberation->save($this->data);

				$this->redirect('/deliberations/listerMesProjets');
			}
			else
			{
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
				$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
				$conditions="seance_id is null";
				$this->set('deliberations', $this->Deliberation->findAll($conditions));
			}
		}
	}

	function listerProjetsDansMesCircuits()
	{
		/**
		 * TODO BUG SI UNE PERSONNE QUI APPARAIT À PLUSIEURS SERVICES APPARAIT PLUSIEURS FOIS DANS UN
		 * MEME CIRCUIT
		 * PB : si une personne apparait plusieurs fois dans le circuit mais sous des services différents
		 * A FAIRE : verifier aussi le service, voir si un meme user peut appartenir à plusieurs services
		 * et apparaitre plusieurs fois dans le meme circuit
		 * CSQ : qui se connecte? un user ou un user service? remise en cause de la relation "un user
		 * peut appartenir à plusieurs services
		 */
		//liste les projets où j'apparais dans le circuit de validation
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		//recherche de tous les circuits où apparait l'utilisateur logué
		$data_circuit=$this->UsersCircuit->findAll("user_id=$user_id", null, "position ASC");
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

			//$conditions=$conditions." )";
			//debug($conditions);
			$deliberations = $this->Deliberation->findAll($conditions);

			for ($i=0; $i<count($deliberations); $i++){
				if(!empty($deliberations[$i]['Seance']['date']))
		    		$deliberations[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberations[$i]['Seance']['date']));
				$id_service = $deliberations[$i]['Service']['id'];
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
			}
			//debug($deliberations);
			//debug($data_circuit);

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
				// on n'affiche que les delib trait�es ou qui sont en attente
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
	}

	function listerProjetsATraiter()
	{
		/**
		 * TODO BUG SI UNE PERSONNE QUI APPARAIT À PLUSIEURS SERVICES APPARAIT PLUSIEURS FOIS DANS UN
		 * MEME CIRCUIT
		 * PB : si une personne apparait plusieurs fois dans le circuit mais sous des services différents
		 * A FAIRE : verifier aussi le service, voir si un meme user peut appartenir à plusieurs services
		 * et apparaitre plusieurs fois dans le meme circuit
		 * CSQ : qui se connecte? un user ou un user service? remise en cause de la relation "un user
		 * peut appartenir à plusieurs services
		 */
		//liste les projets où j'apparais dans le circuit de validation
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		//recherche de tous les circuits où apparait l'utilisateur logué
		$data_circuit=$this->UsersCircuit->findAll("user_id=$user_id", null, "position ASC");
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
				$deliberations[$i]['Service']['libelle'] = $this->requestAction("services/doList/$id_service");
			}
			foreach ($deliberations as $deliberation)
			{

				if (isset($deliberation['Deliberation']['date_limite'])){
				    $deliberation['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($deliberation['Deliberation']['date_limite']));
				}
				//on recupere la position courante de la deliberation
				$lastTraitement=array_pop($deliberation['Traitement']);

				//on recupere la position de l'user dans le circuit
				foreach ($data_circuit as $data)
				{
					if ($data['UsersCircuit']['circuit_id']==$lastTraitement['circuit_id'])
					{
						$position_user=$data['UsersCircuit']['position'];
					}
				}

				if ($lastTraitement['position'] == $position_user){
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

	function getPosition($circuit_id, $delib_id){
		$odjCourant=array();
		$conditions = "Traitement.circuit_id = $circuit_id AND Traitement.delib_id=$delib_id ";
        $objCourant = $this->Traitement->findAll($conditions, null, "position DESC");
		return $objCourant['0']['Traitement']['position'];

	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id de deliberation.');
			$this->redirect('/deliberations/listerProjetsATraiter');
		}
			//affichage anterieure
		$nb_recursion=0;
		$action='view';
		$listeAnterieure=array();
		$tab_delib=$this->Deliberation->find("Deliberation.id = $id");
		$tab_anterieure=$this->chercherVersionAnterieure($id, $tab_delib, $nb_recursion, $listeAnterieure, $action);
	//	debug($tab_anterieure);
		$this->set('tab_anterieure',$tab_anterieure);
		//$this->set('deliberation', $this->Deliberation->read(null, $id));

		$deliberation= $this->Deliberation->read(null, $id);
		if(!empty($deliberation['Seance']['date']))
			$deliberation['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Seance']['date']));
		$id_service = $deliberation['Service']['id'];
		$deliberation['Service']['libelle'] = $this->requestAction("services/doList/$id_service");

		$tab_circuit=$tab_delib['Deliberation']['circuit_id'];
		$delib=array();
		//on recupere la position courante de la deliberation
		$lastTraitement=array_pop($deliberation['Traitement']);
		$deliberation['positionDelib']=$lastTraitement['position'];
		//on recupere la position de l'user dans le circuit
		array_push($delib, $deliberation);
		$this->set('deliberation', $delib);
		$this->set('user_circuit', $this->UsersCircuit->findAll("UsersCircuit.circuit_id = $tab_circuit", null, 'UsersCircuit.position ASC'));
	}



	function getFileData($fileName, $fileSize) {
		return fread(fopen($fileName, "r"), $fileSize);
	}


	function saveLocation($id=null,$idLoc=0,$zone)
	{
		$this->layout = 'fckeditor';
		if($zone==1)
			$this->params['data']['Deliberation']['localisation1_id'] = $idLoc;
		elseif($zone==2)
			$this->params['data']['Deliberation']['localisation2_id'] = $idLoc;
		elseif($zone==3)
			$this->params['data']['Deliberation']['localisation3_id'] = $idLoc;

		$this->params['data']['Deliberation']['id'] = $id;

		if ($this->Deliberation->save($this->params['data'])){
			//$this->redirect($this->Session->read('user.User.lasturl'));

			$this->Session->setFlash('La localisation a &eacute;t&eacute; sauvegard&eacute;e');
			$this->redirect('/deliberations/changeLocation/'.$id);
		}
	}

	function getParent($id_loc)
	{
		if ($id_loc!=0)
		{$condition = "id = $id_loc";
		$parent = $this->Localisation->findAll($condition);
		return $parent[0]['Localisation']['parent_id'];
		}else{
			$parent = 0;
			return $parent;
		}
	}
	function changeLocation($id=null,$pzone1=0,$pzone2=0,$pzone3=0)
	{
		//$this->Session->write('user.User.lasturl','/deliberations/changeLocation/'.$id);
		$this->layout = 'fckeditor';
		if(empty($this->data))
		{
			$data= $this->Deliberation->read(null,$id);

			$this->data['Deliberation']['localisation1_id']= $data['Deliberation']['localisation1_id'];
			$this->data['Deliberation']['localisation2_id']= $data['Deliberation']['localisation2_id'];
			$this->data['Deliberation']['localisation3_id']= $data['Deliberation']['localisation3_id'];

			$this->set('id',$id);

			$conditions = "Localisation.parent_id= 0";
			$this->set('localisations', $this->Deliberation->Localisation->generateList($conditions));
			$this->set('selectedLocalisation1', $this->getParent($data['Deliberation']['localisation1_id']));
			$this->set('selectedLocalisation2', $this->getParent($data['Deliberation']['localisation2_id']));
			$this->set('selectedLocalisation3', $this->getParent($data['Deliberation']['localisation3_id']));

			if($pzone1!=0){
				$conditions = "Localisation.parent_id= $pzone1";
				$zone1 = $this->Localisation->generateList($conditions);
				$this->set('zone1',$zone1);
				$this->set('selectedLocalisation1',$pzone1);
			}else{
				$this->set('zone1',0);
				//$this->set('selectedLocalisation1',0);
				$this->set('selectedzone1',0);
			}

			if($pzone2!=0){
				$conditions = "Localisation.parent_id= $pzone2";
				$zone2 = $this->Localisation->generateList($conditions);
				//debug($zones);
				$this->set('zone2',$zone2);
				$this->set('selectedLocalisation2',$pzone2);
			}else{
				$this->set('zone2',0);
				//$this->set('selectedLocalisation2',0);
				$this->set('selectedzone2',0);
				$this->data['Deliberation']['localisation2_id']=0;
			}

			if($pzone3!=0){
				$conditions = "Localisation.parent_id= $pzone3";
				$zone3 = $this->Localisation->generateList($conditions);
				//debug($zones);
				$this->set('zone3',$zone3);
				$this->set('selectedLocalisation3',$pzone3);
			}else{
				$this->set('zone3',0);
				//$this->set('selectedLocalisation3',0);
				$this->set('selectedzone3',0);
				$this->data['Deliberation']['localisation3_id']=0;
			}
		}
	}

	function add($id=null) {

		if ($id==null){

			$this->Deliberation->save($this->data);
			$this->redirect('/deliberations/add/'.$this->Deliberation->getLastInsertId());
		}

		$user=$this->Session->read('user');
		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('deliberation',$this->data);
			if (empty($this->data['Service']['id']))
				$this->set('servEm', $this->requestAction('/services/doList/'.$user['User']['service']));
			else
				$this->set('servEm',$this->requestAction('/services/doList/'.$this->data['Service']['id']));
			$this->set('datelim',$this->data['Deliberation']['date_limite']);
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="G"'));
			$this->set('rapporteurs', $this->Deliberation->User->generateList('statut=1'));
			$selectedRapporteur = null;
			if($this->Deliberation->User->generateList('service_id='.$user['User']['service']))
				$selectedRapporteur = key($this->Deliberation->User->generateList('service_id='.$user['User']['service']));
			$this->set('selectedRapporteur',$selectedRapporteur);

			$seances = $this->Seance->findAll();
			foreach ($seances as $seance){
				$retard=$seance['Typeseance']['retard'];
				if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y"))))
					$tab[$seance['Seance']['id']]=$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
			}
			$this->set('date_seances',$tab);

			$this->render();

		} else {
			$this->data['Deliberation']['id']=$id;
			$this->data['Deliberation']['date_limite']= $this->Utils->FrDateToUkDate($this->params['form']['date_limite']);
			unset($this->params['form']['date_limite']);
			$this->data['Deliberation']['redacteur_id']=$user['User']['id'];
			$this->data['Deliberation']['service_id']=$user['User']['service'];
			//$this->data['Deliberation']['reporte']=0;
			if($this->data['Deliberation']['seance_id'] != ""){
				$position = $this->getLastPosition($this->data['Deliberation']['seance_id']);
				$this->data['Deliberation']['position']=$position;
			}
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
						$this->redirect('/deliberations/listerMesProjets');

					} else {
						$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
						$this->set('services', $this->Deliberation->Service->generateList());
						$this->set('themes', $this->Deliberation->Theme->generateList());
						$this->set('circuits', $this->Deliberation->Circuit->generateList());
						$this->set('users', $this->Deliberation->User->generateList());
						$condition= 'date >= "'.date('Y-m-d H:i:s').'"';

						$seances = $this->Seance->findAll();
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

	function textsynthese ($id = null) {
	 $this->layout = 'fckeditor';
	 $this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="S"'));

	if (empty($this->data)) {
        $this->data = $this->Deliberation->read(null, $id);
	}
    else {
	    if ($this->data['Deliberation']['texte_doc']['size']!=0){
		    $this->convertDoc2Html($this->data['Deliberation']['texte_doc'], $id, 'texte_synthese');
		    unset($this->data['Deliberation']['texte_doc']);
		}
		$this->data['Deliberation']['id']=$id;
		if(!empty($this->params['form'])) {
				$deliberation = array_shift($this->params['form']);
				$annexes = $this->params['form'];

				$uploaded = true;
				$size = count($this->params['form']);
				$counter = 1;

				while($counter <= ($size/2))
				{
					//echo $annexes['file_'.$counter]['tmp_name']."<br>";
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name']))
					{
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
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
							$this->data['Annex']['type'] = 'S';
							$this->data['Annex']['filename'] = $annexes['file_'.$counter]['name'];
							$this->data['Annex']['filetype'] = $annexes['file_'.$counter]['type'];
							$this->data['Annex']['size'] = $annexes['file_'.$counter]['size'];
							$this->data['Annex']['data'] = $this->getFileData($annexes['file_'.$counter]['tmp_name'], $annexes['file_'.$counter]['size']);
							if(!$this->Annex->save($this->data))
							{
								echo "pb de sauvegarde de l\'annexe ".$counter;
							}
						//$this->log("annexe ".$counter." enregistr�e.");
						//echo "<br>annexe ".$counter." enregistr�e.";
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
		} else{
			if ($this->data['Deliberation']['texte_doc']['size']!=0){
			    $this->convertDoc2Html($this->data['Deliberation']['texte_doc'], $id, 'deliberation');
				unset($this->data['Deliberation']['texte_doc']);
			}
			$this->data['Deliberation']['id']=$id;
			if(!empty($this->params['form']))
			{
				$deliberation = array_shift($this->params['form']);
				$annexes = $this->params['form'];

				$uploaded = true;
				$size = count($this->params['form']);
				$counter = 1;

				while($counter <= ($size/2))
				{
					//echo $annexes['file_'.$counter]['tmp_name']."<br>";
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
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
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

	function textprojet ($id = null) {
		$this->layout = 'fckeditor';
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="P"'));

		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
		} else{
			if ($this->data['Deliberation']['texte_doc']['size']!=0){
			    $this->convertDoc2Html($this->data['Deliberation']['texte_doc'], $id, 'texte_projet');
				unset($this->data['Deliberation']['texte_doc']);
			}

			$this->data['Deliberation']['id']=$id;
			if(!empty($this->params['form']))
			{
				$deliberation = array_shift($this->params['form']);
				$annexes = $this->params['form'];

				$uploaded = true;
				$size = count($this->params['form']);
				$counter = 1;

				while($counter <= ($size/2))
				{
					//echo $annexes['file_'.$counter]['tmp_name']."<br>";
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
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
							$this->data['Annex']['type'] = 'P';
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
						$this->redirect('/deliberations/textprojet/'.$id);
					} else {
						$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					}
				}
			}
		}
	}

	function edit($id=null) {
	    $user=$this->Session->read('user');
		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('servEm',$this->requestAction('/services/doList/'.$this->data['Service']['id']));
			$this->set('deliberation',$this->data);
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="G"'));
			$this->set('rapporteurs', $this->Deliberation->User->generateList('statut=1'));
			$selectedRapporteur = null;
			if($this->Deliberation->User->generateList('service_id='.$user['User']['service']))
				$selectedRapporteur = key($this->Deliberation->User->generateList('service_id='.$user['User']['service']));
			$this->set('selectedRapporteur',$selectedRapporteur);

			$seances = $this->Seance->findAll();
			foreach ($seances as $seance){
				$retard=$seance['Typeseance']['retard'];
				if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y"))))
					$tab[$seance['Seance']['id']]=$this->Date->frenchDateConvocation(strtotime($seance['Seance']['date']));
			}
			$this->set('date_seances',$tab);

			$this->render();

		} else {
			$this->data['Deliberation']['id']=$id;
			$this->data['Deliberation']['date_limite']= $this->Utils->FrDateToUkDate($this->params['form']['date_limite']);
			unset($this->params['form']['date_limite']);
			$this->data['Deliberation']['redacteur_id']=$user['User']['id'];
			$this->data['Deliberation']['service_id']=$user['User']['service'];

		 	if($this->data['Deliberation']['seance_id'] != ""){
				$position = $this->getLastPosition($this->data['Deliberation']['seance_id']);
				$this->data['Deliberation']['position']=$position;
			}
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
						$this->redirect('/deliberations/listerMesProjets');
					} else {
						$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
						$this->set('services', $this->Deliberation->Service->generateList());
						$this->set('themes', $this->Deliberation->Theme->generateList());
						$this->set('circuits', $this->Deliberation->Circuit->generateList());
						$this->set('users', $this->Deliberation->User->generateList());
						$condition= 'date >= "'.date('Y-m-d H:i:s').'"';

						$seances = $this->Seance->findAll();
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
				$this->redirect('/deliberations/listerMesProjets');
			}
			$deliberation = $this->Deliberation->read(null, $id);
			if(!empty($deliberation['Seance']['date']))
				$deliberation['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Seance']['date']));
			if(!empty($deliberation['Deliberation']['date_limite']))
				$deliberation['Deliberation']['date_limite'] = $this->Date->frenchDate(strtotime($deliberation['Deliberation']['date_limite']));
			$deliberation['Deliberation']['created'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Deliberation']['created']));
			$deliberation['Deliberation']['modified'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Deliberation']['modified']));
			$id_service = $deliberation['Service']['id'];
			$deliberation['Service']['libelle'] = $this->requestAction("services/doList/$id_service");

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

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation');
			$this->redirect('/deliberations/listerMesProjets');
		}
		if ($this->Deliberation->del($id)) {
			$this->Session->setFlash('La deliberation a &eacute;t&eacute; supprim&eacute;e.');
			$this->redirect('/deliberations/listerMesProjets');
		}
	}

   function convert($id=null) {
            $this->layout = 'pdf';
            $this->set('id',  $id);
            $this->set('text_projet', $this->getField($id, 'texte_projet'));
            $this->set('text_synthese',$this->getField($id, 'texte_synthese'));
            $this->set('deliberation',$this->getField($id, 'deliberation'));
            $this->set('seance_id',    htmlspecialchars($this->getField($id, 'seance_id')));
            $this->set('rapporteur_id',htmlspecialchars($this->getField($id, 'rapporteur_id')));
            $this->set('objet',        htmlspecialchars($this->getField($id, 'objet')));
  			$this->set('num_delib',    htmlspecialchars($this->getField($id, 'num_delib')));
  			$this->set('titre',       htmlspecialchars( $this->getField($id, 'titre')));
  			$this->set('theme',        htmlspecialchars($this->requestAction("themes/getLibelle/".$this->getField($id, 'theme_id'))));
        	$this->set('service',     htmlspecialchars( $this->requestAction("services/getLibelle/".$this->getField($id, 'service_id'))));
        	$this->set('nom_rapporteur',   htmlspecialchars( $this->requestAction("users/getNom/".$this->getField($id, 'rapporteur_id'))));
          	$this->set('prenom_rapporteur', htmlspecialchars($this->requestAction("users/getPrenom/".$this->getField($id, 'rapporteur_id'))));
            $seance_id = $this->requestAction("seances/getDate/".$this->getField($id, 'seance_id'));
          	if (!empty($seance_id))
            	$date_seance = $this->Date->frenchDateConvocation(strtotime($seance_id));
            else
            	$date_seance ='';
            $this->set('date_seance',  $date_seance   );
            $this->render();
    }


    function addIntoCircuit($id = null){
    	$this->data = $this->Deliberation->read(null,$id);
    	if ($this->data['Deliberation']['circuit_id']!= 0){
	    	$this->data['Deliberation']['id'] = $id;
	    	$this->data['Deliberation']['date_envoi']=date('Y-m-d H:i:s', time());
			$this->data['Deliberation']['etat']='1';
	    	if ($this->Deliberation->save($this->data)) {
				//on doit tester si la delib a une version anterieure, si c le cas il faut mettre à jour l'action dans la table traitement
				$delib=$this->Deliberation->find("Deliberation.id = $id");
				if ($delib['Deliberation']['anterieure_id']!=0) {
					//il existe une version anterieure de la delib
					//on met à jour le traitement anterieure
					$anterieure=$delib['Deliberation']['anterieure_id'];
					$condition="delib_id = $anterieure AND position = '0'";
					$traite=$this->Traitement->find($condition);
					//debug($traite);
					$traite['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
					$this->Traitement->save($traite);
				}
				//enregistrement dans la table traitements
				// TODO Voir comment améliorer ce point (associations cakephp).
				$circuit_id = $delib['Deliberation']['circuit_id'];
				$this->data['Traitement']['id']='';
				$this->data['Traitement']['delib_id']=$id;
				$this->data['Traitement']['circuit_id']=$circuit_id;
				$this->data['Traitement']['position']='1';
				$this->Traitement->save($this->data['Traitement']);

				//Envoi un mail à tous les membres du circuit
				$condition = "circuit_id = $circuit_id";
				$listeUsers = $this->UsersCircuit->findAll($condition);
				foreach($listeUsers as $user)
					$this->notifierInsertionCircuit($id, $user['User']['id']);

				$this->redirect('/deliberations/listerMesProjets');
			} else
				$this->Session->setFlash('Probleme de sauvegarde.');
    	}else{
    		$this->Session->setFlash('Vous devez assigner un circuit a la deliberation	.');
    		$this->redirect('/deliberations/recapitulatif/'.$id);
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

			//affichage du circuit existant
			if($circuit_id == null)
				$circuit_id=$this->data['Deliberation']['circuit_id'];
			if (isset($circuit_id)){
			    $this->set('circuit_id', $circuit_id);
			    $condition = "UsersCircuit.circuit_id = $circuit_id";
			    $desc = 'position ASC';

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
		//debug($this->data);
			$this->data['Deliberation']['id']=$id;

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
				$this->set('commentaire', $this->Commentaire->findAll("delib_id =  $id"));


				$deliberation= $this->Deliberation->read(null, $id);
				$deliberation['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberation['Seance']['date']));
				$id_service = $deliberation['Service']['id'];
				$deliberation['Service']['libelle'] = $this->requestAction("services/doList/$id_service");

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
					//verification du projet, s'il n'est pas pret ->report� a la seance suivante
					$delib = $this->Deliberation->findAll("Deliberation.id = $id");

					$type_id =$delib[0]['Seance']['type_id'];

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
						$position = $this->getLastPosition($this->data['Deliberation']['seance_id']);
						$this->data['Deliberation']['position']=$position;
						$this->Deliberation->save($this->data);
					}
					}
					//on a validé le projet, il passe à la personne suivante
					$tab=$this->Traitement->findAll("delib_id = $id", null, "id ASC");

					$lastpos=count($tab)-1;
					$circuit_id=$tab[$lastpos]['Traitement']['circuit_id'];

					//MAJ de la date de traitement de la dernière position courante $lastpos
					$tab[$lastpos]['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
					$this->Traitement->save($tab[$lastpos]['Traitement']);

					//il faut verifier que le projet n'est pas arrivé en fin de circuit
					//position courante du projet : lastposprojet : $tab[$lastpos]['Traitement']['position'];
					//derniere position théorique : lastposcircuit
					$lastposprojet=$tab[$lastpos]['Traitement']['position'];
					//$lastposcircuit=$this->Circuit->getLastPosition($circuit_id);
					$lastposcircuit=count($this->UsersCircuit->findAll("circuit_id = $circuit_id"));

					if ($lastposcircuit==$lastposprojet) //on est sur la dernière personne, on va faire sortir le projet du workflow et le passer au service des assemblées
					{
						//passage au service des assemblée : etat dans la table deliberations passe à 2
						$tab=$this->Deliberation->findAll("Deliberation.id = $id");
						$this->data['Deliberation']['etat']=2;
						$this->data['Deliberation']['id']=$id;
						$this->Deliberation->save($this->data['Deliberation']);
						$this->redirect('/deliberations/listerProjetsATraiter');
					}
					else
					{
						$this->notifierDossierAtraiter($circuit_id, $tab[$lastpos]['Traitement']['position']+1, $id);
						//sinon on fait passer à la personne suivante
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

					//MAJ de la date de traitement de la dernière position courante $lastpos
					$tab[$lastpos]['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
					$this->Traitement->save($tab[$lastpos]['Traitement']);

					$this->data['Traitement']['id']='';
					//maj de la table traitements
					$this->data['Traitement']['position']=0;
					$circuit_id=$tab[$lastpos]['Traitement']['circuit_id'];
					$this->data['Traitement']['delib_id']=$id;
					$this->data['Traitement']['circuit_id']=$circuit_id;
					$this->Traitement->save($this->data['Traitement']);

					//TODO notifier par mail toutes les personnes qui ont déjà visé le projet
					$condition = "circuit_id = $circuit_id";
					$listeUsers = $this->UsersCircuit->findAll($condition);
					foreach($listeUsers as $user)
						$this->notifierDossierRefuse($id, $user['User']['id']);

					//maj de l'etat de la delib dans la table deliberations
					$tab=$this->Deliberation->findAll("Deliberation.id = $id");
					$this->data['Deliberation']['etat']=-1; //etat -1 : refusé
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

    function transmit($id=null){
        $this->set('dateClassification', $this->getDateClassification());
        $this->set('tabNature',          $this->getNatureListe());
        $this->set('tabMatiere',         $this->getMatiereListe());
        // On affiche que les d�libs vot� pour.
        $deliberations =   $this->Deliberation->findAll("Deliberation.etat=3 OR Deliberation.etat=5 ");

        for($i = 0; $i < count($deliberations); $i++) {
        	$deliberations[$i]['Deliberation'][$deliberations[$i]['Deliberation']['id'].'_num_pref'] = $deliberations[$i]['Deliberation']['num_pref'];
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
            $url = 'https://'.HOST.'/modules/actes/actes_transac_create.php';
            $pos =  strrpos ( getcwd(), 'webroot');
	        $path = substr(getcwd(), 0, $pos);
			foreach ($this->data['Deliberation'] as $id => $bool ){
				if ($bool == 1){
					$delib_id = substr($id, 3, strlen($id));
					$classification = $this->data['Deliberation'][$delib_id."_num_pref"];
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

					$err = $this->requestAction("/postseances/generateDeliberation/$delib_id");
					$file = $path."webroot/files/delibs/DELIB_$delib_id.pdf";
					$delib = $this->Deliberation->findAll("Deliberation.id = $delib_id");

        	        if (!file_exists($file)){
  					   	debug($file);
  					   	die("Fichier � g�n�rer");
        	        }

        	        // Checker le code classification
        	        $data = array(
      	                 'api'           => '1',
     	                 'nature_code'   => '1',
     	                 'classif1'      => $class1 ,
     	                 'classif2'      => $class2,
     	                 'classif3'      => $class3,
     	                 'classif4'      => $class4,
     	                 'classif5'      => $class5,
      	                 'number'        => 'WEBDELIB_'.$delib_id,
     	                 'decision_date' => date("Y-m-d", strtotime($delib[0]['Seance']['date'])),
      	                 'subject'       => $delib[0]['Deliberation']['objet'],
      	                 'acte_pdf_file' => "@$file",
     	                 'acte_pdf_file_sign' => "",
     	                 'acte_attachments[]' => "",
      	                'acte_attachments_sign[]' => ""
   	                 );
   	          	     $ch = curl_init();
 	            	 curl_setopt($ch, CURLOPT_URL, $url);
  	            	 curl_setopt($ch, CURLOPT_POST, TRUE);
  	          	     curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
   	          	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  	           	     curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
  	            	 curl_setopt($ch, CURLOPT_SSLCERT, PEM);
   	           	     curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
   	           	     curl_setopt($ch, CURLOPT_SSLKEY, KEY);
  	            	 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  	               	 curl_exec($ch);
        	         curl_close($ch);
				}
			}
			$this->changeEtat($delib_id, '5');
			$this->redirect('/deliberations/transmit');
		}

		function changeEtat($delib_id, $etat){
			$this->data = $this->Deliberation->read(null, $delib_id);
			$this->data['Deliberation']['id']=$delib_id;
			$this->data['Deliberation']['etat'] = $etat;
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
           return($doc->getElementsByTagName('DateClassification')->item(0)->nodeValue);
        }

 		function getClassification($id=null){
                $url = 'https://'.HOST.'/modules/actes/actes_classification_fetch.php';
        		$data = array(
        		'api'           => '1',
        		);
        $url .= '?'.http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
        curl_setopt($ch, CURLOPT_SSLCERT, PEM);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
        curl_setopt($ch, CURLOPT_SSLKEY, KEY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $reponse = curl_exec($ch);

        if (curl_errno($ch))
          print curl_error($ch);
        curl_close($ch);

        // Assurons nous que le fichier est accessible en �criture
       if (is_writable(FILE_CLASS)) {
           if (!$handle = fopen(FILE_CLASS, 'w')) {
               echo "Impossible d'ouvrir le fichier (".FILE_CLASS.")";
               exit;
        	}
        	// Ecrivons quelque chose dans notre fichier.
        	if (fwrite($handle, utf8_encode($reponse)) === FALSE) {
            	echo "Impossible d'�crire dans le fichier ($filename)";
            	exit;
       	 	}
        	else
            	$this->redirect('/deliberations/transmit');
        	fclose($handle);
        }
        else
            echo "Le fichier FILENAME n'est pas accessible en �criture.";
 		}

        function positionner($id=null, $sens)
        {
        	$positionCourante = $this->getCurrentPosition($id);
			$seance_id  = $this->getCurrentSeance($id);
	   		$lastPosition = $this->getLastPosition($seance_id);
        	if ($sens != 0)
            	$conditions = "Deliberation.seance_id = $seance_id  AND position = $positionCourante-1";
       		else            // on r�cup�re l'objet pr�c�dent"
   		    	$conditions = "Deliberation.seance_id = $seance_id  AND position = $positionCourante+1";

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
			// On r�cup�re les informations de l'objet � d�placer
			$this->data = $this->Deliberation->read(null, $id_obj);
			$this->data['Deliberation']['position']= $positionCourante;

			//enregistrement de l'objet � d�placer avec la position courante
			if ($this->Deliberation->save($this->data)) {

			$this->redirect("/seances/afficherProjets/$seance_id/");
			}
			else {
		 	   $this->Session->setFlash('Erreur durant l\'enregistrement');
			}

        	echo("$positionCourante $seance_id $lastPosition" );
        }

        function sortby($seance_id, $sortby) {
		    $condition= "seance_id=$seance_id AND etat = 2";
  		    $deliberations = $this->Deliberation->findAll($condition,null, "$sortby ASC");
		    for($i=0; $i<count($deliberations); $i++){
			    $deliberations[$i]['Deliberation']['position']=$i+1;
		    	$this->Deliberation->save($deliberations[$i]['Deliberation']);
		    }
		    $this->redirect("seances/afficherProjets/$seance_id");
	    }

        function getCurrentPosition($id){
    		$conditions = "Deliberation.id = $id";
    		$field = 'position';
    		$obj = $this->Deliberation->findAll($conditions);

    		return  $obj['0']['Deliberation']['position'];
  		}

   		function getCurrentSeance($id) {
			$condition = "Deliberation.id = $id";
        	$objCourant = $this->Deliberation->findAll($condition);
			return $objCourant['0']['Deliberation']['seance_id'];
    	}

   		function getLastPosition( $seance_id) {
			return count($this->Deliberation->findAll("seance_id =$seance_id AND etat=2"));
    	}

	function getNextId() {
		$tmp = $this->Deliberation->findAll('Deliberation.id in (select max(id) from deliberations)');
		return $tmp['0']['Deliberation']['id'] +1 ;
	}

	function listerProjetsServicesAssemblees()
	{
		//liste les projets appartenants au service des assembl�es
		$conditions="etat = 2 ";
		$deliberations = $this->Deliberation->findAll($conditions);

		for ($i=0; $i<count($deliberations); $i++)
			$deliberations[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($deliberations[$i]['Seance']['date']));

		$this->set('deliberations',$deliberations );
	}

	function convertDoc2Html($file, $delib_id, $texte) {
		if ($file['type']!='application/msword')
	        die("Ce n'est pas un fichier doc");

		$wvware = "/usr/bin/wvWare";
        $wvware_options = "-d";
    	$pos =  strrpos ( getcwd(), 'webroot');
		$path = substr(getcwd(), 0, $pos);
    	$basedir = $path.'webroot/files/delibs'."/$delib_id/";
    	if (! is_dir($basedir))
    		mkdir($basedir);
		$name=substr($file['name'],0,strlen($file['type']['name'])-5);
		$wordfilename = $basedir . "/" . escapeshellcmd($name).".doc";
		$htmldir = $basedir;
   		$htmlfilename = $htmldir . escapeshellcmd($name) . ".html";

    	if( !move_uploaded_file($file['tmp_name'], 	$wordfilename) )
       	    exit("Impossible de copier le fichier dans $content_dir");

	   if (! is_dir($htmldir))
            die("Directory $htmldir does not exist.  It must be " .
            "created and readable and writable by your web server.");
        if ((! is_writeable($htmldir)) || (! is_readable($htmldir)))
            die("Directory $htmldir must be readable and writable by your " .
            "web server.");
        if (file_exists($htmlfilename) && (! is_writeable($htmlfilename)))
            die("The html file ($htmlfilename) exists already but is not " .
            "writable by the web server.");
        if (! file_exists($wvware))
            die("The wvWare executable file $wvware cannot be found.  Please " .
            "ensure that the \$wvware variable in the script is pointed " .
            "to your wvware executable.");
        if (! is_executable($wvware))
            die("The wvWare executable file $wvware is not " .
            "executable by the web server process.  Please change the file " .
            "permissions to make it executable.");

		if (file_exists($htmlfilename)) {
            /* Do we need to update the html file? */
      	    if (filectime($wordfilename) > filectime($htmlfilename))
                $this->updateword($wordfilename, $htmlfilename);
       	    else readfile ($htmlfilename);
   		}
        else
            $this->updateword($wordfilename, $htmlfilename);

        $handle = fopen ($htmlfilename, "r");
        $contents = fread($handle, filesize ($htmlfilename));
        fclose ($handle);
		$data= $this->Deliberation->read(null, $delib_id);

		$data['Deliberation']["$texte"]=utf8_decode($contents);

 	    $this->Deliberation->save($data['Deliberation']);
 	    if ($texte== 'texte_projet')
		    $this->redirect('/deliberations/textprojet/'.$delib_id);
		elseif  ($texte== 'texte_synthese')
			$this->redirect('/deliberations/textsynthese/'.$delib_id);
		elseif  ($texte== 'deliberation')
			$this->redirect('/deliberations/deliberation/'.$delib_id);
		exit;
	}

    function updateword($wordfilename, $htmlfilename) {
    	$wvware = "/usr/bin/wvWare";
        $wvware_options = "-d";

        $htmldir = dirname ($htmlfilename);
        /* ensure that we get any images into the html directory */
        exec("$wvware $wvware_options $htmldir $wordfilename > $htmlfilename");
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
			$to_nom = $data['0']['User']['nom'];
			$to_prenom = $data['0']['User']['prenom'];

			$this->Email->template = 'email/traiter';
			$addr = "http://".$_SERVER['SERVER_NAME'].$this->base."/deliberations/traiter/$delib_id";
			$text = "Vous avez un dossier à traiter, Cliquer <a href='$addr'> ici</a>";
            $this->set('data', $text);
            $this->Email->to = $to_mail;
            $this->Email->subject = "DELIB $delib_id à traiter";
       	   //  $this->Email->attach($fully_qualified_filename, optionally $new_name_when_attached);
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
			$to_mail = $data['0']['User']['email'];
			$to_nom = $data['0']['User']['nom'];
			$to_prenom = $data['0']['User']['prenom'];
			$this->Email->template = 'email/refuse';

			if(!empty($data_comm) && $data['0']['User']['id']==$redacteur_id){
				$commentaire = $data_comm['0']['Commentaire']['texte'];
				$comm = "Votre dossier a été refusé pour les motifs suivants :<br/><br/>$commentaire";
				$this->set('data',$comm);
			}elseif ($data['0']['User']['id']==$redacteur_id) {
				$this->set('data',"Votre dossier a été refusé");
			}else{
            	$this->set('data', "Le dossier $delib_id a été refusé... Il est reparti au redacteur pour etre modifié");
			}
			$this->Email->to = $to_mail;
            $this->Email->subject = "DELIB $delib_id Refusée !";
       	   // $this->Email->attach($fully_qualified_filename, optionally $new_name_when_attached);
            $result = $this->Email->send();
		}
	}

	function notifierInsertionCircuit ($delib_id, $user_id) {
		$condition = "User.id = $user_id";
		$data = $this->User->findAll($condition);

		// Si l'utilisateur accepte les mails
		if ($data['0']['User']['accept_notif']){
			$to_mail = $data['0']['User']['email'];
			$to_nom = $data['0']['User']['nom'];
			$to_prenom = $data['0']['User']['prenom'];
			$this->Email->template = 'email/circuit';
            $this->set('data', 'Vous allez recevoir un dossier');
            $this->Email->to = $to_mail;
            $this->Email->subject = "vous allez recevoir la délib : $delib_id";
       	   //  $this->Email->attach($fully_qualified_filename, optionally $new_name_when_attached);
            $result = $this->Email->send();
		}
	}

	function getListPresent($delib_id){
			return $this->Listepresence->findAll("Listepresence.delib_id= $delib_id");
	}

	function listerPresents($delib_id) {
		if (empty($this->data)) {
			$presents = $this->getListPresent($delib_id);
			foreach($presents as $present){
				    	$this->data[$present['Listepresence']['user_id']]['present'] = $present['Listepresence']['present'];
					    $this->data[$present['Listepresence']['user_id']]['mandataire'] = $present['Listepresence']['mandataire'];
			}
			$this->set('presents',$presents);
			$this->set('mandataires', $this->User->generateList('statut = 1'));
			$this->set('delib_id', $delib_id);
		}
		else {
			$this->effacerListePresence($delib_id);
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

 			    $this->data['Listepresence']['delib_id']=$delib_id;
			 	$this->Listepresence->save($this->data['Listepresence']);
			}
			$this->redirect('/seances/voter/'.$delib_id);
		}

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
		$elus = $this->User->findAll( "User.statut= 1");
		foreach ($elus as $elu){
			$this->Listepresence->create();
			$this->params['data']['Listepresence']['user_id']=$elu['User']['id'];
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
		$previousDelibId= $this->getDelibIdByPosition($seance_id, $position -1);
		$condition = "delib_id = $previousDelibId";
		$previousPresents = $this->Listepresence->findAll($condition);

		foreach ($previousPresents as $present){
			$this->Listepresence->create();
			$this->params['data']['Listepresence']['user_id']=$present['Listepresence']['user_id'];
			$this->params['data']['Listepresence']['mandataire'] = $present['Listepresence']['mandataire'];
			$this->params['data']['Listepresence']['present']= $present['Listepresence']['present'];
			$this->params['data']['Listepresence']['delib_id']= $delib_id;
			$this->Listepresence->save($this->params['data']);
		}
		return $this->Listepresence->findAll("delib_id =$delib_id");
	}

	function getDelibIdByPosition ($seance_id, $position){
		$condition = "seance_id = $seance_id AND position = $position";
		$delib = $this->Deliberation->findAll($condition);
		return $delib['0']['Deliberation']['id'];
	}

	function afficherListePresents($delib_id=null)	{
		$condition = "Listepresence.delib_id= $delib_id";
		$presents = $this->Listepresence->findAll($condition);
		if ($this->isFirstDelib($delib_id) and (empty($presents)))
			$presents = $this->buildFirstList($delib_id);

		// Si la liste est vide, on r�cup�re la liste des pr�sent lors de la derbi�re d�lib�ration.
		// V�rifier que la liste pr�c�dente n'est pas vide...
		if (empty($presents))
			$presents = $this->copyFromPreviousList($delib_id);

		for($i=0; $i<count($presents); $i++){
			if ($presents[$i]['Listepresence']['mandataire'] !='0')
			    $presents[$i]['Listepresence']['mandataire'] = $this->User->requestAction('/users/getPrenom/'.$presents[$i]['Listepresence']['mandataire']).' '.$this->User->requestAction('/users/getNom/'.$presents[$i]['Listepresence']['mandataire']);
		}
		return ($presents);
	}
}
?>