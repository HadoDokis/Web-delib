<?php

echo $this->Html->css("Cakeflow.design.css");
if (!empty($etape)) {
    $this->pageTitle = sprintf("Compositions de l'étape '%s' du circuit '%s'", $etape['Etape']['nom'], $etape['Circuit']['nom']);
} else {
    $this->pageTitle = __('Compositions', true);
}
$this->Html->addCrumb(__('Liste des circuits'), array('controller' => 'circuits', 'action' => 'index'));
$this->Html->addCrumb(__('Étapes du circuit'), array('controller' => 'etapes', 'action' => 'index', $etape['Circuit']['id']));
$this->Html->addCrumb(__('Composition de l\'étape'));
echo $this->Html->tag('h3', $this->pageTitle);
if (!empty($compositions)) {
    $this->Paginator->options(array('url' => $this->passedArgs));
    $cells = '';
    echo $this->Bs->table(
            array(
        array('title' => CAKEFLOW_TRIGGER_TITLE),
        array('title' => __('Type de validation', true)),
        array('title' => __('Actions', true))
            ), array('striped')
    );
    foreach ($compositions as $rownum => $composition) {
        $rows = Set::extract($composition, 'Composition');
        $triggerLibelle = $this->Bs->cell($rows['triggerLibelle']);
        if (!Configure::read('USE_PARAPHEUR') && $composition['Composition']['trigger_id'] == -1) {
            $triggerLibelle = $this->Bs->cell("<span style='cursor: help; border-bottom-color: #999; border-bottom-style: dotted; border-bottom-width: 1px;' title='Attention : Cette délégation peut poser problème. \nSolution : Activer le parapheur dans les connecteurs ou modifier/supprimer la composition'>
                <i class='fa fa-warning'></i> " . $rows['triggerLibelle'] . "</span>");
        }
        echo $triggerLibelle;
        echo $this->Bs->cell($rows['typeValidationLibelle']);
        $boutons = $this->Bs->div('btn-group');
        $boutons .= $this->Bs->btn(null, array('action' => 'view', $composition['Composition']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => __('Voir')));
        $boutons .= $this->Bs->btn(null, array('action' => 'edit', $composition['Composition']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => __('Modifier')));
        $boutons .= $this->Bs->btn(null, array('action' => 'delete', $composition['Composition']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => __('Supprimer')), __('Êtes vous sur de vouloir supprimer ') . $rows['triggerLibelle'] . ' ?');
        echo $this->Bs->cell($boutons, 'text-nowrap');
    }
    echo $this->Bs->endTable();
    echo '<div class=\'actions\'>';
    echo $this->Html2->btnCancel($previous);
    if ($canAdd) {
        echo $this->Html2->btnAdd(__('Ajouter une composition'), null, array('action' => 'add', $this->params['pass'][0]));
    }
    echo '</div>';
}
?>