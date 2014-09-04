<?php

echo $this->Bs->tag('h3', __('Mon tableau de bord'));
//<div id="content">
//    <div id="tableau_bord">
if ($Droit->check($this->Session->read('user.User.id'), 'Deliberations:mesProjetsATraiter')) {
    echo $this->requestAction(
            array('controller' => 'deliberations', 'action' => 'mesProjetsATraiter'), 
            array('return', 'render' => 'banette')
            ) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($Droit->check($this->Session->read('user.User.id'), 'Deliberations:mesProjetsValidation')) {
    echo $this->requestAction(
            array('controller' => 'deliberations', 'action' => 'mesProjetsValidation'), 
            array('return', 'render' => 'banette')
            ) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($Droit->check($this->Session->read('user.User.id'), 'Deliberations:mesProjetsRedaction')) {
    echo $this->requestAction(
            array('controller' => 'deliberations', 'action' => 'mesProjetsRedaction'), array('return', 'render' => 'banette')) .
    $this->Bs->tag(null, '<br/><br/>');
}
if ($Droit->check($this->Session->read('user.User.id'), 'Seances:listerFuturesSeances')) {
    echo $this->requestAction(array('controller' => 'seances', 'action' => 'listerFuturesSeances'), array('return', 'render' => 'banette'));
}