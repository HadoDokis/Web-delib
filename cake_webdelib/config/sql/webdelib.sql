-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Vendredi 19 Octobre 2007 à 15:23
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=166 ;

-- 
-- Contenu de la table `acos`
-- 

INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES 
(1, 0, 'Pages', 1, 10),
(2, 1, 'Pages:administration', 2, 3),
(3, 2, 'Pages:home', 4, 5),
(4, 3, 'Pages:index', 6, 7),
(5, 4, 'Pages:display', 8, 9),
(6, 0, 'Typeseances', 11, 22),
(7, 1, 'Typeseances:index', 20, 21),
(8, 2, 'Typeseances:view', 18, 19),
(9, 3, 'Typeseances:add', 16, 17),
(10, 4, 'Typeseances:edit', 14, 15),
(11, 5, 'Typeseances:delete', 12, 13),
(12, 0, 'Localisations', 23, 38),
(13, 1, 'Localisations:getLibelle', 36, 37),
(14, 2, 'Localisations:index', 34, 35),
(15, 3, 'Localisations:view', 32, 33),
(16, 4, 'Localisations:add', 30, 31),
(17, 5, 'Localisations:edit', 28, 29),
(18, 6, 'Localisations:delete', 26, 27),
(19, 7, 'Localisations:changeParentId', 24, 25),
(20, 0, 'Deliberations', 39, 128),
(21, 1, 'Deliberations:index', 126, 127),
(22, 2, 'Deliberations:listerMesProjets', 124, 125),
(23, 3, 'Deliberations:listerProjetsAttribues', 122, 123),
(24, 4, 'Deliberations:listerProjetsNonAttribues', 120, 121),
(25, 5, 'Deliberations:listerProjetsDansMesCircuits', 118, 119),
(26, 6, 'Deliberations:listerProjetsATraiter', 116, 117),
(27, 7, 'Deliberations:getPosition', 114, 115),
(28, 8, 'Deliberations:view', 112, 113),
(29, 9, 'Deliberations:getFileData', 110, 111),
(30, 10, 'Deliberations:saveLocation', 108, 109),
(31, 11, 'Deliberations:changeLocation', 106, 107),
(32, 12, 'Deliberations:add', 104, 105),
(33, 13, 'Deliberations:textsynthese', 102, 103),
(34, 14, 'Deliberations:deliberation', 100, 101),
(35, 15, 'Deliberations:textprojet', 98, 99),
(36, 16, 'Deliberations:edit', 96, 97),
(37, 17, 'Deliberations:getTextSynthese', 94, 95),
(38, 18, 'Deliberations:getTextProjet', 92, 93),
(39, 19, 'Deliberations:getField', 90, 91),
(40, 20, 'Deliberations:delete', 88, 89),
(41, 21, 'Deliberations:convert', 86, 87),
(42, 22, 'Deliberations:attribuercircuit', 84, 85),
(43, 23, 'Deliberations:traiter', 82, 83),
(44, 24, 'Deliberations:chercherVersionAnterieure', 80, 81),
(45, 25, 'Deliberations:transmit', 78, 79),
(46, 26, 'Deliberations:getNatureListe', 76, 77),
(47, 27, 'Deliberations:getMatiereListe', 74, 75),
(48, 28, 'Deliberations:getDateClassification', 72, 73),
(49, 29, 'Deliberations:getClassification', 70, 71),
(50, 30, 'Deliberations:positionner', 68, 69),
(51, 31, 'Deliberations:sortby', 66, 67),
(52, 32, 'Deliberations:getCurrentPosition', 64, 65),
(53, 33, 'Deliberations:getCurrentSeance', 62, 63),
(54, 34, 'Deliberations:getLastPosition', 60, 61),
(55, 35, 'Deliberations:getNextId', 58, 59),
(56, 36, 'Deliberations:listerProjetsServicesAssemblees', 56, 57),
(57, 37, 'Deliberations:textprojetvue', 54, 55),
(58, 38, 'Deliberations:convertDoc2Html', 52, 53),
(59, 39, 'Deliberations:updateword', 50, 51),
(60, 40, 'Deliberations:textsynthesevue', 48, 49),
(61, 41, 'Deliberations:deliberationvue', 46, 47),
(62, 42, 'Deliberations:notifierDossierAtraiter', 44, 45),
(63, 43, 'Deliberations:notifierDossierRefuse', 42, 43),
(64, 44, 'Deliberations:notifierInsertionCircuit', 40, 41),
(65, 0, 'Models', 129, 140),
(66, 1, 'Models:index', 138, 139),
(67, 2, 'Models:add', 136, 137),
(68, 3, 'Models:edit', 134, 135),
(69, 4, 'Models:delete', 132, 133),
(70, 5, 'Models:view', 130, 131),
(71, 0, 'Seances', 141, 190),
(72, 1, 'Seances:index', 188, 189),
(73, 2, 'Seances:view', 186, 187),
(74, 3, 'Seances:add', 184, 185),
(75, 4, 'Seances:edit', 182, 183),
(76, 5, 'Seances:delete', 180, 181),
(77, 6, 'Seances:listerFuturesSeances', 178, 179),
(78, 7, 'Seances:listerAnciennesSeances', 176, 177),
(79, 8, 'Seances:afficherCalendrier', 174, 175),
(80, 9, 'Seances:afficherProjets', 172, 173),
(81, 10, 'Seances:getDate', 170, 171),
(82, 11, 'Seances:getType', 168, 169),
(83, 12, 'Seances:addListUsers', 166, 167),
(84, 13, 'Seances:effacerListe', 164, 165),
(85, 14, 'Seances:generateConvocationList', 162, 163),
(86, 15, 'Seances:sendConvoc', 160, 161),
(87, 16, 'Seances:generateConvocationAnonyme', 158, 159),
(88, 17, 'Seances:generateOrdresDuJour', 156, 157),
(89, 18, 'Seances:listerPresents', 154, 155),
(90, 19, 'Seances:effacerListePresence', 152, 153),
(91, 20, 'Seances:afficherListePresents', 150, 151),
(92, 21, 'Seances:details', 148, 149),
(93, 22, 'Seances:effacerVote', 146, 147),
(94, 23, 'Seances:voter', 144, 145),
(95, 24, 'Seances:saisirDebat', 142, 143),
(96, 0, 'Postseances', 191, 204),
(97, 1, 'Postseances:index', 202, 203),
(98, 2, 'Postseances:afficherProjets', 200, 201),
(99, 3, 'Postseances:generatePvSommaire', 198, 199),
(100, 4, 'Postseances:generatePvComplet', 196, 197),
(101, 5, 'Postseances:generateDeliberation', 194, 195),
(102, 6, 'Postseances:getNom', 192, 193),
(103, 0, 'Votes', 205, 216),
(104, 1, 'Votes:index', 214, 215),
(105, 2, 'Votes:view', 212, 213),
(106, 3, 'Votes:add', 210, 211),
(107, 4, 'Votes:edit', 208, 209),
(108, 5, 'Votes:delete', 206, 207),
(109, 0, 'Users', 217, 236),
(110, 1, 'Users:index', 234, 235),
(111, 2, 'Users:view', 232, 233),
(112, 3, 'Users:add', 230, 231),
(113, 4, 'Users:edit', 228, 229),
(114, 5, 'Users:delete', 226, 227),
(115, 6, 'Users:getNom', 224, 225),
(116, 7, 'Users:getPrenom', 222, 223),
(117, 8, 'Users:login', 220, 221),
(118, 9, 'Users:logout', 218, 219),
(119, 0, 'Services', 237, 254),
(120, 1, 'Services:changeService', 252, 253),
(121, 2, 'Services:getLibelle', 250, 251),
(122, 3, 'Services:index', 248, 249),
(123, 4, 'Services:view', 246, 247),
(124, 5, 'Services:add', 244, 245),
(125, 6, 'Services:edit', 242, 243),
(126, 7, 'Services:delete', 240, 241),
(127, 8, 'Services:changeParentId', 238, 239),
(128, 0, 'Collectivites', 255, 262),
(129, 1, 'Collectivites:index', 260, 261),
(130, 2, 'Collectivites:edit', 258, 259),
(131, 3, 'Collectivites:setLogo', 256, 257),
(132, 0, 'Profils', 263, 276),
(133, 1, 'Profils:index', 274, 275),
(134, 2, 'Profils:view', 272, 273),
(135, 3, 'Profils:add', 270, 271),
(136, 4, 'Profils:edit', 268, 269),
(137, 5, 'Profils:delete', 266, 267),
(138, 6, 'Profils:changeParentId', 264, 265),
(139, 0, 'Circuits', 277, 300),
(140, 1, 'Circuits:view', 298, 299),
(141, 2, 'Circuits:edit', 296, 297),
(142, 3, 'Circuits:delete', 294, 295),
(143, 4, 'Circuits:add', 292, 293),
(144, 5, 'Circuits:index', 290, 291),
(145, 6, 'Circuits:addUser', 288, 289),
(146, 7, 'Circuits:intervertirPosition', 286, 287),
(147, 8, 'Circuits:supprimerUser', 284, 285),
(148, 9, 'Circuits:getCurrentPosition', 282, 283),
(149, 10, 'Circuits:getCurrentCircuit', 280, 281),
(150, 11, 'Circuits:getLastPosition', 278, 279),
(151, 0, 'Annexes', 301, 314),
(152, 1, 'Annexes:delete', 312, 313),
(153, 2, 'Annexes:download', 310, 311),
(154, 3, 'Annexes:getFileType', 308, 309),
(155, 4, 'Annexes:getFileName', 306, 307),
(156, 5, 'Annexes:getSize', 304, 305),
(157, 6, 'Annexes:getData', 302, 303),
(158, 0, 'Themes', 315, 330),
(159, 1, 'Themes:getLibelle', 328, 329),
(160, 2, 'Themes:index', 326, 327),
(161, 3, 'Themes:view', 324, 325),
(162, 4, 'Themes:add', 322, 323),
(163, 5, 'Themes:edit', 320, 321),
(164, 6, 'Themes:delete', 318, 319),
(165, 7, 'Themes:changeParentId', 316, 317);

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
-- Structure de la table `aros`
-- 

