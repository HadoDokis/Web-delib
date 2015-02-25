<?php
echo $this->Html->css("Cakeflow.design.css");
echo $this->Html->tag('h3', __('Liste des circuits de traitement', true));
$this->Html->addCrumb(__('Liste des circuits'));
echo $this->element('indexPageCourante');
// initialisation de l'entete du tableau
$tableHeaders = array(
    array('title' => $this->Paginator->sort(__('nom', true), 'Nom')),
    array('title' => __('Description', true)),
    array('title' => __('Etapes', true)),
    array('title' => $this->Paginator->sort(__('actif', true), 'Actif')));
if (CAKEFLOW_GERE_DEFAUT)
    $tableHeaders[] = array('title' => __('Défaut', true));
$tableHeaders[] = array('title' => __('Actions', true));
echo $this->Bs->table(
           $tableHeaders , array('striped')
    );
foreach ($this->data as $rownum => $rowElement) {
    echo $this->Bs->cell($rowElement['Circuit']['nom']);
    echo $this->Bs->cell($rowElement['Circuit']['description']);
    $etapes= '';
    foreach ($rowElement['Etape'] as $etape) {
         $etapes .= $etape['Etape']['nom'] . ' (' . $listeType[$etape['Etape']['type']] . ')' . '<br/>';
    }
    echo $this->Bs->cell($etapes);
    echo $this->Bs->cell($rowElement['Circuit']['actifLibelle']);
    if (CAKEFLOW_GERE_DEFAUT)
        echo $this->Bs->cell($rowElement['Circuit']['defautLibelle']);
    $boutons = $this->Bs->div('btn-group');
    $boutons .= $this->Bs->btn(null,array('controller' => 'etapes', 'action' => 'index', $rowElement['Circuit']['id']), array('type' => 'info', 'icon' => 'glyphicon glyphicon-list', 'title' =>  __('étapes', true)));
    $boutons .= $this->Bs->btn(null,array('action' => 'view', $rowElement['Circuit']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open',array( 'title' =>  __('Voir', true),'class' => !$rowElement['ListeActions']['view'] ? 'disabled' : '' )));
    $boutons .= $this->Bs->btn(null,array('action' => 'edit', $rowElement['Circuit']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit',array( 'title' =>  __('Modifier', true),'class' => !$rowElement['ListeActions']['edit'] ? 'disabled' : ''   )));
    $boutons .= $this->Bs->btn(null,array('action' => 'visuCircuit', $rowElement['Circuit']['id']), array('type' => 'info', 'icon' => 'glyphicon glyphicon-list-alt',array('title' =>  __('Visionner', true),'class' => !$rowElement['ListeActions']['visuCircuit'] ? 'disabled' : ''  )));
    $boutons .= $this->Bs->btn(null, array('action' => 'delete', $rowElement['Circuit']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash',array('title' => __('Supprimer')),'class' => !$rowElement['ListeActions']['delete'] ? 'disabled' : '' ), __('Êtes vous sur de vouloir supprimer ') . $rowElement['Circuit']['nom'] . ' ?');
    echo $this->Bs->cell($boutons,'text-nowrap');
}
echo $this->Bs->endTable();
echo $this->element('indexPageNavigation');
?>
<div class="actions">
    <?php echo $this->Html2->btnAdd(__('Ajouter un circuit de traitement'), null, array('action' => 'add'));?>
</div>