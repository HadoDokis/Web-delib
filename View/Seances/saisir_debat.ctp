<h2>Saisie des débats</h2>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Form->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'saisirDebat', $delib_id, $seance_id), 'type' => 'file')); ?>

<?php
if ($seance['Typeseance']['action']) {
    $size = $this->data['Deliberation']['commission_size'];
    $name = $this->data['Deliberation']['commission_name'];
    $nature = 'commission';
} else {
    $size = $this->data['Deliberation']['debat_size'];
    $name = $this->data['Deliberation']['debat_name'];
    $nature = 'debat';
}
if ($size > 0) {
    echo '<br>Nom fichier : ' . $name;
    echo '<br>Taille : ' . round($size / 1000, 2) . 'ko';
    echo '<br>' . $this->Html->link('[Télécharger]', array('controller' => 'deliberations', 'action' => 'download', $this->data['Deliberation']['id'], $nature));
    echo ' ' . $this->Html->link('[Supprimer]', array('controller' => 'deliberations', 'action' => 'deleteDebat', $this->data['Deliberation']['id'], $seance['Typeseance']['action'], $seance_id));
    echo '<div class="spacer"></div>';
}
echo $this->Form->hidden('Deliberation.id');
echo $this->Form->input('Deliberation.texte_doc', array('label' => 'Nouveau fichier : ', 'type' => 'file'));
?>
<div class="spacer"></div>
<div class="submit">
    <?php
    echo $this->Html->tag("div", null, array("class" => "btn-group", 'style' => 'margin-top:10px;'));
    if (empty($seance['Seance']['traitee'])) {
        if ($seance['Typeseance']['action'] == 0)
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux votes', array('controller' => 'seances', 'action' => 'details', $seance_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
        if ($seance['Typeseance']['action'] == 1)
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux avis', array('controller' => 'seances', 'action' => 'detailsAvis', $seance_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
        if ($seance['Typeseance']['action'] == 2)
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux délibérations', array('controller' => 'seances', 'action' => 'detailsAvis', $seance_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
    } else
        echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux délibérations', array('controller' => 'postseances', 'action' => 'afficherProjets', $seance_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));

    echo $this->Form->button('<i class="fa fa-save"></i> Enregistrer', array('class' => 'btn btn-primary', 'name' => 'saisir', 'escape' => false, 'title' => 'Enregistrer'));
    echo $this->Html->tag('/div', null);
    echo $this->Form->end();
    ?>
</div>
