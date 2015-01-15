<?php
echo $this->Html->css("Cakeflow.design.css");
echo $this->Bs->tag('h3', 'Étapes du circuit : '.$circuit);
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
            $cells = $this->Bs->icon('clock-o').' '. $row['cpt_retard'] .' '. __('jours avant la séance');
        elseif (!isset($row['cpt_retard']))
            $cells = $this->Bs->icon('ban').' '. ' <em>'.__('Pas d\'alerte de retard programmée').'<em>';
        else
            $cells = $this->Bs->icon('clock-o').' '.__('Le jour de la séance');

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
        echo $this->Bs->cell($bouton,'text-nowrap');
    }

    echo $this->Bs->endTable();
}
?>
<div class="actions">
    <?php
    echo $this->Bs->div('btn-group col-md-offset-0', null) .
    $this->Html2->btnCancel(array('controller' => 'circuits', 'action' => 'index')) .
    $this->Html2->btnAdd("Ajouter une étape", null, array('controller' => 'etapes', 'action' => 'add', $circuit_id)) .
    $this->Bs->close();
    ?>
</div>


<?php /*
  return;
  echo $this->Html->css("Cakeflow.design.css");
  $this->pageTitle = sprintf("Étapes du circuit : '%s'", $circuit);
  echo $this->Bs->tag('h3', $this->pageTitle);

  if (!empty($etapes)) {
  $cells = '';
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

  $cells .= '<tr>
  <td>' . "$moveUp $ordre $moveDown" . '</td>
  <td>' . $row['nom'] . '</td>
  <td>' . $row['description'] . '</td>
  <td>' . h($row['libelleType']) . '</td>
  <td>' . implode(', ', $triggers) . '</td>';
  if (!empty($row['cpt_retard']))
  $cells .= '<td><i class="fa fa-clock-o"></i> ' . $row['cpt_retard'] . ' jours avant la séance</td>';
  elseif (!isset($row['cpt_retard']))
  $cells .= '<td><i class="fa fa-ban"></i> <em>Pas d\'alerte de retard programmée<em></td>';
  else
  $cells .= '<td><i class="fa fa-clock-o"></i> Le jour de la séance</td>';

  $cells .= '<td style="text-align:center">';
  $cells .= $this->Myhtml->bouton(array('controller' => 'compositions', 'action' => 'index', $etape['Etape']['id']), __("Composition de l'étape", true), false, '/cakeflow/img/icons/composition.png');
  $cells .= $this->Myhtml->bouton(array('action' => 'view', $etape['Etape']['id']), 'Voir') . '
  ' . $this->Myhtml->bouton(array('action' => 'edit', $etape['Etape']['id']), 'Modifier') . '
  ' . (($etape['ListeActions']['delete']) ? $this->Myhtml->bouton(array('action' => 'delete', $etape['Etape']['id']), 'Supprimer', 'Voulez vous réellement supprimer l&acute;étape ' . $etape['Etape']['nom'] . ' ?') : '') . '
  </td>
  </tr>';
  }

  $headers = $this->Html->tableHeaders(
  array(
  __('Ordre', true),
  __('Nom', true),
  __('Description', true),
  __('Type', true),
  __('Utilisateur(s)', true),
  __('Délais avant retard', true),
  __('Actions', true)
  )
  );

  echo $this->element('indexPageCourante');
  echo $this->Html->tag('table', $this->Html->tag('thead', $headers) . $this->Html->tag('tbody', $cells), array('class' => 'table table-striped')
  );
  echo $this->element('indexPageNavigation');
  }
  ?>
  <div class="actions">
  <?php
  /* echo $this->Html->tag('div', null, array('class' => 'btn-group'));
  echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour à la liste des circuits', array('controller' => 'circuits', 'action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Revenir en arrière'));
  echo $this->Html->link('<i class="fa fa-plus"></i> Ajouter une étape', array('action' => 'add', $this->params['pass'][0]), array('class' => 'btn btn-primary', 'escape' => false, 'title' => 'Ajouter une étape au circuit'));
  echo $this->Html->tag('/div', null);

  echo $this->Html2->btnCancel('', array('controller' => 'circuits', 'action' => 'index')) .
  $this->Html2->btnAdd("Ajouter une étape", array('action' => 'add', $this->params['pass'][0]));
 */ ?>

