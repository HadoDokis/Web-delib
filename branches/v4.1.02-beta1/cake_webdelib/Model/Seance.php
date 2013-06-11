<?php
class Seance extends AppModel {

	var $name = 'Seance';
	var $days = array ('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
	var $months = array ('','Janvier','Février','Mars','Avril','Mai','Juin',
			'Juillet','Août','Septembre','Octobre','Novembre','Décembre');

	var $validate = array(
			'type_id'      => array(array( 'rule'    => 'notEmpty',
					'message' => 'Entrer le type de seance associé.')),
			'avis'         => array(array( 'rule'    => 'notEmpty',
					'message' => 'Sélectionner un avis')),
			'date'         => array(array( 'rule'    => 'notEmpty',
					'message' => 'Entrer une date valide.')),
			'commission'   => array(array('rule'     => 'notEmpty',
					'message' => 'Entrer le texte de débat.')),
			'debat_global_type'   => array(array('rule' => array('checkMimetype', 'debat_global',  array('application/vnd.oasis.opendocument.text')),
					'message' => "Ce type de fichier n'est pas autorisé")),
	);

	var $belongsTo=array(
			'Typeseance'=>array(
					'className'=>'Typeseance',
					'conditions'=>'',
					'order'=>'',
					'dependent'=>false,
					'foreignKey'=>'type_id'),
			'Secretaire' => array('className' => 'Acteur',
					'conditions' => '',
					'order' => '',
					'dependent' => false,
					'foreignKey' => 'secretaire_id'),
			'President' => array('className' => 'Acteur',
					'conditions' => '',
					'order' => '',
					'dependent' => false,
					'foreignKey' => 'president_id'));

	var $hasMany = array(
			'Infosup'=>array('dependent' => true,
					'foreignKey' => 'foreign_key',
					'conditions' => array('Infosup.model' => 'Seance')));

	//    var $hasAndBelongsToMany = array('Deliberation');
	var $hasAndBelongsToMany = array( 'Deliberation' => array( 'className' => 'Deliberation',
			'joinTable' => 'deliberations_seances',
			'foreignKey' => 'seance_id',
			'associationForeignKey' => 'deliberation_id',
			'unique' => true,
			'conditions' => array('Deliberation.etat >='=>0),
			'fields' => '',
			'order' => 'DeliberationsSeance.position',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''));


	/* retourne la liste des séances futures avec le nom du type de séance  */
	function generateList($conditionSup=null, $afficherTtesLesSeances = false, $natures = array()) {
		$generateList = array();
		App::import('model','TypeseancesTypeacte');
		$TypeseancesTypeacte = new TypeseancesTypeacte();
		$typeseances = $TypeseancesTypeacte->getTypeseanceParNature($natures);

		$conditions  = array();
		$conditions['Seance.type_id'] = $typeseances;
		$conditions['Seance.traitee'] = '0';

		if (!empty($conditionSup))
			$conditions = Set::pushDiff($conditions,$conditionSup);

		$this->Behaviors->attach('Containable');
		$seances = $this->find('all',array('conditions' => $conditions,
				'order'      => array('date ASC'),
				'fields'     => array('Seance.id', 'Seance.date'),
				'contain'    => array('Typeseance.action', 'Typeseance.retard', 'Typeseance.libelle')));

		foreach ($seances as $seance) {
			$deliberante = "----";
			if ($seance['Typeseance']['action'] == 0)
				$deliberante = "(*)";
			if ($afficherTtesLesSeances) {
				$dateTimeStamp = strtotime($seance['Seance']['date']);
				$dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.
						$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.
						date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
				$generateList[$seance['Seance']['id']]= "$deliberante ".$seance['Typeseance']['libelle']. " du ".$dateFr;
			}
			else {
				$retard=$seance['Typeseance']['retard'];

				if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y")))){
					$dateTimeStamp = strtotime($seance['Seance']['date']);
					$dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.
							$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.
							date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
					$generateList[$seance['Seance']['id']]= "$deliberante ".$seance['Typeseance']['libelle']. " du ".$dateFr;
				}
			}
		}
		return $generateList;
	}

	function generateAllList() {
		$this->Behaviors->attach('Containable');
		$generateList = array();
		$seances = $this->find('all', array('order'   => 'date ASC',
											'fields'  => array('Seance.id', 'Seance.type_id', 'Seance.date'),
											'contain' => array('Typeseance.libelle', 'Typeseance.action')));

		foreach ($seances as $seance){
			$deliberante = "";
			if ($seance['Typeseance']['action'] == 0)
				$deliberante = "(*)";

			$dateTimeStamp = strtotime($seance['Seance']['date']);
			$dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.
					$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.
					date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
			$generateList[$seance['Seance']['id']]="$deliberante ". $seance['Typeseance']['libelle']. " du ".$dateFr;
		}
		return $generateList;
	}

