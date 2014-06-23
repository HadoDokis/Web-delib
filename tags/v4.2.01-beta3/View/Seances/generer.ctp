<h2> Liste des fichiers g&eacute;n&eacute;r&eacute;s </h2>
<script>
    $("#pourcentage").hide();
    $("#progrbar").hide();
    $("#affiche").hide();
    $("#contTemp").hide();
</script>
<?php
    foreach ($listFiles as $path=> $name) 
        echo $this->Html->link($name, $path)."<br>";
 
    echo ("<br /><br /><a href='/seances/listerFuturesSeances'> Retour &agrave; la liste des s&eacute;ances </a>");
?>

