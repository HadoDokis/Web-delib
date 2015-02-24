<?php
echo $this->Bs->tag('h3', __('Mon tableau de bord'));
//<div id="content">
//    <div id="tableau_bord">
if ($this->permissions->check('Deliberations/mesProjetsATraiter', 'update')) {
    echo $this->requestAction(
            array('controller' => 'deliberations', 'action' => 'mesProjetsATraiter'), 
            array('return', 'render' => 'banette')
            ) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($this->permissions->check('Deliberations/mesProjetsValidation', 'update')) {
    echo $this->requestAction(
            array('controller' => 'deliberations', 'action' => 'mesProjetsValidation'), 
            array('return', 'render' => 'banette')
            ) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($this->permissions->check('Deliberations/mesProjetsRedaction', 'create')) {
    echo $this->requestAction(
            array('controller' => 'deliberations', 'action' => 'mesProjetsRedaction'), array('return', 'render' => 'banette')) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($this->permissions->check('Seances.index', 'create')) {
    echo $this->requestAction(array('controller' => 'seances', 'action' => 'index'), array('return', 'render' => 'banette'));
}