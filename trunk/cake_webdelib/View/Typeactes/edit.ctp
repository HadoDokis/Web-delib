<?php
	if($this->Html->value('Typeacte.id')) {
		echo "<h2>Modification d'un type d'acte</h2>";
		echo $this->Form->create('Typeacte', array('url' => '/typeactes/edit/'.$this->Html->value('Typeacte.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un type d'acte</h2>";
		echo $this->Form->create('Typeacte', array('url' => '/typeactes/add/','type'=>'post'));
	}
?>

<div class="demi">
	<fieldset>
	<legend>Informations générales</legend>
	 	<?php echo $this->Form->input('Typeacte.libelle', array('label'=>'Libell&eacute; <acronym title="obligatoire">*</acronym>','size' => '40', 'type' => 'text'));?>
		<br />
		<br/>
	 	<?php echo $this->Form->input('Typeacte.compteur_id', array('label'=>'Compteur <acronym title="obligatoire">*</acronym>', 'options'=>$compteurs, 'default'=>$this->Html->value('Typeacte.compteur_id'), 'empty'=>(count($compteurs)>1) && (!$this->Html->value('Typeacte.id'))));?>
                <br/>
                <br />
                <?php echo $this->Form->input('Typeacte.nature_id', array('label'=>'Nature <acronym title="obligatoire">*</acronym>', 'options'=>$natures, 'default'=>$selectedNatures,  'empty'=>false));?>

	</fieldset>
</div>

<div class="demi">
	<fieldset>
	<legend>Mod&egrave;les pour les &eacute;ditions</legend>
        <?php echo $this->Form->input('Typeacte.modeleprojet_id', array('label'=>'projet <acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$this->Html->value('Typeacte.modelprojet_id'), 'empty'=>false));?>
        <br/>
        <?php echo $this->Form->input('Typeacte.modelefinal_id', array('label'=>'document final<acronym title="obligatoire">*</acronym>', 'options'=>$models, 'default'=>$this->Html->value('Typeacte.modeldeliberation_id'), 'empty'=>false));?>
	</fieldset>
</div>
<div class="spacer"></div>

<div class="submit">
	<?php 
        echo $this->Form->hidden('Typeacte.id');
        $this->Html2->boutonsSaveCancel(); 
//	echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));
//	echo $this->Html->link('Annuler', '/typeactes/index', array('class'=>'link_annuler', 'name'=>'Annuler'));
        ?>
</div>
<?php $this->Form->end(); ?>
