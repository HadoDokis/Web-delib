<h2> Liste des fichiers générés </h2>
<script>
    $("#pourcentage").hide();
    $("#progrbar").hide();
    $("#affiche").hide();
    $("#contTemp").hide();
</script>
<?php
if (!empty($listFiles))
    foreach ($listFiles as $path=> $name) {
        if ($name != 'Documents.zip') 
	    echo $this->Html->link($name, $path.".$format")."<br>";
        else
	    echo $this->Html->link($name, $path)."<br>";
    }
if (empty($urlRetour)) $urlRetour="javascript:history.back()";
echo ("<br /><br /><a href='".$urlRetour."'> Retour &agrave; la liste des séances </a>");
?>
