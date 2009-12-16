<?php
class CircuitsController extends AppController {

	var $name = 'Circuits';
	var $helpers = array('Html', 'Form' , 'Javascript');
	var $uses = array('Commentaire', 'Circuit', 'User', 'Service', 'UsersService', 'UsersCircuit', 'Deliberation', 'Traitement');
	var $components = array('Parafwebservice');

	// Gestion des droits
	var $aucunDroit = array('getCurrentCircuit', 'getCurrentPosition', 'getLastPosition', 'intervertirPosition', 'isEditable','listDelibsDansParapheur');
	var $commeDroit = array('addUser'=>'Circuits:index', 'supprimerUser'=>'Circuits:index', 'add'=>'Circuits:index', 'delete'=>'Circuits:index', 'view'=>'Circuits:index', 'edit'=>'Circuits:index');

        function listDelibsDansParapheur() {
            //On récupére la liste des id 
            $circuits = $this->UsersCircuit->findAll('UsersCircuit.service_id = -1', 'circuit_id');
	    // Si empty de circuit => On utilise pas de parapheur dasn les circuits 
            if (empty($circuits))
                 return true; 

            // Controle de l'avancement des projets dans le parapheur
            foreach ($circuits as $circuit) {
                $delibs = $this->Deliberation->findAll("Deliberation.etat = 1 AND Deliberation.circuit_id =  ".$circuit['UsersCircuit']['circuit_id']);
                foreach($delibs as $delib){
                    $circuit_id = $delib['Deliberation']['circuit_id'];
		    $nbEtapes = $this->UsersCircuit->findCount("UsersCircuit.circuit_id=$circuit_id");
                    $delib_id   = $delib['Deliberation']['id'];
                    $traitement = $this->Traitement->find("Traitement.delib_id = $delib_id AND Traitement.circuit_id = $circuit_id ", "MAX(position) as pos");
                    $positionCourante   = $traitement[0]['pos'];
                    $tmp = $this->UsersCircuit->find("UsersCircuit.circuit_id=$circuit_id AND UsersCircuit.service_id = -1 AND UsersCircuit.position=  $positionCourante ");
                    if (!empty($tmp)){
                        if ($this->_checkEtatParapheur($delib_id)) {
			    $this->Traitement->create();
			    $traitement['Traitement']['delib_id']   =  $delib_id;
			    $traitement['Traitement']['circuit_id'] = $circuit_id;
			    $traitement['Traitement']['position']   =  $positionCourante + 1;
			    $this->Traitement->save($traitement['Traitement']);
                            if ($nbEtapes ==  $positionCourante ) {
                                // on change l'etat de la delib à 2
                               $del = $this->Deliberation->read(null,  $delib_id);
			       $del['Deliberation']['etat'] = 2;
			       $this->Deliberation->save($del);
			    }
		        }
	            }
                }
            }

            // Controle de l'avancement des délibérations dans le parapheur
	    $delibs = $this->Deliberation->findAll("Deliberation.etat = 3 AND Deliberation.etat_parapheur = 1 ");
            foreach ($delibs as $delib) {
                 $this->_checkEtatParapheur($delib['Deliberation']['id']);
	    }

	    $this->layout = null;
        }
   
