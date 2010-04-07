ALTER TABLE `deliberations` ADD `etat_asalae` TINYINT( 1 ) NULL AFTER `etat_parapheur` ;
ALTER TABLE `deliberations` ADD `signature` BLOB NULL AFTER `delib_pdf` ;

ALTER TABLE `deliberations` ADD `commission` LONGBLOB NULL AFTER `debat_type` ,
ADD `commission_size` INT( 11 ) NULL AFTER `commission` ,
ADD `commission_type` VARCHAR( 255 ) NULL AFTER `commission_size` ,
ADD `commission_name` VARCHAR( 255 ) NULL AFTER `commission_type` ;

CREATE TABLE `historiques` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`delib_id` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`circuit_id` INT( 11 ) NOT NULL ,
`commentaire` VARCHAR( 1000 ) NOT NULL ,
`modified` DATETIME NOT NULL ,
`created` DATETIME NOT NULL
) ENGINE = MYISAM ;

