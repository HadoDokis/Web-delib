<?php
echo $this->Html->css('connecteurs');
?>
<h2>Gestion des connecteurs</h2>
<table class="table table-hover" id="connecteurs">
    <th>Types</th>
    <th>Actions</th>
    <?php
    $numLigne = 1;
    foreach ($connecteurs as $id => $connecteur) {
        if ($id < 1)
            continue;
        $rowClass = ($numLigne & 1) ? array('style' => 'height: 36px') : array('style' => 'height: 36px', 'class' => 'altrow');
        echo $this->Html->tag('tr', null, $rowClass);
        echo '<td>' . $connecteur . '</td>';
        echo '<td class="actions btn-group">';
        echo $this->Html->link('<i class="fa fa-edit"></i>', array('action' => 'edit', $id), array(
            'class' => 'btn',
            'title' => "Modifier le connecteur $connecteur",
            'escape' => false
        ));
        echo $this->Html->link('<i class="fa fa-check"></i>', '/check/index.php#' . urlencode(strtolower($connecteur)), array(
            'class' => 'btn btn-info',
            'title' => "Tester le connecteur $connecteur",
            'escape' => false
        ));
        echo '</td>';
        echo '</tr>';
    }
    ?>
</table>