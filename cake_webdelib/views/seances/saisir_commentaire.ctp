<h2>Commentaire de s&eacute;ance</h2>

<form action="<?php echo $html->url("/seances/saisirCommentaire/$seance_id"); ?>" method="post">
<div class="required">
	<?php echo $form->input('Seance.commentaire',array('label'=>'', 'type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', $session->read('user.User.lasturl'), array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
