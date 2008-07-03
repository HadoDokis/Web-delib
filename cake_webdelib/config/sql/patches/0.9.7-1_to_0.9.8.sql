ALTER TABLE `models` ADD `name` VARCHAR( 255 ) NULL ;
ALTER TABLE `models` ADD `size` INT( 11 ) NOT NULL ;
ALTER TABLE `models` ADD `extension`  VARCHAR( 255 ) NULL ;
ALTER TABLE `models` CHANGE `texte` `content` LONGBLOB NOT NULL;

ALTER TABLE `models` ADD `modele` VARCHAR( 50 ) NOT NULL AFTER `id` ;

ALTER TABLE `deliberations` ADD `texte_projet_size` INT( 11 ) NOT NULL ;
ALTER TABLE `deliberations` ADD `texte_projet_type`  VARCHAR( 255 ) NULL ;
ALTER TABLE `deliberations` ADD `texte_projet_name`  VARCHAR( 255 ) NULL ;
ALTER TABLE `deliberations` ADD `texte_synthese_name`  VARCHAR( 255 ) NULL ;
ALTER TABLE `deliberations` ADD `texte_synthese_size` INT( 11 ) NOT NULL ;
ALTER TABLE `deliberations` ADD `texte_synthese_type`  VARCHAR( 255 ) NULL ;
ALTER TABLE `deliberations` ADD `deliberation_size` INT( 11 ) NOT NULL ;
ALTER TABLE `deliberations` ADD `deliberation_type`  VARCHAR( 255 ) NULL ;
ALTER TABLE `deliberations` ADD `deliberation_name`  VARCHAR( 255 ) NULL ;
ALTER TABLE `deliberations` ADD `avis` INT( 1 ) NOT NULL DEFAULT '0';

ALTER TABLE `users` ADD `note` VARCHAR( 25 ) NOT NULL AFTER `login` ;
ALTER TABLE `users` DROP `titre`,
                    DROP `adresse`,
                    DROP `CP`,
                    DROP `ville`,
                    DROP `service_id`;

ALTER TABLE `users` CHANGE `teldom` `telfixe` VARCHAR( 20 ) NULL DEFAULT NULL ;

ALTER TABLE `compteurs` CHANGE `num_sequence` `sequence_id` INT( 11 ) NOT NULL;
ALTER TABLE `typeseances` ADD `action` TINYINT( 1 ) NOT NULL AFTER `retard` ,
                          ADD `compteur_id` INT( 11 ) NOT NULL AFTER `action` ,
                          ADD `modelconvocation_id` INT( 11 ) NOT NULL AFTER `compteur_id` ,
                          ADD `modelordredujour_id` INT( 11 ) NOT NULL AFTER `modelconvocation_id` ,
                          ADD `modelpvsommaire_id` INT( 11 ) NOT NULL AFTER `modelordredujour_id` ,
                          ADD `modelpvdetaille_id` INT( 11 ) NOT NULL AFTER `modelpvsommaire_id` ;


ALTER TABLE `listepresences` CHANGE `user_id` `acteur_id` INT( 11 ) NOT NULL ;
ALTER TABLE `votes` CHANGE `user_id` `acteur_id` INT( 11 ) NOT NULL DEFAULT '0' ;

CREATE TABLE `typeacteurs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `elus` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
);

CREATE TABLE `acteurs_services` (
  `acteur_id` int(11) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acteur_id`,`service_id`)
);

CREATE TABLE `acteurs` (
  `id` int(11) NOT NULL auto_increment,
  `typeacteur_id` int(11) NOT NULL default '0',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `salutation` varchar(50) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `position` int(11) NOT NULL,
  `date_naissance` date NOT NULL,
  `adresse1` varchar(100) NOT NULL,
  `adresse2` varchar(100) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telfixe` varchar(20) default NULL,
  `telmobile` varchar(20) default NULL,
  `note` varchar(255) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `num_sequence` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
);

CREATE TABLE `typeseances_acteurs` (
  `typeseance_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`acteur_id`)
);

CREATE TABLE `typeseances_typeacteurs` (
  `typeseance_id` int(11) NOT NULL,
  `typeacteur_id` int(11) NOT NULL,
  PRIMARY KEY  (`typeseance_id`,`typeacteur_id`)
);

ALTER TABLE `typeseances` ADD `modelprojet_id` INT( 11 ) NOT NULL AFTER `compteur_id` ,
ADD `modeldeliberation_id` INT( 11 ) NOT NULL AFTER `modelprojet_id` ;


ALTER TABLE `seances` ADD `secretaire_id` INT( 11 ) NULL AFTER `traitee`;