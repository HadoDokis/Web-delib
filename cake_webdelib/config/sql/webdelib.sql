-- phpMyAdmin SQL Dump
-- version 2.6.2-Debian-3sarge3
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mardi 04 Septembre 2007 à 09:27
-- Version du serveur: 4.1.11
-- Version de PHP: 4.3.10-22
-- 
-- Base de données: `webdelib`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `annexes`
-- 

CREATE TABLE `annexes` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL default '0',
  `chemin` text NOT NULL,
  `titre` varchar(100) NOT NULL default '',
  `reference_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Contenu de la table `circuits`
-- 

INSERT INTO `circuits` VALUES (1, 'soutenance');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Contenu de la table `deliberations`
-- 

INSERT INTO `deliberations` VALUES (4, 1, 1, 0, 0, 8, 10, 1, 'test', 'test', '45', '', 0x3c703e74657874652064752070726f6a65743c2f703e, 0x3c703e7465787465206465206c61206e6f74652073796e7468266567726176653b73653c2f703e, '2007-09-03 02:29:16', 1, '2007-09-03 02:26:23', '2007-09-03 02:29:16');
INSERT INTO `deliberations` VALUES (3, 1, 1, 0, 0, 7, 10, 1, 'test', 'test', '567', '', 0x3c703e65737361693c2f703e0d0a3c703e3c666f6e742073697a653d2234223e3c656d3e3c7374726f6e673e65737361693c2f7374726f6e673e3c2f656d3e3c2f666f6e743e3c2f703e0d0a3c703e3c666f6e7420636f6c6f723d2223666630303030223e65737361693c2f666f6e743e3c2f703e, 0x3c703e7465737474657374266e6273703b266e6273703b3c2f703e, '2007-09-02 22:48:42', 1, '2007-09-02 22:47:59', '2007-09-02 22:48:42');
INSERT INTO `deliberations` VALUES (5, 0, 1, 0, 0, 8, 10, 2, 't', 't', 't', '', NULL, NULL, NULL, 0, '2007-09-03 09:55:43', '2007-09-03 09:55:43');
INSERT INTO `deliberations` VALUES (6, 1, 1, 0, 0, 8, 10, 4, 'test', 'test titre', '56', '', 0x3c703e746573742070726f6a65743c2f703e, 0x3c703e6e6f74652064652073796e74686573653c2f703e, '2007-09-03 10:18:47', 1, '2007-09-03 10:17:17', '2007-09-03 10:18:47');

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
  `libelle` varchar(30) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Contenu de la table `profils`
-- 

INSERT INTO `profils` VALUES (1, 'Redacteur', '2007-08-02 10:27:30', '2007-08-02 10:27:30');
INSERT INTO `profils` VALUES (2, 'Administrateur', '2007-08-02 10:27:34', '2007-08-02 10:27:34');
INSERT INTO `profils` VALUES (3, 'Service des assemblées', '2007-08-02 10:27:42', '2007-08-02 10:27:42');
INSERT INTO `profils` VALUES (4, 'Rapporteur', '2007-08-02 10:27:47', '2007-08-02 10:27:47');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Contenu de la table `seances`
-- 

INSERT INTO `seances` VALUES (1, 1, '2007-09-02 22:46:53', '2007-09-02 22:46:53', '2008-05-07 17:03:00');
INSERT INTO `seances` VALUES (2, 2, '2007-09-02 22:47:09', '2007-09-02 22:47:09', '2007-10-06 16:02:00');
INSERT INTO `seances` VALUES (3, 3, '2007-09-02 22:47:25', '2007-09-02 22:47:25', '2015-09-06 17:13:00');
INSERT INTO `seances` VALUES (4, 2, '2007-09-03 02:34:39', '2007-09-03 02:34:39', '2007-10-08 13:07:00');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Contenu de la table `services`
-- 

INSERT INTO `services` VALUES (1, 'Informatique', '2007-08-02 10:25:40', '2007-08-02 10:25:40');
INSERT INTO `services` VALUES (2, 'Urbanisme', '2007-08-02 10:25:48', '2007-08-02 10:25:48');
INSERT INTO `services` VALUES (3, 'Voirie', '2007-08-02 10:25:59', '2007-08-02 10:25:59');
INSERT INTO `services` VALUES (4, 'Education', '2007-08-02 10:26:03', '2007-08-02 10:26:03');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Contenu de la table `themes`
-- 

INSERT INTO `themes` VALUES (1, 'Amenagement du territoire', '2007-08-02 10:26:24', '2007-08-02 10:26:24');
INSERT INTO `themes` VALUES (2, 'Entretien des routes', '2007-08-02 10:26:37', '2007-08-02 10:26:37');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- 
-- Contenu de la table `traitements`
-- 

INSERT INTO `traitements` VALUES (1, 3, 1, 1, '2007-09-02 22:52:01');
INSERT INTO `traitements` VALUES (2, 3, 1, 2, '2007-09-02 22:52:38');
INSERT INTO `traitements` VALUES (3, 3, 1, 3, '0000-00-00 00:00:00');
INSERT INTO `traitements` VALUES (4, 2, 1, 1, '0000-00-00 00:00:00');
INSERT INTO `traitements` VALUES (5, 4, 1, 1, '2007-09-03 02:29:42');
INSERT INTO `traitements` VALUES (6, 4, 1, 2, '2007-09-03 02:30:03');
INSERT INTO `traitements` VALUES (7, 4, 1, 3, '0000-00-00 00:00:00');
INSERT INTO `traitements` VALUES (8, 6, 1, 1, '2007-09-03 10:19:22');
INSERT INTO `traitements` VALUES (9, 6, 1, 2, '2007-09-03 10:19:50');
INSERT INTO `traitements` VALUES (10, 6, 1, 3, '0000-00-00 00:00:00');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Contenu de la table `typeseances`
-- 

INSERT INTO `typeseances` VALUES (1, 'Conseil municipal', 0, '2007-08-02 10:26:47', '2007-08-02 10:26:47');
INSERT INTO `typeseances` VALUES (2, 'Conseil général', 0, '2007-08-02 10:26:53', '2007-08-02 10:26:53');
INSERT INTO `typeseances` VALUES (3, 'Commission permanente', 0, '2007-08-02 10:27:01', '2007-08-02 10:27:01');

-- --------------------------------------------------------

-- 
-- Structure de la table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- 
-- Contenu de la table `users`
-- 

INSERT INTO `users` VALUES (11, 4, 0, 'christophe', 'b2bb80563783f43df1d8ac913003ca7a', 'christophe', 'christophe', 'christophe@christophe.fr', 'christophe', 123456, 'christophe', 1234567890, 0000123890, '1970-01-01', '2007-09-02 22:44:07', '2007-09-02 22:44:07');
INSERT INTO `users` VALUES (10, 3, 1, 'melanie', '73aaec6dc33b96597d8019f7553e96a2', 'melanie', 'melanie', 'melanie@melanie.fr', 'melanie', 67867, 'melanie', 1234567890, 1234567890, '1948-02-02', '2007-09-02 22:41:05', '2007-09-02 22:41:05');
INSERT INTO `users` VALUES (12, 2, 0, 'adullact', '9825b1be57b3d7912cf79db3d16aa501', 'adullact', 'adullact@adullact.org', 'adullact@adullact.org', 'adullact', 12345, 'adullact', 4294967295, 1234567899, '1948-02-02', '2007-09-04 09:24:14', '2007-09-04 09:24:14');
INSERT INTO `users` VALUES (8, 1, 0, 'laurie', '17828ff61bd0ad2487e39a0d83d5e2bb', 'laurie', 'laurie', 'laurie@laurie.fr', 'laurie', 0, 'laurie', 0000000000, 0000000000, '1984-03-13', '2007-09-02 17:00:19', '2007-09-02 17:00:19');
INSERT INTO `users` VALUES (9, 2, 0, 'julien', '30d69d863dde81562ce277fbc0a3cf18', 'julien', 'julien', 'julien@julien.org', 'julien', 99999, 'julien', 4294967295, 4294967295, '1947-01-01', '2007-09-02 22:40:21', '2007-09-02 22:40:21');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Contenu de la table `users_circuits`
-- 

INSERT INTO `users_circuits` VALUES (1, 8, 1, 4, 1);
INSERT INTO `users_circuits` VALUES (2, 9, 1, 1, 3);
INSERT INTO `users_circuits` VALUES (3, 10, 1, 2, 2);
INSERT INTO `users_circuits` VALUES (4, 11, 1, 2, 4);

-- --------------------------------------------------------

-- 
-- Structure de la table `users_listepresences`
-- 

CREATE TABLE `users_listepresences` (
  `user_id` int(11) NOT NULL default '0',
  `liste_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`liste_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `users_listepresences`
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

INSERT INTO `users_services` VALUES (8, 2);
INSERT INTO `users_services` VALUES (8, 3);
INSERT INTO `users_services` VALUES (8, 4);
INSERT INTO `users_services` VALUES (9, 1);
INSERT INTO `users_services` VALUES (10, 2);
INSERT INTO `users_services` VALUES (11, 2);
INSERT INTO `users_services` VALUES (11, 3);
INSERT INTO `users_services` VALUES (12, 1);
INSERT INTO `users_services` VALUES (12, 2);

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

