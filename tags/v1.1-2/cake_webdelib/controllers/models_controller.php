<?php
	class ModelsController extends AppController {

		var $name = 'Models';
		var $uses = array('Deliberation', 'UsersCircuit', 'Traitement', 'User', 'Circuit', 'Annex', 'Typeseance', 'Localisation', 'Seance', 'Service', 'Commentaire', 'Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Acteur');
		var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
		var $components = array('Date','Utils','Email', 'Acl', 'Gedooo');

		// Gestion des droits
		var $aucunDroit = array('sendToGedoo', 'makeProjetXML', 'generateDeliberation', 'generateProjet', 'generatePVDetaille', 'generatePVSommaire', 'listeProjets', 'getModel', 'listeActeursPresents', 'listeActeursAbsents', 'listeActeursMandates', 'listeActeursVotant', 'listeActeursVotantContre', 'listeActeursAbstenus');
		var $commeDroit = array('edit'=>'Models:index', 'add'=>'Models:index', 'delete'=>'Models:index', 'view'=>'Models:index', 'import'=>'Models:index', 'getFileData'=>'Models:index');

		function index() {
		    $this->set('USE_GEDOOO', USE_GEDOOO);
		    $this->set('models', $this->Model->findAll(null, null, 'type ASC '));
		}

		function add() {
			if (empty($this->data)) {
				$this->render();
			} else{
				$this->data['Model']['type']='Document';
				if ($this->Model->save($this->data)) {
					$this->redirect('/models/index');
				}
			}
		}

		function edit($id=null) {
			$data = $this->Model->findAll("Model.id = $id");
			$this->set('libelle', $data['0']['Model']['modele']);

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
			$data = $this->Model->read(null, $id);
			if ($data['Model']['type'] == 'Document') {
			    if ($this->Model->del($id)) {
				$this->Session->setFlash('Le model a &eacute;t&eacute; supprim&eacute;e.');
				$this->redirect('/models/index');
			    }
			}
			else{
			    $this->Session->setFlash('Impossible de supprimer ce type de modele');
			    $this->redirect('/models/index');
			}

		}

		function view($id = null) {
		      $this->set('USE_GEDOOO', USE_GEDOOO);
		      $data = $this->Model->read(null, $id);
		      if (USE_GEDOOO && ($data['Model']['type'] == 'Document') ) {
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
                         // $type = 2 => Ordre du Jour

                        if     ($type == 1) $texte = $this->Model->field('content', 'id=12');
                        elseif ($type == 0) $texte = $this->Model->field('content', 'id=13');
                        elseif ($type == 2) $texte = $this->Model->field('content', 'id=9999');

			$listeProjets = "";
			$projets = $this->Deliberation->findAll("seance_id=$seance_id AND etat>=0",null,'Deliberation.position ASC');
			foreach($projets as $projet) {
			$listeProjets .= $this->_replaceBalises($texte,$projet['Deliberation']['id'] );
		}
			return $listeProjets;
		}

		function _listeActeursPresents($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=8');
		    $listeActeurs = "";
		    $acteurs = $this->Listepresence->findAll("delib_id = $delib_id AND present = 1", null, "Acteur.position ASC");
		    foreach($acteurs as $acteur) {
			$searchReplace = array(
			    "#NOUVELLE_PAGE#" => "<newpage>",
			    "#NOM_PRESENT#" => $acteur['Acteur']['nom'],
			    "#PRENOM_PRESENT#" => $acteur['Acteur']['prenom'],
			    "#SALUTATION_PRESENT#" => $acteur['Acteur']['salutation'],
			    "#TITRE_PRESENT#" => $acteur['Acteur']['titre'],
			    "#ADRESSE1_PRESENT#" => $acteur['Acteur']['adresse1'],
			    "#ADRESSE2_PRESENT#" => $acteur['Acteur']['adresse2'],
			    "#CP_PRESENT#" => $acteur['Acteur']['cp'],
			    "#VILLE_PRESENT#" => $acteur['Acteur']['ville']
			 );
			$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		    }
		    return $listeActeurs;
		}

		function listeActeursPresents($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=8');
		    $listeActeurs = "";
		    $acteurs = $this->Listepresence->findAll("delib_id = $delib_id AND present = 1", null, "Acteur.position ASC");
		    foreach($acteurs as $acteur) {
			$searchReplace = array(
			    "#NOUVELLE_PAGE#" => "<newpage>",
			    "#NOM_PRESENT#" => $acteur['Acteur']['nom'],
			    "#PRENOM_PRESENT#" => $acteur['Acteur']['prenom'],
			    "#SALUTATION_PRESENT#" => $acteur['Acteur']['salutation'],
			    "#TITRE_PRESENT#" => $acteur['Acteur']['titre'],
			    "#ADRESSE1_PRESENT#" => $acteur['Acteur']['adresse1'],
			    "#ADRESSE2_PRESENT#" => $acteur['Acteur']['adresse2'],
			    "#CP_PRESENT#" => $acteur['Acteur']['cp'],
			    "#VILLE_PRESENT#" => $acteur['Acteur']['ville']
			 );
			$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		    }
		    return $listeActeurs;
		}

		function _listeActeursAbsents($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=9');

		    $listeActeurs = "";
		    $acteurs = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire = 0", null, 'Acteur.position ASC');
		    foreach($acteurs as $acteur) {
			$searchReplace = array(
			    "#NOUVELLE_PAGE#" => "<newpage>",
			    "#NOM_ABSENT#" => $acteur['Acteur']['nom'],
			    "#PRENOM_ABSENT#" => $acteur['Acteur']['prenom'],
			    "#SALUTATION_ABSENT#" => $acteur['Acteur']['salutation'],
			    "#TITRE_ABSENT#" => $acteur['Acteur']['titre'],
			    "#ADRESSE1_ABSENT#" => $acteur['Acteur']['adresse1'],
			    "#ADRESSE2_ABSENT#" => $acteur['Acteur']['adresse2'],
			    "#CP_ABSENT#" => $acteur['Acteur']['cp'],
			    "#VILLE_ABSENT#" => $acteur['Acteur']['ville']
			     );
			 $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		     }
		     return $listeActeurs;
		}

		function listeActeursAbsents($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=9');

		    $listeActeurs = "";
		    $acteurs = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire = 0", null, 'Acteur.position ASC');
		    foreach($acteurs as $acteur) {
			$searchReplace = array(
			    "#NOUVELLE_PAGE#" => "<newpage>",
			    "#NOM_ABSENT#" => $acteur['Acteur']['nom'],
			    "#PRENOM_ABSENT#" => $acteur['Acteur']['prenom'],
			    "#SALUTATION_ABSENT#" => $acteur['Acteur']['salutation'],
			    "#TITRE_ABSENT#" => $acteur['Acteur']['titre'],
			    "#ADRESSE1_ABSENT#" => $acteur['Acteur']['adresse1'],
			    "#ADRESSE2_ABSENT#" => $acteur['Acteur']['adresse2'],
			    "#CP_ABSENT#" => $acteur['Acteur']['cp'],
			    "#VILLE_ABSENT#" => $acteur['Acteur']['ville']
			     );
			 $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		     }
		     return $listeActeurs;
		}

		function _listeActeursMandates($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=10');

		    $listeActeurs = "";
		    $acteurs = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire != 0", null, 'Acteur.position ASC');
		    foreach($acteurs as $acteur) {
			$mandataire = $this->Acteur->findById($acteur['Listepresence']['mandataire']);
			$searchReplace = array(
				"#NOUVELLE_PAGE#" => "<newpage>",
				"#NOM_MANDATE#" => $acteur['Acteur']['nom'],
				"#PRENOM_MANDATE#" => $acteur['Acteur']['prenom'],
				"#SALUTATION_MANDATE#" => $acteur['Acteur']['salutation'],
				"#TITRE_MANDATE#" => $acteur['Acteur']['titre'],
				"#NOM_MANDATAIRE#" => $mandataire['Acteur']['nom'],
				"#PRENOM_MANDATAIRE#" => $mandataire['Acteur']['prenom'],
				"#SALUTATION_MANDATAIRE#" => $mandataire['Acteur']['salutation'],
				"#TITRE_MANDATAIRE#" => $mandataire['Acteur']['titre'],
				"#ADRESSE1_MANDATAIRE#" => $mandataire['Acteur']['adresse1'],
				"#ADRESSE2_MANDATAIRE#" => $mandataire['Acteur']['adresse2'],
				"#CP_MANDATAIRE#" => $mandataire['Acteur']['cp'],
				"#VILLE_MANDATAIRE#" => $mandataire['Acteur']['ville']
				 );
			$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		    }
		    return $listeActeurs;
		}

		function listeActeursMandates($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=10');

		    $listeActeurs = "";
		    $acteurs = $this->Listepresence->findAll("delib_id = $delib_id AND present = 0 and mandataire != 0", null, 'Acteur.position ASC');
		    foreach($acteurs as $acteur) {
			$mandataire = $this->Acteur->findById($acteur['Listepresence']['mandataire']);
			$searchReplace = array(
				"#NOUVELLE_PAGE#" => "<newpage>",
				"#NOM_MANDATE#" => $acteur['Acteur']['nom'],
				"#PRENOM_MANDATE#" => $acteur['Acteur']['prenom'],
				"#SALUTATION_MANDATE#" => $acteur['Acteur']['salutation'],
				"#TITRE_MANDATE#" => $acteur['Acteur']['titre'],
				"#NOM_MANDATAIRE#" => $mandataire['Acteur']['nom'],
				"#PRENOM_MANDATAIRE#" => $mandataire['Acteur']['prenom'],
				"#SALUTATION_MANDATAIRE#" => $mandataire['Acteur']['salutation'],
				"#TITRE_MANDATAIRE#" => $mandataire['Acteur']['titre'],
				"#ADRESSE1_MANDATAIRE#" => $mandataire['Acteur']['adresse1'],
				"#ADRESSE2_MANDATAIRE#" => $mandataire['Acteur']['adresse2'],
				"#CP_MANDATAIRE#" => $mandataire['Acteur']['cp'],
				"#VILLE_MANDATAIRE#" => $mandataire['Acteur']['ville']
				 );
			$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		    }
		    return $listeActeurs;
		}

		function _listeActeursVotant($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=11');

		    $listeActeurs = "";
		    $votes = $this->Vote->findAll("delib_id = $delib_id");
		    foreach($votes as $vote) {
			$votant = $this->Acteur->findById($vote['Vote']['acteur_id']);
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
			    "#NOM_VOTANT#" => $votant['Acteur']['nom'],
			    "#PRENOM_VOTANT#" => $votant['Acteur']['prenom'],
			    "#SALUTATION_VOTANT#" => $votant['Acteur']['salutation'],
			    "#TITRE_VOTANT#" => $votant['Acteur']['titre'],
			    "#ADRESSE1_VOTANT#" => $votant['Acteur']['adresse1'],
			    "#ADRESSE2_VOTANT#" => $votant['Acteur']['adresse2'],
			    "#CP_VOTANT#" => $votant['Acteur']['cp'],
			    "#VILLE_VOTANT#" => $votant['Acteur']['ville'],
			    "#RESULTAT_VOTANT#" => $resultat
			);
			$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		    }
		    return $listeActeurs;
		}

		function listeActeursVotant($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=11');

		    $listeActeurs = "";
		    $votes = $this->Vote->findAll("delib_id = $delib_id");
		    foreach($votes as $vote) {
			$votant = $this->Acteur->findById($vote['Vote']['acteur_id']);
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
			    "#NOM_VOTANT#" => $votant['Acteur']['nom'],
			    "#PRENOM_VOTANT#" => $votant['Acteur']['prenom'],
			    "#SALUTATION_VOTANT#" => $votant['Acteur']['salutation'],
			    "#TITRE_VOTANT#" => $votant['Acteur']['titre'],
			    "#ADRESSE1_VOTANT#" => $votant['Acteur']['adresse1'],
			    "#ADRESSE2_VOTANT#" => $votant['Acteur']['adresse2'],
			    "#CP_VOTANT#" => $votant['Acteur']['cp'],
			    "#VILLE_VOTANT#" => $votant['Acteur']['ville'],
			    "#RESULTAT_VOTANT#" => $resultat
			);
			$listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
		    }
		    return $listeActeurs;
		}

	       function listeActeursVotantContre($delib_id) {
		    // Lecture du modele
		    $texte = $this->Model->field('content', 'id=14');

		    $listeActeurs = "";
		    $votes = $this->Vote->findAll("Vote.delib_id = $delib_id AND Vote.resultat=2");
		    foreach($votes as $vote) {
			$votant = $this->Acteur->findById($vote['Vote']['acteur_id']);
			$searchReplace = array(
			    "#NOUVELLE_PAGE#" => "<newpage>",
			    "#NOM_VOTANT#" => $votant['Acteur']['nom'],
			    "#PRENOM_VOTANT#" => $votant['Acteur']['prenom'],
			    "#SALUTATION_VOTANT#" => $votant['Acteur']['salutation'],
			    "#TITRE_VOTANT#" => $votant['Acteur']['titre'],
			    "#ADRESSE1_VOTANT#" => $votant['Acteur']['adresse1'],
			    "#ADRESSE2_VOTANT#" => $votant['Acteur']['adresse2'],
			    "#CP_VOTANT#" => $votant['Acteur']['cp'],
			    "#VILLE_VOTANT#" => $votant['Acteur']['ville']
                );
                $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
            }
            return $listeActeurs;
        }

         function listeActeursAbstenus($delib_id) {
                    // Lecture du modele
                    $texte = $this->Model->field('content', 'id=15');
                    $listeActeurs = "";
                    $votes = $this->Vote->findAll("Vote.delib_id = $delib_id AND Vote.resultat=4");
                    foreach($votes as $vote) {
                        $votant = $this->Acteur->findById($vote['Vote']['acteur_id']);
                        $searchReplace = array(
                            "#NOUVELLE_PAGE#" => "<newpage>",
                            "#NOM_ABSTENANT#" => $votant['Acteur']['nom'],
                            "#PRENOM_ABSTENANT#" => $votant['Acteur']['prenom'],
                            "#SALUTATION_ABSTENANT#" => $votant['Acteur']['salutation'],
                            "#TITRE_ABSTENANT#" => $votant['Acteur']['titre'],
                            "#ADRESSE1_ABSTENANT#" => $votant['Acteur']['adresse1'],
                            "#ADRESSE2_ABSTENANT#" => $votant['Acteur']['adresse2'],
                            "#CP_ABSTENANT#" => $votant['Acteur']['cp'],
                            "#VILLE_ABSTENANT#" => $votant['Acteur']['ville']
                );
                $listeActeurs .= str_replace(array_keys($searchReplace), array_values($searchReplace), $texte);
            }
            return $listeActeurs;
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
			"#LISTE_PROJETS_SOMMAIRES#" => $listeProjetsSommaires,
			"#LISTE_PROJETS_DETAILLES#" => $listeProjetsDetailles
		);

		return  str_replace(array_keys($searchReplace),array_values($searchReplace), $texte);
	}

        function odt2html ($delib_id, $content) {
            return ($content);
        }

	function _replaceBalises($texte, $delib_id) {
		// Lecture des informations en base
		$delib = $this->Deliberation->findById($delib_id);
		$collectivite = $this->Collectivite->findById(1);

		// Traitement de la séance
		if (!empty($delib['Deliberation']['seance_id'])){
			$dateSeance = $this->Date->frenchDate(strtotime($delib['Seance']['date']));
			$libelleSeance = $this->Typeseance->field('libelle', 'Typeseance.id = '.$delib['Seance']['type_id']);
		} else {
			$dateSeance = "";
			$libelleSeance = "";
		}
                if (GENERER_DOC_SIMPLE == false){
	            $delib['Deliberation']['deliberation'] = $this->odt2html($delib_id, $delib['Deliberation']['deliberation']);
	            $delib['Deliberation']['texte_synthese'] = $this->odt2html($delib_id, $delib['Deliberation']['texte_synthese']);
	            $delib['Deliberation']['texte_projet'] = $this->odt2html($delib_id, $delib['Deliberation']['texte_projet']);
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
			"#NUMERO_DELIB#" => $delib['Deliberation']['num_delib'],
			"#TITRE_DELIB#" => $delib['Deliberation']['titre'],
			"#LIBELLE_DELIB#" => $delib['Deliberation']['objet'],
			"#TEXTE_DELIB#" => $delib['Deliberation']['deliberation'],
			"#TEXTE_SYNTHESE#" => $delib['Deliberation']['texte_synthese'],
			"#VOTE_POUR#" => $delib['Deliberation']['vote_nb_oui'],	
			"#VOTE_CONTRE#" => $delib['Deliberation']['vote_nb_non'],
		        "#VOTE_ABSTENTION#" => $delib['Deliberation']['vote_nb_abstention'], 	
			"#VOTE_RETRAIT#" => $delib['Deliberation']['vote_nb_retrait'],
			"#VOTE_COMMENTAIRE#" => $delib['Deliberation']['vote_commentaire'],
			"#TEXTE_PROJET#" => $delib['Deliberation']['texte_projet'],
			"#VOTE_POUR#" => $delib['Deliberation']['vote_nb_oui'],	
			"#VOTE_CONTRE#" => $delib['Deliberation']['vote_nb_non'],
		        "#VOTE_ABSTENTION#" => $delib['Deliberation']['vote_nb_abstention'], 	
			"#VOTE_RETRAIT#" => $delib['Deliberation']['vote_nb_retrait'],
			"#VOTE_COMMENTAIRE#" => $delib['Deliberation']['vote_commentaire'],
			"#POSITION_DELIB#" => $delib['Deliberation']['position'],
			"#DEBAT_DELIB#" => $delib['Deliberation']['debat'],
			"#COMMENTAIRE_DELIB#" => $this->_getCommentaireDelib($delib_id),
			"#SALUTATION_RAPPORTEUR#" => $delib['Rapporteur']['salutation'],
			"#NOM_RAPPORTEUR#" => $delib['Rapporteur']['nom'],
			"#PRENOM_RAPPORTEUR#" => $delib['Rapporteur']['prenom'],
			"#TITRE_RAPPORTEUR#" => $delib['Rapporteur']['titre'],
			"#ADRESSE1_RAPPORTEUR#" => $delib['Rapporteur']['adresse1'],
			"#ADRESSE2_RAPPORTEUR#" => $delib['Rapporteur']['adresse2'],
			"#CP_RAPPORTEUR#" => $delib['Rapporteur']['cp'],
			"#VILLE_RAPPORTEUR#" => $delib['Rapporteur']['ville'],
			"#NOM_REDACTEUR#" => $delib['Redacteur']['nom'],
			"#PRENOM_REDACTEUR#" => $delib['Redacteur']['prenom'],
			"#LOGO_COLLECTIVITE#" => '<img src="files/image/logo.jpg">',
			"#NOM_COLLECTIVITE#" => $collectivite['Collectivite']['nom'],
			"#ADRESSE_COLLECTIVITE#" => $collectivite['Collectivite']['adresse'],
			"#CP_COLLECTIVITE#" => $collectivite['Collectivite']['CP'],
			"#VILLE_COLLECTIVITE#" => $collectivite['Collectivite']['ville'],
			"#TELEPHONE_COLLECTIVITE#" => $collectivite['Collectivite']['telephone'],
			"#LISTE_PRESENTS#" => $this->_listeActeursPresents($delib_id),
			"#LISTE_ABSENTS#" => $this->_listeActeursAbsents($delib_id),
			"#LISTE_MANDATAIRES#" => $this->_listeActeursMandates($delib_id),
			"#LISTE_VOTANTS#" =>$this->_listeActeursVotant($delib_id)
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
		$seance = $this->Seance->read(null, $seance_id);
		$model = $this->Model->read(null, $seance['Typeseance']['modelpvsommaire_id']);
		$texte = $model['Model']['content'];
		return $this->_replaceBalisesSeance($texte, $seance_id);
	}

	function generatePVDetaille($seance_id) {
		$seance = $this->Seance->read(null, $seance_id);
		$model = $this->Model->read(null, $seance['Typeseance']['modelpvdetaille_id']);
		$texte = $model['Model']['content'];
		return $this->_replaceBalisesSeance($texte, $seance_id);
	}


	function import($model_id) {
		$this->set('USE_GEDOOO', USE_GEDOOO);
		$this->set('model_id', $model_id);
                $Model = $this->Model->read(null, $model_id);
		$this->set('libelle', $Model['Model']['modele']);
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
		} else {
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
