<h2>Ajout d'un service</h2>
<?php echo $this->Form->create('Service', array('controller'=>'services','action' => 'add','type' => 'post')); ?>
<div class="optional">
 	<?php echo $this->Form->input('Service.libelle', array('label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','size' => '50'));?>
</div>
<div class="optional">
	<?php echo $this->Form->input('Service.parent_id', array('label'=>'Appartient &agrave; : ','options'=>$services, 'empty'=>'', 'type'=>'select', 'escape'=>false)); ?>
</div>
<div class="optional">
	<?php echo $this->Form->input('Service.circuit_defaut_id', array('label'=>'Circuit par d&eacute;faut','options'=>$circuits, 'empty'=>'', 'type'=>'select'));?>
</div>
<div class="optional">
    <?php echo $this->Form->input('Service.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<br/><br/><br/><br/>
<div class="submit">
    <?php $this->Html2->boutonsAddCancel(); ?>
</div>
<?php $this->Form->end(); ?>
