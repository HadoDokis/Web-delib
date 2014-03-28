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
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <![endif]-->
    <?php
    //META
    echo $this->Html->meta(array("name" => "viewport", "content" => "width=device-width,  initial-scale=1.0"));
    echo $this->Html->meta('icon');
    echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex, nofollow'));
    //CSS
    echo $this->Html->css('/libs/jgrowl/jquery.jgrowl');
    echo $this->Html->css('/libs/bootstrap/css/bootstrap.min');
    echo $this->Html->css('/libs/font-awesome/css/font-awesome.min');
    echo $this->Html->css('/libs/select2/select2');
    echo $this->Html->css('/libs/jstree/style.min');
    echo $this->Html->css('/libs/scrollup/scrollup.css');
    echo $this->Html->css('treeview');
    echo $this->Html->css('webdelib');
    echo $this->Html->css('filtres');
    echo $this->Html->css('global');
    //Scripts JS
    echo $this->Html->script('/libs/modernizr/modernizr.min');
    echo $this->Html->script('/libs/jquery/jquery-1.10.2.min');
    echo $this->Html->script('/libs/bootstrap/js/bootstrap.min');
    echo $this->Html->script('/libs/jgrowl/jquery.jgrowl.min');
    echo $this->Html->script('/libs/select2/select2.min');
    echo $this->Html->script('/libs/select2/select2_locale_fr');
    echo $this->Html->script('/libs/scrollup/jquery.scrollUp.min');
    echo $this->Html->script('/libs/jstree/jstree.min');
    echo $this->Html->script('jquery.placeholder.js');
    echo $this->Html->script('utils');
    echo $this->Html->script('attendable');
    echo $this->Html->script('masterCheckbox');
    echo $this->Html->script('main');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <?php
        echo $this->Html->link(
            $this->Html->image('webdelib_petit.png', array('id' => 'logo', 'class' => 'logo', 'alt' => 'Webdelib')), array('controller' => 'pages', 'action' => 'home'), array('escape' => false, 'title' => 'Bienvenue dans Webdelib', 'class' => 'brand')
        );
        ?>
        <div class="nav-collapse collapse" style='margin-left: 260px'>
            <ul class="nav">
                <li class="active">
                    <?php echo $this->Html->link($Collectivite['nom'], array('controller' => 'pages', 'action' => 'home')); ?>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
        <div class="nav-collapse collapse pull-right" style="margin-right: 30px;">
            <?php
            echo $this->Form->create('User', array(
                'id' => 'quickSearch',
                'class' => 'navbar-search pull-right',
                'url' => array(
                    'plugin' => null,
                    'controller' => 'deliberations',
                    'action' => 'quicksearch')));
            echo $this->Form->input('User.search', array(
                'class' => 'search-query span2',
                'div' => false,
                'label' => false,
                'id' => 'searchInput',
                'placeholder' => 'Rechercher',
                'autocomplete' => 'off'));
            echo $this->Form->end();
            ?>
        </div>
        <!--/.nav-collapse -->
        <div class="nav-collapse collapse pull-right">
            <ul class="nav">
                <li class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="user"><?php echo $infoUser; ?></span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <?php echo $this->Html->link('Changer le format de sortie des éditions', array('plugin' => null, 'controller' => 'pages', 'action' => 'format')); ?>
                        </li>
                        <li>
                            <?php echo $this->Html->link('Changer le service émetteur', array('plugin' => null, 'controller' => 'pages', 'action' => 'service')); ?>
                        </li>
                        <li>
                            <?php echo $this->Html->link('Changer de mot de passe', array('plugin' => null, 'controller' => 'users', 'action' => 'changeUserMdp')); ?>
                        </li>
                        <li>
                            <?php echo $this->Html->link('Changer de thême', array('plugin' => null, 'controller' => 'users', 'action' => 'changeTheme')); ?>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <?php echo $this->Html->link('Se déconnecter', array('plugin' => null, 'controller' => 'users', 'action' => 'logout')); ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>
<?php
App::uses('Debugger', 'Utility');
if (Configure::read('debug') > 0):
    Debugger::checkSecurityKeys();
endif;
?>
<noscript>
    <div class="alert alert-error text-center">
        <strong>Attention!</strong> Vous devez activer JavaScript dans votre navigateur pour pouvoir utiliser le service Webdelib
    </div>
</noscript>
<ul class="nav nav-tabs">
    <?php
    if (isset($session_menuPrincipal['items'])) {
        foreach ($session_menuPrincipal['items'] as $libelle => $items) {
            $carret = '';
            $classDropdown = '';
            $title = '';
            if (isset($items['subMenu'])) {
                $carret = ' <b class="caret"></b>';
                $classDropdown = 'class="dropdown"';
            }
            if (isset($items['title'])) {
                $title = $items['title'];
            }
            echo("<li $classDropdown>");
            if ($libelle == "Accueil")
                echo $this->Html->link($libelle, $items['link']);
            else
                echo $this->Html->link("$libelle $carret", $items['link'], array(
                    'escape' => false,
                    'class' => 'dropdown-toggle',
                    'data-toggle' => 'dropdown',
                    'data-target' => '#',
                    'title' => $title
                ));
            if (isset($items['subMenu'])) {
                echo $this->Html->tag('ul', null, array('class' => 'dropdown-menu'));
                foreach ($items['subMenu'] as $key => $url) {
                    foreach ($url as $titre => $lien) {
                        echo $this->Html->tag('li', null);
                        echo $this->Html->link($titre, $lien['link'], array('escape' => false));
                        echo $this->Html->tag('/li', null);
                    }
                }
                echo $this->Html->tag('/ul', null);
            }
            echo $this->Html->tag('/li', null);
        }
    }
    ?>
</ul>
<!-- Contents -->
<div id="principal">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content'); ?>
</div>
<!--Footer-->
<?php echo $this->element('footer'); ?>
<!--Attendable-->
<?php echo $this->element('waiter'); ?>
<!--Dump sql (debug > 1)-->
<?php echo $this->element('sql_dump'); ?>
</body>
</html>