CREATE TABLE `aros` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `foreign_key` int(10) unsigned default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Contenu de la table `aros`
-- 

INSERT INTO `aros` (`id`, `foreign_key`, `alias`, `lft`, `rght`) VALUES 
(1, 0, 'Validateur', 1, 2),
(2, 0, 'Redacteur', 3, 4),
(3, 0, 'ServiceAssemblees', 5, 6),
(4, 0, 'SecretaireDeSeance', 7, 8),
(5, 0, 'Administrateur', 9, 12),
(6, 1, 'admin', 10, 11);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=273 ;

-- 
-- Contenu de la table `aros_acos`
-- 

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES 
(1, 5, 2, '1', '1', '1', '1'),
(2, 5, 3, '1', '1', '1', '1'),
(3, 5, 4, '1', '1', '1', '1'),
(4, 5, 5, '1', '1', '1', '1'),
(5, 5, 118, '1', '1', '1', '1'),
(6, 5, 117, '1', '1', '1', '1'),
(7, 5, 115, '1', '1', '1', '1'),
(8, 5, 114, '1', '1', '1', '1'),
(9, 5, 113, '1', '1', '1', '1'),
(10, 5, 116, '1', '1', '1', '1'),
(11, 5, 112, '1', '1', '1', '1'),
(12, 5, 111, '1', '1', '1', '1'),
(13, 5, 110, '1', '1', '1', '1'),
(14, 5, 126, '1', '1', '1', '1'),
(15, 5, 125, '1', '1', '1', '1'),
(16, 5, 124, '1', '1', '1', '1'),
(17, 5, 123, '1', '1', '1', '1'),
(18, 5, 122, '1', '1', '1', '1'),
(19, 5, 121, '1', '1', '1', '1'),
(20, 5, 120, '1', '1', '1', '1'),
(21, 5, 127, '1', '1', '1', '1'),
(22, 5, 11, '1', '1', '1', '1'),
(23, 5, 10, '1', '1', '1', '1'),
(24, 5, 9, '1', '1', '1', '1'),
(25, 5, 8, '1', '1', '1', '1'),
(26, 5, 7, '1', '1', '1', '1'),
(27, 5, 19, '1', '1', '1', '1'),
(28, 5, 18, '1', '1', '1', '1'),
(29, 5, 17, '1', '1', '1', '1'),
(30, 5, 16, '1', '1', '1', '1'),
(31, 5, 15, '1', '1', '1', '1'),
(32, 5, 14, '1', '1', '1', '1'),
(33, 5, 13, '1', '1', '1', '1'),
(34, 5, 131, '1', '1', '1', '1'),
(35, 5, 130, '1', '1', '1', '1'),
(36, 5, 129, '1', '1', '1', '1'),
(37, 5, 138, '1', '1', '1', '1'),
(38, 5, 137, '1', '1', '1', '1'),
(39, 5, 136, '1', '1', '1', '1'),
(40, 5, 135, '1', '1', '1', '1'),
(41, 5, 134, '1', '1', '1', '1'),
(42, 5, 133, '1', '1', '1', '1'),
(43, 5, 150, '1', '1', '1', '1'),
(44, 5, 149, '1', '1', '1', '1'),
(45, 5, 148, '1', '1', '1', '1'),
(46, 5, 147, '1', '1', '1', '1'),
(47, 5, 146, '1', '1', '1', '1'),
(48, 5, 145, '1', '1', '1', '1'),
(49, 5, 144, '1', '1', '1', '1'),
(50, 5, 143, '1', '1', '1', '1'),
(51, 5, 141, '1', '1', '1', '1'),
(52, 5, 142, '1', '1', '1', '1'),
(53, 5, 140, '1', '1', '1', '1'),
(54, 5, 165, '1', '1', '1', '1'),
(55, 5, 164, '1', '1', '1', '1'),
(56, 5, 162, '1', '1', '1', '1'),
(57, 5, 161, '1', '1', '1', '1'),
(58, 5, 160, '1', '1', '1', '1'),
(59, 5, 159, '1', '1', '1', '1'),
(60, 5, 163, '1', '1', '1', '1'),
(61, 2, 64, '1', '1', '1', '1'),
(62, 2, 61, '1', '1', '1', '1'),
(63, 2, 60, '1', '1', '1', '1'),
(64, 2, 62, '1', '1', '1', '1'),
(65, 2, 63, '1', '1', '1', '1'),
(66, 2, 59, '1', '1', '1', '1'),
(67, 2, 58, '1', '1', '1', '1'),
(68, 2, 57, '1', '1', '1', '1'),
(69, 2, 56, '1', '1', '1', '1'),
(70, 2, 55, '1', '1', '1', '1'),
(71, 2, 52, '1', '1', '1', '1'),
(72, 2, 53, '1', '1', '1', '1'),
(73, 2, 51, '1', '1', '1', '1'),
(74, 2, 50, '1', '1', '1', '1'),
(75, 2, 54, '1', '1', '1', '1'),
(76, 2, 49, '1', '1', '1', '1'),
(77, 2, 48, '1', '1', '1', '1'),
(78, 2, 47, '1', '1', '1', '1'),
(79, 2, 45, '1', '1', '1', '1'),
(80, 2, 46, '1', '1', '1', '1'),
(81, 2, 44, '1', '1', '1', '1'),
(82, 2, 43, '1', '1', '1', '1'),
(83, 2, 42, '1', '1', '1', '1'),
(84, 2, 40, '1', '1', '1', '1'),
(85, 2, 39, '1', '1', '1', '1'),
(86, 2, 38, '1', '1', '1', '1'),
(87, 2, 41, '1', '1', '1', '1'),
(88, 2, 37, '1', '1', '1', '1'),
(89, 2, 35, '1', '1', '1', '1'),
(90, 2, 36, '1', '1', '1', '1'),
(91, 2, 32, '1', '1', '1', '1'),
(92, 2, 33, '1', '1', '1', '1'),
(93, 2, 34, '1', '1', '1', '1'),
(94, 2, 30, '1', '1', '1', '1'),
(95, 2, 29, '1', '1', '1', '1'),
(96, 2, 28, '1', '1', '1', '1'),
(97, 2, 27, '1', '1', '1', '1'),
(98, 2, 26, '1', '1', '1', '1'),
(99, 2, 25, '1', '1', '1', '1'),
(100, 2, 31, '1', '1', '1', '1'),
(101, 2, 24, '1', '1', '1', '1'),
(102, 2, 22, '1', '1', '1', '1'),
(103, 2, 21, '1', '1', '1', '1'),
(104, 2, 23, '1', '1', '1', '1'),
(105, 2, 2, '-1', '-1', '-1', '-1'),
(106, 2, 3, '1', '1', '1', '1'),
(107, 2, 4, '1', '1', '1', '1'),
(108, 2, 5, '1', '1', '1', '1'),
(109, 1, 3, '1', '1', '1', '1'),
(110, 1, 5, '1', '1', '1', '1'),
(111, 1, 4, '1', '1', '1', '1'),
(112, 3, 3, '1', '1', '1', '1'),
(113, 3, 5, '1', '1', '1', '1'),
(114, 3, 4, '1', '1', '1', '1'),
(115, 4, 3, '1', '1', '1', '1'),
(116, 4, 4, '1', '1', '1', '1'),
(117, 4, 5, '1', '1', '1', '1'),
(118, 4, 95, '1', '1', '1', '1'),
(119, 4, 94, '1', '1', '1', '1'),
(120, 4, 93, '1', '1', '1', '1'),
(121, 4, 92, '1', '1', '1', '1'),
(122, 4, 90, '1', '1', '1', '1'),
(123, 4, 91, '1', '1', '1', '1'),
(124, 4, 89, '1', '1', '1', '1'),
(125, 4, 88, '1', '1', '1', '1'),
(126, 4, 86, '1', '1', '1', '1'),
(127, 4, 83, '1', '1', '1', '1'),
(128, 4, 85, '1', '1', '1', '1'),
(129, 4, 87, '1', '1', '1', '1'),
(130, 4, 84, '1', '1', '1', '1'),
(131, 4, 82, '1', '1', '1', '1'),
(132, 4, 81, '1', '1', '1', '1'),
(133, 4, 80, '1', '1', '1', '1'),
(134, 4, 79, '1', '1', '1', '1'),
(135, 4, 78, '1', '1', '1', '1'),
(136, 4, 76, '1', '1', '1', '1'),
(137, 4, 77, '1', '1', '1', '1'),
(138, 4, 74, '1', '1', '1', '1'),
(139, 4, 75, '1', '1', '1', '1'),
(140, 4, 73, '1', '1', '1', '1'),
(141, 4, 72, '1', '1', '1', '1'),
(142, 1, 117, '1', '1', '1', '1'),
(143, 1, 118, '1', '1', '1', '1'),
(144, 1, 116, '1', '1', '1', '1'),
(145, 1, 115, '1', '1', '1', '1'),
(146, 2, 118, '1', '1', '1', '1'),
(147, 2, 117, '1', '1', '1', '1'),
(148, 2, 116, '1', '1', '1', '1'),
(149, 2, 115, '1', '1', '1', '1'),
(150, 3, 118, '1', '1', '1', '1'),
(151, 3, 117, '1', '1', '1', '1'),
(152, 3, 116, '1', '1', '1', '1'),
(153, 3, 115, '1', '1', '1', '1'),
(154, 4, 118, '1', '1', '1', '1'),
(155, 4, 117, '1', '1', '1', '1'),
(156, 4, 116, '1', '1', '1', '1'),
(157, 3, 95, '1', '1', '1', '1'),
(158, 3, 94, '1', '1', '1', '1'),
(159, 3, 93, '1', '1', '1', '1'),
(160, 3, 92, '1', '1', '1', '1'),
(161, 3, 91, '1', '1', '1', '1'),
(162, 3, 90, '1', '1', '1', '1'),
(163, 3, 89, '1', '1', '1', '1'),
(164, 3, 87, '1', '1', '1', '1'),
(165, 3, 86, '1', '1', '1', '1'),
(166, 3, 85, '1', '1', '1', '1'),
(167, 3, 84, '1', '1', '1', '1'),
(168, 3, 72, '1', '1', '1', '1'),
(169, 3, 73, '1', '1', '1', '1'),
(170, 3, 74, '1', '1', '1', '1'),
(171, 3, 75, '1', '1', '1', '1'),
(172, 3, 77, '1', '1', '1', '1'),
(173, 3, 76, '1', '1', '1', '1'),
(174, 3, 79, '1', '1', '1', '1'),
(175, 3, 80, '1', '1', '1', '1'),
(176, 3, 81, '1', '1', '1', '1'),
(177, 3, 82, '1', '1', '1', '1'),
(178, 3, 78, '1', '1', '1', '1'),
(179, 3, 83, '1', '1', '1', '1'),
(180, 3, 64, '1', '1', '1', '1'),
(181, 3, 63, '1', '1', '1', '1'),
(182, 3, 62, '1', '1', '1', '1'),
(183, 3, 61, '1', '1', '1', '1'),
(184, 3, 60, '1', '1', '1', '1'),
(185, 3, 58, '1', '1', '1', '1'),
(186, 3, 57, '1', '1', '1', '1'),
(187, 3, 59, '1', '1', '1', '1'),
(188, 3, 56, '1', '1', '1', '1'),
(189, 3, 55, '1', '1', '1', '1'),
(190, 3, 53, '1', '1', '1', '1'),
(191, 3, 52, '1', '1', '1', '1'),
(192, 3, 54, '1', '1', '1', '1'),
(193, 3, 50, '1', '1', '1', '1'),
(194, 3, 49, '1', '1', '1', '1'),
(195, 3, 51, '1', '1', '1', '1'),
(196, 3, 46, '1', '1', '1', '1'),
(197, 3, 47, '1', '1', '1', '1'),
(198, 3, 45, '1', '1', '1', '1'),
(199, 3, 44, '1', '1', '1', '1'),
(200, 3, 48, '1', '1', '1', '1'),
(201, 3, 43, '1', '1', '1', '1'),
(202, 3, 42, '1', '1', '1', '1'),
(203, 3, 40, '1', '1', '1', '1'),
(204, 3, 39, '1', '1', '1', '1'),
(205, 3, 38, '1', '1', '1', '1'),
(206, 3, 41, '1', '1', '1', '1'),
(207, 3, 37, '1', '1', '1', '1'),
(208, 3, 34, '1', '1', '1', '1'),
(209, 3, 33, '1', '1', '1', '1'),
(210, 3, 32, '1', '1', '1', '1'),
(211, 3, 31, '1', '1', '1', '1'),
(212, 3, 35, '1', '1', '1', '1'),
(213, 3, 36, '1', '1', '1', '1'),
(214, 3, 27, '1', '1', '1', '1'),
(215, 3, 25, '1', '1', '1', '1'),
(216, 3, 23, '1', '1', '1', '1'),
(217, 3, 30, '1', '1', '1', '1'),
(218, 3, 28, '1', '1', '1', '1'),
(219, 3, 29, '1', '1', '1', '1'),
(220, 3, 26, '1', '1', '1', '1'),
(221, 3, 21, '1', '1', '1', '1'),
(222, 1, 64, '1', '1', '1', '1'),
(223, 1, 63, '1', '1', '1', '1'),
(224, 1, 62, '1', '1', '1', '1'),
(225, 1, 60, '1', '1', '1', '1'),
(226, 1, 61, '1', '1', '1', '1'),
(227, 1, 58, '1', '1', '1', '1'),
(228, 1, 59, '1', '1', '1', '1'),
(229, 1, 57, '1', '1', '1', '1'),
(230, 1, 56, '1', '1', '1', '1'),
(231, 1, 55, '1', '1', '1', '1'),
(232, 1, 54, '1', '1', '1', '1'),
(233, 1, 53, '1', '1', '1', '1'),
(234, 1, 51, '1', '1', '1', '1'),
(235, 1, 50, '1', '1', '1', '1'),
(236, 1, 52, '1', '1', '1', '1'),
(237, 1, 49, '1', '1', '1', '1'),
(238, 1, 48, '1', '1', '1', '1'),
(239, 1, 47, '1', '1', '1', '1'),
(240, 1, 46, '1', '1', '1', '1'),
(241, 1, 45, '1', '1', '1', '1'),
(242, 1, 44, '1', '1', '1', '1'),
(243, 1, 43, '1', '1', '1', '1'),
(244, 1, 41, '1', '1', '1', '1'),
(245, 1, 42, '1', '1', '1', '1'),
(246, 1, 39, '1', '1', '1', '1'),
(247, 1, 38, '1', '1', '1', '1'),
(248, 1, 37, '1', '1', '1', '1'),
(249, 1, 36, '-1', '-1', '-1', '-1'),
(250, 1, 34, '-1', '-1', '-1', '-1'),
(251, 1, 31, '1', '1', '1', '1'),
(252, 1, 30, '1', '1', '1', '1'),
(253, 1, 28, '1', '1', '1', '1'),
(254, 1, 29, '1', '1', '1', '1'),
(255, 1, 27, '1', '1', '1', '1'),
(256, 1, 26, '1', '1', '1', '1'),
(257, 1, 25, '1', '1', '1', '1'),
(258, 1, 24, '1', '1', '1', '1'),
(259, 1, 21, '1', '1', '1', '1'),
(260, 1, 22, '1', '1', '1', '1'),
(261, 3, 22, '1', '1', '1', '1'),
(262, 4, 102, '1', '1', '1', '1'),
(263, 4, 101, '1', '1', '1', '1'),
(264, 4, 100, '1', '1', '1', '1'),
(265, 4, 99, '1', '1', '1', '1'),
(266, 4, 98, '1', '1', '1', '1'),
(267, 4, 97, '1', '1', '1', '1'),
(268, 4, 39, '1', '1', '1', '1'),
(269, 4, 45, '1', '1', '1', '1'),
(270, 4, 70, '1', '1', '1', '1'),
(271, 4, 54, '1', '1', '1', '1'),
(272, 3, 88, '1', '1', '1', '1');

