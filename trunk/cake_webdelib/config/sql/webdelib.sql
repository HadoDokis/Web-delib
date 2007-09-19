-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mercredi 19 Septembre 2007 à 12:06
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Contenu de la table `circuits`
-- 

INSERT INTO `circuits` (`id`, `libelle`) VALUES 
(1, 'soutenance'),
(2, 'plop'),
(3, 'truc');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- 
-- Contenu de la table `commentaires`
-- 

INSERT INTO `commentaires` (`id`, `delib_id`, `agent_id`, `texte`, `created`, `modified`) VALUES 
(1, 4, 7, 'ceci est un commentaire inserer depuis la bdd', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 49, 7, 'tototata', '2007-09-12 14:15:55', '2007-09-12 14:15:55'),
(3, 51, 7, 'tototata', '2007-09-12 14:19:20', '2007-09-12 14:19:20'),
(4, 57, 12, '', '2007-09-12 16:28:03', '2007-09-12 16:28:03'),
(5, 57, 12, '', '2007-09-12 16:34:26', '2007-09-12 16:34:26'),
(30, 61, 12, 'bin c un commentaire tout bÃªte pour dire que le delib est bien, j''ai rien a redire dessus !', '2007-09-14 09:31:48', '2007-09-14 09:31:48'),
(17, 48, 12, 'toto', '2007-09-13 14:43:04', '2007-09-13 16:29:33'),
(18, 0, 12, 'ttt', '2007-09-13 15:38:18', '2007-09-13 15:38:18'),
(13, 0, 12, 'new3 comm', '2007-09-13 14:12:50', '2007-09-13 14:14:30'),
(14, 0, 0, 'edit comm 12 delib 48', '2007-09-13 14:25:17', '2007-09-13 14:25:17'),
(16, 0, 12, 'rrrr', '2007-09-13 14:38:25', '2007-09-13 14:38:25'),
(19, 0, 12, 'ttt', '2007-09-13 15:38:46', '2007-09-13 15:38:46'),
(20, 0, 12, 'r', '2007-09-13 15:41:03', '2007-09-13 15:41:03'),
(21, 0, 12, 'aaa', '2007-09-13 15:42:31', '2007-09-13 15:42:31'),
(22, 48, 12, 'nb', '2007-09-13 15:55:41', '2007-09-13 15:55:41'),
(23, 48, 12, 'comm', '2007-09-13 15:56:09', '2007-09-13 15:56:09'),
(24, 48, 12, 'titi', '2007-09-13 16:00:41', '2007-09-13 16:00:41'),
(25, 48, 12, 'truc', '2007-09-13 16:03:27', '2007-09-13 16:03:27'),
(29, 48, 12, 'wouahou', '2007-09-13 16:18:32', '2007-09-13 16:27:49');

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
  `position` int(3) NOT NULL,
  `anterieure_id` int(11) NOT NULL,
  `objet` varchar(100) NOT NULL default '',
  `titre` varchar(100) NOT NULL default '',
  `num_delib` varchar(10) NOT NULL default '',
  `num_pref` varchar(10) NOT NULL default '',
  `texte_projet` longblob,
  `texte_synthese` longblob,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=74 ;

-- 
-- Contenu de la table `deliberations`
-- 

