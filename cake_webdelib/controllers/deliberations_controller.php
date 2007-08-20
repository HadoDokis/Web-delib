<?php
class DeliberationsController extends AppController {

	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf' );
	var $uses = array('Deliberation', 'AgentsCircuit', 'Traitement', 'Agent', 'Circuit');
	
	function index() {

		$this->Deliberation->recursive = 0;
		$this->set('deliberations', $this->Deliberation->findAll(null,null, 'Seance.date'));

// TODO utilisation de la vue index?

//		$this->Deliberation->recursive = 0;
//		$deliberations = array();
//		$condition="etat = 1";
//		$tmpdeliberations=$this->Deliberation->findAll($condition);
//		$agent=$this->Session->read('agent');
//		foreach ($tmpdeliberations as $delib)
//		{
//			$circuit_id=$delib['Deliberation']['circuit_id'];
//			$data_circuit=$this->AgentsCircuit->findAll("circuit_id=$circuit_id", null, "position ASC");
//			for($i=0; $i<count($data_circuit);$i++)
//			{
//				if ($data_circuit[$i]['AgentsCircuit']['agent_id']==$agent['Agent']['id'])
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
//					$conditions = "circuit_id =".$delib['Deliberation']['circuit_id']." AND Agent_id = ".$agent['Agent']['id'];
//					$field = "position";
//					$traitements = $this->AgentsCircuit->findAll($conditions, $field);
//					debug($traitements);
//					foreach ($traitements as $traitement) {
//						$position = $this->getPosition($delib['Deliberation']['circuit_id'], $delib['Deliberation']['id']);
//					     
//						if ($traitement['AgentsCircuit']['position'] == $position){
//						    echo("D&eacute;lib &agrave; viser : ");
//						    $delib['a_traiter']=true;
//						    echo $delib['Deliberation']['id'];
//						    echo("<br>");
//					     }
//					     elseif ($traitement['AgentsCircuit']['position'] > $position) {
//					     	echo("D&eacute;lib &agrave; Venir");
//					     	$delib['a_traiter']=false;
//						    echo $delib['Deliberation']['id'];
//						    echo("<br>");
//					     }
//						elseif ($traitement['AgentsCircuit']['position'] < $position) {
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
		//liste les projets dont je suis le redacteur
		$agent=$this->Session->read('agent');
		$agent_id=$agent['Agent']['id'];
		$conditions="etat = 0 AND redacteur_id = $agent_id";
		$this->set('deliberations', $this->Deliberation->findAll($conditions));
	}
	
