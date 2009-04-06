-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 06 Avril 2009 à 11:15
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `webdelib2`
--

-- --------------------------------------------------------

--
-- Structure de la table `acos`
--

CREATE TABLE `acos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=41 ;

--
-- Contenu de la table `acos`
--

INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Pages:home', 1, 2),
(2, NULL, 'Pages:mes_projets', 3, 16),
(3, NULL, 'Deliberations:add', 14, 15),
(4, NULL, 'Deliberations:mesProjetsRedaction', 12, 13),
(5, NULL, 'Deliberations:mesProjetsValidation', 10, 11),
(6, NULL, 'Deliberations:mesProjetsATraiter', 8, 9),
(7, NULL, 'Deliberations:mesProjetsValides', 6, 7),
(8, NULL, 'Deliberations:mesProjetsRecherche', 4, 5),
(9, NULL, 'Pages:tous_les_projets', 17, 26),
(10, NULL, 'Deliberations:tousLesProjetsSansSeance', 24, 25),
(11, NULL, 'Deliberations:tousLesProjetsValidation', 22, 23),
(12, NULL, 'Deliberations:tousLesProjetsAFaireVoter', 20, 21),
(13, NULL, 'Deliberations:tousLesProjetsRecherche', 18, 19),
(14, NULL, 'Seances:listerFuturesSeances', 27, 34),
(15, NULL, 'Seances:add', 32, 33),
(16, NULL, 'Seances:listerAnciennesSeances', 30, 31),
(17, NULL, 'Seances:afficherCalendrier', 28, 29),
(18, NULL, 'Pages:postseance', 35, 42),
(19, NULL, 'Postseances:index', 40, 41),
(20, NULL, 'Deliberations:transmit', 38, 39),
(21, NULL, 'Pages:exportged', 36, 37),
(22, NULL, 'Pages:gestion_utilisateurs', 43, 54),
(23, NULL, 'Profils:index', 52, 53),
(24, NULL, 'Droits:edit', 50, 51),
(25, NULL, 'Services:index', 48, 49),
(26, NULL, 'Users:index', 46, 47),
(27, NULL, 'Circuits:index', 44, 45),
(28, NULL, 'Pages:gestion_acteurs', 55, 60),
(29, NULL, 'Typeacteurs:index', 58, 59),
(30, NULL, 'Acteurs:index', 56, 57),
(31, NULL, 'Pages:administration', 61, 76),
(32, NULL, 'Collectivites:index', 74, 75),
(33, NULL, 'Themes:index', 72, 73),
(34, NULL, 'Models:index', 70, 71),
(35, NULL, 'Sequences:index', 68, 69),
(36, NULL, 'Compteurs:index', 66, 67),
(37, NULL, 'Typeseances:index', 64, 65),
(38, NULL, 'Infosupdefs:index', 62, 63),
(39, NULL, 'Deliberations', 77, 80),
(40, NULL, 'Deliberations:editerProjetValide', 78, 79);

-- --------------------------------------------------------

--
-- Structure de la table `acteurs`
--

CREATE TABLE `acteurs` (
  `id` int(11) NOT NULL auto_increment,
  `typeacteur_id` int(11) NOT NULL default '0',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `salutation` varchar(50) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `position` int(11) NOT NULL,
  `date_naissance` date NOT NULL,
  `adresse1` varchar(100) NOT NULL,
  `adresse2` varchar(100) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telfixe` varchar(20) default NULL,
  `telmobile` varchar(20) default NULL,
  `note` varchar(255) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=37 ;

--
-- Contenu de la table `acteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `acteurs_services`
--

CREATE TABLE `acteurs_services` (
  `acteur_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acteur_id`,`service_id`)
) ;

--
-- Contenu de la table `acteurs_services`
--


-- --------------------------------------------------------

--
-- Structure de la table `annexes`
--

CREATE TABLE `annexes` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation_id` int(11) NOT NULL,
  `seance_id` int(11) default NULL,
  `titre` varchar(50) NOT NULL,
  `type` char(1) NOT NULL,
  `filename` varchar(75) NOT NULL,
  `filetype` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `data` mediumblob NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=41 ;

--
-- Contenu de la table `annexes`
--


-- --------------------------------------------------------

--
-- Structure de la table `aros`
--

CREATE TABLE `aros` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `foreign_key` int(10) unsigned default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=7 ;

--
-- Contenu de la table `aros`
--

