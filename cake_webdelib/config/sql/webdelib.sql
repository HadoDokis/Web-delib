-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 14 Mars 2008 à 15:19
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `webdelib`
--

-- --------------------------------------------------------

--
-- Structure de la table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=270 ;

--
-- Contenu de la table `acos`
--

INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES
(1, 0, 'Typeseances', 1, 12),
(2, 1, 'Typeseances:index', 10, 11),
(3, 2, 'Typeseances:view', 8, 9),
(4, 3, 'Typeseances:add', 6, 7),
(5, 4, 'Typeseances:edit', 4, 5),
(6, 5, 'Typeseances:delete', 2, 3),
(7, 0, 'Compteurs', 13, 22),
(8, 1, 'Compteurs:suivant', 20, 21),
(9, 2, 'Compteurs:index', 18, 19),
(10, 3, 'Compteurs:view', 16, 17),
(11, 4, 'Compteurs:edit', 14, 15),
(12, 0, 'Commentaires', 23, 32),
(13, 1, 'Commentaires:add', 30, 31),
(14, 2, 'Commentaires:edit', 28, 29),
(15, 3, 'Commentaires:delete', 26, 27),
(16, 4, 'Commentaires:view', 24, 25),
(17, 0, 'Localisations', 33, 48),
(18, 1, 'Localisations:getLibelle', 46, 47),
(19, 2, 'Localisations:index', 44, 45),
(20, 3, 'Localisations:view', 42, 43),
(21, 4, 'Localisations:add', 40, 41),
(22, 5, 'Localisations:edit', 38, 39),
(23, 6, 'Localisations:delete', 36, 37),
(24, 7, 'Localisations:changeParentId', 34, 35),
(25, 0, 'Deliberations', 49, 172),
(26, 1, 'Deliberations:index', 170, 171),
(27, 2, 'Deliberations:listerMesProjets', 168, 169),
(28, 3, 'Deliberations:listerProjetsAttribues', 166, 167),
(29, 4, 'Deliberations:listerProjetsNonAttribues', 164, 165),
(30, 5, 'Deliberations:listerProjetsDansMesCircuits', 162, 163),
(31, 6, 'Deliberations:listerProjetsATraiter', 160, 161),
(32, 7, 'Deliberations:getPosition', 158, 159),
(33, 8, 'Deliberations:view', 156, 157),
(34, 9, 'Deliberations:getFileData', 154, 155),
(35, 10, 'Deliberations:saveLocation', 152, 153),
(36, 11, 'Deliberations:getParent', 150, 151),
(37, 12, 'Deliberations:changeLocation', 148, 149),
(38, 13, 'Deliberations:add', 146, 147),
(39, 14, 'Deliberations:textsynthese', 144, 145),
(40, 15, 'Deliberations:deliberation', 142, 143),
(41, 16, 'Deliberations:textprojet', 140, 141),
(42, 17, 'Deliberations:edit', 138, 139),
(43, 18, 'Deliberations:recapitulatif', 136, 137),
(44, 19, 'Deliberations:getTextSynthese', 134, 135),
(45, 20, 'Deliberations:getTextProjet', 132, 133),
(46, 21, 'Deliberations:getField', 130, 131),
(47, 22, 'Deliberations:delete', 128, 129),
(48, 23, 'Deliberations:convert', 126, 127),
(49, 24, 'Deliberations:addIntoCircuit', 124, 125),
(50, 25, 'Deliberations:attribuercircuit', 122, 123),
(51, 26, 'Deliberations:traiter', 120, 121),
(52, 27, 'Deliberations:chercherVersionAnterieure', 118, 119),
(53, 28, 'Deliberations:transmit', 116, 117),
(54, 29, 'Deliberations:getNatureListe', 114, 115),
(55, 30, 'Deliberations:classification', 112, 113),
(56, 31, 'Deliberations:getMatiereListe', 110, 111),
(57, 32, 'Deliberations:object2array', 108, 109),
(58, 33, 'Deliberations:sendActe', 106, 107),
(59, 34, 'Deliberations:changeEtat', 104, 105),
(60, 35, 'Deliberations:changeClassification', 102, 103),
(61, 36, 'Deliberations:getDateClassification', 100, 101),
(62, 37, 'Deliberations:getClassification', 98, 99),
(63, 38, 'Deliberations:positionner', 96, 97),
(64, 39, 'Deliberations:sortby', 94, 95),
(65, 40, 'Deliberations:getCurrentPosition', 92, 93),
(66, 41, 'Deliberations:getCurrentSeance', 90, 91),
(67, 42, 'Deliberations:getLastPosition', 88, 89),
(68, 43, 'Deliberations:getNextId', 86, 87),
(69, 44, 'Deliberations:listerProjetsServicesAssemblees', 84, 85),
(70, 45, 'Deliberations:convertDoc2Html', 82, 83),
(71, 46, 'Deliberations:updateword', 80, 81),
(72, 47, 'Deliberations:getRapporteur', 78, 79),
(73, 48, 'Deliberations:textprojetvue', 76, 77),
(74, 49, 'Deliberations:textsynthesevue', 74, 75),
(75, 50, 'Deliberations:deliberationvue', 72, 73),
(76, 51, 'Deliberations:notifierDossierAtraiter', 70, 71),
(77, 52, 'Deliberations:notifierDossierRefuse', 68, 69),
(78, 53, 'Deliberations:notifierInsertionCircuit', 66, 67),
(79, 54, 'Deliberations:getListPresent', 64, 65),
(80, 55, 'Deliberations:listerPresents', 62, 63),
(81, 56, 'Deliberations:effacerListePresence', 60, 61),
(82, 57, 'Deliberations:isFirstDelib', 58, 59),
(83, 58, 'Deliberations:buildFirstList', 56, 57),
(84, 59, 'Deliberations:copyFromPreviousList', 54, 55),
(85, 60, 'Deliberations:getDelibIdByPosition', 52, 53),
(86, 61, 'Deliberations:afficherListePresents', 50, 51),
(213, 4, 'Models:delete', 500, 501),
(212, 3, 'Models:edit', 502, 503),
(211, 2, 'Models:add', 504, 505),
(210, 1, 'Models:index', 506, 507),
(209, 0, 'Models', 417, 508),
(93, 0, 'Seances', 185, 242),
(94, 1, 'Seances:index', 240, 241),
(95, 2, 'Seances:view', 238, 239),
(96, 3, 'Seances:add', 236, 237),
(97, 4, 'Seances:edit', 234, 235),
(98, 5, 'Seances:delete', 232, 233),
(99, 6, 'Seances:listerFuturesSeances', 230, 231),
(100, 7, 'Seances:listerAnciennesSeances', 228, 229),
(101, 8, 'Seances:afficherCalendrier', 226, 227),
(102, 9, 'Seances:afficherProjets', 224, 225),
(103, 10, 'Seances:changeRapporteur', 222, 223),
(104, 11, 'Seances:getDate', 220, 221),
(105, 12, 'Seances:getType', 218, 219),
(106, 13, 'Seances:addListUsers', 216, 217),
(107, 14, 'Seances:effacerListe', 214, 215),
(108, 15, 'Seances:generateConvocationList', 212, 213),
(109, 16, 'Seances:sendConvoc', 210, 211),
(110, 17, 'Seances:generateConvocationAnonyme', 208, 209),
(111, 18, 'Seances:generateOrdresDuJour', 206, 207),
(112, 19, 'Seances:checkLists', 204, 205),
(113, 20, 'Seances:delUserFromList', 202, 203),
(114, 21, 'Seances:addUserFromList', 200, 201),
(115, 22, 'Seances:isInList', 198, 199),
(116, 23, 'Seances:details', 196, 197),
(117, 24, 'Seances:effacerVote', 194, 195),
(118, 25, 'Seances:voter', 192, 193),
(119, 26, 'Seances:saisirDebat', 190, 191),
(120, 27, 'Seances:getFileData', 188, 189),
(121, 28, 'Seances:saisirDebatGlobal', 186, 187),
(122, 0, 'Postseances', 243, 276),
(123, 1, 'Postseances:index', 274, 275),
(124, 2, 'Postseances:afficherProjets', 272, 273),
(125, 3, 'Postseances:getVote', 270, 271),
(126, 4, 'Postseances:getPresence', 268, 269),
(127, 5, 'Postseances:generatePvSommaire', 266, 267),
(128, 6, 'Postseances:generatePvComplet', 264, 265),
(129, 7, 'Postseances:generateDeliberation', 262, 263),
(130, 8, 'Postseances:getNom', 260, 261),
(131, 0, 'Postseances', 277, 278),
(132, 1, 'Postseances:index', 258, 259),
(133, 2, 'Postseances:afficherProjets', 256, 257),
(134, 3, 'Postseances:getVote', 254, 255),
(135, 4, 'Postseances:getPresence', 252, 253),
(136, 5, 'Postseances:generatePvSommaire', 250, 251),
(137, 6, 'Postseances:generatePvComplet', 248, 249),
(138, 7, 'Postseances:generateDeliberation', 246, 247),
(139, 8, 'Postseances:getNom', 244, 245),
(140, 0, 'Votes', 279, 290),
(141, 1, 'Votes:index', 288, 289),
(142, 2, 'Votes:view', 286, 287),
(143, 3, 'Votes:add', 284, 285),
(144, 4, 'Votes:edit', 282, 283),
(145, 5, 'Votes:delete', 280, 281),
(262, 7, 'Users:getPrenom', 520, 521),
(261, 6, 'Users:getNom', 522, 523),
(260, 5, 'Users:delete', 524, 525),
(259, 4, 'Users:edit', 526, 527),
(258, 3, 'Users:add', 528, 529),
(257, 2, 'Users:view', 530, 531),
(256, 1, 'Users:index', 532, 533),
(255, 0, 'Users', 509, 534),
(156, 0, 'Services', 311, 332),
(157, 1, 'Services:changeService', 330, 331),
(158, 2, 'Services:getLibelle', 328, 329),
(159, 3, 'Services:index', 326, 327),
(160, 4, 'Services:view', 324, 325),
(161, 5, 'Services:add', 322, 323),
(162, 6, 'Services:edit', 320, 321),
(163, 7, 'Services:delete', 318, 319),
(164, 8, 'Services:changeParentId', 316, 317),
(165, 9, 'Services:doList', 314, 315),
(166, 10, 'Services:getParentList', 312, 313),
(167, 0, 'Collectivites', 333, 342),
(168, 1, 'Collectivites:index', 340, 341),
(169, 2, 'Collectivites:edit', 338, 339),
(170, 3, 'Collectivites:setLogo', 336, 337),
(171, 4, 'Collectivites:synchronize', 334, 335),
(172, 0, 'Profils', 343, 356),
(173, 1, 'Profils:index', 354, 355),
(174, 2, 'Profils:view', 352, 353),
(175, 3, 'Profils:add', 350, 351),
(176, 4, 'Profils:edit', 348, 349),
(177, 5, 'Profils:delete', 346, 347),
(178, 6, 'Profils:changeParentId', 344, 345),
(179, 0, 'Circuits', 357, 380),
(180, 1, 'Circuits:view', 378, 379),
(181, 2, 'Circuits:edit', 376, 377),
(182, 3, 'Circuits:delete', 374, 375),
(183, 4, 'Circuits:add', 372, 373),
(184, 5, 'Circuits:index', 370, 371),
(185, 6, 'Circuits:addUser', 368, 369),
(186, 7, 'Circuits:intervertirPosition', 366, 367),
(187, 8, 'Circuits:supprimerUser', 364, 365),
(188, 9, 'Circuits:getCurrentPosition', 362, 363),
(189, 10, 'Circuits:getCurrentCircuit', 360, 361),
(190, 11, 'Circuits:getLastPosition', 358, 359),
(191, 0, 'Annexes', 381, 394),
(192, 1, 'Annexes:delete', 392, 393),
(193, 2, 'Annexes:download', 390, 391),
(194, 3, 'Annexes:getFileType', 388, 389),
(195, 4, 'Annexes:getFileName', 386, 387),
(196, 5, 'Annexes:getSize', 384, 385),
(197, 6, 'Annexes:getData', 382, 383),
(198, 0, 'Themes', 395, 410),
(199, 1, 'Themes:getLibelle', 408, 409),
(200, 2, 'Themes:index', 406, 407),
(201, 3, 'Themes:view', 404, 405),
(202, 4, 'Themes:add', 402, 403),
(203, 5, 'Themes:edit', 400, 401),
(204, 6, 'Themes:delete', 398, 399),
(205, 7, 'Themes:changeParentId', 396, 397),
(206, 1, 'Pages:home', 411, 412),
(207, 1, 'Pages:administration', 413, 414),
(208, 1, 'Pages:display', 415, 416),
(214, 5, 'Models:view', 498, 499),
(215, 6, 'Models:getUserNom', 496, 497),
(216, 7, 'Models:getUserPrenom', 494, 495),
(217, 8, 'Models:getUserAdresse', 492, 493),
(218, 9, 'Models:getUserCP', 490, 491),
(219, 10, 'Models:getUserVille', 488, 489),
(220, 11, 'Models:getCollectiviteNom', 486, 487),
(221, 12, 'Models:getCollectiviteAdresse', 484, 485),
(222, 13, 'Models:getCollectiviteCP', 482, 483),
(223, 14, 'Models:getCollectiviteVille', 480, 481),
(224, 15, 'Models:getCollectiviteTelephone', 478, 479),
(225, 16, 'Models:getLibelleTheme', 476, 477),
(226, 17, 'Models:getLibelleService', 474, 475),
(227, 18, 'Models:getLibelleTypeSeance', 472, 473),
(228, 19, 'Models:getDateSeance', 470, 471),
(229, 20, 'Models:getTypeIdFromSeanceId', 468, 469),
(230, 21, 'Models:getCommentaireDelib', 466, 467),
(231, 22, 'Models:getDebatDelib', 464, 465),
(232, 23, 'Models:getTexteProjet', 462, 463),
(233, 24, 'Models:getTexteSynthese', 460, 461),
(234, 25, 'Models:getTexteDeliberation', 458, 459),
(235, 26, 'Models:getDelibLibelle', 456, 457),
(236, 27, 'Models:getDelibEtat', 454, 455),
(237, 28, 'Models:getDelibTitre', 452, 453),
(238, 29, 'Models:getSeanceId', 450, 451),
(239, 30, 'Models:getRapporteurId', 448, 449),
(240, 31, 'Models:getThemeId', 446, 447),
(241, 32, 'Models:getServiceId', 444, 445),
(242, 33, 'Models:getRedacteurId', 442, 443),
(243, 34, 'Models:getDateDuJour', 440, 441),
(244, 35, 'Models:listeProjets', 438, 439),
(245, 36, 'Models:listeUsersPresents', 436, 437),
(246, 37, 'Models:listeUsersAbsents', 434, 435),
(247, 38, 'Models:listeUsersMandates', 432, 433),
(248, 39, 'Models:listeUsersVotant', 430, 431),
(249, 40, 'Models:replaceBalisesSeance', 428, 429),
(250, 41, 'Models:replaceBalises', 426, 427),
(251, 42, 'Models:generateDeliberation', 424, 425),
(252, 43, 'Models:generateProjet', 422, 423),
(253, 44, 'Models:generatePVSommaire', 420, 421),
(254, 45, 'Models:generatePVDetaille', 418, 419),
(263, 8, 'Users:getAdresse', 518, 519),
(264, 9, 'Users:getCP', 516, 517),
(265, 10, 'Users:getVille', 514, 515),
(266, 11, 'Users:login', 512, 513),
(267, 12, 'Users:logout', 510, 511),
(268, 0, 'Seances:changeStatus', 520, 521),
(269, 0, 'Deliberations:PositionneDelibsSeance', 522, 523);

