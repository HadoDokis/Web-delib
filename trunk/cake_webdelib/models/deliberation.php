<?php
class Deliberation extends AppModel {

    var $name = 'Deliberation';

    var $validate = array( 'objet'     => array(
			                  array( 'rule'    => 'notEmpty',
                                                 'message' => 'L\'objet est obligatoire')),
                           'nature_id' => array(
                                          array( 'rule'    => array('canSaveNature'),
                                                 'message' => "Cette s�ance ne peux pas enregistrer cette nature d'acte")),
                           'seance_id' => array(
                                          array('rule'    => array('canSaveSeances'),
                                                 'message' => "Un projet ne peux contenir qu'une s�ance d�lib�rante")),
                           'texte_projet_type'   => array( 
                                                    array('rule' => array('checkMimetype', 'texte_projet', array('application/vnd.oasis.opendocument.text')),
                                                        'message' => "Ce type de fichier n'est pas autoris�")),
                           'texte_synthese_type' => array(
                                                    array('rule' => array('checkMimetype', 'texte_synthese',  array('application/vnd.oasis.opendocument.text')),
                                                        'message' => "Ce type de fichier n'est pas autoris�")),
                           'deliberation_type'   => array(
                                                    array('rule' => array('checkMimetype', 'deliberation',  array('application/vnd.oasis.opendocument.text')),
                                                        'message' => "Ce type de fichier n'est pas autoris�")),
                           'debat_type'           => array(
                                                    array('rule' => array('checkMimetype', 'debat',  array('application/vnd.oasis.opendocument.text')),
                                                        'message' => "Ce type de fichier n'est pas autoris�")),
                           'commission_type'      => array(
                                                     array('rule' => array('checkMimetype', 'commission',  array('application/vnd.oasis.opendocument.text')),
                                                        'message' => "Ce type de fichier n'est pas autoris�")),

             
                                                    );
                          
        

	//dependent : pour les suppression en cascades. ici � false pour ne pas modifier le referentiel
	var $belongsTo = array(
/*                'Nomenclature'=>array(
                        'className'    => 'Nomenclature',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    => false,
                        'foreignKey'   => 'num_pref'), 
 */
		'Service'=>array(
			'className'    => 'Service',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'service_id'),
		'Theme'=>array(
			'className'    => 'Theme',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'theme_id'),
		'Circuit'=>array(
			'className'    => 'Cakeflow.Circuit',
			'conditions'   => '',
			'order'        => '',
			'dependent'    => false,
			'foreignKey'   => 'circuit_id'), 
		'Redacteur' =>array(
			'className'    => 'User',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'redacteur_id'),
		'Rapporteur'=> array(
			'className'    => 'Acteur',
			'conditions'   => '',
			'order'        => '',
			'dependent'    =>  true,
			'foreignKey'   => 'rapporteur_id'),
                'Nature'=> array(
                        'className'    => 'Nature',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    =>  true,
                        'foreignKey'   => 'nature_id')
		);
		
	var $hasMany = array(
		'TdtMessage' => array (
			'className'    => 'TdtMessage',
			'foreignKey'   => 'delib_id'),
		'Historique' =>array(
			'className'    => 'Historique',
			'foreignKey'   => 'delib_id'),
		'Traitement'=>array(
			'className'    => 'Cakeflow.Traitement',
			'foreignKey'   => 'target_id'), 
		'Annex'=>array(
			'className'    => 'Annex',
			'foreignKey' => 'foreign_key',
			//'conditions' => array('Annex.model' => 'Deliberation'),
			'dependent' => true),
		'Commentaire'=>array(
			'className'    => 'Commentaire',
			'foreignKey'   => 'delib_id'),
		'Listepresence'=>array(
			'className'    => 'Listepresence',
			'foreignKey'   => 'delib_id'),
		'Vote'=>array(
			'className'    => 'Vote',
			'foreignKey'   => 'delib_id'),
		'Infosup'=>array(
			'dependent' => true,
                        'foreignKey' => 'foreign_key',
                        'conditions' => array('Infosup.model' => 'Deliberation')),
		'Multidelib'=>array(
			'className'    => 'Deliberation',
			'foreignKey'   => 'parent_id',
			'dependent' => false), 
                'Deliberationseance' =>array( 
                        'className'    => 'Deliberationseance',
                        'foreignKey'   => 'deliberation_id'));

        var $hasAndBelongsToMany = array(
            'Seance' 
        );

/*
 * Indique si le projet de d�lib�ration $delibId est modifiable pour $userId.
 * Attention : ne tient pas compte des droits qui sont fait dans le controller
 * En fonction de l'�tat du projet on a :
 * - le projet est refus� (etat = -1) : non modifiable
 * - le projet est en cours de r�daction (etat = 0) :
 *   + l'utilisateur connect� est le r�dacteur du projet : modifiable
 *   + l'utilisateur connect� n'est pas le r�dacteur du projet : non modifiable
 *  - le projet est en cours de validation (etat = 1) :
 *    + l'utilisateur connect� n'est pas dans le circuit de validation : non modifiable
 *    + l'utilisateur connect� est dans le circuit de validation :
 *      * il a d�ja valid� le projet : non modifiable
 *      * c'est � son tour de traiter le projet : modifiable
 *      * son tour n'est pas encore pass� : modifiable
 *  - le projet est valid� (etat = 2) : non modifiable
 *  - le projet a �t� vot� (etat = 3 ou 4) : non modifiable
 *  - le projet a �t� envoy� (etat = 5) : non modifiable
 *  - le projet a recu un avis (avis = 1 ou 2) : non modifiable
 */
	function estModifiable($delibId, $userId, $canEdit=false) {
		/* lecture en base */
		$delib = $this->find('first', array('conditions' => array('Deliberation.id' => $delibId),
                                                    'recursive'  => '-1',
                                                    'fields'     => array('etat', 'redacteur_id', 'signee')));
		if (empty($delib)) return false;
                if ($delib['Deliberation']['signee'] == 1)
                    return false;

		/* traitement en fonction de l'�tat */
		switch($delib['Deliberation']['etat']) {
		case -1 :
		case 2 : 
                        $ret =  $canEdit;
                        break;
		case 3 :
                        $ret =  $canEdit;
                        break;
		case 4 :
                        $ret =  $canEdit;
                        break;
		case 5 :
			$ret = false;
			break;
		case 0 :
			$ret = ($delib['Deliberation']['redacteur_id'] == $userId);
			break;
		case 1 :
				$ret = ($this->Traitement->positionTrigger($userId, $delibId) > -1);
			break;
		}

		return $ret;
	}
/*
        function saveSeances($delib_id, $seances) {
            foreach($seances as $key=>$seance_id) {
                $this->Seance->DeliberationsSeance->deliberation_id = $delib_id;
                $this->Seance->DeliberationsSeance->seance_id = $seance_id;
                if (!$this->Seance->DeliberationsSeance->save())
                    die('toto');
            }
        } 
*/

/*
 * retourne le libell� correspondant � l'�tat $etat des projets et d�lib�rations
 * si $codesSpeciaux = true, retourne les libell�s avec les codes sp�ciaux des accents
 * si $codesSpeciaux = false, retourne les libell�s sans les accents (listes)
 */
	function libelleEtat($etat, $codesSpeciaux=false) {
 		switch($etat) {
                case -1 :
                        return $codesSpeciaux ? 'Version&eacute;' : 'Versionn�';
                        break;
		case -1 :
			return $codesSpeciaux ? 'Refus&eacute;' : 'Refus�';
			break;
		case 0 :
			return $codesSpeciaux ? 'En cours de r&eacute;daction' : 'En cours de r�daction';
			break;
		case 1:
			return $codesSpeciaux ? 'En cours d\'&eacute;laboration et de validation' : 'En cours d\'�laboration et de validation';
			break;
		case 2:
			return $codesSpeciaux ? 'Valid&eacute;' : 'Valid�';
			break;
		case 3:
			return $codesSpeciaux ? 'Vot&eacute; et adopt&eacute;' : 'Vot� et adopt�';
			break;
		case 4:
			return $codesSpeciaux ? 'Vot&eacute; et non adopt&eacute;' : 'Vot� et non adopt�';
			break;
		case 5:
			return $codesSpeciaux ? 'Transmis au contr&ocirc;le de l&eacute;galit&eacute;' : 'Transmis au contr�le de l�galit�';
			break;
		}
	}

