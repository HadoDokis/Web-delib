

-- Création de la table deliberations_seances
CREATE TABLE `deliberations_seances` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`deliberation_id` INT( 11 ) NOT NULL ,
`seance_id` INT( 11 ) NOT NULL ,
`position` INT( 11 ) NULL ,
INDEX ( `deliberation_id` , `seance_id` )
) ENGINE = MYISAM ;

-- Alimentation de la table deliberations_seances
INSERT INTO `deliberations_seances` (deliberation_id, seance_id, position) 
SELECT id, seance_id, position from `deliberations` where seance_id IS NOT NULL AND seance_id <> 0;

-- Suppression du champs seance_id 
ALTER TABLE `deliberations` DROP `seance_id` ;
ALTER TABLE `deliberations` DROP `position`;
