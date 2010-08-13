<h2>Changement du mot de passe pour  <?php echo $html->value('User.prenom').' '. $html->value('User.nom'); ?></h2>
<?php echo $form->create('User', array('url' => '/users/changeUserMdp/'.$html->value('User.id'),'type'=>'post', 'id'=>'password')); ?>
<?php
	echo $form->input('User.oldpassword', array('type'=>'password', 'label'=>'Ancien password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo $form->input('User.password', array('type'=>'password', 'label'=>'Nouveau password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo $form->input('User.password2',array('type'=>'password', 'label'=>'Confirmez le nouveau password <acronym title="obligatoire">*</acronym>', 'value'=>''));
?>

	<div class="spacer"></div>

	<div class="submit">
		<?php echo $form->hidden('User.id');?>
		<?php echo $form->submit('Changer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
		<?php echo $html->link('Annuler', '/', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>
<?php echo $form->end(); ?>
