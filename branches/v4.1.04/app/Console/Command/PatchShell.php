<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class PatchShell extends AppShell {

    public $tasks = array(
        'Sql',
        'Cakeflow',
        'Tdt', // 4.1.01 => 4.1.02
        'Gedooo', // 4.1.02 => 4.1.03
    );
    public $uses = array('Annex', 'Deliberation');

    public function main() {
        $this->out('Script de patch de Webdelib');
        // Désactivation du cache
        Configure::write('Cache.disable', true);
        // Création de styles perso
        $this->stdout->styles('time', array('text' => 'magenta'));
        $this->stdout->styles('important', array('text' => 'red', 'bold' => true));
        
        // Quelle version installer ? test des arguments
        switch ($this->command) {
            case "4101to4102":
                $this->Version_4101to4102();
                break;

            case "4102to4103":
                $this->Version_4102to4103();
                break;
            
            case "4103to4104":
                $this->Version_4103to4104();
                break;

            case null: // Pas de commande
                $this->out("\n<error>Un nom de patch est nécessaire, tapez 'Console/cake patch -h' pour afficher l'aide.</error>\n");
                break;

            default : // Commande inconnue
                $this->out("\n<error>Commande '" . $this->command . "' inconnue, nom de patch attendu, tapez 'Console/cake patch -h' pour afficher l'aide.</error>\n");
        }
        
        /* // Solution alternative plus élégante mais où il faut correctement nommer les fonctions
        if (method_exists($this, $this->command))
            $this->runCommand($this->command, $this->args);
        else
            $this->out("erreur");
        */
    }

    /**
     * Options d'éxecution et validation des arguments
     *
     * @return Parser $parser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description(__('Commandes de mise à jour de webdelib.'));

        $parser->addSubcommand('4101to4102', array(
            'help' => __('Application du patch de mise à jour de 4.1.01 à 4.1.02.'),
            'parser' => array(
                'options' => array(
                    'PDFtoODT' => array(
                        'help' => __('Conversion PDFtoODT.'),
                        'required' => false,
                        'short' => 'o',
                        'boolean' => true
                    ),
                    'classification' => array(
                        'help' => __('Mise à jour de la classification.'),
                        'required' => false,
                        'short' => 'c',
                        'boolean' => true
                    ),
                    'num_pref' => array(
                        'help' => __('Mise à jour des num_pref'),
                        'required' => false,
                        'short' => 'n',
                        'boolean' => true
                    )
                )
            )
        ));
        
        $parser->addSubcommand('4102to4103', array(
            'help' => __('Application du patch de mise à jour de 4.1.02 à 4.1.03.'),
            'parser' => array(
                'options' => array(
                    'classification' => array(
                        'name' => 'classification',
                        'required' => false,
                        'short' => 'c',
                        'help' => 'Mise à jour de classification.',
                        'boolean' => true
                    ),
                    'textes' => array(
                        'name' => 'textes',
                        'required' => false,
                        'short' => 't',
                        'help' => 'Tests à effectuer (projets et annexes)',
                        'choices' => array('all', 'noseance', 'nontraitees'),
                        'default' => 'all'
                    )
                )
            )
        ));
        
        $parser->addSubcommand('4102to4103', array(
            'help' => __('Application du patch de mise à jour de 4.1.03 à 4.1.04.'),
            'parser' => array(
                'options' => array(
                    'classification' => array(
                        'name' => 'classification',
                        'required' => false,
                        'short' => 'c',
                        'help' => 'Mise à jour de classification.',
                        'boolean' => true
                    ),
                    'Cakeflow3001to3002' => array(
                        'name' => 'Cakeflow3001to3002',
                        'required' => false,
                        'short' => 'u',
                        'help' => 'Mise à jour de Cakeflow',
                        'boolean' => true
                    )
                )
            )
        ));
        
        return $parser;
    }

    /** Mise à jour de la version 4.1.02 à la version 4.1.03
     * Génération des annexes en odt valide, Mise à jour de classification, 
     * Changement du num préfecture
     */
    public function Version_4102to4103()
    {
        $errors = array();
        $warnings = array();
        $logPath = Configure::read("WEBDELIB_PATH") . DS . "tmp" . DS . "logs" . DS . "gedooo.log";
        $success = true;
        $this->out("\n<important>Démarrage du patch de mise à jour de Webdelib 4.1.02 vers 4.1.03...</important>\n");
        $time_start = microtime(true);

        $this->Gedooo->execute();
        
        $textesInError = $this->Gedooo->testTextes($this->params['textes']);
        if (!empty($textesInError)) {
            $error_msg = "Textes non conformes : \n";
            foreach ($textesInError as $texte) {
                $error_msg .= "\t# ". $texte['model']." " . $texte['id'] . ' : \'' . $texte['filename'] . '\' (colonne: ' . $texte['column'] . ")\n";
            }
            $warnings[] = "Des textes de projet peuvent causer des problèmes (voir : $logPath).";
            $this->out("\n<error>$error_msg</error>");
        } else {
            $this->out("\n<info>Tous les textes sont conformes !!</info>");
        }

        $time_end = microtime(true);
        $this->out("<time>Temps écoulé durant la phase de test des textes : " . round($time_end - $time_start) . ' secondes</time>', 1, Shell::VERBOSE);

        // Message avertissant l'utilisateur de l'emplacement du fichier log
        //$this->out("\n<important>Emplacement fichier log Gedooo : " . $this->Gedooo->logPath . "</important>\n");

        //Mise à jour de la classification
        if (!empty($this->params['classification'])) {
            if (Configure::read("USE_S2LOW")) {
                $this->out('<info>Mise à jour classification...</info>');
                $success = $this->Tdt->classification() && $success;
                if ($success)
                    $this->out('<info>Mise à jour de la classification Terminée</info>');
                else
                    $this->out('<warning>Warning : Problème lors de la mise à jour de la classification !!</warning>');
            }
            else
                $warnings[] = '<warning>Warning : l\'utilisation de S2LOW est désactivée (voir fichier webdelib.inc), mise à jour de la classification impossible...</warning>';
        }
        
        if (!empty($warnings)){
            $this->out("\n<warning>Avertissements : </warning>");
            foreach ($warnings as $warning) {
                $this->out("\t<warning>* ".$warning.'</warning>');
            }
        }

        if ($success) {
            $this->footer('<important>Patch de la version 4.1.02 vers la 4.1.03 accompli avec succès !</important>');
//            $this->footer('<comment>Fin de l\'éxecution du patch 4.1.03</comment>');
        }
        else
            $this->footer('<error>Erreur : un problème est survenu lors de l\'application du patch !!</error>');
        
    }

    /* Mise à jour de la version 4.1.01 à la version 4.1.02
     * Génération des annexes en odt valide, Mise à jour de classification, 
     * Changement du num préfecture
     */

    public function Version_4101to4102() {
        $success = true;
        $this->out("Patch Processing...\n");
        $collection = new ComponentCollection();
        $action = 0;

        //Génération des annexes en odt valide
        if (!empty($this->params['PDFtoODT'])) {
            $action++;
            App::uses('PdfComponent', 'Controller/Component');
            $this->out((isset($this->params['option']) ? '' : '[1]') . "Migration des pdf en odt...");
            $this->Pdf = new PdfComponent($collection);

            $annexes = $this->Annex->find('all', array('fields' => array('id', 'filename', 'filename_pdf', 'data_pdf'),
                'order' => 'id ASC',
                'recursive' => -1));

            $i = 0;
            foreach ($annexes as $annexe) {
                if (strpos($annexe['Annex']['filename'], 'odt') === false)
                    continue;

                $this->out('Generation ' . $annexe['Annex']['id'] . '...');

                $i++;
                $newAnnexe['Annex']['id'] = $annexe['Annex']['id'];
                $outputDir = tempdir();
                $folder = new Folder($outputDir);
                $file = new File($outputDir . $annexe['Annex']['id'] . '.pdf', false);
                $file->write($annexe['Annex']['data_pdf']);
                $newAnnexe['Annex']['data'] = $this->Pdf->toOdt($file->pwd());
                $newAnnexe['Annex']['filetype'] = 'application/pdf';
                $newAnnexe['Annex']['size'] = $file->size();

                $file->delete();
                $file->close();
                $folder->delete();
                $success = $this->Annex->save($newAnnexe['Annex']) & $success;
                if (!$success)
                    $this->out('<error>Erreur sur la sauvegarde : ' . $annexe['id'] . "<error>\n");
                else
                    $this->out('Sauvegarde Terminée : ' . $annexe['Annex']['id'] . "\n");
            }

            $this->out((isset($this->params['option']) ? '' : '[1]') . 'Migration des pdf en odt Terminée (' . $i . ' modifications');
        }

        //Mise à jour de la classification
        if (!empty($this->params['classification'])) {
            $action++;
            $this->out((isset($this->params['option']) ? '' : '[2]') . 'Mise à jour classification...');
            $success = $this->Tdt->classification() & $success;
            if ($success)
                $this->out((isset($this->params['option']) ? '' : '[2]') . 'Mise à jour classification Terminée');
            else
                $this->out('<error>Erreur</error> : Mise à jour classification !!');
        }

        //Mise à jour num préfecture
        if (!empty($this->params['num_pref'])) {
            $action++;
            $this->out((isset($this->params['option']) ? '' : '[3]') . 'Mise à jour numéro Préfecture...');
            App::uses('DeliberationsController', 'Controller');
            $this->Delib = new DeliberationsController($collection);
            $deliberations = $this->Deliberation->find('all', array('fields' => array('id', 'num_pref'),
                'recursive' => -1));

            foreach ($deliberations as $deliberation) {
                $this->out('Migration deliberation : ' . $deliberation['Deliberation']['id'] . '...');
                $num_pref = strstr($deliberation['Deliberation']['num_pref'], ' - ', true);
                if (isset($num_pref) && !empty($num_pref)) {
                    $this->Deliberation->id = $deliberation['Deliberation']['id'];
                    $success = $this->Deliberation->saveField('num_pref', $num_pref) & $success;
                    if (!$success)
                        $this->out("<error>Erreur</error> sur la sauvegarde : " . $deliberation['Deliberation']['id'] . "\n");
                    else
                        $this->out('Sauvegarde Terminée : ' . $deliberation['Deliberation']['id'] . "\n");
                }
                else
                    $this->out('Rien à faire : ' . $deliberation['Deliberation']['id'] . "\n");
            }

            if ($success)
                $this->out((isset($this->params['option']) ? '' : '[3]') . 'Mise à jour numéro Préfecture Terminée');
            else
                $this->out('<error>Erreur</error> : Mise à jour numéro Préfecture !!');
        }

        if ($action === 0) {
            $success = false;
            $this->out('<question>Commande inconnue !!</question>');
        }
        if ($success)
            $this->footer('<info>patch complete<info>');
        else
            $this->footer('<warning>patch incomplete !!</warning>');
    }
    
    /** Mise à jour de la version 4.1.03 à la version 4.1.04
     * Upgrade de Cakeflow, Mise à jour de classification
     */
    public function Version_4103to4104()
    {
        $errors = array();
        $warnings = array();
        $success = true;
        $this->out("\n<important>Démarrage du patch de mise à jour de Webdelib 4.1.03 vers 4.1.04...</important>\n");
        
        if (!empty($this->params['Cakeflow3001to3002'])) {
            $cakeflowSql = APP.DS.'Plugin'.DS.'CakeFlow'.DS.'Config'.DS.'sql'.DS.'patchs'.DS.'cakeflow_v3.0_to_v3.1.sql';
            $this->out("\nMise à jour de la base de données...");
            $this->Sql->execute();
            $this->Sql->begin();

            $success = $success && $this->Sql->run($cakeflowSql);

            if ($success){
                //Commit
                $this->Sql->commit();
                //trouver l'attribut etape_id des visas en cours
                $this->out('Mise à jour des données CakeFlow...');
                $this->Cakeflow->findVisaEtapeId();
            }
            else{
                $this->out("\n<error>Une erreur s'est produite pendant l'installation de la mise à jour (Erreur SQL) !!</error>");
                $this->Sql->rollback();
            }
        }

        //Mise à jour de la classification
        if (!empty($this->params['classification'])) {
            if (Configure::read("USE_S2LOW")) {
                $this->out('<info>Mise à jour classification...</info>');
                $success = $this->Tdt->classification() && $success;
                if ($success)
                    $this->out('<info>Mise à jour de la classification Terminée</info>');
                else
                    $this->out('<warning>Warning : Problème lors de la mise à jour de la classification !!</warning>');
            }
            else
                $warnings[] = '<warning>Warning : l\'utilisation de S2LOW est désactivée (voir fichier webdelib.inc), mise à jour de la classification impossible...</warning>';
        }
        
        if (!empty($warnings)){
            $this->out("\n<warning>Avertissements : </warning>");
            foreach ($warnings as $warning) {
                $this->out("\t<warning>* ".$warning.'</warning>');
            }
        }

        if ($success) {
            $this->footer('<important>Patch de la version 4.1.03 vers la 4.1.04 accompli avec succès !</important>');
        }
        else
            $this->footer('<error>Erreur : un problème est survenu lors de l\'application du patch !!</error>');
        
    }

    /**
     * Affiche un message entouré de deux barres horizontales
     * @param string $var message
     */
    public function footer($var) {
        $this->hr();
        $this->out($var);
        $this->hr();
    }

}

?>