-- --------------------------------------------------------

--
-- Structure de la table `annexes`
--

CREATE TABLE IF NOT EXISTS `annexes` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `annexes`
--


-- --------------------------------------------------------

--
-- Structure de la table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `foreign_key` int(10) unsigned default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Contenu de la table `aros`
--

INSERT INTO `aros` (`id`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, 0, 'Valideur', 1, 2),
(2, 0, 'Redacteur', 3, 4),
(4, 0, 'Administrateur', 5, 8),
(3, 0, 'Assemblee', 9, 10),
(5, 1, 'admin', 6, 7);

-- --------------------------------------------------------

--
-- Structure de la table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `aro_id` int(10) unsigned NOT NULL,
  `aco_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL default '0',
  `_read` char(2) NOT NULL default '0',
  `_update` char(2) NOT NULL default '0',
  `_delete` char(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=95 ;

--
-- Contenu de la table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 4, 1, '1', '1', '1', '1'),
(2, 4, 7, '1', '1', '1', '1'),
(3, 4, 17, '1', '1', '1', '1'),
(4, 4, 87, '1', '1', '1', '1'),
(5, 4, 146, '1', '1', '1', '1'),
(6, 4, 156, '1', '1', '1', '1'),
(7, 4, 167, '1', '1', '1', '1'),
(8, 4, 172, '1', '1', '1', '1'),
(9, 4, 179, '1', '1', '1', '1'),
(10, 4, 198, '1', '1', '1', '1'),
(11, 4, 206, '1', '1', '1', '1'),
(12, 4, 207, '1', '1', '1', '1'),
(13, 4, 208, '1', '1', '1', '1'),
(14, 3, 25, '1', '1', '1', '1'),
(15, 3, 93, '1', '1', '1', '1'),
(16, 3, 122, '1', '1', '1', '1'),
(17, 3, 140, '1', '1', '1', '1'),
(18, 3, 191, '1', '1', '1', '1'),
(19, 3, 206, '1', '1', '1', '1'),
(20, 3, 208, '1', '1', '1', '1'),
(21, 2, 25, '1', '1', '1', '1'),
(22, 2, 191, '1', '1', '1', '1'),
(23, 2, 206, '1', '1', '1', '1'),
(24, 2, 208, '1', '1', '1', '1'),
(25, 1, 25, '1', '1', '1', '1'),
(26, 1, 38, '-1', '-1', '-1', '-1'),
(27, 1, 191, '1', '1', '1', '1'),
(28, 1, 206, '1', '1', '1', '1'),
(29, 1, 208, '1', '1', '1', '1'),
(30, 4, 165, '1', '1', '1', '1'),
(31, 2, 165, '1', '1', '1', '1'),
(32, 1, 199, '1', '1', '1', '1'),
(33, 2, 199, '1', '1', '1', '1'),
(34, 3, 199, '1', '1', '1', '1'),
(35, 1, 158, '1', '1', '1', '1'),
(36, 2, 158, '1', '1', '1', '1'),
(37, 3, 158, '1', '1', '1', '1'),
(38, 1, 152, '1', '1', '1', '1'),
(39, 2, 152, '1', '1', '1', '1'),
(40, 3, 152, '1', '1', '1', '1'),
(41, 1, 153, '1', '1', '1', '1'),
(42, 2, 153, '1', '1', '1', '1'),
(43, 3, 153, '1', '1', '1', '1'),
(44, 1, 104, '1', '1', '1', '1'),
(45, 2, 104, '1', '1', '1', '1'),
(46, 3, 104, '1', '1', '1', '1'),
(47, 1, 105, '1', '1', '1', '1'),
(48, 2, 105, '1', '1', '1', '1'),
(49, 3, 105, '1', '1', '1', '1'),
(50, 1, 155, '1', '1', '1', '1'),
(51, 2, 155, '1', '1', '1', '1'),
(52, 3, 155, '1', '1', '1', '1'),
(55, 3, 165, '1', '1', '1', '1'),
(54, 1, 165, '1', '1', '1', '1'),
(89, 3, 255, '1', '1', '1', '1'),
(88, 4, 255, '1', '1', '1', '1'),
(87, 1, 209, '1', '1', '1', '1'),
(86, 2, 209, '1', '1', '1', '1'),
(85, 3, 209, '1', '1', '1', '1'),
(84, 4, 209, '1', '1', '1', '1'),
(91, 1, 255, '1', '1', '1', '1'),
(90, 2, 255, '1', '1', '1', '1'),
(92, 3, 7, '1', '1', '1', '1'),
(93, 3, 268, '1', '1', '1', '1'),
(94, 3, 269, '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Structure de la table `circuits`
--

CREATE TABLE IF NOT EXISTS `circuits` (
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

CREATE TABLE IF NOT EXISTS `collectivites` (
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

CREATE TABLE IF NOT EXISTS `commentaires` (
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
-- Structure de la table `compteurs`
--

CREATE TABLE IF NOT EXISTS `compteurs` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
  `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur',
  `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
  `def_compteur` varchar(255) NOT NULL COMMENT 'Expression formatee du compteur',
  `num_sequence` mediumint(11) NOT NULL COMMENT 'Sequence du compteur qui s''incremente de 1 en 1',
  `def_reinit` varchar(255) NOT NULL COMMENT 'Expression formatee du critere de reinitialisation de la sequence',
  `val_reinit` varchar(255) NOT NULL COMMENT 'Derni�re valeur calculee de la r�initialisation',
  `created` datetime NOT NULL COMMENT 'Date et heure de creation du compteur',
  `modified` datetime NOT NULL COMMENT 'Date et heure de modification du compteur',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `compteurs`
--

INSERT INTO `compteurs` (`id`, `nom`, `commentaire`, `def_compteur`, `num_sequence`, `def_reinit`, `val_reinit`, `created`, `modified`) VALUES
(1, 'Deliberations', 'Numero des deliberations votees dans l''ordre du jour des seances', 'DELIB_#0000#', 4, '#AAAA##MM##JJ#', '20080313', '2008-01-07 12:04:25', '2008-03-13 16:22:14');

-- --------------------------------------------------------

--
-- Structure de la table `deliberations`
--

CREATE TABLE IF NOT EXISTS `deliberations` (
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
  `texte_synthese` longblob,
  `deliberation` longblob,
  `date_limite` date default NULL,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `reporte` tinyint(1) NOT NULL default '0',
  `localisation1_id` int(11) NOT NULL default '0',
  `localisation2_id` int(11) NOT NULL default '0',
  `localisation3_id` int(11) NOT NULL default '0',
  `montant` int(10) NOT NULL,
  `debat` longblob NOT NULL,
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

CREATE TABLE IF NOT EXISTS `listepresences` (
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

CREATE TABLE IF NOT EXISTS `localisations` (
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

CREATE TABLE IF NOT EXISTS `models` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(100) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `texte` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `models`
--

INSERT INTO `models` (`id`, `type`, `libelle`, `texte`) VALUES
(2, 'Document', 'convocation', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c64697620616c69676e3d227269676874223e266e6273703b3c2f6469763e0d0a3c7020616c69676e3d227269676874223e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d226c656674223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e23414452455353455f454c55233c2f6469763e0d0a3c7020616c69676e3d226c656674223e2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e20202020202020202020202020202020202020202020202020202020202020202020202020202020436f6e766f636174696f6e2061752023545950455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d2263656e746572223e0d0a3c64697620616c69676e3d226c656674223e266e6273703b3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e0d0a3c7072653e426f6e6a6f75722c3c2f7072653e0d0a3c2f6469763e0d0a3c2f6469763e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e4a276169206c27686f6e6e65757220646520766f757320696e76697465722061752023545950455f5345414e434523207175692061757261206c696575206c652023444154455f5345414e4345232064616e733c6272202f3e0d0a234c4945555f5345414e4345232e3c6272202f3e0d0a3c2f7374726f6e673e3c6272202f3e0d0a4a6520766f757320707269652064652063726f6972652c204d6164616d652c204d6f6e73696575722c20656e206c276173737572616e6365206465206d6120636f6e736964266561637574653b726174696f6e2064697374696e6775266561637574653b652e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e5472266561637574653b7320636f726469616c656d656e742e3c2f703e),
(3, 'Document', 'ordre du jour', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c6272202f3e0d0a23414452455353455f454c55233c6272202f3e0d0a2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e202020202020202020202020202020202020203c2f7374726f6e673e3c2f703e0d0a3c703e3c7374726f6e673e20202020202020202020202020202020202020202020202020202020202020204f72647265206475206a6f75723c2f7374726f6e673e206475203c7374726f6e673e23545950455f5345414e4345233c2f7374726f6e673e206475203c7374726f6e673e23444154455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f534f4d4d4149524553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(1, 'Document', 'projet', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e0d0a3c7461626c652077696474683d22383025222063656c6c73706163696e673d2231222063656c6c70616464696e673d22312220626f726465723d22312220616c69676e3d2263656e746572223e0d0a202020203c74626f64793e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e4944454e54494649414e542044552050524f4a45543c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e234944454e54494649414e545f50524f4a4554233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e4c6962656c6c653c2f74643e0d0a2020202020202020202020203c74643e234c4942454c4c455f44454c4942233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e54697472653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e2354495452455f44454c4942233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e5468266567726176653b6d653c2f74643e0d0a2020202020202020202020203c74643e234c4942454c4c455f5448454d45233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e536572766963653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e234c4942454c4c455f53455256494345233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e526170706f72746575723c2f74643e0d0a2020202020202020202020203c74643e234e4f4d5f524150504f5254455552233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e446174652053266561637574653b616e63653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e23444154455f5345414e4345233c2f74643e0d0a20202020202020203c2f74723e0d0a202020203c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e20203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c68313e3c753e3c7374726f6e673e54455854452050524f4a4554203a203c2f7374726f6e673e3c2f753e3c2f68313e0d0a3c703e2354455854455f50524f4a4554233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c666f6e742073697a653d2235223e3c753e3c7374726f6e673e2054455854452053594e5448455345203a3c2f7374726f6e673e3c2f753e3c2f666f6e743e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e2354455854455f53594e5448455345233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e202054455854452044454c494245524154494f4e203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e),
(4, 'Document', 'deliberation', 0x3c7020616c69676e3d2263656e746572223e0d0a3c7461626c652077696474683d2232303022206865696768743d223735222063656c6c73706163696e673d2231222063656c6c70616464696e673d22312220626f726465723d2232223e0d0a202020203c74626f64793e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e3c6272202f3e0d0a2020202020202020202020203c64697620616c69676e3d2263656e746572223e234c4f474f5f434f4c4c4543544956495445233c2f6469763e0d0a2020202020202020202020203c2f74643e0d0a20202020202020203c2f74723e0d0a202020203c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e526170706f72746575723a20234e4f4d5f524150504f52544555522320235052454e4f4d5f524150504f5254455552233c2f753e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e23444154455f5345414e4345233c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c6973746520646573205072266561637574653b73656e74733c2f7374726f6e673e3c2f753e203a3c2f703e0d0a3c703e234c495354455f50524553454e5453233c2f703e0d0a3c703e3c6272202f3e0d0a3c753e3c7374726f6e673e4c697374652064657320414253454e54533c2f7374726f6e673e3c2f753e203a3c2f703e0d0a3c703e234c495354455f414253454e5453233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c6973746520646573204d616e646174266561637574653b733c2f7374726f6e673e3c2f753e203a203c6272202f3e0d0a234c495354455f4d414e4441544149524553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e524553554c54415420564f54414e54203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f564f54414e54233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e524553554c544154203a203c666f6e7420636f6c6f723d2223666636363030223e3c656d3e2023434f4d4d454e54414952455f44454c4942233c2f656d3e3c2f666f6e743e3c2f68323e),
(5, 'Document', 'P.V. sommaire', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e3c666f6e742073697a653d2235223e3c753e20505620736f6d6d616972652064752023444154455f5345414e4345233c2f753e3c2f666f6e743e3c2f68323e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e3c666f6e742073697a653d2234223e4c69737465206465732070726f6a657473203a3c2f666f6e743e203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f534f4d4d4149524553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(6, 'Document', 'P.V. detaille', 0x3c703e3c666f6e742073697a653d22332220666163653d22436f7572696572204e6577223e0d0a3c7461626c652077696474683d2231303025222063656c6c73706163696e673d2231222063656c6c70616464696e673d22312220626f726465723d2231223e0d0a202020203c74626f64793e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e0d0a2020202020202020202020203c703e234e4f4d5f434f4c4c4543544956495445233c2f703e0d0a2020202020202020202020203c703e266e6273703b3c2f703e0d0a2020202020202020202020203c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a2020202020202020202020203c703e266e6273703b3c2f703e0d0a2020202020202020202020203c703e266e6273703b3c2f703e0d0a2020202020202020202020203c2f74643e0d0a2020202020202020202020203c746420616c69676e3d227269676874223e3c666f6e742073697a653d22332220666163653d22436f7572696572204e6577223e234c4f474f5f434f4c4c4543544956495445233c2f666f6e743e3c2f74643e0d0a20202020202020203c2f74723e0d0a202020203c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f666f6e743e3c2f703e0d0a3c7020616c69676e3d226a75737469667922207374796c653d22746578742d696e64656e743a20302e34636d3b206d617267696e2d626f74746f6d3a2030636d3b223e266e6273703b3c2f703e0d0a3c7020616c69676e3d226a75737469667922207374796c653d22746578742d696e64656e743a20302e34636d3b206d617267696e2d626f74746f6d3a2030636d3b223e3c666f6e742073697a653d22332220666163653d22436f7572696572204e6577223e3c7374726f6e673e3c753e4461746520533c2f753e3c2f7374726f6e673e3c2f666f6e743e3c666f6e742073697a653d22332220666163653d22436f7572696572204e6577223e3c7374726f6e673e3c753e266561637574653b616e63653c2f753e3c2f7374726f6e673e203a2023444154455f5345414e4345233c2f666f6e743e3c2f703e0d0a3c7020616c69676e3d226a75737469667922207374796c653d22746578742d696e64656e743a20302e34636d3b206d617267696e2d626f74746f6d3a2030636d3b223e3c666f6e742073697a653d22332220666163653d22436f7572696572204e6577223e3c666f6e742073697a653d2235223e3c753e3c7374726f6e673e3c7374726f6e673e3c7370616e207374796c653d22223e3c7370616e207374796c653d22746578742d6465636f726174696f6e3a206e6f6e653b223e3c7370616e207374796c653d22666f6e742d7374796c653a206e6f726d616c3b223e3c666f6e7420636f6c6f723d2223303030303030223e50726f6a6574732064266561637574653b7461696c6c266561637574653b733c2f666f6e743e3c2f7370616e3e3c2f7370616e3e3c2f7370616e3e3c2f7374726f6e673e3c2f7374726f6e673e3c2f753e3c2f666f6e743e3c7374726f6e673e3c7370616e207374796c653d22223e3c7370616e207374796c653d22746578742d6465636f726174696f6e3a206e6f6e653b223e3c7370616e207374796c653d22666f6e742d7374796c653a206e6f726d616c3b223e3c666f6e7420636f6c6f723d2223303030303030223e203a3c2f666f6e743e3c2f7370616e3e3c2f7370616e3e3c2f7370616e3e3c2f7374726f6e673e3c2f666f6e743e3c2f703e0d0a3c7020616c69676e3d226a75737469667922207374796c653d22746578742d696e64656e743a20302e34636d3b206d617267696e2d626f74746f6d3a2030636d3b223e3c666f6e742073697a653d22332220666163653d22436f7572696572204e6577223e3c7370616e207374796c653d22223e3c7370616e207374796c653d22746578742d6465636f726174696f6e3a206e6f6e653b223e3c7370616e207374796c653d22666f6e742d7374796c653a206e6f726d616c3b223e3c666f6e7420636f6c6f723d2223303030303030223e234c495354455f50524f4a4554535f44455441494c4c4553233c2f666f6e743e3c2f7370616e3e3c2f7370616e3e3c2f7370616e3e3c2f666f6e743e3c2f703e0d0a3c7020616c69676e3d226a75737469667922207374796c653d22746578742d696e64656e743a20302e34636d3b206d617267696e2d626f74746f6d3a2030636d3b223e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(8, 'Composant documentaire', 'Liste presents', 0x3c703e3c7374726f6e673e234e4f4d5f50524553454e5423203c2f7374726f6e673e235052454e4f4d5f50524553454e54233c2f703e),
(9, 'Composant documentaire', 'Liste absents', 0x3c703e3c7374726f6e673e235052454e4f4d5f414253454e54233c2f7374726f6e673e3c7374726f6e673e203c2f7374726f6e673e3c7374726f6e673e234e4f4d5f414253454e54233c2f7374726f6e673e3c656d3e203c2f656d3e3c2f703e),
(10, 'Composant documentaire', 'liste mandat', 0x3c703e3c7374726f6e673e235052454e4f4d5f4d414e4441544149524523203c2f7374726f6e673e3c7374726f6e673e234e4f4d5f4d414e44415441495245232c203c6272202f3e0d0a3c6272202f3e0d0a3c2f7374726f6e673e3c656d3e203c2f656d3e3c2f703e),
(11, 'Composant documentaire', 'liste votants', 0x3c703e0d0a3c7461626c652077696474683d22393025222063656c6c73706163696e673d2231222063656c6c70616464696e673d22312220626f726465723d2230223e0d0a202020203c74626f64793e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e234e4f4d5f564f54414e5423266e6273703b20235052454e4f4d5f564f54414e54233c2f74643e0d0a2020202020202020202020203c74643e6120766f74266561637574653b2023524553554c5441545f564f54414e54233c2f74643e0d0a20202020202020203c2f74723e0d0a202020203c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f703e),
(12, 'Composant documentaire', 'liste projets detaille', 0x3c703e266e6273703b3c2f703e0d0a3c6f6c3e0d0a202020203c6c693e0d0a202020203c68323e2354495452455f44454c4942233c2f68323e0d0a202020203c2f6c693e0d0a3c2f6f6c3e0d0a3c703e266e6273703b3c2f703e0d0a3c68333e266e6273703b3c2f68333e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e3c753e5465787465206465206c612064266561637574653b6c6962266561637574653b726174696f6e3c2f753e3c2f7374726f6e673e203a3c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e3c753e436f6d6d656e7461697265733c2f753e3c2f7374726f6e673e203a3c2f703e0d0a3c703e23434f4d4d454e54414952455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d22726967687422207374796c653d22746578742d696e64656e743a20302e34636d3b206d617267696e2d626f74746f6d3a2030636d3b223e266e6273703b3c2f703e0d0a3c703e0d0a3c6d65746120687474702d65717569763d22434f4e54454e542d545950452220636f6e74656e743d22746578742f68746d6c3b20636861727365743d7574662d38223e0d0a3c7469746c653e3c2f7469746c653e0d0a3c6d657461206e616d653d2247454e455241544f522220636f6e74656e743d224f70656e4f66666963652e6f726720322e312020284c696e757829223e0d0a3c6d657461206e616d653d22435245415445442220636f6e74656e743d2232303037313132323b39333735343030223e0d0a3c6d657461206e616d653d224348414e4745442220636f6e74656e743d2232303038303132323b3130313530383030223e200920092009200920093c7374796c6520747970653d22746578742f637373223e0d0a093c212d2d0d0a09094070616765207b2073697a653a203231636d2032392e37636d3b206d617267696e3a2032636d207d0d0a090950207b206d617267696e2d626f74746f6d3a20302e3231636d207d0d0a092d2d3e0d0a093c2f7374796c653e202020202020202020202020202020203c2f6d6574613e0d0a3c2f6d6574613e0d0a3c2f6d6574613e0d0a3c2f6d6574613e0d0a3c2f703e),
(13, 'Composant documentaire', 'liste projets sommaires', 0x3c703e3c753e3c7374726f6e673e4c6962656c6c653c2f7374726f6e673e3c2f753e3c7374726f6e673e203a203c2f7374726f6e673e234c4942454c4c455f44454c4942233c2f703e0d0a3c703e3c7374726f6e673e5469747265203a203c2f7374726f6e673e2354495452455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e);

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE IF NOT EXISTS `profils` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `profils`
--

INSERT INTO `profils` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(3, 0, 'Valideur', '2007-09-03 14:39:20', '2007-09-03 14:39:20'),
(4, 0, 'Redacteur', '2007-09-03 14:39:36', '2007-10-18 11:06:21'),
(1, 0, 'Administrateur', '2007-09-03 14:40:53', '2007-09-03 14:40:53'),
(2, 0, 'Assemblee', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `seances`
--

CREATE TABLE IF NOT EXISTS `seances` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `traitee` int(1) NOT NULL default '0',
  `debat_global` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `seances`
--


-- --------------------------------------------------------

--
-- Structure de la table `seances_users`
--

CREATE TABLE IF NOT EXISTS `seances_users` (
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

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `services`
--

INSERT INTO `services` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(1, 0, 'Informatique', '2007-10-02 14:21:47', '2008-01-14 17:45:21'),
(2, 1, 'Reseau', '2007-10-02 14:24:50', '2007-10-02 16:00:29'),
(3, 1, 'Developpement', '2007-10-02 14:25:00', '2007-10-02 15:59:58'),
(4, 3, 'Java', '2007-10-02 14:25:08', '2007-10-02 14:30:54'),
(5, 3, 'PHP', '2007-10-02 14:25:20', '2007-10-02 14:25:20'),
(6, 0, 'Ressources Humaines', '2007-10-18 10:40:34', '2008-01-14 14:34:49'),
(7, 0, 'Comptabilite', '2007-10-18 10:41:47', '2007-10-18 10:41:47'),
(8, 0, 'Services techniques', '2007-10-18 10:41:56', '2007-10-18 10:41:56'),
(9, 8, 'Espaces verts', '2007-10-18 10:42:06', '2007-10-18 10:42:06'),
(10, 8, 'Voirie', '2007-10-18 10:42:44', '2007-10-19 15:15:16'),
(11, 0, 'Administration Generale', '2007-10-18 16:38:00', '2007-10-19 15:10:39');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `themes`
--

INSERT INTO `themes` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(1, 1, 'Urbanisme', '2007-10-02 14:43:30', '2008-01-18 15:38:07'),
(2, 2, 'Entretien des routes', '2007-10-02 14:52:36', '2008-01-16 17:16:17'),
(3, 1, 'Service technique', '2007-10-02 14:52:57', '2007-10-02 14:52:57'),
(4, 0, 'Informatique', '2007-10-02 14:53:03', '2007-10-02 14:53:03'),
(5, 0, 'Comptabilite-Finances', '2007-10-18 16:46:03', '2007-10-18 16:46:03'),
(8, 4, 'Multimedia', '2008-01-12 16:33:20', '2008-01-12 16:33:20');

-- --------------------------------------------------------

--
-- Structure de la table `traitements`
--

CREATE TABLE IF NOT EXISTS `traitements` (
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

CREATE TABLE IF NOT EXISTS `typeseances` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL,
  `retard` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `typeseances`
--

INSERT INTO `typeseances` (`id`, `libelle`, `retard`, `created`, `modified`) VALUES
(1, 'Conseil municipal', 5, '2007-08-02 10:26:47', '2008-03-13 16:30:20'),
(2, 'Conseil general', 0, '2007-08-02 10:26:53', '2007-08-02 10:26:53'),
(3, 'Commission permanente', 0, '2007-08-02 10:27:01', '2007-08-02 10:27:01');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
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
(1, 1, 0, 0, 'admin', '21232f297a57a5a743894a0e4a801fc3', ' ', 'admin', 'francois.desmaretz@adullact.org', '116 avenue saint clement', 34000, 'Montpellier', 0000000000, 0000000000, '1999-11-30', 0, '0000-00-00 00:00:00', '2008-03-14 14:42:18');

-- --------------------------------------------------------

--
-- Structure de la table `users_circuits`
--

CREATE TABLE IF NOT EXISTS `users_circuits` (
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

CREATE TABLE IF NOT EXISTS `users_services` (
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

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `resultat` int(1) NOT NULL,
  `commentaire` varchar(500) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Ajout des commentaires pour tous ces roles
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '1', '12', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '2', '12', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '3', '12', '1', '1', '1', '1');

-- Ajout du changement de services
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '1', '157', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '2', '157', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '3', '157', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '4', '157', '1', '1', '1', '1');