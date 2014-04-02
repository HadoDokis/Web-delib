<h2>Nouveau modèle : </h2>
<?php echo $this->Html->script('fckeditor/fckeditor'); ?>
<?php echo $this->Form->create('Model',array('url'=>'/models/add','type'=>'post')); ?>

<div class="optional">
   <?php echo $this->Form->input('Model.modele', array('label'=>'Libellé', 'size' => '50', 'empty'=>''));?>
</div>

<br/>

<div class="submit">
    <?php $this->Html2->boutonsAddCancel(); ?>
</div>
<?php echo $this->Form->end(); ?>
<style>
    label{
        padding-top:6px;
    }
</style>