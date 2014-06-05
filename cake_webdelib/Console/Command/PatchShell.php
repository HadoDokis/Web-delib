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
        'AjouteSectionAnnexe', // 4.1.xx => 4.2
        'CopyPresidentId' // 4.1.xx => 4.2
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
            case "4103to4104":
                $this->Version_4103to4104();
                break;

            case "41to42": //Modification des modèles, ajout de la section Annexes avec variable fichier
                $this->Version_41to42();
                break;
            
            case "42to4201": //Modification des modèles, ajout de la section Annexes avec variable fichier
                $this->Version_42to4201();
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

        $parser->addSubcommand('4103to4104', array(
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
                    'Schema' => array(
                        'name' => 'Schema',
                        'required' => false,
                        'short' => 'u',
                        'help' => 'Mise à jour du schema de bdd',
                        'boolean' => true
                    )
                )
            )
        ));
        
        $parser->addSubcommand('41to42', array(
            'help' => __('Application du patch de mise à jour de 4.1.04 à 4.2.'),
            'parser' => array(
                'options' => array(
                    'classification' => array(
                        'name' => 'classification',
                        'required' => false,
                        'short' => 'c',
                        'help' => 'Mise à jour de classification.',
                        'boolean' => true
                    ),
                    'Schema' => array(
                        'name' => 'Schema',
                        'required' => false,
                        'short' => 'u',
                        'help' => 'Mise à jour du schema de bdd',
                        'boolean' => true
                    )
                )
            )
        ));
        
        $parser->addSubcommand('42to4201', array(
            'help' => __('Application du patch de mise à jour de 4.2 à 4.2.01.'),
            'parser' => array(
                'options' => array(
                    'classification' => array(
                        'name' => 'classification',
                        'required' => false,
                        'short' => 'c',
                        'help' => 'Mise à jour de classification.',
                        'boolean' => true
                    ),
                    'Schema' => array(
                        'name' => 'Schema',
                        'required' => false,
                        'short' => 'u',
                        'help' => 'Mise à jour du schema de bdd',
                        'boolean' => true
                    )
                )
            )
        ));
                
        return $parser;
    }

    public function Version_41to42()
    {
        $this->out("<important>Mise à jour de Webdelib 4.1.xx => 4.2</important>\n");

        //1° Modification des modèles le necessitant
        $this->out('Recherche des modèles avec jointure des annexes...');
        $this->AjouteSectionAnnexe->execute();

        //2° Passage des scripts sql de migration
        $this->out("\nPassage des patchs de mise à jour de la base de données...");
        $sql_files = array();
        $sql_files['Webdelib42'] = APP.'Config'.DS.'Schema'.DS.'patchs'.DS.'4.1_to_4.2.sql';
        $sql_files['Plugin.ModelOdtValidator.create'] = APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Config'.DS.'Schema'.DS.'create.sql';
        $sql_files['Plugin.ModelOdtValidator.types'] = APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Config'.DS.'Schema'.DS.'modeltypes.sql';
        $sql_files['Plugin.ModelOdtValidator.sections'] = APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Config'.DS.'Schema'.DS.'modelsections.sql';
        $sql_files['Plugin.ModelOdtValidator.variables'] = APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Config'.DS.'Schema'.DS.'modelvariables.sql';
        $sql_files['Plugin.ModelOdtValidator.validations'] = APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Config'.DS.'Schema'.DS.'modelvalidations.sql';
        $sql_files['Plugin.Cakeflow31'] = APP.'Plugin'.DS.'Cakeflow'.DS.'Config'.DS.'Schema'.DS.'patchs'.DS.'cakeflow_v3.0_to_v3.1.sql';
        
        $this->Sql->execute();
        $this->Sql->begin();
        $success = true;
        foreach ($sql_files as $id => $sql){
            if (!$this->Sql->run($sql)){
                $this->out("\n<error>Erreur lors du lancement du fichier sql de $id</error>");
                $this->Sql->rollback();
                $success = false;
                break;
            }
        }
        
        if ($success){
            $this->Sql->commit();
            //4° Copier l'attribut president_id des Séances délibérantes dans les Délibérations associées
            /*$this->out('Copie de l\'attribut president_id des séances vers les délibérations...');
            $this->CopyPresidentId->execute();*/

            $this->footer('<important>Patch de la version 4.1.xx vers la 4.2 accompli avec succès !</important>');
        }else
            $this->footer('<error>Erreur : un problème est survenu lors de l\'application du patch !!</error>');


    }
    
    public function Version_42to4201()
    {
        $this->out("<important>Mise à jour de Webdelib 4.2 => 4.2.01</important>\n");

        //1° Passage des scripts sql de migration
        $this->out("\nPassage des patchs de mise à jour de la base de données...");
        $sql_files = array();
        $sql_files['Webdelib42'] = APP.'Config'.DS.'Schema'.DS.'patchs'.DS.'4.2_to_4.2.01.sql';
        $sql_files['Plugin.Cakeflow3101'] = APP.'Plugin'.DS.'Cakeflow'.DS.'Config'.DS.'Schema'.DS.'patchs'.DS.'cakeflow_v3.1_to_v3.1.01.sql';

        
        $this->Sql->execute();
        $this->Sql->begin();
        $success = true;
        foreach ($sql_files as $id => $sql){
            if (!$this->Sql->run($sql)){
                $this->out("\n<error>Erreur lors du lancement du fichier sql de $id</error>");
                $this->Sql->rollback();
                $success = false;
                break;
            }
        }
        
        if ($success){
            $this->Sql->commit();

            $this->footer('<important>Patch de la version 4.2 vers la 4.2.01 accompli avec succès !</important>');
        }else
            $this->footer('<error>Erreur : un problème est survenu lors de l\'application du patch !!</error>');


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
        
        if (!empty($this->params['Schema'])) {
        $webdelibSql = APP.'Config'.DS.'Schema'.DS.'patchs'.DS.'4.1.03_to_4.1.04.sql';
        $cakeflowSql = APP.'Plugin'.DS.'Cakeflow'.DS.'Config'.DS.'Schema'.DS.'patchs'.DS.'cakeflow_v3.0.01_to_v3.0.02.sql';
        $this->out("\nMise à jour de la base de données...");
        $this->Sql->execute();
        $this->Sql->begin();

        $success = $this->Sql->run($webdelibSql);
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
