<?php
echo $this->Bs->tag('h3', __('Accueil'));
//<div id="content">
//    <div id="tableau_bord">
if ($this->permissions->check('mesProjetsATraiter')) {
    echo $this->requestAction(
            array(  
                'admin'=>false,
                'prefix'=> null,
                'plugin'=> null,
                'controller' => 'deliberations', 
                'action' => 'mesProjetsATraiter'), 
            array('return', 'render' => 'banette')
            ) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($this->permissions->check('mesProjetsValidation')) {
    echo $this->requestAction(
            array(
                'admin'=>false,
                'prefix'=> null,
                'plugin'=> null,
                'controller' => 'deliberations', 
                'action' => 'mesProjetsValidation'), 
            array('return', 'render' => 'banette')
            ) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($this->permissions->check('Deliberations', 'create')) {
    echo $this->requestAction(
            array('admin'=>false,
                'prefix'=> null,
                'plugin'=> null,
                'controller' => 'deliberations', 
                'action' => 'mesProjetsRedaction'), array('return', 'render' => 'banette')) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($this->permissions->check('Seances', 'create')) {
    echo $this->requestAction(array(
        'admin'=>false,
        'prefix'=> null,
        'plugin'=> null,
        'controller' => 'seances', 
        'action' => 'index'), array('return', 'render' => 'banette'));
}