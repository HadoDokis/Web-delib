RENAME TABLE agents TO users;
ALTER TABLE `users` ADD `email` VARCHAR( 255 ) NOT NULL AFTER `prenom` ;
ALTER TABLE `users` ADD `statut` INT( 11) NOT NULL AFTER `profil_id`; 
ALTER TABLE `users` CHANGE `teldom` `teldom` INT( 10 ) UNSIGNED ZEROFILL NULL DEFAULT NULL; 
ALTER TABLE `users` CHANGE `telmobile` `telmobile` INT( 10 ) UNSIGNED ZEROFILL NULL DEFAULT NULL; 