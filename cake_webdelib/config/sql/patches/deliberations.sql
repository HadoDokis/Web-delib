-- 14/08/2007 : Changement du format des champs de la BDD dans la table délibérations texte_projet et texte_synthèse afin de pouvoir accueillir de grands textes
ALTER TABLE `deliberations` CHANGE `texte_projet` `texte_projet` LONGBLOB;
ALTER TABLE `deliberations` CHANGE `texte_synthese` `texte_projet` LONGBLOB;
