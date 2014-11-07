<h2>Ajout d'un profil</h2>
<?php echo $this->Form->create('Profil', array('controller' => 'profils', 'action' => 'add', 'type' => 'post')); ?>
<div class="optional">
    <?php echo $this->Form->input('Profil.libelle', array('label' => 'Libellé <abbr title="obligatoire">(*)</abbr>')); ?>
</div>
<div>
    <?php echo $this->Form->input('Profil.parent_id', array('label' => 'Appartient à ', 'options' => $profils, 'empty' => true)); ?>
</div>
<div class="spacer"></div>
<div class="submit">
    <?php $this->Html2->boutonsSaveCancel('', 'index', 'Ajouter'); ?>
</div>
<?php $this->Form->end(); ?>
<script>
    $(document).ready(function(){
        $("#ProfilParentId").select2({
            width: 'resolve',
            placeholder: 'Aucun',
            allowClear: true
        });
    });
</script>