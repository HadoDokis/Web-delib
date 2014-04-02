<?php
    // Temps infini alloué à l'exécution du script
    @set_time_limit( 0 );
	// Mémoire maximum allouée à l'exécution du script
	@ini_set( 'memory_limit', '2G' );

class ModelsController extends AppController {

	var $name = 'Models';
	var $uses = array('Deliberation', 'User',  'Annex', 'Typeseance', 'Seance', 'Service', 'Commentaire', 'Model', 'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Acteur', 'Infosupdef', 'Infosuplistedef', 'Historique', 'Modeledition');
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'Html2', 'Session');
	var $components = array('Date','Utils','Email', 'Acl', 'Gedooo', 'Conversion', 'Pdf', 'Gedooo2.Gedooo2Debugger');

	// Gestion des droits
	var $aucunDroit = array(
			'generer',
			'paramMails',
			'checkGedooo'
	);
	var $commeDroit = array(
			'add'          => 'Models:index',
			'delete'       => 'Models:index',
			'view'         => 'Models:index',
			'import'       => 'Models:index',
			'getFileData'  => 'Models:index',
			'changeStatus'=> 'Models:index'
	);

	function index() {
		$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
		$models=$this->Model->find('all', array('fields'=>array('id'), 'recursive'=>-1));
		$deletable=array();
		foreach ($models as $model) {
			$id=$model['Model']['id'];
			if ($this->Typeseance->find('first',array('conditions'=>array(
					'OR'=>array(
							'Typeseance.modelprojet_id'=>$id,
							'Typeseance.modeldeliberation_id'=>$id,
							'Typeseance.modelconvocation_id'=>$id,
							'Typeseance.modelordredujour_id'=>$id,
							'Typeseance.modelpvsommaire_id'=>$id,
							'Typeseance.modelpvdetaille_id'=>$id)),
					'recursive' => -1)))
				$deletable[$id]=false;
			else
				$deletable[$id]=true;
		}
		$this->set('deletable',$deletable);
		$this->set('models', $this->Model->find('all', array('fields' => array('modele', 'multiodj', 'type', 'name', 'recherche', 'joindre_annexe', 'modified', 'id'),
				'order'=>array('Model.modele' => 'ASC'),
				'recursive' => -1)));
	}

	function add() {
		if (empty($this->data)) {
			$this->render();
		} else{
			$this->request->data['Model']['type']='Document';
			if ($this->Model->save($this->data)) {
				$this->redirect('/models/index');
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('id invalide pour le modèle de  délibération','growl', array('type'=>'erreur'));
			$this->redirect('/models/index');
		}
		$data = $this->Model->read(null, $id);
		if (($data['Model']['type'] == 'Document')&&(!$this->Typeseance->find('first',array('conditions'=>array(
				'OR'=>array(
						'Typeseance.modelprojet_id'=>$id,
						'Typeseance.modeldeliberation_id'=>$id,
						'Typeseance.modelconvocation_id'=>$id,
						'Typeseance.modelordredujour_id'=>$id,
						'Typeseance.modelpvsommaire_id'=>$id,
						'Typeseance.modelpvdetaille_id'=>$id
				)
		)
		)))) {
			if ($this->Model->delete($id)) {
				$this->Session->setFlash('Le modèle a été supprimé.', 'growl');
				$this->redirect('/models/index');
			}
			else{
				$this->Session->setFlash('Impossible de supprimer ce type de modele','growl', array('type'=>'erreur'));
				$this->redirect('/models/index');
			}
		}
		else{
			$this->Session->setFlash('Impossible de supprimer ce type de modele','growl', array('type'=>'erreur'));
			$this->redirect('/models/index');
		}
	}

	function view($id = null) {
		$data = $this->Model->read(null, $id);
		if (!empty($data['Model']['name'])) {
			$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
			header('Content-type: '.$this->_getFileType($id));
			header('Content-Length: '.$this->_getSize($id));
			header('Content-Disposition: attachment; filename='.$this->_getFileName($id));
			echo $this->_getModel($id);
			exit();
		}
		else {
			$this->Session->setFlash('Aucun fichier li&eacute; &agrave; ce mod&egrave;le','growl', array('type'=>'erreur'));
			$this->redirect('/models/index');
		}
	}


	function import($model_id) {
		$this->set('USE_GEDOOO', Configure::read('USE_GEDOOO'));
		$this->set('model_id', $model_id);
		$this->Model->id = $model_id;
		$Model = $this->Model->find('first', array('conditions'=> array('Model.id'=> $model_id),
				'recursive' => -1,
				'fields'    => array('modele', 'recherche','joindre_annexe', 'name')));
		$this->set('libelle', $Model['Model']['modele']);
		$this->set('filename', $Model['Model']['name']);

		if (!empty($this->data)){
			if (isset($this->data['Model']['template'])){
				if ($this->request->data['Model']['template']['size']!=0){
					$this->request->data['Model']['id']        = $model_id;
					$this->request->data['Model']['name']      = $this->data['Model']['template']['name'];
					$this->request->data['Model']['size']      = $this->data['Model']['template']['size'];
					$this->request->data['Model']['extension'] = $this->data['Model']['template']['type'];
					$this->request->data['Model']['content']   = file_get_contents($this->data['Model']['template']['tmp_name']);
				}else
                                {
                                   $this->Session->setFlash('Aucun fichier importé','growl', array('type'=>'erreur'));
                                    $this->redirect('/models/index');
                                }
			}
			if ($this->Model->save($this->data))
				$this->redirect('/models/index');
		} else {
			$this->data = $Model;
		}
	}

	function _getFileType($id=null) {
		$objCourant = $this->Model->find('first', array(
				'conditions'=> array('Model.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'extension'));
		return $objCourant['Model']['extension'];
	}

	function _getFileName($id=null) {
		$objCourant = $this->Model->find('first', array(
				'conditions'=> array('Model.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'name'));
		return utf8_encode($objCourant['Model']['name']);
	}

	function _getSize($id=null) {
		$objCourant = $this->Model->find('first', array(
				'conditions'=> array('Model.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'size'));
		return $objCourant['Model']['size'];
	}

	function _getModel($id=null) {
		$objCourant = $this->Model->find('first', array(
				'conditions'=> array('Model.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'content'));
		return $objCourant['Model']['content'];
	}

    /**
     * ****************************************************************************************
     * Méthode de génération documentaire (délibération, texte de projet, convocation, ODJ, PV)
     * ****************************************************************************************
     * - Récupération des données de la collectivité (id=1)
     * - Instanciation d'un objet GDO_PartType
     * - Ajout des champs concernants la collectivité et les dates dans la partie principale du modèle de document
     * - Si un identifiant de délibération ($delib_id) est renseigné : Génération d'une délibération ou d'un texte de projet
     *      - récuperer en base la délibération
     *      - fait appel à Deliberation:makeBalisesProjet (NOTE: ?)
     *      - récupère les annexes de la délibération (Annex:getAnnexesFromDelibId)
     *      - pour chaque annexe :
     *              - récupérer en base de données la valeur de ses attributs (ATTENTION: Seul les champs 'id' et 'data_pdf' sont utilisés alors qu'ils sont tous récupérés)
     *              - ajouter au tableau d'annexes le fichier retourné par la fonction  Gedooo:createFile à partir de ses attributs
     * - Si un identifiant de séance ($seance_id) est renseigné : Génération d'une convocation, ordre du jour ou PV
     *      - récupère en base les délibérations associées à la séance (projets)
     *      - instancie un objet GDO_IterationType("Projets")
     *      - pour chaque projet :
     *              - instancie un objet GDO_PartType (NOTE: partie projet dans GDO ?)
     *              - appel de Deliberation:makeBalisesProjet (paramètres : le projet et l'objet GDO_PartType) (NOTE: ?)
     *              - ajout de la partie créée au bloc projet
     *              - récupération en base des annexes associées au projet
     *              - si des annexes existent pour ce projet, les ajouter au tableau $annexes_id
     *      - pour chaque annexes ($annexes_id) : (NOTE: utilité de stocker le tableau dans un tableau ?)
     *              - récupérer en base les données de l'annexe
     *              - création du fichier annexe en pdf à partir des données de la base (appel de Gedooo:createFile)
     *              - ajout du chemin du fichier créé au tableau d'annexes ($annexes) pour GEDOOO
     *      - ajout du bloc projet à l'objet GDO_PartType ($oMainPart)
     *      - si ce n'est pas un PV (par défaut PV=0)
     *              - mise à la date et heure du jour du paramètre date_convocation de la séance en base de données
     *              - récupérer en base le type de la séance
     *              - récupérer à partir du type de séance, la liste des acteurs convoqués
     *              - si le fichier documents.zip existe dans le repertoire de destination:
     *                      - supprimer le fichier
     *              - compter le nombre d'acteurs convoqués (NOTE: variable inutilisée)
     *              - lecture du model (appel de Model:read(null, $model_id)) et passage du nom à la vue (NOTE: la variable est elle utilisée?, que fait Model:read?)
     *              - si la liste des acteurs convoqués est vide :
     *                      - stopper l'éxecution (return "")
     *              - instanciation d'un objet ZipArchive
     *              - pour chaque acteur convoqué :
     *                      - passage à la vue de la variable unique (paramètre de la méthode par défaut à false) (NOTE: la variable est elle utilisée?)
     *                      - si unique = false (par défaut) :
     *                              - ajout des champs concernants l'acteur dans la partie principale du modèle de document
     *                              - ajout au tableau liste de fichier (clé = $urlWebroot."acteur_id-ActeurNomSansAccentCamelize", valeur = $prenom_acteur." ".$nom_acteur)
     *                      - sinon (unique = true)
     *                              - nom de fichier = 'Document'
     *                              - ajout au tableau liste de fichier (clé = $urlWebroot."Document", valeur = "Document généré")
     *                      - bloc try catch (ATTENTION: création de plusieurs fichiers équivalent avec des méthodes différentes si format = odt, écrasement? perte de perfs)
     *                              - fusion GDO avec comme paramètres : ($oTemplate, $sMimeType, $oMainPart)
     *                              - envoi le contenu fusionné vers le fichier dont le chemin est $path.$nomFichier.".odt" (appel de GDO_FusionType:SendContentToFile) (NOTE: utilisation de file_put_contents)
     *                              - conversion du fichier vers le format odt (NOTE: format d'origine?)
     *                              - conversion du fichier vers le format choisi par l'utilisateur ($format) (ATTENTION: si le format choisi est odt, on effectue 2 conversion identiques)
     *                              - création du fichier par Gedooo à partir du contenu retourné par la conversion
     *                              - si le format choisi est pdf est que la variable joindre_annexe est vrai :
     *                                      - concaténer le fichier avec ses annexes
     *                      - si une exception est lancée (catch)
     *                              - rediriger vers la page /seances/listerFuturesSeances avec un message d'erreur
     *                      - si unique = false (par défaut) :
     *                              - ouverture de l'archive zip au chemin $path.'documents.zip' (création si elle n'existe pas encore)
     *                              - si l'ouverture/création s'est effectuée sans erreur :
     *                                      - ajout du fichier généré à l'archive
     *                      - sinon (unique = true) :
     *                              - stopper la boucle foreach
     *                      - envoi du document par mail à l'acteur (si le champ mail est renseigné: test interne)
     *              - si unique = false (par défaut) :
     *                      - ajout au tableau de la liste des fichiers du document zip
     *              - passage à la vue des variables listFiles et format
     *              - déclaration de l'utilisation de la vue generer (NOTE: n'est elle pas celle par défaut?)
     *              - affectation de la valeur true à la variable genereConvocation
     *      - sinon (si PV = 1 dans les paramètres)
     *              - récupération en base de données des informations de la séance (ATTENTION: tous les champs sont récupérés alors que uniquement les champs debat_global et debat_global_name sont utilisés)
     *              - déclaration du chemin du repertoire à utiliser (WEBROOT_PATH."/files/generee/PV/$seance_id/")
     *              - vérification que Gedooo a la possibilité d'écrire dans le répertoire
     *                      - si Gedooo ne peut pas écrire dans le répertoire, stoper l'éxecution avec un message d'erreur
     *              - si la constante GENERER_DOC_SIMPLE du fichier de configuration est vrai :
     *                      - inclure le composant conversion.php (ATTENTION: appel de include_once au lieu d'une méthode CakePHP)
     *                      - création du fichier debat_seance.html dans le repertoire (appel de Gedooo:createFile)
     *                      - conversion du fichier créé en odt pour insertion dans le document $oMainPart dans le champ debat_seance
     *              - sinon (constante GENERER_DOC_SIMPLE = false)
     *                      - écrasement de la variable $urlWebroot (ATTENTION: inutile, prend la même valeur que l'originale)
     *                      - si ($seance['Seance']['debat_global_name']== "") : (ATTENTION: bloc inutile)
     *                              - affectation de la variable nameDSeance = "vide" (ATTENTION: variable inutilisée)
     *                      - sinon :
     *                              - ajout du champ debat_seance au model à partir de la variable $seance['Seance']['debat_global']
     * - Si on ne génère pas de convocation : Lancement de la fusion
     *  $convocation est vrai seulement si $seance_id n'est pas null ET $isPV est faux (=> donc si on génére une convocation ou un ordre du jour)
     * 	- création d'un objet GDO_FusionType et appel de GDO_FusionType:process
     * 	- si le fichier doit être envoyé au navigateur ($dl = 1)
     * 		- envoi le contenu généré vers le fichier dont le chemin est $path.$nomFichier (appel de GDO_FusionType:SendContentToFile)
     * 		- convertit ce fichier vers le format choisi (pdf ou odt) (appel de Conversion:convertirFichier) (NOTE: quel est son format d'origine ?)
     * 		- crée le fichier à partir du résultat de la conversion (NOTE: deuxième fichier créé ?) (appel de Gedooo:createFile)
     * 		- si le format choisi est pdf et que le modèle en base possède l'attribut $joindre_annexe à vrai :
     * 			- concatène les annexes au fichier (appel de Pdf:concatener avec utilisation de pdftk)
     * 		- passage à la vue des variables listFiles et format
     * 	- sinon (cas par défaut: fichier à stocker sur le serveur)
     * 		- crée le fichier à vide (appel de Gedooo:createFile)
     * 		- envoi le contenu généré vers le fichier (appel de GDO_FusionType:SendContentToFile)
     * 		- convertit ce fichier vers le format choisi (pdf ou odt) (appel de Conversion:convertirFichier)
     * 		- crée le fichier à partir du résultat de la conversion (deuxième fichier créé ?) (appel de Gedooo:createFile)
     * 		- si le format choisi est pdf et que le modèle en base possède l'attribut $joindre_annexe à vrai :
     * 			- concatène les annexes au fichier (appel de Pdf:concatener avec utilisation de pdftk)
     * 		- ouverture dans le navigateur du fichier (passage au navigateur : déclaration headers et die)
     *
     * @see Configure
     * 	- boolean GENERER_DOC_SIMPLE
     * 		@see app/Config/webdelib.inc
     * @see Session
     * 	- integer user.format.sortie
     * 		@see Controller/UsersConrtoller::changeFormat()
     * 		@see Pages/format.ctp array( 0=>'pdf', 1=>'odt' )
     *
     * ATTENTION: $delib_id et $seance_id ont l'air logiquement exclusifs
     * au vu des paramètres, mais ne le sont pas réellement dans le code
     * (pas de if / else if).
     *
     * ATTENTION: pour chacun des acteurs, pour une séance (convocation ou
     * ordre du jour, pas pour un PV), on fabrique l'archive Zip et on l'
     * envoie (ou juste le lien) à chacun des acteurs, si celui-ci possède
     * une adresse email et que Configure::SMTP_USE est vrai.
     *
     * ATTENTION: $editable et stockage dans webroot pour une histoire de WebDav
     *
     * @see
     * 	- Annex:find()
     * 	- Annex:getAnnexesFromDelibId()
     * 	- Collectivite::find() Celle d'id 1
     * 	- ConversionComponent::convertirFichier()
     * 	- DateComponent::frenchDate()
     * 	- Deliberation::find()
     * 	- Deliberation::makeBalisesProjet()
     *  - GedoooComponent::checkPath()
     *  - GedoooComponent::createFile()
     *  - Model::find() / ATTENTION: Model
     *  - PdfComponent::concatener()
     * 	- Seance::getDeliberations()
     * 	- Seance::makeBalise()
     * 	- Seance::getType()
     * 	- Seance::saveField()
     * 	- Typeseance::acteursConvoquesParTypeSeanceId()
     *  - UtilsComponent::strSansAccent() 326
     *
     * @param integer $delib_id La clé primaire d'une délibération ou d'un texte de projet
     * @param integer $seance_id La clé primaire d'une séance
     * @param integer $model_id La clé primaire du modèle de document à utiliser (classe Model)
     * @param boolean $editable Permet de spécifier si le format du fichier
     * 	généré sera du PDF ($editable=0 && user.format.sortie=0) ou de l'ODT
     * @param boolean $dl Permet de spécifier si le fichier doit être stocké
     * 	(dans webroot?) ou directement envoyé au navigateur.
     * @param string $nomFichier Basename du nom du (des) fichier(s) généré(s)
     * 	ou stocké(s)
     * @param boolean $isPV Permet de savoir si l'on génère un PV ou alors
     * 	une convocation, un ordre du jour, ...
     * @param boolean $unique
     */
    function generer($delib_id = null, $seance_id = null, $model_id, $editable = -1, $dl = 0, $nomFichier = 'retour', $isPV = 0, $unique = false) {
        $time_start = microtime(true);

        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_Utility.class'); // Jamais utilisé ??!!
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
        include_once (ROOT . DS . APP_DIR . DS . 'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');
        $genereConvocation = false;
        //*****************************************
        // Choix du format de sortie
        //*****************************************

        if (($this->Session->read('user.format.sortie') == 0) || ($editable == 0)) {
            $sMimeType = "application/pdf";
            $format = "pdf";
        } else {
            $sMimeType = "application/vnd.oasis.opendocument.text";
            $format = "odt";
        }

        //*****************************************
        // Préparation des répertoires pour la création des fichiers
        //*****************************************
        $dyn_path = "/files/generee/fd/$seance_id/$delib_id/";
        $path = WEBROOT_PATH . $dyn_path;

        if (!$this->Gedooo->checkPath($path))
            die("Webdelib ne peut pas ecrire dans le repertoire : $path");
        $urlWebroot = 'http://' . $_SERVER['HTTP_HOST'] . $this->base . $dyn_path;

        //*****************************************
        //Création du model ott
        //*****************************************
        $u = new GDO_Utility(); // Jamais utilisé ??!!
        $model = $this->Model->find('first', array(
            'conditions' => array('Model.id' => $model_id),
            'recursive' => '-1',
            'fields' => array('content', 'joindre_annexe')));

        $content = $model['Model']['content'];
        $joindre_annexe = $model['Model']['joindre_annexe'];

        $oTemplate = new GDO_ContentType("", $this->_getFileName($model_id), "application/vnd.oasis.opendocument.text", "binary", $content);

        //*****************************************
        // Organisation des données
        //*****************************************
        $oMainPart = new GDO_PartType();

        // Informations sur la collectivité
        $data = $this->Collectivite->find('first', array(
            'conditions' => array('Collectivite.id' => 1)));

        $oMainPart->addElement(new GDO_FieldType('nom_collectivite', utf8_encode($data['Collectivite']['nom']), "text"));
        $oMainPart->addElement(new GDO_FieldType('adresse_collectivite', utf8_encode($data['Collectivite']['adresse']), "text"));
        $oMainPart->addElement(new GDO_FieldType('cp_collectivite', utf8_encode($data['Collectivite']['CP']), "text"));
        $oMainPart->addElement(new GDO_FieldType('ville_collectivite', utf8_encode($data['Collectivite']['ville']), "text"));
        $oMainPart->addElement(new GDO_FieldType('telephone_collectivite', utf8_encode($data['Collectivite']['telephone']), "text"));
        $oMainPart->addElement(new GDO_FieldType('date_jour_courant', utf8_encode($this->Date->frenchDate(strtotime("now"))), 'text'));
        $oMainPart->addElement(new GDO_FieldType('date_du_jour', date("d/m/Y", strtotime("now")), 'date'));

        $annexes_id = array();
        //*****************************************
        // Génération d'une délibération ou d'un texte de projet
        //*****************************************
        if ($delib_id != "null") {
            $delib = $this->Deliberation->find('first', array(
                'conditions' => array('Deliberation.id' => $delib_id),
                'recursive' => -1));
            $this->Deliberation->makeBalisesProjet($delib, $oMainPart);
            $tmp_annexes = $this->Deliberation->Annex->getAnnexesFromDelibId($delib_id, 0, 1);
            if (!empty($tmp_annexes))
                array_push($annexes_id, $tmp_annexes);
            $path_annexes = $path . 'annexes/';
            $annexes = array();
            foreach ($annexes_id as $annex_ids) {
                foreach ($annex_ids as $annex_id) {
                    $annexFile = $this->Deliberation->Annex->find('first', array(
                        'conditions' => array('Annex.id' => $annex_id['Annex']['id']),
                        'recursive' => -1));
                    array_push($annexes, $this->Gedooo->createFile($path_annexes, "annex_" . $annexFile['Annex']['id'] . '.pdf', $annexFile['Annex']['data_pdf']));
                }
            }
        }
        //*****************************************
        // Génération d'une convocation, ordre du jour ou PV
        //*****************************************
        if ($seance_id != "null") {
            $projets = $this->Seance->getDeliberations($seance_id);
            $blocProjets = new GDO_IterationType("Projets");
            foreach ($projets as $projet) {
                $oDevPart = new GDO_PartType();
                $this->Deliberation->makeBalisesProjet($projet, $oDevPart);
                $blocProjets->addPart($oDevPart);

                $tmp_annexes = $this->Deliberation->Annex->getAnnexesFromDelibId($projet['Deliberation']['id'], 0, 1);
                if (!empty($tmp_annexes))
                    array_push($annexes_id, $tmp_annexes);
            }
            $path_annexes = $path . 'annexes/';
            $annexes = array();
            foreach ($annexes_id as $annex_ids) {
                foreach ($annex_ids as $annex_id) {
                    $annexFile = $this->Deliberation->Annex->find('first', array(
                        'conditions' => array('Annex.id' => $annex_id['Annex']['id']),
                        'recursive' => -1));
                    array_push($annexes, $this->Gedooo->createFile($path_annexes, "annex_" . $annexFile['Annex']['id'] . '.pdf', $annexFile['Annex']['data_pdf']));
                }
            }

            $oMainPart->addElement($blocProjets);
            $this->Seance->makeBalise($seance_id, $oMainPart);
            if (!$isPV) { // une convocation ou un ordre du jour
                $this->Seance->id = $seance_id;
                $this->Seance->saveField('date_convocation', date("Y-m-d H:i:s", strtotime("now")));
                $type_id = $this->Seance->getType($seance_id);
                $acteursConvoques = $this->Seance->Typeseance->acteursConvoquesParTypeSeanceId($type_id);
                if (file_exists($path . 'documents.zip'))
                    unlink($path . 'documents.zip');

                $nbActeurs = count($acteursConvoques);
                $cpt = 0;
                $model_tmp = $this->Model->read(null, $model_id);
                $this->set('nom_modele', $model_tmp['Model']['modele']);
                if (empty($acteursConvoques))
                    return "";
                $zip = new ZipArchive;
                foreach ($acteursConvoques as $acteur) {
                    $cpt++;
                    $this->set('unique', $unique);
                    if ($unique == false) {
                        $oMainPart->addElement(new GDO_FieldType("nom_acteur", utf8_encode($acteur['Acteur']['nom']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("prenom_acteur", utf8_encode($acteur['Acteur']['prenom']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("salutation_acteur", utf8_encode($acteur['Acteur']['salutation']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("titre_acteur", utf8_encode($acteur['Acteur']['titre']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("date_naissance_acteur", utf8_encode($acteur['Acteur']['date_naissance']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("adresse1_acteur", utf8_encode($acteur['Acteur']['adresse1']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("adresse2_acteur", utf8_encode($acteur['Acteur']['adresse2']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("cp_acteur", utf8_encode($acteur['Acteur']['cp']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("ville_acteur", utf8_encode($acteur['Acteur']['ville']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("email_acteur", utf8_encode($acteur['Acteur']['email']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("telfixe_acteur", utf8_encode($acteur['Acteur']['telfixe']), "text"));
                        $oMainPart->addElement(new GDO_FieldType("note_acteur", utf8_encode($acteur['Acteur']['note']), "text"));
                        $nomFichier = $acteur['Acteur']['id'] . '-' . Inflector::camelize($this->Utils->strSansAccent($acteur['Acteur']['nom']));
                        $listFiles[$urlWebroot . $nomFichier] = $acteur['Acteur']['prenom'] . " " . $acteur['Acteur']['nom'];
                    } else {
                        $nomFichier = 'Document';
                        $listFiles[$urlWebroot . $nomFichier] = 'Document généré';
                    }

                    try {
                        Configure::write('debug', 2);
                        $this->Gedooo2Debugger->toCsv( $oMainPart );
                        return;

                        Configure::write('debug', 0);
                        error_reporting(0);

                        $time_end = microtime(true);
                        $time = $time_end - $time_start;
                        $this->log("Temps création de requete :" . $time);

                        $time_start = microtime(true);
                        $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
                        $oFusion->process();
                        $time_end = microtime(true);
                        $time = $time_end - $time_start;
                        $this->log("Temps création de fusion : " . $time);

                        $time_start = microtime(true);
                        $oFusion->SendContentToFile($path . $nomFichier . ".odt");
                        $content = $this->Conversion->convertirFichier($path . $nomFichier . ".odt", 'odt');
                        file_put_contents($path . $nomFichier . ".odt", $content);

                        $content = $this->Conversion->convertirFichier($path . $nomFichier . ".odt", $format);
                        $chemin_fichier = $this->Gedooo->createFile($path, $nomFichier . ".$format", $content);
                        if (($format == 'pdf') && ($joindre_annexe))
                            $this->Pdf->concatener($chemin_fichier, $annexes);
                        $time_end = microtime(true);
                        $time = $time_end - $time_start;
                        $this->log("Temps conversion et concaténation : " . $time);
                    } catch (Exception $e) {
                        $this->Session->setFlash($e, 'growl', array('type' => 'erreur'));
                        $this->redirect('/seances/listerFuturesSeances');
                        die;
                    }
                    if ($unique == false) {
                        $res = $zip->open($path . 'documents.zip', ZIPARCHIVE::CREATE);
                        if ($res === TRUE) {
                            $zip->addFile($chemin_fichier, $nomFichier . ".$format");
                            $res2 = $zip->close();
                        }
                    }
                    else
                        break;
                    // envoi des mails si le champ est renseigné
                    $this->_sendDocument($acteur['Acteur'], $nomFichier, $path, '');
                }
                if ($unique == false)
                    $listFiles[$urlWebroot . 'documents.zip'] = 'Documents.zip';
                $this->set('listFiles', $listFiles);
                $this->set('format', $format);
                $this->render('generer');
                $genereConvocation = true;
            }
            else {
                $seance = $this->Seance->find('first', array(
                    'conditions' => array('Seance.id' => $seance_id),
                    'recursive' => -1));
                $dyn_path = "/files/generee/PV/$seance_id/";
                $path = WEBROOT_PATH . $dyn_path;
                if (!$this->Gedooo->checkPath($path))
                    die("Webdelib ne peut pas ecrire dans le repertoire : $path");

                if (Configure::read('GENERER_DOC_SIMPLE')) {
                    include_once ('controllers/components/conversion.php');
                    $this->Conversion = new ConversionComponent;

                    $filename = $path . "debat_seance.html";
                    $this->Gedooo->createFile($path, "debat_seance.html", $seance['Seance']['debat_global']);
                    $content = $this->Conversion->convertirFichier($filename, "odt");

                    $oMainPart->addElement(new GDO_ContentType('debat_seance', $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));
                } else {
                    $urlWebroot = 'http://' . $_SERVER['HTTP_HOST'] . $this->base . $dyn_path;

                    if ($seance['Seance']['debat_global_name'] == "")
                        $nameDSeance = "vide";
                    else {
                        $oMainPart->addElement(new GDO_ContentType('debat_seance', 'debat_seance.odt', "application/vnd.oasis.opendocument.text", 'binary', $seance['Seance']['debat_global']));
                    }
                }
            }
        }

        if ($genereConvocation == false) {
            Configure::write('debug', 2);
            $this->Gedooo2Debugger->toCsv( $oMainPart );
            return;

            //*****************************************
            // Lancement de la fusion
            //*****************************************
            Configure::write('debug', 0);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $oFusion = new GDO_FusionType($oTemplate, $sMimeType, $oMainPart);
            $oFusion->process();
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            if ($dl == 1) {
                $oFusion->SendContentToFile($path . $nomFichier);
                $content = $this->Conversion->convertirFichier($path . $nomFichier, $format);
                $chemin_fichier = $this->Gedooo->createFile($path, "$nomFichier.$format", $content);
                if (($format == 'pdf') && ($joindre_annexe))
                    $this->Pdf->concatener($chemin_fichier, $annexes);
                $listFiles[$urlWebroot . $nomFichier] = 'Document généré';
                $this->set('listFiles', $listFiles);
                $this->set('format', $format);
            }
            else {
                $nomFichier = "$nomFichier.$format";
                $fichier = $this->Gedooo->createFile($path, $nomFichier, '');
                $oFusion->SendContentToFile($fichier);
                $content = $this->Conversion->convertirFichier($fichier, $format);

                $chemin_fichier = $this->Gedooo->createFile($path, $nomFichier . "2", $content);
                if (($format == 'pdf') && ($joindre_annexe))
                    $this->Pdf->concatener($chemin_fichier, $annexes);
                $content = file_get_contents($chemin_fichier);

                $time_end = microtime(true);
                $time = $time_end - $time_start;

                header("Content-type: $sMimeType");
                header("Content-Disposition: attachment; filename=\"$nomFichier\"");
                header("Content-Length: " . strlen($content));
                die($content);
            }
        }
    }

	function _sendDocument($acteur, $fichier, $path, $doc) {
		if (($this->Session->read('user.format.sortie')==0) )
			$format    = ".pdf";
		else
			$format    = ".odt";

		if ($acteur['email'] != '') {
			if (Configure::read("SMTP_USE")) {
				$this->Email->smtpOptions = array( 'port'     => Configure::read("SMTP_PORT"),
						'timeout'  => Configure::read("SMTP_TIMEOUT"),
						'host'     => Configure::read("SMTP_HOST"),
						'username' => Configure::read("SMTP_USERNAME"),
						'password' => Configure::read("SMTP_PASSWORD"),
						'client'   => Configure::read("SMTP_CLIENT")
				);
				$this->Email->delivery = 'smtp';
			}
			else
				$this->Email->delivery = 'mail';

			$this->Email->from = Configure::read("MAIL_FROM");
			$this->Email->to = $acteur['email'];
			$this->Email->charset = 'UTF-8';

			$this->Email->subject = utf8_encode("Vous venez de recevoir un document de Webdelib ");
			$this->Email->sendAs = 'text';
			$this->Email->layout = 'default';
			$this->Email->template = 'convocation';
			$this->set('data',   $this->paramMails('convocation',  $acteur ));

			$this->Email->attachments = array($path.$fichier.$format);
			$this->Email->send();
		}
	}

	function paramMails($type,  $acteur) {
		$handle  = fopen(CONFIG_PATH.'/emails/'.$type.'.txt', 'r');
		$content = fread($handle, filesize(CONFIG_PATH.'/emails/'.$type.'.txt'));
		$searchReplace = array(
				"#NOM#" => $acteur['nom'],
				"#PRENOM#" => $acteur['prenom'],
		);
		return utf8_encode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $content))));
	}

	function checkGedooo() {
		$name = tempnam ("/tmp/" , "testGedooo" ).".pdf";
		@unlink($name);
		include_once ('vendors/GEDOOo/phpgedooo/GDO_PartType.class');
		include_once ('vendors/GEDOOo/phpgedooo/GDO_FieldType.class');
		include_once ('vendors/GEDOOo/phpgedooo/GDO_ContentType.class');
		include_once ('vendors/GEDOOo/phpgedooo/GDO_FusionType.class');

		$oTemplate = new GDO_ContentType("", "empty.odt", "application/vnd.oasis.opendocument.text",
				"binary", file_get_contents(WEBROOT_PATH."/files/empty.odt"));
		$oMainPart = new GDO_PartType();
		$oMainPart->addElement(new GDO_FieldType('ma_variable', 'OK', 'text'));




		$oFusion = new GDO_FusionType($oTemplate, "application/pdf", $oMainPart);
		$oFusion->process();
		$oFusion->SendContentToFile($name);

		if (file_exists($name))
			die ('OK');
		else
			die ('OK');
	}

	function changeStatus($field, $id) {

             $data = $this->Modeledition->find('first', array('conditions' => array("Modeledition.id" => $id),
                                               'recursive'  => -1,
                                               'fields'     => array("$field")));
             $this->Modeledition->id = $id;
             if ($data['Modeledition'][$field] == 0)
                 $this->Modeledition->saveField($field, 1);
             elseif($data['Modeledition'][$field] == 1)
                 $this->Modeledition->saveField($field, 0);

            $this->Session->setFlash('Modification effectuée...', 'growl');
	    $this->redirect('/models/index');

	}

}
?>
