<?php

/**
 * CakePHP GedoooTask
 * Tâches de vérification des textes en base de données par Gedooo
 * Créé le : 01/10/2013
 *
 * @category CakeShell_Webdelib
 * @author Florian Ajir @ Adullact <florian.ajir@adullact.org>
 */

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class GedoooTask extends Shell
{

    public $uses = array('Annex', 'Deliberation', 'Collectivite', 'Seance', 'Deliberationseance');

    /**
     * variable contenant le log de génération des annexes par Gedooo
     * @var type string rapport de gedooo pour les annexes 
     */
    private $_rapport;

    /**
     * fichier de rapport d'éxecution
     * @var type File 
     */
    private $_logFile;

    /**
     * fichier modèle pour génération Gedooo
     * @var type File 
     */
    private $_modelFile;

    /**
     * dossier pour textes générées par Gedooo
     * @var type Folder
     */
    private $_textesFolder;

    /**
     * identifiants des textes en erreur
     * @var type array
     */
    private $_textesInError;

    /**
     * Chemins fichiers & dossiers
     */
    public $logPath;
    public $textePath;
    public $modelPath;

    /**
     * Fonction principale pour l'execution des tests
     *
     * @return void
     */
    public function execute()
    {
        // Inclusion des librairies Gedooo
        include_once Configure::read("WEBDELIB_PATH") . DS . 'Vendor' . DS . 'GEDOOo' . DS . 'phpgedooo' . DS . 'GDO_PartType.class';
        include_once Configure::read("WEBDELIB_PATH") . DS . 'Vendor' . DS . 'GEDOOo' . DS . 'phpgedooo' . DS . 'GDO_ContentType.class';
        include_once Configure::read("WEBDELIB_PATH") . DS . 'Vendor' . DS . 'GEDOOo' . DS . 'phpgedooo' . DS . 'GDO_FusionType.class';

        // Initialisations
        $this->_textesInError = array();

        // Chemins fichiers & dossiers
        $this->textePath = Configure::read("WEBDELIB_PATH") . DS . 'tmp' . DS . 'files' . DS . 'textes';
        $this->logPath = Configure::read("WEBDELIB_PATH") . DS . "tmp" . DS . "logs" . DS . "gedooo.log";
        $this->modelPath = Configure::read("WEBDELIB_PATH") . DS . WEBROOT_DIR . DS . 'files' . DS . 'model_annexe_test.odt';

        // Instanciations
        $this->_textesFolder = new Folder($this->textePath, true);
        $this->_logFile = new File($this->logPath, true, 0644);
        $this->_modelFile = new File($this->modelPath, false);
    }

    /**
     * Test des annexes 
     * Pour les annexes des délibérations de séances en cours et sans séance
     * Génère un fichier texte avec les colonnes suivantes :
     * # seance_id delib_id annex_id result
     *
     * @param string $mode Mode de test des annexes {'all','noseance','nontraitees'}
     * @param string $logPath Chemin du fichier de log personnalisable
     *
     */
    public function testTextes($mode = 'all', $id = 'null' , $logPath = null)
    {
        if ($logPath != null)  $this->logPath = $logPath;

        if ($mode != 'id')
            // Charge les délibs et annexes depuis la base de données
            $delibsWithAnnexes = $this->_getDelibsWithAnnexes();

        // filtre les annexes (scope selon argument $mode)
        switch ($mode) {
            case 'nontraitees':
                $projets = $this->_getProjetsSeanceEnCours($delibsWithAnnexes);
                break;

            case 'noseance':
                $projets = $this->_getProjetsSansSeance($delibsWithAnnexes);
                break;

            case 'id':
                $projets = $this->_getProjetsParSeanceId($id);
                break;

            default: // case 'all'
                $projets = array_merge($this->_getProjetsSeanceEnCours($delibsWithAnnexes), $this->_getProjetsSansSeance($delibsWithAnnexes));
                break;
        }
        $nbProjets = count($projets);
        $nbAnnexes = $this->countAnnexesForDelibs($projets);
        if (!empty($nbProjets) || !empty($nbAnnexes)) {
            $this->out("\n<important>Nombre de projets à tester : $nbProjets ...</important>");
            $this->out("<important>Nombre d'annexes à tester : $nbAnnexes ...</important>\n");
            $p = 0;
            //Pour chaque projet
            foreach ($projets as $projet) {
                $p++;
                $hasTexte = false;
                $this->out("<info>$p/$nbProjets -> Test du projet ".$projet['Deliberation']['id'].' "' . $projet['Deliberation']['objet_delib'] . "\"...</info>\n", 0);

                // Envoi des textes à gedooo
                if (!empty($projet['Deliberation']['texte_projet'])) {
                    $hasTexte = true;
                    $this->out("* Texte de projet " . $projet['Deliberation']['texte_projet_name'] . ' ... ', 0);
                    $result = $this->_sendToGedooo($projet['Deliberation']['texte_projet'], $projet['Deliberation']['texte_projet_name'], $projet['Deliberation']['id'], 'Deliberation', 'texte_projet');
                    // Log du résultat
                    $this->_ajouterLigneAuRapport($projet['Deliberation']['id'], 'Deliberation', 'texte_projet', $result);
                }
                if (!empty($projet['Deliberation']['texte_synthese'])) {
                    $hasTexte = true;
                    $this->out("* Texte de synthèse " . $projet['Deliberation']['texte_synthese_name'] . ' ... ', 0);
                    $result = $this->_sendToGedooo($projet['Deliberation']['texte_synthese'], $projet['Deliberation']['texte_synthese_name'], $projet['Deliberation']['id'], 'Deliberation', 'texte_synthese');
                    // Log du résultat
                    $this->_ajouterLigneAuRapport($projet['Deliberation']['id'], 'Deliberation', 'texte_synthese', $result);
                }
                if (!empty($projet['Deliberation']['deliberation'])) {
                    $hasTexte = true;
                    $this->out("* Texte de délibération " . $projet['Deliberation']['deliberation_name'] . ' ... ', 0);
                    $result = $this->_sendToGedooo($projet['Deliberation']['deliberation'], $projet['Deliberation']['deliberation_name'], $projet['Deliberation']['id'], 'Deliberation', 'deliberation');
                    // Log du résultat
                    $this->_ajouterLigneAuRapport($projet['Deliberation']['id'], 'Deliberation', 'deliberation', $result);

                }
                foreach ($projet['Annex'] as $annexe) {
                    $hasTexte = true;
                    $this->out("* Annexe " . $annexe['filename'] . ' (id: ' . $annexe['id'] . ', délibération: ' . $projet['Deliberation']['id'] . ')... ', 0);
                    // Envoi de l'annexe à gedooo
                    $result = $this->_sendToGedooo($annexe['data'], $annexe['filename'], $annexe['id'], 'Annexe', 'data');
                    // Log du résultat
                    $this->_ajouterLigneAuRapport($annexe['id'], 'Annexe', 'data', $result);
                } // Fin foreach annexe
                if (!$hasTexte)
                    $this->out("Aucun texte associé !\n", 0);

            } // Fin foreach projet

            try {
                // Création du fichier de rapport
                $this->_creerRapport();
            } catch (Exception $exc) {
                $this->out('<error>Erreur fichier journal : ' . $exc->getMessage() . '</error>');
            }
        } else
            $this->out("\n<info>Aucun texte à tester !</info>\n");

        $this->_textesFolder->delete();

        return $this->_textesInError;
    }

    /**
     * Effectue une requête afin de récupérer un tableau de délibération avec annexes et séances,
     * Ce tableau est utilisé par les fonctions _getProjetsSeanceEnCours et _getProjetsSansSeance
     *
     * @see _getProjetsSeanceEnCours 
     * @see _getProjetsSansSeance
     * @return array Tableau de délibérations contenant Annexes et Séances 
     */
    private function _getDelibsWithAnnexes()
    {
        $this->out("<info>Chargement des projets depuis la base de données...</info>");
        $time_start = microtime(true);

        $this->Deliberation->Behaviors->attach('Containable');
        $options = array(
            'fields' => array('id', 'objet_delib', 'texte_projet', 'texte_projet_name', 'texte_synthese', 'texte_synthese_name', 'deliberation', 'deliberation_name'),
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
     *
     * @params $delibs les délibs à filtrer
     *
     * @return array Les projets de séances en cours
     */
    private function _getProjetsSeanceEnCours($delibs)
    {
        $this->out("<info>Sélection des projets attachées à une séance à traiter...</info>");
        $time_start = microtime(true);

        $projets = array();
        //Pour chaque délib
        foreach ($delibs as $delib) {
            $controler = false;
            //Si la délib possède une séance
            if (!empty($delib['Deliberationseance'])) {
                // Parcourir les séances et si une d'elle n'est pas traitée (non vide) contrôller
                foreach ($delib['Deliberationseance'] as $deliberationseance) {
                    if (!empty($deliberationseance['Seance'])) {
                        foreach ($deliberationseance['Seance'] as $seance) {
                            if ($seance['traitee'] == 0) {
                                $controler = true;
                                break;
                            }
                        }
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

    /**
     * Filtre parmis les enregistrements ceux qui ne sont attachés à aucune séance
     *
     * @params $delibs tableau de délibérations
     *
     * @return array annexes Les annexes des délibérations sans séance
     */
    private function _getProjetsSansSeance($delibs)
    {
        $this->out("<info>Sélection des projets sans séance...</info>");
        $time_start = microtime(true);

        $projets = array();
        //Pour chaque délib
        foreach ($delibs as $delib)
        //Si le projet ne possède pas de séance
            if (empty($delib['Deliberationseance']))
            // Ajouter toutes les annexes au tableau d'annexes à tester
                $projets[] = $delib;

        $time_end = microtime(true);
        $this->out('<time>Durée : ' . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $projets;
    }


    /**
     * @params $seanceId id de la séance contenant les textes à tester
     *
     * @return array projets
     */
    private function _getProjetsParSeanceId($seanceId)
    {
        $this->out("<info>Sélection des projets de la séance $seanceId...</info>");
        $time_start = microtime(true);

        $options = array(
            'recursive' => -1,
            'fields' => array('deliberation_id'),
            'conditions' => array('seance_id' => $seanceId)
        );

        $delibseance = $this->Deliberationseance->find('all', $options);
        $delibsid = array();
        foreach ($delibseance as $delibid)
            $delibsid[] = $delibid['Deliberationseance']['deliberation_id'];

        $this->Deliberation->Behaviors->attach('Containable');
        $delibs = $this->Deliberation->find('all', array(
            'conditions' => array('Deliberation.id' => $delibsid),
            'fields' => array(
                'Deliberation.id',
                'Deliberation.objet_delib',
                'Deliberation.texte_projet',
                'Deliberation.texte_projet_name',
                'Deliberation.texte_synthese',
                'Deliberation.texte_synthese_name',
                'Deliberation.deliberation',
                'Deliberation.deliberation_name'),
            'contain' => array(
                'Annex' => array(
                    'fields' => array(
                        'Annex.id',
                        'Annex.filename',
                        'Annex.filetype',
                        'Annex.data'
                    ),
                    'conditions' => array('filetype' => array("application/vnd.oasis.opendocument.text", "application/pdf"))
        ))));

        $projets = array();
        foreach ($delibs as $delib)
            $projets[] = $delib;

        $time_end = microtime(true);
        $this->out('<time>Durée : ' . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $projets;
    }

    /**
     * Ajoute une ligne avec les informations sur l'annexe à la variable $_rapport
     *
     * @param integer $id           numéro de délib
     * @param string  $model        nom du model
     * @param string  $column       nom de la colonne dans la base de données
     * @param string  $retourGedooo Informations sur le passage de l'annexe dans gedooo et ses propriétés
     *
     * @return void
     */
    private function _ajouterLigneAuRapport($id, $model, $column, $retourGedooo)
    {
        $this->_rapport .=
                date("d-m-Y H:i:s") . "\t"
                . $id . "\t"
                . $model . "\t"
                . $column . "\t"
                . $retourGedooo . "\n";
    }

    /**
     * Envoyer le rapport de l'éxecution au fichier de log
     *
     * @throws Exception si problème d'accès disque
     *
     * @return string sortie standard
     */
    private function _creerRapport()
    {
        $this->out('<info>Création du rapport ('.$this->logPath.')...</info>');
        $time_start = microtime(true);

        if ($this->_logFile->writable()) {
            if ($this->_logFile->open('w')) {
                $this->_rapport = $this->_logFile->prepare($this->_rapport);
                $this->_logFile->append($this->_rapport);
                $this->_logFile->close();
            } else {
                throw new Exception("Impossible d'ouvrir le fichier " . $this->_logFile->path);
            }
        } else {
            throw new Exception("Impossible d'écrire dans le fichier " . $this->_logFile->path);
        }

        $time_end = microtime(true);
        $this->out('<time>Durée : ' . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);
    }

    /**
     * Envoi d'un fichier à Gedooo pour tester le retour
     *
     * @param $content
     * @param $filename
     * @param $id
     * @param $modelName
     * @param $column
     *
     * @return string retour {ok,code_erreur}
     */
    private function _sendToGedooo($content, $filename, $id, $modelName, $column)
    {

        $time_start = microtime(true);

        //Initialisations
        $mimetype = 'application/vnd.oasis.opendocument.text';


        // Partie principale du document
        $oMainPart = new GDO_PartType();
        $oMainPart->addElement(
            new GDO_ContentType('fichier', $filename, $mimetype, 'binary', $content)
        );
        // Model 
        $oTemplate = new GDO_ContentType("", $this->_modelFile->path, $mimetype, "binary", $this->_modelFile->read());

        //Fusion
        $oFusion = new GDO_FusionType($oTemplate, $mimetype, $oMainPart);
        $oFusion->process();
        $newFile = new File($this->_textesFolder->path . DS . $filename . '.odt', false);
        try {
            //Conversion et concaténation
            $oFusion->SendContentToFile($newFile->path);
            if(!$newFile->delete())
                throw new Exception("Problème lors de la suppression du fichier :\n" . $newFile->path);
            $retourGedooo = "OK";
            $this->out('<info>' . $retourGedooo . '</info>');
        } catch (Exception $exc) {
            $retourGedooo = "KO";
            $this->out('<info>' . $retourGedooo . '</info>');
            $this->out("<warning>" . $exc->getMessage() . "</warning>");
            //$this->out("<warning>Trace :\n" . $exc->getTraceAsString() . "</warning>");
            $this->_textesInError[] = array(
                'id' => $id,
                'model' => $modelName,
                'filename' => $filename,
                'column' => $column
            );
        }
       
        $time_end = microtime(true);
        $this->out("<time>Durée : " . round($time_end - $time_start, 2) . 's</time>', 1, Shell::VERBOSE);

        return $retourGedooo;
    }

    /**
     * Compte les annexes contenu par les délibs
     *
     * @param Deliberation $delibs délibs avec annexes à compter
     *
     * @return int
     */
    public function countAnnexesForDelibs($delibs)
    {
        $nbAnnexes = 0;
        foreach ($delibs as $projet)
            $nbAnnexes += count($projet['Annex']);
        return $nbAnnexes;
    }

}
