<?php
class DeliberationsController extends AppController {

	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf' );
	var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit');
	
	function index() {

		$this->Deliberation->recursive = 0;
		$this->set('deliberations', $this->Deliberation->findAll(null,null, 'Seance.date'));

// TODO utilisation de la vue index?

//		$this->Deliberation->recursive = 0;
//		$deliberations = array();
//		$condition="etat = 1";
//		$tmpdeliberations=$this->Deliberation->findAll($condition);
//		$user=$this->Session->read('user');
//		foreach ($tmpdeliberations as $delib)
//		{
//			$circuit_id=$delib['Deliberation']['circuit_id'];
//			$data_circuit=$this->UsersCircuit->findAll("circuit_id=$circuit_id", null, "position ASC");
//			for($i=0; $i<count($data_circuit);$i++)
//			{
//				if ($data_circuit[$i]['UsersCircuit']['user_id']==$user['User']['id'])
//				{
//					//l'utilisateur logué apparait dans un circuit, on affiche la delib
//					
//					
//					/*recherche des actions que l'utilisateur logué peut faire
//					 *  1 - recherche si c'est à l'utilisateur logué de traiter la deliberation (selon table traitements)
//					 *  2 - recherche s'il peut modifier/supprimer/convertir la delib (selon profil)
//					 */
//	
//					
//					$conditions = "circuit_id =".$delib['Deliberation']['circuit_id']." AND User_id = ".$user['User']['id'];
//					$field = "position";
//					$traitements = $this->UsersCircuit->findAll($conditions, $field);
//					debug($traitements);
//					foreach ($traitements as $traitement) {
//						$position = $this->getPosition($delib['Deliberation']['circuit_id'], $delib['Deliberation']['id']);
//					     
//						if ($traitement['UsersCircuit']['position'] == $position){
//						    echo("D&eacute;lib &agrave; viser : ");
//						    $delib['a_traiter']=true;
//						    echo $delib['Deliberation']['id'];
//						    echo("<br>");
//					     }
//					     elseif ($traitement['UsersCircuit']['position'] > $position) {
//					     	echo("D&eacute;lib &agrave; Venir");
//					     	$delib['a_traiter']=false;
//						    echo $delib['Deliberation']['id'];
//						    echo("<br>");
//					     }
//						elseif ($traitement['UsersCircuit']['position'] < $position) {
//					     	echo("D&eacute;lib d&eacute;j&agrave; vis&eacute;e : ");
//					     	$delib['a_traiter']=false;
//						    echo $delib['Deliberation']['id'];
//						    echo("<br>");	// Délib déja visées
//					     }
//					}
//					array_push($deliberations, $delib);
//				}
//			}
//		}
//		$this->set('deliberations', $deliberations);

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
			//$this->cleanUpFields();
			//debug($this->data);
			//exit;
			$deliberation['Deliberation']['seance_id']= $this->data['Deliberation']['seance_id'];

			if ($this->Deliberation->save($this->data)) 
			{
				$this->redirect('/deliberations/listerMesProjets');
			}
			else
			{
				$this->Session->setFlash('Please correct errors below.');
				$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
				$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));	
				$conditions="seance_id is null";
				$this->set('deliberations', $this->Deliberation->findAll($conditions));
			}
		}
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
			
				$conditions=$conditions." circuit_id = ".$data['UsersCircuit']['circuit_id'];
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
				}else{
					$deliberation['action']="view";
					$deliberation['act']="voir";
				}
				//debug($deliberation);
				
				array_push($delib, $deliberation);
				
			}
		}
		$this->set('deliberations', $delib);
	}
	
	
	function getPosition($circuit_id, $delib_id){
		$odjCourant=array();
		$conditions = "Traitement.circuit_id = $circuit_id AND Traitement.delib_id=$delib_id ";
        $objCourant = $this->Traitement->findAll($conditions, null, "position DESC");
		return $objCourant['0']['Traitement']['position'];
	
	}
	
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Deliberation.');
			$this->redirect('/deliberations/listerProjetsATraiter');
		}
		$this->set('deliberation', $this->Deliberation->read(null, $id));
	}

	function add() {
	$user=$this->Session->read('user');
		if (empty($this->data)) {
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('rapporteurs', $this->Deliberation->User->generateList('statut=1'));
			$this->set('selectedRapporteur',key($this->Deliberation->User->generateList('service_id='.$user['User']['service'])));
			//debug($this->Deliberation->User->generateList('service_id='.$user['User']['service']));
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
			$this->render();
		} else {
			//$this->data['Deliberation']['seance_id']= $this->Utils->FrDateToUkDate($this->params['form']['seance_id']);
			
			$this->data['Deliberation']['redacteur_id']=$user['User']['id'];
			$this->data['Deliberation']['service_id']=$user['User']['service'];
			$this->cleanUpFields();
			//debug($this->data);
			
			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/deliberations/textprojet/'.$this->Deliberation->getLastInsertId());
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Deliberation->Service->generateList());
				$this->set('themes', $this->Deliberation->Theme->generateList());
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('users', $this->Deliberation->User->generateList());
				$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
				$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
			}
		}
	}
	
	function textsynthese ($id = null)
	{
		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
		} else {//debug($this->data);
			$this->data['Deliberation']['id']=$id;
			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/deliberations/attribuercircuit/'.$id);
				//$this->redirect('/deliberations/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}	
	}
	
	function textprojet ($id=null)
	{
		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
			//debug($this->data); 
		} else {
			
			$this->data['Deliberation']['id']=$id;
			//debug($this->data);
			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/deliberations/textsynthese/'.$id);
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}			
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Deliberation');
				$this->redirect('/deliberations/listerMesProjets');
			}
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('deliberations',$this->Deliberation->read(null, $id));
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('users', $this->Deliberation->User->generateList());
			$this->set('rapporteurs', $this->Deliberation->User->generateList('statut=1'));
						
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
		} else {
			$this->cleanUpFields();
			if ($this->Deliberation->save($this->data)) {
				$this->Session->setFlash('The Deliberation has been saved');
				$this->redirect('/deliberations/listerMesProjets');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Deliberation->Service->generateList());
				$this->set('themes', $this->Deliberation->Theme->generateList());
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('users', $this->Deliberation->User->generateList());
				$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
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
			$this->Session->setFlash('Invalid id for Deliberation');
			$this->redirect('/deliberations/listerMesProjets');
		}
		if ($this->Deliberation->del($id)) {
			$this->Session->setFlash('The Deliberation deleted: id '.$id.'');
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
				$this->Session->setFlash('Please correct errors below.');
			}
		}	
	}
	
	
	function traiter($id = null, $valid=null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Deliberation.');
			$this->redirect('/deliberations/listerProjetsATraiter');
		}
		else
		{
			if ($valid==null)
			{
				
				$this->set('deliberation', $this->Deliberation->read(null, $id));
				//debug($this);
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
					
					//on a refusé le projet, il repars au redacteur
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

	
}
?>