<?php echo $javascript->link('compteurs.js'); ?>

<?php
	if($html->value('Compteur.id')) {
		echo "<h2>Modification d'un compteur param&eacute;trable</h2>";
		echo $form->create('Compteur',array('url'=>'/compteurs/edit/'.$html->value('Compteur.id'),'type'=>'post','id'=>'form_compteur'));
	}
	else {
		echo "<h2>Ajout d'un compteur param&eacute;trable</h2>";
		echo $form->create('Compteur',array('url'=>'/compteurs/add/','type'=>'post','id'=>'form_compteur'));
	}
?>

<div class="required">
 	<?php echo $form->input('Compteur.nom', array('label'=>'Nom <acronym title="obligatoire">(*)</acronym>','size' => '60'));?> <br />
</div>
<br/>
<div class="required">
 	<?php echo $form->input('Compteur.commentaire', array('label'=>'Commentaire','size' => '100'));?>
</div>
<br/>
<div class="required">
 	<?php echo $form->input('Compteur.def_compteur', array('label'=>'D&eacute;finition du compteur <acronym title="obligatoire">(*)</acronym>','size' => '40'));?>

<select onchange="InsertSelectedValueInToInput(this, 'form_compteur', 'CompteurDefCompteur');">
    <option value="AIDEFORMAT">sélectionner les formats dans la liste</option>
    <option value="#s#">num&eacute;ro de la s&eacute;quence</option>
    <option value="#S#">num&eacute;ro de la s&eacute;quence sur 1 chiffre</option>
    <option value="#SS#">num&eacute;ro de la s&eacute;quence sur 2 chiffres (compl&eacute;t&eacute; par un soulign&eacute;)</option>
    <option value="#SSS#">num&eacute;ro de la s&eacute;quence sur 3 chiffres (compl&eacute;t&eacute; par des soulign&eacute;s)</option>
    <option value="#SSSS#">num&eacute;ro de la s&eacute;quence sur 4 chiffres (compl&eacute;t&eacute; par des soulign&eacute;s)</option>
    <option value="#00#">num&eacute;ro de la s&eacute;quence sur 2 chiffres (compl&eacute;t&eacute; par un z&eacute;ro)</option>
    <option value="#000#">num&eacute;ro de la s&eacute;quence sur 3 chiffres (compl&eacute;t&eacute; par des z&eacute;ros)</option>
    <option value="#0000#">num&eacute;ro de la s&eacute;quence sur 4 chiffres (compl&eacute;t&eacute; par des z&eacute;ros)</option>
    <option value="#AAAA#">ann&eacute;e sur 4 chiffres</option>
    <option value="#AA#">ann&eacute;e sur 2 chiffres</option>
    <option value="#M#">num&eacute;ro du mois sans z&eacute;ro significatif</option>
    <option value="#MM#">num&eacute;ro du mois avec z&eacute;ro significatif</option>
    <option value="#J#">num&eacute;ro du jour sans z&eacute;ro significatif</option>
    <option value="#JJ#">num&eacute;ro du jour avec z&eacute;ro significatif</option>
</select>
</div>

<br/>
<div class="required">
 	<?php echo $form->input('Compteur.def_reinit', array('label'=>'Crit&egrave;re de r&eacute;initialisation','size' => '40'));?>

<select onchange="InsertSelectedValueInToInput(this, 'form_compteur', 'CompteurDefReinit');">
    <option value="AIDEFORMAT">sélectionner les formats dans la liste</option>
    <option value="#AAAA#">Ann&eacute;e</option>
    <option value="#MM#">Mois</option>
    <option value="#JJ#">Jour</option>
</select>
</div>

<br>
<div class="required">
 	<?php echo $form->input('Compteur.sequence_id', array('label'=>'S&eacute;quence <acronym title="obligatoire">(*)</acronym>', 'options'=>$sequences, 'empty'=>(count($sequences)>1)));?>
</div>

<br>

<br/><br/><br/><br/><br/>
<div class="submit">
	<?php if ($this->action=='edit') echo $form->hidden('Compteur.id')?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php echo $html->link('Annuler', '/compteurs/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $form->end(); ?>
