<?php
/*
 * Fichier de description des menus
 *
 *	$menuVar = array(
 *		'menuClass'' => str|array(str),		classe du menu
 *		'itemTag' => str|array(str),		defaut = 'li', nom de la balise des éléments du menu
 *		'currentItem' => str|array(str),	nom de la classe utilisée pour l'élément courant du menu
 *		'items' => array(					liste des éléments du menu
 *			str => array(					nom affiché de l'élément du menu
 *				'link' => str,				lien cake du style /nomContoleur/index
 *				'title' => str,				infobulle
 *				'subMenu' => array()		sous-menu qui a la même structure que le menu
 *			)
 *		)
 *	)
 *
 */

$webDelib = array(
	'menuClass' => array('menuNiveau0', 'menuNiveau1'),
	'currentItem' => 'menuCourant',
	'items' => array(
		'Accueil' => array('link' => '/'),
		'Mes projets' => array(
			'link' => 'mes_projets',
			'title' => 'Projets que j\'ai créés ou qui sont dans mes circuits d\'élaboration et de validation',
			'subMenu' => array(
				'items' => array(
					'Nouveau...' => array('link' => '/deliberations/add'),
					'En cours de rédaction' => array(
						'link' => '/deliberations/mesProjetsRedaction',
						'title' => 'Projets que j\'ai créés'),
					'En cours de validation' => array(
						'link' => '/deliberations/mesProjetsValidation',
						'title' => 'Projets qui sont dans mes circuits d\'élaboration et de validation ou que j\'ai créés'),
					'A traiter' => array(
						'link' => '/deliberations/mesProjetsATraiter',
						'title' => 'Projets qui sont dans mes circuits d\'élaboration et de validation'),
					'Validés' => array(
						'link' => '/deliberations/mesProjetsValides',
						'title' => 'Projets qui sont dans mes circuits d\'élaboration et de validation ou que j\'ai créés'),
					'Recherches' => array(
						'link' => '/deliberations/mesProjetsRecherche',
						'title' => 'Parmi les projets qui sont dans mes circuits d\'élaboration et de validation ou que j\'ai créés')
					)
				)
			),
		'Tous les projets' => array(
			'link' => '/pages/tous_les_projets',
			'title' => 'Projets de tous les rédacteurs',
			'subMenu' => array(
				'items' => array(
					'A attribuer' => array(
						'link' => '/deliberations/tousLesProjetsSansSeance',
						'title' => 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance'),
					'A valider' => array(
						'link' => '/deliberations/tousLesProjetsValidation',
						'title' => 'Projets en cours d\'élaboration et de validation'),
					'A faire voter' => array(
						'link' => '/deliberations/tousLesProjetsAFaireVoter',
						'title' => 'Projets validés associés à une séance'),
					'Recherches' => array(
						'link' => '/deliberations/tousLesProjetsRecherche',
						'title' => 'Parmi tous les projets')
					)
				)
			),
		'Séances' => array(
			'link' => '/seances/listerFuturesSeances',
			'subMenu' => array(
				'items' => array(
					'Nouvelle...' => array('link' => '/seances/add'),
					'Passées' => array('link' => '/seances/listerAnciennesSeances'),
					'Calendrier' => array('link' => '/seances/afficherCalendrier')
					)
				)
			),
		'Post-séance' => array(
			'link' => 'postseances',
			'subMenu' => array(
				'items' => array(
					'Editions' => array('link' => '/postseances/index'),
					'Signature : i-Parapheur' => array('link' => '/deliberations/sendToParapheur'),
					'Contrôle de légalité : à télétransmettre' => array('link' => '/deliberations/toSend'),
					'Contrôle de légalité : télétransmises' => array('link' => '/deliberations/transmit'),
					'Versement As@lae' => array('link' => '/deliberations/verserAsalae')
					)
				)
			),
		'Utilisateurs' => array(
			'link' => 'gestion_utilisateurs',
			'subMenu' => array(
				'items' => array(
					'Profils' => array('link' => '/profils/index'),
					'Droits' => array('link' => '/droits/edit'),
					'Services' => array('link' => '/services/index'),
					'Utilisateurs' => array('link' => '/users/index'),
					'Circuits' => array('link' => '/circuits/index')
					)
				)
			),
		'Acteurs' => array(
			'link' => 'gestion_acteurs',
			'subMenu' => array(
				'items' => array(
					'Type d\'acteurs' => array('link' => '/typeacteurs/index'),
					'Acteurs' => array('link' => '/acteurs/index')
					)
				)
			),
		'Administration' => array(
			'link' => 'administration',
			'subMenu' => array(
				'items' => array(
					'Collectivité' => array('link' => '/collectivites/index'),
					'Thèmes' => array('link' => '/themes/index'),
					'Modèles d\'édition' => array('link' => '/models/index'),
					'Séquences' => array('link' => '/sequences/index'),
					'Compteurs' => array('link' => '/compteurs/index'),
					'Types de séance' => array('link' => '/typeseances/index'),
					'Informations sup.' => array('link' => '/infosupdefs/index')
					)
				)
			)
		)
	);
?>
