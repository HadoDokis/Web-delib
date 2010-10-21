<h2>Nouveau mod&egrave;le : </h2>
<?php echo $javascript->link('fckeditor/fckeditor'); ?>
<?php echo $form->create('Model',array('url'=>'/models/add','type'=>'post')); ?>

<div class="optional">
   <?php echo $form->input('Model.modele', array('label'=>'Libellé', 'size' => '50', 'empty'=>''));?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $form->submit('Ajouter', array('class'=>'bt_save_border', 'name'=>'Ajouter', 'div'=>false));?>
	<?php echo $html->link('Annuler', '/models/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
