ALTER TABLE `acos`
	DROP COLUMN `object_id`;
	
ALTER TABLE `acos`
	ADD `parent_id` int(10) NOT NULL DEFAULT '0';

ALTER TABLE `acos`
	ADD `model` varchar(255);

ALTER TABLE `acos`
	ADD `foreign_key` int(10) NOT NULL DEFAULT '0';

ALTER TABLE `aros`
	ADD `parent_id` int(10) NOT NULL DEFAULT '0';

ALTER TABLE `aros`
	ADD `model` varchar(255);
	
UPDATE `acos`
	SET `model` = NULL;
	
UPDATE `acos`
	SET `foreign_key` = 0;
	
UPDATE `acos`
	SET `parent_id` = 0;
	
UPDATE `aros`
	SET `parent_id` = 0;

UPDATE `acos`
    SET `alias` = CONCAT( 'Module:', `alias` )
    WHERE LOCATE(':', `alias`) = '0';

UPDATE `aros`
    SET `model` = SUBSTRING_INDEX(`alias`, ':', '1'),
        `alias` = SUBSTRING_INDEX(`alias`, ':', '-1');
            
UPDATE `acos`
	SET `alias` = 'Pages:postseances'
	WHERE `alias` = 'Pages:postseance';
	
UPDATE `aros`, `profils`
	SET `aros`.`foreign_key` = `profils`.`id`
	WHERE `aros`.`alias` = `profils`.`libelle`
	AND `aros`.`model` LIKE "Profil";

ALTER TABLE `infosupdefs` CHANGE `taille` `taille` INT( 11 ) NULL;

ALTER TABLE `deliberations` CHANGE `rapporteur_id` `rapporteur_id` INT( 11 ) NULL DEFAULT '0';

ALTER TABLE `users` CHANGE `circuit_defaut_id` `circuit_defaut_id` INT( 11 ) NULL;

ALTER TABLE `commentaires` CHANGE `texte` `texte` VARCHAR( 1000 );

ALTER TABLE `themes` ADD `lft` INT NULL DEFAULT '0',
ADD `rght` INT NULL DEFAULT '0';

ALTER TABLE `services` ADD `lft` INT NULL DEFAULT '0',
ADD `rght` INT NULL DEFAULT '0';

ALTER TABLE `deliberations` CHANGE `num_pref` `num_pref` VARCHAR( 100 ) NOT NULL;

ALTER TABLE `votes` CHANGE `resultat` `resultat` INT( 1 ) NULL;

ALTER TABLE `typeseances` CHANGE `retard` `retard` INT( 11 ) NULL DEFAULT '0';

ALTER TABLE `services` CHANGE `parent_id` `parent_id` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `acteurs_services` DROP PRIMARY KEY;
  
ALTER TABLE `acteurs_services` ADD `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

ALTER TABLE `typeseances_acteurs` DROP PRIMARY KEY;

ALTER TABLE `typeseances_acteurs` ADD `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

ALTER TABLE `typeseances_typeacteurs` DROP PRIMARY KEY;

ALTER TABLE `typeseances_typeacteurs` ADD `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

ALTER TABLE `users_services` DROP PRIMARY KEY;

ALTER TABLE `users_services` ADD `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

ALTER TABLE `themes` CHANGE `parent_id` `parent_id` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `typeseances` CHANGE `action` `action` TINYINT(2) NOT NULL;

CREATE TABLE IF NOT EXISTS `ados` (
`id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT ,
`alias` varchar( 255 ) NOT NULL default '',
`lft` int( 10 ) default NULL ,
`rght` int( 10 ) default NULL ,
`parent_id` int( 10 ) NOT NULL default '0',
`model` varchar( 255 ) default NULL ,
`foreign_key` int( 10 ) NOT NULL default '0',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

--

INSERT INTO `ados` (`id`, `alias`, `lft`, `rght`, `parent_id`, `model`, `foreign_key`) VALUES
(1, 'Nature:Délibérations', 1, 2, 0, 'Nature', 1),
(2, 'Nature:Arrêtés Réglementaires', 3, 4, 0, 'Nature', 2),
(3, 'Nature:Arrêtés Individuels', 5, 6, 0, 'Nature', 3),
(4, 'Nature:Contrats et conventions', 7, 8, 0, 'Nature', 4);

CREATE TABLE IF NOT EXISTS `aros_ados` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `aro_id` int(10) unsigned NOT NULL,
  `ado_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL default '0',
  `_read` char(2) NOT NULL default '0',
  `_update` char(2) NOT NULL default '0',
  `_delete` char(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `natures` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(100) NOT NULL,
  `code` varchar(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `natures`
--

INSERT INTO `natures` (`id`, `libelle`, `code`) VALUES
(1, 'Délibérations', 'DE'),
(2, 'Arrêtés Réglementaires', 'AR'),
(3, 'Arrêtés Individuels', 'AI'),
(4, 'Contrats et conventions', 'CC');

ALTER TABLE `deliberations` ADD `nature_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `id` ;

CREATE TABLE `typeseances_natures` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`typeseance_id` INT( 11 ) NOT NULL ,
`nature_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM 
