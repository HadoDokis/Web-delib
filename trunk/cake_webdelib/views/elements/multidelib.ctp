<?php
// initialisation des boutons pour les délibération
$links = array(
	'modifier' => array('title'=>SHY, 'url'=>'#', 'escapeTitle'=>false, 'htmlAttributes'=>array('class'=>'link_modifier', 'title'=>'Modifier')),
	'annulerModifier' => array('title'=>SHY, 'url'=>'#', 'escapeTitle'=>false, 'htmlAttributes'=>array('class'=>'link_modifier_back', 'title'=>'Annuler les modifications', 'style'=>'display: none;')),
	'supprimer' => array('title'=>SHY, 'url'=>'#', 'escapeTitle'=>false, 'htmlAttributes'=>array('class'=>'link_supprimer', 'title'=>'Supprimer')),
	'annulerSupprimer' => array('title'=>SHY, 'url'=>'#', 'escapeTitle'=>false, 'htmlAttributes'=>array('class'=>'link_supprimer_back', 'title'=>'Annuler la suppression', 'style'=>'display: none;')),
	);

// affichage des délibérations rattachées
if (isset($this->data['Multidelib'])) {
	foreach($this->data['Multidelib'] as $i=>$delib) {
		echo $html->tag('fieldset', null, array('id'=>'delibRattachee'.$delib['id']));
		echo $html->tag('legend', '&nbsp;Délibération rattachée : '.$delib['id'].'&nbsp;');
			// info pour la suppression
			echo $form->hidden('MultidelibASupprimer.'.$delib['id'], array('value'=>$delib['id'], 'disabled'=>true));
			// affichage de la délibération rattachée
			echo $html->tag('div', null, array('id'=>'delibRattacheeDisplay'.$delib['id']));
				// affichage libellé
				echo $html->tag('label', 'Libellé <acronym title="obligatoire">(*)</acronym>');
				echo $html->tag('span', $delib['objet']);
				echo $html->tag('div', '', array('class'=>'spacer'));
				// affichage texte de délibération
				echo $html->tag('label', 'Texte délibération');
				if (Configure::read('GENERER_DOC_SIMPLE'))
					echo $html->tag('span', $delib['deliberation']);
				else
					echo $html->tag('span', $delib['deliberation_name']);
				echo $html->tag('div', '', array('class'=>'spacer'));
				// affichage des annexes
				echo $html->tag('label', 'Annexe(s)');
				echo '<div class="fckEditorProjet">';
					$annexeOptions = array('mode'=>'display');
					if (isset($delib['Annex'])) $annexeOptions['annexes'] = $delib['Annex'];
					echo $this->element('annexe', $annexeOptions);
				echo '</div>';
				echo $html->tag('div', '', array('class'=>'spacer'));
			echo $html->tag('/div');
			// modification de la délibération rattachée
			echo $html->tag('div', null, array('id'=>'delibRattacheeForm'.$delib['id'], 'style'=>'display: none'));
				echo $form->hidden('Multidelib.'.$delib['id'].'.id', array('value'=>$delib['id'], 'disabled'=>true));
				// saisie libellé
				echo $html->tag('label', 'Libellé <acronym title="obligatoire">(*)</acronym>');
				echo $form->input('Multidelib.'.$delib['id'].'.objet', array(
					'type'=>'textarea',
					'label'=>'',
					'cols'=>'60','rows'=>'1',
					'value'=>$delib['objet'],
					'disabled'=>true));
				echo $html->tag('div', '', array('class'=>'spacer'));
				// saisie texte de délibération
				echo $html->tag('label', 'Texte délibération');
				if (Configure::read('GENERER_DOC_SIMPLE')){
					echo '<div class="fckEditorProjet">';
						echo $form->input('Multidelib.'.$delib['id'].'.deliberation', array(
							'label'=>'',
							'type'=>'textarea',
							'value'=>$delib['deliberation'],
							'disabled'=>true));
					echo '</div>';
				} else {
					if (empty($delib['deliberation_name']))
						echo  $form->input("Multidelib.".$delib['id'].".deliberation", array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>'Texte d&eacute;lib&eacute;ration', 'disabled'=>true));
					else {
						$url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$delib['id']."/deliberation.odt";
						echo $html->tag('span', '', array('id'=>'MultidelibDeliberationAdd'.$delib['id'], 'style'=>'display: none;'));
						echo $html->tag('span', null, array('id'=>'MultidelibDeliberationDisplay'.$delib['id']));
							echo "<a href='$url'>".$delib['deliberation_name']."</a>";
							echo '&nbsp;&nbsp;';
							echo $html->link('Supprimer', 'javascript:supprimerTextDelibDelibRattachee('.$delib['id'].')', null, 'Voulez-vous vraiment supprimer le fichier ?');
						echo $html->tag('/span');
					}
				}
				echo $html->tag('div', '', array('class'=>'spacer'));
				// saisie des annexes
				echo $html->tag('label', 'Annexe(s)');
				echo '<div class="fckEditorProjet">';
					$annexeOptions = array('ref'=>'delibRattachee'.$delib['id'], 'affichage'=>'partiel');
					if (isset($delib['Annex'])) $annexeOptions['annexes'] = $delib['Annex'];
					echo $this->element('annexe', $annexeOptions);
				echo '</div>';
			echo $html->tag('/div');
			echo $html->tag('div', '', array('class'=>'spacer'));
			// affichage des boutons action
			echo $html->tag('div', null, array('id'=>'delibRattacheeAction'.$delib['id'], 'class'=>'action'));
				$links['modifier']['htmlAttributes']['onclick'] = 'modifierDelibRattachee(this, '.$delib['id'].')';
				$links['annulerModifier']['htmlAttributes']['onclick'] = 'annulerModifierDelibRattachee(this, '.$delib['id'].')';
				$links['supprimer']['htmlAttributes']['onclick'] = 'supprimerDelibRattachee(this, '.$delib['id'].')';
				$links['annulerSupprimer']['htmlAttributes']['onclick'] = 'annulerSupprimerDelibRattachee(this, '.$delib['id'].')';
				echo $menu->linkBarre($links);
			echo $html->tag('/div');
		echo $html->tag('/fieldset');
		echo $html->tag('div', '', array('class'=>'spacer'));
	}
}

