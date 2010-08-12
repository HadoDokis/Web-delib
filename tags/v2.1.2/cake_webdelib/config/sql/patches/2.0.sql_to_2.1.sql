-- --------------------------------------------------------
--
-- Modification de la table `infosupdefs`
--
ALTER TABLE `infosupdefs` ADD `val_initiale` VARCHAR( 255 ) NULL AFTER `type` ;

-- --------------------------------------------------------
--
-- Modification de la table `deliberations`
--
ALTER TABLE `deliberations` ADD `tdt_id` INT( 11 ) NULL AFTER `num_pref` ;
ALTER TABLE `deliberations` ADD `dateAR` VARCHAR( 100 ) NULL AFTER `tdt_id` ;

ALTER TABLE `deliberations` ADD `etat_parapheur` TINYINT NULL AFTER `etat` ;

ALTER TABLE `deliberations` ADD `delib_pdf` LONGBLOB NULL AFTER `vote_commentaire` ;
ALTER TABLE `seances` ADD `commentaire` VARCHAR( 500 ) NULL AFTER `traitee`;
ALTER TABLE `commentaires` ADD `commentaire_auto` TINYINT( 1 ) NOT NULL AFTER `pris_en_compte` ;

ALTER TABLE `acteurs` CHANGE `date_naissance` `date_naissance` DATE NULL ;

UPDATE `aros_acos` SET `_create` = '1',
`_read` = '1',
`_update` = '1',
`_delete` = '1' WHERE `aros_acos`.`id` =31 LIMIT 1 ;

UPDATE `aros_acos` SET `_create` = '1',
`_read` = '1',
`_update` = '1',
`_delete` = '1' WHERE `aros_acos`.`id` =32 LIMIT 1 ;

UPDATE `aros_acos` SET `_create` = '1',
`_read` = '1',
`_update` = '1',
`_delete` = '1' WHERE `aros_acos`.`id` = 29 LIMIT 1 ;

UPDATE `aros_acos` SET `_create` = '1',
`_read` = '1',
`_update` = '1',
`_delete` = '1' WHERE `aros_acos`.`id` =7 LIMIT 1 ;

--
-- Structure de la table `infosuplistedefs`
--
CREATE TABLE `infosuplistedefs` (
  `id` int(11) NOT NULL auto_increment,
  `infosupdef_id` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `actif` tinyint(1) NOT NULL default '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `INFOSUPDEF_ID_ORDRE` (`infosupdef_id`,`ordre`)
);

ALTER TABLE `deliberations` ADD `debat_name` VARCHAR( 2555 ) NOT NULL AFTER `debat` ,
ADD `debat_size` INT( 11 ) NOT NULL AFTER `debat_name` ,
ADD `debat_type` VARCHAR( 255 ) NOT NULL AFTER `debat_size` ;
