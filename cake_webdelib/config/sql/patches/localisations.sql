-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Vendredi 12 Octobre 2007 à 09:51
-- Version du serveur: 5.0.38
-- Version de PHP: 5.2.1
-- 
-- Base de données: `webdelib`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `localisations`
-- 

CREATE TABLE `localisations` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(30) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Contenu de la table `localisations`
-- 

INSERT INTO `localisations` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES 
(1, 0, 'Cantons', '2007-10-11 16:44:17', '2007-10-11 16:44:17'),
(2, 0, 'Villes', '2007-10-11 16:48:22', '2007-10-11 16:48:22'),
(3, 2, 'Montpellier', '2007-10-11 16:44:44', '2007-10-11 16:44:44'),
(5, 0, 'Pays', '2007-10-11 17:19:21', '2007-10-11 17:19:21'),
(6, 5, 'écrins', '2007-10-11 17:19:35', '2007-10-11 17:19:35'),
(7, 0, 'Conseil général', '2007-10-11 17:20:42', '2007-10-11 17:20:42'),
(8, 0, 'Conseil Régional', '2007-10-11 17:20:51', '2007-10-11 17:20:51'),
(9, 2, 'Castelnau le lez', '2007-10-12 09:35:34', '2007-10-12 09:35:34');
