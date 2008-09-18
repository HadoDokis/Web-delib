ALTER TABLE `commentaires`
ADD `pris_en_compte` TINYINT NOT NULL DEFAULT '0' AFTER `texte` ;