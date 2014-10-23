<h2>Saisie des débats généraux</h2>
<?php
echo $this->Form->create('Seances', array('url' => array('controller' => 'seances', 'action' => 'saisirDebatGlobal', $this->data['Seance']['id']), 'type' => 'file'));
if ($this->data['Seance']['debat_global_size'] > 0) {
    echo '<br>Nom fichier : ' . $this->data['Seance']['debat_global_name'];
    echo '<br>Taille : ' . round($this->data['Seance']['debat_global_size'] / 1000, 2) . 'ko';
    echo '<br>' . $this->Html->link('[Telecharger]', "/seances/download/$seance_id/debat_global");
    echo ' ' . $this->Html->link('[Supprimer]', "/seances/deleteDebatGlobal/$seance_id");
    echo '<br><br>';
}
echo $this->Form->input('Seance.texte_doc', array('label' => 'Nouveau fichier : ', 'type' => 'file'));
echo $this->Form->hidden('Seance.id');
?>

<div class="optional">
    <?php
    if (!empty($annexes)) {
        echo $this->Form->label('Annexe.titre', 'Annexe(s) :');
        foreach ($annexes as $annexe) {
            echo '<br>Titre : ' . $annexe['Annex']['titre'];
            echo '<br>Nom fichier : ' . $annexe['Annex']['filename'];
            echo '<br>Taille : ' . $annexe['Annex']['size'];
            echo '<br>' . $this->Html->link('[Telecharger]', array('controller'=>'annexes','action'=>'download', $annexe['Annex']['id'])) . '&nbsp;'. $this->Html->link('Supprimer', array('controller'=>'annexes', 'action'=>'delete', $annexe['Annex']['id']));
            echo '<div class="spacer"></div>';
        }
    } ?>
</div>
<div class="spacer"></div>
<div class="submit">
    <?php
    echo $this->Html->tag("div", null, array("class" => "btn-group"));
    if($this->data['Seance']['traitee'])
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux seances passées', array('controller' => 'seances', 'action' => 'listerAnciennesSeances'), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
    else
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux seances à traiter', array('controller' => 'seances', 'action' => 'listerFuturesSeances'), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
    echo $this->Form->button('<i class="fa fa-save"></i> Enregistrer', array('class' => 'btn btn-primary', 'name' => 'saisir', 'escape' => false));
    echo $this->Form->end();
    echo $this->Html->tag('/div', null);
    ?>
</div>