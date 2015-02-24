<?php 

$this->Navbar->create(array(
    'inverse'=>true,
    'fixed'=>'top',
    'responsive'=> true,
    'fluid'=> true));

$this->Navbar->brand($collectivite['nom'],array(
                'admin' => false,
                'plugin'=> null, 
                'controller' => 'pages', 
                'action' => 'home'));

$this->Navbar->link($this->Bs->icon('home', array('lg')), array(
                                                    'admin' => false,
                                                    'plugin'=> null,
                                                    'controller' => 'pages', 
                                                    'action' => 'home'
                                                    ),array('escape'=>false));

$this->Menu->createMenuPrincipale($this->Navbar);

if ($this->fetch('filtre')){
    $this->Navbar->block(
    $this->Bs->btn(__('Filtrer'), '#',
         array(
                'id'=>'boutonBasculeCriteres',
                'class' => 'navbar-btn',
                'type'=>'primary',
                'title'=>__('Afficher-masquer les critères du filtre'),
                'onClick'=>"basculeCriteres(this);",
                //'escape'=> false,filtreCriteres
                /*'icon'=>'glyphicon glyphicon-filter"'*/))
            , array('list'=>false));
}  else {
    $this->Navbar->block(
    $this->Bs->btn(__('Filtrer'), '#',
     array(
            'id'=>'boutonBasculeCriteres',
            'class' => 'navbar-btn',
            'type'=>'primary',
            'title'=>__('Afficher-masquer les critères du filtre'),
            'disabled'=>"disabled"),null, array('escape'=>false))
    , array('list'=>false));
}

 $block=$this->Form->create('User', array(
        'id' => 'quickSearch',
        'role'=>'search',
        'class' => 'navbar-form navbar-right',
        'url' => array(
            'plugin' => null,
            'controller' => 'deliberations',
            'action' => 'quicksearch')));
    $block.= '<div class="form-group">';
    $block.= $this->Form->input('User.search', array(
        'class' => 'form-control span2',
        'div' => false,
        'label' => false,
        'id' => 'searchInput',
        'placeholder' => 'Rechercher',
        'autocomplete' => 'off'));
    $block.= '</div>';
    $block.= $this->Form->end();
     $this->Navbar->block($block, array('list'=>false));
     
/*$this->Navbar->searchForm(array(
    'id' => 'quickSearch',
    'model'=>array('url' => array(
            'plugin' => null,
            'controller' => 'deliberations',
            'action' => 'quicksearch',
    ),), 
    'pull'=>'right',
    'form'=>array(
        'id' => 'quickSearch',
        //'class' => 'span2',
        'placeholder' => 'Rechercher', 
        'autocomplete' => 'off',
        'button'=> $this->Bs->icon('search'),
    )), array('pull'=>'right'));*/

$this->Navbar->beginMenu($infoUser, null, array('pull'=>'right'));
$this->Navbar->link('Changer le format de sortie des éditions', 
            array('admin' => false, 'plugin' => null, 'controller' => 'pages', 'action' => 'format'),
            array('escape'=>false));
$this->Navbar->link('Changer le service émetteur', 
            array('admin' => false, 'plugin' => null, 'controller' => 'pages', 'action' => 'service'),
            array('escape'=>false));
$this->Navbar->link('Changer de mot de passe', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeUserMdp'),
            array('escape'=>false));
$this->Navbar->link('Changer de thême', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeTheme'),
            array('escape'=>false));
$this->Navbar->link('Se déconnecter', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'logout'),
            array('escape'=>false));
$this->Navbar->endMenu();



