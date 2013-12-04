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
	<meta name="robots" content="noindex, nofollow" />
        <?php
        echo $this->Html->meta(array("name" => "viewport", "content" => "width=device-width,  initial-scale=1.0"));
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        
        echo $this->Html->css('webdelib');
        echo $this->Html->css('jquery.jgrowl.min');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('bootstrap-responsive.min');
        echo $this->Html->css('font-awesome.min');
        echo $this->Html->css('attendable');
        echo $this->Html->css('/lib/select2/select2');
        echo $this->Html->css('filtres');
        echo $this->Html->css('global');
        echo $this->fetch('css');

        echo $this->Html->script('modernizr.min');
        echo $this->Html->script('jquery-1.10.2.min.js');
        echo $this->Html->script('libs/bootstrap.min');
        echo $this->html->script('jquery.jgrowl.min', true);
        echo $this->html->script('/lib/select2/select2.min', true);
        echo $this->html->script('/lib/select2/select2_locale_fr', true);
        echo $this->Html->script('utils');
        echo $this->html->script('attendable', true);
        echo $this->html->script('waitAndBlock', true);
        echo $this->html->script('masterCheckbox', true);
        echo $this->fetch('script');
        ?>

    </head>
    <body data-spy="scroll" data-target=".subnav" data-offset="50">
        <div id="container">
            <div id="content">
                <div class="navbar navbar-fixed-top">
                    <div class="navbar-inner"> 
                        <div class='user'>
                            <?php echo $this->Html->image('webdelib_petit.png', array('id'=>'logo')); ?>
                            <?php if (isset($infoUser)) { ?>
                                <form class="navbar-search pull-right" action="/deliberations/quicksearch">
                                    <?php
                                    echo $this->Form->input('field', array('class' => 'search-query span2',
                                        'div' => false, 'label' => false,
                                        'id' => 'searchInput',
                                        'placeholder' => 'Rechercher',
                                        'autocomplete' => 'off'));
                                    ?>
                                </form>
                                <ul class="nav pull-right">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="user"><?php echo $infoUser; ?></span><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="/pages/format">Changer le format de sortie des éditions</a>
                                            </li>
                                            <li>
                                                <a href="/pages/service">Changer le service émetteur</a>
                                            </li>
                                            <li>
                                                <a href="/users/changeUserMdp">Changer de mot de passe</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a id="logout" href="/users/logout">Se déconnecter</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
<?php } ?>
                        </div>
                    </div>
                </div>
                <?php
                App::uses('Debugger', 'Utility');
                if (Configure::read('debug') > 0):
                    Debugger::checkSecurityKeys();
                endif;
                ?>


                <ul class="nav nav-tabs hidden-phone">
                    <?php
                    if (isset($session_menuPrincipal['items'])){
                        foreach ($session_menuPrincipal['items'] as $libelle => $items) {
                            $carret = "";
                            $classDropdown = '';
                            if (isset($session_menuPrincipal['currentItem']) && isset($items['subMenu']))
                                $carret = " <b class='caret'></b>";
                            if (isset($items['subMenu']))
                                $classDropdown = 'class="dropdown"';
                            echo ("<li $classDropdown>");
                            if ($libelle == "Accueil")
                                echo ("<a href='" . $items['link'] . "'>$libelle</a>");
                            else
                                echo ("<a class='dropdown-toggle' data-toggle='dropdown' href='" . $items['link'] . "'>$libelle $carret</a>");
                            if (isset($items['subMenu'])) {
                                echo ('<ul class="dropdown-menu">');
                                foreach ($items['subMenu'] as $key => $url) {
                                    foreach ($url as $titre => $lien)
                                        echo ("<li> <a href='" . $lien['link'] . "'> $titre</a></li>");
                                }
                                echo ('</ul>');
                            }
                            echo ('</li>');
                        }
                    }
                    ?>
                </ul>
                <?php echo $this->Session->flash(); ?>
                <?php echo $this->fetch('content'); ?>
            </div>
        </div>
<?php echo $this->element('footer'); ?>
<?php echo $this->element( 'sql_dump' ); ?>