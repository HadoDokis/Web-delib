<h2>Nouveau mod&egrave;le : </h2>
<?php echo $this->Html->script('fckeditor/fckeditor'); ?>
<?php echo $this->Form->create('Model',array('url'=>'/models/add','type'=>'post')); ?>

<div class="optional">
   <?php echo $this->Form->input('Model.modele', array('label'=>'Libellé', 'size' => '50', 'empty'=>''));?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $this->Form->submit('Ajouter', array('class'=>'bt_save_border', 'name'=>'Ajouter', 'div'=>false));?>
	<?php echo $this->Html->link('Annuler', '/models/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
