<?php
/*
	Affiche une d�lib rattach�e pour la vue view
	Param�tres :
		array	$delib : tableau de donn�e de la d�lib rattach�e
		array	$annexes : tableau des annexes
		string	$natureLibelle : libelle de la nature du projet
*/

/* Initialisation des param�tres */
if (empty($delib))
	return;

if ($delib['etat']==3 || $delib['etat']==5)
	echo $html->tag('h2', 'D&eacute;lib&eacute;ration n&deg; '.$delib['num_delib']);
else
	echo $html->tag('h2', 'Identifiant projet '.$natureLibelle.' : '.$delib['id']);
echo $html->tag('dt', 'Libell�');
echo $html->tag('dd', '&nbsp;'.$delib['objet_delib']);

echo $this->element('viewTexte', array('type'=>'deliberation', 'delib'=>$delib));

if(!empty($annexes)) {
	echo '<dt>Annexes</dt>';
	echo '<dd><br>';
 	foreach ($annexes as $annexe) {
		if ($annexe['titre']) echo '<br>Titre : '.$annexe['titre'];
		echo '<br>Nom fichier : '.$annexe['filename'];
		echo '<br>Joindre au contr�le de l�galit� : '.($annexe['joindre_ctrl_legalite']?'oui':'non');
//		echo '<br>Taille : '.$annexe['size'];
		echo '<br>'.$html->link('Telecharger','/annexes/download/'.$annexe['id']).'<br>';
	}
	echo '</dd>';
}

?>
