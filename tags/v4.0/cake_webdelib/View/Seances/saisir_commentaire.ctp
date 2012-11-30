<h2>Commentaire de s&eacute;ance</h2>

<form action="<?php echo $this->Html->url("/seances/saisirCommentaire/$seance_id"); ?>" method="post">
<div class="required">
	<?php echo $this->Form->input('Seance.commentaire',array('label'=>'', 'type'=>'textarea', 'cols'=>'50','rows'=>'10')); ?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $this->Form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
