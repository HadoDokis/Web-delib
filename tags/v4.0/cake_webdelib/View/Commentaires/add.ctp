<h2>Nouveau commentaire</h2>

<?php echo $this->Form->create('Commentaire',array('url'=>'/commentaires/add/'.$delib_id,'type'=>'post')); ?>
<?php echo $this->Form->hidden('Commentaire.delib_id',array('value'=> "$delib_id")); ?>
<div class="required">
	<?php echo $this->Form->input('Commentaire.texte',array('type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $this->Form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', $this->Session->read('user.User.lasturl'), array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
