-- 05/09/2007 : Ajout du champ anterieure_id correspondant à l'id de la version anterieure à la delib
ALTER TABLE `deliberations` ADD `anterieure_id` INT NOT NULL AFTER `seance_id` ;
ALTER TABLE `deliberations` ADD `position` INT( 4 ) NOT NULL AFTER `seance_id` ;

-- 27/09/2007 : Ajout du champ date_limite
ALTER TABLE `deliberations` ADD `date_limite` DATE NULL AFTER `texte_synthese` ;