	function generateListEtat() {
		$ret = array();
		for($i=-1; $i <= 5; $i++) $ret[$i] = $this->libelleEtat($i, false);
		return $ret;
	}

	function getCurrentPosition($id){
		$delib = $this->find("Deliberation.id = $id", 'position', null, -1);
		return  $delib['Deliberation']['position'];
	}

	function getCurrentSeances($id, $retourDetaillee) {
            $this->Behaviors->attach('Containable');
            $delib = $this->find('first', array('conditions' => array("Deliberation.id" => $id),
                                                'fields'     => 'Deliberation.id', 
                                                'contain'    => 'Seance'));
            if ( $retourDetaillee) 
                return $delib['Seance'];
            else {
                $seances = array();
                foreach($delib['Seance'] as $seance) 
                    $seances[] =  $seance['id'];
                return $seances;
            }
	}

	function getLastPosition($seance_id) {
		return $this->findCount("seance_id =$seance_id AND (etat != -1 )") + 1;
	}

        function isFirstDelib($delib_id, $seance_id) {
            $position = $this->getPosition($delib_id, $seance_id);
            return ($position == 1);
        }

	function changeClassification($delib_id, $classification){
            $this->id = $delib_id;
            $this->saveField('num_pref', $classification);
	}

        function changeDateAR($delib_id, $dateAR){
            $this->id = $delib_id;
            $this->saveField('dateAR', $dateAR);
        }

        function getModelId($delib_id, $seance_id) {
             $this->Seance->Behaviors->attach('Containable');
             $data = $this->find('first', array('conditions' => array('Deliberation.id' => $delib_id), 
                                                'recursive'  => -1, 
                                                'fields'     => array('Deliberation.id', 'Deliberation.etat')));
             $seance = $this->Seance->find('first', array(
                                           'conditions' => array('Seance.id' => $seance_id), 
                                           'fields'     => array('Seance.id'),
                                           'contain'    => array('Typeseance.modelprojet_id', 'Typeseance.modeldeliberation_id') ));

             if (!empty($seance)){
                 if ($data['Deliberation']['etat']<3)
                     return $seance['Typeseance']['modelprojet_id'];
                 else
                     return $seance['Typeseance']['modeldeliberation_id'];
             }
             else {
                  return 1;
             }
        }

	function refusDossier($id) {
		// lecture en base de donn�es
		$this->Behaviors->attach('Containable');
		$delib=$this->find('first', array(
			'contain' => array('Annex', 'Infosup'),
			'conditions' => array('Deliberation.id'=>$id)));

		// maj de l'etat de la delib dans la table deliberations
		$delib['Deliberation']['etat']=-1; //etat -1 : refuse
		// Retour de la position a 0 pour ne pas qu'il y ait de confusion
		//$delib['Deliberation']['position']=0;
		$delib['Deliberation']['id']=$id;
		$this->save($delib['Deliberation']);

		// cr�ation de la nouvelle version
		$this->create();
		$delib['Deliberation']['id']='';
		$delib['Deliberation']['etat']=0;
		$delib['Deliberation']['anterieure_id']=$id;
		$delib['Deliberation']['date_envoi']=0;
		$delib['Deliberation']['created']=date('Y-m-d H:i:s', time());
		$delib['Deliberation']['modified']=date('Y-m-d H:i:s', time());
		$this->save($delib['Deliberation']);
		$delib_id = $this->id;
                $this->copyPositionsDelibs($id,  $delib_id );

		// copie des annexes du projet refus� vers le nouveau projet
		$annexes = $delib['Annex'];
		foreach($annexes as $annexe) {
			$tmp['Annex']= $annexe;
			$tmp['Annex']['id']=null;
			$tmp['Annex']['foreign_key']= $delib_id;
			$this->Annex->save( $tmp, false);
		}

		// copie des infos suppl�mentaires du projet refus� vers le nouveau projet
		$infoSups = $delib['Infosup'];
		foreach($infoSups as $infoSup) {
			$infoSup['id'] = null;
			$infoSup['foreign_key'] = $delib_id;
			$infoSup['model'] = 'Deliberation';
			$this->Infosup->save($infoSup, false);
		}

		// copie des d�lib�rations rattach�es vers le nouveau projet
		$delibRattachees = $this->find('all', array(
			'contain' => array('Annex'),
			'conditions'=>array('Deliberation.parent_id'=>$id)));
		foreach($delibRattachees as $delibRattachee) {
			// maj de l'etat de la delib dans la table deliberations
			$delibRattachee['Deliberation']['etat']=-1; //etat -1 : refuse
			// Retour de la position a 0 pour ne pas qu'il y ait de confusion
			//$delibRattachee['Deliberation']['position']=0;
			$this->save($delibRattachee['Deliberation']);
	
			// cr�ation de la nouvelle version
			$this->create();
			$delibRattachee['Deliberation']['id']='';
			$delibRattachee['Deliberation']['parent_id']=$delib_id;
			$delibRattachee['Deliberation']['etat']=0;
			$delibRattachee['Deliberation']['anterieure_id']=0;
			$delibRattachee['Deliberation']['date_envoi']=0;
	//		$delibRattachee['Deliberation']['circuit_id']=0;
			$delibRattachee['Deliberation']['created']=date('Y-m-d H:i:s', time());
			$delibRattachee['Deliberation']['modified']=date('Y-m-d H:i:s', time());
			$this->save($delibRattachee['Deliberation']);
			$delibRattachee_id = $this->id;
	
			// copie des annexes du projet refus� vers le nouveau projet
			$annexes = $delibRattachee['Annex'];
			foreach($annexes as $annexe) {
				$tmp['Annex']= $annexe;
				$tmp['Annex']['id']=null;
				$tmp['Annex']['model']='Deliberation';
				$tmp['Annex']['foreign_key']= $delibRattachee_id ;
				$this->Annex->save( $tmp, false);
			}
		}
	}

        function canSaveSeances(){
            $result = false;
            $nb_seances_deliberante = 0;
            $this->Seance->Behaviors->attach('Containable');
            $seances = $this->Seance->find('all', array('conditions' => array('Seance.id' => $this->data['Deliberation']['seance_id']),
                                                        'fields'     => array('Seance.id'),
                                                        'contain'    => array('Typeseance.action')));
            foreach($seances as $seance) {
                if($seance['Typeseance']['action'] == 0)
                    $nb_seances_deliberante ++;
            }
            if ($nb_seances_deliberante > 1)
                return false;
            else 
                return true;
           //return $this->Seance->NaturecanSave($this->data['Deliberation']['seance_id'], $this->data['Deliberation']['nature_id']);
       }
 
       function canSaveNature() {
           if (isset($this->data['Deliberation']['seance_id']) && (!empty($this->data['Deliberation']['seance_id']))) {
               foreach ($this->data['Deliberation']['seance_id'] as $key => $seance_id) {
                    $result = $this->Seance->NaturecanSave($seance_id, $this->data['Deliberation']['nature_id']);
                    if ($result == false)
                        return false;
               }
               return true;
           }
           else
               return true;
       }

