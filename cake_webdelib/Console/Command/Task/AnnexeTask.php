<?php

/**
 * Créé le : 01/10/2013
 * Tâches de vérification des annexes en base de données
 */
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * CakePHP AnnexesTask
 * @author Florian Ajir / Adullact
 */
class AnnexeTask extends Shell {

    public $uses = array('Annex', 'Deliberation', 'Collectivite');

    /**
     * variable contenant le log de génération des annexes par Gedooo
     * @var type string rapport de gedooo pour les annexes 
     */
    private $rapport;

    /**
     * fichier de rapport d'éxecution
     * @var type File 
     */
    private $logFile;

    /**
     * fichier modèle pour génération Gedooo
     * @var type File 
     */
    private $modelFile;

    /**
     * dossier pour annexes générées par Gedooo
     * @var type Folder
     */
    private $annexesFolder;

    /**
     * identifiants des annexes en erreur
     * @var type array
     */
    private $annexesInError;
    
    /**
     * Chemins fichiers & dossiers
     */
    public $logPath;
    public $annexePath;
    public $modelPath;

    public function execute() {
        // Inclusion des librairies Gedooo
        include_once (Configure::read("WEBDELIB_PATH") . DS . 'Vendor' . DS . 'GEDOOo' . DS . 'phpgedooo' . DS . 'GDO_PartType.class');
        include_once (Configure::read("WEBDELIB_PATH") . DS . 'Vendor' . DS . 'GEDOOo' . DS . 'phpgedooo' . DS . 'GDO_ContentType.class');
        include_once (Configure::read("WEBDELIB_PATH") . DS . 'Vendor' . DS . 'GEDOOo' . DS . 'phpgedooo' . DS . 'GDO_FusionType.class');

        // Initialisations
        $this->annexesInError = array();

        // Chemins fichiers & dossiers
        $this->annexePath = Configure::read("WEBDELIB_PATH") . DS . 'tmp' . DS . 'files' . DS . 'annexes';
        $this->logPath = Configure::read("WEBDELIB_PATH") . DS . "tmp" . DS . "logs" . DS . "gedooo.log";
        $this->modelPath = Configure::read("WEBDELIB_PATH") . DS . WEBROOT_DIR . DS . 'files' . DS . 'model_annexe_test.odt';

        // Instanciations
        $this->annexesFolder = new Folder($this->annexePath, true);
        $this->logFile = new File($this->logPath, true, 0644);
        $this->modelFile = new File($this->modelPath, false);
    }

    /**
     * Test des annexes 
     * Pour les annexes des délibérations de séances en cours et sans séance
     * Génère un fichier texte avec les colonnes suivantes :
     * # seance_id delib_id annex_id result
     * @param string $mode Mode de test des annexes {'all','noseance','nontraitees'}
     */
    public function testAnnexes($mode = 'all') {
        // Charge les annexes depuis la base de données
        $delibsWithAnnexes = $this->_getDelibsWithAnnexes();

        // filtre les annexes (scope selon argument $mode)
        switch ($mode) {
            case 'nontraitees':
                $projets = $this->_getProjetsSeanceEnCours($delibsWithAnnexes);
                break;

            case 'noseance':
                $projets = $this->_getProjetsSansSeance($delibsWithAnnexes);
                break;

            default: // case 'all'
                $projets = array_merge($this->_getProjetsSeanceEnCours($delibsWithAnnexes), $this->_getProjetsSansSeance($delibsWithAnnexes));
                break;
        }
        $nbAnnexes = $this->countAnnexesForDelibs($projets);
        $this->out("<info>Nombre d'annexes à tester : " . $nbAnnexes . ' (' . count($projets) . " délibérations)</info>\n");
        $i = 1;
        //Pour chaque projet
        foreach ($projets as $projet) {
            foreach ($projet['Annex'] as $annexe) {
                
                $this->out("$i/$nbAnnexes ---> Test de l'annexe ".$annexe['filename'] .' (id: '. $annexe['id'] . ', délibération: '.$projet['Deliberation']['id'] .')...');
                
                // Envoi de l'annexe à gedooo
                $result = $this->_sendToGedooo($annexe, $projet['Deliberation']['id']);
                // Log du résultat
                $this->_ajouterLigneAuRapport($projet['Deliberation']['id'], $annexe['id'], $result);
                $i++;
            } // Fin foreach annexe
        } // Fin foreach projet
        
        try {
            // Création du fichier de rapport
            $this->_creerRapport();
        }
        catch (Exception $exc) {
            $this->out('<error>Erreur fichier journal : ' . $exc->getMessage() . '</error>');
        }
        
        $this->annexesFolder->delete();
        
        return $this->annexesInError;
    }

