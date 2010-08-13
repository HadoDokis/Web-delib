<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<title>Web-delib</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	
	<?php echo $html->css('webdelib'); ?>
	<?php echo $html->css('jquery.jgrowl'); ?>
	<?php echo $javascript->link('utils'); ?>
	<?php echo $javascript->link('jquery', true); ?>
	<?php echo $javascript->link('jquery.jgrowl', true); ?>

</head>
<body>
<div id="conteneur">

	<div id="header">
	</div>

	<div id="user">
		<?php
                    if (isset($infoUser)) {
                        echo $html->link($infoUser, '/users/changeUserMdp', array('escape'=>false));
                        $urlPage =  FULL_BASE_URL . $this->webroot;
                        echo $form->input('Service',array('label'=>'', 'options'=>$agentServices, 'default'=>$session_service_id, 'id' => $urlPage, 'onChange'=>"changeService(this)", 'empty'=>false,'div'=>false));
                        echo $lienDeconnexion;
                    }
		?>

	</div>

	<div id="centre">
		<div id="menuPrincipal">
			<?php
                              if (isset($infoUser)) 
                                  $menu->menu( $session_menuPrincipal ); 
			?>
		</div>
		<?php $session->flash(); ?>

		<?php echo $content_for_layout; ?>
	</div>

	<div id="pied"><?php echo "(CakePHP : " .Configure::version().") Web-delib v".VERSION; ?> &copy; 2010 ADULLACT</div>
</div>
</body>
</html>
