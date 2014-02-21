<h2>Modification de la collectivit&eacute;</h2>
<?php echo $this->Form->create('Collectivite', array('url' => array('action' => 'edit', $this->Html->value('Collectivite.id')), 'type' => 'file')); ?>
<div class="required">
    <?php
    if (isset($entities))
        echo $this->Form->input('Collectivite.id_entity', array('options' => $entities, 'selected' => $selected, 'label' => 'Nom'));
    else
        echo $this->Form->input('Collectivite.nom', array('label' => 'Nom'));?>
</div>
<div class="spacer"></div>
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
    <?php $this->Html2->boutonsSaveCancel(); ?>
</div>
<?php echo $this->Form->end(); ?>
<style>
    label {
        padding-top: 5px;
    }
</style>
<script>
    $('#CollectiviteIdEntity').select2({
        width: 'resolve'
    })
</script>
