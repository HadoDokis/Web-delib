<?php
$this->Html->addCrumb('Liste des types d\'acteur');
echo $this->Bs->tag('h3', 'Liste des types d\'acteur') .
 $this->Bs->table(array(array('title' => 'Libellé'),
    array('title' => 'Commentaire'),
    array('title' => 'Statut'),
    array('title' => 'Actions')
        ), array('hover', 'striped'));
foreach ($typeacteurs as $typeacteur) {
    echo $this->Bs->cell($typeacteur['Typeacteur']['nom']);
    echo $this->Bs->cell($typeacteur['Typeacteur']['commentaire']);
    echo $this->Bs->cell($typeacteur['Typeacteur']['elu'] ? 'élu' : 'non élu');
    echo $this->Bs->cell(
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'typeacteurs', 'action' => 'view', $typeacteur['Typeacteur']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'typeacteurs', 'action' => 'edit', $typeacteur['Typeacteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'typeacteurs', 'action' => 'delete', $typeacteur['Typeacteur']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !empty($typeacteur['Acteur']) ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $typeacteur['Typeacteur']['nom'] . ' ?') .
        $this->Bs->close()
    );
}
echo $this->Bs->endTable() .
 $this->Html2->btnAdd("Ajouter un type d'acteur", "Ajouter");