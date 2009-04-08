-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 06 Avril 2009 à 11:15
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `webdelib2`
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
) ;

--
-- Contenu de la table `acos`
--

INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Pages:home', 1, 2),
(2, NULL, 'Pages:mes_projets', 3, 16),
(3, NULL, 'Deliberations:add', 14, 15),
(4, NULL, 'Deliberations:mesProjetsRedaction', 12, 13),
(5, NULL, 'Deliberations:mesProjetsValidation', 10, 11),
(6, NULL, 'Deliberations:mesProjetsATraiter', 8, 9),
(7, NULL, 'Deliberations:mesProjetsValides', 6, 7),
(8, NULL, 'Deliberations:mesProjetsRecherche', 4, 5),
(9, NULL, 'Pages:tous_les_projets', 17, 26),
(10, NULL, 'Deliberations:tousLesProjetsSansSeance', 24, 25),
(11, NULL, 'Deliberations:tousLesProjetsValidation', 22, 23),
(12, NULL, 'Deliberations:tousLesProjetsAFaireVoter', 20, 21),
(13, NULL, 'Deliberations:tousLesProjetsRecherche', 18, 19),
(14, NULL, 'Seances:listerFuturesSeances', 27, 34),
(15, NULL, 'Seances:add', 32, 33),
(16, NULL, 'Seances:listerAnciennesSeances', 30, 31),
(17, NULL, 'Seances:afficherCalendrier', 28, 29),
(18, NULL, 'Pages:postseance', 35, 42),
(19, NULL, 'Postseances:index', 40, 41),
(20, NULL, 'Deliberations:transmit', 38, 39),
(21, NULL, 'Pages:exportged', 36, 37),
(22, NULL, 'Pages:gestion_utilisateurs', 43, 54),
(23, NULL, 'Profils:index', 52, 53),
(24, NULL, 'Droits:edit', 50, 51),
(25, NULL, 'Services:index', 48, 49),
(26, NULL, 'Users:index', 46, 47),
(27, NULL, 'Circuits:index', 44, 45),
(28, NULL, 'Pages:gestion_acteurs', 55, 60),
(29, NULL, 'Typeacteurs:index', 58, 59),
(30, NULL, 'Acteurs:index', 56, 57),
(31, NULL, 'Pages:administration', 61, 76),
(32, NULL, 'Collectivites:index', 74, 75),
(33, NULL, 'Themes:index', 72, 73),
(34, NULL, 'Models:index', 70, 71),
(35, NULL, 'Sequences:index', 68, 69),
(36, NULL, 'Compteurs:index', 66, 67),
(37, NULL, 'Typeseances:index', 64, 65),
(38, NULL, 'Infosupdefs:index', 62, 63),
(39, NULL, 'Deliberations', 77, 80),
(40, NULL, 'Deliberations:editerProjetValide', 78, 79);

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
) ;

