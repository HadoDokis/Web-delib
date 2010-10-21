<h2>Nouveau commentaire</h2>

<?php echo $form->create('Commentaire',array('url'=>'/commentaires/add/'.$delib_id,'type'=>'post')); ?>
<?php echo $form->hidden('Commentaire.delib_id',array('value'=> "$delib_id")); ?>
<div class="required">
	<?php echo $form->input('Commentaire.texte',array('type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', $session->read('user.User.lasturl'), array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
