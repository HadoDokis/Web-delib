<?php
	if($html->value('Typeseance.id')) {
		echo "<h2>Modification d'un type de s&eacute;ance</h2>";
		echo $form->create('Typeseance', array('url' => '/typeseances/edit/'.$html->value('Typeseance.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un type de s&eacute;ance</h2>";
		echo $form->create('Typeseance', array('url' => '/typeseances/add/','type'=>'post'));
	}
?>

<div class="demi">
	<fieldset>
	<legend>Informations générales</legend>
	 	<?php echo $form->input('Typeseance.libelle', array('label'=>'Libell&eacute; <acronym title="obligatoire">*</acronym>','size' => '40'));?>
		<br />
		<br/>
	 	<?php echo $form->input('Typeseance.retard', array('label'=>'Nombre de jours avant retard'));?>
		<br/>
		<br/>
	 	<?php echo $form->input('Typeseance.action', array('label'=>'Action en séance<acronym title="obligatoire">*</acronym>', 'options'=>$actions, 'default'=>$html->value('Typeseance.action'), 'empty'=>!$html->value('Typeseance.id'))); ?>
		<br/>
		<br/>
	 	<?php echo $form->input('Typeseance.compteur_id', array('label'=>'Compteur <acronym title="obligatoire">*</acronym>', 'options'=>$compteurs, 'default'=>$html->value('Typeseance.compteur_id'), 'empty'=>(count($compteurs)>1) && (!$html->value('Typeseance.id'))));?>
	</fieldset>
</div>

<div class="demi">
	<fieldset>
	<legend>Mod&egrave;les pour les &eacute;ditions</legend>
        <?php echo $form->input('Typeseance.modelprojet_id', array('label'=>'projet <acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$html->value('Typeseance.modelprojet_id'), 'empty'=>!$html->value('Typeseance.id')));?>
        <br/>
        <?php echo $form->input('Typeseance.modeldeliberation_id', array('label'=>'d&eacute;lib&eacute;ration <acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$html->value('Typeseance.modeldeliberation_id'), 'empty'=>!$html->value('Typeseance.id')));?>
        <br/>
	 	<?php echo $form->input('Typeseance.modelconvocation_id', array('label'=>'convocation <acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$html->value('Typeseance.modelconvocation_id'), 'empty'=>!$html->value('Typeseance.id')));?>
		<br/>
	 	<?php echo $form->input('Typeseance.modelordredujour_id', array('label'=>'ordre du jour <acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$html->value('Typeseance.modelordredujour_id'), 'empty'=>!$html->value('Typeseance.id')));?>
		<br/>
	 	<?php echo $form->input('Typeseance.modelpvsommaire_id', array('label'=>'PV sommaire <acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$html->value('Typeseance.modelpvsommaire_id'), 'empty'=>!$html->value('Typeseance.id')));?>
		<br/>
	 	<?php echo $form->input('Typeseance.modelpvdetaille_id', array('label'=>'PV d&eacute;taill&eacute; <acronym title="obligatoire">*</acronym>', 'options'=>$models, $html->value('Typeseance.modelpvdetaille_id'), 'empty'=>!$html->value('Typeseance.id')));?>
	</fieldset>
</div>
<div class="spacer"></div>

<fieldset>
<legend>Convocations (union des deux s&eacute;lections ci-dessous)</legend>
	<div class="demi">
	 	<?php echo $form->input('Typeacteur', array('label'=>'Par type d\'acteur', 'options'=>$typeacteurs, 'default'=>$selectedTypeacteurs, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty'=>true));?>
	</div>
	<div class="demi">
	 	<?php echo $form->input('Acteur', array('label'=>'Par acteur', 'options'=>$acteurs, 'default'=>$selectedActeurs, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'size' => 8, 'empty'=>true));?>
	</div>
</fieldset>

<br/>
<div class="submit">
	<?php echo $form->hidden('Typeseance.id')?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php echo $html->link('Annuler', '/typeseances/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php $form->end(); ?>
