<?php
	if($this->Html->value('Infosuplistedef.id')) {
		echo "<h2>Modification d'un &eacute;l&eacute;ment de la liste de l'information suppl&eacute;mentaire : ".$infosupdef['Infosupdef']['nom']."</h2>";
		echo $this->Form->create('Infosuplistedef',array('url'=>'/infosuplistedefs/edit','type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un &eacute;l&eacute;ment &agrave; la liste de l'information suppl&eacute;mentaire : ".$infosupdef['Infosupdef']['nom']."</h2>";
		echo $this->Form->create('Infosuplistedef',array('url'=>'/infosuplistedefs/add/'.$infosupdef['Infosupdef']['id'],'type'=>'post'));
	}
?>

	<div class="required">
	 	<?php echo $this->Form->input('Infosuplistedef.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>', 'size' => '40', 'title'=>'Nom de l\'element'));?>
	</div>
	<br/>
<?php
	echo $this->Form->label('Infosuplistedef.actif', $this->Form->input('Infosuplistedef.actif',array('type'=>'checkbox', 'label'=>false, 'div'=>false)).' élément actif', array('class'=>'span2'));
	echo $this->Html->tag('div', '', array('class'=>'spacer'));
?>

	<div class="submit">
            <?php 
                echo $this->Form->hidden('Infosuplistedef.id');
                echo $this->Form->hidden('Infosuplistedef.infosupdef_id');
                $this->Html2->boutonsSaveCancelUrl('/infosuplistedefs/index/'.$infosupdef['Infosupdef']['id']); 
            ?>
	</div>

<?php echo $this->Form->end(); ?>