// Ajout des délibérations
// template pour l'ajout
echo $html->tag('div', null, array('id'=>'ajouteMultiDelibTemplate', 'style'=>'width:800px; display:none'));
	echo $html->tag('fieldset', null, array('id'=>'delibRattachee0'));
	echo $html->tag('legend', 'Nouvelle délibération rattachée ');
		echo $html->tag('div', null, array('id'=>'delibRattacheeForm0'));
			// saisie libellé
			echo $html->tag('label', 'Libellé <acronym title="obligatoire">(*)</acronym>');
			echo $form->input('Multidelib.0.objet', array(
				'type'=>'textarea',
				'label'=>'',
				'value'=>'',
				'cols'=>'60','rows'=>'1',
				'disabled'=>true));
			echo $html->tag('div', '', array('class'=>'spacer'));
			// saisie texte de délibération
			echo $html->tag('label', 'Texte délibération');
			if (Configure::read('GENERER_DOC_SIMPLE')){
				echo '<div class="fckEditorProjet">';
					echo $form->input('Multidelib.0.deliberation', array(
						'label'=>'',
						'type'=>'textarea',
						'value'=>'',
						'disabled'=>true));
				echo '</div>';
			} else
				echo  $form->input("Multidelib.0.deliberation", array(
					'label'=>'',
					'type'=>'file',
					'size'=>'60',
					'disabled'=>true));
		echo $html->tag('/div');
		echo $html->tag('div', '', array('class'=>'spacer'));
		// affichage des boutons action
		echo $html->tag('div', null, array('id'=>'delibRattacheeAction0', 'class'=>'action'));
			echo $html->link('Annuler', '#self', array('class'=>'link_annuler_sans_border', 'onClick'=>'javascript:$(this).parent().parent().parent().remove();'));
		echo $html->tag('/div');
	echo $html->tag('/fieldset');
echo $html->tag('/div');

// div pour l'ajout les délibérations rattachées
echo $html->tag('div', '', array('id'=>'ajouteMultiDelib'));

