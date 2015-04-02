<?php
echo $this->Bs->tag('h3', $titre) .
        $this->element('filtre').
 $this->Bs->table(array(array('title' => 'Ordre'),
    array('title' => 'Libellé'),
    array('title' => 'Commentaire'),
    array('title' => 'Code'),
    array('title' => 'Type'),
    array('title' => 'Recherche'),
    array('title' => 'Active'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($this->data as $infosupdef) {
   
//Bouttons actions
$actions = $this->Bs->div('btn-group').
           $this->Bs->btn(null, array('controller' => 'infosupdefs', 'action' => 'view', $infosupdef['Infosupdef']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir'));
    if (in_array($infosupdef['Infosupdef']['type'], array('list','listmulti')))       
        $actions.=$this->Bs->btn(null, array('controller' => 'infosuplistedefs', 'action' => 'index', $infosupdef['Infosupdef']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-th-list', 'title' => 'Liste'));
    $actions .= $this->Bs->btn(null, array('controller' => 'infosupdefs', 'action' => 'edit', $infosupdef['Infosupdef']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
                $this->Bs->btn(null, array('controller' => 'infosupdefs', 'action' => 'delete', $infosupdef['Infosupdef']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$Infosupdef->isDeletable($infosupdef['Infosupdef']['id']) ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer ' . $infosupdef['Infosupdef']['nom'] . ' ?') .
                $this->Bs->close();

    echo $this->Bs->cell(
        $this->Bs->btn('', array('controller' => 'infosupdefs', 'action' => 'changerOrdre', $infosupdef['Infosupdef']['id'], 0), array('icon'=>'glyphicon glyphicon-chevron-up','escape' => false), false).
        $this->Bs->btn('', array('controller' => 'infosupdefs', 'action' => 'changerOrdre', $infosupdef['Infosupdef']['id']), array('icon'=>'glyphicon glyphicon-chevron-down','escape' => false), false)
        );
    echo $this->Bs->cell($infosupdef['Infosupdef']['nom']);
    echo $this->Bs->cell($infosupdef['Infosupdef']['commentaire']);
    echo $this->Bs->cell($infosupdef['Infosupdef']['code']);
    echo $this->Bs->cell($Infosupdef->libelleType($infosupdef['Infosupdef']['type']));
    echo $this->Bs->cell($Infosupdef->libelleRecherche($infosupdef['Infosupdef']['recherche']));
    echo $this->Bs->cell($Infosupdef->libelleActif($infosupdef['Infosupdef']['actif']));
    echo $this->Bs->cell($actions);
}
echo $this->Bs->endTable() .
    $this->Bs->btn('Ajouter une information supplémentaire', $lienAdd, array('id' => 'bouton_ajouter_infosup', 'type' => "primary",'icon'=>'glyphicon glyphicon-plus', 'escape' => false, 'title' => 'Ajouter une information supplémentaire'));