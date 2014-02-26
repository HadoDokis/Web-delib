<h2>Ajout d'un thème</h2>
<?php echo $this->Form->create('Theme', array('url' => '/themes/add/', 'type' => 'post')); ?>
<?php echo $this->Form->input('Theme.libelle', array('label' => 'Libellé <abbr title="obligatoire">*</abbr>', 'maxlength' => '500')); ?>
<?php echo $this->Form->input('Theme.order', array('label' => 'Critère de tri')); ?>
<?php echo $this->Form->input('Theme.parent_id', array('label' => 'Appartient à', 'options' => $themes, 'empty' => true, 'escape' => false)) ?>
<div class="spacer"></div>
<div class="submit">
    <?php $this->Html2->boutonsAddCancel(); ?>
</div>
<?php echo $this->Form->end(); ?>

<script>
    $(document).ready(function () {
        $("#ThemeParentId").select2({
            width: 'resolve',
            placeholder: 'Aucun',
            allowClear: true,
            formatSelection: function (object) {
                // trim sur la sélection (affichage en arbre)
                return $.trim(object.text);
            }
        });
    });
</script>
<style>
    #ThemeAddForm label {
        padding-top: 5px;
    }
</style>