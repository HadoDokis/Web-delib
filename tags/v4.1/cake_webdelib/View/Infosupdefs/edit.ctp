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

<?php
echo $this->Html->script('calendrier.js');

echo $this->Html->tag('h2', $titre);

echo $this->Form->create('Infosupdef',array('url'=>array('action'=>$this->action),'type'=>'post','name'=>'infoSupForm'));
?>

	<div class="required">
	 	<?php echo $this->Form->input('Infosupdef.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>','size' => '40', 'title'=>'Nom affiché dans le formulaire d\'édition des projets'));?>
	</div>
	<br/>
	<div class="required">
	 	<?php echo $this->Form->input('Infosupdef.commentaire', array('label'=>'Commentaire','size' => '80', 'title'=>'Bulle d\'information affiché dans le formulaire d\'édition des projets'));?>
	</div>
	<br/>
	<div class="required">
	 	<?php echo $this->Form->input('Infosupdef.code', array('label'=>'Code <acronym title="obligatoire">*</acronym>','size' => '40', 'title'=>'Code unique utilisé pour les éditions (pas d\'espace ni de caractère spécial)'), false, false);?>
	</div>
	<br/>
	<div class="required">
		<?php
			$htmlAttributes['disabled'] = '';
			$empty=false;
			$mesErr='';
			if (($this->action=='edit') && !$Infosupdef->isDeletable($this->data, $mesErr)) {
				$htmlAttributes['disabled'] = 'disabled';
				echo $this->Form->hidden('Infosupdef.type');
				$empty=true;
			}
		?>
	 	<?php echo $this->Form->input('Infosupdef.type',array('label'=>'type <acronym title="obligatoire">(*)</acronym>', 'options'=>$types, 'id'=>'selectTypeInfoSup', 'onChange'=>"afficheOptions(this);", 'disabled'=>$htmlAttributes['disabled'], 'showEmpty'=>$empty)); ?>
	</div>
	</ br>
	<div class="required" id="taille">
	 	<?php echo $this->Form->input('Infosupdef.taille', array('label'=>'Taille','size' => '2', 'title'=>'Taille du champ affiché dans le formulaire d\'édition des projets (uniquement pour le type Texte)'));?>
	</div>
	<br/>
	<div class="required" id="val_initiale">
	 	<?php echo $this->Form->input('Infosupdef.val_initiale', array('label'=>'Valeur initiale','size' => '80', 'title'=>'Valeur initiale lors de la création d\'un projet'));?>
	</div>
	<div class="required" id="val_initiale_boolean">
	 	<?php echo $this->Form->input('Infosupdef.val_initiale_boolean', array('label'=>'Valeur initiale','options'=>$listEditBoolean));?>
	</div>
	<div class="required" id="val_initiale_date">
		<?php echo $this->Form->input('Infosupdef.val_initiale_date', array('div'=>false, 'label'=>'Valeur initiale','id'=>'InfosupdefValInitialeDate', 'size'=>'9', 'title'=>'Valeur initiale lors de la création d\'un projet'));?>
		<?php echo '&nbsp;';?>
		<?php echo $this->Html->link($this->Html->image("calendar.png", array('style'=>"border:0;")), "javascript:show_calendar('infoSupForm.InfosupdefValInitialeDate', 'f');", array('escape' => false), false);?>
	</div>
	<br/>
<?php
	if($this->Html->value('Infosupdef.model') == 'Deliberation') {
		echo $this->Html->tag('div', null, array('class'=>'required', 'id'=>'recherche'));
		echo $this->Form->label('Infosupdef.recherche','Inclure dans la recherche');
		echo $this->Form->input('Infosupdef.recherche',array('type'=>'checkbox', 'label'=>false));
		echo $this->Html->tag('/div');
	} else
		echo $this->Form->hidden('Infosupdef.recherche', array('value'=>false));
?>
	<br/>
	<div id="gestionListe">
		<span>Note : la gestion des éléments de la liste est accessible &agrave; partir de la liste des informations suppl&eacute;mentaires.</span>
	</div>
	<br/>
        <?php echo $this->Form->input('Profil', array('options' =>$profils, 'multiple' => true, 'size' => 10, 'default' => $profils_selected)); ?>

	<div class="submit">
	<?php
		if ($this->action=='edit') echo $this->Form->hidden('Infosupdef.id');
		echo $this->Form->hidden('Infosupdef.id');
		echo $this->Form->hidden('Infosupdef.model');
                $this->Html2->boutonsSaveCancelUrl($lienRetour); 
//		echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));
//		echo $this->Html->link('Annuler', $lienRetour, array('class'=>'link_annuler', 'name'=>'Annuler'))
	?>
	</div>
<?php echo $this->Form->end(); ?>
