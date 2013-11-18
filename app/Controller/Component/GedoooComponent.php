<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class GedoooComponent extends Component {

	
	function GedoooComponent() { }
	
	
	/**fonction createFile
	 * $path va indiquer ou créer le fichier
	 * $name sera le nom du fichier
	 * $content est le contenu du fichier
	 *
	 * la fonction va retourner le path ou gedooo pourra aller chercher le fichier
	 */
	function createFile ($path, $name, $content) {
            //TODO Pourquoi supprimer le dossier
	    $this->checkPath($path);
            
            $file = new File($path.$name, false, 0644);
            if($file->exists())
                $file->delete();
            
            $file->create();
            $file->write($content);
            $file->close();
            
            return ($path.$name);
        }

    /**fonction sendFiles ATTENTION : Cette fonction est obsolète
	 * $fileModel va indiquer ou récupérer le fichier de modèle
	 *     exemple : '/var/www/tmp/Jean.xml'
	 * $fileDatava indiquer ou récupérer le fichier de données
	 * $retour = 'pdf' ou 'odt' suivant le retour souhaité de gedooo
	 *
	 * la fonction va envoyer les deux fichiers à Ged'OOo
	 */

    function sendFiles ($fileModel, $fileData, $retour = 1, $download=0, $name='retour', $seance_id=null) {
        if ($retour == 0)
	    $retour = 'pdf';
	elseif ($retour ==1)
	   $retour = 'odt';
        elseif ($retour ==2)
	    $retour = 'html';           
	
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
            if ($seance_id == null)
	        $dyn_path = "/files/generee/modeles/";
            else 
	         $dyn_path = "/files/generee/modeles/$seance_id/";
	    $path = WEBROOT_PATH.$dyn_path;
            $folder = new Folder($path,true,0755);
            $errors=$folder->errors();
            if (!empty($errors) && is_array($errors))
                die("Webdelib ne peut pas ecrire dans le repertoire : ".@implode(',',$folder->errors));
            
            $this->createFile ($path, $name, $return);
            
	    $zip = new ZipArchive;
	    if ($zip->open($path.'documents.zip', ZipArchive::CREATE) === TRUE) {
	        $zip->addFile($path.$name, $name);
	        $zip->close();
	  } else {
	      echo 'Impossible d\'ajouter le fichier dans l\'archive';
	  }
	}
    }
    //Attention fonction public
    function checkPath($path) {
	if (!is_dir($path))
	   return (mkdir($path, 0770, true));
	else {
            return true;
	}
    }

   function CreerBalise ($nom, $valeur, $type) {
       if (!empty($valeur)) {
           $balise  = "<champ>\n";
           $balise .= "    <nom>$nom</nom>\n";
           $balise .= "    <valeur>$valeur</valeur>\n";
           $balise .= "    <type>$type</type>\n";
           $balise .= "</champ>\n";
           return $balise;
       }
       else
           return '';
   }

}

?>
