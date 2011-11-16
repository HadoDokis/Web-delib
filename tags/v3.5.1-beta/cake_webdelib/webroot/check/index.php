<?php
//fichier de conf
include_once('verification.php');

// affichage de l'entête
include_once('header.php');

?>
<div id="wrapper"> 
	<div id="content">  
		<h1 class="heading">Vérification de l'installation</h1>

		<?php if (isMulti()) : ?>
			<div class="demo">
				<h2 class="expand">Fonctionnement multi services d'archives</h2>
				<div class="collapse">
				<?php afficheMulti(); ?>
			</div> 
		<?php endif; ?>

		<div class="demo">
			<h2 class="expand">Affichage des Versions</h2>
			<div class="collapse">
			<?php verifVersions(); ?>
		</div> 
		
		<h2 class="expand">Permissions</h2>
		<div class="collapse">
			<?php d('Propriétaire du script courant : ' . get_current_user(), 'info' ); ?>
			<?php d('Répertoire d\'installation de l\'application : ' . $appli_path, 'info'); ?>
			<?php verifRepEchangeStockage(); ?>
		</div> 
		
		<h2 class="expand">Modules Apache</h2>
		<div class="collapse">
			<?php apache_check_modules($mods_apache);?>
		</div> 
		
		<h2 class="expand">Extensions PHP</h2>
		<div class="collapse">
			<?php php_check_extensions($exts_php); ?>
		</div> 
		
		<h2 class="expand">Console Cake PHP</h2>
		<div class="collapse">
			<?php verifConsoleCakePhp(); ?>
		</div> 
		
		<h2 class="expand">Présence des fichiers de configuration de as@lae</h2>
		<div class="collapse">
			<?php verifPresenceFichierIni(); ?>
		</div> 
		
		<h2 class="expand">Base de données</h2>
		<div class="collapse">
			<?php infoDataBase(); ?>
		</div> 
		
		<h2 class="expand">Mails</h2>
		<div class="collapse">
			<?php infoMails(); ?>
		</div> 
		
		<h2 class="expand">Horodatage</h2>
		<div class="collapse">
			<?php verifHorodatage(); ?>
		</div> 
		
		<h2 class="expand">Antivirus</h2>
		<div class="collapse">
			<?php verifAntivirus(); ?>
		</div> 
		
		<h2 class="expand">Valideur de format</h2>
		<div class="collapse">
			<?php verifFormatValidator(); ?>
		</div> 
		
		<h2 class="expand">Outil de conversion</h2>
		<div class="collapse">
			<?php verifConversion(); ?>
		</div> 
	</div> 
</div> 

<?php // affichage du pied de page
include_once('footer.php');
?>