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
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $cakeDescription ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta(array("name" => "viewport", "content" => "width=device-width,  initial-scale=1.0"));
    echo $this->Html->meta('icon');

    echo $this->Html->css('/libs/bootstrap/css/bootstrap.min');
    echo $this->Html->css('/libs/scrollup/scrollup.css');
    echo $this->Html->css('popup');

    echo $this->Html->script('/libs/modernizr/modernizr.min');
    echo $this->Html->script('/libs/jquery/jquery-1.10.2.min');
    echo $this->Html->script('/libs/bootstrap/js/bootstrap.min');
    echo $this->Html->script('/libs/scrollup/jquery.scrollUp.min');
    echo $this->Html->script('utils');
    echo $this->Html->script('main');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>

</head>
<body data-spy="scroll" data-target=".subnav" data-offset="50">
        <div id="container">
            <div id="content">
<?php echo $content_for_layout; ?> 
            </div>
        </div>
<?php echo $this->element('footer'); ?>
</body>
</html>