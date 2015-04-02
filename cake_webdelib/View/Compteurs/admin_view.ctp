<?php
$this->Html->addCrumb('Liste des compteurs', array('action'=>'index'));
$this->Html->addCrumb('Compteur : '.$compteur['Compteur']['nom']);

$panel_left = '<b>Nom : </b>'.$compteur['Compteur']['nom'].'<br>' .
              '<b>Définition du compteur : </b>'.$compteur['Compteur']['def_compteur'].'<br>' .
              '<b>Critère de réinitialisation de la séquence : </b>'.$compteur['Compteur']['def_reinit'].'<br>' .
              '<b>Date de création : </b>'.$compteur['Compteur']['created'].'<br>';
$panel_right = '<b>Commentaire : </b>'.$compteur['Compteur']['commentaire'].'<br>' .
               '<b>Nom et numéro de la séquence : </b>' . $compteur['Sequence']['nom'].' : '.$compteur['Sequence']['num_sequence'].'<br>' .
               '<b>Dernière valeur calculée du critère de réinitialisation : </b>'.$compteur['Compteur']['val_reinit'].'<br>' .
               '<b>Date de modification : </b>'.$compteur['Compteur']['modified'].'<br>';

echo $this->Bs->tag('h3', 'Compteur') .
$this->Bs->panel('Fiche Compteur: '.$compteur['Compteur']['nom']) .
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
$this->Bs->btn('Modifier', array('controller' => 'compteurs', 'action' => 'edit', $compteur['Compteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
$this->Bs->close(6);
