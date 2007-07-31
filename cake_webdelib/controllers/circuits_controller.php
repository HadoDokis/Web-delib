<?php
class CircuitsController extends AppController {

	var $name = 'Circuits';
	var $helpers = array('Html', 'Form' , 'Javascript');
	var $uses = array('Circuit', 'Agent', 'Service', 'AgentsService', 'AgentsCircuit');

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Circuit.');
			$this->redirect('/circuits/index');
		}
		$this->set('circuit', $this->Circuit->read(null, $id));
	}
	
	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Circuit');
				$this->redirect('/circuits/index');
			}
			$this->data = $this->Circuit->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Circuit->save($this->data)) {
				$this->Session->setFlash('The Circuit has been saved');
				$this->redirect('/circuits/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Circuit');
			$this->redirect('/circuits/index');
		}
		if ($this->Circuit->del($id)) {
			$this->Session->setFlash('The Circuit deleted: id '.$id.'');
			$this->redirect('/circuits/index');
		}
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Circuit->save($this->data)) {
				$this->Session->setFlash('The Circuit has been saved');
				$this->redirect('/circuits/index');
			} else {
			}
		}
	}

	function index($circuit_id=null, $service_id=null) {
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
		$circuits=$this->Circuit->generateList(null, "libelle ASC");
		
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
  			$this->set('lastPosition', $this->getLastPosition($circuit_id));
		}
		else 
			$this->set('circuit_id', '0');
		
		$this->set('circuits', $circuits);
		
		$services=$this->Service->generateList(null, "libelle ASC");
		if (isset($service_id))
		    $this->set('service_id', $service_id);
		else 
		    $this->set('service_id', '0');
		    
		$this->set('services', $services);

		//traitement d circuit (création ou modification)
		if (empty($this->data)) {
			if ($service_id!=null) {
				$liste_agents=$this->AgentsService->findAll("AgentsService.service_id=$service_id");			

				for ($i=0; $i<count($liste_agents);$i++){
				    array_push($listeAgents['id'], $liste_agents[$i]['AgentsService']['agent_id']); 
				    array_push($listeAgents['nom'],  $this->requestAction("agents/getNom/".$liste_agents[$i]['AgentsService']['agent_id']));
				    array_push($listeAgents['prenom'], $this->requestAction("agents/getPrenom/".$liste_agents[$i]['AgentsService']['agent_id']));
				}
				$this->set('service_id', $service_id);
  			    $this->set('listeAgent', $listeAgents);
			}
			$this->render();
		}
	}

	function addAgent($circuit_id=null, $service_id=null, $agent_id=null)
	{
		$condition = "circuit_id = $circuit_id";
        $data = $this->AgentsCircuit->findAll($condition);	
        $position = $this->getLastPosition($circuit_id) + 1;
		
		$this->params['data']['AgentsCircuit']['position'] = $position;
		$this->params['data']['AgentsCircuit']['circuit_id'] = $circuit_id ;
		$this->params['data']['AgentsCircuit']['service_id'] = $service_id ;
		$this->params['data']['AgentsCircuit']['agent_id']   = $agent_id ;
			
		if ($this->AgentsCircuit->save($this->params['data'])){
		    $this->redirect("/circuits/index/$circuit_id/$service_id");
		}
		else {
			$this->Session->setFlash('Please correct errors below.');
		}
	}
	    
    function intervertirPosition ($oldIdPos, $sens) {
    	// $sens == 0 => Descendre
        // $sens == 1 => Monter

		$positionCourante = $this->getCurrentPosition($oldIdPos); 
		$circuitCourant  = $this->getCurrentCircuit($oldIdPos);
	   	$lastPosition = $this->getLastPosition($circuitCourant);
	     	
        if ($sens != 0)
            $conditions = "AgentsCircuit.circuit_id = $circuitCourant  AND position = $positionCourante-1";
       	else            // on récupère l'objet précédent
   		    $conditions = "AgentsCircuit.circuit_id = $circuitCourant  AND position = $positionCourante+1";

		$obj = $this->AgentsCircuit->findAll($conditions);	
		//position du suivant ou du precedent
        $id_obj = $obj['0']['AgentsCircuit']['id'];
		$newPosition = $obj['0']['AgentsCircuit']['position'];
		// On récupère les informations de l'objet courant
		$this->data = $this->AgentsCircuit->read(null, $oldIdPos);
		$this->data['AgentsCircuit']['position'] = $newPosition;
		
		//enregistrement de l'objet courant avec la nouvelle position
		if (!$this->AgentsCircuit->save($this->data)) {
		   die('Erreur durant l\'enregistrement');
		}
		// On récupère les informations de l'objet à déplacer
		$this->data = $this->AgentsCircuit->read(null, $id_obj);
		$this->data['AgentsCircuit']['position']= $positionCourante;
		
		//enregistrement de l'objet à déplacer avec la position courante
		if ($this->AgentsCircuit->save($this->data)) {
			if ($sens ==2)
			    return true;
			else	
			    $this->redirect("/circuits/index/$circuitCourant/");
		}
		else {
		    $this->Session->setFlash('Please correct errors below.');
		}
	}

	function supprimerAgent($id) {
		$position     = $this->getCurrentPosition($id);
		$circuit_id   = $this->getCurrentCircuit($id);
		$lastPosition = $this->getLastPosition($circuit_id);
	
		if ($lastPosition != $position) {
			$conditions = "circuit_id = $circuit_id and position > $position ";
			$order = "position ASC";
			$obj = $this->AgentsCircuit->findAll($conditions, null, $order);	
			
			foreach ($obj as $agent)	
			    $this->intervertirPosition($agent['AgentsCircuit']['id'], 2);
			
			$conditions = "circuit_id = $circuit_id and position = $lastPosition-1";
			$avantDernier = $this->AgentsCircuit->findAll($conditions);	

		    $this->data = $this->AgentsCircuit->read(null, $avantDernier[0]['AgentsCircuit']['id']);
		    $this->data['AgentsCircuit']['position'] = $lastPosition-1;
			$this->AgentsCircuit->save($this->data);

		}
		
		if ($this->AgentsCircuit->del($id))
		    $this->redirect("/circuits/index/$circuit_id/");
	    else
		    $this->Session->setFlash('Suppression impossible');

	}
	

	
  	function getCurrentPosition($id){
    	$conditions = "AgentsCircuit.id = $id";
    	$field = 'position';
    	$obj = $this->AgentsCircuit->findAll($conditions);
    	
    	return  $obj['0']['AgentsCircuit']['position'];
    }
	
    function getCurrentCircuit($id)
    {
		$condition = "AgentsCircuit.id = $id";
        $objCourant = $this->AgentsCircuit->findAll($condition);
		return $objCourant['0']['AgentsCircuit']['circuit_id'];
    	
    }
    
   	function getLastPosition($circuit_id) {
		return count($this->AgentsCircuit->findAll("circuit_id = $circuit_id"));
    }
	
  
	
}
?>