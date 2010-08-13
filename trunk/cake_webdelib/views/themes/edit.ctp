<h2>Modification d'un th&egrave;me</h2>
<?php echo $form->create('Theme', array('url' => '/themes/edit/'.$html->value('Service.id'),'type'=>'post')); ?>
<div class="optional">
 	<?php echo $form->input('Theme.libelle', array('label'=>'Libellé','size' => '60'));?>
</div>
<div class="optional">
 	<?php echo $form->input('Theme.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<br/>
<div>
	<?php
		if ($isEditable){
	 		echo $form->input('Theme.parent_id', array('label'=>'Appartient &agrave;', 'options'=>$themes, 'default'=>$selectedTheme, 'empty'=>'', 'escape'=>false));
		}
	?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $form->hidden('Theme.id')?>
	<?php echo $form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/themes/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
