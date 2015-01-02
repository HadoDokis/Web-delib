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
$cakeDescription = __d('webdelib', 'Webdelib');
?><!DOCTYPE html>
<html class="no-js" lang="fr">
<head>
    <meta charset="utf-8">
    <title>
        <?php echo $cakeDescription ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
    echo $this->Html->meta(array("name" => "viewport", "content" => "width=device-width,  initial-scale=1.0"));
    echo $this->Html->meta('icon');

    echo $this->Html->css('/components/jgrowl/jquery.jgrowl');
    echo $this->Html->css('/components/bootstrap/css/bootstrap.min');
    echo $this->Html->css('/components/font-awesome/css/font-awesome.min');
    echo $this->Html->css('webdelib');
    echo $this->Html->css('connexion');

    echo $this->Html->script('/components/modernizr/modernizr.min');
    echo $this->Html->script('/components/jquery/jquery.min');
    echo $this->Html->script('/components/bootstrap/dist/js/bootstrap.min');
    echo $this->Html->script('/components/jgrowl/jquery.jgrowl.min');
    echo $this->html->script('/components/jquery-placeholder/jquery.placeholder.min.js');
    echo $this->Html->script('utils');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
<div id="container">
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <?php echo $this->Html->image($logo_path, array('id' => 'logo-adullact', 'style' => 'margin-left:10px')); ?>
        </div>
    </div>
    <noscript>
    <div class="alert alert-heading text-center">
        <strong>Attention!</strong> Vous devez activer JavaScript dans votre navigateur pour pouvoir utiliser le service Webdelib
    </div>
    </noscript>
    <?php echo $content_for_layout; ?>
</div>
<?php echo $this->element('footer'); ?>
</body>
</html>