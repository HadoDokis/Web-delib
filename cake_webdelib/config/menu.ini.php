<?php
/*
 * Fichier de description des menus
 *
 *	$menuVar = array(
 *		'menuClass'' => str|array(str),		classe du menu
 *		'itemTag' => str|array(str),		defaut = 'li', nom de la balise des �l�ments du menu
 *		'currentItem' => str|array(str),	nom de la classe utilis�e pour l'�l�ment courant du menu
 *		'items' => array(					liste des �l�ments du menu
 *			str => array(					nom affich� de l'�l�ment du menu
 *				'link' => str,				lien cake du style /nomContoleur/index
 *				'subMenu' => array()		sous-menu qui a la m�me structure que le menu
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
			'subMenu' => array(
				'items' => array(
					'Nouveau...' => array('link' => '/deliberations/add'),
					'Refus�s' => array('link' => '/deliberations/mesProjetsRefuses'),
					'En cours de r�daction' => array('link' => '/deliberations/mesProjetsRedaction'),
					'En cours de validation' => array('link' => '/deliberations/mesProjetsValidation'),
					'Valid�s' => array('link' => '/deliberations/mesProjetsValides'),
					'Vot�s et adopt�s' => array('link' => '/deliberations/mesProjetsVotesOui'),
					'Vot�s et non adopt�s' => array('link' => '/deliberations/mesProjetsVotesNon'),
					'Transmis au contr�le de l�galit�' => array('link' => '/deliberations/mesProjetsTransmis'),
					'A traiter' => array('link' => '/deliberations/listerProjetsATraiter'),
					'Dans mes circuits' => array('link' => '/deliberations/listerProjetsDansMesCircuits')
					)
				)
			),
		'Tous les projets' => array(
			'link' => '/pages/tous_les_projets',
			'subMenu' => array(
				'items' => array(
					'A attribuer' => array('link' => '/deliberations/listerProjetsNonAttribues'),
					'A faire voter' => array('link' => '/deliberations/listerProjetsServicesAssemblees')
					)
				)
			),
		'S�ances' => array(
			'link' => '/seances/listerFuturesSeances',
			'subMenu' => array(
				'items' => array(
					'Nouvelle...' => array('link' => '/seances/add'),
					'Pass�es' => array('link' => '/seances/listerAnciennesSeances'),
					'Calendrier' => array('link' => '/seances/afficherCalendrier')
					)
				)
			),
		'Post-s�ance' => array(
			'link' => 'postseance',
			'subMenu' => array(
				'items' => array(
					'Editions' => array('link' => '/postseances/index'),
					'Contr�le de l�galit�' => array('link' => '/deliberations/transmit'),
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
					'Collectivit�' => array('link' => '/collectivites/index'),
					'Localisations' => array('link' => '/localisations/index'),
					'Th�mes' => array('link' => '/themes/index'),
					'Mod�les d\'�dition' => array('link' => '/models/index'),
					'S�quences' => array('link' => '/sequences/index'),
					'Compteurs' => array('link' => '/compteurs/index'),
					'Types de s�ance' => array('link' => '/typeseances/index'),
					'Informations sup.' => array('link' => '/infosupdefs/index')
					)
				)
			)
		)
	);
?>
