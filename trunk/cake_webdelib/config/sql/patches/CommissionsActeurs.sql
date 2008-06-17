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
);


-- --------------------------------------------------------

--
-- Structure de la table `acteurs_services`
--

CREATE TABLE `acteurs_services` (
  `acteur_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acteur_id`,`service_id`)
);


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
);


-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
  `login` varchar(50) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `telfixe` varchar(20) default NULL,
  `telmobile` varchar(20) default NULL,
  `accept_notif` tinyint(1) default NULL,
  `note` varchar(255) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`)
);



-- --------------------------------------------------------

--
-- Structure de la table `compteurs`
--

CREATE TABLE `compteurs` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
  `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur',
  `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
  `def_compteur` varchar(255) NOT NULL COMMENT 'Expression formatee du compteur',
  `sequence_id` int(11) NOT NULL COMMENT 'Id de la Sequence',
  `def_reinit` varchar(255) NOT NULL COMMENT 'Expression formatee du critere de reinitialisation de la sequence',
  `val_reinit` varchar(255) NOT NULL COMMENT 'Derniere valeur calculee de la reinitialisation',
  `created` datetime NOT NULL COMMENT 'Date et heure de creation du compteur',
  `modified` datetime NOT NULL COMMENT 'Date et heure de modification du compteur',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
);


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
);


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
  `modelconvocation_id` int(11) NOT NULL,
  `modelordredujour_id` int(11) NOT NULL,
  `modelpvsommaire_id` int(11) NOT NULL,
  `modelpvdetaille_id` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `libelle` (`libelle`)
);

-- --------------------------------------------------------

--
-- Structure de la table `typeseances_acteurs`
--

CREATE TABLE `typeseances_acteurs` (
  `typeseance_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`acteur_id`)
);

-- --------------------------------------------------------

--
-- Structure de la table `typeseances_typeacteurs`
--

CREATE TABLE `typeseances_typeacteurs` (
  `typeseance_id` int(11) NOT NULL,
  `typeacteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`typeacteur_id`)
);



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
);


-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL auto_increment,
  `acteur_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `resultat` int(1) NOT NULL,
  `commentaire` varchar(500) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);
