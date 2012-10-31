<?php
	if($html->value('Infosuplistedef.id')) {
		echo "<h2>Modification d'un &eacute;l&eacute;ment de la liste de l'information suppl&eacute;mentaire : ".$infosupdef['Infosupdef']['nom']."</h2>";
		echo $form->create('Infosuplistedef',array('url'=>'/infosuplistedefs/edit','type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un &eacute;l&eacute;ment &agrave; la liste de l'information suppl&eacute;mentaire : ".$infosupdef['Infosupdef']['nom']."</h2>";
		echo $form->create('Infosuplistedef',array('url'=>'/infosuplistedefs/add','type'=>'post'));
	}
?>

	<div class="required">
	 	<?php echo $form->input('Infosuplistedef.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>', 'size' => '40', 'title'=>'Nom de l\'element'));?>
	</div>
	<br/>

	<div class="submit">
		<?php if ($this->action=='edit') echo $form->hidden('Infosuplistedef.id'); ?>
		<?php echo $form->hidden('Infosuplistedef.infosupdef_id')?>
		<?php echo $form->hidden('Infosuplistedef.actif')?>
		<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
		<?php echo $html->link('Annuler', '/infosuplistedefs/index/'.$infosupdef['Infosupdef']['id'], array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>

<?php echo $form->end(); ?>
