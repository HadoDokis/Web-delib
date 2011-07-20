<h2> Donner un avis pour le projet : "<?php echo $html->value('Deliberation.objet')?>"</h2>
<?php echo $form->create('Deliberation',array('url'=>'/seances/donnerAvis/'.$html->value('Deliberation.id'),'type'=>'post')); ?>

	<div class="demi">
		<?php echo $form->label('Deliberation.avis', 'Donner un avis <acronym title="obligatoire">*</acronym>'); ?>
		<?php echo $form->input('Deliberation.avis', array('fieldset'=>false, 'legend'=>false, 'label'=>false, 'options'=>$avis, 'type'=>'radio'));?>
		<br/>
		<br/>
	</div>
	<br/>

	<div class="spacer"></div>

	<div class="demi" id="selectSeance">
		<?php echo $form->label('Deliberation.seance_id', 'Attribuer une nouvelle séance');?>
		<?php echo $form->input('Deliberation.seance_id', array('label'=>false, 'div'=>false, 'options'=>$seances, 'empty'=>true, 'selected' => ''));?>
		<br/>
		<br/>
	</div>
	<br/>

	<div class="spacer"></div>

	<br/>
	<br/>
	<div class="submit">
		<?php echo $form->hidden('Deliberation.id')?>
		<?php echo $form->submit('Sauvegarder', array('div'=>false,'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
		<?php echo $html->link('Annuler', '/seances/detailsAvis/'.$html->value('Seance.id'), array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>

<?php echo $form->end(); ?>
