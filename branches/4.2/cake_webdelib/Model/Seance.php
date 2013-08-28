<?php
    App::uses( 'DateFrench', 'Utility' );
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
					'conditions' => array('Infosup.model' => 'Seance')),
                        'Deliberationseance' =>array(
					'className'    => 'Deliberationseance',
                                        //'joinTable' => 'Deliberation',
					'foreignKey'   => 'seance_id',
                                        //'conditions' => array('Deliberation.etat >='=>0),
                                        'order'      => 'Deliberationseance.position ASC'
                         ),


            );

	//    var $hasAndBelongsToMany = array('Deliberation');
	var $hasAndBelongsToMany = array( 'Deliberation' => array( 'className' => 'Deliberation',
			'joinTable' => 'deliberations_seances',
			'foreignKey' => 'seance_id',
			'associationForeignKey' => 'deliberation_id',
			'unique' => true,
			'conditions' => array('Deliberation.etat >='=>0),
			'fields' => '',
			'order' => 'DeliberationsSeance.position ASC',
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

        /**
         * Récupère en base la liste des délibérations pour une séance ainsi que le theme et le rapporteur associé
         * @todo Diviser le nombre de requêtes en utilisant contain + conditions ! (vérifier que valeur != null)
         * @param integer $seance_id
         * @return array deliberations liste des délibérations avec libelle du theme et nom, prenom du rapporteur
         */
	function getDeliberations($seance_id) {
            $deliberations = $this->Deliberationseance->find(
                    'all',
                    array(
                        'fields' => array('Deliberationseance.seance_id','Deliberationseance.deliberation_id','Deliberationseance.position','Deliberation.*'),
                        'contain'=>'Deliberation',
                        'recursive' => 1,
                        'conditions' =>  array(
                            'Deliberationseance.seance_id' => $seance_id,
                            'Deliberation.etat >='=>0,
                        ),
                        'order'=>'Deliberationseance.position ASC',
                    )
                );

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

        /**
         * @deprecated
         * NOTE: cette fonction n'est pas utilisée et ne pourrai pas fonctionner en l'état
         */
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

		$delibs = $this->Deliberationseance->find('all', array(
                                'conditions' => array('Deliberation.id' => $delib_id),
				'fields'     => array('Seance.id',
                                    'Deliberationseance.deliberation_id','Deliberationseance.id',
                                    'Deliberationseance.position','Deliberationseance.seance_id'),
                                'order'      => array( 'Deliberationseance.position ASC' )));
		foreach ($delibs as $delib)
			$seances_enregistrees[] = $delib['Seance']['id'];

		$seances_a_retirer = array_diff($seances_enregistrees, $seances_selectionnees);

		foreach($seances_a_retirer as $key => $seance_id) {
                    //$position = 1;
                    $jointure = $this->Deliberationseance->find('first', array('conditions' => array( 'Seance.id'            => $seance_id,
                                    'Deliberation.id'      => $delib_id,
                                    'Deliberation.etat !=' => -1),
                                    'fields'     => array( 'Deliberationseance.id')));
                    $this->Deliberationseance->delete($jointure['Deliberationseance']['id']);
//On commente le code car on ne veut pas reordonner après une supression
                   /* $seances = $this->Deliberationseance->find('all', array('conditions' => array( 'Seance.id'            => $seance_id,
                                    'Deliberation.etat !=' => -1),
                                    'fields'     => array( 'Deliberationseance.id',
                                                    'Deliberationseance.position' ),
                                    'order'      => array( 'Deliberationseance.position ASC' )));
                    // pour toutes les délibsd
                    /*foreach($delibs as $delib) {
                            if ($position != $delib['Deliberationseance']['position'])
                                    $this->Deliberationseance->save(array( 'id'      => $delib['Deliberationseance']['id'],
                                            'deliberation_id'      => $delib['Deliberationseance']['deliberation_id'],
                                            'seance_id'      => $delib['Deliberationseance']['seance_id'],
                                            'position' => $position++),
                                                    array( 'validate' => false,
                                                                    'callbacks' => false));
                    }*/
		}

                if (is_array($seances_enregistrees) and  (!empty($seances_enregistrees)))  {
		    $seances_a_ajouter = array_diff($seances_selectionnees, $seances_enregistrees);
                }
                else
                    $seances_a_ajouter = $seances_selectionnees;

		foreach($seances_a_ajouter as $seance_id) {
                        $Deliberationseance=array();
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

    /**
     * Contruit l'objet GDO_PartType passé en paramètre, ou en crée un nouveau si celui-ci est null et le rempli avec les valeurs des champs trouvés en base
     * les dates et heure sont mises en français : @see DateComponent
     * Données Gedoo :
     *  - les attributs de la séance (passée en paramètre) :
     *      - date_seance_lettres/$this->Date->dateLettres(strtotime($seance['Seance']['date']))/text
     *      - heure.$suffixe/$this->Date->Hour($seance['Seance']['date'])/text
     *      - date.$suffixe/$this->Date->frDate($seance['Seance']['date'])/date
     *      - hh.$suffixe/$this->Date->Hour($seance['Seance']['date'], 'hh')/string
     *      - mm.$suffixe/$this->Date->Hour($seance['Seance']['date']/string
     *      - date_convocation.$suffixe/$this->Date->frDate($seance['Seance']['date_convocation'])/date
     *      - identifiant.$suffixe/$seance['Seance']['id']/text
     *      - commentaire.$suffixe/$seance['Seance']['commentaire']/lines
     *  - si la liste des acteur convoqués n'est pas vide, un bloc comprenant les informations sur les élus convoqués à la séance :
     *      - nom_acteur_convoque.$suffixe/Acteur.{n}.nom/text
     *      - prenom_acteur_convoque.$suffixe/Acteur.{n}.prenom/text
     *      - salutation_acteur_convoque.$suffixe/Acteur.{n}.salutation/text
     *      - titre_acteur_convoque.$suffixe/Acteur.{n}.titre/text
     *      - note_acteur_convoque.$suffixe/Acteur.{n}.note/text
     *  - type.$suffixe/typeseance.libelle/text
     *  - un bloc président @see Acteur:makeBalise
     *  - un bloc secrétaire @see Acteur:makeBalise
     *  - un bloc concernant les avis des séances :
     *      - commentaire/Deliberationseance.{n}.commentaire/lines
     *  - un bloc concernant les infos sups @see Infosup:addField
     *  - un bloc projet concernant les délibérations de la séance (si $include_projets = true) :
     *      - pour chaque délibération @see Deliberation:makeBalisesProjet
     *
     * ATTENTION:
     *  - utilisation de $oDevPart puis de @$oDevPart (@$oDevPart->addElement($aviss);)
     *
     * @see
     *  - Acteur:makeBalise
     *  - Acteur:makeBalise
     *  - Infosup:addField
     *
     * @param integer $seance_id l'id de la seance
     * @param GDO_PartType $oDevPart l'objet GDO_PartType dans lequel ajouter les champs
     * - Si ce champ est null, crée un nouveau objet GDO_PartType et met le suffixe seance au pluriel, la variable $return à true (la méthode retournera l'objet GDO_PartType)
     * @param boolean $include_projets, inclure ou non un bloc projet (infos sur les délibérations de la séance)
     * @param array $conditions, tableau de conditions pour la méthode find sur le modèle délibération.
     * - Si $include_projets = false, le paramètre $conditions est inutilisé
     * @return \GDO_PartType $oDevPart, l'objet GDO_PartType construit si le paramètre $oDevPart passé est null, retourne rien sinon
     */
    function makeBalise($seance_id, $oDevPart = null, $include_projets = false, $conditions = array()) {
        include_once (ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
        $this->Date = new DateComponent;

        $this->Behaviors->attach('Containable');
        $seance = $this->find('first', array('conditions' => array('Seance.id' => $seance_id),
            'contain' => array('Deliberation.id')));

        $return = false;
        $suffixe = "_seance";
        if ($oDevPart == null) {
            $oDevPart = new GDO_PartType();
            $return = true;
            $suffixe = "_seances";
        }

        $date_lettres= (!empty($seance['Seance']['date'])) ? $this->Date->dateLettres(strtotime($seance['Seance']['date'])) : '';

        //$oDevPart->addElement(new GDO_FieldType('date_seance_lettres'.$suffixe, ($date_lettres), 'text'));
        $oDevPart->addElement(new GDO_FieldType('date_seance_lettres', $date_lettres, 'text'));
        $oDevPart->addElement(new GDO_FieldType("heure" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->Hour($seance['Seance']['date']) : ''), 'text'));
        $oDevPart->addElement(new GDO_FieldType("date" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->frDate($seance['Seance']['date']) : ''), 'date'));
        $oDevPart->addElement(new GDO_FieldType("hh" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->Hour($seance['Seance']['date'], 'hh') : ''), 'string'));
        $oDevPart->addElement(new GDO_FieldType("mm" . $suffixe, (!empty($seance['Seance']['date']) ? $this->Date->Hour($seance['Seance']['date'], 'mm') : ''), 'string'));
        $oDevPart->addElement(new GDO_FieldType("date_convocation" . $suffixe, $this->Date->frDate($seance['Seance']['date_convocation']), 'date'));
        $oDevPart->addElement(new GDO_FieldType("identifiant" . $suffixe, (!empty($seance['Seance']['id']) ? $seance['Seance']['id'] : ''), 'text'));
        $oDevPart->addElement(new GDO_FieldType("commentaire" . $suffixe, (!empty($seance['Seance']['commentaire']) ? $seance['Seance']['commentaire'] : ''), 'lines'));

        $elus = $this->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id']);
        if (!empty($elus)) {
            //insère un bloc comprenant les informations sur les élus convoqués à la séance
            $oDevPart->addElement(new GDO_FieldType("nombre_acteur" . $suffixe, count($elus), "text"));
            $blocConvoque = new GDO_IterationType("Convoques");
            $oDevPartConvoque = new GDO_PartType();
            foreach ($elus as $elu) {
                $oDevPartConvoque = new GDO_PartType();
                $oDevPartConvoque->addElement(new GDO_FieldType("nom_acteur_convoque" . $suffixe, ($elu['Acteur']['nom']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("prenom_acteur_convoque" . $suffixe, ($elu['Acteur']['prenom']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("salutation_acteur_convoque" . $suffixe, ($elu['Acteur']['salutation']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("titre_acteur_convoque" . $suffixe, ($elu['Acteur']['titre']), "text"));
                $oDevPartConvoque->addElement(new GDO_FieldType("note_acteur_convoque" . $suffixe, ($elu['Acteur']['note']), "text"));
                $blocConvoque->addPart($oDevPartConvoque);
            }
            $oDevPart->addElement($blocConvoque);
        }

        $typeSeance = $this->Typeseance->find('first', array(
            'conditions' => array('Typeseance.id' => $seance['Seance']['type_id']),
            'recursive' => -1));
        $oDevPart->addElement(new GDO_FieldType('type' . $suffixe, (!empty($typeSeance['Typeseance']['libelle']) ? $typeSeance['Typeseance']['libelle'] : ''), 'text'));

        $this->President->makeBalise($oDevPart, $seance['Seance']['president_id']);
        $this->Secretaire->makeBalise($oDevPart, $seance['Seance']['secretaire_id']);

        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $avisSeances = $this->Deliberationseance->find('all', array(
            'conditions' => array('seance_id' => $seance['Seance']['id']),
            'recursive' => -1));
        if (!empty($avisSeances)) {
            $aviss = new GDO_IterationType("AvisSeance");
            foreach ($avisSeances as $avisSeance) {
                $Part = new GDO_PartType();
                $Part->addElement(new GDO_FieldType("commentaire", ($avisSeance['Deliberationseance']['commentaire']), "lines"));
                $aviss->addPart($Part);
            }
            @$oDevPart->addElement($aviss);
        }

        $infosups = $this->Infosup->find('all', array('conditions' => array('Infosup.foreign_key' => $seance['Seance']['id'],
                'Infosup.model' => 'Seance'),
            'recursive' => -1));

        if (isset($infosups) && !empty($infosups)) {
            foreach ($infosups as $champs) {
                $infosup = $this->Infosup->addField($champs['Infosup'], $seance_id, 'Seance');
                if (!empty($infosup))
                    $oDevPart->addElement($infosup);
            }
        }
        /*
        else {
            $defs = $this->Infosup->Infosupdef->find('all', array('conditions' => array('model' => 'Seance'), 'recursive' => -1));
            foreach ($defs as $def) {
                $oDevPart->addElement(new GDO_FieldType($def['Infosupdef']['code'], '', 'text'));
            }
        }
        */
        if ($include_projets) {
            if (isset($seance['Deliberation']) && !empty($seance['Deliberation'])) {
                $blocProjets = new GDO_IterationType("Projets");
                foreach ($seance['Deliberation'] as $deliberation) {
                    $conditions['Deliberation.id'] = $deliberation['DeliberationsSeance']['deliberation_id'];
                    $projet = $this->Deliberation->find('first', array(
                        'conditions' => $conditions,
                        'recursive' => -1));
                    $Part = new GDO_PartType();
                    $this->Deliberation->makeBalisesProjet($projet, $Part, true, $seance['Seance']['id']);
                    $blocProjets->addPart($Part);
                }
                $oDevPart->addElement($blocProjets);
            }
        }

        if ($return)
            return $oDevPart;
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

    // -------------------------------------------------------------------------

        /**
         *
         * @todo: suffixe
         *
         * @param array $seance
         * @return array
         */
		public function gedoooNormalize( array $seance ) {
			$date_seance = Hash::get( $seance, 'Seance.date' );
			$seance['Seance']['date_lettres'] = ( empty( $date_seance ) ? null : DateFrench::dateLettres( strtotime( $date_seance ) ) );
			$seance['Seance']['heure'] = ( empty( $date_seance ) ? null : DateFrench::hour( $date_seance ) );
			$seance['Seance']['date'] = ( empty( $date_seance ) ? null : DateFrench::frDate( $date_seance ) );
			$seance['Seance']['hh'] = ( empty( $date_seance ) ? null : DateFrench::hour( $date_seance, 'hh' ) );
			$seance['Seance']['mm'] = ( empty( $date_seance ) ? null : DateFrench::hour( $date_seance, 'mm' ) );

			$date_convocation = Hash::get( $seance, 'Seance.date_convocation' );
			$seance['Seance']['date_convocation'] = ( empty( $date_convocation ) ? null : DateFrench::frDate( $date_convocation ) );

			return $seance;
		}

        /**
         *
         * @todo: suffixe
         *
         * @return array
         */
		public function gedoooPaths() {
            $types = array(
                'commentaire_seance' => 'Seance.commentaire',
				'date_lettres_seance' => 'Seance.date_lettres',
				'heure_seance' => 'Seance.heure',
				'date_seance' => 'Seance.date',
				'hh_seance' => 'Seance.hh',
				'mm_seance' => 'Seance.mm',
				'date_convocation_seance' => 'Seance.date_convocation',
				'identifiant_seance' => 'Seance.id',
                'type_seance' => 'Typeseance.libelle',
            );

            $types = Hash::merge( $types, $this->types() );

			return $types;
		}

        /**
         * @return array
         */
		public function gedoooTypes() {
            $types = array(
                'Seance.commentaire' => 'text',
				'Seance.date_lettres' => 'text',
				'Seance.heure' => 'text',
				'Seance.date' => 'date',
				'Seance.hh' => 'text',
				'Seance.mm' => 'text',
				'Seance.date_convocation' => 'date',
                'Seance.id' => 'text',
                'Typeseance.libelle' => 'text',
            );

            $types = Hash::merge( $this->types(), $types );

			return $types;
		}
}
?>
