<?php

echo $this->Bs->tag('h3', 'Liste des séquences') .
 $this->Bs->table(array(array('title' => 'Libellé'),
    array('title' => 'Commentaire'),
    array('title' => 'Numéro de séquence'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($sequences as $sequence) {
    echo $this->Bs->tableCells(array(
        $sequence['Sequence']['nom'],
        $sequence['Sequence']['commentaire'],
        $sequence['Sequence']['num_sequence'],
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'sequences', 'action' => 'view', $sequence['Sequence']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'sequences', 'action' => 'edit', $sequence['Sequence']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'sequences', 'action' => 'delete', $sequence['Sequence']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => empty($sequence['Compteur']) ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $sequence['Sequence']['nom'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
 $this->Html2->btnAdd("Ajouter une séquence", "Ajouter");
