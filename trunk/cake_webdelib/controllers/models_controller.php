<?php
class ModelsController extends AppController {

	var $name = 'Models';
	var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Localisation','Seance', 'Service', 'Commentaire','Model', 'Theme', 'Collectivite', 'Vote','SeancesUser', 'Listepresence');
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $components = array('Date','Utils','Email', 'Acl', 'Gedooo');

	// Gestion des droits
	var $aucunDroit = array('sendToGedoo', 'makeProjetXML', 'generateDeliberation', 'generateProjet', 'generatePVDetaille', 'generatePVSommaire', 'listeProjets', 'getModel');
	var $commeDroit = array('edit'=>'Models:index', 'add'=>'Models:index', 'delete'=>'Models:index', 'view'=>'Models:index', 'import'=>'Models:index', 'getFileData'=>'Models:index');

	function index() {
		$this->set('models', $this->Model->findAll());
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else{

			if ($this->Model->save($this->data)) {
				$this->redirect('/models/index');
			}
		}
	}

	function edit($id=null) {
		$data = $this->Model->findAll("Model.id = $id");
		$this->set('libelle', $data['0']['Model']['libelle']);

		if (empty($this->data)) {
			$this->data = $this->Model->read(null, $id);
		} else{
			$this->data['Model']['id']=$id;
			if ($this->Model->save($this->data)) {
				$this->redirect('/models/index');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la deliberation');
			$this->redirect('/models/index');
		}
		if ($this->Model->del($id)) {
			$this->Session->setFlash('Le model a &eacute;t&eacute; supprim&eacute;e.');
			$this->redirect('/models/index');
		}
	}

	function view($id = null) {
	      $this->set('USE_GEDOOO', USE_GEDOOO);
	      if (USE_GEDOOO) {
	          header('Content-type: '.$this->_getFileType($id));
              header('Content-Length: '.$this->_getSize($id));
              header('Content-Disposition: attachment; filename='.$this->_getFileName($id));
              echo $this->_getData($id);
              exit();
           }
	       else {
               $this->set('model', $this->Model->read(null, $id));
	       }
        }


	// Accesseurs Déliberation
	function _getCommentaireDelib($delib_id) {
		$data= $this->Vote->findAll("delib_id=$delib_id");
		if (isset($data['0']['Vote']['commentaire']))
			return $data['0']['Vote']['commentaire'];
	}

	function _getDelibEtat($deliberationEtat) {
		 switch ($deliberationEtat){
			case -1:
    			$etat = 'refusé';
    			break;
			case 0:
    			$etat = 'en cours de rédaction';
    			break;
			case 1:
    			$etat = ' dans un circuit';
   				 break;
   			case 2:
    			$etat = 'validé';
   				 break;
    		case 3:
    			$etat = 'Voté pour';
   				 break;
   			case 4:
    			$etat = 'Voté contre';
   				 break;
   			case 5:
    			$etat = 'envoyé';
   				 break;
		 }
		 return $etat;
	}

	function _getDateDuJour(){
		return $this->Date->frenchDate(time());
	}

	function listeProjets($seance_id, $type=0) {
		 // $type = 0 => Liste projets Sommaires
		 // $type = 1 => Liste projets Détaillés
		if ($type == 1) $texte = $this->Model->field('content', 'id=12');
		else $texte = $this->Model->field('content', 'id=13');

		$listeProjets = "";
		$projets = $this->Deliberation->findAll("seance_id=$seance_id AND etat>=2",null,'Deliberation.position ASC');
		foreach($projets as $projet) {
        	$listeProjets .= $this->_replaceBalises($texte,$projet['Deliberation']['id'] );
        }
		return $listeProjets;
	}

	function _listeUsersPresents($delib_id) {
		// Lecture du modele
		$texte = $this->Model->field('content', 'id=8');

		$listeUsers = "";
		$users = $this->Listepresence->findAll("delib_id = $delib_id AND present = 1", null, "User.position ASC");
		foreach($users as $user) {
			$searchReplace = array(
				"#NOUVELLE_PAGE#" => "<newpage>",
				"#NOM_PRESENT#" => $user['User']['nom'],
			 	"#PRENOM_PRESENT#" => $user['User']['prenom'],
			 	"#ADRESSE_PRESENT#" => $user['User']['adresse'],
			 	"#CP_PRESENT#" => $user['User']['CP'],
			 	"#TITRE_PRESENT#" => $user['User']['titre'],
			 	"#VILLE_PRESENT#" => $user['User']['ville']
			 );
        	$listeUsers .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
        }
       return $listeUsers;
        /* if (!USE_GEDOOO)
		    return $listeUsers;
		else {
			$dyn_path = "/files/generee/$delib_id/";
            $path = WEBROOT_PATH.$dyn_path;
 			$this->Gedooo->createFile ($path,'presents.html', $listeUsers);
		} */
	}

	function _listeUsersAbsents($delib_id) {
		// Lecture du modele
		$texte = $this->Model->field('content', 'id=9');

		$listeUsers = "";
		$users = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire = 0", null, 'User.position ASC');
		foreach($users as $user) {
			$searchReplace = array(
				"#NOUVELLE_PAGE#" => "<newpage>",
				"#NOM_ABSENT#" => $user['User']['nom'],
			 	"#PRENOM_ABSENT#" => $user['User']['prenom'],
			 	"#ADRESSE_ABSENT#" => $user['User']['adresse'],
			 	"#CP_ABSENT#" => $user['User']['CP'],
			 	"#TITRE_ABSENT#" => $user['User']['titre'],
			 	"#VILLE_ABSENT#" => $user['User']['ville']
			 );
        	$listeUsers .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
        }
		return $listeUsers;
	}

		function _listeUsersMandates($delib_id) {
		// Lecture du modele
		$texte = $this->Model->field('content', 'id=10');

		$listeUsers = "";
		$users = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire != 0", null, 'User.position ASC');
		foreach($users as $user) {
			$mandataire = $this->User->findById($user['Listepresence']['mandataire']);
			$searchReplace = array(
				"#NOUVELLE_PAGE#" => "<newpage>",
				"#NOM_MANDATE#" => $user['User']['nom'],
				"#PRENOM_MANDATE#" => $user['User']['prenom'],
				"#NOM_MANDATAIRE#" => $mandataire['User']['nom'],
			 	"#PRENOM_MANDATAIRE#" => $mandataire['User']['prenom'],
			 	"#ADRESSE_MANDATAIRE#" => $mandataire['User']['adresse'],
			 	"#CP_MANDATAIRE#" => $mandataire['User']['CP'],
			 	"#TITRE_MANDATAIRE#" => $mandataire['User']['titre'],
			 	"#VILLE_MANDATAIRE#" => $mandataire['User']['ville']
			 );
        	$listeUsers .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
        }
		return $listeUsers;
	}

	function _listeUsersVotant($delib_id) {
		// Lecture du modele
		$texte = $this->Model->field('content', 'id=11');

		$listeUsers = "";
		$votes = $this->Vote->findAll("delib_id = $delib_id");
		foreach($votes as $vote) {
			$votant = $this->User->findById($vote['Vote']['user_id']);
			if ($vote['Vote']['resultat']==2)
				$resultat = "contre";
			elseif ($vote['Vote']['resultat']==3)
				$resultat = "pour";
			elseif ($vote['Vote']['resultat']==4)
				$resultat = "abstention";
			elseif ($vote['Vote']['resultat']==5)
				$resultat = "Pas de participation";

			$searchReplace = array(
				"#NOUVELLE_PAGE#" => "<newpage>",
				"#NOM_VOTANT#" => $votant['User']['nom'],
			 	"#PRENOM_VOTANT#" => $votant['User']['prenom'],
			 	"#ADRESSE_VOTANT#" => $votant['User']['adresse'],
			 	"#CP_VOTANT#" => $votant['User']['CP'],
			 	"#VILLE_VOTANT#" => $votant['User']['ville'],
			 	"#TITRE_VOTANT#" => $votant['User']['titre'],
			 	"#RESULTAT_VOTANT#" => $resultat,
				"#COMMENTAIRE_VOTE#" => $vote['Vote']['commentaire']
			);
        	$listeUsers .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
        }
		return $listeUsers;
	}

	function _replaceBalisesSeance($texte, $seance_id) {
		// Lecture des informations en base
		$seance = $this->Seance->findById($seance_id);
		$collectivite = $this->Collectivite->findById(1);

		// Initialisation ses balises documentaires
		$listeProjetsSommaires = "";
		$listeProjetsDetailles = "";
		if (strpos($texte, '#LISTE_PROJETS_SOMMAIRES#'))
			$listeProjetsSommaires = $this->listeProjets($seance_id,0);
		if (strpos($texte, '#LISTE_PROJETS_DETAILLES#'))
			$listeProjetsDetailles = $this->listeProjets($seance_id,1);

		// Initialisation du tableau de remplacement
		$searchReplace = array(
			"#NOUVELLE_PAGE#" => "<newpage>",
			"#DATE_DU_JOUR#" => $this->_getDateDuJour(),
			"#SEANCE_ID#" => $seance_id,
			"#DEBAT_SEANCE#" => $seance['Seance']['debat_global'],
			"#DATE_SEANCE#" => $this->Date->frenchDate(strtotime($seance['Seance']['date'])),
	 		"#LIBELLE_TYPE_SEANCE#" => $seance['Typeseance']['libelle'],
			"#LOGO_COLLECTIVITE#" => '<img src="files/image/logo.jpg">',
			"#NOM_COLLECTIVITE#" => $collectivite['Collectivite']['nom'],
			"#ADRESSE_COLLECTIVITE#" => $collectivite['Collectivite']['adresse'],
			"#CP_COLLECTIVITE#" => $collectivite['Collectivite']['CP'],
			"#VILLE_COLLECTIVITE#" => $collectivite['Collectivite']['ville'],
			"#TELEPHONE_COLLECTIVITE#" => $collectivite['Collectivite']['telephone'],
			"#LIEU_SEANCE#" => "un lieu a definir",
			"#LISTE_PROJETS_SOMMAIRES#" => $listeProjetsSommaires,
			"#LISTE_PROJETS_DETAILLES#" => $listeProjetsDetailles
		);

		return  str_replace(array_keys($searchReplace),array_values($searchReplace), $texte);
	}

	function _replaceBalises($texte, $delib_id) {
		// Lecture des informations en base
		$delib = $this->Deliberation->findById($delib_id);
		$collectivite = $this->Collectivite->findById(1);

		// Traitement de la séance
		if (!empty($delib['Deliberation']['seance_id'])){
			$dateSeance = $this->Date->frenchDate(strtotime($delib['Seance']['date']));
			$libelleSeance = $this->Typeseance->field('libelle', 'id = '.$delib['Seance']['type_id']);
		} else {
			$dateSeance = "";
			$libelleSeance = "";
		}

		// Initialisation du tableau de remplacement
		$searchReplace = array(
			"#NOUVELLE_PAGE#" => "<newpage>",
			"#IDENTIFIANT_PROJET#" => $delib_id,
			"#DATE_DU_JOUR#" => $this->_getDateDuJour(),
			"#SEANCE_ID#" => $delib['Deliberation']['seance_id'],
			"#DEBAT_SEANCE#" => $delib['Seance']['debat_global'],
			"#DATE_SEANCE#" => $dateSeance,
			"#LIBELLE_TYPE_SEANCE#" => $libelleSeance,
			"#ETAT_DELIB#" => $this->_getDelibEtat($delib['Deliberation']['etat']),
			"#LIBELLE_THEME#" => $delib['Theme']['libelle'],
			"#LIBELLE_SERVICE#" => $delib['Service']['libelle'],
			"#TITRE_DELIB#" => $delib['Deliberation']['titre'],
			"#LIBELLE_DELIB#" => $delib['Deliberation']['objet'],
			"#TEXTE_DELIB#" => $delib['Deliberation']['deliberation'],
			"#TEXTE_SYNTHESE#" => $delib['Deliberation']['texte_synthese'],
			"#TEXTE_PROJET#" => $delib['Deliberation']['texte_projet'],
			"#POSITION_DELIB#" => $delib['Deliberation']['position'],
			"#DEBAT_DELIB#" => $delib['Deliberation']['debat'],
			"#COMMENTAIRE_DELIB#" => $this->_getCommentaireDelib($delib_id),
			"#NOM_RAPPORTEUR#" => $delib['Rapporteur']['nom'],
			"#PRENOM_RAPPORTEUR#" => $delib['Rapporteur']['prenom'],
			"#ADRESSE_RAPPORTEUR#" => $delib['Rapporteur']['adresse'],
			"#CP_RAPPORTEUR#" => $delib['Rapporteur']['CP'],
			"#VILLE_RAPPORTEUR#" => $delib['Rapporteur']['ville'],
			"#TITRE_RAPPORTEUR#" => $delib['Rapporteur']['titre'],
			"#NOM_REDACTEUR#" => $delib['Redacteur']['nom'],
			"#PRENOM_REDACTEUR#" => $delib['Redacteur']['prenom'],
			"#ADRESSE_REDACTEUR#" => $delib['Redacteur']['adresse'],
			"#CP_REDACTEUR#" => $delib['Redacteur']['CP'],
			"#VILLE_REDACTEUR#" => $delib['Redacteur']['ville'],
			"#TITRE_REDACTEUR#" => $delib['Redacteur']['titre'],
			"#LOGO_COLLECTIVITE#" => '<img src="files/image/logo.jpg">',
			"#NOM_COLLECTIVITE#" => $collectivite['Collectivite']['nom'],
			"#ADRESSE_COLLECTIVITE#" => $collectivite['Collectivite']['adresse'],
			"#CP_COLLECTIVITE#" => $collectivite['Collectivite']['CP'],
			"#VILLE_COLLECTIVITE#" => $collectivite['Collectivite']['ville'],
			"#TELEPHONE_COLLECTIVITE#" => $collectivite['Collectivite']['telephone'],
			"#LISTE_PRESENTS#" => $this->_listeUsersPresents($delib_id),
			"#LISTE_ABSENTS#" => $this->_listeUsersAbsents($delib_id),
			"#LISTE_MANDATAIRES#" => $this->_listeUsersMandates($delib_id),
			"#LISTE_VOTANTS#" =>$this->_listeUsersVotant($delib_id)
		);

		return  str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
	}

	function generateDeliberation($delib_id) {
		$data = $this->Model->findAll('Model.id=4');
		$texte = $data['0']['Model']['content'];
		return $this->_replaceBalises($texte, $delib_id );
	}

	function generateProjet($delib_id) {
		$data = $this->Model->findAll('Model.id=1');
		$texte = $data['0']['Model']['content'];
		return $this->_replaceBalises($texte, $delib_id);
	}

	function generatePVSommaire($seance_id) {
		$data = $this->Model->findAll('Model.id=5');
		$texte = $data['0']['Model']['content'];
		return $this->_replaceBalisesSeance($texte, $seance_id);
	}

	function generatePVDetaille($seance_id) {
		$data = $this->Model->findAll('Model.id=6');
		$texte = $data['0']['Model']['content'];
		return $this->_replaceBalisesSeance($texte, $seance_id);
	}


        function import($model_id) {
	    $this->set('USE_GEDOOO', USE_GEDOOO);
            $this->set('model_id', $model_id);
            if (! empty($this->data)){
                  if (isset($this->data['Model']['template'])){
                    if ($this->data['Model']['template']['size']!=0){
                        $this->data['Model']['id']        = $model_id;
                        $this->data['Model']['name']      = $this->data['Model']['template']['name'];
                        $this->data['Model']['size']      = $this->data['Model']['template']['size'];
                        $this->data['Model']['extension'] = $this->data['Model']['template']['type'];
                        $this->data['Model']['content']   = $this->getFileData($this->data['Model']['template']['tmp_name'], $this->data['Model']['template']['size']);
                        if ($this->Model->save($this->data))
                          $this->redirect('/models/index');
                     }
                }
            }
	    else {
                $this->data = $this->Model->read(null, $model_id);
	    }
        }

        function getFileData($fileName, $fileSize) {
                return fread(fopen($fileName, "r"), $fileSize);
        }

        function _getFileType($id=null) {
                $condition = "Model.id = $id";
                $objCourant = $this->Model->findAll($condition);
                return $objCourant['0']['Model']['extension'];
        }

        function _getFileName($id=null) {
                $condition = "Model.id = $id";
        $objCourant = $this->Model->findAll($condition);
                return $objCourant['0']['Model']["name"];
        }

        function _getSize($id=null) {
            $condition = "Model.id = $id";
            $objCourant = $this->Model->findAll($condition);
            return $objCourant['0']['Model']["size"];
        }

        function _getData($id=null) {
            $condition = "Model.id = $id";
            $objCourant = $this->Model->findAll($condition);
            return $objCourant['0']['Model']['content'];
        }

       function getModel($id=null) {
            $condition = "Model.id = $id";
            $objCourant = $this->Model->findAll($condition);
            return $objCourant['0']['Model']['content'];
        }
}
?>
