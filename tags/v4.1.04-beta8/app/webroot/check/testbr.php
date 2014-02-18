<?php 
require_once('base.html'); 
require_once('verification.php'); 
?>
<div class="container">
      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h2>Asalae check</h2>
        <p>Vérification de l'environenemt Asalae.</p>
        <p><a class="btn btn-primary btn-large" href="index2.php">Lancer la vérification <i class="icon-white icon-info-sign"></i></a></p>
</div>




 <div id=”conteneur” style=”display:none; background-color:FFFFF6;”>

 <div id=”barre” style=”display:block; background-color:#313131; width:0%;”>
 <div id=”pourcentage” style=”text-align:right;”>
 &nbsp;
 </div>
 </div>
 </div> 


<div class="row">
        <div class="span12">
<div class="well well-small">
          <p><i class="icon-user"></i> Cines</p>

<?php
echo "<script>";
echo "document.getElementById(‘conteneur’).style.display = \"block\";";
echo "</script>";
ob_flush();
flush();
ob_flush();
flush(); 

for( $i=0 ; $i < $x ; $i++ )
{
$indice = ( ($i+1)*100 ) / $x;
progression($indice);
verifFormatValidator(); 

for( $j = 0 ; $j < 120 ; $j++ )
echo '.';
//echo '<br />';
} 

echo "<script>";
echo "document.getElementById(‘pourcentage’).innerHTML = \”TERMINÉ !\";";
echo "</script>"; 
?>


</div>
</div>
</div>
<br />



</div><!--container-->
<?php
require_once('footer.html');
?>
