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

      <!-- Example row of columns -->
<div class="row">
        <div class="span12">
<div class="well well-small">
          <p><i class="icon-ok"></i> Versions</p>
	<?php verifVersions(); ?>        
</div>       
</div>       
</div>       
<br />       


<div class="row">
	<div class="span12">
<div class="well well-small">
          <p><i class="icon-user"></i> Horodatage</p>
<!--<?php verifHorodatage(); ?>--> 
</div>
</div>
</div>
<br />


<div class="row">
	<div class="span12">
<div class="well well-small">
          <p><i class="icon-user"></i> Antivirus</p>
<?php verifAntivirus(); ?>
</div>
</div>
</div>
<br />

<div class="row">
	<div class="span12">
<div class="well well-small">
          <p><i class="icon-user"></i> Cines</p>
<!--<?php verifFormatValidator(); ?>-->
</div>
</div>
</div>
<br />

</div><!--row-->
</div><!--container-->
<?php
require_once('footer.html');
?>
