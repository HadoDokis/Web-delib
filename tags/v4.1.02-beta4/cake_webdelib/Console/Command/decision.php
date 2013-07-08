<?php

    App::import(array('Model', 'AppModel', 'File'));
    App::import(array('Model', 'Deliberation', 'File'));
    App::import(array('Model', 'Seance', 'File'));
    App::import(array('Model', 'Annex', 'File'));
    App::import(array('Model', 'Infosup', 'File'));
    App::import(array('Model', 'Model', 'File'));

    class DecisionShell extends Shell{

        var $uses = array ('Deliberation', 'Seance', 'Annex', 'Infosup', 'Model');
        function main () {
            $root_path = TMP.'backups'.DS;
            $list_files =  $root_path.'liste_fichiers.lst';
            file_put_contents($list_files, '');
            if (!file_exists($root_path)) {
                if(mkdir($root_path)) {
                    echo ("Création du répertoire : $root_path");
                }
            } 
            $result = true;
            define ('CRON_DISPATCHER', true);

            $annexes = $this->Annex->find('all', array('recursive' => -1));
           
            foreach ($annexes as $annexe) {
                $dir = $root_path.'Annex'.DS.$annexe['Annex']['id'].DS;
                if (!file_exists($dir)) {
                    echo("Creation du repertoire : $dir \n");
                    mkdir($dir, 0777, true);
                }
                file_put_contents($dir.'data', $annexe['Annex']['data']);
                file_put_contents($dir.'data_pdf', $annexe['Annex']['data_pdf']);
                file_put_contents($list_files, $dir.'data'."\n",  FILE_APPEND); 
                file_put_contents($list_files, $dir.'data_pdf'."\n",  FILE_APPEND); 
            }

            $actes =  $this->Deliberation->find('all', array('recursive' => -1));
            foreach ( $actes as $acte) {
                $dir = $root_path.'Deliberation'.DS.$acte['Deliberation']['id'].DS;
                if (! file_exists($dir)) {
                    mkdir($dir, 0777, true);
                    echo("Creation du repertoire : $dir \n");
                }
                file_put_contents($dir.'texte_projet', $acte['Deliberation']['texte_projet']);
                file_put_contents($list_files, $dir.'texte_projet'."\n",  FILE_APPEND); 

                file_put_contents($dir.'texte_synthese', $acte['Deliberation']['texte_synthese']);
                file_put_contents($list_files, $dir.'texte_synthese'."\n",  FILE_APPEND); 

                file_put_contents($dir.'deliberation', $acte['Deliberation']['deliberation']);
                file_put_contents($list_files, $dir.'deliberation'."\n",  FILE_APPEND); 

                file_put_contents($dir.'debat', $acte['Deliberation']['debat']);
                file_put_contents($list_files, $dir.'debat'."\n",  FILE_APPEND); 

                file_put_contents($dir.'delib_pdf', $acte['Deliberation']['delib_pdf']);
                file_put_contents($list_files, $dir.'delib_pdf'."\n",  FILE_APPEND); 

                file_put_contents($dir.'bordereau', $acte['Deliberation']['bordereau']);
                file_put_contents($list_files, $dir.'bordereau'."\n",  FILE_APPEND); 
                file_put_contents($dir.'signature', $acte['Deliberation']['signature']);
                file_put_contents($list_files, $dir.'signature'."\n",  FILE_APPEND); 
 
                file_put_contents($dir.'commission', $acte['Deliberation']['commission']);
                file_put_contents($list_files, $dir.'commission'."\n",  FILE_APPEND); 
            }

            $infosups = $this->Infosup->find('all', array('recursive' => -1));
            foreach($infosups as  $infosup) {
                $dir = $root_path.'Infosup'.DS.$infosup['Infosup']['id'].DS;
                if (! file_exists($dir)) {
                    mkdir($dir, 0777, true);
                    echo("Creation du repertoire : $dir \n");
                }
                file_put_contents($dir.'content', $infosup['Infosup']['content']);
                file_put_contents($list_files, $dir.'content'."\n",  FILE_APPEND); 
            }

            $models =  $this->Model->find('all', array('recursive' => -1));
            foreach($models as $model) {
                $dir = $root_path.'Model'.DS.$model['Model']['id'].DS;
                if (! file_exists($dir)) {
                    mkdir($dir, 0777, true);
                    echo("Creation du repertoire : $dir \n");
                }
                file_put_contents($dir.'content', $model['Model']['content']);
                file_put_contents($list_files, $dir.'content'."\n",  FILE_APPEND);
            }

            $seances =  $this->Seance->find('all', array('recursive' => -1));
            foreach($seances as $seance) {
                $dir = $root_path.'Seance'.DS.$seance['Seance']['id'].DS;
                if (! file_exists($dir)) {
                    mkdir($dir, 0777, true);
                    echo("Creation du repertoire : $dir \n");
                }
                file_put_contents($dir.'debat_global', $seance['Seance']['debat_global']);
                file_put_contents($list_files, $dir.'debat_global'."\n",  FILE_APPEND);
             
                file_put_contents($dir.'pv_sommaire', $seance['Seance']['pv_sommaire']);
                file_put_contents($list_files, $dir.'pv_sommaire'."\n",  FILE_APPEND);

                file_put_contents($dir.'pv_complet', $seance['Seance']['pv_complet']);
                file_put_contents($list_files, $dir.'pv_complet'."\n",  FILE_APPEND);
            }
        }
	
}
?>
