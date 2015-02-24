<?php
echo $this->Html->css("Cakeflow.design.css");
echo $this->Bs->tag('h3', 'Étapes du circuit : ' . $circuit);
$this->Html->addCrumb(__('Liste des circuits'), array('controller' => 'circuits', 'action' => 'index'));
$this->Html->addCrumb(__('Étapes du circuit'));
if (!empty($etapes)) {
    echo $this->Bs->table(
            array(
        array('title' => __('Ordre')),
        array('title' => __('Nom')),
        array('title' => __('Description')),
        array('title' => __('Type')),
        array('title' => __('Utilisateur(s)')),
        array('title' => __('Délais avant retard')),
        array('title' => __('Actions'))
            ), array('striped')
    );
    foreach ($etapes as $rownum => $etape) {
        $row = Set::extract($etape, 'Etape');
        // Liens pour changer la position de l'étape
        $ordre = $etape['Etape']['ordre'];
        $moveUp = $ordre > 1 ? $this->Html->link('&#9650;', array('action' => 'moveUp', $row['id']), array('escape' => false), false) : '&#9650;';
        $moveDown = $ordre < $nbrEtapes ? $this->Html->link('&#9660;', array('action' => 'moveDown', $row['id']), array('escape' => false)) : '&#9660;';
        // Mise en forme de la liste des déclencheurs
        $triggers = array();
        foreach ($etape['Composition'] as $composition) {
            $triggers[] = $composition['libelleTrigger'];
        }
        if (!empty($row['cpt_retard']))
            $cells = $this->Bs->icon('clock-o') . ' ' . $row['cpt_retard'] . ' ' . __('jours avant la séance');
        elseif (!isset($row['cpt_retard']))
            $cells = $this->Bs->icon('ban') . ' ' . ' <em>' . __('Pas d\'alerte de retard programmée') . '<em>';
        else
            $cells = $this->Bs->icon('clock-o') . ' ' . __('Le jour de la séance');
        //glyphicon-user
        $bouton = $this->Bs->div('btn-group')
                . $this->Bs->btn(null, array('controller' => 'compositions', 'action' => 'index', $etape['Etape']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-user', 'title' => __("Composition de l'étape", true)))
                . $this->Bs->btn(null, array('action' => 'view', $etape['Etape']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => __('Voir')))
                . $this->Bs->btn(null, array('action' => 'edit', $etape['Etape']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => __('Modifier')))
                . $this->Bs->btn(null, array('action' => 'delete', $etape['Etape']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-trash', 'title' => __('Supprimer')), __('Êtes vous sur de vouloir supprimer ') . $etape['Etape']['nom'] . ' ?');
        echo $this->Bs->cell($moveUp . $ordre . $moveDown);
        echo $this->Bs->cell($row['nom']);
        echo $this->Bs->cell($row['description']);
        echo $this->Bs->cell(h($row['libelleType']));
        echo $this->Bs->cell(implode(', ', $triggers));
        echo $this->Bs->cell($cells);
        echo $this->Bs->cell($bouton, 'text-nowrap');
    }
    echo $this->Bs->endTable();
}
?>
<div class="actions">
    <?php
    debug($previous);
    echo $this->Bs->div('btn-group col-md-offset-0', null) .
    $this->Html2->btnCancel($previous) .
    $this->Html2->btnAdd("Ajouter une étape", null, array('controller' => 'etapes', 'action' => 'add', $circuit_id)) .
    $this->Bs->close();
    ?>
</div>