-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Vendredi 21 Décembre 2007 à 17:19
-- Version du serveur: 5.0.38
-- Version de PHP: 5.2.1
--
-- Base de données: `webdelib`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `acos`
--


-- --------------------------------------------------------

--
-- Structure de la table `annexes`
--

CREATE TABLE `annexes` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation_id` int(11) NOT NULL,
  `seance_id` int(11) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `type` char(1) NOT NULL,
  `filename` varchar(75) NOT NULL,
  `filetype` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `data` mediumblob NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `aros`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `aros_acos`
--


-- --------------------------------------------------------

--
-- Structure de la table `circuits`
--

CREATE TABLE `circuits` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `telephone` int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `collectivites`
--

INSERT INTO `collectivites` (`id`, `nom`, `adresse`, `CP`, `ville`, `telephone`) VALUES
(1, 'Adullact', '335, Cour Messier', 34000, 'Montpellier', 0467650588);

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
  `redacteur_id` int(11) NOT NULL default '0',
  `rapporteur_id` int(11) NOT NULL default '0',
  `seance_id` int(11) default NULL,
  `position` int(4) NOT NULL,
  `anterieure_id` int(11) NOT NULL,
  `objet` varchar(1000) NOT NULL,
  `titre` varchar(1000) NOT NULL,
  `num_delib` varchar(10) NOT NULL default '',
  `num_pref` varchar(1000) NOT NULL,
  `texte_projet` longblob,
  `texte_synthese` longblob,
  `deliberation` longblob,
  `date_limite` date default NULL,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `reporte` tinyint(1) NOT NULL default '0',
  `debat` longblob,
  `localisation1_id` int(11) NOT NULL default '0',
  `localisation2_id` int(11) NOT NULL default '0',
  `localisation3_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `deliberations`
--


-- --------------------------------------------------------

--
-- Structure de la table `listepresences`
--

CREATE TABLE `listepresences` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL,
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
-- Structure de la table `localisations`
--

CREATE TABLE `localisations` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `localisations`
--


-- --------------------------------------------------------

--
-- Structure de la table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `texte` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `models`
--

INSERT INTO `models` (`id`, `type`, `texte`) VALUES
(1, 'convocation', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c6272202f3e0d0a23414452455353455f454c55233c6272202f3e0d0a2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e20202020202020202020202020202020202020202020202020202020202020202020202020202020436f6e766f636174696f6e2061752023545950455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c6272202f3e0d0a4d6164616d652c204d6f6e73696575722c3c6272202f3e0d0a3c7374726f6e673e4a276169206c27686f6e6e65757220646520766f757320696e76697465722061752023545950455f5345414e434523207175692061757261206c696575206c652023444154455f5345414e4345232064616e733c6272202f3e0d0a234c4945555f5345414e4345232e3c6272202f3e0d0a3c2f7374726f6e673e3c6272202f3e0d0a4a6520766f7573207072696520646520636f6972652c204d6164616d652c204d6f6e73696575722c20656e206c276173737572616e6365206465206d6120636f6e736964266561637574653b726174696f6e2064697374696e6775266561637574653b652e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e4f72647265206475206a6f7572203a3c2f7374726f6e673e3c2f703e),
(2, 'ordre du jour', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c6272202f3e0d0a23414452455353455f454c55233c6272202f3e0d0a2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b3c2f7374726f6e673e3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b204f72647265206475206a6f75723c2f7374726f6e673e206475203c7374726f6e673e23545950455f5345414e4345233c2f7374726f6e673e206475203c7374726f6e673e23444154455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e);

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `profils`
--

INSERT INTO `profils` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(3, 0, 'Validateur', '2007-09-03 14:39:20', '2007-09-03 14:39:20'),
(2, 0, 'Rédacteur', '2007-09-03 14:39:36', '2007-09-03 14:39:36'),
(4, 0, 'ServiceAssemblees', '2007-09-03 14:40:03', '2007-09-03 14:40:03'),
(5, 0, 'SecretaireDeSeance', '2007-09-03 14:40:20', '2007-09-03 14:40:20'),
(1, 0, 'Administrateur', '2007-09-03 14:40:53', '2007-09-03 14:40:53');

-- --------------------------------------------------------

--
-- Structure de la table `seances`
--

CREATE TABLE `seances` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '0',
  `debat_global` longblob NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `seances`
--


-- --------------------------------------------------------

--
-- Structure de la table `seances_users`
--

CREATE TABLE `seances_users` (
  `id` int(11) NOT NULL auto_increment,
  `seance_id` int(9) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `seances_users`
--


-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `services`
--

INSERT INTO `services` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(1, 0, 'Informatique', '2007-10-02 14:21:47', '2007-10-02 14:23:16'),
(2, 1, 'Réseau', '2007-10-02 14:24:50', '2007-10-02 16:00:29'),
(3, 1, 'Développement', '2007-10-02 14:25:00', '2007-10-02 15:59:58'),
(4, 3, 'PHP', '2007-10-02 14:25:20', '2007-10-02 14:25:20');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `traitements`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeseances`
--

CREATE TABLE `typeseances` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  `retard` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `typeseances`
--


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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `profil_id`, `service_id`, `statut`, `login`, `password`, `nom`, `prenom`, `email`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `accept_notif`, `created`, `modified`) VALUES
(1, 1, 1, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrateur', 'Administrateur', 'admin@adullact.org', '335 cour Messier', 34000, 'Montpellier', 0000000000, 0000000000, '1981-07-11', 0, '0000-00-00 00:00:00', '2007-12-21 17:13:22');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users_services`
--


-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `resultat` int(1) NOT NULL,
  `commentaire` varchar(500) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `votes`
--

