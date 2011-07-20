<script>
window.onload=initAffichage;

/*
* Affiche ou masque les options en fonction du type d'info sup
*/
function afficheOptions(typeInfoSup) {

	/* On masque toutes les options */
	document.getElementById("taille").style.display = 'none';
	document.getElementById("val_initiale").style.display = 'none';
	document.getElementById("val_initiale_boolean").style.display = 'none';
	document.getElementById("val_initiale_date").style.display = 'none';
	document.getElementById("recherche").style.display = 'none';
	document.getElementById("gestionListe").style.display = 'none';

	/* si le choix est vide : on sort */
	if((typeInfoSup.value.length==0) || (typeInfoSup.value==null)) return;

	/* on affiche en fonction du type d'info sup */
	switch(typeInfoSup.value) {
	case "text":
		document.getElementById("taille").style.display = '';
		document.getElementById("val_initiale").style.display = '';
		document.getElementById("recherche").style.display = '';
		break;
	case "richText":
		document.getElementById("val_initiale").style.display = '';
		document.getElementById("recherche").style.display = '';
		break;
	case "date":
		document.getElementById("val_initiale_date").style.display = '';
		document.getElementById("recherche").style.display = '';
		break;
	case "file":
		break;
	case "boolean":
		document.getElementById("val_initiale_boolean").style.display = '';
		document.getElementById("recherche").style.display = '';
		break;
	case "odtFile":
		break;
	case "list":
		document.getElementById("recherche").style.display = '';
		document.getElementById("gestionListe").style.display = '';
		break;
	}
}

function initAffichage() {
	selectTypeInfoSup = document.getElementById("selectTypeInfoSup");
	afficheOptions(selectTypeInfoSup);
}
</script>

<?php echo $javascript->link('calendrier.js'); ?>

<?php
	if($html->value('Infosupdef.id')) {
		echo "<h2>Modification d'une information suppl&eacute;mentaire</h2>";
		echo $form->create('Infosupdef',array('url'=>'/infosupdefs/edit/'.$html->value('Infosupdef.id'),'type'=>'post','name'=>'infoSupForm'));
	}
	else {
		echo "<h2>Ajout d'une information suppl&eacute;mentaire</h2>";
		echo $form->create('Infosupdef',array('url'=>'/infosupdefs/add/','type'=>'post','name'=>'infoSupForm'));
	}
?>

	<div class="required">
	 	<?php echo $form->input('Infosupdef.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>','size' => '40', 'title'=>'Nom affiché dans le formulaire d\'édition des projets'));?>
	</div>
	<br/>
	<div class="required">
	 	<?php echo $form->input('Infosupdef.commentaire', array('label'=>'Commentaire','size' => '80', 'title'=>'Bulle d\'information affiché dans le formulaire d\'édition des projets'));?>
	</div>
	<br/>
	<div class="required">
	 	<?php echo $form->input('Infosupdef.code', array('label'=>'Code <acronym title="obligatoire">*</acronym>','size' => '40', 'title'=>'Code unique utilisé pour les éditions (pas d\'espace ni de caractère spécial)'), false, false);?>
	</div>
	<br/>
	<div class="required">
		<?php
			$htmlAttributes['disabled'] = '';
			$empty=false;
			$mesErr='';
			if (($this->action=='edit') && !$Infosupdef->isDeletable($this->data, $mesErr)) {
				$htmlAttributes['disabled'] = 'disabled';
				echo $form->hidden('Infosupdef.type');
				$empty=true;
			}
		?>
	 	<?php echo $form->input('Infosupdef.type',array('label'=>'type <acronym title="obligatoire">(*)</acronym>', 'options'=>$types, 'id'=>'selectTypeInfoSup', 'onChange'=>"afficheOptions(this);", 'disabled'=>$htmlAttributes['disabled'], 'showEmpty'=>$empty)); ?>
	</div>
	</ br>
	<div class="required" id="taille">
	 	<?php echo $form->input('Infosupdef.taille', array('label'=>'Taille','size' => '2', 'title'=>'Taille du champ affiché dans le formulaire d\'édition des projets (uniquement pour le type Texte)'));?>
	</div>
	<br/>
	<div class="required" id="val_initiale">
	 	<?php echo $form->input('Infosupdef.val_initiale', array('label'=>'Valeur initiale','size' => '80', 'title'=>'Valeur initiale lors de la création d\'un projet'));?>
	</div>
	<div class="required" id="val_initiale_boolean">
	 	<?php echo $form->input('Infosupdef.val_initiale_boolean', array('label'=>'Valeur initiale','options'=>$listEditBoolean));?>
	</div>
	<div class="required" id="val_initiale_date">
		<?php echo $form->input('Infosupdef.val_initiale_date', array('div'=>false, 'label'=>'Valeur initiale','id'=>'InfosupdefValInitialeDate', 'size'=>'9', 'title'=>'Valeur initiale lors de la création d\'un projet'));?>
		<?php echo '&nbsp;';?>
		<?php echo $html->link($html->image("calendar.png", array('style'=>"border:0;")), "javascript:show_calendar('infoSupForm.InfosupdefValInitialeDate', 'f');", array(), false, false);?>
	</div>
	<br/>
	<div class="required" id="recherche">
		<?php echo $form->label('Infosupdef.recherche','Inclure dans la recherche'); ?>
 		<?php echo $form->input('Infosupdef.recherche',array('type'=>'checkbox', 'label'=>false));?>
	</div>
	<br/>
	<div id="gestionListe">
		<span>Note : la gestion des éléments de la liste est accessible &agrave; partir de la liste des informations suppl&eacute;mentaires.</span>
	</div>
	<br/>

	<div class="submit">
		<?php if ($this->action=='edit') echo $form->hidden('Infosupdef.id'); ?>
		<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
		<?php echo $html->link('Annuler', '/infosupdefs/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>

<?php echo $form->end(); ?>
