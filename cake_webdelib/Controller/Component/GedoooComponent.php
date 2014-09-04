<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class GedoooComponent extends Component
{

    function GedoooComponent()
    {
    }


    /**
     * fonction createFile
     * $path va indiquer ou créer le fichier
     * $name sera le nom du fichier
     * $content est le contenu du fichier
     *
     * la fonction va retourner le path ou gedooo pourra aller chercher le fichier
     */
    function createFile($path, $name, $content)
    {
        //TODO Pourquoi supprimer le dossier
        $this->checkPath($path);
        $file = new File($path . $name, true, 0644);
        $file->write($content, 'w', true);
        $file->close();
        return ($path . $name);
    }

    //Attention fonction public
    function checkPath($path)
    {
        if (!is_dir($path))
            return (mkdir($path, 0770, true));
        else {
            return true;
        }
    }

    function CreerBalise($nom, $valeur, $type)
    {
        if (!empty($valeur)) {
            $balise = "<champ>\n";
            $balise .= "    <nom>$nom</nom>\n";
            $balise .= "    <valeur>$valeur</valeur>\n";
            $balise .= "    <type>$type</type>\n";
            $balise .= "</champ>\n";
            return $balise;
        } else
            return '';
    }

}
