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
?>
<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $cakeDescription ?>:
            <?php echo $title_for_layout; ?>
        </title>
        <?php
        echo $this->Html->meta(array("name" => "viewport", "content" => "width=device-width,  initial-scale=1.0"));
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');

        echo $this->Html->css('webdelib');
        echo $this->Html->css('jquery.jgrowl');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('bootstrap-responsive.min');
        echo $this->Html->css('font-awesome.min');
        echo $this->Html->css('connexion');
        echo $this->fetch('css');

        echo $this->Html->script('jquery');
        echo $this->Html->script('modernizr.min');
        echo $this->Html->script('libs/bootstrap.min');
        echo $this->html->script('jquery.jgrowl', true);
        echo $this->Html->script('utils');

        echo $this->fetch('script');
        ?>
    </head>
    <body data-spy="scroll" data-target=".subnav" data-offset="50" style="background-position: 0 0;">
        <div id="container">
            <div class="navbar navbar-fixed-top">
                <div class="navbar-inner"> 
                    <?php echo $this->Html->image($logo_path, array('align' => 'left', 'style' => 'margin-left:10px')); ?>
                </div>
            </div>
            <?php echo $content_for_layout; ?>
        </div>
        <?php echo $this->element('footer'); ?>