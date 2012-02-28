<?php

    App::import(array('Model', 'AppModel', 'File'));
    App::import(array('Model', 'Deliberation', 'File'));
    App::import(array('Model', 'Seance', 'File'));

    class DecisionShell extends Shell{

        var $uses = array ('Deliberation', 'Seance');
        function main () {
            $root_path = Configure::read('STOCK_PATH').DS.'fichiers'.DS;
            if (!file_exists($root_path)) {
                if(mkdir($root_path)) {
                    echo ("Création du répertoire : $root_path");
                }
            } 
            $result = true;
            define ('CRON_DISPATCHER', true);

            $seances =  $this->Seance->find('all');

            foreach($seances as $seance) {
                $seance_id = $seance['Seance']['id'];
                $seance_path = $root_path.DS.$seance_id.DS;
                if (!file_exists($seance_path)) {
                    if(mkdir($seance_path)) {
                        echo ("####################################\n");
                        echo ("Création du répertoire : $seance_path -> $result");
                    }
                }
                $delibs = $this->Deliberation->find("all", array('conditions'=>array("Deliberation.seance_id"=> $seance_id)));
                foreach($delibs as $delib) {
                    $delib_id =  $delib['Deliberation']['id'];
                    $delib_path = $seance_path.$delib_id.DS;
                    $annex_path = $seance_path.$delib_id.DS.'annexes'.DS;
                    if (!file_exists($delib_path)) {
                        if(mkdir($delib_path)) {
                            echo ("Création du répertoire : $delib_path");
                        }
                    }
                    if (!file_exists($annex_path)) {
                        if(mkdir($annex_path)) {
                            echo ("Création du répertoire : $annex_path");
                        }
                    }

                    file_put_contents($delib_path.'texte_projet.odt', $delib['Deliberation']['texte_projet']);
                    file_put_contents($delib_path.'texte_synthese.odt', $delib['Deliberation']['texte_synthese']);
                    file_put_contents($delib_path.'texte_deliberation.odt', $delib['Deliberation']['deliberation']);
                    file_put_contents($delib_path.'debat.odt', $delib['Deliberation']['debat']);
                    file_put_contents($delib_path.'deliberation.pdf', $delib['Deliberation']['delib_pdf']);

                    echo ("Acte enregistré\n");
                    echo ("-----------------------------------\n");
                }                
            }
            if ($result)
                echo ("Sauvegarde réussie\n");
            else 
                echo ("Sauvegarde échouée\n");
            
        }
	
}
?>
