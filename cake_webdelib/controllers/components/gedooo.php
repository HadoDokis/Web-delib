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

    function sendFiles ($fileModel, $fileData, $retour = 1) {
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
        $return = curl_multi_getcontent  ($ch);
        curl_close($ch);
        
        header('Content-type: application/pdf');
        header('Content-Length: '.strlen($return));
        header('Content-Disposition: attachment; filename=retour.'.$retour);
        die($return);
    }
  
    function checkPath($path) {
	if (!is_dir($path))
	   return (mkdir($path, 0770, true));
	else 
            return true;
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