INSERT INTO `aros` (`id`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, 0, 'Profil:Administrateur', 1, 4),
(2, 1, 'Utilisateur:admin', 2, 3),
(3, 0, 'Profil:Défaut', 5, 6),
(4, 0, 'Profil:Rédacteur', 7, 8),
(5, 0, 'Profil:Service assemblée', 9, 10),
(6, 0, 'Profil:Valideur', 11, 12);

-- --------------------------------------------------------

--
-- Structure de la table `aros_acos`
--

CREATE TABLE `aros_acos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `aro_id` int(10) unsigned NOT NULL,
  `aco_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL default '0',
  `_read` char(2) NOT NULL default '0',
  `_update` char(2) NOT NULL default '0',
  `_delete` char(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=49 ;

--
-- Contenu de la table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 1, 1, '1', '1', '1', '1'),
(2, 1, 2, '-1', '-1', '-1', '-1'),
(3, 1, 9, '-1', '-1', '-1', '-1'),
(4, 1, 14, '-1', '-1', '-1', '-1'),
(5, 1, 18, '-1', '-1', '-1', '-1'),
(6, 1, 22, '1', '1', '1', '1'),
(7, 1, 28, '-1', '-1', '-1', '-1'),
(8, 1, 31, '1', '1', '1', '1'),
(9, 1, 39, '-1', '-1', '-1', '-1'),
(10, 3, 1, '1', '1', '1', '1'),
(11, 3, 2, '-1', '-1', '-1', '-1'),
(12, 3, 9, '-1', '-1', '-1', '-1'),
(13, 3, 14, '-1', '-1', '-1', '-1'),
(14, 3, 18, '-1', '-1', '-1', '-1'),
(15, 3, 22, '-1', '-1', '-1', '-1'),
(16, 3, 28, '-1', '-1', '-1', '-1'),
(17, 3, 31, '-1', '-1', '-1', '-1'),
(18, 3, 39, '-1', '-1', '-1', '-1'),
(19, 4, 1, '1', '1', '1', '1'),
(20, 4, 2, '1', '1', '1', '1'),
(21, 4, 9, '-1', '-1', '-1', '-1'),
(22, 4, 14, '-1', '-1', '-1', '-1'),
(23, 4, 18, '-1', '-1', '-1', '-1'),
(24, 4, 22, '-1', '-1', '-1', '-1'),
(25, 4, 28, '-1', '-1', '-1', '-1'),
(26, 4, 31, '-1', '-1', '-1', '-1'),
(27, 4, 39, '-1', '-1', '-1', '-1'),
(28, 5, 1, '1', '1', '1', '1'),
(29, 5, 2, '-1', '-1', '-1', '-1'),
(30, 5, 9, '1', '1', '1', '1'),
(31, 5, 14, '-1', '-1', '-1', '-1'),
(32, 5, 18, '-1', '-1', '-1', '-1'),
(33, 5, 22, '-1', '-1', '-1', '-1'),
(34, 5, 28, '1', '1', '1', '1'),
(35, 5, 31, '-1', '-1', '-1', '-1'),
(36, 5, 39, '-1', '-1', '-1', '-1'),
(37, 6, 1, '1', '1', '1', '1'),
(38, 6, 2, '1', '1', '1', '1'),
(39, 6, 3, '-1', '-1', '-1', '-1'),
(40, 6, 4, '-1', '-1', '-1', '-1'),
(41, 6, 7, '-1', '-1', '-1', '-1'),
(42, 6, 9, '-1', '-1', '-1', '-1'),
(43, 6, 14, '-1', '-1', '-1', '-1'),
(44, 6, 18, '-1', '-1', '-1', '-1'),
(45, 6, 22, '-1', '-1', '-1', '-1'),
(46, 6, 28, '-1', '-1', '-1', '-1'),
(47, 6, 31, '-1', '-1', '-1', '-1'),
(48, 6, 39, '-1', '-1', '-1', '-1');

-- --------------------------------------------------------

--
-- Structure de la table `circuits`
--

CREATE TABLE `circuits` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=4 ;

--
-- Contenu de la table `circuits`
--


-- --------------------------------------------------------

--
-- Structure de la table `collectivites`
--