       function genererRecherche($projets, $model_id=1, $format=0, $multiSeances=array(), $conditions=array() ){
            include_once ('vendors/GEDOOo/phpgedooo/GDO_Utility.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FieldType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_ContentType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_IterationType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_PartType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_FusionType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixType.class');
            include_once ('vendors/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
	    include_once ('vendors/GEDOOo/phpgedooo/GDO_AxisTitleType.class');

	    include_once ('controllers/components/conversion.php');
            $this->Conversion = new ConversionComponent;

            if ($format == 0) {
                $sMimeType = "application/pdf";
                $format    = "pdf";
            }
            elseif ($format ==1) {
                $sMimeType = "application/vnd.oasis.opendocument.text";
                $format    = "odt";
            }
	    $dyn_path = "/files/generee/deliberations/";
	    $nomFichier = "recherche";
            $path = WEBROOT_PATH.$dyn_path;
            if (!file_exists($path))
                mkdir($path);

            $content = $this->Seance->Typeseance->Modelprojet->find('first', array('conditions' => array('id' => $model_id),
                                                                                   'fields'     => array('content'),
                                                                                   'recursive'  => -1));
            $oTemplate = new GDO_ContentType("",
                                "modele.odt",
                                "application/vnd.oasis.opendocument.text",
                                "binary",
                                $content['Modelprojet']['content']);
            $oMainPart = new GDO_PartType();

            if (empty($multiSeances)) {
                $nbProjets = count($projets);
                if ($nbProjets > 1) {
                    $i =0;
                    $blocProjets = new GDO_IterationType("Projets");
                }
                foreach ($projets as $projet) {
                    $oDevPart = new GDO_PartType();
                    $this->makeBalisesProjet($projet,  $oDevPart);
                    if ($nbProjets > 1)
                        $blocProjets->addPart($oDevPart);
                }
                if ( $nbProjets > 1)
                    $oMainPart->addElement($blocProjets);
                else
                    $oMainPart =  $oDevPart;
            }
            else {
                $seances = new GDO_IterationType("Seances");
                foreach($multiSeances as $key => $seance_id) 
                    $seances->addPart($this->Seance->makeBalise($seance_id, null, true, $conditions));
                $oMainPart->addElement($seances);
            }

            $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
            $oFusion->process();

	    $oFusion->SendContentToFile($path.$nomFichier.".odt");      
            $content = $this->Conversion->convertirFichier($path.$nomFichier.".odt", $format);

            header("Content-type: $sMimeType");
            header("Content-Disposition: attachment; filename=recherche.$format");
            die($content);

           

        }

        function makeBalisesProjet ($delib, &$oMainPart, $exceptSeance=false, $seance_id=null)  {
           include_once (ROOT.DS.APP_DIR.DS.'controllers/components/gedooo.php');
	   include_once (ROOT.DS.APP_DIR.DS.'controllers/components/date.php');
	   include_once (ROOT.DS.APP_DIR.DS.'controllers/components/conversion.php');
           include_once (ROOT.DS.APP_DIR.DS.'vendors/GEDOOo/phpgedooo/GDO_Utility.class');
           $isDelib = ($delib['Deliberation']['etat'] >= 3);
           $u = new GDO_Utility();

           $this->Conversion = new ConversionComponent;
           $this->Date = new DateComponent;
           $this->Gedooo = new GedoooComponent;

           $dyn_path = "/files/generee/projet/".$delib['Deliberation']['id']."/";
           $path = WEBROOT_PATH.$dyn_path;

           // It�ration sur les s�ances
           if (!$exceptSeance) {
               $delibseances = $this->getSeancesid($delib['Deliberation']['id']);
               $oMainPart->addElement(new GDO_FieldType('nombre_seance', count($delibseances), 'text'));
               if (count($delibseances) == 1) {
                   $this->Seance->makeBalise($delibseances[0], $oMainPart);
                   $position = $this->getPosition($delib['Deliberation']['id'], $delibseances[0]);
                   $oMainPart->addElement(new GDO_FieldType('position_projet', $position, 'text'));
                   $seances = new GDO_IterationType("Seances");
                   $seances->addPart($this->Seance->makeBalise($delibseances[0]));
                   $oMainPart->addElement($seances);
               }
               elseif(count($delibseances) >1) {
                   $seance_deliberante = $this->Seance->getSeanceDeliberante($delibseances);
                    
                   $this->Seance->makeBalise($seance_deliberante, $oMainPart);

                   $seances = new GDO_IterationType("Seances");
                   foreach($delibseances as $key => $seance_id) {
                       $seances->addPart($this->Seance->makeBalise($seance_id));
                   }                  
                   $oMainPart->addElement($seances);
               }
           }
           if ($seance_id != null) { 
               $position = $this->getPosition($delib['Deliberation']['id'], $seance_id);
               $oMainPart->addElement(new GDO_FieldType('position_projet', $position, 'text'));
           }
           $oMainPart->addElement(new GDO_FieldType('titre_projet',   $this->_encode($delib['Deliberation']['titre']),    'text'));
	   $oMainPart->addElement(new GDO_FieldType('objet_projet',   $this->_encode($delib['Deliberation']['objet']),     'text'));
	   $oMainPart->addElement(new GDO_FieldType('libelle_projet', $this->_encode($delib['Deliberation']['objet']),      'text'));
           $oMainPart->addElement(new GDO_FieldType('objet_delib',    $this->_encode($delib['Deliberation']['objet_delib']), 'text'));
	   $oMainPart->addElement(new GDO_FieldType('libelle_delib',  $this->_encode($delib['Deliberation']['objet_delib']), 'text'));

           $oMainPart->addElement(new GDO_FieldType('identifiant_projet',          utf8_encode($delib['Deliberation']['id']),       'text'));
           $oMainPart->addElement(new GDO_FieldType('numero_deliberation',         utf8_encode($delib['Deliberation']['num_delib']),'text'));
           $oMainPart->addElement(new GDO_FieldType('classification_deliberation', utf8_encode($delib['Deliberation']['num_pref']), 'text'));

           $this->Service->makeBalise($oMainPart, $delib['Deliberation']['service_id']);
           // Informations sur la nature
           $this->Nature->makeBalise($oMainPart, $delib['Deliberation']['nature_id']);
           // Informations sur le th�me
           $this->Theme->makeBalise($oMainPart, $delib['Deliberation']['theme_id']);
           // Informations sur le rapporteur
           $this->Rapporteur->makeBalise($oMainPart, $delib['Deliberation']['rapporteur_id']);
           // Informations sur le r�dacteur
           $this->Redacteur->makeBalise($oMainPart, $delib['Deliberation']['redacteur_id']);

           // Informations sur la d�lib�ration
            
	   $nb_votant = $delib['Deliberation']['vote_nb_oui']+$delib['Deliberation']['vote_nb_abstention']+$delib['Deliberation']['vote_nb_non'];
	   if (($delib['Deliberation']['etat'] == 3 ) &&  ($delib['Deliberation']['vote_nb_oui']==0 )) 
               $oMainPart->addElement(new GDO_FieldType('acte_adopte',  '1', 'text'));
           else {
               $oMainPart->addElement(new GDO_FieldType('acte_adopte',  '0', 'text'));
               $oMainPart->addElement(new GDO_FieldType('nombre_pour',  utf8_encode($delib['Deliberation']['vote_nb_oui'])   , 'text'));
               $oMainPart->addElement(new GDO_FieldType('nombre_abstention', utf8_encode( $delib['Deliberation']['vote_nb_abstention']), 'text'));
               $oMainPart->addElement(new GDO_FieldType('nombre_contre',  utf8_encode($delib['Deliberation']['vote_nb_non']), 'text'));
	       $oMainPart->addElement(new GDO_FieldType('nombre_sans_participation', utf8_encode( $delib['Deliberation']['vote_nb_retrait']), 'text'));           }
           $oMainPart->addElement(new GDO_FieldType('nombre_votant', $nb_votant, 'text'));
           $oMainPart->addElement(new GDO_FieldType('date_reception',  utf8_encode($delib['Deliberation']['dateAR']), 'text'));

           if (isset($delib['Deliberation']['vote_commentaire'])) {
                   $filename = $path."commentaire_vote.html";
                   $vote_commentaire = "<html><head></head><body><p>".nl2br($delib['Deliberation']['vote_commentaire'])."</p></body></html>";
                   $filepath_comm = $this->Gedooo->createFile($path, "commentaire.html",  $vote_commentaire);
                   $content = $this->Conversion->convertirFichier($filepath_comm, "odt");
		   $oMainPart->addElement(new GDO_ContentType('commentaire_vote', 
                                                              'commentaire.odt', 
                                                              'application/vnd.oasis.opendocument.text', 
                                                              'binary', 
                                                              $content));
           }
                  
           $coms = $this->Commentaire->find('all', 
                                            array('conditions' => array('Commentaire.delib_id' => $delib['Deliberation']['id']),
                                                  'fields'     => array('texte', 'commentaire_auto'),
                                                  'recursive'  => -1));

           if (!empty($coms)) {
               $commentaires = new GDO_IterationType("Commentaires");
               foreach($coms as $commentaire) {
                   $oDevPart = new GDO_PartType();
	           if ($commentaire['Commentaire']['commentaire_auto']==0){
                       $oDevPart->addElement(new GDO_FieldType("texte_commentaire", utf8_encode($commentaire['Commentaire']['texte']), "text"));
                       $commentaires->addPart($oDevPart);
	           }
               }
               @$oMainPart->addElement($commentaires);

               $avisCommission = new GDO_IterationType("AvisCommission");
               foreach($coms as $commentaire) {
                   $oDevPart = new GDO_PartType();
		   if ($commentaire['Commentaire']['commentaire_auto']==1) {
                       $oDevPart->addElement(new GDO_FieldType("avis", utf8_encode($commentaire['Commentaire']['texte']), "text"));
                       $avisCommission->addPart($oDevPart);
		   }
               }
               @$oMainPart->addElement($avisCommission);
          }

               $historik = $this->Historique->find('all',
                                                   array('conditions' => array('Historique.delib_id' => $delib['Deliberation']['id']), 
                                                         'fields'     => array('commentaire'),
                                                         'recursive'  => -1));

               if (!empty($historik)) {
                   @$historique =  new GDO_IterationType("Historique");
	           foreach($historik as $histo) {
                       $oDevPart = new GDO_PartType();
                       $oDevPart->addElement(new GDO_FieldType("log", utf8_encode($histo['Historique']['commentaire']), "text"));
                       $historique->addPart($oDevPart);
                   }
                   @$oMainPart->addElement($historique);
               } 

               $infosup = $this->Infosup->find('all',
                                               array('conditions' => array('Infosup.foreign_key' => $delib['Deliberation']['id'],
                                                                           'Infosup.model'       => 'Deliberation'), 
                                                     'recursive'  => -1));

               if (!empty($infosup['Infosup'])) {
                   foreach($infosup['Infosup'] as  $champs)
                       $oMainPart->addElement($this->_addField($champs, $delib['Deliberation']['id'], 'Deliberation'));
               }
               else {
                   $defs = $this->Infosup->Infosupdef->find('all', array('conditions'=>array('model' => 'Deliberation'), 'recursive' => -1));
                   foreach($defs as $def) {
                        $oMainPart->addElement(new GDO_FieldType($def['Infosupdef']['code'],  utf8_encode(' '), 'text')) ;
                   }
               }

               $multidelibs = $this->find('first', array('conditions' => array('Deliberation.parent_id' => $delib['Deliberation']['id']),
                                                        'fields'     => array('id', 'objet')));
               @$Multi =  new GDO_IterationType("Deliberations");
               if (!empty($multidelibs['Multidelib'])) {
                   foreach($multidelibs['Multidelib'] as $multidelib ){
                       $oDevPart = new GDO_PartType();
                       $oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", utf8_encode($multidelib['objet']), "text"));
                       $oDevPart->addElement(new GDO_FieldType("id_multi_delib",      utf8_encode($multidelib['id']),    "text"));
                       $Multi->addPart($oDevPart);
                   }
               }
               else {
                       $oDevPart = new GDO_PartType();
                       $oDevPart->addElement(new GDO_FieldType("libelle_multi_delib", " ", "text"));
                       $oDevPart->addElement(new GDO_FieldType("id_multi_delib",      " ",    "text"));
                       $Multi->addPart($oDevPart);
               }
               @$oMainPart->addElement($Multi);

               if (Configure::read('GENERER_DOC_SIMPLE')) {
                   if (isset($delib['Deliberation']['texte_projet'])) {
                       $filename = $path."texte_projet.html";
                       $delib['Deliberation']['texte_projet'] = $this->_url2pathImage($delib['Deliberation']['texte_projet']);
                       $this->Gedooo->createFile($path, "texte_projet.html",  $delib['Deliberation']['texte_projet']);
                       $content = $this->Conversion->convertirFichier($filename, "odt");
                       $oMainPart->addElement(new GDO_ContentType('texte_projet', 'texte_projet.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
                   }
                   if (isset($delib['Deliberation']['texte_synthese'])) {
                       $filename = $path."texte_synthese.html";
                       $this->Gedooo->createFile($path, "texte_synthese.html",  $delib['Deliberation']['texte_synthese']);
                       $content = $this->Conversion->convertirFichier($filename, "odt");
                       $oMainPart->addElement(new GDO_ContentType('note_synthese', 'texte_synthese.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
                   }
                   if (isset($delib['Deliberation']['deliberation'])) { 
                       $filename = $path."texte_deliberation.html";
                       $this->Gedooo->createFile($path, "texte_deliberation.html",  $delib['Deliberation']['deliberation']);
                       $content = $this->Conversion->convertirFichier($filename, "odt");
                       $oMainPart->addElement(new GDO_ContentType('texte_deliberation', 'deliberation.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
                   }
                   if (isset($delib['Deliberation']['debat'])) { 
                       $filename = $path."debat_deliberation.html";
                       $this->Gedooo->createFile($path, "debat_deliberation.html",  $delib['Deliberation']['debat']);
                       $content = $this->Conversion->convertirFichier($filename, "odt");
                       $oMainPart->addElement(new GDO_ContentType('debat_deliberation', 'debat.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content));
                   }
                   if (isset($delib['Deliberation']['commission'])) {
                       $filename = $path."commission.html";
                       $this->Gedooo->createFile($path, "commission.html",  $delib['Deliberation']['commission']);
                       $content = $this->Conversion->convertirFichier($filename, "odt");
                       $oMainPart->addElement(new GDO_ContentType('debat_commission', 'commission.odt', 'application/vnd.oasis.opendocument.text', 'binary', $content)); 
                   }
               }
               else {

                   if (!$this->Gedooo->checkPath($path))
                       die("Webdelib ne peut pas ecrire dans le repertoire : $path");

                   $urlWebroot =  'http://'.$_SERVER['HTTP_HOST'].$dyn_path;
                   if (!empty($delib['Deliberation']['texte_projet'])) {
                       $oMainPart->addElement(new GDO_ContentType('texte_projet', 
                                                                  'text_projet.odt' ,
                                                                  'application/vnd.oasis.opendocument.text',  
                                                                  'binary', 
                                                                  $delib['Deliberation']['texte_projet']));
                  }
                  if (!empty($delib['Deliberation']['deliberation'])) {
                      $oMainPart->addElement(new GDO_ContentType('texte_deliberation', 
                                                                 'td.odt', 
                                                                 'application/vnd.oasis.opendocument.text' ,   
                                                                 'binary', 
                                                                  $delib['Deliberation']['deliberation']));
                  } 
                  if (!empty($delib['Deliberation']['texte_synthese'])) {
                      $oMainPart->addElement(new GDO_ContentType('note_synthese', 
                                                                 'ns.odt', 
                                                                 'application/vnd.oasis.opendocument.text' , 
                                                                 'binary', 
                                                                  $delib['Deliberation']['texte_synthese']));
                   }
                   if (!empty($delib['Deliberation']['debat'])) {
                       $oMainPart->addElement(new GDO_ContentType('debat_deliberation', 
                                                                  'debat.odt',  
                                                                  'application/vnd.oasis.opendocument.text' , 
                                                                  'binary', 
                                                                  $delib['Deliberation']['debat']));
                   }
                   if (!empty($delib['Deliberation']['commission'])) {
                       $oMainPart->addElement(new GDO_ContentType('debat_commission', 
                                                                  'debat_commission.odt',  
                                                                  'application/vnd.oasis.opendocument.text', 
                                                                  'binary', 
                                                                  $delib['Deliberation']['commission']));
                   }

	       }

               // $annexe_ids = $this->Annex->getAnnexesIFromDelibId($delib['Deliberation']['id'], 0, 1);
               $annexe_ids = array();
               $anns = $this->Annex->find('all', array('conditions' =>  array(
                                                           'Annex.foreign_key' => $delib['Deliberation']['id'],
                                                           'Annex.filetype like' => '%vnd.oasis.opendocument.text%'),
                                                       'fields' => array('id'),
                                                       'recursive' => -1));
               foreach( $anns as $ann )
                   $annexe_ids[] = $ann['Annex']['id'];
               $oMainPart->addElement(new GDO_FieldType('nombre_annexe', count($annexe_ids), 'text'));

               @$annexes =  new GDO_IterationType("Annexes");
               foreach($annexe_ids as $key => $annexe_id) {
                   $annexe = $this->Annex->find('first', array ('conditions' => array('Annex.id' => $annexe_id),
                                                               'recursive'  => -1));
                   if (($annexe['Annex']['filetype'] == "application/vnd.oasis.opendocument.text")) {  
                       $oDevPart = new GDO_PartType();
                       $oDevPart->addElement(new GDO_FieldType('nom_fichier',  utf8_encode($annexe['Annex']['filename']), 'text'));
                       $oDevPart->addElement(new GDO_FieldType('titre_annexe', utf8_encode($annexe['Annex']['titre']), 'text'));
                       $oDevPart->addElement(new GDO_ContentType('fichier',    utf8_encode($annexe['Annex']['filename']),  
                                                                                           'application/vnd.oasis.opendocument.text', 
                                                                                           'binary',
                                                                                           $annexe['Annex']['data']));
                       $annexes->addPart($oDevPart);
                   }
               }
               @$oMainPart->addElement($annexes);

               //LISTE DES PRESENCES...
               $this->Listepresence->Behaviors->attach('Containable');
               $this->Vote->Behaviors->attach('Containable');
               $acteurs_presents = array();
               $acteurs_absents = array();
               $acteurs_remplaces = array();
               $acteurs_contre = array();
               $acteurs_pour = array();
               $acteurs_abstention = array();
               $acteurs_sans_participation = array();

               $acteurs = $this->Listepresence->find('all', 
                                                     array ('conditions' => array("delib_id" => $delib['Deliberation']['id']),
                                                            'contain'   => array('Acteur', 'Mandataire'),           
                                                            'order' => 'Acteur.position ASC'));
               if (!empty($acteurs)) {
                     foreach($acteurs as $acteur) {
                         if ( $acteur['Listepresence']['present'] == 1 ){
                             $acteurs_presents[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                                                     'prenom_acteur' => $acteur['Acteur']['prenom'],
                                                     'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                                     'titre_acteur'=> $acteur['Acteur']['titre'],
                                                     'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                                     'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                                     'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                                     'cp_acteur' => $acteur['Acteur']['cp'],
                                                     'ville_acteur' => $acteur['Acteur']['ville'],
                                                     'email_acteur' => $acteur['Acteur']['email'],
                                                     'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                                     'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                                     'note_acteur' => $acteur['Acteur']['note']);
                         }
                         elseif(($acteur['Listepresence']['present'] == 0) AND ($acteur['Listepresence']['mandataire']==0)) {
                             $acteurs_absents[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                                                     'prenom_acteur' => $acteur['Acteur']['prenom'],
                                                     'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                                     'titre_acteur'=> $acteur['Acteur']['titre'],
                                                     'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                                     'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                                     'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                                     'cp_acteur' => $acteur['Acteur']['cp'],
                                                     'ville_acteur' => $acteur['Acteur']['ville'],
                                                     'email_acteur' => $acteur['Acteur']['email'],
                                                     'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                                     'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                                     'note_acteur' => $acteur['Acteur']['note']);

                         }
                        elseif(($acteur['Listepresence']['present'] == 0) AND ($acteur['Listepresence']['mandataire']!=0)) {
                             $acteurs_remplaces[] = array(
                                                     'nom_acteur' => $acteur['Acteur']['nom'],
                                                     'prenom_acteur' => $acteur['Acteur']['prenom'],
                                                     'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                                     'titre_acteur'=> $acteur['Acteur']['titre'],
                                                     'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                                     'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                                     'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                                     'cp_acteur' => $acteur['Acteur']['cp'],
                                                     'ville_acteur' => $acteur['Acteur']['ville'],
                                                     'email_acteur' => $acteur['Acteur']['email'],
                                                     'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                                     'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                                     'note_acteur' => $acteur['Acteur']['note'],
                                                     'nom_mandate' => $acteur['Mandataire']['nom'],
                                                     'prenom_mandate' => $acteur['Mandataire']['prenom'],
                                                     'salutation_mandate'=> $acteur['Mandataire']['salutation'],
                                                     'titre_mandate'=> $acteur['Mandataire']['titre'],
                                                     'date_naissance_mandate' => $acteur['Mandataire']['date_naissance'],
                                                     'adresse1_mandate' => $acteur['Mandataire']['adresse1'],
                                                     'adresse2_mandate' => $acteur['Mandataire']['adresse2'],
                                                     'cp_mandate' => $acteur['Mandataire']['cp'],
                                                     'ville_mandate' => $acteur['Mandataire']['ville'],
                                                     'email_mandate' => $acteur['Mandataire']['email'],
                                                     'telfixe_mandate' => $acteur['Mandataire']['telfixe'],
                                                     'telmobile_mandate' => $acteur['Mandataire']['telmobile'],
                                                     'note_mandate' => $acteur['Mandataire']['note']);
                         }
                    }
                }

               $acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                                                                               "Vote.resultat"=> 2),
                                                          'contain'   => array('Acteur'),
                                                          'order' => 'Acteur.position ASC'));

                foreach ($acteurs as $acteur) {
                     $acteurs_contre[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                                               'prenom_acteur' => $acteur['Acteur']['prenom'],
                                               'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                               'titre_acteur'=> $acteur['Acteur']['titre'],
                                               'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                               'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                               'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                               'cp_acteur' => $acteur['Acteur']['cp'],
                                               'ville_acteur' => $acteur['Acteur']['ville'],
                                               'email_acteur' => $acteur['Acteur']['email'],
                                               'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                               'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                               'note_acteur' => $acteur['Acteur']['note']);
                }
             
               $acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                                                                               "Vote.resultat"=> 3),
                                                          'contain'   => array('Acteur'),
                                                          'order' => 'Acteur.position ASC'));

                foreach ($acteurs as $acteur) {
                       $acteurs_pour[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                                               'prenom_acteur' => $acteur['Acteur']['prenom'],
                                               'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                               'titre_acteur'=> $acteur['Acteur']['titre'],
                                               'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                               'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                               'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                               'cp_acteur' => $acteur['Acteur']['cp'],
                                               'ville_acteur' => $acteur['Acteur']['ville'],
                                               'email_acteur' => $acteur['Acteur']['email'],
                                               'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                               'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                               'note_acteur' => $acteur['Acteur']['note']);
               }
             
               $acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                                                                               "Vote.resultat"=> 4),
                                                          'contain'   => array('Acteur'),
                                                          'order' => 'Acteur.position ASC'));

               foreach ($acteurs as $acteur) {
                 $acteurs_abstention[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                                               'prenom_acteur' => $acteur['Acteur']['prenom'],
                                               'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                               'titre_acteur'=> $acteur['Acteur']['titre'],
                                               'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                               'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                               'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                               'cp_acteur' => $acteur['Acteur']['cp'],
                                               'ville_acteur' => $acteur['Acteur']['ville'],
                                               'email_acteur' => $acteur['Acteur']['email'],
                                               'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                               'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                               'note_acteur' => $acteur['Acteur']['note']);
               }
               $acteurs = $this->Vote->find('all', array ('conditions' => array("delib_id" => $delib['Deliberation']['id'],
                                                                               "Vote.resultat"=> 5),
                                                          'contain'   => array('Acteur'),
                                                          'order' => 'Acteur.position ASC'));

               foreach ($acteurs as $acteur) {
                 $acteurs_sans_participation[] = array('nom_acteur' => $acteur['Acteur']['nom'],
                                               'prenom_acteur' => $acteur['Acteur']['prenom'],
                                               'salutation_acteur'=> $acteur['Acteur']['salutation'],
                                               'titre_acteur'=> $acteur['Acteur']['titre'],
                                               'date_naissance_acteur' => $acteur['Acteur']['date_naissance'],
                                               'adresse1_acteur' => $acteur['Acteur']['adresse1'],
                                               'adresse2_acteur' => $acteur['Acteur']['adresse2'],
                                               'cp_acteur' => $acteur['Acteur']['cp'],
                                               'ville_acteur' => $acteur['Acteur']['ville'],
                                               'email_acteur' => $acteur['Acteur']['email'],
                                               'telfixe_acteur' => $acteur['Acteur']['telfixe'],
                                               'telmobile_acteur' => $acteur['Acteur']['telmobile'],
                                               'note_acteur' => $acteur['Acteur']['note']);
               }

               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursPresents",   $acteurs_presents, false, '_present'));
               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursAbsents",    $acteurs_absents, false, '_absent'));
               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursMandates",   $acteurs_remplaces, true, '_mandataire'));
               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursContre",     $acteurs_contre, false, '_contre'));
               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursPour",       $acteurs_pour, false, '_pour'));
               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursAbstention", $acteurs_abstention, false, '_abstention'));
               $oMainPart->addElement($this->_makeBlocsActeurs("ActeursSansParticipation", $acteurs_sans_participation, false, '_sans_participation'));
        }