INSERT INTO `deliberations` (`id`, `circuit_id`, `theme_id`, `service_id`, `vote_id`, `redacteur_id`, `rapporteur_id`, `seance_id`, `position`, `anterieure_id`, `objet`, `titre`, `num_delib`, `num_pref`, `texte_projet`, `texte_synthese`, `date_envoi`, `etat`, `created`, `modified`) VALUES 
(73, 0, 1, 1, 0, 12, 10, NULL, 0, 0, '', '', '', '', NULL, NULL, NULL, 0, '2007-09-17 10:07:53', '2007-09-17 10:07:53'),
(44, 2, 1, 1, 0, 12, 10, 4, 0, 0, 'delib5', 'delib5', 'delib5', '', 0x3c703e64656c6962353c2f703e, 0x3c703e64656c6962353c2f703e, '2007-09-12 14:07:20', -1, '2007-09-11 16:13:01', '2007-09-12 14:17:30'),
(43, 1, 2, 1, 0, 12, 13, 0, 0, 0, 'delib4', 'delib4', 'delib4', '', 0x3c703e74657874652070726f6a65742064656c6962343c2f703e, 0x3c703e746578746520737974686573652064656c6962343c2f703e, '2007-09-11 15:09:43', 1, '2007-09-11 15:07:00', '2007-09-11 15:09:43'),
(13, 1, 2, 1, 0, 9, 10, 2, 0, 0, 'Plus de routes', 'Encore plus de route', 'RN99', '', 0x3c703e4e6f757320766f756c6f6e732064657320726f757465732c20656e636f726520706c757320646520726f7574657320212121213c2f703e, 0x3c703e457420706f75722066696e6972206f6e2076657574206465732074726f74746f69726573207175616e64206d656d65203a293c2f703e, '2007-09-10 11:40:21', -1, '2007-09-10 11:39:32', '2007-09-12 14:17:40'),
(46, 2, 2, 1, 0, 12, 10, 1, 0, 0, 'delib6', 'delib6', 'delib6', '', 0x3c703e64656c6962363c2f703e, 0x3c703e64656c6962363c2f703e, '2007-09-12 11:16:15', -1, '2007-09-12 11:15:58', '2007-09-12 12:12:19'),
(48, 2, 1, 1, 0, 12, 10, 4, 0, 0, 'delib8', 'delib8', 'delib8', '', 0x3c703e64656c6962383c2f703e, 0x3c703e64656c6962383c2f703e, '2007-09-13 13:59:14', 1, '2007-09-12 11:42:24', '2007-09-13 13:59:14'),
(47, 2, 1, 1, 0, 12, 10, 2, 0, 0, 'delib7', 'delib7', 'delib7', '', 0x3c703e64656c6962373c2f703e, 0x3c703e64656c6962373c2f703e, '2007-09-12 11:24:14', -1, '2007-09-12 11:23:57', '2007-09-12 14:06:28'),
(41, 2, 1, 1, 0, 12, 13, 4, 0, 0, 'delib3', 'delib3', 'delib3', '', NULL, NULL, '2007-09-12 14:50:22', -1, '2007-09-11 10:37:37', '2007-09-12 16:44:31'),
(39, 1, 1, 1, 0, 12, 10, 1, 0, 0, 'delib1', 'delib1', 'delib1', '', 0x3c703e74657874652064652070726f6a65742064652064656c6962313c2f703e, 0x3c703e74657874652064652073796e74686573652064652064656c6962313c2f703e, '2007-09-10 17:26:12', 1, '2007-09-10 17:24:06', '2007-09-11 09:37:19'),
(42, 1, 1, 1, 0, 12, 10, 0, 0, 0, 'tutu', 'tutute', 'tutu', '', '', '', NULL, 0, '2007-09-11 12:31:39', '2007-09-18 16:30:11'),
(40, 2, 2, 1, 0, 12, 13, 2, 0, 0, 'delib2', 'delib2', 'delib2', '', 0x3c703e746578742064652064656c69623c2f703e, 0x3c703e74657874652064652073796e74686573652064652064656c6962323c2f703e, '2007-09-11 09:40:28', -1, '2007-09-11 09:39:22', '2007-09-12 10:57:05'),
(72, 1, 1, 3, 0, 8, 13, 4, 0, 0, 'delib_pos', 'delib_pos', 'delib_pos', '', 0x3c703e64656c69625f706f733c2f703e, 0x3c703e64656c69625f706f733c2f703e, '2007-09-14 16:43:46', 2, '2007-09-14 16:43:25', '2007-09-14 16:55:37'),
(70, 2, 2, 1, 0, 12, 10, 2, 0, 0, 'delib_ann', 'delib_ann', 'delib_ann', '', 0x3c703e64656c69625f616e6e3c2f703e, 0x3c703e64656c69625f616e6e3c2f703e, '2007-09-14 10:39:04', 1, '2007-09-14 10:38:40', '2007-09-14 11:55:05'),
(71, 0, 1, 1, 0, 12, 10, NULL, 0, 0, '', '', '', '', NULL, NULL, NULL, 0, '2007-09-14 13:55:30', '2007-09-14 13:55:30'),
(61, 2, 1, 1, 0, 12, 10, 2, 0, 0, 'delib9', 'delib9', 'delib9', '', 0x3c703e64656c6962393c2f703e, 0x3c703e64656c6962393c2f703e, '2007-09-13 13:59:40', 2, '2007-09-12 16:49:30', '2007-09-14 16:54:29'),
(60, 2, 1, 1, 0, 12, 10, 2, 0, 0, 'delib8', 'delib8', 'delib8', '', 0x3c703e64656c6962383c2f703e, 0x3c703e64656c6962383c2f703e, '2007-09-13 13:58:53', 1, '2007-09-12 16:49:03', '2007-09-13 13:58:53');

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
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Contenu de la table `listepresences`
-- 

