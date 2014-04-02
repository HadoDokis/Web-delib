<h2>Commentaire de s&eacute;ance</h2>

<form action="<?php echo $this->Html->url("/seances/saisirCommentaire/$seance_id"); ?>" method="post">
<div class="required">
	<?php echo $this->Form->input('Seance.commentaire',array('label'=>'', 'type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>

<br/><br/><br/><br/>

<div class="submit btn-group">
     <?php echo $this->Html->link('<i class="icon-arrow-left"></i> Annuler', '/seances/listerFuturesSeances', array('escape'=>false, 'class'=>'btn'))?>
    <?php echo $this->Form->button('<i class="icon-save"></i> Enregistrer', array('class'=>'btn btn-primary', 'name'=>'saisir','escape'=>false, 'title' => 'Enregistrer'));?>
</div>
<?php echo $this->Form->end(); ?>
