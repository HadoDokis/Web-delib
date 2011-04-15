<?php
/*
	Affiche les liens pour la gestion des onglets
	Paramètres :
		$listeOnglets=array() : liste des noms des onglets (obligatoire)
		$nOngletCourant : numéro de l'onglet courant (1 par défaut)
		$classeOngletCourant : nom de la classe de l'onglet courant ('ongletCourant' par défaut)
*/
// Initialisations
if (!isset($listeOnglets)) return;
$nOngletCourant = isset($nOngletCourant)? $nOngletCourant : 1;
$classeOngletCourant = isset($classeOngletCourant)? $classeOngletCourant : 'ongletCourant';

echo $html->div('onglet');
	echo $html->tag('a');
	foreach($listeOnglets as $i => $nomOnglet) {
		$nOnglet = $i+1;
		$htmlOptions = array('id' => 'lienTab'.$nOnglet);
		if ($nOnglet == $nOngletCourant) $htmlOptions['class'] = $classeOngletCourant;

		echo $html->link($nomOnglet, "javascript:afficheOnglet($nOnglet)", $htmlOptions, false, false);
	}
echo '</div>';
?>
