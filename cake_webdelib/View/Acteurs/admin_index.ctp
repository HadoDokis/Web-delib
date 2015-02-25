<?php

$this->Html->addCrumb('Liste des acteurs');

echo $this->Bs->tag('h3', 'Liste des acteurs') .
 $this->Bs->table(array(array('title' => 'Civilité'),
    array('title' => $this->Paginator->sort( 'nom', 'Nom')),
    array('title' => $this->Paginator->sort('prenom', 'Prénom')),
    array('title' => $this->Paginator->sort('titre', 'Titre')),
    array('title' => $this->Paginator->sort( 'Typeacteur.nom', 'Type d\'acteur')), 
    array('title' => 'Elus'),
    array('title' => $this->Paginator->sort('position', 'N° d\'ordre')),
    array('title' => 'Téléphone'),
    array('title' => 'Suppléant'),
    array('title' => $this->Paginator->sort('Service.libelle',  'Délégation(s)')),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($acteurs as $acteur) {
    $service='';
    foreach ($acteur['Service'] as $aService) 
        $service.=$aService['libelle'].$this->Html->tag(null, '<br />');
          
    echo $this->Bs->tableCells(array(
        $acteur['Acteur']['salutation'],
        $acteur['Acteur']['nom'],
        $acteur['Acteur']['prenom'],
        $acteur['Acteur']['titre'],
        $acteur['Typeacteur']['nom'],
        $acteur['Typeacteur']['elu'],
        $acteur['Acteur']['libelleOrdre'],
        'Fixe :'.$this->Html->tag(null, '<br />').$acteur['Acteur']['telfixe'].$this->Html->tag(null, '<br />').
        'Mobile :'.$this->Html->tag(null, '<br />').$acteur['Acteur']['telmobile'],
        isset( $acteur['Acteur']['suppleant_id'])? $acteur['Suppleant']['prenom']." ".$acteur['Suppleant']['nom']:'',
        $service,
        $this->Bs->col() .$this->Bs->row(). 
        $this->Bs->div('btn-group-vertical') .
        $this->Bs->btn(null, array('controller' => 'acteurs', 'action' => 'view', $acteur['Acteur']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'acteurs', 'action' => 'edit', $acteur['Acteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'acteurs', 'action' => 'delete', $acteur['Acteur']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer'), 'Êtes vous sur de vouloir supprimer :' . $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'] . ' ?') .
        $this->Bs->close(3)
    ));
}
echo $this->Bs->endTable() .
    $this->Paginator->numbers(array(
    'before' => '<ul class="pagination">',
    'separator' => '',
   'currentClass' => 'active',
    'currentTag' => 'a',
    'tag' => 'li',
    'after' => '</ul><br />'
)).
$this->Html2->btnAdd("Ajouter un acteur", "Ajouter");  