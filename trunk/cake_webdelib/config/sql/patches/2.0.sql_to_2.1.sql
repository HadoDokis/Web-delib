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
ADD `dateAR` VARCHAR( 100 ) NULL AFTER `tdt_id` ;

ALTER TABLE `deliberations` ADD `delib_pdf` LONGBLOB NULL AFTER `vote_commentaire` 

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
