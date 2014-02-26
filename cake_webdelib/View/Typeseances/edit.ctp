<?php
	if($this->Html->value('Typeseance.id')) {
		echo "<h2>Modification d'un type de séance</h2>";
		echo $this->Form->create('Typeseance', array('url' => '/typeseances/edit/'.$this->Html->value('Typeseance.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un type de séance</h2>";
		echo $this->Form->create('Typeseance', array('url' => array('action'=>'add'), 'type'=>'post'));
	}
?>

<div class="demi">
	<fieldset>
	<legend>Informations générales</legend>
	 	<?php echo $this->Form->input('Typeseance.libelle', array('label'=>'Libellé <abbr title="obligatoire">*</abbr>','size' => '40'));?>
		<br />
		<br/>
	 	<?php echo $this->Form->input('Typeseance.retard', array('label'=>'Nombre de jours avant retard'));?>
		<br/>
		<br/>
	 	<?php echo $this->Form->input('Typeseance.action', array('label'=>'Action en séance<abbr title="obligatoire">*</abbr>', 'options'=>$actions, 'default'=>$this->Html->value('Typeseance.action'), 'empty'=>!$this->Html->value('Typeseance.id'))); ?>
		<br/>
		<br/>
	 	<?php echo $this->Form->input('Typeseance.compteur_id', array('label'=>'Compteur <abbr title="obligatoire">*</abbr>', 'options'=>$compteurs, 'default'=>$this->Html->value('Typeseance.compteur_id'), 'empty'=>(count($compteurs)>1) && (!$this->Html->value('Typeseance.id'))));?>
        <br/>
        <br />
        <?php echo $this->Form->input('Typeacte', array('label'=>'Type d\'acte <abbr title="obligatoire">*</abbr>', 'options'=>$natures, 'default'=>$selectedNatures, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty'=>false));?>
	</fieldset>
</div>

<div class="demi">
	<fieldset>
	<legend>Modèles pour les éditions</legend>
        <?php echo $this->Form->input('Typeseance.modelprojet_id', array('label'=>'Projet <abbr title="obligatoire">*</abbr>', 'options'=>$models_projet, 'default'=>$this->Html->value('Typeseance.modelprojet_id'), 'empty'=>!$this->Html->value('Typeseance.id')));?>
        <br/>
        <?php echo $this->Form->input('Typeseance.modeldeliberation_id', array('label'=>'Document final <abbr title="obligatoire">*</abbr>', 'options'=>$models_delib, 'default'=>$this->Html->value('Typeseance.modeldeliberation_id'), 'empty'=>!$this->Html->value('Typeseance.id')));?>
        <br/>
	 	<?php echo $this->Form->input('Typeseance.modelconvocation_id', array('label'=>'Convocation <abbr title="obligatoire">*</abbr>', 'options'=>$models_convoc, 'default'=>$this->Html->value('Typeseance.modelconvocation_id'), 'empty'=>!$this->Html->value('Typeseance.id')));?>
		<br/>
	 	<?php echo $this->Form->input('Typeseance.modelordredujour_id', array('label'=>'Ordre du jour <abbr title="obligatoire">*</abbr>', 'options'=>$models_odj, 'default'=>$this->Html->value('Typeseance.modelordredujour_id'), 'empty'=>!$this->Html->value('Typeseance.id')));?>
		<br/>
	 	<?php echo $this->Form->input('Typeseance.modelpvsommaire_id', array('label'=>'PV sommaire <abbr title="obligatoire">*</abbr>', 'options'=>$models_pvsommaire, 'default'=>$this->Html->value('Typeseance.modelpvsommaire_id'), 'empty'=>!$this->Html->value('Typeseance.id')));?>
		<br/>
	 	<?php echo $this->Form->input('Typeseance.modelpvdetaille_id', array('label'=>'PV détaillé <abbr title="obligatoire">*</abbr>', 'options'=>$models_pvdetaille, $this->Html->value('Typeseance.modelpvdetaille_id'), 'empty'=>!$this->Html->value('Typeseance.id')));?>
	</fieldset>
</div>
<div class="spacer"></div>

<fieldset>
<legend>Convocations (union des deux sélections ci-dessous)</legend>
	<div class="demi">
	 	<?php echo $this->Form->input('Typeacteur', array('label'=>'Par type d\'acteur', 'options'=>$typeacteurs, 'default'=>$selectedTypeacteurs, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty'=>true));?>
	</div>
	<div class="demi">
	 	<?php echo $this->Form->input('Acteur', array('label'=>'Par acteur', 'options'=>$acteurs, 'default'=>$selectedActeurs, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'size' => 8, 'empty'=>true));?>
	</div>
</fieldset>

<br/>
<div class="submit">
    <?php echo $this->Form->hidden('Typeseance.id')?>
    <?php $this->Html2->boutonsSaveCancel('','/typeseances/index'); ?>
    <?php // echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
    <?php // echo $this->Html->link('Annuler', '/typeseances/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php $this->Form->end(); ?>
