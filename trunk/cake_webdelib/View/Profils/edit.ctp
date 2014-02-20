<?php echo $this->element('onglets', array('listeOnglets' => array(
    'Informations principales',
    'Droits'))); ?>

<h2>Modification d'un profil</h2>

<?php echo $this->Form->create('Profil', array('controller' => 'profils', 'action' => 'edit', 'type' => 'post')); ?>

<div id='tab1'>
    <?php
    echo $this->Form->input('Profil.libelle', array('label' => array('text'=>'Libellé', 'style'=>'padding-top:5px;')));
    if ($selectedProfil == 0) $selectedProfil = '';
    echo $this->Form->input('Profil.parent_id', array('label' => array('text'=>'Appartient à', 'style'=>'padding-top:5px;'), 'type' => 'select', 'options' => $profils, 'default' => $selectedProfil, 'empty' => ''));
    ?>
</div>

<div id='tab2' style="display: none;">
    <?php
    echo $this->element('editDroits');
    ?>
</div>

<div class="spacer"></div>

<div class="submit">
    <?php echo $this->Form->hidden('Profil.id', array('label' => '$nbsp;')) ?>
    <?php $this->Html2->boutonsSaveCancel('', 'index', 'Modifier'); ?>
</div>

<?php echo $this->Form->end(); ?>

<script>
    $(document).ready(function () {
        $('#ProfilParentId').select2({
            width: 'resolve',
            placeholder: 'Aucun',
            allowClear: true
        });
    })
</script>