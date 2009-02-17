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
 *				'subMenu' => array()		sous-menu qui a la même structure que le menu
 *			)
 *		)
 *	)
 *
 */

$OldwebDelib = array(
	'menuClass' => array('menuNiveau0', 'menuNiveau1'),
	'currentItem' => 'menuCourant',
	'items' => array(
		'Accueil' => array('link' => '/'),
		'Projets' => array(
			'link' => '/deliberations/listerMesProjets',
			'subMenu' => array(
				'items' => array(
					'Historique' => array('link' => '/deliberations/listerHistorique'),
					'Nouveau...' => array('link' => '/deliberations/add'),
					'A attribuer' => array('link' => '/deliberations/listerProjetsNonAttribues'),
					'A traiter' => array('link' => '/deliberations/listerProjetsATraiter'),
					'A faire voter' => array('link' => '/deliberations/listerProjetsServicesAssemblees')
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
			'link' => 'postseance',
			'subMenu' => array(
				'items' => array(
					'Editions' => array('link' => '/postseances/index'),
					'Contrôle de légalité' => array('link' => '/deliberations/transmit'),
					'Export GED/Intranet' => array('link' => 'exportged')
					)
				)
			),
		'Administration' => array(
			'link' => 'administration',
			'subMenu' => array(
				'items' => array(
					'Utilisateurs' => array('link' => '/users/index'),
					'Profils' => array('link' => '/profils/index'),
					'Droits' => array('link' => '/droits/edit'),
					'Acteurs' => array('link' => 'acteurs_typeacteurs'),
					'Circuits' => array('link' => '/circuits/index'),
					'Services' => array('link' => '/services/index'),
					'Thèmes' => array('link' => '/themes/index'),
					'Types de séance' => array('link' => '/typeseances/index'),
					'Collectivité' => array('link' => '/collectivites/index'),
					'Générations' => array('link' => '/models/index'),
					'Localisations' => array('link' => '/localisations/index'),
					'Compteurs' => array('link' => 'compteurs_sequences')
					)
				)
			)
		)
	);

$webDelib = array(
	'menuClass' => array('menuNiveau0', 'menuNiveau1'),
	'currentItem' => 'menuCourant',
	'items' => array(
		'Accueil' => array('link' => '/'),
		'Projets' => array(
			'link' => '/deliberations/listerMesProjets',
			'subMenu' => array(
				'items' => array(
					'Historique' => array('link' => '/deliberations/listerHistorique'),
					'Nouveau...' => array('link' => '/deliberations/add'),
					'A attribuer' => array('link' => '/deliberations/listerProjetsNonAttribues'),
					'A traiter' => array('link' => '/deliberations/listerProjetsATraiter'),
					'A faire voter' => array('link' => '/deliberations/listerProjetsServicesAssemblees')
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
			'link' => 'postseance',
			'subMenu' => array(
				'items' => array(
					'Editions' => array('link' => '/postseances/index'),
					'Contrôle de légalité' => array('link' => '/deliberations/transmit'),
					'Export GED/Intranet' => array('link' => 'exportged')
					)
				)
			),
		'Recherches' => array(
			'link' => '/deliberations/rechercheMutliCriteres',
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
					'Localisations' => array('link' => '/localisations/index'),
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
