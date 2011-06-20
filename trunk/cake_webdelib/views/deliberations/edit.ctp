<?php echo $javascript->link('calendrier.js'); ?>
<?php echo $javascript->link('utils.js'); ?>
<?php echo $javascript->link('ckeditor/ckeditor'); ?>

<?php
	if($html->value('Deliberation.id')) {
		echo "<h2>Modification du projet : ".$html->value('Deliberation.id')."</h2>";
		echo $form->create('Deliberation', array('url'=>'/deliberations/edit/'.$html->value('Deliberation.id'), 'type'=>'file', 'name'=>'Deliberation'));
	} else {
		echo "<h2>Ajout d'un projet</h2>";
		echo $form->create('Deliberation', array('url'=>'/deliberations/add','type'=>'file', 'name'=>'Deliberation'));
	}
?>

<div class='onglet'>
	<a></a>
	<a href="javascript:afficheOnglet(1)" id='lienTab1' class="ongletCourant">Informations principales</a>
	<a href="javascript:afficheOnglet(2)" id='lienTab2'>Textes</a>
	<a href="javascript:afficheOnglet(3)" id='lienTab3'>Annexe(s)</a>
<?php if (!empty($infosupdefs)): ?>
	<a href="javascript:afficheOnglet(4)" id='lienTab4'>Informations suppl&eacute;mentaires</a>
<?php endif; ?>
<?php if (Configure::read('DELIBERATIONS_MULTIPLES')): ?>
	<a href="javascript:afficheOnglet(5)" id='lienTab5'>D&eacute;lib&eacute;rations rattach&eacute;es</a>
<?php endif; ?>
</div>

<div id="tab1">
        <fieldset id='info'>
	<div class='demi'>
		<?php echo '<b><u>Rédacteur</u></b> : <i>'.$html->value('Redacteur.prenom').'</i>';?>
		<br/>
		<?php echo '<b><u>Service émetteur</u></b> : <i>'.$html->value('Service.libelle').'</i>'; ?>
	</div>
	<div class='demi'>
		<?php echo '<b><u>Date cr&eacute;ation</u></b> : <i>'. $html->value('Deliberation.created').'</i>';?>
		<br/>
		<?php echo '<b><u>Date de modification</u></b> : <i>'. $html->value('Deliberation.modified').'</i>';?>
	</div>
        </fieldset>
	<div class='spacer'></div>
        <?php echo $form->input('Deliberation.nature_id', array('label'   =>'Nature <acronym title="obligatoire">(*)</acronym>', 
                                                                 'options' =>$session->read('user.Nature'), 
                                                                 'empty'   =>false, 
                                                                 'escape'  =>false)); ?>
	<div class='spacer'></div>
 	<?php echo $form->input('Deliberation.objet', array('type'=>'textarea','label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','cols' => '60','rows'=> '2'));?>
	<div class='spacer'></div>

 	<?php echo $form->input('Deliberation.titre', array('type'=>'textarea','label'=>'Titre','cols' => '60','rows'=> '2'));?>
	<div class='spacer'></div>

	<?php echo $form->input('Deliberation.seance_id', array('label'=>'Date séance', 'options'=>$date_seances, 'empty'=>true)); ?>
	<div class='spacer'></div>

	<?php echo $form->input('Deliberation.rapporteur_id', array('label'=>'Rapporteur', 'options'=>$rapporteurs, 'empty'=>true)); ?>
	<div class='spacer'></div>

	<?php echo $form->input('Deliberation.theme_id', array('label'=>'Thème <acronym title="obligatoire">(*)</acronym>', 'options'=>$themes, 'default'=>$html->value('Deliberation.theme_id'), 'empty'=>false, 'escape'=>false)); ?>
	<div class='spacer'></div>

	<?php echo $form->input('Deliberation.num_pref',array('div'=>false,'label'=>'Num Pref','id'=>'classif1', 'size' => '60','readonly'=> 'readonly'));?>
		<a class="list_form" href="#add" onclick="javascript:window.open('<?php echo $this->base; ?>/deliberations/classification', 'Select_attribut', 'scrollbars=yes,width=570,height=450');" id="classification_text">[Choisir la classification]</a>
		 <?php echo $form->hidden('Deliberation.num_pref',array('id'=>'classif2','name'=>'classif2'))?>
	<div class='spacer'></div>

	<?php echo $form->label('Deliberation.date_limite', 'Date limite');?>
	<?php
		if (!empty($this->data['Deliberation']['date_limite']) && $this->data['Deliberation']['date_limite'] != '01/01/1970')
			$value = "value='".$this->data['Deliberation']['date_limite']."'";
		else
			$value = "value=''";
	?>
	<input name="date_limite" size="9" <?php echo $value; ?>"/>&nbsp;<a href="javascript:show_calendar('Deliberation.date_limite','f');"><?php echo $html->image("calendar.png", array('style'=>"border='0'")); ?></a>
	<div class='spacer'></div>
