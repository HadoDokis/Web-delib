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
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);


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
);


-- --------------------------------------------------------
--
-- Modification de la table `localisations`
--

ALTER TABLE `localisations` ADD `order` VARCHAR( 50 ) NOT NULL AFTER `parent_id` ;
ALTER TABLE `localisations` ADD `actif` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `libelle` ;

-- --------------------------------------------------------
--
-- Modification de la table `services`
--

ALTER TABLE `services` ADD `order` VARCHAR( 50 ) NOT NULL AFTER `parent_id` ;
ALTER TABLE `services` ADD `circuit_defaut_id` INT( 11 ) NOT NULL AFTER `libelle` ;
ALTER TABLE `services` ADD `actif` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `circuit_defaut_id` ;

-- --------------------------------------------------------
--
-- Modification de la table `themes`
--

ALTER TABLE `themes` ADD `order` VARCHAR( 50 ) NOT NULL AFTER `parent_id` ;
ALTER TABLE `themes` ADD `actif` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `libelle` ;

-- --------------------------------------------------------
--
-- Modification de la table `profils`
--
ALTER TABLE `profils` ADD `actif` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `libelle` ;

-- --------------------------------------------------------
--
-- Modification de la table `users`
--

ALTER TABLE `users` ADD `circuit_defaut_id` INT( 11 ) NOT NULL AFTER `note` ;

