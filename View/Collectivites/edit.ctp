<h2>Modification de la collectivit&eacute;</h2>
<?php echo $this->Form->create('Collectivite', array('url' => array('action' => 'edit', $this->Html->value('Collectivite.id')), 'type' => 'file')); ?>
<div class="required">
    <?php
    if (isset($entities)){
        echo $this->Form->input('Collectivite.id_entity', array('options' => $entities, 'selected' => $selected, 'label' => 'Nom'));
        echo '<div class="spacer"></div>';
    }
    else
        echo $this->Form->input('Collectivite.nom', array('label' => 'Nom'));?>
</div>
<div class="optional">
    <?php
    echo $this->Form->input('Collectivite.adresse', array('label' => 'Adresse', 'size' => '30'));
    echo $this->Form->input('Collectivite.CP', array('label' => 'Code Postal'));
    echo $this->Form->input('Collectivite.ville', array('label' => 'Ville'));
    echo $this->Form->input('Collectivite.telephone', array('label' => 'Num téléphone'));
    echo $this->Form->hidden('Collectivite.id');
    ?>
</div>
<div class="submit">
    <?php
    echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $previous, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
    echo $this->Form->button('<i class="fa fa-save"></i> Enregistrer', array('type' => 'submit', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer les modifications'));
    echo $this->Html->tag('/div', null);
    ?>
</div>
<?php echo $this->Form->end(); ?>
<style>
    label {
        line-height: 25px;
    }
</style>
<script>
    $('#CollectiviteIdEntity').select2({
        width: 'resolve'
    })
</script>
