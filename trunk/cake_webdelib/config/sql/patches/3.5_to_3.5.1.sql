ALTER TABLE `deliberations` ADD `objet_delib` VARCHAR( 1000 ) NOT NULL AFTER `objet` ;
ALTER TABLE `deliberations` ADD `is_multidelib` TINYINT( 1 )  NOT NULL AFTER `anterieure_id` ;
UPDATE `deliberations` SET `objet_delib` = `objet`;

ALTER TABLE `acos` ADD INDEX ( `model` , `foreign_key` ); 
ALTER TABLE `acos` ADD INDEX ( `parent_id` ) ;
ALTER TABLE `acos` ADD INDEX ( `lft` );
ALTER TABLE `acos` ADD INDEX ( `rght` );
ALTER TABLE `acos` ADD INDEX ( `alias` ); 

ALTER TABLE `ados` ADD INDEX ( `model` , `foreign_key` ) ;
ALTER TABLE `ados` ADD INDEX ( `parent_id` ) ;
ALTER TABLE `ados` ADD INDEX ( `alias` ) ;
ALTER TABLE `ados` ADD INDEX ( `rght` );
ALTER TABLE `ados` ADD INDEX ( `alias` );

ALTER TABLE `aros` ADD INDEX ( `model` , `foreign_key` ) ;
ALTER TABLE `aros` ADD INDEX ( `parent_id` ) ;
ALTER TABLE `aros` ADD INDEX ( `alias` ) ;
ALTER TABLE `aros` ADD INDEX ( `rght` );
ALTER TABLE `aros` ADD INDEX ( `alias` );

ALTER TABLE `aros_acos` ADD INDEX ( `aro_id` ) ;
ALTER TABLE `aros_acos` ADD INDEX ( `aco_id` ) ;

ALTER TABLE `aros_ados` ADD INDEX ( `aro_id` );
ALTER TABLE `aros_ados` ADD INDEX ( `ado_id` ) ;

ALTER TABLE `seances` ADD INDEX ( `type_id` ) ;

ALTER TABLE `typeseances_natures` ADD INDEX ( `typeseance_id` , `nature_id` );
ALTER TABLE `typeseances_acteurs` ADD INDEX ( `typeseance_id` , `acteur_id` );
ALTER TABLE `typeseances_typeacteurs` ADD INDEX ( `typeseance_id` , `typeacteur_id` ) ;
