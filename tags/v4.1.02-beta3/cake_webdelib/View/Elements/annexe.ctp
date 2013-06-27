<?php
/*
	Gestion des annexes : affichage, modification, suppression
	Paramètres :
		string	$mode = 'edit' : mode édition ('edit') ou affichage ('display'), dans ce dernier cas, les autres paramètres ne sont pas ovligatoires
		string	$ref : référence d'appartenance des annexes pour les nouvelles annexes (delibPrincipale, delibRattachee1, delibRattachee2, ...)
		array	$annexes = array() : liste des annexes a afficher
		boolean	$affichage = 'complet' : 'complet', affiche tout, y compris le javascript, 'partiel' affiche uniquement le nécessaire pour ne pas répéter des élements du dom
*/

// Initialisation des paramètres
if (empty($mode)) $mode = 'edit';
if ($mode == 'edit' && empty($ref)) return; 
if (empty($annexes)) $annexes = array();
if (empty($affichage)) $affichage = 'complet';

// affichage des annexes
$tableOptions = array('cellpadding'=>'0', 'cellspacing'=>'0', 'width'=> '100%');
if ($mode == 'edit') $tableOptions['id'] = 'tableAnnexe'.$ref;
echo $this->Html->tag('table', null, $tableOptions);
if ($mode == 'edit')
	echo $this->Html->tableHeaders(array('No', 'Nom du fichier', 'Titre', 'Joindre au  contrôle de légalité', 'Joindre à la fusion', 'Action'));
else
	echo $this->Html->tableHeaders(array('No', 'Nom du fichier', 'Titre', 'Joindre au  contrôle de légalité', 'Joindre à la fusion'));
if (isset($annexes)) {
	foreach ($annexes as $rownum => $annexe) {
		$rowClass = array();
		if ($rownum & 1) $rowClass['class'] = 'altrow';
		if ($mode == 'edit') {
			$rowClass['height'] = '36px';
			$rowClass['id'] = 'afficheAnnexe'.$annexe['id'];
			$rowClass['annexe_id'] = $annexe['id'];
		}
		echo $this->Html->tag('tr', null,  $rowClass);
			echo $this->Html->tag('td', $rownum+1);
			echo $this->Html->tag('td');
				$pos = strpos($annexe['filetype'], 'vnd.oasis.opendocument');
				if ($mode == 'edit') {
                                        // lien de téléchargement de la version pdf de l'annexe
					if ($annexe['filetype']=='application/pdf'){
                                            echo $this->Html->tag('span', $this->Html->link($annexe['filename_pdf'], '/annexes/download/'.$annexe['id'].'/1', array('title'=>'Télécharger le fichier')));
                                            echo $this->Html->tag('span', ' '.$this->Html->link('(Aperçu odt)', '/annexes/download/'.$annexe['id'], array('title'=>'Télécharger le fichier')));
                                        }
                                        else
                                        {
                                        // lien de téléchargement de l'annexe
					echo $this->Html->tag('span', $this->Html->link($annexe['filename'], '/annexes/download/'.$annexe['id'], array('title'=>'Télécharger le fichier')));
                                        }
                                        // lien de modification de l'annexe en webdav si texte opendocument
					if ($pos !== false) {
						$url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$annexe['foreign_key']."/".$annexe['filename'];
						echo  $this->Html->link('modifier : '.$annexe['filename'] , $url, array(
							'id'=>'urlWebdavAnnexe'.$annexe['id'],
							'style'=>'display:none;',
							'title'=>'Modifier le fichier'));
					}
				} else {
					// nom de l'annexe
					echo $this->Html->tag('span', $annexe['filename']);
					// nom de la version pdf de l'annexe
					if (($pos !== false) && (strlen($annexe['filename_pdf']) > 0)) 
						echo $this->Html->tag('span', ' '.'version pdf : '.$annexe['filename_pdf']);
				}
			echo $this->Html->tag('/td');
			echo $this->Html->tag('td');
				if ($mode == 'edit') {
					echo $this->Html->tag('span', $annexe['titre'], array(
						'id' => 'afficheAnnexeTitre'.$annexe['id'],
						'valeur_init' => $annexe['titre']));
					echo $this->Form->input('AnnexesAModifier.'.$annexe['id'].'.titre', array(
						'id' => 'modifieAnnexeTitre'.$annexe['id'],
						'label' => false,
						'value' => $annexe['titre'],
						'size' => '200',
						'disabled'=>'disabled',
						'style'=>'display:none;'));
				} else
					echo $this->Html->tag('span', $annexe['titre']);
			echo $this->Html->tag('/td');

			echo $this->Html->tag('td');
				if ($mode == 'edit') {
					echo $this->Html->tag('span', $annexe['joindre_ctrl_legalite']?'Oui':'Non', array(
						'id' => 'afficheAnnexeCtrl'.$annexe['id'],
						'valeur_init' => $annexe['joindre_ctrl_legalite']));
					echo $this->Form->input('AnnexesAModifier.'.$annexe['id'].'.joindre_ctrl_legalite', array(
						'id'=>'modifieAnnexeCtrl'.$annexe['id'],
						'label'=>false,
						'type'=>'checkbox',
						'checked'=>($annexe['joindre_ctrl_legalite']==1),
						'disabled'=>'disabled',
						'style'=>'display:none;'));
				} else
					echo $this->Html->tag('span', $annexe['joindre_ctrl_legalite']?'Oui':'Non');
			echo $this->Html->tag('/td');
                        echo $this->Html->tag('td');
                                if ($mode == 'edit') {
                                        echo $this->Html->tag('span', $annexe['joindre_fusion']?'Oui':'Non', array(
                                                'id' => 'afficheAnnexeCtrl'.$annexe['id'],
                                                'valeur_init' => $annexe['joindre_fusion']));
                                        echo $this->Form->input('AnnexesAModifier.'.$annexe['id'].'.joindre_fusion', array(
                                                'id'=>'modifieAnnexeFusion'.$annexe['id'],
                                                'label'=>false,
                                                'type'=>'checkbox',
                                                'checked'=>($annexe['joindre_fusion']==1),
                                                'disabled'=>'disabled',
                                                'style'=>'display:none;'));
                                } else
                                        echo $this->Html->tag('span', $annexe['joindre_fusion']?'Oui':'Non');
                        echo $this->Html->tag('/td');

			if ($mode == 'edit') {
				echo $this->Html->tag('td');
					echo $this->Html->link(SHY, '#', array('title'=>'Supprimer', 
                                                                               'class'=>"link_supprimer", 
                                                                               'escape' => false, 
                                                                               'onClick'=>'supprimerAnnexe(this, '.$annexe['id'].')'), false);
					echo $this->Html->link(SHY, '#', array('title'=>'Annuler la suppression', 
                                                                               'class'=>'link_supprimer_back', 
                                                                               'escape' => false, 
                                                                               'onClick'=>"annulerSupprimerAnnexe(this, ".$annexe['id'].")", 
                                                                               'style'=>'display: none;'), false);
					echo '&nbsp;&nbsp;';
					echo $this->Html->link(SHY, '#', array('title'=>'Modifier', 
                                                                               'escape' => false, 
                                                                               'class'=> 'link_modifier', 
                                                                               'onClick'=>'modifierAnnexe(this, '.$annexe['id'].')'), false);
					echo $this->Html->link(SHY, '#', array('title'=>'Annuler la modification', 
                                                                               'class'=> 'link_modifier_back', 
                                                                               'escape' => false, 
                                                                               'onClick'=>"annulerModifierAnnexe(this, ".$annexe['id'].")", 
                                                                               'style'=>'display: none;'), false);
				echo $this->Html->tag('/td');
			}
		echo $this->Html->tag('/tr');
	}
}
echo $this->Html->tag('/table');
echo $this->Html->tag('div', '', array('class'=>'spacer'));

