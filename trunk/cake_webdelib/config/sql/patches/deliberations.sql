-- 17/10/2007
ALTER TABLE `deliberations` ADD `reporte` TINYINT( 1 ) NOT NULL AFTER `etat`,

-- 22/10/2007
CHANGE `objet` `objet` VARCHAR( 1000 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `titre` `titre` VARCHAR( 1000 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL 