<?php
class CircuitsController extends AppController {

	var $name = 'Circuits';
	var $helpers = array('Html', 'Form' , 'Javascript');
	var $uses = array('Circuit', 'User', 'Service', 'UsersService', 'UsersCircuit', 'Deliberation');

	// Gestion des droits
	var $aucunDroit = array('getCurrentCircuit', 'getCurrentPosition', 'getLastPosition', 'intervertirPosition', 'isEditable');
	var $commeDroit = array('addUser'=>'Circuits:index', 'supprimerUser'=>'Circuits:index', 'add'=>'Circuits:index', 'delete'=>'Circuits:index', 'view'=>'Circuits:index', 'edit'=>'Circuits:index');

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le circuit.');
			$this->redirect('/circuits/index');
		}
		$this->set('circuit', $this->Circuit->read(null, $id));
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le circuit');
				$this->redirect('/circuits/index');
			}
			$this->data = $this->Circuit->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Circuit->save($this->data)) {
				$this->Session->setFlash('Le circuit a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/circuits/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le circuit');
			$this->redirect('/circuits/index');
		}
		if ($this->Circuit->del($id)) {
			$this->Session->setFlash('Le circuit a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/circuits/index');
		}
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Circuit->save($this->data)) {
				$this->Session->setFlash('Le circuit a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/circuits/index');
			} else {
			}
		}
	}

	function index($circuit_id=null, $service_id=null) {
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
		$circuits=$this->Circuit->generateList(null, "libelle ASC");

		//affichage du circuit existant
		if (isset($circuit_id)){
		    $this->set('circuit_id', $circuit_id);
		    $condition = "UsersCircuit.circuit_id = $circuit_id";
		    $desc = 'UsersCircuit.position ASC';

		    $this->set('isEditable', $this->isEditable($circuit_id));
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

		//traitement du circuit (cr�ation ou modification)
		if (empty($this->data)) {
			if ($service_id!=null) {
				$liste_users=$this->UsersService->findAll("UsersService.service_id=$service_id");

				for ($i=0; $i<count($liste_users);$i++){
				    array_push($listeUsers['id'], $liste_users[$i]['UsersService']['user_id']);
				    array_push($listeUsers['nom'],  $this->requestAction("users/getNom/".$liste_users[$i]['UsersService']['user_id']));
				    array_push($listeUsers['prenom'], $this->requestAction("users/getPrenom/".$liste_users[$i]['UsersService']['user_id']));
				}
				$this->set('service_id', $service_id);
  			    $this->set('listeUser', $listeUsers);
			}
			$this->render();
		}
	}

	function addUser($circuit_id=null, $service_id=null, $user_id=null)
	{
		$condition = "circuit_id = $circuit_id";
        $data = $this->UsersCircuit->findAll($condition);
        $position = $this->getLastPosition($circuit_id) + 1;

        //on recherche si l'utilisateur existe d�j� dans le circuit de validation
		$uniq=true;
		$i=0;
		while(($uniq==true)&&($i<sizeof($data)))
		{
			if ($data[$i]['UsersCircuit']['user_id']==$user_id)
			{
				$uniq=false; //il existe
			}
			$i++;
		}

		if ($uniq==true)
		{
       		$this->params['data']['UsersCircuit']['position'] = $position;
			$this->params['data']['UsersCircuit']['circuit_id'] = $circuit_id ;
			$this->params['data']['UsersCircuit']['service_id'] = $service_id ;
			$this->params['data']['UsersCircuit']['user_id']   = $user_id ;

			if ($this->UsersCircuit->save($this->params['data'])){
			    $this->redirect("/circuits/index/$circuit_id/$service_id");
			}
			else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
		else
		{
			$this->Session->setFlash("L'utilisateur est d�j� dans le circuit !");
			$this->redirect("/circuits/index/$circuit_id/$service_id");
		}
	}

    function intervertirPosition ($oldIdPos, $sens) {
    	// $sens == 0 => Descendre
        // $sens == 1 => Monter

		$positionCourante = $this->getCurrentPosition($oldIdPos);
		$circuitCourant  = $this->getCurrentCircuit($oldIdPos);
	   	$lastPosition = $this->getLastPosition($circuitCourant);

        if ($sens != 0)
            $conditions = "UsersCircuit.circuit_id = $circuitCourant  AND UsersCircuit.position = $positionCourante-1";
       	else            // on recupere l'objet precedent
   		    $conditions = "UsersCircuit.circuit_id = $circuitCourant  AND UsersCircuit.position = $positionCourante+1";

		$obj = $this->UsersCircuit->findAll($conditions);
		//position du suivant ou du precedent
        $id_obj = $obj['0']['UsersCircuit']['id'];
		$newPosition = $obj['0']['UsersCircuit']['position'];
		// On r�cup�re les informations de l'objet courant
		$this->data = $this->UsersCircuit->read(null, $oldIdPos);
		$this->data['UsersCircuit']['position'] = $newPosition;

		//enregistrement de l'objet courant avec la nouvelle position
		if (!$this->UsersCircuit->save($this->data)) {
		   die('Erreur durant l\'enregistrement');
		}
		// On r�cup�re les informations de l'objet � d�placer
		$this->data = $this->UsersCircuit->read(null, $id_obj);
		$this->data['UsersCircuit']['position']= $positionCourante;

		//enregistrement de l'objet � d�placer avec la position courante
		if ($this->UsersCircuit->save($this->data)) {
			if ($sens ==2)
			    return true;
			else
			    $this->redirect("/circuits/index/$circuitCourant/");
		}
		else {
		    $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
	}

	function supprimerUser($id) {
		$position     = $this->getCurrentPosition($id);
		$circuit_id   = $this->getCurrentCircuit($id);
		$lastPosition = $this->getLastPosition($circuit_id);

		if ($lastPosition != $position) {
			$conditions = "circuit_id = $circuit_id and position > $position ";
			$order = "position ASC";
			$obj = $this->UsersCircuit->findAll($conditions, null, $order);

			foreach ($obj as $user)
			    $this->intervertirPosition($user['UsersCircuit']['id'], 2);

			$conditions = "circuit_id = $circuit_id and position = $lastPosition-1";
			$avantDernier = $this->UsersCircuit->findAll($conditions);

		    $this->data = $this->UsersCircuit->read(null, $avantDernier[0]['UsersCircuit']['id']);
		    $this->data['UsersCircuit']['position'] = $lastPosition-1;
			$this->UsersCircuit->save($this->data);

		}

		if ($this->UsersCircuit->del($id))
		    $this->redirect("/circuits/index/$circuit_id/");
	    else
		    $this->Session->setFlash('Suppression impossible');

	}

	function isEditable ($circuit_id) {
		$condition = "circuit_id=$circuit_id and etat=1";
		$delibInCircuit = $this->Deliberation->findAll($condition);
		return empty($delibInCircuit);
	}

  	function getCurrentPosition($id){
    	$conditions = "UsersCircuit.id = $id";
    	$field = 'position';
    	$obj = $this->UsersCircuit->findAll($conditions);

    	return  $obj['0']['UsersCircuit']['position'];
    }

    function getCurrentCircuit($id)
    {
		$condition = "UsersCircuit.id = $id";
        $objCourant = $this->UsersCircuit->findAll($condition);
		return $objCourant['0']['UsersCircuit']['circuit_id'];

    }

   	function getLastPosition($circuit_id) {
		return count($this->UsersCircuit->findAll("circuit_id = $circuit_id"));
    }



}
?>