--
-- Contenu de la table `acteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `acteurs_services`
--

CREATE TABLE `acteurs_services` (
  `acteur_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acteur_id`,`service_id`)
) ;

--
-- Contenu de la table `acteurs_services`
--


-- --------------------------------------------------------

--
-- Structure de la table `annexes`
--

CREATE TABLE `annexes` (
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
) ;

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
) ;

--
-- Contenu de la table `aros`
--

INSERT INTO `aros` (`id`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, 0, 'Profil:Administrateur', 1, 4),
(2, 1, 'Utilisateur:admin', 2, 3),
(3, 0, 'Profil:Défaut', 5, 6),
(4, 0, 'Profil:Rédacteur', 7, 8),
(5, 0, 'Profil:Service assemblée', 9, 10),
(6, 0, 'Profil:Valideur', 11, 12);

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
) ;

--
-- Contenu de la table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 1, 1, '1', '1', '1', '1'),
(2, 1, 2, '-1', '-1', '-1', '-1'),
(3, 1, 9, '-1', '-1', '-1', '-1'),
(4, 1, 14, '-1', '-1', '-1', '-1'),
(5, 1, 18, '-1', '-1', '-1', '-1'),
(6, 1, 22, '1', '1', '1', '1'),
(7, 1, 28, '-1', '-1', '-1', '-1'),
(8, 1, 31, '1', '1', '1', '1'),
(9, 1, 39, '-1', '-1', '-1', '-1'),
(10, 3, 1, '1', '1', '1', '1'),
(11, 3, 2, '-1', '-1', '-1', '-1'),
(12, 3, 9, '-1', '-1', '-1', '-1'),
(13, 3, 14, '-1', '-1', '-1', '-1'),
(14, 3, 18, '-1', '-1', '-1', '-1'),
(15, 3, 22, '-1', '-1', '-1', '-1'),
(16, 3, 28, '-1', '-1', '-1', '-1'),
(17, 3, 31, '-1', '-1', '-1', '-1'),
(18, 3, 39, '-1', '-1', '-1', '-1'),
(19, 4, 1, '1', '1', '1', '1'),
(20, 4, 2, '1', '1', '1', '1'),
(21, 4, 9, '-1', '-1', '-1', '-1'),
(22, 4, 14, '-1', '-1', '-1', '-1'),
(23, 4, 18, '-1', '-1', '-1', '-1'),
(24, 4, 22, '-1', '-1', '-1', '-1'),
(25, 4, 28, '-1', '-1', '-1', '-1'),
(26, 4, 31, '-1', '-1', '-1', '-1'),
(27, 4, 39, '-1', '-1', '-1', '-1'),
(28, 5, 1, '1', '1', '1', '1'),
(29, 5, 2, '-1', '-1', '-1', '-1'),
(30, 5, 9, '1', '1', '1', '1'),
(31, 5, 14, '-1', '-1', '-1', '-1'),
(32, 5, 18, '-1', '-1', '-1', '-1'),
(33, 5, 22, '-1', '-1', '-1', '-1'),
(34, 5, 28, '1', '1', '1', '1'),
(35, 5, 31, '-1', '-1', '-1', '-1'),
(36, 5, 39, '-1', '-1', '-1', '-1'),
(37, 6, 1, '1', '1', '1', '1'),
(38, 6, 2, '1', '1', '1', '1'),
(39, 6, 3, '-1', '-1', '-1', '-1'),
(40, 6, 4, '-1', '-1', '-1', '-1'),
(41, 6, 7, '-1', '-1', '-1', '-1'),
(42, 6, 9, '-1', '-1', '-1', '-1'),
(43, 6, 14, '-1', '-1', '-1', '-1'),
(44, 6, 18, '-1', '-1', '-1', '-1'),
(45, 6, 22, '-1', '-1', '-1', '-1'),
(46, 6, 28, '-1', '-1', '-1', '-1'),
(47, 6, 31, '-1', '-1', '-1', '-1'),
(48, 6, 39, '-1', '-1', '-1', '-1');

-- --------------------------------------------------------

--
-- Structure de la table `circuits`
--

CREATE TABLE `circuits` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;

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
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `collectivites`
--

INSERT INTO `collectivites` (`id`, `nom`, `adresse`, `CP`, `ville`, `telephone`) VALUES
(1, 'Adullact', '335, Cour Messier', 34000, 'Montpellier', '0467650588');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL auto_increment,
  `delib_id` int(11) NOT NULL default '0',
  `agent_id` int(11) NOT NULL default '0',
  `texte` varchar(200) NOT NULL default '',
  `pris_en_compte` tinyint(4) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `commentaires`
--


-- --------------------------------------------------------

--
-- Structure de la table `compteurs`
--

CREATE TABLE `compteurs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `def_compteur` varchar(255) NOT NULL,
  `sequence_id` int(11) NOT NULL,
  `def_reinit` varchar(255) NOT NULL,
  `val_reinit` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) ;

--
-- Contenu de la table `compteurs`
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
  `objet` varchar(1000) NOT NULL,
  `titre` varchar(1000) NOT NULL,
  `num_delib` varchar(15) NOT NULL,
  `num_pref` varchar(10) NOT NULL default '',
  `texte_projet` longblob,
  `texte_projet_name` varchar(75) NOT NULL,
  `texte_projet_type` varchar(255) NOT NULL,
  `texte_projet_size` int(11) NOT NULL,
  `texte_synthese` longblob,
  `texte_synthese_name` varchar(75) NOT NULL,
  `texte_synthese_type` varchar(255) NOT NULL,
  `texte_synthese_size` int(11) NOT NULL,
  `deliberation` longblob,
  `deliberation_name` varchar(75) NOT NULL,
  `deliberation_type` varchar(255) NOT NULL,
  `deliberation_size` int(11) NOT NULL,
  `date_limite` date default NULL,
  `date_envoi` datetime default NULL,
  `etat` int(11) NOT NULL default '0',
  `reporte` tinyint(1) NOT NULL default '0',
  `montant` int(10) NOT NULL,
  `debat` longblob NOT NULL,
  `debat_name` varchar(255) NOT NULL,
  `debat_size` int(11) NOT NULL,
  `debat_type` varchar(255) NOT NULL,
  `avis` int(1) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_nb_oui` int(3) NOT NULL,
  `vote_nb_non` int(3) NOT NULL,
  `vote_nb_abstention` int(3) NOT NULL,
  `vote_nb_retrait` int(3) NOT NULL,
  `vote_commentaire` varchar(500) NOT NULL,
  `commission` longblob NOT NULL,
  `commission_size` int(11) NOT NULL,
  `commission_type` varchar(255) NOT NULL,
  `commission_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `deliberations`
