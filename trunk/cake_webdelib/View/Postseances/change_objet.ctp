<h2>Changement de l'objet pour la délibération  </h2>
<?php echo $form->create('Postseances',array('url'=>'/postseances/changeObjet/','type'=>'post')); ?>
<div class="required">);
 	<?php echo $form->input('Deliberation.objet',array('label'=>'Nouvel Objet'));?>
</div>
<div class="submit">
	<?php echo $form->submit('Changer', array('div'=>false,'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/deliberations/transmit', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
