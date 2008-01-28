<?php
class ModelsController extends AppController {

	var $name = 'Models';
	var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Localisation','Seance', 'Service', 'Commentaire','Model', 'Theme', 'Collectivite', 'Vote','SeancesUser', 'Listepresence');
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $components = array('Date','Utils','Email', 'Acl');

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
		$this->set('model', $this->Model->read(null, $id));
	}

	// Accesseurs Utilisateurs
	function getUserNom($user_id) {
		return $this->requestAction("/users/getNom/$user_id");
	}

	function getUserPrenom($user_id) {
		return $this->requestAction("/users/getPrenom/$user_id");
	}

	function getUserAdresse($user_id) {
		return $this->requestAction("/users/getAdresse/$user_id");
	}

	function getUserCP($user_id) {
		return $this->requestAction("/users/getCP/$user_id");
	}

	function getUserVille($user_id) {
		return $this->requestAction("/users/getVille/$user_id");
	}
	// Accesseurs Collectivité
	function getCollectiviteNom() {
		$data= $this->Collectivite->findAll("Collectivite.id=1");
		return $data['0']['Collectivite']['nom'];
	}

	function getCollectiviteAdresse() {
		$data= $this->Collectivite->findAll("Collectivite.id=1");
		return $data['0']['Collectivite']['adresse'];
	}

	function getCollectiviteCP() {
		$data= $this->Collectivite->findAll("Collectivite.id=1");
		return $data['0']['Collectivite']['CP'];
	}

	function getCollectiviteVille() {
		$data= $this->Collectivite->findAll("Collectivite.id=1");
		return $data['0']['Collectivite']['ville'];
	}

	function getCollectiviteTelephone() {
		$data= $this->Collectivite->findAll("Collectivite.id=1");
		return $data['0']['Collectivite']['telephone'];
	}

	function getLibelleTheme($theme_id){
		$data= $this->Theme->findAll("Theme.id=$theme_id");
		return $data['0']['Theme']['libelle'];
	}

	function getLibelleService($service_id){
		$data= $this->Service->findAll("Service.id=$service_id");
		return $data['0']['Service']['libelle'];
	}

	// Accesseurs Séance
	function getLibelleTypeSeance($type_id){
		if(!empty($type_id)){
			$data= $this->Typeseance->findAll("Typeseance.id=$type_id");
			return $data['0']['Typeseance']['libelle'];
		}else
			return "";
	}

	function getDateSeance($seance_id) {
		if (!empty($seance_id)){
			$data= $this->Seance->findAll("Seance.id=$seance_id");
			return $data['0']['Seance']['date'];
		}else
			return "";
	}

	function getTypeIdFromSeanceId ($seance_id) {
		if(!empty($seance_id)){
			$data= $this->Seance->findAll("Seance.id=$seance_id");
			return $data['0']['Seance']['type_id'];
		}else
			return "";
	}

	// Accesseurs Déliberation
	function getCommentaireDelib($delib_id) {
		$data= $this->Vote->findAll("delib_id=$delib_id");
		if (isset($data['0']['Vote']['commentaire']))
			return $data['0']['Vote']['commentaire'];
	}

	function getPositionDelib($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['position'];
	}
	
	function getDebatDelib($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		if (isset($data['0']['Deliberation']['debat']))
			return $data['0']['Deliberation']['debat'];
	}

	function getTexteProjet($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['texte_projet'];
	}

	function getTexteSynthese($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['texte_synthese'];
	}

	function getTexteDeliberation($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['deliberation'];
	}

	function getDelibLibelle($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['objet'];
	}

	function getDelibEtat($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		 switch ($data['0']['Deliberation']['etat']){
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

	function getDelibTitre($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['titre'];
	}

	function getSeanceId($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['seance_id'];
	}

	function getRapporteurId($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['rapporteur_id'];
	}

	function getThemeId($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['theme_id'];
	}

	function getServiceId($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['service_id'];
	}

	function getRedacteurId($delib_id) {
		$data= $this->Deliberation->findAll("Deliberation.id=$delib_id");
		return $data['0']['Deliberation']['redacteur_id'];
	}

	function getDateDuJour(){
		return $this->Date->frenchDate(time());
	}

	function listeProjets($seance_id, $type=0) {
		/**
		 * $type = 0 => Liste projets Sommaires
		 * $type = 1 => Liste projets Détaillés
		 **/
		if ($type == 1)
			$condition = 'Model.id=12';
		else
			$condition = 'Model.id=13';

		$listeProjets = "";
		$projets = $this->Deliberation->findAll("seance_id=$seance_id AND etat>=2",null,'position ASC');
		foreach($projets as $projet) {
			$data = $this->Model->findAll($condition);
			$texte = $data['0']['Model']['texte'];
        	$listeProjets .= $this->replaceBalises ($texte,$projet['Deliberation']['id'] );
        }
		return $listeProjets;
	}

	function listeUsersPresents($delib_id) {
		$condition = 'Model.id=8';
		$listeUsers = "";
		$users = $this->Listepresence->findAll("delib_id = $delib_id AND present = 1");
		foreach($users as $user) {
			$data = $this->Model->findAll($condition);
			$texte = $data['0']['Model']['texte'];
			$present_id = $user['Listepresence']['user_id'];
			$search = array("#NOUVELLE_PAGE#",
						"#NOM_PRESENT#",
			 			"#PRENOM_PRESENT#",
			 			"#ADRESSE_PRESENT#",
			 			"#CP_PRESENT#",
			 			"#VILLE_PRESENT#");
			$replace = array ("<newpage>", $this->getUserNom($present_id),
			 			$this->getUserPrenom($present_id),
		  				$this->getUserAdresse($present_id),
						$this->getUserCP($present_id),
			 			$this->getUserVille($present_id));
        	$listeUsers .= str_replace($search,$replace, $texte);
        }
		return $listeUsers;
	}

	function listeUsersAbsents($delib_id) {
		$condition = 'Model.id=9';
		$listeUsers = "";
		$users = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire = 0");
		foreach($users as $user) {
			$data = $this->Model->findAll($condition);
			$texte = $data['0']['Model']['texte'];
			$absent_id = $user['Listepresence']['user_id'];
			$search = array("#NOUVELLE_PAGE#",
							"#NOM_ABSENT#",
			 			"#PRENOM_ABSENT#",
			 			"#ADRESSE_ABSENT#",
			 			"#CP_ABSENT#",
			 			"#VILLE_ABSENT#");
			$replace = array ("<newpage>",
							  $this->getUserNom($absent_id),
			 				  $this->getUserPrenom($absent_id),
		  				      $this->getUserAdresse($absent_id),
						      $this->getUserCP($absent_id),
			 			      $this->getUserVille($absent_id)
			 			     );
        	$listeUsers .= str_replace($search,$replace, $texte);
        }
		return $listeUsers;
	}

		function listeUsersMandates($delib_id) {
		$condition = 'Model.id=10';
		$listeUsers = "";
		$users = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire != 0");
		foreach($users as $user) {
			$data = $this->Model->findAll($condition);
			$texte = $data['0']['Model']['texte'];
			$mandate_id = $user['Listepresence']['user_id'];
			$mandataire_id = $user['Listepresence']['mandataire'];
			$search = array("#NOUVELLE_PAGE#",
						"#NOM_MANDATE#",
						"#PRENOM_MANDATE#",
						"#NOM_MANDATAIRE#",
			 			"#PRENOM_MANDATAIRE#",
			 			"#ADRESSE_MANDATAIRE#",
			 			"#CP_MANDATAIRE#",
			 			"#VILLE_MANDATAIRE#");
			$replace = array ("<newpage>", $this->getUserNom($mandate_id),
			 			$this->getUserPrenom($mandate_id),
						$this->getUserNom($mandataire_id),
			 			$this->getUserPrenom($mandataire_id),
		  				$this->getUserAdresse($mandataire_id),
						$this->getUserCP($mandataire_id),
			 			$this->getUserVille($mandataire_id));
        	$listeUsers .= str_replace($search,$replace, $texte);
        }
		return $listeUsers;
	}

	function listeUsersVotant($delib_id) {
		$condition = 'Model.id=11';
		$listeUsers = "";
		$users = $this->Vote->findAll("delib_id = $delib_id");
		foreach($users as $user) {
			$data = $this->Model->findAll($condition);
			$texte = $data['0']['Model']['texte'];
			$votant_id = $user['Vote']['user_id'];
			$resultat = $user['Vote']['resultat'];
			$commentaire = $user['Vote']['commentaire'];
			if ($resultat==2)
				$resultat = "contre";
			elseif ($resultat==3)
				$resultat = "pour";
			elseif ($resultat==4)
				$resultat = "abstention";
			elseif ($resultat==5)
				$resultat = "Pas de participation";

			$search = array("#NOUVELLE_PAGE#",
						"#NOM_VOTANT#",
			 			"#PRENOM_VOTANT#",
			 			"#ADRESSE_VOTANT#",
			 			"#CP_VOTANT#",
			 			"#VILLE_VOTANT#",
			 			"#RESULTAT_VOTANT#",
						"#COMMENTAIRE_VOTE#");
			$replace = array ("<newpage>", $this->getUserNom($votant_id),
			 			$this->getUserPrenom($votant_id),
		  				$this->getUserAdresse($votant_id),
						$this->getUserCP($votant_id),
			 			$this->getUserVille($votant_id),
			 			$resultat,
			 			$commentaire);
        	$listeUsers .= str_replace($search,$replace, $texte);
        }
		return $listeUsers;
	}

	function replaceBalisesSeance ($texte, $seance_id) {
	$search = array("#NOUVELLE_PAGE#",
					"#DATE_DU_JOUR#",
				 	"#SEANCE_ID#",
				 	"#DATE_SEANCE#",
			 		"#LIBELLE_TYPE_SEANCE#",
			 		"#LOGO_COLLECTIVITE#",
			 		"#NOM_COLLECTIVITE#",
			 		"#ADRESSE_COLLECTIVITE#",
			 		"#CP_COLLECTIVITE#",
			 		"#VILLE_COLLECTIVITE#",
			 		"#TELEPHONE_COLLECTIVITE#",
			 		"#LIEU_SEANCE#",
					"#LISTE_PROJETS_SOMMAIRES#",
					"#LISTE_PROJETS_DETAILLES#");

		$replace=array( "<newpage>",$this->getDateDuJour(),
						$seance_id,
		 				$this->Date->frenchDate(strtotime($this->getDateSeance($seance_id))),
						$this->getLibelleTypeSeance($this->getTypeIdFromSeanceId($seance_id)),
			 			'<img src="files/image/logo.jpg">',
						$this->getCollectiviteNom(1),
			 			$this->getCollectiviteAdresse(1),
		  				$this->getCollectiviteCP(1),
			 			$this->getCollectiviteVille(1),
			 			$this->getCollectiviteTelephone(1),
			 			"un lieu a definir",
						$this->listeProjets($seance_id,0),
						$this->listeProjets($seance_id,1)
			 			);

		return  str_replace($search,$replace, $texte);
	}

	function replaceBalises ($texte, $delib_id) {
		$seance_id = $this->getSeanceId($delib_id);
		$rapporteur_id = $this->getRapporteurId($delib_id);
		$redacteur_id = $this->getRedacteurId($delib_id);
		$theme_id =  $this->getThemeId($delib_id);
		$service_id = $this->getServiceId($delib_id);
		
		if(!empty($seance_id)){
			$dateSeance = $this->Date->frenchDate(strtotime($this->getDateSeance($seance_id)));
			$libelleSeance = $this->getLibelleTypeSeance($this->getTypeIdFromSeanceId($seance_id));
		}else{
			$dateSeance = "";
			$libelleSeance = "";
		}
			
			
		$search = array("#NOUVELLE_PAGE#",
						"#IDENTIFIANT_PROJET#",
						"#DATE_DU_JOUR#",
				 		"#SEANCE_ID#",
				 		"#DATE_SEANCE#",
						"#LIBELLE_TYPE_SEANCE#",
			 			"#ETAT_DELIB#",
						"#LIBELLE_THEME#",
						"#LIBELLE_SERVICE#",
			 			"#TITRE_DELIB#",
			 			"#LIBELLE_DELIB#",
			 			"#TEXTE_DELIB#",
			 			"#TEXTE_SYNTHESE#",
			 			"#TEXTE_PROJET#",
			 			"#POSITION_DELIB#",
			 			"#DEBAT_DELIB#",
			 			"#COMMENTAIRE_DELIB#",
			 			"#NOM_RAPPORTEUR#",
			 			"#PRENOM_RAPPORTEUR#",
			 			"#ADRESSE_RAPPORTEUR#",
			 			"#CP_RAPPORTEUR#",
			 			"#VILLE_RAPPORTEUR#",
			 			"#NOM_REDACTEUR#",
			 			"#PRENOM_REDACTEUR#",
			 			"#ADRESSE_REDACTEUR#",
			 			"#CP_REDACTEUR#",
			 			"#VILLE_REDACTEUR#",
			 			"#LOGO_COLLECTIVITE#",
			 			"#NOM_COLLECTIVITE#",
			 			"#ADRESSE_COLLECTIVITE#",
			 			"#CP_COLLECTIVITE#",
			 			"#VILLE_COLLECTIVITE#",
			 			"#TELEPHONE_COLLECTIVITE#",
			 			"#LISTE_PRESENTS#",
			 			"#LISTE_ABSENTS#",
			 			"#LISTE_MANDATAIRES#",
			 			"#LISTE_VOTANT#");

		$replace=array( "<newpage>", $delib_id,
						$this->getDateDuJour(),
						$seance_id,
		 				$dateSeance,
						$libelleSeance,
			 			$this->getDelibEtat($delib_id),
			 			$this->getLibelleTheme($theme_id),
			 			$this->getLibelleService($service_id),
			 			$this->getDelibTitre($delib_id),
			 			$this->getDelibLibelle($delib_id),
			  			$this->getTexteDeliberation($delib_id),
			 			$this->getTexteSynthese($delib_id),
						$this->getTexteProjet($delib_id),
						$this->getPositionDelib($delib_id),
						$this->getDebatDelib($delib_id),
						$this->getCommentaireDelib($delib_id),
						$this->getUserNom($rapporteur_id),
			 			$this->getUserPrenom($rapporteur_id),
		  				$this->getUserAdresse($rapporteur_id),
						$this->getUserCP($rapporteur_id),
			 			$this->getUserVille($rapporteur_id),
			 			$this->getUserNom($redacteur_id),
			 			$this->getUserPrenom($redacteur_id),
		  				$this->getUserAdresse($redacteur_id),
						$this->getUserCP($redacteur_id),
			 			$this->getUserVille($redacteur_id),
			 			'<img src="files/image/logo.jpg">',
						$this->getCollectiviteNom(1),
			 			$this->getCollectiviteAdresse(1),
		  				$this->getCollectiviteCP(1),
			 			$this->getCollectiviteVille(1),
			 			$this->getCollectiviteTelephone(1),
 						$this->listeUsersPresents($delib_id),
 						$this->listeUsersAbsents($delib_id),
						$this->listeUsersMandates($delib_id),
						$this->listeUsersVotant($delib_id));
		return  str_replace($search,$replace, $texte);
	}

	function generateDeliberation($delib_id) {
		$data = $this->Model->findAll('Model.id=4');
		$texte = $data['0']['Model']['texte'];
		return $this->replaceBalises($texte, $delib_id );
	}

	function generateProjet($delib_id) {
		$data = $this->Model->findAll('Model.id=1');
		$texte = $data['0']['Model']['texte'];
		return $this->replaceBalises($texte, $delib_id);
	}

	function generatePVSommaire($seance_id) {
		$data = $this->Model->findAll('Model.id=5');
		$texte = $data['0']['Model']['texte'];
		return $this->replaceBalisesSeance($texte, $seance_id);
	}

	function generatePVDetaille($seance_id) {
		$data = $this->Model->findAll('Model.id=6');
		$texte = $data['0']['Model']['texte'];
		return $this->replaceBalisesSeance($texte, $seance_id);
	}
}
?>