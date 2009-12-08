<?php
class CircuitsController extends AppController {

	var $name = 'Circuits';
	var $helpers = array('Html', 'Form' , 'Javascript');
	var $uses = array('Circuit', 'User', 'Service', 'UsersService', 'UsersCircuit', 'Deliberation');
	var $components = array('Parafwebservice');

	// Gestion des droits
	var $aucunDroit = array('getCurrentCircuit', 'getCurrentPosition', 'getLastPosition', 'intervertirPosition', 'isEditable','test');
	var $commeDroit = array('addUser'=>'Circuits:index', 'supprimerUser'=>'Circuits:index', 'add'=>'Circuits:index', 'delete'=>'Circuits:index', 'view'=>'Circuits:index', 'edit'=>'Circuits:index');

        function test () {
	        if (!defined('CRON_DISPATCHER')) { $this->redirect('/'); exit(); } 
                //echo $this->Parafwebservice->test();

                /*$echo = $this->Parafwebservice->echoWebservice();
                echo $echo;*/

/*                $types = $this->Parafwebservice->getListeTypesWebservice();
                debug($types);

                $soustypes = $this->Parafwebservice->getListeSousTypesWebservice(TYPETECH);
                debug($soustypes);

                $circuit = $this->Parafwebservice->getCircuit(TYPETECH, 'C1');
                debug($circuit);
*/
                $histo = $this->Parafwebservice->getHistoDossierWebservice('webdelib_1');
                debug($histo);
		debug($this->Deliberation->findAll());

                //var_dump($histo);
                //$rechdos = $this->Parafwebservice->rechercherDossierWebservice('HELIOS', 'C1', '', '', '');
                //echo $rechdos;
                //var_dump($rechdos);

                //$archdos = $this->Parafwebservice->archiverDossierWebservice('test_20091126_02', 'ARCHIVER');
                //echo $archdos;
                //var_dump($archdos);

                /*$effdos = $this->Parafwebservice->effacerDossierRejeteWebservice('R-006-00-C2-20091005035902');
                echo $effdos;
                var_dump($effdos);*/

                //$remorddos = $this->Parafwebservice->exercerDroitRemordWebservice('R-006-00-C2-20091005035902');
                //echo $remorddos;
                //var_dump($remorddos);
/*
                $typetech = "ACTES";
                $soustype = "C1";
                $emailemetteur = "htexier@cogitis.fr";
                $dossierid = "test_20091202_04";
                $visibilite = "PUBLIC";
                $nomfichierpdf = "testdocprincip.pdf";
                $pdf = file_get_contents(DOSPDF."/".$nomfichierpdf);
                $creerdos = $this->Parafwebservice->creerDossierWebservice($typetech, $soustype, $emailemetteur, $dossierid, '', '', $visibilite, '', $pdf);
                echo $creerdos;
*/
                /*$getdos = $this->Parafwebservice->getDossierWebservice('R-006-00-C2-20091005035902');
                echo $getdos;*/

                //$envoitdt = $this->Parafwebservice->envoyerDossierTdTWebservice('R-006-00-C2-20091005035902');
                //echo $envoitdt;
                //var_dump($envoitdt);

                //$statutdt = $this->Parafwebservice->getStatutTdTWebservice('R-006-00-C2-20091005035902');
                //echo $statutdt;
                //var_dump($statutdt);

                //$forcetape = $this->Parafwebservice->forcerEtapeWebservice('test_20091202_04', 'OK', 'Etape TdT sauté', '');
                //echo $forcetape;
                //var_dump($forcetape);

                /*$getdossier = $this->Parafwebservice->getDossierWebservice('test_20091202_01');//test_20091126_01
                echo $getdossier;
                var_dump($getdossier);*/

            exit;
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
                 if (($data[$i]['UsersCircuit']['user_id']==$user_id)&&($service_id!=-1)) {
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
