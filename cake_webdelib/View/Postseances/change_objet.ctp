<h2>Changement de l'objet pour la délibération  </h2>
<?php echo $this->Form->create('Postseances',array('url'=>'/postseances/changeObjet/','type'=>'post')); ?>
<div class="required">);
 	<?php echo $this->Form->input('Deliberation.objet',array('label'=>'Nouvel Objet'));?>
</div>
<div class="submit">
	<?php echo $this->Form->submit('Changer', array('div'=>false,'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/deliberations/transmit', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
