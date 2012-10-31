<h2>Modifier commentaire</h2>
<?php echo $this->Form->create('Commentaire',array('url'=>'/commentaires/edit/'.$this->Html->value('Commentaire.id'),'type'=>'post')); ?>
<?php echo $this->Form->hidden('Commentaire.delib_id',array('value'=> "$delib_id")); ?>
<div class="required"> 
		<?php echo $this->Form->input('Commentaire.texte',array('type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $this->Form->hidden('Commentaire.id')?>
	<?php echo $this->Form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/commentaires/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
