<?php
class DeliberationsController extends AppController {

	var $name = 'Deliberations';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf' );
	var $uses = array('Deliberation', 'AgentsCircuit', 'Traitement', 'Agent', 'Circuit');
	
	function index() {
		$this->Deliberation->recursive = 0;
		$this->set('deliberations', $this->Deliberation->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Deliberation.');
			$this->redirect('/deliberations/index');
		}
		$this->set('deliberation', $this->Deliberation->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList(null,'libelle asc',null,'{n}.Theme.id','{n}.Theme.libelle'));
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('agents', $this->Deliberation->Agent->generateList());
			$this->render();
		} else {
			$this->data['Deliberation']['date_session']= $this->Utils->FrDateToUkDate($this->params['form']['date_session']);
			$agent=$this->Session->read('agent');
			debug($agent);
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
				$this->redirect('/deliberations/index');
			}
			$this->data = $this->Deliberation->read(null, $id);
			$this->set('services', $this->Deliberation->Service->generateList());
			$this->set('themes', $this->Deliberation->Theme->generateList());
			$this->set('circuits', $this->Deliberation->Circuit->generateList());
			$this->set('agents', $this->Deliberation->Agent->generateList());
		} else {
			$this->cleanUpFields();
			if ($this->Deliberation->save($this->data)) {
				$this->Session->setFlash('The Deliberation has been saved');
				$this->redirect('/deliberations/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('services', $this->Deliberation->Service->generateList());
				$this->set('themes', $this->Deliberation->Theme->generateList());
				$this->set('circuits', $this->Deliberation->Circuit->generateList());
				$this->set('agents', $this->Deliberation->Agent->generateList());
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
			$this->redirect('/deliberations/index');
		}
		if ($this->Deliberation->del($id)) {
			$this->Session->setFlash('The Deliberation deleted: id '.$id.'');
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
				//$this->data['']
				if ($this->Deliberation->save($this->data)) {
					

					//enregistrement dans la table traitements
					// TODO Voir comment améliorer ce point (associations cakephp).
					$this->data['Traitement']['delib_id']=$id;
					$this->data['Traitement']['circuit_id']=$circuit_id;
					$this->data['Traitement']['position']='1';
					$this->Traitement->save($this->data['Traitement']);
										
					
					$this->redirect('/deliberations/index');
				} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}	
	}
	
	
	function traiter($id = null, $valid=null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Deliberation.');
			$this->redirect('/deliberations/index');
		}
		else
		{
			if ($valid==null)
			{
				
				$this->set('deliberation', $this->Deliberation->read(null, $id));
				debug($this);
			}
			else
			{
				if ($valid=='1') 
				{
					//on a validé le projet, il passe à la personne suivante
					$tab=$this->Traitement->findAll("delib_id = $id");
					$lastpos=count($tab)-1;
					
					$this->data['Traitement']['position']=$tab[$lastpos]['Traitement']['position']+1;
					$circuit_id=$tab[$lastpos]['Traitement']['circuit_id'];
					$this->data['Traitement']['delib_id']=$id;
					$this->data['Traitement']['circuit_id']=$circuit_id;
					//debug($this->data['Traitement']);
					$this->Traitement->save($this->data['Traitement']);
					$this->redirect('/deliberations/index');
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