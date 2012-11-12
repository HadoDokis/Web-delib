<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta(array("name"=>"viewport","content"=>"width=device-width,  initial-scale=1.0"));
		echo $this->Html->meta('icon');
		
		echo $this->Html->css('webdelib');

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('bootstrap-responsive.min');
		// docs.css is only for this exapmple, remove for app dev
		echo $this->Html->css('docs');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->Html->script('libs/modernizr.min');
		echo $this->Html->script('libs/jquery');
		echo $this->Html->script('libs/bootstrap.min');
		echo $this->Html->script('bootstrap/application');

                echo $this->Html->script('utils'); 

		echo $this->fetch('script');
	?>
	
</head>
<body data-spy="scroll" data-target=".subnav" data-offset="50">
	<div id="container">
	    <div id="content">
                <div class="navbar navbar-fixed-top">
                    <div class="navbar-inner"> 
<div class='user'>
                        <?php echo $this->Html->image('webdelib_petit.png', array('align' => 'left')); ?>
<?php
                    if (isset($infoUser)) {
                        echo ("<a href='/users/changeUserMdp'>$infoUser</a>");
                        echo (" [Service : <a href='/users/changeService'>".$agentServices[$session_service_id]."</a> ]");
                        echo ("<a href='/users/logout'>Se déconnecter ".$this->Html->image('/img/icons/logout.png')."</a>");
                    }
?>
</div>
                    </div>
                </div>
                <ul class="nav nav-tabs">

                <?php 
                   
                   foreach ($session_menuPrincipal['items'] as $libelle => $items)  {
                       $carret = "";
                       $classDropdown = '';
                       if (isset($session_menuPrincipal['currentItem']))
                           $carret = " <b class='caret'></b>";
                       if (isset($items['subMenu']))
                           $classDropdown = 'class="dropdown"';
                       echo ("<li $classDropdown>");
                       $this->log($items);
                       echo ("<a class='dropdown-toggle' data-toggle='dropdown' href='".$items['link']."'>$libelle $carret</a>");
                       if (isset($items['subMenu'])) {
                           echo ('<ul class="dropdown-menu">');
                           foreach ($items['subMenu'] as  $key => $url)  {
                               foreach($url as $titre => $lien) 
                                   echo ("<li> <a href='".$lien['link']."'> $titre</a></li>");
                           }
                           echo ('</ul>');
                       }
                       echo ('</li>');
                   }
                ?>
               </ul>
   		    <?php echo $this->Session->flash(); ?>
		     <?php echo $this->fetch('content'); ?>
		</div>
	</div>
	
        <div class="pied">
            <?php echo $this->element('format'); ?><br /><br />
            <?php echo "Web-delib v".VERSION; ?> &copy; 2006-2012 ADULLACT 
	</div><!-- /container -->
	
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
