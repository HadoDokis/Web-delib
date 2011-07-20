<?php
/*
	Affiche une délib rattachée pour la vue view
	Paramètres :
		array	$delib : tableau de donnée de la délib rattachée
		array	$annexes : tableau des annexes
		string	$natureLibelle : libelle de la nature du projet
*/

/* Initialisation des paramètres */
if (empty($delib))
	return;

if ($delib['etat']==3 || $delib['etat']==5)
	echo $html->tag('h2', 'D&eacute;lib&eacute;ration n&deg; '.$delib['num_delib']);
else
	echo $html->tag('h2', 'Identifiant projet '.$natureLibelle.' : '.$delib['id']);
echo $html->tag('dt', 'Libellé');
echo $html->tag('dd', '&nbsp;'.$delib['objet']);

echo $this->element('viewTexte', array('type'=>'deliberation', 'delib'=>$delib));

if(!empty($annexes)) {
	echo '<dt>Annexes</dt>';
	echo '<dd><br>';
 	foreach ($annexes as $annexe) {
		if ($annexe['titre']) echo '<br>Titre : '.$annexe['titre'];
		echo '<br>Nom fichier : '.$annexe['filename'];
		echo '<br>Joindre au contrôle de légalité : '.($annexe['joindre_ctrl_legalite']?'oui':'non');
//		echo '<br>Taille : '.$annexe['size'];
		echo '<br>'.$html->link('Telecharger','/annexes/download/'.$annexe['id']).'<br>';
	}
	echo '</dd>';
}

?>
