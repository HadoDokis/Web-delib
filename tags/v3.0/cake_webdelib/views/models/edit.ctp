<?php echo $javascript->link('fckeditor/fckeditor'); ?>
<?php echo $form->create('Model',array('url'=>'/models/edit/'.$html->value('Model.id'),'type'=>'post')); ?>
<h2>Modification du mod&egrave;le : <?php echo $libelle; ?></h2>
<div class="optional">
    <?php echo $form->input('Model.content', array('type'=>'textarea','cols' => '10', 'rows' => '20'));?>
    <?php echo $fck->load('data[Model][content]'); ?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $form->hidden('Model.id',array('label'=>'&nbsp;'))?>
	<?php echo $form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/models/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