        function _checkEtatParapheur($delib_id, $tdt=false) {
            $histo = $this->Parafwebservice->getHistoDossierWebservice(PREFIX_WEBDELIB.$delib_id);
	    for ($i =0; $i < count($histo['logdossier']); $i++){
		if(!$tdt){
	    	   if (($histo['logdossier'][$i]['status']  ==  'Signe') || ($histo['logdossier'][$i]['status']  ==  'Archive')) {
	           // TODO LIST : Récupère la date et heure de signature  + QUi l'a signé (annotation)
			   $this->Commentaire->create();
	                   $comm ['Commentaire']['delib_id'] = $delib_id;
	                   $comm ['Commentaire']['agent_id'] = -1;
			   $comm ['Commentaire']['texte'] = utf8_decode($histo['logdossier'][$i]['nom']." : ".$histo['logdossier'][$i]['annotation']);	
			   $comm ['Commentaire']['commentaire_auto'] = 0;
	                   $this->Commentaire->save($comm['Commentaire']); 

			   $delib=$this->Deliberation->read(null, $delib_id);
			   if ($delib['Deliberation']['etat_parapheur']==1){
			       // etat_paraph à 1, donc, nous sommes en post_seance, on ne supprime pas le projet
                               $delib['Deliberation']['etat_parapheur']=2;      
			       $this->Deliberation->save($delib);
			   }
			   else {
			   // On est dans un circuit d'élaboration, 
			   // On est obligé de supprimé le projet sinon, on ne peut pas le ré-insérer dans un autre circuit du parapheur
			       $archdos = $this->Parafwebservice->archiverDossierWebservice(PREFIX_WEBDELIB.$delib_id, 'EFFACER');
			   }
			       
	                   return true;
			   
		       }
		       elseif(($histo['logdossier'][$i]['status']=='RejetSignataire')||($histo['logdossier'][$i]['status']=='RejetVisa') ){ // Cas de refus dans le parapheur
			   
	                   $this->Commentaire->create();
	                   $comm ['Commentaire']['delib_id'] = $delib_id;
	                   $comm ['Commentaire']['agent_id'] = -1;
	                   $comm ['Commentaire']['texte'] = utf8_decode($histo['logdossier'][$i]['nom']." : ".$histo['logdossier'][$i]['annotation']);
	                   $comm ['Commentaire']['commentaire_auto'] = 0;
	                   $this->Commentaire->save($comm['Commentaire']);
					   $this->Deliberation->refusDossier($delib_id);
			   //             Supprimer le dossier du parapheur
	                   $effdos = $this->Parafwebservice->effacerDossierRejeteWebservice(PREFIX_WEBDELIB.$delib_id);
		       }			 
            }
            else{
            	if ($histo['logdossier'][$i]['status']  ==  'EnCoursTransmission'){
            		return true;
            	}
            }
	    }
            return false;
        }

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

            $this->set('lastPosition', '-1');
            if (USE_PARAPH) {
	        $i=0;
                $listCircuitsParaph = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
                foreach  ($listCircuitsParaph['soustype'] as $circuitParaph){
		    $listCircuitsParaph['circuit'][$i]= $this->Parafwebservice->getCircuit(TYPETECH,  $circuitParaph);
		    $i++;
                }
            }
            $circuits=$this->Circuit->generateList(null, "libelle ASC");

             //affichage du circuit existant 
            if (isset($circuit_id)){
                $this->set('circuit_id', $circuit_id);
                $this->set('isEditable', $this->isEditable($circuit_id));
		$listeUserCircuit = $this->UsersCircuit->afficheListeCircuit($circuit_id, $listCircuitsParaph);
                $this->set('listeUserCircuit', $listeUserCircuit);
                $this->set('lastPosition', $this->getLastPosition($circuit_id));
            }
            else
                $this->set('circuit_id', '0');

            $this->set('circuits', $circuits);

            $services=$this->Service->generateList('Service.actif=1', "libelle ASC");
            if (USE_PARAPH) 
                $services['-1']= 'i-parapheur';
                
            if (isset($service_id))
                $this->set('service_id', $service_id);
            else
                $this->set('service_id', '0');
            $this->set('services', $services);

