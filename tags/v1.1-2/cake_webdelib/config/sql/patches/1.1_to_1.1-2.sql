ALTER TABLE `seances` ADD `debat_global_name` VARCHAR( 75 ) NOT NULL ,
ADD `debat_global_size` INT( 11 ) NOT NULL ,
ADD `debat_global_type` VARCHAR( 255 ) NOT NULL ;

INSERT INTO `models` (`id`, `modele`, `type`, `name`, `size`, `extension`, `content`) VALUES
(15, 'Liste abstenants', 'Composant documentaire', NULL, 0, NULL, 0x235052454e4f4d5f41425354454e414e542320234e4f4d5f41425354454e414e54232d2d);

INSERT INTO `models` (`id` ,`modele` ,`type` ,`name` ,`size` ,`extension`) VALUES
('9999', 'Liste Projets Ordre du Jour', 'Composant documentaire', '', '', '');
