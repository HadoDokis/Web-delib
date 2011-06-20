<?php
if (empty($this->data['Multidelib']))
	return;

// affichage des délibérations rattachées
foreach($this->data['Multidelib'] as $i=>$delib) {
	echo $html->tag('fieldset');
	echo $html->tag('legend', 'Délibération rattachée #'.($i+1));
		echo $html->tag('div', null, array('id'=>'delibRattacheeForm'.$delib['id']));
			echo $form->hidden('Multidelib.'.$delib['id'].'.id', array('value'=>$delib['id'], 'disabled'=>true));
			echo $form->input('Multidelib.'.$delib['id'].'.objet', array(
				'type'=>'textarea',
				'label'=>'Libellé <acronym title="obligatoire">(*)</acronym>',
				'cols'=>'60','rows'=>'2',
				'value'=>$delib['objet'],
				'disabled'=>true));
			echo $html->tag('div', '', array('class'=>'spacer'));

echo $html->tag('div', null, array('class'=>'required'));
	echo $form->label('Multidelib'.$delib['id'].'Deliberation', 'Texte délibération');
	if (Configure::read('GENERER_DOC_SIMPLE')){
		echo '<div class="annexesGauche"></div>';
		echo '<div class="fckEditorProjet">';
			echo $form->input('Multidelib.'.$delib['id'].'.deliberation', array(
				'label'=>'',
				'type'=>'textarea',
				'value'=>$delib['deliberation'],
				'disabled'=>true,
				'style'=>'display: none;'));
		echo '</div>';
	} else {
		if (empty($delib['Deliberation']['deliberation_name']))
			echo  $form->input("Multidelib.0.deliberation", array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>'Texte d&eacute;lib&eacute;ration'));
		else {
			$url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/$id/deliberation.odt";
			echo '<span id="DeliberationdeliberationInputFichierJoint" style="display: none;"></span>';
			echo '<span id="DeliberationdeliberationAfficheFichierJoint">'; 
				echo "<a href='$url'>$filename</a>";
				echo '&nbsp;&nbsp;';
				echo $html->link('Supprimer', "javascript:supprimerFichierJoint('Deliberation', 'deliberation', 'Texte d&eacute;lib&eacute;ration')", null, 'Voulez-vous vraiment supprimer le fichier ?');
			echo '</span>';
		}
	}
echo $html->tag('/div');


		echo $html->tag('/div');
		// input techniques
		echo $form->hidden('MultidelibASupprimer.'.$delib['id'], array('value'=>$delib['id'], 'disabled'=>true));
		// boutons
		echo $html->link('Modifier', '#', array(
			'onClick'=>'modifierDelibRattachee(this, '.$delib['id'].')'));
		echo $html->link('Annuler les modifications', '#', array(
			'onClick'=>'annulerModifierDelibRattachee(this, '.$delib['id'].')',
			'style'=>'display: none;'));
		echo '&nbsp;&nbsp;';
		echo $html->link('Supprimer', '#', array(
			'onClick'=>'supprimerDelibRattachee(this, '.$delib['id'].')'));
		echo $html->link('Annuler la suppression', '#', array(
			'onClick'=>'annulerDelibRattachee(this, '.$delib['id'].')',
			'style'=>'display: none;'));
	echo $html->tag('/fieldset');
}


//echo "<div class='required'>";
//	echo $form->label('Multidelib0Deliberation', 'Texte délibération');
//	if (Configure::read('GENERER_DOC_SIMPLE')){
//		echo '<div class="annexesGauche"></div>';
//		echo '<div class="fckEditorProjet">';
//			echo $form->input("Multidelib.0.deliberation", array('label'=>'', 'type'=>'textarea'));
//			echo $fck->load('data[Multidelib][0][deliberation]');
//		echo '</div>';
//	} else {
//		if (empty($delib['Deliberation']['deliberation_name']))
//			echo  $form->input("Multidelib.0.deliberation", array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>'Texte d&eacute;lib&eacute;ration'));
//		else {
//			$url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/$id/deliberation.odt";
//			echo '<span id="DeliberationdeliberationInputFichierJoint" style="display: none;"></span>';
//			echo '<span id="DeliberationdeliberationAfficheFichierJoint">'; 
//				echo "<a href='$url'>$filename</a>";
//				echo '&nbsp;&nbsp;';
//				echo $html->link('Supprimer', "javascript:supprimerFichierJoint('Deliberation', 'deliberation', 'Texte d&eacute;lib&eacute;ration')", null, 'Voulez-vous vraiment supprimer le fichier ?');
//			echo '</span>';
//		}
//	}
//echo '</div>';
?>
<script>
// variables globales

// Fonction de modification d'une délibération rattachée
function modifierDelibRattachee(obj, delibId) {
	$('#delibRattacheeForm'+delibId+' :input').removeAttr('disabled');
	
$('#Multidelib'+delibId+'Deliberation').ckeditor();
	
	$(obj).hide();
	$(obj).next().show();
	$(obj).next().next().hide();
}

// Fonction d'annulation des modifications d'une délibération rattachée
function annulerModifierDelibRattachee(obj, delibId) {
	$('#delibRattacheeForm'+delibId+' :input').attr('disabled', true);
	$(obj).hide();
	$(obj).prev().show();
	$(obj).next().show();

alert($('#Multidelib'+delibId+'Deliberation').val());
// remove editor from the page
 $('#Multidelib'+delibId+'Deliberation').ckeditor(function(){
 this.destroy();
 });

}

// Fonction de suppression d'une délibération rattachée
function supprimerDelibRattachee(obj, delibId) {
	$('#MultidelibASupprimer'+delibId).removeAttr('disabled');
	$(obj).hide();
	$(obj).next().show();
	$(obj).prev().prev().hide();
}

// Fonction de d'annulation de suppression d'une annexe
function annulerDelibRattachee(obj, delibId) {
	$('#MultidelibASupprimer'+delibId).attr('disabled', true);
	$(obj).hide();
	$(obj).prev().show();
	$(obj).prev().prev().prev().show();
}

</script>