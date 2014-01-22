<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class MaintenanceShell extends AppShell {

    public $tasks = array(
        'Tdt',
        'Gedooo'
    );
    public $uses = array('Annex', 'Deliberation');

    public function main() {
//        $this->out('Script de patch de Webdelib');
    }

    /**
     * Options d'éxecution et validation des arguments
     *
     * @return Parser $parser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description(__('Commandes de test webdelib.'));

//        $parser->addArgument('seance', array(
//            'help' => 'quelles séances tester',
//            'required' => true,
//            'choices' => array('seance', 'aco')
//        ));

        $parser->addSubcommand('seance', array(
            'help' => __('Test les textes de seances webdelib.'),
            'parser' => array(
                'options' => array(
                    'id' => array(
                        'name' => 'id',
                        'required' => false,
                        'short' => 'i',
                        'help' => 'Test les textes d\'une séance donnée par son id.'
                    ),
                    'group' => array(
                        'name' => 'group',
                        'required' => false,
                        'short' => 'g',
                        'help' => 'Test les textes d\'un ensemble de séances.',
                        'choices' => array('all', 'nonaffectees', 'nontraitees'),
//                        'default' => 'all'
                    )
                )
            )
        ));

        return $parser;
    }

    /**
     * Vérifie la compatibilité des textes en base avec Gedooo,
     * - textes de projets valides ?
     * - annexes en odt valide ?
     */
    public function seance()
    {
        // Création de styles perso
        $this->stdout->styles('time', array('text' => 'magenta'));
        $this->stdout->styles('important', array('text' => 'red', 'bold' => true));

        $errors = array();
        $warnings = array();
        $logPath = Configure::read("WEBDELIB_PATH") . DS . "tmp" . DS . "logs" . DS . "gedooo.log";
        $success = true;
        $this->out("\n<important>Démarrage des tests...</important>\n");
        $time_start = microtime(true);

        $this->Gedooo->execute();

        if (!empty($this->params['id']))
            $textesInError = $this->Gedooo->testTextes('id', $this->params['id']);
        elseif (!empty($this->params['group']))
            $textesInError = $this->Gedooo->testTextes($this->params['group']);
        else
        if (!isset($textesInError))
            $this->out("<error>Veuillez déclarer la cible des tests ! (-h pour obtenir de l'aide)</error>");
        elseif (!empty($textesInError)) {
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

        if (!empty($warnings)){
            $this->out("\n<warning>Avertissements : </warning>");
            foreach ($warnings as $warning) {
                $this->out("\t<warning>* ".$warning.'</warning>');
            }
        }

        if ($success)
            $this->footer('<important>Tests accomplis avec succès !</important>');
        else
            $this->footer('<error>Erreur : un problème est survenu durant les tests !!</error>');
        
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
