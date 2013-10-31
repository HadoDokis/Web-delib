<h2>Ajout d'un profil</h2>
<?php echo $this->Form->create('Profil', array('controller' => 'profils', 'action' => 'add', 'type' => 'post')); ?>
<div class="optional">
    <?php echo $this->Form->input('Profil.libelle', array('label' => 'Libellé <acronym title="obligatoire">(*)</acronym>', 'size' => '50')); ?>
</div>
<br/>
<div>
    <?php echo $this->Form->input('Profil.parent_id', array('label' => 'Appartient à ', 'options' => $profils, 'empty' => true)); ?>
</div>
<br/>
<div class="submit">
    <?php $this->Html2->boutonsSaveCancel('', 'index', 'Ajouter'); ?>
</div>
<?php $this->Form->end(); ?>