CREATE TABLE `collectivites` (
  `id` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `CP` int(11) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `collectivites`
--

INSERT INTO `collectivites` (`id`, `nom`, `adresse`, `CP`, `ville`, `telephone`) VALUES
(1, 'Adullact', '335, Cour Messier', 34000, 'Montpellier', '0467650588');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL default '0',
  `agent_id` int(11) NOT NULL default '0',
  `texte` varchar(200) NOT NULL default '',
  `pris_en_compte` tinyint(4) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=3 ;

--
-- Contenu de la table `commentaires`
--


-- --------------------------------------------------------

--
-- Structure de la table `compteurs`
--

CREATE TABLE `compteurs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `def_compteur` varchar(255) NOT NULL,
  `sequence_id` int(11) NOT NULL,
  `def_reinit` varchar(255) NOT NULL,
  `val_reinit` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) AUTO_INCREMENT=3 ;

--
-- Contenu de la table `compteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `deliberations`
--

CREATE TABLE `deliberations` (
  `id` int(11) NOT NULL auto_increment,
  `circuit_id` int(11) default '0',
  `theme_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  `vote_id` int(11) NOT NULL default '0',
  `redacteur_id` int(11) NOT NULL default '0',
  `rapporteur_id` int(11) NOT NULL default '0',
  `seance_id` int(11) default NULL,
  `position` int(4) NOT NULL,
  `anterieure_id` int(11) NOT NULL,
  `objet` varchar(1000) NOT NULL,
  `titre` varchar(1000) NOT NULL,
  `num_delib` varchar(15) NOT NULL,
  `num_pref` varchar(10) NOT NULL default '',
  `texte_projet` longblob,
  `texte_projet_name` varchar(75) NOT NULL,
  `texte_projet_type` varchar(255) NOT NULL,
  `texte_projet_size` int(11) NOT NULL,
  `texte_synthese` longblob,
  `texte_synthese_name` varchar(75) NOT NULL,
  `texte_synthese_type` varchar(255) NOT NULL,
  `texte_synthese_size` int(11) NOT NULL,
  `deliberation` longblob,
  `deliberation_name` varchar(75) NOT NULL,
  `deliberation_type` varchar(255) NOT NULL,
  `deliberation_size` int(11) NOT NULL,
  `date_limite` date default NULL,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `reporte` tinyint(1) NOT NULL default '0',
  `localisation1_id` int(11) NOT NULL default '0',
  `localisation2_id` int(11) NOT NULL default '0',
  `localisation3_id` int(11) NOT NULL default '0',
  `montant` int(10) NOT NULL,
  `debat` longblob NOT NULL,
  `debat_name` varchar(255) NOT NULL,
  `debat_size` int(11) NOT NULL,
  `debat_type` varchar(255) NOT NULL,
  `avis` int(1) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_nb_oui` int(3) NOT NULL,
  `vote_nb_non` int(3) NOT NULL,
  `vote_nb_abstention` int(3) NOT NULL,
  `vote_nb_retrait` int(3) NOT NULL,
  `vote_commentaire` varchar(500) NOT NULL,
  `commission` longblob NOT NULL,
  `commission_size` int(11) NOT NULL,
  `commission_type` varchar(255) NOT NULL,
  `commission_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=443 ;

--
-- Contenu de la table `deliberations`
--


-- --------------------------------------------------------

--
-- Structure de la table `infosupdefs`
--

CREATE TABLE `infosupdefs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `ordre` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `taille` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `recherche` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=7 ;

--
-- Contenu de la table `infosupdefs`
--


-- --------------------------------------------------------

--
-- Structure de la table `infosups`
--

CREATE TABLE `infosups` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation_id` int(11) NOT NULL,
  `infosupdef_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `deliberation_id` (`deliberation_id`),
  KEY `infosupdef_id` (`infosupdef_id`)
) AUTO_INCREMENT=82 ;

--
-- Contenu de la table `infosups`
--


-- --------------------------------------------------------

--
-- Structure de la table `listepresences`
--

CREATE TABLE `listepresences` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL,
  `mandataire` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=1263 ;

--
-- Contenu de la table `listepresences`
--


-- --------------------------------------------------------

--
-- Structure de la table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL auto_increment,
  `modele` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(255) default NULL,
  `size` int(11) NOT NULL,
  `extension` varchar(255) default NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=8 ;

--
-- Contenu de la table `models`
--


-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=6 ;

--
-- Contenu de la table `profils`
--

INSERT INTO `profils` (`id`, `parent_id`, `libelle`, `actif`, `created`, `modified`) VALUES
(1, 0, 'Défaut', 1, '2009-04-06 11:06:17', '2009-04-06 11:06:17'),
(2, 0, 'Administrateur', 1, '2009-04-06 11:06:39', '2009-04-06 11:06:39'),
(3, 0, 'Rédacteur', 1, '2009-04-06 11:06:48', '2009-04-06 11:06:48'),
(4, 0, 'Valideur', 1, '2009-04-06 11:06:54', '2009-04-06 11:06:54'),
(5, 0, 'Service assemblée', 1, '2009-04-06 11:07:03', '2009-04-06 11:07:03');

-- --------------------------------------------------------

--
-- Structure de la table `seances`
--

CREATE TABLE `seances` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `traitee` int(1) NOT NULL default '0',
  `secretaire_id` int(11) default NULL,
  `debat_global` longblob NOT NULL,
  `debat_global_name` varchar(75) NOT NULL,
  `debat_global_size` int(11) NOT NULL,
  `debat_global_type` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=4 ;

--
-- Contenu de la table `seances`
--


-- --------------------------------------------------------

--
-- Structure de la table `sequences`
--

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `num_sequence` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) AUTO_INCREMENT=3 ;