// lien pour ajouter une nouvelle délibération rattachée
echo $html->tag('div', '', array('class'=>'spacer'));
echo $html->link('Ajouter une délibération rattachée', 'javascript:ajouterMultiDelib()', array('class'=>'link_annexe'));
echo $html->tag('div', '', array('class'=>'spacer'));
echo $html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
?>
<script>
// variables globales
var iMultiDelibAAjouter = 1000;

// Fonction d'ajout d'une nouvelle deliberation : duplique le div ajouteMultiDelibTemplate et incrémente l'indexe
function ajouterMultiDelib() {
	iMultiDelibAAjouter++; 
	var newTemplate = $('#ajouteMultiDelibTemplate').clone();
	newTemplate.attr('id', newTemplate.attr('id').replace('Template', iMultiDelibAAjouter));
	newTemplate.find('textarea').each(function(){
		$(this).removeAttr('disabled');
		$(this).attr('id', $(this).attr('id').replace('0', iMultiDelibAAjouter));
		$(this).attr('name', $(this).attr('name').replace('0', iMultiDelibAAjouter));
	});
	newTemplate.find('input').each(function(){
		$(this).removeAttr('disabled');
		$(this).attr('id', $(this).attr('id').replace('0', iMultiDelibAAjouter));
		$(this).attr('name', $(this).attr('name').replace('0', iMultiDelibAAjouter));
	});
	$('#ajouteMultiDelib').append(newTemplate);
	<?php
	if (Configure::read('GENERER_DOC_SIMPLE')){
		echo "$('#Multidelib'+iMultiDelibAAjouter+'Deliberation').ckeditor();\n";
	}
	?>
	newTemplate.show();
}

// Fonction de modification d'une délibération rattachée
function modifierDelibRattachee(obj, delibId) {
	$('#delibRattacheeDisplay'+delibId).hide();
	$('#delibRattacheeForm'+delibId).show();
	
	$('#Multidelib'+delibId+'Id').removeAttr('disabled');
	$('#Multidelib'+delibId+'Objet').removeAttr('disabled').show();
	$('#Multidelib'+delibId+'Deliberation').removeAttr('disabled').show();
//	$('#delibRattacheeForm'+delibId+' :input').removeAttr('disabled').show();
	if ($('#Multidelib'+delibId+'Deliberation').length)
		$('#Multidelib'+delibId+'Deliberation').ckeditor();
	
	$(obj).hide();
	$(obj).next().show();
	$(obj).next().next().hide();
}

// Fonction d'annulation des modifications d'une délibération rattachée
function annulerModifierDelibRattachee(obj, delibId) {
	$('#delibRattacheeDisplay'+delibId).show();
	$('#delibRattacheeForm'+delibId).hide();

	$('#delibRattacheeForm'+delibId+' :input').attr('disabled', true);
	if ($('#Multidelib'+delibId+'Deliberation').length)
		$('#Multidelib'+delibId+'Deliberation').ckeditor(function(){this.destroy();});

	$(obj).hide();
	$(obj).prev().show();
	$(obj).next().show();
}

// Fonction de suppression d'une délibération rattachée
function supprimerDelibRattachee(obj, delibId) {
	$('#delibRattacheeDisplay'+delibId).addClass('aSupprimer');

	$('#MultidelibASupprimer'+delibId).removeAttr('disabled');
	$(obj).hide();
	$(obj).next().show();
	$(obj).prev().prev().hide();
}

// Fonction de d'annulation de suppression d'une annexe
function annulerSupprimerDelibRattachee(obj, delibId) {
	$('#delibRattacheeDisplay'+delibId).removeClass('aSupprimer');

	$('#MultidelibASupprimer'+delibId).attr('disabled', true);
	$(obj).hide();
	$(obj).prev().show();
	$(obj).prev().prev().prev().show();
}

// Fonction de suppression du texte de délibération sous forme de fichier joint
function supprimerTextDelibDelibRattachee(delibId) {
	$('#MultidelibDeliberationDisplay'+delibId).hide();
	$('#MultidelibDeliberationAdd'+delibId)
		.html('<input type="file" id="Multidelib'+delibId+'Deliberation" value="" title="" size="60" name="data[Multidelib]['+delibId+'][deliberation]"></input>')
		.show();
	
}
</script>