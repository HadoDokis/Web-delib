<h2>Modifier le commentaire</h2>
<?php echo $this->Form->create('Commentaire', array('url' => array('action' => 'edit', $this->Html->value('Commentaire.id')), 'type' => 'post')); ?>
<?php echo $this->Form->hidden('Commentaire.delib_id', array('value' => $delib_id)); ?>
<?php echo $this->Form->hidden('Commentaire.id') ?>
<div class="required">
    <?php echo $this->Form->input('Commentaire.texte', array('type' => 'textarea', 'cols' => '50', 'rows' => '10')); ?>
</div>
<div class="submit">
    <?php
    echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $retour, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
    echo $this->Form->button('<i class="fa fa-comment"></i> Valider', array('type' => 'submit', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le commentaire'));
    echo $this->Html->tag('/div', null);
    ?>
</div>
<?php echo $this->Form->end(); ?>
<style>
    label {
        width: auto;
        float: none;
        text-align: left;
    }
</style>