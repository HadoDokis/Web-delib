<?php
	if($this->Html->value('Sequence.id')) {
		echo "<h2>Modification d'une s&eacute;quence</h2>";
		echo $this->Form->create('Sequence', array('url' => '/sequences/edit/'.$this->Html->value('Sequence.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'une s&eacute;quence</h2>";
		echo $this->Form->create('Sequence', array('url' => '/sequences/add/','type'=>'post'));
	}
?>

<div class="required">
 	<?php echo $this->Form->input('Sequence.nom', array('label'=>'Nom <acronym title="obligatoire">(*)</acronym>','size' => '60'));?>
</div>
<br/>
<div class="required">
 	<?php echo $this->Form->input('Sequence.commentaire', array('label'=>'Commentaire','size' => '100'));?>
</div>
<br/>
<div class="required">
 	<?php
 		if (Configure::read('INIT_SEQ'))
 			echo $this->Form->input('Sequence.num_sequence', array('label'=>'Num&eacute;ro de s&eacute;quence','size' => '10', 'value'=>1));
 		else
 			echo $this->Form->input('Sequence.num_sequence', array('label'=>'Num&eacute;ro de s&eacute;quence','size' => '10', 'readonly'=> 'readonly', 'value'=>1));
 	?>
</div>
<br/>

<br/><br/><br/><br/><br/>
<div class="submit">
	<?php if ($this->action=='edit') echo $this->Form->hidden('Sequence.id')?>
	<?php echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php echo $this->Html->link('Annuler', '/sequences/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $this->Form->end(); ?>