	function listerProjetsAttribuer()
	{
		if (empty ($this->data))
		{
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));	
			$conditions="seance_id != 0";
			$this->set('deliberations', $this->Deliberation->findAll($conditions));
		}
	}
		
		
	function listerProjetsNonAttribuer()
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
		 * A FAIRE : verifier aussi le service, voir si un meme agent peut appartenir à plusieurs services
		 * et apparaitre plusieurs fois dans le meme circuit
		 * CSQ : qui se connecte? un agent ou un agent service? remise en cause de la relation "un agent
		 * peut appartenir à plusieurs services
		 */
		//liste les projets où j'apparais dans le circuit de validation
		$agent=$this->Session->read('agent');
		$agent_id=$agent['Agent']['id'];
		$data_circuit=$this->AgentsCircuit->findAll("agent_id=$agent_id", null, "position ASC");
		$conditions="";
		$delib=array();
		$cpt=0;
		foreach ($data_circuit as $data)
		{
			if ($cpt>0)
				$conditions=$conditions." OR ";
			
			$conditions=$conditions." circuit_id = ".$data['AgentsCircuit']['circuit_id'];
			$cpt++;
		}
		$deliberations = $this->Deliberation->findAll($conditions);
		//debug($deliberations);
		//debug($data_circuit);
		foreach ($deliberations as $deliberation)
		{
			//on recupere la position courante de la deliberation
			$lastTraitement=array_pop($deliberation['Traitement']);
			
			//on recupere la position de l'agent dans le circuit
			foreach ($data_circuit as $data)
			{
				if ($data['AgentsCircuit']['circuit_id']==$lastTraitement['circuit_id'])
				{
					$position_agent=$data['AgentsCircuit']['position'];
				}
			}
			
			if ($lastTraitement['position']==$position_agent)
				$deliberation['action']="traiter";
				else
				$deliberation['action']="view";
			//debug($data);
			//debug($position_agent);
			//exit;
			array_push($delib, $deliberation);
			//debug($delib);
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
		if (empty($this->data)) {
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('agents', $this->Deliberation->Agent->generateList());
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$this->set('date_seances', $this->Deliberation->Seance->generateList($condition,'date asc',null,'{n}.Seance.id','{n}.Seance.date'));
			$this->render();
		} else {
			//$this->data['Deliberation']['seance_id']= $this->Utils->FrDateToUkDate($this->params['form']['seance_id']);
			$agent=$this->Session->read('agent');

			$this->data['Deliberation']['redacteur_id']=$agent['Agent']['id'];
			$this->cleanUpFields();
			if ($this->Deliberation->save($this->data)) {
				$this->redirect('/deliberations/textprojet/'.$this->Deliberation->getLastInsertId());
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Deliberation->Service->generateList());
				$this->set('themes', $this->Deliberation->Theme->generateList());
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('agents', $this->Deliberation->Agent->generateList());
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
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList());
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('agents', $this->Deliberation->Agent->generateList());
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
				$this->set('agents', $this->Deliberation->Agent->generateList());
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
			//$this->redirect('/deliberations/listerMesProjets');
			$this->redirect('/deliberations/index');
		}
	}
 
   function convert($id=null)
        {
            $this->layout = 'pdf'; //this will use the pdf.thtml layout
            $this->set('text_projet',  $this->getField($id, 'texte_projet'));
            $this->set('text_synthese',$this->getField($id, 'texte_synthese'));
            $this->set('date_session', $this->getField($id, 'date_session'));
            $this->set('rapporteur_id',   $this->getField($id, 'rapporteur_id'));
            $this->set('objet',        $this->getField($id, 'objet'));
  
            $this->render();
        } 
        
	function attribuercircuit ($id = null, $circuit_id=null)
	{
		if (empty($this->data)) {
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('lastPosition', '-1');
			$listeAgents['id']=array();
			$listeAgents['nom']=array();
			$listeAgents['prenom']=array();
			$listeAgentCircuit['id']=array();
	       	$listeAgentCircuit['circuit_id']=array();
	       	$listeAgentCircuit['libelle']=array();
	       	$listeAgentCircuit['agent_id']=array();
	       	$listeAgentCircuit['nom']=array();
	       	$listeAgentCircuit['prenom']=array();
	       	$listeAgentCircuit['service_id']=array();
	       	$listeAgentCircuit['position']=array();
	       	$listeAgentCircuit['service_libelle']=array();
			$circuits=$this->Deliberation->Circuit->generateList(null, "libelle ASC");
		
			//affichage du circuit existant
			if (isset($circuit_id)){	
			    $this->set('circuit_id', $circuit_id);
			    $condition = "AgentsCircuit.circuit_id = $circuit_id";
			    $desc = 'position ASC';
		     
    	   		$tmplisteAgentCircuit = $this->AgentsCircuit->findAll($condition, null, $desc);
    	   		 
    	   		for ($i=0; $i<count($tmplisteAgentCircuit);$i++) {
    	   			array_push($listeAgentCircuit['id'], $tmplisteAgentCircuit[$i]['AgentsCircuit']['id']);
    	   			array_push($listeAgentCircuit['circuit_id'], $tmplisteAgentCircuit[$i]['AgentsCircuit']['circuit_id']);
    	   			array_push($listeAgentCircuit['libelle'], $tmplisteAgentCircuit[$i]['Circuit']['libelle']);
    	   			array_push($listeAgentCircuit['agent_id'], $tmplisteAgentCircuit[$i]['AgentsCircuit']['agent_id']);
    	   			array_push($listeAgentCircuit['nom'], $tmplisteAgentCircuit[$i]['Agent']['nom']);
    	   			array_push($listeAgentCircuit['prenom'], $tmplisteAgentCircuit[$i]['Agent']['prenom']);
    	   			array_push($listeAgentCircuit['service_libelle'], $tmplisteAgentCircuit[$i]['Service']['libelle']);
    	   			array_push($listeAgentCircuit['service_id'], $tmplisteAgentCircuit[$i]['AgentsCircuit']['service_id']);
    	   			array_push($listeAgentCircuit['position'], $tmplisteAgentCircuit[$i]['AgentsCircuit']['position']);
    	   		}
				 
  				$this->set('listeAgentCircuit', $listeAgentCircuit);
  			}
			else 
				$this->set('circuit_id', '0');
			
			$this->set('circuits', $circuits);
				} else {
				$this->data['Deliberation']['id']=$id;
				$this->data['Deliberation']['date_envoi']=date('Y-m-d H:i:s', time());
				$this->data['Deliberation']['etat']='1';
				if ($this->Deliberation->save($this->data)) {
					

					//enregistrement dans la table traitements
					// TODO Voir comment améliorer ce point (associations cakephp).
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
					
					//MAJ de la date de traitement de la dernière position courante $lastpos
					$tab[$lastpos]['Traitement']['date_traitement']=date('Y-m-d H:i:s', time());
					$this->Traitement->save($tab[$lastpos]['Traitement']);
					
					$this->data['Traitement']['id']='';
					
					$this->data['Traitement']['position']=$tab[$lastpos]['Traitement']['position']+1;
					$circuit_id=$tab[$lastpos]['Traitement']['circuit_id'];
					$this->data['Traitement']['delib_id']=$id;
					$this->data['Traitement']['circuit_id']=$circuit_id;
					
					
					$this->Traitement->save($this->data['Traitement']);
					
					$this->redirect('/deliberations/listerProjetsATraiter');
				}
				else
				{	
					//on a refusé le projet, il repars au redacteur
					
				}
			}
		}
	}

	
}
?>