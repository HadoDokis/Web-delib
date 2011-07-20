<h2>Ajout d'un service</h2>
<?php echo $form->create('Service', array('controller'=>'services','action' => 'add','type' => 'post')); ?>
<div class="optional">
 	<?php echo $form->input('Service.libelle', array('label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','size' => '50'));?>
</div>
<div class="optional">
	<?php echo $form->input('Service.parent_id', array('label'=>'Appartient &agrave; : ','options'=>$services, 'empty'=>'', 'type'=>'select', 'escape'=>false)); ?>
</div>
<div class="optional">
	<?php echo $form->input('Service.circuit_defaut_id', array('label'=>'Circuit par d&eacute;faut','options'=>$circuits, 'empty'=>'', 'type'=>'select'));?>
</div>
<div class="optional">
    <?php echo $form->input('Service.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<br/><br/><br/><br/>
<div class="submit">
	<?php echo $form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/services/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php $form->end(); ?>
