<?php

//App::import('Core', 'Router');

class CronShell extends Shell {

    var $uses = array('Traitement');
    function startup() {
        
    }

    function main() {

        // initialisation de la ressource curl
        $c = curl_init();
        // indique à curl quelle url on souhaite télécharger
        curl_setopt($c, CURLOPT_URL, FULL_BASE_URL . "/crons/runCrons");
        // indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour
        curl_setopt($c, CURLOPT_HEADER, false);
        // Display communication with server
        curl_setopt($c, CURLOPT_VERBOSE, false); 
        // indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        // execution de la requete
        $output = curl_exec($c);
        if ($output === false) {
            trigger_error('Erreur curl : ' . curl_error($c), E_USER_WARNING);
        } else {
            $this->out($output);
        }
        curl_close($c);
    }
}

?>