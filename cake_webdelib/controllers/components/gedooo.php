<?php

class GedoooComponent extends Object {

	/**fonction createFile
	 * $path va indiquer ou créer le fichier
	 * $name sera le nom du fichier
	 * $content est le contenu du fichier
	 *
	 * la fonction va retourner le path ou gedooo pourra aller chercher le fichier
	 */
	function createFile ($path, $name, $content) {
            $this->checkPath($path);
            if (file_exists($path.$name))
                unlink($path.$name);

            if (!$handle = fopen($path.$name, 'a'))
                die("Impossible d'ouvrir le fichier ($path"."$name)");

            if (fwrite($handle, $content) === FALSE)
                die ("Impossible d'écrire dans le fichier ($path"."$name)");

            fclose($handle);
            return ($path.$name);
        }

    /**fonction sendFiles
	 * $fileModel va indiquer ou récupérer le fichier de modèle
	 *     exemple : '/var/www/tmp/Jean.xml'
	 * $fileDatava indiquer ou récupérer le fichier de données
	 * $retour = 'pdf' ou 'odt' suivant le retour souhaité de gedooo
	 *
	 * la fonction va envoyer les deux fichiers à Ged'OOo
	 */

    function sendFiles ($fileModel, $fileData, $retour = 1, $download=0, $name='retour') {
        if ($retour == 0)
	    $retour = 'pdf';
	else
	   $retour = 'odt';

	$data = array('Format' => $retour,
                      'data' => "@$fileData",
                      'model' => "@$fileModel"
                      );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, URL_GEDOOO);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_exec($ch);
        $return = curl_multi_getcontent($ch);
        curl_close($ch);

        if ($download== 0){
            header('Content-type: application/pdf');
            header('Content-Length: '.strlen($return));
            header('Content-Disposition: attachment; filename='.utf8_encode($name).'.'.$retour);
            die($return);
	}
	else {
            // Préparation des répertoires et URL pour la création des fichiers
            $dyn_path = "/files/generee/modeles/";
            $path = WEBROOT_PATH.$dyn_path;
            if (!$this->checkPath($path))
                die("Webdelib ne peut pas ecrire dans le repertoire : $path");
            $fp = fopen($path.$name, 'w');
            fwrite($fp, $return);
            fclose($fp);            
            $zip = new ZipArchive;
	    if ($zip->open($path.'documents.zip', ZipArchive::CREATE) === TRUE) {
	        $zip->addFile($path.$name, $name);
	        $zip->close();
	  } else {
	      echo 'Impossible d\'ajouter le fichier dans l\'archive';
	  }
	}
    }

    function checkPath($path) {
	if (!is_dir($path))
	   return (mkdir($path, 0770, true));
	else {
            // on nettoie ce qu'il y a dedans pour ne pas encombrer le serveur
	    $dh = opendir($path);
	    while (false !== ($document = readdir($dh))) 
                if (is_file($document))
		    unlink($document);
            return true;
	}
    }

   function CreerBalise ($nom, $valeur, $type) {
       $balise  = "<champ>\n";
       $balise .= "    <nom>$nom</nom>\n";
       $balise .= "    <valeur>$valeur</valeur>\n";
       $balise .= "    <type>$type</type>\n";
       $balise .= "</champ>\n";
       return $balise;
   }

}

?>
