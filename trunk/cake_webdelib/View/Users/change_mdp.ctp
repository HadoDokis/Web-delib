<h2>Changement du mot de passe pour  <?php echo $this->Html->value('User.prenom').' '. $this->Html->value('User.nom'); ?></h2>
<?php echo $this->Form->create('User', array('url' => '/users/changeMdp/'.$this->Html->value('User.id'),'type'=>'post')); ?>
<?php
	echo "<div class='tiers'>";
 		echo $this->Form->input('User.password', array('type'=>'password', 'label'=>'Password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo "</div>";
	echo "<div class='tiers'>";
 		echo $this->Form->input('User.password2',array('type'=>'password', 'label'=>'Confirmez le password <acronym title="obligatoire">*</acronym>', 'value'=>''));
	echo "</div>";
?>

	<div class="spacer"></div>

	<div class="submit">
		<?php echo $this->Form->hidden('User.id');?>
		<?php echo $this->Form->submit('Changer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
		<?php echo $this->Html->link('Annuler', '/users/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>
<?php $this->Form->end(); ?>
