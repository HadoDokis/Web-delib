<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class InstallShell extends AppShell {

    public $tasks = array('Sql');

    public $version;

    public function main() {
        if (!file_exists(APP . 'VERSION.txt')) {
            $this->out("<error>Le fichier VERSION.txt est introuvable</error>");
            $this->out("\n<important>Annulation de l'installation</important>");
            return;
        }
        $this->version = file_get_contents(APP . 'VERSION.txt');

        $this->install($this->params['install-like-update']);

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
        $parser->addOption('install-like-update', array(
            'short' => 'u',
            'help' => __('Installe Webdelib en passant les patchs un par un.'),
            'boolean' => true
        ));

        return $parser;
    }

    public function install($likeupdate = false) {
        $success = true;
        $sql_files = array();
        if ($likeupdate) {
            $sql_files['Webdelib v4.1'] = APP . 'Config' . DS . 'Schema' . DS . 'webdelib-v4.1.sql';
            $sql_files['Webdelib 4.1 to 4.1.02'] = APP . 'Config' . DS . 'Schema' . DS . 'patches' . DS . '4.1_to_4.1.02.sql';
            $sql_files['Webdelib 4.1.02 to 4.1.03'] = APP . 'Config' . DS . 'Schema' . DS . 'patches' . DS . '4.1.02_to_4.1.03.sql';
            $sql_files['Webdelib 4.1.03 to 4.1.04'] = APP . 'Config' . DS . 'Schema' . DS . 'patches' . DS . '4.1.03_to_4.1.04.sql';
            $sql_files['Webdelib 4.1 to 4.2'] = APP . 'Config' . DS . 'Schema' . DS . 'patches' . DS . '4.1_to_4.2.sql';
            $sql_files['Cakeflow v3.0'] = APP . 'Plugins' . DS . 'Cakeflow' . DS . 'Config' . DS . 'sql' . DS . 'cakeflow_postgresql_3.0.sql';
            $sql_files['Cakeflow v3.0.01 to v3.0.02'] = APP . 'Plugins' . DS . 'Cakeflow' . DS . 'Config' . DS . 'sql' . DS . 'patches' . DS . 'cakeflow_v3.0.01_to_v3.0.02.sql';
            $sql_files['Cakeflow v3.0 to v3.1'] = APP . 'Plugins' . DS . 'Cakeflow' . DS . 'Config' . DS . 'sql' . DS . 'patches' . DS . 'cakeflow_v3.0_to_v3.1.sql';
            $sql_files['ModelOdtValidator v1'] = APP . 'Plugins' . DS . 'ModelOdtValidator' . DS . 'Schema' . DS . 'FormatValidator-v1.sql';
        } else {
            $sql_files['Webdelib v' . $this->version] = APP . 'Config' . DS . 'Schema' . DS . 'webdelib-v' . $this->version . '.sql';
        }

        $this->out("<important>Installation de Webdelib v" . $this->version . "</important>\n");

        // Passage des scripts sql de migration
        $this->Sql->execute();
        $this->Sql->begin();
        $this->out("\nInstallation des données... Veuillez patienter");
        foreach ($sql_files as $id => $sql) {
            if (!$this->Sql->run($sql)) {
                $this->out("\n<error>Erreur levée durant l'éxecution du fichier sql de $id</error>");
                $this->Sql->rollback();
                $success = false;
                break;
            }
        }

        if ($success) {
            $this->Sql->commit();
            $this->footer('<important>Installation de Webdelib v' . $this->version . ' accompli avec succès !</important>');
        } else
            $this->footer("\n<important>Annulation de l'installation !</important>");
    }

    /**
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
