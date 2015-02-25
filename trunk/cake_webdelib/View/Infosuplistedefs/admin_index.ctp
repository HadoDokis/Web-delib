<?php

echo $this->Bs->tag('h3', 'Gestion de la liste de l\'information supplémentaire : '.$infosupdef['Infosupdef']['nom']) .
 $this->element('filtre').
        $this->Bs->table(array(array('title' => 'Ordre'),
    array('title' => 'Libellé'),
    array('title' => 'Actif'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($this->data  as $infosuplistedef) {
    echo $this->Bs->tableCells(array(
        $infosuplistedef['Infosuplistedef']['ordre'].
                $this->Bs->btn('', array('controller' => 'infosuplistedefs', 'action' => 'changerOrdre', $infosuplistedef['Infosuplistedef']['id'], 0), array('icon'=>'glyphicon glyphicon-chevron-up','escape' => false), false).
        $this->Bs->btn('', array('controller' => 'infosuplistedefs', 'action' => 'changerOrdre', $infosuplistedef['Infosuplistedef']['id']), array('icon'=>'glyphicon glyphicon-chevron-down','escape' => false), false),
        $infosuplistedef['Infosuplistedef']['nom'],
        $Infosuplistedef->libelleActif($infosuplistedef['Infosuplistedef']['actif']),
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'infosuplistedefs', 'action' => 'edit', $infosuplistedef['Infosuplistedef']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'infosuplistedefs', 'action' => 'delete', $infosuplistedef['Infosuplistedef']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$Infosuplistedef->isDeletable($infosuplistedef['Infosuplistedef']['id']) ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $infosuplistedef['Infosuplistedef']['nom'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable().
         $this->Html2->btnAdd('Ajouter un élément', 'Ajouter', array('action'=>'add', $infosupdef['Infosupdef']['id']));
//
//    <?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', array('controller'=>'infosupdefs', 'action'=>'index'), array('class'=>'btn',  'escape' => false), false); 
    //echo $this->Html->link('<i class="fa fa-plus"></i> Ajouter un élément', array('action'=>'add', $infosupdef['Infosupdef']['id']), array('class'=>'btn btn-primary', 'escape' => false, 'title'=>'Ajouter un élément'), false); 
