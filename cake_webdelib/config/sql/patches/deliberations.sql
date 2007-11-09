-- 17/10/2007
ALTER TABLE `deliberations` ADD `reporte` tinyint(1) NOT NULL default '0' AFTER `etat`,

-- 22/10/2007
CHANGE `objet` `objet` VARCHAR( 1000 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `titre` `titre` VARCHAR( 1000 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL

-- 09/11/2007
ALTER TABLE `deliberations` ADD `debat` LONGBLOB NULL AFTER `reporte` ;