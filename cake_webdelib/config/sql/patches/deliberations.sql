-- 05/09/2007 : Ajout du champ anterieure_id correspondant à l'id de la version anterieure à la delib
ALTER TABLE `deliberations` ADD `anterieure_id` INT NOT NULL AFTER `seance_id` ;
