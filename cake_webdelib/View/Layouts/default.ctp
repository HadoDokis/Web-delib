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
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        <?php
        echo $this->Html->link(
            $this->Html->image('webdelib_petit.png', array('id' => 'logo', 'class' => 'logo', 'alt' => 'Webdelib')), array('plugin'=> null,'controller' => 'pages', 'action' => 'home'), array('escape' => false, 'title' => 'Bienvenue dans Webdelib', 'class' => 'navbar-brand')
        );
        ?>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active" style='margin-left: 230px'>
            <?php echo $this->Html->link($Collectivite['nom'], array(
                'plugin'=> null, 
                'controller' => 'pages', 
                'action' => 'home')); ?>
        </li>
         <li>
             <?php echo $this->Bs->btn(null, array(
                                                    'plugin'=> null,
                                                    'controller' => 'pages', 
                                                    'action' => 'home'
                                                    ),
                 array('icon'=>'glyphicon glyphicon-home"')); 
             ?>
            </li>
      </ul>
      <ul class="nav navbar-nav">
        <?php
        if ($this->fetch('filtre')){
            echo $this->Bs->btn(__('FILTRER'), '#',
         array(
                'id'=>'boutonBasculeCriteres',
                'class' => 'navbar-btn',
                'type'=>'primary',
                'title'=>__('Afficher-masquer les critères du filtre'),
                'onClick'=>"basculeCriteres(this);",
                //'escape'=> false,filtreCriteres
                /*'icon'=>'glyphicon glyphicon-filter"'*/));
        }  else {
            echo $this->Bs->btn(__('FILTRER'), '#',
             array(
                    'id'=>'boutonBasculeCriteres',
                    'class' => 'navbar-btn',
                    'type'=>'primary',
                    'title'=>__('Afficher-masquer les critères du filtre'),
                    'disabled'=>"disabled"));
        }
        ?>
      </ul>
    <?php
    echo $this->Form->create('User', array(
        'id' => 'quickSearch',
        'role'=>'search',
        'class' => 'navbar-form navbar-right',
        'url' => array(
            'plugin' => null,
            'controller' => 'deliberations',
            'action' => 'quicksearch')));
    ?><div class="form-group"><?php
    echo $this->Form->input('User.search', array(
        'class' => 'form-control span2',
        'div' => false,
        'label' => false,
        'id' => 'searchInput',
        'placeholder' => 'Rechercher',
        'autocomplete' => 'off'));
    ?></div><?php
    echo $this->Form->end();
    ?>
        
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $infoUser; ?> <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
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
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
    
<!--<nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
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
            <ul class="nav  navbar-nav">
                <li class="active">
                    <?php echo $this->Html->link($Collectivite['nom'], array('controller' => 'pages', 'action' => 'home')); ?>
                </li>
            </ul>
        </div>
        /.nav-collapse 
        <div class="nav-collapse collapse pull-right" style="margin-right: 30px;">
            <?php
            echo $this->Form->create('User', array(
                'id' => 'quickSearch',
                'role'=>'search',
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
        /.nav-collapse 
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
        /.nav-collapse 
    </div>
</div>-->
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