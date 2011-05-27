<script>
window.onload=initAffichageInfosElus;

function afficheInfosElus(typeActeur)
{
	divElement = document.getElementById("infoElus");

	if((typeActeur.value.length==0) || (typeActeur.value==null)) {
		divElement.style.display = 'none';
	} else {
		if (typeActeur.options[typeActeur.selectedIndex].className == '1')
			divElement.style.display = '';
		else
			divElement.style.display = 'none';
	}
}

function initAffichageInfosElus()
{
	selectTypeActeur = document.getElementById("ActeurTypeacteurId");
	afficheInfosElus(selectTypeActeur);
}
</script>

<?php
	if($html->value('Acteur.id')) {
		echo "<h2>Modification d'un acteur</h2>";
		echo $form->create('Acteur',array('url'=>'/acteurs/edit/'.$html->value('Acteur.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un acteur</h2>";
		echo $form->create('Acteur',array('url'=>'/acteurs/add','type'=>'post'));
	}
?>
	<div class="demi">
		<fieldset>
		<legend>Identit&eacute;</legend>
		 	<?php echo $form->input('Acteur.salutation', array('label'=>'Civilit&eacute;','size' => '20'));?>
			<br/>
		 	<?php echo $form->input('Acteur.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>','size' => '40'));?>
			<br/>
		 	<?php echo $form->input('Acteur.prenom', array('label'=>'Prénom <acronym title="obligatoire">*</acronym>','size' => '40'));?>
			<br/>
		 	<?php echo $form->input('Acteur.titre', array('label'=>'Titre','size' => '40'));?>
		</fieldset>
		<fieldset>
		<legend>Adresse postale</legend>
		 	<?php echo $form->input('Acteur.adresse1', array('label'=>'Adresse 1','size' => '40'));?>
			<br/>
		 	<?php echo $form->input('Acteur.adresse2', array('label'=>'Adresse 2','size' => '40'));?>
			<br/>
		 	<?php echo $form->input('Acteur.cp', array('label'=>'Code postal','size' => '20'));?>
			<br/>
		 	<?php echo $form->input('Acteur.ville', array('label'=>'Ville','size' => '40'));?>
			<br/>
		</fieldset>
		<fieldset>
		<legend>Contacts</legend>
		 	<?php echo $form->input('Acteur.telfixe', array('label'=>'Téléphone fixe','size' => '20'));?>
			<br/>
		 	<?php echo $form->input('Acteur.telmobile', array('label'=>'Téléphone mobile','size' => '20'));?>
			<br/>
		 	<?php echo $form->input('Acteur.email', array('label'=>'Email','size' => '40'));?>
		</fieldset>
	</div>

	<div class="demi">
		<fieldset>
		<legend>Type</legend>
			<?php echo $form->label('Acteur.typeacteur_id', 'Type d\'acteur <acronym title="obligatoire">*</acronym>');?>
			<?php
				if ($html->value('Acteur.typeacteur_id')) $typeacteur_id = $html->value('Acteur.typeacteur_id');
				else $typeacteur_id = 0;
			?>
			<select id="ActeurTypeacteurId" onchange="afficheInfosElus(this);" name="data[Acteur][typeacteur_id]">
				<?php if ((count($typeacteurs)>1) && (!$html->value('Acteur.id'))) echo '<option value=""> </option>' ?>
				<?php
					foreach($typeacteurs as $typeacteur) {
						echo '<option class="'.$typeacteur['Typeacteur']['elu'].'" value="'.$typeacteur['Typeacteur']['id'].'"';
						if ($typeacteur['Typeacteur']['id']==$typeacteur_id)
							echo ' selected="selected" ';
						echo '>'.$typeacteur['Typeacteur']['nom'].'</option>';
					}
				?>
			</select>
			<div class="spacer"></div>
			<div id='infoElus'>
			 	<?php echo $form->input('Acteur.position', array('label'=>'Ordre dans le conseil','size' => '3'));?>
				<div class="spacer"></div>
			 	<?php echo $form->input('Service.Service', array('label'=>'Délégation(s)', 'options'=>$services, 'default'=>$selectedServices, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty'=>true, 'escape' => false));?>
				<div class="spacer"></div>
				<?php echo $form->label('Acteur.date_naissance', 'Date de naissance');?>
				<?php echo $form->day('Acteur.date_naissance',null,null,false); ?>-
                <?php echo $form2->monthOptionTagFr('Acteur.date_naissance_month', $html->value('Acteur.date_naissance'));?>-
                <?php echo $form2->yearOptionTag('Acteur.date_naissance_year', $html->value('Acteur.date_naissance'),null, 1920, 1990, null); ?>
			</div>
		</fieldset>
		<div class="spacer"></div>

		<fieldset>
		<legend>Autres informations</legend>
 			<?php echo $form->input('Acteur.note', array('type'=>'textarea', 'label'=>'Note', 'cols' => '30'));?>
		</fieldset>
	</div>
	<div class="spacer"></div>

<div class="submit">
	<?php if ($this->action=='edit') echo $form->hidden('Acteur.id')?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php echo $html->link('Annuler', '/acteurs/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $form->end(); ?>
