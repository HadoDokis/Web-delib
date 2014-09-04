<?php
echo $this->Bs->tag('h3', 'Liste des types de séance') .
 $this->Bs->table(array(array('title' => 'Libellé'),
    array('title' => 'Nb jours avant retard'),
    array('title' => 'Action'),
    array('title' => 'Compteur'),
    array('title' => 'Modèles'),
    array('title' => 'Convocations'),
    array('title' => 'Actions'),
),array('hover','striped'));
foreach ($typeseances as $typeseance) {
    $Typeacteur='';
    if(!empty($typeseance['Typeacteur'])) {
            $Typeacteur='Types d\'acteur :<br/>';
            foreach ($typeseance['Typeacteur'] as $typeacteur)
                    $Typeacteur.= '&nbsp;&nbsp;'.$typeacteur['nom'].$this->Html->tag(null, '<br />');
    }
    $Acteur='';
    if(!empty($typeseance['Acteur'])) {
           $Acteur='Acteurs :<br/>';
           foreach ($typeseance['Acteur'] as $acteur)
                   $Acteur.= '&nbsp;'.$acteur['prenom'].' '.$acteur['nom'].$this->Html->tag(null, '<br />');
    }
                
    echo $this->Bs->tableCells(array(
        $typeseance['Typeseance']['libelle'],
        $typeseance['Typeseance']['retard'],
        $typeseance['Typeseance']['action'],
        $typeseance['Compteur']['nom'],
       'Projet : ' .       $typeseance['Modelprojet']['name']. 
        $this->Html->tag(null, '<br />') .
        'D&eacute;lib&eacute;ration : ' .  $typeseance['Modeldeliberation']['name'] .
        $this->Html->tag(null, '<br />') .
        'Convocation : ' .  $typeseance['Modelconvocation']['name'] .
        $this->Html->tag(null, '<br />') .
        'Orde du jour : ' . $typeseance['Modelordredujour']['name'] .
        $this->Html->tag(null, '<br />') .
        'PV sommaire : ' .  $typeseance['Modelpvsommaire']['name'] .
        $this->Html->tag(null, '<br />') .
        'PV d&eacute;taill&eacute; : ' .  $typeseance['Modelpvdetaille']['name'] ,
        $Typeacteur.$Acteur,
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'typeseances', 'action' => 'view', $typeseance['Typeseance']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'typeseances', 'action' => 'edit', $typeseance['Typeseance']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'typeseances', 'action' => 'delete', $typeseance['Typeseance']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$typeseance['Typeseance']['is_deletable'] ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $typeseance['Typeseance']['libelle'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
 $this->Html2->btnAdd("Ajouter un type de séance", "Ajouter");
