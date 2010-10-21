<h2>Modification d'un service</h2>
<?php echo $form->create('Service',array('action'=>'edit','type'=>'post')); ?>
<div class="optional">
 	<?php echo $form->input('Service.libelle', array('label'=>'Libellé','size' => '60'));?>
</div>
<div class="optional">
	<?php
		if ($isEditable){
		    echo $form->input('Service.parent_id', array('label'=>'Appartient à','options'=>$services,'default'=>$selectedService, 'empty'=>'', 'escape'=>false));
		}
	?>
</div>
<div class="optional">
	<?php echo $form->input('Service.circuit_defaut_id', array('options'=>$circuits,'label'=>'Circuit par d&eacute;faut', 'empty'=>'', 'type'=>'select')); ?>
</div>
<div class="optional">
    <?php echo $form->input('Service.order', array('label'=>'Crit&egrave;re de tri', 'size' => '10'));?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $form->hidden('Service.id',array('label'=>'&nbsp;'))?>
	<?php echo $form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/services/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php $form->end(); ?>
