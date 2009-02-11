ALTER TABLE `deliberations` ADD `commission` LONGBLOB NOT NULL ,
ADD `commission_size` INT( 11 ) NOT NULL ,
ADD `commission_type` VARCHAR( 255 ) NOT NULL ,
ADD `commission_name` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE `deliberations` ADD `debat_name` VARCHAR( 255 ) NOT NULL AFTER `debat` ,
ADD `debat_size` INT( 11 ) NOT NULL AFTER `debat_name` ,
ADD `debat_type` VARCHAR( 255 ) NOT NULL AFTER `debat_size` ;
