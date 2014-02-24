<h2>Modification du service : <?php echo $this->Html->value('Service.libelle'); ?></h2>
<?php echo $this->Form->create('Service', array('action' => 'edit', 'type' => 'post')); ?>
<?php echo $this->Form->input('Service.libelle', array('label' => 'Libellé')); ?>
<?php echo $this->Form->input('Service.parent_id', array('label' => 'Appartient à', 'options' => $services, 'default' => $selectedService, 'empty' => true, 'escape' => false, 'class' => 'autocomplete')); ?>
<div class="spacer"></div>
<?php echo $this->Form->input('Service.circuit_defaut_id', array('options' => $circuits, 'label' => 'Circuit par défaut', 'empty' => true, 'type' => 'select', 'class' => 'autocomplete')); ?>
<div class="spacer"></div>
<?php echo $this->Form->input('Service.order', array('label' => 'Critère de tri', 'type' => 'number')); ?>
<div class="submit">
    <?php
    echo $this->Form->hidden('Service.id', array('label' => false));
    $this->Html2->boutonsSaveCancel();
    ?>
</div>
<?php $this->Form->end(); ?>
<script>
    $(document).ready(function () {
        $(".autocomplete").select2({
            width: 'resolve',
            placeholder: 'Aucun',
            allowClear: true,
            formatSelection: function (object, container) {
                // trim sur la sélection (affichage en arbre)
                return $.trim(object.text);
            }
        });
    });
</script>
<style>
    #ServiceEditForm label {
        padding-top: 5px;
    }
</style>