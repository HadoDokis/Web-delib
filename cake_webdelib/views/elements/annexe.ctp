<?php
// affichage des annexes
echo $html->tag('table', null, array('id'=>'listeAnnexes', 'cellpadding'=>'0', 'cellspacing'=>'0'));
	echo $html->tableHeaders(array('No', 'Nom du fichier', 'Titre', 'Joindre au  contrôle de légalité', 'Action'));
	if (isset($this->data['Annex'])) {
		foreach ($this->data['Annex'] as $rownum => $annexe) {
			echo $html->tag('tr', null, array('id'=>'afficheAnnexe'.$annexe['id']));
				echo $html->tag('td', $rownum+1);
				echo $html->tag('td', $html->link($annexe['filename'] ,'/annexes/download/'.$annexe['id']));
				echo $html->tag('td', $annexe['titre']);
				echo $html->tag('td', $annexe['joindre_ctrl_legalite']?'Oui':'Non');
				echo $html->tag('td', $html->link('Supprimer', "javascript:supprimerAnnexe(".$annexe['id'].")", null, 'Voulez-vous vraiment supprimer cette annexe ?\n\nNote : ne prendra effet que lors de la sauvegarde.\n'));
			echo $html->tag('/tr');
		}
	}
echo $html->tag('/table');
echo $html->tag('div', '', array('class'=>'spacer'));

// div pour la suppression des annexes
echo $html->tag('div', '', array('id'=>'annexeASupprimer'));

// template pour l'ajout des annexes
echo $html->tag('div', null, array('id'=>'ajouterAnnexeTemplate', 'style'=>'width:800px; display:none;'));
echo $html->tag('fieldset');
echo $html->tag('legend', 'Nouvelle annexe');
	echo $form->input('Annex.0.file', array('label'=>'Annexe<acronym title="obligatoire">(*)</acronym>', 'type'=>'file', 'size' => '80', 'disabled'=>'disabled'));
	echo $form->input('Annex.0.titre', array('label'=>'Titre', 'value'=>'', 'size' => '60', 'disabled'=>'disabled'));
	echo $form->input('Annex.0.ctrl', array('label'=>'Joindre ctrl légalité', 'type'=>'checkbox', 'value'=>false, 'disabled'=>'disabled'));
echo $html->tag('/fieldset');
echo $html->tag('/div');

// div pour l'ajout des annexes
echo $html->tag('div', '', array('id'=>'annexeAAjouter'));

// lien pour ajouter une nouvelle annexes
echo $html->tag('div', '', array('class'=>'spacer'));
echo $html->link('Ajouter une annexe', 'javascript:ajouterAnnexe()', array('class'=>'link_annexe'));
?>
<script>
// variables globales
var nbAnnexeAAjouter = 0;
var nbAnnexeASupprimer = 0;

// Fonction d'ajout d'une nouvelle annexe : duplique le div ajouteAnnexeTemplate et incrémente l'indexe
function ajouterAnnexe() {
	nbAnnexeAAjouter++; 
	var addDiv = $('#annexeAAjouter');
	var newTemplate = $('#ajouterAnnexeTemplate').clone();
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
function supprimerAnnexe(annexeId) {
	nbAnnexeASupprimer++;
	$('#afficheAnnexe'+annexeId).remove();
	var supAnnexe = $(document.createElement('input')).attr({
		id: 'AnnexesASupprimer'+nbAnnexeASupprimer,
		name: 'data[AnnexesASupprimer]['+nbAnnexeASupprimer+']',
		type: 'hidden', value: annexeId});
	$('#annexeASupprimer').append(supAnnexe);
	$('#listeAnnexes td:first-child').each(function(index){
		$(this).html(index+1);
	})
}

</script>