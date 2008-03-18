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

$webDelib = array(
	'menuClass' => array('menuNiveau0', 'menuNiveau1'),
	'currentItem' => 'menuCourant',
	'items' => array(
		'Accueil' => array('link' => '/'),
		'Projets' => array(
			'link' => '/deliberations/listerMesProjets',
			'subMenu' => array(
				'items' => array(
					'Nouveau...' => array('link' => '/deliberations/add'),
					'Mes projets' => array('link' => '/deliberations/listerMesProjets'),
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
					'Export GED' => array('link' => 'exportged')
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
					'Circuits' => array('link' => '/circuits/index'),
					'Services' => array('link' => '/services/index'),
					'Thèmes' => array('link' => '/themes/index'),
					'Types de séance' => array('link' => '/typeseances/index'),
					'Collectivité' => array('link' => '/collectivites/index'),
					'Générations' => array('link' => '/models/index'),
					'Localisations' => array('link' => '/localisations/index'),
					'Compteurs' => array('link' => '/compteurs/index')
					)
				)
			)
		)
	);
?>
