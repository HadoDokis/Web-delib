ALTER TABLE `deliberations` ADD INDEX `circuit_id`    ( `circuit_id` ) ;
ALTER TABLE `deliberations` ADD INDEX `nature_id`     ( `nature_id` ) ;
ALTER TABLE `deliberations` ADD INDEX `theme_id`      ( `theme_id` ) ;
ALTER TABLE `deliberations` ADD INDEX `service_id`    ( `service_id` ) ;
ALTER TABLE `deliberations` ADD INDEX `redacteur_id`  ( `redacteur_id` ) ;
ALTER TABLE `deliberations` ADD INDEX `rapporteur_id` ( `rapporteur_id` ) ;
ALTER TABLE `deliberations` ADD INDEX `seance_id`     ( `seance_id` ) ;

ALTER TABLE `deliberations` DROP `vote_id`;

ALTER TABLE `infosups` ADD INDEX `text` (`text` );



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


OPTIMIZE table acteurs               ;
OPTIMIZE table acos                  ;
OPTIMIZE table acteurs_services      ;
OPTIMIZE table ados                  ;
OPTIMIZE table annexes               ;
OPTIMIZE table aros                  ;
OPTIMIZE table aros_acos             ;
OPTIMIZE table aros_ados             ;
OPTIMIZE table collectivites         ;
OPTIMIZE table commentaires          ;
OPTIMIZE table compteurs             ;
OPTIMIZE table deliberations         ;
OPTIMIZE table historiques           ;
OPTIMIZE table infosupdefs           ;
OPTIMIZE table infosuplistedefs      ;
OPTIMIZE table infosups              ;
OPTIMIZE table listepresences        ;
OPTIMIZE table models                ;
OPTIMIZE table natures               ;
OPTIMIZE table profils               ;
OPTIMIZE table seances               ;
OPTIMIZE table sequences             ;
OPTIMIZE table services              ;
OPTIMIZE table themes                ;
OPTIMIZE table typeacteurs           ;
OPTIMIZE table typeseances           ;
OPTIMIZE table typeseances_acteurs   ;
OPTIMIZE table typeseances_natures   ;
OPTIMIZE table typeseances_typeacteurs ;
OPTIMIZE table users                 ;
OPTIMIZE table users_services        ;
OPTIMIZE table votes                 ;
OPTIMIZE table  wkf_circuits         ;
OPTIMIZE table  wkf_compositions     ;
OPTIMIZE table  wkf_etapes           ;
OPTIMIZE table  wkf_signatures       ;
OPTIMIZE table  wkf_traitements      ;
OPTIMIZE table  wkf_visas            ;

--
--Ajout après la 3.5.1-beta
--
ALTER TABLE `infosups` ADD `model` VARCHAR( 25 ) NOT NULL DEFAULT 'Deliberation' AFTER `id` ;
ALTER TABLE `infosups` ADD `foreign_key` INT( 11 ) NOT NULL AFTER `model` ;
ALTER TABLE `infosups` ADD INDEX ( `foreign_key` ) ;
UPDATE infosups set foreign_key = deliberation_id;

ALTER TABLE `infosups` DROP `deliberation_id`;
ALTER TABLE `infosupdefs` ADD `model` VARCHAR( 25 ) NOT NULL  DEFAULT 'Deliberation'  AFTER `id` ;

ALTER TABLE `annexes` ADD `joindre_fusion` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `joindre_ctrl_legalite` ;

ALTER TABLE `collectivites` ADD `id_entity` INT( 11 ) NULL AFTER `id` ;
