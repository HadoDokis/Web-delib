<h2>Editer le Circuit</h2>
<?php echo $form->create('Circuit',array('url'=>'/circuits/edit/'.$html->value('Circuit.id'),'type'=>'post')); ?>
<div class="required"> 
 	<?php echo $form->input('Circuit.libelle', array('label'=>'Libellé','size' => '60'));?>
</div>
<?php echo $form->hidden('Circuit.id')?>
<br/>
<div class="submit">
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Valider'));?>
	<?php echo $html->link('Annuler', '/circuits/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $form->end(); ?>
