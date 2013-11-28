<h2> Liste des fichiers générés </h2>
<script>
    $("#pourcentage").hide();
    $("#progrbar").hide();
    $("#affiche").hide();
    $("#contTemp").hide();
</script>
<?php
if (!empty($listFiles)){
    foreach ($listFiles as $path=> $name) {
        if ($name != 'Documents.zip') {
            $filename = end(explode('/', $path.'.'.$format));
            $filename = str_replace(array('pdf2','odt2'), array('pdf','odt'), $filename);
            echo $name.' : <strong>'.$filename.'</strong> ';
            if ($format == 'pdf')
                echo $this->Html->link('[Visualiser]', $path.".$format", array('target'=>'_blank', 'style' => 'font-weight: bold', 'title' => 'Visualiser le document dans votre navigateur'));

            echo '&nbsp;';
            //fix pour odt2 et pdf2
            echo $this->Html->link('[Télécharger]', $path.".$format", array('download'=>$filename, 'style' => 'font-weight: bold', 'title' => 'Télécharger le fichier sur votre disque'));
        } else echo $this->Html->link($name, $path);
    }
    echo $this->Html->tag('br');
    echo $this->Html->tag('br');
    echo $this->Html->tag('p', 'Attention: Le fichier généré deviendra inaccessible au prochain changement de page. Pensez à le sauvegarder.');
} else
    echo '<strong>L\'accès au fichier généré a expiré ou une erreur s\'est produite, veuillez recommencer la générération.</strong>';

echo '<br><br>';
if (empty($urlRetour) || strpos($urlRetour, "multiodj"))
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', '/', array('class'=>'btn', 'escape' => false));
else
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $urlRetour, array('class'=>'btn', 'escape' => false));
?>