    /**
     * Effectue une requête afin de récupérer un tableau de délibération avec annexes et séances,
     * Ce tableau est utilisé par les fonctions _getProjetsSeanceEnCours et _getProjetsSansSeance
     * @see _getProjetsSeanceEnCours 
     * @see _getProjetsSansSeance
     * @return array Tableau de délibérations contenant Annexes et Séances 
     */
    private function _getDelibsWithAnnexes() {

        $this->out("<info>Chargement des annexes depuis la base de données...</info>");
        $time_start = microtime(true);

        $this->Deliberation->Behaviors->attach('Containable');
        $options = array(
            'fields' => array('id'),
            'contain' => array(
                'Annex' => array(
                    'fields' => array('id', 'filename', 'filetype', 'data'),
                    'conditions' => array('filetype' => array("application/vnd.oasis.opendocument.text", "application/pdf"))
                ),
                'Deliberationseance' => array(
                    'fields' => array(),
                    'Seance' => array(
                        'fields' => array('id', 'traitee')
                    )
                )
            ),
        );

        $delibs = $this->Deliberation->find('all', $options);

        $time_end = microtime(true);
        $this->out("<time>Durée : " . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $delibs;
    }

    /**
     * Filtre parmis les enregistrements ceux qui sont attachés à une séance en cours
     * @return array annexes Les annexes des délibérations de séances en cours
     */
    private function _getProjetsSeanceEnCours($delibs) {

        $this->out("<info>Sélection des annexes des délibérations attachées à une séance à traiter...</info>");
        $time_start = microtime(true);

        $projets = array();
        //Pour chaque délib
        foreach ($delibs as $delib) {
            $controler = false;
            //Si la délib possède une annexe et une séance
            if (!empty($delib['Annex']) && !empty($delib['Deliberationseance'])) {
                // Parcourir les séances et si une d'elle n'est pas traitée (non vide) contrôler l'annexe
                foreach ($delib['Deliberationseance'] as $deliberationseance) {
                    if (!empty($deliberationseance['Seance']))
                        foreach ($deliberationseance['Seance'] as $seance)
                            if ($seance['traitee'] == 0) {
                                $controler = true;
                                break;
                            }

                    // Si une séance de la délib est non traitée, arreter le parcourt des séances de la délibération
                    if ($controler)
                        break;
                }
                // Si la délib fait partie d'une séance non traitée, ajouter au tableau de projets à tester
                if ($controler)
                    $projets[] = $delib;
            }
        }

        $time_end = microtime(true);
        $this->out("<time>Durée : " . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $projets;
    }

    /** @todo TESTER
     * Filtre parmis les enregistrements ceux qui ne sont attachés à aucune séance
     * @return array annexes Les annexes des délibérations sans séance
     */
    private function _getProjetsSansSeance($delibs) {

        $this->out("<info>Sélection des annexes des délibérations sans séance...</info>");
        $time_start = microtime(true);

        $projets = array();
        //Pour chaque délib
        foreach ($delibs as $delib)
        //Si la délib possède une annexe et pas de séance
            if (!empty($delib['Annex']) && empty($delib['Deliberationseance']))
            // Ajouter toutes les annexes au tableau d'annexes à tester
                $projets[] = $delib;

        $time_end = microtime(true);
        $this->out('<time>Durée : ' . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $projets;
    }

    /**
     * Ajoute une ligne avec les informations sur l'annexe à la variable $rapport
     * @param array $infosAnnexe Informations sur le passage de l'annexe dans gedooo et ses propriétés
     */
    private function _ajouterLigneAuRapport($deliberation_id, $annexe_id, $retourGedooo) {
        $this->rapport .=
                date("d-m-Y H:i:s") . "\t"
                . $deliberation_id . "\t"
                . $annexe_id . "\t"
                . $retourGedooo . "\n";
    }

    /**
     * Envoyer le rapport de l'éxecution au fichier de log
     */
    private function _creerRapport() {

        $this->out('<info>Création du rapport...</info>');
        $time_start = microtime(true);

        if ($this->logFile->writable()) {
            if ($this->logFile->open('w')) {
                $this->rapport = $this->logFile->prepare($this->rapport);
                $this->logFile->append($this->rapport);
                $this->logFile->close();
            } else {
                throw new Exception("Impossible d'ouvrir le fichier " . $this->logFile->path);
            }
        } else {
            throw new Exception("Impossible d'écrire dans le fichier " . $this->logFile->path);
        }

        $time_end = microtime(true);
        $this->out('<time>Durée : ' . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);
    }

    /** @TODO
     * Envoi d'une annexe à Gedooo pour tester le retour
     * @param type $annexe
     * @return string retour {ok,code_erreur}
     */
    private function _sendToGedooo($annexe, $delib_id) {

        $time_start = microtime(true);

        //Initialisations
        $mimetype = 'application/vnd.oasis.opendocument.text';


        // Partie principale du document
        $oMainPart = new GDO_PartType();
        $oMainPart->addElement(
                new GDO_ContentType('fichier', $annexe['filename'], $mimetype, 'binary', $annexe['data'])
        );
        // Model 
        $oTemplate = new GDO_ContentType("", $this->modelFile->path, $mimetype, "binary", $this->modelFile->read());

        //Fusion
        $oFusion = new GDO_FusionType($oTemplate, $mimetype, $oMainPart);
        $oFusion->process();

        try {
            //Conversion et concaténation
            $oFusion->SendContentToFile($this->annexesFolder->path . DS . 'deliberation_' . $delib_id . '-annexe_' . $annexe['id'] . '.odt');
            $retourGedooo = "OK";
        } catch (Exception $exc) {
            $this->out("<warning>Problème détecté :\n" . $exc->getMessage() . "</warning>");
//            $this->out("<warning>Problème détecté :\n" . $exc->getTraceAsString() . "</warning>");
            $this->annexesInError[] = array(
                'id' => $annexe['id'],
                'filename' => $annexe['filename'],
                'delib_id' => $delib_id
            );
            $retourGedooo = "KO";
        }
        $this->out('<info>'.$retourGedooo.'</info>');
        
        $time_end = microtime(true);
        $this->out("<time>Durée : " . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $retourGedooo;
    }

    public function countAnnexesForDelibs($delibs) {
        $nbAnnexes = 0;
        foreach ($delibs as $projet)
            $nbAnnexes += count($projet['Annex']);
        return $nbAnnexes;
    }

}
