<h2>Nouveau commentaire</h2>

<?php echo $this->Form->create('Commentaire',array('url'=>'/commentaires/add/'.$delib_id,'type'=>'post')); ?>
<?php echo $this->Form->hidden('Commentaire.delib_id',array('value'=> "$delib_id")); ?>
<div class="required">
	<?php echo $this->Form->input('Commentaire.texte',array('type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>

<div class="submit">
    <?php $this->Html2->boutonsAddCancel("",'/deliberations/traiter/' . $delib_id); ?>
</div>
<?php echo $this->Form->end(); ?>
