<?php

class ImportShell extends AppShell {

    public $uses = array('Deliberation', 'Annex', 'Seance', 'Infosup', 'Modeledition');

    public function main() {
        $list_files = file_get_contents(TMP.'backups'.DS.'liste_fichiers.lst');
        foreach(explode("\n", $list_files) as $ligne) {
            if ($ligne != "") {
                $pos   = strpos($ligne, 'app/tmp/backups/')+ strlen('app/tmp/backups/');
                $model = substr(substr($ligne, $pos, strlen($ligne)), 0, strpos(substr($ligne, $pos, strlen($ligne)), '/'));
                $tmp   = substr(substr($ligne, $pos, strlen($ligne)), strlen($model) +1, strlen($ligne));
                $id    =  substr( $tmp, 0, strpos($tmp, '/'));
                $field   = substr($tmp, strlen($id) +1, strlen($tmp));            

                if ($model == 'Model') $model = 'Modeledition';
                    $this->saveContent($model,$id, $field); 
            }
        }
    }
 
    function getContent($model,$id, $field) {
        if ($model ==  'Modeledition')
            $model = 'Model';
        $filename = TMP."backups/$model/$id/$field";
        echo("$model => $id :  récupération du fichier '$field' ($filename)" );
        
        if (file_exists($filename))
            return (file_get_contents($filename));
    }

    function saveContent($model,$id, $field) {
       $this->$model->id = $id;
       $content =  $this->getContent($model,$id, $field);
       if (!empty( $content )) {
           if ($this->$model->saveField( $field , $content))
               echo (" : OK\n");
           else 
               echo (" : KO\n");
        }
        else {
             echo (":  vide\n");
        }
    }

}

?>
