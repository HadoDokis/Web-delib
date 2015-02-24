<?php
/*
 * Fichier de description des menus
 *
 *	$navbar = array(
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

$navbar = array(
    'Mes projets'=>array(
        'title' => 'Projets que j\'ai créés ou qui sont dans mes circuits d\'élaboration et de validation',
        'subMenu' => array(
                array(
                    'html' => 'link',
                    'libelle' => 'Nouveau',
                    'check'=> array('Deliberations', 'create'),
                    'title'=> 'Crée un nouveau projet',
                    'icon' => 'plus',
                    'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'add')
                ),
                array(
                    'html' => 'link',
                    'libelle' => 'En cours de rédaction',
                    'check'=> array('Deliberations/mesProjetsRedaction'),
                    'title'=> 'Projets que j\'ai créés',
                    'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsRedaction')
                ),
                array(
                    'html' => 'link',
                    'libelle' => 'Validés',
                    'check'=> array('Deliberations/mesProjetsValides'),
                    'title'=> 'Projets qui sont dans mes circuits d\'élaboration et de validation ou que j\'ai créés',
                    'icon' => 'check-square-o',
                    'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsValides')
                ),
                array(
                    'html' => 'link',
                    'libelle' => 'A traiter',
                    'check'=> array('Deliberations/mesProjetsATraiter'),
                    'title'=> 'Projets qui sont dans mes circuits d\'élaboration et de validation',
                    'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsATraiter')
                ),
                array('html' => 'divider'),
                array(
                    'html' => 'link',
                    'libelle' => 'Mon service',
                    'check'=> array('Deliberations/projetsMonService'),
                    'title'=> 'Projets rédigés par mon service',
                    'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'projetsMonService')
                ),
                array('html' => 'divider'),
                array(
                    'html' => 'link',
                    'libelle' => 'Rechercher',
                    'check'=> array('Deliberations/mesProjetsRecherche'),
                    'title'=> 'Parmi les projets qui sont dans mes circuits d\'élaboration et de validation ou que j\'ai créés',
                    'icon' => 'search',
                    'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'mesProjetsRecherche')
                ),
            )
    ),
    'Tous les projets'=>array(
        'title' => 'Projets de tous les rédacteurs',
        'subMenu' => array(
            array(
                'html' => 'subMenu',
                'content' =>array(
                    'Délibérations' => array(
                        'title' => 'Projets de type délibération de tous les rédacteurs',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'A attibuer',
                                'check'=> array('Deliberations/tousLesProjetsSansSeance'),
                                'title'=> 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autresActesAValider')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'A valider',
                                'check'=> array('Deliberations/tousLesProjetsValidation'),
                                'title'=> 'Projets en cours d\'élaboration et de validation',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsValidation')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'A faire voter',
                                'check'=> array('Deliberations/tousLesProjetsAFaireVoter'),
                                'title'=> 'Projets validés associés à une séance',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsAFaireVoter')
                            ),
                        )
                    )
                )
            ),
            array(
                'html' => 'subMenu',
                'content' =>array(
                    'Autres actes...' => array(
                        'title' => 'Projets de type délibération de tous les rédacteurs',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'A valider',
                                'check'=> array('Deliberations/autresActesAValider'),
                                'title'=> 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autresActesAValider')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Validés',
                                'check' => array('Deliberations/autreActesValides'),
                                'title' => 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance',
                                'icon' => 'check-square-o',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesValides')
                            ),
                            array('html' => 'divider'),
                            array(
                                'html' => 'link',
                                'libelle' => 'A télétranmettre',
                                'check'=> array('Deliberations/autreActesAEnvoyer'),
                                'title'=> 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance',
                                'icon' => 'envelope',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesAEnvoyer')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Télétranmis',
                                'check'=> array('Deliberations/autreActesEnvoyes'),
                                'title'=> 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance',
                                'icon' => 'institution',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'autreActesEnvoyes')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Non Transmis',
                                'check'=> array('Deliberations/nonTransmis'),
                                'title'=> 'Projets en cours de rédaction ou d\'élaboration ou validés non associés à une séance',
                                'icon' => 'hdd-o',
                                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'nonTransmis')
                            ),
                        )
                    )
                )
            ),
            array('html' => 'divider'),
            array(
                'html' => 'link',
                'libelle' => 'Rechercher',
                'check'=> array('Deliberations/tousLesProjetsRecherche'),
                'title'=> 'Rechercher parmi tous les projets',
                'icon' => 'search',
                'url' => array('admin' => false, 'plugin'=>null, 'controller'=>'deliberations', 'action'=>'tousLesProjetsRecherche')
            ),
        )
    ),
    'Seances'=>array(
    'title' => '',//FIX
    'subMenu' => array(
            array(
                'html' => 'link',
                'libelle' => 'Nouvelle',
                'check'=> array('Seances', 'create'),
                'title'=> 'Créer une nouvelle projet',
                'icon' => 'plus',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'seances', 'action' => 'add')
            ),
            array(
                'html' => 'link',
                'libelle' => 'A traiter',
                'check'=> array('Seances', 'read'),
                'title'=> 'Créer une nouvelle projet',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'seances', 'action' => 'index')
            ),
            array(
                'html' => 'link',
                'libelle' => 'Passées',
                'check'=> array('Seances', 'read'),
                'title'=> 'Créer une nouvelle projet',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'seances', 'action' => 'listerAnciennesSeances')
            ),
            array(
                'html' => 'link',
                'libelle' => 'Calendrier',
                'check'=> array('Seances/afficherCalendrier'),
                'title'=> 'Calendrier des séances',
                'icon' => 'calendar',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'seances', 'action' => 'afficherCalendrier')
            ),
        )
    ),
    'Post-séances'=>array(
    'title' => '',//FIX
    'subMenu' => array(
            array(
                'html' => 'link',
                'libelle' => 'Editions',
                'check'=> array('Postseances', 'read'),
                'title'=> 'Editions des séances passées',
                'icon' => 'plus',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'postseances', 'action' => 'index')
            ),
            array('html' => 'divider'),
            array(
                'html' => 'link',
                'libelle' => 'Signatures',
                'check'=> array('Deliberations/sendToParapheur'),
                'title'=> 'Signature des délibérations',
                'icon' => 'certificate',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'Deliberations', 'action' => 'sendToParapheur')
            ),
            array('html' => 'divider'),
            array(
                'html' => 'link',
                'libelle' => 'A télétransmettres',
                'check'=> array('Deliberations/toSend'),
                'title'=> 'Envoi des délibérations au contrôle de légalité',
                'icon' => 'envelope',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'Deliberations', 'action' => 'toSend')
            ),
            array(
                'html' => 'link',
                'libelle' => 'Télétransmises',
                'check'=> array('Deliberations/transmit'),
                'title'=> 'Délibérations télétransmises au contrôle de légalité',
                'icon' => 'institution',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'Deliberations', 'action' => 'transmit')
            ),
            array('html' => 'divider'),
            array(
                'html' => 'link',
                'libelle' => 'Versement SAE',
                'check'=> array('Deliberations/sendToSae'),
                'title'=> 'Envoi des délibérations au SAE',
                'icon' => 'archive',
                'url' => array('admin' => false, 'plugin' => null, 'controller' => 'Deliberations', 'action' => 'sendToSae')
            ),
        )
    ),
    'Administration'=>array(
    'title' => 'Administration de l\'application',//FIX
    'subMenu' => array(
            array(
                'html' => 'subMenu',
                'content' =>array(
                    'Générale' => array(
                        'title' => 'Administration Générale',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'Collectivites',
                                'check'=> array('Collectivites'),
                                'title'=> 'Informations sur la collectivité',
                                'icon' => 'building-o',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'collectivites', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Thèmes',
                                'check'=> array('Themes'),
                                'title'=> 'Informations sur la collectivité',
                                'icon' => 'bookmark',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'themes', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Modèles d\'édition',
                                'check'=> array('modelOdtValidator/modeltemplates'),
                                'title'=> 'Informations sur la collectivité',
                                'icon' => 'book',
                                'url' => array('admin' => true, 'plugin'=>'model_odt_validator', 'controller'=>'modeltemplates', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Séquences',
                                'check'=> array('Sequences'),
                                'title'=> 'Séquences des compteurs',
                                'icon' => 'list-ol',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'sequences', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Compteurs',
                                'check'=> array('Compteurs'),
                                'title'=> 'Compteurs des types d\'actes',
                                'icon' => 'dashboard',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'compteurs', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Compteurs',
                                'check'=> array('Compteurs'),
                                'title'=> 'Gestion des types d\'actes',
                                'icon' => 'book',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'typeactes', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Types de séance',
                                'check'=> array('Typeseance'),
                                'title'=> 'Gestion des types de séance',
                                'icon' => 'leanpub',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'typeactes', 'action'=>'index')
                            ),
                        )
                    ),
                    'Utilisateurs' => array(
                        'title' => 'Administration des utilisateurs',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'Utilisateurs',
                                'check'=> array('Users', 'create'),
                                'title'=> 'Gestion des utilisateurs',
                                'icon' => 'user',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'users', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Profils',
                                'check'=> array('Profils', 'create'),
                                'title'=> 'Gestion des profils',
                                'icon' => 'users',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'profils', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Services',
                                'check'=> array('Services', 'create'),
                                'title'=> 'Gestion des services',
                                'icon' => 'sitemap',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'services', 'action'=>'index')
                            ),
                            array(
                                'html' => 'link',
                                'libelle' => 'Circuits',
                                'check'=> array('Profils', 'create'),
                                'title'=> 'Informations sur la collectivité',
                                'icon' => 'road',
                                'url' => array('admin' => true, 'plugin'=> 'Cakeflow', 'controller'=>'circuits', 'action'=>'index')
                            ),
                        )
                    ),
                    'Acteurs' => array(
                        'title' => 'Administration des acteurs',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'Types',
                                'check'=> array('Acteurs'),
                                'title'=> 'Types des Acteurs',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'typeacteurs', 'action'=>'index')
                            ),
                            array(
                                'html' => 'Liste',
                                'libelle' => 'Profils',
                                'check'=> array('Acteurs'),
                                'title'=> 'Gestion des acteurs',
                                'icon' => 'list',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'acteurs', 'action'=>'index')
                            ),
                        )
                    ),
                    'Informations sup.' => array(
                        'title' => 'Administration des informations suplémentaires',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'Projet',
                                'check'=> array('Infosupdefs'),
                                'title'=> 'Gestion des informations suplémentaires de projet',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'infosupdefs', 'action'=>'index')
                            ),
                            array(
                                'html' => 'Liste',
                                'libelle' => 'Séance',
                                'check'=> array('Infosupdefs'),
                                'title'=> 'Gestion des informations suplémentaires de projet',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'infosupdefs', 'action'=>'index_seance')
                            ),
                        )
                    ),
                    'Maintenance' => array(
                        'title' => 'Maintenance de l\'application',
                        'subMenu' => array(
                            array(
                                'html' => 'link',
                                'libelle' => 'Connecteurs',
                                'check'=> array('Connecteurs'),
                                'title'=> 'Gestion des informations suplémentaires de projet',
                                'icon' => 'connectdevelop',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'connecteurs', 'action'=>'index')
                            ),
                            array(
                                'html' => 'Liste',
                                'libelle' => 'Séance',
                                'check'=> array('Crons'),
                                'title'=> 'Gestion des informations suplémentaires de projet',
                                'icon' => 'clock-o',
                                'url' => array('admin' => true, 'plugin'=>null, 'controller'=>'crons', 'action'=>'index_seance')
                            ),
                        )
                    ),
                )
            ),
        )
    ),
);