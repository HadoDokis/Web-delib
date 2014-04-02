<?php echo $this->Html->script('compteurs.js'); ?>

<?php
	if($this->Html->value('Compteur.id')) {
		echo "<h2>Modification d'un compteur paramétrable</h2>";
		echo $this->Form->create('Compteur',array('url'=>'/compteurs/edit/'.$this->Html->value('Compteur.id'),'type'=>'post','id'=>'form_compteur'));
	}
	else {
		echo "<h2>Ajout d'un compteur paramétrable</h2>";
		echo $this->Form->create('Compteur',array('url'=>'/compteurs/add/','type'=>'post','id'=>'form_compteur'));
	}
?>

<div class="required">
 	<?php echo $this->Form->input('Compteur.nom', array('label'=>'Nom <acronym title="obligatoire">(*)</acronym>','size' => '60'));?> <br />
</div>
<br/>
<div class="required">
 	<?php echo $this->Form->input('Compteur.commentaire', array('label'=>'Commentaire','size' => '100'));?>
</div>
<br/>
<div class="required">
 	<?php echo $this->Form->input('Compteur.def_compteur', array('label'=>'Définition du compteur <acronym title="obligatoire">(*)</acronym>','size' => '40'));?>

<select onchange="InsertSelectedValueInToInput(this, 'form_compteur', 'CompteurDefCompteur');">
    <option value="AIDEFORMAT">sélectionner les formats dans la liste</option>
    <option value="#s#">numéro de la séquence</option>
    <option value="#S#">numéro de la séquence sur 1 chiffre</option>
    <option value="#SS#">numéro de la séquence sur 2 chiffres (complété par un souligné)</option>
    <option value="#SSS#">numéro de la séquence sur 3 chiffres (complété par des soulignés)</option>
    <option value="#SSSS#">numéro de la séquence sur 4 chiffres (complété par des soulignés)</option>
    <option value="#00#">numéro de la séquence sur 2 chiffres (complété par un zéro)</option>
    <option value="#000#">numéro de la séquence sur 3 chiffres (complété par des zéros)</option>
    <option value="#0000#">numéro de la séquence sur 4 chiffres (complété par des zéros)</option>
    <option value="#AAAA#">année sur 4 chiffres</option>
    <option value="#AA#">année sur 2 chiffres</option>
    <option value="#M#">numéro du mois sans zéro significatif</option>
    <option value="#MM#">numéro du mois avec zéro significatif</option>
    <option value="#J#">numéro du jour sans zéro significatif</option>
    <option value="#JJ#">numéro du jour avec zéro significatif</option>
    <option value="#p#">numéro de la position</option>
</select>
</div>

<br/>
<div class="required">
 	<?php echo $this->Form->input('Compteur.def_reinit', array('label'=>'Crit&egrave;re de réinitialisation','size' => '40'));?>

<select onchange="InsertSelectedValueInToInput(this, 'form_compteur', 'CompteurDefReinit');">
    <option value="AIDEFORMAT">sélectionner les formats dans la liste</option>
    <option value="#AAAA#">Année</option>
    <option value="#MM#">Mois</option>
    <option value="#JJ#">Jour</option>
</select>
</div>

<br>
<div class="required">
 	<?php echo $this->Form->input('Compteur.sequence_id', array('label'=>'Séquence <acronym title="obligatoire">(*)</acronym>', 'options'=>$sequences, 'empty'=>(count($sequences)>1)));?>
</div>

<br>

<br/><br/><br/><br/><br/>
<div class="submit">
    <?php if ($this->action=='edit') echo $this->Form->hidden('Compteur.id')?>
    <?php $this->Html2->boutonsSaveCancel(); ?>
	<?php // echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php // echo $this->Html->link('Annuler', '/compteurs/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $this->Form->end(); ?>