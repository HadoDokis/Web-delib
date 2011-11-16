<h2>Nouveau Circuit</h2>
<?php echo $form->create('Circuit',array('url'=>'/circuits/add','type'=>'post')); ?>
<div class="required"> 
 	<?php echo $form->input('Circuit.libelle', array('label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','size' => '60'));?>
</div>

<br/><br/><br/><br/>

<div class="submit">
	<?php echo $form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/circuits/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php $form->end(); ?>
