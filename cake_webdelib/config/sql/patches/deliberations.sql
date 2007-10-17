ALTER TABLE `deliberations` ADD `localisation1_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `etat` ,
ADD `localisation2_id` INT NOT NULL DEFAULT '0' AFTER `localisation1_id` ,
ADD `localisation3_id` INT NOT NULL DEFAULT '0' AFTER `localisation2_id`,

-- 17/10/2007
ADD `reporte` TINYINT( 1 ) NOT NULL AFTER `etat` ;