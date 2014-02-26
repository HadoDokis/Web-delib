<h2>Modification d'un thème</h2>
<?php echo $this->Form->create('Theme', array('url' => array('action' => 'edit', $this->Html->value('Theme.id')), 'type' => 'post')); ?>
<?php echo $this->Form->input('Theme.libelle', array('label' => 'Libellé', 'maxlength' => '500')); ?>
<?php echo $this->Form->input('Theme.order', array('label' => 'Critère de tri')); ?>
<?php echo $this->Form->input('Theme.parent_id', array('label' => 'Appartient à', 'options' => $themes, 'default' => $selectedTheme, 'empty' => true, 'escape' => false)); ?>
<div class="spacer"></div>
<div class="submit">
    <?php
    echo $this->Form->hidden('Theme.id');
    $this->Html2->boutonsSaveCancel();
    ?>
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
    #ThemeEditForm label {
        padding-top: 5px;
    }
</style>