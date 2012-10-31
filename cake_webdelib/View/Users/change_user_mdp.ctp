<h2>Changement du mot de passe pour  <?php echo $this->Html->value('User.prenom').' '. $this->Html->value('User.nom'); ?></h2>
<?php echo $this->Form->create('User', array('url' => '/users/changeUserMdp/'.$this->Html->value('User.id'),'type'=>'post', 'id'=>'password')); ?>
<?php
	echo $this->Form->input('User.oldpassword', array('type'=>'password', 'label'=>'Ancien password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo $this->Form->input('User.password', array('type'=>'password', 'label'=>'Nouveau password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo $this->Form->input('User.password2',array('type'=>'password', 'label'=>'Confirmez le nouveau password <acronym title="obligatoire">*</acronym>', 'value'=>''));
?>

	<div class="spacer"></div>

	<div class="submit">
		<?php echo $this->Form->hidden('User.id');?>
		<?php echo $this->Form->submit('Changer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
		<?php echo $this->Html->link('Annuler', '/', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>
<?php echo $this->Form->end(); ?>
