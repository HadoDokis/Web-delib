ALTER TABLE `deliberations` ADD `commission` LONGBLOB NOT NULL ,
ADD `commission_size` INT( 11 ) NOT NULL ,
ADD `commission_type` VARCHAR( 255 ) NOT NULL ,
ADD `commission_name` VARCHAR( 255 ) NOT NULL ;
