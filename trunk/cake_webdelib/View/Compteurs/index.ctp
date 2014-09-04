<?php

echo $this->Bs->tag('h3', 'Liste des compteurs') .
 $this->Bs->table(array(array('title' => 'Libellé'),
    array('title' => 'Commentaire'),
    array('title' => 'Définition'),
    array('title' => 'Critère de réinitialisation'),
    array('title' => 'Séquence'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($compteurs as $compteur) {
    echo $this->Bs->tableCells(array(
        $compteur['Compteur']['nom'],
        $compteur['Compteur']['commentaire'],
        $compteur['Compteur']['def_compteur'],
        $compteur['Compteur']['def_reinit'],
        $compteur['Sequence']['nom'].' : '.$compteur['Sequence']['num_sequence'],
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'compteurs', 'action' => 'view', $compteur['Compteur']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'compteurs', 'action' => 'edit', $compteur['Compteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'compteurs', 'action' => 'delete', $compteur['Compteur']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => empty($compteur['Typeseance'])? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $compteur['Compteur']['nom'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
 $this->Html2->btnAdd("Ajouter un type d'acte", "Ajouter");