--


-- --------------------------------------------------------

--
-- Structure de la table `infosupdefs`
--

CREATE TABLE `infosupdefs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `ordre` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `taille` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `recherche` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `infosupdefs`
--


-- --------------------------------------------------------

--
-- Structure de la table `infosups`
--

CREATE TABLE `infosups` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation_id` int(11) NOT NULL,
  `infosupdef_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `deliberation_id` (`deliberation_id`),
  KEY `infosupdef_id` (`infosupdef_id`)
) ;

--
-- Contenu de la table `infosups`
--


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
) ;

--
-- Contenu de la table `listepresences`
--


-- --------------------------------------------------------

--
-- Structure de la table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL auto_increment,
  `modele` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(255) default NULL,
  `size` int(11) NOT NULL,
  `extension` varchar(255) default NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `models`
--
INSERT INTO `models` (`id`, `modele`, `type`, `content`, `name`, `size`, `extension`) VALUES
(1, 'Modèle défaut', 'Document', 0x504b0304140000000000176a873a5ec6320c2700000027000000080000006d696d65747970656170706c69636174696f6e2f766e642e6f617369732e6f70656e646f63756d656e742e74657874504b0304140000000000176a873a0000000000000000000000001a000000436f6e66696775726174696f6e73322f7374617475736261722f504b0304140008000800176a873a00000000000000000000000027000000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c0300504b0708000000000200000000000000504b0304140000000000176a873a00000000000000000000000018000000436f6e66696775726174696f6e73322f666c6f617465722f504b0304140000000000176a873a0000000000000000000000001a000000436f6e66696775726174696f6e73322f706f7075706d656e752f504b0304140000000000176a873a0000000000000000000000001c000000436f6e66696775726174696f6e73322f70726f67726573736261722f504b0304140000000000176a873a00000000000000000000000018000000436f6e66696775726174696f6e73322f6d656e756261722f504b0304140000000000176a873a00000000000000000000000018000000436f6e66696775726174696f6e73322f746f6f6c6261722f504b0304140000000000176a873a0000000000000000000000001f000000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b0304140008000800176a873a0000000000000000000000000b000000636f6e74656e742e786d6cc59b4d6fe3361086effd15860ebd298a9d1e366e9cc5a28b0205368ba2d916bd191445c74c295225297ffcfb0e4959a6ec3811901df69244d4900f3924df1989d1ddc75d2d261ba60d5772914dafaeb3099354555c3e2db23fbffd9a7fc83edeff70a7562b4ed9bc52b4ad99b43955d2c2ef09d496661eee2eb256cbb922869bb9243533734be7aa61f2506b1e5bcf3d2b9418bb17a3ab7be3b8b6653b3bb6b2b31dd425e578b2378e6b579a6cc75676b6e0d4b8fa4a8dadbc33225f29f07add10cb4f7ab1135cfeb3c8d6d636f3a2d86eb757db9b2ba59f8ae9eded6de1eff61da6bd5dd36ae1ad2a5a30c11ccc14d3ab6971b0ad992563fbe76ce32ec9b62e991eed1a62c9d9ac9acdd3e815b179bae01aba267af4daf0c6c3e9bda9c64fef4d15d7ad895d5f98930fc503dcf43f1ebe1cd782aec7b29cedc05554f366f43083755c5f29d577d555081bd47777767dfd5311ae23ebedabe65bcd2dd391397dd59c12417b8fabfa25a781ddb4008b9c6ddc32ed17be7384b950615684dbbdb1a92e36fdf7c39747ba6635391af3b78d732e8d25f2e8991567e2b060fa817673c1760dd3dc4d0311e013371855ada00970956ae651036166baca91324fb3fb830c872934455fb00239ce5784b2bc625498fbbbb09dfae249b8765d59645f795db666f287aa899c7c55b7135884b0850ed63517fb45f6236994f9f9dc3494679301c055c99f988411c25c6b673db068b8a5b01b36447327a15931aa838f449ab7fb16ac4674cb6cb931efe9d667f64cfe6a3df062a7229b315dda1bcbeab7fa545c9ae4ae9cb4163c6e39cd7d3bd1b22855b5ef2f5ce8bbbff301d0b07f5b88f27d43e785135f5471d308b2cf556b218ab05cc0fe138b0c96a7bf1ddcf29b10adb11a3aa0a4ebeebb1afb7618f4fb5a813fdfddc8e710b0fd0c5cf65a6b98cefdbe7dadb8dfce44b42cb7fb06da079fb9e60fb7c265ee2d16d9a02752d54baa8460d4f20d686b3fb4ef4a219566c6b00424da24806c381012702c244fcd5ac9142c4894d8f259b51a58ad26d2e260dc7a23d4b256e3b4df68868d305066bd24a1622cb79aa112fc944b02a1cb651aa8a84e00a62920335408080c66f3415a3009905d7281bb729958f11dee28a4b2b8005e413ecd571c947069984fc5f1366122c052300ba26270406b980bdca1387b54800b1d8651cd2ce11a89d145286c4c14a5b051215261534ea21536ae8f588940337410442e6c44885ed89410c1d0577517c5b0393e92a5d9a28d56cf0c29b15725348d4a6894e15ecf302171d8c7e4c8b6665a2d2b2678c94e5e707cdf5d2f883130201a62013e10dada80c992d5906ce0e5986b681f7586dc9fb884b0f3f712c662d044cc0d027fd221a3299dc028bc7cdf034869dcd920f638dc11249ede7b04643166d9106d39e50de6fe57b53f1980d0b2dc28b4b747ac2436c13a0b1c3726c803f124ec983b6bd2344aa3bfafc2c6a460841c03dd61872c001b14b24c74a73151ab920b7cc71df2596ccec9731a36ae7f4e4b049aa183e0390d1b119ed3f045c726d0834e405985fbba111dd1e90d2e24921b7450501b5ccca9d860af028bce38c637ec09724f0651768883f9e508304bc320e12556216ed2e032786c83472aac33ccc119232eeaecac1117179f39e2925e3c7bc4459e9c412681cd92c0fa33495c4c7c36894b8acf289157fce0ac129dd505df14b4e8841417f4c933ccef819152e549994ae43149e71a8f491b483c26e86585c7249e0a7c02d62c05eb28ef989481ba638206e28ebad487da8e8c1a4a3b262c56764c4e27ec9fca24baded11e8874ca91328cd41e89fe1f1f8968e7e1049b380829d8b097c30a36f534b424e2cd52f18e21069b340833d8b041a841df06c3709300370c39d8c038ece0b34ef432a13427d6e574a2fc3f287252394eaac58984389d0aa793e094fa9b567c93296ff878ef25d2e12bb12634e03fdbcb43338fd6d5d595afdb519dd1f12a7cd477b83afd22fffe3f504b070805e3fa559a050000d23f0000504b0304140008000800176a873a0000000000000000000000000a0000007374796c65732e786d6ccd594b8fdb3610bef757182ada1b6dcbbb9baeddece6d0226881248724ed35a025da6222910249f9915fdf195294685bf26a1f29bc8705c419ce7c9c27397efd6657e4a30d539a4b7117c5e369346222912917ebbbe89fcf6fc96df4e6fea7d772b5e2095ba432a90a260cd1669f333d82cd422f1cf12eaa945848aab95e085a30bd30c942964cf84d8b907b6155b9152b6ce876cb1cee366c67866e46de83bd74395cb3650e77a78a6e876e465eb069b87d25876edee99cac2449645152c38f50ec722ebedd459931e56232d96eb7e3edd558aaf5249ecfe7134b6d00270d5f59a9dc72a5c984e50c95e9493c8e279eb760860ec587bc212451154ba6069b861a7ae255bd590f8e88cdbac7344946d5e0d8b0cc87eebd4a87bbf72a0df716d4643d3eb99dbc07a2fdf7fe5d1b0baa18aa0b790f4c95285e0e3ea6e30ef74b291ba8b8c125a8853b9b4eaf27ee3be0de9e65df2a6e980ad893b3ec09cd93c6e2b2e8321af0c513e0206c83611a8dea121294ad38baf7356a25a13ead68c248ca925cdfbf76b1d52c8fdc37dae82efac08b65a5471f6541c5e8839c8fc023104f9ebbe0f9fe2efa959652ff7ecaead6a3d18102dc42d64c30c5e1e00ab90f384a6e12088d0d551ceb49341904f01315fa616c8e6b002cbde55a3f07d69fec2bfdb7b20a7b41053c4320edb561c54398267d4eaed75d5ff2d853b6a2555e772b2fb9c6b856b4cc781279defa9b940aa254190edd0d6bf6426734955b02f235336477174dc75709e0ec20ee8f88060a0a81facb882e6902d59f6452f1ef009de6c83abb3dcbbc4118c9292ba4f450a927ac1d326bb3e4708e2d371971fd7445731d44414915b5160aede348c84f686524ea80d0e029938e95e66546bd020b63a918855ea50db8dc780a160cc456c814b6e78a98e54118709132ac6378ef080fe3417a8cd09fc1d3b2d41827fdb01b76c47d729a4a33308340af5ae589cc257432a32aa8802be91069fe1d90c6b3d2d8b59c8a7545d7b0b45276219195300ac2e1edc7e6f8cc405524df981216ba13189c126512a8de14ebd9747c5336f6f1e23df5fb6ee749b5224f11527408c5de98b39d077c24b4a176886d6856706bd683bc1a926c8d1fa2b30105a6cbf665c604b47229484ed3146c66d12006f07ec19b130c8cbbb21289a99cc02d90a1c1c0d1c1070f07a60f289272484f814ae2f1ec266eb3e630744bb0679b324f88afc073fdede94706216af5c17458e27f70a05ac54db89d55fd52e11c461bf3b5e43804152b281704ef863e0e67274c65a5b3239667e48a7d6684452d676118b957c8522a4c0d8c3ba8e51044392d3506f573151325b747ca61e52849bf31561223d7cc6478cdc7247c4871a8d0c5f62748a994aa34eaad15de7d39d51ae0413eb5d9752aef2f46d320ad7bc5c142f38225dd5004a66ec8f01916becca65f9632dd77c17aa8aa155441c9019395d878af67b6f1b6eb4b690c5e7da127c7b39a646d6c1bb2b00d99e65bbad70f9597d3dae16f8e4765e3bacd9da7657ea780c766300a6903e78180e9720254e632a7fbc04da390fc9c2078b27fcffb76f071df41d379ca39cec46a8e220704d081a3678ff1d11fb4c492f882f607948a3e32c1da2cea4b303b0872630a7ba7d44d03b0147b27f0538c697fd2f5f462b7e6a65a1caee5f0cc386bde03a235494deddbdcd90383ed0dbd1630d87f7fc3357bf782dee356de59efbd902b1e15be56b2ac8c7b4e74ace56cc3f2ba4b3a18b800e29a165115040731144a73733eccaf7aebf1010b8ef7d92550dacb243c166fe3ba24f4691f886b76a1b8ae2e14d7f585e2bab9505caf2e14d76f178aebf64271cd2f14573cfdff811d9242b4421aa6a1898a155f57ca3ef2460d81d4ad6d25a5c1ef2ee071ddbcdc546f43f30a51d58b7ea326a5d4dcd809b61d1d847b5cc7c3d902caf3337c3cd170844ca47d007937402f1e2dd222e852d3dbafdd2cd43e73e6f37652d2659d5a486b859cad4c4de32251f65729ac89c1e0d74a6be7bdf8f404993c219ee06f1a6bf036dd83770fee376511471d3c47773a4bd9f2147fc3994dc77377104fc8185f67f8ca9f8f5f9d3962ad042c6888541c8e426b5f4b05575a6ea2e38b6bcfa5f568198d74b2a86a487da3a9a3a9aa0b4052d05d731a7cb6b433ff9a41b3d28b73d6988ea7f16dabc4a71c593238b9e5479e781a77f0d0150e9fba5868fab5d2c679dbc5805b5790acde0d37bfb4e31c3b32fb796affa270dadbe5507fa88c511c99d88f4978d260f154501b79a7a156130aaa1b198db67a11259d9d7a8498db100d02fe48faa4fb97f8fbff00504b070825edb99d7d060000c91f0000504b0304140000000000176a873a97e75e245204000052040000080000006d6574612e786d6c3c3f786d6c2076657273696f6e3d22312e302220656e636f64696e673d225554462d38223f3e0a3c6f66666963653a646f63756d656e742d6d65746120786d6c6e733a6f66666963653d2275726e3a6f617369733a6e616d65733a74633a6f70656e646f63756d656e743a786d6c6e733a6f66666963653a312e302220786d6c6e733a786c696e6b3d22687474703a2f2f7777772e77332e6f72672f313939392f786c696e6b2220786d6c6e733a64633d22687474703a2f2f7075726c2e6f72672f64632f656c656d656e74732f312e312f2220786d6c6e733a6d6574613d2275726e3a6f617369733a6e616d65733a74633a6f70656e646f63756d656e743a786d6c6e733a6d6574613a312e302220786d6c6e733a6f6f6f3d22687474703a2f2f6f70656e6f66666963652e6f72672f323030342f6f666669636522206f66666963653a76657273696f6e3d22312e31223e3c6f66666963653a6d6574613e3c6d6574613a67656e657261746f723e4f70656e4f66666963652e6f72672f322e34244c696e7578204f70656e4f66666963652e6f72675f70726f6a6563742f3638306d3137244275696c642d393331303c2f6d6574613a67656e657261746f723e3c6d6574613a696e697469616c2d63726561746f723e6672616e636f69733c2f6d6574613a696e697469616c2d63726561746f723e3c6d6574613a6372656174696f6e2d646174653e323030392d30342d30375431343a31353a34303c2f6d6574613a6372656174696f6e2d646174653e3c64633a63726561746f723e6672616e636f69733c2f64633a63726561746f723e3c64633a646174653e323030392d30342d30375431353a31363a34363c2f64633a646174653e3c6d6574613a65646974696e672d6379636c65733e343c2f6d6574613a65646974696e672d6379636c65733e3c6d6574613a65646974696e672d6475726174696f6e3e505435354d3439533c2f6d6574613a65646974696e672d6475726174696f6e3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2031222f3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2032222f3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2033222f3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2034222f3e3c6d6574613a646f63756d656e742d737461746973746963206d6574613a7461626c652d636f756e743d223022206d6574613a696d6167652d636f756e743d223022206d6574613a6f626a6563742d636f756e743d223022206d6574613a706167652d636f756e743d223122206d6574613a7061726167726170682d636f756e743d223022206d6574613a776f72642d636f756e743d223022206d6574613a6368617261637465722d636f756e743d2230222f3e3c2f6f66666963653a6d6574613e3c2f6f66666963653a646f63756d656e742d6d6574613e504b0304140008000800176a873a000000000000000000000000180000005468756d626e61696c732f7468756d626e61696c2e706e67eb0cf073e7e592e2626060e0f5f4700902d25b1918181938d880acafcae6b7191898f67bba388654cc797b6923278301cf810d7c3fff3ffdd2e974d043bce2c39b778d937eca32ac9f394b2678727256918f6fdfca9386a696cbd48e5f79326354705470c005c5cfe5b2fd330aff2a0b4cd10c9eae7e2eeb9c129a00504b070884d783a37c000000f8020000504b0304140008000800176a873a0000000000000000000000000c00000073657474696e67732e786d6cbd5a5d73da3a107dbfbf22e3770a2469da30091d20a5372d0d0c90666edf84bd806e84d623c901fefd5dc986217c24d446f789d696ce6a57bb7b8ee4dc7c59ccc4d90b28cd51de06d50f95e00c6488119793dbe071d82e7d0ebed4ffbac1f19887508b304c66204d49833134449fd174a96be9ebdb2051b2864c735d936c06ba66c21ac62057d36a9ba36bce58fa6421b87cbe0da6c6c4b572793e9f7f985f7c40352957afafafcbeeed6a688872cc27c79a4a476f9a42c4b5213b215d8c33765ea95c96d3ff0767d9223742530deaab38acdcafdf6406d29f123730b3b139cb1edba5dd0664b2f6c261be8e5ab06fdeeb39bf687c43011b621cacde98654c6fb83441bd7afea97253de05391eb80363b30fb974757d715e0cfa894766ba0ffbe2eaf3e78fc5b0ff063e99ee5d78f5aa7a59cd073e98e2bc0f11a519b4a64c4e406f1918210a6032a81b95403e1bf7b2a970aee1274670087dcc843e1abe34637189cb081610edc66a7f8eb939541d6a795cc4efa3ada56aa3288183ba4de702597228f92e2a9502a8076aa508a8e623015e4ac5217ba86e87db3f5424e7d7d71f73164906dd446370b677d59f2eab3997fd1b713624a4ed6c9ba222e002a06d161a54fb61ab959cc0f77a00024203515bd1831cd5bce7e166691e7a9d55fbfe01442ec7d351fa2051cc10b9fd092f35a2a8c7141b324a8541cc42db0d4ede2b7bd4644c1f2cf7c27607cad72c5fe37713637b7d872c887f3888483f24b311a8379c2960ad43eae5318e98d9d7fb57b958bcebb7518d781481240a5394f614bcc21cd021324cd86467d1190b8c55be88b430b1f60fa0b6fb391b14539cc9edb697a196df9fdf840997b45907bc3d02e1ab8cde9c5fb4ec8faa9bafb3d82c7bcc8f88994854d0e64a1bebe83d2d4c9a7be9b37ad6d9dcc259ac405b357e72aa70811b900302bee3e860e00ab8913680b6229e83592ce8df9eda668fc5a0ac9d0198645b5d9cc2136200477bddf19878c347ac9c1f567479cae24ceaf7141a6271caa71fb0dd8b464cc3d565934b466dea88ca774b76a4e861bdd942a9c16febae531598692406d314f594962da41e87c24b74081ed4de74ffc35d4cdb1b6da36ec8a829987cd61474dbe95a4c8489706ac957c237a444e32c1c960939db5b0759d40716a1143b897e9a8e603561260787e8eaa005c2c76e93adaf0bda6ec944873cf2223f1b712c968f1ad41d33ecf4f06dab387d16dc80bdc0aff4deaa2b5b02f5692e1c768d7c133862e22ebb72b33ad7071ddceb1fb4df0d4de2ae97c8d024becab021f84452f20e0cc63dd4fc2d33f977a72578dcd06b59d39021d536444f8a86aab6583a72f511c756a2146d934d6adb18edef001315e6d7bb77f880a6c562932818103ac9fa6838a59fc9b43bfad7870f9b16ef149b5b335dd9db3da29cc298eb637d9cff00f021a432e27ad8bd42387e071c469b2d0a8110376b1e811ac2c23c2916772585950ac0870cd6d01551e6f94fa02586de14a4bb04f37510d21d368275ebf313292bf8c0c98f776e5a8a73f7e67d4ec30c0c49c2d3076dedd22ad1623f3eb926d1073a9d58d71ac6282be3e890da462f5b959ebb14ea9854ba2ffc6f14afe91bd55240ee0881736783cebe2d2643101e58ef35f9d0617ec6882c760ff2ee2a39a7ce751bff3dd1868f97b66cf41337d39f4c264c3415b0673f1ca1fb604f062f30c4f442c463e55063a61c5b89133fe5937567eb8bfda06a951d5b6272e062ad3ea5365b52a05124764d4578c1e73d5276393281260b9f274eaafc5f595e8cddadcc7197f37e4ece74cc0d9f6d63f442f77241791bc26f5048c7b6774e6c7ff8c1c47df028ef7c912f1ffa5b85fa7f504b070826bb79da36050000ed200000504b0304140008000800176a873a000000000000000000000000150000004d4554412d494e462f6d616e69666573742e786d6cb5954b6ac3301040f73d85d1de56db553171022df404e90126f2d811e887661492db570ee4d33694a6583b09a4f74623cd68b1da5b53ed3092f6ae134fcda3a8d029df6b3776e263fd5ebf88d5f26161c1e90189dbd3a0cafb1c9da79d48d1b51e4853ebc022b5ac5a1fd0f55e258b8edbafebdbc9b47ca82ee0411bacf3c278a82e32ec35d47c08d80908c168059ce3943bd737475773ad6818f72c2ebb87644c1d80b79d9042de25bb4d79f36ed0638ac720e859120327da402c8307a5d0609efa28558a713a62ce62715711c1603c301682071f52c84f2015c2473f46a472373d855e0ccede9b62706d614492af9a2d042aeab893fdbd5f507253f5344937ea5af0b718ee944f9d484eb57a139efdfcbf62fe9d4b7c3048b3632d32ccd678d6db64370eb421c9a76113dc38377cdec42273fe10cfa95dc81fffe1f213504b07083562d7393e0100004a070000504b01021400140000000000176a873a5ec6320c27000000270000000800000000000000000000000000000000006d696d6574797065504b01021400140000000000176a873a0000000000000000000000001a000000000000000000000000004d000000436f6e66696775726174696f6e73322f7374617475736261722f504b01021400140008000800176a873a000000000200000000000000270000000000000000000000000085000000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c504b01021400140000000000176a873a0000000000000000000000001800000000000000000000000000dc000000436f6e66696775726174696f6e73322f666c6f617465722f504b01021400140000000000176a873a0000000000000000000000001a0000000000000000000000000012010000436f6e66696775726174696f6e73322f706f7075706d656e752f504b01021400140000000000176a873a0000000000000000000000001c000000000000000000000000004a010000436f6e66696775726174696f6e73322f70726f67726573736261722f504b01021400140000000000176a873a000000000000000000000000180000000000000000000000000084010000436f6e66696775726174696f6e73322f6d656e756261722f504b01021400140000000000176a873a0000000000000000000000001800000000000000000000000000ba010000436f6e66696775726174696f6e73322f746f6f6c6261722f504b01021400140000000000176a873a0000000000000000000000001f00000000000000000000000000f0010000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b01021400140008000800176a873a05e3fa559a050000d23f00000b000000000000000000000000002d020000636f6e74656e742e786d6c504b01021400140008000800176a873a25edb99d7d060000c91f00000a00000000000000000000000000000800007374796c65732e786d6c504b01021400140000000000176a873a97e75e2452040000520400000800000000000000000000000000b50e00006d6574612e786d6c504b01021400140008000800176a873a84d783a37c000000f802000018000000000000000000000000002d1300005468756d626e61696c732f7468756d626e61696c2e706e67504b01021400140008000800176a873a26bb79da36050000ed2000000c00000000000000000000000000ef13000073657474696e67732e786d6c504b01021400140008000800176a873a3562d7393e0100004a07000015000000000000000000000000005f1900004d4554412d494e462f6d616e69666573742e786d6c504b0506000000000f000f00ee030000e01a00000000, 'projet.odt', 7908, 'application/vnd.oasis.opendocument.text');


-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `libelle` varchar(100) NOT NULL default '',
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `profils`
--

INSERT INTO `profils` (`id`, `parent_id`, `libelle`, `actif`, `created`, `modified`) VALUES
(1, 0, 'Défaut', 1, '2009-04-06 11:06:17', '2009-04-06 11:06:17'),
(2, 0, 'Administrateur', 1, '2009-04-06 11:06:39', '2009-04-06 11:06:39'),
(3, 0, 'Rédacteur', 1, '2009-04-06 11:06:48', '2009-04-06 11:06:48'),
(4, 0, 'Valideur', 1, '2009-04-06 11:06:54', '2009-04-06 11:06:54'),
(5, 0, 'Service assemblée', 1, '2009-04-06 11:07:03', '2009-04-06 11:07:03');

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
  `traitee` int(1) NOT NULL default '0',
  `secretaire_id` int(11) default NULL,
  `debat_global` longblob NOT NULL,
  `debat_global_name` varchar(75) NOT NULL,
  `debat_global_size` int(11) NOT NULL,
  `debat_global_type` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `seances`
--


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
) ;

--
-- Contenu de la table `sequences`
--


-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `order` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `circuit_defaut_id` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `services`
--

INSERT INTO `services` (`id`, `parent_id`, `order`, `libelle`, `circuit_defaut_id`, `actif`, `created`, `modified`) VALUES
(1, 0, '', 'Défaut', 0, 1, '2009-04-06 08:35:48', '2009-04-06 08:35:48');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `order` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

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
) ;

--
-- Contenu de la table `traitements`
--


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
) ;

--
-- Contenu de la table `typeacteurs`
--


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
  `modelprojet_id` int(11) NOT NULL,
  `modeldeliberation_id` int(11) NOT NULL,
  `modelconvocation_id` int(11) NOT NULL,
  `modelordredujour_id` int(11) NOT NULL,
  `modelpvsommaire_id` int(11) NOT NULL,
  `modelpvdetaille_id` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `typeseances`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeseances_acteurs`
