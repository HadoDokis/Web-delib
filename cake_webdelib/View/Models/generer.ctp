<h2> Liste des fichiers générés </h2>
<script>
    $("#pourcentage").hide();
    $("#progrbar").hide();
    $("#affiche").hide();
    $("#contTemp").hide();
</script>
<?php
if (!empty($listFiles)){
    echo $this->Html->tag('strong', 'Attention: Le fichier généré deviendra inaccessible au prochain changement de page. Pensez à le sauvegarder.');
    echo '<br><br>';
    foreach ($listFiles as $path=> $name) {
        if ($name != 'Documents.zip') {
            echo $name.' : ';
            if ($format == 'pdf')
                echo $this->Html->link('[Visualiser]', $path.".$format", array('target'=>'_blank', 'style' => 'font-weight: bold', 'title' => 'Visualiser le document dans votre navigateur'));
            $filename = end(explode('/', $path.'.'.$format));
            echo '&nbsp;';
            echo $this->Html->link('[Télécharger]', $path.".$format", array('download'=>$filename, 'style' => 'font-weight: bold', 'title' => 'Télécharger le fichier sur votre disque'));
        } else echo $this->Html->link($name, $path);
    }
} else
    echo '<strong>L\'accès au fichier généré a expiré ou une erreur s\'est produite, veuillez recommencer la générération.</strong>';

echo '<br><br>';
if (empty($urlRetour) || strpos($urlRetour, "multiodj"))
    echo $this->Html->link('< Retour', '/', array('class'=>'btn'));
else
    echo $this->Html->link('< Retour', $urlRetour, array('class'=>'btn'));
?>
