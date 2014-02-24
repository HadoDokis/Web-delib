<h2>Ajout d'un service</h2>
<?php echo $this->Form->create('Service', array('controller' => 'services', 'action' => 'add', 'type' => 'post')); ?>
<?php echo $this->Form->input('Service.libelle', array('label' => 'Libellé <abbr title="obligatoire">(*)</abbr>', 'size' => '50')); ?>
<?php echo $this->Form->input('Service.parent_id', array('label' => 'Appartient à', 'options' => $services, 'empty' => true, 'type' => 'select', 'escape' => false, 'class' => 'autocomplete')); ?>
<div class="spacer"></div>
<?php echo $this->Form->input('Service.circuit_defaut_id', array('label' => 'Circuit par défaut', 'options' => $circuits, 'empty' => true, 'type' => 'select', 'class' => 'autocomplete')); ?>
<div class="spacer"></div>
<?php echo $this->Form->input('Service.order', array('label' => 'Critère de tri', 'type' => 'number')); ?>
<div class="submit">
    <?php $this->Html2->boutonsAddCancel(); ?>
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
    #ServiceAddForm label{
        padding-top: 5px;
    }
</style>