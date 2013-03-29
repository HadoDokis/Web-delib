<?php
if ($this->Html->value('Typeacteur.id')) {
    echo "<h2>Modification d'un type d'acteur</h2>";
    echo $this->Form->create('Typeacteur', array('url' => '/typeacteurs/edit/' . $this->Html->value('Typeacteur.id'), 'type' => 'post'));
} else {
    echo "<h2>Ajout d'un type d'acteur</h2>";
    echo $this->Form->create('User', array('url' => '/typeacteurs/add/', 'type' => 'post'));
}
?>

<div class="required">
    <?php echo $this->Form->input('Typeacteur.nom', array('label' => 'Nom <acronym title="obligatoire">*</acronym>', 'size' => '60')); ?> <br />
</div>
<br/>
<div class="required">
    <?php echo $this->Form->input('Typeacteur.commentaire', array('label' => 'Commentaire', 'size' => '100')); ?>
</div>
<br/>
<div class="required">
    <?php echo $this->Form->label('Typeacteur.elu', 'Statut <acronym title="obligatoire">(*)</acronym>'); ?>
    <?php echo $this->Form->input('Typeacteur.elu', array('fieldset' => false, 'legend' => false, 'label' => false, 'type' => 'radio', 'options' => $eluNonElu)); ?>
</div>
<br/>

<br/>
<div class="submit">
    <?php if ($this->action == 'edit') echo $this->Form->hidden('Typeacteur.id'); ?>
    <?php $this->Html2->boutonsSaveCancel(); ?>
    <?php // echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
    <?php // echo $this->Html->link('Annuler', '/typeacteurs/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php $this->Form->end(); ?>
