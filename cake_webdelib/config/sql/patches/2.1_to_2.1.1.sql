ALTER TABLE `deliberations` ADD `etat_asalae` TINYINT( 1 ) NULL AFTER `etat_parapheur` ;

CREATE TABLE `historiques` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`delib_id` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`circuit_id` INT( 11 ) NOT NULL ,
`commentaire` VARCHAR( 1000 ) NOT NULL ,
`modified` DATETIME NOT NULL ,
`created` DATETIME NOT NULL
) ENGINE = MYISAM ;
