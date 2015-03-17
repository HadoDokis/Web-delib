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
            'admin'=>false,
            'plugin' => null,
            'controller' => 'deliberations',
            'action' => 'search')));
 
    $block.= $this->BsForm->hidden('type', array('value'=>'quick'));
    $this->BsForm->setMax();
    $block.= '<div class="form-group">';
    $block.= $this->BsForm->inputGroup('User.search', array(array(
                                'content'=>'',
                                'id' => 'search_tree_button',
                                'icon'=>'glyphicon glyphicon-search',
                                'title' => __('Rechercher un service'),
                                'type' => 'button',
                                'state' => 'primary',
    ), array(
    'content'=>'<span class="caret"></span>',
                                'class' => 'dropdown-toggle',
                                'title' => __('Option de recherche'),
                                'data-toggle' => 'dropdown',
                                'icon'=>'glyphicon glyphicon-cog',
                                'after'=>'<ul class="dropdown-menu dropdown-menu-right" role="menu">
            <li><a id="search_tree_erase_button" title="Remettre à zéro la recherche">Effacer la recherche</a></li>
            <li class="divider"></li>
            <li>'.$this->Bs->btn('Recherche détaillée', array(
                'admin'=>false,
                'prefix'=> null,
                'controller'=>'deliberations', 'action'=>'search', 'all'),
                    array('title'=>'Recherche détaillée')).'</i></li>
        </ul>',
                                'type' => 'button',
                                'state' => 'default')
    ), array(
        'placeholder'=>__('Rechercher'),
        'class' => 'form-control span2',
        'autocomplete' => 'off'
    ), array('multiple'=>true, 'side'=>'right', 
        ));
    $block.= '</div>';
    $this->BsForm->setDefault();
    
    /*$block.= '<div class="form-group">';
    $block.= $this->Form->input('User.search', array(
        'class' => 'form-control span2',
        'div' => false,
        'label' => false,
        'id' => 'searchInput',
        'placeholder' => 'Rechercher',
        'autocomplete' => 'off'));
    $block.= '</div>';*/
         
    /*block.= '<div class="form-group">';
    $block.= $this->Form->input('User.search', array(
        'class' => 'form-control span2',
        'div' => false,
        'label' => false,
        'id' => 'searchInput',
        'placeholder' => 'Rechercher',
        'autocomplete' => 'off'));
    $block.= '</div>';*/
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
if ($this->permissions->check('changeFormat')) {
$this->Navbar->link('Changer le format de sortie des éditions', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeFormat'),
            array('escape'=>false));
}
$this->Navbar->link('Changer le service émetteur', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'service'),
            array('escape'=>false));
if ($this->permissions->check('changeUserMdp')) {
$this->Navbar->link('Changer de mot de passe', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeUserMdp'),
            array('escape'=>false));
}
if ($this->permissions->check('changeTheme')) {
$this->Navbar->link('Changer de thème', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'changeTheme'),
            array('escape'=>false));
}
$this->Navbar->link('Se déconnecter', 
            array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'logout'),
            array('escape'=>false));
$this->Navbar->endMenu();

echo $this->Navbar->end(true);