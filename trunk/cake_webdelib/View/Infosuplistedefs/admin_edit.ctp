<?php


	if($this->Html->value('Infosuplistedef.id')) {
            echo $this->Bs->tag('h3', 'Modification d\'un &eacute;l&eacute;ment de la liste de l\'information suppl&eacute;mentaire : '.$infosupdef['Infosupdef']['nom']);
		echo $this->BsForm->create('Infosuplistedef',array('url'=>'/infosuplistedefs/edit','type'=>'post'));
	}
	else {
            echo $this->Bs->tag('h3', 'Ajout d\'un &eacute;l&eacute;ment &agrave; la liste de l\'information suppl&eacute;mentaire : '.$infosupdef['Infosupdef']['nom']);
		echo $this->BsForm->create('Infosuplistedef',array('url'=>'/infosuplistedefs/add/'.$infosupdef['Infosupdef']['id'],'type'=>'post'));
	}
?>

	<div class="required">
	 	<?php echo $this->BsForm->input('Infosuplistedef.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>', 'size' => '40', 'title'=>'Nom de l\'element'));?>
	</div>
<?php
	echo $this->BsForm->checkbox('Infosuplistedef.actif', array('label'=>'élément actif'));
	echo $this->Html->tag('div', '', array('class'=>'spacer'));
?>

	<div class="submit">
            <?php 
                echo $this->BsForm->hidden('Infosuplistedef.id');
                echo $this->BsForm->hidden('Infosuplistedef.infosupdef_id');
                echo $this->Html2->btnSaveCancel(null, array('controller'=>'infosuplistedefs',
                                                    'action'=>'index', $infosupdef['Infosupdef']['id']),
                        array('controller'=>'infosuplistedefs',
                                                    'action'=>'index', $infosupdef['Infosupdef']['id'])
                        ); 
            ?>
	</div>

<?php echo $this->BsForm->end(); ?>