//        'id' => 'quickSearch',
//        'role'=>'search',
//        'url' => array(
//            'plugin' => null,
//            'controller' => 'deliberations',
//            'action' => 'quicksearch',
//        )),
//        'placeholder' => 'Rechercher',
//        'autocomplete' => 'off'
//                
echo $this->Navbar->end(true);
return;

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
<!--
        <li class="dropdown">
            <?php echo $this->Bs->link('Tous les projets <span class="caret"></span>', '#',
                 array(
                     'escape'=>false,
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
            //list-unstyled"
             ?>
            <ul class="dropdown-menu">
                <li>
                    <div class="yamm-content">
                    <div class="row">
                    <ul class="col-sm-6  list-unstyled">
                            <li><p><strong>Délibérations</strong></p></li>
                            <?php
                          if($this->permissions->check('Deliberations/tousLesProjetsSansSeance', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A attibuer', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsSansSeance'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          if($this->permissions->check('Deliberations/tousLesProjetsValidation', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A valider', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsValidation'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          if($this->permissions->check('Deliberations/tousLesProjetsAFaireVoter', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A faire voter', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsAFaireVoter'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          if($this->permissions->check('Deliberations/tousLesProjetsRecherche', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A faire voter', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsRecherche'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          ?></ul>
                                <ul class="col-sm-6  list-unstyled">
                    <li><p><strong>Autres Actes...</strong></p></li>
                    <?php
                    if($this->permissions->check('Deliberations/autresActesAValider', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('A valider', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autresActesAValider'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                    if($this->permissions->check('Deliberations/autreActesValides', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Validés', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesValides'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                        
                    }
                    if($this->permissions->check('Deliberations/autreActesAEnvoyer', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link('A télétranmettre', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesAEnvoyer'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                    if($this->permissions->check('Deliberations/autreActesEnvoyes', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Télétranmis', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesEnvoyes'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                    if($this->permissions->check('Deliberations/nonTransmis', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Non Transmis', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'nonTransmis'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                   ?>
                    </ul>
                    </div>
                        </div>
                </li>
                <?php
                    if($this->permissions->check('Deliberations/tousLesProjetsRecherche', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link($this->Bs->icon('search').' '.'Rechercher', array('admin' => false,'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsRecherche'),array('escape'=>false)));
                    }
                ?>
            </ul>
        </li>
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
    
    return;
?>
<script>
$(document).on('click', '.yamm .dropdown-menu', function(e) {
  e.stopPropagation()
})
</script>-->
    
    
    
    <nav class="navbar yamm navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    <?php echo $this->Html->link($collectivite['nom'], array(
                'admin' => false,
                'plugin'=> null, 
                'controller' => 'pages', 
                'action' => 'home'),array('class'=>'navbar-brand')); ?>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
         <li>
             <?php echo $this->Bs->btn(null, array(
                                                    'admin' => false,
                                                    'plugin'=> null,
                                                    'controller' => 'pages', 
                                                    'action' => 'home'
                                                    ),
                 array('icon'=>'glyphicon glyphicon-home"')); 
             ?>
            </li>
            
            <li class="dropdown">
            <?php echo $this->Bs->link('Mes projets <span class="caret"></span>', '#',
                 array(
                     'escape'=>false,
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
            <ul class="dropdown-menu" role="menu">
            <?php
            if($this->permissions->check('Deliberations', 'create'))
            {
                echo $this->Bs->tag('li', $this->Bs->link('Nouveau', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'add'),
                 array('icon' => 'glyphicon glyphicon-plus')));
            }
            if($this->permissions->check('Deliberations/mesProjetsRedaction'))
            { 
                echo $this->Bs->tag('li', $this->Bs->link('En cours de rédaction', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsRedaction'),
                 array('icon' => 'glyphicon glyphicon-plus')));
            }
            if($this->permissions->check('Deliberations/mesProjetsValides'))
            {    
                echo $this->Bs->tag('li', $this->Bs->link('Validés', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsValides'),
                 array('icon' => 'glyphicon glyphicon-plus')));
            }
            if($this->permissions->check('Deliberations/mesProjetsATraiter'))
            {
                echo $this->Bs->tag('li', $this->Bs->link('A traiter', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsATraiter'),
                 array('icon' => 'glyphicon glyphicon-plus')));
            }
            if($this->permissions->check('Deliberations/projetsMonService'))
            {
                echo '<li class="divider"></li>';
                echo $this->Bs->tag('li', $this->Bs->link('Mon service', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'projetsMonService'),
                 array('icon' => 'glyphicon glyphicon-plus')));
            }
            if($this->permissions->check('Deliberations/mesProjetsRecherche'))
            {
                echo '<li class="divider"></li>';
                echo $this->Bs->tag('li', $this->Bs->link('Rechercher', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsRecherche'),
                 array('icon' => 'glyphicon glyphicon-search')));
            }
            ?>
                </ul>
        </li>
        
        <li class="dropdown">
            <?php echo $this->Bs->link('Tous les projets <span class="caret"></span>', '#',
                 array(
                     'escape'=>false,
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
            //list-unstyled"
             ?>
            <ul class="dropdown-menu">
                <li>
                    <div class="yamm-content">
                    <div class="row">
                    <ul class="col-sm-6  list-unstyled">
                            <li><p><strong>Délibérations</strong></p></li>
                            <?php
                          if($this->permissions->check('Deliberations/tousLesProjetsSansSeance', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A attibuer', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsSansSeance'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          if($this->permissions->check('Deliberations/tousLesProjetsValidation', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A valider', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsValidation'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          if($this->permissions->check('Deliberations/tousLesProjetsAFaireVoter', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A faire voter', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsAFaireVoter'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          if($this->permissions->check('Deliberations/tousLesProjetsRecherche', '*'))
                          {
                              echo $this->Bs->tag('li', $this->Bs->link('A faire voter', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsRecherche'),
                               array('icon' => 'glyphicon glyphicon-plus')));
                          }
                          ?></ul>
                                <ul class="col-sm-6  list-unstyled">
                    <li><p><strong>Autres Actes...</strong></p></li>
                    <?php
                    if($this->permissions->check('Deliberations/autresActesAValider', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('A valider', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'autresActesAValider'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                    if($this->permissions->check('Deliberations/autreActesValides', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Validés', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesValides'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                        
                    }
                    if($this->permissions->check('Deliberations/autreActesAEnvoyer', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link('A télétranmettre', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesAEnvoyer'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                    if($this->permissions->check('Deliberations/autreActesEnvoyes', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Télétranmis', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesEnvoyes'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                    if($this->permissions->check('Deliberations/nonTransmis', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Non Transmis', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'nonTransmis'),
                         array('icon' => 'glyphicon glyphicon-plus')));
                    }
                   ?>
                    </ul>
                    </div>
                        </div>
                </li>
                <?php
                    if($this->permissions->check('Deliberations/tousLesProjetsRecherche', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link($this->Bs->icon('search').' '.'Rechercher', array('plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsRecherche'),array('escape'=>false)));
                    }
                ?>
            </ul>
        </li>
        
        <li class="dropdown">
            <?php echo $this->Bs->link('Séances <span class="caret"></span>', '#',
                 array(
                     'escape'=>false,
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
          <ul class="dropdown-menu" role="menu">
              <?php
                    if($this->permissions->check('Seances', 'create'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link($this->Bs->icon('plus').
                                ' '.'Nouvelle', array(
                                    'plugin'=>null, 
                                    'controller'=>'seances', 
                                    'action'=>'add'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Seances', 'read'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('A traiter', array(
                                    'plugin'=>null, 
                                    'controller'=>'seances', 
                                    'action'=>'index'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Seances', 'read'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Passées', array(
                                    'plugin'=>null, 
                                    'controller'=>'seances', 
                                    'action'=>'listerAnciennesSeances'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Seances', 'read'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link($this->Bs->icon('calendar').
                                ' '.'Calendrier', array(
                                    'plugin'=>null, 
                                    'controller'=>'seances', 
                                    'action'=>'listerAnciennesSeances'), array('escape'=>false)));
                    }
                ?>
          </ul>
        </li>
        
        <li class="dropdown">
            <?php echo $this->Bs->link('Post-séances <span class="caret"></span>', '#',
                 array(
                     'escape'=>false,
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
          <ul class="dropdown-menu" role="menu">
              <?php
              if($this->permissions->check('Postseances', 'read'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link($this->Bs->icon('plus').
                                ' '.'Editions', array(
                                    'plugin'=>null, 
                                    'controller'=>'postseances', 
                                    'action'=>'index'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Deliberations/sendToParapheur', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link('Signatures', array(
                                    'plugin'=>null, 
                                    'controller'=>'deliberations', 
                                    'action'=>'sendToParapheur'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Deliberations/toSend', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link('A télétransmettres', array(
                                    'plugin'=>null, 
                                    'controller'=>'deliberations', 
                                    'action'=>'toSend'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Deliberations/transmit', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link('Télétransmises', array(
                                    'plugin'=>null, 
                                    'controller'=>'deliberations', 
                                    'action'=>'transmit'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Deliberations/sendToSae', '*'))
                    {
                        echo '<li class="divider"></li>';
                        echo $this->Bs->tag('li', $this->Bs->link('Versement SAE', array(
                                    'plugin'=>null, 
                                    'controller'=>'deliberations', 
                                    'action'=>'sendToSae'), array('escape'=>false)));
                    }
                    
              ?>
          </ul>
        </li>
        
        <li class="dropdown">
            <?php echo $this->Bs->link('Administration <span class="caret"></span>', '#',
                 array(
                     'escape'=>false,
                     'icon'=>'glyphicon glyphicon-menu-hamburger',
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
          <ul class="dropdown-menu" role="menu">
              <li class="dropdown-submenu">
            <?php 
                    echo $this->Bs->link('Générale', '#',
                    array(
                        'escape'=>false,
                        'icon'=> 'glyphicon glyphicon-user',
                        'class'=>'dropdown-toggle',
                           'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                        )); 
             ?>
            <ul class="dropdown-menu" role="menu">
                <?php
                    if($this->permissions->check('Collectivites', '*'))
                    {
                        echo $this->Bs->tag('li', $this->Bs->link($this->Bs->icon('building-o').
                                ' '.'Collectivité', array(
                                    'plugin'=>null, 
                                    'controller'=>'Collectivites', 
                                    'action'=>'index'), array('escape'=>false)));
                    }
                    if($this->permissions->check('Themes', '*'))
                    {
                        echo $this->Bs->tag('li', array('Thèmes', array(
                                    'plugin'=>null, 
                                    'controller'=>'Themes', 
                                    'action'=>'index'), array('escape'=>false)));
                    }
                    if($this->permissions->check('modelOdtValidator/modeltemplates', '*'))
                    {
                        echo $this->Bs->tag('li', array('Modèles d\'édition', array(
                                    'plugin'=> 'model_odt_validator', 
                                    'controller'=>'modeltemplates', 
                                    'action'=>'index'), array('escape'=>false)));
                    }
                ?>
                <li><a href="#">Modèles d'édition</a></li>
                <li><a href="#">Séquences</a></li>
                <li><a href="#">Compteurs</a></li>
                <li><?php echo $this->Bs->link('Types d\'actes', array('admin' => true,
              'plugin'=>null, 'controller'=>'typeactes', 'action'=>'index'),
             array('icon' => 'glyphicon glyphicon-plus')); 
            ?></li>
                <li><a href="#">Types de séance</a></li>
            </ul>
          </li>
            
            <li class="divider"></li>
            <li class="dropdown-submenu">
            <?php echo $this->Bs->link('Utilisateurs', '#',
                 array(
                     'escape'=>false,
                     'icon'=> 'glyphicon glyphicon-user',
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
            <ul class="dropdown-menu" role="menu">
            <li><?php echo $this->Bs->link('Utilisateurs', array('admin' => true,
              'plugin'=>null, 'controller'=>'users', 'action'=>'index'),
             array('icon' => 'glyphicon glyphicon-plus')); 
            ?></li>
              <li><?php echo $this->Bs->link('Profils', array('admin' => true,
              'plugin'=>null, 'controller'=>'profils', 'action'=>'index'),
             array('icon' => 'glyphicon glyphicon-plus')); 
            ?></li>
              <li><?php echo $this->Bs->link('Services', array('admin' => true,
              'plugin'=>null, 'controller'=>'services', 'action'=>'index'),
             array('icon' => 'glyphicon glyphicon-plus')); 
            ?>
                  <a href="#">Services</a></li>
              <li><?php echo $this->Bs->link('Circuits', array('admin' => true,
              'plugin'=>'Cakeflow', 'controller'=>'circuits', 'action'=>'index'),
             array('icon' => 'glyphicon glyphicon-plus')); 
            ?></li>
            </ul>
          </li>
            <li class="divider"></li>
            <li class="dropdown-submenu">
            <?php echo $this->Bs->link('Acteurs', '#',
                 array(
                     'escape'=>false,
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
            <ul class="dropdown-menu" role="menu">
              <li><a href="#">Type d'acteurs</a></li>
              <li><a href="#">Acteurs</a></li>
            </ul>
          </li>
            <li class="divider"></li>
            <li class="dropdown-submenu">
            <?php echo $this->Bs->link('Informations sup.', '#',
                 array(
                     'escape'=>false,
                     'icon'=> 'glyphicon glyphicon-user',
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
            <ul class="dropdown-menu" role="menu">
              <li><a href="#">Projet</a></li>
              <li><a href="#">Seance</a></li>
            </ul>
          </li>
          <?php 
            if($this->permissions->check('Connecteurs', '*') || $this->permissions->check('Crons', '*')){
                    ?>
          <li class="divider"></li>
            <li class="dropdown-submenu">
            <?php 
            echo $this->Bs->link('Maintenance', '#',
                 array(
                     'escape'=>false,
                     'icon'=> 'glyphicon glyphicon-user',
                     'class'=>'dropdown-toggle',
                        'data-toggle'=>'dropdown', 'role'=>'button', 'aria-expanded'=>'false'
                     )); 
             ?>
            <ul class="dropdown-menu" role="menu">
              <li><a href="#">Configuration des connecteurs</a></li>
              <li><a href="#">Tâches automatiques</a></li>
            </ul>
          </li>
          <?php } ?>
          </ul>
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
                    <?php echo $this->Html->link('Changer le format de sortie des éditions', array('admin' => false, 'plugin' => null, 'controller' => 'pages', 'action' => 'format')); ?>
                </li>
                <li>
                    <?php echo $this->Html->link('Changer le service émetteur', array('admin' => false, 'plugin' => null, 'controller' => 'pages', 'action' => 'service')); ?>
                </li>
                <li>
                    <?php echo $this->Html->link('Changer de mot de passe', array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeUserMdp')); ?>
                </li>
                <li>
                    <?php echo $this->Html->link('Changer de thême', array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeTheme')); ?>
                </li>
                <li class="divider"></li>
                <li>
                    <?php echo $this->Html->link('Se déconnecter', array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'logout')); ?>
                </li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<script>
$(document).on('click', '.yamm .dropdown-menu', function(e) {
  e.stopPropagation()
})
</script>
