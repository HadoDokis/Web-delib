<?php echo $this->Html->css('connecteurs'); ?>
<h2>Gestion des connecteurs</h2>
<table class="table table-striped" id="connecteurs">
    <?php
    $numLigne = 1;
    foreach ($connecteurs as $id => $connecteur) {
        if ($id < 1)
            continue;
        echo $this->Html->tag('tr', null);
        echo $this->Html->tag('td', $connecteur, array('style' => 'border-right:0;'));
        echo $this->Html->tag('td', null, array('style' => 'text-align:right; border-left:0;'));
        echo $this->Html->tag('div', null, array('class' => 'btn-group'));
        echo $this->Html->link('<i class="fa fa-edit"></i>', array('action' => 'edit', $id), array(
            'class' => 'btn',
            'title' => "Modifier le connecteur $connecteur",
            'escape' => false
        ));
        echo $this->Html->link('<i class="fa fa-check"></i>', '/check/index.php#' . urlencode(strtolower($connecteur)), array(
            'class' => 'btn btn-info',
            'title' => "Tester le connecteur $connecteur",
            'escape' => false,
            'target' => '_blank'
        ));
        echo $this->Html->tag('/div');
        echo $this->Html->tag('/td');
        echo $this->Html->tag('/tr');
    }
    ?>
</table>