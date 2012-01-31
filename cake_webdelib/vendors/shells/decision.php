<?php

    App::import(array('Model', 'AppModel', 'File'));
    App::import(array('Model', 'Deliberation', 'File'));

    class DecisionShell extends Shell{

        var $uses = array ('Deliberation');
        function main () {
            $result = true;
            $seances = array(24, 27);

            $_SERVER['HTTP_HOST'] = 'webdelib.test.adullact.org';
            define ('CRON_DISPATCHER', true);
            foreach($seances as $seance_id) {
                echo ("####################################\n");
                echo ("Stockage des acte de la séance : $seance_id\n");
                echo ("####################################\n\n\n");

                $delibs = $this->Deliberation->find("all", array('conditions' => array("Deliberation.seance_id"=>$seance_id),
                                                              'order'      => "Deliberation.position ASC"));
                foreach($delibs as $delib) {
                    $delib_id =  $delib['Deliberation']['id'];
                    $this->Deliberation->id =  $delib['Deliberation']['id'];
                    echo ("Stockage de l'acte : $delib_id\n");
                    $model_id = $this->Deliberation->getModelId($delib_id);
                    echo ("Utilisation du modèle : $model_id\n");
                    $err = $this->requestAction("/models/generer/$delib_id/null/$model_id/0/1/D_$delib_id.odt");
                    $filename =  WEBROOT_PATH."/files/generee/fd/null/$delib_id/D_$delib_id.odt.pdf";
                    $content = file_get_contents($filename);

                    if (strlen($content) == 0)
                        $result = false;
                    // On stock le fichier en base de données.
                    if ($this->Deliberation->saveField('delib_pdf', $content))
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