        function _makeBlocsActeurs ($nomBloc, $listActeur, $isMandate, $type) {
	    $acteurs = new GDO_IterationType("$nomBloc");
            
            if ( count($listActeur) == 0 ) {
              $oDevPart = new GDO_PartType();
	      $oDevPart->addElement(new GDO_FieldType("nombre_acteur".$type,        '0', "text"));
             
              $oDevPart->addElement(new GDO_FieldType("nom_acteur".$type,            ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type,         ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,     ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("titre_acteur".$type,          ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, ' ', "date"));
              $oDevPart->addElement(new GDO_FieldType("adresse1_acteur".$type,       ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("adresse2_acteur".$type,       ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("cp_acteur".$type,             ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("ville_acteur".$type,          ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("email_acteur".$type,          ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("telfixe_acteur".$type,        ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("telmobile_acteur".$type,      ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType("note_acteur".$type,           ' ', "text"));

              $oDevPart->addElement(new GDO_FieldType('nom_acteur_mandate',                 ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('prenom_acteur_mandate',              ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('salutation_acteur_mandate',          ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('titre_acteur_mandate',               ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('date_naissance_acteur_mandate',      ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('adresse1_acteur_mandate',            ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('adresse2_acteur_mandate',            ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('cp_acteur_mandate',                  ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('ville_acteur_mandate',               ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('email_acteur_mandate',               ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('telfixe_acteur_mandate',             ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('telmobile_acteur_mandate',           ' ', "text"));
              $oDevPart->addElement(new GDO_FieldType('note_acteur_mandate',                ' ', "text"));
              $acteurs->addPart($oDevPart);
              return $acteurs;
            }
	    else {
                $nbre_acteurs = count($listActeur);
             foreach($listActeur as $acteur) {
                $oDevPart = new GDO_PartType();
	        $oDevPart->addElement(new GDO_FieldType("nombre_acteur".$type, $nbre_acteurs , "text"));
                $oDevPart->addElement(new GDO_FieldType("nom_acteur".$type, utf8_encode($acteur['nom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("prenom_acteur".$type, utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("salutation_acteur".$type,utf8_encode($acteur['salutation_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("titre_acteur".$type, utf8_encode($acteur['titre_acteur']), "text"));
                if ($acteur['date_naissance_acteur'] != null)
                    $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type,  $this->Date->frDate($acteur['date_naissance_acteur']), "date"));
                else
                      $oDevPart->addElement(new GDO_FieldType("date_naissance_acteur".$type, '', "date"));

                $oDevPart->addElement(new GDO_FieldType("adresse1_acteur".$type, utf8_encode($acteur['adresse1_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("adresse2_acteur".$type, utf8_encode($acteur['adresse2_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("cp_acteur".$type, utf8_encode($acteur['cp_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("ville_acteur".$type, utf8_encode($acteur['ville_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("email_acteur".$type, utf8_encode($acteur['email_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("telfixe_acteur".$type,utf8_encode($acteur['telfixe_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("telmobile_acteur".$type,  utf8_encode($acteur['prenom_acteur']), "text"));
                $oDevPart->addElement(new GDO_FieldType("note_acteur".$type, utf8_encode($acteur['note_acteur']), "text"));
                if ($isMandate) {
                    $oDevPart->addElement(new GDO_FieldType('nom_acteur_mandate', utf8_encode($acteur['nom_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('prenom_acteur_mandate', utf8_encode($acteur['prenom_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('salutation_acteur_mandate', utf8_encode($acteur['salutation_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('titre_acteur_mandate', utf8_encode($acteur['titre_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('date_naissance_acteur_mandate', utf8_encode($acteur['date_naissance_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('adresse1_acteur_mandate', utf8_encode($acteur['adresse1_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('adresse2_acteur_mandate', utf8_encode($acteur['adresse2_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('cp_acteur_mandate', utf8_encode($acteur['cp_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('ville_acteur_mandate', utf8_encode($acteur['ville_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('email_acteur_mandate', utf8_encode($acteur['email_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('telfixe_acteur_mandate', utf8_encode($acteur['telfixe_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('telmobile_acteur_mandate', utf8_encode($acteur['telmobile_mandate']), "text"));
                    $oDevPart->addElement(new GDO_FieldType('note_acteur_mandate', utf8_encode($acteur['note_mandate']), "text"));
                }
                $acteurs->addPart($oDevPart);
            }
            return $acteurs;

            }
        }

        function _addField($champs,  $id, $model='Deliberation') {
            $champs_def = $this->Infosup->Infosupdef->read(null, $champs['infosupdef_id']);

            if(($champs_def['Infosupdef']['type'] == 'list' )&&($champs['text']!= "")) {
                $tmp= $this->Infosup->Infosupdef->Infosuplistedef->find('id = '.$champs['text'], 'nom', null, -1);
                $champs['text'] = $tmp['Infosuplistedef']['nom'];
            }
            elseif (($champs_def['Infosupdef']['type'] == 'list' )&&($champs['text']== ""))
                 return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode(' '), 'text'));

            if ($champs['text'] != '') { 
                return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode($champs['text']), 'text'));
            }
	    elseif ($champs['date'] != '0000-00-00') {
                include_once ('controllers/components/date.php');
                $this->Date = new DateComponent;
		return  (new GDO_FieldType($champs_def['Infosupdef']['code'], $this->Date->frDate($champs['date']),   'date'));
             }
             elseif ($champs['file_size'] != 0 ) {
                 $name = utf8_decode(str_replace(" ", "_", $champs['file_name']));
                 return (new GDO_ContentType($champs_def['Infosupdef']['code'], $name  ,'application/vnd.oasis.opendocument.text',  'binary', utf8_decode($champs['content'])));
             }
             elseif ((!empty($champs['content'])) && ($champs['file_size']==0) ) {
                 include_once ('controllers/components/gedooo.php');
                 include_once ('controllers/components/conversion.php');

                 $this->Gedooo = new GedoooComponent;
                 $this->Conversion = new ConversionComponent; 
                 if ( $model == 'Deliberation' ) { 
                     $filename = WEBROOT_PATH."/files/generee/projet/$id/".$champs_def['Infosupdef']['code'].".html";
                     $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/projet/$id/", $champs_def['Infosupdef']['code'].".html", $champs['content']);
                     $content = $this->Conversion->convertirFichier($filename, "odt");
		     return (new GDO_ContentType($champs_def['Infosupdef']['code'], $filename, 'application/vnd.oasis.opendocument.text', 'binary', utf8_decode($content)));
                 }
		 elseif ( $model == 'Seance' ) {
                     $filename = WEBROOT_PATH."/files/generee/seance/$id/".$champs_def['Infosupdef']['code'].".html";
                     $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/seance/$id/", $champs_def['Infosupdef']['code'].".html", $champs['content']);
                     $content = $this->Conversion->convertirFichier($filename, "odt");
                     return (new GDO_ContentType($champs_def['Infosupdef']['code'], $filename, 'application/vnd.oasis.opendocument.text', 'binary', utf8_decode($content)));

                 } 
             }
            elseif  ($champs['text'] == '' )
                 return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode(' '), 'text'));
        }

        function _url2pathImage($url) {
            $content = str_replace('http://webdelib/app/', Configure::read('WEBDELIB_PATH'), $url);
            $content = str_replace( '\"', '"', $content);
            return $content;
        }

        function _encode($texte) {
            $texte = utf8_encode($texte);
            $texte = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC), $texte);
            return $texte;
        }
/**
 * op�rations post sauvegarde des d�lib�rations :
 * - gestion des s�ances et de l'ordre des projets
 * - mise � jour des d�lib�rations rattach�es
 * @param integer $delibId id du projet � traiter
 * @param integer $oldSeanceId id de la s�ance pr�c�dente avant sauvegarde
 */
function majDelibRatt($delibId, $oldSeanceId) {
	// initialisation
	$position = 0;
	$majPosition = false;
	$majFields = array(
		'nature_id', 'theme_id', 'service_id', 'redacteur_id', 'rapporteur_id',
		'seance_id', 'position',
		'titre', 'num_pref', 'etat',
		'texte_projet', 'texte_projet_size', 'texte_projet_type', 'texte_projet_name',
		'texte_synthese', 'texte_synthese_size', 'texte_synthese_type', 'texte_synthese_name', 
		'date_limite');
	
	// lecture en base
	$this->Behaviors->attach('Containable');
	$delib = $this->find('first', array(
		'fields' => $majFields,
		'contain' => array('Multidelib.id', 'Multidelib.position'),
		'conditions' => array('Deliberation.id' => $delibId)));


	if (empty($delib['Multidelib'])) return;

	// faut-il mettre a jour la position dans la s�ance
	if (!empty($delib['Deliberation']['seance_id']) && (empty($oldSeanceId)))
		// attribution d'une s�ance
		$majPosition = true;
	elseif (!empty($delib['Deliberation']['seance_id']) && (!empty($oldSeanceId) && ($delib['Deliberation']['seance_id'] !== $oldSeanceId)))
		// changement de s�ance
		$majPosition = true;
	elseif (empty($delib['Deliberation']['seance_id']) && !empty($oldSeanceId))
		// suppression de la s�ance
		$majPosition = true;

	if (!empty($delib['Deliberation']['seance_id']))
 		$position = $this->getLastPosition($delib['Deliberation']['seance_id'])-1;

	// mise � jour des d�lib�rations rattach�es
	$majDelibRatt = array();
	foreach ($majFields as $fieldName)
		$majDelibRatt['Deliberation'][$fieldName] = $delib['Deliberation'][$fieldName];
	foreach ($delib['Multidelib'] as $delibRattachee) {
		$majDelibRatt['Deliberation']['id'] = $delibRattachee['id'];
		if ($majPosition || (!empty($delib['Deliberation']['seance_id']) && empty($delibRattachee['position']))) {
			if ($position > 0) $position++;
			$majDelibRatt['Deliberation']['position'] = $position;
		} else
			unset($majDelibRatt['Deliberation']['position']);

            $this->save($majDelibRatt['Deliberation'], array('validate' => false, 'callbacks' => false));
	}
}

/**
 * reordonne les positions de la s�ance $seanceId
 */
function reOrdonnePositionSeance($seanceId) {
	// initialisations
	$position = 0;
	// lecture des delibs de la s�ance
	$delibs = $this->find('all', array(
		'recursive' => -1,
		'fields' => array('id', 'position'),
		'conditions' => array(
			'etat <>' => -1,
			'seance_id' => $seanceId),
		'order' => 'position ASC'));
	// pour toutes les d�libs
	foreach($delibs as $delib) {
		$position++;
		if ($position != $delib['Deliberation']['position'])
			$this->save(array('id'=>$delib['Deliberation']['id'], 'position'=>$position), array('validate' => false, 'callbacks' => false));
	}
}

/**
 * sauvergarde des d�lib�rations attach�es
 * @param integer $parentId id de la d�lib�ration principale
 * @param array $delib d�lib�ration rattach�e retourn� par le formulaire 'edit'
 */
function saveDelibRattachees($parentId, $delib) {
	// initialisations
	$newDelib = array();
	if (!isset($delib['objet'])) {
		$this->Session->setFlash('Libell� obligatoire.', 'growl', array('type'=>'erreur'));
		return false;
	}

	if (isset($delib['id'])) {
		// modification
		$newDelib['Deliberation']['id'] = $delib['id'];
	} else {
		// ajout
		$newDelib = $this->create();
		$newDelib['Deliberation']['parent_id'] = $parentId;
	}

	$newDelib['Deliberation']['objet'] = $delib['objet'];
	if (Configure::read('GENERER_DOC_SIMPLE')){
		$newDelib['Deliberation']['deliberation'] = $delib['deliberation'];
	} else {
		if (isset($delib['deliberation'])) {
 			$newDelib['Deliberation']['objet_delib'] = $delib['objet'];
 			$newDelib['Deliberation']['deliberation_name'] = $delib['deliberation']['name'];
			$newDelib['Deliberation']['deliberation_size'] = $delib['deliberation']['size'];
			$newDelib['Deliberation']['deliberation_size'] = $delib['deliberation']['size'];
			if (empty($delib['deliberation']['tmp_name']))
				$newDelib['Deliberation']['deliberation'] = '';
			else
				$newDelib['Deliberation']['deliberation'] = file_get_contents($delib['deliberation']['tmp_name']);
		}
	}

	if(!$this->save($newDelib['Deliberation'], false)) {
		$this->Session->setFlash('Erreur lors de la sauvegarde des d�lib�rations rattach�es.', 'growl', array('type'=>'erreur'));
		return false;
	}
        $tabs = array();
        $seances = $this->Deliberationseance->find('all', array(
                                                   'conditions' => array('Deliberationseance.deliberation_id' => $parentId),
                                                   'recursive'  => -1));
        foreach($seances as $seance) 
           $tabs[] = $seance['Deliberationseance']['seance_id'];
        $this->Seance->reOrdonne($this->id, $tabs);   
	return $this->id;
}

/**
 * fonction r�cursive de suppression de la d�lib�ration $delib8d, de ses versions ant�rieures et de ses d�lib�rations rattach�es
 * @param integer $delibId id de la d�lib � supprimer
 */
function supprimer($delibId) {
	// lecture de la d�lib en base
    $delib = $this->find('first', array(
    	'recursive' => -1,
		'fields' => array('anterieure_id', 'parent_id'),
		'conditions' => array('id' => $delibId)));
	if (empty($delib)) return;

	// suppression de la d�lib
	$this->del($delibId);
	// suppression du r�pertoire des docs
	$repFichier = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$delibId.DS;
	$this->rmDir($repFichier);
	// gestion de la s�ance
	if (!empty($delib['Deliberation']['seance_id'])) {
		$this->reOrdonnePositionSeance($delib['Deliberation']['seance_id']);
	}

	// pour les d�lib rattach�es, le traitement finit ici
	if (!empty($delib['Deliberation']['parent_id'])) return;

	// suppression des d�lib rattach�es
	$delibRattachees = $this->find('all', array(
    	'recursive' => -1,
		'fields' => array('id'),
		'conditions' => array('parent_id' => $delibId)));
	foreach($delibRattachees as $delibRattachee) {
		$this->supprimer($delibRattachee['Deliberation']['id']);
	}

	// suppression des d�lib ant�rieures
	if ( $delib['Deliberation']['anterieure_id'] != 0)
		$this->supprimer($delib['Deliberation']['anterieure_id']);
}

     /**
     * Supprime un r�pertoire et son contenu
     * @param string $dossier chemin du r�pertoire � supprimer
     */
     function rmDir($dossier) {
	$ouverture=@opendir($dossier);
	if (!$ouverture) return;
	while($fichier=readdir($ouverture)) {
		if ($fichier == '.' || $fichier == '..') continue;
		if (is_dir($dossier."/".$fichier)) {
			$r = $this->rmDir($dossier."/".$fichier);
			if (!$r) return false;
		} else {
			$r=@unlink($dossier."/".$fichier);
			if (!$r) return false;
		}
	}
	closedir($ouverture);
	$r=@rmdir($dossier);
	if (!$r) return false;
	return true;
    }
 
    function getSeancesid($deliberation_id) {
        $seances = array();
        $delib_seances = $this->Deliberationseance->find( 'all', 
                                                          array('conditions' =>  array(
                                                                'Deliberationseance.deliberation_id' => $deliberation_id),
                                                                'fields' => array('Deliberationseance.seance_id'),
                                                                'recursive' => -1));
        if (!empty( $delib_seances ))
            foreach( $delib_seances as $seance)
                 $seances[] = $seance['Deliberationseance']['seance_id']; 
        return $seances;
    }

    function getSeanceDeliberanteId($deliberation_id) {
        $seances = $this->getSeancesid($deliberation_id); 
        return ($this->Seance->getSeanceDeliberante($seances));
    }

   function getNbSeances($deliberation_id) {
       return count($this->getSeancesid($deliberation_id));
   }
 
    function getSeancesFromArray($projets) {
        $seances = array();
        if (isset($projets) && !empty($projets))
        foreach ($projets as $projet)
            if (isset($projet['Seance']) && (!empty($projet['Seance']))) {
                foreach($projet['Seance'] as $seance) {
                    $seances[$seance['id']] = $seance['Typeseance']['libelle'].' : '.$seance['date'];
                }
            }
        return $seances;
    }

    function getTypeseancesFromArray($projets) {
        $typeseances = array();
        if  (isset($projets) && !empty($projets))
            foreach ($projets as $projet) {
                if  (isset($projet['Seance']) && !empty($projet['Seance']))
                    foreach($projet['Seance'] as $seance)
                        $typeseances[$seance['type_id']] = $seance['Typeseance']['libelle'];
            }
        return $typeseances;
    }  

    function getPosition($deliberation_id, $seance_id) {
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $deliberation = $this->Deliberationseance->find('first', array('conditions' => array('Seance.id' => $seance_id,
                                                                                             'Deliberation.id' => $deliberation_id),
                                                                       'fields'     => array('Deliberationseance.position')));
        return($deliberation['Deliberationseance']['position']);
    }

    function afficherListePresents($delib_id=null, $seance_id)      {
        $presents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id),
                                                            'order'      => array("Acteur.position ASC")));
        if ($this->isFirstDelib($delib_id, $seance_id) and (empty($presents))) {
 
            $presents = $this->_buildFirstList($delib_id, $seance_id);
        }

        // Si la liste est vide, on recupere la liste des present lors de la derbiere deliberation.
        // Verifier que la liste precedente n'est pas vide...
        if (empty($presents))
            $presents = $this->_copyFromPreviousList($delib_id, $seance_id);
        for($i=0; $i<count($presents); $i++){
            if ($presents[$i]['Listepresence']['mandataire'] !='0') {
                $mandataire = $this->Seance->Typeseance->Acteur->read('nom, prenom', $presents[$i]['Listepresence']['mandataire']);
                $presents[$i]['Listepresence']['mandataire'] = $mandataire['Acteur']['prenom']." ".$mandataire['Acteur']['nom'];
            }
        }
        return ($presents);
    }

   function _buildFirstList($delib_id, $seance_id) {
        $seance = $this->Seance->find('first', array('conditions' => array('Seance.id'),
                                                     'recursive'  => -1,
                                                     'fields'     => array('Seance.type_id')));
        $elus = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($seance['Seance']['type_id'], true);
        foreach ($elus as $elu){
            $this->Listepresence->create();
            $this->params['data']['Listepresence']['acteur_id']=$elu['Acteur']['id'];
            $this->params['data']['Listepresence']['mandataire'] = '0';
            $this->params['data']['Listepresence']['present']= 1;
            $this->params['data']['Listepresence']['delib_id']= $delib_id;
            $this->Listepresence->save($this->params['data']);
        }
        return $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $delib_id)));
    }

    function _copyFromPreviousList($delib_id, $seance_id){
        $this->Listepresence->Behaviors->attach('Containable');

        $position = $this->getPosition($delib_id, $seance_id);
        $previousDelibId= $this->_getDelibIdByPosition($seance_id, $position);
        $previousPresents = $this->Listepresence->find('all', array('conditions' => array('Listepresence.delib_id' => $previousDelibId),
                                                                    'recursive'  => -1));

        foreach ($previousPresents as $present){
            $this->Listepresence->create();
            $this->params['data']['Listepresence']['acteur_id']=$present['Listepresence']['acteur_id'];
            $this->params['data']['Listepresence']['mandataire'] = $present['Listepresence']['mandataire'];
            $this->params['data']['Listepresence']['present']= $present['Listepresence']['present'];
            $this->params['data']['Listepresence']['delib_id']= $delib_id;
            $this->Listepresence->save($this->params['data']);
        }
        $liste = $this->Listepresence->find('all', array('conditions' => array("Listepresence.delib_id" =>$delib_id),
                                                         'contain'  => array('Acteur', 'Mandataire') ));
        if (!empty($liste))
            return  $liste;
        else
            return ($this->_buildFirstList($delib_id, $seance_id));
    }

    function _effacerListePresence($delib_id) {
        $this->Listepresence->deleteAll(array("delib_id" => $delib_id));
    }

    function _getDelibIdByPosition ($seance_id, $position){
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $delib = $this->Deliberationseance->find('first', array('conditions' => array('Deliberationseance.position'  =>  $position-1,
                                                                                      'Seance.id' => $seance_id),
                                                                'fields'    => array('Deliberation.id')));

        if (isset($delib['Deliberation']['id']))
            return $delib['Deliberation']['id'];
        else
            return 0;
    }

    function getDeliberationsSansSeance($fields=array(), $natures_id=array()) {
        if (empty($fields))
            $fields = 'Deliberation.id, Deliberation.objet, Deliberation.circuit_id,
                       Deliberation.etat, Deliberation.anterieure_id, Deliberation.etat, 
                       Deliberation.date_limite, Deliberation.num_pref, Deliberation.titre,
                       Deliberation.signee, Deliberation.redacteur_id, Deliberation.nature_id, 
                       Deliberation.service_id, Service.libelle, Nature.libelle, Theme.libelle,
                       Deliberation.theme_id';
        elseif($fields == 'id')
            $fields = 'Deliberation.id';

        $natures_id =  implode(", ", $natures_id);

        $this->Behaviors->attach('Containable');
        $requete = "SELECT $fields
                              FROM deliberations as Deliberation, 
                                   services as Service, 
                                   themes as Theme,
                                   natures as Nature
                              WHERE Deliberation.id NOT IN (SELECT deliberation_id FROM deliberations_seances)
                                    AND Deliberation.parent_id is null
                                    AND Deliberation.theme_id  = Theme.id
                                    AND Deliberation.nature_id = Nature.id
                                    AND Deliberation.service_id = Service.id 
                                    AND Deliberation.nature_id IN ($natures_id)
                                    AND Deliberation.etat != -1
                              ORDER BY Deliberation.created DESC;";
      
        return ($this->query($requete));

    }

    function copyPositionsDelibs($delib_id, $new_id) {
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $positions = $this->Deliberationseance->find('all', 
                                                     array('conditions' => array('Deliberationseance.deliberation_id' => $delib_id),
                                                           'fields'     => array('Deliberationseance.position', 
                                                                                 'Deliberationseance.seance_id'),
                                                           'recursive'  => -1));
        foreach($positions as $position) {
            $this->Deliberationseance->create();
            $Deliberationseance['Deliberationseance']['position']  = $position['Deliberationseance']['position']; 
            $Deliberationseance['Deliberationseance']['seance_id'] = $position['Deliberationseance']['seance_id'];
            $Deliberationseance['Deliberationseance']['deliberation_id'] = $new_id;
            $this->Deliberationseance->save($Deliberationseance);
        }

    }

}
?>
