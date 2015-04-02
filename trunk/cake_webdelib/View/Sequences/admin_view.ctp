<?php
$this->Html->addCrumb('Liste des séquences', array('action'=>'index'));

$panel_left = '<b>Libelle : </b>'.$sequence['Sequence']['nom'].'<br>' .
              '<b>Numéro de la séquence : </b>'.$sequence['Sequence']['num_sequence'].'<br>' .
              '<b>Date de création : </b>'.$sequence['Sequence']['created'].'<br>';
$panel_right = '<b>Commentaire : </b>'.$sequence['Sequence']['commentaire'].'<br>' .
               '<b>Date de modification : </b>'.$sequence['Sequence']['modified'].'<br>';

echo $this->Bs->tag('h3', 'Séquence') .
$this->Bs->panel('Fiche séquence : '.$sequence['Sequence']['nom']) .
    $this->Bs->row() .
    $this->Bs->col('xs6').$panel_left .
    $this->Bs->close() .
    $this->Bs->col('xs6').$panel_right .
    $this->Bs->close(2) .
$this->Bs->endPanel() .
$this->Bs->row() .
$this->Bs->col('md4 of5') .
$this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
$this->Html2->btnCancel() .
$this->Bs->btn('Modifier', array('controller' => 'sequences', 'action' => 'edit', $sequence['Sequence']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
$this->Bs->close(6);