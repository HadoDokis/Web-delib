<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class InstallShell extends AppShell {

    public $tasks = array('Sql');

    public $version;

    public function main() {

        // Création de styles perso
        $this->stdout->styles('time', array('text' => 'magenta'));
        $this->stdout->styles('important', array('text' => 'red', 'bold' => true));

        if (!file_exists(APP . 'VERSION.txt')) {
            $this->out("<error>Le fichier VERSION.txt est introuvable</error>");
            $this->out("\n<important>Annulation de l'installation</important>");
            return;
        }
        $this->version = file(APP . DS . 'VERSION.txt');

        $this->install($this->params);

        if ($this->params['install-demo']) {
            $this->injectDemoRecords();
        }
    }

    /**
     * Options d'éxecution et validation des arguments
     *
     * @return Parser $parser
     */
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->description(__('Installateur Webdelib'));

        $parser->addOption('install-demo', array(
            'short' => 'd',
            'help' => __('Installe le jeu de données de démo.'),
            'boolean' => true
        ));

        $parser->addOption('reinstall', array(
            'short' => 'r',
            'help' => __('Néttoie les données dans la bases avant d\'installer.'),
            'boolean' => true
        ));

        $parser->addOption('no-transac', array(
            'short' => 'd',
            'help' => __('Désactive le mode transactionnel du passage des scripts sql.'),
            'boolean' => true
        ));

        $parser->addOption('skip-errors', array(
            'short' => 's',
            'help' => __('Continu l\'éxecution du script même si une erreur est levée'),
            'boolean' => true
        ));

        return $parser;
    }

    public function install($options) {
        $success = true;
        $sql_files = array();
        $clean = $options['reinstall'];
        $notransac = $options['no-transac'];
        $noerrors = $options['skip-errors'];
        if ($clean)
            $notransac = $noerrors = true;

        if (!$clean){
            $sql_files['web-delib v'.$this->version] = APP . 'Config' . DS . 'Schema' . DS . 'webdelib-v4.2.sql';
            $sql_files['Plugin : Cakeflow v3.1.01'] = APP.'Plugin'.DS.'Cakeflow'.DS.'Config'.DS.'Schema'.DS.'patchs'.DS.'3.1_to_3.1.01.sql';
            $sql_files['Plugin : ModelOdtValidator v1.0.01'] = APP.'Plugin'.DS.'ModelOdtValidator'.DS.'Config'.DS.'Schema'.DS.'patchs'.DS.'1.0_to_1.0.01.sql';
        }
        else
            $sql_files['web-delib v4.2 purge'] = APP . 'Config' . DS . 'Schema' . DS . 'patchs' . DS . 'purges_delibs.sql';

        $this->out("<important>Installation de Webdelib v" . $this->version . "</important>\n");

        // Passage des scripts sql de migration
        $this->Sql->execute();
        if (!$notransac)
            $this->Sql->begin();
        $this->out("\nInstallation des données... Veuillez patienter...");
        foreach ($sql_files as $id => $sql) {
            $res = $noerrors ? $this->Sql->runSkipErrors($sql) : $this->Sql->run($sql);
            if (!$res) {
                $this->out("\n<error>Erreur levée durant l'éxecution du fichier sql de $id</error>");
                if (!$notransac)
                    $this->Sql->rollback();
                $success = false;
                break;
            }
        }

        if ($success) {
            if (!$notransac)
                $this->Sql->commit();
            $this->footer('<important>Installation de Webdelib v' . $this->version . ' accompli avec succès !</important>');
        } else
            $this->footer("\n<important>Annulation de l'installation !</important>");
    }

    /**
     * TODO
     * Installe les données de démo dans la base de données
     */
    public function injectDemoRecords() {
        $this->out("\n<error>Données de démonstration inexistantes !</error>");
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
