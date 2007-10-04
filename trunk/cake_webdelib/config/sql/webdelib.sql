-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeudi 04 Octobre 2007 à 09:27
-- Version du serveur: 5.0.38
-- Version de PHP: 5.2.1
--
-- Base de données: `webdelib`
--

-- --------------------------------------------------------

--
-- Structure de la table `annexes`
--

CREATE TABLE `annexes` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation_id` int(11) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `type` char(1) NOT NULL,
  `filename` varchar(75) NOT NULL,
  `filetype` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `data` mediumblob NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `annexes`
--


-- --------------------------------------------------------

--
-- Structure de la table `circuits`
--

CREATE TABLE `circuits` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `circuits`
--

INSERT INTO `circuits` (`id`, `libelle`) VALUES
(1, 'soutenance');

-- --------------------------------------------------------

--
-- Structure de la table `collectivites`
--

CREATE TABLE `collectivites` (
  `id` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `collectivites`
--

INSERT INTO `collectivites` (`id`, `nom`) VALUES
(0, 'ADULLACT');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL default '0',
  `agent_id` int(11) NOT NULL default '0',
  `texte` varchar(200) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `commentaires`
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
  `objet` varchar(100) NOT NULL default '',
  `titre` varchar(100) NOT NULL default '',
  `num_delib` varchar(10) NOT NULL default '',
  `num_pref` varchar(10) NOT NULL default '',
  `texte_projet` longblob,
  `texte_synthese` longblob,
  `date_limite` date default NULL,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Contenu de la table `deliberations`
--

INSERT INTO `deliberations` (`id`, `circuit_id`, `theme_id`, `service_id`, `vote_id`, `redacteur_id`, `rapporteur_id`, `seance_id`, `position`, `anterieure_id`, `objet`, `titre`, `num_delib`, `num_pref`, `texte_projet`, `texte_synthese`, `date_limite`, `date_envoi`, `etat`, `created`, `modified`) VALUES
(1, 1, 2, 1, 0, 1, 14, 1, 1, 0, 'Regarnissage de la route 66', 'Regarnissage de la route 66 du aux intempéries de la nuit du 5 au 8 octobre 2006.', '', '', 0x3c703e52656761726e697373616765206465206c61207275653c2f703e, NULL, NULL, '2007-10-03 15:15:16', 1, '2007-10-03 15:09:36', '2007-10-03 15:15:16'),
(3, 0, 2, 1, 0, 1, 14, 1, 2, 0, 'Regarnissage de la route 66', 'Regarnissage de la route 66', '', '', 0x3c703e52656761726e697373616765206465206c6120726f7574652036363c2f703e, NULL, NULL, NULL, 0, '2007-10-03 15:32:37', '2007-10-03 15:32:43'),
(4, 0, 4, 1, 0, 1, 14, 1, 3, 0, 'Achat d''un clavier usb', 'Achat d''un clavier usb', '', '', 0x3c703e4163686174206427756e20636c61766965723c2f703e, NULL, NULL, NULL, 0, '2007-10-03 15:36:29', '2007-10-03 16:29:36');

-- --------------------------------------------------------

--
-- Structure de la table `deliberations_odjs`
--

CREATE TABLE `deliberations_odjs` (
  `delib_id` int(11) NOT NULL default '0',
  `odj_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`delib_id`,`odj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `deliberations_odjs`
--


-- --------------------------------------------------------

--
-- Structure de la table `listepresences`
--

CREATE TABLE `listepresences` (
  `id` int(11) NOT NULL auto_increment,
  `seance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL,
  `mandataire` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `listepresences`
--


-- --------------------------------------------------------

--
-- Structure de la table `odjs`
--