-- --------------------------------------------------------

-- 
-- Structure de la table `circuits`
-- 

CREATE TABLE `circuits` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(50) NOT NULL default '',
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
  `deliberation` longblob,
  `date_limite` date default NULL,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
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
(1, 'convocation', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c64697620616c69676e3d227269676874223e266e6273703b3c2f6469763e0d0a3c7020616c69676e3d227269676874223e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d226c656674223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e23414452455353455f454c55233c2f6469763e0d0a3c7020616c69676e3d226c656674223e2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e20202020202020202020202020202020202020202020202020202020202020202020202020202020436f6e766f636174696f6e2061752023545950455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d2263656e746572223e0d0a3c68313e53414c555420746f7574206c65206d6f4e64653c2f68313e0d0a3c2f6469763e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e4a276169206c27686f6e6e65757220646520766f757320696e76697465722061752023545950455f5345414e434523207175692061757261206c696575206c652023444154455f5345414e4345232064616e733c6272202f3e0d0a234c4945555f5345414e4345232e3c6272202f3e0d0a3c2f7374726f6e673e3c6272202f3e0d0a4a6520766f7573207072696520646520636f6972652c204d6164616d652c204d6f6e73696575722c20656e206c276173737572616e6365206465206d6120636f6e736964266561637574653b726174696f6e2064697374696e6775266561637574653b652e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e4f72647265206475206a6f7572203a3c2f7374726f6e673e3c2f703e),
(2, 'ordre du jour', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c6272202f3e0d0a23414452455353455f454c55233c6272202f3e0d0a2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b3c2f7374726f6e673e3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b204f72647265206475206a6f75723c2f7374726f6e673e206475203c7374726f6e673e23545950455f5345414e4345233c2f7374726f6e673e206475203c7374726f6e673e23444154455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Contenu de la table `users`
-- 

INSERT INTO `users` (`id`, `profil_id`, `service_id`, `statut`, `login`, `password`, `nom`, `prenom`, `email`, `adresse`, `CP`, `ville`, `teldom`, `telmobile`, `date_naissance`, `accept_notif`, `created`, `modified`) VALUES 
(1, 1, 5, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'administrateur', 'administrateur', 'admin@adullact.org', '335 cour Messier', 34000, 'Montpellier', 0000000000, 0000000000, '1981-07-11', 0, '0000-00-00 00:00:00', '2007-10-17 16:42:53');

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

INSERT INTO `users_services` (`user_id`, `service_id`) VALUES 
(1, 1);

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

