<?php
$this->Html->addCrumb('Liste des types d\'acte');
echo $this->Bs->tag('h3', 'Liste des types d\'acte') .
 $this->Bs->table(array(array('title' => 'Libellé'),
    array('title' => 'Compteur'),
    array('title' => 'Modèles'),
    array('title' => 'Nature'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($typeactes as $typeacte) {
    echo $this->Bs->tableCells(array(
        $typeacte['Typeacte']['name'],
        $typeacte['Compteur']['nom'],
        'Document préparatoire : ' . $typeacte['Modelprojet']['name'] .
        $this->Html->tag(null, '<br />') .
        'Document final : ' . $typeacte['Modeldeliberation']['name'],
        $typeacte['Nature']['name'],
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'typeactes', 'action' => 'view', $typeacte['Typeacte']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'typeactes', 'action' => 'edit', $typeacte['Typeacte']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'typeactes', 'action' => 'delete', $typeacte['Typeacte']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$typeacte['Typeacte']['is_deletable'] ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $typeacte['Typeacte']['name'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
 $this->Html2->btnAdd("Ajouter un type d'acte", "Ajouter");