           //traitement du circuit (création ou modification)
            if (empty($this->data)) {
                if ($service_id!=null) {
                    if ($service_id == -1){
			for ($i=0; $i<count($listCircuitsParaph['soustype']);$i++){
			    $circ = "";
                            array_push($listeUsers['id'], $i);
			    foreach ($listCircuitsParaph['circuit'][$i] as $etape)
			        for($j=0; $j<count($etape) ; $j++)
				    $circ .= utf8_decode($etape[$j]['Prenom']).' '.utf8_decode($etape[$j]['Nom']).', ';

                            array_push($listeUsers['nom'],   $circ);
                            array_push($listeUsers['prenom'], "<u>".$listCircuitsParaph['soustype'][$i]. '</u> : ');
                        }
                    }
                    else {
                        $liste_users=$this->UsersService->findAll("UsersService.service_id=$service_id");
                        for ($i=0; $i<count($liste_users);$i++){
                            array_push($listeUsers['id'], $liste_users[$i]['UsersService']['user_id']);
                            array_push($listeUsers['nom'],  $this->requestAction("users/getNom/".$liste_users[$i]['UsersService']['user_id']));
                            array_push($listeUsers['prenom'], $this->requestAction("users/getPrenom/".$liste_users[$i]['UsersService']['user_id']));
                        }
                    }
                    $this->set('service_id', $service_id);
                    $this->set('listeUser', $listeUsers);
                    $this->render();
		}
	    }
        }

	function addUser($circuit_id=null, $service_id=null, $user_id=null)
	{
            $condition = "circuit_id = $circuit_id";
            $data = $this->UsersCircuit->findAll($condition);
            $position = $this->getLastPosition($circuit_id) + 1;

            //on recherche si l'utilisateur existe déjà dans le circuit de validation
	    $uniq=true;
	    $i=0;
	    while(($uniq==true)&&($i<sizeof($data))) {
                 if (($data[$i]['UsersCircuit']['user_id']==$user_id)&& ($data[$i]['UsersCircuit']['service_id']==$service_id)) {
                     $uniq=false; //il existe
                 }
                 $i++;
            }

            if ($uniq==true)  {
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
            else {
                 $this->Session->setFlash("L'utilisateur est déjà dans le circuit !");
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
		// On récupère les informations de l'objet courant
		$this->data = $this->UsersCircuit->read(null, $oldIdPos);
		$this->data['UsersCircuit']['position'] = $newPosition;

		//enregistrement de l'objet courant avec la nouvelle position
		if (!$this->UsersCircuit->save($this->data)) {
		   die('Erreur durant l\'enregistrement');
		}
		// On récupère les informations de l'objet à déplacer
		$this->data = $this->UsersCircuit->read(null, $id_obj);
		$this->data['UsersCircuit']['position']= $positionCourante;

		//enregistrement de l'objet à déplacer avec la position courante
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
		$iPosition = 0;
    	$userCircuit = $this->UsersCircuit->read("id, circuit_id", $id);
    	if (empty($userCircuit)) {
			$this->Session->setFlash('Invalide id pour l\'utilisateur dans le circuit');
			$redirect = $this->Session->read('user.User.lasturl');
    	} elseif ($this->UsersCircuit->del($id)) {
    		$circuit_id = $userCircuit['UsersCircuit']['circuit_id'];
			$redirect = "/circuits/index/$circuit_id/";
			$usersCircuit = $this->UsersCircuit->findAll("circuit_id = $circuit_id", 'id, position', 'position ASC', null, 1, -1);
			foreach ($usersCircuit as $userCircuit) {
				$iPosition++;
				if ($userCircuit['UsersCircuit']['position'] != $iPosition) {
					$userCircuit['UsersCircuit']['position'] = $iPosition;
					$this->UsersCircuit->save($userCircuit);
				}
			}
		} else {
		    $this->Session->setFlash('Suppression impossible');
			$redirect = $this->Session->read('user.User.lasturl');
		}

	    $this->redirect($redirect);
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

    function getCurrentCircuit($id){
        $condition = "UsersCircuit.id = $id";
        $objCourant = $this->UsersCircuit->findAll($condition);
        return $objCourant['0']['UsersCircuit']['circuit_id'];

    }

    function getLastPosition($circuit_id) {
        return count($this->UsersCircuit->findAll("circuit_id = $circuit_id"));
    }

}
?>
