<?php
/**
 * OdtFusion behavior class.
 *
 * Centralise les fonctions de fusion des modèles odt avec les données des modèles
 *
 * Callbacks:
 *  - getModelTemplateId($this->_id, $this->_modelTypeName) : le modèle doit posséder cette méthode qui retourne l'id du modeltemplate à utiliser
 *  - beforeFusion($this->_id, $this->_modelTypeName) : le modèle doit posséder cette méthode pour l'initialisation des variables gedooo avant de faire la fusion
 *
 * ATTENTION :
 *  - ce comportement ajoute et stocke le résultat de la fusion dans la variable odtFusionResult du modèle
 *
 */

class OdtFusionBehavior extends ModelBehavior {

    // id de l'occurence en base de données à fusionner
    protected $_id = null;

    // variables du modelType utilisé pour la fusion
    protected $_modelTypeid = null;
    protected $_modelTypeName = '';

    // variables du modelTemplate utilisé pour la fusion
    protected $_modelTemplateId = null;
    protected $_modelTemplateName = '';
    protected $_modelTemplateContent = '';

    // variable pour la détermination du nom du fichier de fusion
    protected $_fileNameSuffixe = '';

    /**
     * Sets up the configuration for the model, and loads OdtFusion models if they haven't been already
     * Génère une exception en cas d'erreur
     *
     * @param Model $model
     * @param array $options liste des options formatée comme suit :
     *  'id' => id de l'occurence du modèle sujet à la fusion
     *  'modelTypeName' => nom du type de modèle de fusion
     *  'fileNameSuffixe' : suffixe du nom de la fusion (défaut : $id)
     * @return void
     */
    public function setup(Model $model, $options = array()) {
        // initialisations
        $model->odtFusionResult = null;
        $this->_setup($options);
	}

    /**
     * initialisation des variables du behavior
     * @param array $options liste des options formatée comme suit :
     *  'id' => id de l'occurence du modèle sujet à la fusion
     *  'modelTypeName' => nom du type de modèle de fusion
     *  'fileNameSuffixe' : suffixe du nom de la fusion (défaut : $id)
     * @return void
     */
    public function _setup($options) {
        // initialisations
        $defaultOptions = array(
            'id' => $this->_id,
            'modelTypeName' => $this->_modelTypeName,
            'fileNameSuffixe' => $this->_fileNameSuffixe);
        $options = array_merge($defaultOptions, $options);

        // affectation des variables de la classe
        $this->_id = $options['id'];
        $this->_modelTypeName = $options['modelTypeName'];
        $this->_fileNameSuffixe = empty($options['fileNameSuffixe'])?$options['id']:$options['fileNameSuffixe'];
    }

    /**
     * Retourne un nom pour la fusion qui est constitué du nom (liellé) du modèle odt échapé, suivi de '_'.$suffix.
     * Génère une exception en cas d'erreur
     * @param Model $model modele du comportement
     * @param array $options tableau des parmètres optionnels :
     * 	'id' : identifiant de l'occurence en base de données (défaut : $this->_id)
     *  'modelTypeName' => nom du type de modèle de fusion
     * 	'fileNameSuffixe' : suffixe du nom de la fusion (défaut : $id)
     * @return string
     * @throws Exception en cas d'erreur
     */
    public function fusionName(Model &$model, $options = array()) {
        // initialisations
        $this->_setup($options);
        if (empty($this->_id))
            throw new Exception('détermination du nom de la fusion -> occurence en base de données non déterminée');

        // chargement du modelTemplate
        $this->_loadModelTemplate($model);

        // contitution du nom
        $fusionName = str_replace(array(' ', 'é', 'è', 'ê', 'ë', 'à'), array('_', 'e', 'e', 'e', 'e', 'a'), $this->_modelTemplateName);
        return preg_replace('/[^a-zA-Z0-9-_\.]/','', $fusionName).'_'.$this->_fileNameSuffixe;
    }

