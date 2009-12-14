-- --------------------------------------------------------
--
-- Modification de la table `infosupdefs`
--
ALTER TABLE `infosupdefs` ADD `val_initiale` VARCHAR( 255 ) NULL AFTER `type` ;

-- --------------------------------------------------------
--
-- Modification de la table `deliberations`
--
ALTER TABLE `deliberations` ADD `tdt_id` INT( 11 ) NULL AFTER `num_pref` ,
ALTER TABLE `deliberations` ADD `dateAR` VARCHAR( 100 ) NULL AFTER `tdt_id` ;

ALTER TABLE `deliberations` ADD `etat_parapheur` TINYINT NULL AFTER `etat` 

ALTER TABLE `deliberations` ADD `delib_pdf` LONGBLOB NULL AFTER `vote_commentaire` ;
ALTER TABLE `seances` ADD `commentaire` VARCHAR( 500 ) NULL AFTER `traitee`;
ALTER TABLE `commentaires` ADD `commentaire_auto` TINYINT( 1 ) NOT NULL AFTER `pris_en_compte` 


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
