<?php

$this->Html->addCrumb(__('Liste des circuits'), array('controller' => 'circuits', 'action' => 'index'));
$this->Html->addCrumb(__('Visualisation du circuit'));

$affichage = $this->element('circuit');

$affichage .= $this->Html2->btnCancel(array('Controller' => 'circuits', 'action' => 'index'));
echo $this->Html->tag('div', $affichage, array('class' => 'circuit', 'id' => 'etapes'));
?>
