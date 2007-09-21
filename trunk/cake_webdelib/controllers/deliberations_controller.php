<?php
class DeliberationsController extends AppController {

	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf' );
	var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex','Commentaire');
	
	function index() {
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		$this->Deliberation->recursive = 0;
		$this->set('deliberations', $this->Deliberation->findAll('redacteur_id='.$user_id,null, 'Seance.date'));
	}

	function listerMesProjets()
	{
		//liste les projets dont je suis le redacteur et qui sont en cours de rédaction
		//il faut verifier la position du projet de delib dans la table traitement s'il existe car
		//si la position est à 0 cela notifie un refus
		$user=$this->Session->read('user');
		$user_id=$user['User']['id'];
		$conditions="etat = 0 AND redacteur_id = $user_id";
		//debug($user);
		$this->set('deliberations', $this->Deliberation->findAll($conditions));
	}
	
	function listerProjetsAttribues()
	{
		if (empty ($this->data))
		{
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));	
			$conditions="seance_id != 0";
			$this->set('deliberations', $this->Deliberation->findAll($conditions));
		}
	}
		
		
	function listerProjetsNonAttribues()
	{
		if (empty ($this->data))
		{
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));	
			$conditions="seance_id is null OR seance_id= 0";
			$this->set('deliberations', $this->Deliberation->findAll($conditions));
		}
		else
		{

			$deliberation['Deliberation']['seance_id']= $this->data['Deliberation']['seance_id'];

			if ($this->Deliberation->save($this->data)) 
			{
				
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
		//$position_user=0;
		$cpt=0;
		//debug($data_circuit);
		if ($data_circuit!=null)
		{
			foreach ($data_circuit as $data)
			{
				
				
				if ($cpt>0)
					$conditions=$conditions." OR ";
				else
					$conditions=$conditions." AND (";
			
				$conditions=$conditions." seance_id!=0 AND circuit_id = ".$data['UsersCircuit']['circuit_id'];
				$cpt++;
			}
			if ($cpt>=0)
				$conditions=$conditions." )";
			//$conditions=$conditions." )";
			//debug($conditions);
			$deliberations = $this->Deliberation->findAll($conditions);
			//debug($deliberations);
			//debug($data_circuit);
			foreach ($deliberations as $deliberation)
			{
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
			
				if ($lastTraitement['position']==$position_user){
					$deliberation['action']="traiter";
					$deliberation['act']="traiter";
				}else{
					$deliberation['action']="view";
					$deliberation['act']="voir";
				}
				//debug($deliberation);
				
				array_push($delib, $deliberation);
				
			}
		}
		$this->set('deliberations', $delib);
		//debug($delib);
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
		//$position_user=0;
		$cpt=0;
		//debug($data_circuit);
		if ($data_circuit!=null)
		{
			foreach ($data_circuit as $data)
			{
				if ($cpt>0)
					$conditions=$conditions." OR ";
				else
					$conditions=$conditions." AND (";
			
				$conditions=$conditions." seance_id!=0 AND circuit_id = ".$data['UsersCircuit']['circuit_id'];
				$cpt++;
			}
			if ($cpt>=0)
				$conditions=$conditions." )";

			$deliberations = $this->Deliberation->findAll($conditions);
			//debug($deliberations);

			foreach ($deliberations as $deliberation)
			{
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
			
				if ($lastTraitement['position']==$position_user){
					$deliberation['action']="traiter";
					$deliberation['act']="traiter";
					array_push($delib, $deliberation);

				}
			}
		}
		$this->set('deliberations', $delib);
		$this->render('listerProjetsATraiter');
		//debug($delib);
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
		$this->set('deliberation', $this->Deliberation->read(null, $id));
	}
	
	function getFileData($fileName, $fileSize)
	{
		return fread(fopen($fileName, "r"), $fileSize);
	}	
		
	function add() 
	{
	$user=$this->Session->read('user');
		if (empty($this->data)) {
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('rapporteurs', $this->Deliberation->User->generateList('statut=1'));
			$this->set('selectedRapporteur',key($this->Deliberation->User->generateList('service_id='.$user['User']['service'])));
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
			$this->render();
		} else {
			//$this->data['Deliberation']['seance_id']= $this->Utils->FrDateToUkDate($this->params['form']['seance_id']);
			
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
					//echo $annexes['file_'.$counter]['tmp_name']."<br>";
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name']))
					{
						$uploaded = false;
					}
					$counter++;
				}
				
				if($uploaded)
				{

					if ($this->Deliberation->save($this->data)) 
					{
						$delib_id = $this->Deliberation->getLastInsertId();
						$counter = 1;
						
						$position = $this->getLastPosition($this->data['Deliberation']['seance_id']);
						$this->data['Deliberation']['position']=$position;
						$this->Deliberation->save($this->data);
						
						
						while($counter <= ($size/2))
						{	
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $delib_id;
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
							$this->data['Annex']['type'] = 'G';
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
			
						$this->redirect('/deliberations/textprojet/'.$this->Deliberation->getLastInsertId());
					} else {
					$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					$this->set('services', $this->Deliberation->Service->generateList());
					$this->set('themes', $this->Deliberation->Theme->generateList());
					$this->set('circuits', $this->Deliberation->Circuit->generateList());
					$this->set('users', $this->Deliberation->User->generateList());
					$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
					$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
					}
				}	
			}
		}
	}
	
	
	
	
	
	function textsynthese ($id = null)
	{
	$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="S"'));
	
	if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
		} else 
		{//debug($this->data);
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
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name']))
					{
						$uploaded = false;
					}
					$counter++;
				}
				
				if($uploaded)
				{
			
					if ($this->Deliberation->save($this->data)) {
					
					$counter = 1;
					
						while($counter <= ($size/2))
						{	
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $id;
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
						$this->redirect('/deliberations/attribuercircuit/'.$id);
						
					} else {
					$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					}
				}
			}
		}	
	}
	
	
	function textprojet ($id = null)
	{
	$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="P"'));
	
	if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
		} else 
		{//debug($this->data);
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
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name']))
					{
						$uploaded = false;
					}
					$counter++;
				}
				
				if($uploaded)
				{
			
					if ($this->Deliberation->save($this->data)) {
					
					$counter = 1;
					
						while($counter <= ($size/2))
						{	
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $id;
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
	
	
	

	function edit($id = null) {
	
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la deliberation');
				$this->redirect('/deliberations/listerMesProjets');
			}
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('deliberations',$this->Deliberation->read(null, $id));
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="G"'));
			//debug($this->Deliberation->Annex->findAll('deliberation_id='.$id.' AND type="G"',array('titre','size','filename')));								
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
		} else {
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
					//echo $annexes['file_'.$counter]['tmp_name']."<br>";
					if(!is_uploaded_file($annexes['file_'.$counter]['tmp_name']))
					{
						$uploaded = false;
					}
					$counter++;
				}
				
				if($uploaded)
				{			
					if ($this->Deliberation->save($this->data)) 
					{
						$counter = 1;
					
						while($counter <= ($size/2))
						{	
							$this->data['Annex']['id'] = null;
							$this->data['Annex']['deliberation_id'] = $id;
							$this->data['Annex']['titre'] = $annexes['titre_'.$counter];
							$this->data['Annex']['type'] = 'G';
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
						$this->Session->setFlash('La deliberation a &eacute;t&eacute; sauvegard&eacute;e');
						$this->redirect('/deliberations/textprojet/'.$id);
						//$this->redirect('/deliberations/listerMesProjets');
					} else {
					$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
					$this->set('services', $this->Deliberation->Service->generateList());
					$this->set('themes', $this->Deliberation->Theme->generateList());
					$this->set('circuits', $this->Deliberation->Circuit->generateList());
					$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
					$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
					}
				}
			}
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
	   	return $dataValeur['0'] ['Deliberation'][$field];
		
	}
	

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation');
			$this->redirect('/deliberations/listerMesProjets');
		}
		if ($this->Deliberation->del($id)) {
			$this->Session->setFlash('La deliberation a &eacute;t&eacute; suprim&eacute;e.');
			$this->redirect('/deliberations/listerMesProjets');
					}
	}
 
   function convert($id=null)
        {
            $this->layout = 'pdf'; //this will use the pdf.thtml layout
            $this->set('text_projet',  $this->getField($id, 'texte_projet'));
            $this->set('text_synthese',$this->getField($id, 'texte_synthese'));
            $this->set('seance_id', $this->getField($id, 'seance_id'));
            $this->set('rapporteur_id',   $this->getField($id, 'rapporteur_id'));
            $this->set('objet',        $this->getField($id, 'objet'));
  
            $this->render();
        } 
        
	function attribuercircuit ($id = null, $circuit_id=null)
	{
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
  			}
			else 
				$this->set('circuit_id', '0');
			
			$this->set('circuits', $circuits);
				} else {
				$this->data['Deliberation']['id']=$id;
				$this->data['Deliberation']['date_envoi']=date('Y-m-d H:i:s', time());
				$this->data['Deliberation']['etat']='1';
				if ($this->Deliberation->save($this->data)) {
					
					//on doit tester si la delib a une version anterieure, si c le cas il faut mettre à jour l'action dans la table traitement
					$delib=$this->Deliberation->find("Deliberation.id = $id");					
					//debug($delib);
					if ($delib['Deliberation']['anterieure_id']!=0)
					{
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
					$this->data['Traitement']['id']='';
					$this->data['Traitement']['delib_id']=$id;
					$this->data['Traitement']['circuit_id']=$circuit_id;
					$this->data['Traitement']['position']='1';
					$this->Traitement->save($this->data['Traitement']);
										
					
					$this->redirect('/deliberations/listerMesProjets');
				} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
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
				$this->set('deliberation', $this->Deliberation->read(null, $id));
	
			}
			else
			{
				if ($valid=='1') 
				{
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
					
					//on a refusé le projet, il repart au redacteur
					//TODO notifier par mail toutes les personnes qui ont déjà visé le projet
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
					$delib['Deliberation']['circuit_id']=0;
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
		
		if ($anterieure_id!=0)
		{
	
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
            $this->set('dateClassification',$this->getDateClassification());
            $this->set('tabNature', $this->getNatureListe());
            $this->set('tabMatiere', $this->getMatiereListe());
         
           
        }

        function getNatureListe(){
        	 $tabNatures = array();
        	 $doc = new DOMDocument();
              if(!$dom = $doc->load(FILE_CLASS)) {
                        die("Error opening xml file");
              }

             $NaturesActes = $doc->getElementsByTagName('NatureActe');
		
			 foreach ($NaturesActes as $NatureActe) {
   			     $tabNatures[$NatureActe->getAttribute('CodeNatureActe')]= utf8_decode($NatureActe->getAttribute('Libelle'));
			 }
			 return $tabNatures; 
        }

        function getMatiereListe(){
 			 $tabMatieres = array();
        	 $doc = new DOMDocument();
              if(!$dom = $doc->load(FILE_CLASS)) {
                        die("Error opening xml file");
              }

             $Matieres1 = $doc->getElementsByTagName('Matiere1');
			 foreach ($Matieres1 as $Matiere1) {
   			     $tabMatieres[$Matiere1->getAttribute('CodeMatiere')]= utf8_decode($Matiere1->getAttribute('Libelle'));
			 }
			 return $tabMatieres; 
        }

        function getDateClassification(){
			  $doc = new DOMDocument();
              if(!$dom = $doc->load(FILE_CLASS)) {
                        die("Error opening xml file");
              }
              
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
                else {
                    $this->redirect('/deliberations/transmit');
                }
                fclose($handle);

                } 
                else {
              	  echo "Le fichier FILENAME n'est pas accessible en �criture.";
                }
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
        
        function getCurrentPosition($id){
    		$conditions = "Deliberation.id = $id";
    		$field = 'position';
    		$obj = $this->Deliberation->findAll($conditions);
    	
    		return  $obj['0']['Deliberation']['position'];
  		}
	
   		function getCurrentSeance($id)
    	{
			$condition = "Deliberation.id = $id";
        	$objCourant = $this->Deliberation->findAll($condition);
			return $objCourant['0']['Deliberation']['seance_id'];
    	}
    
   		function getLastPosition( $seance_id) {
			return count($this->Deliberation->findAll("seance_id =$seance_id" ));
    	}
	
	function listerProjetsServicesAssemblees()
	{
		//liste les projets appartenants au service des assembl�es
		$conditions="etat = 2 ";
		$this->set('deliberations', $this->Deliberation->findAll($conditions));
	}
	
	
	
	function textprojetvue ($id = null)
	{
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="P"'));
		$this->set('deliberation', $this->Deliberation->read(null, $id));
		$this->set('delib_id', $id);
	}
	
	function textsynthesevue ($id = null)
	{
		$this->set('annexes',$this->Annex->findAll('deliberation_id='.$id.' AND type="S"'));
		$this->set('deliberation', $this->Deliberation->read(null, $id));
		$this->set('delib_id', $id);
	}
}
?>