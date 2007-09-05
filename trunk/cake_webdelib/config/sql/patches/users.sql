-- 05/09/2007 : ajout du service_id dans la table users pour dire à quel service un rapporteur est rattaché
ALTER TABLE `users` ADD `service_id` INT NOT NULL AFTER `profil_id` ;