--
-- Contenu de la table `sequences`
--


-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `order` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `circuit_defaut_id` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=9 ;

--
-- Contenu de la table `services`
--

INSERT INTO `services` (`id`, `parent_id`, `order`, `libelle`, `circuit_defaut_id`, `actif`, `created`, `modified`) VALUES
(1, 0, '', 'Défaut', 0, 1, '2009-04-06 08:35:48', '2009-04-06 08:35:48');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `order` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=3 ;

--
-- Contenu de la table `themes`
--


-- --------------------------------------------------------

--
-- Structure de la table `traitements`
--

CREATE TABLE `traitements` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL default '0',
  `circuit_id` int(11) NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  `date_traitement` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=12 ;

--
-- Contenu de la table `traitements`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeacteurs`
--

CREATE TABLE `typeacteurs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `elu` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) AUTO_INCREMENT=3 ;

--
-- Contenu de la table `typeacteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeseances`
--

CREATE TABLE `typeseances` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL,
  `retard` int(11) NOT NULL default '0',
  `action` tinyint(1) NOT NULL,
  `compteur_id` int(11) NOT NULL,
  `modelprojet_id` int(11) NOT NULL,
  `modeldeliberation_id` int(11) NOT NULL,
  `modelconvocation_id` int(11) NOT NULL,
  `modelordredujour_id` int(11) NOT NULL,
  `modelpvsommaire_id` int(11) NOT NULL,
  `modelpvdetaille_id` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=3 ;

--
-- Contenu de la table `typeseances`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeseances_acteurs`
--

CREATE TABLE `typeseances_acteurs` (
  `typeseance_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`acteur_id`)
) ;

--
-- Contenu de la table `typeseances_acteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeseances_typeacteurs`
--

CREATE TABLE `typeseances_typeacteurs` (
  `typeseance_id` int(11) NOT NULL,
  `typeacteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`typeacteur_id`)
) ;

--
-- Contenu de la table `typeseances_typeacteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
  `statut` int(11) NOT NULL default '0',
  `login` varchar(50) NOT NULL default '',
  `note` varchar(25) NOT NULL,
  `circuit_defaut_id` int(11) NOT NULL,
  `password` varchar(100) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `telfixe` varchar(20) default NULL,
  `telmobile` varchar(20) default NULL,
  `date_naissance` date default NULL,
  `accept_notif` tinyint(1) default NULL,
  `position` int(3) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=48 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `profil_id`, `statut`, `login`, `note`, `circuit_defaut_id`, `password`, `nom`, `prenom`, `email`, `telfixe`, `telmobile`, `date_naissance`, `accept_notif`, `position`, `created`, `modified`) VALUES
(1, 2, 0, 'admin', '', 0, '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin', 'francois.desmaretz@adullact.org', '0000000000', '0000000000', '1999-11-30', 0, 0, '0000-00-00 00:00:00', '2009-04-06 11:07:38');

-- --------------------------------------------------------

--
-- Structure de la table `users_circuits`
--

CREATE TABLE `users_circuits` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `circuit_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=9 ;

--
-- Contenu de la table `users_circuits`
--


-- --------------------------------------------------------

--
-- Structure de la table `users_services`
--

CREATE TABLE `users_services` (
  `user_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`service_id`)
) ;

--
-- Contenu de la table `users_services`
--

INSERT INTO `users_services` (`user_id`, `service_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL auto_increment,
  `acteur_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `resultat` int(1) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=1265 ;

--
-- Contenu de la table `votes`
--

