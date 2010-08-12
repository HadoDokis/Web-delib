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
 *				'title' => str,				infobulle
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
			'title' => 'Projets que j\'ai cr��s ou qui sont dans mes circuits d\'�laboration et de validation',
			'subMenu' => array(
				'items' => array(
					'Nouveau...' => array('link' => '/deliberations/add'),
					'En cours de r�daction' => array(
						'link' => '/deliberations/mesProjetsRedaction',
						'title' => 'Projets que j\'ai cr��s'),
					'En cours de validation' => array(
						'link' => '/deliberations/mesProjetsValidation',
						'title' => 'Projets qui sont dans mes circuits d\'�laboration et de validation ou que j\'ai cr��s'),
					'A traiter' => array(
						'link' => '/deliberations/mesProjetsATraiter',
						'title' => 'Projets qui sont dans mes circuits d\'�laboration et de validation'),
					'Valid�s' => array(
						'link' => '/deliberations/mesProjetsValides',
						'title' => 'Projets qui sont dans mes circuits d\'�laboration et de validation ou que j\'ai cr��s'),
					'Recherches' => array(
						'link' => '/deliberations/mesProjetsRecherche',
						'title' => 'Parmi les projets qui sont dans mes circuits d\'�laboration et de validation ou que j\'ai cr��s')
					)
				)
			),
		'Tous les projets' => array(
			'link' => '/pages/tous_les_projets',
			'title' => 'Projets de tous les r�dacteurs',
			'subMenu' => array(
				'items' => array(
					'A attribuer' => array(
						'link' => '/deliberations/tousLesProjetsSansSeance',
						'title' => 'Projets en cours de r�daction ou d\'�laboration ou valid�s non associ�s � une s�ance'),
					'A valider' => array(
						'link' => '/deliberations/tousLesProjetsValidation',
						'title' => 'Projets en cours d\'�laboration et de validation'),
					'A faire voter' => array(
						'link' => '/deliberations/tousLesProjetsAFaireVoter',
						'title' => 'Projets valid�s associ�s � une s�ance'),
					'Recherches' => array(
						'link' => '/deliberations/tousLesProjetsRecherche',
						'title' => 'Parmi tous les projets')
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
			'link' => 'postseances',
			'subMenu' => array(
				'items' => array(
					'Editions' => array('link' => '/postseances/index'),
					'Signature : i-Parapheur' => array('link' => '/deliberations/sendToParapheur'),
					'Contr�le de l�galit� : � t�l�transmettre' => array('link' => '/deliberations/toSend'),
					'Contr�le de l�galit� : t�l�transmises' => array('link' => '/deliberations/transmit'),
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
					'Collectivit�' => array('link' => '/collectivites/index'),
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
