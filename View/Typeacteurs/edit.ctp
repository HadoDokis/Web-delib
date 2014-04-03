<?php
if ($this->Html->value('Typeacteur.id')) {
    echo "<h2>Modification du type d'acteur : {$this->Html->value('Typeacteur.nom')}</h2>";
    echo $this->Form->create('Typeacteur', array('url' => array('action' => 'edit', $this->Html->value('Typeacteur.id')), 'type' => 'post'));
} else {
    echo "<h2>Ajouter un type d'acteur</h2>";
    echo $this->Form->create('Typeacteur', array('url' => array('action' => 'add'), 'type' => 'post'));
}
?>

<div class="required">
    <?php echo $this->Form->input('Typeacteur.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>')); ?>
</div>
<div class="required">
    <?php echo $this->Form->input('Typeacteur.commentaire', array('label' => 'Commentaire')); ?>
</div>
<div class="spacer"></div>
<div class="required inline">
    <?php echo $this->Form->label('Typeacteur.elu', 'Statut <abbr title="obligatoire">*</abbr>'); ?>
    <?php echo $this->Form->input('Typeacteur.elu', array(
        'label' => false,
        'fieldset' => false,
        'legend' => false,
        'div' => false,
        'type' => 'radio',
        'class' => 'btn',
        'style' => 'margin-left:5px; margin-right:5px;',
        'options' => $eluNonElu)); ?>
</div>
<br/>
<div class="submit">
    <?php if ($this->action == 'edit') echo $this->Form->hidden('Typeacteur.id'); ?>
    <?php $this->Html2->boutonsSaveCancel('', array('action' => 'index')); ?>
</div>
<?php $this->Form->end(); ?>