CREATE TABLE `odjs` (
  `id` int(11) NOT NULL auto_increment,
  `seance_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `odjs`
--


-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(30) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

--
-- Contenu de la table `profils`
--

INSERT INTO `profils` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(62, 0, 'Validateur', '2007-09-03 14:39:20', '2007-09-03 14:39:20'),
(63, 62, 'R?dacteur', '2007-09-03 14:39:36', '2007-09-03 14:39:36'),
(64, 63, 'Service Assembl?es', '2007-09-03 14:40:03', '2007-09-03 14:40:03'),
(65, 64, 'Secr?taire de s?ance', '2007-09-03 14:40:20', '2007-09-03 14:40:20'),
(66, 0, 'Elu', '2007-09-03 14:40:35', '2007-09-03 14:40:35'),
(67, 0, 'Administrateur', '2007-09-03 14:40:53', '2007-09-03 14:40:53');

-- --------------------------------------------------------

--
-- Structure de la table `pvcomplets`
--

CREATE TABLE `pvcomplets` (
  `id` int(11) NOT NULL auto_increment,
  `seance_id` int(11) NOT NULL default '0',
  `chemin` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `pvcomplets`
--


-- --------------------------------------------------------

--
-- Structure de la table `pvsommaires`
--

CREATE TABLE `pvsommaires` (
  `id` int(11) NOT NULL auto_increment,
  `seance_id` int(11) NOT NULL default '0',
  `chemin` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `pvsommaires`
--


-- --------------------------------------------------------

--
-- Structure de la table `refannexes`
--

CREATE TABLE `refannexes` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `refannexes`
--


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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `seances`
--

INSERT INTO `seances` (`id`, `type_id`, `created`, `modified`, `date`) VALUES
(1, 1, '2007-09-02 22:46:53', '2007-09-02 22:46:53', '2008-05-07 17:03:00'),
(2, 2, '2007-09-02 22:47:09', '2007-09-02 22:47:09', '2007-10-06 16:02:00'),
(3, 3, '2007-09-02 22:47:25', '2007-09-02 22:47:25', '2015-09-06 17:13:00'),
(4, 2, '2007-09-03 02:34:39', '2007-09-03 02:34:39', '2007-10-08 13:07:00');

-- --------------------------------------------------------

--
-- Structure de la table `seances_users`
--

CREATE TABLE `seances_users` (
  `seance_id` int(9) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`seance_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `seances_users`
--

INSERT INTO `seances_users` (`seance_id`, `user_id`) VALUES
(1, 14);

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(30) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `services`
--

INSERT INTO `services` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(1, 0, 'Informatique', '2007-10-02 14:21:47', '2007-10-02 14:23:16'),
(2, 1, 'Réseau', '2007-10-02 14:24:50', '2007-10-02 16:00:29'),
(3, 1, 'Développement', '2007-10-02 14:25:00', '2007-10-02 15:59:58'),
(4, 3, 'Java', '2007-10-02 14:25:08', '2007-10-02 14:30:54'),
(5, 3, 'PHP', '2007-10-02 14:25:20', '2007-10-02 14:25:20');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(30) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `themes`
--

INSERT INTO `themes` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(1, 0, 'Urbanisme', '2007-10-02 14:43:30', '2007-10-02 14:43:30'),
(2, 1, 'Entretien des routes', '2007-10-02 14:52:36', '2007-10-02 14:52:36'),
(3, 1, 'Service technique', '2007-10-02 14:52:57', '2007-10-02 14:52:57'),
(4, 0, 'Informatique', '2007-10-02 14:53:03', '2007-10-02 14:53:03');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

--
-- Contenu de la table `traitements`
--

INSERT INTO `traitements` (`id`, `delib_id`, `circuit_id`, `position`, `date_traitement`) VALUES
(1, 3, 1, 1, '2007-09-02 22:52:01'),
(2, 3, 1, 2, '2007-09-02 22:52:38'),
(3, 3, 1, 3, '0000-00-00 00:00:00'),
(4, 2, 1, 1, '0000-00-00 00:00:00'),
(5, 4, 1, 1, '2007-09-03 02:29:42'),
(6, 4, 1, 2, '2007-09-03 02:30:03'),
(7, 4, 1, 3, '0000-00-00 00:00:00'),
(8, 6, 1, 1, '2007-09-03 10:19:22'),
(9, 6, 1, 2, '2007-09-03 10:19:50'),
(10, 6, 1, 3, '0000-00-00 00:00:00'),
(11, 1, 1, 1, '2007-10-01 21:43:22'),
(12, 1, 1, 2, '2007-10-01 21:43:57'),
(13, 2, 1, 1, '2007-10-02 10:01:41'),
(14, 2, 1, 2, '2007-10-02 10:05:30'),
(15, 3, 1, 1, '2007-10-02 10:11:50'),
(16, 3, 1, 2, '2007-10-02 10:12:42'),
(17, 4, 1, 1, '2007-10-02 10:15:16'),
(18, 4, 1, 2, '2007-10-02 10:39:43'),
(19, 5, 1, 1, '2007-10-02 10:39:26'),
(20, 5, 1, 2, '2007-10-02 10:39:50'),
(21, 6, 0, 1, '0000-00-00 00:00:00'),
(22, 3, 1, 1, '2007-10-02 11:02:00'),
(23, 3, 1, 2, '2007-10-02 11:02:54'),
(24, 3, 1, 3, '2007-10-02 11:06:05'),
(25, 3, 1, 4, '2007-10-02 11:06:59'),
(26, 4, 0, 1, '0000-00-00 00:00:00'),
(27, 1, 1, 1, '2007-10-02 11:12:26'),
(28, 1, 1, 2, '2007-10-02 11:15:21'),
(29, 1, 1, 3, '2007-10-02 11:16:01'),
(30, 1, 1, 4, '2007-10-02 11:16:56'),
(31, 2, 1, 1, '2007-10-02 11:24:01'),
(32, 2, 1, 2, '2007-10-02 11:24:17'),
(33, 2, 1, 3, '2007-10-02 11:24:35'),
(34, 2, 1, 4, '2007-10-02 11:24:56'),
(35, 2, 1, 0, '2007-10-02 12:30:03'),
(36, 3, 1, 1, '2007-10-02 11:28:34'),
(37, 3, 1, 2, '2007-10-02 11:29:56'),
(38, 3, 1, 3, '2007-10-02 11:30:22'),
(39, 3, 1, 0, '2007-10-02 12:31:19'),
(40, 4, 1, 1, '2007-10-02 11:34:03'),
(41, 4, 1, 0, '2007-10-02 12:56:48'),
(42, 4, 1, 0, '2007-10-02 11:35:30'),
(43, 4, 1, 0, '2007-10-02 11:37:48'),
(44, 4, 1, 0, '2007-10-02 11:38:09'),
(45, 4, 1, 0, '2007-10-02 11:38:33'),
(46, 4, 1, 1, '0000-00-00 00:00:00'),
(47, 5, 1, 1, '2007-10-02 11:39:17'),
(48, 5, 1, 2, '2007-10-02 11:39:35'),
(49, 5, 1, 3, '2007-10-02 11:43:08'),
(50, 5, 1, 0, '2007-10-02 13:06:59'),
(51, 6, 0, 1, '0000-00-00 00:00:00'),
(52, 1, 1, 1, '2007-10-02 11:51:27'),
(53, 1, 1, 2, '2007-10-02 11:52:07'),
(54, 1, 1, 3, '2007-10-02 11:53:21'),
(55, 1, 1, 0, '2007-10-02 11:54:13'),
(56, 2, 1, 1, '2007-10-02 11:58:14'),
(57, 2, 1, 0, '0000-00-00 00:00:00'),
(58, 3, 1, 1, '0000-00-00 00:00:00'),
(59, 3, 1, 1, '0000-00-00 00:00:00'),
(60, 3, 1, 1, '2007-10-02 12:30:39'),
(61, 3, 1, 0, '0000-00-00 00:00:00'),
(62, 4, 1, 1, '2007-10-02 12:35:29'),
(63, 4, 1, 2, '2007-10-02 12:36:07'),
(64, 4, 1, 3, '2007-10-02 12:36:57'),
(65, 4, 1, 4, '2007-10-02 12:40:20'),
(66, 4, 1, 0, '2007-10-02 12:45:31'),
(67, 4, 1, 0, '2007-10-02 12:46:05'),
(68, 4, 1, 0, '0000-00-00 00:00:00'),
(69, 5, 1, 1, '0000-00-00 00:00:00'),
(70, 5, 1, 1, '2007-10-02 12:58:23'),
(71, 5, 1, 2, '2007-10-02 13:01:41'),
(72, 5, 1, 3, '2007-10-02 13:02:49'),
(73, 5, 1, 4, '2007-10-02 13:05:54'),
(74, 5, 1, 0, '0000-00-00 00:00:00'),
(75, 6, 1, 1, '2007-10-02 13:09:58'),
(76, 6, 1, 2, '2007-10-02 13:11:28'),
(77, 6, 1, 3, '2007-10-02 13:13:36'),
(78, 6, 1, 4, '2007-10-02 13:14:14'),
(79, 6, 1, 0, '2007-10-02 13:15:58'),
(80, 7, 1, 1, '2007-10-02 13:16:30'),
(81, 7, 1, 2, '2007-10-02 13:29:15'),
(82, 7, 1, 3, '2007-10-02 13:30:21'),
(83, 7, 1, 4, '2007-10-02 13:32:26'),
(84, 7, 1, 0, '2007-10-02 13:32:36'),
(85, 8, 1, 1, '2007-10-02 13:32:47'),
(86, 8, 1, 2, '2007-10-02 15:04:50'),
(87, 8, 1, 3, '2007-10-02 15:11:04'),
(88, 8, 1, 4, '0000-00-00 00:00:00'),
(89, 1, 1, 1, '2007-10-04 09:23:24'),
(90, 1, 1, 2, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `typeseances`
--

CREATE TABLE `typeseances` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(50) NOT NULL default '',
  `retard` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `typeseances`
--

INSERT INTO `typeseances` (`id`, `libelle`, `retard`, `created`, `modified`) VALUES
(1, 'Conseil municipal', 0, '2007-08-02 10:26:47', '2007-08-02 10:26:47'),
(2, 'Conseil général', 0, '2007-08-02 10:26:53', '2007-08-02 10:26:53'),
(3, 'Commission permanente', 0, '2007-08-02 10:27:01', '2007-08-02 10:27:01');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL,
  `statut` int(11) NOT NULL default '0',
  `login` varchar(50) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `adresse` varchar(255) NOT NULL default '',
  `CP` int(11) NOT NULL default '0',
  `ville` varchar(50) NOT NULL default '',
  `teldom` int(10) unsigned zerofill default NULL,
  `telmobile` int(10) unsigned zerofill default NULL,
  `date_naissance` date default NULL,
  `accept_notif` tinyint(1) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `profil_id`, `service_id`, `statut`, `login`, `password`, `nom`, `prenom`, `email`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `accept_notif`, `created`, `modified`) VALUES
(1, 0, 1, 0, 'francois', 'eb7abf5f00d2dd1678fd3763b90d5ea7', 'Desmaretz', 'françois', 'francois.desmaretz@adullact.org', '', 0, '', NULL, NULL, NULL, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 66, 2, 1, 'pascal', '57c2877c1d84c4b49f3289657deca65c', 'Feydel', 'pascal', 'pascal@adullact.org', '', 0, '', NULL, NULL, '0000-00-00', 1, '2007-10-01 21:29:52', '2007-10-01 21:29:52'),
(15, 0, 0, 0, 'stephanie', 'e8fab42752f318b2b2beb039a57dedcd', 'Monchecourt', 'stephanie', 'stephanie@adullact.org', '', 0, '', NULL, NULL, '0000-00-00', 1, '2007-10-01 21:34:37', '2007-10-01 21:34:37'),
(16, 0, 1, 0, 'julien', '30d69d863dde81562ce277fbc0a3cf18', 'Calvet', 'Julien', 'julien@adullact.org', '', 0, '', NULL, NULL, '1970-01-01', 1, '2007-10-02 10:42:43', '2007-10-02 10:42:43'),
(17, 0, 0, 0, 'remi', 'f1067e7173c7b9e6714ec7c88cf04bb1', 'Moine', 'RÃ©mi', 'remi@adullact.org', '', 0, '', 0000000000, 0000000000, '1999-11-30', 1, '2007-10-02 10:43:10', '2007-10-02 10:43:32');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `users_circuits`
--

INSERT INTO `users_circuits` (`id`, `user_id`, `circuit_id`, `service_id`, `position`) VALUES
(1, 1, 1, 1, 1),
(8, 17, 1, 1, 4),
(7, 16, 1, 1, 3),
(6, 15, 1, 4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `users_services`
--

CREATE TABLE `users_services` (
  `user_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users_services`
--

INSERT INTO `users_services` (`user_id`, `service_id`) VALUES
(15, 4),
(16, 1),
(17, 1);

-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `resultat` int(1) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `votes`
--
