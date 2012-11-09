<h2>Modification d'un th&egrave;me</h2>
<?php echo $this->Form->create('Theme', array('url' => '/themes/edit/'.$this->Html->value('Service.id'),'type'=>'post')); ?>
<div class="optional">
 	<?php echo $this->Form->input('Theme.libelle', array('label'=>'Libellé','size' => '60', 'maxlength' => '500'));?>
</div>
<div class="optional">
 	<?php echo $this->Form->input('Theme.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<br/>
<div>
	<?php
	     echo $this->Form->input('Theme.parent_id', array('label'=>'Appartient &agrave;', 'options'=>$themes, 'default'=>$selectedTheme, 'empty'=>'', 'escape'=>false));
	?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $this->Form->hidden('Theme.id')?>
	<?php echo $this->Form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/themes/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
