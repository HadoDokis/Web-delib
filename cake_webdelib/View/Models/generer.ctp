<h2> Liste des fichiers g&eacute;n&eacute;r&eacute;s </h2>
<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>
<?php
    foreach ($listFiles as $path=> $name) {
        if ($name != 'Documents.zip') 
	    echo $this->Html->link($name, $path.".$format")."<br>";
        else
	    echo $this->Html->link($name, $path)."<br>";
    }
    echo ("<br /><br /><a href='/seances/listerFuturesSeances'> Retour &agrave; la liste des s&eacute;ances </a>");
?>
