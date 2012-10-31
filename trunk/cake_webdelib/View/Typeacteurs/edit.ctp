<?php
	if($html->value('Typeacteur.id')) {
		echo "<h2>Modification d'un type d'acteur</h2>";
		echo $form->create('Typeacteur', array('url' => '/typeacteurs/edit/'.$html->value('Typeacteur.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un type d'acteur</h2>";
		echo $form->create('User', array('url' => '/typeacteurs/add/','type'=>'post'));
	}
?>
<div class="required">
 	<?php echo $form->input('Typeacteur.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>','size' => '60'));?> <br />
</div>
<br/>
<div class="required">
 	<?php echo $form->input('Typeacteur.commentaire', array('label'=>'Commentaire','size' => '100'));?>
</div>
<br/>
<div class="required">
	<?php echo $form->label('Typeacteur.elu','Statut <acronym title="obligatoire">(*)</acronym>');?>
	<?php echo $form->input('Typeacteur.elu',array('fieldset'=>false, 'legend'=>false, 'label'=>false, 'type'=>'radio', 'options'=>$eluNonElu));?>
</div>
<br/>

<br/><br/><br/><br/><br/>
<div class="submit">
	<?php if( $this->action == 'edit' ) echo $form->hidden('Typeacteur.id'); ?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php echo $html->link('Annuler', '/typeacteurs/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php $form->end(); ?>
