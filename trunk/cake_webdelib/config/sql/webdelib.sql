-- phpMyAdmin SQL Dump
-- version 2.6.2-Debian-3sarge3
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lundi 13 Août 2007 à 16:46
-- Version du serveur: 4.1.11
-- Version de PHP: 5.2.0-8+etch3~bpo.1
-- 
-- Base de données: `webdelib`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `agents`
-- 

CREATE TABLE `agents` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
  `login` varchar(50) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `adresse` varchar(255) NOT NULL default '',
  `CP` int(11) NOT NULL default '0',
  `ville` varchar(50) NOT NULL default '',
  `teldom` int(11) default '0',
  `telmobile` int(11) default '0',
  `date_naissance` date default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `agents`
-- 

INSERT INTO `agents` (`id`, `profil_id`, `login`, `password`, `nom`, `prenom`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES (1, 2, 'jing', '43ae0add70fd1bda16d0700282cd8d2d', 'jing', 'jing', '', 0, '', 0, 0, NULL, '2007-08-13 16:41:36', '2007-08-13 16:41:36');
INSERT INTO `agents` (`id`, `profil_id`, `login`, `password`, `nom`, `prenom`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES (2, 4, 'mel', '0ef174fc614c8d61e2d63329ef7f46c0', 'mel', 'mel', '', 0, '', 0, 0, NULL, '2007-08-13 16:42:55', '2007-08-13 16:42:55');
INSERT INTO `agents` (`id`, `profil_id`, `login`, `password`, `nom`, `prenom`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES (3, 2, 'christophe', '60784186ea5b29f3f7e16238805ab329', 'christophe', 'christophe', '', 0, '', 0, 0, NULL, '2007-08-13 16:43:07', '2007-08-13 16:43:07');
INSERT INTO `agents` (`id`, `profil_id`, `login`, `password`, `nom`, `prenom`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES (4, 1, 'ju', 'e744f57da9e5a4bb6ec8ba3bc0ad3e4e', 'ju', 'ju', '', 0, '', 0, 0, NULL, '2007-08-13 16:43:18', '2007-08-13 16:43:18');
INSERT INTO `agents` (`id`, `profil_id`, `login`, `password`, `nom`, `prenom`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES (5, 2, 'françois', '36cebf4a8c6412f8f15d4e60ccbceca9', 'françois', 'françois', '', 0, '', 0, 0, NULL, '2007-08-13 16:43:31', '2007-08-13 16:43:31');
INSERT INTO `agents` (`id`, `profil_id`, `login`, `password`, `nom`, `prenom`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `created`, `modified`) VALUES (6, 1, 'marine', 'b329f324cc17d6221a385ea1afb3a289', 'marine', 'marine', '', 0, '', 0, 0, NULL, '2007-08-13 16:43:48', '2007-08-13 16:43:48');

-- --------------------------------------------------------

-- 
-- Structure de la table `agents_circuits`
-- 

CREATE TABLE `agents_circuits` (
  `id` int(11) NOT NULL auto_increment,
  `agent_id` int(11) NOT NULL default '0',
  `circuit_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `agents_circuits`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `agents_listepresences`
-- 

CREATE TABLE `agents_listepresences` (
  `agent_id` int(11) NOT NULL default '0',
  `liste_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`agent_id`,`liste_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `agents_listepresences`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `agents_services`
-- 

CREATE TABLE `agents_services` (
  `agent_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`agent_id`,`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `agents_services`
-- 

INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (1, 2);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (2, 2);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (3, 1);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (3, 2);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (4, 4);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (5, 3);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (6, 2);
INSERT INTO `agents_services` (`agent_id`, `service_id`) VALUES (6, 3);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `circuits`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `date_session` datetime NOT NULL default '0000-00-00 00:00:00',
  `objet` varchar(100) NOT NULL default '',
  `titre` varchar(100) NOT NULL default '',
  `num_delib` varchar(10) NOT NULL default '',
  `num_pref` varchar(10) NOT NULL default '',
  `texte_projet` varchar(200) NOT NULL default '',
  `texte_synthese` varchar(200) NOT NULL default '',
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `deliberations`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `profils`
-- 

INSERT INTO `profils` (`id`, `libelle`, `created`, `modified`) VALUES (1, 'Redacteur', '2007-08-02 10:27:30', '2007-08-02 10:27:30');
INSERT INTO `profils` (`id`, `libelle`, `created`, `modified`) VALUES (2, 'Administrateur', '2007-08-02 10:27:34', '2007-08-02 10:27:34');
INSERT INTO `profils` (`id`, `libelle`, `created`, `modified`) VALUES (3, 'Service des assemblÃ©es', '2007-08-02 10:27:42', '2007-08-02 10:27:42');
INSERT INTO `profils` (`id`, `libelle`, `created`, `modified`) VALUES (4, 'Rapporteur', '2007-08-02 10:27:47', '2007-08-02 10:27:47');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `seances`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `services`
-- 

INSERT INTO `services` (`id`, `libelle`, `created`, `modified`) VALUES (1, 'Informatique', '2007-08-02 10:25:40', '2007-08-02 10:25:40');
INSERT INTO `services` (`id`, `libelle`, `created`, `modified`) VALUES (2, 'Urbanisme', '2007-08-02 10:25:48', '2007-08-02 10:25:48');
INSERT INTO `services` (`id`, `libelle`, `created`, `modified`) VALUES (3, 'Voirie', '2007-08-02 10:25:59', '2007-08-02 10:25:59');
INSERT INTO `services` (`id`, `libelle`, `created`, `modified`) VALUES (4, 'Education', '2007-08-02 10:26:03', '2007-08-02 10:26:03');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `themes`
-- 

INSERT INTO `themes` (`id`, `libelle`, `created`, `modified`) VALUES (1, 'Amenagement du territoire', '2007-08-02 10:26:24', '2007-08-02 10:26:24');
INSERT INTO `themes` (`id`, `libelle`, `created`, `modified`) VALUES (2, 'Entretien des routes', '2007-08-02 10:26:37', '2007-08-02 10:26:37');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

-- 
-- Structure de la table `typeseances`
-- 

CREATE TABLE `typeseances` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(50) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `typeseances`
-- 

INSERT INTO `typeseances` (`id`, `libelle`, `created`, `modified`) VALUES (1, 'Conseil municipal', '2007-08-02 10:26:47', '2007-08-02 10:26:47');
INSERT INTO `typeseances` (`id`, `libelle`, `created`, `modified`) VALUES (2, 'Conseil gÃ©nÃ©ral', '2007-08-02 10:26:53', '2007-08-02 10:26:53');
INSERT INTO `typeseances` (`id`, `libelle`, `created`, `modified`) VALUES (3, 'Commission permanente', '2007-08-02 10:27:01', '2007-08-02 10:27:01');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `votes`
-- 

        