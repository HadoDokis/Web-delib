<h2>Changement du mot de passe pour  <?php echo $html->value('User.prenom').' '. $html->value('User.nom'); ?></h2>
<?php echo $form->create('User', array('url' => '/users/changeMdp/'.$html->value('User.id'),'type'=>'post')); ?>
<?php
	echo "<div class='tiers'>";
 		echo $form->input('User.password', array('type'=>'password', 'label'=>'Password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo "</div>";
	echo "<div class='tiers'>";
 		echo $form->input('User.password2',array('type'=>'password', 'label'=>'Confirmez le password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo "</div>";
?>

	<div class="spacer"></div>

	<div class="submit">
		<?php echo $form->hidden('User.id');?>
		<?php echo $form->submit('Changer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
		<?php echo $html->link('Annuler', '/users/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>
<?php $form->end(); ?>
