RENAME TABLE agents TO users;
ALTER TABLE `users` ADD `elu` BOOL NOT NULL AFTER `profil_id` ;