	function NaturecanSave($seance_id, $nature_id) {
		if (empty($seance_id))
			return true;
		$seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
			                             'recursive'  => -1,
			                             'fields'     => array('Seance.type_id')));
                App::import('Model', 'TypeseancesTypeacte');
                $this->TypeseancesTypeacte = new TypeseancesTypeacte();
		$natures = $this->TypeseancesTypeacte->getNaturesParTypeseance($seance['Seance']['type_id']);
		return in_array($nature_id, $natures);
	}

	function getDeliberations($seance_id, $options = array()) {
		// initialisation des valeurs par défaut
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();

		$defaut = array(
				'order'     => array('Deliberationseance.position ASC'),
				'conditions' => array());
		$options = array_merge($defaut, $options);

		$options['conditions']['Seance.id'] = $seance_id;
		$options['conditions']['Deliberation.etat >='] = 0;
		$deliberations = $this->Deliberationseance->find('all', $options);
		for ($i = 0; $i < count($deliberations); $i++) {
			if (isset($deliberations[$i]['Deliberation']['theme_id'])) {
				$theme = $this->Deliberation->Theme->find('first',
						array('conditions' => array('Theme.id'=> $deliberations[$i]['Deliberation']['theme_id']),
								'fields'     => array('Theme.id', 'Theme.libelle'),
								'recursive'  => -1));
				$deliberations[$i]['Theme']['libelle'] = $theme['Theme']['libelle'];
			}
			if (isset ( $deliberations[$i]['Deliberation']['rapporteur_id'])) {
				$rapporteur = $this->Deliberation->Rapporteur->find('first',
						array('conditions' => array('Rapporteur.id'=> $deliberations[$i]['Deliberation']['rapporteur_id']),
								'fields'     => array('Rapporteur.id', 'Rapporteur.nom', 'Rapporteur.prenom'),
								'recursive'  => -1));
				$deliberations[$i]['Rapporteur']['nom'] = $rapporteur['Rapporteur']['nom'];
				$deliberations[$i]['Rapporteur']['prenom'] = $rapporteur['Rapporteur']['prenom'];
			}
		}
		return ($deliberations);
	}

	function getDeliberationsId($seance_id) {
		$tab = array();
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();

		$this->Deliberationseance->Behaviors->attach('Containable');
		$deliberations = $this->Deliberationseance->find('all', array(
				'conditions' => array('Deliberationseance.seance_id' => $seance_id, 'Deliberation.etat >=' => 0),
				'fields'     => array('Deliberationseance.deliberation_id'),
                                'contain'    => array('Deliberation.id', 'Deliberation.etat', 'Seance.id'),
                                'order'      => 'Deliberationseance.position ASC',
				));
		foreach ($deliberations as $deliberation)
			$tab[] = $deliberation['Deliberationseance']['deliberation_id'];
		return $tab;
	}

	function getDeliberationsIdByTypeseance_id($type_id) {
		$tabs = array();
		$typeseances = $this->Typeseance->find('all', array(
				'conditions' => array('Typeseance.id' => $type_id),
				'recursive'  => -1,
				'fields'     => array('.id')));
		foreach($seances as $seance) {
			$ids = $this->getDeliberationsId($seance['Seance']['id']);
			$tabs = array_merge($tabs, $ids);
		}
		return $tabs;
	}

	function getLastPosition($seance_id) {
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();
		$deliberations = $this->Deliberationseance->find('count', array('conditions' => array('Seance.id' => $seance_id,
				'Deliberation.etat !='=> -1) ));
		return($deliberations+1);
	}

	function reOrdonne($delib_id, $seances_selectionnees) {
		if (is_int($seances_selectionnees)) {
			$seance_id = $seances_selectionnees;
			unset($seances_selectionnees);
			$seances_selectionnees = array();
			$seances_selectionnees[0] =  $seance_id;
		}
		elseif (!isset($seances_selectionnees) || empty($seances_selectionnees) || ($seances_selectionnees[0] == null))
		    $seances_selectionnees= array();

		$seances_enregistrees = array();
		App::import('Model', 'Deliberationseance');
		$this->Deliberationseance = new Deliberationseance();

		$delibs = $this->Deliberationseance->find('all', array('conditions' => array('Deliberation.id' => $delib_id),
				'fields'     => array('Seance.id')));
		foreach ($delibs as $delib)
			$seances_enregistrees[] = $delib['Seance']['id'];
		$seances_a_retirer = array_diff($seances_enregistrees, $seances_selectionnees);
		foreach($seances_a_retirer as $key => $seance_id) {
			$position = 0;
			$jointure = $this->Deliberationseance->find('first', array('conditions' => array( 'Seance.id'            => $seance_id,
					'Deliberation.id'      => $delib_id,
					'Deliberation.etat !=' => -1),
					'fields'     => array( 'Deliberationseance.id')));
			$this->Deliberationseance->delete($jointure['Deliberationseance']['id']);

			$delibs = $this->Deliberationseance->find('all', array('conditions' => array( 'Seance.id'            => $seance_id,
					'Deliberation.etat !=' => -1),
					'fields'     => array( 'Deliberationseance.id',
							'Deliberationseance.position' ),
					'order'      => array( 'Deliberationseance.position ASC' )));
			// pour toutes les délibs
			foreach($delibs as $delib) {
				$position++;
				if ($position != $delib['Deliberationseance']['position'])
					$this->Deliberationseance->save(array( 'id'      => $delib['Deliberationseance']['id'],
							'position'=> $position),
							array( 'validate' => false,
									'callbacks' => false));
			}

		}
                if (is_array($seances_enregistrees) and  (!empty($seances_enregistrees)))  {
		    $seances_a_ajouter = array_diff($seances_selectionnees, $seances_enregistrees);
                }
                else
                    $seances_a_ajouter = $seances_selectionnees;

		foreach($seances_a_ajouter as $seance_id) {
			$this->Deliberationseance->create();
			$Deliberationseance['Deliberationseance']['position'] = intval($this->getLastPosition($seance_id));
			$Deliberationseance['Deliberationseance']['deliberation_id'] = $delib_id;
			$Deliberationseance['Deliberationseance']['seance_id'] = $seance_id;
			$this->Deliberationseance->save($Deliberationseance['Deliberationseance']);
		}

		$multidelibs = $this->Deliberation->find('all', array('conditions' => array('Deliberation.parent_id' => $delib_id),
				'recursive'  => -1,
				'fields' => array('Deliberation.id')));
                if (isset($multidelibs) && !empty($multidelibs)) 
		    foreach ($multidelibs as $multidelib)
			$this->reOrdonne($multidelib['Deliberation']['id'], $seances_selectionnees);
	}

	function getDate($seance_id) {
		if (empty($seance_id))
			return '';
		else{
			$objCourant = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
					'fields'     => array('Seance.date'),
					'recursive'  => -1));
			return $objCourant['Seance']['date'];
		}
	}

	function getType($seance_id) {
		if (empty($seance_id))
			return '';
		else{
			$objCourant = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
					'fields'     => array('Seance.type_id'),
					'recursive'  => -1));
			return $objCourant['Seance']['type_id'];
		}
	}

	function getSeanceDeliberante($tab_seances) {
		$this->Behaviors->attach('Containable');
		foreach($tab_seances as $key => $seance_id) {
			$seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
					'fields'     => array('Seance.id'),
					'contain'    => array('Typeseance.action')));
			if ($seance['Typeseance']['action'] == 0) {
				return $seance['Seance']['id'];
			}
		}
		return null;
	}

        function isSeanceDeliberante($seance_id) {
            $this->Behaviors->attach('Containable');
            $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
                                                 'fields'     => array('Seance.id', 'Seance.type_id'),
                                                 'contain'    => array('Typeseance.action')));
            return ($seance['Typeseance']['action'] == 0); 
        }

	function makeBalise($seance_id, $oDevPart=null, $include_projets=false, $conditions = array()) {
		include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/DateComponent.php');
		$this->Date = new DateComponent;

		$this->Behaviors->attach('Containable');
		$seance = $this->find('first', array('conditions' => array('Seance.id' =>$seance_id),
				'contain'    => array('Deliberation.id')));

		$return = false;
		if ( $oDevPart == null) {
			$oDevPart = new GDO_PartType();
			$return = true;
			$suffixe = "_seances";
		}
		else {
			$suffixe = "_seance";
		}
		$date_lettres =  $this->Date->dateLettres(strtotime($seance['Seance']['date']));
		//$oDevPart->addElement(new GDO_FieldType('date_seance_lettres'.$suffixe, ($date_lettres), 'text'));
		$oDevPart->addElement(new GDO_FieldType('date_seance_lettres', $date_lettres, 'text'));
		$oDevPart->addElement(new GDO_FieldType("heure".$suffixe,       $this->Date->Hour($seance['Seance']['date']),     'text'));
		$oDevPart->addElement(new GDO_FieldType("date".$suffixe,        $this->Date->frDate($seance['Seance']['date']),       'date'));
		$oDevPart->addElement(new GDO_FieldType("hh".$suffixe,          $this->Date->Hour($seance['Seance']['date'], 'hh'), 'string'));
		$oDevPart->addElement(new GDO_FieldType("mm".$suffixe,          $this->Date->Hour($seance['Seance']['date'], 'mm'), 'string'));
		$oDevPart->addElement(new GDO_FieldType("date_convocation".$suffixe, $this->Date->frDate($seance['Seance']['date_convocation']),   'date'));
		$oDevPart->addElement(new GDO_FieldType("identifiant".$suffixe, $seance['Seance']['id'], 'text'));
		$oDevPart->addElement(new GDO_FieldType("commentaire".$suffixe, $seance['Seance']['commentaire'], 'lines'));

		$elus = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
		if (!empty($elus)) {
			$oDevPart->addElement(new GDO_FieldType("nombre_acteur".$suffixe,     count($elus), "text"));
			$blocConvoque = new GDO_IterationType("Convoques");
			$oDevPartConvoque = new GDO_PartType();
			foreach($elus as $elu) {
				$oDevPartConvoque = new GDO_PartType();
				$oDevPartConvoque->addElement(new GDO_FieldType("nom_acteur_convoque".$suffixe, ($elu['Acteur']['nom']), "text"));
				$oDevPartConvoque->addElement(new GDO_FieldType("prenom_acteur_convoque".$suffixe, ($elu['Acteur']['prenom']), "text"));
				$oDevPartConvoque->addElement(new GDO_FieldType("salutation_acteur_convoque".$suffixe, ($elu['Acteur']['salutation']), "text"));
				$oDevPartConvoque->addElement(new GDO_FieldType("titre_acteur_convoque".$suffixe, ($elu['Acteur']['titre']), "text"));
				$oDevPartConvoque->addElement(new GDO_FieldType("note_acteur_convoque".$suffixe, ($elu['Acteur']['note']), "text"));
				$blocConvoque->addPart($oDevPartConvoque);
			}
			$oDevPart->addElement($blocConvoque);
		}

		$typeSeance = $this->Typeseance->find('first',
				array('conditions' => array('Typeseance.id' => $seance['Seance']['type_id']),
						'recursive'   => -1));
		$oDevPart->addElement(new GDO_FieldType('type'.$suffixe, ($typeSeance['Typeseance']['libelle']), 'text'));

		$this->President->makeBalise($oDevPart, $seance['Seance']['president_id']);
		$this->Secretaire->makeBalise($oDevPart, $seance['Seance']['secretaire_id']);

                App::import('Model', 'Deliberationseance');
                $this->Deliberationseance = new Deliberationseance();
                $avisSeances =  $this->Deliberationseance->find('all', array(
                                'conditions' => array('seance_id' => $seance['Seance']['id']),
                                'recursive'  => -1));
                if (!empty($avisSeances)) {
                    $aviss =  new GDO_IterationType("AvisSeance");
                    foreach($avisSeances as $avisSeance) {
                        $Part = new GDO_PartType();
                        $Part->addElement(new GDO_FieldType("commentaire", ($avisSeance['Deliberationseance']['commentaire']), "lines"));
                        $aviss->addPart($Part);
                    }
                    @$oDevPart->addElement($aviss);
                }

		$infosups = $this->Infosup->find( 'all',
                                                  array('conditions' => array('Infosup.foreign_key' => $seance['Seance']['id'],
                                                                              'Infosup.model'        => 'Seance'),
                                                        'recursive'   => -1));
                if (isset($infosups) && !empty($infosups))  {
                    foreach($infosups as  $champs) {
                        $oDevPart->addElement($this->Infosup->addField($champs['Infosup'], $seance_id, 'Seance'));
                    }
                }
                else {
                    $defs = $this->Infosup->Infosupdef->find('all', array('conditions'=>array('model' => 'Seance'), 'recursive' => -1));
                    foreach($defs as $def) {
                        $oDevPart->addElement(new GDO_FieldType($def['Infosupdef']['code'],  utf8_encode(' '), 'text')) ;
                    }
                }
		if ($include_projets) {
			if (isset($seance['Deliberation']) && !empty($seance['Deliberation'])) {
				$blocProjets = new GDO_IterationType("Projets");
				foreach($seance['Deliberation'] as $deliberation){
					$conditions['Deliberation.id'] = $deliberation['DeliberationsSeance']['deliberation_id'];
					$projet = $this->Deliberation->find('first', array('conditions' => $conditions,
							'recursive'  => -1));
					$Part = new GDO_PartType();
					$this->Deliberation->makeBalisesProjet($projet, $Part, true, $seance['Seance']['id']);
					$blocProjets->addPart($Part);
				}
				$oDevPart->addElement($blocProjets);
			}
		}

		if ($return) return  $oDevPart;
	}

	function getSeancesDeliberantes () {
		$tab_seances = array();
		$this->Behaviors->attach('Containable');
		$seances = $this->find('all', array('conditions' => array('Typeseance.action' => 0,
				'Seance.traitee'    => 0),
				'fields'     => array('Seance.id'),
				'contain'    => array('Typeseance.action') ));
		if (isset($seances) && (!empty($seances)))
			foreach ($seances as $seance)
			$tab_seances[] = $seance['Seance']['id'];
		return($tab_seances);
	}
}
?>