INSERT INTO `listepresences` (`id`, `created`, `modified`) VALUES 
(1, '2007-09-17 11:10:17', '2007-09-17 11:10:17'),
(5, '2007-09-17 15:15:45', '2007-09-17 15:15:45'),
(6, '2007-09-17 15:18:44', '2007-09-17 15:18:44'),
(7, '2007-09-17 15:19:06', '2007-09-17 15:19:06'),
(8, '2007-09-17 15:20:39', '2007-09-17 15:20:39');

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
(63, 62, 'Rédacteur', '2007-09-03 14:39:36', '2007-09-03 14:39:36'),
(64, 63, 'Service Assemblées', '2007-09-03 14:40:03', '2007-09-03 14:40:03'),
(65, 64, 'Secrétaire de séance', '2007-09-03 14:40:20', '2007-09-03 14:40:20'),
(66, 0, 'Elu', '2007-09-03 14:40:35', '2007-09-03 14:40:35'),
(67, 0, 'Administrateur', '2007-09-03 14:40:53', '2007-09-18 17:11:48');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- 
-- Contenu de la table `seances`
-- 

INSERT INTO `seances` (`id`, `type_id`, `created`, `modified`, `date`) VALUES 
(1, 1, '2007-09-02 22:46:53', '2007-09-02 22:46:53', '2008-05-07 17:03:00'),
(2, 2, '2007-09-02 22:47:09', '2007-09-02 22:47:09', '2007-10-06 16:02:00'),
(3, 3, '2007-09-02 22:47:25', '2007-09-02 22:47:25', '2015-09-06 17:13:00'),
(4, 2, '2007-09-03 02:34:39', '2007-09-03 02:34:39', '2007-10-08 13:07:00'),
(5, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2006-09-06 00:00:00'),
(6, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2007-09-02 00:00:00'),
(7, 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2005-09-21 00:00:00'),
(18, 2, '2007-09-18 14:54:08', '2007-09-18 15:49:10', '2010-10-10 10:10:00'),
(16, 2, '2007-09-18 11:08:56', '2007-09-18 16:03:21', '2012-12-12 12:12:00'),
(17, 1, '2007-09-18 11:10:01', '2007-09-18 15:50:00', '2011-11-11 11:11:00');

-- --------------------------------------------------------

-- 
-- Structure de la table `seances_users`
-- 

CREATE TABLE `seances_users` (
  `seance_id` int(9) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `seances_users`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `services`
-- 

CREATE TABLE `services` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- 
-- Contenu de la table `services`
-- 

INSERT INTO `services` (`id`, `libelle`, `created`, `modified`) VALUES 
(1, 'Informatique', '2007-08-02 10:25:40', '2007-09-18 17:04:14'),
(2, 'Urbanisme', '2007-08-02 10:25:48', '2007-09-18 17:11:13'),
(3, 'Voirie', '2007-08-02 10:25:59', '2007-08-02 10:25:59'),
(4, 'Education', '2007-08-02 10:26:03', '2007-08-02 10:26:03'),
(5, 'SantÃ©', '2007-09-06 14:37:47', '2007-09-06 14:37:47'),
(12, 'jeunesse', '2007-09-06 15:11:42', '2007-09-06 15:11:42'),
(18, 'environnement', '2007-09-06 17:05:14', '2007-09-06 17:05:14');

-- --------------------------------------------------------

-- 
-- Structure de la table `themes`
-- 

CREATE TABLE `themes` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Contenu de la table `themes`
-- 

INSERT INTO `themes` (`id`, `libelle`, `created`, `modified`) VALUES 
(1, 'Amenagement du territoire', '2007-08-02 10:26:24', '2007-09-18 16:59:54'),
(2, 'Entretien des routes', '2007-08-02 10:26:37', '2007-08-02 10:26:37');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=81 ;

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
(7, 4, 1, 3, '2007-09-07 10:00:33'),
(8, 6, 1, 1, '2007-09-03 10:19:22'),
(9, 6, 1, 2, '2007-09-03 10:19:50'),
(10, 6, 1, 3, '0000-00-00 00:00:00'),
(11, 4, 1, 4, '0000-00-00 00:00:00'),
(12, 7, 1, 1, '2007-09-07 10:05:54'),
(13, 7, 1, 2, '2007-09-07 14:12:37'),
(14, 8, 1, 1, '2007-09-07 10:52:01'),
(15, 8, 1, 0, '2007-09-07 10:52:22'),
(16, 9, 1, 1, '2007-09-07 10:52:37'),
(17, 9, 1, 2, '2007-09-07 10:53:25'),
(18, 10, 1, 1, '0000-00-00 00:00:00'),
(19, 7, 1, 0, '0000-00-00 00:00:00'),
(20, 12, 1, 1, '0000-00-00 00:00:00'),
(21, 13, 1, 1, '2007-09-10 11:40:44'),
(22, 13, 1, 2, '2007-09-12 14:17:40'),
(23, 37, 1, 1, '0000-00-00 00:00:00'),
(24, 39, 1, 1, '2007-09-14 17:08:17'),
(25, 40, 2, 1, '2007-09-11 16:29:32'),
(26, 43, 1, 1, '0000-00-00 00:00:00'),
(27, 40, 2, 2, '2007-09-12 10:57:05'),
(28, 40, 2, 0, '2007-09-12 14:07:02'),
(29, 46, 2, 1, '2007-09-12 11:21:34'),
(30, 46, 2, 2, '2007-09-12 12:11:41'),
(31, 47, 2, 1, '2007-09-12 11:41:15'),
(32, 47, 2, 2, '2007-09-12 14:06:28'),
(33, 48, 2, 1, '2007-09-12 11:56:39'),
(34, 48, 2, 2, '2007-09-12 12:00:21'),
(35, 46, 2, 0, '2007-09-12 14:06:55'),
(36, 46, 2, 0, '0000-00-00 00:00:00'),
(37, 50, 2, 1, '2007-09-12 14:04:11'),
(38, 50, 2, 2, '2007-09-12 14:05:03'),
(39, 47, 2, 0, '2007-09-12 14:18:35'),
(40, 49, 2, 1, '2007-09-12 14:15:55'),
(41, 45, 2, 1, '2007-09-12 14:09:03'),
(42, 44, 2, 1, '2007-09-12 14:10:53'),
(43, 45, 2, 2, '2007-09-12 14:17:35'),
(44, 44, 2, 2, '2007-09-12 14:17:30'),
(45, 49, 2, 2, '2007-09-12 14:17:16'),
(46, 49, 2, 0, '2007-09-12 14:18:43'),
(47, 44, 2, 0, '2007-09-12 14:18:14'),
(48, 45, 2, 0, '0000-00-00 00:00:00'),
(49, 13, 1, 0, '0000-00-00 00:00:00'),
(50, 53, 2, 1, '2007-09-12 14:27:09'),
(51, 51, 2, 1, '2007-09-12 14:19:20'),
(52, 52, 2, 1, '2007-09-12 14:24:24'),
(53, 51, 2, 2, '2007-09-12 14:49:51'),
(54, 52, 2, 0, '2007-09-12 14:50:28'),
(55, 53, 2, 0, '2007-09-12 14:50:16'),
(56, 51, 2, 0, '2007-09-12 14:50:33'),
(57, 57, 2, 1, '2007-09-12 16:24:26'),
(58, 41, 2, 1, '2007-09-12 14:54:01'),
(59, 56, 2, 1, '2007-09-12 16:10:56'),
(60, 58, 2, 1, '0000-00-00 00:00:00'),
(61, 41, 2, 2, '2007-09-12 16:44:31'),
(62, 56, 2, 2, '2007-09-12 16:22:25'),
(63, 57, 2, 2, '2007-09-12 16:34:26'),
(64, 41, 2, 0, '0000-00-00 00:00:00'),
(65, 60, 2, 1, '2007-09-13 09:55:45'),
(66, 61, 2, 1, '0000-00-00 00:00:00'),
(67, 60, 2, 2, '0000-00-00 00:00:00'),
(68, 0, 0, 0, '2007-09-13 10:16:33'),
(69, 0, 0, 0, '2007-09-13 10:18:01'),
(70, 0, 0, 0, '2007-09-13 10:18:06'),
(71, 0, 0, 0, '2007-09-13 10:19:39'),
(72, 60, 2, 1, '0000-00-00 00:00:00'),
(73, 48, 2, 1, '2007-09-13 16:31:59'),
(74, 61, 2, 1, '2007-09-14 09:32:05'),
(75, 48, 2, 2, '0000-00-00 00:00:00'),
(76, 61, 2, 2, '2007-09-14 16:54:29'),
(77, 70, 2, 1, '0000-00-00 00:00:00'),
(78, 72, 1, 1, '2007-09-14 16:44:23'),
(79, 72, 1, 2, '2007-09-14 16:55:36'),
(80, 39, 1, 2, '0000-00-00 00:00:00');

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
(1, 'Conseil municipal', 9, '2007-08-02 10:26:47', '2007-09-18 16:54:53'),
(2, 'Conseil g?n?ral', 0, '2007-08-02 10:26:53', '2007-08-02 10:26:53'),
(3, 'Commission permanente', 0, '2007-08-02 10:27:01', '2007-08-02 10:27:01');

-- --------------------------------------------------------

-- 
-- Structure de la table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
  `service_id` int(11) default NULL,
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
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

-- 
-- Contenu de la table `users`
-- 

INSERT INTO `users` (`id`, `profil_id`, `service_id`, `statut`, `login`, `password`, `nom`, `prenom`, `email`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES 
(20, 63, NULL, 0, 'po', 'f6122c971aeb03476bf01623b09ddfd4', 'po', 'po', 'po', 'po', 0, 'po', 0000000000, 0000000000, '1960-04-17', '2007-09-17 14:42:11', '2007-09-19 11:54:46'),
(21, 62, 1, 1, 'mo', '27c9d5187cd283f8d160ec1ed2b5ac89', 'mo', 'mo', 'mo', 'mo', 0, 'mo', 0000000000, 0000000000, '1947-01-20', '2007-09-18 15:29:42', '2007-09-18 16:45:29'),
(10, 66, 1, 1, 'melanie', '73aaec6dc33b96597d8019f7553e96a2', 'Le breton', 'Melanie', 'melanie@melanie.fr', 'melanie', 67867, 'melanie', 1234567890, 1234567890, '1948-02-02', '2007-09-02 22:41:05', '2007-09-14 10:10:20'),
(12, 2, 0, 0, 'adullact', '9825b1be57b3d7912cf79db3d16aa501', 'Adullact', 'Adullact', 'adullact@adullact.org', 'adullact', 12345, 'adullact', 4294967295, 1234567899, '1948-02-02', '2007-09-04 09:24:14', '2007-09-04 09:24:14'),
(8, 1, 0, 0, 'laurie', '17828ff61bd0ad2487e39a0d83d5e2bb', 'Lebec', 'Laurie', 'laurie@laurie.fr', 'laurie', 0, 'laurie', 0000000000, 0000000000, '1984-03-13', '2007-09-02 17:00:19', '2007-09-02 17:00:19'),
(9, 2, 0, 0, 'julien', '30d69d863dde81562ce277fbc0a3cf18', 'Calvet', 'Julien', 'julien@julien.org', 'julien', 99999, 'julien', 4294967295, 4294967295, '1947-01-01', '2007-09-02 22:40:21', '2007-09-02 22:40:21'),
(13, 66, 3, 1, 'elu', 'c1347040a278b93357d3075e7cf4bc8b', 'Elu', 'Elu', 'elu', 'elu', 0, 'elu', 0000000000, 0000000000, '1970-01-02', '2007-09-06 14:22:39', '2007-09-14 10:10:54'),
(11, 2, NULL, 0, 'christophe', '60784186ea5b29f3f7e16238805ab329', 'Espiau', 'Christophe', 'christophe@christophe.fr', 'christophe', 123456, 'christophe', 1234567890, 0123456789, '1970-01-02', '2007-09-11 16:34:06', '2007-09-11 16:55:04');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- 
-- Contenu de la table `users_circuits`
-- 

INSERT INTO `users_circuits` (`id`, `user_id`, `circuit_id`, `service_id`, `position`) VALUES 
(1, 8, 1, 4, 2),
(2, 9, 1, 1, 1),
(15, 0, 0, 0, 1),
(18, 11, 1, 2, 3),
(17, 8, 2, 2, 2),
(16, 12, 2, 1, 1),
(20, 20, 3, 0, 0);

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
(8, 2),
(8, 3),
(8, 4),
(9, 1),
(11, 2),
(11, 4),
(12, 1),
(12, 2),
(20, 3),
(20, 4),
(20, 5);

-- --------------------------------------------------------

-- 
-- Structure de la table `votes`
-- 

CREATE TABLE `votes` (
  `id` int(11) NOT NULL auto_increment,
  `liste_id` int(11) NOT NULL default '0',
  `seance_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `votes`
-- 