    /**
     * Fonction de fusion du modèle odt et des données.
     * Le résultat de la fusion est un odt dont le contenu est stocké dans la variable du model odtFusionResult
     * @param Model $model modele du comportement
     * @param array $options tableau des parmètres optionnels :
     * 	'id' : identifiant de l'occurence en base de données (défaut : $this->_id)
     *  'modelTypeName' => nom du type de modèle de fusion
     * @return void
     * @throws Exception en cas d'erreur
     */
    public function odtFusion(Model &$model, $options = array()) {
        // initialisations
        $this->_setup($options);
        if (empty($this->_id))
            throw new Exception('détermination du nom de la fusion -> occurence en base de données non déterminée');

        // chargement du modelTemplate
        $this->_loadModelTemplate($model);

        // parsing du model d'édition odt pour accéder aux variables et sections déclarées
        require_once(APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Lib'.DS.'phpOdtApi.php');
        $modelOdtInfos = new phpOdtApi();
        $modelOdtInfos->loadFromOdtBin($this->_modelTemplateContent);

        // chargement des classes php de Gedooo
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_Utility.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FieldType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_ContentType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_IterationType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_PartType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_FusionType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_MatrixRowType.class');
        include_once (ROOT.DS.APP_DIR.DS.'Vendor/GEDOOo/phpgedooo/GDO_AxisTitleType.class');

        // nouveau document odt à partir du model
        $oTemplate = new GDO_ContentType("",
            $this->_modelTemplateName,
            "application/vnd.oasis.opendocument.text",
            "binary",
            $this->_modelTemplateContent);

        // initialisation de la racine du document
        $oMainPart = new GDO_PartType();

        // initialisation des variables communes
        $this->_setVariablesCommunesFusion($oMainPart, $modelOdtInfos);

        // initialisation des variables du model de données
        $model->beforeFusion($oMainPart, $modelOdtInfos, $this->_id, $this->_modelTypeName);

        // initialisation de la fusion
        $oFusion = new GDO_FusionType($oTemplate, "application/vnd.oasis.opendocument.text", $oMainPart);

        // appel du webservice de fusion
        $oService = new SoapClient(Configure::read('GEDOOO_WSDL'),
            array("cache_wsdl"=>WSDL_CACHE_NONE,
                "exceptions"=> 1,
                "trace"=>1,
                "classmap"=>array(
                    "FieldType" => "GDO_FieldType",
                    "ContentType" => "GDO_ContentType",
                    "DrawingType" => "GDO_DrawingType",
                    "FusionType" => "GDO_FusionType",
                    "IterationType" => "GDO_IterationType",
                    "PartType" => "GDO_PartType",
                    "MatrixType"=>"GDO_MatrixType",
                    "MatrixRowType"=> "GDO_MatrixRowType",
                    "MatrixTitleType"=>"GDO_MatrixTitleType")));
        $model->odtFusionResult = $oService->Fusion($oFusion);

        // libération explicite de la mémoire
        unset($oTemplate);
        unset($oMainPart);
        unset($oFusion);
        unset($oService);
    }

    /**
     * Lecture et stockage du modele d'édition
     * @param Model $model modele du comportement
     * @return void
     * @throws Exception en cas d'erreur
     */
    private function _loadModelTemplate(Model &$model) {
        if (!empty($this->_modelTemplateId)) return;

        $modelTemplateId = $model->getModelTemplateId($this->_id, $this->_modelTypeName);
        if (empty($modelTemplateId))
            throw new Exception('identifiant du modèle d\'édition non trouvé pour id:'.$this->_id.' du model de données '.$model->alias);

        $myModeltemplate = ClassRegistry::init('ModelOdtValidator.Modeltemplate');
        $modelTemplate = $myModeltemplate->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'name', 'content'),
            'conditions' => array('id' => $modelTemplateId)));
        if (empty($modelTemplate))
            throw new Exception('modèle d\'édition non trouvé en base de données id:'.$this->_id);

        $this->_modelTemplateId = $modelTemplate['Modeltemplate']['id'];
        $this->_modelTemplateName = $modelTemplate['Modeltemplate']['name'];
        $this->_modelTemplateContent = $modelTemplate['Modeltemplate']['content'];
    }

    /**
     * fonction de fusion des variables communes : collectivité et dates
     * génère une exception en cas d'erreur
     * @param GDO_PartType $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param phpOdtApi $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     */
    private function _setVariablesCommunesFusion(GDO_PartType &$oMainPart, phpOdtApi &$modelOdtInfos) {
        // variables des dates du jour
        if ($modelOdtInfos->hasUserField('date_jour_courant')) {
            $myDate = new DateComponent;
            $oMainPart->addElement(new GDO_FieldType('date_jour_courant', $myDate->frenchDate(strtotime("now")), 'text'));
        }
        if ($modelOdtInfos->hasUserField('date_du_jour'))
            $oMainPart->addElement(new GDO_FieldType('date_du_jour', date("d/m/Y", strtotime("now")), 'date'));

        // variables de la collectivité
        $myCollectivite = ClassRegistry::init('Collectivite');
        $myCollectivite->setVariablesFusion($oMainPart, $modelOdtInfos, 1);
    }

}
