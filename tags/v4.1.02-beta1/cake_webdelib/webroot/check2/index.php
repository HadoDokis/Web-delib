<?php 
require_once('base.html'); 
require_once('verification.php'); 
?>
<style type="text/css">
<!--
@import url('http://twitter.github.com/bootstrap/assets/css/bootstrap.css');
-->
</style>


<div class="container">
      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h2>Webdelib check</h2>
        <p>Vérification de l'environenemt Webdelib.</p>
        <p><a class="btn btn-primary btn-large" href="index2.php">Lancer la vérification <i class="icon-white icon-play"></i></a></p>
</div>


<br />


<div class="container">
    <div class="progress progress-striped active">
    <div class="bar" style="float: left; width: 0%; " data-percentage="60"></div>

</div>
</div>




</div><!--container-->
<?php
require_once('footer.html');
?>