if ($mode != 'edit') return;

// div pour la suppression des annexes
if ($affichage == 'complet')
	echo $this->Html->tag('div', '', array('id'=>'supprimeAnnexes'));

// template pour l'ajout des annexes
if ($affichage == 'complet') {
	echo $this->Html->tag('div', null, array('id'=>'ajouteAnnexeTemplate', 'style'=>'width:800px; display:none;'));
		echo $this->Html->tag('fieldset');
		echo $this->Html->tag('legend', 'Nouvelle annexe');
			echo $this->Form->hidden('Annex.0.ref', array('disabled'=>'disabled'));
			echo $this->Form->input('Annex.0.file', array('label'=>'Annexe<acronym title="obligatoire">(*)</acronym>', 'type'=>'file', 'size' => '80', 'disabled'=>'disabled'));
			echo $this->Html->tag('div', '', array('class'=>'spacer'));
			echo $this->Form->input('Annex.0.titre', array('label'=>'Titre', 'value'=>'', 'size' => '200', 'disabled'=>'disabled'));
			echo $this->Html->tag('div', '', array('class'=>'spacer'));
			echo $this->Form->input('Annex.0.ctrl', array('label'=>'Joindre&nbsp;ctrl&nbsp;légalité', 'type'=>'checkbox', 'value'=>false, 'disabled'=>'disabled'));
			echo $this->Html->tag('div', '', array('class'=>'spacer'));
			echo $this->Form->input('Annex.0.fusion', array('label'=>'Joindre&nbsp;fusion', 'type'=>'checkbox', 'value'=>false, 'disabled'=>'disabled'));
			echo $this->Html->tag('div', '', array('class'=>'spacer'));
			echo $this->Html->link('Annuler', '#self', array('class'=>'btn btn-link', 'onClick'=>'javascript:$(this).parent().parent().remove();'));
		echo $this->Html->tag('/fieldset');
	echo $this->Html->tag('/div');
}

// div pour l'ajout des annexes
echo $this->Html->tag('div', '', array('id'=>'ajouteAnnexes'.$ref));

// lien pour ajouter une nouvelle annexes
echo $this->Html->tag('div', '', array('class'=>'spacer'));
echo $this->Html->link('Ajouter une annexe', 'javascript:ajouterAnnexe(\''.$ref.'\')', array('class'=>'link_annexe'));

if ($affichage != 'complet') return;
?>
<script>
// variables globales
var nbAnnexeAAjouter = 0;

// Fonction d'ajout d'une nouvelle annexe : duplique le div ajouteAnnexeTemplate et incrémente l'indexe
function ajouterAnnexe(ref) {
	nbAnnexeAAjouter++;
	var addDiv = $('#ajouteAnnexes'+ref);
	var newTemplate = $('#ajouteAnnexeTemplate').clone();
	newTemplate.attr('id', newTemplate.attr('id').replace('Template', nbAnnexeAAjouter));
	newTemplate.find('#Annex0Ref').val(ref);
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
	$('#urlWebdavAnnexe'+annexeId).show();

	$('#afficheAnnexe'+annexeId).addClass('aModifier');

	$(obj).hide();
	$(obj).next().show();
	$(obj).prev().hide();
	$(obj).prev().prev().hide();
}

// Fonction d'annulation de la modification de l'annexe
function annulerModifierAnnexe(obj, annexeId) {
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
	$('#urlWebdavAnnexe'+annexeId).hide();

	trObj.removeClass('aModifier');

	$(obj).hide();
	$(obj).prev().show();
	$(obj).prev().prev().prev().show();
}

</script>
