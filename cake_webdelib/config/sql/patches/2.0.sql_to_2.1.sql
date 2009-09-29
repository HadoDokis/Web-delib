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
ADD `dateAR` VARCHAR( 100 ) NULL AFTER `tdt_id` 
