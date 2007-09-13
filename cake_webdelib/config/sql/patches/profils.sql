-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lundi 03 Septembre 2007 à 14:41
-- Version du serveur: 5.0.37
-- Version de PHP: 5.0.4
-- 
-- Base de données: `webdelib`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `profils`
-- 
DROP TABLE profils;

CREATE TABLE `profils` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(30) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `profils`
-- 

INSERT INTO `profils` VALUES (62, 0, 'Validateur', '2007-09-03 14:39:20', '2007-09-03 14:39:20');
INSERT INTO `profils` VALUES (63, 62, 'Rédacteur', '2007-09-03 14:39:36', '2007-09-03 14:39:36');
INSERT INTO `profils` VALUES (64, 63, 'Service Assemblées', '2007-09-03 14:40:03', '2007-09-03 14:40:03');
INSERT INTO `profils` VALUES (65, 64, 'Secrétaire de séance', '2007-09-03 14:40:20', '2007-09-03 14:40:20');
INSERT INTO `profils` VALUES (66, 0, 'Elu', '2007-09-03 14:40:35', '2007-09-03 14:40:35');
INSERT INTO `profils` VALUES (67, 0, 'Administrateur', '2007-09-03 14:40:53', '2007-09-03 14:40:53');