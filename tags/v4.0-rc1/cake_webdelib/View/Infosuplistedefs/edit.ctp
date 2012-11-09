<?php
	if($this->Html->value('Infosuplistedef.id')) {
		echo "<h2>Modification d'un &eacute;l&eacute;ment de la liste de l'information suppl&eacute;mentaire : ".$infosupdef['Infosupdef']['nom']."</h2>";
		echo $this->Form->create('Infosuplistedef',array('url'=>'/infosuplistedefs/edit','type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un &eacute;l&eacute;ment &agrave; la liste de l'information suppl&eacute;mentaire : ".$infosupdef['Infosupdef']['nom']."</h2>";
		echo $this->Form->create('Infosuplistedef',array('url'=>'/infosuplistedefs/add','type'=>'post'));
	}
?>

	<div class="required">
	 	<?php echo $this->Form->input('Infosuplistedef.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>', 'size' => '40', 'title'=>'Nom de l\'element'));?>
	</div>
	<br/>

	<div class="submit">
		<?php if ($this->action=='edit') echo $this->Form->hidden('Infosuplistedef.id'); ?>
		<?php echo $this->Form->hidden('Infosuplistedef.infosupdef_id')?>
		<?php echo $this->Form->hidden('Infosuplistedef.actif')?>
		<?php echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
		<?php echo $this->Html->link('Annuler', '/infosuplistedefs/index/'.$infosupdef['Infosupdef']['id'], array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>

<?php echo $this->Form->end(); ?>
