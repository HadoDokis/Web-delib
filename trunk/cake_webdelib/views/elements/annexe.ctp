<?php
// affichage des annexes
echo $html->tag('table', null, array('id'=>'afficheAnnexes', 'cellpadding'=>'0', 'cellspacing'=>'0'));
	echo $html->tableHeaders(array('No', 'Nom du fichier', 'Titre', 'Joindre au  contrôle de légalité', 'Action'));
	if (isset($this->data['Annex'])) {
		foreach ($this->data['Annex'] as $rownum => $annexe) {
			echo $html->tag('tr', null, array('id'=>'afficheAnnexe'.$annexe['id']));
				echo $html->tag('td', $rownum+1);
				echo $html->tag('td', $html->link($annexe['filename'] ,'/annexes/download/'.$annexe['id']));
				echo $html->tag('td');
					echo $html->tag('span', $annexe['titre'], array(
						'id'=>'afficheAnnexeTitre'.$annexe['id'],
						'valeur_init'=>$annexe['titre']));
					echo $form->input('AnnexesAModifier.'.$annexe['id'].'.titre', array(
						'id'=>'modifieAnnexeTitre'.$annexe['id'],
						'label'=>false,
						'value'=>$annexe['titre'],
						'size' => '40',
						'disabled'=>'disabled',
						'style'=>'display:none;'));
				echo $html->tag('/td');
				echo $html->tag('td');
					echo $html->tag('span', $annexe['joindre_ctrl_legalite']?'Oui':'Non', array(
						'id'=>'afficheAnnexeCtrl'.$annexe['id'],
						'valeur_init'=>$annexe['joindre_ctrl_legalite']));
					echo $form->input('AnnexesAModifier.'.$annexe['id'].'.joindre_ctrl_legalite', array(
						'id'=>'modifieAnnexeCtrl'.$annexe['id'],
						'label'=>false,
						'type'=>'checkbox',
						'checked'=>($annexe['joindre_ctrl_legalite']==1),
						'disabled'=>'disabled',
						'style'=>'display:none;'));
				echo $html->tag('/td');
				echo $html->tag('td');
					echo $html->link('Supprimer', '#', array('onClick'=>'supprimerAnnexe(this, '.$annexe['id'].')'));
					echo $html->link('Annuler la suppression', '#', array('onClick'=>"annulerSupprimerAnnexe(this, ".$annexe['id'].")", 'style'=>'display: none;'));
					echo '&nbsp;&nbsp;';
					echo $html->link('Modifier', '#', array('onClick'=>'modifierAnnexe(this, '.$annexe['id'].')'));
					echo $html->link('Annuler la modification', '#', array('onClick'=>"annulermodifierAnnexe(this, ".$annexe['id'].")", 'style'=>'display: none;'));
				echo $html->tag('/td');
			echo $html->tag('/tr');
		}
	}
echo $html->tag('/table');
echo $html->tag('div', '', array('class'=>'spacer'));

// div pour la suppression des annexes
echo $html->tag('div', '', array('id'=>'supprimeAnnexes'));

// template pour l'ajout des annexes
echo $html->tag('div', null, array('id'=>'ajouteAnnexeTemplate', 'style'=>'width:800px; display:none;'));
	echo $html->tag('fieldset');
	echo $html->tag('legend', 'Nouvelle annexe');
		echo $form->input('Annex.0.file', array('label'=>'Annexe<acronym title="obligatoire">(*)</acronym>', 'type'=>'file', 'size' => '80', 'disabled'=>'disabled'));
		echo $html->tag('div', '', array('class'=>'spacer'));
		echo $form->input('Annex.0.titre', array('label'=>'Titre', 'value'=>'', 'size' => '60', 'disabled'=>'disabled'));
		echo $html->tag('div', '', array('class'=>'spacer'));
		echo $form->input('Annex.0.ctrl', array('label'=>'Joindre ctrl légalité', 'type'=>'checkbox', 'value'=>false, 'disabled'=>'disabled'));
		echo $html->tag('div', '', array('class'=>'spacer'));
		echo $html->link('Annuler', '#self', array('class'=>'link_annuler_sans_border', 'onClick'=>'javascript:$(this).parent().parent().remove();'));
	echo $html->tag('/fieldset');