</div>

<div id="tab2" style="display: none;">
	<h3>Texte de projet</h3>
    <?php echo $this->element('texte', array('key' => 'texte_projet'));?>
	<div class='spacer'></div>

	<h3>Note synth&egrave;se</h3>
    <?php echo $this->element('texte', array('key' => 'texte_synthese'));?>
	<div class='spacer'></div>

	<h3>Texte de d&eacute;lib&eacute;ration</h3>
    <?php echo $this->element('texte', array('key' => 'deliberation'));?>
	<div class='spacer'></div>
</div>

<div id="tab3" style="display: none;">
    <?php echo $this->element('annexe');?>
</div>

<?php if (!empty($infosupdefs)): ?>
<div id="tab4" style="display: none;">
	<?php
	foreach($infosupdefs as $infosupdef) {
		$fieldName = 'Infosup.'.$infosupdef['Infosupdef']['code'];
		$fieldId = 'Infosup'.Inflector::camelize($infosupdef['Infosupdef']['code']);
		echo "<div class='required'>";
			echo $form->label($fieldName, $infosupdef['Infosupdef']['nom'], array('name'=>'label'.$infosupdef['Infosupdef']['code']));
			if ($infosupdef['Infosupdef']['type'] == 'text') {
				echo $form->input($fieldName, array('label'=>'', 'size'=>$infosupdef['Infosupdef']['taille'], 'title'=>$infosupdef['Infosupdef']['commentaire']));
			} elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
				echo $form->input($fieldName, array('label'=>'', 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire']));
			} elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				echo $form->input($fieldName, array('type'=>'text', 'div'=>false, 'label'=>'', 'size'=>'9', 'title'=>$infosupdef['Infosupdef']['commentaire']));
				echo '&nbsp;';
				echo $html->link($html->image("calendar.png", array('style'=>"border='0'")), "javascript:show_calendar('Deliberation.$fieldId', 'f');", array(), false, false);
			} elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
				echo '<div class="annexesGauche"></div>';
				echo '<div class="fckEditorProjet">';
					echo $form->input($fieldName, array('label'=>'', 'type'=>'textarea'));
					echo $fck->load($fieldId);
				echo '</div>';
				echo '<div class="spacer"></div>';
			} elseif ($infosupdef['Infosupdef']['type'] == 'file') {
				if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
					echo  $form->input($fieldName, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire']));
				else {
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
					echo '['.$html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
					echo '&nbsp;&nbsp;';
					echo $html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
					echo '</span>';
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
				if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
					echo  $form->input($fieldName, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire']));
				else {
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
					if (Configure::read('GENERER_DOC_SIMPLE')) {
						echo '['.$html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
					} else {
						$name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']] ;
						$url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$this->data['Deliberation']['id']."/$name";
						echo "<a href='$url'>$name</a> ";
					}
					echo '&nbsp;&nbsp;';
					echo $html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
					echo '</span>';
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'list') {
				echo $form->input($fieldName, array('label'=>'', 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire']));
			}
		echo '</div>';
		echo '<br>';
	};?>
</div>
<?php endif; ?>

<?php if (Configure::read('DELIBERATIONS_MULTIPLES')) :?> 
<div id="tab5" style="display: none;">
    <?php echo $this->element('multidelib');?>
</div>
<?php endif; ?>

<div class="spacer" style="border-top: solid 1px #e0ef90;"></div>

<div class="submit">
	<?php echo $form->hidden('Deliberation.id')?>
	<?php
		if ($html->value('Deliberation.id'))
			$onclick = "javascript:return checkForm(form, ".$html->value('Deliberation.id').")";
		else
			$onclick = "javascript:return checkForm(form, 0)";
		echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder', 'onclick'=>$onclick));
	?>
	<?php echo $html->link('Annuler', $redirect, array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $form->end(); ?>
<script>
// variables globales

// affichage de l'éditeur de texte intégré ckEditor
function editerTexte(obj, textId, afficheTextId) {
	$('#'+textId).ckeditor();
	$('#'+afficheTextId).hide();
	$(obj).hide();
}
</script>