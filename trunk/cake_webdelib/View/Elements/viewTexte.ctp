<?php
/*
	Affiche le texte d'une délib
	Paramètres :
		string	type : type de texte 'projet', 'synthese', 'deliberation'
		string	$delib : delib contenant les textes
*/

/* Initialisation des paramètres */
if (empty($type))
	return;

switch($type) {
	case 'projet' :
		$libelle = 'Texte projet';
		$textKey = 'texte_projet';
		$filename = $delib['texte_projet_name'];
		break;
	case 'synthese' :
		$libelle = 'Note synth&egrave;se';
		$textKey = 'texte_synthese';
		$filename = $delib['texte_synthese_name'];
		break;
	case 'deliberation' :
		$libelle = 'Texte d&eacute;lib&eacute;ration';
		$textKey = 'deliberation';
		$filename = $delib['deliberation_name'];
}

echo $html->tag('dt', $libelle);
echo $html->tag('dd');
	if (Configure::read('GENERER_DOC_SIMPLE')){
		if (!empty($delib[$textKey])) {
			echo $html->link('[Afficher le texte]', 'javascript:afficheMasqueTexteEnrichi(\'afficheMasque'.$type.$delib['id'].'\', \''.$type.$delib['id'].'\')', array(
				'id'=> 'afficheMasque'.$type.$delib['id'], 'affiche'=>'masque'));
			echo '<div class="annexesGauche"></div>';
			echo '<div class="fckEditorProjet">';
				echo $form->input($type.$delib['id'], array('label'=>'', 'type'=>'textarea', 'style'=>'display:none;', 'value'=>$delib[$textKey]));
			echo '</div>';
			echo '<div class="spacer"></div>';
		}
	} else {
		if (!empty($delib[$textKey])) {
			echo $html->link($filename, array('action'=>'download', $delib['id'], $textKey));
	    }
	}
echo $html->tag('/dd');
?>