echo $html->tag('/div');

// div pour l'ajout des annexes
echo $html->tag('div', '', array('id'=>'ajouteAnnexes'));

// lien pour ajouter une nouvelle annexes
echo $html->tag('div', '', array('class'=>'spacer'));
echo $html->link('Ajouter une annexe', 'javascript:ajouterAnnexe()', array('class'=>'link_annexe'));
echo $html->tag('div', '', array('class'=>'spacer'));
echo $html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
?>
<script>
// variables globales
var nbAnnexeAAjouter = 0;

// Fonction d'ajout d'une nouvelle annexe : duplique le div ajouteAnnexeTemplate et incrémente l'indexe
function ajouterAnnexe() {
	nbAnnexeAAjouter++; 
	var addDiv = $('#ajouteAnnexes');
	var newTemplate = $('#ajouteAnnexeTemplate').clone();
	newTemplate.attr('id', newTemplate.attr('id').replace('Template', nbAnnexeAAjouter));
	newTemplate.find('input').each(function(){
		$(this).removeAttr('disabled');
		$(this).attr('id', $(this).attr('id').replace('0', nbAnnexeAAjouter));
		$(this).attr('name', $(this).attr('name').replace('0', nbAnnexeAAjouter));
	});
	addDiv.append(newTemplate);
	newTemplate.show();
}

// Fonction de suppression d'une annexe
function supprimerAnnexe(obj, annexeId) {
	$('#afficheAnnexe'+annexeId).removeClass();
	$('#afficheAnnexe'+annexeId).addClass('aSupprimer');
	var supAnnexe = $(document.createElement('input')).attr({
		id: 'supprimeAnnexe'+annexeId,
		name: 'data[AnnexesASupprimer]['+annexeId+']',
		type: 'hidden', value: annexeId});
	$('#supprimeAnnexes').append(supAnnexe);
	$(obj).hide();
	$(obj).next().show();
	$(obj).next().next().hide();
	$(obj).next().next().next().hide();
}

// Fonction de d'annulation de suppression d'une annexe
function annulerSupprimerAnnexe(obj, annexeId) {
	$('#afficheAnnexe'+annexeId).removeClass('aSupprimer');
	$('#supprimeAnnexe'+annexeId).remove();
	$(obj).hide();
	$(obj).prev().show();
	$(obj).next().show();
}

// Fonction de modification de l'annexe
function modifierAnnexe(obj, annexeId) {
	var trObj = $('#afficheAnnexe'+annexeId);
	trObj.find('span').each(function(){
		$(this).hide();
	});
	trObj.find('input').each(function(){
		$(this).removeAttr('disabled');
		$(this).show();
	});

	$('#afficheAnnexe'+annexeId).addClass('aModifier');

	$(obj).hide();
	$(obj).next().show();
	$(obj).prev().hide();
	$(obj).prev().prev().hide();
}

// Fonction d'annulation de la modification de l'annexe
function annulermodifierAnnexe(obj, annexeId) {
	$('#modifieAnnexeTitre'+annexeId).val($('#afficheAnnexeTitre'+annexeId).attr('valeur_init'));
	$('#modifieAnnexeCtrl'+annexeId).attr('checked', $('#afficheAnnexeCtrl'+annexeId).attr('valeur_init')==1);
	var trObj = $('#afficheAnnexe'+annexeId);
	trObj.find('span').each(function(){
		$(this).show();
	});
	trObj.find('input').each(function(){
		$(this).attr('disabled', 'disabled');
		$(this).hide();
	});

	trObj.removeClass('aModifier');

	$(obj).hide();
	$(obj).prev().show();
	$(obj).prev().prev().prev().show();
}

</script>