--

CREATE TABLE `typeseances_acteurs` (
  `typeseance_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`acteur_id`)
) ;

--
-- Contenu de la table `typeseances_acteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `typeseances_typeacteurs`
--

CREATE TABLE `typeseances_typeacteurs` (
  `typeseance_id` int(11) NOT NULL,
  `typeacteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`typeacteur_id`)
) ;

--
-- Contenu de la table `typeseances_typeacteurs`
--


-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `profil_id` int(11) NOT NULL default '0',
  `statut` int(11) NOT NULL default '0',
  `login` varchar(50) NOT NULL default '',
  `note` varchar(25) NOT NULL,
  `circuit_defaut_id` int(11) NOT NULL,
  `password` varchar(100) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `telfixe` varchar(20) default NULL,
  `telmobile` varchar(20) default NULL,
  `date_naissance` date default NULL,
  `accept_notif` tinyint(1) default NULL,
  `position` int(3) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `profil_id`, `statut`, `login`, `note`, `circuit_defaut_id`, `password`, `nom`, `prenom`, `email`, `telfixe`, `telmobile`, `date_naissance`, `accept_notif`, `position`, `created`, `modified`) VALUES
(1, 2, 0, 'admin', '', 0, '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin', 'francois.desmaretz@adullact.org', '0000000000', '0000000000', '1999-11-30', 0, 0, '0000-00-00 00:00:00', '2009-04-06 11:07:38');

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
) ;

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
) ;

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
  `acteur_id` int(11) NOT NULL default '0',
  `delib_id` int(11) NOT NULL default '0',
  `resultat` int(1) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ;

--
-- Contenu de la table `votes`
--

