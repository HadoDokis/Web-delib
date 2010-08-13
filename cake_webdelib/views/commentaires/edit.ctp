<h2>Modifier commentaire</h2>
<?php echo $form->create('Commentaire',array('url'=>'/commentaires/edit/'.$html->value('Commentaire.id'),'type'=>'post')); ?>
<?php echo $form->hidden('Commentaire.delib_id',array('value'=> "$delib_id")); ?>
<div class="required"> 
		<?php echo $form->input('Commentaire.texte',array('type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $form->hidden('Commentaire.id')?>
	<?php echo $form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/commentaires/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
