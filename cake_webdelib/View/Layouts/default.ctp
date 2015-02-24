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
        <?php echo $this->fetch('title'); ?>
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
    //META
    echo $this->Html->meta(array("name" => "viewport", "content" => "width=device-width,  initial-scale=1.0"));
    echo $this->Html->meta('icon');
    echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex, nofollow'));
    //CSS
    echo $this->Html->css('/components/bootstrap/dist/css/bootstrap.min');
    echo $this->Html->css('/components/font-awesome/css/font-awesome.min');
    echo $this->Html->css('/components/select2/select2');
    echo $this->Html->css('/components/select2-bootstrap-css/select2-bootstrap');
    echo $this->Html->css('/components/yamm3/yamm/yamm.css');
    echo $this->Html->css('/components/bootstrap-submenu/dist/css/bootstrap-submenu.min');
    echo $this->Html->css('/components/jstree/dist/themes/default/style.min');
    echo $this->Html->css('treeview');
    echo $this->Html->css('webdelib');
    echo $this->Html->css('filtres');
    echo $this->Html->css('global');
    //Scripts JS
    echo $this->Html->script('/components/modernizr/modernizr');
    echo $this->Html->script('/components/jquery/jquery.min');
    echo $this->Html->script('/components/jquery/jquery-migrate.js');
    echo $this->Html->script('/components/bootstrap/dist/js/bootstrap.min');
    echo $this->Html->script('/components/bootstrap.growl/bootstrap-growl.min');
    echo $this->Html->script('/components/select2/select2.min');
    echo $this->Html->script('/components/select2/select2_locale_fr');
    echo $this->Html->script('/components/scrollup/dist/jquery.scrollUp.min');
    echo $this->Html->script('/components/jstree/dist/jstree.min');
    echo $this->html->script('/components/bootstrap-submenu/dist/js/bootstrap-submenu.min');
    echo $this->html->script('/components/jquery-placeholder/jquery.placeholder.min');
    echo $this->Html->script('utils');
    echo $this->Html->script('attendable');
    echo $this->Html->script('masterCheckbox');
    echo $this->Html->script('filtre.js');
    echo $this->Html->script('main');
    
    echo $this->fetch('css');
    echo $this->fetch('meta');
    ?>
</head>
<body>
<?php echo $this->element('navbar'); ?>
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
<?php
echo $this->fetch('filtre');
?>
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
<div id="principal" class="container-fluid">
    <?php 
    echo $this->Html->getCrumbList(array('class'=>'breadcrumb'), __('Mon tableau de bord')); ?>
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content'); ?>
</div>
<!--Footer-->
<?php echo $this->element('footer');
echo '<!--Attendable-->';
echo $this->element('waiter'); 
echo '<!--Dump sql (debug > 1)-->';
echo $this->element('sql_dump'); 
echo $this->fetch('script');
?>
</body>
</html>